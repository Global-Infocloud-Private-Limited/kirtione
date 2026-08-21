<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<style>
    .table-daily_report          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.table-daily_report thead th { position: sticky; top: 0; z-index: 1; }
.table-daily_report tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
</style>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            
            <div class="panel_s">
               <div class="panel-body">
                   <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Payment Request List</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
                  
                    <div class="row">
                       <div class="col-md-2">
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
                          <?php echo render_date_input('from_date','from_date',$from_date); ?>
                        </div>
                        <div class="col-md-2">
                          <?php echo render_date_input('to_date','to_date',$to_date); ?>
                        </div>
                        <div class="col-md-2">
                         <div class="form-group">
                             <label>Status</label>
                           <select name="status" id="status" class="form-control">
                               <option value="">Select All</option>
                               <option value="1">Pending</option>
                               <option value="2">Approved</option>
							   <option value="3">Rejected</option>
							   <option value="4">Payment</option>
                           </select>
                           </div>
                        </div>                        
                            <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 19px;" id="search_data">Show</button>
                        </div>
      <!--</div>-->                 
                  
        <div class="clearfix mtop20"></div>
        <div class="row">
            <div class="col-md-6">
                <?php if (has_permission_new('accounting_cd_report', '', 'export')) {
                    ?>
                <a class="btn btn-default buttons-excel buttons-html5"   tabindex="0" aria-controls="table-daily_report" href="#" id="caexcel"><span>Export to excel</span></a>
                <?php } ?>
                
                <?php if (has_permission_new('accounting_cd_report', '', 'print')) {
                    ?>
                <a class="btn btn-default" href="javascript:void(0);"    onclick="printPage();">Print</a>
                <?php } ?>
             
            </div>
             <span id="searchh3" style="display:none;">Please wait exporting data...</span>
            <div class="col-md-6">
                <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search ..." title="Type in a name" style="float: right;">            
            </div>
        </div>
            <div class="table-daily_report tableFixHead2">             
              <table class="tree table table-striped table-bordered table-daily_report tableFixHead2" id="table-daily_report" width="100%">
                  
                <thead>
                 
                    <tr style="display:none;">
                      <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span style="font-size:10px;font-weight:600;">Payment Request List</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                  </tr>
                  <tr>
                    <th id="sl" style="text-align:center;">Sr. <span class="up_starting">  &#8593;</span><span class="down" style="display:none;"> &#8593;</span><span class="up" style="display:none;"> &#8595;</span></th>
                    <th style="text-align:center;">AccountID</th>
					<th style="text-align:center;">Party Name</th>
                    <th style="text-align:center;">Request Date</th>
                    <th style="text-align:center;">Amount</th>
                    <th style="text-align:left;">Status</th>
                    <th style="text-align:center;">Action</th>                 
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>   
            </div>
            <span id="searchh2" style="display:none;">Loading.....</span>
                 
				 <div class="modal fade Item_List" id="Item_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                        <div class="modal-header" style="padding:5px 10px;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Generate Payment</h4>
                        </div>
                        <div class="modal-body" style="padding:0px 5px !important">              
                           <form id="paymentform">
						   
						    <div class="form-group">
								<label for="Accountid">AccountID:</label>
								<span id="Accountid" class="form-control-static"></span>	
							</div>
							
							 <div class="form-group">
								<label for="name">Party Name:</label>
								<span id="name" class="form-control-static"></span>	
							</div>
							
							<div class="form-group">
								<label for="amt">Amount:</label>
								<span id="amt" class="form-control-static"></span>	
							</div>						
							
							<div class="form-group">
                             <label>Payment Mode:</label>
							   <select name="paymode" id="paymode" class="selectpicker form-control">								  
							   </select>
                           </div>						   
						   </form> 
                        </div>
                        <div class="modal-footer" style="padding:0px; display: flex; justify-content: center;">
                            <button type="submit" id="savebtn" class="btn-tr save_detail btn btn-info mleft10">Submit</button>  
                        </div>
                        </div>
                    <!-- /.modal-content -->
                    </div>
                <!-- /.modal-dialog -->
                </div>
				
				
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>

