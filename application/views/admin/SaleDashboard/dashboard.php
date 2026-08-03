<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
  <style>
      .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover, .navbar-pills.nav-tabs>li>a:focus, .navbar-pills.nav-tabs>li>a:hover{
          background-color: #02a9f4;
    color: #fff;
    border-radius: 1px;
      }
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
    							<li class="breadcrumb-item active text-capitalize"><b>Admin</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Sell Dashboard</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
            <ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active">
					<a href="#TraderTab" aria-controls="TraderTab" style="font-size:22px;padding:5px; 22px;" role="tab" data-toggle="tab">
						<?php echo "Trader"; ?>
					</a>
				</li>
				<li role="presentation">
					<a href="#FarmerTab" aria-controls="FarmerTab" role="tab" style="font-size:22px;padding:5px 22px;;" data-toggle="tab">
						<?php echo "Farmer"; ?>
					</a>
				</li>
			</ul>
			
			<div class="tab-content">
			    <!--// Trader Tab details-->
				<div role="tabpanel" class="tab-pane active" id="TraderTab">
					<div class="row">
					    <div class="col-md-12">
                    
                        </div>
                        <?php
                            $i = 1;
                        foreach($AllCenter as $key=>$val) { 
                        
                            if($i == "3"){
                                    $i = 1
                                ?>
                                <div class="clearfix"></div>
                            <?php
                            }
                        ?>
                        
                        
                        <div class="col-md-6">
                            <div class="table-daily_report">
                              <table class="tree table table-striped table-bordered table-daily_report" id="table-daily_report" width="100%">
                                <thead>
                                    <tr>
                                        <th colspan="10" style="text-align:center;"><?php echo $val["CenterName"];?></th>
                                    </tr>
                                  <tr>
                                    <td style="text-align:left;">QC</td>
                                    <td style="text-align:left;">Pur till Dt</td>
                                    <td style="text-align:left;">Avg Rate</td>
                                    <td style="text-align:left;">Today's Pur</td>
                                    <td style="text-align:left;">Avg Rate</td>
                                    <td style="text-align:left;">Curr Rate</td>
                                    <td style="text-align:left;">Today's Conf. Trade</td>
                                    <td style="text-align:left;">Closing Stock</td>
                                    <td style="text-align:left;">Avg rate of Closing Stock</td>
                                    <td style="text-align:left;">Comp Rate</td>
                                  </tr>
                                </thead>
                                <tbody>
                                <?php foreach($CenterWiseItem as $keys=>$value){
                                    $match = 0;
                                    $purch_weight = '';
                                    $Todaypurch_weight = '';
                                    $CurrentRate = '';
                                    $AvgRate = '';
                                    $AvgTRate = "";
                                        if($val['CenterID'] == $value["CenterID"]){
                                            
                                            foreach($ItemWiseCenterWisePurchase as $purkey=>$purvalue){
                                                if($purvalue["ItemID"] == $value["ItemID"] && $purvalue["CenterID"]==$val['CenterID']){
                                                    $match++;
                                                    $purch_weight = $purvalue["NetWeight"];
                                                }
                                            }
                                            
                                            foreach($ItemWiseCenterWiseTodayPurchase as $purTkey=>$purTvalue){
                                                if($purTvalue["ItemID"] == $value["ItemID"] && $purTvalue["CenterID"]==$val['CenterID']){
                                                    $match++;
                                                    $Todaypurch_weight = $purTvalue["NetWeight"];
                                                }
                                            }
                                            
                                            foreach($ItemWiseCenterWiseCurrentRate as $Ratekey=>$Ratevalue){
                                                if($Ratevalue["ItemID"] == $value["ItemID"] && $Ratevalue["CenterID"]==$val['CenterID']){
                                                    $CurrentRate = $Ratevalue["Rate"];
                                                }
                                            }
                                            
                                            foreach($ItemWiseCenterWiseAvgRate as $avgRatekey=>$AvgRatevalue){
                                                if($AvgRatevalue["ItemID"] == $value["ItemID"] && $AvgRatevalue["CenterID"]==$val['CenterID']){
                                                    $AvgRate = $AvgRatevalue["AVGRAte"] / $AvgRatevalue["TotalRate"];
                                                }
                                            }
                                            
                                            foreach($ItemWiseCenterWiseCurrentAvgRate as $avgTRatekey=>$AvgTRatevalue){
                                                if($AvgTRatevalue["ItemID"] == $value["ItemID"] && $AvgTRatevalue["CenterID"]==$val['CenterID']){
                                                    $AvgTRate = $AvgTRatevalue["AVGRAte"] / $AvgTRatevalue["TotalRate"];
                                                }
                                            }
                                            
                                    if($match > 0){
                                        ?>
                                            <tr>
                                                <td><?php echo $value["ItemID"];?></td>
                                                <td style="text-align:center"><?php echo  number_format($purch_weight,2,'.','')?></td>
                                                <td style="text-align:center"><?php echo  number_format($AvgRate,2,'.','')?></td>
                                                <td style="text-align:center"><?php echo  number_format($Todaypurch_weight,2,'.','')?></td>
                                                <td style="text-align:center"><?php echo  number_format($AvgTRate,2,'.','')?></td>
                                                <td><a href="<?php echo admin_url(); ?>Rate_master/RateUpdate" target="_blank"><?php echo  number_format($CurrentRate,2,'.','')?></a></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        <?php
                                    }
                                ?>
                                        
                                    <?php
                                        }
                                    } ?>
                                    
                                </tbody>
                              </table>   
                            </div>
                        </div>
                    <?php $i++; } ?>
					    
					    
					    
					</div>
				</div>
				
				<!--// Farmer Tab details-->
				<div role="tabpanel" class="tab-pane " id="FarmerTab">
					<div class="row">
					    <div class="col-md-12">
					        <h1>Coming Soon...</h1>
					        </div>
					</div>
				</div>
				
			</div>
							
            <div class="row">
                
            </div>
          </div>
