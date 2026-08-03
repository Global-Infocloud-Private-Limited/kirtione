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
    							<li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b>Warehouse Master</b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="searchh2" style="display:none;">Please wait while fetching data.</div>
                                <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                                <div class="searchh4" style="display:none;">Please wait while updating data.</div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <small class="req text-danger">* </small>
                                    <label for="WarehouseID" class="control-label">WHID</label>
                                    <input type="text" id="WarehouseID" name="WarehouseID" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <small class="req text-danger">* </small>
                                    <label for="WarehouseName" class="control-label">WH Name</label>
                                    <input type="text" id="WarehouseName" name="WarehouseName" class="form-control" value="" required>
                                </div>
                            </div>
                            <div class="col-md-2 pb-3">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <small class="req text-danger">* </small>
                                    <label for="type_of_assignment" class="selectpicker control-label">Type of Assignment</label>
                                    <select required id="type_of_assignment" name="type_of_assignment" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <option value="Factory Premises">Factory Premises</option>
                                        <option value="Stock Management">Stock Management</option>
                                        <option value="Liscensed">Liscensed</option>
                                        <option value="Unliscensed">Unliscensed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <small class="req text-danger">* </small>
                                    <label for="structure_type" class="selectpicker control-label">Structure Type</label>
                                    <select required id="structure_type" name="structure_type" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="Dry Warehouse">Dry Warehouse</option>
                                        <option value="Cold Storage">Cold Storage</option>
                                        <option value="Open Plinth">Open Plinth</option>
                                        <option value="Open Shed">Open Shed</option>
                                        <option value="Silo">Silo</option>
                                        <option value="Tank">Tank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <small class="req text-danger">* </small>
                                    <label for="latitude" class="control-label">Latitude</label>
                                    <input type="text" id="latitude" name="latitude" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <small class="req text-danger">* </small>
                                    <label for="longitude" class="control-label">Longitude</label>
                                    <input type="text" id="longitude" name="longitude" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <small class="req text-danger">* </small>
                                    <label for="center" class="control-label">Center</label>
                                    <select name="center" id="center" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <small class="req text-danger">* </small>
                                    <label for="address" class="control-label">Address</label>
                                    <input type="text" id="address" name="address" class="form-control" value="" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="AccountID">
                                    <small class="req text-danger">* </small>
                                    <label for="pincode" class="control-label">Pincode</label>
                                    <input type="text" id="pincode" name="pincode" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                                </div>
                            </div>
                            
                            <div class="col-md-2" id="length_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="length" class="control-label">Length</label>
                                    <input required type="text" id="length" name="length" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                </div>
                            </div>
                            <div class="col-md-2" id="breadth_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="breadth" class="control-label">Breadth</label>
                                    <input required type="text" id="breadth" name="breadth" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                </div>
                            </div>
                            <div class="col-md-2" id="height_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="height" class="control-label">Height</label>
                                    <input required type="text" id="height" name="height" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                </div>
                            </div>
                            <div class="col-md-2" id="diameter_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="diameter" class="control-label">Diameter</label>
                                    <input required type="text" id="diameter" name="diameter" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                </div>
                            </div>
                            <div class="col-md-2" id="capacity_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="capacity" class="control-label">Capacity (MT)</label>
                                    <input readonly type="text" id="capacity" name="capacity" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                </div>
                            </div>
                            <div class="col-md-2" id="no_of_chambers_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="no_of_chambers" class="control-label">No of Chambers</label>
                                    <input required type="text" id="no_of_chambers" name="no_of_chambers" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                </div>
                            </div>
                            <div class="col-md-2" id="no_of_floors_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="no_of_floors" class="control-label">No of Floors</label>
                                    <input required type="text" id="no_of_floors" name="no_of_floors" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                </div>
                            </div>
                            <div class="col-md-2" id="flooring_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="flooring" class="control-label">Flooring</label>
                                    <select required id="flooring" name="flooring" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <option value="Cemented">Cemented</option>
                                        <option value="Bricks">Bricks</option>
                                        <option value="Wooden">Wooden</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" id="shutters_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="shutter_door" class="control-label">Shutters/Doors</label>
                                    <select required id="shutter_door" name="shutter_door" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                                <div class="col-md-2" id="no_of_shutter_div">
                                    <div class="form-group" app-field-wrapper="AccoountName">
                                        <label for="no_of_shutter_door" class="control-label">No. of Shutters/Doors</label>
                                        <input required type="text" id="no_of_shutter_door" name="no_of_shutter_door" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                    </div>
                                </div>
                                <div class="col-md-2" id="no_of_lock_div">
                                    <div class="form-group" app-field-wrapper="AccoountName">
                                        <label for="no_of_lock" class="control-label">No. of Lock</label>
                                        <input readonly type="text" id="no_of_lock" name="no_of_lock" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                    </div>
                                </div>
                                <div class="col-md-2" id="lock_point_functional_div">
                                    <div class="form-group" app-field-wrapper="AccoountName">
                                        <label for="lock_point_functional" class="control-label">Lock Point Functional</label>
                                        <input required type="text" id="lock_point_functional" name="lock_point_functional" class="form-control" value="">
                                    </div>
                                </div>
                                <div class="col-md-2" id="windows_div">
                                    <div class="form-group" app-field-wrapper="AccoountName">
                                        <label for="windows" class="control-label">Windows</label>
                                        <select required id="windows" name="windows" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                            <option value="">Not Selected</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2" id="no_of_window_div">
                                    <div class="form-group" app-field-wrapper="no_of_window">
                                        <label for="no_of_window" class="control-label">No. of Windows</label>
                                        <input required type="text" id="no_of_window" name="no_of_window" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                    </div>
                                </div>
                                <div class="col-md-2" id="ventilator_div">
                                    <div class="form-group" app-field-wrapper="AccoountName">
                                        <label for="ventilator" class="control-label">Ventilator</label>
                                        <select required id="ventilator" name="ventilator" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                            <option value="">Not Selected</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2" id="no_of_ventilator_div">
                                    <div class="form-group" app-field-wrapper="no_of_ventilator">
                                        <label for="no_of_ventilator" class="control-label">No. of Ventilators</label>
                                        <input required type="text" id="no_of_ventilator" name="no_of_ventilator" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                    </div>
                                </div>
                            <div class="col-md-2" id="wall_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="wall" class="control-label">Wall</label>
                                    <select required id="wall" name="wall" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="Cemented/Bricks">Cemented/Bricks</option>
                                        <option value="Metallic Sheet">Metallic Sheet</option>
                                        <option value="Other">Other</option>
                                        <option value="Not Applicable">Not Applicable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" id="roof_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="roof" class="control-label">Roof</label>
                                    <select required id="roof" name="roof" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <option value="Metallic Sheet">Metallic Sheet</option>
                                        <option value="RCC">RCC</option>
                                        <option value="Tin Sheet">Tin Sheet</option>
                                        <option value="Other">Other</option>
                                        <option value="Not Applicable">Not Applicable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" id="leakage_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="leakage" class="control-label">Any Leakage</label>
                                    <select required id="leakage" name="leakage" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" id="plinth_height_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="plinth_height" class="control-label">Plinth Height</label>
                                    <input required type="text" id="plinth_height" name="plinth_height" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                </div>
                            </div>
                            <div class="col-md-2" id="drainage_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="drainage_channel" class="control-label">Drainage Channel</label>
                                    <select required id="drainage_channel" name="drainage_channel" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" id="electric_wire_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="electric_wire" class="control-label">Electric Wire Inside</label>
                                    <select required id="electric_wire" name="electric_wire" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" id="compound_wall_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="compound_wall" class="control-label">Compound Wall Available</label>
                                    <select required id="compound_wall" name="compound_wall" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" id="compound_gate_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="compound_gate" class="control-label">Compound Gate Available</label>
                                    <select required id="compound_gate" name="compound_gate" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" id="is_w_clean_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="is_warehouse_clean" class="control-label">Is Warehouse Clean</label>
                                    <select required id="is_w_clean" name="is_w_clean" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" id="cooling_system_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="cooling_system" class="control-label">Type of Cooling System</label>
                                    <select required id="cooling_system" name="cooling_system" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" id="insulation_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="insulation" class="control-label">Type of Insulation</label>
                                    <select required id="insulation" name="insulation" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" id="temprature_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="temprature" class="control-label">Temprature Maintained</label>
                                    <select required id="temprature" name="temprature" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <option value="Inside Chamber">Inside Chamber</option>
                                        <option value="Outside Chamber">Outside Chamber</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" id="insurance_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="insurance" class="control-label">Insurance</label>
                                    <select required id="insurance" name="insurance" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                                <div class="col-md-2" id="insurance_by_div">
                                    <div class="form-group" app-field-wrapper="AccoountName">
                                        <label for="insurance_by" class="control-label">Insurance Taken By</label>
                                        <select required id="insurance_by" name="insurance_by" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                            <option value="">Not Selected</option>
                                            <option value="Kirti">Kirti</option>
                                            <option value="Depositer">Depositer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2" id="insurance_compound_div">
                                    <div class="form-group" app-field-wrapper="AccoountName">
                                        <label for="insurance_compound" class="control-label">Insurance Company</label>
                                        <input required type="text" id="insurance_compound" name="insurance_compound" class="form-control" value="">
                                    </div>
                                </div>
                                <div class="col-md-2" id="policy_div">
                                    <div class="form-group" app-field-wrapper="AccoountName">
                                        <label for="policy" class="control-label">Policy No.</label>
                                        <input required type="text" id="policy" name="policy" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                    </div>
                                </div>
                                <div class="col-md-2" id="assured_sum_div">
                                    <div class="form-group" app-field-wrapper="AccoountName">
                                        <label for="assured_sum" class="control-label">Sum Assured</label>
                                        <input required type="text" id="assured_sum" name="assured_sum" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                    </div>
                                </div>
                                <div class="col-md-2" id="validity_div">
                                    <div class="form-group" app-field-wrapper="AccoountName">
                                        <label for="validity" class="control-label">Validity</label>
                                        <input required type="date" id="validity" name="validity" class="form-control" value="">
                                    </div>
                                </div>
                            <div class="col-md-2" id="security_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="security" class="control-label">Security Available</label>
                                    <select required id="security" name="security" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                                <div class="col-md-2" id="watchman_div">
                                    <div class="form-group" app-field-wrapper="AccoountName">
                                        <label for="watchman" class="control-label">Security Agency</label>
                                        <input required type="text" id="watchman" name="watchman" class="form-control" value="">
                                    </div>
                                </div>
                                <div class="col-md-2" id="security_type_div">
                                    <div class="form-group" app-field-wrapper="AccoountName">
                                        <label for="security_type" class="control-label">Security Type</label>
                                        <select required id="security_type" name="security_type" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                            <option value="">Not Selected</option>
                                            <option value="Employed">Employed</option>
                                            <option value="Outsource">Outsource</option>
                                            <option value="Roaming">Roaming</option>
                                        </select>
                                    </div>
                                </div>
                            <div class="col-md-2" id="weigh_bridge_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="weigh_bridge" class="control-label">Weigh Bridge Available</label>
                                    <select required id="weigh_bridge" name="weigh_bridge" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                                <div class="col-md-2" id="weigh_bridge_type_div">
                                    <div class="form-group" app-field-wrapper="AccoountName">
                                        <label for="weigh_bridge_type" class="control-label">Weigh Bridge Type</label>
                                        <select required id="weigh_bridge_type" name="weigh_bridge_type" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                            <option value="">Not Selected</option>
                                            <option value="Electronic">Electronic</option>
                                            <option value="Manual">Manual</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2" id="no_of_weighbridge_div">
                                    <div class="form-group" app-field-wrapper="AccoountName">
                                        <label for="no_of_weighbridge" class="control-label">No. of Weigh Bridge</label>
                                        <input required type="text" id="no_of_weighbridge" name="no_of_weighbridge" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                    </div>
                                </div>
                                <div class="col-md-2" id="distance_from_weighbridge_div">
                                    <div class="form-group" app-field-wrapper="AccoountName">
                                        <label for="distance_from_weighbridge" class="control-label">Nearest Weight Bridge (KM)</label>
                                        <input required type="text" id="distance_from_weighbridge" name="distance_from_weighbridge" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                    </div>
                                </div>
                            <div class="col-md-2" id="police_station_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="police_station" class="control-label">Nearest Police Station (KM)</label>
                                    <input required type="text" id="police_station" name="police_station" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                </div>
                            </div>
                            <div class="col-md-2" id="fire_station_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="fire_station" class="control-label">Nearest Fire Station (KM)</label>
                                    <input required type="text" id="fire_station" name="fire_station" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                </div>
                            </div>
                            <div class="col-md-2" id="pest_control_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="pest_control" class="control-label">Pest Control</label>
                                    <select required id="pest_control" name="pest_control" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" id="amenities_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="amenities" class="control-label">Amenities for CM Person</label>
                                    <select required id="amenities" name="amenities" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" id="note_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="note" class="control-label">Note</label>
                                    <input required type="text" id="note" name="note" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-2" id="manager_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="manager" class="control-label">WH Manager</label>
                                    <select required id="manager" name="manager" class="form-control selectpicker" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <?php foreach($managers as $key=>$val){ ?>
                                            <option value="<?php echo $val['firstname'] ?><?php echo $val['lastname']; ?>"><?php echo $val['firstname'] ?> <?php echo $val['lastname']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" id="remark_div">
                                <div class="form-group" app-field-wrapper="AccoountName">
                                    <label for="remark" class="control-label">Remark</label>
                                    <input required type="text" id="remark" name="remark" class="form-control" value="">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                
                                <!--<button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                                
                                <button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>-->
                                
                                <?php if (has_permission_new('WarehouseMaster', '', 'create')) {
                                ?>
                                    <button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                                <?php
                                }else{
                                ?>
                                    <button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
                                <?php
                                }?>
                                
                                <?php if (has_permission_new('WarehouseMaster', '', 'edit')) {
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
                                
                        <!------------ Modal ------------->
                        <div class="modal fade warehouse_List" id="warehouse_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                <div class="modal-header" style="padding:5px 10px;margin-bottom: 5px;">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Warehouse List</h4>
                                </div>
                                <div class="modal-body" style="padding:0px 5px !important;margin-top: 5px;">
                                    <div class="col-md-5">
                                        <?php if (has_permission_new('WarehouseMaster', '', 'export')) {
                                        ?>
                                        <a class="btn btn-default buttons-excel buttons-html2" tabindex="0"
                                            aria-controls="table-trial_bal_report" href="#" id="caexcel"
                                            style="float: left ! important;"><span>Export to Excel</span></a>
                                        <?php } ?>
                                        
                                        <?php if (has_permission_new('WarehouseMaster', '', 'print')) {
                                        ?>
                                        <button class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</button>
                                        <?php } ?>
                                    </div>
                                    <div class="table-StackListTable tableFixHead2">
                                        <table class="table table-striped table-bordered table-hover" id="table_warehouse_List" width="100%">
                                            <thead>
                                                <tr style="display:none;">
                                                    <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                                </tr>
                                                <tr>
                                                    <th id="sl" style="text-align:left;">WHID <span class="up_starting">  &#8593;</span><span class="down" style="display:none;"> &#8593;</span><span class="up" style="display:none;"> &#8595;</span></th>
                                                    <th style="text-align:left;">WH Name</th>
                                                    <th style="text-align:left;">Address</th>
                                                    <th style="text-align:left;">Center</th>
                                                    <th style="text-align:left;">Pincode</th>
                                                    <th style="text-align:left;">Assignment</th>
                                                    <th style="text-align:left;">Capacity (MT)</th>
                                                    <th style="text-align:left;">Structure</th>
                                                    <th style="text-align:left;">Latitude</th>
                                                    <th style="text-align:left;">Longitude</th>
                                                </tr>
                                            </thead>
                                            <tbody id="warehouse_List_body">
                                            
                                            </tbody>
                                        </table>   
                                    </div>
                                </div>
                                <div class="modal-footer" style="padding:0px;">
                                    <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: left;width: 100%;">
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

<script type="text/javascript">
    function printPage() {
        var html_filter_name = $('.report_for').html();
        var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">' + document.getElementsByTagName('table')[0].innerHTML + '</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
        heading_data += '<tr>';
        heading_data += '<td style="text-align:center;"colspan="3">Warehouse List Report</td>';
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
        url:"<?php echo admin_url(); ?>Warehouse/export_warehouseMaster",
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
<script>
    $('#WarehouseID').keyup(function () {
        $(this).val($(this).val().toUpperCase());
    });
</script>
<script>
    $(document).ready(function(){
       $('.saveBtn').show();
       $('.updateBtn').hide();
       $('.saveBtn2').show();
       $('.updateBtn2').hide();
       $('#diameter_div').hide();
       $('#plinth_height_div').hide();
       $('#cooling_system_div').hide();
       $('#insulation_div').hide();
       $('#temprature_div').hide();
       $('#no_of_lock_div').hide();
       $('#lock_point_functional_div').hide();
       $('#no_of_chambers_div').hide();
       $('#no_of_floors_div').hide();
       $('#no_of_shutter_div').hide();
       $('#no_of_window_div').hide();
       $('#no_of_ventilator_div').hide();
       $('#insurance_by_div').hide();
       $('#insurance_compound_div').hide();
       $('#policy_div').hide();
       $('#assured_sum_div').hide();
       $('#validity_div').hide();
       $('#watchman_div').hide();
       $('#security_type_div').hide();
       $('#weigh_bridge_type_div').hide();
       $('#no_of_weighbridge_div').hide();
       $('#distance_from_weighbridge_div').hide();
       $('#weigh_bridge_type_div').hide();
    });    
</script>
<script>
    function myFunction2() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table_warehouse_List");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            td1 = tr[i].getElementsByTagName("td")[1];
            td2 = tr[i].getElementsByTagName("td")[2];
            td3 = tr[i].getElementsByTagName("td")[3];
            td4 = tr[i].getElementsByTagName("td")[4];
            td5 = tr[i].getElementsByTagName("td")[5];
            td6 = tr[i].getElementsByTagName("td")[6];
            td7 = tr[i].getElementsByTagName("td")[7];
            td8 = tr[i].getElementsByTagName("td")[8];
            td9 = tr[i].getElementsByTagName("td")[9];
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
        }else if(td4){
            txtValue = td4.textContent || td4.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
        }else if(td5){
            txtValue = td5.textContent || td5.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
        }else if(td6){
            txtValue = td6.textContent || td6.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
        }else if(td7){
            txtValue = td7.textContent || td7.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
        }else if(td8){
            txtValue = td8.textContent || td8.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
        }else if(td9){
            txtValue = td9.textContent || td9.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
        }else{
           tr[i].style.display = "none";
        } 
        }
     }}}}}
    }
      }
    }     
  }
}
}
 </script>
