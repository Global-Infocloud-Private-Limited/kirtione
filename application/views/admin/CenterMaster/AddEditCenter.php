<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
	
	#AccountID {
    text-transform: uppercase;
	}
	#table_Center_List td:hover {
    cursor: pointer;
	}
	#table_Center_List tr:hover {
    background-color: #ccc;
	}
	
    
	table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	th     { background: #50607b;
	color: #fff !important; }
</style> 
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Center Master</b></li>
							</ol>
						</nav>
    					<hr class="hr_style">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="searchh2" style="display:none;">Please wait while fetching data.</div>
                                <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                                <div class="searchh4" style="display:none;">Please wait while updating data.</div>
							</div>
                            <br>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <small class="req text-danger">* </small>
                                    <label for="CenterID" class="control-label">Center ID</label>
                                    <input type="text" id="CenterID" name="CenterID" class="form-control" value="">
								</div>
							</div>
                            <div class="col-md-4">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <small class="req text-danger">* </small>
                                    <label for="CenterName" class="control-label">Center Name</label>
                                    <input type="text" id="CenterName" name="CenterName" class="form-control" value="">
								</div>
							</div>
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="CenterType">
									<small class="req text-danger">* </small>
                                    <label for="CenterType" class="control-label">Center Type</label>
                                    <select id="CenterType" name="CenterType" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value=""></option>
                                        <option value="F">Factory Location</option>
                                        <option value="M">Mandi Location</option>
                                        <option value="W">Warehouse Location</option>
									</select>
								</div>
							</div>
                            
                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="Premises">
									<small class="req text-danger">* </small>
                                    <label for="Premises" class="control-label">Premises</label>
                                    <select id="Premises" name="Premises" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value=""></option>
                                        <option value="O">Own Premises</option>
                                        <option value="KAS">Own KASPL Premises</option>
                                        <option value="S">Third Party WSP</option>
                                        <option value="KWPL">KisanMitra</option>
									</select>
								</div>
							</div>
							<div class="clearfix"></div>
                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="AccountID">
									<small class="req text-danger">* </small>
                                    <label for="commodity" class="control-label">Commodity</label>
                                    <select name="commodity" id="commodity" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true">
                                        <?php 
											foreach($commodity as $key=>$value){ ?>
											<option value="<?php echo $value['ItemID']; ?>"><?php echo $value['ItemName']; ?></option>
											<?php   }
										?>
									</select>
								</div>
							</div>
							
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <small class="req text-danger">* </small>
                                    <label for="state" class="control-label">State</label>
                                    <select name="state" id="state" class="selectpicker form-control" data-max-options="1" data-none-selected-text="Non Selected" data-live-search="true">
                                        
									</select>
								</div>
							</div>
                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <small class="req text-danger">* </small>
                                    <label for="city" class="control-label">City</label>
                                    <select id="city" name="city" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        
									</select>
								</div>
							</div>
                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="taluka">
									<small class="req text-danger">* </small>
                                    <label for="taluka" class="control-label">Taluka</label>
                                    <select id="taluka" name="taluka" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        
									</select>
								</div>
							</div>
                            <div class="clearfix"></div>
                            <div class="col-md-3">
                                <div class="form-group" app-field-wrapper="Pincode">
                                    <small class="req text-danger">* </small>
                                    <label class="control-label" for="Pincode">Pincode</label>
                                    <input type="text" name="Pincode" id="Pincode" class="form-control" value="" />
								</div>
							</div>
							
							<div class="col-md-6">
							    <div class="form-group" app-field-wrapper="address">
									<small class="req text-danger">* </small>
                                    <label class="control-label" for="address">Address</label>
                                    <textarea name = "address" id="address" class="form-control"></textarea>
								</div>
							</div>
							
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="regionID">
                                    <small class="req text-danger">* </small>
                                    <label for="regionID" class="control-label">Select Region</label>
                                    <select name="regionID" id="regionID" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <?php 
                                            foreach($RegionList as $key=>$value){ ?>
											<option value="<?php echo $value['AccountID']; ?>"><?php echo $value['region']; ?></option>
											<?php   }
										?>
									</select>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <small class="req text-danger">* </small>
									<label class="control-label" for="MobileNo">Mobile No</label>
                                    <?php $value = $User_details->gst_in;?>
                                    <input type="text" name="MobileNo" id="MobileNo" class="form-control" value="<?php echo $value; ?>" />
								</div>
							</div>
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <small class="req text-danger">* </small>
									<label class="control-label" for="Gst_no">GST Number</label>
                                    <?php $value = $User_details->gst_in;?>
                                    <input type="text" name="Gst_no" id="Gst_no" class="form-control" value="<?php echo $value; ?>" />
								</div>
							</div>
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <label class="control-label" for="latitude">Latitude</label>
                                    <input type="text" name="latitude" id="latitude" class="form-control" value="" />
								</div>
							</div>
							
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="AccountID">
                                   <!-- <small class="req text-danger">* </small>-->
									<label class="control-label" for="longitude">Longitude</label>
                                    <?php $value = $User_details->gst_in;?>
                                    <input type="text" name="longitude" id="longitude" class="form-control" value="<?php echo $value; ?>" />
								</div>
							</div>
							<div class="clearfix"></div>
							
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="Fertikizers">
									<label class="control-label" for="Fertikizers">Fertilizers LC.No(Retail)</label>
                                    <input type="text" name="Fertikizers" id="Fertikizers" class="form-control" value="" />
								</div>
							</div>
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="Insecticides">
									<label class="control-label" for="Insecticides">Insecticides LC.No(Retail)</label>
                                    <input type="text" name="Insecticides" id="Insecticides" class="form-control" value="" />
								</div>
							</div>
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="Seeds">
									<label class="control-label" for="Seeds">Seeds LC.No(Retail)</label> 
                                    <input type="text" name="Seeds" id="Seeds" class="form-control" value="" />
								</div>
							</div>
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="Cotton">
									<label class="control-label" for="Cotton">Cotton LC.No(Retail)</label>
                                    <input type="text" name="Cotton" id="Cotton" class="form-control" value="" />
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="Fertikizers2">
									<label class="control-label" for="Fertikizers2">Fertilizers LC.No(B2B)</label>
                                    <input type="text" name="Fertikizers2" id="Fertikizers2" class="form-control" value="" />
								</div>
							</div>
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="Insecticides2">
									<label class="control-label" for="Insecticides2">Insecticides LC.No(B2B)</label>
                                    <input type="text" name="Insecticides2" id="Insecticides2" class="form-control" value="" />
								</div>
							</div>
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="Seeds2">
									<label class="control-label" for="Seeds2">Seeds LC.No(B2B)</label> 
                                    <input type="text" name="Seeds2" id="Seeds2" class="form-control" value="" />
								</div>
							</div>
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="Cotton2">
									<label class="control-label" for="Cotton2">Cotton LC.No(B2B)</label>
                                    <input type="text" name="Cotton2" id="Cotton2" class="form-control" value="" />
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <label for="competitor" class="control-label">Select Competitor</label>
                                    <select name="competitor" id="competitor" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true">
                                        <?php 
                                            foreach($competitor as $key=>$value){ ?>
											<option value="<?php echo $value['CompetitorID']; ?>"><?php echo $value['Competitor']; ?></option>
											<?php   }
										?>
									</select>
								</div>
							</div>
							
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <label for="MandiID" class="control-label">Select Mandi</label>
                                    <select name="MandiID" id="MandiID" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true">
                                        <?php 
                                            foreach($Mandi as $key=>$value){ ?>
											<option value="<?php echo $value['CompetitorID']; ?>"><?php echo $value['Competitor']; ?></option>
											<?php   }
										?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="CenterStatus">
									<small class="req text-danger">* </small>
                                    <label for="CenterStatus" class="control-label">Center Status</label>
                                    <select id="CenterStatus" name="CenterStatus" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value=""></option>
                                        <option value="Y">Active</option>
                                        <option value="N">DeActive</option>
									</select>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-6">
							    <div class="form-group" app-field-wrapper="mac_address">
                                    <label class="control-label" for="mac_address">MAC Address</label>
                                    <textarea name = "mac_address" id="mac_address" class="form-control"></textarea>
								</div>
							</div>
							<div class="col-md-6">
							    <div class="form-group" app-field-wrapper="trade_condition">
									<!--<small class="req text-danger">* </small>-->
                                    <label class="control-label" for="trade_condition">Trade Condition</label>
                                    <textarea name = "trade_condition" cols = "5" id="trade_condition" class="form-control"></textarea>
								</div>
							</div>
                            
						</div>
                        <div class="row">
                           
                            <div class="col-md-8" style="margin-top:1%;">
                                <h4>Commision Master</h4>
							</div>
                            <div class="col-md-8">
                                <input type="hidden" name="ItemAdded" id="ItemAdded" value="">
                                <table class="table items table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="20%">Commodity</th>
                                            <th width="20%">Company Name</th>
                                            <th width="20%">Commision Amt(KASPL)</th>
                                            <th width="10%">&nbsp;</th>
										</tr>
									</thead>
									<tbody id="commision_body">
										<?php
											$allParameter = json_encode($commodity);
											$AllParties = json_encode($Parties);
										?>
										<tr>
											<td width="30%">
												<select id="Commodity1" name="Commodity" class="selectpicker" data-width="100%" data-none-selected-text="-- Item not found --" data-live-search="true">
													<option value= ''>-- Select Commodity --</option>
													<?php foreach($commodity as $key=>$value){ ?>
														<option value="<?= $value['ItemID']; ?>"><?= $value['ItemName']; ?></option> 
													<?php } ?>
												</select>
											</td>
											<td width="30%">
												<select id="PartyID1" name="PartyID" class="selectpicker" data-width="100%" data-none-selected-text="-- Plant not found --" data-live-search="true">
													<option value= ''>-- Select party --</option>
													<?php foreach($Parties as $key=>$value){ ?>
														<option value="<?= $value['PlantID']; ?>"><?= $value['PlantName']; ?></option> 
													<?php } ?>
												</select>
											</td>
											
											<td width="20%"><input id="commisionAmt" name="commisionAmt" type="text" value="" onkeypress="return isNumber(event)"></td>
											
											<td width="10%"><button type="button" onclick="add_row_commision()" class="btn btn-success" title="Add Commision Amt"><i class="fa fa-plus"></i></button></td>
										</tr>
									</tbody>
								</table>
							</div>
							<br><br>
							
						</div>
                        <br>
                        <div class="row">
                            
                            <div class="col-md-12">
                                <?php if (has_permission_new('CenterMaster', '', 'create')) {
								?>
								<button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                                <?php
									}else{
								?>
								<button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
                                <?php
								}?>
                                
                                <?php if (has_permission_new('CenterMaster', '', 'edit')) {
								?>
								<button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>
                                <?php
									}else{
								?>
								<button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
                                <?php
								}?>
                                
                                <button type="button" class="btn btn-default cancelBtn" >Cancel</button>
							</div>
						</div>
					</div>
                    <!------------ Modal ------------->
					<div class="modal fade CenterListModel" id="CenterListModel" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content">
                                <div class="modal-header" style="padding:5px 10px;margin-bottom: 5px;">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Center List</h4>
								</div>
                                <div class="modal-body" style="padding:0px 5px !important;margin-top: 5px;">
                                    <div class="col-md-5">
										<?php if (has_permission_new('CenterMaster', '', 'export')) {
										?>
                                        <a class="btn btn-default buttons-excel buttons-html2" tabindex="0"
										aria-controls="table-trial_bal_report" href="#" id="caexcel"
										style="float: left ! important;margin-right: 10px;"><span>Export to Excel</span></a>
										<?php } ?>
										<?php if (has_permission_new('CenterMaster', '', 'print')) {
										?>
                                        <a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
										<?php } ?>
									</div>
                                    
                                    <div class="table-StackListTable tableFixHead2">
                                        <table class="table table-striped table-bordered table-hover" id="table_Center_List" width="100%">
                                            <thead>
                                                <tr>
                                                    <th style="text-align:left;">Center ID </th>
                                                    <th style="text-align:left;">Center Name</th>
                                                    <th style="text-align:left;">Region</th>
                                                    <th style="text-align:left;">Center Type</th>
                                                    <th style="text-align:left;">Premises</th>
                                                    <th style="text-align:left;"> State</th>
                                                    <th style="text-align:left;"> City</th>
                                                    <th style="text-align:left;"> Status</th>
												</tr>
											</thead>
                                            <tbody id="Center_table_modal">
												
											</tbody>
										</table>   
									</div>
								</div>
                                <div class="modal-footer" style="padding:0px;">
                                    <input type="text" id="focusInput" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: left;width: 100%;">
								</div>
							</div>
						</div>
					</div>
                    <!----           Modal             ------->    
				</div>
			</div>
		</div>       
	</div>
</div>
<?php init_tail(); ?>
<style>
    .tableFixHead2          { overflow: auto;max-height: 45vh;width:100%;position:relative;top: 0px; }
	.tableFixHead2 thead th { position: sticky; top: 0; z-index: 1; }
	.tableFixHead2 tbody th { position: sticky; left: 0; }
	
	table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	th     { background: #50607b;
    color: #fff !important; } 
</style>
<script>
    
    function isNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode = 46 && charCode > 31 
		&& (charCode < 48 || charCode > 57)){
			return false;
		}
		return true;
	}
    $("#commision_body").on('click','.removeCommbtn',function(){
        $(this).parent().parent().remove();
	});
	
	function add_row_commision(){
		var Commodity = $("#Commodity1").val();
		var PartyID = $("#PartyID1").val();
		var commisionAmt = $("#commisionAmt").val();
		var ItemAdded = $("#ItemAdded").val();
		var ItemAddedListArray = ItemAdded.split(",");
		if(Commodity == ""){
		    alert("Please Select Commodity");
			}else if(PartyID == ""){
		    alert("Please Select Company");
			}else if(commisionAmt == ""){
		    alert("Please Enter Commision Amount");
		}else if(ItemAddedListArray.includes(Commodity))
		{
			alert("Selected Commodity already added");
			}else{
		    var lasttr = $('#commision_body tr:last td').find("select").attr('id');
			var num= lasttr.match(/-?\d+\.?\d*/);
			var newcount = parseInt(num)+parseInt(1);
			var allParameter = <?= $allParameter?>;
			var allParty = <?= $AllParties?>;
			
			markup = "<tr class='addedtr'>";
			markup += "<td><input type='hidden' name='addtblid[]' value='"+Commodity+"' class='CommodityName'><select name='Commodity1[]'  required id='Commodity"+newcount+"' value='"+Commodity+"' class='form-control selectpicker' data-live-search='true'></select></td>";
			markup += "<td><select name='PartyID1[]'  required id='PartyID"+newcount+"' value='"+PartyID+"' class='form-control selectpicker' data-live-search='true'></select></td>";
			markup += "<td><input name='commisionAmt1[]' id='commisionAmt"+newcount+"' value='"+commisionAmt+"' class='form-control' onkeypress='return isNumber(event)'></td>";
			markup += "<td><a href='#' style='float:right;padding: 2px;width: 30px; float:right;' style='float:right' id='removeCommbtn' class='btn btn-danger removeCommbtn'><i class='fa fa-times'></i></a></td></tr>";
			tableBody = $("#commision_body");
			tableBody.append(markup);
			
			for (var i = 0; i < allParameter.length; i++) {
				$("#Commodity"+newcount).append(new Option(allParameter[i].ItemName, allParameter[i].ItemID));
			}
			for (var i = 0; i < allParty.length; i++) {
				$("#PartyID"+newcount).append(new Option(allParty[i].PlantName, allParty[i].PlantID));
			}
			$("#Commodity"+newcount).val(Commodity);
			$("#Commodity"+newcount).selectpicker('refresh');
			
			$("#PartyID"+newcount).val(PartyID);
			$("#PartyID"+newcount).selectpicker('refresh');
			
			$("#Commodity1").val('');
			$('#Commodity1').selectpicker('refresh');
			$("#PartyID1").val('');
			$('#PartyID1').selectpicker('refresh');
			$("#commisionAmt").val('');
		}
	}
    
