<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="widget relative" id="widget-<?php echo create_widget_id(); ?>" data-name="Sell Dashboard">
      <div class="widget-dragger"></div>
        <div class="row">
            <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            
            <div class="row">
                <?php
                    $i = 1;
                foreach($AllCenter as $key=>$val) { 
                
                    if($i == "4"){
                            $i = 1
                        ?>
                        <div class="clearfix"></div>
                    <?php
                    }
                ?>
                
                
                <div class="col-md-4">
                    <div class="table-daily_report">
                      <table class="tree table table-striped table-bordered table-daily_report" id="table-daily_report" width="100%">
                        <thead>
                            <tr>
                                <th colspan="7" style="text-align:center;"><?php echo $val["CenterName"];?></th>
                            </tr>
                          <tr>
                            <td style="text-align:left;">QC</td>
                            <td style="text-align:left;">Qty Purch till Date</td>
                            <td style="text-align:left;">Avg Rate</td>
                            <td style="text-align:left;">Today's Purch Qty</td>
                            <td style="text-align:left;">Avg Rate</td>
                            <td style="text-align:left;">Current Rate</td>
                            <td style="text-align:left;">New Rate</td>
                          </tr>
                        </thead>
                        <tbody>
                        <?php foreach($CenterWiseItem as $keys=>$value){
                            $match = 0;
                            $purch_weight = '';
                            $Todaypurch_weight = '';
                            $CurrentRate = '';
                            $AvgRate = '';
                            $AvgTRate = "";
                                if($val['CenterID'] == $value["CenterID"]){
                                    
                                    foreach($ItemWiseCenterWisePurchase as $purkey=>$purvalue){
                                        if($purvalue["ItemID"] == $value["ItemID"] && $purvalue["CenterID"]==$val['CenterID']){
                                            $match++;
                                            $purch_weight = $purvalue["NetWeight"];
                                        }
                                    }
                                    
                                    foreach($ItemWiseCenterWiseTodayPurchase as $purTkey=>$purTvalue){
                                        if($purTvalue["ItemID"] == $value["ItemID"] && $purTvalue["CenterID"]==$val['CenterID']){
                                            $match++;
                                            $Todaypurch_weight = $purTvalue["NetWeight"];
                                        }
                                    }
                                    
                                    foreach($ItemWiseCenterWiseCurrentRate as $Ratekey=>$Ratevalue){
                                        if($Ratevalue["ItemID"] == $value["ItemID"] && $Ratevalue["CenterID"]==$val['CenterID']){
                                            $CurrentRate = $Ratevalue["Rate"];
                                        }
                                    }
                                    
                                    foreach($ItemWiseCenterWiseAvgRate as $avgRatekey=>$AvgRatevalue){
                                        if($AvgRatevalue["ItemID"] == $value["ItemID"] && $AvgRatevalue["CenterID"]==$val['CenterID']){
                                            $AvgRate = $AvgRatevalue["AVGRAte"] / $AvgRatevalue["TotalRate"];
                                        }
                                    }
                                    
                                    foreach($ItemWiseCenterWiseCurrentAvgRate as $avgTRatekey=>$AvgTRatevalue){
                                        if($AvgTRatevalue["ItemID"] == $value["ItemID"] && $AvgTRatevalue["CenterID"]==$val['CenterID']){
                                            $AvgTRate = $AvgTRatevalue["AVGRAte"] / $AvgTRatevalue["TotalRate"];
                                        }
                                    }
                                    
                            if($match > 0){
                                ?>
                                    <tr>
                                        <td><?php echo $value["ItemID"];?></td>
                                        <td style="text-align:center"><?php echo  number_format($purch_weight,2,'.','')?></td>
                                        <td style="text-align:center"><?php echo  number_format($AvgRate,2,'.','')?></td>
                                        <td style="text-align:center"><?php echo  number_format($Todaypurch_weight,2,'.','')?></td>
                                        <td style="text-align:center"><?php echo  number_format($AvgTRate,2,'.','')?></td>
                                        <td><?php echo  number_format($CurrentRate,2,'.','')?></td>
                                        <td></td>
                                    </tr>
                                <?php
                            }
                        ?>
                                
                            <?php
                                }
                            } ?>
                            
                        </tbody>
                      </table>   
                    </div>
                </div>
            <?php $i++; } ?>
            </div>
          </div>
</div>
</div>
         
        </div>
   </div>
