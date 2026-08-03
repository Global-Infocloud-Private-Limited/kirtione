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
					<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
					<li class="breadcrumb-item active" aria-current="page"><b>FPO Order</b></li>
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
                        
                        $new_FPO_orderNumbar = get_option('next_FPO_number_for_kirti');
                        $format = get_option('invoice_number_format');
               
						$prefix = "FPO";
						$__number = $new_FPO_orderNumbar;
						$prefix = $prefix.'<span id="prefix_year">'.$fy.'</span>';
                        $_FPO_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                        
                        $edit_orderID = $OrderDetails->OrderID;
                        $ExistOrderId = substr($edit_orderID, 5);
                    ?> 
					    <div class="form-group">
                            <label for="pro_orderid">PO.No.</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <?php echo $prefix; ?>
                                </span>
                                <input type="text" name="pro_orderid" id="pro_orderid" class="form-control receiptsid" value="<?php echo !empty($ExistOrderId) ? $ExistOrderId : $_FPO_number; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" <?php echo ($edit_orderID) ? 'disabled' : '' ?>>
                                <input type="hidden" name="po_no" id="po_no" 
                                    value="<?php echo htmlspecialchars($prefix . $_FPO_number, ENT_QUOTES, 'UTF-8'); ?>">
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
                        $display = '';
                        if($OrderDetails){
                            $FPOID = $OrderDetails->FPOID;
                            $display = "disabled";
                            $ItemID = $OrderDetails->ItemID;
                            $Vehicle = $OrderDetails->VehicleNo;
                            $FpoRate = $OrderDetails->FpoRate;
                            $CenterIDs = $OrderDetails->CenterID;
                        }
                    ?>
                        <input type="hidden" name="po_order" id="po_order" value="<?php echo $OrderDetails->OrderID;?>">
                       <?php $order_date = isset($OrderDetails) ? explode(' ', _d($OrderDetails->Transdate))[0] : explode(' ', _d($date))[0];
                            echo render_date_input('FPO_Date','Date',$order_date); ?>
                    </div>
                    
                    <?php 
                        $isItemSelected = !empty($ItemID) ? 'disabled' : '';
                        $isFpoSelected = !empty($FpoList) ? 'disabled' : '';
                        $isCenterSelected = !empty($CenterIDs) ? 'disabled' : '';
                    ?>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="CenterID">Center List</label>
                            <select name="CenterID" id="CenterID" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" <?= $isCenterSelected ?>>
                                <option value=""></option>
                            <?php foreach($CenterList as $s) { ?>
                                <option value="<?php echo html_entity_decode($s['CenterID']); ?>" <?php if($CenterIDs == $s['CenterID']){ echo 'selected';} ?>><?php echo html_entity_decode($s['CenterName']); ?></option>
                            <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="ItemID">ItemID</label>
                            <select name="ItemID" id="ItemID" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" <?= $isItemSelected ?>>
                                <option value=""></option>
                            <?php 
                                foreach($ItemList as $key=>$value){ ?>
                                    <option value="<?php echo $value['ItemID']; ?>" <?php if($ItemID == $value['ItemID']){ echo 'selected';} ?>><?php echo $value['ItemName']; ?></option>
                            <?php 
                                }
                            ?>
                            </select>
                            
                            <input type="hidden" name="itemid" id="itemid" value="<?php echo $ItemID;?>">
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="fpolist">FPO List</label>
                        <select name="fpolist" id="fpolist"  <?php echo $display;?> class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" <?= $isFpoSelected ?>>
                        <option value=""></option>
                        <?php
                            foreach($FPOStaffList as $key=>$val){
                                ?>
                                    <option value="<?php echo $val["AccountID"]?>" <?php if($FPOID == $val["AccountID"]) echo 'selected';?> ><?php echo $val["firstname"]." ".$val["lastname"];?></option>
                                <?php
                            }
                        ?>
                        </select>
                        <input type="hidden" name="fpo_list" id="fpo_list" value="<?php echo $FPOID;?>">
                      </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="rate">Rate</label>
                            <input type="text" name="rate" id="rate" class="form-control" value="<?php echo $FpoRate;?>" readonly>
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
        <p class="bold p_style"><?php echo _l('FPO Order Detail'); ?></p>
        <hr class="hr_style"/>
         <div class="" id="example">
         </div>
         <?php echo form_hidden('pur_order_detail'); ?>
         <input type="hidden" name="dynamic_param_data" />
        
         <div class="col-md-12 ">
            <table class="table">
               <tbody>
                    <tr id="total_td">
                        <td id="total_td">
                            <label for="total_net_wgt">Total Net Weight</label> 
                            <input type="text" readonly class="form-control text-right" name="total_net_wgt" id="total_net_wgt" value="<?php echo $OrderDetails->OrderQty;?>">
                        </td>
                        <td class="td_style">
                            <label  for="Total_bag">Total Bag</label>  
                            <input  type="text" readonly class="form-control text-right" name="Total_bag" id="Total_bag"  value="<?php echo $OrderDetails->PurchAmt;?>" >
                        </td>
                        
                        <td>  
                            <label  for="total_tent_wgt">Total Tentative Weight</label>  
                            <input type="text" readonly value="<?php echo $OrderDetails->BrokerAmt;?>" class="form-control pull-left text-right"  name="total_tent_wgt" id="total_tent_wgt">
                        </td>
                        <td>  
                            <label  for="total_amt">Total Amount</label>
                            <input type="text" readonly value="<?php echo $OrderDetails->MrktLevyAmt;?>" class="form-control pull-left text-right" name="total_amt" id="total_amt">
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
                    <?php
                    $isDisabled = (!empty($IsExistDispatch)) ? 'disabled' : '';
                
                    // Create mode
                    if (empty($edit_orderID) && has_permission_new('Fpo_Order_Form', '', 'create')) {
                    ?>
                        <button type="button" <?= $isDisabled; ?> class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit">
                            <?= _l('submit'); ?>
                        </button>
                    <?php
                    }
                
                    // Edit mode
                    if (!empty($edit_orderID) && has_permission_new('Fpo_Order_Form', '', 'edit')) {
                    ?>
                        <button type="button" <?= $isDisabled; ?> class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit">
                            <?= _l('update'); ?>
                        </button>
                    <?php
                    }
                    ?>
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
            <h4 class="modal-title">Fpo Order List</h4>
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
                                    <th style="width:7% ">Order No.</th>
                                    <th style="width:8% ">Order Date</th>
                                    <th style="width:15% text-align:left;">FPO Name</th>
                                    <th style="width:10% text-align:left;">Party Name</th>
                                    <th style="width:10% text-align:left;">Item Name</th>  
                                    <th style="width:7% text-align:left;">Rate</th>
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
    $('#ItemID').on('change', function() {
      const itemID = $(this).val();
      const Fpolist = $('#fpolist').val();
      const CenterID = $('#CenterID').val();
      
      if (!itemID) {
        if (window.hot) window.hot.destroy();
        $('#example').empty().hide();
      } else {
        $('#example').show();
        fetchItemParameters(itemID);
      }
        
       FetchRates(Fpolist,itemID,CenterID);
    });
    
    $('#fpolist').on('change', function() {
        const Fpolist = $(this).val();
        const ItemID = $('#ItemID').val();
        const CenterID = $('#CenterID').val();
        
        FetchRates(Fpolist,ItemID,CenterID);
    });
    
    $('#CenterID').on('change', function() {
        const CenterID = $(this).val();
        const ItemID = $('#ItemID').val();
        const Fpolist = $('#fpolist').val();
        
        FetchRates(Fpolist,ItemID,CenterID);
    });  
    
    function FetchRates(Fpolist,ItemID,CenterID)
    {
        $.ajax({
              url:"<?php echo admin_url(); ?>FpoOrder/FetchRate",
              dataType:"JSON",
              method:"POST",
              data:{Fpolist:Fpolist,ItemID:ItemID,CenterID:CenterID},
              beforeSend: function () {
                $('#searchh2').css('display','block');
             },
              complete: function () {
                $('#searchh2').css('display','none');
             },
            success: function(data){
                $('#rate').val(data.Rate);
            }
        });
    }
    
    $('.save_detail').on('click', function() {
        var CenterID =  $('#CenterID').val();
        var ItemID = $('#ItemID').val();
        var FpoList = $('#fpolist').val();
        var Rate = $('#rate').val();
        
        if(CenterID == ''){
            alert("Please select Center.")
            return false;
        }else if(ItemID == '')
        {
            alert("Please select Item.")
            return false;
        }
        else if(FpoList == '')
        {
            alert("Please select FPO.")
            return false;
        }else if(Rate == ''){
            alert("Rate Should not be null.");  
            return false;
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
      const itemSelect = document.getElementById('ItemID');
      const selectedItem = itemSelect ? itemSelect.value : null;
    
      if(selectedItem) {
        fetchItemParameters(selectedItem);
      }
      
      itemSelect.addEventListener('change', function() {
        fetchItemParameters(this.value);
      });
    });
</script>

<script>
    function fetchItemParameters(itemID) {
      $.ajax({
        url: "<?php echo admin_url(); ?>FpoOrder/GetItemParameters",
        method: "POST",
        dataType: "JSON",
        data: { itemID: itemID },
        success(data) {
          let dynamicCols = [];
          let dynamicHeaders = [];
    
          if (data.Parameter && Array.isArray(data.Parameter) && data.Parameter.length > 0) {
            const parameters = data.Parameter;
        
            dynamicCols = [];
            dynamicHeaders = [];
            const dynamicParamIDs = []; 
        
            parameters.forEach(param => {
                dynamicCols.push({
                    data: param.ItemParameterName,
                    type: 'numeric',
                    numericFormat: { pattern: '0,00' },
                    width: 80
                });
                dynamicHeaders.push(param.ItemParameterName);
                dynamicParamIDs.push({ id: param.ItemParameterID, isAmount: false });
               
                dynamicCols.push({
                    data: param.ItemParameterName + '_Amt',
                    type: 'numeric',
                    numericFormat: { pattern: '0,00' },
                    width: 80
                });
                dynamicHeaders.push(param.ItemParameterName + ' Amt');
            });
        }
    
          loadHandsontable(dynamicCols, dynamicHeaders);
        },
        error() {
          alert("Failed to load parameters, showing static table.");
          loadHandsontable();
        }
      });
    }

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
        url:"<?php echo admin_url(); ?>FpoOrder/load_fpo_order_data",
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
                var url = "'<?php echo admin_url() ?>FpoOrder/Fpo_Order/"+data[count].OrderID+"'";
                html += '<tr onclick="location.href='+url+'">';
                html += '<td style="text-align:center;">'+data[count].OrderID+'</td>';
                var date = data[count].Transdate.substring(0, 10)
                var date_new = date.split("-").reverse().join("/");
              
                html += '<td style="text-align:center;">'+date_new+'</td>';
                html += '<td style="text-align:left;">'+data[count].FPOName+'</td>';
                html += '<td style="text-align:left;">'+data[count].PlantName+'</td>';
                html += '<td style="text-align:left;">'+data[count].ItemName+'</td>';
                html += '<td style="text-align:left;">'+data[count].FpoRate +'</td>';
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
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.trim();       
        table = document.getElementById("table_purchase_report");
        tr = table.getElementsByTagName("tr"); 

        for (i = 2; i < tr.length; i++) 
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
<script>
    $('.edit-new-order').on('click', function(){
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
   
    $('#FPO_Date').datetimepicker({
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
<?php require 'Fpo_pur_order_js.php';?>

