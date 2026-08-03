<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
	.hidden-button {
    display: none;
	}
</style>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-8">
				<div class="panel_s">
					<div class="panel-body">                        						 
						<div class="row"> 
							<nav aria-label="breadcrumb" >
								<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
									<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
									<li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
									<li class="breadcrumb-item active" aria-current="page"><b>Commision Master</b></li>
								</ol>
							</nav>
							<hr class="hr_style">
							
							<div class="col-md-12">
								<div class="searchh2" style="display:none;">Please wait while fetching data.</div>                                    
								<div class="searchh3" style="display:none;">Please wait while creating new record.</div>
								<div class="searchh4" style="display:none;">Please wait while updating data.</div>
							</div> 
							
							<br>                              
							<div class="col-md-4"> 						
								<div class="form-group" app-field-wrapper="centername">
									<small class="req text-danger">* </small>
									<label for="centername" class="control-label">Center Name</label>
									<select name="centername[]" id="centername" data-actions-box="true" multiple class="selectpicker form-control" data-none-selected-text="None Selected" data-live-search="true">
										<?php
											foreach($centermaster as $center) 
											{						
												echo '<option value="' . $center['CenterID'] . '" 
												data-statsid="' . $center['state'] . '" >' 
												. $center['CenterName'] . 
												'</option>';
											} 
										?>                                                                                                                                    
									</select>
								</div>
							</div>         
							
							<div class="col-md-4">
								<div class="form-group" app-field-wrapper="vendor"> 
									<small class="req text-danger">* </small>                          
									<label for="vendor">Vendor</label>							
									<select name="vendor" id="vendor" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected">
										<option value="">None selected</option>
										<?php
											foreach($trader_list as $vendor) 
											{										
												echo '<option value="' . $vendor['AccountID'] . '" data-partyid="' . $vendor['state'] . '" >' 
												. $vendor['company'] . 
												'</option>';
											} 
										?>    
									</select>							
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group" app-field-wrapper="ItemCode">      
									<small class="req text-danger">* </small>                     
									<label for="ItemCode">Item</label>							
									<select name="ItemCode[]" id="ItemCode" data-actions-box="true" multiple class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected">
										  
									</select>							
								</div>
							</div>
							<div class="clearfix"></div>
							<!--<div class="col-md-3">
								<div class="form-group">
									<label for="CommisionAmt">Commision Amount</label>
									<input type="text" name="CommisionAmt" id="CommisionAmt" class="form-control" value="">
								</div>
							</div>-->
							<div class="col-md-3">
								<div class="form-group">
									<small class="req text-danger"> </small>
									<label for="CommisionPercent">Commision (%)</label>
									<input type="text" name="CommisionPercent" id="CommisionPercent" class="form-control" value="">
									<input type="hidden" name="EditId" id="EditId" class="form-control" value="">
								</div>
							</div>	
						</div>	                             
						
						<div class="clearfix"></div>
						<br>                                   
						
						<div class="row"> 					
							<div class="col-md-12">
								<?php
									if (has_permission_new('CommsionMaster', '', 'create')) {
									?>
									<button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                                    <?php
                                        }else{
									?>
									<button type="button" class="btn btn-info saveBtn2 hidden-button" disabled style="margin-right: 25px;">Save</button> 
                                    <?php
									}
								?>
								<?php
									if (has_permission_new('CommsionMaster', '', 'edit')) {
									?>
									<button type="button" class="btn btn-info updateBtn hidden-button" style="margin-right: 25px;">Update</button> 
                                    <?php
                                        }else{
									?>
									<button type="button" class="btn btn-info updateBtn2 hidden-button" disabled style="margin-right: 25px;">Update</button> 
                                    <?php
									}
								?>                                                 
								
							</div>
						</div>                                       
						<div class="clearfix"></div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="content" style="margin-top: -40px;">
		<div class="row">
			<div class="col-md-8">
				<div class="panel_s">
					<div class="panel-body">                        						 
						<div class="row"> 
					        <div class="searchh5" style="display:none;margin-left:16px;">Please wait while fetching data.</div>     
							<div class="col-md-4"> 						
								<div class="form-group" app-field-wrapper="filtercentername">
									<label for="filtercentername" class="control-label">Center Name</label>
									<select name="filtercentername[]" id="filtercentername" data-actions-box="true" multiple class="selectpicker form-control" data-none-selected-text="None Selected" data-live-search="true">
										<?php
											foreach($centermaster as $center) 
											{						
												echo '<option value="' . $center['CenterID'] . '" 
												data-statsid="' . $center['state'] . '" >' 
												. $center['CenterName'] . 
												'</option>';
											} 
										?>                                                                                                                                    
									</select>
								</div>
							</div>         
							
							<div class="col-md-4">
								<div class="form-group" app-field-wrapper="filtervendor"> 
									<label for="filtervendor">Vendor</label>							
									<select name="filtervendor" id="filtervendor" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected">
										<option value="">None selected</option>
										<?php
											foreach($trader_list as $vendor) 
											{										
												echo '<option value="' . $vendor['AccountID'] . '" data-partyid="' . $vendor['state'] . '" >' 
												. $vendor['company'] . 
												'</option>';
											} 
										?>    
									</select>							
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group" app-field-wrapper="filterItemCode"> 
									<label for="filterItemCode">Item</label>							
									<select name="filterItemCode[]" id="filterItemCode" data-actions-box="true" multiple class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected">
										  
									</select>							
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
						<br>
						<div class="row">
						    <div class="col-md-6">
                                <button type="button" class="btn btn-info" id="search_data">
                                  Search
                                </button>
                            
                                <a class="btn btn-default buttons-excel buttons-html5" id="caexcel" href="#" style="margin-left:5px;">
                                  <span>Export to Excel</span>
                                </a>
                            
                                <a class="btn btn-default" href="javascript:void(0);" onclick="printPage();" style="margin-left:5px;">
                                  Print
                                </a>
                           </div>
                            
                            <div class="col-md-6">
                                <input type="text" id="myInput2" onkeyup="filterFunction2()" placeholder="Search here.." title="Search" class="form-control" style="float: right; width: 50%;">
                            </div>
						</div>
							
					    <div class="clearfix"></div>
						<div class="row">
							<div class="col-md-12">
								<span id="searchh" style="display:none;">Please wait data loading.</span>
								<span id="searchh2" style="display:none;">Please wait exporting data.....</span>
								<div class="commsion load_data">
									
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
    function refreshTable() 
    {
        $.ajax({
            url:  "<?php echo admin_url(); ?>CommisionMaster/CommisionMaster_table_data",
            type: "GET", 
            dataType: "json", 
            success: function(data) {             
				
                var tableBody = $("#table_Commision_List tbody"); 
                tableBody.empty();                 
				
				var sr = 1;
                $.each(data, function(index, value) {
                    var newRow = $("<tr class='get_ItemID' data-id='" + value.id + "'>");
                    newRow.append("<td>" + sr + "</td>");
                    newRow.append("<td>" + value.CenterName + "</td>");
                    newRow.append("<td>" + value.company + "</td>");                    
                    newRow.append("<td>" + value.ProductName + "</td>");                    
                    newRow.append("<td>" + (value.Amount ?? '-') + "</td>");                    
                    newRow.append("<td>" + (value.Percent ?? '-') + "</td>");                    
					sr++;
                    tableBody.append(newRow); 
				});
			},
            error: function(xhr, status, error) {
                console.error("Error occurred while fetching data: " + error);
			}
		});
	}
