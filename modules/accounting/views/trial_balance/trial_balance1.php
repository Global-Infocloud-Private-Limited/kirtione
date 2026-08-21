<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    .table-trial_bal_report {
        overflow: auto;
        max-height: 55vh;
        width: 100%;
        position: relative;
        top: 0px;
    }

    .table-trial_bal_report thead th {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .table-trial_bal_report tbody th {
        position: sticky;
        left: 0;
    }


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
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            
            <div class="col-md-11">

                <div class="panel_s">
                    <div class="panel-body">

                       <div class="row">
                        <div class="col-md-12">
                            <div class="searchh2" style="display:none;">Please wait while fetching data.</div>
                        </div>
                        <?php echo form_open('admin/accounting/trialbalance1',array('id'=>'filter_item_form')); ?>
                            <div class="col-md-2">
                                <?php
                                $fy = $this->session->userdata('finacial_year');
                                $fy_new = $fy + 1;
                                $lastdate_date = '20' . $fy_new . '-03-31';
                                $firstdate_date = '20' . $fy_new . '-04-01';
                                $curr_date = date('Y-m-d');
                                $curr_date_new = new DateTime($curr_date);
                                $last_date_yr = new DateTime($lastdate_date);
                                if ($last_date_yr < $curr_date_new) {
                                    $to_date = '31/03/20' . $fy_new;
                                    $from_date = '01/03/20' . $fy_new;
                                } else {
                                    $from_date = "01/" . date('m') . "/" . date('Y');
                                    $to_date = date('d/m/Y');
                                }
                                ?>
                                <?php echo render_date_input('as_on', 'As On', $to_date); ?>
                            </div>

                            <?php
                                if($filter_data){
                                    $SelectedmainGroup = $filter_data["MainGroup"];
                                    // convert to array
                                    //$mainGroup_array = explode(',', $mainGroup);
                                }
                            ?>
                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="MainGroup">
                                    <small class="req text-danger">* </small>
                                    <label for="MainGroup" class="control-label">ActMainGroup Name</label>
                                    <select name="MainGroup[]" id="MainGroup"  value ="MainGroup" class="selectpicker form-control" multiple
                                        data-none-selected-text="Non Selected" data-live-search="true">
                                        <option></option>
                                        <?php
                                        foreach ($MainGroup as $key => $value) {
                                            $selected = '';
                                            if (in_array($value['ActGroupID'], $SelectedmainGroup)){
                                                $selected = "selected";
                                            }
                                        ?>
                                            <option value="<?php echo $value['ActGroupID']; ?>" <?php echo $selected;?>><?php echo $value['ActGroupName']; ?></option>
                                        <?php }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <?php
                                if($filter_data){
                                    $SelectedSubGroup1 = $filter_data["MainSubGroup"];
                                }
                            ?>
                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="MainSubGroup">
                                    <small class="req text-danger">* </small>
                                    <label for="MainSubGroup" class="control-label">ActGroup Name</label>
                                    <select name="MainSubGroup[]" id="MainSubGroup" value="MainSubGroup" class="selectpicker form-control" multiple
                                        data-none-selected-text="Non Selected" data-live-search="true">
                                        <option></option>
                                        <?php 
                                            if($SubGroup1){
                                                foreach($SubGroup1 as $SG1Key=>$SG1Val){
                                                    $selected = '';
                                                    if (in_array($SG1Val['SubActGroupID1'], $SelectedSubGroup1)){
                                                        $selected = "selected";
                                                    }
                                                ?>
                                                    <option value="<?php echo $SG1Val['SubActGroupID1']; ?>" <?php echo $selected;?>><?php echo $SG1Val['SubActGroupName']; ?></option>
                                                <?php
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <?php
                                if($filter_data){
                                    $SelectedSubGroup = $filter_data["AccountSubGroupID2"];
                                }
                            ?>
                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="AccountSubGroupID2">
                                    <small class="req text-danger">* </small>
                                    <label for="AccountSubGroupID2" class="control-label">ActSubGroup Name</label>
                                    <select name="AccountSubGroupID2[]" id="AccountSubGroupID2" value ="AccountSubGroupID2" multiple
                                        class="selectpicker form-control" data-none-selected-text="Non Selected"
                                        data-live-search="true">
                                        <option></option>
                                        <?php 
                                            if($SubGroup){
                                                foreach($SubGroup as $SGKey=>$SGVal){
                                                    $selected = '';
                                                    if (in_array($SGVal['SubActGroupID'], $SelectedSubGroup)){
                                                        $selected = "selected";
                                                    }
                                                ?>
                                                    <option value="<?php echo $SGVal['SubActGroupID']; ?>" <?php echo $selected;?>><?php echo $SGVal['SubActGroupName']; ?></option>
                                                <?php
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>


								<button  type ="submit"class="btn btn-info pull-left mleft5 search_data" style="margin-top: 19px;"
									id="search_data">Show</button>
						<?php echo form_close(); ?>
                      </div>
                        <!--</div>-->


                        <div class="clearfix mtop20"></div>
                        <div class="row">
                            <div class="col-md-6">
                                <a class="btn btn-default buttons-excel buttons-html5" tabindex="0"
                                    aria-controls="table-trial_bal_report" href="#" id="caexcel"><span>Export to
                                        excel</span></a>
                                <a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>

                            </div>
                            <span id="searchh3" style="display:none;">Please wait exporting data...</span>
                            <!-- <div class="col-md-6">
                                <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search .."
                                    title="Type in a name" style="float: right;">

                            </div> -->
                            <!-- <div class="col-md-12">
                                
                               
                                 <span id="searchh2" style="display:none;">Loading.....</span> 
                            </div> -->
                            <div class="col-md-12">
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
                                                    <tr class="tr_header" style="padding-right: 10px; font-size:14px;">
                                                        <th class="th_total">Account Name</th>
                                                        <th class="th_total">Opening Bal</th>
                                                        <th class="th_total">DebitAmt</th>
                                                        <th class="th_total">CreditAmt</th>
                                                        <th class="th_total">BalanceAmt</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php
                                                    $TotalOpnBal = 0;
                                                    $TotalDR = 0;
                                                    $TotalCR = 0;
                                                    $MainCounter = 1000;
                                                    $SubCounter = 2000;
                                                    $Counter2 = 3000;
                                                    $SubCounter2 = 4000;
                                                    foreach ($nestedData as $key => $val) {

                                                        ?>
                                                        <tr class="treegrid-<?php echo $MainCounter; ?> parent-node expanded"
                                                            style="font-size:14px;" id="maingroup">
                                                            <td class="parent">
                                                                <?php echo $val['MainGroup']; ?>
                                                            </td>
                                                            <td style="text-align:right"><b><?php echo number_format($val['OpnBal'][0], 2, '.', ''); ?></b></td>
                                                            <td style="text-align:right"><b><?php echo number_format($val['DR'][0], 2, '.', ''); ?></b></td>
                                                            <td style="text-align:right"><b><?php echo number_format($val['CR'][0], 2, '.', ''); ?></b></td>
                                                            <?php 
                                                                $BalAmt = $val['OpnBal'][0] + $val['DR'][0] - $val['CR'][0];
                                                                $TotalOpnBal += $val['OpnBal'][0];
                                                                $TotalDR += $val['DR'][0];
                                                                $TotalCR += $val['CR'][0];
                                                                if($BalAmt <= 0){
                                                                    $Pre = "CR";
                                                                }else{
                                                                    $Pre = "DR";
                                                                }
                                                            ?>
                                                            <td style="text-align:right;"><b><?php echo number_format(abs($BalAmt), 2, '.', '')." ".$Pre;?></b></td>
                                                        </tr>
                                                        <?php

                                                        foreach ($val['SubGroups1'] as $key2 => $val2) {
                                                            ?>
                                                            <tr class="treegrid-<?php echo html_entity_decode($SubCounter); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node  "
                                                                style="font-size:13px;" id="subgroup">
                                                                <td class="parent">
                                                                    <?php echo $val2["SubGroup1Name"]; ?>
                                                                </td>
                                                                <td style="text-align:right"><b><?php echo number_format($val2['OpnBal1'][0], 2, '.', ''); ?></b></td>
                                                                <td style="text-align:right"><b><?php echo number_format($val2['DR1'][0], 2, '.', ''); ?></b></td>
                                                                <td style="text-align:right"><b><?php echo number_format($val2['CR1'][0], 2, '.', ''); ?></b></td>
                                                                <?php 
                                                                $BalAmt1 = $val2['OpnBal1'][0] + $val2['DR1'][0] - $val2['CR1'][0];
                                                                if($BalAmt1 <= 0){
                                                                    $Pre1 = "CR";
                                                                }else{
                                                                    $Pre1 = "DR";
                                                                }
                                                            ?>
                                                            <td style="text-align:right;"><b><?php echo number_format(abs($BalAmt1), 2, '.', '')." ".$Pre1;?></b></td>
                                                            </tr>
                                                            <?php

                                                            foreach ($val2['SubGroups'] as $key3 => $val3) {
                                                                ?>
                                                                <tr class="treegrid-<?php echo html_entity_decode($Counter2); ?> treegrid-parent-<?php echo $SubCounter; ?> parent-node "
                                                                    id="subgroup1">
                                                                    <td class="parent">
                                                                        <?php echo $val3["SubGroupName"]; ?>
                                                                    </td>
                                                                    <td style="text-align:right"><b><?php echo number_format($val3['OpnBal2'][0], 2, '.', ''); ?></b></td>
                                                                    <td style="text-align:right"><b><?php echo number_format($val3['DR2'][0], 2, '.', ''); ?></b></td>
                                                                    <td style="text-align:right"><b><?php echo number_format($val3['CR2'][0], 2, '.', ''); ?></b></td>
                                                                
                                                                    <?php 
																		$BalAmt2 = $val3['OpnBal2'][0] + $val3['DR2'][0] - $val3['CR2'][0];
																		if($BalAmt2 <= 0){
																			$Pre2 = "CR";
																		}else{
																			$Pre2 = "DR";
																		}
                                                                    ?>
                                                                    <td style="text-align:right;"><b><?php echo number_format(abs($BalAmt2), 2, '.', '')." ".$Pre2;?></b></td>
                                                                </tr>
                                                                <?php
                                                                foreach ($val3['ledgers'] as $key4 => $val4) {
                                                                    ?>
                                                                    <tr class="treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $Counter2; ?> parent-node "
                                                                        id="ledgers">
                                                                        <td class="parent">
                                                                            <?php echo $val4["AccountName"]; ?>
                                                                        </td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>

                                                                    </tr>
                                                                    <?php
                                                                    $SubCounter2++;
                                                                }
                                                                ?>
                                                                <?php
                                                                $Counter2++;
                                                            }
                                                            ?>
                                                            <?php
                                                            $SubCounter++;
                                                        }
                                                        ?>
                                                        <?php
                                                        $MainCounter++;
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><b>Total</b></td>
                                                        <td style="text-align:right;"><b><?php echo number_format($TotalOpnBal, 2, '.', '');?></b></td>
                                                        <td style="text-align:right;"><b><?php echo number_format($TotalDR, 2, '.', '');?></b></td>
                                                        <td style="text-align:right;"><b><?php echo number_format($TotalCR, 2, '.', '');?></b></td>
                                                        <?php 
                                                            $TotalBal = $TotalOpnBal + $TotalDR - $TotalCR;
                                                            if($TotalBal <= 0){
                                                                $Pre5 = "CR";
                                                            }else{
                                                                $Pre5 = "DR";
                                                            }
                                                        ?>
                                                        <td style="text-align:right;"><b><?php echo number_format(abs($TotalBal), 2, '.', '')." ".$Pre5;?></b></td>
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
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    function myFunction2() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table-trial_bal_report");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            td1 = tr[i].getElementsByTagName("td")[1];
            td2 = tr[i].getElementsByTagName("td")[2];
            td3 = tr[i].getElementsByTagName("td")[3];
            td4 = tr[i].getElementsByTagName("td")[4];
            td5 = tr[i].getElementsByTagName("td")[5];
            td6 = tr[i].getElementsByTagName("td")[6];
            td7 = tr[i].getElementsByTagName("td")[7];
            td8 = tr[i].getElementsByTagName("td")[8];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else if (td1) {
                    txtValue = td1.textContent || td1.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else if (td2) {
                        txtValue = td2.textContent || td2.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                        } else if (td3) {
                            txtValue = td3.textContent || td3.innerText;
                            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                tr[i].style.display = "";
                            } else if (td4) {
                                txtValue = td4.textContent || td4.innerText;
                                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                    tr[i].style.display = "";

                                } else if (td5) {
                                    txtValue = td5.textContent || td5.innerText;
                                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                        tr[i].style.display = "";

                                    } else if (td6) {
                                        txtValue = td6.textContent || td6.innerText;
                                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                            tr[i].style.display = "";

                                        } else if (td7) {
                                            txtValue = td7.textContent || td7.innerText;
                                            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                                tr[i].style.display = "";

                                            } else if (td8) {
                                                txtValue = td8.textContent || td8.innerText;
                                                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                                    tr[i].style.display = "";

                                                } else {
                                                    tr[i].style.display = "none";
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
</script>
<script>

    $("#caexcel").click(function () {
        var as_on = $("#as_on").val();
        var SubgroupID = $("#Account_Group").val();
        $.ajax({
            url: "<?php echo admin_url(); ?>accounting/load_data_trial_balance_reportNewExport",
            method: "POST",
            data: { as_on: as_on, SubgroupID: SubgroupID },
            beforeSend: function () {
                $('#searchh3').css('display', 'block');
            },
            complete: function () {

                $('#searchh3').css('display', 'none');
            },
            success: function (data) {
                response = JSON.parse(data);
                window.location.href = response.site_url + response.filename;
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
        heading_data += '<td style="text-align:center;"colspan="3">Trial Balance Report</td>';
        heading_data += '</tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">' + html_filter_name + '</td>';
        heading_data += '</tr>';

        heading_data += '</tbody></table>';
        var print_data = stylesheet + heading_data + tableData
        newWin = window.open("");
        newWin.document.write(print_data);
        newWin.print();
        newWin.close();
    };
</script>
<script>

    function sortTable(f, n) {
        var rows = $('#table-trial_bal_report tbody  tr').get();

        rows.sort(function (a, b) {

            var A = getVal(a);
            var B = getVal(b);

            if (A < B) {
                return -1 * f;
            }
            if (A > B) {
                return 1 * f;
            }
            return 0;
        });

        function getVal(elm) {
            var v = $(elm).children('td').eq(n).text().toUpperCase();
            if ($.isNumeric(v)) {
                v = parseInt(v, 10);
            }
            return v;
        }

        $.each(rows, function (index, row) {
            $('#table-trial_bal_report').children('tbody').append(row);
        });
    }
    var f_sl = 1;
    var f_nm = 1;
    $("#sl").click(function () {
        if ($('.up').css('display') == 'none') {
            $(".up_starting").hide()
            $(".up").show()
            $(".down").hide()
        } else {
            $(".up_starting").hide()
            $(".up").hide()
            $(".down").show()
        }
        f_sl *= -1;
        var n = $(this).prevAll().length;
        sortTable(f_sl, n);
    });
    $("#nm").click(function () {
        f_nm *= -1;
        var n = $(this).prevAll().length;
        sortTable(f_nm, n);
    });
</script>
<script>
    $(document).ready(function () {
        var maxEndDate = new Date('Y/m/d');
        var fin_y = "<?php echo $this->session->userdata('finacial_year') ?>";

        var year = "20" + fin_y;
        var cur_y = new Date().getFullYear().toString().substr(-2);
        if (cur_y => fin_y) {
            var year2 = parseInt(fin_y) + parseInt(1);
            var year2_new = "20" + year2;

            var e_dat = new Date(year2_new + '/03/31');

            var maxEndDate_new = e_dat;
        } else {
            var e_dat2 = new Date(year2 + '/03/31');
            var maxEndDate_new = e_dat2;
        }

        var minStartDate = new Date(year, 03);

        $('#as_on').datetimepicker({
            format: 'd/m/Y',
            minDate: minStartDate,
            maxDate: maxEndDate_new,
            timepicker: false,
            showOtherMonths: false,
            pickTime: false,
            orientation: "left",
        });

    });
</script>
<script>


    $('#MainGroup').on('change', function () {
        var MainGroup = $("#MainGroup").val();
        $.ajax({
            url: "<?php echo admin_url(); ?>accounting/GetSubGroup1ByMainGroup",
            dataType: "JSON",
            method: "POST",
            data: { MainGroup: MainGroup },
            beforeSend: function () {
                $('.searchh2').css('display', 'block');
                $('.searchh2').css('color', 'blue');
            },
            complete: function () {
                $('.searchh2').css('display', 'none');
            },

            success: function (data) {
                $("#MainSubGroup").find('option').remove();
                $("#MainSubGroup").selectpicker("refresh");
                var html = "";
                for (var i = 0; i < data.length; i++) {
                    html += '<option value="' + data[i].SubActGroupID1 + '">' + data[i].SubActGroupName + '</option>';
                }
                $('#MainSubGroup').append(html);
                $('.selectpicker').selectpicker('refresh');
                $("#AccountSubGroupID2").find('option').remove();
                $("#AccountSubGroupID2").selectpicker("refresh");
            }
        });
    })

    $('#MainSubGroup').on('change', function () {
        var MainSubGroup =  $("#MainSubGroup").val();
        $.ajax({
            url: "<?php echo admin_url(); ?>accounting/GetActSubGroupID2ByAct1SubGroup1",
            dataType: "JSON",
            method: "POST",
            data: {  MainSubGroup:MainSubGroup },
            beforeSend: function () {
                $('.searchh2').css('display', 'block');
                $('.searchh2').css('color', 'blue');
            },
            complete: function () {
                $('.searchh2').css('display', 'none');
            },

            success: function (data) {
                $("#AccountSubGroupID2").find('option').remove();
                $("#AccountSubGroupID2").selectpicker("refresh");
                var html = "";
                for (var i = 0; i < data.length; i++) {
                    html += '<option value="' + data[i].SubActGroupID + '">' + data[i].SubActGroupName + '</option>';
                }
				 $('#AccountSubGroupID2').append(html);
                $('.selectpicker').selectpicker('refresh');
            }
        });
    })

</script>



</body>

</html>