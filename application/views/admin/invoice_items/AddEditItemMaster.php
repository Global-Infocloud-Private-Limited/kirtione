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
    							<li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Item Master</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
    					
                <div class="row">
                    <div class="col-md-12">
                        <div class="searchh2" style="display:none;">Please wait fetching data...</div>
                        <div class="searchh3" style="display:none;">Please wait Create new ItemID...</div>
                    </div>
                    <br>
                    <div class="col-md-2">
                        <?php echo render_input('item_code1','Item ID','','text'); ?>
                        <input type="hidden" id="item_code" name="item_code" class="form-control" value="0">
                        <span id="lblError" style="color: red"></span>
                    </div>
                    <div class="col-md-4">
                        <?php echo render_input('description','invoice_item_add_edit_description'); ?>
                        <input type="hidden" id="rate" name="rate" class="form-control" value="0">
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="tax"><?php echo _l('gst'); ?></label>
                            <select class="selectpicker display-block"  id="tax" name="tax" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
                                <!--<option value=""></option>-->
                                <?php foreach($taxes as $tax){ ?>
                                <option value="<?php echo $tax['id']; ?>" ><?php echo $tax['taxrate']; ?>%</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="unit"><?php echo _l('measured_in'); ?></label>
                            <select class="selectpicker display-block"  name="unit" id="unit" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
                                <option value="Pcs">Pcs</option>
                                <option value="Kgs">Kgs</option>
                                <option value="Gms">Gms</option>
                                <option value="Ltrs">Ltrs</option>
                                <option value="Mtrs">Mtrs</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                         <?php echo render_select('subgroup_id',$items_sub_groups,array('id','name'),'item_sub_group'); ?>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="control-label" for="group_id">Division</label>
                        <select class="selectpicker" name="group_id" id="group_id" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
                            <?php
                                foreach ($items_groups as $key => $value) {
                            ?>
                                <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>   
                            <?php   
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">MonitorStock?</label>
                            <select class="selectpicker" id= "monitorstock" name="monitorstock" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
                                <option value="Y">Yes</option> 
                                <option value="N">No</option> 
                            </select>
                        </div>
                    </div>
                    
                    
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">HSN Code</label>
                            <select class="selectpicker" name="hsn_code" id="hsn_code" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
                            <?php
                            foreach ($hsn as $key => $value) {
                            ?>
                                <option value="<?php echo $value['name']; ?>"><?php echo $value['name']; ?></option>   
                            <?php   
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">CD Applicable?</label>
                            <select class="selectpicker" id= "cd_applicable" name="cd_applicable" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
                                <option value="N">No</option> 
                                <option value="Y">Yes</option> 
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3" id="cd_percentage_div" style="display:none;">
                        <div class="form-group">
                            <label class="form-label">CD Percentage</label>
                            <input type="text" class="form-control" id="cd_percentage" name="cd_percentage" value="">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Opening Stock</label>
                            <input type="text" class="form-control" id="opening_stock" name="opening_stock" value="">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Base Value</label>
                            <input type="text" class="form-control" id="base_value" name="base_value" value="">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Is Active?</label>
                            <select class="selectpicker" name="isactive" id="isactive" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
                                <option value="Y">Active</option> 
                                <option value="N">Deactive</option> 
                            </select>
                        </div>
                    </div>


                    <div class="col-md-12" style="margin-top:1%;">
                        <table class="table items table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="20%">QC Parameter</th>
                                    <th width="18%">Min Value</th>
                                    <th width="18%">Max Value</th>
                                    <th width="18%">Base Value</th>
                                    <th width="16%">Status</th>
                                    <th width="10%">&nbsp;</th>
                                </tr>
                            </thead>
                                <tbody id="parameter_body">
                                    <?php
                                    $allParameter = json_encode($parameters);
                                    ?>
                                        <tr>
                                            <td width="30%">
                                                <select id="fill_parameter1" name="fill_parameter" class="selectpicker" data-width="100%" data-none-selected-text="-- Item not found --" data-live-search="false">
                                                    <option value= ''>None selected</option>
                                                    <?php foreach($parameters as $key=>$value){ ?>
                                                        <option value="<?= $value['ItemParameterID']; ?>"><?= $value['ItemParameterName']; ?></option> 
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td width="18%"><input id="fill_MinValue" name="fill_MinValue" type="text" value=""></td>
                                            <td width="18%"><input id="fill_MaxValue" name="fill_MaxValue" type="text" value=""></td>
                                            <td width="18%"><input id="fill_BaseValue" name="fill_BaseValue" type="text" value=""></td>
                                            <td width="16%">
                                                <select id="fill_parameterStatus1" name="fill_parameterStatus" class="selectpicker" data-width="100%" data-none-selected-text="-- Item not found --" data-live-search="false">
                                                    <option value ='Y'>Active</option>
                                                    <option value ='N'>DeActive</option>
                                                </select>
                                            </td>
                                            <td width="10%"><button type="button" onclick="add_row()" class="btn btn-success" title="Add Parameter"><i class="fa fa-plus"></i></button></td>
                                        </tr>
                                </tbody>
                        </table>
                    </div>
                    <br><br>
                    <div class="col-md-12" style="margin-top:2%;">
                        <?php if (has_permission_new('items', '', 'create')) {
                        ?>
                        <button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                        <?php
                        }else{
                        ?>
                        <button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
                        <?php
                        }?>
                        
                        <?php if (has_permission_new('items', '', 'edit')) {
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Is Active?</label>
                                        <select class="selectpicker" name="searchisactive" id="searchisactive" data-width="100%" data-none-selected-text="-- Select --" data-live-search="true">
                                            <option value="Y">Active</option> 
                                            <option value="N">Deactive</option> 
                                            <option value="">All</option> 
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-top: 21px;">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-info SearchItemList" style="margin-right: 25px;">Search</button>
                                    </div>
                                </div>
                                <div class="col-md-5" style="margin-top: 21px;">
                                    <?php if (has_permission_new('items', '', 'export')) {
                                    ?>
                                        <a class="btn btn-default buttons-excel buttons-html2" tabindex="0"
                                            aria-controls="table-trial_bal_report" href="#" id="caexcel"
                                            style="float: left ! important;"><span>Export to Excel</span></a>
                                    <?php } ?>
                                    
                                    <?php if (has_permission_new('items', '', 'print')) {
                                    ?>
                                        <button class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</button>
                                    <?php } ?>
                               </div>
                            <div class="table-Item_List tableFixHead2">
                                <table class="tree table table-striped table-bordered table-Item_List tableFixHead2" id="table_Item_List" width="100%">
                                    <thead>
                                        <tr style="display:none;">
                                            <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                        </tr>
                                        <tr>
                                            <th id="sl" style="text-align:left;">ItemID</th>
                                            <th style="text-align:left;">Item Name</th>
                                            <th style="text-align:left;">MeasuredIn</th>
                                            <th style="text-align:left;">HSN Code</th>
                                            <th style="text-align:left;">Main Group</th>
                                            <th style="text-align:left;"> Sub Group</th>
                                            <th style="text-align:left;">Is Active</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ListTableBody">
                                    
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
		var fill_parameter = $("#fill_parameter1").val();
		var fill_MinValue = $("#fill_MinValue").val();
		var fill_MaxValue = $("#fill_MaxValue").val();
		var fill_BaseValue = $("#fill_BaseValue").val();
		var fill_status = $("#fill_parameterStatus1").val();
		
		if(fill_parameter != '' && fill_MinValue !='' && fill_MaxValue !='' && fill_BaseValue !='' ){
			var lasttr = $('#parameter_body tr:last td').find("select").attr('id');
			var num= lasttr.match(/-?\d+\.?\d*/);
			var newcount = parseInt(num)+parseInt(1);
			var allParameter = <?= $allParameter?>;
			
			markup = "<tr class='addedtr'><td><select name='fill_parameter1[]'  required id='fill_parameter"+newcount+"' value='"+fill_parameter+"' class='form-control selectpicker' data-live-search='true'></select></td>";
			markup += "<td><input name='fill_MinValue1[]' id='fill_MinValue"+newcount+"' value='"+fill_MinValue+"' class='form-control'></td>";
			markup += "<td><input type='text' name='fill_MaxValue1[]' id='fill_MaxValue"+newcount+"' value='"+fill_MaxValue+"' class='form-control '></td>";
			markup += "<td><input type='text' name='fill_BaseValue1[]' id='fill_BaseValue"+newcount+"' value='"+fill_BaseValue+"'  class='form-control inputheight'></td>";
			markup += "<td><select name='fill_parameterStatus1[]'  required id='fill_parameterStatus"+newcount+"' value='"+fill_status+"' class='form-control selectpicker' data-live-search='true'></select></td>";
			markup += "<td><a href='#' style='float:right;padding: 2px;width: 30px; float:right;' style='float:right' id='removebtn' class='btn btn-danger removebtn'><i class='fa fa-times'></i></a></td></tr>";
			tableBody = $("#parameter_body");
			tableBody.append(markup);
			
			for (var i = 0; i < allParameter.length; i++) {
				$("#fill_parameter"+newcount).append(new Option(allParameter[i].ItemParameterName, allParameter[i].ItemParameterID));
			}
			
			$("#fill_parameterStatus"+newcount).append(new Option('Active', "Y"));
			$("#fill_parameterStatus"+newcount).append(new Option('DeActive', "N"));
			
			$("#fill_parameter"+newcount).val(fill_parameter);
			$("#fill_parameter"+newcount).selectpicker('refresh');
			$("#fill_parameterStatus"+newcount).val(fill_status);
			$("#fill_parameterStatus"+newcount).selectpicker('refresh');
			
			
			$("#fill_parameter1").val('');
			$('#fill_parameter1').selectpicker('refresh');
			$("#fill_parameterstatus1").val('Y');
			$('#fill_parameterstatus1').selectpicker('refresh');
			$("#fill_MinValue").val('');
			$("#fill_MaxValue").val('');
			$("#fill_BaseValue").val('');
			
		}else
		{
			alert('All Fields Are Required');
		}
		
	}
</script>
<script>
//=========================== Load Table data ==================================
    function load_data()
    {
        var searchisactive = $('#searchisactive').val();
        $.ajax({
            url:"<?php echo admin_url(); ?>invoice_items/GetItemList",
            method:"POST",
            cache: false,
            data:{searchisactive:searchisactive,},
            success:function(data){
                $("#ListTableBody").html(data);
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
                       $('#item_code1').val(data.ItemID);
                       $('#item_code').val(data.ItemID);
                       $('#opening_stock').val(data.OQty);
                       $('#description').val(data.ItemName);
                       $('#base_value').val(data.base_value);
                       $('select[name=tax]').val(data.tax).selectpicker('refresh');
                       $('select[name=unit]').val(data.unit).selectpicker('refresh');
                       $('select[name=subgroup_id]').val(data.subgroup_id).selectpicker('refresh');
                       $('select[name=group_id]').val(data.DivisionID).selectpicker('refresh');
                       $('select[name=monitorstock]').val(data.MonitorStock).selectpicker('refresh');
                       $('select[name=cd_applicable]').val(data.cd_applicable).selectpicker('refresh');
                       if(data.cd_applicable == "Y"){
                          $("#cd_percentage_div").css("display", "block"); 
                          $("#cd_percentage").val(data.cd_percentage); 
                       }else{
                           $("#cd_percentage_div").css("display", "none"); 
                           $("#cd_percentage").val(0);
                       }
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
                            var ParameterStatus = ItemParameter[count].Status;
							
							
							var lasttr = $('#parameter_body tr:last td').find("select").attr('id');
							var num= lasttr.match(/-?\d+\.?\d*/);
							var newcount = parseInt(num)+parseInt(1);
							
							var allParameter = <?= $allParameter?>;
							
							markup = "<tr class='addedtr'><td><input type='hidden' name='addtblid[]' value='"+tblid+"'><select name='fill_parameter1[]' required id='fill_parameter"+newcount+"' value='"+ItemParameterID+"' class='form-control selectpicker' data-live-search='true'></select></td>";
							markup += "<td><input name='fill_MinValue1[]' id='fill_MinValue"+newcount+"' value='"+MinValue+"' class='form-control '></td>";
							markup += "<td><input type='text' name='fill_MaxValue1[]' id='fill_MaxValue"+newcount+"' value='"+MaxValue+"' class='form-control '></td>";
							markup += "<td><input type='text' name='fill_BaseValue1[]' id='fill_BaseValue"+newcount+"' value='"+BaseValue+"'  class='form-control inputheight'></td>";
							markup += "<td><select name='fill_parameterStatus1[]' required id='fill_parameterStatus"+newcount+"' value='"+ParameterStatus+"' class='form-control selectpicker' data-live-search='true'></select></td>";
							markup += "<td></td></tr>";
							tableBody = $("#parameter_body");
							tableBody.append(markup);
							
							for (var i = 0; i < allParameter.length; i++) {
								$("#fill_parameter"+newcount).append(new Option(allParameter[i].ItemParameterName, allParameter[i].ItemParameterID));
							}
							
							$("#fill_parameter"+newcount).val(ItemParameterID);
							$("#fill_parameter"+newcount).selectpicker('refresh');
							
							$("#fill_parameterStatus"+newcount).append(new Option('Active', 'Y'));
							$("#fill_parameterStatus"+newcount).append(new Option('DeActive', 'N'));
                            $("#fill_parameterStatus"+newcount).val(ParameterStatus);
							$("#fill_parameterStatus"+newcount).selectpicker('refresh');
						}
                           
                       $('.saveBtn').hide();
                       $('.updateBtn').show();
                       $('.saveBtn2').hide();
                       $('.updateBtn2').show();
                    }
                });
                $('#Item_List').modal('hide');
            });
            }
        });
    }
    
    $(".SearchItemList").click(function(){
        load_data();
    });
    
    $("#item_code1").dblclick(function(){
        $('#Item_List').modal('show');
        $('#Item_List').on('shown.bs.modal', function () {
            $('#myInput1').val("");
            $('#myInput1').focus();
            load_data();
        });
    });
    $(document).ready(function(){
        $('.updateBtn').hide();
        $('.updateBtn2').hide();
        
    // ItemID Typing Validation
        $("#item_code1").keypress(function (e) {
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
    $("#cd_applicable").change(function(){
        val = $('#cd_applicable').val();
        if(val == "Y"){
            $("#cd_percentage_div").css("display", "block");
        }else{
            $("#cd_percentage_div").css("display", "none");
            $("#cd_percentage").val(0);
        }
    })
//============================= Reset Form =====================================
    function ResetForm()
    {
        /*$('#item_code1').val('');
        $('#item_code').val('');*/
        $('#description').val('');
        $('#opening_stock').val('');
        $('#base_value').val('');
        $('select[name=cd_applicable]').val('N');
        $('.selectpicker').selectpicker('refresh');
        
        $("#cd_percentage_div").css("display", "none");
        $("#cd_percentage").val(0);
        
        $('select[name=isactiveAll]').val('Y');
        $('.selectpicker').selectpicker('refresh');
                    
        $('select[name=tax]').val('1');
        $('.selectpicker').selectpicker('refresh');
                   
        $('select[name=unit]').val('Pcs');
        $('.selectpicker').selectpicker('refresh');
                   
        $('select[name=subgroup_id]').val('');
        $('.selectpicker').selectpicker('refresh');
                   
        $('select[name=group_id]').val('1');
        $('.selectpicker').selectpicker('refresh');
                   
        $('select[name=monitorstock]').val('Y');
        $('.selectpicker').selectpicker('refresh');
        
        $('select[name=cd_applicable]').val('N');
        $('.selectpicker').selectpicker('refresh');
                   
        $('select[name=hsn_code]').val('48043100');
        $('.selectpicker').selectpicker('refresh');
                   
        $('select[name=isactive]').val('Y');
        $('.selectpicker').selectpicker('refresh');
        $(".addedtr").remove();           
        $('.saveBtn').show();
        $('.saveBtn2').show();
        $('.updateBtn').hide();
        $('.updateBtn2').hide();
    }
    // Empty and open create mode
    $("#item_code1").focus(function(){
        $('#item_code1').val('');
        $('#item_code').val('');
        ResetForm(); 
    });
        
    // Cancel selected data
    $(".cancelBtn").click(function(){
        $('#item_code1').val('');
        $('#item_code').val('');
        ResetForm(); 
    });
        
    // On Blur ItemID Get All Date
        $('#item_code1').blur(function(){ 
            ItemID = $(this).val();
            if(ItemID == ''){
                $('#item_code1').val('');
                $('#item_code').val('');
                ResetForm(); 
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
                       ResetForm(); 
                    }else{
                        $('#fill_ItemID').val(data.ItemID);
                       $('#item_code1').val(data.ItemID);
                       $('#item_code').val(data.ItemID);
                       $('#opening_stock').val(data.OQty);
                       $('#description').val(data.ItemName);
                       $('#base_value').val(data.base_value);
                       $('select[name=tax]').val(data.tax).selectpicker('refresh');
                       $('select[name=unit]').val(data.unit).selectpicker('refresh');
                       $('select[name=subgroup_id]').val(data.subgroup_id).selectpicker('refresh');
                       $('select[name=group_id]').val(data.DivisionID).selectpicker('refresh');
                       $('select[name=monitorstock]').val(data.MonitorStock).selectpicker('refresh');
                       $('select[name=cd_applicable]').val(data.cd_applicable).selectpicker('refresh');
                       if(data.cd_applicable == "Y"){
                          $("#cd_percentage_div").css("display", "block"); 
                          $("#cd_percentage").val(data.cd_percentage); 
                       }else{
                           $("#cd_percentage_div").css("display", "none"); 
                           $("#cd_percentage").val(0);
                       }
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
                            var ParameterStatus = ItemParameter[count].Status;
							
							
							var lasttr = $('#parameter_body tr:last td').find("select").attr('id');
							var num= lasttr.match(/-?\d+\.?\d*/);
							var newcount = parseInt(num)+parseInt(1);
							
							var allParameter = <?= $allParameter?>;
							
							markup = "<tr class='addedtr'><td><input type='hidden' name='addtblid[]' value='"+tblid+"'><select name='fill_parameter1[]' required id='fill_parameter"+newcount+"' value='"+ItemParameterID+"' class='form-control selectpicker' data-live-search='true'></select></td>";
							markup += "<td><input name='fill_MinValue1[]' id='fill_MinValue"+newcount+"' value='"+MinValue+"' class='form-control '></td>";
							markup += "<td><input type='text' name='fill_MaxValue1[]' id='fill_MaxValue"+newcount+"' value='"+MaxValue+"' class='form-control '></td>";
							markup += "<td><input type='text' name='fill_BaseValue1[]' id='fill_BaseValue"+newcount+"' value='"+BaseValue+"'  class='form-control inputheight'></td>";
							markup += "<td><select name='fill_parameterStatus1[]' required id='fill_parameterStatus"+newcount+"' value='"+ParameterStatus+"' class='form-control selectpicker' data-live-search='true'></select></td>";
							markup += "<td></td></tr>";
							tableBody = $("#parameter_body");
							tableBody.append(markup);
							
							for (var i = 0; i < allParameter.length; i++) {
								$("#fill_parameter"+newcount).append(new Option(allParameter[i].ItemParameterName, allParameter[i].ItemParameterID));
							}
							
							$("#fill_parameter"+newcount).val(ItemParameterID);
							$("#fill_parameter"+newcount).selectpicker('refresh');
							
							$("#fill_parameterStatus"+newcount).append(new Option('Active', 'Y'));
							$("#fill_parameterStatus"+newcount).append(new Option('DeActive', 'N'));
                            $("#fill_parameterStatus"+newcount).val(ParameterStatus);
							$("#fill_parameterStatus"+newcount).selectpicker('refresh');
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
        
        
        
    // Save New Item
        $('.saveBtn').on('click',function(){ 
            item_code = $('#item_code1').val();
            description = $('#description').val();
            tax = $('#tax').val();
            unit = $('#unit').val();
            subgroup_id = $('#subgroup_id').val();
            group_id = $('#group_id').val();
            monitorstock = $('#monitorstock').val();
            cd_applicable = $('#cd_applicable').val();
            cd_percentage = $('#cd_percentage').val();
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
    				
    				var fill_status = $("select[name='fill_parameterStatus1[]']")
    				.map(function(){return $(this).val();}).get()[index];
    				
    				var ii = i - 1;
    				ParadataArr[ii]=new Array();
    				ParadataArr[ii][0]=value;
    				ParadataArr[ii][1]=fill_MinValue;
    				ParadataArr[ii][2]=fill_MaxValue;
    				ParadataArr[ii][3]=fill_BaseValue;
    				ParadataArr[ii][4]=fill_status;
    				i++;
    			}
    		});
		
		    let paradataArraylength = ParadataArr.length;
		    var paradataSerializedArr = JSON.stringify(ParadataArr);
        if(item_code == ''){
            alert('please enterItemID');
            $('#item_code1').focus();
        }else{
            $.ajax({
                url:"<?php echo admin_url(); ?>invoice_items/SaveItemID",
                dataType:"JSON",
                method:"POST",
                data:{item_code:item_code,description:description,tax:tax,unit:unit,subgroup_id:subgroup_id,group_id:group_id,opening_stock:opening_stock,
                    monitorstock:monitorstock,cd_applicable:cd_applicable,cd_percentage:cd_percentage,hsn_code:hsn_code,isactive:isactive,
                    base_value:base_value,paradataArraylength:paradataArraylength,paradataSerializedArr:paradataSerializedArr
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
                       $('#item_code1').val('');
                        $('#item_code').val('');
                       ResetForm(); 
                   }else{
                       alert_float('warning', 'Something went wrong...');
                   }
                }
            });    
        }
            
        });
    // Update Exiting Item
        $('.updateBtn').on('click',function(){ 
            item_code = $('#item_code1').val();
            description = $('#description').val();
            tax = $('#tax').val();
            unit = $('#unit').val();
            subgroup_id = $('#subgroup_id').val();
            group_id = $('#group_id').val();
            monitorstock = $('#monitorstock').val();
            cd_applicable = $('#cd_applicable').val();
            cd_percentage = $('#cd_percentage').val();
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
				    
				    var fill_status = $("select[name='fill_parameterStatus1[]']")
				    .map(function(){return $(this).val();}).get()[index];
				    
    				var ii = i - 1;
    				ParadataArr[ii]=new Array();
    				ParadataArr[ii][0]=value;
    				ParadataArr[ii][1]=fill_MinValue;
    				ParadataArr[ii][2]=fill_MaxValue;
    				ParadataArr[ii][3]=fill_BaseValue;
    				ParadataArr[ii][4]=addtblid;
    				ParadataArr[ii][5]=fill_status;
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
                    monitorstock:monitorstock,cd_applicable:cd_applicable,cd_percentage:cd_percentage,hsn_code:hsn_code,isactive:isactive,
                    base_value:base_value,paradataArraylength:paradataArraylength,paradataSerializedArr:paradataSerializedArr
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
                       $('#item_code1').val('');
                        $('#item_code').val('');
                       ResetForm(); 
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

#item_code1 {
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