<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>       
    #textbox 
    {
        display: none;
        margin-top: 10px;
    }
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
                                        <li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
                                        <li class="breadcrumb-item active" aria-current="page"><b>Change Mobile No</b></li>
                                    </ol>
                                </nav>
                                <hr class="hr_style">

                                <div class="col-md-12">
                                    <div class="searchh1" style="display:none;">Account id already exist.</div>  
                                    <div class="searchh2" style="display:none;">Please wait while fetching data.</div>                                    
                                    <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                                    <div class="searchh4" style="display:none;">Please wait while updating data.</div>
                                </div>   

                                <div class="clearfix"></div>
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <small class="req text-danger">* </small>                                       
                                        <label for="clientlist" class="form-label">Account List</label>
                                        <select name="" id="clientlist" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                            <option value="">Non Selected</option>                                            
                                            <?php
                                                foreach ($AllClients as $key => $value) {
                                            ?>
                                                <option value="<?php echo $value['AccountID'];?>"><?php echo $value['company']."(".$value['AccountID'].")";?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>     

                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <small class="req text-danger">* </small>                                        
                                        <label for="newid" class="control-label">New Mobile No</label>
                                        <input type="text" id="newid" name="" class="form-control" value="" maxlength="10" oninput="validateInput(this)" pattern="\d{0,10}" title="Enter up to 10 digits">                                   
                                    </div>
                                </div>    
                                
                                <div class="col-md-4">
                                    <div class="form-group">                                       
                                        <label for="customertype" class="control-label">Account Type</label>
                                        <input type="text" id="customertype" name="" class="form-control" value="" readonly>                                   
                                    </div>
                                </div>                                 
                            </div> 

                            <div class="clearfix"></div>
                           
                            <div class="row"> 
                                <div class="col-md-4">
                                    <div class="form-group">                                       
                                        <label for="phonenumber" class="control-label">Mobile No</label>
                                        <input type="text" id="phonenumber" name="" class="form-control" value="" readonly>                                   
                                    </div>
                                </div>  

                                <div class="col-md-4">
                                    <div class="form-group">                                       
                                        <label for="state" class="control-label">State</label>
                                        <input type="text" id="state" name="" class="form-control" value="" readonly>                                   
                                    </div>
                                </div> 

                                <div class="col-md-4">
                                    <div class="form-group">                                       
                                        <label for="city" class="control-label">City</label>
                                        <input type="text" id="city" name="" class="form-control" value="" readonly>                                   
                                    </div>
                                </div> 
                            </div>	

                            <div class="clearfix"></div>

                            <div class="row"> 
                                <div class="col-md-4">
                                    <div class="form-group">                                       
                                        <label for="taluka" class="control-label">Taluka</label>
                                        <input type="text" id="taluka" name="" class="form-control" value="" readonly>                                   
                                    </div>
                                </div>  

                                <div class="col-md-4">
                                    <div class="form-group">                                       
                                        <label for="zip" class="control-label">Zip</label>
                                        <input type="text" id="zip" name="" class="form-control" value="" readonly>                                   
                                    </div>
                                </div>                          
                            </div>     
                            
                            <div class="clearfix"></div>

                            <div class="row"> 	
                                <div class="col-md-12">
                                    <div class="form-group">                                      
                                        <label for="address">Address</label>
                                        <textarea name="" id="address" class="form-control" rows="4" placeholder="" readonly></textarea>                                        
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            <br>

                            <div class="row"> 					
                                <div class="col-md-12">        
                                <?php 
                                    $disabled = 'disabled';
                                if (has_permission_new('ChangeMobileNo', '', 'edit')) {
                                    $disabled = '';
                                 } ?>
                                    <button type="submit" class="btn btn-info saveBtn2" <?php echo $disabled;?> style="margin-right: 25px;">Update</button>	
                                    <button type="submit" class="btn btn-default cancelBtn" >Cancel</button>
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
        //cancel the records
        $('.cancelBtn').on('click',function()
        {           
            $('#clientlist').val('');
            $('#clientlist').selectpicker('refresh');  
            $('#newid').val('');
            $('#customertype').val('');
            $('#phonenumber').val('');
            $('#state').val('');
            $('#city').val('');
            $('#taluka').val('');
            $('#zip').val('');
            $('#address').val('');
            $('.saveBtn2').show();             
            $('.searchh1').css('display','none');  
        });        

        //update the records
        $('.saveBtn2').on('click',function()
        {
            var AccountID  = $('#clientlist').val();
            var newaccountID  = $('#newid').val();     
            var isValidAccountID = /^\d{10}$/.test(newaccountID);
            if(AccountID == '')
            {
                alert('please select an Account');
                $('#clientlist').focus();
            }      
            else if(newaccountID == '')
            {
                alert('please enter an New Mobile No');
                $('#newid').focus();
            }
            else if(newaccountID != '')
            {
                if(isValidAccountID)
                {
                    $.ajax({
                    url: "<?php echo admin_url(); ?>ChangeMobileNo/CheckAccountExist",
                    type: "POST",
                    data: {newaccountID : newaccountID,AccountID: AccountID},
                    dataType: "json",                    
                        success: function(data)
                        {    
                            if (data.success) 
                            {   
                                alert('Account id already exist.');  
                                $('#newid').val('');    
                                $('.saveBtn2').show();  
                                $('#clientlist').val('');
                                $('#clientlist').selectpicker('refresh');  
                                $('#newid').val('');
                                $('#customertype').val('');
                                $('#phonenumber').val('');
                                $('#state').val('');
                                $('#city').val('');
                                $('#taluka').val('');
                                $('#zip').val('');
                                $('#address').val('');                                      
                            } else 
                            {        
                                if(confirm("Do you want to change Mobile no and AcoountID?"))
                                {
                                    $.ajax({
                                    url: "<?php echo admin_url(); ?>ChangeMobileNo/ChangeMobileNumber",
                                    type: "POST",
                                    data: {AccountID : AccountID,newaccountID:newaccountID},
                                    dataType: "json", 
                                        beforeSend: function () {
                                            $('.searchh4').css('display','block');
                                            $('.searchh4').css('color','blue');
                                        },
                                        complete: function () {
                                            $('.searchh4').css('display','none');
                                        },                    
                                        success: function(data)
                                        {  
                                            console.log                                                                                     
                                            if (data.success) 
                                            {
                                                alert('Mobile no updated successfully.');  
                                                location.reload();
                                                
                                                $('#newid').val('');  
                                                $('#clientlist').val('');
                                                $('#clientlist').selectpicker('refresh');  
                                                $('#newid').val('');
                                                $('#customertype').val('');
                                                $('#phonenumber').val('');
                                                $('#state').val('');
                                                $('#city').val('');
                                                $('#taluka').val('');
                                                $('#zip').val('');
                                                $('#address').val('');                                                 
                                                $('.saveBtn2').show();  
                                            }                                        
                                        }     
                                    });     
                                }                                                                                  
                            }          
                        }               
                    });
                }else
                {
                    alert('Entered mobile no is not valid');
                    $('#newid').val('');  
                    $('#clientlist').val('');
                    $('#clientlist').selectpicker('refresh');  
                    $('#newid').val('');
                    $('#customertype').val('');
                    $('#phonenumber').val('');
                    $('#state').val('');
                    $('#city').val('');
                    $('#taluka').val('');
                    $('#zip').val('');
                    $('#address').val('');                                                 
                    $('.saveBtn2').show();  
                }           
            }                    
        });

        //fetch on client select 
        $('#clientlist').on('change', function() 
        {
            var AccountID  = $('#clientlist').val();
            var newaccountID = $('#newid').val();           
           
            if(AccountID !== '')
            {               
                $.ajax({
                url: "<?php echo admin_url(); ?>ChangeMobileNo/GetAccountDetailsByID",
                type: "POST",
                data: {AccountID : AccountID,newaccountID : newaccountID},
                dataType: "json",
                    
                    success: function(data)
                    {                                                                
                        if (data) 
                        {    
                            var clientdetails = data.clients_data;                          
                           
                            var statelist = data.state_list;
                            var citylist = data.city_list;
                            var talukalist = data.taluka_list;
                           
                            var house = clientdetails?.house || ''; 
                            var street = clientdetails?.street || '';
                            var loc = clientdetails?.loc || '';
                            var vtc = clientdetails?.vtc || '';
                            var po = clientdetails?.po || '';                            

                            var fullAddress = `${house} ${street} ${loc} ${vtc} ${po}`.trim();
                            var AllaccountIds = data.all_accountdetails;                     

                            if (data && clientdetails.CustomerType) 
                            {
                                var customertype;
                                if(clientdetails.CustomerType == 1)
                                {
                                    customertype = "Farmer"
                                }
                                else if(clientdetails.CustomerType == 2)
                                {
                                    customertype = "Trader"
                                }
                                else if(clientdetails.CustomerType == 3)
                                {
                                    customertype = "Broker"
                                }
                                else if(clientdetails.CustomerType == 4)
                                {
                                    customertype = "Corporate"
                                }
                                $('#customertype').val(customertype);
                            } 
                            else 
                            {
                                $('#customertype').val('NA');
                            }    

                            if(clientdetails.phonenumber !==null)
                            {
                                $('#phonenumber').val(clientdetails.phonenumber);
                            }
                            else
                            {
                                $('#phonenumber').val('NA');
                            }                           
                            
                            if (data && statelist !==null) 
                            {
                                $('#state').val(statelist.state_name);
                            } else {
                                $('#state').val('NA');
                            }

                            if (data && citylist !==null) 
                            {
                                $('#city').val(citylist.city_name);
                            } else {
                                $('#city').val('NA');
                            }

                            if (data && talukalist !==null) 
                            {
                                $('#taluka').val(talukalist.TalukaName);
                            } else {
                                $('#taluka').val('NA');
                            }
                           
                            if(clientdetails.zip !==null)
                            {
                                $('#zip').val(clientdetails.zip);
                            }
                            else {
                                $('#zip').val('NA');
                            }       
                            
                            if(fullAddress && fullAddress!=null)
                            {                               
                                $('#address').val(fullAddress);
                            }  
                            else
                            {
                                $('#address').val('NA');                                
                            }                      
                              
                            $('.saveBtn2').show();                                                                                               
                        } 
                        else 
                        {      
                            $('#customertype').val('NA');
                            $('#state').val('NA');                                
                            $('.saveBtn2').hide();      
                            alert('Account id already exist.');                                                                                             
                        }          
                    }               
                });
            }  
        });       
    }); 
</script>

<script>
    function validateInput(element) 
    {    
        const value = element.value;   
        element.value = value.replace(/[^0-9]/g, '').slice(0, 10);
    }
</script>



