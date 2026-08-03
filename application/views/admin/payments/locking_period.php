<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>

#AccountID {
    text-transform: uppercase;
}
#table_locking_List td:hover {
    cursor: pointer;
}
#table_locking_List tr:hover {
    background-color: #ccc;
}

    
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-10">
                <div class="panel_s">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Locking Period Master</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="searchh2" style="display:none;">Please wait while fetching data.</div>
                                <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                                <div class="searchh4" style="display:none;">Please wait while updating data.</div>
                            </div>
                            <br>
                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="LockID">
                                    <small class="req text-danger">* </small>
                                    <label for="LockID" class="control-label">Lock ID</label>
                                    <input type="text" id="LockID" name="LockID" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="LockName">
                                    <small class="req text-danger">* </small>
                                    <label for="LockName" class="control-label">Lock Name</label>
                                    <input type="text" id="LockName" name="LockName" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="LockDays">
                                    <small class="req text-danger">* </small>
                                    <label for="LockDays" class="control-label">Locking Days</label>
                                    <input type="text" id="LockDays" name="LockDays" class="form-control" value="">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <?php if (has_permission_new('Locking', '', 'create')) {
                                ?>
                                    <button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                                <?php
                                }else{
                                ?>
                                    <button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
                                <?php
                                }?>
                                
                                <?php if (has_permission_new('Locking', '', 'edit')) {
                                ?>
                                    <button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>
                                <?php
                                }else{
                                ?>
                                    <button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
                                <?php
                                }?>
                                <button type="button" class="btn btn-default cancelBtn" style="margin-right: 25px;" >Cancel</button>
                            </div>
                        </div>
                    </div>
                    <!------------ Modal ------------->
                        <div class="modal fade region_list" id="region_list" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                <div class="modal-header" style="padding:5px 10px;">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Locking Period List</h4>
                                </div>
                                <div class="modal-body" style="padding:0px 5px !important">
                                    
                                    <div class="">
                                        <table class="table table-striped table-bordered table-hover" id="table_locking_List" width="100%">
                                            <thead>
                                                <tr>
                                                    <th style="text-align:left;">Sr.</th>
                                                    <th style="text-align:left;">LockID</th>
                                                    <th style="text-align:left;">LockName</th>
                                                    <th style="text-align:left;">LockDays</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_data">
                                            
                                            </tbody>
                                        </table>   
                                    </div>
                                </div>
                                <div class="modal-footer" style="padding:0px;">
                                    <input type="text" id="focusInput" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: left;width: 100%;">
                                </div>
                                </div>
                            </div>
                        </div>
                    <!----           Modal             ------->    
                </div>
            </div>
        </div>       
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(document).ready(function(){
        $.ajax({
            url:"<?php echo admin_url(); ?>Payment_cycle/getAllLocking",
            method: "POST",
            success:function(data){
                $('#table_data').html(data);
            }    
        });
        $('.saveBtn').show();
        $('.updateBtn').hide();
        $('.saveBtn2').show();
        $('.updateBtn2').hide();
    });
</script>
<script>
$(document).ready(function(){
    $('#LockID').on('keyup',function(){
        $(this).val($(this).val().toUpperCase());
        $('.saveBtn').show();
        $('.updateBtn').hide();
        $('.saveBtn2').show();
        $('.updateBtn2').hide();
    });
});
</script>
<script>
    $('#LockID').on('keydown',function(e) {
        var keyCode = e.keyCode || e.which; 

        if (keyCode == 9) { 
            e.preventDefault(); 
            var LockID = $('#LockID').val();
            $.ajax({
                url: "<?php echo admin_url(); ?>Payment_cycle/getSingleLock",
                method:"POST",
                dataType:"JSON",
                data:{
                    LockID:LockID,
                },
                success:function(data){
                    $('#LockID').val(data.LockID);
                    $('#LockName').val(data.LockName);
                    $('#LockDays').val(data.LockDays);
                    $('#LockID').blur();
                    $('.saveBtn').hide();
                    $('.updateBtn').show();
                    $('.saveBtn2').hide();
                    $('.updateBtn2').show();
                },
            });
        } 
    });
