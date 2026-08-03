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
                                <?php echo render_date_input('from_date', 'From Date', $from_date); ?>
                            </div>

                            <div class="col-md-2">
                                <?php echo render_date_input('to_date', 'To Date', $to_date); ?>
                            </div>                         
                            <div class="col-md-3">                               
                               <label for="centername">Center Name</label>
                               <select name="centername" id="centername" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                    <option value="">All</option> 
                                    <?php foreach ($centermaster as $val): ?>
                                        <option value="<?php echo $val["CenterID"]; ?>"><?php echo $val["CenterName"]; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>                     
                           	</div>
							<div class="row ">
                            <div class="col-md-6" style="margin-top:5px;">
                                <?php
                                    if (has_permission_new('DemandListReport', '', 'view')) {
                                ?>
                                    <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 10px;"
                                        id="search_data">Show</button>
                                <?php
                                    }
                                ?>
                                <?php
                                    if (has_permission_new('DemandListReport', '', 'export')) {
                                ?>
                                    <a class="btn btn-default buttons-excel buttons-html5" style="margin-top: 10px;margin-left:10px;" tabindex="0" aria-controls="table-purchase_request" href="#" id="caexcel"><span>Export to excel</span></a>
                                <?php
                                    }
                                ?>
                                <?php
                                    if (has_permission_new('DemandListReport', '', 'print')) {
                                ?>
                                    <a class="btn btn-default" href="javascript:void(0);"
                                    style="margin-top: 10px;margin-left:10px;" onclick="printPage();">Print</a>
                                <?php
                                    }
                                ?>
                            </div>
        					<div class="col-md-3" style="margin-left:215px;">
        					    <br>
                                <input type="text" class="form-control" id="myInput1" onkeyup="myFunction2()"
                                    placeholder="Search.." title="Type in a name" style="float: right;">
                            </div>
                        </div>
                  
                        <hr class="hr-panel-heading" />
                        <div class="row">
                            <div class="col-md-6"></div>
                        </div>
                        <div class="table-daily_report tableFixHead2 row">

                        </div>
                        <span id="searchh2" style="display:none;">Loading.....</span>
                        <span id="searchh3" style="display:none;">Please wait data exporting...</span>
						
						<div class="col-md-12">
    						<div class="table_purchase_report">
    							<table class="tree table table-striped table-bordered table_purchase_report" id="table_purchase_report" width="100%">
    								<thead>
    									<tr style="display:none;">
    										<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
    									</tr>
    									<tr>
    										<th style="width:8% ">Sr.No</th>
    										<th style="width:10% text-align:left;">Date</th> 
    										<th style="width:10% ">Center Name</th>
    										<th style="width:15% text-align:left;">Item Name</th>
    										<th style="width:10% text-align:left;">Qty</th> 	
    									    
    									</tr>
    								</thead>
    								<tbody>
    								</tbody>
    							</table>   
    						</div>
    						<span id="searchh2" style="display:none;">Loading.....</span>
    					</div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail() ?>
<script src="<?= base_url() ?>public/plugins/jquery.table2excel.js"></script>

<script>
	function load_data(from_date,to_date,centername)
	{
		$.ajax({
			url:"<?php echo admin_url(); ?>PurchaseMaster/load_data_for_demandReport",            
			method:"POST",
			data:{from_date:from_date, to_date:to_date,centername:centername},
			beforeSend: function () {
				$('#searchh2').css('display','block');
				$('.table_purchase_report tbody').css('display','none');
			},
			complete: function () {
				$('.table_purchase_report tbody').css('display','');
				$('#searchh2').css('display','none');
			},
			success:function(data){
				$('.table_purchase_report tbody').html(data);
			}
		});
	}
	
	function myFunction2() 
    {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.querySelector(".table_purchase_report");
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
	
    $('#search_data').on('click',function(){
		var from_date = $("#from_date").val();
		var to_date = $("#to_date").val();	  
		var centername = $("#centername").val();
		
		load_data(from_date,to_date,centername);
	});
</script>

<script type="text/javascript">
    function printPage() 
    {
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        var centername = $("#centername").val();
    
        var companyName = "<?php echo addslashes($company_detail->company_name); ?>";
        var companyAddress = "<?php echo addslashes($company_detail->address); ?>";
    
        var tableBodyHtml = $(".table_purchase_report tbody").html();
    
        if (!tableBodyHtml.trim()) {
            alert("No data to print!");
            return;
        }
   
        var parts = [];
        if (from_date) parts.push('From Date: ' + from_date);
        if (to_date) parts.push('To Date: ' + to_date);
        if (centername) parts.push('Center Name: ' + centername);
        var filterString = parts.join(' , ');

        var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';

        var tableHtml = `
            <table border="1" cellpadding="0" cellspacing="0" width="100%" style="font-size:12px;">
                <thead>
                    <tr>
                        <th>ID</th><th>Center Name</th><th>Item Name</th><th>Qty</th><th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableBodyHtml}
                </tbody>
            </table>`;

        var headingHtml = `
            <table border="1" cellpadding="0" cellspacing="0" width="100%" style="font-size:12px;">
                <tbody>
                    <tr><td style="text-align:center;" colspan="5">${companyName}</td></tr>
                    <tr><td style="text-align:center;" colspan="5">${companyAddress}</td></tr>
                    <tr><td style="text-align:center;" colspan="5">Demand List Report</td></tr>
                    <tr><td style="text-align:center;" colspan="5">${filterString}</td></tr>
                </tbody>
            </table>`;

        var printContent = stylesheet + headingHtml + tableHtml;

        var newWin = window.open("");
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
</script>

<script>
    $("#caexcel").click(function () 
    {        
        var from_date = $("#from_date").val();      
        var to_date = $("#to_date").val();        
        var centername = $("#centername").val();
        var centername_text = $("#centername option:selected").text();
        $.ajax({
            url: "<?php echo admin_url(); ?>PurchaseMaster/export_DemandList",
            method: "POST",
            data: { from_date: from_date, to_date: to_date,centername:centername,centername_text:centername_text},
            success: function (response) {
                 response = JSON.parse(response);
                window.location.href = response.site_url + response.filename;
            }
        });
    });
</script>

</body>
</html>

<style>
    #table-daily_report td:hover {
    cursor: pointer;
}
#table-daily_report tr:hover {
    background-color: #ccc;
}
</style>