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
    							<li class="breadcrumb-item active" aria-current="page"><b>Purchase Payment List</b></li>
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
                
                
                <div class="col-md-12">
                    <hr>
                </div>
                
                <div class="col-md-12">
                    <div class="col-md-4">
                         <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 10px;margin-right: 10px;" id="search_data">Show</button>
                         
                         <?php if (has_permission_new('PurchasePaymentList', '', 'export')) {
                            ?>
                         <a class="btn btn-default buttons-excel buttons-html5"  style="margin-top: 10px;"  tabindex="0" aria-controls="table-purchase_request" href="#" id="caexcel"><span>Export to excel</span></a>
                         <?php } ?>
                         
                         <?php if (has_permission_new('PurchasePaymentList', '', 'print')) {
                        ?>
                         <a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 10px;margin-left:10px;"  onclick="printPage();">Print</a>
                         <?php } ?>
                   </div>
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4" style="margin-top:19px;">
                        <input type="text" id="myInput1" onkeyup="myFunction2()" class="form-control" placeholder="Search here.." title="Search" style="float: right;width:100%">
                    </div>
                </div>
                
            </div>
            
            <div class="clearfix mtop20"></div>
            <span id="search" style="display:none;">Please wait fetching data..</span>
            <span id="search1" style="display:none;">Please wait exporting data..</span>
            <div class="table-purchase_request tableFixHead2">
              <table class="table-purchase_request tree table table-bordered " id="table-purchase_request" width="100%">
                
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
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var CenterID = $("#Center :selected").val();
	    var purchase_for = $("#purchase_for :selected").val();
	    $.ajax({
            url:"<?php echo admin_url(); ?>GateControl/GetPendingPaymentList",
            method:"POST",
            data:{from_date:from_date, to_date:to_date,CenterID:CenterID,purchase_for:purchase_for},
            beforeSend: function () {
                $('#table-purchase_request').html('');
                $('#search').css('display','block');
            },
            complete: function () {
                $('#search').css('display','none');
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
    function fill_data(id){
        window.open("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+id,'_blank');
    }
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
 
<script type="text/javascript">
    function printPage()
    {
        var html_filter_name =    $('.report_for').html();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Purchase Payment List</td>';
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
        $.ajax({
            url:"<?php echo admin_url(); ?>GateControl/export_Purchasepaymentlist",
            method:"POST",
            data:{from_date:from_date, to_date:to_date,CenterID:CenterID,purchase_for:purchase_for},
            beforeSend: function () {
                $('#search1').css('display','block');
            },
            complete: function () {
                $('#search1').css('display','none');
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
