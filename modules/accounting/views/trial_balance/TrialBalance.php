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
                <div class="col-md-12 text-centerr"  >
					<nav aria-label="breadcrumb" >
						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
							<li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
							<li class="breadcrumb-item active" aria-current="page"><b>Trial Balance</b></li>
							
						</ol>
					</nav>
					<hr class="hr_style" style="margin-Bottom:12px !important;">
				</div>
				
                <div class="col-md-12">
                    <div class="searchh2" style="display:none;">Please wait while fetching data.</div>
                </div>
                <div class="col-md-8">
                    <div class="row">
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
                                    $SelectedSubGroup1 = $filter_data["ActSubGroup1"];
                                }
                            ?>
                            
                        <div class="col-md-3">
                            <div class="form-group" app-field-wrapper="ActSubGroup1">
                                <small class="req text-danger">* </small>
                                <label for="ActSubGroup1" class="control-label">ActGroup Name</label>
                                <select name="ActSubGroup1[]" id="ActSubGroup1" value="ActSubGroup1" class="selectpicker form-control" multiple
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
                </div> 		
            </div>
                      
            <div class="row ">
                
                <div class="col-md-8">
                    
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
                                        $MainCounter = 1000;
                                        $SubCounter = 2000;
                                        $Counter2 = 3000;
                                        $i = 1;
                                        foreach ($nestedData as $key => $val) {
                                            
                                            ?>
                                            <tr class="treegrid-<?php echo $MainCounter; ?> parent-node expanded"
                                                style="font-size:14px;"id="maingroup">
                                                <td class="parent" colspan="5"><?php echo $val['MainGroup']; ?></td>
                                                
                                            </tr>
                                            <?php

                                            foreach ($val['SubGroups1'] as $key1 => $val1) {
                                                    
                                                ?>
                                                <tr class="treegrid-<?php echo html_entity_decode($SubCounter); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node expanded "
                                                     id="subgroup">
                                                    <td class="parent" style="font-size:13px;" colspan="5"><?php echo $val1["SubGroup1Name"]; ?></td>
                                                    
                                                </tr>
                                                
                                                <?php
                                                foreach ($val1['SubGroups'] as $key2 => $val2) {
                                                    if($i >1){
                                                        // Assets and other all main group except Liability
                                                        if($val2['Group2ClsBal'] >= 0){
                                                            $CrDrGRP2 = "Dr";
                                                        }else{
                                                            $CrDrGRP2 = "Cr";
                                                        }
                                                    }else{
                                                        // Liability
                                                        if($val2['Group2ClsBal'] >= 0){
                                                            $CrDrGRP2 = "Cr";
                                                        }else{
                                                            $CrDrGRP2 = "Dr";
                                                        }
                                                    }
                                                    ?>
                                                    <tr
                                                        class="treegrid-<?php echo html_entity_decode($Counter2); ?> treegrid-parent-<?php echo $SubCounter; ?> parent-node " id="subgroup1">
                                                        <td class="parent" style="font-size:12px;"><?php echo $val2["SubGroupName"]; ?></td>
                                                        <td style="text-align:right;font-size:12px;"><?php echo number_format($val2['Group2OpnBal'], 2, '.', ''); ?></td>
                                                        <td style="text-align:right;font-size:12px;"><?php echo number_format($val2['Group2DrBal'], 2, '.', ''); ?></td>
                                                        <td style="text-align:right;font-size:12px;"><?php echo number_format($val2['Group2CrBal'], 2, '.', ''); ?></td>
                                                        <td style="text-align:right;font-size:12px;"><?php echo number_format(abs($val2['Group2ClsBal']), 2, '.', '')." ".$CrDrGRP2; ?></td>
                                                    </tr>
                                                    <?php
                                                        foreach ($val2['Accounts'] as $key3 => $val3) {
                                                            $AccountBal = $val3["AccountClsBal"];
                                                            if($i >1){
                                                                // Assets and other all main group except Liability
                                                                if($AccountBal >= 0){
                                                                    $CrDr = "Dr";
                                                                }else{
                                                                    $CrDr = "Cr";
                                                                }
                                                            }else{
                                                                // Liability
                                                                if($AccountBal >= 0){
                                                                    $CrDr = "Cr";
                                                                }else{
                                                                    $CrDr = "Dr";
                                                                }
                                                            }
                                                        ?>
                                                        <tr class="treegrid-<?php echo html_entity_decode($Counter3); ?> treegrid-parent-<?php echo $Counter2; ?> parent-node " id="Accounts">
                                                            <td class="parent"><?php echo $val3["AccountName"]; ?></td>
                                                            <td style="text-align:right;"><?php echo number_format($val3['AccountOpnBal'], 2, '.', ''); ?></td>
                                                            <td style="text-align:right;"><?php echo number_format($val3['AccountDrBal'], 2, '.', ''); ?></td>
                                                            <td style="text-align:right;"><?php echo number_format($val3['AccountCrBal'], 2, '.', ''); ?></td>
                                                            <td style="text-align:right;"><?php echo number_format(abs($val3['AccountClsBal']), 2, '.', '')." ".$CrDr; ?></td>
                                                        <?php
                                                            $Counter3 ++;
                                                            }
                                                        ?>
                                                <?php
                                                    $Counter2 ++;
                                                    }
                                                    
                                                    if($i >1){
                                                        // Assets and other all main group except Liability
                                                        if($val1['Group1ClsBal'] >= 0){
                                                            $CrDrGRP1 = "Dr";
                                                        }else{
                                                            $CrDrGRP1 = "Cr";
                                                        }
                                                    }else{
                                                        // Liability
                                                        if($val1['Group1ClsBal'] >= 0){
                                                            $CrDrGRP1 = "Cr";
                                                        }else{
                                                            $CrDrGRP1 = "Dr";
                                                        }
                                                    }
                                                ?>
                                                <tr style="border: 1px solid #000;">
                                                    <td colspan="1" style="font-size:13px;font-weight:600;">Total for <?php echo $val1['SubGroup1Name']; ?></td>
                                                    
                                                    <td style="font-size:13px;font-weight:600;text-align:right;"><?php echo number_format($val1['Group1OpnBal'], 2, '.', ''); ?></td>
                                                    <td style="font-size:13px;font-weight:600;text-align:right;"><?php echo number_format($val1['Group1DrBal'], 2, '.', ''); ?></td>
                                                    <td style="font-size:13px;font-weight:600;text-align:right;"><?php echo number_format($val1['Group1CrBal'], 2, '.', ''); ?></td>
                                                    <td style="font-size:13px;font-weight:600;text-align:right;"><?php echo number_format(abs($val1['Group1ClsBal']), 2, '.', '')." ".$CrDrGRP1; ?></td>
                                                </tr>
                                            <?php
                                            $SubCounter ++;
                                            }
                                            ?>
                
                                            <tr style="border: 1px solid #000;">
                                                <td colspan="1" style="font-size:14px;font-weight:700;">Total for <?php echo $val['MainGroup']; ?></td>
                                                
                                                <td style="font-size:14px;font-weight:700;text-align:right;"><?php echo number_format($val['MainGroupOpnBal'], 2, '.', ''); ?></td>
                                                <td style="font-size:14px;font-weight:700;text-align:right;"><?php echo number_format($val['MainGroupDrBal'], 2, '.', ''); ?></td>
                                                <td style="font-size:14px;font-weight:700;text-align:right;"><?php echo number_format($val['MainGroupCrBal'], 2, '.', ''); ?></td>
                                                <td style="font-size:14px;font-weight:700;text-align:right;"><?php echo number_format(abs($val['MainGroupClsBal']), 2, '.', ''); ?></td>
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
                $("#ActSubGroup1").find('option').remove();
                $("#ActSubGroup1").selectpicker("refresh");
                var html = "";
                for (var i = 0; i < data.length; i++) {
                    html += '<option value="' + data[i].SubActGroupID1 + '">' + data[i].SubActGroupName + '</option>';
                }
                $('#ActSubGroup1').append(html);
                $('.selectpicker').selectpicker('refresh');
                $("#AccountSubGroupID2").find('option').remove();
                $("#AccountSubGroupID2").selectpicker("refresh");
            }
        });
    })

    $('#ActSubGroup1').on('change', function () {
        var ActSubGroup1 =  $("#ActSubGroup1").val();
        $.ajax({
            url: "<?php echo admin_url(); ?>accounting/GetActSubGroupID2ByAct1SubGroup1",
            dataType: "JSON",
            method: "POST",
            data: {  ActSubGroup1:ActSubGroup1 },
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