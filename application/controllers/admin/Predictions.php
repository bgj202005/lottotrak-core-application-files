<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Predictions extends Admin_Controller {
	
	
	public function __construct() {
		 parent::__construct();
		 $this->load->model('lotteries_m');
		 $this->load->model('predictions_m');
		 $this->load->library('Math_Combinatorics'); // * Originally from the Pear Libraries *
		 $this->load->model('maintenance_m'); 
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
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->session->set_userdata('uri', 'admin/'.$this->data['current']);
		$this->data['predictions'] = $this;		// Access the methods in the view
		$this->data['subview'] = 'admin/dashboard/predictions/index';
		$this->session->set_userdata('uri', 'admin/'.$this->data['current']);
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this ->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->session->unset_userdata('range'); // Range is non-existent prior to combinations
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
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/combinations'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
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
		$file_name = (intval($this->data['lottery']->pick)<=9 ? '0'.$this->data['lottery']->pick : $this->data['lottery']->pick);
		$file_name .= '0'.$this->data['lottery']->predict;
		$file_name .= (intval($this->data['combinations'])<1000 ? '0'.$this->data['combinations'] : $this->data['combinations']);
		
		$path = $this->predictions_m->full_path($file_name);

		if((file_exists($path))&&($this->predictions_m->lottery_combination_record($file_name))) 
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
		$this->data['save'] = FALSE; // Don't allow for duplicate saving of the same filename on the same screen.
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/combinations'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
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
			$file_name = $this->data['lottery']->generate[0]->file_name; // Get the single file name
			$file_path = $this->predictions_m->full_path($file_name);
			if (file_exists($file_path)) {
				$file_content = file_get_contents($file_path); // Read file content
				$is_generated = !empty(trim($file_content)); // Check if file content is not empty
			} else {
				$file_content = ''; // No content if file does not exist
				$is_generated = false; // File does not exist, so not generated
			}
			$this->data['combinations']=$this->data['lottery']->generate[0]->CCCC; 	//Calculated Combinations
			$this->data['predict']=$this->data['lottery']->generate[0]->N;			//Number of Predictions
			$this->data['pick']=$this->data['lottery']->generate[0]->R;				// Pick Game
			$this->data['filename']=$this->data['lottery']->generate[0]->file_name;	// File name of text file
			unset($this->data['lottery']->generate);
			// Pass these variables to the view
	        $this->data['file_content'] = $file_content;
    	    $this->data['is_generated'] = $is_generated;
			$this->data['subview'] = 'admin/dashboard/predictions/generate';
		}
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/generate'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
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
		// Read the content of the file
    	$file_path = $this->predictions_m->full_path($file_name);
		if (file_exists($file_path)) {
			$file_content = file_get_contents($file_path); // Read file content
			$is_generated = !empty(trim($file_content)); // Check if file content is not empty
		} 
		else {
			$file_content = ''; // No content if file does not exist
			$is_generated = false; // File does not exist, so not generated
		}
		// Pass these variables to the view
		$this->data['file_content'] = $file_content;
		$this->data['is_generated'] = $is_generated;
		unset($this->data['lottery']->generate);
		
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/generate'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	 
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
		$message = "";							// Defaulted to No Error Messages
		$error = FALSE;
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
		$this->data['combinations'] = count($combinations);
		if(!$this->predictions_m->combs_already($this->data['filename'], $this->data['combinations']))
		{
			if(!$this->predictions_m->text_combs_save($this->data['filename'],$combinations)) //Separate into the proper format and save to the text file
			{
			//$this->data['message'] = "An error has occurred to convert the combinations to a text file.";
				$message = "An error has occurred to convert the combinations to a text file.";
				$error = TRUE;
			}
			else 
			{
				$error = FALSE;
				//$message = "The Data File has ADDED the Combinations to the ".$this->data['filename'].".txt file.";
			}
		}
		else
		{
			//$this->data['message'] = "This Combination File:".$this->data['filename'].".txt ALREADY have the combinations added to the file.";
			$message = "This Combination File:".$this->data['filename'].".txt ALREADY have the combinations added to the file.";
			$error = TRUE;
		}
		if($error)
		{
			$output = array(
			'success'	=> FALSE,
			'error'  => $message
			);
		} 
		else
		{
			$output = array(
			'success'  => TRUE,
			'message'  => $message
		);
		}
		echo json_encode($output);
	}

	/**
	 * Combination Counter
	 * with HTML and PHP using ajax calls
	 * @param string	$name		progress percentage until complete
	 * @param integer	$combs		Total Number of Combinations in the table
	 * @return      	none
	 */
	public function combo_counter($name, $combs)
	{
		$fp = fopen($this->predictions_m->full_path($name), "r");
		$combotext = '';
		$processed_combinations = 20; // Process 20 combinations at a time

		// Initialize session variables if not already set
		if (!$this->session->userdata('percent')) {
			$percent = 0; // Start with 0%
			$offset = 0;
		} else {
			$percent = $this->session->userdata('percent');
			$offset = $this->session->userdata('offset');
		}

		// Calculate remaining combinations
		$remaining_combinations = $combs - ($percent / 100 * $combs);
		$interval = ($remaining_combinations < $processed_combinations) ? $remaining_combinations : $processed_combinations;

		// If there are no remaining combinations, complete immediately
		if ($remaining_combinations <= 0) {
			$percent = 100;
			$this->session->unset_userdata('percent');
			$this->session->unset_userdata('offset');
			fclose($fp);

			$output = array(
				'success' => true,
				'combotext' => '',
				'percent' => $percent
			);

			echo json_encode($output);
			return;
		}

		// Process combinations in chunks
		$i = $interval;
		fseek($fp, $offset); // Move the file pointer to the last processed position
		while ($i > 0 && !feof($fp)) {
			$combotext .= fgets($fp);
			$i--;
		}

		$offset = ftell($fp); // Update the file pointer offset
		$percent += ($interval / $combs) * 100; // Calculate progress percentage

		if ($percent > 100) {
			$percent = 100; // Ensure progress does not exceed 100%
		}

		// Update session data
		$newdata = array(
			'percent' => $percent,
			'offset' => $offset
		);
		$this->session->set_userdata($newdata);

		fclose($fp); // Close the file pointer

		// Return the output
		$output = array(
			'success' => true,
			'combotext' => $combotext,
			'percent' => $percent // Return the updated progress percentage
		);

		echo json_encode($output);
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
		elseif(count($this->data['lottery']->generate)==1) 
		{
			redirect('admin/predictions'); 	// No More Files available, Redirect
		}
		elseif(!$this->data['lottery']->generate)
		{
			redirect('admin/predictions'); 	// No More Files available, Redirect
		}
		elseif(!is_null($this->uri->segment(6,NULL)))
		{
			redirect('admin/predictions'); 	// No More Files available, Redirect
		}
		else
		{
			$this->data['combinations']=$this->data['lottery']->generate[0]->CCCC; 	//Calculated Combinations
			$this->data['predict']=$this->data['lottery']->generate[0]->N;			//Number of Predictions
			$this->data['pick']=$this->data['lottery']->generate[0]->R;				// Pick Game
			$this->data['filename']=$this->data['lottery']->generate[0]->file_name;	
			unset($this->data['lottery']->generate);
			$this->data['subview'] = 'admin/dashboard/predictions/generate';
		}
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->load->view('admin/_layout_main', $this->data);
	}

	/**
	 * go to a form that lists the number of combination files 
	 * Files generate combinations calls to html and php.
	 * @param       integer	$id		Lottery Identifier
	 * @return      none
	 */
	public function files($id)
	{
		$this->data['message'] = '';			// Defaulted to No Error Messages
		$this->data['lottery'] = $this->lotteries_m->get($id);
		$this->data['lottery']->generate = $this->predictions_m->lottery_combination_files($id); //$this->predictions_m->all_combination_files();
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->data['predictions'] = $this;		// Access the methods in the view
		$this->data['subview'] = 'admin/dashboard/predictions/combo_select';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	/**
	 * Views all Combinations from this file, filtering and Draw Search Options
	 *  being imported in the database
	 * @param       $id		current id of draws		
	 * @return      none
	 */
	public function combo_view($id)
	{
		$this->data['message'] = '';
		$this->data['lottery'] = $this->lotteries_m->get($id);
		$filename = (!empty($this->input->post('file')) ? $this->input->post('file') : $this->uri->segment(5));
		$this->data['file'] = $this->predictions_m->lottery_combination_record($filename);
		if(!empty($this->uri->segment(6))) 
		{
			$new_range = $this->uri->segment(6,0); // Return segment range
		}
		$old_range = (!is_null($this->session->userdata('range')) ? $this->session->userdata('range') : 400); // Default will be all the combinatons 
		if(!isset($new_range)) $new_range = $old_range;	// Database Range
		$CCCC = (int) $this->data['file'][0]->CCCC; // Return the Number of combinations of this file
		if($CCCC>=400)
		{
			$interval = intval($CCCC / 400); // Create the drop down in multiples of 100 and typecast to an integer value (truncates the floating point portion)
			if(!$interval) $interval = 1;	// 0 = 0 - 399, 1 = 800, 2 = 1200, 3 = 1600, 4 = 2200, 5 = 2600  
		}
		else
		{
			$interval = 0;
			$old_range = $CCCC;
			$new_range = $old_range;
		}
		$this->data['draws'] = $this->predictions_m->load_draws($filename, (int) $this->data['file'][0]->R, $new_range);
		if(!$this->data['draws'])
		{
			$this->data['message'] = 'The Combination File of Picks could not be loaded.';
		}
		
		$sel_range = 1;								// All Defaults
		if($new_range>=400) $sel_range = intval($new_range / 400);
		$this->data['interval'] = $interval;		// Record the interval here (for the dropdown)
		$this->data['sel_range'] = $sel_range;		// What was selected for the range in the previous page
		$this->data['range'] = $new_range;
		$this->data['all'] = $CCCC;
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'combo_view'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['subview']  = 'admin/dashboard/predictions/combo_view';
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->load->view('admin/_layout_main', $this->data);
	}

	/**
	 * Displays the statistics for a selected combination file.
	 *
	 * This method calculates the number of winning tickets for each prize tier,
	 * the percentage of wins, and the probability of winning. It retrieves the
	 * combinations from a text file and processes them for statistical analysis.
	 *
	 * @param int 		$id The ID of the selected lottery.
	 * @param string $file_name The name of the combination file (without the .txt extension).
	 * @return void
	 */
	public function combo_statistics($id) {
		$file_name = (!empty($this->input->post('file')) ? $this->input->post('file') : $this->uri->segment(5));
		// Decode the file name
		$pick_per_ticket = substr($file_name, 0, 2); // First two digits
		$numbers_to_pick = substr($file_name, 2, 2); // Next two digits
    	$tickets = substr($file_name, 4); // Truncate the first 4 characters to get the tickets
		// Fetch the prize tiers for the lottery
		$lottery = $this->lotteries_m->get($id);
    	// Fetch the prize tiers for the lottery
    	$prizes_data = $this->predictions_m->prizes_data_array($id);
		 // Map prize tiers to their corresponding names and required matches
		$prize_tiers = [
			'9_win_extra' => ['name' => '9 Matches + Extra', 'matches' => 9],
			'9_win' => ['name' => '9 Matches', 'matches' => 9],
			'8_win_extra' => ['name' => '8 Matches + Extra', 'matches' => 8],
			'8_win' => ['name' => '8 Matches', 'matches' => 8],
			'7_win_extra' => ['name' => '7 Matches + Extra', 'matches' => 7],
			'7_win' => ['name' => '7 Matches', 'matches' => 7],
			'6_win_extra' => ['name' => '6 Matches + Extra', 'matches' => 6],
			'6_win' => ['name' => '6 Matches', 'matches' => 6],
			'5_win_extra' => ['name' => '5 Matches + Extra', 'matches' => 5],
			'5_win' => ['name' => '5 Matches', 'matches' => 5],
			'4_win_extra' => ['name' => '4 Matches + Extra', 'matches' => 4],
			'4_win' => ['name' => '4 Matches', 'matches' => 4],
			'3_win_extra' => ['name' => '3 Matches + Extra', 'matches' => 3],
			'3_win' => ['name' => '3 Matches', 'matches' => 3],
			'2_win_extra' => ['name' => '2 Matches + Extra', 'matches' => 2],
			'2_win' => ['name' => '2 Matches', 'matches' => 2],
			'1_win_extra' => ['name' => '1 Match + Extra', 'matches' => 1],
			'1_win' => ['name' => '1 Match', 'matches' => 1],
			'extra' => ['name' => 'Extra Ball Only', 'matches' => 0],
		];
		// Filter out the prize tiers that are set (value is 1)
		$prizes = [];
		foreach ($prizes_data as $key => $value) {
			if ($value == 1 && isset($prize_tiers[$key])) {
				$prizes[] = $prize_tiers[$key];
			}
		}
		// Path to the file containing combinations
		$file_path = $this->predictions_m->full_path($file_name);
		// Check if the file exists
		if (!file_exists($file_path)) {
			show_error('The selected combination file does not exist.');
		}
		// Read the file and extract combinations
		$combinations = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		// Total tickets in the file
		$total_tickets = count($combinations);
		// Calculate statistics for each prize tier
		$stats = [];
		foreach ($prizes as $prize) {
			$matching_tickets = $this->predictions_m->calculate_matching_tickets($combinations, $prize['matches']);
			$stats[] = [
				'tier' => $prize['name'],
				'tickets' => $matching_tickets,
				'percentage' => round(($matching_tickets / $total_tickets) * 100, 2),
				'probability' => round(($matching_tickets / $total_tickets) * 100 / 100, 6)
			];
		}
		$this->data['current'] = $file_name; // Sets the Admins Menu Highlighted
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'combo_statistics'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->data['lottery'] = $lottery;
		$this->data['file_name'] = $file_name;
		$this->data['pick_per_ticket'] = $pick_per_ticket;
		$this->data['numbers_to_pick'] = $numbers_to_pick;
		$this->data['tickets'] = $tickets;
		$this->data['stats'] = $stats;
		// Add navigation links
		$this->data['back_to_dashboard'] = base_url('admin/predictions');
		$this->data['back_to_combo_list'] = base_url('admin/predictions/combo_select/' . $id);
		// Load the statistics view
		$this->data['subview'] = 'admin/dashboard/predictions/combo_results';
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
		$icon_class = 'fa fa-circle-o-notch fa-2x';
		if(!$disabled) 
		{
			$title = '';
			$a = 'disabled';
			$style = "pointer-events: none; color: #ccc;";
			$icon_class .= ' disabled-icon'; // Add a class for additional styling if needed
		}
		$attributes = array('title' => $title,
							'style' => $style,
							'disabled' => $a);
	return anchor($uri, '<i class="' . $icon_class . '" aria-hidden="true"></i>', $attributes);
	}

	/**
	 * Saved Full Wheeling Table Files for Filtering
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_table_of_wins($uri, $disabled = FALSE)
	{
		$style = '';
		$a = '';
		$title = 'Detailed Breakdown of Win Possibilities!';
		if(!$disabled) 
		{
			$title = '';
			$a = 'disabled';
			$style = "pointer-events: none";
		}
		$attributes = array('title' => $title,
							'style' => $style,
							'disabled' => $a);

		return anchor($uri, '<i class="fa fa-table fa-2x" aria-hidden="true">', $attributes);
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
	 * View Combinations
	 * 
	 * @param       string	$uri	uri admin address of the statistics page
	 * @return      none
	 */
	public function btn_view($uri)
	{
		return anchor($uri, '<i class="fa fa-eye fa-2x" aria-hidden="true">', 
		array('title' => 'View the Complete List of Combinations', 'class' => 'view'));
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
	/**
 * Custom validation callback to ensure the number of balls to predict (N)
 * is greater than the number to pick (R).
 *
 * @param none 
 * @return bool Returns TRUE if valid, otherwise FALSE.
 */
public function _validate_picks($str)
{
    if (intval($this->input->post('ball_predict') <= (intval($this->input->post('lottery_balls_drawn'))))) {	
        $this->form_validation->set_message(
            '_validate_picks',
            'The Number of Balls to Predict (N) must be greater than the Number to Balls to Pick (R).'
        );
        return FALSE; // Validation failed
    }
    return TRUE; // Validation passed
}
}