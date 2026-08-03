<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
			echo form_open($this->uri->uri_string(),array('id'=>'pur_order-form','class'=>'_transaction_form'));
			
			?>
		<div class="col-md-12">
        <div class="panel_s accounting-template estimate">
        <div class="row">
        <div class="col-md-12"> 
        <div class="panel-body">
            
            <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Transactions</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Item Issue</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">

      
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="general_infor">
                <div class="row">
                    <div class="col-md-2">
				    <?php
                        $selected_company = $this->session->userdata('root_company');
                        $fy = $this->session->userdata('finacial_year');
                        $new_purchase_orderNumbar = get_option('next_issue_number_for_kirti');
						$prefix = "IO";
						$__number = $new_purchase_orderNumbar;
						$prefix = $prefix.'<span id="prefix_year">'.$fy.'</span>';
                        $_production_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                    ?> 
					    <div class="form-group">
                            <label for="issue_orderid">Issue.No.</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <?php echo $prefix; ?>
                                </span>
                                <input type="text" name="issue_orderid" id="issue_orderid" class="form-control" value="<?php echo ($_is_draft) ? 'DRAFT' : $_production_number; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" <?php echo ($_is_draft) ? 'disabled' : '' ?>>
                            </div>
                        </div>
						
                    </div>   
                          
                    <div class="col-md-2">
                        <?php
                            $fy = $this->session->userdata('finacial_year');
                            $fy_new  = $fy + 1;
                            $lastdate_date = '20'.$fy_new.'-03-31';
                            $curr_date = date('Y-m-d');
                            $curr_date_new    = new DateTime($curr_date);
                            $last_date_yr = new DateTime($lastdate_date);
                            if($last_date_yr < $curr_date_new){
                                $date = $lastdate_date;
                            }else{
                                $date = date('Y-m-d');
                            }
                        ?>
                           <?php $order_date = (isset($pur_order) ? _d($pur_order->order_date) : _d($date));
                                echo render_date_input('issue_date','Document Date',$order_date); ?>
                        </div>
                        
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="CenterID">Center</label>
                                <select name="CenterID" id="CenterID" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                                    <option value=""></option>
                                <?php foreach($CenterList as $s) { ?>
                                    <option value="<?php echo html_entity_decode($s['CenterID']); ?>" ><?php echo html_entity_decode($s['CenterName']); ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                          <div class="form-group">
                            <label for="WHID">Warehouse</label>
                            <select name="WHID" id="WHID" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                                <option value=""></option>
                            </select>
                          </div>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control"></textarea>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-1">
                          <br>
                          <span></span><a href="#" class="btn btn-warning edit-new-order">View List</a>
                        </div>
                    </div>
                    
                   
                    
              </div>
            </div>
        </div>
        </div>
        </div>
        <div class="panel-body mtop10">
        <div class="row col-md-12">
            <p class="bold p_style">Issue Item Details</p>
            <hr class="hr_style"/>
            <div class="" id="example">
            </div>
            <?php echo form_hidden('pur_order_detail'); ?>
        
            <div class="col-md-6 ">
            <table class="table">
               <tbody>
                    <tr id="total_td">
                        <td id="total_td">
                            <label for="total_issue_qty">Total Issue Qty(MT)</label> 
                            <input type="text" readonly class="form-control text-right" name="total_issue_qty" id="total_issue_qty" value="0.00">
                        </td>
                        <td id="total_td">
                            <label for="total_issue_amt">Total Issue Amt</label> 
                            <input type="text" readonly class="form-control text-right" name="total_issue_amt" id="total_issue_amt" value="0.00">
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
        </div>
        <div class="row">
          <div class="col-md-12 mtop15">
             <div class="panel-body bottom-transaction">
                
                <div id="vendor_data">
                  
                </div>

                <div class="btn-bottom-toolbar text-right" style="width: 100%;">
                  <?php if (has_permission_new('ItemIssue', '', 'create')){
                      ?>
                      <button type="button"  class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit">
                      <?php echo _l('submit'); ?>
                      </button>
                  <?php
                  }?>
                </div>
             </div>
               <div class="btn-bottom-pusher"></div>
          </div>
        </div>
        </div>
			</div>
			<?php echo form_close(); ?>
			
		</div>
	</div>
</div>
</div>

<div class="modal fade" id="transfer-modal">
   <div class="modal-dialog modal-xl" style=" max-width: 1230px;">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">order List</h4>
         </div>
         
         
         <div class="modal-body" style="padding:5px;">
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
            <div class="row">
                
                <div class="col-md-3">
                    <?php
                   echo render_date_input('from_date','From',$from_date);
                   ?>
                </div>
                <div class="col-md-3">
                    <?php
                   echo render_date_input('to_date','To',$to_date);
                   ?>
                </div>
                <div class="col-md-3">
                    <br>
                    <button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button>
                </div>
                <div class="col-md-3">
                    <br>
                    <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
                </div>
                <div class="col-md-12">
                 
            <div class="table_purchase_report">
             
              <table class="tree table table-striped table-bordered table_purchase_report" id="table_purchase_report" width="100%">
                  
                <thead>
                    <tr style="display:none;">
                      <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                  </tr>
                  <tr>
                    <th style="width:1% ">BT</th>
                    <th style="width:7% ">PurchID</th>
                    <th style="width:5% ">Date</th>
                    <th style="width:15% text-align:left;">PurchasedForm</th>
                    <th style="width:5% text-align:left;">InvoceNo</th>
                    <th style="width:5% text-align:left;">Date</th>
                    <th style="width:5% text-align:left;">Purchamt</th>
                    <th style="width:3% text-align:left;">Dsc</th>
                    <th style="width:5% text-align:left;">CGSTAmt</th>
                    <th style="width:5% text-align:left;">SGSTAmt</th>
                    <th style="width:5% text-align:left;">IGSTAmt</th>
                    <th style="width:5% text-align:left;">TCSAmt</th>
                    <th style="width:5% text-align:left;">FrtAmt</th>
                    <th style="width:5% text-align:left;">OthAmt</th>
                    <th style="width:5% text-align:left;">Invamt</th>
                    
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>   
            </div>
            <span id="searchh2" style="display:none;">
                                Loading.....
                            </span>
                    
                </div>
              </div>
            
              
         </div>
        
      </div>
   </div>
</div>
<?php init_tail(); ?>

</body>
<script type="text/javascript">
   $('#tcs_pre_data').on('keypress',function (event) {
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
    var input = $(this).val();
    if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
        event.preventDefault();
    }
});

    $('#CenterID').on('change', function() {
		var CenterID = $(this).val();
		var url = "<?php echo base_url(); ?>admin/order/GetWHListByCenterID";
        jQuery.ajax({
            type: 'POST',
            url:url,
            data: {CenterID: CenterID},
            dataType:'json',
            success: function(data) {
                $("#WHID").find('option').remove();
                $("#WHID").selectpicker("refresh");
                $("#WHID").append(new Option('', 'select center'));
                for (var i = 0; i < data.length; i++) {
                    $("#WHID").append(new Option(data[i].w_name, data[i].AccountID));
                }
                $('.selectpicker').selectpicker('refresh');
            }
        });
	});
	
	$('#TDSCode').on('change', function() {
		var TDSCode = $(this).val();
		var url = "<?php echo base_url(); ?>admin/purchase/GetTdsPercentage";
        jQuery.ajax({
            type: 'POST',
            url:url,
            data: {TDSCode: TDSCode},
            dataType:'json',
            success: function(data) {
                $("#tds_rate").val(data.rate);
            }
        });
	});
	
	
    
