<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|   example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|   http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|   $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|   $route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|   $route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples: my-controller/index -> my_controller/index
|       my-controller/my-method -> my_controller/my_method
*/

$route['default_controller']   = 'clients';
$route['404_override']         = '';
$route['translate_uri_dashes'] = false;

//================== Flutter APP API List ======================================
$route['API/ChkMobile'] = 'AppUser/ChkMobileAPI';
$route['ChkAadhar'] = 'AppUser/ChkAadharNoAPI';
$route['ChkAccount'] = 'AppUser/ChkAccountNoAPI';
$route['ChkPanNo'] = 'AppUser/ChkPanNoAPI';
$route['GetAadharDetails'] = 'AppUser/GetAadharDetailsAPI';
$route['API/SignIN'] = 'AppUser/SignInAPI';
$route['API/SetAccountType'] = 'AppUser/SetAccountTypeAPI';
$route['API/langUpdate'] = 'AppUser/langUpdateAPI'; 
$route['API/latlongUpdate'] = 'AppUser/latlongUpdateAPI';
$route['API/AadharCheck'] = 'AppUser/AadharCheckAPI';
$route['API/AadharCheckAndValidate'] = 'AppUser/AadharCheckAndValidateAPI';
$route['API/AadharDetailUpdate'] = 'AppUser/AadharUpdateAPI';
$route['API/DocUpload'] = 'UserApp_Controller/DocUploadAPI';
$route['API/GetAllStaff'] = 'AppUser/GetAllStaffAPI';
$route['API/GstListAdd'] = 'AppUser/GstListAddAPI';
$route['API/PANCheck'] = 'AppUser/PANCheckAPI';
$route['API/PANCheckAndValidate'] = 'AppUser/PANCheckAndValidateAPI';
$route['API/AddPan'] = 'AppUser/AddPanAPI';
$route['API/SetPrimaryGst'] = 'AppUser/SetPrimaryGstAPI';
$route['API/GetStateList'] = 'AppUser/GetStateListAPI';
$route['API/GetCityList'] = 'AppUser/GetCityListAPI';
$route['API/AddDinDetails'] = 'AppUser/AddDinDetailsAPI';
$route['API/BankDetailUpdate'] = 'AppUser/BankAccountUpdateAPI';
$route['API/KYCStatus'] = 'AppUser/KYCStatusAPI';
$route['API/Dashboard'] = 'AppUser/DashboardAPI';
$route['API/ItemGroupWiseCenterUpdated'] = 'AppUser/ItemGroupWiseCenterUpdatedAPI';
$route['API/CenterWiseItemGroup'] = 'AppUser/CenterWiseItemGroupAPI';
$route['API/CenterItemGroupWiserate'] = 'AppUser/CenterItemGroupWiserateAPI';
$route['API/GetRate'] = 'AppUser/GetRateAPI';
$route['API/CropSell'] = 'AppUser/AddCropSellAPI';
$route['API/BookingList'] = 'AppUser/BookingListAPI';
$route['API/TransactionSummery'] = 'AppUser/TransactionSummeryAPI';
$route['API/InwardDetailsByBookingID'] = 'AppUser/InwardDetailsByBookingIDAPI';
$route['API/Send_request'] = 'AppUser/Send_requestAPI';
$route['API/ApproveRequest'] = 'AppUser/ApproveRequestAPI';
$route['API/RequestList'] = 'AppUser/RequestListAPI';
$route['API/BlockUnblockParty'] = 'AppUser/BlockUnblockPartyAPI';
$route['API/AddWishlist'] = 'AppUser/AddWishlistAPI';
$route['API/GetWishlist'] = 'AppUser/GetWishlistAPI';
$route['API/newsList'] = 'AppUser/GetNewsAPI';
$route['API/UserProfile'] = 'AppUser/UserProfileAPI';
$route['API/SetPrimaryBankAccount'] = 'AppUser/SetPrimaryBankAccountAPI';
$route['API/Userlogout'] = 'AppUser/logoutAPI';
$route['API/GetTraderList'] = 'AppUser/GetTraderListAPI';
$route['API/GetBrokerList'] = 'AppUser/GetBrokerListAPI';
$route['API/BookingAccept'] = 'AppUser/BookingAcceptAPI';
$route['API/AllActiveCenterList'] = 'AppUser/AllActiveCenterListAPI';
$route['API/WHList'] = 'AppUser/WHListAPI';
$route['API/WHWiseItems'] = 'AppUser/WHWiseItemsAPI';
$route['API/BookWH'] = 'AppUser/BookWHAPI';
$route['API/AddAnamatTrade'] = 'AppUser/AddAnamatTradeAPI';

