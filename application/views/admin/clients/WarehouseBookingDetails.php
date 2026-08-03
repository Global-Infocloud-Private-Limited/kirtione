<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table_purchase_request          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.table_purchase_request thead th { position: sticky; top: 0; z-index: 1; }
.table_purchase_request tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
    
#table_purchase_request td:hover {
    cursor: pointer;
}
#table_purchase_request tr:hover {
    background-color: #ccc;
}

</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
		    <div class="col-md-6">
		        <div class="panel_s">
                    <div class="panel-body">
                    <div class="clearfix mtop20"></div>
                        <div class="row">
		                    <div class="col-md-12">
		                        <div class="table-purchase_request tableFixHead2">
                                    <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                                    <thead>
                                        <tr>
                                            <td colspan="2"> <b><h3>Warehouse Booking Details</h3></b></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><b>Booking ID : </b></td>
                                            <td><?php echo $OrderDetails->BookingID; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Booking Date : </b></td>
                                            <td><?php echo _d(substr($OrderDetails->TransDate,0,10)); ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Party Code : </b></td>
                                            <td><?php echo $OrderDetails->AccountID; ?></td>
                                        </tr>
                                        <tr>
                                            <?php
                                                if($OrderDetails->company == null){
                                                    $party_name = $OrderDetails->firstname.' '.$OrderDetails->lastname; 
                                                }
                                                else{
                                                    $party_name = $OrderDetails->company;
                                                }
                                            ?>
                                            <td><b>Party Name :</b></td>
                                            <td><?php echo $party_name; ?></td>
                                        </tr>
                                        <tr>
                                            <?php 
                                                $customer = $OrderDetails->CustomerType;
                                                if($customer == '1'){
                                                    $customer = 'Farmer';
                                                }
                                                if($customer == '2'){
                                                    $customer = 'Broker';
                                                }
                                                if($customer == '3'){
                                                    $customer = 'Trader';
                                                }
                                                if($customer == '4'){
                                                    $customer = 'Corporate/Processor';
                                                }
                                            ?>
                                            <td><b>Account Type :</b></td>
                                            <td><?php echo $customer; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Warehouse ID : </b></td>
                                            <td><?php echo $OrderDetails->WHID; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Warehouse Name : </b></td>
                                            <td><?php echo $OrderDetails->w_name; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>ItemID : </b></td>
                                            <td><?php echo $OrderDetails->ItemID; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Item Name : </b></td>
                                            <td><?php echo $OrderDetails->ItemName; ?></td>
                                        </tr>
                                    </tbody>
                                    </table>   
                                </div>
		                    </div>
		                    
		                    <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="zip">
                                    <input id="ID" value="<?php echo $OrderDetails->id; ?>" hidden>
                                    <input id="AccountID" value="<?php echo $OrderDetails->AccountID; ?>" hidden>
                                    <input id="quantity" value="<?php echo $OrderDetails->quantity; ?>" hidden>
                                    <label for="qty" class="control-label">Quantity</label>
                                    <input type="text" name="qty" id="qty" class="form-control" value="" onkeypress="return isNumber(event)">
                                </div>
                            </div>
                            
                            <div class="col-md-3" >
                                
                                <div class="form-group" app-field-wrapper="unit">
                                    <input id="unit_val" value="<?php echo $OrderDetails->unit; ?>" hidden>
                                    <label for="unit" class="form-label">Unit</label>
                                    <select name="unit" id="unit" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Non Selected</option>
                                        <option value="Bags">Bags</option>
                                        <option value="Quintal">Quintal</option> 
                                        <option value="Ton">Ton</option> 
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" >
                                <div class="form-group" app-field-wrapper="status_list">
                                    <input id="IsApprove" value="<?php echo $OrderDetails->IsApprove; ?>" hidden>
                                    <label for="status_list" class="form-label">Status</label>
                                    <select name="status_list" id="status_list" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="NA">Non Selected</option>
                                        <option value="Y">Accepted</option>
                                        <option value="N">Rejected</option> 

                                    </select>
                                </div>
                            </div>
		                </div>
                        <br>
                        <div class="row" style="width:100%;margin:auto;">
                            <button id="updateBtn" class="btn btn-info">Update</button> 
                        </div>
                    </div>
                </div>
		    </div>
		 
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script>
$(document).ready(function(){
    var unit = $('#unit_val').val();
    var quantity = $('#quantity').val();
    var IsApprove = $('#IsApprove').val();
    $('#unit').val(unit).selectpicker('refresh');
    $('#qty').val(quantity);
    $('#status_list').val(IsApprove).selectpicker('refresh');
});
</script>
<script>
    function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode = 46 && charCode > 31 
            && (charCode < 48 || charCode > 57)){
        return false;
    }
    return true;
    }
</script>
<script>
    $('#updateBtn').click(function(){
        var ID = $('#ID').val();
        var quantity = $('#qty').val();
        var unit = $('#unit').val();
        var status_list = $('#status_list').val();
        
        $.ajax({
           url:"<?php echo admin_url(); ?>Warehouse/UpdateWarehouseBooking",
            dataType:"json",
            method:"POST",
            data:{
                ID:ID, 
                quantity:quantity, 
                unit:unit,
                status_list:status_list
            },
            success: function (data) {
                if(data == true){
                    alert("Details Updated Successfully!");
                    window.location.replace("<?php echo admin_url(); ?>Warehouse/bookWarehouse");
                }
            }, 
        });
    });
</script>
</body>
</html>
