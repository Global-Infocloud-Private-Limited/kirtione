<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>



<div id="wrapper">

  <div id="pageLoader" style="display:none;">

		<div>

			<div class="spinner"></div>

			<p style="color:#fff; text-align:center;">Please wait...</p>

		</div>

	</div>

	<div class="content">

		<div class="row">

			<div class="col-md-12">

				<div class="panel_s">

					<div class="panel-body">

						<nav aria-label="breadcrumb">

            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">

            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>

            					<li class="breadcrumb-item active text-capitalize"><b>Master</b></li>

            					<li class="breadcrumb-item active" aria-current="page"><b>Import Statement</b></li>

							</ol>

						</nav>

                        <hr class="hr_style">

						<?php //echo $this->import->downloadStatementSampleFormHtml(); ?>

						<?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'import_form')) ;?>

						

						<div class="row">

							<div class="col-md-3">

								<?php 

									$select_attrs = [];

									echo render_select('BankAccount', $BankAccount, array('id','label'), 'Bank Account', '',$select_attrs); 

								?>

							</div>

							<div class="col-md-3">

								<?php echo form_hidden('items_import','true'); ?>

								<?php echo render_input('file_csv','choose_excel_file','','file'); ?>

							</div>

							<div class="col-md-4" style="margin-top:20px;">

								<div class="form-group">

									<button type="button" class="btn btn-info import btn-import-submit"><?php echo _l('import'); ?></button>

								</div>

							</div>

						</div>

						<?php echo form_close(); ?>

						

                        <hr class="hr_style">

						<div class="clearfix"></div>

						

						<div class="_buttons" style="margin-top:10px;">

							<div class="row">  

								<div class="col-md-3">

									<?php 

										$select_attrs = [];

										echo render_select('BankAccount2', $BankAccount, array('id','label'), 'Bank Account', '',$select_attrs); 

									?>

								</div>

								<div class="col-md-8" style="margin-top:20px;">

									<button class="btn btn-info pull-left mleft5 search_data " id="search_data"><?php echo _l('rate_filter'); ?></button>

									<button class="btn btn-warning pull-left mleft5 preview_data " id="preview_data" style="display:none;">Preview</button>

									<button class="btn btn-danger pull-left mleft5 delete_data " id="delete_data" style="display:none;">Delete</button>

									<button class="btn btn-primary pull-left mleft5" id="auto_generate" style="display:none;">Auto Generate</button>

								</div>

							</div>

							<div class="col-md-12">

							    <div id="loader" class="dots" style="display:none;">

  <span></span><span></span><span></span>

