<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>

#AccountID {
    text-transform: uppercase;
}
#table_warehouse_List td:hover {
    cursor: pointer;
}
#table_warehouse_List tr:hover {
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
    							<li class="breadcrumb-item active" aria-current="page"><b>Cluster Master</b></li>
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
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <small class="req text-danger">* </small>
                                    <label for="AccountID" class="control-label">AccountID</label>
                                    <input type="text" id="AccountID" name="AccountID" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <input value="" id="cluster_id" hidden>
                                    <small class="req text-danger">* </small>
                                    <label for="cluster" class="control-label">Cluster Name</label>
                                    <input type="text" id="cluster" name="cluster" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="CenterName">
                                    <small class="req text-danger">* </small>
                                    <label for="centerName" class="control-label">Select Centers</label>
                                    <select name="centerName" id="centerName" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true">
                                        
                                    </select>
                                </div>
                            </div>
                            <!--<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <input id="state_name" value="" hidden>
                                    <small class="req text-danger">* </small>
                                    <label for="state" class="control-label">State</label>
                                    <select name="state" id="state" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true">
                                        
                                    </select>
                                </div>
                            </div>-->
                            <!--<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <input id="city_name" value="" hidden>
                                    <small class="req text-danger">* </small>
                                    <label for="city" class="control-label">City</label>
                                    <select name="city" id="city" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true">
                                        
                                    </select>
                                </div>
                            </div>-->
                        </div>
                        
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <?php if (has_permission_new('ClusterMaster', '', 'create')) { ?>
                                <button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                                <?php } else {?>
                                <button type="button" class="btn btn-info saveBtn disabled" disabled style="margin-right: 25px;">Save</button>
                                <?php } ?>
                                
                                <?php if (has_permission_new('ClusterMaster', '', 'edit')) { ?>
                                <button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>
                                <?php } else {?>
                                <button type="button" class="btn btn-info updateBtn disabled" disabled style="margin-right: 25px;">Update</button>
                                <?php } ?>
                                
                                <button type="button" class="btn btn-default cancelBtn" style="margin-right: 25px;" >Cancel</button>
                            </div>
                        </div>
                    </div>
                    <!------------ Modal ------------->
                        <div class="modal fade cluster_list" id="cluster_list" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                <div class="modal-header" style="padding:5px 10px;">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Cluster List</h4>
                                </div>
                                <div class="modal-body" style="padding:0px 5px !important">
                                    
                                    <div class="">
                                        <table class="table table-striped table-bordered table-hover" id="table_warehouse_List" width="100%">
                                            <thead>
                                                <tr>
                                                    <th style="text-align:left;">AccountID </th>
                                                    <th style="text-align:left;">Cluster Name</th>
                                                    <th style="text-align:left;"> States</th>
                                                    <th style="text-align:left;"> Cities</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            
                                            foreach ($table_data as $key => $value) {
                                            ?>
                                                <tr onclick="fill_data(<?php echo $value['AccountID'];?>)">
                                                    <td><?php echo $value['AccountID'];?></td>
                                                    <td><?php echo $value['cluster'];?></td>
                                                    <td><?php echo $value['state_name'];?></td>
                                                    <td><?php echo $value['city_name'];?></td>
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
                            </div>
                        </div>
                    <!------------ Modal ------------->    
                </div>
            </div>
        </div>       
    </div>
</div>
<?php init_tail(); ?>
<script>
    $('#AccountID').on('keydown', function(e) {
        var keyCode = e.keyCode || e.which;
        var AccountID = $('#AccountID').val();
        if(keyCode == 9){
            e.preventDefault(); 
            $.ajax({
                url: "<?php echo admin_url(); ?>Cluster/getClusterDetails",
                method:"POST",
                dataType:"JSON",
                data:{
                    AccountID:AccountID,
                },
                success:function(data){
                    $('#AccountID').val(data.AccountID);
                    $('#cluster').val(data.cluster);
                    $('#state_name').val(data.state_name);
                    $('#city_name').val(data.city_name);
                    
                    var states = data.state.split(',');
                    var cities = data.city.split(',');
                    var vals = "";
                    var valc = "";
                    
                    states = states.slice(0,-1);
                    cities = cities.slice(0,-1);
                    
                    for(i=0;i<states.length;i++){
                        if(i!=states.length-1){
                            vals += states[i] +", ";
                        }
                        else{
                            vals += states[i];
                        }
                    }
                    for(j=0;j<cities.length;j++){
                        if(j!=cities.length-1){
                            valc += cities[j] +", ";
                        }
                        else{
                            valc += cities[j];
                        }
                    }
                    
                    console.log(vals.split(", "));
                    console.log(valc.split(", "));
                    
                    $('select[name=state]').selectpicker('val',vals.split(", "));
                    $('select[name=city]').selectpicker('val',valc.split(", "));
                    
                    var state_id = $('#state').val();
                    if(state != null){
                        $.ajax({
                            url : "<?php echo admin_url(); ?>Cluster/GetCityFromState",
                            type: "post",
                            data: {
                                state_id: state_id,
                            },
                            beforeSend: function () {
                                $('select[name=city]').empty();
                            },
                            success: function(data){
                                $('select[name=city]').append(data).selectpicker('refresh');
                                $('select[name=city]').selectpicker('val',valc.split(", "));
                            }
                        });
                    }
                    
                    $('.saveBtn').hide();
                    $('.updateBtn').show();
                    $('#cluster').blur();
                },
            });
        }
    });
</script>
<script>
    $('.updateBtn').click(function(){
        var AccountID = $('#AccountID').val();
        var cluster = $('#cluster').val();
        var states = $('#state').val();
        var cities = $('#city').val();
        
        var state_name = $('#state :selected').text();
        var city_name = $('.bootstrap-select button[data-id=city]').prop('title');
        
        $('#state_name').val(state_name);
        $('#city_name').val(city_name);
        
        if((cluster != '') && (states != '') && (cities != '')){
            $.ajax({
                url:"<?php echo admin_url(); ?>Cluster/updateCluster",
                method: "POST",
                dataType: "JSON",
                data:{
                    AccountID : AccountID,
                    cluster: cluster,
                    states: states,
                    state_name: state_name,
                    cities: cities,
                    city_name: city_name,
                },
                success:function(data){
                    if(data == true){
                        $('#AccountID').val('');
                        $('#cluster').val('');
                        $('select[name=state]').val('').selectpicker('refresh');
                        $('select[name=city]').val('').selectpicker('refresh');
                        alert('Cluster Updated Successful');
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
    function fill_data(AccountID){
        $('#cluster_list').modal('hide');
        $.ajax({
            url: "<?php echo admin_url(); ?>Cluster/getSingleCluster",
            method:"POST",
            dataType:"JSON",
            data:{
                AccountID:AccountID,
            },
            success:function(data){
                $('#AccountID').val(data.AccountID);
                $('#cluster').val(data.cluster);
                $('#state_name').val(data.state_name);
                $('#city_name').val(data.city_name);
                
                var states = data.state.split(',');
                var vals = "";
                states = states.slice(0,-1);
                for(i=0;i<states.length;i++){
                    if(i!=states.length-1){
                        vals += states[i] +", ";
                    }
                    else{
                        vals += states[i];
                    }
                }
                $('select[name=state]').selectpicker('val',vals.split(", "));

                var cities = data.city.split(',');
                cities = cities.slice(0,-1);
                var valc = "";
                for(j=0;j<cities.length;j++){
                    if(j!=cities.length-1){
                        valc += cities[j] +", ";
                    }
                    else{
                        valc += cities[j];
                    }
                }
                
                var state_id = $('#state :selected').val();
                if(state != null){
                    $.ajax({
                        url : "<?php echo admin_url(); ?>Cluster/GetCityFromState",
                        type: "post",
                        data: {
                            state_id: state_id,
                        },
                        success: function(c){
                            $('select[name=city]').append(c).selectpicker('refresh');
                            $('select[name=city]').selectpicker('val',valc.split(", "));
                        }
                    });
                }
                
                $('.saveBtn').hide();
                $('.updateBtn').show();
                $('#cluster').blur();
            },
        });
    }
</script>
<script>
    $('.cancelBtn').click(function(){
        $('#AccountID').val('');
        $('#cluster').val('');
        $('select[name=state]').val('').selectpicker('refresh');
        $('select[name=city]').val('').selectpicker('refresh');
        $('.saveBtn').show();
        $('.updateBtn').hide();
    });
</script>
<script>
    $(document).ready(function(){
        $('.saveBtn').show();
        $('.updateBtn').hide();
        $('#state_name').val('');
        $('#city_name').val('');
        
        $.ajax({
            url : "<?php echo admin_url(); ?>Cluster/GetState",
            type: "post",
            data: {
            },
            beforeSend: function () {
                $('select[name=city]').val('').selectpicker('refresh');
            },
            success: function(data){
                $('select[name=state]').html(data).selectpicker('refresh');
            }
          });
    });
</script>
<script>
    $('#state').change(function(){
        var state_id = $('#state :selected').val();
        var city = $('#city :selected').val();
        var cluster_id = $('#cluster_id').val();
        
        if(city != null){
            $.ajax({
                url: "<?php echo admin_url(); ?>Cluster/getSingleCluster",
                method:"POST",
                dataType:"JSON",
                data:{
                    cluster_id:cluster_id,
                },
                success:function(data){
                    var cities = data.city.split(',');
                    var valc = "";
                    
                    cities = cities.slice(0,-1);
                    
                    for(j=0;j<cities.length;j++){
                        if(j!=cities.length-1){
                            valc += cities[j] +", ";
                        }
                        else{
                            valc += cities[j];
                        }
                    }
                    
                    $('select[name=city]').selectpicker('val',valc.split(", "));
                    
                    var state_id = $('#state').val();
                    if(state != null){
                        $.ajax({
                            url : "<?php echo admin_url(); ?>Cluster/GetCityFromState",
                            type: "post",
                            data: {
                                state_id: state_id,
                            },
                            success: function(data){
                                $('select[name=city]').html(data).selectpicker('refresh');
                                $('select[name=city]').selectpicker('val',valc.split(", "));
                            }
                        });
                    }
                },
            });
        }
        else{
            $.ajax({
                url : "<?php echo admin_url(); ?>Cluster/GetCityFromState",
                type: "post",
                data: {
                    state_id: state_id,
                },
                beforeSend: function(){
                    $('select[name=city]').val('').selectpicker('refresh');
                },
                success: function(data){
                    $('select[name=city]').html(data).selectpicker('refresh');
                }
            });
        }
    });
</script>
<script>
    $('.saveBtn').click(function(){
        var AccountID = $('#AccountID').val();
        var cluster = $('#cluster').val();
        //var states = $('#state').val();
        //var cities = $('#city').val();
        var center_id = $('#centerName').val();
        
        //var state_name = $('.bootstrap-select button[data-id=state]').prop('title');
        //var city_name = $('.bootstrap-select button[data-id=city]').prop('title');
        var center_name = $('.bootstrap-select button[data-id=centerName]').prop('title');
        
        //$('#state_name').val(state_name);
        //$('#city_name').val(city_name);
        $('#center_name').val(center_name);
        
        if((cluster != '')){
            $.ajax({
                url:"<?php echo admin_url(); ?>Cluster/saveCluster",
                method: "POST",
                dataType: "JSON",
                data:{
                    AccountID: AccountID,
                    cluster: cluster,
                    // states: states,
                    // state_name: state_name,
                    // cities: cities,
                    // city_name: city_name,
                    centerName: center_name,
                    center_id: center_id
                },
                success:function(data){
                    if(data == true){
                        $('#AccountID').val('');
                        $('#cluster').val('');
                        // $('#state_name').val('');
                        // $('#city_name').val('');
                        // $('select[name=state]').val('').selectpicker('refresh');
                        // $('select[name=city]').val('').selectpicker('refresh');
                        alert('Cluster Created Successful');
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
    $('#AccountID').dblclick(function(){
        
        $('#cluster_list').modal('show');
        $('#myInput1').focus();
    });
</script>
<script>
    $('#AccountID').focus(function(){
        $('#AccountID').val('');
        $('#cluster').val('');
        $('#state_name').val('');
        $('#city_name').val('');
        $('select[name=state]').val('').selectpicker('refresh');
        $('select[name=city]').val('').selectpicker('refresh');
        $('.saveBtn').show();
        $('.updateBtn').hide();
    });
</script>

<script>
    $(document).ready(function(){
       $.ajax({
            url : "<?php echo admin_url(); ?>Cluster/GetCenterList",
            type: "post",
            data: {
            },
            beforeSend: function () {
                $('select[name=centerName]').val('').selectpicker('refresh');
            },
            success: function(data){
                $('select[name=centerName]').html(data).selectpicker('refresh');
            }
        });
    });
</script>
