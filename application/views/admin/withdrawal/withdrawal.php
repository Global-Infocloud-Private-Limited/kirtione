<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table_WithdrawalTrade          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
    .table_WithdrawalTrade thead th { position: sticky; top: 0; z-index: 1; }
    .table_WithdrawalTrade tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
</style>
<div id="wrapper">
	<div class="content">
	<div class="row">
		<div class="panel_s">
        <div class="panel-body">
            <nav aria-label="breadcrumb" >
				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
					<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
					<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
					<li class="breadcrumb-item active" aria-current="page"><b>Daily Withdrawal Trade</b></li>
				</ol>
			</nav>
			<hr class="hr_style">
		    <div class="_buttons">
                <?php
                    $selected_company = $this->session->userdata('root_company');
                    $fy = $this->session->userdata('finacial_year');
                    $fy_new  = $fy + 1;
                    $lastdate_date = '20'.$fy_new.'-03-31';
                    $firstdate_date = '20'.$fy_new.'-04-01';
                    $curr_date = date('Y-m-d');
                    $curr_date_new    = new DateTime($curr_date);
                    $last_date_yr = new DateTime($lastdate_date);
                    if($last_date_yr < $curr_date_new){
                        $to_date = '31/03/20'.$fy_new;
                        $from_date = '01/03/20'.$fy_new;
                    }else{
                        $from_date = "01/".date('m')."/".date('Y');
                        $to_date = date('d/m/Y');
                    }
                ?> 
                <div class="col-md-2">
                    <?php echo render_date_input('from_date','From',$from_date); ?>
                </div>
                <div class="col-md-2">
                    <?php echo render_date_input('to_date','To',$to_date);  ?>
                </div>
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="account_type">
                        <label for="CenterID" class="form-label">Center Name</label>
                        <select name="CenterID" id="CenterID" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <?php 
                                foreach($CenterList as $key=>$val){
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
                        <label for="item" class="form-label">Item</label>
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
                    <div class="form-group" app-field-wrapper="account_type">
                        <label for="CustomerType" class="form-label">Party Type</label>
                        <select name="CustomerType" id="CustomerType" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <?php 
                                foreach($customers as $key=>$val){
                            ?>        
                                    <option value="<?php echo $val['id']; ?>" ><?php echo $val['Name']; ?></option>
                            <?php        
                                }
                            ?>
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
                <div class="col-md-12">
                    <div class="col-md-4">

                        <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 19px;" id="search_data">Show </button>
                        <?php if (has_permission_new('Withdrawal_Booking', '', 'export')) {
                        ?>
                        <a class="btn btn-default buttons-excel buttons-html5"  style="margin-top: 19px;"  tabindex="0" aria-controls="table-purchase_request" href="#" id="caexcel"><span>Export to excel</span></a>
                        <?php } ?>
                        
                        <?php if (has_permission_new('Withdrawal_Booking', '', 'print')) {
                        ?>
                        <a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 19px;margin-left:10px;"  onclick="printPage();">Print</a>
                        <?php } ?>
                    </div>
                    <div class="custom_button col-md-4">
                        <!-- <a class="dt-button buttons-pdf buttons-html5" tabindex="0" aria-controls="ca_datatable" href="#"><span>Export to PDF</span></a> -->
                    </div>
                    <div class="col-md-4" style="margin-top: 20px;float: right;">
                        <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." class="from-control" style="float: right;width:100%">
                    </div>
                </div>
                
            </div>
           
            <div class="table-purchase_request tableFixHead2">
              <table class="tree table table-striped table-bordered table_WithdrawalTrade tableFixHead2" id="table_WithdrawalTrade" width="100%">
                <thead>
                    <tr style="display:none;">
                        <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span style="font-size:10px;font-weight:600;">Order List</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                    </tr>
                    <tr>
                        <th style="text-align:left;">Sr.No</th>
                        <th style="text-align:left;">BookingID</th>
                        <th style="text-align:left;">Booking Date</th>
                        <th style="text-align:left;">Party Type</th>
                        <th style="text-align:left;">AccountID</th>
                        <th style="text-align:left;">Party Name</th>
                        <!--<th style="text-align:left;">WHID</th> 
                        <th style="text-align:left;">WH Name</th>-->
                        <th style="text-align:left;">Center Name</th>
                        <th style="text-align:left;">ItemID</th>
                        <th style="text-align:left;">Item Name</th>
                        <th style="text-align:left;">Quantity</th>
                        <th style="text-align:left;">Unit</th>
                        <th style="text-align:left;">Status</th>
                        <th style="text-align:left;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
              </table>   
            </div>
            <span id="searchh2" style="display:none;">Loading.....</span>
        </div>
        </div>
		</div>
		
		<div class="modal fade" id="modifyModal" tabindex="-1" role="dialog" aria-labelledby="modifyModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="padding:5px 10px;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Modify Withdrawal Booking</h4>
                    </div>
                  <div class="modal-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>BookingID</td>
                                <th>Item Name</td>
                                <th>Party Type</td>
                                <th>Party Name</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" id="modal_BookingID" class="form-control" style="border:none;background-color:#fff;" readonly></td>
                                <td><input type="text" id="modal_item" class="form-control" style="border:none;background-color:#fff;" readonly></td>
                                <td><input type="text" id="modal_party_type" class="form-control" style="border:none;background-color:#fff;" readonly></td>
                                <td><input type="text" id="modal_party" class="form-control" style="border:none;background-color:#fff;" readonly> </td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="modal_payment" class="form-label">Payment Remark</label>
                            <input type="text" id="modal_payment" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <?php echo render_date_input('modify_date','Modify Date',date('Y-m-d')); ?>
                        </div>
                        <div class="col-md-4">
                            <label for="modal_status" class="form-label">Status</label>
                            <select name="modal_status" id="modal_status" class="selectpicker form-control" data-live-search="true">
                                <option value="NA">--Select Status--</option>
                                <option value="Y">Accept</option> 
                                <option value="Y">Modify</option> 
                            </select>
                        </div>
                    </div>    
                    <div class="row">
                        <div class="col-md-3" style="margin-top:2%">
                            <button type="button" id="Modify" class="btn btn-primary">Modify</button>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
	</div>
</div>
<?php init_tail(); ?>
<script>
    function acceptTrade(BookingID)
    {
      $.ajax({
            url:"<?php echo admin_url(); ?>Withdrawal/AcceptTrade",
            dataType:"json",
            method:"POST",
            data:{
                BookingID:BookingID,
            },
            success: function(data){
                if(data == true){
                    
                    var from_date = $("#from_date").val();
            	    var to_date = $("#to_date").val();
            	    var CenterID = $("#CenterID :selected").val();
            	    var CustomerType = $("#CustomerType :selected").val();
            	    var ItemID = $("#item :selected").val();
            	    var IsApprove = $("#IsApprove :selected").val();
	    
                    $.ajax({
                        url:"<?php echo admin_url(); ?>Withdrawal/GetWithdrawalBooking",
                        method:"POST",
                        data:{
                            from_date:from_date, 
                            to_date:to_date, 
                            CenterID:CenterID,
                            CustomerType:CustomerType,
                            ItemID:ItemID,
                            IsApprove:IsApprove
                        },
                        beforeSend: function () {
                            $('#table_WithdrawalTrade tbody').html('');
                        },
                        success:function(data){
                            if(data == null){ 
                                data = '<span style="color:red;">No records found...</span>';
                            }
                            else{
                                $('#table_WithdrawalTrade tbody').html(data);
                            }
                        }
                    });
                    
                }
            }
      });
    };
</script>
<script>
    function modifyTrade(BookingID)
    {
        $.ajax({
            url:"<?php echo admin_url(); ?>Withdrawal/getModalData",
            dataType:"json",
            method:"POST",
            data:{
                BookingID:BookingID,
            },
            success: function(data)
            {
                $('#modal_BookingID').val(data.BookingID);
                $('#modal_item').val(data.ItemName);
                
                if(data.CustomerType == '1'){
                    var party_type = 'Farmer';
                }
                if(data.CustomerType == '2'){
                    var party_type = 'Broker';
                }
                if(data.CustomerType == '3'){
                    var party_type = 'Trader';
                }
                if(data.CustomerType == '4'){
                    var party_type = 'Corporate/Processor';
                }
                
                $('#modal_party_type').val(party_type);
                if((data.firstname != null) && (data.lastname != null)){
                    $('#modal_party').val(data.firstname+' '+data.lastname);
                }
                if((data.firstname != null) && (data.lastname == null)){
                    $('#modal_party').val(data.firstname);
                }
                if((data.firstname == null) && (data.lastname != null)){
                    $('#modal_party').val(data.lastname);
                }
                
                $('#modal_quantity').val(data.quantity);
                $('#modal_unit').val(data.unit).selectpicker('refresh');
                $('#modifyModal').modal('show');
                
                $('#Modify').click(function(){
                    $('#modifyModal').modal('hide');
                    var modal_BookingID = $('#modal_BookingID').val();
                    var modal_payment = $('#modal_payment').val();
                    var modal_status = $('#modal_status').val();
                    var modify_date = $('#modify_date').val();
                    
                    $.ajax({
                        url:"<?php echo admin_url(); ?>Withdrawal/ModifyTrade",
                        dataType:"json",
                        method:"POST",
                        data:{
                            modal_BookingID:modal_BookingID,
                            modal_payment:modal_payment,
                            modal_status:modal_status,
                            modify_date:modify_date
                        },
                        success: function(res){
                            if(res == true){
                                var from_date = $("#from_date").val();
                        	    var to_date = $("#to_date").val();
                        	    var CenterID = $("#CenterID :selected").val();
                        	    var CustomerType = $("#CustomerType :selected").val();
                        	    var ItemID = $("#item :selected").val();
                        	    var IsApprove = $("#IsApprove :selected").val();
            	    
                                $.ajax({
                                    url:"<?php echo admin_url(); ?>Withdrawal/GetWithdrawalBooking",
                                    method:"POST",
                                    data:{
                                        from_date:from_date, 
                                        to_date:to_date, 
                                        CenterID:CenterID,
                                        CustomerType:CustomerType,
                                        ItemID:ItemID,
                                        IsApprove:IsApprove
                                    },
                                    beforeSend: function () {
                                        $('#table_WithdrawalTrade tbody').html('');
                                    },
                                    success:function(data){
                                        if(data == null){ 
                                            data = '<span style="color:red;">No records found...</span>';
                                        }
                                        else{
                                            $('#table_WithdrawalTrade tbody').html(data);
                                        }
                                    }
                                });
                                
                            }
                        }
                   });    
                });
            }
        });
    };
</script>
<script>
$(document).ready(function(){
    $('#search_data').on('click',function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var CenterID = $("#CenterID :selected").val();
	    var CustomerType = $("#CustomerType :selected").val();
	    var ItemID = $("#item :selected").val();
	    var IsApprove = $("#IsApprove :selected").val();
	    
	   // alert('test');return false;
	    $.ajax({
            url:"<?php echo admin_url(); ?>Withdrawal/GetWithdrawalBooking",
            method:"POST",
            data:{
                from_date:from_date, 
                to_date:to_date, 
                CenterID:CenterID,
                CustomerType:CustomerType,
                ItemID:ItemID,
                IsApprove:IsApprove
            },
            beforeSend: function () {
                $('#table_WithdrawalTrade tbody').html('');
            },
            success:function(data){
                if(data == null){ 
                    data = '<span style="color:red;">No records found...</span>';
                    $('#table_WithdrawalTrade tbody').html(data);
                }
                else{
                    $('#table_WithdrawalTrade tbody').html(data);
                }
            }
        });
    });
});
</script>
<script>
    function awaiting(){
        alert("Awaiting Client Approval !");
    }
