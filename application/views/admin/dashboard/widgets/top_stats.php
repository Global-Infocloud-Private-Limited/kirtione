<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget relative" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('quick_stats'); ?>">
      <div class="widget-dragger"></div>
      <div class="row">
      
         <?php if(is_admin()){ ?>
         <div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6 col-lg-3">
            <div class="top_stats_wrapper">
                <?php
                    $percent_data = GetAccountListByType(1);
                ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-balance-scale"></i> <?php echo "Registered Farmers / KYC"; ?>
                  <span class="pull-right"> <?php echo $percent_data['total']; ?> / <?php echo $percent_data['total_by_status']; ?></span>
               </p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                  </div>
               </div>
            </div>
         </div>
         <?php } ?>
         <?php if(is_admin()){ ?>
         <div class="quick-stats-leads col-xs-12 col-md-6 col-sm-6 col-lg-3">
            <div class="top_stats_wrapper">
                <?php
                  $percent_data = GetAccountListByType(3);
                ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-tty"></i> <?php echo "Registered Traders / KYC"; ?>
                  <span class="pull-right"><?php echo $percent_data['total']; ?> / <?php echo $percent_data['total_by_status']; ?></span>
               </p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                  </div>
               </div>
            </div>
         </div>
         
         <div class="quick-stats-projects col-xs-12 col-md-6 col-sm-6 col-lg-3">
            <div class="top_stats_wrapper">
                <?php
                    $percent_data = GetAccountListByType(2);
                ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-cubes"></i> <?php echo "Registered Broker / KYC"; ?>
                    <span class="pull-right"><?php echo $percent_data['total']; ?> / <?php echo $percent_data['total_by_status']; ?></span>
               </p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-success no-percent-text not-dynamic"  role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                  </div>
               </div>
            </div>
         </div>
         <div class="quick-stats-tasks col-xs-12 col-md-6 col-sm-6 col-lg-3">
            <div class="top_stats_wrapper">
                <?php
                    $percent_data = GetAccountListByType(4);
                ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-tasks"></i> <?php echo "Registered Corporate / KYC"; ?> <span class="pull-right">
                  <?php echo $percent_data['total']; ?> / <?php echo $percent_data['total_by_status']; ?>
                  </span>
               </p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_not_finished_tasks; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_not_finished_tasks; ?>">
                  </div>
               </div>
            </div>
         </div>
         <?php } ?>
         
      </div>
   </div>
