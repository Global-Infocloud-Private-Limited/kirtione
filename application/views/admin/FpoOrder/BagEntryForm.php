<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.hidden-button {
    display: none;
}
</style>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">                        						 
                            <div class="row"> 
                                <nav aria-label="breadcrumb" >
                                    <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                                        <li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                        <li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
                                        <li class="breadcrumb-item active" aria-current="page"><b>Bag Entry Form</b></li>
                                    </ol>
                                </nav>
                                <hr class="hr_style">

                                <div class="col-md-12">
                                    <div class="searchh2" style="display:none;">Please wait while fetching data.</div>                                    
                                    <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                                    <div class="searchh4" style="display:none;">Please wait while updating data.</div>
                                </div> 
                                
                            <?php if (has_permission('Bag_ledger', '', 'create')) {
                            ?>
                                <br>                              
                                <div class="col-md-4">
                                  <div class="form-group">
                                     <small class="req text-danger">* </small>
                                    <label for="fpolist">Select FPO</label>
                                    <select name="fpolist" id="fpolist" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" <?= $isFpoSelected ?>>
                                    <option value=""></option>
                                    <?php
                                        foreach($FPOStaffList as $key=>$val){
                                            ?>
                                                <option value="<?php echo $val["AccountID"]?>" <?php if($FPOID == $val["AccountID"]) echo 'selected';?> ><?php echo $val["firstname"]." ".$val["lastname"];?></option>
                                            <?php
                                        }
                                    ?>
                                    </select>
                                    <input type="hidden" name="fpo_list" id="fpo_list" value="<?php echo $FPOID;?>">
                                  </div>
                                </div>    
                                
                                <?php
                                    $fy = $this->session->userdata('finacial_year');
                                    $fy_new  = $fy + 1;
                                    $lastdate_date = '20'.$fy_new.'-03-31';
                                    $curr_date = date('Y-m-d');
                                    $curr_date_new    = new DateTime($curr_date);
                                    $last_date_yr = new DateTime($lastdate_date);
                                    if($last_date_yr < $curr_date_new){
                                        $date = $lastdate_date;
                                    }else{
                                        $date = date('Y-m-d');
                                    }
                                ?>
                                <div class="col-md-3">
                                    <?php echo render_date_input('bagdate','Date',_d($date)); ?>
                                </div>
                                
                                <div class="col-md-4">
                                  <div class="form-group">
                                     <small class="req text-danger">* </small>
                                    <label for="bagtype">Type</label>
                                    <select name="bagtype" id="bagtype" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" <?= $isFpoSelected ?>>
                                        <option value="Open">Open</option>
                                        <option value="Transfer">Transfer</option>
                                    </select>
                                  </div>
                                </div>  
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="bagqty">Bag Qty</label>
                                        <input type="text" name="bagqty" id="bagqty" class="form-control" value="">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="bal">Closing Bag Qty</label>
                                        <input type="text" name="bal" id="bal" class="form-control" value="" readonly>
                                    </div>
                                </div>	
                                
                                <div class="col-md-4">
                                    <?php
                                        if (has_permission_new('Bag_ledger', '', 'create')) {
                                    ?>
                                            <button type="button" class="btn btn-info saveBtn" style="margin-top: 20px;">Save</button>
                                    <?php
                                        }else{
                                    ?>
                                        <button type="button" class="btn btn-info saveBtn2 hidden-button" disabled style="margin-top: 20px;">Save</button> 
                                    <?php
                                        }
                                    ?>
                                    <?php
                                        if (has_permission_new('Bag_ledger', '', 'edit')) {
                                    ?>
                                            <button type="button" class="btn btn-info updateBtn hidden-button" style="margin-top: 20px;">Update</button> 
                                    <?php
                                        }else{
                                    ?>
                                        <button type="button" class="btn btn-info updateBtn2 hidden-button" disabled style="margin-top: 20px;">Update</button> 
                                    <?php
                                        }
                                    ?>                                                                
                                    <button type="submit" class="btn btn-default cancelBtn" style="margin-top: 20px;margin-left:5px;">Cancel</button>
                                </div>
                            
                            <?php } ?>
                            </div>	 
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-6">
			    <div class="panel_s">
			        <div class="panel-body">  
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
                    <input type="hidden" name="comp_name" id="comp_name" value="<?php echo $company_detail->company_name;?>">
					<input type="hidden" name="comp_addr" id="comp_addr" value="<?php echo $company_detail->address;?>">
					<h4>Bag Ledger</h4>
			        <div class="row">
                        <div class="col-md-3">
                            <?php
                                echo render_date_input('from_date','From',$from_date);
                            ?>
                        </div>
                        
                        <div class="col-md-3">
                            <?php
                                echo render_date_input('to_date','To',$to_date);
                            ?>
                        </div>
                        
                        <div class="col-md-4">
                          <div class="form-group">
                            <label for="fpolistfilter">Select FPO</label>
                            <select name="fpolistfilter" id="fpolistfilter" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="Non Selected" >
                            <option value=""></option>
                            <?php
                                foreach($FPOStaffList as $key=>$val){
                                    ?>
                                        <option value="<?php echo $val["AccountID"]?>"><?php echo $val["firstname"]." ".$val["lastname"];?></option>
                                    <?php
                                }
                            ?>
                            </select>
                          </div>
                        </div>
                        
                        <div class="col-md-6">
							<div class="custom_button">
								<button class="btn btn-info pull-left search_data" id="search_data" style="font-size:12px;margin-top: 10px;">Show</button>
								<?php if (has_permission_new('Bag_ledger', '', 'export')) {
								?>
								<a class="btn btn-default " tabindex="0" aria-controls="stock_position" href="#" id="caexcel" style="margin-left:5px;margin-top: 10px;"><span>Export to Excel</span></a>
								<?php } ?>
								
								<?php if (has_permission_new('Bag_ledger', '', 'print')) {
								?>
								<a class="btn btn-default" href="javascript:void(0);" onclick="printPage();" style="margin-left:5px;margin-top: 10px;">Print</a>
								<?php } ?>
							</div>
						</div>
						<div class="col-md-6" style="margin-top:1%;">
                            <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search.." title="Type in a name" style="float: right;">
                        </div>
			        </div>
			        
			        <div class="clearfix"></div>
			        
			        <div class="row">
						<div class="col-md-12">
                            <span id="searchh2" style="display:none;">Loading.....</span>
                            <div class="table_bag_report">
                            <table class="table table-bordered table-striped table_bag_report" id="table_bag_report">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Date</th>
                                        <th>Passed From</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Bal</th>
                                    </tr>
                                </thead>
                                <tbody id="bagLedgerBody">
                                    
                                </tbody>
                            </table>
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
$(document).ready(function() 
{  
    loadTable();
    
    //save new card name
    $('.saveBtn').on('click',function() 
    {
        FpoList = $('#fpolist').val(); 
        BagDate = $('#bagdate').val();
        BagType = $('#bagtype').val();
        BagQty =  $('#bagqty').val();
        if(FpoList == '')
        {
            alert('please select Fpo.');
            $('#fpolist').focus();
        }else if(BagType == ''){
            alert('please select Bag Type.');
            $('#bagtype').focus();
        }else if(BagQty == ''){
            alert('please enter Bag Qty.');
            $('#bagqty').focus();
        }
        else
        {
            $.ajax({
                url: "<?php echo admin_url(); ?>FpoOrder/AddBagLedger", 
                type: 'POST', 
                data: {FpoList:FpoList,BagDate:BagDate,BagType:BagType,BagQty:BagQty}, 
                dataType: 'json',
                success: function(response) {
                    if (response == true) {                   
                        alert_float('success', 'Record Created Successfully...');
                        ResetForm();
                        loadTable();
                    } else {                    
                        alert_float('warning', 'Something went wrong...');
                    }
                },
                error: function(xhr, status, error) {                
                    $('#messageContainer').text('An error occurred while processing the request').css('background-color', '#f44336').css('color', 'white').fadeIn();
                }
            });   
        }
    });

    $('.cancelBtn').on('click',function() 
    {
        ResetForm();	               
    });
    
    $('#search_data').on('click',function()
	{
	    loadTable();
	});

});

