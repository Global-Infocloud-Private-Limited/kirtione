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
										<li class="breadcrumb-item active" aria-current="page"><b>Generate GateIN</b></li>

									</ol>
								</nav>
								<hr class="hr_style" style="margin-Bottom:12px !important;">
							</div>

							<div class="col-md-12">
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
									<input type="hidden" id="PartyID" name="PartyID" class="form-control" value="">
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
										<input type="hidden" id="PartyID" name="PartyID" class="form-control" value="">
										<div class="search-results"></div>
									</div>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="ASNID">
									<small class="req text-danger">* </small>
									<label for="ASNID" class="control-label">ASN ID</label>
									<input type="text" id="ASNID" name="ASNID" class="form-control" value="">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="BookingID">
									<small class="req text-danger">* </small>
									<label for="BookingID" class="control-label">BookingID</label>
									<input type="text" id="BookingID" name="BookingID" class="form-control" value="" readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="CenterID">
									<small class="req text-danger">* </small>
									<label for="CenterID" class="control-label">CenterID</label>
									<input type="text" id="CenterID" name="CenterID" class="form-control" value="" readonly>
								</div>
							</div>
							<!--<div class="col-md-2">
                                <div class="form-group" app-field-wrapper="PartyType">
                                    <small class="req text-danger">* </small>
                                    <label for="PartyType" class="control-label">Party Type</label>
                                    <input type="text" id="PartyType" name="PartyType" class="form-control" value="" readonly>
								</div>
							</div>-->

							<div class="col-md-4">
								<div class="form-group" app-field-wrapper="PartyName">
									<small class="req text-danger">* </small>
									<label for="PartyName" class="control-label">PartyName</label>
									<input type="text" id="PartyName" name="PartyName" class="form-control" value="" readonly>
								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Item">
									<small class="req text-danger">* </small>
									<label for="Item" class="control-label">Item Name</label>
									<input type="text" id="Item" name="Item" class="form-control" value="" readonly>
								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Quantity">
									<small class="req text-danger">* </small>
									<label for="Quantity" class="control-label">Trade Qty</label>
									<input type="text" id="Quantity" name="Quantity" class="form-control" value="" readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Unit">
									<small class="req text-danger">* </small>
									<label for="Unit" class="control-label">Trade Unit</label>
									<input type="text" id="Unit" name="Unit" class="form-control" value="" readonly>

								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="asn_qty_bag">
									<small class="req text-danger">* </small>
									<label for="asn_qty_bag" class="control-label">ASN / DO Bag</label>
									<input type="text" id="asn_qty_bag" name="asn_qty_bag" class="form-control" value="" onkeypress="return isNumber(event)" readonly>
								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="asn_qty_mt">
									<small class="req text-danger">* </small>
									<label for="asn_qty_mt" class="control-label">ASN / DO Weight(MT)</label>
									<input type="text" id="asn_qty_mt" name="asn_qty_mt" class="form-control" value="" readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="asn_date_time">
									<small class="req text-danger">* </small>
									<label for="asn_date_time" class="control-label">ASN DateTime</label>
									<input type="text" id="asn_date_time" name="asn_date_time" class="form-control" value="" readonly>
								</div>
							</div>



						</div>
						<div class="row">
							<form target="_blank" action="<?php echo admin_url(); ?>GateControl/GenerateGateInPass" id="gate_in_form" method="post" enctype="multipart/form-data">
								<input type="hidden" id="BID" name="BID" value="">
								<input type="hidden" id="CID" name="CID" value="">
								<input type="hidden" id="ASNID_hidden" name="ASNID_hidden" value="">
								<input type="hidden" id="ItemID" name="ItemID" value="">
								<input type="hidden" id="IsASNApprove" name="IsASNApprove" value="">
								<input type="hidden" id="TType" name="TType" value="">
								<input type="hidden" id="TType2" name="TType2" value="">
								<div class="col-md-2">
									<div class="form-group" app-field-wrapper="VehicleNo">
										<small class="req text-danger">* </small>
										<label for="VehicleNo" class="control-label">Vehicle No</label>
										<input type="text" id="VehicleNo" name="VehicleNo" class="form-control" value="">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group" app-field-wrapper="Phone">
										<small class="req text-danger">* </small>
										<label for="Phone" class="control-label">Phone No</label>
										<input type="tel" id="Phone" name="Phone" class="form-control" value="" maxlength="10" minlength="10" onkeypress="return isNumber(event)">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group" app-field-wrapper="VehicleImg">
										<!--<small class="req text-danger">* </small>-->
										<label for="VehicleImg" class="control-label">Vehicle Image</label>
										<input type="file" name="VehicleImg" id="VehicleImg" class="form-control">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group" app-field-wrapper="DriverImg">
										<!--<small class="req text-danger">* </small>-->
										<label for="DriverImg" class="control-label">Driver Image</label>
										<input type="file" name="DriverImg" id="DriverImg" class="form-control">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group" app-field-wrapper="GodownID">
										<small class="req text-danger">* </small>
										<label for="GodownID" class="control-label">Send Vehicle To</label>
										<select name="GodownID" id="GodownID" class="selectpicker form-control" data-live-search="true">
											<option value="">Non Selected</option>
											<!--<option value="Warehouse" >Warehouse</option>-->
											<!--<option value="Processing Unit" >Processing Unit</option>-->
										</select>
									</div>
								</div>

								<div class="col-md-2">
									<div class="form-group">
										<?php $value = date('d/m/Y'); ?>
										<?php echo render_date_input('gateindate', 'GateIn Date', $value, 'text'); ?>
									</div>
								</div>

								<div class="col-md-3" id="ChamberDiv" style="display:none">
									<div class="form-group" app-field-wrapper="Select Chamber">
										<label for="chamber" class="control-label">Select Chamber</label>
										<select name="chamber" id="chamber" class="selectpicker form-control" data-live-search="true">
											<option value="">Non Selected</option>
										</select>
									</div>
								</div>
								<div class="col-md-3" id="StackDiv" style="display:none">
									<div class="form-group" app-field-wrapper="Select Stack">
										<label for="Stack" class="control-label">Select Stack</label>
										<select name="Stack" id="Stack" class="selectpicker form-control" data-live-search="true">
											<option value="">Non Selected</option>
										</select>
									</div>
								</div>
								<div class="col-md-3" id="LotDiv" style="display:none">
									<div class="form-group" app-field-wrapper="Select LOT">
										<label for="LOTID" class="control-label">Select LOT</label>
										<select name="LOTID" id="LOTID" class="selectpicker form-control" data-live-search="true">
											<option value="">Non Selected</option>
										</select>
									</div>
								</div>

								<div class="col-md-3" id="SelectDeliveryType" style="display:none;">
									<div class="form-group" app-field-wrapper="DeliveryType">
										<small class="req text-danger">* </small>
										<label for="DeliveryType" class="control-label">Delivery Type</label>
										<select name="DeliveryType" id="DeliveryType" class="selectpicker form-control" data-live-search="true">
											<option value="">Non Selected</option>
											<option value="1">Ex-Factory</option>
											<option value="2">Delivery</option>
										</select>
									</div>
								</div>

								<div class="col-md-2" id="EnterDOAmount" style="display:none;">
									<div class="form-group" app-field-wrapper="DoAmount">
										<small class="req text-danger">* </small>
										<label for="DoAmount" class="control-label">DO Taxable Amount</label>
										<input type="text" name="DoAmount" id="DoAmount" class="form-control">
									</div>
								</div>

							</form>
						</div>
						<br>

						<div class="row">
							<div class="col-md-12">
								<?php
								if (has_permission_new('Ganerate_asn', '', 'view')) {
								?>
									<button type="button" class="btn btn-info viewBtn" style="margin-right: 25px;">View ASN</button>
								<?php } ?>
								<?php
								if (has_permission_new('Ganerate_gatein', '', 'create')) {
								?>
									<button type="button" class="btn btn-info getinBtn" style="margin-right: 25px;">Gate In Pass</button>

									<button type="button" class="btn btn-info ASNApprove" style="margin-right: 25px;">Approve ASN/ Generate DO</button>
									<button type="button" class="btn btn-info ViewDO" style="margin-right: 25px;">View DO</button>
								<?php } ?>

							</div>
						</div>
					</div>
					<!------------ Modal ------------->
					<div class="modal fade cluster_list" id="cluster_list" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content">
								<div class="modal-header" style="padding:5px 10px;">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title">ASN List</h4>
								</div>
								<div class="modal-body" style="padding:0px 5px !important">

									<div class="table-trade_List">
										<table class="tree table table-striped table-bordered table-trade_List" id="table_trade_List" width="100%">
											<thead>
												<tr>
													<th style="text-align:left;">AccountID </th>
													<th style="text-align:left;">ASN No.</th>
													<th style="text-align:left;">BookingID</th>
													<th style="text-align:left;">TType</th>
													<th style="text-align:left;">CenterID</th>
													<th style="text-align:left;">PartyType</th>
													<th style="text-align:left;">PartyName</th>
													<th style="text-align:left;">ItemName</th>
													<th style="text-align:left;">Qty Bags</th>
													<th style="text-align:left;">Weight MT</th>
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
	//============ Allow Only Number ===============================================
	function isNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode = 46 && charCode > 31 &&
			(charCode < 48 || charCode > 57)) {
			return false;
		}
		return true;
	}

	$('#asn_qty_mt,#DoAmount').keypress(function(event) {
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	});
</script>
<script>
	$(document).ready(function() {
		$('.viewBtn').hide();
		$('.ASNApprove').hide();
		$('.ViewDO').hide();

		$('#ASNID').keyup(function() {
			$(this).val($(this).val().toUpperCase());
		});
		$('#VehicleNo').keyup(function() {
			$(this).val($(this).val().toUpperCase());
		});
	});
