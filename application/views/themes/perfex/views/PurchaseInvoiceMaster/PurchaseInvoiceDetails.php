<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .table-purchase_request          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.table-purchase_request thead th { position: sticky; top: 0; z-index: 1; }
.table-purchase_request tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
td:hover {
    cursor: pointer;
}
tr:hover {
    background-color: #ccc;
}
</style>


<div class="col-md-12">
    <div class="panel_s">
        <div class="panel-body">
            <div class="row">
                <nav aria-label="breadcrumb" >
                    <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                        <li class="breadcrumb-item" ><a href="<?= base_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                        <li class="breadcrumb-item active text-capitalize"><b>Kirti One</b></li>
                        <li class="breadcrumb-item active" aria-current="page"><b>Purchase Invoice Details</b></li>
                    </ol>
                </nav>
                <hr class="hr_style">
                
                <div class="col-md-8">
                    <h4><b>Purchase Invoice Detail</b></h4>
                    <div class="table-purchase_request tableFixHead2">
                        <table class="tree  table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                        <tbody id="for_uppercase">
                            <tr>
                                <td><b>PI.No. : </b></td>
                                <td><?php echo $PurchaseInvoiceData->Inv_No; ?></td>
                                
                                <td><b>PI Date. : </b></td>
                                <td><?php echo _d($PurchaseInvoiceData->Inv_date); ?></td>
                            </tr>
                            
                            <tr>
                                <td><b>PO.No. : </b></td>
                                <td><?php echo $PurchaseInvoiceData->PurchID; ?></td>
                                
                                <td><b>PO Date: </b></td>
                                <td><?php echo _d($PurchaseInvoiceData->Transdate); ?></td>
                            </tr>
                            
                            <tr>
                                <td><b>Center Name : </b></td>
                                <td><b><?php echo $PurchaseInvoiceData->CenterName; ?></b></td>
                                
                                <td><b>Vendor Name : </b></td>
                                <td><?php echo $PurchaseInvoiceData->company; ?></td>
                            </tr>
                            
                            <tr>
                                 <td><b>Mobile.No : </b></td>
                                <td><?php echo $PurchaseInvoiceData->phonenumber; ?></td>
                                
                                <td><b>State : </b></td>
                                <td><?php echo $PurchaseInvoiceData->state_name; ?></td>
                            </tr>
                            
                        </tbody>
                        </table>  
                    </div>
                </div>
                    
            </div>  
            
            <!--Item table-->
            <div class="clearfix mtop30"></div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-purchase_request tableFixHead2"  id="first_table_container">
                        <table class="table-purchase_request tree table table-bordered OrderList" id="OrderList" width="100%">
                        
                        </table>   
                    </div>
                    <span id="searchh2" style="display:none;">Loading.....</span>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
$(document).ready(function(){  
    
    var ID = "<?php echo $this->uri->segment(3); ?>";
    
    load_data();
    function load_data(){
       
        $.ajax({
            url:"<?php echo base_url(); ?>PurchaseInvoiceMaster/GetPurchInvoiceList",
            method:"POST",
            data:{ID:ID},
            beforeSend: function () {
                $('#OrderList').html('');
            },
            success:function(data){
                if(data != ''){
                    $('#OrderList').html(data);
                }else{
                    $('#OrderList').html('<span style="color:red;">No records found...</span>');
                }
            }
        });
    }
    
    $('#search_data').on('click',function(){
        load_data();
    });

});
</script>



 