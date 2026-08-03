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
    							<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Trade Settled List</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
		    <div class="_buttons">
                <div class="col-md-2">
                    <?php echo render_date_input('from_date','From',$from_date); ?>
                </div>
                <div class="col-md-2">
                    <?php echo render_date_input('to_date','To',$to_date);  ?>
                </div>
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
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="purchase_for">
                        <label for="purchase_for" class="form-label">Purchase For</label>
                        <select name="purchase_for" id="purchase_for" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <?php
                                foreach($AllParty as $value){
                                    ?>
                                        <option value="<?php echo $value['PlantID']; ?>" ><?php echo $value['PlantName']; ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="purchase_for">
                        <label for="purchase_for" class="form-label">Trade Status</label>
                        <select name="TradeStatus" id="TradeStatus" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <option value="1">Open</option>
                            <option value="2">Settled</option>
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <button class="btn btn-primary pull-left mleft5 mright5 mtop10 search_data" id="search_data"><i class="fa fa-search"></i> Show</button>
                    <?php if (has_permission_new('SettledList', '', 'print')) { ?>
                    <a class="btn btn-warning pull-left mright5 mtop10" href="javascript:void(0);" onclick="printPage();"><i class="fa fa-print"></i> Print</a>
                    <?php } ?>
                    <?php if (has_permission_new('SettledList', '', 'export')) { ?>
                    <a class="btn btn-success pull-left mtop10 buttons-excel buttons-html5" tabindex="0" aria-controls="table-purchase_request" href="#" id="caexcel"><i class="fa fa-file-excel-o"></i> <span>Export to excel</span></a>
                    <?php } ?>
                </div>
                <div class="col-md-8" style="padding-top: 7px;">
                    <input type="text" class="form-control" id="myInput1" onkeyup="myFunction2()" placeholder="Search.." title="Search" style="float: right;width:35%;display:none">
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <div class="table-purchase_request tableFixHead2" >
              <table class="table-purchase_request tree table table-bordered " id="table-purchase_request" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:left;">Booking ID</th>
                        <th style="text-align:left;">Booking Date</th>
                        <th style="text-align:left;">Settlement Date</th>
                        <th style="text-align:left;">Due Date</th>
                        <th style="text-align:left;">Purchase For</th>
                        <th style="text-align:left;">Center Name</th>
                        <th style="text-align:left;">Party Name</th>
                        <th style="text-align:left;">Broker Name</th>
                        <th style="text-align:left;">Item Name</th>
                        <th style="text-align:left;">Booking Rate</th>
                        <th style="text-align:left;">Rate(at Setlled)</th>
                        <th style="text-align:left;">Booking WT(MT)</th>
                        <th style="text-align:left;">Inward WT(MT)</th>
                        <th style="text-align:left;">Trade Completion(%)</th>
                        <th style="text-align:left;">Shortage Charge</th>
                        <th style="text-align:left;">Non Delivery Charge</th>
                        <th style="text-align:left;">Commision Invoice</th>
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
</div>
<?php init_tail(); ?>
<script>
$(document).ready(function(){
    $('#from_date').on('change', function(){
        $('#filter_data_table').html('<span style="color:red;"></span>');
	    $('#myInput1').css('display','none');
    });
    $('#to_date').on('change', function(){
        $('#filter_data_table').html('<span style="color:red;"></span>');
    	$('#myInput1').css('display','none');
    });
    $('#Center').on('change', function(){
        $('#filter_data_table').html('<span style="color:red;"></span>');
    	$('#myInput1').css('display','none');
    });
    $('#purchase_for').on('change', function(){
        $('#filter_data_table').html('<span style="color:red;"></span>');
    	$('#myInput1').css('display','none');
    });
    $('#TradeStatus').on('change', function(){
        $('#filter_data_table').html('<span style="color:red;"></span>');
    	$('#myInput1').css('display','none');
    });
    $('#search_data').on('click',function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var CenterID = $("#Center :selected").val();
	    var purchase_for = $("#purchase_for :selected").val();
	    var TradeStatus = $("#TradeStatus :selected").val();
	    $.ajax({
            url:"<?php echo admin_url(); ?>GateControl/GetBookingListDetails",
            method:"POST",
            data:{from_date:from_date, to_date:to_date,CenterID:CenterID,purchase_for:purchase_for,TradeStatus:TradeStatus},
            beforeSend: function () {
                $('#filter_data_table').html('');
            },
            success:function(data){
                if(data != ''){
                    $('#myInput1').css('display','block');
                    $('#filter_data_table').html(data);
                }else{
                    $('#filter_data_table').html('<span style="color:red;">No records found...</span>');
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
<script>
    $("#caexcel").click(function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
        var CenterID = $("#Center :selected").val();
	    var purchase_for = $("#purchase_for :selected").val();
	    var TradeStatus = $("#TradeStatus :selected").val();
        $.ajax({
            url:"<?php echo admin_url(); ?>GateControl/export_tradsettledlist",
            method:"POST",
            data:{from_date:from_date, to_date:to_date,CenterID:CenterID,purchase_for:purchase_for,TradeStatus:TradeStatus},
            success:function(data){
                response = JSON.parse(data);
                window.location.href = response.site_url+response.filename;
            }
        });
    });
</script>
</body>
</html>
