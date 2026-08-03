<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-daily_report          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.table-daily_report thead th { position: sticky; top: 0; z-index: 1; }
.table-daily_report tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
</style>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
           
            <div class="panel_s">
               <div class="panel-body">
                   <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Warehouse</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Warehouse Reports</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
                  <div class="row ">
                    
                    <div class="col-md-2">
                        <div class="form-group" app-field-wrapper="AccoountName">
                            <label for="structure_type" class="selectpicker control-label">Structure Type</label>
                            <select required id="structure_type" name="structure_type" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                <option value="">Not Selected</option>
                                <option value="Dry Warehouse">Dry Warehouse</option>
                                <option value="Cold Storage">Cold Storage</option>
                                <option value="Open Plinth">Open Plinth</option>
                                <option value="CAP Storage">CAP Storage</option>
                                <option value="Open Shed">Open Shed</option>
                                <option value="Shed">Shed</option>
                                <option value="Silo">Silo</option>
                                <option value="Tank">Tank</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group" app-field-wrapper="AccountID">
                            <label for="state" class="control-label">State</label>
                            <select name="state" id="state" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group" app-field-wrapper="AccountID">
                            <label for="city" class="control-label">City</label>
                            <select name="city" id="city" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 19px;" id="search_data">Show</button>
                               
                </div>
                  <?php if(has_permission_new('customers','','view') || have_assigned_customers()) {
                     $where_summary = '';
                     if(!has_permission_new('customers','','view')){
                         $where_summary = ' AND userid IN (SELECT customer_id FROM '.db_prefix().'customer_admins WHERE staff_id='.get_staff_user_id().')';
                     }
                     ?>
                 
                  <?php } ?>
                  <hr class="hr-panel-heading" />
                 <div class="row">
                    <div class="col-md-6">
                        <div class="custom_button">&nbsp;&nbsp;
                        <?php if (has_permission_new('WarehouseReports', '', 'export')) {
                                ?>
                            <a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="table-daily_report" href="#" id="caexcel"><span>Export to excel</span></a>
                            <?php } ?>
                            
                        <?php if (has_permission_new('WarehouseReports', '', 'print')) {
                                ?>
                            <a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
                            <?php } ?>
                        <!--<a class="dt-button buttons-pdf buttons-html5" tabindex="0" aria-controls="ca_datatable" href="#"><span>Export to PDF</span></a>-->
                        </div>
                    </div>
                    <div class="col-md-6">
                            <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
                    </div>
                </div>
                     
            <div class="table-daily_report tableFixHead2">
             
                <table class="table table-striped table-bordered" width="100%">
                    <thead>
                        <tr>
                            <th>WHID</th>
                            <th>WH Name</th>
                            <th>Address</th>
                            <th>State</th>
                            <th>City</th>
                            <th>Total Capacity (MT)</th>
                            <th>Structure Type</th>
                        </tr>
                    </thead>
                    <tbody id="filter_table">
                        
                    </tbody>
                </table>    
            </div>
             <span id="searchh2" style="display:none;">
                                Loading.....
                            </span>
            <span id="searchh3" style="display:none;">Please wait data exporting...</span>
                  
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script>
    $(document).ready(function(){
       $.ajax({
            url : "<?php echo admin_url(); ?>Warehouse/GetState",
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

 $('#search_data').click(function(){
	    var city = $("#city :selected").val();
	    var structure_type = $("#structure_type :selected").val();
	    
    	$.ajax({
            url:"<?php echo admin_url(); ?>Warehouse/load_filter_data_warehouse",
            dataType:"json",
            method:"post",
            data:{
                city:city,
                structure_type:structure_type
            },
            beforeSend: function() 
            {
                $('#filter_table').html('');
            },
            success:function(data){
                $('#filter_table').html(data);
            },
        });
     
        
 });

 
</script>
<script>
    $('#state').change(function(){
        var state_id = $('#state').val();
        $.ajax({
            url : "<?php echo admin_url(); ?>Warehouse/GetCityFromState",
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
    });
</script>
</body>
</html>