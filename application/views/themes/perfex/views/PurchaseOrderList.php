<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.table-purchase_request          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.table-purchase_request thead th { position: sticky; top: 0; z-index: 1; }
.table-purchase_request tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
ul{
    max-height:250px !important;
}
#table-purchase_request td:hover {
    cursor: pointer;
}
#table-purchase_request tr:hover {
    background-color: #ccc;
}
</style>
<div class="panel_s">
  <div class="panel-body">
    <div class="col-md-12">
                <?php
                    
                    $from_date = "01/".date('m')."/".date('Y');
                    $to_date = date('d/m/Y');
                ?> 
        <div class="row">
            <h4>Purchase Order List</h4>
            <div class="col-md-2">
                <input type="text" value="<?php echo $this->session->userdata('AccountID'); ?>" id="AccountID" hidden>
                    <?php echo render_date_input('from_date','From',$from_date); ?>
                </div>
                <div class="col-md-2">
                    <?php echo render_date_input('to_date','To',$to_date);  ?>
                </div>
                
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="center">
                        <label for="center" class="form-label">Select Center</label>
                        <select name="center" id="center" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <?php
                                foreach($CenterList as $value){
                                    ?>
                                        <option value="<?php echo $value['CenterID']; ?>" ><?php echo $value['CenterName']; ?></option>
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
           
                <div class="col-md-2" style="margin-top:20px;">
                    <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search here.." title="Search" style="float: right;width:100%">
                </div>
                
                <div class="col-md-2" style="margin-top:12px;">
                    <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 10px;" id="search_data">Show</button>
                </div>
        </div>
        
        <div class="table-purchase_request tableFixHead2" style="margin-top:1%;">
            <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                <thead>

                    <tr>
                        <th style="text-align:left;">Sr.No</th>
                        <th style="text-align:left;">Trade ID</th>
                        <th style="text-align:left;">Trade Date</th>
                        <th style="text-align:left;">Center ID</th>
                        <th style="text-align:left;">Center Name</th>
                        <th style="text-align:left;">Broaker ID</th>
                        <th style="text-align:left;">Broaker Name</th>
                        <th style="text-align:left;">Item ID</th>
                        <th style="text-align:left;">Item Name</th>
                        <th style="text-align:left;">Net Wt.</th>
                        <th style="text-align:left;">Status</th>
                    </tr>

                </thead>
                <tbody id="table-purchase_request tbody">
                
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
	    var center = $("#center").val();
	    var item = $("#item :selected").val();
	    
	    $.ajax({
            url:"<?php echo base_url(); ?>clients/GetPurchaseOrderList",
            method:"POST",
            data:{AccountID:AccountID,from_date:from_date, to_date:to_date,center:center,item:item},
            beforeSend: function () {
                $('#searchh2').css('display','block');
                $('#table-purchase_request tbody').css('display','none');
            },
            complete: function () {
                $('#table-purchase_request tbody').css('display','');
                $('#searchh2').css('display','none');
            },
            success:function(data){
                if(data != ''){ 
                    $('#table-purchase_request tbody').html(data);
                    $('.GetDetails').on('click',function(){ 
                        Gate_in_ID = $(this).attr("data-id");
                        ASNID = $(this).attr("data-asn");
                        if(Gate_in_ID != ''){
                            // window.open("<?php echo base_url(); ?>Clients/CropSellDetails/"+Gate_in_ID,'_blank');
                            window.open("<?php echo base_url(); ?>Clients/PurchaseOrderDetails/"+Gate_in_ID,'_blank');
                        }
                        else if(Gate_in_ID == ''){
                            window.open("<?php echo base_url(); ?>Clients/PurchaseOrderDetailsByASN/"+ASNID,'_blank');
                        }
                    });
                }else{
                    data = '<span style="color:red;">No records found...</span>';
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
        table = document.getElementById("table-purchase_request");
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