<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="mtop40">
    <div class="col-md-4 col-md-offset-4 text-center">
        <h1 class="text-uppercase mbot20 login-heading">
            <?php
            echo _l(get_option('allow_registration') == 1 ? 'clients_login_heading_register' : 'clients_login_heading_no_register');
            ?>
        </h1>
    </div>
    <div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2">
        <?php echo form_open($this->uri->uri_string(), array('class' => 'login-form')); ?>
        <?php hooks()->do_action('clients_login_form_start'); ?>
        <div class="panel_s">
            <div class="panel-body">
                <!-- <?php if (!is_language_disabled()) { ?>
                    <div class="form-group select-placeholder">
                        <label for="language" class="control-label"><?php echo _l('language'); ?>
                        </label>
                        <select name="language" id="language" class="form-control selectpicker" onchange="change_contact_language(this)" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                            <?php $selected = (get_contact_language() != '') ? get_contact_language() : get_option('active_language'); ?>
                            <?php foreach ($this->app->get_available_languages() as $availableLanguage) {
                            ?>
                                <option value="<?php echo $availableLanguage; ?>" <?php echo ($availableLanguage == $selected) ? 'selected' : '' ?>>
                                    <?php echo ucfirst($availableLanguage); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                <?php } ?> -->
                <div class="form-group">
                    <label for="email">Mobile Number</label>
                    <input type="text" autofocus="true" class="form-control" name="email" id="email">
                    <?php echo form_error('email'); ?>
                </div>
                <div style="display:none;" class="form-group">
                    <label for="password"><?php echo _l('clients_login_password'); ?></label>
                    <input type="password" class="form-control" name="password" id="password">
                    <?php echo form_error('password'); ?>
                </div>
                <?php if (show_recaptcha_in_customers_area()) { ?>
                    <div class="g-recaptcha mbot15" data-sitekey="<?php echo get_option('recaptcha_site_key'); ?>"></div>
                    <?php echo form_error('g-recaptcha-response'); ?>
                <?php } ?>
                <!--div class="checkbox">
					<input type="checkbox" name="remember" id="remember">
					<label for="remember">
					<?php echo _l('clients_login_remember'); ?>
					</label>
				</div-->

                <div class="form-group" id="SendOTP">
                    <button class="btn btn-info btn-block send_otp" type="button">Send OTP</button>
                </div>
                <div class="OTP_Div" id="OTP_Div" style="display:none;">
                    <div class="mb-3 form-otp-toggle">
                        <div class="d-flex justify-content-between">
                            <label class="form-label" for="otp">OTP</label>
                        </div>
                        <div class="form-group">
                            <input type="text" id="otp" minlength="6" maxlength="6" class="form-control" name="otp" placeholder="" aria-describedby="otp" onkeypress="return isNumber(event)" />
                        </div>
                        <!-- <div style="color:#2b61ab" id="timer_id">Time left = <span id="timer"></span></div> -->
                        <div id="resend_otp" style="display:none;"><a href="#" id="resend_email">Resend OTP</a></div>
                    </div>

                    <div class="form-group">
                        <button id="verifyOTP" class="btn btn-primary  btn-block submit_otp" type="button">Submit OTP</button>
                    </div>
                </div>
                <div class="form-group">
                    <button style="display:none" type="submit" class="btn btn-info btn-block"><?php echo "Submit OTP"; ?></button>
                    <?php if (get_option('allow_registration') == 1) { ?>
                        <a href="<?php echo site_url('authentication/register'); ?>" class="btn btn-success btn-block"><?php echo _l('clients_register_string'); ?>
                        </a>
                    <?php } ?>
                </div>
                <a href="<?php echo site_url('authentication/forgot_password'); ?>"><?php echo _l('customer_forgot_password'); ?></a>
                <?php hooks()->do_action('clients_login_form_end'); ?>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {

        let timerOn = true;


        function timer(remaining) {
            var m = Math.floor(remaining / 60);
            var s = remaining % 60;

            m = m < 10 ? '0' + m : m;
            s = s < 10 ? '0' + s : s;
            document.getElementById('timer').innerHTML = m + ':' + s;
            remaining -= 1;

            if (remaining >= 0 && timerOn) {
                setTimeout(function() {
                    timer(remaining);
                }, 1000);
                return;
            }

            if (!timerOn) {
                // Do validate stuff here
                return;
            }
            $('#timer_id').css('display', 'none');
            $('#resend_otp').css('display', 'block');
            // Do timeout stuff here
            //alert('Timeout for otp');
        }

        $('#resend_email').on('click', function() {
            var Email_id = $('#email').val();
            $('#SendOTP').css('display', 'none');
            $('.OTP_Div').css('display', 'block');
            $('#otphidden').val(data);
            $('#timer_id').css('display', 'block');
            $('#resend_otp').css('display', 'none');
            timer(120);
        });

        $('.send_otp').on('click', function() {
            var phoneNumber = $('#email').val();
            // 			if(Email_id != ''){
            // 				$('#SendOTP').css('display','none');
            // 				$('.OTP_Div').css('display','block');
            // 				$('#otphidden').val('12345');
            // 				timer(120);
            // 			}

            $.ajax({
                url: "<?php echo site_url(); ?>authentication/sendOTP",
                method: "POST",
                dataType:"json",
                data: {
                    phoneNumber: phoneNumber
                },
                success: function(data) {
                    if(data == false){
                        console.log("Please register first, number does not exist in the database.");
                        alert("Please enter your registered Mobile Number");
                    }
                    else{
                        $('#SendOTP').css('display', 'none');
                        $('.OTP_Div').css('display', 'block');
                    }
                }
            });

        });

        $('#verifyOTP').on('click', function() {
            var phoneNumber = $('#email').val();
            var otp = $('#otp').val();
            $.ajax({
                url: "<?php echo site_url(); ?>authentication/verifyOTP",
                method: "POST",
                data: {
                    phoneNumber: phoneNumber,
                    otp: otp
                },
                success: function(data) {
                    if (data) {
                        window.location.href = "<?php echo site_url(); ?>";
                    } else {
                        alert("Verification failed. Please check your OTP and try again.");
                    }
                }
            });

        });

    });
</script>

<script>
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode = 46 && charCode > 31 &&
            (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
</script>