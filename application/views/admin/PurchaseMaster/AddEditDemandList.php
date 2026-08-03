<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
				echo form_open($this->uri->uri_string(),array('id'=>'pur_order-form','class'=>'_transaction_form'));
			?>
			
			<div class="col-md-10">
				<div class="panel_s accounting-template estimate">
					<div class="row">
						<div class="col-md-12"> 
							<div class="panel-body">
								<nav aria-label="breadcrumb" >
									<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
										<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
										<li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
										<li class="breadcrumb-item active" aria-current="page"><b>Demand List</b></li>
									</ol>
								</nav>
								<hr class="hr_style">
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane active" id="general_infor">
										<div class="row">
											<div class="col-md-4"> 
												<?php $value = (isset($demandlist) ? $demandlist->CenterID : ''); ?>						
												<div class="form-group" app-field-wrapper="centername">
													<label for="centername" class="control-label">Center Name</label>
													<select name="centername" id="centername" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                                        <option value=""></option>   
                                                        <?php
                                                        foreach ($centermaster as $center) {
                                                            $selected = ($value == $center['CenterID']) ? 'selected' : '';
                                                            echo '<option value="' . $center['CenterID'] . '" ' . $selected . '>' . $center['CenterName'] . '</option>';
                                                        }
                                                        ?>                                                                                                                              
                                                    </select>
												</div>
											</div>  
										</div>
										
										
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body mtop10">
						<div class="row col-md-12">
							<p class="bold p_style"><?php echo _l('Add Demand List'); ?></p>
							<hr class="hr_style"/>
							<div class="" id="example">
							</div>
							<?php echo form_hidden('pur_order_detail'); ?>
							
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 mtop15">
							<div class="panel-body bottom-transaction">
								
								<div class="btn-bottom-toolbar text-right" style="width: 100%; display: flex; justify-content: flex-end; align-items: center;">
									
									<div class="col-md-1" style="margin-left: 0px; margin-right: -30px;">
										<a href="#" class="btn btn-default reset-new-order" id="resetbutton">Reset</a>						
									</div>	
									
									<div class="col-md-1" style="margin-left: 10px;">
										<a href="#" class="btn btn-warning edit-new-order">View List</a>
									</div>
									<?php if (has_permission_new('DemandList', '', 'create')){
									?>	
									<button type="submit" id="savebtn" class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit">
										<?php echo _l('submit'); ?>
									</button>
									<?php
									}
									?>	
									
									<?php if (has_permission_new('DemandList', '', 'edit')){
										?>	
										<button type="submit" id="updatebtn" class="btn-tr save_detail btn btn-info mleft5 estimate-form-submit transaction-submit" style="display:none;"> UPDATE</button>
										<?php
									}
									?>	
								</div>
							</div>
							<div class="btn-bottom-pusher"></div>
						</div>
					</div>
				</div>
				
			</div>
			<?php echo form_close(); ?>
			
		</div>
	</div>
</div>
</div>

<div class="modal fade" id="transfer-modal">
	<div class="modal-dialog modal-lg" style=" max-width: 1230px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Demand List</h4>
			</div>
			<div class="modal-body" style="padding:5px;">
				<?php
                    $fy = $this->session->userdata('finacial_year');
                    $fy_new  = $fy + 1;
                    $lastdate_date = '20'.$fy_new.'-03-31';
                    $firstdate_date = '20'.$fy_new.'-04-01';
                    $curr_date = date('Y-m-d');
                    $curr_date_new    = new DateTime($curr_date);
                    $last_date_yr = new DateTime($lastdate_date);
                    if($last_date_yr < $curr_date_new){
                        $to_date = '31/03/20'.$fy_new;
                        $from_date = '01/03/20'.$fy_new;
						}else{
                        $from_date = "01/".date('m')."/".date('Y');
                        $to_date = date('d/m/Y');
					}
				?> 
				<div class="row">
					<div class="col-md-2">
						<?php echo render_date_input('from_date','From Date',$from_date); ?>
					</div>
					<div class="col-md-2">
						<?php echo render_date_input('to_date','To Date',$to_date);?>
					</div>
					<div class="col-md-3">                               
                       <label for="center">Center Name</label>
                       <select name="center" id="center" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                            <option value="">All</option> 
                            <?php foreach ($centermaster as $val): ?>
                                <option value="<?php echo $val["CenterID"]; ?>"><?php echo $val["CenterName"]; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>   
					<div class="col-md-2">
						<br>
						<button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button>
					</div>
					
					<div class="col-md-3">
					    <br>
                        <input type="text" class="form-control" id="myInput1" onkeyup="myFunction2()"
                            placeholder="Search.." title="Type in a name" style="float: right;">
                    </div>
				
					<div class="col-md-12">
						<div class="table_purchase_report">
							<table class="tree table table-striped table-bordered table_purchase_report" id="table_purchase_report" width="100%">
								<thead>
									<tr style="display:none;">
										<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
									</tr>
									<tr>
										<th style="width:5% ">Sr.No</th>
										<th style="width:5% text-align:left;">Date</th> 
										<th style="width:15% ">Center Name</th>
										<th style="width:15% text-align:left;">Item Name</th>
										<th style="width:5% text-align:left;">Qty</th> 
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>   
						</div>
						<span id="searchh2" style="display:none;">Loading.....</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>

