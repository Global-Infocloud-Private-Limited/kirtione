<?php defined('BASEPATH') or exit('No direct script access allowed');
$FY = (int) $this->session->userdata('finacial_year');
$fy_start_year = 2000 + $FY;
$fy_end_year = $fy_start_year + 1;
$CY = (int) date('y');
$CM = (int) date('m');
$today_year = 2000 + $CY;
$is_inside_fy = ($today_year == $fy_start_year && $CM >= 4 && $CM <= 12)
    || ($today_year == $fy_end_year && $CM >= 1 && $CM <= 3);
$default_effective_date = $is_inside_fy ? date('d/m/Y') : sprintf('31/03/%d', $fy_end_year);
$fy_min_date_display = sprintf('01/04/%d', $fy_start_year);
$fy_max_date_display = sprintf('31/03/%d', $fy_end_year);
$list_from_date = $is_inside_fy ? date('d/m/Y') : sprintf('31/03/%d', $fy_end_year);
$list_to_date   = $list_from_date;
?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-bottom:0px !important;">
                                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                <li class="breadcrumb-item active text-capitalize"><b>K1 Inventory</b></li>
                                <li class="breadcrumb-item active" aria-current="page"><b>Retail Rate Master</b></li>
                            </ol>
                        </nav>
                        <hr class="hr_style">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info" id="status_message" style="display:none;"></div>
                                <div class="searchh2" style="display:none;">Please wait fetching data...</div>
                                <div class="searchh3" style="display:none;">Please wait creating new Rate Master...</div>
                                <div class="searchh4" style="display:none;">Please wait updating Rate Master...</div>
                            </div>
                        </div>

                        <input type="hidden" id="RateCode" name="RateCode" value="">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="ItemName" class="control-label">Item Name</label>
                                    <select class="selectpicker display-block" data-width="100%" id="ItemName" name="ItemName" data-live-search="true" required>
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="HSNCode" class="control-label">HSN Code</label>
                                    <input type="text" id="HSNCode" name="HSNCode" class="form-control" value="" required readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="UOM" class="control-label">UOM</label>
                                    <input type="text" id="UOM" name="UOM" class="form-control" value="" required readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="UnitWtKg" class="control-label">Unit Wt (Kg)</label>
                                    <input type="number" id="UnitWtKg" name="UnitWtKg" class="form-control" value="" step="0.01" required readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="CenterName" class="control-label">Center Name</label>
                                    <select class="selectpicker display-block" data-width="100%" id="CenterName" name="CenterName[]" data-live-search="true" data-actions-box="true" multiple required>
                                        <!-- <option value="ALL">All Centers</option> -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <?= render_date_input('EffectiveDate', 'Effective On', $default_effective_date, ['inputmode' => 'numeric', 'pattern' => '[0-9\\/]*', 'placeholder' => 'dd/mm/yyyy']); ?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="Rate" class="control-label">Rate (including GST)</label>
                                    <input type="text" id="Rate" name="Rate" class="form-control" value="" placeholder="Enter sale rate (e.g. 100.00)" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="Discount" class="control-label">Discount (in ₹)</label>
                                    <input type="text" id="Discount" name="Discount" class="form-control" value="" placeholder="Enter discount amount (optional)">
                                </div>
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="action-buttons text-left">
                                    <?php if (has_permission_new('K1RateMaster', '', 'create')) { ?>
                                        <button type="button" id="saveBtn" class="btn btn-success btn-group-custom saveBtn">
                                            <i class="fa fa-save"></i> Save
                                        </button>
                                    <?php } ?>
                                    <?php if (has_permission_new('K1RateMaster', '', 'edit')) { ?>
                                        <button type="button" id="updateBtn" class="btn btn-success btn-group-custom updateBtn" style="display:none;">
                                            <i class="fa fa-save"></i> Update
                                        </button>
                                    <?php } ?>
                                    <button type="button" class="btn btn-warning btn-group-custom resetBtn">
                                        <i class="fa fa-refresh"></i> Reset
                                    </button>
                                    <button type="button" class="btn btn-info btn-group-custom showListBtn" data-toggle="modal" data-target="#rateListModal">
                                        <i class="fa fa-list"></i> Show List
                                    </button>
                                </div>
                            </div>
                        </div>
                        <br>
                        <hr class="hr_style">

                        <div class="row" id="rateImportSection">
                            <div class="col-md-12">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <div class="clearfix import-section-header mbot15">
                                            <h4 class="bold mtop5 mbot0 pull-left">Upload K1 Rate Master CSV</h4>
                                            <button type="button" id="downloadSampleCSV" class="btn btn-info pull-right">
                                                <i class="fa fa-download"></i> Download Sample CSV
                                            </button>
                                        </div>
                                        <div class="row import-fields-row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <small class="req text-danger">* </small>
                                                    <label for="ImportCenterName" class="control-label">Center Name</label>
                                                    <select class="selectpicker display-block" data-width="100%" id="ImportCenterName" name="ImportCenterName[]" data-live-search="true" data-actions-box="true" multiple>
                                                        <!-- <option value="ALL">All Centers</option> -->
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <small class="req text-danger">* </small>
                                                    <label for="ImportEffectiveDate" class="control-label">Effective On</label>
                                                    <input type="text" id="ImportEffectiveDate" name="ImportEffectiveDate" class="form-control datepicker" value="<?= $default_effective_date; ?>" placeholder="dd/mm/yyyy" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <small class="req text-danger">* </small>
                                                    <label for="RateCSVFile" class="control-label">CSV File</label>
                                                    <input type="file" id="RateCSVFile" name="RateCSVFile" class="form-control" accept=".csv" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <small class="text-muted">CSV must contain columns <strong>ItemID</strong>, <strong>AssignedRate</strong>, and <strong>Discount</strong>.</small>
                                            </div>
                                        </div>
                                        <div class="form-group" id="importResultContainer" style="display:none;">
                                            <div id="importResultMessage"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 text-right">
                                                <?php if (has_permission_new('K1RateMaster', '', 'create')) { ?>
                                                    <button type="button" id="importBtn" class="btn btn-success">
                                                        <i class="fa fa-upload"></i> Upload &amp; Save
                                                    </button>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="rateListModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog modal-xl" style="max-width:1230px;">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title">K1 Rate Master List</h4>
                                    </div>
                                    <div class="modal-body" style="padding:5px;">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <?php echo render_date_input('ListFromDate', 'From', $list_from_date, ['placeholder' => 'dd/mm/yyyy']); ?>
                                            </div>
                                            <div class="col-md-3">
                                                <?php echo render_date_input('ListToDate', 'To', $list_to_date, ['placeholder' => 'dd/mm/yyyy']); ?>
                                            </div>
                                            <div class="col-md-3">
                                                <br>
                                                <button type="button" id="showListBtnFilter" class="btn btn-info pull-left mleft5">
                                                    <i class="fa fa-search"></i> <?php echo _l('rate_filter'); ?>
                                                </button>
                                                <button type="button" id="clearListFilterBtn" class="btn btn-default pull-left mleft5" style="display:none;">
                                                    <i class="fa fa-times"></i> Clear
                                                </button>
                                            </div>
                                            <div class="col-md-3">
                                                <br>
                                                <input type="text" id="rateListSearchInput" onkeyup="filterRateListTable()" placeholder="Search in list..." title="Search in list" class="form-control" style="float:right;">
                                            </div>
                                            <div class="col-md-12">
                                                <div class="table_purchase_report">
                                                    <table class="tree table table-striped table-bordered table_purchase_report" id="ratemaster_table" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th style="text-align:left;">Item Name</th>
                                                                <th style="text-align:left;">Center Name</th>
                                                                <th style="text-align:left;">HSN Code</th>
                                                                <th style="text-align:left;">UOM</th>
                                                                <th style="text-align:left;">Unit Wt (Kg)</th>
                                                                <th style="text-align:left;">Rate</th>
                                                                <th style="text-align:left;">Discount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="rate_table_body"></tbody>
                                                    </table>
                                                </div>
                                                <span id="rateListLoading" style="display:none;">Loading.....</span>
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
</div>

<?php init_tail(); ?>

<style>
    .hr_style { margin: 15px 0; border: 0; border-top: 1px solid #e3e3e3; }
    .custombreadcrumb { padding: 0.75rem 0 !important; }
    .btn-group-custom { margin-right: 5px; margin-bottom: 5px; }
    .action-buttons { margin-top: 20px; }
    .table_purchase_report { overflow: auto; max-height: 60vh; width: 100%; position: relative; top: 0; }
    .table_purchase_report thead th { position: sticky; top: 0; z-index: 1; background-color: #f5f5f5; }
    #ratemaster_table { font-size: 13px; }
    #rateImportSection .import-section-header .btn {
        margin-top: 2px;
    }
</style>

<script>
    var rateMasterStatusTimeout = null;
    var isEditMode = false;
    var itemDetailsMap = {};
    var fyMinDate = null;
    var fyMaxDate = null;
    var fyMinDateDisplay = '<?= $fy_min_date_display; ?>';
    var fyMaxDateDisplay = '<?= $fy_max_date_display; ?>';
    var listDefaultFromDate = '<?= $list_from_date; ?>';
    var listDefaultToDate = '<?= $list_to_date; ?>';

    function initFinancialYearDatePickers() {
        var fin_y = parseInt("<?= $FY; ?>", 10);
        var year = 2000 + fin_y;
        fyMinDate = new Date(year, 3, 1);
        var fyEndDate = new Date(year + 1, 2, 31);
        var today = new Date();
        today.setHours(0, 0, 0, 0);

        fyMaxDate = fyEndDate;

        var effectivePickerOptions = {
            format: 'd/m/Y',
            minDate: today,
            // maxDate: fyEndDate,
            timepicker: false,
            scrollMonth: false,
            scrollInput: false,
            scrollTime: false,
        };

        var listMaxDate = today < fyEndDate ? today : fyEndDate;

        var listPickerOptions = {
            format: 'd/m/Y',
            minDate: fyMinDate,
            maxDate: listMaxDate,
            timepicker: false,
            scrollMonth: false,
            scrollInput: false,
            scrollTime: false,
            onSelectDate: function() {
                onListDateChanged();
            },
            onChangeDateTime: function() {
                onListDateChanged();
            }
        };

        ['#EffectiveDate', '#ImportEffectiveDate'].forEach(function(selector) {
            var $el = $(selector);
            if (!$el.length) {
                return;
            }
            if ($el.data('xdsoft_datetimepicker')) {
                $el.datetimepicker('destroy');
            }
            if ($el.hasClass('hasDatepicker')) {
                $el.datepicker('destroy');
            }
            $el.datetimepicker(effectivePickerOptions);
        });

        ['#ListFromDate', '#ListToDate'].forEach(function(selector) {
            var $el = $(selector);
            if (!$el.length) {
                return;
            }
            if ($el.data('xdsoft_datetimepicker')) {
                $el.datetimepicker('destroy');
            }
            if ($el.hasClass('hasDatepicker')) {
                $el.datepicker('destroy');
            }
            $el.datetimepicker(listPickerOptions);
        });
    }

    function isWithinFinancialYear(dateStr) {
        if (!dateStr || !fyMinDate || !fyMaxDate) {
            return false;
        }
        var dt = parseDDMMYYYY(dateStr);
        if (!dt) {
            return false;
        }
        dt.setHours(0, 0, 0, 0);
        var min = new Date(fyMinDate.getTime());
        min.setHours(0, 0, 0, 0);
        var max = new Date(fyMaxDate.getTime());
        max.setHours(0, 0, 0, 0);
        return dt >= min && dt <= max;
    }

    function showToast(type, message, duration) {
        duration = duration || 3200;
        if (typeof toastr !== 'undefined') {
            var opts = { timeOut: duration, extendedTimeOut: duration };
            if (type === 'success') toastr.success(message, null, opts);
            else if (type === 'error') toastr.error(message, null, opts);
            else if (type === 'warning') toastr.warning(message, null, opts);
            else toastr.info(message, null, opts);
        }
    }

    function showStatusMessage(type, message, duration) {
        duration = duration || 4000;
        var $status = $('#status_message');
        if (!$status.length) return;
        if (rateMasterStatusTimeout) {
            clearTimeout(rateMasterStatusTimeout);
            rateMasterStatusTimeout = null;
        }
        $status.stop(true, true);
        $status.removeClass('alert-success alert-danger alert-warning alert-info');
        $status.addClass(type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : type === 'warning' ? 'alert-warning' : 'alert-info');
        $status.html(message).css('display', 'block');
        rateMasterStatusTimeout = setTimeout(function() {
            $status.fadeOut(400, function() { rateMasterStatusTimeout = null; });
        }, duration);
    }

    $(function() {
        setTimeout(function() {
            initFinancialYearDatePickers();
            updateListFilterButtons();
        }, 150);

        loadItems();
        loadCenters();

        $('.saveBtn').click(function() {
            if (validateForm()) saveRateMaster();
        });

        $('.resetBtn').click(function() {
            resetForm();
        });

        $('.updateBtn').click(function() {
            if (validateForm()) updateRateMaster();
        });

        resetImportModal();

        $('#ImportCenterName').on('changed.bs.select', function() {
            var selected = $(this).val() || [];
            if (selected.indexOf('ALL') !== -1 && selected.length > 1) {
                $(this).val(['ALL']).selectpicker('refresh');
            }
        });

        $('#ImportEffectiveDate').on('input', function() {
            $(this).val($(this).val().replace(/[^0-9\/]/g, ''));
        });

        $('#downloadSampleCSV').click(function() {
            downloadSampleCSV();
        });

        $('#importBtn').click(function() {
            importRateMasterCSV();
        });

        $('#RateCSVFile').on('change', function() {
            hideImportResult();
        });

        $('#ItemName').on('changed.bs.select change', function() {
            var itemId = $(this).val();
            if (itemId) {
                applyItemDetails(itemId);
            } else {
                clearItemFields();
            }
        });

        $('#CenterName').on('changed.bs.select', function() {
            if (isEditMode) return;
            var selected = $(this).val() || [];
            if (selected.indexOf('ALL') !== -1 && selected.length > 1) {
                $(this).val(['ALL']).selectpicker('refresh');
            }
        });

        $('#EffectiveDate').on('input', function() {
            $(this).val($(this).val().replace(/[^0-9\/]/g, ''));
        });

        $('#Rate, #Discount').on('input', function() {
            var value = $(this).val().replace(/[^\d.]/g, '');
            var parts = value.split('.');
            if (parts.length > 2) value = parts[0] + '.' + parts[1];
            $(this).val(value);
        });

        $('#Rate').on('blur', function() {
            var value = $(this).val().trim();
            if (value && !/^\d+(\.\d{1,2})?$/.test(value)) {
                showToast('warning', 'Rate must be a number (e.g., 10 or 1.20)');
                $(this).val('');
            }
        });
    });

    function loadRateMasterData(fromDate, toDate) {
        $('#rateListLoading').show();
        $.ajax({
            url: '<?= admin_url('K1RateMaster/getRateMasterData'); ?>',
            type: 'POST',
            data: { FromDate: fromDate || '', ToDate: toDate || '' },
            dataType: 'json',
            success: function(data) {
                var html = '';
                if (data.length > 0) {
                    var currentDate = null;
                    $.each(data, function(i, item) {
                        var effDate = formatDateDDMMYYYY(item.EffectiveDate);
                        if (effDate !== currentDate) {
                            currentDate = effDate;
                            html += '<tr class="rate-list-date-row" style="background:#f0f0f0;font-weight:bold;"><td colspan="7">Effective Date: ' + (effDate || 'N/A') + '</td></tr>';
                        }
                        html += '<tr onclick="rowClickHandler(event, \'' + item.id + '\')" style="cursor:pointer;">';
                        html += '<td>' + escapeHtml(item.ItemName || '') + '</td>';
                        html += '<td>' + escapeHtml(item.CenterName || '') + '</td>';
                        html += '<td>' + escapeHtml(item.HSNCode || '') + '</td>';
                        html += '<td>' + escapeHtml(item.UnitShortCode || '') + '</td>';
                        html += '<td>' + escapeHtml(item.UnitWtKg || '') + '</td>';
                        html += '<td>' + escapeHtml(item.Rate || '') + '</td>';
                        html += '<td>' + escapeHtml(item.dis_per || '0.00') + '</td>';
                        html += '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="7" class="text-center">No Data Found</td></tr>';
                }
                $('#rate_table_body').html(html);
                $('#rateListSearchInput').val('');
            },
            error: function() {
                showToast('error', 'Error loading data');
            },
            complete: function() {
                $('#rateListLoading').hide();
            }
        });
    }

    function filterRateListTable() {
        var filter = ($('#rateListSearchInput').val() || '').toUpperCase();
        $('#rate_table_body tr').each(function() {
            if ($(this).hasClass('rate-list-date-row')) {
                return;
            }
            var rowText = $(this).text().toUpperCase();
            $(this).toggle(rowText.indexOf(filter) > -1);
        });
    }

    function formatDateDDMMYYYY(dateStr) {
        if (!dateStr) return '';
        var datePart = dateStr.split(' ')[0];
        var parts = datePart.split('-');
        if (parts.length === 3) return parts[2] + '/' + parts[1] + '/' + parts[0];
        return dateStr;
    }

    function parseDDMMYYYY(dateStr) {
        var parts = dateStr.split('/');
        if (parts.length !== 3) return null;
        return new Date(parts[2], parts[1] - 1, parts[0]);
    }

    function setListDateValue(selector, dateStr) {
        var $el = $(selector);
        $el.val(dateStr);
        var dt = parseDDMMYYYY(dateStr);
        if (dt && $el.data('xdsoft_datetimepicker')) {
            $el.datetimepicker({ value: dt });
        }
    }

    function updateListFilterButtons() {
        $('#showListBtnFilter').show();
        var fromVal = ($('#ListFromDate').val() || '').trim();
        var toVal = ($('#ListToDate').val() || '').trim();
        if (fromVal !== listDefaultFromDate || toVal !== listDefaultToDate) {
            $('#clearListFilterBtn').show();
        } else {
            $('#clearListFilterBtn').hide();
        }
    }

    function onListDateChanged() {
        updateListFilterButtons();
    }

    $('#ListFromDate, #ListToDate').on('change input', function() {
        onListDateChanged();
    });

    $('#showListBtnFilter').click(function() {
        var fromVal = $('#ListFromDate').val();
        var toVal = $('#ListToDate').val();
        if (!fromVal) {
            showToast('warning', 'Please select From Date.');
            return;
        }
        if (!toVal) {
            var d = new Date();
            toVal = ('0' + d.getDate()).slice(-2) + '/' + ('0' + (d.getMonth() + 1)).slice(-2) + '/' + d.getFullYear();
            $('#ListToDate').val(toVal);
        }
        var fromDate = parseDDMMYYYY(fromVal);
        var toDate = parseDDMMYYYY(toVal);
        if (fromDate > toDate) {
            showToast('warning', 'From Date cannot be after To Date.');
            return;
        }
        loadRateMasterData(fromVal, toVal);
    });

    $('#clearListFilterBtn').click(function() {
        setListDateValue('#ListFromDate', listDefaultFromDate);
        setListDateValue('#ListToDate', listDefaultToDate);
        $('#rateListSearchInput').val('');
        updateListFilterButtons();
        loadRateMasterData(listDefaultFromDate, listDefaultToDate);
    });

    $('#rateListModal').on('show.bs.modal', function() {
        updateListFilterButtons();
        loadRateMasterData($('#ListFromDate').val(), $('#ListToDate').val());
    });

    function loadItems() {
        $.ajax({
            url: '<?= admin_url('K1RateMaster/getItems'); ?>',
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                var options = '<option value=""></option>';
                itemDetailsMap = {};
                $.each(data, function(i, item) {
                    var itemId = $.trim(item.ItemID || '');
                    if (itemId) {
                        itemDetailsMap[itemId] = item;
                    }
                    options += '<option value="' + escapeHtml(itemId) + '">' + escapeHtml(item.ItemName || '') + '</option>';
                });
                $('#ItemName').html(options).selectpicker('refresh');
            }
        });
    }

    function escapeHtml(value) {
        return $('<div/>').text(value == null ? '' : value).html();
    }

    function setItemDetailFields(item) {
        if (!item) {
            clearItemFields();
            return;
        }
        $('#HSNCode').val(item.hsn_code || item.HSNCode || '');
        $('#UOM').val(item.unit || item.UOM || item.UnitShortCode || '');
        $('#UnitWtKg').val(item.PackingWeight != null && item.PackingWeight !== '' ? item.PackingWeight : (item.UnitWtKg || ''));
    }

    function applyItemDetails(itemId) {
        itemId = $.trim(itemId || '');
        if (!itemId) {
            clearItemFields();
            return;
        }

        if (itemDetailsMap[itemId]) {
            setItemDetailFields(itemDetailsMap[itemId]);
            return;
        }

        loadItemDetails(itemId);
    }

    function loadItemDetails(itemId) {
        $.ajax({
            url: '<?= admin_url('K1RateMaster/getItemDetails'); ?>',
            type: 'POST',
            data: { ItemID: itemId },
            dataType: 'json',
            success: function(response) {
                setItemDetailFields(response.item || null);
            },
            error: function() {
                clearItemFields();
            }
        });
    }

    function loadCenters() {
        $.ajax({
            url: '<?= admin_url('K1RateMaster/getCenters'); ?>',
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                var options = '';
                $.each(data, function(i, item) {
                    options += '<option value="' + item.CenterID + '">' + item.CenterName + '</option>';
                });
                $('#CenterName').html(options).selectpicker('refresh');
                $('#ImportCenterName').html(options).selectpicker('refresh');
            }
        });
    }

    function clearItemFields() {
        $('#HSNCode').val('');
        $('#UOM').val('');
        $('#UnitWtKg').val('');
    }

    function rowClickHandler(event, rateCode) {
        if ($(event.target).closest('button, a').length > 0) return;
        editRateMaster(rateCode);
    }

    function validateForm() {
        if ($('#ItemName').val() == '') {
            showToast('warning', 'Item Name is required');
            return false;
        }
        if ($('#HSNCode').val() == '') {
            showToast('warning', 'HSN Code is required');
            return false;
        }
        if ($('#UOM').val() == '') {
            showToast('warning', 'UOM is required');
            return false;
        }
        if ($('#UnitWtKg').val() == '') {
            showToast('warning', 'Unit Weight is required');
            return false;
        }
        if (!$('#CenterName').val() || $('#CenterName').val().length === 0) {
            showToast('warning', 'Center Name is required');
            return false;
        }
        if ($('#Rate').val() == '') {
            showToast('warning', 'Rate is required');
            return false;
        }
        if (!/^\d+(\.\d{1,2})?$/.test($('#Rate').val().trim())) {
            showToast('warning', 'Rate must be a valid number (e.g., 10 or 1.20)');
            return false;
        }
        // if (!isWithinFinancialYear($('#EffectiveDate').val().trim())) {
        //     showToast('warning', 'Effective date must be within current financial year (' + fyMinDateDisplay + ' to ' + fyMaxDateDisplay + ').');
        //     return false;
        // }
        return true;
    }

    function saveRateMaster() {
        var centerIds = $('#CenterName').val() || [];
        if (!$.isArray(centerIds)) {
            centerIds = centerIds ? [centerIds] : [];
        }

        var formData = new FormData();
        formData.append('ItemName', $('#ItemName').val());
        $.each(centerIds, function(i, centerId) {
            formData.append('CenterName[]', centerId);
        });
        formData.append('HSNCode', $('#HSNCode').val());
        formData.append('UOM', $('#UOM').val());
        formData.append('UnitWtKg', $('#UnitWtKg').val());
        formData.append('Rate', $('#Rate').val());
        formData.append('Discount', $('#Discount').val());
        formData.append('EffectiveDate', $('#EffectiveDate').val());
        formData.append('<?= $this->security->get_csrf_token_name(); ?>', $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val());

        $('.searchh3').show();
        $.ajax({
            url: '<?= admin_url('K1RateMaster/SaveRateMaster'); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                $('.searchh3').hide();
                if (response.success) {
                    showToast('success', response.message);
                    showStatusMessage('success', response.message);
                    resetForm();
                } else {
                    showToast('error', response.message);
                    showStatusMessage('error', response.message);
                }
            },
            error: function() {
                $('.searchh3').hide();
                showToast('error', 'Error saving Rate Master');
            }
        });
    }

    function updateRateMaster() {
        var rateCode = $('#RateCode').data('edit-code');
        if (!rateCode) {
            showToast('warning', 'No record selected for update');
            return;
        }
        $('.searchh4').show();
        $.ajax({
            url: '<?= admin_url('K1RateMaster/UpdateRateMaster'); ?>',
            type: 'POST',
            data: {
                RateCode: rateCode,
                ItemName: $('#ItemName').val(),
                CenterName: $('#CenterName').val(),
                EffectiveDate: $('#EffectiveDate').val(),
                Rate: $('#Rate').val(),
                Discount: $('#Discount').val()
            },
            traditional: true,
            dataType: 'json',
            success: function(response) {
                $('.searchh4').hide();
                if (response.success) {
                    showToast('success', response.message);
                    showStatusMessage('success', response.message);
                    resetForm();
                } else {
                    showToast('error', response.message);
                    showStatusMessage('error', response.message);
                }
            },
            error: function() {
                $('.searchh4').hide();
                showToast('error', 'Error updating Rate Master');
            }
        });
    }

    function editRateMaster(rateCode) {
        $.ajax({
            url: '<?= admin_url('K1RateMaster/getRateMasterDetail'); ?>',
            type: 'POST',
            data: { RateCode: rateCode },
            dataType: 'json',
            success: function(data) {
                if (!data) {
                    showToast('error', 'Record not found');
                    return;
                }
                isEditMode = true;
                $('#RateCode').val(data.RateCode).data('edit-code', data.RateCode);
                $('#ItemName').val(data.ItemId).selectpicker('refresh');
                setItemDetailFields({
                    hsn_code: data.HSNCode,
                    unit: data.UOM,
                    PackingWeight: data.UnitWtKg
                });

                var effDate = '';
                if (data.EffectiveDate) {
                    var datePart = data.EffectiveDate.split(' ')[0];
                    var parts = datePart.split('-');
                    if (parts.length === 3) effDate = parts[2] + '/' + parts[1] + '/' + parts[0];
                }

                $('#CenterName').val([data.CenterID]).selectpicker('refresh');
                $('#CenterName').prop('disabled', true).selectpicker('refresh');
                $('#EffectiveDate').val(effDate);
                $('#Rate').val(data.Rate);
                $('#Discount').val(data.Discount);
                $('#saveBtn').hide();
                $('#updateBtn').show();
                $('#rateListModal').modal('hide');
                $('html, body').animate({ scrollTop: 0 }, 'slow');
            },
            error: function() {
                showToast('error', 'Error loading record');
            }
        });
    }

    function resetForm() {
        isEditMode = false;
        $('#RateCode').val('').removeData('edit-code');
        $('#ItemName').val('').selectpicker('refresh');
        clearItemFields();
        $('#CenterName').prop('disabled', false).val('').selectpicker('refresh');
        $('#EffectiveDate').val('<?= $default_effective_date; ?>');
        $('#Rate').val('');
        $('#Discount').val('');
        $('#updateBtn').hide();
        $('#saveBtn').show();
    }

    function resetImportForm() {
        $('#ImportCenterName').val('').selectpicker('refresh');
        $('#ImportEffectiveDate').val('<?= $default_effective_date; ?>');
        $('#RateCSVFile').val('');
    }

    function hideImportResult() {
        $('#importResultContainer').hide();
        $('#importResultMessage').html('');
    }

    function resetImportModal() {
        resetImportForm();
        hideImportResult();
    }

    function showImportResult(response) {
        var html = '';
        if (response.success_message) {
            html += '<div class="alert alert-success mbot5">' + escapeHtml(response.success_message) + '</div>';
        }
        if (response.error_message) {
            var alertClass = response.success ? 'alert-warning mbot0' : 'alert-danger mbot0';
            html += '<div class="alert ' + alertClass + '">' + escapeHtml(response.error_message) + '</div>';
        }
        if (!html) {
            hideImportResult();
            return;
        }
        $('#importResultMessage').html(html);
        $('#importResultContainer').show();
    }

    function csvEscape(value) {
        value = String(value == null ? '' : value);
        return '"' + value.replace(/"/g, '""') + '"';
    }

    function downloadSampleCSV() {
        $.ajax({
            url: '<?= admin_url('K1RateMaster/GetItemsData'); ?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                var csv = 'ItemID,ItemName,AssignedRate,Discount\n';
                $.each(response, function(index, item) {
                    csv += [
                        csvEscape(item.ItemID),
                        csvEscape(item.ItemName),
                        '',
                        ''
                    ].join(',') + '\n';
                });

                var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                var link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'K1RateMaster_Sample.csv';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                showToast('success', 'CSV downloaded successfully');
            },
            error: function() {
                showToast('error', 'Failed to load item data');
            }
        });
    }

    function importRateMasterCSV() {
        var centerIds = $('#ImportCenterName').val();
        var effectiveDate = $('#ImportEffectiveDate').val().trim();
        var fileInput = $('#RateCSVFile')[0];
        var file = fileInput.files.length ? fileInput.files[0] : null;

        if (!centerIds || centerIds.length === 0) {
            showToast('warning', 'Please select Center Name for import.');
            return;
        }
        if (!effectiveDate) {
            showToast('warning', 'Please enter Effective On date for import.');
            return;
        }
        // if (!isWithinFinancialYear(effectiveDate)) {
        //     showToast('warning', 'Effective date must be within current financial year (' + fyMinDateDisplay + ' to ' + fyMaxDateDisplay + ').');
        //     return;
        // }
        if (!file) {
            showToast('warning', 'Please choose a CSV file to upload.');
            return;
        }
        if (!/\.csv$/i.test(file.name)) {
            showToast('warning', 'Please upload a CSV file.');
            return;
        }

        var formData = new FormData();
        formData.append('RateCSVFile', file);
        formData.append('EffectiveDate', effectiveDate);
        formData.append('<?= $this->security->get_csrf_token_name(); ?>', $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val());
        $.each(centerIds, function(i, centerId) {
            formData.append('CenterName[]', centerId);
        });

        hideImportResult();

        $.ajax({
            url: '<?= admin_url('K1RateMaster/ImportRateMasterCSV'); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                var statusParts = [];

                if (response.success_message) {
                    showToast('success', response.success_message, 6000);
                    statusParts.push(response.success_message);
                }

                if (response.error_message) {
                    showToast(response.success ? 'warning' : 'error', response.error_message, 8000);
                    statusParts.push(response.error_message);
                }

                if (statusParts.length) {
                    var statusType = response.success ? 'success' : (response.success_message ? 'warning' : 'error');
                    showStatusMessage(statusType, statusParts.join(' '), 10000);
                    showImportResult(response);
                }

                if (response.success) {
                    resetImportForm();
                }

                if (!response.success && !response.success_message) {
                    var errorMsg = response.message || 'Import failed.';
                    showToast('error', errorMsg, 8000);
                    showStatusMessage('error', errorMsg, 8000);
                    showImportResult({
                        success: false,
                        success_message: '',
                        error_message: errorMsg
                    });
                }
            },
            error: function() {
                var errorMessage = 'Error importing CSV file.';
                showToast('error', errorMessage, 8000);
                showStatusMessage('error', errorMessage);
                showImportResult({
                    success: false,
                    success_message: '',
                    error_message: errorMessage
                });
            }
        });
    }
</script>
