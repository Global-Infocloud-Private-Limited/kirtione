<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-daily_report {
        overflow: auto;
        max-height: 55vh;
        width: 100%;
        position: relative;
        top: 0px;
    }

    .table-daily_report thead th {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .table-daily_report tbody th {
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
            <div class="col-md-10">

                <div class="panel_s">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Misc Reports </b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Customer List</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
    					
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
                            <div class="col-md-2">
                                <?php echo render_date_input('from_date', 'FROM', $from_date); ?>
                            </div>

                            <div class="col-md-2">
                                <?php echo render_date_input('to_date', 'TO', $to_date); ?>
                            </div>
                            <div class="col-md-2 leads-filter-column">
                                <?php echo render_select('client_type', $groups, array('id', 'Name'), 'User Type'); ?>
                            </div>


                            <?php
                            $report_type = array();
                            $array = array(
                                "value" => 1,
                                "name" => "Detailed"
                            );
                            array_push($report_type, $array);
                            $array = array(
                                "value" => 2,
                                "name" => "Summary"
                            );
                            array_push($report_type, $array);
                            ?>
                            <div class="col-md-2 leads-filter-column">
                                <?php echo render_select('report_type', $report_type, array('value', 'name'), 'Report Type', '1'); ?>
                            </div>

                            <div class="col-md-3 leads-filter-column">
                                <?php echo render_select('Account_state', $state, array('short_name', 'state_name'), 'State'); ?>
                            </div>

                            <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 19px;"
                                id="search_data">Show</button>
                            <div class="col-md-8">
                                <?php if (has_permission_new('CustomerList', '', 'export')) {
                                ?>
                                <a class="btn btn-default buttons-excel buttons-html5" style="margin-top: 19px;"
                                    tabindex="0" aria-controls="table-purchase_request" href="#"
                                    id="caexcel"><span>Export to excel</span></a>
                                    <?php } ?>
                                <?php if (has_permission_new('CustomerList', '', 'print')) {
                                ?>    
                                <a class="btn btn-default" href="javascript:void(0);"
                                    style="margin-top: 19px;margin-left:10px;" onclick="printPage();">Print</a>
                                    <?php } ?>
                            </div>
                            <div class="col-md-4" style="margin-top:15px;">
                                <input type="text" id="myInput1" class="form-control" onkeyup="searchTable()" placeholder="Search" title="Type in a name" style="float: right;">
                            </div>
                        </div>
                        
                        <hr class="hr-panel-heading" />
                        
                        <div class="table-daily_report tableFixHead2 row">
                        </div>
                        <span id="searchh2" style="display:none;">Loading.....</span>
                        <span id="searchh3" style="display:none;">Please wait data exporting...</span>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail() ?>
<script>

    function searchTable() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.querySelector(".table-daily_report table");
        tr = table.getElementsByTagName("tr");
        for (i = 2; i < tr.length; i++) {
            var tdArray = tr[i].getElementsByTagName("td");
            var rowContainsSearchTerm = false;
            for (var j = 0; j < tdArray.length; j++) {
                td = tdArray[j];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        rowContainsSearchTerm = true;
                        break;
                    }
                }
            }
            if (rowContainsSearchTerm) {

                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }



    $('#search_data').on('click', function () {
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        var client_type = $("#client_type").val();
        var Account_state = $("#Account_state").val();
        var report_type = $("#report_type").val();

        $.ajax({
            url: "<?php echo admin_url(); ?>Clients/load_data_filter",
            dataType: "json",
            method: "POST",
            data: { client_type: client_type, from_date: from_date, to_date: to_date, Account_state: Account_state, report_type: report_type },
            beforeSend: function () {
                $('.tableFixHead2').html('');
                $('#searchh22').css('display', 'none');
                $('#searchh2').css('display', 'block');
            },
            complete: function () {
                $('#searchh2').css('display', 'none');
            },
            success: function (data) {
                $('.tableFixHead2').html(data.html);
            }
        });
    });
</script>
<script type="text/javascript">
    function printPage() {

        var htmlString = $('.report_for').html();
        //   $( this ).text( htmlString );
        var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">' + document.getElementsByTagName('table')[0].innerHTML + '</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Customer Master</td>';
        heading_data += '</tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">' + htmlString + '</td>';
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
    $("#caexcel").click(function () {
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        var client_type = $("#client_type").val();
        var Account_state = $("#Account_state").val();
        var report_type = $("#report_type").val();
        $.ajax({
            url: "<?php echo admin_url(); ?>Clients/export_customerlist",
            method: "POST",
            data: { client_type: client_type, from_date: from_date, to_date: to_date, Account_state: Account_state, report_type: report_type },
            success: function (data) {
                response = JSON.parse(data);
                window.location.href = response.site_url + response.filename;
            }
        });
    });
</script>


</body>

</html>