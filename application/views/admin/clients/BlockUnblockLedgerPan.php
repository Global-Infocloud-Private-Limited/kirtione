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
        <div class="panel-body">
		    <div class="_buttons">
		        <div class="col-md-12 text-centerr"  >
					<nav aria-label="breadcrumb" >
						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
							<li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
							<li class="breadcrumb-item active" aria-current="page"><b>Block Unblock Ledger</b></li>
							
						</ol>
					</nav>
					<hr class="hr_style" style="margin-Bottom:12px !important;">
				</div>
				
				<div class="col-md-3">
					<div class="form-group" app-field-wrapper="Pan">
						<label for="Pan" class="control-label">PAN Number</label>
						<input type="text"  name="Pan" id="Pan" class="form-control"  value="">
					</div>
				</div>
               
                <div class="col-md-2">
					<label for="active">Status</label>
					<select name="active" id="active" class="selectpicker form-control tcs_type" data-none-selected-text="None selected" data-live-search = "true" tabindex="-98">
						<option value="0">Block</option>
						<option value="1">Unblock</option>
					</select>
				</div>
                
                <div class="clearfix"></div>
                
                <div class="col-md-8">
                    <?php if (has_permission_new('BlockUnblock_ledger', '', 'view')) {
					?>
                    <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 10px;margin-right:10px;" id="search_data">Show</button>
                    <?php
						}
					?>
                    <?php if (has_permission_new('BlockUnblock_ledger', '', 'edit')) {
					?>
                    <button class="btn btn-success pull-left search_data" style="margin-top: 10px;margin-right:2px;" id="editstatus">Update</button>
                    <?php
						}
					?>
                </div>
                
                <div class="col-md-2" style="margin-top:8px;float: right">
                    <input type="text" id="myInput1" onkeyup="myFunction2()" class="form-control" placeholder="Search.." title="Search" style="width:100%">
                </div>
                <br>
            </div>
            
            <div class="clearfix mtop10"></div>
            <div class="table-purchase_request tableFixHead2" >
              <table class="table-purchase_request tree table table-bordered " id="table-purchase_request" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:left;">Sr No.</th>
                        <th style="text-align:left;">AccountID</th>
                        <th style="text-align:left;">Party Name</th>
                        <th style="text-align:left;">First Name</th>
                        <th style="text-align:left;">Last Name</th>
                        <th style="text-align:left;">Customer Type</th>
                        <th style="text-align:left;">Gst.No</th>
                        <th style="text-align:left;">Status</th>
                    </tr>
                </thead>
                <tbody id="filter_data_table">
                    
                </tbody>
              </table>   
            </div>
            <span id="search" style="display:none;">Please Wait fetching data</span>
            <span id="search1" style="display:none;">Please Wait exporting data</span>
        </div>
        </div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script>
    $(document).ready(function(){
        $('#search_data').on('click',function(){
            var Pan = $('#Pan').val();
            if (Pan === '') {
                alert('Please enter a PAN number.');
                $('#filter_data_table').html(''); 
                return; 
            }
    	    $.ajax({
                url:"<?php echo admin_url(); ?>clients/GetPanWiseDetails",
                method:"POST",
                data:{Pan:Pan},
                beforeSend: function () {
                    $('#filter_data_table').html('');
                    $('#search').css('display','block');
                },
                complete: function () {
                    $('#search').css('display','none');
                },
                success:function(data){
                    if(data != ''){
                        $('#filter_data_table').html(data);
                    }
                    else{
                        $('#filter_data_table').html('<span style="color:red;">No records found...</span>');
                    }
                }
            });
        });
        
        $('#Pan').blur(function(){
            var Pan = $('#Pan').val();
    		$.ajax({
    			url:"<?php echo admin_url(); ?>clients/CheckPanExist",
    			method:"POST",
    			dataType:'json',
    			data:{Pan:Pan},
    			beforeSend: function () {
    				$('.searchh6').css('display','block');
    				
    				$('.searchh6').css('color','blue');
    			},
    			complete: function () {
    				$('.searchh6').css('display','none');
    			},
    			success:function(data){
    				if(data == true){
    					alert("This Pan Number Is Not Registered");
    					$('#Pan').val('');
    					$('#Pan').focus();
    				}
    			}
    		});
        });
        
        $('#editstatus').on('click',function(){
            var Pan = $('#Pan').val();
            var Status = $('#active').val();
            if (Pan === '') {
                alert('Please enter a PAN number.');
                $('#filter_data_table').html(''); 
                return; 
            }
            
            $.ajax({
                url:"<?php echo admin_url(); ?>clients/UpdateStatusPan",
                method:"POST",
                dataType:'json',
                data:{Pan:Pan,Status:Status},
                beforeSend: function () {
                    $('#filter_data_table').html('');
                    $('#search').css('display','block');
                },
                complete: function () {
                    $('#search').css('display','none');
                },
                success:function(data){
                    if(data == true){
    					alert("Status Updated Successfully.");
    					$('#Pan').val('');
    					$('select[name=active]').val('0');
		                $('.selectpicker').selectpicker('refresh');
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

</body>
</html>
