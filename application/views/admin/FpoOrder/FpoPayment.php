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
    /*$qcSums = [];
    $qcCounts = [];
    
    foreach ($Qcdetails as $qc) {
        $param = $qc['ItemParameterName'];
        $value = floatval($qc['Qc_Value']);
    
        if (!isset($qcSums[$param])) {
            $qcSums[$param] = 0;
            $qcCounts[$param] = 0;
        }
        $qcSums[$param] += $value;
        $qcCounts[$param]++;
    }
    
    $qcAverages = [];
    foreach ($qcSums as $param => $sum) {
        $qcAverages[$param] = $qcCounts[$param] ? round($sum / $qcCounts[$param], 2) : 0;
    }*/
?>

<?php
    $details = $OrderDetails->details;   
    $qcdetails = $Qcdetails;             
    
    $weights = [];
    foreach ($details as $item) {
        $weights[$item['AccountID']] = floatval($item['NetWgt']);
    }
    
    $qcWeightedSums = [];
    $qcWeightTotals = [];

    foreach ($qcdetails as $qc) {
        $param = $qc['ItemParameterName'];
        $value = floatval($qc['Qc_Value']);
        $accountID = $qc['AccountID'];
        $weight = isset($weights[$accountID]) ? $weights[$accountID] : 0;
    
        if (!isset($qcWeightedSums[$param])) {
            $qcWeightedSums[$param] = 0;
            $qcWeightTotals[$param] = 0;
        }
    
        $qcWeightedSums[$param] += $value * $weight;
        $qcWeightTotals[$param] += $weight;
    }
    
    $qcAverages = [];
    foreach ($qcWeightedSums as $param => $weightedSum) {
        $totalWeight = $qcWeightTotals[$param];
        $qcAverages[$param] = $totalWeight > 0 ? round($weightedSum / $totalWeight, 2) : 0;
    }

?>

