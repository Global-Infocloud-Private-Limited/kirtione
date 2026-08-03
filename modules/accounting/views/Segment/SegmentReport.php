<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    .th_total {
        padding-right: 10px;
    }
</style>

<style>
    .table-segment_report          { overflow: auto;max-height: 75vh;width:100%;position:relative;top: 0px; }
.table-segment_report thead th { position: sticky; top: 0;  }
.table-segment_report tbody th { position: sticky; left: 0; }
.col-id-sr-no{
    left: 0px;
    position: sticky !important;
    min-width: 50px;
    background-color: #438eb9;
    color: #fff;
}
.col-id-particular:hover {
    cursor: pointer;
    background-color: #ccc;
}
/*.col-id-sr-no:hover {
    cursor: pointer;
    background-color: #ccc;
}*/
#maingroup:hover {
    cursor: pointer;
    background-color: #ccc;
}
#subgroup:hover {
    cursor: pointer;
    background-color: #ccc;
}
.col-id-particular{
    left: 50px;
    position: sticky !important;
    min-width: 30%;
    background-color: #438eb9;
    color: #fff;
}
</style>

<div id="wrapper">
    <div class="panel_s">
        <div class="panel-body">
            <div class="row ">
				<div class="col-md-12 text-centerr"  >
					<nav aria-label="breadcrumb" >
						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
							<li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
							<li class="breadcrumb-item active" aria-current="page"><b>Segment Report</b></li>
							
						</ol>
					</nav>
					<hr class="hr_style" style="margin-Bottom:12px !important;">
				</div>
				<?php echo form_open('admin/accounting/SegmentReports',array('id'=>'filter_item_form')); ?>
				    <div class="col-md-3">
    				    <div class="form-group" app-field-wrapper="CenterID">
                            <label for="CenterID" class="control-label">Center Name</label><small class="req text-danger">* </small>
                            <select name="CenterID" id="CenterID" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                <option value="">All</option>
                            <?php 
                                foreach($CenterList as $key=>$value){ ?>
                                    <option value="<?php echo $value['CenterID']; ?>" <?php if($CenterID == $value['CenterID']){ echo 'selected';}?>><?php echo $value['CenterName']; ?></option>
                        <?php   }
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button  type ="submit"class="btn btn-info pull-left mleft5 search_data" style="margin-top: 19px;" id="search_data">Show</button>
                    </div>
				
				<?php echo form_close(); ?>
                <div class="col-md-1">
                    <?php if (has_permission_new('accounting_report', '', 'print')) {
                                ?>
                    <a class="btn btn-default" href="javascript:void(0);" style="margin-top: 19px;" onclick="printPage();">Print</a>
                    <?php } ?>
                </div>

                <div class="col-md-12">
                    <?php
                        $fy = $this->session->userdata('finacial_year');
                        $lastFy = $fy - 1;
                        $fy_ = $fy + 1;
                        $CurrYrFirstDate = '01/04/20' . $fy;
                        $CurrDate = date('d-m-Y');
                        $CurrYrLastDate = '31-03-20' . $fy_;
                        
                        $date1 = new DateTime($CurrDate); 
                        $date2 = new DateTime($CurrYrLastDate); 
                        
                        if($date1 < $date2){
                            $last_date = $CurrDate;
                        }else{
                            $last_date = $CurrYrLastDate;
                        }
                        
                        $LastYrFirstDate = '01/04/20' . $lastFy;
                        $LastYrLastDate = '31/03/20' . $fy;
                    ?>
                    <div class="page" id="DivIdToPrint">
                        <div id="accordion">
                            <div class="card">
                            <div class="table-segment_report tableFixHead2">
                                <table class="tree table table-striped table-bordered table-segment_report tableFixHead2">
                                    <thead>
                                        <?php
                                            $commodityCount = count($CommodityList);
                                            $colspan = ($commodityCount * 2) + 4;
                                            $FY = $this->session->userdata('finacial_year');
                                            $FY_next = $FY + 1;
                                            $FY_pre = $FY - 1;
                                            $CommodityIDArray = array();
                                        ?>
                                           
                                            <tr class="tr_header">
                                                <td class="col-id-sr-no"><b>Sr. No.</b></td>
                                                <td class="col-id-particular"><b>Particular</b></td>
                                                <?php
                                                    foreach($CommodityList as $val){
                                                        array_push($CommodityIDArray,$val['id']);
                                                ?>
                                                        <th colspan="2" style="text-align:center;"><?php echo $val["name"];?></th>
                                                <?php
                                                    }
                                                ?>
                                                <th colspan="2">Unallocated Total</th>
                                                <th colspan="2">Consolidated Total</th>
                                            </tr>
                                            <tr class="tr_header">
                                                <td class="col-id-sr-no"></td>
                                                <td class="col-id-particular"></td>
                                                <?php
                                                    foreach($CommodityList as $val){
                                                ?>
                                                        <th><?php echo "FY ".$FY_pre.'-'.$FY;?></th>
                                                        <th><?php echo "FY ".$FY."-".$FY_next;?></th>
                                                <?php
                                                    }
                                                ?>
                                                <th><?php echo "FY ".$FY_pre.'-'.$FY;?></th>
                                                <th><?php echo "FY ".$FY."-".$FY_next;?></th>
                                                <th><?php echo "FY ".$FY_pre.'-'.$FY;?></th>
                                                <th><?php echo "FY ".$FY."-".$FY_next;?></th>
                                            </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $commodityCount = count($CommodityList);
                                        $commodityCount_ = $commodityCount * 2;
                                        $colspan = $commodityCount_ + 2;
                                        $colspan_ = $colspan + 4;
                                        $MainCounter = 1000;
                                        $SubCounter1 = 2000;
                                        $SubCounter2 = 3000;
                                        $SubCounter3 = 4000;
                                        $i = 1;
                                        ?>
                                        
                                        <?php
                                            $TotalBal = 0;
                                            $TotalBalPre = 0;
                                        ?>
                                        <?php $MainCounter++;?>
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node "
                                            style="font-size:14px;"id="maingroup">
                                            <td class="parent col-id-sr-no" >1</td>
                                            <td class="parent col-id-particular"><b>EXTERNAL SALES</b></td>
                                            <?php
                                                $saleAmtpre = 0;
                                                $saleAmtcurr = 0;
                                                $i = 0;
                                                foreach($CommodityList as $val){
                                                    $GrpTotal = 0;
                                                    $GrpTotalPre = 0;
                                                    foreach($CommodityWiseInventoryAmt as $IKey=>$Ival){
                                                        if($Ival["id"] == $val['id']){
                                                            $GrpTotalPre += $Ival["SaleAmtPre"];
                                                            $GrpTotal += $Ival["SaleAmt"];
                                                        }
                                                    }
                                                    $CommodityList[$i]['TotalSale'] = $GrpTotal; 
                                                    $CommodityList[$i]['TotalSalePre'] = $GrpTotalPre; 
                                                    $saleAmtpre += $GrpTotalPre;
                                                    $saleAmtcurr += $GrpTotal;
                                                    ?>
                                                    
                                                    <td style="text-align:right;"><?php echo  number_format($GrpTotalPre, 2, '.', '') ?></td>
                                                    <td style="text-align:right;background-color: orange;"><?php echo  number_format($GrpTotal, 2, '.', '') ?></td>
                                                <?php
                                                $i++;
                                                } ?>
                                            <td style="text-align:right;">0.00</td>
                                            <td style="text-align:right;background-color: orange;">0.00</td>
                                            <td style="text-align:right;"><?php echo  number_format($saleAmtpre, 2, '.', '') ?></td>
                                            <td style="text-align:right;background-color: orange;"><?php echo  number_format($saleAmtcurr, 2, '.', '') ?></td>
                                        </tr>
                                        
                                        <?php $MainCounter++;?>
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node "
                                            style="font-size:14px;"id="maingroup">
                                            <td class="parent col-id-sr-no" >2</td>
                                            <td class="parent col-id-particular"><b>INTER-SEGMENT SALES</b></td>
                                            <?php
                                                $TotalBal = 0;
                                                $TotalBalPre = 0;
                                                $saleAmtpre = 0;
                                                $saleAmtcurr = 0;
                                                $i = 0;
                                                foreach($CommodityList as $val){
                                                    $GrpTotal = 0;
                                                    $GrpTotalPre = 0;
                                                    $CommodityList[$i]['InterSegTotalSale'] = $GrpTotal; 
                                                    $CommodityList[$i]['InterSegTotalSalePre'] = $GrpTotalPre; 
                                                ?>
                                                    <td style="text-align:right;"><?php echo  number_format($GrpTotalPre, 2, '.', '') ?></td>
                                                    <td style="text-align:right;background-color: orange;"><?php echo  number_format($GrpTotal, 2, '.', '') ?></td>
                                                <?php
                                                $i++;
                                                } ?>
                                            <td style="text-align:right;">0.00</td>
                                            <td style="text-align:right;background-color: orange;">0.00</td>
                                            <td style="text-align:right;"><?php echo  number_format($saleAmtpre, 2, '.', '') ?></td>
                                            <td style="text-align:right;background-color: orange;"><?php echo  number_format($saleAmtcurr, 2, '.', '') ?></td>
                                        </tr>
                                        
                                        <?php $MainCounter++;?>
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node "
                                            style="font-size:14px;"id="maingroup">
                                            <td class="col-id-sr-no parent"></td>
                                            <td class="parent col-id-particular"><b>TOTAL REVENUE (1 + 2)</b></td>
                                            <?php
                                                $saleAmtpre = 0;
                                                $saleAmtcurr = 0;
                                                $i = 0;
                                                foreach($CommodityList as $val){
                                                    $GrpTotal = 0;
                                                    $GrpTotalPre = 0;
                                                    
                                                    foreach($CommodityWiseInventoryAmt as $IKey=>$Ival){
                                                        if($Ival["id"] == $val['id']){
                                                            $GrpTotalPre += $Ival["SaleAmtPre"];
                                                            $GrpTotal += $Ival["SaleAmt"];
                                                            $ClsAmt = $Ival["CurrentValue"];
                                                            $ClsAmtPre = $Ival["CurrentValuePre"];
                                                        }
                                                    }
                                                    $CommodityList[$i]['TotalRevenue'] = $GrpTotal; 
                                                    $CommodityList[$i]['TotalRevenuePre'] = $GrpTotalPre; 
                                                    $CommodityList[$i]['CurrentValue'] = $ClsAmt; 
                                                    $CommodityList[$i]['CurrentValuePre'] = $ClsAmtPre; 
                                                    $saleAmtpre += $GrpTotalPre;
                                                    $saleAmtcurr += $GrpTotal;
                                                    ?>
                                                    
                                                    <td style="text-align:right;font-weight:700;"><?php echo  number_format($GrpTotalPre, 2, '.', '') ?></td>
                                                    <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($GrpTotal, 2, '.', '') ?></td>
                                                <?php
                                                $i++;
                                                } ?>
                                                <td style="text-align:right;font-weight:700;">0.00</td>
                                                <td style="text-align:right;font-weight:700;background-color: orange;">0.00</td>
                                                <td style="text-align:right;font-weight:700;"><?php echo  number_format($saleAmtpre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($saleAmtcurr, 2, '.', '') ?></td>
                                        </tr>
                                        <?php $MainCounter++;?>
                                        <?php
                                            $ActGroup1List = array();
                                            $ActGroup2List = array();
                                            $AccountList = array();
                                            foreach($DirectExpLedgerWiseCommodityWise as $DExpKey=>$DExpVal){
                                                $new1 = array(
                                                    "AccountID"=>$DExpVal["SubActGroupID1"],
                                                    "AccountName"=>$DExpVal["SubActGroupName1"]
                                                );
                                                array_push($ActGroup1List,$new1);
                                                $new2 = array(
                                                    "AccountID1"=>$DExpVal["SubActGroupID1"],
                                                    "AccountID"=>$DExpVal["SubActGroupID"],
                                                    "AccountName"=>$DExpVal["SubActGroupName2"]
                                                );
                                                array_push($ActGroup2List,$new2);
                                                
                                                $new = array(
                                                    "AccountID2"=>$DExpVal["SubActGroupID"],
                                                    "AccountID"=>$DExpVal["AccountID"],
                                                    "AccountName"=>$DExpVal["company"]
                                                );
                                                array_push($AccountList,$new);
                                            }
                                            $ActGroup1UniqueList = array_unique($ActGroup1List,SORT_REGULAR);
                                            $ActGroup2UniqueList = array_unique($ActGroup2List,SORT_REGULAR);
                                            $AccountUniqueList = array_unique($AccountList,SORT_REGULAR);
                                        ?>
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node"
                                            style="font-size:14px;"id="maingroup">
                                            <td class="parent col-id-sr-no">3</td>
                                            <td class="parent col-id-particular" ><b>DIRECT EXPENSE</b> <?php //print_r($ActGroup1UniqueList);?></td>
                                            <?php
                                                $TotalBal = 0;
                                                $TotalBalPre = 0;
                                                $i = 0;
                                            foreach($CommodityList as $val){
                                                $bal = 0;
                                                $CrAmt = 0;
                                                $DrAmt = 0;
                                                $balPre = 0;
                                                $CrAmtPre = 0;
                                                $DrAmtPre = 0;
                                                $bal1 = 0;
                                                $CrAmt1 = 0;
                                                $DrAmt1 = 0;
                                                $balPre1 = 0;
                                                $CrAmtPre1 = 0;
                                                $DrAmtPre1 = 0;
                                                
                                                foreach($DirectExpLedgerWiseCommodityWise as $DExpKey=>$DExpVal){
                                                    //Total Direct Expenses
                                                    if($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $lastFy){
                                                        $DrAmtPre += $DExpVal["SumAmt"];
                                                    }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $lastFy){
                                                        $CrAmtPre += $DExpVal["SumAmt"];
                                                    }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $FY){
                                                        $DrAmt += $DExpVal["SumAmt"];
                                                    }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $FY){
                                                        $CrAmt += $DExpVal["SumAmt"];
                                                    }
                                                }
                                                
                                                
                                                $bal = $DrAmt - $CrAmt;
                                                $balPre = $DrAmtPre - $CrAmtPre;
                                                
                                                $CommodityList[$i]['DirectExp'] = $bal; 
                                                $CommodityList[$i]['DirectExpPre'] = $balPre;
                                                $TotalBal += $bal;
                                                $TotalBalPre += $balPre;
                                                ?>
                                                <td style="text-align:right;font-weight:700;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <?php
                                            $i++;
                                            }
                                                
                                                $balUn = 0;
                                                $CrAmtUn = 0;
                                                $DrAmtUn = 0;
                                                $balUnPre = 0;
                                                $CrAmtUnPre = 0;
                                                $DrAmtUnPre = 0;
                                                foreach($DirectExpLedgerWiseCommodityWise as $DExpKey=>$DExpVal){
                                                    if($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $lastFy){
                                                        $DrAmtUnPre += $DExpVal["SumAmt"];
                                                    }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $lastFy){
                                                        $CrAmtUnPre += $DExpVal["SumAmt"];
                                                    }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $FY){
                                                        $DrAmtUn += $DExpVal["SumAmt"];
                                                    }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $FY){
                                                        $CrAmtUn += $DExpVal["SumAmt"];
                                                    }
                                                }
                                                
                                                $balUn = $DrAmtUn - $CrAmtUn;
                                                $balUnPre = $DrAmtUnPre - $CrAmtUnPre;
                                                $UnallocatedObj->DirectExp = $balUn;
                                                $UnallocatedObj->DirectExpPre = $balUnPre;
                                                $TotalBal += $balUn;
                                                $TotalBalPre += $balUnPre;
                                            ?>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($balUnPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($balUn, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($TotalBalPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($TotalBal, 2, '.', '') ?></td>
                                        </tr>
                                        <?php $SubCounter++; ?>
                                        <?php 
                                            
                                            foreach($ActGroup1UniqueList as $ActGrp1){
                                                $TotalAct1 = 0;
                                                $PreTotalAct1 = 0;
                                        ?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node  "
                                                style="font-size:13px;" id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
                                                <td class="col-id-sr-no"></td>
                                                <td class="col-id-particular"><?php echo $ActGrp1["AccountName"]; ?></td>
                                        <?php 
                                                foreach($CommodityList as $val){
                                                    $bal = 0;
                                                    $CrAmt = 0;
                                                    $DrAmt = 0;
                                                    $balPre = 0;
                                                    $CrAmtPre = 0;
                                                    $DrAmtPre = 0;
                                                    foreach($DirectExpLedgerWiseCommodityWise as $DExpKey=>$DExpVal){
                                                        if($DExpVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $lastFy){
                                                                $DrAmtPre += $DExpVal["SumAmt"];
                                                            }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $lastFy){
                                                                $CrAmtPre += $DExpVal["SumAmt"];
                                                            }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $FY){
                                                                $DrAmt += $DExpVal["SumAmt"];
                                                            }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $FY){
                                                                $CrAmt += $DExpVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $bal = $DrAmt - $CrAmt;
                                                    $balPre = $DrAmtPre - $CrAmtPre;
                                                    $TotalAct1 += $bal;
                                                    $PreTotalAct1 += $balPre;
                                                    ?>
                                                    <td style="text-align:right;font-weight:600;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                    <td style="text-align:right;font-weight:600;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <?php
                                                }
                                            ?>
                                                <?php
                                                    $Unbal = 0;
                                                    $UnCrAmt = 0;
                                                    $UnDrAmt = 0;
                                                    $UnbalPre = 0;
                                                    $UnCrAmtPre = 0;
                                                    $UnDrAmtPre = 0;
                                                    foreach($DirectExpLedgerWiseCommodityWise as $DExpKey=>$DExpVal){
                                                        if($DExpVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $lastFy){
                                                                $UnDrAmtPre += $DExpVal["SumAmt"];
                                                            }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $lastFy){
                                                                $UnCrAmtPre += $DExpVal["SumAmt"];
                                                            }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $FY){
                                                                $UnDrAmt += $DExpVal["SumAmt"];
                                                            }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $FY){
                                                                $UnCrAmt += $DExpVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $Unbal = $UnDrAmt - $UnCrAmt;
                                                    $UnbalPre = $unDrAmtPre - $UnCrAmtPre;
                                                    $TotalAct1 += $Unbal;
                                                    $PreTotalAct1 += $UnbalPre;
                                                ?>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($UnbalPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($Unbal, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($PreTotalAct1, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($TotalAct1, 2, '.', '') ?></td>
                                                </tr>
                                        <?php   
                                                
                                                foreach($ActGroup2UniqueList as $val2){
                                                    if($ActGrp1["AccountID"] == $val2["AccountID1"]){
                                                        $PreTotalAct2 = 0;
                                                        $TotalAct2 = 0;
                                        ?>
                                                        <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
                                                        style="font-size:13px;" id="subgroup" data-id="<?php echo $val2["AccountID"] ?>">
                                                        <td class="col-id-sr-no"></td>
                                                        <td class="col-id-particular"><?php echo $val2["AccountName"]; ?></td>    
                                        <?php
                                                        foreach($CommodityList as $val){
                                                            $bal = 0;
                                                            $CrAmt = 0;
                                                            $DrAmt = 0;
                                                            $balPre = 0;
                                                            $CrAmtPre = 0;
                                                            $DrAmtPre = 0;
                                                            foreach($DirectExpLedgerWiseCommodityWise as $DExpKey=>$DExpVal){
                                                                if($DExpVal["SubActGroupID"] == $val2["AccountID"]){
                                                                    if($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $lastFy){
                                                                        $DrAmtPre += $DExpVal["SumAmt"];
                                                                    }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $lastFy){
                                                                        $CrAmtPre += $DExpVal["SumAmt"];
                                                                    }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $FY){
                                                                        $DrAmt += $DExpVal["SumAmt"];
                                                                    }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $FY){
                                                                        $CrAmt += $DExpVal["SumAmt"];
                                                                    }
                                                                }
                                                            }
                                                            $bal = $DrAmt - $CrAmt;
                                                            $balPre = $DrAmtPre - $CrAmtPre;
                                                            $TotalAct2 += $bal;
                                                            $PreTotalAct2 += $balPre;
                                                        ?>  
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                        <?php
                                                        }
                                                        ?>
                                                        <?php
                                                            $Unbal = 0;
                                                            $UnCrAmt = 0;
                                                            $UnDrAmt = 0;
                                                            $UnbalPre = 0;
                                                            $UnCrAmtPre = 0;
                                                            $UnDrAmtPre = 0;
                                                            foreach($DirectExpLedgerWiseCommodityWise as $DExpKey=>$DExpVal){
                                                                if($DExpVal["SubActGroupID"] == $val2["AccountID"]){
                                                                    if($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $lastFy){
                                                                        $UnDrAmtPre += $DExpVal["SumAmt"];
                                                                    }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $lastFy){
                                                                        $UnCrAmtPre += $DExpVal["SumAmt"];
                                                                    }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $FY){
                                                                        $UnDrAmt += $DExpVal["SumAmt"];
                                                                    }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $FY){
                                                                        $UnCrAmt += $DExpVal["SumAmt"];
                                                                    }
                                                                }
                                                            }
                                                            $Unbal = $UnDrAmt - $UnCrAmt;
                                                            $UnbalPre = $unDrAmtPre - $UnCrAmtPre;
                                                            $TotalAct2 += $Unbal;
                                                            $PreTotalAct2 += $UnbalPre;
                                                        ?>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($UnbalPre, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($Unbal, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($PreTotalAct2, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($TotalAct2, 2, '.', '') ?></td>
                                                            </tr>
                                                        <?php
                                                            foreach($AccountUniqueList as $ActList){
                                                                if($ActList["AccountID2"] == $val2["AccountID"]){
                                                                    $PreTotalAct = 0;
                                                                    $TotalAct = 0;
                                                                ?>
                                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $ActList["AccountID"] ?>">
                                                                    <td class="col-id-sr-no"></td>
                                                                    <td class="col-id-particular"><?php echo $ActList["AccountName"]; ?></td>  
                                                                <?php
                                                                    foreach($CommodityList as $val){
                                                                        $bal = 0;
                                                                        $CrAmt = 0;
                                                                        $DrAmt = 0;
                                                                        $balPre = 0;
                                                                        $CrAmtPre = 0;
                                                                        $DrAmtPre = 0;
                                                                        foreach($DirectExpLedgerWiseCommodityWise as $DExpKey=>$DExpVal){
                                                                            if($DExpVal["AccountID"] == $ActList["AccountID"]){
                                                                                if($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $lastFy){
                                                                                    $DrAmtPre += $DExpVal["SumAmt"];
                                                                                }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $lastFy){
                                                                                    $CrAmtPre += $DExpVal["SumAmt"];
                                                                                }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $FY){
                                                                                    $DrAmt += $DExpVal["SumAmt"];
                                                                                }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $FY){
                                                                                    $CrAmt += $DExpVal["SumAmt"];
                                                                                }
                                                                            }
                                                                        }
                                                                        $bal = $DrAmt - $CrAmt;
                                                                        $balPre = $DrAmtPre - $CrAmtPre;
                                                                        $TotalAct += $bal;
                                                                        $PreTotalAct += $balPre;
                                                                ?>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                <?php
                                                                    }
                                                                ?>
                                                                <?php
                                                                    $Unbal = 0;
                                                                    $UnCrAmt = 0;
                                                                    $UnDrAmt = 0;
                                                                    $UnbalPre = 0;
                                                                    $UnCrAmtPre = 0;
                                                                    $UnDrAmtPre = 0;
                                                                    foreach($DirectExpLedgerWiseCommodityWise as $DExpKey=>$DExpVal){
                                                                        if($DExpVal["AccountID"] == $ActList["AccountID"]){
                                                                            if($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $lastFy){
                                                                                $UnDrAmtPre += $DExpVal["SumAmt"];
                                                                            }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $lastFy){
                                                                                $UnCrAmtPre += $DExpVal["SumAmt"];
                                                                            }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $FY){
                                                                                $UnDrAmt += $DExpVal["SumAmt"];
                                                                            }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $FY){
                                                                                $UnCrAmt += $DExpVal["SumAmt"];
                                                                            }
                                                                        }
                                                                    }
                                                                    $Unbal = $UnDrAmt - $UnCrAmt;
                                                                    $UnbalPre = $unDrAmtPre - $UnCrAmtPre;
                                                                    $TotalAct += $Unbal;
                                                                    $PreTotalAct += $UnbalPre;
                                                                ?>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($PreTotalAct, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($TotalAct, 2, '.', '') ?></td>
                                                                    </tr>
                                                                <?php
                                                                    $SubCounter3++;
                                                                }
                                                            }
                                                            
                                                        $SubCounter2++;
                                                    }
                                                }
                                                $SubCounter1++;
                                            }
                                        ?>
                                        <?php $MainCounter++;?>
                                        
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node"
                                            style="font-size:14px;background-color: red;"id="maingroup">
                                            <td class="parent col-id-sr-no" >4</td>
                                            <td class="parent col-id-particular" >SEGMENT RESULT (COGS)</td>
                                            <?php
                                            $TotalBal = 0;
                                            $TotalBalPre = 0;
                                            $COGSUn = 0;
                                            $COGSUnPre = 0;
                                            $i = 0;
                                            foreach($CommodityList as $val){
                                                $COGS = 0;
                                                $COGSPre = 0;
                                                $bal = 0;
                                                $CrAmt = 0;
                                                $DrAmt = 0;
                                                $balPre = 0;
                                                $CrAmtPre = 0;
                                                $DrAmtPre = 0;
                                                
                                                foreach($DirectExpLedgerWiseCommodityWise as $DExpKey=>$DExpVal){
                                                    if($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $lastFy){
                                                        $DrAmtPre += $DExpVal["SumAmt"];
                                                    }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $lastFy){
                                                        $CrAmtPre += $DExpVal["SumAmt"];
                                                    }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $FY){
                                                        $DrAmt += $DExpVal["SumAmt"];
                                                    }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $FY){
                                                        $CrAmt += $DExpVal["SumAmt"];
                                                    }
                                                }
                                                $bal = $DrAmt - $CrAmt;
                                                $balPre = $DrAmtPre - $CrAmtPre;
                                                
                                                
                                                // Opening, Purchase, Closing Amt 
                                                $OpnAmt = 0;
                                                $PurchAmt = 0;
                                                $ClsAmt = 0;
                                                $OpnAmtPre = 0;
                                                $PurchAmtPre = 0;
                                                $ClsAmtPre = 0;
                                                foreach($CommodityWiseInventoryAmt as $IKey=>$Ival){
                                                    if($Ival["id"] == $val['id']){
                                                        $OpnAmt = $Ival["OpnBal"];
                                                        $PurchAmt = $Ival["PurchAmt"];
                                                        $ClsAmt = $Ival["CurrentValue"];
                                                        $OpnAmtPre = $Ival["OpnBalPre"];
                                                        $PurchAmtPre = $Ival["PurchAmtPre"];
                                                        $ClsAmtPre = $Ival["CurrentValuePre"];
                                                    }
                                                }
                                                $COGS = $OpnAmt + $PurchAmt + $bal - $ClsAmt;
                                                $COGSPre = $OpnAmtPre + $PurchAmtPre + $balPre - $ClsAmtPre;
                                                $TotalBal += $COGS;
                                                $TotalBalPre += $COGSPre;
                                                $CommodityList[$i]["COGS"] = $COGS;
                                                $CommodityList[$i]["COGSPre"] = $COGSPre;
                                                ?>
                                                <td style="text-align:right;font-weight:700;"><?php echo  number_format($COGSPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:700;"><?php echo  number_format($COGS, 2, '.', '') ?></td>
                                            <?php
                                            $i++;
                                            }
                                                $balUn = 0;
                                                $CrAmtUn = 0;
                                                $DrAmtUn = 0;
                                                $balUnPre = 0;
                                                $CrAmtUnPre = 0;
                                                $DrAmtUnPre = 0;
                                                foreach($DirectExpLedgerWiseCommodityWise as $DExpKey=>$DExpVal){
                                                    if($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $lastFy){
                                                        $DrAmtUnPre += $DExpVal["SumAmt"];
                                                    }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $lastFy){
                                                        $CrAmtUnPre += $DExpVal["SumAmt"];
                                                    }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $FY){
                                                        $DrAmtUn += $DExpVal["SumAmt"];
                                                    }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $FY){
                                                        $CrAmtUn += $DExpVal["SumAmt"];
                                                    }
                                                }
                                                $balUn = $DrAmtUn - $CrAmtUn;
                                                $balUnPre = $DrAmtUnPre - $CrAmtUnPre;
                                                $COGSUn = $balUn;
                                                $COGSUnPre = $balUnPre;
                                                $TotalBal += $COGSUn;
                                                $TotalBalPre += $COGSUnPre;
                                                $UnallocatedObj->COGS = $COGSUn;
                                                $UnallocatedObj->COGSPre = $COGSUnPre;
                                            ?>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($COGSUnPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($COGSUn, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($TotalBalPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($TotalBal, 2, '.', '') ?></td>
                                        </tr>   
                                            
                                            <tr class="treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node expanded "
                                                style="font-size:13px;" id="subgroup">
                                                <td class="col-id-sr-no"></td>
                                                <td class="col-id-particular">Opening Amt - add</td>
                                            <?php
                                                $TotalOpnAmt = 0;
                                                $TotalOpnAmtPre = 0;
                                                foreach($CommodityList as $val){
                                                    // Opening, Purchase, Closing Amt 
                                                    $OpnAmt = 0;
                                                    $OpnAmtPre = 0;
                                                    foreach($CommodityWiseInventoryAmt as $IKey=>$Ival){
                                                        if($Ival["id"] == $val['id']){
                                                            $OpnAmt = $Ival["OpnBal"];
                                                            $OpnAmtPre = $Ival["OpnBalPre"];
                                                        }
                                                    }
                                                    $TotalOpnAmt += $OpnAmt;
                                                    $TotalOpnAmtPre += $OpnAmtPre;
                                                ?>
                                                <td style="text-align:right;"><?php echo  number_format($OpnAmtPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;background-color: orange;"><?php echo  number_format($OpnAmt, 2, '.', '') ?></td>
                                            <?php
                                                }
                                            ?>
                                                <td style="text-align:right;">0.00</td>
                                                <td style="text-align:right;background-color: orange;">0.00</td>
                                                <td style="text-align:right;"><?php echo  number_format($TotalOpnAmtPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;background-color: orange;"><?php echo  number_format($TotalOpnAmt, 2, '.', '') ?></td>
                                            </tr>
                                            <?php $SubCounter1++;?>
                                            
                                            <tr class="treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node expanded "
                                                style="font-size:13px;" id="subgroup">
                                                <td class="col-id-sr-no"></td>
                                                <td class="col-id-particular">Purchase Amt - add</td>
                                            <?php
                                                $TotalPurchAmt = 0;
                                                $TotalPurchAmtPre = 0;
                                                foreach($CommodityList as $val){
                                                    // Opening, Purchase, Closing Amt 
                                                    $PurchAmt = 0;
                                                    $PurchAmtPre = 0;
                                                    foreach($CommodityWiseInventoryAmt as $IKey=>$Ival){
                                                        if($Ival["id"] == $val['id']){
                                                            $PurchAmt = $Ival["PurchAmt"];
                                                            $PurchAmtPre = $Ival["PurchAmtPre"];
                                                        }
                                                    }
                                                    $TotalPurchAmt += $PurchAmt;
                                                    $TotalPurchAmtPre += $PurchAmtPre;
                                                ?>
                                                <td style="text-align:right;"><?php echo  number_format($PurchAmtPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;background-color: orange;"><?php echo  number_format($PurchAmt, 2, '.', '') ?></td>
                                            <?php
                                                }
                                            ?>
                                                <td style="text-align:right;">0.00</td>
                                                <td style="text-align:right;background-color: orange;">0.00</td>
                                                <td style="text-align:right;"><?php echo  number_format($TotalPurchAmtPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;background-color: orange;"><?php echo  number_format($TotalPurchAmt, 2, '.', '') ?></td>
                                            </tr>
                                            <?php $SubCounter1++;?>
                                            
                                            <tr class="treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node expanded "
                                                style="font-size:13px;" id="subgroup">
                                                <td class="col-id-sr-no"></td>
                                                <td class="col-id-particular">Direct Expense - add</td>
                                                <?php
                                                $TotalBal = 0;
                                                $TotalBalPre = 0;
                                                foreach($CommodityList as $val){
                                                    $bal = 0;
                                                    $CrAmt = 0;
                                                    $DrAmt = 0;
                                                    $balPre = 0;
                                                    $CrAmtPre = 0;
                                                    $DrAmtPre = 0;
                                                    
                                                    foreach($DirectExpLedgerWiseCommodityWise as $DExpKey=>$DExpVal){
                                                        if($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $lastFy){
                                                            $DrAmtPre += $DExpVal["SumAmt"];
                                                        }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $lastFy){
                                                            $CrAmtPre += $DExpVal["SumAmt"];
                                                        }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $FY){
                                                            $DrAmt += $DExpVal["SumAmt"];
                                                        }elseif($val['id']==$DExpVal["subgroup_id"] && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $FY){
                                                            $CrAmt += $DExpVal["SumAmt"];
                                                        }
                                                    }
                                                    $bal = $DrAmt - $CrAmt;
                                                    $balPre = $DrAmtPre - $CrAmtPre;
                                                    
                                                    
                                                    $TotalBal += $bal;
                                                    $TotalBalPre += $balPre;
                                                    ?>
                                                    <td style="text-align:right;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                    <td style="text-align:right;background-color: orange;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                <?php
                                                }
                                                    $balUn = 0;
                                                    $CrAmtUn = 0;
                                                    $DrAmtUn = 0;
                                                    $balUnPre = 0;
                                                    $CrAmtUnPre = 0;
                                                    $DrAmtUnPre = 0;
                                                    foreach($DirectExpLedgerWiseCommodityWise as $DExpKey=>$DExpVal){
                                                        if($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $lastFy){
                                                            $DrAmtUnPre += $DExpVal["SumAmt"];
                                                        }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $lastFy){
                                                            $CrAmtUnPre += $DExpVal["SumAmt"];
                                                        }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "D" && $DExpVal["FY"] == $FY){
                                                            $DrAmtUn += $DExpVal["SumAmt"];
                                                        }elseif($DExpVal["subgroup_id"] == "" && $DExpVal["TType"] == "C" && $DExpVal["FY"] == $FY){
                                                            $CrAmtUn += $DExpVal["SumAmt"];
                                                        }
                                                    }
                                                    $balUn = $DrAmtUn - $CrAmtUn;
                                                    $balUnPre = $DrAmtUnPre - $CrAmtUnPre;
                                                    $TotalBal += $balUn;
                                                    $TotalBalPre += $balUnPre;
                                                ?>
                                                <td style="text-align:right;"><?php echo  number_format($balUnPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;background-color: orange;"><?php echo  number_format($balUn, 2, '.', '') ?></td>
                                                <td style="text-align:right;"><?php echo  number_format($TotalBalPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;background-color: orange;"><?php echo  number_format($TotalBal, 2, '.', '') ?></td>
                                            </tr>
                                            <?php $SubCounter1++;?>
                                            <tr class="treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node  "
                                                style="font-size:13px;" id="subgroup">
                                                <td class="col-id-sr-no"></td>
                                                <td class="col-id-particular">Closing Amt - less</td>
                                            <?php
                                                $TotalClsAmt = 0;
                                                $TotalClsAmtPre = 0;
                                                foreach($CommodityList as $val){
                                                    // Opening, Purchase, Closing Amt 
                                                    $ClsAmt = 0;
                                                    $ClsAmtPre = 0;
                                                    foreach($CommodityWiseInventoryAmt as $IKey=>$Ival){
                                                        if($Ival["id"] == $val['id']){
                                                            $ClsAmt = $Ival["CurrentValue"];
                                                            $ClsAmtPre = $Ival["CurrentValuePre"];
                                                        }
                                                    }
                                                    $TotalClsAmt += $ClsAmt;
                                                    $TotalClsAmtPre += $ClsAmtPre;
                                                ?>
                                                <td style="text-align:right;"><?php echo  number_format($ClsAmtPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;background-color: orange;"><?php echo  number_format($ClsAmt, 2, '.', '') ?></td>
                                            <?php
                                                }
                                            ?>
                                                <td style="text-align:right;">0.00</td>
                                                <td style="text-align:right;background-color: orange;">0.00</td>
                                                <td style="text-align:right;"><?php echo  number_format($TotalClsAmtPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;background-color: orange;"><?php echo  number_format($TotalClsAmt, 2, '.', '') ?></td>
                                            </tr>
                                            <?php $SubCounter1++;?>
                                            
                                        <?php $MainCounter++;?>
                                        
                                        <?php
                                            $ActGroup1List = array();
                                            $ActGroup2List = array();
                                            $AccountList = array();
                                            foreach($OthIncLedgerWiseCommodityWise as $OIncKey=>$OIncVal){
                                                $new1 = array(
                                                    "AccountID"=>$OIncVal["SubActGroupID1"],
                                                    "AccountName"=>$OIncVal["SubActGroupName1"]
                                                );
                                                array_push($ActGroup1List,$new1);
                                                $new2 = array(
                                                    "AccountID1"=>$OIncVal["SubActGroupID1"],
                                                    "AccountID"=>$OIncVal["SubActGroupID"],
                                                    "AccountName"=>$OIncVal["SubActGroupName2"]
                                                );
                                                array_push($ActGroup2List,$new2);
                                                
                                                $new = array(
                                                    "AccountID2"=>$OIncVal["SubActGroupID"],
                                                    "AccountID"=>$OIncVal["AccountID"],
                                                    "AccountName"=>$OIncVal["company"]
                                                );
                                                array_push($AccountList,$new);
                                            }
                                            $ActGroup1UniqueList = array_unique($ActGroup1List,SORT_REGULAR);
                                            $ActGroup2UniqueList = array_unique($ActGroup2List,SORT_REGULAR);
                                            $AccountUniqueList = array_unique($AccountList,SORT_REGULAR);
                                        ?>
                                        
                                        
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node"
                                            style="font-size:14px;"id="maingroup">
                                            <td class="parent col-id-sr-no">5</td>
                                            <td class="parent col-id-particular" ><b>OTHER INCOME</b> </td>
                                            <?php
                                            $TotalBal = 0;
                                            $TotalBalPre = 0;
                                            $i = 0;  
                                            foreach($CommodityList as $val){
                                                $bal = 0;
                                                $CrAmt = 0;
                                                $DrAmt = 0;
                                                $balPre = 0;
                                                $CrAmtPre = 0;
                                                $DrAmtPre = 0;
                                                
                                                
                                                foreach($OthIncLedgerWiseCommodityWise as $OIncKey=>$OIncVal){
                                                    //Total Direct Expenses
                                                    if($val['id']==$OIncVal["subgroup_id"] && $OIncVal["TType"] == "D" && $OIncVal["FY"] == $lastFy){
                                                        $DrAmtPre += $OIncVal["SumAmt"];
                                                    }elseif($val['id']==$OIncVal["subgroup_id"] && $OIncVal["TType"] == "C" && $OIncVal["FY"] == $lastFy){
                                                        $CrAmtPre += $OIncVal["SumAmt"];
                                                    }elseif($val['id']==$OIncVal["subgroup_id"] && $OIncVal["TType"] == "D" && $OIncVal["FY"] == $FY){
                                                        $DrAmt += $OIncVal["SumAmt"];
                                                    }elseif($val['id']==$OIncVal["subgroup_id"] && $OIncVal["TType"] == "C" && $OIncVal["FY"] == $FY){
                                                        $CrAmt += $OIncVal["SumAmt"];
                                                    }
                                                }
                                                
                                                $bal =  $CrAmt - $DrAmt;
                                                $balPre = $CrAmtPre - $DrAmtPre;
                                                
                                                $CommodityList[$i]['OthInc'] = $bal; 
                                                $CommodityList[$i]['OthIncPre'] = $balPre;
                                                $TotalBal += $bal;
                                                $TotalBalPre += $balPre;
                                                ?>
                                                <td style="text-align:right;font-weight:700;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <?php
                                            $i++;
                                            }
                                                
                                                $balUn = 0;
                                                $CrAmtUn = 0;
                                                $DrAmtUn = 0;
                                                $balUnPre = 0;
                                                $CrAmtUnPre = 0;
                                                $DrAmtUnPre = 0;
                                                foreach($OthIncLedgerWiseCommodityWise as $OIncKey=>$OIncVal){
                                                    if($OIncVal["subgroup_id"] == "" && $OIncVal["TType"] == "D" && $OIncVal["FY"] == $lastFy){
                                                        $DrAmtUnPre += $OIncVal["SumAmt"];
                                                    }elseif($OIncVal["subgroup_id"] == "" && $OIncVal["TType"] == "C" && $OIncVal["FY"] == $lastFy){
                                                        $CrAmtUnPre += $OIncVal["SumAmt"];
                                                    }elseif($OIncVal["subgroup_id"] == "" && $OIncVal["TType"] == "D" && $OIncVal["FY"] == $FY){
                                                        $DrAmtUn += $OIncVal["SumAmt"];
                                                    }elseif($OIncVal["subgroup_id"] == "" && $OIncVal["TType"] == "C" && $OIncVal["FY"] == $FY){
                                                        $CrAmtUn += $OIncVal["SumAmt"];
                                                    }
                                                }
                                                
                                                $balUn =  $CrAmtUn - $DrAmtUn;
                                                $balUnPre = $CrAmtUnPre - $DrAmtUnPre;
                                                $UnallocatedObj->OthInc = $balUn;
                                                $UnallocatedObj->OthIncPre = $balUnPre;
                                                $TotalBal += $balUn;
                                                $TotalBalPre += $balUnPre;
                                            ?>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($balUnPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($balUn, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($TotalBalPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($TotalBal, 2, '.', '') ?></td>
                                        </tr>
                                        <?php $SubCounter++; ?>
                                        <?php 
                                            
                                            foreach($ActGroup1UniqueList as $ActGrp1){
                                                $TotalAct1 = 0;
                                                $PreTotalAct1 = 0;
                                        ?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node  "
                                                style="font-size:13px;" id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
                                                <td class="col-id-sr-no"></td>
                                                <td class="col-id-particular"><?php echo $ActGrp1["AccountName"]; ?></td>
                                        <?php 
                                                foreach($CommodityList as $val){
                                                    $bal = 0;
                                                    $CrAmt = 0;
                                                    $DrAmt = 0;
                                                    $balPre = 0;
                                                    $CrAmtPre = 0;
                                                    $DrAmtPre = 0;
                                                    foreach($OthIncLedgerWiseCommodityWise as $OIncKey=>$OIncVal){
                                                        if($OIncVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($val['id']==$OIncVal["subgroup_id"] && $OIncVal["TType"] == "D" && $OIncVal["FY"] == $lastFy){
                                                                $DrAmtPre += $OIncVal["SumAmt"];
                                                            }elseif($val['id']==$OIncVal["subgroup_id"] && $OIncVal["TType"] == "C" && $OIncVal["FY"] == $lastFy){
                                                                $CrAmtPre += $OIncVal["SumAmt"];
                                                            }elseif($val['id']==$OIncVal["subgroup_id"] && $OIncVal["TType"] == "D" && $OIncVal["FY"] == $FY){
                                                                $DrAmt += $OIncVal["SumAmt"];
                                                            }elseif($val['id']==$OIncVal["subgroup_id"] && $OIncVal["TType"] == "C" && $OIncVal["FY"] == $FY){
                                                                $CrAmt += $OIncVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $bal = $CrAmt - $DrAmt;
                                                    $balPre = $CrAmtPre - $DrAmtPre;
                                                    $TotalAct1 += $bal;
                                                    $PreTotalAct1 += $balPre;
                                                    ?>
                                                    <td style="text-align:right;font-weight:600;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                    <td style="text-align:right;font-weight:600;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <?php
                                                }
                                            ?>
                                                <?php
                                                    $Unbal = 0;
                                                    $UnCrAmt = 0;
                                                    $UnDrAmt = 0;
                                                    $UnbalPre = 0;
                                                    $UnCrAmtPre = 0;
                                                    $UnDrAmtPre = 0;
                                                    foreach($OthIncLedgerWiseCommodityWise as $OIncKey=>$OIncVal){
                                                        if($OIncVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($OIncVal["subgroup_id"] == "" && $OIncVal["TType"] == "D" && $OIncVal["FY"] == $lastFy){
                                                                $UnDrAmtPre += $OIncVal["SumAmt"];
                                                            }elseif($OIncVal["subgroup_id"] == "" && $OIncVal["TType"] == "C" && $OIncVal["FY"] == $lastFy){
                                                                $UnCrAmtPre += $OIncVal["SumAmt"];
                                                            }elseif($OIncVal["subgroup_id"] == "" && $OIncVal["TType"] == "D" && $OIncVal["FY"] == $FY){
                                                                $UnDrAmt += $OIncVal["SumAmt"];
                                                            }elseif($OIncVal["subgroup_id"] == "" && $OIncVal["TType"] == "C" && $OIncVal["FY"] == $FY){
                                                                $UnCrAmt += $OIncVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $Unbal = $UnCrAmt - $UnDrAmt;
                                                    $UnbalPre = $UnCrAmtPre - $unDrAmtPre;
                                                    $TotalAct1 += $Unbal;
                                                    $PreTotalAct1 += $UnbalPre;
                                                ?>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($UnbalPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($Unbal, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($PreTotalAct1, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($TotalAct1, 2, '.', '') ?></td>
                                                </tr>
                                        <?php   
                                                
                                                foreach($ActGroup2UniqueList as $val2){
                                                    if($ActGrp1["AccountID"] == $val2["AccountID1"]){
                                                        $PreTotalAct2 = 0;
                                                        $TotalAct2 = 0;
                                        ?>
                                                        <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
                                                        style="font-size:13px;" id="subgroup" data-id="<?php echo $val2["AccountID"] ?>">
                                                        <td class="col-id-sr-no"></td>
                                                        <td class="col-id-particular"><?php echo $val2["AccountName"]; ?></td>    
                                        <?php
                                                        foreach($CommodityList as $val){
                                                            $bal = 0;
                                                            $CrAmt = 0;
                                                            $DrAmt = 0;
                                                            $balPre = 0;
                                                            $CrAmtPre = 0;
                                                            $DrAmtPre = 0;
                                                            foreach($OthIncLedgerWiseCommodityWise as $OIncKey=>$OIncVal){
                                                                if($OIncVal["SubActGroupID"] == $val2["AccountID"]){
                                                                    if($val['id']==$OIncVal["subgroup_id"] && $OIncVal["TType"] == "D" && $OIncVal["FY"] == $lastFy){
                                                                        $DrAmtPre += $OIncVal["SumAmt"];
                                                                    }elseif($val['id']==$OIncVal["subgroup_id"] && $OIncVal["TType"] == "C" && $OIncVal["FY"] == $lastFy){
                                                                        $CrAmtPre += $OIncVal["SumAmt"];
                                                                    }elseif($val['id']==$OIncVal["subgroup_id"] && $OIncVal["TType"] == "D" && $OIncVal["FY"] == $FY){
                                                                        $DrAmt += $OIncVal["SumAmt"];
                                                                    }elseif($val['id']==$OIncVal["subgroup_id"] && $OIncVal["TType"] == "C" && $OIncVal["FY"] == $FY){
                                                                        $CrAmt += $OIncVal["SumAmt"];
                                                                    }
                                                                }
                                                            }
                                                            $bal = $CrAmt - $DrAmt;
                                                            $balPre = $CrAmtPre - $DrAmtPre;
                                                            $TotalAct2 += $bal;
                                                            $PreTotalAct2 += $balPre;
                                                        ?>  
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                        <?php
                                                        }
                                                        ?>
                                                        <?php
                                                            $Unbal = 0;
                                                            $UnCrAmt = 0;
                                                            $UnDrAmt = 0;
                                                            $UnbalPre = 0;
                                                            $UnCrAmtPre = 0;
                                                            $UnDrAmtPre = 0;
                                                            foreach($OthIncLedgerWiseCommodityWise as $OIncKey=>$OIncVal){
                                                                if($OIncVal["SubActGroupID"] == $val2["AccountID"]){
                                                                    if($OIncVal["subgroup_id"] == "" && $OIncVal["TType"] == "D" && $OIncVal["FY"] == $lastFy){
                                                                        $UnDrAmtPre += $OIncVal["SumAmt"];
                                                                    }elseif($OIncVal["subgroup_id"] == "" && $OIncVal["TType"] == "C" && $OIncVal["FY"] == $lastFy){
                                                                        $UnCrAmtPre += $OIncVal["SumAmt"];
                                                                    }elseif($OIncVal["subgroup_id"] == "" && $OIncVal["TType"] == "D" && $OIncVal["FY"] == $FY){
                                                                        $UnDrAmt += $OIncVal["SumAmt"];
                                                                    }elseif($OIncVal["subgroup_id"] == "" && $OIncVal["TType"] == "C" && $OIncVal["FY"] == $FY){
                                                                        $UnCrAmt += $OIncVal["SumAmt"];
                                                                    }
                                                                }
                                                            }
                                                            $Unbal = $UnCrAmt - $UnDrAmt;
                                                            $UnbalPre = $UnCrAmtPre - $unDrAmtPre;
                                                            $TotalAct2 += $Unbal;
                                                            $PreTotalAct2 += $UnbalPre;
                                                        ?>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($UnbalPre, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($Unbal, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($PreTotalAct2, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($TotalAct2, 2, '.', '') ?></td>
                                                            </tr>
                                                        <?php
                                                            foreach($AccountUniqueList as $ActList){
                                                                if($ActList["AccountID2"] == $val2["AccountID"]){
                                                                    $PreTotalAct = 0;
                                                                    $TotalAct = 0;
                                                                ?>
                                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $ActList["AccountID"] ?>">
                                                                    <td class="col-id-sr-no"></td>
                                                                    <td class="col-id-particular"><?php echo $ActList["AccountName"]; ?></td>  
                                                                <?php
                                                                    foreach($CommodityList as $val){
                                                                        $bal = 0;
                                                                        $CrAmt = 0;
                                                                        $DrAmt = 0;
                                                                        $balPre = 0;
                                                                        $CrAmtPre = 0;
                                                                        $DrAmtPre = 0;
                                                                        foreach($OthIncLedgerWiseCommodityWise as $OIncKey=>$OIncVal){
                                                                            if($OIncVal["AccountID"] == $ActList["AccountID"]){
                                                                                if($val['id']==$OIncVal["subgroup_id"] && $OIncVal["TType"] == "D" && $OIncVal["FY"] == $lastFy){
                                                                                    $DrAmtPre += $DExpVal["SumAmt"];
                                                                                }elseif($val['id']==$OIncVal["subgroup_id"] && $OIncVal["TType"] == "C" && $OIncVal["FY"] == $lastFy){
                                                                                    $CrAmtPre += $OIncVal["SumAmt"];
                                                                                }elseif($val['id']==$OIncVal["subgroup_id"] && $OIncVal["TType"] == "D" && $OIncVal["FY"] == $FY){
                                                                                    $DrAmt += $OIncVal["SumAmt"];
                                                                                }elseif($val['id']==$OIncVal["subgroup_id"] && $OIncVal["TType"] == "C" && $OIncVal["FY"] == $FY){
                                                                                    $CrAmt += $OIncVal["SumAmt"];
                                                                                }
                                                                            }
                                                                        }
                                                                        $bal = $CrAmt - $DrAmt;
                                                                        $balPre = $CrAmtPre - $DrAmtPre;
                                                                        $TotalAct += $bal;
                                                                        $PreTotalAct += $balPre;
                                                                ?>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                <?php
                                                                    }
                                                                ?>
                                                                <?php
                                                                    $Unbal = 0;
                                                                    $UnCrAmt = 0;
                                                                    $UnDrAmt = 0;
                                                                    $UnbalPre = 0;
                                                                    $UnCrAmtPre = 0;
                                                                    $UnDrAmtPre = 0;
                                                                    foreach($OthIncLedgerWiseCommodityWise as $OIncKey=>$OIncVal){
                                                                        if($OIncVal["AccountID"] == $ActList["AccountID"]){
                                                                            if($OIncVal["subgroup_id"] == "" && $OIncVal["TType"] == "D" && $OIncVal["FY"] == $lastFy){
                                                                                $UnDrAmtPre += $OIncVal["SumAmt"];
                                                                            }elseif($OIncVal["subgroup_id"] == "" && $OIncVal["TType"] == "C" && $OIncVal["FY"] == $lastFy){
                                                                                $UnCrAmtPre += $OIncVal["SumAmt"];
                                                                            }elseif($OIncVal["subgroup_id"] == "" && $OIncVal["TType"] == "D" && $OIncVal["FY"] == $FY){
                                                                                $UnDrAmt += $OIncVal["SumAmt"];
                                                                            }elseif($OIncVal["subgroup_id"] == "" && $OIncVal["TType"] == "C" && $OIncVal["FY"] == $FY){
                                                                                $UnCrAmt += $OIncVal["SumAmt"];
                                                                            }
                                                                        }
                                                                    }
                                                                    $Unbal = $UnCrAmt - $UnDrAmt;
                                                                    $UnbalPre = $UnCrAmtPre - $unDrAmtPre;
                                                                    $TotalAct += $Unbal;
                                                                    $PreTotalAct += $UnbalPre;
                                                                ?>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($PreTotalAct, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($TotalAct, 2, '.', '') ?></td>
                                                                    </tr>
                                                                <?php
                                                                    $SubCounter3++;
                                                                }
                                                            }
                                                            
                                                        $SubCounter2++;
                                                    }
                                                }
                                                $SubCounter1++;
                                            }
                                        ?>
                                        <?php $MainCounter++;?>
                                        
                                        <?php
                                            $ActGroup1List = array();
                                            $ActGroup2List = array();
                                            $AccountList = array();
                                            foreach($IndExpLedgerWiseCommodityWise as $IndExpKey=>$IndExpVal){
                                                $new1 = array(
                                                    "AccountID"=>$IndExpVal["SubActGroupID1"],
                                                    "AccountName"=>$IndExpVal["SubActGroupName1"]
                                                );
                                                array_push($ActGroup1List,$new1);
                                                $new2 = array(
                                                    "AccountID1"=>$IndExpVal["SubActGroupID1"],
                                                    "AccountID"=>$IndExpVal["SubActGroupID"],
                                                    "AccountName"=>$IndExpVal["SubActGroupName2"]
                                                );
                                                array_push($ActGroup2List,$new2);
                                                
                                                $new = array(
                                                    "AccountID2"=>$IndExpVal["SubActGroupID"],
                                                    "AccountID"=>$IndExpVal["AccountID"],
                                                    "AccountName"=>$IndExpVal["company"]
                                                );
                                                array_push($AccountList,$new);
                                            }
                                            $ActGroup1UniqueList = array_unique($ActGroup1List,SORT_REGULAR);
                                            $ActGroup2UniqueList = array_unique($ActGroup2List,SORT_REGULAR);
                                            $AccountUniqueList = array_unique($AccountList,SORT_REGULAR);
                                        ?>
                                        
                                        
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node"
                                            style="font-size:14px;"id="maingroup">
                                            <td class="parent col-id-sr-no">6</td>
                                            <td class="parent col-id-particular" ><b>INDIRECT EXPENSE</b> </td>
                                            <?php
                                                $TotalBal = 0;
                                                $TotalBalPre = 0;
                                                $i = 0;
                                            foreach($CommodityList as $val){
                                                $bal = 0;
                                                $CrAmt = 0;
                                                $DrAmt = 0;
                                                $balPre = 0;
                                                $CrAmtPre = 0;
                                                $DrAmtPre = 0;
                                                $bal1 = 0;
                                                $CrAmt1 = 0;
                                                $DrAmt1 = 0;
                                                $balPre1 = 0;
                                                $CrAmtPre1 = 0;
                                                $DrAmtPre1 = 0;
                                                
                                                foreach($IndExpLedgerWiseCommodityWise as $IndExpKey=>$IndExpVal){
                                                    if($val['id']==$IndExpVal["subgroup_id"] && $IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $lastFy){
                                                        $DrAmtPre += $IndExpVal["SumAmt"];
                                                    }elseif($val['id']==$IndExpVal["subgroup_id"] && $IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $lastFy){
                                                        $CrAmtPre += $IndExpVal["SumAmt"];
                                                    }elseif($val['id']==$IndExpVal["subgroup_id"] && $IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $FY){
                                                        $DrAmt += $IndExpVal["SumAmt"];
                                                    }elseif($val['id']==$IndExpVal["subgroup_id"] && $IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $FY){
                                                        $CrAmt += $IndExpVal["SumAmt"];
                                                    }
                                                }
                                                
                                                $bal =  $CrAmt - $DrAmt;
                                                $balPre = $CrAmtPre - $DrAmtPre;
                                                
                                                $CommodityList[$i]['IndExp'] = $bal; 
                                                $CommodityList[$i]['IndExpPre'] = $balPre;
                                                $TotalBal += $bal;
                                                $TotalBalPre += $balPre;
                                                ?>
                                                <td style="text-align:right;font-weight:700;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <?php
                                            $i++;
                                            }
                                                
                                                $balUn = 0;
                                                $CrAmtUn = 0;
                                                $DrAmtUn = 0;
                                                $balUnPre = 0;
                                                $CrAmtUnPre = 0;
                                                $DrAmtUnPre = 0;
                                                foreach($IndExpLedgerWiseCommodityWise as $IndExpKey=>$IndExpVal){
                                                    if($IndExpVal["subgroup_id"] == "" && $IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $lastFy){
                                                        $DrAmtUnPre += $IndExpVal["SumAmt"];
                                                    }elseif($IndExpVal["subgroup_id"] == "" && $IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $lastFy){
                                                        $CrAmtUnPre += $IndExpVal["SumAmt"];
                                                    }elseif($IndExpVal["subgroup_id"] == "" && $IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $FY){
                                                        $DrAmtUn += $IndExpVal["SumAmt"];
                                                    }elseif($IndExpVal["subgroup_id"] == "" && $IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $FY){
                                                        $CrAmtUn += $IndExpVal["SumAmt"];
                                                    }
                                                }
                                                
                                                $balUn =  $CrAmtUn - $DrAmtUn;
                                                $balUnPre = $CrAmtUnPre - $DrAmtUnPre;
                                                $UnallocatedObj->IndExp = $balUn;
                                                $UnallocatedObj->IndExpPre = $balUnPre;
                                                $TotalBal += $balUn;
                                                $TotalBalPre += $balUnPre;
                                            ?>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($balUnPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($balUn, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($TotalBalPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($TotalBal, 2, '.', '') ?></td>
                                        </tr>
                                        <?php $SubCounter++; ?>
                                        <?php 
                                            
                                            foreach($ActGroup1UniqueList as $ActGrp1){
                                                $TotalAct1 = 0;
                                                $PreTotalAct1 = 0;
                                        ?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node  "
                                                style="font-size:13px;" id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
                                                <td class="col-id-sr-no"></td>
                                                <td class="col-id-particular"><?php echo $ActGrp1["AccountName"]; ?></td>
                                        <?php 
                                                foreach($CommodityList as $val){
                                                    $bal = 0;
                                                    $CrAmt = 0;
                                                    $DrAmt = 0;
                                                    $balPre = 0;
                                                    $CrAmtPre = 0;
                                                    $DrAmtPre = 0;
                                                    foreach($IndExpLedgerWiseCommodityWise as $IndExpKey=>$IndExpVal){
                                                        if($IndExpVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($val['id']==$IndExpVal["subgroup_id"] && $IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $lastFy){
                                                                $DrAmtPre += $IndExpVal["SumAmt"];
                                                            }elseif($val['id']==$IndExpVal["subgroup_id"] && $IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $lastFy){
                                                                $CrAmtPre += $IndExpVal["SumAmt"];
                                                            }elseif($val['id']==$IndExpVal["subgroup_id"] && $IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $FY){
                                                                $DrAmt += $IndExpVal["SumAmt"];
                                                            }elseif($val['id']==$IndExpVal["subgroup_id"] && $IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $FY){
                                                                $CrAmt += $IndExpVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $bal = $CrAmt - $DrAmt;
                                                    $balPre = $CrAmtPre - $DrAmtPre;
                                                    $TotalAct1 += $bal;
                                                    $PreTotalAct1 += $balPre;
                                                    ?>
                                                    <td style="text-align:right;font-weight:600;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                    <td style="text-align:right;font-weight:600;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <?php
                                                }
                                            ?>
                                                <?php
                                                    $Unbal = 0;
                                                    $UnCrAmt = 0;
                                                    $UnDrAmt = 0;
                                                    $UnbalPre = 0;
                                                    $UnCrAmtPre = 0;
                                                    $UnDrAmtPre = 0;
                                                    foreach($IndExpLedgerWiseCommodityWise as $IndExpKey=>$IndExpVal){
                                                        if($IndExpVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($IndExpVal["subgroup_id"] == "" && $IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $lastFy){
                                                                $UnDrAmtPre += $IndExpVal["SumAmt"];
                                                            }elseif($IndExpVal["subgroup_id"] == "" && $IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $lastFy){
                                                                $UnCrAmtPre += $IndExpVal["SumAmt"];
                                                            }elseif($IndExpVal["subgroup_id"] == "" && $IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $FY){
                                                                $UnDrAmt += $IndExpVal["SumAmt"];
                                                            }elseif($IndExpVal["subgroup_id"] == "" && $IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $FY){
                                                                $UnCrAmt += $IndExpVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $Unbal = $UnCrAmt - $UnDrAmt;
                                                    $UnbalPre = $UnCrAmtPre - $unDrAmtPre;
                                                    $TotalAct1 += $Unbal;
                                                    $PreTotalAct1 += $UnbalPre;
                                                ?>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($UnbalPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($Unbal, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($PreTotalAct1, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($TotalAct1, 2, '.', '') ?></td>
                                                </tr>
                                        <?php   
                                                
                                                foreach($ActGroup2UniqueList as $val2){
                                                    if($ActGrp1["AccountID"] == $val2["AccountID1"]){
                                                        $PreTotalAct2 = 0;
                                                        $TotalAct2 = 0;
                                        ?>
                                                        <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
                                                        style="font-size:13px;" id="subgroup" data-id="<?php echo $val2["AccountID"] ?>">
                                                        <td class="col-id-sr-no"></td>
                                                        <td class="col-id-particular"><?php echo $val2["AccountName"]; ?></td>    
                                        <?php
                                                        foreach($CommodityList as $val){
                                                            $bal = 0;
                                                            $CrAmt = 0;
                                                            $DrAmt = 0;
                                                            $balPre = 0;
                                                            $CrAmtPre = 0;
                                                            $DrAmtPre = 0;
                                                            foreach($IndExpLedgerWiseCommodityWise as $IndExpKey=>$IndExpVal){
                                                                if($IndExpVal["SubActGroupID"] == $val2["AccountID"]){
                                                                    if($val['id']==$IndExpVal["subgroup_id"] && $IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $lastFy){
                                                                        $DrAmtPre += $IndExpVal["SumAmt"];
                                                                    }elseif($val['id']==$IndExpVal["subgroup_id"] && $IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $lastFy){
                                                                        $CrAmtPre += $OIncVal["SumAmt"];
                                                                    }elseif($val['id']==$IndExpVal["subgroup_id"] && $IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $FY){
                                                                        $DrAmt += $IndExpVal["SumAmt"];
                                                                    }elseif($val['id']==$IndExpVal["subgroup_id"] && $IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $FY){
                                                                        $CrAmt += $IndExpVal["SumAmt"];
                                                                    }
                                                                }
                                                            }
                                                            $bal = $CrAmt - $DrAmt;
                                                            $balPre = $CrAmtPre - $DrAmtPre;
                                                            $TotalAct2 += $bal;
                                                            $PreTotalAct2 += $balPre;
                                                        ?>  
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                        <?php
                                                        }
                                                        ?>
                                                        <?php
                                                            $Unbal = 0;
                                                            $UnCrAmt = 0;
                                                            $UnDrAmt = 0;
                                                            $UnbalPre = 0;
                                                            $UnCrAmtPre = 0;
                                                            $UnDrAmtPre = 0;
                                                            foreach($IndExpLedgerWiseCommodityWise as $IndExpKey=>$IndExpVal){
                                                                if($IndExpVal["SubActGroupID"] == $val2["AccountID"]){
                                                                    if($IndExpVal["subgroup_id"] == "" && $IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $lastFy){
                                                                        $UnDrAmtPre += $IndExpVal["SumAmt"];
                                                                    }elseif($IndExpVal["subgroup_id"] == "" && $IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $lastFy){
                                                                        $UnCrAmtPre += $IndExpVal["SumAmt"];
                                                                    }elseif($IndExpVal["subgroup_id"] == "" && $IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $FY){
                                                                        $UnDrAmt += $IndExpVal["SumAmt"];
                                                                    }elseif($IndExpVal["subgroup_id"] == "" && $IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $FY){
                                                                        $UnCrAmt += $IndExpVal["SumAmt"];
                                                                    }
                                                                }
                                                            }
                                                            $Unbal = $UnCrAmt - $UnDrAmt;
                                                            $UnbalPre = $UnCrAmtPre - $unDrAmtPre;
                                                            $TotalAct2 += $Unbal;
                                                            $PreTotalAct2 += $UnbalPre;
                                                        ?>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($UnbalPre, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($Unbal, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($PreTotalAct2, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($TotalAct2, 2, '.', '') ?></td>
                                                            </tr>
                                                        <?php
                                                            foreach($AccountUniqueList as $ActList){
                                                                if($ActList["AccountID2"] == $val2["AccountID"]){
                                                                    $PreTotalAct = 0;
                                                                    $TotalAct = 0;
                                                                ?>
                                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $ActList["AccountID"] ?>">
                                                                    <td class="col-id-sr-no"></td>
                                                                    <td class="col-id-particular"><?php echo $ActList["AccountName"]; ?></td>  
                                                                <?php
                                                                    foreach($CommodityList as $val){
                                                                        $bal = 0;
                                                                        $CrAmt = 0;
                                                                        $DrAmt = 0;
                                                                        $balPre = 0;
                                                                        $CrAmtPre = 0;
                                                                        $DrAmtPre = 0;
                                                                        foreach($IndExpLedgerWiseCommodityWise as $IndExpKey=>$IndExpVal){
                                                                            if($IndExpVal["AccountID"] == $ActList["AccountID"]){
                                                                                if($val['id']==$IndExpVal["subgroup_id"] && $IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $lastFy){
                                                                                    $DrAmtPre += $IndExpVal["SumAmt"];
                                                                                }elseif($val['id']==$IndExpVal["subgroup_id"] && $IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $lastFy){
                                                                                    $CrAmtPre += $IndExpVal["SumAmt"];
                                                                                }elseif($val['id']==$IndExpVal["subgroup_id"] && $IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $FY){
                                                                                    $DrAmt += $IndExpVal["SumAmt"];
                                                                                }elseif($val['id']==$IndExpVal["subgroup_id"] && $IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $FY){
                                                                                    $CrAmt += $IndExpVal["SumAmt"];
                                                                                }
                                                                            }
                                                                        }
                                                                        $bal = $CrAmt - $DrAmt;
                                                                        $balPre = $CrAmtPre - $DrAmtPre;
                                                                        $TotalAct += $bal;
                                                                        $PreTotalAct += $balPre;
                                                                ?>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                <?php
                                                                    }
                                                                ?>
                                                                <?php
                                                                    $Unbal = 0;
                                                                    $UnCrAmt = 0;
                                                                    $UnDrAmt = 0;
                                                                    $UnbalPre = 0;
                                                                    $UnCrAmtPre = 0;
                                                                    $UnDrAmtPre = 0;
                                                                    foreach($IndExpLedgerWiseCommodityWise as $IndExpKey=>$IndExpVal){
                                                                        if($IndExpVal["AccountID"] == $ActList["AccountID"]){
                                                                            if($IndExpVal["subgroup_id"] == "" && $IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $lastFy){
                                                                                $UnDrAmtPre += $IndExpVal["SumAmt"];
                                                                            }elseif($IndExpVal["subgroup_id"] == "" && $IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $lastFy){
                                                                                $UnCrAmtPre += $IndExpVal["SumAmt"];
                                                                            }elseif($IndExpVal["subgroup_id"] == "" && $IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $FY){
                                                                                $UnDrAmt += $IndExpVal["SumAmt"];
                                                                            }elseif($IndExpVal["subgroup_id"] == "" && $IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $FY){
                                                                                $UnCrAmt += $IndExpVal["SumAmt"];
                                                                            }
                                                                        }
                                                                    }
                                                                    $Unbal = $UnCrAmt - $UnDrAmt;
                                                                    $UnbalPre = $UnCrAmtPre - $unDrAmtPre;
                                                                    $TotalAct += $Unbal;
                                                                    $PreTotalAct += $UnbalPre;
                                                                ?>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($PreTotalAct, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($TotalAct, 2, '.', '') ?></td>
                                                                    </tr>
                                                                <?php
                                                                    $SubCounter3++;
                                                                } 
                                                            }
                                                            
                                                        $SubCounter2++;
                                                    }
                                                }
                                                $SubCounter1++;
                                            }
                                        ?>
                                        <?php $MainCounter++;?>
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node"
                                            style="font-size:14px;"id="maingroup">
                                            <td class="parent col-id-sr-no" >7</td>
                                            <td class="parent col-id-particular" ><b>OPERATING PROFIT (1 + 2 - 4 + 5 - 6)</b></td>
                                            <?php
                                            $TotalBal = 0;
                                            $TotalBalPre = 0;
                                            $i = 0;
                                            foreach($CommodityList as $val){
                                                // Operating Profit = Sale - COGS + Other Income - Indirect Expense
                                                $OptProfit = $val["TotalRevenue"] - $val["COGS"] + $val["OthInc"] - $val["IndExp"];
                                                $OptProfitPre = $val["TotalRevenuePre"] - $val["COGSPre"] + $val["OthIncPre"] - $val["IndExpPre"];
                                                $TotalBal += $OptProfit;
                                                $TotalBalPre += $OptProfitPre;
                                                $CommodityList[$i]['OptProfit'] = $OptProfit;
                                                $CommodityList[$i]['OptProfitPre'] = $OptProfitPre;
                                                ?>
                                                <td style="text-align:right;font-weight:700;"><?php echo  number_format($OptProfitPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($OptProfit, 2, '.', '') ?></td>
                                            <?php
                                            $i++;
                                            }
                                                $OptProfitUn = 0;
                                                $OptProfitUnPre = 0;
                                                $SaleUn = 0;
                                                $SaleUnPre = 0;
                                                // Operating Profit = Sale - COGS + Other Income - Indirect Expense
                                                $OptProfitUn = $SaleUn - $UnallocatedObj->COGS + $UnallocatedObj->OthInc - $UnallocatedObj->IndExp;
                                                $OptProfitUnPre = $SaleUnPre - $UnallocatedObj->COGSPre + $UnallocatedObj->OthIncPre - $UnallocatedObj->IndExpPre;
                                                $TotalBal += $OptProfitUn;
                                                $TotalBalPre += $OptProfitUnPre;
                                                $UnallocatedObj->OptProfit = $OptProfitUn;
                                                $UnallocatedObj->OptProfitPre = $OptProfitUnPre;
                                                
                                            ?>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($OptProfitUnPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($OptProfitUn, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($TotalBalPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($TotalBal, 2, '.', '') ?></td>
                                        </tr>
                                        
                                        <?php
                                            $ActGroup1List = array();
                                            $ActGroup2List = array();
                                            $AccountList = array();
                                            foreach($FinCostLedgerWiseCommodityWise as $FinCostKey=>$FinCostVal){
                                                $new1 = array(
                                                    "AccountID"=>$FinCostVal["SubActGroupID1"],
                                                    "AccountName"=>$FinCostVal["SubActGroupName1"]
                                                );
                                                array_push($ActGroup1List,$new1);
                                                $new2 = array(
                                                    "AccountID1"=>$FinCostVal["SubActGroupID1"],
                                                    "AccountID"=>$FinCostVal["SubActGroupID"],
                                                    "AccountName"=>$FinCostVal["SubActGroupName2"]
                                                );
                                                array_push($ActGroup2List,$new2);
                                                
                                                $new = array(
                                                    "AccountID2"=>$FinCostVal["SubActGroupID"],
                                                    "AccountID"=>$FinCostVal["AccountID"],
                                                    "AccountName"=>$FinCostVal["company"]
                                                );
                                                array_push($AccountList,$new);
                                            }
                                            $ActGroup1UniqueList = array_unique($ActGroup1List,SORT_REGULAR);
                                            $ActGroup2UniqueList = array_unique($ActGroup2List,SORT_REGULAR);
                                            $AccountUniqueList = array_unique($AccountList,SORT_REGULAR);
                                        ?>
                                        
                                        <?php $MainCounter++;?>
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node"
                                            style="font-size:14px;"id="maingroup">
                                            <td class="parent col-id-sr-no">8</td>
                                            <td class="parent col-id-particular" ><b>FINANCE COST</b> </td>
                                            <?php
                                                $TotalBal = 0;
                                                $TotalBalPre = 0;
                                                $i = 0;
                                            foreach($CommodityList as $val){
                                                $bal = 0;
                                                $CrAmt = 0;
                                                $DrAmt = 0;
                                                $balPre = 0;
                                                $CrAmtPre = 0;
                                                $DrAmtPre = 0;
                                                $bal1 = 0;
                                                $CrAmt1 = 0;
                                                $DrAmt1 = 0;
                                                $balPre1 = 0;
                                                $CrAmtPre1 = 0;
                                                $DrAmtPre1 = 0;
                                                
                                                foreach($FinCostLedgerWiseCommodityWise as $FinCostKey=>$FinCostVal){
                                                    if($val['id']==$FinCostVal["subgroup_id"] && $FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $lastFy){
                                                        $DrAmtPre += $FinCostVal["SumAmt"];
                                                    }elseif($val['id']==$FinCostVal["subgroup_id"] && $FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $lastFy){
                                                        $CrAmtPre += $FinCostVal["SumAmt"];
                                                    }elseif($val['id']==$FinCostVal["subgroup_id"] && $FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $FY){
                                                        $DrAmt += $FinCostVal["SumAmt"];
                                                    }elseif($val['id']==$FinCostVal["subgroup_id"] && $FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $FY){
                                                        $CrAmt += $FinCostVal["SumAmt"];
                                                    }
                                                }
                                                
                                                $bal =  $CrAmt - $DrAmt;
                                                $balPre = $CrAmtPre - $DrAmtPre;
                                                
                                                $CommodityList[$i]['FinCost'] = $bal; 
                                                $CommodityList[$i]['FinCostPre'] = $balPre;
                                                $TotalBal += $bal;
                                                $TotalBalPre += $balPre;
                                                ?>
                                                <td style="text-align:right;font-weight:700;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <?php
                                            $i++;
                                            }
                                                
                                                $balUn = 0;
                                                $CrAmtUn = 0;
                                                $DrAmtUn = 0;
                                                $balUnPre = 0;
                                                $CrAmtUnPre = 0;
                                                $DrAmtUnPre = 0;
                                                foreach($FinCostLedgerWiseCommodityWise as $FinCostKey=>$FinCostVal){
                                                    if($FinCostVal["subgroup_id"] == "" && $FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $lastFy){
                                                        $DrAmtUnPre += $FinCostVal["SumAmt"];
                                                    }elseif($FinCostVal["subgroup_id"] == "" && $FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $lastFy){
                                                        $CrAmtUnPre += $FinCostVal["SumAmt"];
                                                    }elseif($FinCostVal["subgroup_id"] == "" && $FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $FY){
                                                        $DrAmtUn += $FinCostVal["SumAmt"];
                                                    }elseif($FinCostVal["subgroup_id"] == "" && $FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $FY){
                                                        $CrAmtUn += $FinCostVal["SumAmt"];
                                                    }
                                                }
                                                
                                                $balUn =  $CrAmtUn - $DrAmtUn;
                                                $balUnPre = $CrAmtUnPre - $DrAmtUnPre;
                                                $UnallocatedObj->FinCost = $balUn;
                                                $UnallocatedObj->FinCostPre = $balUnPre;
                                                $TotalBal += $balUn;
                                                $TotalBalPre += $balUnPre;
                                            ?>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($balUnPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($balUn, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($TotalBalPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($TotalBal, 2, '.', '') ?></td>
                                        </tr>
                                        <?php $SubCounter++; ?>
                                        <?php 
                                            
                                            foreach($ActGroup1UniqueList as $ActGrp1){
                                                $TotalAct1 = 0;
                                                $PreTotalAct1 = 0;
                                        ?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node  "
                                                style="font-size:13px;" id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
                                                <td class="col-id-sr-no"></td>
                                                <td class="col-id-particular"><?php echo $ActGrp1["AccountName"]; ?></td>
                                        <?php 
                                                foreach($CommodityList as $val){
                                                    $bal = 0;
                                                    $CrAmt = 0;
                                                    $DrAmt = 0;
                                                    $balPre = 0;
                                                    $CrAmtPre = 0;
                                                    $DrAmtPre = 0;
                                                    foreach($FinCostLedgerWiseCommodityWise as $FinCostKey=>$FinCostVal){
                                                        if($FinCostVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($val['id']==$FinCostVal["subgroup_id"] && $FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $lastFy){
                                                                $DrAmtPre += $FinCostVal["SumAmt"];
                                                            }elseif($val['id']==$FinCostVal["subgroup_id"] && $FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $lastFy){
                                                                $CrAmtPre += $FinCostVal["SumAmt"];
                                                            }elseif($val['id']==$FinCostVal["subgroup_id"] && $FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $FY){
                                                                $DrAmt += $FinCostVal["SumAmt"];
                                                            }elseif($val['id']==$FinCostVal["subgroup_id"] && $FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $FY){
                                                                $CrAmt += $FinCostVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $bal = $CrAmt - $DrAmt;
                                                    $balPre = $CrAmtPre - $DrAmtPre;
                                                    $TotalAct1 += $bal;
                                                    $PreTotalAct1 += $balPre;
                                                    ?>
                                                    <td style="text-align:right;font-weight:600;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                    <td style="text-align:right;font-weight:600;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <?php
                                                }
                                            ?>
                                                <?php
                                                    $Unbal = 0;
                                                    $UnCrAmt = 0;
                                                    $UnDrAmt = 0;
                                                    $UnbalPre = 0;
                                                    $UnCrAmtPre = 0;
                                                    $UnDrAmtPre = 0;
                                                    foreach($FinCostLedgerWiseCommodityWise as $FinCostKey=>$FinCostVal){
                                                        if($FinCostVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($FinCostVal["subgroup_id"] == "" && $FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $lastFy){
                                                                $UnDrAmtPre += $FinCostVal["SumAmt"];
                                                            }elseif($FinCostVal["subgroup_id"] == "" && $FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $lastFy){
                                                                $UnCrAmtPre += $FinCostVal["SumAmt"];
                                                            }elseif($FinCostVal["subgroup_id"] == "" && $FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $FY){
                                                                $UnDrAmt += $FinCostVal["SumAmt"];
                                                            }elseif($FinCostVal["subgroup_id"] == "" && $FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $FY){
                                                                $UnCrAmt += $FinCostVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $Unbal = $UnCrAmt - $UnDrAmt;
                                                    $UnbalPre = $UnCrAmtPre - $unDrAmtPre;
                                                    $TotalAct1 += $Unbal;
                                                    $PreTotalAct1 += $UnbalPre;
                                                ?>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($UnbalPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($Unbal, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($PreTotalAct1, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($TotalAct1, 2, '.', '') ?></td>
                                                </tr>
                                        <?php   
                                                
                                                foreach($ActGroup2UniqueList as $val2){
                                                    if($ActGrp1["AccountID"] == $val2["AccountID1"]){
                                                        $PreTotalAct2 = 0;
                                                        $TotalAct2 = 0;
                                        ?>
                                                        <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
                                                        style="font-size:13px;" id="subgroup" data-id="<?php echo $val2["AccountID"] ?>">
                                                        <td class="col-id-sr-no"></td>
                                                        <td class="col-id-particular"><?php echo $val2["AccountName"]; ?></td>    
                                        <?php
                                                        foreach($CommodityList as $val){
                                                            $bal = 0;
                                                            $CrAmt = 0;
                                                            $DrAmt = 0;
                                                            $balPre = 0;
                                                            $CrAmtPre = 0;
                                                            $DrAmtPre = 0;
                                                            foreach($FinCostLedgerWiseCommodityWise as $FinCostKey=>$FinCostVal){
                                                                if($FinCostVal["SubActGroupID"] == $val2["AccountID"]){
                                                                    if($val['id']==$FinCostVal["subgroup_id"] && $FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $lastFy){
                                                                        $DrAmtPre += $FinCostVal["SumAmt"];
                                                                    }elseif($val['id']==$FinCostVal["subgroup_id"] && $FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $lastFy){
                                                                        $CrAmtPre += $FinCostVal["SumAmt"];
                                                                    }elseif($val['id']==$FinCostVal["subgroup_id"] && $FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $FY){
                                                                        $DrAmt += $FinCostVal["SumAmt"];
                                                                    }elseif($val['id']==$FinCostVal["subgroup_id"] && $FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $FY){
                                                                        $CrAmt += $FinCostVal["SumAmt"];
                                                                    }
                                                                }
                                                            }
                                                            $bal = $CrAmt - $DrAmt;
                                                            $balPre = $CrAmtPre - $DrAmtPre;
                                                            $TotalAct2 += $bal;
                                                            $PreTotalAct2 += $balPre;
                                                        ?>  
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                        <?php
                                                        }
                                                        ?>
                                                        <?php
                                                            $Unbal = 0;
                                                            $UnCrAmt = 0;
                                                            $UnDrAmt = 0;
                                                            $UnbalPre = 0;
                                                            $UnCrAmtPre = 0;
                                                            $UnDrAmtPre = 0;
                                                            foreach($FinCostLedgerWiseCommodityWise as $FinCostKey=>$FinCostVal){
                                                                if($FinCostVal["SubActGroupID"] == $val2["AccountID"]){
                                                                    if($FinCostVal["subgroup_id"] == "" && $FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $lastFy){
                                                                        $UnDrAmtPre += $FinCostVal["SumAmt"];
                                                                    }elseif($FinCostVal["subgroup_id"] == "" && $FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $lastFy){
                                                                        $UnCrAmtPre += $FinCostVal["SumAmt"];
                                                                    }elseif($FinCostVal["subgroup_id"] == "" && $FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $FY){
                                                                        $UnDrAmt += $FinCostVal["SumAmt"];
                                                                    }elseif($FinCostVal["subgroup_id"] == "" && $FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $FY){
                                                                        $UnCrAmt += $FinCostVal["SumAmt"];
                                                                    }
                                                                }
                                                            }
                                                            $Unbal = $UnCrAmt - $UnDrAmt;
                                                            $UnbalPre = $UnCrAmtPre - $unDrAmtPre;
                                                            $TotalAct2 += $Unbal;
                                                            $PreTotalAct2 += $UnbalPre;
                                                        ?>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($UnbalPre, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($Unbal, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($PreTotalAct2, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($TotalAct2, 2, '.', '') ?></td>
                                                            </tr>
                                                        <?php
                                                            foreach($AccountUniqueList as $ActList){
                                                                if($ActList["AccountID2"] == $val2["AccountID"]){
                                                                    $PreTotalAct = 0;
                                                                    $TotalAct = 0;
                                                                ?>
                                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $ActList["AccountID"] ?>">
                                                                    <td class="col-id-sr-no"></td>
                                                                    <td class="col-id-particular"><?php echo $ActList["AccountName"]; ?></td>  
                                                                <?php
                                                                    foreach($CommodityList as $val){
                                                                        $bal = 0;
                                                                        $CrAmt = 0;
                                                                        $DrAmt = 0;
                                                                        $balPre = 0;
                                                                        $CrAmtPre = 0;
                                                                        $DrAmtPre = 0;
                                                                        foreach($FinCostLedgerWiseCommodityWise as $FinCostKey=>$FinCostVal){
                                                                            if($FinCostVal["AccountID"] == $ActList["AccountID"]){
                                                                                if($val['id']==$FinCostVal["subgroup_id"] && $FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $lastFy){
                                                                                    $DrAmtPre += $FinCostVal["SumAmt"];
                                                                                }elseif($val['id']==$FinCostVal["subgroup_id"] && $FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $lastFy){
                                                                                    $CrAmtPre += $FinCostVal["SumAmt"];
                                                                                }elseif($val['id']==$FinCostVal["subgroup_id"] && $FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $FY){
                                                                                    $DrAmt += $FinCostVal["SumAmt"];
                                                                                }elseif($val['id']==$FinCostVal["subgroup_id"] && $FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $FY){
                                                                                    $CrAmt += $FinCostVal["SumAmt"];
                                                                                }
                                                                            }
                                                                        }
                                                                        $bal = $CrAmt - $DrAmt;
                                                                        $balPre = $CrAmtPre - $DrAmtPre;
                                                                        $TotalAct += $bal;
                                                                        $PreTotalAct += $balPre;
                                                                ?>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                <?php
                                                                    }
                                                                ?>
                                                                <?php
                                                                    $Unbal = 0;
                                                                    $UnCrAmt = 0;
                                                                    $UnDrAmt = 0;
                                                                    $UnbalPre = 0;
                                                                    $UnCrAmtPre = 0;
                                                                    $UnDrAmtPre = 0;
                                                                    foreach($FinCostLedgerWiseCommodityWise as $FinCostKey=>$FinCostVal){
                                                                        if($FinCostVal["AccountID"] == $ActList["AccountID"]){
                                                                            if($FinCostVal["subgroup_id"] == "" && $FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $lastFy){
                                                                                $UnDrAmtPre += $FinCostVal["SumAmt"];
                                                                            }elseif($FinCostVal["subgroup_id"] == "" && $FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $lastFy){
                                                                                $UnCrAmtPre += $FinCostVal["SumAmt"];
                                                                            }elseif($FinCostVal["subgroup_id"] == "" && $FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $FY){
                                                                                $UnDrAmt += $FinCostVal["SumAmt"];
                                                                            }elseif($FinCostVal["subgroup_id"] == "" && $FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $FY){
                                                                                $UnCrAmt += $FinCostVal["SumAmt"];
                                                                            }
                                                                        }
                                                                    }
                                                                    $Unbal = $UnCrAmt - $UnDrAmt;
                                                                    $UnbalPre = $UnCrAmtPre - $unDrAmtPre;
                                                                    $TotalAct += $Unbal;
                                                                    $PreTotalAct += $UnbalPre;
                                                                ?>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($PreTotalAct, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($TotalAct, 2, '.', '') ?></td>
                                                                    </tr>
                                                                <?php
                                                                    $SubCounter3++;
                                                                } 
                                                            }
                                                            
                                                        $SubCounter2++;
                                                    }
                                                }
                                                $SubCounter1++;
                                            }
                                        ?>
                                        
                                        
                                        <?php
                                            $ActGroup1List = array();
                                            $ActGroup2List = array();
                                            $AccountList = array();
                                            foreach($EmpBenLedgerWiseCommodityWise as $EmpBenKey=>$EmpBenVal){
                                                $new1 = array(
                                                    "AccountID"=>$EmpBenVal["SubActGroupID1"],
                                                    "AccountName"=>$EmpBenVal["SubActGroupName1"]
                                                );
                                                array_push($ActGroup1List,$new1);
                                                $new2 = array(
                                                    "AccountID1"=>$EmpBenVal["SubActGroupID1"],
                                                    "AccountID"=>$EmpBenVal["SubActGroupID"],
                                                    "AccountName"=>$EmpBenVal["SubActGroupName2"]
                                                );
                                                array_push($ActGroup2List,$new2);
                                                
                                                $new = array(
                                                    "AccountID2"=>$EmpBenVal["SubActGroupID"],
                                                    "AccountID"=>$EmpBenVal["AccountID"],
                                                    "AccountName"=>$EmpBenVal["company"]
                                                );
                                                array_push($AccountList,$new);
                                            }
                                            $ActGroup1UniqueList = array_unique($ActGroup1List,SORT_REGULAR);
                                            $ActGroup2UniqueList = array_unique($ActGroup2List,SORT_REGULAR);
                                            $AccountUniqueList = array_unique($AccountList,SORT_REGULAR);
                                        ?>
                                        
                                        <?php $MainCounter++;?>
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node"
                                            style="font-size:14px;"id="maingroup">
                                            <td class="parent col-id-sr-no">9</td>
                                            <td class="parent col-id-particular" ><b>EMPLOYEE BENEFITS EXPENSE</b> </td>
                                            <?php
                                                $TotalBal = 0;
                                                $TotalBalPre = 0;
                                                $i = 0;
                                            foreach($CommodityList as $val){
                                                $bal = 0;
                                                $CrAmt = 0;
                                                $DrAmt = 0;
                                                $balPre = 0;
                                                $CrAmtPre = 0;
                                                $DrAmtPre = 0;
                                                $bal1 = 0;
                                                $CrAmt1 = 0;
                                                $DrAmt1 = 0;
                                                $balPre1 = 0;
                                                $CrAmtPre1 = 0;
                                                $DrAmtPre1 = 0;
                                                
                                                foreach($EmpBenLedgerWiseCommodityWise as $EmpBenKey=>$EmpBenVal){
                                                    if($val['id']==$EmpBenVal["subgroup_id"] && $EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $lastFy){
                                                        $DrAmtPre += $EmpBenVal["SumAmt"];
                                                    }elseif($val['id']==$EmpBenVal["subgroup_id"] && $EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $lastFy){
                                                        $CrAmtPre += $EmpBenVal["SumAmt"];
                                                    }elseif($val['id']==$EmpBenVal["subgroup_id"] && $EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $FY){
                                                        $DrAmt += $EmpBenVal["SumAmt"];
                                                    }elseif($val['id']==$EmpBenVal["subgroup_id"] && $EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $FY){
                                                        $CrAmt += $EmpBenVal["SumAmt"];
                                                    }
                                                }
                                                
                                                $bal =   $DrAmt - $CrAmt;
                                                $balPre = $DrAmtPre - $CrAmtPre;
                                                
                                                $CommodityList[$i]['EmpBen'] = $bal; 
                                                $CommodityList[$i]['EmpBenPre'] = $balPre;
                                                $TotalBal += $bal;
                                                $TotalBalPre += $balPre;
                                                ?>
                                                <td style="text-align:right;font-weight:700;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <?php
                                            $i++;
                                            }
                                                
                                                $balUn = 0;
                                                $CrAmtUn = 0;
                                                $DrAmtUn = 0;
                                                $balUnPre = 0;
                                                $CrAmtUnPre = 0;
                                                $DrAmtUnPre = 0;
                                                foreach($EmpBenLedgerWiseCommodityWise as $EmpBenKey=>$EmpBenVal){
                                                    if($EmpBenVal["subgroup_id"] == "" && $EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $lastFy){
                                                        $DrAmtUnPre += $EmpBenVal["SumAmt"];
                                                    }elseif($EmpBenVal["subgroup_id"] == "" && $EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $lastFy){
                                                        $CrAmtUnPre += $EmpBenVal["SumAmt"];
                                                    }elseif($EmpBenVal["subgroup_id"] == "" && $EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $FY){
                                                        $DrAmtUn += $EmpBenVal["SumAmt"];
                                                    }elseif($EmpBenVal["subgroup_id"] == "" && $EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $FY){
                                                        $CrAmtUn += $EmpBenVal["SumAmt"];
                                                    }
                                                }
                                                
                                                $Unbal = $UnDrAmt - $UnCrAmt;
                                                $UnbalPre = $unDrAmtPre - $UnCrAmtPre;
                                                $UnallocatedObj->EmpBen = $balUn;
                                                $UnallocatedObj->EmpBenPre = $balUnPre;
                                                $TotalBal += $balUn;
                                                $TotalBalPre += $balUnPre;
                                            ?>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($balUnPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($balUn, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($TotalBalPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($TotalBal, 2, '.', '') ?></td>
                                        </tr>
                                        <?php $SubCounter++; ?>
                                        <?php 
                                            
                                            foreach($ActGroup1UniqueList as $ActGrp1){
                                                $TotalAct1 = 0;
                                                $PreTotalAct1 = 0;
                                        ?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node  "
                                                style="font-size:13px;" id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
                                                <td class="col-id-sr-no"></td>
                                                <td class="col-id-particular"><?php echo $ActGrp1["AccountName"]; ?></td>
                                        <?php 
                                                foreach($CommodityList as $val){
                                                    $bal = 0;
                                                    $CrAmt = 0;
                                                    $DrAmt = 0;
                                                    $balPre = 0;
                                                    $CrAmtPre = 0;
                                                    $DrAmtPre = 0;
                                                    foreach($EmpBenLedgerWiseCommodityWise as $EmpBenKey=>$EmpBenVal){
                                                        if($EmpBenVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($val['id']==$EmpBenVal["subgroup_id"] && $EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $lastFy){
                                                                $DrAmtPre += $EmpBenVal["SumAmt"];
                                                            }elseif($val['id']==$EmpBenVal["subgroup_id"] && $EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $lastFy){
                                                                $CrAmtPre += $EmpBenVal["SumAmt"];
                                                            }elseif($val['id']==$EmpBenVal["subgroup_id"] && $EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $FY){
                                                                $DrAmt += $EmpBenVal["SumAmt"];
                                                            }elseif($val['id']==$EmpBenVal["subgroup_id"] && $EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $FY){
                                                                $CrAmt += $EmpBenVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $bal =   $DrAmt - $CrAmt;
                                                    $balPre = $DrAmtPre - $CrAmtPre;
                                                    $TotalAct1 += $bal;
                                                    $PreTotalAct1 += $balPre;
                                                    ?>
                                                    <td style="text-align:right;font-weight:600;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                    <td style="text-align:right;font-weight:600;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <?php
                                                }
                                            ?>
                                                <?php
                                                    $Unbal = 0;
                                                    $UnCrAmt = 0;
                                                    $UnDrAmt = 0;
                                                    $UnbalPre = 0;
                                                    $UnCrAmtPre = 0;
                                                    $UnDrAmtPre = 0;
                                                    foreach($EmpBenLedgerWiseCommodityWise as $EmpBenKey=>$EmpBenVal){
                                                        if($EmpBenVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($EmpBenVal["subgroup_id"] == "" && $EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $lastFy){
                                                                $UnDrAmtPre += $EmpBenVal["SumAmt"];
                                                            }elseif($EmpBenVal["subgroup_id"] == "" && $EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $lastFy){
                                                                $UnCrAmtPre += $EmpBenVal["SumAmt"];
                                                            }elseif($EmpBenVal["subgroup_id"] == "" && $EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $FY){
                                                                $UnDrAmt += $EmpBenVal["SumAmt"];
                                                            }elseif($EmpBenVal["subgroup_id"] == "" && $EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $FY){
                                                                $UnCrAmt += $EmpBenVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $Unbal = $UnDrAmt - $UnCrAmt;
                                                    $UnbalPre = $unDrAmtPre - $UnCrAmtPre;
                                                    $TotalAct1 += $Unbal;
                                                    $PreTotalAct1 += $UnbalPre;
                                                ?>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($UnbalPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($Unbal, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($PreTotalAct1, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($TotalAct1, 2, '.', '') ?></td>
                                                </tr>
                                        <?php   
                                                
                                                foreach($ActGroup2UniqueList as $val2){
                                                    if($ActGrp1["AccountID"] == $val2["AccountID1"]){
                                                        $PreTotalAct2 = 0;
                                                        $TotalAct2 = 0;
                                        ?>
                                                        <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
                                                        style="font-size:13px;" id="subgroup" data-id="<?php echo $val2["AccountID"] ?>">
                                                        <td class="col-id-sr-no"></td>
                                                        <td class="col-id-particular"><?php echo $val2["AccountName"]; ?></td>    
                                        <?php
                                                        foreach($CommodityList as $val){
                                                            $bal = 0;
                                                            $CrAmt = 0;
                                                            $DrAmt = 0;
                                                            $balPre = 0;
                                                            $CrAmtPre = 0;
                                                            $DrAmtPre = 0;
                                                            foreach($EmpBenLedgerWiseCommodityWise as $EmpBenKey=>$EmpBenVal){
                                                                if($EmpBenVal["SubActGroupID"] == $val2["AccountID"]){
                                                                    if($val['id']==$EmpBenVal["subgroup_id"] && $EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $lastFy){
                                                                        $DrAmtPre += $EmpBenVal["SumAmt"];
                                                                    }elseif($val['id']==$EmpBenVal["subgroup_id"] && $EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $lastFy){
                                                                        $CrAmtPre += $EmpBenVal["SumAmt"];
                                                                    }elseif($val['id']==$EmpBenVal["subgroup_id"] && $EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $FY){
                                                                        $DrAmt += $EmpBenVal["SumAmt"];
                                                                    }elseif($val['id']==$EmpBenVal["subgroup_id"] && $EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $FY){
                                                                        $CrAmt += $EmpBenVal["SumAmt"];
                                                                    }
                                                                }
                                                            }
                                                            $bal =   $DrAmt - $CrAmt;
                                                            $balPre = $DrAmtPre - $CrAmtPre;
                                                            $TotalAct2 += $bal;
                                                            $PreTotalAct2 += $balPre;
                                                        ?>  
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                        <?php
                                                        }
                                                        ?>
                                                        <?php
                                                            $Unbal = 0;
                                                            $UnCrAmt = 0;
                                                            $UnDrAmt = 0;
                                                            $UnbalPre = 0;
                                                            $UnCrAmtPre = 0;
                                                            $UnDrAmtPre = 0;
                                                            foreach($EmpBenLedgerWiseCommodityWise as $EmpBenKey=>$EmpBenVal){
                                                                if($EmpBenVal["SubActGroupID"] == $val2["AccountID"]){
                                                                    if($EmpBenVal["subgroup_id"] == "" && $EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $lastFy){
                                                                        $UnDrAmtPre += $EmpBenVal["SumAmt"];
                                                                    }elseif($EmpBenVal["subgroup_id"] == "" && $EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $lastFy){
                                                                        $UnCrAmtPre += $EmpBenVal["SumAmt"];
                                                                    }elseif($EmpBenVal["subgroup_id"] == "" && $EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $FY){
                                                                        $UnDrAmt += $EmpBenVal["SumAmt"];
                                                                    }elseif($EmpBenVal["subgroup_id"] == "" && $EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $FY){
                                                                        $UnCrAmt += $EmpBenVal["SumAmt"];
                                                                    }
                                                                }
                                                            }
                                                            $Unbal = $UnDrAmt - $UnCrAmt;
                                                            $UnbalPre = $unDrAmtPre - $UnCrAmtPre;
                                                            $TotalAct2 += $Unbal;
                                                            $PreTotalAct2 += $UnbalPre;
                                                        ?>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($UnbalPre, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($Unbal, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($PreTotalAct2, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($TotalAct2, 2, '.', '') ?></td>
                                                            </tr>
                                                        <?php
                                                            foreach($AccountUniqueList as $ActList){
                                                                if($ActList["AccountID2"] == $val2["AccountID"]){
                                                                    $PreTotalAct = 0;
                                                                    $TotalAct = 0;
                                                                ?>
                                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $ActList["AccountID"] ?>">
                                                                    <td class="col-id-sr-no"></td>
                                                                    <td class="col-id-particular"><?php echo $ActList["AccountName"]; ?></td>  
                                                                <?php
                                                                    foreach($CommodityList as $val){
                                                                        $bal = 0;
                                                                        $CrAmt = 0;
                                                                        $DrAmt = 0;
                                                                        $balPre = 0;
                                                                        $CrAmtPre = 0;
                                                                        $DrAmtPre = 0;
                                                                        foreach($EmpBenLedgerWiseCommodityWise as $EmpBenKey=>$EmpBenVal){
                                                                            if($EmpBenVal["AccountID"] == $ActList["AccountID"]){
                                                                                if($val['id']==$EmpBenVal["subgroup_id"] && $EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $lastFy){
                                                                                    $DrAmtPre += $EmpBenVal["SumAmt"];
                                                                                }elseif($val['id']==$EmpBenVal["subgroup_id"] && $EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $lastFy){
                                                                                    $CrAmtPre += $EmpBenVal["SumAmt"];
                                                                                }elseif($val['id']==$EmpBenVal["subgroup_id"] && $EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $FY){
                                                                                    $DrAmt += $EmpBenVal["SumAmt"];
                                                                                }elseif($val['id']==$EmpBenVal["subgroup_id"] && $EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $FY){
                                                                                    $CrAmt += $EmpBenVal["SumAmt"];
                                                                                }
                                                                            }
                                                                        }
                                                                        $bal =   $DrAmt - $CrAmt;
                                                                        $balPre = $DrAmtPre - $CrAmtPre;
                                                                        $TotalAct += $bal;
                                                                        $PreTotalAct += $balPre;
                                                                ?>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                <?php
                                                                    }
                                                                ?>
                                                                <?php
                                                                    $Unbal = 0;
                                                                    $UnCrAmt = 0;
                                                                    $UnDrAmt = 0;
                                                                    $UnbalPre = 0;
                                                                    $UnCrAmtPre = 0;
                                                                    $UnDrAmtPre = 0;
                                                                    foreach($EmpBenLedgerWiseCommodityWise as $EmpBenKey=>$EmpBenVal){
                                                                        if($EmpBenVal["AccountID"] == $ActList["AccountID"]){
                                                                            if($EmpBenVal["subgroup_id"] == "" && $EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $lastFy){
                                                                                $UnDrAmtPre += $EmpBenVal["SumAmt"];
                                                                            }elseif($EmpBenVal["subgroup_id"] == "" && $EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $lastFy){
                                                                                $UnCrAmtPre += $EmpBenVal["SumAmt"];
                                                                            }elseif($EmpBenVal["subgroup_id"] == "" && $EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $FY){
                                                                                $UnDrAmt += $EmpBenVal["SumAmt"];
                                                                            }elseif($EmpBenVal["subgroup_id"] == "" && $EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $FY){
                                                                                $UnCrAmt += $EmpBenVal["SumAmt"];
                                                                            }
                                                                        }
                                                                    }
                                                                    $Unbal = $UnDrAmt - $UnCrAmt;
                                                                    $UnbalPre = $unDrAmtPre - $UnCrAmtPre;
                                                                    $TotalAct += $Unbal;
                                                                    $PreTotalAct += $UnbalPre;
                                                                ?>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($PreTotalAct, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($TotalAct, 2, '.', '') ?></td>
                                                                    </tr>
                                                                <?php
                                                                    $SubCounter3++;
                                                                } 
                                                            }
                                                            
                                                        $SubCounter2++;
                                                    }
                                                }
                                                $SubCounter1++;
                                            }
                                        ?>
                                        
                                        <?php $MainCounter++;?>
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node"
                                            style="font-size:14px;"id="maingroup">
                                            <td class="parent col-id-sr-no" >10</td>
                                            <td class="parent col-id-particular" ><b>INCOME TAX</b></td>
                                            <?php
                                            $TotalBal = 0;
                                            $TotalBalPre = 0;
                                            foreach($CommodityList as $val){
                                                $bal = 0;
                                                $balPre = 0;
                                                $i = 0;
                                                $CommodityList[$i]['IncTax'] = $bal; 
                                                $CommodityList[$i]['IncTaxPre'] = $balPre;
                                                $TotalBal += $bal;
                                                $TotalBalPre += $balPre;
                                                ?>
                                                <td style="text-align:right;font-weight:700;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <?php
                                            $i++;
                                            }
                                                
                                                $balUn = 0;
                                                $balUnPre = 0;
                                                
                                                $UnallocatedObj->IncTax = $balUn;
                                                $UnallocatedObj->IncTaxPre = $balUnPre;
                                                $TotalBal += $balUn;
                                                $TotalBalPre += $balUnPre;
                                            ?>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($balUnPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($balUn, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($TotalBalPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($TotalBal, 2, '.', '') ?></td>
                                        </tr>
                                        
                                        <?php $MainCounter++;?>
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node"
                                            style="font-size:14px;"id="maingroup">
                                            <td class="parent col-id-sr-no" >11</td>
                                            <td class="parent col-id-particular" ><b>PROFIT FROM ORDINARY ACTIVITIES</b></td>
                                            <?php
                                            $TotalBal = 0;
                                            $TotalBalPre = 0;
                                            foreach($CommodityList as $val){
                                                $bal = 0;
                                                $balPre = 0;
                                                $i = 0;
                                                $CommodityList[$i]['OrdAct'] = $bal; 
                                                $CommodityList[$i]['OrdActPre'] = $balPre;
                                                $TotalBal += $bal;
                                                $TotalBalPre += $balPre;
                                                ?>
                                                <td style="text-align:right;font-weight:700;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <?php
                                            $i++;
                                            }
                                                
                                                $balUn = 0;
                                                $balUnPre = 0;
                                                
                                                $UnallocatedObj->OrdAct = $balUn;
                                                $UnallocatedObj->OrdActPre = $balUnPre;
                                                $TotalBal += $balUn;
                                                $TotalBalPre += $balUnPre;
                                            ?>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($balUnPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($balUn, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($TotalBalPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($TotalBal, 2, '.', '') ?></td>
                                        </tr>
                                        
                                        <?php $MainCounter++;?>
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node"
                                            style="font-size:14px;"id="maingroup">
                                            <td class="parent col-id-sr-no" >11</td>
                                            <td class="parent col-id-particular" ><b>EXTRAORDINARY ITEMS(EXP)</b></td>
                                            <?php
                                            $TotalBal = 0;
                                            $TotalBalPre = 0;
                                            foreach($CommodityList as $val){
                                                $bal = 0;
                                                $balPre = 0;
                                                $i = 0;
                                                $CommodityList[$i]['EXTEXP'] = $bal; 
                                                $CommodityList[$i]['EXTEXPPre'] = $balPre;
                                                $TotalBal += $bal;
                                                $TotalBalPre += $balPre;
                                                ?>
                                                <td style="text-align:right;font-weight:700;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <?php
                                            $i++;
                                            }
                                                
                                                $balUn = 0;
                                                $balUnPre = 0;
                                                
                                                $UnallocatedObj->EXTEXP = $balUn;
                                                $UnallocatedObj->EXTEXPPre = $balUnPre;
                                                $TotalBal += $balUn;
                                                $TotalBalPre += $balUnPre;
                                            ?>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($balUnPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($balUn, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($TotalBalPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($TotalBal, 2, '.', '') ?></td>
                                        </tr>
                                        
                                        <?php $MainCounter++;?>
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node"
                                            style="font-size:14px;background-color: green;"id="maingroup">
                                            <td class="parent col-id-sr-no" >12</td>
                                            <td class="parent col-id-particular" ><b>NET PROFIT (7 - 8 - 9)</b></td>
                                            <?php
                                            $TotalBal = 0;
                                            $TotalBalPre = 0;
                                            $i = 0;
                                            foreach($CommodityList as $val){
                                                $NetProfit = $val["OptProfit"] - $val["FinCost"] - $val["EmpBen"];
                                                $NetProfitPre = $val["OptProfitPre"] - $val["FinCostPre"] - $val["EmpBenPre"];
                                                $CommodityList[$i]['NetProfit'] = $NetProfit; 
                                                $CommodityList[$i]['NetProfitPre'] = $NetProfitPre;
                                                $TotalBal += $NetProfit;
                                                $TotalBalPre += $NetProfitPre;
                                                ?>
                                                <td style="text-align:right;font-weight:700;"><?php echo  number_format($NetProfitPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:700;"><?php echo  number_format($NetProfit, 2, '.', '') ?></td>
                                            <?php
                                            $i++;
                                            }
                                                
                                                $NetProfitUn = $UnallocatedObj->OptProfit - $UnallocatedObj->FinCost - $UnallocatedObj->EmpBen;
                                                $NetProfitUnPre = $UnallocatedObj->OptProfitPre - $UnallocatedObj->FinCostPre - $UnallocatedObj->EmpBenPre;
                                                
                                                $UnallocatedObj->NetProfit = $NetProfitUn;
                                                $UnallocatedObj->NetProfitPre = $NetProfitUnPre;
                                                $TotalBal += $NetProfitUn;
                                                $TotalBalPre += $NetProfitUnPre;
                                            ?>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($NetProfitUnPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($NetProfitUn, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($TotalBalPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($TotalBal, 2, '.', '') ?></td>
                                        </tr>
                                        
                                       
                                        <?php
                                            $ActGroup1List = array();
                                            $ActGroup2List = array();
                                            $AccountList = array();
                                            foreach($AssetsLedgerWiseCommodityWise as $AssetsKey=>$AssetsVal){
                                                $new1 = array(
                                                    "AccountID"=>$AssetsVal["SubActGroupID1"],
                                                    "AccountName"=>$AssetsVal["SubActGroupName1"]
                                                );
                                                array_push($ActGroup1List,$new1);
                                                $new2 = array(
                                                    "AccountID1"=>$AssetsVal["SubActGroupID1"],
                                                    "AccountID"=>$AssetsVal["SubActGroupID"],
                                                    "AccountName"=>$AssetsVal["SubActGroupName2"]
                                                );
                                                array_push($ActGroup2List,$new2);
                                                
                                                $new = array(
                                                    "AccountID2"=>$AssetsVal["SubActGroupID"],
                                                    "AccountID"=>$AssetsVal["AccountID"],
                                                    "AccountName"=>$AssetsVal["company"]
                                                );
                                                array_push($AccountList,$new);
                                            }
                                            $ActGroup1UniqueList = array_unique($ActGroup1List,SORT_REGULAR);
                                            $ActGroup2UniqueList = array_unique($ActGroup2List,SORT_REGULAR);
                                            $AccountUniqueList = array_unique($AccountList,SORT_REGULAR);
                                        ?>
                                        
                                        <?php $MainCounter++;?>
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node"
                                            style="font-size:14px;"id="maingroup">
                                            <td class="parent col-id-sr-no">13</td>
                                            <td class="parent col-id-particular" ><b>ASEETS</b> </td>
                                            <?php
                                                $TotalBal = 0;
                                                $TotalBalPre = 0;
                                                $i = 0;
                                            foreach($CommodityList as $val){
                                                $bal = 0;
                                                $CrAmt = 0;
                                                $DrAmt = 0;
                                                $balPre = 0;
                                                $CrAmtPre = 0;
                                                $DrAmtPre = 0;
                                                $bal1 = 0;
                                                $CrAmt1 = 0;
                                                $DrAmt1 = 0;
                                                $balPre1 = 0;
                                                $CrAmtPre1 = 0;
                                                $DrAmtPre1 = 0;
                                                
                                                foreach($AssetsLedgerWiseCommodityWise as $AssetsKey=>$AssetsVal){
                                                    if($val['id']==$AssetsVal["subgroup_id"] && $AssetsVal["TType"] == "D" && $AssetsVal["FY"] == $lastFy){
                                                        $DrAmtPre += $AssetsVal["SumAmt"];
                                                    }elseif($val['id']==$AssetsVal["subgroup_id"] && $AssetsVal["TType"] == "C" && $AssetsVal["FY"] == $lastFy){
                                                        $CrAmtPre += $AssetsVal["SumAmt"];
                                                    }elseif($val['id']==$AssetsVal["subgroup_id"] && $AssetsVal["TType"] == "D" && $AssetsVal["FY"] == $FY){
                                                        $DrAmt += $AssetsVal["SumAmt"];
                                                    }elseif($val['id']==$AssetsVal["subgroup_id"] && $AssetsVal["TType"] == "C" && $AssetsVal["FY"] == $FY){
                                                        $CrAmt += $AssetsVal["SumAmt"];
                                                    }
                                                }
                                                
                                                $bal =  $DrAmt - $CrAmt;
                                                $balPre = $DrAmtPre - $CrAmtPre;
                                                
                                                $CommodityList[$i]['AsstBen'] = $bal + $val["CurrentValue"]; 
                                                $CommodityList[$i]['AsstBenPre'] = $balPre + $val["CurrentValuePre"];
                                                $TotalBal += ($bal + $val["CurrentValue"]);
                                                $TotalBalPre += ($balPre + $val["CurrentValuePre"]);
                                                ?>
                                                <td style="text-align:right;font-weight:700;"><?php echo  number_format($balPre + $val["CurrentValuePre"], 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($bal + $val["CurrentValue"], 2, '.', '') ?></td>
                                            <?php
                                            $i++;
                                            }
                                                
                                                $balUn = 0;
                                                $CrAmtUn = 0;
                                                $DrAmtUn = 0;
                                                $balUnPre = 0;
                                                $CrAmtUnPre = 0;
                                                $DrAmtUnPre = 0;
                                                foreach($AssetsLedgerWiseCommodityWise as $AssetsKey=>$AssetsVal){
                                                    if($AssetsVal["subgroup_id"] == "" && $AssetsVal["TType"] == "D" && $AssetsVal["FY"] == $lastFy){
                                                        $DrAmtUnPre += $AssetsVal["SumAmt"];
                                                    }elseif($AssetsVal["subgroup_id"] == "" && $AssetsVal["TType"] == "C" && $AssetsVal["FY"] == $lastFy){
                                                        $CrAmtUnPre += $AssetsVal["SumAmt"];
                                                    }elseif($AssetsVal["subgroup_id"] == "" && $AssetsVal["TType"] == "D" && $AssetsVal["FY"] == $FY){
                                                        $DrAmtUn += $AssetsVal["SumAmt"];
                                                    }elseif($AssetsVal["subgroup_id"] == "" && $AssetsVal["TType"] == "C" && $AssetsVal["FY"] == $FY){
                                                        $CrAmtUn += $AssetsVal["SumAmt"];
                                                    }
                                                }
                                                
                                                $Unbal = $UnDrAmt - $UnCrAmt;
                                                $UnbalPre = $unDrAmtPre - $UnCrAmtPre;
                                                $UnallocatedObj->AsstBen = $balUn;
                                                $UnallocatedObj->AsstBenPre = $balUnPre;
                                                $TotalBal += $balUn;
                                                $TotalBalPre += $balUnPre;
                                            ?>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($balUnPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($balUn, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($TotalBalPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($TotalBal, 2, '.', '') ?></td>
                                        </tr>
                                                
                                                <tr class="treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node "
                                                style="font-size:13px;" id="subgroup">
                                                <td class="col-id-sr-no"></td>
                                                <td class="col-id-particular"><?php echo "Inventory"; ?></td>
                                                <?php
                                                    $TotalBal = 0;
                                                    $TotalBalPre = 0;
                                                    foreach($CommodityList as $val){
                                                        $TotalBal += $val["CurrentValue"];
                                                        $TotalBalPre += $val["CurrentValuePre"];
                                                        ?>
                                                        <td style="text-align:right;"><?php echo  number_format($val["CurrentValuePre"], 2, '.', '') ?></td>
                                                        <td style="text-align:right;"><?php echo  number_format($val["CurrentValue"], 2, '.', '') ?></td>
                                                <?php
                                                    }
                                                    $balUn = 0;
                                                    $balUnPre = 0;
                                                        
                                                    $TotalBal += $balUn;
                                                    $TotalBalPre += $balUnPre;
                                                    ?>
                                                    <td style="text-align:right;"><?php echo  number_format($balUnPre, 2, '.', '') ?></td>
                                                    <td style="text-align:right;"><?php echo  number_format($balUn, 2, '.', '') ?></td>
                                                    <td style="text-align:right;"><?php echo  number_format($TotalBalPre, 2, '.', '') ?></td>
                                                    <td style="text-align:right;"><?php echo  number_format($TotalBal, 2, '.', '') ?></td>
                                                </tr>
                                        <?php $SubCounter1++; ?>
                                        
                                        
                                        <?php 
                                            
                                            foreach($ActGroup1UniqueList as $ActGrp1){
                                                $TotalAct1 = 0;
                                                $PreTotalAct1 = 0;
                                        ?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node  "
                                                style="font-size:13px;" id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
                                                <td class="col-id-sr-no"></td>
                                                <td class="col-id-particular"><?php echo $ActGrp1["AccountName"]; ?></td>
                                        <?php 
                                                foreach($CommodityList as $val){
                                                    $bal = 0;
                                                    $CrAmt = 0;
                                                    $DrAmt = 0;
                                                    $balPre = 0;
                                                    $CrAmtPre = 0;
                                                    $DrAmtPre = 0;
                                                    foreach($AssetsLedgerWiseCommodityWise as $AssetsKey=>$AssetsVal){
                                                        if($AssetsVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($val['id']==$AssetsVal["subgroup_id"] && $AssetsVal["TType"] == "D" && $AssetsVal["FY"] == $lastFy){
                                                                $DrAmtPre += $AssetsVal["SumAmt"];
                                                            }elseif($val['id']==$AssetsVal["subgroup_id"] && $AssetsVal["TType"] == "C" && $AssetsVal["FY"] == $lastFy){
                                                                $CrAmtPre += $AssetsVal["SumAmt"];
                                                            }elseif($val['id']==$AssetsVal["subgroup_id"] && $AssetsVal["TType"] == "D" && $AssetsVal["FY"] == $FY){
                                                                $DrAmt += $AssetsVal["SumAmt"];
                                                            }elseif($val['id']==$AssetsVal["subgroup_id"] && $AssetsVal["TType"] == "C" && $AssetsVal["FY"] == $FY){
                                                                $CrAmt += $AssetsVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $bal =  $DrAmt - $CrAmt;
                                                    $balPre = $DrAmtPre - $CrAmtPre;
                                                    $TotalAct1 += $bal;
                                                    $PreTotalAct1 += $balPre;
                                                    ?>
                                                    <td style="text-align:right;font-weight:600;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                    <td style="text-align:right;font-weight:600;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <?php
                                                }
                                            ?>
                                                <?php
                                                    $Unbal = 0;
                                                    $UnCrAmt = 0;
                                                    $UnDrAmt = 0;
                                                    $UnbalPre = 0;
                                                    $UnCrAmtPre = 0;
                                                    $UnDrAmtPre = 0;
                                                    foreach($AssetsLedgerWiseCommodityWise as $AssetsKey=>$AssetsVal){
                                                        if($AssetsVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($AssetsVal["subgroup_id"] == "" && $AssetsVal["TType"] == "D" && $AssetsVal["FY"] == $lastFy){
                                                                $UnDrAmtPre += $AssetsVal["SumAmt"];
                                                            }elseif($AssetsVal["subgroup_id"] == "" && $AssetsVal["TType"] == "C" && $AssetsVal["FY"] == $lastFy){
                                                                $UnCrAmtPre += $AssetsVal["SumAmt"];
                                                            }elseif($AssetsVal["subgroup_id"] == "" && $AssetsVal["TType"] == "D" && $AssetsVal["FY"] == $FY){
                                                                $UnDrAmt += $AssetsVal["SumAmt"];
                                                            }elseif($AssetsVal["subgroup_id"] == "" && $AssetsVal["TType"] == "C" && $AssetsVal["FY"] == $FY){
                                                                $UnCrAmt += $AssetsVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $Unbal = $UnDrAmt - $UnCrAmt;
                                                    $UnbalPre = $unDrAmtPre - $UnCrAmtPre;
                                                    $TotalAct1 += $Unbal;
                                                    $PreTotalAct1 += $UnbalPre;
                                                ?>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($UnbalPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($Unbal, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($PreTotalAct1, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($TotalAct1, 2, '.', '') ?></td>
                                                </tr>
                                                
                                        <?php   
                                                
                                                foreach($ActGroup2UniqueList as $val2){
                                                    if($ActGrp1["AccountID"] == $val2["AccountID1"]){
                                                        $PreTotalAct2 = 0;
                                                        $TotalAct2 = 0;
                                        ?>
                                                        <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
                                                        style="font-size:13px;" id="subgroup" data-id="<?php echo $val2["AccountID"] ?>">
                                                        <td class="col-id-sr-no"></td>
                                                        <td class="col-id-particular"><?php echo $val2["AccountName"]; ?></td>    
                                        <?php
                                                        foreach($CommodityList as $val){
                                                            $bal = 0;
                                                            $CrAmt = 0;
                                                            $DrAmt = 0;
                                                            $balPre = 0;
                                                            $CrAmtPre = 0;
                                                            $DrAmtPre = 0;
                                                            foreach($AssetsLedgerWiseCommodityWise as $AssetsKey=>$AssetsVal){
                                                                if($AssetsVal["SubActGroupID"] == $val2["AccountID"]){
                                                                    if($val['id']==$AssetsVal["subgroup_id"] && $AssetsVal["TType"] == "D" && $AssetsVal["FY"] == $lastFy){
                                                                        $DrAmtPre += $AssetsVal["SumAmt"];
                                                                    }elseif($val['id']==$AssetsVal["subgroup_id"] && $AssetsVal["TType"] == "C" && $AssetsVal["FY"] == $lastFy){
                                                                        $CrAmtPre += $AssetsVal["SumAmt"];
                                                                    }elseif($val['id']==$AssetsVal["subgroup_id"] && $AssetsVal["TType"] == "D" && $AssetsVal["FY"] == $FY){
                                                                        $DrAmt += $AssetsVal["SumAmt"];
                                                                    }elseif($val['id']==$AssetsVal["subgroup_id"] && $AssetsVal["TType"] == "C" && $AssetsVal["FY"] == $FY){
                                                                        $CrAmt += $AssetsVal["SumAmt"];
                                                                    }
                                                                }
                                                            }
                                                            $bal =  $DrAmt - $CrAmt;
                                                            $balPre = $DrAmtPre - $CrAmtPre;
                                                            $TotalAct2 += $bal;
                                                            $PreTotalAct2 += $balPre;
                                                        ?>  
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                        <?php
                                                        }
                                                        ?>
                                                        <?php
                                                            $Unbal = 0;
                                                            $UnCrAmt = 0;
                                                            $UnDrAmt = 0;
                                                            $UnbalPre = 0;
                                                            $UnCrAmtPre = 0;
                                                            $UnDrAmtPre = 0;
                                                            foreach($AssetsLedgerWiseCommodityWise as $AssetsKey=>$AssetsVal){
                                                                if($AssetsVal["SubActGroupID"] == $val2["AccountID"]){
                                                                    if($AssetsVal["subgroup_id"] == "" && $AssetsVal["TType"] == "D" && $AssetsVal["FY"] == $lastFy){
                                                                        $UnDrAmtPre += $AssetsVal["SumAmt"];
                                                                    }elseif($AssetsVal["subgroup_id"] == "" && $AssetsVal["TType"] == "C" && $AssetsVal["FY"] == $lastFy){
                                                                        $UnCrAmtPre += $AssetsVal["SumAmt"];
                                                                    }elseif($AssetsVal["subgroup_id"] == "" && $AssetsVal["TType"] == "D" && $AssetsVal["FY"] == $FY){
                                                                        $UnDrAmt += $AssetsVal["SumAmt"];
                                                                    }elseif($AssetsVal["subgroup_id"] == "" && $AssetsVal["TType"] == "C" && $AssetsVal["FY"] == $FY){
                                                                        $UnCrAmt += $AssetsVal["SumAmt"];
                                                                    }
                                                                }
                                                            }
                                                            $Unbal = $UnDrAmt - $UnCrAmt;
                                                            $UnbalPre = $unDrAmtPre - $UnCrAmtPre;
                                                            $TotalAct2 += $Unbal;
                                                            $PreTotalAct2 += $UnbalPre;
                                                        ?>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($UnbalPre, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($Unbal, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($PreTotalAct2, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($TotalAct2, 2, '.', '') ?></td>
                                                            </tr>
                                                        <?php
                                                            foreach($AccountUniqueList as $ActList){
                                                                if($ActList["AccountID2"] == $val2["AccountID"]){
                                                                    $PreTotalAct = 0;
                                                                    $TotalAct = 0;
                                                                ?>
                                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $ActList["AccountID"] ?>">
                                                                    <td class="col-id-sr-no"></td>
                                                                    <td class="col-id-particular"><?php echo $ActList["AccountName"]; ?></td>  
                                                                <?php
                                                                    foreach($CommodityList as $val){
                                                                        $bal = 0;
                                                                        $CrAmt = 0;
                                                                        $DrAmt = 0;
                                                                        $balPre = 0;
                                                                        $CrAmtPre = 0;
                                                                        $DrAmtPre = 0;
                                                                        foreach($AssetsLedgerWiseCommodityWise as $AssetsKey=>$AssetsVal){
                                                                            if($AssetsVal["AccountID"] == $ActList["AccountID"]){
                                                                                if($val['id']==$AssetsVal["subgroup_id"] && $AssetsVal["TType"] == "D" && $AssetsVal["FY"] == $lastFy){
                                                                                    $DrAmtPre += $AssetsVal["SumAmt"];
                                                                                }elseif($val['id']==$AssetsVal["subgroup_id"] && $AssetsVal["TType"] == "C" && $AssetsVal["FY"] == $lastFy){
                                                                                    $CrAmtPre += $AssetsVal["SumAmt"];
                                                                                }elseif($val['id']==$AssetsVal["subgroup_id"] && $AssetsVal["TType"] == "D" && $AssetsVal["FY"] == $FY){
                                                                                    $DrAmt += $AssetsVal["SumAmt"];
                                                                                }elseif($val['id']==$AssetsVal["subgroup_id"] && $AssetsVal["TType"] == "C" && $AssetsVal["FY"] == $FY){
                                                                                    $CrAmt += $AssetsVal["SumAmt"];
                                                                                }
                                                                            }
                                                                        }
                                                                        $bal =  $DrAmt - $CrAmt;
                                                                        $balPre = $DrAmtPre - $CrAmtPre;
                                                                        $TotalAct += $bal;
                                                                        $PreTotalAct += $balPre;
                                                                ?>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                <?php
                                                                    }
                                                                ?>
                                                                <?php
                                                                    $Unbal = 0;
                                                                    $UnCrAmt = 0;
                                                                    $UnDrAmt = 0;
                                                                    $UnbalPre = 0;
                                                                    $UnCrAmtPre = 0;
                                                                    $UnDrAmtPre = 0;
                                                                    foreach($AssetsLedgerWiseCommodityWise as $AssetsKey=>$AssetsVal){
                                                                        if($AssetsVal["AccountID"] == $ActList["AccountID"]){
                                                                            if($AssetsVal["subgroup_id"] == "" && $AssetsVal["TType"] == "D" && $AssetsVal["FY"] == $lastFy){
                                                                                $UnDrAmtPre += $AssetsVal["SumAmt"];
                                                                            }elseif($AssetsVal["subgroup_id"] == "" && $AssetsVal["TType"] == "C" && $AssetsVal["FY"] == $lastFy){
                                                                                $UnCrAmtPre += $AssetsVal["SumAmt"];
                                                                            }elseif($AssetsVal["subgroup_id"] == "" && $AssetsVal["TType"] == "D" && $AssetsVal["FY"] == $FY){
                                                                                $UnDrAmt += $AssetsVal["SumAmt"];
                                                                            }elseif($AssetsVal["subgroup_id"] == "" && $AssetsVal["TType"] == "C" && $AssetsVal["FY"] == $FY){
                                                                                $UnCrAmt += $AssetsVal["SumAmt"];
                                                                            }
                                                                        }
                                                                    }
                                                                    $Unbal = $UnDrAmt - $UnCrAmt;
                                                                    $UnbalPre = $unDrAmtPre - $UnCrAmtPre;
                                                                    $TotalAct += $Unbal;
                                                                    $PreTotalAct += $UnbalPre;
                                                                ?>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($PreTotalAct, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($TotalAct, 2, '.', '') ?></td>
                                                                    </tr>
                                                                <?php
                                                                    $SubCounter3++;
                                                                } 
                                                            }
                                                            
                                                        $SubCounter2++;
                                                    }
                                                }
                                                $SubCounter1++;
                                            }
                                        ?>
                                        
                                        
                                        
                                        
                                        
                                        
                                        <?php
                                            $ActGroup1List = array();
                                            $ActGroup2List = array();
                                            $AccountList = array();
                                            foreach($LiaLedgerWiseCommodityWise as $LiaKey=>$LiaVal){
                                                $new1 = array(
                                                    "AccountID"=>$LiaVal["SubActGroupID1"],
                                                    "AccountName"=>$LiaVal["SubActGroupName1"]
                                                );
                                                array_push($ActGroup1List,$new1);
                                                $new2 = array(
                                                    "AccountID1"=>$LiaVal["SubActGroupID1"],
                                                    "AccountID"=>$LiaVal["SubActGroupID"],
                                                    "AccountName"=>$LiaVal["SubActGroupName2"]
                                                );
                                                array_push($ActGroup2List,$new2);
                                                
                                                $new = array(
                                                    "AccountID2"=>$LiaVal["SubActGroupID"],
                                                    "AccountID"=>$LiaVal["AccountID"],
                                                    "AccountName"=>$LiaVal["company"]
                                                );
                                                array_push($AccountList,$new);
                                            }
                                            $ActGroup1UniqueList = array_unique($ActGroup1List,SORT_REGULAR);
                                            $ActGroup2UniqueList = array_unique($ActGroup2List,SORT_REGULAR);
                                            $AccountUniqueList = array_unique($AccountList,SORT_REGULAR);
                                        ?>
                                        
                                        <?php $MainCounter++;?>
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node"
                                            style="font-size:14px;"id="maingroup">
                                            <td class="parent col-id-sr-no">14</td>
                                            <td class="parent col-id-particular" ><b>EQUITY AND LIABILITIES</b> </td>
                                            <?php
                                                $TotalBal = 0;
                                                $TotalBalPre = 0;
                                                $i = 0;
                                            foreach($CommodityList as $val){
                                                $bal = 0;
                                                $CrAmt = 0;
                                                $DrAmt = 0;
                                                $balPre = 0;
                                                $CrAmtPre = 0;
                                                $DrAmtPre = 0;
                                                $bal1 = 0;
                                                $CrAmt1 = 0;
                                                $DrAmt1 = 0;
                                                $balPre1 = 0;
                                                $CrAmtPre1 = 0;
                                                $DrAmtPre1 = 0;
                                                
                                                foreach($LiaLedgerWiseCommodityWise as $LiaKey=>$LiaVal){
                                                    if($val['id']==$LiaVal["subgroup_id"] && $LiaVal["TType"] == "D" && $LiaVal["FY"] == $lastFy){
                                                        $DrAmtPre += $LiaVal["SumAmt"];
                                                    }elseif($val['id']==$LiaVal["subgroup_id"] && $LiaVal["TType"] == "C" && $LiaVal["FY"] == $lastFy){
                                                        $CrAmtPre += $LiaVal["SumAmt"];
                                                    }elseif($val['id']==$LiaVal["subgroup_id"] && $LiaVal["TType"] == "D" && $LiaVal["FY"] == $FY){
                                                        $DrAmt += $LiaVal["SumAmt"];
                                                    }elseif($val['id']==$LiaVal["subgroup_id"] && $LiaVal["TType"] == "C" && $LiaVal["FY"] == $FY){
                                                        $CrAmt += $LiaVal["SumAmt"];
                                                    }
                                                }
                                                
                                                $bal =  $CrAmt - $DrAmt;
                                                $balPre = $CrAmtPre - $DrAmtPre;
                                                
                                                $CommodityList[$i]['LiaBen'] = $bal + $val["NetProfit"]; 
                                                $CommodityList[$i]['LiaBenPre'] = $balPre + $val["NetProfitPre"];
                                                $TotalBal += ($bal + $val["NetProfit"]);
                                                $TotalBalPre += ($balPre + $val["NetProfitPre"]);
                                                ?>
                                                <td style="text-align:right;font-weight:700;"><?php echo  number_format($balPre + $val["NetProfitPre"], 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($bal + $val["NetProfit"], 2, '.', '') ?></td>
                                            <?php
                                            $i++;
                                            }
                                                
                                                $balUn = 0;
                                                $CrAmtUn = 0;
                                                $DrAmtUn = 0;
                                                $balUnPre = 0;
                                                $CrAmtUnPre = 0;
                                                $DrAmtUnPre = 0;
                                                foreach($LiaLedgerWiseCommodityWise as $LiaKey=>$LiaVal){
                                                    if($LiaVal["subgroup_id"] == "" && $LiaVal["TType"] == "D" && $LiaVal["FY"] == $lastFy){
                                                        $DrAmtUnPre += $LiaVal["SumAmt"];
                                                    }elseif($LiaVal["subgroup_id"] == "" && $LiaVal["TType"] == "C" && $LiaVal["FY"] == $lastFy){
                                                        $CrAmtUnPre += $LiaVal["SumAmt"];
                                                    }elseif($LiaVal["subgroup_id"] == "" && $LiaVal["TType"] == "D" && $LiaVal["FY"] == $FY){
                                                        $DrAmtUn += $LiaVal["SumAmt"];
                                                    }elseif($LiaVal["subgroup_id"] == "" && $LiaVal["TType"] == "C" && $LiaVal["FY"] == $FY){
                                                        $CrAmtUn += $LiaVal["SumAmt"];
                                                    }
                                                }
                                                
                                                $Unbal = $UnCrAmt - $UnDrAmt;
                                                $UnbalPre = $UnCrAmtPre - $unDrAmtPre;
                                                $UnallocatedObj->LiaBen = $balUn;
                                                $UnallocatedObj->LiaBenPre = $balUnPre;
                                                $TotalBal += $balUn;
                                                $TotalBalPre += $balUnPre;
                                            ?>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($balUnPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($balUn, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;"><?php echo  number_format($TotalBalPre, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;background-color: orange;"><?php echo  number_format($TotalBal, 2, '.', '') ?></td>
                                        </tr>
                                                
                                                <tr class="treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node "
                                                style="font-size:13px;" id="subgroup">
                                                <td class="col-id-sr-no"></td>
                                                <td class="col-id-particular"><?php echo "Profit & Loss"; ?></td>
                                                <?php
                                                    $TotalBal = 0;
                                                    $TotalBalPre = 0;
                                                    foreach($CommodityList as $val){
                                                        $TotalBal += $val["NetProfit"];
                                                        $TotalBalPre += $val["NetProfitPre"];
                                                        ?>
                                                        <td style="text-align:right;"><?php echo  number_format($val["NetProfitPre"], 2, '.', '') ?></td>
                                                        <td style="text-align:right;"><?php echo  number_format($val["NetProfit"], 2, '.', '') ?></td>
                                                <?php
                                                    }
                                                    $balUn = 0;
                                                    $balUnPre = 0;
                                                        
                                                    $TotalBal += $balUn;
                                                    $TotalBalPre += $balUnPre;
                                                    ?>
                                                    <td style="text-align:right;"><?php echo  number_format($balUnPre, 2, '.', '') ?></td>
                                                    <td style="text-align:right;"><?php echo  number_format($balUn, 2, '.', '') ?></td>
                                                    <td style="text-align:right;"><?php echo  number_format($TotalBalPre, 2, '.', '') ?></td>
                                                    <td style="text-align:right;"><?php echo  number_format($TotalBal, 2, '.', '') ?></td>
                                                </tr>
                                        <?php $SubCounter1++; ?>
                                        
                                        
                                        <?php 
                                            
                                            foreach($ActGroup1UniqueList as $ActGrp1){
                                                $TotalAct1 = 0;
                                                $PreTotalAct1 = 0;
                                        ?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node  "
                                                style="font-size:13px;" id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
                                                <td class="col-id-sr-no"></td>
                                                <td class="col-id-particular"><?php echo $ActGrp1["AccountName"]; ?></td>
                                        <?php 
                                                foreach($CommodityList as $val){
                                                    $bal = 0;
                                                    $CrAmt = 0;
                                                    $DrAmt = 0;
                                                    $balPre = 0;
                                                    $CrAmtPre = 0;
                                                    $DrAmtPre = 0;
                                                    foreach($LiaLedgerWiseCommodityWise as $LiaKey=>$LiaVal){
                                                        if($LiaVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($val['id']==$LiaVal["subgroup_id"] && $LiaVal["TType"] == "D" && $LiaVal["FY"] == $lastFy){
                                                                $DrAmtPre += $LiaVal["SumAmt"];
                                                            }elseif($val['id']==$LiaVal["subgroup_id"] && $LiaVal["TType"] == "C" && $LiaVal["FY"] == $lastFy){
                                                                $CrAmtPre += $LiaVal["SumAmt"];
                                                            }elseif($val['id']==$LiaVal["subgroup_id"] && $LiaVal["TType"] == "D" && $LiaVal["FY"] == $FY){
                                                                $DrAmt += $LiaVal["SumAmt"];
                                                            }elseif($val['id']==$LiaVal["subgroup_id"] && $LiaVal["TType"] == "C" && $LiaVal["FY"] == $FY){
                                                                $CrAmt += $LiaVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $bal =  $CrAmt - $DrAmt;
                                                    $balPre = $CrAmtPre - $DrAmtPre;
                                                    $TotalAct1 += $bal;
                                                    $PreTotalAct1 += $balPre;
                                                    ?>
                                                    <td style="text-align:right;font-weight:600;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                    <td style="text-align:right;font-weight:600;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <?php
                                                }
                                            ?>
                                                <?php
                                                    $Unbal = 0;
                                                    $UnCrAmt = 0;
                                                    $UnDrAmt = 0;
                                                    $UnbalPre = 0;
                                                    $UnCrAmtPre = 0;
                                                    $UnDrAmtPre = 0;
                                                    foreach($LiaLedgerWiseCommodityWise as $LiaKey=>$LiaVal){
                                                        if($LiaVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($LiaVal["subgroup_id"] == "" && $LiaVal["TType"] == "D" && $LiaVal["FY"] == $lastFy){
                                                                $UnDrAmtPre += $LiaVal["SumAmt"];
                                                            }elseif($LiaVal["subgroup_id"] == "" && $LiaVal["TType"] == "C" && $LiaVal["FY"] == $lastFy){
                                                                $UnCrAmtPre += $LiaVal["SumAmt"];
                                                            }elseif($LiaVal["subgroup_id"] == "" && $LiaVal["TType"] == "D" && $LiaVal["FY"] == $FY){
                                                                $UnDrAmt += $LiaVal["SumAmt"];
                                                            }elseif($LiaVal["subgroup_id"] == "" && $LiaVal["TType"] == "C" && $LiaVal["FY"] == $FY){
                                                                $UnCrAmt += $LiaVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $Unbal = $UnCrAmt - $UnDrAmt;
                                                    $UnbalPre = $UnCrAmtPre - $unDrAmtPre;
                                                    $TotalAct1 += $Unbal;
                                                    $PreTotalAct1 += $UnbalPre;
                                                ?>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($UnbalPre, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($Unbal, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($PreTotalAct1, 2, '.', '') ?></td>
                                                <td style="text-align:right;font-weight:600;"><?php echo  number_format($TotalAct1, 2, '.', '') ?></td>
                                                </tr>
                                                
                                        <?php   
                                                
                                                foreach($ActGroup2UniqueList as $val2){
                                                    if($ActGrp1["AccountID"] == $val2["AccountID1"]){
                                                        $PreTotalAct2 = 0;
                                                        $TotalAct2 = 0;
                                        ?>
                                                        <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
                                                        style="font-size:13px;" id="subgroup" data-id="<?php echo $val2["AccountID"] ?>">
                                                        <td class="col-id-sr-no"></td>
                                                        <td class="col-id-particular"><?php echo $val2["AccountName"]; ?></td>    
                                        <?php
                                                        foreach($CommodityList as $val){
                                                            $bal = 0;
                                                            $CrAmt = 0;
                                                            $DrAmt = 0;
                                                            $balPre = 0;
                                                            $CrAmtPre = 0;
                                                            $DrAmtPre = 0;
                                                            foreach($LiaLedgerWiseCommodityWise as $LiaKey=>$LiaVal){
                                                                if($LiaVal["SubActGroupID"] == $val2["AccountID"]){
                                                                    if($val['id']==$LiaVal["subgroup_id"] && $LiaVal["TType"] == "D" && $LiaVal["FY"] == $lastFy){
                                                                        $DrAmtPre += $LiaVal["SumAmt"];
                                                                    }elseif($val['id']==$LiaVal["subgroup_id"] && $LiaVal["TType"] == "C" && $LiaVal["FY"] == $lastFy){
                                                                        $CrAmtPre += $LiaVal["SumAmt"];
                                                                    }elseif($val['id']==$LiaVal["subgroup_id"] && $LiaVal["TType"] == "D" && $LiaVal["FY"] == $FY){
                                                                        $DrAmt += $LiaVal["SumAmt"];
                                                                    }elseif($val['id']==$LiaVal["subgroup_id"] && $LiaVal["TType"] == "C" && $LiaVal["FY"] == $FY){
                                                                        $CrAmt += $LiaVal["SumAmt"];
                                                                    }
                                                                }
                                                            }
                                                            $bal =  $CrAmt - $DrAmt;
                                                            $balPre = $CrAmtPre - $DrAmtPre;
                                                            $TotalAct2 += $bal;
                                                            $PreTotalAct2 += $balPre;
                                                        ?>  
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                        <?php
                                                        }
                                                        ?>
                                                        <?php
                                                            $Unbal = 0;
                                                            $UnCrAmt = 0;
                                                            $UnDrAmt = 0;
                                                            $UnbalPre = 0;
                                                            $UnCrAmtPre = 0;
                                                            $UnDrAmtPre = 0;
                                                            foreach($LiaLedgerWiseCommodityWise as $LiaKey=>$LiaVal){
                                                                if($LiaVal["SubActGroupID"] == $val2["AccountID"]){
                                                                    if($LiaVal["subgroup_id"] == "" && $LiaVal["TType"] == "D" && $LiaVal["FY"] == $lastFy){
                                                                        $UnDrAmtPre += $LiaVal["SumAmt"];
                                                                    }elseif($LiaVal["subgroup_id"] == "" && $LiaVal["TType"] == "C" && $LiaVal["FY"] == $lastFy){
                                                                        $UnCrAmtPre += $LiaVal["SumAmt"];
                                                                    }elseif($LiaVal["subgroup_id"] == "" && $LiaVal["TType"] == "D" && $LiaVal["FY"] == $FY){
                                                                        $UnDrAmt += $LiaVal["SumAmt"];
                                                                    }elseif($LiaVal["subgroup_id"] == "" && $LiaVal["TType"] == "C" && $LiaVal["FY"] == $FY){
                                                                        $UnCrAmt += $LiaVal["SumAmt"];
                                                                    }
                                                                }
                                                            }
                                                            $Unbal = $UnCrAmt - $UnDrAmt;
                                                            $UnbalPre = $UnCrAmtPre - $unDrAmtPre;
                                                            $TotalAct2 += $Unbal;
                                                            $PreTotalAct2 += $UnbalPre;
                                                        ?>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($UnbalPre, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($Unbal, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($PreTotalAct2, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($TotalAct2, 2, '.', '') ?></td>
                                                            </tr>
                                                        <?php
                                                            foreach($AccountUniqueList as $ActList){
                                                                if($ActList["AccountID2"] == $val2["AccountID"]){
                                                                    $PreTotalAct = 0;
                                                                    $TotalAct = 0;
                                                                ?>
                                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $ActList["AccountID"] ?>">
                                                                    <td class="col-id-sr-no"></td>
                                                                    <td class="col-id-particular"><?php echo $ActList["AccountName"]; ?></td>  
                                                                <?php
                                                                    foreach($CommodityList as $val){
                                                                        $bal = 0;
                                                                        $CrAmt = 0;
                                                                        $DrAmt = 0;
                                                                        $balPre = 0;
                                                                        $CrAmtPre = 0;
                                                                        $DrAmtPre = 0;
                                                                        foreach($LiaLedgerWiseCommodityWise as $LiaKey=>$LiaVal){
                                                                            if($LiaVal["AccountID"] == $ActList["AccountID"]){
                                                                                if($val['id']==$LiaVal["subgroup_id"] && $LiaVal["TType"] == "D" && $LiaVal["FY"] == $lastFy){
                                                                                    $DrAmtPre += $LiaVal["SumAmt"];
                                                                                }elseif($val['id']==$LiaVal["subgroup_id"] && $LiaVal["TType"] == "C" && $LiaVal["FY"] == $lastFy){
                                                                                    $CrAmtPre += $LiaVal["SumAmt"];
                                                                                }elseif($val['id']==$LiaVal["subgroup_id"] && $LiaVal["TType"] == "D" && $LiaVal["FY"] == $FY){
                                                                                    $DrAmt += $LiaVal["SumAmt"];
                                                                                }elseif($val['id']==$LiaVal["subgroup_id"] && $LiaVal["TType"] == "C" && $LiaVal["FY"] == $FY){
                                                                                    $CrAmt += $LiaVal["SumAmt"];
                                                                                }
                                                                            }
                                                                        }
                                                                        $bal =  $CrAmt - $DrAmt;
                                                                        $balPre = $CrAmtPre - $DrAmtPre;
                                                                        $TotalAct += $bal;
                                                                        $PreTotalAct += $balPre;
                                                                ?>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                <?php
                                                                    }
                                                                ?>
                                                                <?php
                                                                    $Unbal = 0;
                                                                    $UnCrAmt = 0;
                                                                    $UnDrAmt = 0;
                                                                    $UnbalPre = 0;
                                                                    $UnCrAmtPre = 0;
                                                                    $UnDrAmtPre = 0;
                                                                    foreach($LiaLedgerWiseCommodityWise as $LiaKey=>$LiaVal){
                                                                        if($LiaVal["AccountID"] == $ActList["AccountID"]){
                                                                            if($LiaVal["subgroup_id"] == "" && $LiaVal["TType"] == "D" && $LiaVal["FY"] == $lastFy){
                                                                                $UnDrAmtPre += $LiaVal["SumAmt"];
                                                                            }elseif($LiaVal["subgroup_id"] == "" && $LiaVal["TType"] == "C" && $LiaVal["FY"] == $lastFy){
                                                                                $UnCrAmtPre += $LiaVal["SumAmt"];
                                                                            }elseif($LiaVal["subgroup_id"] == "" && $LiaVal["TType"] == "D" && $LiaVal["FY"] == $FY){
                                                                                $UnDrAmt += $LiaVal["SumAmt"];
                                                                            }elseif($LiaVal["subgroup_id"] == "" && $LiaVal["TType"] == "C" && $LiaVal["FY"] == $FY){
                                                                                $UnCrAmt += $LiaVal["SumAmt"];
                                                                            }
                                                                        }
                                                                    }
                                                                    $Unbal = $UnCrAmt - $UnDrAmt;
                                                                    $UnbalPre = $UnCrAmtPre - $unDrAmtPre;
                                                                    $TotalAct += $Unbal;
                                                                    $PreTotalAct += $UnbalPre;
                                                                ?>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($PreTotalAct, 2, '.', '') ?></td>
                                                                    <td style="text-align:right;font-weight:400;"><?php echo  number_format($TotalAct, 2, '.', '') ?></td>
                                                                    </tr>
                                                                <?php
                                                                    $SubCounter3++;
                                                                } 
                                                            }
                                                            
                                                        $SubCounter2++;
                                                    }
                                                }
                                                $SubCounter1++;
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
    <style>
        table  { border-collapse: collapse; width: 100%; }
        th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
        th     { background: #50607b;color: #fff !important; }
        .table_annexure {
            overflow: auto;
            max-height: 60vh;
            width: 98%;
            position: relative;
            top: 0px;
        }
    </style>
    <script>
        $('.get_AccountID').on('click',function(){ 
            AccountID = $(this).attr("data-id");
            $.ajax({
                url:"<?php echo admin_url(); ?>accounting/SetAccountID",
                dataType:"JSON",
                method:"POST",
                data:{AccountID:AccountID},
                beforeSend: function () {
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
                },
                complete: function () {
                $('.searchh2').css('display','none');
                },
                success:function(data){
                    var url = "<?php echo admin_url();?>accounting/rp_general_ledger";
                    window.open(url, '_blank');
                }
            });
        });
    </script>

    <script type="text/javascript">
        function printPage() {
            var html_filter_name = $('.report_for').html();
            var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
            var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">' + document.getElementsByTagName('table')[0].innerHTML + '</table>';
            var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
            heading_data += '<tr>';
            heading_data += '<td style="text-align:center;"colspan="3">Balance Sheet</td>';
            heading_data += '</tr>';
            heading_data += '<tr>';
            heading_data += '</tbody></table>';
            var print_data = stylesheet + heading_data + tableData
            newWin = window.open("");
            newWin.document.write(print_data);
            newWin.print();
            newWin.close();
        };
    </script>

    
<script>
</script>