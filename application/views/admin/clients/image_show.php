<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-10">
        <div class="panel_s">
          <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                            //echo "<pre>";
                            //print_r($image_details);
                            $img_file = 'bank_image';
                            if($Type == "bank_cheque"){
                                $Image = $image_details["cheque_image"];
                            }else if($Type == "farm_image"){
                                $Image = $image_details["saatbara_image"];
                            }else if($Type == "Aadhaar_profile"){
                                $Image = $image_details["aadhar_image"];
                            }
                            $src = 'data: '.mime_content_type($img_file).';base64,'.$Image;
                        ?>
                        <img src="<?php echo $src; ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>