</script>
<script type="text/javascript">
   $('#CommisionAmt,#CommisionPercent').on('keypress',function (event) {
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
    var input = $(this).val();
    if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
        event.preventDefault();
    }
});
</script>
<script>
	$(document).ready(function() 
	{      
		//save new card name
		$('.saveBtn').on('click',function() 
		{
			let centername = $('#centername').val(); 
			let vendor = $('#vendor').val(); 
			let ItemCode = $('#ItemCode').val(); 
			//let CommisionAmt = $('#CommisionAmt').val().trim(); 
			let CommisionAmt = null;
			let CommisionPercent = $('#CommisionPercent').val().trim();
			
			// Validate multiselects
			if (!centername || centername.length === 0) {
				alert('Please select at least one Center Name.');
				$('#centername').focus();
				return false;
			}
			
			if (!vendor || vendor.length === 0) {
				alert('Please select at least one Vendor.');
				$('#vendor').focus();
				return false;
			}
			
			if (!ItemCode || ItemCode.length === 0) {
				alert('Please select at least one Item Code.');
				$('#ItemCode').focus();
				return false;
			}
			if (empty(CommisionPercent)) {
				alert('Please enter Commission Percent.');
				$('#CommisionPercent').focus();
				return false;
			}
			
			// Validate commission fields
			/*if ((CommisionAmt === '' && CommisionPercent === '') || (CommisionAmt !== '' && CommisionPercent !== '')) {
				alert('Please fill either Commission Amount or Commission Percent — not both.');
				$('#CommisionAmt').focus();
				return false;
			}*/
			
			$.ajax({
				url: "<?php echo admin_url(); ?>CommisionMaster/insertCommisionDetails", 
				type: 'POST', 
				data: {centername:centername,vendor:vendor,ItemCode:ItemCode,CommisionAmt:CommisionAmt,CommisionPercent:CommisionPercent}, 
				dataType: 'json',
				success: function(response) {
					if (response.success) {                   
						alert_float('success', 'Record Created Successfully...');  
						ResetForm();
						
						loadCommissionTable(null, null, null);
					} else {                    
						alert_float('warning', 'Something went wrong...');
					}
				},
				error: function(xhr, status, error) {                
					alert('An error occurred while processing the request');
				}
			});   
			
		});
	
		
		$('#search_data').on('click', function () {
            var centername = $("#filtercentername").val();
            var filtervendor = $("#filtervendor").val();
            var filterItemCode = $("#filterItemCode").val();
            loadCommissionTable(centername, filtervendor, filterItemCode);
        });
		
		function loadCommissionTable(centername, vendor, ItemCode) {
            $.ajax({
                url: "<?php echo admin_url(); ?>CommisionMaster/GetCommisionData",
                dataType: "html",
                method: "POST",
                data: {centername: centername, filtervendor: vendor, filterItemCode: ItemCode},
                beforeSend: function () {
                    $('#searchh').show();
                    $('.load_data').hide();
                },
                complete: function () {
                    $('.load_data').show();
                    $('#searchh').hide();
                },
                success: function (data) {
                    $('.load_data').html(data);
                },
                error: function () {
                    $('.load_data').html('<p style="color:red;">Failed to load data.</p>');
                }
            });
        }
	});
	
	$(document).on('change', '#vendor', function()
	{
		AccountIDs = $(this).val();
		$.ajax({
			url:"<?php echo admin_url(); ?>CommisionMaster/ItemListByVendorID",
			dataType:"JSON",
			method:"POST",
			data:{AccountIDs:AccountIDs},
			beforeSend: function () {
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
			},
			complete: function () {               
                $('.searchh2').css('display','none');
			},
			success:function(data)
            {	
                let ItemList = data;
                $("#ItemCode").children().remove();
                for (var i = 0; i < ItemList.length; i++) {
                    $("#ItemCode").append('<option value="'+ItemList[i]["ProductID"]+'">'+ItemList[i]["ProductName"]+'</option>');
                }
                $('.selectpicker').selectpicker('refresh');
			}
		});
	});
		
	$(document).on('change', '#filtervendor', function()
	{
	    AccountIDs = $(this).val();
		$.ajax({
			url:"<?php echo admin_url(); ?>CommisionMaster/ItemListByVendorID",
			dataType:"JSON",
			method:"POST",
			data:{AccountIDs:AccountIDs},
			beforeSend: function () {
                $('.searchh5').css('display','block');
                $('.searchh5').css('color','blue');
			},
			complete: function () {               
                $('.searchh5').css('display','none');
			},
			success:function(data)
            {	
                let ItemList = data;
                $("#filterItemCode").children().remove();
                for (var i = 0; i < ItemList.length; i++) {
                    $("#filterItemCode").append('<option value="'+ItemList[i]["ProductID"]+'">'+ItemList[i]["ProductName"]+'</option>');
                }
                $('.selectpicker').selectpicker('refresh');
			}
		});
	});
	
	$(document).on('click', '.get_ItemID', function()
	{
		id = $(this).attr("data-id");
		$.ajax({
			url:"<?php echo admin_url(); ?>CommisionMaster/GetCommisionDetailsbyID",
			dataType:"JSON",
			method:"POST",
			data:{id:id},
			beforeSend: function () {
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
			},
			complete: function () {               
                $('.searchh2').css('display','none');
			},
			success:function(data)
            {			
                $('#centername').val(data.CenterID);
                $('#vendor').val(data.AccountID);
                $('#ItemCode').val(data.ItemID);
				$('#CommisionAmt').val(data.Amount); 
				$('#CommisionPercent').val(data.Percent);
				$('#EditId').val(data.id);
				$('#centername').prop('disabled', true).selectpicker('refresh');
				$('#vendor').prop('disabled', true).selectpicker('refresh');
				$('#ItemCode').prop('disabled', true).selectpicker('refresh');
				
                $('.saveBtn').hide();
                $('.updateBtn').show();	
                $('.saveBtn2').hide();
                $('.updateBtn2').show();
			}
		});
		$('#Item_List').modal('hide');
		
		//update brand details
		$('.updateBtn').on('click',function() 
		{        
			EditId= $('#EditId').val(); 
			CommisionAmt = $('#CommisionAmt').val().trim(); 
			CommisionPercent = $('#CommisionPercent').val().trim(); 
			
			// Validate commission fields
			if ((CommisionAmt === '' && CommisionPercent === '') || (CommisionAmt !== '' && CommisionPercent !== '')) {
				alert('Please fill either Commission Amount or Commission Percent — not both.');
				$('#CommisionAmt').focus();
				return false;
			}
			$.ajax({
                url: "<?php echo admin_url(); ?>CommisionMaster/UpdateCommisionDetails", 
                type: 'POST', 
                data: {EditId:EditId,CommisionAmt:CommisionAmt,CommisionPercent:CommisionPercent}, 
                dataType: 'json',
                success: function(response) {
                    if (response.success) {                   
                        alert_float('success', 'Record Updated Successfully...');
                        ResetForm();                                              
						} else {                    
                        alert_float('warning', 'Something went wrong...');    
					}
				},
                error: function(xhr, status, error) {                
                    alert('An error occurred while processing the request');
				}                
			}); 
		});
	});
