<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-purchase_request          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
.table-purchase_request thead th { position: sticky; top: 0; z-index: 1; }
.table-purchase_request tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%;margin-top:0px; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
tbody#for_uppercase{
    text-transform:uppercase;
}

.btn-top-toolbar {
    position: fixed;
    top: 8.5%;
    padding:5px 0px;
    -webkit-box-shadow: 0 -4px 1px -4px rgba(0,0,0,.1);
    box-shadow: 0 -4px 1px -4px rgba(0,0,0,.1);
    /*background: #50607b;*/
    color:#fff;
    /*width: calc(100% - 211px);*/
    /*width:100%;*/
    z-index: 5;
    border-top: 1px solid #ededed;
}
</style>

<?php
    $qcSums = [];             
    $paramNames = [];         
    $dispatchSums = [];    
    
    $accountDispatchQty = [];
    foreach ($OrderDetails->details as $detail) {
        $accountId = $detail['AccountID'];
        $dispatchQty = floatval($detail['DispatchQty']);
        $accountDispatchQty[$accountId] = $dispatchQty;
    }
    
    foreach ($OrderDetails->qcdetails as $qc) {
        $paramId = $qc['Parameter_ID'];
        $paramName = $qc['ItemParameterName'];
        $qcValue = floatval($qc['Qc_Value']);
        $accountId = $qc['AccountID'];
    
        $dispatchQty = isset($accountDispatchQty[$accountId]) ? $accountDispatchQty[$accountId] : 0;
    
        $paramNames[$paramId] = $paramName;
    
        if (!isset($qcSums[$paramId])) {
            $qcSums[$paramId] = 0;
            $dispatchSums[$paramId] = 0;
        }
    
        $qcSums[$paramId] += $qcValue * $dispatchQty;
        $dispatchSums[$paramId] += $dispatchQty;
    }
    
    $qcAverages = [];
    foreach ($qcSums as $paramId => $weightedSum) {
        $average = $dispatchSums[$paramId] > 0 ? round($weightedSum / $dispatchSums[$paramId], 2) : 0;
        $qcAverages[$paramId] = [
            'name' => $paramNames[$paramId],
            'average' => $average
        ];
    }
    
    $TotalAvg = [];
    foreach ($qcSums as $paramId => $weightedSum) {
        $average = $dispatchSums[$paramId] > 0 ? round($weightedSum / $dispatchSums[$paramId], 2) : 0;
        $TotalAvg[$paramId] = [
            'name' => $paramNames[$paramId],
        ];
    }
?>


