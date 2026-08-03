<?php defined('BASEPATH') or exit ('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-10">
                <div class="panel_s">
                    <div class="panel-body">

                        <?php //echo form_open('admin/Accounts_master/',array('id'=>'accounting_head'));  ?>
                        
                        <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Transport Master</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="searchh2" style="display:none;">Please wait while fetching data.</div>
                                <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                                <div class="searchh4" style="display:none;">Please wait while updating data.</div>
                            </div>


                            <input type="hidden" id="user_id" value="<?php echo $_SESSION["staff_user_id"] ?>">

                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="TransportID">
                                    <small class="req text-danger">* </small>
                                    <label for="TransportID" class="control-label">Transporter ID</label>
                                    <input type="text" id="TransportID" name="TransportID" class="form-control"
                                        value="">
                                    <input type="hidden" id="Transport_ID" name="Transport_ID" class="form-control"
                                        value="">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="transportername">
                                    <small class="req text-danger">* </small>
                                    <label for="transportername" class="control-label">Transporter Name</label>
                                    <input type="text" id="TransportName" name="TransportName" class="form-control"
                                        value="">
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group" app-field-wrapper="address">
                                    <small class="req text-danger">* </small>
                                    <label for="address" class="control-label">address</label>
                                    <input type="text" id="address" name="address" class="form-control" rows="4"
                                        value="">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <!-- <small class="req text-danger">* </small> -->
                                    <label for="country" class="control-label">Country</label>
                                    <select class="selectpicker display-block" data-width="100%" id="country"
                                        name="country" readonly>
                                        <option value="<?php echo $country[101]['country_id']; ?>">
                                            <?php echo $country[101]['short_name']; ?>
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="state">
                                    <small class="req text-danger">* </small>
                                    <label for="state" class="control-label">State</label>
                                    <select name="state" id="state" class="selectpicker form-control"
                                        data-max-options="1" data-none-selected-text="Non Selected"
                                        data-live-search="true">
                                        <option></option>
                                        <?php
                                        foreach ($state as $value) { ?>
                                            <option value="<?php echo ($value['short_name']); ?>">
                                                <?php echo $value['state_name'] ?>
                                            </option>
                                        <?php }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="city">
                                    <small class="req text-danger">* </small>
                                    <label for="city" class="control-label">City</label>
                                    <select id="city" name="city" class="selectpicker form-control"
                                        data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Select city name</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="PAN">
                                    <small class="req text-danger">* </small>
                                    <label for="PAN" class="control-label">PAN number</label>
                                    <input type="text" maxlength="10" minlength="10" name="PAN"
                                        pattern="[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}" id="PAN" class="form-control" value="">
                                    <span class="pan_denger" style="color:red;"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="ifsc_code">
                                    <label for="ifsc_code" class="control-label">IFSC Code</label>
                                    <input type="text" maxlength="11" minlength="11" onblur="getBankDetail(this.value)"
                                        name="ifsc_code" id="ifsc_code" class="form-control" value="">
                                    <span class="ifsc_danger" style="color:red;"></span>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="bank">
                                    <!-- <small class="req text-danger">* </small>
                                    <label for="bank" class="control-label">Bank</label> -->
                                    <!-- <input type="text" id="bank" name="bank" class="form-control" value=""> -->
                                    <div class="form-group" app-field-wrapper="bank"><label for="bank"
                                            class="control-label">Bank Name</label><input type="text" readonly id="bank"
                                            name="bank" class="form-control" value="<?= $bank ?>"></div>

                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="bank_branch">
                                    <div class="form-group" app-field-wrapper="bank_branch">
                                        <label for="bank_branch" class="control-label">Bank Branch</label>
                                        <input type="text" readonly id="bank_branch" name="bank_branch"
                                            class="form-control" value="<?= $bank_branch ?>">
                                    </div>

                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="account_number">
                                    <small class="req text-danger">* </small>
                                    <label for="account_number" class="control-label">Account Number</label>
                                    <input type="number" id="account_number" name="account_number" class="form-control"
                                        value="">
                                    <span class="actnumber_denger" style="color:red;"></span>

                                </div>
                            </div>


                        </div>

                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="account_name">
                                    <small class="req text-danger">* </small>
                                    <label for="account_name" class="control-label">Account Name</label>
                                    <input type="text" id="account_name" name="account_name" class="form-control"
                                        value="">

                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="account_type" class="control-label">Select Account Type</label>
                                    <select class="selectpicker display-block" data-width="100%" id="account_type"
                                        name="account_type" data-live-search="true">
                                        <option value="0" selected disabled>Account Type</option>
                                        <option value="1">Current</option>
                                        <option value="2">Saving</option>
                                        <option value="3">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="controllacc" class="control-label">Control Account</label>
                                    <select class="selectpicker display-block" data-width="100%" id="controllacc"
                                        name="" data-live-search="true">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="downpaymentacc" class="control-label">Down Payment Account</label>
                                    <select class="selectpicker display-block" data-width="100%" id="downpaymentacc"
                                        name="" data-live-search="true">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h3>Service Details</h3>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <small class="req text-danger">* </small>
                                            <label for="state_list" class="control-label">Service State</label>
                                            <select class="selectpicker display-block" data-width="100%" id="state_list"
                                                name="state_list"
                                                data-none-selected-text="<?php echo 'Select State'; ?>"
                                                data-live-search="true" multiple>

                                                <?php foreach ($state as $st) { ?>
                                                    <option value="<?php echo $st['short_name']; ?>">
                                                        <?php echo $st['state_name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">

                                        <div id="PAN_Card_image_preview"></div>
                                        <div class="form-group" app-field-wrapper="PAN_Card_image">
                                            <small class="req text-danger">* </small>
                                            <label for="PAN_Card_image" class="control-label">PAN Card</label>
                                            <input type="file" id="PAN_Card_image" name="PAN_Card_image"
                                                class="form-control" required>
                                        </div>
                                    </div>

                                    <script>

                                    </script>
                                    <div class="col-md-3">
                                        <div id="aadhaar_image_preview"></div>
                                        <div class="form-group" app-field-wrapper="aadhaar_image">
                                            <small class="req text-danger">* </small>
                                            <label for="aadhaar_image" class="control-label">Adhaar Card</label>
                                            <input type="file" id="aadhaar_image" name="aadhaar_image"
                                                class="fileInput form-control" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div id="gst_certification_image_preview"></div>
                                        <div class="form-group" app-field-wrapper="gst_certification_image">
                                            <small class="req text-danger">* </small>
                                            <label for="gst_certification_image" class="control-label">GST
                                                Certificate</label>
                                            <input type="file" id="gst_certification_image"
                                                name="gst_certification_image" class="fileInput form-control" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div id="transport_permit_preview"></div>
                                        <div class="form-group" app-field-wrapper="transport_permit">
                                            <small class="req text-danger">* </small>
                                            <label for="transport_permit" class="control-label">Transport
                                                Permits</label>
                                            <input type="file" id="transport_permit" name="transport_permit"
                                                class="fileInput form-control" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div id="address_proof_preview"></div>
                                        <div class="form-group" app-field-wrapper="address_proof">
                                            <small class="req text-danger">* </small>
                                            <label for="address_proof" class="control-label">Address Proof</label>
                                            <input type="file" id="address_proof" name="address_proof"
                                                class="fileInput form-control" value="">
                                        </div>
                                    </div>


                                    <div class="col-md-3">
                                        <div id="cancel_cheque_preview"></div>
                                        <div class="form-group" app-field-wrapper="cancel_cheque">
                                            <small class="req text-danger">* </small>
                                            <label for="cancel_cheque" class="control-label">Cancel Cheque</label>
                                            <input type="file" id="cancel_cheque" name="cancel_cheque"
                                                class="fileInput form-control" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div id="ownership_photo_preview"></div>
                                        <div class="form-group" app-field-wrapper="ownership_photo">
                                            <small class="req text-danger">* </small>
                                            <label for="ownership_photo" class="control-label">Owner Photograph</label>
                                            <input type="file" id="ownership_photo" name="ownership_photo"
                                                class="fileInput form-control" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div id="shop_act_image_preview"></div>
                                        <div class="form-group" app-field-wrapper="shop_act_image">
                                            <small class="req text-danger">* </small>
                                            <label for="shop_act_image" class="control-label">Shop Act</label>
                                            <input type="file" id="shop_act_image" name="shop_act_image"
                                                class="fileInput form-control" value="">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-12" style="text-align: right; margin-top: 1%;">
                            <?php if (has_permission_new('TransportMaster', '', 'create')) {
                                ?>
                                <button type="button" class="btn btn-info saveBtn" onclick="uploadFiles();"
                                    style="margin-right: 25px;">Save</button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;"
                                    onclick="uploadFiles()">Save</button>
                                <?php
                            } ?>

                            <?php if (has_permission_new('TransportMaster', '', 'edit')) {
                                ?>
                                <button type="button" class="btn btn-info updateBtn" onclick="this.disabled = true;"
                                    style="margin-right: 25px;">Update</button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="btn btn-info updateBtn2 disabled"
                                    style="margin-right: 25px;">Update</button>
                                <?php
                            } ?>

                            <button type="button" class="btn btn-default cancelBtn">Cancel</button>
                        </div>
                    </div>



                    <!--<div class="col-md-3">
                        <?php $value = (isset ($account_detail) ? $account_detail->Blockyn : ''); ?>
                        <div class="form-group">
                        <label for="block_ac" class="control-label">Block A/C</label>
                        <select class="form-control " name="block_ac" data-live-search="true" id="block_ac">
                            <option value="N" <?php if ($value == "N")
                                echo "selected"; ?>>No</option>
                            <option value="Y" <?php if ($value == "Y")
                                echo "selected"; ?>>Yes</option>
                        </select>
                        </div>
                    </div>-->


                </div>

                <div class="row">



                </div>

                <div class="clearfix"></div>
                <!-- Account Head List Model-->

                <div class="modal fade AccountHead_List" id="AccountHead_List" tabindex="-1" role="dialog"
                    data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header" style="padding:5px 10px;">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Transportation List</h4>
                            </div>
                            <div class="modal-body" style="padding:0px 5px !important">

                                <div class="table-AccountHead_List tableFixHead2">
                                    <table
                                        class="tree table table-striped table-bordered table-AccountHead_List tableFixHead2"
                                        id="table_AccountHead_List" width="100%">
                                        <thead>
                                            <tr style="display:none;">
                                                <td colspan="9">
                                                    <h5 style="text-align:center;"><span
                                                            style="font-size:15px;font-weight:700;">
                                                            <?php echo $company_detail->company_name; ?>
                                                        </span><br><span style="font-size:10px;font-weight:600;">
                                                            <?php echo $company_detail->address; ?>
                                                        </span><br><span class="" style="font-size:10px;">Item
                                                            Master</span><br><span class="report_for"
                                                            style="font-size:10px;"></span></h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th id="sl" style="text-align:left;">Transport Code</th>
                                                <th style="text-align:left;">Transporter Name</th>
                                                <th style="text-align:left;">State</th>
                                                <th style="text-align:left;">City</th>
                                                <th style="text-align:left;">PAN Number</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ListTableBody">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer" style="padding:0px;">
                                <input type="text" id="myInput1" onkeyup="myFunction2()"
                                    placeholder="Search for names.." title="Type in a name"
                                    style="float: left;width: 100%;">
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


<?php init_tail(); ?>
<script>
    $(function () {
        'use strict';
        appValidateForm($('#accounting_head'), {

            id: {
                required: true,
                remote: {
                    url: site_url + "admin/misc/accountID_exists",
                    type: 'post',
                    data: {
                        id: function () {
                            return $('input[name="id"]').val();
                        },
                    }
                }
            },
            TransportName: 'required',
            Account_Group: 'required',
            PAN: 'required',
        });
    });

    $('#PAN').keyup(function (e) {
        var val = $('#PAN').val();
        if (val == "") {
            $(".pan_denger").text(" ");
        } else {
            e.preventDefault();
            if (!$('#PAN').val().match('[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}')) {
                $(".pan_denger").text("Enter valid PAN number");
            } else {
                $(".pan_denger").text(" ");
            }
        }
    });

    $('#ifsc_code').keyup(function (e) {
        var val = $('#ifsc_code').val();
        if (val == "") {
            $(".ifsc_danger").text(" ");
        } else {
            e.preventDefault();
            if (!$('#ifsc_code').val().match('[a-zA-Z]{4}[0-9]{7}')) {
                $(".ifsc_danger").text("Enter valid IFSC Code");
            } else {
                $(".ifsc_danger").text(" ");
            }
        }
    });

    $('#account_number').keyup(function (e) {
        e.preventDefault();
        if (!$('#account_number').val().match(/^\d{9,16}$/)) {
            $(".actnumber_denger").text("Limit of Account number is between 9 to 16 digits");
        } else {
            $(".actnumber_denger").text("");
        }
    });


    function getBankDetail(ifsccode) {
        var xhr = new XMLHttpRequest();
        var url = 'https://ifsc.razorpay.com/' + ifsccode;

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var bankDetails = JSON.parse(xhr.responseText);
                var bankName = bankDetails.BANK;
                var bankaddress = bankDetails.address;
                var bank_branch = bankDetails.BRANCH;

                // Display the bank name and address
                document.getElementById('bank').value = bankName;
                document.getElementById('bank_branch').value = bank_branch;
            } else if (xhr.readyState === 4 && xhr.status !== 200) {
                // Handle error
                $(".ifsc_code").text("Enter valid IFSC Code");
                $('#ifsc_code').val('');
                $('#bank').val('');
                // $('#bank_branch').val('');
            }
        };

        xhr.open('GET', url, true);
        xhr.send();
    }

    // IMAGE PREVIEW

    var inputElement = document.getElementById('PAN_Card_image');
    inputElement.addEventListener('change', function () {
        var file = this.files[0];
        var reader = new FileReader();
        reader.onload = function (e) {
            var imgElement = document.createElement('img');
            imgElement.src = e.target.result;

            imgElement.style.maxHeight = '100px';

            document.getElementById('PAN_Card_image_preview').innerHTML = '';
            document.getElementById('PAN_Card_image_preview').appendChild(imgElement);
        };
        reader.readAsDataURL(file);
    });


    var inputElement = document.getElementById('gst_certification_image');
    inputElement.addEventListener('change', function () {
        var file = this.files[0];
        var reader = new FileReader();
        reader.onload = function (e) {
            var imgElement = document.createElement('img');
            imgElement.src = e.target.result;

            imgElement.style.maxHeight = '100px';

            document.getElementById('gst_certification_image_preview').innerHTML = '';
            document.getElementById('gst_certification_image_preview').appendChild(imgElement);
        };
        reader.readAsDataURL(file);
    });


    var inputElement = document.getElementById('aadhaar_image');
    inputElement.addEventListener('change', function () {
        var file = this.files[0];
        var reader = new FileReader();
        reader.onload = function (e) {
            var imgElement = document.createElement('img');
            imgElement.src = e.target.result;

            imgElement.style.maxHeight = '100px';

            document.getElementById('aadhaar_image_preview').innerHTML = '';
            document.getElementById('aadhaar_image_preview').appendChild(imgElement);
        };
        reader.readAsDataURL(file);
    });


    var inputElement = document.getElementById('shop_act_image');
    inputElement.addEventListener('change', function () {
        var file = this.files[0];
        var reader = new FileReader();
        reader.onload = function (e) {
            var imgElement = document.createElement('img');
            imgElement.src = e.target.result;

            imgElement.style.maxHeight = '100px';

            document.getElementById('shop_act_image_preview').innerHTML = '';
            document.getElementById('shop_act_image_preview').appendChild(imgElement);
        };
        reader.readAsDataURL(file);
    });


    var inputElement = document.getElementById('transport_permit');
    inputElement.addEventListener('change', function () {
        var file = this.files[0];
        var reader = new FileReader();
        reader.onload = function (e) {
            var imgElement = document.createElement('img');
            imgElement.src = e.target.result;

            imgElement.style.maxHeight = '100px';

            document.getElementById('transport_permit_preview').innerHTML = '';
            document.getElementById('transport_permit_preview').appendChild(imgElement);
        };
        reader.readAsDataURL(file);
    });


    var inputElement = document.getElementById('cancel_cheque');
    inputElement.addEventListener('change', function () {
        var file = this.files[0];
        var reader = new FileReader();
        reader.onload = function (e) {
            var imgElement = document.createElement('img');
            imgElement.src = e.target.result;

            imgElement.style.maxHeight = '100px';

            document.getElementById('cancel_cheque_preview').innerHTML = '';
            document.getElementById('cancel_cheque_preview').appendChild(imgElement);
        };
        reader.readAsDataURL(file);
    });


    var inputElement = document.getElementById('ownership_photo');
    inputElement.addEventListener('change', function () {
        var file = this.files[0];
        var reader = new FileReader();
        reader.onload = function (e) {
            var imgElement = document.createElement('img');
            imgElement.src = e.target.result;

            imgElement.style.maxHeight = '100px';

            document.getElementById('ownership_photo_preview').innerHTML = '';
            document.getElementById('ownership_photo_preview').appendChild(imgElement);
        };
        reader.readAsDataURL(file);
    });


    var inputElement = document.getElementById('address_proof');
    inputElement.addEventListener('change', function () {
        var file = this.files[0];
        var reader = new FileReader();
        reader.onload = function (e) {
            var imgElement = document.createElement('img');
            imgElement.src = e.target.result;

            imgElement.style.maxHeight = '100px';

            document.getElementById('address_proof_preview').innerHTML = '';
            document.getElementById('address_proof_preview').appendChild(imgElement);
        };
        reader.readAsDataURL(file);
    });

</script>

<script>
    $(document).ready(function () {
        $('.updateBtn').hide();
        $('.updateBtn2').hide();

        $("#TransportID").dblclick(function () {
            $('#AccountHead_List').modal('show');
            $('#AccountHead_List').on('shown.bs.modal', function () {
                $('#myInput1').val('');
                $('#myInput1').focus();

                var AccountID = "";
                $.ajax({
                    url: "<?php echo admin_url(); ?>Transportation/AccountListPopUp",
                    method: "POST",
                    cache: false,
                    data: { id: AccountID, },
                    success: function (data) {
                        if (empty(data)) {

                        } else {
                            $("#ListTableBody").html(data);
                            $('.get_AccountID').on('click', function () {
                                AccountID = $(this).attr("data-id");
                                $.ajax({
                                    url: "<?php echo admin_url(); ?>Transportation/GetAccountDetailByID",
                                    dataType: "JSON",
                                    method: "POST",
                                    data: { id: AccountID },
                                    beforeSend: function () {
                                        $('.searchh2').css('display', 'block');
                                        $('.searchh2').css('color', 'blue');
                                    },
                                    complete: function () {
                                        $('.searchh2').css('display', 'none');
                                    },
                                    success: function (data) {
                                        $('#TransportID').val(data.TransportID);
                                        $('#Transport_ID').val(data.id);
                                        $('#TransportName').val(data.TransportName);
                                        // $('#state').val(data.state);
                                        $('#state').selectpicker('val', data.state);
                                        let CityList = data.CityList;
                                        $("#city").children().remove();
                                        for (var i = 0; i < CityList.length; i++) {
                                            $("#city").append('<option value="' + CityList[i]["id"] + '">' + CityList[i]["city_name"] + '</option>');
                                        }
                                        $('.selectpicker').selectpicker('refresh');
                                        $('#city').selectpicker('val', data.city);
                                        $('.selectpicker').selectpicker('refresh');
                                        $('#state_list').selectpicker('val', data.state_list);
                                        $('.selectpicker').selectpicker('refresh');
                                        $('#address').val(data.address);
                                        $('#PAN').val(data.PAN);
                                        $('#bank').val(data.bank);
                                        $('#bank_branch').val(data.bank_branch);
                                        $('#account_type').val(data.account_type);
                                        $('#account_number').val(data.account_number);
                                        $('#account_name').val(data.account_name);
                                        $('#ifsc_code').val(data.ifsc_code);
                                        $('.selectpicker').selectpicker('refresh');
                                        $('.saveBtn').hide();
                                        $('.updateBtn').show();
                                        $('.saveBtn2').hide();
                                        $('.updateBtn2').show();
                                        var base_url = "<?php echo base_url(); ?>";
                                        if (data.PAN_Card_image) {
                                            $('#PAN_Card_image_preview').html('<img src="' + base_url + data.PAN_Card_image + '" style="height: 100px;" />');
                                        } if (data.aadhaar_image) {
                                            $('#aadhaar_image_preview').html('<img src="' + base_url + data.aadhaar_image + '" style="height: 100px;" />');
                                        } if (data.gst_certification_image) {
                                            $('#gst_certification_image_preview').html('<img src="' + base_url + data.gst_certification_image + '" style="height: 100px;" />');
                                        } if (data.transport_permit) {
                                            $('#transport_permit_preview').html('<img src="' + base_url + data.transport_permit + '" style="height: 100px;" />');
                                        } if (data.address_proof) {
                                            $('#address_proof_preview').html('<img src="' + base_url + data.address_proof + '" style="height: 100px;" />');
                                        } if (data.cancel_cheque) {
                                            $('#cancel_cheque_preview').html('<img src="' + base_url + data.cancel_cheque + '" style="height: 100px;" />');
                                        } if (data.ownership_photo) {
                                            $('#ownership_photo_preview').html('<img src="' + base_url + data.ownership_photo + '" style="height: 100px;" />');
                                        } if (data.shop_act_image) {
                                            $('#shop_act_image_preview').html('<img src="' + base_url + data.shop_act_image + '" style="height: 100px;" />');
                                        }
                                    }
                                });
                                $('#AccountHead_List').modal('hide');
                            });
                        }
                    }
                });
            })
        });


        $("#TransportID").focus(function () {
            $('#TransportID').val('');
            $('#TransportName').val('');
            $('#PAN').val('');
            $('#state').val('');
            $('.selectpicker').selectpicker('refresh');
            $('#city').val('');
            $('.selectpicker').selectpicker('refresh');
            $('#address').val('');
            $('#account_name').val('');
            $('#bank').val('');
            $('#account_type').val('');
            $('.selectpicker').selectpicker('refresh');
            $('#account_number').val('');
            $('#ifsc_code').val('');
            $('#PAN_Card_image').val('');
            $('#gst_certification_image').val('');
            $('#aadhaar_image').val('');
            $('#shop_act_image').val('');
            $('#transport_permit').val('');
            $('#cancel_cheque').val('');
            $('#ownership_photo').val('');
            $('#address_proof').val('');
            $('#bank_branch').val('');
            $('.selectpicker').selectpicker('refresh');
            $('#state_list').val('');
            $('.selectpicker').selectpicker('refresh');
            $('#PAN_Card_image_preview').html('');
            $('#gst_certification_image_preview').html('');
            $('#aadhaar_image_preview').html('');
            $('#shop_act_image_preview').html('');
            $('#transport_permit_preview').html('');
            $('#cancel_cheque_preview').html('');
            $('#ownership_photo_preview').html('');
            $('#address_proof_preview').html('');




            $('.saveBtn').removeAttr('disabled');
            $('.saveBtn').show();
            $('.saveBtn2').show();
            $('.updateBtn').hide();
            $('.updateBtn2').hide();

        });

        // Cancel selected data
        $(".cancelBtn").click(function () {
            $('#TransportID').val('');
            $('#TransportName').val('');
            $('#PAN').val('');
            $('#address').val('');
            $('#account_name').val('');
            $('#state').val('');
            $('.selectpicker').selectpicker('refresh');
            $('#city').val('');
            $('.selectpicker').selectpicker('refresh');
            $('#bank').val('');
            $('#account_type').val('');
            $('#account_number').val('');
            $('#ifsc_code').val('');
            $('#state_list').val('');
            $('.selectpicker').selectpicker('refresh');
            $('#bank_branch').val('');
            $('#PAN_Card_image').val('');
            $('#aadhaar_image').val('');
            $('#gst_certification_image').val('');
            $('#transport_permit').val('');
            $('#address_proof').val('');
            $('#cancel_cheque').val('');
            $('#ownership_photo').val('');
            $('#shop_act_image').val('');
            $('.selectpicker').selectpicker('refresh');
            $('.saveBtn').removeAttr('disabled');
            $('.saveBtn').show();
            $('.saveBtn2').show();
            $('.updateBtn').hide();
            $('.updateBtn2').hide();
            $('#PAN_Card_image_preview').html('');
            $('#gst_certification_image_preview').html('');
            $('#aadhaar_image_preview').html('');
            $('#shop_act_image_preview').html('');
            $('#transport_permit_preview').html('');
            $('#cancel_cheque_preview').html('');
            $('#ownership_photo_preview').html('');
            $('#address_proof_preview').html('');



        });



        $('.saveBtn').on('click', function () {
            var AccountID = $('#TransportID').val();
            var user_id = $('#user_id').val();
            var company = $('#TransportName').val();
            var state = $('#state').val();
            var city = $('#city').val();
            var address = $('#address').val();
            var PAN = $('#PAN').val();
            var bank = $('#bank').val();
            var account_type = $('#account_type').val();
            var account_number = $('#account_number').val();
            var account_name = $('#account_name').val();
            var ifsc_code = $('#ifsc_code').val();
            var bank_branch = $('#bank_branch').val();
            var state_list = $('#state_list').val();
            var PAN_Card_image = $('#PAN_Card_image').val();
            var gst_certification_image = $('#gst_certification_image').val();
            var aadhaar_image = $('#aadhaar_image').val();
            var shop_act_image = $('#shop_act_image').val();
            var transport_permit = $('#transport_permit').val();
            var cancel_cheque = $('#cancel_cheque').val();
            var ownership_photo = $('#ownership_photo').val();
            var address_proof = $('#address_proof').val();

            // console.log(state_list);
            if (AccountID === '') {
                alert('Please enter Transportation Code');
                $('#TransportID').focus();
                return;
            } else if (company === '') {
                alert('Please enter Transporter Name');
                $('#TransportName').focus();
                return;
            } else if (address === '') {
                alert('Please Enter Address');
                $('#address').focus();
                return;
            } if (state === '') {
                alert('Please Select State');
                $('#state').focus();
                return;
            } if (city === '') {
                alert('Please Select City');
                $('#city').focus();
                return;
            } if (address === '') {
                alert('Please Enter Address');
                $('#address').focus();
                return;
            } if (PAN === '') {
                alert('Please Enter PAN Card Number');
                $('#PAN').focus();
                return;
            } if (ifsc_code === '') {
                alert('Please Enter IFSC Code');
                $('#ifsc_code').focus();
                return;
            } if (account_number === '') {
                alert('Please Enter Account Number');
                $('#account_number').focus();
                return;
            } if (account_name === '') {
                alert('Please Enter Account Name');
                $('#account_name').focus();
                return;
            } if (empty(account_type)) {
                alert('Please Enter Account Type');
                $('#account_type').focus();
                return;
            } if (empty(state_list)) {
                alert('Please Select Servicing States');
                $('#state_list').focus();
                return;
            } if (PAN_Card_image === '') {
                alert('Please Attatch PAN Card Image');
                $('#PAN_Card_image').focus();
                return;
            } if (gst_certification_image === '') {
                alert('Please Attatch GST Certificate');
                $('#gst_certification_image').focus();
                return;
            } if (aadhaar_image === '') {
                alert('Please Attatch Aadhar Card Image');
                $('#aadhaar_image').focus();
                return;
            } if (shop_act_image === '') {
                alert('Please Attatch Shop Act Image');
                $('#shop_act_image').focus();
                return;
            } if (transport_permit === '') {
                alert('Please Attatch Transport Permit');
                $('#transport_permit').focus();
                return;
            } if (ownership_photo === '') {
                alert('Please Attatch Ownership Photo');
                $('#ownership_photo').focus();
                return;
            } if (cancel_cheque === '') {
                alert('Please Attatch Cancel Cheque');
                $('#cancel_cheque').focus();
                return;
            } if (address_proof === '') {
                alert('Please Attatch Address Proof');
                $('#address_proof').focus();
                return;
            } else {
                $('.saveBtn').prop('disabled', true);
                var formData = new FormData();
                formData.append('AccountID', AccountID);
                formData.append('user_id', user_id);
                formData.append('company', company);
                formData.append('state', state);
                formData.append('city', city);
                formData.append('address', address);
                formData.append('PAN', PAN);
                formData.append('bank', bank);
                formData.append('account_type', account_type);
                formData.append('account_number', account_number);
                formData.append('account_name', account_name);
                formData.append('ifsc_code', ifsc_code);
                formData.append('bank_branch', bank_branch);
                formData.append('state_list', state_list);
                formData.append('PAN_Card_image', $('#PAN_Card_image')[0].files[0]);
                formData.append('gst_certification_image', $('#gst_certification_image')[0].files[0]);
                formData.append('aadhaar_image', $('#aadhaar_image')[0].files[0]);
                formData.append('shop_act_image', $('#shop_act_image')[0].files[0]);
                formData.append('transport_permit', $('#transport_permit')[0].files[0]);
                formData.append('cancel_cheque', $('#cancel_cheque')[0].files[0]);
                formData.append('ownership_photo', $('#ownership_photo')[0].files[0]);
                formData.append('address_proof', $('#address_proof')[0].files[0]);
                $.ajax({
                    url: "<?php echo admin_url(); ?>Transportation/SaveItemID",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $('.searchh3').css('display', 'block');
                        $('.searchh3').css('color', 'blue');
                    },
                    complete: function () {
                        $('.searchh3').css('display', 'none');
                    },
                    success: function (data) {
                        $('.saveBtn').prop('disabled', false);
                        $('.searchh3').css('display', 'none');

                        console.log(data);
                        if (data) {
                            alert_float('success', 'Record Inserted Successfully...');
                            $('#TransportID,#user_id, #TransportName, #state, #city, #address, #PAN, #bank, #bank_branch, #account_type, #account_number, #account_name, #ifsc_code, #state_list, #PAN_Card_image').val('');
                            $('.selectpicker').selectpicker('refresh');
                            $('#aadhaar_image').val('');
                            $('#gst_certification_image').val('');
                            $('#transport_permit').val('');
                            $('#address_proof').val('');
                            $('#cancel_cheque').val('');
                            $('#ownership_photo').val('');
                            $('#shop_act_image').val('');
                            $('#PAN_Card_image_preview').html('');
                            $('#gst_certification_image_preview').html('');
                            $('#aadhaar_image_preview').html('');
                            $('#shop_act_image_preview').html('');
                            $('#transport_permit_preview').html('');
                            $('#cancel_cheque_preview').html('');
                            $('#ownership_photo_preview').html('');
                            $('#address_proof_preview').html('');

                            $('.saveBtn').show();
                            $('.updateBtn').hide();
                            $('.saveBtn2').show();
                            $('.updateBtn2').hide();
                        } else {
                            alert('Failed to insert record.');
                        }
                    },
                    error: function (xhr, status, error) {
                        alert('Failed to upload file: ' + error);
                    },
                    complete: function () {
                        $('.saveBtn').prop('disabled', false);
                    }
                });
            }
        });



        $('.updateBtn').on('click', function () {

            if ($('#TransportName').val() === '') {
                alert('Please Enter Transport Name.');
                $(this).prop('disabled', false);
                return;
            }
            if ($('#address').val() === '') {
                alert('Please Enter Address.');
                $(this).prop('disabled', false);
                return;
            }

            if ($('#state').val() === '') {
                alert('Please select a State.');
                $(this).prop('disabled', false);
                return;
            }
            if ($('#city').val() === '') {
                alert('Please select a City.');
                $(this).prop('disabled', false);
                return;
            }
            if ($('#PAN').val() === '') {
                alert('Please select a Pan Card Number.');
                $(this).prop('disabled', false);
                return;
            }
            if ($('#ifsc_code').val() === '') {
                alert('Please Enter IFSC Code.');
                $(this).prop('disabled', false);
                return;
            }
            if ($('#account_number').val() === '') {
                alert('Please Enter Account Number.');
                $(this).prop('disabled', false);
                return;
            }
            if ($('#account_name').val() === '') {
                alert('Please Enter Account Name.');
                $(this).prop('disabled', false);
                return;
            }
            if ($('#account_type').val() === '') {
                alert('Please Select Account Type.');
                $(this).prop('disabled', false);
                return;
            }


            $(this).prop('disabled', true);
            var id = $('#Transport_ID').val();
            var AccountID = $('#TransportID').val();
            var user_id = $('#user_id').val();
            var TransportName = $('#TransportName').val();
            var state = $('#state').val();
            var city = $('#city').val();
            var PAN = $('#PAN').val();
            var address = $('#address').val();
            var bank = $('#bank').val();
            var bank_branch = $('#bank_branch').val();
            var account_type = $('#account_type').val();
            var account_number = $('#account_number').val();
            var account_name = $('#account_name').val();
            var ifsc_code = $('#ifsc_code').val();
            var state_list = $('#state_list').val();
            var PAN_Card_image = $('#PAN_Card_image')[0].files[0];
            var gst_certification_image = $('#gst_certification_image')[0].files[0];
            var shop_act_image = $('#shop_act_image')[0].files[0];
            var transport_permit = $('#transport_permit')[0].files[0];
            var cancel_cheque = $('#cancel_cheque')[0].files[0];
            var ownership_photo = $('#ownership_photo')[0].files[0];
            var address_proof = $('#address_proof')[0].files[0];
            var aadhaar_image = $('#aadhaar_image')[0].files[0];

            var formData = new FormData();
            formData.append('id', id);
            formData.append('AccountID', AccountID);
            formData.append('user_id', user_id);
            formData.append('TransportName', TransportName);
            formData.append('state', state);
            formData.append('city', city);
            formData.append('PAN', PAN);
            formData.append('address', address);
            formData.append('bank', bank);
            formData.append('bank_branch', bank_branch);
            formData.append('account_type', account_type);
            formData.append('account_number', account_number);
            formData.append('account_name', account_name);
            formData.append('ifsc_code', ifsc_code);
            formData.append('state_list', state_list);
            if ($('#PAN_Card_image').val()) {
                formData.append('PAN_Card_image', PAN_Card_image);
            }
            if ($('#gst_certification_image').val()) {
                formData.append('gst_certification_image', gst_certification_image);
            }
            if ($('#shop_act_image').val()) {
                formData.append('shop_act_image', shop_act_image);
            }
            if ($('#transport_permit').val()) {
                formData.append('transport_permit', transport_permit);
            }
            if ($('#cancel_cheque').val()) {
                formData.append('cancel_cheque', cancel_cheque);
            }
            if ($('#ownership_photo').val()) {
                formData.append('ownership_photo', ownership_photo);
            }
            if ($('#address_proof').val()) {
                formData.append('address_proof', address_proof);
            }
            if ($('#aadhaar_image').val()) {
                formData.append('aadhaar_image', aadhaar_image);
            }

            $.ajax({
                url: "<?php echo admin_url('Transportation/UpdateAccountID'); ?>",
                type: "POST",
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('.searchh4').css('display', 'block');
                    $('.searchh4').css('color', 'blue');
                },
                complete: function () {
                    $('.searchh4').css('display', 'none');
                },
                success: function (data) {
                    $('.updateBtn').prop('disabled', false);
                    $('.searchh4').css('display', 'none');

                    if (data.success == true) {
                        alert_float('success', 'Record updated successfully...');
                        $('#TransportID, #TransportName, #state, #city, #address, #PAN, #bank, #bank_branch, #account_type, #account_number, #account_name, #ifsc_code, #state_list, #PAN_Card_image, #gst_certification_image, #shop_act_image, #transport_permit, #cancel_cheque, #ownership_photo, #address_proof, #aadhaar_image').val('');
                        $('#PAN_Card_image_preview').html('');
                        $('#gst_certification_image_preview').html('');
                        $('#aadhaar_image_preview').html('');
                        $('#shop_act_image_preview').html('');
                        $('#transport_permit_preview').html('');
                        $('#cancel_cheque_preview').html('');
                        $('#ownership_photo_preview').html('');
                        $('#address_proof_preview').html('');
                        $('.selectpicker').selectpicker('refresh');

                        $('.saveBtn').removeAttr('disabled');
                        $('.saveBtn').show();
                        $('.saveBtn2').show();
                        $('.updateBtn').hide();
                        $('.updateBtn2').hide();
                    } else {
                        alert_float('warning', 'Data not updated...');
                    }
                },
                error: function (xhr, status, error) {
                    alert('Failed to update record: ' + error);
                },
                complete: function () {
                    $('.updateBtn').prop('disabled', false);
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
        table = document.getElementById("table_AccountHead_List");
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
                } else if (td1) {
                    txtValue = td1.textContent || td1.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else if (td2) {
                        txtValue = td2.textContent || td2.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                        } else if (td3) {
                            txtValue = td3.textContent || td3.innerText;
                            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                tr[i].style.display = "";
                            } else if (td4) {
                                txtValue = td4.textContent || td4.innerText;
                                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                    tr[i].style.display = "";

                                } else {
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
            && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
</script>

<script type="text/javascript">
    $('#accountnumber').on('keypress', function (event) {
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
            event.preventDefault();
        }
        var input = $(this).val();
        if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 3)) {
            event.preventDefault();
        }
    });
</script>

<script>
    $('#state').on('change', function () {
        var StateID = $(this).val();
        var url = "<?php echo base_url(); ?>admin/Transportation/GetCity";
        jQuery.ajax({
            type: 'POST',
            url: url,
            data: { StateID: StateID },
            dataType: 'json',
            success: function (data) {
                $("#city").find('option').remove();
                $("#city").selectpicker("refresh");
                $("#city").append(new Option('', 'select city'));
                for (var i = 0; i < data.length; i++) {
                    $("#city").append(new Option(data[i].city_name, data[i].id));
                }
                $('.selectpicker').selectpicker('refresh');

            }
        });
    });


</script>

<style>
    #TransportID {
        text-transform: uppercase;
    }

    #PAN {
        text-transform: uppercase;
    }

    #ifsc_code {
        text-transform: uppercase;
    }

    #table_AccountHead_List td:hover {
        cursor: pointer;
    }

    #table_AccountHead_List tr:hover {
        background-color: #ccc;
    }

    .table-AccountHead_List {
        overflow: auto;
        max-height: 65vh;
        width: 100%;
        position: relative;
        top: 0px;
    }

    .table-AccountHead_List thead th {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .table-AccountHead_List tbody th {
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

</body>

</html>