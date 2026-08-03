<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.hidden-button {
    display: none;
}

#FeatureTableBody {
    display: none; 
}

#tblcarddetails td:hover {
    cursor: pointer;
}

#tblcarddetails tr:hover {
    background-color: #ccc;
}
</style>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-9">
				<div class="panel_s">
					<div class="panel-body">                        						 
                            <div class="row"> 
                                <nav aria-label="breadcrumb" >
                                    <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                                        <li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                        <li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
                                        <li class="breadcrumb-item active" aria-current="page"><b>Card Master</b></li>
                                    </ol>
                                </nav>
                                <hr class="hr_style">

                                <div class="col-md-12">
                                    <div class="searchh2" style="display:none;">Please wait while fetching data.</div>                                    
                                    <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                                    <div class="searchh4" style="display:none;">Please wait while updating data.</div>
                                </div> 

                                <br>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="cardid">Prefix</label>
                                        <input type="text" name="prefix" id="cardid" class="form-control" value="" ondblclick="OpenEditModel()">                                        
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="farmer_card">Card Name</label>
                                        <input type="text" name="cardname" id="farmer_card" class="form-control" value="">
                                    </div>
                                </div>	                            

                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="cardvalidity">      
                                        <small class="req text-danger">* </small>                                  
                                        <label for="cardvalidity" class="form-label">Card Validity (Yrs)</label>
                                        <select name="" id="cardvalidity" class="selectpicker form-control" data-none-selected-text="Non Selected" data-width="100%" data-live-search="true">
                                            <option value=""></option>                                                 
                                            <?php for ($i=1;$i<=10;$i++) { ?>
                                                <option value="<?php echo $i;?>"><?php echo $i;?> yrs</option>  
                                            <?php } ?> 
                                        </select>
                                    </div>
                                </div>                          

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="cardfees">Card Fees</label>
                                        <input type="text" name="" id="cardfees" class="form-control" value="">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="renewalfees">Renewal Fees</label>
                                        <input type="text" name="" id="renewalfees" class="form-control" value="">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="welcomebonus">Welcome Bonus</label>
                                        <input type="text" name="" id="welcomebonus" class="form-control" value="">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="ptconversion">Point Conversion</label>
                                        <input type="text" name="" id="ptconversion" class="form-control" value="">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="interestrate">Interest Of Pay Later (%)</label>
                                        <input type="text" name="" id="interestrate" class="form-control" value="">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="ratebenefitmin">Rate Benefit/MT</label>
                                        <input type="text" name="" id="ratebenefitmin" class="form-control" value="">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="ratebenefitmax">Rate Benefit Upto</label>
                                        <input type="text" name="" id="ratebenefitmax" class="form-control" value="">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="reedemption">Redmption (%)</label>
                                        <input type="text" name="" id="reedemption" class="form-control" value="">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="soiltest">Soil Test</label>
                                        <input type="text" name="" id="soiltest" class="form-control" value="">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="soiltestdisc">Soil Test Discount (%)</label>
                                        <input type="text" name="" id="soiltestdisc" class="form-control" value="">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group" app-field-wrapper="status">
                                        <small class="req text-danger">* </small>
                                        <label for="status" class="form-label">Status</label>
                                    <select name="" id="status" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value=""></option> 
                                        <option value="Y">Active</option>
                                        <option value="N">Deactive</option>                                       
                                    </select>
                                    </div>
                                </div>  
                            </div>	                             
                            
                            <div class="clearfix"></div>
                            <br>                                   
						
                            <div class="row"> 					
                                <div class="col-md-12">
                                    <?php if (has_permission('AddEditCard', '', 'create')) {
                                    ?>
                                        <button type="submit" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                                    <?php
                                    } else { ?>
                                        <button type="submit" class="btn btn-info saveBtn" disabled style="margin-right: 25px;">Save</button>
                                    <?php } ?>

                                    <?php if (has_permission('AddEditCard', '', 'edit')) { ?>
                                        <button type="submit" class="btn btn-info saveBtn2 hidden-button" style="margin-right: 25px;">Update</button>								
                                    <?php } else { ?>
                                        <button type="submit" class="btn btn-info saveBtn2 hidden-button" disabled style="margin-right: 25px;">Update</button>			
                                    <?php } ?>                                    
                                    <button type="submit" class="btn btn-default cancelBtn" >Cancel</button>
                                </div>
                            </div>                    

                            <!-- Modal -->
                            <div class="modal fade AccountGroup" id="AccountGroup" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header" style="padding:5px 10px;">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title">Card Lists</h4>
                                        </div>
                                        <div class="modal-body" style="padding:0px 5px !important">
                                            
                                            <div class="table-AccountGroup tableFixHead2">
                                                <table id="tblcarddetails" class="tree table table-striped table-bordered table-AccountGroup tableFixHead2" id="table_AccountGroup" width="100%">
                                                    <thead>
                                                        <tr style="display:none;">
                                                            <td colspan="6" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                                        </tr>
                                                        <tr>
                                                            <th id="sl" style="text-align:left;">Prefix <span class="up_starting">  &#8593;</span><span class="down" style="display:none;"> &#8593;</span><span class="up" style="display:none;"> &#8595;</span></th>
                                                            <th style="text-align:left;">Card Name</th> 
                                                            <th style="text-align:left;">Card Validity (Yrs)</th> 
                                                            <th style="text-align:left;">Card Fees</th>  
                                                            <th style="text-align:left;">Renewal Fees</th>                                                            
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tableBody">                                                                                                    
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
						    <!-- /.modal -->                       
						<div class="clearfix"></div>			
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
    
    $("#cardid").dblclick(function()
    {
		$('#AccountGroup').modal('show');			
	});

    //save new card name
    $('.saveBtn').on('click',function() 
    {
        card_name= $('#farmer_card').val(); 
        prefix = $('#cardid').val();      
        validity = $('#cardvalidity').val();
        cardfees = $('#cardfees').val();
        renewalfees = $('#renewalfees').val();
        status = $('#status').val();
        welcomebonus = $('#welcomebonus').val();
        ptconversion = $('#ptconversion').val();
        interestrate = $('#interestrate').val();
        ratebenefitmin = $('#ratebenefitmin').val();
        ratebenefitmax = $('#ratebenefitmax').val();
        reedemption = $('#reedemption').val();
        soiltest = $('#soiltest').val();
        soiltestdisc = $('#soiltestdisc').val();
        if(prefix == '')
        {
            alert('please enter Prefix');
            $('#cardid').focus();
        }
        else if(card_name == '')
        {
            alert('please enter Card Name');
            $('#farmer_card').focus();
        }
        else if(validity == '')
        {
            alert('please enter Card Validity');
            $('#cardvalidity').focus();
        }
        else if(cardfees == '')
        {
            alert('please enter Card Fees');
            $('#cardfees').focus();
        }
        else if(renewalfees == '')
        {
            alert('please enter Renewal Fees');
            $('#renewalfees').focus();
        }
        else if(welcomebonus == '')
        {
            alert('please enter Welcome Bonus');
            $('#welcomebonus').focus();
        }
        else if(ptconversion == '')
        {
            alert('please enter Point Conversion');
            $('#ptconversion').focus();
        }
        else if(interestrate == '')
        {
            alert('please enter Interest Rate');
            $('#interestrate').focus();
        }
        else if(ratebenefitmin == '')
        {
            alert('please enter Rate Benefit');
            $('#ratebenefitmin').focus();
        }
        else if(ratebenefitmax == '')
        {
            alert('please enter Rate Benefit Upto');
            $('#ratebenefitmax').focus();
        }
        else if(reedemption == '')
        {
            alert('please enter reedemption');
            $('#reedemption').focus();
        }
        else if(soiltest == '')
        {
            alert('please enter Soil test');
            $('#soiltest').focus();
        }
        else if(soiltestdisc == '')
        {
            alert('please enter Soil test diccount');
            $('#soiltestdisc').focus();
        }
        else if(status == '')
        {
            alert('please select Status');
            $('#status').focus();
        }
        else
        {
            $.ajax({
                url: "<?php echo admin_url(); ?>CardMaster/insert_card", 
                type: 'POST', 
                data: {prefix:prefix,card_name:card_name,validity:validity,cardfees:cardfees,renewalfees:renewalfees,status:status,welcomebonus:welcomebonus,ptconversion:ptconversion,interestrate:interestrate,ratebenefitmin:ratebenefitmin,ratebenefitmax:ratebenefitmax,reedemption:reedemption,soiltest:soiltest,soiltestdisc:soiltestdisc}, 
                dataType: 'json',
                success: function(response) {
                    if (response.success) {                   
                        alert_float('success', 'Record Created Successfully...');            
                        ResetForm();      
                    } else {                    
                        alert_float('warning', 'Something went wrong...');
                        ResetForm();
                    }
                },
                error: function(xhr, status, error) {                
                    $('#messageContainer').text('An error occurred while processing the request').css('background-color', '#f44336').css('color', 'white').fadeIn();
                }
            });   
        }    
    });

    //update existing card name
    $('.saveBtn2').on('click',function() 
    {  
        prefix = $('#cardid').val(); 
        cardName= $('#farmer_card').val();       
        validity = $('#cardvalidity').val();
        cardfees = $('#cardfees').val();
        renewalfees = $('#renewalfees').val();       
        status = $('#status').val();      
        welcomebonus = $('#welcomebonus').val();
        ptconversion = $('#ptconversion').val();
        interestrate = $('#interestrate').val();
        ratebenefitmin = $('#ratebenefitmin').val();
        ratebenefitmax = $('#ratebenefitmax').val();
        reedemption = $('#reedemption').val();
        soiltest = $('#soiltest').val();
        soiltestdisc = $('#soiltestdisc').val();
       
        $.ajax({            
            url: "<?php echo admin_url(); ?>CardMaster/EditCard_details", 
            type: 'POST', 
            data: {prefix:prefix,cardName:cardName,validity:validity,cardfees:cardfees,renewalfees:renewalfees,status:status,welcomebonus:welcomebonus,ptconversion:ptconversion,interestrate:interestrate,ratebenefitmin:ratebenefitmin,ratebenefitmax:ratebenefitmax,reedemption:reedemption,soiltest:soiltest,soiltestdisc:soiltestdisc}, 
            dataType: 'json',
            success: function(response) {               
                if (response.success) {                                  
                    alert_float('success', 'Record Updated Successfully...');     
                    ResetForm();                       
                } else {                    
                    alert_float('warning', 'Something went wrong...');
                    ResetForm();
                }
            },
            error: function(xhr, status, error) {                
                $('#messageContainer').text('An error occurred while processing the request').css('background-color', '#f44336').css('color', 'white').fadeIn();
            }
        });
    });

    //cancel the records
    $('.cancelBtn').on('click',function() 
    {
        ResetForm();         
    });   
});
</script>
<script>
    function ResetForm()
    {
        $('#cardid').val(''); 
        $('#farmer_card').val('');
        $('#cardvalidity').val('');
        $('#cardvalidity').selectpicker('refresh');  
        $('#cardfees').val('');
        $('#renewalfees').val('');
        $('#welcomebonus').val('');
        $('#ptconversion').val('');
        $('#interestrate').val('');
        $('#ratebenefitmin').val('');
        $('#ratebenefitmax').val('');
        $('#reedemption').val('');
        $('#soiltest').val('');
        $('#soiltestdisc').val('');

        $('#status').val('');
        $('#status').selectpicker('refresh'); 
        $('#chk').prop('checked', false);
        $('#textbox').val('');
        $('.saveBtn2').hide();     
        $('.saveBtn').show();  
        $('#FeatureTableBody').hide(); 
    }
