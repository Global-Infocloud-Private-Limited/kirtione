<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
      This is test TDS Master
    <div class="row">
      <div class="col-md-8">
        <div class="panel_s">
          <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="searchh2" style="display:none;">Please wait fetching data...</div>
                        <div class="searchh3" style="display:none;">Please wait Create new ItemID...</div>
                    </div>
                    <br>
                    <div class="col-md-2">
                        <label class="control-label" for="tds_code">TDS Code</label>
                        <input type="text" id="tds_code" name="tds_code" class="form-control" value="">
                        <span id="lblError" style="color: red"></span>
                    </div>
                    <div class="col-md-4">
                        <label class="control-label" for="tds_name">TDS Name</label>
                        <input type="text" id="tds_name" name="tds_name" class="form-control" value="">
                        <span id="lblError" style="color: red"></span>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Is Active?</label>
                            <select class="selectpicker" name="isactive" id="isactive" data-width="100%" data-none-selected-text="-- Select --" data-live-search="true">
                                <option value="Y">Active</option> 
                                <option value="N">Deactive</option> 
                            </select>
                        </div>
                    </div>


                    <div class="col-md-12" style="margin-top:1%;">
                        <table class="table items table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="20%">TDS Sub Name</th>
                                    <th width="20%">Rate (%)</th>
                                    <th width="10%">&nbsp;</th>
                                </tr>
                            </thead>
                                <tbody id="parameter_body">
                                    <tr>
                                        <td width="20%"><input id="tds_sub_name" name="tds_sub_name" type="text" value=""></td>
                                        <td width="20%"><input id="tds_rate" name="tds_rate" type="text" value=""></td>
                                        <td width="10%"><button type="button" onclick="add_row()" class="btn btn-success" title="Add TDS Rate"><i class="fa fa-plus"></i></button></td>
                                    </tr>
                                </tbody>
                        </table>
                    </div>
                    <br><br>
                    <div class="col-md-12" style="margin-top:2%;">
                        <?php if (has_permission_new('tds', '', 'create')) {
                        ?>
                        <button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                        <?php
                        }else{
                        ?>
                        <button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
                        <?php
                        }?>
                        
                        <?php if (has_permission_new('tds', '', 'edit')) {
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
                
                <div class="clearfix"></div>
            <!-- Iteme List Model-->
            
                <div class="modal fade Item_List" id="Item_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                        <div class="modal-header" style="padding:5px 10px;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Item List</h4>
                        </div>
                        <div class="modal-body" style="padding:0px 5px !important">
                                <div class="col-md-5">
                                        <a class="btn btn-default buttons-excel buttons-html2" tabindex="0"
                                            aria-controls="table-trial_bal_report" href="#" id="caexcel"
                                            style="float: left ! important;"><span>Export to Excel</span></a>
                                        <button class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</button>
                               </div>
                            <div class="table-Item_List tableFixHead2">
                                <table class="tree table table-striped table-bordered table-Item_List tableFixHead2" id="table_Item_List" width="100%">
                                    <thead>
                                        <tr style="display:none;">
                                            <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                        </tr>
                                        <tr>
                                            <th id="sl" style="text-align:left;">TDS Code</th>
                                            <th style="text-align:left;">TDS Name</th>
                                            <th style="text-align:left;">Rate (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($table_data as $key => $value) {
                                    ?>
                                        <tr class="get_ItemID" data-id="<?php echo $value["id"]; ?>">
                                            <td><?php echo $value['TDSCode'];?></td>
                                            <td><?php echo $value['TDSName'];?></td>
                                            <td><?php echo $value["rate"];?></td>
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
<script>
    $("#parameter_body").on('click','.removebtn',function(){
        $(this).parent().parent().remove();
	});
	
	function add_row(){
		var tds_sub_name = $("#tds_sub_name").val();
		var tds_rate = $("#tds_rate").val();
		
		if(tds_sub_name != '' && tds_rate !=''){
			var lasttr = $('#parameter_body tr:last td').find("select").attr('id');
			var num= lasttr.match(/-?\d+\.?\d*/);
			var newcount = parseInt(num)+parseInt(1);
			
			markup = "<tr class='addedtr'><td><input type='text' name='tds_sub_name"+newcount+"'  required id='tds_sub_name"+newcount+"' value='"+tds_sub_name+"' class='form-control' /></td>";
			markup += "<td><input type='text' name='tds_rate"+newcount+"'  required id='tds_rate"+newcount+"' value='"+tds_rate+"' class='form-control' /></td>";
			markup += "<td><a href='#' style='float:right;padding: 2px;width: 30px; float:right;' style='float:right' id='removebtn' class='btn btn-danger removebtn'><i class='fa fa-times'></i></a></td></tr>";
			tableBody = $("#parameter_body");
			tableBody.append(markup);
			
			$("#tds_sub_name").val('');
			$("#tds_rate").val('');
		}
		else
		{
			alert('All Fields Are Required');
		}
		
	}
</script>
<script>
    $(document).ready(function(){
        $('.updateBtn').hide();
        $('.updateBtn2').hide();
        $("#tds_code").dblclick(function(){
            $('#Item_List').modal('show');
            $('#Item_List').on('shown.bs.modal', function () {
              $('#myInput1').focus();
            })
        });
    // ItemID Typing Validation
        $("#tds_code").keypress(function (e) {
            var keyCode = e.keyCode || e.which;
            if(keyCode == ""){
                $("#lblError").html("");
            }else{
                //Regex for Valid Characters i.e. Alphabets and Numbers.
                var regex = /^[A-Za-z0-9]+$/;
                //Validate TextBox value against the Regex.
                var isValid = regex.test(String.fromCharCode(keyCode));
                if (!isValid) {
                    $("#lblError").html("Only Alphabets and Numbers allowed.");
                }else{
                    $("#lblError").html("");
                }
                return isValid;
            }
        });
        
    // Empty and open create mode
        $("#tds_code").focus(function(){
            $('#tds_code').val('');
            $('#item_code').val('');
            $('#tds_name').val('');
            $('#opening_stock').val('');
            
            $('select[name=isactiveAll]').val('');
            $('.selectpicker').selectpicker('refresh');
                        
            $('select[name=tax]').val('1');
            $('.selectpicker').selectpicker('refresh');
                       
            $('select[name=unit]').val('');
            $('.selectpicker').selectpicker('refresh');
                       
            $('select[name=subgroup_id]').val('');
            $('.selectpicker').selectpicker('refresh');
                       
            $('select[name=group_id]').val('');
            $('.selectpicker').selectpicker('refresh');
                       
            $('select[name=monitorstock]').val('');
            $('.selectpicker').selectpicker('refresh');
            
            $('select[name=cd_applicable]').val('');
            $('.selectpicker').selectpicker('refresh');
                       
            $('select[name=hsn_code]').val('');
            $('.selectpicker').selectpicker('refresh');
                       
            $('select[name=isactive]').val('');
            $('.selectpicker').selectpicker('refresh');
            $(".addedtr").remove();           
            $('.saveBtn').show();
            $('.saveBtn2').show();
            $('.updateBtn').hide();
            $('.updateBtn2').hide();
            
        });
        
    // Cancel selected data
        $(".cancelBtn").click(function(){
            $('#tds_code').val('');
            $('#item_code').val('');
            $('#tds_name').val('');
            $('#opening_stock').val('');
            $('select[name=isactiveAll]').val('');
            $('.selectpicker').selectpicker('refresh');
                        
            $('select[name=tax]').val('1');
            $('.selectpicker').selectpicker('refresh');
                       
            $('select[name=unit]').val('');
            $('.selectpicker').selectpicker('refresh');
                       
            $('select[name=subgroup_id]').val('');
            $('.selectpicker').selectpicker('refresh');
                       
            $('select[name=group_id]').val('');
            $('.selectpicker').selectpicker('refresh');
                       
            $('select[name=monitorstock]').val('');
            $('.selectpicker').selectpicker('refresh');
            
            $('select[name=cd_applicable]').val('');
            $('.selectpicker').selectpicker('refresh');
                       
            $('select[name=hsn_code]').val('');
            $('.selectpicker').selectpicker('refresh');
                       
            $('select[name=isactive]').val('');
            $('.selectpicker').selectpicker('refresh');
            $(".addedtr").remove();           
            $('.saveBtn').show();
            $('.saveBtn2').show();
            $('.updateBtn').hide();
            $('.updateBtn2').hide();
            
        });
        
    // On Blur ItemID Get All Date
        $('#tds_code').blur(function(){ 
            ItemID = $(this).val();
            if(ItemID == ''){
                
            }else{
                $.ajax({
                url:"<?php echo admin_url(); ?>invoice_items/GetItemDetailByID",
                dataType:"JSON",
                method:"POST",
                data:{ItemID:ItemID},
                beforeSend: function () {
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
                },
                complete: function () {
                $('.searchh2').css('display','none');
                },
                success:function(data){
                    init_selectpicker();
                    if(data == null){
                       // alert('Item not found...')
                        $('#item_code').val('');
                        $('#tds_name').val('');
                        $('#opening_stock').val('');
                        $('select[name=isactiveAll]').val('');
                        $('.selectpicker').selectpicker('refresh');
                        
                        $('select[name=tax]').val('1');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=unit]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=subgroup_id]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=group_id]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=monitorstock]').val('');
                        $('.selectpicker').selectpicker('refresh');
                        
                        $('select[name=cd_applicable]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=hsn_code]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=isactive]').val('');
                        $('.selectpicker').selectpicker('refresh');
                        $(".addedtr").remove();           
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                    }else{
                        $('#fill_ItemID').val(data.ItemID);
                        $('#tds_code').val(data.ItemID);
                       $('#item_code').val(data.ItemID);
                       $('#tds_name').val(data.ItemName);
                       $('#opening_stock').val(data.OQty);
                       $('#base_value').val(data.base_value);
                       $('select[name=tax]').val(data.tax).selectpicker('refresh');
                       $('select[name=unit]').val(data.unit).selectpicker('refresh');
                       $('select[name=subgroup_id]').val(data.subgroup_id).selectpicker('refresh');
                       $('select[name=group_id]').val(data.DivisionID).selectpicker('refresh');
                       $('select[name=monitorstock]').val(data.MonitorStock).selectpicker('refresh');
                       $('select[name=cd_applicable]').val(data.cd_applicable).selectpicker('refresh');
                       $('select[name=hsn_code]').val(data.hsn_code).selectpicker('refresh');
                       $('select[name=isactive]').val(data.isactive).selectpicker('refresh');
                       let ItemParameter = data.Parameter;
						for(var count = 0; count < ItemParameter.length; count++)
                            {
                                var tblid = ItemParameter[count].id;
                                var ItemParameterID = ItemParameter[count].ItemParameterID;
                                var MinValue = ItemParameter[count].MinValue;
                                var MaxValue = ItemParameter[count].MaxValue;
                                var BaseValue = ItemParameter[count].BaseValue;
								
								
								var lasttr = $('#parameter_body tr:last td').find("select").attr('id');
								var num= lasttr.match(/-?\d+\.?\d*/);
								var newcount = parseInt(num)+parseInt(1);
								
								
								markup = "<tr class='addedtr'><td><input type='hidden' name='addtblid[]' value='"+tblid+"'><select name='fill_parameter1[]' required id='fill_parameter"+newcount+"' value='"+ItemParameterID+"' class='form-control selectpicker' data-live-search='true'></select></td>";
								markup += "<td><input name='fill_MinValue1[]' id='fill_MinValue"+newcount+"' value='"+MinValue+"' class='form-control '></td>";
								markup += "<td><input type='text' name='fill_MaxValue1[]' id='fill_MaxValue"+newcount+"' value='"+MaxValue+"' class='form-control '></td>";
								markup += "<td><input type='text' name='fill_BaseValue1[]' id='fill_BaseValue"+newcount+"' value='"+BaseValue+"'  class='form-control inputheight'></td>";
								markup += "<td></td></tr>";
								tableBody = $("#parameter_body");
								tableBody.append(markup);
								
								for (var i = 0; i < allParameter.length; i++) {
									$("#fill_parameter"+newcount).append(new Option(allParameter[i].ItemParameterName, allParameter[i].ItemParameterID));
								}
								
								// $("#fill_parameter"+newcount).val(ItemParameterID);
								// $("#fill_parameter"+newcount).selectpicker('refresh');
							}
                       
                       $('.saveBtn').hide();
                       $('.updateBtn').show();
                       $('.saveBtn2').hide();
                       $('.updateBtn2').show();
                    } 
                }
            });
            }
            
        });
        
        $('.get_ItemID').on('click',function(){ 
            ItemID = $(this).attr("data-id");
            $.ajax({
                url:"<?php echo admin_url(); ?>invoice_items/GetItemDetailByID",
                dataType:"JSON",
                method:"POST",
                data:{ItemID:ItemID},
                beforeSend: function () {
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
                },
                complete: function () {
                $('.searchh2').css('display','none');
                },
                success:function(data){
                    init_selectpicker();
                    $('#fill_ItemID').val(data.ItemID);
                       $('#tds_code').val(data.ItemID);
                       $('#item_code').val(data.ItemID);
                       $('#opening_stock').val(data.OQty);
                       $('#tds_name').val(data.ItemName);
                       $('#base_value').val(data.base_value);
                       $('select[name=tax]').val(data.tax).selectpicker('refresh');
                       $('select[name=unit]').val(data.unit).selectpicker('refresh');
                       $('select[name=subgroup_id]').val(data.subgroup_id).selectpicker('refresh');
                       $('select[name=group_id]').val(data.DivisionID).selectpicker('refresh');
                       $('select[name=monitorstock]').val(data.MonitorStock).selectpicker('refresh');
                       $('select[name=cd_applicable]').val(data.cd_applicable).selectpicker('refresh');
                       $('select[name=hsn_code]').val(data.hsn_code).selectpicker('refresh');
                       $('select[name=isactive]').val(data.isactive).selectpicker('refresh');
                        let ItemParameter = data.Parameter;
						for(var count = 0; count < ItemParameter.length; count++)
                            {
                                var tblid = ItemParameter[count].id;
                                var ItemParameterID = ItemParameter[count].ItemParameterID;
                                var MinValue = ItemParameter[count].MinValue;
                                var MaxValue = ItemParameter[count].MaxValue;
                                var BaseValue = ItemParameter[count].BaseValue;
								
								
								var lasttr = $('#parameter_body tr:last td').find("select").attr('id');
								var num= lasttr.match(/-?\d+\.?\d*/);
								var newcount = parseInt(num)+parseInt(1);
								
								markup = "<tr class='addedtr'><td><input type='hidden' name='addtblid[]' value='"+tblid+"'><select name='fill_parameter1[]' required id='fill_parameter"+newcount+"' value='"+ItemParameterID+"' class='form-control selectpicker' data-live-search='true'></select></td>";
								markup += "<td><input name='fill_MinValue1[]' id='fill_MinValue"+newcount+"' value='"+MinValue+"' class='form-control '></td>";
								markup += "<td><input type='text' name='fill_MaxValue1[]' id='fill_MaxValue"+newcount+"' value='"+MaxValue+"' class='form-control '></td>";
								markup += "<td><input type='text' name='fill_BaseValue1[]' id='fill_BaseValue"+newcount+"' value='"+BaseValue+"'  class='form-control inputheight'></td>";
								markup += "<td></td></tr>";
								tableBody = $("#parameter_body");
								tableBody.append(markup);
								
								for (var i = 0; i < allParameter.length; i++) {
									$("#fill_parameter"+newcount).append(new Option(allParameter[i].ItemParameterName, allParameter[i].ItemParameterID));
								}
								
								// $("#fill_parameter"+newcount).val(ItemParameterID);
								// $("#fill_parameter"+newcount).selectpicker('refresh');
                                
							}
                       
                       $('.saveBtn').hide();
                       $('.updateBtn').show();
                       $('.saveBtn2').hide();
                       $('.updateBtn2').show();
                }
            });
            $('#Item_List').modal('hide');
        });
        
    // Save New Item
        $('.saveBtn').on('click',function(){ 
            item_code = $('#tds_code').val();
            description = $('#tds_name').val();
            tax = $('#tax').val();
            unit = $('#unit').val();
            subgroup_id = $('#subgroup_id').val();
            group_id = $('#group_id').val();
            monitorstock = $('#monitorstock').val();
            cd_applicable = $('#cd_applicable').val();
            hsn_code = $('#hsn_code').val();
            opening_stock = $('#opening_stock').val();
            isactive = $('#isactive').val();
	        ItemID = $(this).val();
	        base_value = $('#base_value').val()
	        
	        let ParadataArr = [];
		    var i = 1;
		    var fill_parameter = $("select[name='fill_parameter1[]']")
    		.map(function(){return $(this).val();}).get();
    		fill_parameter.forEach(function callback(value, index) {
    			if(value != "")
    			{
    				var fill_MinValue = $("input[name='fill_MinValue1[]']")
    				.map(function(){return $(this).val();}).get()[index];
    				
    				var fill_MaxValue = $("input[name='fill_MaxValue1[]']")
    				.map(function(){return $(this).val();}).get()[index];
    				var fill_BaseValue = $("input[name='fill_BaseValue1[]']")
    				.map(function(){return $(this).val();}).get()[index];
    				
    				var ii = i - 1;
    				ParadataArr[ii]=new Array();
    				ParadataArr[ii][0]=value;
    				ParadataArr[ii][1]=fill_MinValue;
    				ParadataArr[ii][2]=fill_MaxValue;
    				ParadataArr[ii][3]=fill_BaseValue;
    				i++;
    			}
    		});
		
		    let paradataArraylength = ParadataArr.length;
		    var paradataSerializedArr = JSON.stringify(ParadataArr);
        if(item_code == ''){
            alert('please enterItemID');
            $('#tds_code').focus();
        }else{
            $.ajax({
                url:"<?php echo admin_url(); ?>invoice_items/SaveItemID",
                dataType:"JSON",
                method:"POST",
                data:{item_code:item_code,description:description,tax:tax,unit:unit,subgroup_id:subgroup_id,group_id:group_id,opening_stock:opening_stock,
                    monitorstock:monitorstock,cd_applicable:cd_applicable,hsn_code:hsn_code,isactive:isactive,base_value:base_value,paradataArraylength:paradataArraylength,paradataSerializedArr:paradataSerializedArr
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
                       $('#tds_code').val('');
                       $('#item_code').val('');
                        $('#tds_name').val('');
                        $('#opening_stock').val('');
                        $('#base_value').val('');
                        
                        $('select[name=isactiveAll]').val('');
                        $('.selectpicker').selectpicker('refresh');
                        
                        $('select[name=tax]').val('1');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=unit]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=subgroup_id]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=group_id]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=monitorstock]').val('');
                        $('.selectpicker').selectpicker('refresh');
                        
                        $('select[name=cd_applicable]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=hsn_code]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=isactive]').val('');
                        $('.selectpicker').selectpicker('refresh');
                        $(".addedtr").remove();
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                   }else{
                       alert_float('warning', 'Something went wrong...');
                   }
                }
            });    
        }
            
        });
    // Update Exiting Item
        $('.updateBtn').on('click',function(){ 
            item_code = $('#tds_code').val();
            description = $('#tds_name').val();
            tax = $('#tax').val();
            unit = $('#unit').val();
            subgroup_id = $('#subgroup_id').val();
            group_id = $('#group_id').val();
            monitorstock = $('#monitorstock').val();
            cd_applicable = $('#cd_applicable').val();
            hsn_code = $('#hsn_code').val();
            opening_stock = $('#opening_stock').val();
            isactive = $('#isactive').val();
            base_value = $('#base_value').val();
            let ParadataArr = [];
		    var i = 1;
		    var fill_parameter = $("select[name='fill_parameter1[]']")
    		.map(function(){return $(this).val();}).get();
    		fill_parameter.forEach(function callback(value, index) {
    			if(value != "")
    			{
    				var fill_MinValue = $("input[name='fill_MinValue1[]']")
    				.map(function(){return $(this).val();}).get()[index];
    				
    				var fill_MaxValue = $("input[name='fill_MaxValue1[]']")
    				.map(function(){return $(this).val();}).get()[index];
    				
    				var fill_BaseValue = $("input[name='fill_BaseValue1[]']")
    				.map(function(){return $(this).val();}).get()[index];
    				
    				var addtblid = $("input[name='addtblid[]']")
				    .map(function(){return $(this).val();}).get()[index];
    				
    				var ii = i - 1;
    				ParadataArr[ii]=new Array();
    				ParadataArr[ii][0]=value;
    				ParadataArr[ii][1]=fill_MinValue;
    				ParadataArr[ii][2]=fill_MaxValue;
    				ParadataArr[ii][3]=fill_BaseValue;
    				ParadataArr[ii][4]=addtblid;
    				i++;
    			}
    		});
		
		    let paradataArraylength = ParadataArr.length;
		    var paradataSerializedArr = JSON.stringify(ParadataArr);
	        
            $.ajax({
                url:"<?php echo admin_url(); ?>invoice_items/UpdateItemID",
                dataType:"JSON",
                method:"POST",
                data:{item_code:item_code,description:description,tax:tax,unit:unit,subgroup_id:subgroup_id,group_id:group_id,opening_stock:opening_stock,
                    monitorstock:monitorstock,cd_applicable:cd_applicable,hsn_code:hsn_code,isactive:isactive,base_value:base_value,paradataArraylength:paradataArraylength,paradataSerializedArr:paradataSerializedArr
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
                       alert_float('success', 'Record updated successfully...');
                       $('#tds_code').val('');
                       $('#item_code').val('');
                        $('#tds_name').val('');
                        $('#opening_stock').val('');
                        $('#base_value').val('');
                        $('select[name=isactiveAll]').val('');
                        $('.selectpicker').selectpicker('refresh');
                        
                        $('select[name=tax]').val('1');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=unit]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=subgroup_id]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=group_id]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                                   
                        $('select[name=monitorstock]').val('');
                        $('.selectpicker').selectpicker('refresh');
                        
                        $('select[name=cd_applicable]').val('');
                        $('.selectpicker').selectpicker('refresh');
                        
                        $('select[name=cd_applicable]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=hsn_code]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=isactive]').val('');
                        $('.selectpicker').selectpicker('refresh');
                        $(".addedtr").remove();           
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                   }else{
                       alert_float('warning', 'Something went wrong...');
                   }
                }
            });
        });
    });
