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
      <div class="row">
        <div class="col-md-12 text-centerr">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
              <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
              <li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
              <li class="breadcrumb-item active" aria-current="page"><b>Trial Balance Summary</b></li>
            </ol>
          </nav>
          <hr class="hr_style" style="margin-Bottom:12px !important;">
        </div>
      </div>
      <div class="row ">
        <div class="col-md-5">
          <?php if (has_permission_new('trial_balance_summary', '', 'print')) {
          ?>
            <a class="btn btn-default" href="javascript:void(0);" style="margin-bottom: 20px;margin-left: 10px;" onclick="printPage();">Print</a>
            <button type="button" class="btn btn-success" id="exportTrialBalanceExcel" onclick="exportTrialBalanceToExcel()" style="margin-bottom: 20px;margin-left: 10px;">
                <i class="fa fa-file-excel-o"></i>
                Export Excel
            </button>
          <?php } ?>
        </div>
        <div class="col-md-10">
          <?php
          $fy = $this->session->userdata('finacial_year');
          $lastFy = $fy - 1;
          $fy_ = $fy + 1;
          $CurrYrFirstDate = '01/04/20' . $fy;
          $CurrYrLastDate = date('d/m/Y');
          $LastYrFirstDate = '01/04/20' . $lastFy;
          $LastYrLastDate = '31/03/20' . $fy;
          ?>
          <div id="tbLoadingBox" style="padding:15px 0;">
              <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                  <strong>Loading Trial Balance Summary...</strong>
                  <span id="tbProgressText">0%</span>
              </div>
              <div style="
                  width:100%;
                  height:8px;
                  background:#e5e5e5;
                  border-radius:4px;
                  overflow:hidden;
              ">
                  <div id="tbProgressBar" style="
                      width:0%;
                      height:100%;
                      background:#337ab7;
                      transition:width 0.1s linear;
                  "></div>
              </div>
              <div id="tbProgressDetail" style="margin-top:6px; font-size:12px;">
                  Preparing data...
              </div>
          </div>

          <div class="page" id="DivIdToPrint">
            <div id="accordion">
              <div class="card">
                <table class="tree">
                  <thead>
                    <tr class="tr_header">
                      <th>Particular</th>
                      <th colspan="6" class="text-center th_total">Transaction Details</th>
                    </tr>
                    <tr class="tr_header">
                      <th class="th_total"></th>
                      <th class="th_total">Opening Bal DR</th>
                      <th class="th_total">Opening Bal CR</th>
                      <th class="th_total">DebitAmt</th>
                      <th class="th_total">CreditAmt</th>
                      <th class="th_total">Closing Bal DR</th>
                      <th class="th_total">Closing Bal CR</th>
                    </tr>
                  </thead>
                  <tbody id="trialBalanceBody"></tbody>
                  <tfoot>
                    <tr class="tr_header">
                      <th class="th_total">All Total</th>
                      <th class="th_total" id="tfoot_opening_bal_dr">-</th>
                      <th class="th_total" id="tfoot_opening_bal_cr">-</th>
                      <th class="th_total" id="tfoot_debitamt">-</th>
                      <th class="th_total" id="tfoot_creditamt">-</th>
                      <th class="th_total" id="tfoot_closing_bal_dr">-</th>
                      <th class="th_total" id="tfoot_closing_bal_cr">-</th>
                    </tr>
                  </tfoot>
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

              <h4>Closing Inventory Details (Inventory Value : <?php echo number_format($CurrentInventoryValue, 2, '.', ','); ?>)</h4><!--

                            <span style="color:red;font-size:10px;">Calculated closing inventory as per FIFO Based.</span>-->

              <div class="table_annexure">

                <table class="tree table table-bordered table_TradeReceivable_data" id="table_TradeReceivable_data" width="100%">

                  <thead>

                    <tr>

                      <th>Particular</th>

                      <?php

                      foreach ($CurrentInventoryItemWiseValue as $key => $val) {

                      ?>

                        <th><?php echo $val["name"]; ?></th>

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

                      foreach ($CurrentInventoryItemWiseValue as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php echo number_format($val["GOpnQtyValue"], 2, '.', ','); ?></td>

                      <?php

                        $TotalOpnAmt += $val["GOpnQtyValue"];
                      }

                      ?>

                      <td style="text-align:right;"><?php echo number_format($TotalOpnAmt, 2, '.', ','); ?></td>

                    </tr>

                    <tr>

                      <td>Purchase Amt</td>

                      <?php

                      $TotalPurchAmt = 0;

                      foreach ($CurrentInventoryItemWiseValue as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php echo number_format($val["GPurchValue"], 2, '.', ','); ?></td>

                      <?php

                        $TotalPurchAmt += $val["GPurchValue"];
                      }

                      ?>

                      <td style="text-align:right;"><?php echo number_format($TotalPurchAmt, 2, '.', ','); ?></td>

                    </tr>

                    <tr>

                      <td>Sale Amt</td>

                      <?php

                      $TotalSaleAmt = 0;

                      foreach ($CurrentInventoryItemWiseValue as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php echo number_format($val["GSaleValue"], 2, '.', ','); ?></td>

                      <?php

                        $TotalSaleAmt += $val["GSaleValue"];
                      }

                      ?>

                      <td style="text-align:right;"><?php echo number_format($TotalSaleAmt, 2, '.', ','); ?></td>

                    </tr>

                    <tr>

                      <td>Production Amt</td>

                      <?php

                      $TotalPrdAmt = 0;

                      foreach ($CurrentInventoryItemWiseValue as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php echo number_format($val["GPrdValue"], 2, '.', ','); ?></td>

                      <?php

                        $TotalPrdAmt += $val["GPrdValue"];
                      }

                      ?>

                      <td style="text-align:right;"><?php echo number_format($TotalPrdAmt, 2, '.', ','); ?></td>

                    </tr>

                    <tr>

                      <td>Issue Amt</td>

                      <?php

                      $TotalIssueAmt = 0;

                      foreach ($CurrentInventoryItemWiseValue as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php echo number_format($val["GIssueValue"], 2, '.', ','); ?></td>

                      <?php

                        $TotalIssueAmt += $val["GIssueValue"];
                      }

                      ?>

                      <td style="text-align:right;"><?php echo number_format($TotalIssueAmt, 2, '.', ','); ?></td>

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

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <th><?php echo $val["company"]; ?></th>

                      <?php

                      }

                      ?>

                      <th>Total</th>

                    </tr>

                  </thead>

                  <tbody>

                    <tr>

                      <td style="color: #226faa;font-weight: 700;font-size: 13px;">A. YEAR ENDED MARCH 31 <?php echo '20' . $Nfy; ?></td>



                      <td style="text-align:right;" colspan="<?php echo $count; ?>"></td>

                    </tr>

                    <tr>

                      <td style="font-weight: 700;font-size: 12px;">Gross Carrying Amoount</td>



                      <td style="text-align:right;" colspan="<?php echo $count; ?>"></td>

                    </tr>

                    <tr>

                      <td style="font-weight: 700;font-size: 12px;">Opening Gross Carrying amount as at April 1 <?php echo '20' . $fy; ?></td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td>Add : Additions</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td>Add : Slump Purchase as per BTA</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td>Less : Deduction</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td style="font-weight: 700;font-size: 12px;">Closing Gross Carrying Amount</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td style="font-weight: 700;font-size: 12px;">Accumulated Depreciation and Impairment</td>



                      <td style="text-align:right;" colspan="<?php echo $count; ?>"></td>

                    </tr>



                    <tr>

                      <td style="font-weight: 700;font-size: 12px;">Opening Accumulated Depreciation and Impairment as at April 1 <?php echo '20' . $fy; ?></td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td>Add : depreciation charges during this year</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td>Add : Impairment</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td>Less : Deduction</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td style="font-weight: 700;font-size: 12px;">Closing Depreciation and Impairment</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td style="font-weight: 700;font-size: 12px;">Net Carrying Amount</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>





                    <!--Section B -->

                    <tr>

                      <td style="color: #226faa;font-weight: 700;font-size: 13px;">B. YEAR ENDED MARCH 31 <?php echo '20' . $fy; ?></td>



                      <td style="text-align:right;" colspan="<?php echo $count; ?>"></td>

                    </tr>

                    <tr>

                      <td style="font-weight: 700;font-size: 12px;">Gross Carrying Amoount</td>



                      <td style="text-align:right;" colspan="<?php echo $count; ?>"></td>

                    </tr>

                    <tr>

                      <td style="font-weight: 700;font-size: 12px;">Opening Gross Carrying amount as at April 1 <?php echo '20' . $NNfy; ?></td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td>Add : Additions</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td>Add : Slump Purchase as per BTA</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td>Less : Deduction</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td style="font-weight: 700;font-size: 12px;">Closing Gross Carrying Amount</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td style="font-weight: 700;font-size: 12px;">Accumulated Depreciation and Impairment</td>



                      <td style="text-align:right;" colspan="<?php echo $count; ?>"></td>

                    </tr>



                    <tr>

                      <td style="font-weight: 700;font-size: 12px;">Opening Accumulated Depreciation and Impairment as at April 1 <?php echo '20' . $NNfy; ?></td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td>Add : depreciation charges during this year</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td>Add : Impairment</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td>Less : Deduction</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td style="font-weight: 700;font-size: 12px;">Closing Depreciation and Impairment</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

                    </tr>



                    <tr>

                      <td style="font-weight: 700;font-size: 12px;">Net Carrying Amount</td>

                      <?php

                      $TotalGrossAmt = 0;

                      foreach ($FixedAssets as $key => $val) {

                      ?>

                        <td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');
                                                      ?></td>

                      <?php

                        //$TotalPurchAmt += $val["PurchAmt"];

                      }

                      ?>

                      <td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');
                                                    ?></td>

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
    table {
      border-collapse: collapse;
      width: 100%;
    }

    th,
    td {
      padding: 1px 5px !important;
      white-space: nowrap;
      border: 1px solid !important;
      font-size: 11px;
      line-height: 1.42857143 !important;
      vertical-align: middle !important;
    }

    th {
      background: #50607b;
      color: #fff !important;
    }

    .table_annexure {

      overflow: auto;

      max-height: 60vh;

      width: 98%;

      position: relative;

      top: 0px;

    }
  </style>

  <style>
      /* Main Group: entire row largest and bold */
      #trialBalanceBody tr.tb-main-group td {
          font-size: 15px !important;
          font-weight: 700 !important;
      }

      /* SubGroup 1: entire row smaller and bold */
      #trialBalanceBody tr.tb-subgroup-1 td {
          font-size: 13px !important;
          font-weight: 700 !important;
      }

      /* SubGroup 2: entire row smaller and bold */
      #trialBalanceBody tr.tb-subgroup-2 td {
          font-size: 12px !important;
          font-weight: 600 !important;
      }

      /* Account: entire row normal */
      #trialBalanceBody tr.tb-account td {
          font-size: 11px !important;
          font-weight: 400 !important;
      }


      /* Only indentation changes for first column */

      #trialBalanceBody tr.tb-main-group td:first-child {
          padding-left: 8px !important;
      }

      #trialBalanceBody tr.tb-subgroup-1 td:first-child {
          padding-left: 25px !important;
      }

      #trialBalanceBody tr.tb-subgroup-2 td:first-child {
          padding-left: 45px !important;
      }

      #trialBalanceBody tr.tb-account td:first-child {
          padding-left: 65px !important;
      }


      /* Amount columns alignment */

      #trialBalanceBody td.text-right {
          text-align: right !important;
          padding-right: 10px !important;
      }
  </style>

  <script>
    const trialBalanceData = <?php
        echo json_encode(
            $nestedData,
            JSON_HEX_TAG |
            JSON_HEX_AMP |
            JSON_HEX_APOS |
            JSON_HEX_QUOT
        );
    ?>;
  </script>

  <script>
    (function () {

        const tbody = document.getElementById('trialBalanceBody');

        const progressBox = document.getElementById('tbLoadingBox');
        const progressBar = document.getElementById('tbProgressBar');
        const progressText = document.getElementById('tbProgressText');
        const progressDetail = document.getElementById('tbProgressDetail');

        const rows = [];


        function escapeHtml(value) {

            if (value === null || value === undefined) {
                return '';
            }

            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }


        function money(value) {

            let number = parseFloat(value);

            if (!isFinite(number)) {
                number = 0;
            }

            return number.toFixed(2);
        }


        function makeCells(data) {

            return `
                <td>${escapeHtml(data.name)}</td>

                <td class="text-right">
                    ${money(data.DROpeningAmt)}
                </td>

                <td class="text-right">
                    ${money(data.CROpeningAmt)}
                </td>

                <td class="text-right">
                    ${money(data.DRAmt)}
                </td>

                <td class="text-right">
                    ${money(data.CRAmt)}
                </td>

                <td class="text-right">
                    ${money(data.DRClsAmt)}
                </td>

                <td class="text-right">
                    ${money(data.CRClsAmt)}
                </td>
            `;
        }


        function prepareRows() {

            trialBalanceData.forEach(function (mainGroup) {

                /*
                * LEVEL 1
                * Main Account Group
                */
                rows.push({

                    className: 'tb-main-group',

                    html: makeCells({

                        name: mainGroup.MainGroup,

                        DROpeningAmt:
                            mainGroup.DROpeningAmt,

                        CROpeningAmt:
                            mainGroup.CROpeningAmt,

                        DRAmt:
                            mainGroup.DRAmt,

                        CRAmt:
                            mainGroup.CRAmt,

                        DRClsAmt:
                            mainGroup.DRClsAmt,

                        CRClsAmt:
                            mainGroup.CRClsAmt
                    })
                });


                /*
                * LEVEL 2
                * SubGroup 1
                */
                (mainGroup.SubGroups1 || [])
                    .forEach(function (group1) {

                        rows.push({

                            className: 'tb-subgroup-1',

                            html: makeCells({

                                name:
                                    group1.SubGroup1Name,

                                DROpeningAmt:
                                    group1.DROpeningAmt,

                                CROpeningAmt:
                                    group1.CROpeningAmt,

                                DRAmt:
                                    group1.DRAmt,

                                CRAmt:
                                    group1.CRAmt,

                                DRClsAmt:
                                    group1.DRClsAmt,

                                CRClsAmt:
                                    group1.CRClsAmt
                            })
                        });


                        /*
                        * LEVEL 3
                        * SubGroup 2
                        */
                        (group1.SubGroups || [])
                            .forEach(function (group2) {

                                rows.push({

                                    className:
                                        'tb-subgroup-2',

                                    html: makeCells({

                                        name:
                                            group2.SubGroupName,

                                        DROpeningAmt:
                                            group2.DROpeningAmt,

                                        CROpeningAmt:
                                            group2.CROpeningAmt,

                                        DRAmt:
                                            group2.DRAmt,

                                        CRAmt:
                                            group2.CRAmt,

                                        DRClsAmt:
                                            group2.DRClsAmt,

                                        CRClsAmt:
                                            group2.CRClsAmt
                                    })
                                });


                                /*
                                * LEVEL 4
                                * Accounts
                                */
                                (group2.Accounts || [])
                                    .forEach(function (account) {

                                        rows.push({

                                            className:
                                                'tb-account',

                                            html: makeCells({

                                                name:
                                                    account.AccountName,

                                                DROpeningAmt:
                                                    account.DROpeningAmt,

                                                CROpeningAmt:
                                                    account.CROpeningAmt,

                                                DRAmt:
                                                    account.DRAmt,

                                                CRAmt:
                                                    account.CRAmt,

                                                DRClsAmt:
                                                    account.DRClsAmt,

                                                CRClsAmt:
                                                    account.CRClsAmt
                                            })
                                        });

                                    });

                            });

                    });

            });

        }


        function calculateAndDisplayTotals() {

            let totalDROpening = 0;
            let totalCROpening = 0;
            let totalDRTransaction = 0;
            let totalCRTransaction = 0;
            let totalDRClosing = 0;
            let totalCRClosing = 0;


            /*
            * Traverse nested data and sum all account values
            */
            trialBalanceData.forEach(function (mainGroup) {

                (mainGroup.SubGroups1 || []).forEach(function (group1) {

                    (group1.SubGroups || []).forEach(function (group2) {

                        (group2.Accounts || []).forEach(function (account) {

                            totalDROpening += parseFloat(account.DROpeningAmt) || 0;
                            totalCROpening += parseFloat(account.CROpeningAmt) || 0;
                            totalDRTransaction += parseFloat(account.DRAmt) || 0;
                            totalCRTransaction += parseFloat(account.CRAmt) || 0;
                            totalDRClosing += parseFloat(account.DRClsAmt) || 0;
                            totalCRClosing += parseFloat(account.CRClsAmt) || 0;

                        });

                    });

                });

            });


            /*
            * Update footer cells with calculated totals
            */
            document.getElementById('tfoot_opening_bal_dr').textContent = 
                totalDROpening.toFixed(2);

            document.getElementById('tfoot_opening_bal_cr').textContent = 
                totalCROpening.toFixed(2);

            document.getElementById('tfoot_debitamt').textContent = 
                totalDRTransaction.toFixed(2);

            document.getElementById('tfoot_creditamt').textContent = 
                totalCRTransaction.toFixed(2);

            document.getElementById('tfoot_closing_bal_dr').textContent = 
                totalDRClosing.toFixed(2);

            document.getElementById('tfoot_closing_bal_cr').textContent = 
                totalCRClosing.toFixed(2);

        }


        function appendChunk(startIndex) {

            /*
            * Recommended:
            * 100 to 200 rows per chunk.
            */
            const CHUNK_SIZE = 150;

            const endIndex = Math.min(
                startIndex + CHUNK_SIZE,
                rows.length
            );

            const fragment =
                document.createDocumentFragment();


            for (
                let index = startIndex;
                index < endIndex;
                index++
            ) {

                const rowData = rows[index];

                const tr =
                    document.createElement('tr');

                tr.className =
                    rowData.className;

                tr.innerHTML =
                    rowData.html;

                fragment.appendChild(tr);
            }


            tbody.appendChild(fragment);


            const percent =
                rows.length === 0
                    ? 100
                    : Math.round(
                        (endIndex / rows.length) * 100
                    );


            progressBar.style.width =
                percent + '%';

            progressText.textContent =
                percent + '%';

            progressDetail.textContent =
                endIndex +
                ' of ' +
                rows.length +
                ' rows loaded';


            if (endIndex < rows.length) {

                /*
                * Browser gets time to:
                *
                * 1. Paint rows
                * 2. Update progress bar
                * 3. Handle mouse events
                * 4. Avoid unresponsive popup
                */
                setTimeout(function () {

                    appendChunk(endIndex);

                }, 10);

            } else {

                progressBar.style.width = '100%';

                progressText.textContent = '100%';

                progressDetail.textContent =
                    rows.length +
                    ' rows loaded successfully';


                setTimeout(function () {

                    progressBox.style.display =
                        'none';

                    /*
                    * Calculate and display final totals
                    */
                    calculateAndDisplayTotals();

                }, 700);

            }

        }


        /*
        * Convert nested data into a flat rendering queue.
        */
        prepareRows();


        /*
        * Allow the initial HTML page to display first.
        */
        setTimeout(function () {

            appendChunk(0);

        }, 100);

    })();
  </script>

  <script>
    function exportTrialBalanceToExcel()
    {
        if (
            typeof trialBalanceData === 'undefined' ||
            !Array.isArray(trialBalanceData) ||
            trialBalanceData.length === 0
        ) {
            alert('No Trial Balance data available for export.');
            return;
        }

        const exportButton =
            document.getElementById('exportTrialBalanceExcel');

        if (exportButton) {
            exportButton.disabled = true;
            exportButton.innerHTML =
                '<i class="fa fa-spinner fa-spin"></i> Exporting...';
        }


        /*
        * Excel rows
        */
        const excelRows = [];


        /*
        * Header row
        */
        excelRows.push([
            'Account',
            'Opening Debit',
            'Opening Credit',
            'Transaction Debit',
            'Transaction Credit',
            'Closing Debit',
            'Closing Credit'
        ]);


        /*
        * Convert nested Trial Balance data
        * directly into Excel rows.
        */
        trialBalanceData.forEach(function (mainGroup) {

            // MAIN GROUP
            excelRows.push([
                mainGroup.MainGroup || '',
                toExcelNumber(mainGroup.DROpeningAmt),
                toExcelNumber(mainGroup.CROpeningAmt),
                toExcelNumber(mainGroup.DRAmt),
                toExcelNumber(mainGroup.CRAmt),
                toExcelNumber(mainGroup.DRClsAmt),
                toExcelNumber(mainGroup.CRClsAmt)
            ]);


            (mainGroup.SubGroups1 || []).forEach(function (group1) {

                // SUB GROUP 1
                excelRows.push([
                    '    ' + (group1.SubGroup1Name || ''),
                    toExcelNumber(group1.DROpeningAmt),
                    toExcelNumber(group1.CROpeningAmt),
                    toExcelNumber(group1.DRAmt),
                    toExcelNumber(group1.CRAmt),
                    toExcelNumber(group1.DRClsAmt),
                    toExcelNumber(group1.CRClsAmt)
                ]);


                (group1.SubGroups || []).forEach(function (group2) {

                    // SUB GROUP 2
                    excelRows.push([
                        '        ' + (group2.SubGroupName || ''),
                        toExcelNumber(group2.DROpeningAmt),
                        toExcelNumber(group2.CROpeningAmt),
                        toExcelNumber(group2.DRAmt),
                        toExcelNumber(group2.CRAmt),
                        toExcelNumber(group2.DRClsAmt),
                        toExcelNumber(group2.CRClsAmt)
                    ]);


                    (group2.Accounts || []).forEach(function (account) {

                        // ACCOUNT
                        excelRows.push([
                            '            ' + (account.AccountName || ''),
                            toExcelNumber(account.DROpeningAmt),
                            toExcelNumber(account.CROpeningAmt),
                            toExcelNumber(account.DRAmt),
                            toExcelNumber(account.CRAmt),
                            toExcelNumber(account.DRClsAmt),
                            toExcelNumber(account.CRClsAmt)
                        ]);

                    });

                });

            });

        });


        /*
        * Create worksheet
        */
        const worksheet =
            XLSX.utils.aoa_to_sheet(excelRows);


        /*
        * Set column widths
        */
        worksheet['!cols'] = [
            { wch: 55 },
            { wch: 18 },
            { wch: 18 },
            { wch: 20 },
            { wch: 20 },
            { wch: 18 },
            { wch: 18 }
        ];


        /*
        * Auto filter on header
        */
        worksheet['!autofilter'] = {
            ref: 'A1:G1'
        };


        /*
        * Freeze header row
        */
        worksheet['!freeze'] = {
            xSplit: 0,
            ySplit: 1
        };


        /*
        * Number formatting
        */
        const range =
            XLSX.utils.decode_range(worksheet['!ref']);


        for (
            let row = 1;
            row <= range.e.r;
            row++
        ) {

            for (
                let col = 1;
                col <= 6;
                col++
            ) {

                const cellAddress =
                    XLSX.utils.encode_cell({
                        r: row,
                        c: col
                    });


                if (worksheet[cellAddress]) {

                    worksheet[cellAddress].z =
                        '#,##0.00';
                }
            }
        }


        /*
        * Create workbook
        */
        const workbook =
            XLSX.utils.book_new();


        XLSX.utils.book_append_sheet(
            workbook,
            worksheet,
            'Trial Balance Summary'
        );


        /*
        * File name
        */
        const now = new Date();

        const date =
            now.getFullYear() +
            '-' +
            String(now.getMonth() + 1).padStart(2, '0') +
            '-' +
            String(now.getDate()).padStart(2, '0');


        const fileName =
            'Trial_Balance_Summary_' +
            date +
            '.xlsx';


        /*
        * Download Excel
        */
        XLSX.writeFile(
            workbook,
            fileName
        );


        /*
        * Restore button
        */
        if (exportButton) {

            exportButton.disabled = false;

            exportButton.innerHTML =
                '<i class="fa fa-file-excel-o"></i> Export Excel';
        }
    }


    function toExcelNumber(value)
    {
        const number = parseFloat(value);

        return Number.isFinite(number)
            ? number
            : 0;
    }
  </script>

  <script>
    function GetAnnexure(SubgroupID, SubgroupName) {

      if (SubgroupID == "1000015") {

        $('#Inventory-modal').modal('show');

      } else if (SubgroupID == "1000010") {

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

      heading_data += '<td style="text-align:center;"colspan="3">Trial Balance Summary</td>';

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
    $("#caexcel").click(function() {

      var maingroup = $("#maingroup").val();

      var subgroup = $("#subgroup").val();

      var subgroup1 = $("#subgroup1").val();

      $.ajax({

        url: "<?php echo admin_url(); ?>Misc_reports/export_balsheetreport",

        method: "POST",

        data: {
          maingroup: maingroup,
          subgroup: subgroup,
          subgroup1: subgroup1
        },

        beforeSend: function() {

          $('#searchh3').css('display', 'block');

        },

        complete: function() {

          $('#searchh3').css('display', 'none');

        },

        success: function(data) {

          response = JSON.parse(data);

          window.location.href = response.site_url + response.filename;

        }

      });

    });
  </script>