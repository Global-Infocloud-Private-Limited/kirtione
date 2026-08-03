<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>

#AccountID {
    text-transform: uppercase;
}
#table_comp_mandi_List td:hover {
    cursor: pointer;
}
#table_comp_mandi_List tr:hover {
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
    							<li class="breadcrumb-item active" aria-current="page"><b>Competitor Master</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
                        <div class="row">
                            <div class="col-md-12">
                                <!--<h4 style="margin-top:-10px;">Competitor / Mandi Master</h4>-->
                                <!--<hr>-->
                                <div class="searchh2" style="display:none;">Please wait while fetching data.</div>
                                <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                                <div class="searchh4" style="display:none;">Please wait while updating data.</div>
                            </div>
                            <br>
                            <br>
                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="CompetitorID">
                                    <small class="req text-danger">* </small>
                                    <label for="CompetitorID" class="control-label">CompetitorID</label>
                                    <input type="text" id="CompetitorID" name="CompetitorID" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="Competitor">
                                    <input id="region_id" value="" hidden>
                                    <small class="req text-danger">* </small>
                                    <label for="Competitor" class="control-label">Competitor Name</label>
                                    <input type="text" id="Competitor" name="Competitor" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group"><small class="req text-danger">* </small> 
        						<label for="Type" class="control-label">Select Type</label>
        						<select class="selectpicker" name="Type" data-live-search="true" id="Type" data-width="100%">
        						    <option value="">Non Selected</option>
        						    <option value="C">Competitor</option>
        						    <option value="M">Mandi</option>
        						</select>
        						</div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                
                                <?php if (has_permission_new('Competitor', '', 'create')) {
                                ?>
                                    <button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                                <?php
                                }else{
                                ?>
                                    <button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
                                <?php
                                }?>
                                
                                <?php if (has_permission_new('Competitor', '', 'edit')) {
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
                        <div class="modal fade Comp_mandiList" id="Comp_mandiList" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                <div class="modal-header" style="padding:5px 10px;">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Competitor / Mandi List</h4>
                                </div>
                            </br>
                                <div class="modal-body" style="padding:0px 5px !important">
                                <div class="col-md-5">
                                    <?php if (has_permission_new('Competitor', '', 'export')) {
                                    ?>
                                        <a class="btn btn-default buttons-excel buttons-html2" tabindex="0"
                                            aria-controls="table-trial_bal_report" href="#" id="caexcel"
                                            style="float: left ! important;"><span>Export to Excel</span></a>
                                            <?php } ?>
                                            
                                    <?php if (has_permission_new('Competitor', '', 'print')) {
                                    ?>
                                        <button class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</button>
                                        <?php } ?>
                               </div>
                            </br>
                                    <div class="">
                                        <table class="table table-striped table-bordered table-hover" id="table_comp_mandi_List" width="100%">
                                            <thead>
                                                <tr>
                                                    <th style="text-align:left;">Sr.</th>
                                                    <th style="text-align:left;">CompetitorID</th>
                                                    <th style="text-align:left;">Competitor Name</th>
                                                    <th style="text-align:left;">Type</th>
                                                </tr>
                                            </thead>
                                            <tbody id="ListTableBody">
                                            
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
    $('#CompetitorID').on('blur', function() {
        var CompetitorID = $('#CompetitorID').val(); 
        if(CompetitorID != ""){
            $.ajax({
                url: "<?php echo admin_url(); ?>Competitor/getSingleCompetitor",
                method:"POST",
                dataType:"JSON",
                data:{
                    CompetitorID:CompetitorID,
                },
                success:function(data){
                    $('#CompetitorID').val(data.CompetitorID);
                    $('#Competitor').val(data.Competitor);
                    $('#Type').val(data.Type);
                    $("#Type").selectpicker("refresh");
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
        $('#Type').val('');
        $("#Type").selectpicker("refresh");
        $('.saveBtn').show();
        $('.updateBtn').hide();
        $('.saveBtn2').show();
        $('.updateBtn2').hide();
    });
</script>
<script>
    $(document).ready(function(){
        $('.saveBtn').show();
        $('.updateBtn').hide();
        $('.saveBtn2').show();
        $('.updateBtn2').hide();
    });
</script>
<script>
    $('.saveBtn').click(function(){
        var CompetitorID = $('#CompetitorID').val();
        var Competitor = $('#Competitor').val();
        var Type = $('#Type').val();
        if((CompetitorID != '') && (Competitor != '') && (Type != '')){
            $.ajax({
                url:"<?php echo admin_url(); ?>Competitor/saveCompetitor",
                method: "POST",
                dataType: "JSON",
                data:{
                    CompetitorID: CompetitorID,
                    Competitor: Competitor,
                    Type:Type
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
                        $('input').val('');
                        $('#Type').val('');
                        $("#Type").selectpicker("refresh");
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                        alert('Competitor/Mandi Created Successfully');
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
        var CompetitorID = $('#CompetitorID').val();
        var Competitor = $('#Competitor').val();
        var Type = $('#Type').val();
        if((CompetitorID != '') && (Competitor != '')){
            $.ajax({
                url:"<?php echo admin_url(); ?>Competitor/updateCompetitor",
                method: "POST",
                dataType: "JSON",
                data:{
                    CompetitorID: CompetitorID,
                    Competitor: Competitor,
                    Type:Type
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
                        $('input').val('');
                        $('#Type').val('');
                        $("#Type").selectpicker("refresh");
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                        alert('Competitor/Mandi Updated Successfully');
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
    $('#CompetitorID').dblclick(function(){
        $('#Comp_mandiList').modal('show');
        $('#Comp_mandiList').on('shown.bs.modal', function () {
            $('#focusInput').val('');
              $('#focusInput').focus();
        })
        var AccountID = "";
            $.ajax({
                  url:"<?php echo admin_url(); ?>Competitor/AccountListPopUp",
                  //dataType:"JSON",
                  method:"POST",
                  cache: false,
                  data:{AccountID:AccountID,},
                  success:function(data){
                    if(empty(data)){
                        
                    }else{
                        $("#ListTableBody").html(data);
                         
                        $('.get_AccountID').click(function(){ 
                            CompetitorID = $(this).attr("data-id");
							$.ajax({
    							url:"<?php echo admin_url(); ?>Competitor/getSingleCompetitor",
    							dataType:"JSON",
    							method:"POST",
    							data:{CompetitorID:CompetitorID},
    							
    							success:function(data){
    								$('#CompetitorID').val(data.CompetitorID);
                                    $('#Competitor').val(data.Competitor);
                                    $('#Type').val(data.Type);
                                    $("#Type").selectpicker("refresh");
                                    $('.saveBtn').hide();
                                    $('.updateBtn').show();
                                    $('.saveBtn2').hide();
                                    $('.updateBtn2').show();
    							 }
							})  
                            $('#Comp_mandiList').modal('hide');
                        });
                    }
                  }
            });
    });
</script>

<script>
    $('#CompetitorID').focus(function(){
        $('input').val('');
        $('#Type').val('');
        $("#Type").selectpicker("refresh");
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
  table = document.getElementById("table_comp_mandi_List");
  tr = table.getElementsByTagName("tr");
   for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
      td1 = tr[i].getElementsByTagName("td")[1];
      td2 = tr[i].getElementsByTagName("td")[2];
    if (td) {
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
      }else{
           tr[i].style.display = "none";
      } 
    }
    }    
  }
}
}
 </script>
 
<script type="text/javascript">
    function printPage() {
        var html_filter_name = $('.report_for').html();
        var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">' + document.getElementsByTagName('table')[0].innerHTML + '</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Competitor / Mandi List Report</td>';
        heading_data += '</tr>';
        

        heading_data += '</tbody></table>';
        var print_data = stylesheet + heading_data + tableData
        newWin = window.open("");
        newWin.document.write(print_data);
        newWin.print();
        newWin.close();
    };
</script>
<script>
$("#caexcel").click(function(){
    var data_val = "data";
    $.ajax({
        url:"<?php echo admin_url(); ?>Competitor/export_CompetitoMandiListMaster",
        method:"POST",
        data:{data_val:data_val,},
        beforeSend: function () {
            $('#searchh3').css('display','block');
        },
        complete: function () {
            $('#searchh3').css('display','none');
        },
        success:function(data){
            response = JSON.parse(data);
            window.location.href = response.site_url+response.filename;
        }
    });
});


</script> 