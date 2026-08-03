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

#pageLoader {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5); /* dark overlay */
  z-index: 99999;
  display: none;

  /* center loader */
  display: flex;
  justify-content: center;
  align-items: center;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 6px solid #f3f3f3;
  border-top: 6px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  100% { transform: rotate(360deg); }
}
</style>
<div id="wrapper">
  <div id="pageLoader" style="display:none;">
    <div>
      <div class="spinner"></div>
      <p style="color:#fff; text-align:center;">Please wait...</p>
    </div>
  </div>
	<div class="content">
		<div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-12">
                <nav aria-label="breadcrumb" >
                  <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                    <li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                    <li class="breadcrumb-item active text-capitalize"><b>Inventory</b></li>
                    <li class="breadcrumb-item active" aria-current="page"><b>Stock Adjustment Report</b></li>
                  </ol>
                </nav>
                <hr style="margin-Bottom:12px !important;">
              </div>
            </div>
            <div class="row">
              <?php
              $CY = date('y');
              $CM = date('m');

              // Convert FY code → actual years
              $fy_start = 2000 + $FY;
              $fy_end   = $fy_start + 1;
              $today_year  = 2000 + $CY;
              $today_month = intval($CM);

              // Condition 1: Inside FY from Apr(Y1) to Dec(Y1)
              $is_inside_fy = ($today_year == $fy_start  && $today_month >= 4 && $today_month <= 12) || ($today_year == $fy_end    && $today_month >= 1 && $today_month <= 3);
              if ($is_inside_fy) {
                $year = ($today_month >= 4) ? $fy_start : $fy_end;
                $from_date = "01/$CM/$year";
                $to_date   = date("t/$CM/$year");
              } else {
                $from_date = "01/03/$fy_end";
                $to_date   = "31/03/$fy_end";
              }
              ?> 
              <div class="col-md-2">
                  <?php echo render_date_input('from_date','From Date',$from_date); ?>
              </div>
              <div class="col-md-2">
                  <?php echo render_date_input('to_date','To Date',$to_date);  ?>
              </div> 
              <div class="col-md-3">                            
                <div class="form-group" app-field-wrapper="AccountID">                        
                  <label for="centerid" class="control-label">Center Name</label>
                  <select name="centerid" id="centerid" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                    <option value="">All</option>   			    
                    <?php                                
                    foreach($centermaster as $center)                                 
                    {                                
                      echo '<option value="' . $center['CenterID'] . '">' . $center['CenterName'] . '</option>';                                
                    }                             
                    ?>                                                                                            
                  </select>
                </div>
              </div>
              <div class="col-md-3">                            
                <div class="form-group" app-field-wrapper="AccountID">                        
                  <label for="AccountID" class="control-label">Select Party</label>
                  <select name="AccountID" id="AccountID" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                    <option value="">All</option> 
                    <?php
                      foreach($clients as $value) 
                      {
                        echo '<option value="' . $value['AccountID'] . '">' . $value['company']. ' (' . $value['AccountID'] . ')</option>';
                      } 
                    ?>                                                                 
                  </select>
                </div>
              </div>
              <div class="col-md-2" >
                  <div class="form-group" app-field-wrapper="ItemID">
                      <label for="adjustmenttype" class="form-label">Adjustment Type</label>
                      <select name="adjustmenttype" id="adjustmenttype" class="selectpicker form-control" data-live-search="true">
                          <option value="" selected>All</option>
                          <option value="MARKETING">Marketing</option>
                          <option value="FREE DISTRIBUTION">Free Distribution</option>
                          <option value="STOCK ADJUSTMENT">Stock Adjustment</option>
                      </select>
                  </div>
              </div>
              <div class="col-md-2" >
                  <div class="form-group" app-field-wrapper="ItemID">
                      <label for="reporttype" class="form-label">Report Type</label>
                      <select name="reporttype" id="reporttype" class="selectpicker form-control" data-live-search="true">
                          <option value="1" selected>Bill Wise</option>
                          <option value="2">Item Wise</option> 
                      </select>
                  </div>
              </div>
              <div class="col-md-3">                            
                <div class="form-group" app-field-wrapper="itemid">                        
                  <label for="itemid" class="control-label">Select Item</label>
                  <select name="itemid" id="itemid" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true" disabled>
                    <option value="">All</option> 
                    <?php
                      foreach($products as $item) 
                      {
                          echo '<option value="' . $item['ProductID'] . '">' . $item['ProductName'] . '</option>';
                      } 
                    ?>                                                                                       
                  </select>
                </div>
              </div>
              <div class="col-md-4" style="margin-top:20px;">
                <?php
                  if (has_permission_new('PurchOrderList', '', 'view')) {
                ?>
                <button class="btn btn-info pull-left mleft5 search_data" style="margin-right:10px;" id="search_data">Show</button>
                <?php
                }
                ?>
                
                <div class="custom_button">
                    <?php
                            if (has_permission_new('PurchOrderList', '', 'export')) {
                        ?>
                            <a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="table-purchase_request" href="#" id="caexcel"><span>Export to excel</span></a>
                        <?php
                      }
                        ?>
                        <?php
                            if (has_permission_new('PurchOrderList', '', 'print')) {
                        ?>
                            <a class="btn btn-default" href="javascript:void(0);"  style="margin-left:10px;"  onclick="printPage();">Print</a>
                        <?php
                      }
                        ?>	
                </div>
              </div>
              <div class="col-md-3" style="margin-top:20px;">
                <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search here.." title="Search" class="form-control" style="float: right;width:100%">
              </div>
              <div class="clearfix"></div>
              <div class="col-md-12">
                  <span id="fetchspan" style="display:none;">please wait data loading</span>
                  <span id="exportspan" style="display:none;">please wait data exporting</span>
              </div>
              <div class="clearfix"></div>
              <div class="col-md-12">
                <div class="table-purchase_request tableFixHead2"  id="first_table_container">
                  <table class="table-purchase_request tree table table-bordered OrderList" id="OrderList" width="100%"></table>   
                </div>
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
$(document).ready(function(){  
    
  load_data();
  function load_data(){
    var from_date = $("#from_date").val();
    var to_date = $("#to_date").val();	   
    var order_status = $("#orderstat :selected").val();
    var AccountID = $("#AccountID").val();        	
    var CenterID  = $("#centerid").val();  
    var ItemID = $("#itemid").val();
    var Report_type = $("#reporttype").val();
		var Entry_type = $("#entrytype").val();
		var adjustmenttype = $("#adjustmenttype").val();
    
    $.ajax({
      url:"<?php echo admin_url(); ?>K1Stock_transfer/GetAdjustmentReportFilterData",
      method:"POST",
      data:{from_date, to_date, order_status, AccountID, CenterID, ItemID, Report_type, Entry_type, adjustmenttype},
      beforeSend: function() {
        $('#OrderList').html('<span style="color:red;">please wait report loading...</span>');
        $("#pageLoader").show();
        $("#fetchspan").css("display", "block");
      },
      complete: function() {
        $("#fetchspan").css("display", "none");
        $("#pageLoader").hide();
      },
      success:function(data){
        if(data != ''){
          $('#OrderList').html(data);
        }else{
          $('#OrderList').html('<span style="color:red;">No records found...</span>');
        }
      }
    });
  }
    
  $('#search_data').on('click',function(){
    load_data();
  });
  $('#reporttype').on('change', function() {
    var Report_type = $("#reporttype").val();		
    if(Report_type == 2)
    {
      $('#itemid').prop('disabled', false); 
      $('#itemid').val(''); 
      $('#itemid').selectpicker('refresh'); 
    }else {           
      $('#itemid').prop('disabled', true);
      $('#itemid').val('');
      $('#itemid').selectpicker('refresh'); 
    }
  });

});
</script>

