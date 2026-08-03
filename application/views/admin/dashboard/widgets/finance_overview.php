<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('finance_overview'); ?>">
   <?php if(is_admin()){ ?>
   <div class="finance-summary">
      <div class="panel_s">
         <div class="panel-body">
            <div class="widget-dragger"></div>
            <div class="row home-summary">
               <?php if(is_admin()){
                  ?>
                  <div class="col-md-6 col-lg-4 col-sm-6">
                     <div class="row">
                        <div class="col-md-12">
                           <p class="text-dark text-uppercase"><?php echo 'Purchase Overview'; ?></p>
                           <hr class="mtop15" />
                        </div>
                        <?php $percent_data = GetAllTrade('P'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=6'); ?>" class="text-muted mbot15 inline-block">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo "Total Trade";//format_invoice_status(6,'',false); ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        <?php $percent_data = GetTradeByStatus(1,'NA','P'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?filter=not_sent'); ?>" class="text- inline-block mbot15">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo 'New Trade'; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar- no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        <?php $percent_data = GetTradeByStatus(1,'Y','P'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=1'); ?>" class="text-success mbot15 inline-block">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo 'Trade Accepted'; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        <?php $percent_data = GetTradeByStatus(1,'N','P'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=3'); ?>" class="text-danger mbot15 inline-block">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo 'Trade Rejected'; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        <?php $percent_data = GetTradeByStatus('3','Y','P'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=4'); ?>" class="text-warning mbot15 inline-block">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo 'Partial Completed'; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-warning no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php $percent_data = GetTradeByStatus(2,'Y','P'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>" class="text-success mbot15 inline-block">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo "Trade Completed"; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        
                        <?php $percent_data = GetAllInward('P'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>" class="text-muted mbot15 inline-block">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo "ASN Generated"; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php $percent_data = GateInGenerated('P'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>" class="text- mbot15 inline-block">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo "Gate IN Generated"; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar- no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php $percent_data = TareWeightGenerated('P'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>" class="text-info mbot15 inline-block">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo "Tare Weight Done"; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php $percent_data = GateOutGenerated('P'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>" class="text-success mbot15 inline-block">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo "Gate Out Generated"; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        
                        
                     </div>
                  </div>
                  <?php } ?>
                  <?php if(is_admin()){ ?>
                  <div class="col-md-6 col-lg-4 col-sm-6">
                     <div class="row">
                        <div class="col-md-12 text-stats-wrapper">
                           <p class="text-dark text-uppercase"><?php echo 'Deposit OVERVIEW'; ?></p>
                           <hr class="mtop15" />
                        </div>
                        <?php
                            $percent_data = GetAllTrade('D');
                         ?>
                         <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo $url; ?>" class="text-muted mbot15 inline-block estimate-status-dashboard-default">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span>
                              <?php echo 'All Trade'; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php
                            $percent_data = GetTradeByStatus(1,'NA','D');
                         ?>
                         <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo $url; ?>" class="text- mbot15 inline-block estimate-status-dashboard-">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span>
                              <?php echo 'New Trade'; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar- no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        
                        <?php
                            $percent_data = GetTradeByStatus(1,'Y','D');
                         ?>
                         <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo $url; ?>" class="text-success mbot15 inline-block estimate-status-dashboard-success">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span>
                              <?php echo 'Trade Accepted'; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php
                            $percent_data = GetTradeByStatus(1,'N','D');
                         ?>
                         <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo $url; ?>" class="text-danger mbot15 inline-block estimate-status-dashboard-danger">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span>
                              <?php echo 'Trade Rejected'; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php
                            $percent_data = GetTradeByStatus(3,'Y','D');
                         ?>
                         <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo $url; ?>" class="text-warning mbot15 inline-block estimate-status-dashboard-warning">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span>
                              <?php echo 'Partial Completed'; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-warning no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php
                            $percent_data = GetTradeByStatus(2,'Y','D');
                         ?>
                         <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo $url; ?>" class="text-success mbot15 inline-block estimate-status-dashboard-success">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span>
                              <?php echo 'Completed'; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php $percent_data = GetAllInward('D'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>" class="text-muted mbot15 inline-block">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo "ASN Generated"; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php $percent_data = GateInGenerated('D'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>" class="text- mbot15 inline-block">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo "Gate IN Generated"; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar- no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php $percent_data = TareWeightGenerated('D'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>" class="text-info mbot15 inline-block">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo "Tare Weight Done"; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php $percent_data = GateOutGenerated('D'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>" class="text-success mbot15 inline-block">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo "Gate Out Generated"; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        
                     </div>
                  </div>
                  <?php } ?>
                  <?php if(is_admin()){ ?>
                  <div class="col-md-12 col-sm-6 col-lg-4">
                     <div class="row">
                        <div class="col-md-12 text-stats-wrapper">
                           <p class="text-dark text-uppercase"><?php echo 'Withdrawal OVERVIEW'; ?></p>
                           <hr class="mtop15" />
                        </div>
                            
                            <?php
                                $percent_data = GetAllTrade('W');
                            ?>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="<?php echo $url; ?>" class="text-muted mbot15 inline-block">
                                 <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo 'All Trade'; ?>
                              </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              <?php echo $percent_data['percent']; ?>%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                                 </div>
                              </div>
                           </div>
                           
                            <?php
                            $percent_data = GetTradeByStatus(1,'NA','W');
                         ?>
                         <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo $url; ?>" class="text- mbot15 inline-block estimate-status-dashboard-">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span>
                              <?php echo 'New Trade'; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar- no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        
                        <?php
                            $percent_data = GetTradeByStatus(1,'Y','W');
                         ?>
                         <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo $url; ?>" class="text-success mbot15 inline-block estimate-status-dashboard-success">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span>
                              <?php echo 'Trade Accepted'; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php
                            $percent_data = GetTradeByStatus(1,'N','W');
                         ?>
                         <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo $url; ?>" class="text-danger mbot15 inline-block estimate-status-dashboard-danger">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span>
                              <?php echo 'Trade Rejected'; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php
                            $percent_data = GetTradeByStatus(3,'Y','W');
                         ?>
                         <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo $url; ?>" class="text-warning mbot15 inline-block estimate-status-dashboard-warning">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span>
                              <?php echo 'Partial Completed'; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-warning no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php
                            $percent_data = GetTradeByStatus(2,'Y','W');
                         ?>
                         <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo $url; ?>" class="text-success mbot15 inline-block estimate-status-dashboard-success">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span>
                              <?php echo 'Completed'; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php $percent_data = GetAllInward('W'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>" class="text-muted mbot15 inline-block">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo "ASN Generated"; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php $percent_data = GateInGenerated('W'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>" class="text- mbot15 inline-block">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo "Gate IN Generated"; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar- no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php $percent_data = TareWeightGenerated('W'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>" class="text-info mbot15 inline-block">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo "Loaded Weight Done"; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                        
                        <?php $percent_data = GateOutGenerated('W'); ?>
                        <div class="col-md-12 text-stats-wrapper">
                           <a href="<?php echo admin_url('invoices/list_invoices?status=2'); ?>" class="text-success mbot15 inline-block">
                              <span class="_total bold"><?php echo $percent_data['total_by_status']; ?></span> <?php echo "Gate Out Generated"; ?>
                           </a>
                        </div>
                        <div class="col-md-12 text-right progress-finance-status">
                           <?php echo $percent_data['percent']; ?>%
                           <div class="progress no-margin progress-bar-mini">
                              <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_data['percent']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_data['percent']; ?>">
                              </div>
                           </div>
                        </div>
                           
                           
                           <div class="clearfix"></div>
                        </div>
                     </div>
                     <?php } ?>
                  </div>
                  <?php if(has_permission('invoices','','view') || has_permission('invoices','','view_own')){ ?>
                  <hr />
                  <a href="#" class="hide invoices-total initialized"></a>
                  <div id="invoices_total" class="invoices-total-inline">
                     <?php //load_invoices_total_template(); ?>
                  </div>
                  <?php } ?>
               </div>
            </div>
         </div>
         <?php } ?>
      </div>

