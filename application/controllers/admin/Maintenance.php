<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Maintenance extends Admin_Controller {
	
	public function __construct() 
    {
		 parent::__construct();
		 $this->load->model('maintenance_m');
	}
}