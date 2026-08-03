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
                                        <li class="breadcrumb-item active" aria-current="page"><b>Pincode Master</b></li>
                                    </ol>
                                </nav>
                                <hr class="hr_style">

                                <div class="col-md-12">
                                    <div class="searchh2" style="display:none;">Please wait while fetching data.</div>                                    
                                    <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                                    <div class="searchh4" style="display:none;">Please wait while updating data.</div>
                                </div> 

                                <br>  
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="pinid">Pincode</label>
                                        <input type="text" name="PincodeId" id="pinid" class="form-control" value="">
                                    </div>
                                </div>	

                                <!-- <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="state">State</label>                                        
                                        <select name="State" id="state" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                            <option value=""></option> 
                                            <?php
                                            // foreach($AllPincode as $state) 
                                            // {
                                            //     echo '<option value="' . $state['State'] . '">' . $state['State'] . '</option>';
                                            // } 
                                            ?>                                                                      
                                        </select>
                                    </div>
                                </div>	 -->

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="state">State</label>
                                        <input type="text" name="State" id="state" class="form-control" value="" readonly>
                                        <input type="hidden" name="state_id" id="state_id" class="form-control" value="">
                                    </div>
                                </div>	

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="district">District</label>
                                        <input type="text" name="District" id="district" class="form-control" value="" readonly>
                                        <input type="hidden" name="disctrict_id" id="disctrict_id" class="form-control" value="">
                                    </div>
                                </div>	

                                <!-- <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="district">District</label>                                        
                                        <select name="District" id="district" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                            <option value=""></option> 
                                            <?php
                                            // foreach($AllPincode as $district) 
                                            // {
                                            //     echo '<option value="' . $district['District'] . '">' . $district['cityname'] . '</option>';
                                            // } 
                                            ?>                                                                      
                                        </select>
                                    </div>
                                </div>	 -->

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="taluka">Taluka</label>
                                        <input type="text" name="Taluka" id="taluka" class="form-control" value="" readonly>
                                        <input type="hidden" name="taluka_id" id="taluka_id" class="form-control" value="">
                                    </div>
                                </div>

                                <!-- <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="taluka">Taluka</label>
                                        <select name="Taluka" id="taluka" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                            <option value=""></option> 
                                            <?php
                                            // foreach($AllPincode as $taluka) 
                                            // {
                                            //     echo '<option value="' . $taluka['Taluka'] . '">' . $taluka['talukaname'] . '</option>';
                                            // } 
                                            ?>                                                                      
                                        </select>
                                    </div>
                                </div>	 -->
                            </div>	                             
                            
                            <div class="clearfix"></div>
                            <br>                                   
						
                            <div class="row"> 					
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                                    <button type="submit" class="btn btn-info updateBtn hidden-button" style="margin-right: 25px;">Update</button>                                                                   
                                    <button type="submit" class="btn btn-default cancelBtn" >Cancel</button>
                                </div>
                            </div>                                       
						<div class="clearfix"></div>     
                        
                        <!-- Iteme List Model-->            
                        <div class="modal fade Item_List" id="Item_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                <div class="modal-header" style="padding:5px 10px;">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Pincode List</h4>
                                </div>
                                <div class="modal-body" style="padding:0px 5px !important; max-height: 400px; overflow-y: auto;">                                            
                                    <div class="table-Item_List tableFixHead2">
                                        <table class="tree table table-striped table-bordered table-Item_List tableFixHead2" id="table_Item_List" width="100%">
                                            <thead>
                                                <tr style="display:none;">
                                                    <td colspan="5" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                                </tr>
                                                <tr>
                                                    <th id="sl" style="text-align:left; width: 25%;">PincodeId</th>                                                        
                                                    <th style="text-align:left; width: 35%;">State</th> 
                                                    <th style="text-align:left; width: 40%;">Ditrict</th>   
                                                    <th style="text-align:left; width: 40%;">Taluka</th>                                                      
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($AllPincode as $key => $value) {
                                                ?>
                                                    <tr class="get_ItemID" data-id="<?php echo $value["id"]; ?>">
                                                        <td><?php echo $value['Pincode'];?></td>
                                                        <td><?php echo $value['statename'];?></td>    
                                                        <td><?php echo $value['cityname'];?></td> 
                                                        <td><?php echo $value['talukaname'];?></td>                                                           
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>   
                                    </div>
                                </div>
                                <div class="modal-footer" style="padding:0px;">
                                    <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: left;width: 100%;">
                                </div>
                                </div>
                            <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <!-- /.modal -->
                       
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
            url:  "<?php echo admin_url(); ?>PincodeMaster/Pincode_table_data",
            type: "GET", 
            dataType: "json", 
            success: function(data) {             
               
                var tableBody = $("#table_Item_List tbody"); 
                tableBody.empty();                 

                $.each(data, function(index, value) {
                    var newRow = $("<tr class='get_ItemID' data-id='" + value.id + "'>");
                    newRow.append("<td>" + value.Pincode + "</td>");
                    newRow.append("<td>" + value.statename + "</td>");  
                    newRow.append("<td>" + value.cityname + "</td>");     
                    newRow.append("<td>" + value.talukaname + "</td>");                     
                    tableBody.append(newRow); 
                });
            },
            error: function(xhr, status, error) {
                console.error("Error occurred while fetching data: " + error);
            }
        });
    }
