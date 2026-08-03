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
			<div class="col-md-10">
				<div class="panel_s">
					<div class="panel-body">                        						 
                            <div class="row"> 
                                <nav aria-label="breadcrumb" >
                                    <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                                        <li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                        <li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
                                        <li class="breadcrumb-item active" aria-current="page"><b>Form</b></li>
                                    </ol>
                                </nav>
                                <hr class="hr_style">

                                <div class="col-md-12">
                                    <div class="searchh2" style="display:none;">Please wait while fetching data.</div>                                    
                                    <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                                    <div class="searchh4" style="display:none;">Please wait while updating data.</div>
                                </div> 

                                <br>                                                                                           
                                <div class="col-md-2">
                                    <div class="form-group">                                                                          
                                       <?php                                             
                                          $current_date = date('d/m/Y');
                                          echo render_date_input('visitdate','Visit Date',$current_date);   
                                       ?>                                       
                                    </div>
                                </div>  
                                
                                <div class="col-md-2">
                                    <div class="form-group">  
                                        <small class="req text-danger">* </small>                                    
                                        <label for="villagename">Village Name</label>
                                        <input type="text" name="VillageName" id="villagename" class="form-control" value="">                                        
                                        <div id="VillageIDError" style="color: red;"></div>
                                    </div>
                                </div>                          

                                <div class="col-md-2">
                                    <div class="form-group" app-field-wrapper="Pincode">                                       
                                        <small class="req text-danger">* </small>
                                        <label for="pin" class="form-label">Pincode</label>
                                    <select name="Pincode" id="pin" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value=""></option> 
                                        <?php
                                          foreach($pincodes as $pin) 
                                          {
                                            echo '<option value="' . $pin['Pincode'] . '">' . $pin['Pincode'] . '</option>';
                                          } 
                                        ?>   
                                    </select>
                                    <div id="PinIDError" style="color: red;"></div>
                                    </div>
                                </div>  

                                <div class="col-md-2">
                                    <div class="form-group">  
                                        <small class="req text-danger">* </small>                                    
                                        <label for="state">State</label>
                                        <input type="text" name="statename" id="state" class="form-control" value="" readonly>                                        
                                        <input type="hidden" name="state_id" id="state_id" />
                                        <div id="StateIDError" style="color: red;"></div>  
                                    </div>
                                </div>   

                                <div class="col-md-2">
                                    <div class="form-group">  
                                        <small class="req text-danger">* </small>                                    
                                        <label for="district">District</label>
                                        <input type="text" name="District" id="district" class="form-control" value="" readonly>                                        
                                        <input type="hidden" name="district_id" id="district_id" />
                                        <div id="DistIDError" style="color: red;"></div> 
                                    </div>
                                </div>   

                                <div class="col-md-2">
                                    <div class="form-group">  
                                        <small class="req text-danger">* </small>                                    
                                        <label for="taluka">Taluka</label>
                                        <input type="text" name="Taluka" id="taluka" class="form-control" value="" readonly>                                        
                                        <input type="hidden" name="taluka_id" id="taluka_id" />
                                        <div id="TalukaIDError" style="color: red;"></div> 
                                    </div>
                                </div>   

                                <!-- <div class="col-md-2">
                                    <div class="form-group" app-field-wrapper="State">      
                                        <small class="req text-danger">* </small>                                
                                        <label for="state">State</label>
                                    <select name="statename" id="state" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">> 
                                        <option value=""></option> 
                                        <?php
                                          foreach($statedetails as $state) 
                                          {
                                            echo '<option value="' . $state['short_name'] . '">' . $state['state_name'] . '</option>';
                                          } 
                                        ?>    
                                    </select>    
                                    <div id="StateIDError" style="color: red;"></div>                              
                                    </div>
                                </div>   -->
                                
                                <!-- <div class="col-md-2">
                                    <div class="form-group" app-field-wrapper="District">   
                                        <small class="req text-danger">* </small>                                   
                                        <label for="district">District</label>
                                    <select name="District" id="district" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value=""></option> 
                                        <?php
                                          foreach($citydetails as $city) 
                                          {
                                            echo '<option value="' . $city['id'] . '">' . $city['city_name'] . '</option>';
                                          } 
                                        ?>  
                                    </select>
                                    <div id="DistIDError" style="color: red;"></div> 
                                    </div>
                                </div>  -->

                                <!-- <div class="col-md-2">
                                    <div class="form-group" app-field-wrapper="Taluka"> 
                                        <small class="req text-danger">* </small>                                     
                                        <label for="taluka">Taluka</label>
                                    <select name="Taluka" id="taluka" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value=""></option> 
                                        <?php
                                          foreach($talukadetails as $taluka) 
                                          {
                                            echo '<option value="' . $taluka['id'] . '">' . $taluka['TalukaName'] . '</option>';
                                          } 
                                        ?>       
                                    </select>
                                    <div id="TalukaIDError" style="color: red;"></div> 
                                    </div>
                                </div>  -->
                            </div>	                     
                            <div class="clearfix"></div>

                            <div class="row"> 
                                <div class="col-md-3">
                                    <div class="form-group">    
                                        <small class="req text-danger">* </small>                                   
                                        <label for="sarpanch">Village Sarpanch</label>
                                        <input type="text" name="VillageSarpanch" id="sarpanch" class="form-control" value="">                                        
                                        <div id="SarpanchError" style="color: red;"></div> 
                                    </div>
                                </div>                         

                                <div class="col-md-3">
                                    <div class="form-group">                                      
                                        <label for="population">Village Population</label>
                                        <input type="text" name="VillagePopulation" id="population" class="form-control" value="">                                        
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">                                      
                                        <label for="area">Area in Hector</label>
                                        <input type="text" name="Areainhector" id="area" class="form-control" value="">                                        
                                    </div>
                                </div>  

                                <div class="col-md-4">
                                    <div class="form-group">                                      
                                        <label for="influencername">Village Influencer Name</label>
                                        <input type="text" name="VillageinfluencerName" id="influencername" class="form-control" value="">                                        
                                    </div>
                                </div>   
                            </div>
                            <div class="clearfix"></div>	
                                    
                            <div class="row"> 
                                <div class="col-md-3">
                                    <div class="form-group">                                      
                                        <label for="govtpost">Influencer Government Post</label>
                                        <input type="text" name="GovtPost" id="govtpost" class="form-control" value="">                                        
                                    </div>
                                </div>  
                                
                                <div class="col-md-3">
                                    <div class="form-group">                                      
                                        <label for="mobileno">Influencer Mobile No</label>
                                        <input type="text" name="MobileNo" id="mobileno" class="form-control" value="">                                        
                                    </div>
                                </div>   
                                
                                <div class="col-md-2">
                                    <div class="form-group">                                      
                                        <label for="rtrsno">No.of RTRS Farmers</label>
                                        <input type="text" name="rtrsname" id="rtrsno" class="form-control" value="">                                        
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">                                      
                                        <label for="info">Other information</label>
                                        <textarea name="otherinfo" id="info" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div> 
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <p class="bold p_style" style="color: #D5006D;">Village Aggregator Details</p>
                                    <hr class="hr_style" style="border-color: #D5006D; border-width: 1.5px;">
                                    <table width="100%">
                                        <thead>
                                            <tr>                                              
                                                <th>Village Aggregator Name</th>
                                                <th>Village Aggregator Mobile No</th>                                                
                                                <th>Add New</th>
                                            </tr>
                                        </thead>
                                        <tbody id="aggregatortbody">
                                            <tr>                                            
                                                <td style="text-align:center;">
                                                    <input type="text" id="aggregatorname" name="aggname[]" class="form-control">
                                                    <div id="aggrename-error" style="color: red;"></div> 
                                                </td>
                                                <td>
                                                    <input type="text" id="aggregatorno" name="aggno[]" class="form-control">
                                                    <div id="mobile-error" style="color: red;"></div> 
                                                </td>                                                
                                                <td>
                                                    <a class="btn btn-success" onclick="addAggregatorRow()"><i class="fa fa-plus"></i></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>        
                            </div>
						    <div class="clearfix"></div>
                            <br>

                            <div class="row">
                                <div class="col-md-8">
                                    <p class="bold p_style" style="color: #D5006D;">KSK Details</p>
                                    <hr class="hr_style" style="border-color: #D5006D; border-width: 1.5px;">
                                    <table width="100%">
                                        <thead>
                                            <tr>                                              
                                                <th>Krushi Seva Kendra Name</th>
                                                <th>KSK Shop Owner Name</th>
                                                <th>KSK Shop Owner Number</th>                                               
                                                <th>Add New</th>
                                            </tr>
                                        </thead>
                                        <tbody id="kskdetailstbody">
                                            <tr>                                       
                                                <td style="text-align:center;">
                                                    <input type="text" id="kskname" name="KSKname[]" class="form-control">
                                                    <div id="kskname-error" style="color: red;"></div> 
                                                </td>
                                                <td>
                                                    <input type="text" id="kskshopownername" name="kskshname[]" class="form-control">
                                                    <div id="kskownername-error" style="color: red;"></div>
                                                </td>
                                                <td>
                                                    <input type="text" id="kskshopownerno" name="kskshno[]" class="form-control">
                                                    <div id="kskownerno-error" style="color: red;"></div>
                                                </td>                                            
                                                <td>
                                                    <a class="btn btn-success" onclick="addKsk()"><i class="fa fa-plus"></i></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>        
                            </div>
						    <div class="clearfix"></div>
                            <br> 
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <p class="bold p_style" style="color: #D5006D;">Village Crop Details</p>
                                    <hr class="hr_style" style="border-color: #D5006D; border-width: 1.5px;">
                                    <table width="100%">
                                        <thead>
                                            <tr>                                              
                                                <th>Major Crops</th>
                                                <th>Fertilizer Brands</th>
                                                <th>Seed Brands</th>
                                                <th>Pesticide Brands</th>                                               
                                                <th>Add New</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cropdetailstbody">
                                            <tr>                                                
                                                <td>
                                                    <select name="cropname[]" id="cropname" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">> 
                                                        <option value="">Non Selected</option> 
                                                        <?php
                                                            foreach($crops as $crop) 
                                                            {
                                                                echo '<option value="' . $crop['id'] . '">' . $crop['CropName'] . '</option>';
                                                            } 
                                                        ?>  
                                                    </select> 
                                                    <div id="crop-error" style="color: red;"></div>
                                                </td>
                                                <td>
                                                    <select name="fertilizername[]" id="fertilizername" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true">> 
                                                        <option value="">Non Selected</option> 
                                                        <?php
                                                            foreach($fertilizers as $fer) 
                                                            {
                                                                echo '<option value="' . $fer['id'] . '">' . $fer['fertilizerName'] . '</option>';
                                                            } 
                                                        ?>  
                                                    </select> 
                                                    <div id="fertilizer-error" style="color: red;"></div> 
                                                </td>
                                                <td>
                                                    <select name="seedname[]" id="seedname" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true">> 
                                                        <option value="">Non Selected</option> 
                                                        <?php
                                                            foreach($seeds as $seed) 
                                                            {
                                                                echo '<option value="' . $seed['id'] . '">' . $seed['SeedName'] . '</option>';
                                                            } 
                                                        ?>  
                                                    </select>     
                                                    <div id="seed-error" style="color: red;"></div> 
                                                </td>
                                                <td>
                                                    <select name="pesticidename[]" id="pesticidename" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true">> 
                                                        <option value="">Non Selected</option> 
                                                        <?php
                                                            foreach($pesticides as $pesti) 
                                                            {
                                                                echo '<option value="' . $pesti['id'] . '">' . $pesti['PesticideName'] . '</option>';
                                                            } 
                                                        ?>  
                                                    </select>    
                                                    <div id="pesti-error" style="color: red;"></div> 
                                                </td>                                               
                                                <td>
                                                    <a class="btn btn-success" onclick="addCrop()"><i class="fa fa-plus"></i></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>        
                            </div>
						    <div class="clearfix"></div>
                            <br>   

                            <div class="row">
                                <div class="col-md-8">
                                    <p class="bold p_style" style="color: #D5006D;">Village Vehicle Details</p>
                                    <hr class="hr_style" style="border-color: #D5006D; border-width: 1.5px;">
                                    <table width="100%">
                                        <thead>
                                            <tr>                                              
                                                <th>Vehicle Type</th>
                                                <th>Vehicle Registration No</th>
                                                <th>Vehicle Capacity(Qtls)</th>
                                                <th>Vehicle Driver Name</th>
                                                <th>Driver Mobile No</th>
                                                <th>Owner Name</th>
                                                <th>Owner Mobile No</th>
                                                <th>Add New</th>
                                            </tr>
                                        </thead>
                                        <tbody id="addresstbody">
                                            <tr>                                                
                                                <td>
                                                    <select class="form-control city selectpicker" data-width="100%" data-none-selected-text="Non Selected" name="vehicle_type[]" id="vehicle_type" data-live-search="true">
                                                        <option value="">Non Selected</option>
                                                        <?php
                                                            foreach($vehicletypes as $val) 
                                                            {
                                                                echo '<option value="' . $val['id'] . '">' . $val['VehicleType'] . '</option>';
                                                            } 
                                                        ?>       
                                                    </select>
                                                    <div id="vehicletype-error" style="color: red;"></div>
                                                </td>
                                                <td style="text-align:center;">
                                                    <input type="text" id="regno" name="vehicleregno[]" class="form-control">
                                                    <div id="regno-error" style="color: red;"></div>
                                                </td>
                                                <td>
                                                    <input type="text" id="capacity" name="vehiclecapacity[]" class="form-control">
                                                    <div id="capacity-error" style="color: red;"></div>
                                                </td>
                                                <td>
                                                    <input type="text" id="drivername" name="vehicledrivername[]" class="form-control">
                                                    <div id="drivername-error" style="color: red;"></div>
                                                </td>
                                                <td width="2%" style="text-align: center;">												
                                                    <input type="text" id="driverno" name="vehicledrivermobileno[]" class="form-control">
                                                    <div id="driverno-error" style="color: red;"></div>
                                                </td>
                                                <td>
                                                    <input type="text" id="vehownername" name="vehownernames[]" class="form-control">
                                                    <div id="ownername-error" style="color: red;"></div>
                                                </td>
                                                <td>
                                                    <input type="text" id="vehownerno" name="vehmobileno[]" class="form-control custom-width">
                                                    <div id="ownerno-error" style="color: red;"></div>
                                                </td>
                                                <td>
                                                    <a class="btn btn-success" onclick="addRow()"><i class="fa fa-plus"></i></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>        
                            </div>
						    <div class="clearfix"></div>
                            <br> 
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <p class="bold p_style" style="color: #D5006D;">Village Hotel Details</p>
                                    <hr class="hr_style" style="border-color: #D5006D; border-width: 1.5px;">
                                    <table width="100%">
                                        <thead>
                                            <tr>                                              
                                                <th>Village Hotel Name</th>
                                                <th>Hotel Owner Name</th> 
                                                <th>Owner Mobile No</th> 
                                                <th>Add New</th>
                                            </tr>
                                        </thead>
                                        <tbody id="hoteltbody">
                                            <tr>                                            
                                                <td style="text-align:center;">
                                                    <input type="text" id="hotelname" name="hotname[]" class="form-control">
                                                    <div id="hotel-error" style="color: red;"></div> 
                                                </td>
                                                <td>
                                                    <input type="text" id="ownername" name="owname[]" class="form-control">
                                                    <div id="ow-error" style="color: red;"></div> 
                                                </td> 
                                                <td>
                                                    <input type="text" id="owmobile" name="ownermobileno[]" class="form-control">
                                                    <div id="owmobile-error" style="color: red;"></div> 
                                                </td>   
                                                <td>
                                                    <a class="btn btn-success" onclick="addHotelRow()"><i class="fa fa-plus"></i></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>        
                            </div>
						    <div class="clearfix"></div>
                            <br>

                            <div class="row"> 					
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                                    <button type="submit" class="btn btn-info updateBtn hidden-button" style="margin-right: 25px;">Update</button>                                                                   
                                    <button type="submit" class="btn btn-default cancelBtn" >Cancel</button>
                                </div>
                            </div>                                       
						<div class="clearfix"></div>	
                        
                         <!-- Iteme List Model-->            
                            <div class="modal fade Item_List" id="Item_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                    <div class="modal-header" style="padding:0px 5px !important; max-height: 400px; overflow-y: auto;">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">Village Details</h4>
                                    </div>
                                    <div class="modal-body" style="padding:0px 5px !important; max-height: 400px; overflow-y: auto;">                                            
                                        <div class="table-Item_List tableFixHead2">
                                            <table class="tree table table-striped table-bordered table-Item_List tableFixHead2" id="table_Item_List" width="100%">
                                                <thead>
                                                    <tr style="display:none;">
                                                        <td colspan="8" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                                    </tr>
                                                    <tr>
                                                        <th id="sl" style="text-align:left;">Id</th>
                                                        <th style="text-align:left;">Village Name</th>
                                                        <th style="text-align:left;">Visit Date</th>
                                                        <th style="text-align:left;">Pincode</th>
                                                        <th style="text-align:left;">Taluka</th>
                                                        <th style="text-align:left;">District</th>
                                                        <th style="text-align:left;">State</th>
                                                        <th style="text-align:left;">Village Population</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($table_data as $key => $value) {
                                                    ?>
                                                        <tr class="get_ItemID" data-id="<?php echo $value["id"]; ?>">
                                                            <td><?php echo $value['id'];?></td>
                                                            <td><?php echo $value['VillageName'];?></td>
                                                            <td><?php echo _d(substr($value['VisitDate'],0,10));?></td>
                                                            <td><?php echo $value["Pincode"];?></td>
                                                            <td><?php echo $value["talukaname"];?></td>
                                                            <td><?php echo $value["districtname"];?></td>
                                                            <td><?php echo $value["statename"];?></td>
                                                            <td><?php echo $value["VillagePopulation"];?></td>
                                                        </tr>
                                                    <?php } ?>
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
                            <!-- /.modal-dialog -->
                            </div>
                        <!-- /.modal -->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
    $VillageId = $_POST['id'];       
    echo "<script type='text/javascript'>
    var phpVillageId = '" . addslashes($VillageId) . "';
    </script>";
}
?>