</script>
<script>
    $('.cancelBtn').click(function(){
        $('input').val('');
        $('.saveBtn').show();
        $('.updateBtn').hide();
        $('.saveBtn2').show();
        $('.updateBtn2').hide();
    });
</script>
<script>
    $('.saveBtn').click(function(){
        var LockID = $('#LockID').val();
        var LockName = $('#LockName').val();
        var LockDays = $('#LockDays').val();
        
        if((LockID != '') && (LockName != '') && (LockDays != '')){
            $.ajax({
                url:"<?php echo admin_url(); ?>Payment_cycle/saveLock",
                method: "POST",
                dataType: "JSON",
                data:{
                    LockID: LockID,
                    LockName: LockName,
                    LockDays: LockDays,
                },
                success:function(data){
                    if(data == true){
                        $('input').val('');
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                        alert('Locking Period Created Successful');
                    }
                }    
            });
        }
        else{
            alert("Select Required Data !");   
        }
    });
</script>
<script>
    $('.updateBtn').click(function(){
        var LockID = $('#LockID').val();
        var LockName = $('#LockName').val();
        var LockDays = $('#LockDays').val();
        
        if((LockID != '') && (LockName != '') && (LockDays != '')){
            $.ajax({
                url:"<?php echo admin_url(); ?>Payment_cycle/updateLock",
                method: "POST",
                dataType: "JSON",
                data:{
                    LockID: LockID,
                    LockName: LockName,
                    LockDays: LockDays,
                },
                success:function(data){
                    if(data == true){
                        $('input').val('');
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                        alert('Locking Period Updated Successful');
                    }
                }    
            });
        }
        else{
            alert("Select Required Data !");   
        }
    });
</script>
<script>
    $('#LockID').dblclick(function(){
        $.ajax({
            url:"<?php echo admin_url(); ?>Payment_cycle/getAllLocking",
            method: "POST",
            success:function(data){
                $('#table_data').html(data);
            }    
        });
        $('#region_list').modal('show');
        $('#region_list').on('shown.bs.modal', function () {
            $('#focusInput').focus();
        }) 
    });
</script>
<script>
    function fill_data(LockID){
        $('#region_list').modal('hide');
        $.ajax({
            url: "<?php echo admin_url(); ?>Payment_cycle/getSingleLock",
            method:"POST",
            dataType:"JSON",
            data:{
                LockID:LockID,
            },
            success:function(data){
                $('#LockID').val(data.LockID);
                $('#LockName').val(data.LockName);
                $('#LockDays').val(data.LockDays);
                $('.saveBtn').hide();
                $('.updateBtn').show();
                $('.saveBtn2').hide();
                $('.updateBtn2').show();
            },
        });
    };
</script>
<script>
    $('#LockID').focus(function(){
        $('input').val('');
        $('.saveBtn').show();
        $('.updateBtn').hide();
        $('.saveBtn2').show();
        $('.updateBtn2').hide();
    });
</script>
<script>
    function myFunction2() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("focusInput");
        filter = input.value.toUpperCase();
        //alert(filter);
        table = document.getElementById("table_locking_List");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            td1 = tr[i].getElementsByTagName("td")[1];
            td2 = tr[i].getElementsByTagName("td")[2];
            td3 = tr[i].getElementsByTagName("td")[3];
        if(td) {
            txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
        } else if(td1){
            txtValue = td1.textContent || td1.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
        } else if(td2){
            txtValue = td2.textContent || td2.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
        }else if(td3){
            txtValue = td3.textContent || td3.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
        }else{
           tr[i].style.display = "none";
        } 
        }
    }
    }     
  }
}
}
 </script>