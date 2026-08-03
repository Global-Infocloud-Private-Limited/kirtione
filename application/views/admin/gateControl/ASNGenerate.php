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
            <form action="" method="post" id="form_asn" enctype="multipart/form-data">
              <div class="row">
                <div class="col-md-12 text-centerr">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                      <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                      <li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
                      <li class="breadcrumb-item active" aria-current="page"><b>Generate ASN</b></li>

                    </ol>
                  </nav>
                  <hr class="hr_style" style="margin-Bottom:12px !important;">
                </div>
                <div class="col-md-12">
                  <div class="save_msg" style="display:none;">Please wait ASN generating.</div>
                  <div class="searchh2" style="display:none;">Please wait while fetching data.</div>
                  <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                  <div class="searchh4" style="display:none;">Please wait while updating data.</div>
                </div>
                <br>
                <div class="col-md-2">
                  <!-- <div class="form-group" app-field-wrapper="AccountID">
                    <small class="req text-danger">* </small>
                    <label for="AccountID" class="control-label">AccountID</label>
                    <input type="text" id="AccountID" name="AccountID" class="form-control" value="">
                    <input type="hidden" id="basic_rate" name="basic_rate" class="form-control" value="">
                    <input type="hidden" id="CurrentRate" name="CurrentRate" class="form-control" value="">
                    <input type="hidden" id="TType" name="TType" class="form-control" value="">
                    <input type="hidden" id="TType2" name="TType2" class="form-control" value="">
                  </div> -->
                  <div class="form-group" app-field-wrapper="AccountID">
                    <small class="req text-danger"> </small>
                    <label for="AccountID" class="control-label">AccountID</label>
                    <style>
                      .ajax-search-box{position:relative;width:100%;}
                      .search-results{position:absolute;top:100%;left:0;width:100%;background:#fff;border:1px solid #ddd;max-height:250px;overflow-y:auto;display:none;z-index:9999;}
                      .search-item{padding:10px;cursor:pointer;border-bottom:1px solid #eee;}
                      .search-item:hover{background:#f5f5f5;}
                    </style>
                    <div class="ajax-search-box" data-url="<?= admin_url('KirtiOneOrder/get_party'); ?>">
                      <input type="search" class="search-input form-control" placeholder="Search name... or Mobile number" value="">
                      <input type="hidden" class="selected-id" id="AccountID" name="AccountID" value="">
                      <input type="hidden" id="basic_rate" name="basic_rate" class="form-control" value="">
                      <input type="hidden" id="CurrentRate" name="CurrentRate" class="form-control" value="">
                      <input type="hidden" id="TType" name="TType" class="form-control" value="">
                      <input type="hidden" id="TType2" name="TType2" class="form-control" value="">
                      <div class="search-results"></div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group" app-field-wrapper="BookingID">
                    <small class="req text-danger">* </small>
                    <label for="BookingID" class="control-label">BookingID</label>
                    <input type="text" id="BookingID" name="BookingID" class="form-control" value="" required>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group" app-field-wrapper="CenterID">
                    <small class="req text-danger">* </small>
                    <label for="CenterID" class="control-label">CenterID</label>
                    <input type="text" id="CenterID" name="CenterID" class="form-control" value="" readonly required>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group" app-field-wrapper="PartyType">
                    <small class="req text-danger">* </small>
                    <label for="PartyType" class="control-label">Party Type</label>
                    <input type="text" id="PartyType" name="PartyType" class="form-control" value="" readonly required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group" app-field-wrapper="PartyName">
                    <small class="req text-danger">* </small>
                    <label for="PartyName" class="control-label">PartyName</label>
                    <input type="text" id="PartyName" name="PartyName" class="form-control" value="" readonly required>
                  </div>
                </div>


                <div class="col-md-2">
                  <div class="form-group" app-field-wrapper="Item">
                    <small class="req text-danger">* </small>
                    <label for="Item" class="control-label">Item Name</label>
                    <input type="text" id="Item" name="Item" class="form-control" value="" readonly required>
                    <input type="hidden" id="ItemRate" name="ItemRate" value="">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group" app-field-wrapper="Quantity">
                    <small class="req text-danger">* </small>
                    <label for="Quantity" class="control-label"> Trade Qty</label>
                    <input type="text" id="Quantity" name="Quantity" class="form-control" value="" readonly required>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group" app-field-wrapper="Unit">
                    <small class="req text-danger">* </small>
                    <label for="Unit" class="control-label">Trade Unit</label>
                    <input type="text" id="Unit" name="Unit" class="form-control" value="" readonly required>

                  </div>
                </div>

                <div class="col-md-2">
                  <div class="form-group" app-field-wrapper="asn_qty_bag">
                    <!-- <small class="req text-danger">* </small> -->
                    <label for="asn_qty_bag" class="control-label">Jute Bag</label>
                    <input type="text" id="asn_qty_bag" name="asn_qty_bag" class="form-control" value="" onkeypress="return isNumber(event)">
                  </div>
                </div>

                <div class="col-md-2">
                  <div class="form-group" app-field-wrapper="asn_qty_ppbag">
                    <!-- <small class="req text-danger">* </small> -->
                    <label for="asn_qty_ppbag" class="control-label">PP Bag</label>
                    <input type="text" id="asn_qty_ppbag" name="asn_qty_ppbag" class="form-control" value="" onkeypress="return isNumber(event)">
                  </div>
                </div>

                <div class="col-md-2">
                  <div class="form-group" app-field-wrapper="asn_qty_mt">
                    <small class="req text-danger">* </small>
                    <label for="asn_qty_mt" class="control-label">ASN / Invoice Weight(MT)</label>
                    <input type="text" id="asn_qty_mt" name="asn_qty_mt" class="form-control" value="" required>
                  </div>
                </div>

                <div class="col-md-2" id="InvoiceAmt">
                  <div class="form-group" app-field-wrapper="asn_amt">
                    <small class="req text-danger">* </small>
                    <label for="asn_amt" class="control-label">ASN / Invoice Amount</label>
                    <input type="text" id="asn_amt" name="asn_amt" class="form-control" value="" required>
                  </div>
                </div>
                <!-- <div class="clearfix"></div> -->
                <div class="col-md-2">
                  <?php
                  $from_date = date('d/m/Y');
                  $attr = array('disabled' => 'disabled');
                  echo render_date_input('delivery_date_time', 'Delivery DateTime', $from_date, $attr);
                  ?>
                </div>

                <div class="col-md-2">
                  <div class="form-group" app-field-wrapper="VehicleNo">
                    <small class="req text-danger">* </small>
                    <label for="VehicleNo" class="control-label">Vehicle No</label>
                    <input type="text" id="VehicleNo" name="VehicleNo" class="form-control" value="" required>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group" app-field-wrapper="Phone">
                    <small class="req text-danger">* </small>
                    <label for="Phone" class="control-label">Phone No</label>
                    <input type="tel" id="Phone" name="Phone" class="form-control" value="" maxlength="10" minlength="10" onkeypress="return isNumber(event)" required>
                  </div>
                </div>

                <div class="col-md-2">
                  <div class="form-group">
                    <?php
                    $value = date('d/m/Y');
                    echo render_date_input('asndate', 'ASN Date', $value, 'text');
                    ?>
                  </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-2" id="ven_inv_number">
                  <div class="form-group" app-field-wrapper="ven_inv_number">
                    <!-- <small class="req text-danger">* </small> -->
                    <label for="ven_inv_number" class="control-label">Vendor Invoice Number</label>
                    <input type="text" id="ven_inv_number" name="ven_inv_number" class="form-control" value="">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <?php $value = date('d/m/Y'); ?>
                    <?php echo render_date_input('ven_inv_date', 'Vendor Invoice Date', $value, 'text'); ?>
                  </div>
                </div>
                <div class="col-md-2" id="ven_inv_amt">
                  <div class="form-group" app-field-wrapper="ven_inv_amt">
                    <!-- <small class="req text-danger">* </small> -->
                    <label for="ven_inv_amt" class="control-label">Vendor Invoice Amount</label>
                    <input type="text" id="ven_inv_amt" name="ven_inv_amt" class="form-control" value="">
                  </div>
                </div>
                <div class="col-md-2" id="ven_invoice_doc">
                  <div class="form-group" app-field-wrapper="ven_invoice_doc">
                    <!-- <small class="req text-danger">* </small> -->
                    <label for="ven_invoice_doc" class="control-label">Vendor Invoice Doc</label>
                    <input type="file" id="ven_invoice_doc" name="ven_invoice_doc" class="form-control" value="" accept="image/*,application/pdf">
                  </div>
                </div>
                <div class="col-md-2" id="ven_eway_bill_number">
                  <div class="form-group" app-field-wrapper="ven_eway_bill_number">
                    <!-- <small class="req text-danger">* </small> -->
                    <label for="ven_eway_bill_number" class="control-label">Vendor Eway Bill Number</label>
                    <input type="text" id="ven_eway_bill_number" name="ven_eway_bill_number" class="form-control" value="">
                  </div>
                </div>
                <div class="col-md-2" id="ven_eway_bill_doc">
                  <div class="form-group" app-field-wrapper="ven_eway_bill_doc">
                    <!-- <small class="req text-danger">* </small> -->
                    <label for="ven_eway_bill_doc" class="control-label">Vendor Eway Bill Doc</label>
                    <input type="file" id="ven_eway_bill_doc" name="ven_eway_bill_doc" class="form-control" value="" accept="image/*,application/pdf">
                  </div>
                </div>

                <div class="col-md-8" id="SaleRepresentativeDiv" style="display:none;">
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group" app-field-wrapper="SalesPerson">
                        <small class="req text-danger">* </small>
                        <label for="SalesPerson" class="control-label">Sales Representative Name</label>
                        <input type="text" id="SalesPerson" name="SalesPerson" class="form-control" value="">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group" app-field-wrapper="Salesmobile">
                        <small class="req text-danger">* </small>
                        <label for="Salesmobile" class="control-label">Representative Mobile</label>
                        <input type="text" id="Salesmobile" name="Salesmobile" class="form-control" value="" maxlength="10" minlength="10" onkeypress="return isNumber(event)">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group" app-field-wrapper="SalesID">
                        <label for="SalesID" class="control-label">Representative ID</label>
                        <input type="file" id="SalesID" name="SalesID" class="form-control" value="">
                      </div>
                    </div>

                  </div>
                </div>

              </div>

              <div class="row">
                <div class="col-md-12">
                  <?php
                  if (has_permission_new('Ganerate_asn', '', 'create')) {
                  ?>
                    <button type="submit" class="btn btn-info asnBtn" style="margin-right: 25px;">Generate ASN</button>
                  <?php } ?>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!------------ Modal ------------->
<div class="modal fade cluster_list" id="cluster_list" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding:5px 10px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Trade List</h4>
      </div>
      <div class="modal-body" style="padding:0px 5px !important">

        <div class="table-trade_List">
          <table class="tree table table-striped table-bordered table-trade_List" id="table_trade_List" width="100%">
            <thead>
              <tr>
                <th style="text-align:left;">AccountID </th>
                <th style="text-align:left;">BookingID</th>
                <th style="text-align:left;">Qty/Unit</th>
                <th style="text-align:left;">Asn Qty</th>
                <th style="text-align:left;">In/Out Qty</th>
                <th style="text-align:left;">TType</th>
                <th style="text-align:left;">CenterID</th>
                <th style="text-align:left;">PartyType</th>
                <th style="text-align:left;">PartyName</th>
                <th style="text-align:left;">ItemID</th>
                <th style="text-align:left;">ItemName</th>
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
  </div>
</div>
<!------------ Modal ------------->
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
  $(document).ready(function() {

    $('#BookingID').keyup(function() {
      $(this).val($(this).val().toUpperCase());
    });
    $('#VehicleNo').keyup(function() {
      $(this).val($(this).val().toUpperCase());
    });
  });
</script>
<script>
  $('#BookingID').blur(function() {
    var BookingID = $('#BookingID').val();
    $.ajax({
      url: "<?php echo admin_url(); ?>GateControl/getSingleTrade",
      method: "POST",
      dataType: "JSON",
      data: {
        BookingID: BookingID,
      },
      success: function(data) {
        if (data.company == null) {
          var PartyName = data.firstname + ' ' + data.firstname;
        } else {
          var PartyName = data.company;
        }
        $('#CenterID').val(data.CenterID);
        $('#PartyName').val(PartyName);
        $('#AccountID').val(data.AccountID);
        $('#BookingID').val(data.BookingID);
        $('#PartyType').val(data.CustomerType);
        $('#Item').val(data.ItemID);
        if (data.e_quantity == "" || data.e_quantity == null) {
          var qty = data.quantity;
        } else {
          var qty = data.e_quantity;
        }
        $('#Quantity').val(qty);
        $('#Unit').val(data.unit);
        $('#basic_rate').val(data.basic_rate);
        $('#CurrentRate').val(data.CurrentRate);
        $('#TType').val(data.TType);
        $('#TType2').val(data.TType2);
        if (data.TType == "S") {
          $("#SaleRepresentativeDiv").css("display", "block");
        } else {
          $("#SaleRepresentativeDiv").css("display", "none");
        }
      }
    });
  });
</script>
<script>
  function fetchItemRate() {
    var item = $('#Item').val();
    var center = $('#CenterID').val();
    var AccountType = $('#PartyType').val();

    if (item && center && AccountType) {
      $.ajax({
        url: "<?php echo admin_url(); ?>GateControl/fetchItemRate",
        method: "POST",
        dataType: "JSON",
        data: {
          item: item,
          center: center,
          AccountType: AccountType
        },
        success: function(data) {
          if (data && data.Rate !== undefined) {
            $('#ItemRate').val(data.Rate);
          } else {
            $('#ItemRate').val('');
            alert("Rate not found.");
          }
        },
        error: function(xhr, status, error) {
          console.error("Error fetching rate:", error);
          alert("Error fetching rate: " + error);
        }
      });
    }
  }

  function fill_data(BookingID) {
    $('#cluster_list').modal('hide');
    $.ajax({
      url: "<?php echo admin_url(); ?>GateControl/getSingleTrade",
      method: "POST",
      dataType: "JSON",
      data: {
        BookingID: BookingID,
      },
      success: function(data) {
        if (data.company == null) {
          var PartyName = data.firstname + ' ' + data.firstname;
        } else {
          var PartyName = data.company;
        }
        $('#CenterID').val(data.CenterID);
        $('#PartyName').val(PartyName);
        $('#AccountID').val(data.AccountID);
        $('#BookingID').val(data.BookingID);
        $('#PartyType').val(data.CustomerType);
        $('#Item').val(data.ItemID);
        if (data.e_quantity == "" || data.e_quantity == null) {
          var qty = data.quantity;
        } else {
          var qty = data.e_quantity;
        }
        $('#Quantity').val(qty);
        $('#Unit').val(data.unit);
        $('#basic_rate').val(data.basic_rate);
        $('#CurrentRate').val(data.CurrentRate);
        $('#TType').val(data.TType);
        $('#TType2').val(data.TType2);
        if (data.TType == "S") {
          $("#SaleRepresentativeDiv").css("display", "block");
        } else {
          $("#SaleRepresentativeDiv").css("display", "none");
        }

        if (data.TType2 == "Purchase") {
          $('#asndate').prop('disabled', true);
        } else {
          $('#asndate').prop('disabled', false);
        }
        fetchItemRate();
      }
    });
  }
</script>
<script>
  $('.cancelBtn').click(function() {
    $('#form_asn')[0].reset();
    $('#AccountID').val('');
    $('#BookingID').val('');
    $('#basic_rate').val('');
    $('#CurrentRate').val('');
    $('#CenterID').val('');
    $('#PartyType').val('');
    $('#PartyName').val('');
    $('#Item').val('');
    $('#Quantity').val('');
    $('#Unit').val('');
    $('#asn_qty_bag').val('');
    $('#asn_qty_mt').val('');
    $('#VehicleNo').val('');
    $('#Phone').val('');
    $("#SaleRepresentativeDiv").css("display", "none");
  });
</script>
<script>
  $('#form_asn').submit(function(e) {
    e.preventDefault();
    $('.asnBtn').prop('disabled', true);
    var AccountID = $('#AccountID').val();
    var BookingID = $('#BookingID').val();
    var PartyType = $('#PartyType').val();
    var PartyName = $('#PartyName').val();
    var ItemID = $('#Item').val();
    var Quantity = $('#Quantity').val();
    var Unit = $('#Unit').val();
    var basic_rate = $('#basic_rate').val();
    var CurrentRate = $('#CurrentRate').val();
    var TType = $('#TType').val();
    var TType2 = $('#TType2').val();
    var asn_qty_bag = $('#asn_qty_bag').val();
    var asn_qty_ppbag = $('#asn_qty_ppbag').val();
    var asn_qty_mt = $('#asn_qty_mt').val();
    var asn_amt = $('#asn_amt').val();
    var VehicleNo = $('#VehicleNo').val();
    var Phone = $('#Phone').val();
    var SalesPerson = $('#SalesPerson').val();
    var Salesmobile = $('#Salesmobile').val();
    var AsnDate = $('#asndate').val();
    var ItemRate = $('#ItemRate').val();

    if (asn_qty_bag == "" || asn_qty_ppbag == "") {
      alert('please enter Jute/PP bag quantity');
      $('.asnBtn').prop('disabled', false);
      return false;
    } else if (asn_qty_mt == "") {
      alert("please enter ASN Qty in MT");
      $('.asnBtn').prop('disabled', false);
      return false;
    } else if (asn_amt == "") {
      alert("please enter ASN Amount");
      $('.asnBtn').prop('disabled', false);
      return false;
    } else if (SalesPerson == "" && TType == "S") {
      alert("please enter sales representative name");
      $('.asnBtn').prop('disabled', false);
      return false;
    } else if (Salesmobile == "" && TType == "S") {
      alert("please enter sales representative mobile number");
      $('.asnBtn').prop('disabled', false);
      return false;
    } else if ((AccountID == '') || (BookingID == '') || (PartyType == '') || (PartyName == '') || (ItemID == '') || (Quantity == '') || (Unit == '')) {
      alert('data loading error please refresh page and try again');
      $('.asnBtn').prop('disabled', false);
      return false;
    } else {
      if (PartyType == "1" && (parseFloat(basic_rate) != parseFloat(CurrentRate))) {
        var msg = 'Current rate and booking rate is not matched So current rate is : ' + parseFloat(CurrentRate) + ' and booking rate is : ' + parseFloat(basic_rate) + ' Do you want to continue with new rate';
        if (parseFloat(basic_rate) > parseFloat(CurrentRate)) {
          var NewRate = parseFloat(basic_rate);
        } else {
          var NewRate = parseFloat(CurrentRate);
        }
        if (!confirm(msg)) {
          $('.asnBtn').prop('disabled', false);
          return false;
        }
      }
    }

    $.ajax({
      url: "<?php echo admin_url(); ?>GateControl/GenerateASN",
      method: "POST",
      dataType: "JSON",
      cache: false,
      contentType: false,
      processData: false,
      data: new FormData(this),
      beforeSend: function() {
        $('.save_msg').css('display', 'block');
        $('.save_msg').css('color', 'blue');
      },
      complete: function() {
        $('.save_msg').css('display', 'none');
      },
      success: function(data) {
        $('.asnBtn').prop('disabled', false);
        if (data.result == true) {
          var ASNID = data.ASNID;
          var BookingID = data.BookingID;
          $('input').val('');
          $('.selectpicker').val('').selectpicker('refresh');
          alert("ASN Generated");
          window.open("<?= admin_url(); ?>GateControl/GenerateASNQR/" + BookingID + "/" + ASNID, "_blank");
        } else {
          alert('This Trade has been settled or cancel');
          window.location.reload(true);
        }
      }
    });
  })
  // $('.asnBtn').click(function() {
  //     $('.asnBtn').prop('disabled', true);
  //     var AccountID = $('#AccountID').val();
  //     var BookingID = $('#BookingID').val();
  //     var PartyType = $('#PartyType').val();
  //     var PartyName = $('#PartyName').val();
  //     var ItemID = $('#Item').val();
  //     var Quantity = $('#Quantity').val();
  //     var Unit = $('#Unit').val();
  //     var basic_rate = $('#basic_rate').val();
  //     var CurrentRate = $('#CurrentRate').val();
  //     var TType = $('#TType').val();
  //     var TType2 = $('#TType2').val();
  //     var asn_qty_bag = $('#asn_qty_bag').val();
  //     var asn_qty_mt = $('#asn_qty_mt').val();
  //     var asn_amt = $('#asn_amt').val();
  //     var VehicleNo = $('#VehicleNo').val();
  //     var Phone = $('#Phone').val();
  //     var SalesPerson = $('#SalesPerson').val();
  //     var Salesmobile = $('#Salesmobile').val();
  //     var AsnDate = $('#asndate').val();
  //     var ItemRate = $('#ItemRate').val();

  //     if (asn_qty_bag == "") {
  //         alert('please enter ASN Qty in bag');
  //         $('.asnBtn').prop('disabled', false);
  //     } else if (asn_qty_mt == "") {
  //         alert("please enter ASN Qty in MT");
  //         $('.asnBtn').prop('disabled', false);
  //     } else if (asn_amt == "") {
  //         alert("please enter ASN Amount");
  //         $('.asnBtn').prop('disabled', false);
  //     } else if (SalesPerson == "" && TType == "S") {
  //         alert("please enter sales representative name");
  //         $('.asnBtn').prop('disabled', false);
  //     } else if (Salesmobile == "" && TType == "S") {
  //         alert("please enter sales representative mobile number");
  //         $('.asnBtn').prop('disabled', false);
  //     } else if ((AccountID == '') || (BookingID == '') || (PartyType == '') || (PartyName == '') || (ItemID == '') || (Quantity == '') || (Unit == '')) {
  //         alert('data loading error please refresh page and try again');
  //         $('.asnBtn').prop('disabled', false);
  //     } else {
  //         if (PartyType == "1" && (parseFloat(basic_rate) != parseFloat(CurrentRate))) {
  //             var msg = 'Current rate and booking rate is not matched So current rate is : ' + parseFloat(CurrentRate) + ' and booking rate is : ' + parseFloat(basic_rate) + ' Do you want to continue with new rate';
  //             if (parseFloat(basic_rate) > parseFloat(CurrentRate)) {
  //                 var NewRate = parseFloat(basic_rate);
  //             } else {
  //                 var NewRate = parseFloat(CurrentRate);
  //             }
  //             if (confirm(msg)) {
  //                 $.ajax({
  //                     url: "<?php echo admin_url(); ?>GateControl/GenerateASN",
  //                     method: "POST",
  //                     dataType: "JSON",
  //                     data: {
  //                         AccountID: AccountID,
  //                         BookingID: BookingID,
  //                         ItemID: ItemID,
  //                         basic_rate: basic_rate,
  //                         Unit: Unit,
  //                         TType: TType,
  //                         TType2: TType2,
  //                         asn_amt: asn_amt,
  //                         asn_qty_bag: asn_qty_bag,
  //                         asn_qty_mt: asn_qty_mt,
  //                         VehicleNo: VehicleNo,
  //                         Phone: Phone,
  //                         SalesPerson: SalesPerson,
  //                         Salesmobile: Salesmobile,
  //                         AsnDate: AsnDate,
  //                         ItemRate: ItemRate
  //                     },
  //                     beforeSend: function() {
  //                         $('.save_msg').css('display', 'block');
  //                         $('.save_msg').css('color', 'blue');
  //                     },
  //                     complete: function() {
  //                         $('.save_msg').css('display', 'none');
  //                     },
  //                     success: function(data) {
  //                         $('.asnBtn').prop('disabled', false);
  //                         if (data.result == true) {
  //                             var ASNID = data.ASNID;
  //                             var BookingID = data.BookingID;
  //                             $('input').val('');
  //                             $('.selectpicker').val('').selectpicker('refresh');
  //                             alert("ASN Generated");
  //                             window.open("<?php echo admin_url(); ?>GateControl/GenerateASNQR/" + BookingID + "/" + ASNID, "_blank");
  //                         } else {
  //                             alert('This Trade has been settled or cancel');
  //                             window.location.reload(true);
  //                         }
  //                     }
  //                 });
  //             }
  //         } else {
  //             $.ajax({
  //                 url: "<?php echo admin_url(); ?>GateControl/GenerateASN",
  //                 method: "POST",
  //                 dataType: "JSON",
  //                 data: {
  //                     AccountID: AccountID,
  //                     BookingID: BookingID,
  //                     ItemID: ItemID,
  //                     basic_rate: basic_rate,
  //                     Unit: Unit,
  //                     TType: TType,
  //                     TType2: TType2,
  //                     asn_amt: asn_amt,
  //                     asn_qty_bag: asn_qty_bag,
  //                     asn_qty_mt: asn_qty_mt,
  //                     VehicleNo: VehicleNo,
  //                     Phone: Phone,
  //                     SalesPerson: SalesPerson,
  //                     Salesmobile: Salesmobile,
  //                     AsnDate: AsnDate,
  //                     ItemRate: ItemRate
  //                 },
  //                 beforeSend: function() {
  //                     $('.save_msg').css('display', 'block');
  //                     $('.save_msg').css('color', 'blue');
  //                 },
  //                 complete: function() {
  //                     $('.save_msg').css('display', 'none');
  //                 },
  //                 success: function(data) {
  //                     $('.asnBtn').prop('disabled', false);
  //                     if (data.result == true) {
  //                         var ASNID = data.ASNID;
  //                         var BookingID = data.BookingID;
  //                         $('input').val('');
  //                         $('.selectpicker').val('').selectpicker('refresh');
  //                         alert("ASN Generated");
  //                         window.open("<?php echo admin_url(); ?>GateControl/GenerateASNQR/" + BookingID + "/" + ASNID, "_blank");
  //                     } else {
  //                         alert('This Trade has been settled or cancel');
  //                         window.location.reload(true);
  //                     }
  //                 }
  //             });
  //         }
  //     }
  // });
</script>
<script>
  $('#AccountID').on('change', function() {
    $('#cluster_list').modal('show');
    $("#ListTableBody").html('');
    var i = 0;
    $('#cluster_list').on('shown.bs.modal', function() {
      $('#myInput1').focus();
      var AccountID = $('#AccountID').val();
      if (i == "0") {
        i++;
        $.ajax({
          url: "<?= admin_url('GateControl/Booking_for_ASNGenerate_ByAccountID'); ?>",
          //dataType:"JSON",
          method: "POST",
          cache: false,
          data: {
            AccountID: AccountID,
          },
          beforeSend: function() {
            $("#ListTableBody").html('');
          },
          success: function(data) {
            if (empty(data)) {
              $("#ListTableBody").html('');
            } else {
              $("#ListTableBody").html(data);
              $('.get_AccountID').click(function() {
                AccountID = $(this).attr("data-id");
                fill_data(AccountID);
              })
            }
          }
        })
      }
    })
  });

  $('#AccountID').dblclick(function() {
    $('#cluster_list').modal('show');
    var i = 0;
    $('#cluster_list').on('shown.bs.modal', function() {
      $('#myInput1').focus();
      var AccountID = "";
      if (i == "0") {
        i++;
        $.ajax({
          url: "<?php echo admin_url(); ?>GateControl/Booking_for_ASNGenerate",
          //dataType:"JSON",
          method: "POST",
          cache: false,
          data: {
            AccountID: AccountID,
          },
          success: function(data) {
            if (empty(data)) {

            } else {
              $("#ListTableBody").html(data);
              $('.get_AccountID').click(function() {
                AccountID = $(this).attr("data-id");
                fill_data(AccountID);
              })
            }
          }
        })
      }
    })
  });
</script>
<script>
  $('#AccountID').focus(function() {
    $('#AccountID').val('');
    $('#BookingID').val('');
    $('#basic_rate').val('');
    $('#CurrentRate').val('');
    $('#CenterID').val('');
    $('#PartyType').val('');
    $('#PartyName').val('');
    $('#Item').val('');
    $('#Quantity').val('');
    $('#Unit').val('');
    $('#asn_qty_bag').val('');
    $('#asn_qty_mt').val('');
    $('#VehicleNo').val('');
    $('#Phone').val('');
    $("#SaleRepresentativeDiv").css("display", "none");
  });
</script>
<script type="text/javascript">
  $('#asn_qty_mt').on('keypress', function(event) {
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
      event.preventDefault();
    }
    var input = $(this).val();
    if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
      event.preventDefault();
    }
  });

  $('#asn_amt').on('keypress', function(event) {
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
      event.preventDefault();
    }
    var input = $(this).val();
    if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
      event.preventDefault();
    }
  });
