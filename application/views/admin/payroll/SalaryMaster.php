<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-daily_report          { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
.table-daily_report thead th { position: sticky; top: 0; z-index: 1; }
.table-daily_report tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
    input:focus{
            outline: none;
            box-shadow: none;
            border: none;
        }
.for-item-id{
    position: sticky !important;
    left: 0;
    width: 43px;
    background-color:#fff;
    }
.for-item-name{
    position: sticky;
    width: 81px;
    left: 43px;
    background-color:#fff;
    }
    
.for-item-idth{
    position: sticky !important;
    left: 0;
    width: 43px;
    }
    
    .for-item-nameth{
position: sticky;
    width: 81px;
    left: 43px;
    }
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4>Salary Master</h4>
                        <hr>
                    </div>
                    <div class="col-md-12">
                <?php
			            echo form_open($this->uri->uri_string(),array('id'=>'salary_form','class'=>'_transaction_form invoice-form'));
			    ?>
                        
                        <div class="table-daily_report tableFixHead2">
                        <input type="hidden" name="error_log" id="error_log" value="">
                        <table class="tree table table-striped table-bordered table-daily_report tableFixHead2" id="table-daily_report" width="100%">
                            <thead>
                                <tr>
                                    <th style="text-align:center;" class="for-item-idth">EMP Code</th>
                                    <th style="text-align:center;" class="for-item-nameth">EMP Name</th>
                                    <?php
                                    $EHead = 0;
                                    $DHead = 0;
                                    foreach($SalaryHead as $Key=>$val){
                                        if($val['type']=="1"){
                                            $EHead++;
                                        }else{
                                            $DHead++;
                                        }
                                    }
                                    ?>
                                    <th style="text-align:center;" class="for-item-nameth" colspan="<?php echo $EHead; ?>">Earnings</th>
                                    <th style="text-align:center;" class="for-item-nameth" colspan="<?php echo $DHead; ?>">Deductions</th>
                                    <th style="text-align:center;" class="for-item-nameth" colspan="3">Summary</th>
                                </tr>
                                <tr>
                                    <td style="text-align:center;" class="for-item-idth" colspan="2"></td>
                                <?php
                                    foreach($SalaryHead as $Key1=>$val1){
                                        if($val1['mesuredIn']=="1"){
                                           $ValueType = "Amt"; 
                                        }else{
                                            $ValueType = "%"; 
                                        }
                                ?>
                                    <td style="text-align:center;" class="for-item-idth"><b><?php echo $val1['name'].' ('.$ValueType.')';?></b></td>
                                    
                                <?php
                                    }
                                ?>
                                <td>Monthly Gross</td>
                                <td>Monthly Deduction</td>
                                <td>Yearly CTC</td>
                                </tr>
                            </thead>
                            <tbody id="rate_update_table">
                                <tr>
                                <?php
                                    foreach($ActiveStaff as $staffKey=>$staffval){
                                ?>
                                        <td style="text-align:left;" class="for-item-idth"><b><?php echo $staffval['AccountID'];?></b></td>
                                        <td style="text-align:left;" class="for-item-idth"><b><?php echo $staffval['firstname'].$staffval['lastname'];?></b></td>
                                <?php
                                        $deduction = 0;
                                        $earning = 0;
                                        $basic = 0;
                                        $NetPayable = 0;
                                        $NetAmt = 0;
                                        foreach($SalaryHead as $Key1=>$val1){
                                            $value = '';
                                            $css = '';
                                            if($val1['auto_calculate']=="Y"){
                                                $css = 'readonly';
                                            }
                                            foreach($SalaryDetails as $salaryKey=>$salaryValue){
                                                if($staffval['AccountID'] == $salaryValue['AccountID'] && $val1['code']==$salaryValue['HeadID']){
                                                    if($salaryValue['HeadID'] == "BASIC"){
                                                       $basic =  $salaryValue['value'];
                                                    }
                                                    $value = $salaryValue['value'];
                                                }
                                            }
                                            if($val1['type'] == '1' && $val1['code'] != "NET"){
                                                $earning += $value;
                                            }else if($val1['type'] == '2' && $val1['code'] != "NET"){
                                                $deduction += $value;
                                            }
                                            /*if($val1['mesuredIn'] == '1' && $val1['type'] == '1' && $value !=''){
                                                $earning += $value;
                                            }else if($val1['mesuredIn'] == '2' && $val1['type'] == '1' && $value !=''){
                                                $Amt = ($basic * $value) / 100;
                                                $earning += $Amt;
                                            }else if($val1['mesuredIn'] == '2' && $val1['type'] == '2' && $value !=''){
                                                $Amt = ($basic * $value) / 100;
                                                $deduction += $Amt;
                                            }else if($val1['mesuredIn'] == '1' && $val1['type'] == '2' && $value !=''){
                                                $deduction += $value;
                                            }*/
                                        if($val1['code'] == "NET"){
                                            $functonName = 'changeValue';
                                            $NetAmt = $value;
                                        }else{
                                            $functonName = 'changeAmt';
                                        }
                                ?>
                                            <td style="text-align:right;" class="for-item-idth"><input type="text" <?php echo $css;?> class="AmtEnter form-control" name="Amt_<?php echo $staffval['AccountID']?>_<?php echo $val1['code']?>" id="Amt_<?php echo $staffval['AccountID']?>_<?php echo $val1['code']?>" value="<?php echo $value;?>" style="width: 100%;" onchange = "<?php echo $functonName;?>(this.id,this.value)"></td>
                                <?php
                                        }
                                    $NetPayable = $earning - $deduction;
                                    $YearlyCTC = $earning * 12;
                                    if($NetAmt != $NetPayable){
                                        $css = "background-color: red;";
                                    }else{
                                        $css = '';
                                    }
                                ?>
                                        <td style="text-align:right;<?php echo $css;?>" id="total_earning_td_<?php echo $staffval['AccountID'];?>" ><span id="total_earning_html_<?php echo $staffval['AccountID'];?>"><?php echo $earning; ?></span><input type="hidden" name="total_earning_<?php echo $staffval['AccountID'];?>" id="total_earning_<?php echo $staffval['AccountID'];?>"></td>
                                        <td style="text-align:right;<?php echo $css;?> " id="total_deduction_td_<?php echo $staffval['AccountID'];?>" ><span id="total_deduction_html_<?php echo $staffval['AccountID'];?>"><?php echo $deduction; ?></span><input type="hidden" name="total_deduction_<?php echo $staffval['AccountID'];?>" id="total_deduction_<?php echo $staffval['AccountID'];?>"></td>
                                        <td style="text-align:right; <?php echo $css;?> " id="yearly_ctc_td_<?php echo $staffval['AccountID'];?>" ><span id="yearly_ctc_html_<?php echo $staffval['AccountID'];?>"><?php echo $YearlyCTC; ?></span><input type="hidden" name="yearly_ctc_<?php echo $staffval['AccountID'];?>" id="yearly_ctc_<?php echo $staffval['AccountID'];?>"></td>
                                </tr>
                                <?php
                                    }
                                ?>
                                
                            </tbody>
                        </table>   
                       
                        </div>
                        <div class="btn-bottom-toolbar text-right">
                            <button type="button" class="btn btn-info saveBtn" onclick="SubmitFormData()" style="margin-right: 25px;">Update</button>
                            <button type="button" class="btn btn-default cancelBtn">Cancel</button>
                        </div>
            <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
<script type="text/javascript">
	$('.AmtEnter').on('keypress',function (event) {
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
		var input = $(this).val();
		if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
			event.preventDefault();
		}
	});
	$('.cancelBtn').on('click',function () {
	    window.location.reload(true);
	})
