<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
 <style>
   .table-purchase_request          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.table-purchase_request thead th { position: sticky; top: 0; z-index: 1; }
.table-purchase_request tbody th { position: sticky; left: 0; }

	table { 
		border-collapse: collapse; 
		width: 60%; 		
		table-layout: fixed;
	}
	th, td { 
		padding: 1px 5px !important; 
		white-space: nowrap; 
		border:1px solid !important;
		font-size:11px; 
		line-height:1.42857143!important;
		vertical-align: middle !important;
		text-align: left;
	}
   th { 
		background: #50607b;
		color: #fff !important; 
   }
   td:hover {
	  cursor: pointer;
   }
   tr:hover 
   {
	  background-color: #ccc;
   } 
	h4 {
    margin-bottom: -8px; 
  }   
 </style>
 <?php 
 if($SaleDetails->OrderStatus == "F") 
{
	$orderstat = "Completed";
} elseif ($SaleDetails->OrderStatus == "C") {
	$orderstat = "Cancelled";
}	
 ?>
	<div class="panel_s">
	 <div class="panel-body">
		<div class="row">
            <div class="col-md-12">
                <h4>Purchase Order Details</h4>
				<div class="clearfix mtop20"></div>
		        <div>					
					<table>
						<thead>
							<tr>
								<td width="20%"><strong>PO No</strong></td>
								<td><?php echo $SaleDetails->PurchID;?></td>
							</tr>
							<tr>
								<td width="20%"><strong>PO Date</strong></td>
								<td><?php echo _d(substr($SaleDetails->Transdate,0,10));?></td>
							</tr>
							<tr>
								<td width="20%"><strong>Center Name</strong></td>
								<td><?php echo $SaleDetails->CenterName;?></td>
							</tr>							
							<tr>
								<td width="20%"><strong>AccountID</strong></td>
								<td><?php echo $SaleDetails->AccountID;?></td>
							</tr>
							<tr>
								<td width="20%"><strong>Party Name</strong></td>
								<td><?php echo $SaleDetails->company;?></td>
							</tr>
							<tr>
								<td width="20%"><strong>Order Status</strong></td>
								<td><?php echo $orderstat;?></td>
							</tr>
							<tr>
								<td width="15%"><strong>Total Quantity</strong></td>
								<td><?php echo $SaleDetails->TotalOrderQty;?></td>								
							</tr>							
						</thead>						
					</table>	
								
					<div class="clearfix mtop10"></div>
					<h4>Item Details</h4> 
					<div class="table-purchase_request tableFixHead2" id="first_table_container">
					  <table class="table-purchase_request tree table table-bordered OrderList" id="OrderList" width="100%">
						<table>
							<thead>
								<tr>
									<th width="15%"><strong>Sr No.</strong></th>
									<th width="25%"><strong>Item Name</strong></th>
									<th width="15%"><strong>Quantity</strong></th>
									<th width="15%"><strong>Item Amt</strong></th>
									<th width="15%"><strong>Disc Amt</strong></th>
									<th width="15%"><strong>GST Amt</strong></th>	
									<th width="15%"><strong>Net Amt</strong></th>	
									<th width="15%"><strong>Order Status</strong></th>										
								</tr>														
							</thead>
							<tbody>
								<?php $sr_no = 1;
								foreach($SaleHistoryDetails as $val):
								$gstamt = $val['cgstamt'] + $val['sgstamt'] + $val['igstamt'];
								if($val['OrderStatus'] == "F") 
								{
									$OrderStat = "Completed";
								} elseif ($val['OrderStatus'] == "C") {
									$OrderStat = "Cancelled";
								}	
								$totalQtySum += $val['OrderQty'];
								$totalValueAmtSum += $val['OrderAmt'];
								$totalDiscountAmtSum += $val['DiscAmt'];
								$totalTaxAmtSum += $gstamt;
								$totalNetAmtSum += $val['NetOrderAmt'];
								?> 
									<tr>
										<td><?php echo $sr_no++; ?></td>
										<td><?php echo $val['ProductName']; ?></td>
										<td style="text-align:right;"><?php echo number_format($val['OrderQty'], 2, '.', '');?></td>
										<td style="text-align:right;"><?php echo number_format($val['OrderAmt'], 2, '.', '');?></td>
										<td style="text-align:right;"><?php echo number_format($val['DiscAmt'], 2, '.', '');?></td>
										<td style="text-align:right;"><?php echo number_format($val['gstamt'], 2, '.', '');?></td>
										<td style="text-align:right;"><?php echo number_format($val['NetOrderAmt'], 2, '.', ''); ?></td>
										<td><?php echo $OrderStat; ?></td>
									</tr>				
									
								<?php endforeach; ?>
								
								<tr>
										<td colspan="2" style="text-align:right;"><strong>Total</strong></td>;        
										<td style="text-align:right;"><strong><?php echo number_format($totalQtySum, 2, '.', '');?></td>
										<td style="text-align:right;"><strong><?php echo number_format($totalValueAmtSum, 2, '.', '');?></td>
										<td style="text-align:right;"><strong><?php echo number_format($totalDiscountAmtSum, 2, '.', '');?></td>
										<td style="text-align:right;"><strong><?php echo number_format($totalTaxAmtSum, 2, '.', '');?></td>
										<td style="text-align:right;"><strong><?php echo number_format($totalNetAmtSum, 2, '.', '');?></td>
										<td></td> 
									</tr>
							</tbody>
						</table>	
					  </table>   
					</div>	
					<span id="searchh2" style="display:none;">Loading.....</span>
     					
                </div>
		    </div>
        </div>         
     </div> 	
</div>

<script>
    function fill_data(id){
        window.open("<?php echo admin_url(); ?>Clients/CropSell_Report_Details/"+Gate_in_ID,'_blank');
    }
</script>

<script>
    $('#AsnBtn').click(function(){
        var BookingID = $('#BookingID').val();
        if($('input').val() != ''){
            $('#asn_form').submit();
            $('#AsnBtn').hide();
            $('#ViewAsnBtn').show();
        }
        else{
            alert("Enter Required Data !");   
        }
    });

	$('.GetDetails').on('click',function(){ 
        Gate_in_ID = $(this).attr("data-id");
        window.open("<?php echo base_url(); ?>Clients/CropSell_Report_Details/"+Gate_in_ID,'_blank');
    });
</script>