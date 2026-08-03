<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">                        						 
                            <div class="row"> 
                                <nav aria-label="breadcrumb" >
                                    <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                                        <li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                        <li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
                                        <li class="breadcrumb-item active" aria-current="page"><b>Vehicle Type Master</b></li>
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
                                        <label for="typeid">Vehicle TypeID</label>
                                        <input type="text" name="typeid" id="typeid" class="form-control" value="<?php echo $maxtypeId;?>" readonly> 
                                        <input type="hidden" name="OrgTypeID" id="OrgTypeID" class="form-control" value="<?php echo $maxtypeId;?>"> 
                                    </div>
                                </div>         
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="typename">Vehicle Type Name</label>
                                        <input type="text" name="typename" id="typename" class="form-control" value="">
                                    </div>
                                </div>	
                            </div>	                             
                            
                            <div class="clearfix"></div>
                            <br>                                   
						
                            <div class="row"> 		
                                <div class="col-md-12">
                                <?php if (has_permission('VehicleTypeMaster', '', 'create')) {
                                    ?>
                                        <button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                                    <?php
                                } else {
                                    ?>
                                        <button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
                                    <?php
                                }
                                ?>
                                    
                                <?php if (has_permission('VehicleTypeMaster', '', 'edit')) {
                                    ?>
                                        <button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>   
                                    <?php
                                } else {
                                    ?>
                                        <button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
                                    <?php
                                }
                                ?>
                                    <button type="button" class="btn btn-default cancelBtn" >Cancel</button>
                                </div>
                            </div>                                       
						<div class="clearfix"></div>		
                        
                         <!-- Iteme List Model-->            
                         <div class="modal fade Item_List" id="Item_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                    <div class="modal-header" style="padding:5px 10px;">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">Vehicle Type List</h4>
                                    </div>
                                    <div class="modal-body" style="padding:0px 5px !important">                                            
                                        <div class="table-Item_List tableFixHead2">
                                            <table class="tree table table-striped table-bordered table-Item_List tableFixHead2" id="table_Item_List" width="100%">
                                                <thead>
                                                    <tr style="display:none;">
                                                        <td colspan="5" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                                    </tr>
                                                    <tr>
                                                        <th id="sl" style="text-align:left; width: 25%;">Id</th>                                                        
                                                        <th style="text-align:left; width: 75%;">Vehicle Type Name</th>                                                       
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($vehicletype as $key => $value) {
                                                    ?>
                                                        <tr class="get_ItemID" data-id="<?php echo $value["id"]; ?>">
                                                            <td><?php echo $value['id'];?></td>
                                                            <td><?php echo $value['VehicleType'];?></td>                                                           
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
            url:  "<?php echo admin_url(); ?>VehicleTypeMaster/vehicletype_table_data",
            type: "GET", 
            dataType: "json", 
            success: function(data) {    
                var tableBody = $("#table_Item_List tbody"); 
                tableBody.empty();       
                $.each(data, function(index, value) {
                    var newRow = $("<tr class='get_ItemID' data-id='" + value.id + "'>");
                    newRow.append("<td>" + value.id + "</td>");
                    newRow.append("<td>" + value.VehicleType + "</td>");                    
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
    var OrgTypeID = $('#OrgTypeID').val(); 
    $('#typeid').val(OrgTypeID);      
    $('#typename').val('');       
    $('.saveBtn').show();
    $('.saveBtn2').show();
    $('.updateBtn').hide();
    $('.updateBtn2').hide();
}
$(document).ready(function() 
{      
    $('.saveBtn').show();
	$('.saveBtn2').show();
	$('.updateBtn').hide();
	$('.updateBtn2').hide();
    //save new card name
    $('.saveBtn').on('click',function() 
    {
        TypeName= $('#typename').val(); 
        if(TypeName == '')
        {
            alert('please enter vehicle type name');
            $('#typename').focus();
        }
        else
        {
            $.ajax({
                url: "<?php echo admin_url(); ?>VehicleTypeMaster/insertVehicleType", 
                type: 'POST', 
                data: {TypeName:TypeName}, 
                dataType: 'json',
                success: function(response) {
                    if (response.success) {                   
                        alert_float('success', 'Record Created Successfully...');
                        var maxtypeId = response.newMaxTypeId;  
                        $('#OrgTypeID').val(maxtypeId + 1); 
                        ResetForm();
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

    $("#typename").dblclick(function(){
        $('#Item_List').modal('show');
        $('#Item_List').on('shown.bs.modal', function () {
            $('#myInput1').focus();
        })
        refreshTable();
    });

    $('.cancelBtn').on('click',function() 
    {
        ResetForm();
    });
});

$(document).on('click', '.get_ItemID', function()
{
    TypeId = $(this).attr("data-id");
    $.ajax({
			url:"<?php echo admin_url(); ?>VehicleTypeMaster/GetVehcileTypeDetailsbyID",
			dataType:"JSON",
			method:"POST",
			data:{TypeId:TypeId},
			beforeSend: function () {
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
			},
			complete: function () {               
                $('.searchh2').css('display','none');
			},
			success:function(data)
            {			
                $('#typeid').val(data.id);
                $('#typename').val(data.VehicleType);
				$('.saveBtn').hide();
				$('.saveBtn2').hide();
				$('.updateBtn').show();
				$('.updateBtn2').show();		
			}
	});
	$('#Item_List').modal('hide');

    //update brand details
    $('.updateBtn').on('click',function() 
    {        
        typeid= $('#typeid').val(); 
        typename = $('#typename').val(); 

        $.ajax({
                url: "<?php echo admin_url(); ?>VehicleTypeMaster/UpdateVehicleDetails", 
                type: 'POST', 
                data: {typeid:typeid,typename:typename}, 
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
                    $('#messageContainer').text('An error occurred while processing the request').css('background-color', '#f44336').css('color', 'white').fadeIn();
                }                
        }); 
    });
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
.hidden-button {
    display: none;
}
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









