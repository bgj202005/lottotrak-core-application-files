<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Predictions extends Admin_Controller {
	
	public function __construct() {
		 parent::__construct();
		 $this->load->model('lotteries_m');
		 $this->load->model('predictions_m');
		 $this->load->library('Math_Combinatorics'); // * Originally from the Pear Libraries *
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
	 * @param       integer	$id		Lottery id
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
	 * @param       integer	$id		Lottery id
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
		
		$path = $this->predictions_m->full_path($file_name);

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
					$this->data['message'] = 'The FiLE:'.$file_name.'.txt has been successfully created and saved.<br />You can generate the Combinations.';
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
	 * Begin the generation process, go to a form that selects the proper combination 
	 * File or start the generate combinations calls to html and php.
	 * @param       integer $id		Lottery id
	 * @return      none
	 */
	public function generate($id)
	{
		$this->data['message'] = '';			// Defaulted to No Error Messages
		$this->data['lottery'] = $this->lotteries_m->get($id);
		$this->data['lottery']->generate = $this->predictions_m->lottery_combination_files($id);
		if(count($this->data['lottery']->generate)>1) 
		{
			$this->data['predictions'] = $this;		// Access the methods in the view
			$this->data['subview'] = 'admin/dashboard/predictions/file_select';
		}
		else
		{
			$this->data['combinations']=$this->data['lottery']->generate[0]->CCCC; 	//Calculated Combinations
			$this->data['predict']=$this->data['lottery']->generate[0]->N;			//Number of Predictions
			$this->data['pick']=$this->data['lottery']->generate[0]->R;				// Pick Game
			$this->data['filename']=$this->data['lottery']->generate[0]->file_name;	// File name of text file
			unset($this->data['lottery']->generate);
			$this->data['subview'] = 'admin/dashboard/predictions/generate';
		}
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	/**
	 * Get the data from the selected combination file record, begin the combination calculation process
	 * with HTML and PHP using ajax calls
	 * @param       integer	$id		Lottery id
	 * @return      none
	 */
	public function generate_select($id)
	{
		$this->data['message'] = '';	// Defaulted to No Error Messages
		$this->data['lottery'] = $this->lotteries_m->get($id);
		$file_name = $this->input->post('file', TRUE);  // POST value from radio selection
		$this->data['lottery']->generate = $this->predictions_m->lottery_combination_record($file_name);
		
		$this->data['combinations']=$this->data['lottery']->generate[0]->CCCC; 		//Calculated Combinations
		$this->data['predict']=$this->data['lottery']->generate[0]->N;				//Number of Predictions
		$this->data['pick']=$this->data['lottery']->generate[0]->R;					// Pick Game
		$this->data['filename']=$this->data['lottery']->generate[0]->file_name;		// File name of text file
		unset($this->data['lottery']->generate);
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->data['subview'] = 'admin/dashboard/predictions/generate';
		$this->load->view('admin/_layout_main', $this->data);
	}

	/**
	 * Combination Generator, that cycles through all the combinations between html and php 
	 * Completes a number of combinations before the text file is updated on the server
	 * @param       integer $id		Lottery id
	 * @return      none
	 */
	public function combo_gen($id)
	{
		$this->data['message'] = '';			// Defaulted to No Error Messages
		$this->data['lottery'] = $this->lotteries_m->get($id);
		$file_name = $this->input->post('filename', TRUE);  // POST value from radio selection
		$this->data['lottery']->generate = $this->predictions_m->lottery_combination_record($file_name);
		$this->data['combinations']=$this->data['lottery']->generate[0]->CCCC; 	//Calculated Combinations
		$this->data['predict']=$this->data['lottery']->generate[0]->N;			//Number of Predictions
		$this->data['pick']=$this->data['lottery']->generate[0]->R;				// Pick Game
		$this->data['filename']=$this->data['lottery']->generate[0]->file_name;		// File name of text file
		unset($this->data['lottery']->generate);
		//$this->data['subview'] = 'admin/dashboard/predictions/generate';
		$predict[] = array();	// declare a blank number prediction array
		$combinations[] = array();
		$predict = $this->predictions_m->wheeled($this->data['predict']);
		$combinations = $this->math_combinatorics->combinations($predict, $this->data['pick']); // Based on the pick game 
		if(!$this->predictions_m->text_combo_save($this->data['filename'],$combinations)) //Separate into the proper format and save to the text file
		{
			$this->data['message'] = "An error has occurred to convert the combinations to a text file.";
		}
		
			$str_value = implode(' ', $combinations[0]);
			$return_arr[] = array('row'	=> '1',
								  'result'	=> $str_value);
			unset($combinations);

		$this->session->set_userdata($return_arr);

		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->load->view('admin/_layout_main', $this->data);

		echo json_encode($return_arr);

	}

	/**
	 * Combination Counter
	 * with HTML and PHP using ajax calls
	 * @param       integer	$id		Lottery id
	 * @return      none
	 */
	public function combo_counter($id)
	{
		$return_arr[] = array('row'	=> $this->session->row,
						'result'	=>	$this->session->result);
		$return_arr['row'] = '2';
		echo json_encode($return_arr);
	}

	/**
	 * Get the data from the selected combination file record, begin the combination calculation process
	 * with HTML and PHP using ajax calls
	 * @param		integer	$id			Lottery id
	 * @return      none
	 */
	public function delete($id)
	{		
		$this->data['message'] = '';	// Defaulted to No Error Messages
		$name = $this->uri->segment(5,NULL); // Return segment file_name or NULL
		$this->data['lottery'] = $this->lotteries_m->get($id);

		if(is_null($name))
		{
			$this->data['message'] = 'There is no Filename avaiable to delete the database record and file.';
		}
		if(!is_null($name)&&!$this->predictions_m->delete_combination_record($name))
		{
			$this->data['message'] = 'The record for the filename '.$name.'.txt could not be found.';
		}
		if(!is_null($name)&&!$this->predictions_m->delete_combination_file($name))
		{
			$this->data['message'] = 'The file with the filename '.$name.'.txt could not be found in the combinations directory.';
		}
		$this->data['lottery']->generate = $this->predictions_m->lottery_combination_files($id);
		if(count($this->data['lottery']->generate)>1) 
		{
			$this->data['predictions'] = $this;		// Access the methods in the view
			$this->data['subview'] = 'admin/dashboard/predictions/file_select';
		}
		elseif(!$this->data['lottery']->generate)
		{
			redirect('admin/predictions'); 	// No More Files available, Redirect
		}
		else
		{
			$this->data['combinations']=$this->data['lottery']->generate[0]->CCCC; 	//Calculated Combinations
			$this->data['predict']=$this->data['lottery']->generate[0]->N;			//Number of Predictions
			$this->data['pick']=$this->data['lottery']->generate[0]->R;				// Pick Game
			unset($this->data['lottery']->generate);
			$this->data['subview'] = 'admin/dashboard/predictions/generate';
		}
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->load->view('admin/_layout_main', $this->data);
	}

	/**
	 * Activate the link, if there are combo files waiting to be generated
	 * 
	 * @param       int		$id				Lottery_id associated with Combination Generated Files
	 * @return      boolean TRUE / FALSE	True on Combination Files in the DB or FALSE that there is no record of the combination files.
	 */
	public function active($id) 
	{
		return ($this->predictions_m->lottery_combination_files($id) ? TRUE : FALSE);
	}
	/**
	 * Generate Full Wheeling Tables
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_generate($uri, $disabled = FALSE) 
	{
		$style = '';
		$a = '';
		$title = 'Existing text files can be generated now!';
		if(!$disabled) 
		{
			$title = '';
			$a = 'disabled';
			$style = "pointer-events: none";
		}
		$attributes = array('title' => $title,
							'style' => $style,
							'disabled' => $a);

	return anchor($uri, '<i class="fa fa-circle-o-notch fa-2x" aria-hidden="true">', $attributes);
	}

	/**
	 * Saved Full Wheeling Table Files for Filtering
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_files($uri, $disabled = FALSE)
	{
		$style = '';
		$a = '';
		$title = 'Existing text files can be viewed now!';
		if(!$disabled) 
		{
			$title = '';
			$a = 'disabled';
			$style = "pointer-events: none";
		}
		$attributes = array('title' => $title,
							'style' => $style,
							'disabled' => $a);

		return anchor($uri, '<i class="fa fa-file-text-o fa-2x" aria-hidden="true">', $attributes);
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
	 * Predictions for the next draw
	 * 
	 * @param       string	$uri		uri admin address of the statistics page
	 * @param 		string $file_name	File name of the text file without the .txt extension
	 * @return      none
	 */

	public function btn_trash($uri, $file_name)
	{
		return anchor($uri, '<i class="fa fa-trash-o fa-2x" aria-hidden="true">', 
		array('title' => 'Delete this file and database record', 'class' => 'trash',
		'onclick' => "return confirm('You are about to make a permanent deletion of the filename: $file_name.txt. Both the Filename and the Database Record will be deleted. This can not be UNDONE. Are you sure?')"));
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