</script>

<script>
    function OpenEditModel() 
    {
        $.ajax({
            url: "<?php echo site_url('admin/CardMaster/get_all_card_details'); ?>",
            type: "GET",        
            dataType: "json",
            success: function(data)
            {            
                var tableBody = $('#tableBody');
                tableBody.empty(); 

                if (data && data.length > 0) 
                {
                        $.each(data, function(index, item) 
                        {                   
                            tableBody.append(
                                '<tr  class="get_AccountID" data-id="' + item["Prefix"] + '">' +
                                    '<td class="prefix-cell" data-id="' + item.Prefix + '">' + item.Prefix + '</td>' +
                                    '<td>' + item.CardName + '</td>' +
                                    '<td>' + item.Validity + ' yrs</td>' +
                                    '<td>' + item.CardFees + '</td>' +
                                    '<td>' + item.RenewalFees + '</td>' +
                                '</tr>'
                            );
                        });
                } 
            }
        });
        $("#AccountGroup").modal("show");   
    }

    $(document).on('click', '.get_AccountID', function() 
    {
        var cardId = $(this).data('id');       
        $("#AccountGroup").modal("hide");        
        fecthcarddetails(cardId);
    });
</script>

<script>
    $('#cardid').focus(function()
    {
        ResetForm();
    });
    $('#cardid').blur(function()
    {
        var prefix = $('#cardid').val();
        if(prefix !== '')
        {
            $.ajax({
                url: "<?php echo site_url('admin/CardMaster/get_card_name'); ?>",
                type: "POST",
                data: {prefix : prefix},
                dataType: "json",
                beforeSend: function () {
                    $('.searchh2').css('display','block');
                    $('.searchh2').css('color','blue');
                },
                complete: function () {
                    $('.searchh2').css('display','none');
                },    
                success: function(data)
                {                      
                    if (data && data.cardinfo && data.cardinfo.CardName !== null && data.cardinfo.CardName !== '') 
                    {                        
                        $('#farmer_card').val(data.cardinfo.CardName);  
                        $('#cardvalidity').val(data.cardinfo.Validity);               
                        $('#cardvalidity').selectpicker('refresh');      
                        $('#cardfees').val(data.cardinfo.CardFees);
                        $('#renewalfees').val(data.cardinfo.RenewalFees);
                        $('#welcomebonus').val(data.cardinfo.WelcomeBonus);
                        $('#ptconversion').val(data.cardinfo.PointConversion);
                        $('#interestrate').val(data.cardinfo.InterestRate);
                        $('#ratebenefitmin').val(data.cardinfo.RateBenefits);
                        $('#ratebenefitmax').val(data.cardinfo.RateBenefitUpto);
                        $('#reedemption').val(data.cardinfo.Redmption);
                        $('#soiltest').val(data.cardinfo.SoilTest);
                        $('#soiltestdisc').val(data.cardinfo.SoilTestDisc);

                        $('#status').val(data.cardinfo.Status);
                        $('#status').selectpicker('refresh'); 
                        $('.saveBtn2').show();
                        $('.saveBtn').hide();                                        
                    }else 
                    {                       
                        $('#farmer_card').val('');                
                        $('.saveBtn2').hide();
                        $('.saveBtn').show();                                                                    
                    }          
                }               
            });
        }
    });
    
    var currentcardId = null;
    function fecthcarddetails(cardId)
    {           
        currentcardId = cardId;
        $.ajax({
        url: "<?php echo site_url('admin/CardMaster/getcardDetailsbyId'); ?>",
        type: "POST",
        data: {currentcardId : currentcardId},
        dataType: "json", 
        beforeSend: function () {
            $('.searchh2').css('display','block');
            $('.searchh2').css('color','blue');
        },
        complete: function () {
            $('.searchh2').css('display','none');
        },      
        success: function(data)
        {             
            if (data && data.cardbyid.id) 
            {   
                $('#cardid').val(data.cardbyid.Prefix);  
                $('#farmer_card').val(data.cardbyid.CardName);                           
                $('#cardvalidity').val(data.cardbyid.Validity);                     
                $('#cardvalidity').selectpicker('refresh');          
                $('#cardfees').val(data.cardbyid.CardFees);
                $('#renewalfees').val(data.cardbyid.RenewalFees);
                $('#welcomebonus').val(data.cardbyid.WelcomeBonus);
                $('#ptconversion').val(data.cardbyid.PointConversion);
                $('#interestrate').val(data.cardbyid.InterestRate);
                $('#ratebenefitmin').val(data.cardbyid.RateBenefits);
                $('#ratebenefitmax').val(data.cardbyid.RateBenefitUpto);
                $('#reedemption').val(data.cardbyid.Redmption);
                $('#soiltest').val(data.cardbyid.SoilTest);
                $('#soiltestdisc').val(data.cardbyid.SoilTestDisc);

                $('#status').val(data.cardbyid.Status); 
                $('#status').selectpicker('refresh');        

                $('.saveBtn2').show();
                $('.saveBtn').hide();                      
            } 
            else 
            {             
                $('.saveBtn2').hide();
                $('.saveBtn').show();                              
            }          
        }
        });     
    }
</script>

<script>
   function myFunction2() 
   {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.trim();       
        table = document.getElementById("tblcarddetails");
        tr = table.getElementsByTagName("tr"); 

        for (i = 2; i < tr.length; i++) 
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

<style>
    #table_AccountGroup td:hover {
    cursor: pointer;
	}
	#table_AccountGroup tr:hover {
    background-color: #ccc;
	}
	
    .table-AccountGroup          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-AccountGroup thead th { position: sticky; top: 0; z-index: 1; }
    .table-AccountGroup tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>