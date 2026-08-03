<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
	.th_total {
		padding-right: 10px;
	}

	.tree tr th {
		padding-bottom: 0px !important;
		padding-top: 10px !important;
	}
</style>
<div id="wrapper">
	<div class="panel_s">
		<div class="panel-body">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
					<li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
					<li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
					<li class="breadcrumb-item active" aria-current="page"><b>Profit Loss Report</b></li>
				</ol>
			</nav>
			<hr class="hr_style">
			<div class="row ">
				<div class="col-md-2">
					<?php
					$formatter = new NumberFormatter('en_IN', NumberFormatter::DECIMAL);
					if (!empty($FromDate) && !empty($ToDate)) {
						$from_date = $FromDate;
						$to_date = $ToDate;
						$ShowHideBtn = "margin-bottom: 20px;display:";
					} else {
						$ShowHideBtn = "margin-bottom: 20px;display:none";
						$fy = $this->session->userdata('finacial_year');
						$fy1 = '20' . $fy . "-04-01";
						$fy_new  = $fy + 1;
						$lastdate_date = '20' . $fy_new . '-03-31';
						$curr_date = date('Y-m-d');
						$curr_date_new    = new DateTime($curr_date);
						$last_date_yr = new DateTime($lastdate_date);
						if ($last_date_yr < $curr_date_new) {
							$date = $lastdate_date;
						} else {
							$date = date('Y-m-d');
						}
						$from_date = _d($fy1);
						$to_date = _d($date);
					}
					?>
					<?php //$cur_date = _d(date('Y-m-d')); 
					?>
					<?php echo render_date_input('from_date', 'From Date', $from_date); ?>
				</div>
				<div class="col-md-2">
					<?php echo render_date_input('as_on_date', 'To Date', $to_date); ?>
				</div>
				<div class="col-md-8">
					<br>
					<button class="btn btn-info pull-left mleft5 search_data" id="search_data">Show</button>
					<?php if (has_permission_new('profitlossTFormat', '', 'export')) {
					?>&nbsp;&nbsp;
					<a class="btn btn-default" id="caexcel" href="javascript:void(0);" style="<?php echo $ShowHideBtn; ?>"><i class="fa fa-spinner fa-spin Loader" style="display:none;"></i> Export</a>
				<?php } ?>
				<?php if (has_permission_new('profitlossTFormat', '', 'print')) {
				?>&nbsp;&nbsp;
				<a class="btn btn-default ShowHideBtn" href="javascript:void(0);" style="<?php echo $ShowHideBtn; ?>" onclick="printPage();">Print</a>
			<?php } ?>
			&nbsp;&nbsp;
			<label class="ShowHideBtn" style="<?php echo $ShowHideBtn; ?>">
				<input type="checkbox" id="expandAll"> Expand All
			</label>
				</div>

			</div>
			<?php
			if (!empty($FromDate) && !empty($ToDate)) {
			?>
				<div class="row ">
					<div class="col-md-12">
						<div class="page">
							<div id="accordion">
								<div class="card">
									<div class="row" id="DivIdToPrint">
										<div class="col-md-6">
											<table class="tree">
												<thead>
													<tr class="tr_header">
														<th style="text-align:left;font-weight:500;font-size: 14px;">Particulars</th>
														<th style=""><b></b></th>
														<th style="text-align:right;font-weight:500;font-size: 14px;">Amount(₹)</th>
													</tr>
												</thead>
												<tbody>
													<?php
													$MainCounter = 1000;
													$SubCounter1 = 2000;
													$SubCounter2 = 3000;
													$SubCounter3 = 4000;
													$SubCounter4 = 5000;
													?>
													<?php
													$TotalSaleRtn = ($TransactionAmt->TotalFreshRtnAmt + $TransactionAmt->TotalDamageRtnAmt);
													$TotalCredit = $TransactionAmt->TotalSaleCNAmt;
													$TotalDebit = $TransactionAmt->TotalSaleDNAmt;
													//$TotalSaleRtnPre = ($TransactionAmt->FrtRtnPriviousYear + $TransactionAmt->DFrtRtnPriviousYear);
													$TotalRevenueIncome = $TransactionAmt->TotalSaleAmt - $TotalSaleRtn - $TotalCredit + $TotalDebit;
													//$TotalRevenueIncomePre += $TransactionAmt->SalePriviousYear - $TotalSaleRtnPre;
													?>
													<tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
														<td style="text-align:left;font-weight:500;font-size: 14px;">OPENING AMOUNT</td>
														<td style="text-align:left;font-weight:700;font-size: 14px;text-align:right;"></td>
														<td style="text-align:left;font-weight:500;font-size: 14px;text-align:right;"><?php echo  $formatter->format($OpeningInventoryAmt->TotalinventoryAmt, 2) ?></td>
													</tr>

													<?php foreach ($OpeningInventoryAmt->inventory as $mainGroup) {
														$MainGroupTotalAmt = (float)$mainGroup['MainGroupTotalAmt'];
														if ($MainGroupTotalAmt == 0) {
															continue;
														}
													?>
														<!-- Main Group -->
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup" data-id="<?php echo $mainGroup["MainGroupID"] ?>">
															<td class="col-id-sr-no" style="font-size:12px;font-weight:500;"><?php echo $mainGroup['MainGroupName']; ?></td>
															<td class="col-id-particular" style="font-size:12px;font-weight:500;text-align:right;"><?php echo $formatter->format($mainGroup['MainGroupTotalAmt'], 2); ?></td>
															<td class="col-id-particular" style="font-size:12px;font-weight:500;text-align:right;"></td>
														</tr>

														<?php foreach ($mainGroup['SubGroup1'] as $subGroup1) {
															$SubGroup1TotalAmt = (float)$subGroup1['SubGroup1TotalAmt'];
															if ($SubGroup1TotalAmt == 0) {
																continue;
															}
														?>

															<!-- Sub Group 1 -->
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node " id="subgroup" data-id="<?php echo $subGroup1["SubGroup1ID"] ?>">
																<td class="col-id-sr-no" style="font-size:12px;font-weight:400;"><?php echo $subGroup1['SubGroup1Name']; ?></td>
																<td class="col-id-particular" style="font-size:12px;font-weight:400;text-align:right;"><?php echo $formatter->format($subGroup1['SubGroup1TotalAmt'], 2); ?></td>
																<td class="col-id-particular" style="font-size:12px;font-weight:400;text-align:right;"></td>
															</tr>

																<?php foreach ($subGroup1['ItemDetails'] as $item) {
																	$ItemTotalAmt = (float)$item['ItemTotalAmt'];
																	if ($ItemTotalAmt == 0) {
																		continue;
																	}
																?>
																	<!-- Item -->
																	<tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node " id="subgroup" data-id="<?php echo $item["ItemID"] ?>">
																		<td class="col-id-sr-no" style="font-size:10px;font-weight:400;"><?php echo $item['ItemID'] . ' - ' . $item['ItemName']; ?></td>
																		<td class="col-id-particular" style="font-size:10px;font-weight:400;text-align:right;"><?php echo $formatter->format($item['ItemTotalAmt'], 2); ?></td>
																		<td class="col-id-particular" style="font-size:10px;font-weight:400;text-align:right;"></td>
																	</tr>
																	<?php $SubCounter3++; ?>
																<?php } ?>
															<?php $SubCounter2++; ?>
														<?php } ?>
														<?php $SubCounter1++; ?>

													<?php } ?>

													<?php $MainCounter++; ?>
													<?php $NetPurchase = $TransactionAmt->TotalPurchaseAmt - $TransactionAmt->TotalPurchaseRtnAmt - $TransactionAmt->TotalPurchDNAmt + $TransactionAmt->TotalPurchCNAmt; ?>
													<tr class="treegrid-<?php echo $MainCounter; ?> parent-node" id="maingroup">
														<td style="text-align:left;font-weight:500;font-size: 14px;">NET PURCHASE AMOUNT</td>
														<td style="text-align:left;font-weight:700;font-size: 14px;">
															<span class="NotesLink" onclick="ShowNotes('NET PURCHASE AMOUNT')">Notes</span>
														</td>
														<td style="text-align:right;font-weight:500;font-size: 14px;"><?php echo  $formatter->format($NetPurchase, 2) ?></td>
													</tr>

													<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup" data-id="">
														<td class="col-id-sr-no" style="font-size:13px;font-weight:500;">PURCHASE AMOUNT (Taxable) - add</td>
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;">
															<span class="NotesLink" onclick="ShowNotes('PURCHASE AMOUNT (Taxable)')">Notes</span>
														</td>
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  $formatter->format($TransactionAmt->TotalPurchaseAmt, 2) ?></td>

													</tr>
													<?php foreach ($TransactionAmt->inventory as $mainGroup) {
														$MainGroupTotalQty = (float)$mainGroup['MainGroupPurchaseAmt'];
														if ($MainGroupTotalQty == 0) {
															continue;
														}
													?>
														<!-- Main Group -->
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node " id="subgroup" data-id="<?php echo $mainGroup["MainGroupID"] ?>">
															<td class="col-id-sr-no" style="font-size:12px;font-weight:500;"><?php echo $mainGroup['MainGroupName']; ?></td>
															<td class="col-id-particular" style="font-size:12px;font-weight:500;text-align:right;"><?php echo $formatter->format($mainGroup['MainGroupPurchaseAmt'], 2); ?></td>
															<td class="col-id-particular" style="font-size:12px;font-weight:500;text-align:right;"></td>
														</tr>

														<?php foreach ($mainGroup['SubGroup1'] as $subGroup1) {
															$SubGroup1TotalQty = (float)$subGroup1['SubGroup1PurchaseAmt'];
															if ($SubGroup1TotalQty == 0) {
																continue;
															}
														?>

															<!-- Sub Group 1 -->
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node " id="subgroup" data-id="<?php echo $subGroup1["SubGroup1ID"] ?>">
																<td class="col-id-sr-no" style="font-size:12px;font-weight:400;"><?php echo $subGroup1['SubGroup1Name']; ?></td>
																<td class="col-id-particular" style="font-size:12px;font-weight:400;text-align:right;"><?php echo $formatter->format($subGroup1['SubGroup1PurchaseAmt'], 2); ?></td>
																<td class="col-id-particular" style="font-size:12px;font-weight:400;text-align:right;"></td>
															</tr>

																<?php foreach ($subGroup1['ItemDetails'] as $item) {
																	$ItemTotalQty = (float)$item['PurchaseAmt'];
																	if ($ItemTotalQty == 0) {
																		continue;
																	}
																?>
																	<!-- Item -->
																	<tr class=" treegrid-<?php echo html_entity_decode($SubCounter4); ?> treegrid-parent-<?php echo $SubCounter3; ?> parent-node " id="subgroup" data-id="Purchase"
																		data-itemname="<?php echo $item['ItemName']; ?>" style="text-align:left;font-weight:400;font-size:12px;cursor:pointer;" onclick="ShowTransaction(this,'<?php echo strtoupper($item["ItemID"]); ?>')">
																		<td class="col-id-sr-no" style="font-size:10px;font-weight:400;"><?php echo $item['ItemID'] . ' - ' . $item['ItemName']; ?></td>
																		<td class="col-id-particular" style="font-size:10px;font-weight:400;text-align:right;"><?php echo $formatter->format($item['PurchaseAmt'], 2); ?></td>
																		<td class="col-id-particular" style="font-size:10px;font-weight:400;text-align:right;"></td>
																	</tr>
																	<?php $SubCounter4++; ?>
																<?php } ?>
															<?php $SubCounter3++; ?>
														<?php } ?>
														<?php $SubCounter2++; ?>

													<?php } ?>
													<?php $SubCounter1++; ?>

													<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup" data-id="">
														<td class="col-id-sr-no" style="font-size:13px;font-weight:500;">PURCHASE RETURN AMOUNT (Taxable) - less</td>
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;">
															<span class="NotesLink" onclick="ShowNotes('PURCHASE RETURN AMOUNT (Taxable)')">Notes</span>
														</td>
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  $formatter->format($TransactionAmt->TotalPurchaseRtnAmt, 2) ?></td>

													</tr>
													<?php foreach ($TransactionAmt->inventory as $mainGroup) {
														$MainGroupTotalQty = (float)$mainGroup['MainGroupPurchaseRtnAmt'];
														if ($MainGroupTotalQty == 0) {
															continue;
														}
													?>
														<!-- Main Group -->
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node " id="subgroup" data-id="<?php echo $mainGroup["MainGroupID"] ?>">
															<td class="col-id-sr-no" style="font-size:12px;font-weight:500;"><?php echo $mainGroup['MainGroupName']; ?></td>
															<td class="col-id-particular" style="font-size:12px;font-weight:500;text-align:right;"><?php echo $formatter->format($mainGroup['MainGroupPurchaseRtnAmt'], 2); ?></td>
															<td class="col-id-particular" style="font-size:12px;font-weight:500;text-align:right;"></td>
														</tr>

														<?php foreach ($mainGroup['SubGroup1'] as $subGroup1) {
															$SubGroup1TotalQty = (float)$subGroup1['SubGroup1PurchaseRtnAmt'];
															if ($SubGroup1TotalQty == 0) {
																continue;
															}
														?>

															<!-- Sub Group 1 -->
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node " id="subgroup" data-id="<?php echo $subGroup1["SubGroup1ID"] ?>">
																<td class="col-id-sr-no" style="font-size:12px;font-weight:400;"><?php echo $subGroup1['SubGroup1Name']; ?></td>
																<td class="col-id-particular" style="font-size:12px;font-weight:400;text-align:right;"><?php echo $formatter->format($subGroup1['SubGroup1PurchaseRtnAmt'], 2); ?></td>
																<td class="col-id-particular" style="font-size:12px;font-weight:400;text-align:right;"></td>
															</tr>

																<?php foreach ($subGroup1['ItemDetails'] as $item) {
																	$ItemTotalQty = (float)$item['PurchaseRtnAmt'];
																	if ($ItemTotalQty == 0) {
																		continue;
																	}
																?>
																	<!-- Item -->
																	<tr class=" treegrid-<?php echo html_entity_decode($SubCounter4); ?> treegrid-parent-<?php echo $SubCounter3; ?> parent-node " id="subgroup" data-id="Purchase Return"
																		data-itemname="<?php echo $item['ItemName']; ?>" style="text-align:left;font-weight:400;font-size:12px;cursor:pointer;" onclick="ShowTransaction(this,'<?php echo strtoupper($item["ItemID"]); ?>')">
																		<td class="col-id-sr-no" style="font-size:10px;font-weight:400;"><?php echo $item['ItemID'] . ' - ' . $item['ItemName']; ?></td>
																		<td class="col-id-particular" style="font-size:10px;font-weight:400;text-align:right;"><?php echo $formatter->format($item['PurchaseRtnAmt'], 2); ?></td>
																		<td class="col-id-particular" style="font-size:10px;font-weight:400;text-align:right;"></td>
																	</tr>
																	<?php $SubCounter4++; ?>
																<?php } ?>
															<?php $SubCounter3++; ?>
														<?php } ?>
														<?php $SubCounter2++; ?>

													<?php } ?>
													<?php $SubCounter1++; ?>

													<!-- Debit Note Details -->
													<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup" data-id="">
														<td class="col-id-sr-no" style="font-size:13px;font-weight:500;">DEBIT NOTE AMOUNT (Taxable) - less</td>
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;">
															<span class="NotesLink" onclick="ShowNotes('DEBIT NOTE AMOUNT (Taxable)')">Notes</span>
														</td>
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  $formatter->format(($TransactionAmt->TotalPurchDNAmt), 2) ?></td>

													</tr>
													<?php foreach ($TransactionAmt->inventory as $mainGroup) {
														$MainGroupTotalQty = (float)($mainGroup['MainGroupPurchDNAmt']);
														if ($MainGroupTotalQty == 0) {
															continue;
														}
													?>
														<!-- Main Group -->
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node " id="subgroup" data-id="<?php echo $mainGroup["MainGroupID"] ?>">
															<td class="col-id-sr-no" style="font-size:12px;font-weight:500;"><?php echo $mainGroup['MainGroupName']; ?></td>
															<td class="col-id-particular" style="font-size:12px;font-weight:500;text-align:right;"><?php echo $formatter->format($mainGroup['MainGroupPurchDNAmt'], 2); ?></td>
															<td class="col-id-particular" style="font-size:12px;font-weight:500;text-align:right;"></td>
														</tr>

														<?php foreach ($mainGroup['SubGroup1'] as $subGroup1) {
															$SubGroup1TotalQty = (float)$subGroup1['SubGroup1PurchDNAmt'];
															if ($SubGroup1TotalQty == 0) {
																continue;
															}
														?>

															<!-- Sub Group 1 -->
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node " id="subgroup" data-id="<?php echo $subGroup1["SubGroup1ID"] ?>">
																<td class="col-id-sr-no" style="font-size:12px;font-weight:400;"><?php echo $subGroup1['SubGroup1Name']; ?></td>
																<td class="col-id-particular" style="font-size:12px;font-weight:400;text-align:right;"><?php echo $formatter->format($subGroup1['SubGroup1PurchDNAmt'], 2); ?></td>
																<td class="col-id-particular" style="font-size:12px;font-weight:400;text-align:right;"></td>
															</tr>

																<?php foreach ($subGroup1['ItemDetails'] as $item) {
																	$ItemTotalQty = (float)$item['PurchDNAmt'];
																	if ($ItemTotalQty == 0) {
																		continue;
																	}
																?>
																	<!-- Item -->
																	<tr class=" treegrid-<?php echo html_entity_decode($SubCounter4); ?> treegrid-parent-<?php echo $SubCounter3; ?> parent-node " id="ItemName" data-id="Purchase Debit"
																		data-itemname="<?php echo $item['ItemName']; ?>" style="text-align:left;font-weight:400;font-size:12px;cursor:pointer;" onclick="ShowTransaction(this,'<?php echo strtoupper($item["ItemID"]); ?>')">
																		<td class="col-id-sr-no" style="font-size:10px;font-weight:400;"><?php echo $item['ItemID'] . ' - ' . $item['ItemName']; ?></td>
																		<td class="col-id-particular" style="font-size:10px;font-weight:400;text-align:right;"><?php echo $formatter->format($item['PurchDNAmt'], 2); ?></td>
																		<td class="col-id-particular" style="font-size:10px;font-weight:400;text-align:right;"></td>
																	</tr>
																	<?php $SubCounter4++; ?>
																<?php } ?>
															<?php $SubCounter3++; ?>
														<?php } ?>
														<?php $SubCounter2++; ?>

													<?php } ?>
													<?php $SubCounter1++; ?>

													<?php $MainCounter++; ?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?> parent-node " id="subgroup">
														<td class="col-id-particular" style="text-align:left;font-weight:500;font-size: 14px;">DIRECT EXPENSES - add</td>
														<td style="text-align:left;font-weight:700;font-size: 14px;"></td>
														<td style="text-align:right;font-weight:500;font-size: 14px;"><?php echo  $formatter->format($DirectExp->CurrentYear, 2) ?></td>
													</tr>
													<?php
													foreach ($DirectExp->nestedData as $DEKey => $DEVal) {
													?>
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $DEVal["AccountID"] ?>">
															<td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $DEVal["Group1Name"]; ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  $formatter->format($DEVal["Group1ClsBal"], 2) ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"></td>
														</tr>
														<?php
														foreach ($DEVal["SubGroups2"] as $DE2Key => $DE2Val) {
														?>
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
																style="font-size:13px;" id="subgroup" data-id="<?php echo $DE2Val["SubActGroupID"] ?>">
																<td class="col-id-particular"><?php echo $DE2Val["SubGroupName"]; ?></td>
																<td style="text-align:right;font-weight:500;font-size:13px;"><?php echo  $formatter->format($DE2Val["Group2ClsBal"], 2) ?></td>
																<td style="text-align:right;font-weight:500;font-size:13px;"></td>
															</tr>
															<?php
															foreach ($DE2Val["Accounts"] as $DEActKey => $DEActVal) {
															?>
																<tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
																	style="font-size:12px;" id="subgroup" data-id="<?php echo $DEActVal["AccountID"] ?>">
																	<td class="col-id-particular" style="cursor:pointer;" onclick="RedirectLedger('<?php echo strtoupper($DEActVal["AccountID"]); ?>')"><?php echo $DEActVal["AccountName"]; ?></td>
																	<td style="text-align:right;font-weight:400;font-size:12px;"><?php echo  $formatter->format($DEActVal["AccountClsBal"], 2) ?></td>
																	<td style="text-align:right;font-weight:400;font-size:12px;"></td>
																</tr>
													<?php
																$SubCounter3++;
															}
															$SubCounter2++;
														}
														$SubCounter1++;
													}
													?>
													<?php $MainCounter++; ?>
													<?php
													$GrossProfitC_F = ($TotalRevenueIncome + $ClosingInventoryAmt->TotalinventoryAmt) - ($OpeningInventoryAmt->TotalinventoryAmt + $NetPurchase + $DirectExp->CurrentYear);
													?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<td class="parent col-id-sr-no" style="font-size:14px;font-weight:600;">GROSS PROFIT C/F</td>
														<td style="font-size:14px;font-weight:700;text-align:right;"></td>
														<td style="font-size:14px;font-weight:600;text-align:right;"><?php echo  $formatter->format($GrossProfitC_F, 2) ?></td>
													</tr>
													<?php $MainCounter++; ?>
													<?php $Total = $GrossProfitC_F + ($OpeningInventoryAmt->TotalinventoryAmt + $NetPurchase + $DirectExp->CurrentYear); ?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<th class="parent col-id-sr-no" style="font-size:14px;font-weight:700;"></th>
														<th style="font-size:14px;font-weight:700;text-align:right;"></th>
														<th style="font-size:14px;font-weight:700;text-align:right;"><?php echo  $formatter->format($Total, 2) ?></th>
													</tr>
													<?php $MainCounter++; ?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?> parent-node " id="subgroup">
														<td class="parent col-id-sr-no" style="font-size:14px;font-weight:500;">EMPLOYEE BENEFITS EXPENSES</td>
														<td style="font-size:14px;font-weight:700;text-align:right;"></td>
														<td style="font-size:14px;font-weight:500;text-align:right;"><?php echo  $formatter->format($EMPBenData->CurrentYear, 2) ?></td>
													</tr>
													<?php
													foreach ($EMPBenData->nestedData as $EBKey => $EBVal) {
													?>
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $EBVal["SubActGroupID"] ?>">
															<td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $EBVal["SubGroupName"]; ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  $formatter->format($EBVal["Group2ClsBal"], 2) ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"></td>
														</tr>
														<?php
														foreach ($EBVal["Accounts"] as $EBActKey => $EBActVal) {
														?>
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
																style="font-size:12px;" id="subgroup" data-id="<?php echo $EBActVal["AccountID"] ?>">
																<td class="col-id-particular" style="text-align:left;font-weight:500;font-size:12px;cursor:pointer;" onclick="RedirectLedger('<?php echo strtoupper($EBActVal["AccountID"]); ?>')"><?php echo $EBActVal["AccountName"]; ?></td>
																<td style="text-align:right;font-weight:500;font-size:12px;"><?php echo  $formatter->format($EBActVal["AccountClsBal"], 2) ?></td>
																<td style="text-align:right;font-weight:500;font-size:12px;"></td>
															</tr>
													<?php
															$SubCounter2++;
														}
														$SubCounter1++;
													}
													?>
													<?php $MainCounter++; ?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?> parent-node " id="subgroup">
														<td class="parent col-id-sr-no" style="font-size:14px;font-weight:500;">TAX EXPENSES</td>
														<td style="font-size:14px;font-weight:700;text-align:right;"></td>
														<td style="font-size:14px;font-weight:500;text-align:right;"><?php echo  $formatter->format($TaxExpense->CurrentYear, 2) ?></td>
													</tr>
													<?php
													foreach ($TaxExpense->nestedData as $TaxKey => $TaxVal) {
													?>
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $EBVal["SubActGroupID"] ?>">
															<td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $TaxVal["SubGroupName"]; ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  $formatter->format($TaxVal["Group2ClsBal"], 2) ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"></td>
														</tr>
														<?php
														foreach ($TaxVal["Accounts"] as $TaxActKey => $TaxActVal) {
														?>
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
																style="font-size:12px;" id="subgroup" data-id="<?php echo $TaxActVal["AccountID"] ?>">
																<td class="col-id-particular" style="text-align:left;font-weight:500;font-size:12px;cursor:pointer;" onclick="RedirectLedger('<?php echo strtoupper($EBActVal["AccountID"]); ?>')"><?php echo $EBActVal["AccountName"]; ?></td>
																<td style="text-align:right;font-weight:500;font-size:12px;"><?php echo  $formatter->format($TaxActVal["AccountClsBal"], 2) ?></td>
																<td style="text-align:right;font-weight:500;font-size:12px;"></td>
															</tr>
													<?php
															$SubCounter2++;
														}
														$SubCounter1++;
													}
													?>
													<?php $MainCounter++; ?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<td class="parent col-id-sr-no" style="font-size:14px;font-weight:500;">FINANCE COST</td>
														<td style="font-size:14px;font-weight:700;text-align:right;"></td>
														<td style="font-size:14px;font-weight:500;text-align:right;"><?php echo  $formatter->format($FinanceCostData->CurrentYear, 2) ?></td>
													</tr>
													<?php
													foreach ($FinanceCostData->nestedData as $FCKey => $FCVal) {
													?>
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $FCVal["SubActGroupID"] ?>">
															<td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $FCVal["SubGroupName"]; ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  $formatter->format($FCVal["Group2ClsBal"], 2) ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"></td>
														</tr>
														<?php
														foreach ($FCVal["Accounts"] as $FCActKey => $FCActVal) {
														?>
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
																style="font-size:12px;" id="subgroup" data-id="<?php echo $FCActVal["AccountID"] ?>">
																<td class="col-id-particular" style="text-align:left;font-weight:500;font-size:12px;cursor:pointer;" onclick="RedirectLedger('<?php echo strtoupper($FCActVal["AccountID"]); ?>')"><?php echo $FCActVal["AccountName"]; ?></td>
																<td style="text-align:right;font-weight:500;font-size:12px;"><?php echo  $formatter->format($FCActVal["AccountClsBal"], 2) ?></td>
																<td style="text-align:right;font-weight:500;font-size:12px;"></td>
															</tr>
													<?php
															$SubCounter2++;
														}
														$SubCounter1++;
													}
													?>
													<?php $MainCounter++; ?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<td class="parent col-id-sr-no" style="font-size:14px;font-weight:500;">DEPRECIATION AND AMORTIZATION EXP</td>
														<td style="font-size:14px;font-weight:700;text-align:right;"></td>
														<td style="font-size:14px;font-weight:500;text-align:right;"><?php echo  $formatter->format($DeprecData->CurrentYear, 2) ?></td>
													</tr>
													<?php
													foreach ($DeprecData->nestedData as $DAKey => $DAVal) {
													?>
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $DAVal["SubActGroupID"] ?>">
															<td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $DAVal["SubGroupName"]; ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  $formatter->format($DAVal["Group2ClsBal"], 2) ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"></td>
														</tr>
														<?php
														foreach ($DAVal["Accounts"] as $DAActKey => $DAActVal) {
														?>
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
																style="font-size:12px;" id="subgroup" data-id="<?php echo $DAActVal["AccountID"] ?>">
																<td class="col-id-particular" style="cursor:pointer;" onclick="RedirectLedger('<?php echo strtoupper($DAActVal["AccountID"]); ?>')"><?php echo $DAActVal["AccountName"]; ?></td>
																<td style="text-align:right;font-weight:500;"><?php echo  $formatter->format($DAActVal["AccountClsBal"], 2) ?></td>
																<td style="text-align:right;font-weight:500;"></td>
															</tr>
													<?php
															$SubCounter2++;
														}
														$SubCounter1++;
													}
													?>
													<?php $MainCounter++; ?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?> parent-node " id="subgroup">
														<td class="col-id-particular" style="font-size:14px;font-weight:500;">INDIRECT EXPENSES</td>
														<td style="font-size:14px;font-weight:700;text-align:right;"></td>
														<td style="font-size:14px;font-weight:500;text-align:right;"><?php echo  $formatter->format($OtherExpensesData->CurrentYear, 2) ?></td>
													</tr>
													<?php
													foreach ($OtherExpensesData->nestedData as $IExpKey => $IExpVal) {
													?>
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $IExpVal["AccountID"] ?? ''; ?>">
															<td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $IExpVal["Group1Name"]; ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  $formatter->format($IExpVal["Group1ClsBal"], 2) ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"></td>
														</tr>
														<?php
														foreach ($IExpVal["SubGroups2"] as $IExp2Key => $IExp2Val) {
														?>
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
																style="font-size:12px;" id="subgroup" data-id="<?php echo $IExp2Val["SubActGroupID"] ?>">
																<td class="col-id-particular"><?php echo $IExp2Val["SubGroupName"]; ?></td>
																<td style="text-align:right;font-weight:500;font-size:12px;"><?php echo  $formatter->format($IExp2Val["Group2ClsBal"], 2) ?></td>
																<td style="text-align:right;font-weight:500;font-size:12px;"></td>
															</tr>
															<?php
															foreach ($IExp2Val["Accounts"] as $IExpActKey => $IExpActVal) {
															?>
																<tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
																	style="font-size:13px;" id="subgroup" data-id="<?php echo $IExpActVal["AccountID"] ?>">
																	<td class="col-id-particular" style="text-align:left;font-weight:400;font-size:12px;cursor:pointer;" onclick="RedirectLedger('<?php echo strtoupper($IExpActVal["AccountID"]); ?>')"><?php echo $IExpActVal["AccountName"]; ?></td>
																	<td style="text-align:right;font-weight:400;font-size:12px;"><?php echo  $formatter->format($IExpActVal["AccountClsBal"], 2) ?></td>
																	<td style="text-align:right;font-weight:400;font-size:12px;"></td>
																</tr>
													<?php
																$SubCounter3++;
															}
															$SubCounter2++;
														}
														$SubCounter1++;
													}
													?>
													<?php $MainCounter++; ?>
													<?php
													$IndirectExp = ($OtherExpensesData->CurrentYear + $EMPBenData->CurrentYear + $FinanceCostData->CurrentYear + $DeprecData->CurrentYear);
													$NetProfit = $GrossProfitC_F + $OtherIncome->CurrentYear - $IndirectExp;
													?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<th class="parent col-id-sr-no" style="font-size:15px;font-weight:700;border-top:none;">NET PROFIT</th>
														<th style="font-size:14px;font-weight:700;text-align:right;border-top:none;"></th>
														<th style="font-size:15px;font-weight:700;text-align:right;border-top:none;"><?php echo  $formatter->format($NetProfit, 2) ?></th>
													</tr>
													<?php $MainCounter++; ?>
													<?php $AllTotal = $NetProfit + $IndirectExp; ?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<th class="parent col-id-sr-no" style="font-size:15px;font-weight:700;">TOTAL</th>
														<th style="font-size:14px;font-weight:700;text-align:right;"></th>
														<th style="font-size:15px;font-weight:700;text-align:right;"><?php echo  $formatter->format($AllTotal, 2) ?></th>
													</tr>
													<?php $MainCounter++; ?>
												</tbody>
											</table>
										</div>
										<div class="col-md-6">
											<table class="tree">
												<thead>
													<tr class="tr_header">
														<th style="text-align:left;font-weight:500;font-size: 14px;">Particulars</th>
														<th style=""><b></b></th>
														<th style="text-align:right;font-weight:500;font-size: 14px;">Amount(₹)</th>
													</tr>
												</thead>
												<tbody>
													<?php
													$MainCounter = 1000;
													$SubCounter1 = 2000;
													?>
													<?php $MainCounter++; ?>
													<tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
														<td class="parent col-id-sr-no" style="text-align:left;font-weight:500;font-size: 14px;">REVENUE FROM OPERATION</td>
														<td class="parent col-id-particular" style="text-align:left;font-weight:700;font-size: 14px;text-align:right;">
															<span class="NotesLink" onclick="ShowNotes('REVENUE FROM OPERATION')">Notes</span>
														</td>
														<td class="parent col-id-particular" style="text-align:left;font-weight:500;font-size: 14px;text-align:right;"><?php echo  $formatter->format($TotalRevenueIncome, 2) ?></td>
													</tr>
													<!-- Sale Details -->
													<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup" data-id="">
														<td class="col-id-sr-no" style="font-size:13px;font-weight:500;">SALE AMOUNT (Taxable) - add</td>
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;">
															<span class="NotesLink" onclick="ShowNotes('SALE AMOUNT (Taxable)')">Notes</span>
														</td>
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  $formatter->format($TransactionAmt->TotalSaleAmt, 2) ?></td>

													</tr>
													<?php foreach ($TransactionAmt->inventory as $mainGroup) {
														$MainGroupTotalQty = (float)$mainGroup['MainGroupSaleAmt'];
														if ($MainGroupTotalQty == 0) {
															continue;
														}
													?>
														<!-- Main Group -->
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node " id="subgroup" data-id="<?php echo $mainGroup["MainGroupID"] ?>">
															<td class="col-id-sr-no" style="font-size:12px;font-weight:500;"><?php echo $mainGroup['MainGroupName']; ?></td>
															<td class="col-id-particular" style="font-size:12px;font-weight:500;text-align:right;"><?php echo $formatter->format($mainGroup['MainGroupSaleAmt'], 2); ?></td>
															<td class="col-id-particular" style="font-size:12px;font-weight:500;text-align:right;"></td>
														</tr>

														<?php foreach ($mainGroup['SubGroup1'] as $subGroup1) {
															$SubGroup1TotalQty = (float)$subGroup1['SubGroup1SaleAmt'];
															if ($SubGroup1TotalQty == 0) {
																continue;
															}
														?>

															<!-- Sub Group 1 -->
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node " id="subgroup" data-id="<?php echo $subGroup1["SubGroup1ID"] ?>">
																<td class="col-id-sr-no" style="font-size:12px;font-weight:400;"><?php echo $subGroup1['SubGroup1Name']; ?></td>
																<td class="col-id-particular" style="font-size:12px;font-weight:400;text-align:right;"><?php echo $formatter->format($subGroup1['SubGroup1SaleAmt'], 2); ?></td>
																<td class="col-id-particular" style="font-size:12px;font-weight:400;text-align:right;"></td>
															</tr>
															
																<?php foreach ($subGroup1['ItemDetails'] as $item) {
																	$ItemTotalQty = (float)$item['SaleAmt'];
																	if ($ItemTotalQty == 0) {
																		continue;
																	}
																?>
																	<!-- Item -->
																	<tr class=" treegrid-<?php echo html_entity_decode($SubCounter4); ?> treegrid-parent-<?php echo $SubCounter3; ?> parent-node " id="ItemName" data-id="Sale"
																		data-itemname="<?php echo $item['ItemName']; ?>" style="text-align:left;font-weight:400;font-size:12px;cursor:pointer;" onclick="ShowTransaction(this,'<?php echo strtoupper($item["ItemID"]); ?>')">
																		<td class="col-id-sr-no" style="font-size:10px;font-weight:400;"><?php echo $item['ItemID'] . ' - ' . $item['ItemName']; ?></td>
																		<td class="col-id-particular" style="font-size:10px;font-weight:400;text-align:right;"><?php echo $formatter->format($item['SaleAmt'], 2); ?></td>
																		<td class="col-id-particular" style="font-size:10px;font-weight:400;text-align:right;"></td>
																	</tr>
																	<?php $SubCounter4++; ?>
																<?php } ?>
															<?php $SubCounter3++; ?>
														<?php } ?>
														<?php $SubCounter2++; ?>

													<?php } ?>
													<?php $SubCounter1++; ?>

													<!-- Sale Return Details -->
													<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup" data-id="">
														<td class="col-id-sr-no" style="font-size:13px;font-weight:500;">SALE RETURN AMOUNT (Taxable) - less</td>
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;">
															<span class="NotesLink" onclick="ShowNotes('SALE RETURN AMOUNT (Taxable)')">Notes</span>
														</td>
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  $formatter->format(($TransactionAmt->TotalFreshRtnAmt + $TransactionAmt->TotalDamageRtnAmt), 2) ?></td>

													</tr>
													<?php foreach ($TransactionAmt->inventory as $mainGroup) {
														$MainGroupTotalQty = (float)($mainGroup['MainGroupFreshRtnAmt'] + $mainGroup['MainGroupDamageRtnAmt']);
														if ($MainGroupTotalQty == 0) {
															continue;
														}
													?>
														<!-- Main Group -->
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node " id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
															<td class="col-id-sr-no" style="font-size:12px;font-weight:500;"><?php echo $mainGroup['MainGroupName']; ?></td>
															<td class="col-id-particular" style="font-size:12px;font-weight:500;text-align:right;"><?php echo $formatter->format(($mainGroup['MainGroupFreshRtnAmt'] + $mainGroup['MainGroupDamageRtnAmt']), 2); ?></td>
															<td class="col-id-particular" style="font-size:12px;font-weight:500;text-align:right;"></td>
														</tr>

														<?php foreach ($mainGroup['SubGroup1'] as $subGroup1) {
															$SubGroup1TotalQty = (float)($subGroup1['SubGroup1FreshRtnAmt'] + $subGroup1['SubGroup1DamageRtnAmt']);
															if ($SubGroup1TotalQty == 0) {
																continue;
															}
														?>

															<!-- Sub Group 1 -->
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node " id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
																<td class="col-id-sr-no" style="font-size:12px;font-weight:400;"><?php echo $subGroup1['SubGroup1Name']; ?></td>
																<td class="col-id-particular" style="font-size:12px;font-weight:400;text-align:right;"><?php echo $formatter->format(($subGroup1['SubGroup1FreshRtnAmt'] + $subGroup1['SubGroup1DamageRtnAmt']), 2); ?></td>
																<td class="col-id-particular" style="font-size:12px;font-weight:400;text-align:right;"></td>
															</tr>
															
																<?php foreach ($subGroup1['ItemDetails'] as $item) {
																	$ItemTotalQty = (float)($item['FreshRtnAmt'] + $item['DamageRtnAmt']);
																	if ($ItemTotalQty == 0) {
																		continue;
																	}
																?>
																	<!-- Item -->
																	<tr class=" treegrid-<?php echo html_entity_decode($SubCounter4); ?> treegrid-parent-<?php echo $SubCounter3; ?> parent-node " id="ItemName" data-id="Sale Return"
																		data-itemname="<?php echo $item['ItemName']; ?>" style="text-align:left;font-weight:400;font-size:12px;cursor:pointer;" onclick="ShowTransaction(this,'<?php echo strtoupper($item["ItemID"]); ?>')">
																		<td class="col-id-sr-no" style="font-size:10px;font-weight:400;"><?php echo $item['ItemID'] . ' - ' . $item['ItemName']; ?></td>
																		<td class="col-id-particular" style="font-size:10px;font-weight:400;text-align:right;"><?php echo $formatter->format(($item['FreshRtnAmt'] + $item['DamageRtnAmt']), 2); ?></td>
																		<td class="col-id-particular" style="font-size:10px;font-weight:400;text-align:right;"></td>
																	</tr>
																	<?php $SubCounter4++; ?>
																<?php } ?>
															<?php $SubCounter3++; ?>
														<?php } ?>
														<?php $SubCounter2++; ?>

													<?php } ?>
													<?php $SubCounter1++; ?>

													<!-- Credit Note Details -->
													<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup" data-id="">
														<td class="col-id-sr-no" style="font-size:13px;font-weight:500;">CREDIT NOTE AMOUNT (Taxable) - less</td>
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;">
															<span class="NotesLink" onclick="ShowNotes('CREDIT NOTE AMOUNT (Taxable)')">Notes</span>
														</td>
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  $formatter->format(($TransactionAmt->TotalSaleCNAmt), 2) ?></td>

													</tr>
													<?php foreach ($TransactionAmt->inventory as $mainGroup) {
														$MainGroupTotalQty = (float)($mainGroup['MainGroupSaleCNAmt']);
														if ($MainGroupTotalQty == 0) {
															continue;
														}
													?>
														<!-- Main Group -->
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node " id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
															<td class="col-id-sr-no" style="font-size:12px;font-weight:500;"><?php echo $mainGroup['MainGroupName']; ?></td>
															<td class="col-id-particular" style="font-size:12px;font-weight:500;text-align:right;"><?php echo $formatter->format($mainGroup['MainGroupSaleCNAmt'], 2); ?></td>
															<td class="col-id-particular" style="font-size:12px;font-weight:500;text-align:right;"></td>
														</tr>

														<?php foreach ($mainGroup['SubGroup1'] as $subGroup1) {
															$SubGroup1TotalQty = (float)$subGroup1['SubGroup1SaleCNAmt'];
															if ($SubGroup1TotalQty == 0) {
																continue;
															}
														?>

															<!-- Sub Group 1 -->
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node " id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
																<td class="col-id-sr-no" style="font-size:12px;font-weight:400;"><?php echo $subGroup1['SubGroup1Name']; ?></td>
																<td class="col-id-particular" style="font-size:12px;font-weight:400;text-align:right;"><?php echo $formatter->format($subGroup1['SubGroup1SaleCNAmt'], 2); ?></td>
																<td class="col-id-particular" style="font-size:12px;font-weight:400;text-align:right;"></td>
															</tr>

																<?php foreach ($subGroup1['ItemDetails'] as $item) {
																	$ItemTotalQty = (float)$item['SaleCNAmt'];
																	if ($ItemTotalQty == 0) {
																		continue;
																	}
																?>
																	<!-- Item -->
																	<tr class=" treegrid-<?php echo html_entity_decode($SubCounter4); ?> treegrid-parent-<?php echo $SubCounter3; ?> parent-node " id="ItemName" data-id="Sale Credit"
																		data-itemname="<?php echo $item['ItemName']; ?>" style="text-align:left;font-weight:400;font-size:12px;cursor:pointer;" onclick="ShowTransaction(this,'<?php echo strtoupper($item["ItemID"]); ?>')">
																		<td class="col-id-sr-no" style="font-size:10px;font-weight:400;"><?php echo $item['ItemID'] . ' - ' . $item['ItemName']; ?></td>
																		<td class="col-id-particular" style="font-size:10px;font-weight:400;text-align:right;"><?php echo $formatter->format($item['SaleCNAmt'], 2); ?></td>
																		<td class="col-id-particular" style="font-size:10px;font-weight:400;text-align:right;"></td>
																	</tr>
																	<?php $SubCounter4++; ?>
																<?php } ?>
															<?php $SubCounter3++; ?>
														<?php } ?>
														<?php $SubCounter2++; ?>

													<?php } ?>
													<?php $SubCounter1++; ?>

													<?php $MainCounter++; ?>
													<!-- Cloasing Inventory -->
													<tr class="treegrid-<?php echo $MainCounter; ?> parent-node" id="maingroup">
														<td class="parent col-id-sr-no" style="text-align:left;font-weight:500;font-size: 14px;"> CLOSING AMOUNT</td>
														<td style="text-align:right;font-weight:700;font-size: 14px;"></td>
														<td style="text-align:right;font-weight:500;font-size: 14px;"><?php echo  $formatter->format($ClosingInventoryAmt->TotalinventoryAmt, 2) ?></td>
													</tr>
													<?php foreach ($ClosingInventoryAmt->inventory as $mainGroup) {
														$MainGroupTotalAmt = (float)$mainGroup['MainGroupTotalAmt'];
														if ($MainGroupTotalAmt == 0) {
															continue;
														}
													?>
														<!-- Main Group -->
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup" data-id="">
															<td class="col-id-sr-no" style="font-size:12px;font-weight:500;"><?php echo $mainGroup['MainGroupName']; ?></td>
															<td class="col-id-particular" style="font-size:12px;font-weight:500;text-align:right;"><?php echo $formatter->format($mainGroup['MainGroupTotalAmt'], 2); ?></td>
															<td class="col-id-particular" style="font-size:12px;font-weight:500;text-align:right;"></td>
														</tr>

														<?php foreach ($mainGroup['SubGroup1'] as $subGroup1) {
															$SubGroup1TotalAmt = (float)$subGroup1['SubGroup1TotalAmt'];
															if ($SubGroup1TotalAmt == 0) {
																continue;
															}
														?>

															<!-- Sub Group 1 -->
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node " id="subgroup" data-id="">
																<td class="col-id-sr-no" style="font-size:12px;font-weight:400;"><?php echo $subGroup1['SubGroup1Name']; ?></td>
																<td class="col-id-particular" style="font-size:12px;font-weight:400;text-align:right;"><?php echo $formatter->format($subGroup1['SubGroup1TotalAmt'], 2); ?></td>
																<td class="col-id-particular" style="font-size:12px;font-weight:400;text-align:right;"></td>
															</tr>
															
																<?php foreach ($subGroup1['ItemDetails'] as $item) {
																	$ItemTotalAmt = (float)$item['ItemTotalAmt'];
																	if ($ItemTotalAmt == 0) {
																		continue;
																	}
																?>
																	<!-- Item -->
																	<tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node " id="subgroup" data-id="">
																		<td class="col-id-sr-no" style="font-size:10px;font-weight:400;"><?php echo $item['ItemID'] . ' - ' . $item['ItemName']; ?></td>
																		<td class="col-id-particular" style="font-size:10px;font-weight:400;text-align:right;"><?php echo $formatter->format($item['ItemTotalAmt'], 2); ?></td>
																		<td class="col-id-particular" style="font-size:10px;font-weight:400;text-align:right;"></td>
																	</tr>
																	<?php $SubCounter3++; ?>
																<?php } ?>
															<?php $SubCounter2++; ?>
														<?php } ?>
														<?php $SubCounter1++; ?>

													<?php } ?>

													<?php $MainCounter++; ?>
													<tr class="treegrid-<?php echo $MainCounter; ?> parent-node" id="maingroup" style="height:54px;">
														<td colspan="3"></td>
													</tr>
													<?php $MainCounter++; ?>
													<?php $Total2 = $TotalRevenueIncome + $ClosingInventoryAmt->TotalinventoryAmt;
													?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<th class="parent col-id-sr-no" style="font-size:14px;font-weight:700;"></th>
														<th style="font-size:14px;font-weight:700;text-align:right;"></th>
														<th style="font-size:14px;font-weight:700;text-align:right;"><?php echo  $formatter->format($Total2, 2) ?></th>
													</tr>
													<?php $MainCounter++; ?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<td class="parent col-id-sr-no" style="font-size:14px;font-weight:600;">GROSS PROFIT B/F</td>
														<td style="font-size:14px;font-weight:700;text-align:right;"></td>
														<td style="font-size:14px;font-weight:600;text-align:right;"><?php echo  $formatter->format($GrossProfitC_F, 2) ?></td>
													</tr>
													<?php $MainCounter++; ?>
													<tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
														<td class="parent col-id-sr-no" style="text-align:left;font-weight:500;font-size: 14px;">OTHER INCOME</td>
														<?php
														$TotalRevenueIncome += $OtherIncome->CurrentYear;
														// $TotalRevenueIncomePre += $OtherIncome->PriviousYear;
														?>
														<td style="text-align:right;font-weight:700;font-size: 14px;"></td>
														<td style="text-align:right;font-weight:500;font-size: 14px;"><?php echo  $formatter->format($OtherIncome->CurrentYear, 2) ?></td>
													</tr>
													<?php
													foreach ($OtherIncome->nestedData as $OthKey => $OthVal) {
													?>
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $OthVal["AccountID"] ?? ''; ?>">
															<td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $OthVal["Group1Name"]; ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  $formatter->format($OthVal["Group1ClsBal"], 2) ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"></td>
														</tr>
														<?php
														foreach ($OthVal["SubGroups2"] as $Oth2Key => $Oth2Val) {
														?>
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
																style="font-size:13px;" id="subgroup" data-id="<?php echo $Oth2Val["SubActGroupID"] ?>">
																<td class="col-id-particular"><?php echo $Oth2Val["SubGroupName"]; ?></td>
																<td style="text-align:right;font-weight:500;"><?php echo  $formatter->format($Oth2Val["Group2ClsBal"], 2) ?></td>
																<td style="text-align:right;font-weight:500;"></td>
															</tr>
															<?php
															foreach ($Oth2Val["Accounts"] as $OthActKey => $OthActVal) {
															?>
																<tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
																	style="font-size:13px;" id="subgroup" data-id="<?php echo $OthActVal["AccountID"] ?>">
																	<td class="col-id-particular" style="cursor:pointer;" onclick="RedirectLedger('<?php echo strtoupper($OthActVal["AccountID"]); ?>')"><?php echo $OthActVal["AccountName"]; ?></td>
																	<td style="text-align:right;font-weight:400;"><?php echo  $formatter->format($OthActVal["AccountClsBal"], 2) ?></td>
																	<td style="text-align:right;font-weight:400;"></td>
																</tr>
													<?php
																$SubCounter3++;
															}
															$SubCounter2++;
														}
														$SubCounter1++;
													}
													?>
													<?php $MainCounter++; ?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<td colspan="3" style="height:85px;"></td>
													</tr>
													<?php $MainCounter++; ?>
													<?php $AllTotal2 = $GrossProfitC_F + $OtherIncome->CurrentYear; ?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<th class="parent col-id-sr-no" style="font-size:15px;font-weight:700;">TOTAL</th>
														<th style="font-size:14px;font-weight:700;text-align:right;"></th>
														<th style="font-size:15px;font-weight:700;text-align:right;"><?php echo  $formatter->format($AllTotal2, 2) ?></th>
													</tr>
													<?php $MainCounter++; ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php
			}
			?>
		</div>
	</div>