$route['API/CenterWiseItemGroupSale'] = 'AppUser/CenterWiseItemGroupSaleAPI';
$route['API/ItemGroupWiseCenterSaleUpdated'] = 'AppUser/ItemGroupWiseCenterSaleUpdatedAPI';
$route['API/CenterItemGroupWiseSalerate'] = 'AppUser/CenterItemGroupWiseSalerateAPI';
$route['API/GetSaleRate'] = 'AppUser/GetSaleRateAPI';
$route['API/AddKirtiSale'] = 'AppUser/AddKirtiSaleAPI';

$route['API/GetItemForWithdraw'] = 'AppUser/GetItemForWithdrawAPI';
$route['API/GetWHforWithdrawItemWise'] = 'AppUser/GetWHforWithdrawItemWiseAPI';
$route['API/GetStackforWithdrawWHWise'] = 'AppUser/GetStackforWithdrawWHWiseAPI';
$route['API/GetLotforWithdrawStackWise'] = 'AppUser/GetLotforWithdrawStackWiseAPI';
$route['API/GetLotDetailsByLotID'] = 'AppUser/GetLotDetailsByLotIDAPI';
$route['API/SubmitWithdrawRequest'] = 'AppUser/SubmitWithdrawRequestAPI';


//================== Flutter APP API List End ==================================

//================ Kirti One API ===============================================
$route['CardListDetails'] = 'UserApp_Controller/CardListDetailsAPI';
$route['CardLedger'] = 'UserApp_Controller/CardLedgerAPI';
$route['SubmitCardRequest'] = 'UserApp_Controller/SubmitCardRequestAPI';
$route['SubmitSoilTestRequest'] = 'UserApp_Controller/SubmitSoilTestRequestAPI';
$route['GetSoilTestRequest'] = 'UserApp_Controller/GetSoilTestRequestAPI';
//=============== Village Details API Start ==============================================
$route['VillageListByPincode'] = 'UserApp_Controller/VillageListByPincodeAPI';
$route['VillageMasters'] = 'UserApp_Controller/VillageMastersAPI';
$route['PincodeDetails'] = 'UserApp_Controller/PincodeDetailsAPI';
$route['AddVillageDetails'] = 'UserApp_Controller/AddVillageDetailsAPI';
$route['VillageLIstStaffWise'] = 'UserApp_Controller/VillageLIstStaffWiseAPI';
$route['CheckVillageDetailsExist'] = 'UserApp_Controller/CheckVillageDetailsExistAPI';
$route['CheckAggregatorDetails'] = 'UserApp_Controller/CheckAggregatorDetailsAPI';
$route['AddAggregatorDetails'] = 'UserApp_Controller/AddAggregatorDetailsAPI';
$route['CheckKskDetails'] = 'UserApp_Controller/CheckKskDetailsAPI';
$route['AddKskDetails'] = 'UserApp_Controller/AddKskDetailsAPI';
$route['CheckHotelDetails'] = 'UserApp_Controller/CheckHotelDetailsAPI';
$route['AddHotelDetails'] = 'UserApp_Controller/AddHotelDetailsAPI';
$route['CheckCropDetails'] = 'UserApp_Controller/CheckCropDetailsAPI';
$route['CheckVehicleDetails'] = 'UserApp_Controller/CheckVehicleDetailsAPI';
$route['AddVehicleDetails'] = 'UserApp_Controller/AddVehicleDetailsAPI';
$route['AddCropDetails']= 'UserApp_Controller/AddCropDetailsAPI';
$route['VehicleType']= 'UserApp_Controller/VehicleTypeAPI';
$route['ItemDetails'] = 'UserApp_Controller/ItemDetailsAPI';
$route['ItemCategoryBrandDetail'] = 'UserApp_Controller/ItemCategoryBrandDetailAPI';
$route['FGItemList'] = 'UserApp_Controller/GetFGItemListAPI';
$route['ItemWiseQCParameter'] = 'UserApp_Controller/GetItemWiseQCParameterAPI';
$route['GetCalculation'] = 'UserApp_Controller/GetCalculationAPI';
$route['KirtiOneItemCategory'] = 'UserApp_Controller/KirtiOneItemCategoryAPI';
$route['KirtiOneItemList'] = 'UserApp_Controller/KirtiOneItemListAPI';
$route['AddEditItemInCart'] = 'UserApp_Controller/AddEditItemInCartAPI';
$route['CartItemList'] = 'UserApp_Controller/CartItemListAPI';
$route['RemoveItemFromCart'] = 'UserApp_Controller/RemoveItemFromCartAPI';
$route['KirtiOnePlaceOrder'] = 'UserApp_Controller/KirtiOnePlaceOrderAPI';
$route['KirtiOneOrderList'] = 'UserApp_Controller/KirtiOneOrderListAPI';
$route['AccountLedger'] = 'UserApp_Controller/AccountLedgerAPI';
$route['AccountClosingBalance'] = 'UserApp_Controller/AccountClosingBalanceAPI';
$route['PaymentRequest'] = 'UserApp_Controller/PaymentRequestAPI';
$route['PaymentRequestList'] = 'UserApp_Controller/PaymentRequestListAPI';
$route['ShippingAddress'] = 'UserApp_Controller/ShippingAddressAPI';
//=============== Village Details API END ==============================================
//================ Weight Bridge Integration API Start =========================
$route['API/AddWeight'] = 'AppUser/AddWeightAPI';
//================ Weight Bridge Integration API End =========================

