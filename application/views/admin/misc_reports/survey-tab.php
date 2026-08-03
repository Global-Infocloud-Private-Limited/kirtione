<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    .table-purchase_request          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
.table-purchase_request thead th { position: sticky; top: 0; z-index: 1; }
.table-purchase_request tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
tbody#for_uppercase{
    text-transform:uppercase;
}

</style>
<style>
body { background-color: #fff;}


/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  color : #fff;
  background-color: #50607b;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #808080;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #fff;
  color:#50607b;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}

.tablinks{
	padding :10px 11px !important;
	font-size:14px !important;
}
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
		    <div class="col-md-10">
		        <div class="panel_s">
                    <div class="panel-body">
						<div class="row">
							<div class="col-md-5">
								<h4 style="padding: 1%;">Survey Details :</h4>
								<table>
								<tr>
									<td>Full Name</td>
									<td><?php echo $details->name; ?></td>
								</tr>
								<tr>
									<td>Mobile No.</td>
									<td><?php echo $details->mobile_number; ?></td>
								</tr>
								<tr>
									<td>State</td>
									<td><?php echo $details->state_name; ?></td>
								</tr>
								<tr>
									<td>City</td>
									<td><?php echo $details->city_name; ?></td>
								</tr>
								<tr>
									<td>Taluka</td>
									<td><?php echo $details->TalukaName; ?></td>
								</tr>
								<tr>
									<td>Village</td>
									<td><?php echo $details->village; ?></td>
								</tr> 
								</table>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-12" style="margin-top: 2%;">
								<div class="tab" style="line-height: 0.6;">
								  <button class="tablinks active" onclick="openCity(event, 'dependants')">Dependants</button>
								  <button class="tablinks" onclick="openCity(event, 'water')">Water Resource</button>
								  <button class="tablinks" onclick="openCity(event, 'equipment')">Equipment</button>
								  <button class="tablinks" onclick="openCity(event, 'livestock')">Livestock</button>
								  <button class="tablinks" onclick="openCity(event, 'crop')">Crop Pattern</button>
								  <button class="tablinks" onclick="openCity(event, 'production')">Production Cost</button>
								  <button class="tablinks" onclick="openCity(event, 'labour')">Labour Availability</button>
								  <button class="tablinks" onclick="openCity(event, 'govt')">Government Schemes</button>
								  <button class="tablinks" onclick="openCity(event, 'smartphone')">Smartphone Usage</button>
								</div>
							</div>
						</div>
						
						<div class="clearfix"></div>
							<div class="col-md-12" style="margin-top: 1%;" >
								<div id="dependants" class="tabcontent" style="display:block">
								<!--Dependants -->
									<div class="row">
										<div class="col-md-8">
											<h4>Dependants : </h4> 
											<div class="table-purchase_request tableFixHead2">
											<table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
											<tbody id="for_uppercase">
											<tr>
												<th align="center">Type</th>
												<th align="center">Total Count</th>
												<th align="center">Survey/Gut No.</th>
												<th align="center">Irrigated Land</th>
												<th align="center">Un-Irrigated Land</th>
												<th align="center">Total Land Holding</th>
											</tr>
										<?php foreach ($dependants as $key => $value) { ?>
											<tr>
												<td align="center"><?php echo $value["name"] ?></td>
												<td align="center"><?php echo $value["number"] ?></td>
												<td align="center"><?php echo $value["gut_number"] ?></td>
												<td align="center"><?php echo $value["Irrigated_land"] ?></td>
												<td align="center"><?php echo $value["UnIrrigated_land"] ?></td>
												<td align="center"><?php echo $value["total_land"] ?></td>
											</tr>
										<?php } ?>
											</tbody>
											</table>  
											</div>
										</div>
									</div>
								</div>

								<div id="water" class="tabcontent">
								<!--water resource-->
									<div class="row">
										<div class="col-md-8">
											<h4>Water Resource :</h4>
											<div class="table-purchase_request tableFixHead2" style="width:83%">
												<table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
												<thead>
												    <th>Type</th>
												    <th>Value</th>
												</thead>
												<tbody id="for_uppercase">
												<tr>
													<td style="width: 70%;"><b>Well (Ft) : </b></td>
													<td><?php echo $details->well; ?></td>
												</tr>
												<tr>
													<td style="width: 70%;"><b>Borewell (Ft) : </b></td>
													<td><b><?php echo $details->borewell; ?></b></td>
												</tr>
												<tr>
													<td style="width: 70%;"><b>Canal (water Availability during cultivation - Days in week) : </b></td>
													<td><?php echo $details->canal; ?></td>
												</tr>
												<tr>
													<td style="width: 70%;"><b>River/Nala (Months) : </b></td>
													<td><?php echo $details->river_nala; ?></td>
												</tr>
												<tr>
													<td style="width: 70%;"><b>Farm Pond (Area & storage capacity) : </b></td>
													<td><?php echo $details->farm_pond; ?></td>
												</tr>
												<tr>
													<?php
														if($details->fisheries == 'Y'){
															$fishery = 'Yes';
														}else{
															$fishery = 'No'; 
														}?>
														<td style="width: 70%;"><b>Have Fisheries Business: </b></td>
														<td><?php echo $fishery; ?></td>
												</tr>
												<tr>
													<td style="width: 70%;"><b>Fisheries Revenue (If Any) : </b></td>
													<td><?php echo $details->fisheries_revenue; ?></td>
												</tr>
												</tbody>
												</table>  
											</div>
										</div>
									</div>
								</div>
								<div id="equipment" class="tabcontent">
								  <!-- Equipment -->
									<div class="row">
										<div class="col-md-6">
											<h4>Equipment : </h4> 
											<div class="table-purchase_request tableFixHead2">
												<table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
												<tbody id="for_uppercase">
												<tr>
													<th align="left">Type</th>
													<th align="center">Total Count</th>
													<th align="left">Company</th>
												</tr>
												<?php foreach ($equipment as $key => $value) { ?>
													<tr>
														<td align="left"><?php echo $value["name"] ?></td>
														<td align="center"><?php echo $value["number"] ?></td>
														<td align="left"><?php echo $value["company"] ?></td>
													</tr>
												<?php } ?>
											</tbody>
											</table>  
											</div>
										</div>
									</div>
								</div>
								<div id="livestock" class="tabcontent">
									<!-- Livestock -->
										<div class="row">
											<div class="col-md-8">
												<h4>Livestock : </h4> 
												<div class="table-purchase_request tableFixHead2" >
													<table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
													<tbody id="for_uppercase">
														<tr>
															<th align="left">Type</th>
															<th align="center">Total Count</th>
															<th align="center">Milk/Day</th>
															<th align="left">Breed</th>
														</tr>
														<?php foreach ($livestock as $key => $value) { ?>
															<tr>
																<td align="left"><?php echo $value["name"] ?></td>
																<td align="center"><?php echo $value["number"] ?></td>
																<td align="center"><?php echo $value["milk_per_day"] ?></td>
																<td align="left"><?php echo $value["breed"] ?></td>
															</tr>
														<?php } ?>
													</tbody>
												</table>  
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-8">
											<h4>Cattle Feed Cost : </h4>
											<div class="table-purchase_request tableFixHead2" style="width:70%">
												<table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
												<tbody id="for_uppercase">
													<tr>
														<td style="width: 60%;"><b>Per day Requirement (Kg) : </b></td>
														<td><?php echo $details->Feed_per_day; ?></td>
													</tr>
													<tr>
														<td style="width: 60%;"><b>General Purchase : </b></td>
														<td><b><?php echo $details->Feed_purchase; ?></b></td>
													</tr>
													<tr>
														<td style="width: 60%;"><b>Avg. Cost Per Kg : </b></td>
														<td><?php echo $details->FeedAvgCostPerKG; ?></td>
													</tr>
													<tr>
														<td style="width: 60%;"><b>Preferred Feed Manufacturing Company : </b></td>
														<td><?php echo $details->FeedCompany; ?></td>
													</tr>
													<tr>
														<td style="width: 60%;"><b>Sells in nearby town : </b></td>
														<td><?php echo 'Earning/Ltr Rs. '.$details->OtherRate; ?></td>
													</tr>
													<tr>
														<td style="width: 60%;"><b>if sold to nearest Milk collection centre (Dairy) : </b></td>
														<td><?php echo 'Earning/Ltr Rs. '.$details->DairyRate; ?></td>
													</tr>
												</tbody>
												</table>  
											</div>
										</div>
									</div>
								</div>
								<div id="crop" class="tabcontent">
									<!-- Crop Pattern -->
									<div class="row">
										<div class="col-md-8">
											<h4>Crop Pattern : </h4> 
												<div class="table-purchase_request tableFixHead2">
													<table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
													<tbody id="for_uppercase">
													<tr>
														<th align="center">Year</th>
														<th align="left">Particulars</th>
														<th align="left">Kharif</th>
														<th align="left">Rabi</th>
													</tr>
												<?php foreach ($crop as $key => $value) { ?>
													<tr>
														<td align="center"><?php echo $value["Year"] ?></td>
														<td align="left"><?php echo $value["name"] ?></td>
														<td align="left"><?php echo $value["kharif"] ?></td>
														<td align="left"><?php echo $value["rabi"] ?></td>
													</tr>
												<?php } ?>
													</tbody>
													</table>  
												</div>
											</div>
										</div>
									</div>

									<div id="production" class="tabcontent">
									  <!-- Production Cost -->
										<div class="row">
											<div class="col-md-8">
												<h4>Production Cost : </h4> 
													<div class="table-purchase_request tableFixHead2">
														<table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
														<tbody id="for_uppercase">
															<tr>
																<th align="left">Cost Type</th>
																<th align="left">Name</th>
																<th align="right">Value</th>
															</tr>
														<?php foreach ($production as $key => $value) { ?>
															<tr>
																<td align="left"><?php echo $value["CostType"] ?></td>
																<td align="left"><?php echo $value["name"] ?></td>
																<td align="right"><?php echo $value["value"] ?></td>
															</tr>
														<?php } ?>
													</tbody>
													</table>  
												</div>
											</div>
										</div>
									</div>

									<div id="labour" class="tabcontent">
									  <!-- Labour availability  -->
											<div class="row">
												<div class="col-md-8">
													<h4>Labour Availability  : </h4>
														<div class="table-purchase_request tableFixHead2" style="width:60%">
															<table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
															<tbody id="for_uppercase">
																<tr>
																	<td style="width: 70%;"><b>Available In same Village : </b></td>
																	<td><?php echo $details->labour_in_village; ?></td>
																</tr>
																<tr>
																	<td style="width: 70%;"><b>To Be made Available form nearby village : </b></td>
																	<td><b><?php echo $details->labour_in_nearby_village; ?></b></td>
																</tr>
																<tr>
																	<td style="width: 70%;"><b>Male labour daily wages : </b></td>
																	<td><?php echo $details->male_labour_cost; ?></td>
																</tr>
																<tr>
																	<td style="width: 70%;"><b>Female labour daily wages : </b></td>
																	<td><?php echo $details->female_labour_cost; ?></td>
																</tr>
															</tbody>
														</table>  
													</div>
												</div>
											</div>
									</div>

									<div id="govt" class="tabcontent">
										<!-- Govt Schemes  -->
											<div class="row">
												<div class="col-md-8">
													<h4>Government Schemes  : </h4>
														<div class="table-purchase_request tableFixHead2" style="width:60%">
															<table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
															<tbody id="for_uppercase">
															    <tr>
																	<td style="width: 70%;"><b>Solar Pump : </b></td>
																	<td><?php echo $details->solar_pump; ?></td>
																</tr>
																<tr>
																	<td style="width: 70%;"><b>Solar Pump Capacity : </b></td>
																	<td><?php echo $details->solar_capacity; ?></td>
																</tr>
																<tr>
																	<td style="width: 70%;"><b>Crop Insurance : </b></td>
																	<td><b><?php echo $details->crop_insurance; ?></b></td>
																</tr>
																<tr>
																	<td style="width: 70%;"><b>Crop Insurance Company : </b></td>
																	<td><b><?php echo $details->insurance_company; ?></b></td>
																</tr>
																<tr>
																	<td style="width: 70%;"><b>Compensations Received: </b></td>
																	<td><?php echo $details->compensations_received; ?></td>
																</tr>
																<tr>
																	<td style="width: 70%;"><b>PM Kisan samman Nidhi Received : </b></td>
																	<td><?php echo $details->PMKSN; ?></td>
																</tr>
																<tr>
																	<td style="width: 70%;"><b>Agri Equipment Supply From Panchayat Committee : </b></td>
																	<td><?php echo $details->AgriEquipmentByPanchayat; ?></td>
																</tr>
															</tbody>
															</table>  
														</div>
													</div>
												</div>
									</div>

								<div id="smartphone" class="tabcontent">
								<!-- Smartphone Usage  -->
									<div class="row">
										<div class="col-md-8">
											<h4>Smartphone Usage  : </h4>
												<div class="table-purchase_request tableFixHead2" style="width:80%">
													<table class="tree table table-striped table-bordered table-purchase_request tableFixHead2" id="table-purchase_request" width="100%">
													<tbody id="for_uppercase">
													<tr>
														<td style="width: 70%;"><b>Smartphone Holders In family : </b></td>
														<td><?php echo $details->smart_phone_user; ?></td>
													</tr>
													<tr>
														<td style="width: 70%;"><b>Whatsapp Users : </b></td>
														<td><b><?php echo $details->WhatsAppUser; ?></b></td>
													</tr>
													<tr>
														<td style="width: 70%;"><b>Whether youtube videos referred for better cultivation : </b></td>
														<td><?php echo $details->youtube_referred; ?></td>
													</tr>
													<tr>
														<td style="width: 70%;"><b>Whatsapp users subscribed for agri service: </b></td>
														<td><?php echo $details->WhatsAppAgriService; ?></td>
													</tr>
													<tr>
														<td style="width: 70%;"><b>Service paid for free : </b></td>
														<td><?php echo $details->ServiceIsPaid; ?></td>
													</tr>
													<tr> 
														<td style="width: 70%;"><b>Subscription Charges : </b></td>
														<td><?php echo $details->ServicePaidAmt; ?></td>
													</tr>
													<tr>
														<td style="width: 70%;"><b>Charges Payment Frequency : </b></td>
														<td><?php echo $details->PaymentFrquancy; ?></td>
													</tr>
													<tr>
														<td style="width: 70%;"><b>Explores Weather Forecast/ Market Forecast/ Govt Scheme : </b></td>
														<td><?php echo $details->mob_used_for_forcasting; ?></td>
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
		</div>
	</div>
</div>





<script>
function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>
 
<?php init_tail(); ?>