<script>
    $('#no_of_shutter_door').keyup(function(){
       var no_of_shutter_door = $('#no_of_shutter_door').val();
       var val = parseInt(no_of_shutter_door)*2;
       $('#no_of_lock').val(val);
    });
</script>
<script>
    $('#structure_type').change(function(){
       var value = $(this).val();
       if(value === "Silo" || value === "Tank"){
           $('div.col-md-2').show();
           $('#no_of_chambers_div').hide();
           $('#no_of_floors_div').hide();
           $('#length_div').hide();
           $('#breadth_div').hide();
           $('#flooring_div').hide();
           $('#shutters_div').hide();
           $('#no_of_shutter_div').hide();
           $('#windows_div').hide();
           $('#no_of_window_div').hide();
           $('#ventilator_div').hide();
           $('#no_of_ventilator_div').hide();
           $('#wall_div').hide();
           $('#roof_div').hide();
           $('#electric_wire_div').hide();
           $('#leakage_div').hide();
           $('#plinth_height_div').hide();
           $('#drainage_div').hide();
           $('#cooling_system_div').hide();
           $('#insulation_div').hide();
           $('#temprature_div').hide();
           $('#no_of_lock_div').hide();
           $('#lock_point_functional_div').hide();
           $('#insurance_by_div').hide();
           $('#insurance_compound_div').hide();
           $('#policy_div').hide();
           $('#assured_sum_div').hide();
           $('#validity_div').hide();
           $('#watchman_div').hide();
           $('#security_type_div').hide();
           $('#weigh_bridge_type_div').hide();
           $('#no_of_weighbridge_div').hide();
           $('#distance_from_weighbridge_div').hide();
       }
       else if(value === "Cold Storage"){
           $('div.col-md-2').show();
           $('#no_of_chambers_div').show();
           $('#no_of_floors_div').show();
           $('#windows_div').hide();
           $('#diameter_div').hide();
           $('#flooring_div').hide();
           $('#shutters_div').hide();
           $('#no_of_shutter_div').hide();
           $('#no_of_lock_div').hide();
           $('#lock_point_functional_div').hide();
           $('#windows_div').hide();
           $('#wall_div').hide();
           $('#roof_div').hide();
           $('#plinth_height_div').hide();
           $('#insurance_by_div').hide();
           $('#insurance_compound_div').hide();
           $('#policy_div').hide();
           $('#assured_sum_div').hide();
           $('#validity_div').hide();
           $('#watchman_div').hide();
           $('#security_type_div').hide();
           $('#weigh_bridge_type_div').hide();
           $('#no_of_weighbridge_div').hide();
           $('#distance_from_weighbridge_div').hide();
           $('#no_of_window_div').hide();
           $('#ventilator_div').hide();
           $('#no_of_ventilator_div').hide();
       }
       else if(value === "Open Plinth"){
           $('div.col-md-2').show();
           $('#flooring_div').hide();
           $('#no_of_chambers_div').hide();
           $('#no_of_floors_div').hide();
           $('#height_div').hide();
           $('#diameter_div').hide();
           $('#shutters_div').hide();
           $('#no_of_shutter_div').hide();
           $('#windows_div').hide();
           $('#no_of_window_div').hide();
           $('#ventilator_div').hide();
           $('#no_of_ventilator_div').hide();
           $('#wall_div').hide();
           $('#roof_div').hide();
           $('#electric_wire_div').hide();
           $('#leakage_div').hide();
           $('#cooling_system_div').hide();
           $('#insulation_div').hide();
           $('#temprature_div').hide();
           $('#no_of_lock_div').hide();
           $('#lock_point_functional_div').hide();
           $('#insurance_by_div').hide();
           $('#insurance_compound_div').hide();
           $('#policy_div').hide();
           $('#assured_sum_div').hide();
           $('#validity_div').hide();
           $('#watchman_div').hide();
           $('#security_type_div').hide();
           $('#weigh_bridge_type_div').hide();
           $('#no_of_weighbridge_div').hide();
           $('#distance_from_weighbridge_div').hide();
       }
       else if(value === "Open Shed"){
           $('div.col-md-2').show();
           $('#plinth_height_div').hide();
           $('#flooring_div').hide();
           $('#no_of_chambers_div').hide();
           $('#no_of_floors_div').hide();
           $('#height_div').hide();
           $('#diameter_div').hide();
           $('#shutters_div').hide();
           $('#no_of_shutter_div').hide();
           $('#windows_div').hide();
           $('#no_of_window_div').hide();
           $('#ventilator_div').hide();
           $('#no_of_ventilator_div').hide();
           $('#wall_div').hide();
           $('#roof_div').hide();
           $('#electric_wire_div').hide();
           $('#leakage_div').hide();
           $('#cooling_system_div').hide();
           $('#insulation_div').hide();
           $('#temprature_div').hide();
           $('#no_of_lock_div').hide();
           $('#lock_point_functional_div').hide();
           $('#insurance_by_div').hide();
           $('#insurance_compound_div').hide();
           $('#policy_div').hide();
           $('#assured_sum_div').hide();
           $('#validity_div').hide();
           $('#watchman_div').hide();
           $('#security_type_div').hide();
           $('#weigh_bridge_type_div').hide();
           $('#no_of_weighbridge_div').hide();
           $('#distance_from_weighbridge_div').hide();
       }
       else{
           $('div.col-md-2').show();
           $('#plinth_height_div').hide();
           $('#cooling_system_div').hide();
           $('#insulation_div').hide();
           $('#temprature_div').hide();
           $('#drainage_div').hide();
           $('#no_of_chambers_div').hide();
           $('#no_of_floors_div').hide();
           $('#leakage_div').hide();
           $('#diameter_div').hide();
           $('#no_of_shutter_div').hide();
           $('#no_of_lock_div').hide();
           $('#lock_point_functional_div').hide();
           $('#no_of_window_div').hide();
           $('#no_of_ventilator_div').hide();
           $('#insurance_by_div').hide();
           $('#insurance_compound_div').hide();
           $('#policy_div').hide();
           $('#assured_sum_div').hide();
           $('#validity_div').hide();
           $('#watchman_div').hide();
           $('#security_type_div').hide();
           $('#weigh_bridge_type_div').hide();
           $('#no_of_weighbridge_div').hide();
           $('#distance_from_weighbridge_div').hide();
       }
    });