//=============== PC Soft API Start ============================================
$route['CompanyDetails'] = 'UserApp_Controller/CompanyDetailsAPI';
$route['CenterDetails'] = 'UserApp_Controller/CenterDetailsAPI';
$route['GetAccountDetails'] = 'UserApp_Controller/GetAccountDetailsAPI';
$route['GetAccountDetailsNew'] = 'UserApp_Controller/GetAccountDetailsNewAPI';
$route['GetInwardDataFromPcSoft'] = 'UserApp_Controller/GetInwardDataFromPcSoftAPI';
$route['GetPIDataFromPcSoft'] = 'UserApp_Controller/GetPIDataFromPcSoftAPI';
$route['UpdatePaymentStatus'] = 'UserApp_Controller/UpdatePaymentStatusAPI';
$route['GetInvoiceData'] = 'UserApp_Controller/GetInvoiceDataAPI';


//=============== PC Soft API End ==============================================

//=============== Star Agri API Start ============================================
$route['GetPartyDetails'] = 'UserApp_Controller/GetPartyDetailsAPI';


//=============== PC Soft API End ==============================================

//================= Agri Bazaar API Start ======================================
$route['PANCheckAgriBazaar'] = 'UserApp_Controller/PANCheckAgriBazaarAPI';
$route['SignINAgriBazaar'] = 'UserApp_Controller/SignINAgriBazaarAPI';
//================= Agri Bazaar API End ========================================

//================ kirti Staff Application API Start ===========================

$route['GetPurchOrder'] = 'UserApp_Controller/GetPurchOrderAPI';
$route['GetStaffData'] = 'UserApp_Controller/GetStaffDataAPI';
    
//================ kirti Staff Application API End ===========================

//=========== Kirti API ========================================================
$route['ChkMobile'] = 'UserApp_Controller/ChkMobileAPI';
$route['ChkAadhar'] = 'UserApp_Controller/ChkAadharNoAPI';
$route['ChkAccount'] = 'UserApp_Controller/ChkAccountNoAPI';
$route['ChkPanNo'] = 'UserApp_Controller/ChkPanNoAPI';
$route['GetAadharDetails'] = 'UserApp_Controller/GetAadharDetailsAPI';
$route['SignIN'] = 'UserApp_Controller/SignInAPI';
$route['langUpdate'] = 'UserApp_Controller/langUpdateAPI';
$route['SetAccountType'] = 'UserApp_Controller/SetAccountTypeAPI';
$route['SetAccountTypeByBroker'] = 'UserApp_Controller/SetAccountTypeByBrokerAPI';
$route['latlongUpdate'] = 'UserApp_Controller/latlongUpdateAPI';
$route['AadharDetailUpdate'] = 'UserApp_Controller/AadharUpdateAPI';
$route['BankDetailUpdate'] = 'UserApp_Controller/BankAccountUpdateAPI';
$route['BankDetailUpdateByBroker'] = 'UserApp_Controller/BankDetailUpdateByBrokerAPI';
$route['UserProfile'] = 'UserApp_Controller/UserProfileAPI';
$route['GetBankAccounts'] = 'UserApp_Controller/GetBankAccountsAPI';
$route['SetPrimaryBankAccount'] = 'UserApp_Controller/SetPrimaryBankAccountAPI';
$route['Userlogout'] = 'UserApp_Controller/logoutAPI';
$route['AddPan'] = 'UserApp_Controller/AddPanAPI';
$route['GstListAdd'] = 'UserApp_Controller/GstListAddAPI';
$route['GstListAddByBroker'] = 'UserApp_Controller/GstListAddByBrokerAPI';
$route['SetPrimaryGst'] = 'UserApp_Controller/SetPrimaryGstAPI';
$route['SetPrimaryGstByBroker'] = 'UserApp_Controller/SetPrimaryGstByBrokerAPI';
$route['AddDinDetails'] = 'UserApp_Controller/AddDinDetailsAPI';

