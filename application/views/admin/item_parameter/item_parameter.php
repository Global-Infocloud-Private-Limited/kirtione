<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-8">
        <div class="panel_s">
          <div class="panel-body">
              <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Item QA Parameters</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
    					
                <div class="row">
                    <div class="col-md-12">
                        <div class="searchh2" style="display:none;">Please wait fetching data...</div>
                        <div class="searchh3" style="display:none;">Please wait Create new ItemDivision...</div>
                        <div class="searchh4" style="display:none;">Please wait update ItemDivision...</div>
                    </div>
                    <br>
                    <div class="col-md-2">
                        <?php
                            $ItemParametersRecord = $lastId + 1;
                        ?>
                        <div class="form-group" app-field-wrapper="ItemParameterID">
                            <label for="ItemParameterID" class="control-label">ItemParameterID</label>
                            <input type="text" id="ItemParameterID" name="ItemParameterID" class="form-control" value="<?= $ItemParametersRecord ?>" >
                            <input type="hidden" id="NextItemDivisionID" name="NextItemDivisionID" class="form-control" value="<?php echo $ItemParametersRecord; ?>">
                        </div>
                        <span id="lblError" style="color: red"></span>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="ItemParameterName">
                            <label for="ItemParameterName" class="control-label">ItemParameterName</label>
                            <input type="text" id="ItemParameterName" name="ItemParameterName" class="form-control" value="" >
                        </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    <br><br>
                    <div class="col-md-12">
                        <?php if (has_permission('itemsparameters', '', 'create')) {
                        ?>
                        <button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                        <?php
                        }else{
                        ?>
                        <button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
                        <?php
                        }?>
                        
                        <?php if (has_permission('itemsparameters', '', 'edit')) {
                        ?>
                        <button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>
                        <?php
                        }else{
                        ?>
                        <button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
                        <?php
                        }?>
                        
                        <button type="button" class="btn btn-default cancelBtn" >Cancel</button>
                    </div>
                </div>
                
                <div class="clearfix"></div>
            <!-- Iteme List Model-->
            
                <div class="modal fade ItemParameters_List" id="ItemParameters_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                        <div class="modal-header" style="padding:5px 10px;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">ItemParameter List</h4>
                        </div>
                        <div class="modal-body" style="padding:0px 5px !important">
                            
                            <div class="table-ItemDivision_List tableFixHead2">
                                <table class="tree table table-striped table-bordered table-ItemDivision_List tableFixHead2" id="table_ItemDivision_List" width="100%">
                                    <thead>
                                        <tr style="display:none;">
                                            <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                        </tr>
                                        <tr>
                                            <th style="text-align:left;">ItemParameter ID </th>
                                            <th style="text-align:left;">ItemParameter Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($table_data as $key => $value) {
                                    ?>
                                        <tr class="get_ItemParameter" data-id="<?php echo $value["ItemParameterID"]; ?>">
                                            <td><?php echo $value['ItemParameterID'];?></td>
                                            <td><?php echo $value['ItemParameterName'];?></td>
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
    $(document).ready(function(){
        $('.updateBtn').hide();
        $('.updateBtn2').hide();
    });
</script>
<script>
    $("#ItemParameterID").dblclick(function(){
        $("#ItemParameterID").val('');
        $('#ItemParameters_List').modal('show');
        $('#ItemParameters_List').on('shown.bs.modal', function () {
            $('#myInput1').focus();
        })
    });
</script>
<script>
     function myFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("table_ItemDivision_List");
  tr = table.getElementsByTagName("tr");
   for (i = 1; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[0];
      td1 = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else if(td1){
         txtValue = td1.textContent || td1.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else{
           tr[i].style.display = "none";
      } 
    }   
  }
}
}
 </script>
<script>
    $("#ItemParameterID").focus(function(){
        var NextItemDivisionID = $('#NextItemDivisionID').val();
        $('#ItemParameterID').val(NextItemDivisionID);
        $('#ItemParameterName').val('');
                       
        $('.saveBtn').show();
        $('.saveBtn2').show();
        $('.updateBtn').hide();
        $('.updateBtn2').hide();
            
    });
</script>
<script>
    $(".cancelBtn").click(function(){
        var NextItemDivisionID = $('#NextItemDivisionID').val();
        $('#ItemParameterID').val(NextItemDivisionID);
        $('#ItemParameterName').val('');
                       
        $('.saveBtn').show();
        $('.saveBtn2').show();
        $('.updateBtn').hide();
        $('.updateBtn2').hide();
            
    });