function loadTable() {
    var from_date = $("#from_date").val();
    var to_date = $("#to_date").val();
	var Fpolist = $("#fpolistfilter").val();
		
		$.ajax({
            url:"<?php echo admin_url(); ?>FpoOrder/GetFilterBagData",
            dataType:"html",
            method:"POST",
            data:{from_date:from_date,to_date:to_date,Fpolist:Fpolist},
            beforeSend: function () {
                $('#searchh2').css('display','block');
            },
            complete: function () {
                $('#searchh2').css('display','none');
            },
            success:function(data){
                $('#searchh2').hide();        
                $('#bagLedgerBody').html(data); 
            },
            error: function () {
                $('#searchh2').hide();
                $('#bagLedgerBody').html('<p class="text-danger">Failed to load data.</p>');
            }
        });
}

</script>

<script>
    function ResetForm()
    {
        $('#fpolist').val('').selectpicker('refresh');
        $('#bagtype').val('').selectpicker('refresh');
        
        const today = new Date();
        const dd = String(today.getDate()).padStart(2, '0');
        const mm = String(today.getMonth() + 1).padStart(2, '0'); 
        const yyyy = today.getFullYear();
    
        const formattedDate = `${dd}/${mm}/${yyyy}`;
        
        $('#bagdate').val(formattedDate);
        
        $('#bal').val('');
        $('#bagqty').val('');
        
        $('.saveBtn').show();
        $('.updateBtn').hide();	
        $('.saveBtn2').show();
        $('.updateBtn2').hide();
    }     
    
    function allowOnlyNumbersAndDecimal(id) {
        document.getElementById(id).addEventListener('input', function () {
            let value = this.value;
            value = value.replace(/[^0-9]/g, '');
            this.value = value;
        });
    }
    allowOnlyNumbersAndDecimal('bagqty');