</div>
</div>
</div>
</div>
</div>
<style>
    .table-daily_report { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
.table-daily_report thead th { position: sticky; top: 0; z-index: 1; }
.table-daily_report tbody th { position: sticky; left: 0; }

/* Just common table stuff. Really. */
.table-daily_report table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
.table-daily_report th     { background: #50607b;color: #fff !important; }
</style>


<?php init_tail(); ?>
<!--new update -->
<script src="<?= base_url() ?>public/plugins/jquery.table2excel.js"></script>
<script>
    function myFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("table-daily_report");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[3];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
 
  function load_data(from_date,to_date)
  {
    $.ajax({
      url:"<?php echo admin_url(); ?>sale_reports/load_data",
      dataType:"JSON",
      method:"POST",
      data:{from_date:from_date, to_date:to_date},
      beforeSend: function () {
               
        $('#searchh2').css('display','block');
        $('.table-daily_report tbody').css('display','none');
        
     },
      complete: function () {
                            
        $('.table-daily_report tbody').css('display','');
        $('#searchh2').css('display','none');
     },
      success:function(data){
          var html = '';
      
        var i = 0;
        var total = 0;
        var rowspan = 0;
        var grand_total = 0;
        for(var count = 0; count < data.length; count++)
        {
            var bill_amt = parseFloat(data[count].BillAmt);
            var RndAmt = parseFloat(data[count].RndAmt);
            var grand_total = grand_total + RndAmt ;
            RndAmt = parseFloat(RndAmt.toFixed()).toFixed(2);
            html += '<tr>';
              var j = j + 1;
              html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="orderid" style="text-align:center;">'+data[count].ChallanID+'</td>';
          
          html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="date" style="text-align:center;">'+data[count].SalesID+'</td>';
          var date = data[count].Transdate.substring(0, 10)
          var date_new = date.split("-").reverse().join("/");
          
          html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="accountid" style="text-align:center;">'+date_new+'</td>';
          html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="accountname" >'+data[count].AccountID+'</td>';
          html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="accountname" >'+data[count].AccountName+'</td>';
          html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="text-align:right;">'+ RndAmt +'</td>';
          html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="closebalamt" style="text-align:center;">N</td>';
          html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="orderamt"></td>';
          
          html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="cancel"></td>';
          html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="remark" ></td>';
          //html += '<td><button type="button" name="delete_btn" id="'+data[count].OrderID+'" class="btn btn-xs btn-danger btn_delete"><span class="glyphicon glyphicon-remove"></span></button></td></tr>';
          html += '</tr>';
          var challan_id = data[count].ChallanID
          
          if(data[count].ChallanID == challan_id){
              var i = i + 1;
          }
          
          if(data[count].Count_number>1){
              
              if(data[count].Count_number == i){
                 
                  html += '<tr>';
                  html += '<td></td>';
                  html += '<td></td>';
                  html += '<td></td>';
                  html += '<td></td>';
                  html += '<td align="right">Total</td>';
                  html += '<td style="text-align:right;">'+data[count].Total_number+'</td>';
                  html += '<td></td>';
                  html += '<td></td>';
                  html += '<td></td>';
                  html += '<td></td>';
                  html += '</tr>';
                  var i = 0;
              }
              
          
          }else {
              var i = 0;
          }
        }
        
            html += '<tr>';
            html += '<td></td>';
            html += '<td></td>';
            html += '<td></td>';
            html += '<td></td>';
            html += '<td align="right">Total '+data.length+' rows Grand Total</td>';
            html += '<td style="text-align:right;">'+ parseFloat(grand_total.toFixed()).toFixed(2)+'</td>';
            html += '<td></td>';
            html += '<td></td>';
            html += '<td></td>';
            html += '<td></td>';
            html += '</tr>';
        
        $('tbody').html(html);
      }
    });
  }
 $('#search_data').on('click',function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var msg = "Sales Report "+from_date +" To " + to_date;
	    $(".report_for").text(msg);
        load_data(from_date,to_date);
        
 });

  
  
});

 $("#caexcel").click(function(){
  
    var from_date = $("#from_date").val();
	var to_date = $("#to_date").val();
	    
	    $.ajax({
            url:"<?php echo admin_url(); ?>sale_reports/export_daily_sale",
            method:"POST",
            data:{from_date:from_date, to_date:to_date},
            beforeSend: function () {
                $('#searchh3').css('display','block');
                
            },
            complete: function () {
                
                $('#searchh3').css('display','none');
            },
            success:function(data){
                response = JSON.parse(data);
                window.location.href = response.site_url+response.filename;
            }
        });
});


</script>
<script type="text/javascript">
 function printPage(){
        
         var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
    var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="9"><?php echo $PlantDetail->FIRMNAME; ?></td></tr><tr><td style="text-align:center;" colspan="9"><?php echo $PlantDetail->ADDRESS1.' '.$PlantDetail->ADDRESS2; ?></td></tr>';
         heading_data += '<tr>';
         heading_data += '<td style="text-align:center;"colspan="9">Sales Report : '+from_date+' To '+to_date+'</td>';
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