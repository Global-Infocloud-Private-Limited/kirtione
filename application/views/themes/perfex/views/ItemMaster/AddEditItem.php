<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-8">
    <div class="panel_s">
        <div class="panel-body">
            <div class="row">
                    <nav aria-label="breadcrumb" >
                        <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                            <li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                            <li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
                            <li class="breadcrumb-item active" aria-current="page"><b>Item Master</b></li>
                        </ol>
                    </nav>
                    <hr class="hr_style">

                    <div class="col-md-12">
                        <div class="searchh2" style="display:none;">Please wait fetching data...</div>
                        <div class="searchh3" style="display:none;">Please wait Create new ItemID...</div>
                    </div>
                    <br>
                    <?php  
                        $nextproductnumber = $NextNumber->value;        
                    ?>
                    <div class="col-md-2">
                        <div class="form-group">                                      
                            <label for="itemid">Item ID</label>
                            <input type="text" name="itemid" id="itemid" class="form-control" value="<?php echo $nextproductnumber;?>" readonly>                                        
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">     
                            <small class="req text-danger">* </small>                                 
                            <label for="itemname">Item Name</label>
                            <input type="text" name="itemname" id="itemname" class="form-control" value="">                                        
                            <span id="nameError" style="color: red"></span>
                        </div>
                    </div>                  

                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="brandname">
                            <small class="req text-danger">* </small>
                            <label for="subcat" class="form-label">Category</label>
                         <select name="subcat" id="subcat" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                             <option value="">None selected</option>
                            <?php
                                foreach($Categories as $subcat) 
                                {
                                echo '<option value="' . $subcat['id'] . '">' . $subcat['SubcategoryName'] . '</option>';
                                } 
                            ?>                                                         
                        </select>
                        <span id="catError" style="color: red"></span>
                        </div>
                    </div> 
                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="brandname">
                            <small class="req text-danger">* </small>
                            <label for="subcat2" class="form-label">Sub Category</label>
                       <select name="subcat2" id="subcat2" class="selectpicker form-control" data-selected-id="<?= $selectedSubcat2ID ?? '' ?>" data-live-search="true">
					    <option value="">None selected</option>
                              </select>
							 
                        <span id="catError" style="color: red"></span>
                        </div>
                    </div>
			 </div>

                <div class="clearfix"></div>                

                <div class="row">  
                    
                    <div class="col-md-3">
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
                        <span id="brandError" style="color: red"></span>
                        </div>
                    </div>  
                              

                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="brandname">
                            <small class="req text-danger">* </small>
                            <label for="gst" class="form-label">GST(%)</label>
                       <select name="gst" id="gst" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                            <option value="">None selected</option>
                            <?php foreach($taxes as $tax){ ?>
                                <option value="<?php echo $tax['id']; ?>" data-rate="<?php echo $tax['taxrate']; ?>"> <?php echo $tax['taxrate']; ?>%</option>
                            <?php } ?>                                                                  
                        </select>
					<div id="tax-input-container"></div>
                        <span id="gstError" style="color: red"></span>
                        </div>
                    </div>		
						
						<div class="col-md-3">
                        <div class="form-group">     
                        <small class="req text-danger">* </small>                                 
                            <label for="tot_amount">Sale Rate</label>
                            <input type="text" name="SaleRate" id="SaleRate" class="form-control" value="">                                        
                            <span id="rateError" style="color: red"></span>   
                            <span id="rateinputError" style="color: red"></span>                          
                        </div>
                    </div>
				
                    <div class="col-md-3">
                        <div class="form-group">     
                        <small class="req text-danger">* </small>                                 
                            <label for="price">Basic Rate</label>
                            <input type="text" name="price" id="price" class="form-control" value="" readonly>                                        
                            <span id="rateError" style="color: red"></span>   
                            <span id="rateinputError" style="color: red"></span>                          
                        </div>
                    </div>                    
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <small class="req text-danger">* </small>
                            <label class="form-label">MonitorStock?</label>
                            <select class="selectpicker" id= "monitorstock" name="monitorstock" data-width="100%" data-none-selected-text="-- Select --" data-live-search="false">
                                <option value="Y">Yes</option> 
                                <option value="N">No</option> 
                            </select>
                        </div>
                    </div>  

                    <div class="col-md-3">
                        <div class="form-group">
                            <small class="req text-danger">* </small>
                            <label class="form-label">HSN Code</label>
                            <select class="selectpicker" name="hsn_code" id="hsn_code" data-width="100%" data-none-selected-text="<?php echo _l('no_measured_in'); ?>" data-live-search="true">
                            <option value=""></option>
                            <?php
                            foreach ($hsn as $key => $value) {
                            ?>
                                <option value="<?php echo $value['name']; ?>"><?php echo $value['name']; ?></option>   
                            <?php   
                                }
                            ?>
                            </select>
                            <span id="HsnError" style="color: red"></span>
                        </div>
                    </div>                               


                    <div class="col-md-3">
                        <div class="form-group">
                            <small class="req text-danger">* </small>
                            <label class="control-label" for="unit"><?php echo _l('measured_in'); ?></label>
                            <select class="selectpicker display-block" data-width="100%" name="unit" id="unit" data-none-selected-text="<?php echo _l('no_measured_in'); ?>">
                                <option value=""></option>
                                <option value="Pcs">Pcs</option>
                                <option value="Boxs">Box</option>   
                                <option value="Kgs">Kgs</option>
                                <option value="Gms">Gms</option>
                                <option value="Ltrs">Ltrs</option>
                                <option value="Mtrs">Mtrs</option>                                
                            </select>
                            <span id="unitError" style="color: red"></span>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">  
                            <small class="req text-danger">* </small>                                                      
                            <label for="packqty">Packing Qty</label>
                            <input type="text" name="packqty" id="packqty" class="form-control" value="">                                                      
                            <span id="qtyError" style="color: red"></span>
                            <span id="qtyinputError" style="color: red"></span>   
                        </div>                        
                    </div>  
		  </div>       
                
                <div class="clearfix"></div>     

                <div class="row">
					
                    <div class="col-md-3">
                        <div class="form-group"> 
                            <small class="req text-danger">* </small>                                                         
                            <label for="weight">Packing weight(Kg)</label>
                            <input type="text" name="weight" id="weight" class="form-control" value="">                                                      
                            <span id="weightError" style="color: red"></span>
                        </div>
                    </div>               
                   
                   
                    <div class="col-md-5">
                        <div class="form-group">                                      
                            <label for="description">Item Description</label>
                            <textarea name="description" id="description" class="form-control" style="height: 60px;"></textarea>
                        </div>
                    </div> 
					 <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Main Image</label>
                            <input type="file" id="item_image" name="item_image" class="form-control">                            
                        </div>
                    </div>   
                </div> 
                  
                <div class="clearfix"></div>    
                  
                <div class="clearfix"></div>            

                <div class="row">
                    <div class="col-md-2" id="imageDivID" style="display:none;">
                        <div class="form-group">
                            <img id="item_image_edit" src="" style="height: 100px;width: 110px;">
                        </div>
                    </div>
                </div>   
                   
                <div class="clearfix"></div>              
                    <div class="col-md-12" style="margin-top:2%;">                       
                        <button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                                                          
                        <button type="button" class="btn btn-info updateBtn hidden-button" style="margin-right: 25px;">Update</button>
                       
                        <button type="button" class="btn btn-default cancelBtn" >Cancel</button>
                    </div>
                    
                 <!-- Iteme List Model-->            
                <div class="modal fade Item_List" id="Item_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                        <div class="modal-header" style="padding:5px 10px;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Item List </h4>
                        </div>
                        <div class="modal-body" style="padding:0px 5px !important">                                            
                            <div class="table-Item_List tableFixHead2">
                                <table class="tree table table-striped table-bordered table-Item_List tableFixHead2" id="table_Item_List" width="100%">
                                    <thead>
                                       <tr style="display:none;">
                                            <td colspan="5" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                        </tr>
										 <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Is Active </label>
                                        <select class="selectpicker" name="searchisactive" id="searchisactive" data-width="100%" data-none-selected-text="-- Select --" data-live-search="true">
                                            <option value="Y">Active</option> 
                                            <option value="N" >Deactive</option> 
                                            <option value=""Selected>All</option> 
                                        </select>
                                    </div>
                                </div>
							
                                <div class="col-md-3" style="margin-top: 21px;">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-info SearchItemList"  onclick="refreshTable()" style="margin-right: 25px;">Search</button>
                                    </div>
                                </div>
                                        <tr>
                                            <th id="sl" style="text-align:left; width: 10%;">Item ID</th>                                                        
                                            <th style="text-align:left; width: 15%;">Item Name</th>
                                            <th style="text-align:left; width: 15%;">Item For</th>
                                            <th style="text-align:left; width: 15%;">Category</th>
                                            <th style="text-align:left; width: 15%;">Subcategory</th> 
                                            <th style="text-align:left; width: 15%;">Brand Name</th> 
                                            <th style="text-align:left; width: 10%;">Rate</th>
											<th style="text-align:left; width: 10%;">SaleRate</th>  
                                            <th style="text-align:left; width: 10%;">Gst</th>  
                                            <th style="text-align:left; width: 10%;">MeasuredIn</th>  
                                            <th style="text-align:left; width: 10%;">Hsn Code</th> 
                                            <th style="text-align:left; width: 10%;">Packing Qty</th>  
                                            <th style="text-align:left; width: 10%;">Packing Weight</th>
										    <th style="text-align:left; width: 10%;">Is Active</th>                                                                                                                                      
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
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