</script>
<script>
    
</script>
<script>	
    
	$('#city').on('change', function() {
		var CityID = $(this).val();
		var url = "<?php echo admin_url(); ?>clients/GetTaluka";
        $.ajax({
            type: 'POST',
            url:url,
            data: {CityID: CityID},
            dataType:'json',
            beforeSend: function () {
                $('select[name=taluka]').val('').selectpicker('refresh');
			},
            success: function(data) {
                $("#taluka").find('option').remove();
                $("#taluka").selectpicker("refresh");
                $("#taluka").append(new Option('', 'select taluka'));
                for (var i = 0; i < data.length; i++) {
                    $("#taluka").append(new Option(data[i].TalukaName, data[i].id));
				}
                $('.selectpicker').selectpicker('refresh');
			}
		});
	});
	
</script>
<script>
	$(document).ready(function () {
		$('#CenterID').keyup(function(){
			$(this).val($(this).val().toUpperCase());
		});
	});
</script>
<script>
    $('#CenterID').on('keydown', function(e) {
        var keyCode = e.keyCode || e.which;
        var CenterID = $('#CenterID').val();
        if(keyCode == 9){
            e.preventDefault(); 
            $.ajax({
                url: "<?php echo admin_url(); ?>Cluster/getSingleCenter",
                method:"POST",
                dataType:"JSON",
                data:{
                    CenterID:CenterID,
				},
                success:function(data){
                    if(data !=null){
                        $('#CenterID').val(data.CenterID);
                        $('#CenterName').val(data.CenterName);
                        $('#address').val(data.address);
                        $('#latitude').val(data.latitude);
                        $('#longitude').val(data.longitude);
                        $('#mac_address').val(data.mac_address);
                        $('#trade_condition').val(data.trade_condition);
                        $('select[name=CenterType]').selectpicker('refresh');
                        $('select[name=CenterType]').val(data.CenterType).selectpicker('refresh');
                        $('select[name=Premises]').selectpicker('refresh');
                        $('select[name=Premises]').val(data.Premises).selectpicker('refresh');
                        $('select[name=CenterStatus]').selectpicker('refresh');
                        $('select[name=CenterStatus]').val(data.status).selectpicker('refresh');
                        $('select[name=regionID]').selectpicker('refresh');
                        $('select[name=regionID]').val(data.RegionID).selectpicker('refresh');
                        if(data.CompetitorID){
                            var competitor = data.CompetitorID.split(",");
                            var valc = "";
                            for(j=0;j<competitor.length;j++){
                                if(j!=competitor.length-1){
                                    valc += competitor[j] +", ";
									}else{
                                    valc += competitor[j];
								}
							}
                            $('select[name=competitor]').selectpicker('val',valc.split(", "));
						}
                        
                        if(data.MandiIDs){
                            var MandiID = data.MandiIDs.split(",");
                            var valc = "";
                            for(j=0;j<MandiID.length;j++){
                                if(j!=MandiID.length-1){
                                    valc += MandiID[j] +", ";
									}else{
                                    valc += MandiID[j];
								}
							}
                            $('select[name=MandiID]').selectpicker('val',valc.split(", "));
						}
                        
                        if(data.Items){
                            var commodity = data.Items;
                            var valc = "";
                            for(j=0;j<commodity.length;j++){
                                if(j!=commodity.length-1){
                                    valc += commodity[j]['ItemID'] +", ";
									}else{
                                    valc += commodity[j]['ItemID'];
								}
							}
                            $('select[name=commodity]').selectpicker('val',valc.split(", "));
						}
    					let CommisionParameter = data.Commision;
    					
    					for(var count = 0; count < CommisionParameter.length; count++)
						{
							var ItemAdded = $("#ItemAdded").val();
							var tblid = CommisionParameter[count].id;
							var ItemID = CommisionParameter[count].ItemID;
							var PartyID = CommisionParameter[count].PartyID;
							var IsOn = CommisionParameter[count].IsOn;
							var CommisionAmt = CommisionParameter[count].CommisionAmt;
							var lasttr = $('#commision_body tr:last td').find("select").attr('id');
							var num= lasttr.match(/-?\d+\.?\d*/);
							var newcount = parseInt(num)+parseInt(1);
							
							var allParameter = <?= $allParameter?>;
							var allParty = <?= $AllParties?>;
							
							markup = "<tr class='addedtr'>";
							markup += "<td><input type='hidden' name='addtblid[]' value='"+ItemID+"' class='CommodityName'><select name='Commodity1[]' required id='Commodity"+newcount+"' value='"+ItemID+"' class='form-control selectpicker ' data-live-search='true'></select></td>";
							markup += "<td><select name='PartyID1[]' required id='PartyID"+newcount+"' value='"+PartyID+"' class='form-control selectpicker' data-live-search='true'></select></td>";
							markup += "<td><input name='commisionAmt1[]' id='commisionAmt"+newcount+"' value='"+CommisionAmt+"' class='form-control ' onkeypress='return isNumber(event)'></td>";
							markup += "<td><a href='#' style='padding: 4px;width: 35px;height: 30px;' class='btn btn-danger removebtn'><i class='fa fa-times'></i></a></td></tr>";
							tableBody = $("#commision_body");
							tableBody.append(markup);
							
							for (var i = 0; i < allParameter.length; i++) {
								$("#Commodity"+newcount).append(new Option(allParameter[i].ItemName, allParameter[i].ItemID));
							}
							for (var i = 0; i < allParty.length; i++) {
								$("#PartyID"+newcount).append(new Option(allParty[i].PlantName, allParty[i].PlantID));
							}
							$("#Commodity"+newcount).val(ItemID);
							$("#Commodity"+newcount).selectpicker('refresh');
							$("#PartyID"+newcount).val(PartyID);
							$("#PartyID"+newcount).selectpicker('refresh');
							$("#ItemAdded").val(ItemAdded+','+ItemID);
						}
                        
                        $('select[name=state]').val(data.state).selectpicker('refresh');
                        var state_id = $('#state :selected').val();
                        $.ajax({
                            url : "<?php echo admin_url(); ?>Cluster/GetCityFromState",
                            type: "post",
                            data: {
                                state_id: state_id,
							},
                            beforeSend: function(){
                                $('select[name=city]').val('').selectpicker('refresh');
							},
                            success: function(c){
                                $('select[name=city]').append(c).selectpicker('refresh');
                                $('select[name=city]').val(data.city).selectpicker('refresh');
                                
                                var CityID = data.city;
                                $.ajax({
                                    url : "<?php echo admin_url(); ?>Cluster/GetTalukaFromCity",
                                    type: "post",
                                    data: {
                                        CityID: CityID,
									},
                                    beforeSend: function(){
                                        $('select[name=taluka]').val('').selectpicker('refresh');
									},
                                    success: function(t){
                                        $('select[name=taluka]').append(t).selectpicker('refresh');
                                        $('select[name=taluka]').selectpicker('refresh');
                                        $('select[name=taluka]').val(data.taluka);
                                        $('select[name=taluka]').selectpicker('refresh');
									}
								});
								
							}
						});
                        
                        $('.saveBtn').hide();
                        $('.updateBtn').show();
                        $('.saveBtn2').hide();
                        $('.updateBtn2').show();
                        $('#CenterID').blur();
					}
				},
			});
		}
	});
