<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-reminder_report { overflow: auto; max-height: 55vh; width: 100%; }
    .table-reminder_report thead th { position: sticky; top: 0; z-index: 1; background: #50607b; color: #fff !important; }
    .table-reminder_report table { border-collapse: collapse; width: 100%; }
    .table-reminder_report th,
    .table-reminder_report td { padding: 4px 8px !important; white-space: nowrap; border: 1px solid !important; font-size: 12px; vertical-align: middle !important; }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-bottom:0 !important;">
                                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                <li class="breadcrumb-item active text-capitalize"><b>K1 Purchase</b></li>
                                <li class="breadcrumb-item active" aria-current="page"><b>Purchase Order Reminder Report</b></li>
                            </ol>
                        </nav>
                        <hr class="hr_style">

                        <div class="row">
                            <?php
                            $can_view_reminder_report = isset($can_view_reminder_report) ? $can_view_reminder_report : has_permission_new('PurchaseOrderReminderReport', '', 'view');
                            $can_print_reminder_report = isset($can_print_reminder_report) ? $can_print_reminder_report : has_permission_new('PurchaseOrderReminderReport', '', 'print');
                            $can_export_reminder_report = isset($can_export_reminder_report) ? $can_export_reminder_report : has_permission_new('PurchaseOrderReminderReport', '', 'export');
                            $fy = $this->session->userdata('finacial_year');
                            $fy_new = $fy + 1;
                            $lastdate_date = '20' . $fy_new . '-03-31';
                            $curr_date = date('Y-m-d');
                            $curr_date_new = new DateTime($curr_date);
                            $last_date_yr = new DateTime($lastdate_date);
                            if ($last_date_yr < $curr_date_new) {
                                $to_date = '31/03/20' . $fy_new;
                                $from_date = '01/03/20' . $fy_new;
                            } else {
                                $from_date = '01/' . date('m') . '/' . date('Y');
                                $to_date = date('d/m/Y');
                            }
                            ?>
                            <div class="col-md-2">
                                <?php echo render_date_input('from_date', 'From Date', $from_date); ?>
                            </div>
                            <div class="col-md-2">
                                <?php echo render_date_input('to_date', 'To Date', $to_date); ?>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">&nbsp;</label><br>
                                <?php if ($can_view_reminder_report || $can_print_reminder_report) { ?>
                                    <button type="button" class="btn btn-info" id="search_data">Show</button>
                                <?php } ?>
                                <?php if ($can_print_reminder_report) { ?>
                                    <button type="button" class="btn btn-warning mleft5" id="print_data">Print</button>
                                <?php } ?>
                                <?php if ($can_export_reminder_report) { ?>
                                    <button type="button" class="btn btn-success mleft5" id="caexcel">Export</button>
                                <?php } ?>
                            </div>
                            <?php if ($can_view_reminder_report || $can_print_reminder_report) { ?>
                            <div class="col-md-4">
                                <label class="control-label">&nbsp;</label>
                                <input type="text" class="form-control" id="myInput1" onkeyup="myFunction2()" placeholder="Search.." title="Type to search">
                            </div>
                            <?php } ?>
                        </div>

                        <?php if (!$can_view_reminder_report && !$can_print_reminder_report && !$can_export_reminder_report) { ?>
                        <div class="alert alert-warning mtop10">You do not have permission to access this report.</div>
                        <?php } elseif ($can_view_reminder_report || $can_print_reminder_report) { ?>
                        <hr class="hr-panel-heading" />

                        <div class="table-reminder_report mtop10">
                            <table class="tree table table-striped table-bordered table_purchase_report" id="table_purchase_report" width="100%">
                                <thead>
                                    <tr style="display:none;" class="print-header-row">
                                        <td colspan="5">
                                            <h5 style="text-align:center;">
                                                <span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br>
                                                <span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br>
                                                <span class="report_for" style="font-size:10px;"></span>
                                            </h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="width:8%; text-align:center;">Sr.No</th>
                                        <th style="width:20%; text-align:left;">Purchase Order ID</th>
                                        <th style="width:15%; text-align:left;">Reminder Date</th>
                                        <th style="width:15%; text-align:left;">Reminder Status</th>
                                        <th style="width:42%; text-align:left;">Reminder Notes</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <span id="searchh2" style="display:none;">Loading.....</span>
                        <span id="searchh3" style="display:none;">Please wait, data exporting...</span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
    var canViewReminderReport = <?php echo $can_view_reminder_report ? 'true' : 'false'; ?>;
    var canPrintReminderReport = <?php echo $can_print_reminder_report ? 'true' : 'false'; ?>;
    var canExportReminderReport = <?php echo $can_export_reminder_report ? 'true' : 'false'; ?>;

    function load_data(from_date, to_date) {
        if (!canViewReminderReport && !canPrintReminderReport) {
            alert('You do not have permission to load this report.');
            return;
        }
        $.ajax({
            url: "<?php echo admin_url(); ?>PurchaseMaster/load_data_for_purchase_order_reminder_report",
            method: "POST",
            data: { from_date: from_date, to_date: to_date },
            beforeSend: function () {
                $('#searchh2').show();
                $('.table_purchase_report tbody').hide();
            },
            complete: function () {
                $('.table_purchase_report tbody').show();
                $('#searchh2').hide();
            },
            success: function (data) {
                $('.table_purchase_report tbody').html(data);
                var from_date_val = $('#from_date').val();
                var to_date_val = $('#to_date').val();
                $('.report_for').text('Purchase Order Reminder Report | From Date: ' + from_date_val + ' To Date: ' + to_date_val);
            }
        });
    }

    function myFunction2() {
        var input = document.getElementById('myInput1');
        var filter = input.value.toUpperCase();
        var table = document.querySelector('.table_purchase_report');
        var tr = table.getElementsByTagName('tr');
        for (var i = 2; i < tr.length; i++) {
            var tdArray = tr[i].getElementsByTagName('td');
            var rowContainsSearchTerm = false;
            for (var j = 0; j < tdArray.length; j++) {
                var td = tdArray[j];
                if (td) {
                    var txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        rowContainsSearchTerm = true;
                        break;
                    }
                }
            }
            tr[i].style.display = rowContainsSearchTerm ? '' : 'none';
        }
    }

    $('#search_data').on('click', function () {
        load_data($('#from_date').val(), $('#to_date').val());
    });

    function printPage() {
        if (!canPrintReminderReport) {
            alert('You do not have permission to print this report.');
            return;
        }
        var tableBodyHtml = $('.table_purchase_report tbody').html();
        if (!tableBodyHtml.trim()) {
            alert('No data to print!');
            return;
        }

        var companyName = "<?php echo addslashes($company_detail->company_name); ?>";
        var companyAddress = "<?php echo addslashes($company_detail->address); ?>";
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var filterString = 'From Date: ' + from_date + ' , To Date: ' + to_date;

        var printContent =
            '<style>th,td{padding:5px;border:1px solid #000;font-size:12px;}</style>' +
            '<table border="1" cellpadding="0" cellspacing="0" width="100%" style="font-size:12px;">' +
            '<tr><td style="text-align:center;" colspan="5">' + companyName + '</td></tr>' +
            '<tr><td style="text-align:center;" colspan="5">' + companyAddress + '</td></tr>' +
            '<tr><td style="text-align:center;" colspan="5">Purchase Order Reminder Report</td></tr>' +
            '<tr><td style="text-align:center;" colspan="5">' + filterString + '</td></tr>' +
            '</table>' +
            '<table border="1" cellpadding="0" cellspacing="0" width="100%" style="font-size:12px;margin-top:10px;">' +
            '<thead><tr>' +
            '<th>Sr.No</th><th>Purchase Order ID</th><th>Reminder Date</th><th>Reminder Status</th><th>Reminder Notes</th>' +
            '</tr></thead><tbody>' + tableBodyHtml + '</tbody></table>';

        var newWin = window.open('');
        if (!newWin) {
            alert('Please allow popups for this website');
            return;
        }
        newWin.document.write(printContent);
        newWin.document.close();
        newWin.focus();
        newWin.print();
        newWin.close();
    }

    $('#print_data').on('click', function () {
        printPage();
    });

    $('#caexcel').on('click', function () {
        if (!canExportReminderReport) {
            alert('You do not have permission to export this report.');
            return;
        }
        $('#searchh3').show();
        $.ajax({
            url: "<?php echo admin_url(); ?>PurchaseMaster/export_PurchaseOrderReminderReport",
            method: 'POST',
            data: {
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val()
            },
            complete: function () {
                $('#searchh3').hide();
            },
            success: function (response) {
                response = JSON.parse(response);
                window.location.href = response.site_url + response.filename;
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        var maxEndDate = new Date('Y/m/d');
        var fin_y = "<?php echo $this->session->userdata('finacial_year'); ?>";
        var year = '20' + fin_y;
        var cur_y = new Date().getFullYear().toString().substr(-2);
        if (cur_y > fin_y) {
            var year2 = parseInt(fin_y) + parseInt(1);
            var year2_new = '20' + year2;
            var e_dat = new Date(year2_new + '/03/31');
            var maxEndDate_new = e_dat;
        } else {
            var maxEndDate_new = maxEndDate;
        }
        var minStartDate = new Date(year, 3);
        $('#from_date, #to_date').datetimepicker({
            format: 'd/m/Y',
            minDate: minStartDate,
            maxDate: maxEndDate_new,
            timepicker: false
        });
    });
</script>