<script>
    function Aprrovereq(id) {
         var isConfirmed = confirm("Are you sure you want to approve this payment request?");
         if (isConfirmed) 
         {  
            $.ajax({
                url: "<?php echo admin_url(); ?>accounting/update_payment_request_status", 
                method: "POST",
                data: {id: id},
                success: function(response) {    
                    var res = JSON.parse(response);
                    if(res.status === 'success') {
                        alert(res.message); 
                    } else {
                        alert(res.message);
                    }
                },
                error: function(error) {                          
                    console.log("There was an error approving the payment request.");
                }
            });
         }
         else {            
            console.log("User cancelled the approval.");
        }
     }
     
     function Rejectreq(id) {
         var isConfirmed = confirm("Are you sure you want to Reject this payment request?");
         if (isConfirmed) 
         {  
            $.ajax({
                url: "<?php echo admin_url(); ?>accounting/reject_payment_request_status", 
                method: "POST",
                data: {id: id},
                success: function(response) {    
                    var res = JSON.parse(response);
                    if(res.status === 'success') {
                        alert(res.message); 
                    } else {
                        alert(res.message);
                    }
                },
                error: function(error) {                          
                    console.log("There was an error approving the payment request.");
                }
            });
         }
         else {            
            console.log("User cancelled the approval.");
        }
     }
     
     function PaymentReq(id){   
	 //$('#Item_List').modal('show');	    
        $.ajax({
            url: "<?php echo admin_url(); ?>accounting/updateto_payment_status", 
            method: "POST", 
            data: {id: id},
            success: function(response) {    
                var res = JSON.parse(response);	
				var PaymentData = res.payment_data;
				var PayModes = res.PaymentModes;
                if(res.status === 'success') { 
                    //alert(res.message); 
					$('#Item_List').modal('show');					
					$('#Item_List').on('shown.bs.modal', function () {
						$('#myInput1').focus();		
						$('#Accountid').text(PaymentData.AccountID); 
						$('#name').text(PaymentData.company);
						$('#amt').text(PaymentData.Amount);
						
						var paymodeSelect = $('#paymode');
						paymodeSelect.empty(); 						
						paymodeSelect.append('<option value="">None Selected</option>');		
						PayModes.forEach(function(mode) {							
							paymodeSelect.append('<option value="' + mode.AccountID + '">' + mode.company + '</option>');
						});						
						paymodeSelect.selectpicker('refresh');
					})		
                } else {
                    alert(res.message);
                }
            },
            error: function(error) {                          
                console.log("There was an error approving the payment request.");
            }
        });                 
     }
	 
  $(document).ready(function()
  { 
	  function load_data(from_date,to_date,status)
	  {
		$.ajax({
		  url:"<?php echo admin_url(); ?>accounting/load_data_Payment_request",
		  dataType:"html",
		  method:"POST",
		  data:{from_date:from_date, to_date:to_date,status:status},
		  beforeSend: function () {
				   
			$('#searchh2').css('display','block');
			$('.table-daily_report tbody').css('display','none');
			
		 },
		  complete: function () {
								
			$('.table-daily_report tbody').css('display','');
			$('#searchh2').css('display','none');
		 },
		  success:function(data){			
			   $('#table-daily_report tbody').html(data);			
		  }
		});
	  }  
     
	 $('#search_data').on('click',function()
	 {
			var from_date = $("#from_date").val();
			var to_date = $("#to_date").val();
			var status = $("#status").val();			
			var msg = "Filter "+from_date +" To " + to_date+", status: "+status;
			
			$(".report_for").text(msg);
			load_data(from_date,to_date,status);				
	 }); 
	 
	 $('#savebtn').on('click', function() 
	 {
		var AccountId = $("#Accountid").text();   
		var Amount = $("#amt").text();  
		var PayMode = $("#paymode").val();  
		
		$.ajax({
                url: "<?php echo admin_url(); ?>accounting/Generate_PaymentLedger", 
                method: "POST",
                data: {AccountId: AccountId,Amount:Amount,PayMode:PayMode},
                success: function(response) {    
                    var res = JSON.parse(response);
                    if(res.status === 'success') {
                        alert(res.message);  
						$('#Item_List').modal('hide');  
                    } else {
                        alert(res.message);
                    }
                },
                error: function(error) {                          
                    console.log("There was an error approving the payment request.");
                }
            });
	 });
  });
</script>
<script src="<?= base_url() ?>public/plugins/jquery.table2excel.js"></script>
 <script>
 
    function myFunction2() 
	 {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table-daily_report");
		tr = table.getElementsByTagName("tr");
		for (i = 2; i < tr.length; i++) {	 		
			alltags = tr[i].getElementsByTagName("td");
			isFound = false;
			for(j=0; j< alltags.length; j++) {
			  td = alltags[j];
			  if (td) { 
				  txtValue = td.textContent || td.innerText;
				  if (txtValue.toUpperCase().indexOf(filter) > -1) {
					  tr[i].style.display = "";
					  j = alltags.length;
					  isFound = true;
				  } 
				}       
			} 
			if(!isFound && tr[i].className !== "header") {
				tr[i].style.display = "none";
			}
	  }
	}	 
 </script>
 <script>
 
$("#caexcel").click(function(){
    var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var credit_debit_type = $("#credit_debit_type").val();
	    var gst_type = $("#gst_type").val();
	    
	    $.ajax({
            url:"<?php echo admin_url(); ?>accounting/export_credit_debit_report",
            method:"POST",
            data:{from_date:from_date, to_date:to_date,credit_debit_type,gst_type},
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
 function printPage(){
    var html_filter_name =    $('.report_for').html();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
    var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
         heading_data += '<tr>';
         heading_data += '<td style="text-align:center;"colspan="3">Payment Request List</td>';
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
    
      function sortTable(f,n){
	var rows = $('#table-daily_report tbody  tr').get();

	rows.sort(function(a, b) {

		var A = getVal(a);
		var B = getVal(b);

		if(A < B) {
			return -1*f;
		}
		if(A > B) {
			return 1*f;
		}
		return 0;
	});

	function getVal(elm){
		var v = $(elm).children('td').eq(n).text().toUpperCase();
		if($.isNumeric(v)){
			v = parseInt(v,10);
		}
		return v;
	}

	$.each(rows, function(index, row) {
		$('#table-daily_report').children('tbody').append(row);
	});
    }
    var f_sl = 1;
    var f_nm = 1;
    $("#sl").click(function(){
      if ( $('.up').css('display') == 'none')
    {
         $(".up_starting").hide()
      $(".up").show()
      $(".down").hide()
    }else{
         $(".up_starting").hide()
        $(".up").hide()
      $(".down").show()
    }
        f_sl *= -1;
        var n = $(this).prevAll().length;
        sortTable(f_sl,n);
    });
    $("#nm").click(function(){
        f_nm *= -1;
        var n = $(this).prevAll().length;
        sortTable(f_nm,n);
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