</script>

<script>
   function ResetForm()
    {
        $('#pinid').val(''); 
        $('#state').val(''); 
        $('#state').selectpicker('refresh');
        $('#district').val(''); 
        $('#district').selectpicker('refresh');
        $('#taluka').val(''); 
        $('#taluka').selectpicker('refresh');   

        $('.saveBtn').show();
        $('.updateBtn').hide();	 
    }    
</script>

<script>
$(document).ready(function() 
{
    $('#pinid').on('input', function() {        
        this.value = this.value.replace(/[^0-9]/g, '');        
        if (this.value.length > 6) {
            this.value = this.value.substring(0, 6);
        }
    });    

    $("#pinid").dblclick(function(){
        $('#Item_List').modal('show');
        $('#Item_List').on('shown.bs.modal', function () {
            $('#myInput1').focus();
        })
    });

    $('.cancelBtn').on('click',function() 
    {
        ResetForm();        
    });

    //save new pincode
    $('.saveBtn').on('click',function() 
    {
        Pincodename= $('#pinid').val(); 
        Statename = $('#state').val();          
        Districtname = $('#district').val(); 
        Talukaname = $('#taluka').val(); 
        if(Pincodename == '')
        {
            alert('please enter pincode');
            $('#pinid').focus();
        }
        else if(Statename == '')
        {
            alert('No Record Found');
            $('#state').focus();
        }
        else if(Districtname == '')
        {
            alert('No Record Found');
            $('#district').focus();
        }
        else if(Talukaname == '')
        {
            alert('No Record Found');
            $('#taluka').focus();
        }
        else
        {
            $.ajax({
                url: "<?php echo admin_url(); ?>PincodeMaster/savePincode", 
                type: 'POST', 
                data: {Pincodename:Pincodename,Statename:Statename,Districtname:Districtname,Talukaname:Talukaname}, 
                dataType: 'json',
                success: function(response) {
                    if (response.success) {                   
                        alert_float('success', 'Record Created Successfully...');    
                        ResetForm();     
                        refreshTable();                                 
                    } else {                    
                        alert_float('warning', 'Something went wrong...');                                                        
                    }
                },
                error: function(xhr, status, error) {                
                    $('#messageContainer').text('An error occurred while processing the request').css('background-color', '#f44336').css('color', 'white').fadeIn();
                }
            });   
        }
    });
});

