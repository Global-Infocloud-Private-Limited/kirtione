<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<style>
  #AccountID {
    text-transform: uppercase;
  }

  #table_trade_List td:hover {
    cursor: pointer;
  }

  #table_trade_List tr:hover {
    background-color: #ccc;
  }

  .table-trade_List {
    overflow: auto;

    max-height: 50vh;

    width: 100%;

    position: relative;
    top: 0px;
  }

  .table-trade_List thead th {
    position: sticky;
    top: 0;
    z-index: 1;
  }

  .table-trade_List tbody th {
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

<div id="wrapper">

  <div class="content">

    <div class="row">

      <div class="col-md-10">

        <div class="panel_s">

          <div class="panel-body">

            <div class="row">

              <div class="col-md-12 text-centerr">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                    <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                    <li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
                    <li class="breadcrumb-item active" aria-current="page"><b>Create Purchase Trade</b></li>
                  </ol>
                </nav>
                <hr class="hr_style" style="margin-Bottom:5px !important;">
              </div>
              <div class="col-md-12" style="height:10px;margin-bottom:3px;">
                <span class="searchh2" style="display:none;">Please wait while fetching data.</span>
                <span class="searchh3" style="display:none;">Please wait while creating new record.</span>
                <span class="searchh4" style="display:none;">Please wait while updating data.</span>
              </div>

              <div class="col-md-3">
                <!-- <div class="form-group" app-field-wrapper="AccountID">
                  <small class="req text-danger">* </small>
                  <label for="AccountID" class="control-label">AccountID/Mobile number</label>
                  <input type="text" id="AccountID" name="AccountID" class="form-control" value="">
                  <input type="hidden" id="CurrentRate" name="CurrentRate" class="form-control" value="">
                  <input type="hidden" id="AccountType" name="AccountType" class="form-control" value="">
                </div> -->
                <div class="form-group" app-field-wrapper="AccountID">
                  <small class="req text-danger"> </small>
                  <label for="AccountID" class="control-label">AccountID/Mobile number</label>
                  <style>
										.ajax-search-box{position:relative;width:100%;}
										.search-results{position:absolute;top:100%;left:0;width:100%;background:#fff;border:1px solid #ddd;max-height:250px;overflow-y:auto;display:none;z-index:9999;}
										.search-item{padding:10px;cursor:pointer;border-bottom:1px solid #eee;}
										.search-item:hover{background:#f5f5f5;}
									</style>
                  <div class="ajax-search-box" data-url="<?= admin_url('KirtiOneOrder/get_party'); ?>">
                    <input type="search" class="search-input form-control" placeholder="Search name... or Mobile number" value="">
                    <input type="hidden" class="selected-id" id="AccountID" name="AccountID" value="">
                    <input type="hidden" id="CurrentRate" name="CurrentRate" class="form-control" value="">
                    <input type="hidden" id="AccountType" name="AccountType" class="form-control" value="">
                    <div class="search-results"></div>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group" app-field-wrapper="accountName">
                  <small class="req text-danger">* </small>
                  <label for="accountName" class="control-label">AccountName</label>
                  <input type="text" id="accountName" name="accountName" class="form-control" value="" readonly>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group" app-field-wrapper="center">
                  <small class="req text-danger">* </small>
                  <label for="center" class="form-label">Center Name</label>
                  <select name="center" id="center" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                    <option value="">Non Selected</option>
                    <?php
                    foreach ($center as $key => $value) {
                    ?>
                      <option value="<?php echo $value['CenterID']; ?>"><?php echo $value['CenterName']; ?> </option>
                    <?php
                    }
                    ?>
                  </select>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group" app-field-wrapper="Commodity">
                  <small class="req text-danger">* </small>
                  <label for="Commodity" class="form-label">Commodity Name</label>
                  <select name="Commodity" id="Commodity" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                    <option value="">Non Selected</option>
                  </select>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group" app-field-wrapper="item">
                  <small class="req text-danger">* </small>
                  <label for="item" class="form-label">Item Name</label>
                  <select name="item" id="item" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                    <option value="">Non Selected</option>
                  </select>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group" app-field-wrapper="currentrate">
                  <small class="req text-danger">* </small>
                  <label for="currentrate" class="control-label">Current Rate</label>
                  <input type="text" id="currentrate" name="currentrate" class="form-control" value="" disabled>
                  <input type="hidden" id="Mastercurrentrate" name="Mastercurrentrate" class="form-control" value="">
                </div>
              </div>

              <div class="col-md-2">
                <div class="form-group" app-field-wrapper="Quantity">
                  <small class="req text-danger">* </small>
                  <label for="Quantity" class="control-label">Min Quantity(MT)</label>
                  <input type="text" id="Quantity" name="Quantity" class="form-control" value="">
                </div>
              </div>

              <div class="col-md-2">
                <div class="form-group" app-field-wrapper="MaxQuantity">
                  <small class="req text-danger">* </small>
                  <label for="MaxQuantity" class="control-label">Max Quantity(MT)</label>
                  <input type="text" id="MaxQuantity" name="MaxQuantity" class="form-control" value="">
                </div>
              </div>

              <div class="col-md-2">
                <div class="form-group" app-field-wrapper="QuantityBag">
                  <!-- <small class="req text-danger">* </small> -->
                  <label for="QuantityBag" class="control-label">Quantity(Bag)</label>
                  <input type="text" id="QuantityBag" name="QuantityBag" class="form-control" value="" onkeypress="return isNumber(event)">
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group" app-field-wrapper="TradeType">
                  <small class="req text-danger">* </small>
                  <label for="TradeType" class="form-label">Trade Type</label>
                  <select name="TradeType" id="TradeType" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                    <option value="P">Purchase</option>
                    <option value="D">Warehouse Storage</option>
                  </select>
                </div>
              </div>
              
              <div class="col-md-3">
                <div class="form-group" app-field-wrapper="FreightTerms">
                  <label for="FreightTerms" class="form-label">Freight Terms</label>
                  <select name="FreightTerms" id="FreightTerms" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                    <option value="1">SPOT</option>
                    <option value="2" selected>DELIVERY</option>
                  </select>
                </div>
              </div>
              <!-- <div class="clearfix"></div> -->

              <div class="col-md-3">
                <div class="form-group" app-field-wrapper="vehicle_number">
                  <!-- <small class="req text-danger">* </small> -->
                  <label for="vehicle_number" class="control-label">Vehicle Number</label>
                  <input type="text" id="vehicle_number" name="vehicle_number" class="form-control" value="">
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group" app-field-wrapper="driver_mobile">
                  <!-- <small class="req text-danger">* </small> -->
                  <label for="driver_mobile" class="control-label">Driver Mobile No.</label>
                  <input type="text" id="driver_mobile" name="driver_mobile" minlength="10" maxlength="10" class="form-control" value="" onkeypress="return isNumber(event)">
                </div>
              </div>
              <div class="submit_otp_div" style="display:none;">
                <div class="col-md-2">
                  <div class="form-group" app-field-wrapper="enter_otp">
                    <small class="req text-danger">* </small>
                    <label for="enter_otp" class="control-label">Enter OTP</label>
                    <input type="text" id="enter_otp" minlength="6" maxlength="6" class="form-control" name="enter_otp" placeholder="" aria-describedby="enter_otp" onkeypress="return isNumber(event)" />
                    <div style="color:#2b61ab" id="timer_id">Time left = <span id="timer"></span></div>
                    <div id="resend_otp" style="display:none;"><a href="#" id="resend_email">Resend OTP</a></div>
                  </div>
                </div>
                <div class="col-md-2" style="margin-top: 20px;">
                  <div class="form-group">
                    <button id="verifyOTP" class="btn btn-primary  btn-block submit_otp" type="button">Submit OTP</button>
                    <div id="conf_msg" style="display:none;"><a href="#">OTP Veryfied Successfully</a></div>
                    <input type="hidden" name="is_otp_veryfied" id="is_otp_veryfied" value="">
                  </div>
                </div>
              </div>
              <div class="send_otp_div" style="display:none;">
                <div class="col-md-2" style="margin-top: 20px;">
                  <div class="form-group">
                    <button id="send_otp" class="btn btn-primary  btn-block send_otp" type="button">Send OTP</button>
                  </div>
                </div>
              </div>

              <div class="col-md-12" style="margin-top: 22px;">
                <?php if (has_permission_new('Purchasetrade_punch', '', 'create')) {
                ?>
                  <button type="button" class="btn btn-info asnBtn" style="margin-right: 25px;">Save</button>
                <?php } ?>
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
  let currentRequest = null;

  function debounce(callback, delay = 300) {
    let timer;

    return function() {
      let context = this;
      let args = arguments;

      clearTimeout(timer);

      timer = setTimeout(function() {
        callback.apply(context, args);
      }, delay);
    };
  }

  $(document).on('keyup', '.search-input', debounce(function() {
      let $input = $(this);
      let keyword = $.trim($input.val());

      let $container = $input.closest('.ajax-search-box');
      let $results = $container.find('.search-results');
      let url = $container.data('url');

      let html = '';
      $results.html(html).show();

      if (keyword.length < 2) {
        $results.empty().hide();
        return;
      }

      if (currentRequest) {
        currentRequest.abort();
      }

      currentRequest = $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        data: {
          keyword: keyword
        },
        success: function(response) {
          $.each(response, function(index, item) {
            html += `<div class="search-item" data-id="${item.AccountID}" data-name="${item.company}"> ${item.company} - ${item.AccountID}</div>`;
          });

          $results.html(html).show();
        },
        error: function() {
          $results.empty().hide();
        }
      });

    }, 300)
  );

  $(document).on('click', '.search-item', function() {
    let $item = $(this);
    let $container = $item.closest('.ajax-search-box');
    $container.find('.search-input').val($item.data('id'));
    $container.find('.selected-id').val($item.data('id')).trigger('change');
    $container.find('.search-results').hide();
  });

  $(document).on('click', function(e) {
    if (!$(e.target).closest('.ajax-search-box').length) {
      $('.search-results').hide();
    }
  });
</script>

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
    }

    $('#resend_email').on('click', function() {
      var phoneNumber = $('#AccountID').val();
      var center = $('#center').val();
      var Commodity = $('#Commodity').val();
      var item = $('#item').val();
      var CurrentRate = $('#currentrate').val();
      var Quantity = $('#Quantity').val();
      var QuantityBag = $('#QuantityBag').val();
      var vehicle_number = $('#vehicle_number').val();
      var driver_mobile = $('#driver_mobile').val();
      if (phoneNumber == '') {
        alert('Enter mobile number');
      } else if (center == '') {
        alert('Select Center');
      } else if (Commodity == '') {
        alert('Select Commodity');
      } else if (item == '') {
        alert('Select Item');
      } else if (Quantity == '') {
        alert('Enter Quantity');
      // } else if (QuantityBag == '') {
      //   alert('Enter Bag Quantity');
      // }else if(vehicle_number == ''){
      //   alert('Enter Vehicle Number');
      // }else if(driver_mobile == ''){
      //   alert('Enter Driver Mobile Number');
      } else {
        $.ajax({
          url: "<?php echo site_url(); ?>authentication/sendOTP",
          method: "POST",
          dataType: "json",
          data: {
            phoneNumber: phoneNumber
          },
          success: function(data) {
            if (data == false) {
              console.log("Please register first, number does not exist in the database.");
              alert("Please enter your registered Mobile Number");
            } else {
              $(".send_otp_div").css("display", "none");
              $(".submit_otp_div").css("display", "block");
              $(".submit_otp").css("display", "block");
              $('#timer_id').css('display', 'block');
              $('#resend_otp').css('display', 'none');
              timer(120);
            }
          }
        });
      }
    });

    $('.send_otp').on('click', function() {
      var phoneNumber = $('#AccountID').val();
      var center = $('#center').val();
      var Commodity = $('#Commodity').val();
      var item = $('#item').val();
      var CurrentRate = $('#currentrate').val();
      var Quantity = $('#Quantity').val();
      var QuantityBag = $('#QuantityBag').val();
      var vehicle_number = $('#vehicle_number').val();
      var driver_mobile = $('#driver_mobile').val();
      if (phoneNumber == '') {
        alert('Enter mobile number');
      } else if (center == '') {
        alert('Select Center');
      } else if (Commodity == '') {
        alert('Select Commodity');
      } else if (item == '') {
        alert('Select Item');
      } else if (Quantity == '') {
        alert('Enter Quantity');
      // } else if (QuantityBag == '') {
      //   alert('Enter Bag Quantity');
        // }else if(vehicle_number == ''){
        //     alert('Enter Vehicle Number');
        // }else if(driver_mobile == ''){
        //     alert('Enter Driver Mobile Number');
      } else {
        $.ajax({
          url: "<?php echo site_url(); ?>authentication/sendOTP",
          method: "POST",
          dataType: "json",
          data: {
            phoneNumber: phoneNumber
          },
          success: function(data) {
            if (data == false) {
              console.log("Please register first, number does not exist in the database.");
              alert("Please enter your registered Mobile Number");
            } else {
              $(".send_otp_div").css("display", "none");
              $(".submit_otp_div").css("display", "block");
              $(".submit_otp").css("display", "block");
              $('#timer_id').css('display', 'block');
              $('#resend_otp').css('display', 'none');
              timer(120);
            }
          }
        });
      }
    });

    $('#verifyOTP').on('click', function() {
      var phoneNumber = $('#AccountID').val();
      var otp = $('#enter_otp').val();
      $.ajax({
        url: "<?php echo admin_url(); ?>order/verifyOTP",
        method: "POST",
        data: {
          phoneNumber: phoneNumber,
          otp: otp
        },
        success: function(data) {
          if (data) {
            $('#verifyOTP').css("display", "none");
            $('#conf_msg').css("display", "block");
            $('#is_otp_veryfied').val('1');
            $('#resend_otp').css("display", "none");
            $('#timer_id').css("display", "none");

            //alert('veryfication done');
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
<script>
  $('#AccountID').focus(function() {
    $(".send_otp_div").css("display", "none");
    $(".submit_otp_div").css("display", "none");
    $("#verifyOTP").css("display", "none");
    $("#conf_msg").css("display", "none");
    $("#timer_id").css("display", "none");
    $("#resend_otp").css("display", "none");
    $('#accountName').val('');
    $('#AccountID').val('');
    $('#AccountType').val('');
    $('#is_otp_veryfied').val('');
    $('#Quantity').val('');
    $('#currentrate').val('');
    $('#Mastercurrentrate').val('');
    $('#QuantityBag').val('');
    $('#vehicle_number').val('');
    $('#driver_mobile').val('');
    $('#center').val('');
    $("#center").selectpicker("refresh");
    $("#Commodity").find('option').remove();
    $("#Commodity").selectpicker("refresh");
    $("#item").find('option').remove();
    $("#item").selectpicker("refresh");
    $("#currentrate").attr('disabled', 'disabled');
  });

  $('#AccountID').on('change blur', function() {
    var AccountID = $('#AccountID').val();
    if (AccountID) {
      $.ajax({
        url: "<?php echo admin_url(); ?>order/fetchClientData",
        method: "POST",
        dataType: "JSON",
        data: {
          AccountID: AccountID,
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
            $(".send_otp_div").css("display", "none");
            $(".submit_otp_div").css("display", "none");
            $("#verifyOTP").css("display", "none");
            $("#conf_msg").css("display", "none");
            $("#timer_id").css("display", "none");
            $("#resend_otp").css("display", "none");
            alert('No records found');
            $('#accountName').val('');
            $('#AccountID').val('');
            $('#AccountType').val('');
            $('#is_otp_veryfied').val('');
          } else {
            //$(".send_otp_div").css("display", "block");
            $(".submit_otp_div").css("display", "none");
            $("#verifyOTP").css("display", "none");
            $("#conf_msg").css("display", "none");
            $("#timer_id").css("display", "none");
            $("#resend_otp").css("display", "none");
            $('#accountName').val(data.company);
            $('#AccountType').val(data.CustomerType);
            $('#is_otp_veryfied').val('');
            if (data.CustomerType == "1") {
              $("#currentrate").removeAttr('disabled');
            } else {
              $("#currentrate").attr('disabled', 'disabled');
            }
          }
          $('#Quantity').val('');
          $('#currentrate').val('');
          $('#Mastercurrentrate').val('');
          $('#QuantityBag').val('');
          $('#vehicle_number').val('');
          $('#driver_mobile').val('');
          $('#center').val('');
          $("#center").selectpicker("refresh");
          $("#Commodity").find('option').remove();
          $("#Commodity").selectpicker("refresh");
          $("#item").find('option').remove();
          $("#item").selectpicker("refresh");

        }
      });
    }

  });
</script>

<script>
  $('#center').on('change', function() {
    var id = $(this).val();
    $.ajax({
      url: "<?php echo admin_url(); ?>order/GetCommodity",
      dataType: "JSON",
      method: "POST",
      data: {
        CenterID: id
      },
      beforeSend: function() {
        $('.searchh2').css('display', 'block');
        $('.searchh2').css('color', 'blue');
      },
      complete: function() {
        $('.searchh2').css('display', 'none');
      },
      success: function(data) {
        $("#Commodity").find('option').remove();
        $("#Commodity").selectpicker("refresh");
        var html = "";
        html += '<option value=""></option>';
        for (var i = 0; i < data.length; i++) {
          html += '<option value="' + data[i].GroupCode + '">' + data[i].name + '</option>';
        }
        $('#Commodity').append(html);
        $('.selectpicker').selectpicker('refresh');

        $("#item").find('option').remove();
        $("#item").selectpicker("refresh");
        $('#currentrate').val('');
        $('#Mastercurrentrate').val('');
      }
    });
  })

  $('#Commodity').on('change', function() {
    var center = $("#center").val();
    var CommodityID = $("#Commodity").val();
    $.ajax({
      url: "<?php echo admin_url(); ?>order/GetItemId",
      dataType: "JSON",
      method: "POST",
      data: {
        center: center,
        CommodityID: CommodityID,
      },
      beforeSend: function() {
        $('.searchh2').css('display', 'block');
        $('.searchh2').css('color', 'blue');
      },
      complete: function() {
        $('.searchh2').css('display', 'none');
      },
      success: function(data) {
        $("#item").find('option').remove();
        $("#item").selectpicker("refresh");
        $('#item').append('<option value=""></option>');
        var html = "";
        for (var i = 0; i < data.length; i++) {
          html += '<option value="' + data[i].ItemID + '">' + data[i].ItemName + '</option>';
        }
        $('#item').append(html);
        $('.selectpicker').selectpicker('refresh');
        $('#currentrate').val('');
        $('#Mastercurrentrate').val('');
      }
    });
  })

  $('#item').on('change', function() {
    var center = $("#center").val();
    var item = $("#item").val();
    var AccountType = $("#AccountType").val();
    $.ajax({
      url: "<?php echo admin_url(); ?>order/fetchItemRate",
      dataType: "JSON",
      method: "POST",
      data: {
        center: center,
        item: item,
        AccountType: AccountType
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
          alert('Rate not found');
          $('#currentrate').val('');
          $('#Mastercurrentrate').val('');
        } else {
          $('#currentrate').val(data.Rate);
          $('#Mastercurrentrate').val(data.Rate);
        }
      }
    });
  })
</script>

<script>
  $('.asnBtn').on('click', function() {
    var AccountID = $('#AccountID').val();
    var AccountType = $('#AccountType').val();
    var center = $('#center').val();
    var Commodity = $('#Commodity').val();
    var item = $('#item').val();
    var CurrentRate = $('#currentrate').val();
    var Mastercurrentrate = $('#Mastercurrentrate').val();
    var Quantity = $('#Quantity').val();
    var MaxQuantity = $('#MaxQuantity').val();
    var QuantityBag = $('#QuantityBag').val();
    var vehicle_number = $('#vehicle_number').val();
    var driver_mobile = $('#driver_mobile').val();
    var is_otp_veryfied = $('#is_otp_veryfied').val();
    var TradeType = $('#TradeType').val();
    var FreightTerms = $('#FreightTerms').val();

    if (AccountID == '') {
      alert('Enter Account ID');
    } else if (center == '') {
      alert('Select Center');
    } else if (Commodity == '') {
      alert('Select Commodity');
    } else if (item == '') {
      alert('Select Item');
    } else if (Quantity == '') {
      alert('Enter Quantity');
    } else if (MaxQuantity == '') {
      alert('Enter Max Quantity');
    // } else if (QuantityBag == '') {
    //   alert('Enter Bag Quantity');
      // }else if(vehicle_number == '' && TradeType == "P"){
      //     alert('Enter Vehicle Number');
      // }else if(driver_mobile == ''  && TradeType == "P"){
      //     alert('Enter Driver Mobile Number');
    }
    /*else if(is_otp_veryfied == ''){
                alert('please veryfied your registered mobile number');
            }*/
    else {
      $.ajax({
        url: "<?php echo admin_url(); ?>order/SaveOrder",
        method: "POST",
        dataType: "JSON",
        data: {
          AccountID: AccountID,
          center: center,
          Commodity: Commodity,
          item: item,
          CurrentRate: CurrentRate,
          Mastercurrentrate: Mastercurrentrate,
          AccountType: AccountType,
          Quantity: Quantity,
          MaxQuantity: MaxQuantity,
          QuantityBag: QuantityBag,
          vehicle_number: vehicle_number,
          driver_mobile: driver_mobile,
          is_otp_veryfied: is_otp_veryfied,
          TradeType: TradeType,
          FreightTerms: FreightTerms
        },
        beforeSend: function() {
          $('.searchh2').css('display', 'block');
          $('.searchh2').css('color', 'blue');
        },
        complete: function() {
          $('.searchh2').css('display', 'none');
        },
        success: function(data) {
          if (data == true) {
            alert('Trade added successfully');
            window.location.reload();
          } else {
            alert('Error occured');
            window.location.reload();
          }
        }
      });
    }
  })
</script>

<script type="text/javascript">
  $('#Quantity').on('keypress', function(event) {
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
      event.preventDefault();
    }
    var input = $(this).val();
    if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 3)) {
      event.preventDefault();
    }
  });
</script>