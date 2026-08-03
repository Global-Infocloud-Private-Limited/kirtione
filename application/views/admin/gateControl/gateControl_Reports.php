<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-purchase_request          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.table-purchase_request thead th { position: sticky; top: 0; z-index: 1; }
.table-purchase_request tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
td:hover {
    cursor: pointer;
}
tr:hover {
    background-color: #ccc;
}
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
		 <div class="panel_s">
		        <?php
                    $from_date = "01/".date('m')."/".date('Y');
                    $to_date = date('d/m/Y');
                ?> 
        <div class="panel-body">
		    <div class="_buttons">
		        <div class="col-md-12 text-centerr"  >
					<nav aria-label="breadcrumb" >
						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
							<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
							<li class="breadcrumb-item active" aria-current="page"><b>Gate Control</b></li>
							
						</ol>
					</nav>
					<hr class="hr_style" style="margin-Bottom:12px !important;">
				</div>
                <div class="col-md-2">
                    <?php echo render_date_input('from_date','From',$to_date); ?>
                </div>
                <div class="col-md-2">
                    <?php echo render_date_input('to_date','To',$to_date);  ?>
                </div>
                
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="ItemID">
                        <label for="ItemID" class="form-label">Item Name</label>
                        <select name="ItemID" id="ItemID" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <?php
                                foreach($items as $value){
                                    ?>
                                        <option value="<?php echo $value['ItemID']; ?>" ><?php echo $value['ItemName']; ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="Center">
                        <label for="Center" class="form-label">Center</label>
                        <select name="Center" id="Center" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <?php
                                foreach($centers as $value){
                                    ?>
                                        <option value="<?php echo $value['CenterID']; ?>" ><?php echo $value['CenterName']; ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2" >
                    <div class="form-group" app-field-wrapper="TType">
                        <label for="TType" class="form-label">Booking Type</label>
                        <select name="TType" id="TType" class="selectpicker form-control" data-live-search="true">
                            <option value="">All</option>
                            <option value="P" >Purchase</option>
                            <option value="D">Deposit</option> 
                            <option value="W" >Withdrawal</option>
                            <option value="A" >Anamat</option>
                            <option value="T" >Trade Finance</option>
                            <option value="S" >Sell</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group" app-field-wrapper="FeildOfficer">
                        <small class="req text-danger">* </small>
                        <label for="FeildOfficer" class="control-label">Select Field Officer</label>
                        <select name="FeildOfficer" id="FeildOfficer" class="selectpicker form-control" data-live-search="true">
                            <option value="" >Non Selected</option>
                        <?php
                            foreach($StaffList as $key=>$val){
                        ?>
                                <option value="<?php echo $val["AccountID"];?>" <?php if($val["AccountID"] == $details->FeildOfficer){ echo "selected";}?>><?php echo $val["firstname"]." ".$val["lastname"];?></option>
                        <?php
                            }
                        ?>
						</select>
					</div>
				</div>
				
				<div class="col-md-2">
				    <div class="form-group" app-field-wrapper="villagename">
				        <label for="villagename" class="control-label">Village Name</label>
                        <select name="villagename" id="villagename" class="selectpicker form-control" data-live-search="true" title="None Selected">
                            <?php
                                foreach($village_detail as $value){
                                    ?>
                                        <option value="<?php echo $value['id']; ?>" ><?php echo $value['VillageName']; ?></option>
                                    <?php
                                }
                            ?>
						</select>
				    </div>
				</div>
                
                <div class="clearfix"></div>
                
                <div class="col-md-8">
                    <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 10px;margin-right:10px;" id="search_data">Show</button>
                    <div class="custom_button">
                        <?php if (has_permission_new('GateControl_Reports', '', 'export')) {
                        ?>
                        <a class="btn btn-default buttons-excel buttons-html5"  style="margin-top: 10px;"  tabindex="0" aria-controls="table-purchase_request" href="#" id="caexcel"><span>Export to excel</span></a>
                        <?php } ?>
                        
                        <?php if (has_permission_new('GateControl_Reports', '', 'print')) {
                        ?>
                        <a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 10px;margin-left:10px;"  onclick="printPage();">Print</a>
                        <?php } ?>
                    </div>
                </div>
                
                <div class="col-md-4" style="margin-top:8px;">
                    <input type="text" id="myInput1" onkeyup="myFunction2()" class="form-control" placeholder="Search.." title="Search" style="float: right;width:100%">
                </div>
                <br>
            </div>
            
            <div class="clearfix mtop20"></div>
            <div class="table-purchase_request tableFixHead2" >
              <table class="table-purchase_request tree table table-bordered " id="table-purchase_request" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:left;">Sr No.</th>
                        <th style="text-align:left;">AccountID</th>
                        <th style="text-align:left;">Party Name</th>
                        <th style="text-align:left;">Village Name</th>
                        <th style="text-align:left;">BookingID</th>
                        <th style="text-align:left;">Vehicle Arrival Date</th>
                        <th style="text-align:left;">ASN ID</th>
                        <th style="text-align:left;">Gate Pass No</th>
                        <th style="text-align:left;">GatePass Date</th>
                        <th style="text-align:left;">Truck No.</th>
                        <th style="text-align:left;">TType</th>
                        <th style="text-align:left;">ItemID</th>
                        <th style="text-align:left;">Item Name</th>
                        <th style="text-align:left;">Net Weight (MT)</th>
                        <th style="text-align:left;">Field Officer Name</th>
                        <th style="text-align:left;">Status</th>
                    </tr>
                </thead>
                <tbody id="filter_data_table">
                    
                </tbody>
              </table>   
            </div>
            <span id="search" style="display:none;">Please Wait fetching data</span>
            <span id="search1" style="display:none;">Please Wait exporting data</span>
            
            <div class="modal fade" id="modifyModal" tabindex="-1" role="dialog" aria-labelledby="modifyModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="padding:5px 10px;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Modify Trade</h4>
                    </div>
                  <div class="modal-body">
                    <input type="text" id="modal_id" hidden>
                    <input type="text" id="AccountID" hidden>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>BookingID</td>
                                <th>Item Name</td>
                                <th>Party Type</td>
                                <th>Party Name</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" id="modal_bookingid" class="form-control" style="border:none;background-color:#fff;" readonly></td>
                                <td><input type="text" id="modal_item" class="form-control" style="border:none;background-color:#fff;" readonly></td>
                                <td><input type="text" id="modal_party_type" class="form-control" style="border:none;background-color:#fff;" readonly></td>
                                <td><input type="text" id="modal_party" class="form-control" style="border:none;background-color:#fff;" readonly> </td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="modal_quantity" class="form-label">Quantity</label>
                            <input type="text" id="modal_quantity" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="modal_unit" class="form-label">Unit</label>
                            <select name="modal_unit" id="modal_unit" class="selectpicker form-control" data-live-search="true">
                                <option value="Bags">Bags</option>
                                <option value="Quintal">Quintal</option> 
                                <option value="Ton">Ton</option> 
                            </select>
                        </div>  
                        <div class="col-md-3" style="margin-top:2%">
                            <button type="button" id="Modify" class="btn btn-primary">Modify</button>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
        </div>
        </div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script>
$(document).ready(function(){
    
    
    $('#search_data').on('click',function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var TType = $("#TType :selected").val();
	    var ItemID = $("#ItemID :selected").val();
	    var CenterID = $("#Center :selected").val();
	    var FeildOfficer = $("#FeildOfficer :selected").val();
	    var villagename = $("#villagename").val();
	    $.ajax({
            url:"<?php echo admin_url(); ?>GateControl/GetFilterDataGateControl",
            method:"POST",
            data:{from_date:from_date, to_date:to_date, TType:TType,ItemID:ItemID,CenterID:CenterID,FeildOfficer:FeildOfficer,villagename:villagename},
            beforeSend: function () {
                $('#filter_data_table').html('');
                $('#search').css('display','block');
            },
            complete: function () {
                $('#search').css('display','none');
            },
            success:function(data){
                if(data != ''){
                    $('#filter_data_table').html(data);
                }
                else{
                    $('#filter_data_table').html('<span style="color:red;">No records found...</span>');
                }
            }
        });
    });
});
</script>
<script>
    function fill_data(id){
        window.open("<?php echo admin_url(); ?>GateControl/GateControl_Reports_Details/"+id,'_blank');
    }
</script>
<script>
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table-purchase_request");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) 
        {
            tr[i].style.display = "none"; 
            td = tr[i].getElementsByTagName("td"); 
            for (j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;                
                    if (txtValue.toUpperCase().indexOf(filter.toUpperCase()) > -1) {
                        tr[i].style.display = "";  
                        break; 
                    }
                }
            }
        }
    }
 </script>
 