</script>
<script>
    $('.cancelBtn').click(function(){
        $('input').val('');
        $('#mac_address').val('');
        $('#trade_condition').val('');
        $("#city").find('option').remove();
        $("#city").selectpicker("refresh");
        $("#taluka").find('option').remove();
        $("#taluka").selectpicker("refresh");
        $('.selectpicker').val('').selectpicker('refresh');
        $(".addedtr").remove(); 
        $('.saveBtn').show();
        $('.updateBtn').hide();
        $('.saveBtn2').show();
        $('.updateBtn2').hide();
	});
</script>
<script>
    $(document).ready(function(){
        $('.saveBtn').show();
        $('.updateBtn').hide();
        $('.saveBtn2').show();
        $('.updateBtn2').hide();
        $.ajax({
            url: "<?php echo admin_url(); ?>Cluster/getAllCenter",
            method:"POST",
            data:{
			},
            beforeSend: function(){
				$('#Center_table_modal').html('');  
			},
            success:function(data){
                $('#Center_table_modal').html(data);
			},
		});
        
        $.ajax({
            url : "<?php echo admin_url(); ?>Cluster/GetState",
            type: "post",
            data: {
			},
            beforeSend: function () {
                $('select[name=city]').val('').selectpicker('refresh');
			},
            success: function(data){
                $('select[name=state]').append(data).selectpicker('refresh');
			}
		});
	});
