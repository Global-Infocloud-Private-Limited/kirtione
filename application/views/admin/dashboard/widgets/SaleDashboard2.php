<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php if (has_permission_new('sell_dashboard', '', 'view')) { ?>
<div class="widget relative" id="widget-<?php echo create_widget_id(); ?>" data-name="Sell Dashboard2">
    <div class="widget-dragger"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div class="row" style="margin-bottom:20px;">
                <a href="#" class="btn btn-success mright5" style="float:right;margin-top:-1%" data-toggle="tooltip" data-title="Click to get real time data" onclick="GetSaleDashboardData(); return false;" data-original-title="" title="">
                    <i class="fa fa-refresh"> &nbsp;&nbsp;&nbsp;&nbsp;Refresh</i>
                </a>
                <a href="<?php echo admin_url();?>accounting/CashFlow" target="_blank" class="btn btn-info mright5" style="float:left;margin-top:-1%" data-toggle="tooltip" data-title="Click to Move Cash Flow Page" data-original-title="" title="">
                    cash flow page
                </a>
                <div class="col-md-12" >
                    <div id="ck-button" for="All2" style="width:13%">
                       <label for="All2">
                          <input type="checkbox" style="width:13%" value="true" id="All2" name="All2" onclick="toggle2(this);"><span>All</span>
                       </label>
                    </div>
                <?php
                    foreach($AllCenter as $value){
                        ?>
                        <div id="ck-button" style="width:13%">
                           <label>
                              <input type="checkbox" style="width:13%" value="<?php echo $value['CenterID']?>" id="<?php echo $value['CenterName']?>" name="CenterIDs" onclick="CheckCenter();"><span><?php echo strtoupper($value['CenterName']);?></span>
                           </label>
                        </div>
                        <?php
                    }
                ?>
                </div>
                
                <div class="col-md-12">
                    <hr style="border-bottom: 2px solid #51647c;">
                        
                        <div id="ck-button" for="All" style="width:13%">
                           <label for="All">
                              <input type="checkbox" style="width:13%" value="true" id="All" name="All" onclick="toggle(this);"><span>All</span>
                           </label>
                        </div>
                    <?php
                        foreach($CommodityGroup as $value){
                            ?>
                            <!--<div class="col-md-1" style="padding: 0 3px;">
                                <input id="<?php echo $value['name']?>" name="CommodityIDs" onclick="CheckCommodity();" class="chk" type="checkbox"  value="<?php echo $value['ShortCode']?>"><label style="margin-right: 10px;margin-left: 5px;" for="<?php echo $value['name']?>"><?php echo $value['name']?></label>
                            </div>-->
                            <div id="ck-button" style="width:13%">
                               <label>
                                  <input type="checkbox" style="width:13%" value="<?php echo $value['ShortCode']?>" id="<?php echo $value['name']?>" name="CommodityIDs" onclick="CheckCommodity();"><span><?php echo strtoupper($value['name']);?></span>
                               </label>
                            </div>
                            <?php
                        }
                    ?>
                </div>
                
            </div>
            <ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active">
					<a href="#TraderTab" aria-controls="TraderTab" style="font-size:22px;padding:5px; 22px;" role="tab" data-toggle="tab">
						<?php echo "Trader"; ?>
					</a>
				</li>
				<li role="presentation">
					<a href="#FarmerTab" aria-controls="FarmerTab" role="tab" style="font-size:22px;padding:5px 22px;;" data-toggle="tab">
						<?php echo "Farmer"; ?>
					</a>
				</li>
			</ul>
			
			<div class="tab-content">
			    <!--// Trader Tab details-->
				<div role="tabpanel" class="tab-pane active" id="TraderTab">
					<div class="row" id="TraderData">
					    
					</div>
				</div>
				
				<!--// Farmer Tab details-->
				<div role="tabpanel" class="tab-pane " id="FarmerTab">
					<div class="row" id="FarmerData">
					    
					</div>
				</div>
			</div>
			
			<div class="modal fade" id="DasboardDetails">
				<div class="modal-dialog pup100" >
					<div class="modal-content" style="width: 119%;">
						<div class="modal-header" style="padding: 5px !important;">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title">Dashboard Details</h4>
						</div>
						<div class="modal-body modalbody" style="">
							<div class="row" id="ShowDetails">
							    
							</div>
						</div>
					</div>
				</div>
			</div>
			
          </div>
        </div>
      </div>
    </div>
</div>
<?php } ?>