</script>

<script type="text/javascript">
   function printPage() 
   {
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var comp_name = $("#comp_name").val();
        var comp_addr = $("#comp_addr").val();
        var FpoList = $("#fpolistfilter option:selected").text() || "ALL";
        
        var tableContent = document.querySelector('.table_bag_report'); 
        
        var tableHTML = tableContent.innerHTML;
        var stylesheet = `
            <style>
                body { font-size: 12px; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid black; padding: 5px; }
                .hide_in_print { display: none !important; }
            </style>
        `;

        var heading_data = `
            <table>
                <tr><td colspan="9" style="text-align:center;">${comp_name}</td></tr>
                <tr><td colspan="9" style="text-align:center;">${comp_addr}</td></tr>
                <tr><td colspan="9" style="text-align:center;">Filters:- &nbsp;From Date: ${from_date} &nbsp;To Date: ${to_date} &nbsp;FPO Name: ${FpoList}</td></tr>
            </table>
        `;
        
        var printContent = `
            ${stylesheet}
            ${heading_data}
            ${tableHTML}
        `;
        
        var newWin = window.open('', '', 'width=900,height=700');
        newWin.document.write('<html><head><title>Print</title></head><body>');
        newWin.document.write(printContent);
        newWin.document.write('</body></html>');
        newWin.document.close();
       
        newWin.onload = function () {
            newWin.focus();
            newWin.print();
            newWin.close();
        };
    }
</script>

<script>
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table_bag_report");
        tr = table.getElementsByTagName("tr");

        for (i = 1; i < tr.length; i++) {           
            tr[i].style.display = "none";           
            
            td = tr[i].getElementsByTagName("td");
            for (j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = ""; 
                break; 
                }
            }
            }
        }
    }
    
    $("#caexcel").click(function(){
	    var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var FpoList = $("#fpolistfilter").val();
	    var FpoListText = $("#fpolistfilter option:selected").text() || "ALL";
	    
		$.ajax({
			url:"<?php echo admin_url(); ?>FpoOrder/export_Bag_Report",
			method:"POST",
			data:{
		        from_date:from_date,to_date:to_date,FpoList:FpoList,FpoListText:FpoListText,
			},
			beforeSend: function () {
				$('#searchh2').css('display','block');
			},
			complete: function () {
				$('#searchh2').css('display','none');
			},
			success:function(data){
				response = JSON.parse(data);
				window.location.href = response.site_url+response.filename;
			}
		});
	});
</script>

<style>	
    #table_Brand_List td:hover {
        cursor: pointer;
    }
    #table_Brand_List tr:hover {
        background-color: #ccc;
    }
    .table_Brand_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table_Brand_List thead th { position: sticky; top: 0; z-index: 1; }
    .table_Brand_List tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>









