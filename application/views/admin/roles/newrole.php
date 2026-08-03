<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    #number_day {
    width: 30px!important;
    height: 20px!important;
}
.tableFixHead2 {
    overflow: auto;
    max-height: 60vh;
    width: 100%;
    position: relative;
    top: 0px;
}
.d-none{
    display:none;
}
th {
    background: #50607b;
    color: #fff !important;
}
</style>
<div id="wrapper">
<div class="content">
   <div class="row">
      <div class="col-md-8"  >
         <div class="panel_s">
            <div class="panel-body">
               <!--<h4 class="no-margin">-->
               <!--   <!?php echo $title; ?>-->
               <!--</h4>-->
               <!--<hr class="hr-panel-heading" />-->
               <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Admin</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b><?php echo $title; ?></b></li>
    							
    							
    						</ol>
    						<?php if(isset($role)){ ?>
    						<a href="<?php echo admin_url('roles/rolenew'); ?>" class="btn btn-success pull-right mbot2 display-block" style="margin-top: -40px;"><?php echo _l('new_role'); ?></a>
    						<?php } ?>
    					</nav>
    					<hr class="hr_style">
               
               <?php if(isset($role)){ ?>
               <!--<a href="<?php echo admin_url('roles/rolenew'); ?>" class="btn btn-success pull-right mbot20 display-block"><?php echo _l('new_role'); ?></a>-->
               <div class="clearfix"></div>
               <?php } ?>
               <?php echo form_open($this->uri->uri_string()); ?>
               <?php if(isset($role)){ ?>
                    <?php if(total_rows(db_prefix().'staff',array('role'=>$role->roleid)) > 0){ ?>
               <div class="alert alert-warning bold">
                  <?php echo _l('change_role_permission_warning'); ?>
                  <div class="checkbox">
                     <input type="checkbox" name="update_staff_permissions" id="update_staff_permissions">
                     <label for="update_staff_permissions"><?php echo _l('role_update_staff_permissions'); ?></label>
                  </div>
               </div>
                    <?php } ?>
               <?php } ?>
               
               
               
               
               <?php $attrs = (isset($role) ? array() : array('autofocus'=>true)); ?>
               <?php $value = (isset($role) ? $role->name : ''); ?>
               
               
               <?php echo render_input('name','role_add_edit_name',$value,'text',$attrs); ?>
               
               <br>
                <?php
                  $permissionsData = [ 'funcData' => ['role'=> isset($role) ? $role : null ] ];
                  $this->load->view('admin/staff/role_permissions', $permissionsData);
                  
                  
               ?>
               <hr />
                  <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                  <?php echo form_close(); ?>
            </div>
         </div>
      </div>
      <?php if(isset($role_staff)) { ?>
      <div class="col-md-5 d-none">
         <div class="panel_s">
            <div class="panel-body">
               <!--<h4 class="no-margin">-->
               <!--   <?php echo _l('staff_which_are_using_role'); ?>-->
               <!--</h4>-->
               <!--<hr class="hr-panel-heading" />-->
               <nav aria-label="breadcrumb" >
    						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
    							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
    							<li class="breadcrumb-item active text-capitalize"><b>Admin</b></li>
    							<li class="breadcrumb-item active" aria-current="page"><b><?php echo _l('staff_which_are_using_role'); ?></b></li>
    						</ol>
    					</nav>
    					<hr class="hr_style">
    					
               <div class="table-responsive">
                  <table class="table dt-table" >
                     <thead>
                        <tr>
                           <th><?php echo _l('staff_dt_name'); ?></th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php 
                        
                        foreach($role_staff as $staff) { ?>
                        <tr>
                           <td>
                              <?php
                                 echo '<a href="' . admin_url('staff/profile/' . $staff['staffid']) . '">' . staff_profile_image($staff['staffid'], [
                                   'staff-profile-image-small',
                                 ]) . '</a>';
                                 echo ' <a href="' . admin_url('staff/member/' . $staff['staffid']) . '">' . $staff['firstname'] . ' ' . $staff['lastname'] . '</a>';
                                 ?>
                           </td>
                        </tr>
                        <?php } ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
      <?php } ?>
   </div>
</div>
<?php init_tail(); ?>
<script>

//   $(document).ready(function () {
//     $('.capability').on('change', function () {
//         var capability = $(this).val(); 
//         var feature = $(this).attr('id').split('_')[0]; 

//         if (capability === 'create' || capability === 'edit') {
//             if ($(this).is(':checked')) {
//                 $('#' + feature + '_view').prop('checked', true);
//             }
//         }

//         if (capability === 'create' || capability === 'edit') {
//             var createChecked = $('#' + feature + '_create').is(':checked');
//             var editChecked = $('#' + feature + '_edit').is(':checked');

//             if (!createChecked && !editChecked) {
//                 $('#' + feature + '_view').prop('checked', false);
//             }
//         }
//     });
// });


   $(function(){
     appValidateForm($('form'),{name:'required'});
   });
   
   
   

</script>
</body>
</html>
