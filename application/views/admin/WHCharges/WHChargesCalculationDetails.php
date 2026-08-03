<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
	#AccountID {
    text-transform: uppercase;
	}
	#table-BillingList td:hover {
    cursor: pointer;
	}
	#table-BillingList tr:hover {
    background-color: #ccc;
	}
	
	#table-inward_list td:hover {
    cursor: pointer;
	}
	#table-inward_list tr:hover {
    background-color: #ccc;
	}
	
	#table-withdrawal_list td:hover {
    cursor: pointer;
	}
	#table-withdrawal_list tr:hover {
    background-color: #ccc;
	}
	
	.table-BillingList          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-BillingList thead th { position: sticky; top: 0; z-index: 1; }
    .table-BillingList tbody th { position: sticky; left: 0; }
    
    .table-inward_list          { overflow: auto;max-height: 50vh;width:100%;position:relative;top: 0px; }
    .table-inward_list thead th { position: sticky; top: 0; z-index: 1; }
    .table-inward_list tbody th { position: sticky; left: 0; }
    
    .table-withdrawal_list          { overflow: auto;max-height: 50vh;width:100%;position:relative;top: 0px; }
    .table-withdrawal_list thead th { position: sticky; top: 0; z-index: 1; }
    .table-withdrawal_list tbody th { position: sticky; left: 0; }


    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
    h4{
        color:50607b;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="panel_s">
            <div class="panel-body">
                <div class="row">
                    <nav aria-label="breadcrumb" >
						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
							<li class="breadcrumb-item active text-capitalize"><b>Warehouse</b></li>
							<li class="breadcrumb-item active" aria-current="page"><b>Storage Charges Calculation</b></li>
						</ol>
					</nav>
					<hr class="hr_style">
				</div>
				<div class="row">
                    <div class="col-md-6">
    		            <div class="table-trade_list tableFixHeadTrade">
                        <table class="tree table table-striped table-bordered table-purchase_request tableFixHeadTrade" id="table-purchase_request" style="width:100%;">
                            <thead>
                                <?php
                                    if($OrderDetails->company == null){
                                        $party_name = $OrderDetails->firstname.' '.$OrderDetails->lastname; 
                                    }
                                    else{
                                        $party_name = $OrderDetails->company;
                                    }
                                    if($OrderDetails->IsApprove == 'Y'){
                                        $status = 'ACCEPTED';
                                    }
                                    if($OrderDetails->IsApprove == 'Y' && $OrderDetails->ClientApprove == 'N'){
                                        $status = "Waiting for party approval";
                                    }
                                    if($OrderDetails->IsApprove == 'N'){
                                        $status = 'REJECTED';
                                    }
                                    if($OrderDetails->IsApprove == 'NA'){
                                        $status = 'NO ACTION';
                                    }
                                    if($OrderDetails->status == '2'){
                                        $status = "COMPLATED";
                                    }
                                    if($OrderDetails->status == '3'){
                                        $status = "PARTIAL COMPLATED";
                                    }
                                ?>
                                <tr>
                                    <th colspan="3"><h4><b>Trade Details</b></h4></th>
                                </tr>
                                <tr>
                                    <td><b>Booking ID : </b><?php echo $OrderDetails->BookingID; ?></td>
                                    <td><b>TransDate : </b><?php echo _d($OrderDetails->TransDate); ?></td>
                                    <td><b>Status : </b><?php echo $status;?></td>
                                </tr>
                                <tr>
                                    <td><b>AccountID : </b><?php echo $OrderDetails->AccountID; ?></td>
                                    <td colspan="2"><b>Party Name : </b><?php echo $party_name; ?></td>
                                </tr>
                                <tr>
                                    <td><b>Item Name : </b><?php echo $OrderDetails->ItemName; ?></td>
                                    <td colspan="2"><b>Quantity : </b><?php echo $OrderDetails->quantity.' '.$OrderDetails->unit; ?></td>
                                </tr>
                                <tr>
                                    <td><b>Payment Cycle : </b><?php echo $OrderDetails->CycleName; ?></td>
                                    <td><b>Locking Period : </b><?php echo $OrderDetails->LockName; ?></td>
                                    <?php
                                        $lockEndDate = date('Y-m-d', strtotime('+'.$OrderDetails->LockDays.' day', strtotime(substr($OrderDetails->TransDate,0,10))));
                                    ?>
                                    <td><b>Lock in End date : </b><?php echo _d($lockEndDate); ?></td>
                                </tr>
                                <tr>
                                    <td><b>Min Quantity(MT) : </b><?php echo $OrderDetails->MinQty; ?></td>
                                    <td><b>Charge Rate(MT/Month) : </b><?php echo $OrderDetails->basic_rate; ?></td>
                                    <?php
                                        if($OrderDetails->RateType == "1"){
                                           $ChargeType = "Including GST";
                                        }else{
                                            $ChargeType = "Excluding GST";
                                        }
                                    ?>
                                    <td><b>Rate Type : </b><?php echo $ChargeType; ?></td>
                                </tr>
                                <tr>
                                    <?php
                                        if($OrderDetails->IsFumigation == "1"){
                                           $IsFumigation = "Yes";
                                        }else{
                                            $IsFumigation = "No";
                                        }
                                        
                                        if($OrderDetails->RateIncFumigation == "1"){
                                           $RateIncFumigation = "Yes";
                                        }else{
                                            $RateIncFumigation = "No";
                                        }
                                    ?>
                                    <td><b>Is Fumigation</b><?php echo $IsFumigation; ?></td>
                                    <td><b>Rate Inc.Fumigation : </b><?php echo $RateIncFumigation; ?></td>
                                    <td><b>Fumigation Charges Amt(MT/Month) : </b><?php echo $OrderDetails->FumigationAmt; ?></td>
                                </tr>
                                <tr>
                                    <td><b>Deposit Period(days) : </b><?php echo $OrderDetails->DepositPeriod." Days"; ?></td>
                                    <td colspan="2"><b>Credit Days : </b><?php echo $OrderDetails->CreditDays; ?></td>
                                </tr>
                            </thead>
                        </table>   
                        </div>
    		        </div>
                </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="table-BillingList tableFixHeadBillingList">
                        <table class="tree table table-striped table-bordered table-BillingList tableFixHeadBillingList" id="table-BillingList" width="100%">
                            <thead>
                                <tr>
                                    <th>Billing Month</th>
                                    <th>Inward Date</th>
                                    <th>No Of Bags</th>
                                    <th>Inward Qty(MT)</th>
                                    <th>Outward Qty (MT)</th>
                                    <th>Closing Balance</th>
                                    <th>Billing Amt</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $start_date = substr($OrderDetails->TransDate,0,10);
                    			$end_date = date('Y-m-d');
                    			$begin = new DateTime($start_date);
                    			$end = new DateTime($end_date);
                    			$end = $end->modify('+1 day'); // Include the end date
                    			$interval = new DateInterval('P1D'); // 1 day interval
                    			$date_range = new DatePeriod($begin, $interval, $end);
                    			$CycleDays = $OrderDetails->CycleDays;
                    			$CycleCount = 1;
                    			$CycleNo = 1;
                    			$ClosingBal = 0;
                    			$ChargeRate = $OrderDetails->basic_rate;
                    			$TradeWeight = $OrderDetails->quantity; // MT
                    			$DepositPeriod = $OrderDetails->DepositPeriod; // days
                    			$DayCount = 1;
                    			
                        		foreach ($date_range as $date) { 
                        		    $dateStr = $date->format('d/m/Y');
                        		    $timestamp = DateTime::createFromFormat('d/m/Y', $dateStr);
                                    // Get number of days in the month
                                    $daysInMonth = $timestamp->format('t');
                                    $PerDaysChargeAmt = $ChargeRate/$daysInMonth;
                        		    $dateSqlFormate = $date->format('Y-m-d');
                        		    $parsedDate = DateTime::createFromFormat('d/m/Y', $dateStr);
                        		    $MonthName = $parsedDate->format('M-Y');
                        		    $InwardBag = 0;
                        		    $InwardQty = 0;
                        		    $OutwardBag = 0;
                        		    $OutwardQty = 0;
                        		    foreach($StockInventory as $key=>$value){ 
                        		        if($dateSqlFormate == substr($value["TransDate"],0,10) && $value["TType"]=="D"){
                        		            $InwardQty += $value["Weight"];
                        		            $InwardBag += $value["BagQty"];
                        		        }
                        		        if($dateSqlFormate == substr($value["TransDate"],0,10) && $value["TType"]=="W"){
                        		            $OutwardQty += $value["Weight"];
                        		            $OutwardBag += $value["BagQty"];
                        		        }
                        		    }
                        		    if($CycleNo == 1){
                        		            // Check this cycle is beyond current month
                        		            $startDateMonth = substr($dateSqlFormate,5,2);
                            		       $CycleDays2 = $CycleDays *2;
                            		       $CycleEndDate = date('Y-m-d', strtotime('+'.$CycleDays2.' day', strtotime($dateSqlFormate)));
                            		       
                            		       $CycleEndDateFormated = _d($CycleEndDate);
                        			       $parsedCycleEndDateFormated = DateTime::createFromFormat('d/m/Y', $CycleEndDateFormated);
                        			       $EndCycleDayMonth = $parsedCycleEndDateFormated->format('M-Y');
                        			       if($EndCycleDayMonth != $MonthName){
                        			           $startDate = new DateTime($dateSqlFormate);
                                               $endDate   = new DateTime($CycleEndDate);
                                		       $period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate);
                                		       $Count = 0;
                                		       foreach ($period as $dates) {
                                		           if ($dates->format('m') == $startDateMonth) {
                                		               $Count++;
                                		           }
                                		       }
                                		       $NewCycleDays = $Count;
                        			       }else{
                        			           $NewCycleDays = $CycleDays;
                        			       }
                        		        ?>
                        		            <tr>
                        		                <td colspan="7" style="font-weight:700;font-size:13px;"><b>Billing Cycle <?php echo $CycleCount;?></b></td>
                        		            </tr>
                        		       <?php
                        		    }
                        		    $ClosingBal += $InwardQty;
                        		    $ClosingBal -= $OutwardQty;
                            ?>
                            
                                <tr>
                                    <td style="text-align:center;"><?php echo $MonthName;?></td>
                                    <td style="text-align:center;"><?php echo $dateStr;?></td>
                                    <td style="text-align:center;"><?php echo $InwardBag;?></td>
                                    <td style="text-align:center;"><?php echo number_format($InwardQty, 3, '.', '');?></td>
                                    <td style="text-align:center;"><?php echo number_format($OutwardQty, 3, '.', '');?></td>
                                    <td style="text-align:center;"><?php echo number_format($ClosingBal, 3, '.', '');?></td>
                                    <?php
                                        $CalculateWt = 0;
                                        if($DepositPeriod >= $DayCount && $ClosingBal == 0){
                                            $CalculateWt = 0;
                                        }else if($ClosingBal > $TradeWeight){
                                            $CalculateWt = $ClosingBal;
                                        }else{
                                            if($OrderDetails->MinQty > 0 && $ClosingBal < $OrderDetails->MinQty){
                                                $CalculateWt = $OrderDetails->MinQty;
                                            }else{
                                                $CalculateWt = $TradeWeight;
                                            }
                                        }
                                        $ChargeAmt = $PerDaysChargeAmt * $CalculateWt;
                                    ?>
                                    <td style="text-align:center;"><?php echo number_format($ChargeAmt, 3, '.', '');?></td>
                                </tr>
                                <?php 
                                    $DayCount++;
                                    $CycleNo++;
                                    if($NewCycleDays == $CycleNo){
                                        $CycleNo = 0;
                                        $CycleCount++;
                                    }
                                    $PreDayMonth = $MonthName;
                                } ?>
                            </tbody>
                        </table>
    		        </div>
                </div>
                <div class="col-md-6">
                    <div class="table-inward_list tableFixHeadInwardList">
                        <table class="tree table table-striped table-bordered table-inward_list tableFixHeadInwardList" id="table-inward_list" width="100%">
                            <thead>
                                <tr>
                                    <th colspan="7"><h4><b>Inward Details</b></h4></th>
                                </tr>
                                <tr>
                                    <th>Sr.No.</th>
                                    <th>ASNID</th>
                                    <th>Gate Pass No</th>
                                    <th>TransDate</th>
                                    <th>Bag Qty</th>
                                    <th>Net Weight(MT)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <?php $sr = 1; 
                                $TotalInwardWeight = 0;
                                $TotalInwardbag = 0;
                            ?>
                            <tbody>
                                <?php foreach($OrderList as $key=>$value){ ?>
                                    <tr class="GetDetails" data-id="<?php echo $value['id']; ?>">
                                        <?php 
                                        
                                            $InwardBag = 0;
                                		    $InwardQty = 0;
                                		    foreach($StockInventory as $key=>$val)
                                		    { 
                                		        if($value['Gate_in_ID'] == $val["GateINID"] && $val["TType"] =="D"){
                                		            $InwardQty += $val["Weight"];
                                		            $InwardBag += $val["BagQty"];
                                		        }
                                		    }
                                		    $TotalInwardWeight += $InwardQty;
                                		    $TotalInwardbag += $InwardBag;
                                            if($value['TType'] == 'D'){
                                                if($value['status'] == 0){
                                                    $status_val = "NO ACTION";
                                                }elseif($value['status'] == 1){
                                                    $status_val = "ASN GENERATED";
                                                }elseif($value['status'] == 2){
                                                    $status_val = "GATE IN GENERATED";
                                                }elseif($value['status'] == 3){
                                                    $status_val = "PERIPHERAL DONE";
                                                }elseif($value['status'] == 4){
                                                    $status_val = "GROSS WEIGHT CAPTURED ";
                                                }elseif($value['status'] == 5){
                                                    $status_val = "UNLOADING IN PROGRESS ";
                                                }elseif($value['status'] == 6){
                                                    $status_val = "UNLOADING FINISHED ";
                                                }elseif($value['status'] == 7){
                                                    $status_val = "QC DONE ";
                                                }elseif($value['status'] == 9){
                                                    $status_val = "TARE WEIGHT CAPTURED ";
                                                }elseif($value['status'] == 10){
                                                    $status_val = "FINAL QC DONE ";
                                                }elseif($value['status'] == 11){
                                                    $status_val = "READY TO EXIT ";
                                                }elseif($value['status'] == 12){
                                                    $status_val = "EXIT";
                                                }elseif($value['status'] == 13){
                                                    $status_val = "PAYMENT APPROVED";
                                                }
                                            }
                                        ?>
                                        <td><?php echo $sr; ?></td>
                                        <td><?php echo $value['ASNID']; ?></td>
                                        <td><?php echo $value['Gate_in_ID']; ?></td>
                                        <td><?php echo _d($value['asn_date']); ?></td>
                                        <td style="text-align:right"><?php echo $InwardBag; ?></td>
                                        <td style="text-align:right"><?php echo number_format($InwardQty, 2, '.', ''); ?></td>
                                        <td><?php echo $status_val; ?></td>
                                    </tr>
                                <?php $sr++; } ?>
                                    <tr>
                                        <td colspan="4" style="text-align:right;font-size:14px;"><b>Total</b></td>
                                        <td style="text-align:right;font-size:14px;"><b><?php echo number_format($TotalInwardbag, 2, '.', ''); ?></b></td>
                                        <td style="text-align:right;font-size:14px;"><b><?php echo number_format($TotalInwardWeight, 2, '.', ''); ?></b></td>
                                        <td></td>
                                    </tr>
                            </tbody>
                        </table>   
                    </div>
                    
                    <div class="table-withdrawal_list tableFixHeadwithdrawal_list">
                        <table class="tree table table-striped table-bordered table-withdrawal_list tableFixHeadwithdrawal_list" id="table-withdrawal_list" width="100%">
                            <thead>
                                <tr>
                                    <th colspan="8"><h4><b>Withdrawal List</b></h4></th>
                                </tr>
                                <tr>
                                    <th>Sr.No.</th>
                                    <th>W TradeID</th>
                                    <th>W GateIN ID</th>
                                    <th>W Date</th>
                                    <th>D GateINID</th>
                                    <th>W Bag Qty</th>
                                    <th>W Net Weight(MT)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <?php 
                                $sr = 1; 
                                $TotalOutwardWeight = 0;
                            ?>
                            <tbody>
                                <?php foreach($OutwardList as $key=>$value){ ?>
                                    <tr class="GetDetails" data-id="<?php echo $value['id']; ?>">
                                        <?php 
                                            $TotalOutwardWeight += $value['OutwardWeight'];
                                                if($value['WStatus'] == 0){
                                                    $status_val = "NO ACTION";
                                                }elseif($value['WStatus'] == 1){
                                                    $status_val = "ASN GENERATED";
                                                }elseif($value['WStatus'] == 2){
                                                    $status_val = "GATE IN GENERATED";
                                                }elseif($value['WStatus'] == 3){
                                                    $status_val = "TARE WEIGHT CAPTURED ";
                                                }elseif($value['WStatus'] == 4){
                                                    $status_val = "LOADING IN PROGRESS ";
                                                }elseif($value['WStatus'] == 5){
                                                    $status_val = "GROSS WEIGHT CAPTURED";
                                                }elseif($value['WStatus'] == 6){
                                                    $status_val = "READY TO EXIT";
                                                }elseif($value['WStatus'] == 7){
                                                    $status_val = "EXIT";
                                                }
                                        ?>
                                        <td><?php echo $sr; ?></td>
                                        <td><?php echo $value['WTradeID']; ?></td>
                                        <td><?php echo $value['WGateINID']; ?></td>
                                        <td><?php echo _d($value['WGatINDate']); ?></td>
                                        <td><?php echo $value['DGateINID']; ?></td>
                                        <td style="text-align:right"><?php echo number_format($value['OutwardQty'], 2, '.', ''); ?></td>
                                        <td style="text-align:right"><?php echo number_format($value['OutwardWeight'], 2, '.', ''); ?></td>
                                        <td><?php echo $status_val; ?></td>
                                    </tr>
                                <?php $sr++; } ?>
                                <tr>
                                    <td colspan="6" style="text-align:right;font-size:14px;"><b>Total</b></td>
                                    <td style="text-align:right;font-size:14px;"><b><?php echo number_format($TotalOutwardWeight, 2, '.', ''); ?></b></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>   
                    </div>
                    
                </div>
            </div>
        </div>       
	</div>
