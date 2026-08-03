<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">

	<div class="content">

		<div class="row">



			<div class="col-md-9">

				<div class="panel_s">

					<div class="panel-body">

						<nav aria-label="breadcrumb">

							<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">

								<li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>

								<li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>

								<li class="breadcrumb-item active" aria-current="page"><b>Farmer Master</b></li>

							</ol>

						</nav>

						<hr class="hr_style">

						<div class="row">

							<div class="col-md-12">

								<div class="searchh2" style="display:none;">Please wait while fetching data.</div>

								<div class="searchh5" style="display:none;">Please wait while sending otp.</div>

								<div class="searchh3" style="display:none;">Please wait while creating new record.</div>

								<div class="searchh4" style="display:none;">Please wait while updating data.</div>

							</div>

							<br>

							<div class="col-md-3">

								<?php ?>

								<div class="form-group" app-field-wrapper="AccountID">

									<small class="req text-danger">* </small>

									<label for="AccountID" class="control-label">AccountID</label>

									<input type="text" id="AccountID" name="AccountID" class="form-control" value="">

									<?php $staff_user_id = $this->session->userdata('staff_user_id'); ?>

									<input type="hidden" name="staffid" value="<?php echo $staff_user_id; ?>" id="staffid">

									<input type="hidden" name="PlantID" value="<?php echo $this->session->userdata('root_company'); ?>" id="PlantID">

								</div>

							</div>



							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="firstname">
									<small class="req text-danger">* </small>
									<label for="firstname" class="control-label">First Name</label>
									<input type="text" id="firstname" name="firstname" class="form-control" >
								</div>
								<?php // echo render_input('firstname', 'First Name', '', 'text'); ?>

							</div>

							<div class="col-md-3">

								<?php echo render_input('middlename', 'Middle Name', '', 'text'); ?>

							</div>



							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="lastname">
									<small class="req text-danger">* </small>
									<label for="lastname" class="control-label">Last Name</label>
									<input type="text" id="lastname" name="lastname" class="form-control" >
								</div>
								<?php // echo render_input('lastname', 'Last Name', '', 'text'); ?>

							</div>



							<div class="clearfix"></div>



							<div class="col-md-3">

								<div class="form-group" app-field-wrapper="phonenumber">

									<small class="req text-danger">* </small>

									<label for="phonenumber" class="control-label">Mobile Number</label>

									<input type="text" id="phonenumber" name="phonenumber" class="form-control" value="" maxlength="10" minlength="10" onkeypress="return isNumber(event)">

								</div>

							</div>



							<div class="col-md-3">

								<div class="form-group" app-field-wrapper="gender">

									<label for="gender" class="form-label">Gender</label>

									<select name="gender" id="gender" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">

										<option value=""></option>

										<option value="M">Male</option>

										<option value="F">Female</option>

									</select>

								</div>

							</div>



							<div class="col-md-3">

								<div class="form-group">

									<small class="req text-danger">* </small>

									<label for="state" class="control-label">State</label>

									<select class="selectpicker display-block" data-width="100%" id="state" name="state" data-live-search="true" title="None Selected">



										<?php foreach ($state as $st) { ?>

											<option value="<?php echo $st['short_name']; ?>"><?php echo $st['state_name']; ?></option>

										<?php } ?>

									</select>

								</div>

							</div>



							<div class="col-md-3">

								<div class="form-group">

									<small class="req text-danger">* </small>

									<label for="city" class="control-label">City</label>

									<select class="selectpicker display-block" data-width="100%" id="city" name="city" data-live-search="true" title="None Selected">

										<option value=""></option>

									</select>

								</div>

							</div>



							<div class="clearfix"></div>



							<div class="col-md-3">

								<div class="form-group">

									<small class="req text-danger">* </small>

									<label for="subdist" class="control-label">Taluka</label>

									<select class="selectpicker display-block" data-width="100%" id="subdist" name="subdist" data-live-search="true" title="None Selected">

										<option value=""></option>

									</select>

								</div>

							</div>



							<div class="col-md-3">

								<div class="form-group">

									<label for="po">

										<small class="req text-danger">* </small>Post Office

									</label>

									<input type="text" id="po" name="po" class="form-control" />

								</div>

							</div>



							<!--<div class="col-md-3">

                        <?php //echo render_input('po', 'Post Office'); 
												?>

                    </div>-->



							<div class="col-md-3">

								<div class="form-group">

									<label for="po">

										<small class="req text-danger">* </small>Locality

									</label>

									<input type="text" id="loc" name="loc" class="form-control" />

								</div>

							</div>



							<!--<div class="col-md-3">

                        <?php //echo render_input('loc', 'Locality'); 
												?>

                    </div>-->



							<!--<div class="col-md-3">

                        <?php //echo render_input('street', 'Street'); 
												?>

                    </div>-->



							<div class="col-md-3">

								<div class="form-group">

									<label for="po">

										<small class="req text-danger">* </small>Street

									</label>

									<input type="text" id="street" name="street" class="form-control" />

								</div>

							</div>



							<div class="clearfix"></div>



							<!--<div class="col-md-3">

                        <?php //echo render_input('house', 'House'); 
												?>

                    </div>-->



							<div class="col-md-3">

								<div class="form-group">

									<label for="po">

										<small class="req text-danger">* </small>House

									</label>

									<input type="text" id="house" name="house" class="form-control" />

								</div>

							</div>



							<div class="col-md-3">

								<div class="form-group" app-field-wrapper="zip">

									<small class="req text-danger">* </small>

									<label for="zip" class="control-label">Pin Code</label>

									<input type="text" name="zip" id="zip" class="form-control" onchange="validateZipCode" value="" maxlength="6" minlength="6" onkeypress="return isNumber(event)">

								</div>

							</div>



							<div class="col-md-3">

								<div class="form-group">

									<small class="req text-danger">* </small>

									<label for="village">Select Village</label>

									<select name="village" id="village" class="selectpicker form-control" data-live-search="true" title="None Selected">

										<option value="new">New Village</option>

									</select>

								</div>

							</div>



							<div class="col-md-3">

								<div class="form-group" id="villageNameGroup" style="display:none;">

									<label for="villagename"><small class="req text-danger">* </small>Village Name</label>

									<input type="text" name="villagename" id="villagename" class="form-control" value="">

								</div>

							</div>



							<div class="col-md-3">

								<div class="form-group" app-field-wrapper="Aadhaarno">

									<small class="req text-danger">* </small>

									<label for="aadhaar" class="control-label">Aadhaar Number</label>&nbsp;&nbsp;&nbsp;

									<!--<a id="verifyAadhar" style="color:#f93500;cursor:pointer;display:none;font-size:14px;font-weight:600;">Verify</a>-->

									<span id="check" style="display:none;color:#1be91b;">

										<i style="font-size:16px;" class="fa fa-check-circle" aria-hidden="true"></i>

									</span>

									<div class="input-group">

										<input type="text" maxlength="12" minlength="12" name="Aadhaarno" pattern="[0-9] {12}" id="Aadhaarno" class="form-control numbersOnly" onkeypress="return isNumber(event)" value="">

										<!--<div class="input-group-append">-->

										<span class="input-group-btn">

											<button id="btn_send_otp" class="btn btn-primary" type="button">

												Verify

											</button>

										</span>

										<!--</div>-->

									</div>

									<span class="aadhar_denger" style="color:red;"></span>

									<input type="hidden" id="isValidaadhar" value="N">

									<input type="hidden" id="aadharClientID" value="">

									<input type="hidden" id="aadharState" value="">

									<input type="hidden" id="aadharDist" value="">

									<input type="hidden" id="aadharSubDist" value="">

									<input type="hidden" id="aadharPO" value="">

									<input type="hidden" id="aadharVtc" value="">

									<input type="hidden" id="aadharLoc" value="">

									<input type="hidden" id="aadharStreet" value="">

									<input type="hidden" id="aadharHouse" value="">

									<input type="hidden" id="aadharPincode" value="">

									<input type="hidden" id="aadharImage" value="">

									<input type="hidden" id="aadharDOB" value="">

									<input type="hidden" id="aadharFullname" value="">

									<input type="hidden" id="aadharGender" value="">

									<input type="hidden" id="otpCheck" value="0">

									<input type="hidden" id="existing_aadhar" value="">

									<input type="hidden" id="aadhaar_verified_date" value="">

								</div>

							</div>

							<div id="aadharOtpDiv" style="display:none;">

								<div class="col-md-3">

									<div class="form-group" app-field-wrapper="aadharOtp">

										<small class="req text-danger">* </small>

										<label for="aadharOtp" class="control-label">Enter OTP</label>&nbsp;&nbsp;&nbsp;

										<div class="input-group">

											<input type="text" maxlength="6" minlength="6" name="aadharOtp" pattern="[0-9] {6}" id="aadharOtp" class="form-control numbersOnly" onkeypress="return isNumber(event)" value="">

											<!--<div class="input-group-append">-->

											<span class="input-group-btn">

												<button id="btn_verify_otp" class="btn btn-primary" type="button">

													Verify

												</button>

											</span>

											<!--</div>-->

										</div>

										<span class="aadhar_denger" style="color:red;"></span>

									</div>

								</div>

							</div>







							<div class="col-md-3">

								<?php $value = ''; ?>

								<?php echo render_date_input('dob', 'DOB', $value, 'text'); ?>

							</div>







							<div class="col-md-3">

								<div class="form-group" app-field-wrapper="active">

									<label for="active" class="form-label">Status</label>

									<select name="active" id="active" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">

										<option value="1">Active</option>

										<option value="0">InActive</option>

									</select>

								</div>

							</div>



							<div class="col-md-3">

								<div class="form-group" app-field-wrapper="region">

									<label for="region" class="form-label">Region</label>

									<select name="region" id="region" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">

										<option value=""></option>

										<?php

										foreach ($GetRegion as $key => $value) {

										?>

											<option value="<?php echo $value['id']; ?>"><?php echo $value['region']; ?></option>

										<?php } ?>



									</select>

								</div>

							</div>



							<div class="col-md-3">

								<div class="form-group" app-field-wrapper="is_approve">

									<label for="is_approve" class="form-label">Is Approve</label>

									<select name="is_approve" id="is_approve" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">

										<option value="0">No</option>

										<option value="1">Yes</option>

									</select>

								</div>

							</div>

							<div class="clearfix"></div>

							<div class="col-md-3">

								<div class="form-group">

									<label for="ShortCode"><small class="req text-danger">* </small>Short Code</label>

									<input type="text" name="ShortCode" id="ShortCode" readonly class="form-control" value="">

								</div>

							</div>

							<div class="col-md-3" id="manually_aadhar_check" style="display:none;">

								<div class="form-group" app-field-wrapper="man_aa_ver">

									<input type="checkbox" id="man_aa_ver" name="man_aa_ver">

									<label for="man_aa_ver"> Manually veryfied Aadhaar</label>

									<br>

								</div>

							</div>



							<div id="aadhaar_front_back" style="display:none;">

								<div class="col-md-3">

									<div class="form-group" app-field-wrapper="Aadhaar_front">

										<label for="Aadhaar_front" class="control-label">Aadhaar front</label>

										<input type="file" name="Aadhaar_front" id="Aadhaar_front" class="form-control">

									</div>

								</div>

								<div class="col-md-3">

									<div class="form-group" app-field-wrapper="Aadhaar_back">

										<label for="Aadhaar_back" class="control-label">Aadhaar back</label>

										<input type="file" name="Aadhaar_back" id="Aadhaar_back" class="form-control">

									</div>

								</div>

							</div>





							<div class="clearfix"></div>

							<br>





							<div class="container-fluid">

								<label for="active" class="form-label">Bank Details</label>

								<table class="table table-striped table-bordered">

									<thead>

										<tr>

											<th>Primary Acc.</th>

											<th>Bank Name</th>

											<th>Branch Name</th>

											<th>Account Name</th>

											<th>Account No.</th>

											<th>IFSC</th>

											<!--<th>Status</th>-->

											<th>Cheque/Passbook Img</th>

										</tr>

									</thead>

									<tbody id="BankTableBody"></tbody>

								</table>

							</div>

							<div class="container-fluid">

								<label for="active" class="form-label">Farm Details</label>

								<table class="table table-striped table-bordered">

									<thead>

										<tr>

											<th>Survey No</th>

											<th>Latitude</th>

											<th>Longitude</th>

											<th>Land Area</th>

											<th>State</th>

											<th>District</th>

											<th>Taluka</th>

											<th>Village</th>

											<th>Pincode</th>

											<th>Address</th>

											<th>7 / 12 </th>

										</tr>

									</thead>

									<tbody id="FarmTableBody"></tbody>

								</table>

							</div>



							<div class="container-fluid col-md-8">

								<label for="active" class="form-label">Crop Details</label>

								<table class="table table-striped table-bordered">

									<thead>

										<tr>

											<th>Farm Name</th>

											<th>CropID</th>

											<th>Crop Name</th>

											<th>Crop Area</th>

											<th>Season</th>

										</tr>

									</thead>

									<tbody id="CropTableBody"></tbody>

								</table>

							</div>



							<div class="row">

								<div class="col-md-12">

									<?php if (has_permission('FarmarMaster', '', 'create')) {

									?>

										<button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>

									<?php

									} else {

									?>

										<button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>

									<?php

									}

									?>



									<?php if (has_permission('FarmarMaster', '', 'edit')) {

									?>

										<button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>

										<button type="button" class="btn btn-info updateBtn2" style="margin-right: 25px;" onclick="openBankModal()">Upload Bank Details</button>

									<?php

									} else {

									?>

										<button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>

									<?php

									}

									?>

									<button type="button" class="btn btn-default cancelBtn">Cancel</button>

								</div>

							</div>



							<div class="clearfix"></div>

							<!-- Iteme List Model-->

							<div class="modal fade" id="bankModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">

								<div class="modal-dialog" role="document">

									<div class="modal-content">

										<div class="modal-header">

											<h5 class="modal-title" id="exampleModalLongTitle">Upload Bank Details</h5>

											<button type="button" class="close" data-dismiss="modal" aria-label="Close">

												<span aria-hidden="true">&times;</span>

											</button>

										</div>

										<div class="modal-body">

											<div class="row">

												<div class="col-md-12">

													<div class="searchh6" style="display:none;">Please wait while fetching data.</div>

													<div class="searchh7" style="display:none;">Please wait while adding data.</div>

												</div>

												<div class="col-md-12">

													<div class="form-group" app-field-wrapper="zip">

														<small class="req text-danger">* </small>

														<label for="zip" class="control-label">Account of</label>

														<select class="selectpicker display-block" data-width="100%" id="accountFor" name="accountFor" data-none-selected-text="<?php echo 'Select Account For'; ?>" data-live-search="true">

															<option value=""></option>

															<option value="Self">Self</option>

															<option value="Brother">Brother</option>

															<option value="Son">Son</option>

															<option value="Spouse">Spouse</option>

														</select>

													</div>

												</div>

												<div class="col-md-12">

													<div class="form-group" app-field-wrapper="zip">

														<small class="req text-danger">* </small>

														<label for="zip" class="control-label">IFSC Code</label>

														<input type="text" name="ifsc_code" id="ifsc_code" class="form-control" value="">

													</div>

												</div>

												<div class="col-md-12">

													<div class="form-group" app-field-wrapper="zip">

														<label for="zip" class="control-label">Bank Name</label>

														<input type="text" name="bank_name" id="bank_name" readonly class="form-control" value="">

													</div>

												</div>

												<div class="col-md-12">

													<div class="form-group" app-field-wrapper="zip">

														<label for="zip" class="control-label">Branch Name</label>

														<input type="text" name="bank_branch" id="bank_branch" readonly class="form-control" value="">

													</div>

												</div>

												<div class="col-md-12">

													<div class="form-group" app-field-wrapper="zip">

														<small class="req text-danger">* </small>

														<label for="zip" class="control-label">Account Number</label>

														<input type="text" name="account_number" id="account_number" oncopy="return false" onpaste="return false" oncut="return false" onkeyup=" validateAccountNumber()" class="form-control" value="">

													</div>

												</div>

												<div class="col-md-12">

													<div class="form-group" app-field-wrapper="zip">

														<small class="req text-danger">* </small>

														<label for="zip" class="control-label">Confirm Account Number</label>

														<input type="text" name="reaccount_number" id="reaccount_number" oncopy="return false" onpaste="return false" oncut="return false" onkeyup="validateAccountNumber()" class="form-control" value="">

														<span id="account_number_error" style="color:red;"></span>

													</div>

												</div>

												<div class="col-md-12">

													<div class="form-group" app-field-wrapper="account_name">

														<label for="account_name" class="control-label">Account Name</label>

														<input type="text" name="account_name" id="account_name" class="form-control" value="">

													</div>

												</div>

												<div class="col-md-12">

													<div class="form-group" app-field-wrapper="cheque_image">

														<label for="cheque_image" class="control-label">Cheque Image</label>

														<input type="file" name="cheque_image" id="cheque_image" class="form-control" value="">

													</div>

												</div>

												<div class="col-md-4">

													<input type="checkbox" name="is_primarybank" id="is_primarybank" value="Y">

													<b for="is_primarybank">Is Primary</b>

												</div>

												<div class="col-md-8">

													<input type="checkbox" name="is_act_validate" id="is_act_validate" value="Y">

													<b for="is_act_validate">Manual Account Validate</b>

												</div>

											</div>

										</div>

										<div class="modal-footer">

											<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

											<button type="button" id="bankSubmit" onclick="addBankDetails()" class="btn btn-primary">Save changes</button>

										</div>

									</div>

								</div>

							</div>



							<div class="modal fade Account_List" id="Account_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">

								<div class="modal-dialog modal-lg" role="document">

									<div class="modal-content">

										<div class="modal-header" style="padding:5px 10px;">

											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

											<h4 class="modal-title">Farmer List</h4>

										</div>





										<div class="modal-body" style="padding:0px 5px !important">

											<!--<div class="col-md-5">

                                <a class="btn btn-default buttons-excel buttons-html2" tabindex="0" aria-controls="table-trial_bal_report" href="#" id="caexcel"

                                                style="float: left ! important;"><span>Export to Excel</span></a>

                                <button class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</button>

                            </div>-->

											<div class="table-Account_List tableFixHead2">

												<table class="tree table table-striped table-bordered table-Account_List tableFixHead2" style="overflow-y:scrollable;" id="Farmer_List" width="100%">

													<thead>

														<tr style="display:none;">

															<td colspan="9">
																<h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5>
															</td>

														</tr>

														<tr>

															<th style="text-align:center;">Sr. No.</th>

															<!--<th style="text-align:left;">AccountID</th>-->

															<th style="text-align:left;">Farmer Name</th>

															<th style="text-align:left;">Mobile No</th>

															<th style="text-align:left;">Aadhaar No.</th>

															<th style="text-align:left;">State</th>

															<th style="text-align:left;">City</th>

														</tr>

													</thead>

													<tbody id="ListTableBody">



													</tbody>

												</table>

											</div>

										</div>

										<div class="modal-footer" style="padding:0px;">

											<input type="text" id="myInput1" onkeyup="mysearch()" placeholder="Search for names.." title="Type in a name" style="float: left;width: 100%;">

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



			<div class="col-md-3">

				<div class="panel_s">

					<div class="panel-body">

						<div class="row">

							<div class="col-md-12">

								<div id="aadhar_label" class="row" style="width:100%;margin:auto; font-weight:600;font-size: 15px;">



								</div>

								<div id="aadhar_address" class="row" style="width:100%;margin:auto;">



								</div>

								<div id="aadhar_state" class="row" style="width:100%;margin:auto;">



								</div>

								<div id="aadhar_city" class="row" style="width:100%;margin:auto;">



								</div>

							</div>

							<div class="col-md-12">

								<div id="aadhar_pics" class="row" style="width:100%;margin:auto;">



								</div>

							</div>

							<div class="col-md-12">

								<div id="aadhar_profile" class="row" style="width:100%;margin:auto;">

								</div>

							</div>



							<br>

						</div>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>

