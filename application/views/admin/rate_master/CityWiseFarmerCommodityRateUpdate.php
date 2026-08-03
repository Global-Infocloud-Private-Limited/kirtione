<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-city-rate { overflow: auto; max-height: 70vh; width: 100%; }
    .table-city-rate thead th { position: sticky; top: 0; z-index: 1; }
    #city_rate_table { border-collapse: collapse; width: 100%; }
    #city_rate_table td { padding: 1px 5px !important; white-space: nowrap; border: 1px solid !important; font-size: 11px; line-height: 1.2 !important; vertical-align: middle !important; }
    #city_rate_table thead th { padding: 6px 8px !important; white-space: nowrap; border: 1px solid !important; font-size: 12px; line-height: 1.3 !important; vertical-align: middle !important; background: #50607b; color: #fff !important; }
    #city_rate_table th:last-child,
    #city_rate_table td:last-child { min-width: 140px; }
    #city_rate_table .rate-input { width: 100%; min-width: 130px; height: 22px; padding: 1px 4px; font-size: 11px; line-height: 1.2; }
    .center-count-tip { display: inline-block; min-width: 22px; padding: 1px 7px; border-radius: 3px; cursor: help; color: #337ab7; font-weight: 600; transition: background-color 0.2s, color 0.2s; }
    .center-count-tip:hover { background-color: #17a2b8; color: #fff; }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 text-centerr">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                                        <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                        <li class="breadcrumb-item active text-capitalize"><b>Master</b></li>
                                        <li class="breadcrumb-item active" aria-current="page"><b>Kirti Purchase Farmer Rate City Wise</b></li>
                                    </ol>
                                </nav>
                                <hr style="margin-Bottom:12px !important;">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="Commodity" class="control-label">Commodity</label>
                                    <select class="selectpicker" name="Commodity" data-live-search="true" id="Commodity" data-width="100%">
                                        <option value="">Non Selected</option>
                                        <?php foreach ($commodity as $value) { ?>
                                            <option value="<?php echo $value['ItemID']; ?>" data-unit="<?php echo htmlspecialchars(isset($value['unit']) ? $value['unit'] : '', ENT_QUOTES, 'UTF-8'); ?>"><?php echo strtoupper($value['ItemName']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-city-rate">
                                    <table class="table table-striped table-bordered" id="city_rate_table" width="100%">
                                        <thead>
                                            <tr>
                                                <th style="text-align:left;">City</th>
                                                <th style="text-align:center;">Centers Count</th>
                                                <th id="current_rate_header" style="text-align:center;">Current Rate</th>
                                                <th id="new_rate_header" style="text-align:center;">New Rate</th>
                                            </tr>
                                        </thead>
                                        <tbody id="city_rate_body">
                                            <tr id="no_data_row">
                                                <td colspan="4" class="text-center text-muted">Please select a commodity to load cities.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mtop15">
                            <div class="col-md-12">
                                <?php if (has_permission_new('CityWiseFarmerCommodityRate', '', 'edit')) { ?>
                                    <button type="button" class="btn btn-info" id="updateCityRatesBtn">Update</button>
                                <?php } else { ?>
                                    <button type="button" class="btn btn-info disabled">Update</button>
                                <?php } ?>
                                <?php if (has_permission_new('CityWiseFarmerCommodityRate', '', 'print')) { ?>
                                    <button type="button" class="btn btn-warning mleft5" id="printCityRatesBtn" onclick="printCityRateReport();"><i class="fa fa-print"></i> Print</button>
                                <?php } ?>
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
function escapeAttr(value) {
    return String(value || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function formatRateUnitLabel(unit) {
    if (!unit) {
        return '';
    }
    var u = String(unit).trim().toLowerCase();
    if (u === 'quintal') {
        return 'per qtl';
    }
    if (u === 'bags') {
        return 'per bag';
    }
    if (u === 'mt') {
        return 'per MT';
    }
    if (u === 'kgs' || u === 'kg') {
        return 'per kg';
    }
    return 'per ' + String(unit).trim();
}

function updateRateHeaders() {
    var unit = $('#Commodity option:selected').data('unit') || '';
    var unitLabel = formatRateUnitLabel(unit);
    var suffix = unitLabel ? ' (' + unitLabel + ')' : '';
    $('#current_rate_header').text('Current Rate' + suffix);
    $('#new_rate_header').text('New Rate' + suffix);
}

function formatDisplayRate(rate) {
    if (rate === '' || rate === null || rate === undefined || rate === '-') {
        return '-';
    }
    var num = parseFloat(rate);
    if (isNaN(num)) {
        return rate;
    }
    return num.toFixed(2);
}

function sanitizeRateInput(value) {
    value = String(value || '').replace(/[^\d.]/g, '');
    var parts = value.split('.');
    if (parts.length > 2) {
        value = parts[0] + '.' + parts.slice(1).join('');
        parts = value.split('.');
    }
    if (parts.length === 2 && parts[1].length > 2) {
        value = parts[0] + '.' + parts[1].substring(0, 2);
    }
    return value;
}

function isValidRate(value) {
    if (value === '' || value === null) {
        return false;
    }
    if (!/^\d+(\.\d{1,2})?$/.test(value)) {
        return false;
    }
    var rate = parseFloat(value);
    return !isNaN(rate) && rate >= 0;
}

$(document).ready(function() {
    $(document).on('input', '.city-rate-input', function() {
        $(this).val(sanitizeRateInput($(this).val()));
    });

    $('#Commodity').on('change', function() {
        var ItemID = $(this).val();
        var $tbody = $('#city_rate_body');
        updateRateHeaders();

        if (ItemID === '') {
            $('#current_rate_header').text('Current Rate');
            $('#new_rate_header').text('New Rate');
            $tbody.html('<tr id="no_data_row"><td colspan="4" class="text-center text-muted">Please select a commodity to load cities.</td></tr>');
            return;
        }

        $tbody.html('<tr><td colspan="4" class="text-center">Loading cities...</td></tr>');

        $.ajax({
            type: 'POST',
            url: "<?php echo admin_url(); ?>rate_master/GetItemWiseCityFarmer",
            data: { ItemID: ItemID },
            dataType: 'json',
            success: function(data) {
                $tbody.empty();

                if (!data || data.length === 0) {
                    $tbody.html('<tr><td colspan="4" class="text-center text-muted">No cities found for the selected commodity.</td></tr>');
                    return;
                }

                for (var i = 0; i < data.length; i++) {
                    var city = data[i];
                    var currentRate = (city.CurrentRate !== '' && city.CurrentRate !== null && city.CurrentRate !== undefined) ? formatDisplayRate(city.CurrentRate) : '-';
                    var centerNames = city.CenterNames ? city.CenterNames : '';
                    var row = '<tr data-city-id="' + city.CityID + '">' +
                        '<td>' + city.CityName + '</td>' +
                        '<td style="text-align:center;"><span class="center-count-tip" title="' + escapeAttr(centerNames) + '">' + city.CenterCount + '</span></td>' +
                        '<td style="text-align:center;">' + currentRate + '</td>' +
                        '<td style="text-align:center;">' +
                            '<input type="text" class="form-control rate-input city-rate-input" data-city-id="' + city.CityID + '" placeholder="0.00" maxlength="12">' +
                        '</td>' +
                        '</tr>';
                    $tbody.append(row);
                }
            },
            error: function() {
                $tbody.html('<tr><td colspan="4" class="text-center text-danger">Failed to load cities. Please try again.</td></tr>');
            }
        });
    });

    $('#updateCityRatesBtn').on('click', function() {
        var commodity = $('#Commodity').val();
        var cityRates = [];
        var hasInvalidRate = false;

        if (commodity === '') {
            alert('Please select commodity');
            return;
        }

        $('.city-rate-input').each(function() {
            var rate = $.trim($(this).val());
            if (rate === '') {
                return;
            }
            if (!isValidRate(rate)) {
                hasInvalidRate = true;
                return false;
            }
            cityRates.push({
                city_id: $(this).data('city-id'),
                rate: rate
            });
        });

        if (hasInvalidRate) {
            alert('Please enter valid rates with up to 2 decimal places (zero or greater).');
            return;
        }

        if (cityRates.length === 0) {
            alert('Please enter at least one new rate.');
            return;
        }

        $('#updateCityRatesBtn').prop('disabled', true);

        $.ajax({
            url: "<?php echo admin_url(); ?>rate_master/UpdateCityWiseFarmerRate",
            method: 'POST',
            data: {
                Commodity: commodity,
                city_rates: JSON.stringify(cityRates)
            },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    alert_float('success', response.message);
                    $('#Commodity').trigger('change');
                } else {
                    alert_float('warning', response.message);
                }
            },
            error: function() {
                alert_float('danger', 'Failed to update rates. Please try again.');
            },
            complete: function() {
                $('#updateCityRatesBtn').prop('disabled', false);
            }
        });
    });
});

function printCityRateReport() {
    var commodity = $('#Commodity option:selected').text();
    if ($('#Commodity').val() === '') {
        alert('Please select commodity to print report.');
        return;
    }

    var $rows = $('#city_rate_body tr');
    if ($rows.length === 0 || ($rows.length === 1 && $rows.first().find('td[colspan]').length > 0)) {
        alert('No data to print.');
        return;
    }

    var stylesheet = '<style>th,td{padding:4px 6px;border:1px solid #000;font-size:11px;}table{border-collapse:collapse;width:100%;}th{background:#50607b;color:#fff;}</style>';
    var $tableClone = $('#city_rate_table').clone();
    $tableClone.find('.city-rate-input').each(function() {
        var val = $.trim($(this).val());
        $(this).parent().html(val !== '' ? val : '-');
    });

    var heading = '<table width="100%" style="margin-bottom:10px;"><tr><td style="text-align:center;font-weight:bold;font-size:14px;">Kirti Purchase Farmer Rate City Wise</td></tr>';
    heading += '<tr><td style="text-align:center;font-size:12px;">Commodity: ' + commodity + '</td></tr></table>';

    var printData = stylesheet + heading + $tableClone[0].outerHTML;
    var newWin = window.open('');
    newWin.document.write(printData);
    newWin.print();
    newWin.close();
}
</script>
