<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="widget" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo 'CenterWise CommodityWise Deposit'; ?>">
   <?php if(is_admin()){ ?>
   <div class="purchase-summary">
      <div class="panel_s">
         <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="row">
                <?php
                    $from_date = "01/".date('m')."/".date('Y');
                    $to_date = date('d/m/Y');
                ?> 
                <div class="col-md-4">
                    <?php echo render_date_input('from_date_deposit','From',$to_date); ?>
                </div>
                <div class="col-md-4">
                    <?php echo render_date_input('to_date_deposit','To',$to_date);  ?>
                </div>
            </div>
            <hr class="hr-panel-heading-dashboard">
            <div class="row home-summary">
               <?php if(is_admin()){
                  ?>
                  <div class="col-md-12 col-lg-12 col-sm-12">
                        
                        <div id="centerwise_commoditywise_deposit">
						</div>
                  </div>
                  <?php } ?>
                  
                  </div>
                  
               </div>
            </div>
         </div>
         <?php } ?>
      </div>
