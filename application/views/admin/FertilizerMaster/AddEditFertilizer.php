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
                                        <li class="breadcrumb-item active" aria-current="page"><b>Fertilizer Master</b></li>
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
                                        <label for="fertilizerid">Id</label>
                                        <input type="text" name="ferid" id="fertilizerid" class="form-control" value="" readonly>                                        
                                    </div>
                                </div>         
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="fertilizername">Fertilizer Name</label>
                                        <input type="text" name="fername" id="fertilizername" class="form-control" value="">
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
                                        if (has_permission_new('FertilizerMaster', '', 'create')) {
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
                                        if (has_permission_new('FertilizerMaster', '', 'edit')) {
                                    ?>
                                            <button type="button" class="btn btn-info updateBtn hidden-button" style="margin-right: 25px;">Update</button> 
                                    <?php
                                        }else{
                                    ?>
                                        <button type="button" class="btn btn-info updateBtn2 hidden-button" disabled style="margin-right: 25px;">Update</button> 
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
                                        <h4 class="modal-title">Fertilizer Name List</h4>
                                    </div>
                                    <div class="modal-body" style="padding:0px 5px !important">                                            
                                        <div class="table_Fertilizer_List tableFixHead2">
                                            <table class="tree table table-striped table-bordered table_Fertilizer_List tableFixHead2" id="table_Fertilizer_List" width="100%">
                                                <thead>
                                                    <tr style="display:none;">
                                                        <td colspan="5" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                                    </tr>
                                                    <tr>
                                                        <th id="sl" style="text-align:left; width: 25%;">FertilizerId</th>                                                        
                                                        <th style="text-align:left; width: 35%;">Fertilizer Name</th> 
                                                        <th style="text-align:left; width: 40%;">Brand Name</th>                                                      
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($fertilizers as $key => $value) {
                                                    ?>
                                                        <tr class="get_ItemID" data-id="<?php echo $value["id"]; ?>">
                                                            <td><?php echo $value['id'];?></td>
                                                            <td><?php echo $value['fertilizerName'];?></td>    
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
            url:  "<?php echo admin_url(); ?>FertilizerMaster/Fertilizers_table_data",
            type: "GET", 
            dataType: "json", 
            success: function(data) {             
               
                var tableBody = $("#table_Fertilizer_List tbody"); 
                tableBody.empty();                 

                $.each(data, function(index, value) {
                    var newRow = $("<tr class='get_ItemID' data-id='" + value.id + "'>");
                    newRow.append("<td>" + value.id + "</td>");
                    newRow.append("<td>" + value.fertilizerName + "</td>");  
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
    var maxFerId = <?php echo isset($maxFerId) ? $maxFerId : 'null'; ?>;
    if (maxFerId == null || maxFerId == undefined || maxFerId == '') 
    {
        $('#fertilizerid').val(1);      
    }
    else
    {
        $('#fertilizerid').val(maxFerId + 1);  
    }  

    //save new card name
    $('.saveBtn').on('click',function() 
    {
        FertilizerName= $('#fertilizername').val(); 
        Brandname = $('#brandid').val(); 
        if(FertilizerName == '')
        {
            alert('please enter fertilizer name');
            $('#fertilizername').focus();
        }
        else if(Brandname == '')
        {
            alert('please enter brand name');
            $('#brandid').focus();
        }
        else
        {
            $.ajax({
                url: "<?php echo admin_url(); ?>FertilizerMaster/insertFerDetails", 
                type: 'POST', 
                data: {FertilizerName:FertilizerName,Brandname:Brandname}, 
                dataType: 'json',
                success: function(response) {
                    if (response.success) {                   
                        alert_float('success', 'Record Created Successfully...');       
                        var maxFerId = response.newMaxFerId; 
                        $('#fertilizerid').val(maxFerId + 1);        
                        $('#fertilizername').val(''); 
                        $('#brandid').val(''); 
                        $('#brandid').selectpicker('refresh');  
                        refreshTable();           
                    } else {                    
                        alert_float('warning', 'Something went wrong...');
                        ResetForm();
                        refreshTable();      
                    }
                },
                error: function(xhr, status, error) {                
                    $('#messageContainer').text('An error occurred while processing the request').css('background-color', '#f44336').css('color', 'white').fadeIn();
                }
            });   
        }
    });

    $("#fertilizername").dblclick(function(){
        $('#Item_List').modal('show');
        $('#Item_List').on('shown.bs.modal', function () {
            $('#myInput1').focus();
        })
    });

    $('.cancelBtn').on('click',function() 
    {
        ResetForm();
        refreshTable();   
    });
});

$(document).on('click', '.get_ItemID', function()
{
    FertilizerId = $(this).attr("data-id");
    $.ajax({
			url:"<?php echo admin_url(); ?>FertilizerMaster/GetFertilizerDetailsbyID",
			dataType:"JSON",
			method:"POST",
			data:{FertilizerId:FertilizerId},
			beforeSend: function () {
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
			},
			complete: function () {
                $('.searchh2').css('display','none');
			},
			success:function(data)
            {	 
                $('#fertilizerid').val(data.id);
                $('#fertilizername').val(data.fertilizerName);           
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

    //update fertilizers details
    $('.updateBtn').on('click',function() 
    {        
        FertilizerId= $('#fertilizerid').val(); 
        FertilizerName = $('#fertilizername').val(); 
        BrandId = $('#brandid').val();

        $.ajax({
                url: "<?php echo admin_url(); ?>FertilizerMaster/UpdateFertilizerDetails", 
                type: 'POST', 
                data: {FertilizerId:FertilizerId,FertilizerName:FertilizerName,BrandId:BrandId}, 
                dataType: 'json',
                success: function(response) {
                    if (response.success) {                   
                        alert_float('success', 'Record Updated Successfully...');
                        var fertilizers = response.Fertilizers;
                        var lastFer = fertilizers[fertilizers.length - 1];  
                        var lastFerId = parseInt(lastFer.id);
                        var newFerId = lastFerId + 1;
                      
                        $('#fertilizername').val('');                          
                        $('#fertilizerid').val(newFerId);  
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
var maxFerId = <?php echo $maxFerId; ?>;  
    function ResetForm()
    {
        $('#fertilizername').val(''); 
        $('#brandid').val(''); 
        $('#brandid').selectpicker('refresh');
        $('#fertilizerid').val(maxFerId + 1); 
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
        table = document.getElementById("table_Fertilizer_List");
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
    #table_Fertilizer_List td:hover {
        cursor: pointer;
    }
    #table_Fertilizer_List tr:hover {
        background-color: #ccc;
    }
    .table_Fertilizer_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table_Fertilizer_List thead th { position: sticky; top: 0; z-index: 1; }
    .table_Fertilizer_List tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>