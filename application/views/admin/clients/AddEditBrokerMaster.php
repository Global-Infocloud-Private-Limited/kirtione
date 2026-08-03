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
    							<li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Broker Master</b></li>
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
							<div class="col-md-3">
								<?php  ?>
								<div class="form-group" app-field-wrapper="AccountID">
									<small class="req text-danger">* </small>
									<label for="AccountID" class="control-label">AccountID</label>
									<input type="text" id="AccountID" name="AccountID" class="form-control" value="" maxlength="10">
									<?php $staff_user_id = $this->session->userdata('staff_user_id'); ?>
									<input type="hidden" name="staffid" value="<?php echo $staff_user_id; ?>" id="staffid">
									<input type="hidden" name="PlantID" value="<?php echo $this->session->userdata('root_company');?>" id="PlantID">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="AccoountName">
									<small class="req text-danger">* </small>
									<label for="AccoountName" class="control-label">Business Name</label>
									<input type="text" id="AccoountName" name="AccoountName" class="form-control" value="" >
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="firstname">
									<small class="req text-danger">* </small>
									<label for="firstname" class="control-label">First Name</label>
									<input type="text" id="firstname" name="firstname" class="form-control" value="">
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="lastname">
									<small class="req text-danger">* </small>
									<label for="lastname" class="control-label">Last Name</label>
									<input type="text" id="lastname" name="lastname" class="form-control" value="">
								</div>
							</div>
							<div class="clearfix"></div>
							
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="phonenumber">
									<small class="req text-danger">* </small>
									<label for="phonenumber" class="control-label">Mobile Number</label>
									<input type="text" id="phonenumber" readonly name="phonenumber" class="form-control" value="" maxlength="10" minlength="10" onkeypress="return isNumber(event)" >
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="altphonenumber">
									<label for="altphonenumber" class="control-label">Alternative Mobile</label>
									<input type="text" id="altphonenumber" name="altphonenumber" class="form-control" value="" maxlength="10" minlength="10" onkeypress="return isNumber(event)">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="email">
									<label for="email" class="control-label">Email</label>
									<input type="text" id="email" name="email" class="form-control" value="">
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="Pan"> 
									<small class="req text-danger">* </small>
									<label for="Pan" class="control-label">PAN Number</label>&nbsp;&nbsp;&nbsp;
									<a id="verifyPan" style="color:#f93500;cursor:pointer;font-size:14px;font-weight:600;">Click To Verify</a>
									<span id="check" style="display:none;color:#1be91b;"><i style="font-size:16px;" class="fa fa-check-circle" aria-hidden="true"></i>
									</span>
									<input type="text" maxlength="10" minlength="10" onchange="VerifyPanNo()" name="Pan" pattern="[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}" id="Pan" class="form-control" value="">
									<span class="pan_denger" style="color:red;"></span>
									<input type="hidden" id="isValidPan" value="N" >
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="state">
									<small class="req text-danger">* </small>
									<label for="state" class="form-label">State</label>
									<select name="state" id="state" class="selectpicker form-control" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
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
							
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="AccountID">
									<input id="city_name" value="" hidden>
									<small class="req text-danger">* </small>
									<label for="city" class="control-label">City</label>
									<select name="city" id="city" class="selectpicker form-control" data-none-selected-text="None selected" data-live-search="true">
                                        
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<!--<small class="req text-danger">* </small>-->
									<label for="subdist" class="control-label">Taluka</label>
									<select class="selectpicker form-control" id="subdist" name="subdist" data-none-selected-text="None selected" data-live-search="true">
									</select>
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group">
									<!--<small class="req text-danger">* </small>-->
									<label for="po" class="control-label">Post Office</label>
									<input type="text" id="po" name="po" class="form-control" value="">
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group">
									<!--<small class="req text-danger">* </small>-->
									<label for="vtc" class="control-label">Town</label>
									<input type="text" id="vtc" name="vtc" class="form-control" value="">
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group">
									<!--<small class="req text-danger">* </small>-->
									<label for="loc" class="control-label">Locality</label>
									<input type="text" id="loc" name="loc" class="form-control" value="">
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group">
									<!--<small class="req text-danger">* </small>-->
									<label for="street" class="control-label">Street</label>
									<input type="text" id="street" name="street" class="form-control" value="">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<!--<small class="req text-danger">* </small>-->
									<label for="house" class="control-label">House</label>
									<input type="text" id="house" name="house" class="form-control" value="">
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="zip">
									<label for="zip" class="control-label">Pin Code</label>
									<input type="text"  name="zip" id="zip" class="form-control" onchange="validateZipCode" value="" maxlength="6" minlength="6" onkeypress="return isNumber(event)">
								</div>
							</div>
							<div class="col-md-2">
								<small class="req text-danger">* </small>
								<label for="active">Status</label>
								<select name="active" id="active" class="selectpicker form-control tcs_type" data-none-selected-text="None selected" data-live-search = "true" tabindex="-98">
									<option value="0">No</option>
									<option value="1">Yes</option>
								</select>
							</div>
							
							<div class="col-md-2" >
                                <div class="form-group" app-field-wrapper="is_approve">
                                    <small class="req text-danger">* </small>
                                    <label for="is_approve" class="form-label">Kirti Approval</label>
                                    <select name="is_approve" id="is_approve" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="0" >No</option> 
                                        <option value="1" >Yes</option>
                                    </select>
                                </div>
                            </div>
							<!--<div class="col-md-3">
								<small class="req text-danger">* </small>
								<label for="istcs">TCS</label>
								<select name="istcs" id="istcs" class="selectpicker form-control tcs_type" data-none-selected-text="None selected" data-live-search = "true" tabindex="-98">
									<option value="0">No</option>
									<option value="1">Yes</option>
								</select>
							</div>
							<div class="col-md-3">
							    <?php $value = date('d/m/Y');?>
								<?php echo render_date_input( 'TcsStartDate1', 'TCS Date',$value,'text'); ?>
								<input type="hidden" name="TcsStartDate" value="">
							</div>-->
							
							<div class="col-md-3">
								<?php $value = date('d/m/Y');?>
								<?php echo render_date_input( 'StartDate', 'Start Date',$value,'text'); ?>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="short_code">
									<label for="short_code" class="control-label">Short Code</label>
									<input type="text"  name="short_code" id="short_code" class="form-control"  value="" readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="IsKirtiOneAccess">
									<label for="IsKirtiOneAccess" class="control-label">Kirti One Access</label>
									<select name="IsKirtiOneAccess" id="IsKirtiOneAccess" class="selectpicker form-control" data-none-selected-text="None selected" data-live-search = "true" tabindex="-98">
    									<option value="N">No</option>
    									<option value="Y">Yes</option>
    								</select>
								</div>
							</div>
							<div class="col-md-2">
								<small class="req text-danger">* </small>
								<label for="istds">TDS</label>
								<select name="istds" id="istds" class="selectpicker form-control tds_type" data-none-selected-text="None selected" data-live-search = "true" tabindex="-98">
									<option value="0">No</option>
									<option value="1">Yes</option>
								</select>
							</div>
							<div class="col-md-3" id="div_tds_section">
								<label for="tds_section">TDS Section</label>
								<select name="tds_section" id="tds_section" class="selectpicker form-control tds_section" data-none-selected-text="None selected" data-live-search = "true" tabindex="-98">
									<option value="">Select TDS Section</option>
									<?php
										foreach ($tds_sections as $key => $value) {
										?>
										<option value="<?php echo $value['TDSCode'];?>"><?php echo $value['TDSName'];?></option>
										<?php
										}
									?>
								</select>
							</div>
							<div class="col-md-2" id="div_tds_rate">
								<label for="tds_rate">TDS Rate (%)</label>
								<!--<input type="text" name="tds_rate" id="tds_rate" class="form-control" disabled>-->
								<select name="tds_rate" id="tds_rate" class="selectpicker form-control tds_rate" data-none-selected-text="None selected" data-live-search = "true" tabindex="-98">
									<!--<option value="">Select TDS Rate</option>-->
								</select>
							</div>
							
							
							
							<div class="clearfix"></div>
							<div class="container-fluid">
								<label for="active" class="form-label">Bank Account Details</label>
								<table class="table table-striped table-bordered">
									<thead>
										<tr>
											<th>Bank Name</th>
											<th>Branch Name</th>
											<th>Account Name</th>
											<th>Account No.</th>
											<th>IFSC</th>
											<th>Is Primary</th>
											<th>Cheque/Passbook Img</th>
										</tr>
									</thead>
									<tbody id="BankTableBody"></tbody>
								</table>
							</div>
							<div class="container-fluid">
								<label for="active" class="form-label">GST Account Details</label>
								<table class="table table-striped table-bordered">
									<thead>
										<tr>
											<th>Gst no.</th>
											<th>State</th>
											<th>Business Name</th>
											<th>Address</th>
											<th>Is Primary</th>
										</tr>
									</thead>
									<tbody id="GstTableBody"></tbody>
								</table>
							</div>
							
							<div class="row">
								<div class="col-md-12">
									<?php if (has_permission_new('BrokerMaster', '', 'create')) {
									?>
									<button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
									<?php
										}else{
									?>
									<button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
									<?php
									}?>
									
									<?php if (has_permission_new('BrokerMaster', '', 'edit')) {
									?>
									<button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button><button type="button" class="btn btn-info updateBtn2" style="margin-right: 25px;" onclick="openBankModal()" >Upload Bank Details</button>
									<button type="button" class="btn btn-info updateGstBtn" style="margin-right: 25px;" onclick="openGstModal()" >Upload GST Details</button>
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
														<input type="text" name="ifsc_code" id="ifsc_code" class="form-control"  value="" >
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group" app-field-wrapper="zip">
														<label for="zip" class="control-label">Bank Name</label>
														<input type="text" name="bank_name" id="bank_name" readonly class="form-control"  value="" >
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group" app-field-wrapper="zip">
														<label for="zip" class="control-label">Branch Name</label>
														<input type="text"  name="bank_branch" id="bank_branch" readonly class="form-control"  value="" >
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group" app-field-wrapper="zip">
														<small class="req text-danger">* </small>
														<label for="zip" class="control-label">Account Number</label>
														<input type="text"  name="account_number" id="account_number" oncopy="return false" onpaste="return false" oncut="return false" onkeyup=" validateAccountNumber()" class="form-control"  value="" >
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group" app-field-wrapper="zip">
														<small class="req text-danger">* </small>
														<label for="zip" class="control-label">Confirm Account Number</label>
														<input type="text"  name="reaccount_number" id="reaccount_number" oncopy="return false" onpaste="return false" oncut="return false" onkeyup="validateAccountNumber()" class="form-control"  value="" >
														<span id="account_number_error" style="color:red;"></span>
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group" app-field-wrapper="account_name">
														<label for="account_name" class="control-label">Account Name</label>
														<input type="text"  name="account_name" id="account_name" class="form-control"  value="" >
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group" app-field-wrapper="cheque_image">
														<label for="cheque_image" class="control-label">Cheque Image</label>
														<input type="file"  name="cheque_image" id="cheque_image" class="form-control" value="" >
													</div>
												</div>
												<div class="col-md-4">
													<input type="checkbox" name="is_primarybank" id="is_primarybank"  value="Y" >
													<b for="is_primarybank" >Is Primary</b>
												</div>
												<div class="col-md-8">
													<input type="checkbox" name="is_act_validate" id="is_act_validate"  value="Y">
													<b for="is_act_validate" >Manual Account Validate</b>
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
							<!--GST MODAL-->
							<div class="modal fade" id="GSTModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitleGST" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="exampleModalLongTitleGST">Upload GST Details</h5>
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
														<label for="zip" class="control-label">GST NO</label>
														<input type="text" name="gst_no" id="gst_no" class="form-control"  value="" >
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group" app-field-wrapper="zip">
														<small class="req text-danger">* </small>
														<label for="business_name" class="control-label">Business Name</label>
														<input type="text" name="business_name" id="business_name" readonly class="form-control"  value="" >
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group" app-field-wrapper="state_gst">
														<small class="req text-danger">* </small>
														<label for="state_gst" class="control-label">State</label>
														<input type="text"  name="state_gst" id="state_gst" readonly class="form-control"  value="" >
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group" app-field-wrapper="address_gst">
														<small class="req text-danger">* </small>
														<label for="address_gst" class="control-label">Address</label>
														<input type="text"  name="address_gst" readonly id="address_gst" class="form-control"  value="" >
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group" app-field-wrapper="constitution_business">
														<small class="req text-danger">* </small>
														<label for="constitution_business" class="control-label">Constitution Of Business</label>
														<input type="text"  name="constitution_business" readonly id="constitution_business" class="form-control"  value="" >
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group" app-field-wrapper="taxpayer_type">
														<small class="req text-danger">* </small>
														<label for="taxpayer_type" class="control-label">TaxPayer Type</label>
														<input type="text"  name="taxpayer_type" readonly id="taxpayer_type" class="form-control"  value="" >
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group" app-field-wrapper="date_of_reg">
														<small class="req text-danger">* </small>
														<label for="date_of_reg" class="control-label">Date Of Registration</label>
														<input type="text"  name="date_of_reg" readonly id="date_of_reg" class="form-control"  value="" >
													</div>
												</div>
												<div class="col-md-12">
													<div class="form-group" app-field-wrapper="status_gst">
														<small class="req text-danger">* </small>
														<label for="status_gst" class="control-label">Status</label>
														<input type="text"  name="status_gst" readonly id="status_gst" class="form-control"  value="" >
													</div>
												</div>
												<div class="col-md-12">
													<input type="checkbox" name="is_primary" id="is_primary"  value="Y" >
													<b for="is_primary" >Is Primary</b>
												</div>
												
											</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
											<button type="button" id="GSTSubmit" onclick="addGSTDetails()" class="btn btn-primary">Save changes</button>
										</div>
									</div>
								</div>
							</div>
							<div class="modal fade Account_List" id="Account_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
										<div class="modal-header" style="padding:5px 10px;">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											<h4 class="modal-title">Account List</h4>
										</div>
										
										<div class="modal-body" style="padding:0px 5px !important">
											<div class="col-md-5">
												<?php if (has_permission_new('BrokerMaster', '', 'export')) {
												?>
												<a class="btn btn-default buttons-excel buttons-html2" tabindex="0"
												aria-controls="table-trial_bal_report" href="#" id="caexcel"
												style="float: left ! important;"><span>Export to Excel</span></a>
												<?php } ?>
												
												<?php if (has_permission_new('BrokerMaster', '', 'print')) {
												?>
												<button class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</button>
												<?php } ?>
											</div>
											<div class="table-Account_List tableFixHead2">
												<table class="tree table table-striped table-bordered table-Account_List tableFixHead2" id="table_Account_List" width="100%">
													<thead>
														<tr style="display:none;">
															<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
														</tr>
														<tr>
															<th id="sl" style="text-align:left;">AccountID <span class="up_starting">  &#8593;</span><span class="down" style="display:none;"> &#8593;</span><span class="up" style="display:none;"> &#8595;</span></th>
															<th style="text-align:left;">Business Name</th>
															<th style="text-align:left;">User Type</th>
															<th style="text-align:left;">Contact Person</th>
															<th style="text-align:left;">Mobile No</th>
															<th style="text-align:left;">Pan No</th>
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
		function VerifyPanNo(){
			$('#verifyPan').click();
		}
		$('#verifyPan').click(function(){
			var AccountID = $('#AccountID').val();
			var Pan = $('#Pan').val();
			
			if(AccountID == '' || AccountID == null ){
				alert('Please enter account ID');
			}else if(Pan.length != 10){
				alert('Enter Valid Pan Number');
				$('#isValidPan').val('N');
				$('#check').css('display','none');
				$('#verifyPan').css('display','');
			}else{
				$.ajax({
					url:"<?php echo admin_url(); ?>clients/VerifyPan",
					method:"POST",
					dataType:"JSON",
					data:{
						AccountID:AccountID,
						Pan:Pan,
					},
					success:function(data){
						if(data == false){
							alert('Pan already exists');
							$('#isValidPan').val('N');
							$('#check').css('display','none');
						}else if(data.success == false || data.success == null){
							alert('Enter valid Pan card number');
							$('#isValidPan').val('N');
							$('#check').css('display','none');
							$('#verifyPan').css('display','');
						}else{
							// $('#verifyPan').css('display','none');
							alert('Pan Verified Successfully!');
							$('#isValidPan').val('Y');
							$('#check').css('display','');
							$('#verifyPan').css('display','none');
							
						}
					}
				}); 
			}
		});
	</script>
	<script>
		$(document).ready(function(){
			$('.saveBtn').show();
			$('.updateBtn').hide();
			$('#state_name').val('');
			$('#city_name').val('');
			
			$('#div_tds_section').hide();
			$('#div_tds_rate').hide();
			
			$.ajax({
				url : "<?php echo admin_url(); ?>clients/GetState",
				type: "post",
				data: {
				},
				beforeSend: function () {
					$('select[name=state]').val('').selectpicker('refresh');
				},
				success: function(data){
					$('select[name=state]').append(data).selectpicker('refresh');
				}
			});
		});
		
		$('#istds').on('change', function() {
			var isTdsVal = $(this).val();
			if(isTdsVal == "1") {
				$('#div_tds_section').show();
				$('#div_tds_rate').show();
				} else {
				$('#div_tds_section').hide();
				$('#div_tds_rate').hide();
				$('select[name=tds_section]').val('').selectpicker('refresh');
				$('select[name=tds_rate]').val('').selectpicker('refresh');
				// $("#tds_rate").val('');
			}
		});
		
		$('#tds_section').on('change', function() {
			var tdsSection = $(this).val();
			$.ajax({
				type: 'POST',
				url : "<?php echo admin_url(); ?>clients/GetTDSSectionRate",
				data: {tds_section_code: tdsSection},
				dataType:'json',
				beforeSend: function () {
					$('select[name=tds_rate]').val('').selectpicker('refresh');
					// $("#tds_rate").val('');
				},
				success: function(data) {
					// $("#tds_rate").val(data.rate);
					$("#tds_rate").find('option').remove();
					$("#tds_rate").selectpicker("refresh");
					$("#tds_rate").append(new Option('', 'Select TDS Rate'));
					for (var i = 0; i < data.length; i++) {
						$("#tds_rate").append(new Option(data[i].rate, data[i].rate));
					}
					$('.selectpicker').selectpicker('refresh');
				}
			});
		});
	</script>
	
	<script>	
		$('#city').on('change', function() {
			var CityID = $(this).val();
			var url = "<?php echo admin_url(); ?>clients/GetTaluka";
			$.ajax({
				type: 'POST',
				url:url,
				data: {CityID: CityID},
				dataType:'json',
				beforeSend: function () {
					$('select[name=subdist]').val('').selectpicker('refresh');
				},
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
		
	</script>
<script>
$(document).ready(function(){
	$('.updateBtn').hide();
	$('.updateBtn2').hide();
	$('.updateGstBtn').hide();
	$("#AccountID").dblclick(function(){
		$('#Account_List').modal('show');
		$('#myInput1').focus();
		var AccountID = "";
		$.ajax({
			url:"<?php echo admin_url(); ?>clients/BrokerListPopUp",
			//dataType:"JSON",
			method:"POST",
			cache: false,
			data:{AccountID:AccountID,},
			success:function(data){
				if(empty(data)){
				}else{
					$("#ListTableBody").html(data);
					$('.get_AccountID').click(function(){ 
						AccountID = $(this).attr("data-id");
						$.ajax({
							url:"<?php echo admin_url(); ?>clients/GetAccountDetailByID",
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
									}else{
									var PlantID = $('#PlantID').val();
									if(PlantID ==data.PlantID ){
										if(data.CustomerType == "2"){
											$('#AccountID').val(data.AccountID);
											$('#short_code').val(data.ShortCode);
											$('#AccoountName').val(data.company);
											$('#firstname').val(data.firstname);
											$('#lastname').val(data.lastname);
											$('#phonenumber').val(data.phonenumber);
											$('#altphonenumber').val(data.altphonenumber);
											$('#email').val(data.email);
											$('#state').val(data.state).selectpicker('refresh');
											// $address = data.house+', '+data.street+', '+data.loc+', '+data.po;
											$('#house').val(data.house);
											$('#street').val(data.street);
											$('#loc').val(data.loc);
											$('#po').val(data.po);
											$('#vtc').val(data.vtc);
											$('#zip').val(data.zip);
											if(data.pan_verified_date == null){
												$('#verifyPan').css('display','none');
												$('#check').css('display','none');
												}else if(data.pan_verified_date != null){
												$('#verifyPan').css('display','none');
												$('#check').css('display','');
											}
											
											if(data.Pan)
            								{
            								    $('#Pan').prop('readonly', true);
            								    $('#isValidPan').val('Y');
            								    $('#Pan').val(data.Pan);
            								    $('#verifyPan').css('display','none');
												$('#check').css('display','');
            								}
            								else
            								{
            								    $('#Pan').prop('readonly', false);
            								    $('#isValidPan').val('N');
            								    $('#verifyPan').css('display','');
												$('#check').css('display','none');
            								}
											
											$('select[name=active]').val(data.active);
											$('.selectpicker').selectpicker('refresh');
											
											$('select[name=is_approve]').val(data.is_approve);
								            $('.selectpicker').selectpicker('refresh');
								
											$('select[name=IsKirtiOneAccess]').val(data.IsKirtiOneAccess);
											$('.selectpicker').selectpicker('refresh');
											
											$('select[name=istds]').val(data.istds);
											$('.selectpicker').selectpicker('refresh');
											
											if(data.istds == '1') {
												$('#div_tds_section').show();
												$('#div_tds_rate').show();
											}
											
											$('select[name=tds_section]').val(data.TdsSection);
											$('.selectpicker').selectpicker('refresh');
											
											$("#tds_rate").find('option').remove();
											$("#tds_rate").selectpicker("refresh");
											$("#tds_rate").append(new Option('', 'Select TDS Rate'));
											for (var i = 0; i < data.TDSList.length; i++) {
												$("#tds_rate").append(new Option(data.TDSList[i].rate, data.TDSList[i].rate));
											}
											$('.selectpicker').selectpicker('refresh');
											
											$('select[name=tds_rate]').val(data.rate);
											$('.selectpicker').selectpicker('refresh');
											
											if(data.TcsStartDate == null || data.TcsStartDate == '' || data.TcsStartDate == "0000-00-00 00:00:00"){
												$('#TcsStartDate1').val('');
												}else{
												var date = data.TcsStartDate.substring(0, 10)
												var date_new = date.split("-").reverse().join("/");
												$('#TcsStartDate1').val(date_new);
											}
											var date = data.StartDate.substring(0, 10)
											var date_new = date.split("-").reverse().join("/");
											$('#StartDate').val(date_new);
											
											let CityList = data.CityList;
											$("#city").children().remove();
											for (var i = 0; i < CityList.length; i++) {
												$("#city").append('<option value="'+CityList[i]["id"]+'">'+CityList[i]["city_name"]+'</option>');
											}
											$('.selectpicker').selectpicker('refresh');
											
											$('#city').selectpicker('val', data.dist);
											$('.selectpicker').selectpicker('refresh');
											
											let TalukaList = data.TalukaList;
											$("#subdist").children().remove();
											for (var i = 0; i < TalukaList.length; i++) {
												$("#subdist").append('<option value="'+TalukaList[i]["id"]+'">'+TalukaList[i]["TalukaName"]+'</option>');
											}
											$('.selectpicker').selectpicker('refresh');
											
											$('#subdist').selectpicker('val', data.subdist);
											$('.selectpicker').selectpicker('refresh');
											$("#BankTableBody").html(data.BankData);
											$("#GstTableBody").html(data.GSTList);
											$('.saveBtn').hide();
											$('.updateBtn').show();
											$('.saveBtn2').hide();
											$('.updateBtn2').show();
											$('.updateGstBtn').show();
										}else if(data.CustomerType == "1"){
											alert('This Account is use for as farmer account');
											$('#AccountID').val('');
											$('#phonenumber').val('');
										}else if(data.CustomerType == "3"){
											alert('This Account is use for as Trader account');
											$('#AccountID').val('');
											$('#phonenumber').val('');
										}
									}else{
										alert('This Account is use for other Plant');
									}
								}
							}
							
						})  
						$('#Account_List').modal('hide');
					});
				}
			}
		});
	});
//=================== AccountID Typing Validation ==============================
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
			
//========================= GST Type Typing Validation =========================
	$("#vat").keypress(function (e) {
		var keyCode = e.keyCode || e.which;
		if(keyCode == ""){
			$("#lblError").html("");
			}else{
			var regex = /^[A-Za-z0-9]+$/;
			var isValid = regex.test(String.fromCharCode(keyCode));
			return isValid;
		}
	});
	
//======================= Pan Number Typing Validation =========================
	$("#Pan").keypress(function (e) {
		var keyCode = e.keyCode || e.which;
		if(keyCode == ""){
			$("#lblError").html("");
			}else{
			var regex = /^[A-Za-z0-9]+$/;
			var isValid = regex.test(String.fromCharCode(keyCode));
			return isValid;
		}
	});
});
		
	$('#ifsc_code').blur(function(){
		var ifsc_code = $('#ifsc_code').val();
		$.ajax({
			url:"<?php echo admin_url(); ?>clients/fetchBankDetailsFromIFSC",
			method:"POST",
			dataType:'json',
			data:{ifsc_code:ifsc_code},
			beforeSend: function () {
				$('.searchh6').css('display','block');
				
				$('.searchh6').css('color','blue');
			},
			complete: function () {
				$('.searchh6').css('display','none');
			},
			success:function(data){
				// var data1 = JSON.parse(data);
				if(data == "Not Found"){
					alert("Enter valid IFSC Code");
					$('#bank_name').prop("readonly", false);
					$('#bank_branch').prop("readonly", false);
					$('#bank_name').val("");
					$('#bank_branch').val("");
					}else{
					$('#bank_name').prop("readonly", true);
					$('#bank_branch').prop("readonly", true);
					$('#bank_name').val(data.BANK);
					$('#bank_branch').val(data.BRANCH);
				}
			}
		});
	});
		
	function validateAccountNumber(){
		var account_number = $('#account_number').val();
		var reaccount_number = $('#reaccount_number').val();
		if(account_number == reaccount_number){
			$('#account_number_error').text('');
			$('#bankSubmit').prop('disabled', false);
			return true;
			}else{
			$('#account_number_error').text('Account number does not match');
			$('#account_number_error').css('color','red');
			$('#bankSubmit').prop('disabled', true);
			return false;
		}
	}
		
	$('#account_number').blur(function(){
		var account_number = $('#account_number').val();
		$.ajax({
			url:"<?php echo admin_url(); ?>clients/CheckBankAccount",
			method:"POST",
			dataType:'json',
			data:{account_number:account_number},
			beforeSend: function () {
				$('.searchh6').css('display','block');
				
				$('.searchh6').css('color','blue');
			},
			complete: function () {
				$('.searchh6').css('display','none');
			},
			success:function(data){
				if(data == false){
					alert("This Account Number Is Already Registered");
					$('#account_number').val('');
					$('#account_number').focus();
				}
			}
		});
	});
	$('#reaccount_number').blur(function(){
		var reaccount_number = $('#reaccount_number').val();
		var ifsc_code = $('#ifsc_code').val();
		if(validateAccountNumber() == false){
			$('#account_number_error').text('Account number does not match');
			$('#bankSubmit').prop('disabled', true);
			}else{
			$.ajax({
				url:"<?php echo admin_url(); ?>clients/verifyBankAccount",
				method:"POST",
				dataType:'json',
				data:{reaccount_number:reaccount_number,ifsc_code:ifsc_code},
				beforeSend: function () {
					$('.searchh6').css('display','block');
					
					$('.searchh6').css('color','blue');
				},
				complete: function () {
					$('.searchh6').css('display','none');
				},
				success:function(data){
					if(data.success == false){
						alert("Bank account not verified");
						$('#account_number_error').text('Bank account not verified');
						$('#account_number_error').css('color','red');
						$("#is_act_validate").removeAttr("disabled");
						$('#bankSubmit').prop('disabled', true);
					}else{
						$('#account_number_error').text('Bank Account verified successfully');
						$('#account_number_error').css('color','green');
						$('#is_act_validate').prop('checked', false);
						$("#is_act_validate").attr("disabled");
						$('#bankSubmit').prop('disabled', false);
						$('#account_name').val(data.data.full_name);
					}
				}
			});
		}
	});
	$('#is_act_validate').change(function(){
	    var isChecked = $('#is_act_validate').prop('checked');
		if (isChecked) {
			$('#bankSubmit').prop('disabled', false);
		}else{
		    $('#bankSubmit').prop('disabled', true);
		}
	})
	$('#gst_no').blur(function(){
		var gst_no = $('#gst_no').val();
		$.ajax({
			url:"<?php echo admin_url(); ?>clients/verifyGSTNNumber",
			method:"POST",
			dataType:'json',
			data:{gst_no:gst_no},
			beforeSend: function () {
				$('.searchh6').css('display','block');
				$('.searchh6').css('color','blue');
			},
			complete: function () {
				$('.searchh6').css('display','none');
			},
			success:function(data){
				if(data.success == false){
					alert("GST No. Is Not Verified");
					$('#GSTSubmit').prop('disabled', true);
					$('#gst_no').val('');
					$('#business_name').val('');
					$('#state_gst').val('');
					$('#address_gst').val('');
					$('#constitution_business').val('');
					$('#taxpayer_type').val('');
					$('#date_of_reg').val('');
					$('#status_gst').val('');
					$('#is_primary').prop('checked', false);
					}else{
					$('#GSTSubmit').prop('disabled', false);
					$('#business_name').val(data.data.details.business_name);
					$('#state_gst').val(data.data.details.state_jurisdiction);
					$('#address_gst').val(data.data.details.contact_details.principal.address);
					$('#constitution_business').val(data.data.details.constitution_of_business);
					$('#taxpayer_type').val(data.data.details.taxpayer_type);
					$('#date_of_reg').val(data.data.details.date_of_registration);
					$('#status_gst').val(data.data.details.gstin_status);
					$('#is_primary').prop('checked', false);
				}
			}
		});
	});
		
	function openBankModal(){
		$('#accountFor').val('');
		$('#ifsc_code').val('');
		$('#bank_name').val('');
		$('#bank_branch').val('');
		$('#account_number').val('');
		$('#reaccount_number').val('');
		$('#account_number_error').html('');
		$('#account_name').val('');
		$('#cheque_image').val('');
		$('#is_primarybank').prop('checked', false);
		$("#is_act_validate").attr("disabled");
		$('#bankModal').modal('show');
	}
	function openGstModal(){
		$('#gst_no').val('');
		$('#business_name').val('');
		$('#state_gst').val('');
		$('#address_gst').val('');
		$('#constitution_business').val('');
		$('#taxpayer_type').val('');
		$('#date_of_reg').val('');
		$('#status_gst').val('');
		$('#is_primary').prop('checked', false);
		$('#GSTModal').modal('show');
	}
	function addGSTDetails() {
		var gst_no = $('#gst_no').val();
		var business_name =	$('#business_name').val();
		var state_gst = $('#state_gst').val();
		var address_gst = $('#address_gst').val();
		var constitution_business = $('#constitution_business').val();
		var taxpayer_type = $('#taxpayer_type').val();
		var date_of_reg = $('#date_of_reg').val();
		var status_gst = $('#status_gst').val();
		var AccountID = $('#AccountID').val();
		
		var isChecked = $('#is_primary').prop('checked');
		if (isChecked) {
			var is_primary = $('#is_primary').val();
			} else {
			var is_primary = '';
		}
		
		if(gst_no == ''){
			alert('Select Enter GST Number');
		}
		else if(business_name == ''){
			alert('Business Name Cannot Be Null');
		}
		else if(state_gst == ''){
			alert('State Cannot Be Null');
		}
		else if(address_gst == ''){
			alert('Address Cannot Be Null');
		}
		else if(constitution_business == ''){
			alert('Constitution Of Business Cannot Be Null');
		}  
		else if(taxpayer_type == ''){
			alert('TaxPayer Type Cannot Be Null');
		}  
		else if(date_of_reg == ''){
			alert('Date Of Registration Cannot Be Null');
		}   
		else if(status_gst == ''){
			alert('Status Cannot Be Null');
			} else{
			
			var formData = new FormData();
			formData.append('gst_no', gst_no);
			formData.append('business_name', business_name);
			formData.append('state_gst', state_gst);
			formData.append('address_gst', address_gst);
			formData.append('constitution_business', constitution_business);
			formData.append('taxpayer_type', taxpayer_type);
			formData.append('date_of_reg', date_of_reg);
			formData.append('status_gst', status_gst);
			formData.append('is_primary', is_primary);
			formData.append('AccountID', AccountID);
			
			$.ajax({
				url: "<?php echo admin_url(); ?>clients/addGSTDetails",
				method: "POST",
				dataType: 'json',
				data: formData,
				contentType: false,
				processData: false,
				beforeSend: function () {
					$('.searchh7').css('display','block');
					$('.searchh7').css('color','blue');
				},
				complete: function () {
					$('.searchh7').css('display','none');
				},
				success: function (data) {
					if (data == true) {
						alert('GST Details successfully Saved')
						$('#GSTModal').modal('hide');
						$('#AccountID').val(AccountID);
						$('#AccountID').blur();
						} else {
						alert('Something went wrong')
					}
					// console.log(data);
				}
			});
		}
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
		if(accountFor == ''){
			alert('Select Account For');
		}
		else if(ifsc_code == ''){
			alert('Enter IFSC Code');
		}
		else if(account_number == ''){
			alert('Enter Bank Account Number');
		}
		else if(reaccount_number == ''){
			alert('Confirm Account Number');
		}
		else if(account_name == ''){
			alert('Enter Account Name');
		}        
		else if( document.getElementById("cheque_image").files.length == 0 ){
			alert('Attatch Cheque Image');
			} else{
			var reader = new FileReader();
			
			if(fileInput.files.length > 0){
				reader.readAsDataURL(fileInput.files[0]);
				}else{
				
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
				formData.append('cheque_image', 'NA');
				
				$.ajax({
					url: "<?php echo admin_url(); ?>clients/addBankDetails",
					method: "POST",
					dataType: 'json',
					data: formData,
					contentType: false,
					processData: false,
					beforeSend: function () {
						$('.searchh7').css('display','block');
						$('.searchh7').css('color','blue');
					},
					complete: function () {
						$('.searchh7').css('display','none');
					},
					success: function (data) {
						if (data == true) {
							alert('Bank Details Successfully Saved');
							$('#bankModal').modal('hide');
							$('#AccountID').val(AccountID);
							$('#AccountID').blur();
							} else {
							alert('Something went wrong')
						}
						// console.log(data);
					}
				});
			}
			reader.onload = function (e) {
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
					beforeSend: function () {
						$('.searchh7').css('display','block');
						$('.searchh7').css('color','blue');
					},
					complete: function () {
						$('.searchh7').css('display','none');
					},
					success: function (data) {
						if (data == true) {
							alert('Bank Details Successfully Saved');
							$('#bankModal').modal('hide');
							$('#AccountID').val(AccountID);
							$('#AccountID').blur();
							} else {
							alert('Something went wrong')
						}
						// console.log(data);
					}
				});
			};
		}
	}
//================== Form Reset ================================================
    function ResetForm()
    {
        $('#verifyPan').css('display','');
		$('#isValidPan').val('N');
		$('#check').css('display','none');
		$('#AccountID').val('');
		$('#short_code').val('');
		$('#AccoountName').val('');
		$('#firstname').val('');
		$('#lastname').val('');
		$('#phonenumber').val('');
		$('#altphonenumber').val(''); 
		$('#email').val('');
		$('select[name=AccountType]').val('');
		$('.selectpicker').selectpicker('refresh');
		$('#state').val('').selectpicker('refresh');
		$("#city").children().remove();
		$('.selectpicker').selectpicker('refresh');
		$("#subdist").children().remove();
		$('.selectpicker').selectpicker('refresh');
		$('#house').val('');
		$('#street').val('');
		$('#loc').val('');
		$('#vtc').val('');
		$('#po').val('');
		$('#zip').val('');
		$('#kms').val('');
		$('#Pan').val('');
		$('#Pan').prop('readonly', false);
		$('select[name=active]').val('0');
		$('select[name=IsKirtiOneAccess]').val('N');
		$('select[name=istds]').val('0');
		$('select[name=is_approve]').val('0');
		$('select[name=tds_section]').val('');
		$('select[name=tds_rate]').val('');
		$("#tds_rate").find('option').remove();
		$('.selectpicker').selectpicker('refresh');
		$('#div_tds_section').hide();
		$('#div_tds_rate').hide();
		var today = new Date();
		var dd = String(today.getDate()).padStart(2, '0');
		var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
		var yyyy = today.getFullYear();
		today = dd + '/' + mm + '/' + yyyy;
		$('#TcsStartDate1').val(today);
		$('#StartDate').val(today);
		
		$('select[name=active]').val('1');
		$('.selectpicker').selectpicker('refresh');
		
		$('.saveBtn').show();
		$('.updateBtn').hide();
		$('.saveBtn2').show();
		$('.updateBtn2').hide();
		$('.updateGstBtn').hide();
		$("#BankTableBody").html('');
		$("#GstTableBody").html('');
    }
//=================== Empty and open create mode ===============================
	$("#AccountID").focus(function(){
		ResetForm();
	});
		
//===================== Cancel selected data ===================================
	$(".cancelBtn").click(function(){
		ResetForm();
	});
		
//=========================== On Blur Broker Get All Data ======================
	$('#AccountID').blur(function(){ 
		AccountID = $(this).val();
		if(AccountID == ''){
		}else{
			$.ajax({
				url:"<?php echo admin_url(); ?>clients/GetAccountDetailByID",
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
					}else{
						var PlantID = $('#PlantID').val();
						if(PlantID ==data.PlantID ){
							if(data.CustomerType == "2"){
								$('#AccountID').val(data.AccountID);
								$('#short_code').val(data.ShortCode);
								$('#AccoountName').val(data.company);
								$('#firstname').val(data.firstname);
								$('#lastname').val(data.lastname);
								$('#phonenumber').val(data.phonenumber);
								$('#altphonenumber').val(data.altphonenumber);
								$('#email').val(data.email);
								$('#state').val(data.state).selectpicker('refresh');
								// $address = data.house+', '+data.street+', '+data.loc+', '+data.po;
								$('#house').val(data.house);
								$('#street').val(data.street);
								$('#loc').val(data.loc);
								$('#po').val(data.po);
								$('#vtc').val(data.vtc);
								$('#zip').val(data.zip);
								if(data.pan_verified_date == null){
									$('#verifyPan').css('display','none');
									$('#check').css('display','none');
									}else if(data.pan_verified_date != null){
									$('#verifyPan').css('display','none');
									$('#check').css('display','');
								}
								if(data.Pan)
								{
								    $('#Pan').prop('readonly', true);
								    $('#isValidPan').val('Y');
								    $('#Pan').val(data.Pan);
								    $('#verifyPan').css('display','none');
									$('#check').css('display','');
								}
								else
								{
								    $('#Pan').prop('readonly', false);
								    $('#isValidPan').val('N');
								    $('#verifyPan').css('display','');
									$('#check').css('display','none');
								}
								
								
								$('select[name=active]').val(data.active);
								$('.selectpicker').selectpicker('refresh');
								
								$('select[name=is_approve]').val(data.is_approve);
								$('.selectpicker').selectpicker('refresh');
								
								$('select[name=IsKirtiOneAccess]').val(data.IsKirtiOneAccess);
								$('.selectpicker').selectpicker('refresh');
								
								$('select[name=istds]').val(data.istds);
								$('.selectpicker').selectpicker('refresh');
								
								if(data.istds == '1') {
									$('#div_tds_section').show();
									$('#div_tds_rate').show();
								}
								
								$('select[name=tds_section]').val(data.TdsSection);
								$('.selectpicker').selectpicker('refresh');
								
								$("#tds_rate").find('option').remove();
								$("#tds_rate").selectpicker("refresh");
								$("#tds_rate").append(new Option('', 'Select TDS Rate'));
								for (var i = 0; i < data.TDSList.length; i++) {
									$("#tds_rate").append(new Option(data.TDSList[i].rate, data.TDSList[i].rate));
								}
								$('.selectpicker').selectpicker('refresh');
								
								$('select[name=tds_rate]').val(data.rate);
								$('.selectpicker').selectpicker('refresh');
								
								if(data.TcsStartDate == null || data.TcsStartDate == '' || data.TcsStartDate == "0000-00-00 00:00:00"){
									$('#TcsStartDate1').val('');
									}else{
									var date = data.TcsStartDate.substring(0, 10)
									var date_new = date.split("-").reverse().join("/");
									$('#TcsStartDate1').val(date_new);
								}
								var date = data.StartDate.substring(0, 10)
								var date_new = date.split("-").reverse().join("/");
								$('#StartDate').val(date_new);
								
								$('select[name=active]').val(data.active);
								$('.selectpicker').selectpicker('refresh');
								
								let CityList = data.CityList;
								$("#city").children().remove();
								for (var i = 0; i < CityList.length; i++) {
									$("#city").append('<option value="'+CityList[i]["id"]+'">'+CityList[i]["city_name"]+'</option>');
								}
								$('.selectpicker').selectpicker('refresh');
								
								$('#city').selectpicker('val', data.dist);
								$('.selectpicker').selectpicker('refresh');
								
								let TalukaList = data.TalukaList;
								$("#subdist").children().remove();
								for (var i = 0; i < TalukaList.length; i++) {
									$("#subdist").append('<option value="'+TalukaList[i]["id"]+'">'+TalukaList[i]["TalukaName"]+'</option>');
								}
								$('.selectpicker').selectpicker('refresh');
								
								$('#subdist').selectpicker('val', data.subdist);
								$('.selectpicker').selectpicker('refresh');
								$("#BankTableBody").html(data.BankData);
								$("#GstTableBody").html(data.GSTList);
								$('.saveBtn').hide();
								$('.updateBtn').show();
								$('.saveBtn2').hide();
								$('.updateBtn2').show();
								$('.updateGstBtn').show();
							}else if(data.CustomerType == "1"){
								alert('This Account is use for as farmer account');
								$('#AccountID').val('');
								$('#phonenumber').val('');
							}else if(data.CustomerType == "3"){
								alert('This Account is use for as Trader account');
								$('#AccountID').val('');
								$('#phonenumber').val('');
							}
						}else{
							alert('This Account is use for other Plant');
						}
					}
				}
			})
		}
	})