</script>

<script>
    function ResetForm()
    {
		$('#centername').val(''); 
		$('#vendor').val(''); 
		$('#ItemCode').val(''); 
		$('#CommisionAmt').val(''); 
		$('#CommisionPercent').val('');
		$('#EditId').val('');
		$('#centername').prop('disabled', false).selectpicker('refresh');
		$('#vendor').prop('disabled', false).selectpicker('refresh');
		$('#ItemCode').prop('disabled', false).selectpicker('refresh');
		$("#ItemCode").children().remove();
		$('.selectpicker').selectpicker('refresh');
        $('.saveBtn').show();
        $('.updateBtn').hide();	
        $('.saveBtn2').show();
        $('.updateBtn2').hide();
	}     
</script>

<script type="text/javascript">
    function printPage()
    {
        var centername = $("#filtercentername option:selected").text();
        var vendors = $("#filtervendor option:selected").val();
        var filtervendor = $("#filtervendor option:selected").text();
        var filterItemCode = $("#filterItemCode option:selected").text();
       
		var center;
        if(centername !=="")
        { center = centername;
        }else { center = "All" ; }
		
		var vendor;
        if(vendors !=="")
        { vendor = filtervendor;
        }else { vendor = "All" ; }

		var item;
        if(filterItemCode !=="")
        { item = filterItemCode; }
        else
        { item = "All"; }   		

        var html_filter_name =    $('.report_for').html();
		var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';

		var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+ $('#filtertable').html() +'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">CenterName : ' + center + ', Vendor : ' + vendor + ', Item Name : ' + item + ' </td>';
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
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table_Commision_List");
        tr = table.getElementsByTagName("tr");
		
        for (i = 2; i < tr.length; i++) {           
            tr[i].style.display = "none";           
            
            td = tr[i].getElementsByTagName("td");
            for (j = 0; j < td.length; j++) {
				if (td[j]) {
					txtValue = td[j].textContent || td[j].innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = ""; 
						break; 
					}
				}
			}
		}
	}
	
	function filterFunction2() 
    {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("myInput2");
        filter = input.value.trim();       
        table = document.getElementById("filtertable");
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
    $("#caexcel").click(function () 
    {     
		var centerid = $("#filtercentername").val();
        var vendor = $("#filtervendor").val();
        var ItemCode = $("#filterItemCode").val();
	
		var CenterName = $("#filtercentername option:selected").text();
    	var VendorName = $("#filtervendor option:selected").text();	
        var ItemName = $("#filterItemCode option:selected").text();

        $.ajax({

            url: "<?php echo admin_url(); ?>CommisionMaster/export_filterwiseCommisionList", 

            method: "POST",

            data: {centerid:centerid,vendor:vendor,ItemCode:ItemCode,CenterName:CenterName,VendorName:VendorName,ItemName:ItemName },

            success: function (data) {              

                response = JSON.parse(data);
                window.location.href = response.site_url+response.filename;
            }
        });

    });
</script>

<style>	
    #table_Commision_List td:hover {
	cursor: pointer;
    }
    #table_Commision_List tr:hover {
	background-color: #ccc;
    }
    .table_Commision_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table_Commision_List thead th { position: sticky; top: 0; z-index: 1; }
    .table_Commision_List tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>









