<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
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
<div id="wrapper">
	<div class="content">
	 <div class="row">
	     <div class="col-md-8">
	         <div class="row">
        		 <div class="panel_s">
        		        <?php
                            $from_date = "01/".date('m')."/".date('Y');
                            $to_date = date('d/m/Y');
                        ?> 
                <div class="panel-body">
                    <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Broker Initiate</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
        		    <div class="_buttons">
                        <div class="col-md-4">
                            <?php echo render_date_input('from_date','From',$from_date); ?>
                        </div>
                        <div class="col-md-4">
                            <?php echo render_date_input('to_date','To',$to_date);  ?>
                        </div>
                        
                        <div class="col-md-4" >
                            <div class="form-group" app-field-wrapper="TType">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="selectpicker form-control" data-live-search="true">
                                    <option value="NA" >Pending</option>
                                    <option value="Y">Accept</option> 
                                    <option value="N" >Reject</option>
                                    <option value="">All</option>
                                </select>
                            </div>
                        </div>
                        
                            <hr>
                        
                        <div class="col-md-6">
                            <?php if (has_permission_new('BrokerInitiateRequest', '', 'create')) {
                                ?>
                            <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 10px;" id="search_data">Show</button>
                            <?php } else { ?>
                            <button class="btn btn-info pull-left mleft5 search_data disabled" disabled style="margin-top: 10px;" id="search_data">Show</button>
                            <?php } ?>
                            <div class="custom_button">
                                <?php if (has_permission_new('BrokerInitiateRequest', '', 'print')) {
                                ?>
                                <a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 10px;margin-left:10px;"  onclick="printPage();">Print</a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-6" style="margin-top:7px;">
                            <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search here.." title="Search" style="float: right;width:100%">
                        </div>
                        
                    </div>
                    
                    <div class="clearfix mtop20"></div>
                    
                    <div class="table-purchase_request tableFixHead2" style="margin-top:1%;">
                      <table class="table-purchase_request tree table table-bordered " id="table-purchase_request" width="100%">
                        <thead>
                            <tr>
                                <th style="text-align:left;">Sr.No.</th>
                                <th style="text-align:left;">Request Date</th>
                                <th style="text-align:left;">Send From ID</th>
                                <th style="text-align:left;">Send From Name</th>
                                <th style="text-align:left;">Send To ID</th>
                                <th style="text-align:left;">Send To Name</th>
                                <th style="text-align:left;">Status</th>
                                <th style="text-align:left;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="filter_data_table">
                            
                        </tbody>
                      </table>   
                    </div>
                    <span id="searchh2" style="display:none;">Loading.....</span>
                    
                    
                    
                </div>
                </div>
        		</div>
	     </div>
	     
	     <div class="col-md-4">
	         <div class="row">
        		<div class="panel_s">
                <div class="panel-body">
                    <!--<h4>Assign Trader to Broker</h4>-->
                    <!--<hr>-->
                    <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Assign Trader to Broker</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
                    <div class="col-md-12" >
                        <div class="msg" style="display:none;">Please wait while data fetching</div>
                        <div class="msg2" style="display:none;">Please wait Trader Broker Mapping</div>
                    </div>
                    
                    <div class="col-md-4" >
                        <div class="form-group" app-field-wrapper="TraderID">
                            <label for="TraderID" class="form-label">TraderID</label>
                            <input type="text" name="TraderID" id="TraderID" class="form-control" maxlength="10" minlength="10" onkeypress="return isNumber(event)">
                        </div>
                    </div>
                    <div class="col-md-8" >
                        <div class="form-group" app-field-wrapper="TraderName">
                            <label for="TraderName" class="form-label">Trader Name</label>
                            <input type="text" name="TraderName" id="TraderName" readonly class="form-control" >
                        </div>
                    </div>
                    
                    <div class="col-md-4" >
                        <div class="form-group" app-field-wrapper="BrokerID">
                            <label for="BrokerID" class="form-label">BrokerID</label>
                            <input type="text" name="BrokerID" id="BrokerID" class="form-control" maxlength="10" minlength="10" onkeypress="return isNumber(event)">
                        </div>
                    </div>
                    <div class="col-md-8" >
                        <div class="form-group" app-field-wrapper="BrokerName">
                            <label for="BrokerName" class="form-label">Broker Name</label>
                            <input type="text" name="BrokerName" id="BrokerName" readonly class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <?php if (has_permission_new('BrokerInitiateRequest', '', 'create')) {
                                ?>
                        <button class="btn btn-info pull-left mleft5 assigned" style="margin-top: 10px;" id="assigned">Assign</button>
                        <?php } else { ?>
                        <button class="btn btn-info pull-left mleft5 assigned disabled" disabled style="margin-top: 10px;" id="assigned">Assign</button>
                        <?php } ?>
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
$(document).ready(function(){
    $('#search_data').on('click',function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var status = $("#status :selected").val();
	   
	    $.ajax({
            url:"<?php echo admin_url(); ?>BrokerInitiate/GetBrokerInitiateRequest",
            method:"POST",
            data:{from_date:from_date, to_date:to_date, status:status},
            beforeSend: function () {
                $('#filter_data_table').html('');
            },
            success:function(data){
                if(data != ''){
                    $('#filter_data_table').html(data);
                }
                else{
                    $('#filter_data_table').html('<span style="color:red;">No records found...</span>');
                }
            }
        });
    });
    $('#TraderID').on('focus',function(){
        $('#TraderID').val('');
        $('#TraderName').val('');
    })
    // Get Trader Details
    $('#TraderID').on('blur',function(){
        var AccountID = $("#TraderID").val();
        if(AccountID){
            $.ajax({
                url:"<?php echo admin_url(); ?>BrokerInitiate/GetAccountDetails",
                method:"POST",
                dataType:"JSON",
                data:{AccountID:AccountID},
                beforeSend: function () {
                    $('.msg').css('display','block');
                    $('.msg').css('color','blue');
                },
                complete: function () {
                    $('.msg').css('display','none');
                },
                success:function(data){
                    if(data != null){
                        if(data.CustomerType == "3"){
                            $('#TraderName').val(data.company);
                        }else{
                            alert('please enter only Trader ID');
                            $('#TraderID').focus();
                        }
                    }else{
                        alert('TraderID is not registered');
                    }
                }
            });
        }
    });
    
    $('#BrokerID').on('focus',function(){
        $('#BrokerID').val('');
        $('#BrokerName').val('');
    })
    
    // Get Broker Details
    $('#BrokerID').on('blur',function(){
        var AccountID = $("#BrokerID").val();
        if(AccountID){
            $.ajax({
                url:"<?php echo admin_url(); ?>BrokerInitiate/GetAccountDetails",
                method:"POST",
                dataType:"JSON",
                data:{AccountID:AccountID},
                beforeSend: function () {
                    $('.msg').css('display','block');
                    $('.msg').css('color','blue');
                },
                complete: function () {
                    $('.msg').css('display','none');
                },
                success:function(data){
                    if(data != null){
                        if(data.CustomerType == "2"){
                            $('#BrokerName').val(data.company);
                        }else{
                            alert('please enter only Broker ID');
                            $('#BrokerID').focus();
                        }
                    }else{
                        alert('BrokerID is not registered');
                    }
                }
            });
        }
    });
    
    $('#assigned').on('click',function(){
        var BrokerID = $("#BrokerID").val();
        var TraderID = $("#TraderID").val();
        if(TraderID == ""){
            alert('please select TraderID');
            $('#TraderID').focus();
        }else if(BrokerID == ""){
            alert('please select BrokerID');
            $('#BrokerID').focus();
        }else{
            $.ajax({
                url:"<?php echo admin_url(); ?>BrokerInitiate/TraderBrokerAssign",
                method:"POST",
                dataType:"JSON",
                data:{BrokerID:BrokerID,TraderID:TraderID},
                beforeSend: function () {
                    $('.msg2').css('display','block');
                    $('.msg2').css('color','blue');
                },
                complete: function () {
                    $('.msg2').css('display','none');
                },
                success:function(data){
                    if(data.status = true){
                        $('#BrokerID').val('');
                        $('#BrokerName').val('');
                        $('#TraderID').val('');
                        $('#TraderName').val('');
                    }
                    alert(data.message);
                }
            });
        }
    });
});
</script>
<script>
    function GetRequst(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var status = $("#status :selected").val();
	   
	    $.ajax({
            url:"<?php echo admin_url(); ?>BrokerInitiate/GetBrokerInitiateRequest",
            method:"POST",
            data:{from_date:from_date, to_date:to_date, status:status},
            beforeSend: function () {
                $('#filter_data_table').html('');
            },
            success:function(data){
                if(data != ''){
                    $('#filter_data_table').html(data);
                }
                else{
                    $('#filter_data_table').html('<span style="color:red;">No records found...</span>');
                }
            }
        });
    }
</script>

<script>
    function acceptRequest(ID)
    {
       $.ajax({
            url:"<?php echo admin_url(); ?>BrokerInitiate/acceptRequest",
            dataType:"json",
            method:"POST",
            data:{
                ID:ID,
            },
            success: function(data){
                if(data == true){
                    alert('Request accepted');
                   GetRequst();
                }else{
                    alert('somthing went wrong please try again');
                }
            }
       });
    };
    
    function rejectRequest(ID)
    {
       $.ajax({
            url:"<?php echo admin_url(); ?>BrokerInitiate/rejectRequest",
            dataType:"json",
            method:"POST",
            data:{
                ID:ID,
            },
            success: function(data){
                if(data == true){
                   alert('Request rejected');
                   GetRequst();
                }else{
                    alert('somthing went wrong please try again');
                }
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
            td9 = tr[i].getElementsByTagName("td")[9];
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
                else if(td9){
                    txtValue = td9.textContent || td9.innerText;
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
        heading_data += '<td style="text-align:center;"colspan="3">Purchase Request List</td>';
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
</body>
</html>
