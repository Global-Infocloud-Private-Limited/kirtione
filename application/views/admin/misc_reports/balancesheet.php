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
                    <!-- <a class="btn btn-default buttons-excel buttons-html2" tabindex="0"
                        aria-controls="table-trial_bal_report" href="#" id="caexcel"
                        style="float: left ! important;"><span>Export to Excel</span></a> -->
                    <a class="btn btn-default" href="javascript:void(0);" style="margin-bottom: 20px;margin-left: 10px;"
                        onclick="printPage();">Print</a>

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
                                            <th rowspan="2"></th>
                                            <th colspan="2" class="text-center th_total">
                                                <?php echo _l('total'); ?>
                                            </th>
                                        </tr>
                                        <tr class="tr_header">
                                            <th class="th_total" >
                                                <?php echo $CurrYrLastDate; ?>
                                            </th>
                                            <th class="th_total">
                                                <?php echo $LastYrLastDate; ?>
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $MainCounter = 1000;
                                        $SubCounter = 2000;
                                        $Counter2 = 3000;
                                        foreach ($nestedData as $key => $val) {
                                            $totalmaingroup = 0;
                                            $totalmaingroup22 = 0;
                                            ?>
                                            <tr class="treegrid-<?php echo $MainCounter; ?> parent-node expanded"
                                                style="font-size:14px;"id="maingroup">
                                                <td class="parent">
                                                    <?php echo $val['MainGroup']; ?>
                                                </td>
                                                <td></td>
                                            </tr>

                                            <?php

                                            foreach ($val['SubGroups'] as $key1 => $val1) {
                                                ?>
                                                <tr class="treegrid-<?php echo html_entity_decode($SubCounter); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node expanded "
                                                    style="font-size:13px;" id="subgroup">
                                                    <td class="parent">
                                                        <?php echo $val1["SubGroup"]; ?>
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <?php
                                                $totalsubgroup = 0;
                                                $totalsubgroup11 = 0;
                                                foreach ($val1['SubGroup1'] as $key2 => $val2) {
                                                    ?>
                                                    <tr
                                                        class="treegrid-<?php echo html_entity_decode($Counter2); ?> treegrid-parent-<?php echo $SubCounter; ?> parent-node expanded" id="subgroup1">
                                                        <td class="parent">
                                                            <?php echo $val2["SubActGroupName"]; ?>
                                                        </td>
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
                                                        $ClsBal = $OpnAmt + $crAmt - $drAmt;
                                                        $totalmaingroup += $ClsBal;
                                                        $totalsubgroup += $ClsBal;
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

                                                        $ClsBal2 = $OpnAmt2 + $crAmt2 - $drAmt2;
                                                        $totalmaingroup22 += $ClsBal2;
                                                        $totalsubgroup11 += $ClsBal2;
                                                        ?>
                                                        <td style="text-align:right;  padding-right: 10px;">
                                                            <?php echo number_format($ClsBal, 2, '.', ''); ?>
                                                        </td>
                                                        <td style="text-align:right;  padding-right: 10px;">
                                                            <?php echo number_format($ClsBal2, 2, '.', ''); ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    $Counter2++;
                                                }
                                                ?>
                                                <tr style="border: 1px solid #000;">
                                                    <td><b>Total for
                                                            <?php echo $val1["SubGroup"]; ?>
                                                        </b></td>
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
                                                <td><b>Total for
                                                        <?php echo $val['MainGroup']; ?>
                                                    </b></td>
                                                <td style="text-align:right;   padding-right: 10px; font-size:14px;"><b>
                                                        <?php echo number_format($totalmaingroup, 2, '.', ''); ?>
                                                    </b></td>
                                                <td style="text-align:right;  padding-right: 10px;font-size:14px;"><b>
                                                        <?php echo number_format($totalmaingroup22, 2, '.', ''); ?>
                                                    </b></td>
                                            </tr>
                                            <?php
                                            $MainCounter++;
                                        }
                                        ?>



                                        <!--<tr class="treegrid-<?php echo html_entity_decode($parent_index); ?> treegrid-parent-1000 parent-node expanded">
        <td class="parent"><?php echo _l('acc_current_assets'); ?></td>
        <td></td>
        <td></td>
      </tr>-->

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