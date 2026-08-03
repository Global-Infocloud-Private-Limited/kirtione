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
    
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
		 <div class="panel_s">
        <div class="panel-body">
            <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Misc Reports </b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Customer Enquiry List</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
		    <div class="_buttons">
               
               <div class="col-md-6">
                    <!--<h5 style="font-size:18px;font-weight:bold;margin:15px 0px 0px 0px;">Customer Enquiry List</h5>-->
                    </div>
                    
                <div class="col-md-2">
                    <?php if (has_permission_new('customer_enquiry', '', 'export')) {
                                    ?>
                    <a class="btn btn-default buttons-excel buttons-html2" tabindex="0" aria-controls="table-trial_bal_report" href="#" id="caexcel" style="margin-top: 7px;margin-left:-126px;"><span>Export to Excel</span></a>
                    <?php } ?>
                    <?php if (has_permission_new('customer_enquiry', '', 'print')) {
                                    ?>
                    <a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 10px;margin-left:10px;"  onclick="printPage();">Print</a>
                    <?php } ?>
                 </div>
                     <div class="col-md-4" style="margin-top:7px;">
                      <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search here.." title="Search" style="float: right;width:100%">
                     </div>
               
                
               
                
            </div>
            
            <div class="clearfix"></div>
            
            <div class="table-purchase_request tableFixHead2" style="">
              <table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
                <thead>
                    <tr style="display:none;">
                        <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span style="font-size:10px;font-weight:600;">Order List</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                    </tr>
                    <tr>
                        <th style="text-align:left;">Sr.No</th>
                        <th style="text-align:left;">AccountID</th>
						 <th style="text-align:left;">Full Name</th>
                        <th style="text-align:left;">Customer Type</th>
                        <th style="text-align:left;">Mobile No</th>
                        <th style="text-align:left;">Email ID</th>
                        <th style="text-align:left;">Message</th> 
                        
                    </tr>
                </thead>
                <tbody id="cutomerenquiry_table">
                    
                </tbody>
              </table>   
            </div>
            <span id="searchh2" style="display:none;">Loading.....</span>
            
        </div>
        </div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<style>
    #table-purchase_request td:hover {
    cursor: pointer;
}
#table-purchase_request tr:hover {
    background-color: #ccc;
}
</style>


<script>
    function awaiting(){
        alert("Awaiting Client Approval !");
    }
    function awaiting_for_broker(){
        alert("Awaiting for Broker Approval !");
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
            td = tr[i].getElementsByTagName("td")[0];
            td1 = tr[i].getElementsByTagName("td")[1];
            td2 = tr[i].getElementsByTagName("td")[2];
            td3 = tr[i].getElementsByTagName("td")[3];
            td4 = tr[i].getElementsByTagName("td")[4];
            td5 = tr[i].getElementsByTagName("td")[5];
            td6 = tr[i].getElementsByTagName("td")[6];
            td7 = tr[i].getElementsByTagName("td")[7];
            
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else if(td1){
                    txtValue = td1.textContent || td1.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else if(td2){
                    txtValue = td2.textContent || td2.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }else if(td3){
                    txtValue = td3.textContent || td3.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }else if(td4){
                    txtValue = td4.textContent || td4.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }else if(td5){
                    txtValue = td5.textContent || td5.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }else if(td6){
                    txtValue = td6.textContent || td6.innerText;
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
        }
    }
 </script>
 
<script type="text/javascript">
    function printPage()
    {
        var html_filter_name =    $('.report_for').html();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3"> Cutomer Enquiry List</td>';
        heading_data += '</tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">'+html_filter_name+'</td>';
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
$("#caexcel").click(function(){
    var data_val = "data";
    $.ajax({
        url:"<?php echo admin_url(); ?>misc_reports/export_enquirylist",
        method:"POST",
         data:{data_val:data_val,},
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
$(document).ready(function(){
   var id =  $("#id").val();
        $.ajax({
            url:"<?php echo admin_url(); ?>misc_reports/CustomersEnquiryList",
            dataType:"json",
            method:"POST",
            data:{id:id},
            beforeSend: function () {
                $('#searchh2').css('display','block');
                $('#cutomerenquiry_table').css('display','none');
            },
            complete: function () {
                $('#cutomerenquiry_table').css('display','');
                $('#searchh2').css('display','none');
            },
            success:function(data){
                $('#cutomerenquiry_table').html(data);
				//$('#cutomerenquiry_table').selectpicker("refresh");
               
            }
        });
    
    
});
</script>
</body>
</html>