</div>

							</div>

							

						</div>

						<div class="clearfix"></div>

						<div class="table-daily_report">

							

							<table class="tree table table-striped table-bordered table-daily_report" id="table-daily_report" width="100%">

								

								<thead>

									<tr>

										<th class="sortable" style="text-align:left;width:3%;">IsDelete</th>

										<th class="sortable" style="text-align:left;width:3%;">Tag</th>

										<th class="sortable" style="text-align:left;width:4%;">Sr No.</th>

										<th class="sortable" style="text-align:left;width:7%;">System Entry Date</th>

										<th class="sortable" style="text-align:left;width:7%;">Post Date</th>

										<th class="sortable" style="text-align:left;width:7%;">Value Date</th>

										<th class="sortable" style="text-align:left;width:20%;">Account Description</th>

										<th class="sortable" style="text-align:left;width:15%;">Center</th>

										<th class="sortable" style="text-align:left;width:7%;">Cheque No.</th>

										<th class="sortable" style="text-align:left;width:6%;">Debit</th>

										<th class="sortable" style="text-align:left;width:6%;">Credit</th>

										<th class="sortable" style="text-align:left;width:25%;">Ledger Account</th>

									</tr>

								</thead>

								<tbody id="PendingEntry">

								    <?php

														$i=1;

											foreach($result as $each){

										?>

											<tr>

											<td><input type="checkbox" class="delete_selected_id" name="delete_selected_id" value="<?php echo $value["id"] ?>"></td>

											<td><input type="checkbox" class="selected_id" name="selected_id" value="<?php echo $value["id"]; ?>"></td>

											<td><?php echo $i; ?></td>

											<td><?php echo _d($each["TransDate"]); ?></td>

											<td><?php echo _d($each["transaction_date"]); ?></td>

											<td><?php echo _d($each["value_date"]); ?></td>

											<td><?php echo $each["description"]; ?></td>

											<td><?php echo $each["chq_ref_no"]; ?></td>

											<td><?php echo $each["debit"]; ?></td>

											<td><?php echo $each["credit"]; ?></td>

											<td class="LegerAccount">

												<input type="search" name="AccountID" id="AccountID" list="AccountsList" class="form-control AccountSearch" placeholder="Search Account..." value="<?= $value["LedgerAccountID"] ?? '';?>" onchange="validateAccountDatalist(this)">

												<!-- <select class="selectpicker" name="AccountID" id="AccountID" data-width="100%" data-action-box="true" data-hide-disabled="true" data-live-search="true" data-none-selected-text="None Selected">

													<option value="">None Selected</option> -->

													<?php

													// $selected = '';

													// foreach ($Accounts as $key => $value) {

													//   if($value["LedgerAccountID"] == $value["id"]){

													// 		$selected = 'selected';

													//   }

													// 	echo '<option value="'.$value["id"].'" '.$selected.'>'.$value["label"].'</option>';

													// }

													?>

												<!-- </select> -->

											</td>

											</tr>

											<?php

												$i++;

											}

												?>

								</tbody>

							</table>   

						</div>

						<datalist id="AccountsList">

							<?php

							foreach ($Accounts as $key => $value) {

								echo '<option value="'.$value["id"].'">'.$value["label"].'</option>';

							}

							?>

						</datalist>

						<datalist id="CenterList">

							<?php
							foreach ($center as $key => $value) {
								echo '<option value="' . $value['CenterID'] . '">';
							}
							?>

						</datalist>

						<span id="searchh2" style="display:none;">Loading.....</span>

						<div class="clearfix"></div>

						<br>

					</div>

				</div>

			</div>

		</div>

		

	</div>

</div>

<!-- Preview Modal -->

<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">

	<div class="modal-dialog modal-lg" role="document">

		<div class="modal-content">

			<div class="modal-header">

				<h5 class="modal-title"><b>Preview Selected Rows</b></h5>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">

					<span aria-hidden="true">&times;</span>

				</button>

			</div>

			<div class="modal-body">

				<div id="entryType" style="font-weight:bold; margin-bottom:10px;"></div>

				<table class="table table-bordered previewTable" id="previewTable">

					<thead>

						<tr>

							<th>#</th>

							<th>Date</th>

							<th>Description</th>

							<th>Cheque/Ref</th>

							<th>Debit</th>

							<th>Credit</th>

							<th>Ledger Account</th>

						</tr>

					</thead>

					<tbody>

			

						<!-- Rows will be added dynamically -->

					</tbody>

				</table>

			</div>

			<div class="modal-footer">

				<button type="button" class="btn btn-primary" id="submitPreview">Submit</button>

				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

			</div>

		</div>

	</div>

</div>



<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">

	<div class="modal-dialog modal-lg" role="document">

		<div class="modal-content">

			<div class="modal-header">

				<h5 class="modal-title"><b>Delete Selected Rows</b></h5>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">

					<span aria-hidden="true">&times;</span>

				</button>

			</div>

			<div class="modal-body">

				<table class="table table-bordered deleteTable" id="deleteTable">

					<thead>

						<tr>

							<th>#</th>

							<th>Date</th>

							<th>Description</th>

							<th>Cheque/Ref</th>

							<th>Debit</th>

							<th>Credit</th>

						</tr>

					</thead>

					<tbody>

						<!-- Rows will be added dynamically -->

					</tbody>

				</table>

			</div>

			<div class="modal-footer">

				<button type="button" class="btn btn-danger" id="deletePreview">Confirm Delete</button>

				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

			</div>

		</div>

	</div>

</div>





