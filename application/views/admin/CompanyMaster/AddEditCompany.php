<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-6">
        <div class="panel_s">
          <div class="panel-body">
              <?php //echo form_open('admin/accounts_master/manage_account_group',array('id'=>'account_group_form')); ?>
              <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Company Master</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
                <div class="row">
                    <div class="col-md-12">
                        <div class="searchh2" style="display:none;">Please wait fetching data...</div>
                        <div class="searchh3" style="display:none;">Please wait Create new Company...</div>
                        <div class="searchh4" style="display:none;">Please wait update Comapny...</div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <small class="req text-danger">* </small>
                            <label for="comp_code">Company Code</label>
                            <input type="text" name="comp_code" id="comp_code" class="form-control" value="" >
                                        
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <small class="req text-danger">* </small>
                        <label for="comp_name">Company Name</label>
                        <input type="text" name="comp_name" id="comp_name" class="form-control" value="">
                        <input type="hidden" name="form_mode" id="form_mode" value="add">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="state">
                            <small class="req text-danger">* </small>
                            <label for="state" class="control-label">State</label>
                            <select name="state" id="state" class="selectpicker form-control" data-max-options="1" data-none-selected-text="Non Selected" data-live-search="true">
                                <option></option>
                                <?php 
									foreach ($StateList as $value) { ?>
										<option value="<?php echo html_entity_decode($value['short_name']); ?>"><?php echo $value['state_name'] ?></option>
									<?php }
								?>          
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="city">
                            <small class="req text-danger">* </small>
                            <label for="city" class="control-label">City</label>
                            <select id="city" name="city" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="taluka">
                            <small class="req text-danger">* </small>
                            <label for="taluka" class="control-label">Taluka</label>
                            <select id="taluka" name="taluka" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="pincode">
                            <small class="req text-danger">* </small>
                            <label for="pincode" class="control-label">Pin Code</label>
                            <input type="text"  name="pincode" id="pincode" class="form-control" onchange="validateZipCode" value="" maxlength="6" minlength="6" onkeypress="return isNumber(event)">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <small class="req text-danger">* </small>
                        <label for="address">Address</label>
                        <input type="text" name="address" id="address" class="form-control" value="">
                        <input type="hidden" name="form_mode" id="form_mode" value="add">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="GstNo">
                            <label for="GstNo" class="control-label">GST No</label>
                            <input type="text"  name="GstNo" id="GstNo" class="form-control" >
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="fssai">
                            <label for="fssai" class="control-label">FSSAI No</label>
                            <input type="text"  name="fssai_no" id="fssai_no" class="form-control" >
                        </div>
                    </div>
                </div>

            <div class="row"> 
                    <div class="col-md-12">
                        <?php if (has_permission_new('CompanyMaster', '', 'create')) {
                        ?>
                        <button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                        <?php
                        }else{
                        ?>
                        <button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
                        <?php
                        }?>
                        
                        <?php if (has_permission_new('CompanyMaster', '', 'edit')) {
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
            
            <div class="modal fade CompanyList" id="CompanyList" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog modal-lg" role="document" style="width:800px;">
                        <div class="modal-content">
                        <div class="modal-header" style="padding:5px 10px;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Company List</h4>
                        </div>
                        <div class="modal-body" style="padding:0px 5px !important">
                            
                            <div class="table-CompanyList tableFixHead2">
                                <table class="tree table table-striped table-bordered table-CompanyList tableFixHead2" id="table_CompanyList" width="100%">
                                    <thead>
                                        <tr style="display:none;">
                                            <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                        </tr>
                                        <tr>
                                            <th id="sl" style="text-align:left;">Company Code <span class="up_starting">  &#8593;</span><span class="down" style="display:none;"> &#8593;</span><span class="up" style="display:none;"> &#8595;</span></th>
                                            <th style="text-align:left;">Company Name</th>
                                            <th style="text-align:left;">State</th>
                                            <th style="text-align:left;">City</th>
                                            <th style="text-align:left;">GST No</th>
                                            <th style="text-align:left;">FSSAI No</th>
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
<!--new update -->

<script>
   function validateZipCode(elementValue){
  var zipCodePattern = /^\d{5}$|^\d{5}-\d{4}$/;
  return zipCodePattern.test(elementValue);
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
<script>

    // AccountID Typing Validation
    $("#comp_code").keypress(function (e) {
        var keyCode = e.keyCode || e.which;
        if(keyCode == ""){
            $("#lblError").html("");
        }else{
            var regex = /^[A-Za-z0-9]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            return isValid;
        }
    });
    $('#state').on('change', function() {
		var StateID = $(this).val();
		var url = "<?php echo base_url(); ?>admin/clients/GetCity";
            jQuery.ajax({
                type: 'POST',
                url:url,
                data: {StateID: StateID},
                dataType:'json',
                success: function(data) {
                    $("#city").find('option').remove();
                    $("#city").selectpicker("refresh");
                    $("#city").append(new Option('', 'select city'));
                    for (var i = 0; i < data.length; i++) {
                        $("#city").append(new Option(data[i].city_name, data[i].id));
                    }
                    $('.selectpicker').selectpicker('refresh');
                    // Remove taluka option 
                    $("#taluka").find('option').remove();
                    $('.selectpicker').selectpicker('refresh');
                }
            });
	});
	
	$('#city').on('change', function() {
		var CityID = $(this).val();
		var url = "<?php echo base_url(); ?>admin/clients/GetTaluka";
            jQuery.ajax({
                type: 'POST',
                url:url,
                data: {CityID: CityID},
                dataType:'json',
                success: function(data) {
                    $("#taluka").find('option').remove();
                    $("#taluka").selectpicker("refresh");
                    $("#taluka").append(new Option('', 'select taluka'));
                    for (var i = 0; i < data.length; i++) {
                        $("#taluka").append(new Option(data[i].TalukaName, data[i].id));
                    }
                    $('.selectpicker').selectpicker('refresh');
                }
            });
	});
</script>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
    $("#comp_code").dblclick(function(){
        $('#CompanyList').modal('show');
        $('#CompanyList').on('shown.bs.modal', function () {
            var AccountID = "";
            $.ajax({
                  url:"<?php echo admin_url(); ?>CompanyMaster/CompanyListPopUp",
                  //dataType:"JSON",
                  method:"POST",
                  cache: false,
                  data:{AccountID:AccountID,},
                  success:function(data){
                    if(empty(data)){
                        
                    }else{
                        $("#ListTableBody").html(data);
                        $('.get_AccountID').on('click',function(){ 
                            PlantID = $(this).attr("data-id");
                            $.ajax({
                                url:"<?php echo admin_url(); ?>CompanyMaster/GetCompanyDetailByID",
                                dataType:"JSON",
                                method:"POST",
                                data:{PlantID:PlantID},
                                beforeSend: function () {
                                $('.searchh2').css('display','block');
                                $('.searchh2').css('color','blue');
                                },
                                complete: function () {
                                $('.searchh2').css('display','none');
                                },
                                success:function(data){
                                       $('#comp_code').val(data.PlantID);
                                       $('#comp_name').val(data.PlantName);
                                       $('#fssai_no').val(data.fssai_no);
                                        $('select[name=state]').val(data.state);
                                        $('.selectpicker').selectpicker('refresh');
                                        
                                       let CityList = data.CityList;
                                        $("#city").children().remove();
                                        for (var i = 0; i < CityList.length; i++) {
                                            $("#city").append('<option value="'+CityList[i]["id"]+'">'+CityList[i]["city_name"]+'</option>');
                                        }
                                        $('.selectpicker').selectpicker('refresh');
                                        
                                        $('#city').selectpicker('val', data.city);
                                        $('.selectpicker').selectpicker('refresh');
                                        
                                        let TalukaList = data.TalukaList;
                                        $("#taluka").children().remove();
                                        for (var i = 0; i < TalukaList.length; i++) {
                                            $("#taluka").append('<option value="'+TalukaList[i]["id"]+'">'+TalukaList[i]["TalukaName"]+'</option>');
                                        }
                                        $('.selectpicker').selectpicker('refresh');
                                        
                                        $('#taluka').selectpicker('val', data.taluka);
                                        $('.selectpicker').selectpicker('refresh');
                            		   $('#pincode').val(data.pincode);
                            		   $('#address').val(data.address);
                            		   $('#GstNo').val(data.GstNo);
                                       $('.saveBtn').hide();
                                       $('.updateBtn').show();
                                       $('.saveBtn2').hide();
                                       $('.updateBtn2').show();
                                }
                            });
                            $('#CompanyList').modal('hide');
                        });
                    }
                  }
            });
            $('#myInput1').val('');
              $('#myInput1').focus();
        })
    });
   
    $('.updateBtn').hide();
    $('.updateBtn2').hide();
    
// Focus on GroupID
    $('#comp_code').on('focus',function(){
        $('#comp_code').val('');
        $('#comp_name').val('');
        $('#fssai_no').val('');
        $('select[name=state]').val('');
        $('.selectpicker').selectpicker('refresh');
        $("#city").children().remove();
        $('select[name=city]').val('');
        $('.selectpicker').selectpicker('refresh');
        $("#taluka").children().remove();
        $('select[name=taluka]').val('');
        $('.selectpicker').selectpicker('refresh');
        $('#pincode').val('');
        $('#address').val('');
        $('#GstNo').val('');
        $('.saveBtn').show();
        $('.saveBtn2').show();
        $('.updateBtn').hide();
        $('.updateBtn2').hide();
    });

// Cancel selected data
    $(".cancelBtn").click(function(){
        $('#comp_code').val('');
        $('#comp_name').val('');
        $('#fssai_no').val('');
        $('select[name=state]').val('');
        $('.selectpicker').selectpicker('refresh');
        $("#city").children().remove();
        $('select[name=city]').val('');
        $('.selectpicker').selectpicker('refresh');
        $("#taluka").children().remove();
        $('select[name=taluka]').val('');
        $('.selectpicker').selectpicker('refresh');
        $('#pincode').val('');
        $('#address').val('');
        $('#GstNo').val('');
        $('.saveBtn').show();
        $('.saveBtn2').show();
        $('.updateBtn').hide();
        $('.updateBtn2').hide();
    });
    
// Get Company Detail by PlantID
    $('#comp_code').on('blur',function(){
        var PlantID = $(this).val();
        if(PlantID == ""){
            
        }else{
            $.ajax({
                url:"<?php echo admin_url(); ?>CompanyMaster/GetCompanyDetailByID",
                dataType:"JSON",
                method:"POST",
                data:{PlantID:PlantID},
                beforeSend: function () {
                    $('.searchh2').css('display','block');
                    $('.searchh2').css('color','blue');
                },
                complete: function () {
                    $('.searchh2').css('display','none');
                },
                success:function(data){
                    if(data != null){
                        $('#comp_code').val(data.PlantID);
                        $('#comp_name').val(data.PlantName);
                        $('#fssai_no').val(data.fssai_no);
                        $('select[name=state]').val(data.state);
                        $('.selectpicker').selectpicker('refresh');
                        
                        let CityList = data.CityList;
                        $("#city").children().remove();
                        for (var i = 0; i < CityList.length; i++) {
                        $("#city").append('<option value="'+CityList[i]["id"]+'">'+CityList[i]["city_name"]+'</option>');
                        }
                        $('.selectpicker').selectpicker('refresh');
                        
                        $('#city').selectpicker('val', data.city);
                        $('.selectpicker').selectpicker('refresh');
                        
                        let TalukaList = data.TalukaList;
                        $("#taluka").children().remove();
                        for (var i = 0; i < TalukaList.length; i++) {
                        $("#taluka").append('<option value="'+TalukaList[i]["id"]+'">'+TalukaList[i]["TalukaName"]+'</option>');
                        }
                        $('.selectpicker').selectpicker('refresh');
                        
                        $('#taluka').selectpicker('val', data.taluka);
                        $('.selectpicker').selectpicker('refresh');
                        $('#pincode').val(data.pincode);
                        $('#address').val(data.address);
                        $('#GstNo').val(data.GstNo);
                        $('.saveBtn').hide();
                        $('.updateBtn').show();
                        $('.saveBtn2').hide();
                        $('.updateBtn2').show();
                    }
                }
            });
        }
     })
 
    
   
    
    // Save New Group
        $('.saveBtn').on('click',function(){ 
            comp_code = $('#comp_code').val();
            comp_name = $('#comp_name').val();
            fssai_no = $('#fssai_no').val();
            state = $('#state').val();
            city = $('#city').val();
            taluka = $('#taluka').val();
            pincode = $('#pincode').val();
            address = $('#address').val();
            GstNo = $('#GstNo').val();
            if(comp_code == ""){
                alert('please enter Company Code');
            }else if(comp_name == ""){
                alert('please enter Company Name');
            }else if(comp_name == ""){
                alert('please enter Company Name');
            }else if(state == ""){
                alert('please select Company State');
            }else if(city == ""){
                alert('please select Company City');
            }else if(taluka == ""){
                alert('please select Company taluka');
            }else if(pincode == ""){
                alert('please enter Company pincode');
            }else if(address == ""){
                alert('please enter Company address');
            }else if(fssai_no == ""){
                alert('please enter Company fssai no');
            }else{
                $.ajax({
                    url:"<?php echo admin_url(); ?>CompanyMaster/SaveCompany",
                    dataType:"JSON",
                    method:"POST",
                    data:{comp_code:comp_code,comp_name:comp_name,state:state,city:city,taluka:taluka,pincode:pincode,address:address,GstNo:GstNo,fssai_no:fssai_no
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
                            $('#comp_code').val('');
                            $('#comp_name').val('');
                            $('#fssai_no').val('');
                            $('select[name=state]').val('');
                            $('.selectpicker').selectpicker('refresh');
                            $("#city").children().remove();
                            $('select[name=city]').val('');
                            $('.selectpicker').selectpicker('refresh');
                            $("#taluka").children().remove();
                            $('select[name=taluka]').val('');
                            $('.selectpicker').selectpicker('refresh');
                            $('#pincode').val('');
                            $('#address').val('');
                            $('#GstNo').val('');
                       }else{
                           alert_float('warning', 'Something went wrong...');
                       }
                    }
                });
            }
        }); 
        
    // Update Exiting Item
        $('.updateBtn').on('click',function(){ 
            comp_code = $('#comp_code').val();
            comp_name = $('#comp_name').val();
            fssai_no = $('#fssai_no').val();
            state = $('#state').val();
            city = $('#city').val();
            taluka = $('#taluka').val();
            pincode = $('#pincode').val();
            address = $('#address').val();
            GstNo = $('#GstNo').val();
            if(comp_code == ""){
                alert('please enter Company Code');
            }else if(comp_name == ""){
                alert('please enter Company Name');
            }else if(comp_name == ""){
                alert('please enter Company Name');
            }else if(state == ""){
                alert('please select Company State');
            }else if(city == ""){
                alert('please select Company City');
            }else if(taluka == ""){
                alert('please select Company taluka');
            }else if(pincode == ""){
                alert('please enter Company pincode');
            }else if(address == ""){
                alert('please enter Company address');
            }else if(fssai_no == ""){
                alert('please enter Company fssai no');
            }else{
                $.ajax({
                    url:"<?php echo admin_url(); ?>CompanyMaster/UpdateCompany",
                    dataType:"JSON",
                    method:"POST",
                    data:{comp_code:comp_code,comp_name:comp_name,state:state,city:city,taluka:taluka,pincode:pincode,address:address,GstNo:GstNo,fssai_no:fssai_no
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
                            $('#comp_code').val('');
                            $('#comp_name').val('');
                            $('#fssai_no').val('');
                            $('select[name=state]').val('');
                            $('.selectpicker').selectpicker('refresh');
                            $("#city").children().remove();
                            $('select[name=city]').val('');
                            $('.selectpicker').selectpicker('refresh');
                            $("#taluka").children().remove();
                            $('select[name=taluka]').val('');
                            $('.selectpicker').selectpicker('refresh');
                            $('#pincode').val('');
                            $('#address').val('');
                            $('#GstNo').val('');
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
  table = document.getElementById("table_CompanyList");
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
    #table_CompanyList td:hover {
    cursor: pointer;
}
#table_CompanyList tr:hover {
    background-color: #ccc;
}

    .table-CompanyList          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-CompanyList thead th { position: sticky; top: 0; z-index: 1; }
    .table-CompanyList tbody th { position: sticky; left: 0; }
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