$route['DocUpload'] = 'UserApp_Controller/DocUploadAPI';
$route['DocUploadByBroker'] = 'UserApp_Controller/DocUploadByBrokerAPI';
$route['AddFarm'] = 'UserApp_Controller/AddFarmAPI';
$route['GetFarm'] = 'UserApp_Controller/GetFarmAPI';
$route['GetStateList'] = 'UserApp_Controller/GetStateListAPI';
$route['GetCityList'] = 'UserApp_Controller/GetCityListAPI';
$route['GetTalukaList'] = 'UserApp_Controller/GetTalukaAPI';
$route['AddCrop'] = 'UserApp_Controller/AddCropAPI';
$route['GetCrop'] = 'UserApp_Controller/GetCropAPI';

$route['GetMyCenter'] = 'UserApp_Controller/GetMyCenterAPI';
$route['ItemList'] = 'UserApp_Controller/ItemListAPI';
$route['SalePurchaseItemList'] = 'UserApp_Controller/SalePurchaseItemListAPI';
$route['AadharCheck'] = 'UserApp_Controller/AadharCheckAPI';
$route['PANCheck'] = 'UserApp_Controller/PANCheckAPI';
$route['KYCStatus'] = 'UserApp_Controller/KYCStatusAPI';
$route['Dashboard'] = 'UserApp_Controller/DashboardAPI';
$route['AllActiveCenterList'] = 'UserApp_Controller/AllActiveCenterListAPI';
$route['WHList'] = 'UserApp_Controller/WHListAPI';
$route['WHWiseItems'] = 'UserApp_Controller/WHWiseItemsAPI';
$route['BookWH'] = 'UserApp_Controller/BookWHAPI';
$route['WHBookingList'] = 'UserApp_Controller/WHBookingListAPI';
$route['CenterWiseCommodity'] = 'UserApp_Controller/CenterWiseCommodityAPI';

$route['GetTraderList'] = 'UserApp_Controller/GetTraderListAPI';
$route['GetBrokerList'] = 'UserApp_Controller/GetBrokerListAPI';
$route['GetDeductionMatrix'] = 'UserApp_Controller/GetDeductionMatrixAPI';
$route['GetMACAddress'] = 'UserApp_Controller/GetMACAddressAPI';
$route['BookingAccept'] = 'UserApp_Controller/BookingAcceptAPI';
$route['GetSurveyList'] = 'UserApp_Controller/GetSurveyListAPI';
$route['GetSurveyDetails'] = 'UserApp_Controller/GetSurveyDetailsAPI';
//$route['Trader_initiate_as_broker'] = 'UserApp_Controller/Trader_initiate_as_brokerAPI'; // Not in USE

$route['Send_request'] = 'UserApp_Controller/Send_requestAPI';
$route['ApproveRequest'] = 'UserApp_Controller/ApproveRequestAPI';
$route['BlockUnblockParty'] = 'UserApp_Controller/BlockUnblockPartyAPI';
$route['RequestList'] = 'UserApp_Controller/RequestListAPI';

//Anamat API's

$route['AddAnamatTrade'] = 'UserApp_Controller/AddAnamatTradeAPI';

//Trade Finance API's

$route['AddTradeFinance'] = 'UserApp_Controller/AddTradeFinanceAPI';

// Add Kirti Purchase request 
$route['CenterWiseItemGroup'] = 'UserApp_Controller/CenterWiseItemGroupAPI';
$route['ItemGroupWiseCenter'] = 'UserApp_Controller/ItemGroupWiseCenterAPI';
$route['ItemGroupWiseCenterUpdated'] = 'UserApp_Controller/ItemGroupWiseCenterUpdatedAPI';
$route['CenterItemGroupWiserate'] = 'UserApp_Controller/CenterItemGroupWiserateAPI';
$route['GetRate'] = 'UserApp_Controller/GetRateAPI';
// Get Current rate for PCSoft
$route['GetCurrentRate'] = 'UserApp_Controller/GetCurrentRateAPI';
$route['CropSell'] = 'UserApp_Controller/AddCropSellAPI';
$route['UpdateQcApproval'] = 'UserApp_Controller/UpdateQcApprovalAPI';

$route['AddCleaningDetails'] = 'UserApp_Controller/AddCleaningDetailsAPI';

// Get All type of trade like buy, sale
// Buy - P , Sale - S
$route['BookingList'] = 'UserApp_Controller/BookingListAPI';