</script>
<script>
    $('.saveBtn').click(function(){
        var CenterID = $('#CenterID').val();
        var CenterName = $('#CenterName').val();
        var commodity = $('#commodity').val();
        var competitor = $('#competitor').val();
        var state = $('#state :selected').val();
        var city = $('#city :selected').val();
        var taluka = $('#taluka :selected').val();
        var Pincode = $('#Pincode').val();
        var CenterType = $('#CenterType :selected').val();
        var Premises = $('#Premises :selected').val();
        var mac_address = $('#mac_address').val();
        var address = $('#address').val();
        var trade_condition = $('#trade_condition').val();
		var CenterStatus = $('#CenterStatus').val();
		var regionID = $('#regionID').val();
		var latitude = $('#latitude').val();
		var longitude = $('#longitude').val();
		var MobileNo = $('#MobileNo').val();
		var Gst_no = $('#Gst_no').val();
		var Fertikizers = $('#Fertikizers').val();
		var Insecticides = $('#Insecticides').val();
		var Seeds = $('#Seeds').val();
		var Cotton = $('#Cotton').val();
		var Fertikizers2 = $('#Fertikizers2').val();
		var Insecticides2 = $('#Insecticides2').val();
		var Seeds2 = $('#Seeds2').val();
		var Cotton2 = $('#Cotton2').val();
	    let CommisionArr = [];
	    var i = 1;
	    var Commodity1 = $("select[name='Commodity1[]']")
		.map(function(){return $(this).val();}).get();
		Commodity1.forEach(function callback(value, index) {
			if(value != "")
			{
				var PartyID1 = $("select[name='PartyID1[]']")
				.map(function(){return $(this).val();}).get()[index];
				
				var commisionAmt1 = $("input[name='commisionAmt1[]']")
				.map(function(){return $(this).val();}).get()[index];
				
				var ii = i - 1;
				CommisionArr[ii]=new Array();
				CommisionArr[ii][0]=value;
				CommisionArr[ii][1]=PartyID1;
				CommisionArr[ii][2]=commisionAmt1;
				i++;
			}
		});
		
	    let CommissiondataArrlen = CommisionArr.length;
	    var CommisiondataSerializedArr = JSON.stringify(CommisionArr);
        if(CenterID == ''){
            alert('please enter CenterID');
			}else if(CenterName == ""){
            alert('please enter Center name');
			}else if(state == ""){
            alert('please select state');
			}else if(city == ""){
            alert('please select city');
			}else if(taluka == ""){
            alert('please select taluka');
			}else if(commodity == ""){
            alert('please select atleast one commodity');
			}else if(address == ""){
            alert('please enter center address');
			}else if(Pincode == ""){
            alert('please enter center Pincode');
			}else if(CenterType == ""){
            alert('please select type of center');
			}else if(Premises == ""){
            alert('please select premises');
			}else if(CenterStatus == ""){
            alert('please select center status');
			}else if(regionID == ""){
            alert('please select center region');
			}/*else if(latitude == ""){
            alert('please enter center latitude');
			}else if(longitude == ""){
            alert('please enter center longitude');
			}*/else if(MobileNo == ""){
            alert('please enter center mobile no');
			}else if(Gst_no == ""){
            alert('please enter center gst number');
			}/*else if(Fertikizers == ""){
            alert('please enter center fertikizers');
			}else if(Insecticides == ""){
            alert('please enter center insecticides');
			}else if(Seeds == ""){
            alert('please enter center seeds');
			}else if(Cotton == ""){
            alert('please enter center cotton');
			}*/else{
            $.ajax({
                url:"<?php echo admin_url(); ?>Cluster/SaveCenter",
                method: "POST",
                dataType: "JSON",
                data:{
                    CenterID: CenterID,latitude:latitude,longitude:longitude,
                    CenterName: CenterName,
                    address:address,
                    commodity:commodity,
                    competitor:competitor,
                    state: state,
                    city: city,
                    taluka:taluka,Pincode:Pincode,
                    CenterType:CenterType,
                    Premises:Premises,
                    CenterStatus:CenterStatus,regionID:regionID,
                    CommisiondataSerializedArr:CommisiondataSerializedArr,
                    CommissiondataArrlen:CommissiondataArrlen,
                    mac_address:mac_address,
                    trade_condition:trade_condition,
					MobileNo:MobileNo, Gst_no:Gst_no, Fertikizers:Fertikizers, Insecticides:Insecticides, Seeds:Seeds,
					Cotton:Cotton,Fertikizers2:Fertikizers2, Insecticides2:Insecticides2, Seeds2:Seeds2,Cotton2:Cotton2
				},
                success:function(data){
                    if(data == true){
                        $('input').val('');
                        $('#mac_address').val('');
                        $('#address').val('');
                        $('#trade_condition').val('');
                        $("#city").find('option').remove();
                        $("#city").selectpicker("refresh");
                        $("#taluka").find('option').remove();
                        $("#taluka").selectpicker("refresh");
                        $('.selectpicker').val('').selectpicker('refresh');
                        alert('Center Created Successful');
                        $(".addedtr").remove();
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
					}
				}    
			});
		}
	});