</script>

<script>
    function changeAmt(id,val) {
        var value = $("#"+id).val();
        let id_array = id.split('_');
        var staffName = id_array[0]+'_'+id_array[1];
        //alert(val);
        if(value == ""){
            $("#"+id).val(0);
            var NetID = staffName+'_NET';
            var Net_Payable = $("#"+NetID).val();
            calculateSalary(Net_Payable,id,val);
        }else{
            var NetID = staffName+'_NET';
            var Net_Payable = $("#"+NetID).val();
            calculateSalary(Net_Payable,id,val);
        }
    }
    function calculateSalary(Net_Payable,id,val){
        var CompType = '';
        let id_array = id.split('_');
        var staffName = id_array[0]+'_'+id_array[1];
        var AccountID = id_array[1];
        var Comparray = <?php echo json_encode($SalaryHead); ?>;
        var total_earning = 0;
        var total_deduction = 0;
        var ESIC_Total = 0;
        for (var i = 0; i < Comparray.length; i++) {
            if(Comparray[i]['code'] != "NET" && Comparray[i]['code'] != "ESIC"){
                var CurrentHeadID = staffName+'_'+Comparray[i]['code'];
                if(CurrentHeadID == id){
                    CompType = Comparray[i]['type'];
                }
                var value = $("#"+CurrentHeadID).val();
                if(value !=""){
                    if(Comparray[i]['ESIC_Calculated'] == "Y"){
                        var ESIC = parseFloat(value) * (parseFloat(0.75) / 100);
                        ESIC_Total = parseFloat(ESIC_Total) + parseFloat(ESIC);
                    }
                    if(Comparray[i]['type'] == "1"){
                        total_earning = parseFloat(total_earning) + parseFloat(value);
                    }else{
                        total_deduction = parseFloat(total_deduction) + parseFloat(value);
                    }
                }
            }
        }
        var ESICID = 'Amt_'+AccountID+'_ESIC';
        $("#"+ESICID).val(parseFloat(ESIC_Total).toFixed(2));
        total_deduction = parseFloat(total_deduction) + parseFloat(ESIC_Total);
        var Cal = parseFloat(total_earning) - parseFloat(total_deduction)
        if(parseFloat(Net_Payable) >= parseFloat(Cal)){
            $("#total_earning_html_"+AccountID).html(parseFloat(total_earning).toFixed(2));
            $("#total_earning_"+AccountID).val(parseFloat(total_earning).toFixed(2));
            $("#total_deduction_html_"+AccountID).html(parseFloat(total_deduction).toFixed(2));
            $("#total_deduction_"+AccountID).val(parseFloat(total_deduction).toFixed(2));
            
            var yearly_ctc = parseFloat(total_earning) * 12;
            $("#yearly_ctc_html_"+AccountID).html(parseFloat(yearly_ctc).toFixed(2));
            $("#yearly_ctc_"+AccountID).val(parseFloat(yearly_ctc).toFixed(2));
        }else{
            if(CompType == "1"){
                total_earning = parseFloat(total_earning) - parseFloat(val)
            } else{
                total_deduction = parseFloat(total_deduction) - parseFloat(val)
            }
            $("#total_earning_html_"+AccountID).html(parseFloat(total_earning).toFixed(2));
            $("#total_earning_"+AccountID).val(parseFloat(total_earning).toFixed(2));
            $("#total_deduction_html_"+AccountID).html(parseFloat(total_deduction).toFixed(2));
            $("#total_deduction_"+AccountID).val(parseFloat(total_deduction).toFixed(2));
            
            var yearly_ctc = parseFloat(total_earning) * 12;
            $("#yearly_ctc_html_"+AccountID).html(parseFloat(yearly_ctc).toFixed(2));
            $("#yearly_ctc_"+AccountID).val(parseFloat(yearly_ctc).toFixed(2));
            alert('calculated value is exceed to Net Payable salary');
            $("#"+id).val(0);
        }
        if(parseFloat(Net_Payable) != parseFloat(Cal)){
            $("#total_earning_td_"+AccountID).css({'background-color':'#f71616','color':'#fff','font-size':'14px'});
            $("#total_deduction_td_"+AccountID).css({'background-color':'#f71616','color':'#fff','font-size':'14px'});
            $("#yearly_ctc_td_"+AccountID).css({'background-color':'#f71616','color':'#fff','font-size':'14px'});
            var log = $('#error_log').val();
            let AccountIDs = log.split(',');
            var isInArray = AccountIDs.includes(AccountID);
            if(isInArray == false){
                AccountIDs.push(AccountID);
            }
            let text = AccountIDs.toString();
            $('#error_log').val(text);
        }else{
            $("#total_earning_td_"+AccountID).css({'background-color':'#12bc23','color':'#fff','font-size':'14px'});
            $("#total_deduction_td_"+AccountID).css({'background-color':'#12bc23','color':'#fff','font-size':'14px'});
            $("#yearly_ctc_td_"+AccountID).css({'background-color':'#12bc23','color':'#fff','font-size':'14px'});
            var log = $('#error_log').val();
            let AccountIDs = log.split(',');
            var isInArray = AccountIDs.includes(AccountID);
            if(isInArray == true){
                AccountIDs.splice(AccountIDs.indexOf(AccountID), 1);
            }
            let text = AccountIDs.toString();
            $('#error_log').val(text);
        }
    }
    function changeValue(id,val) {
        var value = $("#"+id).val();
        let id_array = id.split('_');
        var staffName = id_array[0]+'_'+id_array[1];
        var AccountID = id_array[1];
        if(value == ""){
            $("#"+id).val(0);
        }else{
            var Comparray = <?php echo json_encode($SalaryHead); ?>;
            var total_earning = 0;
            var total_deduction = 0;
            var ESIC_Total = 0;
            for (var i = 0; i < Comparray.length; i++) {
                var CurrentHeadID = staffName+'_'+Comparray[i]['code'];
                if(Comparray[i]['mesuredIn'] == "2" && Comparray[i]['code'] != "ESIC"){
                    var per = Comparray[i]['percentage'];
                    var calBy = Comparray[i]['calculatedBy'];
                    var calBy_ID = staffName+'_'+Comparray[i]['calculatedBy'];
                    var BaseValue = $("#"+calBy_ID).val();
                    var calAmt = parseFloat(BaseValue) * (parseFloat(per) / 100);
                    $("#"+CurrentHeadID).val(parseFloat(calAmt).toFixed(2));
                    if(Comparray[i]['ESIC_Calculated'] == "Y"){
                        var ESIC = parseFloat(calAmt) * (parseFloat(0.75) / 100);
                        ESIC_Total = parseFloat(ESIC_Total) + parseFloat(ESIC);
                    }
                    if(Comparray[i]['type'] == "1"){
                        total_earning = parseFloat(total_earning) + parseFloat(calAmt);
                    }else{
                        total_deduction = parseFloat(total_deduction) + parseFloat(calAmt);
                    }
                }
            }
            
            var ESICID = 'Amt_'+AccountID+'_ESIC';
            $("#"+ESICID).val(parseFloat(ESIC_Total).toFixed(2));
            total_deduction = parseFloat(total_deduction) + parseFloat(ESIC_Total);
            $("#total_earning_html_"+AccountID).html(parseFloat(total_earning).toFixed(2));
            $("#total_earning_"+AccountID).val(parseFloat(total_earning).toFixed(2));
            $("#total_deduction_html_"+AccountID).html(parseFloat(total_deduction).toFixed(2));
            $("#total_deduction_"+AccountID).val(parseFloat(total_deduction).toFixed(2));
            
            var yearly_ctc = parseFloat(total_earning) * 12;
            $("#yearly_ctc_html_"+AccountID).html(parseFloat(yearly_ctc).toFixed(2));
            $("#yearly_ctc_"+AccountID).val(parseFloat(yearly_ctc).toFixed(2));
            var NetPayable = parseFloat(total_earning) - parseFloat(total_deduction);
            if(val != NetPayable){
                var log = $('#error_log').val();
                if(log != ""){
                    let AccountIDs = log.split(',');
                }else{
                    var AccountIDs = [];
                }
                var isInArray = AccountIDs.includes(AccountID);
                if(isInArray == false){
                    AccountIDs.push(AccountID);
                }
                var text = AccountIDs.toString();
                $('#error_log').val(text);
                $("#total_earning_td_"+AccountID).css({'background-color':'#f71616','color':'#fff','font-size':'14px'});
                $("#total_deduction_td_"+AccountID).css({'background-color':'#f71616','color':'#fff','font-size':'14px'});
                $("#yearly_ctc_td_"+AccountID).css({'background-color':'#f71616','color':'#fff','font-size':'14px'});
            }else{
                $("#total_earning_td_"+AccountID).css({'background-color':'#12bc23','color':'#fff','font-size':'14px'});
                $("#total_deduction_td_"+AccountID).css({'background-color':'#12bc23','color':'#fff','font-size':'14px'});
                $("#yearly_ctc_td_"+AccountID).css({'background-color':'#12bc23','color':'#fff','font-size':'14px'});
                var log = $('#error_log').val();
                if(log != ""){
                    let AccountIDs = log.split(',');
                }else{
                    let AccountIDs = [];
                }
                
                var isInArray = AccountIDs.includes(AccountID);
                if(isInArray == true){
                    AccountIDs.splice(AccountIDs.indexOf(AccountID), 1);
                }
                let text = AccountIDs.toString();
                $('#error_log').val(text);
            }
        }
    }
    function SubmitFormData() {
        var count = $('#error_log').val();
        //alert(count);
        if(count != ""){
            alert('Please check Net payable and Monthly Gross Amt is not equal');
        }else{
            if(confirm("Do you want to update salary...!")){
                $('#salary_form').submit();
            }else{
                return false;
            }
        }
        
        /*var InputArray = new Array();
        var i = 1;
        $("input[type=text]").each(function() {
            var ii = i - 1;
                InputArray[ii]=new Array();
                InputArray[ii][0]=this.name;
                InputArray[ii][1]=this.value;
                i++;
        });
        var ItemDivSerializedArr = JSON.stringify(InputArray);

        $.ajax({
            url:"<?php echo admin_url(); ?>rate_master/getUpdatedRate",
            method:"POST",
            data:{inputData:ItemDivSerializedArr}, 
        
            success: function(data){
                if(data){
                    Swal.fire({
                        position: 'top-end',
                        title: 'Rate Updated!',
                        padding: '5px',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false,
                        timerProgressBar: false,
                    })  
                }
               
            }
        });*/
        
    }
</script>