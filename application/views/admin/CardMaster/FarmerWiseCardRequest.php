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
	    <div class="col-md-8">
		<div class="row">
		 <div class="panel_s">
		        <?php
                    $from_date = "01/".date('m')."/".date('Y');
                    $to_date = date('d/m/Y');
                ?> 
        <div class="panel-body">
		    <div class="_buttons">
		        <div class="col-md-12 text-centerr"  >
					<nav aria-label="breadcrumb" >
						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
							<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
							<li class="breadcrumb-item active" aria-current="page"><b>Card Request</b></li>
							
						</ol>
					</nav>
					<hr style="margin-Bottom:12px !important;">
				</div>
                <div class="col-md-2">
                    <?php echo render_date_input('from_date','From',$from_date); ?>
                </div>
                <div class="col-md-2">
                    <?php echo render_date_input('to_date','To',$to_date);  ?>
                </div>         
                
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="ItemID">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <option value="0">Pending</option>
                            <option value="1">Approved</option>
                            <option value="2">Rejected</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="ItemID">
                        <label for="cardtype" class="form-label">Card Type</label>
                        <select name="" id="cardtype" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <?php
                                foreach($Allcards as $value){
                                    ?>
                                        <option value="<?php echo $value['Prefix']; ?>" ><?php echo $value['CardName']; ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3" >
                    <div class="form-group" app-field-wrapper="ItemID">
                        <label for="paymentstatus" class="form-label">Payment Status</label>
                        <select name="" id="paymentstatus" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <option value="Y">Payment Received</option>  
                            <option value="N">Payment Pending</option>                       
                        </select>
                    </div>
                </div>
                
                <div class="clearfix"></div>
                
                <div class="col-md-8">
                    <?php if (has_permission_new('CardRequestList', '', 'view')) {  ?>                
                        <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 10px;margin-right:10px;" id="search_data">Show</button>
                    <?php } else { ?>
                        <button class="btn btn-info pull-left mleft5 search_data" disabled style="margin-top: 10px;margin-right:10px;" id="search_data">Show</button>
                    <?php } ?>

                    <div class="custom_button">
                    <?php if (has_permission_new('CardRequestList', '', 'export')) {
                    ?>
                        <a class="btn btn-default buttons-excel buttons-html5"  style="margin-top: 10px;"  tabindex="0" aria-controls="table-purchase_request" href="#" id="caexcel"><span>Export to excel</span></a>
                    <?php } else {  ?>
                        <a class="btn btn-default buttons-excel buttons-html5" disabled  style="margin-top: 10px;"  tabindex="0" aria-controls="table-purchase_request" href="#" id="caexcel"><span>Export to excel</span></a>
                    <?php } ?>  
                    
                    <?php if (has_permission_new('CardRequestList', '', 'print')) { ?>
                        <a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 10px;margin-left:10px;"  onclick="printPage();">Print</a>
                    <?php } else { ?>
                        <a class="btn btn-default" href="javascript:void(0);" disabled style="margin-top: 10px;margin-left:10px;"  onclick="printPage();">Print</a>
                    <?php } ?>
                    </div>
                </div>
                <div class="col-md-4" style="margin-top:7px;">
                    <input type="text" id="myInput1" onkeyup="myFunction2()" class="form-control" placeholder="Search here.." title="Search" style="float: right;width:100%">
                </div>
            </div>
            
            <div class="clearfix mtop20"></div>
            
            <div class="table-purchase_request tableFixHead2">
              <table class="table-purchase_request tree table table-bordered " id="table-purchase_request" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:center;width:5%;">Sr No.</th>
                        <th style="text-align:center;width:10%;">Requested Date</th>
                        <th style="text-align:center;width:10%;">AccountID</th>
                        <th style="text-align:left;width:45%;">Farmer Name</th>
                        <th style="text-align:center;width:15%;">Card Name</th>
                        <th style="text-align:left;">Payment Status</th>
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
<?php init_tail(); ?>
<script>
    $(document).ready(function()
    {
        $('#search_data').on('click',function()
        {
            var from_date = $("#from_date").val();
            var to_date = $("#to_date").val();
            var status = $("#status").val();
            var cardtype = $("#cardtype").val();
            var paymentstatus = $("#paymentstatus").val();
            $.ajax({
            url:"<?php echo admin_url(); ?>CardMaster/GetFarmerWiseCardRequest",
            method:"POST",
            data:{from_date:from_date, to_date:to_date, status:status, cardtype:cardtype,paymentstatus:paymentstatus},
                beforeSend: function () {
                    $('#filter_data_table').html('');
                },
                success:function(data)
                {                                                   
                    if(data != '')
                    {                       
                        var filtereddata = data;
                        $('#filter_data_table').show();
                        $('#filter_data_table').empty();
                        $.each(filtereddata, function(index, item) 
                        {
                            var Stat ='';
                            if(item.status==0)
                            {  Stat = "Pending"; }
                            else if(item.status==1)
                            { Stat = "Card Generated"; } 
                            else if(item.status==2)
                            { Stat = "Rejected"; }  
                            var paymentstat = '';
                            if(item.PaymentStatus == "Y")
                            { paymentstat = "Received"; }
                            else if(item.PaymentStatus == "N")
                            { paymentstat = "Pending";}
                            
                            var formattedDate = item.TransDate.split(' ')[0].replace(/-/g, '/');
                            var row = '<tr>' +                       
                            '<td style="text-align:center;">' + (index + 1) + '</td>' +
                            '<td style="text-align:center;">' + formattedDate  + '</td>' +
                            '<td style="text-align:center;">' + item.AccountID  + '</td>' +
                            '<td style="text-align:left;">' + item.company + '</td>' +
                            '<td style="text-align:center;">' + item.CardName  + '</td>' + 
                            '<td style="text-align:center;">' + paymentstat  + '</td>' +
                            '<td style="text-align:center;">' + Stat + '</td>' +                                              
                            '</tr>';                   
                        
                            $('#filter_data_table').append(row);
                        });                   
                    }
                    else{
                        $('#filter_data_table').html('<tr><td colspan="7" style="color:red;">No records found...</td></tr>');
                    }
                }
            });
        });
    });
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
        var status = $("#status :selected").val();
        var cardtype = $("#cardtype :selected").val();                 
        $.ajax({
            url:"<?php echo admin_url(); ?>CardMaster/GetCardRequestList",
            method:"POST",
            data:{from_date:from_date, to_date:to_date, status:status,cardtype:cardtype},
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
        heading_data += '<td style="text-align:center;"colspan="3">Farmer Wise Card Request List</td>';
        heading_data += '</tr>';
        /*heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">'+html_filter_name+'</td>';
        heading_data += '</tr>';*/
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
