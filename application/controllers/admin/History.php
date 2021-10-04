<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class History extends Admin_Controller {
	
	public function __construct() 
    {
		 parent::__construct();

		 $this->load->dbforge();
		 $this->load->model('lotteries_m');
		 $this->load->model('statistics_m');
		 $this->load->model('maintenance_m');
		 $this->load->helper('file');
		 $this->load->helper('html');
		 //$this->output->enable_profiler(TRUE);
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
		// Fetch all lotteries from the database
		$this->data['lotteries'] = $this->lotteries_m->get();
		foreach($this->data['lotteries'] as $lottery) 
		{
			$tbl_name = $this->lotteries_m->lotto_table_convert($lottery->lottery_name);

			$lottery->last_date = $this->statistics_m->last_date($tbl_name);
			$lottery->last_draw = $this->statistics_m->last_draw($tbl_name, $lottery->balls_drawn, $lottery->extra_ball);
			$c = $this->statistics_m->lottery_rows($tbl_name);
			if($c>100) $c = 100;
		}

		if ($this->session->flashdata('message')) $this->data['message'] = $this->session->flashdata('message');
		else $this->data['message'] = '';
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the Statistics menu
		$this->session->set_userdata('uri', 'admin/'.$this->data['current']);
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	 
		$this->data['subview'] = 'admin/dashboard/history/index';
		$this->data['history'] = $this;				// Access the methods in the view
		$this->load->view('admin/_layout_main', $this->data);
	}
	/**
	 * At a Glance Icon 
	 * 
	 * @param 	   	string	$uri	uri admin address of the history page	
	 * @return      none
	 */
	public function btn_glance($uri) 
	{
		return anchor($uri, '<i class="fa fa-eye-slash fa-2x" aria-hidden="true">', array('title' => 'View the latest winning opptunities for this lottery with the at a glance option'));

	}
	/**
	 * Display History Icon 
	 * 
	 * @param 	   	string	$uri	uri admin address of the history page	
	 * @return      none
	 */
	public function btn_history($uri) 
	{
		return anchor($uri, '<i class="fa fa-history fa-2x" aria-hidden="true">', array('title' => 'View History of Actual Wins of the lottery'));

	}
	/**
	 * Calculate the Current History or Update to the latest Draw
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_calculate($uri)
	{
		return anchor($uri, '<i class="fa fa-calculator fa-2x" aria-hidden="true">', array('title' => 'Calculate the Current History or Update to the latest Draw', 'class' => 'calculate'));
	}
}