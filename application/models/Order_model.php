<?php





defined('BASEPATH') or exit('No direct script access allowed');





class Order_model extends App_Model


{


    const STATUS_UNPAID = 1;





    const STATUS_PAID = 2;





    const STATUS_PARTIALLY = 3;





    const STATUS_OVERDUE = 4;





    const STATUS_CANCELLED = 5;





    const STATUS_DRAFT = 6;





    private $statuses = [


        self::STATUS_UNPAID,


        self::STATUS_PAID,


        self::STATUS_PARTIALLY,


        self::STATUS_OVERDUE,


        self::STATUS_CANCELLED,


        self::STATUS_DRAFT,


    ];





    private $shipping_fields = [


        'shipping_street',


        'shipping_city',


        'shipping_city',


        'shipping_state',


        'shipping_zip',


        'shipping_country',


    ];





    public function __construct()


    {


        parent::__construct();


    }


//================= New Code Start =============================================





    // Get Kirti Sale Request


    public function GetSaleRequest($data)


    {


        $from_date = to_sql_date($data["from_date"]);


        $to_date = to_sql_date($data["to_date"]);


        


        $IsApprove = $data["IsApprove"];


        $fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        $UserID = $this->session->userdata('username');


        //return $UserID;


        


        $kirti_status = array('NA','Y');


        //return $centerIDs;


        $this->db->select('tbllead_master.*,tblclients.company,tblclients.AccountID,tblclients.CustomerType,tblcontacts.firstname,tblcontacts.lastname,


        tblCenterMaster.CenterName,tblitems.ItemName,tblitems.ItemID,


        BClients.company AS BName,BContacts.firstname AS Bfirstname,BContacts.lastname AS Blastname,tblPlantMaster.PlantName');


		$this->db->from(db_prefix() . 'lead_master');


		$this->db->where(db_prefix() . 'lead_master.PlantID', $selected_company);


		$this->db->where(db_prefix() . 'lead_master.FY', $fy);


		$this->db->where('tbllead_master.TType', 'S');


		$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'clients.PlantID = tbllead_master.PlantID ');


		$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'contacts.PlantID = tbllead_master.PlantID ');


		$this->db->join(db_prefix() . 'clients AS BClients', 'BClients.AccountID = tbllead_master.BrokerID AND BClients.PlantID = tbllead_master.PlantID ');


		$this->db->join(db_prefix() . 'contacts AS BContacts', 'BContacts.AccountID = tbllead_master.BrokerID AND BContacts.PlantID = tbllead_master.PlantID ');


		$this->db->join(db_prefix() . 'CenterMaster', '' . db_prefix() . 'CenterMaster.CenterID = tbllead_master.CenterID');


		$this->db->join(db_prefix() . 'PlantMaster', '' . db_prefix() . 'PlantMaster.PlantID = tbllead_master.PartyID');


		$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = tbllead_master.ItemID');


		if($data["from_date"] !== "" && $data["to_date"] !== ""){


		    $this->db->where( db_prefix() . 'lead_master.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');


		}


		


		if(!is_admin()){


		    $this->db->join(db_prefix() . 'staff_wise_center', '' . db_prefix() . 'staff_wise_center.CenterID = tbllead_master.CenterID AND tblstaff_wise_center.AccountID = "'.$UserID.'"');


		    $this->db->join(db_prefix() . 'staff_wise_items', '' . db_prefix() . 'staff_wise_items.ItemID = tbllead_master.ItemID AND tblstaff_wise_items.AccountID = "'.$UserID.'"');


		}


		


		if($data["IsApprove"] && $data["IsApprove"] !== ""){


		    $this->db->where(db_prefix() . 'lead_master.IsApprove ',$data["IsApprove"]);


		}else{


		    //$this->db->where_in(db_prefix() . 'lead_master.IsApprove ',$kirti_status);


		}


		//$this->db->where(db_prefix() . 'lead_master.ClientApprove ','Y');


		//$this->db->where(db_prefix() . 'lead_master.BrokerApprove ','Y');


		


		$this->db->order_by( db_prefix() .'lead_master.id','ASC');


