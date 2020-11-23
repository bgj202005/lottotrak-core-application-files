<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statistics extends Admin_Controller {
	
	public function __construct() {
		 parent::__construct();
		 $this->load->dbforge();
		 $this->load->model('lotteries_m');
		 $this->load->model('statistics_m');
		 $this->load->helper('file');
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
				$draw = $this->statistics_m->lottery_draw_stats($tbl_name, $lt_id, $this-data['lottery']->balls_drawn);

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
			// Average Sum of Last 10 Draws (Integer)
			// Average Sum of Last 100 Draws (Integer) 
			// Average Sum of Last 400 Draws (Integer)
			// Average Sum of Last 500 Draws (Integer)
			// Average Sum of Digits in Last 10 Draws (Integer)
			// Average Sum of Digits in Last 100 Draws (Integer)
			// Average Even Numbers the last 10 Draws (Integer)
			// Average Odd Numbers the last 10 Draws (Integer)
			// Average Even Numbers the last 100 Draws (Integer)
			// Average Odd Numbers the last 100 Draws (Integer)
			// Average Range of Numbers in the last 10 Draws (Integer)
			// Average Range of Numbers in the last 100 Draws (integer)
			// Average Maximum Repeating Decade in the last 10 Draws (Integer)
			// Average Maximum Repeating Decade in the last 100 Draws (integer)
			// Average Maximum Repeating Last Digit in the last 10 Draws (Integer)
			// Average Maximum Repeating Last Digit in the last 100 Draws (Integer)
			// Do until draws statistically calculated, Report on Screen
			$this->session->set_flashdata('message', 'The Draw Statistics Have been Updated.');
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