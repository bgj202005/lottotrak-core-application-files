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
		$old_range = 0;
		
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);

		// Check to see if the actual table exists in the db?
		$draw_db = $this->lotteries_m->last_draw_db($tbl_name);
		if (!$draw_db)
		{
			$this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. '.$tbl_name.' Does not exist. Create the Lottery Database now.');
			redirect('admin/history');
		}
		elseif(is_null($draw_db->sum_draw)&&is_null($draw_db->sum_digits)&&is_null($draw_db->even)&&is_null($draw_db->odd)&&is_null($draw_db->range_draw)
		&&is_null($draw_db->repeat_decade)&&is_null($draw_db->repeat_last))
		{
			$this->session->set_flashdata('message', $this->data['lottery']->lottery_name.' has been updated and the STATISTICS must be CALCULATED. 
			Please go to Statistics and View Lottery Stats on the menu.');
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
		$new_range = $this->uri->segment(5,0); 						// Return segment range
		$glance = FALSE;											// Always FALSE as default
		$bln_chg = FALSE;											// Boolean Change Flag, Only these three conditions result in a recalculation: 
																	// Change in range, Change in Extra Draw, Change in Extra Ball									
		$old_range = ($old_range < 100 ? $all : 100);

		$glance = $this->history_m->glance_exists($id);
		if(empty($glance)) 
		{
			$new_range = $old_range; 								// If no prior record
			$this->data['lottery']->extra_included = 0; 			// Defaults at No Extra Ball and No Extra Draws
			$this->data['lottery']->extra_draws = 0;
			$bln_chg = TRUE;										// No Data exists. A change must occur
		}
		else																				
		{
			// #1 First Change Result
			if($new_range) // Has a change in the range occurred?
			{
				$bln_chg = TRUE; // Change in the range?
				$this->data['lottery']->extra_included = $glance->extra_included;
				$this->data['lottery']->extra_draws = $glance->extra_draws;
			} // if($new_range) 
			// #2 Second Change Result
			elseif($this->uri->segment(6)=='extra') 
				{
					if($glance->extra_included) 
					{
						$this->data['lottery']->extra_included = 0; // No Extra Included
						$this->data['lottery']->extra_draws = 0; // No Extra Draws Included
					}
					else
					{
						$this->data['lottery']->extra_included = 1; // Only Extra Allowed
						$this->data['lottery']->extra_draws = 0; 
					}
					$new_range = $glance->range;
					$bln_chg = TRUE;
				}
				// #3 Third Change Result
			elseif($this->uri->segment(6)=='draws') 
				{
					if($glance->extra_draws) 
					{
						$this->data['lottery']->extra_included = 0; // No Extra Included
						$this->data['lottery']->extra_draws = 0; // No Extra Draws Included
					}
					else
					{
						$this->data['lottery']->extra_draws = 1;	// Only Extra Draws allowed
						$this->data['lottery']->extra_included = 0;
					}
					$new_range = $glance->range;
					$bln_chg = TRUE;
				}
			else 
			{
				$new_range = $glance->range;
				$this->data['lottery']->extra_included = $glance->extra_included;
				$this->data['lottery']->extra_draws = $glance->extra_draws;
			}
		} // if(!empty($glance))	
		$sel_range = ($new_range>100 ? $sel_range = intval($new_range / 100) : $sel_range = 1);
		$this->data['lottery']->last_drawn['interval'] = $interval;		// Record the interval here (for the dropdown)
		$this->data['lottery']->last_drawn['sel_range'] = $sel_range;	// What was selected for the range in the previous page
		$this->data['lottery']->last_drawn['range'] = $new_range;
		$this->data['lottery']->last_drawn['all'] = $all;
		$drawings = $this->history_m->load_history($tbl_name, $id, $new_range, $this->data['lottery']->extra_draws);
		if(!$drawings)
		{
			$this->session->set_flashdata('message', 'Problem loading draws for the last'.$new_range.' draws. Make sure there is a minimum of 100 draws and statistics available.');
			redirect('admin/history'); 
		}
		/**** At A Glance Statistics Analysis Methods up to the latest draw *****/

		$this->data['lottery']->last_drawn['trends'] = ((!empty($glance)&&!$bln_chg) ? $glance->trends : $this->history_m->trend_history($drawings, $this->data['lottery']->balls_drawn, $this->data['lottery']->extra_draws, $this->data['lottery']->extra_included));
		$this->data['lottery']->last_drawn['repeats'] = ((!empty($glance)&&!$bln_chg) ? $glance->repeats : $this->history_m->repeat_history($drawings, $this->data['lottery']->balls_drawn, $this->data['lottery']->extra_draws, $this->data['lottery']->extra_included));
		$this->data['lottery']->last_drawn['consecutives'] = ((!empty($glance)&&!$bln_chg) ? $glance->consecutives : $this->history_m->consecutive_history($drawings, $this->data['lottery']->balls_drawn, $this->data['lottery']->extra_draws, $this->data['lottery']->extra_included));		
		$this->data['lottery']->last_drawn['adjacents'] = ((!empty($glance)&&!$bln_chg) ? $glance->adjacents : $this->history_m->adjacents_history($drawings, $this->data['lottery']->balls_drawn));
		$this->data['lottery']->last_drawn['sums_history'] = ((!empty($glance)&&!$bln_chg) ? $glance->winning_sums : $this->history_m->sums_history($drawings));		
		$this->data['lottery']->last_drawn['digits_history'] = ((!empty($glance)&&!$bln_chg) ? $glance->winning_digits : $this->history_m->digits_history($drawings));
		$this->data['lottery']->last_drawn['range_history'] = ((!empty($glance)&&!$bln_chg) ? $glance->number_range : $this->history_m->range_history($drawings, $this->data['lottery']->balls_drawn));
		$this->data['lottery']->last_drawn['parity_history'] = ((!empty($glance)&&!$bln_chg) ? $glance->parity : $this->history_m->parity_history($drawings, $this->data['lottery']->balls_drawn, $this->data['lottery']->extra_draws, $tbl_name));	
		if(!$this->data['lottery']->last_drawn['parity_history'])
		{
			$this->session->set_flashdata('message', 'Problem retrieving the odd / even combinations for the last '.$new_range.' draws. Please check the '.$tbl_name.' database.');
			redirect('admin/history'); 
		} 
		/***** End of Statistic Calculations ******/
		$aag = array(
			'range'				=> $new_range,
			'trends'			=> $this->data['lottery']->last_drawn['trends'],
			'repeats'			=> $this->data['lottery']->last_drawn['repeats'],
			'consecutives'		=> $this->data['lottery']->last_drawn['consecutives'],
			'adjacents'			=> $this->data['lottery']->last_drawn['adjacents'],
			'winning_sums'		=> $this->data['lottery']->last_drawn['sums_history'],
			'winning_digits'	=> $this->data['lottery']->last_drawn['digits_history'],
			'number_range'		=> $this->data['lottery']->last_drawn['range_history'],
			'parity'			=> $this->data['lottery']->last_drawn['parity_history'],
			'draw_id'			=> $draw_db->id,
			'lottery_id'		=> $id,
			'extra_included'	=> $this->data['lottery']->extra_included,
			'extra_draws'		=> $this->data['lottery']->extra_draws
		); // $aag - At A Glance Data
		if($bln_chg)	// Only if 1 of the 3 options have changed
		{
			if(!$this->history_m->glance_data_save($aag, $glance))
			{
				$this->session->set_flashdata('message', 'There is a problem saving the at a Glance Statistics to the database.');
				redirect('admin/history'); 
			}
		}	
		unset($drawings);	// Free up Memory
		unset($glance);
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/glance'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);		// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);		// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->data['subview']  = 'admin/dashboard/history/glance';
		$this->data['stat_method'] = $this;									// Access the methods in the view
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