</script>    
<script>
    $('#windows').change(function(){
       var value = $(this).val();
       if(value == "1"){
           $('#no_of_window_div').show();
       }else{
          $('#no_of_window_div').hide();
       }
    });
</script>  
<script>
    $('#ventilator').change(function(){
       var ventilator = $('#ventilator').val();
       if(ventilator == '1'){
           $('#no_of_ventilator_div').show();
       }else{
           $('#no_of_ventilator_div').hide();
       }
    });
</script> 
<script>
    $('#insurance').change(function(){
       var insurance = $('#insurance').val();
       if(insurance == '1'){
           $('#insurance_by_div').show();
           $('#insurance_compound_div').show();
           $('#policy_div').show();
           $('#assured_sum_div').show();
           $('#validity_div').show();
       }else{
           $('#insurance_by_div').hide();
           $('#insurance_compound_div').hide();
           $('#policy_div').hide();
           $('#assured_sum_div').hide();
           $('#validity_div').hide();
       }
    });
</script>    
<script>
    $('#security').change(function(){
       var security = $('#security').val();
       if(security == '1'){
           $('#watchman_div').show();
           $('#security_type_div').show();
       }else{
           $('#watchman_div').hide();
           $('#security_type_div').hide();
       }
    });
</script>
<script>
    $('#weigh_bridge').on('change',function(){
       var weigh_bridge = $('#weigh_bridge').val();
       if(weigh_bridge == '1'){
           $('#weigh_bridge_type_div').show();
           $('#no_of_weighbridge_div').show();
           $('#distance_from_weighbridge_div').hide();
       }else if(weigh_bridge == ''){
           $('#weigh_bridge_type_div').hide();
           $('#no_of_weighbridge_div').hide();
           $('#distance_from_weighbridge_div').hide();
       }else if(weigh_bridge == '0'){
           $('#weigh_bridge_type_div').hide();
           $('#no_of_weighbridge_div').hide();
           $('#distance_from_weighbridge_div').show();
       }
    });
