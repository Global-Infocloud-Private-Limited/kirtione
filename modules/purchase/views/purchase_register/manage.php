<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
              <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Misc Reports </b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Purchase Register</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
    					
              <div class="_buttons">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="act_name">A/c no</label>
                            <input type="text" name="act_name" id="act_name" class="form-control" value="<?php echo $value; ?>" <?php if(isset($cd_notes_details)){ echo "disabled";} ?>>
                                        
                        </div>
                    </div>
                    <div class="col-md-4">
                        <br>
                        <div class="form-group">
                        <input type="text" name="account_full_name" id="account_full_name" class="form-control" value="<?php echo $value; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="act_name">Item ID</label>
                            <input type="text" name="item_code" id="item_code" style="width: 100%;border-radius: 2px;height: 30px;">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <br>
                            <div class="form-group">
                            <input type="text" name="item_fill_name" id="item_fill_name" class="form-control" value="<?php echo $value; ?>">
                            </div>
                        </div>
                    </div>
                 
                
                <div class="row"> 
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
                       <div class="col-md-2">
                           
                            <?php echo render_date_input('from_date','FROM',$from_date);  ?>
                        </div>
                        
                        <div class="col-md-2">
                            
                            <?php echo render_date_input('to_date','TO',$to_date); ?>
                        </div>
                        
                        <div class="col-md-2">
                            <br>
                            <div class="form-group">
                           <select name="report_type" id="report_type" class="form-control">
                               <option value="1">Detailed</option>
                               <option value="2">Summary</option>
                               <option value="3">ItemDetails</option>
                           </select>
                           </div>
                        </div>
                    <div class="col-md-1">
                    <br>
                    <div class="custom_button">
                        <button class="btn btn-info pull-left mleft5 search_data" id="search_data" style="font-size:12px;">Show</button>
                    </div>
                </div>
                <div class="col-md-2">
                    <br>
                    <div class="custom_button">
                    <?php if (has_permission_new('purchase_register', '', 'export')) {
                    ?>
                    <a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="table-daily_report" href="#" id="caexcel"><span>Export to excel</span></a>
                    <?php } ?>
                    <!--<a class="dt-button buttons-pdf buttons-html5" tabindex="0" aria-controls="ca_datatable" href="#"><span>Export to PDF</span></a>-->
                    </div>
                </div>   
                <div class="col-md-1">
                    <br>
                    <?php if (has_permission_new('purchase_register', '', 'print')) {
                    ?>
                    <a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
                    <?php } ?>
                </div>
                <!--<div class="col-md-2">
                    <br>
                    <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: left;width: 100%;">
                </div>-->
                
        
             </div>
        
               
            </div>
            <div class="clearfix"></div>
            
        <?php
        //print_r($company_detail);
        ?>
        <span id="searchh3" style="display:none;">Please wait exporting data...</span>
            <div class="fixTableHead load_data">
              
            </div>
            <span id="searchh" style="display:none;">Loading.....</span>
    
            
          </div>
</div>
</div>
</div>
</div>
</div>
<style>

.fixTableHead  { overflow: auto;max-height: 50vh;width:100%;position:relative;top: 0px; }
.fixTableHead thead th { position: sticky; top: 0; z-index: 1; }
.fixTableHead tbody th { position: sticky; left: 0; }

