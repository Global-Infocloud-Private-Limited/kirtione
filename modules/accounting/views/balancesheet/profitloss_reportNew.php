<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    .th_total {
	padding-right: 10px;
    }
</style>

<div id="wrapper">
    <div class="panel_s">
        <div class="panel-body">
			<div class="row">
				<div class="col-md-12 text-centerr"  >
					<nav aria-label="breadcrumb" >
						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
							<li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
							<li class="breadcrumb-item active" aria-current="page"><b>Profit And Loss</b></li>
							
						</ol>
					</nav>
					<hr style="margin-Bottom:12px !important;">
				</div>
			</div>
            <div class="row ">
                <div class="col-md-5">
                    <a class="btn btn-default" href="javascript:void(0);" style="margin-bottom: 20px;margin-left: 10px;" onclick="printPage();">Print</a>
				</div>
			</div>
            <div class="row ">
                <div class="col-md-10">
                    <?php
						$fy = $this->session->userdata('finacial_year');
						$lastFy = $fy - 1;
						$fy_ = $fy + 1;
						$lastFy_ = $lastFy + 1;
						$CurrYrLastDate = '31/03/20' . $fy_;
						$LastYrLastDate = '31/03/20' . $lastFy_;
					?>
                    <div class="page" id="DivIdToPrint">
                        <div id="accordion">
                            <div class="card">
                                <table class="tree">
                                    <thead>
                                        <tr class="tr_header">
                                            <th style=""><b>Particular</b></th>
                                            <th class="th_total"><b>Note No.</b></th>
                                            <th class="th_total"><b>
												<?php echo $CurrYrLastDate; ?>
											</b>
                                            </th>
                                            <th class="th_total"><b>
												<?php echo $LastYrLastDate; ?>
											</b>
                                            </th>
										</tr>
									</thead>
                                    <tbody>
                                        <?php
											$mainCount = 1000;
											$subCount = 2000;
											$AnnexureCount = 1;
											foreach ($profitLossData as $key => $value) {
											?>
                                            <tr id='maingroup' class='treegrid-"<?php echo $mainCount; ?>" parent-node expanded'>
												<td><?php echo $key; ?></td>
												<?php
                                                    if($key == "I. Revenue from Operation" || $key == "II. Other Income" || $key == "III. Total Revenue (I + II)"){
													?>
                                                    <td style="text-align:center;"><a href="#" onclick="GetAnnexure('<?php echo $key;?>')"  class=" Open_Annexure mbot15"><?php echo $AnnexureCount;?></a></td>
													<?php
														}else{
													?>
													<td style="text-align:center;"></td>
                                                    <?php
													}
												?>
												<?php if($value->isPrimary == "Y"){
												?>
												    <td style="text-align:right;"><?php echo number_format($value->CurrentYear, 2, '.', ''); ?></td>
												    <td style="text-align:right;"><?php echo number_format($value->PriviousYear, 2, '.', ''); ?></td>
												<?php
												}else{
												?>
												    <td></td>
												    <td></td>
												    
												<?php
												}?>
												
											</tr>
                                            <?php
                                                if($value->isPrimary == "N"){
                                                ?>
                                                <?php
                                                    $letter = '@';
    												foreach ($value->details as $subKey => $subValue) {
    													$letterAscii = ord($letter);
    													$letterAscii++;
    													$letter = chr($letterAscii);
    													
    												?>
                                                    <tr id='subgroup' class='treegrid-"<?php echo $subCount; ?>" treegrid-parent-". $mainCount ." parent-node expanded'>
    													<td ><?php echo $subValue["name"]; ?></td>
    													<?php
    														if($subValue["name"] == "1. Cost of Goods Sold (COGS)" || $subValue["name"] == "2. Employee benefits expense" || $subValue["name"] == "3. Finance Costs" || $subValue["name"] == "5. Other Expenses"){
    														?>
    														<td style="text-align:center;"><a href="#" onclick="GetAnnexure('<?php echo $subValue["name"];?>')"  class=" Open_Annexure mbot15"><?php echo $AnnexureCount."(".$letter.")";?></a></td>
    														<?php
    															}else{
    														?>
                                                            <td style="text-align:center;"></td>
    														<?php
    														}
    													?>
    													
    													<td style="text-align:right;"><?php echo number_format($subValue["CurrentYear"], 2, '.', ''); ?></td>
    													<td style="text-align:right;"><?php echo number_format($subValue["PriviousYear"], 2, '.', ''); ?></td>
    												</tr>
    												<?php
    													$subCount++;
    												}
    												?>  
                                            <?php
                                                }
                                            ?>
											<?php
												$mainCount++;
												$AnnexureCount++;
											}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
    
    <div class="modal fade" id="revenue_from_operation-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Revenue from Operation</h4>
				</div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table_annexure">
                                <table class="tree table-bordered table_tradePayable_data" id="table_tradePayable_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                            <?php
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<th><?php echo $val["name"];?></th>
												<?php
												}    
											?>
                                            <th>Total</th>
										</tr>
									</thead>
                                    <tbody>
                                        <tr>
                                            <td>Sale Amount</td>
											<?php
                                                $TotalSaleAmt = 0;
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<td style="text-align:right;"><?php echo number_format($val["SaleAmt"], 2, '.', ',');?></td>
												<?php
													$TotalSaleAmt += $val["SaleAmt"];
												}    
											?>
                                            <td style="text-align:right;"><?php echo number_format($TotalSaleAmt, 2, '.', ',');?></td>
										</tr>
                                        
                                        <tr>
                                            <td>Sale Return Amount</td>
											<?php
												foreach($ItemGroup as $key=>$val){
												?>
												<td style="text-align:right;">0.00</td>
												
												<?php
												}    
											?>
                                            <td style="text-align:right;">0.00</td>
										</tr>
									</tbody>
								</table>   
							</div>
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>
    
    
    <div class="modal fade" id="OtherIncome-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Other Income</h4>
				</div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table_annexure">
                                <table class="tree table-bordered table_OtherIncome_data" id="table_OtherIncome_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                            <th>Opening</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Closing</th>
										</tr>
									</thead>
                                    <tbody>
                                        <?php
                                            foreach($OtherIncomeSubgroup2Wise as $key=>$val){
                                                if($val["Opn"] == "0" && $val["CR"] == "0" && $val["DR"] == "0"){
                                                    
                                                }else{
                                            ?>
                                                <tr>
                                                    <td style="font-weight:700;"><?php echo $val["SubActGroupName1"];?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
    											</tr>
											<?php
											    $TotalOpnBal = 0;
                                                $TotalCR = 0;
                                                $TotalDR = 0;
                                                $TotalBalance = 0;
                                                foreach($val["SubGroup2"] as $SG2Key=>$SG2val){
                                                    $TotalCR += $SG2val["CR"];
                                                    $TotalDR += $SG2val["DR"];
                                                    $TotalBalance += $SG2val["Balance"];
                                                    $TotalOpnBal += $SG2val["Opn"];
                                                    if($SG2val["Opn"] == "0" && $SG2val["DR"] == "0" && $SG2val["CR"] == "0"){
                                                        
                                                    }else{
                                                ?>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $SG2val["ActSubGroupName2"];?></td>
                                                        <td style="text-align:right;"><?php echo number_format(abs($SG2val["Opn"]), 2, '.', ''); ?></td>
                                                        <td style="text-align:right;"><?php echo number_format($SG2val["CR"], 2, '.', ''); ?></td>
                                                        <td style="text-align:right;"><?php echo number_format($SG2val["DR"], 2, '.', ''); ?></td>
                                                        <td style="text-align:right;"><?php echo number_format(abs($SG2val["Balance"]), 2, '.', ''); ?></td>
    												</tr>
                                                <?php
                                                    }
												?>
                                                
												<?php
												}
                                                if($TotalOpn == "0" && $TotalCR == "0" && $TotalDR == "0"){}else{
                                            ?>
                                                <tr>
    												<td style="font-weight:700;">Total for <?php echo $val["SubActGroupName1"];?></td>
    												<td style="text-align:right;font-weight:700;"><?php echo number_format(abs($TotalOpnBal), 2, '.', ''); ?></td>
    												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalCR, 2, '.', ''); ?></td>
    												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalDR, 2, '.', ''); ?></td>
    												<td style="text-align:right;font-weight:700;"><?php echo number_format(abs($TotalBalance), 2, '.', ''); ?></td>
    											</tr>
                                            <?php
                                                }
											?>
                                            <?php
                                                }
											?>
											<?php
											}
										?>
										
                                        
									</tbody>
								</table>   
							</div>
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>
    
    <div class="modal fade" id="COGS-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">COGS Details</h4>
				</div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 style = "color:red;">COGS = Opening Inventory + Purchase Amount + Direct Expenses - Closing Inventory</h4>
						</div>
                        <div class="col-md-12">
                            <h4>Inventory Opening Value</h4>
                            <div class="table_annexure">
                                <table class="tree table-bordered table_TradeReceivable_data" id="table_TradeReceivable_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
											<?php
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<th><?php echo $val["name"];?></th>
												<?php
												}    
											?>
                                            <th>Total</th>
										</tr>
									</thead>
                                    <tbody>
                                        <tr>
                                            <td>Opening Amt</td>
                                            <?php
                                                $TotalOpnAmt = 0;
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<td style="text-align:right;"><?php echo number_format($val["GOpnQtyValue"], 2, '.', ',');?></td>
												<?php
													$TotalOpnAmt += $val["GOpnQtyValue"];
												}    
											?>
                                            <td style="text-align:right;"><?php echo number_format($TotalOpnAmt, 2, '.', ',');?></td>
										</tr>
									</tbody>
								</table>   
							</div>
						</div>
                        
                        <div class="col-md-12">
                            <h4>Total Purchase / Sale Details</h4>
                            <div class="table_annexure">
                                <table class="tree table-bordered table_TradeReceivable_data" id="table_TradeReceivable_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
											<?php
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<th><?php echo $val["name"];?></th>
												<?php
												}    
											?>
                                            <th>Total</th>
										</tr>
									</thead>
                                    <tbody>
										
                                        <tr>
                                            <td>Purchase Amt</td>
                                            <?php
                                                $TotalPurchAmt = 0;
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<td style="text-align:right;"><?php echo number_format($val["PurchAmt"], 2, '.', ',');?></td>
												<?php
													$TotalPurchAmt += $val["PurchAmt"];
												}    
											?>
                                            <td style="text-align:right;"><?php echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
										</tr>
										
										<tr>
                                            <td>Sale Amt</td>
                                            <?php
                                                $TotalSaleAmt = 0;
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<td style="text-align:right;"><?php echo number_format($val["SaleAmt"], 2, '.', ',');?></td>
												<?php
													$TotalSaleAmt += $val["SaleAmt"];
												}    
											?>
                                            <td style="text-align:right;"><?php echo number_format($TotalSaleAmt, 2, '.', ',');?></td>
										</tr>
                                        
									</tbody>
								</table>   
							</div>
						</div>
                        
                        
                        <div class="col-md-12">
                            <h4>Direct Expense</h4>
                            <div class="table_annexure">
                                <table class="tree table-bordered table_OtherIncome_data" id="table_OtherIncome_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                            <th>Opening</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Closing</th>
										</tr>
									</thead>
                                    <tbody>
                                        <?php
                                            foreach($DirectExpSubgroup2Wise as $key=>$val){
                                                if($val["Opn"] == "0" && $val["CR"] == "0" && $val["DR"] == "0"){
                                                    
                                                }else{
                                            ?>
                                                <tr>
                                                <td style="font-weight:700;"><?php echo $val["SubActGroupName1"];?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
											</tr>
											<?php
											    $TotalOpn = 0;
                                                $TotalCR = 0;
                                                $TotalDR = 0;
                                                $TotalBalance = 0;
                                                foreach($val["SubGroup2"] as $SG2Key=>$SG2val){
                                                    $TotalOpn += $SG2val["Opn"];
                                                    $TotalCR += $SG2val["CR"];
                                                    $TotalDR += $SG2val["DR"];
                                                    $TotalBalance += $SG2val["Balance"];
                                                    if($SG2val["Opn"] == "0" && $SG2val["DR"] == "0" && $SG2val["CR"] == "0"){
                                                        
                                                    }else{
                                                    ?>
                                                        <tr>
                                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $SG2val["ActSubGroupName2"];?></td>
                                                            <td style="text-align:right;"><?php echo number_format(abs($SG2val["Opn"]), 2, '.', ''); ?></td>
                                                            <td style="text-align:right;"><?php echo number_format($SG2val["CR"], 2, '.', ''); ?></td>
                                                            <td style="text-align:right;"><?php echo number_format($SG2val["DR"], 2, '.', ''); ?></td>
                                                            <td style="text-align:right;"><?php echo number_format(abs($SG2val["Balance"]), 2, '.', ''); ?></td>
        												</tr>
                                                    <?php
                                                    }
												}
												if($TotalOpn == "0" && $TotalCR == "0" && $TotalDR == "0"){}else{
												?>
												    <tr>
        												<td style="font-weight:700;">Total for <?php echo $val["SubActGroupName1"];?></td>
        												<td style="text-align:right;font-weight:700;"><?php echo number_format(abs($TotalOpn), 2, '.', ''); ?></td>
        												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalCR, 2, '.', ''); ?></td>
        												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalDR, 2, '.', ''); ?></td>
        												<td style="text-align:right;font-weight:700;"><?php echo number_format(abs($TotalBalance), 2, '.', ''); ?></td>
        											</tr>
												<?php
												}
											?>
                                                
                                            <?php
                                                }
											?>
										<?php
											}
										?>
										
                                        
									</tbody>
								</table>   
							</div>
						</div>
                        
                        
                        
                        <div class="col-md-12">
                            <h4>Closing Inventory Details (Inventory Value : <?php echo number_format($CurrentInventoryValue, 2, '.', ',');?>)</h4>
                            <div class="table_annexure">
                                <table class="tree table-bordered table_TradeReceivable_data" id="table_TradeReceivable_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
											<?php
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<th><?php echo $val["name"];?></th>
												<?php
												}    
											?>
                                            <th>Total</th>
										</tr>
									</thead>
                                    <tbody>
                                        <tr>
                                            <td>Closing Amt</td>
                                            <?php
                                                $TotalClosingAmt = 0;
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<td style="text-align:right;"><?php echo number_format($val["CurrentValue"], 2, '.', ',');?></td>
												<?php
													$TotalClosingAmt += $val["CurrentValue"];
												}    
											?>
                                            <td style="text-align:right;"><?php echo number_format($TotalClosingAmt, 2, '.', ',');?></td>
										</tr>
                                        <!--<tr>
                                            <td>Purchase Amt</td>
                                            <?php
                                                $TotalPurchAmt = 0;
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<td style="text-align:right;"><?php echo number_format($val["GPurchValue"], 2, '.', ',');?></td>
												<?php
													$TotalPurchAmt += $val["GPurchValue"];
												}    
											?>
                                            <td style="text-align:right;"><?php echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
										</tr>
                                        <tr>
                                            <td>Sale Amt</td>
											<?php
												$TotalSaleAmt = 0;
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<td style="text-align:right;"><?php echo number_format($val["GSaleValue"], 2, '.', ',');?></td>
												<?php
													$TotalSaleAmt += $val["GSaleValue"];
												}    
											?>
                                            <td style="text-align:right;"><?php echo number_format($TotalSaleAmt, 2, '.', ',');?></td>
										</tr>
                                        <tr>
                                            <td>Production Amt</td>
											<?php
												$TotalPrdAmt = 0;
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<td style="text-align:right;"><?php echo number_format($val["GPrdValue"], 2, '.', ',');?></td>
												<?php
													$TotalPrdAmt += $val["GPrdValue"];
												}    
											?>
                                            <td style="text-align:right;"><?php echo number_format($TotalPrdAmt, 2, '.', ',');?></td>
										</tr>
                                        <tr>
                                            <td>Issue Amt</td>
											<?php
												$TotalIssueAmt = 0;
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<td style="text-align:right;"><?php echo number_format($val["GIssueValue"], 2, '.', ',');?></td>
												<?php
													$TotalIssueAmt += $val["GIssueValue"];
												}    
											?>
                                            <td style="text-align:right;"><?php echo number_format($TotalIssueAmt, 2, '.', ',');?></td>
										</tr>-->
									</tbody>
								</table>   
							</div>
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>
    
    <div class="modal fade" id="EmpBen-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Employee Benefits Expense</h4>
				</div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table_annexure">
                                <table class="tree table-bordered table_OtherIncome_data" id="table_OtherIncome_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                            <th>Opening</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Closing</th>
										</tr>
									</thead>
                                    <tbody>
                                        <?php
                                            foreach($EMPBenSubgroup2Wise as $key=>$val){
											?>
                                            <tr>
                                                <td style="font-weight:700;"><?php echo $val["SubActGroupName1"];?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
											</tr>
											<?php
											    $TotalOpn = 0;
                                                $TotalCR = 0;
                                                $TotalDR = 0;
                                                $TotalBalance = 0;
                                                foreach($val["SubGroup2"] as $SG2Key=>$SG2val){
                                                    $TotalOpn += $SG2val["Opn"];
                                                    $TotalCR += $SG2val["CR"];
                                                    $TotalDR += $SG2val["DR"];
                                                    $TotalBalance += $SG2val["Balance"];
												?>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $SG2val["ActSubGroupName2"];?></td>
                                                    <td style="text-align:right;"><?php echo number_format(abs($SG2val["Opn"]), 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["CR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["DR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format(abs($SG2val["Balance"]), 2, '.', ''); ?></td>
												</tr>
												<?php
												}
                                                
											?>
											<tr>
												<td style="font-weight:700;">Total for <?php echo $val["SubActGroupName1"];?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format(abs($TotalOpn), 2, '.', ''); ?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalCR, 2, '.', ''); ?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalDR, 2, '.', ''); ?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format(abs($TotalBalance), 2, '.', ''); ?></td>
											</tr>
											<?php
											}
										?>
										
                                        
									</tbody>
								</table>   
							</div>
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>
    
    <div class="modal fade" id="FinCost-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Finance Costs</h4>
				</div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table_annexure">
                                <table class="tree table-bordered table_OtherIncome_data" id="table_OtherIncome_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                            <th>Opening</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Closing</th>
										</tr>
									</thead>
                                    <tbody>
                                        <?php
                                            foreach($FinCostSubgroup2Wise as $key=>$val){
											?>
                                            <tr>
                                                <td style="font-weight:700;"><?php echo $val["SubActGroupName1"];?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
											</tr>
											<?php
											    $TotalOpn = 0;
                                                $TotalCR = 0;
                                                $TotalDR = 0;
                                                $TotalBalance = 0;
                                                foreach($val["SubGroup2"] as $SG2Key=>$SG2val){
                                                    $TotalOpn += $SG2val["Opn"];
                                                    $TotalCR += $SG2val["CR"];
                                                    $TotalDR += $SG2val["DR"];
                                                    $TotalBalance += $SG2val["Balance"];
												?>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $SG2val["ActSubGroupName2"];?></td>
                                                    <td style="text-align:right;"><?php echo number_format(abs($SG2val["Opn"]), 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["CR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["DR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format(abs($SG2val["Balance"]), 2, '.', ''); ?></td>
												</tr>
												<?php
												}
                                                
											?>
											<tr>
												<td style="font-weight:700;">Total for <?php echo $val["SubActGroupName1"];?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format(abs($TotalOpn), 2, '.', ''); ?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalCR, 2, '.', ''); ?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalDR, 2, '.', ''); ?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format(abs($TotalBalance), 2, '.', ''); ?></td>
											</tr>
											<?php
											}
										?>
										
                                        
									</tbody>
								</table>   
							</div>
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>
    
    <div class="modal fade" id="OtherExp-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Other Expense</h4>
				</div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table_annexure">
                                <table class="tree table-bordered table_OtherIncome_data" id="table_OtherIncome_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                            <th>Opening</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Closing</th>
										</tr>
									</thead>
                                    <tbody>
                                        <?php
                                            foreach($IndirectExpSubgroup2Wise as $key=>$val){
                                                if($val["Opn"] == "0" && $val["CR"] == "0" && $val["DR"] == "0"){
                                                    
                                                }else{
                                            ?>  
                                                <tr>
                                                    <td style="font-weight:700;"><?php echo $val["SubActGroupName1"];?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
    											</tr>
											<?php
                                                $TotalCR = 0;
                                                $TotalDR = 0;
                                                $TotalBalance = 0;
                                                foreach($val["SubGroup2"] as $SG2Key=>$SG2val){
                                                    $TotalCR += $SG2val["CR"];
                                                    $TotalDR += $SG2val["DR"];
                                                    $TotalBalance += $SG2val["Balance"];
                                                    if($SG2val["Opn"] == "0" && $SG2val["DR"] == "0" && $SG2val["CR"] == "0"){
                                                        
                                                    }else{
                                                ?>
                                                        <tr>
                                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $SG2val["ActSubGroupName2"];?></td>
                                                            <td style="text-align:right;"><?php echo number_format($SG2val["Opn"], 2, '.', ''); ?></td>
                                                            <td style="text-align:right;"><?php echo number_format($SG2val["CR"], 2, '.', ''); ?></td>
                                                            <td style="text-align:right;"><?php echo number_format($SG2val["DR"], 2, '.', ''); ?></td>
                                                            <td style="text-align:right;"><?php echo number_format($SG2val["Balance"], 2, '.', ''); ?></td>
        												</tr>
                                                <?php
                                                    }
												?>
                                                
												<?php
												}
												if($TotalOpn == "0" && $TotalCR == "0" && $TotalDR == "0"){}else{
											?>
											        <tr>
        												<td style="font-weight:700;">Total for <?php echo $val["SubActGroupName1"];?></td>
        												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalOpn, 2, '.', ''); ?></td>
        												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalCR, 2, '.', ''); ?></td>
        												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalDR, 2, '.', ''); ?></td>
        												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalBalance, 2, '.', ''); ?></td>
        											</tr>
											<?php
												}
                                                
											?>
											
                                            <?php
                                                }
											?>
                                            
											<?php
											}
										?>
										
                                        
									</tbody>
								</table>   
							</div>
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>
    
    <?php init_tail(); ?>
    <style>
        table  { border-collapse: collapse; width: 100%; }
        th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
        th     { background: #50607b;color: #fff !important; }
        .table_annexure {
		overflow: auto;
		max-height: 60vh;
		width: 98%;
		position: relative;
		top: 0px;
        }
	</style>
    <script>
        function GetAnnexure(Name){
            if(Name == "I. Revenue from Operation"){
                $('#revenue_from_operation-modal').modal('show');
				}else if(Name == "II. Other Income"){
                $('#OtherIncome-modal').modal('show');
				}else if(Name == "1. Cost of Goods Sold (COGS)"){
                $('#COGS-modal').modal('show');
				}else if(Name == "2. Employee benefits expense"){
                $('#EmpBen-modal').modal('show');
				}else if(Name == "3. Finance Costs"){
                $('#FinCost-modal').modal('show');
				}else if(Name == "5. Other Expenses"){
                $('#OtherExp-modal').modal('show');
			}
		}
        
	</script>
    <script type="text/javascript">
        function printPage() {
            var html_filter_name = $('.report_for').html();
            var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
            var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">' + document.getElementsByTagName('table')[0].innerHTML + '</table>';
            var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
            heading_data += '<tr>';
            heading_data += '<td style="text-align:center;"colspan="3">Profit Loss Sheet</td>';
            heading_data += '</tr>';
            heading_data += '<tr>';
            heading_data += '</tbody></table>';
            var print_data = stylesheet + heading_data + tableData
            newWin = window.open("");
            newWin.document.write(print_data);
            newWin.print();
            newWin.close();
		};
	</script>	