<div class="modal fade" id="previewModal2" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">

	<div class="modal-dialog modal-lg" role="document">

		<div class="modal-content">

			<div class="modal-header">
				<h6 class="modal-title"><b>Payment Voucher Generation</b></h6>
			</div>

			<div class="modal-body" style="max-height: 500px; overflow-y: auto;">

				<!-- <div style="font-weight:bold; margin-bottom:10px;">Payment Voucher Generation</div> -->

				<table class="tree table table-striped table-bordered previewTable2" id="previewTable2">

					<thead>
						<tr>
							<th style="background: #50607b;color: #fff !important;">#</th>
							<th style="background: #50607b;color: #fff !important;">Date</th>
							<th style="background: #50607b;color: #fff !important;">Description</th>
							<th style="background: #50607b;color: #fff !important;">Center</th>
							<th style="background: #50607b;color: #fff !important;">Cheque/Ref</th>
							<th style="background: #50607b;color: #fff !important;">Debit</th>
							<th style="background: #50607b;color: #fff !important;">Credit</th>
							<th style="background: #50607b;color: #fff !important;">Ledger Account</th>
						</tr>

					</thead>

					<tbody>

			

						<!-- Rows will be added dynamically -->

					</tbody>

				</table>

			</div>

			<div class="modal-footer">

				<button type="button" class="btn btn-primary" id="submitPreview2">Generate Payment Voucher</button>

				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

			</div>

		</div>

	</div>

</div>

<div style="display: none;" id="CenterListDiv">
	<select name="CenterList" id="CenterList" class="form-control CenterList" data-live-search="true" style="width: 100px;">
		<option value="" selected disabled>None Selected</option>
		<?php
		foreach ($center as $key => $value) {
			echo '<option value="' . $value['CenterID'] . '">' . $value['CenterName'] . '</option>';
		}
		?>
	</select>
</div>

