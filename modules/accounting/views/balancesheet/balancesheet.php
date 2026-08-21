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
            <div class="row ">
                <div class="col-md-5">
                    <a class="btn btn-default" href="javascript:void(0);" style="margin-bottom: 20px;margin-left: 10px;" onclick="printPage();">Print</a>
                </div>

                <div class="col-md-8">
                    <?php
                    $fy = $this->session->userdata('finacial_year');
                    $lastFy = $fy - 1;
                    $fy_ = $fy + 1;
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
                                            <th></th>
                                            <th colspan="3" class="text-center th_total">
                                                <?php echo _l('total'); ?>
                                            </th>
                                        </tr>
                                        <tr class="tr_header">
                                            <th class="th_total" ></th>
                                            <th class="th_total" >Note</th>
                                            <th class="th_total" ><?php echo $CurrYrLastDate; ?></th>
                                            <th class="th_total"><?php echo $LastYrLastDate; ?></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $MainCounter = 1000;
                                        $SubCounter = 2000;
                                        $Counter2 = 3000;
                                        $i = 1;
                                        foreach ($nestedData as $key => $val) {
                                            $MainGroupSum_curr = 0;
                                            $MainGroupSum_pre = 0;
                                            $totalmaingroup = 0;
                                            
                                        ?>
                                            <tr class="treegrid-<?php echo $MainCounter; ?> parent-node expanded"
                                                style="font-size:14px;"id="maingroup">
                                                <td class="parent"><?php echo $val['MainGroup']; ?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>

                                            <?php

                                            foreach ($val['SubGroups1'] as $key1 => $val1) {
                                                ?>
                                                <tr class="treegrid-<?php echo html_entity_decode($SubCounter); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node expanded "
                                                    style="font-size:13px;" id="subgroup">
                                                    <td class="parent"><?php echo $val1["SubGroup1Name"]; ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <?php
                                                $totalsubgroup = 0;
                                                $totalsubgroup11 = 0;
                                                foreach ($val1['SubGroups'] as $key2 => $val2) {
                                                    ?>
                                                    <tr
                                                        class="treegrid-<?php echo html_entity_decode($Counter2); ?> treegrid-parent-<?php echo $SubCounter; ?> parent-node expanded" id="subgroup1">
                                                        <td class="parent"><?php echo $val2["SubGroupName"]; ?></td>
                                                        <?php
                                                        $crAmt = 0;
                                                        $drAmt = 0;
                                                        $OpnAmt = 0;

                                                        // Current FY Year
                                                        foreach ($ledger_data->Cur_yr_ledger as $Key33 => $val33) {
                                                            if ($val33["SubActGroupID"] == $val2["SubActGroupID"] && $val33["TType"] == "C" && $val33["FY"] == $fy) {
                                                                $crAmt = $val33["SUMAmt"];
                                                            }
                                                            if ($val33["SubActGroupID"] == $val2["SubActGroupID"] && $val33["TType"] == "D" && $val33["FY"] == $fy) {
                                                                $drAmt = $val33["SUMAmt"];
                                                            }
                                                        }
                                                        foreach ($OpnBal->Cur_yr_OpnBal as $Key44 => $val44) {
                                                            if ($val44["SubActGroupID"] == $val2["SubActGroupID"] && $val44["FY"] == $fy) {
                                                                $OpnAmt = $val44["SUMAmt"];
                                                            }
                                                        }
                                                        if($i>1){
                                                            // Assets
                                                            $ClsBal = $OpnAmt + $drAmt - $crAmt;
                                                        }else{
                                                            // Liability
                                                            $ClsBal = $OpnAmt + $crAmt - $drAmt;
                                                        }
                                                        
                                                        if($val2["SubActGroupID"] == "1000001"){
                                                            $ClsBal += $PlData;
                                                            $totalmaingroup += $PlData;
                                                        }
                                                        if($val2["SubActGroupID"] == "1000015"){
                                                            $ClsBal += $CurrentInventoryValue;
                                                            $totalmaingroup += $CurrentInventoryValue;
                                                        }
                                                        foreach($pay_rec_data as $prKey=>$prVal){
                                                            if($prVal["SubActGroupID"] == $val2["SubActGroupID"]){
                                                                $ClsBal += $prVal["Balance"];
                                                                $totalmaingroup += $prVal["Balance"];
                                                            }
                                                        }
                                                        
                                                        $MainGroupSum_curr += $ClsBal;
                                                        
                                                        
                                                        // Last FY Year
                                                        $crAmt2 = 0;
                                                        $drAmt2 = 0;
                                                        $OpnAmt2 = 0;
                                                        foreach ($ledger_data->Last_yr_ledger as $Key55 => $val55) {
                                                            if ($val55["SubActGroupID"] == $val2["SubActGroupID"] && $val55["TType"] == "C" && $val55["FY"] == $lastFy) {
                                                                $crAmt2 = $val55["SUMAmt"];
                                                            }
                                                            if ($val55["SubActGroupID"] == $val2["SubActGroupID"] && $val55["TType"] == "D" && $val55["FY"] == $lastFy) {
                                                                $drAmt2 = $val55["SUMAmt"];
                                                            }
                                                        }
                                                        foreach ($OpnBal->Last_yr_OpnBal as $Key66 => $val66) {
                                                            if ($val66["SubActGroupID"] == $val2["SubActGroupID"] && $val66["FY"] == $lastFy) {
                                                                $OpnAmt2 = $val66["SUMAmt"];
                                                            }
                                                        }

                                                        $ClsBal2 = $OpnAmt2 + $drAmt2 - $crAmt2;
                                                        
                                                        $MainGroupSum_pre += $ClsBal2
                                                        
                                                        ?>
                                                        <?php 
                                                            // Trade Payables
                                                            if($val2["SubActGroupID"] == "1000006"){
                                                        ?>
                                                            <td style="text-align:right;"><a href="#" onclick="GetAnnexure('<?php echo $val2["SubActGroupID"];?>','<?php echo $val2["SubGroupName"];?>')"  class=" Open_Annexure mbot15">Trade Payable</a></td>
                                                        <?php
                                                            }else if($val2["SubActGroupID"] == "1000016"){
                                                                //Trade receivables
                                                        ?>
                                                                <td style="text-align:right;"><a href="#" onclick="GetAnnexure('<?php echo $val2["SubActGroupID"];?>','<?php echo $val2["SubGroupName"];?>')"  class=" Open_Annexure mbot15">Trade receivables</a></td>
                                                        <?php        
                                                            }else if($val2["SubActGroupID"] == "1000015"){
                                                                //Inventories
                                                        ?>
                                                                <td style="text-align:right;"><a href="#" onclick="GetAnnexure('<?php echo $val2["SubActGroupID"];?>','<?php echo $val2["SubGroupName"];?>')" class=" Open_Annexure mbot15">Inventories</a></td>
                                                        <?php        
                                                            }else if($val2["SubActGroupID"] == "1000010"){
                                                                //Fixed assets
                                                        ?>
                                                                <td style="text-align:right;"><a href="#" onclick="GetAnnexure('<?php echo $val2["SubActGroupID"];?>','<?php echo $val2["SubGroupName"];?>')" class=" Open_Annexure mbot15">Fixed assets</a></td>
                                                        <?php        
                                                            }else{
                                                        ?>
                                                                <td></td>
                                                        <?php
                                                            }
                                                        ?>
                                                        
                                                        <td style="text-align:right;  padding-right: 10px;">
                                                            <?php echo number_format($ClsBal, 2, '.', ''); ?>
                                                        </td>
                                                        <td style="text-align:right;  padding-right: 10px;">
                                                            <?php echo number_format($ClsBal2, 2, '.', ''); ?>
                                                        </td>
                                                    </tr>
                                                    
                                                    <?php
                                                        $totalAccount = 0;
                                                        $totalAccount11 = 0;
                                                foreach ($val2['Accounts'] as $key3 => $val3) {
                                                    $ClsBalAccountWise = 0;
                                                    ?>
                                                    <tr class="treegrid-<?php echo html_entity_decode($Counter3); ?> treegrid-parent-<?php echo $Counter2; ?> parent-node expanded" id="Accounts">
                                                        <td class="parent"><?php echo $val3["AccountName"]; ?></td>
                                                        <?php
                                                        $crActAmt = 0;
                                                        $drActAmt = 0;
                                                        $OpnActAmt = 0;

                                                        // Current FY Year
                                                        foreach ($ledger_data->Cur_yr_ledger as $Key44 => $val44) {
                                                            if ($val44["AccountID"] == $val3["AccountID"] && $val44["TType"] == "C" && $val44["FY"] == $fy) {
                                                                $crActAmt = $val44["SUMAmt"];
                                                            }
                                                            if ($val44["AccountID"] == $val3["AccountID"] && $val44["TType"] == "D" && $val44["FY"] == $fy) {
                                                                $drActAmt = $val44["SUMAmt"];
                                                            }
                                                        }
                                                        foreach ($OpnBal->Cur_yr_OpnBal as $Key44 => $val44) {
                                                            if ($val44["AccountID"] == $val3["AccountID"] && $val44["FY"] == $fy) {
                                                                $OpnActAmt = $val44["SUMAmt"];
                                                            }
                                                        }
                                                        if($i>1){
                                                            // Assets
                                                            $ClsBalAccountWise = $OpnActAmt + $drActAmt - $crActAmt;
                                                        }else{
                                                            // Liability
                                                            $ClsBalAccountWise = $OpnActAmt + $crActAmt - $drActAmt;
                                                        }
                                                        
                                                        
                                                        $totalmaingroup += $ClsBalAccountWise;
                                                        $totalAccount += $ClsBal;
                                                        
                                                        // Last FY Year
                                                        
                                                        ?>
                                                        <td></td>
                                                        <td style="text-align:right;  padding-right: 10px;">
                                                            <?php echo number_format($ClsBalAccountWise, 2, '.', ''); ?>
                                                        </td>
                                                        <td style="text-align:right;  padding-right: 10px;">
                                                            <?php echo number_format($ClsBalAccountWise, 2, '.', ''); ?>
                                                        </td>
                                                    </tr>
                                                    
                                                    <?php
                                                    $Counter3++;
                                                }
                                                ?>
                                                
                                                    <?php
                                                    $Counter2++;
                                                }
                                                ?>
                                                <tr style="border: 1px solid #000;">
                                                    <td colspan="2"><b>Total for<?php echo $val1["SubGroup1Name"]; ?></b></td>
                                                    <td style="text-align:right;  padding-right: 10px; font-size:13px;"><b>
                                                            <?php echo number_format($totalsubgroup, 2, '.', ''); ?>
                                                        </b></td>
                                                    <td style="text-align:right;  padding-right: 10px; font-size:13px;"><b>
                                                            <?php echo number_format($totalsubgroup11, 2, '.', ''); ?>
                                                        </b></td>
                                                </tr>
                                                <?php
                                                $SubCounter++;

                                            }

                                            ?>
                                            <tr style="border: 1px solid #000;">
                                                <td colspan="2"><b>Total for <?php echo $val['MainGroup']; ?></b></td>
                                                <td style="text-align:right;   padding-right: 10px; font-size:14px;"><b>
                                                        <?php echo number_format($totalmaingroup, 2, '.', ''); ?>
                                                    </b></td>
                                                <td style="text-align:right;  padding-right: 10px;font-size:14px;"><b>
                                                        <?php echo number_format($totalmaingroup22, 2, '.', ''); ?>
                                                    </b></td>
                                            </tr>
                                            <?php
                                            $MainCounter++;
                                            $i++;
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
    
    <div class="modal fade" id="TradePayable-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Trade Payable</h4>
                </div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table_annexure">
                                <table class="tree table table-bordered table_tradePayable_data" id="table_tradePayable_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Account Type</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        foreach($payable_data as $key=>$val){
                                    ?>
                                        <tr>
                                            <td><?php echo $val["Name"];?></td>
                                            <td><?php echo number_format($val["Balance"], 2, '.', '');?></td>
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
    
    <div class="modal fade" id="TradeReceivable-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Trade Receivable</h4>
                </div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table_annexure">
                                <table class="tree table table-bordered table_TradeReceivable_data" id="table_TradeReceivable_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Account Type</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        foreach($receivable_data as $key=>$val){
                                    ?>
                                        <tr>
                                            <td><?php echo $val["Name"];?></td>
                                            <td><?php echo number_format($val["Balance"], 2, '.', '');?></td>
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
    
    <div class="modal fade" id="Inventory-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Item Wise Inventory Details</h4>
                </div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Closing Inventory Details (Inventory Value : <?php echo number_format($CurrentInventoryValue, 2, '.', ',');?>)</h4>
                            <span style="color:red;font-size:10px;">Calculated closing inventory as per FIFO Based.</span>
                            <div class="table_annexure">
                                <table class="tree table table-bordered table_TradeReceivable_data" id="table_TradeReceivable_data" width="100%">
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
    
    <div class="modal fade" id="FixedAssets-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Fixed Assets Details</h4>
                </div>
                <?php 
                    $fy = $this->session->userdata('finacial_year');
                    $Nfy = $fy + 1;
                    $NNfy = $fy - 1;
                    $count = count($FixedAssets) + 1;
                ?>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table_annexure">
                                <table class="tree table table-bordered table_TradeReceivable_data" id="table_TradeReceivable_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                        <?php
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <th><?php echo $val["company"];?></th>
                                        <?php
                                            }    
                                        ?>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="color: #226faa;font-weight: 700;font-size: 13px;">A. YEAR ENDED MARCH 31 <?php echo '20'.$Nfy; ?></td>
                                            
                                            <td style="text-align:right;" colspan="<?php echo $count;?>"></td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: 700;font-size: 12px;">Gross Carrying Amoount</td>
                                            
                                            <td style="text-align:right;" colspan="<?php echo $count;?>"></td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: 700;font-size: 12px;">Opening Gross Carrying amount as at April 1 <?php echo '20'.$fy; ?></td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Add : Additions</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Add : Slump Purchase as per BTA</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Less : Deduction</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td style="font-weight: 700;font-size: 12px;">Closing Gross Carrying Amount</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td style="font-weight: 700;font-size: 12px;">Accumulated Depreciation and Impairment</td>
                                            
                                            <td style="text-align:right;" colspan="<?php echo $count;?>"></td>
                                        </tr>
                                        
                                        <tr>
                                            <td style="font-weight: 700;font-size: 12px;">Opening Accumulated Depreciation and Impairment as at April 1 <?php echo '20'.$fy; ?></td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Add : depreciation charges during this year</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Add : Impairment</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Less : Deduction</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td style="font-weight: 700;font-size: 12px;">Closing Depreciation and Impairment</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td style="font-weight: 700;font-size: 12px;">Net Carrying Amount</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        
                                         <!--Section B -->
                                        <tr>
                                            <td style="color: #226faa;font-weight: 700;font-size: 13px;">B. YEAR ENDED MARCH 31 <?php echo '20'.$fy; ?></td>
                                            
                                            <td style="text-align:right;" colspan="<?php echo $count;?>"></td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: 700;font-size: 12px;">Gross Carrying Amoount</td>
                                            
                                            <td style="text-align:right;" colspan="<?php echo $count;?>"></td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: 700;font-size: 12px;">Opening Gross Carrying amount as at April 1 <?php echo '20'.$NNfy; ?></td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Add : Additions</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Add : Slump Purchase as per BTA</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Less : Deduction</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td style="font-weight: 700;font-size: 12px;">Closing Gross Carrying Amount</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td style="font-weight: 700;font-size: 12px;">Accumulated Depreciation and Impairment</td>
                                            
                                            <td style="text-align:right;" colspan="<?php echo $count;?>"></td>
                                        </tr>
                                        
                                        <tr>
                                            <td style="font-weight: 700;font-size: 12px;">Opening Accumulated Depreciation and Impairment as at April 1 <?php echo '20'.$NNfy; ?></td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Add : depreciation charges during this year</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Add : Impairment</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Less : Deduction</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td style="font-weight: 700;font-size: 12px;">Closing Depreciation and Impairment</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td style="font-weight: 700;font-size: 12px;">Net Carrying Amount</td>
                                            <?php
                                                $TotalGrossAmt = 0;
                                            foreach($FixedAssets as $key=>$val){
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
                                        <?php
                                                //$TotalPurchAmt += $val["PurchAmt"];
                                            }    
                                        ?>
                                            <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
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
        
        function GetAnnexure(SubgroupID,SubgroupName){
            if(SubgroupID == "1000006"){
                $('#TradePayable-modal').modal('show');
            }else if(SubgroupID == "1000016"){
                $('#TradeReceivable-modal').modal('show');
            }else if(SubgroupID == "1000015"){
                $('#Inventory-modal').modal('show');
            }else if(SubgroupID == "1000010"){
                $('#FixedAssets-modal').modal('show');
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
$("#caexcel").click(function(){
    var maingroup = $("#maingroup").val();
    var subgroup = $("#subgroup").val();
    var subgroup1 = $("#subgroup1").val();
    $.ajax({
        url:"<?php echo admin_url(); ?>Misc_reports/export_balsheetreport",
        method:"POST",
        data: {maingroup:maingroup, subgroup:subgroup,subgroup1:subgroup1},
        beforeSend: function () {
            $('#searchh3').css('display','block');
        },
        complete: function () {
            $('#searchh3').css('display','none');
        },
        success:function(data){
            response = JSON.parse(data);
            window.location.href = response.site_url+response.filename;
        }
    });
});

</script>