<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-10">
        <div class="panel_s">
          <div class="panel-body">
              <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>HR </b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Add Sub Admin</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
                <div class="row">
                    <div class="col-md-12">
                        <div class="searchh2" style="display:none;">Please wait while fetching data.</div>
                        <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                        <div class="searchh4" style="display:none;">Please wait while updating data.</div>
                    </div>
                    <br>
                    
                    <div class="col-md-2">
                        <div class="form-group" app-field-wrapper="AccountID">
                            <small class="req text-danger">* </small>
                            <label for="AccountID" class="control-label">Admin Code</label>
                            <input type="text" id="AccountID" name="AccountID" class="form-control" value="" autocomplete="off" />
                            <?php $staff_user_id = $this->session->userdata('staff_user_id'); ?>
                            <input type="hidden" name="staffid" value="<?php echo $staff_user_id; ?>" id="staffid">
                            <input type="hidden" name="userid" value="" id="userid">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <?php echo render_input('firstname','First Name','','text'); ?>
                    </div>
                    
                    <div class="col-md-3">
                        <?php echo render_input('lastname','Last Name','','text'); ?>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group" app-field-wrapper="Aadhaarno">
                            <label for="Aadhaarno" class="control-label">Aadhaar Number</label>
                            <input type="text" maxlength="12" minlength="12"  name="Aadhaarno" pattern="[0-9] {12}" id="Aadhaarno" class="form-control numbersOnly" onkeypress="return isNumber(event)" value="">
                            <span class="aadhar_denger" style="color:red;"></span>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group" app-field-wrapper="Pan"> 
                            <label for="Pan" class="control-label">PAN Number</label>
                            <input type="text" maxlength="10" minlength="10" name="Pan" pattern="[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}" id="Pan" class="form-control" 
                            value="">
                            <span class="pan_denger" style="color:red;"></span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    
                    <div class="col-md-2">
                        <div class="form-group" app-field-wrapper="phonenumber">
                            <small class="req text-danger">* </small>
                            <label for="phonenumber" class="control-label">Mobile Number</label>
                            <input type="text" id="phonenumber" name="phonenumber" class="form-control" value="" maxlength="10" minlength="10" onkeypress="return isNumber(event)">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="email">
                            <label for="email" class="control-label">Email</label>
                            <input type="text" id="email" name="email" class="form-control" value="">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="peremail">
                            <label for="peremail" class="control-label">Optional Email</label>
                            <input type="text" id="peremail" name="peremail" class="form-control" value="">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <?php echo render_date_input( 'birthday', 'Date of Birth','','text'); ?>
                    </div>
                    
                    
					
					 <div class="col-md-2">
						<?php 
						$curDate = date('d/m/Y');
						echo render_date_input('date_of_joining','Date of Joining',$curDate,'date'); ?>
					</div>
                    <div class="clearfix"></div>
                    
                    <div class="col-md-2">
                        <div class="form-group" app-field-wrapper="CenterID">
                            <small class="req text-danger">* </small>
                            <label for="CenterID" class="form-label">Select Centers</label>
                            
                            <select name="CenterID" id="CenterID" class="selectpicker form-control" multiple data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                <option value="">Non Selected</option>
                             <?php
                                foreach ($CenterList as $key => $value) {
                            ?>
                                <option value="<?php echo $value['CenterID']; ?>" <?php ?>><?php echo $value['CenterName']; ?></option>
                            <?php
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="center">
                            <small class="req text-danger">* </small>
                            <label for="ItemID" class="form-label">Select Commodity</label>
                            <select name="ItemID" id="ItemID" class="selectpicker form-control" multiple data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                <option value="">Non Selected</option>
                                
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="state">
                            <small class="req text-danger">* </small>
                            <label for="state" class="form-label">State</label>
                            <select name="state" id="state" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                <option value="">Non Selected</option>
                            <?php
                                foreach ($state as $key => $value) {
                            ?>
                                  <option value="<?php echo $value['short_name'];?>"><?php echo $value['state_name'];?></option>
                            <?php
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group" app-field-wrapper="city">
                           <small class="req text-danger">* </small>
                            <label for="city" class="control-label">City</label>
                            <select class="form-control city selectpicker" data-width="100%" data-none-selected-text="Non Selected" name="city" id="city" data-live-search="true">
                                <option value="">Select city name</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group" app-field-wrapper="zip">
                            <label for="zip" class="control-label">Pin Code</label>
                            <input type="text"  name="zip" id="zip" class="form-control" onchange="validateZipCode" value="" maxlength="6" minlength="6" onkeypress="return isNumber(event)">
                        </div>
                    </div> 

                    <input type="hidden" value="11" id="job_department" name="job_department">
					<input type="hidden" value="21" id="job_position" name="job_position">
                    
					
                    <div class="clearfix"></div>
                     
                    <div class="col-md-2">
						<div class="form-group" app-field-wrapper="ifsc"> 
							<label for="ifsc" class="control-label">IFSC Code</label>
							    <input type="text" maxlength="11" minlength="11"  onblur="getBankDetail(this.value)" name="ifsc"  id="ifsc" class="form-control" 
								value="">
							<span class="pan_denger" style="color:red;"></span>
						</div>
					</div>
                    
                    <div class="col-md-3">	
						<label for="account_number" class="control-label">Account number</label>
						<input type="tel" minlenght="9" maxlength="18"  name="account_number" pattern="[0-9] {10}" id="account_number" class="form-control" value="<?php echo $account_number?>">
						<span class="actnumber_denger" style="color:red;"></span>
					</div>
					
					<div class="col-md-3">
						<?php					
						echo render_input('name_account','Bank Account Name','', 'text'); ?>
					</div>
					
					<div class="col-md-4">
						<div class="form-group" app-field-wrapper="issue_bank"><label for="issue_bank" class="control-label">Bank Name</label><input type="text" readonly id="issue_bank" name="issue_bank" class="form-control" value="<?= $issue_bank?>"></div>
					</div>
					
                    <div class="clearfix"></div>
                    
					<div class="col-md-2">
						<div class="form-group">
							<label for="app_access" class="control-label">SO App Access</label>
							<select name="app_access" class="selectpicker" id="app_access" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
								<option value="No" >No</option>
								<option value="Yes" >Yes</option>
							</select>
						</div>
					</div>
					
		            <div class="col-md-3" id="app_password_div">
						<div class="form-group" app-field-wrapper="app_password">
						    <label for="app_password" class="control-label">App Passward</label>
						    <input type="text" id="app_password" name="app_password" class="form-control">
						 </div>
					</div>
				    
				    <div class="col-md-3">
					<?php 
							echo render_input('DeviceID','DeviceID'); ?>
					</div>
				    
				    <div class="col-md-2">
						<div class="form-group">
                            <label for="active" class="control-label"><?php echo _l('hr_status_work'); ?></label>
                            <select name="active" class="selectpicker" id="active" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                <option value="1">Active</option>
                                <option value="0">De-Active</option>
                            </select>
                        </div>
					</div>
					
		            <div class="clearfix"></div>
                </div>
                
                <div class="btn-bottom-toolbar text-right">
                    <div class="row">
                        <div class="col-md-12">
                            <?php if (has_permission_new('hrm_hr_sub_admin', '', 'create')) {
                            ?>
                            <button type="button" class="btn btn-info saveBtn" onclick="this.disabled = true;" style="margin-right: 25px;">Save</button>
                            <?php
                            }else{
                            ?>
                            <button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
                            <?php
                            }?>
                            
                            <?php if (has_permission_new('hrm_hr_sub_admin', '', 'edit')) {
                            ?>
                            <button type="button" class="btn btn-info updateBtn" onclick="this.disabled = true;" style="margin-right: 25px;">Update</button>
                            <?php
                            }else{
                            ?>
                            <button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
                            <?php
                            }?>
                            <button type="button" class="btn btn-default cancelBtn" >Cancel</button>
                        </div>
                    </div>
                </div>
                
                <div class="clearfix"></div>
            <!-- Iteme List Model-->
            
                <div class="modal fade Account_List" id="Account_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                        <div class="modal-header" style="padding:5px 10px;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Account List</h4>
                        </div>
                        <div class="modal-body" style="padding:0px 5px !important">
                            
                            <div class="table-Account_List tableFixHead2">
                                <table class="tree table table-striped table-bordered table-Account_List tableFixHead2" id="table_Account_List" width="100%">
                                    <thead>
                                        <tr style="display:none;">
                                            <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                        </tr>
                                        <tr>
                                            <th id="sl" style="text-align:left;">Sub Admin ID</th>
                                            <th style="text-align:left;">Full Name</th>
                                            <th style="text-align:left;">Mobile</th>
                                            <th style="text-align:left;">Department</th>
                                            <th style="text-align:left;">Designation</th>
                                            <th style="text-align:left;">Active</th>
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
	function getBankDetail(ifsccode){
		var xhr = new XMLHttpRequest();
		var url = 'https://ifsc.razorpay.com/' + ifsccode;
		
		xhr.onreadystatechange = function() {
			if (xhr.readyState === 4 && xhr.status === 200) {
				var bankDetails = JSON.parse(xhr.responseText);
				var bankName = bankDetails.BANK;
				var bankAddress = bankDetails.ADDRESS;
				
				// Display the bank name and address
				document.getElementById('issue_bank').value = bankName;
				} else if (xhr.readyState === 4 && xhr.status !== 200) {
				// Handle error
				alert('Invalid IFSC Code');
				$('#ifsc').val('');
				$('#issue_bank').val('');
			}
		};
		
		xhr.open('GET', url, true);
		xhr.send();
	}
</script>
<script>
$(document).ready(function(){
    $('#app_password_div').css('display','none');
    $('.updateBtn').hide();
    $('.updateBtn2').hide();
        $("#AccountID").dblclick(function(){
            $('#Account_List').modal('show');
            var SUBAccountID = "1000006";
            $.ajax({
				url:"<?php echo admin_url(); ?>hr_profile/SubAdminListPopUp",
				method:"POST",
				cache: false,
				data:{SUBAccountID:SUBAccountID,},
				success:function(data){
                    if(empty(data)){
                        
					}else{
                        $("#ListTableBody").html(data);
                        $('.get_AccountID').on('click',function(){ 
                        AccountID = $(this).attr("data-id");
                        $.ajax({
                            url:"<?php echo admin_url(); ?>hr_profile/GetAccountDetailByID",
                            dataType:"JSON",
                            method:"POST",
                            data:{AccountID:AccountID},
                            beforeSend: function () {
                                $('.searchh2').css('display','block');
                                $('.searchh2').css('color','blue');
                            },
                            complete: function () {
                                $('.searchh2').css('display','none');
                            },
                            success:function(data){
                                $('#AccountID').val(data.AccountID);
                                $('#firstname').val(data.firstname);
                                $('#lastname').val(data.lastname);
                                $('#userid').val(data.staffid);
                                
                                $('#email').val(data.email);
                                $('#peremail').val(data.peremail);
                                $('#phonenumber').val(data.phonenumber);
                                $('#altphonenumber').val(data.mobile2);
                                if(data.birthday !== null){
                                    var date = data.birthday.substring(0, 10);
                                    var date_new = date.split("-").reverse().join("/");
                                    $('#birthday').val(date_new);
                                }else{
                                    $('#birthday').val('');
                                }
                                var staffid = $('#staffid').val();
                                $('#opening_b').val(data.BAL1);
                                if(staffid !== "3"){
                                    $('#opening_b').attr('disabled','disabled');    
                                }
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
                                
                                $('#zip').val(data.pincode);
                                $('#current_address').val(data.current_address);
                                $('#home_town').val(data.home_town);
                                
                                let Designation = data.DesignationList;
                                $("#job_position").children().remove();
                                for (var i = 0; i < Designation.length; i++) {
                                    $("#job_position").append('<option value="'+Designation[i]["position_id"]+'">'+Designation[i]["position_name"]+'</option>');
                                }
                                $('.selectpicker').selectpicker('refresh');
                                $('#job_position').selectpicker('val', data.job_position);
                                $('.selectpicker').selectpicker('refresh');
                                
                                $('#ifsc').val(data.ifsc);
                                $('#job_department').selectpicker('val', data.job_department);
                                $('.selectpicker').selectpicker('refresh');
                                //Center List updated
                                var AllCenters = data.AllCenters;
                                var AssignedCenters = data.AssignedCenterList;
                                $("#CenterID").children().remove();
                                var CentersArray = [];
                                for (var i = 0; i < AssignedCenters.length; i++) {
                                    CentersArray.push(AssignedCenters[i]["CenterID"]);
                                }
                                for (var i = 0; i < AllCenters.length; i++) {
                                    $("#CenterID").append('<option value="'+AllCenters[i]["CenterID"]+'">'+AllCenters[i]["CenterName"]+'</option>');
                                }
                                $('#CenterID').selectpicker('val', CentersArray);
                                $('.selectpicker').selectpicker('refresh');
                                
                                // Item List append and selected
                                var AllItems = data.AllItems;
                                var AssignedItems = data.AssignedItemList;
                                $("#ItemID").children().remove();
                                var ItemsArray = [];
                                for (var j = 0; j < AssignedItems.length; j++) {
                                    ItemsArray.push(AssignedItems[j]["ItemID"]);
                                }
                                for (var j = 0; j < AllItems.length; j++) {
                                    $("#ItemID").append('<option value="'+AllItems[j]["ItemID"]+'">'+AllItems[j]["ItemName"]+'</option>');
                                }
                                $('#ItemID').selectpicker('val', ItemsArray);
                                $('.selectpicker').selectpicker('refresh');
                                
                                $('#team_manage').selectpicker('val', data.team_manage);
                                $('.selectpicker').selectpicker('refresh');
                                
                                $('#sex').selectpicker('val', data.sex);
                                $('.selectpicker').selectpicker('refresh')
                                $('#Pan').val(data.pan_number);
                                $('#Aadhaarno').val(data.aadhar_number);
                                $('#account_number').val(data.account_number);
                                $('#name_account').val(data.name_account);
                                $('#issue_bank').val(data.issue_bank);
                                
                                $('#literacy').selectpicker('val', data.literacy);
                                $('.selectpicker').selectpicker('refresh');
                                
                                $('#marital_status').selectpicker('val', data.marital_status);
                                $('.selectpicker').selectpicker('refresh');
                                
                                $('#active').selectpicker('val', data.active);
                                $('.selectpicker').selectpicker('refresh');
                                
                                $('#app_access').selectpicker('val', data.app_access);
                                $('.selectpicker').selectpicker('refresh');
                                if(data.app_access == "Yes"){
                                    $('#app_password_div').css('display','block');
                                    $('#app_password').val('');
                                }else{
                                    $('#app_password_div').css('display','none');
                                    $('#app_password').val('');
                                }
                                $('#Movement').selectpicker('val', data.Movement);
                                $('.selectpicker').selectpicker('refresh');
                                if(data.StartDate == '' || data.StartDate == null){
                                    $('#date_of_joining').val('');
                                }else{
                                    var date = data.StartDate.substring(0, 10);
                                    var date_new = date.split("-").reverse().join("/");
                                    $('#date_of_joining').val(date_new);
                                }
                                $('#DeviceID').val(data.DiveceID);
                                $('.updateBtn').removeAttr('disabled');
                                $('.saveBtn').removeAttr('disabled');
                                $('.saveBtn').hide();
                                $('.updateBtn').show();
                                $('.saveBtn2').hide();
                                $('.updateBtn2').show();
                            }
                        });
                            $('#Account_List').modal('hide');
						});
					}
				}
			});
            $('#Account_List').on('shown.bs.modal', function () {
                $('#myInput1').val('');
                $('#myInput1').focus();
            })
        });
    // AccountID Typing Validation
    $("#AccountID").keypress(function (e) {
        var keyCode = e.keyCode || e.which;
        if(keyCode == ""){
            $("#lblError").html("");
        }else{
            var regex = /^[A-Za-z0-9]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            return isValid;
        }
    });
        
    // Pan Number Typing Validation
    $('#Pan').keyup(function(e) {
        var val = $('#Pan').val();
        if(val == ""){
            $(".pan_denger").text(" ");
        }else{
            e.preventDefault();
            if(!$('#Pan').val().match('[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}'))  {
                $(".pan_denger").text("Enter valid PAN number");
            }else{
                $(".pan_denger").text(" ");
            }
        }
    });
        
    $('#phonenumber').keyup(function(e) {
        e.preventDefault();
        if(!$('#phonenumber').val().match('[0-9]{10}'))  {
            
            $(".mob_denger").text("Enter valid 10 digit mobile number");
        }else{
            $(".mob_denger").text(" ");
        }
    });
        
    $('#Aadhaarno').keyup(function(e) {
        e.preventDefault();
        if(!$('#Aadhaarno').val().match('[0-9]{12}'))  {
            
            $(".aadhar_denger").text("Enter valid 12 digit Aadhar number");
        }else{
            $(".aadhar_denger").text(" ");
        }
    });
        
    $('#account_number').keyup(function(e) {
        e.preventDefault();
        if(!$('#account_number').val().match('[0-9]{9}'))  {
            
            $(".actnumber_denger").text("Enter valid Account number");
        }else{
            $(".actnumber_denger").text(" ");
        }
    });
});
    
    // Empty and open create mode
    $("#AccountID").focus(function(){
        $('#AccountID').val('');
        $('#firstname').val('');
        $('#lastname').val('');
        
        $('#email').val('');
        
        $('#phonenumber').val('');
        
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();
        today = dd + '/' + mm + '/' + yyyy;
        $('#birthday').val(today);
        
                   
        $('select[name=state]').val('');
        $('.selectpicker').selectpicker('refresh');
                   
        $("#city").children().remove();
        $('.selectpicker').selectpicker('refresh');
                   
        $('#zip').val('');
        
        $("#job_position").children().remove();
        $('.selectpicker').selectpicker('refresh');
        
        $('select[name=job_department]').val('');
        $('.selectpicker').selectpicker('refresh');
                   
        $('select[name=CenterID]').val('');
        $('.selectpicker').selectpicker('refresh');
        
        $("#ItemID").children().remove();
        $('.selectpicker').selectpicker('refresh');
        
        $('#ifsc').val('');
                   
        $('#sex').selectpicker('val', 'male');
        $('.selectpicker').selectpicker('refresh')
        $('#Pan').val('');
        $('#Aadhaarno').val('');
        $('#account_number').val('');
        $('#name_account').val('');
        $('#issue_bank').val('');
                  
        $('#marital_status').selectpicker('val', 'single');
        $('.selectpicker').selectpicker('refresh');
       
        $('#active').selectpicker('val', '1');
        $('.selectpicker').selectpicker('refresh');
                   
        $('#app_access').selectpicker('val', 'No');
        $('.selectpicker').selectpicker('refresh');
        $('#app_password_div').css('display','none');
        $('#app_password').val('');
        $('#date_of_joining').val(today);
        $('#DeviceID').val('');
       
        $('.updateBtn').removeAttr('disabled');
        $('.saveBtn').removeAttr('disabled');
        $('.saveBtn').show();
        $('.updateBtn').hide();
        $('.saveBtn2').show();
        $('.updateBtn2').hide();
    });
        
    // Cancel selected data
    $(".cancelBtn").click(function(){
        $('#AccountID').val('');
        $('#firstname').val('');
        $('#lastname').val('');
        
        $('#email').val('');
        
        $('#phonenumber').val('');
        
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();
        today = dd + '/' + mm + '/' + yyyy;
        $('#birthday').val(today);
        
                   
        $('select[name=state]').val('');
        $('.selectpicker').selectpicker('refresh');
                   
        $("#city").children().remove();
        $('.selectpicker').selectpicker('refresh');
                   
        $('#zip').val('');
        
        $("#job_position").children().remove();
        $('.selectpicker').selectpicker('refresh');
        
        $('select[name=job_department]').val('');
        $('.selectpicker').selectpicker('refresh');
                   
        $('select[name=CenterID]').val('');
        $('.selectpicker').selectpicker('refresh');
        
        $("#ItemID").children().remove();
        $('.selectpicker').selectpicker('refresh');
        
        $('#ifsc').val('');
                   
        $('#sex').selectpicker('val', 'male');
        $('.selectpicker').selectpicker('refresh')
        $('#Pan').val('');
        $('#Aadhaarno').val('');
        $('#account_number').val('');
        $('#name_account').val('');
        $('#issue_bank').val('');
                  
        $('#marital_status').selectpicker('val', 'single');
        $('.selectpicker').selectpicker('refresh');
       
        $('#active').selectpicker('val', '1');
        $('.selectpicker').selectpicker('refresh');
                   
        $('#app_access').selectpicker('val', 'No');
        $('.selectpicker').selectpicker('refresh');
        $('#app_password_div').css('display','none');
        $('#app_password').val('');
        $('#date_of_joining').val(today);
        $('#DeviceID').val('');
       
        $('.updateBtn').removeAttr('disabled');
        $('.saveBtn').removeAttr('disabled');
        $('.saveBtn').show();
        $('.updateBtn').hide();
        $('.saveBtn2').show();
        $('.updateBtn2').hide();
    });
     
    // On Blur ItemID Get All Date
        $('#AccountID').blur(function(){ 
            AccountID = $(this).val();
            if(AccountID == ''){
                
            }else{
                $.ajax({
                url:"<?php echo admin_url(); ?>hr_profile/GetAccountDetailByID",
                dataType:"JSON",
                method:"POST",
                data:{AccountID:AccountID},
                beforeSend: function () {
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
                },
                complete: function () {
                $('.searchh2').css('display','none');
                },
                success:function(data){
                    
                    if(data == null){
                        
                    }else if(data.Type == 'othercompstaff'){
                        alert('This AccountID Use for Other Company Staff');
                        $('#AccountID').val('');
                    }else if(data.Type == 'staff'){
                       $('#AccountID').val(data.AccountID);
                       $('#firstname').val(data.firstname);
                       $('#lastname').val(data.lastname);
                       $('#userid').val(data.staffid);
                        
                       $('#email').val(data.email);
                       $('#peremail').val(data.peremail);
                       $('#phonenumber').val(data.phonenumber);
                       $('#altphonenumber').val(data.mobile2);
                       if(data.birthday !== null){
                           var date = data.birthday.substring(0, 10);
                            var date_new = date.split("-").reverse().join("/");
                            $('#birthday').val(date_new);
                       }else{
                           $('#birthday').val('');
                       }
                       var staffid = $('#staffid').val();
                       $('#opening_b').val(data.BAL1);
                        if(staffid !== "3"){
                            $('#opening_b').attr('disabled','disabled');    
                        }
                       $('select[name=state]').val(data.state);
                       $('.selectpicker').selectpicker('refresh');
                       
                       //Center List updated
                        var AllCenters = data.AllCenters;
                        var AssignedCenters = data.AssignedCenterList;
                        $("#CenterID").children().remove();
                        var CentersArray = [];
                        for (var i = 0; i < AssignedCenters.length; i++) {
                            CentersArray.push(AssignedCenters[i]["CenterID"]);
                        }
                        for (var i = 0; i < AllCenters.length; i++) {
                            $("#CenterID").append('<option value="'+AllCenters[i]["CenterID"]+'">'+AllCenters[i]["CenterName"]+'</option>');
                        }
                        $('#CenterID').selectpicker('val', CentersArray);
                        $('.selectpicker').selectpicker('refresh');
                        
                        // Item List append and selected
                        var AllItems = data.AllItems;
                        var AssignedItems = data.AssignedItemList;
                        $("#ItemID").children().remove();
                        var ItemsArray = [];
                        for (var j = 0; j < AssignedItems.length; j++) {
                            ItemsArray.push(AssignedItems[j]["ItemID"]);
                        }
                        for (var j = 0; j < AllItems.length; j++) {
                            $("#ItemID").append('<option value="'+AllItems[j]["ItemID"]+'">'+AllItems[j]["ItemName"]+'</option>');
                        }
                        $('#ItemID').selectpicker('val', ItemsArray);
                        $('.selectpicker').selectpicker('refresh');
                        
                       let CityList = data.CityList;
                        $("#city").children().remove();
                        for (var i = 0; i < CityList.length; i++) {
                            $("#city").append('<option value="'+CityList[i]["id"]+'">'+CityList[i]["city_name"]+'</option>');
                        }
                        $('.selectpicker').selectpicker('refresh');
                        
                        $('#city').selectpicker('val', data.city);
                        $('.selectpicker').selectpicker('refresh');
                        
                       $('#zip').val(data.pincode);
                       $('#current_address').val(data.current_address);
                       $('#home_town').val(data.home_town);
                      
                        let Designation = data.DesignationList;
                        $("#job_position").children().remove();
                        for (var i = 0; i < Designation.length; i++) {
                            $("#job_position").append('<option value="'+Designation[i]["position_id"]+'">'+Designation[i]["position_name"]+'</option>');
						}
                        $('.selectpicker').selectpicker('refresh');
            			$('#job_position').selectpicker('val', data.job_position);
            			$('.selectpicker').selectpicker('refresh');
                       
                       $('#ifsc').val(data.ifsc);
                        $('#job_department').selectpicker('val', data.job_department);
    				   $('.selectpicker').selectpicker('refresh');
                        
                        $('#center').selectpicker('val', data.CenterID);
                        $('.selectpicker').selectpicker('refresh');
                   
                        
                       $('#team_manage').selectpicker('val', data.team_manage);
                       $('.selectpicker').selectpicker('refresh');
                       
                       $('#sex').selectpicker('val', data.sex);
                       $('.selectpicker').selectpicker('refresh')
                       $('#Pan').val(data.pan_number);
                       $('#Aadhaarno').val(data.aadhar_number);
                       $('#account_number').val(data.account_number);
                       $('#name_account').val(data.name_account);
                       $('#issue_bank').val(data.issue_bank);
                       
                       $('#literacy').selectpicker('val', data.literacy);
                       $('.selectpicker').selectpicker('refresh');
                       
                       $('#marital_status').selectpicker('val', data.marital_status);
                       $('.selectpicker').selectpicker('refresh');
                       
                      $('#active').selectpicker('val', data.active);
                       $('.selectpicker').selectpicker('refresh');
                       
                       $('#app_access').selectpicker('val', data.app_access);
                       $('.selectpicker').selectpicker('refresh');
                        if(data.app_access == "Yes"){
                           $('#app_password_div').css('display','block');
                           $('#app_password').val('');
                        }else{
                            $('#app_password_div').css('display','none');
                            $('#app_password').val('');
                        }
                       $('#Movement').selectpicker('val', data.Movement);
                       $('.selectpicker').selectpicker('refresh');
                       if(data.StartDate == '' || data.StartDate == null){
                           $('#date_of_joining').val('');
                       }else{
                           var date = data.StartDate.substring(0, 10);
                            var date_new = date.split("-").reverse().join("/");
                            $('#date_of_joining').val(date_new);
                       }
                       
                       $('#DeviceID').val(data.DiveceID);
                       $('#dep_'+data.departmentid).prop('checked', true);
                       $('.updateBtn').removeAttr('disabled');
                        $('.saveBtn').removeAttr('disabled');
                       $('.saveBtn').hide();
                       $('.updateBtn').show();
                       $('.saveBtn2').hide();
                       $('.updateBtn2').show();
                    }else if(data.Type == 'client'){
                        $('.updateBtn').removeAttr('disabled');
                        $('.saveBtn').removeAttr('disabled');
                        alert("This AccountID Use for other Accounts");
                    } 
                }
            });
        }
    });
        
    
        
    // Save New Item
    $('.saveBtn').on('click',function(){ 
        AccountID = $('#AccountID').val();
        firstname = $('#firstname').val();
        lastname = $('#lastname').val();
        Pan = $('#Pan').val();
        Aadhaarno = $('#Aadhaarno').val();
        phonenumber = $('#phonenumber').val();
        email = $('#email').val();
        peremail = $('#peremail').val();
        birthday = $('#birthday').val();
        date_of_joining = $('#date_of_joining').val();
        CenterID = $('#CenterID').val();
        ItemID = $('#ItemID').val();
        state = $('#state').val();
        city = $('#city').val();
        zip = $('#zip').val();
        account_number = $('#account_number').val();
        name_account = $('#name_account').val();
        issue_bank = $('#issue_bank').val();
        ifsc = $('#ifsc').val();
        active = $('#active').val();
        app_access = $('#app_access').val();
        if(app_access == "Y"){
            app_password = $('#app_password').val();
        }else{
            app_password = null;
        }
        DeviceID = $('#DeviceID').val();
        if(AccountID == ''){
            alert('please enter AccountID');
            $('#AccountID').focus();
            $('.saveBtn').removeAttr('disabled');
        }else if(state == ''){
            alert('please select State');
            $('.saveBtn').removeAttr('disabled');
            $('#state').focus();
        }else if(city == ''){
            alert('please select City');
            $('.saveBtn').removeAttr('disabled');
            $('#city').focus();
        }else if(phonenumber == ''){
            alert('please  enter mobile number');
            $('.saveBtn').removeAttr('disabled');
            $('#phonenumber').focus();
        }else if(!$('#phonenumber').val().match('[0-9]{10}') && $('#phonenumber').val() !== ""){
            alert('Enter valid Mobile number');
            $('.saveBtn').removeAttr('disabled');
            $('#phonenumber').focus();
        }else if(CenterID == ''){
            alert('please select atleast one Center');
            $('#CenterID').focus();
        }else if(ItemID == ''){
            alert('please select atleast one commodity');
            $('#ItemID').focus();
        }else {
            $.ajax({
                url:"<?php echo admin_url(); ?>hr_profile/SaveSubAdmin",
                dataType:"JSON",
                method:"POST",
                data:{AccountID:AccountID,firstname:firstname,lastname:lastname,Pan:Pan,Aadhaarno:Aadhaarno,
                        phonenumber:phonenumber,email:email,peremail:peremail,date_of_joining:date_of_joining,birthday:birthday,
                        CenterID:CenterID,ItemID:ItemID,state:state,city:city,zip:zip,
                        ifsc:ifsc,account_number:account_number,name_account:name_account,issue_bank:issue_bank,
                        app_access:app_access,app_password:app_password,DeviceID:DeviceID,active:active
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
                       alert('Record created successfully...');
                       $('#AccountID').val('');
                        $('#firstname').val('');
                        $('#lastname').val('');
                        
                        $('#email').val('');
                        
                        $('#phonenumber').val('');
                        
                        var today = new Date();
                        var dd = String(today.getDate()).padStart(2, '0');
                        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                        var yyyy = today.getFullYear();
                        today = dd + '/' + mm + '/' + yyyy;
                        $('#birthday').val(today);
                        
                                   
                        $('select[name=state]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $("#city").children().remove();
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('#zip').val('');
                        
                        $("#job_position").children().remove();
                        $('.selectpicker').selectpicker('refresh');
                        
                        $('select[name=job_department]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=CenterID]').val('');
                        $('.selectpicker').selectpicker('refresh');
                        
                        $("#ItemID").children().remove();
                        $('.selectpicker').selectpicker('refresh');
                        
                        $('#ifsc').val('');
                                   
                        $('#sex').selectpicker('val', 'male');
                        $('.selectpicker').selectpicker('refresh')
                        $('#Pan').val('');
                        $('#Aadhaarno').val('');
                        $('#account_number').val('');
                        $('#name_account').val('');
                        $('#issue_bank').val('');
                                  
                        $('#marital_status').selectpicker('val', 'single');
                        $('.selectpicker').selectpicker('refresh');
                       
                        $('#active').selectpicker('val', '1');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('#app_access').selectpicker('val', 'No');
                        $('.selectpicker').selectpicker('refresh');
                        $('#app_password_div').css('display','none');
                        $('#app_password').val('');
                        $('#date_of_joining').val(today);
                        $('#DeviceID').val('');
                       
                        $('.updateBtn').removeAttr('disabled');
                        $('.saveBtn').removeAttr('disabled');
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                   }else{
                        $('.saveBtn').removeAttr('disabled');
                       alert_float('warning', 'Something went wrong...');
                   }
                }
            });   
        }
            
    });
    // Update Exiting Item
    $('.updateBtn').on('click',function(){ 
        userID = $('#userid').val();
        AccountID = $('#AccountID').val();
        firstname = $('#firstname').val();
        lastname = $('#lastname').val();
        opening_b = $('#opening_b').val();
        phonenumber = $('#phonenumber').val();
        altphonenumber = $('#altphonenumber').val();
        email = $('#email').val();
        peremail = $('#peremail').val();
        state = $('#state').val();
        city = $('#city').val();
        current_address = $('#current_address').val();
        home_town = $('#home_town').val();
        zip = $('#zip').val();
        sex = $('#sex').val();
        Pan = $('#Pan').val();
        Aadhaarno = $('#Aadhaarno').val();
        team_manage = $('#team_manage').val();
        job_department = $('#job_department').val();
        job_position = $('#job_position').val();
        account_number = $('#account_number').val();
        name_account = $('#name_account').val();
        issue_bank = $('#issue_bank').val();
        headqurter = $('#headqurter').val();
        birthday = $('#birthday').val();
        literacy = $('#literacy').val();
        marital_status = $('#marital_status').val();
        active = $('#active').val();
        ifsc = $('#ifsc').val();
        CenterID = $('#CenterID').val();
        ItemID = $('#ItemID').val();
        app_access = $('#app_access').val();
        date_of_joining = $('#date_of_joining').val();
        Movement = $('#Movement').val();
        DeviceID = $('#DeviceID').val();
        password = $('#app_password').val();
            
        if(AccountID == ''){
            alert('please enter AccountID');
            $('.updateBtn').removeAttr('disabled');
            $('#AccountID').focus();
        }else if(state == ''){
            alert('please select State');
            $('.updateBtn').removeAttr('disabled');
            $('#state').focus();
        }else if(city == ''){
            alert('please select City');
            $('.updateBtn').removeAttr('disabled');
            $('#city').focus();
        }else if(phonenumber == ''){
            alert('please  enter mobile number');
            $('.updateBtn').removeAttr('disabled');
            $('#phonenumber').focus();
        }else if(!$('#phonenumber').val().match('[0-9]{10}') && $('#phonenumber').val() !== ""){
            alert('Enter valid Mobile number');
            $('.updateBtn').removeAttr('disabled');
            $('#phonenumber').focus();
        }else if(!$('#Pan').val().match('[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}') && $('#Pan').val() !== ""){
            alert('Enter valid PAN number');
            $('.updateBtn').removeAttr('disabled');
            $('#Pan').focus();
        }else if(!$('#Aadhaarno').val().match('[0-9]{12}') && $('#Aadhaarno').val() !== ""){
            alert('Enter valid Aadhar number');
            $('.updateBtn').removeAttr('disabled');
            $('#Aadhaarno').focus();
        }else if(job_position == ''){
            alert('please select Position');
            $('.updateBtn').removeAttr('disabled');
            $('#job_position').focus();
        }else if(CenterID == ''){
            alert('please select atleast one Center');
            $('#center').focus();
        }else if(ItemID == ''){
            alert('please select atleast one Item');
            $('#center').focus();
        }else {
            $.ajax({
                url:"<?php echo admin_url(); ?>hr_profile/UpdateAccountID",
                dataType:"JSON",
                method:"POST",
                data:{userID:userID,AccountID:AccountID,firstname:firstname,lastname:lastname,phonenumber:phonenumber,
                    altphonenumber:altphonenumber,email:email,opening_b:opening_b,peremail:peremail,state:state,city:city,current_address:current_address,home_town:home_town,
                    zip:zip,sex:sex,Pan:Pan,Aadhaarno:Aadhaarno,team_manage:team_manage,job_position:job_position,CenterID:CenterID,ItemID:ItemID,ifsc:ifsc,DeviceID:DeviceID,
                    account_number:account_number,name_account:name_account,issue_bank:issue_bank,headqurter:headqurter,birthday:birthday,literacy:literacy,marital_status:marital_status,active:active,app_access:app_access,date_of_joining:date_of_joining,
                    Movement:Movement,job_department:job_department,password:password
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
                       alert('Record updated successfully...');
                        $('#AccountID').val('');
                        $('#firstname').val('');
                        $('#lastname').val('');
                        $('#email').val('');
                        $('#phonenumber').val('');
                        
                        var today = new Date();
                        var dd = String(today.getDate()).padStart(2, '0');
                        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                        var yyyy = today.getFullYear();
                        today = dd + '/' + mm + '/' + yyyy;
                        $('#birthday').val(today);
                        
                                   
                        $('select[name=state]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $("#city").children().remove();
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('#zip').val('');
                        
                        $("#job_position").children().remove();
                        $('.selectpicker').selectpicker('refresh');
                        
                        $('select[name=job_department]').val('');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('select[name=CenterID]').val('');
                        $('.selectpicker').selectpicker('refresh');
                        
                        $("#ItemID").children().remove();
                        $('.selectpicker').selectpicker('refresh');
                        
                        $('#ifsc').val('');
                                   
                        $('#sex').selectpicker('val', 'male');
                        $('.selectpicker').selectpicker('refresh')
                        $('#Pan').val('');
                        $('#Aadhaarno').val('');
                        $('#account_number').val('');
                        $('#name_account').val('');
                        $('#issue_bank').val('');
                                  
                        $('#marital_status').selectpicker('val', 'single');
                        $('.selectpicker').selectpicker('refresh');
                       
                        $('#active').selectpicker('val', '1');
                        $('.selectpicker').selectpicker('refresh');
                                   
                        $('#app_access').selectpicker('val', 'No');
                        $('.selectpicker').selectpicker('refresh');
                        $('#app_password_div').css('display','none');
                        $('#app_password').val('');
                        $('#date_of_joining').val(today);
                        $('#DeviceID').val('');
                       
                        $('.updateBtn').removeAttr('disabled');
                        $('.saveBtn').removeAttr('disabled');
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                   }else{
                       alert('warning', 'there is no changes');
                       $('.updateBtn').removeAttr('disabled');
                   }
                }
            });
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
                for (var i = 0; i < data.length; i++) {
                    $("#city").append(new Option(data[i].city_name, data[i].id));
                }
                $('.selectpicker').selectpicker('refresh');
            }
        });
    });
	

			
	var app_access = $("#app_access").val();
	$('#app_access').on('change', function() {
	    var app_access = $(this).val();
		if(app_access == "Yes"){
		    $('#app_password_div').css('display','block');
		    $('#app_password').val('');
		}else{
		    $('#app_password_div').css('display','none');
		    $('#app_password').val('');
		}
	});
	
