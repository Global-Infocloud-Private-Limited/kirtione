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
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">                        						 
                            <div class="row"> 
                                <nav aria-label="breadcrumb" >
                                    <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                                        <li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                        <li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
                                        <li class="breadcrumb-item active" aria-current="page"><b>Pesticide Master</b></li>
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
                                        <label for="pestiid">Id</label>
                                        <input type="text" name="PesticideId" id="pestiid" class="form-control" value="" readonly>                                        
                                    </div>
                                </div>         
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="pestiname">Pesticide Name</label>
                                        <input type="text" name="PesticideName" id="pestiname" class="form-control" value="">
                                    </div>
                                </div>	
                                
                                <div class="col-md-4">
                                    <div class="form-group" app-field-wrapper="brandname">
                                        <small class="req text-danger">* </small>
                                        <label for="brandid" class="form-label">Brand Name</label>
                                    <select name="BrandName" id="brandid" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value=""></option> 
                                        <?php
                                          foreach($Brands as $brand) 
                                          {
                                            echo '<option value="' . $brand['id'] . '">' . $brand['BrandName'] . '</option>';
                                          } 
                                        ?>                                                                      
                                    </select>
                                    </div>
                                </div>  
                            </div>	                             
                            
                            <div class="clearfix"></div>
                            <br>                                   
						
                            <div class="row"> 	
                                <div class="col-md-12">
                                    <?php
                                        if (has_permission_new('PesticideMaster', '', 'create')) {
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
                                        if (has_permission_new('PesticideMaster', '', 'edit')) {
                                    ?>
                                            <button type="button" class="btn btn-info updateBtn hidden-button" style="margin-right: 25px;">Update</button> 
                                    <?php
                                        }else{
                                    ?>
                                        <button type="button" class="btn btn-info updateBtn2 hidden-button" disabled style="margin-right: 25px;">Update</button> 
                                    <?php
                                        }
                                    ?>                                                                
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
                                        <h4 class="modal-title">Pesticides List</h4>
                                    </div>
                                    <div class="modal-body" style="padding:0px 5px !important">                                            
                                        <div class="table_Pesticide_List tableFixHead2">
                                            <table class="tree table table-striped table-bordered table_Pesticide_List tableFixHead2" id="table_Pesticide_List" width="100%">
                                                <thead>
                                                    <tr style="display:none;">
                                                        <td colspan="5" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                                    </tr>
                                                    <tr>
                                                        <th id="sl" style="text-align:left; width: 25%;">PesticideId</th>                                                        
                                                        <th style="text-align:left; width: 35%;">Pesticide Name</th> 
                                                        <th style="text-align:left; width: 40%;">Brand Name</th>                                                      
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($Pesticides as $key => $value) {
                                                    ?>
                                                        <tr class="get_ItemID" data-id="<?php echo $value["id"]; ?>">
                                                            <td><?php echo $value['id'];?></td>
                                                            <td><?php echo $value['PesticideName'];?></td>    
                                                            <td><?php echo $value['brandname'];?></td>                                                           
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
            url:  "<?php echo admin_url(); ?>PesticideMaster/Pesticides_table_data",
            type: "GET", 
            dataType: "json", 
            success: function(data) {             
               
                var tableBody = $("#table_Pesticide_List tbody"); 
                tableBody.empty();                 

                $.each(data, function(index, value) {
                    var newRow = $("<tr class='get_ItemID' data-id='" + value.id + "'>");
                    newRow.append("<td>" + value.id + "</td>");
                    newRow.append("<td>" + value.PesticideName + "</td>");  
                    newRow.append("<td>" + value.brandname + "</td>");                    
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
$(document).ready(function() 
{
    var maxpestiId = <?php echo isset($maxpestiId) ? $maxpestiId : 'null'; ?>;
    if (maxpestiId == null || maxpestiId == undefined || maxpestiId == '') 
    {
        $('#pestiid').val(1);      
    }
    else
    {
        $('#pestiid').val(maxpestiId + 1);  
    }  

    //save new card name
    $('.saveBtn').on('click',function() 
    {
        PesticideName= $('#pestiname').val(); 
        Brandname = $('#brandid').val(); 
        if(PesticideName == '')
        {
            alert('please enter Pesticide name');
            $('#pestiname').focus();
        }
        else if(Brandname == '')
        {
            alert('please enter brand name');
            $('#brandid').focus();
        }
        else
        {
            $.ajax({
                url: "<?php echo admin_url(); ?>PesticideMaster/insertPesticideDetails", 
                type: 'POST', 
                data: {PesticideName:PesticideName,Brandname:Brandname}, 
                dataType: 'json',
                success: function(response) {
                    if (response.success) {                   
                        alert_float('success', 'Record Created Successfully...');            
                        var maxpestiId = response.newMaxPestiId; 
                        $('#pestiid').val(maxpestiId + 1);        
                        $('#pestiname').val(''); 
                        $('#brandid').val(''); 
                        $('#brandid').selectpicker('refresh');   
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
        }
    });

    $("#pestiname").dblclick(function(){
        $('#Item_List').modal('show');
        $('#Item_List').on('shown.bs.modal', function () {
            $('#myInput1').focus();
        })
    });

    $('.cancelBtn').on('click',function() 
    {
        if (maxpestiId == null || maxpestiId == undefined || maxpestiId == '') 
        {
            $('#pestiid').val(1);      
        }
        else
        {
            $('#pestiid').val(maxpestiId + 1);  
        }  
        $('#pestiname').val(''); 
        $('#brandid').val(''); 
        $('#brandid').selectpicker('refresh'); 
        $('.saveBtn').show();
        $('.updateBtn').hide();
        $('.saveBtn2').show();
        $('.updateBtn2').hide();
    });
});

$(document).on('click', '.get_ItemID', function()
{
    PesticideId = $(this).attr("data-id");

    $.ajax({
			url:"<?php echo admin_url(); ?>PesticideMaster/GetPesticideDetailsbyID",
			dataType:"JSON",
			method:"POST",
			data:{PesticideId:PesticideId},
			beforeSend: function () {
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
			},
			complete: function () {
                $('.searchh2').css('display','none');
			},
			success:function(data)
            {	 
                $('#pestiid').val(data.id);
                $('#pestiname').val(data.PesticideName);           
                $('#brandid').val(data.BrandId);
				$('.selectpicker').selectpicker('refresh')                            
				$('.saveBtn').hide();
				$('.updateBtn').show();
				$('.saveBtn2').hide();
				$('.updateBtn2').show();
                refreshTable();                                         		
			}
	});
	$('#Item_List').modal('hide');

    //update seed details
    $('.updateBtn').on('click',function() 
    {        
        PesticideId= $('#pestiid').val(); 
        PesticideName = $('#pestiname').val(); 
        BrandId = $('#brandid').val();

        $.ajax({
                url: "<?php echo admin_url(); ?>PesticideMaster/UpdateSeedDetails", 
                type: 'POST', 
                data: {PesticideId:PesticideId,PesticideName:PesticideName,BrandId:BrandId}, 
                dataType: 'json',
                success: function(response) {
                    if (response.success) {                   
                        alert_float('success', 'Record Updated Successfully...');
                        var pesticide = response.Pesticides;
                        var lastPesti = pesticide[pesticide.length - 1];  
                        var lastPestiId = parseInt(lastPesti.id);
                        var newPestiId = lastPestiId + 1;
                      
                        $('#pestiname').val('');                          
                        $('#pestiid').val(newPestiId);  
                        $('#brandid').val(''); 
                        $('#brandid').selectpicker('refresh'); 
                        $('.saveBtn').show();
                        $('.updateBtn').hide();	 
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
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
var maxpestiId = <?php echo $maxpestiId; ?>;  
    function ResetForm()
    {
        $('#pestiname').val(''); 
        $('#brandid').val(''); 
        $('#brandid').selectpicker('refresh');
        $('#pestiid').val(maxpestiId + 1);  
        $('.saveBtn').show();
        $('.updateBtn').hide();
        $('.saveBtn2').show();
        $('.updateBtn2').hide();
    }    
</script>

<script>
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table_Pesticide_List");
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
    #table_Pesticide_List td:hover {
        cursor: pointer;
    }
    #table_Pesticide_List tr:hover {
        background-color: #ccc;
    }
    .table_Pesticide_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table_Pesticide_List thead th { position: sticky; top: 0; z-index: 1; }
    .table_Pesticide_List tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>