/* Just common table stuff. Really. */
.fixTableHead table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
.fixTableHead th     { background: #50607b;color: #fff !important; }
</style>

<?php init_tail(); ?>
<!--new update -->
<script src="<?= base_url() ?>public/plugins/jquery.table2excel.js"></script>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
    

// Initialize For Account
     $( "#act_name" ).autocomplete({
        
        source: function( request, response ) {
          // Fetch data
          
          $.ajax({
            url: "<?=base_url()?>admin/purchase/accountlist",
            type: 'post',
            dataType: "json",
            data: {
              search: request.term
            },
            success: function( data ) {
              response( data );
            }
          });
        },
        select: function (event, ui) {
          
          
          $('#act_name').val(ui.item.value); // display the selected text
          $('#account_full_name').val(ui.item.label); // display the selected text
          
            return false;      
            
        }
      });
    
    $('#act_name').on('blur',function(){
         
        var act_id = $(this).val();
        $.ajax({
          url:"<?php echo admin_url(); ?>purchase/get_account_details",
          dataType:"JSON",
          method:"POST",
          cache: false,
          data:{act_id:act_id,},
          
          success:function(data){
            var ItemID = $('#item_code').val();
            if (data == 0 || data == null){
                if(act_id !== ""){
                   alert("AccountID not found..."); 
                }
                $('#act_name').val('');
                $('#account_full_name').val('');
                if(ItemID == ""){
                    $("#report_type").children().remove();
                    // APPEND OR INSERT DATA TO SELECT ELEMENT.
                    $('#report_type').append('<option value="1">Detailed</option>');
                    $('#report_type').append('<option value="2">Summary</option>');
                    $('#report_type').append('<option value="3">ItemDetails</option>');
                    $("#report_type").selectpicker("refresh");
                }
                
            }else{
                $('#account_full_name').val(data.company);
                
                $("#report_type").children().remove();
                    // APPEND OR INSERT DATA TO SELECT ELEMENT.
                $('#report_type').append('<option value="3">ItemDetails</option>');
                             
                $("#report_type").selectpicker("refresh");
                $('#search_data').focus();
                
            }
              
            $('#account_full_name').val(data.company);
           $('#search_data').focus();
            
          }
        });
        
     })
     
    $('#item_code').on('blur',function(){
         
        var ItemID = $(this).val();
        $.ajax({
          url:"<?php echo admin_url(); ?>purchase/get_item_details",
          dataType:"JSON",
          method:"POST",
          cache: false,
          data:{ItemID:ItemID,},
          
          success:function(data){
            var AccounID = $('#act_name').val();
            if (data == 0 || data == null){
                if(ItemID !== ""){
                    alert("ItemID not found...");
                }
                
                $('#item_code').val('');
                $('#item_fill_name').val('');
                if(AccounID == ""){
                    $("#report_type").children().remove();
                    // APPEND OR INSERT DATA TO SELECT ELEMENT.
                    $('#report_type').append('<option value="1">Detailed</option>');
                    $('#report_type').append('<option value="2">Summary</option>');
                    $('#report_type').append('<option value="3">ItemDetails</option>');
                    $("#report_type").selectpicker("refresh");
                }
                
            }else{
                $('#item_fill_name').val(data.description);
                
                $("#report_type").children().remove();
                    // APPEND OR INSERT DATA TO SELECT ELEMENT.
                $('#report_type').append('<option value="3">ItemDetails</option>');
                             
                $("#report_type").selectpicker("refresh");
                $('#search_data').focus();
                
            }
            
            
          }
        });
        
     })
     
    $('#report_type').on('change',function(){
         
        var report_type = $(this).val();
        
        if(report_type == 3){
            var ItemID = $('#item_code').val();
            var AccounID = $('#act_name').val();
            if(ItemID == ""){
                alert("Enter ItemID"); 
                    $("#report_type").children().remove();
                    // APPEND OR INSERT DATA TO SELECT ELEMENT.
                    $('#report_type').append('<option value="1">Detailed</option>');
                    $('#report_type').append('<option value="2">Summary</option>');
                    $('#report_type').append('<option value="3">ItemDetails</option>');
                    $("#report_type").selectpicker("refresh");
                    //$('#item_code').focus();
            }
        }
    });
      // Initialize For Account
     $( "#item_code" ).autocomplete({
         
        source: function( request, response ) {
          // Fetch data
          
          $.ajax({
            url: "<?=base_url()?>admin/purchase/itemlist",
            type: 'post',
            dataType: "json",
            data: {
              search: request.term
            },
            success: function( data ) {
              response( data );
            }
          });
        },
        select: function (event, ui) {
          
          
          $('#item_code').val(ui.item.value); // display the selected text
          $('#item_fill_name').val(ui.item.label); // display the selected text
          $('#search_data').focus();
            return false;      
            
        }
      });
 
 $('#search_data').on('click',function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var report_type = $("#report_type").val();
	    var accountID = $("#act_name").val();
	    var accountName = $("#account_full_name").val();
	    var ItemID = $("#item_code").val();
	    var Itemname = $("#item_fill_name").val();
	    
	        $.ajax({
          url:"<?php echo admin_url(); ?>purchase/get_purchase_data",
          dataType:"JSON",
          method:"POST",
          cache: false,
          data:{from_date:from_date, to_date:to_date, report_type:report_type,accountID:accountID,ItemID:ItemID,accountName:accountName,Itemname:Itemname},
          beforeSend: function () {
                   
            $('#searchh').css('display','block');
            $('.load_data').css('display','none');
            
         },
          complete: function () {
            $('.load_data').css('display','');
            $('#searchh').css('display','none');
         },
          success:function(data){
                $('.load_data').html(data);
          }
        });
	    
 });
});

 $("#caexcel").click(function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var report_type = $("#report_type").val();
	    var accountID = $("#act_name").val();
	    var accountName = $("#account_full_name").val();
	    var ItemID = $("#item_code").val();
	    var Itemname = $("#item_fill_name").val();
	    
	    $.ajax({
            url:"<?php echo admin_url(); ?>purchase/export_purchase_register",
            method:"POST",
            data:{from_date:from_date, to_date:to_date, report_type:report_type,accountID:accountID,ItemID:ItemID,accountName:accountName,Itemname:Itemname},
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
<style>
    input[type=checkbox], input[type=radio] {
    margin: 4px 4px 0px;
    line-height: normal;
}
</style>
<script type="text/javascript">
 function printPage(){
      
	var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} .print_item_h{ background: #505f7b;colr:#fff;} </style>';
    var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
    var print_data = stylesheet+tableData
   newWin= window.open("");
   newWin.document.write(print_data);
   newWin.print();
   newWin.close();
    };
 </script>
<script>
    function myFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  
  filter = input.value.toUpperCase();
  table = document.getElementById("daily_report");
  tr = table.getElementsByTagName("tr");
  for (i = 4; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    td1 = tr[i].getElementsByTagName("td")[1];
    td2 = tr[i].getElementsByTagName("td")[2];
    td3 = tr[i].getElementsByTagName("td")[3];
    td4 = tr[i].getElementsByTagName("td")[4];
    td5 = tr[i].getElementsByTagName("td")[5];
    td6 = tr[i].getElementsByTagName("td")[6];
    td7 = tr[i].getElementsByTagName("td")[7];/*
    td8 = tr[i].getElementsByTagName("td")[8];
    td9 = tr[i].getElementsByTagName("td")[9];
    td10 = tr[i].getElementsByTagName("td")[10];
    td11 = tr[i].getElementsByTagName("td")[11];*/
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td1) {
      txtValue = td1.textContent || td1.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td2) {
      txtValue = td2.textContent || td2.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td3) {
      txtValue = td3.textContent || td3.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td4) {
      txtValue = td4.textContent || td4.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td5) {
      txtValue = td5.textContent || td5.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td6) {
      txtValue = td6.textContent || td6.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td7) {
      txtValue = td7.textContent || td7.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }/*else if(td8) {
      txtValue = td8.textContent || td8.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td9) {
      txtValue = td9.textContent || td9.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td10) {
      txtValue = td10.textContent || td10.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td11) {
      txtValue = td11.textContent || td11.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }*/ else {
        tr[i].style.display = "none";
      }
    }       
  }
}}}//}}}}
}
}
}
}
}
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


