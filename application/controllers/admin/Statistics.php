<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statistics extends Admin_Controller {
	
	public function __construct() {
		 parent::__construct();

		 $this->load->dbforge();
		 $this->load->model('lotteries_m');
		 $this->load->model('statistics_m');
		 $this->load->model('maintenance_m');
		 $this->load->helper('file');
		 $this->load->helper('html');
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
		// Fetch all lotteries from the database
		$this->data['lotteries'] = $this->lotteries_m->get();
		foreach($this->data['lotteries'] as $lottery) 
		{
			$tbl_name = $this->lotteries_m->lotto_table_convert($lottery->lottery_name);

			$lottery->last_date = $this->statistics_m->last_date($tbl_name);
			$lottery->last_draw = $this->statistics_m->last_draw($tbl_name, $lottery->balls_drawn, $lottery->extra_ball);
			$c = $this->statistics_m->lottery_rows($tbl_name);
			if($c>100) $c = 100;
			$lottery->average_sum = $this->statistics_m->lottery_average_sum($tbl_name, $c);
			$lottery->sum_last = $this->statistics_m->sum_last($tbl_name, $lottery->balls_drawn);
			$lottery->repeaters = $this->statistics_m->repeaters($tbl_name, $lottery->balls_drawn);
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
	 * @return      none
	 */
	public function btn_stat($uri) 
	{
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
		return anchor($uri, '<i class="fa fa-retweet fa-2x" aria-hidden="true">', array('title' => 'View Historic Follower Statistics after the last draw', 'class' => 'followers'));
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
	 * Calculate the Current History or Update to the latest Draw
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_calculate($uri)
	{
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

		$this->data['draws'] = $this->lotteries_m->load_draws($tbl_name, $new_range, $this->data['trend']);
		if (!$this->data['draws'])
		{
			$this->session->set_flashdata('message', 'There are no draws associated with this lottery. Please import draws.');
			redirect('admin/statistics'); 
		}
		$this->data['interval'] = $interval;		// Record the interval here (for the dropdown)
		$this->data['sel_range'] = $sel_range;		// What was selected for the range in the previous page
		$this->data['range'] = $new_range;
		$this->data['all'] = $all;
		$this->data['statistics'] = $this->statistics_m->get_by('lottery_id='.$id, TRUE);
		$this->data['evensodds'] = $this->statistics_m->evensodds_sum($tbl_name, $this->data['trend']);
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->session->set_userdata('range', $new_range);
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/view_draws'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->data['subview']  = 'admin/dashboard/statistics/view';
		$this->data['stat_method'] = $this;				// Access the methods in the view
		$this->load->view('admin/_layout_main', $this->data);
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
	 * View the follower numbers after the current draw. Default is 100 draws.
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
		$blnduplicate = ($this->data['lottery']->duplicate_extra_ball ? TRUE : FALSE);
		$drawn = $this->data['lottery']->balls_drawn;		// Get the number of balls drawn for this lottory, Pick 5, Pick 6, Pick 7, etc.
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
		// 1. Check for a record for the current lottery in the friends table
		$followers = $this->statistics_m->followers_exists($id);		// Existing follower row 
		$nonfollowers = $this->statistics_m->nonfollowers_exists($id);	// Non Follower existing row
		$sel_range = 1;
		$this->data['lottery']->extra_included = 0; // No Extra Ball as part of the calculation
		$this->data['lottery']->extra_draws = 0; 	// No Bonus Draws included in the friend calculation
		$blnEX = false;								// Extra Bonus Ball / Draws flag are no change or update
		if(!is_null($followers))
		{
			if($this->uri->segment(6)=='extra') // include both the friends and nonfriends for including the extra (bonus) ball
			{
				$this->data['lottery']->extra_included = $this->statistics_m->extra_included($id, TRUE, 'lottery_followers');
				$this->data['lottery']->extra_included = $this->statistics_m->extra_included($id, TRUE, 'lottery_nonfollowers');
				$blnEX = true; // There has been a change to the Extra Bonus Ball / Draws flag
			}
			else // or both the friends and nonfriends for not including the extra ball
			{
				$this->data['lottery']->extra_included = $this->statistics_m->extra_included($id, FALSE, 'lottery_followers');
			}
			if($this->uri->segment(6)=='draws') // include both the friends and nonfriends for including the extra (bonus) draws
			{
				$this->data['lottery']->extra_draws = $this->statistics_m->extra_draws($id, TRUE, 'lottery_followers');
				$this->data['lottery']->extra_draws = $this->statistics_m->extra_draws($id, TRUE, 'lottery_nonfollowers');
				$blnEX = true; // There has been a change to the Extra Bonus Ball / Draws flag
			}
			else // or both the friends and nonfriends for not including the extra draws
			{
				$this->data['lottery']->extra_draws = $this->statistics_m->extra_draws($id, FALSE, 'lottery_followers');
			} 
			//$this->data['lottery']->extra_included = $this->uri->segment(6)=='extra' ? $this->statistics_m->extra_included($id, TRUE, 'lottery_followers') : $this->statistics_m->extra_included($id, FALSE, 'lottery_followers');
			//$this->data['lottery']->extra_draws = ($this->uri->segment(6)=='draws' ? $this->statistics_m->extra_draws($id, TRUE, 'lottery_followers') : $this->statistics_m->extra_draws($id, FALSE, 'lottery_followers'));
			// 2. If exist, check the database for the latest draw range from 100 to all draws for the change in the range
			$range = $this->uri->segment(5,0); // Return segment range
			if(!$range) $range = $followers['range'];
			if($range>100) $sel_range = intval($range / 100);
			if($range!=0)	
			{
				if(intval($followers['range'])!=(intval($range))||$blnEX) // Any Change in Selection/Settings of the Draws?
				{
					$str_followers = $this->statistics_m->followers_calculate($tbl_name, $this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $range, '', $blnduplicate);
					/** NEW included nonfollower calculations **/
					$max = $this->data['lottery']->maximum_ball;
					$mx_extra = ($blnduplicate ? $this->data['lottery']->maximum_extra_ball : $max);
					$str_nonfollowers = $this->statistics_m->nonfollowers_calculate($tbl_name, $this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $range, $max, '', $blnduplicate, $mx_extra);
					$followers = array(
						'range'				=> $range,
						'lottery_followers'	=> $str_followers,
						'draw_id'			=> $this->data['lottery']->last_drawn['id'],
						'lottery_id'		=> $id
					);
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
			// range is set with either less than 100 rows (based on the exact number of draws) or calculate the number of followers using only 100 rows
			$range = ($all<100 ? $all : 100);
			$str_followers = $this->statistics_m->followers_calculate($tbl_name, $this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $range, ''. $blnduplicate);
			$followers = array(
				'range'				=> $range,
				'lottery_followers'	=> $str_followers,
				'draw_id'			=> $this->data['lottery']->last_drawn['id'],
				'lottery_id'		=> $id
			);
			$this->statistics_m->follower_data_save($followers, FALSE);
			$max = $this->data['lottery']->maximum_ball;
			$mx_extra = ($blnduplicate ? $this->data['lottery']->maximum_extra_ball : $max);
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
			for($b = 1; $b<=$drawn; $b++)
			{
				if($this->data['lottery']->last_drawn['ball'.$b]==$n) $this->data['lottery']->last_drawn[$n] = $f;
			}
			if(($this->data['lottery']->extra_included)&&($this->data['lottery']->last_drawn['extra']==$n))
			{
				$this->data['lottery']->last_drawn[$n] = $f;
			}
		}
		// 5. Do the same for non-following string into the array counter parts also
		$nf_next = (!is_null($nonfollowers) ? explode(",", $nonfollowers['lottery_nonfollowers']) : explode(",", $str_nonfollowers));
		foreach($nf_next as $ball_drawn)
		{
			$n = strstr($ball_drawn, '>', TRUE); // Strip off each number
			$nf = substr(strstr($ball_drawn, '>', FALSE),1); // Remove the '>' from the string
			for($b = 1; $b<=$drawn; $b++)
			{
				if($this->data['lottery']->last_drawn['ball'.$b]==$n) $this->data['lottery']->last_drawn[$n.'nf'] = $nf;
			}
			if(($this->data['lottery']->extra_included&&$this->data['lottery']->last_drawn['extra']==$n))
			{
				$this->data['lottery']->last_drawn[$n.'nf'] = $nf;
			}
		}
		
		$this->data['lottery']->last_drawn['interval'] = $interval;		// Record the interval here (for the dropdown)
		$this->data['lottery']->last_drawn['sel_range'] = $sel_range;	// What was selected for the range in the previous page
		$this->data['lottery']->last_drawn['range'] = $range;
		$this->data['lottery']->last_drawn['all'] = $all;
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
		$this->data['message'] = '';	// Defaulted to No Error Messages
		$this->data['lottery'] = $this->lotteries_m->get($id);
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		$blnduplicate = ($this->data['lottery']->duplicate_extra_ball ? TRUE : FALSE);
		$drawn = $this->data['lottery']->balls_drawn;		// Get the number of balls drawn for this lottory, Pick 5, Pick 6, Pick 7, etc.
		$max_ball = $this->data['lottery']->maximum_ball;	// Get the highest ball drawn for this lottery, e.g. 49 in Lottery 649, 50 in Lottomax
		$extra_ball = $this->data['lottery']->extra_ball;	// Make sure the extra ball is even used.
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
			if($new_range>100) $sel_range = intval($new_range / 100);
			if($new_range!=0)	
			{
				if(intval($old_range)!=(intval($new_range))||($change)) // Any Change in Selection of the Draws? then update ... e.i. 200 draws in db and 300 in query url
				{
					$str_friends = $this->statistics_m->friends_calculate($tbl_name, $drawn, $max_ball, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, '', $blnduplicate);
					$associate = explode('+', $str_friends); // The '+' is the separator
					$str_friends = $associate[0];			 // separated the friends
					$str_nonfriends = $associate[1]; 		 // from the non friends
					$friends = array(
						'range'				=> $new_range,
						'lottery_friends'	=> $str_friends,
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
			$new_range = ($all<100 ? $all : 100);
			$str_friends = $this->statistics_m->friends_calculate($tbl_name, $drawn, $max_ball, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, '', $blnduplicate);
			$associate = explode('+', $str_friends); // The '+' is the separator
			$str_friends = $associate[0];			 // separated the friends
			$str_nonfriends = $associate[1];		 // from the non friends
			$friends = array(
				'range'				=> $new_range,
				'lottery_friends'	=> $str_friends,
				'draw_id'			=> $this->data['lottery']->last_drawn['id'],
				'lottery_id'		=> $id
			);
			$this->statistics_m->friends_data_save($friends, FALSE);
			$nonfriends = array(
				'range'					=> $new_range,
				'lottery_nonfriends'	=> $str_nonfriends,
				'draw_id'				=> $this->data['lottery']->last_drawn['id'],
				'lottery_id'			=> $id
			);
			$this->statistics_m->nonfriends_data_save($nonfriends, FALSE);
		}
		
		// 4. Extract the friends string into the array counter parts
		$next_draw = (!is_null($friends) ? explode(",", $friends['lottery_friends']) : explode(",", $str_friends)); // DB or ??
		$nonfriends_draw = (!is_null($nonfriends) ? explode("|", $nonfriends['lottery_nonfriends']) : explode("|", $str_nonfriends)); // DB or ??
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
															//if the form was submitted, the database values will compared to the submitted ones.
		$this->data['lottery'] = $this->lotteries_m->get($id);
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
				$this->data['lottery' 	]->C = $colds; 				// Number of Colds Distributed e.g 16 Colds
			}
			else
			{
				$hot_form = $this->input->post('hots');				// POST values for hots, warms, colds
				$warm_form = $this->input->post('warms');
				$cold_form = $this->input->post('colds');
				if(($hot_form!=$hots)||($warm_form!=$hots)||($cold_form!=$colds))	// Has a change been requested
				{
					$blnheat = TRUE;								// A change has been made 
					$w_start = intval($hot_form+1);
					$this->data['lottery']->H = $hot_form;
					$c_start = ($max_ball-intval($cold_form))+1;  	
					$this->data['lottery']->W = $warm_form;  					
					$this->data['lottery']->C = $cold_form; 
				}
			}
			$this->data['lottery']->extra_included = $this->uri->segment(6)=='extra' ? $this->statistics_m->extra_included($id, TRUE, 'lottery_h_w_c') : $this->statistics_m->extra_included($id, FALSE, 'lottery_h_w_c');
			if($h_w_c['extra_included']!=$this->data['lottery']->extra_included) $blnheat = TRUE; // A change in extra included has occurred
			$this->data['lottery']->extra_draws = ($this->uri->segment(6)=='draws' ? $this->statistics_m->extra_draws($id, TRUE, 'lottery_h_w_c') : $this->statistics_m->extra_draws($id, FALSE, 'lottery_h_w_c'));
			if($h_w_c['extra_draws']!=$this->data['lottery']->extra_draws) $blnheat = TRUE; // A change in extra draws has occurred
			$sel_range = ($new_range>100 ? $sel_range = intval($new_range / 100) : $sel_range = 1);
			$strdupextra = "";	// Always empty for all lotteries. exception is a lottery with an extra ball that can have a duplicate number

			if($new_range!=0)	
			{
				if(intval($old_range)!=(intval($new_range))||($blnheat)) // Any Change in Selection of the Draws? then update ... e.i. 200 draws in db and 300 in query url
				{
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
						'dupextra'			=>	$strdupextra,
						'overdue'			=> 	$stroverdue,
						'draw_id'			=> 	$this->data['lottery']->last_drawn['id'],
						'lottery_id'		=> 	$id,
						'extra_included'	=> 	$this->data['lottery']->extra_included,
						'extra_draws'		=> 	$this->data['lottery']->extra_draws,
						'w'					=> 	$w_start,
						'c'					=> 	$c_start,
						'h_count'			=> 	$this->data['lottery']->H,
						'w_count'			=> 	$this->data['lottery']->W,
						'c_count'			=> 	$this->data['lottery']->C
					);
					$this->statistics_m->hwc_data_save($hwc, TRUE);
				}
				else  
				{
					//$new_range = $all;
					$sel_range = intval($h_w_c['range'] / 100);
					if(!$sel_range) $sel_range = 1; // Less than 100 draws
					$strhots = $h_w_c['hots']; 		// Pull from DB
					$strwarms = $h_w_c['warms'];
					$strcolds = $h_w_c['colds'];
					$strdupextra = $h_w_c['dupextra'];
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
			$this->data['lottery']->C = $heat[2]; 					// Num
			
			$str_hwc = $this->statistics_m->h_w_c_calculate($tbl_name, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, $w_start, $c_start, '', $blnduplicate);
			if($blnduplicate&&$this->data['lottery']->extra_included) $strdupextra = $this->statistics_m->hwc_duple_extra($tbl_name, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, '');
			$strhots = $this->statistics_m->hots($str_hwc);
			$strwarms = $this->statistics_m->warms($str_hwc);
			$strcolds = $this->statistics_m->colds($str_hwc);
			$stroverdue = $this->statistics_m->overdue($strhots, $strwarms, $strcolds, $tbl_name, $drawn, $new_range);
			$hwc = array(
						'range'				=> 	$new_range,
						'hots'				=> 	$strhots,
						'warms'				=> 	$strwarms,
						'colds'				=> 	$strcolds,
						'dupextra'			=>	$strdupextra,
						'overdue'			=> 	$stroverdue,
						'draw_id'			=> 	$this->data['lottery']->last_drawn['id'],
						'lottery_id'		=> 	$id,
						'extra_included'	=> 	$this->data['lottery']->extra_included,
						'extra_draws'		=> 	$this->data['lottery']->extra_draws,
						'w'					=> 	$w_start,
						'c'					=> 	$c_start,
						'h_count'			=> 	$this->data['lottery']->H,
						'w_count'			=> 	$this->data['lottery']->W,
						'c_count'			=> 	$this->data['lottery']->C	
					);
			$this->statistics_m->hwc_data_save($hwc, FALSE);
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
		foreach($overdue as $all_overdue)
		{
			$n = strstr($all_overdue, '=', TRUE); // Strip off the ball drawn to the right of the equal sign
			$c = substr(strstr($all_overdue, '='), 1); // Strip off to the left of the equal sign count
			$this->data['lottery']->overdue[$n] = $c; 
		}

		$hwc_history = $this->statistics_m->hwc_history_exists($id);
 		if(is_null($hwc_history)) // Correct Lottery & Range?
		{
			$hwc_history = $this->h_w_c_history($id, $tbl_name, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range, $w_start, $c_start, $blnduplicate);
			if (!$hwc_history) // Problem with calculating H-W-C's over range
			{
				$this->session->set_flashdata('message', 'There is a problem with the H (Hots) - W (Warms) - C (Colds) over the last '.$$new_range.' Draws.');
				redirect('admin/statistics');
			}
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
								'draw_id'			=> 	$this->data['lottery']->last_drawn['id'],
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
				if (!$hwc_history) // Problem with calculating H-W-C's over range
				{
					$this->session->set_flashdata('message', 'There is a problem with the H (Hots) - W (Warms) - C (Colds) over the last '.$$new_range.' Draws.');
				redirect('admin/statistics');
				}
				$hwc_history['h_w_c_range'] = substr($hwc_history['h_w_c_range'], 0, -1);  				// Remove the last comma
				$hwc_history['h_w_c_last_10'] = substr($hwc_history['h_w_c_last_10'], 0, -1);
			}
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
								'h_w_c_last_1'		=> 	$this->data['lottery']->last_hwc,
								'h_w_c_last_10'		=> 	$hwc_history['h_w_c_last_10'],
								'draw_id'			=> 	$this->data['lottery']->last_drawn['id'],
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
		/* $h_positions = new SplFixedArray($scale[0]); // Declare the hots
		$w_positions = new SplFixedArray($scale[1]); // Declare the warms
		$c_positions = new SplFixedArray($scale[2]); // Declare the colds */

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

			$fd = $this->statistics_m->hwc_next_draw($table, $examine_date); // return the full with draw date, ball 1 ... ball n + extra
			if($fd)	// next draw returned?
			{
				$examine_date = $fd['draw_date'];	// Next date for H_W_C Calculation
				$next_drawn = $this->statistics_m->only_picks($picks, $fd);
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
			// Find in the associative positions array if the current h-w-c exists?
			// If current h-w-c does not exist, instantiate the new associate array with the hots, warms and colds with the scales (resizes)
					// Iterate the hot positions where if the ball was not drawn set hot position = 0 else the hot position = +1
					// Iterate the warm positions where if the ball was not drawn set warm position = 0 else the warm position = +1 
					// Iterate the colds positions where if the ball was not drawn set cold position = 0 else the cold position = +1
			// if current h-w-c does exist, iterate the hot position = +1 AND iterate the warm position = +1 AND iterate the cold position = +1  
			$row++;
		} 
		while($row<=$range);
		// Create an empty string for h-w-c position counts called $strheats
		// Iterate each h-w-c until last h-w-c. e.g. 6 - 0 - 0, 5 - 1 - 0
		// concatenate the h-w-c to string plus the '>' to the $strheats
			// $h_position is 0 
			// do hots whule $h_position not greater than $scale
				// If associate array $count = $h_w_c[$h_position] does not equal zero, concatenate $h_position=$count plus ',' (separator)
				// if associate array $count = $h_w_c[$h_position] equals zero, do nothing and move on to the next position
				// $h_position = $h_position + 1
			// end do
			// concatenate the '|' separator symbol for the warms
			// $w_position is 0 
			// do warms whule $w_position not greater than $scale
				// If associate array $count = $h_w_c[$w_position] does not equal zero, concatenate $h_position=$count plus ',' (separator)
				// if associate array $count = $h_w_c[$w_position] equals zero, do nothing and move on to the next position
				// $w_position = $w_position + 1
			// end do
			// $c_position is 0 
			// do colds whule $c_position not greater than $scale
				// If associate array $count = $h_w_c[$c_position] does not equal zero, concatenate $c_position=$count plus ',' (separator)
				// if associate array $count = $h_w_c[$c_position] equals zero, do nothing and move on to the next position
				// $c_position = $c_position + 1
			// end do 

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

			$fd = $this->statistics_m->hwc_next_draw($table, $examine_date); // return the full with draw date, ball 1 ... ball n + extra
			if($fd)	// next draw returned?
			{
				$examine_date = $fd['draw_date'];	// Next date for H_W_C Calculation
				$next_drawn = $this->statistics_m->only_picks($picks, $fd);
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
	
		$recalc = FALSE;
		if($this->statistics_m->last_stats_exist($tbl_name)) /** First Check to see that the lottery db exists and statistics exist */
		{
			$draw_id = $this->statistics_m->last_id($tbl_name);
			if($this->statistics_m->recalc_update($id, $draw_id)) // Second, if a new draw has been entered or manually entered, return true to recalc //
			{
				$recalc = TRUE;
				// Verified the Draw Statistics have been completed
				// 1. The H (Hots) - W (Warms) - C (Colds) will be RE-CALC'd
				$this->recalc_hwc($id,$this->data['lottery']);
				
				// 2. The Followers will be RE-CALC'd
				$this->recalc_followers($id,$this->data['lottery']);

				// 3. The Friends of numbers will be RE-CALC'd	
				$this->recalc_friends($id, $this->data['lottery']);
			}
		}
		else
		{	// Verfied that the Draw Statistics have not been completed, exit action with an error message
			$this->session->set_flashdata('message', 'The latest Draw has not been completed. Please enter the next draw and click the Calculator to complete the draw statistics.');
			redirect('admin/statistics');
		}
		if($recalc)
		{
			$this->session->set_flashdata('message', 'The Hot - Warm - Cold, Followers and Friends Statistics have ALL been updated to the latest draw.');
			redirect('admin/statistics');
		}
	}
	/**
	 * ReCALCULATES the Lottery H-W-C, it will retrieve the last H-W-C. If it exists, the first draw (for the given range) will be retrieved.
	 * Each number that was drawn in the first draw will be subtracted from the counts in the H-W-C. The last draw will be retrieved and will be
	 * added to the 
	 * H-W-C. The overdue will be reset, if the last drawn number was an overdue number 
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
	 																						// exception is a lottery with an extra ball that can have a duplicate number
	 $h_w_c = $this->statistics_m->h_w_c_exists($id);

	 if(!is_null($h_w_c))	// Existing HWC?
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
		$stroverdue = $this->statistics_m->overdue($strhots, $strwarms, $strcolds, $tbl, $drawn,  $h_w_c['extra_included'], $h_w_c['extra_draws'], $new_range, '');
		$hwc = array(
			'range'				=> $new_range,
			'hots'				=> $strhots,
			'warms'				=> $strwarms,
			'colds'				=> $strcolds,
			'dupextra'			=> $str_dupextra,
			'overdue'			=> $stroverdue,
			'draw_id'			=> $lotto->last_drawn['id'],
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
		// Recalculation is nesessary
		$hwc_history = $this->h_w_c_history($id, $tbl, $drawn, $h_w_c['extra_included'], $h_w_c['extra_draws'], $new_range, $w_start, $c_start, $blnduplicate);
	 }
	 else 
	 {
		 $lotto->extra_included = 0; // No Extra Ball as part of the calculation
		 $lotto->extra_draws = 0; 	// No Bonus Draws included in the friend calculation
		 $new_range = ($all<100 ? $all : 100);
		 $heat = explode('-', $this->statistics_m->hwc_defaults[$max_ball]); 	// Break out the H-W-C into a new array
		 $w_start = intval($heat[0]+1);					// Warms
		 $lotto->H = $heat[0];  						// Number of Hots Distributed e.g. 16 Hots
		 $c_start = ($max_ball-intval($heat[2]))+1; 	// Return the Cold value
		 $lotto->W = $heat[1];  						// Number of Warms Distributed e.g 18 Colds
		 $lotto->C = $heat[2]; 							// Num
		 
		 $str_hwc = $this->statistics_m->h_w_c_calculate($tbl, $drawn, $lotto->extra_included, $lotto->extra_draws, $new_range, $w_start, $c_start, '');
		 if($blnduplicate&&$h_w_c['extra_included']) $str_dupextra = $this->statistics_m->hwc_duple_extra($tbl, $h_w_c['extra_included'], $$h_w_c['extra_draws'], $new_range, '');	
		 $strhots = $this->statistics_m->hots($str_hwc);
		 $strwarms = $this->statistics_m->warms($str_hwc);
		 $strcolds = $this->statistics_m->colds($str_hwc);
		 $stroverdue = $this->statistics_m->overdue($strhots, $strwarms, $strcolds, $tbl, $drawn, $new_range);
		 $hwc = array(
					 'range'			=> $new_range,
					 'hots'				=> $strhots,
					 'warms'			=> $strwarms,
					 'colds'			=> $strcolds,
					 'dupextra'			=> $str_dupextra,
					 'overdue'			=> $stroverdue,
					 'draw_id'			=> $lotto->last_drawn['id'],
					 'lottery_id'		=> $id,
					 'extra_included'	=> $lotto['extra_included'],
					 'extra_draws'		=> $lotto['extra_draws'],
					 'w'				=> $w_start,
					 'c'				=> $c_start,
					 'h_count'			=> $lotto->H,
					 'w_count'			=> $lotto->W,
					 'c_count'			=> $lotto->C	
				 );
		 $this->statistics_m->hwc_data_save($hwc, FALSE);
		 // Recalculation is nesessary
		$hwc_history = $this->h_w_c_history($id, $tbl, $drawn, $lotto['extra_included'], $lotto['extra_draws'], $new_range, $w_start, $c_start, $blnduplicate);
	 }
	 
	 if (!$hwc_history) // Problem with calculating H-W-C's over range
	 {
		 $this->session->set_flashdata('message', 'There is a problem with the H (Hots) - W (Warms) - C (Colds) over the last '.$new_range.' Draws.');
	 redirect('admin/statistics');
	 }
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
						'draw_id'			=> 	$lotto->last_drawn['id'],
						'lottery_id'		=> 	$id,
						'extra_included'	=> 	(!is_null($h_w_c) ? $h_w_c['extra_included'] : $lotto->extra_included),
						'extra_draws'		=> 	(!is_null($h_w_c) ? $h_w_c['extra_draws'] : $lotto->extra_draws),
						);
		$this->statistics_m->hwc_history_save($hwc_h_data, TRUE); // Update existing lottery H W C Record
		unset($hwc_history); // Remove this temporary holding place for historic h-w-c's
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
		$drawn = $lotto->balls_drawn;		// Get the number of balls drawn for this lottory, Pick 5, Pick 6, Pick 7, etc.
		// Check to see if the actual table exists in the db?
		if (!$this->lotteries_m->lotto_table_exists($tbl))
		{
			$this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. '.$tbl.' Does not exist. Create the Lottery Database now.');
			redirect('admin/statistics');
		}
		$all = $this->lotteries_m->db_row_count($tbl); // Return the total number of draws for this lottery

		$lotto->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl);	// Retrieve the last drawn numbers and draw date
		// 1. Check for a record for the current lottery in the friends table
		$followers = $this->statistics_m->followers_exists($id);		// Existing follower row 
		$nonfollowers = $this->statistics_m->nonfollowers_exists($id);	// Non Follower existing row
	
		if(!is_null($followers))
		{
			// 2. If exist, check the database for the latest draw range from 100 to all draws for the change in the range

			$range = $followers['range'];
			$str_followers = $this->statistics_m->followers_calculate($tbl, $lotto->last_drawn, $drawn, $followers['extra_included'], $followers['extra_draws'], $range,'',$blnduplicate);
			/** NEW included nonfollower calculations **/
			$max = $this->data['lottery']->maximum_ball;
			$mx_extra = ($blnduplicate ? $this->data['lottery']->maximum_extra_ball : $max);
			$str_nonfollowers = $this->statistics_m->nonfollowers_calculate($tbl, $lotto->last_drawn, $drawn, $followers['extra_included'], $followers['extra_draws'], $range, $max, '', $blnduplicate, $mx_extra);
			$followers = array(
				'range'				=> $range,
				'lottery_followers'	=> $str_followers,
				'draw_id'			=> $lotto->last_drawn['id'],
				'lottery_id'		=> $id
			);
			$this->statistics_m->follower_data_save($followers, TRUE);
			/** NEW included nonfollower Data Save **/
			$nonfollowers = array(
				'range'					=> $range,
				'lottery_nonfollowers'	=> $str_nonfollowers,
				'draw_id'				=> $lotto->last_drawn['id'],
				'lottery_id'			=> $id
			);
			$this->statistics_m->nonfollower_data_save($nonfollowers, TRUE);
		}
		else // 3. If does not exist, calculate for the given draw range, return results and save to follower table
		{
			// range is set with either less than 100 rows (based on the exact number of draws) or calculate the number of followers using only 100 rows
			$range = ($all<100 ? $all : 100);
			$str_followers = $this->statistics_m->followers_calculate($tbl, $lotto->last_drawn, $drawn, 0, 0, $range,'',$blnduplicate);
			$followers = array(
				'range'				=> $range,
				'lottery_followers'	=> $str_followers,
				'draw_id'			=> $lotto->last_drawn['id'],
				'lottery_id'		=> $id
			);
			$this->statistics_m->follower_data_save($followers, FALSE);
			$max = $this->data['lottery']->maximum_ball;
			$mx_extra = ($blnduplicate ? $this->data['lottery']->maximum_extra_ball : $max);
			$str_nonfollowers = $this->statistics_m->nonfollowers_calculate($tbl, $lotto->last_drawn, $drawn, 0, 0, $range, $max, '', $blnduplicate, $mx_extra);
			$nonfollowers = array(
			'range'					=> $range,
			'lottery_nonfollowers'	=> $str_nonfollowers,
			'draw_id'				=> $lotto->last_drawn['id'],
			'lottery_id'			=> $id
			);
			$this->statistics_m->nonfollower_data_save($nonfollowers, FALSE);
		}
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

		if(!is_null($friends)&&(!is_null($nonfriends)))
		{
			$range = $friends['range'];
			$str_friends = $this->statistics_m->friends_calculate($tbl_name, $drawn, $max_ball, $friends['extra_included'], $friends['extra_draws'], $range, '', $blnduplicate);
			$associate = explode('+', $str_friends); // The '+' is the separator
			$str_friends = $associate[0];			 // separated the friends
			$str_nonfriends = $associate[1]; 		 // from the non friends
			$friends = array(
				'range'				=> $range,
				'lottery_friends'	=> $str_friends,
				'draw_id'			=> $lotto->last_drawn['id'],
				'lottery_id'		=> $id
			);
			$this->statistics_m->friends_data_save($friends, TRUE);
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
			$str_friends = $this->statistics_m->friends_calculate($tbl_name, $drawn, $max_ball, 0, 0, $new_range, '', $blnduplicate);
			$associate = explode('+', $str_friends); // The '+' is the separator
			$str_friends = $associate[0];			 // separated the friends
			$str_nonfriends = $associate[1]; 		 // from the non friends
			$friends = array(
				'range'				=> $new_range,
				'lottery_friends'	=> $str_friends,
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
}