<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="searchh2" style="display:none;">Please wait while fetching data.</div>
                                <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                                <div class="searchh4" style="display:none;">Please wait while updating data.</div>
                            </div>
                            <br>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="commodity">
                                    <small class="req text-danger">* </small>
                                    <label for="commodity" class="control-label">Commodity</label>
                                    <select id="commodity" name="commodity" class="form-control selectpicker" data-live-search="true">
                                        <option value="">Not Selected</option>
                                        <?php foreach($commodity as $key=>$value): ?>
                                            <option value="<?php echo $value['commodity']; ?>"><?php echo $value['commodity']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                        <br>
                        <div class="row">
                            <table class="table table-striped table-bordered" id="filter_table">
                                <thead >
                                    <tr>
                                        <td>&nbsp;</td>
                                        <?php foreach($center as $key=>$value): ?>
                                            <td><?php echo $value['CenterName']; ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody >
                                        <?php foreach($commodity as $key=>$value): ?>
                                            <tr>
                                                <td><?php echo $value['commodity']; ?></td>
                                                <?php foreach($center as $key=>$val): ?>
                                                    <td><input data-center="<?php echo $val['CenterName'] ?>" data-commodity="<?php echo $value['commodity'] ?>" type="text" value=""></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <div class="row" style="margin:auto;width:100%;">
                            <button class="btn btn-info" id="submit" >Submit</button>
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
    $('input').focus(function(){
       var center = $(this).data('center');
       var commodity = $(this).data('commodity');
       alert(center + commodity);
       $('input').blur();
    });
});
</script>