<?php init_tail(); ?>

<script>
	$('#man_aa_ver').change(function() {

		if ($(this).is(':checked')) {

			$('#aadhaar_front_back').css('display', 'block');

			$('.saveBtn').prop('disabled', false);

		} else {

			$('#aadhaar_front_back').css('display', 'none');

			$('.saveBtn').prop('disabled', true);

		}

	});

	$('#verifyAadhar').click(function() {

		var AccountID = $('#AccountID').val();

		var Aadhaarno = $('#Aadhaarno').val();

		$.ajax({

			url: "<?php echo admin_url(); ?>clients/VerifyAadhar",

			method: "POST",

			dataType: "JSON",

			data: {

				AccountID: AccountID,

				Aadhaarno: Aadhaarno,

			},

			success: function(data) {

				$('#verifyAadhar').css('display', 'none');

				alert('Aadhar Verified Successfully!');

				$('#check').css('display', '');

			}

		});

	});



	// $('#Aadhaarno').blur(function(){

	// $('#btn_send_otp').click(function(){

	// var Aadhaarno = $('#Aadhaarno').val();

	// var AccountID = $('#AccountID').val();

	// if(AccountID == '' || AccountID == null ){

	// alert('Please enter account ID');

	// }else if(Aadhaarno.length != 12){

	// alert('Enter valid Aadhaar Number');

	// }else{

	// $.ajax({

	// url:"<?php echo admin_url(); ?>clients/sendAadhaarOtp",

	// method:"POST",

	// dataType:"JSON",

	// data:{

	// Aadhaarno:Aadhaarno,

	// AccountID:AccountID

	// },

	// beforeSend: function () {

	// $('.searchh5').css('display','block');

	// $('.searchh5').css('color','blue');

	// },

	// complete: function () {

	// $('.searchh5').css('display','none');

	// },

	// success:function(data){

	// if(data == false){

	// alert('Aadhar number already exists');

	// $('.saveBtn').prop('disabled', true);

	// $('#Aadhaarno').val('');

	// }else if(data.success == false || data.success == null){

	// $('#aadharOtpDiv').css('display','block');

	// $('#manually_aadhar_check').css('display','block');

	// $('#aadharClientID').val('');

	// }else{

	// alert('OTP sent on phone number successfully!');

	// $('#aadharOtpDiv').css('display','block');

	// $('#manually_aadhar_check').css('display','block');



	// $('#aadharClientID').val(data.data.client_id);

	// $('.saveBtn').prop('disabled', true);

	// }

	// }

	// });    

	// }    

	// }); 



	$('#btn_send_otp').click(function() {

		var Aadhaarno = $('#Aadhaarno').val();

		var AccountID = $('#AccountID').val();

		if (AccountID == '' || AccountID == null) {

			alert('Please enter account ID');

		} else if (Aadhaarno.length != 12) {

			alert('Enter valid Aadhaar Number');

		} else {

			$.ajax({

				url: "<?php echo admin_url(); ?>clients/sendAadhaarOtp",

				method: "POST",

				dataType: "JSON",

				data: {

					Aadhaarno: Aadhaarno,

					AccountID: AccountID

				},

				beforeSend: function() {

					$('.searchh5').css('display', 'block');

					$('.searchh5').css('color', 'blue');

				},

				complete: function() {

					$('.searchh5').css('display', 'none');

				},

				success: function(data) {
					const STATE_MAP = {
						"ANDAMAN AND NICOBAR ISLANDS": "ANI",
						"ANDHRA PRADESH": "AP",
						"ARUNACHAL PRADESH": "ARP",
						"ASSAM": "AS",
						"BIHAR": "B",
						"CHANDIGARH": "CH",
						"CHHATTISGARH": "C",
						"DADRA AND NAGAR HAVELI": "DNH",
						"DAMAN AND DIU": "DD",
						"DELHI": "DEL",
						"GOA": "G",
						"GUJARAT": "GUJ",
						"HARYANA": "HAR",
						"HIMACHAL PRADESH": "HP",
						"JAMMU AND KASHMIR": "JK",
						"JHARKHAND": "JH",
						"KARNATAKA": "KR",
						"KERALA": "KE",
						"LADAKH": "LA",
						"LAKSHADWEEP": "LD",
						"MADHYA PRADESH": "MP",
						"MAHARASHTRA": "MH",
						"MANIPUR": "MPUR",
						"MEGHALAYA": "ML",
						"MIZORAM": "MIZ",
						"NAGALAND": "N",
						"ODISHA": "OR",
						"PUDUCHERRY": "PC",
						"PUNJAB": "PU",
						"RAJASTHAN": "RAJ",
						"SIKKIM": "S",
						"TAMIL NADU": "TN",
						"TELANGANA": "TG",
						"TRIPURA": "TR",
						"UTTAR PRADESH": "UP",
						"UTTARAKHAND": "UK",
						"WEST BENGAL": "WB"
					};

					if (data == false) {

						alert('Aadhar number already exists');

						$('#isValidaadhar').val('N');

						$('.saveBtn').prop('disabled', true);

						$('#Aadhaarno').val('');

					} else if (data.success == false || data.success == null) {

						alert('Enter valid Aadhaar card number');

						$('.saveBtn').prop('disabled', true);

						$('#aadharOtpDiv').css('display', 'none');

						$('#isValidaadhar').val('N');

						// $('#manually_aadhar_check').css('display','block');

						// $('#aadharClientID').val('');

					} else {

						alert('Aadhar Verified successfully!');

						// $('#aadharOtpDiv').css('display','block');

						$('#manually_aadhar_check').css('display', 'block');



						$('#aadharClientID').val(data.data.client_id);
						$('#gender').val(data.data.gender).selectpicker('refresh');
						const stateCode = STATE_MAP[data.data.state.trim().toUpperCase()] || '';
						$('#state').val(stateCode).selectpicker('refresh').trigger('change');

						$('#isValidaadhar').val('Y');

						$('.saveBtn').prop('disabled', false);

					}

				}

			});

		}

	});



	// $('#aadharOtp').blur(function(){

	$('#btn_verify_otp').click(function() {

		var aadharClientID = $('#aadharClientID').val();

		var aadharOtp = $('#aadharOtp').val();

		if (!aadharClientID) {

			alert('Enter valid Aadhaar Number and hit "Send OTP"');

		} else if (!aadharOtp) {

			alert('Enter OTP');

		} else {

			$.ajax({

				url: "<?php echo admin_url(); ?>clients/verifyAadhaarOtp",

				method: "POST",

				dataType: "JSON",

				data: {

					aadharClientID: aadharClientID,

					aadharOtp: aadharOtp

				},

				success: function(data) {

					if (data.success == false || data.success == null) {

						alert('Enter valid OTP');

						$('.saveBtn').prop('disabled', true);

						$('#otpCheck').val('0');

					} else {

						let adress_data = data.data.address;

						let other_data = data.data;

						alert('OTP verified successfully!');

						$('#man_aa_ver').prop('checked', false);

						$('#check').css('display', 'contents');

						$('#aadharOtpDiv').css('display', 'none');

						$('#aadharClientID').html();

						$('.saveBtn').prop('disabled', false);

						$('#aadharState').val(adress_data.state);

						$('#aadharDist').val(adress_data.dist);

						$('#aadharSubDist').val(adress_data.subdist);

						$('#aadharPO').val(adress_data.po);

						$('#aadharVtc').val(adress_data.vtc);

						$('#aadharLoc').val(adress_data.loc);

						$('#aadharStreet').val(adress_data.street);

						$('#aadharHouse').val(adress_data.house);

						$('#aadharPincode').val(other_data.zip);

						$('#aadharImage').val(data.profile_image);

						$('#aadharFullname').val(other_data.full_name);

						$('#aadharGender').val(other_data.gender);

						$('#aadharDOB').val(other_data.dob);

						$('#otpCheck').val('1');

					}

				}

			});

		}

	});