// Add Kirti Sale request
$route['CenterWiseItemGroupSale'] = 'UserApp_Controller/CenterWiseItemGroupSaleAPI';
$route['ItemGroupWiseCenterSale'] = 'UserApp_Controller/ItemGroupWiseCenterSaleAPI';
$route['ItemGroupWiseCenterSaleUpdated'] = 'UserApp_Controller/ItemGroupWiseCenterSaleUpdatedAPI';
$route['CenterItemGroupWiseSalerate'] = 'UserApp_Controller/CenterItemGroupWiseSalerateAPI';
$route['GetSaleRate'] = 'UserApp_Controller/GetSaleRateAPI';
$route['AddKirtiSale'] = 'UserApp_Controller/AddKirtiSaleAPI';
$route['PunchSaleOrder'] = 'UserApp_Controller/PunchSaleOrderAPI';
$route['SubmitEMD'] = 'UserApp_Controller/SubmitEMDAPI';
$route['EMDDetails'] = 'UserApp_Controller/EMDDetailsAPI';

// Crop Purchase API 
$route['InwardDetailsByBookingID'] = 'UserApp_Controller/InwardDetailsByBookingIDAPI';
$route['GetPaymentStatusByInward'] = 'UserApp_Controller/GetPaymentStatusByInwardAPI';
$route['GetBookingDetails'] = 'UserApp_Controller/GetBookingDetailsAPI';
$route['GetContractNoteDetails'] = 'UserApp_Controller/GetContractNoteDetailsAPI';
$route['PeripheralQC'] = 'UserApp_Controller/PeripheralQCAPI';
$route['UpdateWeighBridgeDetails'] = 'UserApp_Controller/UpdateWeighBridgeDetailsAPI';
$route['AddNoOfLayers'] = 'UserApp_Controller/AddNoOfLayersAPI';
$route['UpdateSingleLayer'] = 'UserApp_Controller/UpdateSingleLayerAPI';
$route['UpdateQualitySingleLayer'] = 'UserApp_Controller/UpdateQualitySingleLayerAPI';
$route['UpdateQualityAllLayer'] = 'UserApp_Controller/UpdateQualityAllLayerAPI';
$route['UpdateUnloadingDetails'] = 'UserApp_Controller/UpdateUnloadingDetailsAPI';
$route['UpdateQuality'] = 'UserApp_Controller/UpdateQualityAPI';
$route['Cleaning'] = 'UserApp_Controller/CleaningAPI';
$route['UpdateTareweight'] = 'UserApp_Controller/UpdateTareweightAPI';
// Crop Purchase API END

// Generate API from Application 
$route['TransactionSummery'] = 'UserApp_Controller/TransactionSummeryAPI';
$route['GenerateAsn'] = 'UserApp_Controller/GenerateAsnAPI';

// Commodty Withdrow APIS 

$route['GetItemForWithdraw'] = 'UserApp_Controller/GetItemForWithdrawAPI';
$route['GetWHforWithdrawItemWise'] = 'UserApp_Controller/GetWHforWithdrawItemWiseAPI';
$route['GetStackforWithdrawWHWise'] = 'UserApp_Controller/GetStackforWithdrawWHWiseAPI';
$route['GetLotforWithdrawStackWise'] = 'UserApp_Controller/GetLotforWithdrawStackWiseAPI';
$route['GetLotDetailsByLotID'] = 'UserApp_Controller/GetLotDetailsByLotIDAPI';
$route['GetOutstandingBalance'] = 'UserApp_Controller/GetOutstandingBalanceAPI';
$route['SubmitWithdrawRequest'] = 'UserApp_Controller/SubmitWithdrawRequestAPI';
$route['PaymentStatusWithdraw'] = 'UserApp_Controller/PaymentStatusWithdrawAPI';
$route['UpdateTareWeightWithdrawl'] = 'UserApp_Controller/UpdateTareWeightWithdrawlAPI';
$route['UnloadingInProgressWithdrawl'] = 'UserApp_Controller/UnloadingInProgressWithdrawlAPI';
$route['UpdateUnloadingDetailsWithdrawal'] = 'UserApp_Controller/UpdateUnloadingDetailsWithdrawalAPI';
$route['UpdateQualityWithdrawal'] = 'UserApp_Controller/UpdateQualityWithdrawalAPI';
$route['UpdateLoadedWeightWithdrawal'] = 'UserApp_Controller/UpdateLoadedWeightWithdrawalAPI';



//============ API for Broker Account to create Trader Account =================
$route['AddTraderByBroker'] = 'UserApp_Controller/AddTraderByBrokerAPI';
$route['AddTraderPANByBroker'] = 'UserApp_Controller/AddTraderPANByBrokerAPI';
$route['TraderCRTByBrokerList'] = 'UserApp_Controller/TraderCRTByBrokerListAPI';

//=================== Misc API =================================================
$route['AddWishlist'] = 'UserApp_Controller/AddWishlistAPI';
$route['GetWishlist'] = 'UserApp_Controller/GetWishlistAPI';
// Customer API Count = 99

