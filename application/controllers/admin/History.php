<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class History extends Admin_Controller {
	
	public function __construct() 
    {
		 parent::__construct();

		 $this->load->dbforge();
		 $this->load->model('lotteries_m');
		 $this->load->model('statistics_m');
		 $this->load->model('history_m');
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
	 * Checks the status of the frontend of the website for maintenance mode or "live", 
	 * if maintenance mode then turn the site 'live' or 'live',
	 * turn the site into maintenance mode
	 * @param	$id
	 * @return 	none	
	 * */
	public function glance($id)
	{
		$this->data['message'] = '';	// Defaulted to No Error Messages
		$this->data['lottery'] = $this->lotteries_m->get($id);
		
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);

		// Check to see if the actual table exists in the db?
		$draw_db = $this->lotteries_m->last_draw_db($tbl_name);
		if (!$draw_db)
		{
			$this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. '.$tbl_name.' Does not exist. Create the Lottery Database now.');
			redirect('admin/history');
		}
		elseif(!$draw_db=='no draws') 
		{
			$this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. '.$tbl_name.' Does exist. However, there arte no draws with this lottery');
			redirect('admin/history');
		}

		if(!$this->statistics_m->lottery_stats_exist($tbl_name)) 
		{
			$this->session->set_flashdata('message', 'There are NO Statistics Calculated yet. They need to be Calculated in Statistics.');
			redirect('admin/history'); 
		}

		$all = $this->lotteries_m->db_row_count($tbl_name); // Return the total number of draws for this lottery
		if($all>100)
		{
			$interval = intval($all / 100); // Create the drop down in multiples of 100 and typecast to an integer value (truncates the floating point portion)
			if(!$interval) $interval = 1;	// 1 to 100 draws
		}
		else
		{
			$interval = 0;
		}
		$new_range = $this->uri->segment(5,0); 					// Return segment range
		$old_range = 100;
		if(!$new_range) $new_range = $old_range;				// Database Range
		$this->data['lottery']->extra_included = ($this->uri->segment(6)=='extra' ? TRUE : FALSE);
		$this->data['lottery']->extra_draws = ($this->uri->segment(6)=='draws' ? TRUE : FALSE);
		$sel_range = ($new_range>100 ? $sel_range = intval($new_range / 100) : $sel_range = 1);
		$this->data['lottery']->last_drawn['interval'] = $interval;		// Record the interval here (for the dropdown)
		$this->data['lottery']->last_drawn['sel_range'] = $sel_range;	// What was selected for the range in the previous page
		$this->data['lottery']->last_drawn['range'] = $new_range;
		$this->data['lottery']->last_drawn['all'] = $all;
		$drawings = $this->history_m->load_history($tbl_name, $id, $new_range);
		if(!$drawings)
		{
			$this->session->set_flashdata('message', 'Problem loading draws for the last'.$new_range.' draws. Make sure there is a minimum of 100 draws and statistics available.');
			redirect('admin/history'); 
		}
		/**** At A Glance Statistics Analysis Methods up to the latest draw *****/
		$this->data['lottery']->last_drawn['trend'] = $this->history_m->trend_history($drawings, $this->data['lottery']->balls_drawn, $this->data['lottery']->extra_draws, $this->data['lottery']->extra_included);
		$this->data['lottery']->last_drawn['repeats'] = $this->history_m->repeat_history($drawings, $this->data['lottery']->balls_drawn, $this->data['lottery']->extra_draws, $this->data['lottery']->extra_included);
		$this->data['lottery']->last_drawn['consecutives'] = $this->history_m->consecutive_history($drawings, $this->data['lottery']->balls_drawn, $this->data['lottery']->extra_draws, $this->data['lottery']->extra_included);		
		/***** End of Statistic Calculations ******/
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/history'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->data['subview']  = 'admin/dashboard/history/glance';
		$this->data['stat_method'] = $this;				// Access the methods in the view
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