</script>


<script>
	$('.viewBtn').click(function() {
		var BookingID = $('#BookingID').val();
		var ASNID = $('#ASNID').val();
		window.open("<?php echo admin_url(); ?>GateControl/viewAsn/" + BookingID + "/" + ASNID, "_blank");
		$('input').val('');
		$('.asnBtn').show();
		$('.getinBtn').hide();
		$('.viewGetin').hide();
		$('.viewBtn').hide();
		$('#no_show').hide();
		$('.ViewDO').hide();
	});

	$('.ViewDO').click(function() {
		var BookingID = $('#BookingID').val();
		var ASNID = $('#ASNID').val();
		window.open("<?php echo admin_url(); ?>GateControl/ViewDO/" + BookingID + "/" + ASNID, "_blank");

	});
</script>

<script>
	$('.ASNApprove').click(function() {
		var BookingID = $('#BookingID').val();
		var AccountID = $('#AccountID').val();
		var ASNID = $('#ASNID').val();
		var CenterID = $('#CenterID').val();
		var ItemID = $('#Item').val();
		var asn_qty_mt = $('#asn_qty_mt').val();
		var asn_qty_bag = $('#asn_qty_bag').val();
		var basic_rate = $('#basic_rate').val();
		var PartyID = $('#PartyID').val();
		var DeliveryType = $('#DeliveryType').val();
		var DoAmount = $('#DoAmount').val();
		var GodownID = $('#GodownID').val();
		if (BookingID == '' || AccountID == "" || ASNID == "" || CenterID == "") {
			alert('Please refresh page and try again');
		} else if (DeliveryType == "") {
			alert('Please select Delivery Type');
		} else if (DoAmount <= 0) {
			alert('please enter DO Amount');
		} else if (asn_qty_mt <= 0) {
			alert('please enter DO Weight in MT');
		} else if (asn_qty_bag <= 0) {
			alert("please enter DO Bag Qty");
		} else {
			$.ajax({
				url: "<?php echo admin_url(); ?>GateControl/GenerateDO",
				method: "POST",
				dataType: "JSON",
				data: {
					BookingID: BookingID,
					AccountID: AccountID,
					ASNID: ASNID,
					CenterID: CenterID,
					ItemID: ItemID,
					basic_rate: basic_rate,
					asn_qty_mt: asn_qty_mt,
					asn_qty_bag: asn_qty_bag,
					PartyID: PartyID,
					DeliveryType: DeliveryType,
					DoAmount: DoAmount,
					GodownID: GodownID
				},
				success: function(data) {
					if (data == true) {
						alert("ASN Approved and DO Generated Successfully");
						window.location.reload();
					} else {
						alert("Error While Crating DO");
						window.location.reload();
					}
				},
			});
		}
	});
	$('.getinBtn').click(function() {
		var BookingID = $('#BookingID').val();
		var AccountID = $('#AccountID').val();
		var CenterID = $('#CenterID').val();
		var GodownID = $('#GodownID :selected').val();
		var VehicleNo = $('#VehicleNo').val();
		var Phone = $('#Phone').val();
		var Stack = $('#Stack').val();
		var chamber = $('#chamber').val();
		var LOTID = $('#LOTID').val();
		var VehicleImg = $('#VehicleImg').val();
		var DriverImg = $('#DriverImg').val();
		var TType = $('#TType').val();
		var IsASNApprove = $('#IsASNApprove').val();
		var GateInDate = $('#gateindate').val();

		if (VehicleNo == '') {
			alert('Fill Vehicle Details !');
		} else if (Phone == '') {
			alert('Enter phone number !');
		} else if (CenterID == '') {
			alert('Select Center !');
		} else if (GodownID == '') {
			alert('Select Warehouse !');
		} else if (IsASNApprove == '' && TType == "S") {
			alert('Please Approve ASN..');
		} else {
			$('#gate_in_form').submit();
			$('input').val('');
			$('.getinBtn').show();
			$('.asnBtn').hide();
			$('.ViewDO').hide();
			$('.viewBtn').hide();
			var options = "<option value=''>Non selected</option>";

			$('select[name=GodownID]').html(options);
			$('.selectpicker').selectpicker('refresh');
			alert("GateIn Pass Generated");

		}

	});