</script>
<script>
    $('#shutter_door').on('change',function(){
       var shutter = $('#shutter_door').val();
       if(shutter == '1'){
           $('#no_of_shutter_div').show();
           $('#no_of_lock_div').show();
           $('#lock_point_functional_div').show();
       }else{
           $('#no_of_shutter_div').hide();
           $('#no_of_lock_div').hide();
           $('#lock_point_functional_div').show();
       }
    });
    
</script>
<script>
    $('#breadth').keyup(function(){
       var len = $('#length').val();
       var br = $('#breadth').val();
       var cap = (parseInt(len)*parseInt(br))/5;
       if((len == '') || (br == '')){
           $('#capacity').val('0');
       }
       else{
           $('#capacity').val(cap);
       }
       
    });
</script>
<script>
    $('#length').keyup(function(){
       var len = $('#length').val();
       var br = $('#breadth').val();
       var cap = (parseInt(len)*parseInt(br))/5;
       if((len == '') || (br == '')){
           $('#capacity').val('0');
       }
       else{
           $('#capacity').val(cap);
       }
    });
</script>
<script>
    $('.saveBtn').click(function(){
        var AccountID = $('#WarehouseID').val();
        var w_name = $('#WarehouseName').val();
        var address = $('#address').val();
        var center = $('#center :selected').val();
        var pincode = $('#pincode').val();
        var latitude = $('#latitude').val();
        var longitude = $('#longitude').val();
        var type_of_assignment = $('#type_of_assignment :selected').val();
        var structure = $('#structure_type :selected').val();
        var length = $('#length').val();
        var breadth = $('#breadth').val();
        var height = $('#height').val();
        var diameter = $('#diameter').val();
        var w_capacity = $('#capacity').val();
        var no_of_chambers = $('#no_of_chambers').val();
        var no_of_floors = $('#no_of_floors').val();
        var flooring = $('#flooring :selected').val();
        var shutter_door = $('#shutter_door :selected').val();
        var no_of_shutter_door = $('#no_of_shutter_door').val();
        var no_of_lock = $('#no_of_lock').val();
        var lock_functional = $('#lock_point_functional').val();
        var windows = $('#windows :selected').val();
        var no_of_window = $('#no_of_window').val();
        var ventilator = $('#ventilator :selected').val();
        var no_of_ventilator = $('#no_of_ventilator').val();
        var wall = $('#wall :selected').val();
        var roof = $('#roof :selected').val();
        var leakage = $('#leakage').val();
        var plinth_height = $('#plinth_height').val();
        var drainage_channel = $('#drainage_channel :selected').val();
        var electric_wire = $('#electric_wire :selected').val();
        var compound_wall = $('#compound_wall :selected').val();
        var compound_gate = $('#compound_gate :selected').val();
        var is_w_clean = $('#is_w_clean :selected').val();
        var cooling_system = $('#cooling_system :selected').val();
        var insulation = $('#insulation :selected').val();
        var temprature = $('#temprature :selected').val();
        var insurance = $('#insurance :selected').val();
        var insurance_taken_by = $('#insurance_by').val();
        var insurance_compound = $('#insurance_compound').val();
        var policy_no = $('#policy').val();
        var assured_sum = $('#assured_sum').val();
        var validity = $('#validity').val();
        var security = $('#security :selected').val();
        var watchman_name = $('#watchman').val();
        var security_type = $('#security_type :selected').val();
        var weigh_bridge = $('#weigh_bridge :selected').val();
        var weighbridge_type = $('#weigh_bridge_type :selected').val();
        var weighbridge_distance = $('#distance_from_weighbridge').val();
        var no_of_weighbridge = $('#no_of_weighbridge').val();
        var police_station = $('#police_station').val();
        var fire_station = $('#fire_station').val();
        var pest_control = $('#pest_control :selected').val();
        var amenities = $('#amenities :selected').val();
        var note = $('#note').val();
        var w_manager = $('#manager :selected').val();
        var remark = $('#remark').val();
        
        if((AccountID != '') && (w_name != '') && (address != '') && (pincode != '') && (latitude != '') && (longitude != '') && (type_of_assignment != '') && (structure_type != '')){
        
            $.ajax({
                    url:"<?php echo admin_url(); ?>Warehouse/SaveWarehouse",
                    dataType:"JSON",
                    method:"POST",
                    data:{
                        AccountID: AccountID,
                        w_name : w_name,
                        address : address,
                        center : center,
                        pincode : pincode,
                        latitude : latitude,
                        longitude : longitude,
                        type_of_assignment : type_of_assignment,
                        structure : structure,
                        length : length,
                        breadth : breadth,
                        height : height,
                        diameter : diameter,
                        w_capacity : w_capacity,
                        no_of_chambers : no_of_chambers,
                        no_of_floors : no_of_floors,
                        flooring : flooring,
                        shutter_door : shutter_door,
                        no_of_shutter_door : no_of_shutter_door,
                        no_of_lock : no_of_lock,
                        lock_functional : lock_functional,
                        windows : windows,
                        no_of_window : no_of_window,
                        ventilator : ventilator,
                        no_of_ventilator : no_of_ventilator,
                        wall : wall,
                        roof : roof,
                        leakage : leakage,
                        plinth_height : plinth_height,
                        drainage_channel : drainage_channel,
                        electric_wire : electric_wire,
                        compound_wall : compound_wall,
                        compound_gate : compound_gate,
                        is_w_clean : is_w_clean,
                        cooling_system : cooling_system,
                        insulation : insulation,
                        temprature : temprature,
                        insurance : insurance,
                        insurance_taken_by : insurance_taken_by,
                        insurance_compound : insurance_compound,
                        policy_no : policy_no,
                        assured_sum : assured_sum,
                        validity : validity,
                        security : security,
                        watchman_name : watchman_name,
                        security_type : security_type,
                        weigh_bridge : weigh_bridge,
                        weighbridge_type : weighbridge_type,
                        weighbridge_distance : weighbridge_distance,
                        no_of_weighbridge : no_of_weighbridge,
                        police_station : police_station,
                        fire_station : fire_station,
                        pest_control : pest_control,
                        amenities : amenities,
                        note : note,
                        w_manager : w_manager,
                        remark : remark,
                    },
                    success:function(data){
                        if(data == true){
                            $(':input').val('');
                            $('.selectpicker').val('').selectpicker('refresh');
                            $('#structure_type').val('Dry Warehouse').selectpicker('refresh');
                            $('#structure_type').change();
                            $('.saveBtn').show();
                            $('.updateBtn').hide();
                            $('.saveBtn2').show();
                            $('.updateBtn2').hide();
                            alert('Record created successfully...');
                        }
                    }
            }); 
        }
        else{
            alert("Enter Required Details !")
        }
    });