</div>

<div class="modal fade" id="Transactional-modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header" style="padding: 4px 10px;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modal-title">Transactional Details</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6">
						<p style='font-weight:500;font-size:13px;'>Item Name : <span class="Itemname"></span></p>
					</div>
					<div class="col-md-6">
						<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search.." class="form-control" style="float: right;">
					</div>
					<div class="col-md-12">
						<div id="loader" style="display:none;">
							<div class="spinner"></div>
							<div id="loader_msg">Loading...</div>
						</div>
						<!--<div class="loader-wrapper" style="display:none;"> <div class="custom-spinner"></div> <div class="loader-text"></div> </div>-->
						<div class="table_Transactional_data">
							<table class="tree table-bordered table_Transactional_data" id="table_Transactional_data" width="100%">

							</table>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="Notes-modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header" style="padding: 4px 10px;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modal-title">Notes</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<p id="Purchase" style='font-size:14px;'>
							<b> PURCHASE AMOUNT:</b><br>
							- To verify the <b>Purchase Amount</b>, open the <b>Account Ledger</b> page and compare the balance of the <b>Purchase Account (PURCH)</b> ledger. Deduct the balance of the <b>Purchase Discount on Trade (PDISC)</b> ledger to arrive at the <b>Actual Purchase Amount</b>.<br>
							- To verify the <b>Purchase Amount</b>, open the <b>GSTR Purchase</b> page.
						</p>
						<p id="PurchaseReturn" style='font-size:14px;'>
							<b>PURCHASE RETURN AMOUNT:</b><br>
							- To verify the <b>Purchase Return Amount</b>, open the <b>Account Ledger</b> page and compare the balance of the <b>Purchase Return Account (RPURCH)</b> ledger. Deduct the balance of the <b>Purchase Return Discount on Trade (PRDISC)</b> ledger to arrive at the <b>Actual Purchase Return Amount</b>.<br>
							- To verify the <b>Purchase Return Amount</b>, open the <b>GSTR Purchase</b> page.
						</p>
						<p id="DebitNote" style='font-size:14px;'>
							<b>DEBIT NOTE AMOUNT:</b><br>
							- To verify the <b>Debit Note Amount</b>, open the <b>Account Ledger</b> page and compare the balance of the <b>Purchase Claim Account (PCLAIM)</b> ledger.<br>
							- To verify the <b>Debit Note Amount</b>, open the <b>GSTR Purchase</b> page.
						</p>
						<p id="NetPurchase" style='font-size:14px;'>
							<b>Net Purchase : </b> Total purchases made during the period after deducting purchase returns and purchase discounts(Trade discount) and Debit Note.
							<br><b>Net Purchase = Gross Purchases − Purchase Returns − Purchase Discounts (Trade Discounts) - Debit Note</b>
						</p>
						<p id="NetSale" style='font-size:14px;'>
							<b>REVENUE FROM OPERATION : </b> Total sale made during the period after deducting sale returns and sale discounts(Trade discount) and credit Note.
							<br><b>Net Sale = Gross sale − sale Returns − sale Discounts (Trade Discounts) - Credit Note</b>
						</p>
						<p id="Sale" style='font-size:14px;'>
							<b>SALE AMOUNT:</b><br>
							- To verify the <b>Sale Amount</b>, open the <b>Account Ledger</b> page and compare the balance of the <b>Sale Account (SALE)</b> ledger. Deduct the balance of the <b>Sale Discount on Trade (DISC)</b> ledger to arrive at the <b>Actual Sale Amount</b>.<br>
							- To verify the <b>Sale Amount</b>, open the <b>GSTR Sale</b> page.
						</p>
						<p id="SaleReturn" style='font-size:14px;'>
							<b>SALE RETURN AMOUNT:</b><br>
							- To verify the <b>Sale Return Amount</b>, open the <b>Account Ledger</b> page and compare the balance of the <b>Sale Return Account (RSALE)</b> ledger. Deduct the balance of the <b>Purchase Return Discount on Trade (SRDISC)</b> ledger to arrive at the <b>Actual Sale Return Amount</b>.<br>
							- To verify the <b>Sale Return Amount</b>, open the <b>GSTR Sale</b> page.
						</p>
						<p id="CreditNote" style='font-size:14px;'>
							<b>CREDIT NOTE AMOUNT:</b><br>
							- To verify the <b>Credit Note Amount</b>, open the <b>Account Ledger</b> page and compare the balance of the <b>Sale Claim Account (SCLAIM)</b> ledger.<br>
							- To verify the <b>Credit Note Amount</b>, open the <b>GSTR Sale</b> page.
						</p>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<style>
	.NotesLink {
		text-align: left;
		font-weight: 400;
		font-size: 12px;
		cursor: pointer;
		color: #03a9f4;
	}

	.table_Transactional_data {
		overflow: auto;
		max-height: 60vh;
		position: relative;
		top: 0px;
	}

	.table_Transactional_data thead th {
		position: sticky;
		top: 0;
		z-index: 1;
	}

	.table_Transactional_data tbody th {
		position: sticky;
		left: 0;
	}

	/* Just common table stuff. Really. */
	.table_Transactional_data table {
		border-collapse: collapse;
	}

	.table_Transactional_data th,
	td {
		padding: 3px 3px !important;
		font-size: 11px;
		line-height: 1.42857143;
		vertical-align: middle;
	}

	.table_Transactional_data th {
		background: #50607b;
		color: #fff !important;
	}
