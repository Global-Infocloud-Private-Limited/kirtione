<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.hidden-button {
    display: none;
}

#FeatureTableBody {
    display: none; 
}

#tblcarddetails td:hover {
    cursor: pointer;
}

#tblcarddetails tr:hover {
    background-color: #ccc;
}
</style>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-10">
				<div class="panel_s">
					<div class="panel-body">                        						 
                            <div class="row"> 
                                <nav aria-label="breadcrumb" >
                                    <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                                        <li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                        <li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
                                        <li class="breadcrumb-item active" aria-current="page"><b>FPO Rate Master</b></li>
                                    </ol>
                                </nav>
                                <hr class="hr_style">

                                <div class="col-md-12">
                                    <div class="searchh2" style="display:none;">Please wait while fetching data.</div>                                    
                                    <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                                    <div class="searchh4" style="display:none;">Please wait while updating data.</div>
                                </div> 
                            </div>
                        <?php if (has_permission('Fpo_Rate', '', 'create')) {
                                    ?>
                            <div class="row">
                                <div class="col-md-3">
									<div class="form-group" app-field-wrapper="fpolist">
										<small class="req text-danger">* </small>
										<label for="fpolist" class="form-label">Select FPO</label> 
										<select name="fpolist" id="fpolist" data-actions-box="true" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true">
											<?php
												foreach ($FPOStaffList as $key => $val) {
												?>
												<option value="<?php echo $val['AccountID'];?>" ><?php echo $val['firstname']." ".$val["lastname"];?></option>
												<?php
												}
											?>
										</select>
									</div>
								</div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="CenterID">Center List</label>
                                        <select name="CenterID" id="CenterID" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                                            <option value=""></option>
                                        <?php foreach($CenterList as $s) { ?>
                                            <option value="<?php echo html_entity_decode($s['CenterID']); ?>"><?php echo html_entity_decode($s['CenterName']); ?></option>
                                        <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="ItemID">Item List</label>
                                        <select name="ItemID" id="ItemID" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                                            <option value=""></option>
                                        <?php 
                                            foreach($ItemList as $key=>$value){ ?>
                                                <option value="<?php echo $value['ItemID']; ?>" <?php if($ItemID == $value['ItemID']){ echo 'selected';} ?>><?php echo $value['ItemName']; ?></option>
                                        <?php 
                                            }
                                        ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="rate">Rate / Quintal</label>
                                        <input type="text" name="rate" id="rate" class="form-control" oninput="validateDecimal(this)" value="">
                                    </div>
                                </div>                           
                            
                                <div class="clearfix"></div>
                                <div class="col-md-12">
                                    <?php if (has_permission('Fpo_Rate', '', 'create')) {
                                    ?>
                                        <button type="submit" class="btn btn-info saveBtn">Save</button>
                                    <?php
                                    } else { ?>
                                        <button type="submit" class="btn btn-info saveBtn" disabled style="margin-right: 25px;">Save</button>
                                    <?php } ?>
                                    <button type="submit" class="btn btn-default cancelBtn" >Cancel</button>
                                </div>    
                            </div>
                        <?php } ?>
						<div class="clearfix"></div>			
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-10">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<div class="row">
								    
								    <div class="col-md-3">
                                      <div class="form-group">
                                          <?php $AccountID = $this->session->userdata('role');?>
                                        <label for="fpolistfilter">FPO List</label>
                                        <select name="fpolistfilter" id="fpolistfilter" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                                        <option value=""></option>
                                        <?php
                                            foreach($FPOStaffList as $key=>$val){
                                                ?>
                                                    <option value="<?php echo $val["AccountID"]?>"><?php echo $val["firstname"]." ".$val["lastname"];?></option>
                                                <?php
                                            }
                                        ?>
                                        </select>
                                      </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filterCenter">Center List</label>
                                            <select name="filterCenter" id="filterCenter" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                                                <option value=""></option>
                                            <?php foreach($CenterList as $s) { ?>
                                                <option value="<?php echo html_entity_decode($s['CenterID']); ?>"><?php echo html_entity_decode($s['CenterName']); ?></option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    
								    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="ItemIDfilter">Item List</label>
                                            <select name="ItemIDfilter" id="ItemIDfilter" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                                                <option value=""></option>
                                            <?php 
                                                foreach($ItemList as $key=>$value){ ?>
                                                    <option value="<?php echo $value['ItemID']; ?>" <?php if($ItemID == $value['ItemID']){ echo 'selected';} ?>><?php echo $value['ItemName']; ?></option>
                                            <?php 
                                                }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="statusfilter">Status</label>
                                            <select name="statusfilter" id="statusfilter" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                                                <option value="Y">Active</option>
                                                <option value="N">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="row" style="margin-top: 10px;">
						    <div class="col-md-6">
    							<div class="custom_button">
    								<button class="btn btn-info pull-left search_data" id="search_data" style="font-size:12px;">Show</button>
    								<?php if (has_permission_new('Fpo_Rate', '', 'export')) {
    								?>
    								<a class="btn btn-default " tabindex="0" aria-controls="rate_position" href="#" id="caexcel" style="margin-left: 5px;"><span>Export to Excel</span></a>
    								<?php } ?>
    							</div>
    						</div>
    						
    						<div class="col-md-6">
    						    <input type="text" id="myInput1" onkeyup="myFunction2()"  class="form-control" placeholder="Search.." title="Type in a name" style="float: right;">
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
                                                <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                            </tr>
                                            <tr>
                                                <th style="width:7% ">Sr. No.</th>
                                                <th style="width:8% ">FPO Name</th>
                                                 <th style="width:10% ">Center Name</th>
                                                <th style="width:10% text-align:left;">Item Name</th>
                                                <th style="width:10% text-align:left;">Rate/Quintal</th>                                    
                                                <th style="width:15% text-align:left;">Date</th>
                                                <th style="width:7% text-align:left;">Status</th>
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
    $(document).ready(function() 
    {
        load_data(null, null, null,'Y');
        //save new rate
        $('.saveBtn').on('click',function() 
        {
            fpolist = $('#fpolist').val();
            CenterID = $('#CenterID').val();
            ItemID = $('#ItemID').val();
            rate = $('#rate').val();
            
            if(fpolist == '')
            {
                alert('please select FPO');
                $('#fpolist').focus();
            }else if(CenterID == ''){
                alert('please select Center');
                $('#CenterID').focus();
            }
            else if(ItemID == '')
            {
                alert('please select Item');
                $('#ItemID').focus();
            }
            else if(rate == '')
            {
                alert('please enter Rate');
                $('#rate').focus();
            }
            else
            {
                $.ajax({
                    url: "<?php echo admin_url(); ?>FpoOrder/InsertRateMaster", 
                    type: 'POST', 
                    data: {fpolist:fpolist,CenterID:CenterID,ItemID:ItemID,rate:rate}, 
                    dataType: 'json',
                    success: function(response) {
                        let anySuccess = false;

                        response.forEach(function(item) {
                            if (item.success) {
                                anySuccess = true;
                            }
                        });
                    
                        if (anySuccess) {
                            alert_float('success', 'Record Created Successfully...');
                        } else {
                            alert_float('warning', 'Something went wrong...');
                        }
                        
                        ResetForm();
                        load_data(null, null, null, null);
                    },
                    error: function(xhr, status, error) {                
                        $('#messageContainer').text('An error occurred while processing the request').css('background-color', '#f44336').css('color', 'white').fadeIn();
                    }
                });   
            }    
        });
    
        //cancel the records
        $('.cancelBtn').on('click',function() 
        {
            ResetForm();         
        });   
        
        $('#search_data').on('click', function() {
    		var Fpolist = $("#fpolistfilter").val();
    		var CenterList = $("#filterCenter").val();
    		var ItemID = $("#ItemIDfilter").val(); 
    		var Status = $("#statusfilter").val();
            load_data(Fpolist,CenterList,ItemID,Status);
        });
        
        function load_data(Fpolist,CenterID,ItemID,Status)
        {
            $.ajax({
              url:"<?php echo admin_url(); ?>FpoOrder/GetFilterWiseFpoRateDetails",
              dataType:"JSON",
              method:"POST",
              data:{Fpolist:Fpolist,CenterID:CenterID,ItemID:ItemID,Status:Status},
              beforeSend: function () {
                       
                $('#searchh2').css('display','block');
                $('.table_purchase_report tbody').css('display','none');
                
             },
              complete: function () {
                                    
                $('.table_purchase_report tbody').css('display','');
                $('#searchh2').css('display','none');
             },
              success: function(data){
                var html = '';
                var i = 1;
                for(var count = 0; count < data.length; count++) {
                    if(data[count].Status == "Y")
                    { var Status = "Active";}
                    else{
                        var Status = "Inactive";
                    }
                    html += '<tr>';
                    html += '<td style="text-align:center;">' + i + '</td>';
                    html += '<td style="text-align:left;">' + data[count].firstname + " "+ data[count].lastname + '</td>';
                    html += '<td style="text-align:left;">' + (data[count].CenterName ? data[count].CenterName : '-') + '</td>';
                    html += '<td style="text-align:left;">' + data[count].ItemName + '</td>';
                    html += '<td style="text-align:left;">' + data[count].Rate + '</td>';
                    
                    var date = data[count].Transdate.substring(0, 10);
                    var date_new = date.split("-").reverse().join("/");
                    html += '<td style="text-align:left;">' + date_new + '</td>';
                    html += '<td style="text-align:left;">' + Status + '</td>'; 
                    html += '</tr>';
                    i++;  
                }
                $('.table_purchase_report tbody').html(html);
            }
            });
         }
         
        $("#caexcel").click(function(){
    		var Fpolist = $("#fpolistfilter").val();
    		var CenterID = $("#filterCenter").val();
    		var ItemID = $("#ItemIDfilter").val(); 
    		var Status = $("#statusfilter").val();
    		
            var FpolistText = $("#fpolistfilter option:selected").text();
            var CenterText = $("#filterCenter option:selected").text();
            var ItemText = $("#ItemIDfilter option:selected").text();
            var Statustext = $("#statusfilter option:selected").text();
		
			$.ajax({
				url:"<?php echo admin_url(); ?>FpoOrder/export_RateMaster_Report",
				method:"POST",
				data:{
			        Fpolist:Fpolist,ItemID:ItemID,Status:Status,FpolistText:FpolistText,ItemText:ItemText,CenterText:CenterText,CenterID:CenterID,Statustext:Statustext
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
	
	function validateDecimal(input) {
        input.value = input.value.replace(/[^0-9.]/g, '')     
                                 .replace(/(\..*?)\..*/g, '$1'); 
    }

</script>
<script>
    function ResetForm()
    {
        $('#rate').val(''); 
        $('#fpolist').val('');
        $('#fpolist').selectpicker('refresh');  
        $('#ItemID').val('');
        $('#ItemID').selectpicker('refresh'); 
        
        $('#CenterID').val('');
        $('#CenterID').selectpicker('refresh'); 
       
        $('.saveBtn2').hide();     
        $('.saveBtn').show();  
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
    #table_purchase_report td:hover {
    cursor: pointer;
	}
	#table_purchase_report tr:hover {
    background-color: #ccc;
	}
	
    .table_purchase_report         { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table_purchase_report thead th { position: sticky; top: 0; z-index: 1; }
    .table_purchase_report tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>