</script>
<style>
    .table_purchase_report { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
.table_purchase_report thead th { position: sticky; top: 0; z-index: 1; }
.table_purchase_report tbody th { position: sticky; left: 0; }

/* Just common table stuff. Really. */
.table_purchase_report table  { border-collapse: collapse; width: 100%; }
.table_purchase_report th, td { padding: 3px 3px !important; white-space: nowrap;font-size:11px; line-height:1.42857143;vertical-align: middle;}
.table_purchase_report th     { background: #50607b;color: #fff !important; }


#table_purchase_report tr:hover {
    background-color: #ccc;
}

#table_purchase_report td:hover {
    cursor: pointer;
}
</style>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
 
  function load_data(from_date,to_date)
  {
    $.ajax({
      url:"<?php echo admin_url(); ?>purchase/load_data_for_purchase",
      dataType:"JSON",
      method:"POST",
      data:{from_date:from_date, to_date:to_date},
      beforeSend: function () {
               
        $('#searchh2').css('display','block');
        $('.table_purchase_report tbody').css('display','none');
        
     },
      complete: function () {
                            
        $('.table_purchase_report tbody').css('display','');
        $('#searchh2').css('display','none');
     },
      success:function(data){
        var html = '';
      
        for(var count = 0; count < data.length; count++)
        {
           
          var url = "'<?php echo admin_url() ?>purchase/order_list/"+data[count].PurchID+"'";
        html += '<tr onclick="location.href='+url+'">';
        html += '<td style="text-align:center;">'+data[count].BT+'</td>';
        html += '<td style="text-align:center;">'+data[count].PurchID+'</td>';
        var date = data[count].Transdate.substring(0, 10)
        var date_new = date.split("-").reverse().join("/");
          
          html += '<td  style="text-align:center;">'+date_new+'</td>';
          html += '<td >'+data[count].AccountName+'</td>';
          html += '<td  style="text-align:left;">'+data[count].Invoiceno +'</td>';
          if(data[count].Invoicedate == null){
              date_new2 = "";
          }else{
                var date2 = data[count].Invoicedate.substring(0, 10)
                var date_new2 = date2.split("-").reverse().join("/");
          }
          
          html += '<td  style="text-align:center;">'+date_new2+'</td>';
          html += '<td style="text-align:right;">'+data[count].Purchamt+'</td>';
          html += '<td >'+data[count].Discamt+'</td>';
          html += '<td >'+data[count].cgstamt+'</td>';
          html += '<td >'+data[count].sgstamt+'</td>';
          html += '<td >'+data[count].igstamt+'</td>';
          html += '<td >'+data[count].tcsAmt+'</td>';
          html += '<td >'+data[count].Frtamt+'</td>';
          html += '<td >'+data[count].Othamt+'</td>';
          html += '<td >'+data[count].Invamt+'</td>';
          html += '</tr>';
        }
         $('.table_purchase_report tbody').html(html);
      
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
</script>

<script>
    function myFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("table_contra_report");
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
<script>
    $('.add-new-transfer').on('click', function(){
    $('#transfer-modal').find('button[type="submit"]').prop('disabled', false);
      $('#transfer-modal').modal('show');
      init_journal_entry_table();
    });
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
   
    $('#issue_date').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false
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
</html>
<?php require 'ItemIssue_js.php';?>

