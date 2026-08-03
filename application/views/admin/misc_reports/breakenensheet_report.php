<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .stock_position {
        overflow: auto;
        max-height: 55vh;
        width: 100%;
        position: relative;
        top: 0px;
    }

    .stock_position thead th {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .stock_position tbody th {
        position: sticky;
        left: 0;
    }

    .fixed_headers tbody td {
        border: 1px solid #E3E3E3;
        padding: 0px 5px;
    }

    .fixed_headers thead tr th {
        background-color: #f5f5f5 !important;
        color: #333;
        height: 20px;
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

    .scrollable-table {
        max-height: 300px;
        overflow-y: scroll;
    }


    .scrollable-table th {
        background-color: #f2f2f2;
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Misc Reports </b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Breakenen Sheet Report</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
                        <div class="row">
                            <div class="col-md-12">
                                <!--<h4>Calculation of BEP for Balance Qty</h4>-->
                                <!--<hr>-->
                            </div>
                        </div>
                        <div class="row ">

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
                            <?php /*
   <div class="col-md-2">
       <?php echo render_date_input('from_date', 'FROM', $from_date); ?>
   </div>
   */?>
                            <div class="col-md-2">
                                <?php echo render_date_input('to_date', 'As On Date', $to_date); ?>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="center">
                                    <label for="center" class="form-label">Center Name</label>
                                    <select name="center" id="center" class="selectpicker form-control"
                                        data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Non Selected</option>
                                        <?php
                                        foreach ($center as $key => $value) {
                                            ?>
                                            <option value="<?php echo $value['CenterID']; ?>">
                                                <?php echo $value['CenterName']; ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>

                                </div>

                            </div>

                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="commodity">
                                    <label for="commodity" class="form-label">Commodity Name</label>
                                    <select  name="commodity" id="commodity" class="selectpicker form-control"
                                        data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Non Selected</option>
                                        <?php
                                        foreach ($commodity as $key => $value) {
                                            ?>
                                            <option value="<?php echo $value['ItemID']; ?>">
                                                <?php echo $value['ItemName']; ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 19px;" id="search_data">Show</button>

                            <div class="col-md-5">
                                <?php if (has_permission_new('breakenen_report', '', 'export')) {
                                ?>
                                <a class="btn btn-default buttons-excel buttons-html2" tabindex="0" aria-controls="table-trial_bal_report" href="#" id="caexcel"
                                    style="float: left ! important;margin-right: 10px;"><span>Export to Excel</span></a>
                                <?php } ?>
                                
                                <?php if (has_permission_new('breakenen_report', '', 'print')) {
                                ?>
                                <a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
                                <?php } ?>
                            </div>
                            <div class="clearfix"></div>

                            </br>
                            <div class="col-md-12">
                                <table id="breakenentable">
                                    
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
<script type="text/javascript">


    function printPage() {
        var html_filter_name = $('.report_for').html();
        var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">' + document.getElementsByTagName('table')[0].innerHTML + '</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Calculation of BEP for Balance Qty</td>';
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

    $(document).ready(function () {
        var maxEndDate = new Date('Y/m/d');
        var fin_y = "23";

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

        $('#to_date').datetimepicker({
            format: 'd/m/Y',
            minDate: minStartDate,
            maxDate: maxEndDate_new,
            timepicker: false,
            showOtherMonths: false,
            pickTime: false,
            orientation: "left",
        });

    });

    $(document).ready(function () {
       // $('#breakenentable').html(<?php echo json_encode($html); ?>);
    });
    $('#commodity,#center,#to_date').on('change', function () {
        $('#breakenentable').html('');
    })
    $('#search_data').on('click', function () {
        var to_date = $("#to_date").val();
        var center = $("#center").val();
        var commodity = $("#commodity").val();
        if (commodity == "") {
            alert("Please Select the commodity.");
            return false;
        } else {

            $.ajax({
                url: "<?php echo admin_url(); ?>Misc_reports/GetBreakenensheet",
                dataType: "json",
                method: "POST",
                data: { commodity: commodity, center: center, to_date: to_date },
                beforeSend: function () {
                    $('#breakenentable').html('');
                    $('#searchh22').css('display', 'none');
                    $('#searchh2').css('display', 'block');
                },
                complete: function () {
                    $('#searchh2').css('display', 'none');
                },
                success: function (data) {
                    $('#breakenentable').html(data);

                }
            });
        }

    });


</script>
<!-- <script>
    $("#caexcel").click(function(){
      var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        var item_main_group = $("#item_main_group").val();
        var item_group = '';
        var favorite = [];
            $.each($("input[name='chk']:checked"), function(){
                favorite.push($(this).val());
            });
        var item_group = favorite.join(",");
        //alert(item_group);

        if{
  $.ajax({
            url:"<?php echo admin_url(); ?>misc_reports/exportCummulativeStock",
            method:"POST",
           data:{from_date:from_date, to_date:to_date, item_group:item_group,item_main_group:item_main_group},
            beforeSend: function () {
               $('#searchh2').css('display','block');
            },
            complete: function () {
                $('#searchh2').css('display','none');
            },
            success:function(data){
                response = JSON.parse(data);
                window.location.href = response.site_url+response.filename;
            }
        });
        } 
});
    </script> -->