</script>
<script>
    $('#ItemParameterID').blur(function(){ 
            ItemParameterID = $(this).val();
            if(ItemParameterID == ''){
                var NextItemDivisionID = $('#NextItemDivisionID').val();
                $('#ItemParameterID').val(NextItemDivisionID);
            }else{
                $.ajax({
                    url:"<?php echo admin_url(); ?>invoice_items/GetItemParametersDetailByID",
                    dataType:"JSON",
                    method:"POST",
                    data:{ItemParameterID:ItemParameterID},
                    beforeSend: function () {
                    $('.searchh2').css('display','block');
                    $('.searchh2').css('color','blue');
                    },
                    complete: function () {
                    $('.searchh2').css('display','none');
                    },
                    success:function(data){
                        if(data != null){
                            $('#ItemParameterID').val(data.ItemParameterID);
                           $('#ItemParameterName').val(data.ItemParameterName);
                           
                           $('.saveBtn').hide();
                           $('.updateBtn').show();
                           $('.saveBtn2').hide();
                           $('.updateBtn2').show();
                        }
                    }
                });
                $('#ItemParameters_List').modal('hide');
            }
            
        });
</script>
<script>
    $('.get_ItemParameter').on('click',function(){ 
            ItemParameterID = $(this).attr("data-id");
            $.ajax({
                url:"<?php echo admin_url(); ?>invoice_items/GetItemParametersDetailByID",
                dataType:"JSON",
                method:"POST",
                data:{ItemParameterID:ItemParameterID},
                beforeSend: function () {
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
                },
                complete: function () {
                $('.searchh2').css('display','none');
                },
                success:function(data){
                       $('#ItemParameterID').val(data.ItemParameterID);
                       $('#ItemParameterName').val(data.ItemParameterName);
                       
                       $('.saveBtn').hide();
                       $('.updateBtn').show();
                       $('.saveBtn2').hide();
                       $('.updateBtn2').show();
                }
            });
            $('#ItemParameters_List').modal('hide');
        });
</script>
<script>
    $('.saveBtn').on('click',function(){ 
            ItemParameterID = $('#ItemParameterID').val();
            ItemParameterName = $('#ItemParameterName').val();
            
            $.ajax({
                url:"<?php echo admin_url(); ?>invoice_items/SaveItemParameters",
                dataType:"JSON",
                method:"POST",
                data:{ItemParameterName:ItemParameterName,ItemParameterID:ItemParameterID
                },
                beforeSend: function () {
                $('.searchh3').css('display','block');
                $('.searchh3').css('color','blue');
                },
                complete: function () {
                $('.searchh3').css('display','none');
                },
                success:function(data){
                   if(data == true){
                       
                       alert_float('success', 'Record created successfully...');
                       var NextItemDivisionID = $('#NextItemDivisionID').val();
                       var newGroupID = parseInt(NextItemDivisionID) + 1;
                        $('#ItemParameterID').val(newGroupID);
                        $('#NextItemDivisionID').val(newGroupID);
                        $('#ItemParameterName').val('');
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                   }else{
                       alert_float('warning', 'Something went wrong...');
                   }
                }
            });
        });
</script>
<script>
    $('.updateBtn').on('click',function(){ 
            ItemParameterID = $('#ItemParameterID').val();
            ItemParameterName = $('#ItemParameterName').val();
            
            $.ajax({
                url:"<?php echo admin_url(); ?>invoice_items/UpdateItemParameters",
                dataType:"JSON",
                method:"POST",
                data:{ItemParameterName:ItemParameterName,ItemParameterID:ItemParameterID
                },
                beforeSend: function () {
                $('.searchh4').css('display','block');
                $('.searchh4').css('color','blue');
                },
                complete: function () {
                $('.searchh4').css('display','none');
                },
                success:function(data){
                   if(data == true){
                       alert_float('success', 'Record updated successfully...');
                       var NextItemDivisionID = $('#NextItemDivisionID').val();
                        $('#ItemParameterID').val(NextItemDivisionID);
                       $('#ItemParameterName').val('');
                       
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                   }else{
                       alert_float('warning', 'Something went wrong...');
                   }
                }
            });
        });
</script>
<style>

#item_code1 {
    text-transform: uppercase;
}
#table_ItemDivision_List td:hover {
    cursor: pointer;
}
#table_ItemDivision_List tr:hover {
    background-color: #ccc;
}

    .table-ItemDivision_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-ItemDivision_List thead th { position: sticky; top: 0; z-index: 1; }
    .table-ItemDivision_List tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>