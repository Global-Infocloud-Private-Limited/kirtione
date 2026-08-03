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
.hidden {
    display: none;
}
</style>

<?php
 $to_date = date('d/m/Y');
?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">                        						 
                            <div class="row"> 
                                <div class="col-md-12">
                                    <div class="searchh2" style="display:none;">Please wait while fetching data.</div>                                    
                                    <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                                    <div class="searchh4" style="display:none;">Please wait while updating data.</div>
                                </div>   
                                <div class="col-md-4">
                                    <div class="form-group"> 
                                        <small class="req text-danger">* </small>                                       
                                        <label for="farmerlist" class="form-label">Farmer List</label>
                                        <select name="" id="farmerlist" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                            <option value="">Non Selected</option>                                            
                                            <?php
                                                foreach ($allfarmers as $key => $value) {
                                            ?>
                                                <option value="<?php echo $value['AccountID'];?>"><?php echo $value['company'];?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>  
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small> 
                                        <label for="cardselect">Card Type</label>
                                        <select name="" id="cardselect" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                            <option value="">Non Selected</option>  
                                            <?php
                                                foreach ($cardnames as $key => $name) {
                                            ?>
                                                <option value="<?php echo $name['Prefix'];?>"><?php echo $name['CardName'];?></option>
                                            <?php
                                                }
                                            ?>                                                                                                                             
                                        </select>                                       
                                    </div>
                                </div>    
                               
                                <div class="col-md-4">
                                    <div class="form-group">                                       
                                        <label for="date" class="control-label">Issue Date</label>
                                        <input type="text" id="date" name="" class="form-control" value="" readonly>                                   
                                    </div>
                                </div>                          
                            </div> 

                            <div class="clearfix"></div>                           

                            <div class="row"> 
                                <div class="col-md-4">
                                    <div class="form-group">                                       
                                        <label for="validity" class="control-label">Card Validity (Yrs)</label>
                                        <input type="text" id="validity" name="" class="form-control" value="" readonly>                                   
                                    </div>
                                </div> 

                                <div class="col-md-4">
                                    <div class="form-group">                                       
                                        <label for="expdate" class="control-label">Expiry Date</label>
                                        <input type="text" id="expdate" name="" class="form-control" value="" readonly>                                   
                                    </div>
                                </div>  

                                <div class="col-md-4">
                                    <div class="form-group">                                       
                                        <label for="cardfees" class="control-label">Card Fees</label>
                                        <input type="text" id="cardfees" name="" class="form-control" value="" readonly>                                   
                                    </div>
                                </div>                                                        
                            </div>

                            <div class="clearfix"></div>                            

                            <div class="row">                               
                                <div class="col-md-4">
                                    <div class="form-group">                                       
                                        <label for="renewalfees" class="control-label">Renewal Fees</label>
                                        <input type="text" id="renewalfees" name="" class="form-control" value="" readonly>                                   
                                    </div>
                                </div>  
                                
                                <div class="col-md-4">
                                    <div class="form-group">                                       
                                        <label for="number" class="control-label">Card Number</label>
                                        <input type="text" id="number" name="" class="form-control" value="" readonly>                                   
                                    </div>
                                </div>    

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="status">Status</label>
                                    <select name="" id="status" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Non Selected</option> 
                                        <option value="Y">Active</option>
                                        <option value="N">Deactive</option>                                       
                                    </select>
                                    </div>
                                </div>       
                            </div>                     	

                            <div class="clearfix"></div>
                            
                            <div class="row">   
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="cardfeesreceived">Card Fees Received</label>
                                    <select name="" id="cardfeesreceived" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Non Selected</option> 
                                        <option value="Y">Yes</option>
                                        <option value="N">No</option>                                       
                                    </select>
                                    </div>
                                </div>  

                                <div class="col-md-4">
                                    <div class="form-group hidden" id="paymentmethodContainer">
                                        <small class="req text-danger">* </small>
                                        <label for="paymentmethod">Payment Method</label>
                                    <select name="" id="paymentmethod" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Non Selected</option>  
                                            <?php
                                                foreach ($payment_methods as $key => $methods) {
                                            ?>
                                                <option value="<?php echo $methods['AccountID'];?>"><?php echo $methods['AccountID'];?></option>
                                            <?php
                                                }
                                            ?>                                                                              
                                    </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group hidden" id="paymentmodeContainer">
                                        <small class="req text-danger">* </small>
                                        <label for="paymentmode">Payment Mode</label>
                                    <select name="" id="paymentmode" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="1">CASH</option>    
                                        <option value="2">UPI</option>    
                                        <option value="3">Netbanking</option>    
                                        <option value="4">Credit/Debit Card</option>    
                                        <option value="5">Aadhar Card</option>                                                      
                                    </select>
                                    </div>
                                </div>
                            </div> 

                            <div class="row">   
                                <div class="col-md-4">
                                    <div class="form-group hidden" id="referenceno">                                       
                                        <label for="refno" class="control-label">Reference No</label>
                                        <input type="text" id="refno" name="" class="form-control" value="">                                   
                                    </div>
                                </div>   

                                <div class="col-md-4 hidden" id="paydate">
                                    <?php echo render_date_input('paymentdate','Payment Date',$to_date); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Feature List</h4>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>                                           
                                                <th>Feature Name</th>
                                                <th>Feature Value</th>                                                                                                                           
                                            </tr>
                                        </thead>
                                        <tbody id="FeatureTableBody"></tbody>                                
                                    </table>
                                </div>
                            </div>
                            
						
                            <div class="row"> 					
                                <div class="col-md-12">
                                <?php if (has_permission_new('CardAllotment', '', 'create')) {
                                    ?>
                                    <button type="submit" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                                <?php
                                } else { ?>
                                    <button type="submit" class="btn btn-info saveBtn" disabled style="margin-right: 25px;">Save</button>
                                <?php } ?>  
                                <?php if (has_permission_new('CardAllotment', '', 'edit')) { ?>                          
                                    <button type="submit" class="btn btn-info saveBtn2 hidden-button" style="margin-right: 25px;">Update</button>                               
                                <?php } else { ?>
                                    <button type="submit" class="btn btn-info saveBtn2 hidden-button" disabled style="margin-right: 25px;">Update</button>                              
                                <?php } ?>
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
        $('#cardfeesreceived').on('change', function() 
        {                     
            var CardFees = $('#cardfeesreceived').val();                  
            if(CardFees ==="Y")
            {  
                $('#paymentmethodContainer').removeClass('hidden');  
                $('#referenceno').removeClass('hidden'); 
                $('#paydate').removeClass('hidden');               
            }     
            else {
            $('#paymentmethodContainer').addClass('hidden');
            $('#paymentmodeContainer').addClass('hidden');
            $('#referenceno').addClass('hidden'); 
            $('#paydate').addClass('hidden');  
            }
        });

        $('#paymentmethod').on('change', function() 
        {                                  
            var PaymentMethod = $('#paymentmethod').val(); 
            $('#paymentmodeContainer').removeClass('hidden');
            $("#paymentmode").children().remove();
            $("#paymentmode").append('<option value="">None Selected</option>');
            if (PaymentMethod === "CASH") 
            {
                $("#paymentmode").append('<option value="1">CASH</option>');
            } else {      
                $("#paymentmode").append('<option value="2">UPI</option>');
                $("#paymentmode").append('<option value="3">Netbanking</option>');
                $("#paymentmode").append('<option value="4">Credit/Debit Card</option>');
                $("#paymentmode").append('<option value="5">Aadhar Card</option>');
            }
            $('#paymentmode').selectpicker('refresh');
        });

        function ResetForm()
        {
            $('#cardselect').val('');
            $('#cardselect').prop('disabled', false);
            $('#cardselect').selectpicker('refresh');             
            $('#farmerlist').val('');
            $('#farmerlist').selectpicker('refresh');  
            $('#validity').val('');  
            $('#cardfees').val('');  
            $('#renewalfees').val('');    
            $('#number').val('');
            $('#date').val('');
            $('#expdate').val('');
            $('#status').val('');
            $('#status').selectpicker('refresh');  
            $('#cardfeesreceived').val('');
            $('#cardfeesreceived').selectpicker('refresh');            
            $('#paymentmethod').val(''); 
            $('#paymentmethod').selectpicker('refresh');  
            $('#paymentmode').val(''); 
            $('#paymentmode').selectpicker('refresh');  

            $('#paymentmethodContainer').addClass('hidden');
            $('#paymentmodeContainer').addClass('hidden'); 
            $('#refno').val('');
            $('#referenceno').addClass('hidden'); 
            $('#paydate').addClass('hidden'); 
            $('#FeatureTableBody').hide();

            $('.saveBtn2').hide();     
            $('.saveBtn').show();  
        }

        //cancel the records
        $('.cancelBtn').on('click',function()
        {
            ResetForm();
        });   
        function AddEditCardDetails()
        {
            var AccountID = $('#farmerlist').val();          
            var prefix  =  $('#cardselect').val();
            var validity = $('#validity').val();
            var issuedate =  $('#date').val();
            var expirydate = $('#expdate').val();
            var cardfees = $('#cardfees').val();
            var status = $('#status').val();  
            var cardfeesreceived = $('#cardfeesreceived').val();
            var paymentmethod = $('#paymentmethod').val();
            var paymentmode = $('#paymentmode').val();
            var refno = $('#refno').val();
            var paymentdate = $('#paymentdate').val();
            if(AccountID == '')
            {
                alert('please select farmer');
                $('#farmerlist').focus();
            }else if(prefix == '')
            {
                alert('please select Card Type');
                $('#cardselect').focus();
            }else if(status == '')
            {
                alert('please select Status');
                $('#status').focus();
            }else if(cardfeesreceived == '')
            {
                alert('please select Card fees received or not!');
                $('#cardfeesreceived').focus();
            }else if(cardfeesreceived =="Y" && paymentmethod == '')
            {                
                alert('please select Payment Method');
                $('#paymentmethod').focus();                              
            }else if(cardfeesreceived =="Y" && paymentmethod != "CASH" && paymentmode =='')
            {                
                alert('please select Payment Mode');
                $('#paymentmode').focus();                              
            }else if(cardfeesreceived =="Y" && paymentmethod != "CASH" && refno == '')
            {                
                alert('please select Reference No');
                $('#refno').focus();                              
            }else
            {
                alert(cardfeesreceived);
                $.ajax({
                    url: "<?php echo admin_url(); ?>CardMaster/update_farmerwise_carddetails", 
                    type: 'POST', 
                    data: {AccountID:AccountID,prefix:prefix,validity:validity,issuedate:issuedate,expirydate:expirydate,cardfees:cardfees,
                    status:status,cardfeesreceived:cardfeesreceived,paymentmethod:paymentmethod,paymentmode:paymentmode,refno:refno,paymentdate:paymentdate}, 
                    dataType: 'json',
                    beforeSend: function () {
                        $('.searchh3').css('display','block');
                        $('.searchh3').css('color','blue');
                    },
                    complete: function () {
                        $('.searchh3').css('display','none');
                    },  
                    success: function(response) 
                    {
                        if (response.success) 
                        {                   
                            alert_float('success', 'Record Created Successfully...');    
                            ResetForm();                                                               
                        } else 
                        {                    
                            alert_float('warning', 'Something went wrong...');
                        }
                    },
                    error: function(xhr, status, error) {                
                        $('#messageContainer').text('An error occurred while processing the request').css('background-color', '#f44336').css('color', 'white').fadeIn();
                    }
                });
            }
        }
        //save new card name
        $('.saveBtn').on('click',function() 
        {
            AddEditCardDetails();
        });      
    
        //update existing card name
        $('.saveBtn2').on('click',function() 
        { 
            AddEditCardDetails();
        });

        //fetch data exist onselect of farmer
        $('#farmerlist').on('change', function() 
        {
            var AccountID = $('#farmerlist').val();
            if(AccountID !=='')
            {
                $.ajax({
                    url: "<?php echo admin_url(); ?>CardMaster/get_accountwise_carddetails_byId", 
                    type: "POST",
                    data: {AccountID : AccountID},
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
                        var accountwiseData = data.accountwise_data;
                        var mastercard = data.mastercard;      
                        var cardfeature = data.cardfeatures;   
                        var staticfeatures = data.staticfeatures;                         

                        var keysToDisplay = [
                                'WelcomeBonus',
                                'PointConversion',
                                'InterestRate',
                                'RateBenefits',
                                'RateBenefitUpto',
                                'Redmption',
                                'SoilTest',
                                'SoilTestDisc'
                            ];
                      
                        if (accountwiseData !== null && mastercard !== null && cardfeature !== null) 
                        {    
                            if (data.accountwise_data.Prefix !==null) 
                            {  $('#cardselect').prop('disabled', true);  }                        
                            
                            var cardno = data.accountwise_data.CardNumber;
                            var formattedCardno = cardno.replace(/(.{4})/g, '$1-').slice(0, -1);

                            $('#cardselect').val(data.accountwise_data.Prefix);  
                            $('#cardselect').selectpicker('refresh');
                            $('#date').val(data.accountwise_data.IssueDate);  
                            $('#validity').val(data.mastercard.Validity);    
                            $('#expdate').val(data.accountwise_data.ExpiryDate);
                            $('#status').val(data.accountwise_data.Status);
                            $('#status').selectpicker('refresh');
                            $('#cardfeesreceived').val(data.accountwise_data.PaymentStatus);
                            $('#cardfeesreceived').prop('disabled', true);
                            $('#cardfeesreceived').selectpicker('refresh');

                            if(data.accountwise_data.PaymentStatus==="Y")
                            {
                                $('#paymentmethod').val(data.accountwise_data.PaymentMethod);
                                $('#paymentmethod').prop('disabled', true);
                                $('#paymentmethod').selectpicker('refresh');
                                $('#paymentmethodContainer').removeClass('hidden');                                 

                                $('#paymentmode').val(data.accountwise_data.PaymentMode);
                                $('#paymentmode').prop('disabled', true);
                                $('#paymentmode').selectpicker('refresh');                             
                                $('#paymentmodeContainer').removeClass('hidden');

                                $('#refno').val(data.accountwise_data.ReferanceNo);
                                $('#referenceno').removeClass('hidden').find('input').prop('readonly', true); 
                                
                                var dbDate = data.accountwise_data.PaymentDate;
                                function formatDatabaseDate(dbDateStr) 
                                {
                                    const parts = dbDateStr.split(' ')[0].split('-'); 
                                    const day = parts[2]; 
                                    const month = parts[1]; 
                                    const year = parts[0]; 
                                    return `${day}/${month}/${year}`;
                                }
                                var formattedPaymentDate = formatDatabaseDate(dbDate);
                                $('#paymentdate').val(formattedPaymentDate);
                                $('#paydate').removeClass('hidden');
                                $('#paymentdate').prop('disabled', true);
                            }
                            else
                            {
                                $('#referenceno').addClass('hidden').find('input').prop('readonly', false).val('');
                            }
                            
                            $('#cardfees').val(data.mastercard.CardFees);
                            $('#renewalfees').val(data.mastercard.RenewalFees);  
                            $('#number').val(formattedCardno);  
                            $('.saveBtn2').show();  
                            $('.saveBtn').hide();                    
                            $('#FeatureTableBody').show();

                            var $tableBody = $('#FeatureTableBody');                 
                            
                            $tableBody.empty();       
                            
                            $.each(keysToDisplay, function(index, key) 
                            {
                                var value = staticfeatures[key];
                                var row = '<tr>' +
                                            '<td>' + key + '</td>' +
                                            '<td>' + (value || 'N/A') + '</td>' +
                                        '</tr>';
                                
                                $tableBody.append(row); 
                            });                       
                        }else 
                        {        
                            $('#cardselect').prop('disabled', false);
                            $('#cardselect').val('');
                            $('#cardselect').selectpicker('refresh');                               
                            $('#validity').val('');  
                            $('#cardfees').val('');  
                            $('#renewalfees').val('');    
                            $('#date').val('');
                            $('#expdate').val('');
                            $('#status').val('');
                            $('#status').selectpicker('refresh'); 
                            $('#FeatureTableBody').hide();
                            $('.saveBtn2').hide();
                            $('.saveBtn').show();                                  
                        }          
                    }
                });
            }else
            {                                       
                $('.saveBtn2').hide();  
                $('.saveBtn').show();  
            }
        });        

        //fetch carddetails on select of card
        $('#cardselect').on('change', function() 
        {
            $('#status').val('');
            $('#status').selectpicker('refresh');  
            var prefix = $('#cardselect').val();
            var now = new Date();
            var year = now.getFullYear();
            var month = String(now.getMonth() + 1).padStart(2, '0');
            var day = String(now.getDate()).padStart(2, '0');             
            var currentDate =  `${day}/${month}/${year}`;            
           
            if(prefix !=='')
            {
                $.ajax({                  
                    url: "<?php echo admin_url(); ?>CardMaster/get_card_details_byprefix",
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
                        if (data) 
                        {     
                            var date = currentDate;                             
                            var validity = data.carddetails.Validity;                            
                            var startDate = moment(date, "DD/MM/YYYY").toDate();                                           
                            var expiryDate = new Date(startDate);
                            expiryDate.setFullYear(expiryDate.getFullYear() + parseInt(validity));
                            
                            var days = String(expiryDate.getDate()).padStart(2, '0');
                            var months = String(expiryDate.getMonth() + 1).padStart(2, '0'); 
                            var years = expiryDate.getFullYear();                           
                            var formattedExpiryDate = `${days}/${months}/${years}`;                           
                            var cardfeature = data.cardfeatures;   
                            var staticfeatures = data.staticfeatures;                         

                            var keysToDisplay = [
                                                    'WelcomeBonus',
                                                    'PointConversion',
                                                    'InterestRate',
                                                    'RateBenefits',
                                                    'RateBenefitUpto',
                                                    'Redmption',
                                                    'SoilTest',
                                                    'SoilTestDisc'
                                                ];                                               

                            $('#validity').val(data.carddetails.Validity);  
                            $('#cardfees').val(data.carddetails.CardFees);  
                            $('#renewalfees').val(data.carddetails.RenewalFees);    
                            $('#date').val(currentDate);
                            $('#expdate').val(formattedExpiryDate);
                            $('#FeatureTableBody').show();

                            if (Array.isArray(cardfeature)) 
                            {                                
                                var $tableBody = $('#FeatureTableBody');                 
                                
                                $tableBody.empty();       
                                
                                $.each(keysToDisplay, function(index, key) 
                                {
                                    var value = staticfeatures[key];
                                    var row = '<tr>' +
                                                '<td>' + key + '</td>' +
                                                '<td>' + (value || 'N/A') + '</td>' +
                                            '</tr>';
                                    
                                    $tableBody.append(row); 
                                });     
                            } else {
                                console.error('Invalid cardfeatures format:', cardfeature);
                            }                                          
                        } 
                        else 
                        {           
                            $('#FeatureTableBody').hide();                                                                 
                        }          
                    }
                });
            }            
        });
    });    
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