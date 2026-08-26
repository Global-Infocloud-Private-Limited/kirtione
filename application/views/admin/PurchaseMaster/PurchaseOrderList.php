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
	        <div class="col-md-12">
	            <div class="panel_s">
	                <div class="panel-body">
	                    <div class="row">
	                        <div class="col-md-12">
	                            <nav aria-label="breadcrumb" >
            						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            							<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
            							<li class="breadcrumb-item active" aria-current="page"><b>Purchase Order List</b></li>
            							
            						</ol>
            					</nav>
            					<hr style="margin-Bottom:12px !important;">
	                        </div>
	                    </div>
	                    <div class="row">
	                    <?php
                            $from_date = "01/".date('m')."/".date('Y');
                            $to_date = date('d/m/Y');
                        ?> 
                            <div class="col-md-2">
                                <?php echo render_date_input('from_date','From Date',$to_date); ?>
                            </div>
                            <div class="col-md-2">
                                <?php echo render_date_input('to_date','To Date',$to_date);  ?>
                            </div> 
                            <div class="col-md-3">                            
                                <div class="form-group" app-field-wrapper="AccountID">                        
                                    <label for="centerid" class="control-label">Center Name</label>
                                    <select name="centerid" id="centerid" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">All</option>   			    
            							   <?php                                
            								foreach($centermaster as $center)                                 
            								{                                
            								echo '<option value="' . $center['CenterID'] . '">' . $center['CenterName'] . '</option>';                                
            								}                             
            							   ?>                                                                                            
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" >
                                <div class="form-group" app-field-wrapper="ItemID">
                                    <label for="reporttype" class="form-label">Report Type</label>
                                    <select name="reporttype" id="reporttype" class="selectpicker form-control" data-live-search="true">
                                        <option value="1">Bill Wise</option>
                                        <option value="2">Item Wise</option>   
                                        <!--<option value="3">HSN Wise</option>  --> 
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">                            
                                <div class="form-group" app-field-wrapper="itemid">                        
                                    <label for="itemid" class="control-label">Select Item</label>
                                    <select name="itemid" id="itemid" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true" disabled>
                                        <option value="">All</option> 
            								<?php
            									foreach($products as $item) 
            									{
            									   echo '<option value="' . $item['ProductID'] . '">' . $item['ProductName'] . '</option>';
            									} 
            								?>                                                                                       
                                    </select>
                                </div>
                            </div> 
                            
                            <div class="clearfix"></div>
                            <div class="col-md-2">                            
                                <div class="form-group" app-field-wrapper="AccountID">                        
                                    <label for="entrytype" class="control-label">Entry Type</label>
                                    <select name="entrytype" id="entrytype" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="1">Purchase Invoice</option>   			    
            							<option value="2">Purchase Inward</option>
            							<option value="3">Purchase Order</option> 							
                                    </select>
                                </div>
                            </div>
            				<div class="col-md-3">                            
                                <div class="form-group" app-field-wrapper="AccountID">                        
                                    <label for="AccountID" class="control-label">Select Party</label>
                                    <select name="AccountID" id="AccountID" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">All</option> 
            								<?php
            									foreach($clients as $value) 
            									{
            										echo '<option value="' . $value['AccountID'] . '">' . $value['company']. ' (' . $value['AccountID'] . ')</option>';
            									} 
            								?>                                                                 
                                    </select>
                                </div>
                            </div>
            
                            <div class="col-md-2" >
                                <div class="form-group" app-field-wrapper="ItemID">
                                    <label for="orderstat" class="form-label">Order Status</label>
                                    <select name="orderstat" id="orderstat" class="selectpicker form-control" data-live-search="true">
                                        <option value="">All</option>                          
                                        <option value="F">Completed</option>
                                        <option value="C">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <span id="fetchspan" style="display:none;">please wait data loading</span>
                                <span id="exportspan" style="display:none;">please wait data exporting</span>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-9" style="margin-top:10px;">
                                <?php
                                    if (has_permission_new('PurchOrderList', '', 'view')) {
                                ?>
                                    <button class="btn btn-info pull-left mleft5 search_data" style="margin-right:10px;" id="search_data">Show</button>
                                <?php
                        			}
                                ?>
                				
                				<div class="custom_button">
                				    <?php
                                    if (has_permission_new('PurchOrderList', '', 'export')) {
                                ?>
                                    <a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="table-purchase_request" href="#" id="caexcel"><span>Export to excel</span></a>
                                <?php
                        			}
                                ?>
                                <?php
                                    if (has_permission_new('PurchOrderList', '', 'print')) {
                                ?>
                                    <a class="btn btn-default" href="javascript:void(0);"  style="margin-left:10px;"  onclick="printPage();">Print</a>
                                <?php
                        			}
                                ?>	
                				</div>
                			</div>
                			<div class="col-md-3" style="margin-top:10px;">
                				<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search here.." title="Search" class="form-control" style="float: right;width:100%">
                			</div>
                			<div class="clearfix"></div>
                			<div class="col-md-12">
                			    <div class="table-purchase_request tableFixHead2"  id="first_table_container">
                                    <table class="table-purchase_request tree table table-bordered OrderList" id="OrderList" width="100%">
                                    
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
    
    load_data();
    function load_data(){
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();	   
        var order_status = $("#orderstat :selected").val();
        var AccountID = $("#AccountID").val();        	
        var CenterID  = $("#centerid").val();  
        var ItemID = $("#itemid").val();
        var Report_type = $("#reporttype").val();
		var Entry_type = $("#entrytype").val();
        $.ajax({
            url:"<?php echo admin_url(); ?>PurchaseMaster/GetFilterDataOrderDetails",
            method:"POST",
            data:{from_date:from_date, to_date:to_date,order_status:order_status,
            AccountID:AccountID,CenterID:CenterID,ItemID:ItemID,Report_type:Report_type,Entry_type:Entry_type},
            beforeSend: function() {
                // setting a timeout
                $("#fetchspan").css("display", "block");
            },
            complete: function() {
                $("#fetchspan").css("display", "none");
            },
            success:function(data){
                if(data != ''){
                    $('#OrderList').html(data);
                }else{
                    $('#OrderList').html('<span style="color:red;">No records found...</span>');
                }
            }
        });
    }
    
    $('#search_data').on('click',function(){
        load_data();
    });
    $('#reporttype').on('change', function() {
	    var Report_type = $("#reporttype").val();		
        if(Report_type == 2)
        {
            $('#itemid').prop('disabled', false); 
            $('#itemid').val(''); 
	        $('#itemid').selectpicker('refresh'); 
        }else {           
            $('#itemid').prop('disabled', true);
            $('#itemid').val('');
	        $('#itemid').selectpicker('refresh'); 
        }
    });

});
</script>

