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
    							<li class="breadcrumb-item active" aria-current="page"><b>Center Wise Trade Quantity</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
            <div class="col-md-8">
		    <div class="_buttons">
                    <div class="row">
                        <div class="col-md-2">
                            <?php echo render_date_input('from_date','From',$from_date); ?>
                        </div>
                        <div class="col-md-2">
                            <?php echo render_date_input('to_date','To',$to_date);  ?>
                        </div>
                        <div class="col-md-2">
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
                        <div class="col-md-2">
                            <div class="form-group" app-field-wrapper="ItemID">
                                <label for="ItemID" class="form-label">Commodity</label>
                                <select name="ItemID" id="ItemID" class="selectpicker form-control" data-live-search="true">
                                    <option value="">All</option>
                                    <?php
                                        foreach($items as $value){
                                            ?>
                                                <option value="<?php echo $value['ItemID']; ?>" ><?php echo $value['ItemName']; ?></option>
                                            <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group" app-field-wrapper="TradeType">
                                <label for="TradeType" class="form-label">Trade Type</label>
                                <select name="TradeType" id="TradeType" class="selectpicker form-control" data-live-search="true">
                                    <option value="">All</option>
                                    <option value="P">Purchase</option>
                                    <option value="T">Trade Finance</option>
                                    <option value="A">Anamat</option>
                                    <option value="D">Deposit</option>
                                    <option value="W">Withdrawal</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group" app-field-wrapper="TradeStatus">
                                <label for="TradeStatus" class="form-label">Status</label>
                                <select name="TradeStatus" id="TradeStatus" class="selectpicker form-control" data-live-search="true">
                                    <option value="">All</option>
                                    <option value="1">Open</option>
                                    <option value="2">Settled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-primary pull-left mleft5 mright5 mtop10 search_data" id="search_data"><i class="fa fa-search"></i> Show</button>
                        <?php if (has_permission_new('CenterWiseTradeQty', '', 'print')) { ?>
                        <a class="btn btn-warning pull-left mright5 mtop10" href="javascript:void(0);" onclick="printPage();"><i class="fa fa-print"></i> Print</a>
                        <?php } ?>
                        <?php if (has_permission_new('CenterWiseTradeQty', '', 'export')) { ?>
                        <a class="btn btn-success pull-left mright5 mtop10 buttons-excel buttons-html5" tabindex="0" aria-controls="table-centerwise-trade" href="#" id="caexcel"><i class="fa fa-file-excel-o"></i> <span>Export to excel</span></a>
                        <?php } ?>
                        <input type="text" class="form-control pull-right mtop10" id="myInput1" onkeyup="myFunction2()" placeholder="Search.." title="Search" style="width:35%;display:none">
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <div class="table-purchase_request tableFixHead2" >
              <table class="table-purchase_request tree table table-bordered " id="table-centerwise-trade" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:left;">Sr. No.</th>
                        <th style="text-align:left;">Center Name</th>
                        <th style="text-align:center;">Total Trade Quantity</th>
                        <th style="text-align:center;">Total Inward Qty</th>
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
</div>
<?php init_tail(); ?>
<script>
$(document).ready(function(){
    $('#from_date, #to_date, #Center, #ItemID, #TradeType, #TradeStatus').on('change', function(){
        $('#filter_data_table').html('<span style="color:red;"></span>');
	    $('#myInput1').css('display','none');
    });
    $('#search_data').on('click',function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var CenterID = $("#Center :selected").val();
	    var ItemID = $("#ItemID :selected").val();
	    var TradeType = $("#TradeType :selected").val();
	    var TradeStatus = $("#TradeStatus :selected").val();
	    $.ajax({
            url:"<?php echo admin_url(); ?>GateControl/GetCenterWiseTradeQuantity",
            method:"POST",
            data:{from_date:from_date, to_date:to_date, CenterID:CenterID, ItemID:ItemID, TradeType:TradeType, TradeStatus:TradeStatus},
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
        var input, filter, table, tr, td, i, j, txtValue, found;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table-centerwise-trade");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++)
        {
            td = tr[i].getElementsByTagName("td");
            found = false;
            for (j = 0; j < td.length; j++) {
                txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
            tr[i].style.display = found ? "" : "none";
        }
    }
 </script>
<script type="text/javascript">
    function getSelectedFilterLabel(selectId)
    {
        var text = $('#' + selectId + ' option:selected').text();
        return text ? $.trim(text) : 'All';
    }
    function printPage()
    {
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementById('table-centerwise-trade').innerHTML+'</table>';
        var heading_data = '<table border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="4"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="4"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr><td style="text-align:center;" colspan="4">Center Wise Trade Quantity Report</td></tr>';
        heading_data += '<tr><td style="text-align:center;" colspan="4">From Date: '+$("#from_date").val()+' | To Date: '+$("#to_date").val()+'</td></tr>';
        heading_data += '<tr><td style="text-align:center;" colspan="4">Center: '+getSelectedFilterLabel('Center')+' | Commodity: '+getSelectedFilterLabel('ItemID')+' | Trade Type: '+getSelectedFilterLabel('TradeType')+' | Status: '+getSelectedFilterLabel('TradeStatus')+'</td></tr>';
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
	    var ItemID = $("#ItemID :selected").val();
	    var TradeType = $("#TradeType :selected").val();
	    var TradeStatus = $("#TradeStatus :selected").val();
        $.ajax({
            url:"<?php echo admin_url(); ?>GateControl/export_centerwise_trade_quantity",
            method:"POST",
            data:{
                from_date:from_date,
                to_date:to_date,
                CenterID:CenterID,
                ItemID:ItemID,
                TradeType:TradeType,
                TradeStatus:TradeStatus,
                CenterText:$("#Center :selected").text().trim(),
                ItemText:$("#ItemID :selected").text().trim(),
                TradeTypeText:$("#TradeType :selected").text().trim(),
                TradeStatusText:$("#TradeStatus :selected").text().trim()
            },
            success:function(data){
                response = JSON.parse(data);
                window.location.href = response.site_url+response.filename;
            }
        });
    });
</script>
</body>
</html>
