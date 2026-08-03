<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-daily_report          { overflow: auto;max-height: 90vh;width:100%;position:relative;top: 0px; }
.table-daily_report thead th { position: sticky; top: 0; z-index: 1; }
.table-daily_report tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
    
.for-item-id{
    position: sticky !important;
    left: 0;
    width: 43px;
    background-color:#fff;
    }
.for-item-name{
    position: sticky;
    width: 81px;
    left: 0px;
    background-color:#fff;
    }
    
.for-item-idth{
    position: sticky !important;
    left: 0;
    width: 43px;
    }
    
    .for-item-nameth{
position: sticky;
    width: 81px;
    left: 0px;
    }
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                    <div class="col-md-12 text-centerr"  >
    					<nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Master</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Trader Kirti Sale Rate Master</b></li>
    							
    						</ol>
    					</nav>
    					<hr style="margin-Bottom:12px !important;">
    				</div>
                </div>
                
                <!-- Rate Import -->
            <?php if(!$this->import->isSimulation()) { ?>

              <?php echo $this->import->importGuidelinesInfoHtml(); ?>
              <?php //echo $this->import->createSampleTableHtml(); ?>

            <?php } else { ?>

              <?php //echo $this->import->simulationDataInfo(); ?>
              <?php //echo $this->import->createSampleTableHtml(true); ?>

            <?php } ?>
            
                
                <div class="row">
                    <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'import_form')) ;?>
                    <div class="col-md-4">
                    <?php echo form_hidden('items_import','true'); ?>
                    <?php echo render_input('file_csv','choose_excel_file','','file'); ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-2">
                        <?php if(has_permission_new('DailyMandiRate','','edit')){ ?>
                        <div class="form-group">
                          <button type="button" class="btn btn-info import btn-import-submit"><?php echo _l('import'); ?></button>
                        </div>
                        <?php } ?>
                    </div>
                    <?php echo form_close(); ?>
                    <div class="col-md-4">
                        <?php echo $this->import->downloadSampleFormHtml(); ?>
                    </div>
                </div>
            <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h4 >Update Rates for Sale Commodity</h4>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group"><small class="req text-danger">* </small> 
                						<label for="Commodity" class="control-label">Select Commodity</label>
                						<select class="selectpicker" name="Commodity" data-live-search="true" id="Commodity" data-width="100%">
                						    <option value="">Non Selected</option>
                						    <?php
                						        foreach($commodity as $value){
                						            ?>
                						               <option value="<?php echo $value['ItemID'];?>"><?php echo strtoupper($value['ItemName']);?></option>
                						            <?php
                						        }
                						    ?>
                						</select>
                						</div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <?php echo render_select('CenterSelect',$staffs,'','Center','',array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
                                        
                                    </div>
                                    
                                    <div class="col-md-2"><small class="req text-danger">* </small>
                                        <label for="new_rate" class="control-label">New rate</label>
                                            <input type="text"  onkeypress="return isNumber(event)" name="new_rate" id="new_rate" class="form-control" value="" >
                                    </div>
                                    
                                    <div class="col-md-2">
                        
                                        <?php if (has_permission_new('DailyRate', '', 'edit')) {
                                        ?>
                                        <button type="button" class="btn btn-info updateBtn" style="margin-top: 20px;">Update</button>
                                        <?php
                                        }else{
                                        ?>
                                        <button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
                                        <?php
                                        }?>
                                        
                                    </div>
                                </div>
                            </div>
                            <?php
                            //if(is_admin()){
                            ?>
                            <div class="col-md-6" style="border-left: 1px solid;">
                                <h4 >Update Trading Status for Sale Commodity</h4>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group"><small class="req text-danger">* </small> 
                						<label for="Commodity_on_off" class="control-label">Select Commodity</label>
                						<select class="selectpicker" name="Commodity_on_off" multiple="1" data-actions-box = "1" data-live-search="true" id="Commodity_on_off" data-width="100%">
                						    
                						    <?php
                						        foreach($commodity as $value){
                						            ?>
                						               <option value="<?php echo $value['ItemID'];?>"><?php echo strtoupper($value['ItemName']);?></option>
                						            <?php
                						        }
                						    ?>
                						</select>
                						</div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <?php echo render_select('Center_on_of',$staffs,'','Center','',array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
                                        
                                    </div>
                                    
                                    <div class="col-md-2"><small class="req text-danger">* </small>
                                        <label for="trading_status" class="control-label">Trading</label>
                                        <select class="selectpicker" name="trading_status" data-live-search="false" id="trading_status" data-width="100%">
                						    <option value="Y">Enable</option>
                						    <option value="N">Disable</option>
                						            
                						</select>
                                    </div>
                                    
                                    <div class="col-md-2">
                        
                                        <?php if (has_permission_new('SaleRateMaster', '', 'edit')) {
                                        ?>
                                        <button type="button" class="btn btn-info TradingupdateBtn" style="margin-top: 20px;">Update</button>
                                        <?php
                                        }else{
                                        ?>
                                        <button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
                                        <?php
                                        }?>
                                        
                                    </div>
                                </div>
                            </div>
                            <?php //} ?>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <input id="All" name="All" type="checkbox" value="true" checked onclick="toggle(this);"> <label style="margin-right: 10px;margin-left: 5px;" for="All">ALL</label>
                        <?php
                            foreach($CommodityGroup as $value){
                                ?>
                                    <input id="<?php echo $value['name']?>" name="<?php echo $value['name']?>" class="chk" type="checkbox" checked value="<?php echo $value['id']?>"><label style="margin-right: 10px;margin-left: 5px;" for="<?php echo $value['name']?>"><?php echo $value['name']?></label>
                                <?php
                            }
                        ?>
                    </div>
                    <div class="col-md-12">
                        <div class="table-daily_report tableFixHead2">
                        <table class="tree table table-striped table-bordered table-daily_report tableFixHead2" id="table-daily_report" width="100%">
                  
                            <thead>
                                <tr>
                                    <!--<th style="text-align:left;" class="for-item-idth">ItemID</th>-->
                                    <th style="text-align:left;" class="for-item-nameth">Item Name</th>
                                    <?php
                                        foreach($center as $key =>$value){
                                            $match2 = 0;
                                            foreach($CenterWiseCommodity as $Key2 =>$value2){
                                                if($value2["CenterID"] == $value["CenterID"]){
                                                    $match2++;
                                                }
                                            }   
                                            if($match2 > 0){
                                                ?>
                                                    <th style="text-align:center;min-width:80px !important;font-size:10px;" title="<?php echo strtoupper($value["CenterName"]);?>"><b><?php echo strtoupper(substr($value["CenterName"],0,10));?></b></th>
                                                <?php
                                            }
                                        }
                                    ?>
                                </tr>
                            </thead>
                            <tbody id="rate_update_table">
                                
                                <?php
                                    foreach($commodity as $ItemID1 => $ItemValue1){
                                        ?>
                                        <tr>
                                            <!--<td class="for-item-id"><?php echo $ItemValue1["ItemID"]; ?></td>-->
                                            <td class="for-item-name"><?php echo strtoupper($ItemValue1["ItemName"]); ?></td>
                                            <?php
                                                foreach($center as $key1 =>$value1){
                                                    $match = 0;
                                                    foreach($CenterWiseCommodity as $Key2 =>$value2){
                                                        if($value2["ItemID"]==$ItemValue1["ItemID"] && $value2["CenterID"] == $value1["CenterID"]){
                                                            $match++;
                                                        }
                                                    }
                                                    if($match >0){
                                                            $rate = "";
                                                            $TradeOnOff = '';
                                                            $css = '';
                                                            foreach($Rate as $rateKey =>$RateValue){
                                                                if($RateValue["ItemID"] == $ItemValue1["ItemID"] && $RateValue["CenterID"] == $value1["CenterID"]){
                                                                    $rate = $RateValue["Rate"];
                                                                    $TradeOnOff = $RateValue["SaleTradeOnOff"];
                                                                }
                                                            }
                                                            if($TradeOnOff == 'Y'){
                                                                $css = 'background-color: green;';
                                                            }else{
                                                                $css = 'background-color: red;';
                                                            }
                                                        ?>
                                                            <td style="text-align:center;font-weight:500;<?php echo $css;?>" title="<?php echo strtoupper($value1["CenterName"]);?>"><?php echo $rate;?>
                                                                <!--<input type="hidden" id="<?php echo $ItemValue1['ItemID'].'_'.$value1["CenterID"].'_C01_hidden'; ?>" name="<?php echo $ItemValue1['ItemID'].'-'.$value1["CenterID"].'-C01'; ?>" value = "<?php echo $rate;?>" >
                                                                <input type="text" onkeypress="return isNumber(event)" style="border:none;height:25px;width:100%;<?php echo $css;?>" onchange="myFunction(this.value,'<?php echo $ItemValue1["ItemID"]."_".$value1["CenterID"]."_C01"; ?>')" id="<?php echo $ItemValue1['ItemID'].'-'.$value1["CenterID"].'-C01'; ?>" name="<?php echo $ItemValue1['ItemID'].'-'.$value1["CenterID"].'-C01'; ?>" value = "<?php echo $rate;?>" >-->
                                                            </td>
                                                        <?php
                                                    }else{
                                                        ?>
                                                            <td style="background-color: #ada6a0;height:15px" title="<?php echo strtoupper($value1["CenterName"]);?>"></td>
                                                        <?php
                                                    }
                                                }
                                            ?>
                                           
                                        </tr>
                                        <?php
                                    }
                                ?>
                            </tbody>
                        </table>   
                       
                        </div>
            
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
<script>
    function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode = 46 && charCode > 31 
            && (charCode < 48 || charCode > 57)){
        return false;
    }
    return true;
    }