//========================= Save New Broker ====================================
	$('.saveBtn').on('click',function(){ 
		groups_in = 2;
		location_type = 1;
		CustomerType = 2;
		route = 1;
		bill_till_bal = "N";
		ActSalestype = "Sales";
		AccountID = $('#AccountID').val();
		AccoountName = $('#AccoountName').val();
		firstname = $('#firstname').val();
		lastname = $('#lastname').val();
		phonenumber = $('#phonenumber').val();
		altphonenumber = $('#altphonenumber').val();
		email = $('#email').val();
		
		state = $('#state :selected').val();
		city = $('#city :selected').val();
		taluka = $('#subdist :selected').val();
		house = $('#house').val();
		street = $('#street').val();
		loc = $('#loc').val();
		vtc = $('#vtc').val();
		po = $('#po').val();
		zip = $('#zip').val();
		kms = $('#kms').val();
		Pan = $('#Pan').val();
		isValidPan = $('#isValidPan').val();
		istcs = $('#istcs').val();
		IsKirtiOneAccess = $('#IsKirtiOneAccess').val();
		istds = $('#istds').val();
		tds_section = $('#tds_section').val();
		tds_rate = $('#tds_rate').val();
		TcsStartDate1 = $('#TcsStartDate1').val();
		active = $('#active').val();
		StartDate = $('#StartDate').val();
		KirtiApproval = $('#is_approve').val();
		
		if(AccountID == ''){
			alert('please enter AccountID');
			$('#AccountID').focus();
		}else if(AccoountName == ''){
			alert('please enter Account Name');
			$('#AccountName').focus();
		}else if(firstname == ''){
			alert('please enter First Name');
			$('#AccountName').focus();
		}else if(lastname == ''){
			alert('please enter Last Name');
			$('#AccountName').focus();
		}else if(state == ''){
			alert('please select State');
			$('#state').focus();
		}else if(city == ''){
			alert('please select City');
			$('#city').focus();
		}else if(phonenumber == ''){
			alert('please  enter mobile number');
			$('#phonenumber').focus();
		}else if(!$('#phonenumber').val().match('[0-9]{10}') && $('#phonenumber').val() !== ""){
			alert('Enter valid Mobile number');
			$('#phonenumber').focus();
		}else if(!$('#Pan').val().match('[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}') || $('#Pan').val() == ""){
			alert('Enter valid PAN number');
			$('#Pan').focus();
		}else if(isValidPan == 'N'){
			alert('Pan not verified');
		}else {
			groups_in = 2;
			location_type = 1;
			route = 1;
			bill_till_bal = "N";
			ActSalestype = "Sales";
			$.ajax({
				url:"<?php echo admin_url(); ?>clients/SaveAccountID",
				dataType:"JSON",
				method:"POST",
				data:{AccountID:AccountID,AccoountName:AccoountName,firstname:firstname,lastname:lastname,phonenumber:phonenumber,IsKirtiOneAccess:IsKirtiOneAccess,
					altphonenumber:altphonenumber,email:email,state:state,city:city,taluka:taluka,house:house,street:street,loc:loc,vtc:vtc,po:po,
					zip:zip,Pan:Pan,istcs:istcs,istds:istds,tds_section:tds_section,tds_rate:tds_rate,TcsStartDate1:TcsStartDate1,active:active,StartDate:StartDate,
					groups_in:groups_in,location_type:location_type,route:route,bill_till_bal:bill_till_bal,ActSalestype:ActSalestype,CustomerType:CustomerType,KirtiApproval:KirtiApproval
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
						//alert_float('success', 'Record created successfully...');
						alert('Record created successfully...');
						ResetForm();
					}else{
						alert_float('warning', 'Something went wrong...');
					}
				}
			});   
		}
	});