/* Surway Form API from Staff Application */
$route['AddVersion'] = 'UserApp_Controller/AddVersionAPI';
$route['GetVersion'] = 'UserApp_Controller/GetAppVersionAPI';
$route['AddSurway'] = 'UserApp_Controller/AddSurweyAPI';
$route['AddSurwayDependant'] = 'UserApp_Controller/AddSurwayDependantAPI';
$route['WaterSource'] = 'UserApp_Controller/WaterSourceAPI';
$route['EquipmentOwned'] = 'UserApp_Controller/EquipmentOwnedAPI';
$route['Livestock'] = 'UserApp_Controller/LivestockAPI';
$route['CropPattern'] = 'UserApp_Controller/CropPatternAPI';
$route['ProductionCost'] = 'UserApp_Controller/ProductionCostAPI';
$route['LabourAvailability'] = 'UserApp_Controller/LabourAvailabilityAPI';
$route['GovtSchemes'] = 'UserApp_Controller/GovtSchemesAPI';
$route['SmartPhoneUsage'] = 'UserApp_Controller/SmartPhoneUsageAPI';
$route['GetChamberList'] = 'UserApp_Controller/GetChamberListAPI';
$route['GetChamberListForTrader'] = 'UserApp_Controller/GetChamberListForTraderAPI'; 
$route['GetStackList'] = 'UserApp_Controller/GetStackListAPI';
$route['GetStackListForTrader'] = 'UserApp_Controller/GetStackListForTraderAPI';
$route['GetLotList'] = 'UserApp_Controller/GetLotListAPI';
$route['GetLotListForTrader'] = 'UserApp_Controller/GetLotListForTraderAPI';
$route['AddFarmData'] = 'UserApp_Controller/AddFarmData';

/* Staff Application API */
$route['LOGIN'] = 'UserApp_Controller/loginAPI';
$route['StaffLogOut'] = 'UserApp_Controller/LogOutStaffAPI';
$route['StaffDashboard'] = 'UserApp_Controller/StaffDashboardAPI';
$route['Check_in_out'] = 'UserApp_Controller/check_in_out_API';
$route['GetLocation'] = 'UserApp_Controller/GetLocationAPI';
$route['GetAllStaff'] = 'UserApp_Controller/GetAllStaffAPI';
$route['staff_permission'] = 'UserApp_Controller/Getstaff_permissionAPI';
$route['FPORate'] = 'UserApp_Controller/FPORateAPI';
$route['FarmerDetails'] = 'UserApp_Controller/FarmerDetailsAPI';
$route['FPOCenterList'] = 'UserApp_Controller/FPOCenterListAPI';
$route['FPOItemsList'] = 'UserApp_Controller/FPOItemsListAPI';
$route['SaveFPOOrder'] = 'UserApp_Controller/SaveFPOOrderAPI';
$route['FPOOrderList'] = 'UserApp_Controller/FPOOrderListAPI';
$route['AddNewFarmer'] = 'UserApp_Controller/AddNewFarmerAPI';
$route['K1SaleItemList'] = 'UserApp_Controller/K1SaleItemListAPI';
$route['K1ItemDetailsAndBatchList'] = 'UserApp_Controller/K1ItemDetailsAndBatchListAPI';
$route['K1ItemBatchDetails'] = 'UserApp_Controller/K1ItemBatchDetailsAPI';
$route['K1SaleOnlineLedger'] = 'UserApp_Controller/K1SaleOnlineLedgerAPI';
$route['K1SaleOtherLedger'] = 'UserApp_Controller/K1SaleOtherLedgerAPI';
$route['K1SaleOrderSave'] = 'UserApp_Controller/K1SaleOrderSaveAPI';
$route['K1SaleOrderList'] = 'UserApp_Controller/K1SaleOrderListAPI';
$route['K1SaleOrderDetails'] = 'UserApp_Controller/K1SaleOrderDetailsAPI';
$route['K1SaveReminders'] = 'UserApp_Controller/K1SaveRemindersAPI';
$route['K1ListReminders'] = 'UserApp_Controller/K1ListRemindersAPI';
/* Customer Enquiry*/
$route['AddEnquiry'] = 'UserApp_Controller/CustomerEnquiry';

//=========== Kirti API End ====================================================


$route['VisLocations'] = 'UserApp_Controller/VisLocationsAPI';
$route['GetCustomer'] = 'UserApp_Controller/Get_CustomerAPI';
$route['GetCustomer_new'] = 'UserApp_Controller/Get_CustomerAPI_new';
$route['GetCustomerGroup'] = 'UserApp_Controller/Get_CustomerGroupAPI';
$route['AddCustomer'] = 'UserApp_Controller/addCustomerAPI';
$route['SearchCustomer'] = 'UserApp_Controller/searchCustomerAPI';
$route['SingleCustomer'] = 'UserApp_Controller/singleCustomerAPI';
$route['GetItemDivision'] = 'UserApp_Controller/Get_ItemDevisionAPI';
$route['GetItem_Divwise_list'] = 'UserApp_Controller/Get_ItemDevwise_listAPI';

