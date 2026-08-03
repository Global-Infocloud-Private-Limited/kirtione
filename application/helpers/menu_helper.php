<?php
defined('BASEPATH') or exit('No direct script access allowed');
function app_init_admin_sidebar_menu_items()
{
    $CI = &get_instance();
    $CI->app_menu->add_sidebar_menu_item('master', [
        'collapse' => true,
        'name'     => "Masters",
        'position' => 1,
        'icon'     => 'fa fa-balance-scale',
    ]);
    if (has_permission_new('Fpo_Rate', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'FpoRate',
                'name'     => 'FPO Rate Master',
                'href'     => admin_url('FpoOrder/FpoRate'),
                'position' => 3,
        ]);
    }
    if (has_permission_new('TraderMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'account-master',
                'name'     => 'Trader Master',
                'href'     => admin_url('clients/AddEditAccount'),
                'position' => 3,
        ]);
    }
    if (has_permission_new('BrokerMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'AddEditBroker',
                'name'     => 'Broker Master',
                'href'     => admin_url('clients/AddEditBroker'),
                'position' => 3,
        ]);
    }
    if (has_permission_new('BlockUnblock_ledger', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'BlockUnblockLedgerPan',
                'name'     => 'Block Unblock Ledger',
                'href'     => admin_url('clients/BlockUnblockLedgerPan'),
                'position' => 4,
        ]);
    }
    if (has_permission_new('FarmarMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'AddEditFarmar',
                'name'     => 'Farmer Master',
                'href'     => admin_url('clients/AddEditFarmar'),
                'position' => 5,
        ]);
    }
    if (has_permission_new('ChangeMobileNo', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'ChangeMobileNo',
                'name'     => 'Change Mobile No',
                'href'     => admin_url('ChangeMobileNo'),
                'position' => 6,
        ]);
    }
    if (has_permission_new('hsnmaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'hsn_master',
                'name'     => 'HSN Master',
                'href'     => admin_url('hsn_master'),
                'position' => 6,
        ]);
    }
    if (has_permission('tcsmaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'tcs_master',
                'name'     => 'TCS Master',
                'href'     => admin_url('tcs_master'),
                'position' => 7,
        ]);
    }
    if (has_permission('tdsmaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'tdsMaster',
                'name'     => 'TDS Master',
                'href'     => admin_url('tdsMaster'),
                'position' => 7,
        ]);
    }
    if (has_permission_new('items', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'items',
                'name'     => "Item Master",
                'href'     => admin_url('invoice_items'),
                'position' => 8,
        ]);
    }
    if (has_permission_new('itemsparameters', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'ItemParameters',
                'name'     => "Item QA Parameters",
                'href'     => admin_url('invoice_items/ItemParameters'),
                'position' => 9,
        ]);
    }
    if (has_permission_new('itemsmaingrp', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'MainGroups',
                'name'     => "ItemMain Group",
                'href'     => admin_url('invoice_items/MainGroups'),
                'position' => 10,
        ]);
    }
    if (has_permission_new('itemssubgrp', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'ItemGroups',
                'name'     => "Item Group",
                'href'     => admin_url('invoice_items/ItemGroups'),
                'position' => 11,
        ]);
    }
    if (has_permission_new('WarehouseMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'WarehouseMaster',
                'name'     => 'Warehouse Master',
                'href'     => admin_url('Warehouse'),
                'position' => 12,
        ]);
    }
    if (has_permission_new('ClusterMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'Cluster',
                'name'     => 'Cluster Master',
                'href'     => admin_url('Cluster'),
                'position' => 13,
        ]);
    }
    if (has_permission_new('RegionMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'Cluster-Region',
                'name'     => 'Region Master',
                'href'     => admin_url('Cluster/Region'),
                'position' => 14,
        ]);
    }
    if (has_permission_new('CenterMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'Center-Region',
                'name'     => 'Center Master',
                'href'     => admin_url('Cluster/Center'),
                'position' => 15,
        ]);
    }
    if (has_permission_new('Competitor', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'Competitor',
                'name'     => 'Competitor Master',
                'href'     => admin_url('Competitor'),
                'position' => 16,
        ]);
    }
    if (has_permission_new('MSPRate', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'MSPRate',
                'name'     => 'MSP Rate Master',
                'href'     => admin_url('Rate_master/MSPRateUpdate'),
                'position' => 17,
        ]);
    }
    if (has_permission_new('DailyTraderRate', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'DailyRate',
                'name'     => 'Kirti Purchase Rate(Trader)',
                'href'     => admin_url('Rate_master/TraderRateMaster'),
                'position' => 17,
        ]);
    }
    if (has_permission_new('CityWiseCommodityRate', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'CityWiseCommodityRate',
                'name'     => 'City Wise Commodity Rate Update',
                'href'     => admin_url('Rate_master/CityWiseCommodityRateUpdate'),
                'position' => 17,
        ]);
    }
    if (has_permission_new('DailyFarmerRate', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'FarmerRateUpdate',
                'name'     => 'Kirti Purchase Rate(Farmer)',
                'href'     => admin_url('Rate_master/FarmerRateUpdate'),
                'position' => 17,
        ]);
    }
    if (has_permission_new('CityWiseFarmerCommodityRate', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'CityWiseFarmerCommodityRate',
                'name'     => 'Kirti Purchase Farmer Rate City Wise',
                'href'     => admin_url('Rate_master/CityWiseFarmerCommodityRateUpdate'),
                'position' => 17,
        ]);
    }
    if (has_permission_new('DailyCompetitorRate', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'CompRateUpdate',
                'name'     => 'Kirti Competitor Rate',
                'href'     => admin_url('Rate_master/CompRateUpdate'),
                'position' => 17,
        ]);
    }
    if (has_permission_new('DailyMandiRate', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'MandiRateUpdate',
                'name'     => 'Mandi Rate Master',
                'href'     => admin_url('Rate_master/MandiRateUpdate'),
                'position' => 17,
        ]);
    }
    if (has_permission_new('SellRateMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'SaleRateUpdate',
                'name'     => 'Kirti Sell Rate ',
                'href'     => admin_url('Rate_master/SellRateUpdate'),
                'position' => 17,
        ]);
    }
    if (has_permission_new('Charges', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'Charges',
                'name'     => 'Charges Master',
                'href'     => admin_url('Rate_master/Charges'),
                'position' => 18,
        ]);
    }
    if (has_permission_new('DeductionMatrix', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'DeductionMatrix',
                'name'     => 'Deduction Matrix',
                'href'     => admin_url('rate_master/DeductionMatrix'),
                'position' => 19,
        ]);
    }
    if (has_permission_new('PaymentCycle', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'PaymentCycle',
                'name'     => 'Payment Cycle Master',
                'href'     => admin_url('Payment_cycle'),
                'position' => 20,
        ]);
    }
    if (has_permission_new('CompanyMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'CompanyMaster',
                'name'     => 'Company Master',
                'href'     => admin_url('CompanyMaster'),
                'position' => 22,
        ]);
    }
    if (has_permission_new('Locking', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'Locking',
                'name'     => 'Locking Period Master',
                'href'     => admin_url('Payment_cycle/locking_period'),
                'position' => 21,
        ]);
    }
    if (has_permission_new('TransportMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'transportation',
                'name'     => "Transport Master",
                'href'     => admin_url('transportation'),
                'position' => 24,
        ]);
    }
    /*if (has_permission_new('Locking', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'Locking',
                'name'     => _l('als_leads'),
                'href'     => admin_url('leads'),
                'position' => 23,
        ]);
    }*/
    /*if (has_permission_new('salaryComponents', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'salaryComponents',
                'name'     => 'Salary Components',
                'href'     => admin_url('payroll/salaryComponents'),
                'position' => 24,
        ]);
    }*/
    /*if (has_permission_new('salarymaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'SalaryMaster',
                'name'     => 'Salary Master',
                'href'     => admin_url('payroll/SalaryMaster'),
                'position' => 25,
        ]);
    }
    if (has_permission_new('Staff_payroll', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'Staff_payroll',
                'name'     => 'Staff Payroll',
                'href'     => admin_url('payroll/Staff_payroll'),
                'position' => 26,
        ]);
    }*/
    if (has_permission_new('BrokerInitiateRequest', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'BrokerInitiate',
                'name'     => 'Broker Initiate',
                'href'     => admin_url('BrokerInitiate'),
                'position' => 26,
        ]);
    }
    /*if (has_permission_new('customers', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'account-master',
                'name'     => 'Customer',
                'href'     => admin_url('clients'),
                'position' => 1,
        ]);
    }*/
    /*if (has_permission_new('other_staff_master', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'staff_master',
                'name'     => 'Other Staff Master',
                'href'     => admin_url('accounts_master/manage_staff'),
                'position' => 4,
        ]);
    }*/
    /*if (has_permission_new('routemaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'route_master',
                'name'     => 'Route Master',
                'href'     => admin_url('route_master'),
                'position' => 6,
        ]);
    }*/
    /*if (has_permission_new('vehiclemaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'vehicle_master',
                'name'     => 'Vehicle Master',
                'href'     => admin_url('vehicles'),
                'position' => 7,
        ]);
    }*/
    /*if (has_permission_new('itemsdivision', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'ItemDivision',
                'name'     => "Item Division",
                'href'     => admin_url('invoice_items/ItemDivision'),
                'position' => 11,
        ]);
    }*/
    /*if (has_permission_new('hierarchy', '', 'update')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'hierarchy',
                'name'     => 'Hierarchy',
                'href'     => admin_url('hierarchy'),
                'position' => 14,
        ]);
    }*/
    /*if (has_permission_new('salesperassign', '', 'update')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'company_assign',
                'name'     => 'Attach SalesTeam to Parties',
                'href'     => admin_url('company_assign'),
                'position' => 15,
        ]);
    }*/
    /*if (has_permission_new('enquiry', '', 'view') || has_permission_new('enquiry', '', 'view_own')) {
        $CI->app_menu->add_sidebar_children_item('master', [
            'name'     => _l('enquiry'),
            'href'     => admin_url('enquiry'),
            'position' => 16,
            'icon'     => 'fa fa-ticket',
        ]);
    }
    if (has_permission('tour', '', 'view') || has_permission('tour', '', 'view_own')) {
        $CI->app_menu->add_sidebar_children_item('master', [
            'name'     => _l('tour_plan'),
            'href'     => admin_url('tour'),
            'position' => 17,
            'icon'     => 'fa fa-ticket',
        ]);
    }*/
    /*if (has_permission_new('year_transfer', '', 'update')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'year_transfer',
                'name'     => 'Year Transfer',
                'href'     => admin_url('year_transfer'),
                'position' => 18,
        ]);
    }*/
    if (has_permission_new('GodownMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'GodownMaster',
                'name'     => 'GodownMaster',
                'href'     => admin_url('GodownMaster'),
                'position' => 13,
        ]);
    }
    /*
    $CI->app_menu->add_sidebar_menu_item('leads', [
                'name'     => _l('als_leads'),
                'href'     => admin_url('leads'),
                'icon'     => 'fa fa-tty',
                'position' => 45,
        ]);*/
    /*if (has_permission('salesperassign', '', 'update')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'roles',
                'name'     => 'Role Authorization',
                'href'     => admin_url('roles'),
                'position' => 31,
        ]);
    }*/
    /*if (has_permission('salesperassign', '', 'update')) {
        $CI->app_menu->add_sidebar_children_item('master', [
                'slug'     => 'account_group_master',
                'name'     => 'Account Group',
                'href'     => admin_url('accounting/account_group_master'),
                'position' => 31,
        ]);
    }*/
    $CI->app_menu->add_sidebar_menu_item('KirtiOne', [
        'collapse' => true,
        'name'     => "Kirti One",
        'position' => 2,
        'icon'     => 'fa fa-balance-scale',
    ]);
    if (has_permission_new('AddEditCard', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('KirtiOne', [
                'slug'     => 'AddEditCard',
                'name'     => 'Card Master',
                'href'     => admin_url('CardMaster/AddEditCard'),
                'position' => 1,
        ]);
    }
    if (has_permission_new('CardAllotment', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('KirtiOne', [
                'slug'     => 'FarmerwiseCardAllocation',
                'name'     => 'Card Allotment',
                'href'     => admin_url('CardMaster/FarmerwiseCardAllocation'),
                'position' => 2,
        ]);
    }
    if (has_permission_new('CardRequestList', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('KirtiOne', [
                'slug'     => 'FarmerWiseCardRequest',
                'name'     => 'Card Request List',
                'href'     => admin_url('CardMaster/FarmerWiseCardRequest'),
                'position' => 3,
        ]);
    }
    if (has_permission_new('CardIssueList', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('KirtiOne', [
                'slug'     => 'FarmerWiseCardList',
                'name'     => 'Subscribers List',
                'href'     => admin_url('CardMaster/FarmerWiseCardList'),
                'position' => 4,
        ]);
    }
    if (has_permission_new('CardLedger', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('KirtiOne', [
                'slug'     => 'PointsLedger',
                'name'     => 'Card Point Ledger',
                'href'     => admin_url('CardMaster/PointsLedger'),
                'position' => 5,
        ]);
    }
    if (has_permission_new('SoilTestRequest', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('KirtiOne', [
                'slug'     => 'SoilTestRequest',
                'name'     => 'Soil Test Request',
                'href'     => admin_url('CardMaster/SoilTestRequest'),
                'position' => 6,
        ]);
    }
    $CI->app_menu->add_sidebar_menu_item('Kirti1Master', [
        'collapse' => true,
        'name'     => "K1 Master",
        'position' => 3,
        'icon'     => 'fa fa-balance-scale',
    ]);
    if (has_permission_new('K1Dashboard', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Master', [
            'slug'     => 'K1Dashboard',
            'name'     => 'Kirti One Dashboard',
            'href'     => admin_url('K1Dashboard'),
            'position' => 1,
        ]);
    }
    if (has_permission_new('VillageChart', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Master', [
            'slug'     => 'VillageChart',
            'name'     => 'Village Chart',
            'href'     => admin_url('VillageMaster/VillageChart'),
            'position' => 2,
        ]);
    }
    if (has_permission_new('BrandMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Master', [
                'slug'     => 'AddEditBrand',
                'name'     => 'Brand Master',
                'href'     => admin_url('BrandMaster/AddEditBrand'),
                'position' => 3,
        ]);
    }
    if (has_permission_new('ItemCategoryMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Master', [
                'slug'     => 'AddEditCategory',
                'name'     => 'Item Category Master',
                'href'     => admin_url('CategoryMaster/AddEditCategory'),
                'position' => 4,
        ]);
    }
	if (has_permission_new('ItemSubCategoryMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Master', [
                'slug'     => 'AddEditCategory',
                'name'     => 'Item SubCategory Master',
                'href'     => admin_url('CategoryMaster/AddEditSubCategory'),
                'position' => 5,
        ]);
    }
    if (has_permission_new('ItemMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Master', [
                'slug'     => 'AddEditProduct',
                'name'     => "Kirti One Item Master",
                'href'     => admin_url('ItemMaster/AddEditProduct'),
                'position' => 6,
        ]);
    }
    if (has_permission_new('ItemWiseRateMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Master', [
                'slug'     => 'AddItemWiseRate',
                'name'     => "Item Wise Rate Master",
                'href'     => admin_url('ItemMaster/AddItemWiseRate'),
                'position' => 6,
        ]);
    }
    if (has_permission_new('CropMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Master', [
                'slug'     => 'AddEditCrop',
                'name'     => 'Crop Master',
                'href'     => admin_url('CropMaster/AddEditCrop'),
                'position' => 7,
        ]);
    }
    if (has_permission_new('FertilizerMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Master', [
                'slug'     => 'AddEditFertilizer',
                'name'     => 'Fertilizer Master',
                'href'     => admin_url('FertilizerMaster/AddEditFertilizer'),
                'position' => 8,
        ]);
    }
    if (has_permission_new('PesticideMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Master', [
                'slug'     => 'AddEditPesticide',
                'name'     => 'Pesticide Master',
                'href'     => admin_url('PesticideMaster/AddEditPesticide'),
                'position' => 9,
        ]);
    }
    if (has_permission_new('SeedsMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Master', [
                'slug'     => 'AddEditSeed',
                'name'     => 'Seeds Master',
                'href'     => admin_url('SeedMaster/AddEditSeed'),
                'position' => 10,
        ]);
    }
    if (has_permission_new('PincodeMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Master', [
                'slug'     => 'AddEditPincode',
                'name'     => 'Pincode Master',
                'href'     => admin_url('PincodeMaster/AddEditPincode'),
                'position' => 11,
        ]);
    }
    if (has_permission_new('VehicleTypeMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Master', [
                'slug'     => 'VehicleTypeMaster',
                'name'     => 'Vehicle Type Master',
                'href'     => admin_url('VehicleTypeMaster'),
                'position' => 12,
        ]);
    }
    if (has_permission_new('VillageMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Master', [
                'slug'     => 'AddEditVillage',
                'name'     => 'Village Master',
                'href'     => admin_url('VillageMaster/AddEditVillage'),
                'position' => 13,
        ]);
    }
	if (has_permission_new('VillageReport', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Master', [
                'slug'     => 'VillageReport',
                'name'     => 'Village  Report',
                'href'     => admin_url('VillageMaster/VillageReport'),
                'position' => 14,
        ]);
    }
    if (has_permission_new('CommsionMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Master', [
            'slug'     => 'CommsionMaster',
            'name'     => 'Commision Master',
            'href'     => admin_url('CommisionMaster/AddEditCommsion'),
            'position' => 15,
        ]);
    }
    if (has_permission_new('TargetvsAttunement', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Master', [
            'slug'     => 'AddEditTargetvsattunment',
            'name'     => 'Target Master',
            'href'     => admin_url('TargetvsAttunement/AddEditTargetvsattunment'),
            'position' => 16,
        ]);
    }
    $CI->app_menu->add_sidebar_menu_item('Kirti1Transaction', [
        'collapse' => true,
        'name'     => "K1 Transaction",
        'position' => 4,
        'icon'     => 'fa fa-balance-scale',
    ]);
    // if (has_permission_new('OrderList', '', 'view')) {
        // $CI->app_menu->add_sidebar_children_item('Kirti1Transaction', [
                // 'slug'     => 'ItemOrderDetails',
                // 'name'     => 'Kirti One Sale List',
                // 'href'     => admin_url('ItemMaster/ItemOrderDetails'),
                // 'position' => 2,
        // ]);
    // }
    // if (has_permission_new('KirtiOneInward', '', 'view')) {
        // $CI->app_menu->add_sidebar_children_item('Kirti1Transaction', [
                // 'slug'     => 'AddEditPurchaseOrder',
                // 'name'     => 'Vendor Stock Inward',
                // 'href'     => admin_url('PurchaseMaster/AddEditPurchaseOrder'),
                // 'position' => 3,
        // ]);
    // }
    // if (has_permission_new('PurchOrderList', '', 'view')) {
        // $CI->app_menu->add_sidebar_children_item('Kirti1Transaction', [
                // 'slug'     => 'PurchaseOrderList',
                // 'name'     => 'Vendor Stock Inward List',
                // 'href'     => admin_url('PurchaseMaster/PurchaseOrderList'),
                // 'position' => 4,
        // ]);
    // }
    // if (has_permission_new('KirtiOneStockTransfer', '', 'view')) {
        // $CI->app_menu->add_sidebar_children_item('Kirti1Transaction', [
                // 'slug'     => 'AddEditStockTransfer',
                // 'name'     => 'Kirti One Stock Transfer',
                // 'href'     => admin_url('K1Stock_transfer/AddEditStockTransfer'),
                // 'position' => 5,
        // ]);
    // }
    // if (has_permission_new('KirtiOneStockTransferList', '', 'view')) {
        // $CI->app_menu->add_sidebar_children_item('Kirti1Transaction', [
                // 'slug'     => 'StockTransferList',
                // 'name'     => 'Kirti One Stock Transfer List',
                // 'href'     => admin_url('K1Stock_transfer/StockTransferList'),
                // 'position' => 6,
        // ]);
    // }
    // if (has_permission_new('K1StockPosition', '', 'view')) {
        // $CI->app_menu->add_sidebar_children_item('Kirti1Transaction', [
                // 'slug'     => 'K1InventoryMaster',
                // 'name'     => 'Kirti One Stock Position',
                // 'href'     => admin_url('K1InventoryMaster'),
                // 'position' => 7,
        // ]);
    // }
	 $CI->app_menu->add_sidebar_menu_item('Kirti1Inventory', [
        'collapse' => true,
        'name'     => "K1 Inventory",
        'position' => 5,
        'icon'     => 'fa fa-balance-scale',
    ]);
	if (has_permission_new('KirtiOneInward', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Inventory', [
                'slug'     => 'AddEditInward',
                'name'     => 'Vendor Stock Inward',
                'href'     => admin_url('PurchaseMaster/AddEditInward'),
                'position' => 1,
        ]);
    }
    if (has_permission_new('KirtiOneStockTransfer', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Inventory', [
                'slug'     => 'AddEditStockTransfer',
                'name'     => 'Stock Transfer',
                'href'     => admin_url('K1Stock_transfer/AddEditStockTransfer'),
                'position' => 3,
        ]);
    }
    if (has_permission_new('KirtiOneGodownStockTransfer', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Inventory', [
                'slug'     => 'AddEditGodownStockTransfer',
                'name'     => 'Wholesale Retail Transfer',
                'href'     => admin_url('K1Stock_transfer/AddEditGodownStockTransfer'),
                'position' => 3,
        ]);
    }
    if (has_permission_new('KirtiOneStockTransferList', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Inventory', [
                'slug'     => 'StockTransferList',
                'name'     => 'Stock Transfer List',
                'href'     => admin_url('K1Stock_transfer/StockTransferList'),
                'position' => 4,
        ]);
    }
    if (has_permission_new('KirtiOneStockAdjustment', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Inventory', [
                'slug'     => 'StockAdjustment',
                'name'     => 'Stock Adjustment',
                'href'     => admin_url('K1Stock_transfer/AddEditStockAdjustment'),
                'position' => 4,
        ]);
    }
    if (has_permission_new('KirtiOneStockAdjustmentReport', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Inventory', [
                'slug'     => 'StockAdjustmentReport',
                'name'     => 'Stock Adjustment Report',
                'href'     => admin_url('K1Stock_transfer/AdjustmentReport'),
                'position' => 5,
        ]);
    }
    if (has_permission_new('K1StockPosition', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Inventory', [
                'slug'     => 'K1InventoryMaster',
                'name'     => 'Stock Position',
                'href'     => admin_url('K1InventoryMaster'),
                'position' => 6,
        ]);
    }
    if (has_permission_new('K1ItemWiseStockPosition', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Inventory', [
                'slug'     => 'K1ItemWiseInventoryMaster',
                'name'     => 'Itemwise Stock Position',
                'href'     => admin_url('K1InventoryMaster/ItemWiseStockReport'),
                'position' => 7,
        ]);
    }
    if (has_permission_new('K1AsOnStockPosition', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Inventory', [
                'slug'     => 'K1AsOnStockPosition',
                'name'     => 'As On Date Stock',
                'href'     => admin_url('K1InventoryMaster/AsOndateStockReport'),
                'position' => 8,
        ]);
    }
    if (has_permission_new('K1ExpairyReport', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Inventory', [
                'slug'     => 'K1ExpairyReport',
                'name'     => 'Expairy Report',
                'href'     => admin_url('K1InventoryMaster/ExpairyReport'),
                'position' => 9,
        ]);
    }
    if (has_permission_new('K1SalableReport', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Inventory', [
                'slug'     => 'K1SalableReport',
                'name'     => 'Most Salable Report',
                'href'     => admin_url('K1InventoryMaster/SalableReport'),
                'position' => 10,
        ]);
    }
    if (has_permission_new('k1ProfitableReport', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Inventory', [
                'slug'     => 'k1ProfitableReport',
                'name'     => 'Most Profitable Report',
                'href'     => admin_url('K1InventoryMaster/ProfitableReport'),
                'position' => 11,
        ]);
    }
    if (has_permission_new('K1RateMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Inventory', [
                'slug'     => 'K1RateMaster',
                'name'     => 'Retail Rate Master',
                'href'     => admin_url('K1RateMaster'),
                'position' => 12,
        ]);
    }
	 $CI->app_menu->add_sidebar_menu_item('Kirti1Purchase', [
        'collapse' => true,
        'name'     => "K1 Purchase",
        'position' => 5,
        'icon'     => 'fa fa-balance-scale',
    ]);
	if (has_permission_new('PurchaseRequest', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Purchase', [
                'slug'     => 'PurchaseRequest',
                'name'     => 'Purchase Request',
                'href'     => admin_url('PurchaseMaster/AddEditPurchaseRequest'),
                'position' => 1,
        ]);
    }
	if (has_permission_new('PurchaseOrder', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Purchase', [
                'slug'     => 'PurchaseOrder',
                'name'     => 'Purchase Order',
                'href'     => admin_url('PurchaseMaster/AddEditPurchaseOrderNew'),
                'position' => 2,
        ]);
    }
	if (has_permission_new('PurchaseInvoice', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Purchase', [
                'slug'     => 'PurchaseInvoice',
                'name'     => 'Purchase Inward',
                'href'     => admin_url('PurchaseMaster/AddEditPurchaseInvoice'),
                'position' => 3,
        ]);
    }
	if (has_permission_new('PurchaseInvoiceLedger', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Purchase', [
                'slug'     => 'PurchaseInvoiceLedger',
                'name'     => 'Purchase Invoice',
                'href'     => admin_url('PurchaseMaster/AddEditPurchaseInvoiceLedger'),
                'position' => 4,
        ]);
    }
    if (has_permission_new('PurchOrderList', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Purchase', [
                'slug'     => 'PurchaseOrderList',
                'name'     => 'Purchase List',
                'href'     => admin_url('PurchaseMaster/PurchaseOrderList'),
                'position' => 5,
        ]);
    }
    if (has_permission_new('PurchaseReturnInvoice', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Purchase', [
                'slug'     => 'AddEditPurchaseReturnInvoice',
                'name'     => 'Purchase Return Invoice',
                'href'     => admin_url('PurchaseMaster/AddEditPurchaseReturnInvoice'),
                'position' => 6,
        ]);
    }
    if (has_permission_new('PurchaseReturnReport', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Purchase', [
                'slug'     => 'PurchaseReturnReport',
                'name'     => 'Purchase Return Report',
                'href'     => admin_url('PurchaseMaster/PurchaseReturnReport'),
                'position' => 7,
        ]);
    }
    if (has_permission_new('ReturnValidityStockReport', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Purchase', [
                'slug'     => 'ReturnValidityStockReport',
                'name'     => 'Return Stock Validity Report',
                'href'     => admin_url('PurchaseMaster/ReturnValidityStockReport'),
                'position' => 8,
        ]);
    }
    if (has_permission_new('DemandList', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Purchase', [
                'slug'     => 'DemandList',
                'name'     => 'Add Demand List',
                'href'     => admin_url('PurchaseMaster/AddEditDemandList'),
                'position' => 9,
        ]);
    }
    if (has_permission_new('DemandListReport', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Purchase', [
                'slug'     => 'DemandListReport',
                'name'     => 'Demand List',
                'href'     => admin_url('PurchaseMaster/DemandListReport'),
                'position' => 10,
        ]);
    }
    if (
        has_permission_new('PurchaseOrderReminderReport', '', 'view')
        || has_permission_new('PurchaseOrderReminderReport', '', 'print')
        || has_permission_new('PurchaseOrderReminderReport', '', 'export')
    ) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Purchase', [
                'slug'     => 'PurchaseOrderReminderReport',
                'name'     => 'PO Reminder Report',
                'href'     => admin_url('PurchaseMaster/PurchaseOrderReminderReport'),
                'position' => 11,
        ]);
    }
	 $CI->app_menu->add_sidebar_menu_item('Kirti1Sale', [
        'collapse' => true,
        'name'     => "K1 Sale",
        'position' => 5,
        'icon'     => 'fa fa-balance-scale',
    ]);
	if (has_permission_new('SaleOrder', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Sale', [
                'slug'     => 'SaleOrder',
                'name'     => 'B2B Sale Order',
                'href'     => admin_url('KirtiOneOrder/AddEditNewSaleOrder'),
                'position' => 1,
        ]);
    }
	if (has_permission_new('DeliveryOrder', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Sale', [
                'slug'     => 'DeliveryOrder',
                'name'     => 'Delivery Order',
                'href'     => admin_url('KirtiOneOrder/AddEditDeliveryOrder'),
                'position' => 2,
        ]);
    }
	if (has_permission_new('DeliveryInvoice', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Sale', [
                'slug'     => 'DeliveryInvoice',
                'name'     => 'Sale Invoice',
                'href'     => admin_url('KirtiOneOrder/AddEditDeliveryInvoice'),
                'position' => 3,
        ]);
    }
	if (has_permission_new('OrderMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Sale', [
                'slug'     => 'AddEditItemOrder',
                'name'     => 'Retail Direct Sale',
                'href'     => admin_url('KirtiOneOrder'),
                'position' => 3,
        ]);
    }
	// if (has_permission_new('DirectSale', '', 'view')) {
        // $CI->app_menu->add_sidebar_children_item('Kirti1Sale', [
                // 'slug'     => 'DirectSale',
                // 'name'     => 'Direct Sale',
                // 'href'     => admin_url('KirtiOneOrder/AddEditSaleOrder'),
                // 'position' => 3,
        // ]);
    // }
	if (has_permission_new('gatepass', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Sale', [
                'slug'     => 'gatepass',
                'name'     => 'Gatepass',
                'href'     => admin_url('KirtiOneOrder/view_gatepass'),
                'position' => 4,
        ]);
    }
    if (has_permission_new('OrderList', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Sale', [
                'slug'     => 'ItemOrderDetails',
                'name'     => 'Sale List',
                'href'     => admin_url('ItemMaster/ItemOrderDetails'),
                'position' => 5,
        ]);
    }
    if (has_permission_new('HsnWiseSale', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Sale', [
                'slug'     => 'HsnWiseSale',
                'name'     => 'Hsn Wise Sale',
                'href'     => admin_url('ItemMaster/HsnWiseSale'),
                'position' => 5,
        ]);
    }
    if (has_permission_new('SaleReturnInvoice', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Sale', [
                'slug'     => 'AddEditSaleReturnInvoice',
                'name'     => 'Sale Return Invoice',
                'href'     => admin_url('SaleReturn/AddEditSaleReturnInvoice'),
                'position' => 6,
        ]);
    }
    if (has_permission_new('SaleReturnReport', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Kirti1Sale', [
                'slug'     => 'Report',
                'name'     => 'Sale Return Report',
                'href'     => admin_url('SaleReturn/Report'),
                'position' => 7,
        ]);
    }
    $CI->app_menu->add_sidebar_menu_item('warehouse', [
        'collapse' => true,
        'name'     => "Warehouse",
        'position' => 5,
        'icon'     => 'fa fa-balance-scale',
    ]);
    if (has_permission_new('WHspacemgmt', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('warehouse', [
                'slug'     => 'WarehouseSize',
                'name'     => 'WH Chamber Mgmt',
                'href'     => admin_url('Warehouse/WarehouseSize'),
                'position' => 1,
        ]);
    }
    if (has_permission_new('WHStackMgmt', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('warehouse', [
                'slug'     => 'AddEditStackPlan',
                'name'     => 'WH Stack Mgmt',
                'href'     => admin_url('Warehouse/AddEditStackPlan'),
                'position' => 2,
        ]);
    }
    if (has_permission_new('WHLotMgmt', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('warehouse', [
                'slug'     => 'AddEditLotPlan',
                'name'     => 'WH Lot Mgmt',
                'href'     => admin_url('Warehouse/AddEditLotPlan'),
                'position' => 3,
        ]);
    }
    if (has_permission_new('WarehouseReports', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('warehouse', [
                'slug'     => 'WarehouseReports',
                'name'     => 'Warehouse Reports',
                'href'     => admin_url('Warehouse/Reports'),
                'position' => 4,
        ]);
    }
    if (has_permission_new('StorageTradeList', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('warehouse', [
                'slug'     => 'WarehouseReports',
                'name'     => 'Storage Trade List',
                'href'     => admin_url('WHCharges'),
                'position' => 5,
        ]);
    }
    $CI->app_menu->add_sidebar_menu_item('tansaction', [
            'collapse' => true,
            'name'     => "Transactions",
            'position' => 6,
            'icon'     => 'fa fa-balance-scale',
        ]);
    if (has_permission_new('Fpo_Order_Form', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'Fpo_Order_Form',
                'name'     => 'FPO Order',
                'href'     => admin_url('FpoOrder/Fpo_Order'),
                'position' => 1,
        ]);
    }
    if (has_permission_new('FpoOrder_Report', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'FpoOrder_Report',
                'name'     => 'FPO Order Report',
                'href'     => admin_url('FpoOrder/FpoOrderReport'),
                'position' => 1,
        ]);
    }
    if (has_permission_new('FpoDispatch_Report', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'FpoDispatch_Report',
                'name'     => 'FPO Dispatch Report',
                'href'     => admin_url('FpoOrder/FpoDispatchReport'),
                'position' => 1,
        ]);
    }
    if (has_permission_new('Bag_ledger', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'Bag_ledger',
                'name'     => 'Bag Ledger',
                'href'     => admin_url('FpoOrder/BagEntryForm'),
                'position' => 1,
        ]);
    }
    if (has_permission_new('Purchasetrade_punch', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'Purchasetrade_punch',
                'name'     => 'Purchase Trade Punch',
                'href'     => admin_url('order/Purchasetradepunch'),
                'position' => 1,
        ]);
    }
    if (has_permission_new('Purchase_Booking', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
            'slug'     => 'PurchaseRequest',
            'name'     => 'Daily Purchase Trade',
            'href'     => admin_url('order/PurchaseRequest'),
            'position' => 2,
        ]);
    }
    if (has_permission_new('DepositeTradePunch', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'Purchasetrade_punch',
                'name'     => 'Deposit Trade Punch',
                'href'     => admin_url('PurchaseTradeMaster/AddEditPurchaseTrade'),
                'position' => 3,
        ]);
    }
    if (has_permission_new('Deposit_Booking', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
            'slug'     => 'WHBooking',
            'name'     => 'Daily Deposit Trade',
            'href'     => admin_url('Warehouse/dailydeposit'),
            'position' => 4,
        ]);
    }
    if (has_permission_new('WithdrawalTradePunch', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'WithdrawalTradePunch',
                'name'     => 'Withdrawal Trade Punch',
                'href'     => admin_url('Withdrawal/TradePunch'),
                'position' => 5,
        ]);
    }
    if (has_permission_new('Withdrawal_Booking', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'Withdrawal',
                'name'     => 'Daily Withdrawal Trade',
                'href'     => admin_url('Withdrawal'),
                'position' => 6,
        ]);
    }
    if (has_permission_new('Sell_Booking', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
            'slug'     => 'SellRequest',
            'name'     => 'Daily Sell Trade',
            'href'     => admin_url('order/SellRequest'),
            'position' => 7,
        ]);
    }
    if (has_permission_new('Anamat_Booking', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'Anamat_Booking',
                'name'     => 'Daily Anamat Trade',
                'href'     => admin_url('order/AnamatRequest'),
                'position' => 7,
        ]);
    }
    if (has_permission_new('Trade_Finance_Booking', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'Trade_Finance_Booking',
                'name'     => 'Daily Trade Finance Trade',
                'href'     => admin_url('order/TradeFinanceRequest'),
                'position' => 8,
        ]);
    }
    if (has_permission_new('Booking_list', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'Booking_list',
                'name'     => 'All Trade List',
                'href'     => admin_url('Booking_list'),
                'position' => 9,
        ]);
    }
    if (has_permission_new('Ganerate_asn', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'ASNGenerate',
                'name'     => 'Generate ASN',
                'href'     => admin_url('GateControl/ASNGenerate'),
                'position' => 10,
        ]);
    }
    if (has_permission_new('Ganerate_gatein', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'GateControl',
                'name'     => 'Generate GATEIN',
                'href'     => admin_url('GateControl'),
                'position' => 11,
        ]);
    }
    if (has_permission_new('GateControl_Reports', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'GateControl_Reports',
                'name'     => 'Gate Control',
                'href'     => admin_url('GateControl/GateControl_Reports'),
                'position' => 12,
        ]);
    }
    if (has_permission_new('AdvancePayment_List', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'AdvancePayment_List',
                'name'     => 'Advance Payment List',
                'href'     => admin_url('GateControl/AdvancePaymentList'),
                'position' => 13,
        ]);
    }
    if (has_permission_new('Booking_settlement', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'Booking_settlement',
                'name'     => 'Trade Settlement',
                'href'     => admin_url('GateControl/Settlement'),
                'position' => 14,
        ]);
    }
    if (has_permission_new('SettledList', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'SettledList',
                'name'     => 'Trade Settled List',
                'href'     => admin_url('GateControl/SettledList'),
                'position' => 14,
        ]);
    }
    if (has_permission_new('CenterWiseTradeQty', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'CenterWiseTradeQty',
                'name'     => 'Center Wise Trade Quantity',
                'href'     => admin_url('GateControl/CenterWiseTradeQuantity'),
                'position' => 14,
        ]);
    }
    if (has_permission_new('PurchasePaymentList', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'PurchasePaymentList',
                'name'     => 'Purchase PaymentList',
                'href'     => admin_url('GateControl/PurchasePaymentList'),
                'position' => 15,
        ]);
    }
    if (has_permission_new('InvoiceList', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'InvoiceList',
                'name'     => 'Invoice List',
                'href'     => admin_url('GateControl/InvoiceList'),
                'position' => 16,
        ]);
    }
    if (has_permission_new('StockTransfer', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'StockTransfer',
                'name'     => 'Stock Transfer',
                'href'     => admin_url('Stock_transfer/StockTransfer'),
                'position' => 17,
        ]);
    }
    if (has_permission_new('orders', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'orders',
                'name'     => 'Direct Sale Invoice',
                'href'     => admin_url('order'),
                'position' => 18,
        ]);
    }
    if (has_permission_new('sale_return', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'sale_return',
                'name'     => 'Sales Return',
                'href'     => admin_url('sale_return'),
                'position' => 19,
        ]);
    }
    if (has_permission_new('cd_notes', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'cd_notes',
                'name'     => 'Credit/Debit Note',
                'href'     => admin_url('cd_notes'),
                'position' => 20,
        ]);
    }
    if (has_permission_new('ItemIssue', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'ItemIssue',
                'name'     => 'Item Issue',
                'href'     => admin_url('inventory/ItemIssue'),
                'position' => 21,
        ]);
    }
    /*if (has_permission_new('sale_list', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'sale-list',
                'name'     => 'Sale List',
                'href'     => admin_url('order/SaleList'),
                'position' => 2,
        ]);
    }*/
    /*if (has_permission_new('challan', '', 'create')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'challan',
                'name'     => 'Challan',
                'href'     => admin_url('challan/challanAddEdit'),
                'position' => 3,
        ]);
    }
    if (has_permission_new('challan_list', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'challan-list',
                'name'     => 'Challan List',
                'href'     => admin_url('challan/challan_list'),
                'position' => 4,
        ]);
    }
    if (has_permission_new('gatepass', '', 'view') || has_permission_new('gatepass', '', 'view_own')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'gatepass',
                'name'     => 'Gatepass',
                'href'     => admin_url('challan/view_gatepass'),
                'position' => 5,
        ]);
    }*/
    /*if (has_permission_new('vehicle_return', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'vehicle_return',
                'name'     => 'Vehicle Return',
                'href'     => admin_url('VehRtn'),
                'position' => 6,
        ]);
    }*/
    /*if (has_permission_new('pending_orders', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'pending-orders',
                'name'     => 'Pending Orders',
                'href'     => admin_url('order/pending_orders'),
                'position' => 7,
        ]);
    }*/
    /*if (has_permission_new('stock_adjustment', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'stock_adjustment',
                'name'     => 'Stock Adjustment',
                'href'     => admin_url('Stock_adjustment'),
                'position' => 10,
        ]);
    }
    if (has_permission_new('staff_target', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'target_sale',
                'name'     => 'Staff Target',
                'href'     => admin_url('misc_reports/target_sale'),
                'position' => 11,
        ]);
    }
    */
    if (has_permission_new('einvoice', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'einvoice',
                'name'     => 'E-invoice',
                'href'     => admin_url('einvoice'),
                'position' => 22,
        ]);
    }
    /*if (has_permission_new('damage_entry', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'damage_entry',
                'name'     => 'Damage Entry',
                'href'     => admin_url('damage_entry'),
                'position' => 13,
        ]);
    }
    if (has_permission_new('SplDisc', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'SplDisc',
                'name'     => 'Special Discount',
                'href'     => admin_url('SplDisc'),
                'position' => 14,
        ]);
    }*/
    /*if (has_permission_new('StockTransfer', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'StockTransfer',
                'name'     => 'StockTransfer',
                'href'     => admin_url('GodownMaster/StockTransfer'),
                'position' => 15,
        ]);
    }*/
    /*if (has_permission_new('SchemeMaster', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('tansaction', [
                'slug'     => 'SchemeMaster',
                'name'     => 'Scheme Master',
                'href'     => admin_url('SchemeMaster'),
                'position' => 16,
        ]);
    }*/
    if (has_permission_new('GSTR_purchase', '', 'view') || has_permission_new('GSTR_sales', '', 'view') || has_permission_new('GGSTR_1', '', 'view') || has_permission_new('GSTR_3B', '', 'view')){
    $CI->app_menu->add_sidebar_menu_item('e-filling', [
            'collapse' => true,
            'name'     => "E-Filling",
            'position' => 7,
            'icon'     => 'fa fa-user-circle menu-icon',
    ]);
    }
    if (has_permission_new('GSTR_purchase', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('e-filling', [
                'slug'     => 'GSTR-purchase',
                'name'     => 'GSTR Purchase',
                'href'     => admin_url('e_filling/purchase_gst_report'),
                'position' => 1,
        ]);
    }
    if (has_permission_new('GSTR_sales', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('e-filling', [
                'slug'     => 'GSTR-sales',
                'name'     => 'GSTR Sales',
                'href'     => admin_url('e_filling'),
                'position' => 2,
        ]);
    }
    if (has_permission_new('GGSTR_1', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('e-filling', [
                'slug'     => 'GGSTR-1',
                'name'     => 'GSTR-1',
                'href'     => admin_url('e_filling/GSTR1'),
                'position' => 3,
        ]);
    }
    if (has_permission_new('GSTR_3B', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('e-filling', [
                'slug'     => 'GSTR-3B',
                'name'     => 'GSTR-3B',
                'href'     => admin_url('e_filling/GSTR3B'),
                'position' => 4,
        ]);
    }
    if (has_permission_new('k1GSTR_purchase', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('e-filling', [
                'slug'     => 'k1GSTR_purchase',
                'name'     => 'K1 GSTR Purchase',
                'href'     => admin_url('K1E_Filling/k1purchase_gst_report'),
                'position' => 5,
        ]);
    }
    if (has_permission_new('k1GSTR_sales', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('e-filling', [
                'slug'     => 'k1GSTR_sales',
                'name'     => 'K1 GSTR Sales',
                'href'     => admin_url('K1E_Filling/k1sale_gst_report'),
                'position' => 6,
        ]);
    }
    if (has_permission_new('k1GSTR1', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('e-filling', [
                'slug'     => 'K1GSTR1',
                'name'     => 'K1 GSTR 1',
                'href'     => admin_url('K1E_Filling/K1GSTR1'),
                'position' => 7,
        ]);
    }
    if (has_permission_new('K1GSTR3B', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('e-filling', [
                'slug'     => 'K1GSTR3B',
                'name'     => 'K1 GSTR 3B',
                'href'     => admin_url('K1E_Filling/K1GSTR3B'),
                'position' => 8,
        ]);
    }
      /*if (has_permission('target', '', 'view') || has_permission('target', '', 'view_own')) {
        $CI->app_menu->add_sidebar_menu_item('target', [
            'name'     => _l('Target VS Achievement'),
            'href'     => admin_url('target'),
            'position' => 3,
            'icon'     => 'fa fa-ticket',
        ]);
    } */
    /*$CI->app_menu->add_sidebar_menu_item('production', [
            'collapse' => true,
            'name'     => "Production",
            'position' => 3,
            'icon'     => 'fa fa-balance-scale',
        ]);*/
  /*if (has_permission_new('production', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('production', [
                'slug'     => 'view-production',
                'name'     => 'View Production',
                'href'     => admin_url('production/view_Order'),
                'position' => 4,
        ]);
    } */
    /*if (has_permission_new('recipe', '', 'create') || has_permission_new('recipe', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('production', [
                'slug'     => 'add-recipe',
                'name'     => ' Recipe',
                'href'     => admin_url('production'),
                'position' => 1,
        ]);
    }
    if (has_permission_new('production', '', 'create') || has_permission_new('production', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('production', [
                'slug'     => 'create-production',
                'name'     => 'Production',
                'href'     => admin_url('production/create_order'),
                'position' => 2,
        ]);
    }
    if (has_permission_new('production_list', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('production', [
                'slug'     => 'view_production_list',
                'name'     => 'Production List',
                'href'     => admin_url('production/view_production_list'),
                'position' => 3,
        ]);
    }
    if (has_permission_new('production_reports', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('production', [
                'slug'     => 'production-reports',
                'name'     => 'Production Report',
                'href'     => admin_url('misc_reports/production_reports'),
                'position' => 4,
        ]);
    }
    if (has_permission_new('production_order_report', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('production', [
                'slug'     => 'production_order_report',
                'name'     => 'Production Order Report',
                'href'     => admin_url('production/production_order_report'),
                'position' => 5,
        ]);
    }
    if (has_permission_new('cost_report', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('production', [
                'slug'     => 'cost_report',
                'name'     => 'Cost Report',
                'href'     => admin_url('production/production_cost_report'),
                'position' => 6,
        ]);
    }*/
  /*if (has_permission_new('recipe', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('production', [
                'slug'     => 'view-recipe',
                'name'     => 'View Recipe',
                'href'     => admin_url('production/all_recipe'),
                'position' => 2,
        ]);
    } */
    $CI->app_menu->add_sidebar_menu_item('sales_report', [
            'collapse' => true,
            'name'     => "Sales Reports",
            'position' => 7,
            'icon'     => 'fa fa-balance-scale',
        ]);
    if (has_permission_new('daily_sale', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('sales_report', [
                'slug'     => 'daily-sale',
                'name'     => 'Daily Sales Report',
                'href'     => admin_url('Sale_reports/daily_sale'),
                'position' => 1,
        ]);
    }
    /*if (has_permission_new('cummulatives_sale', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('sales_report', [
                'slug'     => 'PartyPackWiseCummulativesSales',
                'name'     => 'Party PackWise Cummulatives Sales',
                'href'     => admin_url('Sale_reports/PartyPackWiseCummulativesSales'),
                'position' => 2,
        ]);
    }*/
    /*if (has_permission_new('target_vs_achivements', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('sales_report', [
                'slug'     => 'target_vs_achievement',
                'name'     => 'Target Vs Achievement',
                'href'     => admin_url('misc_reports/target_vs_achievement'),
                'position' => 3,
        ]);
    }*/
    if (has_permission_new('SaleRtn', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('sales_report', [
                'slug'     => 'SaleRtn',
                'name'     => 'Sales Return - Report',
                'href'     => admin_url('Sale_reports/SaleRtn'),
                'position' => 4,
        ]);
    }
    if (has_permission_new('saleVsSaleRtn', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('sales_report', [
                'slug'     => 'saleVsSaleRtn',
                'name'     => 'Sales Vs SalesReturn',
                'href'     => admin_url('Sale_reports/SaleVsSaleRtn'),
                'position' => 4,
        ]);
    }
    /*if (has_permission_new('PartyItemWiseReport', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('sales_report', [
                'slug'     => 'PartyItemWiseReport',
                'name'     => 'Party ItemWise Report',
                'href'     => admin_url('Sale_reports/PartyItemWiseReport'),
                'position' => 5,
        ]);
    }*/
    /*if (has_permission_new('OrderVsDispatch', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('sales_report', [
                'slug'     => 'OrderVsDispatch',
                'name'     => 'Order Vs Dispatch',
                'href'     => admin_url('Sale_reports/OrderVsDispatch'),
                'position' => 6,
        ]);
    }
    if (has_permission_new('OrderVsDispatchItemWise', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('sales_report', [
                'slug'     => 'OrderVsDispatchItemWise',
                'name'     => 'OrderVsDispatch ItemWise',
                'href'     => admin_url('Sale_reports/OrderVsDispatchItemWise'),
                'position' => 6,
        ]);
    }*/
    $CI->app_menu->add_sidebar_menu_item('Misc_Reports', [
            'collapse' => true,
            'name'     => "Misc. Reports",
            'position' => 8,
            'icon'     => 'fa fa-user-circle menu-icon',
    ]);
    /*if (has_permission_new('account_list', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'account-list',
                'name'     => 'Account List',
                'href'     => '#',
                'position' => 1,
        ]);
    }*/
    /*if (has_permission_new('item_rate_list', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'rate_list_report',
                'name'     => 'Item Rate List',
                'href'     => admin_url('misc_reports/rate_list_report'),
                'position' => 2,
        ]);
    }*/
    /*if (has_permission_new('market_outstanding', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'market-outstanding',
                'name'     => 'Market Outstanding',
                'href'     => admin_url('misc_reports/market_outstanding'),
                'position' => 3,
        ]);
    }*/
    /*if (has_permission_new('crate_ledger', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'crate-ledger',
                'name'     => 'Crate Ledger',
                'href'     => admin_url('misc_reports/crate_legder'),
                'position' => 4,
        ]);
    }
    if (has_permission_new('Crates_received_via_vehicle_return', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'Crates-received-via-vehicle-return',
                'name'     => 'Crates received via Vehicle return',
                'href'     => admin_url('misc_reports/crateRcvdVehicle'),
                'position' => 5,
        ]);
    }
    if (has_permission_new('routes_covered_during_a_period', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'routes-covered-during-a-period',
                'name'     => 'Routes Covered during a period',
                'href'     => '#',
                'position' => 6,
        ]);
    }
    if (has_permission_new('vehicles_on_route_duty', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'vehicles-on-route-duty',
                'name'     => 'Vehicles on Route Duty',
                'href'     => '#',
                'position' => 7,
        ]);
    }
    if (has_permission_new('vehicle_operation_during_a_month', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'vehicle-operation-during-a-month',
                'name'     => 'Vehicle Operation during a month',
                'href'     => '#',
                'position' => 8,
        ]);
    }
    if (has_permission_new('contractor_production_report', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'contractor-production-report',
                'name'     => 'Contractor Production Report',
                'href'     => '#',
                'position' => 9,
        ]);
    }
    if (has_permission_new('contractor_production_MTD', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'contractor-production-MTD',
                'name'     => 'Contractor Production - MTD',
                'href'     => '#',
                'position' => 10,
        ]);
    }*/
    if (has_permission_new('MISDashboard', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'Misc_Dashboard',
                'name'     => 'MIS Dashboard',
                'href'     => admin_url('Misc_Dashboard'),
                'position' => 1,
        ]);
    }
    $allowed_staff = [1, 4]; // Staff IDs
        if (in_array(get_staff_user_id(), $allowed_staff)) {
			$CI->app_menu->add_sidebar_children_item('Misc_Reports', [
			'slug'     => 'Traceability_Dashboard',
			'name'     => 'Traceability Dashboard',
			'href'     => admin_url('Traceability/TraceabilityDashboard'),
			'position' => 99,
			]);
		}
    if (has_permission_new('purchase_register', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'purchase-register',
                'name'     => 'Purchase Register',
                'href'     => admin_url('purchase/pur_register'),
                'position' => 1,
        ]);
    }
    if (has_permission_new('stock_position', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'stock-position',
                'name'     => 'Stock Position',
                'href'     => admin_url('misc_reports/stock_position'),
                'position' => 11,
        ]);
    }
    if (has_permission_new('stockCummulative', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'StockCummulative',
                'name'     => 'Stock Cummulative',
                'href'     => admin_url('misc_reports/StockCummulative'),
                'position' => 11,
        ]);
    }
    if (has_permission_new('ItemWiseStockReport', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'ItemWiseStockReport',
                'name'     => 'ItemWise Stock Report',
                'href'     => admin_url('Sale_reports/ItemWiseStockReport'),
                'position' => 12,
        ]);
    }
    if (has_permission_new('ItemList', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'itemlist',
                'name'     => 'Item List',
                'href'     => admin_url('invoice_items/ItemList'),
                'position' => 13,
        ]);
    }
    if (has_permission_new('AcccountGroupList', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'AcccountGroupList',
                'name'     => 'Acccount Group List',
                'href'     => admin_url('accounts_master/AccountGroupList'),
                'position' => 14,
        ]);
    }
    if (has_permission_new('AccountHeadList', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'AccountHeadList',
                'name'     => 'Account Head List',
                'href'     => admin_url('accounts_master/AccountHeadList'),
                'position' => 16,
        ]);
    }
    if (has_permission_new('CustomerList', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'CustomerList',
                'name'     => 'Customer List',
                'href'     => admin_url('clients'),
                'position' => 17,
        ]);
    }
    if (has_permission_new('survey_report', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'survey_report',
                'name'     => 'Survey Report',
                'href'     => admin_url('misc_reports/survey_report'),
                'position' => 18,
        ]);
    }
    if (has_permission_new('survey_reportChart', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'survey_Chartreport',
                'name'     => 'Survey Report Chart',
                'href'     => admin_url('misc_reports/survey_Chartreport'),
                'position' => 18,
        ]);
    }
    if (has_permission_new('rate_report', '', 'view')) {
    $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'rate_report',
                'name'     => 'Rate Report',
                'href'     => admin_url('misc_reports/rate_list_report'),
                'position' => 19,
        ]);
    }
     if (has_permission_new('breakenen_report', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                    'slug'     => 'breakenen_report',
                    'name'     => 'Breakenen sheet report',
                    'href'     => admin_url('misc_reports/viewBreakenensheet'),
                    'position' => 20,
            ]);
     }
     if (has_permission_new('traderbroker_report', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
                'slug'     => 'traderbroker_report',
                'name'     => 'Trader Broker report',
                'href'     => admin_url('misc_reports/traderbrokerreport'),
                'position' => 21,
        ]);
     }
    if (has_permission_new('customer_enquiry', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
            'slug'     => 'CustomersEnquiry',
            'name'     => 'Customers Enquiry',
            'href'     => admin_url('misc_reports/CustomersEnquiry'),
            'position' => 22,
        ]);
    }
    if (has_permission_new('expense_list', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
            'slug'     => 'expense_list',
            'name'     => 'Expense List',
            'href'     => admin_url('misc_reports/expense'),
            'position' => 23,
        ]);
     }
    if (has_permission_new('outstandingcalculation', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
            'slug'     => 'OutStandingCalculation',
            'name'     => 'OutStanding Calculation List',
            'href'     => admin_url('GateControl/OutStandingCalculation'),
            'position' => 23,
        ]);
    }
    /*if (has_permission('subscriptions', '', 'view') || has_permission('subscriptions', '', 'view_own')) {
        $CI->app_menu->add_sidebar_menu_item('subscriptions', [
                'name'     => _l('subscriptions'),
                'href'     => admin_url('subscriptions'),
                'icon'     => 'fa fa-repeat',
                'position' => 15,
        ]);
    }*/
    /*if (has_permission('expenses', '', 'view') || has_permission('expenses', '', 'view_own')) {
        $CI->app_menu->add_sidebar_menu_item('expenses', [
                'name'     => _l('expenses'),
                'href'     => admin_url('expenses'),
                'icon'     => 'fa fa-file-text-o',
                'position' => 20,
        ]);
    }*/
    /*if (has_permission('contracts', '', 'view') || has_permission('contracts', '', 'view_own')) {
        $CI->app_menu->add_sidebar_menu_item('contracts', [
                'name'     => _l('contracts'),
                'href'     => admin_url('contracts'),
                'icon'     => 'fa fa-file',
                'position' => 25,
        ]);
    }*/
    /*$CI->app_menu->add_sidebar_menu_item('projects', [
                'name'     => _l('projects'),
                'href'     => admin_url('projects'),
                'icon'     => 'fa fa-bars',
                'position' => 30,
        ]);*/
    /*$CI->app_menu->add_sidebar_menu_item('tasks', [
                'name'     => _l('als_tasks'),
                'href'     => admin_url('tasks'),
                'icon'     => 'fa fa-tasks',
                'position' => 35,
        ]);*/
    /*if ((!is_staff_member() && get_option('access_tickets_to_none_staff_members') == 1) || is_staff_member()) {
        $CI->app_menu->add_sidebar_menu_item('support', [
                'name'     => _l('support'),
                'href'     => admin_url('tickets'),
                'icon'     => 'fa fa-ticket',
                'position' => 40,
        ]);
    }*/
        /*$CI->app_menu->add_sidebar_menu_item('leads', [
                'name'     => _l('als_leads'),
                'href'     => admin_url('leads'),
                'icon'     => 'fa fa-tty',
                'position' => 45,
        ]);*/
    /*if (has_permission('knowledge_base', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('knowledge-base', [
                'name'     => _l('als_kb'),
                'href'     => admin_url('knowledge_base'),
                'icon'     => 'fa fa-folder-open-o',
                'position' => 50,
        ]);
    }*/
    // Utilities
    /*$CI->app_menu->add_sidebar_menu_item('utilities', [
            'collapse' => true,
            'name'     => _l('als_utilities'),
            'position' => 70,
            'icon'     => 'fa fa-cogs',
        ]);*/
    /*$CI->app_menu->add_sidebar_children_item('utilities', [
                'slug'     => 'media',
                'name'     => _l('als_media'),
                'href'     => admin_url('utilities/media'),
                'position' => 5,
        ]);*/
    /*if (has_permission('bulk_pdf_exporter', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('utilities', [
                'slug'     => 'bulk-pdf-exporter',
                'name'     => _l('bulk_pdf_exporter'),
                'href'     => admin_url('utilities/bulk_pdf_exporter'),
                'position' => 10,
        ]);
    }*/
    /*$CI->app_menu->add_sidebar_children_item('utilities', [
                'slug'     => 'calendar',
                'name'     => _l('als_calendar_submenu'),
                'href'     => admin_url('utilities/calendar'),
                'position' => 15,
        ]);*/
    /*if (is_admin()) {
        $CI->app_menu->add_sidebar_children_item('utilities', [
                'slug'     => 'announcements',
                'name'     => _l('als_announcements_submenu'),
                'href'     => admin_url('announcements'),
                'position' => 20,
        ]);*/
        /*$CI->app_menu->add_sidebar_children_item('utilities', [
                'slug'     => 'activity-log',
                'name'     => _l('als_activity_log_submenu'),
                'href'     => admin_url('utilities/activity_log'),
                'position' => 25,
        ]);
        $CI->app_menu->add_sidebar_children_item('utilities', [
                'slug'     => 'ticket-pipe-log',
                'name'     => _l('ticket_pipe_log'),
                'href'     => admin_url('utilities/pipe_log'),
                'position' => 30,
        ]);*/
    //}
    /*if (has_permission('reports', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('reports', [
                'collapse' => true,
                'name'     => _l('als_reports'),
                'href'     => admin_url('reports'),
                'icon'     => 'fa fa-area-chart',
                'position' => 60,
        ]);
        $CI->app_menu->add_sidebar_children_item('reports', [
                'slug'     => 'sales-reports',
                'name'     => _l('als_reports_sales_submenu'),
                'href'     => admin_url('reports/sales'),
                'position' => 5,
        ]);
        $CI->app_menu->add_sidebar_children_item('reports', [
                'slug'     => 'expenses-reports',
                'name'     => _l('als_reports_expenses'),
                'href'     => admin_url('reports/expenses'),
                'position' => 10,
        ]);
        $CI->app_menu->add_sidebar_children_item('reports', [
                'slug'     => 'expenses-vs-income-reports',
                'name'     => _l('als_expenses_vs_income'),
                'href'     => admin_url('reports/expenses_vs_income'),
                'position' => 15,
        ]);
        $CI->app_menu->add_sidebar_children_item('reports', [
                'slug'     => 'leads-reports',
                'name'     => _l('als_reports_leads_submenu'),
                'href'     => admin_url('reports/leads'),
                'position' => 20,
        ]);
        if (is_admin()) {
            $CI->app_menu->add_sidebar_children_item('reports', [
                    'slug'     => 'timesheets-reports',
                    'name'     => _l('timesheets_overview'),
                    'href'     => admin_url('staff/timesheets?view=all'),
                    'position' => 25,
            ]);
        }
        $CI->app_menu->add_sidebar_children_item('reports', [
                    'slug'     => 'knowledge-base-reports',
                    'name'     => _l('als_kb_articles_submenu'),
                    'href'     => admin_url('reports/knowledge_base_articles'),
                    'position' => 30,
            ]);
    }*/
    // Setup menu
    /*if (has_permission('staff', '', 'view')) {
        $CI->app_menu->add_setup_menu_item('staff', [
                    'name'     => _l('als_staff'),
                    'href'     => admin_url('staff'),
                    'position' => 5,
            ]);
    }*/
    if (is_admin()) {
    }
        /*$CI->app_menu->add_setup_menu_item('customers', [
                    'collapse' => true,
                    'name'     => _l('clients'),
                    'position' => 10,
            ]);*/
        /*$CI->app_menu->add_setup_menu_item('support', [
                    'collapse' => true,
                    'name'     => _l('support'),
                    'position' => 15,
            ]);
        $CI->app_menu->add_setup_children_item('support', [
                    'slug'     => 'tickets-predefined-replies',
                    'name'     => _l('acs_ticket_predefined_replies_submenu'),
                    'href'     => admin_url('tickets/predefined_replies'),
                    'position' => 10,
            ]);
        $CI->app_menu->add_setup_children_item('support', [
                    'slug'     => 'tickets-priorities',
                    'name'     => _l('acs_ticket_priority_submenu'),
                    'href'     => admin_url('tickets/priorities'),
                    'position' => 15,
            ]);
        $CI->app_menu->add_setup_children_item('support', [
                    'slug'     => 'tickets-statuses',
                    'name'     => _l('acs_ticket_statuses_submenu'),
                    'href'     => admin_url('tickets/statuses'),
                    'position' => 20,
            ]);
        $CI->app_menu->add_setup_children_item('support', [
                    'slug'     => 'tickets-services',
                    'name'     => _l('acs_ticket_services_submenu'),
                    'href'     => admin_url('tickets/services'),
                    'position' => 25,
            ]);
        $CI->app_menu->add_setup_children_item('support', [
                    'slug'     => 'tickets-spam-filters',
                    'name'     => _l('spam_filters'),
                    'href'     => admin_url('spam_filters/view/tickets'),
                    'position' => 30,
            ]);*/
        /*$CI->app_menu->add_setup_menu_item('leads', [
                    'collapse' => true,
                    'name'     => _l('acs_leads'),
                    'position' => 20,
            ]);
        $CI->app_menu->add_setup_children_item('leads', [
                    'slug'     => 'leads-sources',
                    'name'     => _l('acs_leads_sources_submenu'),
                    'href'     => admin_url('leads/sources'),
                    'position' => 5,
            ]);
        $CI->app_menu->add_setup_children_item('leads', [
                    'slug'     => 'leads-statuses',
                    'name'     => _l('acs_leads_statuses_submenu'),
                    'href'     => admin_url('leads/statuses'),
                    'position' => 10,
            ]);
        $CI->app_menu->add_setup_children_item('leads', [
                    'slug'     => 'leads-email-integration',
                    'name'     => _l('leads_email_integration'),
                    'href'     => admin_url('leads/email_integration'),
                    'position' => 15,
            ]);
        $CI->app_menu->add_setup_children_item('leads', [
                    'slug'     => 'web-to-lead',
                    'name'     => _l('web_to_lead'),
                    'href'     => admin_url('leads/forms'),
                    'position' => 20,
            ]);*/
        /*$CI->app_menu->add_setup_menu_item('finance', [
                    'collapse' => true,
                    'name'     => _l('acs_finance'),
                    'position' => 25,
            ]);
        $CI->app_menu->add_setup_children_item('finance', [
                    'slug'     => 'taxes',
                    'name'     => _l('acs_sales_taxes_submenu'),
                    'href'     => admin_url('taxes'),
                    'position' => 5,
            ]);
        $CI->app_menu->add_setup_children_item('finance', [
                    'slug'     => 'currencies',
                    'name'     => _l('acs_sales_currencies_submenu'),
                    'href'     => admin_url('currencies'),
                    'position' => 10,
            ]);
        $CI->app_menu->add_setup_children_item('finance', [
                    'slug'     => 'payment-modes',
                    'name'     => _l('acs_sales_payment_modes_submenu'),
                    'href'     => admin_url('paymentmodes'),
                    'position' => 15,
            ]);
        $CI->app_menu->add_setup_children_item('finance', [
                    'slug'     => 'expenses-categories',
                    'name'     => _l('acs_expense_categories'),
                    'href'     => admin_url('expenses/categories'),
                    'position' => 20,
            ]);*/
        /*$CI->app_menu->add_setup_menu_item('contracts', [
                    'collapse' => true,
                    'name'     => _l('acs_contracts'),
                    'position' => 30,
            ]);
        $CI->app_menu->add_setup_children_item('contracts', [
                    'slug'     => 'contracts-types',
                    'name'     => _l('acs_contract_types'),
                    'href'     => admin_url('contracts/types'),
                    'position' => 5,
            ]);*/
        $CI->app_menu->add_sidebar_menu_item('hr', [
            'collapse' => true,
            'name'     => "HR",
            'position' => 9,
            'icon'     => 'fa fa-user-circle menu-icon',
        ]);
        if(has_permission_new('hrm_hr_records','','view')){
    		 $CI->app_menu->add_sidebar_children_item('hr', [
    			'slug'     => 'hr_profile_hr_records',
    			'name'     => "Add Staff",
    			'icon'     => 'fa fa-user',
    			'href'     => admin_url('hr_profile/AddEditStaff'),
    			'position' => 1,
    		]);
        }
        if(has_permission_new('hrm_hr_sub_admin','','view')){
    		 $CI->app_menu->add_sidebar_children_item('hr', [
    			'slug'     => 'hr_profile_hr_records',
    			'name'     => "Add Sub-Admin",
    			'icon'     => 'fa fa-user',
    			'href'     => admin_url('hr_profile/AddEditSubAdmin'),
    			'position' => 1,
    		]);
        }
		if(has_permission_new('hrm_hr_recordslist','','view')){
		 $CI->app_menu->add_sidebar_children_item('hr', [
			'slug'     => 'hr_profile_hr_records',
			'name'     => 'Staff List',
			'icon'     => 'fa fa-user',
			'href'     => admin_url('hr_profile/staff_infor'),
			'position' => 2,
		]);
	 }
	 if(has_permission_new('cliam_expenses','','view') || has_permission_new('cliam_expenses','','edit')){
		 $CI->app_menu->add_sidebar_children_item('hr', [
			'slug'     => 'claim_expenses',
			'name'     => 'Claim Expenses',
			'icon'     => 'fa fa-user',
			'href'     => admin_url('claim_expenses'),
			'position' => 2,
		]);
	 }
	 if(has_permission_new('staffmanage_job_position','','view')){
		 $CI->app_menu->add_sidebar_children_item('hr', [
			'slug'     => 'hr_profile_job_position_manage',
			'name'     => "Job Designation",
			'icon'     => 'fa fa-map-pin',
			'href'     => admin_url('hr_profile/job_positions'),
			'position' => 3,
		]);
	 }
	 /*if(has_permission_new('hrm_dependent_person','','view')){
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug'     => 'hr_profile_dependent_person',
			'name'     => _l('hr_dependent_persons'),
			'icon'     => 'fa fa-address-card-o',
			'href'     => admin_url('hr_profile/dependent_persons'),
			'position' => 4,
		]);
	}*/
	if (has_permission_new('attendance_management', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug'     => 'timesheets_timekeeping',
			'name'     => _l('attendance_sheet'),
			'href'     => admin_url('timesheets/timekeeping'),
			'icon'     => 'fa fa-pencil-square-o',
			'position' =>5,
		]);
	}
