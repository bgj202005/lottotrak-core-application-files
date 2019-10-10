<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Activate extends Frontend_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('activate_m');
    }
    
    public function index() 
    {
        echo "We now need to validate";
    }
}