	    return $this->db->get()->result_array();


		     


    }


    public function GetPurchaseRequest($data)


    {


        $from_date = to_sql_date($data["from_date"]);


        $to_date = to_sql_date($data["to_date"]);


        


        $IsApprove = $data["IsApprove"];


        $fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        $UserID = $this->session->userdata('username');


        //return $UserID;


        


        $kirti_status = array('NA','Y');


        $this->db->select('tbllead_master.*,tblclients.company,tblclients.AccountID,tblclients.CustomerType,tblcontacts.firstname,tblcontacts.lastname,


            tblCenterMaster.CenterName,tblitems.ItemName,tblitems.ItemID,tblpcsoft_gic_number_referance.GIC_Reference,tblpcsoft_gic_number_referance.pcsoft_doc_ref,


            BClients.company AS BName,BContacts.firstname AS Bfirstname,BContacts.lastname AS Blastname,tblPlantMaster.PlantName');


			$this->db->from(db_prefix() . 'lead_master');


			$this->db->where(db_prefix() . 'lead_master.PlantID', $selected_company);


			$this->db->where(db_prefix() . 'lead_master.FY', $fy);


			$this->db->where('tbllead_master.TType', 'P');


			$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'clients.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'contacts.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'clients AS BClients', 'BClients.AccountID = tbllead_master.BrokerID AND BClients.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'contacts AS BContacts', 'BContacts.AccountID = tbllead_master.BrokerID AND BContacts.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'CenterMaster', '' . db_prefix() . 'CenterMaster.CenterID = tbllead_master.CenterID');


			$this->db->join(db_prefix() . 'PlantMaster', '' . db_prefix() . 'PlantMaster.PlantID = tbllead_master.PartyID');


			$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = tbllead_master.ItemID');


			$this->db->join(db_prefix() . 'pcsoft_gic_number_referance', '' . db_prefix() . 'pcsoft_gic_number_referance.GIC_Reference = tbllead_master.BookingID',"LEFT");


			if($data["from_date"] !== "" && $data["to_date"] !== ""){


			    $this->db->where( db_prefix() . 'lead_master.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');


			}


			


			if(!is_admin()){


			    $this->db->join(db_prefix() . 'staff_wise_center', '' . db_prefix() . 'staff_wise_center.CenterID = tbllead_master.CenterID AND tblstaff_wise_center.AccountID = "'.$UserID.'"');


			    $this->db->join(db_prefix() . 'staff_wise_items', '' . db_prefix() . 'staff_wise_items.ItemID = tbllead_master.ItemID AND tblstaff_wise_items.AccountID = "'.$UserID.'"');


			}


			


			if($data["IsApprove"] && $data["IsApprove"] !== ""){


			    $this->db->where(db_prefix() . 'lead_master.IsApprove ',$data["IsApprove"]);


			}else{


			    //$this->db->where_in(db_prefix() . 'lead_master.IsApprove ',$kirti_status);


			}


			//$this->db->where(db_prefix() . 'lead_master.ClientApprove ','Y');


			//$this->db->where(db_prefix() . 'lead_master.BrokerApprove ','Y');


			


			$this->db->order_by( db_prefix() .'lead_master.id','ASC');


		    return $this->db->get()->result_array();


		     


    }


    


    public function GetPurchaseRequest_by_show_button($data)


    {


        $from_date = to_sql_date($data["from_date"]);


        $to_date = to_sql_date($data["to_date"]);


        $account_type = $data["account_type"];


        $center = $data["center"];


        $IsApprove = $data["IsApprove"];


        $fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        $UserID = $this->session->userdata('username');


        //return $UserID;


        $GetAllCenter = $this->GetAllcenter_staff_wise($UserID);


        $GetAllItems = $this->GetAllItems_staff_wise($UserID);


        $centerIDs = array();


        foreach($GetAllCenter as $val){


            array_push($centerIDs,$val["CenterID"]);


        }


        $ItemIDs = array();


        foreach($GetAllItems as $val){


            array_push($ItemIDs,$val["ItemID"]);


        }


        $kirti_status = array('NA','Y');


        //return $centerIDs;


        $this->db->select('tbllead_master.*,tblclients.company,tblclients.AccountID,tblclients.CustomerType,tblcontacts.firstname,tblcontacts.lastname,tblCenterMaster.CenterName,tblitems.ItemName,tblitems.ItemID');


			$this->db->from(db_prefix() . 'lead_master');


			$this->db->where(db_prefix() . 'lead_master.PlantID', $selected_company);


			$this->db->where(db_prefix() . 'lead_master.FY', $fy);


			$this->db->where('tbllead_master.TType', 'P');


			$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'clients.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'contacts.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'CenterMaster', '' . db_prefix() . 'CenterMaster.CenterID = tbllead_master.CenterID');


			$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = tbllead_master.ItemID');


			if($data["from_date"] !== "" && $data["to_date"] !== ""){


			    $this->db->where( db_prefix() . 'lead_master.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');


			}


			if($data["POID"] && $data["POID"] !== ""){


			    $this->db->where(db_prefix() . 'lead_master.id ',$data["POID"]);


			}


			if($data["center"] && $data["center"] !== ""){


			    $this->db->where(db_prefix() . 'lead_master.CenterID ',$data["center"]);


			}else{


			    if(!is_admin()){


			        $this->db->where_in(db_prefix() . 'lead_master.CenterID ',$centerIDs);


			    }


			}


			if(!is_admin()){


			    $this->db->where_in(db_prefix() . 'lead_master.ItemID ',$ItemIDs);


			}


			


			if($data["IsApprove"] && $data["IsApprove"] !== ""){


			    $this->db->where(db_prefix() . 'lead_master.IsApprove ',$data["IsApprove"]);


			}else{


			    $this->db->where_in(db_prefix() . 'lead_master.IsApprove ',$kirti_status);


			}


			$this->db->where(db_prefix() . 'lead_master.ClientApprove ','Y');


			$this->db->where(db_prefix() . 'lead_master.BrokerApprove ','Y');


			


			if($account_type !== ""){


			    $this->db->where(db_prefix() . 'clients.CustomerType ',$account_type);


			}


			$this->db->order_by( db_prefix() .'lead_master.id','ASC');


		    return $this->db->get()->result_array();


    }


    


    public function reject_by_delay_broker_approval()


    {


        $Day_time = date('Y-m-d')." 00:00:00";


        $date_time = date('Y-m-d H:i:s');


        $new_date = date("Y-m-d H:i:s", strtotime($date_time) - 180);


        


        // Broker delay rejection


        $this->db->select('tbllead_master.BookingID,tbllead_master.AccountID,tbllead_master.BrokerID,


        tblclients.company,tblclients.fcm_token,tblcontacts.firstname,tblcontacts.lastname,


        BClients.fcm_token AS Bfcm_token');


        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'clients.PlantID = tbllead_master.PlantID ');


		$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'contacts.PlantID = tbllead_master.PlantID ');


		$this->db->join(db_prefix() . 'clients AS BClients', 'BClients.AccountID = tbllead_master.BrokerID AND BClients.PlantID = tbllead_master.PlantID ');


		//$this->db->where('IsApprove','NA');


        //$this->db->where('ClientApprove','Y');


        $this->db->where('BrokerApprove','NA');


        $this->db->where(db_prefix() . 'lead_master.TransDate BETWEEN "'.$Day_time.'" AND "'.$new_date.'"');


        $rejectAll =  $this->db->get('tbllead_master')->result_array();


        


        //$this->db->where('IsApprove','NA');


        //$this->db->where('ClientApprove','Y');


        $this->db->where('BrokerApprove','NA');


        $this->db->where(db_prefix() . 'lead_master.TransDate BETWEEN "'.$Day_time.'" AND "'.$new_date.'"');


        $this->db->set('BrokerApprove','N');


        $this->db->set('ApproveTime',$date_time);


        $this->db->set('BrokerApproveTime',$date_time);


        $this->db->set('LastActionName','Rejected , Broker Approval Delay..');


        if($this->db->update('tbllead_master')){


            


            $screen = "1";


            foreach($rejectAll as $key=>$val){


                if($val["BrokerID"] == $val["AccountID"]){


                    $title = "Trade Rejected by your approval delay";


                    $body = "Your BookingID : ".$val["BookingID"].' rejected by your delay in Approval';


                    $booking_id = $val["BookingID"];


                    $to = $val["fcm_token"];


                    $this->send_notification($title,$screen,$body,$booking_id,$to);


                    


                }else{


                    if($val["company"]){


                        $partyName = $val["company"];


                    }else{


                        $partyName = $val["firstname"].' '.$val["lastname"];


                    }


                    // Send notification to broker 


                    $title = "Trade Rejected by your approval delay";


                    $body = "Your BookingID : ".$val["BookingID"].' for '.$partyName.' rejected by your delay in Approval ';


                    $booking_id = $val["BookingID"];


                    $to = $val["Bfcm_token"];


                    $this->send_notification($title,$screen,$body,$booking_id,$to);


                    


                    // Send Notification to trader


                    $title = "Trade Rejected by broker approval delay";


                    $body = "Your BookingID : ".$val["BookingID"].' rejected by delay in Broker Approval';


                    $booking_id = $val["BookingID"];


                    $to = $val["fcm_token"];


                    $this->send_notification($title,$screen,$body,$booking_id,$to);


                }


            }


            return true;


        }


    }


    


    public function reject_by_delay_kirti_approval()


    {


        $Day_time = date('Y-m-d')." 00:00:00";


        $date_time = date('Y-m-d H:i:s');


        $new_date = date("Y-m-d H:i:s", strtotime($date_time) - 180);


        


        // Reject by kirti approval delay


        $this->db->select('tbllead_master.BookingID,tbllead_master.AccountID,tbllead_master.BrokerID,


        tblclients.company,tblcontacts.firstname,tblcontacts.lastname,tblclients.fcm_token,BClients.fcm_token AS Bfcm_token');


        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'clients.PlantID = tbllead_master.PlantID ');


		$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'contacts.PlantID = tbllead_master.PlantID ');


		$this->db->join(db_prefix() . 'clients AS BClients', 'BClients.AccountID = tbllead_master.BrokerID AND BClients.PlantID = tbllead_master.PlantID ');


			


        $this->db->where('IsApprove','NA');


        $this->db->where('ClientApprove','Y');


        $this->db->where('BrokerApprove','Y');


        $this->db->where(db_prefix() . 'lead_master.BrokerApproveTime BETWEEN "'.$Day_time.'" AND "'.$new_date.'"');


        $rejectAll =  $this->db->get('tbllead_master')->result_array();


        


        $this->db->where('IsApprove','NA');


        $this->db->where('ClientApprove','Y');


        $this->db->where('BrokerApprove','Y');


        $this->db->where(db_prefix() . 'lead_master.BrokerApproveTime BETWEEN "'.$Day_time.'" AND "'.$new_date.'"');


        $this->db->set('IsApprove','N');


        $this->db->set('ApproveTime',$date_time);


        $this->db->set('LastActionName','Timed Out');


        if($this->db->update('tbllead_master')){


            foreach($rejectAll as $key=>$val){


                


                // Notification for Trader/ Farmer


                $title = "Trade has been Rejected by admin";


                $screen = "1";


                $body = "Your BookingID : ".$val["BookingID"].' has been rejected by admin';


                $booking_id = $val["BookingID"];


                $to = $val["fcm_token"];


                $this->send_notification($title,$screen,$body,$booking_id,$to);


            


            // Notification For Broker 


                if($val["BrokerID"] != NULL && $val["BrokerID"] !="" && $val["BrokerID"] != $val["AccountID"]){


                    if($val["company"] == "" || $val["company"] == null){


                        $AccountName = $val["firstname"].' '.$val["lastname"];


                    }else{


                        $AccountName = $val["company"];


                    }


                    


                    $title = "Trade has been Rejected by admin";


                    $screen = "1";


                    $body = "Trade has been rejected by admin against ".$val["BookingID"] ." for ".$AccountName;


                    $booking_id = $val["BookingID"];


                    $to = $val["Bfcm_token"];


                    $this->send_notification($title,$screen,$body,$booking_id,$to);


                }


            }


            return true;


        }


    }


    public function GetAllcenter_staff_wise($AccountID)


    {


        $this->db->select('tblstaff_wise_center.*');


		$this->db->from(db_prefix() . 'staff_wise_center');


		$this->db->where(db_prefix() . 'staff_wise_center.AccountID ',$AccountID);


		$this->db->order_by( db_prefix() .'staff_wise_center.CenterID','ASC');


		return $this->db->get()->result_array();


    }


    public function GetAllItems_staff_wise($AccountID)


    {


        $this->db->select('tblstaff_wise_items.*');


		$this->db->from(db_prefix() . 'staff_wise_items');


		$this->db->where(db_prefix() . 'staff_wise_items.AccountID ',$AccountID);


		$this->db->order_by( db_prefix() .'staff_wise_items.ItemID','ASC');


		return $this->db->get()->result_array();


    }


    


    public function GetPurchaseRequestCust($data)


    {


        $AccountID = $data["AccountID"];


        $from_date = to_sql_date($data["from_date"]);


        $to_date = to_sql_date($data["to_date"]);


        $center = $data["center"];


        $IsApprove = $data["IsApprove"];


        //$fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        


        $this->db->select('tbllead_master.*,tblclients.company,tblclients.AccountID,tblclients.CustomerType,tblcontacts.firstname,tblcontacts.lastname,tblCenterMaster.CenterName,tblitems.ItemName,tblitems.ItemID');


			$this->db->from(db_prefix() . 'lead_master');


			$this->db->where(db_prefix() . 'lead_master.PlantID', $selected_company);


			//$this->db->where(db_prefix() . 'lead_master.FY', $fy);


			$this->db->where('tbllead_master.TType', 'P');


			$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'clients.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'contacts.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'CenterMaster', '' . db_prefix() . 'CenterMaster.CenterID = tbllead_master.CenterID');


			$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = tbllead_master.ItemID');


			if($data["from_date"] !== "" && $data["to_date"] !== ""){


			    $this->db->where( db_prefix() . 'lead_master.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');


			}


			if($data["center"] && $data["center"] !== ""){


			    $this->db->where(db_prefix() . 'lead_master.CenterID ',$data["center"]);


			}


			if($data["IsApprove"] && $data["IsApprove"] !== ""){


			    if(($data["IsApprove"] == 'NA') && ($data['modified'] == 0)){


			        $this->db->where(db_prefix() . 'lead_master.IsApprove ','NA');


			        $this->db->where(db_prefix() . 'lead_master.modified ',0);


			    }


			    elseif($data["IsApprove"] == 'Y'){


			        $this->db->where(db_prefix() . 'lead_master.IsApprove ','Y');


			    }


			    elseif($data["IsApprove"] == 'N'){


			        $this->db->where(db_prefix() . 'lead_master.IsApprove ','N');


			    }


			    elseif($data["IsApprove"] == 1){


			        $this->db->where(db_prefix() . 'lead_master.modified ',1);


			        $this->db->where(db_prefix() . 'lead_master.modified_by !=',null);


			        $this->db->where(db_prefix() . 'lead_master.modified_date !=',null);


			    }


			}


			if($data["AccountID"] && $data["AccountID"] !== ""){


			    $this->db->where(db_prefix() . 'lead_master.AccountID ',$data["AccountID"]);


			}


			$this->db->order_by( db_prefix() .'lead_master.id','ASC');


			return $this->db->get()->result_array();


    }


    


    public function GetPurchaseRequestWithdrawalCust($data)


    {


        $AccountID = $data["AccountID"];


        $from_date = to_sql_date($data["from_date"]);


        $to_date = to_sql_date($data["to_date"]);


        $center = $data["center"];


        $IsApprove = $data["IsApprove"];


        //$fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        


        $this->db->select('tbllead_master.*,tblclients.company,tblclients.AccountID,tblclients.CustomerType,tblcontacts.firstname,tblcontacts.lastname,tblCenterMaster.CenterName,tblitems.ItemName,tblitems.ItemID');


			$this->db->from(db_prefix() . 'lead_master');


			$this->db->where(db_prefix() . 'lead_master.PlantID', $selected_company);


			//$this->db->where(db_prefix() . 'lead_master.FY', $fy);


			$this->db->where('tbllead_master.TType', 'W');


			$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'clients.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'contacts.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'CenterMaster', '' . db_prefix() . 'CenterMaster.CenterID = tbllead_master.CenterID');


			$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = tbllead_master.ItemID');


			if($data["from_date"] !== "" && $data["to_date"] !== ""){


			    $this->db->where( db_prefix() . 'lead_master.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');


			}


			if($data["center"] && $data["center"] !== ""){


			    $this->db->where(db_prefix() . 'lead_master.CenterID ',$data["center"]);


			}


			if($data["IsApprove"] && $data["IsApprove"] !== ""){


			    if(($data["IsApprove"] == 'NA') && ($data['modified'] == 0)){


			        $this->db->where(db_prefix() . 'lead_master.IsApprove ','NA');


			        $this->db->where(db_prefix() . 'lead_master.modified ',0);


			    }


			    elseif($data["IsApprove"] == 'Y'){


			        $this->db->where(db_prefix() . 'lead_master.IsApprove ','Y');


			    }


			    elseif($data["IsApprove"] == 'N'){


			        $this->db->where(db_prefix() . 'lead_master.IsApprove ','N');


			    }


			    elseif($data["IsApprove"] == 1){


			        $this->db->where(db_prefix() . 'lead_master.modified ',1);


			        $this->db->where(db_prefix() . 'lead_master.modified_by !=',null);


			        $this->db->where(db_prefix() . 'lead_master.modified_date !=',null);


			    }


			}


			if($data["AccountID"] && $data["AccountID"] !== ""){


			    $this->db->where(db_prefix() . 'lead_master.AccountID ',$data["AccountID"]);


			}


			$this->db->order_by( db_prefix() .'lead_master.id','ASC');


			return $this->db->get()->result_array();


    }


    


    public function getModalDataDb($data)


    {


        $this->db->select('tbllead_master.*,tblclients.CustomerType,tblclients.company,tblcontacts.firstname,tblcontacts.lastname,tblCenterMaster.CenterName,tblitems.ItemName,tblDepositTradeDetails.MinQty,tblDepositTradeDetails.DepositPeriod,


                            tblDepositTradeDetails.RateType,tblDepositTradeDetails.IsFumigation,tblDepositTradeDetails.RateIncFumigation,tblDepositTradeDetails.FumigationAmt,tblDepositTradeDetails.CreditDays');


        $this->db->where('tbllead_master.BookingID',$data['BookingID']);


        $this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID');


        $this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID');


        $this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');


        $this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID');


        $this->db->join('tblDepositTradeDetails','tblDepositTradeDetails.BookingID = tbllead_master.BookingID',"LEFT");


        return $this->db->get('tbllead_master')->row();


    }


    


    


    


    public function AcceptTradeDb($data,$BookingID)


    {


        $this->db->where('BookingID',$BookingID);


        return $this->db->update('tbllead_master',$data);


    }


    


    public function RejectTradeDb($data,$TransDate,$ItemID,$CenterID,$TType)


    {


        //$this->db->where('BookingID',$data['BookingID']);


        $UserID = $this->session->userdata('username');


        $lastdate = substr($TransDate,0,19);


        $date = date('Y-m-d H:i:s');


        


        $this->db->select('tbllead_master.BookingID,tbllead_master.AccountID,tbllead_master.BrokerID,tblclients.company,tblclients.fcm_token,tblcontacts.firstname,tblcontacts.lastname,


        BClients.company AS BName,BContacts.firstname AS Bfirstname,BContacts.lastname AS Blastname,BClients.fcm_token AS Bfcm_token');


        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'clients.PlantID = tbllead_master.PlantID ');


		$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'contacts.PlantID = tbllead_master.PlantID ');


		$this->db->join(db_prefix() . 'clients AS BClients', 'BClients.AccountID = tbllead_master.BrokerID AND BClients.PlantID = tbllead_master.PlantID ');


		$this->db->join(db_prefix() . 'contacts AS BContacts', 'BContacts.AccountID = tbllead_master.BrokerID AND BContacts.PlantID = tbllead_master.PlantID ');


        $this->db->where('ItemID',$ItemID);


        $this->db->where('CenterID',$CenterID);


        $this->db->where('IsApprove','NA');


        $this->db->where('TType',$TType);


        $this->db->where('BookingID',$data['BookingID']);


        $this->db->where( db_prefix() . 'lead_master.TransDate BETWEEN "'.$lastdate.'" AND "'.$date.'"');


        $rejectAll =  $this->db->get('tbllead_master')->result_array();


        


        


        $this->db->where('ItemID',$ItemID);


        $this->db->where('CenterID',$CenterID);


        $this->db->where('IsApprove','NA');


        $this->db->where('TType',$TType);


        $this->db->where('BookingID',$data['BookingID']);


        $this->db->where( db_prefix() . 'lead_master.TransDate BETWEEN "'.$lastdate.'" AND "'.$date.'"');


        $this->db->set('ApproveTime',$date);


        $this->db->set('ApproveUserID',$UserID);


        $this->db->set('IsApprove','N');


        if($this->db->update('tbllead_master')){


            


            $title = "Trade Rejected by Kisan Kirti";


            $screen = "1";


            


            foreach($rejectAll as $key=>$val){


                


                $body = "Your BookingID : ".$val["BookingID"].' rejected by Kisan Kirti';


                $booking_id = $val["BookingID"];


                $to = $val["fcm_token"];


                $this->send_notification($title,$screen,$body,$booking_id,$to);


                


                if($val["AccountID"] != $val["BrokerID"]){


                    if($val["company"] == "" || $val["company"] == null){


                        $AccountName = $val["firstname"].' '.$val["lastname"];


                    }else{


                        $AccountName = $val["company"];


                    }


                    $body = "BookingID rejected by Kisan kirti against ".$val["BookingID"] ." for ".$AccountName;


                    $to = $val["Bfcm_token"];


                    $this->send_notification($title,$screen,$body,$booking_id,$to);


                }


            }


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


    


    public function ModifyTradeDb($data,$BookingID)


    {


        $this->db->where('BookingID',$BookingID);


        return $this->db->update('tbllead_master',$data);


    }


    


    public function ModifyDepositTradeDb($deposittradedata,$BookingID)


    {


         $this->db->where('BookingID',$BookingID);


         return $this->db->update('tblDepositTradeDetails',$deposittradedata);    


    }


    


    public function getSingleTradeDB($BookingID)


    {


        $this->db->select('tblGateMaster.*,tblclients.CustomerType,tblCustomerType.Name');


        $this->db->join('tblclients','tblclients.AccountID = tblGateMaster.AccountID');


        $this->db->join('tblCustomerType','tblCustomerType.id = tblclients.CustomerType');


        $this->db->where('tblGateMaster.BookingID', $BookingID);


        return $this->db->get('tblGateMaster')->row();


    }


    public function GetOldTrade($Transdate,$ItemID,$CenterID,$TType)


    {


        $lastdate = substr($Transdate,0,19);


        $new_date = date("Y-m-d H:i:s", strtotime($lastdate) - 1);


        $date = date('Y-m-d').' 00:00:00';


        $this->db->select('tbllead_master.*');


        $this->db->where('tbllead_master.ItemID', $ItemID);


        $this->db->where('tbllead_master.CenterID', $CenterID);


        $this->db->where('tbllead_master.IsApprove', 'NA');


        $this->db->where('tbllead_master.TType', $TType);


        $this->db->where('tbllead_master.ClientApprove', "Y");


        $this->db->where('tbllead_master.BrokerApprove', "Y");


        $this->db->where( db_prefix() . 'lead_master.TransDate BETWEEN "'.$date.'" AND "'.$new_date.'"');


        


        return $this->db->get('tbllead_master')->result_array();


    }


    


    public function GetBookingDetails($BookingID)


    {


        $this->db->select('tbllead_master.*,tblCenterMaster.CenterType,tblCenterMaster.PCCenterID,tblclients.company,tblclients.ShortCode,tblclients.fcm_token,tblclients.CustomerType,


        tblcontacts.firstname,tblcontacts.lastname,Bdetails.fcm_token AS Bfcm_token,tblitems.PCItemID');


        $this->db->join('tblitems','tblitems.ItemID = tbllead_master.ItemID');


        $this->db->join('tblCenterMaster','tblCenterMaster.CenterID = tbllead_master.CenterID');


        $this->db->join('tblclients','tblclients.AccountID = tbllead_master.AccountID');


        $this->db->join('tblcontacts','tblcontacts.AccountID = tbllead_master.AccountID');


        $this->db->join('tblclients AS Bdetails','Bdetails.AccountID = tbllead_master.BrokerID');         


        $this->db->where('tbllead_master.BookingID', $BookingID);


        return $this->db->get('tbllead_master')->row();


    }


    


    public function GetOldSaleTrade($Transdate,$ItemID,$CenterID,$TType)


    {


        $lastdate = substr($Transdate,0,19);


        $new_date = date("Y-m-d H:i:s", strtotime($lastdate) - 1);


        $date = date('Y-m-d').' 00:00:00';


        $this->db->select('tbllead_master.*');


        $this->db->where('tbllead_master.ItemID', $ItemID);


        $this->db->where('tbllead_master.CenterID', $CenterID);


        $this->db->where('tbllead_master.IsApprove', 'NA');


        $this->db->where('tbllead_master.TType', $TType);


        //$this->db->where('tbllead_master.ClientApprove', "Y");


        //$this->db->where('tbllead_master.BrokerApprove', "Y");


        $this->db->where( db_prefix() . 'lead_master.TransDate BETWEEN "'.$date.'" AND "'.$new_date.'"');


        return $this->db->get('tbllead_master')->result_array();


    }


    


    public function GetCenterList()


    {


        $this->db->select('tblCenterMaster.*');


		$this->db->from(db_prefix() . 'CenterMaster');


		$this->db->order_by( db_prefix() .'CenterMaster.id','ASC');


		return $this->db->get()->result_array();


    }


    


    public function GetPlantList()


    {


        $this->db->select('tblPlantMaster.*');


		$this->db->from(db_prefix() . 'PlantMaster');


		$this->db->where( db_prefix() .'PlantMaster.PlantID','KASPL');


		$this->db->order_by( db_prefix() .'PlantMaster.id','ASC');


		return $this->db->get()->result_array();


    }


    


    public function GetAllBrokerList()


    {


        $this->db->select('tblclients.AccountID,tblclients.company');


		$this->db->from(db_prefix() . 'clients');


		$this->db->where( db_prefix() .'clients.CustomerType','2');


		$this->db->order_by( db_prefix() .'clients.company','ASC');


		return $this->db->get()->result_array();


    }


    


    public function GetPaymentCycleList()


    {


        $this->db->select('tblPaymentCycle.CycleID,tblPaymentCycle.CycleName');


		$this->db->from(db_prefix() . 'PaymentCycle');


		$this->db->order_by( db_prefix() .'PaymentCycle.CycleID','ASC');


		return $this->db->get()->result_array();


    }


    public function GetTransportList()


    {


        $this->db->select('tblTransportMaster.TransportID,tblTransportMaster.TransportName');


		$this->db->from(db_prefix() . 'TransportMaster');


		$this->db->order_by( db_prefix() .'TransportMaster.TransportName','ASC');


		return $this->db->get()->result_array();


    }


    


    public function GetStateList()


    {


        $this->db->select('tblxx_statelist.short_name,tblxx_statelist.state_name');


		$this->db->from(db_prefix() . 'xx_statelist');


		$this->db->order_by( db_prefix() .'xx_statelist.state_name','ASC');


		return $this->db->get()->result_array();


    }


    


    public function GetWHListByCenterID($CenterID)


    {


        $this->db->select('tblwarehouse.AccountID,tblwarehouse.w_name');


		$this->db->from(db_prefix() . 'warehouse');


		$this->db->where( db_prefix() .'warehouse.center',$CenterID);


		$this->db->order_by( db_prefix() .'warehouse.w_name','ASC');


		return $this->db->get()->result_array();


    }


    


    function getitems($postData)


    {


        $response = array();


        $selected_company = $this->session->userdata('root_company');


        $fy = $this->session->userdata('finacial_year');


        if(isset($postData['search']) ){


            $q = $postData['search'];


            $where_item = '';


            $subgroup = array('9','20','36');


            $where_item .= '('.db_prefix() .'items.ItemName LIKE "%' . $q . '%" ESCAPE \'!\' OR ItemID LIKE "%' . $q . '%" ESCAPE \'!\') ';


            $where_item2 = 'SEELCT TransID';


       


            $this->db->select(db_prefix() .'items.*, '.db_prefix() . 'taxes.taxrate');


            $this->db->from(db_prefix() . 'items');


            $this->db->join(db_prefix() . 'taxes', '' . db_prefix() . 'taxes.id = ' . db_prefix() . 'items.tax');


            $this->db->where($where_item);


            $this->db->where_not_in(db_prefix() . 'items.subgroup_id',$subgroup);


            


            $this->db->where(db_prefix() . 'items.PlantID', $selected_company);


            $records = $this->db->get()->result();


            


            foreach($records as $row ){


                $response[] = array("label"=>$row->ItemName,"value"=>$row->ItemID,"hsn_code"=>$row->hsn_code,"tax"=>$row->taxrate,"unit"=>$row->unit);


            }


        }


        return $response;


    }


    


    function GetItemDetailsByItemID($ItemID)


    {


        $selected_company = $this->session->userdata('root_company');


        $fy = $this->session->userdata('finacial_year');


        $subgroup = array('9','20','36');


            


        $this->db->select(db_prefix() .'items.*, '.db_prefix() . 'taxes.taxrate');


        $this->db->from(db_prefix() . 'items');


        $this->db->join(db_prefix() . 'taxes', '' . db_prefix() . 'taxes.id = ' . db_prefix() . 'items.tax');


        $this->db->where(db_prefix() . 'items.ItemID',$ItemID);


        $this->db->where_not_in(db_prefix() . 'items.subgroup_id',$subgroup);


        


        $this->db->where(db_prefix() . 'items.PlantID', $selected_company);


        $records = $this->db->get()->row();


        return $records;


    }


    


    function GetSaleItemCenterWiseStaffWise($CenterID)


    {


        $response = array();


        $UserID = $this->session->userdata('username');


        $selected_company = $this->session->userdata('root_company');


        $this->db->select('tblCenter_wise_item.*');


        $this->db->join('tblitems','tblitems.ItemID = tblCenter_wise_item.ItemID');


        $this->db->join('tblstaff_wise_items','tblstaff_wise_items.ItemID = tblCenter_wise_item.ItemID');


        $this->db->join(db_prefix() . 'Itemwise_staff_priority', '' . db_prefix() . 'Itemwise_staff_priority.ItemID = ' . db_prefix() . 'Center_wise_item.ItemID AND tblItemwise_staff_priority.staff_id = "'.$UserID.'"','LEFT');


        $this->db->where(db_prefix() . 'Center_wise_item.CenterID', $CenterID);


        $this->db->where(db_prefix() . 'items.PlantID', $selected_company);


        $this->db->where(db_prefix() . 'staff_wise_items.UserID', $UserID);


        $this->db->where(db_prefix() . 'items.isactive', 'Y');


        $this->db->order_by('tblItemwise_staff_priority.priority','ASC');


        $records = $this->db->get('tblCenter_wise_item')->result_array();


        


        $record_string = '';


        foreach($records as $row ){


            $record_string .= $row->ItemID.",";


        }


        return $record_string;


    }


    


    


//================= New Code End ===============================================


    public function get_statuses()


    {


        return $this->statuses;


    }





    public function get_sale_agents()


    {


        return $this->db->query('SELECT DISTINCT(sale_agent) as sale_agent, CONCAT(firstname, \' \', lastname) as full_name FROM ' . db_prefix() . 'invoices JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid=' . db_prefix() . 'invoices.sale_agent WHERE sale_agent != 0')->result_array();


    }





    /**


     * Get invoice by id


     * @param  mixed $id


     * @return array|object


     */


    public function get($id = '', $where = [])


    {


        $this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'order.id as id, ' . db_prefix() . 'currencies.name as currency_name');


        $this->db->from(db_prefix() . 'order');


        $this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'order.currency', 'left');


        $this->db->where($where);


        if (is_numeric($id)) {


            $this->db->where(db_prefix() . 'order' . '.id', $id);


            $invoice = $this->db->get()->row();


            if ($invoice) {


                $invoice->total_left_to_pay = get_invoice_total_left_to_pay($invoice->id, $invoice->total);





                $invoice->items       = get_items_by_type2('order', $id);


                $invoice->attachments = $this->get_attachments($id);





                if ($invoice->project_id != 0) {


                    $this->load->model('projects_model');


                    $invoice->project_data = $this->projects_model->get($invoice->project_id);


                }





                $invoice->visible_attachments_to_customer_found = false;


                foreach ($invoice->attachments as $attachment) {


                    if ($attachment['visible_to_customer'] == 1) {


                        $invoice->visible_attachments_to_customer_found = true;





                        break;


                    }


                }





                $client          = $this->clients_model->get($invoice->clientid);


                $invoice->client = $client;


                if (!$invoice->client) {


                    $invoice->client          = new stdClass();


                    $invoice->client->company = $invoice->deleted_customer_name;


                }





                $this->load->model('payments_model');


                $invoice->payments = $this->payments_model->get_invoice_payments($id);





                $this->load->model('email_schedule_model');


                $invoice->scheduled_email = $this->email_schedule_model->get($id, 'invoice');


            }





            return hooks()->apply_filters('get_invoice', $invoice);


        }





        $this->db->order_by('number,YEAR(date)', 'desc');





        return $this->db->get()->result_array();


    }


    


    public function get2($id = '', $where = [])


    {


        $fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        


        $this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'ordermaster.OrderID as id, ' . db_prefix() . 'currencies.name as currency_name');


        $this->db->from(db_prefix() . 'ordermaster');


        $this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'ordermaster.currency', 'left');


        $this->db->where($where);


        if ($id) {


            $this->db->where(db_prefix() . 'ordermaster.OrderID', $id);


            $this->db->where(db_prefix() . 'ordermaster.FY', $fy);


            $this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);


            $order = $this->db->get()->row();


            if ($order) {


                


                $client          = $this->clients_model->get($order->AccountID);


                $order->client = $client;


                $accbal = $this->get_accbal($order->AccountID,$selected_company,$fy);


                $order->accbal = $accbal;


                $last_billed_on = $this->get_last_bill_on($order->AccountID,$selected_company,$fy);


                $order->last_billed_on = $last_billed_on;


                $last_deposit_on = $this->get_last_deposit_on($order->AccountID,$selected_company,$fy);


                $order->last_deposit_on = $last_deposit_on;


                $item          = $this->get_order_items($order->OrderID,$selected_company,$fy);


                $itemStocks          = $this->GetItemStock($order->OrderID,$selected_company,$fy);


                $order->items = $item;


                $order->itemStocks = $itemStocks;


                if($order->ChallanID !== null){


                    $SaleDetails = $this->SaleDetails($order->SalesID,$selected_company,$fy);


                    $ChallanDetails = $this->ChallanDetails($order->ChallanID,$selected_company,$fy);


                    $order->ChallanDetails = $ChallanDetails;


                    $order->SaleDetails = $SaleDetails;


                }


            }





            return hooks()->apply_filters('get_invoice', $order);


        }





        //$this->db->order_by('YEAR(date)', 'desc');


        $this->db->where(db_prefix() . 'ordermaster.FY', $fy);


        $this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);


        return $this->db->get()->result_array();


    }


    


    public function check_pending_order($customer_id = '')


    {


        $fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        


        $this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'ordermaster.OrderID as id, ' . db_prefix() . 'currencies.name as currency_name');


        $this->db->from(db_prefix() . 'ordermaster');


        $this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'ordermaster.currency', 'left');


        


        $this->db->where(db_prefix() . 'ordermaster.AccountID', $customer_id);


        $this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');


        $this->db->where(db_prefix() . 'ordermaster.ChallanID', null);


        $this->db->where(db_prefix() . 'ordermaster.FY', $fy);


        $this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);


        $order_data = $this->db->get()->result_array();


        if(empty($order_data)){


            return true; 


        }else{


            if($selected_company == "1"){


                $TaxItems = 0;


                $NonTaxItems = 0;


                foreach ($order_data as $key => $value) {


                    if($value['OrderType']=="TaxItems"){


                        $TaxItems = 1;


                    }


                    if($value['OrderType']=="NonTaxItems"){


                        $NonTaxItems = 1;


                    }


                }


                if($TaxItems == "1" && $NonTaxItems == "1"){


                    return false;


                }


                if($TaxItems == "1"){


                    return 'NonTaxItems';


                }


                if($NonTaxItems == "1"){


                    return 'TaxItems';


                }


            }else{


                return false;


            }


        }


    }


    


    public function load_data($data)


     {  


         $dates = to_sql_date($data["dates"]);


         $order_type = $data["order_type"];


         $state = $data["state"];


         $dist_type = $data["dist_type"];


        $fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        


        if($order_type == "all"){


           $sql1 = 'Transdate <= "'.$dates.' 23:59:00" AND OrderStatus IN("C","O") AND ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy. ' AND '; 


        }if($order_type == "O"){


            $sql1 = 'Transdate <= "'.$dates.' 23:59:00" AND OrderStatus IN("O") AND ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy. ' AND '; 


        }


        if($order_type == "C"){


            $sql1 = 'Transdate <= "'.$dates.' 23:59:00" AND OrderStatus IN("C") AND ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy. ' AND '; 


        }


        if (empty($state)) {


            


        }else {


            $sql1 .= db_prefix().'ordermaster.AccountID = (SELECT '.db_prefix().'clients.AccountID FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.state = "'.$state.'" AND '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') AND ';


        }


        


        if (empty($dist_type)) {


            


        }else {


            $sql1 .= db_prefix().'ordermaster.AccountID = (SELECT '.db_prefix().'clients.AccountID FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.DistributorType = '.$dist_type.' AND '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') AND ';


        }


        


        $sql1 .= db_prefix().'ordermaster.PlantID = '.$selected_company.' ORDER BY OrderID ASC';


        


        $sql ='SELECT '.db_prefix().'ordermaster.*,IFNULL(BAL1,0.00) as bal1,IFNULL(BAL2,0.00) as bal2,IFNULL(BAL3,0.00) as bal3,IFNULL(BAL4,0.00) as bal4,IFNULL(BAL5,0.00) as bal5,IFNULL(BAL6,0.00) as bal6,IFNULL(BAL7,0.00) as bal7,IFNULL(BAL8,0.00) as bal8,IFNULL(BAL9,0.00) as bal9,IFNULL(BAL10,0.00) as bal10,IFNULL(BAL11,0.00) as bal11,IFNULL(BAL12,0.00) as bal12,IFNULL(BAL13,0.00) as bal13,


        (SELECT GROUP_CONCAT(company SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as AccountName,


        (SELECT GROUP_CONCAT(StationName SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as StationName,


        (SELECT GROUP_CONCAT(CONCAT ( firstname," ",lastname ) SEPARATOR ",") FROM '.db_prefix().'customer_admins 


            LEFT JOIN tblstaff ON tblstaff.staffid = tblcustomer_admins.staff_id 


            WHERE '.db_prefix().'customer_admins.customer_id = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'customer_admins.company_id = '.$selected_company.') as SOID,


        (SELECT '.db_prefix().'xx_statelist.short_name FROM  '.db_prefix().'xx_statelist


            INNER JOIN '.db_prefix().'clients ON '.db_prefix().'xx_statelist.short_name = '.db_prefix().'clients.state


            WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as StateName,


        (SELECT '.db_prefix().'customers_groups.name FROM  '.db_prefix().'customers_groups


            INNER JOIN '.db_prefix().'clients ON '.db_prefix().'customers_groups.id = '.db_prefix().'clients.DistributorType


            WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as dist_Type 


        FROM '.db_prefix().'ordermaster 


        INNER JOIN '.db_prefix().'accountbalances 


        ON '.db_prefix().'ordermaster.AccountID = '.db_prefix().'accountbalances.AccountID AND '.db_prefix().'ordermaster.PlantID = '.db_prefix().'accountbalances.PlantID AND '.db_prefix().'ordermaster.FY = '.db_prefix().'accountbalances.FY


        WHERE '.$sql1;


        


        


    $result = $this->db->query($sql)->result_array();


        $i = 0;


        $fy_to = $fy + 1;


        $from_date = '20'.$fy.'-04-01';


        $to_date = '20'.$fy_to.'-03-31';


        foreach($result as $value){


            


            // credit crated


                $this->db->select('sum(Amount) as credit_bal,AccountID');


                $this->db->where('tblaccountledger.PlantID', $selected_company);


                $this->db->where('tblaccountledger.FY', $fy);


                $this->db->where('tblaccountledger.TType', 'C');


                $this->db->where('tblaccountledger.AccountID', $value["AccountID"]);


                $this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');


                $this->db->group_by('AccountID');


                $credit_bal = $this->db->get('tblaccountledger')->result_array();


                


            // Debit crated


                $this->db->select('sum(Amount) as debit_bal,AccountID');


                $this->db->where('tblaccountledger.PlantID', $selected_company);


                $this->db->where('tblaccountledger.FY', $fy);


                $this->db->where('tblaccountledger.TType', 'D');


                $this->db->where('tblaccountledger.AccountID', $value["AccountID"]);


                $this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');


                $this->db->group_by('AccountID');


                $debit_bal = $this->db->get('tblaccountledger')->result_array();


             $balance = $debit_bal[0]['debit_bal'] - $credit_bal[0]['credit_bal'];


            $result[$i]['balance'] = $balance;


            


            $i++; 


                 


        }    


        


        return $result;


        


       


     }


     


    public function load_data_items($data)


     {  


         $dates = to_sql_date($data["dates"]);


         $order_type = $data["order_type"];


         $state = $data["state"];


         $dist_type = $data["dist_type"];


        $fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        


      


        if($order_type == "all"){


           $sql1 = ''.db_prefix().'ordermaster.Transdate <= "'.$dates.' 23:59:00" AND OrderStatus IN("C","O") AND '.db_prefix().'ordermaster.ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy; 


        }if($order_type == "O"){


            $sql1 = ''.db_prefix().'ordermaster.Transdate <= "'.$dates.' 23:59:00" AND OrderStatus IN("O") AND '.db_prefix().'ordermaster.ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy; 


        }


        if($order_type == "C"){


            $sql1 = ''.db_prefix().'ordermaster.Transdate <= "'.$dates.' 23:59:00" AND OrderStatus IN("C") AND '.db_prefix().'ordermaster.ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy; 


        }


        if (empty($state)) {


            


        }else {


            $sql1 .= ' AND '.db_prefix().'ordermaster.AccountID = (SELECT '.db_prefix().'clients.AccountID FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.state = "'.$state.'" AND '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.')';


        }


        


        if (empty($dist_type)) {


            


        }else {


            $sql1 .= ' AND '.db_prefix().'ordermaster.AccountID = (SELECT '.db_prefix().'clients.AccountID FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.DistributorType = '.$dist_type.' AND '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.')';


        }


        


        //$sql1 .= ' AND  '.db_prefix().'stockmaster.FY = "'.$fy.'"';


        $sql1 .= ' AND '.db_prefix().'history.PlantID = '.$selected_company.' GROUP BY '.db_prefix().'history.ItemID,'.db_prefix().'history.CaseQty';


        $sql1 .= '  ORDER BY '.db_prefix().'items.subgroup_id';


        


        $sql ='SELECT SUM('.db_prefix().'history.OrderAmt) AS OrderAmt,SUM(IFNULL('.db_prefix().'history.eOrderQty, '.db_prefix().'history.OrderQty)) AS OrderQty,SUM('.db_prefix().'history.NetOrderAmt) AS NetOrderAmt,'.db_prefix().'history.ItemID AS Item_code,CaseQty,


        '.db_prefix().'stockmaster.OQty,'.db_prefix().'items.description,'.db_prefix().'history.CaseQty,


        (SELECT GROUP_CONCAT(taxrate SEPARATOR ",") FROM '.db_prefix().'taxes WHERE '.db_prefix().'items.tax = '.db_prefix().'taxes.id) as taxName


       FROM '.db_prefix().'history 


        INNER JOIN '.db_prefix().'ordermaster ON '.db_prefix().'history.OrderID = '.db_prefix().'ordermaster.OrderID


        INNER JOIN '.db_prefix().'items ON '.db_prefix().'history.ItemID = '.db_prefix().'items.item_code AND '.db_prefix().'history.PlantID = '.db_prefix().'items.PlantID 


        LEFT JOIN '.db_prefix().'stockmaster ON '.db_prefix().'history.ItemID = '.db_prefix().'stockmaster.ItemID AND '.db_prefix().'history.PlantID = '.db_prefix().'stockmaster.PlantID AND '.db_prefix().'history.FY = '.db_prefix().'stockmaster.FY 


        WHERE '.$sql1;


    $result = $this->db->query($sql)->result_array();


    $itemIds = array();


    foreach ($result as $key => $value) {


        array_push($itemIds, $value["Item_code"]);


    }


    $from_date = '20'.$fy.'-04-01 00:00:00';


        $this->db->select('ItemID,TType,TType2,CaseQty,SUM(BilledQty) AS BilledQty');


        $this->db->from(db_prefix() .'history');


        $this->db->where(db_prefix() .'history.PlantID', $selected_company);


        $this->db->where(db_prefix() .'history.FY', $fy);


        $this->db->where_in(db_prefix() .'history.ItemID', $itemIds);


        $this->db->where(db_prefix() .'history.TransDate2 BETWEEN "'. $from_date. '" AND "'. $dates. ' 23:59:00" ');


        $this->db->where(db_prefix() . 'history.BillID is NOT NULL', NULL, FALSE);


        $this->db->group_by('ItemID,TType,TType2');


        $StockData = $this->db->get()->result_array();


        $i = 0;


    foreach ($result as $key1 => $value1) {


        $PQty = 0;


        $PRQty = 0;


        $IQty = 0;


        $PRDQty = 0;


        $SQty = 0;


        $SRTQty = 0;


        $AQty = 0;


        $GIQty = 0;


        $GOQty = 0;


        


        foreach ($StockData as $key2 => $value2) {


            if($value1["Item_code"] == $value2["ItemID"]){


                


                    if($value2['TType'] == 'P'){


                        $PQty = $value2['BilledQty'];


                    }elseif($value2['TType'] == 'N'){


                        $PRQty = $value2['BilledQty'];


                    }elseif($value2['TType'] == 'A'){


                        $IQty = $value2['BilledQty'];


                    }elseif($value2['TType'] == 'B'){


                        $PRDQty = $value2['BilledQty'];


                    }elseif($value2['TType'] == 'O' && $value2['TType2'] == 'Order'){


                        $SQty = $value2['BilledQty'];


                    }elseif($value2['TType'] == 'R' && $value2['TType2'] == 'Fresh'){


                        $SRTQty = $value2['BilledQty'];


                    }elseif($value2['TType'] == 'X'  && $value2['TType2'] == 'Free distribution'){


                        $AQty += $value2['BilledQty'];


                    }elseif($value2['TType'] == 'X'  && $value2['TType2'] == 'Free Distribution'){


                        $AQty += $value2['BilledQty'];


                    }elseif($value2['TType'] == 'X' && $value2['TType2'] == 'Stock Adjustment'){


                        $AQty += $value2['BilledQty'];


                    }elseif($value2['TType'] == 'X' && $value2['TType2'] == 'Stock Damaged'){


                        $AQty += $value2['BilledQty'];


                    }elseif($value2['TType'] == 'X' && $value2['TType2'] == 'Promotional Activity'){


                        $AQty += $value2['BilledQty'];


                    }elseif($value2['TType'] == 'T' && $value2['TType2'] == 'In'){


                        $GIQty = $value2['BilledQty'];


                    }elseif($value2['TType'] == 'T' && $value2['TType2'] == 'Out'){


                        $GOQty = $value2['BilledQty'];


                    }


                    


            }


        }


        $stockQty = $value1['OQty'] + $PQty - $PRQty - $IQty + $PRDQty - $SQty + $SRTQty - $AQty - $GOQty + $GIQty;


                    $stockQtyInCase = $stockQty / $value1['CaseQty'];


        $result[$i]['StockBal'] = $stockQtyInCase;


        


        $i++;


    }


    return $result;


}


     


    public function update_order_status($selected_ids,$selected_ids_remarks,$unselected_ids,$unselected_ids_remarks)


     {


        $fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        $selected_ids_array = explode(',', $selected_ids);


        $selected_ids_remarks_array = explode(',', $selected_ids_remarks);


        $unselected_ids_array = explode(',', $unselected_ids);


        $unselected_ids_remarks_array = explode(',', $unselected_ids_remarks);


        


        $i = 0;


        


        $this->db->select('OrderID');


        $this->db->from(db_prefix() .'ordermaster');


        $this->db->where(db_prefix() .'ordermaster.PlantID', $selected_company);


        $this->db->where(db_prefix() .'ordermaster.FY', $fy);


        $this->db->where_in(db_prefix() .'ordermaster.OrderID', $selected_ids_array);


        $this->db->where(db_prefix() . 'ordermaster.SalesID IS NULL', NULL, FALSE);


        $PendingData = $this->db->get()->result_array();


        


        foreach($selected_ids_array as $id)


        {


            $data1 = array(


                "OrderStatus" => "C",


                "remark" => $selected_ids_remarks_array2[$i],


            ); 


            foreach($PendingData as $IDS){


                if($IDS["OrderID"] == $id){


                    $this->db->where(db_prefix() . 'ordermaster.OrderID', $id);


                    $this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);


                    $this->db->LIKE(db_prefix() . 'ordermaster.FY', $fy); 


                    $this->db->update(db_prefix() . 'ordermaster', $data1);


                    $i++;


                        $data1_history = array(


                            "TType2" => "Cancel",


                            "TType" => "C",


                        );


                    // for history


                        $this->db->where_in(db_prefix() . 'history.OrderID', $id);


                        $this->db->where(db_prefix() . 'history.PlantID', $selected_company);


                        $this->db->LIKE(db_prefix() . 'history.FY', $fy); 


                        $this->db->update(db_prefix() . 'history', $data1_history);


                }


            } 


        }


        


        


                  


        //return $selected_ids_array;


        $j=0;


        foreach($unselected_ids_array as $id)


        {


                $data2 = array(


                   "OrderStatus" => "O",


                   "remark" => $unselected_ids_remarks_array[$j],


                ); 


                  $this->db->where(db_prefix() . 'ordermaster.OrderID', $id);


                  $this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);


                  $this->db->LIKE(db_prefix() . 'ordermaster.FY', $fy); 


                  $this->db->update(db_prefix() . 'ordermaster', $data2);


                $j++;


        }


        


            $data2_history = array(


                "TType2" => "Order",


            );


            // for history 


                $this->db->where_in(db_prefix() . 'history.OrderID', $unselected_ids_array);


                $this->db->where(db_prefix() . 'history.PlantID', $selected_company);


                $this->db->LIKE(db_prefix() . 'history.FY', $fy); 


                $this->db->update(db_prefix() . 'history', $data2_history);


        return $unselected_ids_array;


       


     }


    


    public function reset_order_status($selected_ids,$selected_ids_remarks,$unselected_ids,$unselected_ids_remarks)


     {


         $fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        $selected_ids_array = explode(',', $selected_ids);


      //  $selected_ids_remarks_array = explode(',', $selected_ids_remarks);


        $unselected_ids_array = explode(',', $unselected_ids);


      //  $unselected_ids_remarks_array = explode(',', $unselected_ids_remarks);


        


         $selected_ids_remarks_array = explode(',', "");


         $unselected_ids_remarks_array = explode(',', "");


        // print_r($selected_ids_array); exit();


        //$selected_ids_remarks_array2 = array();


        /*foreach($selected_ids_remarks_array as $key => $link) 


        { 


            if($link === ' ') 


            { 


                unset($selected_ids_remarks_array[$key]); 


            }else{


                array_push($selected_ids_remarks_array2, $link);


            }


        } */


        $i = 0;


        foreach($selected_ids_array as $id)


        {


            $data1 = array(


                   "OrderStatus" => "C",


                    "remark" => $selected_ids_remarks_array2[$i],


                  ); 


                 


                  $this->db->where(db_prefix() . 'ordermaster.OrderID', $id);


                  $this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);


                  $this->db->LIKE(db_prefix() . 'ordermaster.FY', $fy); 


                  $this->db->update(db_prefix() . 'ordermaster', $data1);


                  //$aa= $this->db->last_query(); print($aa); //exit();


                $i++;


        }


        $j=0;


        foreach($unselected_ids_array as $id)


        { 


            $data2 = array(


                   "OrderStatus" => "O",


                   "remark" => $unselected_ids_remarks_array[$j],


                  ); 


                 


                  $this->db->where(db_prefix() . 'ordermaster.OrderID', $id);


                  $this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);


                  $this->db->LIKE(db_prefix() . 'ordermaster.FY', $fy); 


                  $this->db->update(db_prefix() . 'ordermaster', $data2);


                 //$aa= $this->db->last_query(); print($aa); 


                $j++;


        }


       // print_r($id); exit();


        return $unselected_ids_array;


       


    }


    


    public function load_data2($data)


     {  


         $dates = to_sql_date($data["dates"]);


         $order_type = $data["order_type"];


         $state = $data["state"];


         $dist_type = $data["dist_type"];


         $selected_ids = $data["selected_ids"];


        $fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        


        $month = date('m');


            if($month == "01"){


               $m = 2; 


            }


            if($month == "02"){


               $m = 3; 


            }


            if($month == "03"){


               $m = 4; 


            }


            if($month == "04"){


               $m = 5; 


            }


            if($month == "05"){


               $m = 6; 


            }


            if($month == "06"){


               $m = 7; 


            }


            if($month == "07"){


               $m = 8; 


            }


            if($month == "08"){


               $m = 9; 


            }


            if($month == "09"){


               $m = 10; 


            }


            if($month == "10"){


               $m = 11; 


            }


            if($month == "11"){


               $m = 12; 


            }


            if($month == "12"){


               $m = 13; 


            }


            $mm = "BAL".$m;


      


        if($order_type == "all"){


           $sql1 = 'Transdate <= "'.$dates.' 23:59:00" AND OrderStatus IN("C","O") AND ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy; 


        }if($order_type == "O"){


            $sql1 = 'Transdate <= "'.$dates.' 23:59:00" AND OrderStatus IN("O") AND ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy; 


        }


        if($order_type == "C"){


            $sql1 = 'Transdate <= "'.$dates.' 23:59:00" AND OrderStatus IN("C") AND ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy; 


        }


        if (empty($state)) {


            


        }else {


            $sql1 .= ' AND '.db_prefix().'ordermaster.AccountID = (SELECT '.db_prefix().'clients.AccountID FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.state = "'.$state.'" AND '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.')';


        }


        


        if (empty($dist_type)) {


            


        }else {


            $sql1 .= ' AND '.db_prefix().'ordermaster.AccountID = (SELECT '.db_prefix().'clients.AccountID FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.DistributorType = '.$dist_type.' AND '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.')';


        }


        


        if (empty($selected_ids)) {


            


        }else {


            $ids = explode(",",$selected_ids);   


            $sql1 .= ' AND '.db_prefix().'ordermaster.OrderID IN ("'.implode('","',$ids).'")';


        }


        


        $sql1 .= ' ORDER BY Transdate DESC';


        


        $sql ='SELECT '.db_prefix().'ordermaster.*,IFNULL(BAL1,0.00) as bal1,IFNULL(BAL2,0.00) as bal2,IFNULL(BAL3,0.00) as bal3,IFNULL(BAL4,0.00) as bal4,IFNULL(BAL5,0.00) as bal5,IFNULL(BAL6,0.00) as bal6,IFNULL(BAL7,0.00) as bal7,IFNULL(BAL8,0.00) as bal8,IFNULL(BAL9,0.00) as bal9,IFNULL(BAL10,0.00) as bal10,IFNULL(BAL11,0.00) as bal11,IFNULL(BAL12,0.00) as bal12,IFNULL(BAL13,0.00) as bal13,


        (SELECT GROUP_CONCAT(company SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as AccountName,


        (SELECT GROUP_CONCAT(StationName SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as StationName,


        (SELECT GROUP_CONCAT(CONCAT ( firstname," ",lastname ) SEPARATOR ",") FROM '.db_prefix().'customer_admins 


            LEFT JOIN tblstaff ON tblstaff.staffid = tblcustomer_admins.staff_id 


            WHERE '.db_prefix().'customer_admins.customer_id = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'customer_admins.company_id = '.$selected_company.') as SOID,


        (SELECT '.db_prefix().'xx_statelist.short_name


FROM  '.db_prefix().'xx_statelist


INNER JOIN '.db_prefix().'clients 


ON '.db_prefix().'xx_statelist.short_name = '.db_prefix().'clients.state


WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as StateName,


(SELECT '.db_prefix().'customers_groups.name


FROM  '.db_prefix().'customers_groups


INNER JOIN '.db_prefix().'clients 


ON '.db_prefix().'customers_groups.id = '.db_prefix().'clients.DistributorType


WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as dist_Type


        FROM '.db_prefix().'ordermaster 


        INNER JOIN '.db_prefix().'accountbalances 


ON '.db_prefix().'ordermaster.AccountID = '.db_prefix().'accountbalances.AccountID AND '.db_prefix().'ordermaster.PlantID = '.db_prefix().'accountbalances.PlantID AND '.db_prefix().'ordermaster.FY = '.db_prefix().'accountbalances.FY


WHERE '.$sql1;


        


        


        $result = $this->db->query($sql)->result_array();


        


        $i = 0;


        $fy_to = $fy + 1;


        $from_date = '20'.$fy.'-04-01';


        $to_date = '20'.$fy_to.'-03-31';


        foreach($result as $value){


            


            // credit crated


                $this->db->select('sum(Amount) as credit_bal,AccountID');


                $this->db->where('tblaccountledger.PlantID', $selected_company);


                $this->db->where('tblaccountledger.FY', $fy);


                $this->db->where('tblaccountledger.TType', 'C');


                $this->db->where('tblaccountledger.AccountID', $value["AccountID"]);


                $this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');


                $this->db->group_by('AccountID');


                $credit_bal = $this->db->get('tblaccountledger')->result_array();


                


            // Debit crated


                $this->db->select('sum(Amount) as debit_bal,AccountID');


                $this->db->where('tblaccountledger.PlantID', $selected_company);


                $this->db->where('tblaccountledger.FY', $fy);


                $this->db->where('tblaccountledger.TType', 'D');


                $this->db->where('tblaccountledger.AccountID', $value["AccountID"]);


                $this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');


                $this->db->group_by('AccountID');


                $debit_bal = $this->db->get('tblaccountledger')->result_array();


             $balance = $debit_bal[0]['debit_bal'] - $credit_bal[0]['credit_bal'];


            $result[$i]['balance'] = $balance;


            


            $i++; 


                 


        }  


        return $result;


     }


     


    public function load_data_items2($data)


     {  


         $dates = to_sql_date($data["dates"]);


         $order_type = $data["order_type"];


         $state = $data["state"];


         $dist_type = $data["dist_type"];


         $selected_ids = $data["selected_ids"];


        $fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        


      


        if($order_type == "all"){


           $sql1 = ''.db_prefix().'ordermaster.Transdate <= "'.$dates.' 23:59:00" AND OrderStatus IN("C","O") AND '.db_prefix().'ordermaster.ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy; 


        }if($order_type == "O"){


            $sql1 = ''.db_prefix().'ordermaster.Transdate <= "'.$dates.' 23:59:00" AND OrderStatus IN("O") AND '.db_prefix().'ordermaster.ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy; 


        }


        if($order_type == "C"){


            $sql1 = ''.db_prefix().'ordermaster.Transdate <= "'.$dates.' 23:59:00" AND OrderStatus IN("C") AND '.db_prefix().'ordermaster.ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy; 


        }


        if (empty($state)) {


            


        }else {


            $sql1 .= ' AND '.db_prefix().'ordermaster.AccountID = (SELECT '.db_prefix().'clients.AccountID FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.state = "'.$state.'" AND '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.')';


        }


        


        if (empty($dist_type)) {


            


        }else {


            $sql1 .= ' AND '.db_prefix().'ordermaster.AccountID = (SELECT '.db_prefix().'clients.AccountID FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.DistributorType = '.$dist_type.' AND '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.')';


        }


        


        if (empty($selected_ids)) {


            


        }else {


            $ids = explode(",",$selected_ids);   


            $sql1 .= ' AND '.db_prefix().'history.OrderID IN ("'.implode('","',$ids).'")';


        }


        


        $sql1 .= ' GROUP BY '.db_prefix().'history.ItemID';


        $sql1 .= ' ORDER BY '.db_prefix().'items.subgroup_id';


        


        $sql ='SELECT SUM('.db_prefix().'history.OrderAmt) AS OrderAmt,SUM(IFNULL('.db_prefix().'history.eOrderQty, '.db_prefix().'history.OrderQty)) AS OrderQty,SUM('.db_prefix().'history.NetOrderAmt) AS NetOrderAmt,


        '.db_prefix().'stockmaster.OQty,'.db_prefix().'history.ItemID AS Item_code,CaseQty,'.db_prefix().'items.description,


        (SELECT GROUP_CONCAT(taxrate SEPARATOR ",") FROM '.db_prefix().'taxes WHERE '.db_prefix().'items.tax = '.db_prefix().'taxes.id) as taxName


        FROM '.db_prefix().'history 


    INNER JOIN '.db_prefix().'ordermaster ON '.db_prefix().'history.OrderID = '.db_prefix().'ordermaster.OrderID


    INNER JOIN '.db_prefix().'items ON '.db_prefix().'history.ItemID = '.db_prefix().'items.item_code AND '.db_prefix().'history.PlantID = '.db_prefix().'items.PlantID 


    LEFT JOIN '.db_prefix().'stockmaster ON '.db_prefix().'history.ItemID = '.db_prefix().'stockmaster.ItemID AND '.db_prefix().'history.PlantID = '.db_prefix().'stockmaster.PlantID AND '.db_prefix().'history.FY = '.db_prefix().'stockmaster.FY 


    WHERE '.$sql1;


    $result = $this->db->query($sql)->result_array();


    


    $itemIds = array();


    foreach ($result as $key => $value) {


        array_push($itemIds, $value["Item_code"]);


    }


    $from_date = '20'.$fy.'-04-01 00:00:00';


        $this->db->select('ItemID,TType,TType2,SUM(BilledQty) AS BilledQty');


        $this->db->from(db_prefix() .'history');


        $this->db->where(db_prefix() .'history.PlantID', $selected_company);


        $this->db->where(db_prefix() .'history.FY', $fy);


        $this->db->where_in(db_prefix() .'history.ItemID', $itemIds);


        $this->db->where(db_prefix() .'history.TransDate2 BETWEEN "'. $from_date. '" AND "'. $dates. ' 23:59:00" ');


        $this->db->where(db_prefix() . 'history.BillID is NOT NULL', NULL, FALSE);


        $this->db->group_by('ItemID,TType,TType2');


        $StockData = $this->db->get()->result_array();


        $i = 0;


    foreach ($result as $key1 => $value1) {


        $PQty = 0;


                $PRQty = 0;


                $IQty = 0;


                $PRDQty = 0;


                $SQty = 0;


                $SRTQty = 0;


                $AQty = 0;


                $GIQty = 0;


                $GOQty = 0;


        foreach ($StockData as $key2 => $value2) {


            if($value1["Item_code"] == $value2["ItemID"]){


                


                    if($value2['TType'] == 'P'){


                        $PQty = $value2['BilledQty'];


                    }elseif($value2['TType'] == 'N'){


                        $PRQty = $value2['BilledQty'];


                    }elseif($value2['TType'] == 'A'){


                        $IQty = $value2['BilledQty'];


                    }elseif($value2['TType'] == 'B'){


                        $PRDQty = $value2['BilledQty'];


                    }elseif($value2['TType'] == 'O' && $value2['TType2'] == 'Order'){


                        $SQty = $value2['BilledQty'];


                    }elseif($value2['TType'] == 'R' && $value2['TType2'] == 'Fresh'){


                        $SRTQty = $value2['BilledQty'];


                    }elseif($value2['TType'] == 'X'){


                        $AQty = $value2['BilledQty'];


                    }elseif($value2['TType'] == 'T' && $value2['TType2'] == 'In'){


                        $GIQty = $value2['BilledQty'];


                    }elseif($value2['TType'] == 'T' && $value2['TType2'] == 'Out'){


                        $GOQty = $value2['BilledQty'];


                    }


            }


        }


        $stockQty = $value1['OQty'] + $PQty - $PRQty - $IQty + $PRDQty - $SQty + $SRTQty - $AQty - $GOQty + $GIQty;


                    $stockQtyInCase = $stockQty / $value1['CaseQty'];


        $result[$i]['StockBal'] = $stockQtyInCase;


        


        $i++;


    }


    return $result;


}


     


    public function getorder_by_challan($id = '', $where = [])


    {


        $this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'ordermaster.OrderID as id, ' . db_prefix() . 'currencies.name as currency_name');


        $this->db->from(db_prefix() . 'ordermaster');


        $this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'ordermaster.currency', 'left');


        //$this->db->where($where);


        





        //$this->db->order_by('YEAR(date)', 'desc');


        $fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        


        $this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);


        $this->db->LIKE(db_prefix() . 'ordermaster.FY', $fy);


       $this->db->where(db_prefix() . 'ordermaster.ChallanID', $id);


       //$this->db->or_where(db_prefix() . 'ordermaster.ChallanID', "");


        return $this->db->get()->result_array();


    }


    


    public function get_order_items($orderID,$PlantID,$FY)


    {


        if($PlantID == "1"){


            $GodownID = 'CSPL';


        }else if($PlantID == "2"){


            $GodownID = 'CFF';


        }else if($PlantID == "3"){


            $GodownID = 'CBUPL';


        }


        


        $this->db->select(db_prefix() .'history.*,'.db_prefix() .'stockmaster.*,'.db_prefix() . 'items.description,'.db_prefix() . 'items.hsn_code');


        $this->db->from(db_prefix() .'history');


        $this->db->join(db_prefix() .'items', db_prefix() .'items.item_code = '.db_prefix() .'history.ItemID AND '.db_prefix() .'items.PlantID = '.db_prefix() .'history.PlantID');


        $this->db->join(db_prefix() .'stockmaster', db_prefix() .'stockmaster.ItemID = '.db_prefix() .'history.ItemID AND '.db_prefix() .'stockmaster.PlantID = '.db_prefix() .'history.PlantID AND '.db_prefix() .'stockmaster.FY = '.db_prefix() .'history.FY AND '.db_prefix() .'stockmaster.cnfid = "1" AND '.db_prefix() .'stockmaster.GodownID = "'.$GodownID.'"','LEFT');


        $this->db->where(db_prefix() .'history.OrderID', $orderID);


        $this->db->where(db_prefix() .'history.PlantID', $PlantID);


        //$this->db->where(db_prefix() .'history.NetOrderAmt !=', '0.00');


        //$this->db->where(db_prefix() .'items.PlantID', $PlantID);


        $this->db->where(db_prefix() .'history.FY', $FY);


        //$this->db->where(db_prefix() .'stockmaster.FY', $FY);


        //$this->db->where(db_prefix() .'stockmaster.PlantID', $PlantID);


        return $this->db->get()->result_array();


    }


    public function GetItemStock($orderID,$PlantID,$FY)


    {


        $fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        $this->db->select('ItemID,TType,TType2,SUM(BilledQty) AS BilledQty');


        $this->db->from(db_prefix() .'history');


        //$this->db->where(db_prefix() .'history.OrderID', $orderID);


        $this->db->where(db_prefix() .'history.PlantID', $selected_company);


        //$this->db->where(db_prefix() . 'history.BillID ', NULL);


        $this->db->where(db_prefix() . 'history.BillID is NOT NULL', NULL, FALSE);


        $this->db->where(db_prefix() .'history.FY', $fy);


        $this->db->group_by('ItemID,TType,TType2');


        return $this->db->get()->result_array();


    }


    


    public function get_state_list()


    {


        $this->db->order_by('state_name', 'ASC');


        return $this->db->get(db_prefix() . 'xx_statelist')->result_array();


    }


    


    public function get_selected_company_details()


    {


        $selected_company = $this->session->userdata('root_company');


        $selected_year = $this->session->userdata('finacial_year');


        $this->db->where('PlantID', $selected_company);


        $this->db->where('FY', $selected_year);


        return $this->db->get(db_prefix() . 'setup')->row();


    }


    


    public function get_distributor_type()


    {


       $selected_company = $this->session->userdata('root_company');


        $this->db->where('PlantID', $selected_company);


        


        return $this->db->get(db_prefix() . 'customers_groups')->result_array();


    }


    


   /* public function get_accbal($AccountID,$PlantID,$FY)


    {


        $this->db->where('AccountID', $AccountID);


        $this->db->where('PlantID', $PlantID);


        $this->db->where('FY', $FY);





        return $this->db->get(db_prefix() . 'accountbalances')->row();


    }*/


    


    


    


    public function get_accbal($AccountID,$PlantID,$FY){


        $selected_company = $this->session->userdata('root_company');


        $FY = $this->session->userdata('finacial_year');


        $Obal = 0;


        


        $sql = '';


        $sql .= 'SELECT SUM(Amount) as dramt_sum,tblaccountledger.AccountID,Transdate FROM `tblaccountledger`';


        $sql .= ' WHERE  AccountID = "'.$AccountID.'" AND tblaccountledger.PlantID = '.$selected_company.' AND tblaccountledger.FY = "'.$FY.'" AND tblaccountledger.TType = "D"';


        $result1 = $this->db->query($sql)->row();


        


        $sql2 = '';


        $sql2 .= 'SELECT SUM(Amount) as cramt_sum,tblaccountledger.AccountID,Transdate FROM `tblaccountledger`';


        $sql2 .= ' WHERE  AccountID = "'.$AccountID.'" AND tblaccountledger.PlantID = '.$selected_company.' AND tblaccountledger.FY = "'.$FY.'" AND tblaccountledger.TType = "C"';


        $result2 = $this->db->query($sql2)->row();


        


        $sql3 = '';


        $sql3 .= 'SELECT BAL1 FROM `tblaccountbalances`';


        $sql3 .= ' WHERE  AccountID = "'.$AccountID.'" AND tblaccountbalances.PlantID = '.$selected_company.' AND tblaccountbalances.FY = "'.$FY.'"';


        $result3 = $this->db->query($sql3)->row();


        if(empty($result3)){


            


        }else{


            $Obal = $result3->BAL1;


        }


        $bal = $Obal + $result1->dramt_sum - $result2->cramt_sum;


        return $bal;


    }


    public function SaleDetails($SalesID,$PlantID,$FY)


    {


        $this->db->select('SalesID,irn,ackno,Transdate');


        $this->db->where('SalesID', $SalesID);


        $this->db->where('PlantID', $PlantID);


        $this->db->LIKE('FY', $FY);


        return $this->db->get(db_prefix() . 'salesmaster')->row();


    }


    public function ChallanDetails($ChallanID,$PlantID,$FY)


    {


        $this->db->select('ChallanID,GetPassTime,gatepasstime,Gatepassuserid,ChallanAmt');


        $this->db->where('ChallanID', $ChallanID);


        $this->db->where('PlantID', $PlantID);


        $this->db->LIKE('FY', $FY);


        return $this->db->get(db_prefix() . 'challanmaster')->row();


    }


    public function get_last_bill_on($AccountID,$PlantID,$FY)


    {


       $this->db->where('AccountID', $AccountID);


        $this->db->where('PlantID', $PlantID);


        $this->db->where('FY', $FY);


        $this->db->where('TType', 'D');


        $this->db->where('PassedFrom', 'SALE');


        $this->db->order_by('Transdate', 'DESC');


        return $this->db->get(db_prefix() . 'accountledger')->row();


    }


    


    public function get_last_deposit_on($AccountID,$PlantID,$FY)


    {


        $this->db->where('AccountID', $AccountID);


        $this->db->where('PlantID', $PlantID);


        $this->db->where('FY', $FY);


        $this->db->where('TType', 'C');


        $this->db->order_by('Transdate', 'DESC');


        return $this->db->get(db_prefix() . 'accountledger')->row();


    }


    


    public function check_invoice_generate($id)


    {


        $this->db->where('order_id', $id);





        return $this->db->get(db_prefix() . 'invoices')->row();


    }





    public function mark_as_cancelled($id)


    {


        $isDraft = $this->is_draft($id);





        $this->db->where('id', $id);


        $this->db->update(db_prefix() . 'invoices', [


            'status' => self::STATUS_CANCELLED,


            'sent'   => 1,


        ]);





        if ($this->db->affected_rows() > 0) {


            if ($isDraft) {


                $this->change_invoice_number_when_status_draft($id);


            }





            $this->log_invoice_activity($id, 'invoice_activity_marked_as_cancelled');





            hooks()->do_action('invoice_marked_as_cancelled', $id);





            return true;


        }





        return false;


    }





    public function unmark_as_cancelled($id)


    {


        $this->db->where('id', $id);


        $this->db->update(db_prefix() . 'invoices', [


            'status' => self::STATUS_UNPAID,


        ]);





        if ($this->db->affected_rows() > 0) {


            $this->log_invoice_activity($id, 'invoice_activity_unmarked_as_cancelled');





            return true;


        }





        return false;


    }


    public function remark_update($data)


    {


        $itemid = $data['itemid'];


        unset($data['itemid']);


        


        $this->db->where('OrderID', $itemid);


        $this->db->update(db_prefix() . 'ordermaster', $data);





        if ($this->db->affected_rows() > 0) {


            $this->log_invoice_activity($itemid, 'remark updated');





            return true;


        }





        return false;


    }





    /**


     * Get this invoice generated recurring invoices


     * @since  Version 1.0.1


     * @param  mixed $id main invoice id


     * @return array


     */


    public function get_invoice_recurring_invoices($id)


    {


        $this->db->select('id');


        $this->db->where('is_recurring_from', $id);


        $invoices           = $this->db->get(db_prefix() . 'invoices')->result_array();


        $recurring_invoices = [];





        foreach ($invoices as $invoice) {


            $recurring_invoices[] = $this->get($invoice['id']);


        }





        return $recurring_invoices;


    }





    /**


     * Get invoice total from all statuses


     * @since  Version 1.0.2


     * @param  mixed $data $_POST data


     * @return array


     */


    public function get_invoices_total($data)


    {


        $this->load->model('currencies_model');





        if (isset($data['currency'])) {


            $currencyid = $data['currency'];


        } elseif (isset($data['customer_id']) && $data['customer_id'] != '') {


            $currencyid = $this->clients_model->get_customer_default_currency($data['customer_id']);


            if ($currencyid == 0) {


                $currencyid = $this->currencies_model->get_base_currency()->id;


            }


        } elseif (isset($data['project_id']) && $data['project_id'] != '') {


            $this->load->model('projects_model');


            $currencyid = $this->projects_model->get_currency($data['project_id'])->id;


        } else {


            $currencyid = $this->currencies_model->get_base_currency()->id;


        }





        $result            = [];


        $result['due']     = [];


        $result['paid']    = [];


        $result['overdue'] = [];





        $has_permission_view                = has_permission('invoices', '', 'view');


        $has_permission_view_own            = has_permission('invoices', '', 'view_own');


        $allow_staff_view_invoices_assigned = get_option('allow_staff_view_invoices_assigned');


        $noPermissionsQuery                 = get_invoices_where_sql_for_staff(get_staff_user_id());





        for ($i = 1; $i <= 3; $i++) {


            $select = 'id,total';


            if ($i == 1) {


                $select .= ', (SELECT total - (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid = ' . db_prefix() . 'invoices.id) - (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.invoice_id=' . db_prefix() . 'invoices.id)) as outstanding';


            } elseif ($i == 2) {


                $select .= ',(SELECT SUM(amount) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid=' . db_prefix() . 'invoices.id) as total_paid';


            }


            $this->db->select($select);


            $this->db->from(db_prefix() . 'invoices');


            $this->db->where('currency', $currencyid);


            // Exclude cancelled invoices


            $this->db->where('status !=', self::STATUS_CANCELLED);


            // Exclude draft


            $this->db->where('status !=', self::STATUS_DRAFT);





            if (isset($data['project_id']) && $data['project_id'] != '') {


                $this->db->where('project_id', $data['project_id']);


            } elseif (isset($data['customer_id']) && $data['customer_id'] != '') {


                $this->db->where('clientid', $data['customer_id']);


            }





            if ($i == 3) {


                $this->db->where('status', self::STATUS_OVERDUE);


            } elseif ($i == 1) {


                $this->db->where('status !=', self::STATUS_PAID);


            }





            if (isset($data['years']) && count($data['years']) > 0) {


                $this->db->where_in('YEAR(date)', $data['years']);


            } else {


                $this->db->where('YEAR(date)', date('Y'));


            }





            if (!$has_permission_view) {


                $whereUser = $noPermissionsQuery;


                $this->db->where('(' . $whereUser . ')');


            }





            $invoices = $this->db->get()->result_array();





            foreach ($invoices as $invoice) {


                if ($i == 1) {


                    $result['due'][] = $invoice['outstanding'];


                } elseif ($i == 2) {


                    $result['paid'][] = $invoice['total_paid'];


                } elseif ($i == 3) {


                    $result['overdue'][] = $invoice['total'];


                }


            }


        }


        $currency             = get_currency($currencyid);


        $result['due']        = array_sum($result['due']);


        $result['paid']       = array_sum($result['paid']);


        $result['overdue']    = array_sum($result['overdue']);


        $result['currency']   = $currency;


        $result['currencyid'] = $currencyid;





        return $result;


    }


    


    





    /**


     * Insert new invoice to database


     * @param array $data invoice data


     * @return mixed - false if not insert, invoice ID if succes


     */


    public function AddSaleInvoice($data)


    {


        if(isset($data['sale_invoice_detail'])){


            $sale_invoice_detail = json_decode($data['sale_invoice_detail']);


            unset($data['sale_invoice_detail']);


            


            $es_detail = [];


            $row = [];


            $rq_val = [];


            $header = [];


            $header[] = 'ItemID';


            $header[] = 'Batch';


            $header[] = 'sale_qty';


            $header[] = 'rate';


            $header[] = 'value';


            $header[] = 'gst';


            $header[] = 'cgst_amt';


            $header[] = 'sgst_amt';


            $header[] = 'igst_amt';


            $header[] = 'net_amt';


            foreach ($sale_invoice_detail as $key => $value) {


                if($value[0] != ''){


                    $es_detail[] = array_combine($header, $value);


                }


            }


        }


        


        $PlantID = $this->session->userdata('root_company'); 


        $FY = $this->session->userdata('finacial_year');


        $GodownID = $data['WHID'];


        $CenterID = $data['CenterID'];


        $TaxID = get_option('next_tax_number_for_kirti');


        $newTaxID = 'TAX'.$FY.$TaxID;   


        $ItCount = count($es_detail);


        $Transdate =  to_sql_date($data['order_date'])." ".date('H:i:s');


        $BrokerID = $data['BrokerID'];


        $cash_credit = $data['cash_credit'];


        $PaymentTerm = $data['PaymentTerm'];


        $TransporterID = $data['TransporterID'];


        $VehicleNo = $data['VehicleNo'];


        $LRNo = $data['LRNo'];


        $UserID = $this->session->userdata('username');


        $total_qty_in_mt = $data['total_qty_in_mt'];


        $Total_value = $data['Total_value'];


        $total_cgst_amt = $data['total_cgst_amt'];


        $total_sgst_amt = $data['total_sgst_amt'];


        $total_igst_amt = $data['total_igst_amt'];


        $total_net_value = $data['total_net_value'];


        $selected_state = $data['selected_state'];


        if($cash_credit == "CASH"){


            $AccountID = $data['CustID'];


            $CustName = $data['CustName'];


            $CustState = $data['CustState'];


            $CustAddress = $data['CustAddress'];


            $ShipTo = $data['CustID'];


        }else{


            $AccountID = $data['bill_to_ActID'];


            $ShipTo = $data['ship_to_ActID'];


            $CustName = $data['CustName'];


            $CustState = $data['CustState'];


            $CustAddress = $data['CustAddress'];


        }


        $AccountDetails = $this->GetAccountDetails($AccountID);


        if($AccountDetails){


            $GstIN = $AccountDetails->gstin;


        }else{


            $GstIN = null;


            


            // Insert new Party


            $next_code = $this->get_next_code('next_farmer_code');


            $number = 'KF'.str_pad($next_code->value, 4, '0', STR_PAD_LEFT);


            $New_party_array = array(


                "PlantID" => $PlantID,


                "AccountID"=>$AccountID,


                "ShortCode"=>$number,


                "company"=>$CustName,


                "CustomerType"=>1,


                "ActGroupID"=>'10000',


                "SubActGroupID1"=>'100002',


                "SubActGroupID"=>'1000006',


                "state"=>$CustState,


                "house"=>$CustAddress,


                "StartDate"=>$CenterID,


                "datecreated"=>date('Y-m-d H:i:s'),


                "UserID"=>$UserID


            );


            


            if($this->db->insert(db_prefix() . 'clients',$New_party_array)){


                $this->increment_next_farmer_number();


                $ContactsArray = array(


                    'PlantID' =>$PlantID,


                    'AccountID' =>strtoupper($AccountID),


                    'firstname' =>$CustName,


                    'datecreated' =>date('Y-m-d H:i:s'),


                );


                $this->db->insert(db_prefix() . 'contacts',$ContactsArray);


            }


        }


        $sale_master_array = array(


            "PlantID" => $PlantID,


            "FY"=>$FY,


            "BT"=>'T',


            "InvoiceType"=>$cash_credit,


            "SalesID"=>$newTaxID,


            "Transdate"=>$Transdate,


            "OrderID"=>$newTaxID,


            "ChallanID"=>$newTaxID,


            "AccountID"=>$AccountID,


            "ShipTo"=>$ShipTo,


            "CenterID"=>$CenterID,


            "WHID"=>$GodownID,


            "BrokerID"=>$BrokerID,


            "gstno"=>$GstIN,


            "sale_qty"=>$total_qty_in_mt,


            "SaleAmt"=>$Total_value,


            "sgstamt"=>$total_sgst_amt,


            "cgstamt"=>$total_cgst_amt,


            "igstamt"=>$total_igst_amt,


            "BillAmt"=>$total_net_value,


            "RndAmt"=>$total_net_value,


            "ItCount"=>$ItCount,


            "UserID"=>$UserID,


            "TransportID"=>$TransporterID,


            "vehicleno"=>$VehicleNo,


            "PaymentTerm"=>$PaymentTerm,


            "LRNo"=>$LRNo


        );


        $this->db->insert(db_prefix() . 'salesmaster',$sale_master_array);


        if($this->db->affected_rows() > 0){


            $this->increment_next_tax_invoice_number();


            foreach($es_detail as $value){


                $i = 1;


                if($selected_state == "MH"){


                    $cgst_per = $value["gst"]/2;


                    $sgst_per = $value["gst"]/2;


                    $igst_per = 0;


                }else{


                    $igst_per = $value["gst"];


                    $cgst_per = 0;


                    $sgst_per = 0;


                }


                $data_array_result = array(


                    'PlantID'=>$PlantID,


                    'FY'=>$FY,


                    'OrderID' =>$newTaxID,


                    'BillID' =>$newTaxID,


                    'TransID' =>$newTaxID,


                    'TransDate2'=>date('Y-m-d H:i:s'),


                    'TransDate' =>$Transdate,


                    'TType'=>'S',


                    'TType2'=> 'Sale',


                    'AccountID'=>$AccountID,


                    'ItemID'=>$value["ItemID"],


                    'TypeID'=>"SP",


                    'CenterID'=>$CenterID,


                    'GodownID' =>$GodownID,


                    'PartyID'=>'KASPL',


                    'PurchRate'=>$value['rate']/10,


                    'SaleRate'=>$value['rate']/10,


                    'BasicRate'=>$value['rate']/10,


                    'final_rate'=>$value['rate']/10,


                    'SuppliedIn'=>1,


                    'OrderQty'=>$value['sale_qty'],


                    'BilledQty'=>$value['sale_qty'],


                    'DiscAmt'=>0.00,


                    'cgst'=>$cgst_per,


                    'sgst'=>$sgst_per,


                    'igst'=>$igst_per,


                    'cgstamt'=>$value['cgst_amt'],


                    'sgstamt'=>$value['sgst_amt'],


                    'igstamt'=>$value['igst_amt'],


                    'CaseQty'=>1,


                    'Cases'=>1,


                    'OrderAmt'=>$value['value'],


                    'ChallanAmt'=>$value['value'],


                    'NetOrderAmt'=>$value['net_amt'],


                    'NetChallanAmt'=>$value['net_amt'],


                    'cnfid' =>1,


                    'Ordinalno'=>$i,


                    'UserID'=>$_SESSION['username'],


                    'reason'=>$value['Batch']


                );


                $this->db->insert(db_prefix() . 'history',$data_array_result);


                $i++;


                $ItemID = $value["ItemID"];


            }


            


        // Ledger Entry 


            // Party Debit ledger 


            $ord_n = 1;


            $narrations = 'By Direct Sale no.'.$newTaxID;


            // Debit to Vendor


            $ledger_debit = array(


                "PlantID" => $PlantID,


                "FY" => $FY,


                "Transdate" => $Transdate,


                "TransDate2" => date('Y-m-d H:i:s'),


                "VoucherID" => $newTaxID,


                "CenterID" => $CenterID,


                "CommodityID" => $ItemID,


                "EntryFor" => '3',


                "AccountID" => $AccountID,


                "TType" => "D",


                "Amount" => $total_net_value,


                "Narration" => $narrations,


                "PassedFrom" => "SALE",


                "OrdinalNo" => $ord_n,


                "UserID" => $_SESSION['username'],


            );


            $this->db->insert(db_prefix() . 'accountledger',$ledger_debit);


            $ord_n++;


            


            // Credit to Company Account


            $ledger_credit = array(


                "PlantID" => $PlantID,


                "FY" => $FY,


                "Transdate" => $Transdate,


                "TransDate2" => date('Y-m-d H:i:s'),


                "VoucherID" => $newTaxID,


                "CenterID" => $CenterID,


                "CommodityID" => $ItemID,


                "EntryFor" => '3',


                "AccountID" => "SALE",


                "TType" => "C",


                "Amount" => $Total_value,


                "Narration" => $narrations,


                "PassedFrom" => "SALE",


                "OrdinalNo" => $ord_n,


                "UserID" => $_SESSION['username'],


            );


            $this->db->insert(db_prefix() . 'accountledger',$ledger_credit);


            $ord_n++;


            


            // GST Ledger


            if($selected_state == "MH"){


                // Credit to CGST Account


                $ledger_credit = array(


                    "PlantID" => $PlantID,


                    "FY" => $FY,


                    "Transdate" => $Transdate,


                    "TransDate2" => date('Y-m-d H:i:s'),


                    "VoucherID" => $newTaxID,


                    "CenterID" => $CenterID,


                    "CommodityID" => $ItemID,


                    "EntryFor" => '3',


                    "AccountID" => "CGST",


                    "TType" => "C",


                    "Amount" => $total_cgst_amt,


                    "Narration" => $narrations,


                    "PassedFrom" => "SALE",


                    "OrdinalNo" => $ord_n,


                    "UserID" => $_SESSION['username'],


                );


                $this->db->insert(db_prefix() . 'accountledger',$ledger_credit);


                $ord_n++;


                


                // Credit to SGST Account


                $ledger_credit = array(


                    "PlantID" => $PlantID,


                    "FY" => $FY,


                    "Transdate" => $Transdate,


                    "TransDate2" => date('Y-m-d H:i:s'),


                    "VoucherID" => $newTaxID,


                    "CenterID" => $CenterID,


                    "CommodityID" => $ItemID,


                    "EntryFor" => '3',


                    "AccountID" => "SGST",


                    "TType" => "C",


                    "Amount" => $total_sgst_amt,


                    "Narration" => $narrations,


                    "PassedFrom" => "SALE",


                    "OrdinalNo" => $ord_n,


                    "UserID" => $_SESSION['username'],


                );


                $this->db->insert(db_prefix() . 'accountledger',$ledger_credit);


                $ord_n++;


                


            }else{


                // Credit to IGST Account


                $ledger_credit = array(


                    "PlantID" => $PlantID,


                    "FY" => $FY,


                    "Transdate" => $Transdate,


                    "TransDate2" => date('Y-m-d H:i:s'),


                    "VoucherID" => $newTaxID,


                    "CenterID" => $CenterID,


                    "CommodityID" => $ItemID,


                    "EntryFor" => '3',


                    "AccountID" => "IGST",


                    "TType" => "C",


                    "Amount" => $total_igst_amt,


                    "Narration" => $narrations,


                    "PassedFrom" => "SALE",


                    "OrdinalNo" => $ord_n,


                    "UserID" => $_SESSION['username'],


                );


                $this->db->insert(db_prefix() . 'accountledger',$ledger_credit);


                $ord_n++;


            }


            


            if($cash_credit == "CASH"){


                // Receipt Ledger Entry


                $new_receipts_number = get_option('next_receipts_number_for_kirti');


                $i = 1;


                $credit_data = array(


                    "PlantID" =>$PlantID,


                    "Transdate" =>$Transdate,


                    "TransDate2" =>date('Y-m-d H:i:s'),


                    "VoucherID" =>$new_receipts_number,


                    "AccountID" =>$AccountID,


                    "TType" =>"C",


                    "CenterID" =>$CenterID,


                    "CommodityID" =>$ItemID,


                    "EntryFor" =>3,


                    "PartyID" =>"KASPL",


                    "Amount" =>$total_net_value,


                    "Narration" =>"Payment Received against ".$newTaxID ,


                    "PassedFrom" =>"RECEIPTS",


                    "OrdinalNo" =>$i,


                    "UserID" =>$this->session->userdata('username'),


                    "FY" =>$FY,


                );


                $this->db->insert(db_prefix().'accountledger', $credit_data);


                


                


                $debit_data = array(


                    "PlantID" =>$PlantID,


                    "Transdate" =>$Transdate,


                    "TransDate2" =>date('Y-m-d H:i:s'),


                    "VoucherID" =>$new_receipts_number,


                    "AccountID" =>"CASH",


                    "TType" =>"D",


                    "CenterID" =>$CenterID,


                    "CommodityID" =>$ItemID,


                    "EntryFor" =>3,


                    "PartyID" =>"KASPL",


                    "Amount" =>$total_net_value,


                    "Narration" =>"Payment Received against ".$newTaxID ,


                    "PassedFrom" =>"RECEIPTS",


                    "OrdinalNo" =>$i,


                    "UserID" =>$this->session->userdata('username'),


                    "FY" =>$FY,


                );


                $this->db->insert(db_prefix().'accountledger', $debit_data);


                $i++;


                if($i > 1){


                    $this->increment_next_receipts_number();


                }


            }


            


            return true;


        }


    }


    


    //Increment the Receipts next nubmer


    public function increment_next_receipts_number()


    {


        // Update next receipts number in settings


        $FY = $this->session->userdata('finacial_year');


        $this->db->where('name', 'next_receipts_number_for_kirti');


        $this->db->set('value', 'value+1', false);


        $this->db->WHERE('FY', $FY);


        $this->db->update(db_prefix() . 'options');


    }


    public function GetAccountDetails($AccountID)


    {


        $selected_company = $this->session->userdata('root_company');


        $fy = $this->session->userdata('finacial_year');


        $this->db->select('tblclients.AccountID,tblGstRecord.gstin');


        $this->db->join('tblGstRecord', 'tblGstRecord.AccountID = tblclients.AccountID AND tblGstRecord.IsPrimary = "1"', 'LEFT');


        $this->db->WHERE('tblclients.PlantID', $selected_company);


        $this->db->WHERE('tblclients.AccountID', $AccountID);


        return $this->db->get(db_prefix() . 'clients')->row();


    }


    public function get_next_code($name)


    {


        $this->db->select('tbloptions.*');


        $this->db->where('name', $name);


        $number_details = $this->db->get(db_prefix().'options')->row();


        return $number_details;


    }


    public function neworderplace($data, $expense = false)


    {


        /*echo "<pre>";


        print_r($data);


        die;*/


        $order_data_new = array();


        $selected_company = $this->session->userdata('root_company');


        $finacial_year = $this->session->userdata('finacial_year');


            $order_data_new["PlantID"] = $selected_company;


            $order_data_new["FY"] = $finacial_year;


            


            if($selected_company == 1){


                


                $next_order_number = get_option('next_order_number_for_cspl');


            }elseif($selected_company == 2){


                $next_order_number = get_option('next_order_number_for_cff');


            }elseif($selected_company == 3){


                $next_order_number = get_option('next_order_number_for_cbu');


            }elseif($selected_company == 4){


                $next_order_number = get_option('next_order_number_for_cbupl');


            }


            $new_orderID = "ORD".$finacial_year.str_pad($next_order_number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);


            $order_data_new["OrderID"] = $new_orderID;


            $order_data_new["AccountID"] = $data["clientid"];


            if($data["act_gst"] !== NULL && $data["act_gst"] !== ''){


                $order_data_new["GSTNO"] = $data["act_gst"];


            }


            


            if($data["istcs"] == "1"){


                $order_data_new["OrderAmt"] = $data["subtotal"] + $data["total_tax"] + $data["tcstotal"];


            }else{


                $order_data_new["OrderAmt"] = $data["subtotal"] + $data["total_tax"];


            }


            


            $order_data_new["Crates"] = $data["total_crates"];


            $order_data_new["Cases"] = $data["total_cases"];


            $order_data_new["OrderStatus"] = 'O';


            $order_data_new["OrderType"] = $data["taxes1"];


            $order_data_new["order_type"] = $data["order_type"];


            if($data["act_code"] == ""){


                $order_data_new["AccountID2"] = $data["clientid"];


            $order_data_new["Gstin2"] = $data["act_gst"];


            }else{


                $order_data_new["AccountID2"] = $data["act_code"];


            $order_data_new["Gstin2"] = $data["act2_gst_no"];


            }


            $order_data_new["Transdate"] = to_sql_date($data['date1'])." ".date("H:i:s");


            $order_data_new['UserID'] = $this->session->userdata('username');


            $order_data_new['cnfid'] = 1;


            $client_state = $data["customer_state_id"];


            //echo $client_state;


            $Transdate = to_sql_date($data['date1'])." ".date("H:i:s");


            


            $items = $data['newitems'];


            


            $this->db->insert(db_prefix() . 'ordermaster', $order_data_new);


            


            foreach ($items as $key => $item) {


                


                $OrderAmt = $item["pack_qty"] * $item["qty"] * $item["rate"];


                $gstamt = $OrderAmt * $item["taxrate1"] /100;


                $peritemgst = $item["rate"] * $item["taxrate1"] /100;


                $NetOrderAmt = $OrderAmt + $gstamt;


                $item_data = array();


                $item_data["PlantID"] = $selected_company;


                $item_data["FY"] = $this->session->userdata('finacial_year');


                $item_data["OrderID"] = $new_orderID;


                $item_data["TType"] = "O";


                $item_data["TType2"] = 'Order';


                $item_data["AccountID"] = $data["clientid"];


                $item_data["ItemID"] = $item["item_code1"];


                //$item_data["description"] = $item["description"];


                //$item_data["hsn_code"] = $item["hsn_code"];


                $item_data["BasicRate"] = $item["rate"];


                $item_data["SuppliedIn"] = $item["items_cs_cr"];


                $item_data["OrderQty"] = $item["pack_qty"] * $item["qty"];


                $item_data["SaleRate"] = $item["rate"] + $peritemgst;


                $item_data["DiscAmt"] = $item["dis_amt1"];


                if($client_state == "UP"){


                    $temp_gst = $item["taxrate1"] /2;


                    $item_data["cgst"] = $temp_gst;


                    $item_data["cgstamt"] = $gstamt /2;


                    $item_data["sgst"] = $temp_gst;


                    $item_data["sgstamt"] = $gstamt /2;


                }else {


                    $item_data["igst"]= $item["taxrate1"];


                    $item_data["igstamt"]= $gstamt;


                }


                $item_data["CaseQty"] = $item["pack_qty"];


                $item_data["OrderAmt"] = $OrderAmt;


                $item_data["NetOrderAmt"] = $NetOrderAmt;


                $item_data["Ordinalno"] = $item["order"];


                $item_data["UserID"] = $this->session->userdata('username');


                $item_data["TransDate"] = $Transdate;


               


                $this->db->insert(db_prefix().'history', $item_data);


                


            }


            //die;


            $this->increment_next_number();


            return $order_data_new["OrderID"];


        //die;


    }


    





    





    /**


     * Update invoice data


     * @param  array $data invoice data


     * @param  mixed $id   invoiceid


     * @return boolean


     */


    public function update($data, $id)


    {


        


        $selected_company = $this->session->userdata('root_company');


        if($selected_company == "1"){


            $GodownID = 'CSPL';


        }else if($selected_company == "2"){


            $GodownID = 'CFF';


        }else if($selected_company == "3"){


            $GodownID = 'CBUPL';


        }


        $fy = $this->session->userdata('finacial_year');


        $exiteditems = $data["items"]; 


        $newitems = $data["newitems"]; 


        $OrderDetails = $this->GetOrderDetails($id);


        $itemDetails = $this->GetItemDetails($id);


        $orderAmt = $OrderDetails->OrderAmt;


        $challanAmt = $OrderDetails->ChallanAmt;


        $Ocrates = $OrderDetails->Crates;


        $Ocases = $OrderDetails->Cases;


        $Ccrates = $OrderDetails->CCrates;


        $Ccases = $OrderDetails->CCases;


        $NewChallanAmt = $challanAmt - $orderAmt;


        $newCrates = $Ccrates - $Ocrates;


        $newCases = $Ccases - $Ocases;


        $exItemCount = count($exiteditems);


        $newCount = $exItemCount + 1;


    //if($selected_company !== "1"){


        foreach ($exiteditems as $exkey => $exvalue) {


            $PQty = 0;


            $PRQty = 0;


            $IQty = 0;


            $PRDQty = 0;


            $SQty = 0;


            $SRQty = 0;


            $ADJQTY = 0;


            $GIQty = 0;


            $GOQty = 0;


            $balance = 0;


            $checkStock = $this->CheckStockQty($exvalue['item_code1']);


            $checkStockDetails = $this->getStocksDetails($exvalue['item_code1']);


            


            foreach ($checkStockDetails as $stock) {


                if($stock['TType'] == 'P'){


                    $PQty = $stock['BilledQty'];


                }elseif($stock['TType'] == 'N'){


                    $PRQty = $stock['BilledQty'];


                }elseif($stock['TType'] == 'A'){


                    $IQty = $stock['BilledQty'];


                }elseif($stock['TType'] == 'B'){


                    $PRDQty = $stock['BilledQty'];


                }elseif($stock['TType'] == 'O' && $stock['TType2'] == 'Order'){


                    $SQty = $stock['BilledQty'];


                }elseif($stock['TType'] == 'R' && $stock['TType2'] == 'Fresh'){


                    $SRQty = $stock['BilledQty'];


                }elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Stock Adjustment'){


                    $ADJQTY += $stock['BilledQty'];


                }elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Promotional Activity'){


                    $ADJQTY += $stock['BilledQty'];


                }elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Free distribution'){


                    $ADJQTY += $stock['BilledQty'];


                }elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Free Distribution'){


                    $ADJQTY += $stock['BilledQty'];


                }elseif($stock['TType'] == 'T' && $stock['TType2'] == 'In'){


                        $GIQty = $stock['BilledQty'];


                    }elseif($stock['TType'] == 'T' && $stock['TType2'] == 'Out'){


                        $GOQty = $stock['BilledQty'];


                    }


            }


            if($OrderDetails->SalesID !== NULL){


                foreach ($itemDetails as $Key => $Value) {


                    if($Value['ItemID']==$exvalue['item_code1']){


                        $balance = $Value['BilledQty'];


                        $TransDate2 = $Value['TransDate2'];


                    }


                }


            }


            $balance = (float) $balance + (float) $checkStock->OQty + (float) $PQty - (float) $PRQty - (float) $IQty +  (float) $PRDQty - (float) $SQty + (float) $SRQty - (float) $ADJQTY - (float) $GOQty +  (float) $GIQty;


            $balCase = $balance / $exvalue['pack_qty'];


            //$orderQty = $exvalue['qty'] * $exvalue['pack_qty'];


            if((float) $balCase <= (float) $exvalue['qty'] && $exvalue['qty'] !== '0'){


                //return false;


                set_alert('warning', $exvalue['item_code1'].'- '.$balCase.' - '.$exvalue['qty']);


                    redirect(admin_url('order/order/' . $id));


            }


        }


        


        foreach ($newitems as $newkey => $newvalue) {


            $PQty = 0;


            $PRQty = 0;


            $IQty = 0;


            $PRDQty = 0;


            $SQty = 0;


            $SRQty = 0;


            $ADJQTY = 0;


            $GOQty = 0;


            $GIQty = 0;


            $checkStock = $this->CheckStockQty($newvalue["item_code1"]);


            $checkStockDetails = $this->getStocksDetails($newvalue['item_code1']);


            


            foreach ($checkStockDetails as $stock) {


                if($stock['TType'] == 'P'){


                    $PQty = $stock['BilledQty'];


                }elseif($stock['TType'] == 'N'){


                    $PRQty = $stock['BilledQty'];


                }elseif($stock['TType'] == 'A'){


                    $IQty = $stock['BilledQty'];


                }elseif($stock['TType'] == 'B'){


                    $PRDQty = $stock['BilledQty'];


                }elseif($stock['TType'] == 'O' && $stock['TType2'] == 'Order'){


                    $SQty = $stock['BilledQty'];


                }elseif($stock['TType'] == 'R' && $stock['TType2'] == 'Fresh'){


                    $SRQty = $stock['BilledQty'];


                }elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Stock Adjustment'){


                    $ADJQTY += $stock['BilledQty'];


                }elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Promotional Activity'){


                    $ADJQTY += $stock['BilledQty'];


                }elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Free Distribution'){


                    $ADJQTY += $stock['BilledQty'];


                }elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Free distribution'){


                    $ADJQTY += $stock['BilledQty'];


                }elseif($stock['TType'] == 'T' && $stock['TType2'] == 'In'){


                        $GIQty = $stock['BilledQty'];


                    }elseif($stock['TType'] == 'T' && $stock['TType2'] == 'Out'){


                        $GOQty = $stock['BilledQty'];


                    }


            }


            


            $balance = (float) $checkStock->OQty + (float) $PQty - (float) $PRQty - (float) $IQty +  (float) $PRDQty - (float) $SQty + (float) $SRQty - (float) $ADJQTY - (float) $GOQty + (float) $GIQty;


            $balCase = $balance / $newvalue['pack_qty'];


            //$orderQty = $newvalue['qty'] * $newvalue['pack_qty'];


            if((float) $balCase <= (float) $newvalue['qty'] && $newvalue['qty'] !== '0'){


                set_alert('warning', $newvalue['item_code1'].'- '.$balCase);


                    redirect(admin_url('order/order/' . $id));


            }


        }


    //}


    


        // Stock revart


        if($OrderDetails->SalesID !== NULL){


            foreach ($itemDetails as $Key => $Value) {


                $newStock = $Value["SQty"] - $Value['BilledQty'];


                $TransDate2 = $Value['TransDate2'];


                $dataS = array(


                    'SQty'=>$newStock,


                    );


                $this->db->where('PlantID', $selected_company);


                $this->db->where('FY', $fy);


                $this->db->where('ItemID', $Value['ItemID']);


                $this->db->where('GodownID',$GodownID);


                $this->db->update(db_prefix() . 'stockmaster', $dataS);


            }


        }


        


        


        if($data["istcs"] == "1"){


            $order_total = $data["subtotal"] + $data["total_tax"] + $data["tcstotal"];


        }else{


            $order_total = $data["subtotal"] + $data["total_tax"];


        }


        if($data["act_code"] == ""){


            $accountID2 = $data["cust_id"];


            $Gstin2 = $data["act_gst"];


        }else{


            $accountID2 = $data["act_code"];


            $Gstin2 = $data["act2_gst_no"];


        }


        


        $TCS = $data["tcstotal"];


        $saleAmt = $data["subtotal"];


        $total_cases = $data["total_cases"];


        $total_crates = $data["total_crates"];


        $newCrates = $newCrates + $total_crates;


        $newCases = $newCases + $total_cases;


        if($data["customer_state_id"] == "UP"){


            $cgstAmt = $data["total_tax"] / 2;


            $sgstAmt = $data["total_tax"] / 2;


            $igstAmt = 0.00;


        }else{


            $cgstAmt = 0.00;


            $sgstAmt = 0.00;


            $igstAmt = $data["total_tax"];


        }


        $BillAmtF = $order_total;


        $NewChallanAmt2 = $NewChallanAmt + $BillAmtF;


        $RndAmt = round($order_total,2);


        $round_variation = $BillAmtF - $RndAmt;


        $order_total = round($order_total,2);


        


        $order_data = array(


            "OrderAmt" =>$order_total,


            "AccountID2" =>$accountID2,


            "Gstin2" =>$Gstin2,


            "Crates" =>$data["total_crates"],


            "Cases" =>$data["total_cases"],


            "UserID2" =>$this->session->userdata('username'),


            "Lupdate" =>date('Y-m-d H:i:s'),


            "isEdited" =>1,


            );


            


        /*echo "<pre>";


        //print_r($data);


        echo  $TransDate2;


        print_r($order_data);


        die;*/


        //update order table


        $this->db->where('PlantID', $selected_company);


            $this->db->where('FY', $fy);


            $this->db->where('OrderID', $id);


            $this->db->update(db_prefix() . 'ordermaster', $order_data);


            


        //update exiting items    


       


        


        unset($data["items"]);


            foreach ($exiteditems as $exkey => $exvalue) {


            


            $exOrderAmt = $exvalue["pack_qty"] * $exvalue["qty"] * $exvalue["rate"];


                $exgstamt = $exOrderAmt * $exvalue["taxrate1"] /100;


                $experitemgst = $exvalue["rate"] * $exvalue["taxrate1"] /100;


                $exNetOrderAmt = $exOrderAmt + $exgstamt;


                $exsalerate = $exvalue["rate"] + $experitemgst;


                


                if(empty($exvalue["ereason"])){


                    $lupdate = "";


                    $UserID2 = "";


                }else {


                    $lupdate = date('Y-m-d H:i:s');


                    $UserID2 = $this->session->userdata('username');


                }


                


            if($data["customer_state_id"] == "UP"){


                    $extemp_gst = $exvalue["taxrate1"] /2;


                    $excgst = $extemp_gst;


                    $excgstamt = $exgstamt /2;


                    $exsgst = $extemp_gst;


                    $exsgstamt = $exgstamt /2;


                }else {


                    $exigst= $exvalue["taxrate1"];


                    $exigstamt= $exgstamt;


                }


                


                if($OrderDetails->SalesID !== NULL){


                $updated_item_data = array(


                    "eOrderQty" =>$exvalue["pack_qty"] * $exvalue["qty"],


                    "ereason" => $exvalue["ereason"],


                    "BilledQty" =>$exvalue["pack_qty"] * $exvalue["qty"],


                    "GodownID" =>$GodownID,


                    "cgst" =>$excgst,


                    "cgstamt" =>$excgstamt,


                    "sgst" =>$exsgst,


                    "sgstamt" =>$exsgstamt,


                    "igst" =>$exigst,


                    "igstamt" =>$exigstamt,


                    "CaseQty" =>$exvalue["pack_qty"],


                    "ChallanAmt" =>$exOrderAmt,


                    "NetChallanAmt" =>$exNetOrderAmt,


                    "OrderAmt" =>$exOrderAmt,


                    "NetOrderAmt" =>$exNetOrderAmt,


                    "UserID2" =>$UserID2,


                    "Lupdate" =>$lupdate,


                );


            }else{


                $updated_item_data = array(


                    "eOrderQty" =>$exvalue["pack_qty"] * $exvalue["qty"],


                    "ereason" => $exvalue["ereason"],


                    "cgst" =>$excgst,


                    "cgstamt" =>$excgstamt,


                    "sgst" =>$exsgst,


                    "sgstamt" =>$exsgstamt,


                    "igst" =>$exigst,


                    "igstamt" =>$exigstamt,


                    "CaseQty" =>$exvalue["pack_qty"],


                    "OrderAmt" =>$exOrderAmt,


                    "NetOrderAmt" =>$exNetOrderAmt,


                    "UserID2" =>$UserID2,


                    "Lupdate" =>$lupdate,


                );


            } 


            $this->db->where('PlantID', $selected_company);


            $this->db->where('FY', $fy);


            $this->db->where('id', $exvalue["itemid"]);


            $this->db->where('OrderID', $id);


            $this->db->update(db_prefix() . 'history', $updated_item_data);


        }  


            


     //die;


        // Insert new Items in exiting order


        


        unset($data["newitems"]);


            foreach ($newitems as $newkey => $newvalue) {


                


                $OrderAmt = $newvalue["pack_qty"] * $newvalue["qty"] * $newvalue["rate"];


                $gstamt = $OrderAmt * $newvalue["taxrate1"] /100;


                $peritemgst = $newvalue["rate"] * $newvalue["taxrate1"] /100;


                $NetOrderAmt = $OrderAmt + $gstamt;


                $salerate = $newvalue["rate"] + $peritemgst;


                


                if($data["customer_state_id"] == "UP"){


                    $temp_gst = $newvalue["taxrate1"] /2;


                    $cgst = $temp_gst;


                    $cgstamt = $gstamt /2;


                    $sgst = $temp_gst;


                    $sgstamt = $gstamt /2;


                }else {


                    $igst= $newvalue["taxrate1"];


                    $igstamt= $gstamt;


                }


                


        if($OrderDetails->SalesID !== NULL){


            


            $new_item_data = array(


                "PlantID" =>$selected_company,


                "FY" =>$fy,


                "OrderID" =>$id,


                "TType" =>"O",


                "TType2" =>'Order',


                "BillID"=>$OrderDetails->ChallanID,


                "TransID"=>$OrderDetails->SalesID,


                "GodownID" =>$GodownID,


                "TransDate2"=>$TransDate2,


                "AccountID" =>$data["cust_id"],


                "GodownID" =>$GodownID,


                "ItemID" =>$newvalue["item_code1"],


                "BasicRate" =>$newvalue["rate"],


                "SuppliedIn" =>$newvalue["items_cs_cr"],


                "eOrderQty" =>$newvalue["pack_qty"] * $newvalue["qty"],


                "BilledQty" =>$newvalue["pack_qty"] * $newvalue["qty"],


                "SaleRate" =>$salerate,


                "DiscAmt" =>$newvalue["dis_amt1"],


                "cgst" =>$cgst,


                "cgstamt" =>$cgstamt,


                "sgst" =>$sgst,


                "sgstamt" =>$sgstamt,


                "igst" =>$igst,


                "igstamt" =>$igstamt,


                "CaseQty" =>$newvalue["pack_qty"],


                "OrderAmt" =>$OrderAmt,


                "NetOrderAmt" =>$NetOrderAmt,


                "ChallanAmt" =>$OrderAmt,


                "NetChallanAmt" =>$NetOrderAmt,


                "Ordinalno" =>$newCount,


                "UserID" =>$this->session->userdata('username'),


                "TransDate" =>date('Y-m-d H:i:s'),


            );


            $newCount++;


        }else{


            $new_item_data = array(


                "PlantID" =>$selected_company,


                "FY" =>$fy,


                "OrderID" =>$id,


                "TType" =>"O",


                "TType2" =>'Order',


                "AccountID" =>$data["cust_id"],


                "GodownID" =>$GodownID,


                "ItemID" =>$newvalue["item_code1"],


                "BasicRate" =>$newvalue["rate"],


                "SuppliedIn" =>$newvalue["items_cs_cr"],


                "eOrderQty" =>$newvalue["pack_qty"] * $newvalue["qty"],


                "SaleRate" =>$salerate,


                "DiscAmt" =>$newvalue["dis_amt1"],


                "cgst" =>$cgst,


                "cgstamt" =>$cgstamt,


                "sgst" =>$sgst,


                "sgstamt" =>$sgstamt,


                "igst" =>$igst,


                "igstamt" =>$igstamt,


                "CaseQty" =>$newvalue["pack_qty"],


                "OrderAmt" =>$OrderAmt,


                "NetOrderAmt" =>$NetOrderAmt,


                "Ordinalno" =>$newCount,


                "UserID" =>$this->session->userdata('username'),


                "TransDate" =>date('Y-m-d H:i:s'),


            );


            $newCount++;


        } 


                


        $this->db->insert(db_prefix() . 'history', $new_item_data);


        //print_r($new_item_data);


    }


    $itemDetails = $this->GetItemDetails($id);


    if($OrderDetails->SalesID !== NULL){


    // Stock Update


        foreach ($itemDetails as $Key => $Value) {


            $newStock = $Value["SQty"] + $Value['BilledQty'];


            $dataS = array(


                'SQty'=>$newStock,


                );


            $this->db->where('PlantID', $selected_company);


            $this->db->where('FY', $fy);


            $this->db->where('ItemID', $Value['ItemID']);


            $this->db->where('GodownID',$GodownID);


            $this->db->update(db_prefix() . 'stockmaster', $dataS);


        }


    } 


    if($OrderDetails->SalesID !== NULL){


            


            $this->db->where('PlantID', $selected_company);


            $this->db->LIKE('FY', $fy);


            $this->db->where('ChallanID', $OrderDetails->ChallanID);


            $this->db->update(db_prefix() . 'challanmaster', [


                                'ChallanAmt' =>$NewChallanAmt2,


                                'Crates' =>$newCrates,


                                'Cases' =>$newCases,


                                'UserID2' =>$this->session->userdata('username'),


                                'Lupdate' =>date('Y-m-d H:i:s'),


                            ]);


                                        


            $Oldmonth = substr($data["SalesDate"],5,2);


            if($Oldmonth == "01"){


                $mOld = 11; 


            }


            if($Oldmonth == "02"){


                $mOld = 12; 


            }


            if($Oldmonth == "03"){


                $mOld = 13; 


            }


            if($Oldmonth == "04"){


                $mOld = 2; 


            }


            if($Oldmonth == "05"){


                $mOld = 3; 


            }


            if($Oldmonth == "06"){


                $mOld = 4; 


            }


            if($Oldmonth == "07"){


                $mOld = 5; 


            }


            if($Oldmonth == "08"){


                $mOld = 6; 


            }


            if($Oldmonth == "09"){


                $mOld = 7; 


            }


            if($Oldmonth == "10"){


                $mOld = 8; 


            }


            if($Oldmonth == "11"){


                $mOld = 9; 


            }


            if($Oldmonth == "12"){


                $mOld = 10; 


            }


            $mmOld = "BAL".$mOld;


            // Exiting order in Challan


                    // Balance revert and ledger delete for SALE 


                    $ledgerDetails = $this->get_ledgerDetails($OrderDetails->SalesID);


                    foreach ($ledgerDetails as $key => $value) {


                        $acc_bal_data = $this->get_acc_bal($value["AccountID"]);


                        if($value['TType']=="C"){


                            $newBal = $acc_bal_data->$mmOld + $value['Amount'];


                        }else{


                            $newBal = $acc_bal_data->$mmOld - $value['Amount'];


                        }


                        $this->db->where('PlantID', $selected_company);


                        $this->db->LIKE('FY', $fy);


                        $this->db->where('AccountID', $value["AccountID"]);


                        $this->db->update(db_prefix() . 'accountbalances', [


                                            $mmOld => $newBal,


                                        ]);


                    }


                    


                    $this->db->where('PlantID', $selected_company);


                    $this->db->LIKE('FY', $fy);


                    $this->db->where('VoucherID', $OrderDetails->SalesID);


                    $this->db->delete(db_prefix() . 'accountledger');


                    


                // update Sales Master table


                        $salesdataUpdate =array(


                            "tcsAmt"=>$TCS,


                            "SaleAmt"=>$saleAmt,


                            "sgstamt"=>$sgstAmt,


                            "cgstamt"=>$cgstAmt,


                            "igstamt"=>$igstAmt,


                            "BillAmt"=>$BillAmtF,


                            "RndAmt"=>$RndAmt,


                            "UserID2"=>$this->session->userdata('username'),


                            "Lupdate"=>date('Y-m-d H:i:s')


                        );


                    $this->db->where('PlantID', $selected_company);


                    $this->db->LIKE('FY', $fy);


                    $this->db->where('SalesID', $data["SalesID"]);


                    $this->db->update(db_prefix() . 'salesmaster', $salesdataUpdate);


                    


                    // update Crates 


                    $getCratesDetails = $this->getCratesDetails($OrderDetails->SalesID);


                    $create_ledgerdata = array(


                            "Qty"=>$data["total_crates"],


                            "UserID2"=>$this->session->userdata('username'),


                            "Lupdate"=>date('Y-m-d H:i:s')


                        );


                    $this->db->where('PlantID', $selected_company);


                    $this->db->LIKE('FY', $fy);


                    $this->db->where('VoucherID', $data["SalesID"]);


                    $this->db->update(db_prefix() . 'accountcrates', $create_ledgerdata);


                    


                    


                    $narration = "By SalesID ".$OrderDetails->SalesID."/".$data["ChallanID"]; 


                    $narration_tcs = "TCS@0.1000% on SalesID ".$OrderDetails->SalesID."/".$data["ChallanID"];


                    


                // new Create ledger and update Account balance


                        $ledgerdata_credit=array(


                            "PlantID"=>$selected_company,


                            "FY"=>$fy,


                            "Transdate"=>$data["SalesDate"],


                            "TransDate2"=>date('Y-m-d H:i:s'),


                            "VoucherID"=>$data["SalesID"],


                            "AccountID"=>"SALE",


                            "TType"=>"C",


                            "Amount"=>$saleAmt,


                            "Narration"=>$narration,


                            "PassedFrom"=>"SALE",


                            "OrdinalNo"=>1,


                            "UserID"=>$this->session->userdata('username')


                        );


                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit);


                // CGST,SGST and IGST ledger insert


                    if($igstAmt == "0.00" || $igstAmt == ''){


                        $acct_name1 = "SGST";


                        $acct_name2 = "CGST";


                        $ledgerdata_credit_sgst=array(


                            "PlantID"=>$selected_company,


                            "FY"=>$fy,


                            "Transdate"=>$data["SalesDate"],


                            "TransDate2"=>date('Y-m-d H:i:s'),


                            "VoucherID"=>$OrderDetails->SalesID,


                            "AccountID"=>$acct_name1,


                            "TType"=>"C",


                            "Amount"=>$sgstAmt,


                            "Narration"=>$narration,


                            "PassedFrom"=>"SALE",


                            "OrdinalNo"=>1,


                            "UserID"=>$this->session->userdata('username')


                        );


                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_sgst);


                        $ledgerdata_credit_cgst=array(


                            "PlantID"=>$selected_company,


                            "FY"=>$fy,


                            "Transdate"=>$data["SalesDate"],


                            "TransDate2"=>date('Y-m-d H:i:s'),


                            "VoucherID"=>$OrderDetails->SalesID,


                            "AccountID"=>$acct_name2,


                            "TType"=>"C",


                            "Amount"=>$cgstAmt,


                            "Narration"=>$narration,


                            "PassedFrom"=>"SALE",


                            "OrdinalNo"=>1,


                            "UserID"=>$this->session->userdata('username')


                        );


                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_cgst);


                    }else{


                        $acct_name3 = "IGST";


                        $ledgerdata_credit_igst=array(


                            "PlantID"=>$selected_company,


                            "FY"=>$fy,


                            "Transdate"=>$data["SalesDate"],


                            "TransDate2"=>date('Y-m-d H:i:s'),


                            "VoucherID"=>$OrderDetails->SalesID,


                            "AccountID"=>$acct_name3,


                            "TType"=>"C",


                            "Amount"=>$igstAmt,


                            "Narration"=>$narration,


                            "PassedFrom"=>"SALE",


                            "OrdinalNo"=>1,


                            "UserID"=>$this->session->userdata('username')


                        );


                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_igst);


                    }


                // Party account ledger insert


                        $ledgerdata_debit=array(


                            "PlantID"=>$selected_company,


                            "FY"=>$fy,


                            "Transdate"=>$data["SalesDate"],


                            "TransDate2"=>date('Y-m-d H:i:s'),


                            "VoucherID"=>$OrderDetails->SalesID,


                            "AccountID"=>$data["cust_id"],


                            "TType"=>"D",


                            "Amount"=>$RndAmt,


                            "Narration"=>$narration,


                            "PassedFrom"=>"SALE",


                            "OrdinalNo"=>1,


                            "UserID"=>$this->session->userdata('username')


                        );


                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_debit);


                // TCS Ledger insert


                    if($round_variation >=0){


                        $rTType = "C";


                        $round_variation_new = abs($round_variation);


                    }else{


                        $rTType = "D";


                        $round_variation_new = abs($round_variation);


                    }


                        $ledgerdata_roundoff =array(


                            "PlantID"=>$selected_company,


                            "FY"=>$fy,


                            "Transdate"=>$data["SalesDate"],


                            "TransDate2"=>date('Y-m-d H:i:s'),


                            "VoucherID"=>$OrderDetails->SalesID,


                            "AccountID"=>"ROUNDOFF",


                            "TType"=>$rTType,


                            "Amount"=>$round_variation_new,


                            "Narration"=>$narration,


                            "PassedFrom"=>"SALE",


                            "OrdinalNo"=>1,


                            "UserID"=>$this->session->userdata('username')


                        );


                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_roundoff);


                // TCS ledger insert


                    if($data["istcs"] == "1"){


                        $ledgerdata_tcs=array(


                            "PlantID"=>$selected_company,


                            "FY"=>$fy,


                            "Transdate"=>$data["SalesDate"],


                            "TransDate2"=>date('Y-m-d H:i:s'),


                            "VoucherID"=>$OrderDetails->SalesID,


                            "AccountID"=>"TCS",


                            "TType"=>"C",


                            "Amount"=>$TCS,


                            "Narration"=>$narration_tcs,


                            "PassedFrom"=>"SALE",


                            "OrdinalNo"=>1,


                            "UserID"=>$this->session->userdata('username'),


                        );


                        $this->db->insert(db_prefix() . 'accountledger', $ledgerdata_tcs);


                    }


                // get Account Balances


                    $acc_bal_data = $this->get_acc_bal($data["cust_id"]);


                    $acc_bal_sale = $this->get_acc_bal("SALE");


                    $acc_bal_tcs = $this->get_acc_bal("TCS");


                    $acc_bal_igst = $this->get_acc_bal("IGST");


                    $acc_bal_sgst = $this->get_acc_bal("SGST");


                    $acc_bal_cgst = $this->get_acc_bal("CGST");


                    $acc_bal_roundoff = $this->get_acc_bal("ROUNDOFF");


                    


                    


                // Debit customer account balance


                    $current_bal = $acc_bal_data->$mOld;


                    $current_bal_total = $current_bal + $RndAmt;


                


                    $this->db->where('PlantID', $selected_company);


                    $this->db->LIKE('FY', $fy);


                    $this->db->where('AccountID', $data["cust_id"]);


                    $this->db->update(db_prefix() . 'accountbalances', [


                                        $mOld => $current_bal_total,


                                    ]);


                // Credit Sale Account balance


                    $current_bal_for_sale_act = $acc_bal_sale->$mOld;


                    $total_bal_for_sale_act = $current_bal_for_sale_act - $saleAmt;


                


                    $this->db->where('PlantID', $selected_company);


                    $this->db->LIKE('FY', $fy);


                    $this->db->where('AccountID', "SALE");


                    $this->db->update(db_prefix() . 'accountbalances', [


                                        $mOld => $total_bal_for_sale_act,


                                    ]);    


                // credit tcs account balance


                    if($data["istcs"] == "1"){


                        $current_bal_for_tcs_act = $acc_bal_tcs->$mOld;


                        $total_bal_for_tcs_act = $current_bal_for_tcs_act - $TCS;


                        


                        $this->db->where('PlantID', $selected_company);


                        $this->db->LIKE('FY', $fy);


                        $this->db->where('AccountID', "TCS");


                        $this->db->update(db_prefix() . 'accountbalances', [


                                            $mOld => $total_bal_for_tcs_act,


                                        ]);


                    }


                // credit Roundoff account balance


                        $current_bal_for_roundoff_act = $acc_bal_roundoff->$mOld;


                        $total_bal_for_roundoff_act = $current_bal_for_roundoff_act - $round_variation;


                        


                        $this->db->where('PlantID', $selected_company);


                        $this->db->LIKE('FY', $fy);


                        $this->db->where('AccountID', "ROUNDOFF");


                        $this->db->update(db_prefix() . 'accountbalances', [


                                            $mOld => $total_bal_for_roundoff_act,


                                        ]);


                // CGST,SGST and IGST balances update


                    if($igstAmt == "0.00" || $igstAmt == ''){


                        // ADD SGST Amount


                        $sgst_bal_total = $acc_bal_sgst->$mOld - $sgstAmt;


                        


                        $this->db->where('PlantID', $selected_company);


                        $this->db->LIKE('FY', $fy);


                        $this->db->where('AccountID', "SGST");


                        $this->db->update(db_prefix() . 'accountbalances', [


                                            $mOld => $sgst_bal_total,


                                        ]);


                        // Add CGST Amount


                        $cgst_bal_total = $acc_bal_cgst->$mOld - $cgstAmt;


                    


                        $this->db->where('PlantID', $selected_company);


                        $this->db->LIKE('FY', $fy);


                        $this->db->where('AccountID', "CGST");


                        $this->db->update(db_prefix() . 'accountbalances', [


                                            $mOld => $cgst_bal_total,


                                        ]);


                    }else {


                    // Add IGST Amount


                        $igst_bal_total = $acc_bal_igst->$mOld - $igstAmt;


                            


                        $this->db->where('PlantID', $selected_company);


                        $this->db->LIKE('FY', $fy);


                        $this->db->where('AccountID', "IGST");


                        $this->db->update(db_prefix() . 'accountbalances', [


                                            $mOld => $igst_bal_total,


                                        ]);


                    }


    }


    //die;


    // Delete item in exiting order


        $remove_item = $data["removed_items"];


        foreach ($remove_item as $deletedvalue) {


            


            $this->db->where('id', $deletedvalue);


            $this->db->delete(db_prefix() . 'history');


        }


    return $id;





    }





    public function CheckStockQty($ItemID)


    {


        $selected_company = $this->session->userdata('root_company');


        if($selected_company == "1"){


            $GodownID = 'CSPL';


        }else if($selected_company == "2"){


            $GodownID = 'CFF';


        }else if($selected_company == "3"){


            $GodownID = 'CBUPL';


        }


        $fy = $this->session->userdata('finacial_year');


        


        $this->db->select('*');


		$this->db->from(db_prefix() . 'stockmaster');


		$this->db->where(db_prefix() . 'stockmaster.PlantID', $selected_company);


		$this->db->where(db_prefix() . 'stockmaster.FY', $fy);


		$this->db->where('GodownID',$GodownID);


		$this->db->where(db_prefix() . 'stockmaster.ItemID ', $ItemID);


		return $this->db->get()->row();


    }


    public function getCratesDetails($SalesID)


    {


        $selected_company = $this->session->userdata('root_company');


        $fy = $this->session->userdata('finacial_year');


        $this->db->select(db_prefix() .'accountcrates.*');


        $this->db->from(db_prefix() .'accountcrates');


        $this->db->where(db_prefix() .'accountcrates.VoucherID', $SalesID);


        $this->db->where(db_prefix() .'accountcrates.PlantID', $selected_company);


        $this->db->where(db_prefix() .'accountcrates.FY', $fy);


        return $this->db->get()->row();


    }


    public function get_ledgerDetails($SalesID)


    {


        $selected_company = $this->session->userdata('root_company');


        $fy = $this->session->userdata('finacial_year');


        $this->db->select(db_prefix() .'accountledger.*');


        $this->db->from(db_prefix() .'accountledger');


        $this->db->where(db_prefix() .'accountledger.VoucherID', $SalesID);


        $this->db->where(db_prefix() .'accountledger.PlantID', $selected_company);


        $this->db->where(db_prefix() .'accountledger.FY', $fy);


        return $this->db->get()->result_array();


    }


    public function get_acc_bal($id)


    {


        $selected_company = $this->session->userdata('root_company');


        $fy = $this->session->userdata('finacial_year');


        $this->db->where('PlantID', $selected_company);


        $this->db->LIKE('FY', $fy);


        $this->db->WHERE('AccountID', $id);





        return $this->db->get(db_prefix() . 'accountbalances')->row();


    }


    function getStocksDetails($id){


        $fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        $this->db->select('ItemID,TType,TType2,SUM(BilledQty) AS BilledQty');


        $this->db->from(db_prefix() .'history');


        $this->db->where(db_prefix() .'history.PlantID', $selected_company);


        $this->db->where(db_prefix() . 'history.ItemID ', $id);


        $this->db->where(db_prefix() . 'history.BillID IS NOT NULL', NULL, FALSE);


        $this->db->where(db_prefix() .'history.FY', $fy);


        $this->db->group_by('ItemID,TType,TType2');


        return $this->db->get()->result_array();


    }


    


    public function GetOrderDetails($OrderID)


    {


        $selected_company = $this->session->userdata('root_company');


        $fy = $this->session->userdata('finacial_year');


        


        $this->db->select(db_prefix() . 'ordermaster.OrderAmt,'.db_prefix() . 'ordermaster.Crates,'.db_prefix() . 'ordermaster.Cases,'.db_prefix() . 'challanmaster.Crates AS CCrates,'.db_prefix() . 'challanmaster.Cases AS CCases,'.db_prefix() . 'ordermaster.SalesID,'.db_prefix() . 'salesmaster.ChallanID,'.db_prefix() . 'salesmaster.cgstamt,'.db_prefix() . 'salesmaster.igstamt,'.db_prefix() . 'salesmaster.BillAmt,'.db_prefix() . 'salesmaster.SaleAmt,'.db_prefix() . 'challanmaster.ChallanAmt');


		$this->db->from(db_prefix() . 'ordermaster');


		$this->db->join(db_prefix() . 'salesmaster', '' . db_prefix() . 'salesmaster.SalesID = ' . db_prefix() . 'ordermaster.SalesID AND ' . db_prefix() . 'salesmaster.PlantID = ' . db_prefix() . 'ordermaster.PlantID AND ' . db_prefix() . 'salesmaster.FY = ' . db_prefix() . 'ordermaster.FY','LEFT');


		$this->db->join(db_prefix() . 'challanmaster', '' . db_prefix() . 'challanmaster.ChallanID = ' . db_prefix() . 'ordermaster.ChallanID AND ' . db_prefix() . 'challanmaster.PlantID = ' . db_prefix() . 'ordermaster.PlantID AND ' . db_prefix() . 'challanmaster.FY = ' . db_prefix() . 'ordermaster.FY','LEFT');


		$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);


		$this->db->where(db_prefix() . 'ordermaster.FY', $fy);


		$this->db->where(db_prefix() . 'ordermaster.OrderID ', $OrderID);


		return $this->db->get()->row();


    }


    


    public function GetItemDetails($OrderID)


    {


        $selected_company = $this->session->userdata('root_company');


        $fy = $this->session->userdata('finacial_year');


        if($selected_company == "1"){


            $GodownID = 'CSPL';


        }else if($selected_company == "2"){


            $GodownID = 'CFF';


        }else if($selected_company == "3"){


            $GodownID = 'CBUPL';


        }


        


        $this->db->select(db_prefix() . 'history.*, '.db_prefix() . 'stockmaster.*');


		$this->db->from(db_prefix() . 'history');


		$this->db->join(db_prefix() . 'stockmaster', '' . db_prefix() . 'stockmaster.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'stockmaster.PlantID = ' . db_prefix() . 'history.PlantID AND ' . db_prefix() . 'stockmaster.FY = ' . db_prefix() . 'history.FY');


		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);


		$this->db->where(db_prefix() . 'history.FY', $fy);


		//$this->db->where(db_prefix() . 'history.GodownID',$GodownID);


		$this->db->where(db_prefix() . 'stockmaster.GodownID',$GodownID);


		$this->db->where(db_prefix() . 'history.OrderID ', $OrderID);


		return $this->db->get()->result_array();


    }


    


    public function increment_next_tax_invoice_number()


    {


        $FY = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        


        $this->db->where('name', 'next_tax_number_for_kirti');


        $this->db->set('value', 'value+1', false);


        $this->db->WHERE('FY', $FY);


        $this->db->update(db_prefix() . 'options');


    }


    


    public function increment_next_farmer_number()


    {


        $FY = $this->session->userdata('finacial_year');


        $this->db->where('name', 'next_farmer_code');


        $this->db->set('value', 'value+1', false);


        $this->db->WHERE('FY', $FY);


        $this->db->update(db_prefix() . 'options');


    }


    /**


     * @since  2.7.0


     *


     * Decrement the invoies next number


     *


     * @return void


     */


    public function decrement_next_number()


    {


        $this->db->where('name', 'next_order_number');


        $this->db->set('value', 'value-1', false);


        $this->db->update(db_prefix() . 'options');


    }


    


    public function load_data_for_order($data){


         $from_date = to_sql_date($data["from_date"]);


        $to_date = to_sql_date($data["to_date"]);


        $fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


            $this->db->select(db_prefix() . 'ordermaster.OrderID,'.db_prefix() . 'ordermaster.Transdate,'.db_prefix() . 'ordermaster.SalesID,'.db_prefix() . 'salesmaster.Transdate AS SaleDate,'.db_prefix() . 'ordermaster.ChallanID,'.db_prefix() . 'ordermaster.AccountID,'.db_prefix() . 'ordermaster.OrderAmt,'.db_prefix() . 'ordermaster.order_type,'.db_prefix() . 'salesmaster.BillAmt');


			$this->db->from(db_prefix() . 'ordermaster');


			$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);


			$this->db->where(db_prefix() . 'ordermaster.FY', $fy);


			$this->db->join(db_prefix() . 'salesmaster', '' . db_prefix() . 'salesmaster.OrderID = ' . db_prefix() . 'ordermaster.OrderID AND ' . db_prefix() . 'salesmaster.PlantID = ' . db_prefix() . 'ordermaster.PlantID AND ' . db_prefix() . 'salesmaster.FY = ' . db_prefix() . 'ordermaster.FY');


			$this->db->where(db_prefix() . 'ordermaster.ChallanID is NOT NULL', NULL, FALSE);


			$this->db->where( db_prefix() . 'salesmaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');


			$this->db->order_by( db_prefix() .'ordermaster.OrderID','DESC');


			return $this->db->get()->result_array();


    }


    


    public function GetAnamatRequest($data)


    {


        $from_date = to_sql_date($data["from_date"]);


        $to_date = to_sql_date($data["to_date"]);


        


        $IsApprove = $data["IsApprove"];


        $fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        $UserID = $this->session->userdata('username');


        //return $UserID;


        


        $kirti_status = array('NA','Y');


        //return $centerIDs;


        $this->db->select('tbllead_master.*,tblclients.company,tblclients.AccountID,tblclients.CustomerType,tblcontacts.firstname,tblcontacts.lastname,


            tblCenterMaster.CenterName,tblitems.ItemName,tblitems.ItemID,


            BClients.company AS BName,BContacts.firstname AS Bfirstname,BContacts.lastname AS Blastname');


			$this->db->from(db_prefix() . 'lead_master');


			$this->db->where(db_prefix() . 'lead_master.PlantID', $selected_company);


			$this->db->where(db_prefix() . 'lead_master.FY', $fy);


			$this->db->where('tbllead_master.TType', 'A');


			$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'clients.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'contacts.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'clients AS BClients', 'BClients.AccountID = tbllead_master.BrokerID AND BClients.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'contacts AS BContacts', 'BContacts.AccountID = tbllead_master.BrokerID AND BContacts.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'CenterMaster', '' . db_prefix() . 'CenterMaster.CenterID = tbllead_master.CenterID');


			$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = tbllead_master.ItemID');


			if($data["from_date"] !== "" && $data["to_date"] !== ""){


			    $this->db->where( db_prefix() . 'lead_master.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');


			}


			


			if(!is_admin()){


			    $this->db->join(db_prefix() . 'staff_wise_center', '' . db_prefix() . 'staff_wise_center.CenterID = tbllead_master.CenterID AND tblstaff_wise_center.AccountID = "'.$UserID.'"');


			    $this->db->join(db_prefix() . 'staff_wise_items', '' . db_prefix() . 'staff_wise_items.ItemID = tbllead_master.ItemID AND tblstaff_wise_items.AccountID = "'.$UserID.'"');


			    //$this->db->where_in(db_prefix() . 'lead_master.CenterID ',$centerIDs);


			}


			/*if(!is_admin()){


			    $this->db->where_in(db_prefix() . 'lead_master.ItemID ',$ItemIDs);


			}*/


			


			if($data["IsApprove"] && $data["IsApprove"] !== ""){


			    $this->db->where(db_prefix() . 'lead_master.IsApprove ',$data["IsApprove"]);


			}else{


			    $this->db->where_in(db_prefix() . 'lead_master.IsApprove ',$kirti_status);


			}


			$this->db->where(db_prefix() . 'lead_master.ClientApprove ','Y');


			$this->db->where(db_prefix() . 'lead_master.BrokerApprove ','Y');


			


			$this->db->order_by( db_prefix() .'lead_master.id','ASC');


		    return $this->db->get()->result_array();


		     


    }


    


    public function GetTradeFinanceRequest($data)


    {


        $from_date = to_sql_date($data["from_date"]);


        $to_date = to_sql_date($data["to_date"]);


        


        $IsApprove = $data["IsApprove"];


        $fy = $this->session->userdata('finacial_year');


        $selected_company = $this->session->userdata('root_company');


        $UserID = $this->session->userdata('username');


        //return $UserID;


        


        $kirti_status = array('NA','Y');


        //return $centerIDs;


        $this->db->select('tbllead_master.*,tblclients.company,tblclients.AccountID,tblclients.CustomerType,tblcontacts.firstname,tblcontacts.lastname,


            tblCenterMaster.CenterName,tblitems.ItemName,tblitems.ItemID,


            BClients.company AS BName,BContacts.firstname AS Bfirstname,BContacts.lastname AS Blastname');


			$this->db->from(db_prefix() . 'lead_master');


			$this->db->where(db_prefix() . 'lead_master.PlantID', $selected_company);


			$this->db->where(db_prefix() . 'lead_master.FY', $fy);


			$this->db->where('tbllead_master.TType', 'T');


			$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'clients.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = tbllead_master.AccountID AND ' . db_prefix() . 'contacts.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'clients AS BClients', 'BClients.AccountID = tbllead_master.BrokerID AND BClients.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'contacts AS BContacts', 'BContacts.AccountID = tbllead_master.BrokerID AND BContacts.PlantID = tbllead_master.PlantID ');


			$this->db->join(db_prefix() . 'CenterMaster', '' . db_prefix() . 'CenterMaster.CenterID = tbllead_master.CenterID');


			$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.ItemID = tbllead_master.ItemID');


			if($data["from_date"] !== "" && $data["to_date"] !== ""){


			    $this->db->where( db_prefix() . 'lead_master.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');


			}


			


			if(!is_admin()){


			    $this->db->join(db_prefix() . 'staff_wise_center', '' . db_prefix() . 'staff_wise_center.CenterID = tbllead_master.CenterID AND tblstaff_wise_center.AccountID = "'.$UserID.'"');


			    $this->db->join(db_prefix() . 'staff_wise_items', '' . db_prefix() . 'staff_wise_items.ItemID = tbllead_master.ItemID AND tblstaff_wise_items.AccountID = "'.$UserID.'"');


			    //$this->db->where_in(db_prefix() . 'lead_master.CenterID ',$centerIDs);


			}


			/*if(!is_admin()){


			    $this->db->where_in(db_prefix() . 'lead_master.ItemID ',$ItemIDs);


			}*/


			


			if($data["IsApprove"] && $data["IsApprove"] !== ""){


			    $this->db->where(db_prefix() . 'lead_master.IsApprove ',$data["IsApprove"]);


			}else{


			    $this->db->where_in(db_prefix() . 'lead_master.IsApprove ',$kirti_status);


			}


			$this->db->where(db_prefix() . 'lead_master.ClientApprove ','Y');


			$this->db->where(db_prefix() . 'lead_master.BrokerApprove ','Y');


			


			$this->db->order_by( db_prefix() .'lead_master.id','ASC');


		    return $this->db->get()->result_array();


		     


    }


    


    public function getAllMandiDb()





    {





        return $this->db->get(db_prefix() .'CenterMaster')->result_array();





    }


