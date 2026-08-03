<?php

defined('BASEPATH') or exit('No direct script access allowed');

use app\services\ValidatesContact;

class PurchaseInvoiceMaster extends ClientsController
{
    use ValidatesContact;
    public function __construct()
    {
        parent::__construct();
        hooks()->do_action('after_clients_area_init', $this);
        $this->load->model('taxes_model');
        $this->load->model('hsn_master_model');
        $this->load->model('ItemModel');
		$this->load->model('PurchaseModel');
        $this->load->helper('url', 'form');		
		$this->load->model('K1Stock_transfer_model');
    }
    
    public function index()
    {
        $LogInUser = $this->session->userdata('AccountID');
		$data['AccountID'] = $LogInUser;
        $data['company_detail'] = $this->ItemModel->get_company_detail();      
        
		$data['centermaster'] = $this->PurchaseModel->GetPurchOrderCenterList();
		$data['products'] = $this->PurchaseModel->GetPurchOrderItemList();			
		
		$this->db->where('tblclients.AccountID',$LogInUser);	
		$clients = $this->db->get('tblclients')->row();			
		$data['company'] = $clients->company;
		
		$data['title'] = "Purchase Invoice Master";
        $this->data($data);
        $this->view('PurchaseInvoiceMaster/PurchaseInvoiceList');
        $this->layout();
    }
//======================= Vendor Purchase Invoice List =========================
    public function GetPurchaseInvoiceData()
	{
		 $data = array(
            'from_date' => $this->input->post('from_date'),
            'to_date' => $this->input->post('to_date'),
            'AccountID'=>$this->input->post('AccountID'),
            'CenterID'=>$this->input->post('CenterID')
        );
        $result = $this->PurchaseModel->GetPurchaseInvoiceInfo($data); 
       
        $html = '';
        $PurchAmt = 0;
        $totalDiscountAmtSum =0;
    	$ToatlCgstAmt = 0;
    	$TotalSgstAmt = 0;
    	$TotalIgstAmt =0;
    	$TotalInvAmt = 0;
        foreach($result as $key=>$value)
    	{
    	    $url = base_url()."PurchaseInvoiceMaster/PurchaseInvoiceDetails/".$value["Inv_No"];
	    	$html .= '<tr onclick="window.open(\''.$url.'\', \'_blank\')">';         
            $html .= '<td style="text-align:center;">'.($key+1).'</td>';   
   	        $html .= '<td style="text-align:center;">'.$value["Inv_No"].'</td>';	
            $html .= '<td style="text-align:center;">'._d(substr($value["Inv_date"],0,10)).'</td>';  
            $html .= '<td style="text-align:center;">'.$value["PurchID"].'</td>';
            $html .= '<td style="text-align:center;">'.$value["Pr_no"].'</td>';
  	        $html .= '<td>'.$value['CenterName'].'</td>';       
 	        $html .= '<td style="text-align:right;">' . number_format($value["Purchamt"], 2, '.', '') . '</td>';
            $html .= '<td style="text-align:right;">' . number_format($value["Discamt"], 2, '.', '') . '</td>';
            $html .= '<td style="text-align:right;">' . number_format($value["cgstamt"], 2, '.', '') . '</td>';
            $html .= '<td style="text-align:right;">' . number_format($value["sgstamt"], 2, '.', '') . '</td>'; 
            $html .= '<td style="text-align:right;">' . number_format($value["igstamt"], 2, '.', '') . '</td>'; 
            $html .= '<td style="text-align:right;">' . number_format($value["Invamt"], 2, '.', '') . '</td>'; 
                
	        $html .= '</tr>'; 
	        
	        $PurchAmt += $value["Purchamt"];
	        $totalDiscountAmtSum += $value["Discamt"];
	        $ToatlCgstAmt += $value["cgstamt"];
	        $TotalSgstAmt += $value["sgstamt"];
	        $TotalIgstAmt += $value["igstamt"];
	        $TotalInvAmt += $value["Invamt"];
    	}
    	
    	$html .= '<tr>';
        $html .= '<td colspan="6" style="text-align:right;"><strong>Total</strong></td>';
        $html .= '<td style="text-align:right;"><strong>' . number_format($PurchAmt, 2, '.', '') . '</strong></td>';
        $html .= '<td style="text-align:right;"><strong>' . number_format($totalDiscountAmtSum, 2, '.', '') . '</strong></td>';
        $html .= '<td style="text-align:right;"><strong>' . number_format($ToatlCgstAmt, 2, '.', '') . '</strong></td>';
        $html .= '<td style="text-align:right;"><strong>' . number_format($TotalSgstAmt, 2, '.', '') . '</strong></td>';
        $html .= '<td style="text-align:right;"><strong>' . number_format($TotalIgstAmt, 2, '.', '') . '</strong></td>';
        $html .= '<td style="text-align:right;"><strong>' . number_format($TotalInvAmt, 2, '.', '') . '</strong></td>';
        $html .= '</tr>';
    	
        echo $html;
	}
	
