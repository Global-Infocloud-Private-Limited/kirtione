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
                <?php
                    if (has_permission_new('Charges', '', 'create') || has_permission_new('Charges', '', 'edit')) {
                        ?>
                            <div class="row" style="width:100%;margin:auto;">
                                <button type="button" class="btn btn-info" id="update_charges">Update Charges</button>
                            </div>
                        <?php
                    } 
                ?>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-daily_report tableFixHead2">
             
                        <table class="tree table table-striped table-bordered table-daily_report tableFixHead2" id="table-daily_report" width="100%">
                  
                            <thead>
                                <tr>
                                    <td colspan="2" style="text-align:center;font-weight:700;">Item Details</td>
                                </tr>
                                <tr>
                                    <th style="text-align:left;" class="for-item-idth">ItemID</th>
                                    <th style="text-align:left;" class="for-item-nameth">Item Name</th>
                                    <?php
                                        foreach($center as $key =>$value){ ?>
                                            <th style="text-align:center;"><b><?php echo $value["CenterName"];?></b></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody id="rate_update_table">
                                <?php
                                    foreach($commodity as $ItemID1 => $ItemValue1){
                                        ?>
                                        <tr>
                                            <td class="for-item-id"><?php echo $ItemValue1["ItemID"]; ?></td>
                                            <td class="for-item-name"><?php echo $ItemValue1["ItemName"]; ?></td>
                                            <?php
                                                foreach($center as $key =>$value){
                                                    $rate = "";
                                                    foreach($Charges as $rateKey =>$RateValue){
                                                        if($RateValue["ItemID"] == $ItemValue1["ItemID"] && $RateValue["CenterID"] == $value["CenterID"]){
                                                            $rate = $RateValue["Rate"];
                                                        }
                                                    } ?>
                                                    <td><input type="text" style="border:none;height:15px;" name="<?php echo $ItemValue1["ItemID"].'-'.$value["CenterID"]; ?>" value = "<?php echo $rate;?>" ></td>
                                            <?php  } ?>
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
$(document).ready(function(){
    $('#update_charges').click(function(){
        var InputArray = new Array();
        var i = 1;
        $("input[type=text]").each(function() {
            var ii = i - 1;
                InputArray[ii]=new Array();
                InputArray[ii][0]=this.name;
                InputArray[ii][1]=this.value;
                i++;
        });
        var ItemDivSerializedArr = JSON.stringify(InputArray);

        $.ajax({
            url:"<?php echo admin_url(); ?>rate_master/getUpdatedCharges",
            method:"POST",
            data:{inputData:ItemDivSerializedArr}, 
        
            success: function(data){
                if(data){
                    Swal.fire({
                        position: 'top-end',
                        title: 'Charge Updated!',
                        padding: '5px',
                        icon: 'success',
                        timer: 2500,
                        showConfirmButton: false,
                        timerProgressBar: false,
                    })  
                }
               
            }
        });
    });  
});
</script>