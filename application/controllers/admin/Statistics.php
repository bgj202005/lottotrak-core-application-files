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
		 //$this->output->enable_profiler(TRUE);
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
			$lottery->average_sum = $this->statistics_m->lottery_average_sum($tbl_name, $lottery->balls_drawn, $c);
			$lottery->sum_last = $this->statistics_m->sum_last($tbl_name, $lottery->balls_drawn);
			$lottery->repeaters = $this->statistics_m->repeaters($tbl_name, $lottery->balls_drawn);
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
		return anchor($uri, '<i class="fa fa-line-chart fa-2x" aria-hidden="true">', array('title' => 'View Commulative Statistics'));
	}

	/**
	 * View the Hot Warm Cold Numbers, Overdue and Consecutive numbers of each draw
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_hwc($uri) 
	{
		return anchor($uri, '<i class="fa fa-thermometer-full fa-2x" aria-hidden="true">', array('title' => 'Calculate and View the Hot - Warm - Cold of the Numbers'));
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
		return anchor($uri, '<i class="fa fa-history fa-2x" aria-hidden="true">', array('title' => 'View Historic Friend History Statistics of the lottery'));
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
		if(!empty($this->uri->segment(5))) $this->data['trend'] = 1;
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

		$this->data['draws'] = $this->lotteries_m->load_draws($tbl_name, $id);
		if (!$this->data['draws'])
		{
			$this->session->set_flashdata('message', 'There are no draws associated with this lottery. Please import draws.');
			redirect('admin/statistics'); 
		}
		$this->data['statistics'] = $this->statistics_m->get_by('lottery_id='.$id, TRUE);
		$this->data['evensodds'] = $this->statistics_m->evensodds_sum($tbl_name, $id);
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
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
		$extra_ball = $this->data['lottery']->extra_ball;	// Make sure the extra ball is even used.
		// Check if recalc is needed
		$recalc = $this->uri->segment(5,NULL); // Return segment recalc or NULL
		// Check to see if the actual table exists in the db?
		if (!$this->lotteries_m->lotto_table_exists($tbl_name))
		{
			$this->session->set_flashdata('message', 'There is an INTERNAL error with this lottery. '.$tbl_name.' Does not exist. Create the Lottery Database now.');
			redirect('admin/statistics');
		}
		if(!$this->statistics_m->lottery_stats_exist($tbl_name, is_null($recalc))) // Stats Exists?
		{
			// If FALSE, No Statistics have been previously calculated:
			// Expand the draw database with the 8 field columns. 
			if($this->statistics_m->lottery_expand_columns($tbl_name, is_null($recalc)))  // Columns need to be expanded and recalc is not selected
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
			$draws = $lt_rows; // Capture the number of draws in this lottery
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
			else // The query resulted in NULL indicating the statistics are up-to-date
			{
				$this->session->set_flashdata('message', 'The Statistics are up-to-date. Please Calculate after the next draw.');
				redirect('admin/statistics');
			}
		}
			$stats = array();
			// Average Sum of Last 10 Draws (Integer)
			$stats['sum_10'] = $this->statistics_m->lottery_average_sum($tbl_name, $drawn, 10);
			// Average Sum of Last 100 Draws (Integer) 
			$stats['sum_100'] = $this->statistics_m->lottery_average_sum($tbl_name, $drawn, ($draws < 100 ? $draws : 100));
			// Average Sum of Last 200 Draws (Integer)
			$stats['sum_200'] = $this->statistics_m->lottery_average_sum($tbl_name, $drawn, ($draws < 200 ? $draws : 200));
			// Average Sum of Last 300 Draws (Integer)
			$stats['sum_300'] = $this->statistics_m->lottery_average_sum($tbl_name, $drawn, ($draws < 300 ? $draws : 300));
			// Average Sum of Last 400 Draws (Integer)
			$stats['sum_400'] = $this->statistics_m->lottery_average_sum($tbl_name, $drawn, ($draws < 400 ? $draws : 400));
			// Average Sum of Last 500 Draws (Integer)
			$stats['sum_500'] = $this->statistics_m->lottery_average_sum($tbl_name, $drawn, ($draws < 500 ? $draws : 500));
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
			$this->session->set_flashdata('message', 'The Draw Statistics Have been Updated.'); // Yahoo! Statistics Updated!
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
	 * @param		$id		current id of Lottery	
	 * @return      none
	 */
	public function followers($id)
	{
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
		if(!is_null($followers))
		{
			$this->data['lottery']->extra_included = $this->uri->segment(6)=='extra' ? $this->statistics_m->extra_included($id, TRUE, 'lottery_followers') : $this->statistics_m->extra_included($id, FALSE, 'lottery_followers');
			$this->data['lottery']->extra_draws = ($this->uri->segment(6)=='draws' ? $this->statistics_m->extra_draws($id, TRUE, 'lottery_followers') : $this->statistics_m->extra_draws($id, FALSE, 'lottery_followers'));
			// 2. If exist, check the database for the latest draw range from 100 to all draws for the change in the range
			$range = $this->uri->segment(5,0); // Return segment range
			if(!$range) $range = $followers['range'];
			if($range>100) $sel_range = intval($range / 100);
			if($range!=0)	
			{
				if(intval($followers['range'])!=(intval($range))) // Any Change in Selection of the Draws?
				{
					
					$str_followers = $this->statistics_m->followers_calculate($tbl_name, $this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $range);
					/** NEW included nonfollower calculations **/
					$max = $this->data['lottery']->maximum_ball;
					$str_nonfollowers = $this->statistics_m->nonfollowers_calculate($tbl_name, $this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $range, $max);
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
			$str_followers = $this->statistics_m->followers_calculate($tbl_name, $this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $range);
			
			$followers = array(
				'range'				=> $range,
				'lottery_followers'	=> $str_followers,
				'draw_id'			=> $this->data['lottery']->last_drawn['id'],
				'lottery_id'		=> $id
			);
			$this->statistics_m->follower_data_save($followers, FALSE);
			$max = $this->data['lottery']->maximum_ball;
			$str_nonfollowers = $this->statistics_m->nonfollowers_calculate($tbl_name, $this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $range, $max);
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
	 * @param		$id		current id of draws	
	 * @return  	none
	 */
	public function friends($id)
	{
		$this->data['message'] = '';	// Defaulted to No Error Messages
		$this->data['lottery'] = $this->lotteries_m->get($id);
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
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
		$new_range = $this->uri->segment(5,0); // Return segment range
		$old_range = $friends['range'];
		if(!$new_range) $new_range = $old_range;	// Database Range
		$sel_range = 1;								// All Defaults
		$this->data['lottery']->extra_included = 0; // No Extra Ball as part of the calculation
		$this->data['lottery']->extra_draws = 0; 	// No Bonus Draws included in the friend calculation

		if(!is_null($friends))
		{
			$this->data['lottery']->extra_included = $this->uri->segment(6)=='extra' ? $this->statistics_m->extra_included($id, TRUE, 'lottery_friends') : $this->statistics_m->extra_included($id, FALSE, 'lottery_friends');
			$this->data['lottery']->extra_draws = ($this->uri->segment(6)=='draws' ? $this->statistics_m->extra_draws($id, TRUE, 'lottery_friends') : $this->statistics_m->extra_draws($id, FALSE, 'lottery_friends'));
			if($new_range>100) $sel_range = intval($new_range / 100);
			if($new_range!=0)	
			{
				if(intval($old_range)!=(intval($new_range))) // Any Change in Selection of the Draws? then update ... e.i. 200 draws in db and 300 in query url
				{
					$str_friends = $this->statistics_m->friends_calculate($tbl_name, $drawn, $max_ball, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range);
					$friends = array(
						'range'				=> $new_range,
						'lottery_friends'	=> $str_friends,
						'draw_id'			=> $this->data['lottery']->last_drawn['id'],
						'lottery_id'		=> $id
					);
					$this->statistics_m->friends_data_save($friends, TRUE);
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
			$str_friends = $this->statistics_m->friends_calculate($tbl_name, $drawn, $max_ball, $this->data['lottery']->extra_included, $this->data['lottery']->extra_draws, $new_range);
			$friends = array(
				'range'				=> $new_range,
				'lottery_friends'	=> $str_friends,
				'draw_id'			=> $this->data['lottery']->last_drawn['id'],
				'lottery_id'		=> $id
			);
			$this->statistics_m->friends_data_save($friends, FALSE);
		}
		
		// 4. Extract the friends string into the array counter parts
		$next_draw = (!is_null($friends) ? explode(",", $friends['lottery_friends']) : explode(",", $str_friends)); // DB or ??
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
}