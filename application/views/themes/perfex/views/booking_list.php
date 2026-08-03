<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .table-purchase_request          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.table-purchase_request thead th { position: sticky; top: 0; z-index: 1; }
.table-purchase_request tbody th { position: sticky; left: 0; }


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
<div class="panel_s">
  <div class="panel-body">
        <div class="row" style="margin:auto;width:100%;">
            <div class="col-md-12">
                <h4>Booking List</h4>
                <?php
                        $from_date = "01/".date('m')."/".date('Y');
                        $to_date = date('d/m/Y');
                ?> 
                <div class="col-md-2">
                    <input type="text" id="AccountID" value="<?php echo $this->session->userdata('AccountID'); ?>" hidden>
                    <?php echo render_date_input('from_date','From',$from_date); ?>
                </div>
                <div class="col-md-2">
                    <?php echo render_date_input('to_date','To',$to_date);  ?>
                </div>
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="account_type">
                        <label for="w_id" class="form-label">Warehouse Name</label>
                        <select name="w_id" id="w_id" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <?php 
                                foreach($warehouses as $key=>$val){
                            ?>        
                                    <option value="<?php echo $val['AccountID']; ?>" ><?php echo $val['w_name']; ?></option>
                            <?php        
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="CenterID">
                        <label for="CenterID" class="form-label">Center Name</label>
                        <select name="CenterID" id="CenterID" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <?php 
                                foreach($centers as $key=>$val){
                            ?>        
                                    <option value="<?php echo $val['CenterID']; ?>" ><?php echo $val['CenterName']; ?></option>
                            <?php        
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="account_type">
                        <label for="item" class="form-label">Commodity Name</label>
                        <select name="item" id="item" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <?php 
                                foreach($items as $key=>$val){
                            ?>        
                                    <option value="<?php echo $val['ItemID']; ?>" ><?php echo $val['ItemName']; ?></option>
                            <?php        
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="BookingType">
                        <label for="BookingType" class="form-label">Booking Type</label>
                        <select name="BookingType" id="BookingType" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <option value="P" >Purchase</option>
                            <option value="D">Deposit</option> 
                            <option value="W">Withdrawal</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2" style="margin-top:25px;">
                    <button class="btn btn-info pull-left mleft5 search_data" id="search_data">Show</button>
                </div>
                <div class="col-md-4" style="margin-top:20px;float:right;">
                    <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;width:100%">
                </div>
            </div> 
            </div>
            <div class="row" style="margin:auto;margin-top:20px;width:100%;">
                <div class="table-purchase_request tableFixHead2">
                  <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table_purchase_request" width="100%">
                    <thead>
                        <tr>
                            <th style="text-align:left;">Sr.No</th>
                            <th style="text-align:left;">Booking Type</th>
                            <th style="text-align:left;">BookingID</th>
                            <th style="text-align:left;">Booking Date</th>
                            <th style="text-align:left;">WH</th>
                            <th style="text-align:left;">Center Name</th>
                            <th style="text-align:left;">Item Name</th>
                            <th style="text-align:left;">Quantity</th>
                            <th style="text-align:left;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                  </table>   
                </div>
            </div>
    </div>
</div>
<script>
$(document).ready(function(){
    
    $('#search_data').on('click',function(){
        var AccountID = $('#AccountID').val();
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var w_id = $("#w_id :selected").val();
	    var CenterID = $("#CenterID :selected").val();
	    var ItemID = $("#item :selected").val();
	    var BookingType = $("#BookingType :selected").val();

	    $.ajax({
            url:"<?php echo base_url(); ?>clients/GetBookingListCust",
            method:"POST",
            dataType:"json",
            data:{
                AccountID:AccountID,
                from_date:from_date, 
                to_date:to_date, 
                w_id:w_id,
                CenterID:CenterID,
                ItemID:ItemID,
                BookingType:BookingType,
            },
            beforeSend: function () {
                $('#table_purchase_request tbody').html('');
            },
            success:function(data){
                if(data == ''){ 
                    data = '<span style="color:red;">No records found...</span>';
                }
                else{
                    $('#table_purchase_request tbody').html(data);
                    $('.GetDetails').on('click',function(){ 
                        BookingID = $(this).attr("data-id");
                        window.open("<?php echo base_url(); ?>clients/GetBookingListDetails/"+BookingID,'_blank');
                    });
                }
            }
        });
    });
});
</script>
<script>
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table_purchase_request");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) 
        {
            td = tr[i].getElementsByTagName("td")[0];
            td1 = tr[i].getElementsByTagName("td")[1];
            td2 = tr[i].getElementsByTagName("td")[2];
            td3 = tr[i].getElementsByTagName("td")[3];
            td4 = tr[i].getElementsByTagName("td")[4];
            td5 = tr[i].getElementsByTagName("td")[5];
            td6 = tr[i].getElementsByTagName("td")[6];
            td7 = tr[i].getElementsByTagName("td")[7];
            td8 = tr[i].getElementsByTagName("td")[8];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else if(td1){
                    txtValue = td1.textContent || td1.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else if(td2){
                    txtValue = td2.textContent || td2.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }else if(td3){
                    txtValue = td3.textContent || td3.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }else if(td4){
                    txtValue = td4.textContent || td4.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }else if(td5){
                    txtValue = td5.textContent || td5.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }else if(td6){
                    txtValue = td6.textContent || td6.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }else if(td7){
                    txtValue = td7.textContent || td7.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }else if(td8){
                    txtValue = td8.textContent || td8.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }
                else{
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
 </script>