<?php init_tail(); ?>

<script>
    function ResetForm()
    {
        var currentDate = new Date();           
        var day = ("0" + currentDate.getDate()).slice(-2); 
        var month = ("0" + (currentDate.getMonth() + 1)).slice(-2); 
        var year = currentDate.getFullYear();       
        var formattedDate = day + '/' + month + '/' + year;

        $('#visitdate').val(formattedDate);
        $('#villagename').val(''); 
        $('#sarpanch').val(''); 
        $('#population').val(''); 
        $('#area').val(''); 
        $('#influencername').val(''); 
        $('#govtpost').val(''); 
        $('#mobileno').val(''); 
        $('#rtrsno').val(''); 
        $('#info').val(''); 
        $('#pin').val(''); 
        $('#pin').selectpicker('refresh');    
        $('#taluka').val(''); 
        $('#taluka').selectpicker('refresh');   
        $('#district').val(''); 
        $('#district').selectpicker('refresh');   
        $('#state').val(''); 
        $('#state').selectpicker('refresh');    
        
        $("#hotelname").val('');
        $("#ownername").val('');
        $("#owmobile").val('');
        
        $("#aggregatorname").val('');
        $("#aggregatorno").val('');    
        $("#kskname").val('');
        $("#kskshopownername").val('');
        $("#kskshopownerno").val('');      

        $("#cropname").val('');
        $('#cropname').selectpicker('refresh');    
        $("#fertilizername").val('');
        $('#fertilizername').selectpicker('refresh');    
        $("#seedname").val('');
        $('#seedname').selectpicker('refresh');    
        $("#pesticidename").val('');
        $('#pesticidename').selectpicker('refresh');  
        
        $("#vehicle_type").val('');
        $('#vehicle_type').selectpicker('refresh');  
        $("#regno").val('');
        $("#capacity").val('');  
        $("#drivername").val('');  
        $("#driverno").val('');
        $("#vehownername").val('');
        $("#vehownerno").val('');     
        
        $('.saveBtn').show();
		$('.updateBtn').hide();	         		
    }   

    function refreshTable() 
    {
        $.ajax({
            url:  "<?php echo admin_url(); ?>VillageMaster/GetModeltabledata",
            type: "GET", 
            dataType: "json", 
            success: function(data) {             
               
                var tableBody = $("#table_Item_List tbody"); 
                tableBody.empty();                 

                $.each(data, function(index, value) {
                    var newRow = $("<tr class='get_ItemID' data-id='" + value.id + "'>");
                    newRow.append("<td>" + value.id + "</td>");
                    newRow.append("<td>" + value.VillageName + "</td>");
                    newRow.append("<td>" + value.VisitDate + "</td>");
                    newRow.append("<td>" + value.Pincode + "</td>");
                    newRow.append("<td>" + value.Talukaname + "</td>");
                    newRow.append("<td>" + value.Cityname + "</td>");
                    newRow.append("<td>" + value.Statename + "</td>");
                    newRow.append("<td>" + value.VillagePopulation + "</td>");
                    tableBody.append(newRow); 
                });
            },
            error: function(xhr, status, error) {
                console.error("Error occurred while fetching data: " + error);
            }
        });
    }
</script>

