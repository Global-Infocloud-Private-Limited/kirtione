<?php
defined('BASEPATH') or exit('No direct script access allowed');
	
class BrokerInitiate extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('BrokerInitiate_model');
	}    
    public function index()
	{
        if(!has_permission_new('BrokerInitiateRequest', '', 'view')) {
		  access_denied('Booking settlement');  
		}
		$data['AllInitiateRequest'] = $this->BrokerInitiate_model->GetAllRequest();
		$data['title'] = "Broker Initiate Request";
		$this->load->view('admin/BrokerInitiate/BrokerInitiate',$data);
	}
	
	public function GetAccountDetails()
	{
		$data = array(
			'AccountID' => $this->input->post('AccountID')
        );
		$result = $this->BrokerInitiate_model->GetAccountDetails($data);
		echo json_encode($result);
		die;
	}
	public function TraderBrokerAssign()
	{
		$BrokerID = $this->input->post('BrokerID');
		$TraderID = $this->input->post('TraderID');
		$checkRequstExists = $this->get_request_exists($BrokerID,$TraderID);
		if($checkRequstExists){
            if($checkRequstExists->status == "NA"){
                $status_message = "Sent";
            }else{
                $status_message = "Approved";
            }
            $message = "Request already ".$status_message;
            $response = array("status"=>false,"message"=>$message);
        }else{
            $data = array(
    		    "send_from"=>$TraderID,
    		    "send_to"=>$BrokerID,
    		    "TransDate"=>date('Y-m-d H:i:s'),
    		    "UserID"=>$this->session->userdata('username'),
    		    "status"=>"Y"
    		);
    		if($this->db->insert('tbltrader_broker_assigned',$data)){
    		    $inserted_id = $this->db->insert_id();
    		    $GetRequestDetails = $this->BrokerInitiate_model->GetReq_details($inserted_id);
    		    // Send Notification to Send From
                $title = "Broker Assigned";
                $screen = "4";
                $body = $GetRequestDetails->SendToName . " as a broker assigned to you by Kisan Kirti";
                $booking_id = $GetRequestDetails->send_from;
                $to = $GetRequestDetails->SendFromfcm_token;
                $this->BrokerInitiate_model->send_notification($title,$screen,$body,$booking_id,$to);
                
                // Send Notification to Send To
                $title = "Trader Assigned";
                $body = $GetRequestDetails->SendFromName ." as a trader assigned to you by Kisan Kirti";
                $booking_id = $GetRequestDetails->send_to;
                $to = $GetRequestDetails->SendTofcm_token;
                $this->BrokerInitiate_model->send_notification($title,$screen,$body,$booking_id,$to);
                $message = "Trader Broker Mapped Successfully";
                $response = array("status"=>true,"message"=>$message);
    		}else{
    		    $message = "something went wrong please try again";
    		    $response = array("status"=>false,"message"=>$message);
    		}
        }
		
		echo json_encode($response);
		die;
	}
	
	public function get_request_exists($From,$To)
    {
        $status = array('NA','Y');
        $this->db->select('tbltrader_broker_assigned.*');
        $this->db->where('tbltrader_broker_assigned.send_from',$From);
        $this->db->where('tbltrader_broker_assigned.send_to',$To);
        $this->db->where_in('tbltrader_broker_assigned.status',$status);
        $request = $this->db->get('tbltrader_broker_assigned')->row();
        if(!$request){
            $this->db->select('tbltrader_broker_assigned.*');
            $this->db->where('tbltrader_broker_assigned.send_from',$To);
            $this->db->where('tbltrader_broker_assigned.send_to',$From);
            $this->db->where_in('tbltrader_broker_assigned.status',$status);
            $request = $this->db->get('tbltrader_broker_assigned')->row();
        }
        return $request;
    }
	
	public function GetBrokerInitiateRequest()
	{
		$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date' => $this->input->post('to_date'),
			'status' => $this->input->post('status'),
        );
		$result = $this->BrokerInitiate_model->GetBrokerInitiateRequest($data);
		/*echo json_encode($result);
		die;*/
		$html = '';
		$SrNo = 1;
		foreach($result as $key=>$value){
		    $html .= '<tr >';
		    $html .= '<td>'.$SrNo.'</td>';
		    $html .= '<td>'._d($value["TransDate"]).'</td>';
			$html .= '<td>'.$value["send_from"].'</td>';
			$html .= '<td>'.$value["SendFromName"].'</td>';
			$html .= '<td>'.$value["send_to"].'</td>';
			$html .= '<td>'.$value["SendToName"].'</td>';
			if($value["status"] == "NA"){
			    $html .='<td>Requst Pending</td>';
			}elseif($value["status"] == "Y"){
			    $html .='<td>Requst Accepted</td>';
			}elseif($value["status"] == "N"){
			    $html .='<td>Requst Rejected</td>';
			}
			if($value["status"] == "NA"){
			    $html.= '<td style="text-align:left;width:8%;">
                <button title="Accept" onclick=acceptRequest("'.$value["id"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-success"><i class="fa fa-check"></i></button>
                <button title="Reject" onclick=rejectRequest("'.$value["id"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>
                </td>';
			}elseif($value["status"] == "Y"){
			    $html.= '<td style="text-align:left;width:8%;">
                <button title="Reject" onclick=rejectRequest("'.$value["id"].'") style="margin-right:12px;padding:3px 6px;" class="btn btn-danger"><i class="fa fa-times"></i></button>
                </td>';
			}elseif($value["status"] == "N"){
			    $html.= '<td></td>';
			}
			$html .= '</tr>';
			$SrNo++;
		}
		echo $html;
	}
	
	public function acceptRequest()
    {
        $ID = $this->input->post('ID');
        $result = $this->BrokerInitiate_model->acceptRequest($ID);
        echo json_encode($result);
    }
    public function rejectRequest()
    {
        $ID = $this->input->post('ID');
        $result = $this->BrokerInitiate_model->rejectRequest($ID);
        echo json_encode($result);
    }
}