</script>

<script>
     function myFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("table_Item_List");
  tr = table.getElementsByTagName("tr");
   for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
      td1 = tr[i].getElementsByTagName("td")[1];
      td2 = tr[i].getElementsByTagName("td")[2];
      td3 = tr[i].getElementsByTagName("td")[3];
      td4 = tr[i].getElementsByTagName("td")[4];
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
      }else if(td4){
         txtValue = td4.textContent || td4.innerText;
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
}
 </script>
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


<script type="text/javascript">
    function printPage() {
        var html_filter_name = $('.report_for').html();
        var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">' + document.getElementsByTagName('table')[1].innerHTML + '</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Item List Report</td>';
        heading_data += '</tr>';
        

        heading_data += '</tbody></table>';
        var print_data = stylesheet + heading_data + tableData
        newWin = window.open("");
        newWin.document.write(print_data);
        newWin.print();
        newWin.close();
    };
</script>
<script>
$("#caexcel").click(function(){
    var data_val = "data";
    $.ajax({
        url:"<?php echo admin_url(); ?>invoice_items/export_ItemMaster",
        method:"POST",
        data:{data_val:data_val,},
        beforeSend: function () {
            $('#searchh3').css('display','block');
        },
        complete: function () {
            $('#searchh3').css('display','none');
        },
        success:function(data){
            response = JSON.parse(data);
            window.location.href = response.site_url+response.filename;
        }
    });
});


</script> 
<style>

#tds_code {
    text-transform: uppercase;
}
#table_Item_List td:hover {
    cursor: pointer;
}
#table_Item_List tr:hover {
    background-color: #ccc;
}

    .table-Item_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-Item_List thead th { position: sticky; top: 0; z-index: 1; }
    .table-Item_List tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>