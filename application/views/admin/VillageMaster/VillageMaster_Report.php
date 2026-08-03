<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-daily_report {
        overflow: auto;
        max-height: 55vh;
        width: 100%;
        position: relative;
        top: 0px;
    }

    .table-daily_report thead th {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .table-daily_report tbody th {
        position: sticky;
        left: 0;
    }


    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,

    td {
        padding: 1px 5px !important;
        white-space: nowrap;
        border: 1px solid !important;
        font-size: 11px;
        line-height: 1.42857143 !important;
        vertical-align: middle !important;
    }

    th {
        background: #50607b;
        color: #fff !important;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-10">

                <div class="panel_s">
                    <div class="panel-body">

                        <div class="row ">
                            <?php
                            $fy = $this->session->userdata('finacial_year');
                            $fy_new = $fy + 1;
                            $lastdate_date = '20' . $fy_new . '-03-31';
                            $firstdate_date = '20' . $fy_new . '-04-01';
                            $curr_date = date('Y-m-d');
                            $curr_date_new = new DateTime($curr_date);
                            $last_date_yr = new DateTime($lastdate_date);
                            if ($last_date_yr < $curr_date_new) {
                                $to_date = '31/03/20' . $fy_new;
                                $from_date = '01/03/20' . $fy_new;
                            } else {
                                $from_date = "01/" . date('m') . "/" . date('Y');
                                $to_date = date('d/m/Y');
                            }
                            ?>
                            <div class="col-md-2">
                                <?php echo render_date_input('from_date', 'From', $from_date); ?>
                            </div>

                            <div class="col-md-2">
                                <?php echo render_date_input('to_date', 'To', $to_date); ?>
                            </div>                         
                            <div class="col-md-3">                               
                               <label for="Account_district">District Name</label>
                               <select name="Account_district" id="Account_district" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                    <option value="">All</option> 
                                    <?php foreach ($CityList as $val): ?>
                                        <option value="<?php echo $val["DistrictId"]; ?>"><?php echo $val["city_name"]; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>                                    
                          <div class="col-md-2">
								<label for="Account_taluka">Taluka Name</label>
								<select name="Account_taluka" id="Account_taluka" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
									<option value="">Select Taluka</option>
								</select>
							</div>							
                           
                            <div class="col-md-3">                               
                               <label for="Staff_Id">Created By</label>
                               <select name="Staff_Id" id="Staff_Id" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                    <option value="">All</option> 
                                    <?php foreach ($StaffList as $val): ?>
                                        <option value="<?php echo $val["UserID"]; ?>"><?php echo $val["firstname"]." ".$val["lastname"]; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
							</div>
							   <div class="row ">
							 <div class="col-md-3">                               
                               <label for="Staff_Id">Representative Staff</label>
                               <select name="Repr_Staff" id="Repr_Staff" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                    <option value="">All</option> 
                                    <?php foreach ($ReprStaff as $val): ?>

                                        <option value="<?php echo $val["AssignStaff"]; ?>"><?php echo $val["assignee_firstname"]." ".$val["assignee_lastname"]; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                          
                            
                            <div class="col-md-6" style="margin-top:10px;">
                                <?php
                                    if (has_permission_new('VillageReport', '', 'view')) {
                                ?>
                                    <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 10px;"
                                        id="search_data">Show</button>
                                <?php
                                    }
                                ?>
                                <?php
                                    if (has_permission_new('VillageReport', '', 'export')) {
                                ?>
                                    <a class="btn btn-default buttons-excel buttons-html5" style="margin-top: 10px;" tabindex="0" aria-controls="table-purchase_request" href="#" id="caexcel"><span>Export to excel</span></a>
                                <?php
                                    }
                                ?>
                                <?php
                                    if (has_permission_new('VillageReport', '', 'print')) {
                                ?>
                                    <a class="btn btn-default" href="javascript:void(0);"
                                    style="margin-top: 10px;margin-left:10px;" onclick="printPage();">Print</a>
                                <?php
                                    }
                                ?>
                            </div>
                            <div class="col-md-3" style="margin-top:17px;">
                                <input type="text" class="form-control" id="myInput1" onkeyup="searchTable()"
                                    placeholder="Search for names..." title="Type in a name" style="float: right;">
                            </div>
                        </div>
                  
                        <hr class="hr-panel-heading" />
                        <div class="row">
                           
                            <div class="col-md-6"></div>

                        </div>
                        <div class="table-daily_report tableFixHead2 row">

                        </div>
                        <span id="searchh2" style="display:none;">Loading.....</span>
                        <span id="searchh3" style="display:none;">Please wait data exporting...</span>
						
							<!-- View Village Modal -->
<div class="modal fade" id="viewVillageModal" tabindex="-1" role="dialog" aria-labelledby="viewVillageModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Village Representative Staff Assing</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p><strong>Village Name:</strong> <span id="village_name_view"></span></p>
        <p><strong>State:</strong> <span id="state_name_view"></span></p>
        <p><strong>City:</strong> <span id="city_name_view"></span></p>
        <p><strong>Taluka:</strong> <span id="taluka_name_view"></span></p>
        <p><strong>Pincode:</strong> <span id="pincode_view"></span></p>
        <p><strong>Sarpanch:</strong> <span id="sarpanch_view"></span></p>
       <p><strong>Assign Staff:</strong> <span id="staffassign_select"></span></p>
	    
        <!-- Assign Staff Dropdown -->
       <div class="form-group">
  <label for="staff_assign_select">Assign Staff:</label>
  <input type="hidden" id="village_id_hidden" />
  <select class="selectpicker form-control col-md-6"
          id="staff_assign_select"
          data-live-search="true"
          data-none-selected-text="-- Select Staff --">
    <option value="">-- Select Staff --</option>
    <!-- Staff options will be dynamically loaded here -->
  </select>
</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="assignStaffBtn" class="btn btn-primary">Assign Staff</button>
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
<?php init_tail() ?>
<script>
 function myCustomFunction(id) {

    $.ajax({
        url: '<?php echo admin_url(); ?>VillageMaster/get_village_details',
        type: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function (res) {
		console.log("Clicked ID:", res.id);
            $('#village_id_hidden').val(res.id);
            $('#village_name_view').text(res.VillageName);
            $('#state_name_view').text(res.state_name);
            $('#city_name_view').text(res.city_name);
            $('#taluka_name_view').text(res.TalukaName);
            $('#pincode_view').text(res.Pincode);
            $('#sarpanch_view').text(res.VillageSarpanch);		
			$('#staffassign_select').text(res.assignee_firstname + ' ' + res.assignee_lastname);
			

            // Show the modal
            $('#viewVillageModal').modal('show');

            // Fetch the list of staff for assignment
            $.ajax({
                url: '<?php echo admin_url(); ?>VillageMaster/get_all_staff',
                type: 'GET',
                dataType: 'json',
					success: function(data) {
						var $select = $('#staff_assign_select');
						$select.empty().append('<option value="">-- Select Staff --</option>');
						
						$.each(data, function(index, staff) {
						  $select.append('<option value="' + staff.AccountID + '">' + staff.firstname + ' ' + staff.lastname + '</option>');
						});

						$select.selectpicker('refresh'); // Refresh the selectpicker
					  },
                error: function () {
                    alert('Failed to fetch staff data.');
                }
            });
        },
        error: function () {
            alert('Failed to fetch village details.');
        }
    });
	
  }

  $(document).ready(function() {
    $('#Account_district').change(function() {
	 $("#Account_taluka").val("");
        var districtId = $(this).val();

        if (districtId != "") {
            $.ajax({
                url: "<?php echo admin_url(); ?>VillageMaster/getTalukas",
                type: "POST",
                data: {district_id: districtId},
                dataType: "json",
                success: function(response) {
					var $taluka = $('#Account_taluka');
					
					// Clear existing options and add the placeholder
					$taluka.empty().append('<option value="">Select Taluka</option>');
					
					// Add new options
					$.each(response, function(index, item) {
						$taluka.append('<option value="' + item.id + '">' + item.TalukaName + '</option>');
					});

					// Refresh selectpicker if it's used
					$taluka.selectpicker('refresh');
				}
            });
        } else {
            $('#Account_taluka').html('<option value="">Select Taluka</option>');
        }
    });
	
	$('#assignStaffBtn').on('click', function () {
    var villageId = $('#village_id_hidden').val(); // Store village ID in hidden input
    var selectedStaffId = $('#staff_assign_select').val();

    if (!selectedStaffId) {
        alert('Please select a staff member.');
        return;
    }

    $.ajax({
        url: '<?php echo admin_url(); ?>VillageMaster/assign_staff',
        type: 'POST',
        data: {
            village_id: villageId,
            staff_id: selectedStaffId
        },
        success: function (response) {
            // alert('Staff assigned successfully!');
            $('#viewVillageModal').modal('hide');
			 location.reload(); 
        },
        error: function () {
            alert('Failed to assign staff.');
        }
    });
  });
	
    function searchTable() 
    {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.querySelector(".table-daily_report table");
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

   $('#search_data').on('click', function () 
    {
        var from_date = $("#from_date").val();      
        var to_date = $("#to_date").val();        
        var Account_district = $("#Account_district").val();  
		var Account_taluka = $("#Account_taluka").val();	
        var Staff_Id = $("#Staff_Id").val();
		var Repr_Staff = $("#Repr_Staff").val();	

        $.ajax({
            url: "<?php echo admin_url(); ?>VillageMaster/load_village_data",
            dataType: "json",
            method: "POST",
            data: { from_date: from_date, to_date: to_date, Account_district: Account_district, Account_taluka: Account_taluka, Staff_Id:Staff_Id, Repr_Staff: Repr_Staff},
            beforeSend: function () {
                $('.tableFixHead2').html('');
                $('#searchh22').css('display', 'none');
                $('#searchh2').css('display', 'block');
            },
            complete: function () {
                $('#searchh2').css('display', 'none');
            },
            success: function (data) {
                $('.tableFixHead2').html(data.html);
            }
        });
    });
});	
    
</script>
<script src="<?= base_url() ?>public/plugins/jquery.table2excel.js"></script>

<script>
    function newexportaction(e, dt, button, config) 
    {
        var self = this;
        var oldStart = dt.settings()[0]._iDisplayStart;
        dt.one('preXhr', function (e, s, data) {
            // Just this once, load all data from the server...
            data.start = 0;
            data.length = 2147483647;
            dt.one('preDraw', function (e, settings) {
                // Call the original action function
                if (button[0].className.indexOf('buttons-copy') >= 0) {
                    $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                    $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                    $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                    $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-print') >= 0) {
                    $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
                }
                dt.one('preXhr', function (e, s, data) {
                    // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                    // Set the property to what it was before exporting.
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;

                });
                // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                setTimeout(dt.ajax.reload, 0);
                // Prevent rendering of the full data to the DOM
                return false;
            });
        });
        // Requery the server with the new one-time export settings
        dt.ajax.reload();
    }
</script>
<script type="text/javascript">
    function printPage() 
    {  
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();        
        var Account_district = $("#Account_district").val();       
        var Staff_Id = $("#Staff_Id").val();  

        $.ajax({
            url: "<?php echo admin_url(); ?>VillageMaster/load_village_data",
            dataType: "json",
            method: "POST",
            data: { from_date: from_date, to_date: to_date, Account_district: Account_district,Staff_Id:Staff_Id},
            success: function (data) {              

                var htmlString = data.html2;                      
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = htmlString;

                var districtMatch = htmlString.match(/District:\s*([a-zA-Z\s]+)/);
                var staffIDMatch = htmlString.match(/Staff ID:\s*([a-zA-Z\s]+)/);

                var fromDateMatch = htmlString.match(/From Date:\s*(\d{2}\/\d{2}\/\d{4})/);
                var toDateMatch = htmlString.match(/To Date:\s*(\d{2}\/\d{2}\/\d{4})/);             
                fromDate = 'From Date: ' + fromDateMatch[1];
                toDate = 'To Date: ' + toDateMatch[1];              
                
                var districtText = '';
                var staffIDText = '';

                if (districtMatch) {
                    districtText = 'District: ' + districtMatch[1].trim(); 
                } else {
                    districtText = '';
                }
                
                if (staffIDMatch) {
                    staffIDText = 'Staff ID: ' + staffIDMatch[1].trim(); 
                } else {
                    staffIDText = '';
                }

                var parts = [];               
                if (fromDate) {
                    parts.push(fromDate);
                }               
                if (toDate) {
                    parts.push(toDate);
                }                
                if (districtText) {
                    parts.push(districtText);
                }               
                if (staffIDText) {
                    parts.push(staffIDText);
                }               
                var filterString = parts.join(' , ');

                var htmlString2 = $('.report_for').html();    
                var stylesheet = `
                    <style type="text/css">
                        /* Ensure table and its cells have proper borders */
                        table {
                            border-collapse: collapse;
                            width: 100%;
                            margin: 0;
                        }
                        th, td {
                            padding: 5px;
                            border: 1px solid black;  /* Uniform border for all cells */
                            text-align: center;
                        }
                        /* No additional emphasis on the last row or last cell */
                        tr:last-child td {
                            border-bottom: 1px solid black;  /* Ensuring the last row has a border */
                        }
                    </style>`;

                var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">' + htmlString + '</table>';       
                var heading_data = '<table border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
                heading_data += '<tr>';
                heading_data += '<td style="text-align:center;"colspan="3">Village Details Report</td>';
                heading_data += '</tr>';  
                heading_data += '<tr>';
                heading_data += '<td style="text-align:center;"colspan="3">' + filterString  + '</td>';
                heading_data += '</tr>';     
                heading_data += '</tbody></table>';
                var print_data = stylesheet + heading_data + tableData
                newWin = window.open("");
                newWin.document.write(print_data);
                newWin.print();
                newWin.close();
            }
        });
    
      
    };    
</script>

<script>
    $("#caexcel").click(function () 
    {        
        var from_date = $("#from_date").val();      
        var to_date = $("#to_date").val();        
        var Account_district = $("#Account_district").val();  
        var Account_district_text = $("#Account_district option:selected").text();
		var Account_taluka = $("#Account_taluka").val();	
		var Account_taluka_text = $("#Account_taluka option:selected").text();
        var Staff_Id = $("#Staff_Id").val();
        var Staff_Id_text = $("#Staff_Id option:selected").text();
		var Repr_Staff = $("#Repr_Staff").val();   
		var Repr_Staff_text = $("#Repr_Staff option:selected").text();
        
        $.ajax({
            url: "<?php echo admin_url(); ?>VillageMaster/export_villageDetailslist",
            method: "POST",
            data: { from_date: from_date, to_date: to_date, Account_district: Account_district,Account_district_text:Account_district_text,Account_taluka_text:Account_taluka_text,Staff_Id_text:Staff_Id_text,Repr_Staff_text:Repr_Staff_text, Account_taluka: Account_taluka, Staff_Id:Staff_Id, Repr_Staff: Repr_Staff},
            success: function (response) {
				
                 response = JSON.parse(response);
                // response = JSON.parse(data.html2);
               window.location.href = response.site_url + response.filename;
            }
        });
    });
</script>

</body>
</html>

<style>
    #table-daily_report td:hover {
    cursor: pointer;
}
#table-daily_report tr:hover {
    background-color: #ccc;
}
</style>