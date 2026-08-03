<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
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
                            <li class="breadcrumb-item active" aria-current="page"><b>Item Master</b></li>
                        </ol>
                    </nav>
                    <hr class="hr_style">
                </div>
                <div class="row">
                    
                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="ItemSubCat">
                            <small class="req text-danger">* </small>
                            <label for="ItemSubCat" class="form-label">Item SubCategory</label>
                            <select name="ItemSubCat" id="ItemSubCat" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                 <option value="">None selected</option>
                                <?php
                                    foreach($ItemSubCategory as $val) 
                                    {
                                    echo '<option value="' . $val['id'] . '">' . $val['SubCategoryName'] . '</option>';
                                    } 
                                ?>                                                         
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="ItemID">
                            <small class="req text-danger">* </small>
                            <label for="ItemID" class="form-label">Item List</label>
                            <select name="ItemID" id="ItemID" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                <option value="">None selected</option>                
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group" app-field-wrapper="taxrate">
                            <small class="req text-danger">* </small>
                            <label for="taxrate" class="form-label">GST%</label>
                            <input type="text" name="taxrate" readonly id="taxrate" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="CenterID">
                            <small class="req text-danger">* </small>
                            <label for="CenterID" class="form-label">Center List</label>
                            <select name="CenterID" id="CenterID" class="selectpicker form-control" data-actions-box = "1" multiple = "1" data-none-selected-text="Non Selected" data-live-search="true">
                                <?php
                                    foreach($CenterList as $val){
                                ?>
                                    <option value="<?php echo $val["CenterID"];?>"><?php echo $val["CenterName"];?></option>
                                <?php
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-2">
                        <div class="form-group" app-field-wrapper="new_salerate">
                            <small class="req text-danger">* </small>
                            <label for="new_salerate" class="form-label">Sale Rate</label>
                            <input type="text" name="new_salerate" id="new_salerate" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group" app-field-wrapper="new_basicrate">
                            <small class="req text-danger">* </small>
                            <label for="new_basicrate" class="form-label">Basic Rate</label>
                            <input type="text" name="new_basicrate" readonly id="new_basicrate" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group" app-field-wrapper="new_discAmt">
                            <small class="req text-danger">* </small>
                            <label for="new_discAmt" class="form-label">Disc Amt</label>
                            <input type="text" name="new_discAmt" id="new_discAmt" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-md-2" style="margin-top:2%;"> 
                        <button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                    </div>
                    
                </div>
                
            </div>
        </div>
        
      </div>
      <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group" app-field-wrapper="FilItemSubCat">
                            <label for="FilItemSubCat" class="form-label">Item SubCategory </label>
                            <select name="FilItemSubCat" id="FilItemSubCat" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                 <option value="">None selected</option>
                                <?php
                                    foreach($ItemSubCategory as $val) 
                                    {
                                    echo '<option value="' . $val['id'] . '">' . $val['SubCategoryName'] . '</option>';
                                    } 
                                ?>                                                         
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="FilItemID">
                            <label for="FilItemID" class="form-label">Item List</label>
                            <select name="FilItemID" id="FilItemID" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                <option value="">None selected</option>                
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="FilCenterID">
                            <label for="FilCenterID" class="form-label">Center List</label>
                            <select name="FilCenterID" id="FilCenterID" class="selectpicker form-control" data-actions-box = "1" multiple = "1" data-none-selected-text="Non Selected" data-live-search="true">
                                <?php
                                    foreach($RateAvlCenterList as $val){
                                ?>
                                    <option value="<?php echo $val["CenterID"];?>"><?php echo $val["CenterName"];?></option>
                                <?php
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2" style="margin-top:2%;"> 
                            <button type="button" class="btn btn-info searchBtn" style="margin-right: 25px;">Search</button>
                       
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <div class="table-RateList tableFixHead2" id="tableFixHead2">

                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      
      
    </div>
  </div>
  
</div>
<script>
    $('#ItemSubCat').on('change', function () {
        var ItemSubCat = $(this).val();
        var PartyID = <?php echo $contact->AccountID?>;
        if (ItemSubCat) {
            $.ajax({
                url: "<?php echo base_url(); ?>ItemMaster/GetItemListByCategory",
                type: 'POST',
                data: { ItemSubCat: ItemSubCat,PartyID:PartyID },
                dataType: 'html', // Expect HTML, not JSON
                success: function (data) {
                    $('#ItemID').empty().append('<option value="">None selected</option>' + data);
                    $('#ItemID').selectpicker('refresh');
                    $('#CenterID').val('');
                    $('#CenterID').selectpicker('refresh');
                    $('#new_salerate').val('');
                    $('#new_basicrate').val('');
                    $('#taxrate').val('');
                    $('#new_discAmt').val('');
                },
                error: function (xhr, status, error) {
                    console.error("AJAX error:", xhr.responseText);
                }
            });
        } else {
            $('#ItemID').empty().append('<option value="">None selected</option>');
            $('#ItemID').selectpicker('refresh');
        }
    });
    
    $('#FilItemSubCat').on('change', function () {
        var ItemSubCat = $(this).val();
        var PartyID = <?php echo $contact->AccountID?>;
        if (ItemSubCat) {
            $.ajax({
                url: "<?php echo base_url(); ?>ItemMaster/GetItemListByCategory",
                type: 'POST',
                data: { ItemSubCat: ItemSubCat,PartyID:PartyID },
                dataType: 'html', // Expect HTML, not JSON
                success: function (data) {
                    $('#FilItemID').empty().append('<option value="">None selected</option>' + data);
                    $('#FilItemID').selectpicker('refresh');
                },
                error: function (xhr, status, error) {
                    console.error("AJAX error:", xhr.responseText);
                }
            });
        } else {
            $('#FilItemID').empty().append('<option value="">None selected</option>');
            $('#FilItemID').selectpicker('refresh');
        }
    });
    
    $('#ItemID').on('change', function () {
        var ItemID = $(this).val();
        var PartyID = <?php echo $contact->AccountID?>;
        if (ItemID) {
            $.ajax({
                url: "<?php echo base_url(); ?>ItemMaster/GetProductDetailsbyProductID",
                type: 'POST',
                data: { ItemID: ItemID },
                dataType: 'JSON', // Expect HTML, not JSON
                success: function (data) {
                    $('#taxrate').val(data.taxrate);
                    $('#CenterID').val('');
                    $('#CenterID').selectpicker('refresh');
                    $('#new_salerate').val('');
                    $('#new_basicrate').val('');
                    $('#new_discAmt').val('');
                },
                error: function (xhr, status, error) {
                    console.error("AJAX error:", xhr.responseText);
                }
            });
        } else {
            $('#taxrate').val(0);
        }
    });
    
    $('#new_salerate').on('change keyup', function () {
        var gstRate = parseFloat($('#taxrate').val()) || 0;
        var SaleRate = parseFloat($("#new_salerate").val()) || 0;
        let basic_rate = (SaleRate * 100) / (100 + gstRate);
        $('#new_basicrate').val(basic_rate.toFixed(2));
    })
    
    $("#new_salerate,#new_discAmt").keypress(function (e) 
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
//=========================== Reset Form =======================================
    function ResetForm(){
        $("#ItemID").children().remove();
        $('#ItemID').val('');
        $('#ItemID').selectpicker('refresh');
        $('#CenterID').val('');
        $('#CenterID').selectpicker('refresh');
        $('#ItemSubCat').val('');
        $('#ItemSubCat').selectpicker('refresh');
        $('#new_salerate').val('');
        $('#new_basicrate').val('');
        $('#taxrate').val('');
        $('#new_discAmt').val('');
    }
//========================== Filter Rate Table =================================
//=============================== Search rate List =================================
    function GetRateList(){
        PartyID = <?php echo $contact->AccountID?>;
        ItemSubCat= $('#FilItemSubCat').val();
        ItemID= $('#FilItemID').val(); 
        CenterID = $('#FilCenterID').val();
        $.ajax({
            url: "<?php echo base_url(); ?>ItemMaster/GetAllItemRateList", 
            type: 'POST', 
            data: {PartyID:PartyID,ItemSubCat:ItemSubCat,ItemID:ItemID,CenterID:CenterID}, 
            //dataType: "JSON",
            success: function(response) {
                $('#tableFixHead2').html(response);
            },
            error: function(xhr, status, error) {                
                $('#messageContainer').text('An error occurred while processing the request').css('background-color', '#f44336').css('color', 'white').fadeIn();
            }
        });
    }
    $('.searchBtn').on('click',function() 
    {
        GetRateList();
        
    });
//=============================== Add New Rate =================================
    $('.saveBtn').on('click',function() 
    {
        var PartyID = <?php echo $contact->AccountID?>;
        ItemID= $('#ItemID').val(); 
        CenterID = $('#CenterID').val();
		new_salerate = $('#new_salerate').val();		
        new_basicrate = $('#new_basicrate').val(); 
        new_discAmt = $('#new_discAmt').val(); 
        taxrate = $('#taxrate').val(); 
        if (ItemID == "") {
            alert("Please Select Item"); 
        }else if(CenterID == ""){
            alert("Please Select atleast one center");
        }else if(taxrate == ""){
            alert("Please add taxrate for selected Item");
        }else if(new_salerate == ""){
            alert("Please Enter Sale Rate");
        }else if(new_basicrate == ""){
            alert("Basic Rate not getting, Please reload page an try again");
        }else{
            $.ajax({
                url: "<?php echo base_url(); ?>ItemMaster/AddNewItemWiseRate", 
                type: 'POST', 
                data: {PartyID:PartyID,ItemID:ItemID,CenterID:CenterID,new_salerate:new_salerate,new_basicrate:new_basicrate,new_discAmt:new_discAmt,taxrate:taxrate}, 
                dataType: "JSON",
                success: function(response) {
                    if (response.success) 
                    {     
                        ResetForm();
                        GetRateList();
                        alert_float('success', response.message);  
                    } else {                    
                        alert_float('warning', response.message);                                   
                    }
                },
                error: function(xhr, status, error) {                
                    $('#messageContainer').text('An error occurred while processing the request').css('background-color', '#f44336').css('color', 'white').fadeIn();
                }
            });  
        }
    });
</script>

<style>
#item_code1 {
    text-transform: uppercase;
}
#RateList td:hover {
    cursor: pointer;
}
#RateList tr:hover {
    background-color: #ccc;
}

.hidden-button {
    display: none;
}
.for-item-idth{
    position: sticky;
    width: 40px;
    left: 0px;
}
.for-item-nameth{
    position: sticky;
    width: 81px;
    left: 40px;
}
.for-item-idtd{
    position: sticky;
    width: 40px;
    left: 0px;
    background-color: #fff;
}
.for-item-nametd{
    position: sticky;
    width: 81px;
    left: 40px;
    background-color: #fff;
}

    .table-RateList          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-RateList thead { position: sticky; top: 0; z-index: 1; }
    .table-RateList tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>