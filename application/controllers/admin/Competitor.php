<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Competitor extends AdminController
{
    public function __construct(){
        parent::__construct();
        $this->load->model('clients_model');
        $this->load->Model('Competitor_model');
        $this->load->model('accounts_master_model');
    }
    
    public function index(){
        if (!has_permission_new('Competitor', '', 'view')) {
            access_denied('invoices');
        }
        $data['title'] = 'Add/Edit Competitor';
        $data['company_detail'] = $this->accounts_master_model->get_company_detail();
        $data['table_data'] = $this->Competitor_model->getCompetitor();
        $this->load->view('admin/Competitor/competitor',$data);   
    }
    
    public function getSingleCompetitor(){
        $CompetitorID = $this->input->post('CompetitorID');
        $result = $this->Competitor_model->getSingleCompetitorDb($CompetitorID);
        echo json_encode($result); 
    }
    
    public function saveCompetitor(){
        if (!has_permission_new('Competitor', '', 'create')) {
            access_denied('invoices');
        }
        $data = array(
            'CompetitorID' => $this->input->post('CompetitorID'),
            'Competitor' => $this->input->post('Competitor'),
            'Type' => $this->input->post('Type')
        );
        $result = $this->Competitor_model->saveCompetitorDb($data);
        echo json_encode($result);
    }
    
    public function updateCompetitor(){
        
        if (!has_permission_new('Competitor', '', 'edit')) {
            access_denied('invoices');
        }
        $data = array(
            'CompetitorID' => $this->input->post('CompetitorID'),
            'Competitor' => $this->input->post('Competitor'),
            'Type' => $this->input->post('Type')
        );
        $result = $this->Competitor_model->updateCompetitorDb($data);
        echo json_encode($result);
    }
    
    public function AccountListPopUp()
    {
        $table_data = $this->Competitor_model->getCompetitor();
        
        $html = "";
        $sr = 1;
        foreach ($table_data as $key => $value) {
            $html .= '<tr class="get_AccountID" data-id="'.$value["CompetitorID"].'">';
            $html .= '<td>'.$sr.'</td>';
            $html .= '<td>'.$value["CompetitorID"].'</td>';
            $html .= '<td>'.$value['Competitor'].'</td>';
            if($value['Type'] == "C"){
                $TypeName = "Competitor";
            }else if($value['Type'] == "M"){
                $TypeName = "Mandi";
            }else{
                $TypeName = "";
            }
            $html .= '<td>'.$TypeName.'</td>';
            $html .= '</tr>';
            $sr++;
        }
        echo $html;
        //echo json_encode($account_data);
     }
     
    public function export_CompetitoMandiListMaster(){
        if(!class_exists('XLSXReader_fin')){
            require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
        }
        require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
        
        if($this->input->post()){
            
            $company_detail = $this->clients_model->get_company_detail();
            $result= $this->Competitor_model->getCompetitor();
           
            $writer = new XLSXWriter();
            
            $company_name = array($company_detail->company_name);
    		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_name);

            $address = $company_detail->address;
    		$center_addr = array($address,);
    		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells
    		$writer->writeSheetRow('Sheet1', $center_addr);
            
            
            $set_col_tk = [];
            $set_col_tk["CompetitorID "] =  'CompetitorID ';
            $set_col_tk["Competitor Name"] = 'Competitor Name';
            $set_col_tk["Type"] = 'Type';
            $writer_header = $set_col_tk;
            $writer->writeSheetRow('Sheet1', $writer_header);
            foreach ($result as $k => $value) {
                
                $list_add = [];
                $list_add[] = $value["CompetitorID"];
                $list_add[] = $value["Competitor"];
                if($value['Type'] == "C"){
                    $TypeName = "Competitor";
                }else if($value['Type'] == "M"){
                    $TypeName = "Mandi";
                }else{
                    $TypeName = "";
                }  
                $list_add[] = $TypeName;
                $list_add[] = $row_a;
                
                $writer->writeSheetRow('Sheet1', $list_add);
            
            }
    
            $files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
            foreach($files as $file){
                if(is_file($file)) {
                    unlink($file); 
                }
            }
            $filename = 'CompetitorMaster.xlsx';
            $writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
            echo json_encode([
                'site_url'          => site_url(),
                'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
            ]);
            die;
        }
    }
}