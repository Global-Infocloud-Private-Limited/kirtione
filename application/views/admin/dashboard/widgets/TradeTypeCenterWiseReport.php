<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="widget<?php if(!is_admin()){echo ' hide';} ?>" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo "Center Wise Details"; ?>">
   <?php if(is_admin()){ ?>
   <div class="row">
      <div class="col-md-12">
         <div class="panel_s">
            <div class="panel-body padding-10">
               <div class="widget-dragger"></div>
               <p class="padding-5"><?php echo 'Center Wise Details'; ?></p>
                <div class="row">
                    
                   <div class="col-md-6" >
                        <div class="form-group" app-field-wrapper="CenterID">
                            <label for="CenterID" class="form-label">Center Name</label>
                            <select name="CenterID" id="CenterID" class="selectpicker form-control" data-live-search="true">
                                <?php 
                                    foreach($centers as $key=>$val){
                                ?>        
                                        <option value="<?php echo $val['CenterID']; ?>" ><?php echo $val['CenterName']; ?></option>
                                <?php        
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6" >
                        <div class="form-group" app-field-wrapper="BookingType">
                            <label for="BookingType" class="form-label">Trade Type</label>
                            <select name="BookingType" id="BookingType" class="selectpicker form-control" data-live-search="true">
                                <option value="P" >Purchase</option>
                                <option value="D">Deposit</option> 
                                <option value="W">Withdrawal</option>
                            </select>
                        </div>
                    </div>
               </div>
               <hr class="hr-panel-heading-dashboard">
               <div class="row">
                   <div class="col-md-12">
                       <div class="table-purchase_request tableFixHead2">
                      <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table_purchase_request" width="100%">
                        <thead>
                            <tr>
                                <th style="text-align:left;">Status Name</th>
                                <th style="text-align:left;">Total Count</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                      </table>   
                    </div>
                   </div>
               </div>
               <!--<div class="relative" style="height:250px">
                  <canvas class="chart" height="250" id="leads_status_stats"></canvas>
               </div>-->
            </div>
         </div>
      </div>
   </div>
   <?php } ?>
</div>

