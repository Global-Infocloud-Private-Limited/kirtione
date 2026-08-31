<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

class UserApp_Model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_autologin');
        $this->autologin();
    }

    /**
     * @param  string Email address for login
     * @param  string User Password
     * @param  boolean Set cookies for user if remember me is checked
     * @param  boolean Is Staff Or Client
     * @return boolean if not redirect url found, if found redirect to the url
     */
    public function login($mobile, $password, $staff, $DeviceID, $fcm_token)
    {
         
        if ((!empty($mobile)) and (!empty($password))) {
            if ($staff == true) {
                $table = db_prefix() . 'staff';
            }
            $this->db->select('tblstaff.*,tblhr_job_position.position_name,tblroles.name As RoleName');
            $this->db->where('phonenumber', $mobile);
            $this->db->join(db_prefix() . 'hr_job_position', db_prefix() . 'hr_job_position.position_id = '.db_prefix() . 'staff.job_position',"LEFT");
            $this->db->join(db_prefix() . 'roles', db_prefix() . 'roles.roleid = '.db_prefix() . 'staff.role',"LEFT");
            $user = $this->db->get($table)->row();
            if ($user) {
                // Email is okey lets check the password now
                if (!app_hasher()->CheckPassword($password, $user->password)) {
                    $response=array("status"=>false,"message"=>"You have Enter Wrong Password","user_data"=>null);
                    return $response;
                } else {
                    if ($user->active == 0) {
                        $response=array("status"=>false,"message"=>"Your Account InActive","user_data"=>null);
                        return $response;
                    } else{
                        if($user->app_access == "Yes"){ 
                            if($user->DeviceID == null || $user->DeviceID == '' || $mobile == "9860116842"){
                                $DeviceID_array = array(
                                    "DeviceID"=>$DeviceID
                                );
                                $this->db->set($DeviceID_array);
                                $this->db->where("staffid", $user->staffid);
                                $this->db->update($table);
                                $DivID = $DeviceID;
                            }else{
                                $DivID = $user->DeviceID;
                            }
                            if($DivID == $DeviceID){
                                $state_id = $user->state;
                                $table2 = db_prefix() . 'xx_statelist';
                                $this->db->where('id', $state_id);
                                $state_data = $this->db->get($table2)->row();
                                
                                $city_id = $user->city;
                                $table3 = db_prefix() . 'xx_citylist';
                                $this->db->where('id', $city_id);
                                $city_data = $this->db->get($table3)->row();
                                
                                // Get Department
                                $this->db->select(db_prefix() . 'staff_departments.departmentid AS DeptID,'.db_prefix() . 'staff_departments.staffid AS StaffID,'.db_prefix() . 'departments.name');
                                $this->db->from(db_prefix() . 'staff_departments');
                                $this->db->join(db_prefix() . 'departments', db_prefix() . 'departments.departmentid = '.db_prefix() . 'staff_departments.departmentid');
                                $this->db->where(db_prefix() . 'staff_departments.staffid', $user->staffid);
                                $Departments = $this->db->get()->result_array();
                                
                                $token = bin2hex(random_bytes(16));
                                $this->db->where('staffid', $user->staffid);
                                $this->db->set(['login_tokan' => $token, 'fcm_token' => $fcm_token]);
                                $this->db->update('tblstaff');
                                
                                $user_data=array(
                                    "userId"    => $user->staffid,
                                    "name"      => $user->firstname.' '.$user->lastname,
                                    "email"     => $user->email,
                                    "mobile"    => $user->phonenumber,
                                    "state"     => $state_data->state_name,
                                    "city"      => $city_data->city_name,
                                    "status"    => "Active",
                                    "SubActGroupID" => $user->SubActGroupID,
                                    "admin"     => $user->admin,
                                    "app_access"=> $user->app_access,
                                    "DeptData"  => $Departments,
                                    "job_position"  => $user->job_position,
                                    "job_position_name" => $user->position_name,
                                    "login_tokan"   => $token,
                                    "fcm_token"     => $fcm_token,
                                    'role_id'   =>$user->role,
                                    'role_name' =>$user->RoleName
                                );
                                $response=array("status"=>true,"message"=>"You have logged in successfully","user_data"=>$user_data);
                                return $response;
                            }else{
                                $response=array("status"=>false,"message"=>"Your device is not registered. Please contact to admin.","user_data"=>null);
                                return $response;
                            }
                        }else {
                            $response=array("status"=>false,"message"=>"You are Not Authirized to Login Hare..!","user_data"=>null);
                            return $response;
                        }
                    }
                }
            } else {
                $response=array("status"=>false,"message"=>"You have Enter wrong details.","user_data"=>null);
                return $response;
            }
        }
    }
    
    public function check_in($data,$Tracking){
        $id_admin = 0;
		$date = '';
		$affectedrows = 0;
		if(!isset($data['date'])){
			$data['date'] = date('Y-m-d');
			$date = $data['date'];
		}
		if(!isset($data['staff_id'])){
			$data['staff_id'] = get_staff_user_id();
		}
		if($data['edit_date'] != ''){
			$temp = $this->format_date_time($data['edit_date']);
			$split_date = explode(' ', $temp);
			$date = $split_date[0];
			$data['date'] = $temp;
		}
		else{
			$date = date('Y-m-d');
			$data['date'] = $date.' '.date('H:i:s');
		}
		unset($data['edit_date']);
		
		if(($date != '') && ($data['staff_id'] != '')){
			$check_more = '';
			$count_st = 0;
			
			$point_id = '';
			$workplace_id = '';
			
			$data['route_point_id'] = $point_id;
			$data['workplace_id'] = $workplace_id;
			unset($data['location_user']);  
			unset($data['point_id']); 
			if(isset($data['ip_address'])){
				unset($data['ip_address']);
			}
			$this->db->insert(db_prefix().'check_in_out', $data);
			$insert_id = $this->db->insert_id();
			if ($insert_id) {
			    $this->db->insert(db_prefix().'LocationTracking', $Tracking);
				$affectedrows++;
			}  

			$this->add_check_in_out_value_to_timesheet($data['staff_id'], $date);
			if ($affectedrows > 0) {
				return true;
			}
		}
		return false;
    }
    
    public function add_check_in_out_value_to_timesheet($staff_id, $date){
		$data_check_in_out = $this->get_list_check_in_out($date, $staff_id);
		$check_in_date = '';
		$check_out_date = '';
		$total_work_hours = 0;
		$next_key = '';
		foreach ($data_check_in_out as $key => $value) 
		{
			if($value['type_check'] == 2){  
				$check_out_date = $value['date'];
				if($next_key == $key){
					if($check_out_date != '' && $check_in_date != ''){
						$data_hour = $this->calculate_attendance_timesheets($staff_id, $check_in_date, $check_out_date);
						$total_work_hours += $data_hour->working_hour; 
					}
				}
			}
			if($value['type_check'] == 1){  
				$check_in_date = $value['date'];
				$next_key = $key+1;
			}
		}
		$data_ts = $this->get_ts_staff($staff_id, $date, 'W');
		if($total_work_hours > 0){
			if($data_ts){
				$this->db->where('id', $data_ts->id);
				$this->db->update(db_prefix() . 'timesheets_timesheet', [
					'value' => $total_work_hours,
					'type' => 'W'
				]);
				if ($this->db->affected_rows() > 0) {
					return true;
				}
			}
			else{
				$data_insert['staff_id'] = $staff_id;
				$data_insert['date_work'] = $date;
				$data_insert['type'] = 'W';
				$data_insert['add_from'] = ((get_staff_user_id() && get_staff_user_id() != 0 && get_staff_user_id() != '') ? get_staff_user_id() : $staff_id);
				$data_insert['value'] = $total_work_hours;
				$this->db->insert(db_prefix() . 'timesheets_timesheet', $data_insert);   
				$insert_id = $this->db->insert_id();
				if($insert_id){
					return true;
				}
			}
		}
		else{
			if($data_ts){
				$this->db->where('id', $data_ts->id);
				$this->db->delete(db_prefix() . 'timesheets_timesheet');
				if ($this->db->affected_rows() > 0) {
					return true;
				}
			}
		}
		return false;
	}
	
	public function get_ts_staff($staff, $date, $type = ''){
		if($type != ''){
			$this->db->where('type', $type);
		}
		$this->db->where('date_work', $date);
		$this->db->where('staff_id', $staff);
		return $this->db->get(db_prefix().'timesheets_timesheet')->row();
	}
	
	public function get_list_check_in_out($date, $staffid = '', $route_point_id = ''){
		if ($staffid !='') {
			$this->db->where('staff_id', $staffid);
		}
		if ($route_point_id !='') {
			$this->db->where('route_point_id', $route_point_id);
		}
		$this->db->where('date(date) = "'.$date.'"');
		$this->db->order_by('id');
		return $this->db->get(db_prefix() . 'check_in_out')->result_array();
	}
	
	public function calculate_attendance_timesheets($staffid, $time_in, $time_out){ 
    	$obj = new stdclass();
    	$obj->working_hour = 0;
    	$obj->late_hour = 0;
    	$obj->early_hour = 0;
    	$date_work = date('Y-m-d', strtotime($time_in));
    	$work_time = $this->get_hour_shift_staff($staffid , $date_work);  
    	$affectedrows = 0;
    	if($work_time > 0 && $work_time != ''){
    		$list_shift = $this->get_shift_work_staff_by_date($staffid, $date_work);
    
    		$d1 = strtotime($this->format_date_time($time_in));
    		$d2 = strtotime($this->format_date_time($time_out)); 
    		if($d1 > $d2){
    			$temp = $time_in;
    			$time_in = $time_out;
    			$time_out = $temp;
    		}
    		$hour1 = explode(' ', $time_in);
    		$hour2 = explode(' ', $time_out);
    		$time_in = strtotime($hour1[1]);
    		$time_out = strtotime($hour2[1]);
    
    		$hour = 0;
    		$late = 0;
    		$early = 0;
    		$lunch_time = 0;
    		foreach ($list_shift as $shift) {
    			$data_shift_type = $this->get_shift_type($shift);
    
    			$time_in_ = $time_in;
    			$time_out_ = $time_out;
    
    			if($data_shift_type){
    				$start_work = strtotime($data_shift_type->time_start_work);
    				$end_work = strtotime($data_shift_type->time_end_work);
    				$start_lunch_break = strtotime($data_shift_type->start_lunch_break_time);
    				$end_lunch_break = strtotime($data_shift_type->end_lunch_break_time); 
    				if($time_out < $start_work){
    					continue;
    				}
    				if($time_out > $start_lunch_break && $time_out < $end_lunch_break){
    					$time_out_ = $start_lunch_break;
    				}
    				if($time_in > $start_lunch_break && $time_in < $end_lunch_break){
    					$time_in_ = $end_lunch_break;
    				}
    				if($time_in_ < $start_lunch_break && $time_out_ > $end_lunch_break){
    					$lunch_time += $this->get_hour($data_shift_type->start_lunch_break_time, $data_shift_type->end_lunch_break_time);
    				}
    				if($time_in_ == $start_lunch_break && $time_out_ == $end_lunch_break){
    					continue;
    				}
    				if($time_in_ == $start_lunch_break && $time_out_ > $end_lunch_break){
    					$lunch_time += $this->get_hour($data_shift_type->start_lunch_break_time, $data_shift_type->end_lunch_break_time);
    				}
    				if($time_in_ < $start_lunch_break && $time_out_ == $end_lunch_break){
    					$lunch_time += $this->get_hour($data_shift_type->start_lunch_break_time, $data_shift_type->end_lunch_break_time);
    				}
    
    				if($time_in < $start_work && $time_out > $start_work){
    					$time_in_ = $start_work;
    				}elseif($time_in > $start_work && $time_out > $start_work){
    					if($time_in >= $start_lunch_break && $time_in <= $end_lunch_break){
    						$time_in = $start_lunch_break;
    					}
    					$lunch_time_s = 0;
    					if($time_in > $end_lunch_break){
    						$lunch_time_s = $this->get_hour($data_shift_type->start_lunch_break_time, $data_shift_type->end_lunch_break_time);
    					}
    					$late += round(abs($time_in - $start_work)/(60*60),2) - $lunch_time_s;
    				}
    
    				if($time_out > $end_work && $time_in < $end_work){
    					$time_out_ = $end_work;
    				}elseif($time_out < $end_work && $time_in < $end_work){
    					if($time_out >= $start_lunch_break && $time_out <= $end_lunch_break){
    						$time_out = $end_lunch_break;
    					}
    					$lunch_time_s = 0;
    					if($time_out < $end_lunch_break){
    						$lunch_time_s = $this->get_hour($data_shift_type->start_lunch_break_time, $data_shift_type->end_lunch_break_time);
    					}
    					$early += round(abs($time_out - $end_work)/(60*60),2) - $lunch_time_s;
    				}
    				$hour += round(abs($time_out_ - $time_in_)/(60*60),2);
    			}  
    		}
    		$value = abs($hour - $lunch_time);
    		$obj->working_hour = $value;
    		$obj->late_hour = $late;
    		$obj->early_hour = $early;
    		return $obj;
    	}
    }
    
    public function get_hour($date1, $date2){
		$result = 0; 
		if($date1 != '' && $date2 != ''){
			$timestamp1 = strtotime($date1);
			$timestamp2 = strtotime($date2);
			$result = number_format(abs($timestamp2 - $timestamp1)/(60*60),2);
		}
		return $result;
	}
	public function format_date_time($date){
    	if(!$this->check_format_date($date)){
    		$date = to_sql_date($date, true);
    	}
    	return $date;
    }
    
    public function check_format_date($date){
		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\s(0|[0-1][0-9]|2[0-4]):?((0|[0-5][0-9]):?(0|[0-5][0-9])|6000|60:00)$/",$date)) {
			return true;
		} else {
			return false;
		}
	}
    public function get_hour_shift_staff($staff_id, $date){

		$result = 0;
		$data_shift_list = $this->get_shift_work_staff_by_date($staff_id, $date);        
		foreach ($data_shift_list as $ss) {
			$data_shift_type = $this->get_shift_type($ss);            
			if($data_shift_type){
				$hour = $this->get_hour($data_shift_type->time_start_work, $data_shift_type->time_end_work);
				$lunch_hour = $this->get_hour($data_shift_type->start_lunch_break_time, $data_shift_type->end_lunch_break_time);
				$result += abs($hour - $lunch_hour);
			}  
		}   

		return $result;        
	}
	
	public function get_shift_work_staff_by_date($staff, $date = ''){
		$nv = $this->staff_model->get($staff);
		$dpm = $this->departments_model->get_staff_departments($staff,true);
		$sql_dpm = '';
		if($dpm){
			foreach ($dpm as $key => $value) {
				if($sql_dpm == ''){
					$sql_dpm .= 'find_in_set('.$value.', department)';
				}else{
					$sql_dpm .= ' or find_in_set('.$value.', department)';
				}
			}
		}
		
		if($date == ''){
			$date = date('Y-m-d');
		} 
		$sql_where = 'find_in_set('.$staff.', staff)';
		$this->db->where($sql_where);
		$this->db->where('("'.$date.'" >=  from_date and "'.$date.'" <= to_date)');
		$shift = $this->db->get(db_prefix().'work_shift')->result_array();


		if(!$shift){
			if($sql_dpm != '' && ($nv) && ($nv->role != 0 && $nv->role != '')){
				$this->db->where('find_in_set('.$nv->role.', position)');
				$this->db->where('('.$sql_dpm.')');
				$this->db->where('(staff = "" or staff is null)');
				$this->db->where('("'.$date.'" >=  from_date and "'.$date.'" <= to_date)');
				$shift = $this->db->get(db_prefix().'work_shift')->result_array();
				if(!$shift){
					$this->db->where('(position = "" or position is null)');
					$this->db->where('('.$sql_dpm.')');
					$this->db->where('(staff = "" or staff is null)');
					$this->db->where('("'.$date.'" >=  from_date and "'.$date.'" <= to_date)');
					$shift = $this->db->get(db_prefix().'work_shift')->result_array();                  


					if(!$shift){
						$this->db->where('find_in_set('.$nv->role.', position)');
						$this->db->where('(department = "" or department is null)');
						$this->db->where('(staff = "" or staff is null)');
						$this->db->where('("'.$date.'" >=  from_date and "'.$date.'" <= to_date)');
						$shift = $this->db->get(db_prefix().'work_shift')->result_array();
						


						if(!$shift){
							$this->db->where('(position = "" or position is null)');
							$this->db->where('(department = "" or department is null)');
							$this->db->where('(staff = "" or staff is null)');
							$this->db->where('("'.$date.'" >=  from_date and "'.$date.'" <= to_date)');
							$shift = $this->db->get(db_prefix().'work_shift')->result_array();

						}
					}
				}
			}elseif($sql_dpm == '' && ($nv) && ($nv->role != 0 && $nv->role != '')){

				$this->db->where('find_in_set('.$nv->role.', position)');
				$this->db->where('(department = "" or department is null)');
				$this->db->where('(staff = "" or staff is null)');
				$this->db->where('("'.$date.'" >=  from_date and "'.$date.'" <= to_date)');
				$shift = $this->db->get(db_prefix().'work_shift')->result_array();

				if(!$shift){
					$this->db->where('(position = "" or position is null)');
					$this->db->where('(department = "" or department is null)');
					$this->db->where('(staff = "" or staff is null)');
					$this->db->where('("'.$date.'" >=  from_date and "'.$date.'" <= to_date)');
					$shift = $this->db->get(db_prefix().'work_shift')->result_array();
				}
			}elseif($sql_dpm != '' && ($nv) && ($nv->role == 0 || $nv->role == '')){

				$this->db->where('(position = "" or position is null)');
				$this->db->where('('.$sql_dpm.')');
				$this->db->where('(staff = "" or staff is null)');
				$this->db->where('("'.$date.'" >=  from_date and "'.$date.'" <= to_date)');
				$shift = $this->db->get(db_prefix().'work_shift')->result_array();
				if(!$shift){
					$this->db->where('(position = "" or position is null)');
					$this->db->where('(department = "" or department is null)');
					$this->db->where('(staff = "" or staff is null)');
					$this->db->where('("'.$date.'" >=  from_date and "'.$date.'" <= to_date)');
					$shift = $this->db->get(db_prefix().'work_shift')->result_array();
				}
			}elseif($sql_dpm == '' && ($nv) && ($nv->role == 0 || $nv->role == '')){
				$this->db->where('(position = "" or position is null)');
				$this->db->where('(department = "" or department is null)');
				$this->db->where('(staff = "" or staff is null)');
				$this->db->where('("'.$date.'" >=  from_date and "'.$date.'" <= to_date)');
				$shift = $this->db->get(db_prefix().'work_shift')->result_array();
			}
		}
		$list_shift_id = [];
		$day_number = (int)date('d', strtotime($date));
		$Day = (int)date('N', strtotime($date));
		if($shift){
			foreach ($shift as $key => $value) {
				if($value['type_shiftwork'] == 'by_absolute_time'){
					$this->db->where('(staff_id = '.$staff.' or staff_id = 0)');
					$this->db->where('date',$date);
					$this->db->where('work_shift_id',$value['id']);
					$shift_detail = $this->db->get(db_prefix().'work_shift_detail')->row();
					if($shift_detail){
						if(!in_array($shift_detail->shift_id, $list_shift_id)){
							$list_shift_id[] = $shift_detail->shift_id;   
						}
					}
				}
				else{
					$this->db->where('(staff_id = '.$staff.' or staff_id = 0)');
					$this->db->where('number',$Day);
					$this->db->where('work_shift_id',$value['id']);
					$shift_detail = $this->db->get(db_prefix().'work_shift_detail_number_day')->row();
					if($shift_detail){
						if(!in_array($shift_detail->shift_id, $list_shift_id)){
							$list_shift_id[] = $shift_detail->shift_id;   
						}
					}                    
				}
			}
		}   
		return $list_shift_id;
	}
	
	public function get_shift_type($id=''){
		if($id != ''){
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'shift_type')->row();
		}
		else{
			return $this->db->get(db_prefix() . 'shift_type')->result_array();
		}
	}
	
    
    

    
    public function Get_accountDetails($AccountID,$PlantID)
    {
       
        $ss = 'SELECT * FROM tblclients WHERE AccountID ="'.$AccountID.'" AND PlantID='.$PlantID ;

                $AccountDetails = $this->db->query($ss)->row();
        
                return $AccountDetails;
    }
    
    public function GetItems($ItemIDs,$PlantID)
    {
       if(!empty($ItemIDs)){
           $Sql = "SELECT * FROM tblitems WHERE item_code IN('".implode("','",$ItemIDs)."') AND PlantID=".$PlantID ;

        $ItemDetails = $this->db->query($Sql)->result_array();
        return $ItemDetails;
       }
        return null;
    }
    
    
  
  public function Update_enquiryDetails($data){
            
    $EnqID = $data['enqID'];
    unset($data['enqID']);
            
    $table = db_prefix() . 'so_enquiry';
        $this->db->set($data);
        $this->db->where("id", $EnqID);
        $this->db->update($table);
        if ($this->db->affected_rows() > 0) {
            $response=array("status"=>true,"message"=>"You have updated Successfully");
            return $response;
        }else {
            $response=array("status"=>false,"message"=>"Something went wroung..");
            return $response;
        }
  }
 
  
    
    public function Get_dashboard_status($staff_id)
    {
        
        
        $ss = 'SELECT * FROM tblstaff WHERE staffid ='.$staff_id;

                $staff_data = $this->db->query($ss)->row_array();
        
                $response=array("status"=>true,"message"=>"Staff Detail","staff_data"=>$staff_data);
                
                return $response;
           
    }
    
    public function Get_assigned_company($staff_id)
    {
        
        
        $ss = 'SELECT * FROM tblstaff WHERE staffid ='.$staff_id .'';

                $staff_data = $this->db->query($ss)->row_array();
                $asigned_comp = unserialize($staff_data["staff_comp"]);
                $company = array();
                foreach ($asigned_comp as $value) {
                    array_push($company, $value);
                }
            $this->db->or_where_in('id', $company);    
            $company_data = $this->db->get(db_prefix().'rootcompany')->result_array();
        
       
                $response=array("status"=>true,"message"=>"Staff Detail","staff_data"=>$company_data);
                
                return $response;
           
    }
    
     public function Get_target($staff_id)
    {
        $curr_month = date('m'); 
      $year = date('Y');
      $curr_year = substr( $year, -2);
    
        $SQL = 'SELECT SUM(total) as total FROM `tbltarget_vs_achievement` WHERE `staff_id` ="'.$staff_id.'" AND `month` ="'.$curr_month.'" AND `year` ="'.$curr_year.'" ';
        $target_data = $this->db->query($SQL)->result_array();
        $response=array("status"=>true,"message"=>"Target","target"=>$target_data);
             return $response;
    }
    
    public function Get_achievement($staff_id,$PlantID)
    {
         $from_date = date('Y')."-".date('m')."-01";
         $to_date = date('Y-m-d');
  
            $SQL = 'SELECT customer_id FROM tblcustomer_admins WHERE `staff_id` ="'.$staff_id.'"';
            $cust_data = $this->db->query($SQL)->result_array();
            $cust_id = array();
                
            foreach ($cust_data as $key => $value) {
             array_push($cust_id, $value["customer_id"]);
            }
            
            $customer_id = implode("','", $cust_id);
           
           // $SQL2 = "SELECT SUM(BillAmt) as total FROM `tblsalesmaster` WHERE `PlantID` ='.$PlantID.' AND AccountID IN ( '.$customer_id.') AND Transdate >= '.$from_date.' and Transdate <= '.$to_date.'";
            //$SQL2 = 'SELECT SUM(BillAmt) as total FROM `tblsalesmaster` WHERE `PlantID` ="'.$PlantID.'" AND AccountID IN ( '.$customer_id.') AND Transdate >= "'.$from_date.'" and Transdate <= "'.$to_date.'"';
            //$SQL2  = 'SELECT SUM(BillAmt) as total FROM `tblsalesmaster` WHERE `PlantID` ="'.$PlantID.'" AND Transdate >= "'.$from_date.'" and Transdate <= "'.$to_date.'" AND AccountID IN ("'.$customer_id.'")';
            $SQL2 = "SELECT SUM(BillAmt) as total FROM `tblsalesmaster` WHERE `AccountID` IN('".implode("','",$cust_id)."') AND `PlantID` ='.$PlantID.' AND Transdate >= '$from_date' and Transdate <= '$to_date'";
            $total_data = $this->db->query($SQL2)->result_array();
                
            $response=array("status"=>true,"message"=>"Achievement","achievement"=>$total_data);
            return $response;
    }
    
    public function Get_Citylist($state_id)
    {
        
        $ss = 'SELECT * FROM tblxx_citylist where state_id= "'.$state_id.'"';
        $city_data = $this->db->query($ss)->result_array();
        $response=array("status"=>true,"message"=>"City List","city_data"=>$city_data);
        return $response;
           
    }
    
    public function Get_order_list_detail($dist_id = '',$PlantID = '',$start_date = '',$end_date = '',$order_status = '')
    {
        
        if ( date('m') <= 3 ) {
            $FY = date('y') - 1;
        }
        else {
            $FY = date('y');
        }
        $this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'ordermaster.OrderID as id, ' . db_prefix() . 'currencies.name as currency_name');
        $this->db->from(db_prefix() . 'ordermaster');
        $this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'ordermaster.currency', 'left');
        //$this->db->where($where);
        $this->db->where(db_prefix() . 'ordermaster.AccountID', $dist_id);
        $this->db->where('Transdate BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"');
        
        if($order_status == "P"){
            $this->db->where(db_prefix() . 'ordermaster.OrderStatus', "O");
            $this->db->where(db_prefix() . 'ordermaster.ChallanID', null);
        }
        if($order_status == "O"){
            $this->db->where(db_prefix() . 'ordermaster.OrderStatus', "O");
            //$this->db->where(db_prefix() . 'ordermaster.ChallanID', 'IS NOT NULL');
            $this->db->where(db_prefix() . 'ordermaster.ChallanID !=', null);
        }
        if($order_status == "C"){
            $this->db->where(db_prefix() . 'ordermaster.OrderStatus', "C");
            
        }
        
        $this->db->where(db_prefix() . 'ordermaster.PlantID', $PlantID);
        $this->db->where(db_prefix() . 'ordermaster.FY', $FY);

        //$this->db->order_by('number,YEAR(date)', 'desc');

        $order_data =  $this->db->get()->result_array();
        $response=array("status"=>true,"message"=>"Order List","order_list"=>$order_data);
                
                return $response;
    }
    
    
    
    public function Get_pending_order_list_detail_new($dist_id = '',$PlantID = '')
    {
       
        if ( date('m') <= 3 ) {
            $FY = date('y') - 1;
        }
        else {
            $FY = date('y');
        }
        $this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'ordermaster.OrderID as id, ' . db_prefix() . 'currencies.name as currency_name');
        $this->db->from(db_prefix() . 'ordermaster');
        $this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'ordermaster.currency', 'left');
        //$this->db->where($where);
        $this->db->where(db_prefix() . 'ordermaster.AccountID', $dist_id);
        //$this->db->where('Transdate BETWEEN "'. date('Y-m-d', strtotime($start_date)). '" and "'. date('Y-m-d', strtotime($end_date)).'"');
        $this->db->where(db_prefix() . 'ordermaster.OrderStatus', "O");
        $this->db->where(db_prefix() . 'ordermaster.PlantID', $PlantID);
        $this->db->where(db_prefix() . 'ordermaster.ChallanID', null);
        $this->db->where(db_prefix() . 'ordermaster.FY', $FY);

        //$this->db->order_by('number,YEAR(date)', 'desc');

        $order_data =  $this->db->get()->result_array();
        $response=array("status"=>true,"message"=>"Order List","pending_order_list"=>$order_data);
                
                return $response;
    }
    
    public function Get_pending_order_list_detail_new2($dist_id = '',$PlantID = '',$staff_id = '')
    {
        if ( date('m') <= 3 ) {
            $FY = date('y') - 1;
        }
        else {
            $FY = date('y');
        }
        //$PlantID = 3;
        $sql = 'SELECT '.db_prefix().'staff.*
        FROM tblstaff WHERE  staffid = '.$staff_id;
        $staf_data = $this->db->query($sql)->row();
       if($staf_data->admin == "1"){
           $sql ='SELECT '.db_prefix().'ordermaster.*,' . db_prefix() . 'ordermaster.OrderID as id
        FROM '.db_prefix().'ordermaster WHERE '.db_prefix().'ordermaster.OrderStatus="O" AND '.db_prefix().'ordermaster.PlantID='.$PlantID.' AND '.db_prefix().'ordermaster.ChallanID IS NULL AND '.db_prefix().'ordermaster.FY="'.$FY.'"';
        
        $result = $this->db->query($sql)->result_array();
       }else{
           if($dist_id !==""){
               $sql ='SELECT '.db_prefix().'ordermaster.*,' . db_prefix() . 'ordermaster.OrderID as id
                FROM '.db_prefix().'ordermaster WHERE '.db_prefix().'ordermaster.AccountID IN("'.$dist_id.'") AND '.db_prefix().'ordermaster.OrderStatus="O" AND '.db_prefix().'ordermaster.PlantID='.$PlantID.' AND '.db_prefix().'ordermaster.ChallanID IS NULL AND '.db_prefix().'ordermaster.FY="'.$FY.'"';
                $result = $this->db->query($sql)->result_array();
           }
       }
        
        $response=array("status"=>true,"message"=>"Order List","pending_order_list"=>$result);
        return $response;
    }
    
    public function GetPendingOrder($PlantID = '',$staff_id = '')
    {
        if ( date('m') <= 3 ) {
            $FY = date('y') - 1;
        }
        else {
            $FY = date('y');
        }
        //$PlantID = 3;
            $sql = 'SELECT '.db_prefix().'staff.* FROM tblstaff WHERE  staffid = '.$staff_id;
            $staf_data = $this->db->query($sql)->row();
       if($staf_data->admin == "1"){
           $sql ='SELECT '.db_prefix().'ordermaster.*,' . db_prefix() . 'ordermaster.OrderID as id
        FROM '.db_prefix().'ordermaster WHERE '.db_prefix().'ordermaster.OrderStatus="O" AND '.db_prefix().'ordermaster.PlantID='.$PlantID.' AND '.db_prefix().'ordermaster.ChallanID IS NULL AND '.db_prefix().'ordermaster.FY="'.$FY.'"';
        
        $PendingOrder = $this->db->query($sql)->result_array();
       }else{
        $staff_ids = array();
        array_push($staff_ids, $staff_id);
           $get_sql1 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$staff_id.'"';
            $get_result1 = $this->db->query($get_sql1)->result_array();
            foreach ($get_result1 as $key1 => $value1) {
                array_push($staff_ids, $value1["staffid"]);
                $get_sql2 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value1["staffid"].'"';
                $get_result2 = $this->db->query($get_sql2)->result_array();
                foreach ($get_result2 as $key2 => $value2) {
                    array_push($staff_ids, $value2["staffid"]);
                    $get_sql3 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value2["staffid"].'"';
                    $get_result3 = $this->db->query($get_sql3)->result_array();
                    foreach ($get_result3 as $key3 => $value3) {
                        array_push($staff_ids, $value3["staffid"]);
                        $get_sql4 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value3["staffid"].'"';
                        $get_result4 = $this->db->query($get_sql4)->result_array();
                        foreach ($get_result4 as $key4 => $value4) {
                            array_push($staff_ids, $value4["staffid"]);
                            $get_sql5 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value4["staffid"].'"';
                            $get_result5 = $this->db->query($get_sql5)->result_array();
                            foreach ($get_result5 as $key5 => $value5) {
                                array_push($staff_ids, $value5["staffid"]);
                            }
                        }
                    }
                }
            }
            
            $staff_ids_uniqu = array_unique($staff_ids);  
            $staff_ids_uniqu_s = implode(", ", $staff_ids_uniqu);
    
            $account_id = array();
            $this->db->select(db_prefix() . 'clients.AccountID');
            $this->db->from(db_prefix() . 'customer_admins');
            $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'customer_admins.customer_id', 'left');
       
            $this->db->where(db_prefix() . 'clients.PlantID', $PlantID);
            $this->db->where_in(db_prefix() . 'customer_admins.staff_id', $staff_ids_uniqu);
            $this->db->order_by(db_prefix() . 'clients.AccountID','ASC');
            $data_array =  $this->db->get()->result_array();
            
            foreach($data_array as $value){
                array_push($account_id, $value["AccountID"]);
            }
            $AccountId = implode(", ", $account_id);
            $account_id_array = $AccountId;
            
            $this->db->select(db_prefix() . 'ordermaster.*');
            $this->db->from(db_prefix() . 'ordermaster');
            $this->db->where(db_prefix() . 'ordermaster.PlantID', $PlantID);
            $this->db->LIKE(db_prefix() . 'ordermaster.FY', $FY);
            $this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');
            $this->db->where(db_prefix() . 'ordermaster.ChallanID =', NULL);
            $this->db->where_in(db_prefix() . 'ordermaster.AccountID', $account_id);
            $PendingOrder =  $this->db->get()->result_array();
       }
        $cc = count($PendingOrder);
        $response=array("status"=>true,"message"=>"Order List","pending_order_list"=>$PendingOrder);
        return $response;
    }
    
    public function CheckPendingOrder($distId = '',$FY = '',$PlantID = '',$OrdType = '')
    {
        if($OrdType =="Taxable"){
            $OrderType = "TaxItems";
        }else{
            $OrderType = "NonTaxItems";
        }   
        $sql ='SELECT '.db_prefix().'ordermaster.*,' . db_prefix() . 'ordermaster.OrderID as id
        FROM '.db_prefix().'ordermaster WHERE '.db_prefix().'ordermaster.AccountID ="'.$distId.'" AND 
        '.db_prefix().'ordermaster.OrderStatus="O" AND 
        '.db_prefix().'ordermaster.PlantID='.$PlantID.' AND 
        '.db_prefix().'ordermaster.OrderType = "'.$OrderType.'" AND
        '.db_prefix().'ordermaster.ChallanID IS NULL AND
        '.db_prefix().'ordermaster.FY="'.$FY.'"';
        
        $result = $this->db->query($sql)->result_array();
        if(empty($result)){
            return true;
        }else{
            return false;
        }
    }
    
    public function Get_my_team_list_detail($staff_id = '',$PlantID = '')
    {
        
        $sql ='SELECT '.db_prefix().'staff.*,
        (SELECT GROUP_CONCAT(position_name SEPARATOR ",") FROM '.db_prefix().'hr_job_position WHERE '.db_prefix().'hr_job_position.position_id = '.db_prefix().'staff.job_position) as Position
        FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.team_manage='.$staff_id. ' AND '.db_prefix().'staff.staffid IN (SELECT '.db_prefix().'staff_departments.staffid FROM '.db_prefix().'staff_departments WHERE departmentid = 2) AND '.db_prefix().'staff.active = 1';
        
        $result = $this->db->query($sql)->result_array();
        if($PlantID == ""){
            $response =array("status"=>true,"message"=>"My team List","team_list"=>$result);
        }else{
            $my_team = array();
            foreach ($result as $key => $value) {
                # code...
                $staff_company = unserialize($value["staff_comp"]);
                if(in_array($PlantID, $staff_company)){
                    
                    $new_array = array('staffid'=>$value["staffid"], 'email'=>$value["email"], 'username'=>$value["username"], 'firstname'=>$value["firstname"], 'lastname'=>$value["lastname"], 'Position'=>$value["Position"], 'active'=>$value["active"]);
                    array_push($my_team, $new_array);
                }
            }
            $response=array("status"=>true,"message"=>"My team List","team_list"=>$my_team);
        }
        return $response;
    }
    
    
    
    public function Get_staff_detail($staff_id = '',$PlantID = '')
    {
        
        $sql ='SELECT '.db_prefix().'staff.*,
        (SELECT GROUP_CONCAT(position_name SEPARATOR ",") FROM '.db_prefix().'hr_job_position WHERE '.db_prefix().'hr_job_position.position_id = '.db_prefix().'staff.job_position) as Position
        FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.staffid='.$staff_id;
        
        $result = $this->db->query($sql)->row();
        $response=array("status"=>true,"message"=>"Staff Detail","staff_detail"=>$result);
                
                return $response;
    }
    
    public function Get_sale_reports($UserID = '',$PlantID = '',$AccountID = '',$from_date = '',$to_date = '')
    {
        
        if ( date('m') <= 3 ) {
            $fy = date('y') - 1;
        }
        else {
            $fy = date('y');
        }
        
        
        $sql1 = '(Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:23:59") AND PlantID = '.$PlantID.' AND FY = "'.$fy.'" AND AccountID = "'.$AccountID.'"';
        
        $sql ='SELECT '.db_prefix().'salesmaster.*,  
        (SELECT GROUP_CONCAT(company SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'salesmaster.AccountID AND '.db_prefix().'clients.PlantID = '.$PlantID.') as AccountName, 
        (SELECT GROUP_CONCAT(StationName SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'salesmaster.AccountID AND '.db_prefix().'clients.PlantID = '.$PlantID.') as StationName, 
        (SELECT COUNT(OrderID) FROM '.db_prefix().'ordermaster WHERE '.db_prefix().'ordermaster.ChallanID = '.db_prefix().'salesmaster.ChallanID AND '.db_prefix().'ordermaster.PlantID = '.$PlantID.') as Count_number, 
        (SELECT SUM(OrderAmt) FROM '.db_prefix().'ordermaster WHERE '.db_prefix().'ordermaster.ChallanID = '.db_prefix().'salesmaster.ChallanID AND '.db_prefix().'ordermaster.PlantID = '.$PlantID.') as Total_number
        FROM '.db_prefix().'salesmaster WHERE '.$sql1;
        $result = $this->db->query($sql)->result_array();
        
        $response=array("status"=>true,"message"=>"Sale Reports","sale_reports"=>$result);
                
                return $response;
    }
    
    public function Get_parties_not_billed($UserID = '',$PlantID = '',$from_date = '',$to_date = '')
    {
       
        if ( date('m') <= 3 ) {
            $fy = date('y') - 1;
        }
        else {
            $fy = date('y');
        }
        
        $sql = ' SELECT * FROM '.db_prefix() . 'customer_admins WHERE staff_id = '.$UserID.' AND company_id = '.$PlantID;
        $result = $this->db->query($sql)->result_array();
        
        $this->db->select(db_prefix() . 'customer_admins.*, ' . db_prefix() . 'clients.company, ' . db_prefix() . 'clients.city, ' . db_prefix() . 'clients.StationName,' . db_prefix() . 'salesmaster.ChallanID');
        $this->db->from(db_prefix() . 'customer_admins');
        $this->db->join(db_prefix() . 'salesmaster', '' . db_prefix() . 'salesmaster.AccountID = ' . db_prefix() . 'customer_admins.customer_id AND ' . db_prefix() . 'salesmaster.PlantID = ' . db_prefix() . 'customer_admins.company_id');
        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'customer_admins.customer_id AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'customer_admins.company_id');
        
        $this->db->where(db_prefix() . 'customer_admins.staff_id', $UserID);
        $this->db->where(db_prefix() . 'customer_admins.company_id', $PlantID);
        $this->db->where(db_prefix() . 'salesmaster.Transdate BETWEEN "'. date('Y-m-d', strtotime($from_date)). '" AND "'. date('Y-m-d', strtotime($to_date)).'"');
        $_data =  $this->db->get()->result_array();
        
        
        $not_billed = array();
        
        foreach ($result as $key => $value) {
            # code...
            
            array_push($not_billed, $value["customer_id"]);
            
        }
        
        $res = array(
            "all" => $not_billed,
            "billed" => $_data
            );
        $this->db->select(db_prefix() . 'clients.*');
        $this->db->from(db_prefix() . 'clients');
        $this->db->where(db_prefix() . 'clients.PlantID', $PlantID);
        $this->db->where_in(db_prefix() . 'clients.AccountID', $not_billed);
        $_data2 =  $this->db->get()->result_array();
        /*$this->db->select(db_prefix() . 'customer_admins.*, ' . db_prefix() . 'clients.company, ' . db_prefix() . 'clients.city, ' . db_prefix() . 'clients.StationName');
        $this->db->from(db_prefix() . 'customer_admins');
        $this->db->join(db_prefix() . 'ordermaster', '' . db_prefix() . 'ordermaster.AccountID != ' . db_prefix() . 'customer_admins.customer_id AND ' . db_prefix() . 'ordermaster.PlantID = ' . db_prefix() . 'customer_admins.company_id');
        $this->db->join(db_prefix() . 'salesmaster', '' . db_prefix() . 'salesmaster.AccountID != ' . db_prefix() . 'customer_admins.customer_id AND ' . db_prefix() . 'ordermaster.PlantID = ' . db_prefix() . 'customer_admins.company_id');
        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID != ' . db_prefix() . 'customer_admins.customer_id AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'customer_admins.company_id');
        $this->db->where(db_prefix() . 'salesmaster.FY', $fy);
        $this->db->where(db_prefix() . 'salesmaster.Transdate BETWEEN "'. date('Y-m-d', strtotime($from_date)). '" and "'. date('Y-m-d', strtotime($to_date)).'"');
        $this->db->where(db_prefix() . 'ordermaster.OrderStatus', "O");
        $this->db->where(db_prefix() . 'ordermaster.UserID', $UserID);
        //$this->db->where(db_prefix() . 'ordermaster.ChallanID', null);
        $this->db->where(db_prefix() . 'ordermaster.FY', $fy);
        $_data =  $this->db->get()->result_array();*/
        $response=array("status"=>true,"message"=>"Order List","bill_Act"=>$_data,"All_Act"=>$_data2);
                
                return $response;
    }
    
    public function Get_item_not_billed($AccountID = '',$PlantID = '',$from_date = '',$to_date = '')
    {
        
        if ( date('m') <= 3 ) {
            $fy = date('y') - 1;
        }
        else {
            $fy = date('y');
        }
        $grpip_id = array(16,17,22,23,24,27,29,30,32,33,35,39,40,41,43);
        $sql = ' SELECT * FROM '.db_prefix() . 'history WHERE AccountID = "'.$AccountID.'" AND PlantID = '.$PlantID. ' AND FY = '.$fy.' AND TransDate2 BETWEEN "'. date('Y-m-d', strtotime($from_date)). '" and "'. date('Y-m-d', strtotime($to_date)).'"';
        $result = $this->db->query($sql)->result_array();
        
        $_billed = array();
        if(empty($result)){
            $response=array("status"=>true,"message"=>"Not Bill Item List","cc"=>0,"not_bill_item"=>null);
                return $response;
        }else{
            foreach ($result as $key => $value) {
            # code...
            
            array_push($_billed, $value["ItemID"]);
            
        }
        $this->db->select(db_prefix() . 'items.*');
        $this->db->from(db_prefix() . 'items');
        $this->db->join(db_prefix() . 'accountitemdiv', '' . db_prefix() . 'accountitemdiv.ItemDivID = ' . db_prefix() . 'items.group_id AND ' . db_prefix() . 'accountitemdiv.plant_assign = ' . db_prefix() . 'items.PlantID AND ' . db_prefix() . 'accountitemdiv.AccountID = "'.$AccountID.'"');
        
        $this->db->where_not_in(db_prefix() . 'items.item_code', $_billed);
        $this->db->where_in(db_prefix() . 'items.subgroup_id', $grpip_id);
        $this->db->where(db_prefix() . 'items.PlantID', $PlantID);
        $this->db->where(db_prefix() . 'items.isactive', "Y");
        $_data =  $this->db->get()->result_array();
        $sscount = count($_data);
        if(empty($_data)){
            $response=array("status"=>true,"message"=>"Not Bill Item List","cc"=>0,"not_bill_item"=>null);
                return $response;
        }else{
            $response=array("status"=>true,"message"=>"Not Bill Item List","cc"=>$sscount,"not_bill_item"=>$_data);
                return $response;
        }
        
        }
        
    }
    public function Get_account_ledger($UserID = '',$PlantID = '',$AccountID = '',$from_date = '',$to_date = '')
    {
       
        if ( date('m') <= 3 ) {
            $fy = date('y') - 1;
        }
        else {
            $fy = date('y');
        }
        $sql ='SELECT '.db_prefix().'accountledger.*
        FROM '.db_prefix().'accountledger WHERE PlantID='.$PlantID.' AND AccountID ="'.$AccountID.'" AND FY ="'.$fy.'"  ORDER BY Transdate ASC';
        
        $result = $this->db->query($sql)->result_array();
        
        $act_bal = $this->get_data_for_account_bal($AccountID,$PlantID);
        
        $ledger_array = array();
        
        $new_acc_bal = $act_bal->BAL1;
        $opening_bal = $act_bal->BAL1;
        $i = 1;
        
        $from_date = $from_date . ' 00:00:00';
        $from_date = date('Y-m-d',strtotime($from_date));
        //echo $from_date;
        $to_date = $to_date . ' 23:59:59';
        $to_date = date('Y-m-d',strtotime($to_date));
        $total_debit = 0;
        $total_credit = 0;
        
        foreach ($result as $key => $value) {
            //$led_from_date = strtotime($value["Transdate"]);
            $led_from_date = date('Y-m-d',strtotime($value["Transdate"]));
            $led_to_date = date('Y-m-d',strtotime($value["Transdate"]));
            $Cvalue = 0;
            $Dvalue = 0;
            $new_Dvalue = 0;
            $new_Cvalue = 0;
                
            if($led_from_date >= $from_date && $led_from_date <= $to_date){
                
                if($i == 1){
                    
                    if($opening_bal>=0){
                        $ob_dr_cr = "Cr";
                    }else{
                        $ob_dr_cr = "Dr";
                    }
                       
                    if($opening_bal>0){
                            $debit = abs($opening_bal);
                            $new_Debit = number_format((float)$debit, 2, '.', '');
                            $total_debit = $total_debit + $debit;
                    }    
                    if($opening_bal<=0){
                            $credit = abs($opening_bal);
                            $new_Credit = number_format((float)$credit, 2, '.', '');
                            $total_credit = $total_credit + $credit;
                    }
                    $new_opening_bal = number_format((float)$opening_bal, 2, '.', '');
                    $new_array = array('passedFrom'=>' ', 'VoucherID'=>'', 'Transdate'=>$from_date, 'Narration'=>'Opening Balance', 'debit'=>$new_Debit, 'credit'=>$new_Credit, 'Amount'=>$new_opening_bal);
                    array_push($ledger_array, $new_array);
                    $i++;
                }
                
                if($value["TType"]=="D"){
                    
                    $new_acc_bal = $new_acc_bal + $value["Amount"];
                    $Dvalue = $value["Amount"];
                    $new_Dvalue = number_format((float)$Dvalue, 2, '.', '');
                    $total_debit = $total_debit + $Dvalue;
                    //echo $value; 
                }
                $new_acc_bal2 = abs($new_acc_bal);
                if($value["TType"]=="C"){
                    
                    $new_acc_bal = $new_acc_bal - $value["Amount"];
                    $Cvalue = $value["Amount"];
                    $new_Cvalue = number_format((float)$Cvalue, 2, '.', '');
                    $total_credit = $total_credit + $Cvalue;
                    //echo $value;
                     
                }
                $new_acc_bal_ = number_format((float)$new_acc_bal, 2, '.', '');
                $new_array1 = array('passedFrom'=>$value["PassedFrom"], 'VoucherID'=>$value["VoucherID"], 'Transdate'=>substr($value["Transdate"],0,10), 'Narration'=>$value["Narration"], 'debit'=>$new_Dvalue, 'credit'=>$new_Cvalue, 'Amount'=>$new_acc_bal_);
                array_push($ledger_array, $new_array1);
            
            }else {
                if($value["TType"]=="D"){
                    
                    $new_acc_bal = $new_acc_bal + $value["Amount"];
                    //echo $value["Amount"]; 
                }
            if($value["TType"]=="C"){
                    
                    $new_acc_bal = $new_acc_bal - $value["Amount"];
                    //echo $value["Amount"]; 
                }
                $opening_bal = $new_acc_bal;
                
            }
        }
        if($i > 1){
            
            $total_debit_ = number_format((float)$total_debit, 2, '.', '');
            $total_credit_ = number_format((float)$total_credit, 2, '.', '');
            $new_acc_bal_ = number_format((float)$new_acc_bal, 2, '.', '');
            $new_array1 = array('passedFrom'=>'', 'VoucherID'=>'', 'Transdate'=>'', 'Narration'=>'Closing Balance', 'debit'=>$total_debit_, 'credit'=>$total_credit_, 'Amount'=>$new_acc_bal_);
            array_push($ledger_array, $new_array1);
        }
        
        $response=array("status"=>true,"message"=>"Account Ledger","account_ledger"=>$ledger_array);
                
                return $response;
    }
    
    public function get_data_for_account_bal($AccountID,$PlantID){
        
        if ( date('m') <= 3 ) {
            $fy = date('y') - 1;
        }
        else {
            $fy = date('y');
        }
        
        $this->db->where('PlantID', $PlantID);
        $this->db->where('FY', $fy);    
        
        $this->db->where('AccountID', $AccountID);
        
        $accounts = $this->db->get(db_prefix().'accountbalances')->row();
    return $accounts;   
    }
    
    public function update_tour_plan($staff_id = '',$PlantID = '',$id = '',$status = '',$reason = '')
    {
        
        $this->db->where('PlantID', $PlantID);
        $this->db->where('staff_id', $staff_id);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tour', [
                                        "status" => $status,
                                        "reason" => $reason,
                                    ]);
        if ($this->db->affected_rows() > 0)
            {
              $response=array("status"=>true,"message"=>"Update Tour Plan successfully..","tour_plan_update"=>true);
            }
            else{
              $response=array("status"=>false,"message"=>"somthing went wrong..","tour_plan_update"=>false);
            }
            return $response;
    }
    
    public function Submit_TPlan($data = '')
    {
        $staff_id = $data['staff_id'];
        $PlantID = $data['PlantID'];
        $id = $data['id'];
        unset($data['staff_id']);
        unset($data['PlantID']);
        unset($data['id']);
        
        $this->db->where('PlantID', $PlantID);
        $this->db->where('staff_id', $staff_id);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tour', $data);
        if ($this->db->affected_rows() > 0)
            {
              $response=array("status"=>true,"message"=>"Update Tour Plan successfully..","tour_plan_update"=>true);
            }
            else{
              $response=array("status"=>false,"message"=>"somthing went wrong..","tour_plan_update"=>false);
            }
            return $response;
    }
    
    public function detail_tour_plan($id = '')
    {
        
        /*$this->db->select('*');
        $this->db->where('id', $id);
        $tour_detail = $this->db->get(db_prefix() . 'tour')->result_array();
       
            $response=array("status"=>true,"message"=>"Tour Plan Details..","tour_plan_detail"=>$tour_detail);
            
            return $response;*/
        $ss = 'SELECT '.db_prefix().'tour.*,
        (SELECT GROUP_CONCAT(state_name SEPARATOR ",") FROM '.db_prefix().'xx_statelist WHERE '.db_prefix().'xx_statelist.short_name = '.db_prefix().'tour.state) as state_name,
        (SELECT GROUP_CONCAT(city_name SEPARATOR ",") FROM '.db_prefix().'xx_citylist WHERE '.db_prefix().'xx_citylist.id = '.db_prefix().'tour.city) as city_name,
        (SELECT GROUP_CONCAT(company SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'tour.cust_ID) as cust_name
FROM '.db_prefix().'tour WHERE '.db_prefix().'tour.id = '.$id;

                $tour_detail = $this->db->query($ss)->row_array();
        
                $response=array("status"=>true,"message"=>"Tour Plan Details..","tour_plan_detail"=>$tour_detail);
                
                return $response;
    }
    
    public function Get_order_details($id = '')
    {
        $this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'ordermaster.OrderID as id, ' . db_prefix() . 'currencies.name as currency_name');
        $this->db->from(db_prefix() . 'ordermaster');
        $this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'ordermaster.currency', 'left');
       
            $this->db->where(db_prefix() . 'ordermaster.OrderID', $id);
            $order = $this->db->get()->row();
            if ($order) {
                //$invoice->total_left_to_pay = get_invoice_total_left_to_pay($invoice->id, $invoice->total);

                $order->items       = $this->get_order_item($id);
                //$order->attachments = $this->get_attachments($id);

                /*$this->load->model('clients_model');
                $client          = $this->clients_model->get($order->AccountID);
                $order->client = $client;
                if (!$order->client) {
                    $order->client          = new stdClass();
                    $order->client->company = $order->deleted_customer_name;
                }

                $this->load->model('payments_model');
                $order->payments = $this->payments_model->get_invoice_payments($id);

                $this->load->model('email_schedule_model');
                $order->scheduled_email = $this->email_schedule_model->get($id, 'invoice');*/
            }
            $response=array("status"=>true,"message"=>"Order Details","order_details"=>$order);
                
                return $response;

    }
    
    public function get_order_item($id)
    {
         $this->db->select('*, ' . db_prefix() . 'items.description,IFNULL('.db_prefix().'history.eOrderQty, '.db_prefix().'history.OrderQty) AS OrderQty');
        $this->db->from(db_prefix() . 'history'); 
        $this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
       
        $this->db->where(db_prefix() . 'history.OrderID', $id);
        return $this->db->get()->result_array();
    }
    
    
    public function single_Customer($customer_id,$plant_id)
    {
        
        
        $ss = 'SELECT '.db_prefix().'clients.*,'
        .db_prefix().'contacts.firstname as firstname, '.db_prefix().'contacts.email as email, '.db_prefix().'contacts.phonenumber as mobilno, '.db_prefix().'contacts.title as storetype,
        (SELECT GROUP_CONCAT(name SEPARATOR ",") FROM '.db_prefix().'customers_groups WHERE '.db_prefix().'customers_groups.id = '.db_prefix().'clients.DistributorType) as customerGroups
FROM tblclients
INNER JOIN tblcontacts
ON tblclients.AccountID = tblcontacts.AccountID WHERE tblclients.AccountID = '.$customer_id;

                $singlecustomer_data = $this->db->query($ss)->row_array();
        
                $response=array("status"=>true,"message"=>"Single Customer details","Singlecustomer_data"=>$singlecustomer_data);
                
                return $response;
           
    }
    
    
    public function Get_enquiry($staff_id)
    {
        $ss = 'SELECT * FROM tblso_enquiry WHERE staff_id ='.$staff_id .' ORDER BY id DESC';
        $enquiry_data = $this->db->query($ss)->result_array();
        $response=array("status"=>true,"message"=>"Enquiry List ","Enquiry_data"=>$enquiry_data);
        return $response;
    }
    
    public function Get_enquiryDetails($enqID)
    {
        $sql = 'SELECT tblso_enquiry.*,tblxx_citylist.city_name,tblcustomers_groups.name AS DISTTYPEName,tblxx_statelist.state_name AS stateName FROM tblso_enquiry 
        JOIN tblxx_citylist ON tblxx_citylist.id= tblso_enquiry.district
        LEFT JOIN tblcustomers_groups ON tblcustomers_groups.id= tblso_enquiry.DISTTYPE
        LEFT JOIN  tblxx_statelist ON tblxx_statelist.short_name= tblso_enquiry.state
        WHERE tblso_enquiry.id ='.$enqID ;
        $enquiryDetails = $this->db->query($sql)->row();
        $response = array("status"=>true,"message"=>"Enquiry Details ","EnqDetails"=>$enquiryDetails);
        return $response;
    }
    
    public function Get_tour($staff_id,$from_date,$to_date,$PlantID)
    {
        $ss = 'SELECT tbltour.*,tblstaff.AccountID,tblstaff.firstname,tblstaff.lastname FROM tbltour 
        JOIN tblstaff ON tblstaff.staffid= tbltour.staff_id
        WHERE tbltour.PlantID = "'.$PlantID.'" AND tbltour.start_date BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" AND tbltour.staff_id ='.$staff_id .' ORDER BY tbltour.id DESC';
        $tour_data = $this->db->query($ss)->result_array();
        $response=array("status"=>true,"message"=>"Tour List ","tour_data"=>$tour_data);
        return $response;
    }
    public function GetTeamTour($staff_id,$from_date,$to_date,$PlantID)
    {
        $sql ='SELECT '.db_prefix().'staff.*,
        (SELECT GROUP_CONCAT(position_name SEPARATOR ",") FROM '.db_prefix().'hr_job_position WHERE '.db_prefix().'hr_job_position.position_id = '.db_prefix().'staff.job_position) as Position
        FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.team_manage='.$staff_id. ' AND '.db_prefix().'staff.staffid IN (SELECT '.db_prefix().'staff_departments.staffid FROM '.db_prefix().'staff_departments WHERE departmentid = 2) AND '.db_prefix().'staff.active = 1';
        
        $result = $this->db->query($sql)->result_array();
        if($PlantID == ""){
            $my_team = array();
        }else{
            $my_team = array();
            foreach ($result as $key => $value) {
                # code...
                $staff_company = unserialize($value["staff_comp"]);
                if(in_array($PlantID, $staff_company)){
                    array_push($my_team, $value["staffid"]);
                }
            }
        }
        $string_version = implode(',', $my_team);
        if(empty($my_team)){
            $response=array("status"=>true,"message"=>"Team Tour List ","Team_tour_data"=>$my_team);
        }else{
            $ss = 'SELECT tbltour.*,tblstaff.AccountID,tblstaff.firstname,tblstaff.lastname FROM tbltour 
            JOIN tblstaff ON tblstaff.staffid= tbltour.staff_id
            WHERE tbltour.PlantID = "'.$PlantID.'" AND tbltour.start_date BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" AND tbltour.staff_id IN('.$string_version.') ORDER BY tbltour.id DESC';
            $tour_data = $this->db->query($ss)->result_array();
            $response=array("status"=>true,"message"=>"Team Tour List ","Team_tour_data"=>$tour_data);
        }
        return $response;
    }
    
    public function Get_ItemDivision()
    {
        $ss = 'SELECT * FROM tblitems_groups' ;
        $itmemDivision_data = $this->db->query($ss)->result_array();
        $response=array("status"=>true,"message"=>"Item Division List ","itemDivision_data"=>$itmemDivision_data);
        return $response;
    }
    
    public function Get_ItemDivision_by_dist($dist_id,$plant_id)
    {
        $sql ='SELECT '.db_prefix().'accountitemdiv.ItemDivID AS id,
        (SELECT GROUP_CONCAT(name SEPARATOR ",") FROM '.db_prefix().'items_groups WHERE '.db_prefix().'items_groups.id = '.db_prefix().'accountitemdiv.ItemDivID) as name
        FROM '.db_prefix().'accountitemdiv WHERE '.db_prefix().'accountitemdiv.AccountID LIKE "'.$dist_id.'" AND '.db_prefix().'accountitemdiv.PlantID='.$plant_id.' AND '.db_prefix().'accountitemdiv.plant_assign='.$plant_id;
        
        $result = $this->db->query($sql)->result_array();
        $response=array("status"=>true,"message"=>"Item Division List ","itemDivision_data"=>$result);
        return $response;
    }
    
    public function Get_ItemDivwise_list($group_id)
    {
        $ss = 'SELECT * FROM tblitems WHERE group_id ='.$group_id ;
        $itmem_data = $this->db->query($ss)->result_array();
        $response=array("status"=>true,"message"=>"Item List ","item_data"=>$itmem_data);
        return $response;
    }
    
    public function Get_itemlist($dist_type,$dist_state_id,$item_division,$plant_id)
    {
        
        $grpip_id = array(16,17,22,23,24,27,29,30,32,33,35,39,40,41,43);
        /*$ss = 'SELECT *,
        (SELECT GROUP_CONCAT(assigned_rate SEPARATOR ",") FROM '.db_prefix().'rate_master WHERE distributor_id = '.$dist_type.' AND state_id = "'.$dist_state_id.'" AND item_id = '.db_prefix().'items.item_code AND '.db_prefix().'rate_master.PlantID = '.$plant_id.') as rate,
        (SELECT GROUP_CONCAT(taxrate SEPARATOR ",") FROM '.db_prefix().'taxes WHERE id = '.db_prefix().'items.tax) as taxrate
        FROM tblitems WHERE group_id ='.$item_division.' AND tblitems.PlantID ='.$plant_id.' AND isactive = "Y" AND group_id ='.$item_division.' AND subgroup_id IN (1,3,4,5,6,7,11,12,13,14,15,16,18,19,20,21,41,42,43,48,49,50,51,52,53,56,58,62,63)';
*/      
        /*$SQl = 'SELECT tblitems.*,
        (SELECT GROUP_CONCAT(assigned_rate SEPARATOR ",") FROM '.db_prefix().'rate_master WHERE distributor_id = '.$dist_type.' AND state_id = "'.$dist_state_id.'" AND item_id = '.db_prefix().'items.item_code AND '.db_prefix().'rate_master.PlantID = '.$plant_id.') as rate,
        (SELECT GROUP_CONCAT(taxrate SEPARATOR ",") FROM '.db_prefix().'taxes WHERE id = '.db_prefix().'items.tax) as taxrate
        FROM tblitems  
        INNER JOIN tblitems_sub_groups ON tblitems_sub_groups.id = tblitems.subgroup_id 
        WHERE tblitems.group_id ='.$item_division.' AND tblitems.PlantID ='.$plant_id.' AND tblitems.isactive = "Y" AND tblitems_sub_groups.main_group_id = "1"';*/
        
        $ss = 'SELECT *,
        (SELECT GROUP_CONCAT(assigned_rate SEPARATOR ",") FROM '.db_prefix().'rate_master WHERE distributor_id = '.$dist_type.' AND state_id = "'.$dist_state_id.'" AND item_id = '.db_prefix().'items.item_code AND '.db_prefix().'rate_master.PlantID = '.$plant_id.') as rate,
        (SELECT GROUP_CONCAT(taxrate SEPARATOR ",") FROM '.db_prefix().'taxes WHERE id = '.db_prefix().'items.tax) as taxrate
        FROM tblitems WHERE group_id ='.$item_division.' AND tblitems.PlantID ='.$plant_id.' AND isactive = "Y" AND group_id ='.$item_division.' AND subgroup_id IN (1,3,4,5,6,7,11,12,13,14,15,16,17,19,22,23,24,26,27,30,32,33,35,39,40,41,43,44,45,46,47,48,49,50,66,69,70,74)';

        $itmem_data = $this->db->query($ss)->result_array();
        $response=array("status"=>true,"message"=>"Item List ","item_data"=>$itmem_data);
        return $response;
    }
    
    public function Get_all_dist_type($plant_id)
    {
        
        $ss = 'SELECT * FROM tblcustomers_groups WHERE PlantID ='.$plant_id;
        $dist_type_data = $this->db->query($ss)->result_array();
        
        $response=array("status"=>true,"message"=>"Distributor Type List ","dist_type_data"=>$dist_type_data);
                
                return $response;
           
    }
    
    public function Get_Customer($plant_id,$staff_id)
    {
       
 

$ss = 'SELECT '.db_prefix().'clients.*,
        (SELECT GROUP_CONCAT(name SEPARATOR ",") FROM '.db_prefix().'customers_groups WHERE '.db_prefix().'customers_groups.id = '.db_prefix().'clients.DistributorType) as customerGroups
FROM tblclients WHERE '.db_prefix().'clients.AccountID IN (SELECT customer_id FROM tblcustomer_admins WHERE staff_id = '.$staff_id.' AND company_id = '.$plant_id.') AND PlantID ='.$plant_id;

                $customer_data = $this->db->query($ss)->result_array();
        
                $response=array("status"=>true,"message"=>"Customer details ","customer_data"=>$customer_data);
                
                return $response;
           
    }
    
    public function Get_Customer_new($plant_id,$staff_id)
    {
        
        $sql = 'SELECT '.db_prefix().'staff.*
        FROM tblstaff WHERE  staffid = '.$staff_id;
        $staf_data = $this->db->query($sql)->row();
       if($staf_data->admin == "1"){
           
           $ss = 'SELECT '.db_prefix().'clients.*,
        (SELECT GROUP_CONCAT(name SEPARATOR ",") FROM '.db_prefix().'customers_groups WHERE '.db_prefix().'customers_groups.id = '.db_prefix().'clients.DistributorType) as customerGroups,
        (SELECT GROUP_CONCAT(city_name SEPARATOR ",") FROM '.db_prefix().'xx_citylist WHERE '.db_prefix().'xx_citylist.id = '.db_prefix().'clients.city) as city_new,
        (SELECT GROUP_CONCAT(phonenumber SEPARATOR ",") FROM '.db_prefix().'contacts WHERE '.db_prefix().'contacts.AccountID  = '.db_prefix().'clients.AccountID AND '.db_prefix().'contacts.PlantID  = '.db_prefix().'clients.PlantID) as phonenumber2
FROM tblclients WHERE SubActGroupID = "60001004" AND PlantID ='.$plant_id.' AND Blockyn = "N"';

        $customer_data = $this->db->query($ss)->result_array();
        /*$count = count($customer_data);*/
       }else{
           $sql = 'SELECT '.db_prefix().'staff.*
        FROM tblstaff WHERE  team_manage = '.$staff_id;
        $myteam_data = $this->db->query($sql)->result_array();
        $myteam_id = array();
    
    if(empty($myteam_data)){
        
    }else{
        
        foreach ($myteam_data as $key => $value) {
        
        array_push($myteam_id, $value["staffid"]);
        
        }
    
        $string_version = implode(',', $myteam_id);
        
        $sql2 = 'SELECT '.db_prefix().'staff.*
            FROM tblstaff WHERE  team_manage IN( '.$string_version.')';
            $myteam_data2 = $this->db->query($sql2)->result_array();
            if(empty($myteam_data2)){
                
            }else{
                
                foreach ($myteam_data2 as $key1 => $value1) {
                    
                    array_push($myteam_id, $value1["staffid"]);
                    
                } 
            }
                
        
    }
    
       
    array_push($myteam_id, $staff_id);
    $string_version = implode(',', $myteam_id);
    
    

$ss = 'SELECT '.db_prefix().'clients.*,
        (SELECT GROUP_CONCAT(name SEPARATOR ",") FROM '.db_prefix().'customers_groups WHERE '.db_prefix().'customers_groups.id = '.db_prefix().'clients.DistributorType) as customerGroups,
        (SELECT GROUP_CONCAT(city_name SEPARATOR ",") FROM '.db_prefix().'xx_citylist WHERE '.db_prefix().'xx_citylist.id = '.db_prefix().'clients.city) as city_new,
        (SELECT GROUP_CONCAT(phonenumber SEPARATOR ",") FROM '.db_prefix().'contacts WHERE '.db_prefix().'contacts.AccountID  = '.db_prefix().'clients.AccountID AND '.db_prefix().'contacts.PlantID  = '.db_prefix().'clients.PlantID) as phonenumber2
FROM tblclients WHERE '.db_prefix().'clients.AccountID IN (SELECT customer_id FROM tblcustomer_admins WHERE staff_id IN( '.$string_version.') AND company_id = '.$plant_id.') AND PlantID ='.$plant_id.' AND Blockyn = "N"';

        $customer_data = $this->db->query($ss)->result_array();
       }
 
        
        $response=array("status"=>true,"message"=>"Customer details ","customer_data"=>$customer_data);
                
        return $response;
           
    }
    
    public function Get_allitemlist($dist_type,$dist_state_id,$plant_id)
    {
        
        
        /*$ss = 'SELECT *,
        (SELECT GROUP_CONCAT(SaleRate SEPARATOR ",") FROM '.db_prefix().'rate_master WHERE distributor_id = '.$dist_type.' AND state_id = '.$dist_state_id.' AND item_id = '.db_prefix().'items.item_code AND PlantID = '.$plant_id.') as rate,
        (SELECT GROUP_CONCAT(taxrate SEPARATOR ",") FROM '.db_prefix().'taxes WHERE id = '.db_prefix().'items.tax) as taxrate
        FROM tblitems WHERE '.db_prefix().'C.PlantID ='.$plant_id.' AND '.db_prefix().'items.isactive="Y"';*/
        
    $sql= 'SELECT i.*,r.SaleRate as rate,t.taxrate as taxrate
FROM '.db_prefix().'items i
INNER JOIN '.db_prefix().'rate_master r
ON r.item_id = i.item_code AND r.PlantID = i.PlantID
INNER JOIN '.db_prefix().'taxes t
ON t.id = i.tax
INNER JOIN '.db_prefix().'items_groups ig
ON ig.id = i.group_id
INNER JOIN '.db_prefix().'items_sub_groups isg
ON isg.id = i.subgroup_id 
WHERE r.state_id ="'.$dist_state_id.'" AND r.distributor_id ='.$dist_type.' AND r.PlantID ='.$plant_id;

                $itmem_data = $this->db->query($sql)->result_array();
        
                $response=array("status"=>true,"message"=>"Item List ","all_item_data"=>$itmem_data);
                
                return $response;
           
    }
    public function Get_next_order_number()
    {
        
        
        $sql = "SELECT * FROM `tbloptions` WHERE `name` LIKE 'next_order_number'";

                $next_order_data = $this->db->query($sql)->row_array();
        
                $response=array("status"=>true,"message"=>"Next Order","next_order_data"=>$next_order_data);
                
                return $response;
           
    }
    
    public function search_Customer($search_key,$plant_id)
    {
        
        
        $ss = 'SELECT '.db_prefix().'clients.AccountID as AccountID, '.db_prefix().'clients.company,'.db_prefix().'clients.DistributorType,
        '.db_prefix().'clients.vat, '.db_prefix().'clients.phonenumber as CompanyPhone, ' .db_prefix().'clients.address,' .db_prefix().'clients.city,
        ' .db_prefix().'clients.state, ' .db_prefix().'clients.zip, ' .db_prefix().'clients.website,
        '.db_prefix().'contacts.firstname as firstname, '.db_prefix().'contacts.email as email, '.db_prefix().'contacts.phonenumber as MobilNo,
        (SELECT GROUP_CONCAT(name SEPARATOR ",") FROM '.db_prefix().'customer_groups JOIN '.db_prefix().'customers_groups ON '.db_prefix().'customer_groups.groupid = '.db_prefix().'customers_groups.id WHERE customer_id = '.db_prefix().'clients.userid ORDER by name ASC) as customerGroups
FROM tblclients
INNER JOIN tblcontacts
ON tblclients.AccountID = tblcontacts.AccountID AND  tblclients.PlantID = tblcontacts.PlantID WHERE tblclients.company LIKE "%'.$search_key.'%" AND '.db_prefix().'clients.PlantID ='.$plant_id;

                $customer_data = $this->db->query($ss)->result_array();
        
                $response=array("status"=>true,"message"=>"search Customer details ","searchcustomer_data"=>$customer_data);
                
                return $response;
           
    }
    
    public function single_Customer_detail($customer_id,$plant_id)
    {
        
        
        $ss = 'SELECT '.db_prefix().'clients.*,'
        .db_prefix().'contacts.firstname as firstname, '.db_prefix().'contacts.email as email, '.db_prefix().'contacts.phonenumber as mobilno, '.db_prefix().'contacts.title as storetype,
        (SELECT GROUP_CONCAT(name SEPARATOR ",") FROM '.db_prefix().'customers_groups WHERE '.db_prefix().'customers_groups.id = '.db_prefix().'clients.DistributorType) as customerGroups
FROM tblclients
INNER JOIN tblcontacts
ON tblclients.AccountID = tblcontacts.AccountID WHERE tblclients.PlantID = '.$plant_id.' AND tblclients.AccountID LIKE "'.$customer_id.'"';

                $singlecustomer_data = $this->db->query($ss)->row_array();
        
                $response=array("status"=>true,"message"=>"Single Customer details","Singlecustomer_data"=>$singlecustomer_data);
                
                return $response;
           
    }
    
    public function Get_CustomerGroup($id = '')
    {
        
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix().'customers_groups')->row();
        }
        $this->db->order_by('name', 'asc');

        $customerGroup_data = $this->db->get(db_prefix().'customers_groups')->result_array();
        
                $response=array("status"=>true,"message"=>"Customer Group details ","customerGroup_data"=>$customerGroup_data);
                
                return $response;
           
    }
    
    public function Get_App_Version($status = '')
    {
        
        if (is_numeric($status)) {
            $this->db->where('status', $status);

            $App_data = $this->db->get(db_prefix().'app_version')->row();
        }
        
            $response=array("status"=>true,"message"=>"You have to new Version of this App","FieldAppVersion"=>$App_data);
                
            return $response;
           
    }
    
    public function Get_user_details_by_userID($UserID = '')
    {
        
            $this->db->where('staffid', $UserID);
            $user_data = $this->db->get(db_prefix().'staff')->row();
            return $user_data;
           
    }
    
    public function login1($email, $password, $remember, $staff)
    {
        if ((!empty($email)) and (!empty($password))) {
            $table = db_prefix() . 'contacts';
            $_id   = 'id';
            if ($staff == true) {
                $table = db_prefix() . 'staff';
                $_id   = 'staffid';
            }
            $this->db->where('email', $email);
            $user = $this->db->get($table)->row();
            if ($user) {
                // Email is okey lets check the password now
                if (!app_hasher()->CheckPassword($password, $user->password)) {
                    hooks()->do_action('failed_login_attempt', [
                        'user'            => $user,
                        'is_staff_member' => $staff,
                    ]);

                    log_activity('Failed Login Attempt [Email: ' . $email . ', Is Staff Member: ' . ($staff == true ? 'Yes' : 'No') . ', IP: ' . $this->input->ip_address() . ']');

                    // Password failed, return
                    return false;
                }
            } else {
                hooks()->do_action('non_existent_user_login_attempt', [
                    'email'           => $email,
                    'is_staff_member' => $staff,
                ]);

                log_activity('Non Existing User Tried to Login [Email: ' . $email . ', Is Staff Member: ' . ($staff == true ? 'Yes' : 'No') . ', IP: ' . $this->input->ip_address() . ']');

                return false;
            }

            if ($user->active == 0) {
                hooks()->do_action('inactive_user_login_attempt', [
                    'user'            => $user,
                    'is_staff_member' => $staff,
                ]);
                log_activity('Inactive User Tried to Login [Email: ' . $email . ', Is Staff Member: ' . ($staff == true ? 'Yes' : 'No') . ', IP: ' . $this->input->ip_address() . ']');

                return [
                    'memberinactive' => true,
                ];
            }

            $twoFactorAuth = false;
            if ($staff == true) {
                $twoFactorAuth = $user->two_factor_auth_enabled == 0 ? false : true;

                if (!$twoFactorAuth) {
                    hooks()->do_action('before_staff_login', [
                        'email'  => $email,
                        'userid' => $user->$_id,
                    ]);

                    $user_data = [
                        'staff_user_id'   => $user->$_id,
                        'staff_logged_in' => true,
                    ];
                } else {
                    $user_data                = [];
                    $user_data['tfa_staffid'] = $user->staffid;
                    if ($remember) {
                        $user_data['tfa_remember'] = true;
                    }
                }
            } else {
                hooks()->do_action('before_client_login', [
                    'email'           => $email,
                    'userid'          => $user->userid,
                    'contact_user_id' => $user->$_id,
                ]);

                $user_data = [
                    'client_user_id'   => $user->userid,
                    'contact_user_id'  => $user->$_id,
                    'client_logged_in' => true,
                ];
            }
            $this->session->set_userdata($user_data);

            if (!$twoFactorAuth) {
                if ($remember) {
                    $this->create_autologin($user->$_id, $staff);
                }

                $this->update_login_info($user->$_id, $staff);
            } else {
                return ['two_factor_auth' => true, 'user' => $user];
            }

            return true;
        }

        return false;
    }

    /**
     * @param  boolean If Client or Staff
     * @return none
     */
    public function logout($staff = true)
    {
        $this->delete_autologin($staff);

        if (is_client_logged_in()) {
            hooks()->do_action('before_contact_logout', get_client_user_id());

            $this->session->unset_userdata('client_user_id');
            $this->session->unset_userdata('client_logged_in');
        } else {
            hooks()->do_action('before_staff_logout', get_staff_user_id());

            $this->session->unset_userdata('staff_user_id');
            $this->session->unset_userdata('staff_logged_in');
        }

        $this->session->sess_destroy();
    }

    /**
     * @param  integer ID to create autologin
     * @param  boolean Is Client or Staff
     * @return boolean
     */
    private function create_autologin($user_id, $staff)
    {
        $this->load->helper('cookie');
        $key = substr(md5(uniqid(rand() . get_cookie($this->config->item('sess_cookie_name')))), 0, 16);
        $this->user_autologin->delete($user_id, $key, $staff);
        if ($this->user_autologin->set($user_id, md5($key), $staff)) {
            set_cookie([
                'name'  => 'autologin',
                'value' => serialize([
                    'user_id' => $user_id,
                    'key'     => $key,
                ]),
                'expire' => 60 * 60 * 24 * 31 * 2, // 2 months
            ]);

            return true;
        }

        return false;
    }

    /**
     * @param  boolean Is Client or Staff
     * @return none
     */
    private function delete_autologin($staff)
    {
        $this->load->helper('cookie');
        if ($cookie = get_cookie('autologin', true)) {
            $data = unserialize($cookie);
            $this->user_autologin->delete($data['user_id'], md5($data['key']), $staff);
            delete_cookie('autologin', 'aal');
        }
    }

    /**
     * @return boolean
     * Check if autologin found
     */
    public function autologin()
    {
        if (!is_logged_in()) {
            $this->load->helper('cookie');
            if ($cookie = get_cookie('autologin', true)) {
                $data = unserialize($cookie);
                if (isset($data['key']) and isset($data['user_id'])) {
                    if (!is_null($user = $this->user_autologin->get($data['user_id'], md5($data['key'])))) {
                        // Login user
                        if ($user->staff == 1) {
                            $user_data = [
                                'staff_user_id'   => $user->id,
                                'staff_logged_in' => true,
                            ];
                        } else {
                            // Get the customer id
                            $this->db->select('userid');
                            $this->db->where('id', $user->id);
                            $contact = $this->db->get(db_prefix() . 'contacts')->row();

                            $user_data = [
                                'client_user_id'   => $contact->userid,
                                'contact_user_id'  => $user->id,
                                'client_logged_in' => true,
                            ];
                        }
                        $this->session->set_userdata($user_data);
                        // Renew users cookie to prevent it from expiring
                        set_cookie([
                            'name'   => 'autologin',
                            'value'  => $cookie,
                            'expire' => 60 * 60 * 24 * 31 * 2, // 2 months
                        ]);
                        $this->update_login_info($user->id, $user->staff);

                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param  integer ID
     * @param  boolean Is Client or Staff
     * @return none
     * Update login info on autologin
     */
    private function update_login_info($user_id, $staff)
    {
        $table = db_prefix() . 'contacts';
        $_id   = 'id';
        if ($staff == true) {
            $table = db_prefix() . 'staff';
            $_id   = 'staffid';
        }
        $this->db->set('last_ip', $this->input->ip_address());
        $this->db->set('last_login', date('Y-m-d H:i:s'));
        $this->db->where($_id, $user_id);
        $this->db->update($table);
    }

    /**
     * Send set password email for contacts
     * @param string $email
     */
    public function set_password_email($email)
    {
        $this->db->where('email', $email);
        $user = $this->db->get(db_prefix() . 'contacts')->row();

        if ($user) {
            if ($user->active == 0) {
                return [
                    'memberinactive' => true,
                ];
            }

            $new_pass_key = app_generate_hash();
            $this->db->where('id', $user->id);
            $this->db->update(db_prefix() . 'contacts', [
                'new_pass_key'           => $new_pass_key,
                'new_pass_key_requested' => date('Y-m-d H:i:s'),
            ]);
            if ($this->db->affected_rows() > 0) {
                $data['new_pass_key'] = $new_pass_key;
                $data['userid']       = $user->id;
                $data['email']        = $email;

                $sent = send_mail_template('customer_contact_set_password', $user, $data);

                if ($sent) {
                    hooks()->do_action('set_password_email_sent', ['is_staff_member' => false, 'user' => $user]);

                    return true;
                }

                return false;
            }

            return false;
        }

        return false;
    }

    /**
     * @param  string Email from the user
     * @param  Is Client or Staff
     * @return boolean
     * Generate new password key for the user to reset the password.
     */
    public function forgot_password($email, $staff = false)
    {
        $table = db_prefix() . 'contacts';
        $_id   = 'id';
        if ($staff == true) {
            $table = db_prefix() . 'staff';
            $_id   = 'staffid';
        }
        $this->db->where('email', $email);
        $user = $this->db->get($table)->row();

        if ($user) {
            if ($user->active == 0) {
                return [
                    'memberinactive' => true,
                ];
            }

            $new_pass_key = app_generate_hash();
            $this->db->where($_id, $user->$_id);
            $this->db->update($table, [
                'new_pass_key'           => $new_pass_key,
                'new_pass_key_requested' => date('Y-m-d H:i:s'),
            ]);

            if ($this->db->affected_rows() > 0) {
                $data['new_pass_key'] = $new_pass_key;
                $data['staff']        = $staff;
                $data['userid']       = $user->$_id;
                $merge_fields         = [];

                if ($staff == false) {
                    $sent = send_mail_template('customer_contact_forgot_password', $user->email, $user->userid, $user->$_id, $data);
                } else {
                    $sent = send_mail_template('staff_forgot_password', $user->email, $user->$_id, $data);
                }

                if ($sent) {
                    hooks()->do_action('forgot_password_email_sent', ['is_staff_member' => $staff, 'user' => $user]);

                    return true;
                }

                return false;
            }

            return false;
        }

        return false;
    }

    /**
     * Update user password from forgot password feature or set password
     * @param boolean $staff        is staff or contact
     * @param mixed $userid
     * @param string $new_pass_key the password generate key
     * @param string $password     new password
     */
    public function set_password($staff, $userid, $new_pass_key, $password)
    {
        if (!$this->can_set_password($staff, $userid, $new_pass_key)) {
            return [
                'expired' => true,
            ];
        }

        $password = app_hash_password($password);
        $table    = db_prefix() . 'contacts';
        $_id      = 'id';
        if ($staff == true) {
            $table = db_prefix() . 'staff';
            $_id   = 'staffid';
        }
        $this->db->where($_id, $userid);
        $this->db->where('new_pass_key', $new_pass_key);
        $this->db->update($table, [
            'password' => $password,
        ]);
        if ($this->db->affected_rows() > 0) {
            log_activity('User Set Password [User ID: ' . $userid . ', Is Staff Member: ' . ($staff == true ? 'Yes' : 'No') . ', IP: ' . $this->input->ip_address() . ']');
            $this->db->set('new_pass_key', null);
            $this->db->set('new_pass_key_requested', null);
            $this->db->set('last_password_change', date('Y-m-d H:i:s'));
            $this->db->where($_id, $userid);
            $this->db->where('new_pass_key', $new_pass_key);
            $this->db->update($table);

            return true;
        }

        return null;
    }

    /**
     * @param  boolean Is Client or Staff
     * @param  integer ID
     * @param  string
     * @param  string
     * @return boolean
     * User reset password after successful validation of the key
     */
    public function reset_password($staff, $userid, $new_pass_key, $password)
    {
        if (!$this->can_reset_password($staff, $userid, $new_pass_key)) {
            return [
                'expired' => true,
            ];
        }
        $password = app_hash_password($password);
        $table    = db_prefix() . 'contacts';
        $_id      = 'id';
        if ($staff == true) {
            $table = db_prefix() . 'staff';
            $_id   = 'staffid';
        }

        $this->db->where($_id, $userid);
        $this->db->where('new_pass_key', $new_pass_key);
        $this->db->update($table, [
            'password' => $password,
        ]);
        if ($this->db->affected_rows() > 0) {
            log_activity('User Reseted Password [User ID: ' . $userid . ', Is Staff Member: ' . ($staff == true ? 'Yes' : 'No') . ', IP: ' . $this->input->ip_address() . ']');
            $this->db->set('new_pass_key', null);
            $this->db->set('new_pass_key_requested', null);
            $this->db->set('last_password_change', date('Y-m-d H:i:s'));
            $this->db->where($_id, $userid);
            $this->db->where('new_pass_key', $new_pass_key);
            $this->db->update($table);
            $this->db->where($_id, $userid);
            $user = $this->db->get($table)->row();

            $merge_fields = [];
            if ($staff == false) {
                $sent = send_mail_template('customer_contact_password_resetted', $user->email, $user->userid, $user->$_id);
            } else {
                $sent = send_mail_template('staff_password_resetted', $user->email, $user->$_id);
            }

            if ($sent) {
                return true;
            }
        }

        return null;
    }

    /**
     * @param  integer Is Client or Staff
     * @param  integer ID
     * @param  string Password reset key
     * @return boolean
     * Check if the key is not expired or not exists in database
     */
    public function can_reset_password($staff, $userid, $new_pass_key)
    {
        $table = db_prefix() . 'contacts';
        $_id   = 'id';
        if ($staff == true) {
            $table = db_prefix() . 'staff';
            $_id   = 'staffid';
        }

        $this->db->where($_id, $userid);
        $this->db->where('new_pass_key', $new_pass_key);
        $user = $this->db->get($table)->row();

        if ($user) {
            $timestamp_now_minus_1_hour = time() - (60 * 60);
            $new_pass_key_requested     = strtotime($user->new_pass_key_requested);
            if ($timestamp_now_minus_1_hour > $new_pass_key_requested) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @param  integer Is Client or Staff
     * @param  integer ID
     * @param  string Password reset key
     * @return boolean
     * Check if the key is not expired or not exists in database
     */
    public function can_set_password($staff, $userid, $new_pass_key)
    {
        $table = db_prefix() . 'contacts';
        $_id   = 'id';
        if ($staff == true) {
            $table = db_prefix() . 'staff';
            $_id   = 'staffid';
        }
        $this->db->where($_id, $userid);
        $this->db->where('new_pass_key', $new_pass_key);
        $user = $this->db->get($table)->row();
        if ($user) {
            $timestamp_now_minus_48_hour = time() - (3600 * 48);
            $new_pass_key_requested      = strtotime($user->new_pass_key_requested);
            if ($timestamp_now_minus_48_hour > $new_pass_key_requested) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Get user from database by 2 factor authentication code
     * @param  string $code authentication code to search for
     * @return object
     */
    public function get_user_by_two_factor_auth_code($code)
    {
        $this->db->where('two_factor_auth_code', $code);

        return $this->db->get(db_prefix() . 'staff')->row();
    }

    /**
     * Login user via two factor authentication
     * @param  object $user user object
     * @return boolean
     */
    public function two_factor_auth_login($user)
    {
        hooks()->do_action('before_staff_login', [
            'email'  => $user->email,
            'userid' => $user->staffid,
        ]);

        $this->session->set_userdata(
            [
                'staff_user_id'   => $user->staffid,
                'staff_logged_in' => true,
            ]
        );

        $remember = null;
        if ($this->session->has_userdata('tfa_remember')) {
            $remember = true;
            $this->session->unset_userdata('tfa_remember');
        }

        if ($remember) {
            $this->create_autologin($user->staffid, true);
        }

        $this->update_login_info($user->staffid, true);

        return true;
    }

    /**
     * Check if 2 factor authentication code is valid for usage
     * @param  string  $code auth code
     * @return boolean
     */
    public function is_two_factor_code_valid($code)
    {
        $this->db->select('two_factor_auth_code_requested');
        $this->db->where('two_factor_auth_code', $code);
        $user = $this->db->get(db_prefix() . 'staff')->row();

        // Code not exists because no user is found
        if (!$user) {
            return false;
        }

        $timestamp_minus_1_hour = time() - (60 * 60);
        $new_code_key_requested = strtotime($user->two_factor_auth_code_requested);
        // The code is older then 1 hour and its not valid
        if ($timestamp_minus_1_hour > $new_code_key_requested) {
            return false;
        }
        // Code is valid
        return true;
    }

    /**
     * Clears 2 factor authentication code in database
     * @param  mixed $id
     * @return boolean
     */
    public function clear_two_factor_auth_code($id)
    {
        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'staff', [
            'two_factor_auth_code' => null,
        ]);

        return true;
    }

    /**
     * Set 2 factor authentication code for staff member
     * @param mixed $id staff id
     */
    public function set_two_factor_auth_code($id)
    {
        $code = generate_two_factor_auth_key();
        $code .= $id;

        $this->db->where('staffid', $id);
        $this->db->update(db_prefix() . 'staff', [
            'two_factor_auth_code'           => $code,
            'two_factor_auth_code_requested' => date('Y-m-d H:i:s'),
        ]);

        return $code;
    }

    public function get_qr($System_name)
    {
        $staff    = get_staff(get_staff_user_id());
        $g        = new GoogleAuthenticator();
        $secret   = $g->generateSecret();
        $username = urlencode($staff->email);
        $url      = \Sonata\GoogleAuthenticator\GoogleQrUrl::generate($username, $secret, $System_name);

        return ['qrURL' => $url, 'secret' => $secret];
    }

    public function set_google_two_factor($secret)
    {
        $id     = get_staff_user_id();
        $secret = $this->encrypt($secret);

        $this->db->where('staffid', $id);
        $success = $this->db->update(db_prefix() . 'staff', [
            'two_factor_auth_enabled' => 2,
            'google_auth_secret'      => $secret,
        ]);

        if ($success) {
            return true;
        }

        return false;
    }

    public function is_google_two_factor_code_valid($code, $secret = null)
    {
        $g = new GoogleAuthenticator();

        if (!is_null($secret)) {
            return $g->checkCode($secret, $code);
        }

        $staffid = $this->session->userdata('tfa_staffid');

        $this->db->select('google_auth_secret')
            ->where('staffid', $staffid);

        if ($staff = $this->db->get('staff')->row()) {
            return $g->checkCode(
                $this->decrypt($staff->google_auth_secret),
                $code
            );
        }

        return false;
    }

    public function encrypt($string)
    {
        $this->load->library('encryption');

        return $this->encryption->encrypt($string);
    }

    public function decrypt($string)
    {
        $this->load->library('encryption');

        return $this->encryption->decrypt($string);
    }
     public function targetAchievementAPI($staff_id,$plant_id)
    {
        
       $staff_ids = array();
        array_push($staff_ids, $staff_id);
        if($staff_id){
            $get_sql1 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$staff_id.'"';
            $get_result1 = $this->db->query($get_sql1)->result_array();
            foreach ($get_result1 as $key1 => $value1) {
                array_push($staff_ids, $value1["staffid"]);
                $get_sql2 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value1["staffid"].'"';
                $get_result2 = $this->db->query($get_sql2)->result_array();
                foreach ($get_result2 as $key2 => $value2) {
                    array_push($staff_ids, $value2["staffid"]);
                    $get_sql3 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value2["staffid"].'"';
                    $get_result3 = $this->db->query($get_sql3)->result_array();
                    foreach ($get_result3 as $key3 => $value3) {
                        array_push($staff_ids, $value3["staffid"]);
                        $get_sql4 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value3["staffid"].'"';
                        $get_result4 = $this->db->query($get_sql4)->result_array();
                        foreach ($get_result4 as $key4 => $value4) {
                            array_push($staff_ids, $value4["staffid"]);
                            $get_sql5 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value4["staffid"].'"';
                            $get_result5 = $this->db->query($get_sql5)->result_array();
                            foreach ($get_result5 as $key5 => $value5) {
                                array_push($staff_ids, $value5["staffid"]);
                            }
                        }
                    }
                }
            }
        }
    $staff_ids_uniqu = array_unique($staff_ids);  
    $staff_ids_uniqu_s = implode(", ", $staff_ids_uniqu);
             $from_date = date('Y')."-".date('m')."-01 00:00:00";
         $to_date = date('Y-m-d').' 23:59:59';
    
        //  $from_date = '2021-12-01 00:00:00';
        //  $to_date = '2021-12-31 23:23:23';
           $selected_company = $plant_id;
             if ( date('m') <= 3 ) {
            $year = date('y') - 1;
        }
        else {
            $year = date('y');
        }
        
        $month = date('m');
        // $month = 12;
        // $year = 21;
        $account_id = array();
       $this->db->select(db_prefix() . 'clients.AccountID,'.db_prefix() . 'clients.company');
        $this->db->from(db_prefix() . 'customer_admins');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'customer_admins.customer_id', 'left');
       
       $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
       $this->db->where_in(db_prefix() . 'customer_admins.staff_id', $staff_ids_uniqu_s);
       $this->db->order_by(db_prefix() . 'clients.company');
       $data_array =  $this->db->get()->result_array();
       foreach($data_array as $value){
            array_push($account_id, $value["AccountID"]);
       }
       $AccountId = implode(", ", $account_id);
       
    
       $SQL2 = "SELECT SUM(Targate) as target FROM `tblstaff_target` WHERE `AccountID` IN('".implode("','",$account_id)."') AND `PlantID` ='$selected_company' AND FY = '$year' and MonthID = '$month'";
            $total_target = $this->db->query($SQL2)->result();
        $SQL = "SELECT SUM(`tblhistory`.`NetChallanAmt`) AS `achievement` FROM `tblhistory` WHERE `PlantID` = '$selected_company' AND `FY` = '$year' AND `TransDate` BETWEEN '$from_date' AND '$to_date' AND `TType` LIKE 'O' AND `TType2` LIKE 'Order' AND `AccountID` IN('".implode("','",$account_id)."')";
       
        $count_acheivement = $this->db->query($SQL)->result();
         $count_acheivement = round($count_acheivement[0]->achievement);
        $total_target = round($total_target[0]->target);
        $response=array("status"=>true,"message"=>"Target_and_Achievement","achievement"=>"$count_acheivement","target"=>"$total_target");
            return $response;
                
            
    }
    
    public function division_targetAchievementAPI($staff_id = '',$PlantID = '',$month = '',$party_id = '')
    {
        if($party_id != null){
             
        }else{
            $selected_company = $PlantID;
            $staff_ids = array();
        array_push($staff_ids, $staff_id);
        if($staff_id){
            $get_sql1 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$staff_id.'"';
            $get_result1 = $this->db->query($get_sql1)->result_array();
            foreach ($get_result1 as $key1 => $value1) {
                array_push($staff_ids, $value1["staffid"]);
                $get_sql2 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value1["staffid"].'"';
                $get_result2 = $this->db->query($get_sql2)->result_array();
                foreach ($get_result2 as $key2 => $value2) {
                    array_push($staff_ids, $value2["staffid"]);
                    $get_sql3 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value2["staffid"].'"';
                    $get_result3 = $this->db->query($get_sql3)->result_array();
                    foreach ($get_result3 as $key3 => $value3) {
                        array_push($staff_ids, $value3["staffid"]);
                        $get_sql4 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value3["staffid"].'"';
                        $get_result4 = $this->db->query($get_sql4)->result_array();
                        foreach ($get_result4 as $key4 => $value4) {
                            array_push($staff_ids, $value4["staffid"]);
                            $get_sql5 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value4["staffid"].'"';
                            $get_result5 = $this->db->query($get_sql5)->result_array();
                            foreach ($get_result5 as $key5 => $value5) {
                                array_push($staff_ids, $value5["staffid"]);
                            }
                        }
                    }
                }
            }
        }
    $staff_ids_uniqu = array_unique($staff_ids);  
    $staff_ids_uniqu_s = implode(", ", $staff_ids_uniqu);
    
     $account_id = array();
      $this->db->select(db_prefix() . 'clients.AccountID,'.db_prefix() . 'clients.company');
        $this->db->from(db_prefix() . 'customer_admins');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'customer_admins.customer_id', 'left');
       
      $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
      $this->db->where_in(db_prefix() . 'customer_admins.staff_id', $staff_ids_uniqu_s);
      $this->db->order_by(db_prefix() . 'clients.company');
      $data_array =  $this->db->get()->result_array();
      foreach($data_array as $value){
            array_push($account_id, "'".$value["AccountID"]."'");
      }
       $AccountId = implode(", ", $account_id);
       $account_id_array = $AccountId;
        }
          if(date('m') < $month){
            $year = date('Y')-1;
            $from_date = $year."-".$month."-01";
             $to_date = $year."-".$month."-31";
        }else{
            $from_date = date('Y')."-".$month."-01";
             $to_date = date('Y')."-".$month."-31";
        }
        //      $from_date = date('Y')."-".$month."-01 00:00:00";
        //  $to_date = date('Y-m-d').' 23:23:23';
    
        //  $from_date = '2022-02-01 00:00:00';
        //  $to_date = '2022-02-31 23:59:59';
          $selected_company = $PlantID;
             if ( date('m') <= 3 ) {
            $year = date('y') - 1;
        }
        else {
            $year = date('y');
        }
        
        //$month = date('m');
        //$month = 02;
        // $year = 21;
       
 
    
    
     $this->db->select_sum(db_prefix() . 'history.NetChallanAmt');
          $this->db->select(db_prefix() . 'history.AccountID,'.db_prefix() . 'staff_target.ItemDivID,'.db_prefix() . 'items_groups.name');
    $this->db->from(db_prefix() . 'staff_target');
    
  $this->db->join(db_prefix() . 'items', db_prefix() . 'items.group_id = ' . db_prefix() . 'staff_target.ItemDivID AND '. db_prefix() . 'items.PlantID = '.$selected_company , 'left');
  $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id', 'left');
    $this->db->join(db_prefix() . 'history', db_prefix() . 'history.AccountID = ' . db_prefix() . 'staff_target.AccountID AND '.db_prefix() . 'history.ItemID = ' . db_prefix() . 'items.item_code AND '.db_prefix() . 'history.PlantID = '.$selected_company.' AND '.db_prefix() . 'history.FY = '.$year, 'left');
   if($party_id != null){
       $this->db->like(db_prefix() . 'staff_target.AccountID', $party_id);
    
   }else{
       $this->db->where_in(db_prefix() . 'staff_target.AccountID', $account_id_array,FALSE);
   }
    $this->db->like(db_prefix() . 'staff_target.MonthID', $month);
    $this->db->where(db_prefix() . 'staff_target.PlantID', $selected_company);
    $this->db->like(db_prefix() . 'staff_target.FY', $year);
      $this->db->like(db_prefix() . 'history.TType', 'O');
      $this->db->like(db_prefix() . 'history.TType2', 'Order');
      $this->db->where(db_prefix() . 'history.OrderID !=', NULL);
      $this->db->where(db_prefix() . 'history.TransID !=', NULL);
        $this->db->where(db_prefix() . 'history.TransDate2 >=', $from_date.' 00:00:00');
        $this->db->where(db_prefix() . 'history.TransDate2 <=',$to_date.' 23:59:59');
         $this->db->group_by(db_prefix() . 'staff_target.ItemDivID');
    $acheivement_data =  $this->db->get()->result_array();
      $q =$this->db->last_query(); 
      
      $this->db->select_sum(db_prefix() . 'staff_target.Targate');
      $this->db->select(db_prefix() . 'staff_target.AccountID,'.db_prefix() . 'staff_target.ItemDivID,'.db_prefix() . 'items_groups.name');
 
        $this->db->from(db_prefix() . 'staff_target');
       $this->db->join(db_prefix() . 'items_groups', db_prefix() . 'items_groups.id = ' . db_prefix() . 'staff_target.ItemDivID', 'left');
       $this->db->where(db_prefix() . 'staff_target.PlantID', $selected_company);
       $this->db->where(db_prefix() . 'staff_target.FY', $year);
         $this->db->where(db_prefix() . 'staff_target.MonthID', $month);
            //   $this->db->where_in(db_prefix() . 'staff_target.AccountID', $account_id_array,FALSE);
     if($party_id != null){
       $this->db->where(db_prefix() . 'staff_target.AccountID', $party_id);
    
   }else{
       $this->db->where_in(db_prefix() . 'staff_target.AccountID', $account_id_array,FALSE);
   }
     
       $this->db->group_by(db_prefix() . 'staff_target.ItemDivID');
        $target_data = $this->db->get()->result_array();
       
       
        // print_r($q);die;
        $original = array();
        if(count($target_data) > 0 && count($acheivement_data) > 0){
            
            $i = 0;
            $total_target = 0;
            $total_acheivement = 0;
            foreach($target_data as $target_data_value){
                 foreach($acheivement_data as $acheivement_data_value){
                if($target_data_value['ItemDivID'] == $acheivement_data_value['ItemDivID'] && $target_data_value['AccountID'] == $acheivement_data_value['AccountID']){
                 
                  $original[$i]['account'] = $target_data_value['AccountID'];  
                  $original[$i]['division_name'] = $target_data_value['name']; 
                  
                  $original[$i]['target'] = round($target_data_value['Targate']);  
                  $total_target+=$target_data_value['Targate'];
                  
                  $original[$i]['acheivement'] = round($acheivement_data_value['NetChallanAmt']); 
                  $total_acheivement+=$acheivement_data_value['NetChallanAmt'];
                  $i++;
                }
                 }
            
        }
        $total_target = round($total_target);
        $total_acheivement = round($total_acheivement);
         $original[$i]['account'] = ""; 
         $original[$i]['division_name'] = "Total";
         $original[$i]['target'] = "$total_target"; 
         $original[$i]['acheivement'] = "$total_acheivement"; 
         $response=array("status"=>true,"message"=>"Target_and_Achievement","achievement"=>$original);
        }else{
           $response=array("status"=>true,"message"=>"Target_and_Achievement","achievement"=>$original);  
        }
        // print_r($q);die;
        return $response;
          
    }
    
    public function GetSaleReport($staff_id = '',$PlantID = '',$AsOn = '',$admin = '')
    {
        $selected_company = $PlantID;
        $fromDateNew = $AsOn.' 00:00:00';
        $toDateNew = $AsOn.' 23:59:59'; 
        $staff_ids = array();
        array_push($staff_ids, $staff_id);
        if($staff_id){
            $get_sql1 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$staff_id.'"';
            $get_result1 = $this->db->query($get_sql1)->result_array();
            foreach ($get_result1 as $key1 => $value1) {
                array_push($staff_ids, $value1["staffid"]);
                $get_sql2 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value1["staffid"].'"';
                $get_result2 = $this->db->query($get_sql2)->result_array();
                foreach ($get_result2 as $key2 => $value2) {
                    array_push($staff_ids, $value2["staffid"]);
                    $get_sql3 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value2["staffid"].'"';
                    $get_result3 = $this->db->query($get_sql3)->result_array();
                    foreach ($get_result3 as $key3 => $value3) {
                        array_push($staff_ids, $value3["staffid"]);
                        $get_sql4 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value3["staffid"].'"';
                        $get_result4 = $this->db->query($get_sql4)->result_array();
                        foreach ($get_result4 as $key4 => $value4) {
                            array_push($staff_ids, $value4["staffid"]);
                            $get_sql5 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value4["staffid"].'"';
                            $get_result5 = $this->db->query($get_sql5)->result_array();
                            foreach ($get_result5 as $key5 => $value5) {
                                array_push($staff_ids, $value5["staffid"]);
                            }
                        }
                    }
                }
            }
        }
        $staff_ids_uniqu = array_unique($staff_ids);  
        $staff_ids_uniqu_s = implode(", ", $staff_ids_uniqu);
        
        $account_id = array();
        $this->db->select(db_prefix() . 'clients.AccountID,'.db_prefix() . 'clients.company');
        $this->db->from(db_prefix() . 'customer_admins');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'customer_admins.customer_id', 'left');
           
        $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
        if($admin == "0"){
            $this->db->where_in(db_prefix() . 'customer_admins.staff_id', $staff_ids_uniqu_s);
        }
        $this->db->order_by(db_prefix() . 'clients.company');
        $data_array =  $this->db->get()->result_array();
        foreach($data_array as $value){
            array_push($account_id, $value["AccountID"]);
        }
        $account_iduniqu = array_unique($account_id); 
        $this->db->select(db_prefix() . 'clients.AccountID,'.db_prefix() . 'clients.company,'.db_prefix() . 'clients.StationName,'.db_prefix() . 'salesmaster.SalesID,'.db_prefix() . 'salesmaster.BillAmt,'.db_prefix() . 'salesmaster.Transdate');
        $this->db->from(db_prefix() . 'salesmaster');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'salesmaster.AccountID AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'salesmaster.PlantID ');
           
        $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
        $this->db->where(db_prefix() . 'salesmaster.PlantID', $selected_company);
        $this->db->where_in(db_prefix() . 'salesmaster.AccountID', $account_iduniqu);
        $this->db->where(db_prefix() . 'salesmaster.Transdate BETWEEN "'. $fromDateNew. '" AND "'. $toDateNew.'"');
        $this->db->order_by(db_prefix() . 'salesmaster.Transdate');
        $data =  $this->db->get()->result_array();
        
        $response=array("status"=>true,"message"=>"Sale Report","SaleReport"=>$data);  
       
        return $response;
          
    }
    
    public function CheckAccountID($AccountID)
    {
        $this->db->select(db_prefix() . 'clients.AccountID');
        $this->db->from(db_prefix() . 'clients');
        $this->db->where(db_prefix() . 'clients.AccountID', $AccountID);
        $data =  $this->db->get()->row();
        
        $this->db->select(db_prefix() . 'staff.AccountID');
        $this->db->from(db_prefix() . 'staff');
        $this->db->where(db_prefix() . 'staff.AccountID', $AccountID);
        $data2 =  $this->db->get()->row();
        $result = array(
            "Client" =>$data,
            "Staff" =>$data2,
        );
        $response = array("status"=>true,"message"=>"Detail","Data"=>$result);  
       
        return $response;
    }
    
    public function CheckParty($AccountID)
    {
        $this->db->select(db_prefix() . 'clients.AccountID');
        $this->db->from(db_prefix() . 'clients');
        $this->db->where(db_prefix() . 'clients.AccountID', $AccountID);
        $data =  $this->db->get()->row();
        if($data){
            return false;
        }
        
        $this->db->select(db_prefix() . 'staff.AccountID');
        $this->db->from(db_prefix() . 'staff');
        $this->db->where(db_prefix() . 'staff.AccountID', $AccountID);
        $data2 =  $this->db->get()->row();
        if($data2){
            return false;
        }
        return true;
    }
    
    public function SavePartyDetails($Clientdata,$Contactdata,$Baldata,$EnqID)
    {
        $company_data = $this->GetRootCompany();
        $company_data = $this->GetRootCompany();
        foreach ($company_data as $key => $value) {
            $Clientdata['PlantID'] = $value['id'];
            $Contactdata['PlantID'] = $value['id'];
            $Baldata['PlantID'] = $value['id'];
            $this->db->insert(db_prefix() . 'clients', $Clientdata);
                $INSERT = $this->db->affected_rows();
                if($INSERT > 0){
                    $this->db->insert(db_prefix() . 'contacts', $Contactdata);
                    $this->db->insert(db_prefix() . 'accountbalances', $Baldata);
                }
        }
        if($INSERT > 0){
            $update_array = array(
                "EnqConvert" =>'Y'
            );
            $this->db->where('id', $EnqID);
            $this->db->update(db_prefix() . 'so_enquiry', $update_array);
            $response = array("status"=>true,"message"=>"Create Party Successfully");  
        }else{
            $response = array("status"=>false,"message"=>"Something went wrong"); 
        }
        return $response;
    }
    
    public function GetSaleDetails($SaleID)
    {
        $this->db->select(db_prefix() . 'challanmaster.ChallanID,'.db_prefix() . 'challanmaster.VehicleID,'.db_prefix() . 'challanmaster.DriverID,'.db_prefix() . 'challanmaster.gatepasstime,'.db_prefix() . 'salesmaster.BillAmt,'.db_prefix() . 'salesmaster.Transdate,'.db_prefix() . 'salesmaster.SalesID,'.db_prefix() . 'staff.firstname,'.db_prefix() . 'staff.lastname,'.db_prefix() . 'staff.phonenumber');
        $this->db->from(db_prefix() . 'salesmaster');
        $this->db->join(db_prefix() . 'challanmaster', db_prefix() . 'challanmaster.ChallanID = ' . db_prefix() . 'salesmaster.ChallanID AND '.db_prefix() . 'challanmaster.PlantID = ' . db_prefix() . 'salesmaster.PlantID AND '.db_prefix() . 'challanmaster.FY = ' . db_prefix() . 'salesmaster.FY');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.AccountID = ' . db_prefix() . 'challanmaster.DriverID');
        
        $this->db->where(db_prefix() . 'salesmaster.SalesID', $SaleID);
        $data =  $this->db->get()->row();
        
        $this->db->select(db_prefix() . 'history.OrderID,'.db_prefix() . 'history.TransDate,'.db_prefix() . 'history.ItemID,'.db_prefix() . 'history.SaleRate,'.db_prefix() . 'history.BilledQty,'.db_prefix() . 'history.CaseQty,
        '.db_prefix() . 'history.cgst,'.db_prefix() . 'history.cgstamt,'.db_prefix() . 'history.sgst,'.db_prefix() . 'history.sgstamt,'.db_prefix() . 'history.cgst,'.db_prefix() . 'history.cgstamt,'.db_prefix() . 'history.igst,'.db_prefix() . 'history.igstamt,'.db_prefix() . 'items.description AS ItemName
        ,'.db_prefix() . 'history.ChallanAmt,'.db_prefix() . 'history.NetChallanAmt');
        $this->db->from(db_prefix() . 'history');
        $this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
        $this->db->where(db_prefix() . 'history.TransID', $SaleID);
        $this->db->where(db_prefix() . 'history.TType', 'O');
        $this->db->where(db_prefix() . 'history.TType2', 'Order');
        $Itemdata =  $this->db->get()->result_array();
        $data->ItemDetail = $Itemdata;
        $response = array("status"=>true,"message"=>"Sale Detail","SaleDetail"=>$data);  
       
        return $response;
    }
    public function GetOfficeAddress($staff_id)
    {
        $ss = 'SELECT tbltimesheets_workplace_assign.staffid, tblstaff.Movement, tbltimesheets_workplace.* FROM tbltimesheets_workplace_assign 
        INNER JOIN tbltimesheets_workplace ON tbltimesheets_workplace.id = tbltimesheets_workplace_assign.workplace_id
        INNER JOIN tblstaff ON tblstaff.staffid= tbltimesheets_workplace_assign.staffid
        WHERE tbltimesheets_workplace_assign.staffid="'.$staff_id.'" ' ;
        $WorkPlaceAssign = $this->db->query($ss)->row();
        if($WorkPlaceAssign){
            $response = array("status"=>true,"message"=>"Office Details...","Data"=>$WorkPlaceAssign);
            return $response;
        }else {
            $response = array("status"=>false,"message"=>"Please Allocate Office Address","Data"=>null);
            return $response;
        }
    }
    
    // Get Root Company
    public function GetRootCompany()
    {
        $this->db->select(db_prefix() . 'rootcompany.*');
        $this->db->order_by('id', 'ASC');
        $this->db->from(db_prefix() . 'rootcompany');
        $data =  $this->db->get()->result_array();
        return $data;
    }
    
    public function GetAppVersion($status = '')
    {   
        if (is_numeric($status)) {
            $this->db->where('status', $status);
            $version_data = $this->db->get('tblStaffapp_version')->row();
        }
        $response=array("status"=>true,"message"=>"You have to new Version of this App","FieldAppVersion"=>$version_data);
        return $response;
    }
}