<script>

	function refreshTable()
    {
	    var status = $("#searchisactive").val();
        var LoginID = <?php echo $contact->AccountID?>;
		
        $.ajax({
            url:  "<?php echo base_url(); ?>ItemMaster/GetItemList",
            type: "POST", 
            dataType: "json",
			data: {
			LoginID:LoginID,
            status: status
            },
            success: function(data) {                
                var tableBody = $("#table_Item_List tbody"); 
                tableBody.empty();                 

                $.each(data, function(index, value) {
                    var newRow = $("<tr class='get_ItemID' data-id='" + value.id + "'>");
                    newRow.append("<td>" + value.ProductID + "</td>");
                    newRow.append("<td>" + value.ProductName + "</td>");  
                    newRow.append("<td>" + value.ItemFor + "</td>");  
                    newRow.append("<td>" + value.SubcategoryName + "</td>");					
                    newRow.append("<td>" + value.SubcateName + "</td>");  
                    newRow.append("<td>" + value.BrandName + "</td>");   
                    newRow.append("<td>" + value.rate + "</td>");
					newRow.append("<td>" + value.SaleRate + "</td>");          
                    newRow.append("<td>" + value.taxrate + "%</td>");
                    newRow.append("<td>" + value.unit + "</td>");          
                    newRow.append("<td>" + value.hsn_code + "</td>");   
                    newRow.append("<td>" + value.PackingQty + "</td>");   
                    newRow.append("<td>" + value.PackingWeight + "</td>");  
					newRow.append("<td>" + value.isactive + "</td>");
                    tableBody.append(newRow); 
                });
            },
            error: function(xhr, status, error) {
                console.error("Error occurred while fetching data: " + error);
            }
        });
    }
	
	function calculateSalePrice() {
    var gstRate = parseFloat($('#gst option:selected').data('rate')) || 0;
    var SaleRate = parseFloat($("#SaleRate").val()) || 0;
    let basic_rate = (SaleRate * 100) / (100 + gstRate);
    let gst_amount = SaleRate - basic_rate;			
	var divobj = document.getElementById('price');
        divobj.value = basic_rate.toFixed(2);
	}
	
	calculateSalePrice();
	
	$('#gst, #SaleRate').on('change keyup', function () {
	var gstRate = parseFloat($('#gst option:selected').data('rate')) || 0;
	if (!gstRate) {
        alert("Please select a valid GST tax.");
        return;
    }
    calculateSalePrice();
});
	

	
	
	// $('#SaleRate').on('change', function() {
				// var selectedValue = $(this).val();
				// var selectedText = $('#gst option:selected').val();

				// Remove previous input if any
				// $('#tax-input-container').empty();
				// $('#SaleRate').val("");
				// $('#price').val("");

				// if (selectedValue) {
					// Append new input field
					// $('#tax-input-container').append(
						// '<input type="hidden" name="selected_tax" id ="selected_tax" class="form-control" value="' + selectedText + '" readonly>'
					// );
				// }
			// });
        // $(document).ready(function() {
            // $('#SaleRate').keyup(function(ev) {
				  // let SaleRate = parseFloat($("#SaleRate").val()) || 0;
				  // let gstRate = parseFloat($("#selected_tax").val()) || 0;
				  // console.log(gstRate);
				  // let basic_rate = (SaleRate * 100) / (100 + gstRate);
                  // let gst_amount = SaleRate - basic_rate;
				  // let gstAmount = SaleRate * (gstRate / 100);
                  // let totalAmount = SaleRate + gstAmount;			
                  // var divobj = document.getElementById('price');
                  // divobj.value = basic_rate.toFixed(2);
            // });
        // });
	
	$(document).ready(function () {
    $('#subcat').on('change', function () {
        var categoryId = $(this).val();
        if (categoryId) {
            $.ajax({
                url: "<?php echo base_url(); ?>ItemMaster/GetCategoryFromSubCategory",
                type: 'POST',
                data: { category_id: categoryId },
                dataType: 'html', // Expect HTML, not JSON
                success: function (data) {
                    $('#subcat2').empty().append('<option value="">None selected</option>' + data);
                    $('#subcat2').selectpicker('refresh');
                },
                error: function (xhr, status, error) {
                    console.error("AJAX error:", xhr.responseText);
                }
            });
        } else {
            $('#subcat2').empty().append('<option value="">None selected</option>');
            $('#subcat2').selectpicker('refresh');
        }
    });
 });
	