</style>
<style>
	.treegrid-expander-expanded::after {
		border-top-color: #03a9f4 !important;
	}

	.treegrid-expander-collapsed::after {
		/*border-top-color: #03a9f4 !important;*/
		border-left-color: #03a9f4 !important;
	}

	.tree tr:hover {
		background-color: #ccc;
	}

	#loader {
		background: rgba(255, 255, 255, 0.8);
		z-index: 9999;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
	}

	.spinner {
		width: 70px;
		height: 70px;
		border: 10px solid #ddd;
		border-top: 10px solid #007bff;
		border-radius: 50%;
		animation: spin 1s linear infinite;
	}

	#loader_msg {
		margin-top: 20px;
		font-size: 16px;
		font-weight: bold;
	}

	@keyframes spin {
		100% {
			transform: rotate(360deg);
		}
	}
</style>

<script>
	$(document).ready(function() {
		$('#expandAll').on('change', function() {
			if (this.checked) {
				// Expand all — show all child rows
				$('.tree tr').show().addClass('expanded');
				$("table.tree tr td div span").each(function() {
					var ClassName = $(this).attr('class');
					$needle = "treegrid-expander-collapsed";
					if (ClassName.includes($needle)) {
						$(this).removeClass("treegrid-expander-collapsed").addClass("treegrid-expander-expanded");
					}
				});
				$("table.tree tr td div span").each(function() {
					var ClassName = $(this).attr('class');
					$needle = "treegrid-expander-collapsed";
					if (ClassName.includes($needle)) {
						$(this).removeClass("treegrid-expander-collapsed").addClass("treegrid-expander-expanded");
					}
				});
			} else {
				// Collapse all — hide child rows except top-level
				$('.tree tr').each(function() {
					var parent = $(this).attr('class');
					if (parent && parent.match(/treegrid-parent/)) {
						$(this).hide().removeClass('expanded');
					}
				});
				$("table.tree tr td div span").each(function() {
					var ClassName = $(this).attr('class');
					$needle = "treegrid-expander-expanded";
					if (ClassName.includes($needle)) {
						$(this).removeClass("treegrid-expander-expanded").addClass("treegrid-expander-collapsed");
					}
				});
				$("table.tree tr td div span").each(function() {
					var ClassName = $(this).attr('class');
					$needle = "treegrid-expander-expanded";
					if (ClassName.includes($needle)) {
						$(this).removeClass("treegrid-expander-expanded").addClass("treegrid-expander-collapsed");
					}
				});
			}
		});
	});

	function printPage() {
		var from_date = $("#from_date").val();
		var as_on_date = $("#as_on_date").val();
		var stylesheet = '<style type="text/css">body { font-family: Arial, sans-serif; font-size:12px; }th, td { padding: 5px; border: 1px solid #000; border-collapse: collapse; font-size: 12px; }table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }.print-header { text-align:center; font-weight:bold; border: 1px solid #000; border-collapse: collapse; font-size: 12px; }.row { display: flex; justify-content: space-between; }.col-md-6 { width: 48%; }</style>';
		var heading_data = '<div class="print-header"><div style="border-bottom: 1px solid #000; padding:5px;"><?php echo $company_detail->company_name; ?></div><div style="border-bottom: 1px solid #000; padding:5px;"><?php echo $company_detail->address; ?></div><div style="padding:5px;"> Profit & Loss From ' + from_date + ' To ' + as_on_date + '</div></div>';
		var content = document.getElementById('DivIdToPrint').innerHTML;
		var printWindow = window.open('', '', 'height=600,width=1000');
		printWindow.document.write('<html><head><title>Profit Loss Report</title>');
		printWindow.document.write(stylesheet);
		printWindow.document.write('</head><body>');
		printWindow.document.write(heading_data);
		printWindow.document.write('<div class="row">' + content + '</div>');
		printWindow.document.write('</body></html>');
		printWindow.document.close();
		printWindow.focus();
		printWindow.print();
		printWindow.close();
	}