/* New API*/
$route['GetOfficeAddress'] = 'UserApp_Controller/GetOfficeAddressAPI';
$route['VisLocations_new'] = 'UserApp_Controller/VisLocationsAPI_new';
$route['In_Out_status'] = 'UserApp_Controller/In_Out_statusAPI';
$route['auto_end_day'] = 'UserApp_Controller/auto_end_day_cron';
$route['Get_CityAPI'] = 'UserApp_Controller/Get_CityAPI';
$route['AddEnquiryAPI'] = 'UserApp_Controller/AddEnquiryAPI';
$route['GetEnquiryAPI'] = 'UserApp_Controller/Get_EnquiryAPI';
$route['GetEnqDetailsAPI'] = 'UserApp_Controller/Get_EnqDetailsAPI';
$route['UpdateEnqAPI'] = 'UserApp_Controller/Update_EnqAPI';
$route['AddTourAPI'] = 'UserApp_Controller/AddTourAPI';
$route['GettourAPI'] = 'UserApp_Controller/Get_TourAPI';
$route['GetTeamTourAPI'] = 'UserApp_Controller/GetTeamTourAPI';
$route['Add_versonAPI'] = 'UserApp_Controller/Add_versonAPI';
$route['Get_versonAPI'] = 'UserApp_Controller/Get_versonAPI';
$route['Get_CustDivAPI'] = 'UserApp_Controller/Get_CustDivAPI';
$route['Get_itemlistAPI'] = 'UserApp_Controller/Get_itemlistAPI';
$route['Get_invoice_numberAPI'] = 'UserApp_Controller/Get_order_numberAPI';
$route['Ganerate_hashAPI'] = 'UserApp_Controller/Ganerate_hashAPI';
$route['order_placeAPI'] = 'UserApp_Controller/oder_placeAPI2';
$route['Get_order_list'] = 'UserApp_Controller/Get_order_list_API';
$route['Get_pending_order_list'] = 'UserApp_Controller/Get_pending_order_list_API_new';
$route['Get_pending_order_list_new'] = 'UserApp_Controller/Get_pending_order_list_API_new2';
$route['GetPendingOrders'] = 'UserApp_Controller/GetPendingOrderAPI';
$route['Get_order_detail'] = 'UserApp_Controller/Get_order_detail_API';
$route['Asigned_company'] = 'UserApp_Controller/Asigned_company';
$route['All_Item_price_List'] = 'UserApp_Controller/All_Item_price_List';

$route['All_dist_type'] = 'UserApp_Controller/All_dist_type';

$route['Get_my_team'] = 'UserApp_Controller/Get_my_team_API';
$route['Get_staff_detail'] = 'UserApp_Controller/Get_staff_detail_API';

$route['Get_sale_reports'] = 'UserApp_Controller/Get_sale_reports_API';
$route['Get_account_ledger'] = 'UserApp_Controller/Get_account_ledger_API';
$route['update_tour_planAPI'] = 'UserApp_Controller/update_tour_plan_API';
$route['SubmitTPlanAPI'] = 'UserApp_Controller/SubmitTPlanAPI';
$route['detail_tour_planAPI'] = 'UserApp_Controller/detail_tour_planAPI';
$route['parties_not_billedAPI'] = 'UserApp_Controller/parties_not_billedAPI';
$route['item_not_billedAPI'] = 'UserApp_Controller/item_not_billedAPI';

/* End New API */
/* New1 API */
$route['get_achievementAPI'] = 'UserApp_Controller/get_achievementAPI';
$route['get_targetAPI'] = 'UserApp_Controller/get_targetAPI';

$route['get_targetAchievementAPI'] = 'UserApp_Controller/get_targetandAchievementAPI';
$route['get_division_targetAchievementAPI'] = 'UserApp_Controller/get_division_targetandAchievementAPI';
/*Expence API*/
$route['AddExpenseAPI'] = 'UserApp_Controller/AddExpense_API';
$route['GetExpenseAPI'] = 'UserApp_Controller/GetExpense_API'; 
$route['CheckAccountID'] = 'UserApp_Controller/CheckAccountCodeAPI';
/*Kata API*/
$route['ProOrderKata'] = 'UserApp_Controller/GetProccesDataAPI';
$route['ProOrderVehicles'] = 'UserApp_Controller/GetProccesOrdersDataAPI';
$route['GetTravelData'] = 'UserApp_Controller/GetTravelData';
$route['GetTimesheetReport'] = 'UserApp_Controller/GetTimesheetReport';
/* End New1 API */

