<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="widget<?php if (!is_admin()) {
    echo ' hide';
} ?>" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo "Access Permission "; ?>">

    <?php if (is_admin()) { ?>
        <div class="row">
            <div class="col-md-10">
                <div class="panel_s">
                    <div class=" onoffswitch2 panel-body padding-10">
                        <div class="widget-dragger"></div>
                        <p class="padding-5">
                            <?php echo 'Access Permission For Trader Farmer And Sales'; ?>
                        </p>  
                        <table>
                            <tr>
                                <td>
                                    <div class="toggle-container">
                                        <label class="toggle-label">Kirti purchase Trader</label>
                                    </div>
                                </td>
                                
                                <?php if($kirtiPurchasetraderCheck == true){
                                    $kirtitraderToogle = 'checked';
                                    $value = '1';
                                }else{
                                    $kirtitraderToogle = '';
                                    $value = '0';
                                } 
                                ?>
                                <td>
                                    <div class="onoffswitchT">
                                        <input type="checkbox" data-switch-url=<?php echo site_url('admin/SaleDashboard/AccessPermissionOnOff'); ?> name="onoffswitch"
                                            class="onoffswitchT-checkbox" <?php echo $kirtitraderToogle; ?> value="<?php echo $value; ?>" id="kirtiTraderToggle" data-id="K_P_T">
                                        <label class="onoffswitchT-label"  for="kirtiTraderToggle"></label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="toggle-container">
                                        <label class="toggle-label">Kirti purchase Farmer</label>
                                    </div>
                                </td>
                                <?php if($kirtiPurchasefarmerCheck == true){
                                    $kirtifarmerToogle = 'checked';
                                    $value = '1';
                                }else{
                                    $kirtifarmerToogle = '';
                                    $value = '0';
                                } 
                                ?>
                                <td>
                                    <div class="onoffswitchF">
                                        <input type="checkbox" data-switch-url= <?php echo site_url('admin/SaleDashboard/AccessPermissionOnOff'); ?> name="onoffswitch"
                                            class="onoffswitchF-checkbox"  <?php echo $kirtifarmerToogle; ?> value="<?php echo $value; ?>" id="kirtiFarmerToggle" data-id="K_P_F">
                                        <label class="onoffswitchF-label" for="kirtiFarmerToggle"></label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="toggle-container">
                                        <label class="toggle-label">Kirti Sales</label>
                                    </div>
                                </td>
                                <?php if($kirtiPurchasesaleCheck == true){
                                    $kirtisaleToogle = 'checked';
                                    $value = '1';
                                }else{
                                    $kirtisaleToogle = '';
                                    $value = '0';
                                } 
                                ?>
                                <td>
                                    <div class="onoffswitchS">
                                        <input type="checkbox" data-switch-url=<?php echo site_url('admin/SaleDashboard/AccessPermissionOnOff'); ?> name="onoffswitch"
                                            class="onoffswitchS-checkbox" <?php echo $kirtisaleToogle; ?> value="<?php echo $value; ?>" id="kirtiSalesToggle" data-id="K_P_S">
                                        <label class="onoffswitchS-label" for="kirtiSalesToggle"></label>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        
                        <hr class="hr-panel-heading-dashboard">
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

        

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

</style>