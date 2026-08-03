<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
	   	<div class="row">
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">
					   	<!--<div class="row">-->
					   	
					   	<nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Admin</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>News</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
						
					<!--<form method="post" id="news_upload_form" align="center" enctype="multipart/form-data">-->
						<div class="row">
							<div class="col-md-12">
								<div class="form-group" app-field-wrapper="news_title">
									<label for="title" class="control-label" style="float: left;">News Title</label>
									<input type="text" id="news_title" name="news_title" class="form-control" autocomplete="off" />
								</div>
							</div>
						</div>
						<input id="newsid" value="0" hidden>
						
						<div class="row" style="padding-top: 11px;">
						    <div class="col-md-4">
								<div class="form-group" app-field-wrapper="category">
									<label for="news_category" class="form-label" style="float: left;">News Category</label>
									<select name="news_category" id="news_category" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">Not Selected</option>
										<option value="1">Agriculture</option>
										<option value="2">Weather</option>
										<option value="3">Government Schemes</option>
										<option value="4">Inventions</option>
										<option value="5">Other</option>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group" app-field-wrapper="category">
									<label for="language" class="form-label" style="float: left;">Language</label>
									<select name="language" multiple = "[]" id="language" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">Not Selected</option>
										<option value="english">English</option>
										<option value="marathi">Marathi</option>
										<option value="hindi">Hindi</option>
										<option value="gujarati">Gujarati</option>
										<option value="kannada">Kannada</option>
										<option value="malayalam">Malayalam</option>
										<option value="tamil">Tamil</option>
										<option value="telgu">Telugu</option>
									</select>
								</div>
							</div>
							<div class="col-md-4" >
                                <div class="form-group" app-field-wrapper="active">
                                    <label for="status" class="form-label" style="float: left;">Status</label>
                                    <select name="status" id="status" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="1" >Active</option>
                                        <option value="0" >InActive</option> 
                                    </select>
                                </div>
                            </div>
						</div>

                        
					    <div class="row" style="padding-top: 11px;">
							<div class="col-md-12">
								<div class="form-group" app-field-wrapper="news_description">
									<label for="description" class="form-label" style="float: left;">News Description</label>
									<textarea name="news_description" id="news_description" rows="9" class="form-control" placeholder="description" aria-label="news_description" ></textarea>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-6">
								<div class="form-group" app-field-wrapper="news_image">
									<label for="news_image" class="form-label" style="float: left;">Upload News Image</label>
									<!--<input type="file" name="news_image" id="news_image" style="" class="form-control" />-->
								 <!--   <span><a href ="<?php echo $imgValue;?>" target="_blank"><img class="card-img-bottom" src="<?php echo $imgValue;?>"  style="margin-left:15px;max-width:80px;" alt="news image" /></a></span>-->
								 <input type="file" class="form-control" name="news_image" id="news_image" />
								</div>
							</div>
						</div>
							
						<div class="row">
								<div class="col-md-12">
								    <?php
								        if (has_permission_new('news', '', 'create')) {
								    ?>
								        <button class="btn btn-info pull-left mleft5 save_data" id="save_data" onclick="saveData()" style="font-size:12px;padding:8px 15px;margin:0px 10px;">Save</button>
								    <?php
                                        }
                                        if (has_permission_new('news', '', 'edit')) {
                                    ?>
                                        <button class="btn btn-info pull-left mleft5 updateData" id="update_data" onclick="saveData()" style="font-size:12px;padding:8px 15px;margin:0px 10px; display:none;">Update</button>  
                                    <?php
                                        }
								    ?>
									<button type="button" class="btn btn-default cancelBtn" style="margin:0px 10px" >Cancel</button>
								</div>
							</div>
						<!--</div>-->
					<!--</form>-->
						<!--<div class="clearfix"></div>-->
					<!--</div>-->
					<div class="modal fade News" id="newsModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                            <div class="modal-header" style="padding:5px 10px;">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">News List</h4>
                            </div>
                            <div class="modal-body" style="padding:0px 5px !important">
                                <div class="table-News tableFixHead2">
                                    <table class="tree table table-striped table-bordered table-News tableFixHead2" id="table_News" width="100%">
                                        <thead>
                                            <tr style="display:none;">
                                                <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                            </tr>
                                            <tr>
                                                <th style="text-align:left; display:none;">ID </th>
                                                <th style="text-align:left;">News Title</th>
                                                <th style="text-align:left;">News Category</th>
                                                <th style="text-align:left;">Status</th>
                                                <th style="text-align:left;">Language</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($allNews as $key => $value) {
                                        ?>
                                            <tr class="get_NewsID" data-id="<?php echo $value["id"]; ?>">
                                                <td><?php echo $value["title"];?></td>
                                                <?php if($value["category"] == 1) { ?>
                                                    <td>Agriculture</td>
                                                <?php } else if($value["category"] == 2){ ?>
                                                    <td>Weather</td>
                                                <?php } else if($value["category"] == 3){ ?>
                                                    <td>Government Schemes</td>
                                                <?php } else if($value["category"] == 4){ ?>
                                                    <td>Inventions</td>
                                                <?php } else if($value["category"] == 5){ ?>
                                                    <td>Other</td>
                                                <?php } ?>
                                                
                                                <?php if($value["status"] == 1) { ?>
                                                    <td>Active</td>
                                                <?php } else { ?>
                                                    <td>InActive</td>
                                                <?php } ?>
                                                <td><?php echo $value["language"];?></td>
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
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>

<script>

    function myFunction2() {
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("myInput1");
          filter = input.value.toUpperCase();
          table = document.getElementById("table_News");
          tr = table.getElementsByTagName("tr");
           for (i = 1; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
              td1 = tr[i].getElementsByTagName("td")[1];
              td2 = tr[i].getElementsByTagName("td")[2];
              td3 = tr[i].getElementsByTagName("td")[3];
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
    $('#news_title').on('focus',function(){
        $('#save_data').show();
        $('#update_data').hide();
        $('#news_title').val('');
        $('#news_description').val('');
        $('select[name=news_category]').val('');
        $('.selectpicker').selectpicker('refresh');
        $('select[name=status]').val('');
        $('.selectpicker').selectpicker('refresh');
        $('select[name=language]').val('');
        $('.selectpicker').selectpicker('refresh');
    })
    
    $('.cancelBtn').on('click',function(){
        $('#save_data').show();
        $('#update_data').hide();
        $('#news_title').val('');
        $('#news_description').val('');
        $('select[name=news_category]').val('');
        $('.selectpicker').selectpicker('refresh');
        $('select[name=status]').val('');
        $('.selectpicker').selectpicker('refresh');
        $('select[name=language]').val('');
        $('.selectpicker').selectpicker('refresh');
    })
    $("#news_title").dblclick(function(){
        $('#newsid').val('');
        $('#newsModal').modal('show');
        $('#newsModal').on('shown.bs.modal', function () {
            $('#myInput1').val('');
              $('#myInput1').focus();
        })
    });
    
    $('.get_NewsID').on('click',function(){
        $('#news_title').val('');
        $('#news_description').val('');
        $('#newsid').val('');
        $('#status').val('');
        $('#language').val('');
        news_id = $(this).attr("data-id");
        $.ajax({
              url:"<?php echo admin_url(); ?>News/fetchNewsDetails",
              dataType:"JSON",
              method:"POST",
              cache: false,
              data:{news_id:news_id,},
              success:function(data){
                if(empty(data)){
                    $('#save_data').show();
                    $('#update_data').hide();
                    $('#news_title').val('');
                    $('#news_description').val('');
                    $('select[name=news_category]').val('');
                    $('.selectpicker').selectpicker('refresh');
                    $('select[name=status]').val('');
                    $('.selectpicker').selectpicker('refresh');
                    $('select[name=language]').val('');
                    $('.selectpicker').selectpicker('refresh');
                }else{
                    $('#newsid').val(data.id);
                    $('#news_title').val(data.title);
                    $('#news_description').val(data.description);
                    
                    var select_status = document.getElementById("status");
                    select_status.value = data.status;
                    $('.selectpicker').selectpicker('refresh');
                    
                    var selectElement = document.getElementById("language");
                    var selectedLanguages = data.language;
                    
                    for (var i = 0; i < selectElement.options.length; i++) {
                        var option = selectElement.options[i];
                        if (option.value && selectedLanguages.includes(option.value)) {
                            option.selected = true;
                        } else {
                            option.selected = false; 
                        }
                    }
                    $('.selectpicker').selectpicker('refresh');
                    
                    var news_category = document.getElementById("news_category");
                    news_category.value = data.category;
                    $('.selectpicker').selectpicker('refresh');
            
                    $('#save_data').hide();
                    $('#update_data').show();
                }
                $('#newsModal').modal('hide');
              }
        });
        
    });
    
    function saveData(){
        // e.preventDefault();
        var newsTitle = $('#news_title').val();
        var newsID = $('#newsid').val();
        var newsDescription = $('#news_description').val();
        var newsCategory = $('#news_category').val();
        var newstatus = $('#status').val();
        var Language = $('#language').val();
        var newsImage = $('#news_image')[0].files[0];
        // var formData = new FormData($('#news_upload_form')[0]);
        var formData = new FormData();
        formData.append('newsID',newsID);
        formData.append('news_title',newsTitle);
        formData.append('news_description',newsDescription);
        formData.append('news_category',newsCategory);
        formData.append('status',newstatus);
        formData.append('language',Language);
        formData.append('news_image',newsImage);
        if(newsTitle == "" || newsDescription == "" || newsCategory == ""){
            alert('please enter all required fields');
        }else{
            $.ajax({
                url:"<?php echo admin_url(); ?>news/SaveNews",
                //dataType:"JSON",
                method:"POST",
                //data:{newsTitle:newsTitle,newsDescription:newsDescription,newsCategory:newsCategory,newsImage:newsImage},
                data:formData,
                contentType: false,  
                cache: false,  
                processData:false,  
                beforeSend: function () {
                    $('.save_data').css('display','block');
                    $('.updateData').css('display','none');
                },
                complete: function () {
                    $('.savedata').css('display','none');
                },
                success:function(data){
                    if(data == false){
                        alert('Something went wrong');
                        $('.load_data').html('');
                    }else{
                        $('#news_title').val('');
                        $('#news_description').val('');
                        $('#newsid').val('');
                        $('#status').val('');
                        $('#language').val('');
                        $('.selectpicker').selectpicker('refresh');
                        alert(data);
                        // window.location.reload();
                    }
                }
            });
        }
    }
</script>
<style>
    #table_News td:hover {
        cursor: pointer;
    }
    #table_News tr:hover {
        background-color: #ccc;
    }
    
        .table_News          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
        .table_News thead th { position: sticky; top: 0; z-index: 1; }
        .table_News tbody th { position: sticky; left: 0; }
        table  { border-collapse: collapse; width: 100%; }
        th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
        th     { background: #50607b;
        color: #fff !important; }
</style>