</script>

<script>
    function myFunction2() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table_Account_List");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            td1 = tr[i].getElementsByTagName("td")[1];
            td2 = tr[i].getElementsByTagName("td")[2];
            td3 = tr[i].getElementsByTagName("td")[3];
            td4 = tr[i].getElementsByTagName("td")[4];
            td5 = tr[i].getElementsByTagName("td")[5];
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
            }else if(td5){
                txtValue = td5.textContent || td5.innerText;
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
    }
 </script>
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

<script type="text/javascript">
   $('#MaxCrdAmt,#kms,.opening_bal').on('keypress',function (event) {
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
    var input = $(this).val();
    if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
        event.preventDefault();
    }
});
</script>
<style>

.btn-bottom-toolbar{
    width: 83% !important;
}

#AccountID {
    text-transform: uppercase;
}
#Pan {
    text-transform: uppercase;
}
#vat {
    text-transform: uppercase;
}
#table_Account_List td:hover {
    cursor: pointer;
}
#table_Account_List tr:hover {
    background-color: #ccc;
}

.itemdivisioncomp .btn-default {
    height: 25px !important;
    padding: 0px 10px !important;
    font-size: 12px !important;
}

    .table-Account_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-Account_List thead th { position: sticky; top: 0; z-index: 1; }
    .table-Account_List tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>