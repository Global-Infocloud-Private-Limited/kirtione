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
		    <div class="_buttons">
                <?php
                    $selected_company = $this->session->userdata('root_company');
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

                <div class="col-md-12 text-centerr"  >
					<nav aria-label="breadcrumb" >
						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
							<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
							<li class="breadcrumb-item active" aria-current="page"><b>Daily Sell Trade</b></li>
							
						</ol>
					</nav>
					<hr class="hr_style" style="margin-Bottom:12px !important;">
				</div>
                
                <div class="col-md-7">
                    <h5 style="font-size:18px;font-weight:bold;margin:15px 0px 0px 0px;">Date : <?php echo date('d/m/Y')?></h5>
                </div>
                
                <div class="col-md-1">
                    <?php if (has_permission_new('Sell_Booking', '', 'export')) {
                    ?>
                    <a class="btn btn-default buttons-excel buttons-html2" tabindex="0" aria-controls="table-trial_bal_report" href="#" id="caexcel" style="margin-top: 7px;margin-left:-126px;"><span>Export to Excel</span></a>
                    <?php } ?>
                    
                    <?php if (has_permission_new('Sell_Booking', '', 'print')) {
                     ?>
                    <a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 10px;margin-left:10px;"  onclick="printPage();">Print</a>
                    <?php } ?>
                    </div>
                <div class="col-md-4" style="margin-top:7px;">
                    <input type="text" id="myInput1" onkeyup="myFunction2()" class="form-control" placeholder="Search here.." title="Search" style="float: right;width:100%">
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
                        <th style="text-align:left;">Company Name</th>
                        <th style="text-align:left;">Date & Time</th>
                        <th style="text-align:left;">Location</th>
                        <th style="text-align:left;">Item Name</th>
                        <th style="text-align:left;">Rate</th>
                        <th style="text-align:left;">Quantity</th>
                        <th style="text-align:left;">Action</th>
                        <th style="text-align:left;">Broker Name</th> 
                        <th style="text-align:left;">Party Name</th>
                        <th style="text-align:left;">Status</th>
                    </tr>
                </thead>
                <tbody id="table-purchase_request-body">
                    
                </tbody>
              </table>   
            </div>
            <span id="searchh2" style="display:none;">Loading.....</span>
            
            <div class="modal fade" id="modifyModal" tabindex="-1" role="dialog" aria-labelledby="modifyModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="padding:5px 10px;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Modify Trade</h4>
                    </div>
                  <div class="modal-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>BookingID</td>
                                <th>Item Name</td>
                                <th>Party Type</td>
                                <th>Party Name</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" id="modal_BookingID" class="form-control" style="border:none;background-color:#fff;" readonly></td>
                                <td><input type="text" id="modal_item" class="form-control" style="border:none;background-color:#fff;" readonly></td>
                                <td><input type="text" id="modal_party_type" class="form-control" style="border:none;background-color:#fff;" readonly></td>
                                <td><input type="text" id="modal_party" class="form-control" style="border:none;background-color:#fff;" readonly> </td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="modal_quantity" class="form-label">Quantity</label>
                            <input type="text" id="modal_quantity" class="form-control">
                            <input type="hidden" id="old_moder_quantity" class="form-control">
                            
                        </div>
                        <div class="col-md-4">
                            <label for="modal_unit" class="form-label">Unit</label>
                            <select name="modal_unit" id="modal_unit" class="selectpicker form-control" data-live-search="true">
                                <option value="Bags">Bags</option>
                                <option value="Quintal">Quintal</option> 
                                <option value="MT">MT</option> 
                            </select>
                        </div>  
                        <div class="col-md-3" style="margin-top:2%">
                            <button type="button" id="Modify" class="btn btn-primary">Modify</button>
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
    #table-purchase_request td:hover {
    cursor: pointer;
}
#table-purchase_request tr:hover {
    background-color: #ccc;
}
</style>
<script>
    function acceptSaleTrade(BookingID)
    {
      $.ajax({
            url:"<?php echo admin_url(); ?>order/AcceptSaleTrade",
            dataType:"json",
            method:"POST",
            data:{
                BookingID:BookingID,
            },
            success: function(data){
                if(data == true){
	                fetchEnquiries();
                }else{
                    alert('please accept or reject previous trade');
                }
            }
      });
    };
</script>
<script>
    function rejectSaleTrade(BookingID)
    {
       $.ajax({
            url:"<?php echo admin_url(); ?>order/RejectSaleTrade",
            dataType:"json",
            method:"POST",
            data:{
                BookingID:BookingID,
            },
            success: function(data){
                if(data == true){
            	    fetchEnquiries();
                }else{
                    alert('please accept or reject previous trade');
                }
            }
       });
    };
