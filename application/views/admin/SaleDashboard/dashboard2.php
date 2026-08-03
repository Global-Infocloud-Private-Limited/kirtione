<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
  <style>
      .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover, .navbar-pills.nav-tabs>li>a:focus, .navbar-pills.nav-tabs>li>a:hover{
          background-color: #02a9f4;
    color: #fff;
    border-radius: 1px;
      }
  </style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                <?php
                    foreach($AllCenter as $value){
                        ?>
                            <input id="<?php echo $value['CenterID']?>" name="CenterIDs" onclick="CheckCenter();" class="chk" type="checkbox" value="<?php echo $value['CenterID']?>"><label style="margin-right: 10px;margin-left: 5px;" for="<?php echo $value['CenterName']?>"><?php echo $value['CenterName']?></label>
                        <?php
                    }
                ?>
                </div>
                
                <div class="col-md-12">
                    <hr>
                    <?php
                        foreach($CommodityGroup as $value){
                            ?>
                                <input id="<?php echo $value['name']?>" name="CommodityIDs" onclick="CheckCommodity();" class="chk" type="checkbox"  value="<?php echo $value['id']?>"><label style="margin-right: 10px;margin-left: 5px;" for="<?php echo $value['name']?>"><?php echo $value['name']?></label>
                            <?php
                        }
                    ?>
                    <br>
                    <br>
                </div>
                
            </div>
            <ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active">
					<a href="#TraderTab" aria-controls="TraderTab" style="font-size:22px;padding:5px; 22px;" role="tab" data-toggle="tab">
						<?php echo "Trader"; ?>
					</a>
				</li>
				<li role="presentation">
					<a href="#FarmerTab" aria-controls="FarmerTab" role="tab" style="font-size:22px;padding:5px 22px;;" data-toggle="tab">
						<?php echo "Farmer"; ?>
					</a>
				</li>
			</ul>
			
			<div class="tab-content">
			    <!--// Trader Tab details-->
				<div role="tabpanel" class="tab-pane active" id="TraderTab">
					<div class="row" id="TraderData">
					    
					</div>
				</div>
				
				<!--// Farmer Tab details-->
				<div role="tabpanel" class="tab-pane " id="FarmerTab">
					<div class="row" id="FarmerData">
					    
					</div>
				</div>
				
			</div>
			
          </div>
</div>
</div>
</div>
</div>
</div>
<style>
    .table-daily_report { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
.table-daily_report thead th { position: sticky; top: 0; z-index: 1; }
.table-daily_report tbody th { position: sticky; left: 0; }

/* Just common table stuff. Really. */
.table-daily_report table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
.table-daily_report th     { background: #50607b;color: #fff !important; }
</style>


<?php init_tail(); ?>
<!--new update -->
<script>
    function CheckCenter() {
        // Get Checked Center
        yourCenterIDsArray = Array();    
        $("input:checkbox[name=CenterIDs]:checked").each(function(){
            yourCenterIDsArray.push($(this).val());
        });
        let yourCenterIdsString = yourCenterIDsArray.toString();
        
        // Get Checked Commodity
        yourCommodity = Array();    
        $("input:checkbox[name=CommodityIDs]:checked").each(function(){
            yourCommodity.push($(this).val());
        });
        let yourCommodityString = yourCommodity.toString();
        
        load_data(yourCommodityString,yourCenterIdsString);
    }
</script>

<script>
    function CheckCommodity() {
        // Get Checked Commodity
        yourCommodity = Array();    
        $("input:checkbox[name=CommodityIDs]:checked").each(function(){
            yourCommodity.push($(this).val());
        });
        let yourCommodityString = yourCommodity.toString();
        
        // Get Checked Center
        yourCenterIDsArray = Array();    
        $("input:checkbox[name=CenterIDs]:checked").each(function(){
            yourCenterIDsArray.push($(this).val());
        });
        let yourCenterIdsString = yourCenterIDsArray.toString();
        
        load_data(yourCommodityString,yourCenterIdsString);
    }
</script>

<script>
    function load_data(yourCommodityString,yourCenterIdsString)
    {   
        $('#TraderData').html('');
        if(yourCommodityString == "" || yourCenterIdsString == ""){
            
        }else{
            $.ajax({
                url:"<?php echo admin_url(); ?>SaleDashboard/load_data",
                method:"POST",
                dataType:'json',
                data:{yourCommodityString:yourCommodityString, yourCenterIdsString:yourCenterIdsString},
                beforeSend: function () {
                    $('#searchh2').css('display','block');
                },
                complete: function () {
                    $('#searchh2').css('display','none');
                },
                success:function(data){
                    $('#TraderData').html(data.Trader);
                    $('#FarmerData').html(data.Farmer);
                }
            });
        }
    }
</script>

<script type="text/javascript" language="javascript" >
$(document).ready(function(){
    $('#search_data').on('click',function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var msg = "Sales Report "+from_date +" To " + to_date;
	    $(".report_for").text(msg);
        load_data(from_date,to_date);
    });
    
});
</script>

  