<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Predictions extends Admin_Controller {
	
	
	public function __construct() {
		 parent::__construct();
		 $this->load->model('lotteries_m'); // Lottery Model
		 $this->load->model('statistics_m'); // Statistics Model
		 $this->load->model('history_m'); // History Model
		 $this->load->model('predictions_m'); // Predictions Model	
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
		if ($this->session->userdata('combination_file')) {
			$this->session->unset_userdata('combination_file');
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
		$file_name = (intval($this->data['lottery']->pick) < 10 ? '0' : '') . intval($this->data['lottery']->pick);
		$file_name .= (intval($this->data['lottery']->predict) < 10 ? '0' : '') . intval($this->data['lottery']->predict);
		$file_name .= intval($this->data['combinations']); // No leading zero for tickets
		
		$path = $this->predictions_m->full_path($file_name);

		if((file_exists($path))&&($this->predictions_m->lottery_combination_record($file_name))) 
		{
			$this->data['message'] = $file_name.'.txt currently exists in the '.predictions_m::DIR.' directory.<br />Please delete this File first.';
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
	 * @param       integer $R		Pick Number for the lottery
	 * @return      none
	 */
	public function generate($id)
	{
		$this->data['message'] = '';			// Defaulted to No Error Messages
		$this->data['lottery'] = $this->lotteries_m->get($id);
		$this->data['lottery']->generate = $this->predictions_m->lottery_combination_files($this->data['lottery']->balls_drawn);
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
		$this->data['lottery']->generate = $this->predictions_m->lottery_combination_files($this->data['lottery']->balls_drawn); //$this->predictions_m->all_combination_files();
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
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		$drawn = $this->data['lottery']->balls_drawn; // Get the number of balls drawn for this lottory, Pick 5, Pick 6, Pick 7, etc.
		$this->data['country_code'] = $this->predictions_m->get_lottery_country($id);
		$this->data['state_prov_code'] = $this->predictions_m->get_lottery_state_prov($id);
		// Fetch combination files for the lottery
    	$this->data['combination_files'] = $this->predictions_m->get_combination_files($id);
		// Before passing $combination_files to the view
		if (!empty($this->data['combination_files'])) {
			usort($this->data['combination_files'], function($a, $b) {
				// Extract the number part from the file name (assuming format like "06120500.txt")
				$numA = intval(preg_replace('/\D/', '', $a['file_name']));
				$numB = intval(preg_replace('/\D/', '', $b['file_name']));
				return $numA - $numB;
			});
		}
		// Fetch H-W-C, Followers, and Friends data
		$this->data['h_w_c'] = $this->predictions_m->get_h_w_c($id);
		$h_w_c_group = $this->predictions_m->get_h_w_c_range($id);
		// before passing $h_w_c_group to the view
		$h_w_c_group_options = [];
		foreach ($h_w_c_group as $group) {
			// $group is something like "2-2-2 (17)"
			$value = substr($group, 0, 5); // "2-2-2"
			$h_w_c_group_options[$value] = $group;
		}
		$this->data['h_w_c_group'] = $h_w_c_group_options;
		$this->data['followers'] = $this->predictions_m->get_followers($id);
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
			$position_points = $this->predictions_m->get_sorted_position_points($this->data['lottery']->last_drawn, $drawn);
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
			// Grab the next draw date
			$ld = $this->data['lottery']->last_drawn['draw_date'];	// Return last draw date
			$day = $this->lotteries_m->return_day($ld);				// Returns the day of draw, Saturday, Sunday, etc.
			$this->data['lottery']->next_draw_date = $this->lotteries_m->next_date($this->data['lottery'], $day, $ld);
		// Load the view
		unset($this->data['lottery']->highlights);
		$this->data['current'] = $this->uri->segment(2); // Sets the predictions menu
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/futures');
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
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
		$this->data['country_code'] = $this->predictions_m->get_lottery_country($id);
		$this->data['state_prov_code'] = $this->predictions_m->get_lottery_state_prov($id);
		$this->data['combination_files'] = $this->predictions_m->get_combination_files($id);
		$this->data['h_w_c'] = $this->predictions_m->get_h_w_c($id);
		$this->data['followers'] = $this->predictions_m->get_followers($id);
		$this->data['friends'] = $this->predictions_m->get_friends($id);

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

		$position_points = $this->predictions_m->get_sorted_position_points($this->data['lottery']->last_drawn, $drawn);
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
			if ($session_data) {
				$hwc_checked = $session_data['selected_hwc'];
				$followers_checked = $session_data['selected_followers'];
				$friends_checked = $session_data['selected_friends_checkbox'];
   				$combination_file = $this->session->userdata('combination_file');
				$h_w_c_group = ($this->input->post('h_w_c_group') ? $this->input->post('h_w_c_group') : $this->session->userdata('selected_h_w_c_group'));
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
				$this->data['disable_combination_dropdown'] = true;
			} else {
				// Get all POST values and save to session for future pagination
				$hwc_checked = $this->input->post('hwc') ? true : false;
				$followers_checked = $this->input->post('followers') ? true : false;
				$friends_checked = $this->input->post('friends') ? true : false;
				$combination_file = $this->input->post('wheeling', TRUE);
				$this->session->set_userdata('combination_file', $combination_file);
				$h_w_c_group = $this->input->post('h_w_c_group', TRUE);
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
					'selected_adjacents' 		=> $selected_adjacents
				];
				$this->session->set_userdata('futures_form', $session_data);
			}
				// LOTTERY PROFILE STATISTICS PRESETS Settings
				$this->data['selected_h_w_c_group'] = $h_w_c_group;				// H - W- C Group Selected
				$this->data['selected_followers_type'] = $followers_type;	  	// or 'position' as your default
				$this->data['selected_hwc'] = $hwc_checked; 					// preset value for H-W-C
				$this->data['selected_followers'] = $followers_checked; 		// preset value for Followers
				$this->data['selected_friends_checkbox'] = $friends_checked; 	// preset value for Friends
				$this->data['selected_friends'] = $selected_friends; 			// preset value for Friends choices
				$this->data['selected_wheeling'] = $combination_file; 			// preset value for the Combination File (wheeling file)
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
				$this->session->set_flashdata('message', 'Either the H-W-C or Followers must be checked, they both can not be unchecked.');
				redirect('admin/predictions');
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
			if ($friends_checked) {
				if ($selected_friends !== 'all') {
					$numbers = array_values(array_filter(array_map('trim', explode(',', $number_series))));
					array_unshift($numbers, null);
					unset($numbers[0]);
					if($hwc_checked) {
						$heat_map = $hwc_checked ? $this->predictions_m->get_heat_map($id) : [];
						if(empty($heat_map)) { 
							$this->session->set_flashdata('message', 'Problem with the Heat Map, please try again.');
							redirect('admin/predictions');
						}
						$numbers = $this->predictions_m->friend_search_hwc($id, $numbers, $selected_friends, $heat_map);
					} elseif(!$hwc_checked&&$followers_checked) {
						$followers_list = $followers_checked ? $this->predictions_m->get_followers_list($id, $followers_type, $follower_select) : [];
						if(empty($followers_list)) { 
							$this->session->set_flashdata('message', 'Problem with the Followers List, please try again.');
							redirect('admin/predictions');
						}
						$numbers = $this->predictions_m->friend_search_followers($id, $numbers, $selected_friends, $followers_list);
					}
					array_values($numbers); // Re-index the array from index 1 to index 0
					$number_series = implode(',', $numbers);
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
					'per_page' => $per_page
				];
			 	// Calculate filtered tickets count
    			$filtered_tickets_count = 'Not Available'; // Your logic here
    			$this->data['filtered_tickets_count'] = $filtered_tickets_count;
			} else {
				// Prepare number array and updated combinations
				$number_array = array_map('intval', explode(',', $number_series));
				$this->session->set_userdata('futures_number_array', $number_array);

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
					'drawn' => $drawn,
					'lottery_last_drawn' => $this->data['lottery']->last_drawn,
					'extra_ball' => $this->data['lottery']->extra_ball
				];
				$updated_combinations = $this->predictions_m->insert_number_combination($filepath, $number_array, $page, $per_page, $filters);

				$filter_error = FALSE;	// Initialize filter error flag, no encountered filtered errors
				// Apply trend filtering after getting updated combinations
				if (isset($selected_trends) && $selected_trends !== 'ALL') {
					$filtered_combinations = $this->predictions_m->filtered_trends(
						$updated_combinations,
						$this->data['lottery']->last_drawn,
						$drawn,
						$this->data['lottery']->extra_ball,
						$selected_trends,
						$page,
						$per_page,
						$filepath  // Add filepath as parameter
					);
					
					if ($filtered_combinations === false) {
						$filter_error = TRUE; // Set filter error flag
						$this->data['message'] = 'Filtering with the Up / Down Trend Filter resulted in No Combinations';
						$this->data['combos_paginated'] = [];
						$this->data['pagination'] = [
							'current' => 1,
							'total' => 1,
							'per_page' => $per_page
						];
					} else {
						$updated_combinations = $filtered_combinations;
					}
				}
				if (!$filter_error) {
					$combos_paginated = [];
					foreach ($updated_combinations as $combo) {
						$stats = $this->predictions_m->get_combo_stats($combo, $drawn, $this->data['lottery']->last_drawn);
						$combos_paginated[] = [
							'combo' => $combo,
							'stats' => $stats
						];
					}
					$this->data['combos_paginated'] = $combos_paginated;
					// Paginate for display
					// For pagination controls, you still need the total number of lines in the file:
					$total_lines = count(file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
					$this->data['pagination'] = [
						'current' => $page,
						'total' => ceil($total_lines / $per_page),
						'per_page' => $per_page
					];
					$this->data['number_array'] = $number_array;
					$this->data['message'] = 'Combination Table and filters loaded successfully.';
					}
			}
		}
		// --- GET:   ---
		else {
			// Restore form/filter values
			$future_form = $this->session->userdata('futures_form');
				foreach ($future_form as $key => $value) {
					$this->data[$key] = $value;
				}
			// LOTTERY PROFILE STATISTICS PRESETS Settings
				$this->data['selected_h_w_c_group'] = $future_form['selected_h_w_c_group'];
				$this->data['selected_hwc'] = $future_form['selected_hwc']; 
				$this->data['selected_followers'] = $future_form['selected_followers']; 
				$this->data['selected_friends_checkbox'] = $future_form['selected_friends_checkbox']; 
				$this->data['selected_followers_type'] = $future_form['selected_followers_type']; 	// or 'position' as your default
				$this->data['selected_friends'] = $future_form['selected_friends']; 				// preset value for Friends choices
				$this->data['selected_position_points'] = $future_form['selected_position_points'];
				//Actual Win History Filtering
				$this->data['selected_trends'] = $future_form['selected_trends']; 				    // trends setting
				$this->data['selected_winning_sums'] = $future_form['selected_winning_sums'];  	    // sums setting
				$this->data['selected_winning_digits'] = $future_form['selected_winning_digits']; 	// digit sums setting
				$this->data['selected_repeaters'] = $future_form['selected_repeaters']; 			// repeaters setting
				$this->data['selected_consecutives'] = $future_form['selected_consecutives'];  		// consecutives setting
				$this->data['selected_parity'] = $future_form['selected_parity'];					// parity (odd / even) setting
				$this->data['selected_decades'] = $future_form['selected_decades'];					// decades setting
				$this->data['selected_last_digits'] = $future_form['selected_last_digits']; 		// last digits setting
				$this->data['selected_number_range'] = $future_form['selected_number_range'];		// number range setting
				$this->data['selected_adjacents'] = $future_form['selected_adjacents'];				// adjacents setting
			$number_array = $this->session->userdata('futures_number_array');
			$combination_file = $this->session->userdata('combination_file');
			$this->data['selected_wheeling'] = $combination_file; 	
			$page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
			$per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 10;

			if ($number_array && $combination_file) {
				$filepath = FCPATH . 'combinations/' . basename($combination_file) . '.txt';
				$updated_combinations = $this->predictions_m->insert_number_combination($filepath, $number_array, $page, $per_page);
				// Apply trend filtering for GET requests as well
				$selected_trends = $future_form['selected_trends'] ?? 'ALL';
				if ($selected_trends !== 'ALL') {
					$filtered_combinations = $this->predictions_m->filtered_trends(
						$updated_combinations,
						$this->data['lottery']->last_drawn,
						$drawn,
						$this->data['lottery']->extra_ball,
						$selected_trends,
						$page,
						$per_page,
						$filepath  // Add filepath parameter
					);
					
					if ($filtered_combinations === false) {
						$this->data['message'] = 'No Combinations are available with the Up / Down Trend Filter';
						$this->data['combos_paginated'] = [];
						$this->data['pagination'] = [
							'current' => 1,
							'total' => 1,
							'per_page' => $per_page
						];
					} else {
						$updated_combinations = $filtered_combinations;
					}
				}
				// ... calculate stats and set $this->data['combos_paginated'] and $this->data['pagination'] ...
				$combos_paginated = [];
					foreach ($updated_combinations as $combo) {
						$stats = $this->predictions_m->get_combo_stats($combo, $drawn, $this->data['lottery']->last_drawn);
						$combos_paginated[] = [
							'combo' => $combo,
							'stats' => $stats
						];
					}
					$this->data['combos_paginated'] = $combos_paginated;
					// Paginate for display
					// For pagination controls, you still need the total number of lines in the file:
					$total_lines = count(file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
					$this->data['pagination'] = [
						'current' => $page,
						'total' => ceil($total_lines / $per_page),
						'per_page' => $per_page
					];
					$this->data['number_array'] = $number_array;
					$this->data['message'] = 'Combination Table and filters loaded successfully.';
			} else {
				$this->data['combos_paginated'] = [];
				$this->data['pagination'] = [
					'current' => 1,
					'total' => 1,
					'per_page' => $per_page
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
		// Grab the next draw date
		$ld = $this->data['lottery']->last_drawn['draw_date'];	// Return last draw date
		$day = $this->lotteries_m->return_day($ld);				// Returns the day of draw, Saturday, Sunday, etc.
		$this->data['lottery']->next_draw_date = $this->lotteries_m->next_date($this->data['lottery'], $day, $ld);
		// Load the view
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
        $countries = $this->predictions_m->get_countries($id);
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
        $prov_state = $this->predictions_m->get_prov_states($country_id);
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
        $lottery_games = $this->predictions_m->get_lottery_games($country_id, $province_id);
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
		if ($this->session->userdata('combination_file')) {
			$this->session->unset_userdata('combination_file');
		}
		
		// Now prepare default data similar to futures method
		$this->data['message'] = 'Settings have been reset successfully.';
		$this->data['disable_generate_button'] = true; // Used to disable the generate button in the view
		$this->data['lottery'] = $this->lotteries_m->get($id);
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		$drawn = $this->data['lottery']->balls_drawn;
		$this->data['country_code'] = $this->predictions_m->get_lottery_country($id);
		$this->data['state_prov_code'] = $this->predictions_m->get_lottery_state_prov($id);
		
		// Fetch combination files for the lottery
		$this->data['combination_files'] = $this->predictions_m->get_combination_files($id);
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
		
		$position_points = $this->predictions_m->get_sorted_position_points($this->data['lottery']->last_drawn, $drawn);
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
		
		// Get next draw date
		$ld = $this->data['lottery']->last_drawn['draw_date'];
		$day = $this->lotteries_m->return_day($ld);
		$this->data['lottery']->next_draw_date = $this->lotteries_m->next_date($this->data['lottery'], $day, $ld);
		
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
}