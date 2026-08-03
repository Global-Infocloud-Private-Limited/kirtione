<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<style>
    .load_data          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.load_data thead th { position: sticky; top: 0; z-index: 1; }
.load_data tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">  
                    <div class="panel-body">
                        <nav aria-label="breadcrumb" >
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>K1E-Filling </b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>GST Purchase Report</b></li>
            				</ol>
            			</nav> 
			            <hr class="hr_style">
                        <div class="row">
                            <?php
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
                                    $from_date = date('01/m/Y');
                                    $to_date = date('d/m/Y');
                                }
                            ?>
                            
                            
                            
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="from_date">
                                    <label for="from_date" class="control-label from_date_text">From Date</label>
                                    <div class="input-group date">
                                        <input type="text" id="from_date" name="from_date" class="form-control datepicker" value="<?php echo $from_date; ?>" autocomplete="off">
                                        <div class="input-group-addon">
                                           <i class="fa fa-calendar calendar-icon"></i>
                                         </div>
                                    </div>
                                 </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="to_date">
                                    <label for="to_date" class="control-label to_date_text">To Date</label>
                                    <div class="input-group date">
                                        <input type="text" id="to_date" name="to_date" class="form-control datepicker" value="<?php echo $to_date; ?>" autocomplete="off">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar calendar-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="bill_type">Bill Type</label>
                                    <select name="bill_type" id="bill_type" class="form-control selectpicker" data-live-search="true">
                                        <option value="1">All Bills</option>
                                        <option value="2">GST Bills</option>
                                        <option value="3">Non-GST Bills</option>
                                   </select>
                               </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="form-group">
                                <label for="gst_type">GST Structure</label>
                               <select name="gst_type" id="gst_type" class="form-control selectpicker" data-live-search="true">
                                   <option value="1">With GST Breakup</option>
                                    <option value="2">Without GST Breakup</option>
                               </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                   <label for="bill_wise_type">Report Type</label>
                                   <select name="bill_wise_type" id="bill_wise_type" class="form-control selectpicker" data-live-search="true">
                                        <option value="2">Day Wise Summary</option>
                                       <option value="1">Bill Wise Details</option>
                                   </select>
                               </div>
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="act_name">Supplier</label>
                                    <select name="act_name" id="act_name" class="form-control selectpicker" data-live-search="true" data-none-selected-text="All">
                                        <option value=""></option>
                                        <?php foreach ($VendorList as $vd) { ?>
                                            <option value="<?php echo $vd['AccountID']; ?>">
                                                <?php echo $vd['company']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-5">
                                <div class="custom_button" style="padding-top: 20px;">
                                    <?php if (has_permission_new('k1GSTR_purchase', '', 'view')) {
                                    ?>
                                    <button class="btn btn-info pull-left mleft5 search_data" id="search_data" style="font-size:12px;">Show</button>
                                    <?php } ?>
                                </div>
                                <div class="custom_button">
                                    <?php if (has_permission_new('k1GSTR_purchase', '', 'print')) {
                                    ?>
                                    <button class="btn btn-default pull-left mleft5 " href="javascript:void(0);"    onclick="printPage();" style="font-size:12px;">Print</button>
                                    <?php } ?>
                                </div>
                                <div class="custom_button">
                                    &nbsp;&nbsp;
                                    <?php if (has_permission_new('k1GSTR_purchase', '', 'export')) {
                                    ?>
                                    <a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="production_report" href="#" id="caexcel" style="font-size:12px;"><span>Export</span></a>
                                    <?php } ?>
                                </div>
                            </div>
                            <!--<div class="col-md-4" style="margin-top:10px;">
                				<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search here.." title="Search" class="form-control" style="float: right;width:100%">
                			</div>-->
                        </div> 
                        <div class="clearfix"></div>
                        <div class="row">
                            <span id="searchh" style="display:none;">Please wait data fetching..</span>
                            <span id="searchh1" style="display:none;">Please wait dat exporting..</span>
                            <div class="col-md-12">
                                <div class="fixTableHead load_data"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .production_report { overflow: auto;max-height: 60vh;position:relative;top: 0px; }
    .production_report thead th { position: sticky; top: 0; z-index: 1; }
    .production_report tbody th { position: sticky; left: 0; }
    
    /* Just common table stuff. Really. */
    .production_report table  { border-collapse: collapse; }
    .production_report th, td { padding: 3px 3px !important; white-space: nowrap;font-size:11px; line-height:1.42857143;vertical-align: middle;}
    .production_report th     { background: #50607b;color: #fff !important; }
</style>
<?php init_tail(); ?>
<!--new update -->
<script type="text/javascript" language="javascript">
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.querySelector(".table-gstr_purchase_report");
        tr = table.getElementsByTagName("tr");

        for (i = 3; i < tr.length; i++) {
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
<script type="text/javascript" language="javascript" >
$(document).ready(function()
{
    
    $('#search_data').on('click',function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var accountId = $("#act_name").val();
	    var bill_type = $("#bill_type").val();
	    var bill_wise_type = $("#bill_wise_type").val();
	    var account_full_name = $("#account_full_name").val();
	    var gst_type = $("#gst_type").val();
	    
	    $.ajax({
            url:"<?php echo admin_url(); ?>K1E_Filling/purchase_gst_table",
            dataType:"JSON",
            method:"POST",
            cache: false,
            data:{from_date:from_date,to_date:to_date,
                accountId:accountId,account_full_name:account_full_name,
                bill_type:bill_type,bill_wise_type:bill_wise_type,gst_type:gst_type},
            beforeSend: function () {
                $('#searchh').css('display','block');
                $('.load_data').css('display','none');
            },
            complete: function () {
                $('.load_data').css('display','');
                $('#searchh').css('display','none');
            },
            success:function(data){
                $('.load_data').html(data);
            }
        });
    });
});

    $("#caexcel").click(function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var accountId = $("#act_name").val();
	    var bill_type = $("#bill_type").val();
	    var bill_wise_type = $("#bill_wise_type").val();
	    var account_full_name = $("#account_full_name").val();
	    var gst_type = $("#gst_type").val();
        $.ajax({
            url:"<?php echo admin_url(); ?>K1E_Filling/k1export_gst_purchase_report",
            method:"POST",
            data:{from_date:from_date,account_full_name:account_full_name, to_date:to_date,accountId:accountId,bill_type:bill_type,bill_wise_type:bill_wise_type,gst_type:gst_type},
            beforeSend: function () {
               $('#searchh1').css('display','block');
            },
            complete: function () {
                $('#searchh1').css('display','none');
            },
            success:function(data){
                response = JSON.parse(data);
                window.location.href = response.site_url+response.filename;
            }
        });
    });

     
    $(document).ready(function() {
        $('tbody').scroll(function(e) { //detect a scroll event on the tbody
  	        $('thead').css("left", -$("tbody").scrollLeft()); //fix the thead relative to the body scrolling
            $('thead th:nth-child(1)').css("left", $("tbody").scrollLeft()); //fix the first cell of the header
            $('tbody td:nth-child(1)').css("left", $("tbody").scrollLeft()); //fix the first column of tdbody
        });
    });
    function printPage()
    {
        var html_filter_name =    $('.report_for').html();
        var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">GST Purchase report</td>';
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
    if(cur_y > fin_y){
        var year2 = parseInt(fin_y) + parseInt(1);
        var year2_new = "20"+year2;
        
        var e_dat = new Date(year2_new+'/03/31');
        var maxEndDate_new = e_dat;
    }else{
         var maxEndDate_new = maxEndDate;
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
        timepicker: false
    });
    
});
</script> 


