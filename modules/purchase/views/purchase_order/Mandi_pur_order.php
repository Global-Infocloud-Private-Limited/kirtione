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
    							<li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Mandi Purchase Order</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
      
           <?php
                  $customer_custom_fields = false;
                  if(total_rows(db_prefix().'customfields',array('fieldto'=>'pur_order','active'=>1)) > 0 ){
                       $customer_custom_fields = true;
                  }
                   ?>
            <div class="tab-content">
                <?php if($customer_custom_fields) { ?>
                 <div role="tabpanel" class="tab-pane" id="custom_fields">
                    <?php $rel_id=( isset($pur_order) ? $pur_order->id : false); ?>
                    <?php echo render_custom_fields( 'pur_order',$rel_id); ?>
                 </div>
                <?php } ?>
                <div role="tabpanel" class="tab-pane active" id="general_infor">
                <div class="row">
                    <div class="col-md-2">
				    <?php
                        $selected_company = $this->session->userdata('root_company');
                        $fy = $this->session->userdata('finacial_year');
                        
                        $new_purchase_orderNumbar = get_option('next_mandi_purchase_number_for_kirti');
                        $format = get_option('invoice_number_format');
               
						$prefix = "MPO";
						$__number = $new_purchase_orderNumbar;
						$prefix = $prefix.'<span id="prefix_year">'.$fy.'</span>';
                        $_production_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                    ?> 
					    <div class="form-group">
                            <label for="pro_orderid">PO.No.</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <?php echo $prefix; ?>
                                </span>
                                <input type="text" name="pro_orderid" id="pro_orderid" class="form-control receiptsid" value="<?php echo $_production_number; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" <?php echo ($_is_draft) ? 'disabled' : '' ?>>
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
                        if($OrderDetails){
                            $CenterID = $OrderDetails->CenterID;
                            $WHID = $OrderDetails->WHID;
                            $TDS = $OrderDetails->TDS;
                            $tds_per = $OrderDetails->tds_per;
                            $ItemID = $OrderDetails->ItemID;
                            $Vehicle = $OrderDetails->vehicle_no;
                        }
                    ?>
                        <input type="hidden" name="po_order" id="po_order" value="<?php echo $OrderDetails->PurchID;?>">
                       <?php $order_date = (isset($OrderDetails) ? _d($OrderDetails->TransDate) : _d($date));
                            echo render_date_input('prd_date','Document Date',$order_date); ?>
                    </div>
                        
                        
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="CenterID">Center</label>
                            <select name="CenterID" id="CenterID" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                                <option value=""></option>
                            <?php foreach($CenterList as $s) { ?>
                                <option value="<?php echo html_entity_decode($s['CenterID']); ?>" <?php if($CenterID == $s['CenterID']){ echo 'selected';} ?> ><?php echo html_entity_decode($s['CenterName']); ?></option>
                            <?php } ?>
                            </select>
                        </div>
                    </div>
                        
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="WHID">Warehouse</label>
                        <select name="WHID" id="WHID" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                            
                        <?php
                            foreach($WHListByCenterID as $key=>$val){
                                ?>
                                    <option value="<?php echo $val["AccountID"]?>" <?php if($val["AccountID"] == $WHID){ echo 'selected';}?>><?php echo $val["w_name"];?></option>
                                <?php
                            }
                        ?>
                        </select>
                      </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="ItemID">ItemID</label>
                            <select name="ItemID" id="ItemID" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                                <option value=""></option>
                            <?php 
                                foreach($ItemList as $key=>$value){ ?>
                                    <option value="<?php echo $value['ItemID']; ?>" <?php if($ItemID == $value['ItemID']){ echo 'selected';} ?>><?php echo $value['ItemName']; ?></option>
                            <?php 
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                        
                    <div class="col-md-2">
                        <div class="form-group">
                            <input type="hidden" name="tds_rate" id="tds_rate" value="<?php echo $tds_per;?>">
                            <label for="TDSCode">TDS Code</label>
                            <select name="TDSCode" id="TDSCode" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                                <option value=""></option>
                            <?php foreach($TDSList as $tds) { ?>
                                <option value="<?php echo html_entity_decode($tds['TDSCode']); ?>" <?php if($TDS == $tds['TDSCode']){ echo 'selected';} ?>><?php echo html_entity_decode($tds['TDSName']); ?></option>
                            <?php } ?>
                            </select>
                        </div>
                    </div>
                        
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="vehicle_no">Vehicle No.</label>
                            <input type="text" name="vehicle_no" id="vehicle_no" class="form-control" value="<?php echo $Vehicle;?>">
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
        <p class="bold p_style"><?php echo _l('pur_order_detail'); ?></p>
        <hr class="hr_style"/>
         <div class="" id="example">
         </div>
         <?php echo form_hidden('pur_order_detail'); ?>
        
         <div class="col-md-12 ">
            <table class="table">
               <tbody>
                    <tr id="total_td">
                        <td id="total_td">
                            <label for="total_qty_in_qtl">Qty(Quintal)</label> 
                            <input type="text" readonly class="form-control text-right" name="total_qty_in_qtl" id="total_qty_in_qtl" value="<?php echo $OrderDetails->OrderQty;?>">
                        </td>
                        <td class="td_style">
                            <label  for="Total_value">Total Value</label>  
                            <input  type="text" readonly class="form-control text-right" name="Total_value" id="Total_value"  value="<?php echo $OrderDetails->PurchAmt;?>" >
                        </td>
                        
                        <td>  
                            <label  for="total_brokerage">Total Brokerage</label>  
                            <input type="text" readonly value="<?php echo $OrderDetails->BrokerAmt;?>" class="form-control pull-left text-right"  name="total_brokerage">
                        </td>
                        <td>  
                            <label  for="total_mrkt_levy">Total Market Levy</label>
                            <input type="text" readonly value="<?php echo $OrderDetails->MrktLevyAmt;?>" class="form-control pull-left text-right" name="total_mrkt_levy" id="total_mrkt_levy">
                        </td>
                        <td>  
                            <label  for="total_gross_value">Total Gross Value</label> 
                            <input type="text" readonly value="<?php echo $OrderDetails->GrossAmt;?>" class="form-control pull-left text-right" name="total_gross_value">
                        </td>
                        <td>  
                            <label  for="total_tds">TDS</label>  
                                <input type="text" readonly value="<?php echo $OrderDetails->tdsAmt;?>" class="form-control pull-left text-right" id="total_tds" name="total_tds">
                        </td>
                        <td>  
                            <label  for="total_net_value">Total NetValue</label>  
                            <input type="text" readonly value="<?php echo $OrderDetails->InvAmt;?>" class="form-control pull-left text-right" name="total_net_value">
                        </td>
                    </tr>
                    
                    

               </tbody>
            </table>
         </div> 
         
        </div>
        </div>
        <div class="row">
            <div class="col-md-12 mtop15">
                <div class="btn-bottom-toolbar text-right" style="width: 100%;">
                  <?php if (has_permission_new('mandi_purchase_order', '', 'create')){
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
            <h4 class="modal-title">Mandi Purchase List</h4>
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
                <div class="col-md-2">
                    <?php
                        echo render_date_input('from_date','From',$from_date);
                    ?>
                </div>
                <div class="col-md-2">
                    <?php
                        echo render_date_input('to_date','To',$to_date);
                    ?>
                </div>
                <div class="col-md-2">
                    <br>
                    <button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button>
                </div>
                <div class="col-md-6" style="margin-top:1%;">
                    <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search.." title="Type in a name" style="float: right;">
                </div>
                
                <div class="col-md-12">
                    <span id="searchh2" style="display:none;">Loading.....</span>
                    <div class="table_purchase_report">
                        <table class="tree table table-striped table-bordered table_purchase_report" id="table_purchase_report" width="100%">
                  
                            <thead>
                                <tr style="display:none;">
                                    <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                </tr>
                                <tr>
                                    <th style="width:7% ">PO Number</th>
                                    <th style="width:8% ">Purchase Date</th>
                                    <th style="width:10% text-align:left;">Item Name</th>
                                    <th style="width:7% text-align:left;">Qty(MT)</th>
                                    <th style="width:15% text-align:left;">Purchase For</th>
                                    <th style="width:10% text-align:left;">Center</th>
                                    <th style="width:7% text-align:left;">Purch Amt</th>
                                    <th style="width:7% text-align:left;">Broker Amt</th>
                                    <th style="width:7% text-align:left;">MrktLevy Amt</th>
                                    <th style="width:7% text-align:left;">Gross Amt</th>
                                    <th style="width:5% text-align:left;">TDS Amt</th>
                                    <th style="width:10% text-align:left;">Inv Amt</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>   
                    </div>
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
      url:"<?php echo admin_url(); ?>purchase/load_mandi_purchase_list",
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
           
            var url = "'<?php echo admin_url() ?>purchase/mandi_pur_order/"+data[count].PurchID+"'";
            html += '<tr onclick="location.href='+url+'">';
            html += '<td style="text-align:center;">'+data[count].PurchID+'</td>';
            var date = data[count].TransDate.substring(0, 10)
            var date_new = date.split("-").reverse().join("/");
          
            html += '<td style="text-align:center;">'+date_new+'</td>';
            html += '<td style="text-align:left;">'+data[count].ItemName+'</td>';
            html += '<td style="text-align:right;">'+data[count].OrderQty+'</td>';
            html += '<td style="text-align:left;">'+data[count].PlantName +'</td>';
            html += '<td style="text-align:left;">'+data[count].CenterName+'</td>';
            html += '<td style="text-align:right;">'+data[count].PurchAmt+'</td>';
            html += '<td style="text-align:right;">'+data[count].BrokerAmt+'</td>';
            html += '<td style="text-align:right;">'+data[count].MrktLevyAmt+'</td>';
            html += '<td style="text-align:right;">'+data[count].GrossAmt+'</td>';
            html += '<td style="text-align:right;">'+data[count].tdsAmt+'</td>';
            html += '<td style="text-align:right;">'+data[count].InvAmt+'</td>';
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
        table = document.getElementById("table_purchase_report");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) {
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
        td10 = tr[i].getElementsByTagName("td")[10];
        td11 = tr[i].getElementsByTagName("td")[11];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          }else if (td1) {
          txtValue = td1.textContent || td1.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          }else if (td2) {
          txtValue = td2.textContent || td2.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          }else if (td3) {
          txtValue = td3.textContent || td3.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          }else if (td4) {
          txtValue = td4.textContent || td4.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          }else if (td5) {
          txtValue = td5.textContent || td5.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          }else if (td6) {
          txtValue = td6.textContent || td6.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          }else if (td7) {
          txtValue = td7.textContent || td7.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          }else if (td8) {
          txtValue = td8.textContent || td8.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          }else if (td9) {
          txtValue = td9.textContent || td9.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          }else if (td10) {
          txtValue = td10.textContent || td10.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          }else if (td11) {
          txtValue = td11.textContent || td11.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }}}}}}}}}}}}
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
   
    $('#prd_date').datetimepicker({
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
<?php require 'modules/purchase/assets/js/mandi_pur_order_js.php';?>

