<?php
	
defined('BASEPATH') or exit('No direct script access allowed');
	
class BrokerInitiate_model extends App_Model
{
		
	public function __construct()
	{
		parent::__construct();
	}
		
	public function GetAllRequest()
	{
	    $this->db->select('tbltrader_initiate_as_broker.*');
	    $this->db->where('tbltrader_initiate_as_broker.status','NA');
		return $this->db->get('tbltrader_initiate_as_broker')->result_array();
	}
	
	public function GetBrokerInitiateRequest($data)
	{
	    $from_date = to_sql_date($data['from_date']);
	    $to_date = to_sql_date($data['to_date']);
	    $status = $data['status'];
	    $this->db->select('tbltrader_broker_assigned.*,SendFrom.company AS SendFromName,SendTo.company AS SendToName');
	    if($status != ""){
	        $this->db->where('tbltrader_broker_assigned.status',$status);
	    }
	    if(($data['from_date'] != '') || ($data['to_date'] != '')){
			$this->db->where('tbltrader_broker_assigned.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
		}
		$this->db->join('tblclients AS SendFrom','SendFrom.AccountID = tbltrader_broker_assigned.send_from');
		$this->db->join('tblclients AS SendTo','SendTo.AccountID = tbltrader_broker_assigned.send_to');
		return $this->db->get('tbltrader_broker_assigned')->result_array();
	}
	
	public function GetAccountDetails($data)
	{
	    $AccountID = $data['AccountID'];
	    $this->db->select('tblclients.*');
	    $this->db->where('tblclients.AccountID',$AccountID);
		return $this->db->get('tblclients')->row();
	}
	
	public function acceptRequest($ID)
	{
	    $username = $this->session->userdata('username');
	    $date = date('Y-m-s H:i:s');
        $this->db->where('id',$ID);
        $this->db->set('status','Y');
        $this->db->set('Lupdate',$date);
        $this->db->set('UserID2',$username);
        if($this->db->update('tbltrader_broker_assigned')){
            $GetRequestDetails = $this->GetReq_details($ID);
            
            // Send Notification to Send From
            
            $title = "Request Accepted";
            $screen = "4";
            //$body = "Request Accepted by ".$GetRequestDetails->SendToName." From Kisan Kirti";
            $body = "Trader/Broker request initiate by Kisan Kirti";
            $booking_id = $GetRequestDetails->send_from;
            $to = $GetRequestDetails->SendFromfcm_token;
            $this->send_notification($title,$screen,$body,$booking_id,$to);
            
            // Send Notification to Send To
            //$body = "Request Accepted for ".$GetRequestDetails->SendFromName .' from Kisan Kirti';
            $booking_id = $GetRequestDetails->send_to;
            $to = $GetRequestDetails->SendTofcm_token;
            $this->send_notification($title,$screen,$body,$booking_id,$to);
            return true;
        }
        return false;
	}
	
	public function rejectRequest($ID)
	{
	    $username = $this->session->userdata('username');
	    $date = date('Y-m-s H:i:s');
        $this->db->where('id',$ID);
        $this->db->set('status','N');
        $this->db->set('Lupdate',$date);
        $this->db->set('UserID2',$username);
        if($this->db->update('tbltrader_broker_assigned')){
            $GetRequestDetails = $this->GetReq_details($ID);
            
            // Send Notification to Send From
            
            $title = "Request Rejected";
            $screen = "4";
            $body = "Trader/Broker request rejected by Kisan Kirti";
            //$body = "Request Rejected by ".$GetRequestDetails->SendToName." From Kisan Kirti";
            $booking_id = $GetRequestDetails->send_from;
            $to = $GetRequestDetails->SendFromfcm_token;
            $this->send_notification($title,$screen,$body,$booking_id,$to);
            
            // Send Notification to Send To
            //$body = "Request Rejected for ".$GetRequestDetails->SendFromName .' from Kisan Kirti';
            $booking_id = $GetRequestDetails->send_to;
            $to = $GetRequestDetails->SendTofcm_token;
            $this->send_notification($title,$screen,$body,$booking_id,$to);
            return true;
        }
        return false;
	}
	
	function send_notification($title,$screen,$body,$booking_id,$to)
    {
        $data_arrary = array(
            "title"=>$title,
            "screen"=>$screen,
            "body"=>$body,
            "booking_id"=>$booking_id
        );
        $post_data = array(
            "priority"=>"HIGH",
            "data"=>$data_arrary,
            "to"=>$to
        );
        $finel_data = json_encode($post_data);
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $finel_data,
            CURLOPT_HTTPHEADER => array(
                    "authorization: key=AAAAy7QqWaM:APA91bFtzRBc-XbKW6CVNBYP20vVnfnNghf6tWrUN8YxJQJ3YXl8B0s8P5-aDC_O-B46PZ5srQVnHx8A0HgqQF0ZIq29kTJKrk9KKvhREuB5oHrmfc0nPsUXf58qPVkHxMUDVU5Vjb4K",
                    "content-type: application/json"
                ),
            )
        );
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
       // return $response;
    }
	
	public function GetReq_details($ID)
	{
	    $this->db->select('tbltrader_broker_assigned.*,SendFrom.company AS SendFromName,SendFrom.fcm_token AS SendFromfcm_token,SendFrom.CustomerType AS SendFromCustomerType,SendTo.company AS SendToName,SendTo.CustomerType AS SendToCustomerType,SendTo.fcm_token AS SendTofcm_token');
	    $this->db->join('tblclients AS SendFrom','SendFrom.AccountID = tbltrader_broker_assigned.send_from');
		$this->db->join('tblclients AS SendTo','SendTo.AccountID = tbltrader_broker_assigned.send_to');
	    $this->db->where('tbltrader_broker_assigned.id',$ID);
		return $this->db->get('tbltrader_broker_assigned')->row();
	}
	
	/*public function acceptRequest($ID)
	{
	    $username = $this->session->userdata('username');
        $this->db->where('id',$ID);
        $this->db->set('status','Y');
        if($this->db->update('tbltrader_initiate_as_broker')){
            $GetRequestDetails = $this->GetReq_details($ID);
            $inser_array = array(
                "TraderID"=>$GetRequestDetails->TraderID,
                "TransDate"=>date('Y-m-d H:i:s'),
                "BrokerID"=>"Self",
                "UserID"=>$username
            );
            $this->db->insert('tbltrader_wise_broker',$inser_array);
            $ids = array($GetRequestDetails->TraderID);
            $body = "Broker Initiative Requst accepted from Kirti";
                $fcm = array(
                    "title"=>"Requst Accepted",
                    "body"=>$body,
                    "booking_id"=>7,
                    "screen"=>1
                );
                $FCM_data = array(
                    "data"=>$fcm 
                );
                $data_array =  array(
                    "interests" => $ids,
                    "fcm"=>$FCM_data
                );
                $data = json_encode($data_array);
                
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://51516d8b-a4ac-4796-a51d-0837896c165e.pushnotifications.pusher.com/publish_api/v1/instances/51516d8b-a4ac-4796-a51d-0837896c165e/publishes",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $data,
                    CURLOPT_HTTPHEADER => array(
                            "authorization: Bearer 67A0A5EE5AEB49DF8A406E8B81A08E258CDCCBA15772E36FE695178F6449DD2F",
                            "content-type: application/json"
                        ),
                    )
                );
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
            return true;
        }else{
            return false;
        }
    }
    public function rejectRequest($ID)
	{
        $this->db->where('id',$ID);
        $this->db->set('status','N');
        if($this->db->update('tbltrader_initiate_as_broker')){
            return true;
        }else{
            return false;
        }
    }
    
    public function GetReq_details($ID)
	{
	    $this->db->select('tbltrader_initiate_as_broker.*');
	    $this->db->where('tbltrader_initiate_as_broker.id',$ID);
		return $this->db->get('tbltrader_initiate_as_broker')->row();
	}*/
}