</script>



<script>
	$('#zip').blur(function() {

		var zip = $('#zip').val();

		$.ajax({

			url: "<?php echo admin_url(); ?>clients/GetVillageList",

			method: "POST",

			dataType: "json",

			data: {
				zip: zip
			},

			beforeSend: function() {

				$('.searchh6').css('display', 'block');

				$('.searchh6').css('color', 'blue');

			},

			complete: function() {

				$('.searchh6').css('display', 'none');

			},

			success: function(data) {

				const $villageSelect = $('#village');

				$villageSelect.empty();

				if (data && data.length > 0)

				{

					$villageSelect.append(

						$('<option></option>')

						.val('new')

						.text('New Village')

					);

					$.each(data, function(index, village) {

						$villageSelect.append(

							$('<option></option>')

							.val(village.id)

							.text(village.VillageName)

						);

					});

				} else {

					$villageSelect.append(

						$('<option></option>')

						.val('new')

						.text('New Village')

					);



					alert("No villages found for this pincode.");

				}

				$villageSelect.selectpicker('refresh');

			},

			error: function() {

				alert("An error occurred while checking the pincode.");

			}

		});

	});



	$(document).on('change', '#village', function() {

		const selectedVal = $(this).val();

		if (selectedVal === 'new') {

			$('#villageNameGroup').show();

			$('#villagename').attr('required', true);

		} else {

			$('#villageNameGroup').hide();

			$('#villagename').removeAttr('required').val('');

		}

	});