<div id="wrapper">
<div class="content">
	<div class="row">
        <div class="col-md-10">
        	<div class="panel_s">
            <div class="panel-body">
            <div class="clearfix mtop20"></div>
                <div class="row">
                    <div class="col-md-12 text-centerr"  >
        				<nav aria-label="breadcrumb" >
        					<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
        						<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
        						<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
        						<li class="breadcrumb-item active text-capitalize"><b>FPO Order Payment</b></li>
        					</ol>
        				</nav>
        				<hr style="margin-Bottom:12px !important;">
        			</div>
                    <div class="col-md-6">
                        <h4><b>FPO Order Details</b></h4>
                        <div class="table-purchase_request tableFixHead2">
                            <table class="tree  table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                            <tbody id="for_uppercase">
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
                            </tbody>
                            </table>  
                        </div>
                    </div>
					
					<!--Farmer details-->
					<div class="clearfix"></div>
					<div class="row">
						<div class="col-md-12">
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
                                <label for="total_wt">Total Weight</label>
                                <input type="text" name="total_wt" id="total_wt" class="form-control" value="<?php echo number_format($OrderDetails->TotalWeight, 2);?>" readonly>
                            </div>
    					</div>
    					
    				    <?php foreach ($qcAverages as $param => $avgValue): ?>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="avg_<?php echo strtolower(str_replace(' ', '_', $param)); ?>">Avg <?php echo htmlspecialchars($param); ?></label>
                                    <input type="text" 
                                           id="avg_<?php echo strtolower(str_replace(' ', '_', $param)); ?>" 
                                           class="form-control" 
                                           value="<?php echo $avgValue; ?>" 
                                           readonly>
                                </div>
                            </div>
                        <?php endforeach; ?>
					</div>
					
                    <hr style="margin-Bottom:12px !important;">
					
					 <?php
                        $fy = $this->session->userdata('finacial_year');
                        $fy_new  = $fy + 1;
                        $lastdate_date = '20'.$fy_new.'-03-31';
                        $curr_date = date('Y-m-d');
                        $curr_date_new    = new DateTime($curr_date);
                        $last_date_yr = new DateTime($lastdate_date);
                        if($last_date_yr < $curr_date_new){
                            $date = $lastdate_date;
                        }else{
                            $date = date('Y-m-d');
                        }
                    ?>
					<!-- Add Payment -->
                    <form id="AddPayment" method="POST" action="<?php echo admin_url(); ?>FpoOrder/AddFpoPayment"> 
                        <div class="col-md-3">
                            <input type="text" name="FpoOrderID" id="FpoOrderID" value="<?php echo $OrderDetails->OrderID; ?>" hidden>
                            <input type="text" name="Itemid" id="Itemid" value="<?php echo $OrderDetails->ItemID; ?>" hidden>
                            <input type="text" name="ratefpo" id="ratefpo" value="<?php echo $OrderDetails->FpoRate; ?>" hidden>
                            <input type="text" name="partyid" id="partyid" value="<?php echo $OrderDetails->PartyID; ?>" hidden>
                            <input type="text" name="TraderID" id="TraderID" value="<?php echo $OrderDetails->FPOID; ?>" hidden>
                            <?php
                                if (!empty($OrderDetails->details)) {
                                    foreach ($OrderDetails->details as $detail) {
                                        if (!empty($detail['AccountID']) && isset($detail['NetAmt'])) {
                                            $accountId = htmlspecialchars($detail['AccountID']);
                                            $netAmt = htmlspecialchars($detail['NetAmt']);
                                            $FarmerRate = htmlspecialchars($detail['Rate']);
                                            $OrderWt = htmlspecialchars($detail['NetWeight']);
                                           
                                            echo '<input type="hidden" name="payments[' . $accountId . '][netAmt]" value="' . $netAmt . '">';
                                            echo '<input type="hidden" name="payments[' . $accountId . '][Rate]" value="' . $FarmerRate . '">';
                                            echo '<input type="hidden" name="payments[' . $accountId . '][NetWeight]" value="' . $OrderWt . '">';
                                        }
                                    }
                                }
                            ?>
                            <div class="form-group" app-field-wrapper="FeildOfficer">
                                <small class="req text-danger">* </small>
                                <label for="paymode" class="control-label">Select Payment Mode</label>
                                <select name="paymode" id="paymode" class="selectpicker form-control" data-live-search="true" data-none-selected-text="Non Selected">
                                    <option value="" ></option>
                                    <?php
                                        foreach($genral_account_to_select as $key=>$val){
                                            ?>
                                                <option value="<?php echo $val["AccountID"]?>" <?php if($val["AccountID"] == $OrderDetails->PayMode){ echo 'selected';}?>><?php echo $val["company"];?></option>
                                            <?php
                                        }
                                    ?>
    							</select>
    						</div>
    					</div>
                
    					<div class="col-md-2">
    					<?php $value = (isset($payment_entry) ? _d(substr($OrderDetails->PaymentDate,0,10)) : _d($date)); ?>
                        <?php echo render_date_input('payment_date','Payment Date',$value,$data_attr); ?>
                        </div>
    					
    					<div class="col-md-2">
    					     <div class="form-group">
                                <label for="total_amt">Total Amt</label>
                                <input type="text" name="total_amt" id="total_amt" class="form-control" value="<?php echo number_format($OrderDetails->TotalAmt, 2);?>" readonly>
                            </div>
    					</div>
    					<?php if (has_permission_new('FpoOrder_Payment', '', 'create')){
                         ?>
    					<div class="col-md-2" style="margin-top: 20px;">
    					    <button type="submit" class="btn btn-info btn-sm" id="addpayment" <?php if ($OrderDetails->PaymentStatus == 2) echo 'disabled'; ?>>Pay</button>
    					</div>
    					<?php
                        }?>
					</form>
						
                </div><!-- First row end-->
            </div><!-- End Panel Body-->
            </div><!-- End Panel-->
        </div><!-- End Col-md-8-->
        
    </div><!-- End Main Row-->
</div><!-- End Wrapper div-->

<!-- Payment Confirmation Modal -->
<div class="modal fade" id="confirmPaymentModal" tabindex="-1" role="dialog" aria-labelledby="confirmPaymentLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="confirmPaymentLabel">Are you sure you want to proceed with the payment?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><strong>Payment Mode:</strong> <span id="selectedPayModeText"></span></p>
          <hr>
          <p><strong>Payment Summary:</strong></p>
          <div id="modalFarmerSummary">
            <!-- Inject table here via JS -->
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary btn-sm" id="confirmPayBtn">Generate Payment</button>
      </div>
    </div>
  </div>
</div>


