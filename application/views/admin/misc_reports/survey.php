<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-10">
        <div class="panel_s">
          <div class="panel-body">
              <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Misc Reports </b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Survey Report</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
              <div class="_buttons">
                
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
                            $from_date = "01/".date('m')."/".date('Y');
                            $to_date = date('d/m/Y');
                        }
                    ?>
                       <div class="col-md-2">
                           
                            <?php echo render_date_input('from_date','FROM',$from_date);  ?>
                        </div>
                        
                        <div class="col-md-2">
                            
                            <?php echo render_date_input('to_date','TO',$to_date); ?>
                        </div>
                        <div class="col-md-3">
							<div class="form-group" app-field-wrapper="staff">
								<!--<small class="req text-danger">* </small>-->
								<label for="staff" class="form-label">Staff ID</label>
								<select name="staff" id="staff" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
									<option value="">Non Selected</option>
								<?php
									foreach ($staff as $key => $value) {
								?>
									  <option value="<?php echo $value['staffid'];?>"><?php echo $value['firstname']. " ".$value['lastname']; ?></option>
								<?php
									}
								?>
								</select>
							</div>
						</div>
                    <div class="col-md-1">
                    <br>
                    <div class="custom_button">
                        <button class="btn btn-info pull-left mleft5 search_data" id="search_data" style="font-size:12px;">Show</button>
                    </div>
                </div>
            </div>
    		<div class="row">
    		    <div class="col-md-2">
                        <br>
                    <div class="custom_button">
                        <?php if (has_permission_new('survey_report', '', 'export')) {
                        ?>
                        <a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="table-daily_report" href="#" id="caexcel"><span>Export to excel</span></a>
                        <?php } ?>
                    </div>
                </div>   
                <div class="col-md-1">
                        <br>
                        <?php if (has_permission_new('survey_report', '', 'print')) {
                        ?>
                    <a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
                    <?php } ?>
                </div>
    			<div class="col-md-9">
    			    <br>
    				<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
    			</div>
    		</div>
            </div>
            <div class="clearfix"></div>
            
        <?php
        //print_r($company_detail);
        ?>
        <span id="searchh3" style="display:none;">Please wait exporting data...</span>
            <div class="fixTableHead load_data">
              
            </div>
            <span id="searchh" style="display:none;">Loading.....</span>
    
            
          </div>
</div>
</div>
</div>
</div>
</div>
<style>

.fixTableHead  { overflow: auto;max-height: 50vh;width:100%;position:relative;top: 0px; }
.fixTableHead thead th { position: sticky; top: 0; z-index: 1; }
.fixTableHead tbody th { position: sticky; left: 0; }

/* Just common table stuff. Really. */
.fixTableHead table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
.fixTableHead th     { background: #50607b;color: #fff !important; }
</style>

<?php init_tail(); ?>
<!--new update -->
<script src="<?= base_url() ?>public/plugins/jquery.table2excel.js"></script>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){

 $('#search_data').on('click',function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var staff_id = $("#staff").val();
	    
	    $.ajax({
          url:"<?php echo admin_url(); ?>misc_reports/get_survey_data",
          dataType:"JSON",
          method:"POST",
          cache: false,
          data:{from_date:from_date, to_date:to_date, staff_id:staff_id},
          beforeSend: function () {
                   
            $('#searchh').css('display','block');
            $('.load_data').css('display','none');
            
         },
          complete: function () {
            $('.load_data').css('display','');
            $('#searchh').css('display','none');
         },
          success:function(data){
				$('#myInput1').css('display','block');
                $('.load_data').html(data);
          }
        });
	    
 });
});

 $("#caexcel").click(function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var staff_id = $("#staff").val();
	    
	    $.ajax({
            url:"<?php echo admin_url(); ?>misc_reports/export_survey_details",
            method:"POST",
            data:{from_date:from_date, to_date:to_date, staff_id:staff_id},
            beforeSend: function () {
                $('#searchh3').css('display','block');
                
            },
            complete: function () {
                
                $('#searchh3').css('display','none');
            },
            success:function(data){
                //alert(data);
                response = JSON.parse(data);
                window.location.href = response.site_url+response.filename;
            }
        });
});

$('#from_date').on('change', function(){
    
    $('.load_data').css('display','none');
	$('#myInput1').css('display','none');
	
});
$('#to_date').on('change', function(){
    
    $('.load_data').css('display','none');
	$('#myInput1').css('display','none');
});
$('#staff').on('change', function(){
    
    $('.load_data').css('display','none');
	$('#myInput1').css('display','none');
});
    
</script>
<style>
    input[type=checkbox], input[type=radio] {
    margin: 4px 4px 0px;
    line-height: normal;
}
</style>
<script type="text/javascript">
 function printPage(){
        var html_filter_name =    $('.report_for').html();
    
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
         heading_data += '<tr>';
         heading_data += '<td style="text-align:center;"colspan="3">Survey List</td>';
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
    function myFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  
  filter = input.value.toUpperCase();
  table = document.getElementById("daily_report");
  tr = table.getElementsByTagName("tr");
  for (i = 3; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    td1 = tr[i].getElementsByTagName("td")[1];
    td2 = tr[i].getElementsByTagName("td")[2];
    td3 = tr[i].getElementsByTagName("td")[3];
    td4 = tr[i].getElementsByTagName("td")[4];
    td5 = tr[i].getElementsByTagName("td")[5];
    td6 = tr[i].getElementsByTagName("td")[6];
	td7 = tr[i].getElementsByTagName("td")[7];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td1) {
      txtValue = td1.textContent || td1.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td2) {
      txtValue = td2.textContent || td2.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td3) {
      txtValue = td3.textContent || td3.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td4) {
      txtValue = td4.textContent || td4.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td5) {
      txtValue = td5.textContent || td5.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td6) {
      txtValue = td6.textContent || td6.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td7) {
      txtValue = td7.textContent || td7.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}}}
}
}
}
}
}
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


