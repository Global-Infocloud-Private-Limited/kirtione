<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb" >
							<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
								<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
								<li class="breadcrumb-item active text-capitalize"><b>Transaction </b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>FPO Dispatch Report</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<div class="row">
						    <?php
                                    $fy = $this->session->userdata('finacial_year');
                                    $fy_new  = $fy + 1;
                                    $lastdate_date = '20'.$fy_new.'-03-31';
                                    $firstdate_date = '20'.$fy_new.'-04-01';
                                    $curr_date = date('Y-m-d');
                                    $curr_date_new    = new DateTime($curr_date);
                                    $last_date_yr = new DateTime($lastdate_date);
                                    if($last_date_yr < $curr_date_new){
                                        $to_date = '31/03/20'.$fy_new;
                                        $from_date = '01/03/20'.$fy_new;
                                    }else{
                                        $from_date = "01/".date('m')."/".date('Y');
                                        $to_date = date('d/m/Y');
                                    }
                            ?> 
							<div class="col-md-12">
							    <input type="hidden" name="comp_name" id="comp_name" value="<?php echo $company_detail->company_name;?>">
							    <input type="hidden" name="comp_addr" id="comp_addr" value="<?php echo $company_detail->address;?>">
								<div class="row">
								    <div class="col-md-2">
                                        <?php
                                            echo render_date_input('from_date','From',$from_date);
                                        ?>
                                    </div>
                                    <div class="col-md-2">
                                        <?php
                                            echo render_date_input('to_date','To',$to_date);
                                        ?>
                                    </div>
                                    
                                    <div class="col-md-2">
                                      <div class="form-group">
                                        <label for="fpolist">FPO List</label>
                                        <select name="fpolist" id="fpolist" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                                        <option value=""></option>
                                        <?php
                                            foreach($TraderList as $key=>$val){
                                                ?>
                                                    <option value="<?php echo $val["AccountID"]?>"><?php echo $val["firstname"]." ".$val["lastname"];?></option>
                                                <?php
                                            }
                                        ?>
                                        </select>
                                      </div>
                                    </div>
                                    
                                     <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="CenterID">Center</label>
                                            <select name="CenterID" id="CenterID" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                                                <option value=""></option>
                                            <?php foreach($CenterList as $s) { ?>
                                                <option value="<?php echo html_entity_decode($s['CenterID']); ?>"><?php echo html_entity_decode($s['CenterName']); ?></option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                    </div>
								
									<div class="col-md-2">
                                        <div class="form-group">
                                            <label for="ItemID">ItemID</label>
                                            <select name="ItemID" id="ItemID" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                                                <option value=""></option>
                                            <?php 
                                                foreach($ItemList as $key=>$value){ ?>
                                                    <option value="<?php echo $value['ItemID']; ?>"><?php echo $value['ItemName']; ?></option>
                                            <?php 
                                                }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="statusdispatch">Status</label>
                                            <select name="statusdispatch" id="statusdispatch" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                                                <option value="">ALL</option>
                                                <option value="1">PENDING</option>
                                                <option value="2">INWARD IN PROGRESS</option>
                                                <option value="3">EXIT</option>
                                            </select>
                                        </div>
                                    </div>
                                    
									<div class="col-md-6">
										<div class="custom_button">
											<button class="btn btn-info pull-left search_data" id="search_data" style="font-size:12px;margin-top: 15px;">Show</button>
											<?php if (has_permission_new('FpoDispatch_Report', '', 'export')) {
											?>
											<a class="btn btn-default " tabindex="0" aria-controls="stock_position" href="#" id="caexcel" style="margin-left:5px;margin-top: 15px;"><span>Export to Excel</span></a>
											<?php } ?>
											
											<?php if (has_permission_new('FpoDispatch_Report', '', 'print')) {
											?>
											<a class="btn btn-default" href="javascript:void(0);" onclick="printPage();" style="margin-left:5px;margin-top: 15px;">Print</a>
											<?php } ?>
										</div>
									</div>
									<div class="col-md-6" style="margin-top:1%;">
                                        <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search.." title="Type in a name" style="float: right;">
                                    </div>
									
								</div>
							</div>
							
						</div>
						
						<div class="clearfix"></div>
						<div class="row">
							<div class="col-md-12">
                                <span id="searchh2" style="display:none;">Loading.....</span>
                                <div class="table_purchase_report">
                                    <table class="tree table table-striped table-bordered table_purchase_report" id="table_purchase_report" width="100%">
                              
                                        <thead>
                                            <tr style="display:none;">
                                                <td colspan="10" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                            </tr>
                                            <tr>
                                                <th style="width:7% ">Sr.No</th>
                                                <th style="width:7% ">DIS.No</th>
                                                <th style="width:7% ">Dispatch Date</th>
                                                <th style="width:7% ">PO.No</th>
                                                <th style="width:7% ">FPO Name</th>
                                                <th style="width:7% ">Center Name</th>
                                                <th style="width:7% ">Vehicle No</th>
                                                <th style="width:7% ">FPO Rate</th>
                                                <th style="width:7% ">Item Name</th>
                                                <th style="width:7% ">Farmer Name</th>
                                                <th style="width:7% ">Farmer Rate</th>
                                                <th style="width:7% ">Weight(Qtl)</th>
                                                <!--<th style="width:7% ">Commission Amt</th>-->
                                                <th style="width:7% ">Net Rate</th>
                                                <th style="width:7% ">Amount</th>
                                                <th style="width:7% ">Status</th>
                                                <th style="width:7% ">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
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
<?php init_tail(); ?>
<script>
$(document).ready(function(){
	$('#search_data').on('click',function()
	{
	    var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
		var Fpolist = $("#fpolist").val();
		var Center = $("#CenterID").val();
		var Item = $("#ItemID").val();  
		var statusdispatch = $("#statusdispatch").val();
		$.ajax({
            url:"<?php echo admin_url(); ?>FpoOrder/GetFilterDispatchData",
            dataType:"JSON",
            method:"POST",
            data:{from_date:from_date,to_date:to_date,Fpolist:Fpolist,Center:Center,Item:Item,statusdispatch:statusdispatch},
            beforeSend: function () {
                $('#searchh2').css('display','block');
                $('.table_purchase_report tbody').css('display','none');
            },
            complete: function () {
                $('.table_purchase_report tbody').css('display','');
                $('#searchh2').css('display','none');
            },
            success:function(data){
                var html = '';
                var i=1;
                var orderCounts = {};
                data.forEach(function (row) {
                    if (!orderCounts[row.DispatchID]) {
                        orderCounts[row.DispatchID] = 0;
                    }
                    orderCounts[row.DispatchID]++;
                });
                var rendered = {};
                
                var totalAmt = 0;
                var totalWeight = 0;
                var totalCommissionAmt = 0;

                for(var count = 0; count < data.length; count++)
                {
                    var CommissionAmt = (parseFloat(data[count].FpoRate) - parseFloat(data[count].farmer_rate)) * parseFloat(data[count].weight);
                    
                    if(data[count].FpoStatus == 1)
                    {
                        FpoStatus = "PENDING";
                    }else if(data[count].FpoStatus == 2){
                        FpoStatus = "INWARD IN PROGRESS";
                    }else if(data[count].FpoStatus == 3){
                        FpoStatus = "EXIT";
                    }
                    
                    var url = "<?php echo admin_url(); ?>FpoOrder/FpoOrderDispatch/" + data[count].OrderID + "/" + data[count].DispatchID;
                    var Inwardurl = "<?php echo admin_url(); ?>FpoOrder/FpoInward/" + data[count].OrderID + "/" + data[count].DispatchID;
                    
                    totalWeight += parseFloat(data[count].weight);
                    totalCommissionAmt += CommissionAmt;
                    totalAmt += parseFloat(data[count].NetAmt);
                    
                    html += '<tr>';
                    if (!rendered[data[count].DispatchID]) {
                        var rowspan = orderCounts[data[count].DispatchID];
                        html += '<td style="text-align:center;" rowspan="' + rowspan + '">' + i + '</td>';
                        i++;
                        
                        html += '<td style="text-align:center;" rowspan="' + rowspan + '">'+data[count].DispatchID+'</td>';
                        var date = data[count].Dispatch_Date.substring(0, 10)
                        var date_new = date.split("-").reverse().join("/");
                        html += '<td style="text-align:center;" rowspan="' + rowspan + '">'+date_new+'</td>';
                        html += '<td style="text-align:center;" rowspan="' + rowspan + '">'+data[count].OrderID+'</td>';
                        html += '<td style="text-align:left;" rowspan="' + rowspan + '">'+data[count].company+'</td>';
                        html += '<td style="text-align:left;" rowspan="' + rowspan + '">'+data[count].CenterName+'</td>';
                        html += '<td style="text-align:center;" rowspan="' + rowspan + '">'+data[count].VehicleNo+'</td>';
                        html += '<td style="text-align:center;" rowspan="' + rowspan + '">'+data[count].FpoRate +'</td>';
                        html += '<td style="text-align:left;" rowspan="' + rowspan + '">'+data[count].ItemName+'</td>';
                    }
                    
                    html += '<td style="text-align:left;">'+data[count].farmer_name+'</td>';
                    html += '<td style="text-align:center;">'+data[count].farmer_rate+'</td>';
                    html += '<td style="text-align:center;">'+data[count].weight+'</td>';
                    
                    /*html += '<td style="text-align:center;">'+CommissionAmt.toFixed(2)+'</td>';*/
                    
                    html += '<td style="text-align:center;">'+data[count].NetRate+'</td>';
                    html += '<td style="text-align:right;">'+data[count].NetAmt +'</td>';
                    
                    if (!rendered[data[count].DispatchID]) {
                        var rowspan = orderCounts[data[count].DispatchID];
                        html += '<td style="text-align:left;" rowspan="' + rowspan + '">' + FpoStatus + '</td>';
                        html += '<td style="text-align:center;" rowspan="' + rowspan + '">';
                    <?php
                        if (has_permission_new('FpoOrder_Dispatch', '', 'edit')) {
                    ?>
                        if(data[count].FpoStatus == 1)
                        {
                            html += '<a href="' + url + '" class="btn btn-sm btn-primary" style="margin-right: 5px;" target="_blank" title="Edit Dispatch"><i class="fa fa-edit"></i></a>';
                        }else{
                            html += '<a href="' + url + '" class="btn btn-sm btn-primary disabled" style="margin-right: 5px;" target="_blank" title="Edit Dispatch"><i class="fa fa-edit"></i></a>';
                        }
                    <?php } ?>
                    <?php
                        if (has_permission_new('Fpo_Inward', '', 'create')) {
                    ?>
                        if (data[count].FpoStatus == 3) {
                            html += '<a href="' + Inwardurl + '" class="btn btn-sm btn-info" target="_blank" title="View Inward">View Inward</a> ';
                        } else {
                            html += '<a href="' + Inwardurl + '" class="btn btn-sm btn-success" target="_blank" title="Create Inward">Inward</a> ';
                        }
                    <?php } ?>    
                        html += '</td>';
                        rendered[data[count].DispatchID] = true;
                    }
                    html += '</tr>';
                }
                
                html += '<tr style="font-weight:bold; background:#f2f2f2;">';
                html += '<td colspan="11" style="text-align:right;">TOTAL</td>';
                html += '<td style="text-align:center;">' + totalWeight.toFixed(2) + '</td>';
                 html += '<td></td>';
                html += '<td></td>';
                html += '<td style="text-align:center;">' + totalAmt.toFixed(2) + '</td>';
                html += '<td colspan="2"></td>';
                html += '</tr>';
                
                $('.table_purchase_report tbody').html(html);
            }
        });
	});
		
		$("#caexcel").click(function()
		{
		    var from_date = $("#from_date").val();
	        var to_date = $("#to_date").val();
			var Fpolist = $("#fpolist").val();
			var Center = $("#CenterID").val();
			var Item = $("#ItemID").val();  
			
			var FpoListText = $("#fpolist option:selected").text();
			var CenterText = $("#CenterID option:selected").text();
			var ItemText = $("#ItemID option:selected").text();
		
				$.ajax({
					url:"<?php echo admin_url(); ?>FpoOrder/export_FpoDispatch_Report",
					method:"POST",
					data:{
				        from_date:from_date,to_date:to_date,Fpolist:Fpolist,Item:Item,Center:Center,FpoListText:FpoListText,CenterText:CenterText,
					    ItemText:ItemText
					},
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
		});
	}); 
</script>

<script type="text/javascript">
   function printPage() 
   {
       var from_date = $("#from_date").val();
	   var to_date = $("#to_date").val();
        var comp_name = $("#comp_name").val();
        var comp_addr = $("#comp_addr").val();
        var FpoList = $("#fpolist option:selected").text() || "ALL";
        var CenterName = $("#CenterID option:selected").text() || "ALL";
        var ItemGroup = $("#ItemID option:selected").text() || "ALL";
        
        var tableContent = document.querySelector('.table_purchase_report'); 
        
        var tableHTML = tableContent.innerHTML;
        var stylesheet = `
            <style>
                body { font-size: 12px; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid black; padding: 5px; }
                .hide_in_print { display: none !important; }
            </style>
        `;

        var heading_data = `
            <table>
                <tr><td colspan="9" style="text-align:center;">${comp_name}</td></tr>
                <tr><td colspan="9" style="text-align:center;">${comp_addr}</td></tr>
                <tr><td colspan="9" style="text-align:center;">Filters:- &nbsp;From Date: ${from_date} &nbsp;To Date: ${to_date} &nbsp;FPO Name: ${FpoList} &nbsp;Item Name: ${ItemGroup} &nbsp;Center Name: ${CenterName}</td></tr>
            </table>
        `;
        
        var printContent = `
            ${stylesheet}
            ${heading_data}
            ${tableHTML}
        `;
        
        var newWin = window.open('', '', 'width=900,height=700');
        newWin.document.write('<html><head><title>Print</title></head><body>');
        newWin.document.write(printContent);
        newWin.document.write('</body></html>');
        newWin.document.close();
       
        newWin.onload = function () {
            newWin.focus();
            newWin.print();
            newWin.close();
        };
    }
</script>

<script>
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.trim();       
        table = document.getElementById("table_purchase_report");
        tr = table.getElementsByTagName("tr"); 

        for (i = 2; i < tr.length; i++) 
        {
            tr[i].style.display = "none"; 
            td = tr[i].getElementsByTagName("td"); 

            for (j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;                
                    if (txtValue.toUpperCase().indexOf(filter.toUpperCase()) > -1) {
                        tr[i].style.display = "";  
                        break; 
                    }
                }
            }
        }
   }
</script>

<style>
.table_purchase_report { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
.table_purchase_report thead th { position: sticky; top: 0; z-index: 1; }
.table_purchase_report tbody th { position: sticky; left: 0; }

.table_purchase_report table  { border-collapse: collapse; width: 100%; }
.table_purchase_report th, td { padding: 3px 3px !important; white-space: nowrap;font-size:11px; line-height:1.42857143;vertical-align: middle;}
.table_purchase_report th     { background: #50607b;color: #fff !important; }


#table_purchase_report tr:hover {
    background-color: #ccc;
}

#table_purchase_report td:hover {
    cursor: pointer;
}
</style>