</div>
<?php init_tail(); ?>
<script>
    $('.GetDetails').on('click',function(){ 
        id = $(this).attr("data-id");
        window.open("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+id,'_blank');
    });
    // Calculate amount
    $("#dis_per").keyup(function () {
        var wr_amount = $('#wr_amount').val();
        var val = $(this).val();
        if (val == "") {
            $('#dis_amount').val('0.00');
        } else {
            if (val > 70) {
                alert('please enter less than equal to 70%');
                $('#dis_amount').val('0.00');
                $(this).val('0');
            } else {
                var PayAmt = parseFloat(wr_amount) * (parseFloat(val) / 100);
                $('#dis_amount').val(parseFloat(PayAmt).toFixed(2));
            }
        }
    })
    // Calculate percentage
    $("#dis_amount").keyup(function () {
        var wr_amount = $('#wr_amount').val();
        //var dis_amount = $('#dis_amount').val();
        var wr_amt_limit = wr_amount-(wr_amount*0.30);
        var val = $(this).val();
        if (val == "") {
            $('#dis_per').val('0.00');
        } else {
            if (val > wr_amt_limit) {
                alert('please enter less than equal to 70% of WR value');
                $('#dis_per').val('0.00');
                $(this).val('0');
            } else {
                var dis_per = Math.round((val / wr_amount) * 100);
                $('#dis_per').val(parseFloat(dis_per).toFixed(2));
            }
        }
    })
    // add WR in loan amount
    $('#wr_list').change(function () {
        var wr_list = $('#wr_list').val();
        $.ajax({
            url: "<?php echo admin_url(); ?>GateControl/Ganerate_wr_details",
            dataType: "json",
            method: "POST",
            data: { wr_list: wr_list},
            beforeSend: function () {
                $('#sendrequest').html('Please wait request sending.');
            },
            success: function (data) {
                $('#wr_amount').val(data.total_amount);
                $('#wr_weight').val(data.total_weight);
                $('#dis_per').val('0');
                $('#dis_amount').val('0');
            }
        });
    });
    // Save loan details
    $('#saveloanBtn').click(function () {
        var AccountID = $('#AccountID').val();
        var BookingID = $('#BookingID').val();
        var TType = $('#TType').val();
        var wr_list = $('#wr_list').val();
        var wr_amount = $('#wr_amount').val();
        var wr_weight = $('#wr_weight').val();
        var dis_per = $('#dis_per').val();
        var dis_amount = $('#dis_amount').val();
        var ROI = $('#ROI').val();
        if(isNaN(parseFloat(wr_amount))){
            alert('WR amount not loaded please refresh page');
        }else if(isNaN(parseFloat(wr_weight))){
            alert('WR weight not loaded please refresh page');
        }else if(isNaN(parseFloat(dis_per))){
            alert('please enter loan amount percentage');
        }else if(isNaN(parseFloat(dis_amount))){
            alert('please enter loan amount');
        }else if(isNaN(parseFloat(ROI))){
            alert('please selec loan ROI');
        }else{
            if (confirm('Do you want to add Disbrusment details')) {
                   
                $.ajax({
                    url: "<?php echo admin_url(); ?>GateControl/loan_dis_submit",
                    //dataType: "json",
                    method: "POST",
                    data: { AccountID:AccountID,BookingID:BookingID,TType:TType,wr_list: wr_list,wr_amount:wr_amount,wr_weight:wr_weight,
                        dis_per:dis_per,dis_amount:dis_amount,ROI:ROI
                    },
                    beforeSend: function () {
                        $('#sendrequest').html('Please wait request sending.');
                    },
                    success: function (data) {
                        location.reload();
                    }
                });
            }
        }
        
        /**/
    });
</script>
<script type="text/javascript">
   $('#dis_per,#dis_amount').on('keypress',function (event) {
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
        event.preventDefault();
    }
    var input = $(this).val();
    if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 3 )) {
        event.preventDefault();
    }
});
</script>