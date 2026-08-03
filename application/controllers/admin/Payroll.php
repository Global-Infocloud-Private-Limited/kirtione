<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Payroll extends AdminController
	{
	
		public function __construct()
		{
			parent::__construct();
			$this->load->model('payroll_model');
		}
		
		public function salaryComponents()
		{
		    if (!has_permission_new('salaryComponents', '', 'view')) {
				access_denied('salaryComponents');
			}
		    $data['title'] = "Salary Components";
		    $data['salary_head_table'] = $this->payroll_model->get_head_data();
		    $data['company_detail'] = $this->payroll_model->get_company_detail();
		    $this->load->view('admin/payroll/salaryComponents', $data);
		}
		public function GetSalaryHeadList()
		{
			$headList = $this->payroll_model->get_head_data();
			echo json_encode($headList);
		}
		public function get_salary_head_details()
		{
			$headCode = $this->input->post('head_code');
			$head_data = $this->payroll_model->get_salary_head_details($headCode);
			echo json_encode($head_data);
		}
		
		public function SaveHead()
		{
		    if (!has_permission_new('salaryComponents', '', 'create')) {
				access_denied('salaryComponents');
			}
			$data = array(
                'code'=>$this->input->post('HeadCode'),
                'name'=>$this->input->post('HeadName'),
                'type'=>$this->input->post('type'),
                'mesuredIn'=>$this->input->post('mesuredIn'),
                'UserID'=>$this->session->userdata('username'),
                'TransDate'=>date('Y-m-d H:i:s'),
			);
			if($this->input->post('type') == "1"){
			    $data['ESIC_Calculated'] = $this->input->post('ESIC_Calculated');
			}else{
			    $data['ESIC_Calculated'] = 'N';
			}
			if($this->input->post('mesuredIn') == "2"){
			    $data['percentage'] = $this->input->post('percentage');
			    $data['calculatedBy'] = $this->input->post('calculatedBy');
			    $data['auto_calculate'] = 'Y';
			}
			$SalaryHead  = $this->payroll_model->SaveHead($data);
			echo json_encode($SalaryHead);
		}
		
		public function UpdateSalaryHead()
		{
		    if (!has_permission_new('salaryComponents', '', 'edit')) {
				access_denied('salaryComponents');
			}
			$data = array(
                'name'=>$this->input->post('HeadName'),
                'type'=>$this->input->post('HeadType'),
                'mesuredIn'=>$this->input->post('measuredIn'),
                'UserID2'=>$this->session->userdata('username'),
                'Lupdate'=>date('Y-m-d H:i:s'),
			);
			if($this->input->post('HeadType') == "1"){
			    $data['ESIC_Calculated'] = $this->input->post('ESIC_Calculated');
			}else{
			    $data['ESIC_Calculated'] = 'N';
			}
			if($this->input->post('measuredIn') == "2"){
			    $data['percentage'] = $this->input->post('percentage');
			    $data['calculatedBy'] = $this->input->post('calculatedBy');
			    $data['auto_calculate'] = 'Y';
			}else{
			    $data['percentage'] = NULL;
			    $data['calculatedBy'] = NULL;
			    $data['auto_calculate'] = 'N';
			}
			$HeadCode = $this->input->post('HeadCode');
			$HeadID  = $this->payroll_model->UpdateHead($data,$HeadCode);
			echo json_encode($HeadID);
		}
		
	public function SalaryMaster()
    {
        if (!has_permission_new('salarymaster', '', 'view')) {
			access_denied('salaryHead');
		}
        if($this->input->post()){
            if (!has_permission_new('salarymaster', '', 'edit')) {
			    access_denied('salaryHead');
		    }
            $inputData = $this->input->post();
            $result = $this->payroll_model->SaveSalaryDetails($inputData);
            if($result){
                set_alert('success', 'Salary updated successfully');
            }else{
                set_alert('warning', 'something went wrong please try again.');
            }
            $redUrl = admin_url('payroll/SalaryMaster');
			redirect($redUrl);
            /*echo "<pre>";
            print_r($inputData);
            die;*/
        }
        $data['title'] = 'Salary Master';
        $data['ActiveStaff'] = $this->payroll_model->GetActiveStaff();
        $data['SalaryHead'] = $this->payroll_model->GetSalaryHead();
        $data['SalaryDetails'] = $this->payroll_model->GetSalaryDetails();
        /*echo "<pre>";
        print_r($data['SalaryDetails']);
        die;*/
        
        $this->load->view('admin/payroll/SalaryMaster',$data);     
    }
    public function Staff_payroll()
    {
        if (!has_permission_new('salarymaster', '', 'view')) {
			access_denied('salaryHead');
		}
		$data['title'] = 'Staff Payroll';
        // $data['ActiveStaff'] = $this->payroll_model->GetActiveStaff();
        // $data['SalaryHead'] = $this->payroll_model->GetSalaryHead();
        // $data['SalaryDetails'] = $this->payroll_model->GetSalaryDetails();
        
        $this->load->view('admin/payroll/GenerateSalary',$data);
    }
    
    public function get_Staff_payroll()
    {
       $ActiveStaff =  $this->payroll_model->GetActiveStaff();
       $SalaryHead = $this->payroll_model->GetSalaryHead();
       $SalaryDetails = $this->payroll_model->GetSalaryDetails();
       
       $html = '';
        $html .= '<table class="tree table table-striped table-bordered table-daily_report tableFixHead2" id="table-daily_report" width="100%">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th style="text-align:center;" class="for-item-idth">EMP Code</th>';
        $html .= '<th style="text-align:center;" class="for-item-nameth">EMP Name</th>';
            $EHead = 0;
            $DHead = 0;
                foreach($SalaryHead as $Key=>$val){
                    if($val['type']=="1"){
                        $EHead++;
                    }else{
                        $DHead++;
                    }
                }
        $details_col = $DHead + $EHead + 6;
        $html .= '<th style="text-align:center;" class="for-item-nameth" >Net Salary</th>';
        //$html .= '<th style="text-align:center;" class="for-item-nameth" colspan="'.$DHead.'">Deductions</th>';
        $html .= '<th style="text-align:center;" class="for-item-nameth" colspan="2">Summary</th>';
        $html .= '<th style="text-align:center;" class="for-item-nameth" colspan="'.$details_col.'">Salary Details</th>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="text-align:center;" class="for-item-idth" colspan="2"></td>';
        foreach($SalaryHead as $Key1=>$val1){
            if($val1['code'] == "NET"){
                if($val1['mesuredIn']=="1"){
                   $ValueType = "Amt"; 
                }else{
                    $ValueType = "%"; 
                }
                $html .= '<td style="text-align:center;" class="for-item-idth"><b>'.$val1['code'].' ('.$ValueType.')'.'</b></td>';
            }
        }
        //$html .= '<td>Monthly Gross</td>';
        //$html .= '<td>Monthly Deduction</td>';
        $html .= '<td>Working Days</td>';
        $html .= '<td>Present Days</td>';
        $html .= '<td>Absent Days</td>';
            foreach($SalaryHead as $Key1=>$val1){
                if($val1['mesuredIn']=="1"){
                   $ValueType = "Amt"; 
                }else{
                    $ValueType = "%"; 
                }
            $html .= '<td style="text-align:center;" class="for-item-idth"><b>'.$val1['code'].' ('.$ValueType.')'.'</b></td>';
            }
        $html .= '<td>Monthly Gross</td>';
        $html .= '<td>Monthly Deduction</td>';
        $html .= '<td>Net Amount</td>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody id="rate_update_table">';
        $html .= '<tr>';
        foreach($ActiveStaff as $staffKey=>$staffval){
        $html .= '<td style="text-align:left;" class="for-item-idth"><b>'.$staffval['AccountID'].'</b></td>';
        $html .= '<td style="text-align:left;" class="for-item-idth"><b>'.$staffval['firstname'].' '.$staffval['lastname'].'</b></td>';
            $deduction = 0;
            $earning = 0;
            $basic = 0;
            $NetPayable = 0;
            $NetAmt = 0;
            foreach($SalaryHead as $Key1=>$val1){
                if($val1['code'] == "NET"){
                    $value = '';
                    foreach($SalaryDetails as $salaryKey=>$salaryValue){
                        if($staffval['AccountID'] == $salaryValue['AccountID'] && $val1['code']==$salaryValue['HeadID']){
                            $value = $salaryValue['value'];
                        }
                    }
                    $html .= '<td style="text-align:right;" class="for-item-idth">'.$value.'</td>';
                }
            }
            $NetPayable = $earning - $deduction;
            $YearlyCTC = $earning * 12;
            
            //$html .= '<td style="text-align:right;'.$css.'" id="total_earning_td_'.$staffval['AccountID'].'" ><span id="total_earning_html_'.$staffval['AccountID'].'">'.$earning.'</span><input type="hidden" name="total_earning_'.$staffval['AccountID'].'" id="total_earning_'.$staffval['AccountID'].'"></td>';
            //$html .= '<td style="text-align:right;'.$css.' " id="total_deduction_td_'.$staffval['AccountID'].'" ><span id="total_deduction_html_'.$staffval['AccountID'].'">'.$deduction.'</span><input type="hidden" name="total_deduction_'.$staffval['AccountID'].'" id="total_deduction_'.$staffval['AccountID'].'"></td>';
            $working = 30 ;
            $present = 20 ;
            $html .= '<td>'.$working.'</td>';
            $html .= '<td>'.$present.'</td>';
            $absent = $working-$present;
            $html .= '<td>'.$absent.'</td>';
            foreach($SalaryHead as $Key1=>$val1){
            $value = '';
            $css = '';
            $PRDayAmt = 0;
            if($val1['auto_calculate']=="Y"){
                $css = 'readonly';
            }
            
                foreach($SalaryDetails as $salaryKey=>$salaryValue){
                    if($staffval['AccountID'] == $salaryValue['AccountID'] && $val1['code']==$salaryValue['HeadID']){
                        $value = $salaryValue['value'];
                    }
                }
                if($val1['type'] == '1' && $val1['code'] != "NET"){
                    $earning += $value;
                }else if($val1['type'] == '2' && $val1['code'] != "NET"){
                    $deduction += $value;
                }
                if($val1['code'] == "NET"){
                    $NetAmt = $value;
                }
                $oneDayAmt = $value / $working;
                $PRDayAmt = $oneDayAmt * $present;
            //$html .= '<td style="text-align:right;" class="for-item-idth">'.$value.'</td>';
            $html .= '<td style="text-align:right;" class="for-item-idth"><input type="text"'.$css.' class="AmtEnter form-control" name="Amt_'.$staffval['AccountID'].'_'.$val1['code'].'" id="Amt_'.$staffval['AccountID'].'_'.$val1['code'].'" value="'.number_format($PRDayAmt, 2, '.', '').'" style="width: 80px;" onchange = "'.$functonName.'(this.id,this.value)"></td>';    
            }
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '</tr>';
            }
            $html .= '</tbody>';
            $html .= '</table>'; 
        echo json_encode($html);
    }

}