</script>
<script>
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_Transactional_data");
		tr = table.getElementsByTagName("tr");
		for (i = 1; i < tr.length; i++) {
			tr[i].style.display = "none";
			td = tr[i].getElementsByTagName("td");
			for (j = 0; j < td.length; j++) {
				if (td[j]) {
					txtValue = td[j].textContent || td[j].innerText;
					if (txtValue.toUpperCase().indexOf(filter.toUpperCase()) > -1) {
						tr[i].style.display = "";
						break;
					}
				}
			}
		}
	}
</script>
<script>
	$("#caexcel").click(function() {
		var from_date = $("#from_date").val();
		var as_on_date = $("#as_on_date").val();
		$.ajax({
			url: "<?php echo admin_url(); ?>accounting/export_ProfitLossTFormat",
			method: "POST",
			data: {
				from_date: from_date,
				as_on_date: as_on_date
			},
			beforeSend: function() {
				$('.Loader').show();
			},
			complete: function() {
				$('.Loader').hide();
			},
			success: function(data) {
				response = JSON.parse(data);
				if (response.success === false && response.message) {
					alert(response.message);
					return;
				}
				window.location.href = response.site_url + response.filename;
			}
		});
	});

	function ShowNotes(value) {
		//alert(value);
		$('#Notes-modal').modal('show');
		$('#modal-title').html(value);
		if (value == "DEBIT NOTE AMOUNT (Taxable)") {
			$("#DebitNote").show();
			$("#PurchaseReturn").hide();
			$("#Purchase").hide();
			$("#NetPurchase").hide();

			$("#NetSale").hide();
			$("#SaleReturn").hide();
			$("#CreditNote").hide();
			$("#Sale").hide();
		} else if (value == "PURCHASE RETURN AMOUNT (Taxable)") {
			$("#PurchaseReturn").show();
			$("#Purchase").hide();
			$("#DebitNote").hide();
			$("#NetPurchase").hide();

			$("#NetSale").hide();
			$("#SaleReturn").hide();
			$("#CreditNote").hide();
			$("#Sale").hide();
		} else if (value == "PURCHASE AMOUNT (Taxable)") {
			$("#Purchase").show();
			$("#PurchaseReturn").hide();
			$("#DebitNote").hide();
			$("#NetPurchase").hide();

			$("#NetSale").hide();
			$("#SaleReturn").hide();
			$("#CreditNote").hide();
			$("#Sale").hide();
		} else if (value == "NET PURCHASE AMOUNT") {
			$("#NetPurchase").show();
			$("#PurchaseReturn").hide();
			$("#DebitNote").hide();
			$("#Purchase").hide();

			$("#NetSale").hide();
			$("#SaleReturn").hide();
			$("#CreditNote").hide();
			$("#Sale").hide();
		} else if (value == "CREDIT NOTE AMOUNT (Taxable)") {
			$("#DebitNote").hide();
			$("#PurchaseReturn").hide();
			$("#Purchase").hide();
			$("#NetPurchase").hide();

			$("#NetSale").hide();
			$("#SaleReturn").hide();
			$("#CreditNote").show();
			$("#Sale").hide();
		} else if (value == "SALE RETURN AMOUNT (Taxable)") {
			$("#PurchaseReturn").hide();
			$("#Purchase").hide();
			$("#DebitNote").hide();
			$("#NetPurchase").hide();

			$("#NetSale").hide();
			$("#SaleReturn").show();
			$("#CreditNote").hide();
			$("#Sale").hide();
		} else if (value == "SALE AMOUNT (Taxable)") {
			$("#Purchase").hide();
			$("#PurchaseReturn").hide();
			$("#DebitNote").hide();
			$("#NetPurchase").hide();

			$("#NetSale").hide();
			$("#SaleReturn").hide();
			$("#CreditNote").hide();
			$("#Sale").show();
		} else if (value == "REVENUE FROM OPERATION") {
			$("#NetPurchase").hide();
			$("#PurchaseReturn").hide();
			$("#DebitNote").hide();
			$("#Purchase").hide();

			$("#NetSale").show();
			$("#SaleReturn").hide();
			$("#CreditNote").hide();
			$("#Sale").hide();
		}

	}

	function ShowTransaction(row, ItemID) {
		$("#table_Transactional_data").html("");
		var TransactionType = $(row).data('id'); // jQuery
		$('#Transactional-modal').modal('show');
		var fromDate = $("#from_date").val();
		var toDate = $("#as_on_date").val();
		var itemname = $(row).data('itemname');
		$(".Itemname").html(itemname);
		$.ajax({
			url: "<?php echo admin_url(); ?>accounting/GetTransactionList",
			dataType: "JSON",
			method: "POST",
			data: {
				TransactionType: TransactionType,
				ItemID: ItemID,
				fromDate: fromDate,
				toDate: toDate
			},
			beforeSend: function() {
				$('.table_Transactional_data').css('display', 'none');
				$("#loader").show();
				msgInterval = setInterval(function() {
					$("#loader_msg").text(messages[msgIndex]);
					msgIndex = (msgIndex + 1) % messages.length;
				}, 2000);
			},
			complete: function() {
				$("#loader").hide();
				clearInterval(msgInterval);
				$('.table_Transactional_data').css('display', '');
			},
			success: function(data) {
				$("#table_Transactional_data").html(data);
			}
		});
	}

	function RedirectLedger(AccountID) {
		$.ajax({
			url: "<?php echo admin_url(); ?>accounting/SetAccountID",
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
				var url = "<?php echo admin_url(); ?>accounting/rp_general_ledger";
				var fromVal = $("#from_date").val();
				var toVal = $("#as_on_date").val();
				if (fromVal && toVal) {
					var p1 = fromVal.split('/'),
						p2 = toVal.split('/');
					if (p1.length === 3 && p2.length === 3) {
						var fromParam = p1[2] + '-' + p1[1] + '-' + p1[0];
						var toParam = p2[2] + '-' + p2[1] + '-' + p2[0];
						url += '?from_date=' + encodeURIComponent(fromParam) + '&to_date=' + encodeURIComponent(toParam);
					}
				}
				window.open(url, '_blank');
			}
		});
	}