// 	if (has_permission_new('leave_management', '', 'view_own') || has_permission_new('leave_management', '', 'view') || is_admin()) {
	if (has_permission_new('leave_management', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug'     => 'timesheets_timekeeping_mnrh',
			'name'     => _l('leave'),
			'icon'     => 'fa fa-clipboard',
			'href'     => admin_url('timesheets/requisition_manage') ,
			'position' => 6,
		]);
	}
	if (has_permission_new('timesheet_report', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug'     => 'TimesheetReport',
			'name'     => _l('Timesheet Report'),
			'icon'     => 'fa fa-clipboard',
			'href'     => admin_url('timesheets/TimesheetReport') ,
			'position' => 6,
		]);
	}
	/*if (has_permission_new('route_management', '', 'view_own') || has_permission_new('route_management', '', 'view') || is_admin()) {
		$allow_attendance_by_route = 0;
		$data_by_route = get_timesheets_option('allow_attendance_by_route');
		if($data_by_route){
			$allow_attendance_by_route = $data_by_route;
		}
		if($allow_attendance_by_route == 1){
			$CI->app_menu->add_sidebar_children_item('hr', [
				'slug'     => 'timesheets_route_management',
				'name'     => _l('route_management'),
				'icon'     => 'fa fa-map-signs',
				'href'     => admin_url('timesheets/route_management?tab=route') ,
				'position' => 6,
			]);
		}
	}*/
	if (has_permission_new('timesheets_table_shiftwork', '', 'view_own') || has_permission_new('timesheets_table_shiftwork', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug'     => 'timesheets_table_shiftwork',
			'name'     => _l('shiftwork'),
			'href'     => admin_url('timesheets/table_shiftwork'),
			'icon'     => 'fa fa-ticket',
			'position' =>7,
		]);
	}
	if (has_permission_new('table_shiftwork_management', '', 'view_own') || has_permission_new('table_shiftwork_management', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug'     => 'timesheets_shift_management',
			'name'     => _l('shift_management'),
			'href'     => admin_url('timesheets/shift_management'),
			'icon'     => 'fa fa-calendar',
			'position' =>8,
		]);
	}
	if (has_permission_new('timesheets_shift_type', '', 'view_own') || has_permission_new('timesheets_shift_type', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug'     => 'timesheets_shift_type',
			'name'     => _l('shift_type'),
			'href'     => admin_url('timesheets/manage_shift_type'),
			'icon'     => 'fa fa-magic',
			'position' => 9,
		]);
	}
	/*$data_attendance_by_coordinates = get_timesheets_option('allow_attendance_by_coordinates');
	if($data_attendance_by_coordinates){
		if($data_attendance_by_coordinates == 1){
			if (has_permission_new('table_workplace_management', '', 'view_own') || has_permission_new('table_workplace_management', '', 'view') || is_admin()) {
				$CI->app_menu->add_sidebar_children_item('hr', [
					'slug'     => 'timesheets_workplace_mgt',
					'name'     => _l('workplace_mgt'),
					'href'     => admin_url('timesheets/workplace_mgt?group=workplace_assign'),
					'icon'     => 'fa fa-street-view',
					'position' => 10,
				]);
			}
		}
	}*/
	/*if (has_permission('report_management', '', 'view_own') || has_permission('report_management', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug'     => 'timesheets-report',
			'name'     => "Timesheets Reports",
			'href'     => admin_url('timesheets/reports'),
			'icon'     => 'fa fa-line-chart',
			'position' =>11,
		]);
	}*/
	/*if(has_permission('hrp_employee','','view') || has_permission('hrp_employee','','view_own')){
        $CI->app_menu->add_sidebar_children_item('hr', [
            'slug'     => 'hr_manage_employees',
            'name'     => _l('hr_manage_employees'),
            'icon'     => 'fa fa-vcard-o',
            'href'     => admin_url('hr_payroll/manage_employees'),
            'position' => 12,
        ]);
    }*/
    /*if(has_permission('hrp_attendance','','view') || has_permission('hrp_attendance','','view_own')){
        $CI->app_menu->add_sidebar_children_item('hr', [
            'slug'     => 'hr_manage_attendance',
            'name'     => _l('hr_manage_attendance'),
            'icon'     => 'fa fa-pencil-square-o menu-icon',
            'href'     => admin_url('hr_payroll/manage_attendance'),
            'position' => 13,
        ]);
    }*/
    /*if(has_permission_new('hrp_commission','','view') || has_permission_new('hrp_commission','','view_own')){
        $CI->app_menu->add_sidebar_children_item('hr', [
            'slug'     => 'hr_manage_commissions',
            'name'     => _l('hrp_commission_manage'),
            'icon'     => 'fa fa-american-sign-language-interpreting',
            'href'     => admin_url('hr_payroll/manage_commissions'),
            'position' => 14,
        ]);
    }*/
    /*if(has_permission_new('hrp_deduction','','view') || has_permission_new('hrp_deduction','','view_own')){
        $CI->app_menu->add_sidebar_children_item('hr', [
            'slug'     => 'hr_manage_deductions',
            'name'     => _l('hrp_deduction_manage'),
            'icon'     => 'fa fa-cut',
            'href'     => admin_url('hr_payroll/manage_deductions'),
            'position' => 15,
        ]);
    }*/
    /*if(has_permission_new('hrp_bonus_kpi','','view') || has_permission_new('hrp_bonus_kpi','','view_own')){
        $CI->app_menu->add_sidebar_children_item('hr', [
            'slug'     => 'hr_bonus_kpi',
            'name'     => _l('hr_bonus_kpi'),
            'icon'     => 'fa fa-gift',
            'href'     => admin_url('hr_payroll/manage_bonus'),
            'position' => 16,
        ]);
    }*/
    /*if(has_permission_new('hrp_insurrance','','view') || has_permission_new('hrp_insurrance','','view_own')){
        $CI->app_menu->add_sidebar_children_item('hr', [
            'slug'     => 'hrp_insurrance',
            'name'     => _l('hrp_insurrance'),
            'icon'     => 'fa fa-medkit',
            'href'     => admin_url('hr_payroll/manage_insurances'),
            'position' => 17,
        ]);
    }*/
    /*if(has_permission('hrp_payslip','','view') || has_permission('hrp_payslip','','view_own')){
        $CI->app_menu->add_sidebar_children_item('hr', [
            'slug'     => 'hr_pay_slips',
            'name'     => _l('hr_pay_slips'),
            'icon'     => 'fa fa-money',
            'href'     => admin_url('hr_payroll/payslip_manage'),
            'position' => 18,
        ]);
    }*/
    /*if(has_permission('hrp_payslip_template','','view') || has_permission('hrp_payslip_template','','view_own')){
        $CI->app_menu->add_sidebar_children_item('hr', [
            'slug'     => 'hrp_payslip_template',
            'name'     => _l('hr_pay_slip_templates'),
            'icon'     => 'fa fa-outdent',
            'href'     => admin_url('hr_payroll/payslip_templates_manage'),
            'position' => 19,
        ]);
    }*/
    /*if(has_permission_new('hrp_income_tax','','view') || has_permission_new('hrp_income_tax','','view_own')){
        $CI->app_menu->add_sidebar_children_item('hr', [
            'slug'     => 'hrp_income_tax',
            'name'     => _l('hrp_income_tax'),
            'icon'     => 'fa fa-calendar-minus-o',
            'href'     => admin_url('hr_payroll/income_taxs_manage'),
            'position' => 20,
        ]);
    }*/
    /*if(has_permission('hrp_report','','view')){
        $CI->app_menu->add_sidebar_children_item('hr', [
            'slug'     => 'hr_payroll_reports',
            'name'     => _l('hrp_reports'),
            'icon'     => 'fa fa-list-alt',
            'href'     => admin_url('hr_payroll/reports'),
            'position' => 21,
        ]);
    }*/
        $modules_name = _l('modules');
        if ($modulesNeedsUpgrade = $CI->app_modules->number_of_modules_that_require_database_upgrade()) {
            $modules_name .= '<span class="badge menu-badge bg-warning">' . $modulesNeedsUpgrade . '</span>';
        }
        $CI->app_menu->add_setup_menu_item('modules', [
                    'href'     => admin_url('modules'),
                    'name'     => $modules_name,
                    'position' => 35,
            ]);
    if (has_permission('settings', '', 'view')) {
        $CI->app_menu->add_setup_menu_item('settings', [
                    'href'     => admin_url('settings'),
                    'name'     => _l('acs_settings'),
                    'position' => 200,
            ]);
    }
    if (has_permission('email_templates', '', 'view')) {
        $CI->app_menu->add_setup_menu_item('email-templates', [
                    'href'     => admin_url('emails'),
                    'name'     => _l('acs_email_templates'),
                    'position' => 40,
            ]);
    }
    if (is_admin() || has_permission_new('user_master', '', 'view') || has_permission_new('no_show', '', 'view') || has_permission_new('user_rights', '', 'view') || has_permission_new('distributor_type', '', 'view') || has_permission_new('roles', '', 'view')
    //  || has_permission_new('sell_dashboard','','view') || has_permission_new('hrm_salary_type','','view') || has_permission_new('hrm_allowance_type','','view') || has_permission_new('hrm_hedquarter','','view') || has_permission_new('hrm_gen_setting','','view') || has_permission_new('news', '', 'view')) {
     || has_permission_new('sell_dashboard','','view') || has_permission_new('hrm_salary_type','','view') || has_permission_new('departments','','view') || has_permission_new('hrm_allowance_type','','view') || has_permission_new('hrm_hedquarter','','view') || has_permission_new('hrm_gen_setting','','view') || has_permission_new('news', '', 'view')) {
    $CI->app_menu->add_sidebar_menu_item('admin', [
            'collapse' => true,
            'name'     => "Admin",
            'position' => 80,
            'icon'     => 'fa fa-user-circle menu-icon',
    ]);
    }
    if (is_admin() || has_permission_new('user_master', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('admin', [
                'slug'     => 'user_master',
                'name'     => 'User Master',
                'href'     => admin_url('accounts_master/User_master'),
                'position' => 1,
        ]);
    }
    if (is_admin() || has_permission_new('user_rights', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('admin', [
                'slug'     => 'user_rights',
                'name'     => 'User Rights',
                'href'     => admin_url('roles/user_rights'),
                'position' => 2,
        ]);
    }
    if (is_admin() || has_permission_new('sell_dashboard', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('admin', [
                'slug'     => 'SaleDashboard',
                'name'     => 'Sell Dashboard',
                'href'     => admin_url('SaleDashboard'),
                'position' => 2,
        ]);
    }
    if (is_admin() || has_permission_new('departments', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('admin', [
                'slug'     => 'departments',
                'name'     => 'Departments',
                'href'     => admin_url('departments'),
                'position' => 2,
        ]);
    }
    if(is_admin() || has_permission_new('roles', '', 'view')){
    $CI->app_menu->add_sidebar_children_item('admin', [
                    'href'     => admin_url('roles'),
                    'name'     => 'Staff Role',
                    'position' => 6,
            ]);
    }
    if(has_permission_new('hrm_salary_type','','create') || has_permission_new('hrm_salary_type','','view') || has_permission_new('hrm_salary_type','','edit') || has_permission_new('hrm_salary_type','','delete') || has_permission_new('hrm_allowance_type','','create') || has_permission_new('hrm_allowance_type','','view') || has_permission_new('hrm_allowance_type','','edit') || has_permission_new('hrm_allowance_type','','delete') || has_permission_new('hrm_hedquarter','','view') || has_permission_new('hrm_hedquarter','','create') || has_permission_new('hrm_gen_setting','','view') || has_permission_new('hrm_gen_setting','','edit')){
		 if(has_permission_new('hrm_salary_type','','view') || has_permission_new('hrm_salary_type','','create') || has_permission_new('hrm_salary_type','','edit') || has_permission_new('hrm_salary_type','','delete')){
		     $url = admin_url('hr_profile/setting?group=salary_type');
		 }else if(has_permission_new('hrm_allowance_type','','view') || has_permission_new('hrm_allowance_type','','create') || has_permission_new('hrm_allowance_type','','edit') || has_permission_new('hrm_allowance_type','','delete')){
		     $url = admin_url('hr_profile/setting?group=allowance_type');
		 }else if(has_permission_new('hrm_hedquarter','','view') || has_permission_new('hrm_hedquarter','','create')){
		     $url = admin_url('hr_profile/setting?group=hedquarter');
		 }else if(has_permission_new('hrm_gen_setting','','view') || has_permission_new('hrm_gen_setting','','edit')){
		     $url = admin_url('hr_profile/setting?group=prefix_number');
		 }
// 		 else if(has_permission_new('timesheets_setting','','view') || has_permission_new('timesheets_setting','','create') || has_permission_new('timesheets_setting','','edit')){
// 		     $url = admin_url('timesheets/setting?group=manage_leave');
// 		 }
		 $CI->app_menu->add_sidebar_children_item('admin', [
			'slug'     => 'hr_profile_setting',
			'name'     => 'HR Records Setting',
			'icon'     => 'fa fa-cogs',
			'href'     => $url,
			'position' => 7,
		]);
	 }
	 if(has_permission_new('hrp_setting','','view') || is_admin()){
        $CI->app_menu->add_sidebar_children_item('admin', [
            'slug'     => 'hrp_settings',
            'name'     => 'HR Payroll Setting',
            'icon'     => 'fa fa-cog menu-icon',
            'href'     => admin_url('hr_payroll/setting?group=income_tax_rates'),
            'position' => 8,
        ]);
    }
// 	if (has_permission_new('timesheets_setting','','view') || is_admin()) {
	if (is_admin()) {
		$CI->app_menu->add_sidebar_children_item('admin', [
			'slug'     => 'timesheets_setting',
			'name'     => 'Timesheets Setting',
			'href'     => admin_url('timesheets/setting?group=manage_leave'),
			'icon'     => 'fa fa-gears',
			'position' => 9,
		]);
	}
	/*if (is_admin()) {
	$CI->app_menu->add_sidebar_children_item('admin', [
            'slug'     => 'purchase-settings',
            'name'     => 'Purchase Setting',
            'icon'     => 'fa fa-gears',
            'href'     => admin_url('purchase/setting'),
            'position' => 10,
        ]);
	}*/
	if (has_permission_new('settings', '', 'view') || is_admin()) {
        $CI->app_menu->add_sidebar_children_item('admin', [
                    'href'     => admin_url('settings'),
                    'name'     => 'Genaral Setting',
                    'position' => 11,
            ]);
    }
    if (has_permission_new('news', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('admin', [
                'slug'     => 'news',
                'name'     => 'News',
                'href'     => admin_url('news'),
                'position' => 12,
        ]);
    }
}