<style>

	#pageLoader {

		position: fixed;

		top: 0;

		left: 0;

		width: 100%;

		height: 100%;

		background: rgba(0, 0, 0, 0.5); /* dark overlay */

		z-index: 99999;

		display: none;



		/* center loader */

		display: flex;

		justify-content: center;

		align-items: center;

	}



	.spinner {

		width: 50px;

		height: 50px;

		border: 6px solid #f3f3f3;

		border-top: 6px solid #3498db;

		border-radius: 50%;

		animation: spin 1s linear infinite;

	}



	@keyframes spin {

		100% { transform: rotate(360deg); }

	}

    .table-daily_report { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }

	.table-daily_report thead th { position: sticky; top: 0; z-index: 1; }

	.table-daily_report tbody th { position: sticky; left: 0; }

	

	/* Just common table stuff. Really. */

	.table-daily_report table  { border-collapse: collapse; width: 100%; }

	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}

	.table-daily_report th     { background: #50607b;color: #fff !important; }

	

    .previewTable { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }

	.previewTable thead th { position: sticky; top: 0; z-index: 1; }

	.previewTable tbody th { position: sticky; left: 0; }

	

	/* Just common table stuff. Really. */

	.previewTable table  { border-collapse: collapse; width: 100%; }

	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}

	.previewTable th     { background: #50607b;color: #fff !important; }

	

	.deleteTable { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }

	.deleteTable thead th { position: sticky; top: 0; z-index: 1; }

	.deleteTable tbody th { position: sticky; left: 0; }

	

	/* Just common table stuff. Really. */

	.deleteTable table  { border-collapse: collapse; width: 100%; }

	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}

	.deleteTable th     { background: #50607b;color: #fff !important; }

</style>

<?php init_tail(); ?>

<script type="text/javascript" language="javascript" >

$(document).ready(function(){

	$('.selectpicker').selectpicker('refresh');

    function formatDate(dateStr) {

        if (!dateStr) return '';

        let d = new Date(dateStr);

        return d.toLocaleDateString('en-GB'); // dd-mm-yyyy

    }

	function load_data(BankAccount)

	{

		$.ajax({

			url:"<?php echo admin_url(); ?>rate_master/GetPendingStatement",

			dataType:"JSON",

			method:"POST",

			data:{BankAccount:BankAccount},

			beforeSend: function () {

				$('#PendingEntry').html('');

			  $("#pageLoader").show();

				$('#searchh2').css('display','block');

				$('.table-daily_report tbody').css('display','none');

			},

			complete: function () {

				$('.table-daily_report tbody').css('display','');

				$('#searchh2').css('display','none');

				$("#pageLoader").hide();

			},

			success:function(response){

			    let html = '';

                let i = 1;

        

                $.each(response.result, function(index, each) {

										html = '';

                    html += '<tr>';

                    html += '<td><input type="checkbox" class="delete_selected_id" value="'+each.id+'"></td>';

                    html += '<td><input type="checkbox" class="selected_id" value="'+each.id+'"></td>';

                    html += '<td>'+i+'</td>';

                    html += '<td>'+formatDate(each.TransDate)+'</td>';

                    html += '<td>'+formatDate(each.transaction_date)+'</td>';

                    html += '<td>'+formatDate(each.value_date)+'</td>';

        

                    html += '<td>'+each.description+'</td>';

										html += '<td data-id="'+each.id+'"><input type="search" name="CenterID" id="CenterID'+each.id+'" list="CenterList" class="form-control CenterSearch" placeholder="Search Center..." value="" onchange="validateCenterDatalist(this)"></td>';

                    html += '<td>'+each.chq_ref_no+'</td>';

                    html += '<td>'+each.debit+'</td>';

                    html += '<td>'+each.credit+'</td>';

                    // Dropdown

                    html += '<td class="LegerAccount">';

										html += '<input type="search" name="AccountID" id="AccountID" list="AccountsList" class="form-control AccountSearch" placeholder="Search Account..." value="'+(each.LedgerAccountID ?? '')+'" onchange="validateAccountDatalist(this)">';

                    // html += '<select name = "AccountID" id="AccountID" class="selectpicker AccountID" data-width="100%" data-live-search="true">';

                    // html += '<option value="">None Selected</option>';

                    // var selected = '';

                    // let rowClass = '';

                    // $.each(response.accounts, function(k, val) {

                    //     if(val.id == each.LedgerAccountID){

                    //         selected = 'selected';

                    //         rowClass = 'ledger-selected';

                    //     }

                    //     html += '<option value="'+val.id+'" '+selected+'>'+val.label+'</option>';

                    // }); 

                    // html += '</select>';

                    html += '</td>';

                    html += '</tr>';

                    i++;

										$('#PendingEntry').append(html);

                });



								if(response.result.length > 0) {

									$('#auto_generate').show();

								}else {

									$('#auto_generate').hide();

								}

        

                // $('#PendingEntry').html(html);

        

                // Refresh selectpicker

                // $('.selectpicker').selectpicker('refresh');

				/*$('#PendingEntry').html(data);

				$('.selectpicker').selectpicker('destroy').selectpicker({

					container: 'body',

					dropupAuto: false

				});

				$('.selectpicker').selectpicker('refresh');*/

			}

		});

	}

	//formatDate();

	$('#search_data').on('click',function(){

		var BankAccount = $("#BankAccount2").val();

		if(BankAccount == '' || BankAccount == null){

			alert('Please Select Account');

		}else{

			load_data(BankAccount);

		}

	});

	$('#table-daily_report').on('click', '.selected_id', function() {

			var $checkbox = $(this);

			var $row = $checkbox.closest('tr');

			var ledgerSelect = $row.find('.LegerAccount input[type="search"]'); // Assuming the select is inside a td with class 'LegerAccount'

			var debit = parseFloat($row.find('td').eq(9).text().trim()) || 0;

			var credit = parseFloat($row.find('td').eq(10).text().trim()) || 0;



			let startDate = new Date(<?= 2000 + $FY; ?>, 3, 1);  // 01-Apr

			let endDate   = new Date(<?= 2001 + $FY; ?>, 2, 31); // 31-Mar

			let transdate  = $row.find('td').eq(4).text().trim(); // dd/mm/yyyy



			// Convert "dd/mm/yyyy" to Date object

			let parts = transdate.split('/');

			let rowDate = new Date(parts[2], parts[1] - 1, parts[0]);

			

			// Check if within FY range

			if (rowDate < startDate || rowDate > endDate) {

				alert('This record is not within the current financial year.');

				$checkbox.prop('checked', false);

				return;

			}

			

			if ($checkbox.is(':checked')) {

				// Check if LedgerAccount is selected

				if (!ledgerSelect.val()) {

					alert('Please select an account before selecting the checkbox.');

					$checkbox.prop('checked', false);

					return;

				}

				

				// Determine if this is a credit or debit record

				var isCredit = credit > 0;

				var isDebit = debit > 0;

				

				if (!isCredit && !isDebit) {

					alert('Record must have either debit or credit greater than 0.');

					$checkbox.prop('checked', false);

					return;

				}

				

				// Check all selected checkboxes match the same type

				var conflict = false;

				$('#table-daily_report .selected_id:checked').each(function() {

					var $otherRow = $(this).closest('tr');

					var otherDebit = parseFloat($otherRow.find('td').eq(9).text().trim()) || 0;

					var otherCredit = parseFloat($otherRow.find('td').eq(10).text().trim()) || 0;

					

					if (isCredit && otherCredit <= 0) {

						conflict = true;

						return false; // break each loop

					}

					if (isDebit && otherDebit <= 0) {

						conflict = true;

						return false; // break each loop

					}

				});

				

				if (conflict) {

					alert('You can only select records of the same type: either all credit or all debit.');

					$checkbox.prop('checked', false);

					return;

				}

			}

		});	

		

	$('#delete_data').on('click', function() {

		var rowsData = [];

		var entryType = '';

		var valid = true;

		$('#table-daily_report .delete_selected_id:checked').each(function(index) {

			var $row = $(this).closest('tr');

			var transDate = $row.find('td').eq(4).text().trim();

			var description = $row.find('td').eq(6).text().trim();

			var chqRef = $row.find('td').eq(8).text().trim();

			var debit = $row.find('td').eq(9).text().trim();

			var credit = $row.find('td').eq(10).text().trim();

			

			rowsData.push({

				index: index + 1,

				transDate: transDate,

				description: description,

				chqRef: chqRef,

				debit: debit,

				credit: credit,

			});

		});

		

		

		if(rowsData.length === 0) {

			alert('Please select at least one row to preview.');

			return;

		}

		

		// Clear previous rows

		$('#deleteTable tbody').empty();

		

		// Add new rows

		rowsData.forEach(function(row) {

			$('#deleteTable tbody').append(

            '<tr>' +

            '<td>' + row.index + '</td>' +

            '<td>' + row.transDate + '</td>' +

            '<td>' + row.description + '</td>' +

            '<td>' + row.chqRef + '</td>' +

            '<td>' + row.debit + '</td>' +

            '<td>' + row.credit + '</td>' +

            '</tr>'

			);

		});

		

		// Open the modal

		$('#deleteModal').modal('show');

	});

	$('#deletePreview').on('click', function() {

		var entries = [];

		var valid = true;

		var entryType = ''; // Will be 'Receipt' or 'Payment'

		

		$('#table-daily_report .delete_selected_id:checked').each(function() {

			var $row = $(this).closest('tr');

			

			var id = $(this).val();

			var debit = $row.find('td').eq(9).text().trim();

			var credit = $row.find('td').eq(10).text().trim();

			

			entries.push({

				id: id

			});

		});

		

		if (entries.length === 0) {

			alert('Please select at least one entry.');

			return;

		}

		

		if(confirm('Are You Sure !!! You Want To Delete records ?')){

			$.ajax({

				url: "<?php echo admin_url(); ?>rate_master/DeleteStatementVoucher",

				dataType: "JSON",

				method: "POST",

				data: {

					EntryType: entryType,

					entries: entries

				},

				beforeSend: function () {

				    $("#pageLoader").show();

				},

				complete: function () {

					$("#pageLoader").hide();

				},

				success: function(response) {

					if(response.status === true || response.status === 'true') {

						alert(response.msg);

						$('#deleteModal').modal('hide');

						location.reload();

					} else {

						alert('Data submission failed. Please try again.');

					}

				},

			});

		}

	});

	$('#preview_data').on('click', function() {

		var rowsData = [];

		var entryType = '';

		var valid = true;

		$('#table-daily_report .selected_id:checked').each(function(index) {
			var $row = $(this).closest('tr');
			var transDate = $row.find('td').eq(4).text().trim();
			var description = $row.find('td').eq(6).text().trim();
			var centerID = $row.find('td').eq(7).find('input[type="search"]').val().trim();
			var chqRef = $row.find('td').eq(8).text().trim();
			var debit = $row.find('td').eq(9).text().trim();
			var credit = $row.find('td').eq(10).text().trim();
			var LedgerAccount = $row.find('td').eq(11).find('input[type="search"]').val().trim();
			// Check if ledger account is selected
			/*if(LedgerAccount === "" || LedgerAccount === "None Selected") {
				valid = false;
				return false; // break out of the each loop
			}*/
			
			if(index === 0) {
				if(parseFloat(credit) > 0) {
					entryType = 'Receipt Voucher Entry';
					Btnlabel = "Ganerate Receipts Voucher";
				} else if(parseFloat(debit) > 0) {
					entryType = 'Payment Voucher Entry';
					Btnlabel = "Payment Voucher Reconcile";
				}
			}
			
			$('#entryType').text(entryType);
			$('#submitPreview').text(Btnlabel);
			rowsData.push({
				index: index + 1,
				transDate: transDate,
				description: description,
				chqRef: chqRef,
				debit: debit,
				credit: credit,
				LedgerAccount: LedgerAccount,
				centerID: centerID
			});
		});
		
		if(!valid) {
			alert('Please select a Ledger Account for all selected rows.');
			return;
		}
		
		if(rowsData.length === 0) {
			alert('Please select at least one row to preview.');
			return;
		}
		
		// Clear previous rows
		$('#previewTable tbody').empty();
		
		// Add new rows
		rowsData.forEach(function(row) {
			$('#previewTable tbody').append(
            '<tr>' +
            '<td>' + row.index + '</td>' +
            '<td>' + row.transDate + '</td>' +
            '<td>' + row.description + '</td>' +
            '<td>' + row.chqRef + '</td>' +
            '<td>' + row.debit + '</td>' +
            '<td>' + row.credit + '</td>' +
            '<td>' + row.LedgerAccount + '</td>' +
            '</tr>'
			);
		});
		
		// Open the modal
		$('#previewModal').modal('show');
	});

	// Handle submit button click inside modal

	$('#submitPreview').on('click', function() {
		var entries = [];
		var valid = true;
		var entryType = ''; // Will be 'Receipt' or 'Payment'

		$('#table-daily_report .selected_id:checked').each(function() {
			var $row = $(this).closest('tr');
			
			var id = $(this).val();
			var transDate = $row.find('td').eq(4).text().trim();
			var description = $row.find('td').eq(6).text().trim();
			var centerID = $row.find('td').eq(7).find('input[type="search"]').val().trim();
			var chqRef = $row.find('td').eq(8).text().trim();
			var debit = $row.find('td').eq(9).text().trim();
			var credit = $row.find('td').eq(10).text().trim();
			var ledgerAccount = $row.find('td').eq(11).find('input[type="search"]').val().trim();
			
			// Ensure ledger account is selected
			/*if (ledgerAccount === "" || ledgerAccount == null) {
				valid = false;
				return false; // Break loop

			}*/
		
			// Determine entry type based on credit value
			if (credit !== "0.00" && credit !== "0") {
				entryType = 'Receipt';
			} else if (debit !== "0.00" && debit !== "0") {
				entryType = 'Payment';
			}
			
			entries.push({
				id: id,
				ledgerAccount: ledgerAccount,
				centerID: centerID,
				chqRef: chqRef,
				transDate: transDate,
				description: description,
				debit: debit,
				credit: credit
			});
		});
		
		if (!valid) {
			alert('Please select a Ledger Account for all selected entries.');
			return;
		}
		
		if (entries.length === 0) {
			alert('Please select at least one entry.');
			return;
		}
		
		if(confirm('Are You Sure !!! You Want To Proceed ?')){
			$.ajax({
				url: "<?php echo admin_url(); ?>rate_master/GenerateStatementVoucher",
				dataType: "JSON",
				method: "POST",
				data: {
					EntryType: entryType,
					entries: entries
				},
				beforeSend: function () {
				    $("#pageLoader").show();
				},
				complete: function () {
					$("#pageLoader").hide();
				},
				success: function(response) {
					if(response.status === true || response.status === 'true') {

						alert(response.msg);

						$('#previewModal').modal('hide');

						location.reload();

					} else {

						alert(response.msg);

					}

				},

			});

		}

	});

		

});

	

	$('#auto_generate').on('click', function() {

		$('#table-daily_report tr').each(function() {

			$(this).find('.selected_id').prop('checked', true).trigger('click');

		});

	});

	

	$('#Rate,#BasicRate,#dis_per,#dis_per2').on('keypress',function (event) {

		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {

			event.preventDefault();

		}

		var input = $(this).val();

		if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {

			event.preventDefault();

		}

	});

	

	function validateAccountDatalist(elem) {
    let input = elem.value.trim();
    let list  = document.getElementById("AccountsList").options;

    for (let i = 0; i < list.length; i++) {
        if (list[i].value === input) {
            return true;  // valid, do nothing
        }
    }
    // invalid → clear field
    elem.value = "";
	}
	
	function validateCenterDatalist(elem) {
    let input = elem.value.trim();
    let list  = document.getElementById("CenterList").options;

    for (let i = 0; i < list.length; i++) {
        if (list[i].value === input) {
            return true;  // valid, do nothing
        }
    }
    // invalid → clear field
    elem.value = "";
	}



	document.addEventListener("change", function (e) {

		if (e.target.classList.contains("delete_selected_id")) {

			let deleteChecked = document.querySelectorAll(".delete_selected_id:checked").length > 0;

			deleteChecked ? $('#delete_data').show() : $('#delete_data').hide();

		}

		if (e.target.classList.contains("selected_id")){

			let previewChecked = document.querySelectorAll(".selected_id:checked").length > 0;

			previewChecked ? $('#preview_data').show() : $('#preview_data').hide();

		}

	});



	$("#auto_generate").on("click", function () {

    let found = false;

		let startDate = new Date(<?= 2000 + $FY; ?>, 3, 1);  // 01-Apr

		let endDate   = new Date(<?= 2001 + $FY; ?>, 2, 31); // 31-Mar

		

		$("#PendingEntry tr").each(function () {

			let description = $(this).find("td:eq(6)").text().trim();

			let transdate  = $(this).find("td:eq(4)").text().trim(); // dd/mm/yyyy



			// Convert "dd/mm/yyyy" to Date object

			let parts = transdate.split('/');

			let rowDate = new Date(parts[2], parts[1] - 1, parts[0]);

			

			// Check if within FY range

			if (rowDate < startDate || rowDate > endDate) {

				return; // skip this row

			}



			if (description.includes("CASHRC:")) {
					found = true;
					$(this).find(".selected_id").prop("checked", true);
					$(this).find("input[type='search'].AccountSearch").val("CASH");
			} else if (description.includes("CASH DEPOSIT CHG")) {
					found = true;
					$(this).find(".selected_id").prop("checked", true);
					$(this).find("input[type='search'].AccountSearch").val("BANKCHR");
			} else if (description.includes("CASH HANDLING CHARGE")) {
					found = true;
					$(this).find(".selected_id").prop("checked", true);
					$(this).find("input[type='search'].AccountSearch").val("BANKCHR"); // CASHHAD
			} else if (description.includes("NEFT PHONEPE LIMITED")) {
					found = true;
					$(this).find(".selected_id").prop("checked", true);
					$(this).find("input[type='search'].AccountSearch").val("PHONEPAY");
			} else if (description.includes("NonP2PM QR Rent")) {
					found = true;
					$(this).find(".selected_id").prop("checked", true);
					$(this).find("input[type='search'].AccountSearch").val("BANKCHR");
 			} else if (description.includes("PC:SMS CHARGES+GST")) {
					found = true;
					$(this).find(".selected_id").prop("checked", true);
					$(this).find("input[type='search'].AccountSearch").val("BANKCHR");
 			} else if (description.includes("QR Rent")) {
					found = true;
					$(this).find(".selected_id").prop("checked", true);
					$(this).find("input[type='search'].AccountSearch").val("BANKCHR");
 			} else {
				switch (description) {
					case "BANK COMMISSION":
						found = true;
						$(this).find(".selected_id").prop("checked", true);
						$(this).find("input[type='search'].AccountSearch").val("BANKCHR"); // BCOMM
						break;
					case "GST":
						found = true;
						$(this).find(".selected_id").prop("checked", true);
						$(this).find("input[type='search'].AccountSearch").val("BANKCHR"); //GSTCOMM
						break;
					default:
						break;
				}
			}

    });

    if (!found) {

			alert("No matching entries within this financial year!");

    }else{

			previewSelectedEntries();

		}

	});



	function previewSelectedEntries() {

		var rowsData = [];

		var entryType = '';

		var valid = true;

		$('#table-daily_report .selected_id:checked').each(function(index) {

			var $row = $(this).closest('tr');

			var transDate = $row.find('td').eq(4).text().trim();

			var description = $row.find('td').eq(6).text().trim();
			
			var id = $row.find('td').eq(7).attr('data-id');

			var centerID = $row.find('td').eq(7).find('input[type="search"]').val().trim();

			var chqRef = $row.find('td').eq(8).text().trim();

			var debit = $row.find('td').eq(9).text().trim();

			var credit = $row.find('td').eq(10).text().trim();

			var LedgerAccount = $row.find('td').eq(11).find('input[type="search"]').val().trim();

			

			rowsData.push({
				id : id,

				index: index + 1,

				transDate: transDate,

				description: description,

				chqRef: chqRef,

				debit: debit,

				credit: credit,

				LedgerAccount: LedgerAccount,

				CenterID: centerID
			});

		});

		

		if(!valid) {

			alert('Please select a Ledger Account for all selected rows.');

			return;

		}

		

		if(rowsData.length === 0) {

			alert('Please select at least one row to preview.');

			return;

		}

		

		// Clear previous rows

		$('#previewTable2 tbody').empty();

		// Add new rows

		rowsData.forEach(function(row) {

			$('#previewTable2 tbody').append(`<tr>
					<td>${row.index}</td>
					<td>${row.transDate}</td>
					<td>${row.description}</td>
					<td>
						<input type="search" name="CenterID" id="CenterID" list="CenterList" class="form-control CenterSearch" placeholder="Search Center..." value="${row.CenterID}" onchange="validateCenterDatalist(this); $('#CenterID${row.id}').val(this.value);">
					</td>
					<td>${row.chqRef}</td>
					<td>${row.debit}</td>
					<td>${row.credit}</td>
					<td>${row.LedgerAccount}</td>
				</tr>`
			);
		});

		$('.CenterList').selectpicker('refresh');

		// Open the modal

		$('#previewModal2').modal('show');

	}



	$('#submitPreview2').on('click', function() {

		var entries = [];

		var valid = true;

		var entryType = ''; // Will be 'Receipt' or 'Payment'

		var amount = 0;

		

		$('#table-daily_report .selected_id:checked').each(function() {

			var $row = $(this).closest('tr');

			

			var id = $(this).val();

			var transDate = $row.find('td').eq(4).text().trim();

			var description = $row.find('td').eq(6).text().trim();

			var centerID = $row.find('td').eq(7).find('input[type="search"]').val().trim();

			var chqRef = $row.find('td').eq(8).text().trim();

			var debit = $row.find('td').eq(9).text().trim();

			var credit = $row.find('td').eq(10).text().trim();

			var ledgerAccount = $row.find('td').eq(11).find('input[type="search"]').val().trim();



			// Determine entry type based on credit value

			if (credit !== "0.00" && credit !== "0") {

				entryType = 'Receipt';

				amount = credit;

			} else if (debit !== "0.00" && debit !== "0") {

				entryType = 'Payment';

				amount = debit;

			}

			

			entries.push({

				id: id,

				ledgerAccount: ledgerAccount,
				
				centerID: centerID,

				transDate: transDate,

				description: description,

				chqRef: chqRef,

				entryType: entryType,

				amount: amount,
				credit: credit,
				debit: debit

			});

		});

		

		if (!valid) {

			alert('Please select a Ledger Account for all selected entries.');

			return;

		}

		

		if (entries.length === 0) {

			alert('Please select at least one entry.');

			return;

		}



		let BankAccount = $("#BankAccount2").val();

		

		if(confirm('Are You Sure !!! You Want To Proceed ?')){

			$.ajax({

				url: "<?php echo admin_url(); ?>rate_master/GeneratePaymentVoucher",

				dataType: "JSON",

				method: "POST",

				data: { entries, BankAccount },

				beforeSend: function () {

				    $("#pageLoader").show();

				},

				complete: function () {

					$("#pageLoader").hide();

				},

				success: function(response) {

					if(response.status === true || response.status === 'true') {

						alert(response.msg);

						$('#previewModal2').modal('hide');

						location.reload();

					} else {

						alert(response.msg);

					}

				},

			});

		}

	});

</script>



<script>

	$(function(){

		appValidateForm($('#import_form'),{

			file_csv:{required:true,extension: "csv"},

			BankAccount:{required:true},

		});

	});

</script>

