<?php

defined('BASEPATH') or exit('No direct script access allowed');

class SaleDashboard extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('SaleDashboard_model');
    }

    /* Get all invoices in case user go on index page */
    public function index($id = '')
    {
        if (!has_permission_new('sell_dashboard', '', 'view')) {
            access_denied('sell_dashboard');
        }
        close_setup_menu();
        $data['title']                = "Sell Dashboard";
        $data['AllCenter'] = $this->SaleDashboard_model->GetAllCenter();
        $data['CenterWiseItem'] = $this->SaleDashboard_model->GetCenterWiseItem();
        $data['ItemWiseCenterWisePurchase'] = $this->SaleDashboard_model->GetItemWiseCenterWisePurchase();
        //$data['ItemWiseCenterWiseTodayPurchase'] = $this->SaleDashboard_model->GetItemWiseCenterWiseTodayPurchase();
        $data['ItemWiseCenterWiseCurrentRate'] = $this->SaleDashboard_model->GetItemWiseCenterWiseCurrentRate();
        $data['ItemWiseCenterWiseAvgRate'] = $this->SaleDashboard_model->GetItemWiseCenterWiseAvgRate();
        $data['ItemWiseCenterWiseCurrentAvgRate'] = $this->SaleDashboard_model->GetItemWiseCenterWiseCurrentAvgRate();
        /*echo "<pre>";
        print_r($data['ItemWiseCenterWiseAvgRate']);
        die;*/
        $this->load->view('admin/SaleDashboard/dashboard', $data);
    }
    
    public function New($id = '')
    {
        if (!has_permission_new('sell_dashboard', '', 'view')) {
            access_denied('sell_dashboard');
        }
        $this->load->model('rate_master_model');
        $data['title']                = "Sell Dashboard";
        $data['AllCenter'] = $this->SaleDashboard_model->GetAllCenter();
        $data['CommodityGroup'] = $this->rate_master_model->GetItemGroup_Staff_wise();
        /*echo "<pre>";
        print_r($data['ItemWiseCenterWiseAvgRate']);
        die;*/
        $this->load->view('admin/SaleDashboard/dashboard2', $data);
    }
    
    public function load_data_by_groupwise_centerwise()
    {
        $GroupID = $this->input->post('$GroupID');
        $GroupName = $this->input->post('$GroupName');
        $CenterID = $this->input->post('$CenterID');
        $CenterName = $this->input->post('$CenterName');
        $Type = $this->input->post('$Type');
        $GroupWiseCenterWiseItem = $this->SaleDashboard_model->GroupWiseCenterWiseItem($GroupID,$CenterID);
        $CurrentRateItemWise = $this->SaleDashboard_model->CurrentRateItemWise($GroupID,$CenterID,$Type);
        $AvgCurrentRateItemWise = $this->SaleDashboard_model->AvgCurrentRateItemWise($GroupID,$CenterID,$Type);
        $clsQtyItemWise = $this->SaleDashboard_model->clsQtyItemWise($GroupID,$CenterID,$Type);
        $GetItemWiseCenterWiseTodayPurchase = $this->SaleDashboard_model->GetItemWiseCenterWiseTodayPurchase($GroupID,$CenterID,$Type);
        
        $html = '';
            $html .= '<div class="col-md-12">';
            $html .= '<div class="table-daily_report">';
            $html .= '<table class="tree table table-striped table-bordered table-daily_report" id="ItemWiseDetails" width="100%">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th colspan="6" style="text-align:center;background-color: #f07217;font-size: 14px;"><b>'.$CenterName.'</b></th>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<th colspan="6" style="text-align:center;background-color: #036412;font-size: 14px;"><b>'.$GroupName.'</b></th>';
            $html .= '</tr>';
            $html .= '<tr>';
            //$html .= '<th style="text-align:center;background-color: #2d2d2d;font-size: 12px;"><b>ItemID</b></th>';
            $html .= '<th style="text-align:center;background-color: #2d2d2d;font-size: 12px;"><b>ItemName</b></th>';
            $html .= '<th style="text-align:center;background-color: #2d2d2d;font-size: 12px;"><b>Closing Stock</b></th>';
            $html .= '<th style="text-align:center;background-color: #2d2d2d;font-size: 12px;"><b>FIFO Basis W.Avg Rate</b></th>';
            $html .= '<th style="text-align:center;background-color: #2d2d2d;font-size: 12px;"><b>Todays Purchase Qty</b></th>';
            $html .= '<th style="text-align:center;background-color: #2d2d2d;font-size: 12px;"><b>Todays Avg Rate</b></th>';
            $html .= '<th style="text-align:center;background-color: #2d2d2d;font-size: 12px;"><b>Current Rate</b></th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            foreach($GroupWiseCenterWiseItem as $Key=>$value){
                $AvgRate = 0;
                $Weight = 0;
                $Amount = 0;
                foreach($AvgCurrentRateItemWise as $AvgrateKey=>$Avgratevalue){
                    if($Avgratevalue["CenterID"]== $value["CenterID"] && $Avgratevalue["ItemID"]== $value["ItemID"]){
                        $Weight += $Avgratevalue["NetWeight"];
                        $Amount += $Avgratevalue["Amount"];
                    }
                }
                if($Amount > 0 && $Weight > 0){
                    $AvgRate = $Amount / $Weight;
                }
                
                $ClsQty = 0;
                foreach($clsQtyItemWise as $ClsKey=>$Clsvalue){
                    if($Clsvalue["CenterID"]== $value["CenterID"] && $Clsvalue["ItemID"]== $value["ItemID"]){
                        $ClsQty = $Clsvalue["NetWeight"];
                    }
                }
            
                $CurrentRate = 0;
                foreach($CurrentRateItemWise as $RateKey=>$Ratevalue){
                    if($value["ItemID"] == $Ratevalue["ItemID"] && $value["CenterID"] == $Ratevalue["CenterID"]){
                        $CurrentRate = $Ratevalue["Rate"];
                    }
                }
                
                $TodaysPur = 0;
                foreach($GetItemWiseCenterWiseTodayPurchase as $PurKey=>$Purvalue){
                    if($value["ItemID"] == $Purvalue["ItemID"] && $value["CenterID"] == $Purvalue["CenterID"]){
                        $TodaysPur = $Purvalue["NetWeight"];
                    }
                }
                $html .= '<tr>';
                //$html .= '<td>'.$value["ItemID"].'</td>';
                $html .= '<td>'.$value["ItemName"].'</td>';
                $html .= '<td style="text-align:right;">'.number_format($ClsQty, 2, ".", ",").'</td>';
                $html .= '<td></td>';
                $html .= '<td style="text-align:right;">'.number_format($TodaysPur, 2, ".", ",").'</td>';
                $html .= '<td style="text-align:right;">'.number_format($AvgRate, 2, ".", ",").'</td>';
                $html .= '<td style="text-align:right;">'.number_format($CurrentRate, 2, ".", ",").'</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';
        echo json_encode($html);
    }
    public function load_data()
    {
        if($this->input->post('yourCommodityString') == ""){
            $yourCommodityArray = array();
        }else{
            $yourCommodityArray = explode(",",$this->input->post('yourCommodityString'));
        }
        
        if($this->input->post('yourCenterIdsString') == ""){
            $yourCenterIDsArray = array();
        }else{
            $yourCenterIDsArray = explode(",",$this->input->post('yourCenterIdsString'));
        }
        $AllCenter = $this->SaleDashboard_model->GetAllItemWiseCenter($yourCommodityArray,$yourCenterIDsArray);
        $AllCenterWiseItemWiseTrading = $this->SaleDashboard_model->GetAllItemWiseCenterTradingStatus($yourCommodityArray,$yourCenterIDsArray);
        $TraderRate = $this->SaleDashboard_model->BaseItemRate($yourCommodityArray,$yourCenterIDsArray,'T');
        $FarmerRate = $this->SaleDashboard_model->BaseItemRate($yourCommodityArray,$yourCenterIDsArray,'F');
        $NCDexRate = $this->SaleDashboard_model->BaseItemRate($yourCommodityArray,$yourCenterIDsArray,'N');
        $CompRate = $this->SaleDashboard_model->BaseItemRate($yourCommodityArray,$yourCenterIDsArray,'C');
        $MandiRate = $this->SaleDashboard_model->BaseItemRate($yourCommodityArray,$yourCenterIDsArray,'M');
        $TraderTodaysCnfTradeQtyGroupWise = $this->SaleDashboard_model->TodaysCnfTradeQtyGroupWise($yourCommodityArray,$yourCenterIDsArray,"3");
        $FarmerTodaysCnfTradeQtyGroupWise = $this->SaleDashboard_model->TodaysCnfTradeQtyGroupWise($yourCommodityArray,$yourCenterIDsArray,"1");
        
        $TraderclsQtyGroupWise = $this->SaleDashboard_model->clsQtyGroupWise($yourCommodityArray,$yourCenterIDsArray,"3");
        $FarmerclsQtyGroupWise = $this->SaleDashboard_model->clsQtyGroupWise($yourCommodityArray,$yourCenterIDsArray,"1");
        
        $TraderWeightedAvgRateGroupWise = $this->SaleDashboard_model->weightedAvgRateGroupWise($yourCommodityArray,$yourCenterIDsArray,"3");
        $FarmerWeightedAvgRateGroupWise = $this->SaleDashboard_model->weightedAvgRateGroupWise($yourCommodityArray,$yourCenterIDsArray,"1");
        
        /*echo "<pre>";
        echo json_encode($AllCenter);
        die;*/
        
        
        $html = '';
        $html2 = '';
        $i = 1;
        $ii = 1;
        foreach($AllCenter as $Key=>$value){
            if($i == "7"){
                $i = 1;
                $html .= '<div class="clearfix"></div>';
            }
            $AvgRate = 0;
            foreach($TraderRate as $RateKey=>$Ratevalue){
                if($Ratevalue["CenterID"]== $value["CenterID"] && $Ratevalue["ItemID"]== $value["SubGroupID"]){
                    $AvgRate = $Ratevalue["Rate"];
                }
            }
            
            $NCDEXAvgRate = 0;
            foreach($NCDEXAvgRateGroupWise as $RateKey=>$Ratevalue){
                if($Ratevalue["CenterID"]== $value["CenterID"] && $Ratevalue["subgroup_id"]== $value["SubGroupID"]){
                    $NCDEXAvgRate = $Ratevalue["AvgRate"];
                }
            }
            
            $CompAvgRate = 0;
            foreach($CompAvgRateGroupWise as $RateKey=>$Ratevalue){
                if($Ratevalue["CenterID"]== $value["CenterID"] && $Ratevalue["subgroup_id"]== $value["SubGroupID"]){
                    $CompAvgRate = $Ratevalue["AvgRate"];
                }
            }
            
            $TradeQty = 0;
            foreach($TraderTodaysCnfTradeQtyGroupWise as $TradeKey=>$Tradevalue){
                if($Tradevalue["CenterID"]== $value["CenterID"] && $Tradevalue["GroupCode"]== $value["SubGroupID"]){
                    $TradeQty = $Tradevalue["TotalQty"];
                }
            }
            
            $ClsQty = 0;
            $WtAvgRate = 0;
            foreach($TraderclsQtyGroupWise as $ClsKey=>$Clsvalue){
                if($Clsvalue["CenterID"]== $value["CenterID"] && $Clsvalue["GroupCode"]== $value["SubGroupID"]){
                    $ClsQty = $Clsvalue["NetWeight"];
                }
            }
            
            $Weight = 0;
            $Amount = 0;
            foreach($TraderWeightedAvgRateGroupWise as $AvgrateKey=>$Avgratevalue){
                if($Avgratevalue["CenterID"]== $value["CenterID"] && $Avgratevalue["GroupCode"]== $value["SubGroupID"]){
                    $Weight += $Avgratevalue["NetWeight"];
                    $Amount += $Avgratevalue["Amount"];
                }
            }
            if($Amount > 0 && $Weight > 0){
                $WtAvgRate = $Amount / $Weight;
            }
            $NCDEXRate = 0;
            foreach($NCDexRate as $NCDEXKey=>$NCDEXvalue){
                if($NCDEXvalue["CenterID"]== $value["CenterID"] && $NCDEXvalue["ItemID"]== $value["SubGroupID"]){
                    $NCDEXRate = $NCDEXvalue["Rate"];
                }
            }
            
            $CompRate1 = 0;
            $CompRate2 = 0;
            foreach($CompRate as $CKey=>$Cvalue){
                if($Cvalue["CenterID"]== $value["CenterID"] && $Cvalue["ItemID"]== $value["SubGroupID"]){
                    if($CompRate1 == "0"){
                        $CompRate1 = $Cvalue["Rate"];
                    }else if($CompRate1 > 0 && $CompRate2 == "0"){
                        $CompRate2 = $Cvalue["Rate"];
                    }
                }
            }
            
            $MandiRate1 = 0;
            $MandiRate2 = 0;
            foreach($MandiRate as $MKey=>$Mvalue){
                if($Mvalue["CenterID"]== $value["CenterID"] && $Mvalue["ItemID"]== $value["SubGroupID"]){
                    if($MandiRate1 == "0"){
                        $MandiRate1 = $Mvalue["Rate"];
                    }else if($MandiRate1 > 0 && $MandiRate2 == "0"){
                        $MandiRate2 = $Mvalue["Rate"];
                    }
                }
            }
            
            $TraderTradingOn = '';
            foreach($AllCenterWiseItemWiseTrading as $TradingKey=>$Tradingvalue){
                if($Tradingvalue["CenterID"]== $value["CenterID"] && $Tradingvalue["GroupCode"]== $value["SubGroupID"]){
                    if($TraderTradingOn == "" || $TraderTradingOn == "N"){
                        $TraderTradingOn = $Tradingvalue["TradeOnOff"];
                    }
                }
            }
            $checkedTrader = '';
            if($TraderTradingOn == "Y"){
                $checkedTrader = "checked";
            }
            
            $html .= '<div class="col-md-2">';
            $html .= '<div class="table-daily_report">';
            $html .= '<table class="tree table table-striped table-bordered table-daily_report" id="GroupWiseDetails" width="100%">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th colspan="3" style="text-align:center;background-color: #f07217;font-size: 14px;"><b>'.$value["CenterName"].'</b></th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            $html .= '<tr style="background-color: #333;color: #fff;">';
            $Type = "T";
            $html .= '<td colspan="3" onclick = "GetDetails('."'".$value["SubGroupID"]."'".', '."'".$value["CenterID"]."'".', '."'".$value["name"]."'".', '."'".$value["CenterName"]."'".', '."'".$Type."'".')"  style="text-align:center;background-color: #333;color:#fff;font-size: 13px;" data-id="'.$value['SubGroupID'].'"  data-toggle="modal" data-target="#DasboardDetails"><b>'.$value["name"].'</b></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="background-color: #ad0020;color: #fff;"></td>';
            $html .= '<td style="background-color: #ad0020;color: #fff;"><b>LIVE</b></td>';
            $html .= '<td style="background-color: #ad0020;color: #fff;"><b>Total</b></td>';
            $html .= '</tr>';
            $html .= '<tr style="background-color: #e4c1c1;">';
            $html .= '<td style="color: #000;font-size: 12px;"><b>Rate</b></td>';
            $html .= '<td style="color: #000;font-size: 12px;" onclick = "RateMaster(3)" title="Live Current rate of the perticular location"><b>'. number_format($AvgRate, 2, ".", ",").'</b></td>';
            $html .= '<td style="color: #000;font-size: 12px;" title="Weighted Avg rate of closing stock"><b>'. number_format($WtAvgRate, 2, ".", ",").'</b></td>';
            $html .= '</tr>';
            $html .= '<tr style="background-color: #c1c2ef;">';
            $html .= '<td style="color: #000;font-size: 12px;"><b>Qty</b></td>';
            $html .= '<td style="color: #000;font-size: 12px;" onclick = "TradeCnf()" title="Todays total Confirmed trade Qty"><b>'. number_format($TradeQty, 2, ".", ",").'</b></td>';
            $html .= '<td style="color: #000;font-size: 12px;" title="Closing Stock"><b>'. number_format($ClsQty, 2, ".", ",").'</b></td>';
            $html .= '</tr>';
            $html .= '<tr style="background-color: #e4c1c1;">';
            $html .= '<td style="color: #000;font-size: 12px;"><b>Competitor</b></td>';
            $html .= '<td style="color: #000;font-size: 12px;" onclick = "RateMaster(2)" title="competitor Rate 1"><b>'. number_format($CompRate1, 2, ".", ",").'</b></td>';
            $html .= '<td style="color: #000;font-size: 12px;" onclick = "RateMaster(2)" title="competitor Rate 2"><b>'. number_format($CompRate2, 2, ".", ",").'</b></td>';
            $html .= '</tr>';
            $html .= '<tr style="background-color: #c1c2ef;">';
            $html .= '<td style="color: #000;font-size: 12px;"><b>Mandi</b></td>';
            $html .= '<td style="color: #000;font-size: 12px;" onclick = "RateMaster(4)" title="Mandi Rate 1"><b>'. number_format($MandiRate1, 2, ".", ",").'</b></td>';
            $html .= '<td style="color: #000;font-size: 12px;" onclick = "RateMaster(4)" title="Mandi Rate 2"><b>'. number_format($MandiRate2, 2, ".", ",").'</b></td>';
            $html .= '</tr>';
            $html .= '<tr style="background-color: #e4c1c1;">';
            $html .= '<td style="color: #000;font-size: 12px;"><b>NCDEX</b></td>';
            $html .= '<td style="color: #000;font-size: 12px;" onclick = "RateMaster(2)" title="NCDEX"><b>'. number_format($NCDEXRate, 2, ".", ",").'</b></td>';
            $ID = $value["SubGroupID"].'_'.$value["CenterID"].'_T';
            $html .= '<td style="color: #000;font-size: 12px;"  title="Trade On Off"><div class="onoffswitch1">
                <input type="checkbox" ' . (( !has_permission_new('staff', '', 'edit') && !is_admin()) ? 'disabled' : '') . ' data-switch-url="' . admin_url() . 'SaleDashboard/TradeOnOff" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $ID . '" data-id="' . $ID . '" ' . $checkedTrader . '>
                <label class="onoffswitch-label" for="c_' . $ID . '"></label>
            </div></td>';
            $html .= '</tr>';
            $html .= '</tbody>';
            $html .= '</table>';
            
            $html .= '</div>';
            $html .= '</div>';
            $i++;
        }
        
        foreach($AllCenter as $Key=>$value){
            if($ii == "7"){
                $ii = 1;
                $html2 .= '<div class="clearfix"></div>';
            }
            $AvgRate2 = 0;
            foreach($FarmerRate as $RateKey=>$Ratevalue){
                if($Ratevalue["CenterID"]== $value["CenterID"] && $Ratevalue["ItemID"]== $value["SubGroupID"]){
                    $AvgRate2 = $Ratevalue["Rate"];
                }
            }
            
            $NCDEXAvgRate = 0;
            foreach($NCDEXAvgRateGroupWise as $RateKey=>$Ratevalue){
                if($Ratevalue["CenterID"]== $value["CenterID"] && $Ratevalue["subgroup_id"]== $value["SubGroupID"]){
                    $NCDEXAvgRate = $Ratevalue["AvgRate"];
                }
            }
            
            $CompAvgRate = 0;
            foreach($CompAvgRateGroupWise as $RateKey=>$Ratevalue){
                if($Ratevalue["CenterID"]== $value["CenterID"] && $Ratevalue["subgroup_id"]== $value["SubGroupID"]){
                    $CompAvgRate = $Ratevalue["AvgRate"];
                }
            }
            
            $TradeQty2 = 0;
            foreach($FarmerTodaysCnfTradeQtyGroupWise as $FarmerKey=>$Farmervalue){
                if($Farmervalue["CenterID"]== $value["CenterID"] && $Farmervalue["GroupCode"]== $value["SubGroupID"]){
                    $TradeQty2 = $Farmervalue["TotalQty"];
                }
            }
            
            $ClsQty2 = 0;
            $WtAvgRate2 = 0;
            foreach($FarmerclsQtyGroupWise as $ClsKey=>$Clsvalue){
                if($Clsvalue["CenterID"]== $value["CenterID"] && $Clsvalue["GroupCode"]== $value["SubGroupID"]){
                    $ClsQty2 = $Clsvalue["NetWeight"];
                }
            }
            
            $Weight2 = 0;
            $Amount2 = 0;
            foreach($FarmerWeightedAvgRateGroupWise as $AvgrateKey=>$Avgratevalue){
                if($Avgratevalue["CenterID"]== $value["CenterID"] && $Avgratevalue["GroupCode"]== $value["SubGroupID"]){
                    $Weight2 += $Avgratevalue["NetWeight"];
                    $Amount2 += $Avgratevalue["Amount"];
                }
            }
            if($Amount2 > 0 && $Weight2 > 0){
                $WtAvgRate2 = $Amount2 / $Weight2;
            }
            
            $NCDEXRate = 0;
            foreach($NCDexRate as $NCDEXKey=>$NCDEXvalue){
                if($NCDEXvalue["CenterID"]== $value["CenterID"] && $NCDEXvalue["ItemID"]== $value["SubGroupID"]){
                    $NCDEXRate = $NCDEXvalue["Rate"];
                }
            }
            
            $CompRate1 = 0;
            $CompRate2 = 0;
            foreach($CompRate as $CKey=>$Cvalue){
                if($Cvalue["CenterID"]== $value["CenterID"] && $Cvalue["ItemID"]== $value["SubGroupID"]){
                    if($CompRate1 == "0"){
                        $CompRate1 = $Cvalue["Rate"];
                    }else if($CompRate1 > 0 && $CompRate2 == "0"){
                        $CompRate2 = $Cvalue["Rate"];
                    }
                }
            }
            
            $MandiRate1 = 0;
            $MandiRate2 = 0;
            foreach($MandiRate as $MKey=>$Mvalue){
                if($Mvalue["CenterID"]== $value["CenterID"] && $Mvalue["ItemID"]== $value["SubGroupID"]){
                    if($MandiRate1 == "0"){
                        $MandiRate1 = $Mvalue["Rate"];
                    }else if($MandiRate1 > 0 && $MandiRate2 == "0"){
                        $MandiRate2 = $Mvalue["Rate"];
                    }
                }
            }
            
            $FarmerTradingOn = '';
            foreach($AllCenterWiseItemWiseTrading as $TradingKey=>$Tradingvalue){
                if($Tradingvalue["CenterID"]== $value["CenterID"] && $Tradingvalue["GroupCode"]== $value["SubGroupID"]){
                    if($FarmerTradingOn == "" || $FarmerTradingOn == "N"){
                        $FarmerTradingOn = $Tradingvalue["TradeOnOffFarmer"];
                    }
                }
            }
            $checkedFarmer = '';
            if($FarmerTradingOn == "Y"){
                $checkedFarmer = "checked";
            }
            
            
            $html2 .= '<div class="col-md-2">';
            $html2 .= '<div class="table-daily_report">';
            $html2 .= '<table class="tree table table-striped table-bordered table-daily_report" id="GroupWiseDetails" width="100%">';
            $html2 .= '<thead>';
            $html2 .= '<tr>';
            $html2 .= '<th colspan="2" style="text-align:center;background-color: #f07217;font-size: 14px;"><b>'.$value["CenterName"].'</b></th>';
            $html2 .= '</tr>';
            $html2 .= '</thead>';
            $html2 .= '<tbody>';
            $html2 .= '<tr style="background-color: #333;color: #fff;">';
            $Type = "F";
            $html2 .= '<td colspan="2" onclick = "GetDetails('."'".$value["SubGroupID"]."'".', '."'".$value["CenterID"]."'".', '."'".$value["name"]."'".', '."'".$value["CenterName"]."'".', '."'".$Type."'".')"  style="text-align:center;background-color: #333;color:#fff;font-size: 13px;" data-id="'.$value['SubGroupID'].'"  data-toggle="modal" data-target="#DasboardDetails"><b>'.$value["name"].'</b></td>';
            $html2 .= '</tr>';
            $html2 .= '<tr>';
            $html2 .= '<td style="background-color: #ad0020;color: #fff;"><b>LIVE</b></td>';
            $html2 .= '<td style="background-color: #ad0020;color: #fff;"><b>Total</b></td>';
            $html2 .= '</tr>';
            $html2 .= '<tr style="background-color: #e4c1c1;">';
            $html2 .= '<td style="color: #000;font-size: 12px;" onclick = "RateMaster(1)" title="Live Current rate of the perticular location"><b>'. number_format($AvgRate2, 2, ".", ",").'</b></td>';
            $html2 .= '<td style="color: #000;font-size: 12px;" title="Weighted Avg rate of closing stock"><b>'. number_format($WtAvgRate2, 2, ".", ",").'</b></td>';
            $html2 .= '</tr>';
            $html2 .= '<tr style="background-color: #c1c2ef;">';
            $html2 .= '<td style="color: #000;font-size: 12px;" onclick = "TradeCnf()" title="Todays total Confirmed trade Qty"><b>'. number_format($TradeQty2, 2, ".", ",").'</b></td>';
            $html2 .= '<td style="color: #000;font-size: 12px;" title="Closing Stock"><b>'. number_format($ClsQty2, 2, ".", ",").'</b></td>';
            $html2 .= '</tr>';
            $html2 .= '<tr style="background-color: #e4c1c1;">';
            $html2 .= '<td style="color: #000;font-size: 12px;" onclick = "RateMaster(2)" title="competitor Rate 1"><b>'. number_format($CompRate1, 2, ".", ",").'</b></td>';
            $html2 .= '<td style="color: #000;font-size: 12px;" onclick = "RateMaster(2)" title="competitor Rate 2"><b>'. number_format($CompRate2, 2, ".", ",").'</b></td>';
            $html2 .= '</tr>';
            $html2 .= '<tr style="background-color: #c1c2ef;">';
            $html2 .= '<td style="color: #000;font-size: 12px;" onclick = "RateMaster(4)" title="Mandi Rate 1"><b>'. number_format($MandiRate1, 2, ".", ",").'</b></td>';
            $html2 .= '<td style="color: #000;font-size: 12px;" onclick = "RateMaster(4)" title="Mandi Rate 2"><b>'. number_format($MandiRate2, 2, ".", ",").'</b></td>';
            $html2 .= '</tr>';
            $html2 .= '<tr style="background-color: #e4c1c1;">';
            $html2 .= '<td style="color: #000;font-size: 12px;" onclick = "RateMaster(2)" title="NCDEX"><b>'. number_format($NCDEXRate, 2, ".", ",").'</b></td>';
            $ID = $value["SubGroupID"].'_'.$value["CenterID"].'_F';
            $html2 .= '<td style="color: #000;font-size: 12px;" title="Trade On Off"><div class="onoffswitch1">
                <input type="checkbox" ' . (( !has_permission('staff', '', 'edit') && !is_admin()) ? 'disabled' : '') . ' data-switch-url="' . admin_url() . 'SaleDashboard/TradeOnOff" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $ID . '" data-id="' . $ID . '" ' . $checkedFarmer . '>
                <label class="onoffswitch-label" for="c_' . $ID . '"></label>
            </div></td>';
            $html2 .= '</tr>';
            $html2 .= '</tbody>';
            $html2 .= '</table>';
            
            $html2 .= '</div>';
            $html2 .= '</div>';
            $ii++;
        }
        //$data = array();
        $data->Trader = $html;
        $data->Farmer = $html2;
        echo json_encode($data);
        //echo $html;
    }
    
    function TradeOnOff()
    {
        $id = $this->input->post('id');
        $ids_array = explode('_', $id);
        $ItemID = $ids_array[0];
        $CenterID = $ids_array[1];
        $Type = $ids_array[2];
        $status = $this->input->post('status');
        if($status == "1"){
            $trading_status = "Y";
        }else{
            $trading_status = "N";
        }
        $sql = 'UPDATE tblCenter_wise_item AS ud JOIN tblitems AS u ON ud.ItemID = u.ItemID';
        if($Type == "T"){
            $sql .= ' SET ud.TradeOnOff = "'.$trading_status.'" ';
        }else{
            $sql .= ' SET ud.TradeOnOffFarmer = "'.$trading_status.'" ';
        }
        $sql .= ' WHERE ud.CenterID = "'.$CenterID.'" AND u.GroupCode = "'.$ItemID.'"';
        if($this->db->query($sql)){
            echo json_encode(true);
        }else{
            echo json_encode(false);
        }
    }
   
    /*NEW FUNCTION*/
    function AccessPermissionOnOff()
    {
        $id = $this->input->post('id');
        $Type = $id;
        $status = $this->input->post('status');

        if ($status == "1") {
            $permission_status = "Y";
        } else {
            $permission_status = "N";
        }
    

        $dbColumn = ($Type == "K_P_T") ? 'TradeOnOff' : (($Type == "K_P_F") ? 'TradeOnOffFarmer' : 'SaleTradeOnOff');
        $sql = 'UPDATE tblCenter_wise_item as ud';
        $sql .= ' SET ud.' . $dbColumn . ' = "' . $permission_status . '" ';   
      
        if ($this->db->query($sql)) {
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }
    }
}
?>