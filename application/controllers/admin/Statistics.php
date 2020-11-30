<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statistics extends Admin_Controller {
	
	public function __construct() {
		 parent::__construct();
		 $this->load->dbforge();
		 $this->load->model('lotteries_m');
		 $this->load->model('statistics_m');
		 $this->load->helper('file');
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
			$lottery->average_sum = $this->statistics_m->average_sum_100($tbl_name, $lottery->balls_drawn);
			$lottery->sum_last = $this->statistics_m->sum_last($tbl_name, $lottery->balls_drawn);
			$lottery->repeaters = $this->statistics_m->repeaters($tbl_name, $lottery->balls_drawn);
		}

		if ($this->session->flashdata('message')) $this->data['message'] = $this->session->flashdata('message');
		else $this->data['message'] = '';
		// Load the view

		$this->data['current'] = $this->uri->segment(2); // Sets the Statistics menu
		$this->data['subview'] = 'admin/dashboard/statistics/index';
		$this->data['statistics'] = $this;		// Access the methods in the view
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
	 * View Historic Follower Statistics after the last draw
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_followers($uri)
	{
		return anchor($uri, '<i class="fa fa-retweet fa-2x" aria-hidden="true">', array('title' => 'View Historic Follower Statistics after the last draw'));
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
		return anchor($uri, '<i class="fa fa-calculator fa-2x" aria-hidden="true">', array('title' => 'Calculate the Current History or Update to the latest Draw', 'id' => 'calculate'));
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
		
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->data['subview']  = 'admin/lotteries/view';
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
	$drawn = $this->data['lottery']->balls_drawn;	// Get the number of balls drawn for this lottory, Pick 5, Pick 6, Pick 7, etc.
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
			if($this->statistics_m->lottery_expand_columns($tbl_name))
			{
				$lt_rows = $this->statistics_m->lottery_rows($tbl_name);	// Return the Rows in the table to update
				$lt_id =  $this->statistics_m->lottery_start_id($tbl_name);
				$draw = array();	// Empty Set Array
				$error = FALSE;		// No Errors found at this point							
				do{
				// Update each draw, calculate the Total Sum, Total Sum of Digits, Evens, Odds, Range of Draw, Repeating Decade, Repeating Last Digit
				$draw = $this->statistics_m->lottery_draw_stats($tbl_name, $lt_id, $drawn);

				if (!$this->statistics_m->lottery_draw_update($tbl_name, $lt_id, $draw))
				{
					$error = TRUE; // Unable to update draw row, exist with the error flag set to TRUE
					break;
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
					$error = TRUE; // Unable to update draw row, exist with the error flag set to TRUE
					break;
				}
				$error = FALSE;	// Keep going, update successful.
				//echo json_encode($lt_id);
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
			$stats['sum_100'] = $this->statistics_m->lottery_average_sum($tbl_name, $drawn, 100);
			// Average Sum of Last 200 Draws (Integer)
			$stats['sum_200'] = $this->statistics_m->lottery_average_sum($tbl_name, $drawn, 200);
			// Average Sum of Last 300 Draws (Integer)
			$stats['sum_300'] = $this->statistics_m->lottery_average_sum($tbl_name, $drawn, 300);
			// Average Sum of Last 400 Draws (Integer)
			$stats['sum_400'] = $this->statistics_m->lottery_average_sum($tbl_name, $drawn, 400);
			// Average Sum of Last 500 Draws (Integer)
			$stats['sum_500'] = $this->statistics_m->lottery_average_sum($tbl_name, $drawn, 500);
			// Average Sum of Digits in Last 10 Draws (Integer)
			$stats['digits_10'] = $this->statistics_m->lottery_average_sumdigits($tbl_name, $drawn, 10);
			// Average Sum of Digits in Last 100 Draws (Integer)
			$stats['digits_100'] = $this->statistics_m->lottery_average_sumdigits($tbl_name, $drawn, 100);
			// Average Even Numbers the last 10 Draws (Integer)
			$stats['even_10'] = $this->statistics_m->lottery_average_evens($tbl_name, $drawn, 10);
			// Average Even Numbers the last 100 Draws (Integer)
			$stats['even_100'] = $this->statistics_m->lottery_average_evens($tbl_name, $drawn, 100);
			// Average Odd Numbers the last 10 Draws (Integer)
			$stats['odd_10'] = $this->statistics_m->lottery_average_odds($tbl_name, $drawn, 10);
			// Average Odd Numbers the last 100 Draws (Integer)
			$stats['odd_100'] = $this->statistics_m->lottery_average_odds($tbl_name, $drawn, 100);
			// Average Range of Numbers in the last 10 Draws (Integer)
			$stats['range_10'] = $this->statistics_m->lottery_average_range($tbl_name, $drawn, 10);
			// Average Range of Numbers in the last 100 Draws (integer)
			$stats['range_100'] = $this->statistics_m->lottery_average_range($tbl_name, $drawn, 100);
			// Average Maximum Repeating Decade in the last 10 Draws (Integer)
			$stats['repeat_decade_10'] = $this->statistics_m->lottery_average_decade($tbl_name, $drawn, 10);
			// Average Maximum Repeating Decade in the last 100 Draws (integer)
			$stats['repeat_decade_100'] = $this->statistics_m->lottery_average_decade($tbl_name, $drawn, 100);
			// Average Maximum Repeating Last Digit in the last 10 Draws (Integer)
			$stats['repeat_last_10'] = $this->statistics_m->lottery_average_last($tbl_name, $drawn, 10);
			// Average Maximum Repeating Last Digit in the last 100 Draws (Integer)
			$stats['repeat_last_100'] = $this->statistics_m->lottery_average_last($tbl_name, $drawn, 100);
			$stats['lottery_id'] = $id;		// Must be associated with the lottery_id
			// Do until draws statistically calculated, Report on Screen
			if(!$this->statistics_m->get($id, TRUE))	// No previous lottery global statistics exist, create a new lottery statistics record
			{
				if(!$this->statistics_m->save($stats))
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
}