</script>

<script>
	$(document).ready(function() {

		var SessionID = "<?php echo $this->session->userdata('AccountIDSet'); ?>";



		$('.updateBtn').hide();

		$('.updateBtn2').hide();

		$("#AccountID").dblclick(function() {

			$('#Account_List').modal('show');

			$('#Account_List').on('shown.bs.modal', function() {

				$('#myInput1').val('');

				$('#myInput1').focus();

			})

		});

		// AccountID Typing Validation

		$("#AccountID").keypress(function(e) {

			var keyCode = e.keyCode || e.which;

			if (keyCode == "") {

				$("#lblError").html("");

			} else {

				var regex = /^[A-Za-z0-9]+$/;

				var isValid = regex.test(String.fromCharCode(keyCode));

				return isValid;

			}

		});

	});

	function ResetForm()

	{

		$('#verifyAadhar').css('display', 'none');

		$('#check').css('display', 'none');

		$('#firstname').val('');

		$('#middlename').val('');

		$('#lastname').val('');

		$('#phonenumber').val('');

		$('#house').val('');

		$('#street').val('');

		$('#loc').val('');

		$('#po').val('');

		$('#zip').val('');

		$('#Aadhaarno').val('');

		$('#isValidaadhar').val('N');

		$('#dob').val('');

		$('select[name=state]').val('');

		$('.selectpicker').selectpicker('refresh');

		$('select[name=city]').val('');

		$('.selectpicker').selectpicker('refresh');

		$('select[name=subdist]').val('');

		$('.selectpicker').selectpicker('refresh');

		$('select[name=gender]').val('');

		$('.selectpicker').selectpicker('refresh');

		$('select[name=active]').val('1');

		$('.selectpicker').selectpicker('refresh');



		$('select[name=is_approve]').val('0');

		$('.selectpicker').selectpicker('refresh');



		$('select[name=region]').val('');

		$('.selectpicker').selectpicker('refresh');



		$('#aadhar_pics').html('');

		$('#aadhar_profile').html('');



		$('#aadharState').val('');

		$('#aadharDist').val('');

		$('#aadharSubDist').val('');

		$('#aadharPO').val('');

		$('#aadharVtc').val('');

		$('#aadharLoc').val('');

		$('#aadharStreet').val('');

		$('#aadharHouse').val('');

		$('#aadharPincode').val('');

		$('#aadharImage').val('');

		$('#aadharFullname').val('');

		$('#aadharGender').val('');

		$('#aadharDOB').val('');

		$('#otpCheck').val('0');

		$('#man_aa_ver').prop('checked', false);

		$('#verifyAadhar').css('display', 'none');

		$('#aadharOtpDiv').css('display', 'none');

		$('#manually_aadhar_check').css('display', 'none');

		$('#aadhaar_front_back').css('display', 'none');

		$('.saveBtn').show();

		$('.updateBtn').hide();

		$('.saveBtn2').show();

		$('.updateBtn2').hide();

		$("#BankTableBody").html('');

		$("#FarmTableBody").html('');

		$("#CropTableBody").html('');

		$("#aadhar_label").html('');

		$("#aadhar_address").html('');

		$("#aadhar_state").html('');

		$("#aadhar_city").html('');



		$('#Aadhaarno').prop('disabled', false);

		$('#btn_send_otp').css('display', 'block');

	}

	// Empty and open create mode

	$("#AccountID").focus(function() {

		$('#AccountID').val('');

		ResetForm();

	});



	// Cancel selected data

	$(".cancelBtn").click(function() {

		$('#AccountID').val('');

		ResetForm();

	});



	// On Blur ItemID Get All Date

	$('#AccountID').blur(function() {

		AccountID = $(this).val();

		if (AccountID == '') {



		} else {

			$.ajax({

				url: "<?php echo admin_url(); ?>clients/GetAccountDetailByID",

				dataType: "JSON",

				method: "POST",

				data: {
					AccountID: AccountID
				},

				beforeSend: function() {

					$('.searchh2').css('display', 'block');

					$('.searchh2').css('color', 'blue');

				},

				complete: function() {

					$('.searchh2').css('display', 'none');

				},

				success: function(data) {

					if (data == null) {

						ResetForm();

					} else

					{

						if (data.CustomerType == "2") {

							alert("selected Account is Broker");

							$('#AccountID').val("");

							$('#AccountID').focus();

						} else if (data.CustomerType == "3") {

							alert("selected Account is Trader");

							$('#AccountID').val("");

							$('#AccountID').focus();

						} else if (data.CustomerType == "4") {

							alert("selected Account is Corporate/Processor");

							$('#AccountID').val("");

							$('#AccountID').focus();

						} else {

							$('#AccountID').val(data.AccountID);

							$('#ShortCode').val(data.ShortCode);

							$('#firstname').val(data.firstname);

							$('#middlename').val(data.middlename);

							$('#lastname').val(data.lastname);

							$('#phonenumber').val(data.phonenumber);

							$('#house').val(data.house);

							$('#street').val(data.street);

							$('#loc').val(data.loc);

							$('#po').val(data.po);

							$('#zip').val(data.zip);

							$('#Aadhaarno').val(data.aadhaar_number);

							$('#isValidaadhar').val('Y');

							$('#existing_aadhar').val(data.aadhaar_number);

							if (data.aadhaar_verified_date == null) {

								$('#verifyAadhar').css('display', '');

								$('#check').css('display', 'none');

								$('#otpCheck').val('0');

								$('#Aadhaarno').prop('disabled', false);

								$('#btn_send_otp').css('display', 'block');

								$('#manually_aadhar_check').css('display', 'block');

								$('#aadhaar_verified_date').val('');

							} else {

								//  if(data.aadhaar_verified_date != null){

								$('#check').css('display', '');

								$('#verifyAadhar').css('display', 'none');

								$('#otpCheck').val('1');

								$('#Aadhaarno').prop('disabled', true);

								$('#btn_send_otp').css('display', 'none');

								$('#manually_aadhar_check').css('display', 'none');

								$('#aadhaar_verified_date').val(data.aadhaar_verified_date);

							}

							var dob_date = data.dob

							if (data.dob == null) {



							} else {

								var dob = dob_date.split("-").reverse().join("/");

								$('#dob').val(dob);

							}

							$('select[name=state]').val(data.state);

							$('.selectpicker').selectpicker('refresh');



							$('select[name=gender]').val(data.gender);

							$('.selectpicker').selectpicker('refresh');



							if (data.aadhaar_verified_date == null || data.aadhaar_verified_date == "") {

								date_new = "";

							} else {

								var date = data.aadhaar_verified_date.substring(0, 10)

								var date_new = date.split("-").reverse().join("/");

							}



							var Addr = 'Address : ' + data.Aloc + ', ' + data.Apincode;

							$('#aadhar_label').html("Address as per Aadhaar Verification As On " + date_new);

							$('#aadhar_address').html(Addr);

							$('#aadhar_state').html("State : " + data.Astate);

							$('#aadhar_city').html("City : " + data.Adist);



							$('select[name=active]').val(data.active);

							$('.selectpicker').selectpicker('refresh');



							$('select[name=region]').val(data.regionID);

							$('.selectpicker').selectpicker('refresh');



							$('select[name=is_approve]').val(data.is_approve);

							$('.selectpicker').selectpicker('refresh');

							let CityList = data.CityList;

							$("#city").children().remove();

							for (var i = 0; i < CityList.length; i++) {

								$("#city").append('<option value="' + CityList[i]["id"] + '">' + CityList[i]["city_name"] + '</option>');

							}

							$('.selectpicker').selectpicker('refresh');



							$('#city').selectpicker('val', data.dist);

							$('.selectpicker').selectpicker('refresh');



							let TalukaList = data.TalukaList;

							$("#subdist").children().remove();

							for (var i = 0; i < TalukaList.length; i++) {

								$("#subdist").append('<option value="' + TalukaList[i]["id"] + '">' + TalukaList[i]["TalukaName"] + '</option>');

							}

							$('.selectpicker').selectpicker('refresh');



							$('#subdist').selectpicker('val', data.subdist);

							$('.selectpicker').selectpicker('refresh');



							let VillageList = data.VillageList;

							$("#village").children().remove();

							$("#village").append('<option value="new">New Village</option>');

							for (var i = 0; i < VillageList.length; i++) {

								$("#village").append('<option value="' + VillageList[i]["id"] + '">' + VillageList[i]["VillageName"] + '</option>');

							}

							$('.selectpicker').selectpicker('refresh');

							$('#village').selectpicker('val', data.VillageID);

							$('.selectpicker').selectpicker('refresh');



							$.ajax({

								url: "<?php echo admin_url(); ?>clients/GetBankDetailByID",

								method: "POST",

								data: {
									AccountID: data.AccountID
								},

								success: function(data) {

									$("#BankTableBody").html(data);

								}

							});

							$.ajax({

								url: "<?php echo admin_url(); ?>clients/GetFirmDetailByID",

								method: "POST",

								data: {
									AccountID: data.AccountID
								},

								success: function(data) {

									$("#FarmTableBody").html(data);

								}

							});

							$.ajax({

								url: "<?php echo admin_url(); ?>clients/GetCropDetailByID",

								method: "POST",

								data: {
									AccountID: AccountID
								},

								success: function(data) {

									$("#CropTableBody").html(data);

								}

							});

							$.ajax({

								url: "<?php echo admin_url(); ?>clients/GetAadhar",

								method: "POST",

								data: {

									AccountID: AccountID

								},

								success: function(data) {

									$('#aadhar_pics').html(data);

								}

							});



							$.ajax({

								url: "<?php echo admin_url(); ?>clients/GetAadharProfile",

								method: "POST",

								data: {

									AccountID: AccountID

								},

								success: function(data) {

									$('#aadhar_profile').html(data);

								}

							});



							$('.saveBtn').hide();

							$('.updateBtn').show();

							$('.saveBtn2').hide();

							$('.updateBtn2').show();

						}

					}

				}

			});

		}



	});



	// Save New Item

	$('.saveBtn').on('click', function()
		{
			var CustomerType = 1;
			var AccountID = $('#AccountID').val();
			var firstname = $('#firstname').val();
			var middlename = $('#middlename').val();
			var lastname = $('#lastname').val();
			var phonenumber = $('#phonenumber').val();
			var state = $('#state').val();
			var city = $('#city').val();
			var house = $('#house').val();
			var street = $('#street').val();
			var loc = $('#loc').val();
			var po = $('#po').val();
			var subdist = $('#subdist').val();
			var zip = $('#zip').val();
			var Aadhaarno = $('#Aadhaarno').val();
			var active = $('#active').val();
			var is_approve = $('#is_approve').val();
			var region = $('#region').val();
			var dob = $('#dob').val();
			var gender = $('#gender').val();
			var village = $('#village').val();
			var villagename = $('#villagename').val();
			var aadharState = $('#aadharState').val();
			var aadharDist = $('#aadharDist').val();
			var aadharSubDist = $('#aadharSubDist').val();
			var aadharPO = $('#aadharPO').val();
			var aadharVtc = $('#aadharVtc').val();
			var aadharLoc = $('#aadharLoc').val();
			var aadharStreet = $('#aadharStreet').val();
			var aadharHouse = $('#aadharHouse').val();
			var aadharPincode = $('#aadharPincode').val();
			var aadharImage = $('#aadharImage').val();
			var aadharFullname = $('#aadharFullname').val();
			var aadharGender = $('#aadharGender').val();
			var aadharDOB = $('#aadharDOB').val();
			// var otpCheck = $('#otpCheck').val();
			var otpCheck = $('#isValidaadhar').val();
			var is_manual = $('#man_aa_ver').is(':checked');
			//alert(is_manual);
			if (AccountID == '') {
				alert('please enter AccountID');
				$('#AccountID').focus();
			} else if (firstname == '') {
				alert('please enter First Name');
				$('#firstname').focus();
			} else if (lastname == '') {
				alert('please enter Last Name');
				$('#lastname').focus();
			} else if (phonenumber == '') {
				alert('please enter mobile number');
				$('#phonenumber').focus();
			} else if (!$('#phonenumber').val().match('[0-9]{10}') && $('#phonenumber').val() !== "") {
				alert('Enter valid Mobile number');
				$('#phonenumber').focus();
			} else if (state == '') {

				alert('please select State');

				$('#state').focus();

			} else if (city == '') {

				alert('please select City');

				$('#city').focus();

			} else if (subdist == '') {

				alert('please select Taluka');

				$('#subdist').focus();

			} else if (po == '') {

				alert('please  enter post office');

				$('#po').focus();

			} else if (loc == '') {

				alert('please enter locality');

				$('#loc').focus();

			} else if (street == '') {

				alert('please enter street');

				$('#street').focus();

			} else if (house == '') {

				alert('please enter house');

				$('#house').focus();

			} else if (zip == '') {

				alert('please enter Pincode');

				//$('#zip').focus();

			} else if (village == '') {

				alert('please select village');

				$('#village').focus();

			} else if (village == 'new' && villagename == '') {

				alert('Enter village name');

				$('#villagename').focus();

			} else if (!$('#Aadhaarno').val().match('[0-9]{12}') && $('#Aadhaarno').val() !== "") {

				alert('Enter valid Aadhar number');

				$('#Aadhaarno').focus();

			} else if (otpCheck == 'N' && $('#man_aa_ver').is(':checked') == false) {

				alert('Aadhar not verified');

			} else {



				$.ajax({

					url: "<?php echo admin_url(); ?>clients/SaveFarmar",

					dataType: "JSON",

					method: "POST",

					data: {
						AccountID: AccountID,
						CustomerType: CustomerType,
						firstname: firstname,
						lastname: lastname,
						middlename: middlename,
						phonenumber: phonenumber,

						state: state,
						city: city,
						house: house,
						street: street,
						loc: loc,
						po: po,
						subdist: subdist,
						dob: dob,
						gender: gender,
						is_manual: is_manual,

						zip: zip,
						Aadhaarno: Aadhaarno,
						active: active,
						is_approve: is_approve,
						region: region,
						aadharState: aadharState,
						aadharDist: aadharDist,

						aadharSubDist: aadharSubDist,
						aadharPO: aadharPO,
						aadharVtc: aadharVtc,
						aadharLoc: aadharLoc,
						aadharStreet: aadharStreet,
						aadharHouse: aadharHouse,

						aadharPincode: aadharPincode,
						aadharImage: aadharImage,
						aadharFullname: aadharFullname,
						aadharGender: aadharGender,
						aadharDOB: aadharDOB,
						village: village,
						villagename: villagename,

					},

					beforeSend: function() {

						$('.searchh3').css('display', 'block');

						$('.searchh3').css('color', 'blue');

					},

					complete: function() {

						$('.searchh3').css('display', 'none');

					},

					success: function(data) {



						if (data == true) {

							alert('Record created successfully...');

							$('#AccountID').val('');

							ResetForm();

						} else {

							alert_float('warning', 'Something went wrong...');

						}

					}

				});

			}



		});



	// Update Exiting Item

	$('.updateBtn').on('click', function()
		{
			var AccountID = $('#AccountID').val();
			var firstname = $('#firstname').val();
			var middlename = $('#middlename').val();
			var lastname = $('#lastname').val();
			var phonenumber = $('#phonenumber').val();
			var state = $('#state').val();
			var city = $('#city').val();
			var house = $('#house').val();
			var street = $('#street').val();
			var loc = $('#loc').val();
			var po = $('#po').val();
			var subdist = $('#subdist').val();
			var zip = $('#zip').val();
			var Aadhaarno = $('#Aadhaarno').val();
			var existing_aadhar = $('#existing_aadhar').val();
			var aadhaar_verified_date_val = $('#aadhaar_verified_date').val();
			var active = $('#active').val();
			var is_approve = $('#is_approve').val();
			var region = $('#region').val();
			var dob = $('#dob').val();
			var gender = $('#gender').val();
			var village = $('#village').val();
			var villagename = $('#villagename').val();
			var aadharState = $('#aadharState').val();
			var aadharDist = $('#aadharDist').val();
			var aadharSubDist = $('#aadharSubDist').val();
			var aadharPO = $('#aadharPO').val();
			var aadharVtc = $('#aadharVtc').val();
			var aadharLoc = $('#aadharLoc').val();
			var aadharStreet = $('#aadharStreet').val();
			var aadharHouse = $('#aadharHouse').val();
			var aadharPincode = $('#aadharPincode').val();
			var aadharImage = $('#aadharImage').val();
			var aadharFullname = $('#aadharFullname').val();
			var aadharGender = $('#aadharGender').val();
			var aadharDOB = $('#aadharDOB').val();
			// var otpCheck = $('#otpCheck').val();
			var otpCheck = $('#isValidaadhar').val();
			var is_manual = $('#man_aa_ver').is(':checked');

			if (AccountID == '') {
				alert('please enter AccountID');
				$('#AccountID').focus();
			} else if (firstname == '') {
				alert('please enter First Name');
				$('#firstname').focus();
			} else if (lastname == '') {
				alert('please enter Last Name');
				$('#lastname').focus();
			} else if (state == '') {
				alert('please select State');
				$('#state').focus();
			} else if (city == '') {
				alert('please select City');
				$('#city').focus();
			} else if (subdist == '') {
				alert('please select Taluka');
				$('#subdist').focus();
			} else if (po == '') {
				alert('please  enter post office');
				$('#po').focus();
			} else if (loc == '') {
				alert('please  enter locality');
				$('#loc').focus();
			} else if (street == '') {
				alert('please  enter street');
				$('#street').focus();
			} else if (house == '') {
				alert('please enter house');
				$('#house').focus();
			} else if (phonenumber == '') {
				alert('please  enter mobile number');
				$('#phonenumber').focus();
			} else if (!$('#phonenumber').val().match('[0-9]{10}') && $('#phonenumber').val() !== "") {
				alert('Enter valid Mobile number');
				$('#phonenumber').focus();
			} else if (zip == '') {
				alert('please enter Pincode');
				$('#zip').focus();
			} else if ($('#Aadhaarno').val() == "" || !$('#Aadhaarno').val().match('[0-9]{12}')) {
				alert('Enter valid Aadhar number');
				$('#Aadhaarno').focus();
				// }else if(otpCheck == '0' && existing_aadhar != Aadhaarno){
			} else if (village == '') {
				alert('please select village');
				$('#village').focus();
			} else if (village == 'new' && villagename == '') {
				alert('Enter village name');
				$('#villagename').focus();
			} else if (otpCheck == 'N' && $('#man_aa_ver').is(':checked') == false) {
				alert('Aadhar not verified');
			} else {
				$.ajax({

					url: "<?php echo admin_url(); ?>clients/UpdateFarmar",

					dataType: "JSON",

					method: "POST",

					data: {
						AccountID: AccountID,
						firstname: firstname,
						lastname: lastname,
						phonenumber: phonenumber,
						middlename: middlename,
						gender: gender,

						state: state,
						city: city,
						house: house,
						street: street,
						loc: loc,
						po: po,
						subdist: subdist,
						existing_aadhar: existing_aadhar,
						dob: dob,

						is_manual: is_manual,

						aadhaar_verified_date: aadhaar_verified_date_val,

						zip: zip,
						Aadhaarno: Aadhaarno,
						active: active,
						is_approve: is_approve,
						region: region,
						aadharState: aadharState,
						aadharDist: aadharDist,

						aadharSubDist: aadharSubDist,
						aadharPO: aadharPO,
						aadharVtc: aadharVtc,
						aadharLoc: aadharLoc,
						aadharStreet: aadharStreet,
						aadharHouse: aadharHouse,

						aadharPincode: aadharPincode,
						aadharImage: aadharImage,
						aadharFullname: aadharFullname,
						aadharGender: aadharGender,
						aadharDOB: aadharDOB,
						village: village,
						villagename: villagename,

					},

					beforeSend: function() {

						$('.searchh4').css('display', 'block');

						$('.searchh4').css('color', 'blue');

					},

					complete: function() {

						$('.searchh4').css('display', 'none');

					},

					success: function(data) {

						if (data == true) {

							alert('Record updated successfully...');

							$('#AccountID').val('');

							ResetForm();

						} else {

							alert_float('warning', 'Something went wrong...');

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

			url: url,

			data: {
				StateID: StateID
			},

			dataType: 'json',

			success: function(data) {

				$("#city").find('option').remove();

				$("#city").selectpicker("refresh");

				$("#city").append(new Option('', 'select city'));

				for (var i = 0; i < data.length; i++) {

					$("#city").append(new Option(data[i].city_name, data[i].id));

				}

				$('.selectpicker').selectpicker('refresh');

				// Remove taluka option 

				$("#subdist").find('option').remove();

				$('.selectpicker').selectpicker('refresh');

			}

		});

	});



	$('#city').on('change', function() {

		var CityID = $(this).val();

		var url = "<?php echo base_url(); ?>admin/clients/GetTaluka";

		jQuery.ajax({

			type: 'POST',

			url: url,

			data: {
				CityID: CityID
			},

			dataType: 'json',

			success: function(data) {

				$("#subdist").find('option').remove();

				$("#subdist").selectpicker("refresh");

				$("#subdist").append(new Option('', 'select taluka'));

				for (var i = 0; i < data.length; i++) {

					$("#subdist").append(new Option(data[i].TalukaName, data[i].id));

				}

				$('.selectpicker').selectpicker('refresh');

			}

		});

	});



	function changeDefaultBank(id, accountID) {

		$.ajax({

			url: "<?php echo admin_url(); ?>clients/UpdateDefaultBank",

			method: "POST",

			data: {
				ID: id,
				AccountID: accountID
			},

			success: function(data) {

				if (data) {

					$.ajax({

						url: "<?php echo admin_url(); ?>clients/GetBankDetailByID",

						method: "POST",

						data: {
							AccountID: accountID
						},

						success: function(data) {

							$("#BankTableBody").html(data);

						}

					});

				} else {

					alert("Operation Failed, Primary Account update was unsuccessful");

				}

			}

		});

	}
</script>



<script>
	let timer;

	const waitTime = 1000;



	function mysearch() {

		clearTimeout(timer);

		timer = setTimeout(() => {

			myFunction2();

		}, waitTime);

	}



	function myFunction2() {

		var input, filter, table, tr, td, i, txtValue;

		input = document.getElementById("myInput1");

		filter = input.value.toUpperCase();

		if (filter.length > 4) {

			$.ajax({

				url: "<?php echo admin_url(); ?>clients/FarmarListPopUp",

				//dataType:"JSON",

				method: "POST",

				cache: false,

				data: {
					search: filter,
				},

				success: function(data) {

					if (empty(data)) {

						alert('Record Not Found...');

					} else {

						var html = '';

						var dataArray = JSON.parse(data);

						for (let i = 0; i < dataArray.length; i++) {

							var value = dataArray[i];



							html += '<tr class="get_AccountID" data-id="' + value["AccountID"] + '">';

							html += '<td style="text-align:center;">' + (i + 1) + '</td>';

							// html += '<td>' + value["AccountID"] + '</td>';

							html += '<td>' + value["firstname"] + " " + value["lastname"] + '</td>';

							html += '<td>' + value["phonenumber"] + '</td>';

							html += '<td>' + value['adharno'] + '</td>';

							html += '<td>' + value["state_name"] + '</td>';

							html += '<td>' + value["city_name"] + '</td>';

							html += '</tr>';

						}

						$("#ListTableBody").html(html);

						$('.get_AccountID').on('click', function() {

							AccountID = $(this).attr("data-id");

							$.ajax({

								url: "<?php echo admin_url(); ?>clients/GetBankDetailByID",

								method: "POST",

								data: {
									AccountID: AccountID
								},

								success: function(data) {

									$("#BankTableBody").html(data);

								}

							});

							$.ajax({

								url: "<?php echo admin_url(); ?>clients/GetFirmDetailByID",

								method: "POST",

								data: {
									AccountID: AccountID
								},

								success: function(data) {

									$("#FarmTableBody").html(data);

								}

							});

							$.ajax({

								url: "<?php echo admin_url(); ?>clients/GetCropDetailByID",

								method: "POST",

								data: {
									AccountID: AccountID
								},

								success: function(data) {

									$("#CropTableBody").html(data);

								}

							});

							$.ajax({

								url: "<?php echo admin_url(); ?>clients/GetAadhar",

								method: "POST",

								data: {

									AccountID: AccountID

								},

								success: function(data) {

									$('#aadhar_pics').html(data);

								}

							});

							$.ajax({

								url: "<?php echo admin_url(); ?>clients/GetAadharProfile",

								method: "POST",

								data: {

									AccountID: AccountID

								},

								success: function(data) {

									$('#aadhar_profile').html(data);

								}

							});

							$.ajax({

								url: "<?php echo admin_url(); ?>clients/GetAccountDetailByID",

								dataType: "JSON",

								method: "POST",

								data: {
									AccountID: AccountID
								},

								beforeSend: function() {

									$('.searchh2').css('display', 'block');

									$('.searchh2').css('color', 'blue');

								},

								complete: function() {

									$('.searchh2').css('display', 'none');

								},

								success: function(data) {

									$('#AccountID').val(data.AccountID);

									$('#ShortCode').val(data.ShortCode);

									$('#firstname').val(data.firstname);

									$('#middlename').val(data.middlename);

									$('#lastname').val(data.lastname);

									$('#phonenumber').val(data.phonenumber);

									$('#house').val(data.house);

									$('#street').val(data.street);

									$('#loc').val(data.loc);

									$('#po').val(data.po);

									$('#zip').val(data.zip);

									$('#Aadhaarno').val(data.aadhaar_number);

									$('#existing_aadhar').val(data.aadhaar_number);

									if (data.aadhaar_verified_date == null) {

										$('#verifyAadhar').css('display', '');

										$('#check').css('display', 'none');

										$('#otpCheck').val('0');

										$('#Aadhaarno').prop('disabled', false);

										$('#btn_send_otp').css('display', 'block');

									} else if (data.aadhaar_verified_date != null) {

										$('#verifyAadhar').css('display', 'none');

										$('#check').css('display', '');

										$('#otpCheck').val('1');

										$('#Aadhaarno').prop('disabled', true);

										$('#btn_send_otp').css('display', 'none');

									}

									var dob_date = data.dob

									if (data.dob == null) {



									} else {

										var dob = dob_date.split("-").reverse().join("/");

										$('#dob').val(dob);

									}

									$('select[name=state]').val(data.state);

									$('.selectpicker').selectpicker('refresh');



									$('select[name=gender]').val(data.gender);

									$('.selectpicker').selectpicker('refresh');

									if (data.aadhaar_verified_date == null || data.aadhaar_verified_date == "") {

										date_new = "";

									} else {

										var date = data.aadhaar_verified_date.substring(0, 10)

										var date_new = date.split("-").reverse().join("/");

									}



									var Addr = 'Address : ' + data.Aloc + ', ' + data.Apincode;

									$('#aadhar_label').html("Address as per Aadhaar Verification As On " + date_new);

									$('#aadhar_address').html(Addr);

									$('#aadhar_state').html("State : " + data.Astate);

									$('#aadhar_city').html("City : " + data.Adist);



									$('select[name=active]').val(data.active);

									$('.selectpicker').selectpicker('refresh');



									$('select[name=region]').val(data.regionID);

									$('.selectpicker').selectpicker('refresh');



									$('select[name=is_approve]').val(data.is_approve);

									$('.selectpicker').selectpicker('refresh');

									let CityList = data.CityList;

									$("#city").children().remove();

									for (var i = 0; i < CityList.length; i++) {

										$("#city").append('<option value="' + CityList[i]["id"] + '">' + CityList[i]["city_name"] + '</option>');

									}

									$('.selectpicker').selectpicker('refresh');

									$('#isValidaadhar').val('Y');

									$('#city').selectpicker('val', data.dist);

									$('.selectpicker').selectpicker('refresh');



									let TalukaList = data.TalukaList;

									$("#subdist").children().remove();

									for (var i = 0; i < TalukaList.length; i++) {

										$("#subdist").append('<option value="' + TalukaList[i]["id"] + '">' + TalukaList[i]["TalukaName"] + '</option>');

									}

									$('.selectpicker').selectpicker('refresh');



									$('#subdist').selectpicker('val', data.subdist);

									$('.selectpicker').selectpicker('refresh');



									let VillageList = data.VillageList;

									$("#village").children().remove();

									$("#village").append('<option value="new">New Village</option>');

									for (var i = 0; i < VillageList.length; i++) {

										$("#village").append('<option value="' + VillageList[i]["id"] + '">' + VillageList[i]["VillageName"] + '</option>');

									}

									$('.selectpicker').selectpicker('refresh');

									$('#village').selectpicker('val', data.VillageID);

									$('.selectpicker').selectpicker('refresh');



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

		}

	}
</script>

<script>
	function validateZipCode(elementValue) {

		var zipCodePattern = /^\d{5}$|^\d{5}-\d{4}$/;

		return zipCodePattern.test(elementValue);

	}
</script>

<script>
	function isNumber(evt) {

		evt = (evt) ? evt : window.event;

		var charCode = (evt.which) ? evt.which : evt.keyCode;

		if (charCode = 46 && charCode > 31

			&&
			(charCode < 48 || charCode > 57)) {

			return false;

		}

		return true;

	}
</script>



<script type="text/javascript">
	$('#MaxCrdAmt,#kms,.opening_bal').on('keypress', function(event) {

		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {

			event.preventDefault();

		}

		var input = $(this).val();

		if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {

			event.preventDefault();

		}

	});
</script>

<script type="text/javascript">
	function printPage() {

		var html_filter_name = $('.report_for').html();

		var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';

		var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">' + document.getElementsByTagName('table')[3].innerHTML + '</table>';

		var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';

		heading_data += '<tr>';

		heading_data += '<td style="text-align:center;"colspan="3">Farmer List Report</td>';

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
	$("#caexcel").click(function() {

		var data_val = "data";

		$.ajax({

			url: "<?php echo admin_url(); ?>clients/export_FarmerMaster",

			method: "POST",

			data: {
				data_val: data_val,
			},

			beforeSend: function() {

				$('#searchh3').css('display', 'block');

			},

			complete: function() {

				$('#searchh3').css('display', 'none');

			},

			success: function(data) {

				response = JSON.parse(data);

				window.location.href = response.site_url + response.filename;

			}

		});

	});



	$('#ifsc_code').blur(function() {

		var ifsc_code = $('#ifsc_code').val();

		$.ajax({

			url: "<?php echo admin_url(); ?>clients/fetchBankDetailsFromIFSC",

			method: "POST",

			dataType: 'json',

			data: {
				ifsc_code: ifsc_code
			},

			beforeSend: function() {

				$('.searchh6').css('display', 'block');



				$('.searchh6').css('color', 'blue');

			},

			complete: function() {

				$('.searchh6').css('display', 'none');

			},

			success: function(data) {

				// var data1 = JSON.parse(data);

				if (data == "Not Found") {

					alert("Enter valid IFSC Code");

					$('#bank_name').prop("readonly", false);

					$('#bank_branch').prop("readonly", false);

					$('#bank_name').val("");

					$('#bank_branch').val("");

				} else {

					$('#bank_name').prop("readonly", true);

					$('#bank_branch').prop("readonly", true);

					$('#bank_name').val(data.BANK);

					$('#bank_branch').val(data.BRANCH);

				}

			}

		});

	});



	function validateAccountNumber() {

		var account_number = $('#account_number').val();

		var reaccount_number = $('#reaccount_number').val();

		if (account_number == reaccount_number) {

			$('#account_number_error').text('');

			$('#bankSubmit').prop('disabled', false);

			return true;

		} else {

			$('#account_number_error').text('Account number does not match');

			$('#account_number_error').css('color', 'red');

			$('#bankSubmit').prop('disabled', true);

			return false;

		}

	}



	$('#reaccount_number').blur(function() {

		var reaccount_number = $('#reaccount_number').val();

		var ifsc_code = $('#ifsc_code').val();

		if (validateAccountNumber() == false) {

			$('#account_number_error').text('Account number does not match');

			$('#bankSubmit').prop('disabled', true);

		} else {

			$.ajax({

				url: "<?php echo admin_url(); ?>clients/verifyBankAccount",

				method: "POST",

				dataType: 'json',

				data: {
					reaccount_number: reaccount_number,
					ifsc_code: ifsc_code
				},

				beforeSend: function() {

					$('.searchh6').css('display', 'block');



					$('.searchh6').css('color', 'blue');

				},

				complete: function() {

					$('.searchh6').css('display', 'none');

				},

				success: function(data) {

					if (data.success == false) {

						alert("Bank account not verified");

						$('#account_number_error').text('Bank account not verified');

						$('#account_number_error').css('color', 'red');

						$("#is_act_validate").removeAttr("disabled");

						$('#bankSubmit').prop('disabled', true);

					} else {

						$('#account_number_error').text('Bank Account verified successfully');

						$('#account_number_error').css('color', 'green');

						$('#is_act_validate').prop('checked', false);

						$("#is_act_validate").attr("disabled");

						$('#bankSubmit').prop('disabled', false);

						$('#account_name').val(data.data.full_name);

					}

				}

			});

		}

	});

	$('#is_act_validate').change(function() {

		var isChecked = $('#is_act_validate').prop('checked');

		if (isChecked) {

			$('#bankSubmit').prop('disabled', false);

		} else {

			$('#bankSubmit').prop('disabled', true);

		}

	})



	function openBankModal() {

		$('#accountFor').val('');

		$('#ifsc_code').val('');

		$('#bank_name').val('');

		$('#bank_branch').val('');

		$('#account_number').val('');

		$('#account_name').val('');

		$('#is_primarybank').prop('checked', false);

		$("#is_act_validate").attr("disabled");

		$('#bankModal').modal('show');

	}



	function addBankDetails() {

		var accountFor = $('#accountFor').val();

		var ifsc_code = $('#ifsc_code').val();

		var bank_name = $('#bank_name').val();

		var bank_branch = $('#bank_branch').val();

		var account_number = $('#account_number').val();

		var account_name = $('#account_name').val();

		var AccountID = $('#AccountID').val();

		var fileInput = $('#cheque_image')[0];

		var isChecked = $('#is_primarybank').prop('checked');

		if (isChecked) {

			var is_primary = $('#is_primarybank').val();

		} else {

			var is_primary = '';

		}

		var isCheckedAct = $('#is_act_validate').prop('checked');

		if (isCheckedAct) {

			var is_act_validate = $('#is_act_validate').val();

		} else {

			var is_act_validate = 'N';

		}

		if (accountFor == '') {

			alert('Select Account For');

		} else if (ifsc_code == '') {

			alert('Enter IFSC Code');

		} else if (account_number == '') {

			alert('Enter Bank Account Number');

		} else if (reaccount_number == '') {

			alert('Confirm Account Number');

		} else if (account_name == '') {

			alert('Enter Account Name');

		} else if (document.getElementById("cheque_image").files.length == 0) {

			alert('Attatch Cheque Image');

		} else {

			var reader = new FileReader();



			if (fileInput.files.length > 0) {

				reader.readAsDataURL(fileInput.files[0]);

			} else {



				var formData = new FormData();

				formData.append('accountFor', accountFor);

				formData.append('ifsc_code', ifsc_code);

				formData.append('bank_name', bank_name);

				formData.append('bank_branch', bank_branch);

				formData.append('account_number', account_number);

				formData.append('account_name', account_name);

				formData.append('cheque_image', 'NA');

				formData.append('AccountID', AccountID);

				formData.append('is_primary', is_primary);

				formData.append('is_act_validate', is_act_validate);



				$.ajax({

					url: "<?php echo admin_url(); ?>clients/addBankDetails",

					method: "POST",

					dataType: 'json',

					data: formData,

					contentType: false,

					processData: false,

					beforeSend: function() {

						$('.searchh7').css('display', 'block');

						$('.searchh7').css('color', 'blue');

					},

					complete: function() {

						$('.searchh7').css('display', 'none');

					},

					success: function(data) {

						if (data == true) {

							$('#bankModal').modal('hide');

						} else {

							alert('Something went wrong')

						}

						// console.log(data);

					}

				});

			}

			reader.onload = function(e) {

				var cheque_image = e.target.result; // Base64 encoded image



				var formData = new FormData();

				formData.append('accountFor', accountFor);

				formData.append('ifsc_code', ifsc_code);

				formData.append('bank_name', bank_name);

				formData.append('bank_branch', bank_branch);

				formData.append('account_number', account_number);

				formData.append('account_name', account_name);

				formData.append('AccountID', AccountID);

				formData.append('is_primary', is_primary);

				formData.append('is_act_validate', is_act_validate);

				formData.append('cheque_image', cheque_image);

				$.ajax({

					url: "<?php echo admin_url(); ?>clients/addBankDetails",

					method: "POST",

					dataType: 'json',

					data: formData,

					contentType: false,

					processData: false,

					beforeSend: function() {

						$('.searchh7').css('display', 'block');

						$('.searchh7').css('color', 'blue');

					},

					complete: function() {

						$('.searchh7').css('display', 'none');

					},

					success: function(data) {

						if (data == true) {

							$('#bankModal').modal('hide');

						} else {

							alert('Something went wrong')

						}

						// console.log(data);

					}

				});

			};



		}

	}
</script>

<style>
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



	.table-Account_List {
		overflow: auto;
		max-height: 65vh;
		width: 100%;
		position: relative;
		top: 0px;
	}

	.table-Account_List thead th {
		position: sticky;
		top: 0;
		z-index: 1;
	}

	.table-Account_List tbody th {
		position: sticky;
		left: 0;
	}

	table {
		border-collapse: collapse;
		width: 100%;
	}

	th,
	td {
		padding: 1px 5px !important;
		white-space: nowrap;
		border: 1px solid !important;
		font-size: 11px;
		line-height: 1.42857143 !important;
		vertical-align: middle !important;
	}

	th {
		background: #50607b;

		color: #fff !important;
	}
</style>