</script>
<script>
    $('.updateBtn').click(function(){
        var AccountID = $('#WarehouseID').val();
        var w_name = $('#WarehouseName').val();
        var address = $('#address').val();
        var center = $('#center :selected').val();
        var pincode = $('#pincode').val();
        var latitude = $('#latitude').val();
        var longitude = $('#longitude').val();
        var type_of_assignment = $('#type_of_assignment :selected').val();
        var structure = $('#structure_type :selected').val();
        var length = $('#length').val();
        var breadth = $('#breadth').val();
        var height = $('#height').val();
        var diameter = $('#diameter').val();
        var w_capacity = $('#capacity').val();
        var no_of_chambers = $('#no_of_chambers').val();
        var no_of_floors = $('#no_of_floors').val();
        var flooring = $('#flooring :selected').val();
        var shutter_door = $('#shutter_door :selected').val();
        var no_of_shutter_door = $('#no_of_shutter_door').val();
        var no_of_lock = $('#no_of_lock').val();
        var lock_functional = $('#lock_point_functional').val();
        var windows = $('#windows :selected').val();
        var no_of_window = $('#no_of_window').val();
        var ventilator = $('#ventilator :selected').val();
        var no_of_ventilator = $('#no_of_ventilator').val();
        var wall = $('#wall :selected').val();
        var roof = $('#roof :selected').val();
        var leakage = $('#leakage').val();
        var plinth_height = $('#plinth_height').val();
        var drainage_channel = $('#drainage_channel :selected').val();
        var electric_wire = $('#electric_wire :selected').val();
        var compound_wall = $('#compound_wall :selected').val();
        var compound_gate = $('#compound_gate :selected').val();
        var is_w_clean = $('#is_w_clean :selected').val();
        var cooling_system = $('#cooling_system :selected').val();
        var insulation = $('#insulation :selected').val();
        var temprature = $('#temprature :selected').val();
        var insurance = $('#insurance :selected').val();
        var insurance_taken_by = $('#insurance_by').val();
        var insurance_compound = $('#insurance_compound').val();
        var policy_no = $('#policy').val();
        var assured_sum = $('#assured_sum').val();
        var validity = $('#validity').val();
        var security = $('#security :selected').val();
        var watchman_name = $('#watchman').val();
        var security_type = $('#security_type :selected').val();
        var weigh_bridge = $('#weigh_bridge :selected').val();
        var weighbridge_type = $('#weigh_bridge_type :selected').val();
        var weighbridge_distance = $('#distance_from_weighbridge').val();
        var no_of_weighbridge = $('#no_of_weighbridge').val();
        var police_station = $('#police_station').val();
        var fire_station = $('#fire_station').val();
        var pest_control = $('#pest_control :selected').val();
        var amenities = $('#amenities :selected').val();
        var note = $('#note').val();
        var w_manager = $('#manager :selected').val();
        var remark = $('#remark').val();

        $.ajax({
                url:"<?php echo admin_url(); ?>Warehouse/UpdateWarehouse",
                dataType:"JSON",
                method:"POST",
                data:{
                        AccountID : AccountID,
                        w_name : w_name,
                        address : address,
                        center : center,
                        pincode : pincode,
                        latitude : latitude,
                        longitude : longitude,
                        type_of_assignment : type_of_assignment,
                        structure : structure,
                        length : length,
                        breadth : breadth,
                        height : height,
                        diameter : diameter,
                        w_capacity : w_capacity,
                        no_of_chambers:no_of_chambers,
                        no_of_floors:no_of_floors,
                        flooring : flooring,
                        shutter_door : shutter_door,
                        no_of_shutter_door : no_of_shutter_door,
                        no_of_lock : no_of_lock,
                        lock_functional : lock_functional,
                        windows : windows,
                        no_of_window : no_of_window,
                        ventilator : ventilator,
                        no_of_ventilator : no_of_ventilator,
                        wall : wall,
                        roof : roof,
                        leakage : leakage,
                        plinth_height : plinth_height,
                        drainage_channel : drainage_channel,
                        electric_wire : electric_wire,
                        compound_wall : compound_wall,
                        compound_gate : compound_gate,
                        is_w_clean : is_w_clean,
                        cooling_system : cooling_system,
                        insulation : insulation,
                        temprature : temprature,
                        insurance : insurance,
                        insurance_taken_by : insurance_taken_by,
                        insurance_compound : insurance_compound,
                        policy_no : policy_no,
                        assured_sum : assured_sum,
                        validity : validity,
                        security : security,
                        watchman_name : watchman_name,
                        security_type : security_type,
                        weigh_bridge : weigh_bridge,
                        weighbridge_type : weighbridge_type,
                        weighbridge_distance : weighbridge_distance,
                        no_of_weighbridge : no_of_weighbridge,
                        police_station : police_station,
                        fire_station : fire_station,
                        pest_control : pest_control,
                        amenities : amenities,
                        note : note,
                        w_manager : w_manager,
                        remark : remark,
                    },
                success:function(data){
                    if(data == true){
                        $(':input').val('');
                        $('.selectpicker').val('').selectpicker('refresh');
                        $('#structure_type').val('Dry Warehouse').selectpicker('refresh');
                        $('#structure_type').change();
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                        alert('Record Updated successfully...');
                    }
                }
        });        
    });