<div id="wrapper">
<div class="content">
	<div class="row">
        <div class="col-md-12">
        	<div class="panel_s">
            <div class="panel-body">
            <div class="clearfix mtop20"></div>
                <div class="row">
                    <div class="col-md-12 text-centerr"  >
        				<nav aria-label="breadcrumb" >
        					<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
        						<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
        						<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
        						<li class="breadcrumb-item active text-capitalize"><b>FPO Order Inward</b></li>
        					</ol>
        				</nav>
        				<hr style="margin-Bottom:12px !important;">
        			</div>
                    <div class="col-md-6">
                        <h4><b>FPO Dispatch Details</b></h4>
                        <div class="table-purchase_request tableFixHead2">
                            <table class="tree  table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                            <tbody id="for_uppercase">
                                <tr>
                                    <td style="width:8%;"><b>DIS.NO : </b></td>
                                    <td style="width:20%;"><?php echo $OrderDetails->dispatchID; ?></td>
                                </tr>
                                <tr>
                                    <td style="width:8%;"><b>PO.No. : </b></td>
                                    <td style="width:20%;"><?php echo $OrderDetails->OrderID; ?></td>
                                </tr>
                                <tr>
                                    <td style="width:8%;"><b>PO Date : </b></td>
                                    <td style="width:20%;"><?php echo _d($OrderDetails->Transdate); ?></td>
                                </tr>
                                <tr> 
                                    <td style="width:8%;"><b>FPO Name : </b></td>
                                    <td style="width:20%;"><?php echo $OrderDetails->FPOName; ?></td>
                                </tr>
                                <tr>  
                                    <td style="width:8%;"><b>FPO Rate : </b></td>
                                    <td style="width:20%;"><b><?php echo $OrderDetails->FpoRate; ?></b></td>
                                </tr> 
                                <tr> 
                                    <td style="width:8%;"><b>Item Name : </b></td>
                                    <td style="width:20%;"><?php echo $OrderDetails->ItemName; ?></td>
                                </tr>
                                <tr> 
                                    <td style="width:8%;"><b>Center Name : </b></td>
                                    <td style="width:20%;"><?php echo $OrderDetails->details[0]['CenterName']; ?></td>
                                </tr>
                                <tr> 
                                    <td style="width:8%;"><b>Vehicle No: </b></td>
                                    <td style="width:20%;"><?php echo $OrderDetails->details[0]['VehicleNo']; ?></td>
                                </tr>
                            </tbody>
                            </table>  
                        </div>
                    </div>
					
					<!--Farmer details-->
					<div class="clearfix"></div>
					<div class="row">
						<div class="col-md-10">
                            <span id="searchh2" style="display:none;">Loading.....</span>
                            <div class="table_purchase_report">
                                <table class="tree table table-striped table-bordered table_purchase_report" id="table_purchase_report" width="100%">
                                    <thead>
                                        <tr style="display:none;">
                                            <td colspan="9">
                                                <h5 style="text-align:center;">
                                                    <span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br>
                                                    <span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br>
                                                    <span class="report_for" style="font-size:10px;"></span>
                                                </h5>
                                            </td>
                                        </tr>
                                        <!-- JS will insert headers dynamically here -->
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>   
                            </div>
                        </div>
					</div>
					
					<hr style="margin-Bottom:12px !important;">
					
					<div class="row">
					    <div class="col-md-2">
    					     <div class="form-group">
                                <label for="total_wt">Total Weight(In Qtl)</label>
                                <input type="text" name="total_wt" id="total_wt" class="form-control" value="<?php echo number_format($OrderDetails->TotalWeight, 2);?>" readonly>
                            </div>
    					</div>
                        
                        <?php foreach ($qcAverages as $paramId => $data): ?>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="avg_<?php echo strtolower(str_replace(' ', '_', $data['name'])); ?>">
                                        Avg <?php echo htmlspecialchars($data['name']); ?>
                                    </label>
                                    <input type="text" 
                                           id="avg_<?php echo strtolower(str_replace(' ', '_', $data['name'])); ?>" 
                                           class="form-control" 
                                           value="<?php echo $data['average']; ?>" 
                                           readonly>
                                </div>
                            </div>
                        <?php endforeach; ?>
					</div>
					
					<hr style="margin-Bottom:12px !important;">
					
					<!-- Add Gross Weight -->
                    <form id="AddGrossWeight" method="POST" action="<?php echo admin_url(); ?>FpoOrder/AddGrossWeight"> 
                        <div class="col-md-2">
                            <div class="form-group" app-field-wrapper="AddGrossWeight">
                                <small class="req text-danger">* </small>
                                <label for="grossweight" class="control-label">Gross Weight</label>
                                <input type="text" name="grossweight" id="grossweight" class="form-control" value="<?php echo $OrderDetails->details[0]['GrossWeight']; ?>" onkeypress="return isNumber(this,event)">
                                <input type="text" name="FpoOrderID" id="FpoOrderID" value="<?php echo $OrderDetails->OrderID; ?>" hidden>
                                <input type="text" name="FpoDispatchID" id="FpoDispatchID" value="<?php echo $OrderDetails->dispatchID;; ?>" hidden>
    						</div>
    					</div>
    					<?php if (has_permission_new('Fpo_Inward', '', 'edit') || has_permission_new('Fpo_Inward', '', 'create')){
                         ?>
    					<div class="col-md-2" style="margin-top: 20px;">
    					    <button type="submit" class="btn btn-info btn-sm" <?php echo ($OrderDetails->details[0]['FpoStatus'] != 3) ? '' : 'disabled'; ?>>Add Gross Weight</button>
    					</div>
    					<?php } ?>
					</form>
					
					<!-- Add Tare Weight -->
					<form id="AddTareWeight" method="POST" action="<?php echo admin_url(); ?>FpoOrder/AddTareWeight"> 
                        <div class="col-md-2">
                            <div class="form-group" app-field-wrapper="AddTareWeight">
                                <small class="req text-danger">* </small>
                                <label for="tareweight" class="control-label">Tare Weight</label>
                                <input type="text" name="tareweight" id="tareweight" class="form-control" value="<?php echo $OrderDetails->details[0]['TareWeight']; ?>" onkeypress="return isNumber(this,event)">
    						    <input type="text" name="orderid" id="orderid" value="<?php echo $OrderDetails->OrderID; ?>" hidden>
                                <input type="text" name="dispatchid" id="dispatchid" value="<?php echo $OrderDetails->dispatchID;; ?>" hidden>
    						</div>
    					</div>
    					<?php if (has_permission_new('Fpo_Inward', '', 'edit') || has_permission_new('Fpo_Inward', '', 'create')){
                         ?>
    					<div class="col-md-2" style="margin-top: 20px;">
    					    <button type="submit" class="btn btn-info btn-sm" <?php echo ($OrderDetails->details[0]['FpoStatus'] != 3) ? '' : 'disabled'; ?>>Add Tare Weight</button>
    					</div>
    					<?php } ?>
					</form>
					
					<div class="row">
                        <form id="stack_details_form" method="POST" action="<?php echo admin_url(); ?>FpoOrder/updateStackDetails"> 
                        <div class="col-md-10">
                            <h4>Center QC & Stack Details </h4>
                            <table class="tree table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                                <thead>
                                    <tr>
                                        <?php foreach ($qcAverages as $key => $value): ?>
                                            <th width="15%"><?php echo htmlspecialchars($value['name']); ?></th>
                                        <?php endforeach; ?>
                                        <th width="15%">Godown</th>
                                        <th width="15%">Chamber</th>
                                        <th width="15%">Stack</th>
                                        <th width="15%">Lot</th>
                                        <th width="10%">Weight(In Qtl)</th>
                                        <th width="10%">Bag Qty</th>
                                        <th width="5%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="stack_tbody">
                                    <tr class="item">
                                        <input type="text" name="OrderID" id="OrderID" value="<?php echo $OrderDetails->OrderID; ?>" hidden>
                                        <input type="text" name="DispatchID" id="DispatchID" value="<?php echo $OrderDetails->dispatchID;; ?>" hidden>
                                        <input type="text" name="PODate" id="PODate" value="<?php echo $OrderDetails->Transdate;; ?>" hidden>
                                        <input type="text" name="ItemID" id="ItemID" value="<?php echo $OrderDetails->ItemID;; ?>" hidden>
                                        <input type="text" name="PartyID" id="PartyID" value="<?php echo $OrderDetails->PartyID;; ?>" hidden>
                                        <input type="text" name="AccountID" id="AccountID" value="<?php echo $OrderDetails->details[0]->AccountID; ?>" hidden>
                                       <?php foreach ($qcAverages as $paramId => $param): 
                                            $safeId = 'param_' . $paramId;
                                        ?>
                                            <td>
                                                <input style="width:100%;" 
                                                       type="text" 
                                                       name="StackList[0][<?php echo $paramId; ?>]"   
                                                       id="<?php echo $safeId; ?>" 
                                                       value="" 
                                                       class="form-control" 
                                                       onkeypress="return isNumber(this,event)">
                                            </td>
                                        <?php endforeach; ?>

                                        
                                        <td>
                                            <div class="form-group" app-field-wrapper="Select Godown">
                                                <select name="GodownID" id="GodownID" class="selectpicker form-control" data-live-search="true">
                                                    <option value="" >Non Selected</option>
                                                    <?php if (!empty($WarehouseDetails)) : ?>
                                                        <?php foreach ($WarehouseDetails as $warehouse) : ?>
                                                            <option value="<?= htmlspecialchars($warehouse['AccountID']) ?>">
                                                                <?= htmlspecialchars($warehouse['w_name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
            									</select>
            								</div>
                                        </td>
                                        
                                        <td>
                                            <div class="form-group" app-field-wrapper="Select Chamber">
                                                <select name="chamber" id="chamber" class="selectpicker form-control" data-live-search="true">
                                                    <option value="" >Non Selected</option>
            									</select>
            								</div>
                                        </td>
                                        <td>
                                            <div class="form-group" app-field-wrapper="Select Stack">
                                                <select name="StackID" id="StackID" class="selectpicker form-control" data-live-search="true">
                                                    <option value="" >Non Selected</option>
            									</select>
            								</div>
                                        </td>
                                        <td>
                                            <div class="form-group" app-field-wrapper="Select LOT">
                                                <select name="LOTID" id="LOTID" class="selectpicker form-control" data-live-search="true">
                                                    <option value="" >Non Selected</option>
            									</select>
            								</div>
                                        </td>
                                        <td>
                                            <div class="form-group" app-field-wrapper="Weight">
                                                <input style="height:36px;width:100%;" data-quantity class="form-control" id="lotweight" value="" onkeypress="return isNumber(this,event)">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group" app-field-wrapper="Bag">
                                                <input style="height:36px;width:100%;" class="form-control" id="bag" value="" onkeypress="return isNumber(this,event)">
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <button class="updateCheck form-control" type="button" style="font-size:20px;color:green;" onclick="addrow()"><i class="fa fa-plus"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                                </table>
                        </div>
                        <?php if (has_permission_new('Fpo_Inward', '', 'edit') || has_permission_new('Fpo_Inward', '', 'create')){
                         ?>
                        <div class="col-md-3" >
                            <div class="form-group" >
            			        <button class=" btn btn-success btn-sm" style="margin-top: 10px;" type="button" id="StackSubmit" <?php echo ($OrderDetails->details[0]['FpoStatus'] != 3) ? '' : 'disabled'; ?>>Update Stack Details</button>
            			     </div>
            			</div>
            			<?php } ?>
            			</form>
                    </div>
                    
                    <?php
                        $filtered_weighted_avg = [];
                        
                        foreach ($weighted_avg as $key => $val) {
                            if (is_int($key)) {
                                $filtered_weighted_avg[$key] = $val; 
                            }
                        }
                        $TotalAvg = array_values($TotalAvg);
                        foreach ($TotalAvg as $key => &$data) {
                            $data['val'] = $filtered_weighted_avg[$key] ?? '';
                        }
                        unset($data);
                        
                    ?>
                    <div class="row">
                        <?php foreach ($TotalAvg as $paramId => $data): ?>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="avg_<?php echo strtolower(str_replace(' ', '_', $data['name'])); ?>">
                                        Kirti Avg <?php echo htmlspecialchars($data['name']); ?>
                                    </label>
                                    <input type="text" 
                                        id="avg_<?php echo strtolower(str_replace(' ', '_', $data['name'])); ?>" 
                                        class="form-control" 
                                        value="<?php echo isset($data['val']) && is_numeric($data['val']) ? number_format($data['val'], 2) : htmlspecialchars($data['val']); ?>" 
                                        readonly>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php
                            if($existingStackList){
                        ?>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <button class=" btn btn-warning btn-sm" style="margin-top: 10px;" type="button" id="DebitEntry" <?php if ($DebitExist->FpoDebitEntry == 'Y') echo 'disabled'; ?>>Generate Debit Entry</button>
                                    </div>
                                </div>
                        <?php
                            }
                        ?>
                            
                            <div class="col-md-4">
                                <?php 
                                    //echo "<pre>";
                                    //print_r($existingStackList);
                                ?>
                            </div>
                    </div>
                    
                	<?php if (has_permission_new('Fpo_Inward', '', 'edit') || has_permission_new('Fpo_Inward', '', 'create')){
                     ?>
                    <div class="row">
                        <div class="col-md-3" >
                            <div class="form-group" >
            			        <button class=" btn btn-success btn-sm" style="margin-top: 10px;" type="button" id="VehicleExit" <?php echo ($OrderDetails->details[0]['FpoStatus'] == 2) ? '' : 'disabled'; ?>>Exit</button>
            			     </div>
            			</div>
        			</div>
        			<?php } ?>
						
                </div>
            </div>
            </div>
        </div>
        
    </div>
</div>

<?php init_tail(); ?>
<?php $orderID = $this->uri->segment(4); 
    $id = $this->uri->segment(5); 
    
    $parameterIds = array_keys($existingStackList[0]); 
    $parameterIds = array_filter($parameterIds, 'is_int');
?>
<script>
    const ExistingStackList = <?php echo json_encode($existingStackList); ?>;
    const EditQcParameterList = <?php echo json_encode(array_values($parameterIds)); ?>;
    const QcParameterList = <?php echo json_encode($qcAverages); ?>;
    const TotalAverage = <?php echo json_encode($TotalAvg); ?>;
</script>

<script>
   $(document).ready(function () {
        if (Array.isArray(ExistingStackList)) {
            ExistingStackList.forEach((row, index) => {
                let html = '<tr class="item">';
               
                EditQcParameterList.forEach(paramId => {
                    const value = row[paramId] ?? '';
                    html += '<td>';
                    html += '<input style="width:100%;height:30px" class="form-control" ' +
                            'name="StackList[' + index + '][' + paramId + ']" ' +
                            'value="' + value + '" readonly>';
                    html += '</td>';
                });
                
                html += '<td><input type="hidden" name="StackList[' + index + '][GodownID]" value="' + row.GodownID + '">' + row.GodownName + '</td>';
                html += '<td><input type="hidden" name="StackList[' + index + '][CHID]" value="' + row.CHID + '">' + row.ChemberName + '</td>';
                html += '<td><input type="hidden" name="StackList[' + index + '][StackID]" value="' + row.StackID + '">' + row.StackName + '</td>';
                html += '<td><input type="hidden" name="StackList[' + index + '][LOTID]" value="' + row.LOTID + '">' + row.LotName + '</td>';
                html += '<td><input type="hidden" name="StackList[' + index + '][lot_weight]" value="' + row.lot_weight + '">' + row.lot_weight + '</td>';
                html += '<td><input type="hidden" name="StackList[' + index + '][bag]" value="' + row.bag + '">' + row.bag + '</td>';
                html += '<td><button class="remove form-control" type="button" style="font-size:20px;color:red;"><i class="fa fa-trash"></i></button></td>';
                html += '</tr>';
    
                $('#stack_tbody').append(html);
            });
            
            TotalLot = ExistingStackList.length;
        }
    });
</script>

<script>
    $('#DebitEntry').on('click', function() 
    {
        var OrderID = <?php echo json_encode($OrderDetails->OrderID); ?>;
        var DispatchID = <?php echo json_encode($OrderDetails->dispatchID); ?>;
        var ItemID = <?php echo json_encode($OrderDetails->ItemID); ?>;
        var TotalWt = $('#total_wt').val();
        
        $.ajax({
            url: "<?php echo admin_url(); ?>FpoOrder/GenerateDebitEntry",
            dataType: "JSON",
            method: "POST",
            data: { OrderID: OrderID,DispatchID:DispatchID,QcParameterList:QcParameterList,TotalAverage:TotalAverage,ItemID: ItemID,TotalWt:TotalWt },
            beforeSend: function () {
                $('#searchh2').show(); 
            },
            complete: function () {
                $('#searchh2').hide(); 
            },
            success: function (data) {
                if (data.status) {
                    alert("Debit entry generated successfully.");
                    location.reload();
                } else {
                    alert("Failed to exit vehicle.");
                }
            },
            error: function () {
                alert('AJAX error while exit.');
            }
        });
    });
</script>

<script>
document.getElementById('AddGrossWeight').addEventListener('submit', function(e) {
    var grossWeight = parseFloat(document.getElementById('grossweight').value);
    var totalWeight = parseFloat(document.getElementById('total_wt').value.replace(/,/g, ''));

    if (isNaN(grossWeight)) {
        alert('Please enter a valid Gross Weight.');
        e.preventDefault();
        return;
    }

    if (grossWeight < totalWeight) {
        alert('Gross Weight cannot be less than Total Weight.');
        e.preventDefault();
    }
});

document.getElementById('AddTareWeight').addEventListener('submit', function(e) {
    var TareWeight = parseFloat(document.getElementById('tareweight').value);
    var grossWeight = parseFloat(document.getElementById('grossweight').value);

    if (isNaN(TareWeight)) {
        alert('Please enter a valid Tare Weight.');
        e.preventDefault();
        return;
    }

    if (TareWeight > grossWeight) {
        alert('Gross Weight cannot be less than Tare Weight.');
        e.preventDefault();
    }
});
</script>
<script>
	$(document).ready(function () {
        var OrderID = <?php echo json_encode($orderID); ?>;
        var id = <?php echo json_encode($id); ?>;
        $.ajax({
            url: "<?php echo admin_url(); ?>FpoOrder/GetFpoOrderInward",
            method: "POST",
            dataType: "JSON",
            data: {OrderID: OrderID,id:id },
            beforeSend: function () {
                $('#searchh2').show();
                $('.table_purchase_report tbody').hide();
            },
            complete: function () {
                $('#searchh2').hide();
                $('.table_purchase_report tbody').show();
            },
            success: function (data) {
                var html = '';
                var headerHtml = '';
                var i = 1;
                let uniqueParameters = [];
                data.forEach(row => {
                    if (row.qcdetails) {
                        row.qcdetails.forEach(qc => {
                            if (!uniqueParameters.some(p => p === qc.ItemParameterName)) {
                                uniqueParameters.push(qc.ItemParameterName);
                            }
                        });
                    }
                });
               
                headerHtml += '<tr>';
                headerHtml += '<th style="width:3%">Sr.No</th>';
                headerHtml += '<th style="width:10%">Farmer Name</th>';
                headerHtml += '<th style="width:5%">Farmer Rate</th>';
                headerHtml += '<th style="width:5%">Order Wt (Qtl)</th>';
                headerHtml += '<th style="width:5%">Order Bag</th>';
                headerHtml += '<th style="width:5%">Pending Qty(Qtl)</th>';
                headerHtml += '<th style="width:5%">Dispatch Qty(Qtl)</th>';
                headerHtml += '<th style="width:5%">Dispatch Bag</th>';
                headerHtml += '<th style="width:5%">Total Dispatch Wt (Qtl)</th>';
                uniqueParameters.forEach(param => {
                    headerHtml += '<th>' + param + '</th>';
                    headerHtml += '<th>' + param + ' Amt</th>';
                });
    
                headerHtml += '<th style="width:7%">Total Deduction</th>';
                headerHtml += '<th style="width:10%">Net Rate</th>';
                headerHtml += '<th style="width:10%">Amount</th>';
                headerHtml += '</tr>';
    
                $('.table_purchase_report thead').html(headerHtml);
                let TotalOrderWeight = 0;
                let TotalOrderBag = 0;
                let TotalPendingWeight = 0;
                let TotalDispatchWeight = 0;
                let TotalDispatchBag = 0;
                let AllTotalDispatchWeight = 0;
                let TotalDeductionAmt = 0;
                let TotalNetAmt = 0;
                data.forEach(function (row) {
                    html += '<tr>';
                    html += '<td style="text-align:center;">' + i + '</td>';
                    html += '<td>' + row.farmer_name + '</td>';
                    html += '<td style="text-align:center;">' + row.farmer_rate + '</td>';
                    html += '<td style="text-align:center;">' + row.weight + '</td>';
                    TotalOrderWeight += parseFloat(row.weight);
                    html += '<td style="text-align:center;">' + row.Bag + '</td>';
                    TotalOrderBag += parseFloat(row.Bag);
                    html += '<td style="text-align:center;">' + row.PendingQty.toFixed(2) + '</td>';
                    TotalPendingWeight += parseFloat(row.PendingQty);
                    html += '<td style="text-align:center;">' + row.DispatchQty + '</td>';
                    TotalDispatchWeight += parseFloat(row.DispatchQty);
                    html += '<td style="text-align:center;">' + row.DispatchBag + '</td>';
                    TotalDispatchBag += parseFloat(row.DispatchBag);
                    var dispatchWeight = parseFloat(row.DispatchWt) || 0;
                    AllTotalDispatchWeight += parseFloat(dispatchWeight);
                    html += '<td style="text-align:center;">' + dispatchWeight.toFixed(2) + '</td>';
                    
                    uniqueParameters.forEach(param => {
                        let matchedQc = (row.qcdetails || []).find(qc => qc.ItemParameterName === param);
                        let value = matchedQc ? parseFloat(matchedQc.Qc_Value).toFixed(2) : '0.00';
                        let amount = matchedQc ? parseFloat(matchedQc.Qc_Amt).toFixed(2) : '0.00';
    
                        html += '<td style="text-align:center;">' + value + '</td>';
                        html += '<td style="text-align:right;">' + amount + '</td>';
                    });
    
                    html += '<td style="text-align:right;">' + row.Deduction + '</td>';
                    TotalDeductionAmt += parseFloat(row.Deduction);
                    html += '<td style="text-align:center;">' + row.NetRate + '</td>';
                    html += '<td style="text-align:right;">' + row.NetAmt + '</td>';
                    TotalNetAmt += parseFloat(row.NetAmt);
                    html += '</tr>';
                    i++;
                });
                html += '<tr>';
                html += '<td colspan="3" style="text-align:right;"><b>Total</b></td>';
                html += '<td style="text-align:center;"><b>' + TotalOrderWeight.toFixed(2) + '</b></td>';
                html += '<td style="text-align:center;"><b>' + TotalOrderBag + '</b></td>';
                html += '<td style="text-align:center;"><b>' + TotalPendingWeight.toFixed(2) + '</b></td>';
                html += '<td style="text-align:center;"><b>' + TotalDispatchWeight.toFixed(2) + '</b></td>';
                html += '<td style="text-align:center;"><b>' + TotalDispatchBag + '</b></td>';
                html += '<td style="text-align:center;"><b>' + AllTotalDispatchWeight.toFixed(2) + '</b></td>';
                uniqueParameters.forEach(param => {
                    html += '<td colspan="2"></td>';
                });
                
                html += '<td style="text-align:right;"><b>' + TotalDeductionAmt.toFixed(2) + '</b></td>';
                html += '<td></td>';
                html += '<td style="text-align:right;"><b>' + TotalNetAmt.toFixed(2) + '</b></td>';
                html += '</tr>';
                $('.table_purchase_report tbody').html(html);
            }
        });
    });
    
    var TotalLot = 0; 
    function addrow()
    {
        var GrossWt = parseFloat($('#grossweight').val()) || 0;
        var TareWt = parseFloat($('#tareweight').val()) || 0;
        
        var GodownID = $('#GodownID').val();
        var CHID = $('#chamber').val();
        var StackID = $('#StackID').val();
        var LOTID = $('#LOTID').val();
        let lot_weight = parseFloat($('#lotweight').val()) || 0;
        var bag = $('#bag').val();
        var Total_Weight = parseFloat($('#total_wt').val()) || 0;
        
        var existingTotal = 0;
        $('#stack_tbody').find('input[name^="StackList"]').each(function () {
            if ($(this).attr('name').includes('[lot_weight]')) {
                var val = parseFloat($(this).val()) || 0;
                existingTotal += val;
            }
        });
       
        /*if (existingTotal + lot_weight > Total_Weight) {
            alert("Total Stack Weight exceeds Net Weight (" + Total_Weight + " Qtl). Already added: " + existingTotal.toFixed(2) + " Qtl");
            return;
        }
        
        if(lot_weight > Total_Weight){
            alert("Total Stack Weight is greter than Net Weight");
        }else*/ if(GrossWt == ""){
            alert("please enter Gross Weight.");
        }else if(GrossWt < Total_Weight){
            alert("Gross Weight must be greater than Total Weight");
        }else if(TareWt &&  TareWt > GrossWt){
            alert("Tare Weight must be less than Gross Weight");
        }else if(GodownID == ""){
            alert("Please Select Godown");
        }else if(CHID == ""){
            alert("Please Select Chamber");
        }else if(StackID == ""){
            alert("Please Select Stack");
        }else if(LOTID == ""){
            alert("Please Select LOT");
        }else if(lot_weight == ""){
            alert("Please Enter Weight in MT");
        }else if(bag == ""){
            alert("Please Enter Bag Quantity");
        }else{
            var html = '';
            html +='<tr class="item">';
            for (const paramId in QcParameterList) {
                const safeId = 'param_' + paramId;
                const value = $('#' + safeId).val() || '';
                
                html += '<td>';
                html += '<input style="width:100%;height:30px" class="form-control" ' +
                        'name="StackList[' + TotalLot + '][' + paramId + ']" ' +
                        'value="' + value + '" readonly>';
                html += '</td>';
            
                $('#' + safeId).val('');
            }

            html += '<td><input type="hidden" name="StackList[' + TotalLot + '][GodownID]" value="' + GodownID + '">' + $('#GodownID option:selected').text() + '</td>';
            html += '<td><input type="hidden" name="StackList[' + TotalLot + '][CHID]" value="' + CHID + '">' + $('#chamber option:selected').text() + '</td>';
            html += '<td><input type="hidden" name="StackList[' + TotalLot + '][StackID]" value="' + StackID + '">' + $('#StackID option:selected').text() + '</td>';
            html += '<td><input type="hidden" name="StackList[' + TotalLot + '][LOTID]" value="' + LOTID + '">' + $('#LOTID option:selected').text() + '</td>';
        
            html += '<td><input type="hidden" name="StackList[' + TotalLot + '][lot_weight]" value="' + lot_weight + '">' + lot_weight + '</td>';
            html += '<td><input type="hidden" name="StackList[' + TotalLot + '][bag]" value="' + bag + '">' + bag + '</td>';

            html +='<td> <button class="remove form-control" type="button" style="font-size:20px;color:red;" ><i class="fa fa-trash"></i></button></td>';
            html +='</tr>';
            $('#stack_tbody').append(html);
            $('#GodownID').val('').selectpicker('refresh');
            $('#chamber').val('').selectpicker('refresh');
            $('#StackID').val('').selectpicker('refresh');
            $('#LOTID').val('').selectpicker('refresh');
            $('#lotweight').val('');
            $('#bag').val('');
            TotalLot++;
        }
    }
    
    $('#StackSubmit').click(function(){
        var GrossWt = parseFloat($('#grossweight').val()) || 0;
        var TareWt = parseFloat($('#tareweight').val()) || 0;
        var Total_Weight = parseFloat($('#total_wt').val()) || 0;
        
        var existingTotal = 0;
        $('#stack_tbody').find('input[name^="StackList"]').each(function () {
            if ($(this).attr('name').includes('[lot_weight]')) {
                var val = parseFloat($(this).val()) || 0;
                existingTotal += val;
            }
        });
        if(GrossWt == "" || GrossWt <= 0){
            alert("Please Enter Gross Weight");
        }else if(TareWt == "" || TareWt <= 0){
            alert("Please Enter Tare Weight");
        }else if(GrossWt <= TareWt){
            alert("Please Enter Tare Weight is less than Gross Weight");
        }else if(Total_Weight <= 0){
            alert("Dispatched Weight is Zero, Please Add Dispatched Weight");
        }else if(existingTotal <= 0){
            alert("Please add Stack Details");
        }/*else if(existingTotal > Total_Weight){
            alert("Total Stack Weight must be equal to Net Weight");
        }else if(existingTotal < Total_Weight){
            alert("Total Stack Weight must be equal to Net Weight");
        }*/else{
            $('#stack_details_form').submit();
        }
    });
    
    $('#VehicleExit').click(function(){
        var OrderID = <?php echo json_encode($orderID); ?>;
        var DispatchID = <?php echo json_encode($id); ?>;
        var GrossWt = parseFloat($('#grossweight').val()) || 0;
        var TareWt = parseFloat($('#tareweight').val()) || 0;
        var Total_Weight = parseFloat($('#total_wt').val()) || 0;
        
        var existingTotal = 0;
        $('#stack_tbody').find('input[name^="StackList"]').each(function () {
            if ($(this).attr('name').includes('[lot_weight]')) {
                var val = parseFloat($(this).val()) || 0;
                existingTotal += val;
            }
        });
        if(GrossWt == "" || GrossWt <= 0){
            alert("Please Enter Gross Weight");
        }else if(TareWt == "" || TareWt <= 0){
            alert("Please Enter Tare Weight");
        }else if(GrossWt <= TareWt){
            alert("Please Enter Tare Weight is less than Gross Weight");
        }else if(Total_Weight <= 0){
            alert("Dispatched Weight is Zero, Please Add Dispatched Weight");
        }else if(existingTotal <= 0){
            alert("Please add Stack Details");
        }/*else if(existingTotal > Total_Weight){
            alert("Total Stack Weight must be equal to Net Weight");
        }else if(existingTotal < Total_Weight){
            alert("Total Stack Weight must be equal to Net Weight");
        }*/else{
            $.ajax({
                url: "<?php echo admin_url(); ?>FpoOrder/ExitVehicleOrder",
                dataType: "JSON",
                method: "POST",
                data: { OrderID: OrderID,DispatchID:DispatchID },
                beforeSend: function () {
                    $('#searchh2').show(); 
                },
                complete: function () {
                    $('#searchh2').hide(); 
                },
                success: function (data) {
                    if (data.status) {
                        alert("Vehicle exited successfully.");
                        location.reload();
                    } else {
                        alert("Failed to exit vehicle.");
                    }
                },
                error: function () {
                    alert('AJAX error while exit.');
                }
            });
        }
    });
</script>

<script>
    $(document).ready(function () {
        $('#GodownID').on('change', function () {
            var GodownID = $(this).val();

            if (GodownID !== '') {
                $.ajax({
                    url: "<?php echo admin_url(); ?>FpoOrder/GetChamberListInward",
                    method: "POST",
                    data: { GodownID: GodownID },
                    beforeSend: function () {
                        $('#searchh2').show(); 
                    },
                    complete: function () {
                        $('#searchh2').hide(); 
                    },
                    success: function (data) {
                        try {
                            var chambers = JSON.parse(data);
                            var $chamberSelect = $('#chamber');
                            
                            $chamberSelect.empty();
                            $chamberSelect.append('<option value="">Non Selected</option>');
                            
                            $.each(chambers, function (index, chamber) {
                                $chamberSelect.append(
                                    '<option value="' + chamber.CHID + '">' + chamber.ChaumberName + '</option>'
                                );
                            });
                            
                            $chamberSelect.selectpicker('refresh');
                        } catch (e) {
                            console.error('Parsing error:', e);
                            alert('Error parsing chamber data.');
                        }
                    },
                    error: function () {
                        alert('AJAX error while fetching chamber list.');
                    }
                });
            }
        });
        
        $('#chamber').on('change', function (){
            var CHID = $(this).val();
            
            if (CHID !== '') {
                $.ajax({
                    url: "<?php echo admin_url(); ?>FpoOrder/GetStackListInward",
                    method: "POST",
                    data: { CHID: CHID },
                    beforeSend: function () {
                        $('#searchh2').show(); 
                    },
                    complete: function () {
                        $('#searchh2').hide(); 
                    },
                    success: function (data) {
                        try {
                            var Stacks = JSON.parse(data);
                            var $stackselect = $('#StackID');
                            
                            $stackselect.empty();
                            $stackselect.append('<option value="">Non Selected</option>');
                            
                            $.each(Stacks, function (index, statck) {
                                $stackselect.append(
                                    '<option value="' + statck.StackID + '">' + statck.StackName + '</option>'
                                );
                            });
                            
                            $stackselect.selectpicker('refresh');
                        } catch (e) {
                            console.error('Parsing error:', e);
                            alert('Error parsing stack data.');
                        }
                    },
                    error: function () {
                        alert('AJAX error while fetching stack list.');
                    }
                });
            }
        });
        
        $('#StackID').on('change', function (){
            var StackID = $(this).val();
            
            if (StackID !== '') {
                $.ajax({
                    url: "<?php echo admin_url(); ?>FpoOrder/GetStackLotList",
                    method: "POST",
                    data: { StackID: StackID },
                    beforeSend: function () {
                        $('#searchh2').show(); 
                    },
                    complete: function () {
                        $('#searchh2').hide(); 
                    },
                    success: function (data) {
                        try {
                            var Lots = JSON.parse(data);
                            var $lotselect = $('#LOTID');
                            
                            $lotselect.empty();
                            $lotselect.append('<option value="">Non Selected</option>');
                            
                            $.each(Lots, function (index, lot) {
                                $lotselect.append(
                                    '<option value="' + lot.LOTID + '">' + lot.LotName + '</option>'
                                );
                            });
                            
                            $lotselect.selectpicker('refresh');
                        } catch (e) {
                            console.error('Parsing error:', e);
                            alert('Error parsing stack data.');
                        }
                    },
                    error: function () {
                        alert('AJAX error while fetching stack list.');
                    }
                });
            }
        });
    });
    
    $('#stack_tbody').on('click', '.remove', function () {
        // Removing the current row.
        $(this).closest('tr').remove();
    });
    
    function isNumber(txt,event){
        var charCode = (event.which) ? event.which : event.keyCode
        if (charCode == 46) {
            if (txt.value.indexOf(".") < 0)
                return true;
            else
                return false;
        }

        if (txt.value.indexOf(".") > 0) {
            var txtlen = txt.value.length;
            var dotpos = txt.value.indexOf(".");
            //Change the number here to allow more decimal points than 2
            if ((txtlen - dotpos) > 3)
            {
               return false; 
            } 
        }
        if (charCode > 31 && (charCode < 48 || charCode > 57)){
            return false;
        }
        return true;
    }
</script>

</body>
</html>