$(document).on('click', '.get_ItemID', function()
{
    Id = $(this).attr("data-id");
    $.ajax({
			url:"<?php echo admin_url(); ?>PincodeMaster/GetPincodeDetailsbyID",
			dataType:"JSON",
			method:"POST",
			data:{Id:Id},
			beforeSend: function () {
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
			},
			complete: function () {
                $('.searchh2').css('display','none');
			},
			success:function(data)
            {	 
                $('#pinid').val(data.Pincode);               
                $('#state').val(data.statename);	
                $('#state_id').val(data.state_id);			   
                $('#district').val(data.cityname);		
                $('#disctrict_id').val(data.city_id);		   
                $('#taluka').val(data.talukaname);	
                $('#taluka_id').val(data.taluka_id);		                             
				$('.saveBtn').hide();
				$('.updateBtn').show();	 
                $(".updateBtn").prop("disabled", true); 
                refreshTable();                                             		
			}
	});
	$('#Item_List').modal('hide');

    //update pincode details
    $('.updateBtn').on('click',function() 
    {  
        Pincode= $('#pinid').val(); 
        Statename = $('#state').val(); 
        Districtname = $('#district').val();
        Talukaname = $('#taluka').val();

        $.ajax({
                url: "<?php echo admin_url(); ?>PincodeMaster/UpdatePincodeDetails", 
                type: 'POST', 
                data: {Id:Id,Pincode:Pincode,Statename:Statename,Districtname:Districtname,Talukaname:Talukaname}, 
                dataType: 'json',
                success: function(response) {
                    if (response.success) {                   
                        alert_float('success', 'Record Updated Successfully...');                       
                        ResetForm();                 
                        $('.saveBtn').show();
                        $('.updateBtn').hide();	     
                        refreshTable();                                                                     
                    } else {                    
                        alert_float('warning', 'Something went wrong...');       
                        ResetForm();                                            
                    }
                },
                error: function(xhr, status, error) {                
                    $('#messageContainer').text('An error occurred while processing the request').css('background-color', '#f44336').css('color', 'white').fadeIn();
                }                
        }); 
    });
});
</script>

<script>
    let debounceTimeout;
    $('#pinid').blur(function(){
        var zip = $('#pinid').val();
        if(zip == "" || zip.length < 6){
            $('#state').val("");
            $('#district').val("");
            $('#taluka').val("");
        }else 
        {            
            clearTimeout(debounceTimeout); 
            debounceTimeout = setTimeout(function() {               
                checkPincodeExistence(zip);
            }, 200);
        }

        function checkPincodeExistence(zip) {
            $.ajax({
                url: "<?php echo admin_url(); ?>PincodeMaster/CheckPincodeExistence",
                method: "POST",
                dataType: "json",
                data: { zip: zip },
                beforeSend: function() {
                    $('.searchh6').css('display', 'block');
                    $('.searchh6').css('color', 'blue');
                },
                complete: function() {
                    $('.searchh6').css('display', 'none');
                },
                success: function(data) {
                    if (data) 
                    {                                      
                        $('#state').val(data.state_name);
                        $('#state_id').val(data.short_name);
                        $('#district').val(data.city_name);
                        $('#disctrict_id').val(data.id);
                        $('#taluka').val(data.TalukaName);
                        $('#taluka_id').val(data.id);                       
                        $('.saveBtn').hide();
                        $('.updateBtn').show();	
                        $('.updateBtn').prop('disabled', true);
                    } else {
                        $('.saveBtn').prop('disabled', false);                        
                        fetchAddressDetails(zip);
                    }
                },
                error: function() {
                    alert("An error occurred while checking the pincode.");
                }
            });
        }

        function fetchAddressDetails(zip) {
            $.ajax({
                url: "<?php echo admin_url(); ?>PincodeMaster/FetchAddressDetailsByPincode", // Your original endpoint for fetching address details
                method: "POST",
                dataType: "json",
                data: {zip: zip},
                beforeSend: function() {
                    $('.searchh6').css('display', 'block');
                    $('.searchh6').css('color', 'blue');
                },
                complete: function() {
                    $('.searchh6').css('display', 'none');
                },
                success: function(data) {
                    let result = data[0]["PostOffice"];                
                    if (result == null) {
                        alert(data[0]["Message"]);
                        $('#state').val('');
                        $('#district').val('');
                        $('#taluka').val('');
                    } else {
                        var District = result[0]["District"];
                        var State = result[0]["State"];
                        var Taluka = result[0]["Block"];
                        $('#district').val(District);
                        $('#state').val(State);
                        $('#taluka').val(Taluka);
                    }
                }
            });
        }       
    });   
</script>

<script>
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table_Item_List");
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
</script>

<style>	
#table_Item_List td:hover {
    cursor: pointer;
}
#table_Item_List tr:hover {
    background-color: #ccc;
}

    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>