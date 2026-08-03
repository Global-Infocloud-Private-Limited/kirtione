<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="widget" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo 'CenterWise Purchase & Deposit Status'; ?>">
   <?php if(is_admin()){ ?>
   <div class="purchase-summary">
      <div class="panel_s">
         <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="row">
                <div class="col-md-12">
                    <h4>Center Wise Purchase & Deposit Status</h4>
                    <hr class="hr-panel-heading-dashboard">
                </div>
            </div>
            <div class="row home-summary">
               <?php if(is_admin()){
                  ?>
                  <div class="col-md-12 col-lg-12 col-sm-12">
                        <table class="tree table table-striped table-bordered table-centerwise_status" id="table_centerwise_status" width="100%" >
                            <thead>
                                <th>CenterName</th>
                                <th>Purchase</th>
                                <th>Deposit</th>
                            </thead>
                            <tbody>
                                <?php 
                                    foreach($centers as $key=>$val){
                                ?> 
                                    <tr>
                                        <td><?php echo $val['CenterName'];?></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                <?php }?>
                            </tbody>
                        </table>
                  </div>
                  <?php } ?>
                  
                  </div>
                  
               </div>
            </div>
         </div>
         <?php } ?>
      </div>