	public function PurchaseInvoiceDetails($id)
	{
	    $LogInUser = $this->session->userdata('AccountID');
		$data['AccountID'] = $LogInUser;
        $data['company_detail'] = $this->ItemModel->get_company_detail();      
        
		$data['centermaster'] = $this->PurchaseModel->GetPurchOrderCenterList();
		$data['products'] = $this->PurchaseModel->GetPurchOrderItemList();	
		
		$data['PurchaseInvoiceData'] = $this->PurchaseModel->GetPurchaseInvoiceByInvoiceNo($id);
		
		$this->db->where('tblclients.AccountID',$LogInUser);	
		$clients = $this->db->get('tblclients')->row();			
		$data['company'] = $clients->company;
		$data['id'] = $id;
		 
		$data['title'] = "Purchase Invoice Details";
        $this->data($data);
        $this->view('PurchaseInvoiceMaster/PurchaseInvoiceDetails');
        $this->layout();
	}
	
	public function GetPurchInvoiceList()
	{
	    $ID = $this->input->post('ID');
	    $result = $this->PurchaseModel->GetInvoiceHistoryDetails($ID); 
	    
	    $html = '';
        $html .= '<thead>';
        
        $html .= '<tr>';
        $html .= '<th style="text-align:center;">Sr No.</th>';
        $html .= '<th style="text-align:center;">Item Name</th>';
        $html .= '<th style="text-align:center;">Hsn Code</th>';
        $html .= '<th style="text-align:center;">Brand</th>';            
        $html .= '<th style="text-align:center;">Measured In</th> ';           
        $html .= '<th style="text-align:center;">Packing Qty</th> ';
        $html .= '<th style="text-align:center;">Packing Weight</th> ';
        $html .= '<th style="text-align:center;">Purchase Unit</th> ';
        $html .= '<th style="text-align:center;">Qty</th>';
        $html .= '<th style="text-align:center;">Purchase Rate</th>';                                           
        $html .= '<th style="text-align:center;">Discount(%)</th>';        
        $html .= '<th style="text-align:center;">GST(%)</th>';  
        $html .= '<th style="text-align:center;">CGSTAMT</th>';   
        $html .= '<th style="text-align:center;">SGSTAMT</th>';
        $html .= '<th style="text-align:center;">IGSTAMT</th>';
        $html .= '<th style="text-align:center;">Net Amt</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody id="filter_data_table">';
        
        $PurchAmt = 0;
        $totalDiscountAmtSum =0;
    	$ToatlCgstAmt = 0;
    	$TotalSgstAmt = 0;
    	$TotalIgstAmt =0;
    	$TotalInvAmt = 0;
        foreach($result as $key=>$value)
    	{
    	    	$html .= '<tr>';        
                $html .= '<td style="text-align:center;">'.($key+1).'</td>';   
       	        $html .= '<td style="text-align:center;">'.$value["ProductName"].'</td>';	
       	        $html .= '<td style="text-align:center;">'.$value["hsn_code"].'</td>';	
       	        $html .= '<td style="text-align:center;">'.$value["BrandName"].'</td>';	
       	        $html .= '<td style="text-align:center;">'.$value["unit"].'</td>';	
       	        $html .= '<td style="text-align:center;">'.$value["PackingQty"].'</td>';	
       	        $html .= '<td style="text-align:center;">'.$value["PackingWeight"].'</td>';	
       	        $html .= '<td style="text-align:center;">'.$value["unit"].'</td>';	
       	        $html .= '<td style="text-align:right;">' . number_format($value["BilledQty"], 2, '.', '') . '</td>';
       	        $html .= '<td style="text-align:right;">' . number_format($value["PurchRate"], 2, '.', '') . '</td>';
       	        $html .= '<td style="text-align:right;">' . number_format($value["DiscPerc"], 2, '.', '') . '</td>';
       	        $html .= '<td style="text-align:right;">' . number_format($value["taxrate"], 2, '.', '') . '</td>';
                $html .= '<td style="text-align:right;">' . number_format($value["cgstamt"], 2, '.', '') . '</td>';
                $html .= '<td style="text-align:right;">' . number_format($value["sgstamt"], 2, '.', '') . '</td>'; 
                $html .= '<td style="text-align:right;">' . number_format($value["igstamt"], 2, '.', '') . '</td>'; 
                $html .= '<td style="text-align:right;">' . number_format($value["NetOrderAmt"], 2, '.', '') . '</td>'; 
                    
    	        $html .= '</tr>'; 
    	        
    	        $PurchAmt += $value["Purchamt"];
    	        $totalDiscountAmtSum += $value["Discamt"];
    	        $ToatlCgstAmt += $value["cgstamt"];
    	        $TotalSgstAmt += $value["sgstamt"];
    	        $TotalIgstAmt += $value["igstamt"];
    	        $TotalInvAmt += $value["Invamt"];
    	}
    	$html .= '</body>';
        echo $html;
	}
}