<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Predictions extends Admin_Controller {
	
	public function __construct() {
		 parent::__construct();
		 $this->load->model('lotteries_m');
		 $this->load->model('predictions_m');
		 $this->load->helper('file');
		 $this->load->library('image_lib');
	}

	/**
	 * Retrieves List of All Lotteries
	 * 
	 * @param       none
	 * @return      none
	 */
	public function index() { 
		// Fetch all lotteries from the database
		$this->data['lotteries'] = $this->lotteries_m->get();
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->data['predictions'] = $this;		// Access the methods in the view
		$this->data['subview'] = 'admin/dashboard/predictions/index';
		$this->load->view('admin/_layout_main', $this->data);
	}

	/**
	 * Initialize the calculation form for the generator
	 * 
	 * @param       $id		Lottery id
	 * @return      none
	 */
	public function combinations($id)
	{
		$this->data['message'] = '';			// Defaulted to No Error Messages
		$this->data['save'] = FALSE;			// Default is always greyed out for a Save Filename
		$this->data['combinations'] = (int) 0; 	// Default to 0
		$this->data['lottery'] = $this->lotteries_m->get($id);
		$this->data['lottery']->predict = $this->input->post('ball_predict', TRUE);
		$this->data['lottery']->pick = $this->input->post('lottery_balls_drawn', TRUE);
		
		if(isset($this->data['lottery']->predict)&&isset($this->data['lottery']->pick))	// Must be posted precict and pick
		{
			$combo_rules = $this->predictions_m->rules;
			$this->form_validation->set_rules($combo_rules);
		
			if ($this->form_validation->run() == TRUE) 
			{
				$this->data['combinations'] = $this->predictions_m->bcComb_N_R($this->data['lottery']->predict, $this->data['lottery']->pick);
				$this->data['save'] = TRUE;
				$this->data['message'] = "Combination Calculation is complete.";
			}
		}
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->data['subview'] = 'admin/dashboard/predictions/combinations';
		$this->load->view('admin/_layout_main', $this->data);
	}

	/**
	 * Adds a record to the database and creates a blank text file for generating the full wheeling table
	 * Check for a duplicate filename, return error or add new db record and save filename in combinations directory
	 * 
	 * @param       $id		Lottery id
	 * @return      none
	 */

	public function combo_save($id)
	{
		$this->data['message'] = '';	// Defaulted to No Error Messages
		$this->data['save'] = TRUE; 	// Default is always greyed out for a Save Filename
		$this->data['lottery'] = $this->lotteries_m->get($id);
		$this->data['lottery']->predict = $this->input->post('ball_predict', TRUE);
		$this->data['lottery']->pick = $this->input->post('lottery_balls_drawn', TRUE);
		$this->data['combinations'] = $this->input->post('combinations', TRUE);
		$file_name = (intval($this->data['lottery']->pick)<9 ? '0'.$this->data['lottery']->pick : $this->data['lottery']->pick);
		$file_name .= $this->data['lottery']->predict;
		$file_name .= (intval($this->data['combinations'])<1000 ? '0'.$this->data['combinations'] : $this->data['combinations']);
		
		if(DIRECTORY_SEPARATOR=='\\')
		{
		// Windows	
			$path = 'd:\\wamp64\\www\\lottotrak\\'.predictions_m::DIR.'\\'.$file_name.'.txt';
		}
		else 
		// Linux
		{
			$path = DIRECTORY_SEPARATOR.predictions_m::DIR.DIRECTORY_SEPARATOR.$file_name.'.txt';
		}

		if(file_exists($path)) 
		{
			$this->data['message'] = $file_name.'.txt currently exists in the '.predictions_m::DIR.' directory.<br />Please delete this File first.';
		}
		else
		{
			$combo_data = array(
				'file_name'	=> $file_name,
				'N'	=>	$this->data['lottery']->predict,
				'R'	=>	$this->data['lottery']->pick,
				'CCCC' => $this->data['combinations'],
				'lottery_id' => $id
			);

			if(!$this->predictions_m->lottery_combo_save($combo_data))
			{
				$this->data['message'] = 'There is a problem with adding a record to the lottery_combination_files table.';
			}
			else
			{
				$combo_file = fopen($path, "w");
				if(!$combo_file)
				{
					$this->data['message'] = 'There is a problem writing the file to the directory.';
				}
				else
				{
					$txt = "";	// Blank Text
					fwrite($combo_file, $txt);
					$this->data['message'] = 'The FILE:'.$file_name.'.txt has been successfully created and saved.<br />You can generate the Combinations.';
				}
				fclose($combo_file);
			}
		}

		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->data['subview'] = 'admin/dashboard/predictions/combinations';
		$this->load->view('admin/_layout_main', $this->data);
	}

	/**
	 * Generate Full Wheeling Tables
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_generate($uri) 
	{
		return anchor($uri, '<i class="fa fa-circle-o-notch fa-2x" aria-hidden="true">', array('title' => 'Generate Full Wheeling Table Text Files'));
	}

	/**
	 * Saved Full Wheeling Table Files for Filtering
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_files($uri)
	{
		return anchor($uri, '<i class="fa fa-file-text-o fa-2x" aria-hidden="true">', array('title' => 'View Generated Full Wheeling Table Files', 'class' => 'followers'));
	}

	/**
	 * View Historic Wins from Generated Full Wheeling Tables with Filters
	 * 
	 * @param      string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_wins($uri)
	{
		return anchor($uri, '<i class="fa fa-money fa-2x" aria-hidden="true">', array('title' => 'View Historic Win History and Prizes from the wheeling table and filtering'));
	}

	/**
	 * Calculate the Current History or Update to the latest Draw
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_calculate($uri)
	{
		return anchor($uri, '<i class="fa fa-calculator fa-2x" aria-hidden="true">', array('title' => 'Calculate the Number of Combinations from Total Number of Predictions
		(e.g. 15 Balls) for a sample size (e.g. Pick - 6, Pick 7)', 'class' => 'calculate'));
	}

	/**
	 * Predictions for the next draw
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_predicts($uri)
	{
		return anchor($uri, '<i class="fa fa-eye fa-2x" aria-hidden="true">', 
		array('title' => 'The Best Predictions for the next draw', 'class' => 'predict'));
	}
	/**
	 * Checks to see if the combinations are out of range between pick 3 to pick 9 and not greater than the maximum ball drawn
	 * 
	 * @param       none
	 * @return      TRUE/FALSE TRUE (if all in range), FALSE (if any ball is out of range)
	 */
	public function _range_ball_values($str) 
	{
		if ((intval($this->input->post('ball_predict'))>=intval($this->input->post('minimum_ball')))&&(intval($this->input->post('ball_predict'))<=intval($this->input->post('maximum_ball'))))
		{
			return TRUE;
		} 
	
	$this->form_validation->set_message('_range_ball_values', 'The Number of Balls for the Combinations are out of range (N).');
	return FALSE;
	}
}