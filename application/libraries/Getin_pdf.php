<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(dirname(__FILE__).'/tcpdf/tcpdf.php');

class Getin_pdf extends TCPDF
{
    
    public function __construct(){
        parent::__construct();
    }
}

?>