</script>
<script>
    $(document).ready(function(){
       $.ajax({
            url : "<?php echo admin_url(); ?>Warehouse/GetCenter",
            type: "post",
            data: {
            },
            beforeSend: function () {
                $('select[name=center]').val('').selectpicker('refresh');
            },
            success: function(data){
                $('select[name=center]').html(data).selectpicker('refresh');
            }
        });
    });
</script>
<script>
    function fill_data(AccountID){
        $('#warehouse_List').modal('hide');
    }
</script>
<script>
    $("#WarehouseID").dblclick(function()
    {
        $('#warehouse_List').modal('show');
        $('#warehouse_List').on('shown.bs.modal', function () {
            $('#myInput1').val('');
            $('#myInput1').focus();
            var AccountID = '';
            $.ajax({
                url:"<?php echo admin_url(); ?>Warehouse/GetAllWarehouse",
                  //dataType:"JSON",
                method:"POST",
                cache: false,
                data:{AccountID:AccountID,},
                success:function(data){
                    if(empty(data)){
                            
                    }else{
                        $("#warehouse_List_body").html(data);
                        $('.get_AccountID').click(function(){ 
                            AccountID = $(this).attr("data-id");
                            $.ajax({
                                url: "<?php echo admin_url(); ?>Warehouse/getSingleWarehouse",
                                dataType:"JSON",
                                method:"POST",
                                data:{
                                    AccountID:AccountID,
                                },
                                success:function(data){
                                    if(empty(data)){
                                    }else{
                                        $('#WarehouseID').val(data.AccountID);
                                        $('#WarehouseName').val(data.w_name);
                                        $('#address').val(data.address);
                                        $('#center').val(data.center).selectpicker('refresh');
                                        $('#pincode').val(data.pincode);
                                        $('#latitude').val(data.latitude);
                                        $('#longitude').val(data.longitude);
                                        $('#type_of_assignment').val(data.type_of_assignment);
                                        $('#structure_type').val(data.structure);
                                        $('#length').val(data.length);
                                        $('#breadth').val(data.breadth);
                                        $('#height').val(data.height);
                                        $('#diameter').val(data.diameter);
                                        $('#capacity').val(data.w_capacity);
                                        $('#no_of_chambers').val(data.no_of_chambers);
                                        $('#no_of_floors').val(data.no_of_floors);
                                        $('#flooring').val(data.flooring);
                                        $('#shutter_door').val(data.shutter_door);
                                        if(data.shutter_door == "1"){
                                            $('#no_of_shutter_div').show();
                                            $('#no_of_shutter_door').val(data.no_of_shutter_door);
                                        }
                                        
                                        $('#no_of_lock').val(data.no_of_lock);
                                        $('#lock_point_functional').val(data.lock_functional);
                                        $('#windows').val(data.windows).selectpicker('refresh');
                                        if(data.windows == "1"){
                                            $('#no_of_window_div').show();
                                            $('#no_of_window').val(data.no_of_window);
                                        }     
                                        $('#ventilator').val(data.ventilator).selectpicker('refresh');
                                        if(data.ventilator == "1"){
                                            $('#no_of_ventilator_div').show();
                                            $('#no_of_ventilator').val(data.no_of_ventilator);
                                        }
                                        $('#wall').val(data.wall);
                                        $('#roof').val(data.roof);
                                        $('#leakage').val(data.leakage);
                                        $('#plinth_height').val(data.plinth_height);
                                        $('#drainage_channel').val(data.drainage_channel);
                                        $('#electric_wire').val(data.wire_inside);
                                        $('#compound_wall').val(data.compound_wall);
                                        $('#compound_gate').val(data.compound_gate);
                                        $('#is_w_clean').val(data.is_w_clean);
                                        $('#cooling_system').val(data.cooling_system).selectpicker('refresh');
                                        $('#insulation').val(data.insulation).selectpicker('refresh');
                                        $('#temprature').val(data.temprature).selectpicker('refresh');
                                        $('#insurance').val(data.insurance);
                                        if(data.insurance == "1"){
                                            $('#insurance_by_div').show();
                                            $('#insurance_compound_div').show();
                                            $('#policy_div').show();
                                            $('#assured_sum_div').show();
                                            $('#validity_div').show();
                                            $('#insurance_by').val(data.insurance_taken_by);
                                            $('#insurance_compound').val(data.insurance_compound);
                                            $('#policy').val(data.policy_no);
                                            $('#assured_sum').val(data.assured_sum);
                                            $('#validity').val(data.validity);
                                        }
                                        
                                        $('#security').val(data.security);
                                        if(data.security == "1"){
                                            $('#watchman_div').show();
                                            $('#security_type_div').show();
                                            $('#watchman').val(data.watchman_name);
                                            $('#security_type').val(data.security_type);
                                        }
                                        $('#weigh_bridge').val(data.weigh_bridge);
                                        if(data.weigh_bridge == "1"){
                                            $('#weigh_bridge_type_div').show();
                                            $('#no_of_weighbridge_div').show();
                                            $('#weigh_bridge_type').val(data.weighbridge_type);
                                            $('#no_of_weighbridge').val(data.no_of_weighbridge);
                                        }else if(data.weigh_bridge == "0"){
                                            $('#distance_from_weighbridge_div').show();
                                            $('#distance_from_weighbridge').val(data.weighbridge_distance);
                                        }       
                                        $('#police_station').val(data.police_station);
                                        $('#fire_station').val(data.fire_station);
                                        $('#pest_control').val(data.pest_control);
                                        $('#amenities').val(data.amenities);
                                        $('#note').val(data.note);
                                        $('#manager').val(data.w_manager);
                                        $('#remark').val(data.remark);
                                        
                                        $('#structure_type').change();
                                        
                                        $('.selectpicker').selectpicker('refresh');
                                        $('.saveBtn').hide();
                                        $('.updateBtn').show();
                                        $('.saveBtn2').hide();
                                        $('.updateBtn2').show();
                                    }
                                },
                            });
                            $('#warehouse_List').modal('hide');
                        })
                    }
                }
            });
        })
    });