//========================== Update Exiting Broker =============================
	$('.updateBtn').on('click',function(){ 
		AccountID = $('#AccountID').val();
		AccoountName = $('#AccoountName').val();
		firstname = $('#firstname').val();
		lastname = $('#lastname').val();
		phonenumber = $('#phonenumber').val();
		altphonenumber = $('#altphonenumber').val();
		email = $('#email').val();
		
		state = $('#state :selected').val();
		city = $('#city :selected').val();
		taluka = $('#subdist :selected').val();
		house = $('#house').val();
		street = $('#street').val();
		loc = $('#loc').val();
		vtc = $('#vtc').val();
		po = $('#po').val();
		
		zip = $('#zip').val();
		kms = $('#kms').val();
		Pan = $('#Pan').val();
		isValidPan = $('#isValidPan').val();
		istcs = $('#istcs').val();
		IsKirtiOneAccess = $('#IsKirtiOneAccess').val();
		istds = $('#istds').val();
		tds_section = $('#tds_section').val();
		tds_rate = $('#tds_rate').val();
		TcsStartDate1 = $('#TcsStartDate1').val();
		active = $('#active').val();
		StartDate = $('#StartDate').val();
		KirtiApproval = $('#is_approve').val();
		
		if(AccountID == ''){
			alert('please enter AccountID');
			$('#AccountID').focus();
		}else if(AccoountName == ''){
			alert('please enter Account Name');
			$('#AccountName').focus();
		}else if(firstname == ''){
			alert('please enter First Name');
			$('#AccountName').focus();
		}else if(lastname == ''){
			alert('please enter Last Name');
			$('#AccountName').focus();
		}else if(state == ''){
			alert('please select State');
			$('#state').focus();
		}else if(city == ''){
			alert('please select City');
			$('#city').focus();
		}else if(phonenumber == ''){
			alert('please  enter mobile number');
			$('#phonenumber').focus();
		}else if(!$('#phonenumber').val().match('[0-9]{10}') && $('#phonenumber').val() == ""){
			alert('Enter valid Mobile number');
			$('#phonenumber').focus();
		}else if(!$('#Pan').val().match('[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}') || $('#Pan').val() == ""){
			alert('Enter valid PAN number');
			$('#Pan').focus();
		}else if(isValidPan == 'N'){
			alert('Pan not verified');
		}else {
			$.ajax({
				url:"<?php echo admin_url(); ?>clients/UpdateAccountID", 
				dataType:"JSON",
				method:"POST",
				data:{AccountID:AccountID,AccoountName:AccoountName,firstname:firstname,lastname:lastname,phonenumber:phonenumber,
				street:street,loc:loc,vtc:vtc,po:po,altphonenumber:altphonenumber,email:email,state:state,city:city,taluka:taluka,house:house,IsKirtiOneAccess:IsKirtiOneAccess,
					zip:zip,Pan:Pan,istcs:istcs,istds:istds,tds_section:tds_section,tds_rate:tds_rate,TcsStartDate1:TcsStartDate1,active:active,StartDate:StartDate,KirtiApproval:KirtiApproval
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
						//alert_float('success', 'Record updated successfully...');
						alert('Record updated successfully...');
						ResetForm();
					}else{
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
		
	$('#shipping_state').on('change', function() {
		var StateID = $(this).val();
		//alert(roleid);
		var url = "<?php echo base_url(); ?>admin/clients/GetCity";
		jQuery.ajax({
			type: 'POST',
			url:url,
			data: {StateID: StateID},
			dataType:'json',
			success: function(data) {
				$("#shipping_city").find('option').remove();
				$("#shipping_city").selectpicker("refresh");
				for (var i = 0; i < data.length; i++) {
					$("#shipping_city").append(new Option(data[i].city_name, data[i].id));
				}
				$('.selectpicker').selectpicker('refresh');
			}
		});
	});
		
	$('#AccountID').on('change', function() {
		var AccountID = $(this).val();
		if(!$('#AccountID').val().match('[1-9]{1}[0-9]{9}') && $('#AccountID').val() !== ""){
			alert('Enter valid 10-digit AccountID');
			$('#AccountID').focus();
		}
		$('#phonenumber').val(AccountID);
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
	
	<script type="text/javascript">
		function printPage() {
			var html_filter_name = $('.report_for').html();
			var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
			var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">' + document.getElementsByTagName('table')[2].innerHTML + '</table>';
			var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
			heading_data += '<tr>';
			heading_data += '<td style="text-align:center;"colspan="3">Account List</td>';
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
				url:"<?php echo admin_url(); ?>clients/export_TraderMaster",
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