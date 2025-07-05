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
	 * Calculates the win history based on the h-w-c's, followers, and friends.  The Numbers are selected from
	 * the outputs for these methods and put into a full wheeling table.  The correctly drawn numbers from the
	 * next draw are compared with the wheeling table tickets.  The wins will be updated based on the prize cateogory.
	 * 
	 * @param	integer		$id		Lottery id
	 * @return 	none	
	 * */
	public function calculate($id)
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
				$draw = $this->history_m->onlydrawn($this->data['lottery']->last_drawn,$this->data['lottery']->extra_ball);
				$hots = $h_w_c['h_count'];
				$warms = $h_w_c['w_count'];
				$colds = $h_w_c['c_count'];
				$this->data['lottery']->H = $hots;  // Number of Hots Distributed e.g. 16 Hots
				$this->data['lottery']->W = $warms; // Number of Warms Distributed e.g 18 Colds
				$this->data['lottery']->C = $colds; // Number of colds Distributed e.g 18 Colds
				$this->data['lottery']->extra_included = $h_w_c['extra_included'];
				$this->data['lottery']->extra_draws = $h_w_c['extra_draws'];
				$this->data['lottery']->last_drawn['range'] = $h_w_c['range'];
				$strhots_last = $h_w_c['hots_last']; 		// Pull from DB
				$strwarms_last = $h_w_c['warms_last'];	// All counts for Hots, Warms, Colds
				$strcolds_last = $h_w_c['colds_last'];
				$strhots = $h_w_c['hots']; 		// Pull from DB
				$strwarms = $h_w_c['warms'];	// All counts for Hots, Warms, Colds
				$strcolds = $h_w_c['colds'];
				$strdupextra = $h_w_c['dupextra'];
				$hots = explode(",", $strhots); // Convert to Arrays
				$warms = explode(",", $strwarms); 
				$colds = explode(",", $strcolds);
				$hots_last = explode(",", $strhots_last); // Convert to Arrays
				$warms_last = explode(",", $strwarms_last); 
				$colds_last = explode(",", $strcolds_last); 
				if(!empty($strdupextra)) $dupextra = explode(",", $strdupextra);
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
						$n = strstr($all_dupextra, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
						$c = substr(strstr($all_dupextra, '='), 1); // Strip off to the left of the equal sign count
						$this->data['lottery']->dupextra[$n] = $c; 
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
		$this->data['lottery']->draw = $draw;
		$this->data['lottery']->positions = $positions;
		$this->data['lottery']->positions_last = $positions_last;
		unset($draw);
		unset($positions);
		unset($positions_last);
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
		$followers = $this->statistics_m->followers_exists($id);		// Existing follower row 
		if(!is_null($followers))
		{
			$range = $followers['range'];
			// 2. Extract the details of follower with exctra included and / or extra draws 
			$this->data['lottery']->extra_included = $followers['extra_included'];
			$this->data['lottery']->extra_draws = $followers['extra_draws'];
			// 3. Create the structure for the last draw and the current wins for each number drawn
			$this->data['lottery']->last_drawn = $this->history_m->last_draw_prizegroup($this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_ball, $p_group); 
			// 4. extract the win record for each number into an array
			$follower_wins = explode(">", $followers['wins']);
			$follow_poswins = explode(">", $followers['positions']);
			// 5. Only populate the numbers with the win record that was actually drawn
			$this->data['lottery']->last_drawn = $this->history_m->last_draw_addwins($this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included,$p_group,$follower_wins,$follow_poswins);
			$this->data['lottery']->last_drawn = $this->history_m->last_draw_addpoints($this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included);
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
			if(!empty($friends['wins'])) 
			{
				// Friend only wins
				$wins = explode("|", $friends['wins']); // $wins[0]  = broken like this nofriends,1-wayfriends,2-wayfriends & wins[1] = 1 - 49 (canada 649 for example), 1-way or 2 way friends 
				$direction = explode(",", $wins[0]); // no friends ($direction[0]), 1 - way ($direction[1]) and 2 - way ($direction[2])
				$nonfriend_wins = explode("|", $nonfriends['wins']);  // Canada 649 (example) 1 to 49, Number 1 has a 1 way 34, number 2 has a 1 way 7, 
				// number 2 has a 1 way 25, etc.
				$this->data['lottery']->friend['nofriends'] = $direction[0];
				$this->data['lottery']->friend['1-way'] = $direction[1];
				$this->data['lottery']->friend['2-way'] = $direction[2];
				// Non Friends only with the occurrences of non friends drawn in the next draw
				$this->data['lottery']->friend['0-friends'] = $nonfriend_wins[0];
				$this->data['lottery']->friend['1-friends'] = $nonfriend_wins[1];
				$this->data['lottery']->friend['2-friends'] = $nonfriend_wins[2];
				$this->data['lottery']->friend['3-friends'] = $nonfriend_wins[3];
				$this->data['lottery']->friend['4-friends'] = $nonfriend_wins[4];
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
}	