</script>
<script>
    $(".cancelBtn").click(function(){
        $(':input').val('');
        $('.selectpicker').val('').selectpicker('refresh'); 
        $('#structure_type').val('Dry Warehouse').selectpicker('refresh');
        $('#structure_type').change();
        $('#shutter_door_noshow,#window_noshow,#ventilator_noshow,#insurance_noshow,#security_noshow,#weighbridge_noshow,#weighbridge_no').hide();
        $('.saveBtn').show();
        $('.updateBtn').hide();
        $('.saveBtn2').show();
        $('.updateBtn2').hide();
    });
</script>
<script>
    $("#WarehouseID").focus(function(){
        $(':input').val('');
        $('.selectpicker').val('').selectpicker('refresh');
        $('#structure_type').val('Dry Warehouse').selectpicker('refresh');
        $('#structure_type').change();
        $('#shutter_door_noshow,#window_noshow,#ventilator_noshow,#insurance_noshow,#security_noshow,#weighbridge_noshow,#weighbridge_no').hide();
        $('.saveBtn').show();
        $('.updateBtn').hide();
        $('.saveBtn2').show();
        $('.updateBtn2').hide();
    });
</script>
<script>
    $("#WarehouseID").on('blur', function(e) {
            var keyCode = e.keyCode || e.which;
            var AccountID = $('#WarehouseID').val();
            if(AccountID !== ""){
                e.preventDefault(); 
                $.ajax({
                    url: "<?php echo admin_url(); ?>Warehouse/getSingleWarehouse",
                    dataType:"JSON",
                    method:"POST",
                    data:{
                        AccountID:AccountID,
                    },
                    success:function(data){
                        if(empty(data)){
                        }else{
                            //alert('hello');
                            
                            $('#WarehouseID').val(data.AccountID);
                            $('#WarehouseName').val(data.w_name);
                            $('#address').val(data.address);
                            $('#center').val(data.center).selectpicker('refresh');
                            $('#pincode').val(data.pincode);
                            $('#latitude').val(data.latitude);
                            $('#longitude').val(data.longitude);
                            $('#type_of_assignment').val(data.type_of_assignment);
                            $('#structure_type').val(data.structure);
                            $('#length').val(data.length);
                            $('#breadth').val(data.breadth);
                            $('#height').val(data.height);
                            $('#diameter').val(data.diameter);
                            $('#capacity').val(data.w_capacity);
                            $('#no_of_chambers').val(data.no_of_chambers);
                            $('#no_of_floors').val(data.no_of_floors);
                            $('#flooring').val(data.flooring);
                            $('#shutter_door').val(data.shutter_door);
                            if(data.shutter_door == "1"){
                                $('#no_of_shutter_div').show();
                                $('#no_of_shutter_door').val(data.no_of_shutter_door);
                            }
                            
                            $('#no_of_lock').val(data.no_of_lock);
                            $('#lock_point_functional').val(data.lock_functional);
                            $('#windows').val(data.windows).selectpicker('refresh');
                            if(data.windows == "1"){
                                $('#no_of_window_div').show();
                                $('#no_of_window').val(data.no_of_window);
                            }     
                            $('#ventilator').val(data.ventilator).selectpicker('refresh');
                            if(data.ventilator == "1"){
                                $('#no_of_ventilator_div').show();
                                $('#no_of_ventilator').val(data.no_of_ventilator);
                            }
                            $('#wall').val(data.wall);
                            $('#roof').val(data.roof);
                            $('#leakage').val(data.leakage);
                            $('#plinth_height').val(data.plinth_height);
                            $('#drainage_channel').val(data.drainage_channel);
                            $('#electric_wire').val(data.wire_inside);
                            $('#compound_wall').val(data.compound_wall);
                            $('#compound_gate').val(data.compound_gate);
                            $('#is_w_clean').val(data.is_w_clean);
                            $('#cooling_system').val(data.cooling_system).selectpicker('refresh');
                            $('#insulation').val(data.insulation).selectpicker('refresh');
                            $('#temprature').val(data.temprature).selectpicker('refresh');
                            $('#insurance').val(data.insurance);
                            if(data.insurance == "1"){
                                $('#insurance_by_div').show();
                                $('#insurance_compound_div').show();
                                $('#policy_div').show();
                                $('#assured_sum_div').show();
                                $('#validity_div').show();
                                $('#insurance_by').val(data.insurance_taken_by);
                                $('#insurance_compound').val(data.insurance_compound);
                                $('#policy').val(data.policy_no);
                                $('#assured_sum').val(data.assured_sum);
                                $('#validity').val(data.validity);
                            }
                            
                            $('#security').val(data.security);
                            if(data.security == "1"){
                                $('#watchman_div').show();
                                $('#security_type_div').show();
                                $('#watchman').val(data.watchman_name);
                                $('#security_type').val(data.security_type);
                            }
                            $('#weigh_bridge').val(data.weigh_bridge);
                            if(data.weigh_bridge == "1"){
                                $('#weigh_bridge_type_div').show();
                                $('#no_of_weighbridge_div').show();
                                $('#weigh_bridge_type').val(data.weighbridge_type);
                                $('#no_of_weighbridge').val(data.no_of_weighbridge);
                            }else if(data.weigh_bridge == "0"){
                                $('#distance_from_weighbridge_div').show();
                                $('#distance_from_weighbridge').val(data.weighbridge_distance);
                            }       
                            $('#police_station').val(data.police_station);
                            $('#fire_station').val(data.fire_station);
                            $('#pest_control').val(data.pest_control);
                            $('#amenities').val(data.amenities);
                            $('#note').val(data.note);
                            $('#manager').val(data.w_manager);
                            $('#remark').val(data.remark);        
                            
                            $('#structure_type').change();
                            //$('#WarehouseID').blur();        
                            $('.selectpicker').selectpicker('refresh');
                            $('.saveBtn').hide();
                            $('.updateBtn').show();
                            $('.saveBtn2').hide();
                            $('.updateBtn2').show();
                        }
                    },
                });
            }
        });
</script>
<style>

#table_warehouse_List td:hover {
    cursor: pointer;
}
#table_warehouse_List tr:hover {
    background-color: #ccc;
}
.col-md-2{
    margin-bottom:20px;
}

    
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>