<?php

defined('BASEPATH') or exit('No direct script access allowed');

class TargetvsAttunement extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Targetvsattunement_model');
      
    }

    public function AddEditTargetvsattunment($SINumber = '')
	{
			if (!has_permission_new('TargetvsAttunement', '', 'view')) {
				access_denied('target vs attunement');
			}   		
			if ($this->input->post()) {
				$pur_order_data = $this->input->post();	
				
				//$pur_order_data['terms'] = nl2br($pur_order_data['terms']);
				//if ($pur_order_data == '') {
				
					if (!has_permission_new('TargetvsAttunement', '', 'create')) {
						access_denied('TargetvsAttunement');
					}
					$id = $pur_order_data['AccountID'];
					$result = $this->Targetvsattunement_model->AddKirtiOneTargetvsattunementNew($pur_order_data, true, $id);

					if ($result) {
						set_alert('success', _l('added_successfully'));
						redirect(admin_url('TargetvsAttunement/AddEditTargetvsattunment'));
					}
			}
			$StaffList = $this->Targetvsattunement_model->GetcompanyStaff();
			$data['pur_order_detail'] = json_encode($StaffList); 
			
			$title = "Edit Sale Invoice";
			
			$data['staff_list'] = $this->Targetvsattunement_model->GetcompanyStaff();
			$data['item_code'] = $this->Targetvsattunement_model->get_items_code();	
			$data['statelist'] = $this->Targetvsattunement_model->getstatelist();
			$data['company_detail'] = $this->Targetvsattunement_model->get_company_detail();
			
			$this->load->view('admin/targetvsattunement/Target_vs_Attunement',$data);
		}
		
		public function GetPIByCenterWiseVendor()  
		{
			$CenterID = $this->input->post('CenterID');
			$data = $this->Targetvsattunement_model->PendingInvoiceCenterwiseClients($CenterID);
			echo json_encode($data);
		}
		public function GetPIByVendorAndCenter()
		{
			$VenId = $this->input->post('VenId');
			$CenterID = $this->input->post('CenterID');
			$data = $this->Targetvsattunement_model->get_order_PI_ven_center_details($VenId,$CenterID);
			echo json_encode($data);
		}
		public function GetSIretuenItemData(){
			// POST data
			$PINo = $this->input->post('PINo');
			// Get data
	$InwardData['historytbl'] = $this->Targetvsattunement_model->GetReturnSaleOrderItemListForInv($PINo);
			// echo "<pre>";
			// print_r($InwardData['historytbl']);
			// die;
			echo json_encode($InwardData);
		}
		
}
?>