</script>
<script>
	$(document).ready(function() {
		var fin_y = "<?php echo $this->session->userdata('finacial_year'); ?>";
		var year = "20" + fin_y;
		var year2 = parseInt(fin_y, 10) + 1;
		var year2_new = "20" + year2;
		var minStartDate = new Date(year, 3, 1); // 1 April (month 3 = April in 0-indexed JS)
		var maxEndDate_new = new Date(year2_new + '/03/31'); // 31 March next year
		if ($('#from_date').length && typeof $.fn.datetimepicker !== 'undefined') {
			$('#from_date').datetimepicker({
				format: 'd/m/Y',
				minDate: minStartDate,
				maxDate: maxEndDate_new,
				timepicker: false
			});
		}
		if ($('#as_on_date').length && typeof $.fn.datetimepicker !== 'undefined') {
			$('#as_on_date').datetimepicker({
				format: 'd/m/Y',
				minDate: minStartDate,
				maxDate: maxEndDate_new,
				timepicker: false
			});
		}

		function parseDMY(str) {
			if (!str || typeof str !== 'string') return null;
			var parts = str.trim().split('/');
			if (parts.length !== 3) return null;
			var d = parseInt(parts[0], 10),
				m = parseInt(parts[1], 10) - 1,
				y = parseInt(parts[2], 10);
			if (isNaN(d) || isNaN(m) || isNaN(y)) return null;
			var date = new Date(y, m, d);
			if (date.getDate() !== d || date.getMonth() !== m || date.getFullYear() !== y) return null;
			return date;
		}

		function isDateInFY(date) {
			return date && date >= minStartDate && date <= maxEndDate_new;
		}

		function formatDateForInput(d) {
			var day = ('0' + d.getDate()).slice(-2);
			var month = ('0' + (d.getMonth() + 1)).slice(-2);
			var year = d.getFullYear();
			return day + '/' + month + '/' + year;
		}
		var fyStartLabel = '01/04/20' + fin_y;
		var fyEndLabel = '31/03/20' + year2;
		var fyRangeMsg = 'Date must be within current financial year: ' + fyStartLabel + ' to ' + fyEndLabel + '.';
		$('#from_date').on('blur change', function() {
			var val = $(this).val();
			if (!val) return;
			var parsed = parseDMY(val);
			if (!parsed) {
				alert("Invalid date format. Use dd/mm/yyyy and a valid date within current financial year (" + fyStartLabel + " to " + fyEndLabel + ").");
				$(this).val('');
				return;
			}
			if (!isDateInFY(parsed)) {
				alert(fyRangeMsg);
				$(this).val(formatDateForInput(parsed < minStartDate ? minStartDate : maxEndDate_new));
			}
		});
		$('#as_on_date').on('blur change', function() {
			var val = $(this).val();
			if (!val) return;
			var parsed = parseDMY(val);
			if (!parsed) {
				alert("Invalid date format. Use dd/mm/yyyy and a valid date within current financial year (" + fyStartLabel + " to " + fyEndLabel + ").");
				$(this).val('');
				return;
			}
			if (!isDateInFY(parsed)) {
				alert(fyRangeMsg);
				$(this).val(formatDateForInput(parsed < minStartDate ? minStartDate : maxEndDate_new));
			}
		});
		$('#search_data').on('click', function() {
			var as_on_date = $("#as_on_date").val();
			var from_date = $("#from_date").val();
			if (as_on_date && from_date) {
				var parts = as_on_date.split('/');
				var parts2 = from_date.split('/');
				if (parts.length === 3 && parts2.length === 3) {
					var fromParsed = parseDMY(from_date);
					var toParsed = parseDMY(as_on_date);
					if (!fromParsed || !toParsed) {
						alert("Invalid date format. Please use dd/mm/yyyy.");
						return;
					}
					if (!isDateInFY(fromParsed)) {
						alert("From Date must be within current financial year: " + fyStartLabel + " to " + fyEndLabel + ".");
						return;
					}
					if (!isDateInFY(toParsed)) {
						alert("To Date must be within current financial year: " + fyStartLabel + " to " + fyEndLabel + ".");
						return;
					}
					if (fromParsed > toParsed) {
						alert("From Date cannot be after To Date.");
						return;
					}
					var formatted_date = parts[2] + '-' + parts[1] + '-' + parts[0];
					var formatted_date2 = parts2[2] + '-' + parts2[1] + '-' + parts2[0];
					var redirect_url = '<?php echo admin_url(); ?>accounting/ProfitLossTFormat/' + formatted_date2 + '/' + formatted_date;
					window.location.href = redirect_url; // Perform redirect
				} else {
					alert("Invalid date format. Please use dd/mm/yyyy.");
				}
			} else {
				alert("Please enter a date.");
			}
		});
	});
</script>