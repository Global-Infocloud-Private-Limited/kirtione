<?php defined('BASEPATH') or exit('No direct script access allowed');
echo theme_head_view();
get_template_part($navigationEnabled ? 'navigation' : '');
?>
<style>
    .row {
        margin-right: 0px; 
        margin-left: 0px; 
    }
    .navbar-default {
        height: 65px;
        margin-bottom: 0px;
    }
    .navbar a.navbar-brand {
        padding: 7px 5px 7px 15px;
        height: auto;
        margin-right: 15px;
        margin-top: 7px;
    }
    .hr_style {
        margin-top: 0px;
        margin-bottom: 5px;
        border: 0.5px solid;
        color: #d81b60;
    }
    
</style>
<div id="wrapper">
   <div id="content" style="20px 20px 20px 20px">
      <!--<div class="container">-->
         <div class="row">
            <?php get_template_part('alerts'); ?>
         </div>
      <!--</div>-->
      <?php if(isset($knowledge_base_search)){ ?>
         <?php get_template_part('knowledge_base/search'); ?>
      <?php } ?>
      <!--<div class="container">-->
         <?php hooks()->do_action('customers_content_container_start'); ?>
         <div class="row">
            <?php
            if(is_client_logged_in() && $subMenuEnabled && !isset($knowledge_base_search)){ ?>
               <!--<ul class="submenu customer-top-submenu">
                  <?php hooks()->do_action('before_customers_area_sub_menu_start'); ?>
                  <li class="customers-top-submenu-files"><a href="<?php echo site_url('clients/files'); ?>"><i class="fa fa-file" aria-hidden="true"></i> <?php echo _l('customer_profile_files'); ?></a></li>
                  <li class="customers-top-submenu-calendar"><a href="<?php echo site_url('clients/calendar'); ?>"><i class="fa fa-calendar-minus-o" aria-hidden="true"></i> <?php echo _l('calendar'); ?></a></li>
                  <?php hooks()->do_action('after_customers_area_sub_menu_end'); ?>
               </ul>-->
               <div class="clearfix"></div>
            <?php } ?>
            <?php echo theme_template_view(); ?>
         </div>
      <!--</div>-->
   </div>
   <?php
   echo theme_footer_view();
   ?>
</div>
<?php
/* Always have app_customers_footer() just before the closing </body>  */
app_customers_footer();
   /**
   * Check for any alerts stored in session
   */
   app_js_alerts();
   ?>
</body>
</html>