//============= Get All Active Center ==========================================


    public function GetAllActiveCenterList()


    {


        $this->db->select('tblCenterMaster.*');


        $this->db->from(db_prefix() . 'CenterMaster');


        $this->db->where('tblCenterMaster.status', "Y");


        return $this->db->get()->result_array();





    }


    


    public function GetCommodityListForCompanyMaster($CenterID)


    {





        $this->db->select('tblCenter_wise_item.CenterID, tblitems.GroupCode, tblitems_sub_groups.name');


        $this->db->from(db_prefix() . 'Center_wise_item');


        $this->db->join('tblitems', 'tblitems.ItemID = ' . db_prefix() . 'Center_wise_item.ItemID', 'inner');


        $this->db->join('tblitems_sub_groups', 'tblitems_sub_groups.ShortCode = tblitems.GroupCode', 'inner');


        $this->db->where(db_prefix() . 'Center_wise_item.CenterID', $CenterID);


        $this->db->group_by('tblitems.GroupCode');


        return $this->db->get()->result_array();





    }


    


    public function GetItemListForCompanyMaster($CenterID,$CommodityID)


    {


        $this->db->select('tblCenter_wise_item.CenterID, tblitems.GroupCode, tblitems.ItemName,tblitems.ItemID');


        $this->db->from(db_prefix() . 'Center_wise_item');


        $this->db->join('tblitems', 'tblitems.ItemID = ' . db_prefix() . 'Center_wise_item.ItemID');


        $this->db->where(db_prefix() . 'Center_wise_item.CenterID', $CenterID);


        $this->db->where('tblitems.GroupCode', $CommodityID);


        return $this->db->get()->result_array();





    }


}