</script>
<script>
    $('.updateBtn').click(function(){
        var CenterID = $('#CenterID').val();
        var CenterName = $('#CenterName').val();
        var commodity = $('#commodity').val();
        var competitor = $('#competitor').val();
        var MandiID = $('#MandiID').val();
        var state = $('#state :selected').val();
        var city = $('#city :selected').val();
        var taluka = $('#taluka :selected').val();
        var mac_address = $('#mac_address').val();
        var address = $('#address').val();
        var Pincode = $('#Pincode').val();
        var CenterType = $('#CenterType :selected').val();
        var Premises = $('#Premises :selected').val();
        var trade_condition = $('#trade_condition').val();
        var CenterStatus = $('#CenterStatus').val();
        var regionID = $('#regionID').val();
        var latitude = $('#latitude').val();
		var longitude = $('#longitude').val();
		var MobileNo = $('#MobileNo').val();
		var Gst_no = $('#Gst_no').val();
		var Fertikizers = $('#Fertikizers').val();
		var Insecticides = $('#Insecticides').val();
		var Seeds = $('#Seeds').val();
		var Cotton = $('#Cotton').val();
		var Fertikizers2 = $('#Fertikizers2').val();
		var Insecticides2 = $('#Insecticides2').val();
		var Seeds2 = $('#Seeds2').val();
		var Cotton2 = $('#Cotton2').val();
        
		let CommisionArr = [];
	    var i = 1;
	    var Commodity1 = $("select[name='Commodity1[]']")
		.map(function(){return $(this).val();}).get();
		Commodity1.forEach(function callback(value, index) {
			if(value != "")
			{
				var PartyID1 = $("select[name='PartyID1[]']")
				.map(function(){return $(this).val();}).get()[index];
				
				var commisionAmt1 = $("input[name='commisionAmt1[]']")
				.map(function(){return $(this).val();}).get()[index];
				
				var ii = i - 1;
				CommisionArr[ii]=new Array();
				CommisionArr[ii][0]=value;
				CommisionArr[ii][1]=PartyID1;
				CommisionArr[ii][2]=commisionAmt1;
				i++;
			}
		});
		
	    let CommissiondataArrlen = CommisionArr.length;
	    var CommisiondataSerializedArr = JSON.stringify(CommisionArr);
        if(CenterID == ''){
            alert('please enter CenterID');
			}else if(CenterName == ""){
            alert('please enter Center name');
			}else if(state == ""){
            alert('please select state');
			}else if(city == ""){
            alert('please select city');
			}else if(taluka == ""){
            alert('please select taluka');
			}else if(commodity == ""){
            alert('please select atleast one commodity');
			}else if(address == ""){
            alert('please enter center address');
			}else if(Pincode == ""){
            alert('please enter center Pincode');
			}else if(CenterType == ""){
            alert('please select type of center');
			}else if(Premises == ""){
            alert('please select premises');
			}else if(CenterStatus == ""){
            alert('please select center status');
			}else if(regionID == ""){
            alert('please select center region');
			}/*else if(latitude == ""){
            alert('please enter center latitude');
			}else if(longitude == ""){
            alert('please enter center longitude');
			}*/else if(MobileNo == ""){
            alert('please enter center mobile no');
			}else if(Gst_no == ""){
            alert('please enter center gst number');
			}/*else if(Fertikizers == ""){
            alert('please enter center fertikizers');
			}else if(Insecticides == ""){
            alert('please enter center insecticides');
			}else if(Seeds == ""){
            alert('please enter center seeds');
			}else if(Cotton == ""){
            alert('please enter center cotton');
			}*/else{
            $.ajax({
                url:"<?php echo admin_url(); ?>Cluster/updateCenter",
                method: "POST",
                dataType: "JSON",
                data:{
                    CenterID: CenterID,
                    latitude:latitude,
                    longitude:longitude,
                    CenterName: CenterName,
                    commodity:commodity,
                    competitor:competitor,
                    MandiID:MandiID,
                    state: state,
                    city: city,
                    address:address,Pincode:Pincode,
                    taluka:taluka,
                    CenterType:CenterType,
                    Premises:Premises,
                    CenterStatus:CenterStatus,regionID:regionID,
                    CommissiondataArrlen:CommissiondataArrlen,CommisiondataSerializedArr:CommisiondataSerializedArr,
                    mac_address:mac_address,trade_condition:trade_condition,
					MobileNo:MobileNo, Gst_no:Gst_no, Fertikizers:Fertikizers, Insecticides:Insecticides, Seeds:Seeds,
					Cotton:Cotton,Fertikizers2:Fertikizers2, Insecticides2:Insecticides2, Seeds2:Seeds2,Cotton2:Cotton2
				},
                success:function(data){
                    if(data == true){
                        $('input').val('');
                        $('#mac_address').val('');
                        $('#address').val('');
                        $('#trade_condition').val('');
                        $("#city").find('option').remove();
                        $("#city").selectpicker("refresh");
                        $("#taluka").find('option').remove();
                        $("#taluka").selectpicker("refresh");
                        $('.selectpicker').val('').selectpicker('refresh');
                        alert('Center Updated Successful');
                        $(".addedtr").remove(); 
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
					}
				}    
			});
		}
	});
