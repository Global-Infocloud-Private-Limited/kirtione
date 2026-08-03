<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    #AccountID {
        text-transform: uppercase;
    }

    #table_warehouse_List td:hover {
        cursor: pointer;
    }

    #table_warehouse_List tr:hover {
        background-color: #ccc;
    }


    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        padding: 1px 5px !important;
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
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="custom_button">
                                    <a class="btn btn-default" href="javascript:void(0);"    onclick="printPage();">Print</a>
                                </div>
                                <br>
                            </div>
                            <div class="col-md-2">
                                <?php 
                                    if($InvoiceDetails->IsPaid == "N"){
                                        ?>
                                        <button type="button" class="btn btn-info CreateReceipts" id="CreateReceipts" >Click to Paid</button>
                                    <?php
                                    }else{
                                        ?>
                                        <span style="font-size:15px;color:green;font-weight:600;">Already Paid</span>
                                    <?php
                                    }
                                ?>
                                <br>
                                
                            </div>
                            
                            <?php
                                if($InvoiceDetails->igstAmt > 0){
                                    $rowspan = 3;
                                }else{
                                    $rowspan = 4;
                                }
                                if($InvoiceDetails->CustomerType == 1){
                                    $partyName = 'Farmer';
                                    $supplierGSTN = 'Party';
                                    $supplierGSTNValue = 'Not Applicable';
                                    $address = '';
                                    if($InvoiceDetails->house){
                                        $address .= $InvoiceDetails->house.' ';
                                    }
                                    if($InvoiceDetails->street){
                                        $address .= $InvoiceDetails->street.' ';
                                    }
                                    if($InvoiceDetails->loc){
                                        $address .= $InvoiceDetails->loc.' ';
                                    }
                                    if($InvoiceDetails->po){
                                        $address .= ' ,'.$InvoiceDetails->po.' ';
                                    }
                                    if($InvoiceDetails->subdist){
                                        $address .= ' ,'.$InvoiceDetails->subdist.' ';
                                    }
                                    if($InvoiceDetails->dist){
                                        $address .= ' ,'.$InvoiceDetails->dist.' ';
                                    }
                                    if($InvoiceDetails->pincode){
                                        $address .= ' - '.$InvoiceDetails->pincode;
                                    }
                                    $state = $InvoiceDetails->AState;
                                }else{
                                    $partyName = 'Trader / Supplier';
                                    $supplierGSTN = 'Sup';
                                    $supplierGSTNValue = $InvoiceDetails->gstin;
                                    $state = $InvoiceDetails->GstState;
                                    $address = $InvoiceDetails->GstAddress;
                                }
                            ?>
                            <div class="col-md-12">
                                <table style="width: 100%;border:1px solid #333; font-size:10px;font-weight:400;" cellspacing="1" cellpadding="3">
                                    <thead>
                                        <tr>
                                            <td style="text-align:center;width:10% !important;border-right: 0px !important;" colspan="2"><img src="<?php echo site_url(); ?>/uploads/company/a093e544716efb366a062b996f0ca635.png" style="width:60px;height:40px;"></td>
                                            <td style="text-align:center;width:90% !important" colspan="10"><b style="width: 100%;text-align:center; font-size:18px;font-weight:700;"><b>Tax Invoice</b><br><?php echo $InvoiceDetails->PlantName; ?> </b><br>
                                                <span style="width: 100%;text-align:center; font-size:10px;"><?php echo $InvoiceDetails->address; ?> </span><br>
                                                <span style="width: 100%;text-align:center; font-size:10px;"><b>Supplier GSTN:</b><?php echo $InvoiceDetails->GstNo; ?> , <b>FSSAI No: </b> <?php echo  $InvoiceDetails->fssai_no; ?> </span><br>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="4" style="width:33% !important"><b>Invoice No. : </b><?php echo $InvoiceDetails->TransID;?></td>
                                            <input type="hidden" name="Invoice_number" id="Invoice_number" value="<?php echo $InvoiceDetails->TransID;?>">
                                            <td colspan="4" style="width:33% !important"><b>Invoice Date : </b><?php echo _d(substr($InvoiceDetails->TransDate,0,10));?></td>
                                            <td colspan="4" style="width:34% !important"><b>BookingID : </b><?php echo $InvoiceDetails->BookingID;?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"><b>Invoice From : </b><?php echo _d(substr($InvoiceDetails->InvFromDate,0,10));?></td>
                                            <td colspan="4"><b>Invoice To : </b><?php echo _d(substr($InvoiceDetails->InvToDate,0,10));?></td>
                                            <td colspan="4"><b>Service Type : </b><?php echo $InvoiceDetails->TransType;?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="8"><b><?php echo $partyName;?> : </b><?php echo $InvoiceDetails->company;?></td>
                                            <td colspan="4"><b><?php echo $supplierGSTN. 'GST'?> : </b><?php echo $supplierGSTNValue;?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="4"><b>Party State : </b><?php echo $state;?></td>
                                            <td colspan="8"><b>Party Address : </b><?php echo $address;?></td>
                                        </tr>
                                        
                                        <tr>
                                            <td colspan="4"><b>Location : </b><?php echo $InvoiceDetails->w_name;?></td>
                                            <td colspan="4"><b>Booking Date : </b><?php echo _d(substr($InvoiceDetails->b_date,0,10));?></td>
                                            <td colspan="4"><b>Invoice Amt : </b><?php echo $InvoiceDetails->InvoiceAmt;?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="12" style="height:20px;"></td>
                                        </tr>
                                        <tr>
                                            <th colspan="5" style="width:45% !important">Particular</th>
                                            <th style="width:10% !important">HSN</th>
                                            <th style="text-align:center;width:8% !important">GST</th>
                                            <th  colspan="2" style="text-align:center;width:12% !important">Amount</th>
                                            <th  style="text-align:center;width:10% !important">GST Amt</th>
                                            <th colspan="2" style="text-align:center;width:15% !important">Net Amt</th>
                                        </tr>
                                        
                                        <?php
                                            $taxabla_amt = 0;
                                            $cgst_amt = 0;
                                            $sgst_amt = 0;
                                            $igst_amt = 0;
                                            $inv_amt = 0;
                                            foreach($InvoiceDetails->Details as $key=>$val){
                                                $gst_amt = $val['cgst_amt'] + $val['sgst_amt'] + $val['igst_amt'];
                                                $taxabla_amt += $val['amount'];
                                                $cgst_amt += $val['cgst_amt'];
                                                $sgst_amt += $val['sgst_amt'];
                                                $igst_amt += $val['igst_amt'];
                                                $inv_amt += $val['InvAmt'];
                                                ?>
                                                    <tr>
                                                        <td colspan="5" style="width:45% !important"><?php echo $val['ItemName'];?></td>
                                                        <td style="text-align:center;width:10% !important"><?php echo $val['hsn_code'];?></td>
                                                        <td style="text-align:center;width:8% !important"><?php echo $val['taxrate']." % ";?></td>
                                                        <td colspan="2" style="text-align:right;width:12% !important"><?php echo number_format($val['amount'], 2, '.', '');?></td>
                                                        <td style="text-align:right;width:10% !important"><?php echo number_format($gst_amt, 2, '.', '');?></td>
                                                        <td colspan="2" style="text-align:right;width:15% !important"><?php echo number_format($val['InvAmt'], 2, '.', '');?></td>
                                                    </tr>
                                                <?php
                                            }
                                        ?>
                                        <tr>
                                            <td style="height:40px;" colspan="12"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" style="width:64% !important;text-align:center;"><b>GST Breakup</td>
                                            <td colspan="2" style="width:10% !important;text-align:center;"><b>Taxable Amt</b></td>
                                            <td style="width:8% !important;text-align:center;"><b>CGST Amt</b></td>
                                            <td style="width:8% !important;text-align:center;"><b>SGST Amt</b></td>
                                            <td style="width:8% !important;text-align:center;"><b>IGST Amt</b></td>
                                            <td colspan="2" style="width:12% !important;text-align:center;"><b>Inv Amt</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" style="width:64% !important;text-align:center;"></td>
                                            <td colspan="2" style="text-align:right;"><?php echo number_format($taxabla_amt, 2, '.', '');?></td>
                                            <td style="text-align:right;"><?php echo number_format($cgst_amt, 2, '.', '');?></td>
                                            <td style="text-align:right;"><?php echo number_format($sgst_amt, 2, '.', '');?></td>
                                            <td style="text-align:right;"><?php echo number_format($igst_amt, 2, '.', '');?></td>
                                            <td colspan="2" style="text-align:right;"><?php echo number_format($inv_amt, 2, '.', '');?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="12" style="height:40px;"></td>
                                        </tr>
                                        
                                        <tr>
                                            <td colspan= "8" rowspan="<?php echo $rowspan?>" style="vertical-align: top !important;"><b>Note : </b></td>
                                            <td colspan="2" style="text-align:right;"><b>Taxable Amt</b></td>
                                            <td colspan="2" style="text-align:right;"><b><?php echo number_format($taxabla_amt, 2, '.', '');?></b></td>
                                        </tr>
                                        <tr>
                                            <?php if($InvoiceDetails->igstAmt > 0){
                                                ?>
                                                    <td colspan="2" style="text-align:right;"><b>IGST Amt</b></td>
                                                    <td colspan="2" style="text-align:right;"><b><?php echo number_format($igst_amt, 2, '.', '');?></b></td>
                                                <?php
                                            }else{
                                                ?>
                                                    <td colspan="2" style="text-align:right;"><b>CGST Amt</b></td>
                                                    <td colspan="2" style="text-align:right;"><b><?php echo number_format($cgst_amt, 2, '.', '');?></b></td>
                                                <?php
                                            }?>
                                            
                                        </tr>
                                        <tr>
                                            <?php if($InvoiceDetails->igstAmt > 0){
                                                ?>
                                                    <td colspan="2" style="text-align:right;"><b>IGST Amt</b></td>
                                                    <td colspan="2" style="text-align:right;"><b><?php echo number_format($igst_amt, 2, '.', '');?></b></td>
                                                <?php
                                            }else{
                                                ?>
                                                    <td colspan="2" style="text-align:right;"><b>SGST Amt</b></td>
                                                    <td colspan="2" style="text-align:right;"><b><?php echo number_format($sgst_amt, 2, '.', '');?></b></td>
                                                <?php
                                            }?>
                                            
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="text-align:right;"><b>Invoice Amt</b></td>
                                            <td colspan="2" style="text-align:right;"><b><?php echo number_format($inv_amt, 2, '.', '');?></b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="8">
                                                <b>Bank A/c Details :</b><br>
                                                    <b><?php echo $InvoiceDetails->PlantName;?></b><br>
                                                    1. SBI - A/C - 30634015673, IFSC-SBIN0000086, Bank Road, Latur<br>
                                                    2. PNB - A/C - 1875009300045431, IFSC-PUNB0187500, Jubilee Road, Latur
                                            </td>
                                            <td colspan="4">
                                                <b><?php echo $InvoiceDetails->PlantName;?></b><br><br><br><br>
                                                <b>Authorized Signatory</b>
                                            </td>
                                        </tr>
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
$(document).ready(function(){
    $('#CreateReceipts').click(function(){
        var Invoice_number = $("#Invoice_number").val();
        if(Invoice_number == ""){
            alert('please refress page ');
        }else{
            if (confirm("Do you want to Update Payment Status?") == true) {
                $.ajax({
                    url:"<?php echo admin_url(); ?>GateControl/UpdateInvoiceStatus",
                    method:"POST",
                    dataType: "JSON",
                    data:{Invoice_number:Invoice_number},
                    beforeSend: function () {
                        $('#searchh3').css('display','block');
                    },
                    complete: function () {
                        $('#searchh3').css('display','none');
                    },
                    success:function(data){
                        window.location.reload(true);
                    }
                });
    		}else{
    			return false;
    		}
        }
    }); 
});
</script>
<script type="text/javascript">
    function printPage()
    {
        var html_filter_name =    $('.report_for').html();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        
        heading_data += '</tbody></table>';
        var print_data = stylesheet+tableData
        newWin= window.open("");
        newWin.document.write(print_data);
        newWin.print();
        newWin.close();
    };
</script>
