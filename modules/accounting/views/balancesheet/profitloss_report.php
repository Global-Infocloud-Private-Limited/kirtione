<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    .th_total {
        padding-right: 10px;
    }
</style>

<div id="wrapper">
    <div class="panel_s">
        <div class="panel-body">
                <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Profit Loss Report</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
            <div class="row ">
                <div class="col-md-5">
                    <?php if (has_permission_new('profitlossreport', '', 'print')) {
                                ?>
                    <a class="btn btn-default" href="javascript:void(0);" style="margin-bottom: 20px;margin-left: 10px;" onclick="printPage();">Print</a>
                    <?php } ?>
                </div>
            </div>
            <div class="row ">
                <div class="col-md-10">
                    <?php
                    $FY = $this->session->userdata('finacial_year');
                    $lastFy = $FY - 1;
                    $fy_ = $FY + 1;
                    $lastFy_ = $lastFy + 1;
                    $CurrYrLastDate = '31/03/20' . $fy_;
                    $LastYrLastDate = '31/03/20' . $lastFy_;
                    ?>
                    <div class="page" id="DivIdToPrint">
                        <div id="accordion">
                            <div class="card">
                                <table class="tree">
                                    <thead>
                                        <tr class="tr_header">
                                            <th style=""><b>Particular</b></th>
                                            <!--<th class="th_total"><b>Note No.</b></th>-->
                                            <th class="th_total"><b>
                                                    <?php echo $CurrYrLastDate; ?>
                                                </b>
                                            </th>
                                            <th class="th_total"><b>
                                                    <?php echo $LastYrLastDate; ?>
                                                </b>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $MainCounter = 1000;
                                        $SubCounter1 = 2000;
                                        $SubCounter2 = 3000;
                                        $SubCounter3 = 4000;
                                        $SubCounter4 = 5000;
                                        $TotalRevenueIncome = 0;
                                        $TotalRevenueIncomePre = 0;
                                        ?>
                                        <?php $MainCounter++;?>
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
                                            <td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;"><b></b>I. Revenue from Operation</b></td>
                                            <td class="parent col-id-particular"><b></b></td>
                                            <td class="parent col-id-particular"><b></b></td>
                                        </tr>
                                            <tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
                                                <td class="col-id-sr-no" style="font-size:13px;font-weight:500;">Sale Amount</td>
                                                <td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($revenue_from_opn->CurrentYear, 2, '.', '') ?></td>
                                                <?php
                                                    $TotalRevenueIncome += $revenue_from_opn->CurrentYear;
                                                    $TotalRevenueIncomePre += $revenue_from_opn->PriviousYear;
                                                ?>
                                                <td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($revenue_from_opn->PriviousYear, 2, '.', '') ?></td>
                                            </tr>
                                            <?php $SubCounter1++;?>
                                            <tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
                                                <td class="col-id-sr-no" style="font-size:13px;font-weight:500;">Sale Return Amount</td>
                                                <td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format(0, 2, '.', '') ?></td>
                                                <td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format(0, 2, '.', '') ?></td>
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
                                        
                                        
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node" id="maingroup">
                                            <td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">II. Other Income</td>
                                            <?php
                                                
                                                $bal = 0;
                                                $CrAmt = 0;
                                                $DrAmt = 0;
                                                $balPre = 0;
                                                $CrAmtPre = 0;
                                                $DrAmtPre = 0;
                                                foreach($OthIncLedgerWiseCommodityWise as $OIncKey=>$OIncVal){
                                                    if($OIncVal["TType"] == "D" && $OIncVal["FY"] == $lastFy){
                                                        $DrAmtPre += $OIncVal["SumAmt"];
                                                    }elseif($OIncVal["TType"] == "C" && $OIncVal["FY"] == $lastFy){
                                                        $CrAmtPre += $OIncVal["SumAmt"];
                                                    }elseif($OIncVal["TType"] == "D" && $OIncVal["FY"] == $FY){
                                                        $DrAmt += $OIncVal["SumAmt"];
                                                    }elseif($OIncVal["TType"] == "C" && $OIncVal["FY"] == $FY){
                                                        $CrAmt += $OIncVal["SumAmt"];
                                                    }
                                                }
                                                $bal =  $CrAmt - $DrAmt;
                                                $TotalRevenueIncome += $bal;
                                                $balPre = $CrAmtPre - $DrAmtPre;
                                                $TotalRevenueIncomePre += $balPre;
                                            ?>
                                            <td style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                        </tr>
                                        <?php 
                                            foreach($ActGroup1UniqueList as $ActGrp1){
                                        ?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
                                                    <td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $ActGrp1["AccountName"]; ?></td>
                                                <?php 
                                                    $bal = 0;
                                                    $CrAmt = 0;
                                                    $DrAmt = 0;
                                                    $balPre = 0;
                                                    $CrAmtPre = 0;
                                                    $DrAmtPre = 0;
                                                    foreach($OthIncLedgerWiseCommodityWise as $OIncKey=>$OIncVal){
                                                        if($OIncVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($OIncVal["TType"] == "D" && $OIncVal["FY"] == $lastFy){
                                                                $DrAmtPre += $OIncVal["SumAmt"];
                                                            }elseif($OIncVal["TType"] == "C" && $OIncVal["FY"] == $lastFy){
                                                                $CrAmtPre += $OIncVal["SumAmt"];
                                                            }elseif($OIncVal["TType"] == "D" && $OIncVal["FY"] == $FY){
                                                                $DrAmt += $OIncVal["SumAmt"];
                                                            }elseif($OIncVal["TType"] == "C" && $OIncVal["FY"] == $FY){
                                                                $CrAmt += $OIncVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $bal = $CrAmt - $DrAmt;
                                                    $balPre = $CrAmtPre - $DrAmtPre;
                                                ?>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                </tr>
                                            <?php  
                                                foreach($ActGroup2UniqueList as $val2){
                                                    if($ActGrp1["AccountID"] == $val2["AccountID1"]){
                                            ?>
                                                        <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
                                                        style="font-size:13px;" id="subgroup" data-id="<?php echo $val2["AccountID"] ?>">
                                                            <td class="col-id-particular"><?php echo $val2["AccountName"]; ?></td>    
                                                    <?php
                                                        $bal = 0;
                                                        $CrAmt = 0;
                                                        $DrAmt = 0;
                                                        $balPre = 0;
                                                        $CrAmtPre = 0;
                                                        $DrAmtPre = 0;
                                                        foreach($OthIncLedgerWiseCommodityWise as $OIncKey=>$OIncVal){
                                                            if($OIncVal["SubActGroupID"] == $val2["AccountID"]){
                                                                if($OIncVal["TType"] == "D" && $OIncVal["FY"] == $lastFy){
                                                                    $DrAmtPre += $OIncVal["SumAmt"];
                                                                }elseif($OIncVal["TType"] == "C" && $OIncVal["FY"] == $lastFy){
                                                                    $CrAmtPre += $OIncVal["SumAmt"];
                                                                }elseif($OIncVal["TType"] == "D" && $OIncVal["FY"] == $FY){
                                                                    $DrAmt += $OIncVal["SumAmt"];
                                                                }elseif($OIncVal["TType"] == "C" && $OIncVal["FY"] == $FY){
                                                                    $CrAmt += $OIncVal["SumAmt"];
                                                                }
                                                            }
                                                        }
                                                        $bal = $CrAmt - $DrAmt;
                                                        $balPre = $CrAmtPre - $DrAmtPre;
                                                    ?>  
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                        </tr>
                                                        <?php
                                                            foreach($AccountUniqueList as $ActList){
                                                                if($ActList["AccountID2"] == $val2["AccountID"]){
                                                        ?>
                                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $ActList["AccountID"] ?>">
                                                                        <td class="col-id-particular"><?php echo $ActList["AccountName"]; ?></td>  
                                                                <?php
                                                                    $bal = 0;
                                                                    $CrAmt = 0;
                                                                    $DrAmt = 0;
                                                                    $balPre = 0;
                                                                    $CrAmtPre = 0;
                                                                    $DrAmtPre = 0;
                                                                    foreach($OthIncLedgerWiseCommodityWise as $OIncKey=>$OIncVal){
                                                                        if($OIncVal["AccountID"] == $ActList["AccountID"]){
                                                                            if($OIncVal["TType"] == "D" && $OIncVal["FY"] == $lastFy){
                                                                                $DrAmtPre += $OIncVal["SumAmt"];
                                                                            }elseif($OIncVal["TType"] == "C" && $OIncVal["FY"] == $lastFy){
                                                                                $CrAmtPre += $OIncVal["SumAmt"];
                                                                            }elseif($OIncVal["TType"] == "D" && $OIncVal["FY"] == $FY){
                                                                                $DrAmt += $OIncVal["SumAmt"];
                                                                            }elseif($OIncVal["TType"] == "C" && $OIncVal["FY"] == $FY){
                                                                                $CrAmt += $OIncVal["SumAmt"];
                                                                            }
                                                                        }
                                                                    }
                                                                    $bal = $CrAmt - $DrAmt;
                                                                    $balPre = $CrAmtPre - $DrAmtPre;
                                                                ?>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
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
                                        
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
                                            <td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">III. Total Revenue (I + II)</td>
                                            <td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalRevenueIncome, 2, '.', '') ?></td>
                                            <td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalOtherRevenuePre, 2, '.', '') ?></td>
                                        </tr>
                                        <?php $MainCounter++;?>
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node expanded" id="maingroup">
                                            <td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">IV. Expenses</td>
                                            <td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"></td>
                                            <td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"></td>
                                        </tr>
                                        <?php
                                            $TotalExp = 0;
                                            $TotalExpPre = 0;
                                        ?>
                                        <tr class="treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup">
                                            <td class="parent col-id-sr-no" style="font-size:13px;font-weight:500;">1. Cost of Goods Sold (COGS)</td>
                                            <?php
                                                $COGS = 0;
                                                $COGSPre = 0;
                                                $bal = 0;
                                                $CrAmt = 0;
                                                $DrAmt = 0;
                                                $balPre = 0;
                                                $CrAmtPre = 0;
                                                $DrAmtPre = 0;
                                                
                                                foreach($DirectExpLedgerWiseCommodityWise as $DExpKey=>$DExpVal){
                                                    if($DExpVal["TType"] == "D" && $DExpVal["FY"] == $lastFy){
                                                        $DrAmtPre += $DExpVal["SumAmt"];
                                                    }elseif($DExpVal["TType"] == "C" && $DExpVal["FY"] == $lastFy){
                                                        $CrAmtPre += $DExpVal["SumAmt"];
                                                    }elseif($DExpVal["TType"] == "D" && $DExpVal["FY"] == $FY){
                                                        $DrAmt += $DExpVal["SumAmt"];
                                                    }elseif($DExpVal["TType"] == "C" && $DExpVal["FY"] == $FY){
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
                                                foreach($CurrentInventoryItemWiseValue as $IKey=>$Ival){
                                                    $OpnAmt += $Ival["OpnBal"];
                                                    $PurchAmt += $Ival["PurchAmt"];
                                                    $ClsAmt += $Ival["CurrentValue"];
                                                    $OpnAmtPre += $Ival["OpnBalPre"];
                                                    $PurchAmtPre += $Ival["PurchAmtPre"];
                                                    $ClsAmtPre += $Ival["CurrentValuePre"];
                                                }
                                                $COGS = $OpnAmt + $PurchAmt + $bal - $ClsAmt;
                                                $COGSPre = $OpnAmtPre + $PurchAmtPre + $balPre - $ClsAmtPre;
                                                $TotalExp += $COGS;
                                                $TotalExpPre += $COGSPre;
                                            ?>
                                                <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($COGS, 2, '.', '') ?></td>
                                                <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($COGSPre, 2, '.', '') ?></td>
                                        </tr>   
                                            
                                            <tr class="treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node expanded" id="subgroup">
                                                <td class="col-id-sr-no" style="font-size:12px;font-weight:500;">Opening Amt - add</td>
                                            <?php
                                                    // Opening, Purchase, Closing Amt 
                                                    $OpnAmt = 0;
                                                    $OpnAmtPre = 0;
                                                    foreach($CurrentInventoryItemWiseValue as $IKey=>$Ival){
                                                        $OpnAmt += $Ival["OpnBal"];
                                                        $OpnAmtPre += $Ival["OpnBalPre"];
                                                    }
                                                ?>
                                                <td style="font-size:12px;font-weight:500;text-align:right;"><?php echo  number_format($OpnAmt, 2, '.', '') ?></td>
                                                <td style="font-size:12px;font-weight:500;text-align:right;"><?php echo  number_format($OpnAmtPre, 2, '.', '') ?></td>
                                            </tr>
                                            <?php $SubCounter2++;?>
                                            
                                            <tr class="treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node expanded " id="subgroup">
                                                <td class="col-id-particular" style="font-size:12px;font-weight:500;">Purchase Amt - add</td>
                                            <?php 
                                                    $PurchAmt = 0;
                                                    $PurchAmtPre = 0;
                                                    foreach($CurrentInventoryItemWiseValue as $IKey=>$Ival){
                                                        $PurchAmt += $Ival["PurchAmt"];
                                                        $PurchAmtPre += $Ival["PurchAmtPre"];
                                                    }
                                                ?>
                                                <td style="font-size:12px;font-weight:500;text-align:right;"><?php echo  number_format($PurchAmt, 2, '.', '') ?></td>
                                                <td style="font-size:12px;font-weight:500;text-align:right;"><?php echo  number_format($PurchAmtPre, 2, '.', '') ?></td>
                                            </tr>
                                            <?php $SubCounter2++;?>
                                            
                                            <tr class="treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node expanded " id="subgroup">
                                                <td class="col-id-particular" style="font-size:12px;font-weight:500;">Direct Expense - add</td>
                                                <?php
                                                    $bal = 0;
                                                    $CrAmt = 0;
                                                    $DrAmt = 0;
                                                    $balPre = 0;
                                                    $CrAmtPre = 0;
                                                    $DrAmtPre = 0;
                                                    
                                                    foreach($DirectExpLedgerWiseCommodityWise as $DExpKey=>$DExpVal){
                                                        if($DExpVal["TType"] == "D" && $DExpVal["FY"] == $lastFy){
                                                            $DrAmtPre += $DExpVal["SumAmt"];
                                                        }elseif($DExpVal["TType"] == "C" && $DExpVal["FY"] == $lastFy){
                                                            $CrAmtPre += $DExpVal["SumAmt"];
                                                        }elseif($DExpVal["TType"] == "D" && $DExpVal["FY"] == $FY){
                                                            $DrAmt += $DExpVal["SumAmt"];
                                                        }elseif($DExpVal["TType"] == "C" && $DExpVal["FY"] == $FY){
                                                            $CrAmt += $DExpVal["SumAmt"];
                                                        }
                                                    }
                                                    $bal = $DrAmt - $CrAmt;
                                                    $balPre = $DrAmtPre - $CrAmtPre;
                                                    ?>
                                                    <td style="font-size:12px;font-weight:500;text-align:right;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                    <td style="font-size:12px;font-weight:500;text-align:right;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                
                                            </tr>
                                            <?php $SubCounter2++;?>
                                            <tr class="treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
                                                style="font-size:13px;" id="subgroup">
                                                <td class="col-id-particular">Closing Amt - less</td>
                                            <?php
                                                    $ClsAmt = 0;
                                                    $ClsAmtPre = 0;
                                                    foreach($CurrentInventoryItemWiseValue as $IKey=>$Ival){
                                                        $ClsAmt += $Ival["CurrentValue"];
                                                        $ClsAmtPre += $Ival["CurrentValuePre"];
                                                    }
                                                    $TotalClsAmt += $ClsAmt;
                                                    $TotalClsAmtPre += $ClsAmtPre;
                                                ?>
                                                <td style="text-align:right;background-color: orange;"><?php echo  number_format($ClsAmt, 2, '.', '') ?></td>
                                                <td style="text-align:right;"><?php echo  number_format($ClsAmtPre, 2, '.', '') ?></td>
                                            </tr>
                                            <?php $SubCounter2++;?>
                                            <?php $SubCounter1++;?>
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
                                        
                                        
                                        <tr class="treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup">
                                            <td class="parent col-id-sr-no" style="font-size:13px;font-weight:500;">2. Employee benefits expense</td>
                                            <?php
                                                
                                                $bal = 0;
                                                $CrAmt = 0;
                                                $DrAmt = 0;
                                                $balPre = 0;
                                                $CrAmtPre = 0;
                                                $DrAmtPre = 0;
                                                foreach($EmpBenLedgerWiseCommodityWise as $EmpBenKey=>$EmpBenVal){
                                                    if($EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $lastFy){
                                                        $DrAmtPre += $EmpBenVal["SumAmt"];
                                                    }elseif($EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $lastFy){
                                                        $CrAmtPre += $EmpBenVal["SumAmt"];
                                                    }elseif($EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $FY){
                                                        $DrAmt += $EmpBenVal["SumAmt"];
                                                    }elseif($EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $FY){
                                                        $CrAmt += $EmpBenVal["SumAmt"];
                                                    }
                                                }
                                                $bal = $DrAmt - $CrAmt;
                                                $balPre = $DrAmtPre - $CrAmtPre;
                                                
                                                $TotalExp += $bal;
                                                $TotalExpPre += $balPre;
                                            ?>
                                            <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                        </tr>
                                        <?php 
                                            foreach($ActGroup1UniqueList as $ActGrp1){
                                        ?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node " id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
                                                    <td class="col-id-particular" style="font-size:12px;font-weight:500;"><?php echo $ActGrp1["AccountName"]; ?></td>
                                                <?php 
                                                    $bal = 0;
                                                    $CrAmt = 0;
                                                    $DrAmt = 0;
                                                    $balPre = 0;
                                                    $CrAmtPre = 0;
                                                    $DrAmtPre = 0;
                                                    foreach($EmpBenLedgerWiseCommodityWise as $EmpBenKey=>$EmpBenVal){
                                                        if($EmpBenVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $lastFy){
                                                                $DrAmtPre += $EmpBenVal["SumAmt"];
                                                            }elseif($EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $lastFy){
                                                                $CrAmtPre += $EmpBenVal["SumAmt"];
                                                            }elseif($EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $FY){
                                                                $DrAmt += $EmpBenVal["SumAmt"];
                                                            }elseif($EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $FY){
                                                                $CrAmt += $EmpBenVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $bal = $DrAmt - $CrAmt;
                                                    $balPre = $DrAmtPre - $CrAmtPre;
                                                ?>
                                                    <td style="font-size:12px;font-weight:500;text-align:right;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                    <td style="font-size:12px;font-weight:500;text-align:right;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                </tr>
                                            <?php  
                                                foreach($ActGroup2UniqueList as $val2){
                                                    if($ActGrp1["AccountID"] == $val2["AccountID1"]){
                                            ?>
                                                        <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                        style="font-size:13px;" id="subgroup" data-id="<?php echo $val2["AccountID"] ?>">
                                                            <td class="col-id-particular"><?php echo $val2["AccountName"]; ?></td>    
                                                    <?php
                                                        $bal = 0;
                                                        $CrAmt = 0;
                                                        $DrAmt = 0;
                                                        $balPre = 0;
                                                        $CrAmtPre = 0;
                                                        $DrAmtPre = 0;
                                                        foreach($EmpBenLedgerWiseCommodityWise as $EmpBenKey=>$EmpBenVal){
                                                            if($EmpBenVal["SubActGroupID"] == $val2["AccountID"]){
                                                                if($EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $lastFy){
                                                                    $DrAmtPre += $EmpBenVal["SumAmt"];
                                                                }elseif($EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $lastFy){
                                                                    $CrAmtPre += $EmpBenVal["SumAmt"];
                                                                }elseif($EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $FY){
                                                                    $DrAmt += $EmpBenVal["SumAmt"];
                                                                }elseif($EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $FY){
                                                                    $CrAmt += $EmpBenVal["SumAmt"];
                                                                }
                                                            }
                                                        }
                                                        $bal = $DrAmt - $CrAmt;
                                                        $balPre = $DrAmtPre - $CrAmtPre;
                                                    ?>  
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                        </tr>
                                                        <?php
                                                            foreach($AccountUniqueList as $ActList){
                                                                if($ActList["AccountID2"] == $val2["AccountID"]){
                                                        ?>
                                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter4); ?> treegrid-parent-<?php echo $SubCounter3; ?> parent-node  "
                                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $ActList["AccountID"] ?>">
                                                                        <td class="col-id-particular"><?php echo $ActList["AccountName"]; ?></td>  
                                                                <?php
                                                                    $bal = 0;
                                                                    $CrAmt = 0;
                                                                    $DrAmt = 0;
                                                                    $balPre = 0;
                                                                    $CrAmtPre = 0;
                                                                    $DrAmtPre = 0;
                                                                    foreach($EmpBenLedgerWiseCommodityWise as $EmpBenKey=>$EmpBenVal){
                                                                        if($EmpBenVal["AccountID"] == $ActList["AccountID"]){
                                                                            if($EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $lastFy){
                                                                                $DrAmtPre += $EmpBenVal["SumAmt"];
                                                                            }elseif($EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $lastFy){
                                                                                $CrAmtPre += $EmpBenVal["SumAmt"];
                                                                            }elseif($EmpBenVal["TType"] == "D" && $EmpBenVal["FY"] == $FY){
                                                                                $DrAmt += $EmpBenVal["SumAmt"];
                                                                            }elseif($EmpBenVal["TType"] == "C" && $EmpBenVal["FY"] == $FY){
                                                                                $CrAmt += $EmpBenVal["SumAmt"];
                                                                            }
                                                                        }
                                                                    }
                                                                    $bal = $DrAmt - $CrAmt;
                                                                    $balPre = $DrAmtPre - $CrAmtPre;
                                                                ?>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                    </tr>
                                                                <?php
                                                                    $SubCounter4++;
                                                                }
                                                            }
                                                        $SubCounter3++;
                                                    }
                                                }
                                                $SubCounter2++;
                                            }
                                        ?>
                                        <?php $SubCounter1++;?>
                                        
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
                                        
                                        
                                        <tr class="treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup">
                                            <td class="parent col-id-sr-no" style="font-size:13px;font-weight:500;">3. Finance Costs</td>
                                            <?php
                                                
                                                $bal = 0;
                                                $CrAmt = 0;
                                                $DrAmt = 0;
                                                $balPre = 0;
                                                $CrAmtPre = 0;
                                                $DrAmtPre = 0;
                                                foreach($FinCostLedgerWiseCommodityWise as $FinCostKey=>$FinCostVal){
                                                    if($FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $lastFy){
                                                        $DrAmtPre += $FinCostVal["SumAmt"];
                                                    }elseif($FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $lastFy){
                                                        $CrAmtPre += $FinCostVal["SumAmt"];
                                                    }elseif($FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $FY){
                                                        $DrAmt += $FinCostVal["SumAmt"];
                                                    }elseif($FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $FY){
                                                        $CrAmt += $FinCostVal["SumAmt"];
                                                    }
                                                }
                                                $bal = $DrAmt - $CrAmt;
                                                $balPre = $DrAmtPre - $CrAmtPre;
                                                
                                                $TotalExp += $bal;
                                                $TotalExpPre += $balPre;
                                            ?>
                                            <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                        </tr>
                                        <?php 
                                            foreach($ActGroup1UniqueList as $ActGrp1){
                                        ?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node "id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
                                                    <td class="col-id-particular" style="font-size:12px;font-weight:500;"><?php echo $ActGrp1["AccountName"]; ?></td>
                                                <?php 
                                                    $bal = 0;
                                                    $CrAmt = 0;
                                                    $DrAmt = 0;
                                                    $balPre = 0;
                                                    $CrAmtPre = 0;
                                                    $DrAmtPre = 0;
                                                    foreach($FinCostLedgerWiseCommodityWise as $FinCostKey=>$FinCostVal){
                                                        if($FinCostVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $lastFy){
                                                                $DrAmtPre += $FinCostVal["SumAmt"];
                                                            }elseif($FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $lastFy){
                                                                $CrAmtPre += $FinCostVal["SumAmt"];
                                                            }elseif($FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $FY){
                                                                $DrAmt += $FinCostVal["SumAmt"];
                                                            }elseif($FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $FY){
                                                                $CrAmt += $FinCostVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $bal = $DrAmt - $CrAmt;
                                                    $balPre = $DrAmtPre - $CrAmtPre;
                                                ?>
                                                    <td style="font-size:12px;font-weight:500;text-align:right;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                    <td style="font-size:12px;font-weight:500;text-align:right;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                </tr>
                                            <?php  
                                                foreach($ActGroup2UniqueList as $val2){
                                                    if($ActGrp1["AccountID"] == $val2["AccountID1"]){
                                            ?>
                                                        <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                        style="font-size:13px;" id="subgroup" data-id="<?php echo $val2["AccountID"] ?>">
                                                            <td class="col-id-particular"><?php echo $val2["AccountName"]; ?></td>    
                                                    <?php
                                                        $bal = 0;
                                                        $CrAmt = 0;
                                                        $DrAmt = 0;
                                                        $balPre = 0;
                                                        $CrAmtPre = 0;
                                                        $DrAmtPre = 0;
                                                        foreach($FinCostLedgerWiseCommodityWise as $FinCostKey=>$FinCostVal){
                                                            if($FinCostVal["SubActGroupID"] == $val2["AccountID"]){
                                                                if($FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $lastFy){
                                                                    $DrAmtPre += $FinCostVal["SumAmt"];
                                                                }elseif($FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $lastFy){
                                                                    $CrAmtPre += $FinCostVal["SumAmt"];
                                                                }elseif($FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $FY){
                                                                    $DrAmt += $FinCostVal["SumAmt"];
                                                                }elseif($FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $FY){
                                                                    $CrAmt += $FinCostVal["SumAmt"];
                                                                }
                                                            }
                                                        }
                                                        $bal = $DrAmt - $CrAmt;
                                                        $balPre = $DrAmtPre - $CrAmtPre;
                                                    ?>  
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                        </tr>
                                                        <?php
                                                            foreach($AccountUniqueList as $ActList){
                                                                if($ActList["AccountID2"] == $val2["AccountID"]){
                                                        ?>
                                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter4); ?> treegrid-parent-<?php echo $SubCounter3; ?> parent-node  "
                                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $ActList["AccountID"] ?>">
                                                                        <td class="col-id-particular"><?php echo $ActList["AccountName"]; ?></td>  
                                                                <?php
                                                                    $bal = 0;
                                                                    $CrAmt = 0;
                                                                    $DrAmt = 0;
                                                                    $balPre = 0;
                                                                    $CrAmtPre = 0;
                                                                    $DrAmtPre = 0;
                                                                    foreach($FinCostLedgerWiseCommodityWise as $FinCostKey=>$FinCostVal){
                                                                        if($FinCostVal["AccountID"] == $ActList["AccountID"]){
                                                                            if($FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $lastFy){
                                                                                $DrAmtPre += $FinCostVal["SumAmt"];
                                                                            }elseif($FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $lastFy){
                                                                                $CrAmtPre += $FinCostVal["SumAmt"];
                                                                            }elseif($FinCostVal["TType"] == "D" && $FinCostVal["FY"] == $FY){
                                                                                $DrAmt += $FinCostVal["SumAmt"];
                                                                            }elseif($FinCostVal["TType"] == "C" && $FinCostVal["FY"] == $FY){
                                                                                $CrAmt += $FinCostVal["SumAmt"];
                                                                            }
                                                                        }
                                                                    }
                                                                    $bal = $DrAmt - $CrAmt;
                                                                    $balPre = $DrAmtPre - $CrAmtPre;
                                                                ?>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                    </tr>
                                                                <?php
                                                                    $SubCounter4++;
                                                                }
                                                            }
                                                        $SubCounter3++;
                                                    }
                                                }
                                                $SubCounter2++;
                                            }
                                        ?>
                                        <?php $SubCounter1++;?>
                                        
                                        
                                        <?php
                                            $ActGroup1List = array();
                                            $ActGroup2List = array();
                                            $AccountList = array();
                                            foreach($DepreCostLedgerWiseCommodityWise as $DepreCostKey=>$DepreCostVal){
                                                $new1 = array(
                                                    "AccountID"=>$DepreCostVal["SubActGroupID1"],
                                                    "AccountName"=>$DepreCostVal["SubActGroupName1"]
                                                );
                                                array_push($ActGroup1List,$new1);
                                                $new2 = array(
                                                    "AccountID1"=>$DepreCostVal["SubActGroupID1"],
                                                    "AccountID"=>$DepreCostVal["SubActGroupID"],
                                                    "AccountName"=>$DepreCostVal["SubActGroupName2"]
                                                );
                                                array_push($ActGroup2List,$new2);
                                                
                                                $new = array(
                                                    "AccountID2"=>$DepreCostVal["SubActGroupID"],
                                                    "AccountID"=>$DepreCostVal["AccountID"],
                                                    "AccountName"=>$DepreCostVal["company"]
                                                );
                                                array_push($AccountList,$new);
                                            }
                                            $ActGroup1UniqueList = array_unique($ActGroup1List,SORT_REGULAR);
                                            $ActGroup2UniqueList = array_unique($ActGroup2List,SORT_REGULAR);
                                            $AccountUniqueList = array_unique($AccountList,SORT_REGULAR);
                                        ?>
                                        <tr class="treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node "id="subgroup">
                                            <td class="parent col-id-sr-no" style="font-size:13px;font-weight:500;">4. Depreciation And Amortization Expense</td>
                                            <?php
                                                
                                                $bal = 0;
                                                $CrAmt = 0;
                                                $DrAmt = 0;
                                                $balPre = 0;
                                                $CrAmtPre = 0;
                                                $DrAmtPre = 0;
                                                foreach($DepreCostLedgerWiseCommodityWise as $DepreCostKey=>$DepreCostVal){
                                                    if($DepreCostVal["TType"] == "D" && $DepreCostVal["FY"] == $lastFy){
                                                        $DrAmtPre += $DepreCostVal["SumAmt"];
                                                    }elseif($DepreCostVal["TType"] == "C" && $DepreCostVal["FY"] == $lastFy){
                                                        $CrAmtPre += $DepreCostVal["SumAmt"];
                                                    }elseif($DepreCostVal["TType"] == "D" && $DepreCostVal["FY"] == $FY){
                                                        $DrAmt += $DepreCostVal["SumAmt"];
                                                    }elseif($DepreCostVal["TType"] == "C" && $DepreCostVal["FY"] == $FY){
                                                        $CrAmt += $DepreCostVal["SumAmt"];
                                                    }
                                                }
                                                $bal = $DrAmt - $CrAmt;
                                                $balPre = $DrAmtPre - $CrAmtPre;
                                                $TotalExp += $bal;
                                                $TotalExpPre += $balPre;
                                            ?>
                                            <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                        </tr>
                                        <?php 
                                            foreach($ActGroup1UniqueList as $ActGrp1){
                                        ?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node" id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
                                                    <td class="col-id-particular" style="font-size:12px;font-weight:500;"><?php echo $ActGrp1["AccountName"]; ?></td>
                                                <?php 
                                                    $bal = 0;
                                                    $CrAmt = 0;
                                                    $DrAmt = 0;
                                                    $balPre = 0;
                                                    $CrAmtPre = 0;
                                                    $DrAmtPre = 0;
                                                    foreach($DepreCostLedgerWiseCommodityWise as $DepreCostKey=>$DepreCostVal){
                                                        if($DepreCostVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($DepreCostVal["TType"] == "D" && $DepreCostVal["FY"] == $lastFy){
                                                                $DrAmtPre += $DepreCostVal["SumAmt"];
                                                            }elseif($DepreCostVal["TType"] == "C" && $DepreCostVal["FY"] == $lastFy){
                                                                $CrAmtPre += $DepreCostVal["SumAmt"];
                                                            }elseif($DepreCostVal["TType"] == "D" && $DepreCostVal["FY"] == $FY){
                                                                $DrAmt += $DepreCostVal["SumAmt"];
                                                            }elseif($DepreCostVal["TType"] == "C" && $DepreCostVal["FY"] == $FY){
                                                                $CrAmt += $DepreCostVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $bal = $DrAmt - $CrAmt;
                                                    $balPre = $DrAmtPre - $CrAmtPre;
                                                ?>
                                                    <td style="font-size:12px;font-weight:500;text-align:right;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                    <td style="font-size:12px;font-weight:500;text-align:right;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                </tr>
                                            <?php  
                                                foreach($ActGroup2UniqueList as $val2){
                                                    if($ActGrp1["AccountID"] == $val2["AccountID1"]){
                                            ?>
                                                        <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                        style="font-size:13px;" id="subgroup" data-id="<?php echo $val2["AccountID"] ?>">
                                                            <td class="col-id-particular"><?php echo $val2["AccountName"]; ?></td>    
                                                    <?php
                                                        $bal = 0;
                                                        $CrAmt = 0;
                                                        $DrAmt = 0;
                                                        $balPre = 0;
                                                        $CrAmtPre = 0;
                                                        $DrAmtPre = 0;
                                                        foreach($DepreCostLedgerWiseCommodityWise as $DepreCostKey=>$DepreCostVal){
                                                            if($DepreCostVal["SubActGroupID"] == $val2["AccountID"]){
                                                                if($DepreCostVal["TType"] == "D" && $DepreCostVal["FY"] == $lastFy){
                                                                    $DrAmtPre += $DepreCostVal["SumAmt"];
                                                                }elseif($DepreCostVal["TType"] == "C" && $DepreCostVal["FY"] == $lastFy){
                                                                    $CrAmtPre += $DepreCostVal["SumAmt"];
                                                                }elseif($DepreCostVal["TType"] == "D" && $DepreCostVal["FY"] == $FY){
                                                                    $DrAmt += $DepreCostVal["SumAmt"];
                                                                }elseif($DepreCostVal["TType"] == "C" && $DepreCostVal["FY"] == $FY){
                                                                    $CrAmt += $DepreCostVal["SumAmt"];
                                                                }
                                                            }
                                                        }
                                                        $bal = $DrAmt - $CrAmt;
                                                        $balPre = $DrAmtPre - $CrAmtPre;
                                                    ?>  
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                        </tr>
                                                        <?php
                                                            foreach($AccountUniqueList as $ActList){
                                                                if($ActList["AccountID2"] == $val2["AccountID"]){
                                                        ?>
                                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter4); ?> treegrid-parent-<?php echo $SubCounter3; ?> parent-node  "
                                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $ActList["AccountID"] ?>">
                                                                        <td class="col-id-particular"><?php echo $ActList["AccountName"]; ?></td>  
                                                                <?php
                                                                    $bal = 0;
                                                                    $CrAmt = 0;
                                                                    $DrAmt = 0;
                                                                    $balPre = 0;
                                                                    $CrAmtPre = 0;
                                                                    $DrAmtPre = 0;
                                                                    foreach($DepreCostLedgerWiseCommodityWise as $DepreCostKey=>$DepreCostVal){
                                                                        if($DepreCostVal["AccountID"] == $ActList["AccountID"]){
                                                                            if($DepreCostVal["TType"] == "D" && $DepreCostVal["FY"] == $lastFy){
                                                                                $DrAmtPre += $DepreCostVal["SumAmt"];
                                                                            }elseif($DepreCostVal["TType"] == "C" && $DepreCostVal["FY"] == $lastFy){
                                                                                $CrAmtPre += $DepreCostVal["SumAmt"];
                                                                            }elseif($DepreCostVal["TType"] == "D" && $DepreCostVal["FY"] == $FY){
                                                                                $DrAmt += $DepreCostVal["SumAmt"];
                                                                            }elseif($DepreCostVal["TType"] == "C" && $DepreCostVal["FY"] == $FY){
                                                                                $CrAmt += $DepreCostVal["SumAmt"];
                                                                            }
                                                                        }
                                                                    }
                                                                    $bal = $DrAmt - $CrAmt;
                                                                    $balPre = $DrAmtPre - $CrAmtPre;
                                                                ?>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                    </tr>
                                                                <?php
                                                                    $SubCounter4++;
                                                                }
                                                            }
                                                        $SubCounter3++;
                                                    }
                                                }
                                                $SubCounter2++;
                                            }
                                        ?>
                                        <?php $SubCounter1++;?>
                                        
                                        
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
                                        <tr class="treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node "id="subgroup">
                                            <td class="parent col-id-sr-no" style="font-size:13px;font-weight:500;">5. Other Expenses</td>
                                            <?php
                                                
                                                $bal = 0;
                                                $CrAmt = 0;
                                                $DrAmt = 0;
                                                $balPre = 0;
                                                $CrAmtPre = 0;
                                                $DrAmtPre = 0;
                                                foreach($IndExpLedgerWiseCommodityWise as $IndExpKey=>$IndExpVal){
                                                    if($IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $lastFy){
                                                        $DrAmtPre += $IndExpVal["SumAmt"];
                                                    }elseif($IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $lastFy){
                                                        $CrAmtPre += $IndExpVal["SumAmt"];
                                                    }elseif($IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $FY){
                                                        $DrAmt += $IndExpVal["SumAmt"];
                                                    }elseif($IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $FY){
                                                        $CrAmt += $IndExpVal["SumAmt"];
                                                    }
                                                }
                                                $bal = $DrAmt - $CrAmt;
                                                $balPre = $DrAmtPre - $CrAmtPre;
                                                
                                                $TotalExp += $bal;
                                                $TotalExpPre += $balPre;
                                            ?>
                                            <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                        </tr>
                                        <?php 
                                            foreach($ActGroup1UniqueList as $ActGrp1){
                                        ?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
                                                style="font-size:13px;" id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
                                                    <td class="col-id-particular"><?php echo $ActGrp1["AccountName"]; ?></td>
                                                <?php 
                                                    $bal = 0;
                                                    $CrAmt = 0;
                                                    $DrAmt = 0;
                                                    $balPre = 0;
                                                    $CrAmtPre = 0;
                                                    $DrAmtPre = 0;
                                                    foreach($IndExpLedgerWiseCommodityWise as $IndExpKey=>$IndExpVal){
                                                        if($IndExpVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $lastFy){
                                                                $DrAmtPre += $IndExpVal["SumAmt"];
                                                            }elseif($IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $lastFy){
                                                                $CrAmtPre += $IndExpVal["SumAmt"];
                                                            }elseif($IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $FY){
                                                                $DrAmt += $IndExpVal["SumAmt"];
                                                            }elseif($IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $FY){
                                                                $CrAmt += $IndExpVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $bal = $DrAmt - $CrAmt;
                                                    $balPre = $DrAmtPre - $CrAmtPre;
                                                ?>
                                                    <td style="text-align:right;font-weight:600;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                    <td style="text-align:right;font-weight:600;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                </tr>
                                            <?php  
                                                foreach($ActGroup2UniqueList as $val2){
                                                    if($ActGrp1["AccountID"] == $val2["AccountID1"]){
                                            ?>
                                                        <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                        style="font-size:13px;" id="subgroup" data-id="<?php echo $val2["AccountID"] ?>">
                                                            <td class="col-id-particular"><?php echo $val2["AccountName"]; ?></td>    
                                                    <?php
                                                        $bal = 0;
                                                        $CrAmt = 0;
                                                        $DrAmt = 0;
                                                        $balPre = 0;
                                                        $CrAmtPre = 0;
                                                        $DrAmtPre = 0;
                                                        foreach($IndExpLedgerWiseCommodityWise as $IndExpKey=>$IndExpVal){
                                                            if($IndExpVal["SubActGroupID"] == $val2["AccountID"]){
                                                                if($IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $lastFy){
                                                                    $DrAmtPre += $IndExpVal["SumAmt"];
                                                                }elseif($IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $lastFy){
                                                                    $CrAmtPre += $IndExpVal["SumAmt"];
                                                                }elseif($IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $FY){
                                                                    $DrAmt += $IndExpVal["SumAmt"];
                                                                }elseif($IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $FY){
                                                                    $CrAmt += $IndExpVal["SumAmt"];
                                                                }
                                                            }
                                                        }
                                                        $bal = $DrAmt - $CrAmt;
                                                        $balPre = $DrAmtPre - $CrAmtPre;
                                                    ?>  
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                        </tr>
                                                        <?php
                                                            foreach($AccountUniqueList as $ActList){
                                                                if($ActList["AccountID2"] == $val2["AccountID"]){
                                                        ?>
                                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter4); ?> treegrid-parent-<?php echo $SubCounter3; ?> parent-node  "
                                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $ActList["AccountID"] ?>">
                                                                        <td class="col-id-particular"><?php echo $ActList["AccountName"]; ?></td>  
                                                                <?php
                                                                    $bal = 0;
                                                                    $CrAmt = 0;
                                                                    $DrAmt = 0;
                                                                    $balPre = 0;
                                                                    $CrAmtPre = 0;
                                                                    $DrAmtPre = 0;
                                                                    foreach($IndExpLedgerWiseCommodityWise as $IndExpKey=>$IndExpVal){
                                                                        if($IndExpVal["AccountID"] == $ActList["AccountID"]){
                                                                            if($IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $lastFy){
                                                                                $DrAmtPre += $IndExpVal["SumAmt"];
                                                                            }elseif($IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $lastFy){
                                                                                $CrAmtPre += $IndExpVal["SumAmt"];
                                                                            }elseif($IndExpVal["TType"] == "D" && $IndExpVal["FY"] == $FY){
                                                                                $DrAmt += $IndExpVal["SumAmt"];
                                                                            }elseif($IndExpVal["TType"] == "C" && $IndExpVal["FY"] == $FY){
                                                                                $CrAmt += $IndExpVal["SumAmt"];
                                                                            }
                                                                        }
                                                                    }
                                                                    $bal = $DrAmt - $CrAmt;
                                                                    $balPre = $DrAmtPre - $CrAmtPre;
                                                                ?>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                                    </tr>
                                                                <?php
                                                                    $SubCounter4++;
                                                                }
                                                            }
                                                        $SubCounter3++;
                                                    }
                                                }
                                                $SubCounter2++;
                                            }
                                        ?>
                                        <?php $SubCounter1++;?>
                                        
                                        <?php $MainCounter++;?>
                                        
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
                                            <td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">V. Profit before Exceptional and extraordinary items and tax(III- IV)</td>
                                            <td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalRevenueIncome - $TotalExp, 2, '.', '') ?></td>
                                            <td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalOtherRevenuePre - $TotalExpPre, 2, '.', '') ?></td>
                                        </tr>
                                        <?php $MainCounter++;?>
                                        
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
                                            <td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">VI. Exceptional Items</td>
                                            <td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format(0, 2, '.', '') ?></td>
                                            <td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format(0, 2, '.', '') ?></td>
                                        </tr>
                                        <?php $MainCounter++;?>
                                        
                                        
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
                                            <td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">VII. Profit before extraordinary items and tax (V - VII)</td>
                                            <td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalRevenueIncome - $TotalExp, 2, '.', '') ?></td>
                                            <td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalOtherRevenuePre - $TotalExpPre, 2, '.', '') ?></td>
                                        </tr>
                                        <?php $MainCounter++;?>
                                        
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
                                            <td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">VIII. Extraordinary Items</td>
                                            <td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format(0, 2, '.', '') ?></td>
                                            <td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format(0, 2, '.', '') ?></td>
                                        </tr>
                                        <?php $MainCounter++;?>
                                        
                                        
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
                                            <td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">IX. Profit before tax(VII - VIII)</td>
                                            <td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalRevenueIncome - $TotalExp, 2, '.', '') ?></td>
                                            <td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalOtherRevenuePre - $TotalExpPre, 2, '.', '') ?></td>
                                        </tr>
                                        <?php $MainCounter++;?>
                                        
                                        <?php
                                            $ActGroup1List = array();
                                            $ActGroup2List = array();
                                            $AccountList = array();
                                            foreach($TaxExpLedgerWiseCommodityWise as $TaxExpKey=>$TaxExpVal){
                                                $new1 = array(
                                                    "AccountID"=>$TaxExpVal["SubActGroupID1"],
                                                    "AccountName"=>$TaxExpVal["SubActGroupName1"]
                                                );
                                                array_push($ActGroup1List,$new1);
                                                $new2 = array(
                                                    "AccountID1"=>$TaxExpVal["SubActGroupID1"],
                                                    "AccountID"=>$TaxExpVal["SubActGroupID"],
                                                    "AccountName"=>$TaxExpVal["SubActGroupName2"]
                                                );
                                                array_push($ActGroup2List,$new2);
                                                
                                                $new = array(
                                                    "AccountID2"=>$TaxExpVal["SubActGroupID"],
                                                    "AccountID"=>$TaxExpVal["AccountID"],
                                                    "AccountName"=>$TaxExpVal["company"]
                                                );
                                                array_push($AccountList,$new);
                                            }
                                            $ActGroup1UniqueList = array_unique($ActGroup1List,SORT_REGULAR);
                                            $ActGroup2UniqueList = array_unique($ActGroup2List,SORT_REGULAR);
                                            $AccountUniqueList = array_unique($AccountList,SORT_REGULAR);
                                            $TotalTax = 0;
                                            $TotalTaxPre = 0;
                                        ?>
                                        
                                        
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node" id="maingroup">
                                            <td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">X. Tax Expense</td>
                                            <?php
                                                
                                                $bal = 0;
                                                $CrAmt = 0;
                                                $DrAmt = 0;
                                                $balPre = 0;
                                                $CrAmtPre = 0;
                                                $DrAmtPre = 0;
                                                foreach($TaxExpLedgerWiseCommodityWise as $TaxExpKey=>$TaxExpVal){
                                                    if($TaxExpVal["TType"] == "D" && $TaxExpVal["FY"] == $lastFy){
                                                        $DrAmtPre += $TaxExpVal["SumAmt"];
                                                    }elseif($TaxExpVal["TType"] == "C" && $TaxExpVal["FY"] == $lastFy){
                                                        $CrAmtPre += $TaxExpVal["SumAmt"];
                                                    }elseif($TaxExpVal["TType"] == "D" && $TaxExpVal["FY"] == $FY){
                                                        $DrAmt += $TaxExpVal["SumAmt"];
                                                    }elseif($TaxExpVal["TType"] == "C" && $TaxExpVal["FY"] == $FY){
                                                        $CrAmt += $TaxExpVal["SumAmt"];
                                                    }
                                                }
                                                $bal =  $DrAmt - $CrAmt;
                                                $balPre = $DrAmtPre - $CrAmtPre;
                                                $TotalTax += $bal;
                                                $TotalTaxPre += $balPre;
                                            ?>
                                            <td style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                            <td style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                        </tr>
                                        <?php 
                                            foreach($ActGroup1UniqueList as $ActGrp1){
                                        ?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
                                                    <td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $ActGrp1["AccountName"]; ?></td>
                                                <?php 
                                                    $bal = 0;
                                                    $CrAmt = 0;
                                                    $DrAmt = 0;
                                                    $balPre = 0;
                                                    $CrAmtPre = 0;
                                                    $DrAmtPre = 0;
                                                    foreach($TaxExpLedgerWiseCommodityWise as $TaxExpKey=>$TaxExpVal){
                                                        if($TaxExpVal["SubActGroupID1"] == $ActGrp1["AccountID"]){
                                                            if($TaxExpVal["TType"] == "D" && $TaxExpVal["FY"] == $lastFy){
                                                                $DrAmtPre += $TaxExpVal["SumAmt"];
                                                            }elseif($TaxExpVal["TType"] == "C" && $TaxExpVal["FY"] == $lastFy){
                                                                $CrAmtPre += $TaxExpVal["SumAmt"];
                                                            }elseif($TaxExpVal["TType"] == "D" && $TaxExpVal["FY"] == $FY){
                                                                $DrAmt += $TaxExpVal["SumAmt"];
                                                            }elseif($TaxExpVal["TType"] == "C" && $TaxExpVal["FY"] == $FY){
                                                                $CrAmt += $TaxExpVal["SumAmt"];
                                                            }
                                                        }
                                                    }
                                                    $bal =  $DrAmt - $CrAmt;
                                                    $balPre = $DrAmtPre - $CrAmtPre;
                                                ?>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                </tr>
                                            <?php  
                                                foreach($ActGroup2UniqueList as $val2){
                                                    if($ActGrp1["AccountID"] == $val2["AccountID1"]){
                                            ?>
                                                        <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
                                                        style="font-size:13px;" id="subgroup" data-id="<?php echo $val2["AccountID"] ?>">
                                                            <td class="col-id-particular"><?php echo $val2["AccountName"]; ?></td>    
                                                    <?php
                                                        $bal = 0;
                                                        $CrAmt = 0;
                                                        $DrAmt = 0;
                                                        $balPre = 0;
                                                        $CrAmtPre = 0;
                                                        $DrAmtPre = 0;
                                                        foreach($TaxExpLedgerWiseCommodityWise as $TaxExpKey=>$TaxExpVal){
                                                            if($TaxExpVal["SubActGroupID"] == $val2["AccountID"]){
                                                                if($TaxExpVal["TType"] == "D" && $TaxExpVal["FY"] == $lastFy){
                                                                    $DrAmtPre += $TaxExpVal["SumAmt"];
                                                                }elseif($TaxExpVal["TType"] == "C" && $TaxExpVal["FY"] == $lastFy){
                                                                    $CrAmtPre += $TaxExpVal["SumAmt"];
                                                                }elseif($TaxExpVal["TType"] == "D" && $TaxExpVal["FY"] == $FY){
                                                                    $DrAmt += $TaxExpVal["SumAmt"];
                                                                }elseif($TaxExpVal["TType"] == "C" && $TaxExpVal["FY"] == $FY){
                                                                    $CrAmt += $TaxExpVal["SumAmt"];
                                                                }
                                                            }
                                                        }
                                                        $bal =  $DrAmt - $CrAmt;
                                                        $balPre = $DrAmtPre - $CrAmtPre;
                                                    ?>  
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:500;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
                                                        </tr>
                                                        <?php
                                                            foreach($AccountUniqueList as $ActList){
                                                                if($ActList["AccountID2"] == $val2["AccountID"]){
                                                        ?>
                                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $ActList["AccountID"] ?>">
                                                                        <td class="col-id-particular"><?php echo $ActList["AccountName"]; ?></td>  
                                                                <?php
                                                                    $bal = 0;
                                                                    $CrAmt = 0;
                                                                    $DrAmt = 0;
                                                                    $balPre = 0;
                                                                    $CrAmtPre = 0;
                                                                    $DrAmtPre = 0;
                                                                    foreach($TaxExpLedgerWiseCommodityWise as $TaxExpKey=>$TaxExpVal){
                                                                        if($TaxExpVal["AccountID"] == $ActList["AccountID"]){
                                                                            if($TaxExpVal["TType"] == "D" && $TaxExpVal["FY"] == $lastFy){
                                                                                $DrAmtPre += $TaxExpVal["SumAmt"];
                                                                            }elseif($TaxExpVal["TType"] == "C" && $TaxExpVal["FY"] == $lastFy){
                                                                                $CrAmtPre += $TaxExpVal["SumAmt"];
                                                                            }elseif($TaxExpVal["TType"] == "D" && $TaxExpVal["FY"] == $FY){
                                                                                $DrAmt += $TaxExpVal["SumAmt"];
                                                                            }elseif($TaxExpVal["TType"] == "C" && $TaxExpVal["FY"] == $FY){
                                                                                $CrAmt += $TaxExpVal["SumAmt"];
                                                                            }
                                                                        }
                                                                    }
                                                                    $bal =  $DrAmt - $CrAmt;
                                                                    $balPre = $DrAmtPre - $CrAmtPre;
                                                                ?>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($bal, 2, '.', '') ?></td>
                                                                        <td style="text-align:right;font-weight:400;"><?php echo  number_format($balPre, 2, '.', '') ?></td>
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
                                        
                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
                                            <td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">XI. Profit(Loss) for the period (IX-X)</td>
                                            <td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalRevenueIncome - $TotalExp - $TotalTax, 2, '.', '') ?></td>
                                            <td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalOtherRevenuePre - $TotalExpPre - $TotalTaxPre, 2, '.', '') ?></td>
                                        </tr>
                                        <?php $MainCounter++;?>
                                        
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="revenue_from_operation-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Revenue from Operation</h4>
                </div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table_annexure">
                                <table class="tree table-bordered table_tradePayable_data" id="table_tradePayable_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                            <?php
                                            foreach($TotalSaleGroupWise as $key=>$val){
                                        ?>
                                            <th><?php echo $val["name"];?></th>
                                        <?php
                                            }    
                                        ?>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Sale Amount</td>
                                        <?php
                                                $TotalSaleAmt = 0;
                                            foreach($TotalSaleGroupWise as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php echo number_format($val["SaleAmt"], 2, '.', ',');?></td>
                                        <?php
                                                $TotalSaleAmt += $val["SaleAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php echo number_format($TotalSaleAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Sale Return Amount</td>
                                        <?php
                                            foreach($ItemGroup as $key=>$val){
                                        ?>
                                            <td style="text-align:right;">0.00</td>
                                            
                                        <?php
                                            }    
                                        ?>
                                            <td>0.00</td>
                                        </tr>
                                    </tbody>
                                </table>   
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
    
    
    <div class="modal fade" id="OtherIncome-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Other Income</h4>
                </div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table_annexure">
                                <table class="tree table-bordered table_OtherIncome_data" id="table_OtherIncome_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                            <th>Opening</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Closing</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach($OtherIncomeSubgroup2Wise as $key=>$val){
                                        ?>
                                            <tr>
                                                <td style="font-weight:700;"><?php echo $val["SubActGroupName1"];?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        <?php
                                                $TotalCR = 0;
                                                $TotalDR = 0;
                                                $TotalBalance = 0;
                                                foreach($val["SubGroup2"] as $SG2Key=>$SG2val){
                                                    $TotalCR += $SG2val["CR"];
                                                    $TotalDR += $SG2val["DR"];
                                                    $TotalBalance += $SG2val["Balance"];
                                            ?>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $SG2val["ActSubGroupName2"];?></td>
                                                    <td></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["CR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["DR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["Balance"], 2, '.', ''); ?></td>
                                                </tr>
                                            <?php
                                                }
                                                
                                            ?>
                                                <tr>
                                                    <td style="font-weight:700;">Total for <?php echo $val["SubActGroupName1"];?></td>
                                                    <td></td>
                                                    <td style="text-align:right;font-weight:700;"><?php echo number_format($TotalCR, 2, '.', ''); ?></td>
                                                    <td style="text-align:right;font-weight:700;"><?php echo number_format($TotalDR, 2, '.', ''); ?></td>
                                                    <td style="text-align:right;font-weight:700;"><?php echo number_format($TotalBalance, 2, '.', ''); ?></td>
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
    
    <div class="modal fade" id="COGS-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">COGS Details</h4>
                </div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        
                        <div class="col-md-12">
                            <h4>Inventory Opening Value</h4>
                            <div class="table_annexure">
                                <table class="tree table-bordered table_TradeReceivable_data" id="table_TradeReceivable_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                        <?php
                                            foreach($CurrentInventoryItemWiseValue as $key=>$val){
                                        ?>
                                            <th><?php echo $val["name"];?></th>
                                        <?php
                                            }    
                                        ?>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Opening Amt</td>
                                            <?php
                                                $TotalOpnAmt = 0;
                                            foreach($CurrentInventoryItemWiseValue as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php echo number_format($val["OpnBal"], 2, '.', ',');?></td>
                                        <?php
                                                $TotalOpnAmt += $val["OpnBal"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php echo number_format($TotalOpnAmt, 2, '.', ',');?></td>
                                        </tr>
                                    </tbody>
                                </table>   
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <h4>Total Purchase Details</h4>
                            <div class="table_annexure">
                                <table class="tree table-bordered table_TradeReceivable_data" id="table_TradeReceivable_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                        <?php
                                            foreach($CurrentInventoryItemWiseValue as $key=>$val){
                                        ?>
                                            <th><?php echo $val["name"];?></th>
                                        <?php
                                            }    
                                        ?>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       
                                        <tr>
                                            <td>Purchase Amt</td>
                                            <?php
                                                $TotalPurchAmt = 0;
                                            foreach($CurrentInventoryItemWiseValue as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php echo number_format($val["PurchAmt"], 2, '.', ',');?></td>
                                        <?php
                                                $TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                    </tbody>
                                </table>   
                            </div>
                        </div>
                        
                        
                        <div class="col-md-12">
                            <h4>Direct Expense</h4>
                            <div class="table_annexure">
                                <table class="tree table-bordered table_OtherIncome_data" id="table_OtherIncome_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                            <th>Opening</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Closing</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach($DirectExpSubgroup2Wise as $key=>$val){
                                        ?>
                                            <tr>
                                                <td style="font-weight:700;"><?php echo $val["SubActGroupName1"];?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        <?php
                                                $TotalCR = 0;
                                                $TotalDR = 0;
                                                $TotalBalance = 0;
                                                foreach($val["SubGroup2"] as $SG2Key=>$SG2val){
                                                    $TotalCR += $SG2val["CR"];
                                                    $TotalDR += $SG2val["DR"];
                                                    $TotalBalance += $SG2val["Balance"];
                                            ?>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $SG2val["ActSubGroupName2"];?></td>
                                                    <td></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["CR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["DR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["Balance"], 2, '.', ''); ?></td>
                                                </tr>
                                            <?php
                                                }
                                                
                                            ?>
                                                <tr>
                                                    <td style="font-weight:700;">Total for <?php echo $val["SubActGroupName1"];?></td>
                                                    <td></td>
                                                    <td style="text-align:right;font-weight:700;"><?php echo number_format($TotalCR, 2, '.', ''); ?></td>
                                                    <td style="text-align:right;font-weight:700;"><?php echo number_format($TotalDR, 2, '.', ''); ?></td>
                                                    <td style="text-align:right;font-weight:700;"><?php echo number_format($TotalBalance, 2, '.', ''); ?></td>
                                                </tr>
                                        <?php
                                            }
                                        ?>
                                           
                                        
                                    </tbody>
                                </table>   
                            </div>
                        </div>
                        
                        
                        
                        <div class="col-md-12">
                            <h4>Closing Inventory Details (Inventory Value : <?php echo number_format($CurrentInventoryValue, 2, '.', ',');?>)</h4>
                            <span style="color:red;font-size:10px;">Calculated closing inventory as per FIFO Based.</span>
                            <div class="table_annexure">
                                <table class="tree table-bordered table_TradeReceivable_data" id="table_TradeReceivable_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                        <?php
                                            foreach($CurrentInventoryItemWiseValue as $key=>$val){
                                        ?>
                                            <th><?php echo $val["name"];?></th>
                                        <?php
                                            }    
                                        ?>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Opening Amt</td>
                                            <?php
                                                $TotalOpnAmt = 0;
                                            foreach($CurrentInventoryItemWiseValue as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php echo number_format($val["OpnBal"], 2, '.', ',');?></td>
                                        <?php
                                                $TotalOpnAmt += $val["OpnBal"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php echo number_format($TotalOpnAmt, 2, '.', ',');?></td>
                                        </tr>
                                        <tr>
                                            <td>Purchase Amt</td>
                                            <?php
                                                $TotalPurchAmt = 0;
                                            foreach($CurrentInventoryItemWiseValue as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php echo number_format($val["PurchAmt"], 2, '.', ',');?></td>
                                        <?php
                                                $TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        <tr>
                                            <td>Sale Amt</td>
                                            <?php
                                                $TotalSaleAmt = 0;
                                            foreach($CurrentInventoryItemWiseValue as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php echo number_format($val["SaleAmt"], 2, '.', ',');?></td>
                                        <?php
                                                $TotalSaleAmt += $val["SaleAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php echo number_format($TotalSaleAmt, 2, '.', ',');?></td>
                                        </tr>
                                    </tbody>
                                </table>   
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="EmpBen-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Employee Benefits Expense</h4>
                </div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table_annexure">
                                <table class="tree table-bordered table_OtherIncome_data" id="table_OtherIncome_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                            <th>Opening</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Closing</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach($EMPBenSubgroup2Wise as $key=>$val){
                                        ?>
                                            <tr>
                                                <td style="font-weight:700;"><?php echo $val["SubActGroupName1"];?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        <?php
                                                $TotalCR = 0;
                                                $TotalDR = 0;
                                                $TotalBalance = 0;
                                                foreach($val["SubGroup2"] as $SG2Key=>$SG2val){
                                                    $TotalCR += $SG2val["CR"];
                                                    $TotalDR += $SG2val["DR"];
                                                    $TotalBalance += $SG2val["Balance"];
                                            ?>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $SG2val["ActSubGroupName2"];?></td>
                                                    <td></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["CR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["DR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["Balance"], 2, '.', ''); ?></td>
                                                </tr>
                                            <?php
                                                }
                                                
                                            ?>
                                                <tr>
                                                    <td style="font-weight:700;">Total for <?php echo $val["SubActGroupName1"];?></td>
                                                    <td></td>
                                                    <td style="text-align:right;font-weight:700;"><?php echo number_format($TotalCR, 2, '.', ''); ?></td>
                                                    <td style="text-align:right;font-weight:700;"><?php echo number_format($TotalDR, 2, '.', ''); ?></td>
                                                    <td style="text-align:right;font-weight:700;"><?php echo number_format($TotalBalance, 2, '.', ''); ?></td>
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
    
    <div class="modal fade" id="FinCost-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Finance Costs</h4>
                </div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table_annexure">
                                <table class="tree table-bordered table_OtherIncome_data" id="table_OtherIncome_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                            <th>Opening</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Closing</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach($FinCostSubgroup2Wise as $key=>$val){
                                        ?>
                                            <tr>
                                                <td style="font-weight:700;"><?php echo $val["SubActGroupName1"];?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        <?php
                                                $TotalCR = 0;
                                                $TotalDR = 0;
                                                $TotalBalance = 0;
                                                foreach($val["SubGroup2"] as $SG2Key=>$SG2val){
                                                    $TotalCR += $SG2val["CR"];
                                                    $TotalDR += $SG2val["DR"];
                                                    $TotalBalance += $SG2val["Balance"];
                                            ?>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $SG2val["ActSubGroupName2"];?></td>
                                                    <td></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["CR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["DR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["Balance"], 2, '.', ''); ?></td>
                                                </tr>
                                            <?php
                                                }
                                                
                                            ?>
                                                <tr>
                                                    <td style="font-weight:700;">Total for <?php echo $val["SubActGroupName1"];?></td>
                                                    <td></td>
                                                    <td style="text-align:right;font-weight:700;"><?php echo number_format($TotalCR, 2, '.', ''); ?></td>
                                                    <td style="text-align:right;font-weight:700;"><?php echo number_format($TotalDR, 2, '.', ''); ?></td>
                                                    <td style="text-align:right;font-weight:700;"><?php echo number_format($TotalBalance, 2, '.', ''); ?></td>
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
    
    <div class="modal fade" id="OtherExp-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Other Expense</h4>
                </div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table_annexure">
                                <table class="tree table-bordered table_OtherIncome_data" id="table_OtherIncome_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                            <th>Opening</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Closing</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach($IndirectExpSubgroup2Wise as $key=>$val){
                                        ?>
                                            <tr>
                                                <td style="font-weight:700;"><?php echo $val["SubActGroupName1"];?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        <?php
                                                $TotalCR = 0;
                                                $TotalDR = 0;
                                                $TotalBalance = 0;
                                                foreach($val["SubGroup2"] as $SG2Key=>$SG2val){
                                                    $TotalCR += $SG2val["CR"];
                                                    $TotalDR += $SG2val["DR"];
                                                    $TotalBalance += $SG2val["Balance"];
                                            ?>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $SG2val["ActSubGroupName2"];?></td>
                                                    <td></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["CR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["DR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["Balance"], 2, '.', ''); ?></td>
                                                </tr>
                                            <?php
                                                }
                                                
                                            ?>
                                                <tr>
                                                    <td style="font-weight:700;">Total for <?php echo $val["SubActGroupName1"];?></td>
                                                    <td></td>
                                                    <td style="text-align:right;font-weight:700;"><?php echo number_format($TotalCR, 2, '.', ''); ?></td>
                                                    <td style="text-align:right;font-weight:700;"><?php echo number_format($TotalDR, 2, '.', ''); ?></td>
                                                    <td style="text-align:right;font-weight:700;"><?php echo number_format($TotalBalance, 2, '.', ''); ?></td>
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
        function GetAnnexure(Name){
            if(Name == "I. Revenue from Operation"){
                $('#revenue_from_operation-modal').modal('show');
            }else if(Name == "II. Other Income"){
                $('#OtherIncome-modal').modal('show');
            }else if(Name == "1. Cost of Goods Sold (COGS)"){
                $('#COGS-modal').modal('show');
            }else if(Name == "2. Employee benefits expense"){
                $('#EmpBen-modal').modal('show');
            }else if(Name == "3. Finance Costs"){
                $('#FinCost-modal').modal('show');
            }else if(Name == "5. Other Expenses"){
                $('#OtherExp-modal').modal('show');
            }
        }
        
    </script>
    <script type="text/javascript">
        function printPage() {
            var html_filter_name = $('.report_for').html();
            var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
            var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">' + document.getElementsByTagName('table')[0].innerHTML + '</table>';
            var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
            heading_data += '<tr>';
            heading_data += '<td style="text-align:center;"colspan="3">Profit Loss Sheet</td>';
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