</script>
<script>
    $('#state').change(function(){
        var state_id = $('#state :selected').val();
        $.ajax({
            url : "<?php echo admin_url(); ?>Cluster/GetCityFromState",
            type: "post",
            data: {
                state_id: state_id,
			},
            success: function(data){
                $('select[name=city]').html(data).selectpicker('refresh');
			}
		});
	});
</script>
<script>
    $('#CenterID').dblclick(function(){
        $('#CenterListModel').modal('show');
        $('#CenterListModel').on('shown.bs.modal', function () {
            $('#focusInput').val('');
            $('#focusInput').focus();
		})
        $.ajax({
            url: "<?php echo admin_url(); ?>Cluster/getAllCenter",
            method:"POST",
            data:{
			},
            beforeSend: function(){
				$('#Center_table_modal').html('');  
			},
            success:function(data){
                $('#Center_table_modal').html(data);
                $('.saveBtn').hide();
                $('.updateBtn').show();
                $('.saveBtn2').hide();
                $('.updateBtn2').show();
			},
		});
	});
</script>
<script>
    function fill_data(CenterID){
        $('#CenterListModel').modal('hide');
        $.ajax({
            url: "<?php echo admin_url(); ?>Cluster/getSingleCenter",
            method:"POST",
            dataType:"JSON",
            data:{
                CenterID:CenterID,
			},
            success:function(data){
                $('#CenterID').val(data.CenterID);
                $('#CenterName').val(data.CenterName);
                $('#address').val(data.address);
                $('#Pincode').val(data.pincode);
                $('#latitude').val(data.latitude);
                $('#longitude').val(data.longitude);
				$('#MobileNo').val(data.MobileNo);
				$('#Gst_no').val(data.GSTNo);
				$('#Fertikizers').val(data.Fertikizers);
				$('#Insecticides').val(data.Insecticides);
				$('#Seeds').val(data.Seeds);
				$('#Cotton').val(data.Cotton);
				$('#Fertikizers2').val(data.Fertikizers2);
				$('#Insecticides2').val(data.Insecticides2);
				$('#Seeds2').val(data.Seeds2);
				$('#Cotton2').val(data.Cotton2);
                $('#mac_address').val(data.mac_address);
                $('#trade_condition').val(data.trade_condition);
                $('select[name=CenterType]').selectpicker('refresh');
                $('select[name=CenterType]').val(data.CenterType).selectpicker('refresh');
                $('select[name=Premises]').selectpicker('refresh');
                $('select[name=Premises]').val(data.Premises).selectpicker('refresh');
                $('select[name=CenterStatus]').selectpicker('refresh');
                $('select[name=CenterStatus]').val(data.status).selectpicker('refresh');
                $('select[name=regionID]').selectpicker('refresh');
                $('select[name=regionID]').val(data.RegionID).selectpicker('refresh');
                if(data.CompetitorID){
                    var competitor = data.CompetitorID.split(",");
                    var valc = "";
                    for(j=0;j<competitor.length;j++){
                        if(j!=competitor.length-1){
                            valc += competitor[j] +", ";
							}else{
                            valc += competitor[j];
						}
					}
                    $('select[name=competitor]').selectpicker('val',valc.split(", "));
				}
                if(data.Items){
                    var commodity = data.Items;
                    var valc = "";
                    for(j=0;j<commodity.length;j++){
                        if(j!=commodity.length-1){
                            valc += commodity[j]['ItemID'] +", ";
							}else{
                            valc += commodity[j]['ItemID'];
						}
					}
                    $('select[name=commodity]').selectpicker('val',valc.split(", "));
				}
                if(data.MandiIDs){
                    var MandiID = data.MandiIDs.split(",");
                    var valc = "";
                    for(j=0;j<MandiID.length;j++){
                        if(j!=MandiID.length-1){
                            valc += MandiID[j] +", ";
							}else{
                            valc += MandiID[j];
						}
					}
                    $('select[name=MandiID]').selectpicker('val',valc.split(", "));
				}
                
                var ItemAdded = $("#ItemAdded").val();
			    let CommisionParameter = data.Commision;
				for(var count = 0; count < CommisionParameter.length; count++)
                {
                    var tblid = CommisionParameter[count].id;
                    var ItemID = CommisionParameter[count].ItemID;
                    var PartyID = CommisionParameter[count].PartyID;
                    var IsOn = CommisionParameter[count].IsOn;
                    var CommisionAmt = CommisionParameter[count].CommisionAmt;
					var lasttr = $('#commision_body tr:last td').find("select").attr('id');
					var num= lasttr.match(/-?\d+\.?\d*/);
					var newcount = parseInt(num)+parseInt(1);
					
					var allParameter = <?= $allParameter?>;
					var allParty = <?= $AllParties?>;
					
					markup = "<tr class='addedtr'>";
					markup += "<td><input type='hidden' name='addtblid[]' value='"+ItemID+"' class='CommodityName'><select name='Commodity1[]' required id='Commodity"+newcount+"' value='"+ItemID+"' class='form-control selectpicker ' data-live-search='true'></select></td>";
					markup += "<td><select name='PartyID1[]' required id='PartyID"+newcount+"' value='"+PartyID+"' class='form-control selectpicker' data-live-search='true'></select></td>";
					markup += "<td><input name='commisionAmt1[]' id='commisionAmt"+newcount+"' value='"+CommisionAmt+"' class='form-control ' onkeypress='return isNumber(event)'></td>";
					markup += "<td><a href='#' style='padding: 4px;width: 35px;height: 30px;' class='btn btn-danger removebtn'><i class='fa fa-times'></i></a></td></tr>";
					tableBody = $("#commision_body");
					tableBody.append(markup);
					
					for (var i = 0; i < allParameter.length; i++) {
						$("#Commodity"+newcount).append(new Option(allParameter[i].ItemName, allParameter[i].ItemID));
					}
					for (var i = 0; i < allParty.length; i++) {
						$("#PartyID"+newcount).append(new Option(allParty[i].PlantName, allParty[i].PlantID));
					}
					$("#Commodity"+newcount).val(ItemID);
					$("#Commodity"+newcount).selectpicker('refresh');
					$("#PartyID"+newcount).val(PartyID);
					$("#PartyID"+newcount).selectpicker('refresh');
					$("#ItemAdded").val(ItemAdded+','+ItemID);
				}
                $('select[name=state]').val(data.state).selectpicker('refresh');
                var state_id = $('#state :selected').val();
                $.ajax({
                    url : "<?php echo admin_url(); ?>Cluster/GetCityFromState",
                    type: "post",
                    data: {
                        state_id: state_id,
					},
                    beforeSend: function(){
                        $('select[name=city]').val('').selectpicker('refresh');
					},
                    success: function(c){
                        $('select[name=city]').append(c).selectpicker('refresh');
                        $('select[name=city]').val(data.city).selectpicker('refresh');
                        $('select[name=city]').change();
                        var city_id = $('#city :selected').val();
                        $.ajax({
                            url : "<?php echo admin_url(); ?>clients/GetTaluka",
                            type: "post",
                            data: {
                                city_id: city_id,
							},
                            beforeSend: function(){
                                $('select[name=taluka]').val('').selectpicker('refresh');
							},
                            success: function(t){
                                $('select[name=taluka]').append(t).selectpicker('refresh');
                                $('select[name=taluka]').selectpicker('refresh');
                                $('select[name=taluka]').val(data.taluka);
                                $('select[name=taluka]').selectpicker('refresh');
							}
						});
					}
				});
                
                $('.saveBtn').hide();
                $('.updateBtn').show();
                $('.saveBtn2').hide();
                $('.updateBtn2').show();
			},
		});
	}
