<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
	#AccountID {
    text-transform: uppercase;
	}
	#table_purchase_request td:hover {
    cursor: pointer;
	}
	#table_purchase_request tr:hover {
    background-color: #ccc;
	}
    .table-purchase_request          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
    .table-purchase_request thead th { position: sticky; top: 0; z-index: 1; }
    .table-purchase_request tbody th { position: sticky; left: 0; }

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
                <div class="row" style="margin:auto;width:100%;">
                    <nav aria-label="breadcrumb" >
						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
							<li class="breadcrumb-item active text-capitalize"><b>Transactions</b></li>
							<li class="breadcrumb-item active" aria-current="page"><b>Booking List</b></li>
						</ol>
					</nav>
					<hr class="hr_style">
                    <div class="col-md-12">
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
                    <!--<div class="col-md-2" >
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
                    </div>-->
                    <div class="col-md-2" >
                        <div class="form-group" app-field-wrapper="CenterID">
                            <label for="CenterID" class="form-label">Center Name</label>
                            <select name="CenterID[]" id="CenterID" class="selectpicker form-control" data-live-search="true" multiple tabindex="-98" data-actions-box = "1" data-width="100%">
                                <!--<option value="">All</option>-->
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
                                <option value="T">Trade Finance</option>
                                <option value="A">Anamat</option>
                                <option value="D">Deposit</option> 
                                <option value="W">Withdrawal</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2" >
                        <div class="form-group" app-field-wrapper="IsApprove">
                            <label for="IsApprove" class="form-label">Status</label>
                            <select name="IsApprove" id="IsApprove" class="selectpicker form-control" data-live-search="true">
                                <option value="">All</option>
                                <option value="Y" >Accepted</option>
                                <option value="N">Rejected</option> 
                                <option value="NA">No Action</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-top:20px;">
                        <button class="btn btn-info pull-left mleft5 search_data" id="search_data">Show</button>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <?php if (has_permission_new('Booking_list', '', 'export')) {
                        ?>
                        <a class="btn btn-default buttons-excel buttons-html5"  style="margin-top: -1px margin-left:10px;;"  tabindex="0" aria-controls="table-purchase_request" href="#" id="caexcel"><span>Export to excel</span></a>
                        <?php } ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <?php if (has_permission_new('Booking_list', '', 'print')) {
                        ?>
                        <a class="btn btn-default" href="javascript:void(0);"  style="margin-top: -1px;margin-left:10px;"  onclick="printPage();">Print</a>
                        <?php } ?>
                        
                    </div>
                    
                    <div class="col-md-4" style="margin-top:20px;float:right;">
                        <input type="text" id="myInput1" onkeyup="myFunction2()" class="form-control" placeholder="Search here.." title="search here" style="float: right;width:100%">
                    </div>
                </div> 
            </div>
            <div class="row" style="margin:auto;width:100%;">
                <div class="table-purchase_request tableFixHead2">
                  <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table_purchase_request" width="100%">
                    <thead>
                        <tr>
                            <th style="text-align:center;">Sr. No.</th>
                            <th style="text-align:left;">Account Name</th>
                            <th style="text-align:left;">Booking Type</th>
                            <th style="text-align:left;">BookingID</th>
                            <th style="text-align:left;">Booking Date</th>
                            <!--<th style="text-align:left;">WH Name</th>-->
                            <th style="text-align:left;">Center Name</th>
                            <th style="text-align:left;">Item Name</th>
                            <th style="text-align:left;">Quantity</th>
                            <th style="text-align:left;">Status</th>
                            <th style="text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        
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
$(document).ready(function(){
    
    $('#search_data').on('click',function(){
        var AccountID = $('#AccountID').val();
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var w_id = $("#w_id :selected").val();
	   // var CenterID = $("#CenterID :selected").val();
	    var CenterID = $("#CenterID").val();
	    var ItemID = $("#item :selected").val();
	    var BookingType = $("#BookingType :selected").val();
	    var IsApprove = $("#IsApprove :selected").val();
	    
	    $.ajax({
            url:"<?php echo admin_url(); ?>Booking_list/GetAllBookings",
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
                IsApprove:IsApprove
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
                        window.open("<?php echo admin_url(); ?>Booking_list/GetBookingListDetails/"+BookingID,'_blank');
                    });
                }
            }
        });
    });
});
</script>
<script>
    function ReSendTradeToPcSoft(BookingID)
    {
      $.ajax({
            url:"<?php echo admin_url(); ?>order/ReSendTradeToPcSoft",
            dataType:"json",
            method:"POST",
            data:{
                BookingID:BookingID,
            },
            success: function(data){
                alert(data);
	            fetchEnquiries();
            }
      });
    };
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
$("#caexcel").click(function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var w_id = $("#w_id :selected").val();
	    var CustomerType = $("#CustomerType :selected").val();
	    var CenterID = $("#CenterID").val();
	    var ItemID = $("#item :selected").val();
	    var BookingType = $("#BookingType :selected").val();
	    var IsApprove = $("#IsApprove :selected").val();
    $.ajax({
        url:"<?php echo admin_url(); ?>Booking_list/export_alltradelist",
        method:"POST",
        data:{ from_date:from_date, 
                to_date:to_date, 
                w_id:w_id,
                CustomerType:CustomerType,
                CenterID:CenterID,
                ItemID:ItemID,
                BookingType:BookingType,
                IsApprove:IsApprove},
        success:function(data){
            response = JSON.parse(data);
            window.location.href = response.site_url+response.filename;
        }
    });
});
</script> 


<script type="text/javascript">
    function printPage()
    {
        var html_filter_name =    $('.report_for').html();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Daily Deposit Trader</td>';
        heading_data += '</tr>';
        heading_data += '<tr>';
        // heading_data += '<td style="text-align:center;"colspan="3">'+html_filter_name+'</td>';
        // heading_data += '</tr>';
        heading_data += '</tbody></table>';
        var print_data = stylesheet+heading_data+tableData
        newWin= window.open("");
        newWin.document.write(print_data);
        newWin.print();
        newWin.close();
    };
</script>

