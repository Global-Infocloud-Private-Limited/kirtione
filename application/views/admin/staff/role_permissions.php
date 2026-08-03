<!--<div class="table-responsive">-->
<div class="table-responsive tableFixHead2">
   <!--<table class="table table-bordered roles no-margin">-->
   <table class="table table-bordered roles no-margin  tableFixHead2">
      <thead>
         <tr>
            <th>MainMenu</th>
            <th>SubMenu</th>
        <!--<th>view(own)</th>-->
            <th>View</th>
            <th>add</th>
            <th>edit</th>
            <th>delete</th>
            <th>Print</th>
            <th>Export</th>
            <!--<th>days</th>-->
         </tr>
      </thead>
      <tbody>
            <?php
            if (isset($member)) {
                $is_admin = is_admin($member->staffid);
            }
        
            foreach (get_available_staff_permissions($funcData) as $feature => $permission) { ?>
                <tr data-name="<?php echo $feature; ?>" data-main="<?php echo $permission['main_menu']; ?>">
                    <td><b><?php echo $permission['main_menu']; ?></b></td>
                    <td><?php echo $permission['name']; ?></td>
                    <?php
                      if(isset($permission['before'])){
                       echo $permission['before'];
                      }
                      ?>
                    <?php foreach ($permission['capabilities'] as $capability => $name) {
                        $checked = '';
                        $disabled = '';
        
                        if ($capability == 'view' && strpos($name, '(Global)') !== false) {
                            $name = 'View';  
                        }
                        
                        if ($capability == 'view') {
                            if ((isset($member) && (staff_can('create', $feature, $member->staffid) || staff_can('edit', $feature, $member->staffid))) ||
                                (isset($role) && (has_role_permission($role->roleid, 'create', $feature) || has_role_permission($role->roleid, 'edit', $feature)))) {
                                $checked = ' checked ';
                                // $disabled = ' disabled ';  
                            }
                        }
                        if ((isset($member) && staff_can($capability, $feature, $member->staffid)) ||
                            (isset($role) && has_role_permission($role->roleid, $capability, $feature))) {
                            $checked = ' checked ';
                        }
                        
        
                        if ((isset($is_admin) && $is_admin) ||
                            (is_array($name) && isset($name['not_applicable']) && $name['not_applicable']) ||
                            (
                                ($capability == 'view_own' || $capability == 'view') &&
                                array_key_exists('view_own', $permission['capabilities']) &&
                                array_key_exists('view', $permission['capabilities']) &&
                                ((isset($member) && staff_can(($capability == 'view' ? 'view_own' : 'view'), $feature, $member->staffid)) ||
                                (isset($role) && has_role_permission($role->roleid, ($capability == 'view' ? 'view_own' : 'view'), $feature))
                                )
                            )
                        ) {
                            $disabled = ' disabled ';
                        } 
                        // else if (
                        //     (isset($member) && staff_can($capability, $feature, $member->staffid)) ||
                        //     (isset($role) && has_role_permission($role->roleid, $capability, $feature))
                        // ) {
                        //     $checked = ' checked ';
                        // }
                    ?>
                    <td>
                        <div class="checkbox">
                            <input
                                
                                <?php if ($capability == 'view') { ?> data-can-view <?php } ?>
                                <?php if ($capability == 'view_own') { ?> data-can-view-own <?php } ?>
                                <?php if (is_array($name) && isset($name['not_applicable']) && $name['not_applicable']) { ?> data-not-applicable="true" <?php } ?>
                                type="checkbox"
                                <?php echo $checked; ?>
                                class="capability"
                                id="<?php echo $feature . '_' . $capability; ?>"
                                name="permissions[<?php echo $feature; ?>][]"
                                value="<?php echo $capability; ?>"
                                <?php echo $disabled; ?>
                            >
                            <label for="<?php echo $feature . '_' . $capability; ?>">
                                <?php echo !is_array($name) ? $name : $name['name']; ?>
                            </label>
                            <?php
                            if (isset($permission['help']) && array_key_exists($capability, $permission['help'])) {
                                echo '<i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . $permission['help'][$capability] . '"></i>';
                            }
                            ?>
                        </div>
                    </td>
                    
                    <?php } ?>
                    
                    
                    <!--<td><input type="text" name="permissions" id="number_day" class="form-control" value="" style="width: 40px;"></td>-->
                    <?php
                      if(isset($permission['after'])){
                        echo $permission['after'];
                      }
                      ?>
                </tr>
            <?php } ?>
        </tbody>
   </table>
</div>
