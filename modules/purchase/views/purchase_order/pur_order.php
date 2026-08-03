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
    							<li class="breadcrumb-item active" aria-current="page"><b>Direct Purchase Order</b></li>
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
                        $prefix = get_purchase_option('pur_order_prefix');
    						$prefix = $prefix.'<span id="prefix_year">'.$fy.'</span>';
                        if(isset($purchase_details)){
                            $PONumber = substr($purchase_details->PurchID,4);
                            $isedit = "Y";
                        }else{
                            $new_purchase_orderNumbar = get_option('next_purchase_number_for_kirti');
    						$__number = $new_purchase_orderNumbar;
                            $PONumber = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                            $isedit = '';
                        }
                        
                    ?> 
					    <div class="form-group">
                            <label for="pro_orderid">PO.No.</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <?php echo $prefix; ?>
                                </span>
                                <input type="text" name="pro_orderid" id="pro_orderid" class="form-control receiptsid" value="<?php echo $PONumber; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $purchase_details->PurchID; ?>" <?php echo ($isedit) ? 'disabled' : '' ?>>
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
                        <?php 
                            $value = (isset($purchase_details) ? _d(substr($purchase_details->Transdate,0,10)) : _d($date));
                            echo render_date_input('prd_date','Document Date',$value); 
                        ?>
                        </div>
                        
                        
                        <div class="col-md-2">
                            <?php $value = (isset($purchase_details) ? $purchase_details->CenterID : ''); ?>
                            <div class="form-group">
                                <label for="CenterID">Center</label>
                                <select name="CenterID" id="CenterID" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None selected" >
                                    <option value=""></option>
                                <?php foreach($CenterList as $s) { ?>
                                    <option value="<?php echo $s['CenterID']; ?>" <?php if($value == $s['CenterID']){ echo 'selected';}?> ><?php echo html_entity_decode($s['CenterName']); ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <?php $value = (isset($purchase_details) ? $purchase_details->WHID : ''); ?>
                            <div class="form-group">
                                <label for="WHID">Warehouse</label>
                                <select name="WHID" id="WHID" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None selected" >
                                    <option value=""></option>
                                    <?php foreach($WHListByCenterID as $s) { ?>
                                        <option value="<?php echo $s['AccountID']; ?>" <?php if($value == $s['AccountID']){ echo 'selected';}?> ><?php echo html_entity_decode($s['w_name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <?php $value = (isset($purchase_details) ? $purchase_details->BrokerID : ''); ?>
                            <div class="form-group">
                                <label for="BrokerID">Broker</label>
                                <select name="BrokerID" id="BrokerID" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None selected" >
                                    <option value=""></option>
                                <?php 
                                    foreach($AllBrokerList as $key=>$val){ ?>
                                        <option value="<?php echo $val['AccountID']; ?>" <?php if($value == $val['AccountID']){ echo 'selected';}?>><?php echo $val['company']; ?></option>
                                <?php 
                                    }
                                ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <?php $value = (isset($purchase_details) ? $purchase_details->PaymentTerm : ''); ?>
                            <div class="form-group">
                                <label for="PaymentTerm">Payment Terms</label>
                                <select name="PaymentTerm" id="PaymentTerm" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None selected" >
                                    <option value=""></option>
                                <?php 
                                    foreach($PaymentCycleList as $key=>$val){ ?>
                                        <option value="<?php echo $val['CycleID']; ?>" <?php if($value == $val['CycleID']){ echo 'selected';}?>><?php echo $val['CycleName']; ?></option>
                                <?php 
                                    }
                                ?>
                                </select>
                            </div>
                        </div>
                        
                        
                        
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <?php $value = (isset($purchase_details) ? $purchase_details->AccountID : ''); ?>
                            <div class="form-group" app-field-wrapper="vendor">
                                <label for="vendor">Vendor</label>
                                <input type="text" name="vendor" id="vendor"  class="form-control" value="<?php echo $value;?>">
                            </div>
                            <!--<label for="vendor"><?php echo _l('vendor'); ?></label>
                                <select name="vendor" id="vendor" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                                  <option value=""></option>
                                    <?php foreach($vendors as $s) { ?>
                                    <option value="<?php echo html_entity_decode($s['AccountID']); ?>" ><?php echo html_entity_decode($s['company'])." - ".html_entity_decode($s['AccountID']); ?></option>
                                      <?php } ?>
                                </select>  -->
                          </div>

                        <div class="col-md-4">
                            <?php $value = (isset($purchase_details) ? $purchase_details->company : ''); ?>
                            <div class="form-group">
                                <label for="vendorName">Vendor Name</label>
                                <input type="text" readonly="" class="form-control" name="vendorName" id="vendorName" value="<?php echo $value;?>"  aria-invalid="false">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <?php $value = (isset($purchase_details) ? $purchase_details->gstin : ''); ?>
                            <div class="form-group">
                                <label for="gst_num">Vendor GST</label>
                                <input type="text" readonly="" class="form-control" name="gst_num" id="gst_num" value="<?php echo $value;?>"  aria-invalid="false">
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <?php $value = (isset($purchase_details) ? $purchase_details->state : ''); ?>
                            <div class="form-group">
                                <label for="estimate">State</label>
                                <input type="text" readonly="" class="form-control" name="vendor_state" value="<?php echo $value;?>" id="vendor_state"  aria-invalid="false">
                            </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <label for="estimate">Closing Balance</label>
                           <input type="text" readonly="" class="form-control" name="c_balance" id="c_balance"  aria-invalid="false">
                          </div>
                        </div>
                        
                       
                    </div>
                     <div class="row">
                        <div class="col-md-2">
                            <?php $value = (isset($purchase_details) ? $purchase_details->Invoiceno : ''); ?>
                            <div class="form-group">
                                <label for="invoce_n">Vendor Doc No</label>
                                <input type="text"  class="form-control" name="invoce_n" id="invoce_n" value="<?php echo $value;?>" aria-invalid="false">
                            </div>
                        </div>
                        <div class="col-md-2">
                             <?php $value = (isset($purchase_details) ? _d(substr($purchase_details->Invoicedate,0,10)) : _d(date('Y-m-d')));
                            echo render_date_input('invoce_date','Vendor Doc Date',$value); ?>
                        </div>
                        
                        <div class="col-md-2">
                            <?php $value = (isset($purchase_details) ? $purchase_details->invoiceAmt : ''); ?>
                            <div class="form-group">
                                <label for="vendor_invoice_amt">Vendor Doc Amount</label>
                                <input type="text" class="form-control" name="vendor_invoice_amt" id="vendor_invoice_amt" value="<?php echo $value;?>" aria-invalid="false">
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <?php $value = (isset($purchase_details) ? $purchase_details->TDS : ''); ?>
                            <div class="form-group">
                                <input type="hidden" name="tds_rate" id="tds_rate" value="">
                                <label for="TDSCode">TDS Section</label>
                                <select name="TDSCode" id="TDSCode" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None selected" >
                                    <option value=""></option>
                                <?php foreach($TDSList as $tds) { ?>
                                    <option value="<?php echo html_entity_decode($tds['TDSCode']); ?>" <?php if($value == $tds['TDSCode']) echo 'selected';?>><?php echo html_entity_decode($tds['TDSName']); ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <?php $value = (isset($purchase_details) ? $purchase_details->tds_per : ''); ?>
                            <div class="form-group">
                                <label for="tds_rate_avl">TDS %</label>
                                <select name="tds_rate_avl" id="tds_rate_avl" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None selected" >
                                    <option value=""></option>
                                    <?php foreach($TDSRateBySection as $tdsrate) { ?>
                                        <option value="<?php echo html_entity_decode($tdsrate['rate']); ?>" <?php if($value == $tdsrate['rate']) echo 'selected';?>><?php echo $tdsrate['description']." (".$tdsrate['rate']."%)"; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="clearfix">
                        </div>
                        
                        <div class="col-md-2">
                            <?php $value = (isset($purchase_details) ? $LedgerDetails->SubActGroupID1 : ''); ?>
                            <div class="form-group">
                                <label for="PurchaseType">Purchase Type </label>
                                <select name="PurchaseType" id="PurchaseType" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None selected" >
                                    <option value=""></option>
                                    <option value="100007" <?php if($value == "100007") echo 'selected';?>>INVENTORY</option> <!-- Purchase Accounts -->
                                    <option value="100040" <?php if($value == "100040") echo 'selected';?>>FIXED ASSETS</option>
                                    <option value="100041" <?php if($value == "100041") echo 'selected';?>>OTHER PURCHASE</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <?php $value = (isset($purchase_details) ? $LedgerDetails->SubActGroupID : ''); ?>
                            <div class="form-group">
                                <label for="LedgerGrp">Ledger Group</label>
                                <select name="LedgerGrp" id="LedgerGrp" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None selected" >
                                    <option value=""></option>
                                    <?php foreach($LedgerDetails->ActSubGrp2List as $ActGroup2) { ?>
                                        <option value="<?php echo html_entity_decode($ActGroup2['SubActGroupID']); ?>" <?php if($value == $ActGroup2['SubActGroupID']) echo 'selected';?>><?php echo $ActGroup2['SubActGroupName']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <?php $value = (isset($purchase_details) ? $LedgerDetails->AccountID : ''); ?>
                            <div class="form-group">
                                <label for="Ledger">Ledger</label>
                                <select name="Ledger" id="Ledger" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None selected" >
                                    <option value=""></option>
                                    <?php foreach($LedgerDetails->LedgerList as $led) { ?>
                                        <option value="<?php echo html_entity_decode($led['AccountID']); ?>" <?php if($value == $led['AccountID']) echo 'selected';?>><?php echo $led['company']; ?></option>
                                    <?php } ?>
                                </select>
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
                            <label for="PurchaseAmt">Purchase Amt</label>
                            <input type="text" readonly class="form-control text-right" name="total_mn" value="<?php echo $purchase_details->Purchamt;?>">
                        </td>
                        
                        <td id="total_td">
                            <label for="dc_total">Discount Amt</label>
                            <input type="text" readonly class="form-control text-right" name="dc_total"  value="<?php echo $purchase_details->Discamt;?>">
                        </td>
                        
                        <td id="total_td">
                            <label for="CGST_amt">CGST Amt</label>
                            <input type="text" readonly class="form-control text-right" name="CGST_amt"  value="<?php echo $purchase_details->cgstamt;?>">
                        </td>
                        
                        <td id="total_td">
                            <label for="SGST_AMT">SGST Amt</label>
                            <input type="text" readonly class="form-control text-right" name="SGST_AMT"  value="<?php echo $purchase_details->sgstamt;?>">
                        </td>
                        <td id="total_td">
                            <label for="IGST_amt">IGST Amt</label>
                            <input type="text" readonly class="form-control text-right" name="IGST_amt"  value="<?php echo $purchase_details->igstamt;?>">
                        </td>
                        
                        
                        <td id="total_td">
                            <label for="Freight_AMT">Freight Amt</label>
                            <input type="hidden"  id="Freight_AMT_hidden">
                            <input type="text" class="form-control text-right" name="Freight_AMT" id="Freight_AMT"  value="<?php echo $purchase_details->Frtamt;?>">
                        </td>
                        
                        <td id="total_td">
                            <label for="Other_amt">Other Amt</label>
                            <input type="hidden"  id="Other_amt_hidden">
                            <input type="text" class="form-control text-right" name="Other_amt" id="Other_amt"  value="<?php echo $purchase_details->Othamt;?>">
                        </td>
                        
                        <td id="total_td">
                            <label for="Round_OFF">Round Off</label>
                            <input type="text" readonly class="form-control text-right" name="Round_OFF" id="Round_OFF"  value="<?php echo $purchase_details->RoundOffAmt;?>">
                        </td>
                        <td id="total_td">
                            <label for="tdsAmt">TDS Amt</label>
                            <input type="text" readonly class="form-control text-right" name="tdsAmt" id="tdsAmt"  value="<?php echo $purchase_details->tdsAmt;?>">
                        </td>
                        
                        <td id="total_td">
                            <label for="Invoice_amt">Invoice Amt</label>
                            <input type="text" readonly class="form-control text-right" name="Invoice_amt" id="Invoice_amt"  value="<?php echo $purchase_details->Invamt;?>">
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
                  <?php if (has_permission_new('purchase-order', '', 'create')){
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
<style>
/*    @media (min-width: 768px)*/ 
/*        .modal-xl {*/
/*    width: 90%;*/
/*    max-width: 1230px;*/
/*}*/
</style>
<div class="modal fade" id="transfer-modal">
   <div class="modal-dialog modal-xl" style=" max-width: 1230px;">
      <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Direct Purchase Order List</h4>
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
                    <?php echo render_date_input('from_date','From',$from_date); ?>
                </div>
                <div class="col-md-3">
                    <?php echo render_date_input('to_date','To',$to_date);?>
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
                                    <th style="width:5% ">PO Date</th>
                                    <th style="width:15% text-align:left;">Purchased Form</th>
                                    <th style="width:5% text-align:left;">Invoce No</th>
                                    <th style="width:5% text-align:left;">Inv. Date</th>
                                    <th style="width:5% text-align:left;">Purch Amt</th>
                                    <th style="width:3% text-align:left;">Disc Amt</th>
                                    <th style="width:5% text-align:left;">CGST Amt</th>
                                    <th style="width:5% text-align:left;">SGST Amt</th>
                                    <th style="width:5% text-align:left;">IGST Amt</th>
                                    <th style="width:5% text-align:left;">TDS Amt</th>
                                    <th style="width:5% text-align:left;">Frt Amt</th>
                                    <th style="width:5% text-align:left;">Oth Amt</th>
                                    <th style="width:5% text-align:left;">Inv. Amt</th>
                                </tr>
                            </thead>
                            <tbody>
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

</body>
<script type="text/javascript">
   $('#Freight_AMT,#Other_amt').on('keypress',function (event) {
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
    var input = $(this).val();
    if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
        event.preventDefault();
    }
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
                $("#tds_rate_avl").find('option').remove();
                $("#tds_rate_avl").selectpicker("refresh");
                $("#tds_rate_avl").append(new Option('None selected', ''));
                for (var i = 0; i < data.length; i++) {
                    var Label = data[i].description +" ("+data[i].rate+")";
                    $("#tds_rate_avl").append(new Option(Label, data[i].rate));
                }
                $('.selectpicker').selectpicker('refresh');
            }
        });
	});
	/*$('#tds_rate_avl').on('change', function() {
	    var TDSPer = $(this).find('option:selected').text();
	    $("#tds_rate").val(TDSPer);
	});*/
	
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
                $("#WHID").append(new Option('None selected', ''));
                for (var i = 0; i < data.length; i++) {
                    $("#WHID").append(new Option(data[i].w_name, data[i].AccountID));
                }
                $('.selectpicker').selectpicker('refresh');
            }
        });
	});
	// Get Ledger Group List
	$('#PurchaseType').on('change', function() {
		var PurchaseType = $(this).val();
		var url = "<?php echo base_url(); ?>admin/purchase/GetLedgerGroupList";
        jQuery.ajax({
            type: 'POST',
            url:url,
            data: {PurchaseType: PurchaseType},
            dataType:'json',
            success: function(data) {
                $("#LedgerGrp").find('option').remove();
                $("#LedgerGrp").selectpicker("refresh");
                $("#LedgerGrp").append(new Option('None selected', ''));
                for (var i = 0; i < data.length; i++) {
                    $("#LedgerGrp").append(new Option(data[i].SubActGroupName, data[i].SubActGroupID));
                }
                $('.selectpicker').selectpicker('refresh');
            }
        });
	});
	
	// Get Ledger List
	$('#LedgerGrp').on('change', function() {
		var LedgerGrp = $(this).val();
		var url = "<?php echo base_url(); ?>admin/purchase/GetLedgerList";
        jQuery.ajax({
            type: 'POST',
            url:url,
            data: {LedgerGrp: LedgerGrp},
            dataType:'json',
            success: function(data) {
                $("#Ledger").find('option').remove();
                $("#Ledger").selectpicker("refresh");
                $("#Ledger").append(new Option('None selected', ''));
                for (var i = 0; i < data.length; i++) {
                    $("#Ledger").append(new Option(data[i].company, data[i].AccountID));
                }
                $('.selectpicker').selectpicker('refresh');
            }
        });
	});
	
	$( "#vendor" ).autocomplete({
        source: function( request, response ) {
          $.ajax({
            url: "<?=base_url()?>admin/Cd_notes/accountlist",
            type: 'post',
            dataType: "json",
            data: {
              search: request.term
            },
            success: function( data ) {
              response( data );
            }
          });
        },
        select: function (event, ui) {
          var old_AccountID = $('#old_act_name').val();
            if(empty(old_AccountID)){
                $('#vendor').val(ui.item.value);
                $('#vendorName').val(ui.item.label); 
                $("#invoce_n").focus();
                return false; 
            }else{
              $('#act_name').val(ui.item.value);
                return false
            }
        }
    });
    
    $('#vendor').on('blur', function () {
        var AccountID = $(this).val();
        var CenterID = $('#CenterID').val();
        if(empty(AccountID)){
            
        }else{
            $.ajax({
                url: "<?=base_url()?>admin/Cd_notes/GetAccountDetailsByID",
                type: 'post',
                dataType: "json",
                data: {
                  AccountID: AccountID,
                },
                success: function( data ) {
                    if(empty(data)){
                        alert('Account not found.');
                        $("#vendor").val('');
                        $("#vendorName").focus();
                    }else{
                        $('#vendorName').val(data.AccountName); // display the selected text
                        $('#vendor_state').val(data.state_short_code); // display the selected text
                        if(data.AccountType == "1"){
                            $('#gst_num').val('');
                        }else{
                            $('#gst_num').val(data.GST_Aadhar);
                        }
                        $("#invoce_n").focus();
                    }
                }
            });
        }
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
 
  function load_data(from_date,to_date,POType)
    {
        $.ajax({
            url:"<?php echo admin_url(); ?>purchase/load_data_for_purchase",
            //dataType:"JSON",
            method:"POST",
            data:{from_date:from_date, to_date:to_date,POType:POType},
            beforeSend: function () {
                $('#searchh2').css('display','block');
                $('.table_purchase_report tbody').css('display','none');
            },
            complete: function () {
                $('.table_purchase_report tbody').css('display','');
                $('#searchh2').css('display','none');
            },
            success:function(data){
                $('.table_purchase_report tbody').html(data);
            }
        });
    }
  
    $('#search_data').on('click',function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var POType = "D"; // Direct Purchase Order
	    var msg = "Sales Report "+from_date +" To " + to_date;
	    $(".report_for").text(msg);
        load_data(from_date,to_date,POType);
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
<?php require 'modules/purchase/assets/js/pur_order_js.php';?>