</script>
<script>
	function fill_data(ASNID) {
		$('#cluster_list').modal('hide');
		$.ajax({
			url: "<?php echo admin_url(); ?>GateControl/GetSingleASN",
			method: "POST",
			dataType: "JSON",
			data: {
				ASNID: ASNID,
			},
			success: function(data) {
				if (data.company == null) {
					var PartyName = data.firstname + ' ' + data.firstname;
				} else {
					var PartyName = data.company;
				}
				if (data.TradeEQty == "" || data.TradeEQty == null) {
					var NewTradeqty = data.TradeQty;
				} else {
					var NewTradeqty = data.TradeEQty;
				}

				$('#AccountID').val(data.AccountID);
				$('#ASNID').val(data.ASNID);
				$('#ASNID_hidden').val(data.ASNID);
				$('#ItemID').val(data.ItemID);
				$('#BookingID').val(data.BookingID);
				$('#BID').val(data.BookingID);
				$('#CenterID').val(data.CenterID);
				$('#CID').val(data.CenterID);
				$('#PartyName').val(PartyName);
				$('#Item').val(data.ItemName);
				$('#Quantity').val(NewTradeqty);
				$('#Unit').val(data.unit);
				$('#asn_qty_bag').val(data.quantity);
				$('#asn_qty_mt').val(data.Asn_WT_MT);
				$('#asn_date_time').val(data.asn_date.substring(0, 19));
				$('#basic_rate').val(data.basic_rate);
				$('#PartyID').val(data.PartyID);
				$('#TType').val(data.TType);
				$('#TType2').val(data.TType2);
				$('#VehicleNo').val(data.VehicleNo);
				$('#Phone').val(data.Phone);
				$('#DeliveryType').val(data.DeliveryType);
				$('#DoAmount').val(data.InvoiceAmt);
				let WHLists = data.WHList;
				var options = "<option value=''>Non selected</option>";
				$.each(WHLists, function(index, value) {
					options += "<option value='" + value.AccountID + "'>" + value.w_name + "</option>";
				});
				$('select[name=GodownID]').html(options);
				$('.selectpicker').selectpicker('refresh');
				$('#GodownID').val(data.GodownID).selectpicker('refresh');
				$('.viewBtn').show();
				if (data.DOID == null && data.TType == "S") {
					$('.ASNApprove').show();
					// Show Select delivery type and DO amount field 
					$("#SelectDeliveryType").css("display", "block");
					$("#EnterDOAmount").css("display", "block");
					$("#asn_qty_bag").removeAttr("readonly");
					$("#asn_qty_mt").removeAttr("readonly");
					$('.ViewDO').hide();
				} else if (data.DOID != null && data.TType == "S") {
					$('.ViewDO').show();
					$('#IsASNApprove').val(data.DOID);
					$("#SelectDeliveryType").css("display", "block");
					$("#EnterDOAmount").css("display", "block");
				}

				if (data.TType2 == "Purchase") {
					$('#gateindate').prop('disabled', true);
				} else {
					$('#gateindate').prop('disabled', false);
				}

				const today = new Date();
				const formattedDate = today.getDate().toString().padStart(2, '0') + '/' +
					(today.getMonth() + 1).toString().padStart(2, '0') + '/' +
					today.getFullYear();
				$('#gateindate').val(formattedDate);
				$('.getinBtn').show();
			}
		});
	}

	function GetStackList(WHID) {
		$.ajax({
			url: "<?php echo admin_url(); ?>GateControl/GetStackList",
			method: "POST",
			dataType: "JSON",
			data: {
				WHID: WHID
			},
			success: function(fin) {
				$('#StackDiv').show();
				$('#LotDiv').hide();
				var options = "<option value=''>Non selected</option>";
				$.each(fin, function(index, value) {
					options += "<option value='" + value.StackID + "'>" + value.StackName + "</option>";
				});
				$('select[name=Stack]').html(options);
				$('.selectpicker').selectpicker('refresh');
			}
		});
	}

	function GetChamberList(WHID) {
		$.ajax({
			url: "<?php echo admin_url(); ?>GateControl/GetChamberList",
			method: "POST",
			dataType: "JSON",
			data: {
				WHID: WHID
			},
			success: function(fin) {
				$('#ChamberDiv').show();
				$('#StackDiv').hide();
				$('#LotDiv').hide();
				var options = "<option value=''>Non selected</option>";
				$.each(fin, function(index, value) {
					options += "<option value='" + value.CHID + "'>" + value.ChaumberName + "</option>";
				});
				$('select[name=chamber]').html(options);
				$('.selectpicker').selectpicker('refresh');
			}
		});
	}
	$('#GodownID').change(function() {
		var Value = $(this).val();
		// GetStackList(Value);
		//GetChamberList(Value);
	});

	$('#chamber').change(function() {
		var Value = $(this).val();
		$.ajax({
			url: "<?php echo admin_url(); ?>GateControl/GetWarehouseStackList",
			method: "POST",
			dataType: "JSON",
			data: {
				CHID: Value
			},
			success: function(fin) {
				$('#StackDiv').show();
				$('#LotDiv').hide();
				var options = "<option value=''>Non selected</option>";
				$.each(fin, function(index, value) {
					options += "<option value='" + value.StackID + "'>" + value.StackName + "</option>";
				});
				$('select[name=Stack]').html(options);
				$('.selectpicker').selectpicker('refresh');
			}
		});
	});

	$('#Stack').change(function() {
		var Value = $(this).val();
		$.ajax({
			url: "<?php echo admin_url(); ?>GateControl/GetStackLotList",
			method: "POST",
			dataType: "JSON",
			data: {
				StackID: Value
			},
			success: function(fin) {
				$('#LotDiv').show();
				var options = "<option value=''>Non selected</option>";
				$.each(fin, function(index, value) {
					options += "<option value='" + value.LOTID + "'>" + value.LotName + "</option>";
				});
				$('select[name=LOTID]').html(options);
				$('.selectpicker').selectpicker('refresh');
			}
		});
	});