<style>
    .table_purchase_report { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
	.table_purchase_report thead th { position: sticky; top: 0; z-index: 1; }
	.table_purchase_report tbody th { position: sticky; left: 0; }
	
	/* Just common table stuff. Really. */
	.table_purchase_report table  { border-collapse: collapse; width: 100%; }
	.table_purchase_report th, td { padding: 3px 3px !important; white-space: nowrap;font-size:11px; line-height:1.42857143;vertical-align: middle;}
	.table_purchase_report th     { background: #50607b;color: #fff !important; }
	
	
	#table_purchase_report tr:hover {
    background-color: #ccc;
	}
	
	#table_purchase_report td:hover {
    cursor: pointer;
	}
</style>

<script>
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.querySelector(".table_purchase_report");
        tr = table.getElementsByTagName("tr");
		
        for (i = 2; i < tr.length; i++) {
            var tdArray = tr[i].getElementsByTagName("td");
            var rowContainsSearchTerm = false;
            for (var j = 0; j < tdArray.length; j++) {
                td = tdArray[j];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        rowContainsSearchTerm = true;
                        break;
					}
				}
			}
            if (rowContainsSearchTerm) {
				
                tr[i].style.display = "";
				} else {
                tr[i].style.display = "none";
			}
		}
	}
</script>
<script>
    $(document).ready(function() 
	{
        $('#resetbutton').on('click', function(e) {
            e.preventDefault();  
			window.location.href = '<?php echo admin_url(); ?>PurchaseMaster/AddEditDemandList';  
		});		
		
		var url = window.location.href;
        var urlParts = url.split('/');
        var lastPart = urlParts[urlParts.length - 1];
       
        
        if ($.isNumeric(lastPart)) {
            // In Edit Mode
            $('#savebtn').hide();
            $('#updatebtn').show();
        } else {
            // In Create Mode
            $('#savebtn').show();
            $('#updatebtn').hide();
        }
	});
	
	function load_data(from_date,to_date,centername)
	{
		$.ajax({
			url:"<?php echo admin_url(); ?>PurchaseMaster/load_data_for_demandlist",            
			method:"POST",
			data:{from_date:from_date, to_date:to_date,centername:centername},
			beforeSend: function () {
				$('#searchh2').css('display','block');
				$('.table_purchase_report tbody').css('display','none');
			},
			complete: function () {
				$('.table_purchase_report tbody').css('display','');
				$('#searchh2').css('display','none');
			},
			success:function(data){
				$('.table_purchase_report tbody').html(data);
			}
		});
	}
	
	$('#search_data').on('click',function(){
		var from_date = $("#from_date").val();
		var to_date = $("#to_date").val();	    
		var centername  = $("#center").val();
		
		load_data(from_date,to_date,centername);
	});
		
	$('.edit-new-order').on('click', function(){
		$('#transfer-modal').find('button[type="submit"]').prop('disabled', false);
		$('#transfer-modal').modal('show');
		
		var from_date = $("#from_date").val();
		var to_date = $("#to_date").val();	    
		load_data(from_date,to_date);
	});	
</script>
</html>
<?php require 'kirtione_demandlist_js.php';?>