</script>
<script>
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.querySelector(".table_WithdrawalTrade");
        tr = table.getElementsByTagName("tr");

        for (i = 1; i < tr.length; i++) {
            var tdArray = tr[i].getElementsByTagName("td");
            var rowContainsSearchTerm = false;
            for (var j = 0; j < tdArray.length; j++) {
                td = tdArray[j];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        rowContainsSearchTerm = true;
                        break;
                    }
                }
            }
            if (rowContainsSearchTerm) {

                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
    
 </script>
<script type="text/javascript">
    function printPage()
    {
        var html_filter_name =    $('.report_for').html();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Withdrawal Trade List</td>';
        heading_data += '</tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">'+html_filter_name+'</td>';
        heading_data += '</tr>';
        heading_data += '</tbody></table>';
        var print_data = stylesheet+heading_data+tableData
        newWin= window.open("");
        newWin.document.write(print_data);
        newWin.print();
        newWin.close();
    };
</script>
 <script>
    $(document).ready(function(){
    var maxEndDate = new Date('Y/m/d');
    var fin_y = "<?php echo $this->session->userdata('finacial_year')?>";
    
    var year = "20"+fin_y;
    var cur_y = new Date().getFullYear().toString().substr(-2);
    if(cur_y => fin_y){
        var year2 = parseInt(fin_y) + parseInt(1);
        var year2_new = "20"+year2;
        var e_dat = new Date(year2_new+'/03/31');
        var maxEndDate_new = e_dat;
    }else{
        var e_dat2 = new Date(year2+'/03/31');
        var maxEndDate_new = e_dat2;
    }
    
    var minStartDate = new Date(year, 03);
    $('#from_date').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false
    });
    
    $('#to_date').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false,
        showOtherMonths: false,
        pickTime: false,
            orientation: "left",
    });
    
    });
</script> 

<script>
$("#caexcel").click(function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var w_id = $("#w_id :selected").val();
	    var CustomerType = $("#CustomerType :selected").val();
	    var ItemID = $("#item :selected").val();
	    var IsApprove = $("#IsApprove :selected").val();
    $.ajax({
        url:"<?php echo admin_url(); ?>Withdrawal/export_dailywithdrawaltrader",
        method:"POST",
        data:{ from_date:from_date, 
                to_date:to_date, 
                w_id:w_id,
                CustomerType:CustomerType,
                ItemID:ItemID,
                IsApprove:IsApprove},
        success:function(data){
            response = JSON.parse(data);
            window.location.href = response.site_url+response.filename;
        }
    });
});
</script> 
</body>
</html>