/* API for news */
$route['newsList'] = 'UserApp_Controller/GetNewsAPI';

/* End API for news */

// Expense API New
$route['GetExpenseCategory'] = 'UserApp_Controller/GetExpenseCategoryAPI';
$route['AddExpense'] = 'UserApp_Controller/AddExpenseAPI';
$route['GetExpense'] = 'UserApp_Controller/GetExpenseAPI';
$route['UpdateExpense'] = 'UserApp_Controller/UpdateExpenseAPI';
/**
 * Dashboard clean route
 */
$route['admin'] = 'admin/dashboard';

/**
 * Misc controller routes
 */
$route['admin/access_denied'] = 'admin/misc/access_denied';
$route['admin/not_found']     = 'admin/misc/not_found';

/**
 * Staff Routes
 */
$route['admin/profile']           = 'admin/staff/profile';
$route['admin/profile/(:num)']    = 'admin/staff/profile/$1';
$route['admin/tasks/view/(:any)'] = 'admin/tasks/index/$1';

/**
 * Items search rewrite
 */
$route['admin/items/search'] = 'admin/invoice_items/search';

/**
 * In case if client access directly to url without the arguments redirect to clients url
 */
$route['/'] = 'clients';

/**
 * @deprecated
 */
$route['viewinvoice/(:num)/(:any)'] = 'invoice/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['invoice/(:num)/(:any)'] = 'invoice/index/$1/$2';

/**
 * @deprecated
 */
$route['viewestimate/(:num)/(:any)'] = 'estimate/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['estimate/(:num)/(:any)'] = 'estimate/index/$1/$2';
$route['subscription/(:any)']    = 'subscription/index/$1';

/**
 * @deprecated
 */
$route['viewproposal/(:num)/(:any)'] = 'proposal/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['proposal/(:num)/(:any)'] = 'proposal/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['contract/(:num)/(:any)'] = 'contract/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['knowledge-base']                 = 'knowledge_base/index';
$route['knowledge-base/search']          = 'knowledge_base/search';
$route['knowledge-base/article']         = 'knowledge_base/index';
$route['knowledge-base/article/(:any)']  = 'knowledge_base/article/$1';
$route['knowledge-base/category']        = 'knowledge_base/index';
$route['knowledge-base/category/(:any)'] = 'knowledge_base/category/$1';

/**
 * @deprecated 2.2.0
 */
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'add_kb_answer') === false) {
    $route['knowledge-base/(:any)']         = 'knowledge_base/article/$1';
    $route['knowledge_base/(:any)']         = 'knowledge_base/article/$1';
    $route['clients/knowledge_base/(:any)'] = 'knowledge_base/article/$1';
    $route['clients/knowledge-base/(:any)'] = 'knowledge_base/article/$1';
}

/**
 * @deprecated 2.2.0
 * Fallback for auth clients area, changed in version 2.2.0
 */
$route['clients/reset_password']  = 'authentication/reset_password';
$route['clients/forgot_password'] = 'authentication/forgot_password';
$route['clients/logout']          = 'authentication/logout';
$route['clients/register']        = 'authentication/register';
$route['clients/login']           = 'authentication/login';

// Aliases for short routes
$route['reset_password']  = 'authentication/reset_password';
$route['forgot_password'] = 'authentication/forgot_password';
$route['login']           = 'authentication/login';
$route['logout']          = 'authentication/logout';
$route['register']        = 'authentication/register';

/**
 * Terms and conditions and Privacy Policy routes
 */
$route['terms-and-conditions'] = 'terms_and_conditions';
$route['privacy-policy']       = 'privacy_policy';

/**
 * @since 2.3.0
 * Routes for admin/modules URL because Modules.php class is used in application/third_party/MX
 */
$route['admin/modules']               = 'admin/mods';
$route['admin/modules/(:any)']        = 'admin/mods/$1';
$route['admin/modules/(:any)/(:any)'] = 'admin/mods/$1/$2';

// Public single ticket route
$route['forms/tickets/(:any)'] = 'forms/public_ticket/$1';

/**
 * @since  2.3.0
 * Route for clients set password URL, because it's using the same controller for staff to
 * If user addded block /admin by .htaccess this won't work, so we need to rewrite the URL
 * In future if there is implementation for clients set password, this route should be removed
 */
$route['authentication/set_password/(:num)/(:num)/(:any)'] = 'admin/authentication/set_password/$1/$2/$3';

// For backward compatilibilty
$route['survey/(:num)/(:any)'] = 'surveys/participate/index/$1/$2';

if (file_exists(APPPATH . 'config/my_routes.php')) {
    include_once(APPPATH . 'config/my_routes.php');
}
