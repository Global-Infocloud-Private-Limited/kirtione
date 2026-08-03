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
                                        <li class="breadcrumb-item active" aria-current="page"><b>Category Master</b></li>
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
                                        <label for="subcatid">Id</label>
                                        <input type="text" name="subcatid" id="subcatid" class="form-control" value="" readonly>                                        
                                    </div>
                                </div>         
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <small class="req text-danger">*</small>
                                        <label for="subcatname">Category Name</label>
                                        <input type="text" name="subcatname" id="subcatname" class="form-control" value="">
                                    </div>
                                </div>	                      
                             
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
                                        <h4 class="modal-title">Category List</h4>
                                    </div>
                                    <div class="modal-body" style="padding:0px 5px !important">                                            
                                        <div class="table-Item_List tableFixHead2">
                                            <table class="tree table table-striped table-bordered table-Item_List tableFixHead2" id="table_Item_List" width="100%">
                                                <thead>
                                                    <tr style="display:none;">
                                                        <td colspan="5" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                                    </tr>
                                                    <tr>
                                                        <th id="sl" style="text-align:left; width: 25%;">CategoryId</th>                                                        
                                                        <th style="text-align:left; width: 35%;">Category Name</th>                                                                                                            
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($Categories as $key => $value) {
                                                    ?>
                                                        <tr class="get_ItemID" data-id="<?php echo $value["id"]; ?>">
                                                            <td><?php echo $value['id'];?></td>
                                                            <td><?php echo $value['SubcategoryName'];?></td>                                                                                                                      
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
            url:  "<?php echo admin_url(); ?>CategoryMaster/Category_table_data",
            type: "GET", 
            dataType: "json", 
            success: function(data) {             
               
                var tableBody = $("#table_Item_List tbody"); 
                tableBody.empty();                 

                $.each(data, function(index, value) {
                    var newRow = $("<tr class='get_ItemID' data-id='" + value.id + "'>");
                    newRow.append("<td>" + value.id + "</td>");
                    newRow.append("<td>" + value.SubcategoryName + "</td>");                                       
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
    var maxCatId = <?php echo isset($maxCatId) ? $maxCatId : 'null'; ?>;
    if (maxCatId == null || maxCatId == undefined || maxCatId == '') 
    {
        $('#subcatid').val(1);      
    }
    else
    {
        $('#subcatid').val(maxCatId + 1);  
    }  

    //save new card name
    $('.saveBtn').on('click',function() 
    {
        SubcatName= $('#subcatname').val();       
        if(SubcatName == '')
        {
            alert('please enter subcategory name');
            $('#subcatname').focus();
        }       
        else
        {
            $.ajax({
                url: "<?php echo admin_url(); ?>CategoryMaster/insertSubCategory", 
                type: 'POST', 
                data: {SubcatName:SubcatName}, 
                dataType: 'json',
                success: function(response) {
                    if (response.success) {                   
                        alert_float('success', 'Record Created Successfully...');       
                        var maxCatId = response.newCatId; 
                        $('#subcatid').val(maxCatId + 1);        
                        $('#subcatname').val('');                      
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

    $("#subcatname").dblclick(function(){
        $('#Item_List').modal('show');
        $('#Item_List').on('shown.bs.modal', function () {
            $('#myInput1').focus();
        })
    });

    $('.cancelBtn').on('click',function() 
    {
        ResetForm();
        refreshTable();   
        $('.saveBtn').show();
        $('.updateBtn').hide();	     
    });
});

$(document).on('click', '.get_ItemID', function()
{
    SubCatId = $(this).attr("data-id");
    $.ajax({
			url:"<?php echo admin_url(); ?>CategoryMaster/GetSubcategoryDetailsbyID",
			dataType:"JSON",
			method:"POST",
			data:{SubCatId:SubCatId},
			beforeSend: function () {
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
			},
			complete: function () {
                $('.searchh2').css('display','none');
			},
			success:function(data)
            {	 
                $('#subcatid').val(data.id);
                $('#subcatname').val(data.SubcategoryName);                                        
				$('.saveBtn').hide();
				$('.updateBtn').show();	    
                refreshTable();                            		
			}
	});
	$('#Item_List').modal('hide');

    //update fertilizers details
    $('.updateBtn').on('click',function() 
    {        
        SubcatId= $('#subcatid').val(); 
        SubcatName = $('#subcatname').val();         

        $.ajax({
                url: "<?php echo admin_url(); ?>CategoryMaster/UpdateCategoryDetails", 
                type: 'POST', 
                data: {SubcatId:SubcatId,SubcatName:SubcatName}, 
                dataType: 'json',
                success: function(response) {
                    if (response.success) {                   
                        alert_float('success', 'Record Updated Successfully...');
                        var Category = response.Categories;
                        var lastCat = Category[Category.length - 1];  
                        var lastCatId = parseInt(lastCat.id);
                        var newCatId = lastCatId + 1;
                      
                        $('#subcatname').val('');                          
                        $('#subcatid').val(newCatId);                          
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
var maxCatId = <?php echo $maxCatId; ?>;  
    function ResetForm()
    {
        $('#subcatname').val('');     
        $('#subcatid').val(maxCatId + 1);         
    }    
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
#item_code1 {
    text-transform: uppercase;
}
#table_Item_List td:hover {
    cursor: pointer;
}
#table_Item_List tr:hover {
    background-color: #ccc;
}

.hidden-button {
    display: none;
}

.table-Item_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-Item_List thead th { position: sticky; top: 0; z-index: 1; }
    .table-Item_List tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>