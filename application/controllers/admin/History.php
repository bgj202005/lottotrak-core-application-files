<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class History extends Admin_Controller {
	
	public function __construct() 
    {
		 parent::__construct();

		 $this->load->dbforge();	 $this->load->model('lotteries_m');
	 $this->load->model('statistics_m');
	 $this->load->model('history_m');
	 $this->load->model('predictions_m');
	 
	 // Load new specialized models
	 $this->load->model('combination_files_m');
	 $this->load->model('lottery_data_m');
	 $this->load->model('lottery_statistics_m');
	 $this->load->model('number_generation_m');
	 $this->load->model('combination_filters_m');
	 $this->load->model('math_utilities_m');
	 
	 $this->load->model('maintenance_m');
		 $this->load->helper('file');
		 $this->load->helper('html');
		 //$this->output->enable_profiler(TRUE);
	}

	/**
	 * Lists the Lotteries with At a Glance, Calculate and Results
	 * 
	 * 
	 * @param	none
	 * @return 	none	
	 * */
	public function index()
	{
		// Fetch only enabled lotteries from the database
		$this->data['lotteries'] = $this->lotteries_m->get_enabled();
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

		// Check if lottery highlights need updating and update if necessary
		$this->load->model('prize_m');
		$highlight_check = $this->prize_m->check_and_update_lottery_highlights($id);
		
		// Set message based on highlight check result
		if ($highlight_check['status'] === 'updated') {
			$this->data['message'] = $highlight_check['message'];
		} elseif ($highlight_check['status'] === 'error') {
			$this->session->set_flashdata('message', $highlight_check['message']);
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
			// #1 First Change Result - Check for checkbox actions first (segment 5)
			if($this->uri->segment(5)=='extra') 
				{
					// Toggle extra_included independently
					if($glance->extra_included) 
					{
						$this->data['lottery']->extra_included = 0; // Turn off Extra Included
					}
					else
					{
						$this->data['lottery']->extra_included = 1; // Turn on Extra Included
					}
					// Preserve the current state of extra_draws
					$this->data['lottery']->extra_draws = $glance->extra_draws;
					$new_range = $glance->range;
					$bln_chg = TRUE;
				}
				// #2 Second Change Result
			elseif($this->uri->segment(5)=='draws') 
				{
					// Toggle extra_draws independently
					if($glance->extra_draws) 
					{
						$this->data['lottery']->extra_draws = 0; // Turn off Extra Draws
					}
					else
					{
						$this->data['lottery']->extra_draws = 1; // Turn on Extra Draws
					}
					// Preserve the current state of extra_included
					$this->data['lottery']->extra_included = $glance->extra_included;
					$new_range = $glance->range;
					$bln_chg = TRUE;
				}
			// #3 Third Change Result - Range changes
			elseif($new_range) // Has a change in the range occurred?
			{
				$bln_chg = TRUE; // Change in the range?
				$this->data['lottery']->extra_included = $glance->extra_included;
				$this->data['lottery']->extra_draws = $glance->extra_draws;
			} // if($new_range) 
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
		$drawings = $this->history_m->load_history($tbl_name, $id, $new_range, 0); // $this->data['lottery']->extra_draws is all 0 for trends and repeats
		if(!$drawings)
		{
			$this->session->set_flashdata('message', 'Problem loading draws for the last'.$new_range.' draws. Make sure there is a minimum of 100 draws and statistics available.');
			redirect('admin/history'); 
		}
		/**** At A Glance Statistics Analysis Methods up to the latest draw *****/
		$this->data['lottery']->last_drawn['trends'] = ((!empty($glance)&&!$bln_chg) ? $glance->trends : $this->history_m->trend_history($drawings, $this->data['lottery']->balls_drawn, $this->data['lottery']->extra_included));
		$this->data['lottery']->last_drawn['repeats'] = ((!empty($glance)&&!$bln_chg) ? $glance->repeats : $this->history_m->repeat_history($drawings, $this->data['lottery']->balls_drawn, $this->data['lottery']->extra_included));
		// Required to get the drawings with or without the extra draws, consecutives is OK, adjacents is OK, sums are OK, digits are OK, Range is OK, Parity is OK
		$drawings = $this->history_m->load_history($tbl_name, $id, $new_range, $this->data['lottery']->extra_draws);
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
		// Digit Sum Prediction: use cache only if scores already exist in the new 5-part format.
		// If the stored runners_up is missing or in legacy format (no scores), recalculate now
		// using the raw draws already in memory, then save just the prediction fields immediately.
		$_cached_runners = (isset($glance->predicted_runners_up) ? $glance->predicted_runners_up : '');
		$_has_scores     = (!empty($_cached_runners) && count(explode('=', explode(',', $_cached_runners)[0])) === 5);
		if (!empty($glance) && !$bln_chg && isset($glance->predicted_digit_sum) && $_has_scores)
		{
			// Fully cached — all three scores present, no calculation needed
			$_prediction = array(
				'predicted_digit_sum'   => $glance->predicted_digit_sum,
				'predicted_winning_sum' => $glance->predicted_winning_sum,
				'predicted_runners_up'  => $_cached_runners
			);
		}
		else
		{
			// Calculate fresh (new draw, changed settings, or legacy record without scores)
			$_prediction = $this->history_m->digit_sum_prediction(
				$this->data['lottery']->last_drawn['digits_history'],
				$this->data['lottery']->last_drawn['sums_history'],
				$drawings,
				$tbl_name,
				$id,
				$this->data['lottery']->extra_draws
			);
			// Save prediction fields immediately if glance record exists but scores were missing
			if (!empty($glance) && !$bln_chg && !$_has_scores)
			{
				$this->history_m->glance_prediction_save($id, $_prediction);
			}
		}
		$this->data['lottery']->last_drawn['predicted_digit_sum']   = $_prediction['predicted_digit_sum'];
		$this->data['lottery']->last_drawn['predicted_winning_sum'] = $_prediction['predicted_winning_sum'];
		$this->data['lottery']->last_drawn['predicted_runners_up']  = $_prediction['predicted_runners_up'];
		/***** End of Statistic Calculations ******/
		$aag = array(
			'range'					=> $new_range,
			'trends'				=> $this->data['lottery']->last_drawn['trends'],
			'repeats'				=> $this->data['lottery']->last_drawn['repeats'],
			'consecutives'			=> $this->data['lottery']->last_drawn['consecutives'],
			'adjacents'				=> $this->data['lottery']->last_drawn['adjacents'],
			'winning_sums'			=> $this->data['lottery']->last_drawn['sums_history'],
			'winning_digits'		=> $this->data['lottery']->last_drawn['digits_history'],
			'number_range'			=> $this->data['lottery']->last_drawn['range_history'],
			'parity'				=> $this->data['lottery']->last_drawn['parity_history'],
			'predicted_digit_sum'	=> $this->data['lottery']->last_drawn['predicted_digit_sum'],
			'predicted_winning_sum'	=> $this->data['lottery']->last_drawn['predicted_winning_sum'],
			'predicted_runners_up'	=> $this->data['lottery']->last_drawn['predicted_runners_up'],
			'draw_id'				=> $draw_db->id,
			'lottery_id'			=> $id,
			'extra_included'		=> $this->data['lottery']->extra_included,
			'extra_draws'			=> $this->data['lottery']->extra_draws
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
	 * Calculates the win history based on the h-w-c's, followers, and friends.  The Numbers are selected from
	 * the outputs for these methods and put into a full wheeling table.  The correctly drawn numbers from the
	 * next draw are compared with the wheeling table tickets.  The wins will be updated based on the prize cateogory.
	 * 
	 * @param	integer		$id		Lottery id
	 * @return 	none	
	 * */
	public function calculate($id)
	{
		// Fetch only enabled lotteries from the database
		$this->data['lotteries'] = $this->lotteries_m->get_enabled();
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
	 * Display the win history based on the hots, warms, colds positional hits.  
	 * 
	 * @param	integer		$id		Lottery id
	 * @return 	none	
	 * */
	public function h_w_c($id)
	{
		// Fetch the selected lottery from the database
		$this->data['message'] = '';						// Defaulted to No Error Messages
															//if the form was submitted, the database values will compared to the submitted ones.
		$this->data['lottery'] = $this->lotteries_m->get($id);
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		$drawn = $this->data['lottery']->balls_drawn;		// Get the number of balls drawn for this lottory, Pick 5, Pick 6, Pick 7, etc.
		$max_ball = $this->data['lottery']->maximum_ball;	// Get the highest ball drawn for this lottery, e.g. 49 in Lottery 649, 50 in Lottomax
		// duplicate extra ball flag
		$dup = $this->data['lottery']->duplicate_extra_ball;  // duplicate_extra_ball  = TRUE (1) lotteries
		// Check to see if the actual table exists in the db?
		if (!$this->lotteries_m->lotto_table_exists($tbl_name))
		{
			$this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. '.$tbl_name.' Does not exist. Create the Lottery Database now.');
			redirect('admin/statistics');
		}
		$this->data['lottery']->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl_name);	// Retrieve the last drawn numbers and draw date
		$h_w_c = $this->statistics_m->h_w_c_exists($id);
		if(!is_null($h_w_c))	// Existing HWC?
		{
			$range = $h_w_c['range'];
			$hwc_history = $this->statistics_m->hwc_history_exists($id);
			if(!is_null($hwc_history)) // Correct Lottery & Range?
			{
				$draw = array(); 		// Temporary draw array
				$positions = array();	// Temporary position array
				$positions_last = array();	// Temporary position from last array
				// Use the H-W-C settings from the database, NOT hard-coded duplicate_extra_ball flag
				$extra_included_setting = $h_w_c['extra_included'];
				// Pass the correct duplicate_extra_ball flag (not extra_included) to onlydrawn
				// onlydrawn expects: ($draw_data, $has_extra_ball, $is_duplicate_extra_ball_system)
				$draw = $this->history_m->onlydrawn($this->data['lottery']->last_drawn, $extra_included_setting ? $this->data['lottery']->extra_ball : 0, $dup);
				$hots = $h_w_c['h_count'];
				$warms = $h_w_c['w_count'];
				$colds = $h_w_c['c_count'];
				$this->data['lottery']->H = $hots;  // Number of Hots Distributed e.g. 16 Hots
				$this->data['lottery']->W = $warms; // Number of Warms Distributed e.g 18 Colds
				$this->data['lottery']->C = $colds; // Number of colds Distributed e.g 18 Colds
				$this->data['lottery']->extra_included = $h_w_c['extra_included'];
				$this->data['lottery']->extra_draws = $h_w_c['extra_draws'];
				$this->data['lottery']->last_drawn['range'] = $h_w_c['range'];
				$this->data['lottery']->prediction_pool = isset($h_w_c['prediction_pool']) ? $h_w_c['prediction_pool'] : 18; // Default to 18 if not set
				$strhots_last = $h_w_c['hots_last']; 		// Pull from DB
				$strwarms_last = $h_w_c['warms_last'];	// All counts for Hots, Warms, Colds
				$strcolds_last = $h_w_c['colds_last'];
				$strhots = $h_w_c['hots']; 		// Pull from DB
				$strwarms = $h_w_c['warms'];	// All counts for Hots, Warms, Colds
				$strcolds = $h_w_c['colds'];
				$strdupextra = $h_w_c['dupextra'];
				$strdupextra_last = $h_w_c['dupextra_last'];
				$hots = explode(",", $strhots); // Convert to Arrays
				$warms = explode(",", $strwarms); 
				$colds = explode(",", $strcolds);
				$hots_last = explode(",", $strhots_last); // Convert to Arrays
				$warms_last = explode(",", $strwarms_last); 
				$colds_last = explode(",", $strcolds_last); 
				if(!empty($strdupextra)) {
					$dupextra = explode(",", $strdupextra);
					$dupextra_last = explode(",", $strdupextra_last);
				}
				// Iterate Hots from last draw
				$pos = 0;
				foreach($hots_last as $all_hots)
				{
					$n = strstr($all_hots, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
					$c = substr(strstr($all_hots, '='), 1); // Strip off to the left of the equal sign count
					if(!in_array($n,$draw))
					{
						$this->data['lottery']->hots_last[$n] = $c;
					}
					else
					{
						$this->data['lottery']->hots_last[$n.'*'] = $c;
						$positions_last[$pos.'h'] = 'h';
					}
					$pos++;
				}
				// Iterate Hots for next draw
				$pos = 0;
				foreach($hots as $all_hots)
				{
					$n = strstr($all_hots, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
					$c = substr(strstr($all_hots, '='), 1); // Strip off to the left of the equal sign count
					if(!in_array($n,$draw))
					{
						$this->data['lottery']->hots[$n] = $c;
					}
					else
					{
						$this->data['lottery']->hots[$n.'*'] = $c;
						$positions[$pos.'h'] = 'h';
					}
					$pos++;
				}
				// Interate Warms for last draw
				$pos = 0;
				foreach($warms_last as $all_warms)
				{
					$n = strstr($all_warms, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
					$c = substr(strstr($all_warms, '='), 1); // Strip off to the left of the equal sign count
					if(!in_array($n,$draw))
					{
						$this->data['lottery']->warms_last[$n] = $c;
					}
					else
					{
						$this->data['lottery']->warms_last[$n.'*'] = $c;
						if(!isset($positions_last[$pos.'w'])) $positions_last[$pos.'w'] = 'w';
					}
					$pos++;
				}
				// Interate Warms for next draw
				$pos = 0;
				foreach($warms as $all_warms)
				{
					$n = strstr($all_warms, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
					$c = substr(strstr($all_warms, '='), 1); // Strip off to the left of the equal sign count
					if(!in_array($n,$draw))
					{
						$this->data['lottery']->warms[$n] = $c;
					}
					else
					{
						$this->data['lottery']->warms[$n.'*'] = $c;
						if(!isset($positions[$pos.'w'])) $positions[$pos.'w'] = 'w';
					}
					$pos++;
				}
				// Iterate Colds for last draw
				$pos = 0;
				foreach($colds_last as $all_colds)
				{
					$n = strstr($all_colds, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
					$c = substr(strstr($all_colds, '='), 1); // Strip off to the left of the equal sign count
					if(!in_array($n,$draw))
					{
						$this->data['lottery']->colds_last[$n] = $c;
					}
					else
					{
						$this->data['lottery']->colds_last[$n.'*'] = $c;
						if(!isset($positions_last[$pos.'c'])) $positions_last[$pos.'c'] = 'c';
					}
					$pos++;
				}
				// Iterate Colds for last next
				$pos = 0;
				foreach($colds as $all_colds)
				{
					$n = strstr($all_colds, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
					$c = substr(strstr($all_colds, '='), 1); // Strip off to the left of the equal sign count
					if(!in_array($n,$draw))
					{
						$this->data['lottery']->colds[$n] = $c;
					}
					else
					{
						$this->data['lottery']->colds[$n.'*'] = $c;
						if(!isset($positions[$pos.'c'])) $positions[$pos.'c'] = 'c';
					}
					$pos++;
				}
				if (!empty($strdupextra)) // Only if there is the duplicate extra in this lottery?
				{
					// Iterate Extra Numbers that can have duplicates of the main balls
					foreach($dupextra as $all_dupextra)
					{
						$n = strstr($all_dupextra, '=', TRUE); 				// Strip off the ball drawn to the right of the equal sign
						$c = substr(strstr($all_dupextra, '='), 1); 		// Strip off to the left of the equal sign count
						$this->data['lottery']->dupextra[$n] = $c; 
					}
				}
				if (!empty($strdupextra_last)) // Only if there is the duplicate extra in this lottery?	
				{	
					foreach($dupextra_last as $all_dupextra_last)
					{
						$n = strstr($all_dupextra_last, '=', TRUE); 		// Strip off the ball drawn to the right of the equal sign
						$c = substr(strstr($all_dupextra_last, '='), 1); 	// Strip off to the left of the equal sign count
						$this->data['lottery']->dupextra_last[$n] = $c; 
					}
				}
			// Pull the winning positions for the Hots, Warms, Colds from last draw
				$strpositions_last = $hwc_history['position_last'];
				$heat_position_last = explode("|", $strpositions_last); // Split into arrays of heat_position, 0, 1, 2
				$hotpos_last = explode(">", $heat_position_last[0]);	 	// Hots
				$warmpos_last = explode(">", $heat_position_last[1]);	 	// Warms
				$coldpos_last = explode(">", $heat_position_last[2]); 	// Colds
				$hot_hits_last = explode(",", $hotpos_last[1]);
				$warm_hits_last = explode(",", $warmpos_last[1]);
				$cold_hits_last = explode(",", $coldpos_last[1]);

				// Pull the winning positions for the Hots, Warms, Colds for mext draw
				$strpositions = $hwc_history['position'];
				$heat_position = explode("|", $strpositions); // Split into arrays of heat_position, 0, 1, 2
				$hotpos = explode(">", $heat_position[0]);	 	// Hots
				$warmpos = explode(">", $heat_position[1]);	 	// Warms
				$coldpos = explode(">", $heat_position[2]); 	// Colds
				$hot_hits = explode(",", $hotpos[1]);
				$warm_hits = explode(",", $warmpos[1]);
				$cold_hits = explode(",", $coldpos[1]);
				// Iterate Hots Win Position for last draw
				foreach($hot_hits_last as $hots_pos)
				{
					$n = strstr($hots_pos, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
					$c = substr(strstr($hots_pos, '='), 1); // Strip off to the left of the equal sign count
					$this->data['lottery']->hots_pos_last[$n.'h'] = $c; 
				}
				// Iterate Hots Win Position for next draw
				foreach($hot_hits as $hots_pos)
				{
					$n = strstr($hots_pos, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
					$c = substr(strstr($hots_pos, '='), 1); // Strip off to the left of the equal sign count
					$this->data['lottery']->hots_pos[$n.'h'] = $c; 
				}
				// Interate Warms Win Positions for last draw
				foreach($warm_hits_last as $warm_pos)
				{
					$n = strstr($warm_pos, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
					$c = substr(strstr($warm_pos, '='), 1); // Strip off to the left of the equal sign count
					$this->data['lottery']->warms_pos_last[$n.'w'] = $c; 
				}
				// Interate Warms Win Positions	for next draw
				foreach($warm_hits as $warm_pos)
				{
					$n = strstr($warm_pos, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
					$c = substr(strstr($warm_pos, '='), 1); // Strip off to the left of the equal sign count
					$this->data['lottery']->warms_pos[$n.'w'] = $c; 
				}
				// Iterate Colds Win Positions for last draw
				foreach($cold_hits_last as $cold_pos)
				{
					$n = strstr($cold_pos, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
					$c = substr(strstr($cold_pos, '='), 1); // Strip off to the left of the equal sign count
					$this->data['lottery']->colds_pos_last[$n.'c'] = $c; 
				}
				// Iterate Colds Win Positions for next draw
				foreach($cold_hits as $cold_pos)
				{
					$n = strstr($cold_pos, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
					$c = substr(strstr($cold_pos, '='), 1); // Strip off to the left of the equal sign count
					$this->data['lottery']->colds_pos[$n.'c'] = $c; 
				}
			}
			else
			{
				$this->session->set_flashdata('message', 'There is a problem with the H (Hots) - W (Warms) - C (Colds) over the last '.$range.' Draws.');
				redirect('admin/history');
			}
		}
		else
		{
			$this->session->set_flashdata('message', 'There is an No Hots, Warms, Colds Profile.  Calculate the H-W-C at the Lottery Profile Statistics, Recalc Checkbox.');
			redirect('admin/history');
		}
		if ($this->session->flashdata('message')) $this->data['message'] = $this->session->flashdata('message');
		else $this->data['message'] = '';
		//Don't forget to include the last drawn h-w-c
		$this->data['lottery']->hwc = explode('-',$hwc_history['h_w_c_last_1']);
		
		// For display purposes, always show the extra ball if the lottery has one
		// The $draw array (used for H-W-C matching/asterisks) respects extra_included setting
		// The $complete_draw array (used for visual display) always includes extra if lottery has one
		$complete_draw = $this->history_m->onlydrawn($this->data['lottery']->last_drawn, $this->data['lottery']->extra_ball, $dup);
		$this->data['lottery']->draw = $complete_draw;
		$this->data['lottery']->positions = $positions;
		$this->data['lottery']->positions_last = $positions_last;
		unset($draw);
		unset($positions);
		unset($positions_last);
		// Get H-W-C winners data for the Winners tab
		$hwc_stats = $this->statistics_m->get_hwc_stats($id);
		
		if(!empty($hwc_stats) && !empty($hwc_stats['wins'])) {
			$h_w_c_range = isset($hwc_stats['h_w_c_range']) ? $hwc_stats['h_w_c_range'] : '';
			$this->data['hwc_winners'] = $this->parse_hwc_winners($hwc_stats['wins'], $id, $h_w_c_range);
			
				// Pass H-W-C Last 10 and Last 100 data to the view
			$this->data['h_w_c_last_10'] = isset($hwc_stats['h_w_c_last_10']) ? $hwc_stats['h_w_c_last_10'] : '';
			$this->data['h_w_c_range'] = $h_w_c_range;
		} else {
			$this->data['hwc_winners'] = array();
			$this->data['h_w_c_last_10'] = '';
			$this->data['h_w_c_range'] = '';
		}
		// Pass lottery draws count and range for labelling
		$this->data['hwc_balls_drawn'] = $drawn;
		$this->data['hwc_range'] = isset($h_w_c['range']) ? $h_w_c['range'] : 100;
		
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the Statistics menu
		$this->session->set_userdata('uri', 'admin/'.$this->data['current']);
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	 
		$this->data['subview'] = 'admin/dashboard/history/h_w_c';
		$this->data['history'] = $this;										// Access the methods in the view
		$this->load->view('admin/_layout_main', $this->data);
	}

/**
	 * View the follower numbers after the current draw. Default is set at 100 draws.
	 * 
	 * @param		$id		current id of Lottery related to the draw database of the lottery	
	 * @return      none
	 */
	public function followers($id)
	{
		$this->data['message'] = '';	// Defaulted to No Error Messages
		$this->data['lottery'] = $this->lotteries_m->get($id);
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		$drawn = $this->data['lottery']->balls_drawn;		// Get the number of balls drawn for this lottory, Pick 5, Pick 6, Pick 7, etc.
		$last_drawn = $this->lotteries_m->last_draw_db($tbl_name);
		$ld = $last_drawn->draw_date;				// Return last draw date
		$day = $this->lotteries_m->return_day($ld);	// Returns the day of draw, Saturday, Sunday, etc.
		$this->data['lottery']->next_draw_date = $this->lotteries_m->next_date($this->data['lottery'], $day, $ld);
		// Check to see if the actual table exists in the db?
		if (!$this->lotteries_m->lotto_table_exists($tbl_name))
		{
			$this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. '.$tbl_name.' Does not exist. Create the Lottery Database now.');
			redirect('admin/statistics');
		}
		$this->data['lottery']->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl_name);	// Retrieve the last drawn numbers and draw date
		// 1. Check for a record for the current lottery in the followers table
		$p_group = $this->statistics_m->prize_group_profile($id); // Prize Group Profile Only
		$p_group = $this->statistics_m->prizes_only($p_group,$this->data['lottery']->extra_ball);
		
		// Store the valid prize categories for filtering display
		$this->data['lottery']->valid_prize_categories = array_keys($p_group);
		
		$followers = $this->statistics_m->followers_exists($id);		// Existing follower row 
		if(!is_null($followers))
		{
			$range = $followers['range'];
			// 2. Extract the details of follower with exctra included and / or extra draws 
			$this->data['lottery']->extra_included = isset($followers['extra_included']) ? $followers['extra_included'] : 1;
			$this->data['lottery']->extra_draws = isset($followers['extra_draws']) ? $followers['extra_draws'] : 0;
			// 3. Create the structure for the last draw and the current wins for each number drawn
			$this->data['lottery']->last_drawn = $this->history_m->last_draw_prizegroup($this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_ball, $p_group); 
			
			// Check if this is an independent extra ball lottery and if enhanced wins data exists
			if ($this->data['lottery']->duplicate_extra_ball) {
				$enhanced_wins = $this->get_enhanced_follower_wins($id, $range);
				if ($enhanced_wins) {
					// Check if data exists - don't overwrite existing calculations
					if ($enhanced_wins && !empty($enhanced_wins->wins)) {
						// Use existing wins data regardless of format
						$this->data['lottery']->enhanced_wins = true;  // Boolean flag for view
						$this->data['lottery']->enhanced_wins_data = $enhanced_wins;  // Actual data
						
						// Parse wins data (handle both old and new formats)
						if (strpos($enhanced_wins->wins, '#') !== false) {
							// New format with # separator
							$this->data['lottery']->parsed_wins = $this->statistics_m->parse_wins_string_with_separator($enhanced_wins->wins);
						} else {
							// Old format without # separator  
							$this->data['lottery']->parsed_wins = $this->statistics_m->parse_wins_string_with_separator_OLD($enhanced_wins->wins, $this->data['lottery']->maximum_ball, $this->data['lottery']->maximum_extra_ball, $id);
						}
						
						$this->data['lottery']->parsed_positions = $this->parse_positions_string_enhanced($enhanced_wins->positions, $id);
						
						// Add dupextra position data for independent extra ball lotteries
						if ($this->data['lottery']->duplicate_extra_ball == 1 && !empty($enhanced_wins->dupextra_wins)) {
							$dupextra_position_data = $this->parse_dupextra_position_data($enhanced_wins->dupextra_wins);
							
							// Add position 6 (extra ball position) to parsed_positions for point ranking calculation
							$extra_position_index = $this->data['lottery']->balls_drawn + 1; // Position 6 for 5+1 lottery
							if (isset($dupextra_position_data['position_' . $extra_position_index])) {
								$this->data['lottery']->parsed_positions['position_' . $extra_position_index] = $dupextra_position_data['position_' . $extra_position_index];
							}
						}
						
						// Set data with the names the view expects
						$this->data['lottery']->enhanced_parsed_wins = $this->data['lottery']->parsed_wins;
						$this->data['lottery']->enhanced_parsed_positions = $this->data['lottery']->parsed_positions;
						
						// Parse dupextra_wins data for independent extra ball lotteries BEFORE calculating point rankings
						if (!empty($enhanced_wins->dupextra_wins)) {
							$this->data['lottery']->parsed_dupextra_wins = $this->parse_dupextra_wins_string($enhanced_wins->dupextra_wins, $this->data['lottery']->valid_prize_categories);
						}
						
						// Merge dupextra data into parsed_wins for point ranking calculation
						$merged_wins = $this->data['lottery']->parsed_wins;
						if (!empty($this->data['lottery']->parsed_dupextra_wins) && isset($this->data['lottery']->last_drawn['extra'])) {
							// Only include the specific extra ball that was actually drawn
							$drawn_extra_ball = $this->data['lottery']->last_drawn['extra'];
							$extra_key = 'extra_' . $drawn_extra_ball;
							
							if (isset($this->data['lottery']->parsed_dupextra_wins[$extra_key])) {
								// Add the drawn extra ball's wins using the actual ball number as the key
								$merged_wins[$drawn_extra_ball] = $this->data['lottery']->parsed_dupextra_wins[$extra_key];
							}
						}
						
						// Calculate point rankings including dupextra data
						$this->data['lottery']->enhanced_point_rankings = $this->calculate_point_rankings(
							$merged_wins, 
							$this->data['lottery']->parsed_positions, 
							$this->data['lottery']->valid_prize_categories
						);
						
						// Convert parsed position data to last_drawn position fields for view compatibility
						$this->convert_parsed_positions_to_last_drawn($this->data['lottery']->parsed_positions);
						
						// Get the actual drawn ball numbers for enhanced display
						$last_draw_data = $this->statistics_m->db_row($tbl_name, 0);
						if ($last_draw_data) {
							// Set actual drawn ball numbers
							for ($i = 1; $i <= $this->data['lottery']->balls_drawn; $i++) {
								$ball_field = 'ball' . $i;
								if (isset($last_draw_data->$ball_field)) {
									$this->data['lottery']->last_drawn[$ball_field] = $last_draw_data->$ball_field;
								}
							}
							// Set extra ball if it exists
							if ($this->data['lottery']->extra_included && isset($last_draw_data->extra)) {
								$this->data['lottery']->last_drawn['extra'] = $last_draw_data->extra;
							}
						}
					} else {
						// Still no data, fall back to regular display
						$this->data['lottery']->enhanced_wins = false;
						$this->setup_regular_follower_display($followers, $drawn, $p_group);
					}
				} else {
					// No enhanced data exists, try to generate it on-demand
					
					// Try to generate enhanced data for this lottery
					$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
					
					try {
						// Use the enhanced follower calculation methods we fixed earlier
						$enhanced_ball_wins = $this->statistics_m->calculate_independent_extra_follower_wins_OLD($tbl_name, $id, $range);
						$enhanced_position_wins = $this->statistics_m->calculate_independent_extra_follower_positions_OLD($tbl_name, $id, $range);
						
						if (!empty($enhanced_ball_wins) && !empty($enhanced_position_wins)) {
							// Convert to the format expected by the view
							$this->data['lottery']->enhanced_wins = true;
							$this->data['lottery']->parsed_wins = $enhanced_ball_wins;
							$this->data['lottery']->parsed_positions = $enhanced_position_wins;
							
							// Add dupextra position data for independent extra ball lotteries
							if ($this->data['lottery']->duplicate_extra_ball == 1 && !empty($this->data['followers']['dupextra_wins'])) {
								$dupextra_position_data = $this->parse_dupextra_position_data($this->data['followers']['dupextra_wins']);
								
								// Add position 6 (extra ball position) to parsed_positions for point ranking calculation
								$extra_position_index = $this->data['lottery']->balls_drawn + 1; // Position 6 for 5+1 lottery
								if (isset($dupextra_position_data['position_' . $extra_position_index])) {
									$this->data['lottery']->parsed_positions['position_' . $extra_position_index] = $dupextra_position_data['position_' . $extra_position_index];
								}
							}
							
							// Get the actual drawn ball numbers for enhanced display FIRST
							$last_draw_data = $this->statistics_m->db_row($tbl_name, 0);
							if ($last_draw_data) {
								// Set actual drawn ball numbers
								for ($i = 1; $i <= $this->data['lottery']->balls_drawn; $i++) {
									$ball_field = 'ball' . $i;
									if (isset($last_draw_data->$ball_field)) {
										$this->data['lottery']->last_drawn[$ball_field] = $last_draw_data->$ball_field;
									}
								}
								// Set extra ball if it exists
								if ($this->data['lottery']->extra_included && isset($last_draw_data->extra)) {
									$this->data['lottery']->last_drawn['extra'] = $last_draw_data->extra;
								}
							}
							
							// Parse and merge dupextra data for independent extra ball lotteries
							$merged_ball_wins = $enhanced_ball_wins;
							if ($this->data['lottery']->duplicate_extra_ball == 1 && !empty($this->data['followers']['dupextra_wins'])) {
								$parsed_dupextra_wins = $this->parse_dupextra_wins_string($this->data['followers']['dupextra_wins'], $this->data['lottery']->valid_prize_categories);
								
								// Use the drawn extra ball we just retrieved
								if (isset($this->data['lottery']->last_drawn['extra'])) {
									$drawn_extra_ball = $this->data['lottery']->last_drawn['extra'];
									$extra_key = 'extra_' . $drawn_extra_ball;
									
									if (isset($parsed_dupextra_wins[$extra_key])) {
										// Add the drawn extra ball's wins using the actual ball number as the key
										$merged_ball_wins[$drawn_extra_ball] = $parsed_dupextra_wins[$extra_key];
									}
								}
							}
							
							$this->data['lottery']->enhanced_point_rankings = $this->calculate_point_rankings($merged_ball_wins, $this->data['lottery']->parsed_positions, $this->data['lottery']->valid_prize_categories);
							
							// Convert parsed position data to last_drawn position fields for view compatibility
							$this->convert_parsed_positions_to_last_drawn($this->data['lottery']->parsed_positions);
						} else {
							throw new Exception("Enhanced calculation returned empty data");
						}
					} catch (Exception $e) {
						// Enhanced generation failed, fall back to regular display
						$this->data['lottery']->enhanced_wins = false;
						$this->setup_regular_follower_display($followers, $drawn, $p_group);
					}
				}
			} else {
				// Regular lottery - use standard follower display
				$this->data['lottery']->enhanced_wins = false;
				$this->setup_regular_follower_display($followers, $drawn, $p_group);
			}
		}
		else // The prize details have not been found or instantiated
		{
			$this->session->set_flashdata('message', 'There are no follower prize details. Calculate the Followers at the Lottery Profile Statistics, Recalc Checkbox.');
			redirect('admin/history');
		}
		$this->data['lottery']->last_drawn['range'] = $range;
		
		$this->data['current'] = $this->uri->segment(2); 				// Sets the Admins Menu Highlighted
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/followers'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	 
		$this->data['subview']  = 'admin/dashboard/history/followers';
		$this->load->view('admin/_layout_main', $this->data);
	}

	/**
	 * Get enhanced follower wins data for independent extra ball lotteries
	 */
	private function get_enhanced_follower_wins($lottery_id, $range)
	{
		return $this->db->select('wins, positions, dupextra_wins')
						->where('lottery_id', $lottery_id)
						->where('range', $range)
						->get('lottery_followers')
						->row();
	}

	/**
	 * Setup regular follower display (non-independent extra ball lotteries)
	 */
	private function setup_regular_follower_display($followers, $drawn, $p_group)
	{
		// Preserve the original ball numbers before processing
		$original_balls = array();
		for ($i = 1; $i <= $this->data['lottery']->balls_drawn; $i++) {
			if (isset($this->data['lottery']->last_drawn['ball'.$i])) {
				$original_balls['ball'.$i] = $this->data['lottery']->last_drawn['ball'.$i];
			}
		}
		if (isset($this->data['lottery']->last_drawn['extra'])) {
			$original_balls['extra'] = $this->data['lottery']->last_drawn['extra'];
		}
		
		// 4. extract the win record for each number into an array
		$follower_wins = explode(">", $followers['wins']);
		$follow_poswins = explode(">", $followers['positions']);
		// 5. Only populate the numbers with the win record that was actually drawn
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_addwins($this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included,$p_group,$follower_wins,$follow_poswins);
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_addpoints($this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->duplicate_extra_ball);
		
		// Restore the original ball numbers (in case they were overwritten)
		foreach ($original_balls as $key => $value) {
			$this->data['lottery']->last_drawn[$key] = $value;
		}
	}

	/**
	 * Parse wins string into displayable format
	 */
	private function parse_wins_string($wins_string)
	{
		$parsed = array();
		$numbers = explode('<', $wins_string);
		
		foreach ($numbers as $number_data) {
			if (empty($number_data)) continue;
			
			$parts = explode('>', $number_data);
			if (count($parts) != 2) continue;
			
			$number = $parts[0];
			$wins = explode(',', $parts[1]);
			
			$parsed[$number] = $wins;
		}
		
		return $parsed;
	}

	/**
	 * Parse positions string for enhanced independent extra ball lotteries
	 */
	private function parse_positions_string_enhanced($positions_string, $lottery_id = 15)
	{
		$parsed = array();
		
		if (empty($positions_string)) {
			return $parsed;
		}
		
		// Fixed category mapping for Daily Grand (lottery 15)
		$category_names = ['extra', '1_win_extra', '2_win', '2_win_extra', '3_win', '3_win_extra', '4_win', '4_win_extra', '5_win', '5_win_extra'];
		
		// For enhanced lotteries, positions are separated by '>' like wins
		// Format: pos1_data>pos2_data>pos3_data>pos4_data>pos5_data>extra_data
		$entries = explode('>', $positions_string);
		$position_index = 1;
		// Parse each position (1 through max positions + extra)
		foreach ($entries as $entry) {
			if (empty($entry)) continue;
			
			$values = explode(',', $entry);
			
			// Map numeric indexes to category names
			$categorized_wins = array();
			foreach ($values as $cat_idx => $count) {
				if (isset($category_names[$cat_idx])) {
					$categorized_wins[$category_names[$cat_idx]] = intval($count);
				}
			}
			
			$parsed['position_' . $position_index] = $categorized_wins;
			$position_index++;
		}
		
		return $parsed;
	}

	/**
	 * Parse positions string into displayable format
	 */
	private function parse_positions_string($positions_string)
	{
		$parsed = array();
		$positions = explode('<', $positions_string);
		
		foreach ($positions as $position_data) {
			if (empty($position_data)) continue;
			
			$parts = explode('>', $position_data);
			if (count($parts) != 2) continue;
			
			$position = $parts[0];
			$wins = explode(',', $parts[1]);
			
			$parsed[$position] = $wins;
		}
		
		return $parsed;
	}

	/**
	 * Calculate point rankings based on the point system for independent extra ball lotteries
	 */
	private function calculate_point_rankings($parsed_wins, $parsed_positions, $valid_categories = null)
	{
		$ball_rankings = array();
		$position_rankings = array();
		
		// Point mapping for enhanced format with associative keys
		$category_points = array(
			'extra' => 1,
			'1_win' => 2,
			'1_win_extra' => 3,
			'2_win' => 4,
			'2_win_extra' => 5,
			'3_win' => 6,
			'3_win_extra' => 7,
			'4_win' => 8,
			'4_win_extra' => 9,
			'5_win' => 10,
			'5_win_extra' => 11,
			'6_win' => 12,
			'6_win_extra' => 13,
			'7_win' => 14,
			'7_win_extra' => 15,
			'8_win' => 16,
			'8_win_extra' => 17,
			'9_win' => 18,
			'9_win_extra' => 19
		);
		
		// Calculate points for each ball (number wins)
		foreach ($parsed_wins as $number => $wins) {
			$total_points = 0;
			
			foreach ($wins as $category => $count) {
				// Skip categories not in the valid prize categories for duplicate_extra_ball lotteries
				if ($valid_categories && !in_array($category, $valid_categories)) {
					continue;
				}
				
				if (isset($category_points[$category])) {
					$total_points += intval($count) * $category_points[$category];
				}
			}
			
			$ball_rankings[$number] = $total_points;
		}
		
		// Calculate points for each position
		foreach ($parsed_positions as $position => $position_wins) {
			$total_points = 0;
			
			// Special handling for position 6 with pre-calculated total_points
			if (isset($position_wins['total_points'])) {
				$total_points = $position_wins['total_points'];
			} else {
				// Normal calculation for other positions
				foreach ($position_wins as $category => $count) {
					// Skip categories not in the valid prize categories for duplicate_extra_ball lotteries
					if ($valid_categories && !in_array($category, $valid_categories)) {
						continue;
					}
					
					if (isset($category_points[$category])) {
						$points_for_category = intval($count) * $category_points[$category];
						$total_points += $points_for_category;
					}
				}
			}
			
			// Convert position key from 'position_6' to '6' for view compatibility
			$position_number = str_replace('position_', '', $position);
			$position_rankings[$position_number] = $total_points;
		}
		
		// Sort by points (highest first)
		arsort($ball_rankings);
		arsort($position_rankings);
		
		return array(
			'balls' => $ball_rankings,
			'positions' => $position_rankings
		);
	}

	/**
	 * View the friends of drawn numbers that most often are drawn with this number. Default is 100 draws.
	 * 
	 * @param		$id		current lottery id for the draw database	
	 * @return  	none
	 */
	public function friends($id)
	{
		$this->data['message'] = '';	// Defaulted to No Error Messages
		$this->data['lottery'] = $this->lotteries_m->get($id);
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		$last_drawn = $this->lotteries_m->last_draw_db($tbl_name);
		$ld = $last_drawn->draw_date;				// Return last draw date
		$day = $this->lotteries_m->return_day($ld);	// Returns the day of draw, Saturday, Sunday, etc.
		$this->data['lottery']->next_draw_date = $this->lotteries_m->next_date($this->data['lottery'], $day, $ld);
		// Check to see if the actual table exists in the db?
		if (!$this->lotteries_m->lotto_table_exists($tbl_name))
		{
			$this->session->set_flashdata('message', 'There are no friendship prize details. Calculate the Friends at the Lottery Profile Statistics, Recalc Checkbox');
			redirect('admin/history');
		}
		$this->data['lottery']->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl_name);	// Retrieve the last drawn numbers and draw date

		$friends = $this->statistics_m->friends_exists($id);
		$nonfriends = $this->statistics_m->nonfriends_exists($id);
		if(!is_null($friends)&&(!is_null($nonfriends)))
		{
			$this->data['lottery']->extra_included = $friends['extra_included'];
			$this->data['lottery']->extra_draws = $friends['extra_draws'];
			$range = $friends['range'];
			// 4. Extract the friends string into the array counter parts
			$next_draw = explode(",", $friends['lottery_friends']); // DB or ??
			$nonfriends_draw = explode("|", $nonfriends['lottery_nonfriends']); // DB
			$b = 1;
			foreach($next_draw as $all_balls)
			{
				$n = strstr($all_balls, '>', TRUE); // Strip off each number
				$d = substr($all_balls, strpos($all_balls, "|") + 1);
				$tm = strstr($all_balls, '|', TRUE);
				$c = substr($tm, strpos($tm, ">") + 1);    // Strip off the count
				$this->data['lottery']->friend['ball'.$b] = $n; 
				$this->data['lottery']->friend['count'.$b] = $c;
				$this->data['lottery']->friend['date'.$b] = $d;
				$this->data['lottery']->nonfriends['ball'.$b] = $nonfriends_draw[$b-1];  // Array is zero based
				$b++;
			}
			if(isset($friends['wins']) && !empty($friends['wins'])) 
			{
				// Friend only wins
				$wins = explode("|", $friends['wins']); // $wins[0]  = broken like this nofriends,1-wayfriends,2-wayfriends & wins[1] = 1 - 49 (canada 649 for example), 1-way or 2 way friends 
				$direction = explode(",", $wins[0]); // no friends ($direction[0]), 1 - way ($direction[1]) and 2 - way ($direction[2])
				$this->data['lottery']->friend['nofriends'] = $direction[0];
				$this->data['lottery']->friend['1-way'] = $direction[1];
				$this->data['lottery']->friend['2-way'] = $direction[2];
				$ball_friend = explode(',', $wins[1]);
				// Zero-based, so all balls drawn start at ba1l 1
				foreach($ball_friend as $friend => $direct)
				{
					$this->data['lottery']->friend['ball_friend'.($friend+1)] = $direct;
				} 
			}
			else
			{
				$this->session->set_flashdata('message', 'There is no win information associated with this lottery. Select Lottery Profile Statistics in dropdown, Recalc Checkbox');
				redirect('admin/history');
			}	
		}
		else
		{
			$this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. '.$tbl_name.' Does not exist. Create the Lottery Database now.');
			redirect('admin/history');
		}
		$this->data['lottery']->last_drawn['range'] = $range;
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/friends'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	 
		$this->data['subview']  = 'admin/dashboard/history/friends';
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
		return anchor($uri, '<i class="fa fa-eye-slash fa-2x" aria-hidden="true">', array('title' => 'View the latest winning opportunities for this lottery with the at a glance option'));
	}

	/**
	 * View the Results of the Hot Warm Cold Numbers as it applies to the H-W-C
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_hwc($uri) 
	{
		return anchor($uri, '<i class="fa fa-thermometer-full fa-2x" aria-hidden="true">', array('title' => 'View the actual win results of H-W-C from positional values'));
	}

	/**
	 * View the results of historic wins from  Followers of the last draw
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_followers($uri)
	{
		return anchor($uri, '<i class="fa fa-retweet fa-2x" aria-hidden="true">', array('title' => 'View the actual results of follower wins'));
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
	public function btn_friends($uri)
	{
		return anchor($uri, '<i class="fa fa-history fa-2x" aria-hidden="true">', array('title' => 'Calculate the Current History or Update to the latest Draw', 'class' => 'calculate'));
	}
	/**
	 * go to a form that lists the number of combination files 
	 * Files generate combinations calls to html and php.
	 * @param       integer	$id		Lottery Identifier
	 * @return      none
	 */
	public function calculate_combo($id)
	{
		$this->data['message'] = ''; // Defaulted to No Messagesa
		$this->data['lottery'] = $this->lotteries_m->get($id);
		$this->data['lottery']->generate = $this->combination_files_m->lottery_combination_files($id); //$this->combination_files_m->all_combination_files();
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->data['predictions'] = $this;		// Access the methods in the view
		$this->data['subview'] = 'admin/dashboard/history/calculate_combo';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	/**
	 * Parse dupextra_wins string into displayable format
	 * Format: "prize1,prize2,prize3>prize1,prize2,prize3>..." where each section represents prizes for an extra ball
	 * 
	 * @param string $dupextra_wins_string The dupextra_wins string from database
	 * @param array $valid_categories Array of valid prize categories (non-NULL)
	 * @return array Parsed dupextra wins data
	 */
	private function parse_dupextra_wins_string($dupextra_wins_string, $valid_categories)
	{
		$parsed = array();
		
		if (empty($dupextra_wins_string)) {
			return $parsed;
		}
		
		// Split by '>' to get prizes for each extra ball
		$extra_ball_prizes = explode('>', $dupextra_wins_string);
		
		// Define all possible prize categories in order
		$all_categories = array('extra', '1_win', '1_win_extra', '2_win', '2_win_extra', '3_win', '3_win_extra', '4_win', '4_win_extra', '5_win', '5_win_extra', '6_win', '6_win_extra', '7_win', '7_win_extra');
		
		// Filter to only include valid (non-NULL) categories
		$prize_categories = array();
		foreach($all_categories as $category) {
			if(in_array($category, $valid_categories)) {
				$prize_categories[] = $category;
			}
		}
		
		// Process the single extra ball result - calculate_dupextra_wins now only returns data for drawn extra ball
		$extra_num = 1;
		if(isset($extra_ball_prizes[$extra_num - 1]) && !empty($extra_ball_prizes[$extra_num - 1])) {
			$prizes = explode(',', $extra_ball_prizes[$extra_num - 1]);
			
			// Get the drawn extra ball number for the key
			$drawn_extra = isset($this->data['lottery']->last_drawn['extra']) ? intval($this->data['lottery']->last_drawn['extra']) : 1;
			$parsed['extra_' . $drawn_extra] = array();
			
			foreach($prizes as $index => $count) {
				if(isset($prize_categories[$index]) && intval($count) > 0) {
					$parsed['extra_' . $drawn_extra][$prize_categories[$index]] = intval($count);
				}
			}
		}
		
		return $parsed;
	}

	/**
	 * Convert parsed position data to last_drawn position fields for view compatibility
	 * For independent extra ball lotteries, use dupextra_wins for the extra ball position
	 */
	private function convert_parsed_positions_to_last_drawn($parsed_positions)
	{
		// Convert each position's data to the format expected by the view
		for ($i = 1; $i <= $this->data['lottery']->balls_drawn; $i++) {
			if (isset($parsed_positions['position_' . $i])) {
				$this->data['lottery']->last_drawn['position' . $i . '_win'] = $parsed_positions['position_' . $i];
			}
		}
		
		// Handle extra ball position for independent extra ball lotteries
		if ($this->data['lottery']->extra_included) {
			if ($this->data['lottery']->duplicate_extra_ball == 1) {
				// Independent extra ball lottery - parse dupextra_wins for position 6 (extra ball position)
				if (!empty($this->data['followers']['dupextra_wins'])) {
					$dupextra_position_data = $this->parse_dupextra_position_data($this->data['followers']['dupextra_wins']);
					
					// Position 6 is the extra ball position, use dupextra_wins data for this position
					$extra_position_index = $this->data['lottery']->balls_drawn + 1; // Position 6 for 5+1 lottery
					if (isset($dupextra_position_data['position_' . $extra_position_index])) {
						$position_6_data = $dupextra_position_data['position_' . $extra_position_index];
						$this->data['lottery']->last_drawn['position_extra_win'] = $position_6_data;
						
						// Set the total points for position 6 (either pre-calculated or calculate from categories)
						if (isset($position_6_data['total_points'])) {
							$this->data['lottery']->last_drawn['position_extra_total'] = $position_6_data['total_points'];
						}
					}
				}
			} else {
				// Regular extra ball lottery - use regular position data
				if (isset($parsed_positions['position_extra'])) {
					$this->data['lottery']->last_drawn['position_extra_win'] = $parsed_positions['position_extra'];
				}
			}
		}
	}

	/**
	 * Parse dupextra_wins string to extract position-specific data
	 */
	private function parse_dupextra_position_data($dupextra_wins_string)
	{
		$parsed = array();
		
		if (empty($dupextra_wins_string)) {
			return $parsed;
		}
		
		// Get valid prize categories
		$p_group = $this->statistics_m->prize_group_profile($this->data['lottery']->id);
		$p_group = $this->statistics_m->prizes_only($p_group, $this->data['lottery']->extra_ball);
		$valid_categories = array_keys($p_group);
		$prize_categories = $valid_categories; // Use the category names, not the values
		
		// Split by '>' to get prizes for each position
		$position_prizes = explode('>', $dupextra_wins_string);
		
		// Point system for calculating totals
		$category_points = array(
			'extra' => 1,
			'1_win' => 2, '1_win_extra' => 3,
			'2_win' => 4, '2_win_extra' => 5,
			'3_win' => 6, '3_win_extra' => 7,
			'4_win' => 8, '4_win_extra' => 9,
			'5_win' => 10, '5_win_extra' => 11,
			'6_win' => 12, '6_win_extra' => 13,
			'7_win' => 14, '7_win_extra' => 15,
			'8_win' => 16, '8_win_extra' => 17,
			'9_win' => 18, '9_win_extra' => 19
		);
		
		foreach ($position_prizes as $position_index => $position_data) {
			if (empty($position_data)) continue;
			
			$position_num = $position_index + 1; // Position 1, 2, 3, etc.
			$prizes = explode(',', $position_data);
			
			$categorized_wins = array();
			foreach($prizes as $index => $count) {
				if(isset($prize_categories[$index]) && intval($count) > 0) {
					$categorized_wins[$prize_categories[$index]] = intval($count);
				}
			}
			
			if (!empty($categorized_wins)) {
				$parsed['position_' . $position_num] = $categorized_wins;
			}
		}
		
		// Special handling for position 6 (extra ball position)
		// Position 6 should be the SUM of all individual extra ball points (1-7)
		if ($this->data['lottery']->duplicate_extra_ball == 1) {
			$total_position_6_points = 0;
			$aggregated_categories = array(); // Aggregate category counts from all extra balls
			
			// Calculate individual points for each of the 7 positions (representing each extra ball 1-7)
			for ($position_index = 0; $position_index < 7 && $position_index < count($position_prizes); $position_index++) {
				if (isset($position_prizes[$position_index])) {
					$position_data = $position_prizes[$position_index];
					$prizes = explode(',', $position_data);
					
					$extra_ball_wins = array();
					foreach($prizes as $index => $count) {
						if(isset($prize_categories[$index]) && intval($count) > 0) {
							$extra_ball_wins[$prize_categories[$index]] = intval($count);
							
							// Aggregate category counts for position 6
							if (!isset($aggregated_categories[$prize_categories[$index]])) {
								$aggregated_categories[$prize_categories[$index]] = 0;
							}
							$aggregated_categories[$prize_categories[$index]] += intval($count);
						}
					}
					
					if (!empty($extra_ball_wins)) {
						// Calculate points for this extra ball
						$extra_ball_points = 0;
						foreach ($extra_ball_wins as $category => $count) {
							if (isset($category_points[$category])) {
								$extra_ball_points += intval($count) * $category_points[$category];
							}
						}
						
						$total_position_6_points += $extra_ball_points;
					}
				}
			}
			
			// Override position 6 with the aggregated category data and summed total
			if ($total_position_6_points > 0) {
				$aggregated_categories['total_points'] = $total_position_6_points;
				$parsed['position_6'] = $aggregated_categories;
			}
		}
		
		return $parsed;
	}
	
	/**
	 * Display H-W-C + Follower analysis: for every ball 1..max_ball, shows
	 * which H-W-C draw pattern produced the highest average follower hits
	 * in the next actual draw.  Followers are computed dynamically from the
	 * draw history (threshold >= 3, same as the existing follower system).
	 *
	 * URL: /admin/history/hwc_followers/{lottery_id}
	 *
	 * @param  int  $id  Lottery id
	 * @return void
	 */
	public function hwc_followers($id)
	{
		$this->data['message'] = '';
		$this->data['lottery'] = $this->lotteries_m->get($id);

		if (empty($this->data['lottery'])) {
			$this->session->set_flashdata('message', 'Lottery not found.');
			redirect('admin/history');
		}

		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);

		if (!$this->lotteries_m->lotto_table_exists($tbl_name)) {
			$this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. ' . $tbl_name . ' Does not exist.');
			redirect('admin/history');
		}

		// Require H-W-C data (provides range, H/W/C counts, classification strings)
		$h_w_c = $this->statistics_m->h_w_c_exists($id);
		if (is_null($h_w_c)) {
			$this->session->set_flashdata('message', 'No H-W-C profile found. Calculate H-W-C in Statistics first.');
			redirect('admin/history');
		}

		$range = min(500, intval($h_w_c['range']));

		$this->data['lottery']->last_drawn  = (array) $this->lotteries_m->last_draw_db($tbl_name);
		$this->data['lottery']->last_drawn['range'] = $range;
		$this->data['lottery']->extra_included = $h_w_c['extra_included'];
		$this->data['lottery']->extra_draws    = $h_w_c['extra_draws'];
		$this->data['lottery']->H = $h_w_c['h_count'];
		$this->data['lottery']->W = $h_w_c['w_count'];
		$this->data['lottery']->C = $h_w_c['c_count'];

		// Run the H-W-C + follower analysis across all balls
		$_hwc_result = $this->history_m->get_hwc_follower_stats(
			$tbl_name,
			$this->data['lottery']->balls_drawn,
			$this->data['lottery']->maximum_ball,
			$h_w_c['extra_included'],
			$h_w_c['extra_draws'],
			$h_w_c['h_count'],
			$h_w_c['w_count'],
			$h_w_c['hots'],
			$h_w_c['warms'],
			$h_w_c['colds'],
			$range,
			(bool) $this->data['lottery']->duplicate_extra_ball,
			intval($this->data['lottery']->maximum_extra_ball)
		);
		$this->data['hwc_follower_results'] = $_hwc_result['main'];
		$this->data['hwc_extra_results']    = $_hwc_result['extra'];
		$this->data['is_dup_extra']         = (bool) $this->data['lottery']->duplicate_extra_ball;
		$this->data['max_extra_ball']       = intval($this->data['lottery']->maximum_extra_ball);
		$this->data['min_extra_ball']       = intval($this->data['lottery']->minimum_extra_ball);

		// Parse HWC scores (ball => score) from hots/warms/colds strings
		$hwc_scores = array();
		foreach (array($h_w_c['hots'], $h_w_c['warms'], $h_w_c['colds']) as $_str) {
			foreach (explode(',', $_str) as $_entry) {
				if (strpos($_entry, '=') !== false) {
					list($_b, $_s) = explode('=', $_entry, 2);
					$_b = intval($_b); $_s = intval($_s);
					if ($_b > 0) $hwc_scores[$_b] = $_s;
				}
			}
		}
		$this->data['hwc_scores'] = $hwc_scores;

		// Compute follower points per ball (same logic as followers view)
		// Also used to rank last-drawn balls and find the best ball
		$best_points_ball     = 0;
		$best_points_val      = 0;
		$best_points_is_extra = false;
		$ball_points          = array(); // ball_number => follower pts from last draw
		$followers_data = $this->statistics_m->followers_exists($id);
		if (!is_null($followers_data)) {
			$p_group = $this->statistics_m->prize_group_profile($id);
			$p_group = $this->statistics_m->prizes_only($p_group, $this->data['lottery']->extra_ball);
			$tmp_last = (array) $this->lotteries_m->last_draw_db($tbl_name);
			$tmp_last = $this->history_m->last_draw_prizegroup($tmp_last, $this->data['lottery']->balls_drawn, $this->data['lottery']->extra_ball, $p_group);
			$follower_wins   = explode('>', $followers_data['wins']);
			$follow_poswins  = explode('>', $followers_data['positions']);
			$tmp_last = $this->history_m->last_draw_addwins($tmp_last, $this->data['lottery']->balls_drawn, $h_w_c['extra_included'], $p_group, $follower_wins, $follow_poswins);
			$tmp_last = $this->history_m->last_draw_addpoints($tmp_last, $this->data['lottery']->balls_drawn, $h_w_c['extra_included'], 0);
			$max_balls_pts = $this->data['lottery']->balls_drawn + ($h_w_c['extra_included'] ? 1 : 0);
			for ($i = 1; $i <= $max_balls_pts; $i++) {
				$is_extra_pos = ($i > $this->data['lottery']->balls_drawn);
				$wins_arr = $is_extra_pos
					? (isset($tmp_last['extra_win'])    ? $tmp_last['extra_win']    : array())
					: (isset($tmp_last['ball'.$i.'_win']) ? $tmp_last['ball'.$i.'_win'] : array());
				$pts = 0;
				foreach ($wins_arr as $key => $value) {
					if (strpos($key, '_points') !== false) $pts += intval($value);
				}
				$ball_num = $is_extra_pos ? intval($tmp_last['extra']) : intval($tmp_last['ball'.$i]);
				$ball_points[$ball_num] = $pts;
				if ($pts > $best_points_val) {
					$best_points_val      = $pts;
					$best_points_is_extra = $is_extra_pos;
					$best_points_ball     = $ball_num;
				}
			}
		}
		$this->data['best_points_ball']     = $best_points_ball;
		$this->data['best_points_val']      = $best_points_val;
		$this->data['best_points_is_extra'] = $best_points_is_extra;
		$this->data['ball_points']          = $ball_points;

		// Build last-drawn main ball list, sorted by follower points desc
		$_last = $this->data['lottery']->last_drawn;
		$_last_drawn_balls = array();
		for ($_i = 1; $_i <= $this->data['lottery']->balls_drawn; $_i++) {
			if (!empty($_last['ball'.$_i])) $_last_drawn_balls[] = intval($_last['ball'.$_i]);
		}
		// Include extra ball in main list for non-dup-extra lotteries (same pool)
		if (!$this->data['lottery']->duplicate_extra_ball && !empty($_last['extra'])) {
			$_last_drawn_balls[] = intval($_last['extra']);
		}
		usort($_last_drawn_balls, function($a, $b) use ($ball_points) {
			$sa = isset($ball_points[$a]) ? $ball_points[$a] : 0;
			$sb = isset($ball_points[$b]) ? $ball_points[$b] : 0;
			return $sb - $sa;
		});
		$this->data['last_drawn_balls']      = $_last_drawn_balls;
		// Track the extra ball number for all lotteries — view uses it to apply grey styling
		$this->data['last_drawn_extra_ball'] = !empty($_last['extra']) ? intval($_last['extra']) : 0;

		if ($this->session->flashdata('message')) $this->data['message'] = $this->session->flashdata('message');
		else $this->data['message'] = '';

		$this->data['current'] = $this->uri->segment(2);
		$this->session->set_userdata('uri', 'admin/' . $this->data['current']);
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users']    = $this->maintenance_m->logged_online(0);
		$this->data['admins']   = $this->maintenance_m->logged_online(1);
		$this->data['visitors'] = $this->maintenance_m->active_visitors();
		$this->data['subview']  = 'admin/dashboard/history/hwc_followers';
		$this->data['history']  = $this;
		$this->load->view('admin/_layout_main', $this->data);
	}

	/**
	 * H-W-C + Followers analysis icon button
	 */
	public function btn_hwc_followers($uri)
	{
		return anchor($uri, '<i class="fa fa-fire fa-2x" aria-hidden="true">', array('title' => 'H-W-C + Follower analysis: best H-W-C pattern per ball'));
	}

	/**
	 * View the H-W-C winners based on calculated statistics and points
	 * 
	 * @param		$id		current id of Lottery related to the draw database of the lottery	
	 * @return      none
	 */
	public function h_w_c_winners($id)
	{
		$this->data['message'] = '';	// Defaulted to No Error Messages
		$this->data['lottery'] = $this->lotteries_m->get($id);
		
		// Check if lottery exists
		if(empty($this->data['lottery'])) {
			$this->session->set_flashdata('message', 'Lottery not found.');
			redirect('admin/history');
		}
		
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		$drawn = $this->data['lottery']->balls_drawn;		// Get the number of balls drawn for this lottery, Pick 5, Pick 6, Pick 7, etc.
		
		// Check to see if the actual table exists in the db?
		if (!$this->lotteries_m->lotto_table_exists($tbl_name))
		{
			$this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. '.$tbl_name.' Does not exist. Create the Lottery Database now.');
			redirect('admin/history');
		}
		
		// Get H-W-C statistics data
		$hwc_stats = $this->statistics_m->get_hwc_stats($id);
		if(empty($hwc_stats) || empty($hwc_stats['wins'])) {
			$this->session->set_flashdata('message', 'No H-W-C winner statistics found. Please recalculate H-W-C statistics first.');
			redirect('admin/history');
		}
		
		// Parse the wins string and calculate points, filtering by occurrence counts if available
		$h_w_c_range = isset($hwc_stats['h_w_c_range']) ? $hwc_stats['h_w_c_range'] : '';
		$this->data['hwc_winners'] = $this->parse_hwc_winners($hwc_stats['wins'], $id, $h_w_c_range);
		
		// Get lottery profile information for display
		$this->data['lottery']->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl_name);
		$h_w_c = $this->statistics_m->h_w_c_exists($id);
		if(!is_null($h_w_c)) {
			$this->data['lottery']->last_drawn['range'] = $h_w_c['range'];
			$this->data['lottery']->extra_included = $h_w_c['extra_included'];
			$this->data['lottery']->extra_draws = $h_w_c['extra_draws'];
		}
		
		if ($this->session->flashdata('message')) $this->data['message'] = $this->session->flashdata('message');
		else $this->data['message'] = '';
		
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the History menu
		$this->session->set_userdata('uri', 'admin/'.$this->data['current']);
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	 
		$this->data['subview'] = 'admin/dashboard/history/h_w_c_winners';
		$this->data['history'] = $this;										// Access the methods in the view
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	/**
	 * Parse H-W-C winners string and calculate points based on follower win system
	 * 
	 * @param		string	$wins_string	The encoded wins string from database
	 * @param		int		$lottery_id		Lottery ID for prize profile lookup
	 * @return		array					Array of H-W-C patterns with points sorted by points desc
	 */
	private function parse_hwc_winners($wins_string, $lottery_id, $h_w_c_range = '')
	{
		$winners = array();
		
		// Parse H-W-C occurrence counts to filter out patterns that never occurred
		$hwc_counts = array();
		if (!empty($h_w_c_range)) {
			$items = explode(',', $h_w_c_range);
			foreach ($items as $item) {
				$parts = explode('=', $item);
				if (count($parts) == 2) {
					$label = trim($parts[0]);
					$total = (int)trim($parts[1]);
					$hwc_counts[$label] = $total;
					
					// Also store without spaces for matching flexibility
					$label_no_spaces = str_replace(' ', '', $label);
					if ($label_no_spaces != $label) {
						$hwc_counts[$label_no_spaces] = $total;
					}
				}
			}
		}
		
		// Get prize profile for this lottery to determine point values
		$prize_profile = $this->statistics_m->get_lottery_prize_profile($lottery_id);
		if(empty($prize_profile)) {
			return $winners;
		}		// Define point system based on follower wins (from user documentation)
		$category_points = array(
			'extra' => 1,		// Extra/Bonus Ball Only = 1 point
			'2_win' => 4,		// 2 Balls = 4 points  
			'2_win_extra' => 5,	// 2 Balls + Extra = 5 points
			'3_win' => 6,		// 3 Balls = 6 points
			'3_win_extra' => 7,	// 3 Balls + Extra = 7 points
			'4_win' => 8,		// 4 Balls = 8 points
			'4_win_extra' => 9,	// 4 Balls + Extra = 9 points
			'5_win' => 10,		// 5 Balls = 10 points
			'5_win_extra' => 11,// 5 Balls + Extra = 11 points
			'6_win' => 12,		// 6 Balls = 12 points
			'6_win_extra' => 13,// 6 Balls + Extra = 13 points
			'7_win' => 14,		// 7 Balls = 14 points
			'7_win_extra' => 15,// 7 Balls + Extra = 15 points
			'8_win' => 16,		// 8 Balls = 16 points
			'8_win_extra' => 17,// 8 Balls + Extra = 17 points
			'9_win' => 18,		// 9 Balls = 18 points
			'9_win_extra' => 19	// 9 Balls + Extra = 19 points
		);
		
		// Split the wins string by pipe separator
		$hwc_entries = explode('|', $wins_string);
		
		foreach($hwc_entries as $entry_index => $entry) {
			if(empty($entry)) continue;
			
			// Split H-W-C pattern from win counts
			$parts = explode('=', $entry);
			if(count($parts) != 2) {
				continue;
			}
			
			$hwc_pattern = $parts[0];  // e.g., "4-1-1"
			$win_counts = $parts[1];   // e.g., "3,1,5,2,0,2,0"
			
			// Parse win counts into array
			$counts = explode(',', $win_counts);
			
			// Get enabled prize categories for this lottery
			$enabled_categories = array();
			$category_index = 0;
			
			// Build enabled categories array based on prize profile
			foreach($prize_profile as $category => $enabled) {
				if($enabled && $category != 'lottery_id' && $category != 'id') {
					$enabled_categories[$category_index] = $category;
					$category_index++;
				}
			}
			
			// Calculate total points for this H-W-C pattern
			$total_points = 0;
			$win_breakdown = array();
			
			foreach($counts as $index => $count) {
				$count = intval($count);
				if($count > 0 && isset($enabled_categories[$index])) {
					$category = $enabled_categories[$index];
					if(isset($category_points[$category])) {
						$points = $count * $category_points[$category];
						$total_points += $points;
						$win_breakdown[$category] = $count;
					}
				}
			}
			
			// Get occurrence count for display (try multiple formats for matching)
			$pattern_count = 0;
			if (isset($hwc_counts[$hwc_pattern])) {
				$pattern_count = $hwc_counts[$hwc_pattern];
			} else {
				// Try alternative formats (with spaces, without spaces)
				$pattern_with_spaces = str_replace('-', ' - ', $hwc_pattern);
				$pattern_no_spaces = str_replace('-', '', $hwc_pattern);
				
				if (isset($hwc_counts[$pattern_with_spaces])) {
					$pattern_count = $hwc_counts[$pattern_with_spaces];
				} elseif (isset($hwc_counts[$pattern_no_spaces])) {
					$pattern_count = $hwc_counts[$pattern_no_spaces];
				}
			}
			
			// Show all patterns that occurred at least once in the range
			// This includes patterns with 0 wins for complete analysis
			if ($pattern_count > 0) {
				$winners[] = array(
					'hwc_pattern' => $hwc_pattern,
					'total_points' => $total_points,
					'win_breakdown' => $win_breakdown,
					'enabled_categories' => $enabled_categories,
					'occurrence_count' => $pattern_count
				);
			}
		}
		
		// Sort by total points descending
		usort($winners, function($a, $b) {
			return $b['total_points'] - $a['total_points'];
		});
		
		return $winners;
	}
}	