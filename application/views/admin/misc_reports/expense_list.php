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
    
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
		 <div class="panel_s">
           <div class="panel-body">
		    <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Misc Reports </b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Expense List</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
		    <div class="_buttons">
               
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
					<?php echo render_date_input('from_date', 'FROM', $from_date); ?>
				</div>
				<div class="col-md-2">
					<?php echo render_date_input('to_date', 'TO', $to_date); ?>
				</div>
				<div class="col-md-2">
					<div class="form-group" app-field-wrapper="Staff">
						<label for="Staff" class="form-label">Staff Name</label>
						<select name="Staff" id="Staff" class="selectpicker form-control"
							data-width="100%" data-none-selected-text="Non Selected" 
							data-live-search="true">
							<option value="">Non Selected</option>
							
							<?php
							foreach ($staff as $key => $value) {
								?>
								<option value="<?php echo $value['phonenumber']; ?>">
									<?php echo  $value['firstname'] . ' ' . $value['lastname']; ?>
								</option>
								<?php
							}
							?>
						</select>
					   
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group" app-field-wrapper="Category">
						<label for="Category" class="form-label">Category Name</label>
						<select name="Category" id="Category" class="selectpicker form-control"
							data-width="100%" data-none-selected-text="Non Selected" 
							data-live-search="true">
							<option value="">Non Selected</option>
							
							<?php
							foreach ($Category as $key => $value) {
								?>
								<option value="<?php echo $value['id']; ?>">
									<?php echo  $value['CategoryName']; ?>
								</option>
								<?php
							}
							?>
						</select>
					   
					</div>
				</div>
				
				
                <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 19px;"
                                id="search_data">Show
                </button>
		   
            </div>
			</br>
			   <div class="col-md-5">
			       <?php if (has_permission_new('expense_list', '', 'export')) {
                                    ?>
					<a class="btn btn-default buttons-excel buttons-html2" tabindex="0"
						aria-controls="table-trial_bal_report" href="#" id="caexcel"
						style="float: left ! important;"><span>Export to Excel</span></a>
					<?php } ?>
					
					<?php if (has_permission_new('expense_list', '', 'print')) {
                                    ?>
					<a class="btn btn-default"  style="margin-left: 10px;"href="javascript:void(0);" onclick="printPage();">Print</a>
					<?php } ?>

                </div>
				<div class="col-md-12">
					<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search .."
						title="Type in a name" style="float: right;">
               </div>
            
            <div class="expense_table tableFixHead2 row">

             </div>
            
           
            <span id="searchh2" style="display:none;">Loading.....</span>
            
        </div>
        </div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<style>
    #table-purchase_request td:hover {
    cursor: pointer;
}
#table-purchase_request tr:hover {
    background-color: #ccc;
}
</style>


<script>
    function awaiting(){
        alert("Awaiting Client Approval !");
    }
    function awaiting_for_broker(){
        alert("Awaiting for Broker Approval !");
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
            td = tr[i].getElementsByTagName("td")[0];
            td1 = tr[i].getElementsByTagName("td")[1];
            td2 = tr[i].getElementsByTagName("td")[2];
            td3 = tr[i].getElementsByTagName("td")[3];
            td4 = tr[i].getElementsByTagName("td")[4];
            td5 = tr[i].getElementsByTagName("td")[5];
            td6 = tr[i].getElementsByTagName("td")[6];
            td7 = tr[i].getElementsByTagName("td")[7];
            
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
                }else if(td4){
                    txtValue = td4.textContent || td4.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }else if(td5){
                    txtValue = td5.textContent || td5.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }else if(td6){
                    txtValue = td6.textContent || td6.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                }
                }
            }
            }     
            }
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
        heading_data += '<td style="text-align:center;"colspan="3"> Expense List</td>';
        heading_data += '</tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">'+html_filter_name+'</td>';
        heading_data += '</tr>';
        heading_data += '</tbody></table>';
        var print_data = stylesheet+heading_data+tableData
        newWin= window.open("");
        newWin.document.write(print_data);
        newWin.print();
        newWin.close();
    };
</script>

<script>
$("#caexcel").click(function(){
    var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
		var Staff = $("#Staff").val();
        var Category = $("#Category").val();
    $.ajax({
        url:"<?php echo admin_url(); ?>misc_reports/export_expenselist",
        method:"POST",
         data: { from_date: from_date, rate: rate, Staff: Staff, Category: Category },
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



<script>

    $('#search_data').on('click', function () {
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
		var Staff = $("#Staff").val();
        var Category = $("#Category").val();
		/*alert(Category);*/
            $.ajax({
                url: "<?php echo admin_url(); ?>Misc_reports/expensedata",
                dataType: "json",
                method: "POST",
                data: { from_date: from_date, to_date: to_date,Staff: Staff,Category: Category,},
                beforeSend:function () {
                    $('.tableFixHead2').html();
                    $('#searchh22').css('display', 'none');
                    $('#searchh2').css('display', 'block');
                },
                complete: function () {
                    $('#searchh2').css('display', 'none');
                },
                success: function (data) {
                    $('.tableFixHead2').html(data);
                    
                }
            });
        
    });


</script>


</body>
</html>
