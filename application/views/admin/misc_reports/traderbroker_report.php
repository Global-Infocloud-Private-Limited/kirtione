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
            <div class="col-md-10">
                <div class="panel_s">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Misc Reports </b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Trader Broker Report</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
                        <div class="row ">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="trader">
                                        <label for="trader" class="form-label">Select Trader</label>
                                        <select name="trader" id="trader" class="selectpicker form-control"
                                            data-width="100%" multiple data-none-selected-text="Non Selected"  data-actions-box = "1"
                                            data-live-search="true">
                                            <!-- <option value="traderAll">All</option> -->
                                            <?php
                                            foreach ($trader as $key => $value) {
                                                ?>
                                                <option value="<?php echo $value['AccountID']; ?>">
                                                    <?php echo $value['company']; ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="broker">
                                        <label for="broker" class="form-label">Select Broker</label>
                                        <select  name="broker" id="broker" class="selectpicker form-control"
                                            data-width="100%" multiple data-none-selected-text="Non Selected"  data-actions-box = "1"
                                            data-live-search="true">
                                            <!-- <option value="brokerAll">All</option> -->
                                            <?php
                                            foreach ($broker as $key => $value) {
                                                ?>
                                                <option value="<?php echo $value['AccountID']; ?>">
                                                    <?php echo $value['company']; ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 20px;"id="search_data">Show</button>
                                </div>
                            </div>
                                        <br>
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <?php if (has_permission_new('traderbroker_report', '', 'export')) {
                                    ?>
                                    <a class="btn btn-default buttons-excel buttons-html2" tabindex="0"
                                        aria-controls="table-trial_bal_report" href="#" id="caexcel"
                                        style="float: left ! important;"><span>Export to Excel</span></a>
                                        <?php } ?>
                                    <?php if (has_permission_new('traderbroker_report', '', 'print')) {
                                ?>
                                    <a class="btn btn-default" href="javascript:void(0);" style="margin-left: 10px;" onclick="printPage();">Print</a>
                                    <?php } ?>
                                </div>
                            

                            </div>
                          

                            <div class="table-traderbroker_report tableFixHead2 row ">

                            </div>
                            <span id="searchh2" style="display:none;">Loading.....</span>
                        
                            <span id="searchh3" style="display:none;">Please wait data exporting...</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>




<?php init_tail(); ?>
<style>
    .tableFixHead2          { overflow: auto;max-height: 45vh;width:100%;position:relative;top: 0px; }
.tableFixHead2 thead th { position: sticky; top: 0; z-index: 1; }
.tableFixHead2 tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }


</style>

 <script>
   $(document).ready(function() {
    $("#trader").change(function() {
        if ($(this).val() != "") {
            $("#broker").prop("disabled", true);
        } else {
            $("#broker").prop("disabled", false);
        }
     });

     $("#broker").change(function() {
        if ($(this).val() != "") {
            $("#trader").prop("disabled", true);
        } else {
            $("#trader").prop("disabled", false);
        }
    }); 

    // if ($("#trader").val() !== "" && $("#broker").val() !== "") {
    //     $("#trader").prop("disabled", false);
    //     $("#broker").prop("disabled", false);
    // }

});
      
</script> 


</script>
  
<script type="text/javascript">
    function printPage() {
        var html_filter_name = $('.report_for').html();
        var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">' + document.getElementsByTagName('table')[0].innerHTML + '</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3"> Trader Broker Report</td>';
        heading_data += '</tr>';
        heading_data += '<tr>';
        // heading_data += '<td style="text-align:center;"colspan="3">' + html_filter_name + '</td>';
        // heading_data += '</tr>';

        heading_data += '</tbody></table>';
        var print_data = stylesheet + heading_data + tableData
        newWin = window.open("");
        newWin.document.write(print_data);
        newWin.print();
        newWin.close();
    };
</script> 

<script>
    function myFunction2() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table-traderbroker_report");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1];
            td1 = tr[i].getElementsByTagName("td")[2];
            td2 = tr[i].getElementsByTagName("td")[3];
            td3 = tr[i].getElementsByTagName("td")[4];
            td4 = tr[i].getElementsByTagName("td")[5];
            
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
$("#caexcel").click(function(){
    var trader = $("#trader").val();
    var broker = $("#broker").val();
    var reportType = '';
    if (trader.length > 0) {
        reportType = 'trader';
    } else if (broker.length > 0) {
        reportType = 'broker';
    }
    $.ajax({
        url:"<?php echo admin_url(); ?>Misc_reports/export_traderbrokerreport",
        method:"POST",
        data: { reportType: reportType, trader: trader, broker: broker },
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


<script>
$('#search_data').on('click', function () {
    var trader = $("#trader").val();
    var broker = $("#broker").val();
    var reportType = '';

    if (trader.length > 0) {
        reportType = 'trader';
    } else if (broker.length > 0) {
        reportType = 'broker';
    }

    if (reportType === "") {
        alert("Please Select the Trader or Broker Field.");
        return false;
    } else {
        $.ajax({
            url: "<?php echo admin_url(); ?>Misc_reports/generateReporttraderbroker",
            dataType: "json",
            method: "POST",
            data: { reportType: reportType, trader: trader, broker: broker },
            beforeSend: function () {
                $('.tableFixHead2').html('');
                $('#searchh22').css('display', 'none');
                $('#searchh2').css('display', 'block');
            },
            complete: function () {
                $('#searchh2').css('display', 'none');
            },
            success: function (data) {
                $('.tableFixHead2').html(data);
            }
        });
    }
});



$('#trader').on('change', function(){
    $('.load_data').css('display','none');
});

$('#broker').on('change', function(){
    $('.load_data').css('display','none');	
});
</script>


