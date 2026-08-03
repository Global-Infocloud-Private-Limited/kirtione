<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
.table-purchase_request          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.table-purchase_request thead th { position: sticky; top: 0; z-index: 1; }
.table-purchase_request tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
td:hover {
    cursor: pointer;
}
tr:hover {
    background-color: #ccc;
}
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
		 <div class="panel_s">
		        <?php
                    $from_date = "01/".date('m')."/".date('Y');
                    $to_date = date('d/m/Y');
                ?> 
            <div class="col-md-8">
                <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 text-centerr"  >
    					<nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Soil Test Request</b></li>
    							
    						</ol>
    					</nav>
    					<hr style="margin-Bottom:12px !important;">
    				</div>
    
                    <div class="col-md-3">
                        <?php echo render_date_input('from_date','From',$from_date); ?>
                    </div>
                    <div class="col-md-3">
                        <?php echo render_date_input('to_date','To',$to_date);  ?>
                    </div>   
                    
                    <div class="col-md-3" >
                        <div class="form-group" app-field-wrapper="state">
                            <label for="reqstatus" class="form-label">Status</label>
                            <select name="" id="reqstatus" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                <option value="">All</option>
                                <option value="0">Pending</option>     
                                <option value="1">Approved</option>     
                                <option value="2">Rejected</option>                            
                            </select>
                        </div>
                    </div>        
    
                    <div class="clearfix"></div>
                    <div class="col-md-8">
                        <?php if (has_permission_new('SoilTestRequest', '', 'view')) {
                        ?>
                        <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 10px;margin-right:10px;" id="search_data">Show</button>
                        <?php } ?>
                        <div class="custom_button">
                            <?php if (has_permission_new('SoilTestRequest', '', 'export')) {
                            ?>
                            <a class="btn btn-default buttons-excel buttons-html5"  style="margin-top: 10px;"  tabindex="0" aria-controls="table-purchase_request" href="#" id="caexcel"><span>Export to excel</span></a>
                            <?php } ?>
                            <?php if (has_permission_new('SoilTestRequest', '', 'print')) {
                            ?>
                            <a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 10px;margin-left:10px;"  onclick="printPage();">Print</a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-top:7px;">
                        <input type="text" id="myInput1" onkeyup="myFunction2()" class="form-control" placeholder="Search here.." title="Search" style="float: right;width:100%">
                    </div> 
                
                    <div class="clearfix mtop20"></div>
                    <div class="col-md-12">
                        <div class="table-purchase_request tableFixHead2" >
                          <table class="table-purchase_request tree table table-bordered " id="table-purchase_request" width="100%">
                            <thead>
                                <tr>
                                    <th style="text-align:center;width:5%;">Sr No.</th>
                                    <th style="text-align:center;width:10%;">Requested Date</th>
                                    <th style="text-align:center;width:10%;">AccountID</th>
                                    <th style="text-align:left;width:45%;">Farmer Name</th>
                                    <th style="text-align:center;width:15%;">Card Name</th>
                                    <th style="text-align:center;width:15%;">Status</th>                              
                                </tr>
                            </thead>
                            <tbody id="filter_data_table">
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
</div>
<?php init_tail(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script>
    $(document).ready(function()
    {
        function reloadTable()
        {
            var from_date = $("#from_date").val();
            var to_date = $("#to_date").val();                      
            var Status = $("#reqstatus :selected").val();               
            
            $.ajax({
            url:"<?php echo admin_url(); ?>CardMaster/GateSoilTestRquest_Details",
            method:"POST",
            data:{from_date:from_date, to_date:to_date ,Status:Status},
                beforeSend: function () {
                    $('#filter_data_table').html('');
                },
                success:function(data)
                {                                           
                    if(data != '')
                    {       
                        var request_status = "";                
                        var filtereddata = data;                      
                        $('#filter_data_table').show();
                        $('#filter_data_table').empty();
                        $.each(filtereddata, function(index, item) 
                        {
                            if(item.status == 0)
                            {  request_status = "Pending"; }
                            else if(item.status == 1)
                            { request_status = "Approved";}
                            else if(item.status == 2)
                            { request_status = "Rejected";}                                
                            if(item.CardName){
                                var CarName = item.CardName;
                            }else{
                                var CarName ="";
                            }
                            var row = '<tr>' +                       
                            '<td style="text-align:center;">' + (index + 1) + '</td>' +
                            '<td style="text-align:center;">' + item.formattedDate + '</td>' +
                            '<td style="text-align:center;">' + item.AccountID  + '</td>' +
                            '<td style="text-align:left;">' + item.company  + '</td>' +
                            '<td style="text-align:center;">' + CarName  + '</td>';
                            row += '<td style="text-align:center;">';
                            if(item.status == 1){
                                row += 'Approved';
                            }else{
                                <?php if (has_permission_new('SoilTestRequest', '', 'edit')) {
                                ?>
                                    row += '<select style="font-size: 12px;" class="form-control" onchange="updateStatus(this, ' + item.AccountID + ' , ' + (item.id) + ',' + item.Prefix + ')">' +
                                    '<option value="0" ' + (request_status === 'Pending' ? 'selected' : '') + '>Pending</option>' +
                                    '<option value="1" ' + (request_status === 'Approved' ? 'selected' : '') + '>Approved</option>' +
                                    '<option value="2" ' + (request_status === 'Rejected' ? 'selected' : '') + '>Rejected</option>' +
                                '</select>';   
                                <?php } else{
                                    ?>
                                    row += request_status;
                                <?php
                                }?>
                             
                            }
                            row += '</td></tr>';
                                           
                        
                            $('#filter_data_table').append(row);
                        });                   
                    }
                    else{
                        $('#filter_data_table').html('<span style="color:red;">No records found...</span>');
                    }
                }
            });
        }
        $('#search_data').on('click',function()
        {
            reloadTable();
        });
    });

    function updateStatus(selectElement, accountId,id,prefix) 
    {
        var newStatus = selectElement.value;            

        $.ajax({
        url: "<?php echo admin_url(); ?>CardMaster/Update_Soilreq_Status", 
        method: 'POST',
        data: {
            accountId: accountId,
            newStatus: newStatus,
            id : id,
            prefix : prefix
        },
        success: function(response) {            
            alert_float('success', 'Status Updated Successfully...');
            reloadTable();
        },
        error: function(error) {
            console.error('Error updating status:', error);
        }
        });
    }
</script>

<script>
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table-purchase_request");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) 
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
<script>
    $("#caexcel").click(function()
    {
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        var status = $("#reqstatus :selected").val();  
        
        $.ajax({
            url:"<?php echo admin_url(); ?>CardMaster/GetSoiltestReqList",
            method:"POST",
            data:{from_date:from_date, to_date:to_date, status:status},
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
 
<script type="text/javascript">
    function printPage()
    {
        var html_filter_name =    $('.report_for').html();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Farmer Soil Test Rquest List</td>';
        heading_data += '</tr>';
        heading_data += '</tbody></table>';
        var print_data = stylesheet+heading_data+tableData
        newWin= window.open("");
        newWin.document.write(print_data);
        newWin.print();
        newWin.close();
    };
</script>
</body>
</html>