<script>
    function updateDropdowns(pincodeId) {
        if (pincodeId) {
            $.ajax({
                url: "<?php echo admin_url(); ?>VillageMaster/GetDetailsFromPincode",
                dataType: "JSON",
                method: "POST",
                data: { pincodeId: pincodeId },
                beforeSend: function () {
                    $('.searchh2').css('display', 'block');
                    $('.searchh2').css('color', 'blue');
                },
                complete: function () {
                    $('.searchh2').css('display', 'none');
                },
                success: function (data) {                 
                    var PincodeDetails = data.pincodeDetails;  
                    var stateName = data.Statename;
                    var cityName = data.Cityname;
                    var talukaName = data.Talukaname;                  
                    
                    $("#state").val(stateName.state_name);
                    $("#district").val(cityName.city_name);
                    $("#taluka").val(talukaName.TalukaName);

                    $("#state_id").val(stateName.short_name);
                    $("#district_id").val(cityName.id);
                    $("#taluka_id").val(talukaName.id);
                }
            });
        } else {
            // Clear dropdowns if no pincode is selected
            $('#state').val('');
            $('#state').selectpicker('refresh');
            $('#district').val('');
            $('#district').selectpicker('refresh');
            $('#taluka').val('');
            $('#taluka').selectpicker('refresh');
        }
    }

    $('#pin').change(function() 
    {
        var pincodeId = $(this).val();            
        updateDropdowns(pincodeId);             
    });   

    $(document).ready(function() 
    {   
        var pincodeId = $('#pin').val();  
        updateDropdowns(pincodeId);               

        $("#villagename").dblclick(function(){
            $('#Item_List').modal('show');
            $('#Item_List').on('shown.bs.modal', function () {
                $('#myInput1').focus();
            })
        });      

        //save village details
        $('.saveBtn').on('click',function() 
        {
            visitdate= $('#visitdate').val(); 
            villagename = $('#villagename').val(); 
            pin = $('#pin').val(); 
            taluka = $('#taluka_id').val();
            district = $('#district_id').val();
            state = $('#state_id').val();
            villagesarpanch = $('#sarpanch').val();
            villagepopulation = $('#population').val();
            villagearea = $('#area').val();
            villageinfluencername = $('#influencername').val();  
            govtpost = $('#govtpost').val();      
            mobileno = $('#mobileno').val();
            rtrsno = $('#rtrsno').val();  
            otherinfo = $('#info').val();
            
            var hotelName = $("#hotelname").val();
            var OwnerName = $("#ownername").val();
            var ownerMobile = $("#owmobile").val();
            
            var aggregatorName = $("#aggregatorname").val();
            var aggregatorNumber = $("#aggregatorno").val();     
            
            var kskname = $("#kskname").val();
            var kskshopownername = $("#kskshopownername").val();
            var kskshopownerno = $("#kskshopownerno").val();

            var cropname = $("#cropname").val();
            var fertilizername = $("#fertilizername").val();
            var seedname = $("#seedname").val();
            var pesticidename = $("#pesticidename").val();    
            
            var vehicle_type = $("#vehicle_type").val();
            var regno = $("#regno").val();
            var capacity = $("#capacity").val();
            var drivername = $("#drivername").val();  
            var driverno = $("#driverno").val();
            var vehownername = $("#vehownername").val();
            var vehownerno = $("#vehownerno").val();
            
            //Hotel Details 
            var HotelDetails = [];
            $('tr.addedtr').each(function() {
                var Hotname = $(this).find('input[name="hotelnames[]"]').val();
                var hotownerName = $(this).find('input[name="ownernames[]"]').val();
                var hotownerMobile =  $(this).find('input[name="ownermobiles[]"]').val();
                HotelDetails.push({
                    Hotname: Hotname,
                    hotownerName: hotownerName,
                    hotownerMobile:hotownerMobile
                });
            });

            //aggregator details
            var aggregatorDetails = [];

            $('tr.addedtr').each(function() {
                var name = $(this).find('input[name="aggregator_name[]"]').val();
                var no = $(this).find('input[name="aggregator_no[]"]').val();

                aggregatorDetails.push({
                    name: name,
                    no: no
                });
            });                                      

            //Ksk details
            var kskDetails = [];

            $('tr.addedtr').each(function() {
                var kskName = $(this).find('input[name="ksk_name[]"]').val();
                var kskownershopname = $(this).find('input[name="ksk_owner_name[]"]').val();
                var kskshopownerNo = $(this).find('input[name="ksk_owner_no[]"]').val();

                kskDetails.push({
                    kskName: kskName,
                    kskownershopname: kskownershopname,
                    kskshopownerNo: kskshopownerNo
                });
            });  

            var cropDetails = [];

            $('tr.addedtr').each(function(index) {
                var cropname = $(this).find('select[name="majorcrop[]"]').val();  // Get the selected crop IDs
                var fertilizername = $(this).find('select[name="fertilizername[]"]').val();  // Get the selected fertilizer IDs
                var seedname = $(this).find('select[name="seedname[]"]').val();  // Get the selected seed IDs
                var pesticidename = $(this).find('select[name="pesticidename[]"]').val();

                cropDetails.push({
                    index: index, 
                    cropname: cropname,
                    fertilizername: fertilizername,
                    seedname: seedname,
                    pesticidename:pesticidename
                });
            });     
            
            //village vehicle details
            var vehicleDetails = [];

            $('tr.addedtr').each(function() {
                var vehicleType = $(this).find('input[name="vehicle_type[]"]').val();
                var RegNo = $(this).find('input[name="regsistration_no[]"]').val();
                var vehicleCapacity = $(this).find('input[name="vehicle_capacity[]"]').val();
                var Drivername = $(this).find('input[name="vehicle_drivername[]"]').val();
                var driverNo = $(this).find('input[name="vehicle_driverno[]"]').val();
                var ownerName = $(this).find('input[name="vehicle_ownername[]"]').val();
                var ownerNo = $(this).find('input[name="vehicleowner_no[]"]').val();

                vehicleDetails.push({
                    vehicleType: vehicleType,
                    RegNo: RegNo,
                    vehicleCapacity: vehicleCapacity,
                    Drivername:Drivername,
                    driverNo:driverNo,
                    ownerName:ownerName,
                    ownerNo:ownerNo
                });
            });            

            if (!villagename) {
                $('#VillageIDError').text('Please Enter Village Name').show();
                $('#villagename').focus();
                setTimeout(() => { $('#VillageIDError').hide(); }, 2000);
                return;
            }
            else if(!pin){            
                $('#PinIDError').text('Please select Pincode').show();
                $('#pin').focus();
                setTimeout(() => { $('#PinIDError').hide(); }, 2000);
                return;
            }
            else if(!state){
                $('#StateIDError').text('Please select State').show();
                $('#state').focus();
                setTimeout(() => { $('#StateIDError').hide(); }, 2000);
                return;  
            }
            else if(!district){
                $('#DistIDError').text('Please select District').show();
                $('#district').focus();
                setTimeout(() => { $('#DistIDError').hide(); }, 2000);
                return;
            }
            else if(!taluka){
                $('#TalukaIDError').text('Please select Taluka').show();
                $('#taluka').focus();
                setTimeout(() => { $('#TalukaIDError').hide(); }, 2000);
                return;   
            }
            else if(!villagesarpanch){
                $('#SarpanchError').text('Enter village sarpanch name').show();
                $('#sarpanch').focus();
                setTimeout(() => { $('#SarpanchError').hide(); }, 2000);
                return;   
            }
            else if(villagepopulation !== "" && !/^\d+$/.test(villagepopulation)){
                alert('Enter a valid positive integer for the village population.');
                $('#population').focus();
                return;
            }
            else if(villagearea !== "" && !/^\d+(\.\d+)?$/.test(villagearea)){
                alert('Enter a valid area value (positive integer or float).');
                $('#area').focus();
                return;
            }
            else if(mobileno !== "" && !mobileno.match(/^[0-9]{10}$/)) {
                alert('Enter a valid 10-digit mobile number.');
                $('#mobileno').focus();
                return; 
            }
            else if(rtrsno !== "" && !/^\d+$/.test(rtrsno)){
                alert('Enter a valid no of rtrs farmers.');
                $('#rtrsno').focus();
                return;
            } 
            else if(hotelName === '' && OwnerName !== '')
            {
                alert('Enter hotel name');  
            }
            else if(OwnerName === '' && hotelName !== '')
            {
                alert('Enter hotel owner name');  
            }
            else if (hotelName !== '' && OwnerName !== '' && (ownerMobile.length !== 10 || !/^[0-9]{10}$/.test(ownerMobile))) 
            {              
                alert('Enter a valid mobile number');            
            }
            else if (aggregatorName === '' && aggregatorNumber !== '') 
            {                 
                alert('Enter aggregator name');                
            }else if (aggregatorName !== '' && aggregatorNumber === '') 
            {             
                alert('Enter aggregator number');              
            }else if (aggregatorName !== '' && aggregatorNumber !== '' && (aggregatorNumber.length !== 10 || !/^[0-9]{10}$/.test(aggregatorNumber))) 
            {              
                alert('Enter a valid mobile number');            
            }      
            else if(kskname !== '' && kskshopownername === '') 
            {
                alert('Enter ksk shop owner name.');      
            } 
            else if(kskshopownername !== '' && kskname == '')  
            {
                alert('Enter ksk name.');  
            }    
            else if(kskshopownername !== '' && kskshopownerno ==='')
            {
                alert('Enter ksk shop owner number.');   
            }            
            else if (kskname !== '' && kskshopownername !== '' && kskshopownerno !=='' && (kskshopownerno.length !== 10 || !/^[0-9]{10}$/.test(kskshopownerno))) 
            {              
                alert('Enter a valid mobile number');            
            } 
            else if(cropname !== '' && fertilizername ==='')
            {
                alert('Select fertilizer name.');   
            }
            // else if(fertilizername !=='' && cropname === '')
            // {
            //     alert('Select crop name.');   
            // }
            else if(fertilizername !=='' && seedname ==='')
            {
                alert('Select seed name.');   
            }
            else if(seedname !=='' && pesticidename ==='')
            {
                alert('Select pesticide name.');   
            }
            else if(vehicle_type !=='' && regno ==='')
            {   alert('Enter vehicle registration number.');  }
            else if(regno !=='' && vehicle_type ==='')
            {  alert('Select vehicle type'); }
            else if(regno !=='' && capacity ==='')
            {  alert('Enter vehicle capacity'); }
            else if(capacity !=='' && (capacity !=="" && !/^\d+$/.test(capacity)))
            { alert('Enter valid vehicle capacity'); }
            else if(capacity !=='' && drivername ==='')
            { alert('Enter vehicle driver name'); }
            else if(drivername !=='' && driverno ==='')
            { alert('Enter vehicle driver number'); }
            else if(driverno !=='' && (driverno.length !== 10 || !/^[0-9]{10}$/.test(driverno)))
            {  alert('Enter a valid mobile number'); }
            else if(driverno !=='' && vehownername ==='')
            { alert('Enter vehicle owner name');  }
            else if(vehownername !=='' && vehownerno ==='')
            { alert('Enter vehicle owner number');}
            else if(vehownerno !=='' && (vehownerno.length !== 10 || !/^[0-9]{10}$/.test(vehownerno)))
            {  alert('Enter a valid mobile number'); }
            else
            {                                   
                $.ajax({
                    url: "<?php echo admin_url(); ?>VillageMaster/addVillageDetails", 
                    type: 'POST', 
                    data: {visitdate:visitdate,villagename:villagename,pin:pin,taluka:taluka,district:district,state:state,
                            villagesarpanch:villagesarpanch,villagepopulation:villagepopulation,villagearea:villagearea,villageinfluencername:villageinfluencername,
                            govtpost:govtpost,mobileno:mobileno,rtrsno:rtrsno,otherinfo:otherinfo,HotelDetails:HotelDetails,aggregatorDetails:aggregatorDetails,kskDetails:kskDetails,cropDetails:cropDetails,vehicleDetails:vehicleDetails,
                            aggregatorName:aggregatorName,aggregatorNumber:aggregatorNumber,kskname:kskname,kskshopownername:kskshopownername,kskshopownerno:kskshopownerno,cropname:cropname,fertilizername:fertilizername,seedname:seedname,pesticidename:pesticidename,
                            vehicle_type:vehicle_type,regno:regno,capacity:capacity,drivername:drivername,driverno:driverno,vehownername:vehownername,vehownerno:vehownerno,hotelName:hotelName,OwnerName:OwnerName,ownerMobile:ownerMobile}, 
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {                   
                            alert_float('success', 'Record Created Successfully...');
                            refreshTable();             
                            $('tr.addedtr').remove();     
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

        //cancel 
        $('.cancelBtn').on('click',function() 
        {
            ResetForm();   
            $('tr.addedtr').remove();                    
        });

        //open on click of report
        var VillageId = phpVillageId;      
      
        $.ajax({
            url:"<?php echo admin_url(); ?>VillageMaster/GetVillageDetailByID",
            dataType:"JSON",
            method:"POST",
            data:{VillageId:VillageId},
            beforeSend: function () {
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
            },
            complete: function () {
                $('.searchh2').css('display','none');
            },
            success:function(data)
            {	
                var Villageinfo = data.villageDetails;
                var Aggregatorinfo = data.villageAggregatorDetails;                
                var Kskinfo = data.kskDetails;
                var Cropinfo = data.cropdetails;
                var Vehicleinfo = data.vehicleDetails;
                var Crops = data.crops;        
                var Fertilizers = data.fertilizers;    
                var Seeds = data.seeds;    
                var Pesticides = data.pesticides;

                var States = data.Statename;
                var Cities = data.Cityname;
                var Taluka = data.Talukaname;

                var pincodeId = Villageinfo.Pincode;           
                updateDropdowns(pincodeId);  

                var date = new Date(Villageinfo.VisitDate.split(' ')[0]);                
                var day = ("0" + date.getDate()).slice(-2); 
                var month = ("0" + (date.getMonth() + 1)).slice(-2); 
                var year = date.getFullYear();                
                var formattedDate = day + '/' + month + '/' + year;

                $('#visitdate').val(formattedDate);
                $('.selectpicker').selectpicker('refresh')

                $('#villagename').val(Villageinfo.VillageName);

                $('#pin').val(Villageinfo.Pincode);
                $('.selectpicker').selectpicker('refresh')

                $('#state').val(States.state_name);		              
                $("#district").val(Cities.city_name);
                $("#taluka").val(Taluka.TalukaName);

                $('#sarpanch').val(Villageinfo.VillageSarpanch);
                $('#population').val(Villageinfo.VillagePopulation);
                $('#area').val(Villageinfo.Area);
                $('#influencername').val(Villageinfo.InfluencerName);
                $('#govtpost').val(Villageinfo.InfluencerGovtPost);
                $('#mobileno').val(Villageinfo.Influencer_MobNo);
                $('#rtrsno').val(Villageinfo.NoRtrsFarmers);
                $('#info').val(Villageinfo.OtherInformation);

                $("#aggregatortbody").find('tr').remove();
                $("#aggregatortbody").append(`
                        <tr>
                            <td style="text-align:center;">
                                <input type="text" id="aggregatorname" name="aggname[]" class="form-control">
                                <div id="aggrename-error" style="color: red;"></div> 
                            </td>
                            <td>
                                <input type="text" id="aggregatorno" name="aggno[]" class="form-control">
                                <div id="mobile-error" style="color: red;"></div> 
                            </td>
                            <td>
                                <a class="btn btn-success" onclick="addAggregatorRow()"><i class="fa fa-plus"></i></a>
                            </td>
                        </tr>
                    `);
                
                $.each(Aggregatorinfo, function(index, aggregator) 
                {                   
                    var newRow = $("<tr class='addedtr'></tr>");                
                    newRow.append("<td><input type='text' name='aggregator_name[]' class='form-control' value='" + aggregator.VillageAggregatorName + "'></td>");
                    newRow.append("<td><input type='text' name='aggregator_no[]' class='form-control' value='" + aggregator.AggregatorMobNo + "'></td>");
                    newRow.append("<td><a href='#' class='btn btn-danger removeaggregatorbtn'><i class='fa fa-times'></i></a></td>");               
                    
                    $("#aggregatortbody").append(newRow);
                });
                
                $("#kskdetailstbody").find('tr').remove();
                $("#kskdetailstbody").append(`
                    <tr>
                        <td style="text-align:center;">
                            <input type="text" id="kskname" name="KSKname[]" class="form-control">
                            <div id="kskname-error" style="color: red;"></div> 
                        </td>
                        <td>
                            <input type="text" id="kskshopownername" name="kskshname[]" class="form-control">
                            <div id="kskownername-error" style="color: red;"></div>
                        </td>
                        <td>
                            <input type="text" id="kskshopownerno" name="kskshno[]" class="form-control">
                            <div id="kskownerno-error" style="color: red;"></div>
                        </td>
                        <td>
                            <a class="btn btn-success" onclick="addKsk()"><i class="fa fa-plus"></i></a>
                        </td>
                    </tr>
                `);                
                $.each(Kskinfo, function(index, ksk) 
                {
                    var newRow = $("<tr class='addedtr'></tr>");
                    // Append columns to the new row      
                    newRow.append("<td><input type='text' name='ksk_name[]' class='form-control' value='" + ksk.KskName + "'></td>");
                    newRow.append("<td><input type='text' name='ksk_owner_name[]' class='form-control' value='" + ksk.KskShopOwnerName + "'></td>");
                    newRow.append("<td><input type='text' name='ksk_owner_no[]' class='form-control' value='" + ksk.KskShopOwnerNo + "'></td>");       
                    newRow.append("<td><a href='#' class='btn btn-danger kskremovebtn'><i class='fa fa-times'></i></a></td>");
                
                    $("#kskdetailstbody").append(newRow);
                });
                
                $("#cropdetailstbody").find('tr').remove();
                $("#cropdetailstbody").append(`
                    <tr>
                        <td>
                            <select name="cropname[]" id="cropname" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                <option value="">Non Selected</option> 
                                <?php
                                    foreach($crops as $crop) {
                                        echo '<option value="' . $crop['id'] . '">' . $crop['CropName'] . '</option>';
                                    }
                                ?>  
                            </select>
                            <div id="crop-error" style="color: red;"></div>
                        </td>   
                        <td>
                            <select name="fertilizername[]" id="fertilizername" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true">
                                <option value="">Non Selected</option> 
                                <!-- PHP generated options for fertilizers -->
                                <?php
                                    foreach($fertilizers as $fer) {
                                        echo '<option value="' . $fer['id'] . '">' . $fer['fertilizerName'] . '</option>';
                                    }
                                ?>  
                            </select>
                            <div id="fertilizer-error" style="color: red;"></div> 
                        </td> 
                        <td>
                            <select name="seedname[]" id="seedname" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true">
                                <option value="">Non Selected</option> 
                                <!-- PHP generated options for seeds -->
                                <?php
                                    foreach($seeds as $seed) {
                                        echo '<option value="' . $seed['id'] . '">' . $seed['SeedName'] . '</option>';
                                    }
                                ?>  
                            </select>   
                            <div id="seed-error" style="color: red;"></div>     
                        </td>  
                        <td>
                            <select name="pesticidename[]" id="pesticidename" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true">
                                <option value="">Non Selected</option> 
                                <!-- PHP generated options for pesticides -->
                                <?php
                                    foreach($pesticides as $pesti) {
                                        echo '<option value="' . $pesti['id'] . '">' . $pesti['PesticideName'] . '</option>';
                                    }
                                ?>  
                            </select>   
                            <div id="pesti-error" style="color: red;"></div>  
                        </td>              
                        <td>
                            <a class="btn btn-success" onclick="addCrop()"><i class="fa fa-plus"></i></a>
                        </td>
                    </tr>
                `);
                $('.selectpicker').selectpicker();
                $.each(Cropinfo, function(index, crop) 
                {
                    var fertilizerIds = crop.FertilizerId.split(',');                   
                    var fertilizerNames = crop.fertilizername.split(','); 
                    var fertilizerDisplay = fertilizerNames.join(", ");

                    var cropIds = crop.CropId.split(',');     
                    var cropNames = crop.cropname.split(','); 
                    var cropDisplay = cropNames.join(", ");

                    var seedIds = crop.SeedId.split(','); 
                    var seedNames = crop.SeedName.split(','); 
                    var seedDisplay = seedNames.join(", "); 

                    var pesticideIds = crop.PesticideId.split(','); 
                    var pesticideNames = crop.PesticideName.split(','); 
                    var pesticideDisplay = pesticideNames.join(", "); 

                    var newRow = $("<tr class='addedtr'></tr>");
                    //Append columns to the new row
                    //newRow.append("<td><input type='hidden' name='majorcrop[]' value='" + crop.CropId + "'>" + cropDisplay + "</td>");  
                    
                    var cropsIds = crop.CropId.split(',');                           
                    var dropdownHTML = "<td><select name='majorcrop[]' class='selectpicker form-control' data-live-search='true'>" +
                    "<option value=''>Non Selected</option>"; 
                    Crops.forEach(function(crops) {                      

                        var selected = cropsIds.includes(crops.id.toString()) ? "selected" : "";                   
                        dropdownHTML += "<option value='" + crops.id + "' " + selected + ">" + crops.CropName + "</option>";                        
                    });
                    dropdownHTML += "</select></td>";
                    newRow.append(dropdownHTML);
                    $('.selectpicker').selectpicker('refresh');

                    //newRow.append("<td><input type='hidden' name='fertilizername[]' value='" + crop.FertilizerId + "'>" + fertilizerDisplay + "</td>");        
                    
                    var fersIds = crop.FertilizerId.split(',');    
                    var dropdownHTMLfer = "<td><select name='fertilizername[]' class='selectpicker form-control' data-live-search='true' multiple>" +
                    "<option value=''>Non Selected</option>"; 
                    Fertilizers.forEach(function(fertilizers) {                      

                    var selected = fersIds.includes(fertilizers.id.toString()) ? "selected" : "";                   
                    dropdownHTMLfer += "<option value='" + fertilizers.id + "' " + selected + ">" + fertilizers.fertilizerName + "</option>";                        
                    });   
                    dropdownHTMLfer += "</select></td>";
                    newRow.append(dropdownHTMLfer);
                    $('.selectpicker').selectpicker('refresh');
                    
                    //newRow.append("<td><input type='hidden' name='seedname[]' value='" + crop.SeedId + "'>" + seedDisplay + "</td>");        
                    
                    var seedsIds = crop.SeedId.split(',');    
                    var dropdownHTMLseed = "<td><select name='seedname[]' class='selectpicker form-control' data-live-search='true' multiple>" +
                    "<option value=''>Non Selected</option>"; 
                    Seeds.forEach(function(seed) {                      

                    var selected = seedsIds.includes(seed.id.toString()) ? "selected" : "";                   
                    dropdownHTMLseed += "<option value='" + seed.id + "' " + selected + ">" + seed.SeedName + "</option>";                        
                    });  
                    dropdownHTMLseed += "</select></td>";
                    newRow.append(dropdownHTMLseed);
                    $('.selectpicker').selectpicker('refresh'); 

                    //newRow.append("<td><input type='hidden' name='pesticidename[]' value='" + crop.PesticideId + "'>" + pesticideDisplay + "</td>");          
                    var pesticidesIds = crop.PesticideId.split(',');   
                    var dropdownHTMLpesticide = "<td><select name='pesticidename[]' class='selectpicker form-control' data-live-search='true' multiple>" +
                    "<option value=''>Non Selected</option>";  
                    Pesticides.forEach(function(pesticide) {                      

                    var selected = pesticidesIds.includes(pesticide.id.toString()) ? "selected" : "";                   
                    dropdownHTMLpesticide += "<option value='" + pesticide.id + "' " + selected + ">" + pesticide.PesticideName + "</option>";                        
                    });  
                    dropdownHTMLpesticide += "</select></td>";
                    newRow.append(dropdownHTMLpesticide);
                    $('.selectpicker').selectpicker('refresh'); 

                    newRow.append("<td><a href='#' class='btn btn-danger cropremovebtn'><i class='fa fa-times'></i></a></td>");
                    
                    // Append the new row to the table body
                    $("#cropdetailstbody").append(newRow);                                                     
                });

                $("#addresstbody").find('tr').remove();  
                $("#addresstbody").append(`
                    <tr>
                        <td>
                            <select class="form-control city selectpicker" data-width="100%" data-none-selected-text="Non Selected" name="vehicle_type[]" id="vehicle_type" data-live-search="true">
                                <option value="">Non Selected</option>
                                <option value="Truck">Truck</option>
                                <option value="Tractor">Tractor</option>
                                <option value="Pickup Man">Pickup Man</option>
                            </select>
                            <div id="vehicletype-error" style="color: red;"></div>
                        </td>
                        <td style="text-align:center;">
                            <input type="text" id="regno" name="vehicleregno[]" class="form-control">
                            <div id="regno-error" style="color: red;"></div>
                        </td>
                        <td>
                            <input type="text" id="capacity" name="vehiclecapacity[]" class="form-control">
                            <div id="capacity-error" style="color: red;"></div>
                        </td>
                        <td>
                            <input type="text" id="drivername" name="vehicledrivername[]" class="form-control">
                            <div id="drivername-error" style="color: red;"></div>
                        </td>
                        <td width="2%" style="text-align: center;">
                            <input type="text" id="driverno" name="vehicledrivermobileno[]" class="form-control">
                            <div id="driverno-error" style="color: red;"></div>
                        </td>
                        <td>
                            <input type="text" id="vehownername" name="vehownernames[]" class="form-control">
                            <div id="ownername-error" style="color: red;"></div>
                        </td>
                        <td>
                            <input type="text" id="vehownerno" name="vehmobileno[]" class="form-control custom-width">
                            <div id="ownerno-error" style="color: red;"></div>
                        </td>
                        <td>
                            <a class="btn btn-success" onclick="addRow()"><i class="fa fa-plus"></i></a>
                        </td>
                    </tr>
                `);   
                $('.selectpicker').selectpicker();             
                $.each(Vehicleinfo, function(index, vehicle) 
                {
                    var newRow = $("<tr class='addedtr'></tr>");
                    // Append columns to the new row
                    newRow.append("<td><input type='hidden' name='vehicle_type[]' value='" + vehicle.VehicleType + "'>" + vehicle.VehicleType + "</td>");        
                    newRow.append("<td><input type='text' name='regsistration_no[]' class='form-control' value='" + vehicle.RegsiterNo + "'></td>");
                    newRow.append("<td><input type='text' name='vehicle_capacity[]' class='form-control' value='" + vehicle.capacity + "'></td>");
                    newRow.append("<td><input type='text' name='vehicle_drivername[]' class='form-control' value='" + vehicle.DriverName + "'></td>");
                    newRow.append("<td><input type='text' name='vehicle_driverno[]' class='form-control' value='" + vehicle.MobileNo + "'></td>");
                    newRow.append("<td><input type='text' name='vehicle_ownername[]' class='form-control' value='" + vehicle.OwnerName + "'></td>");
                    newRow.append("<td><input type='text' name='vehicleowner_no[]' class='form-control' value='" + vehicle.OwnerMobNo + "'></td>");
                    newRow.append("<td><a href='#' class='btn btn-danger removebtn'><i class='fa fa-times'></i></a></td>");

                    // Append the new row to the table body
                    $("#addresstbody").append(newRow);
                });

                $('.saveBtn').hide();
                $('.updateBtn').show();	         		
            }
        });
        $('#Item_List').modal('hide');   
        
        //update village details
        $('.updateBtn').on('click',function() 
        {              
            visitdate= $('#visitdate').val(); 
            villagename = $('#villagename').val(); 
            pin = $('#pin').val();           
            taluka = $('#taluka_id').val();
            district = $('#district_id').val();
            state = $('#state_id').val();
            villagesarpanch = $('#sarpanch').val();
            villagepopulation = $('#population').val();
            villagearea = $('#area').val();
            villageinfluencername = $('#influencername').val();  
            govtpost = $('#govtpost').val();      
            mobileno = $('#mobileno').val();
            rtrsno = $('#rtrsno').val();  
            otherinfo = $('#info').val();  
            
            var hotelName = $("#hotelname").val();
            var OwnerName = $("#ownername").val();
            var ownerMobile = $("#owmobile").val();

            var aggregatorName = $("#aggregatorname").val();
            var aggregatorNumber = $("#aggregatorno").val();  
            
            var kskname = $("#kskname").val();
            var kskshopownername = $("#kskshopownername").val();
            var kskshopownerno = $("#kskshopownerno").val();

            var cropname = $("#cropname").val();
            var fertilizername = $("#fertilizername").val();
            var seedname = $("#seedname").val();
            var pesticidename = $("#pesticidename").val();   

            var vehicle_type = $("#vehicle_type").val();
            var regno = $("#regno").val();
            var capacity = $("#capacity").val();
            var drivername = $("#drivername").val();  
            var driverno = $("#driverno").val();
            var vehownername = $("#vehownername").val();
            var vehownerno = $("#vehownerno").val();
            
            //Hotel Details 
            var HotelDetails = [];
            $('tr.addedtr').each(function() {
                var Hotname = $(this).find('input[name="hotelnames[]"]').val();
                var hotownerName = $(this).find('input[name="ownernames[]"]').val();
                var hotownerMobile =  $(this).find('input[name="ownermobiles[]"]').val();
                HotelDetails.push({
                    Hotname: Hotname,
                    hotownerName: hotownerName,
                    hotownerMobile:hotownerMobile
                });
            });  

            //aggregator details
            var aggregatorDetails = [];
            $('tr.addedtr').each(function() {
                var name = $(this).find('input[name="aggregator_name[]"]').val();
                var no = $(this).find('input[name="aggregator_no[]"]').val();

                aggregatorDetails.push({
                    name: name,
                    no: no
                });
            });    
                
            //Ksk details
            var kskDetails = [];
            $('tr.addedtr').each(function() {
                var kskName = $(this).find('input[name="ksk_name[]"]').val();
                var kskownershopname = $(this).find('input[name="ksk_owner_name[]"]').val();
                var kskshopownerNo = $(this).find('input[name="ksk_owner_no[]"]').val();

                kskDetails.push({
                    kskName: kskName,
                    kskownershopname: kskownershopname,
                    kskshopownerNo: kskshopownerNo
                });
            });  

            //crop details
            var cropDetails = [];

            $('tr.addedtr').each(function(index) {
                var cropname = $(this).find('select[name="majorcrop[]"]').val();  // Get the selected crop IDs
                var fertilizername = $(this).find('select[name="fertilizername[]"]').val();  // Get the selected fertilizer IDs
                var seedname = $(this).find('select[name="seedname[]"]').val();  // Get the selected seed IDs
                var pesticidename = $(this).find('select[name="pesticidename[]"]').val();

                cropDetails.push({
                    index: index, 
                    cropname: cropname,
                    fertilizername: fertilizername,
                    seedname: seedname,
                    pesticidename:pesticidename
                });
            });             

            //village vehicle details
            var vehicleDetails = [];

            $('tr.addedtr').each(function() {
                var vehicleType = $(this).find('input[name="vehicle_type[]"]').val();
                var RegNo = $(this).find('input[name="regsistration_no[]"]').val();
                var vehicleCapacity = $(this).find('input[name="vehicle_capacity[]"]').val();
                var Drivername = $(this).find('input[name="vehicle_drivername[]"]').val();
                var driverNo = $(this).find('input[name="vehicle_driverno[]"]').val();
                var ownerName = $(this).find('input[name="vehicle_ownername[]"]').val();
                var ownerNo = $(this).find('input[name="vehicleowner_no[]"]').val();

                vehicleDetails.push({
                    vehicleType: vehicleType,
                    RegNo: RegNo,
                    vehicleCapacity: vehicleCapacity,
                    Drivername:Drivername,
                    driverNo:driverNo,
                    ownerName:ownerName,
                    ownerNo:ownerNo
                });
            });           

            if (!villagename) {
                $('#VillageIDError').text('Please Enter Village Name').show();
                $('#villagename').focus();
                setTimeout(() => { $('#VillageIDError').hide(); }, 2000);
                return;
            }
            else if(!pin){            
                $('#PinIDError').text('Please select Pincode').show();
                $('#pin').focus();
                setTimeout(() => { $('#PinIDError').hide(); }, 2000);
                return;
            }
            else if(!state){
                $('#StateIDError').text('Please select State').show();
                $('#state').focus();
                setTimeout(() => { $('#StateIDError').hide(); }, 2000);
                return;  
            }
            else if(!district){
                $('#DistIDError').text('Please select District').show();
                $('#district').focus();
                setTimeout(() => { $('#DistIDError').hide(); }, 2000);
                return;
            }
            else if(!taluka){
                $('#TalukaIDError').text('Please select Taluka').show();
                $('#taluka').focus();
                setTimeout(() => { $('#TalukaIDError').hide(); }, 2000);
                return;   
            }
            else if(!villagesarpanch){
                $('#SarpanchError').text('Enter village sarpanch name').show();
                $('#sarpanch').focus();
                setTimeout(() => { $('#SarpanchError').hide(); }, 2000);
                return;   
            }
            else if(villagepopulation !== "" && !/^\d+$/.test(villagepopulation)){
                alert('Enter a valid positive integer for the village population.');
                $('#population').focus();
                return;
            }
            else if(villagearea !== "" && !/^\d+(\.\d+)?$/.test(villagearea)){
                alert('Enter a valid area value (positive integer or float).');
                $('#area').focus();
                return;
            }
            else if(mobileno !== "" && !mobileno.match(/^[0-9]{10}$/)) {
                alert('Enter a valid 10-digit mobile number.');
                $('#mobileno').focus();
                return; 
            }
            else if(rtrsno !== "" && !/^\d+$/.test(rtrsno)){
                alert('Enter a valid no of rtrs farmers.');
                $('#rtrsno').focus();
                return;
            } 
            else if(hotelName === '' && OwnerName !== '')
            {
                alert('Enter hotel name');  
            }
            else if(OwnerName === '' && hotelName !== '')
            {
                alert('Enter hotel owner name');  
            }
            else if (hotelName !== '' && OwnerName !== '' && (ownerMobile.length !== 10 || !/^[0-9]{10}$/.test(ownerMobile))) 
            {              
                alert('Enter a valid mobile number');            
            }
            else if (aggregatorName === '' && aggregatorNumber !== '') 
            {                 
                alert('Enter aggregator name');                
            }else if (aggregatorName !== '' && aggregatorNumber === '') 
            {             
                alert('Enter aggregator number');              
            }else if (aggregatorName !== '' && aggregatorNumber !== '' && (aggregatorNumber.length !== 10 || !/^[0-9]{10}$/.test(aggregatorNumber))) 
            {              
                alert('Enter a valid mobile number');            
            }else if(kskname !== '' && kskshopownername === '') 
            {
                alert('Enter ksk shop owner name.');      
            } 
            else if(kskshopownername !== '' && kskname == '')  
            {
                alert('Enter ksk name.');  
            }    
            else if(kskshopownername !== '' && kskshopownerno ==='')
            {
                alert('Enter ksk shop owner number.');   
            }            
            else if (kskname !== '' && kskshopownername !== '' && kskshopownerno !=='' && (kskshopownerno.length !== 10 || !/^[0-9]{10}$/.test(kskshopownerno))) 
            {              
                alert('Enter a valid mobile number');            
            }else if(cropname !== '' && fertilizername ==='')
            {
                alert('Select fertilizer name.');   
            }
            // else if(fertilizername !=='' && cropname === '')
            // {
            //     alert('Select crop name.');   
            // }
            else if(fertilizername !=='' && seedname ==='')
            {
                alert('Select seed name.');   
            }
            else if(seedname !=='' && pesticidename ==='')
            {
                alert('Select pesticide name.');   
            }
            else if(vehicle_type !=='' && regno ==='')
            {   alert('Enter vehicle registration number.');  }
            else if(regno !=='' && vehicle_type ==='')
            {  alert('Select vehicle type'); }
            else if(regno !=='' && capacity ==='')
            {  alert('Enter vehicle capacity'); }
            else if(capacity !=='' && (capacity !=="" && !/^\d+$/.test(capacity)))
            { alert('Enter valid vehicle capacity'); }
            else if(capacity !=='' && drivername ==='')
            { alert('Enter vehicle driver name'); }
            else if(drivername !=='' && driverno ==='')
            { alert('Enter vehicle driver number'); }
            else if(driverno !=='' && (driverno.length !== 10 || !/^[0-9]{10}$/.test(driverno)))
            {  alert('Enter a valid mobile number'); }
            else if(driverno !=='' && vehownername ==='')
            { alert('Enter vehicle owner name');  }
            else if(vehownername !=='' && vehownerno ==='')
            { alert('Enter vehicle owner number');}
            else if(vehownerno !=='' && (vehownerno.length !== 10 || !/^[0-9]{10}$/.test(vehownerno)))
            {  alert('Enter a valid mobile number'); }
            else
            {
                $.ajax({
                    url: "<?php echo admin_url(); ?>VillageMaster/UpdateVillageDetails", 
                    type: 'POST', 
                    data: {VillageId:VillageId,visitdate:visitdate,villagename:villagename,pin:pin,taluka:taluka,district:district,state:state,
                            villagesarpanch:villagesarpanch,villagepopulation:villagepopulation,villagearea:villagearea,villageinfluencername:villageinfluencername,
                            govtpost:govtpost,mobileno:mobileno,rtrsno:rtrsno,otherinfo:otherinfo,VillageId:VillageId,HotelDetails:HotelDetails,aggregatorDetails:aggregatorDetails,kskDetails:kskDetails,cropDetails:cropDetails,vehicleDetails:vehicleDetails,
                            aggregatorName:aggregatorName,aggregatorNumber:aggregatorNumber,kskname:kskname,kskshopownername:kskshopownername,kskshopownerno:kskshopownerno,cropname:cropname,fertilizername:fertilizername,seedname:seedname,pesticidename:pesticidename,
                            vehicle_type:vehicle_type,regno:regno,capacity:capacity,drivername:drivername,driverno:driverno,vehownername:vehownername,vehownerno:vehownerno,hotelName:hotelName,OwnerName:OwnerName,ownerMobile:ownerMobile}, 
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {                            
                            alert_float('success', 'Record Updated Successfully...');                       
                            refreshTable();   
                            $('tr.addedtr').remove();                                          
                            ResetForm();   
                            return;                   
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
    });

    $(document).on('click', '.get_ItemID', function()
    {        
        VillageId = $(this).attr("data-id");       
      
        $.ajax({
			url:"<?php echo admin_url(); ?>VillageMaster/GetVillageDetailByID",
			dataType:"JSON",
			method:"POST",
			data:{VillageId:VillageId},
			beforeSend: function () {
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
			},
			complete: function () {
                $('.searchh2').css('display','none');
			},
			success:function(data)
            {	
                var Villageinfo = data.villageDetails;
                var Hotelinfo = data.HotelDetails;
                var Aggregatorinfo = data.villageAggregatorDetails;                
                var Kskinfo = data.kskDetails;
                var Cropinfo = data.cropdetails;
                var Vehicleinfo = data.vehicleDetails;
                var Crops = data.crops;        
                var Fertilizers = data.fertilizers;    
                var Seeds = data.seeds;    
                var Pesticides = data.pesticides;
                var States = data.Statename;
                var Cities = data.Cityname;
                var Taluka = data.Talukaname;

                var pincodeId = Villageinfo.Pincode;           
                updateDropdowns(pincodeId);  

                var date = new Date(Villageinfo.VisitDate.split(' ')[0]);                
                var day = ("0" + date.getDate()).slice(-2); 
                var month = ("0" + (date.getMonth() + 1)).slice(-2); 
                var year = date.getFullYear();                
                var formattedDate = day + '/' + month + '/' + year;

				$('#visitdate').val(formattedDate);
				$('.selectpicker').selectpicker('refresh')

                $('#villagename').val(Villageinfo.VillageName);

                $('#pin').val(Villageinfo.Pincode);
				$('.selectpicker').selectpicker('refresh')             
               
                $('#state').val(States.state_name);		              
                $("#district").val(Cities.city_name);
                $("#taluka").val(Taluka.TalukaName);

                $('#sarpanch').val(Villageinfo.VillageSarpanch);
                $('#population').val(Villageinfo.VillagePopulation);
                $('#area').val(Villageinfo.Area);
                $('#influencername').val(Villageinfo.InfluencerName);
                $('#govtpost').val(Villageinfo.InfluencerGovtPost);
                $('#mobileno').val(Villageinfo.Influencer_MobNo);
                $('#rtrsno').val(Villageinfo.NoRtrsFarmers);
                $('#info').val(Villageinfo.OtherInformation);
                
                $("#hoteltbody").find('tr').remove();
                $("#hoteltbody").append(`
                        <tr>
                            <td style="text-align:center;">
                                <input type="text" id="hotelname" name="hotname[]" class="form-control">
                                <div id="hotel-error" style="color: red;"></div> 
                            </td>
                            <td>
                                <input type="text" id="ownername" name="owname[]" class="form-control">
                                <div id="ow-error" style="color: red;"></div> 
                            </td>
                            <td>
                                <input type="text" id="owmobile" name="ownermobileno[]" class="form-control">
                                <div id="owmobile-error" style="color: red;"></div> 
                            </td>
                            <td>
                                <a class="btn btn-success" onclick="addHotelRow()"><i class="fa fa-plus"></i></a>
                            </td>
                        </tr>
                    `);
                    
                $.each(Hotelinfo, function(index, hotel) 
                {                   
                    var newRow = $("<tr class='addedtr'></tr>");                
                    newRow.append("<td><input type='text' name='hotelnames[]' class='form-control' value='" + hotel.HotelName + "'></td>");
                    newRow.append("<td><input type='text' name='ownernames[]' class='form-control' value='" + hotel.OwnerName + "'></td>");
                    newRow.append("<td><input type='text' name='ownermobiles[]' class='form-control' value='" + hotel.OwnerMobileNo + "'></td>");
                    newRow.append("<td><a href='#' class='btn btn-danger removehotelbtn'><i class='fa fa-times'></i></a></td>");               
                    
                    $("#hoteltbody").append(newRow);
                });

                $("#aggregatortbody").find('tr').remove();
                $("#aggregatortbody").append(`
                        <tr>
                            <td style="text-align:center;">
                                <input type="text" id="aggregatorname" name="aggname[]" class="form-control">
                                <div id="aggrename-error" style="color: red;"></div> 
                            </td>
                            <td>
                                <input type="text" id="aggregatorno" name="aggno[]" class="form-control">
                                <div id="mobile-error" style="color: red;"></div> 
                            </td>
                            <td>
                                <a class="btn btn-success" onclick="addAggregatorRow()"><i class="fa fa-plus"></i></a>
                            </td>
                        </tr>
                    `);
               
                $.each(Aggregatorinfo, function(index, aggregator) 
                {                   
                    var newRow = $("<tr class='addedtr'></tr>");                
                    newRow.append("<td><input type='text' name='aggregator_name[]' class='form-control' value='" + aggregator.VillageAggregatorName + "'></td>");
                    newRow.append("<td><input type='text' name='aggregator_no[]' class='form-control' value='" + aggregator.AggregatorMobNo + "'></td>");
                    newRow.append("<td><a href='#' class='btn btn-danger removeaggregatorbtn'><i class='fa fa-times'></i></a></td>");               
                    
                    $("#aggregatortbody").append(newRow);
                });
                
                $("#kskdetailstbody").find('tr').remove();
                $("#kskdetailstbody").append(`
                    <tr>
                        <td style="text-align:center;">
                            <input type="text" id="kskname" name="KSKname[]" class="form-control">
                            <div id="kskname-error" style="color: red;"></div> 
                        </td>
                        <td>
                            <input type="text" id="kskshopownername" name="kskshname[]" class="form-control">
                            <div id="kskownername-error" style="color: red;"></div>
                        </td>
                        <td>
                            <input type="text" id="kskshopownerno" name="kskshno[]" class="form-control">
                            <div id="kskownerno-error" style="color: red;"></div>
                        </td>
                        <td>
                            <a class="btn btn-success" onclick="addKsk()"><i class="fa fa-plus"></i></a>
                        </td>
                    </tr>
                `);                
                $.each(Kskinfo, function(index, ksk) 
                {
                    var newRow = $("<tr class='addedtr'></tr>");
                    // Append columns to the new row      
                    newRow.append("<td><input type='text' name='ksk_name[]' class='form-control' value='" + ksk.KskName + "'></td>");
                    newRow.append("<td><input type='text' name='ksk_owner_name[]' class='form-control' value='" + ksk.KskShopOwnerName + "'></td>");
                    newRow.append("<td><input type='text' name='ksk_owner_no[]' class='form-control' value='" + ksk.KskShopOwnerNo + "'></td>");       
                    newRow.append("<td><a href='#' class='btn btn-danger kskremovebtn'><i class='fa fa-times'></i></a></td>");
                
                    $("#kskdetailstbody").append(newRow);
                });
               
                $("#cropdetailstbody").find('tr').remove();
                $("#cropdetailstbody").append(`
                    <tr>
                        <td>
                            <select name="cropname[]" id="cropname" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                <option value="">Non Selected</option> 
                                <?php
                                    foreach($crops as $crop) {
                                        echo '<option value="' . $crop['id'] . '">' . $crop['CropName'] . '</option>';
                                    }
                                ?>  
                            </select>
                            <div id="crop-error" style="color: red;"></div>
                        </td>   
                        <td>
                            <select name="fertilizername[]" id="fertilizername" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true">
                                <option value="">Non Selected</option> 
                                <!-- PHP generated options for fertilizers -->
                                <?php
                                    foreach($fertilizers as $fer) {
                                        echo '<option value="' . $fer['id'] . '">' . $fer['fertilizerName'] . '</option>';
                                    }
                                ?>  
                            </select>
                            <div id="fertilizer-error" style="color: red;"></div> 
                        </td> 
                        <td>
                            <select name="seedname[]" id="seedname" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true">
                                <option value="">Non Selected</option> 
                                <!-- PHP generated options for seeds -->
                                <?php
                                    foreach($seeds as $seed) {
                                        echo '<option value="' . $seed['id'] . '">' . $seed['SeedName'] . '</option>';
                                    }
                                ?>  
                            </select>   
                            <div id="seed-error" style="color: red;"></div>     
                        </td>  
                        <td>
                            <select name="pesticidename[]" id="pesticidename" class="selectpicker form-control" multiple data-none-selected-text="Non Selected" data-live-search="true">
                                <option value="">Non Selected</option> 
                                <!-- PHP generated options for pesticides -->
                                <?php
                                    foreach($pesticides as $pesti) {
                                        echo '<option value="' . $pesti['id'] . '">' . $pesti['PesticideName'] . '</option>';
                                    }
                                ?>  
                            </select>   
                            <div id="pesti-error" style="color: red;"></div>  
                        </td>              
                        <td>
                            <a class="btn btn-success" onclick="addCrop()"><i class="fa fa-plus"></i></a>
                        </td>
                    </tr>
                `);
                $('.selectpicker').selectpicker();
                $.each(Cropinfo, function(index, crop) 
                {
                    var fertilizerIds = crop.FertilizerId.split(',');                   
                    var fertilizerNames = crop.fertilizername.split(','); 
                    var fertilizerDisplay = fertilizerNames.join(", ");

                    var cropIds = crop.CropId.split(',');     
                    var cropNames = crop.cropname.split(','); 
                    var cropDisplay = cropNames.join(", ");

                    var seedIds = crop.SeedId.split(','); 
                    var seedNames = crop.SeedName.split(','); 
                    var seedDisplay = seedNames.join(", "); 

                    var pesticideIds = crop.PesticideId.split(','); 
                    var pesticideNames = crop.PesticideName.split(','); 
                    var pesticideDisplay = pesticideNames.join(", "); 

                    var newRow = $("<tr class='addedtr'></tr>");
                    //Append columns to the new row
                    //newRow.append("<td><input type='hidden' name='majorcrop[]' value='" + crop.CropId + "'>" + cropDisplay + "</td>");  
                    
                    var cropsIds = crop.CropId.split(',');                           
                    var dropdownHTML = "<td><select name='majorcrop[]' class='selectpicker form-control' data-live-search='true'>" +
                    "<option value=''>Non Selected</option>"; 
                    Crops.forEach(function(crops) {                      

                        var selected = cropsIds.includes(crops.id.toString()) ? "selected" : "";                   
                        dropdownHTML += "<option value='" + crops.id + "' " + selected + ">" + crops.CropName + "</option>";                        
                    });
                    dropdownHTML += "</select></td>";
                    newRow.append(dropdownHTML);
                    $('.selectpicker').selectpicker('refresh');

                    //newRow.append("<td><input type='hidden' name='fertilizername[]' value='" + crop.FertilizerId + "'>" + fertilizerDisplay + "</td>");        
                    
                    var fersIds = crop.FertilizerId.split(',');    
                    var dropdownHTMLfer = "<td><select name='fertilizername[]' class='selectpicker form-control' data-live-search='true' multiple>" +
                    "<option value=''>Non Selected</option>"; 
                    Fertilizers.forEach(function(fertilizers) {                      

                    var selected = fersIds.includes(fertilizers.id.toString()) ? "selected" : "";                   
                    dropdownHTMLfer += "<option value='" + fertilizers.id + "' " + selected + ">" + fertilizers.fertilizerName + "</option>";                        
                    });   
                    dropdownHTMLfer += "</select></td>";
                    newRow.append(dropdownHTMLfer);
                    $('.selectpicker').selectpicker('refresh');
                    
                    //newRow.append("<td><input type='hidden' name='seedname[]' value='" + crop.SeedId + "'>" + seedDisplay + "</td>");        
                    
                    var seedsIds = crop.SeedId.split(',');    
                    var dropdownHTMLseed = "<td><select name='seedname[]' class='selectpicker form-control' data-live-search='true' multiple>" +
                    "<option value=''>Non Selected</option>"; 
                    Seeds.forEach(function(seed) {                      

                    var selected = seedsIds.includes(seed.id.toString()) ? "selected" : "";                   
                    dropdownHTMLseed += "<option value='" + seed.id + "' " + selected + ">" + seed.SeedName + "</option>";                        
                    });  
                    dropdownHTMLseed += "</select></td>";
                    newRow.append(dropdownHTMLseed);
                    $('.selectpicker').selectpicker('refresh'); 

                    //newRow.append("<td><input type='hidden' name='pesticidename[]' value='" + crop.PesticideId + "'>" + pesticideDisplay + "</td>");          
                    var pesticidesIds = crop.PesticideId.split(',');   
                    var dropdownHTMLpesticide = "<td><select name='pesticidename[]' class='selectpicker form-control' data-live-search='true' multiple>" +
                    "<option value=''>Non Selected</option>";  
                    Pesticides.forEach(function(pesticide) {                      

                    var selected = pesticidesIds.includes(pesticide.id.toString()) ? "selected" : "";                   
                    dropdownHTMLpesticide += "<option value='" + pesticide.id + "' " + selected + ">" + pesticide.PesticideName + "</option>";                        
                    });  
                    dropdownHTMLpesticide += "</select></td>";
                    newRow.append(dropdownHTMLpesticide);
                    $('.selectpicker').selectpicker('refresh'); 

                    newRow.append("<td><a href='#' class='btn btn-danger cropremovebtn'><i class='fa fa-times'></i></a></td>");
                    
                    // Append the new row to the table body
                    $("#cropdetailstbody").append(newRow);                                                     
                });

                $("#addresstbody").find('tr').remove();  
                $("#addresstbody").append(`
                    <tr>
                        <td>
                            <select class="form-control city selectpicker" data-width="100%" data-none-selected-text="Non Selected" name="vehicle_type[]" id="vehicle_type" data-live-search="true">
                                <option value="">Non Selected</option>
                                <option value="Truck">Truck</option>
                                <option value="Tractor">Tractor</option>
                                <option value="Pickup Man">Pickup Man</option>
                            </select>
                            <div id="vehicletype-error" style="color: red;"></div>
                        </td>
                        <td style="text-align:center;">
                            <input type="text" id="regno" name="vehicleregno[]" class="form-control">
                            <div id="regno-error" style="color: red;"></div>
                        </td>
                        <td>
                            <input type="text" id="capacity" name="vehiclecapacity[]" class="form-control">
                            <div id="capacity-error" style="color: red;"></div>
                        </td>
                        <td>
                            <input type="text" id="drivername" name="vehicledrivername[]" class="form-control">
                            <div id="drivername-error" style="color: red;"></div>
                        </td>
                        <td width="2%" style="text-align: center;">
                            <input type="text" id="driverno" name="vehicledrivermobileno[]" class="form-control">
                            <div id="driverno-error" style="color: red;"></div>
                        </td>
                        <td>
                            <input type="text" id="vehownername" name="vehownernames[]" class="form-control">
                            <div id="ownername-error" style="color: red;"></div>
                        </td>
                        <td>
                            <input type="text" id="vehownerno" name="vehmobileno[]" class="form-control custom-width">
                            <div id="ownerno-error" style="color: red;"></div>
                        </td>
                        <td>
                            <a class="btn btn-success" onclick="addRow()"><i class="fa fa-plus"></i></a>
                        </td>
                    </tr>
                `);   
                $('.selectpicker').selectpicker();             
                $.each(Vehicleinfo, function(index, vehicle) 
                {
                    var newRow = $("<tr class='addedtr'></tr>");
                    // Append columns to the new row
                    newRow.append("<td><input type='hidden' name='vehicle_type[]' value='" + vehicle.VehicleType + "'>" + vehicle.VehicleType + "</td>");        
                    newRow.append("<td><input type='text' name='regsistration_no[]' class='form-control' value='" + vehicle.RegsiterNo + "'></td>");
                    newRow.append("<td><input type='text' name='vehicle_capacity[]' class='form-control' value='" + vehicle.capacity + "'></td>");
                    newRow.append("<td><input type='text' name='vehicle_drivername[]' class='form-control' value='" + vehicle.DriverName + "'></td>");
                    newRow.append("<td><input type='text' name='vehicle_driverno[]' class='form-control' value='" + vehicle.MobileNo + "'></td>");
                    newRow.append("<td><input type='text' name='vehicle_ownername[]' class='form-control' value='" + vehicle.OwnerName + "'></td>");
                    newRow.append("<td><input type='text' name='vehicleowner_no[]' class='form-control' value='" + vehicle.OwnerMobNo + "'></td>");
                    newRow.append("<td><a href='#' class='btn btn-danger removebtn'><i class='fa fa-times'></i></a></td>");

                    // Append the new row to the table body
                    $("#addresstbody").append(newRow);
                });

				$('.saveBtn').hide();
				$('.updateBtn').show();	         		
			}
		});
		$('#Item_List').modal('hide');        

        //update village details
        $('.updateBtn').on('click',function() 
        {              
            visitdate= $('#visitdate').val(); 
            villagename = $('#villagename').val(); 
            pin = $('#pin').val(); 
            taluka = $('#taluka_id').val();
            district = $('#district_id').val();
            state = $('#state_id').val();
            villagesarpanch = $('#sarpanch').val();
            villagepopulation = $('#population').val();
            villagearea = $('#area').val();
            villageinfluencername = $('#influencername').val();  
            govtpost = $('#govtpost').val();      
            mobileno = $('#mobileno').val();
            rtrsno = $('#rtrsno').val();  
            otherinfo = $('#info').val();  

            var hotelName = $("#hotelname").val();
            var OwnerName = $("#ownername").val();
            var ownerMobile = $("#owmobile").val();


            var aggregatorName = $("#aggregatorname").val();
            var aggregatorNumber = $("#aggregatorno").val();  
            
            var kskname = $("#kskname").val();
            var kskshopownername = $("#kskshopownername").val();
            var kskshopownerno = $("#kskshopownerno").val();

            var cropname = $("#cropname").val();
            var fertilizername = $("#fertilizername").val();
            var seedname = $("#seedname").val();
            var pesticidename = $("#pesticidename").val();   

            var vehicle_type = $("#vehicle_type").val();
            var regno = $("#regno").val();
            var capacity = $("#capacity").val();
            var drivername = $("#drivername").val();  
            var driverno = $("#driverno").val();
            var vehownername = $("#vehownername").val();
            var vehownerno = $("#vehownerno").val();

            //Hotel Details 
            var HotelDetails = [];
            $('tr.addedtr').each(function() {
                var Hotname = $(this).find('input[name="hotelnames[]"]').val();
                var hotownerName = $(this).find('input[name="ownernames[]"]').val();
                var hotownerMobile =  $(this).find('input[name="ownermobiles[]"]').val();
                HotelDetails.push({
                    Hotname: Hotname,
                    hotownerName: hotownerName,
                    hotownerMobile:hotownerMobile
                });
            });  
            
            //aggregator details
            var aggregatorDetails = [];
            $('tr.addedtr').each(function() {
                var name = $(this).find('input[name="aggregator_name[]"]').val();
                var no = $(this).find('input[name="aggregator_no[]"]').val();

                aggregatorDetails.push({
                    name: name,
                    no: no
                });
            });    
                
            //Ksk details
            var kskDetails = [];
            $('tr.addedtr').each(function() {
                var kskName = $(this).find('input[name="ksk_name[]"]').val();
                var kskownershopname = $(this).find('input[name="ksk_owner_name[]"]').val();
                var kskshopownerNo = $(this).find('input[name="ksk_owner_no[]"]').val();

                kskDetails.push({
                    kskName: kskName,
                    kskownershopname: kskownershopname,
                    kskshopownerNo: kskshopownerNo
                });
            });  

            //crop details
            var cropDetails = [];

            $('tr.addedtr').each(function(index) {
                var cropname = $(this).find('select[name="majorcrop[]"]').val();  // Get the selected crop IDs
                var fertilizername = $(this).find('select[name="fertilizername[]"]').val();  // Get the selected fertilizer IDs
                var seedname = $(this).find('select[name="seedname[]"]').val();  // Get the selected seed IDs
                var pesticidename = $(this).find('select[name="pesticidename[]"]').val();

                cropDetails.push({
                    index: index, 
                    cropname: cropname,
                    fertilizername: fertilizername,
                    seedname: seedname,
                    pesticidename:pesticidename
                });
            });             

            //village vehicle details
            var vehicleDetails = [];

            $('tr.addedtr').each(function() {
                var vehicleType = $(this).find('input[name="vehicle_type[]"]').val();
                var RegNo = $(this).find('input[name="regsistration_no[]"]').val();
                var vehicleCapacity = $(this).find('input[name="vehicle_capacity[]"]').val();
                var Drivername = $(this).find('input[name="vehicle_drivername[]"]').val();
                var driverNo = $(this).find('input[name="vehicle_driverno[]"]').val();
                var ownerName = $(this).find('input[name="vehicle_ownername[]"]').val();
                var ownerNo = $(this).find('input[name="vehicleowner_no[]"]').val();

                vehicleDetails.push({
                    vehicleType: vehicleType,
                    RegNo: RegNo,
                    vehicleCapacity: vehicleCapacity,
                    Drivername:Drivername,
                    driverNo:driverNo,
                    ownerName:ownerName,
                    ownerNo:ownerNo
                });
            });           

            if (!villagename) {
                $('#VillageIDError').text('Please Enter Village Name').show();
                $('#villagename').focus();
                setTimeout(() => { $('#VillageIDError').hide(); }, 2000);
                return;
            }
            else if(!pin){            
                $('#PinIDError').text('Please select Pincode').show();
                $('#pin').focus();
                setTimeout(() => { $('#PinIDError').hide(); }, 2000);
                return;
            }
            else if(!state){
                $('#StateIDError').text('Please select State').show();
                $('#state').focus();
                setTimeout(() => { $('#StateIDError').hide(); }, 2000);
                return;  
            }
            else if(!district){
                $('#DistIDError').text('Please select District').show();
                $('#district').focus();
                setTimeout(() => { $('#DistIDError').hide(); }, 2000);
                return;
            }
            else if(!taluka){
                $('#TalukaIDError').text('Please select Taluka').show();
                $('#taluka').focus();
                setTimeout(() => { $('#TalukaIDError').hide(); }, 2000);
                return;   
            }
            else if(!villagesarpanch){
                $('#SarpanchError').text('Enter village sarpanch name').show();
                $('#sarpanch').focus();
                setTimeout(() => { $('#SarpanchError').hide(); }, 2000);
                return;   
            }
            else if(villagepopulation !== "" && !/^\d+$/.test(villagepopulation)){
                alert('Enter a valid positive integer for the village population.');
                $('#population').focus();
                return;
            }
            else if(villagearea !== "" && !/^\d+(\.\d+)?$/.test(villagearea)){
                alert('Enter a valid area value (positive integer or float).');
                $('#area').focus();
                return;
            }
            else if(mobileno !== "" && !mobileno.match(/^[0-9]{10}$/)) {
                alert('Enter a valid 10-digit mobile number.');
                $('#mobileno').focus();
                return; 
            }
            else if(rtrsno !== "" && !/^\d+$/.test(rtrsno)){
                alert('Enter a valid no of rtrs farmers.');
                $('#rtrsno').focus();
                return;
            } 
            else if(hotelName === '' && OwnerName !== '')
            {
                alert('Enter hotel name');  
            }
            else if(OwnerName === '' && hotelName !== '')
            {
                alert('Enter hotel owner name');  
            }
            else if (hotelName !== '' && OwnerName !== '' && (ownerMobile.length !== 10 || !/^[0-9]{10}$/.test(ownerMobile))) 
            {              
                alert('Enter a valid mobile number');            
            }
            else if (aggregatorName === '' && aggregatorNumber !== '') 
            {                 
                alert('Enter aggregator name');                
            }else if (aggregatorName !== '' && aggregatorNumber === '') 
            {             
                alert('Enter aggregator number');              
            }else if (aggregatorName !== '' && aggregatorNumber !== '' && (aggregatorNumber.length !== 10 || !/^[0-9]{10}$/.test(aggregatorNumber))) 
            {              
                alert('Enter a valid mobile number');            
            }else if(kskname !== '' && kskshopownername === '') 
            {
                alert('Enter ksk shop owner name.');      
            } 
            else if(kskshopownername !== '' && kskname == '')  
            {
                alert('Enter ksk name.');  
            }    
            else if(kskshopownername !== '' && kskshopownerno ==='')
            {
                alert('Enter ksk shop owner number.');   
            }            
            else if (kskname !== '' && kskshopownername !== '' && kskshopownerno !=='' && (kskshopownerno.length !== 10 || !/^[0-9]{10}$/.test(kskshopownerno))) 
            {              
                alert('Enter a valid mobile number');            
            }else if(cropname !== '' && fertilizername ==='')
            {
                alert('Select fertilizer name.');   
            }
            // else if(fertilizername !=='' && cropname === '')
            // {
            //     alert('Select crop name.');   
            // }
            else if(fertilizername !=='' && seedname ==='')
            {
                alert('Select seed name.');   
            }
            else if(seedname !=='' && pesticidename ==='')
            {
                alert('Select pesticide name.');   
            }
            else if(vehicle_type !=='' && regno ==='')
            {   alert('Enter vehicle registration number.');  }
            else if(regno !=='' && vehicle_type ==='')
            {  alert('Select vehicle type'); }
            else if(regno !=='' && capacity ==='')
            {  alert('Enter vehicle capacity'); }
            else if(capacity !=='' && (capacity !=="" && !/^\d+$/.test(capacity)))
            { alert('Enter valid vehicle capacity'); }
            else if(capacity !=='' && drivername ==='')
            { alert('Enter vehicle driver name'); }
            else if(drivername !=='' && driverno ==='')
            { alert('Enter vehicle driver number'); }
            else if(driverno !=='' && (driverno.length !== 10 || !/^[0-9]{10}$/.test(driverno)))
            {  alert('Enter a valid mobile number'); }
            else if(driverno !=='' && vehownername ==='')
            { alert('Enter vehicle owner name');  }
            else if(vehownername !=='' && vehownerno ==='')
            { alert('Enter vehicle owner number');}
            else if(vehownerno !=='' && (vehownerno.length !== 10 || !/^[0-9]{10}$/.test(vehownerno)))
            {  alert('Enter a valid mobile number'); }
            else
            {
                $.ajax({
                    url: "<?php echo admin_url(); ?>VillageMaster/UpdateVillageDetails", 
                    type: 'POST', 
                    data: {VillageId:VillageId,visitdate:visitdate,villagename:villagename,pin:pin,taluka:taluka,district:district,state:state,
                            villagesarpanch:villagesarpanch,villagepopulation:villagepopulation,villagearea:villagearea,villageinfluencername:villageinfluencername,
                            govtpost:govtpost,mobileno:mobileno,rtrsno:rtrsno,otherinfo:otherinfo,VillageId:VillageId,HotelDetails:HotelDetails,aggregatorDetails:aggregatorDetails,kskDetails:kskDetails,cropDetails:cropDetails,vehicleDetails:vehicleDetails,
                            aggregatorName:aggregatorName,aggregatorNumber:aggregatorNumber,kskname:kskname,kskshopownername:kskshopownername,kskshopownerno:kskshopownerno,cropname:cropname,fertilizername:fertilizername,seedname:seedname,pesticidename:pesticidename,
                            vehicle_type:vehicle_type,regno:regno,capacity:capacity,drivername:drivername,driverno:driverno,vehownername:vehownername,vehownerno:vehownerno,hotelName:hotelName,OwnerName:OwnerName,ownerMobile:ownerMobile}, 
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {                            
                            alert_float('success', 'Record Updated Successfully...');                       
                            refreshTable();   
                            $('tr.addedtr').remove();                                          
                            ResetForm();   
                            return;                   
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
    });
</script>

<script>
    function addRow() 
    {
        var vehicle_type = $("#vehicle_type").val();
        var vehicle_type_text = $("#vehicle_type option:selected").text();
        var regsistration_no = $("#regno").val();
        var vehicle_capacity = $("#capacity").val();
        var vehicle_drivername = $("#drivername").val();
        var vehicle_driverno = $("#driverno").val();
        var vehicle_ownername = $("#vehownername").val();
        var vehicleowner_no = $("#vehownerno").val();
       
        if (vehicle_type === '') {            
            $('#vehicletype-error').text('Please select a vehicle type.');
            setTimeout(() => {
                $('#vehicletype-error').text('');  
            }, 2000); 
            return;            
        }else if (regsistration_no.trim() === '') {            
            $('#regno-error').text('Enter vehicle registration number.');
            setTimeout(() => {
                $('#regno-error').text('');  
            }, 2000); 
            return;            
        }
        else if ((vehicle_capacity == "") || (vehicle_capacity !=="" && !/^\d+$/.test(vehicle_capacity))) {            
            $('#capacity-error').text('Enter vehicle valid vehicle capacity.');
            setTimeout(() => {
                $('#capacity-error').text('');  
            }, 2000); 
            return;            
        }else if (vehicle_drivername.trim() === '') {            
            $('#drivername-error').text('Enter driver name.');
            setTimeout(() => {
                $('#drivername-error').text('');  
            }, 2000); 
            return;            
        }else if (vehicle_driverno.length !== 10 || !/^[0-9]+$/.test(vehicle_driverno)) {            
            $('#driverno-error').text('Enter valid mobile number.');
            setTimeout(() => {
                $('#driverno-error').text('');  
            }, 2000); 
            return;           
        }else if (vehicle_ownername.trim() === '') {            
            $('#ownername-error').text('Enter owner name.');
            setTimeout(() => {
                $('#ownername-error').text('');  
            }, 2000); 
            return;            
        }else if (vehicleowner_no.length !== 10 || !/^[0-9]+$/.test(vehicleowner_no)) {            
            $('#ownerno-error').text('Enter valid owner mobile number.');
            setTimeout(() => {
                $('#ownerno-error').text('');  
            }, 2000); 
            return;           
        }

        var newRow = $("<tr class='addedtr'></tr>");
        // Append columns to the new row
        newRow.append("<td><input type='hidden' name='vehicle_type[]' value='" + vehicle_type + "'>" + vehicle_type_text + "</td>");        
        newRow.append("<td><input type='text' name='regsistration_no[]' class='form-control' value='" + regsistration_no + "'></td>");
        newRow.append("<td><input type='text' name='vehicle_capacity[]' class='form-control' value='" + vehicle_capacity + "'></td>");
        newRow.append("<td><input type='text' name='vehicle_drivername[]' class='form-control' value='" + vehicle_drivername + "'></td>");
        newRow.append("<td><input type='text' name='vehicle_driverno[]' class='form-control' value='" + vehicle_driverno + "'></td>");
        newRow.append("<td><input type='text' name='vehicle_ownername[]' class='form-control' value='" + vehicle_ownername + "'></td>");
        newRow.append("<td><input type='text' name='vehicleowner_no[]' class='form-control' value='" + vehicleowner_no + "'></td>");
        newRow.append("<td><a href='#' class='btn btn-danger removebtn'><i class='fa fa-times'></i></a></td>");

        // Append the new row to the table body
        $("#addresstbody").append(newRow);

        // Clear input fields after adding row
        $("#vehicle_type").val('').selectpicker('refresh');        
        $("#regno").val('');
        $("#capacity").val('');
        $("#drivername").val('');  
        $("#driverno").val('');
        $("#vehownername").val('');     
        $("#vehownerno").val('');      
    }

    $(document).on('click', '.removebtn', function() {
    $(this).closest('tr').remove();
    });
</script>

<script>
    function addCrop() 
    {             
        var cropsData = <?php echo json_encode($crops); ?>;
        var fertilizersData = <?php echo json_encode($fertilizers); ?>;
        var seedData = <?php echo json_encode($seeds); ?>;
        var pesticideData = <?php echo json_encode($pesticides); ?>;

        var majorcrop = $("#cropname").val();                    
        var majorcrop_text = $("#cropname option:selected").text();
        var majorcrop_text = [];      
        $("#cropname option:selected").each(function() {
            majorcrop_text.push($(this).text());  
        });        
        var majorcrop_text_display = majorcrop_text.join(", ");

        var fertilizername = $("#fertilizername").val();        
        var fertilizername_text = [];
        $("#fertilizername option:selected").each(function() {
            fertilizername_text.push($(this).text()); 
        });
        var fertilizername_text_display = fertilizername_text.join(", ");

        var seedname = $("#seedname").val();
        var seedname_text = [];       
        $("#seedname option:selected").each(function() {
            seedname_text.push($(this).text());  
        });       
        var seedname_text_display = seedname_text.join(", "); 

        var pesticidename = $("#pesticidename").val();
        var pesticidename_text = [];
        $("#pesticidename option:selected").each(function() {
            pesticidename_text.push($(this).text());  
        });
        var pesticidename_text_display = pesticidename_text.join(", ");  

        if (majorcrop === '') {            
            $('#crop-error').text('Please select a crop name.');
            setTimeout(() => {
                $('#crop-error').text('');  
            }, 2000); 
            return;            
        }
        else if(fertilizername === ''){
            $('#fertilizer-error').text('Please select a fertilizer brand.');
            setTimeout(() => {
                $('#fertilizer-error').text('');  
            }, 2000); 
            return;   
        }
        else if(seedname === ''){
            $('#seed-error').text('Please select a seed brand.');
            setTimeout(() => {
                $('#seed-error').text('');  
            }, 2000); 
            return;   
        }
        else if(pesticidename === ''){
            $('#pesti-error').text('Please select a pesticide brand.');
            setTimeout(() => {
                $('#pesti-error').text('');  
            }, 2000); 
            return;   
        }

        var newRow = $("<tr class='addedtr'></tr>");
        // Append columns to the new row      

        //newRow.append("<td><input type='hidden' name='majorcrop[]' value='" + majorcrop.join(',') + "'>" + majorcrop_text_display + "</td>");  
        //newRow.append("<td><input type='hidden' name='fertilizername[]' value='" + fertilizername.join(',') + "'>" + fertilizername_text_display + "</td>");        
       
        var cropsIds = majorcrop;                           
        var dropdownHTML = "<td><select name='majorcrop[]' class='selectpicker form-control' data-live-search='true'>" +
        "<option value=''>Non Selected</option>"; 
        cropsData.forEach(function(crops) {                      

            var selected = cropsIds.includes(crops.id.toString()) ? "selected" : "";                   
            dropdownHTML += "<option value='" + crops.id + "' " + selected + ">" + crops.CropName + "</option>";                        
        });
        dropdownHTML += "</select></td>";
        newRow.append(dropdownHTML);
        setTimeout(function() {           
            $('.selectpicker').selectpicker('refresh');
        }, 0);

        var fersIds = fertilizername;         
        var dropdownHTMLfer = "<td><select name='fertilizername[]' class='selectpicker form-control' data-live-search='true' multiple>" +
        "<option value=''>Non Selected</option>"; 
        fertilizersData.forEach(function(fertilizers) {                      

        var selected = fersIds.includes(fertilizers.id.toString()) ? "selected" : "";     
                  
        dropdownHTMLfer += "<option value='" + fertilizers.id + "' " + selected + ">" + fertilizers.fertilizerName + "</option>";                        
        });             
        dropdownHTMLfer += "</select></td>";
        newRow.append(dropdownHTMLfer);
        setTimeout(function() {           
            $('.selectpicker').selectpicker('refresh');
        }, 0);

        var seedsIds = seedname; 
        var dropdownHTMLseed = "<td><select name='seedname[]' class='selectpicker form-control' data-live-search='true' multiple>" +
        "<option value=''>Non Selected</option>"; 
        seedData.forEach(function(seed) {                      

        var selected = seedsIds.includes(seed.id.toString()) ? "selected" : "";                   
        dropdownHTMLseed += "<option value='" + seed.id + "' " + selected + ">" + seed.SeedName + "</option>";                        
        });  
        dropdownHTMLseed += "</select></td>";
        newRow.append(dropdownHTMLseed);
        setTimeout(function() {           
            $('.selectpicker').selectpicker('refresh');
        }, 0);

        var pesticidesIds = pesticidename;  
        var dropdownHTMLpesticide = "<td><select name='pesticidename[]' class='selectpicker form-control' data-live-search='true' multiple>" +
        "<option value=''>Non Selected</option>";  
        pesticideData.forEach(function(pesticide) {                      

        var selected = pesticidesIds.includes(pesticide.id.toString()) ? "selected" : "";                   
        dropdownHTMLpesticide += "<option value='" + pesticide.id + "' " + selected + ">" + pesticide.PesticideName + "</option>";                        
        });  
        dropdownHTMLpesticide += "</select></td>";
        newRow.append(dropdownHTMLpesticide);
        setTimeout(function() {           
            $('.selectpicker').selectpicker('refresh');
        }, 0);

        //newRow.append("<td><input type='hidden' name='seedname[]' value='" + seedname.join(',') + "'>" + seedname_text_display + "</td>");        
        //newRow.append("<td><input type='hidden' name='pesticidename[]' value='" + pesticidename.join(',') + "'>" + pesticidename_text_display + "</td>");          
        newRow.append("<td><a href='#' class='btn btn-danger cropremovebtn'><i class='fa fa-times'></i></a></td>");
    
        // Append the new row to the table body
        $("#cropdetailstbody").append(newRow);

        // Clear input fields after adding row
        $("#cropname").val('').selectpicker('refresh');        
        $("#fertilizername").val('').selectpicker('refresh');        
        $("#seedname").val('').selectpicker('refresh');        
        $("#pesticidename").val('').selectpicker('refresh');        
    }

    $(document).on('click', '.cropremovebtn', function() {
    $(this).closest('tr').remove();
    });
</script>

<script>
    function addKsk() 
    {
        var ksk_name = $("#kskname").val();        
        var ksk_owner_name = $("#kskshopownername").val();
        var ksk_owner_no = $("#kskshopownerno").val();

        if (ksk_name.trim() === '') {            
            $('#kskname-error').text('Enter krushi seva kendra name.');
            setTimeout(() => {
                $('#kskname-error').text('');  
            }, 2000); 
            return;            
        }else if(ksk_owner_name.trim() === '') {
            $('#kskownername-error').text('Enter shop owner name.');
            setTimeout(() => {
                $('#kskownername-error').text('');  
            }, 2000); 
            return;  
        }else if(ksk_owner_no.length !== 10 || !/^[0-9]+$/.test(ksk_owner_no)) {            
            $('#kskownerno-error').text('Please enter a valid 10-digit mobile number.');
            setTimeout(() => {
                $('#kskownerno-error').text('');  
            }, 2000); 
            return; 
        }        
        $('#kskownerno-error').text('');

        var newRow = $("<tr class='addedtr'></tr>");
        // Append columns to the new row      
        newRow.append("<td><input type='text' name='ksk_name[]' class='form-control' value='" + ksk_name + "'></td>");
        newRow.append("<td><input type='text' name='ksk_owner_name[]' class='form-control' value='" + ksk_owner_name + "'></td>");
        newRow.append("<td><input type='text' name='ksk_owner_no[]' class='form-control' value='" + ksk_owner_no + "'></td>");       
        newRow.append("<td><a href='#' class='btn btn-danger kskremovebtn'><i class='fa fa-times'></i></a></td>");
    
        $("#kskdetailstbody").append(newRow);

        // Clear input fields after adding row      
        $("#kskname").val('');
        $("#kskshopownername").val('');
        $("#kskshopownerno").val('');         
    }

    $(document).on('click', '.kskremovebtn', function() {
    $(this).closest('tr').remove();
    });
</script>

<script>
    function addAggregatorRow() 
    {
        var aggregator_name = $("#aggregatorname").val();      
        var aggregator_no = $("#aggregatorno").val();    
        
        if (aggregator_name.trim() === '') {            
            $('#aggrename-error').text('Enter village aggregator name.');
            setTimeout(() => {
                $('#aggrename-error').text('');  
            }, 2000); 
            return;            
        }else if(aggregator_no.length !== 10 || !/^[0-9]+$/.test(aggregator_no)) {            
            $('#mobile-error').text('Please enter a valid 10-digit mobile number.');
            setTimeout(() => {
                $('#mobile-error').text('');  
            }, 2000); 
            return; 
        }        
        $('#mobile-error').text('');

        var newRow = $("<tr class='addedtr'></tr>");
        newRow.append("<td><input type='text' name='aggregator_name[]' class='form-control' value='" + aggregator_name + "'></td>");
        newRow.append("<td><input type='text' name='aggregator_no[]' class='form-control' value='" + aggregator_no + "'></td>");
        newRow.append("<td><a href='#' class='btn btn-danger removeaggregatorbtn'><i class='fa fa-times'></i></a></td>");
        
        // Append the new row to the table body
        $("#aggregatortbody").append(newRow);         

        // Clear input fields after adding row              
        $("#aggregatorname").val('');
        $("#aggregatorno").val('');       
    }

    $(document).on('click', '.removeaggregatorbtn', function() {
    $(this).closest('tr').remove();
    });
</script>

<script>
    function addHotelRow() 
    {
        var hotelname = $("#hotelname").val();      
        var ownername = $("#ownername").val();    
        var ownermobile = $("#owmobile").val();
        
        if (hotelname.trim() === '') {            
            $('#hotel-error').text('Enter village hotel name.');
            setTimeout(() => {
                $('#hotel-error').text('');  
            }, 2000); 
            return;            
        }else if (ownername.trim() === '') {            
            $('#ow-error').text('Enter hotel owner name.');
            setTimeout(() => {
                $('#ow-error').text('');  
            }, 2000); 
            return;            
        }else if(ownermobile.length !== 10 || !/^[0-9]+$/.test(ownermobile)) {            
            $('#owmobile-error').text('Please enter a valid 10-digit mobile number.');
            setTimeout(() => {
                $('#owmobile-error').text('');  
            }, 2000); 
            return; 
        }        
        $('#owmobile-error').text('');
        
        var newRow = $("<tr class='addedtr'></tr>");
        newRow.append("<td><input type='text' name='hotelnames[]' class='form-control' value='" + hotelname + "'></td>");
        newRow.append("<td><input type='text' name='ownernames[]' class='form-control' value='" + ownername + "'></td>");
        newRow.append("<td><input type='text' name='ownermobiles[]' class='form-control' value='" + ownermobile + "'></td>");
        newRow.append("<td><a href='#' class='btn btn-danger removehotelbtn'><i class='fa fa-times'></i></a></td>");
        
        $("#hoteltbody").append(newRow);     
               
        $("#hotelname").val('');
        $("#ownername").val(''); 
        $("#owmobile").val('');
    }
    
    $(document).on('click', '.removehotelbtn', function() {
    $(this).closest('tr').remove();
    });
</script>

<script>
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table_Item_List");
        tr = table.getElementsByTagName("tr");

        for (i = 2; i < tr.length; i++) {           
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
</script>

<style>	  
#table_Item_List td:hover {
    cursor: pointer;
}
#table_Item_List tr:hover {
    background-color: #ccc;
}

    .custom-width {
        width: 105px;
    }

    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>