<script>
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.querySelector(".table-purchase_request");
        tr = table.getElementsByTagName("tr");

        for (i = 1; i < tr.length; i++) {
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
 </script>
 
<script type="text/javascript">
    function printPage()
    {
		var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();	   
        var order_status = $("#orderstat :selected").val();
		var orderstatustext = $("#orderstat option:selected").text();  
        var AccountID = $("#AccountID").val();   
		var company = $("#AccountID option:selected").text();         	
        var CenterID  = $("#centerid").val();  
		var CenterName = $("#centerid option:selected").text();
		var reporttype;
		var Report_type = $("#reporttype").val();
        if(Report_type == 1)
        { reporttype = "Bill Wise";}
        else if(Report_type == 2)
        { reporttype = "Item Wise";}

		var center;
        if(CenterID !=="")
        { center = CenterName;
        }else { center = "All" ; }

		var party;
        if(AccountID !=="")
        { party = company; }
        else
        { party = "All"; }   

		var Entry_type = $("#entrytype").val();
		var EntryName = $("#entrytype option:selected").text(); 	

        var html_filter_name =    $('.report_for').html();
		var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';

		var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Order Report : From : ' + from_date + ', To : ' + to_date + ', Center Name : ' + center + ',Entry Type : ' + EntryName + ', Report Type : ' + reporttype + ', Party Name : ' + party + ', Order Status : ' + orderstatustext + ' </td>';
        heading_data += '</tr>';  	
        heading_data += '</tbody></table>';
        var print_data = stylesheet+heading_data+tableData
        newWin= window.open("");
        newWin.document.write(print_data);
        newWin.print();
        newWin.close();
    };
</script>

<script>
    $("#caexcel").click(function () 
    {     
		var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();	   
        var order_status = $("#orderstat").val();
        var order_statusText = $("#orderstat option:selected").text();         
        var AccountID = $("#AccountID").val();       
        var Partyname = $("#AccountID option:selected").text();
        var CenterID  = $("#centerid").val();  
        var Centertext  = $("#centerid option:selected").text(); 
        var Report_type = $("#reporttype").val();     
        var ReportTypetext = $("#reporttype option:selected").text();    
        var ItemID = $("#itemid").val();       
        var ItemName = $("#itemid option:selected").text();     
		var Entry_type = $("#entrytype").val();
		var EntryName = $("#entrytype option:selected").text(); 
        $.ajax({
            url: "<?php echo admin_url(); ?>PurchaseMaster/export_PurchaseOrderlist",
            method: "POST",
            data: {order_statusText:order_statusText,Partyname:Partyname,Centertext,Centertext,ReportTypetext:ReportTypetext,ItemName:ItemName,ItemID:ItemID,Report_type:Report_type,AccountID: AccountID, 
                from_date: from_date, to_date: to_date, order_status: order_status, CenterID: CenterID,Entry_type:Entry_type,EntryName:EntryName},
            beforeSend: function() {
                // setting a timeout
                $("#exportspan").css("display", "block");
            },
            complete: function() {
                $("#exportspan").css("display", "none");
            },
            success: function (data) {  
                response = JSON.parse(data);
                window.location.href = response.site_url+response.filename;
            }
        });
    });
</script>

 <script>
    $(document).ready(function(){
    var maxEndDate = new Date('Y/m/d');
    var fin_y = "<?php echo $this->session->userdata('finacial_year')?>";
    
    var year = "20"+fin_y;
    var cur_y = new Date().getFullYear().toString().substr(-2);
    if(cur_y => fin_y){
        var year2 = parseInt(fin_y) + parseInt(1);
        var year2_new = "20"+year2;
        var e_dat = new Date(year2_new+'/03/31');
        var maxEndDate_new = e_dat;
    }else{
        var e_dat2 = new Date(year2+'/03/31');
        var maxEndDate_new = e_dat2;
    }
    
    var minStartDate = new Date(year, 03);
    $('#from_date').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false
    });
    
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
</script>


</body>
</html>
