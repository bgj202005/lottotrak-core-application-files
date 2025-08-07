<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Predictions extends Admin_Controller {
		
		public function __construct() {
		 parent::__construct();
		 $this->load->model('lotteries_m'); // Lottery Model
		 $this->load->model('statistics_m'); // Statistics Model
		 $this->load->model('history_m'); // History Model
		 $this->load->model('predictions_m'); // Predictions Model (main interface)
		 
		 // Load new specialized models
		 $this->load->model('combination_files_m'); // Combination file operations
		 $this->load->model('lottery_data_m'); // Lottery profile data
		 $this->load->model('lottery_statistics_m'); // Statistical analysis
		 $this->load->model('number_generation_m'); // Number generation
		 $this->load->model('combination_filters_m'); // Filtering logic
		 $this->load->model('math_utilities_m'); // Math utilities
		 
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
		// Fetch only enabled lotteries from the database
		$this->data['lotteries'] = $this->lotteries_m->get_enabled();
		// Check if there is at least one generated file for each lottery
		foreach ($this->data['lotteries'] as &$lottery) {
			$lottery->has_generated_file = $this->predictions_m->has_generated_file($lottery->balls_drawn); // Check if a file exists
		}
		// If futures_form session exists, destroy it
		if ($this->session->userdata('futures_form')) {
			$this->session->unset_userdata('futures_form');
		}
		if ($this->session->userdata('futures_number_array')) {
			$this->session->unset_userdata('futures_number_array');
		}
		if ($this->session->userdata('combination_file_name')) {
			$this->session->unset_userdata('combination_file_name');
			$this->session->unset_userdata('combination_file_id');
		}
		if ($this->session->userdata('current_combination_file')) {
			$this->session->unset_userdata('current_combination_file'); // Clear previous selection
		}
		// Clear statistics file session data
		if ($this->session->userdata('current_statistics_file')) {
			$this->session->unset_userdata('current_statistics_file');
		}  

		if ($this->session->flashdata('message')) $this->data['message'] = $this->session->flashdata('message');
		else $this->data['message'] = '';
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
		$this->data['main_combinations'] = null;	// Default to null
		$this->data['extra_balls_count'] = null;	// Default to null
		$this->data['lottery'] = $this->lotteries_m->get($id);
		$this->data['lottery']->predict = $this->input->post('ball_predict', TRUE);
		$this->data['lottery']->pick = $this->input->post('lottery_balls_drawn', TRUE);
		if(isset($this->data['lottery']->predict)&&isset($this->data['lottery']->pick))	// Must be posted precict and pick
		{
			$combo_rules = $this->predictions_m->rules;
			$this->form_validation->set_rules($combo_rules);
				if ($this->form_validation->run() == TRUE) 
		{
			// Check if this is a lottery with independent extra balls
			if($this->data['lottery']->duplicate_extra_ball && $this->data['lottery']->extra_ball) {
				// Special calculation for lotteries with independent extra balls
				$main_combinations = $this->math_utilities_m->bcComb_N_R($this->data['lottery']->predict, $this->data['lottery']->pick);
				$extra_balls_count = ($this->data['lottery']->maximum_extra_ball - $this->data['lottery']->minimum_extra_ball) + 1;
				$this->data['combinations'] = $main_combinations * $extra_balls_count;
				$this->data['main_combinations'] = $main_combinations;
				$this->data['extra_balls_count'] = $extra_balls_count;
			} else {
				// Standard calculation for regular lotteries
				$this->data['combinations'] = $this->math_utilities_m->bcComb_N_R($this->data['lottery']->predict, $this->data['lottery']->pick);
				$this->data['main_combinations'] = null;
				$this->data['extra_balls_count'] = null;
			}
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
		$this->data['main_combinations'] = null;	// Default to null
		$this->data['extra_balls_count'] = null;	// Default to null
		$this->data['lottery'] = $this->lotteries_m->get($id);
		$this->data['lottery']->predict = $this->input->post('ball_predict', TRUE);
		$this->data['lottery']->pick = $this->input->post('lottery_balls_drawn', TRUE);
		$this->data['combinations'] = $this->input->post('combinations', TRUE);
		$file_name = (intval($this->data['lottery']->pick) < 10 ? '0' : '') . intval($this->data['lottery']->pick);
		$file_name .= (intval($this->data['lottery']->predict) < 10 ? '0' : '') . intval($this->data['lottery']->predict);
		$file_name .= intval($this->data['combinations']); // No leading zero for tickets
		
		// No 'E' suffix needed for independent extra ball lotteries (duplicate_extra_ball = 1)
		// They are handled internally without requiring filename distinction
		
	$path = $this->combination_files_m->full_path($file_name);

	if((file_exists($path))&&($this->combination_files_m->lottery_combination_record($file_name))) 
	{
		$this->data['message'] = $file_name.'.txt currently exists in the '.Combination_files_m::DIR.' directory.<br />Please delete this File first.';
	}
		else
		{
			$combo_data = [
            'file_name' => $file_name,
            'N' => $this->data['lottery']->predict,
            'R' => $this->data['lottery']->pick,
            'CCCC' => $this->data['combinations'],
            'pick_id' => $this->data['lottery']->pick, // Use pick_id instead of lottery_id
        	];

			if(!$this->combination_files_m->lottery_combo_save($combo_data))
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
	 * @param       integer $R		Pick Number for the lottery
	 * @return      none
	 */
	public function generate($id)
	{
	$this->data['message'] = '';			// Defaulted to No Error Messages
	$this->data['lottery'] = $this->lotteries_m->get($id);
	$this->data['lottery']->generate = $this->combination_files_m->lottery_combination_files($this->data['lottery']->balls_drawn);
	if(count($this->data['lottery']->generate)>1)
		{
			$this->data['predictions'] = $this;		// Access the methods in the view
			$this->data['subview'] = 'admin/dashboard/predictions/file_select';
		}
		else
		{		
			$file_name = $this->data['lottery']->generate[0]->file_name; // Get the single file name
			$file_path = $this->combination_files_m->full_path($file_name);
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
		// If no POST data, try to get from session or URL segment
		if (empty($file_name)) {
			// Check if we have stored file_name in session from previous selection
			$file_name = $this->session->userdata('current_combination_file');
			// If still empty, try to get from URL segment
			if (empty($file_name)) {
				$file_name = $this->uri->segment(5, NULL); // Check if file_name is in URL
			}
			// If still empty, redirect to file selection
			if (empty($file_name)) {
				$this->data['message'] = 'No combination file was selected. Please select a file and try again.';
				redirect('admin/predictions/generate/' . $id);
				return;
			}
		} else {
			// Store the selected file in session for future reference
			$this->session->set_userdata('current_combination_file', $file_name);
		}
		$this->data['lottery']->generate = $this->combination_files_m->lottery_combination_record($file_name);
		// Add check if record was found
		if (empty($this->data['lottery']->generate)) {
			$this->data['message'] = 'The selected combination file "' . $file_name . '" was not found in the database.';
			redirect('admin/predictions/generate/' . $id);
			return;
		}
		$this->data['combinations']=$this->data['lottery']->generate->CCCC; 		//Calculated Combinations
		$this->data['predict']=$this->data['lottery']->generate->N;				//Number of Predictions
		$this->data['pick']=$this->data['lottery']->generate->R;					// Pick Game
		$this->data['filename']=$this->data['lottery']->generate->file_name;		// File name of text file
		// Read the content of the file
    	$file_path = $this->combination_files_m->full_path($file_name);
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
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/generate_select'.($id ? '/'.$id : ''));
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
		
		// Reset ALL session variables for this file at the start of new generation
		$percent_key = 'percent_' . $file_name;
		$offset_key = 'offset_' . $file_name;
		$complete_key = 'complete_' . $file_name;
		
		$this->session->unset_userdata($percent_key);
		$this->session->unset_userdata($offset_key);
		$this->session->unset_userdata($complete_key);
		$this->data['lottery']->generate = $this->combination_files_m->lottery_combination_record($file_name);
		$this->data['combinations']=$this->data['lottery']->generate->CCCC; 	//Calculated Combinations
		$this->data['predict']=$this->data['lottery']->generate->N;			//Number of Predictions
		$this->data['pick']=$this->data['lottery']->generate->R;				// Pick Game
		$this->data['filename']=$this->data['lottery']->generate->file_name;		// File name of text file
		unset($this->data['lottery']->generate);
		//$this->data['subview'] = 'admin/dashboard/predictions/generate';
		$predict = $this->number_generation_m->wheeled($this->data['predict']);
		
		// Check if this is a lottery with independent extra balls
		if($this->data['lottery']->duplicate_extra_ball && $this->data['lottery']->extra_ball) {
			// Special generation for lotteries with independent extra balls
			$main_combinations = $this->math_combinatorics->combinations($predict, $this->data['pick']); // Main combinations
			$combinations = array();
			
			// Check if minimum and maximum extra ball values are set
			if (!isset($this->data['lottery']->minimum_extra_ball) || !isset($this->data['lottery']->maximum_extra_ball)) {
				log_message('error', "Missing minimum_extra_ball or maximum_extra_ball for lottery ID: " . $this->data['lottery']->id);
				$message = "Error: Lottery configuration missing minimum_extra_ball or maximum_extra_ball values.";
				$error = TRUE;
			} else {
				// Generate combinations with each possible extra ball
				for($extra = $this->data['lottery']->minimum_extra_ball; $extra <= $this->data['lottery']->maximum_extra_ball; $extra++) {
					foreach($main_combinations as $main_combo) {
						// Add the extra ball to each main combination
						$full_combo = $main_combo;
						$full_combo[] = $extra; // Add extra ball as the last number
						$combinations[] = $full_combo;
					}
				}
				log_message('info', "Generated " . count($combinations) . " total combinations for independent extra ball lottery");
			}
		} else {
			// Standard generation for regular lotteries
			$combinations = $this->math_combinatorics->combinations($predict, $this->data['pick']); // Based on the pick game 
		}
		
		$this->data['combinations'] = count($combinations);
		
		// Additional check to prevent saving empty files
		if ($this->data['combinations'] == 0) {
			log_message('error', "No combinations generated for file: " . $this->data['filename']);
			$message = "Error: No combinations were generated. Check lottery configuration.";
			$error = TRUE;
		} else {
			if(!$this->combination_files_m->combs_already($this->data['filename'], $this->data['combinations']))
			{
				if(!$this->combination_files_m->text_combs_save($this->data['filename'],$combinations)) //Separate into the proper format and save to the text file
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
		$fp = fopen($this->combination_files_m->full_path($name), "r");
		$combotext = '';
		$processed_combinations = 20; // Process 20 combinations at a time
		
		// Use file-specific session keys to prevent conflicts
		$percent_key = 'percent_' . $name;
		$offset_key = 'offset_' . $name;
		$complete_key = 'complete_' . $name;
		
		// Check if already completed
		if ($this->session->userdata($complete_key)) {
			fclose($fp);
			$output = array(
				'success' => true,
				'combotext' => '',
				'percent' => 100
			);
			echo json_encode($output);
			return;
		}
		
		// Initialize session variables if not already set
		if (!$this->session->userdata($percent_key)) {
			$percent = 0; // Start with 0%
			$offset = 0;
		} else {
			$percent = $this->session->userdata($percent_key);
			$offset = $this->session->userdata($offset_key);
		}
		
		// Calculate how many combinations we've processed so far
		$processed_so_far = ($percent / 100) * $combs;
		$remaining_combinations = $combs - $processed_so_far;
		$interval = min($processed_combinations, $remaining_combinations);

		// If there are no remaining combinations, complete immediately
		if ($remaining_combinations <= 0 || $percent >= 100) {
			$percent = 100;
			$this->session->set_userdata($complete_key, true);
			$this->session->unset_userdata($percent_key);
			$this->session->unset_userdata($offset_key);
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
		$new_offset = ftell($fp); // Update the file pointer offset
		
		// Calculate new percentage based on actual progress
		$new_processed = $processed_so_far + $interval;
		$new_percent = ($new_processed / $combs) * 100;
		
		// Ensure we don't exceed 100%
		if ($new_percent >= 100 || $new_processed >= $combs) {
			$new_percent = 100;
			// Mark as complete and clear session data
			$this->session->set_userdata($complete_key, true);
			$this->session->unset_userdata($percent_key);
			$this->session->unset_userdata($offset_key);
		} else {
			// Update session data only if not complete
			$newdata = array(
				$percent_key => $new_percent,
				$offset_key => $new_offset
			);
			$this->session->set_userdata($newdata);
		}
		
		fclose($fp); // Close the file pointer
		
		// Return the output
		$output = array(
			'success' => true,
			'combotext' => $combotext,
			'percent' => $new_percent // Return the updated progress percentage
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
	if(!is_null($name)&&!$this->combination_files_m->delete_combination_record($name))
	{
		$this->data['message'] = 'The record for the filename '.$name.'.txt could not be found.';
	}
	if(!is_null($name)&&!$this->combination_files_m->delete_combination_file($name))
	{
		$this->data['message'] = 'The file with the filename '.$name.'.txt could not be found in the combinations directory.';
	}
	$this->data['lottery']->generate = $this->combination_files_m->lottery_combination_files($id);
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
		$this->data['lottery']->generate = $this->combination_files_m->lottery_combination_files($this->data['lottery']->balls_drawn); //$this->predictions_m->all_combination_files();
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/files'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->data['predictions'] = $this;		// Access the methods in the view
		$this->data['subview'] = 'admin/dashboard/predictions/combo_select';
		$this->load->view('admin/_layout_main', $this->data);
	}
	/**
	 * Main Prediction Futures Selection 
	 * 
	 * @param       $id		Lottery id	
	 * @return      none
	 */
	public function futures($id)
	{
		$this->data['message'] = '';					// Defaulted to No Error Messages
		$this->data['disable_generate_button'] = true; // Used to disable the generate button in the view
		$this->data['lottery'] = $this->lotteries_m->get($id);
		
		// Check if lottery was found
		if (!$this->data['lottery']) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Lottery not found with ID: ' . $id . '</div>');
			redirect('admin/predictions');
			return;
		}
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		// Verify and update expired combination ticket filters
		$expired_check = $this->lottery_data_m->verify_active_date($id, $tbl_name);
		if (!$expired_check) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Unable to update expired combination tables before entering the prediction futures view.</div>');
		}
		$drawn = $this->data['lottery']->balls_drawn; // Get the number of balls drawn for this lottory, Pick 5, Pick 6, Pick 7, etc.
		$this->data['country_code'] = $this->lottery_data_m->get_lottery_country($id);
		$this->data['state_prov_code'] = $this->lottery_data_m->get_lottery_state_prov($id);
		// Fetch combination files for the lottery
    	$this->data['combination_files'] = $this->predictions_m->get_combination_files($id);
		// Before passing $combination_files to the view
		if (!empty($this->data['combination_files'])) {
			 // Transform the combination files to include id|filename in value
			foreach ($this->data['combination_files'] as $index => &$file) {
				$file_path = $this->combination_files_m->full_path($file['file_name']);
				$file_content = file_get_contents($file_path); // Read file content
				if (!empty(trim($file_content))) {
					$file['value'] = $file['id'] . '|' . $file['file_name']; // e.g., "246|060828"
					$file['display'] = $file['file_name']; // Keep original filename for display
				}
				else {
					unset($this->data['combination_files'][$index]); // Remove file with no content
				}
			}
			usort($this->data['combination_files'], function($a, $b) {
				// Extract the number part from the file name (assuming format like "06120500.txt")
				$numA = intval(preg_replace('/\D/', '', $a['file_name']));
				$numB = intval(preg_replace('/\D/', '', $b['file_name']));
				return $numA - $numB;
			});
	}
	
	// Fetch H-W-C, Followers, and Friends data
	$this->data['h_w_c'] = $this->lottery_data_m->get_h_w_c($id);
	$h_w_c_group = $this->lottery_statistics_m->get_h_w_c_range($id);
	// before passing $h_w_c_group to the view
		$h_w_c_group_options = [];
		foreach ($h_w_c_group as $group) {
			// $group is something like "2-2-2 (17)"
			$value = substr($group, 0, 5); // "2-2-2"
			$h_w_c_group_options[$value] = $group;
		}
	$this->data['h_w_c_group'] = $h_w_c_group_options;
	
	// Fetch extra ball occurrences for independent extra ball lotteries only
	if ($this->data['lottery']->duplicate_extra_ball == 1) {
		$this->data['extra_ball_occurrences'] = $this->lottery_data_m->get_extra_ball_occurrences($id);
		$this->data['is_independent_extra_ball'] = true;
	} else {
		$this->data['extra_ball_occurrences'] = [];
		$this->data['is_independent_extra_ball'] = false;
	}
	
	$this->data['followers'] = $this->lottery_data_m->get_followers($id);
		$this->data['lottery']->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl_name);	// Retrieve the last drawn numbers and draw date
			// 1. Check for a record for the current lottery in the followers table
			$p_group = $this->statistics_m->prize_group_profile($id); // Prize Group Profile Only
			$p_group = $this->statistics_m->prizes_only($p_group,$this->data['lottery']->extra_ball);
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_prizegroup($this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_ball, $p_group); 
			// 2. extract the win record for each number into an array
			$follower_wins = explode(">",$this->data['followers']['wins']);
			$follow_poswins = explode(">",$this->data['followers']['positions']);
			// 3. Only populate the numbers with the win record that was actually drawn
			$this->data['lottery']->last_drawn = $this->history_m->last_draw_addwins($this->data['lottery']->last_drawn, $drawn, $this->data['followers']['extra_included'],$p_group,$follower_wins,$follow_poswins);
			$this->data['lottery']->last_drawn = $this->history_m->last_draw_addpoints($this->data['lottery']->last_drawn, $drawn, $this->data['followers']['extra_included']);
		
		$ball_points = $this->predictions_m->get_sorted_ball_points($this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->duplicate_extra_ball);
		// Example $ball_points_labels = ['7 (142)', '+14 (62)', '12 (88)', ...];
		$ball_points_options = [];
		foreach ($ball_points as $label) {
				// Extract value: if it starts with '+', keep '+', else just the number before space
				if (strpos($label, '+') === 0) {
					$value = substr($label, 0, strpos($label, ' ')); // '+14'
				} else {
					$value = strtok($label, ' '); // '7'
				}
		$ball_points_options[$value] = $label;
		}
		$this->data['ball_points_options'] = $ball_points_options;
		$position_points = $this->lottery_statistics_m->get_sorted_position_points($this->data['lottery']->last_drawn, $drawn);
		$position_points_options = [];
		foreach ($position_points as $label) {
			if (strpos($label, '+') === 0) {
					$value = substr($label, 0, strpos($label, ' ')); // '+14'
				} else {
					$value = strtok($label, ' '); // '7'
				}
				$position_points_options[$value] = $label;
			}
    		$this->data['selected_followers_type'] = 'after_ball'; // or 'position' as your default
		    $this->data['selected_hwc'] = true; // preset value for H-W-C
		    $this->data['selected_followers'] = true; // preset value for Followers
		    $this->data['selected_friends_checkbox'] = true; // preset value for Friends
			$this->data['position_points_options'] = $position_points_options;
			$this->data['combo_id'] = NULL; // Initialize combo_id to NULL
			$this->data['active'] = false; // Initialize active flag to false
			
			// Get all saved combination filters for the user
			$user_id = $this->session->userdata('id');
			$this->data['saved_combinations'] = $this->lottery_data_m->get_all_user_combination_filters($id, $user_id);
			
			$this->data['lottery']->highlights = $this->predictions_m->get_lottery_highlights($id);
			$this->data['lottery']->trends = $this->predictions_m->get_trends($this->data['lottery']->highlights['trends']);
			$this->data['lottery']->winning_digits = $this->predictions_m->get_digit_sums($this->data['lottery']->highlights['winning_digits']);
			$this->data['lottery']->winning_sums = $this->predictions_m->get_sums($this->data['lottery']->highlights['winning_sums']);
			$this->data['lottery']->repeaters = $this->predictions_m->get_repeaters($this->data['lottery']->highlights['repeats']);
			$this->data['lottery']->consecutives = $this->predictions_m->get_consecutives($this->data['lottery']->highlights['consecutives']);
			$this->data['lottery']->parity = $this->predictions_m->get_parity($this->data['lottery']->highlights['parity']);
			// Call get_decade and get_last from predictions_m
			$this->data['lottery']->decades = $this->predictions_m->get_decade($tbl_name, $this->data['lottery']->highlights['range']);
			$this->data['lottery']->last_digits = $this->predictions_m->get_last($tbl_name, $this->data['lottery']->highlights['range']);
			$this->data['lottery']->number_range = $this->predictions_m->get_range($this->data['lottery']->highlights['number_range']);
			$this->data['lottery']->adjacents = $this->predictions_m->get_adjacents($this->data['lottery']->highlights['adjacents']);
			$this->data['friends'] = $this->predictions_m->get_friends($id);
			$this->data['friends_dropdown_options'] = $this->predictions_m->get_friends_dropdown_options($id);
			// Grab the next draw date
			$ld = $this->data['lottery']->last_drawn['draw_date'];	// Return last draw date
			$day = $this->lotteries_m->return_day($ld);				// Returns the day of draw, Saturday, Sunday, etc.
			$this->data['lottery']->next_draw_date = $this->lotteries_m->next_date($this->data['lottery'], $day, $ld);
		// Load the view
		unset($this->data['lottery']->highlights);
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/futures'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->data['is_independent_extra_ball'] = ($this->data['lottery']->duplicate_extra_ball && $this->data['lottery']->extra_ball);
		$this->data['subview'] = 'admin/dashboard/predictions/futures';
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
		$this->data['file'] = $this->combination_files_m->lottery_combination_record($filename);
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
		// Get file_name from POST, URI, or session as fallback
		$file_name = $this->input->post('file', TRUE);
		
		if (empty($file_name)) {
			$file_name = $this->uri->segment(5, NULL);
		}
		
		// If still empty, try to get from session
		if (empty($file_name)) {
			$file_name = $this->session->userdata('current_statistics_file');
		}
		
		// Add validation for file_name
		if (empty($file_name)) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger">No combination file was selected for statistics.</div>');
			redirect('admin/predictions/generate/' . $id);
			return;
		}
		
		// Store the file_name in session for future navigation
		$this->session->set_userdata('current_statistics_file', $file_name);
		
		// Decode the file name
		$pick_per_ticket = (int)substr($file_name, 0, 2); // First two digits - numbers drawn in lottery
		$numbers_to_pick = (int)substr($file_name, 2, 2); // Next two digits - numbers user picks
		
		// Check if this is an independent extra ball file (ends with E)
		$is_extra_ball_file = (substr($file_name, -1) === 'E');
		
		if ($is_extra_ball_file) {
			// Remove the 'E' and extract ticket count
			$tickets = (int)substr($file_name, 4, -1); // Everything after position 4, excluding the 'E'
		} else {
			$tickets = (int)substr($file_name, 4); // Remaining digits - total tickets
		}
		
		// Fetch the lottery details
		$lottery = $this->lotteries_m->get($id);
    	
		// Check if this is an independent extra ball lottery (define early)
		$is_independent_extra_ball = ($lottery->duplicate_extra_ball && $lottery->extra_ball);
		
		// Fetch the prize tiers for the lottery to determine minimum prize match
		$prizes_data = $this->predictions_m->prizes_data_array($id);
		
		// Determine minimum matches needed for a prize (default 2)
		$minimum_prize_match = 2;
		
		// Check for minimum prize match from lottery prizes
		if ($prizes_data) {
			// For independent extra ball lotteries, the logic is different:
			// - Regular matches need to meet the standard minimum (usually 2)
			// - Extra ball combinations (including "Extra only") are always prizes
			if ($is_independent_extra_ball) {
				// For extra ball lotteries, check what the minimum regular match prize is
				if (isset($prizes_data['2_win']) && !is_null($prizes_data['2_win']) && $prizes_data['2_win'] == 1) {
					$minimum_prize_match = 2; // 2 matches alone is a prize
				} elseif (isset($prizes_data['3_win']) && !is_null($prizes_data['3_win']) && $prizes_data['3_win'] == 1) {
					$minimum_prize_match = 3; // 3 matches alone is a prize
				} elseif (isset($prizes_data['4_win']) && !is_null($prizes_data['4_win']) && $prizes_data['4_win'] == 1) {
					$minimum_prize_match = 4; // 4 matches alone is a prize
				} elseif (isset($prizes_data['5_win']) && !is_null($prizes_data['5_win']) && $prizes_data['5_win'] == 1) {
					$minimum_prize_match = 5; // 5 matches alone is a prize
				}
				// Note: 1_win being NULL means "1 match alone" is NOT a prize
				// But "1 + Extra" is still a valid prize, handled separately
			} else {
				// Regular lottery logic
				if (isset($prizes_data['1_win']) && !is_null($prizes_data['1_win']) && $prizes_data['1_win'] == 1) {
					$minimum_prize_match = 1;
				} elseif (isset($prizes_data['2_win']) && !is_null($prizes_data['2_win']) && $prizes_data['2_win'] == 1) {
					$minimum_prize_match = 2;
				} elseif (isset($prizes_data['3_win']) && !is_null($prizes_data['3_win']) && $prizes_data['3_win'] == 1) {
					$minimum_prize_match = 3;
				} elseif (isset($prizes_data['4_win']) && !is_null($prizes_data['4_win']) && $prizes_data['4_win'] == 1) {
					$minimum_prize_match = 4;
				} elseif (isset($prizes_data['5_win']) && !is_null($prizes_data['5_win']) && $prizes_data['5_win'] == 1) {
					$minimum_prize_match = 5;
				}
			}
		}
		
		// Calculate extra ball range for probability calculations
		$extra_ball_range = 1;
		if ($is_independent_extra_ball && isset($lottery->minimum_extra_ball) && isset($lottery->maximum_extra_ball)) {
			$extra_ball_range = ($lottery->maximum_extra_ball - $lottery->minimum_extra_ball) + 1;
		}
		
		// Calculate detailed breakdown using combinatorial mathematics
		$breakdown_data = $this->predictions_m->calculate_detailed_breakdown(
			$numbers_to_pick, 
			$pick_per_ticket, 
			$minimum_prize_match,
			$is_independent_extra_ball,
			$extra_ball_range,
			$tickets  // Pass the actual ticket count from the file
		);
		
		// Remove the incorrect validation message - the filename parsing is correct
		// For independent extra ball files like 050642E.txt: 05=drawn, 06=picked, 42=tickets, E=independent extra ball
		// The ticket count in the filename represents the total tickets in the file, not a calculated expectation
		
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/combo_statistics'.($id ? '/'.$id : '').($file_name ? '/'.$file_name : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->data['lottery'] = $lottery;
		$this->data['file_name'] = $file_name;
		$this->data['pick_per_ticket'] = $pick_per_ticket;
		$this->data['numbers_to_pick'] = $numbers_to_pick;
		$this->data['tickets'] = $tickets;
		$this->data['breakdown_data'] = $breakdown_data;
		$this->data['minimum_prize_match'] = $minimum_prize_match;
		$this->data['is_independent_extra_ball'] = $is_independent_extra_ball;
		$this->data['extra_ball_range'] = $extra_ball_range;
		
		// Add navigation links
		$this->data['back_to_dashboard'] = base_url('admin/predictions');
		$this->data['back_to_combo_list'] = base_url('admin/predictions/combo_select/' . $id);
		
		// Load the detailed breakdown view instead of the old statistics view
		$this->data['subview'] = 'admin/dashboard/predictions/combo_breakdown';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	/**
	 * Display detailed combination breakdown with sub-prize analysis (Legacy method - now redirects to combo_statistics)
	 * Shows winning tickets for different match scenarios using combinatorial mathematics
	 * 
	 * @param int $id The ID of the selected lottery.
	 * @return void
	 */
	public function combo_breakdown($id) {
		// Redirect to combo_statistics which now shows the detailed breakdown
		$file_name = $this->input->post('file', TRUE);
		if (empty($file_name)) {
			$file_name = $this->uri->segment(5, NULL);
		}
		redirect('admin/predictions/combo_statistics/' . $id . '/' . $file_name);
	}
	/**
	 * Activate the link, if there are combo files waiting to be generated
	 * 
	 * @param       int		$result			Number of Combination Files for a pick lottery
	 * @return      boolean TRUE / FALSE	True on Combination Files in the DB or FALSE that there is no record of the combination files.
	 */
	public function active($result) 
	{
		return ($result > 0 ? TRUE : FALSE);
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
	/**
     * Fetches the list of countries for the first dropdown.
     * @parm   none  
     * @return string $countries Outputs string abreviation of Country
     */
    public function get_countries($id)
    {
        $countries = $this->lottery_data_m->get_countries($id);
	return $countries;
    }
	 /**
     * Fetches the list of provinces/states based on the selected country.
     *
     * @param int $country_id The ID of the selected country.
     * @return  Outputs a JSON-encoded array of provinces/states.
     */
    public function get_prov_states($country_id)
    {
        $prov_state = $this->lottery_data_m->get_prov_states($country_id);
 	return $prov_state;
	}
	/**
     * Fetches the list of lottery games based on the selected country and province/state.
     *
     * @param int $country_id The ID of the selected country.
     * @param string $province_id The ID of the selected province/state or "ALL" for country-wide lotteries.
     * @return void Outputs a JSON-encoded array of lottery games.
     */
    public function get_lottery_games($country_id, $province_id)
    {
        $lottery_games = $this->lottery_data_m->get_lottery_games($country_id, $province_id);
        echo json_encode($lottery_games);
    }
	/**
     * Fetches the list of wheeling tables based on the selected lottery game.
     *
     * @param int $lottery_id The ID of the selected lottery game.
     * @return void Outputs a JSON-encoded array of wheeling tables.
     */
    public function get_wheeling_tables($lottery_id)
    {
        $wheeling_table = $this->predictions_m->get_wheeling_tables($lottery_id);
        echo json_encode($wheeling_table);
    }
	/**
	 * Reset all futures form settings and clear session data
	 *
	 * @param int $id The ID of the selected lottery.
	 * @return void Redirects back to futures page with cleared settings.
	 */
	public function reset_settings($id)
	{
		// Clear all futures-related session data first
		if ($this->session->userdata('futures_form')) {
			$this->session->unset_userdata('futures_form');
		}
		if ($this->session->userdata('futures_number_array')) {
			$this->session->unset_userdata('futures_number_array');
		}
		if ($this->session->userdata('combination_file_name')) {
			$this->session->unset_userdata('combination_file_name');
			$this->session->unset_userdata('combination_file_id');
		}
		// Now prepare default data similar to futures method
		$this->data['message'] = 'Settings have been reset successfully.';
		$this->data['disable_generate_button'] = true; // Used to disable the generate button in the view
		$this->data['lottery'] = $this->lotteries_m->get($id);
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		$drawn = $this->data['lottery']->balls_drawn;
	$this->data['country_code'] = $this->lottery_data_m->get_lottery_country($id);
	$this->data['state_prov_code'] = $this->lottery_data_m->get_lottery_state_prov($id);
	// Fetch combination files for the lottery
	$this->data['combination_files'] = $this->predictions_m->get_combination_files($id);
	
	// Sort combination files numerically like in the futures method
	if (!empty($this->data['combination_files'])) {
		usort($this->data['combination_files'], function($a, $b) {
			$numA = intval(preg_replace('/\D/', '', $a['file_name']));
			$numB = intval(preg_replace('/\D/', '', $b['file_name']));
			return $numA - $numB;
		});
	}
		// Fetch H-W-C, Followers, and Friends data
		$this->data['h_w_c'] = $this->predictions_m->get_h_w_c($id);
		$h_w_c_group = $this->predictions_m->get_h_w_c_range($id);
		$h_w_c_group_options = [];
		foreach ($h_w_c_group as $group) {
			$value = substr($group, 0, 5);
			$h_w_c_group_options[$value] = $group;
		}
		$this->data['h_w_c_group'] = $h_w_c_group_options;
		$this->data['followers'] = $this->predictions_m->get_followers($id);
		// Prepare lottery data for points calculations
		$this->data['lottery']->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl_name);
		$p_group = $this->statistics_m->prize_group_profile($id);
		$p_group = $this->statistics_m->prizes_only($p_group, $this->data['lottery']->extra_ball);
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_prizegroup($this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_ball, $p_group);
		$follower_wins = explode(">", $this->data['followers']['wins']);
		$follow_poswins = explode(">", $this->data['followers']['positions']);
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_addwins($this->data['lottery']->last_drawn, $drawn, $this->data['followers']['extra_included'], $p_group, $follower_wins, $follow_poswins);
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_addpoints($this->data['lottery']->last_drawn, $drawn, $this->data['followers']['extra_included']);
	// Ball points and position points setup
	$ball_points = $this->predictions_m->get_sorted_ball_points($this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->duplicate_extra_ball);
	$ball_points_options = [];
	foreach ($ball_points as $label) {
		if (strpos($label, '+') === 0) {
			$value = substr($label, 0, strpos($label, ' '));
		} else {
			$value = strtok($label, ' ');
		}
		$ball_points_options[$value] = $label;
	}
	$this->data['ball_points_options'] = $ball_points_options;
	
	$position_points = $this->lottery_statistics_m->get_sorted_position_points($this->data['lottery']->last_drawn, $drawn);
	$position_points_options = [];
		foreach ($position_points as $label) {
			if (strpos($label, '+') === 0) {
				$value = substr($label, 0, strpos($label, ' '));
			} else {
				$value = strtok($label, ' ');
			}
			$position_points_options[$value] = $label;
		}
		$this->data['position_points_options'] = $position_points_options;
		// Set all the DEFAULT values (same as futures method)
		$this->data['selected_followers_type'] = 'after_ball'; // Default followers type
		$this->data['selected_hwc'] = true; // Default H-W-C checkbox checked
		$this->data['selected_followers'] = true; // Default Followers checkbox checked
		$this->data['selected_friends_checkbox'] = true; // Default Friends checkbox checked
		// Set empty/default values for dropdowns and selections
		$this->data['selected_h_w_c_group'] = ''; // No H-W-C group selected by default
		$this->data['selected_ball_points'] = ''; // No ball points selected by default
		$this->data['selected_position_points'] = ''; // No position points selected by default
		$this->data['selected_friends'] = ''; // No friends selected by default
		$this->data['selected_wheeling'] = ''; // No combination file selected by default
		// Default all filter selections to empty (no filters applied)
		$this->data['selected_trends'] = '';
		$this->data['selected_winning_sums'] = '';
		$this->data['selected_winning_digits'] = '';
		$this->data['selected_repeaters'] = '';
		$this->data['selected_consecutives'] = '';
		$this->data['selected_parity'] = '';
		$this->data['selected_decades'] = '';
		$this->data['selected_last_digits'] = '';
		$this->data['selected_number_range'] = '';
		$this->data['selected_adjacents'] = '';
		// Get lottery highlights and historical data for filters
		$this->data['lottery']->highlights = $this->predictions_m->get_lottery_highlights($id);
		$this->data['lottery']->trends = $this->predictions_m->get_trends($this->data['lottery']->highlights['trends']);
		$this->data['lottery']->winning_digits = $this->predictions_m->get_digit_sums($this->data['lottery']->highlights['winning_digits']);
		$this->data['lottery']->winning_sums = $this->predictions_m->get_sums($this->data['lottery']->highlights['winning_sums']);
		$this->data['lottery']->repeaters = $this->predictions_m->get_repeaters($this->data['lottery']->highlights['repeats']);
		$this->data['lottery']->consecutives = $this->predictions_m->get_consecutives($this->data['lottery']->highlights['consecutives']);
		$this->data['lottery']->parity = $this->predictions_m->get_parity($this->data['lottery']->highlights['parity']);
		$this->data['lottery']->decades = $this->predictions_m->get_decade($tbl_name, $this->data['lottery']->highlights['range']);
		$this->data['lottery']->last_digits = $this->predictions_m->get_last($tbl_name, $this->data['lottery']->highlights['range']);
		$this->data['lottery']->number_range = $this->predictions_m->get_range($this->data['lottery']->highlights['number_range']);
		$this->data['lottery']->adjacents = $this->predictions_m->get_adjacents($this->data['lottery']->highlights['adjacents']);
		$this->data['friends'] = $this->predictions_m->get_friends($id);
		$this->data['friends_dropdown_options'] = $this->predictions_m->get_friends_dropdown_options($id);
		$this->data['combo_id'] = NULL; // Reset combo_id to NULL
		// Get next draw date
		$ld = $this->data['lottery']->last_drawn['draw_date'];
		$day = $this->lotteries_m->return_day($ld);
		$this->data['lottery']->next_draw_date = $this->lotteries_m->next_date($this->data['lottery'], $day, $ld);
		
		// Get all saved combination filters for the user - ensure this is always available
		$user_id = $this->session->userdata('id');
		$this->data['saved_combinations'] = $this->lottery_data_m->get_all_user_combination_filters($id, $user_id);
		
		// Load the view with reset defaults
		unset($this->data['lottery']->highlights);
		$this->data['current'] = $this->uri->segment(2);
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/futures');
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);
		$this->data['admins'] = $this->maintenance_m->logged_online(1);
		$this->data['visitors'] = $this->maintenance_m->active_visitors();
		$this->data['subview'] = 'admin/dashboard/predictions/futures';
		$this->load->view('admin/_layout_main', $this->data);
	}
	/**
     * Handles the Combination Table and filter validation and loading.
     *
     * This method validates that the form has posted values from the futures view.
     * If the combination file (from the 'wheeling' POST value) does not exist in the combinations directory,
     * it sets an error message. If the file exists, it loads the posted values for
     * Combination Table, H-W-C group, Followers, and Friends for further processing.
     *
     * @param int $id The ID of the selected lottery.
     * @return void Loads the appropriate view with error or success message and posted values.
     */
    public function combination($id)
	{
		$this->data['message'] = '';
		$this->data['disable_generate_button'] = false; // Used to disable the generate button in the view
		
		// Fetch lottery and related data
		$this->data['lottery'] = $this->lotteries_m->get($id);
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		$drawn = $this->data['lottery']->balls_drawn;
		$this->data['country_code'] = $this->lottery_data_m->get_lottery_country($id);
		$this->data['state_prov_code'] = $this->lottery_data_m->get_lottery_state_prov($id);
		$this->data['is_independent_extra_ball'] = ($this->data['lottery']->duplicate_extra_ball && $this->data['lottery']->extra_ball);
		
		// Fetch extra ball occurrences for independent extra ball lotteries
		if ($this->data['lottery']->duplicate_extra_ball == 1) {
			$this->data['extra_ball_occurrences'] = $this->lottery_data_m->get_extra_ball_occurrences($id);
		} else {
			$this->data['extra_ball_occurrences'] = [];
		}
		$this->data['combination_files'] = $this->predictions_m->get_combination_files($id);
		
		// Sort combination files numerically
		if (!empty($this->data['combination_files'])) {
			usort($this->data['combination_files'], function($a, $b) {
				$numA = intval(preg_replace('/\D/', '', $a['file_name']));
				$numB = intval(preg_replace('/\D/', '', $b['file_name']));
				return $numA - $numB;
			});
		}
		
		$this->data['h_w_c'] = $this->predictions_m->get_h_w_c($id);
		$this->data['followers'] = $this->predictions_m->get_followers($id);
		$this->data['friends'] = $this->predictions_m->get_friends($id);
		$this->data['friends_dropdown_options'] = $this->predictions_m->get_friends_dropdown_options($id);

		// Prepare dropdown options
		$h_w_c_group = $this->predictions_m->get_h_w_c_range($id);
		$h_w_c_group_options = [];
		foreach ($h_w_c_group as $group) {
			$value = substr($group, 0, 5);
			$h_w_c_group_options[$value] = $group;
		}
		$this->data['h_w_c_group'] = $h_w_c_group_options;
		$this->data['lottery']->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl_name);
		$p_group = $this->statistics_m->prize_group_profile($id);
		$p_group = $this->statistics_m->prizes_only($p_group, $this->data['lottery']->extra_ball);
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_prizegroup($this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_ball, $p_group);
		$follower_wins = explode(">", $this->data['followers']['wins']);
		$follow_poswins = explode(">", $this->data['followers']['positions']);
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_addwins($this->data['lottery']->last_drawn, $drawn, $this->data['followers']['extra_included'], $p_group, $follower_wins, $follow_poswins);
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_addpoints($this->data['lottery']->last_drawn, $drawn, $this->data['followers']['extra_included']);
		// Ball points and position points
		$ball_points = $this->predictions_m->get_sorted_ball_points($this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->duplicate_extra_ball);
		$ball_points_options = [];
		foreach ($ball_points as $label) {
			$value = (strpos($label, '+') === 0) ? substr($label, 0, strpos($label, ' ')) : strtok($label, ' ');
			$ball_points_options[$value] = $label;
		}
		$this->data['ball_points_options'] = $ball_points_options;
		$position_points = $this->lottery_statistics_m->get_sorted_position_points($this->data['lottery']->last_drawn, $drawn);
		$position_points_options = [];
		foreach ($position_points as $label) {
			$value = (strpos($label, '+') === 0) ? substr($label, 0, strpos($label, ' ')) : strtok($label, ' ');
			$position_points_options[$value] = $label;
		}
		$this->data['position_points_options'] = $position_points_options;
		// Pagination setup
		$page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
		$per_page = $this->input->post('per_page') ?: $this->input->get('per_page');
		if (!$per_page) $per_page = 10;
		// --- POST: Generate and Save Everything to Session ---
		if ($this->input->method() === 'post') {
			// Check if futures_form session is set
			$session_data = $this->session->userdata('futures_form');
			$combination_file_value = (isset($session_data) ? $this->session->userdata('combination_file_name') : $this->input->post('wheeling', TRUE));
			// Parse the combination file value to extract ID and filename
			if (strpos($combination_file_value, '|') !== false) {
				list($combo_id, $combination_file) = explode('|', $combination_file_value, 2);
				$combo_id = (int)$combo_id;
			} else {
				// Fallback for old format (just filename)
				$combination_file = $combination_file_value;
				$combo_id = $this->combination_files_m->get_combination_id($combination_file);
			}
			// Store both values in session
			$this->session->set_userdata('combination_file_id', $combo_id);
			$this->session->set_userdata('combination_file_name', $combination_file);
			if ($session_data) {
				// Use POST values if available, otherwise fall back to session values
				// For checkboxes, if form was submitted but checkbox not present in POST, it means unchecked
				$hwc_checked = ($this->input->post() && !$this->input->post('hwc')) ? false : ($this->input->post('hwc') ? (($this->input->post('hwc') == '1') ? true : false) : $session_data['selected_hwc']);
				$followers_checked = ($this->input->post() && !$this->input->post('followers')) ? false : ($this->input->post('followers') ? (($this->input->post('followers') == '1') ? true : false) : $session_data['selected_followers']);
				$friends_checked = ($this->input->post() && !$this->input->post('friends')) ? false : ($this->input->post('friends') ? (($this->input->post('friends') == '1') ? true : false) : $session_data['selected_friends_checkbox']);
 				$h_w_c_group = ($this->input->post('h_w_c_group') ? $this->input->post('h_w_c_group') : $this->session->userdata('selected_h_w_c_group'));
				$selected_extra_ball = ($this->input->post('extra_ball_filter') ? $this->input->post('extra_ball_filter') : $this->session->userdata('selected_extra_ball'));
				// Set default value for extra ball filter if not set (for regular lotteries)
				if (empty($selected_extra_ball)) {
					$selected_extra_ball = 'ALL';
				}
				$followers_type = ($this->input->post('followers_type') ? $this->input->post('followers_type') : $this->session->userdata('selected_followers_type'));
				$selected_ball_points = ($this->input->post('ball_points') ? $this->input->post('ball_points') : $this->session->userdata('selected_ball_points'));
				$selected_position_points = ($this->input->post('position_points') ? $this->input->post('position_points') : $this->session->userdata('selected_position_points'));
				$selected_friends = ($this->input->post('friends_select') ? $this->input->post('friends_select') : $this->session->userdata('selected_friends'));
				// Actual Win Filtering History
				$selected_trends = $this->input->post('trends', TRUE);
				$selected_winning_sums = $this->input->post('winning_sums', TRUE);
				$selected_winning_digits = $this->input->post('winning_digits', TRUE);
				$selected_repeaters = $this->input->post('repeaters', TRUE);
				$selected_consecutives = $this->input->post('consecutives', TRUE);
				$selected_parity = $this->input->post('parity', TRUE);
				$selected_decades = $this->input->post('decades', TRUE);
				$selected_last_digits = $this->input->post('last_digits', TRUE);
				$selected_number_range = $this->input->post('number_range', TRUE);
				$selected_adjacents = $this->input->post('adjacents', TRUE);
				$session_data = [
					'selected_h_w_c_group'      => $h_w_c_group,
					'selected_extra_ball'       => $selected_extra_ball,
					'selected_followers_type'   => $followers_type,
					'selected_ball_points'      => $selected_ball_points,
					'selected_position_points'  => $selected_position_points,
					'selected_friends'          => $selected_friends,
					'selected_hwc'              => $hwc_checked,
					'selected_followers'        => $followers_checked,
					'selected_friends_checkbox' => $friends_checked,
					'selected_wheeling' 		=> $combination_file,
					'selected_trends'          	=> $selected_trends,
					'selected_winning_sums'     => $selected_winning_sums,
					'selected_winning_digits' 	=> $selected_winning_digits,
					'selected_repeaters' 		=> $selected_repeaters,
					'selected_consecutives' 	=> $selected_consecutives,
					'selected_parity'          	=> $selected_parity,
					'selected_decades'          => $selected_decades,
					'selected_last_digits'      => $selected_last_digits,
					'selected_number_range' 	=> $selected_number_range,
					'selected_adjacents' 		=> $selected_adjacents,
					'selected_combo_id'         => $combo_id, // Add combo_id to session
				];
				$this->session->set_userdata('futures_form', $session_data);
				$this->data['disable_combination_dropdown'] = true;
			} else {
				// Get all POST values and save to session for future pagination
				$hwc_checked = ($this->input->post('hwc') == '1') ? true : false;
				$followers_checked = ($this->input->post('followers') == '1') ? true : false;
				$friends_checked = ($this->input->post('friends') == '1') ? true : false;
				
				$h_w_c_group = $this->input->post('h_w_c_group', TRUE);
				$selected_extra_ball = $this->input->post('extra_ball_filter', TRUE);
				// Set default value for extra ball filter if not set (for regular lotteries)
				if (empty($selected_extra_ball)) {
					$selected_extra_ball = 'ALL';
				}
				$followers_type = $this->input->post('followers_type', TRUE);
				$selected_ball_points = $this->input->post('ball_points', TRUE);
				$selected_position_points = $this->input->post('position_points', TRUE);
				$selected_friends = $this->input->post('friends_select', TRUE);
				// Actual Win Filtering History
				$selected_trends = $this->input->post('trends', TRUE);
				$selected_winning_sums = $this->input->post('winning_sums', TRUE);
				$selected_winning_digits = $this->input->post('winning_digits', TRUE);
				$selected_repeaters = $this->input->post('repeaters', TRUE);
				$selected_consecutives = $this->input->post('consecutives', TRUE);
				$selected_parity = $this->input->post('parity', TRUE);
				$selected_decades = $this->input->post('decades', TRUE);
				$selected_last_digits = $this->input->post('last_digits', TRUE);
				$selected_number_range = $this->input->post('number_range', TRUE);
				$selected_adjacents = $this->input->post('adjacents', TRUE);
				// If combination table is posted and not in session, set and lock it
				if ($this->input->post('wheeling')) {
					$futures_form['selected_wheeling'] = $this->input->post('wheeling');
					$this->data['disable_combination_dropdown'] = true;
				} else {
					$this->data['disable_combination_dropdown'] = !empty($futures_form['selected_wheeling']);
				}
				// Always enable Generate and Save Filtered Tickets
				$this->data['enable_generate_button'] = true;
				$this->data['enable_save_filtered_button'] = true;
				$session_data = [
					'selected_h_w_c_group'      => $h_w_c_group,
					'selected_extra_ball'       => $selected_extra_ball,
					'selected_followers_type'   => $followers_type,
					'selected_ball_points'      => $selected_ball_points,
					'selected_position_points'  => $selected_position_points,
					'selected_friends'          => $selected_friends,
					'selected_hwc'              => $hwc_checked,
					'selected_followers'        => $followers_checked,
					'selected_friends_checkbox' => $friends_checked,
					'selected_wheeling' 		=> $combination_file,
					'selected_trends'          	=> $selected_trends,
					'selected_winning_sums'     => $selected_winning_sums,
					'selected_winning_digits' 	=> $selected_winning_digits,
					'selected_repeaters' 		=> $selected_repeaters,
					'selected_consecutives' 	=> $selected_consecutives,
					'selected_parity'          	=> $selected_parity,
					'selected_decades'          => $selected_decades,
					'selected_last_digits'      => $selected_last_digits,
					'selected_number_range' 	=> $selected_number_range,
					'selected_adjacents' 		=> $selected_adjacents,
					'selected_combo_id'         => $combo_id, // Add combo_id to session
				];
				$this->session->set_userdata('futures_form', $session_data);
			}
				 // LOTTERY PROFILE STATISTICS PRESETS Settings
				$this->data['selected_h_w_c_group'] = $h_w_c_group;				// H - W- C Group Selected
				$this->data['selected_extra_ball'] = $selected_extra_ball;		// Extra Ball Filter Selected
				$this->data['selected_followers_type'] = $followers_type;	  	// or 'position' as your default
				$this->data['selected_hwc'] = $hwc_checked; 					// preset value for H-W-C
				$this->data['selected_followers'] = $followers_checked; 		// preset value for Followers
				$this->data['selected_friends_checkbox'] = $friends_checked; 	// preset value for Friends
				$this->data['selected_friends'] = $selected_friends; 			// preset value for Friends choices
				$this->data['selected_wheeling'] = $combination_file; 			// preset value for the Combination File (wheeling file)
				$this->data['combo_id'] = ($this->lottery_data_m->validate_combo_id($combo_id) ? $combo_id : NULL);
				$this->data['active'] = $this->combination_filters_m->get_active_flag($combo_id);	
				
				// Get filter record ID for Prize controller navigation
				// This should work for both active and expired filters
				$this->data['filter_record_id'] = NULL;
				if ($combo_id) {
					// Look up the filter record by combo_id to get the actual filter ID
					// This works regardless of active/expired status
					$saved_settings = $this->combination_filters_m->get_saved_settings($combo_id);
					if ($saved_settings && isset($saved_settings['id'])) {
						$this->data['filter_record_id'] = $saved_settings['id'];
						log_message('info', "Predictions::combination - Found filter_record_id: {$saved_settings['id']} for combo_id: {$combo_id}, active: " . ($this->data['active'] ? 'true' : 'false'));
					} else {
						log_message('info', "Predictions::combination - No saved settings found for combo_id: {$combo_id}");
					}
				}
				
				// Get filename and CCCC data for futures view
				if ($combo_id && $this->data['combo_id']) {
					$filename_cccc_data = $this->lottery_data_m->get_combination_filename_cccc($combo_id);
					if ($filename_cccc_data) {
						$this->data['file_name'] = $filename_cccc_data['file_name'];
						$this->data['CCCC'] = $filename_cccc_data['CCCC'];
					}
				}
				$this->data['selected_ball_points'] = $selected_ball_points;
				$this->data['selected_position_points'] = $selected_position_points;
				//Actual Win History Filtering
				$this->data['selected_trends'] = $selected_trends; 					// trends setting
				$this->data['selected_winning_sums'] = $selected_winning_sums;  	// sums setting
				$this->data['selected_winning_digits'] = $selected_winning_digits; 	// digit sums setting
				$this->data['selected_repeaters'] = $selected_repeaters; 			// repeaters setting
				$this->data['selected_consecutives'] = $selected_consecutives;  	// consecutives setting
				$this->data['selected_parity'] = $selected_parity;					// parity (odd / even) setting
				$this->data['selected_decades'] = $selected_decades;				// decades setting
				$this->data['selected_last_digits'] = $selected_last_digits; 		// last digits setting
				$this->data['selected_number_range'] = $selected_number_range;		// number range setting
				$this->data['selected_adjacents'] = $selected_adjacents;			// adjacents setting
			// Extract number of selections from combination_file (3rd and 4th digits)
			$selections = (int)substr($combination_file, 2, 2);
			$this->data['enable_generate_button'] = true; 		// or false
			// Generate number series based on selections
			$number_series = '';
			
			if (!$hwc_checked && !$followers_checked) {
				$this->data['message'] = 'Either Hot - Warm - Cold checkbox or Follower checkbox predictions can be unchecked but not both.';
				
				// Preserve form values so user can make corrections
				$this->data['selected_h_w_c_group'] = $h_w_c_group;				// H - W- C Group Selected
				$this->data['selected_followers_type'] = $followers_type;	  	// or 'position' as your default
				$this->data['selected_hwc'] = $hwc_checked; 					// preset value for H-W-C
				$this->data['selected_followers'] = $followers_checked; 		// preset value for Followers
				$this->data['selected_friends_checkbox'] = $friends_checked; 	// preset value for Friends
				$this->data['selected_friends'] = $selected_friends; 			// preset value for Friends choices
				$this->data['selected_wheeling'] = $combination_file; 			// preset value for the Combination File (wheeling file)
				$this->data['combo_id'] = ($this->lottery_data_m->validate_combo_id($combo_id) ? $combo_id : NULL);
				$this->data['active'] = $this->combination_filters_m->get_active_flag($combo_id);	
				
				// Get filter record ID for Prize controller navigation
				$this->data['filter_record_id'] = NULL;
				if ($combo_id) {
					$saved_settings = $this->combination_filters_m->get_saved_settings($combo_id);
					if ($saved_settings && isset($saved_settings['id'])) {
						$this->data['filter_record_id'] = $saved_settings['id'];
					}
				}
				
				// Get filename and CCCC data for futures view
				if ($combo_id && $this->data['combo_id']) {
					$filename_cccc_data = $this->lottery_data_m->get_combination_filename_cccc($combo_id);
					if ($filename_cccc_data) {
						$this->data['file_name'] = $filename_cccc_data['file_name'];
						$this->data['CCCC'] = $filename_cccc_data['CCCC'];
					}
				}
				$this->data['selected_ball_points'] = $selected_ball_points;
				$this->data['selected_position_points'] = $selected_position_points;
				//Actual Win History Filtering
				$this->data['selected_trends'] = $selected_trends; 					// trends setting
				$this->data['selected_winning_sums'] = $selected_winning_sums;  	// sums setting
				$this->data['selected_winning_digits'] = $selected_winning_digits; 	// digit sums setting
				$this->data['selected_repeaters'] = $selected_repeaters; 			// repeaters setting
				$this->data['selected_consecutives'] = $selected_consecutives;  	// consecutives setting
				$this->data['selected_parity'] = $selected_parity;					// parity (odd / even) setting
				$this->data['selected_decades'] = $selected_decades;				// decades setting
				$this->data['selected_last_digits'] = $selected_last_digits; 		// last digits setting
				$this->data['selected_number_range'] = $selected_number_range;		// number range setting
				$this->data['selected_adjacents'] = $selected_adjacents;			// adjacents setting
				
				// Load necessary lottery data for the view to work properly
				$this->data['lottery'] = $this->lotteries_m->get($id);
				$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
				$drawn = $this->data['lottery']->balls_drawn;
				
				// Get lottery highlights and historical data for filters
				$this->data['lottery']->highlights = $this->predictions_m->get_lottery_highlights($id);
				$this->data['lottery']->trends = $this->predictions_m->get_trends($this->data['lottery']->highlights['trends']);
				$this->data['lottery']->winning_digits = $this->predictions_m->get_digit_sums($this->data['lottery']->highlights['winning_digits']);
				$this->data['lottery']->winning_sums = $this->predictions_m->get_sums($this->data['lottery']->highlights['winning_sums']);
				$this->data['lottery']->repeaters = $this->predictions_m->get_repeaters($this->data['lottery']->highlights['repeats']);
				$this->data['lottery']->consecutives = $this->predictions_m->get_consecutives($this->data['lottery']->highlights['consecutives']);
				$this->data['lottery']->parity = $this->predictions_m->get_parity($this->data['lottery']->highlights['parity']);
				$this->data['lottery']->decades = $this->predictions_m->get_decade($tbl_name, $this->data['lottery']->highlights['range']);
				$this->data['lottery']->last_digits = $this->predictions_m->get_last($tbl_name, $this->data['lottery']->highlights['range']);
				$this->data['lottery']->number_range = $this->predictions_m->get_range($this->data['lottery']->highlights['number_range']);
				$this->data['lottery']->adjacents = $this->predictions_m->get_adjacents($this->data['lottery']->highlights['adjacents']);
				
				// Add friends data (essential for the form to work)
				$this->data['friends'] = $this->predictions_m->get_friends($id);
				$this->data['friends_dropdown_options'] = $this->predictions_m->get_friends_dropdown_options($id);
				
				// Get next draw date
				$this->data['lottery']->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl_name);
				$ld = $this->data['lottery']->last_drawn['draw_date'];
				$day = $this->lotteries_m->return_day($ld);
				$this->data['lottery']->next_draw_date = $this->lotteries_m->next_date($this->data['lottery'], $day, $ld);
				
				// Get all saved combination filters for the user - ensure this is always available
				$user_id = $this->session->userdata('id');
				$this->data['saved_combinations'] = $this->lottery_data_m->get_all_user_combination_filters($id, $user_id);
				
				// Set default values and display the form with error message
				$this->data['combos_paginated'] = [];
				$this->data['pagination'] = [
					'current' => 1,
					'total' => 1,
					'per_page' => $per_page,
					'total_records' => 0
				];
				
				// Clean up and load view with all necessary data
				unset($this->data['lottery']->highlights);
				$this->data['current'] = $this->uri->segment(2);
				$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/futures'.'/'.$id);
				$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
				$this->data['users'] = $this->maintenance_m->logged_online(0);
				$this->data['admins'] = $this->maintenance_m->logged_online(1);
				$this->data['visitors'] = $this->maintenance_m->active_visitors();
				$this->data['predictions'] = $this;	 // Access the methods in the view (essential for form functionality)
				$this->data['subview'] = 'admin/dashboard/predictions/futures';
				$this->load->view('admin/_layout_main', $this->data);
				return;
			} elseif ($hwc_checked && !$followers_checked) {
				$number_series = $this->predictions_m->hwc_only($id, $selections, $h_w_c_group);
				if(!$number_series) { 
					$this->session->set_flashdata('message', 'Could not return a series of numbers for inserting in the Combination Tickets File.');
					redirect('admin/predictions');
				}
			} elseif (!$hwc_checked && $followers_checked) {
				$follower_select = ($followers_type === 'after_ball') ? $selected_ball_points : $selected_position_points;
				$number_series = $this->predictions_m->followers_only($id, $selections, $followers_type, $follower_select);
				if(!$number_series) { 
					$this->session->set_flashdata('message', 'Could not return a series of numbers for inserting in the Combination Tickets File.');
					redirect('admin/predictions');
				}
			} elseif ($hwc_checked && $followers_checked) {
				$follower_select = ($followers_type == 'after_ball') ?  $selected_ball_points : $selected_position_points;
				$number_series = $this->predictions_m->hwc_followers($id, $selections, $h_w_c_group, $followers_type, $follower_select);
				if(!$number_series) { 
					$this->session->set_flashdata('message', 'Could not return a series of numbers for inserting in the Combination Tickets File.');
					redirect('admin/predictions');
				}
			} 
			// Friends logic
			$friendship_warning = null; // Initialize friendship warning message
			if ($friends_checked) {
				if ($selected_friends !== 'all') {
					// Ensure $number_series is valid before processing
					if (empty($number_series)) {
						$this->session->set_flashdata('message', 'No number series available for Friends processing. Please select H-W-C or Followers first.');
						redirect('admin/predictions');
					}
					$numbers = array_values(array_filter(array_map('trim', explode(',', $number_series))));
					array_unshift($numbers, null);
					unset($numbers[0]);
					
					if($hwc_checked && !$followers_checked) {
						// H-W-C only with Friends
						$heat_map = $this->predictions_m->get_heat_map($id);
						if(empty($heat_map)) { 
							$this->session->set_flashdata('message', 'Problem with the Heat Map, please try again.');
							redirect('admin/predictions');
						}
						$result = $this->predictions_m->friend_search_hwc_with_status($id, $numbers, $selected_friends, $heat_map);
						$numbers = $result['numbers'];
						$friendship_warning = $result['friendship_status']['warning_message'];
					} elseif(!$hwc_checked && $followers_checked) {
						// Followers only with Friends - use friends_only method for consistency
						$result = $this->predictions_m->friends_only_with_status($id, $numbers, $selected_friends);
						$numbers = $result['numbers'];
						$friendship_warning = $result['friendship_status']['warning_message'];
					} elseif($hwc_checked && $followers_checked) {
						// Both H-W-C and Followers with Friends - use H-W-C method
						$heat_map = $this->predictions_m->get_heat_map($id);
						if(empty($heat_map)) { 
							$this->session->set_flashdata('message', 'Problem with the Heat Map, please try again.');
							redirect('admin/predictions');
						}
						$result = $this->predictions_m->friend_search_hwc_with_status($id, $numbers, $selected_friends, $heat_map);
						$numbers = $result['numbers'];
						$friendship_warning = $result['friendship_status']['warning_message'];
					} elseif(!$hwc_checked && !$followers_checked) {
						// Friends-only processing
						$result = $this->predictions_m->friends_only_with_status($id, $numbers, $selected_friends);
						$numbers = $result['numbers'];
						$friendship_warning = $result['friendship_status']['warning_message'];
					}
					
					// Ensure $numbers is a valid array before processing
					if (is_array($numbers) && !empty($numbers)) {
						$numbers = array_values($numbers); // Re-index the array from index 1 to index 0
						$number_series = implode(',', $numbers);
					} else {
						// Handle case where numbers is null or empty
						$number_series = '';
					}
				}
			}
			// Validate combination file
			$combinations_dir = FCPATH . 'combinations/';
			$filename = basename($combination_file);
			$filepath = $combinations_dir . $filename . '.txt';
			if (empty($combination_file) || !file_exists($filepath)) {
				$this->data['message'] = 'The selected Combination Table file does not exist.';
				$this->data['combos_paginated'] = [];
				$this->data['pagination'] = [
					'current' => 1,
					'total' => 1,
					'per_page' => $per_page,
					'total_filtered' => 0
				];
			} else {
				// Prepare number array and updated combinations
				$number_array = array_map('intval', explode(',', $number_series));
				$this->session->set_userdata('futures_number_array', $number_array);
				// Load lottery highlights for filtering
				if (!isset($this->data['lottery']->highlights)) {
					$this->data['lottery']->highlights = $this->predictions_m->get_lottery_highlights($id);
				}
				// Prepare filter array
				$filters = [
					'selected_trends' => $selected_trends,
					'selected_winning_sums' => $selected_winning_sums,
					'selected_winning_digits' => $selected_winning_digits,
					'selected_repeaters' => $selected_repeaters,
					'selected_consecutives' => $selected_consecutives,
					'selected_parity' => $selected_parity,
					'selected_decades' => $selected_decades,
					'selected_last_digits' => $selected_last_digits,
					'selected_number_range' => $selected_number_range,
					'selected_adjacents' => $selected_adjacents,
					'selected_extra_ball' => $selected_extra_ball,
					'drawn' => $drawn,
					'lottery_last_drawn' => $this->data['lottery']->last_drawn,
					'extra_ball' => $this->data['lottery']->extra_ball,
					'duplicate_extra_ball' => $this->data['lottery']->duplicate_extra_ball,
					'max_ball' => $this->data['lottery']->maximum_ball,
					'lottery_highlights' => $this->data['lottery']->highlights
				];
				$combos_paginated = $this->predictions_m->insert_number_combination($filepath, $number_array, $page, $per_page, $filters);
				// Check if any combinations were found
				if (empty($combos_paginated)) {
					$this->data['message'] = 'No Combinations are available with the applied filters';
					$this->data['combos_paginated'] = [];
					$this->data['pagination'] = [
						'current' => 1,
						'total' => 1,
						'per_page' => $per_page,
						'total_filtered' => 0
					];
				} else {
					$this->data['combos_paginated'] = $combos_paginated;
					// Paginate for display - use filtered count for accurate pagination
					$total_filtered = $this->predictions_m->get_filtered_combinations_count($filepath, $number_array, $filters);
					$this->data['pagination'] = [
						'current' => $page,
						'total' => ceil($total_filtered / $per_page),
						'per_page' => $per_page,
						'total_filtered' => $total_filtered  // Add total filtered count
					];
					$this->data['number_array'] = $number_array;
					$this->data['message'] = 'Combination Table and filters loaded successfully.';
				}
			}
		}
		// --- GET:   ---
		else {
			// Restore form/filter values
			$futures_form = $this->session->userdata('futures_form');
				foreach ($futures_form as $key => $value) {
					$this->data[$key] = $value;
				}
			// LOTTERY PROFILE STATISTICS PRESETS Settings
				$this->data['selected_h_w_c_group'] = $futures_form['selected_h_w_c_group'];
				$this->data['selected_hwc'] = $futures_form['selected_hwc']; 
				$this->data['selected_followers'] = $futures_form['selected_followers']; 
				$this->data['selected_friends_checkbox'] = $futures_form['selected_friends_checkbox']; 
				$this->data['selected_followers_type'] = $futures_form['selected_followers_type']; 	// or 'position' as your default
				$this->data['selected_friends'] = $futures_form['selected_friends']; 				// preset value for Friends choices
				$this->data['selected_position_points'] = $futures_form['selected_position_points'];
				//Actual Win History Filtering
				$this->data['selected_trends'] = $futures_form['selected_trends']; 				    // trends setting
				$this->data['selected_winning_sums'] = $futures_form['selected_winning_sums'];  	    // sums setting
				$this->data['selected_winning_digits'] = $futures_form['selected_winning_digits']; 	// digit sums setting
				$this->data['selected_repeaters'] = $futures_form['selected_repeaters']; 			// repeaters setting
				$this->data['selected_consecutives'] = $futures_form['selected_consecutives'];  		// consecutives setting
				$this->data['selected_parity'] = $futures_form['selected_parity'];					// parity (odd / even) setting
				$this->data['selected_decades'] = $futures_form['selected_decades'];					// decades setting
				$this->data['selected_last_digits'] = $futures_form['selected_last_digits']; 		// last digits setting
				$this->data['selected_number_range'] = $futures_form['selected_number_range'];		// number range setting
				$this->data['selected_adjacents'] = $futures_form['selected_adjacents'];				// adjacents setting
			$number_array = $this->session->userdata('futures_number_array');
			$combination_file = $this->session->userdata('combination_file_name'); // Use parsed filename
			
			// Check for friendship warnings in GET requests (when viewing existing results)
			$friendship_warning = null;
			if ($number_array && $futures_form['selected_friends_checkbox'] === 'on' && $futures_form['selected_friends'] !== 'all') {
				// Analyze the current number array for friendship warnings
				$friendship_analysis = $this->predictions_m->analyze_friendships($id, $number_array);
				$selected_friends = $futures_form['selected_friends'];
				
				// Generate appropriate warning message based on what was requested vs found
				if ($selected_friends === 'none') {
					if ($friendship_analysis['has_1way'] || $friendship_analysis['has_2way']) {
						$friendship_warning = 'Warning: Some friendships may still exist in the combination despite selecting "No Friends".';
					}
				} elseif ($selected_friends === '1') {
					if (!$friendship_analysis['has_1way']) {
						$friendship_warning = 'Warning: No 1-way friendships were found in the current combination.';
					}
					if ($friendship_analysis['has_2way']) {
						$friendship_warning = 'Warning: Some 2-way friendships may still exist despite selecting "1-way Friends Only".';
					}
				} elseif ($selected_friends === '2') {
					if (!$friendship_analysis['has_2way']) {
						$friendship_warning = 'Warning: No 2-way friendships were found in the current combination.';
					}
					if ($friendship_analysis['has_1way']) {
						$friendship_warning = 'Warning: Some 1-way friendships may still exist despite selecting "2-way Friends Only".';
					}
				}
			}
    		$combo_id = $this->session->userdata('combination_file_id'); // Get combo_id
			$this->data['combo_id'] = ($this->lottery_data_m->validate_combo_id($combo_id) ? $combo_id : NULL); 
			$this->data['active'] = $this->combination_filters_m->get_active_flag($combo_id);
			
			// Get filter record ID for Prize controller navigation
			// This should work for both active and expired filters
			$this->data['filter_record_id'] = NULL;
			if ($combo_id) {
				// Look up the filter record by combo_id to get the actual filter ID
				// This works regardless of active/expired status
				$saved_settings = $this->combination_filters_m->get_saved_settings($combo_id);
				if ($saved_settings && isset($saved_settings['id'])) {
					$this->data['filter_record_id'] = $saved_settings['id'];
					log_message('info', "Predictions::combination - Found filter_record_id: {$saved_settings['id']} for combo_id: {$combo_id}, active: " . ($this->data['active'] ? 'true' : 'false') . " (session data path)");
				} else {
					log_message('info', "Predictions::combination - No saved settings found for combo_id: {$combo_id} (session data path)");
				}
			}
			
			// Get filename and CCCC data for futures view
			if ($combo_id && $this->data['combo_id']) {
				$filename_cccc_data = $this->lottery_data_m->get_combination_filename_cccc($combo_id);
				if ($filename_cccc_data) {
					$this->data['file_name'] = $filename_cccc_data['file_name'];
					$this->data['CCCC'] = $filename_cccc_data['CCCC'];
				}
			}
			 $this->data['selected_wheeling'] = $combination_file;
    		 //$this->data['selected_combo_id'] = $combo_id; Query the database to see if has an existing combo_id	
			$page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
			$per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 10;
			if ($number_array && $combination_file) {
				$filepath = FCPATH . 'combinations/' . basename($combination_file) . '.txt';
				// Load lottery highlights for filtering
				if (!isset($this->data['lottery']->highlights)) {
					$this->data['lottery']->highlights = $this->predictions_m->get_lottery_highlights($id);
				}
				// Prepare filter array for GET requests
				$filters = [
					'selected_trends' => $futures_form['selected_trends'],
					'selected_winning_sums' => $futures_form['selected_winning_sums'],
					'selected_winning_digits' => $futures_form['selected_winning_digits'],
					'selected_repeaters' => $futures_form['selected_repeaters'],
					'selected_consecutives' => $futures_form['selected_consecutives'],
					'selected_parity' => $futures_form['selected_parity'],
					'selected_decades' => $futures_form['selected_decades'],
					'selected_last_digits' => $futures_form['selected_last_digits'],
					'selected_number_range' => $futures_form['selected_number_range'],
					'selected_adjacents' => $futures_form['selected_adjacents'],
					'selected_extra_ball' => isset($futures_form['selected_extra_ball']) ? $futures_form['selected_extra_ball'] : 'ALL',
					'drawn' => $drawn,
					'lottery_last_drawn' => $this->data['lottery']->last_drawn,
					'extra_ball' => $this->data['lottery']->extra_ball,
					'duplicate_extra_ball' => $this->data['lottery']->duplicate_extra_ball,
					'max_ball' => $this->data['lottery']->maximum_ball,
					'lottery_highlights' => $this->data['lottery']->highlights
				];
				$updated_combinations = $this->predictions_m->insert_number_combination($filepath, $number_array, $page, $per_page, $filters);
				// Check if any combinations were found
				if (empty($updated_combinations)) {
					$this->data['message'] = 'No Combinations are available with the applied filters';
					$this->data['combos_paginated'] = [];
					$this->data['pagination'] = [
						'current' => 1,
						'total' => 1,
						'per_page' => $per_page,
						'total_filtered' => 0
					];
				} else {
					$this->data['combos_paginated'] = $updated_combinations;
					// Paginate for display - use filtered count for accurate pagination
					$total_filtered = $this->predictions_m->get_filtered_combinations_count($filepath, $number_array, $filters);
					$this->data['pagination'] = [
						'current' => $page,
						'total' => ceil($total_filtered / $per_page),
						'per_page' => $per_page,
						'total_filtered' => $total_filtered
					];
					$this->data['number_array'] = $number_array;
					$this->data['message'] = 'Combination Table and filters loaded successfully.';
				}
			} else {
				$this->data['combos_paginated'] = [];
				$this->data['pagination'] = [
					'current' => 1,
					'total' => 1,
					'per_page' => $per_page,
					'total_filtered' => 0
				];
			}
			$this->data['disable_combination_dropdown'] = true; // or false
			$this->data['disable_generate_button'] = false; 	// or false
		}
		$this->data['lottery']->highlights = $this->predictions_m->get_lottery_highlights($id);
		$this->data['lottery']->trends = $this->predictions_m->get_trends($this->data['lottery']->highlights['trends']);
		$this->data['lottery']->winning_digits = $this->predictions_m->get_digit_sums($this->data['lottery']->highlights['winning_digits']);
		$this->data['lottery']->winning_sums = $this->predictions_m->get_sums($this->data['lottery']->highlights['winning_sums']);
		$this->data['lottery']->repeaters = $this->predictions_m->get_repeaters($this->data['lottery']->highlights['repeats']);
		$this->data['lottery']->consecutives = $this->predictions_m->get_consecutives($this->data['lottery']->highlights['consecutives']);
		$this->data['lottery']->parity = $this->predictions_m->get_parity($this->data['lottery']->highlights['parity']);
		// Call get_decade and get_last from predictions_m
		$this->data['lottery']->decades = $this->predictions_m->get_decade($tbl_name, $this->data['lottery']->highlights['range']);
		$this->data['lottery']->last_digits = $this->predictions_m->get_last($tbl_name, $this->data['lottery']->highlights['range']);
		$this->data['lottery']->number_range = $this->predictions_m->get_range($this->data['lottery']->highlights['number_range']);
		$this->data['lottery']->adjacents = $this->predictions_m->get_adjacents($this->data['lottery']->highlights['adjacents']);
		$this->data['friends'] = $this->predictions_m->get_friends($id);
		$this->data['friends_dropdown_options'] = $this->predictions_m->get_friends_dropdown_options($id);
		
		// Grab the next draw date
		$ld = $this->data['lottery']->last_drawn['draw_date'];	// Return last draw date
		$day = $this->lotteries_m->return_day($ld);				// Returns the day of draw, Saturday, Sunday, etc.
		$this->data['lottery']->next_draw_date = $this->lotteries_m->next_date($this->data['lottery'], $day, $ld);
		
		// Get all saved combination filters for the user - ensure this is always available
		$user_id = $this->session->userdata('id');
		$this->data['saved_combinations'] = $this->lottery_data_m->get_all_user_combination_filters($id, $user_id);
		
		// Add friendship warning if it exists
		if (isset($friendship_warning) && !empty($friendship_warning)) {
			$this->data['friendship_warning'] = $friendship_warning;
		}
		
		// Load the view
		unset($this->data['lottery']->highlights);
		$this->data['current'] = $this->uri->segment(2);
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/futures'.'/'.$id);
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);
		$this->data['admins'] = $this->maintenance_m->logged_online(1);
		$this->data['visitors'] = $this->maintenance_m->active_visitors();
		$this->data['predictions'] = $this;	 // Access the methods in the view
		$this->data['subview'] = 'admin/dashboard/predictions/futures';
		$this->load->view('admin/_layout_main', $this->data);
	}
	/**
	 * Saves filtered combination tickets to the lottery_combination_filters table
	 * 
	 * @param       integer	$id		Lottery id
	 * @return      void
	 */
	public function combination_save($id)
	{
		// Start output buffering to catch any unexpected output
		if ($this->input->is_ajax_request()) {
			ob_start();
		}
		
		try {
			// Check if this is an AJAX request
			$is_ajax = $this->input->is_ajax_request();
			
			log_message('info', "Combination_save: Starting save process for lottery_id: {$id}, is_ajax: " . ($is_ajax ? 'true' : 'false'));
			
			$this->data['message'] = '';
			$this->data['lottery'] = $this->lotteries_m->get($id);
			
			// Get session data
			$session_data = $this->session->userdata('futures_form');
			$number_array = $this->session->userdata('futures_number_array');
			$combination_file = $this->session->userdata('combination_file_name'); // Use parsed filename
			$combo_id = $this->session->userdata('combination_file_id'); // Use stored combo_id

			log_message('info', "Combination_save: Session data check - session_data: " . ($session_data ? 'exists' : 'missing') . 
				", number_array: " . ($number_array ? 'exists' : 'missing') . 
				", combination_file: " . ($combination_file ? $combination_file : 'missing') . 
				", combo_id: " . ($combo_id ? $combo_id : 'missing'));

			if (!$session_data || !$number_array || !$combination_file || !$combo_id) {
				$message = 'Session data not found. Please generate tickets first.';
				log_message('error', "Combination_save: Session validation failed - {$message}");
				if ($is_ajax) {
					// Clean output buffer and send clean JSON
					ob_clean();
					$this->output
						->set_content_type('application/json')
						->set_output(json_encode([
							'success' => false,
							'message' => $message
						]));
					return;
				}
				$this->session->set_flashdata('message', '<div class="alert alert-danger">' . $message . '</div>');
				redirect('admin/predictions/futures/' . $id);
				return;
			}
		// Get the filtered combinations count
		$drawn = $this->data['lottery']->balls_drawn;
		$filepath = FCPATH . 'combinations/' . basename($combination_file) . '.txt';
		// Load required data for filtering
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		$this->data['lottery']->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl_name);
		$p_group = $this->statistics_m->prize_group_profile($id);
		$p_group = $this->statistics_m->prizes_only($p_group, $this->data['lottery']->extra_ball);
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_prizegroup($this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_ball, $p_group);
		// Get followers data for last_drawn processing
		$followers = $this->predictions_m->get_followers($id);
		$follower_wins = explode(">", $followers['wins']);
		$follow_poswins = explode(">", $followers['positions']);
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_addwins($this->data['lottery']->last_drawn, $drawn, $followers['extra_included'], $p_group, $follower_wins, $follow_poswins);
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_addpoints($this->data['lottery']->last_drawn, $drawn, $followers['extra_included']);
		// Load lottery highlights for filtering
		$this->data['lottery']->highlights = $this->predictions_m->get_lottery_highlights($id);
		// Prepare filter array
		$filters = [
			'selected_trends' => $session_data['selected_trends'],
			'selected_winning_sums' => $session_data['selected_winning_sums'],
			'selected_winning_digits' => $session_data['selected_winning_digits'],
			'selected_repeaters' => $session_data['selected_repeaters'],
			'selected_consecutives' => $session_data['selected_consecutives'],
			'selected_parity' => $session_data['selected_parity'],
			'selected_decades' => $session_data['selected_decades'],
			'selected_last_digits' => $session_data['selected_last_digits'],
			'selected_number_range' => $session_data['selected_number_range'],
			'selected_adjacents' => $session_data['selected_adjacents'],
			'drawn' => $drawn,
			'lottery_last_drawn' => $this->data['lottery']->last_drawn,
			'extra_ball' => $this->data['lottery']->extra_ball,
			'duplicate_extra_ball' => $this->data['lottery']->duplicate_extra_ball,
			'lottery_highlights' => $this->data['lottery']->highlights
		];
		
		$filtered_count = $this->predictions_m->get_filtered_combinations_count($filepath, $number_array, $filters);
		// Get current user ID and format it with leading zero if needed
		$current_user_id = $this->session->userdata('id');
		$formatted_user_id = str_pad($current_user_id, 2, '0', STR_PAD_LEFT);
		// Create filename: 060828ADMIN01 format (MMDDYY + ADMIN + user_id)
		//$current_date = date('mdy'); // Get current date in MMDDYY format
		$file_name = $combination_file . 'ADMIN' . $formatted_user_id;
		// Get N from the 2 digits of the combination file name instead of the database
		$N = substr($combination_file, 2, 2); // eg 060828 is R = 06, N = 8 and 28 is the number of ticket combinations
		$R = $this->data['lottery']->balls_drawn; // Pick number from lottery data (Pick 5, Pick 6, etc.)
		// CCCC is the actual filtered count from get_filtered_combinations_count() method
		// This will be the actual number of tickets after filtering (e.g., 5 tickets after sum filtering)
		// Prepare data for saving
		// Grab the next draw date
		$ld = $this->data['lottery']->last_drawn['draw_date'];
		$mysql_date = $this->lottery_data_m->format_date_to_mysql($ld);
		$save_data = [
  			'file_name' => $file_name,
			'N' => $N,
			'R' => $R,
			'CCCC' => $filtered_count, // Use actual filtered count
			'hwc' => $session_data['selected_hwc'] ? 1 : 0,
			'followers' => $session_data['selected_followers'] ? 1 : 0,
			'friends' => $session_data['selected_friends_checkbox'] ? 1 : 0,
			'h_w_c_group' => $session_data['selected_h_w_c_group'],
			'follower_type' => $session_data['selected_followers_type'],
			'ball_points' => $session_data['selected_ball_points'],
			'position_points' => $session_data['selected_position_points'],
			'selected_friends' => $session_data['selected_friends'], // Use actual friends dropdown value (all, none, 1, 2)
			'trends' => $session_data['selected_trends'],
			'winning_sums' => $session_data['selected_winning_sums'],
			'winning_digits' => $session_data['selected_winning_digits'],
			'repeaters' => $session_data['selected_repeaters'],
			'consecutives' => $session_data['selected_consecutives'],
			'parity' => $session_data['selected_parity'],
			'decades' => $session_data['selected_decades'],
			'last_digits' => $session_data['selected_last_digits'],
			'number_range' => $session_data['selected_number_range'],
			'adjacents' => $session_data['selected_adjacents'],
			'user' => 1, // Admin user
			'user_id' => $current_user_id,
			'member_id' => 0, // Default for admin
			'extra' => 0,
			'1_win' => 0,
			'1_win_extra' => 0,
			'2_win' => 0,
			'2_win_extra' => 0,
			'3_win' => 0,
			'3_win_extra' => 0,
			'4_win' => 0,
			'4_win_extra' => 0,
			'5_win' => 0,
			'5_win_extra' => 0,
			'6_win' => 0,
			'6_win_extra' => 0,
			'7_win' => 0,
			'7_win_extra' => 0,
			'8_win' => 0,
			'8_win_extra' => 0,
			'9_win' => 0,
			'9_win_extra' => 0,
			'active' => 1,
			'combo_id' => $combo_id, 		// Store the id from the combination_table_files table
			'lastdate' => $mysql_date,		// Next draw date 
			'lottery_id' => $id
		];
		// Save to database
		$saved = $this->predictions_m->save_combination_filter($save_data);
		log_message('info', "Combination_save: Database save result: " . ($saved ? 'success' : 'failed'));
		
		if ($saved) {
			// Create Pick subdirectory in combinations directory if it doesn't exist
			$pick_dir = FCPATH . 'combinations/pick' . $R . '/';
			if (!is_dir($pick_dir)) {
				mkdir($pick_dir, 0755, true);
				log_message('info', "Combination_save: Created directory: {$pick_dir}");
			}
			// Save filtered combinations to file
			$pick_file_path = $pick_dir . $file_name . '.txt';
			log_message('info', "Combination_save: Attempting to save filtered combinations to: {$pick_file_path}");
			log_message('info', "Combination_save: Source file path: {$filepath}");
			log_message('info', "Combination_save: Number array size: " . (is_array($number_array) ? count($number_array) : 'not an array'));
			log_message('info', "Combination_save: Filter array keys: " . implode(', ', array_keys($filters)));
			
			$success = $this->combination_filters_m->save_filtered_combinations_to_file($filepath, $number_array, $filters, $pick_file_path);
			log_message('info', "Combination_save: File save result: " . ($success ? 'success' : 'failed'));
			
			if ($success) {
				$message = 'Combination Ticket File ' . preg_replace('/ADMIN.*/', '', $file_name) . ' is Successfully Saved to the combinations/pick' . $R . ' Directory.';
				log_message('info', "Combination_save: Success - {$message}");
				if ($is_ajax) {
					// Clean output buffer and send clean JSON
					ob_clean();
					$this->output
						->set_content_type('application/json')
						->set_output(json_encode([
							'success' => true,
							'message' => $message
						]));
					return;
				}
				$this->session->set_flashdata('message', '<div class="alert alert-success">' . $message . '</div>');
			} else {
				$message = 'Combination Ticket File ' . $file_name . ' has not been Saved to the combinations/pick' . $R . ' Directory.';
				log_message('error', "Combination_save: File save failed - {$message}");
				if ($is_ajax) {
					// Clean output buffer and send clean JSON
					ob_clean();
					$this->output
						->set_content_type('application/json')
						->set_output(json_encode([
							'success' => false,
							'message' => $message
						]));
					return;
				}
				$this->session->set_flashdata('message', '<div class="alert alert-danger">' . $message . '</div>');
			}
		} else {
			$message = 'Failed to save combination filter data to database.';
			log_message('error', "Combination_save: Database save failed - {$message}");
			if ($is_ajax) {
				// Clean output buffer and send clean JSON
				ob_clean();
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode([
						'success' => false,
						'message' => $message
					]));
				return;
			}
			$this->session->set_flashdata('message', '<div class="alert alert-danger">' . $message . '</div>');
		}
		// For non-AJAX requests, redirect back to futures page with message
		redirect('admin/predictions/futures/' . $id);
		
		} catch (Exception $e) {
			$error_message = 'Exception in combination_save: ' . $e->getMessage();
			log_message('error', $error_message);
			log_message('error', 'Exception stack trace: ' . $e->getTraceAsString());
			
			if ($this->input->is_ajax_request()) {
				// Clean output buffer and send clean JSON
				ob_clean();
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode([
						'success' => false,
						'message' => 'An error occurred while saving. Please check the logs for details.',
						'debug' => ENVIRONMENT === 'development' ? $error_message : null
					]));
				return;
			}
			$this->session->set_flashdata('message', '<div class="alert alert-danger">' . $error_message . '</div>');
			redirect('admin/predictions/futures/' . $id);
		} catch (Error $e) {
			$error_message = 'Fatal error in combination_save: ' . $e->getMessage();
			log_message('error', $error_message);
			log_message('error', 'Fatal error stack trace: ' . $e->getTraceAsString());
			
			if ($this->input->is_ajax_request()) {
				// Clean output buffer and send clean JSON
				ob_clean();
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode([
						'success' => false,
						'message' => 'A fatal error occurred while saving. Please check the logs for details.',
						'debug' => ENVIRONMENT === 'development' ? $error_message : null
					]));
				return;
			}
			$this->session->set_flashdata('message', '<div class="alert alert-danger">' . $error_message . '</div>');
			redirect('admin/predictions/futures/' . $id);
		}
	}
	/**
	 * Refresh method to restore previously saved combination filter settings
	 * 
	 * @param int $id The ID of the selected lottery
	 * @return void Loads the futures view with restored settings
	 */
	public function refresh($id)
	{
		$this->data['message'] = '';
		$this->data['disable_generate_button'] = false;
		$this->data['disable_combination_dropdown'] = true;
		// Get the record_id from the URL parameter or POST data
		$record_id = $this->input->get('combo_id') ?: $this->input->post('combo_id');
		
		// Debug: Log what we received
		log_message('info', "Refresh method: Received combo_id parameter: " . var_export($record_id, true));
		
		// If record_id comes from dropdown value format (253|06077), extract just the ID
		if ($record_id && strpos($record_id, '|') !== false) {
			list($record_id, $filename) = explode('|', $record_id, 2);
			$record_id = (int)$record_id;
			log_message('info', "Refresh method: Extracted record_id from dropdown: " . $record_id . ", filename: " . $filename);
		} else {
			$record_id = (int)$record_id;
			log_message('info', "Refresh method: Using record_id as is: " . $record_id);
		}
		
		if (!$record_id) {
			log_message('error', "Refresh method: No record_id found");
			$this->session->set_flashdata('message', '<div class="alert alert-danger">No combination ID found for refresh.</div>');
			redirect('admin/predictions/futures/' . $id);
			return;
		}
		
		// Load the combination_filters_m model to get saved settings
		$this->load->model('combination_filters_m');
		$saved_settings = $this->combination_filters_m->get_saved_settings($record_id);
		
		// Debug: Log what we found
		log_message('info', "Refresh method: Saved settings lookup result: " . var_export($saved_settings, true));
		
		if (!$saved_settings) {
			log_message('error', "Refresh method: No saved settings found for record_id: " . $record_id);
			$this->session->set_flashdata('message', '<div class="alert alert-danger">No saved settings found for combination ID: ' . $record_id . '</div>');
			redirect('admin/predictions/futures/' . $id);
			return;
		}
		// User confirmed, proceed with loading settings
		$this->data['lottery'] = $this->lotteries_m->get($id);
		// Check if lottery was found
		if (!$this->data['lottery']) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Lottery not found with ID: ' . $id . '</div>');
			redirect('admin/predictions');
			return;
		}
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		$drawn = $this->data['lottery']->balls_drawn;
		// Extract original combination file name (remove ADMIN## suffix)
		$original_filename = $this->combination_filters_m->extract_original_filename($saved_settings['file_name']);
		
		// Get the actual combo_id from saved settings
		$combo_id = $saved_settings['combo_id'];
		
		// Debug: Log the key values
		log_message('info', "Refresh method: Original filename: " . $original_filename . ", combo_id: " . $combo_id);
		// Set up all the basic lottery data
		$this->data['country_code'] = $this->lottery_data_m->get_lottery_country($id);
		$this->data['state_prov_code'] = $this->lottery_data_m->get_lottery_state_prov($id);
		$this->data['combination_files'] = $this->predictions_m->get_combination_files($id);
		// Sort combination files numerically and set up the dropdown value format
		if (!empty($this->data['combination_files'])) {
			foreach ($this->data['combination_files'] as &$file) {
				$file['value'] = $file['id'] . '|' . $file['file_name'];
				$file['display'] = $file['file_name'];
			}
			usort($this->data['combination_files'], function($a, $b) {
				$numA = intval(preg_replace('/\D/', '', $a['file_name']));
				$numB = intval(preg_replace('/\D/', '', $b['file_name']));
				return $numA - $numB;
			});
		}
		// Set up H-W-C, Followers, and Friends data
		$this->data['h_w_c'] = $this->lottery_data_m->get_h_w_c($id);
		$h_w_c_group = $this->lottery_statistics_m->get_h_w_c_range($id);
		$h_w_c_group_options = [];
		foreach ($h_w_c_group as $group) {
			$value = substr($group, 0, 5);
			$h_w_c_group_options[$value] = $group;
		}
		$this->data['h_w_c_group'] = $h_w_c_group_options;
		$this->data['followers'] = $this->lottery_data_m->get_followers($id);
		// Set up lottery data for points calculations
		$this->data['lottery']->last_drawn = (array) $this->lotteries_m->last_draw_db($tbl_name);
		$p_group = $this->statistics_m->prize_group_profile($id);
		$p_group = $this->statistics_m->prizes_only($p_group, $this->data['lottery']->extra_ball);
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_prizegroup($this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->extra_ball, $p_group);
		$follower_wins = explode(">", $this->data['followers']['wins']);
		$follow_poswins = explode(">", $this->data['followers']['positions']);
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_addwins($this->data['lottery']->last_drawn, $drawn, $this->data['followers']['extra_included'], $p_group, $follower_wins, $follow_poswins);
		$this->data['lottery']->last_drawn = $this->history_m->last_draw_addpoints($this->data['lottery']->last_drawn, $drawn, $this->data['followers']['extra_included']);
		// Ball points and position points setup
		$ball_points = $this->predictions_m->get_sorted_ball_points($this->data['lottery']->last_drawn, $drawn, $this->data['lottery']->duplicate_extra_ball);
		$ball_points_options = [];
		foreach ($ball_points as $label) {
			if (strpos($label, '+') === 0) {
				$value = substr($label, 0, strpos($label, ' '));
			} else {
				$value = strtok($label, ' ');
			}
			$ball_points_options[$value] = $label;
		}
		$this->data['ball_points_options'] = $ball_points_options;
		$position_points = $this->lottery_statistics_m->get_sorted_position_points($this->data['lottery']->last_drawn, $drawn);
		$position_points_options = [];
		foreach ($position_points as $label) {
			if (strpos($label, '+') === 0) {
				$value = substr($label, 0, strpos($label, ' '));
			} else {
				$value = strtok($label, ' ');
			}
			$position_points_options[$value] = $label;
		}
		$this->data['position_points_options'] = $position_points_options;
		// Restore all saved settings
		$this->data['selected_followers_type'] = $saved_settings['follower_type'];
		$this->data['selected_hwc'] = (bool)$saved_settings['hwc'];
		$this->data['selected_followers'] = (bool)$saved_settings['followers'];
		$this->data['selected_friends_checkbox'] = (bool)$saved_settings['friends'];
		$this->data['selected_h_w_c_group'] = $saved_settings['h_w_c_group'];
		$this->data['selected_ball_points'] = $saved_settings['ball_points'];
		$this->data['selected_position_points'] = $saved_settings['position_points'];
		$this->data['selected_friends'] = $saved_settings['selected_friends'];
		$this->data['selected_wheeling'] = $record_id . '|' . $original_filename; // Set dropdown value format
		$this->data['combo_id'] = $combo_id;
		$this->data['active'] = $this->combination_filters_m->get_active_flag($record_id);	
		// Restore filter selections
		$this->data['selected_trends'] = $saved_settings['trends'];
		$this->data['selected_winning_sums'] = $saved_settings['winning_sums'];
		$this->data['selected_winning_digits'] = $saved_settings['winning_digits'];
		$this->data['selected_repeaters'] = $saved_settings['repeaters'];
		$this->data['selected_consecutives'] = $saved_settings['consecutives'];
		$this->data['selected_parity'] = $saved_settings['parity'];
		$this->data['selected_decades'] = $saved_settings['decades'];
		$this->data['selected_last_digits'] = $saved_settings['last_digits'];
		$this->data['selected_number_range'] = $saved_settings['number_range'];
		$this->data['selected_adjacents'] = $saved_settings['adjacents'];
		// Get filename and CCCC data for display
		if ($combo_id) {
			$filename_cccc_data = $this->lottery_data_m->get_combination_filename_cccc($combo_id);
			if ($filename_cccc_data) {
				$this->data['file_name'] = $filename_cccc_data['file_name'];
				$this->data['CCCC'] = $filename_cccc_data['CCCC'];
			}
		}
		// Store restored settings in session
		$session_data = [
			'selected_h_w_c_group' => $saved_settings['h_w_c_group'],
			'selected_followers_type' => $saved_settings['follower_type'],
			'selected_ball_points' => $saved_settings['ball_points'],
			'selected_position_points' => $saved_settings['position_points'],
			'selected_friends' => $saved_settings['selected_friends'],
			'selected_hwc' => (bool)$saved_settings['hwc'],
			'selected_followers' => (bool)$saved_settings['followers'],
			'selected_friends_checkbox' => (bool)$saved_settings['friends'],
			'selected_wheeling' => $record_id . '|' . $original_filename,
			'selected_trends' => $saved_settings['trends'],
			'selected_winning_sums' => $saved_settings['winning_sums'],
			'selected_winning_digits' => $saved_settings['winning_digits'],
			'selected_repeaters' => $saved_settings['repeaters'],
			'selected_consecutives' => $saved_settings['consecutives'],
			'selected_parity' => $saved_settings['parity'],
			'selected_decades' => $saved_settings['decades'],
			'selected_last_digits' => $saved_settings['last_digits'],
			'selected_number_range' => $saved_settings['number_range'],
			'selected_adjacents' => $saved_settings['adjacents'],
			'selected_combo_id' => $record_id,
		];
		$this->session->set_userdata('futures_form', $session_data);
		$this->session->set_userdata('combination_file_id', $combo_id);
		$this->session->set_userdata('combination_file_name', $original_filename);
		// Get lottery highlights and historical data for filters
		$this->data['lottery']->highlights = $this->predictions_m->get_lottery_highlights($id);
		$this->data['lottery']->trends = $this->predictions_m->get_trends($this->data['lottery']->highlights['trends']);
		$this->data['lottery']->winning_digits = $this->predictions_m->get_digit_sums($this->data['lottery']->highlights['winning_digits']);
		$this->data['lottery']->winning_sums = $this->predictions_m->get_sums($this->data['lottery']->highlights['winning_sums']);
		$this->data['lottery']->repeaters = $this->predictions_m->get_repeaters($this->data['lottery']->highlights['repeats']);
		$this->data['lottery']->consecutives = $this->predictions_m->get_consecutives($this->data['lottery']->highlights['consecutives']);
		$this->data['lottery']->parity = $this->predictions_m->get_parity($this->data['lottery']->highlights['parity']);
		$this->data['lottery']->decades = $this->predictions_m->get_decade($tbl_name, $this->data['lottery']->highlights['range']);
		$this->data['lottery']->last_digits = $this->predictions_m->get_last($tbl_name, $this->data['lottery']->highlights['range']);
		$this->data['lottery']->number_range = $this->predictions_m->get_range($this->data['lottery']->highlights['number_range']);
		$this->data['lottery']->adjacents = $this->predictions_m->get_adjacents($this->data['lottery']->highlights['adjacents']);
		$this->data['friends'] = $this->predictions_m->get_friends($id);
		// Get next draw date
		$ld = $this->data['lottery']->last_drawn['draw_date'];
		$day = $this->lotteries_m->return_day($ld);
		$this->data['lottery']->next_draw_date = $this->lotteries_m->next_date($this->data['lottery'], $day, $ld);
		
		// **PREVENT IMMEDIATE EXPIRATION**
		// Update the lastdate to the next draw date to prevent the filter from being expired
		// when verify_active_date runs on page load
		$mysql_next_date = date('Y-m-d H:i:s', strtotime($this->data['lottery']->next_draw_date));
		
		$update_data = [
			'lastdate' => $mysql_next_date,
			'active' => 1  // Ensure it stays active
		];
		
		$this->db->where('id', $record_id);
		$update_result = $this->db->update('lottery_combination_filters', $update_data);
		
		if ($update_result) {
			log_message('info', "Refresh method: Updated lastdate to {$mysql_next_date} to prevent immediate expiration");
		} else {
			log_message('error', "Refresh method: Failed to update lastdate for record {$record_id}");
		}
		// **END EXPIRATION PREVENTION**
		// **LOAD EXISTING FILTERED TICKETS INSTEAD OF REGENERATING**
		// First, try to load the existing filtered tickets from the saved file
		
		$saved_filename = $saved_settings['file_name']; // This is the ADMIN## filename
		
		// Get picks count from the lottery combination files table
		$this->db->select('N');
		$this->db->from('lottery_combination_files');
		$this->db->where('id', $combo_id);
		$combo_file = $this->db->get()->row();
		
		if (!$combo_file || !$combo_file->N) {
			log_message('error', "Refresh method: Could not get picks count for combo_id: " . $combo_id);
			$this->session->set_flashdata('message', '<div class="alert alert-danger">Unable to determine lottery pick count.</div>');
			redirect('admin/predictions/futures/' . $id);
			return;
		}
		
		$picks = (int)$combo_file->N;
		
		// Construct the file path for the saved filtered tickets
		$directory = FCPATH . 'combinations/pick' . $picks . '/';
		$file_path = $directory . $saved_filename . '.txt';
		
		log_message('info', "Refresh method: Looking for saved filtered tickets at: " . $file_path);
		
		// Check if the filtered tickets file exists
		if (file_exists($file_path)) {
			// Load the existing filtered tickets
			$file_content = file_get_contents($file_path);
			
			if ($file_content) {
				$lines = explode("\n", $file_content);
				$filtered_tickets = [];
				
				foreach ($lines as $line) {
					$line = trim($line);
					if (!empty($line)) {
						// Parse the line into numbers
						$numbers = preg_split('/[\s,]+/', $line);
						$numbers = array_map('intval', array_filter($numbers, 'is_numeric'));
						
						if (count($numbers) == $picks) {
							$filtered_tickets[] = $numbers;
						}
					}
				}
				
				if (!empty($filtered_tickets)) {
					// Create number array from the original combination file (for filtering purposes)
					$original_file_path = $directory . $original_filename . '.txt';
					
					if (file_exists($original_file_path)) {
						$original_content = file_get_contents($original_file_path);
						$original_lines = explode("\n", $original_content);
						$number_array = [];
						
						foreach ($original_lines as $line) {
							$line = trim($line);
							if (!empty($line)) {
								$numbers = preg_split('/[\s,]+/', $line);
								$numbers = array_filter($numbers, 'is_numeric');
								foreach ($numbers as $num) {
									$number_array[] = (int)$num;
								}
							}
						}
						$number_array = array_unique($number_array);
						sort($number_array);
						
						// Set session data for the existing filtered tickets
						$this->session->set_userdata('futures_number_array', $number_array);
						$this->session->set_userdata('combination_file_id', $combo_id);
						$this->session->set_userdata('combination_file_name', $original_filename);
						
						log_message('info', "Refresh method: Successfully loaded " . count($filtered_tickets) . " existing filtered tickets");
						
						// Set up pagination for existing tickets
						$page = 1;
						$per_page = 10;
						$total_filtered = count($filtered_tickets);
						$total_pages = ceil($total_filtered / $per_page);
						$offset = ($page - 1) * $per_page;
						$paginated_tickets = array_slice($filtered_tickets, $offset, $per_page);
						
						// Add row numbers to tickets
						foreach ($paginated_tickets as $index => &$ticket) {
							$ticket['row_number'] = $offset + $index + 1;
							$ticket['numbers'] = is_array($ticket) && isset($ticket[0]) ? array_slice($ticket, 0, $picks) : $ticket;
						}
						
						// Set up the data for the view
						$this->data['combos_paginated'] = $paginated_tickets;
						$this->data['total_filtered'] = $total_filtered;
						$this->data['total_pages'] = $total_pages;
						$this->data['current_page'] = $page;
						$this->data['per_page'] = $per_page;
						$this->data['number_array'] = $number_array;
						
						// Set up pagination array for the view
						$this->data['pagination'] = [
							'current' => $page,
							'total' => $total_pages,
							'per_page' => $per_page,
							'total_filtered' => $total_filtered
						];
						
						log_message('info', "Refresh method: Displaying existing filtered tickets, page {$page} of {$total_pages}, showing {$per_page} tickets per page");
						
						// Set filter record ID for Prize controller navigation
						$this->data['filter_record_id'] = $record_id;
						log_message('info', "Refresh method: Set filter_record_id to {$record_id} for dollar icon navigation");
						
						// Get all saved combination filters for the user - ensure this is always available
						$user_id = $this->session->userdata('id');
						$this->data['saved_combinations'] = $this->lottery_data_m->get_all_user_combination_filters($id, $user_id);
						
						// Load the futures view with existing filtered tickets
						$this->data['subview'] = 'admin/dashboard/predictions/futures';
						$this->load->view('admin/_layout_main', $this->data);
						return;
					}
				}
			}
		}
		
		log_message('error', "Refresh method: Could not load existing filtered tickets, falling back to regeneration");
		
		// **FALLBACK: REGENERATE TICKETS IF EXISTING ONES CAN'T BE LOADED**
		// Set the session data that the combination method expects
		$session_data = [
			'selected_h_w_c_group' => $saved_settings['h_w_c_group'],
			'selected_followers_type' => $saved_settings['follower_type'],
			'selected_ball_points' => $saved_settings['ball_points'],
			'selected_position_points' => $saved_settings['position_points'],
			'selected_friends' => $saved_settings['selected_friends'],
			'selected_hwc' => (bool)$saved_settings['hwc'],
			'selected_followers' => (bool)$saved_settings['followers'],
			'selected_friends_checkbox' => (bool)$saved_settings['friends'],
			'selected_wheeling' => $record_id . '|' . $original_filename,
			'selected_trends' => $saved_settings['trends'],
			'selected_winning_sums' => $saved_settings['winning_sums'],
			'selected_winning_digits' => $saved_settings['winning_digits'],
			'selected_repeaters' => $saved_settings['repeaters'],
			'selected_consecutives' => $saved_settings['consecutives'],
			'selected_parity' => $saved_settings['parity'],
			'selected_decades' => $saved_settings['decades'],
			'selected_last_digits' => $saved_settings['last_digits'],
			'selected_number_range' => $saved_settings['number_range'],
			'selected_adjacents' => $saved_settings['adjacents'],
			'selected_combo_id' => $record_id,
		];
		
		$this->session->set_userdata('futures_form', $session_data);
		$this->session->set_userdata('combination_file_id', $combo_id);
		$this->session->set_userdata('combination_file_name', $original_filename);
		
		log_message('info', "Refresh method: Session data set, redirecting to combination method for regeneration");
		
		// Redirect to combination method with POST data to trigger ticket generation
		$_POST = [
			'h_w_c_group' => $saved_settings['h_w_c_group'],
			'hwc' => $saved_settings['hwc'] ? '1' : '0',
			'followers' => $saved_settings['followers'] ? '1' : '0',
			'friends' => $saved_settings['friends'] ? '1' : '0',
			'followers_type' => $saved_settings['follower_type'],
			'ball_points' => $saved_settings['ball_points'],
			'position_points' => $saved_settings['position_points'],
			'friends_dropdown' => $saved_settings['selected_friends'],
			'combination_file' => $record_id . '|' . $original_filename,
			'trends' => $saved_settings['trends'],
			'winning_sums' => $saved_settings['winning_sums'],
			'winning_digits' => $saved_settings['winning_digits'],
			'repeaters' => $saved_settings['repeaters'],
			'consecutives' => $saved_settings['consecutives'],
			'parity' => $saved_settings['parity'],
			'decades' => $saved_settings['decades'],
			'last_digits' => $saved_settings['last_digits'],
			'number_range' => $saved_settings['number_range'],
			'adjacents' => $saved_settings['adjacents']
		];
		
		// Call the combination method directly with the POST data
		return $this->combination($id);
	}
	/**
	 * Delete combination filter record and associated file
	 * 
	 * Deletes a combination filter from the lottery_combination_filters table
	 * and removes the associated .txt file from the filesystem. The file location
	 * is determined by the lottery's balls_drawn value (pick{balls_drawn} directory).
	 * Only allows deletion if the current admin user owns the combination filter.
	 * 
	 * @param int $combo_id The combination ID to delete
	 * @return void Redirects to predictions page with success/error message
	 * @throws Exception If database transaction fails or file deletion fails
	 */
	public function delete_combo($combo_id) {
		// Load the correct model
		$this->load->model('Lottery_data_m');
		try {
			// Validate ownership using existing model method
			if (!$this->Lottery_data_m->validate_combo_id($combo_id)) {
				$this->session->set_flashdata('error_message', 'You do not have permission to delete this combination filter or it does not exist.');
				redirect('admin/predictions');
				return;
			}
			// Get the combination filter record
			$this->db->select('combo_id, file_name, lottery_id, CCCC');
			$this->db->from('lottery_combination_filters');
			$this->db->where('combo_id', $combo_id);
			$combination_filter = $this->db->get()->row();
			// Get lottery details using direct database query
			$this->db->select('id, balls_drawn, lottery_name');
			$this->db->from('lottery_profiles');
			$this->db->where('id', $combination_filter->lottery_id);
			$lottery = $this->db->get()->row();
			if (!$lottery) {
				$this->session->set_flashdata('error_message', 'Lottery not found.');
				redirect('admin/predictions');
				return;
			}
			// Construct the directory path: pick + balls_drawn (same as combination_save method)
			$directory = FCPATH . 'combinations/pick' . $lottery->balls_drawn . '/';
			// Get the filename from the combination filter record
			$filename = $combination_filter->file_name . '.txt';
			$full_file_path = $directory . $filename;
			
			// Check if file exists before starting transaction
			$file_exists_before = file_exists($full_file_path);
			
			// Begin transaction
			$this->db->trans_start();
			// Delete the database record
			$this->db->where('combo_id', $combo_id);
			$delete_result = $this->db->delete('lottery_combination_filters');
			if (!$delete_result) {
				throw new Exception('Failed to delete combination filter from database.');
			}
			// Complete transaction
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE) {
				throw new Exception('Database transaction failed.');
			}
			
			// Delete the physical file if it exists (after successful DB transaction)
			$file_deleted = false;
			if ($file_exists_before) {
				$file_deleted = unlink($full_file_path);
				if (!$file_deleted) {
					log_message('error', 'Failed to delete combination file: ' . $full_file_path);
				}
			}
			
			// Set detailed success message
			$message = 'Combination Table previously saved settings for "' . preg_replace('/ADMIN.*/', '', $combination_filter->file_name) . '" have been successfully deleted from the database';
			if ($file_exists_before) {
				if ($file_deleted) {
					$message .= ' and the associated text file has been removed from the pick' . $lottery->balls_drawn . ' directory';
				} else {
					$message .= ' but the associated text file could not be deleted from the pick' . $lottery->balls_drawn . ' directory';
				}
			} else {
				$message .= ' (no associated text file was found)';
			}
			$message .= '.';
			
			$this->session->set_flashdata('success_message', $message);
			
			// Clear any related session data for the deleted combination
			$this->session->unset_userdata('futures_form');
			$this->session->unset_userdata('futures_number_array');
			$this->session->unset_userdata('combination_file_name');
			$this->session->unset_userdata('combination_file_id');
			$this->session->unset_userdata('generated_combos');
			$this->session->unset_userdata('selected_wheeling');
		} catch (Exception $e) {
			// Set error message
			$this->session->set_flashdata('error_message', 'Error deleting combination filter: ' . $e->getMessage());
			// Log the error
			log_message('error', 'Delete combo error: ' . $e->getMessage());
		}
		// Redirect back to the lottery's prediction futures page
		redirect('admin/predictions/futures/' . $combination_filter->lottery_id);
	}
}