<script type="text/javascript">
    function printPage()
    {
        var html_filter_name =    $('.report_for').html();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Inward List</td>';
        heading_data += '</tr>';
        /*heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">'+html_filter_name+'</td>';
        heading_data += '</tr>';*/
        heading_data += '</tbody></table>';
        var print_data = stylesheet+heading_data+tableData
        newWin= window.open("");
        newWin.document.write(print_data);
        newWin.print();
        newWin.close();
    };
</script>
 <script>
    $(document).ready(function(){
    var maxEndDate = new Date('Y/m/d');
    var fin_y = "<?php echo $this->session->userdata('finacial_year')?>";
    
    var year = "20"+fin_y;
    var cur_y = new Date().getFullYear().toString().substr(-2);
    if(cur_y => fin_y){
        var year2 = parseInt(fin_y) + parseInt(1);
        var year2_new = "20"+year2;
        var e_dat = new Date(year2_new+'/03/31');
        var maxEndDate_new = e_dat;
    }else{
        var e_dat2 = new Date(year2+'/03/31');
        var maxEndDate_new = e_dat2;
    }
    
    var minStartDate = new Date(year, 03);
    $('#from_date').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false
    });
    
    $('#to_date').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false,
        showOtherMonths: false,
        pickTime: false,
            orientation: "left",
    });
    
    });
</script>

<script>
    $("#caexcel").click(function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var TType = $("#TType :selected").val();
	    var ItemID = $("#ItemID :selected").val();
	    var CenterID = $("#Center :selected").val();
	    var FeildOfficer = $("#FeildOfficer :selected").val();
	    var villagename = $("#villagename :selected").val();
	    
	    var Bookingidtext = $("#TType option:selected").text();
	    var ItemNametext = $("#ItemID option:selected").text();
	    var centertext =  $("#Center option:selected").text();
	    var fieldofficertext =  $("#FeildOfficer option:selected").text();
	    var villagenametext =  $("#villagename option:selected").text();
	    
        $.ajax({
            url:"<?php echo admin_url(); ?>GateControl/export_gateControllist",
            method:"POST",
             data:{from_date:from_date, to_date:to_date, TType:TType,ItemID:ItemID,CenterID:CenterID,FeildOfficer:FeildOfficer,villagename:villagename,Bookingidtext:Bookingidtext,
                ItemNametext:ItemNametext,centertext:centertext,fieldofficertext:fieldofficertext,villagenametext:villagenametext
            },
            beforeSend: function () {
                $('#search1').css('display','block');
            },
            complete: function () {
                $('#search1').css('display','none');
            },
            success:function(data){
                response = JSON.parse(data);
                window.location.href = response.site_url+response.filename;
            }
        });
    });


</script>  
</body>
</html>