</script>
<script>
    $("#commision_body").on('click','.removebtn',function(){
        $(this).parent().parent().remove();
		
		var ii = $(this).parents("tr").find(".CommodityName").val();
		var ItemAdded = $("#ItemAdded").val();
		let result = ItemAdded.replace(ii, " ");
		$("#ItemAdded").val(result);
	});
</script>
<script>
    $('#CenterID').focus(function(){
        $('input').val('');
        $('#mac_address').val('');
        $('#trade_condition').val('');
        $("#city").find('option').remove();
        $("#city").selectpicker("refresh");
        $("#taluka").find('option').remove();
        $("#taluka").selectpicker("refresh");
        $('.selectpicker').val('').selectpicker('refresh');
        $(".addedtr").remove(); 
        $('.saveBtn').show();
        $('.updateBtn').hide();
        $('.saveBtn2').show();
        $('.updateBtn2').hide();
	});
</script>
<script>
    function myFunction2() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("focusInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("table_Center_List");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            td1 = tr[i].getElementsByTagName("td")[1];
            td2 = tr[i].getElementsByTagName("td")[2];
            td3 = tr[i].getElementsByTagName("td")[3];
			if(td) {
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
								}else{
								tr[i].style.display = "none";
							} 
						}
					}
					
				}     
			}
		}
	}
</script>

<script type="text/javascript">
    function printPage() {
        var html_filter_name = $('.report_for').html();
        var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">' + document.getElementsByTagName('table')[1].innerHTML + '</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Center List Report</td>';
        heading_data += '</tr>';
        
		
        heading_data += '</tbody></table>';
        var print_data = stylesheet + heading_data + tableData
        newWin = window.open("");
        newWin.document.write(print_data);
        newWin.print();
        newWin.close();
	};
</script>

<script>
	$("#caexcel").click(function(){
		var data_val = "data";
		$.ajax({
			url:"<?php echo admin_url(); ?>Cluster/export_centerMaster",
			method:"POST",
			data:{data_val:data_val,},
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