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
    input:focus{
            outline: none;
            box-shadow: none;
            border: none;
        }
.for-item-id{
    position: sticky !important;
    left: 0;
    width: 43px;
    background-color:#fff;
    }
.for-item-name{
    position: sticky;
    width: 81px;
    left: 43px;
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
    left: 43px;
    }
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <h4 style="margin-top:-10px;">Competitor Rate Master</h4>
                                <hr>
                <!-- Rate Import Buttom -->
                <?php echo $this->import->downloadSampleFormHtml(); ?>

            <?php if(!$this->import->isSimulation()) { ?>

              <?php echo $this->import->importGuidelinesInfoHtml(); ?>
              <?php //echo $this->import->createSampleTableHtml(); ?>

            <?php } else { ?>

              <?php echo $this->import->simulationDataInfo(); ?>
              <?php echo $this->import->createSampleTableHtml(true); ?>

            <?php } ?>
            
                <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'import_form')) ;?>
            
                <div class="row">
                  <div class="col-md-4">
                    
                    <?php echo form_hidden('items_import','true'); ?>
                    
                    <?php echo render_input('file_csv','choose_excel_file','','file'); ?>
                    <?php if(has_permission_new('DailyCompetitorRate','','edit')){ ?>
                    <div class="form-group">
                      <button type="button" class="btn btn-info import btn-import-submit"><?php echo _l('import'); ?></button>
                    </div>
                    <?php } ?>
                  </div>
                </div>
            <?php echo form_close(); ?>
                <div class="row">
                    <div class="col-md-12">
                       
                        <div class="table-daily_report tableFixHead2">
             
                        <table class="tree table table-striped table-bordered table-daily_report tableFixHead2" id="table-daily_report" width="100%">
                  
                            <thead>
                                <tr>
                                    <th style="text-align:left;" class="for-item-idth">ItemID</th>
                                    <th style="text-align:left;" class="for-item-nameth">Item Name</th>
                                    <?php
                                        foreach($center as $key =>$value){
                                            $ColSpan = 0;
                                            $CompetitorIDs = $value["CompetitorID"];
                                            $Competitor_array = explode(',', $CompetitorIDs);
                                            
                                            foreach($Competitor as $Ckey =>$Cvalue){
                                                if(in_array($Cvalue["CompetitorID"], $Competitor_array)){
                                                    $ColSpan++;
                                                }
                                            }
                                            if($ColSpan >1){
                                                $colspan_style = 'colspan = "'.$ColSpan.'"';
                                            }else{
                                                $colspan_style = "";
                                            }
                                            ?>
                                            <th style="text-align:center;" <?php echo $colspan_style; ?>><b><?php echo $value["CenterName"];?></b></th>
                                            <?php
                                        }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="2" style="text-align:center;font-weight:700;"> Item Details</td>
                                    <?php
                                        foreach($center as $key =>$value){
                                            $CompetitorIDs = $value["CompetitorID"];
                                            $Competitor_array = explode(',', $CompetitorIDs);
                                            
                                            foreach($Competitor as $Ckey =>$Cvalue){
                                                if(in_array($Cvalue["CompetitorID"], $Competitor_array)){
                                                    ?>
                                                        <td style="text-align:center;font-weight:700;"><?php echo $Cvalue["Competitor"].'('.$Cvalue["CompetitorID"].')';?></td>
                                                    <?php
                                                }
                                            }
                                        }
                                    ?>
                                    
                                </tr>
                                <?php
                                    foreach($commodity as $ItemID1 => $ItemValue1){
                                        ?>
                                        <tr>
                                            <td class="for-item-id"><?php echo $ItemValue1["ItemID"]; ?></td>
                                            <td class="for-item-name"><?php echo $ItemValue1["ItemName"]; ?></td>
                                            <?php
                                                foreach($center as $key1 =>$value1){
                                                    $CompetitorIDs1 = $value1["CompetitorID"];
                                                    $Competitor_array1 = explode(',', $CompetitorIDs1);
                                                    
                                                    $ItemIDs = $value1["commodity"];
                                                    $Item_array = explode(',', $ItemIDs);
                                                    
                                                    foreach($Competitor as $Ckey1 =>$Cvalue1){
                                                        if(in_array($Cvalue1["CompetitorID"], $Competitor_array1)){
                                                            if(in_array($ItemValue1["ItemID"], $Item_array)){
                                                                    $rate = "";
                                                                    foreach($Rate as $rateKey =>$RateValue){
                                                                        if($RateValue["ItemID"] == $ItemValue1["ItemID"] && $RateValue["CenterID"] == $value1["CenterID"] && $RateValue["KeyID"] == $Cvalue1["CompetitorID"]){
                                                                            $rate = $RateValue["Rate"];
                                                                        }
                                                                    }
                                                                ?>
                                                                    <td style="text-align:center;font-weight:500;<?php echo $css;?>">
                                                                        <input type="hidden" id="<?php echo $ItemValue1['ItemID'].'-'.$value1["CenterID"].'-'.$Cvalue1["CompetitorID"].'_hidden'; ?>" name="<?php echo $ItemValue1['ItemID'].'-'.$value1["CenterID"].'-'.$Cvalue1["CompetitorID"]; ?>" value = "<?php echo $rate;?>" >
                                                                        <input type="text" onkeypress="return isNumber(event)" style="border:none;height:25px;width:100%;<?php echo $css;?>" onblur="myFunction(this.value,'<?php echo $ItemValue1["ItemID"]."-".$value1["CenterID"]."-".$Cvalue1["CompetitorID"]; ?>')" id="<?php echo $ItemValue1['ItemID'].'-'.$value1["CenterID"].'-C01'; ?>" name="<?php echo $ItemValue1['ItemID'].'-'.$value1["CenterID"].'-'.$Cvalue1["CompetitorID"]; ?>" value = "<?php echo $rate;?>" >
                                                                    </td>
                                                                <?php
                                                            }else{
                                                                ?>
                                                                    <td style="background-color: #ada6a0;height:15px"></td>
                                                                <?php
                                                            }
                                                        }
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
    function myFunction(new_rate,id){
        old_rate = $("#"+id+"_hidden").val();
        if(parseFloat(old_rate) != parseFloat(new_rate) && new_rate != ""){
            $.ajax({
                url:"<?php echo admin_url(); ?>rate_master/UpdateCompetitorRateByAjax",
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
<!--<script>
    $('input').blur(function(){
       $('#search_data').click(); 
    });
</script>-->