</script>
<script>
function toggle(source) {
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i] != source)
            checkboxes[i].checked = source.checked;
    }
}
</script>
<script>
$(document).ready(function(){
    $('#Attempt input:checkbox').change(function () {
        
    })
    
    $('#Commodity').on('change', function() {
		var ItemID = $(this).val();
		var url = "<?php echo admin_url(); ?>rate_master/GetItemWiseCenter";
        jQuery.ajax({
            type: 'POST',
            url:url,
            data: {ItemID: ItemID},
            dataType:'json',
            success: function(data) {
                $("#CenterSelect").find('option').remove();
                $("#CenterSelect").selectpicker("refresh");
                for (var i = 0; i < data.length; i++) {
                    $("#CenterSelect").append(new Option(data[i].CenterName, data[i].CenterID));
                }
                $('.selectpicker').selectpicker('refresh');
            }
        });
	});
    
    $('#Commodity_on_off').on('change', function() {
		var ItemID = $(this).val().toString();
		var url = "<?php echo admin_url(); ?>rate_master/GetItemWiseCenter";
        jQuery.ajax({
            type: 'POST',
            url:url,
            data: {ItemID: ItemID},
            dataType:'json',
            success: function(data) {
                $("#Center_on_of").find('option').remove();
                $("#Center_on_of").selectpicker("refresh");
                for (var i = 0; i < data.length; i++) {
                    $("#Center_on_of").append(new Option(data[i].CenterName, data[i].CenterID));
                }
                $('.selectpicker').selectpicker('refresh');
            }
        });
	});
    
    $('.TradingupdateBtn').click(function(){
        Commodity = $("#Commodity_on_off").val();
        var Commoditys = Commodity.toString();
        CenterID = $("#Center_on_of").val();
        var CenterIDs = CenterID.toString();
        trading_status = $("#trading_status").val();
        if(Commoditys == ""){
            alert('please select atleast one Commodity');
        }else if(CenterIDs == ""){
            alert('please select atleast one Center');
        }else{
            $.ajax({
                url:"<?php echo admin_url(); ?>rate_master/UpdateSaleTradingStatus",
                method:"POST",
                data:{Commoditys:Commoditys,CenterIDs:CenterIDs,trading_status:trading_status}, 
                dataType:'json',
                success: function(data){
                    if(data == true){
                        alert_float('success', 'Trading status Updated!');
                    }else{
                        alert_float('warning', 'Trading status Not Updated');
                    }
                    window.location.reload();
                }
            });
        }
    })
    $('.updateBtn').click(function(){
        Commodity = $("#Commodity").val();
        CenterID = $("#CenterSelect").val();
        var CenterIDs = CenterID.toString()
        new_rate = $("#new_rate").val();
        if(Commodity == ""){
            alert('please select commodity');
        }else if(CenterIDs == ""){
            alert('please select atleast one center');
        }else if(new_rate == ""){
            alert('please enter new rate');
        }else{
            $.ajax({
                url:"<?php echo admin_url(); ?>rate_master/UpdateSaleRateByForm",
                method:"POST",
                data:{Commodity:Commodity,CenterIDs:CenterIDs,new_rate:new_rate}, 
                dataType:'json',
                success: function(data){
                    if(data == '1'){
                        alert_float('success', 'New Rate Updated!');
                    }else if(data == '0'){
                        alert_float('warning', 'Old Rate Blank');
                    }else{
                        alert_float('warning', 'Rate not updated');
                    }
                    window.location.reload();
                    alert_float('success', 'New Rate Updated!');
                }
            });
        }
    });
    
    
});
</script>
<script>
    function myFunction(new_rate,id){
        old_rate = $("#"+id+"_hidden").val();
        if(parseFloat(old_rate) != parseFloat(new_rate) && new_rate != ""){
            $.ajax({
                url:"<?php echo admin_url(); ?>rate_master/UpdateSaleRate",
                method:"POST",
                data:{id:id,new_rate:new_rate}, 
                dataType:'json',
                success: function(data){
                    if(data == '1'){
                        Swal.fire({
                            position: 'top-end',
                            title: 'New Rate Updated!',
                            padding: '5px',
                            icon: 'success',
                            timer: 3000,
                            showConfirmButton: false,
                            timerProgressBar: false,
                        })  
                    }else if(data == '0'){
                        Swal.fire({
                            position: 'top-end',
                            title: 'Rate Blank',
                            padding: '5px',
                            icon: 'warning',
                            timer: 3000,
                            showConfirmButton: false,
                            timerProgressBar: false,
                        })
                    }else{
                        Swal.fire({
                            position: 'top-end',
                            title: 'Rate not updated',
                            padding: '5px',
                            icon: 'warning',
                            timer: 3000,
                            showConfirmButton: false,
                            timerProgressBar: false,
                        })
                    }
                }
            });
        }
        
    }
</script>