<script>
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.querySelector(".table-purchase_request");
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
		var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();	   
        var order_status = $("#orderstat :selected").val();
		var orderstatustext = $("#orderstat option:selected").text();  
        var AccountID = $("#AccountID").val();   
		var company = $("#AccountID option:selected").text();         	
        var CenterID  = $("#centerid").val();  
		var CenterName = $("#centerid option:selected").text();
		var reporttype;
		var Report_type = $("#reporttype").val();
        if(Report_type == 1)
        { reporttype = "Bill Wise";}
        else if(Report_type == 2)
        { reporttype = "Item Wise";}
    var adjustmenttype;
    var AdjustmentType = $("#adjustmenttype").val();
        if(AdjustmentType == "MARKETING")
        { adjustmenttype = "Marketing";}
        else if(AdjustmentType == "FREE DISTRIBUTION")
        { adjustmenttype = "Free Distribution";}
        else if(AdjustmentType == "STOCK ADJUSTMENT")
        { adjustmenttype = "Stock Adjustment";}
        else { adjustmenttype = "All";}

		var center;
        if(CenterID !=="")
        { center = CenterName;
        }else { center = "All" ; }

		var party;
        if(AccountID !=="")
        { party = company; }
        else
        { party = "All"; }   

		var Entry_type = $("#entrytype").val();
		var EntryName = $("#entrytype option:selected").text(); 	

        var html_filter_name =    $('.report_for').html();
		var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';

		var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Sales Return Report : From : ' + from_date + ', To : ' + to_date + ', Center Name : ' + center + ', Report Type : ' + reporttype + ', Party Name : ' + party + ', Adjustment Type : ' + adjustmenttype + '</td>';
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
    $("#caexcel").click(function () 
    {     
		var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();       
      var AccountID = $("#AccountID").val();       
      var Partyname = $("#AccountID option:selected").text();
      var CenterID  = $("#centerid").val();  
      var Centertext  = $("#centerid option:selected").text(); 
      var Report_type = $("#reporttype").val();     
      var ReportTypetext = $("#reporttype option:selected").text();    
      var ItemID = $("#itemid").val();       
      var ItemName = $("#itemid option:selected").text();    
      var AdjustmentType = $("#adjustmenttype").val();
      var AdjustmentTypetext = $("#adjustmenttype option:selected").text(); 
      
        $.ajax({
            url: "<?php echo admin_url(); ?>K1Stock_transfer/export_GetAdjustmentReportFilterData",
            method: "POST",
            data: {Partyname,Centertext,ReportTypetext,ItemName,ItemID,Report_type,AccountID, 
                from_date, to_date, CenterID, AdjustmentType, AdjustmentTypetext},
            beforeSend: function() {
                // setting a timeout
                $("#exportspan").css("display", "block");
            },
            complete: function() {
                $("#exportspan").css("display", "none");
            },
            success: function (data) {  
                response = JSON.parse(data);
                window.location.href = response.site_url+response.filename;
            }
        });
    });
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