</script>
<script>
	$('.cancelBtn').click(function() {
		$('.viewBtn').hide();
		$('.ASNApprove').hide();
		$('.getinBtn').show();
		$('#no_show_processing').hide();
		$('.GodownID').hide();
		$('#no_show').hide();
		$('input').val('');
		$("#SelectDeliveryType").css("display", "none");
		$("#EnterDOAmount").css("display", "none");
		$("#asn_qty_bag").attr("readonly", "readonly");
		$("#asn_qty_mt").attr("readonly", "readonly");
		$('.selectpicker').val('').selectpicker('refresh');
	});
</script>

<script>
	$('#AccountID').on('change', function() {
		$('#cluster_list').modal('show');
    $("#ListTableBody").html('');
		$('#cluster_list').on('shown.bs.modal', function() {
			$('#myInput1').focus();
			var AccountID = $('#AccountID').val();
			$.ajax({
				url: "<?php echo admin_url(); ?>GateControl/GetAsnListPopUpByAccountID",
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
							ASNID = $(this).attr("data-id");
							fill_data(ASNID);
						})
					}
				}
			})
		})
	});

	$('#AccountID').dblclick(function() {
		$('#cluster_list').modal('show');
		$('#cluster_list').on('shown.bs.modal', function() {
			$('#myInput1').focus();
			var AccountID = "";
			$.ajax({
				url: "<?php echo admin_url(); ?>GateControl/GetAsnListPopUp",
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
							ASNID = $(this).attr("data-id");
							fill_data(ASNID);
						})
					}
				}
			})
		})
	});
</script>
<script>
	$('#AccountID').focus(function() {
		$('.viewBtn').hide();
		$('.ASNApprove').hide();
		$('.getinBtn').show();
		$('#no_show_processing').hide();
		$('.GodownID').hide();
		$('#no_show').hide();
		$('input').val('');
		$("#SelectDeliveryType").css("display", "none");
		$("#EnterDOAmount").css("display", "none");
		$("#asn_qty_bag").attr("readonly", "readonly");
		$("#asn_qty_mt").attr("readonly", "readonly");
		$('.selectpicker').val('').selectpicker('refresh');
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