<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Maintenance extends Admin_Controller {
	
	public function __construct() 
    {
		 parent::__construct();
		 $this->load->model('maintenance_m');
	}

	/**
	 * Checks the status of the frontend of the website for maintenance mode or "live", 
	 * if maintenance mode then turn the site 'live' or 'live',
	 * turn the site into maintenance mode
	 * @param	none
	 * @return 	none	
	 * */
	public function index()
	{
		if (!$this->maintenance_m->maintenance_check()) // Site Live?
		{
			$this->maintenance_m->maintenance_on();	// Maintance Mode is active
		}
		else
		{
			$this->maintenance_m->maintenance_off(); // Live Site is active
		}

	redirect($this->session->uri);		// Return to the previous administration page	
	}
}