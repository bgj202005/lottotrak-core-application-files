<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statistics extends Admin_Controller {
	
	public function __construct() {
		 parent::__construct();

		 $this->load->dbforge();
		 $this->load->library('session');
		 $this->load->model('lotteries_m');
		 $this->load->model('statistics_m');
		 $this->load->model('maintenance_m');
		 $this->load->helper('file');
		 $this->load->helper('html');
		 $this->load->config('statistics_optimization');
		// $this->output->enable_profiler(TRUE);
	}

	/**
	 * Retrieves List of All Lotteries
	 * 
	 * @param       none
	 * @return      none
	 */
	public function index()
	{ 
		// Fetch only enabled lotteries from the database
		$this->data['lotteries'] = $this->lotteries_m->get_enabled();
		foreach($this->data['lotteries'] as $lottery) 
		{
			$tbl_name = $this->lotteries_m->lotto_table_convert($lottery->lottery_name);

			$lottery->last_date = $this->statistics_m->last_date($tbl_name);
			$lottery->last_draw = $this->statistics_m->last_draw($tbl_name, $lottery->balls_drawn, $lottery->extra_ball);
			
			// Get draw count for minimum range checking
			$lottery->draw_count = $this->lotteries_m->count_draws_db($lottery->lottery_name);
			if ($lottery->draw_count === FALSE) $lottery->draw_count = 0;
			
			// Get prediction_min_range (default to 100 if not set)
			$lottery->prediction_min_range = isset($lottery->prediction_min_range) && $lottery->prediction_min_range > 0 
				? intval($lottery->prediction_min_range) 
				: 100;
			
			// Calculate required draws based on prediction range
			$lottery->required_draws = $lottery->prediction_min_range * 2;
			
			// Check if minimum draw requirement is met
			$lottery->min_draws_met = ($lottery->draw_count >= $lottery->required_draws);
			
			// If minimum not met, calculate draws remaining and next prediction date
			if (!$lottery->min_draws_met) {
				$lottery->draws_remaining = $lottery->required_draws - $lottery->draw_count;
				$lottery->next_prediction_date = $this->lotteries_m->calculate_next_prediction_date(
					$lottery, 
					$lottery->draw_count, 
					$lottery->required_draws
				);
				// If date calculation fails, set a default message
				if ($lottery->next_prediction_date === FALSE) {
					$lottery->next_prediction_date = 'Unable to calculate';
				}
			}
			
			$c = $this->statistics_m->lottery_rows($tbl_name);
			if($c>100) $c = 100;
			$lottery->average_sum = $this->statistics_m->lottery_average_sum($tbl_name, $c);
			$lottery->sum_last = $this->statistics_m->sum_last($tbl_name, $lottery->balls_drawn);
			$lottery->repeaters = $this->statistics_m->repeaters($tbl_name, $lottery->balls_drawn);
			
			// Check if followers need recalculation (after reset)
			$followers_check = $this->statistics_m->followers_exists($lottery->id);
			$lottery->needs_recalc = (is_null($followers_check) || empty($followers_check['lottery_followers']));
			
			// Check if H-W-C needs recalculation (after reset)
			$hwc_check = $this->statistics_m->h_w_c_exists($lottery->id);
			$lottery->needs_hwc_recalc = (is_null($hwc_check) || empty($hwc_check['hots']) || empty($hwc_check['warms']) || empty($hwc_check['colds']) || $hwc_check['draw_id'] == 0);
		}

		if ($this->session->flashdata('message')) $this->data['message'] = $this->session->flashdata('message');
		else $this->data['message'] = '';
		// Load the view
		if($this->session->has_userdata('range')) $this->session->unset_userdata('range');
		$this->data['current'] = $this->uri->segment(2); // Sets the Statistics menu
		$this->session->set_userdata('uri', 'admin/'.$this->data['current']);
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	 
		$this->data['subview'] = 'admin/dashboard/statistics/index';
		$this->data['statistics'] = $this;				// Access the methods in the view
		$this->load->view('admin/_layout_main', $this->data);
	}

	/**
	 * View Commulative Statistics of each draw
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @param       bool	$disabled	Whether to grey out the icon
	 * @return      none
	 */
	public function btn_stat($uri, $disabled = false) 
	{
		if ($disabled) {
			return '<span style="color: #ccc; cursor: not-allowed;" title="No draws available"><i class="fa fa-line-chart fa-2x" aria-hidden="true"></i></span>';
		}
		return anchor($uri, '<i class="fa fa-line-chart fa-2x" aria-hidden="true">', array('title' => 'View Commulative Statistics', 'class' => 'stats'));
	}

	/**
	 * View the Hot Warm Cold Numbers, Overdue and Consecutive numbers of each draw
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_hwc($uri) 
	{
		return anchor($uri, '<i class="fa fa-thermometer-full fa-2x" aria-hidden="true">', array('title' => 'Calculate and View the Hot - Warm - Cold of the Numbers','class' => 'h-w-c'));
	}

	/**
	 * View Historic Follower Statistics after the last draw
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_followers($uri)
	{
		return anchor($uri, '<i class="fa fa-retweet fa-2x" aria-hidden="true"></i>', array('title' => 'View Historic Follower Statistics after the last draw', 'class' => 'followers followers-btn'));
	}

	/**
	 * View Historic Friend history statistics of the Lottery
	 * 
	 * @param      string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_friends($uri)
	{
		return anchor($uri, '<i class="fa fa-history fa-2x" aria-hidden="true">', array('title' => 'View Historic Friend History Statistics of the lottery', 'class' => 'friends'));
	}

	/**
	 * View H-W-C + Followers combined prediction for the next draw
	 *
	 * @param       string  $uri    uri admin address of the page
	 * @return      none
	 */
	public function btn_hwc_followers($uri)
	{
		return anchor($uri, '<i class="fa fa-fire fa-2x" aria-hidden="true"></i>', array('title' => 'View H-W-C + Followers Predicted Numbers for the Next Draw', 'class' => 'hwc-followers'));
	}

	/**
	 * Calculate the Current History or Update to the latest Draw
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @param       bool	$disabled	Whether to grey out the icon
	 * @return      none
	 */
	public function btn_calculate($uri, $disabled = false)
	{
		if ($disabled) {
			return '<span style="color: #ccc; cursor: not-allowed;" title="No draws available"><i class="fa fa-calculator fa-2x" aria-hidden="true"></i></span>';
		}
		return anchor($uri, '<i class="fa fa-calculator fa-2x" aria-hidden="true">', array('title' => 'Calculate the Current History or Update to the latest Draw', 'class' => 'calculate'));
	}
	/**
	 * Views all draws with associated statistics, pagination and statistics filtering
	 *  
	 * @param       $id		current id of draws		
	 * @return      none
	 */
	public function view_draws($id)
	{
		$this->data['message'] = '';	// Defaulted to No Error Messages
		$this->data['lottery'] = $this->lotteries_m->get($id);
		
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		$this->data['trend'] = 0;
		$new_range = 0;
		if (!empty($this->uri->segment(5)&&$this->uri->segment(5)=='1'))
		{
			$this->data['trend'] = 1;
		}
		if(!empty($this->uri->segment(6))) 
		{
			$new_range = $this->uri->segment(6,0); // Return segment range
		}
		$all = $this->lotteries_m->db_row_count($tbl_name); // Return the total number of draws for this lottery
		if($all>100)
		{
			$interval = intval($all / 100); // Create the drop down in multiples of 100 and typecast to an integer value (truncates the floating point portion)
			if(!$interval) $interval = 1;	// 1 = 100, 2 = 200, 3 = 300, 4 = 400, 0 < 100 
		}
		else
		{
			$interval = 0;
		}
		$old_range = (!is_null($this->session->userdata('range')) ? $this->session->userdata('range') : 100); // Default will be 100 previous draws
		if(!$new_range) $new_range = $old_range;	// Database Range
		$sel_range = 1;								// All Defaults
		if($new_range>100) $sel_range = intval($new_range / 100);
		// Check to see if the actual table exists in the db?
		if (!$this->lotteries_m->lotto_table_exists($tbl_name))
		{
			$this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. '.$tbl_name.' Does not exist. Create the Lottery Database now.');
			redirect('admin/statistics');
		}

		if(!$this->statistics_m->lottery_stats_exist($tbl_name)) 
		{
			$this->session->set_flashdata('message', 'There are NO Statistics Calculated yet. Please Click on the Calculate Icon.');
			redirect('admin/statistics'); 
		}

		// Check threshold BEFORE loading draws to avoid timeouts
		$ajax_threshold = $this->config->item('ajax_pagination_threshold') ?: 200;
		
		if ($new_range > $ajax_threshold) {
			// For large datasets, don't pre-load draws - use AJAX pagination instead
			$this->data['draws'] = []; // Empty array to prevent view errors
			$this->data['subview'] = 'admin/dashboard/statistics/view_optimized';
		} else {
			// For smaller datasets, load draws normally and use original method
			$this->data['draws'] = $this->lotteries_m->load_draws($tbl_name, $new_range, $this->data['trend']);
			if (!$this->data['draws'])
			{
				$this->session->set_flashdata('message', 'There are no draws associated with this lottery. Please add draws.');
				redirect('admin/statistics'); 
			}
			$this->data['subview'] = 'admin/dashboard/statistics/view';
		}
		
		$this->data['interval'] = $interval;		// Record the interval here (for the dropdown)
		$this->data['sel_range'] = $sel_range;		// What was selected for the range in the previous page
		$this->data['range'] = $new_range;
		$this->data['all'] = $all;
		$this->data['statistics'] = $this->statistics_m->get_lottery_stats_cached($id);
		$this->data['evensodds'] = $this->statistics_m->evensodds_sum_cached($tbl_name, $this->data['trend']);
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->session->set_userdata('range', $new_range);
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/view_draws'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins
		
		$this->data['stat_method'] = $this;				// Access the methods in the view
		$this->load->view('admin/_layout_main', $this->data);
	}

	/**
	 * AJAX endpoint for loading draws data with pagination
	 * 
	 * @param       int $id		lottery id
	 * @return      json		JSON response with draws data
	 */
	public function ajax_load_draws($id)
	{
		// Set JSON header
		$this->output->set_content_type('application/json');
		
		// Add error logging for debugging

		
		// Allow AJAX requests and direct access for debugging
		// In production, you might want to re-enable this check
		// if (!$this->input->is_ajax_request()) {
		// 	show_404();
		// 	return;
		// }

		try {
			$lottery = $this->lotteries_m->get($id);
			if (!$lottery) {
				log_message('error', "Lottery not found for ID: $id");
				$this->output->set_output(json_encode(['error' => 'Lottery not found with ID: ' . $id]));
				return;
			}
		} catch (Exception $e) {
			log_message('error', "Database error getting lottery $id: " . $e->getMessage());
			$this->output->set_output(json_encode(['error' => 'Database error: ' . $e->getMessage()]));
			return;
		}

		$tbl_name = $this->lotteries_m->lotto_table_convert($lottery->lottery_name);
		
		// Check if table exists
		if (!$this->lotteries_m->lotto_table_exists($tbl_name)) {
			log_message('error', "Lottery table does not exist: $tbl_name");
			$this->output->set_output(json_encode(['error' => 'Lottery table does not exist: ' . $tbl_name]));
			return;
		}
		
		// Get pagination parameters
		$page = (int)$this->input->get('page', TRUE) ?: 1;
		$limit = (int)$this->input->get('limit', TRUE) ?: 10; // Default to 10 rows for better UX with large datasets
		$trend = (int)$this->input->get('trend', TRUE) ?: 0;
		$requested_range = (int)$this->input->get('range', TRUE) ?: 100; // The requested range (200, 300, etc.)
		$search = $this->input->get('search', TRUE) ?: '';
		

		
		// Calculate offset
		$offset = ($page - 1) * $limit;
		
		try {
			// Get total count for pagination
			$total_count = $this->lotteries_m->get_draws_count($tbl_name, $trend);

			
			if ($total_count === 0) {
				$this->output->set_output(json_encode(['error' => 'No draws found in this lottery table: ' . $tbl_name]));
				return;
			}
			
			// Limit the requested range to available draws to prevent negative numbering
			$effective_range = min($requested_range, $total_count);

			
		} catch (Exception $e) {
			log_message('error', "Error getting draws count: " . $e->getMessage());
			$this->output->set_output(json_encode(['error' => 'Database error getting count: ' . $e->getMessage()]));
			return;
		}
		
		// Load draws with pagination - use effective range for numbering and limiting
		$draws = $this->lotteries_m->load_draws_paginated($tbl_name, $limit, $offset, $trend, $effective_range);
		
		if (!$draws) {
			$this->output->set_output(json_encode(['error' => 'Failed to load draws data']));
			return;
		}
		
		// Process draws for display (minimal processing for performance)
		$processed_draws = [];
		
		foreach ($draws as $key => $draw) {
			$next_draw = isset($draws[$key + 1]) ? $draws[$key + 1] : null;
			$repeaters = [];
			
			// Calculate repeaters if we have a next draw (independent of trend setting)
			if ($next_draw) {
				$repeaters = $this->last_repeaters($next_draw, $draw, $lottery->balls_drawn);
			}
			
			$processed_draw = [
				'id' => $draw->id,
				'draw' => $draw->draw,
				'draw_date' => date("D, M d, Y", strtotime(str_replace('/', '-', $draw->draw_date))),
				'ball1' => $draw->ball1,
				'ball2' => $draw->ball2,
				'ball3' => $draw->ball3,
				'sum_draw' => $draw->sum_draw,
				'sum_digits' => $draw->sum_digits,
				'odd' => $draw->odd,
				'even' => $draw->even,
				'range_draw' => $draw->range_draw,
				'repeat_decade' => $draw->repeat_decade,
				'repeat_last' => $draw->repeat_last,
				'repeaters' => $repeaters,
				'trends' => []  // Initialize trends array
			];
			
			// Add trend arrows if enabled and we have a next draw (previous chronologically)
			// Note: next_draw is chronologically previous since draws are ordered DESC by date
			if ($trend && $next_draw !== null) {
				$processed_draw['trends']['ball1'] = $this->trend($next_draw->ball1, $draw->ball1);
				$processed_draw['trends']['ball2'] = $this->trend($next_draw->ball2, $draw->ball2);
				$processed_draw['trends']['ball3'] = $this->trend($next_draw->ball3, $draw->ball3);
				
				// Add trend arrows for additional balls based on lottery configuration
				if (intval($lottery->balls_drawn) >= 4) {
					$processed_draw['trends']['ball4'] = $this->trend($next_draw->ball4, $draw->ball4);
				}
				if (intval($lottery->balls_drawn) >= 5) {
					$processed_draw['trends']['ball5'] = $this->trend($next_draw->ball5, $draw->ball5);
				}
				if (intval($lottery->balls_drawn) >= 6) {
					$processed_draw['trends']['ball6'] = $this->trend($next_draw->ball6, $draw->ball6);
				}
				if (intval($lottery->balls_drawn) >= 7) {
					$processed_draw['trends']['ball7'] = $this->trend($next_draw->ball7, $draw->ball7);
				}
				if (intval($lottery->balls_drawn) >= 8) {
					$processed_draw['trends']['ball8'] = $this->trend($next_draw->ball8, $draw->ball8);
				}
				if (intval($lottery->balls_drawn) == 9) {
					$processed_draw['trends']['ball9'] = $this->trend($next_draw->ball9, $draw->ball9);
				}
				if (intval($lottery->extra_ball) == 1) {
					$processed_draw['trends']['extra'] = $this->trend($next_draw->extra, $draw->extra);
				}
			}
			
			// Add additional balls based on lottery configuration
			if (intval($lottery->balls_drawn) >= 4) $processed_draw['ball4'] = $draw->ball4;
			if (intval($lottery->balls_drawn) >= 5) $processed_draw['ball5'] = $draw->ball5;
			if (intval($lottery->balls_drawn) >= 6) $processed_draw['ball6'] = $draw->ball6;
			if (intval($lottery->balls_drawn) >= 7) $processed_draw['ball7'] = $draw->ball7;
			if (intval($lottery->balls_drawn) >= 8) $processed_draw['ball8'] = $draw->ball8;
			if (intval($lottery->balls_drawn) == 9) $processed_draw['ball9'] = $draw->ball9;
			if (intval($lottery->extra_ball) == 1) $processed_draw['extra'] = $draw->extra;
			
			$processed_draws[] = $processed_draw;
		}
		
		// Return JSON response
		$this->output->set_output(json_encode([
			'draws' => $processed_draws,
			'pagination' => [
				'total' => $effective_range, // Use effective range for pagination, not total database count
				'per_page' => $limit,
				'current_page' => $page,
				'last_page' => ceil($effective_range / $limit)
			],
			'lottery_config' => [
				'balls_drawn' => $lottery->balls_drawn,
				'extra_ball' => $lottery->extra_ball
			]
		]));
	}

	/**
	 * Calculate the Draw Database or update the latest draws to the statistics
	 *  
	 * @param       $id		current id of draws		
	 * @return      none
	 */
	public function calculate($id)
	{
	// 1. Determine if the draws have the columns with the Statistics data
		$this->data['message'] = '';	// Defaulted to No Error Messages
		$this->data['lottery'] = $this->lotteries_m->get($id);
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		$drawn = $this->data['lottery']->balls_drawn;		// Get the number of balls drawn for this lottory, Pick 5, Pick 6, Pick 7, etc.

		// Check to see if the actual table exists in the db?
		if (!$this->lotteries_m->lotto_table_exists($tbl_name))
		{
			$this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. '.$tbl_name.' Does not exist. Create the Lottery Database now.');
			redirect('admin/statistics');
		}
		if(!$this->statistics_m->lottery_stats_exist($tbl_name)) // Stats Exists?
		{
			// If FALSE, No Statistics have been previously calculated:
			// Expand the draw database with the 8 field columns. 
			if($this->statistics_m->lottery_expand_columns($tbl_name))  // Columns need to be expanded and recalc is not selected
			{
				$lt_rows = $this->statistics_m->lottery_rows($tbl_name);	// Return the Rows in the table to update
				$draws = $lt_rows; // Capture the number of draws in this lottery
				$lt_id =  $this->statistics_m->lottery_start_id($tbl_name);
				$draw = array();	// Empty Set Array
				$error = FALSE;		// Default state, No Errors
				do{
				// Update each draw, calculate the Total Sum, Total Sum of Digits, Evens, Odds, Range of Draw, Repeating Decade, Repeating Last Digit
					$draw = $this->statistics_m->lottery_draw_stats($tbl_name, $lt_id, $drawn);
					if($draw)	// Only if a draw exists! Is the $lt_id pointer could be past the last lottery draw record?
					{
						if (!$this->statistics_m->lottery_draw_update($tbl_name, $lt_id, $draw))
						{
							$error = TRUE; // Unable to update draw row, exist with the error flag set to TRUE
							break;
						}
					}
					$error = FALSE;	// Keep going, update successful.		
					$lt_id++;
					$lt_rows--;
				} while($lt_rows>0);
				if ($error) 
				{
					$this->session->set_flashdata('message', 'There is a Problem with updating the draw database. Stopped at Draw id:'.$lt_id.'.');
					redirect('admin/statistics');
				}
				
				// Do until draw database is complete. Report on the screen each draw complete on the screen.
			}
			else // Failure on adding columns
			{
				$this->session->set_flashdata('message', 'There is a Problem with expanding the columns to '.$tbl_name.'.');
				redirect('admin/statistics');
			}

		}
		else // Update New Draws Here
		{
			$lt_rows = $this->statistics_m->lottery_next_rows($tbl_name);	// Return the Rows in the table to update
			$lt_id =  $this->statistics_m->lottery_next_id($tbl_name);
			if ($lt_id)	// The next id was returned, continue with the statistics calculations
			{
				$draw = array();	// Empty Set Array
				$error = FALSE;		// No Errors found at this point							
				do{
				// Update each draw, calculate the Total Sum, Total Sum of Digits, Evens, Odds, Range of Draw, Repeating Decade, Repeating Last Digit
				$draw = $this->statistics_m->lottery_draw_stats($tbl_name, $lt_id, $this->data['lottery']->balls_drawn);

				if (!$this->statistics_m->lottery_draw_update($tbl_name, $lt_id, $draw))
				{
					$error = TRUE; 	// Unable to update draw row, exist with the error flag set to TRUE
					break;			// exit from loop, error has resulted.
				}
				$error = FALSE;	// Keep going, update successful.
				$lt_id++;
				$lt_rows--;
				} while($lt_rows>0);
				if ($error) 
				{
					$this->session->set_flashdata('message', 'There is a Problem with updating the draw database. Stopped at Draw id:'.$lt_id.'.');
					redirect('admin/statistics');
				}
			}
			/* else // The query resulted in NULL indicating the statistics are up-to-date
			{
				$this->session->set_flashdata('message', 'The Statistics are up-to-date. Please Calculate after the next draw.');
				redirect('admin/statistics');
			} */
		}
			$stats = array();
			//$draws = $this->statistics_m->lottery_rows($tbl_name); // Return the number of draws in this lottery
			$draws = $this->statistics_m->lottery_rows_noextra($tbl_name, $this->data['lottery']->extra_ball); // Return the number of draws without the extra draws
			// Average Sum of Last 10 Draws (Integer)
			$stats['sum_10'] = $this->statistics_m->lottery_average_sum($tbl_name, 10);
			// Average Sum of Last 100 Draws (Integer) 
			$stats['sum_100'] = $this->statistics_m->lottery_average_sum($tbl_name, ($draws < 100 ? $draws : 100));
			// Average Sum of Last 200 Draws (Integer)
			$stats['sum_200'] = $this->statistics_m->lottery_average_sum($tbl_name, ($draws < 200 ? $draws : 200));
			// Average Sum of Last 300 Draws (Integer)
			$stats['sum_300'] = $this->statistics_m->lottery_average_sum($tbl_name, ($draws < 300 ? $draws : 300));
			// Average Sum of Last 400 Draws (Integer)
			$stats['sum_400'] = $this->statistics_m->lottery_average_sum($tbl_name, ($draws < 400 ? $draws : 400));
			// Average Sum of Last 500 Draws (Integer)
			$stats['sum_500'] = $this->statistics_m->lottery_average_sum($tbl_name, ($draws < 500 ? $draws : 500));
			// Average Sum of Digits in Last 10 Draws (Integer)
			$stats['digits_10'] = $this->statistics_m->lottery_average_sumdigits($tbl_name, 10);
			// Average Sum of Digits in Last 100 Draws (Integer)
			$stats['digits_100'] = $this->statistics_m->lottery_average_sumdigits($tbl_name, ($draws < 100 ? $draws : 100));
			// Average Even Numbers the last 10 Draws (Integer)
			$stats['even_10'] = $this->statistics_m->lottery_average_evens($tbl_name, 10);
			// Average Even Numbers the last 100 Draws (Integer)
			$stats['even_100'] = $this->statistics_m->lottery_average_evens($tbl_name, ($draws < 100 ? $draws : 100));
			// Average Odd Numbers the last 10 Draws (Integer)
			$stats['odd_10'] = $this->statistics_m->lottery_average_odds($tbl_name, 10);
			// Average Odd Numbers the last 100 Draws (Integer)
			$stats['odd_100'] = $this->statistics_m->lottery_average_odds($tbl_name, ($draws < 100 ? $draws : 100));
			// Average Range of Numbers in the last 10 Draws (Integer)
			$stats['range_10'] = $this->statistics_m->lottery_average_range($tbl_name, 10);
			// Average Range of Numbers in the last 100 Draws (integer)
			$stats['range_100'] = $this->statistics_m->lottery_average_range($tbl_name, ($draws < 100 ? $draws : 100));
			// Average Maximum Repeating Decade in the last 10 Draws (Integer)
			$stats['repeat_decade_10'] = $this->statistics_m->lottery_average_decade($tbl_name, 10);
			// Average Maximum Repeating Decade in the last 100 Draws (integer)
			$stats['repeat_decade_100'] = $this->statistics_m->lottery_average_decade($tbl_name, ($draws < 100 ? $draws : 100));
			// Average Maximum Repeating Last Digit in the last 10 Draws (Integer)
			$stats['repeat_last_10'] = $this->statistics_m->lottery_average_last($tbl_name, 10);
			// Average Maximum Repeating Last Digit in the last 100 Draws (Integer)
			$stats['repeat_last_100'] = $this->statistics_m->lottery_average_last($tbl_name, ($draws < 100 ? $draws : 100));
			$stats['lottery_id'] = $id;		// Must be associated with the lottery_id
			// Do until draws statistically calculated, Report on Screen
			if(!$this->statistics_m->stats_id($id))	// No previous lottery global statistics exist, create a new lottery statistics record
			{
				if(!$this->statistics_m->save($stats, NULL))
				{
					$this->session->set_flashdata('message', 'There is a problem adding the Statistics for '.$this->data['lottery']->lottery_name.' Please try again.');
					redirect('admin/statistics');
				}
			}
			else // Previous lottery statistics exist, update!
			{
				$index_id = $this->statistics_m->update_stats_id($id);
				if($index_id)
				{
					if(!$this->statistics_m->save($stats, $index_id))
					{
						$this->session->set_flashdata('message', 'There is a problem updating the Statistics for '.$this->data['lottery']->lottery_name.' Please try again.');
						redirect('admin/statistics');
					}
				}
				else 
				{
					$this->session->set_flashdata('message', 'There is a problem updating the Statistics for '.$this->data['lottery']->lottery_name.' Index_id was not found.');
					redirect('admin/statistics');
				}
			}						
			$this->session->set_flashdata('message', 'Draw Statistics Complete and Up To-Date.'); // Yahoo! Statistics Updated!
			redirect('admin/statistics');
	}
	/**
	 * Update the current count of the current lottery database that is calculated
	 * 
	 * @param      string	$tbl	Current lottery table database
	 * @return      none
	 */
	public function update($tbl)
	{
		// if TRUE, Statistics have been previously calculated, update only the current draws, report on screen
		// If TRUE, Update the existing statistics from the most current draws, report on screen
		//Return to Statistics Dashboard, reporting a success message on the screen
		
		$conditions = array('sum_draw ' => NULL, 'sum_digits ' => NULL, 'even ' => NULL, 'odd ' => NULL, 'range_draw ' => NULL, 'repeat_decade ' => NULL, 'repeat_decade ' => NULL);
		$this->db->where($conditions);
		$this->db->from($tbl);
		$count = $this->db->count_all_results();
		
		$total = $this->db->count_all($tbl);	
		if (!$count) $count = 1;
		$data = ['count' =>	$count,
				'total' => $total];
		echo json_encode($data);
	}
	/**
	 * Returns the number of repeats comparing the previous draw from the current one. Return the repeats as TRUE or FALSE values
	 * 
	 * @param	object	$before		Draw previous
	 * @param	object	$today		Draw Current
	 * @param	integer	$max		Maximum number of drawn balls
	 * @return	object	$repeats	Object of boolean values for the number of balls drawn
	 */
	public function last_repeaters($before, $today, $max)
	{
		$repeats = array();	// Empty Array
		$before = (array) $before;	// Cast to Array
		$today = (array) $today;	// Cast to Array

		$n = 1;	// Begin with Ball 1
		$c = 1;	// Counter
		while($n<=$max)
		{
			do
			{
				if($today['ball'.$n]==$before['ball'.$c]) $repeats['ball'.$n] = TRUE;
				$c++;
			} while($c<=$max);
			$c=1; // Reset to the first ball
			$n++;
		}
	return (object) $repeats;	
	}

	/**
	 * Display Repeater Icon (location, just after the drawn ball)
	 * 
	 * @param 	   	none	
	 * @return      img repeater png
	 */
	public function icon_repeater() 
	{
		return img('images/assets/repeat-icon.png', FALSE, 'class="repeater"');
	}
	/**
	 * Display Trend Up Icon (location, just after the drawn ball)
	 * 
	 * @param 	   	none	
	 * @return      <i tag  font awesome up></i>
	 */
	public function icon_up() 
	{
		return '<i class="fa fa-arrow-up" aria-hidden="true" style = "color:red"></i>';
	}
	/**
	 * Display Trend Down Icon (location, just after the drawn ball)
	 * 
	 * @param 	   	none	
	 * @return      <i tag font awesome down></i>
	 */
	public function icon_down() 
	{
		return '<i class="fa fa-arrow-down" aria-hidden="true" style = "color:green"</i>';
	}
	/**
	 * This method looks at the previous draw with the next draw and compares an up or down trend for each draw
	 * 
	 * @param		integer		$prev		current ball number	
	 * @param 		integer 	$next		next draw number
	 * @return      string		$icon		The font awesome icon
	 */
	public function trend($prev, $next)
	{
		if ($prev<$next)
		{
			$icon = $this->icon_up();
		}
		if($prev>$next)
		{
			$icon = $this->icon_down();
		}
		elseif ($prev==$next)
		{
			$icon = ''; // Display Blank
		}
	return $icon;
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
		
		// Check if followers data exists and block access if empty (after reset)
		$followers_check = $this->statistics_m->followers_exists($id);
		
		if(is_null($followers_check) || empty($followers_check['lottery_followers'])) {
			$this->session->set_flashdata('message', 'Followers must be ReCalculated with the ReCalc checkbox before viewing followers data.');
			redirect('admin/statistics');
			return;
		}
		
		// Retrieve the lottery ta ble name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		$blnduplicate = ($this->data['lottery']->duplicate_extra_ball ? TRUE : FALSE);
		$drawn = $this->data['lottery']->balls_drawn;		// Get the number of balls drawn for this lottory, Pick 5, Pick 6, Pick 7, etc.
		$low = $this->data['lottery']->minimum_ball;		// Regular Drawn Low ball e.g. ball 1
		$high = $this->data['lottery']->maximum_ball;		// Regular Drawn High ball e.g. ball 49
		global $positions;									// ** New ** Prize Wins based on the position of the ball drawn and not the ball itself
		global $prizes;										// prizes are global to model methods
		$prizes = array();
		// Check to see if the actual table exists in the db?
		if (!$this->lotteries_m->lotto_table_exists($tbl_name))
		{
			$this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. '.$tbl_name.' Does not exist. Create the Lottery Database now.');
			redirect('admin/statistics');
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
		
		// ============================================================
		// TEST MODE: Check for /test_mode in URL to use second-to-last draw
		$this->data['lottery']->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl_name);	// Retrieve the last drawn numbers and draw date
		
		// 1. Check for a record for the current lottery in the followers table
		// Clear cache to ensure we get fresh data (especially for prev_* fields)
		$this->statistics_m->clear_follower_cache($id);
		
		$followers = $this->statistics_m->followers_exists($id);		// Existing follower row 
		$nonfollowers = $this->statistics_m->nonfollowers_exists($id);	// Non Follower existing row
		$sel_range = 1;
		
		// Extract previous draw followers/nonfollowers data if available
		$prev_followers_data = null;
		$prev_nonfollowers_data = null;
		if ($followers && isset($followers['prev_lottery_followers']) && $followers['prev_lottery_followers']) {
			$prev_followers_data = $followers['prev_lottery_followers'];
		}
		if ($nonfollowers && isset($nonfollowers['prev_lottery_nonfollowers']) && $nonfollowers['prev_lottery_nonfollowers']) {
			$prev_nonfollowers_data = $nonfollowers['prev_lottery_nonfollowers'];
		}
		
		// Pass previous data to view
		$this->data['prev_followers_data'] = $prev_followers_data;
		$this->data['prev_nonfollowers_data'] = $prev_nonfollowers_data;
		
		// Initialize settings - handle independent extra_included and extra_draws parameters
			$url_extra_included = ($this->uri->segment(6)=='extra') ? 1 : 0;
			$url_extra_draws = ($this->uri->segment(6)=='draws') ? 1 : 0;
		// Don't set lottery object values yet - determine source first (URL vs Database)
		$outofrange = FALSE;						// default is not out of range for the prize pool
		$blnEX = false;								// Extra Bonus Ball / Draws flag are no change or update
		

		
		// Determine if we have actual parameter segments (extra/draws), not just range or lottery_id
		$has_extra_param = ($this->uri->segment(6) == 'extra' || $this->uri->segment(7) == 'extra' || $this->uri->segment(8) == 'extra');
		$has_draws_param = ($this->uri->segment(6) == 'draws' || $this->uri->segment(7) == 'draws' || $this->uri->segment(8) == 'draws');
		$has_param_segments = ($has_extra_param || $has_draws_param);

		
		// Session-based preference management: restore user's last checkbox settings when returning from stats dashboard
		// Check if user is coming from external navigation (referer is not followers page)
		$referer = $this->input->server('HTTP_REFERER');
		$coming_from_followers = ($referer && strpos($referer, '/followers/') !== false);
		
		// **DISABLED**: Removing automatic redirects to prevent conflicts
		// Users will manually check/uncheck boxes as needed
		/*
		if(!$has_param_segments && !$coming_from_followers) {
			// No URL parameters AND not coming from followers page - check if we have saved preferences for this lottery
			$saved_prefs = $this->session->userdata('followers_preferences');
			if($saved_prefs && isset($saved_prefs[$id])) {
				// User has saved preferences for this lottery - redirect to URL with saved parameters
				$saved_extra = $saved_prefs[$id]['extra_included'];
				$saved_draws = $saved_prefs[$id]['extra_draws'];

				
				// Only redirect if saved preferences are different from current URL (which has no parameters)
				if($saved_extra || $saved_draws) {
					// Build URL with saved parameters
					$range = $this->uri->segment(5, 100);
					$redirect_url = "admin/statistics/followers/$id/$range";
					if($saved_extra) $redirect_url .= '/extra';
					if($saved_draws) $redirect_url .= '/draws';
					

					redirect($redirect_url);
					return;
				}
			}
		}
		*/
		
		
		if(!is_null($followers))
		{
			// Check if followers data is empty (after reset) - preserve URL parameters or database values
			$followers_data_empty = empty($followers['lottery_followers']);
			
			// Get current database values for comparison and fallback
			if(!$followers_data_empty) {
				$db_extra_included = $this->statistics_m->extra_included($id, FALSE, 'lottery_followers');
				$db_extra_draws = $this->statistics_m->extra_draws($id, FALSE, 'lottery_followers');
				
				// **SMART LOGIC**: Handle different navigation scenarios
				if($has_param_segments) {
					// URL has explicit parameters - update database to match URL
					if($url_extra_included != $db_extra_included || $url_extra_draws != $db_extra_draws) {
						$blnEX = true;
						$this->set_extra_included_value($id, $url_extra_included, 'lottery_followers');
						$this->set_extra_included_value($id, $url_extra_included, 'lottery_nonfollowers');
						$this->set_extra_draws_value($id, $url_extra_draws, 'lottery_followers');
						$this->set_extra_draws_value($id, $url_extra_draws, 'lottery_nonfollowers');
						
						// Set a flag to force complete recalculation for this request
						$this->session->set_userdata('force_recalc_lottery_' . $id, time());
					}
					// Use URL values
					$this->data['lottery']->extra_included = $url_extra_included;
					$this->data['lottery']->extra_draws = $url_extra_draws;
				} else {
					// NO URL parameters - check user intent based on navigation source
					$coming_from_stats = ($referer && (strpos($referer, '/statistics') !== false || strpos($referer, '/admin/statistics') !== false));
					
					if($coming_from_stats && !$coming_from_followers) {
						// Coming from stats page (reset/recalc) - preserve database state
						$this->data['lottery']->extra_included = $db_extra_included;
						$this->data['lottery']->extra_draws = $db_extra_draws;
					} else {
						// Direct navigation or from followers page - user wants to uncheck (URL has no params)
						if($db_extra_included != 0 || $db_extra_draws != 0) {
							$blnEX = true;
							$this->set_extra_included_value($id, 0, 'lottery_followers');
							$this->set_extra_included_value($id, 0, 'lottery_nonfollowers');
							$this->set_extra_draws_value($id, 0, 'lottery_followers');
							$this->set_extra_draws_value($id, 0, 'lottery_nonfollowers');
						}
						$this->data['lottery']->extra_included = 0;
						$this->data['lottery']->extra_draws = 0;
					}
				}
			} else {
				// Followers data is empty (reset case) - use URL params if present, otherwise default to 0
				$this->data['lottery']->extra_included = $url_extra_included;
				$this->data['lottery']->extra_draws = $url_extra_draws;
			}
			
			// Always save current state as user preferences
			$prefs = $this->session->userdata('followers_preferences') ?: array();
			$prefs[$id] = array(
				'extra_included' => $this->data['lottery']->extra_included,
				'extra_draws' => $this->data['lottery']->extra_draws
			);
			$this->session->set_userdata('followers_preferences', $prefs); 

			$p_group = $this->statistics_m->prize_group_profile($id); // Prize Group Profile Only
			
			// Handle case where no prize profile exists
			if (empty($p_group)) {
				// Use default prize structure if no profile found
				$p_group = array(
					'extra' => 1,
					'1_win' => 1, '1_win_extra' => 1,
					'2_win' => 1, '2_win_extra' => 1,
					'3_win' => 1, '3_win_extra' => 1,
					'4_win' => 1, '4_win_extra' => 1,
					'5_win' => 1, '5_win_extra' => 1,
					'6_win' => 1, '6_win_extra' => 1,
					'7_win' => 1, '7_win_extra' => 1,
					'8_win' => 1, '8_win_extra' => 1,
					'9_win' => 1, '9_win_extra' => 1
				);
			}
			
			// CRITICAL FIX: Use actual extra_included state, not just lottery extra_ball config
			// When user unchecks extra, we should filter out extra categories from p_group
			$current_extra_state = $this->data['lottery']->extra_included;
			$p_group = $this->statistics_m->prizes_only($p_group, $current_extra_state);

			// 2. If exist, check the database for the latest draw range from 100 to all draws for the change in the range
			// Use same range logic as recalc_followers method for consistency
			$range = $this->get_current_range($id, $tbl_name);
			if($range>100) $sel_range = intval($range / 100);
			if($range!=0)	
			{
				$force_recalc = (($followers && intval($followers['range'])!=(intval($range))) || $blnEX || !$followers);
				
				if($force_recalc) // Any Change in Selection/Settings of the Draws?
				{
					
					$max = $this->data['lottery']->maximum_ball;
					$mx_extra = ($blnduplicate ? $this->data['lottery']->maximum_extra_ball : $max);
					
					// Ensure range doesn't exceed available draws
					$total_draws = $this->lotteries_m->db_row_count($tbl_name);
					if($range > $total_draws) {
						$range = $total_draws;
					}
					
					// CRITICAL FIX: Create prize and position arrays INSIDE calculation block 
					// with current extra_included state to avoid stale data when checkbox changes
					global $prizes; // Explicitly reference global to ensure no local variable shadowing
					$prizes = $this->statistics_m->create_prize_array($p_group, $low, $high);
					$positions = $this->statistics_m->create_positions_prize_array($p_group, $drawn, $this->data['lottery']->extra_included);
					
					// Extra validation: When extra parameters change, ensure clean calculation
					if ($blnEX) {
						// Reinitialize global prizes array to ensure clean state
						$GLOBALS['prizes'] = array();
						$prizes = $this->statistics_m->create_prize_array($p_group, $low, $high);
						$positions = $this->statistics_m->create_positions_prize_array($p_group, $drawn, $this->data['lottery']->extra_included);
					}
					
					// Validate arrays before passing to model methods to prevent null parameter errors
					if (!is_array($prizes)) {
						log_message('error', "Invalid prizes array detected, reinitializing");
						$prizes = $this->statistics_m->create_prize_array($p_group, $low, $high);
					}
					if (!is_array($positions)) {
						log_message('error', "Invalid positions array detected, reinitializing");  
						$positions = $this->statistics_m->create_positions_prize_array($p_group, $drawn, $this->data['lottery']->extra_included);
					}
					
					// Validate last_drawn array to prevent undefined offset errors
					if (!is_array($this->data['lottery']->last_drawn) || empty($this->data['lottery']->last_drawn)) {
						$this->session->set_flashdata('message', 'Invalid lottery draw data. Please check the lottery configuration.');
						redirect('admin/statistics');
						return;
					}
					
				$str_followers = $this->statistics_m->followers_calculate($tbl_name, $this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $range, '', $blnduplicate);
					$outofrange = $this->statistics_m->followers_prizes($tbl_name, $this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $range, $max, '', $blnduplicate, $mx_extra);
					
					// Additional validation before string conversion to prevent array_key_exists errors
					if (!is_array($prizes)) {
						log_message('error', "Prizes is not an array after followers_prizes call, setting to empty");
						$prizes = array();
					}
					if (!is_array($positions)) {
						log_message('error', "Positions is not an array, setting to empty");
						$positions = array();
					}
					
					$str_prizes  = (!$outofrange ? $this->statistics_m->followers_prize_string($prizes) : '');
					$str_positions_prizes = (!$outofrange ? $this->statistics_m->followers_positions_prize_string($positions) : '');					// Calculate dupextra_wins for independent extra ball lotteries only
					$str_dupextra_wins = '';
					if($blnduplicate && !$outofrange) {
						$str_dupextra_wins = $this->statistics_m->calculate_dupextra_wins($tbl_name, $this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $range, $mx_extra, '');
					}
					
					/** NEW included nonfollower calculations **/
					$str_nonfollowers = $this->statistics_m->nonfollowers_calculate($tbl_name, $this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $range, $max, '', $blnduplicate, $mx_extra);
					$followers = array(
 						'range'				=> $range,
						'lottery_followers'	=> $str_followers,
						'wins'				=> $str_prizes,
						'positions'			=> $str_positions_prizes,
						'draw_id'			=> $this->data['lottery']->last_drawn['id'],
						'lottery_id'		=> $id,
						'extra_included'	=> $this->data['lottery']->extra_included,
						'extra_draws'		=> $this->data['lottery']->extra_draws
					);
					
					// Add dupextra_wins field only for independent extra ball lotteries
					if($blnduplicate) {
						$followers['dupextra_wins'] = $str_dupextra_wins;
					}
					
					$this->statistics_m->follower_data_save($followers, TRUE);
					/** NEW included nonfollower Data Save **/
					$nonfollowers = array(
						'range'					=> $range,
						'lottery_nonfollowers'	=> $str_nonfollowers,
						'draw_id'				=> $this->data['lottery']->last_drawn['id'],
						'lottery_id'			=> $id
					);
					$this->statistics_m->nonfollower_data_save($nonfollowers, TRUE);
				}
			}
			else
			{
				$range = $all;
			}
		}
		else // 3. If does not exist, calculate for the given draw range, return results and save to follower table
		{
			// No existing followers record - set checkbox values based on URL parameters
			$this->data['lottery']->extra_included = $url_extra_included;
			$this->data['lottery']->extra_draws = $url_extra_draws;
			
			// Save current state as user preferences
			$prefs = $this->session->userdata('followers_preferences') ?: array();
			$prefs[$id] = array(
				'extra_included' => $this->data['lottery']->extra_included,
				'extra_draws' => $this->data['lottery']->extra_draws
			);
			$this->session->set_userdata('followers_preferences', $prefs);
			
			$p_group = $this->statistics_m->prize_group_profile($id); // Prize Group Profile Only
			
			// Handle case where no prize profile exists
			if (empty($p_group)) {
				// Use default prize structure if no profile found
				$p_group = array(
					'extra' => 1,
					'1_win' => 1, '1_win_extra' => 1,
					'2_win' => 1, '2_win_extra' => 1,
					'3_win' => 1, '3_win_extra' => 1,
					'4_win' => 1, '4_win_extra' => 1,
					'5_win' => 1, '5_win_extra' => 1,
					'6_win' => 1, '6_win_extra' => 1,
					'7_win' => 1, '7_win_extra' => 1,
					'8_win' => 1, '8_win_extra' => 1,
					'9_win' => 1, '9_win_extra' => 1
				);
			}
			
			// CRITICAL FIX: Use actual extra_included state, not just lottery extra_ball config
			// When user unchecks extra, we should filter out extra categories from p_group
			$current_extra_state = $this->data['lottery']->extra_included;
			$p_group = $this->statistics_m->prizes_only($p_group, $current_extra_state);
			$prizes = $this->statistics_m->create_prize_array($p_group, $low, $high);

			$positions = $this->statistics_m->create_positions_prize_array($p_group, $drawn, $this->data['lottery']->extra_included);
			// Get range using same logic as recalc_followers method for consistency
			$range = $this->get_current_range($id, $tbl_name);
			$max = $this->data['lottery']->maximum_ball;
			$mx_extra = ($blnduplicate ? $this->data['lottery']->maximum_extra_ball : $max);
			
			// Validate arrays before passing to model methods
			if (!is_array($prizes)) {
				log_message('error', "New followers: Invalid prizes array detected, reinitializing");
				$prizes = $this->statistics_m->create_prize_array($p_group, $low, $high);
			}
			if (!is_array($positions)) {
				log_message('error', "New followers: Invalid positions array detected, reinitializing");
				$positions = $this->statistics_m->create_positions_prize_array($p_group, $drawn, $this->data['lottery']->extra_included);
			}
			
			// Validate last_drawn array to prevent undefined offset errors
			if (!is_array($this->data['lottery']->last_drawn) || empty($this->data['lottery']->last_drawn)) {
				log_message('error', "New followers: Invalid last_drawn data detected: " . print_r($this->data['lottery']->last_drawn, true));
				$this->session->set_flashdata('message', 'Invalid lottery draw data. Please check the lottery configuration.');
				redirect('admin/statistics');
				return;
			}
			
			$str_followers = $this->statistics_m->followers_calculate($tbl_name, $this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $range, '', $blnduplicate);
			$outofrange = $this->statistics_m->followers_prizes($tbl_name, $this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $range, $max, '', $blnduplicate, $mx_extra);
			
			// CRITICAL FIX: When extra_included=0, consolidate extra wins into base categories
			if (!$current_extra_state && is_array($prizes)) {
				$consolidated_count = 0;
				$filtered_count = 0;
				$preserved_count = 0;
				foreach ($prizes as $ball => $categories) {
					if (is_array($categories)) {
						// Count wins before processing
						$ball_wins_before = array_sum($categories);
						
						// First, consolidate *_extra categories into their base categories
						foreach ($categories as $cat => $count) {
							if (strpos($cat, '_extra') !== false && $count > 0) {
								$base_cat = str_replace('_extra', '_win', $cat);
								// If base category exists, add the extra wins to it
								if (array_key_exists($base_cat, $categories)) {
									$categories[$base_cat] += $count;
									$consolidated_count++;
								}
								// Remove the extra category after consolidation
								unset($categories[$cat]);
							}
						}
						
						// Then filter out non-base categories (7_win, 8_win, 9_win, etc.)
						foreach ($categories as $cat => $count) {
							// Keep 1_win and categories in p_group, filter out everything else
							if (!array_key_exists($cat, $p_group) && $cat !== '1_win') {
								if ($count > 0) {
									$filtered_count++;
								}
								unset($categories[$cat]);
							}
						}
						
						// Update the prizes array with consolidated results
						$prizes[$ball] = $categories;
						
						// Count wins after processing
						$ball_wins_after = array_sum($categories);
						if ($ball_wins_before > 0 || $ball_wins_after > 0) {
							$preserved_count++;
						}
						
						// Remove balls with no remaining wins
						if (array_sum($categories) == 0) {
							unset($prizes[$ball]);
						}
					}
				}
			}
			
			// Additional validation before string conversion
			if (!is_array($prizes)) {
				log_message('error', "New followers: Prizes is not an array after followers_prizes call, setting to empty");
				$prizes = array();
			}
			if (!is_array($positions)) {
				log_message('error', "New followers: Positions is not an array, setting to empty");
				$positions = array();
			}
			
			$str_prizes = (!$outofrange ? $this->statistics_m->followers_prize_string($prizes) : '');
			$str_positions_prizes = (!$outofrange ? $this->statistics_m->followers_positions_prize_string($positions) : '');			// Calculate dupextra_wins for independent extra ball lotteries only
			$str_dupextra_wins = '';
			if($blnduplicate && !$outofrange) {
				$str_dupextra_wins = $this->statistics_m->calculate_dupextra_wins($tbl_name, $this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $range, $mx_extra, '');
			}
			
			$followers = array(
				'range'				=> $range,
				'lottery_followers'	=> $str_followers,
				'wins'				=> $str_prizes,
				'positions'			=> $str_positions_prizes,
				'draw_id'			=> $this->data['lottery']->last_drawn['id'],
				'lottery_id'		=> $id,
				'extra_included'	=> $this->data['lottery']->extra_included,
				'extra_draws'		=> $this->data['lottery']->extra_draws
			);
			
			// Add dupextra_wins field only for independent extra ball lotteries
			if($blnduplicate) {
				$followers['dupextra_wins'] = $str_dupextra_wins;
			}
			
			$this->statistics_m->follower_data_save($followers, FALSE);
			$str_nonfollowers = $this->statistics_m->nonfollowers_calculate($tbl_name, $this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $range, $max, '', $blnduplicate, $mx_extra);
			$nonfollowers = array(
						'range'					=> $range,
						'lottery_nonfollowers'	=> $str_nonfollowers,
						'draw_id'				=> $this->data['lottery']->last_drawn['id'],
						'lottery_id'			=> $id
					);
			$this->statistics_m->nonfollower_data_save($nonfollowers, FALSE);
		}
		
		// 4. Extract the follower string into the array counter parts
		$next_draw = (!is_null($followers) ? explode(",", $followers['lottery_followers']) : explode(",", $str_followers));
		foreach($next_draw as $ball_drawn)
		{
			$n = strstr($ball_drawn, '>', TRUE); // Strip off each number
			$f = substr(strstr($ball_drawn, '>', FALSE),1); // Remove the '>' from the string
			
			// Handle independent extra ball format with # separator
			if($blnduplicate && strpos($f, '#') !== FALSE) {
				$parts = explode('#', $f);
				$main_followers = $parts[0]; // Main ball followers
				$extra_followers = isset($parts[1]) ? $parts[1] : '0=0'; // Extra ball followers
				
				// Store main and extra followers
				for($b = 1; $b<=$drawn; $b++)
				{
					if(($this->data['lottery']->last_drawn['ball'.$b]==$n)&&(!isset($this->data['lottery']->last_drawn[$n]))) {
						$this->data['lottery']->last_drawn[$n] = $main_followers;
						$this->data['lottery']->last_drawn[$n.'_extra'] = $extra_followers; // Store extra followers separately
					}
				}
				// Always generate extra ball data when lottery has extra ball, regardless of extra_included setting
				// The extra_included setting controls calculation logic, not data availability for display
				if(($this->data['lottery']->extra_ball)&&($this->data['lottery']->last_drawn['extra']==$n))
				{
					$this->data['lottery']->last_drawn[$n.'x'] = $main_followers; // Main followers for extra ball
					$this->data['lottery']->last_drawn[$n.'x_extra'] = $extra_followers; // Extra followers for extra ball
				}
			} else {
				// Original processing for non-independent extra ball lotteries
				for($b = 1; $b<=$drawn; $b++)
				{
					if(($this->data['lottery']->last_drawn['ball'.$b]==$n)&&(!isset($this->data['lottery']->last_drawn[$n]))) $this->data['lottery']->last_drawn[$n] = $f;
				}
				// Always generate extra ball data when lottery has extra ball, regardless of extra_included setting
				if(($this->data['lottery']->extra_ball)&&(!$blnduplicate)&&($this->data['lottery']->last_drawn['extra']==$n))
				{
					$this->data['lottery']->last_drawn[$n] = $f;
				}
				elseif(($this->data['lottery']->extra_ball)&&($blnduplicate)&&($this->data['lottery']->last_drawn['extra']==$n))
				{
					$this->data['lottery']->last_drawn[$n.'x'] = $f; // denotes x for 'duplicate' extra
				}
			}
		}
		// 5. Do the same for non-following string into the array counter parts also
		$nf_next = (!is_null($nonfollowers) ? explode(",", $nonfollowers['lottery_nonfollowers']) : explode(",", $str_nonfollowers));
		foreach($nf_next as $ball_drawn)
		{
			$n = strstr($ball_drawn, '>', TRUE); // Strip off each number
			$nf = substr(strstr($ball_drawn, '>', FALSE),1); // Remove the '>' from the string
			
			// Handle independent extra ball format with # separator for nonfollowers
			if($blnduplicate && strpos($nf, '#') !== FALSE) {
				$parts = explode('#', $nf);
				$main_nonfollowers = $parts[0]; // Main ball nonfollowers
				$extra_nonfollowers = isset($parts[1]) ? $parts[1] : '0'; // Extra ball nonfollowers
				
				// Store main and extra nonfollowers
				for($b = 1; $b<=$drawn; $b++)
				{
					if(($this->data['lottery']->last_drawn['ball'.$b]==$n)&&(!isset($this->data['lottery']->last_drawn[$n.'nf']))) {
						$this->data['lottery']->last_drawn[$n.'nf'] = $main_nonfollowers;
						$this->data['lottery']->last_drawn[$n.'nf_extra'] = $extra_nonfollowers; // Store extra nonfollowers separately
					}
				}
				// Always generate extra ball data when lottery has extra ball, regardless of extra_included setting
				// The extra_included setting controls calculation logic, not data availability for display
				if(($this->data['lottery']->extra_ball)&&($this->data['lottery']->last_drawn['extra']==$n))
				{
					$this->data['lottery']->last_drawn[$n.'nfx'] = $main_nonfollowers; // Main nonfollowers for extra ball
					$this->data['lottery']->last_drawn[$n.'nfx_extra'] = $extra_nonfollowers; // Extra nonfollowers for extra ball
				}
			} else {
				// Original processing for non-independent extra ball lotteries
				for($b = 1; $b<=$drawn; $b++)
				{
					if(($this->data['lottery']->last_drawn['ball'.$b]==$n)&&(!isset($this->data['lottery']->last_drawn[$n.'nf']))) $this->data['lottery']->last_drawn[$n.'nf'] = $nf;
				}
				// Always generate extra ball data when lottery has extra ball, regardless of extra_included setting
				if(($this->data['lottery']->extra_ball)&&(!$blnduplicate)&&($this->data['lottery']->last_drawn['extra']==$n))
				{
					$this->data['lottery']->last_drawn[$n.'nf'] = $nf;
				}
				elseif(($this->data['lottery']->extra_ball)&&($blnduplicate)&&($this->data['lottery']->last_drawn['extra']==$n))
				{
					$this->data['lottery']->last_drawn[$n.'nfx'] = $nf;
				}
			}
		}
		unset($prizes);													// Remove this array, free up memory
		$this->data['lottery']->out_of_range = $outofrange; 			// Is there enough draws to calculate the prizes?
		$this->data['lottery']->last_drawn['interval'] = $interval;		// Record the interval here (for the dropdown)
		$this->data['lottery']->last_drawn['sel_range'] = $sel_range;	// What was selected for the range in the previous page
		$this->data['lottery']->last_drawn['range'] = $range;
		$this->data['lottery']->last_drawn['all'] = $all;
		
		// DYNAMIC COMPARISON: Fetch the draw using prev_draw_id from the followers table
		// This shows which draw the previous followers were calculated for
		$prev_draw_data = null;
		if ($followers && isset($followers['prev_draw_id']) && $followers['prev_draw_id']) {
			// Use the stored prev_draw_id to get the exact draw
			$prev_draw_data = $this->lotteries_m->get_draw_by_id($tbl_name, $followers['prev_draw_id']);
		}
		
		// Get current draw numbers for comparison
		$current_draw_numbers = array();
		for($b = 1; $b <= $drawn; $b++) {
			$ball_key = 'ball'.$b;
			if (isset($this->data['lottery']->last_drawn[$ball_key])) {
				$current_draw_numbers[] = $this->data['lottery']->last_drawn[$ball_key];
			}
		}
		// Only include extra ball if lottery has it AND user has it enabled (explicitly check == 1)
		if ($this->data['lottery']->extra_ball && $this->data['lottery']->extra_included == 1 && isset($this->data['lottery']->last_drawn['extra'])) {
			$current_draw_numbers['extra'] = $this->data['lottery']->last_drawn['extra'];
		}
		
		if ($prev_draw_data) {
			// Convert previous draw object to array of numbers AND store ball positions
			$prev_draw_numbers = array();
			$prev_draw_balls = array(); // Store ball positions for matching in view
			for($b = 1; $b <= $drawn; $b++) {
				$ball_key = 'ball'.$b;
				if (isset($prev_draw_data->$ball_key)) {
					$prev_draw_numbers[] = $prev_draw_data->$ball_key;
					$prev_draw_balls['ball'.$b] = $prev_draw_data->$ball_key;
				}
			}
			// Only include extra ball if lottery has it AND user has it enabled (explicitly check == 1)
			if ($this->data['lottery']->extra_ball && $this->data['lottery']->extra_included == 1 && isset($prev_draw_data->extra)) {
				$prev_draw_numbers['extra'] = $prev_draw_data->extra;
				$prev_draw_balls['extra'] = $prev_draw_data->extra;
			}
			
			$this->data['prev_draw'] = array(
				'numbers' => $prev_draw_numbers,
				'balls' => $prev_draw_balls,
				'date' => $prev_draw_data->draw_date,
				'exists' => true
			);
			$this->data['current_draw_numbers'] = $current_draw_numbers;
		} else {
			// No previous draw exists (we're at the first draw)
			$this->data['prev_draw'] = array(
				'exists' => false
			);
			$this->data['current_draw_numbers'] = $current_draw_numbers;
		}
		
		$this->data['current'] = $this->uri->segment(2); 				// Sets the Admins Menu Highlighted
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/followers'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	 
		$this->data['subview']  = 'admin/dashboard/statistics/followers';
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
		global $relatives;				// global totals of no friends, 1 way friend, 2 way friends
		global $nonrelatives;			// global non friend occurences
		
		// CRITICAL: Initialize globals to prevent stale data from previous requests
		$relatives = null;
		$nonrelatives = null;
		
		$this->data['message'] = '';	// Defaulted to No Error Messages
		$this->data['lottery'] = $this->lotteries_m->get($id);
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		$blnduplicate = ($this->data['lottery']->duplicate_extra_ball ? TRUE : FALSE);
		$drawn = $this->data['lottery']->balls_drawn;		// Get the number of balls drawn for this lottory, Pick 5, Pick 6, Pick 7, etc.
		$min_ball = $this->data['lottery']->minimum_ball;	// Regular Drawn Low ball e.g. ball 1
		$max_ball = $this->data['lottery']->maximum_ball;	// Get the highest ball drawn for this lottery, e.g. 49 in Lottery 649, 50 in Lottomax
		// Check to see if the actual table exists in the db?
		if (!$this->lotteries_m->lotto_table_exists($tbl_name))
		{
			$this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. '.$tbl_name.' Does not exist. Create the Lottery Database now.');
			redirect('admin/statistics');
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
		$this->data['lottery']->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl_name);	// Retrieve the last drawn numbers and draw date
		$friends = $this->statistics_m->friends_exists($id);
		$nonfriends = $this->statistics_m->nonfriends_exists($id);
		$new_range = $this->uri->segment(5,0); // Return segment range
		$old_range = $friends['range'];
		if(!$new_range) $new_range = $old_range;	// Database Range
		$sel_range = 1;								// All Defaults
		$this->data['lottery']->extra_included = 0; // No Extra Ball as part of the calculation
		$this->data['lottery']->extra_draws = 0; 	// No Bonus Draws included in the friend calculation
		if(!is_null($friends)&&(!is_null($nonfriends)))
		{
			$change = FALSE;  // Default is no change
			$this->data['lottery']->extra_included = $this->uri->segment(6)=='extra' ? $this->statistics_m->extra_included($id, TRUE, 'lottery_friends') : $this->statistics_m->extra_included($id, FALSE, 'lottery_friends');
			$this->data['lottery']->extra_draws = ($this->uri->segment(6)=='draws' ? $this->statistics_m->extra_draws($id, TRUE, 'lottery_friends') : $this->statistics_m->extra_draws($id, FALSE, 'lottery_friends'));
 			$change = ($this->data['lottery']->extra_included!=$friends['extra_included'] ? TRUE : FALSE); // Only for a change in the extra (bonus) ball
			if(!$change)
			{
				$change = ($this->data['lottery']->extra_draws!=$friends['extra_draws'] ? TRUE : FALSE); // Only for a change in the extra draws and there was no change in the extra ball
			}
			
			// Log parameter changes for debugging
			log_message('info', "Friends view - lottery_id=$id, old_range={$friends['range']}, new_range=$new_range, old_extra={$friends['extra_included']}, new_extra={$this->data['lottery']->extra_included}, old_draws={$friends['extra_draws']}, new_draws={$this->data['lottery']->extra_draws}, change=$change");
			
			if($new_range>100) $sel_range = intval($new_range / 100);
			if($new_range!=0)	
			{
				if(intval($old_range)!=(intval($new_range))||($change)) // Any Change in Selection of the Draws? then update ... e.i. 200 draws in db and 300 in query url
				{
					log_message('info', "Friends recalculation triggered - range changed from $old_range to $new_range OR parameters changed (change=$change)");
					
					$relatives = $this->statistics_m->create_friend_array();
					$nonrelatives = $this->statistics_m->create_nonfriend_array();
					
					$str_friends = $this->statistics_m->friends_calculate($tbl_name, $drawn, $max_ball, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, '', $blnduplicate);
					$associate = explode('+', $str_friends); // The '+' is the separator
					$str_friends = $associate[0];			 // separated the friends which is a string
					$str_nonfriends = $associate[1]; 		 // from the non friends which is a string
					$this->statistics_m->friends_hits($str_friends, $str_nonfriends, $tbl_name, $drawn, $max_ball, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, '', $blnduplicate);
					$fr_stats = $this->statistics_m->combine_friends_string($relatives, $str_friends, $max_ball);
					$nfr_stats = $this->statistics_m->combine_nonfriends_string($nonrelatives);
					$matrix = $this->statistics_m->build_friends_matrix($tbl_name, $drawn, $max_ball, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, $blnduplicate);
					
					log_message('info', "Friends recalculation complete - wins=$fr_stats, relatives=".json_encode($relatives));
					
					$friends = array(
						'range'				=> $new_range,
						'lottery_friends'	=> $str_friends,
						'friendship_matrix'	=> json_encode($matrix),
						'wins'				=> $fr_stats,
						'extra_included'	=> $this->data['lottery']->extra_included,
						'extra_draws'		=> $this->data['lottery']->extra_draws,
						'draw_id'			=> $this->data['lottery']->last_drawn['id'],
						'lottery_id'		=> $id
					);
					$this->statistics_m->friends_data_save($friends, TRUE);
					$nonfriends = array(
						'range'					=> $new_range,
						'lottery_nonfriends'	=> $str_nonfriends,
						'draw_id'				=> $this->data['lottery']->last_drawn['id'],
						'lottery_id'			=> $id
					);
					$this->statistics_m->nonfriends_data_save($nonfriends, TRUE);
				}
			}
			else
			{
				$new_range = $all;
			}
		}
		else 
		{
			$relatives = $this->statistics_m->create_friend_array();
			$nonrelatives = $this->statistics_m->create_nonfriend_array();
			$new_range = ($all<100 ? $all : 100);
			$str_friends = $this->statistics_m->friends_calculate($tbl_name, $drawn, $max_ball, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, '', $blnduplicate);
			$associate = explode('+', $str_friends); // The '+' is the separator
			$str_friends = $associate[0];			 // separated the friends
			$str_nonfriends = $associate[1];		 // from the non friends
			$this->statistics_m->friends_hits($str_friends, $str_nonfriends, $tbl_name, $drawn, $max_ball, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, '', $blnduplicate);
			$fr_stats = $this->statistics_m->combine_friends_string($relatives, $str_friends, $max_ball);
			$nfr_stats = $this->statistics_m->combine_nonfriends_string($nonrelatives);
			$matrix = $this->statistics_m->build_friends_matrix($tbl_name, $drawn, $max_ball, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, $blnduplicate);
			
			// Check if friends data already exists (maybe from previous failed calculation)
			$existing_check = $this->statistics_m->friends_exists($id);
			$use_update = !is_null($existing_check);
			
			$friends = array(
				'range'				=> $new_range,
				'lottery_friends'	=> $str_friends,
				'friendship_matrix'	=> json_encode($matrix),
				'wins'				=> $fr_stats,
				'extra_included'	=> $this->data['lottery']->extra_included,
				'extra_draws'		=> $this->data['lottery']->extra_draws,
				'draw_id'			=> $this->data['lottery']->last_drawn['id'],
				'lottery_id'		=> $id
			);
			$this->statistics_m->friends_data_save($friends, $use_update);
			$nonfriends = array(
				'range'					=> $new_range,
				'lottery_nonfriends'	=> $str_nonfriends,
				'draw_id'				=> $this->data['lottery']->last_drawn['id'],
				'lottery_id'			=> $id
			);
			$this->statistics_m->nonfriends_data_save($nonfriends, $use_update);
		}
		
		// 4. Extract the friends string into the array counter parts
		$next_draw = (!is_null($friends) ? explode(",", $friends['lottery_friends']) : explode(",", $str_friends)); // DB or ??
		$nonfriends_draw = (!is_null($nonfriends) ? explode("|", $nonfriends['lottery_nonfriends']) : explode("|", $str_nonfriends)); // DB or ??
		
		// Safety check: Verify friends data has correct number of entries
		$expected_entries = $max_ball;
		$actual_entries = count($next_draw);
		if($actual_entries != $expected_entries) {
			log_message('error', "Friends data mismatch for lottery_id=$id: expected $expected_entries entries, got $actual_entries entries");
			$this->session->set_flashdata('message', "Friends data is incomplete ($actual_entries of $expected_entries balls). Please recalculate friends data.");
		}
		
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
		
		// Fill in missing ball indices to prevent view errors
		for($missing = $b; $missing <= $max_ball; $missing++) {
			$this->data['lottery']->friend['ball'.$missing] = '';
			$this->data['lottery']->friend['count'.$missing] = '0';
			$this->data['lottery']->friend['date'.$missing] = '';
			$this->data['lottery']->nonfriends['ball'.$missing] = '';
		}

		unset($relatives);
		unset($nonrelatives);
		$this->data['lottery']->last_drawn['interval'] = $interval;		// Record the interval here (for the dropdown)
		$this->data['lottery']->last_drawn['sel_range'] = $sel_range;	// What was selected for the range in the previous page
		$this->data['lottery']->last_drawn['range'] = $new_range;
		$this->data['lottery']->last_drawn['all'] = $all;
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/friends'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	 
		$this->data['subview']  = 'admin/dashboard/statistics/friends';
		$this->load->view('admin/_layout_main', $this->data);
	}

	/**
	 * View the Hots, Warms and Colds of Draw based on range. Default is 100 draws or up to the number of draws in the database.
	 * 
	 * @param		$id		current lottery id of draws	
	 * @return  	none
	 */
	public function h_w_c($id)
	{
		$this->data['message'] = '';						// Defaulted to No Error Messages
		$blnheat = FALSE;									// CHANGE flag. default is FALSE, 
		// if the form was submitted, the database values will compared to the submitted ones.
		$this->data['lottery'] = $this->lotteries_m->get($id);
		
		// Check if H-W-C data exists and block access if empty (after reset)
		$hwc_check = $this->statistics_m->h_w_c_exists($id);
		
		if(is_null($hwc_check) || empty($hwc_check['hots']) || empty($hwc_check['warms']) || empty($hwc_check['colds']) || $hwc_check['draw_id'] == 0) {
			$this->session->set_flashdata('message', 'Click the ReCalc checkbox first before viewing H-W-C data.');
			redirect('admin/statistics');
			return;
		}
		
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		$blnduplicate = ($this->data['lottery']->duplicate_extra_ball ? TRUE : FALSE);
		$drawn = $this->data['lottery']->balls_drawn;		// Get the number of balls drawn for this lottory, Pick 5, Pick 6, Pick 7, etc.
		$max_ball = $this->data['lottery']->maximum_ball;	// Get the highest ball drawn for this lottery, e.g. 49 in Lottery 649, 50 in Lottomax
		// Check to see if the actual table exists in the db?
		if (!$this->lotteries_m->lotto_table_exists($tbl_name))
		{
			$this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. '.$tbl_name.' Does not exist. Create the Lottery Database now.');
			redirect('admin/statistics');
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
		$this->data['lottery']->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl_name);	// Retrieve the last drawn numbers and draw date
		$h_w_c = $this->statistics_m->h_w_c_exists($id);
		if(!is_null($h_w_c))	// Existing HWC?
		{
			$new_range = $this->uri->segment(5,0); 					// Return segment range
			$old_range = $h_w_c['range'];
			if(!$new_range) $new_range = $old_range;				// Database Range
			$hots = $h_w_c['h_count'];
			$warms = $h_w_c['w_count'];
			$colds = $h_w_c['c_count'];
			if(empty($this->input->post(NULL, true)))
			{
				$w_start = intval($hots+1);							// Warms
				$this->data['lottery']->H = $hots;  				// Number of Hots Distributed e.g. 16 Hots
				$c_start = ($max_ball-intval($colds))+1; 			// Return the Cold value
				$this->data['lottery']->W = $warms;  				// Number of Warms Distributed e.g 18 Colds
				$this->data['lottery']->C = $colds; 				// Number of Colds Distributed e.g 16 Colds
				$this->data['lottery']->prediction_pool = isset($h_w_c['prediction_pool']) ? $h_w_c['prediction_pool'] : 18; // Default prediction pool
			}
			else
			{
				// Detect which button was pressed and handle accordingly
				$heat_button_pressed = $this->input->post('heat');
				$pool_button_pressed = $this->input->post('change_pool');
				
				// Handle heat level changes
				if($heat_button_pressed) {
					// Get the submitted values from spinners
					$hot_form = $this->input->post('hots');
					$warm_form = $this->input->post('warms');
					$cold_form = $this->input->post('colds');
					
					// Get original values for comparison
					$original_hots = $this->input->post('original_hots');
					$original_warms = $this->input->post('original_warms');
					$original_colds = $this->input->post('original_colds');
					
					// Use current values as fallback if form values are empty
					$hot_form = $hot_form ? $hot_form : $hots;
					$warm_form = $warm_form ? $warm_form : $warms;
					$cold_form = $cold_form ? $cold_form : $colds;
					
					// Check for actual changes by comparing with original values
					$changes_made = false;
					$heat_changes = array();
					
					if($hot_form != $original_hots) {
						$heat_changes[] = "Hots changed from $original_hots to $hot_form";
						$changes_made = true;
					}
					if($warm_form != $original_warms) {
						$heat_changes[] = "Warms changed from $original_warms to $warm_form";
						$changes_made = true;
					}
					if($cold_form != $original_colds) {
						$heat_changes[] = "Colds changed from $original_colds to $cold_form";
						$changes_made = true;
					}
					
					if($changes_made) {
						$blnheat = TRUE;
						$w_start = intval($hot_form + 1);
						$this->data['lottery']->H = $hot_form;
						$c_start = ($max_ball - intval($cold_form)) + 1;
						$this->data['lottery']->W = $warm_form;
						$this->data['lottery']->C = $cold_form;
						
						// Set flash message for changes
						$this->session->set_flashdata('heat_message', implode(', ', $heat_changes));
						$this->session->set_userdata('heat_redirect_needed', true); // Flag for redirect after save
					} else {
						// No changes made, keep current values
						$w_start = intval($hots + 1);
						$this->data['lottery']->H = $hots;
						$c_start = ($max_ball - intval($colds)) + 1;
						$this->data['lottery']->W = $warms;
						$this->data['lottery']->C = $colds;
					}
				}
				
				// Handle prediction number pool changes - check both button press and presence of pool data
				if($pool_button_pressed || $this->input->post('prediction_pool')) {
					$pool_form = $this->input->post('prediction_pool');
					$original_pool = $this->input->post('original_prediction_pool');
					
					// Get current value for fallback
					$current_pool_value = isset($h_w_c['prediction_pool']) ? $h_w_c['prediction_pool'] : 18;
					
					// Only proceed if there's an actual change and form value is valid
					if($pool_form && $pool_form != $original_pool) {
						$min_pool = $drawn; // Minimum is the pick number
						$max_pool = intval($max_ball / 2); // Maximum is half of total numbers
						
						// Validate pool range
						if($pool_form >= $min_pool && $pool_form <= $max_pool) {
							$this->data['lottery']->prediction_pool = $pool_form;
							$this->session->set_flashdata('pool_message', "The Prediction Number Pool has changed from $original_pool to $pool_form Numbers");
							$blnheat = TRUE; // Trigger recalculation
							$this->session->set_userdata('pool_redirect_needed', true); // Flag for redirect after save
						} else {
							$this->session->set_flashdata('error_message', "The Prediction Number Pool value must be between $min_pool and $max_pool");
							$this->data['lottery']->prediction_pool = $current_pool_value; // Keep current value on error
						}
					} else {
						// No change made, keep current value
						$this->data['lottery']->prediction_pool = $current_pool_value;
					}
				}
				
				// Ensure values are set when buttons are pressed but no changes occur
				if(!$heat_button_pressed) {
					$w_start = intval($hots + 1);
					$this->data['lottery']->H = $hots;
					$c_start = ($max_ball - intval($colds)) + 1;
					$this->data['lottery']->W = $warms;
					$this->data['lottery']->C = $colds;
				}
				
				if(!$pool_button_pressed) {
					$this->data['lottery']->prediction_pool = isset($h_w_c['prediction_pool']) ? $h_w_c['prediction_pool'] : 18;
				}
				
				// Ensure w_start and c_start are always set when pool button is pressed but heat button is not
				if($pool_button_pressed && !$heat_button_pressed) {
					if(!isset($w_start)) $w_start = intval($hots + 1);
					if(!isset($c_start)) $c_start = ($max_ball - intval($colds)) + 1;
					if(!isset($this->data['lottery']->H)) $this->data['lottery']->H = $hots;
					if(!isset($this->data['lottery']->W)) $this->data['lottery']->W = $warms;
					if(!isset($this->data['lottery']->C)) $this->data['lottery']->C = $colds;
				}
				
				// Handle H-W-C option (radio button + Change H-W-C Option button)
				$hwc_option_button_pressed = $this->input->post('change_hwc_option');
				if($hwc_option_button_pressed) {
					$this->load->model('Predictions_m', 'predictions_m');
					$posted_option = (int) $this->input->post('hwc_option');
					$posted_option = ($posted_option === 2) ? 2 : 1;
					$posted_select = max(1, (int) $this->input->post('hwc_select'));
					// Get ranked H-W-C groups for this lottery
					$h_w_c_groups = $this->predictions_m->get_h_w_c_range_with_rank($id);
					$group_patterns = array_keys($h_w_c_groups);
					// Determine which pattern to use
					if($posted_option === 1) {
						// Top Ranked: first pattern in the ranked list
						$selected_pattern = !empty($group_patterns) ? $group_patterns[0] : '';
						$posted_select = 1;
					} else {
						// Manual: pick by rank index (1-based)
						$idx = $posted_select - 1;
						$selected_pattern = isset($group_patterns[$idx]) ? $group_patterns[$idx] : (!empty($group_patterns) ? $group_patterns[0] : '');
					}
					// Generate predictions if a pattern is available
					if(!empty($selected_pattern)) {
						$pool_size = isset($h_w_c['prediction_pool']) ? (int)$h_w_c['prediction_pool'] : 18;
						$generated = $this->predictions_m->hwc_only($id, $pool_size, $selected_pattern);
						$hwc_predictions_str = $generated ? $generated : '';
					} else {
						$hwc_predictions_str = '';
					}
					// Preserve prev_h_w_c_predictions — only cleared when the lottery profile
					// itself changes (critical parameters). Pass null to leave it untouched.
					$this->statistics_m->hwc_save_predictions($id, $posted_option, $posted_select, $hwc_predictions_str, null);
					$this->session->set_flashdata('hwc_prediction_message', 'Generating Numbers for the next draw');
					redirect('admin/statistics/h_w_c/' . $id);
					return;
				}
			}
			// Toggle extra_included when /extra is in URL, otherwise use database value
			if($this->uri->segment(6)=='extra') {
				// Toggle the value from what's in the database
				$this->data['lottery']->extra_included = $h_w_c['extra_included'] ? 0 : 1;
				$blnheat = TRUE; // Force recalculation when toggling
			} else {
				// Use the database value
				$this->data['lottery']->extra_included = $h_w_c['extra_included'];
			}
			
			// Toggle extra_draws when /draws is in URL, otherwise use database value
			if($this->uri->segment(6)=='draws') {
				// Toggle the value from what's in the database
				$this->data['lottery']->extra_draws = $h_w_c['extra_draws'] ? 0 : 1;
				$blnheat = TRUE; // Force recalculation when toggling
			} else {
				// Use the database value
				$this->data['lottery']->extra_draws = $h_w_c['extra_draws'];
			}
			$sel_range = ($new_range>100 ? $sel_range = intval($new_range / 100) : $sel_range = 1);
			$strdupextra = "";	// Always empty for all lotteries. exception is a lottery with an extra ball that can have a duplicate number
			$strdupextra_last = ""; // Initialize last draw duplicate extra
			$strhots_last = ""; // Initialize last draw hots
			$strwarms_last = ""; // Initialize last draw warms
			$strcolds_last = ""; // Initialize last draw colds
			if($new_range!=0)	
			{
				if(intval($old_range)!=(intval($new_range))||($blnheat)) // Any Change in Selection of the Draws? then update ... e.i. 200 draws in db and 300 in query url
				{
					// A change has occurred, return the last draw date
					$last_draw = $this->statistics_m->hwc_DrawBeforeLast($tbl_name); // Reuqirements havd changed
					$draw_id_last = $h_w_c['draw_id_last']; // Default to existing value
					if($last_draw) // Only if a previous draw has occurred
					{
						$str_hwc_last = $this->statistics_m->h_w_c_calculate($tbl_name, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, $w_start, $c_start, $last_draw['draw_date'], $blnduplicate);
						$strhots_last = $this->statistics_m->hots($str_hwc_last);
						$strwarms_last = $this->statistics_m->warms($str_hwc_last);
						$strcolds_last = $this->statistics_m->colds($str_hwc_last);
						$draw_id_last = $last_draw['id']; // Update with the actual last draw ID
						if($blnduplicate&&$this->data['lottery']->extra_included) {
							$strdupextra_last = $this->statistics_m->hwc_duple_extra($tbl_name, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, $last_draw['draw_date']);
						}
					}
					$str_hwc = $this->statistics_m->h_w_c_calculate($tbl_name, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, $w_start, $c_start, '', $blnduplicate);
					if($blnduplicate&&$this->data['lottery']->extra_included) $strdupextra = $this->statistics_m->hwc_duple_extra($tbl_name, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, '');
					$strhots = $this->statistics_m->hots($str_hwc);
					$strwarms = $this->statistics_m->warms($str_hwc);
					$strcolds = $this->statistics_m->colds($str_hwc);
					$stroverdue = $this->statistics_m->overdue($strhots, $strwarms, $strcolds, $tbl_name, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, '');
					$hwc = array(
						'range'				=> 	$new_range,
						'hots'				=> 	$strhots,
						'warms'				=> 	$strwarms,
						'colds'				=> 	$strcolds,
						'hots_last'			=> 	$strhots_last,
						'warms_last'		=> 	$strwarms_last,
						'colds_last'		=> 	$strcolds_last,
						'dupextra'			=>	$strdupextra,
						'dupextra_last'		=>	$strdupextra_last,
						'overdue'			=> 	$stroverdue,
						'draw_id'			=> 	$this->data['lottery']->last_drawn['id'],
						'draw_id_last'		=> 	$draw_id_last,
						'lottery_id'		=> 	$id,
						'extra_included'	=> 	$this->data['lottery']->extra_included,
						'extra_draws'		=> 	$this->data['lottery']->extra_draws,
						'w'					=> 	$w_start,
						'c'					=> 	$c_start,
						'h_count'			=> 	$this->data['lottery']->H,
						'w_count'			=> 	$this->data['lottery']->W,
						'c_count'			=> 	$this->data['lottery']->C,
						'prediction_pool'	=> 	$this->data['lottery']->prediction_pool
					);
					$this->statistics_m->hwc_data_save($hwc, TRUE);
					
					// Calculate H-W-C win statistics when recalculation occurs
					// Calculate counts from the actual hot/warm/cold arrays, not from non-existent properties
					$hot_count = count(explode(',', $strhots));
					$warm_count = count(explode(',', $strwarms));
					$cold_count = count(explode(',', $strcolds));
					$this->calculate_hwc_wins($id, $new_range, $this->data['lottery']->prediction_pool, 
						$hot_count, $warm_count, $cold_count,
						$this->data['lottery']->extra_included, $this->data['lottery']->extra_draws);
				}
				else  
				{
					//$new_range = $all;
					$sel_range = intval($h_w_c['range'] / 100);
					if(!$sel_range) $sel_range = 1; // Less than 100 draws
					$strhots = $h_w_c['hots']; 		// Pull from DB
					$strwarms = $h_w_c['warms'];
					$strcolds = $h_w_c['colds'];
					$strhots_last = $h_w_c['hots_last']; 		// Pull from DB
					$strwarms_last = $h_w_c['warms_last'];
					$strcolds_last = $h_w_c['colds_last'];
					$strdupextra = $h_w_c['dupextra'];
					$strdupextra_last = isset($h_w_c['dupextra_last']) ? $h_w_c['dupextra_last'] : "";
					$stroverdue = $h_w_c['overdue']; 
				}
			}
		}
		else 
		{
			$sel_range = 1;								// All Defaults
			$this->data['lottery']->extra_included = 0; // No Extra Ball as part of the calculation
			$this->data['lottery']->extra_draws = 0; 	// No Bonus Draws included in the friend calculation
			$blnduplicate = ($this->data['lottery']->duplicate_extra_ball ? TRUE : FALSE); // Lotteries that ONLY have the extra ball up to a given number
			$new_range = ($all<100 ? $all : 100);
			$heat = explode('-', $this->statistics_m->hwc_defaults[$max_ball]); 	// Break out the H-W-C into a new array
			$w_start = intval($heat[0]+1);							// Warms
			$this->data['lottery']->H = $heat[0];  					// Number of Hots Distributed e.g. 16 Hots
			$c_start = ($max_ball-intval($heat[2]))+1; 				// Return the Cold value
			$this->data['lottery']->W = $heat[1];  					// Number of Warms Distributed e.g 18 Colds
			$this->data['lottery']->C = $heat[2]; 					// Number of Colds Distributed e.g 16 Colds
			$this->data['lottery']->prediction_pool = 18; 			// Default prediction pool
			
			// Calculate last draw H-W-C for new profile
			$last_draw = $this->statistics_m->hwc_DrawBeforeLast($tbl_name);
			$draw_id_last = 0; // Default to 0 for new profiles
			if($last_draw) {
				$str_hwc_last = $this->statistics_m->h_w_c_calculate($tbl_name, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, $w_start, $c_start, $last_draw['draw_date'], $blnduplicate);
				$strhots_last = $this->statistics_m->hots($str_hwc_last);
				$strwarms_last = $this->statistics_m->warms($str_hwc_last);
				$strcolds_last = $this->statistics_m->colds($str_hwc_last);
				$draw_id_last = $last_draw['id']; // Set the actual last draw ID
				if($blnduplicate&&$this->data['lottery']->extra_included) {
					$strdupextra_last = $this->statistics_m->hwc_duple_extra($tbl_name, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, $last_draw['draw_date']);
				}
			}
			
			$str_hwc = $this->statistics_m->h_w_c_calculate($tbl_name, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, $w_start, $c_start, '', $blnduplicate);
			if($blnduplicate&&$this->data['lottery']->extra_included) $strdupextra = $this->statistics_m->hwc_duple_extra($tbl_name, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, '');
			$strhots = $this->statistics_m->hots($str_hwc);
			$strwarms = $this->statistics_m->warms($str_hwc);
			$strcolds = $this->statistics_m->colds($str_hwc);
			$stroverdue = $this->statistics_m->overdue($strhots, $strwarms, $strcolds, $tbl_name, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range);
			$hwc = array(
						'range'				=> 	$new_range,
						'hots'				=> 	$strhots,
						'warms'				=> 	$strwarms,
						'colds'				=> 	$strcolds,
						'hots_last'			=> 	$strhots_last,
						'warms_last'		=> 	$strwarms_last,
						'colds_last'		=> 	$strcolds_last,
						'dupextra'			=>	$strdupextra,
						'dupextra_last'		=>	$strdupextra_last,
						'overdue'			=> 	$stroverdue,
						'draw_id'			=> 	$this->data['lottery']->last_drawn['id'],
						'draw_id_last'		=> 	$draw_id_last,
						'lottery_id'		=> 	$id,
						'extra_included'	=> 	$this->data['lottery']->extra_included,
						'extra_draws'		=> 	$this->data['lottery']->extra_draws,
						'w'					=> 	$w_start,
						'c'					=> 	$c_start,
						'h_count'			=> 	$this->data['lottery']->H,
						'w_count'			=> 	$this->data['lottery']->W,
						'c_count'			=> 	$this->data['lottery']->C,
						'prediction_pool'	=> 	$this->data['lottery']->prediction_pool
					);
			$this->statistics_m->hwc_data_save($hwc, FALSE);
			
			// Calculate H-W-C win statistics for new H-W-C setup
			// Calculate counts from the actual hot/warm/cold arrays, not from non-existent properties
			$hot_count = count(explode(',', $strhots));
			$warm_count = count(explode(',', $strwarms));
			$cold_count = count(explode(',', $strcolds));
			$this->calculate_hwc_wins($id, $new_range, $this->data['lottery']->prediction_pool, 
				$hot_count, $warm_count, $cold_count,
				$this->data['lottery']->extra_included, $this->data['lottery']->extra_draws);
		}
		$hots = explode(",", $strhots); // Convert to Arrays
		$warms = explode(",", $strwarms); 
		$colds = explode(",", $strcolds); 
		if(!empty($strdupextra)) $dupextra = explode(",", $strdupextra);
		$overdue = explode(",", $stroverdue); 
		// Iterate Hots
		foreach($hots as $all_hots)
		{
			$n = strstr($all_hots, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
			$c = substr(strstr($all_hots, '='), 1); // Strip off to the left of the equal sign count
			$this->data['lottery']->hots[$n] = $c; 
		}
		// Interate Warms
		foreach($warms as $all_warms)
		{
			$n = strstr($all_warms, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
			$c = substr(strstr($all_warms, '='), 1); // Strip off to the left of the equal sign count
			$this->data['lottery']->warms[$n] = $c; 
		}
		// Iterate Colds
		foreach($colds as $all_colds)
		{
			$n = strstr($all_colds, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
			$c = substr(strstr($all_colds, '='), 1); // Strip off to the left of the equal sign count
			$this->data['lottery']->colds[$n] = $c; 
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
		// Iterate Overdues
		$overdue_temp = array();
		foreach($overdue as $all_overdue)
		{
			$n = strstr($all_overdue, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
			$c = substr(strstr($all_overdue, '='), 1); // Strip off to the left of the equal sign count
			$skips = explode('|', $c); // Get draws skipped value
			$overdue_temp[] = array('ball' => $n, 'value' => $c, 'skips' => intval($skips[0]));
		}
		// Sort by draws skipped (ascending - least to most)
		usort($overdue_temp, function($a, $b) {
			return $a['skips'] - $b['skips'];
		});
		// Rebuild the overdue array in sorted order
		foreach($overdue_temp as $item) {
			$this->data['lottery']->overdue[$item['ball']] = $item['value'];
		}
		$hwc_history = $this->statistics_m->hwc_history_exists($id);
 		if(is_null($hwc_history)) // Correct Lottery & Range?
		{
			$hwc_history = $this->h_w_c_history($id, $tbl_name, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, $w_start, $c_start, $blnduplicate);
			$hwc_history['position_last'] = $this->statistics_m->positions_before_last($tbl_name, $drawn, $this->data['lottery']->extra_included, $blnduplicate, $strhots_last, $strwarms_last, $strcolds_last, $hwc_history['position']);
			if (!$hwc_history) // Problem with calculating H-W-C's over range
			{
				$this->session->set_flashdata('message', 'There is a problem with the H (Hots) - W (Warms) - C (Colds) over the last '.$new_range.' Draws.');
				redirect('admin/statistics');
			}
			if(empty($hwc_history['draw_id_last'])) $hwc_history['draw_id_last']=$this->data['lottery']->last_drawn['id']; // Last Draw ID
			$hwc_history['h_w_c_range'] = substr($hwc_history['h_w_c_range'], 0, -1);  				// Remove the last comma
			$hwc_history['h_w_c_last_10'] = substr($hwc_history['h_w_c_last_10'], 0, -1);
			$this->data['lottery']->last_hwc = $hwc_history['h_w_c_last_1'];
			// Iterate Top H - W - C's from Range
			$hwc_totals = explode(',',$hwc_history['h_w_c_range']);							
			foreach($hwc_totals as $heat)
			{
				$n = strstr($heat, '=', TRUE);										// Strip off the h-w-c to the left of the equal sign
				$c = substr(strrchr($heat, "="), 1); 								// Strip off the count to the right of the equal sign
				$this->data['lottery']->hwc[$n] = $c; 
			}
			// Iterate H - W - C's from last 10 draws
			$hwc_last10 = explode(',',$hwc_history['h_w_c_last_10']);							
			foreach($hwc_last10 as $heat)
			{
				$n = strstr($heat, '=', TRUE);										// Strip off the h-w-c to the left of the equal sign
				$c = substr(strrchr($heat, "="), 1); 								// Strip off the count to the right of the equal sign
				$this->data['lottery']->last10[$n] = $c; 
			}
			$hwc_h_data = array(
								'range'				=>	$new_range,
								'h_w_c_range'		=> 	$hwc_history['h_w_c_range'],
								'h_w_c_last_1'		=> 	$this->data['lottery']->last_hwc,
								'h_w_c_last_10'		=> 	$hwc_history['h_w_c_last_10'],
								'position'			=> 	$hwc_history['position'],
								'position_last'		=> 	$hwc_history['position_last'],
								'draw_id'			=> 	$this->data['lottery']->last_drawn['id'],
								'draw_id_last'		=> 	$hwc_history['draw_id_last'],
								'lottery_id'		=> 	$id,
								'extra_included'	=> 	$this->data['lottery']->extra_included,
								'extra_draws'		=> 	$this->data['lottery']->extra_draws,
								);
			$this->statistics_m->hwc_history_save($hwc_h_data, FALSE); // New Record
		}
		else
		{
			if(($old_range!=$new_range)||($blnheat))	 // Range has changed OR change in extra draws / extra ball included
			{
				// Recalculation is nesessary
				$hwc_history = $this->h_w_c_history($id, $tbl_name, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, $w_start, $c_start, $blnduplicate);
				$hwc_history['position_last'] = $this->statistics_m->positions_before_last($tbl_name, $drawn, $this->data['lottery']->extra_included, $blnduplicate, $strhots_last, $strwarms_last, $strcolds_last, $hwc_history['position']);
				if (!$hwc_history) // Problem with calculating H-W-C's over range
				{
					$this->session->set_flashdata('message', 'There is a problem with the H (Hots) - W (Warms) - C (Colds) over the last '.$$new_range.' Draws.');
				redirect('admin/statistics');
				}
				if(empty($hwc_history['draw_id_last'])) $hwc_history['draw_id_last']=$this->data['lottery']->last_drawn['id']; // Last Draw ID
				$hwc_history['h_w_c_range'] = substr($hwc_history['h_w_c_range'], 0, -1);  				// Remove the last comma
				$hwc_history['h_w_c_last_10'] = substr($hwc_history['h_w_c_last_10'], 0, -1);
			}
			$this->data['lottery']->last_hwc = $hwc_history['h_w_c_last_1'];
			$hwc_totals = explode(',',$hwc_history['h_w_c_range']); 		// Strip off the h-w-c to the right of the ','
			foreach($hwc_totals as $heat)
			{
				$n = strstr($heat, '=', TRUE); 					// Strip off the h-w-c to the left of the equal sign
				$c = substr(strchr($heat, "="), 1);				// Strip off the count to the right of the equal sign
				$this->data['lottery']->hwc[$n] = $c; 
			}
			$hwc_last10 = explode(',',$hwc_history['h_w_c_last_10']); 		// Strip off the h-w-c to the right of the ','
			foreach($hwc_last10 as $heat)
			{
				$n = strstr($heat, '=', TRUE); 						// Strip off the h-w-c to the left of the equal sign
				$c = substr(strrchr($heat, "="), 1);				// Strip off the count to the right of the equal sign
				$this->data['lottery']->last10[$n] = $c; 
			}
			$hwc_h_data = array(
								'range'				=>	$new_range,
								'h_w_c_range'		=> 	$hwc_history['h_w_c_range'],
								'h_w_c_last_1'		=> 	$this->data['lottery']->last_hwc,
								'h_w_c_last_10'		=> 	$hwc_history['h_w_c_last_10'],
								'position'			=> 	$hwc_history['position'],
								'position_last'		=> 	$hwc_history['position_last'],
								'draw_id'			=> 	$this->data['lottery']->last_drawn['id'],
								'draw_id_last'		=> 	$hwc_history['draw_id_last'],
								'lottery_id'		=> 	$id,
								'extra_included'	=> 	$this->data['lottery']->extra_included,
								'extra_draws'		=> 	$this->data['lottery']->extra_draws,
								);
			$this->statistics_m->hwc_history_save($hwc_h_data, TRUE); // Update existing lottery H W C Record
		}
		unset($hwc_history); // Remove this temporary holding place for historic h-w-c's
		$this->data['lottery']->last_drawn['interval'] = $interval;		// Record the interval here (for the dropdown)
		$this->data['lottery']->last_drawn['sel_range'] = $sel_range;	// What was selected for the range in the previous page
		$this->data['lottery']->last_drawn['range'] = $new_range;
		$this->data['lottery']->last_drawn['all'] = $all;
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/h_w_c'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	 
		// Check if we need to redirect after successful database save
		if($this->session->userdata('pool_redirect_needed') || $this->session->userdata('heat_redirect_needed')) {
			$this->session->unset_userdata('pool_redirect_needed'); // Clear the flags
			$this->session->unset_userdata('heat_redirect_needed');
			redirect('admin/statistics/h_w_c/' . $id);
		}
		
		// Redirect after toggle to prevent re-toggling on refresh
		if($this->uri->segment(6)=='extra' || $this->uri->segment(6)=='draws') {
			// Build redirect URL with current range selection
			$redirect_url = 'admin/statistics/h_w_c/' . $id;
			// Include range if it was specified in the original URL (segment 5 exists)
			if($this->uri->segment(5)) {
				$redirect_url .= '/' . $this->uri->segment(5);
			}
			redirect($redirect_url);
		}
		
		// Load prediction-related data for the H-W-C option UI
		$this->load->model('Predictions_m', 'predictions_m');
		$h_w_c_current = $this->statistics_m->h_w_c_exists($id);
		$this->data['hwc_option']      = isset($h_w_c_current['hwc_option'])      ? (int)$h_w_c_current['hwc_option']      : 1;
		$this->data['hwc_select']      = isset($h_w_c_current['hwc_select'])      ? (int)$h_w_c_current['hwc_select']      : 1;
		$this->data['hwc_predictions'] = isset($h_w_c_current['hwc_predictions']) ? $h_w_c_current['hwc_predictions']      : '';
		$this->data['h_w_c_group']     = $this->predictions_m->get_h_w_c_range_with_rank($id);
		
		$this->data['subview']  = 'admin/dashboard/statistics/h_w_c';
		$this->load->view('admin/_layout_main', $this->data);
	}
	/**
	 * h_w_c_history. Calculates the Lottery the Historic H-W-C.  It will determine the last H - W - C from the range of draws and the H-W-C from the last draw.
	 * For example, if a 100 draw range is selected, there must be a minimum of 100 draws prior to calcuatling the 100 draw range.  
	 * This will remain constant for all ranges. e.g. 100 draws will be calculated prior to a 200, 300, 400, 500 or all draw ranges.
	 * @param	integer	$id				lottery_id (identifier)
	 * @param 	string	$table			Exact name of the lottery
	 * @param 	integer	$picks			Pick 6, Pick 7, etc.
	 * @param 	boolean	$bn				Extra Ball included, 0 = no, 1 = yes
	 * @param 	boolean	$xtra			Extra Draws (extra = 0?) are included in the calculation, 0 = no 1 = yes
 	 * @param 	integer	$range			Range of Draws
	 * @param 	integer	$w_bound		Passed value. Start of the warm numbers begin. e.g 1-16 Hots, 17-33 Warms, 34-49 Colds in 49 system
	 * @param 	integer $c_bound		Passed value. Cold count of the numbers .e.g 16 hot, 17 warm adn 16 cold for a pick 6 - 49 system
	 * @param	boolean $dup			Boolean True or False. If the extra ball is separate from the other balls that were drawn. True = Yes, False = No
	 * @return 	string	$totals			String return of the range of draws H-W-Cs and the last draw H-W-C.
	 */
	public function h_w_c_history($id, $table, $picks, $bn, $xtra, $range, $w_bound, $c_bound, $dup)
	{
		$scale = $this->statistics_m->hwc_size($id);
		$h_cnt = intval($scale['h_count']); // integer count only
		$w_cnt = intval($scale['w_count']);	// integer count only
		$c_cnt = intval($scale['c_count']); // integer count only
 		$h_pos = new SplFixedArray($h_cnt); 	// Declare the hot positions
			$h_pos = array_fill(0, $h_cnt, 0); // Zeroed array
		$w_pos = new SplFixedArray($w_cnt); // Declare the warm positions
			$w_pos = array_fill(0, $w_cnt, 0); // Zeroed array
		$c_pos = new SplFixedArray($c_cnt); // Declare the colds positions
			$c_pos = array_fill(0, $c_cnt, 0); // Zeroed array		
		$examine_date = $this->statistics_m->lottery_return_date($table, $range+1, $xtra); 	// Please note: This an off by 1 error. It has to go +1 draw back 
		if(!$examine_date) return false;													// to iterate for the given range
		
		$heats = explode(",",$this->statistics_m->hwc_heats[$picks]); 						// break the heat h-w-c in an array
		$heats = array_flip($heats);								  						// reverse the values as associative keys
		foreach($heats as $level => $value)
		{
			$heats[$level] = 0;		// Will be used as counters and zero out the values
		}
		$row  = 1;	// Starting point at $row 1
		do
		{
			// Calculate H-W-C BEFORE getting the next draw (prediction is based on past, not including the draw being tested)
			$str_h_w_c = $this->statistics_m->h_w_c_calculate($table, $picks, $bn, $xtra, $range, $w_bound, $c_bound, $examine_date, $dup);
			$str_hots = $this->statistics_m->hots($str_h_w_c);
			$str_warms = $this->statistics_m->warms($str_h_w_c);
			$str_colds = $this->statistics_m->colds($str_h_w_c);
			$hots = explode(",", $str_hots);
			$warms = explode(",", $str_warms);
			$colds = explode(",", $str_colds);
			$highs = array();
			$averages = array();
			$lows = array();
			foreach($hots as $key => $value) 			
			{
				$h = explode('=', $hots[$key]);
				array_push($highs, $h[0]);
			}
			foreach($warms as $key => $value) 
			{
				$w = explode('=', $warms[$key]);
				array_push($averages, $w[0]);
			}
			foreach($colds as $key => $value) // Remove the odd elements
			{
				$c = explode('=', $colds[$key]);
				array_push($lows, $c[0]);
			}
			
			// NOW get the next draw AFTER the H-W-C calculation date
			$fd = $this->statistics_m->hwc_next_draw($table, $examine_date); // return the full with draw date, ball 1 ... ball n + extra
			if($fd)	// next draw returned?
			{
				$next_drawn = $this->statistics_m->only_picks($picks, $fd);
				
				// Determine your positional values based on heat, Hot, Warm, Cold
				$h_pos = $this->statistics_m->positions($next_drawn,$highs,$h_pos,$bn,$xtra,$dup); 		// Pass the hot positional value array, compare the current drawn numbers with the high numbers 
				$w_pos = $this->statistics_m->positions($next_drawn,$averages,$w_pos,$bn,$xtra,$dup); 	// Pass the hot positional value array, compare the current drawn numbers with the average numbers 
				$c_pos = $this->statistics_m->positions($next_drawn,$lows,$c_pos,$bn,$xtra,$dup); 		// Pass the hot positional value array, compare the current drawn numbers with the average numbers 
				
				$examine_date = $fd['draw_date'];	// Move to next date for next iteration
			}
			else
			{
				break;
			}
			$h = 0; $w = 0; $c = 0;
			foreach($next_drawn as $temp)
			{
				if(in_array($temp, $highs)) $h++;
				if(in_array($temp, $averages)) $w++;
				if(in_array($temp, $lows)) $c++;
			}
			$lvl = (string)$h.'-'.$w.'-'.$c;
			if(array_key_exists($lvl, $heats)) $heats[$lvl]++; 
			$row++;
		} 
		while($row<=$range);
 		$totals = array();
		$totals['h_w_c_last_1'] = $lvl;	// include the last h-w-c
		$totals['h_w_c_range'] = '';
		foreach($heats as $hwc => $tot)
		{
			$totals['h_w_c_range'] .= $hwc.'='.$tot.',';
		}
		// next, do the last 10 draws
		$examine_date = $this->statistics_m->lottery_return_date($table, 11, $xtra);	// Please note: This an off by 1 error. It has to go 11 draws 
		if(!$examine_date) return false;												// back to interate for 10 draws.
		foreach($heats as $level => $value)
		{
			$heats[$level] = 0;		// Will be used as counters and zero out the values
		}
		$row  = 1;	// Starting point at $row 1
		do
		{
			// Calculate H-W-C BEFORE getting the next draw
			$str_h_w_c = $this->statistics_m->h_w_c_calculate($table, $picks, $bn, $xtra, $range, $w_bound, $c_bound, $examine_date);
			$str_hots = $this->statistics_m->hots($str_h_w_c);
			$str_warms = $this->statistics_m->warms($str_h_w_c);
			$str_colds = $this->statistics_m->colds($str_h_w_c);
			$hots = explode(",", $str_hots);
			$warms = explode(",", $str_warms);
			$colds = explode(",", $str_colds);
			$highs = array();
			$averages = array();
			$lows = array();
			foreach($hots as $key => $value) 			
			{
				$h = explode('=', $hots[$key]);
				array_push($highs, $h[0]);
			}
			foreach($warms as $key => $value) 
			{
				$w = explode('=', $warms[$key]);
				array_push($averages, $w[0]);
			}
			foreach($colds as $key => $value) // Remove the odd elements
			{
				$c = explode('=', $colds[$key]);
				array_push($lows, $c[0]);
			}
			
			// NOW get the next draw AFTER the H-W-C calculation
			$fd = $this->statistics_m->hwc_next_draw($table, $examine_date); // return the full with draw date, ball 1 ... ball n + extra
			if($fd)	// next draw returned?
			{
				$next_drawn = $this->statistics_m->only_picks($picks, $fd);
				$examine_date = $fd['draw_date'];	// Move to next date for next iteration
			}
			else
			{
				break;
			}
			$h = 0; $w = 0; $c = 0;
			foreach($next_drawn as $temp)
			{
				if(in_array($temp, $highs)) $h++;
				if(in_array($temp, $averages)) $w++;
				if(in_array($temp, $lows)) $c++;
			}
			$lvl = (string)$h.'-'.$w.'-'.$c;
			if(array_key_exists($lvl, $heats)) $heats[$lvl]++; 
			$row++;
		} 
		while($row<=10);
		$totals['h_w_c_last_10'] = '';
		foreach($heats as $hwc => $tot)
		{
			$totals['h_w_c_last_10'] .= $hwc.'='.$tot.',';
		}
		$str_positions = $this->statistics_m->positIon_string($h_pos, $w_pos, $c_pos); 			// Generate the formatted string
		$totals['position'] = $str_positions; 										   			// Add a new position elemnt to the totals
	return $totals;
	}
	/**
	 * Return the complete draws of the lottery and return the data in the form of JSON
	 * 
	 * @param 		$id		Lottery id
	 * @return  	none
	 */
	public function return_draws($id)
	{
		$table = $this->uri->segment(4);
		$trnd = $this->uri->segment(5);
		$this->db->query('SET @draw_number = 0; '); // Add a Draw Number to the Draw List
		$where = (!$trnd ? '' : ' AND extra <> 0');
		$query = $this->db->query('SELECT *, 
				(@draw_number:=@draw_number + 1) AS draw 
				FROM '.$table.' WHERE lottery_id='.$id.$where. 
				' ORDER BY draw_date ASC;');					
		$result_db = $query->result_array();
		echo json_encode($result_db);
	}
	/**
	 * ReCALCULATES all the H-W-C, Followers and Friends all in one action ONLY after the draw statistics are completed.  
	 * 
	 * @param 		$id		Lottery id
	 * @return  	none
	 */
	public function recalc($id)
	{
		// 1. Determine if the draws have the columns with the Statistics data
		$this->data['message'] = '';	// Defaulted to No Error Messages
		$this->data['lottery'] = $this->lotteries_m->get($id);
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		
		// Check minimum draw requirement before attempting recalculation
		// If lottery has extra_ball = 1, exclude draws where extra = 0 from count
		if (intval($this->data['lottery']->extra_ball) == 1) {
			// Count only draws where extra != 0 (valid draws with extra ball)
			$draw_count = $this->statistics_m->lottery_rows_noextra($tbl_name, $this->data['lottery']->extra_ball);
			if ($draw_count === FALSE || $draw_count === NULL) $draw_count = 0;
		} else {
			// Count all draws
			$draw_count = $this->lotteries_m->db_row_count($tbl_name);
			if ($draw_count === FALSE || $draw_count === NULL) $draw_count = 0;
		}
		
		// Get prediction_min_range (default to 25 if not set)
		$prediction_min_range = isset($this->data['lottery']->prediction_min_range) && $this->data['lottery']->prediction_min_range > 0 
			? intval($this->data['lottery']->prediction_min_range) 
			: 25;
		
		$required_draws = $prediction_min_range * 2;
		
		// If minimum draws not met, show error message and redirect
		if ($draw_count < $required_draws) {
			$draws_remaining = $required_draws - $draw_count;
			$extra_note = (intval($this->data['lottery']->extra_ball) == 1) 
				? ' <em>(Note: Only draws with valid extra ball numbers are counted. Draws with extra = 0 are excluded.)</em>' 
				: '';
			$this->session->set_flashdata('message', 
				'<div class="alert alert-warning">' .
				'<strong>Insufficient Draws:</strong> This lottery (' . htmlspecialchars($this->data['lottery']->lottery_name) . ') requires a minimum of <strong>' . $required_draws . 
				' draws</strong> before ReCalc can be performed. Currently there are <strong>' . $draw_count . 
				' valid draw(s)</strong> in the database. Please add <strong>' . $draws_remaining . ' more valid draw(s)</strong> before attempting recalculation.' .
				$extra_note .
				'</div>'
			);
			redirect('admin/statistics');
			return;
		}
	
		$recalc = FALSE;
		$stats_exist = $this->statistics_m->last_stats_exist($tbl_name);
		
		if($stats_exist) /** First Check to see that the lottery db exists and statistics exist */
		{
			$draw_id = $this->statistics_m->last_id($tbl_name);
			$needs_update = $this->statistics_m->recalc_update($id, $draw_id);
			
			// Also check if followers data was specifically reset (cleared but record exists)
			$followers_reset = FALSE;
			$existing_followers = $this->statistics_m->followers_exists($id);
			if (!is_null($existing_followers) && (empty($existing_followers['wins']) || empty($existing_followers['positions']))) {
				$followers_reset = TRUE;
			}
			
			if($needs_update || $followers_reset) // Second, if a new draw has been entered or manually entered, or followers were reset, return true to recalc //
			{
				$recalc = TRUE;
				
				// Initialize extra_included and extra_draws properties from database (preserve user settings)
				if(!isset($this->data['lottery']->extra_included)) {
					// Read saved checkbox state from database instead of defaulting to 0
					$saved_extra_included = $this->statistics_m->extra_included($id, FALSE, 'lottery_followers');
					$this->data['lottery']->extra_included = $saved_extra_included ? 1 : 0;
				}
				if(!isset($this->data['lottery']->extra_draws)) {
					// Read saved checkbox state from database instead of defaulting to 0  
					$saved_extra_draws = $this->statistics_m->extra_draws($id, FALSE, 'lottery_followers');
					$this->data['lottery']->extra_draws = $saved_extra_draws ? 1 : 0;
				}
				
				// Verified the Draw Statistics have been completed
				// 1. The H (Hots) - W (Warms) - C (Colds) will be RE-CALC'd
				$this->recalc_hwc($id,$this->data['lottery']);
				
				// 2. The Followers will be RE-CALC'd
				$this->recalc_followers($id,$this->data['lottery']);

				// 3. The Friends of numbers will be RE-CALC'd	
				$this->recalc_friends($id, $this->data['lottery']);
			} else {
				// Statistics are already up to date
				$recalc = FALSE;
			}
		}
		else
		{	// Verfied that the Draw Statistics have not been completed, exit action with an error message
			log_message('error', "Recalc: last_stats_exist returned FALSE for table=$tbl_name - statistics don't exist");
			$this->session->set_flashdata('message', 'The latest Draw has not been completed. Please enter the next draw and click the Calculator to complete the draw statistics.');
			redirect('admin/statistics');
		}
		if($recalc)
		{
			$this->session->set_flashdata('message', 'The Hot - Warm - Cold, Followers and Friends Statistics have ALL been updated to the latest draw.');
			redirect('admin/statistics');
		}
		else
		{
			// Recalculation was not needed or recalc_update returned FALSE
			$this->session->set_flashdata('message', 'Statistics are already up-to-date. No recalculation was needed.');
			redirect('admin/statistics');
		}
	}
	
	/**
	 * Reset follower statistics to force complete recalculation from scratch
	 * 
	 * @return  	none
	 */
	public function reset_followers()
	{
		// Set JSON header using CodeIgniter's output library
		$this->output->set_content_type('application/json');
		
		try {
			$admin_id = $this->session->userdata('id');
			
			if (!$admin_id) {
				$this->output->set_output(json_encode(['success' => false, 'message' => 'Not authorized']));
				return;
			}
			
			$id = $this->input->post('lottery_id');
			
			if (!$id || !is_numeric($id)) {
				$this->output->set_output(json_encode(['success' => false, 'message' => 'Invalid lottery ID']));
				return;
			}
			
			// Get lottery info
			$lottery = $this->lotteries_m->get($id);
			if (!$lottery) {
				$this->output->set_output(json_encode(['success' => false, 'message' => 'Lottery not found']));
				return;
			}
			
			// Reset follower statistics in database - clear only calculation data, preserve settings
			// First, get current settings before clearing data
			$current_followers = $this->statistics_m->followers_exists($id);
			$current_nonfollowers = $this->statistics_m->nonfollowers_exists($id);
			
			// VALIDATION: Check if prev_draw_id is exactly 1 draw before draw_id
			// If not valid, clear prev_* fields to prevent incorrect data display
			$tbl_name = $this->lotteries_m->lotto_table_convert($lottery->lottery_name);
			
			if($current_followers && isset($current_followers['draw_id']) && $current_followers['draw_id'] > 0) {
				$current_draw_id = $current_followers['draw_id'];
				$stored_prev_draw_id = isset($current_followers['prev_draw_id']) ? $current_followers['prev_draw_id'] : null;
				
				// Get the actual previous draw before current_draw_id
				$actual_prev_draw = $this->lotteries_m->get_previous_draw($tbl_name, $current_draw_id);
				$actual_prev_draw_id = $actual_prev_draw ? $actual_prev_draw->id : null;
				
				// Validate: stored prev_draw_id must match actual previous draw
				if($stored_prev_draw_id && $actual_prev_draw_id && $stored_prev_draw_id != $actual_prev_draw_id) {
					// Invalid prev_draw_id - clear prev_* fields for both followers and nonfollowers
					log_message('info', "RESET VALIDATION: Lottery $id - prev_draw_id ($stored_prev_draw_id) does not match actual previous draw ($actual_prev_draw_id). Clearing prev_* fields.");
					
					$this->db->where('lottery_id', $id);
					$this->db->update('lottery_followers', array(
						'prev_lottery_followers' => null,
						'prev_draw_id' => null
					));
					
					$this->db->where('lottery_id', $id);
					$this->db->update('lottery_nonfollowers', array(
						'prev_lottery_nonfollowers' => null,
						'prev_draw_id' => null
					));
					
					// Clear cache and reload the data after clearing prev_* fields
					$this->statistics_m->clear_follower_cache($id);
					$current_followers = $this->statistics_m->followers_exists($id);
					$current_nonfollowers = $this->statistics_m->nonfollowers_exists($id);
				}
			}
			
			// Clear calculation data AND draw_id to force recalculation
			// The draw_id field is what recalc_update() checks to determine if recalc is needed
			if($current_followers) {
				$clear_data = array(
					'lottery_followers' => '',
					'wins' => '',
					'positions' => '',
					'draw_id' => 0  // Clear draw_id to force recalculation
				);
				if(isset($current_followers['dupextra_wins'])) {
					$clear_data['dupextra_wins'] = '';
				}
				$this->db->where('lottery_id', $id);
				$this->db->update('lottery_followers', $clear_data);
			} else {
				$this->db->where('lottery_id', $id);
				$this->db->delete('lottery_followers');
			}
			
			if($current_nonfollowers) {
				$clear_data = array(
					'lottery_nonfollowers' => '',
					'draw_id' => 0  // Clear draw_id to force recalculation
				);
				$this->db->where('lottery_id', $id);
				$this->db->update('lottery_nonfollowers', $clear_data);
			} else {
				$this->db->where('lottery_id', $id);
				$this->db->delete('lottery_nonfollowers');
			}
			
			// CRITICAL: Clear the cache for this lottery's follower data
			// Otherwise followers_exists() will return stale cached data
			$this->statistics_m->clear_follower_cache($id);
			$this->output->set_output(json_encode([
				'success' => true, 
				'message' => 'Follower statistics reset successfully. Next ReCalc will start from scratch.'
			]));
			
		} catch (Exception $e) {
			log_message('error', "Reset followers error: " . $e->getMessage());
			log_message('error', "Reset followers stack trace: " . $e->getTraceAsString());
			$this->output->set_output(json_encode(['success' => false, 'message' => 'Error resetting follower statistics: ' . $e->getMessage()]));
		}
	}
	
	/**
	 * Reset H-W-C statistics to force full recalculation
	 * Clears calculation data but preserves settings (range, extra_included, etc.)
	 * 
	 * @return	JSON response
	 */
	public function reset_hwc()
	{
		$this->output->set_content_type('application/json');
		
		try {
			$admin_id = $this->session->userdata('id');
			
			if (!$admin_id) {
				$this->output->set_output(json_encode(['success' => false, 'message' => 'Not authorized']));
				return;
			}
			
			$id = $this->input->post('lottery_id');
			
			if (!$id || !is_numeric($id)) {
				$this->output->set_output(json_encode(['success' => false, 'message' => 'Invalid lottery ID']));
				return;
			}
			
			// Get lottery info
			$lottery = $this->lotteries_m->get($id);
			if (!$lottery) {
				$this->output->set_output(json_encode(['success' => false, 'message' => 'Lottery not found']));
				return;
			}
			
			// Reset H-W-C statistics - clear only calculation data, preserve settings
			$current_hwc = $this->statistics_m->h_w_c_exists($id);
			$current_hwc_stats = $this->statistics_m->hwc_stats_exists($id);
			
			// Clear lottery_h_w_c table
			if($current_hwc) {
				$clear_data = array(
					'hots' => '',
					'hots_last' => '',
					'warms' => '',
					'warms_last' => '',
					'colds' => '',
					'colds_last' => '',
					'dupextra' => '',
					'dupextra_last' => '',
					'overdue' => '',
					'draw_id' => 0,  // Clear draw_id to force recalculation
					'draw_id_last' => 0
				);
				$this->db->where('lottery_id', $id);
				$this->db->update('lottery_h_w_c', $clear_data);
			} else {
				$this->db->where('lottery_id', $id);
				$this->db->delete('lottery_h_w_c');
			}
			
			// Clear lottery_h_w_c_stats table
			if($current_hwc_stats) {
				$clear_data = array(
					'range' => 0,
					'h_w_c_range' => '',
					'h_w_c_last_1' => '',
					'h_w_c_last_10' => '',
					'position' => '',
					'position_last' => '',
					'wins' => '',
					'draw_id' => 0,
					'draw_id_last' => 0
				);
				$this->db->where('lottery_id', $id);
				$this->db->update('lottery_h_w_c_stats', $clear_data);
			} else {
				$this->db->where('lottery_id', $id);
				$this->db->delete('lottery_h_w_c_stats');
			}
			
			// Clear the cache for this lottery's H-W-C data
			$this->statistics_m->clear_hwc_cache($id);
			
			$this->output->set_output(json_encode([
				'success' => true, 
				'message' => 'H-W-C statistics reset successfully. Next ReCalc will start from scratch.'
			]));
			
		} catch (Exception $e) {
			log_message('error', "Reset H-W-C error: " . $e->getMessage());
			log_message('error', "Reset H-W-C stack trace: " . $e->getTraceAsString());
			$this->output->set_output(json_encode(['success' => false, 'message' => 'Error resetting H-W-C statistics: ' . $e->getMessage()]));
		}
	}
	
	/**
	 * ReCALCULATES the Lottery H-W-C, it will retrieve the last H-W-C. If it exists, the first draw (for the given range) will be retrieved.
	 * Each number that was drawn in the first draw will be subtracted from the counts in the H-W-C. The last draw will be retrieved and will be
	 * added to the H-W-C. The overdue will be reset, if the last drawn number was an overdue number 
	 * @param 	integer	$id			Lottery ID
	 * @param 	array	$lotto		Lottery Profile Objects
	 * @return 	none
	 */
	public function recalc_hwc($id, $lotto)
	{
	 // Retrieve the lottery table name for the database
	 $tbl = $this->lotteries_m->lotto_table_convert($lotto->lottery_name);
	 $blnduplicate = ($lotto->duplicate_extra_ball ? TRUE : FALSE);
	 $drawn = $lotto->balls_drawn;						// Get the number of balls drawn for this lottory, Pick 5, Pick 6, Pick 7, etc.
	 $max_ball = $lotto->maximum_ball;					// Get the highest ball drawn for this lottery, e.g. 49 in Lottery 649, 50 in Lottomax
	 // Check to see if the actual table exists in the db?
	 if (!$this->lotteries_m->lotto_table_exists($tbl))
	 {
		 $this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. '.$tbl.' Does not exist. Create the Lottery Database now.');
		 redirect('admin/statistics');
	 }
	 $all = $this->lotteries_m->db_row_count($tbl); 										// Return the total number of draws for this lottery
	 $lotto->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl);					// Retrieve the last drawn numbers and draw date
	 $str_dupextra = "";																	// Always empty for all lotteries. 
	 $prev_str_dupextra = ""; // Empty String
	 $prev_draw = array();	// Initialize the previous draw array
	 $h_w_c = $this->statistics_m->h_w_c_exists($id);
	 
	 // Try sliding window optimization if existing data is present
	 $use_sliding_window = false;
	 if(!is_null($h_w_c) && !empty($h_w_c['hots']) && $h_w_c['draw_id'] > 0)	// Existing HWC with data?
	 {
		$new_range = $h_w_c['range'];
		$hots = $h_w_c['h_count'];
		$warms = $h_w_c['w_count'];
		$colds = $h_w_c['c_count'];
		$w_start = intval($hots+1);					// Warms
		$lotto->H = $hots;  						// Number of Hots Distributed e.g. 16 Hots
		$c_start = ($max_ball-intval($colds))+1; 	// Return the Cold value
		$lotto->W = $warms;  						// Number of Warms Distributed e.g 18 Colds
		$lotto->C = $colds; 						// Number of Colds Distributed e.g 16 Colds
		
		// Check if we can use sliding window (same settings, only one new draw)
		$can_slide = (
			$h_w_c['extra_included'] == $lotto->extra_included &&
			$h_w_c['extra_draws'] == $lotto->extra_draws &&
			$h_w_c['draw_id'] == ($lotto->last_drawn['id'] - 1)  // Exactly one draw behind
		);
		
		if ($can_slide) {
			// Save current values as "_last" before updating with sliding window
			$strhots_last = $h_w_c['hots'];
			$strwarms_last = $h_w_c['warms'];
			$strcolds_last = $h_w_c['colds'];
			$str_dupextra_last = isset($h_w_c['dupextra']) ? $h_w_c['dupextra'] : '';
			$draw_id_last = $h_w_c['draw_id'];
			
			$slide_result = $this->statistics_m->hwc_sliding_window($tbl, $id, $drawn, $h_w_c['extra_included'], $h_w_c['extra_draws'], $new_range, $w_start, $c_start, $blnduplicate);
			
			if ($slide_result['success']) {
				$strhots = $slide_result['hots'];
				$strwarms = $slide_result['warms'];
				$strcolds = $slide_result['colds'];
				$use_sliding_window = true;
				
				// Calculate overdue and dupextra
				$stroverdue = $this->statistics_m->overdue($strhots, $strwarms, $strcolds, $tbl, $drawn, $h_w_c['extra_included'], $h_w_c['extra_draws'], $new_range, '');
				if($blnduplicate && $h_w_c['extra_included']) {
					$str_dupextra = $this->statistics_m->hwc_duple_extra($tbl, $h_w_c['extra_included'], $h_w_c['extra_draws'], $new_range, '');
				} else {
					$str_dupextra = '';
				}
				
				// Save with _last values preserved
				$hwc = array(
					'range'				=> $new_range,
					'hots'				=> $strhots,
					'warms'				=> $strwarms,
					'colds'				=> $strcolds,
					'hots_last'			=> $strhots_last,
					'warms_last'		=> $strwarms_last,
					'colds_last'		=> $strcolds_last,
					'dupextra'			=> $str_dupextra,
					'dupextra_last'		=> $str_dupextra_last,
					'overdue'			=> $stroverdue,
					'draw_id'			=> $lotto->last_drawn['id'],
					'draw_id_last'		=> $draw_id_last,
					'lottery_id'		=> $id,
					'extra_included'	=> $h_w_c['extra_included'],
					'extra_draws'		=> $h_w_c['extra_draws'],
					'w'					=> $w_start,
					'c'					=> $c_start,
					'h_count'			=> $lotto->H,
					'w_count'			=> $lotto->W,
					'c_count'			=> $lotto->C
				);
				$this->statistics_m->hwc_data_save($hwc, TRUE);
				
				// Calculate H-W-C win statistics
				$prediction_pool = isset($h_w_c['prediction_pool']) ? $h_w_c['prediction_pool'] : 18;
				$hot_count = count(explode(',', $strhots));
				$warm_count = count(explode(',', $strwarms));
				$cold_count = count(explode(',', $strcolds));
				$this->calculate_hwc_wins($id, $new_range, $prediction_pool, 
					$hot_count, $warm_count, $cold_count,
					$h_w_c['extra_included'], $h_w_c['extra_draws']);
					
				// Update history stats
				$hwc_history = $this->h_w_c_history($id, $tbl, $drawn, $h_w_c['extra_included'], $h_w_c['extra_draws'], $new_range, $w_start, $c_start, $blnduplicate);
				$hwc_history['position_last'] = $this->statistics_m->positions_before_last($tbl, $drawn, $lotto->extra_included, $blnduplicate, $strhots_last, $strwarms_last, $strcolds_last, $hwc_history['position']);
				
				// Save the updated position counts to database
				$hwc_h_data = array(
					'range'				=>	$new_range,
					'h_w_c_range'		=> 	$hwc_history['h_w_c_range'],
					'h_w_c_last_1'		=> 	$hwc_history['h_w_c_last_1'],
					'h_w_c_last_10'		=> 	$hwc_history['h_w_c_last_10'],
					'position'			=> 	$hwc_history['position'],
					'position_last'		=> 	$hwc_history['position_last'],
					'draw_id'			=> 	$lotto->last_drawn['id'],
					'draw_id_last'		=> 	$draw_id_last,
					'lottery_id'		=> 	$id,
					'extra_included'	=> 	$h_w_c['extra_included'],
					'extra_draws'		=> 	$h_w_c['extra_draws'],
				);
				$this->statistics_m->hwc_history_save($hwc_h_data, TRUE);
			}
		}
	 }
	 
	 // Full recalculation if sliding window wasn't used
	 if (!$use_sliding_window && !is_null($h_w_c))	// Existing HWC?
	 {
		$new_range = $h_w_c['range'];
		$hots = $h_w_c['h_count'];
		$warms = $h_w_c['w_count'];
		$colds = $h_w_c['c_count'];
		$w_start = intval($hots+1);					// Warms
		$lotto->H = $hots;  						// Number of Hots Distributed e.g. 16 Hots
		$c_start = ($max_ball-intval($colds))+1; 	// Return the Cold value
		$lotto->W = $warms;  						// Number of Warms Distributed e.g 18 Colds
		$lotto->C = $colds; 						// Number of Colds Distributed e.g 16 Colds
		$str_hwc = $this->statistics_m->h_w_c_calculate($tbl, $drawn, $h_w_c['extra_included'], $h_w_c['extra_draws'], $new_range, $w_start, $c_start, '', $blnduplicate);
		if($blnduplicate&&$h_w_c['extra_included']) $str_dupextra = $this->statistics_m->hwc_duple_extra($tbl, $h_w_c['extra_included'], $h_w_c['extra_draws'], $new_range, '');		
		$strhots = $this->statistics_m->hots($str_hwc);
		$strwarms = $this->statistics_m->warms($str_hwc);
		$strcolds = $this->statistics_m->colds($str_hwc);
 		$prev_draw = $this->statistics_m->hwc_DrawBeforeLast($tbl);// Get the previous draw data
			$prev_strhwc = $this->statistics_m->h_w_c_calculate($tbl, $drawn, $h_w_c['extra_included'], $h_w_c['extra_draws'], $new_range, $w_start, $c_start, $prev_draw['draw_date'], $blnduplicate);
			if($blnduplicate&&$h_w_c['extra_included']) $prev_str_dupextra = $this->statistics_m->hwc_duple_extra($tbl, $h_w_c['extra_included'], $h_w_c['extra_draws'], $new_range, $prev_draw['draw_date']);	
			$h_w_c['hots_last'] = $this->statistics_m->hots($prev_strhwc);
			$h_w_c['warms_last'] = $this->statistics_m->warms($prev_strhwc);
			$h_w_c['colds_last'] = $this->statistics_m->colds($prev_strhwc);
		$stroverdue = $this->statistics_m->overdue($strhots, $strwarms, $strcolds, $tbl, $drawn,  $h_w_c['extra_included'], $h_w_c['extra_draws'], $new_range, '');
		$hwc = array(
			'range'				=> $new_range,
			'hots'				=> $strhots,
			'warms'				=> $strwarms,
			'colds'				=> $strcolds,
			'hots_last'			=> $h_w_c['hots_last'],
			'warms_last'		=> $h_w_c['warms_last'],
			'colds_last'		=> $h_w_c['colds_last'],
			'dupextra'			=> $str_dupextra,
			'dupextra_last'		=> $prev_str_dupextra,
			'overdue'			=> $stroverdue,
			'draw_id'			=> $lotto->last_drawn['id'],
			'draw_id_last'		=> $prev_draw['id'],
			'lottery_id'		=> $id,
			'extra_included'	=> $h_w_c['extra_included'],
			'extra_draws'		=> $h_w_c['extra_draws'],
			'w'					=> $w_start,
			'c'					=> $c_start,
			'h_count'			=> $lotto->H,
			'w_count'			=> $lotto->W,
			'c_count'			=> $lotto->C
		);
		$this->statistics_m->hwc_data_save($hwc, TRUE);
		
		// Calculate H-W-C win statistics during recalculation
		$prediction_pool = isset($h_w_c['prediction_pool']) ? $h_w_c['prediction_pool'] : 18;
		// Calculate counts from the actual hot/warm/cold arrays, not from non-existent properties
		$hot_count = count(explode(',', $strhots));
		$warm_count = count(explode(',', $strwarms));
		$cold_count = count(explode(',', $strcolds));
		$this->calculate_hwc_wins($id, $new_range, $prediction_pool, 
			$hot_count, $warm_count, $cold_count,
			$h_w_c['extra_included'], $h_w_c['extra_draws']);
			
		$pos_last = $this->statistics_m->position_copylasts($id);
		// Recalculation is nesessary
		$hwc_history = $this->h_w_c_history($id, $tbl, $drawn, $h_w_c['extra_included'], $h_w_c['extra_draws'], $new_range, $w_start, $c_start, $blnduplicate);
	 	$hwc_history['position_last'] = $this->statistics_m->positions_before_last($tbl, $drawn, $this->data['lottery']->extra_included, $blnduplicate, $h_w_c['hots_last'], $h_w_c['warms_last'], $h_w_c['colds_last'], $hwc_history['position']);
	 }
	 else 
	 {
		 // Initialize extra_included and extra_draws from database saved settings instead of hardcoding to 0
		 // This preserves user preferences from the H-W-C page checkboxes
		 if(!isset($this->data['lottery']->extra_included)) {
			 $saved_extra_included = $this->statistics_m->extra_included($id, FALSE, 'lottery_followers');
			 $this->data['lottery']->extra_included = $saved_extra_included ? 1 : 0;
		 }
		 if(!isset($this->data['lottery']->extra_draws)) {
			 $saved_extra_draws = $this->statistics_m->extra_draws($id, FALSE, 'lottery_followers');
			 $this->data['lottery']->extra_draws = $saved_extra_draws ? 1 : 0;
		 }
		 
		 $new_range = ($all<100 ? $all : 100);
		 $heat = explode('-', $this->statistics_m->hwc_defaults[$max_ball]); 	// Break out the H-W-C into a new array
		 $w_start = intval($heat[0]+1);					// Warms
		 $this->data['lottery']->H = $heat[0];  						// Number of Hots Distributed e.g. 16 Hots
		 $c_start = ($max_ball-intval($heat[2]))+1; 	// Return the Cold value
		 $this->data['lottery']->W = $heat[1];  						// Number of Warms Distributed e.g 18 Colds
		 $this->data['lottery']->C = $heat[2]; 							// Num
		 $str_hwc = $this->statistics_m->h_w_c_calculate($tbl, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, $w_start, $c_start, '');
		 if($blnduplicate&&$this->data['lottery']->extra_included) $str_dupextra = $this->statistics_m->hwc_duple_extra($tbl, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, '');	
		 $strhots = $this->statistics_m->hots($str_hwc);
		 $strwarms = $this->statistics_m->warms($str_hwc);
		 $strcolds = $this->statistics_m->colds($str_hwc);
		 $stroverdue = $this->statistics_m->overdue($strhots, $strwarms, $strcolds, $tbl, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range);
		 
		 // Calculate H-W-C win statistics for new lottery setup
		 $hot_count = count(explode(',', $strhots));
		 $warm_count = count(explode(',', $strwarms));
		 $cold_count = count(explode(',', $strcolds));
		 $prediction_pool = 18; // Default prediction pool for new lotteries
		 $this->calculate_hwc_wins($id, $new_range, $prediction_pool, 
			$hot_count, $warm_count, $cold_count,
			$this->data['lottery']->extra_included, $this->data['lottery']->extra_draws);
		 
		 $prev_draw = $this->statistics_m->hwc_DrawBeforeLast($tbl);// Get the previous draw data
			$prev_strhwc = $this->statistics_m->h_w_c_calculate($tbl, $drawn, $h_w_c['extra_included'], $h_w_c['extra_draws'], $new_range, $w_start, $c_start, $prev_draw['draw_date'], $blnduplicate);
			if($blnduplicate&&$h_w_c['extra_included']) $prev_str_dupextra = $this->statistics_m->hwc_duple_extra($tbl, $h_w_c['extra_included'], $h_w_c['extra_draws'], $new_range, $prev_draw['draw_date']);	
			$h_w_c['hots_last'] = $this->statistics_m->hots($prev_strhwc);
			$h_w_c['warms_last'] = $this->statistics_m->warms($prev_strhwc);
			$h_w_c['colds_last'] = $this->statistics_m->colds($prev_strhwc);
		 $hwc = array(
					 'range'			=> $new_range,
					 'hots'				=> $strhots,
					 'warms'			=> $strwarms,
					 'colds'			=> $strcolds,
					 'hots_last'		=> $h_w_c['hots_last'],
					 'warms_last'		=> $h_w_c['warms_last'],
					 'colds_last'		=> $h_w_c['colds_last'],
					 'dupextra'			=> $str_dupextra,
					 'dupextra_last'	=> $prev_str_dupextra,
					 'hots_last'		=> $h_w_c['hots_last'],
					 'warms_last'		=> $h_w_c['warms_last'],
					 'colds_last'		=> $h_w_c['colds_last'],
					 'overdue'			=> $stroverdue,
					 'draw_id'			=> $this->data['lottery']->last_drawn['id'],
					 'draw_id_last'		=> $prev_draw['id'],
					 'lottery_id'		=> $id,
					 'extra_included'	=> $this->data['lottery']->extra_included,
					 'extra_draws'		=> $this->data['lottery']->extra_draws,
					 'w'				=> $w_start,
					 'c'				=> $c_start,
					 'h_count'			=> $this->data['lottery']->H,
					 'w_count'			=> $this->data['lottery']->W,
					 'c_count'			=> $this->data['lottery']->C	
				 );
		$this->statistics_m->hwc_data_save($hwc, FALSE);
		 // Recalculation is nesessary
		$pos_last = $this->statistics_m->position_copylasts($id);	
		$hwc_history = $this->h_w_c_history($id, $tbl, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, $w_start, $c_start, $blnduplicate);
	 	$hwc_history['position_last'] = $this->statistics_m->positions_before_last($tbl, $drawn, $this->data['lottery']->extra_included, $blnduplicate, $strhots_last, $strwarms_last, $strcolds_last, $hwc_history['position']);
	 }
	 if (!$hwc_history) // Problem with calculating H-W-C's over range
	 {
		$this->session->set_flashdata('message', 'There is a problem with the H (Hots) - W (Warms) - C (Colds) over the last '.$new_range.' Draws.');
	 	redirect('admin/statistics');
	 }
	 //if($hwc_history['position']!=$pos_last) $hwc_history['position_last']=$pos_last; 
	 $hwc_history['h_w_c_range'] = substr($hwc_history['h_w_c_range'], 0, -1);  				// Remove the last comma
	 $hwc_history['h_w_c_last_10'] = substr($hwc_history['h_w_c_last_10'], 0, -1);
	 $this->data['lottery']->last_hwc = $hwc_history['h_w_c_last_1'];
	$hwc_totals = explode(',',$hwc_history['h_w_c_range']); 		// Strip off the h-w-c to the right of the ','
	foreach($hwc_totals as $heat)
	{
		$n = strstr($heat, '=', TRUE); 						// Strip off the h-w-c to the left of the equal sign
		$c = substr(strchr($heat, "="), 1);				// Strip off the count to the right of the equal sign
		$this->data['lottery']->hwc[$n] = $c; 
	}
	$hwc_last10 = explode(',',$hwc_history['h_w_c_last_10']); 		// Strip off the h-w-c to the right of the ','
	foreach($hwc_last10 as $heat)
	{
		$n = strstr($heat, '=', TRUE); 						// Strip off the h-w-c to the left of the equal sign
		$c = substr(strrchr($heat, "="), 1);				// Strip off the count to the right of the equal sign
		$this->data['lottery']->last10[$n] = $c; 
	}
	$hwc_h_data = array(
						'range'				=>	$new_range,
						'h_w_c_range'		=> 	$hwc_history['h_w_c_range'],
						'h_w_c_last_1'		=> 	$lotto->last_hwc,
						'h_w_c_last_10'		=> 	$hwc_history['h_w_c_last_10'],
						'position'			=> 	$hwc_history['position'],
						'position_last'		=> 	$hwc_history['position_last'],
						'draw_id'			=> 	$lotto->last_drawn['id'],
						'draw_id_last'		=> 	$prev_draw['id'],
						'lottery_id'		=> 	$id,
						'extra_included'	=> 	(!is_null($h_w_c) ? $h_w_c['extra_included'] : $lotto->extra_included),
						'extra_draws'		=> 	(!is_null($h_w_c) ? $h_w_c['extra_draws'] : $lotto->extra_draws),
						);
		$this->statistics_m->hwc_history_save($hwc_h_data, TRUE); // Update existing lottery H W C Record
		unset($h_w_c); 		 // Remove this temporary holding place for h-w-c's
		unset($hwc_history); // Remove this temporary holding place for historic h-w-c's
		
		// Re-generate H-W-C predictions after recalc using the stored option settings
		$this->load->model('Predictions_m', 'predictions_m');
		$h_w_c_after = $this->statistics_m->h_w_c_exists($id);
		if(!is_null($h_w_c_after)) {
			$stored_option  = isset($h_w_c_after['hwc_option'])  ? (int)$h_w_c_after['hwc_option']  : 1;
			$stored_select  = isset($h_w_c_after['hwc_select'])  ? (int)$h_w_c_after['hwc_select']  : 1;
			$pool_size      = isset($h_w_c_after['prediction_pool']) ? (int)$h_w_c_after['prediction_pool'] : 18;
			$h_w_c_groups   = $this->predictions_m->get_h_w_c_range_with_rank($id);
			$group_patterns = array_keys($h_w_c_groups);
			if(!empty($group_patterns)) {
				if($stored_option === 2) {
					$idx = $stored_select - 1;
					$pattern = isset($group_patterns[$idx]) ? $group_patterns[$idx] : $group_patterns[0];
				} else {
					$pattern = $group_patterns[0]; // Top ranked
				}
				// Generate predictions for the NEXT draw from current H-W-C data
				$generated = $this->predictions_m->hwc_only($id, $pool_size, $pattern);
				// Generate "previous" predictions: what H-W-C suggested for the LAST draw,
				// using hots_last/warms_last/colds_last (H-W-C data excluding the last draw)
				$prev_gen = '';
				$prev_hots  = isset($h_w_c_after['hots_last'])  ? $h_w_c_after['hots_last']  : '';
				$prev_warms = isset($h_w_c_after['warms_last']) ? $h_w_c_after['warms_last'] : '';
				$prev_colds = isset($h_w_c_after['colds_last']) ? $h_w_c_after['colds_last'] : '';
				$h_count    = isset($h_w_c_after['h_count'])   ? (int)$h_w_c_after['h_count'] : 0;
				$w_count    = isset($h_w_c_after['w_count'])   ? (int)$h_w_c_after['w_count'] : 0;
				$c_count    = isset($h_w_c_after['c_count'])   ? (int)$h_w_c_after['c_count'] : 0;
				if(!empty($prev_hots) && !empty($prev_warms) && !empty($prev_colds)) {
					$prev_gen = $this->predictions_m->hwc_only_from_strings(
						$prev_hots, $prev_warms, $prev_colds,
						$h_count, $w_count, $c_count,
						$pool_size, $pattern
					);
					if($prev_gen === FALSE) $prev_gen = '';
				}
				if($generated) {
					// Preserve any snapshot taken at import/manual-entry time.
					// Only fall back to the computed $prev_gen when no snapshot exists yet
					// (e.g. fresh install before the first import with this feature active).
					$existing_prev = isset($h_w_c_after['prev_h_w_c_predictions']) ? $h_w_c_after['prev_h_w_c_predictions'] : '';
					$final_prev = !empty($existing_prev) ? null : $prev_gen; // null = don't overwrite
					$this->statistics_m->hwc_save_predictions($id, $stored_option, $stored_select, $generated, $final_prev);
				}
			}
		}
	}
	/**
	* ReCALCULATES the Lottery Followers for the next draw,
	* If they does not exist, Calculate the Followers for the first time with a default of 100 draws.
	* 
	* @param 	integer	$id			Lottery ID
	* @param 	array	$lotto		Lottery Profile Object
	* @return 	none
	*/
	public function recalc_followers($id, $lotto)
	{
		// Retrieve the lottery table name for the database
		$tbl = $this->lotteries_m->lotto_table_convert($lotto->lottery_name);
		$blnduplicate = ($lotto->duplicate_extra_ball ? TRUE : FALSE);
		
		// Check if recalculation is actually needed
		$recalc_status = $this->check_recalc_needed($id, $lotto, $tbl);
		if ($recalc_status['skip_recalc']) {
			$this->session->set_flashdata('message', $recalc_status['message']);
			redirect('admin/statistics');
			return;
		}
		

		
		$drawn = $lotto->balls_drawn;		// Get the number of balls drawn for this lottory, Pick 5, Pick 6, Pick 7, etc.
		$low = $lotto->minimum_ball;		// Regular Drawn Low ball e.g. ball 1
		$high = $lotto->maximum_ball;		// Regular Drawn High ball e.g. ball 49
		global $prizes;						// $prizes is global to statistics_m(odel) methods
		global $positions;					// ** New ** Prize Wins based on the position of the ball drawn and not the ball itself
		// Check to see if the actual table exists in the db?
		if (!$this->lotteries_m->lotto_table_exists($tbl))
		{
			$this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. '.$tbl.' Does not exist. Create the Lottery Database now.');
			redirect('admin/statistics');
		}
		$all = $this->lotteries_m->db_row_count($tbl); // Return the total number of draws for this lottery
		$lotto->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl);	// Retrieve the last drawn numbers and draw date
		$lottery_extra = $lotto->extra_ball; // the lottery definition of the extra ball when it is created (doesn't change!)
		// 1. Check for a record for the current lottery in the friends table
		$followers = $this->statistics_m->followers_exists($id);		// Existing follower row 
		$nonfollowers = $this->statistics_m->nonfollowers_exists($id);	// Non Follower existing row
		$outofrange = FALSE; // default is not out of range for the prize pool
		$max = $this->data['lottery']->maximum_ball;
		$mx_extra = ($blnduplicate ? $this->data['lottery']->maximum_extra_ball : $max);
		
		// Try sliding window optimization for followers
		$use_sliding_window = FALSE;
		if(!is_null($followers) && !empty($followers['lottery_followers']) && $followers['draw_id'] > 0)
		{
			// Check if we can use sliding window (same settings, only one new draw)
			$recalc_extra_included = isset($lotto->extra_included) ? $lotto->extra_included : $followers['extra_included'];
			$recalc_extra_draws = isset($lotto->extra_draws) ? $lotto->extra_draws : $followers['extra_draws'];
			
			$can_slide = (
				$followers['extra_included'] == $recalc_extra_included &&
				$followers['extra_draws'] == $recalc_extra_draws &&
				$followers['draw_id'] == ($lotto->last_drawn['id'] - 1)  // Exactly one draw behind
			);
			
			if ($can_slide) {
				$range = $followers['range'];
				$slide_result = $this->statistics_m->followers_sliding_window($tbl, $lotto->last_drawn, $drawn, $recalc_extra_included, $recalc_extra_draws, $range, $followers, $blnduplicate, $max, $mx_extra);
				
				if ($slide_result['success'] && !$slide_result['outofrange']) {
					$use_sliding_window = TRUE;
					$str_followers = $slide_result['followers'];
					$outofrange = $slide_result['outofrange'];
					
					// Still need to calculate prizes on the updated followers data
					$p_group = $this->statistics_m->prize_group_profile($id);
					if (empty($p_group)) {
						$p_group = array(
							'extra' => 1,
							'1_win' => 1, '1_win_extra' => 1,
							'2_win' => 1, '2_win_extra' => 1,
							'3_win' => 1, '3_win_extra' => 1,
							'4_win' => 1, '4_win_extra' => 1,
							'5_win' => 1, '5_win_extra' => 1,
							'6_win' => 1, '6_win_extra' => 1,
							'7_win' => 1, '7_win_extra' => 1,
							'8_win' => 1, '8_win_extra' => 1,
							'9_win' => 1, '9_win_extra' => 1
						);
					}
					
					$p_group = $this->statistics_m->prizes_only($p_group, $recalc_extra_included);
					$prizes = $this->statistics_m->create_prize_array($p_group, $low, $high);
					$positions = $this->statistics_m->create_positions_prize_array($p_group, $drawn, $recalc_extra_included);
					
					// Recalculate prizes with updated followers
					$this->statistics_m->followers_prizes($tbl, $lotto->last_drawn, $drawn, $recalc_extra_included, $recalc_extra_draws, $range, $max, '', $blnduplicate, $mx_extra);
					
					if (!$recalc_extra_included && is_array($prizes)) {
						foreach ($prizes as $ball => $categories) {
							if (is_array($categories)) {
								foreach ($categories as $cat => $count) {
									if (strpos($cat, '_extra') !== FALSE && $count > 0) {
										$base_cat = str_replace('_extra', '_win', $cat);
										if (array_key_exists($base_cat, $categories)) {
											$categories[$base_cat] += $count;
										}
										unset($categories[$cat]);
									}
								}
								foreach ($categories as $cat => $count) {
									if (!array_key_exists($cat, $p_group) && $cat !== '1_win') {
										unset($categories[$cat]);
									}
								}
								$prizes[$ball] = $categories;
								if (array_sum($categories) == 0) {
									unset($prizes[$ball]);
								}
							}
						}
					}
					
					$str_prizes = $this->statistics_m->followers_prize_string($prizes);
					$str_positions_prizes = $this->statistics_m->followers_positions_prize_string($positions);
					$str_nonfollowers = $this->statistics_m->nonfollowers_calculate($tbl, $lotto->last_drawn, $drawn, $recalc_extra_included, $recalc_extra_draws, $range, $max, '', $blnduplicate, $mx_extra);
					
					if($blnduplicate) {
						$str_prizes = $this->sanitize_duplicate_extra_wins($str_prizes, $range);
						$str_positions_prizes = $this->sanitize_duplicate_extra_wins($str_positions_prizes, $range);
					}
					
					$followers_data = array(
						'range'				=> $range,
						'lottery_followers'	=> $str_followers,
						'wins'				=> $str_prizes,
						'positions'			=> $str_positions_prizes,
						'draw_id'			=> $lotto->last_drawn['id'],
						'lottery_id'		=> $id,
						'extra_included'	=> $recalc_extra_included,
						'extra_draws'		=> $recalc_extra_draws
					);
					
					$this->statistics_m->follower_data_save($followers_data, TRUE);
					log_message('info', "recalc_followers: Used SLIDING WINDOW optimization for lottery_id={$id}");
					
					$nonfollowers_data = array(
						'range'					=> $range,
						'lottery_nonfollowers'	=> $str_nonfollowers,
						'draw_id'				=> $lotto->last_drawn['id'],
						'lottery_id'			=> $id
					);
					$this->statistics_m->nonfollower_data_save($nonfollowers_data, TRUE);
				}
			}
		}
		
		// Full recalculation if sliding window wasn't used
		if(!$use_sliding_window && !is_null($followers))
		{
			// 2. If exist, check the database for the latest draw range from 100 to all draws for the change in the range
			$p_group = $this->statistics_m->prize_group_profile($id);
			
			// Handle case where no prize profile exists
			if (empty($p_group)) {
				// Use default prize structure if no profile found
				$p_group = array(
					'extra' => 1,
					'1_win' => 1, '1_win_extra' => 1,
					'2_win' => 1, '2_win_extra' => 1,
					'3_win' => 1, '3_win_extra' => 1,
					'4_win' => 1, '4_win_extra' => 1,
					'5_win' => 1, '5_win_extra' => 1,
					'6_win' => 1, '6_win_extra' => 1,
					'7_win' => 1, '7_win_extra' => 1,
					'8_win' => 1, '8_win_extra' => 1,
					'9_win' => 1, '9_win_extra' => 1
				);
			}
			
			// Use current saved checkbox states from lottery object, not old database record
			$recalc_extra_included = isset($lotto->extra_included) ? $lotto->extra_included : $followers['extra_included'];
			$recalc_extra_draws = isset($lotto->extra_draws) ? $lotto->extra_draws : $followers['extra_draws'];
			
			// CRITICAL FIX: Use actual extra_included state for p_group filtering
			$p_group = $this->statistics_m->prizes_only($p_group, $recalc_extra_included);
 			$prizes = $this->statistics_m->create_prize_array($p_group, $low, $high);
			// For duplicate extra ball lotteries, include all main positions but handle extra separately
			
			// Detect and log parameter changes that trigger full recalculation
			$old_extra_included = $followers['extra_included'];
			$old_extra_draws = $followers['extra_draws'];  
			$old_range = $followers['range'];
			
			// **CRITICAL FIX**: Use current range from URL/parameters, NOT old database range
			$current_range = $this->get_current_range($id, $tbl);
			$range = $current_range; // Force use of current range for parameter changes
			
			$include_extra_position = $recalc_extra_included; // Use current checkbox state
			$positions = $this->statistics_m->create_positions_prize_array($p_group, $drawn, $include_extra_position);
			
			$str_followers = $this->statistics_m->followers_calculate($tbl, $lotto->last_drawn, $drawn, $recalc_extra_included, $recalc_extra_draws, $range,'',$blnduplicate);
			
			$outofrange = $this->statistics_m->followers_prizes($tbl, $lotto->last_drawn, $drawn, $recalc_extra_included, $recalc_extra_draws, $range, $max, '', $blnduplicate, $mx_extra);
			
			// CRITICAL FIX: When extra_included=0, consolidate extra wins into base categories
			if (!$recalc_extra_included && is_array($prizes)) {
				$consolidated_count = 0;
				$filtered_count = 0;
				$preserved_count = 0;
				foreach ($prizes as $ball => $categories) {
					if (is_array($categories)) {
						// Count wins before processing
						$ball_wins_before = array_sum($categories);
						
						// First, consolidate *_extra categories into their base categories
						foreach ($categories as $cat => $count) {
							if (strpos($cat, '_extra') !== false && $count > 0) {
								$base_cat = str_replace('_extra', '_win', $cat);
								// If base category exists, add the extra wins to it
								if (array_key_exists($base_cat, $categories)) {
									$categories[$base_cat] += $count;
									$consolidated_count++;
								}
								// Remove the extra category after consolidation
								unset($categories[$cat]);
							}
						}
						
						// Then filter out non-base categories (7_win, 8_win, 9_win, etc.)
						foreach ($categories as $cat => $count) {
							// Keep 1_win and categories in p_group, filter out everything else
							if (!array_key_exists($cat, $p_group) && $cat !== '1_win') {
								if ($count > 0) {
									$filtered_count++;
								}
								unset($categories[$cat]);
							}
						}
						
						// Update the prizes array with consolidated results
						$prizes[$ball] = $categories;
						
						// Count wins after processing
						$ball_wins_after = array_sum($categories);
						if ($ball_wins_before > 0 || $ball_wins_after > 0) {
							$preserved_count++;
						}
						
						// Remove balls with no remaining wins
						if (array_sum($categories) == 0) {
							unset($prizes[$ball]);
						}
					}
				}
			}
			
			$str_prizes = (!$outofrange ? $this->statistics_m->followers_prize_string($prizes) : '');
			//$str_positions_prizes = '';
			$str_positions_prizes = (!$outofrange ? $this->statistics_m->followers_positions_prize_string($positions) : '');			// Let regular followers_prizes handle duplicate extra balls - no separate dupextra calculation needed
			$str_dupextra_wins = '';
			// Duplicate extra balls are handled by the regular followers_prizes method with $duple flag
			
			/** NEW included nonfollower calculations **/
			$str_nonfollowers = $this->statistics_m->nonfollowers_calculate($tbl, $lotto->last_drawn, $drawn, $recalc_extra_included, $recalc_extra_draws, $range, $max, '', $blnduplicate, $mx_extra);
			

			
			// For duplicate extra ball lotteries, sanitize inflated win counts
			if($blnduplicate) {
				$str_prizes = $this->sanitize_duplicate_extra_wins($str_prizes, $range);
				$str_positions_prizes = $this->sanitize_duplicate_extra_wins($str_positions_prizes, $range);
			}
			
			$followers = array(
				'range'				=> $range,
				'lottery_followers'	=> $str_followers,
				'wins'				=> $str_prizes,
				'positions'			=> $str_positions_prizes,
				'draw_id'			=> $lotto->last_drawn['id'],
				'lottery_id'		=> $id,
				'extra_included'	=> $recalc_extra_included,
				'extra_draws'		=> $recalc_extra_draws
			);
			
			$this->statistics_m->follower_data_save($followers, TRUE);
			log_message('info', "recalc_followers: Saved followers data to database for lottery_id={$id}");
			
			/** NEW included nonfollower Data Save **/
			$nonfollowers = array(
				'range'					=> $range,
				'lottery_nonfollowers'	=> $str_nonfollowers,
				'draw_id'				=> $lotto->last_drawn['id'],
				'lottery_id'			=> $id
			);
			$this->statistics_m->nonfollower_data_save($nonfollowers, TRUE);
			log_message('info', "recalc_followers: Saved nonfollowers data to database for lottery_id={$id}");
		}
		else // 3. If does not exist, calculate for the given draw range, return results and save to follower table
		{
			// range is set with either less than 100 rows (based on the exact number of draws) or calculate the number of followers using only 100 rows
			// 2. If exist, check the database for the latest draw range from 100 to all draws for the change in the range
			
			// Use current saved checkbox states from lottery object for new record calculation
			$recalc_extra_included = isset($lotto->extra_included) ? $lotto->extra_included : 0;
			$recalc_extra_draws = isset($lotto->extra_draws) ? $lotto->extra_draws : 0;
			
			$p_group = $this->statistics_m->prize_group_profile($id);
			
			// Handle case where no prize profile exists
			if (empty($p_group)) {
				// Use default prize structure if no profile found
				$p_group = array(
					'extra' => 1,
					'1_win' => 1, '1_win_extra' => 1,
					'2_win' => 1, '2_win_extra' => 1,
					'3_win' => 1, '3_win_extra' => 1,
					'4_win' => 1, '4_win_extra' => 1,
					'5_win' => 1, '5_win_extra' => 1,
					'6_win' => 1, '6_win_extra' => 1,
					'7_win' => 1, '7_win_extra' => 1,
					'8_win' => 1, '8_win_extra' => 1,
					'9_win' => 1, '9_win_extra' => 1
				);
			}
			
			// CRITICAL FIX: Use actual extra_included state for p_group filtering
			$p_group = $this->statistics_m->prizes_only($p_group, $recalc_extra_included);
 			$prizes = $this->statistics_m->create_prize_array($p_group, $low, $high);
			$positions = $this->statistics_m->create_positions_prize_array($p_group, $drawn, $recalc_extra_included);
			
			// **CRITICAL FIX**: Use current range from URL/parameters for new records too  
			$current_range = $this->get_current_range($id, $tbl);
			$range = $current_range; // Use current range, not just default logic
			
			$outofrange = $this->statistics_m->followers_prizes($tbl, $lotto->last_drawn, $drawn, $recalc_extra_included, $recalc_extra_draws, $range, $max, '', $blnduplicate, $mx_extra);
			
			// CRITICAL FIX: When extra_included=0, consolidate extra wins into base categories
			if (!$recalc_extra_included && is_array($prizes)) {
				$consolidated_count = 0;
				$filtered_count = 0;
				$preserved_count = 0;
				foreach ($prizes as $ball => $categories) {
					if (is_array($categories)) {
						// Count wins before processing
						$ball_wins_before = array_sum($categories);
						
						// First, consolidate *_extra categories into their base categories
						foreach ($categories as $cat => $count) {
							if (strpos($cat, '_extra') !== false && $count > 0) {
								$base_cat = str_replace('_extra', '_win', $cat);
								// If base category exists, add the extra wins to it
								if (array_key_exists($base_cat, $categories)) {
									$categories[$base_cat] += $count;
									$consolidated_count++;
								}
								// Remove the extra category after consolidation
								unset($categories[$cat]);
							}
						}
						
						// Then filter out non-base categories (7_win, 8_win, 9_win, etc.)
						foreach ($categories as $cat => $count) {
							// Keep 1_win and categories in p_group, filter out everything else
							if (!array_key_exists($cat, $p_group) && $cat !== '1_win') {
								if ($count > 0) {
									log_message('info', "ReCalc new: Ball $ball: Filtering out non-base category $cat=$count");
									$filtered_count++;
								}
								unset($categories[$cat]);
							}
						}
						
						// Update the prizes array with consolidated results
						$prizes[$ball] = $categories;
						
						// Count wins after processing
						$ball_wins_after = array_sum($categories);
						if ($ball_wins_before > 0 || $ball_wins_after > 0) {
							$preserved_count++;
						}
						
						// Remove balls with no remaining wins
						if (array_sum($categories) == 0) {
							unset($prizes[$ball]);
						}
					}
				}
			}
			

			
			$str_prizes = (!$outofrange ? $this->statistics_m->followers_prize_string($prizes) : '');
			//$str_positions_prizes = '';
			$str_positions_prizes = (!$outofrange ? $this->statistics_m->followers_positions_prize_string($positions) : '');
			$str_followers = $this->statistics_m->followers_calculate($tbl, $lotto->last_drawn, $drawn, $recalc_extra_included, $recalc_extra_draws, $range,'',$blnduplicate);
			
			// Calculate dupextra_wins for independent extra ball lotteries during recalc (new record)
			$str_dupextra_wins = '';
			if($blnduplicate && !$outofrange) {
				$str_dupextra_wins = $this->statistics_m->calculate_dupextra_wins($tbl, $lotto->last_drawn, $drawn, $recalc_extra_included, $recalc_extra_draws, $range, $mx_extra, '');
			}
			
			// For duplicate extra ball lotteries, sanitize inflated win counts (new record path)
			if($blnduplicate) {
				$str_prizes = $this->sanitize_duplicate_extra_wins($str_prizes, $range);
				$str_positions_prizes = $this->sanitize_duplicate_extra_wins($str_positions_prizes, $range);
			}
			
			$followers = array(
				'range'				=> $range,
				'lottery_followers'	=> $str_followers,
				'wins'				=> $str_prizes,
				'positions'			=> $str_positions_prizes,
				'draw_id'			=> $lotto->last_drawn['id'],
				'lottery_id'		=> $id,
				'extra_included'	=> $recalc_extra_included,
				'extra_draws'		=> $recalc_extra_draws
			);
			
			// Add dupextra_wins field only for independent extra ball lotteries during recalc (new record)
			if($blnduplicate) {
				$followers['dupextra_wins'] = $str_dupextra_wins;
			}
			
			$this->statistics_m->follower_data_save($followers, FALSE);
			$str_nonfollowers = $this->statistics_m->nonfollowers_calculate($tbl, $lotto->last_drawn, $drawn, $recalc_extra_included, $recalc_extra_draws, $range, $max, '', $blnduplicate, $mx_extra);
			$nonfollowers = array(
			'range'					=> $range,
			'lottery_nonfollowers'	=> $str_nonfollowers,
			'draw_id'				=> $lotto->last_drawn['id'],
			'lottery_id'			=> $id
			);
			$this->statistics_m->nonfollower_data_save($nonfollowers, FALSE);
		}
		unset($prizes);			// Remove the $prize array - Free up memory 
	}

	/**
	* ReCALCULATES the Lottery Friends for the next draw,
	* If it does not exist, Calculate Friends with a default of 100 draws.
	* 
	* @param 	integer	$id			Lottery ID
	* @param 	array	$lotto		Lottery Profile Object
	* @return 	none
	*/
	public function recalc_friends($id, $lotto)
	{
		global $relatives;		// global totals of no friends, 1 way friend, 2 way friends
		global $nonrelatives;	// global non friend occurences
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($lotto->lottery_name);
		$blnduplicate = ($lotto->duplicate_extra_ball ? TRUE : FALSE);
		$drawn = $lotto->balls_drawn;		// Get the number of balls drawn for this lottory, Pick 5, Pick 6, Pick 7, etc.
		$max_ball = $lotto->maximum_ball;	// Get the highest ball drawn for this lottery, e.g. 49 in Lottery 649, 50 in Lottomax
		// Check to see if the actual table exists in the db?
		if (!$this->lotteries_m->lotto_table_exists($tbl_name))
		{
			$this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. '.$tbl_name.' Does not exist. Create the Lottery Database now.');
			redirect('admin/statistics');
		}
		$all = $this->lotteries_m->db_row_count($tbl_name); // Return the total number of draws for this lottery
		$lotto->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl_name);	// Retrieve the last drawn numbers and draw date
		$friends = $this->statistics_m->friends_exists($id);
		$nonfriends = $this->statistics_m->nonfriends_exists($id);
		
		// Try sliding window optimization for friends
		$use_sliding_window = FALSE;
		
		if(!is_null($friends) && !is_null($nonfriends) && !empty($friends['lottery_friends']) && $friends['draw_id'] > 0)
		{
			// Check if we can use sliding window (same settings, only one new draw)
			$can_slide = (
				$friends['draw_id'] == ($lotto->last_drawn['id'] - 1)  // Exactly one draw behind
				&& !empty($friends['friendship_matrix']) // Has cached matrix
			);
			
			if ($can_slide) {
				$range = $friends['range'];
				$extra_included = isset($friends['extra_included']) ? $friends['extra_included'] : 0;
				$extra_draws = isset($friends['extra_draws']) ? $friends['extra_draws'] : 0;
				
				$slide_result = $this->statistics_m->friends_sliding_window($tbl_name, $lotto->last_drawn, $drawn, $max_ball, $extra_included, $extra_draws, $range, $friends, $blnduplicate);
				
				if ($slide_result['success']) {
					$use_sliding_window = TRUE;
					
					// Need to recalculate hits/wins on the updated friends data
					$relatives = $this->statistics_m->create_friend_array();
					$nonrelatives = $this->statistics_m->create_nonfriend_array();
					
					$str_friends = $slide_result['lottery_friends'];
					
					// Calculate nonfriends normally
					$str_friends_temp = $this->statistics_m->friends_calculate($tbl_name, $drawn, $max_ball, $extra_included, $extra_draws, $range, '', $blnduplicate);
					$associate = explode('+', $str_friends_temp);
					$str_nonfriends = isset($associate[1]) ? $associate[1] : '';
					
					$this->statistics_m->friends_hits($str_friends, $str_nonfriends, $tbl_name, $drawn, $max_ball, $extra_included, $extra_draws, $range, '', $blnduplicate);
					$fr_stats = $this->statistics_m->combine_friends_string($relatives, $str_friends, $max_ball);
					$nfr_stats = $this->statistics_m->combine_nonfriends_string($nonrelatives);
					
					$friends_data = array(
						'range'				=> $range,
						'lottery_friends'	=> $str_friends,
						'friendship_matrix'	=> $slide_result['matrix'], // Save updated matrix
						'wins'				=> $fr_stats,
						'extra_included'	=> $extra_included,
						'extra_draws'		=> $extra_draws,
						'draw_id'			=> $lotto->last_drawn['id'],
						'lottery_id'		=> $id
					);
					$this->statistics_m->friends_data_save($friends_data, TRUE);
					log_message('info', "recalc_friends: Used SLIDING WINDOW optimization for lottery_id={$id}");
					
					$nonfriends_data = array(
						'range'					=> $range,
						'lottery_nonfriends'	=> $str_nonfriends,
						'draw_id'				=> $lotto->last_drawn['id'],
						'lottery_id'			=> $id
					);
					$this->statistics_m->nonfriends_data_save($nonfriends_data, TRUE);
				}
			}
		}
		
		// Full recalculation if sliding window wasn't used
		if(!$use_sliding_window && !is_null($friends) && !is_null($nonfriends))
		{
			$range = $friends['range'];
			$relatives = $this->statistics_m->create_friend_array();
			$nonrelatives = $this->statistics_m->create_nonfriend_array();
			$str_friends = $this->statistics_m->friends_calculate($tbl_name, $drawn, $max_ball, $friends['extra_included'], $friends['extra_draws'], $range, '', $blnduplicate);
			$associate = explode('+', $str_friends); // The '+' is the separator
			$str_friends = $associate[0];			 // separated the friends
			$str_nonfriends = $associate[1]; 		 // from the non friends
			$this->statistics_m->friends_hits($str_friends, $str_nonfriends, $tbl_name, $drawn, $max_ball, $friends['extra_included'], $friends['extra_draws'], $range, '', $blnduplicate);
			$fr_stats = $this->statistics_m->combine_friends_string($relatives, $str_friends, $max_ball);
			$nfr_stats = $this->statistics_m->combine_nonfriends_string($nonrelatives);
			
			// Build and cache the co-occurrence matrix for future sliding window updates
			$matrix = $this->statistics_m->build_friends_matrix($tbl_name, $drawn, $max_ball, $friends['extra_included'], $friends['extra_draws'], $range, $blnduplicate);
			
			$friends_data = array(
				'range'				=> $range,
				'lottery_friends'	=> $str_friends,
				'friendship_matrix'	=> json_encode($matrix), // Cache matrix
				'wins'				=> $fr_stats,
				'extra_included'	=> $friends['extra_included'],
				'extra_draws'		=> $friends['extra_draws'],
				'draw_id'			=> $lotto->last_drawn['id'],
				'lottery_id'		=> $id
			);
			$this->statistics_m->friends_data_save($friends_data, TRUE);
			$nonfriends = array(
				'range'					=> $range,
				'lottery_nonfriends'	=> $str_nonfriends,
				'draw_id'				=> $this->data['lottery']->last_drawn['id'],
				'lottery_id'			=> $id
			);
			$this->statistics_m->nonfriends_data_save($nonfriends, TRUE);
		}
		else 
		{
			$new_range = ($all<100 ? $all : 100);
			$relatives = $this->statistics_m->create_friend_array();
			$nonrelatives = $this->statistics_m->create_nonfriend_array();
			$str_friends = $this->statistics_m->friends_calculate($tbl_name, $drawn, $max_ball, 0, 0, $new_range, '', $blnduplicate);
			$associate = explode('+', $str_friends); // The '+' is the separator
			$str_friends = $associate[0];			 // separated the friends
			$str_nonfriends = $associate[1]; 		 // from the non friends
			$this->statistics_m->friends_hits($str_friends, $str_nonfriends, $tbl_name, $drawn, $max_ball, 0, 0, $new_range, '', $blnduplicate);
			$fr_stats = $this->statistics_m->combine_friends_string($relatives, $str_friends, $max_ball);
			$nfr_stats = $this->statistics_m->combine_nonfriends_string($nonrelatives);
			
			// Build and cache matrix for first run
			$matrix = $this->statistics_m->build_friends_matrix($tbl_name, $drawn, $max_ball, 0, 0, $new_range, $blnduplicate);
			
			$friends = array(
				'range'				=> $new_range,
				'lottery_friends'	=> $str_friends,
				'friendship_matrix'	=> json_encode($matrix),
				'wins'				=> $fr_stats,
				'extra_included'	=> 0,
				'extra_draws'		=> 0,
				'draw_id'			=> $lotto->last_drawn['id'],
				'lottery_id'		=> $id
			);
			$this->statistics_m->friends_data_save($friends, FALSE);
			$nonfriends = array(
				'range'					=> $new_range,
				'lottery_nonfriends'	=> $str_nonfriends,
				'draw_id'				=> $this->data['lottery']->last_drawn['id'],
				'lottery_id'			=> $id
			);
			$this->statistics_m->nonfriends_data_save($nonfriends, TRUE);
		}
	}

	/**
	 * Calculate and save follower wins for independent extra ball lotteries
	 * This method implements the enhanced prize calculation system for duplicate_extra_ball = 1
	 * 
	 * @param int $id Lottery ID
	 * @param int $range Draw range (100, 200, 300, etc.)
	 */
	public function calculate_follower_wins($id, $range = 100)
	{
		// Validate lottery
		$lottery = $this->lotteries_m->get($id);
		if (!$lottery) {
			$this->session->set_flashdata('message', 'Lottery not found.');
			redirect('admin/statistics');
			return;
		}

		// Check if this is an independent extra ball lottery
		if (!$lottery->duplicate_extra_ball) {
			$this->session->set_flashdata('message', 'This feature is only available for independent extra ball lotteries (duplicate_extra_ball = 1).');
			redirect('admin/statistics');
			return;
		}

		$table_name = $this->lotteries_m->lotto_table_convert($lottery->lottery_name);
		
		// Check if table exists
		if (!$this->lotteries_m->lotto_table_exists($table_name)) {
			$this->session->set_flashdata('message', 'Lottery database table does not exist.');
			redirect('admin/statistics');
			return;
		}

		try {
			// Calculate follower wins using the enhanced algorithm
			$result = $this->statistics_m->calculate_independent_extra_follower_wins(
				$table_name, 
				$id, 
				$range, 
				true, // extra_included
				false // extra_draws
			);

			// Check for errors (insufficient draws)
			if (isset($result['error'])) {
				$this->session->set_flashdata('message', $result['error']);
				redirect('admin/statistics');
				return;
			}

			// Save to database
			$this->save_follower_wins_data($id, $range, $result);

			$this->session->set_flashdata('message', "Follower wins calculated successfully for {$lottery->lottery_name} using {$range} draw range. Enhanced independent extra ball algorithm applied.");
			
		} catch (Exception $e) {
			$this->session->set_flashdata('message', 'Error calculating follower wins: ' . $e->getMessage());
		}

		redirect('admin/statistics');
	}

	/**
	 * Save follower wins data to existing lottery_followers table (REMOVED redundant lottery_follower_wins table)
	 */
	private function save_follower_wins_data($lottery_id, $range, $result)
	{
		// Data is already saved by the model methods - this method is now simplified
		// The enhanced calculation methods in Statistics_m handle the database operations directly
		
		// Just return success since the model handles all database operations
		return true;
	}

	/**
	 * View follower wins for a specific lottery using existing lottery_followers table
	 * This creates the display interface for the calculated follower wins
	 * 
	 * @param int $id Lottery ID
	 * @param int $range Draw range to display
	 */
	public function view_follower_wins($id, $range = 100)
	{
		$this->data['lottery'] = $this->lotteries_m->get($id);
		
		if (!$this->data['lottery']) {
			$this->session->set_flashdata('message', 'Lottery not found.');
			redirect('admin/statistics');
			return;
		}

		// Get follower wins data from existing lottery_followers table
		$wins_data = $this->db->select('*')
							  ->where('lottery_id', $id)
							  ->where('range', $range)
							  ->get('lottery_followers')
							  ->row();

		if (!$wins_data) {
			$this->session->set_flashdata('message', 'No follower wins data found for this lottery and range. Please calculate first.');
			redirect('admin/statistics');
			return;
		}

		// Parse the wins string based on lottery type
		if ($this->data['lottery']->duplicate_extra_ball == 1) {
			// Independent extra ball lottery - parse with # separator
			$this->data['parsed_wins'] = $this->parse_wins_string_with_separator($wins_data->wins);
			
			// Also parse dupextra_wins for independent extra ball lotteries
			if (!empty($wins_data->dupextra_wins)) {
				$this->data['parsed_dupextra_wins'] = $this->parse_dupextra_wins_string($wins_data->dupextra_wins);
			}
		} else {
			// Regular lottery - parse standard format
			$this->data['parsed_wins'] = $this->parse_wins_string($wins_data->wins);
		}
		
		$this->data['wins_data'] = $wins_data;
		$this->data['range'] = $range;
		$this->data['subview'] = 'admin/dashboard/statistics/view_follower_wins';
		$this->load->view('admin/_layout_main', $this->data);
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
	 * Parse wins string with # separator for independent extra ball lotteries
	 */
	private function parse_wins_string_with_separator($wins_string)
	{
		$parsed = array();
		
		if (empty($wins_string)) {
			return $parsed;
		}
		
		// Split by # to separate main and extra balls
		$parts = explode('#', $wins_string);
		$main_string = isset($parts[0]) ? $parts[0] : '';
		$extra_string = isset($parts[1]) ? $parts[1] : '';
		
		// Parse main balls
		if (!empty($main_string)) {
			$main_entries = explode('<', $main_string);
			foreach ($main_entries as $entry) {
				if (empty($entry)) continue;
				$entry_parts = explode('>', $entry);
				if (count($entry_parts) == 2) {
					$number = $entry_parts[0];
					$wins = explode(',', $entry_parts[1]);
					$parsed['main_' . $number] = $wins;
				}
			}
		}
		
		// Parse extra balls
		if (!empty($extra_string)) {
			$extra_entries = explode('<', $extra_string);
			foreach ($extra_entries as $entry) {
				if (empty($entry)) continue;
				$entry_parts = explode('>', $entry);
				if (count($entry_parts) == 2) {
					$number = $entry_parts[0];
					$wins = explode(',', $entry_parts[1]);
					$parsed['extra_' . $number] = $wins;
				}
			}
		}
		
		return $parsed;
	}

	/**
	 * Calculate point rankings based on the point system
	 */
	private function calculate_point_rankings($parsed_wins)
	{
		$rankings = array();
		
		foreach ($parsed_wins as $number => $wins) {
			$total_points = 0;
			
			// Apply point system (scaled for independent extra ball lotteries)
			// Index 0 = no winners (0 points)
			// Index 1 = 1 win (2 points)
			// Index 2 = 1 win + extra (3 points)
			// Index 3 = 2 wins (2 points - special case for pick 5)
			// Index 4 = 2 wins + extra (1 point)
			// etc.
			
			$point_values = array(0, 2, 3, 2, 1, 3, 1, 4, 1, 5, 1); // Based on the point system
			
			for ($i = 0; $i < count($wins) && $i < count($point_values); $i++) {
				$total_points += $wins[$i] * $point_values[$i];
			}
			
			$rankings[$number] = $total_points;
		}
		
		// Sort by points (descending)
		arsort($rankings);
		
		return $rankings;
	}

	/**
	 * Helper method to calculate enhanced follower wins during recalc for independent extra ball lotteries
	 * This integrates the enhanced calculation into the existing recalc process
	 */
	private function calculate_enhanced_follower_wins($id, $range, $lotto, $table_name)
	{
		try {
			// Use the enhanced algorithm for independent extra ball lotteries
			$result = $this->statistics_m->calculate_independent_extra_follower_wins(
				$table_name, 
				$id, 
				$range, 
				true, // extra_included
				false // extra_draws
			);

			// Check for errors (insufficient draws)
			if (isset($result['error'])) {
				// Log error but don't break the recalc process
				log_message('error', "Enhanced follower wins calculation failed for lottery {$id}: " . $result['error']);
				return;
			}

			// Save enhanced wins data to database
			$this->save_follower_wins_data($id, $range, $result);
			
		} catch (Exception $e) {
			// Log error but don't break the recalc process
			log_message('error', "Enhanced follower wins calculation exception for lottery {$id}: " . $e->getMessage());
		}
	}
	
	/**
	 * Parse dupextra_wins string into displayable format
	 * Format: "prize1,prize2,prize3>prize1,prize2,prize3>..." where each section represents prizes for an extra ball
	 */
	private function parse_dupextra_wins_string($dupextra_wins_string)
	{
		$parsed = array();
		
		if (empty($dupextra_wins_string)) {
			return $parsed;
		}
		
		// Split by '>' to get prizes for each extra ball
		$extra_ball_prizes = explode('>', $dupextra_wins_string);
		
		// Process each extra ball's prizes - since calculate_dupextra_wins now only returns data for drawn extra ball
		$extra_num = 1;
		if(isset($extra_ball_prizes[$extra_num - 1]) && !empty($extra_ball_prizes[$extra_num - 1])) {
			$prizes = explode(',', $extra_ball_prizes[$extra_num - 1]);
			
			// Map prizes to categories - only include non-NULL categories
			$prize_categories = array();
			$all_categories = array('extra', '1_win', '1_win_extra', '2_win', '2_win_extra', '3_win', '3_win_extra', '4_win', '4_win_extra', '5_win', '5_win_extra', '6_win', '6_win_extra', '7_win', '7_win_extra', '8_win', '8_win_extra', '9_win', '9_win_extra');
			
			// Get prize profile to check which categories are not NULL
			$prize_profile = $this->statistics_m->prize_group_profile($this->data['lottery']->lottery_id);
			
			foreach($all_categories as $category) {
				if(isset($prize_profile[$category]) && $prize_profile[$category] !== null) {
					$prize_categories[] = $category;
				}
			}
			
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
	 * Calculate H-W-C win statistics - helper method to integrate with recalculation triggers
	 * 
	 * @param int $lottery_id The lottery ID
	 * @param int $range The analysis range
	 * @param int $prediction_pool The prediction pool size
	 * @param int $hots Number of hot numbers
	 * @param int $warms Number of warm numbers
	 * @param int $colds Number of cold numbers
	 * @param boolean $extra_included Whether extra ball is included
	 * @param boolean $extra_draws Whether extra draws are included
	 * @return boolean Success status
	 */
	private function calculate_hwc_wins($lottery_id, $range, $prediction_pool, $hots, $warms, $colds, $extra_included = false, $extra_draws = false)
	{
		try {
			// Call the comprehensive H-W-C win analysis method
			$wins_string = $this->statistics_m->calculate_hwc_win_statistics(
				$lottery_id, 
				$range, 
				$prediction_pool, 
				$hots, 
				$warms, 
				$colds, 
				$extra_included, 
				$extra_draws
			);
			
			if ($wins_string !== FALSE) {
				// Success - wins string has been calculated and saved to database
				return TRUE;
			} else {
				// Log error or handle failure case
				log_message('error', "H-W-C win analysis FAILED for lottery_id: $lottery_id, range: $range");
				return FALSE;
			}
		} catch (Exception $e) {
			// Handle any exceptions during calculation
			log_message('error', "H-W-C win analysis error: " . $e->getMessage());
			return FALSE;
		}
	}

	/**
	 * Sanitize duplicate extra ball win counts to prevent inflated values
	 * @param string $win_string The win string to sanitize
	 * @param int $range The draw range (typically 100)
	 * @return string Sanitized win string
	 */
	private function sanitize_duplicate_extra_wins($win_string, $range)
	{
		if(empty($win_string)) return $win_string;
		
		// Parse the win string (format: "0,1,2,3,4,5>1,0,0,2,1,0>..." where each section is a ball's wins)
		$ball_sections = explode('>', $win_string);
		$sanitized_sections = array();
		
		foreach($ball_sections as $section) {
			if(empty($section)) {
				$sanitized_sections[] = $section;
				continue;
			}
			
			$counts = explode(',', $section);
			$sanitized_counts = array();
			
			foreach($counts as $count) {
				$int_count = intval($count);
				// Cap any count that exceeds the range (prevents 269 winners from 100 draws)
				$sanitized_count = min($int_count, $range);
				$sanitized_counts[] = $sanitized_count;
			}
			
			$sanitized_sections[] = implode(',', $sanitized_counts);
		}
		
		$result = implode('>', $sanitized_sections);
		return $result;
	}
	
	/**
	 * Check if H-W-C recalculation is needed or if a warning should be displayed
	 * 
	 * @param	integer	$lottery_id		Lottery ID
	 * @param	object	$lotto			Lottery object  
	 * @param	string	$tbl			Lottery table name
	 * @return	array	Array with skip_recalc flag and message
	 */
	private function check_hwc_recalc_needed($lottery_id, $lotto, $tbl)
	{
		// Get existing H-W-C data
		$existing_hwc = $this->statistics_m->h_w_c_exists($lottery_id);
		
		// If no existing data, recalc is needed (first time)
		if (is_null($existing_hwc)) {
			return array('skip_recalc' => false, 'message' => '');
		}
		
		// Get the latest draw from the table
		$latest_draw = $this->lotteries_m->last_draw_db($tbl);
		if (!$latest_draw) {
			return array('skip_recalc' => false, 'message' => '');
		}
		
		// Check if H-W-C data was reset (empty hots/warms/colds or draw_id=0)
		if (empty($existing_hwc['hots']) || empty($existing_hwc['warms']) || empty($existing_hwc['colds']) || $existing_hwc['draw_id'] == 0) {
			return array('skip_recalc' => false, 'message' => '');
		}
		
		// Check if existing calculation is up to date
		$existing_draw_id = $existing_hwc['draw_id'];
		$latest_draw_id = $latest_draw->id;
		
		// If there's a new draw since last calculation, recalc is needed
		if ($existing_draw_id != $latest_draw_id) {
			return array('skip_recalc' => false, 'message' => '');
		}
		
		// If the existing calculation is already for the latest draw AND has data, warn user
		if ($existing_draw_id == $latest_draw_id && 
		    !empty($existing_hwc['hots']) && 
		    !empty($existing_hwc['warms']) && 
		    !empty($existing_hwc['colds'])) {
			
			$draw_date = date('M j, Y', strtotime($latest_draw->draw_date));
			$message = 'ReCalc option is not Required. ReCalculation has been completed up to ' . $draw_date;
			
			return array('skip_recalc' => true, 'message' => $message);
		}
		
		// All other cases, allow recalc
		return array('skip_recalc' => false, 'message' => '');
	}
	
	/**
	 * Check if recalculation is needed or if a warning should be displayed
	 * 
	 * @param	integer	$lottery_id		Lottery ID
	 * @param	object	$lotto			Lottery object  
	 * @param	string	$tbl			Lottery table name
	 * @return	array	Array with skip_recalc flag and message
	 */
	private function check_recalc_needed($lottery_id, $lotto, $tbl)
	{
		// Get existing follower data
		$existing_followers = $this->statistics_m->followers_exists($lottery_id);
		
		// If no existing data, recalc is needed (first time)
		if (is_null($existing_followers)) {
			return array('skip_recalc' => false, 'message' => '');
		}
		
		// Get the latest draw from the table
		$latest_draw = $this->lotteries_m->last_draw_db($tbl);
		if (!$latest_draw) {
			return array('skip_recalc' => false, 'message' => '');
		}
		
		// Check if follower data was reset (empty wins/positions but record exists)
		if (empty($existing_followers['wins']) || empty($existing_followers['positions'])) {
			return array('skip_recalc' => false, 'message' => '');
		}
		
		// **CRITICAL**: Check if calculation parameters have changed - this forces FULL RECALC
		$current_extra_included = isset($lotto->extra_included) ? $lotto->extra_included : 0;
		$current_extra_draws = isset($lotto->extra_draws) ? $lotto->extra_draws : 0;
		$current_range = $this->get_current_range($lottery_id, $tbl); // Get range from URL or default
		
		$existing_extra_included = isset($existing_followers['extra_included']) ? $existing_followers['extra_included'] : 0;
		$existing_extra_draws = isset($existing_followers['extra_draws']) ? $existing_followers['extra_draws'] : 0;
		$existing_range = $existing_followers['range'];
		
		if ($current_extra_included != $existing_extra_included || 
		    $current_extra_draws != $existing_extra_draws ||
		    $current_range != $existing_range) {
			return array('skip_recalc' => false, 'message' => '');
		}
		
		// Check if existing calculation is up to date
		$existing_draw_id = $existing_followers['draw_id'];
		$latest_draw_id = $latest_draw->id;
		
		// If draw_id is 0, it means the data was reset and needs recalculation
		if ($existing_draw_id == 0 || empty($existing_followers['lottery_followers'])) {
			return array('skip_recalc' => false, 'message' => '');
		}
		
		// If there's a new draw since last calculation, recalc is needed
		if ($existing_draw_id != $latest_draw_id) {
			return array('skip_recalc' => false, 'message' => '');
		}
		
		// If the existing calculation is already for the latest draw AND parameters haven't changed, warn user
		if ($existing_draw_id == $latest_draw_id && 
		    !empty($existing_followers['wins']) && 
		    !empty($existing_followers['positions'])) {
			
			$draw_date = date('M j, Y', strtotime($latest_draw->draw_date));
			$message = 'ReCalc option is not Required. ReCalculation has been completed up to ' . $draw_date;
			
			return array('skip_recalc' => true, 'message' => $message);
		}
		
		// All other cases, allow recalc
		return array('skip_recalc' => false, 'message' => '');
	}
	
	/**
	 * Get the current range from URL parameters or use default
	 * 
	 * @param	integer	$lottery_id		Lottery ID
	 * @param	string	$tbl			Lottery table name  
	 * @return	integer	Current range value
	 */
	private function get_current_range($lottery_id, $tbl)
	{
		// URL structure: /admin/statistics/followers/{lottery_id}/{range}/{extra}/{draws}
		// So segment(5) should be the range
		$url_range = $this->uri->segment(5);
		
		if ($url_range && is_numeric($url_range) && $url_range >= 50 && $url_range <= 1000) {
			return intval($url_range);
		}
		
		// Fallback: look through all segments for numeric range value  
		$uri_segments = $this->uri->segment_array();
		foreach ($uri_segments as $segment) {
			if (is_numeric($segment) && $segment >= 50 && $segment <= 1000 && $segment != $lottery_id) {
				return intval($segment);
			}
		}
		
		// Default range logic
		$all = $this->lotteries_m->db_row_count($tbl);
		return ($all < 100 ? $all : 100);
	}
	
	/**
	 * Set extra_included value directly without toggling
	 * 
	 * @param	integer	$lottery_id		Lottery ID
	 * @param	integer	$value			Value to set (0 or 1)  
	 * @param	string	$table			Table name (lottery_followers or lottery_nonfollowers)
	 * @return	void
	 */
	private function set_extra_included_value($lottery_id, $value, $table)
	{
		$this->db->set('extra_included', intval($value));
		$this->db->where('lottery_id', $lottery_id);
		$this->db->update($table);
		
		log_message('info', "Set extra_included=$value for lottery_id=$lottery_id in table=$table");
	}
	
	/**
	 * Set extra_draws value directly without toggling
	 * 
	 * @param	integer	$lottery_id		Lottery ID
	 * @param	integer	$value			Value to set (0 or 1)
	 * @param	string	$table			Table name (lottery_followers or lottery_nonfollowers)  
	 * @return	void
	 */
	private function set_extra_draws_value($lottery_id, $value, $table)
	{
		$this->db->set('extra_draws', intval($value));
		$this->db->where('lottery_id', $lottery_id);
		$this->db->update($table);
		
		log_message('info', "Set extra_draws=$value for lottery_id=$lottery_id in table=$table");
	}

	// -------------------------------------------------------------------------

	/**
	 * H-W-C + Followers combined prediction page for the next draw.
	 * Allows saving H-W-C group + follower type/ball settings and displays
	 * the generated prediction numbers.
	 *
	 * @param  integer  $id  Lottery ID
	 * @return void
	 */
	public function hwc_followers($id)
	{
		$this->data['message'] = '';

		// Must have H-W-C calculated before this page is usable
		$hwc_check = $this->statistics_m->h_w_c_exists($id);
		if (is_null($hwc_check) || empty($hwc_check['hots']) || empty($hwc_check['warms']) || empty($hwc_check['colds']) || $hwc_check['draw_id'] == 0) {
			$this->session->set_flashdata('message', 'H-W-C must be calculated (ReCalc) before using the H-W-C + Followers prediction page.');
			redirect('admin/statistics');
			return;
		}

		// Must have followers calculated
		$followers_check = $this->statistics_m->followers_exists($id);
		if (is_null($followers_check) || empty($followers_check['lottery_followers'])) {
			$this->session->set_flashdata('message', 'Followers must be ReCalculated before using the H-W-C + Followers prediction page.');
			redirect('admin/statistics');
			return;
		}

		$this->data['lottery'] = $this->lotteries_m->get($id);
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);

		if (!$this->lotteries_m->lotto_table_exists($tbl_name)) {
			$this->session->set_flashdata('message', 'Internal error: lottery table ' . $tbl_name . ' does not exist.');
			redirect('admin/statistics');
		}

		// Load model dependencies on-demand
		$this->load->model('Predictions_m', 'predictions_m');
		$this->load->model('Lottery_statistics_m', 'lottery_statistics_m');
		$this->load->model('History_m', 'history_m');

		$drawn      = (int) $this->data['lottery']->balls_drawn;
		$pool_size  = isset($hwc_check['prediction_pool']) ? (int) $hwc_check['prediction_pool'] : 18;

		// Build H-W-C group dropdown (ranked list)
		$this->data['h_w_c_group'] = $this->predictions_m->get_h_w_c_range_with_rank($id);

		// Build ball and position points options using the full followers points chain
		$p_group = $this->statistics_m->prize_group_profile($id);
		$p_group = $this->statistics_m->prizes_only($p_group, $this->data['lottery']->extra_ball);
		$this->data['lottery']->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl_name);
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_prizegroup($this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_ball, $p_group);
		$extra_included_f  = isset($followers_check['extra_included']) ? (int) $followers_check['extra_included'] : 0;
		$follower_wins     = explode('>', $followers_check['wins']);
		$follow_poswins    = explode('>', $followers_check['positions']);
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_addwins($this->data['lottery']->last_drawn, $drawn, $extra_included_f, $p_group, $follower_wins, $follow_poswins);
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_addpoints($this->data['lottery']->last_drawn, $drawn, $extra_included_f, $this->data['lottery']->duplicate_extra_ball);

		$ball_points_raw = $this->predictions_m->get_sorted_ball_points($this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->duplicate_extra_ball);
		$ball_points_options = [];
		foreach ($ball_points_raw as $label) {
			if (strpos($label, '+') === 0) {
				$value = substr($label, 0, strpos($label, ' '));
			} else {
				$value = strtok($label, ' ');
			}
			$ball_points_options[$value] = $label;
		}
		$this->data['ball_points_options'] = $ball_points_options;

		$position_points_raw = $this->lottery_statistics_m->get_sorted_position_points($this->data['lottery']->last_drawn, $drawn);
		$position_points_options = [];
		foreach ($position_points_raw as $label) {
			if (strpos($label, '+') === 0) {
				$value = substr($label, 0, strpos($label, ' '));
			} else {
				$value = strtok($label, ' ');
			}
			$position_points_options[$value] = $label;
		}
		$this->data['position_points_options'] = $position_points_options;

		// Handle "Change H-W-C + Follower Options" form submission
		if (!empty($this->input->post(NULL, true)) && $this->input->post('change_hwc_follower_options')) {

			$h_w_c_group_keys = array_keys($this->data['h_w_c_group']);
			$posted_h_w_c    = $this->input->post('h_w_c_group', true);
			$follower_type   = $this->input->post('follower_type', true);
			$ball_points     = $this->input->post('ball_points', true);
			$position_points = $this->input->post('position_points', true);

			// Validate h_w_c_group
			if (!in_array($posted_h_w_c, $h_w_c_group_keys)) {
				$posted_h_w_c = !empty($h_w_c_group_keys) ? $h_w_c_group_keys[0] : '';
			}

			// Normalise follower_type
			$follower_type = ($follower_type === 'position') ? 'position' : 'after_ball';

			// Determine which follower_select to use
			$follower_select = ($follower_type === 'position') ? $position_points : $ball_points;
			$follower_select = trim((string) $follower_select);

			if (empty($posted_h_w_c) || empty($follower_select)) {
				$this->session->set_flashdata('message', 'Please select a valid H-W-C group and follower option.');
				redirect('admin/statistics/hwc_followers/' . $id);
				return;
			}

			// Generate the prediction
			$generated = $this->predictions_m->hwc_followers($id, $pool_size, $posted_h_w_c, $follower_type, $follower_select);

			$lottery_numbers = $generated ? $generated : '';

			if (!$generated) {
				$this->session->set_flashdata('message', 'The selected H-W-C group and follower combination did not produce any predictions. Please try different settings.');
			}

			$this->statistics_m->hwc_followers_save(
				$id,
				$posted_h_w_c,
				$follower_type,
				$ball_points,
				$position_points,
				$lottery_numbers,
				null   // preserve prev_lottery_numbers; only cleared on lottery profile changes
			);

			$this->session->set_flashdata('hwc_follower_message', 'Prediction updated with H-W-C (' . $posted_h_w_c . ') + ' . ($follower_type === 'position' ? 'Position ' . $follower_select : 'After Ball ' . $follower_select));
			redirect('admin/statistics/hwc_followers/' . $id);
			return;
		}

		// Load saved prediction record
		$hwc_followers_record = $this->statistics_m->hwc_followers_exists($id);

		// Pass H-W-C settings (range, extra_included, extra_draws) — read-only display
		$hwc_settings = $this->statistics_m->h_w_c_exists($id);
		$this->data['hwc_settings'] = array(
			'range'          => isset($hwc_settings['range'])          ? $hwc_settings['range']          : 'N/A',
			'extra_included' => isset($hwc_settings['extra_included']) ? (int) $hwc_settings['extra_included'] : 0,
			'extra_draws'    => isset($hwc_settings['extra_draws'])    ? (int) $hwc_settings['extra_draws']    : 0,
		);

		// Pass Followers settings (range, extra_included, extra_draws) — read-only display
		$followers_settings = $this->statistics_m->followers_exists($id);
		$this->data['followers_settings'] = array(
			'range'          => isset($followers_settings['range'])          ? $followers_settings['range']          : 'N/A',
			'extra_included' => isset($followers_settings['extra_included']) ? (int) $followers_settings['extra_included'] : 0,
			'extra_draws'    => isset($followers_settings['extra_draws'])    ? (int) $followers_settings['extra_draws']    : 0,
		);

		$this->data['hwc_followers_record']    = $hwc_followers_record;
		$this->data['saved_h_w_c_group']       = $hwc_followers_record ? $hwc_followers_record['h_w_c_group']     : '';
		$this->data['saved_follower_type']     = $hwc_followers_record ? $hwc_followers_record['follower_type']   : 'after_ball';
		$this->data['saved_ball_points']       = $hwc_followers_record ? $hwc_followers_record['ball_points']     : '';
		$this->data['saved_position_points']   = $hwc_followers_record ? $hwc_followers_record['position_points'] : '';
		$this->data['lottery_numbers']         = $hwc_followers_record ? $hwc_followers_record['lottery_numbers'] : '';
		$this->data['prev_lottery_numbers']    = $hwc_followers_record ? $hwc_followers_record['prev_lottery_numbers'] : '';

		// Auto-refresh: if the saved ball/position is no longer in the current draw's options
		// (stale after a new draw import), regenerate the prediction with the top-ranked option.
		if ($hwc_followers_record && !empty($this->data['lottery_numbers'])) {
			$_curr_type = $this->data['saved_follower_type'];
			$_stale = false;
			if ($_curr_type === 'position') {
				$_stale = !empty($this->data['saved_position_points'])
					&& !array_key_exists($this->data['saved_position_points'], $position_points_options);
			} else {
				$_stale = !empty($this->data['saved_ball_points'])
					&& !array_key_exists($this->data['saved_ball_points'], $ball_points_options);
			}

			if ($_stale) {
				$_new_ball = !empty($ball_points_options)     ? (string) array_key_first($ball_points_options)     : $this->data['saved_ball_points'];
				$_new_pos  = !empty($position_points_options) ? (string) array_key_first($position_points_options) : $this->data['saved_position_points'];
				$_follower_select = ($_curr_type === 'position') ? $_new_pos : $_new_ball;

				$_generated = $this->predictions_m->hwc_followers(
					$id, $pool_size, $hwc_followers_record['h_w_c_group'], $_curr_type, $_follower_select
				);
				$_new_numbers = $_generated ?: $this->data['lottery_numbers'];

				// Save updated ball/position and regenerated numbers; keep prev_lottery_numbers intact
				$this->statistics_m->hwc_followers_save(
					$id,
					$hwc_followers_record['h_w_c_group'],
					$_curr_type,
					$_new_ball,
					$_new_pos,
					$_new_numbers,
					null   // null = leave prev_lottery_numbers unchanged
				);

				$this->data['saved_ball_points']     = $_new_ball;
				$this->data['saved_position_points'] = $_new_pos;
				$this->data['lottery_numbers']       = $_new_numbers;
			}
		}

		// Next draw date (last_drawn already set via the points chain above)
		$ld  = $this->data['lottery']->last_drawn['draw_date'];
		$day = $this->lotteries_m->return_day($ld);
		$this->data['next_draw_date'] = $this->lotteries_m->next_date($this->data['lottery'], $day, $ld);

		// Flash messages
		if ($this->session->flashdata('hwc_follower_message')) {
			$this->data['hwc_follower_message'] = $this->session->flashdata('hwc_follower_message');
		}

		$this->data['current']     = $this->uri->segment(2);
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users']       = $this->maintenance_m->logged_online(0);
		$this->data['admins']      = $this->maintenance_m->logged_online(1);
		$this->data['visitors']    = $this->maintenance_m->active_visitors();
		$this->data['subview']     = 'admin/dashboard/statistics/hwc_followers';
		$this->load->view('admin/_layout_main', $this->data);
	}
}