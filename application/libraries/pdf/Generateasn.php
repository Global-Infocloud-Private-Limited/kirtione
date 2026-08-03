<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/App_pdf.php');

class Generateasn extends App_pdf
{
    protected $AsnDetails;

    public function __construct($AsnDetails = array())
    {
        $GLOBALS['asn_pdf'] = $AsnDetails;
        
        parent::__construct();
        
        $this->AsnDetails = $AsnDetails;
        $this->BookingID = $AsnDetails->BookingID;
        $this->tag = $tag;
        $this->SetTitle($AsnDetails->BookingID);
    }

    public function prepare()
    {

        $this->set_view_vars([
            'AsnDetails'=> $this->AsnDetails,
            'BookingID' => $this->BookingID,
        ]);

        return $this->build();
    }

    protected function type()
    {
        return 'asn';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/asn/my_asn.php';
    
        return $customPath;
    }

}
