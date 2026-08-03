<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-daily_report          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.table-daily_report thead th { position: sticky; top: 0; z-index: 1; }
.table-daily_report tbody th { position: sticky; left: 0; }


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
    							<li class="breadcrumb-item active text-capitalize"><b>HR </b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Timesheet Report</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
                
            <div class="row">
              <div class="col-md-12">
                
             <!-- <h4 class="no-margin font-bold">Timesheet Report</h4>-->
             <!--<hr>-->
              </div>
               <div class="clearfix"></div> 
               <div class="col-md-2">
                    <?php $from_date = '01/'.date('m').'/'.date('Y'); ?>
                    <?php echo render_date_input('from_date','FROM',$from_date);  ?>
                </div>
                        
                <div class="col-md-2">
                    <?php $to_date = date('d/m/Y');?>        
                    <?php echo render_date_input('to_date','TO',$to_date); ?>
                </div>
            <div class="col-md-2">
              <?php echo render_select('by_staff',$AllStaff,array('staffid',array('firstname','lastname')),'Select Staff'); ?>
            </div>
           <!--
            <div class="col-md-2">
              <?php echo render_select('by_dept',$departments,array('departmentid','name'),'Select Departmentt'); ?>
            </div>-->
            <div class="col-md-3">
              <?php echo render_select('by_position',$job_position,array('position_id','position_name'),'Select Designation'); ?>
            </div>
            
            <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 24px;" id="search_data">Show</button>
             
            <div class="clearfix"></div> 
            <div class="col-md-3" >
                <div class="custom_button">
                    <?php if(is_admin() || has_permission_new('timesheet_report','','export')) {?>
                        &nbsp;<a class="btn btn-default buttons-excel buttons-html5"    tabindex="0"  href="#" id="caexcel"><span>Export to excel</span></a>
                    <?php }?>
                    <?php if(is_admin() || has_permission_new('timesheet_report','','print')) {?>
                        <a class="btn btn-default" href="javascript:void(0);"    onclick="printPage();">Print</a>
                    <?php }?>
                </div>
            </div>
            <div class="col-md-9">
                 <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
            
            </div>
             
          </div>
         <div class="table-daily_report tableFixHead2">
             
              <table class="tree table table-striped table-bordered table-daily_report  tableFixHead2" id="table-daily_report" width="100%">
                  
                <thead>
                 
                  <tr>
                    <th style="text-align:left;">Staff Name </th>
                    <th style="text-align:left;">Date</th>
                    <th style="text-align:left;">In Time</th>
                    <th style="text-align:left;">Out Time</th>
                    <th style="text-align:left;">Km. Travel</th>
                    <th style="text-align:left;">Action</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>   
            </div>
            <span id="searchh2" style="display:none;">Loading.....</span>
            <span id="searchh3" style="display:none;">Please wait data exporting.....</span>
          </div>
        </div>
        
      </div>
      
    </div>
  </div>
</div>

<?php init_tail(); ?>
<script>
   function myFunction2() {
      
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("table-daily_report");
  tr = table.getElementsByTagName("tr");
for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
      td1 = tr[i].getElementsByTagName("td")[1];
      td2 = tr[i].getElementsByTagName("td")[2];
      td3 = tr[i].getElementsByTagName("td")[3];
      td4 = tr[i].getElementsByTagName("td")[4];
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
        
      }else {
        tr[i].style.display = "none";
      }
    }     
  }
}
}
}
}
}

 function load_data(from_date,to_date,by_staff,by_dept,by_position)
  {
    $.ajax({
        url:"<?php echo admin_url(); ?>timesheets/GetTimesheetReport",
        dataType:"json",
        method:"POST",
        data:{from_date:from_date, to_date:to_date,by_staff:by_staff,by_dept:by_dept,by_position:by_position},
        beforeSend: function () {
        $('#searchh2').css('display','block');
        $('.table-daily_report tbody').css('display','none');
        },
        complete: function () {
                            
        $('.table-daily_report tbody').css('display','');
        $('#searchh2').css('display','none');
        },
        success:function(data){
           var msg = "Accounts List Filter Account: "+data.act +", SubActGroup Name: " +data.sub_act+", Active: "+data.active;
        $(".report_for").text(msg);
           $('#table-daily_report tbody').html(data.html);
        }
    });
  }
 $('#search_data').on('click',function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var by_staff = $("#by_staff").val();
	    var by_dept = $("#by_dept").val();
	    var by_position = $("#by_position").val();
        load_data(from_date,to_date,by_staff,by_dept,by_position);
 });

</script>
 
<script>
 
$("#caexcel").click(function () {
    var from_date = $("#from_date").val();
    var to_date = $("#to_date").val();
    $.ajax({
        url: "<?php echo admin_url(); ?>timesheets/export_timesheetReport",
        method: "POST",
        data: { from_date: from_date, to_date: to_date },
        beforeSend: function () {
            $('#searchh3').css('display', 'block');
        },
        complete: function () {
            $('#searchh3').css('display', 'none');
        },
        success: function (data) {
            response = JSON.parse(data);
            window.location.href = response.site_url + response.filename;
        }
    });
});

</script>
<script type="text/javascript">
 function printPage(){
    var html_filter_name =    $('.report_for').html();
         var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
    var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
         heading_data += '<tr>';
         heading_data += '<td style="text-align:center;"colspan="3">Account Head</td>';
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
      function sortTable(f,n){
	var rows = $('#table-daily_report tbody  tr').get();

	rows.sort(function(a, b) {

		var A = getVal(a);
		var B = getVal(b);

		if(A < B) {
			return -1*f;
		}
		if(A > B) {
			return 1*f;
		}
		return 0;
	});

	function getVal(elm){
		var v = $(elm).children('td').eq(n).text().toUpperCase();
		if($.isNumeric(v)){
			v = parseInt(v,10);
		}
		return v;
	}

	$.each(rows, function(index, row) {
		$('#table-daily_report').children('tbody').append(row);
	});
}
var f_sl = 1;
var f_nm = 1;
$("#sl").click(function(){
      if ( $('.up').css('display') == 'none')
    {
         $(".up_starting").hide()
      $(".up").show()
      $(".down").hide()
    }else{
         $(".up_starting").hide()
        $(".up").hide()
      $(".down").show()
    }
    f_sl *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_sl,n);
});
$("#nm").click(function(){
    f_nm *= -1;
    var n = $(this).prevAll().length;
    sortTable(f_nm,n);
});
 </script>
</body>
</html>