<?php init_tail(); ?>
<?php $orderID = $this->uri->segment(4); ?>
<script>
	$(document).ready(function () {
        var OrderID = <?php echo json_encode($orderID); ?>;
        $.ajax({
            url: "<?php echo admin_url(); ?>FpoOrder/GetFpoOrderDetailsPayment",
            method: "POST",
            dataType: "JSON",
            data: { OrderID: OrderID },
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
                
                farmerPayments = [];
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
                headerHtml += '<th style="width:5%">Sr.No</th>';
                headerHtml += '<th style="width:10%">Farmer Name</th>';
                headerHtml += '<th style="width:10%">Farmer Rate</th>';
                headerHtml += '<th style="width:10%">Order Wt (Qtl)</th>';
                headerHtml += '<th style="width:7%">Order Bag</th>';
                headerHtml += '<th style="width:10%">Dispatch Wt (Qtl)</th>';
                
                uniqueParameters.forEach(param => {
                    headerHtml += '<th>' + param + '</th>';
                    headerHtml += '<th>' + param + ' Amt</th>';
                });
    
                headerHtml += '<th style="width:10%">Total Deduction</th>';
                headerHtml += '<th style="width:10%">Net Rate</th>';
                headerHtml += '<th style="width:10%">Amount</th>';
                headerHtml += '</tr>';
    
                $('.table_purchase_report thead').html(headerHtml);
                let TotalWeight = 0;
                let TotalBag = 0;
                let TotalDispatch = 0;
                let TotalDeduction = 0;
                let TotalNetAmt = 0;
                data.forEach(function (row) {
                    html += '<tr>';
                    html += '<td style="text-align:center;">' + i + '</td>';
                    html += '<td>' + row.farmer_name + '</td>';
                    html += '<td style="text-align:center;">' + row.farmer_rate + '</td>';
                    html += '<td style="text-align:center;">' + row.weight + '</td>';
                    TotalWeight += parseFloat(row.weight);
                    html += '<td style="text-align:center;">' + row.Bag + '</td>';
                    TotalBag += parseFloat(row.Bag);
                    var dispatchWeight = parseFloat(row.DispatchWt) || 0;
                    TotalDispatch += parseFloat(dispatchWeight);
                    html += '<td style="text-align:center;">' + dispatchWeight.toFixed(2) + '</td>';
                    uniqueParameters.forEach(param => {
                        let matchedQc = (row.qcdetails || []).find(qc => qc.ItemParameterName === param);
                        let value = matchedQc ? parseFloat(matchedQc.Qc_Value).toFixed(2) : '0.00';
                        let amount = matchedQc ? parseFloat(matchedQc.Qc_Amt).toFixed(2) : '0.00';
    
                        html += '<td style="text-align:center;">' + value + '</td>';
                        html += '<td style="text-align:right;">' + amount + '</td>';
                    });
    
                    html += '<td style="text-align:right;">' + row.Deduction + '</td>';
                    TotalDeduction += parseFloat(row.Deduction);
                    html += '<td style="text-align:center;">' + row.NetRate + '</td>';
                    html += '<td style="text-align:right;">' + row.NetAmt + '</td>';
                    TotalNetAmt += parseFloat(row.NetAmt);
                    html += '</tr>';
                    farmerPayments.push({
                        name: row.farmer_name,
                        amount: parseFloat(row.NetAmt)
                    });
        
                    i++;
                });
                html += '<tr>';
                html += '<td colspan="3" style="text-align:right;"><b>Total</b></td>';
                html += '<td style="text-align:center;">' + TotalWeight.toFixed(2) + '</td>';
                html += '<td style="text-align:center;">' + TotalBag + '</td>';
                html += '<td style="text-align:center;">' + TotalDispatch.toFixed(2) + '</td>';
                uniqueParameters.forEach(param => {
                    html += '<td colspan="2"><b></b></td>';
                });
                html += '<td style="text-align:right;">' + TotalDeduction.toFixed(2) + '</td>';
                html += '<td></td>';
                html += '<td style="text-align:right;">' + TotalNetAmt.toFixed(2) + '</td>';
                html += '</tr>';
                $('.table_purchase_report tbody').html(html);
            }
        });
        
        $('#AddPayment').on('submit', function(e) {
            e.preventDefault(); 
            var selectedText = $('#paymode option:selected').text();
            var selectedValue = $('#paymode').val();
            if (selectedValue === '') {
                alert('Please select a payment mode before proceeding.');
                return; 
            }
            
            $('#selectedPayModeText').text(selectedText);
            
            $('#modalFarmerSummary').empty(); 
            if (typeof farmerPayments !== 'undefined' && farmerPayments.length > 0) {
                let table = '<table class="table table-bordered table-sm mb-0">';
                table += '<thead><tr><th>Farmer Name</th><th>Net Amount (₹)</th></tr></thead><tbody>';
        
                let total = 0;
        
                farmerPayments.forEach(function(fp) {
                    table += `<tr><td>${fp.name}</td><td>${fp.amount.toFixed(2)}</td></tr>`;
                    total += fp.amount;
                });
                table += `<tr><td><strong>Total</strong></td><td><strong>${total.toFixed(2)}</strong></td></tr>`;
                table += '</tbody></table>';
        
                $('#modalFarmerSummary').html(table);
            } else {
                $('#modalFarmerSummary').html('<p>No farmer payment data available.</p>');
            }
    
            $('#confirmPaymentModal').modal('show'); 
        });

        $('#confirmPayBtn').click(function() 
        {
            var $btn = $(this);
            $btn.prop('disabled', true);
            $btn.text('Processing...');
            $('#AddPayment')[0].submit();
        });
    });

</script>

</body>
</html>