</script>
<script>
  function myFunction2() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInput1");
    filter = input.value.toUpperCase();
    table = document.getElementById("table_trade_List");
    tr = table.getElementsByTagName("tr");
    for (i = 1; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[0];
      td1 = tr[i].getElementsByTagName("td")[1];
      td2 = tr[i].getElementsByTagName("td")[2];
      td3 = tr[i].getElementsByTagName("td")[3];
      td4 = tr[i].getElementsByTagName("td")[4];
      td5 = tr[i].getElementsByTagName("td")[5];
      td6 = tr[i].getElementsByTagName("td")[6];
      td7 = tr[i].getElementsByTagName("td")[7];
      td8 = tr[i].getElementsByTagName("td")[8];
      td9 = tr[i].getElementsByTagName("td")[9];
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
                } else if (td5) {
                  txtValue = td5.textContent || td5.innerText;
                  if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                  } else if (td6) {
                    txtValue = td6.textContent || td6.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                      tr[i].style.display = "";
                    } else if (td7) {
                      txtValue = td7.textContent || td7.innerText;
                      if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                      } else if (td8) {
                        txtValue = td8.textContent || td8.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                          tr[i].style.display = "";
                        } else if (td9) {
                          txtValue = td9.textContent || td9.innerText;
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
          }
        }
      }
    }
  }
</script>