</script>

<script>
$(document).ready(function() 
{
    $('#imageDivID').hide();
    $('#item_image_edit').attr('src', '');    

    $("#price").keypress(function (e) 
    {
        var keyCode = e.keyCode || e.which;
        var key = String.fromCharCode(keyCode);      
       
        var regex = /^[0-9]$/;  
        var isValid = regex.test(key);
       
        if (key === '.' && $(this).val().indexOf('.') === -1) {
            isValid = true;
        }

        if (!isValid) {
            $("#rateinputError").html("Enter valid rate.");
            setTimeout(function() {
                $("#rateinputError").html("");
            }, 2000);
        } else {
            $("#rateinputError").html("");
        }
        return isValid;
    });

    $("#packqty").keypress(function (e) 
    {
        var keyCode = e.keyCode || e.which;
        var key = String.fromCharCode(keyCode);      
       
        var regex = /^[0-9]$/;  
        var isValid = regex.test(key);
       
        if (key === '.' && $(this).val().indexOf('.') === -1) {
            isValid = true;
        }

        if (!isValid) {
            $("#qtyinputError").html("Enter valid qty.");
            setTimeout(function() {
                $("#qtyinputError").html("");
            }, 2000);
        } else {
            $("#qtyinputError").html("");
        }
        return isValid;
    });

    //save new product
    $('.saveBtn').on('click',function() 
    {
        Itemid= $('#itemid').val();
		ProductName= $('#itemname').val(); 
        Category = $('#subcat').val();
		Subcategory = $('#subcat2').val();		
        Brandname = $('#brandid').val(); 
        BasicRate = $('#price').val();
        Gst = $('#gst').val();
		SaleRate = $('#SaleRate').val();
        unit = $('#unit').val();
        HsnCode = $('#hsn_code').val();
        PackingQuantity = $('#packqty').val();
        PackingWeight = $('#weight').val();
        Stock = $('#monitorstock').val();
        // isactive = $('#isactive').val();
        ItemFor = <?php echo $contact->AccountID?>;
        description = $('#description').val();    
        item_image = $('#item_image')[0].files[0];  
        
        var formData = new FormData();
		formData.append('Itemid', Itemid);
        formData.append('ItemFor', ItemFor);
        formData.append('ProductName', ProductName);
        formData.append('Category', Category);
        formData.append('Subcategory', Subcategory);
        formData.append('Brandname', Brandname);
        formData.append('BasicRate', BasicRate);
        formData.append('Gst', Gst);
		formData.append('SaleRate', SaleRate);
        formData.append('unit', unit);
        formData.append('HsnCode', HsnCode);
        formData.append('Quantity', PackingQuantity);
        formData.append('Weight', PackingWeight);
        formData.append('MonitorStock', Stock);
        // formData.append('isactive', isactive);
        formData.append('description', description);
        
        if (item_image) {
        formData.append('item_image', item_image); 
        }

        if(ProductName == '')
        {
            $("#nameError").html("please enter product name.");
            setTimeout(function() {
                $("#nameError").fadeOut(); 
            }, 2000); 
        }
        else if(Subcategory == '')
        {
            $("#catError").html("select category name.");
            setTimeout(function() {
                $("#catError").fadeOut(); 
            }, 2000);           
        }
        else if(Brandname == '')
        {
            $("#brandError").html("select brand name.");
            setTimeout(function() {
                $("#brandError").fadeOut(); 
            }, 2000);            
        }
        else if(BasicRate == '')
        {
            $("#rateError").html("enter basic rate.");
            setTimeout(function() {
                $("#rateError").fadeOut(); 
            }, 2000);  
        }
        else if(Gst == '')
        {
            $("#gstError").html("Select Gst.");
            setTimeout(function() {
                $("#gstError").fadeOut(); 
            }, 2000);  
        }
        else if(unit == '')
        {
            $("#unitError").html("Select Unit.");
            setTimeout(function() {
                $("#unitError").fadeOut(); 
            }, 2000);  
        }
        else if(HsnCode == '')
        {
            $("#HsnError").html("Select Hsn code.");
            setTimeout(function() {
                $("#HsnError").fadeOut(); 
            }, 2000);  
        }
        else if(PackingQuantity == '')
        {
            $("#qtyError").html("Enter Packing Quantity.");
            setTimeout(function() {
                $("#qtyError").fadeOut(); 
            }, 2000);  
        }
        else if(PackingWeight == '')
        {
            $("#weightError").html("Enter Packing Weight.");
            setTimeout(function() {
                $("#weightError").fadeOut(); 
            }, 2000);  
        }        
        else
        {
            $.ajax({
                url: "<?php echo base_url(); ?>ItemMaster/AddItem", 
                type: 'POST', 
                data: formData, 
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) 
                    {                   
                        alert_float('success', 'Record Created Successfully...');  
                        var Productnumber = response.nextproductnumber; 
                        $('#itemid').val(parseInt(Productnumber) + 1);
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

    $("#itemname").dblclick(function(){
        $('#Item_List').modal('show');
        $('#Item_List').on('shown.bs.modal', function () {
            $('#myInput1').focus();
            refreshTable();
        })
    });

    $('.cancelBtn').on('click',function() 
    {
        var productid = <?php echo json_encode($nextproductnumber); ?>;
        $('#itemid').val(productid);
        $('.saveBtn').show();
        $('.updateBtn').hide();	      
        ResetForm();         
    });

});

$(document).on('click', '.get_ItemID', function()
{
    Id = $(this).attr("data-id");
    $.ajax({
			url:"<?php echo base_url(); ?>ItemMaster/GetProductDetailsbyID",
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
                $('#itemid').val(data.ProductID).prop('readonly', true);
                $('#itemname').val(data.ProductName);     
                $('#subcat').val(data.Category);
				$('.selectpicker').selectpicker('refresh') 
			    $('#subcat2').val(data.Subcategory).trigger('change');
				$('.selectpicker').selectpicker('refresh')				
                $('#brandid').val(data.BrandId);
				$('.selectpicker').selectpicker('refresh')               
                $('#price').val(data.rate);  
				$('#SaleRate').val(data.SaleRate);   
                $('#gst').val(data.gst); 
				
                $('.selectpicker').selectpicker('refresh')
                $('#unit').val(data.unit);   
                $('#hsn_code').val(data.hsn_code); 

                $('#packqty').val(data.PackingQty);
                $('#weight').val(data.PackingWeight);
                $('#monitorstock').val(data.MonitorStock); 
                $('.selectpicker').selectpicker('refresh')                

                // $('#isactive').val(data.isactive);
				$('.selectpicker').selectpicker('refresh')  
                $('#description').val(data.ProductDescription); 

                if(data.Productimg != ''){
                    img_url = '<?php echo base_url();?>'+'uploads/productimage/'+data.Productimg;               
                                 
                    $('#imageDivID').css('display', 'block');
                    $("#item_image_edit").attr("src", img_url);
                }
                else
                {
                    $('#imageDivID').hide();
                    $('#item_image_edit').attr('src', '');                    
                }
				$('#subcat').val(data.Category).selectpicker('refresh');
                        var category_id = $('#subcat :selected').val();
                        $.ajax({
                            url : "<?php echo base_url(); ?>ItemMaster/GetCategoryBySubCategory",
                            type: "post",
                            data: {
                                category_id: category_id,
                            },
                            beforeSend: function(){
                                $('#subcat2').val('').selectpicker('refresh');
                            },
                            success: function(c){
                                $('#subcat2').append(c).selectpicker('refresh');
                                $('#subcat2').val(data.Subcategory).selectpicker('refresh');
                                
                            }
                        });

                refreshTable();
				$('.saveBtn').hide();
				$('.updateBtn').show();	                                     		
			}
	});
	$('#Item_List').modal('hide');

    //update product details
    $('.updateBtn').on('click',function() 
    {	
		Itemid= $('#Itemid').val();
        ProductName= $('#itemname').val(); 
        Category = $('#subcat').val();
		Subcategory = $('#subcat2').val();	
        Brandname = $('#brandid').val(); 
        BasicRate = $('#price').val();
        Gst = $('#gst').val();
		SaleRate = $('#SaleRate').val();
        unit = $('#unit').val();
        HsnCode = $('#hsn_code').val();
        PackingQuantity = $('#packqty').val();
        PackingWeight = $('#weight').val();
        Stock = $('#monitorstock').val();
        // isactive = $('#isactive').val();
        description = $('#description').val();  
        item_image = $('#item_image')[0].files[0];  
        ItemFor = <?php echo $contact->AccountID?>;
        var formData = new FormData();
        formData.append('Id', Id);ItemidIt
		formData.append('Itemid', Itemid);
        formData.append('ProductName', ProductName);
        formData.append('ItemFor', ItemFor);
        formData.append('Category', Category);
        formData.append('Subcategory', Subcategory);
        formData.append('Brandname', Brandname);
        formData.append('BasicRate', BasicRate);
        formData.append('Gst', Gst);
		formData.append('SaleRate', SaleRate);
        formData.append('unit', unit);
        formData.append('HsnCode', HsnCode);
        formData.append('Quantity', PackingQuantity);
        formData.append('Weight', PackingWeight);
        formData.append('MonitorStock', Stock);
        // formData.append('isactive', isactive);
        formData.append('description', description);
        
        if (item_image) {
        formData.append('item_image', item_image); 
        }
        
        if(ProductName == '')
        {
            $("#nameError").html("please enter product name.");
            setTimeout(function() {
                $("#nameError").fadeOut(); 
            }, 2000); 
        }
        else if(Subcategory == '')
        {
            $("#catError").html("select category name.");
            setTimeout(function() {
                $("#catError").fadeOut(); 
            }, 2000);           
        }
        else if(Brandname == '')
        {
            $("#brandError").html("select brand name.");
            setTimeout(function() {
                $("#brandError").fadeOut(); 
            }, 2000);            
        }
        else if(BasicRate == '')
        {
            $("#rateError").html("enter basic rate.");
            setTimeout(function() {
                $("#rateError").fadeOut(); 
            }, 2000);  
        }
        else if(Gst == '')
        {
            $("#gstError").html("enter gst.");
            setTimeout(function() {
                $("#gstError").fadeOut(); 
            }, 2000);  
        }
        else if(unit == '')
        {
            $("#unitError").html("Select Unit.");
            setTimeout(function() {
                $("#unitError").fadeOut(); 
            }, 2000);  
        }
        else if(HsnCode == '')
        {
            $("#HsnError").html("Select Hsn code.");
            setTimeout(function() {
                $("#HsnError").fadeOut(); 
            }, 2000);  
        }
        else if(PackingQuantity == '')
        {
            $("#qtyError").html("Enter Packing Quantity.");
            setTimeout(function() {
                $("#qtyError").fadeOut(); 
            }, 2000);  
        }
        else if(PackingWeight == '')
        {
            $("#weightError").html("Enter Packing Weight.");
            setTimeout(function() {
                $("#weightError").fadeOut(); 
            }, 2000);  
        }       
        else
        {        
            $.ajax({
                    url: "<?php echo base_url(); ?>ItemMaster/UpdateProductDetails", 
                    type: 'POST', 
                    data: formData, 
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {                   
                            alert_float('success', 'Record Updated Successfully...');
                            var Productnumber = response.productnumber; 
                            $('#itemid').val(parseInt(Productnumber));
                            ResetForm();     
                            refreshTable();                
                            $('.saveBtn').show();
                            $('.updateBtn').hide();	                                                                                         
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
</script>

<script>
    function ResetForm()
    {       
        $('#itemname').val(''); 
        $('#subcat').val(''); 
        $('#subcat').selectpicker('refresh');
		$('#subcat2').empty().append('<option value="">None selected</option>'); 
        $('#subcat2').selectpicker('refresh');
        $('#brandid').val(''); 
        $('#brandid').selectpicker('refresh');
        $('#price').val('');     
        $('#gst').val(''); 
		$('#SaleRate').val('');
        $('#gst').selectpicker('refresh');           
        $('#hsn_code').val('');
        $('#hsn_code').selectpicker('refresh');  
        $('#unit').val('');
        $('#unit').selectpicker('refresh'); 
		$('#monitorstock').val('');
        $('#monitorstock').selectpicker('monitorstock');           
        $('#packqty').val('');
        $('#weight').val('');       
        $('#description').val('');     
        $('#item_image').val('');
        $('#imageDivID').hide();
        $('#item_image_edit').attr('src', '');       
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
            