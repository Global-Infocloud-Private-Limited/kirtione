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
		 <div class="panel_s">
		        <?php
                    $from_date = "01/".date('m')."/".date('Y');
                    $to_date = date('d/m/Y');
                ?> 
        <div class="panel-body">
            <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Misc Reports </b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>OutStanding Calculation List</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
		    <div class="_buttons">
                <!--<div class="col-md-2">
                    <?php echo render_date_input('from_date','From',$from_date); ?>
                </div>
                <div class="col-md-2">
                    <?php echo render_date_input('to_date','To',$to_date);  ?>
                </div>-->
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="Center">
                        <label for="Center" class="form-label">Center</label>
                        <select name="Center" id="Center" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <?php
                                foreach($centers as $value){
                                    ?>
                                        <option value="<?php echo $value['CenterID']; ?>" ><?php echo $value['CenterName']; ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <!--<div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="invoice_by">
                        <label for="invoice_by" class="form-label">Invoice By</label>
                        <select name="invoice_by" id="invoice_by" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <?php
                                foreach($AllInvoiceByCompany as $value){
                                    ?>
                                        <option value="<?php echo $value['AccountFrom']; ?>" ><?php echo $value['PlantName']; ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>-->
                
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="outstanding_to">
                        <label for="outstanding_to" class="form-label">Invoice To</label>
                        <select name="outstanding_to" id="outstanding_to" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <?php
                                foreach($AllStockToParty as $value){
                                    ?>
                                        <option value="<?php echo $value['AccountID']; ?>" ><?php echo $value['company']; ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="service_type">
                        <label for="service_type" class="form-label">Service Type</label>
                        <select name="service_type" id="service_type" class="selectpicker form-control" data-live-search="true">
                            <option value="D">Warehouse Deposit</option>
                            <option value="T">Trade Finance</option>
                            <option value="A">Anamat</option>
                        </select>
                    </div>
                </div>
                
                <div class="clearfix"></div>
                <div class="col-md-1">
                    <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 10px;" id="search_data">Show</button>
                    
                </div>
                <div class="col-md-1">
                    <div class="custom_button">
                        <?php if (has_permission_new('outstandingcalculation', '', 'print')) {
                                    ?>
                        <a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 10px;margin-left:10px;"  onclick="printPage();">Print</a>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-md-10" style="margin-top:7px;">
                    <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search here.." title="Search" style="float: right;width:25%">
                </div>
            </div>
            
            <div class="clearfix mtop20"></div>
            
            <div class="table-purchase_request tableFixHead2">
              <table class="table-purchase_request tree table table-bordered " id="table-purchase_request" width="100%">
                
              </table>   
            </div>
            <span id="searchh2" style="display:none;">Loading.....</span>
            
            
            
        </div>
        </div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script>
$(document).ready(function(){
    $('#search_data').on('click',function(){
	    var CenterID = $("#Center :selected").val();
	    var outstanding_to = $("#outstanding_to :selected").val();
	    var service_type = $("#service_type :selected").val();
	    $.ajax({
            url:"<?php echo admin_url(); ?>GateControl/GetOutStandingList",
            method:"POST",
            data:{outstanding_to:outstanding_to,service_type:service_type,CenterID:CenterID},
            beforeSend: function () {
                $('#table-purchase_request').html('');
            },
            success:function(data){
                if(data != ''){
                    $('#table-purchase_request').html(data);
                }
                else{
                    $('#table-purchase_request').html('<span style="color:red;">No records found...</span>');
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
                }else{
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
 </script>
 
<script type="text/javascript">
    function printPage()
    {
        var html_filter_name =    $('#report_for').val();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Outstanding List</td>';
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
 
</body>
</html>