</script>
<script>
    function modifyTrade(BookingID)
    {
        $.ajax({
            url:"<?php echo admin_url(); ?>order/getModalData",
            dataType:"json",
            method:"POST",
            data:{
                BookingID:BookingID,
            },
            success: function(data)
            {
                if(data == false){
                    alert('please accept or reject previous trade');
                }else{
                    $('#modal_BookingID').val(data.BookingID);
                    $('#modal_item').val(data.ItemName);
                    
                    if(data.CustomerType == '1'){
                        var party_type = 'Farmer';
                    }
                    if(data.CustomerType == '2'){
                        var party_type = 'Broker';
                    }
                    if(data.CustomerType == '3'){
                        var party_type = 'Trader';
                    }
                    if(data.CustomerType == '4'){
                        var party_type = 'Corporate/Processor';
                    }
                    
                    $('#modal_party_type').val(party_type);
                    if((data.firstname != null) && (data.lastname != null)){
                        $('#modal_party').val(data.firstname+' '+data.lastname);
                    }
                    if((data.firstname != null) && (data.lastname == null)){
                        $('#modal_party').val(data.firstname);
                    }
                    if((data.firstname == null) && (data.lastname != null)){
                        $('#modal_party').val(data.lastname);
                    }
                    if(data.e_quantity == '' || data.e_quantity == null){
                        var qty = data.quantity;
                    }else{
                        var qty = data.e_quantity;
                    }
                    $('#modal_quantity').val(qty);
                    $('#old_moder_quantity').val(qty);
                    $('#modal_unit').val(data.unit).selectpicker('refresh');
                    $('#modifyModal').modal('show');
                    
                    $('#Modify').click(function(){
                        var modal_BookingID = $('#modal_BookingID').val();
                        var old_quantity = $('#old_moder_quantity').val();
                        var modal_quantity = $('#modal_quantity').val();
                        var modal_unit = $('#modal_unit').val();
                        if(parseFloat(modal_quantity) > parseFloat(old_quantity)){
                            alert('Please Enter Quantity lesser than current quantity');
                        }else{
                            $.ajax({
                                
                                url:"<?php echo admin_url(); ?>order/ModifyTrades",
                                dataType:"json",
                                method:"POST",
                                data:{
                                    modal_BookingID:modal_BookingID,
                                    modal_quantity:modal_quantity,
                                    modal_unit:modal_unit,
                                },
                                success: function(res){
                                    if(res == true){
                                        $('#modifyModal').modal('hide');
                                        $('#modal_BookingID').val('');
                                        $('#old_moder_quantity').val('');
                                        $('#modal_quantity').val('');
                                        $('#modal_unit').val('');
                    	                fetchEnquiries();
                                    }
                                }
                            });   
                        }
                    });
                }
            }
        });
    };
</script>
<script>
    function awaiting(){
        alert("Awaiting Client Approval !");
    }
    function awaiting_for_broker(){
        alert("Awaiting for Broker Approval !");
    }
</script>

<script>

    // Function to fetch new enquiries from the server
    function fetchEnquiries() {
        // Create a new XMLHttpRequest object
        var xhr = new XMLHttpRequest();
        // Configure the AJAX request
        xhr.open('GET', '<?php echo admin_url(); ?>order/Get_Sale_request', true);
        // Set up the callback function to handle the server response
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Update the enquiry-container div with the server response
                document.getElementById('table-purchase_request-body').innerHTML = xhr.responseText;
            }
        };
        // Send the AJAX request
        xhr.send();
    }
    // Periodically call fetchEnquiries to update the enquiries without page reload
        setInterval(fetchEnquiries, 10000); // Update every 5 seconds (adjust as needed)
    
</script>

<script>
    $(document).ready(function(){
        fetchEnquiries();
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
            td = tr[i].getElementsByTagName("td")[0];
            td1 = tr[i].getElementsByTagName("td")[1];
            td2 = tr[i].getElementsByTagName("td")[2];
            td3 = tr[i].getElementsByTagName("td")[3];
            td4 = tr[i].getElementsByTagName("td")[4];
            td5 = tr[i].getElementsByTagName("td")[5];
            td6 = tr[i].getElementsByTagName("td")[6];
            td7 = tr[i].getElementsByTagName("td")[7];
            td8 = tr[i].getElementsByTagName("td")[8];
            td9 = tr[i].getElementsByTagName("td")[9];
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
                }else if(td7){
                    txtValue = td7.textContent || td7.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }else if(td8){
                    txtValue = td8.textContent || td8.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }else if(td9){
                    txtValue = td9.textContent || td9.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }else{
                    tr[i].style.display = "none";
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
        heading_data += '<td style="text-align:center;"colspan="3">Sell Request List</td>';
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
<script>
$("#caexcel").click(function(){
    var data_val = "data";
    $.ajax({
        url:"<?php echo admin_url(); ?>order/export_selltrader",
        method:"POST",
        data:{data_val:data_val},
        success:function(data){
            response = JSON.parse(data);
            window.location.href = response.site_url+response.filename;
        }
    });
});


</script> 
</body>
</html>
