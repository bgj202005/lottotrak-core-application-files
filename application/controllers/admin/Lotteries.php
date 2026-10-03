<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lotteries extends Admin_Controller {
	
	const FILE_PATH = "lotto_zip_csv_uploads/";  // Directory of all uploaded and transferred lottery data files
	
	public function __construct() {
		 parent::__construct();
		 $this->load->dbforge();
		 $this->load->model('lotteries_m');
		 $this->load->model('statistics_m');
		 $this->load->helper('file');
		 $this->load->library('image_lib');
		 $this->load->library('pagination');
		 $this->load->model('maintenance_m');
		 //$this->load->library('CSV_Import');
		 //$this->output->enable_profiler(TRUE);
	}

	/**
	 * Retrieves List of All Lotteries
	 * 
	 * @param       none
	 * @return      none
	 */
	public function index() { 
		// Fetch all lotteries from the database
		// Include Pageination
		$count = $this->db->count_all_results('lottery_profiles');
		$perpage = 10;
		if ($count > $perpage) 
		{
			$config['base_url'] = site_url($this->uri->segment(1).'/lotteries/page');
			$config['total_rows'] = $count;
			$config['per_page'] = $perpage;
			$config['url_segment'] = 4;
			$this->pagination->initialize($config);
			$this->data['pagination'] = $this->pagination->create_links();
			$offset = $this->uri->segment(3);
		}
		else {
			$this->data['pagination'] = '';
			$offset = 0;
		} // End of Pagination
		$this->db->limit($perpage, $offset);
		$this->data['lotteries'] = $this->lotteries_m->get();
		
		// Fetch last draw data for each lottery
		foreach($this->data['lotteries'] as $lottery) 
		{
			$tbl_name = $this->lotteries_m->lotto_table_convert($lottery->lottery_name);
			$lottery->last_date = $this->statistics_m->last_date($tbl_name);
			$lottery->last_draw = $this->statistics_m->last_draw($tbl_name, $lottery->balls_drawn, $lottery->extra_ball);
		}

		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the lotteries menu
		$this->session->set_userdata('uri', 'admin/'.$this->data['current']);
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->data['subview'] = 'admin/lotteries/index';
		if ($this->session->flashdata('message')) $this->data['message'] = $this->session->flashdata('message');
		else $this->data['message'] = '';
		$this->load->view('admin/_layout_main', $this->data);
	}

	public function page()
	{
		$count = $this->db->count_all_results('lottery_profiles');
		$perpage = 10;	// 10 Lotteries per page
		if ($count > $perpage) 
		{
			$config['base_url'] = site_url($this->uri->segment(1).'/lotteries/page');
			$config['total_rows'] = $count;
			$config['per_page'] = $perpage;
			$config['url_segment'] = 4;
			$this->pagination->initialize($config);
			$this->data['pagination'] = $this->pagination->create_links();
			$offset = $this->uri->segment(4);
		}
		else {
			$this->data['pagination'] = '';
			$offset = 0;
		} // End of Pagination
		$this->db->limit($perpage, $offset);
		$this->data['lotteries'] = $this->lotteries_m->get();
		
		// Fetch last draw data for each lottery
		foreach($this->data['lotteries'] as $lottery) 
		{
			$tbl_name = $this->lotteries_m->lotto_table_convert($lottery->lottery_name);
			$lottery->last_date = $this->statistics_m->last_date($tbl_name);
			$lottery->last_draw = $this->statistics_m->last_draw($tbl_name, $lottery->balls_drawn, $lottery->extra_ball);
		}

		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the lotteries menu
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->data['subview'] = 'admin/lotteries/index';
		if ($this->session->flashdata('message')) $this->data['message'] = $this->session->flashdata('message');
		else $this->data['message'] = '';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function edit($id = NULL) {
		// Initialize message type (default to warning)
		$this->data['message_type'] = 'warning';
		
		// Check for flashdata messages first (from redirects like delete_prior_draws)
		if ($this->session->flashdata('message')) {
			$this->data['message'] = $this->session->flashdata('message');
		} elseif ($this->session->flashdata('error')) {
			$this->data['message'] = $this->session->flashdata('error');
			$this->data['message_type'] = 'danger';
		}
		// Check if user cancelled parameter change confirmation
		elseif ($this->input->get('cancelled') == '1' && $id) {
			$this->data['message'] = 'Update cancelled. No changes were made to the lottery profile.';
			$this->data['message_type'] = 'info'; // Info message for cancellation
		} else {
			$this->data['message'] = '';  // Create a Message object
		}
		
		// Fetch a lottery profile or create a new one
		if ($id) {
			$this->data['lottery'] = $this->lotteries_m->get($id);
			is_array($this->data['lottery']) || $this->data['errors'][] = 'Lottery Profile could not be found';
			// Retrieve the lottery table name for the database
			$table = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
			 // Check for prior draws
    		$this->data['has_prior_draws'] = $this->lotteries_m->check_prior_draws($table, $this->data['lottery']->firstdate);
			// Store original lottery data for comparison
			$this->data['original_lottery'] = clone $this->data['lottery'];
		} else {
			//load file helper
			$this->data['lottery'] = $this->lotteries_m->get_new();
			$this->data['has_prior_draws'] = FALSE; // No Prior Draws
			$this->data['original_lottery'] = NULL;
		}
		$this->data['requires_confirmation'] = FALSE; // Flag for showing confirmation modal
		$error = NULL;				  // Related to Image upload only
		// Setup the form

		if (isset($_FILES['lottery_image'])) //File being uploaded
		{
			// Setup for File Uploads
			$config['upload_path']          = 'images/uploads/'; 
			$config['allowed_types']        = 'gif|jpg|png|jpeg';
			$config['max_size']             = '0'; /* 10000; */
			$config['max_width']            = '0'; /* 2048; */
			$config['max_height']           = '0'; /* 1536; */
			$config['overwrite'] 	        = TRUE;
			$this->load->library('upload', $config); 
			/* $this->load->initialize($config); */
			$image_field_name = 'lottery_image';
			if ($_FILES['lottery_image']['error']!=4)  // Did not select a file to upload.  Indicates the file browse was not selected, 
			// so no image required to upload
			{ 
				if (!$this->upload->do_upload($image_field_name))
				{
					$error = array('error' => $this->upload->display_errors());
				}
				else
				{
					$image_data = $this->upload->data();
					$config['image_library'] = 'gd2';
					$config['source_image'] = 'images/uploads/'.$image_data["raw_name"].$image_data['file_ext'];
					$config['new_image'] = 'images/uploads/'.$image_data["raw_name"].$image_data['file_ext'];
					$config['create_thumb'] = FALSE;
					$config['maintain_ratio'] = TRUE;
					$config['width']         = 175;
					$config['height']       = 175;
					$this->image_lib->initialize($config);  // Change the dimentions keeping the proportions
					$this->image_lib->resize();
				}
			} 
		}
		$_POST['extra_ball'] = (is_null($this->input->post('extra_ball')) ? 0 : 1);
		$_POST['duplicate_extra_ball'] = (is_null($this->input->post('duplicate_extra_ball')) ? 0 : 1);
		$_POST['enabled'] = (is_null($this->input->post('enabled')) ? 0 : 1);

		$rules = $this->lotteries_m->rules;
		$this->form_validation->set_rules($rules);
		
		if ($this->form_validation->run() == TRUE&&is_null($error)) {
 			$_POST['lottery_image'] = (isset($_FILES['lottery_image']['name']) && !empty($_FILES['lottery_image']['name']) ? $_FILES['lottery_image']['name'] : ''); 
			
			// Preserve the original image if no new image was uploaded
			if (empty($_POST['lottery_image']) && isset($_POST['image']) && !empty($_POST['image'])) {
				$_POST['lottery_image'] = $_POST['image'];
			}
			
			$firstdate = new DateTime($_POST['firstdate']); 
			$_POST['firstdate'] = $firstdate->format('Y-m-d');
			$lastdate = new DateTime($_POST['lastdate']);
			$_POST['lastdate'] = $lastdate->format('Y-m-d');
			// We can save and redirect
			$data = $this->lotteries_m->array_from_post ( array (
					'lottery_name',
					'lottery_description',
					'balls_drawn',
					'lottery_state_prov',
					'lottery_country_id',
					'lottery_image',
					'minimum_ball',
					'maximum_ball',
					'extra_ball',
					'duplicate_extra_ball',
					'minimum_extra_ball',
					'maximum_extra_ball',
					'prediction_min_range',
					'monday',
					'tuesday',
					'wednesday',
					'thursday',
					'friday',
					'saturday',
					'sunday',
					'firstdate',
					'lastdate',
					'enabled'
			) );

			// Check if critical parameters have changed (only for existing lotteries)
			if ($id && $this->has_critical_parameter_changed($this->data['original_lottery'], $data)) {
				// Check if user has confirmed the deletion
				$confirmed = $this->input->post('confirm_data_deletion');
				
				if ($confirmed === 'no') {
					// User declined to clear data - cancel the save completely
					$this->session->set_flashdata('message', 'Lottery profile update cancelled. Critical parameter changes require clearing historical data. No changes were saved.');
					redirect('admin/lotteries/edit/' . $id);
					return;
				} elseif ($confirmed !== 'yes') {
					// Check if the first draw date has been changed
					// Critical parameter changes require a new starting date for the lottery
					$original_firstdate = date('Y-m-d', strtotime($this->data['original_lottery']->firstdate));
					$new_firstdate = $data['firstdate'];
					
					if ($original_firstdate === $new_firstdate) {
						// First draw date hasn't changed - require the administrator to update it
						$this->data['message'] = '<strong>REQUIRED:</strong> You must update the <strong>First Draw Date</strong> when changing critical lottery parameters. '
							. 'The old draw history will be deleted, so you need to specify a new starting date for this lottery configuration. '
							. 'Please adjust the First Draw Date before proceeding.';
						$this->data['firstdate_change_required'] = TRUE;
						// Update lottery object with pending changes so form shows new values
						$this->data['lottery'] = $this->lotteries_m->array_to_object($this->data['lottery'], $data);
						goto skip_save;
					}
					
					// First draw date has been changed - proceed with confirmation modal
					$this->data['requires_confirmation'] = TRUE;
					$this->data['pending_changes'] = $data;
					$this->data['message'] = '';
					
					// Fetch the date range of draws that will be affected
					$first_draw = $this->lotteries_m->first_draw_db($this->data['lottery']->lottery_name);
					$last_draw = $this->lotteries_m->last_draw_db($this->data['lottery']->lottery_name);
					$total_draws = $this->lotteries_m->count_draws_db($this->data['lottery']->lottery_name);
					
					if ($first_draw && $first_draw !== 'nodraws' && $last_draw && $last_draw !== 'nodraws') {
						$this->data['affected_draw_range'] = array(
							'first_date' => $first_draw->draw_date,
							'last_date' => $last_draw->draw_date,
							'first_draw_id' => isset($first_draw->id) ? $first_draw->id : 1,
							'last_draw_id' => isset($last_draw->id) ? $last_draw->id : $total_draws,
							'total_draws' => $total_draws
						);
					} else {
						$this->data['affected_draw_range'] = null;
					}
					
					// Update lottery object with pending changes so form shows new values
					$this->data['lottery'] = $this->lotteries_m->array_to_object($this->data['lottery'], $data);
					// Don't save yet, show confirmation first
					goto skip_save;
				} else {
					// User confirmed (yes) - clear historical data and proceed with save
					$this->clear_historical_prediction_data($id);
					$this->data['message'] = 'Historical prediction data has been cleared due to parameter changes. ';
				}
			}
			$data['lottery_image'] = (empty($data['lottery_image']) && isset($_POST['image']) ? $_POST['image'] : $data['lottery_image']);  // Only if not updating the image
			foreach ($data as $key => $value)
			{
				if(intval($value)&&(!$this->is_valid_date($value))) // If the value is an integer and not a date	
				{
					$data[$key] = intval($value);
				}
				if(is_null($value)||empty($value)&&($key!='lottery_state_prov'&&$key!='lottery_image'&&$key!='enabled')) $data[$key] = 0;  // Revert from NULL to 0 only or FALSE (int 0)
			}
			$this->data['lottery'] = $this->lotteries_m->array_to_object($this->data['lottery'], $data);
			$this->data['lottery']->id = $this->lotteries_m->save($data, $id);
			if (!$id) $this->lotteries_m->create_lottery_db($data);
			else $this->lotteries_m->update_lottery_db($data);

			$this->data['message'] .= (is_null($id) ? "The Lottery Profile has been added to the Database." : "The ".$this->data['lottery']->lottery_name." Profile has been updated.");
			
			skip_save: // Label for skipping save when confirmation is needed
		}
		else 
		{
			$this->data['message'] = isset($error['error']);  // Only Errors associated with Uploading an image.
		}
		// Load the View
		if(($this->data['lottery']->extra_ball&&!$this->data['lottery']->minimum_extra_ball)||(!$this->data['lottery']->extra_ball)) $this->data['lottery']->minimum_extra_ball = '';
		if(($this->data['lottery']->extra_ball&&!$this->data['lottery']->maximum_extra_ball)||(!$this->data['lottery']->extra_ball)) $this->data['lottery']->maximum_extra_ball = '';
		if ($id) $this->data['lastdraw'] = $this->lotteries_m->last_draw_db($this->data['lottery']->lottery_name);
		if(isset($this->data['lastdraw']->draw_date)) {
			if((strtotime($this->data['lastdraw']->draw_date)!=(strtotime($this->data['lottery']->lastdate)))) $this->data['lottery']->lastdate=$this->data['lastdraw']->draw_date;
		}
		
		// Calculate repeater numbers for display
		$this->data['repeater_display'] = 'None (0)';
		$this->data['max_last'] = 'N/A';
		if ($id && isset($this->data['lastdraw']->draw_date) && $this->data['lastdraw'] !== 'nodraws') {
			$repeaters = $this->get_repeater_numbers($this->data['lottery'], $this->data['lastdraw']);
			if (!empty($repeaters)) {
				sort($repeaters);
				$this->data['repeater_display'] = implode(' ', $repeaters) . ' (' . count($repeaters) . ')';
			}
			// Calculate Max Last (maximum repeating last digits)
			$this->data['max_last'] = $this->calculate_max_last($this->data['lottery'], $this->data['lastdraw']);
		}
		// Pass the retrieved values to the view
    	$this->data['lottery_country_id'] = $this->data['lottery']->lottery_country_id ?? 'CA'; // Default to Canada if not set
	    $this->data['lottery_state_prov'] = $this->data['lottery']->lottery_state_prov ?? ''; // Default to empty if not set
		$this->data['current'] = $this->uri->segment(2); // Sets the Lottery Menu as Active
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/edit'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->data['subview']  = 'admin/lotteries/edit';
		$this->load->view('admin/_layout_main', $this->data);
	}
	/**
	 * Function to check if a string is a valid date using strtotime
	 * @param       $date_str
	 * @return      $timestamp	// Returns a timestamp if the date is valid
	 */
	public function is_valid_date($date_str) {
    $timestamp = strtotime($date_str);
    return $timestamp !== false;
	}
	/**
	 * Handle User's Response and Delete Prior Draws
	 * If the user confirms, the prior draws are deleted from the specified table.
	 * A success or no-action message is set in the session and the user is redirected back to the edit page.
	 * @param none
	 * @return none
	 */
	public function delete_prior_draws() {
		$lottery_id = $this->input->post('lottery_id');
		$start_date = $this->input->post('start_date');
		$confirm = $this->input->post('confirm');
		
		// Validate required inputs
		if (!$lottery_id || !$start_date || !$confirm) {
			log_message('error', 'delete_prior_draws - Missing required parameters');
			$this->session->set_flashdata('error', 'Missing required parameters for deletion.');
			redirect('admin/lotteries/edit/' . ($lottery_id ?: ''));
			return;
		}

		if ($confirm === 'Y') {
			// Get the lottery by ID to get the actual lottery name
			$lottery = $this->lotteries_m->get($lottery_id);
			
			if (!$lottery) {
				$this->session->set_flashdata('error', 'Lottery not found.');
				redirect('admin/lotteries/edit/' . $lottery_id);
				return;
			}
			
			// Convert lottery name to table name (e.g., "Lotto Max" -> "lotto_max")
			$table_name = $this->lotteries_m->lotto_table_convert($lottery->lottery_name);
			
			// Check if the table exists
			if (!$this->db->table_exists($table_name)) {
				$this->session->set_flashdata('error', 'Lottery draw table does not exist: ' . $table_name);
				redirect('admin/lotteries/edit/' . $lottery_id);
				return;
			}
			
			// Count draws that will be deleted
			$this->db->where('draw_date <', $start_date);
			$count_to_delete = $this->db->count_all_results($table_name);
			
			// Delete the draws
			$this->db->where('draw_date <', $start_date);
			$this->db->delete($table_name);
			$deleted_count = $this->db->affected_rows();
			
			// Check if there are any remaining draws
			$remaining_draws = $this->db->count_all($table_name);
			
			if ($remaining_draws == 0) {
				// No draws left - update lottery profile to reflect this
				$update_data = array(
					'lastdate' => NULL
				);
				
				$this->db->where('id', $lottery_id);
				$this->db->update('lottery_profiles', $update_data);
				
				// Clear all prediction data since there are no draws
				$this->clear_historical_prediction_data($lottery_id);
				
				// Clear statistics cache
				if (isset($this->statistics_m)) {
					$this->statistics_m->clear_cache($table_name);
				}
				
				$this->session->set_flashdata('message', "All prior draws ($deleted_count) have been deleted. The lottery has no remaining draws. Last Date and Numbers have been set to N/A.");
			} else {
				// Some draws remain - update the lastdate to the most recent draw
				$this->db->select('MAX(draw_date) as max_date');
				$this->db->from($table_name);
				$query = $this->db->get();
				$result = $query->row();
				
				if ($result && $result->max_date) {
					$update_data = array(
						'lastdate' => $result->max_date
					);
					
					$this->db->where('id', $lottery_id);
					$this->db->update('lottery_profiles', $update_data);
				}
				
				// Clear prediction data as the draw history has changed
				$this->clear_historical_prediction_data($lottery_id);
				
				// Clear statistics cache
				if (isset($this->statistics_m)) {
					$this->statistics_m->clear_cache($table_name);
				}
				
				$this->session->set_flashdata('message', "$deleted_count draws prior to $start_date have been deleted successfully. $remaining_draws draws remain in the database.");
			}
		} else {
			$this->session->set_flashdata('message', 'No draws were deleted.');
		}
		
		redirect('admin/lotteries/edit/' . $lottery_id);
	}
	
	/**
	 * Lottery Prize Breakdown
	 * 
	 * @param       $id, Lottery id
	 * @return      none
	 */

	public function prizes($id)
	/**
	 * Displays the Prizes Page
	 * 
	 * @param       integer		$id, Lottery id of current lottery
	 * @return      none
	 */
	{
		$this->data['lottery'] = $this->lotteries_m->get($id);
		$this->data['message'] = '';	// Defaulted to No Error Messages
		$this->data['lottery']->set_prizes = $this->lotteries_m->load_prizes($id); // Load the prizes first
		if($this->input->post(NULL, TRUE))
		{
			$post_prizes = array();
			if ($this->input->post('9_win')!==NULL) $post_prizes += ['9_win' => '1'];
			if ($this->input->post('8_win_extra')!==NULL) $post_prizes += ['8_win_extra' => '1'];
			if ($this->input->post('8_win')!==NULL) $post_prizes += ['8_win' => '1'];
			if ($this->input->post('7_win_extra')!==NULL) $post_prizes += ['7_win_extra' => '1'];
			if ($this->input->post('7_win')!==NULL) $post_prizes += ['7_win' => '1'];
			if ($this->input->post('6_win_extra')!==NULL) $post_prizes += ['6_win_extra' => '1'];
			if ($this->input->post('6_win')!==NULL) $post_prizes += ['6_win' => '1'];
			if ($this->input->post('5_win_extra')!==NULL) $post_prizes += ['5_win_extra' => '1'];
			if ($this->input->post('5_win')!==NULL) $post_prizes += ['5_win' => '1'];
			if ($this->input->post('4_win_extra')!==NULL) $post_prizes += ['4_win_extra' => '1'];
			if ($this->input->post('4_win')!==NULL) $post_prizes += ['4_win' => '1'];
			if ($this->input->post('3_win_extra')!==NULL) $post_prizes += ['3_win_extra' => '1'];
			if ($this->input->post('3_win')!==NULL) $post_prizes += ['3_win' => '1'];
			if ($this->input->post('2_win_extra')!==NULL) $post_prizes += ['2_win_extra' => '1'];
			if ($this->input->post('2_win')!==NULL) $post_prizes += ['2_win' => '1'];
			if ($this->input->post('1_win_extra')!==NULL) $post_prizes += ['1_win_extra' => '1'];
			if ($this->input->post('1_win')!==NULL) $post_prizes += ['1_win' => '1'];
			if ($this->input->post('extra')!==NULL) $post_prizes += ['extra' => '1'];
			$prize_rules = $this->lotteries_m->prize_rules;
			$this->form_validation->set_rules($prize_rules);
			if ($this->form_validation->run() == TRUE) 
			{
				$this->data['lottery'] = $this->lotteries_m->array_to_object($this->data['lottery'], $post_prizes);
				$post_prizes = $this->lotteries_m->prize_nulled($post_prizes, $this->data['lottery']->set_prizes); // Any updates?
				if(!empty($post_prizes)) $post_prizes += ['lottery_id' => $id];
				$this->lotteries_m->prizes_data_save($post_prizes);
				$this->data['message'] = "Prize Categories have been saved.";
			}
			else
			{
				$this->data['message'] = "No Prize Category is selected. Please enter at least one prize category.";
			}
		}
		$this->data['lottery']->prizes = $this->lotteries_m->list_prizes($this->data['lottery']->balls_drawn, $this->data['lottery']->extra_ball);
		// $this->data['lottery']->set_prizes = $this->lotteries_m->load_prizes($id);
			
		$this->data['current'] = $this->uri->segment(2);
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/prizes');
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	  
		$this->data['subview']  = 'admin/lotteries/prizes';
		$this->load->view('admin/_layout_main', $this->data); 
	}
	public function import($id)
	{
		$this->data['lottery'] = $this->lotteries_m->get($id);
		$import_results = $this->lotteries_m->import_data_retrieve($id); // False or import result objects
		if ($import_results) {
			$this->data['import_data'] = $import_results;
			if (!empty($import_results[0]->columns)) $columns = explode(',', $import_results[0]->columns);
		}
		// Retrieve the lottery table name for the database
		$this->data['lottery']->table_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		// Check for existing lottery draws
		$this->data['lottery']->last_draw = $this->lotteries_m->last_draw_db($this->data['lottery']->lottery_name);
	
		$zero_extra = (is_null($this->input->post('allow_zero_extra')) ? 0 : 1);
		$this->data['lottery']->zero_extra = $zero_extra;
		$this->session->set_userdata(array(
			'table_name' => $this->data['lottery']->table_name,
			'last_draw' => $this->data['lottery']->last_draw,
			'balls_drawn' => $this->data['lottery']->balls_drawn,
			'minimum_ball' => $this->data['lottery']->minimum_ball,
			'maximum_ball' => $this->data['lottery']->maximum_ball,
			'minimum_extra_ball' => $this->data['lottery']->minimum_extra_ball,
			'maximum_extra_ball' => $this->data['lottery']->maximum_extra_ball,
			'extra_ball' => $this->data['lottery']->extra_ball,
			'duplicate_extra' => $this->data['lottery']->duplicate_extra_ball,
			'allow_zero_extra' => $zero_extra,
			'firstdate' => $this->data['lottery']->firstdate
		));
	
		if (is_array($this->input->post("csv_field")) && count($this->input->post("csv_field"))) {
			$n = count($this->input->post("csv_field"));
			$csv_filter = ''; // No Filter Elimination at this point
			if ($n > 0) // There are currently fields that we can't import into the database
			{
				$this->session->set_userdata(array('elim' => $_POST['csv_field']));
				foreach ($this->input->post("csv_field") as $filter => $key) {
					$csv_filter .= $key . ',';
				}
				$csv_filter = substr($csv_filter, 0, -1);
			}
		}
	
		$this->data['message'] = '';  // Create a Message object
		$error = NULL;                // Related to Image upload only
	
		if (!empty($this->input->post('hidden_field'))) {
			if (empty($this->input->post('import_lottery_url'))) {
				$error = '';
				$total_data = '';
				$allowed_extension = array('csv');
				$file_array = explode(".", $_FILES["lottery_upload_csv"]["name"]);
				$extension = end($file_array);
	
				if ($_FILES['lottery_upload_csv']['name'] != '') {
					if (in_array($extension, $allowed_extension)) {
						$new_file_name = rand() . '.' . $extension;
						$this->session->set_userdata(array('new_file_name' => $new_file_name));
						move_uploaded_file($_FILES['lottery_upload_csv']['tmp_name'], self::FILE_PATH . $new_file_name);
						$file_content = array_map('str_getcsv', file(self::FILE_PATH . $new_file_name, FILE_SKIP_EMPTY_LINES));
	
						// Retrieve the column index for the draw date from the CSV header
						$header = array_shift($file_content); // Remove the header row
						$draw_date_column_index = $this->lotteries_m->get_draw_date_column_index($header);
	
						if ($draw_date_column_index === false) {
							// Handle error: draw_date column not found
							echo json_encode(['error' => 'Draw date column not found in the import data.']);
							return;
						}
	
						// Retrieve firstdate from session
						$firstdate = $this->session->userdata('firstdate');
						$firstdate_timestamp = strtotime($firstdate);
	
						// Filter out draws prior to the start date
						$filtered_content = array_filter($file_content, function ($row) use ($firstdate_timestamp, $draw_date_column_index) {
							$csv_date = (strpos($row[$draw_date_column_index], '-')) ? explode('-', $row[$draw_date_column_index]) : explode('/', $row[$draw_date_column_index]); // Assuming date is in the specified column
							$unix_date = (isset($csv_date[2]) && isset($csv_date[1]) && isset($csv_date[0])) ? strtotime($csv_date[0] . '/' . $csv_date[1] . '/' . $csv_date[2]) : FALSE; // m / d / yyyy is assumed with '/'
							return $unix_date >= $firstdate_timestamp;
						});
	
						// Count the valid draws
						$total_data = count($filtered_content);
	
						$this->lotteries_m->import_data_save(array('lottery_id' => $id, 'columns' => $csv_filter, 'zero_extra' => $zero_extra, 'csv_file' => $_FILES['lottery_upload_csv']['name'], 'csv_url' => ''));
					} else {
						$error = 'Only CSV File Format is allowed';
					}
				} else {
					$error = 'Please Select File';
				}
				if ($error != '') {
					$output = array(
						'error' => $error
					);
				} else {
					$output = array(
						'success' => TRUE,
						'total_data' => ($total_data - 1)
					);
				}
			} else {
				$url = $this->input->post('import_lottery_url');
				$url = strtok($url, '?'); // Remove the query string
	
				/*  1. Check if the File has been selected (Uploading is first examined) 
					check for a valid url (http: or https:) and active on the internet 
				2.  if valid, copy file to server at uploaded location csv_zip_upload
					If Filename is valid zip file */
	
				if ($this->lotteries_m->is_valid_domain($url)) {
					$file_path = explode(".", $url);
					$ext = end($file_path);
					if (($ext != "csv") && ($ext != "zip")) {
						$output = array(
							'error' => $url . ' has neither a csv or zip file extention for transfer to our server.'
						);
					} else {
						// Yes, it is either a csv or zip file type
						// Download it to the correct directory
						// Create stream context with custom user agent
						$opts = [
							'http' => [
								'method' => 'GET',
								'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
								'timeout' => 30
							]
						];
						$context = stream_context_create($opts);
						$url_filename = self::FILE_PATH . basename($url);
						$rw = file_put_contents($url_filename, fopen($url, 'r', false, $context));  // Transfer the contents of file to server in directory
						if (!$rw) {
							$output = array(
								'error' => $url . ' does not exist. Please check the url again.'
							);
						} else {
							/* if valid, copy file to server at uploaded location csv_zip_upload
							If Filename is valid zip file
							unzip in directory, uncompress csv file
							delete current zip file
							open csv file */
	
							if ($ext == 'zip') {
								## Extract the zip file ---- start
								$zip = new ZipArchive;
								$res = $zip->open($url_filename);
								if ($res === TRUE) {
									// Extract file
									$zip->extractTo(self::FILE_PATH);
									$unzip_name = $zip->getNameIndex(0);    // Returns the name of the compressed file     
									$zip->close();
									$unzip_ext = explode(".", $unzip_name);
									$unzip_ext = end($unzip_ext);
	
									if ($unzip_ext == 'csv') {
										$this->session->set_userdata(array('new_file_name' => $unzip_name));
										$file_content = array_map('str_getcsv', file(self::FILE_PATH . $unzip_name, FILE_SKIP_EMPTY_LINES));
	
										// Retrieve the column index for the draw date from the CSV header
										$header = array_shift($file_content); // Remove the header row
										$draw_date_column_index = $this->lotteries_m->get_draw_date_column_index($header);
	
										if ($draw_date_column_index === false) {
											// Handle error: draw_date column not found
											echo json_encode(['error' => 'Draw date column not found in the import data.']);
											return;
										}
	
										// Retrieve firstdate from session
										$firstdate = $this->session->userdata('firstdate');
										$firstdate_timestamp = strtotime($firstdate);
	
										// Filter out draws prior to the start date
										$filtered_content = array_filter($file_content, function ($row) use ($firstdate_timestamp, $draw_date_column_index) {
											$csv_date = (strpos($row[$draw_date_column_index], '-')) ? explode('-', $row[$draw_date_column_index]) : explode('/', $row[$draw_date_column_index]); // Assuming date is in the specified column
											$unix_date = (isset($csv_date[2]) && isset($csv_date[1]) && isset($csv_date[0])) ? strtotime($csv_date[0] . '/' . $csv_date[1] . '/' . $csv_date[2]) : FALSE; // m / d / yyyy is assumed with '/'
											return $unix_date >= $firstdate_timestamp;
										});
	
										// Count the valid draws
										$total_data = count($filtered_content);
	
										$output = array(
											'success' => TRUE,
											'total_data' => ($total_data - 1)
										);
										// Remove the zip file (dot zip in the directory) from the directory
										unlink($url_filename);
										$this->lotteries_m->import_data_save(array('lottery_id' => $id, 'columns' => $csv_filter, 'zero_extra' => $zero_extra, 'csv_file' => '', 'csv_url' => $url));
									} else {
										$output = array(
											'error' => 'This is not a valid csv file extension.'
										);
									}
								} else {
									$output = array(
										'error' => "Can't Open Zip File, Try Again."
									);
								}
							}
						}
					}
				} else {
					// Correct this url
					$output = array(
						'error' => $url . ' is not an active and valid url.'
					);
				}
			}
			echo json_encode($output);
		} else {
			if (!empty($columns)) $this->data['columns'] = $columns;
			$this->data['current'] = $this->uri->segment(2);
			$this->session->set_userdata('uri', 'admin/' . $this->data['current'] . '/import' . ($id ? '/' . $id : ''));
			$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
			$this->data['users'] = $this->maintenance_m->logged_online(0);    // Members
			$this->data['admins'] = $this->maintenance_m->logged_online(1);    // Admins
			$this->data['visitors'] = $this->maintenance_m->active_visitors();    // Active Visitors excluding users and admins        
			$this->data['subview'] = 'admin/lotteries/import';
			$this->load->view('admin/_layout_main', $this->data);
		}
	}

	public function import_process($id)
	{
		//$this->data['lottery'] = $this->lotteries_m->get($id);
		$lottery_props = (object) [
			'balls_drawn'			=> $this->session->userdata('balls_drawn'),
			'minimum_ball'			=> $this->session->userdata('minimum_ball'),
			'maximum_ball'			=> $this->session->userdata('maximum_ball'),
			'minimum_extra_ball'	=> $this->session->userdata('minimum_extra_ball'),
			'maximum_extra_ball'	=> $this->session->userdata('maximum_extra_ball'),
			'extra_ball'			=> $this->session->userdata('extra_ball'),
			'duplicate'				=> $this->session->userdata('duplicate_extra'),
			'allow_zero_extra'		=> $this->session->userdata('allow_zero_extra')
		];

		// Retrieve the lottery table name for the database
		$table = $this->session->userdata('table_name');
		
		// CRITICAL: Refresh last_draw from database to ensure we have current state
		// This prevents issues where session has old data after table truncation
		$lottery = $this->lotteries_m->get($id);
		if ($lottery) {
			$current_last_draw = $this->lotteries_m->last_draw_db($lottery->lottery_name);
			$this->session->set_userdata('last_draw', $current_last_draw);
			log_message('info', "Refreshed last_draw from database: " . ($current_last_draw === 'nodraws' ? 'nodraws' : 'has draws'));
		} 
		// Enhanced server compatibility headers and settings
		header('Content-type: text/html; charset=utf-8');
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Connection: keep-alive");
		
		// More aggressive timeout and memory settings for hosting servers
		set_time_limit(0);
		ini_set('memory_limit', '256M'); // Increase memory limit
		ini_set('max_execution_time', 0);
		
		// Force output buffering for hosting server compatibility
		if (ob_get_level()) {
			ob_end_clean();
		}
		ob_start();
		ob_implicit_flush(1);
		
		// Counter for batch processing
		$processed_count = 0;
		$batch_size = 5; // Even smaller batches for maximum server stability
		$heartbeat_counter = 0; // Counter for regular status updates
		
		if (!empty($this->session->userdata('new_file_name')))
		{
			// CRITICAL: Ensure table structure is ready BEFORE any processing
			// Add h_w_c column if it doesn't exist (must be done before pre-scan)
			$col_check = $this->db->query("SHOW COLUMNS FROM `{$table}` LIKE 'h_w_c'");
			if ($col_check->num_rows() === 0)
			{
				$this->db->query("ALTER TABLE `{$table}` ADD COLUMN `h_w_c` VARCHAR(20) NULL DEFAULT NULL");
				log_message('info', "Added h_w_c column to {$table} before import");
			}
			
			// OPTIMIZATION: Pre-scan CSV to identify only records that need importing
			$file_data = fopen(self::FILE_PATH.$this->session->userdata('new_file_name'), 'r');
			if (!$file_data) {
				echo json_encode(['error' => TRUE, 'message' => 'Failed to open CSV file']);
				return;
			}
			
			$header = fgetcsv($file_data); // Set the File Pointer to start of file and move retrieve the header
			$column_count = count($header);
			$draw_data = array();
			$elim = array_fill(0, $column_count, TRUE);	// All values set to TRUE
						
			if (is_array($this->session->userdata('elim')))
			{
				do
				{
					foreach($this->session->userdata('elim') as $column => $key)
					{
						if($key!=""&&(intval(trim($key)))==$column_count-1) $elim[$column_count-1] = FALSE;
					}	
					$column_count--;
				} while($column_count>=0);
			} 
			
			// Retrieve firstdate and last draw info from session
    		$firstdate = $this->session->userdata('firstdate');
    		$firstdate_timestamp = strtotime($firstdate);
			$ld = $this->session->userdata('last_draw');
			// Handle 'nodraws' case or when last_draw is invalid
			if ($ld === 'nodraws' || $ld === FALSE || !is_object($ld)) {
				$last_draw_timestamp = 0; // No previous draws, import everything after firstdate
			} else {
				$last_draw_timestamp = strtotime($ld->draw_date);
			}
			
			// PHASE 1: Pre-scan CSV to collect only records that need importing
			$records_to_import = array();
			$total_csv_records = 0;
			$skipped_records = 0;
			$already_imported = 0;
			
			while($row = fgetcsv($file_data)) 
			{
				$total_csv_records++;
				
				// Apply column elimination
				$temp_row = $row; // Keep original for processing
				$column_count = count($elim);
				$i = 0;
				while ($i!=$column_count)
				{
					if(!$elim[$i])
					{
						unset($temp_row[$i]);	// Remove this csv column
					}
					$i++;
				}
				$temp_row = array_values($temp_row);	// Reindex the row without the eliminated column
				
				// Parse the date
				$csv_date = (strpos($temp_row[0], '-')) ? explode('-', $temp_row[0]) : explode('/', $temp_row[0]);
				$unix_date = (isset($csv_date[2])&&isset($csv_date[1])&&isset($csv_date[0]) ? strtotime($csv_date[0].'/'.$csv_date[1].'/'.$csv_date[2]) : FALSE);
				
				// Skip records before the first date
				if ($unix_date < $firstdate_timestamp) {
					$skipped_records++;
					continue;
				}
				
				// Skip records that are already imported (before or equal to last draw date)
				if ($last_draw_timestamp > 0 && $unix_date <= $last_draw_timestamp) {
					$already_imported++;
					continue;
				}
				
				// Quick check if this draw already exists in database
				$draw_exists = FALSE;
				if ($unix_date) {
					try {
						$draw_exists = $this->lotteries_m->lotto_draw_exists($table, $this->lotteries_m->drawn_only($temp_row), $lottery_props->extra_ball, date('Y-m-d', $unix_date));
					} catch (Exception $e) {
						// If error checking existence, assume it doesn't exist and try to import
						log_message('error', "Error checking draw existence: " . $e->getMessage());
						$draw_exists = FALSE;
					}
				}
				
				if ($draw_exists) {
					$already_imported++;
					continue;
				}
				
				// This record needs to be imported
				$records_to_import[] = $temp_row;
				
				// Log progress every 100 records during scan
			}
			
			fclose($file_data);
			
			// If no records to process, exit early
			if (empty($records_to_import)) {
				log_message('info', "No new records to import for table: {$table}. Total CSV: {$total_csv_records}, Skipped: {$skipped_records}, Already imported: {$already_imported}, Last draw timestamp: {$last_draw_timestamp}, Firstdate timestamp: {$firstdate_timestamp}");
				echo json_encode(array('exit' => TRUE));
				return;
			}
			
			log_message('info', "Import process starting for table: {$table}. Records to import: " . count($records_to_import));
			
			// PHASE 2: Process only the records that need importing
			// Note: h_w_c column already added at the start of this function

			foreach ($records_to_import as $row_index => $row) {
				$csv_date = (strpos($row[0], '-')) ? explode('-', $row[0]) : explode('/', $row[0]);
				$unix_date = (isset($csv_date[2])&&isset($csv_date[1])&&isset($csv_date[0]) ? strtotime($csv_date[0].'/'.$csv_date[1].'/'.$csv_date[2]) : FALSE);
				
				$balls_drawn = intval($this->session->userdata('balls_drawn'));
				$draw_data = array();
				
				$c = 1; // array counter
				for ($balls_drawn; $balls_drawn>0; $balls_drawn--) 
				{
					$draw_data['ball'.$c] =  $row[$c];
					$c++;	// Increment row count
				}
				
				if (!empty($lottery_props->extra_ball)) $draw_data['extra'] = $row[$c];

				$draw_data += ['draw_date'	 =>	$row[0], 
								'lottery_id' => $id];
				// Check the draw date to make sure it is in the correct format for Month / Day / Year
				if (intval($csv_date[1])<1||(intval($csv_date[1]>12))) // Month between 1 and 12
				{
					$draw_data += [
						'month_error'	=>	TRUE];
					break;
				} 
				else if (intval($csv_date[2])<1&&(intval($csv_date[2])<=cal_days_in_month(CAL_GREGORIAN, $csv_date[1], $csv_date[0])))
				{
					$draw_data += [
						'day_error'	=>	TRUE];
					break;
				} 
				else if (intval($csv_date[0]<1)||intval($csv_date[0])>intval(date("Y")))
				{
					$draw_data += [
						'year_error'	=>	TRUE];
					break;
				}
				else if (!$this->lotteries_m->range_check($draw_data, $lottery_props)) 
				{
					$draw_data += [
							'range_error'	=>	TRUE];
					break;
				}
				else if (!$this->lotteries_m->duplicate_extra_check($lottery_props->balls_drawn, $draw_data, $lottery_props->duplicate)) 
				{
					$draw_data += [
							'duplicate_error'	=>	TRUE];
					break;
				}
				else if (!$this->lotteries_m->zero_extra_check($lottery_props->extra_ball, $draw_data['extra'], $lottery_props->allow_zero_extra)) 
				{
					$draw_data += [
							'zero_error'	=>	TRUE];
					break;
				}
				else if (!$this->lotteries_m->duplicate_regular_drawn($lottery_props->extra_ball, $draw_data)) 
				{
					$draw_data += [
							"regular_duplicate_error"	=>	TRUE];
					break;
				}

				// Enhanced database operation with error handling for hosting servers
				$insert_result = $this->lotteries_m->csv_array_to_query($table, $draw_data);
				if (!$insert_result) 
				{
					$db_error = $this->db->error();
					
					$draw_data += [
						'error'	=>	TRUE,
						'db_error' => $db_error['message']];
					break;
				}
				
				$draw_data += ['success' => TRUE];
				$processed_count++; // Increment batch counter
				
				// Batch processing for server stability
				if ($processed_count % $batch_size == 0) {
					// Refresh database connection to prevent timeout
					$this->db->reconnect();
					
					// Longer delay for hosting server stability
					usleep(250000); // 0.25 second delay every 5 records
					
					// Force session update to prevent timeout
					$this->session->mark_as_temp(array(
						'new_file_name' => 600,
						'table_name' => 600,
						'last_draw' => 600
					));
					
					// Periodic memory cleanup
					if (function_exists('gc_collect_cycles')) {
						gc_collect_cycles();
					}
				}
				
				// Send heartbeat every record to prevent timeout
				$heartbeat_counter++;
				if ($heartbeat_counter % 1 == 0) {
					// Send a small response to keep connection alive
					echo " "; // Single space as heartbeat
					if(ob_get_level() > 0) {
						ob_flush();
						flush();
					}
				}

				if(ob_get_level() > 0)
				{
					ob_flush();
					flush();
				}
				echo json_encode($draw_data);
				
				unset($draw_data);		// Remove Current Draw Date for next iteration
			}
			
			if (isset($draw_data)) {
				echo json_encode($draw_data);
				unset($draw_data);
			} elseif(!isset($draw_data)) { // No more data to process
				// Import completed successfully - update lastdate field in lottery_profiles
				$table_name = $this->session->userdata('table_name');
				if ($table_name) {
					$latest_date = $this->lotteries_m->get_latest_draw_date($table_name);
					if ($latest_date) {
						$this->lotteries_m->update_lastdraw($id, $latest_date);
					}
				}

				// Snapshot current hwc_predictions → prev_h_w_c_predictions so history
				// page can highlight which balls were predicted before this new draw
				if ($processed_count > 0) {
					$this->statistics_m->hwc_snapshot_predictions($id);
					$this->statistics_m->hwc_followers_snapshot($id);
				}
				$this->session->unset_userdata(array('new_file_name', 'table_name', 'last_draw', 'balls_drawn', 'extra_ball', 'minimum_ball', 
							'maximum_ball', 'minimum_ball', 'minimum_extra_ball', 'maximum_extra_ball', 'duplicate_extra', 'allow_zero_extra', 'elim'));
			unset($lottery_props);					
			} // close elseif(!isset($draw_data))
		} // close if(!empty(new_file_name))
	} // close import_process
	
	/**
	 * Determines the current row count of the csv to data
	 *  being imported in the database
	 * @param       $table	$current lottery table name		
	 * @return      none
	 */
	public function process($table)
	{
		$row_count = $this->lotteries_m->db_row_count($table);
	echo($row_count);
	}

	/**
	 * Get the latest draw information for a lottery after import completion
	 * @param       $id		lottery id		
	 * @return      JSON with last draw information including draw number
	 */
	public function get_last_draw($id)
	{
		$lottery = $this->lotteries_m->get($id);
		if (!$lottery) {
			echo json_encode(['error' => 'Lottery not found']);
			return;
		}
		
		$last_draw = $this->lotteries_m->last_draw_db($lottery->lottery_name);
		
		if ($last_draw == 'nodraws') {
			echo json_encode(['nodraws' => true]);
		} elseif ($last_draw && !empty($last_draw->id)) {
			$draw = "";
			$last_date = "";
			$draw_id = $last_draw->id; // Get the draw ID/number
			
			foreach ($last_draw as $key => $value) {
				if (substr($key, 0, 4) == 'ball') $draw .= $value . " ";
				if ($key == 'extra') $draw .= " + " . $value;
				if ($key == 'draw_date') $last_date = date("D, M d, Y", strtotime(str_replace('/', '-', $value)));
			}
			
			echo json_encode([
				'success' => true,
				'last_date' => $last_date,
				'draw_numbers' => trim($draw),
				'draw_id' => $draw_id
			]);
		} else {
			echo json_encode(['error' => 'Unable to retrieve last draw']);
		}
	}

	/**
	 * Views all draws with pagination, filtering and Draw Search Options
	 *  being imported in the database
	 * @param       $id		current id of draws		
	 * @return      none
	 */
	public function view_draws($id)
	{
		$this->data['lottery'] = $this->lotteries_m->get($id);
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		// Check to see if the actual table exists in the db?
		if (!$this->lotteries_m->lotto_table_exists($tbl_name))
		{
			$this->session->set_flashdata('message', 'There is an INTERNAL error deleting this lottery. '.$tbl_name.' Does not exist. The Draw Database has been RE-CREATED.');
			$this->lotteries_m->create_lotto_table_fields($tbl_name, $this->data['lottery']->balls_drawn, $this->data['lottery']->extra_ball); // Re-create the Draw Databasse
			redirect('admin/lotteries');
		}
		// Check for existing lottery draws
		$this->data['message'] = '';  // Create a Message object
		$this->data['request'] = '';  // only view draws		
		$new_range = 0;				  // First Pass through, default is 100 draws

		if(!empty($this->uri->segment(5))) 
		{
			$new_range = $this->uri->segment(5,0); // Return segment range
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

		$this->data['draws'] = $this->lotteries_m->load_draws($tbl_name, $new_range, 0); // Trend is N/A
		
		if (!$this->data['draws'])
		{
			$this->data['message'] = 'There are no draws associated with this lottery. Please import draws.'; 
		}
		else	// Yes, Draws are available. next we need to find the next draw date
		{
			$c = count($this->data['draws']);					// Determine total count of array of objects
			$ld = $this->data['draws'][0]->draw_date;			// Return last draw date
			$day = $this->lotteries_m->return_day($ld);							// Returns the day of draw, Saturday, Sunday, etc.
			$this->data['lottery']->next_draw_date = $this->lotteries_m->next_date($this->data['lottery'], $day, $ld);
			$this->data['lottery']->num = strval(++$c);
		}
		$this->data['interval'] = $interval;		// Record the interval here (for the dropdown)
		$this->data['sel_range'] = $sel_range;		// What was selected for the range in the previous page
		$this->data['range'] = $new_range;
		$this->data['all'] = $all;
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/view_draws'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['subview']  = 'admin/lotteries/view';
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->load->view('admin/_layout_main', $this->data);
	}

	public function delete($id) {
		if(!$this->lotteries_m->delete($id)) $this->session->set_flashdata('message', 'There is a problem Deleting this Lottery. The Lottery Profile (Structure & Data)
	 and Draw table (Structure only) must EXIST.');
		redirect('admin/lotteries');
	}

	/**
	 * Returns TRUE if Lottery Name is Unique and does not match the id, 
	 * otherwise FALSE for the lottery name already exists.
	 * @param       $str
	 * @return      TRUE/FALSE TRUE (if does not exist), FALSE (if the name selected currently exists)
	 */
	public function _unique_lotteryname($str)
	{
		// Do Not validate if Lottery Name already exists
		// Unless it's the Lottery Name for the current Lottery
		$id = $this->uri->segment(4);
		$lottery_name = $this->input->post('lottery_name');
		$this->db->where('lottery_name', $lottery_name);
		! $id || $this->db->where('id !=', $id);
		$lotteries = $this->lotteries_m->get();
			if (count($lotteries)) 
			{
				$this->form_validation->set_message('_unique_lotteryname', '%s already exists. Please type another Lottery Name');
				return FALSE;
			}
	return TRUE;
	}

	/**
	 * Returns the extra ball is set (TRUE) or not set (FALSE)
	 * 
	 * @param       none
	 * @return      TRUE/FALSE TRUE (if not set), FALSE (if set)
	 */
	public function _extra_ball_set($str) 
	{
		if (is_null($this->input->post('extra_ball'))&&empty($this->input->post('minimum_extra_ball'))&&empty($this->input->post('maximum_extra_ball')))
		{
			return TRUE;
		} 
		elseif (!is_null($this->input->post('extra_ball'))&&!empty($this->input->post('minimum_extra_ball'))&&!empty($this->input->post('maximum_extra_ball'))) 
		{
			return TRUE;
		}
	$this->form_validation->set_message('_extra_ball_set', 'The Extra Ball must be set to adjust the minimum and maximum extra ball values.');
	return FALSE;
	}

	/**
	 * Returns TRUE if at least one day in the week is set, or 
	 * FALSE is all the days are not set
	 * 
	 * @param       $str (monday, tuesday, wednesday, thursday, friday, saturday, sunday) depending on which checkboxes are set.
	 * @return      TRUE/FALSE TRUE (if at least one day is set), FALSE (if no days are set, we need at least one draw day)
	 */
	public function _require_day_of_week_set($str) 
	{
		$get_user_posts = $this->input->post(NULL, FALSE); 
		if (is_array($get_user_posts)) 
		{
			foreach($get_user_posts as $key => $value) 
			{
				if ($key=='monday'&&$value=='1') return TRUE;
				if ($key=='tuesday'&&$value=='1') return TRUE;
				if ($key=='wednesday'&&$value=='1') return TRUE;
				if ($key=='thursday'&&$value=='1') return TRUE;
				if ($key=='friday'&&$value=='1') return TRUE;
				if ($key=='saturday'&&$value=='1') return TRUE;
				if ($key=='sunday'&&$value=='1') return TRUE;
			}	
		}
		
	$this->form_validation->set_message('_require_day_of_week_set', 'There is no days set for the draw.<br />Select at least one draw day.');
	return FALSE;	
	}
	
	/**
	 * file value and type check during validation
	 * 
	 * 
	 * @param       $str
	 * @return      TRUE/FALSE 
	 */
	public function _file_check($str){
        $allowed_mime_type_arr = array('image/gif','image/jpeg','image/pjpeg','image/png','image/x-png');
        
        // Check if file upload exists and has a name before getting mime type
        if(isset($_FILES['lottery_image']['name']) && $_FILES['lottery_image']['name']!=""){
            $mime = get_mime_by_extension($_FILES['lottery_image']['name']);
            if(in_array($mime, $allowed_mime_type_arr)){
                return TRUE;
            }else{
                $this->form_validation->set_message('_file_check', 'Please select only pdf/gif/jpg/png file.');
                return FALSE;
            }
        }else{
            // No file uploaded, which is OK for updates
            return TRUE;
        }
	}

	/**
	 * Returns TRUE if at least one prize category is set, or 
	 * FALSE is all the prize categories are not set
	 * 
	 * @param       none 			
	 * @return      boolean  		TRUE (if at least one prize is set), FALSE (if no prizes are set, we need at least one prize category)
	 */
	public function _require_one_prize_set() 
	{
		$get_user_posts = $this->input->post(NULL, FALSE); 
		if (is_array($get_user_posts)) 
		{
			foreach($get_user_posts as $key => $value) 
			{
				if ($key=='9_win'&&$value=='1') return TRUE;
				if ($key=='8_win_extra'&&$value=='1') return TRUE;
				if ($key=='8_win'&&$value=='1') return TRUE;
				if ($key=='7_win_extra'&&$value=='1') return TRUE;
				if ($key=='7_win'&&$value=='1') return TRUE;
				if ($key=='6_win_extra'&&$value=='1') return TRUE;
				if ($key=='6_win'&&$value=='1') return TRUE;
				if ($key=='5_win_extra'&&$value=='1') return TRUE;
				if ($key=='5_win'&&$value=='1') return TRUE;
				if ($key=='4_win_extra'&&$value=='1') return TRUE;
				if ($key=='4_win'&&$value=='1') return TRUE;
				if ($key=='3_win_extra'&&$value=='1') return TRUE;
				if ($key=='3_win'&&$value=='1') return TRUE;
				if ($key=='2_win_extra'&&$value=='1') return TRUE;
				if ($key=='2_win'&&$value=='1') return TRUE;
				if ($key=='1_win_extra'&&$value=='1') return TRUE;
				if ($key=='1_win'&&$value=='1') return TRUE;
				if ($key=='extra'&&$value=='1') return TRUE;
			}	
		}
		
	$this->form_validation->set_message('_require_one_prize_set', 'There is no prizes set for this lottery.<br />Select at least one prize category for this lottery.');
	return FALSE;	
	}
	/**
	 * Returns FALSE if the first date is greater than or equal to the last date
	 * EXCEPTION: Skips validation if critical parameters changed and first date was updated
	 * 
	 * @param       none		
	 * @return      TRUE/FALSE 	TRUE (if firstdate is less than lastdate OR exception applies), FALSE (if firstdate is greater than lastdate)
	 */
	public function _firstdate_greater_equal_lastdate() 
	{
		// Check if we should skip validation due to critical parameter change with new firstdate
		if ($this->should_skip_date_validation()) {
			log_message('info', 'Date validation skipped for firstdate - critical parameters changed with updated first date');
			return TRUE;
		}
		
		$firstdate = strtotime($this->input->post('firstdate'));
		$lastdate  = strtotime($this->input->post('lastdate'));
		
		log_message('info', 'Validating firstdate vs lastdate: ' . date('Y-m-d', $firstdate) . ' vs ' . date('Y-m-d', $lastdate));
		
		if ($firstdate>=$lastdate) 
		{
			$this->form_validation->set_message('_firstdate_greater_equal_lastdate', 'The First Date must be less than the Last Date.');
			return FALSE;
		}
	return TRUE;
	}
	/**
	 * Returns FALSE if the first date is greater than or equal to the last date
	 * EXCEPTION: Skips validation if critical parameters changed and first date was updated
	 * 
	 * @param       none		
	 * @return      TRUE/FALSE 	TRUE (if lastdate is less than firstdate OR exception applies), FALSE (if lastdate is greater than or equal lastdate)
	 */
	public function _lastdate_less_equal_firstdate() 
	{
		// Check if we should skip validation due to critical parameter change with new firstdate
		if ($this->should_skip_date_validation()) {
			log_message('info', 'Date validation skipped for lastdate - critical parameters changed with updated first date');
			return TRUE;
		}
		
		$firstdate = strtotime($this->input->post('firstdate'));
		$lastdate  = strtotime($this->input->post('lastdate'));
		
		log_message('info', 'Validating lastdate vs firstdate: ' . date('Y-m-d', $lastdate) . ' vs ' . date('Y-m-d', $firstdate));
		
		if ($lastdate<=$firstdate) 
		{
			$this->form_validation->set_message('_lastdate_less_equal_firstdate', 'The Last Date must be greater than the First Date.');
			return FALSE;
		}
	return TRUE;
	}
	
	/**
	 * Determines if date validation should be skipped
	 * Skips when critical parameters have changed AND first draw date has been updated
	 * Also skips during confirmation flow
	 * 
	 * @return      bool TRUE if validation should be skipped, FALSE otherwise
	 */
	private function should_skip_date_validation() 
	{
		// Skip validation if we're in the confirmation flow
		if ($this->input->post('confirm_data_deletion') === 'yes') {
			log_message('info', 'Skipping date validation - in confirmation flow');
			return TRUE;
		}
		
		// Check if we have an original lottery stored (only available during edit)
		if (!isset($this->data['original_lottery']) || !$this->data['original_lottery']) {
			log_message('info', 'Not skipping date validation - no original lottery data');
			return FALSE;
		}
		
		// Get current POST data to compare
		$current_data = array(
			'balls_drawn' => $this->input->post('balls_drawn'),
			'minimum_ball' => $this->input->post('minimum_ball'),
			'maximum_ball' => $this->input->post('maximum_ball'),
			'extra_ball' => $this->input->post('extra_ball') ? 1 : 0,
			'minimum_extra_ball' => $this->input->post('minimum_extra_ball'),
			'maximum_extra_ball' => $this->input->post('maximum_extra_ball')
		);
		
		// Check if critical parameters have changed
		$critical_params_changed = $this->has_critical_parameter_changed($this->data['original_lottery'], $current_data);
		
		if (!$critical_params_changed) {
			log_message('info', 'Not skipping date validation - no critical parameter changes detected');
			return FALSE; // No critical changes, apply normal validation
		}
		
		log_message('info', 'Critical parameters changed - checking if first date was updated');
		
		// Critical parameters changed - check if first date was also updated
		$original_firstdate = date('Y-m-d', strtotime($this->data['original_lottery']->firstdate));
		$new_firstdate_input = $this->input->post('firstdate');
		
		// Convert the posted date from dd-mm-yyyy to Y-m-d for comparison
		$firstdate_obj = DateTime::createFromFormat('d-m-Y', $new_firstdate_input);
		if (!$firstdate_obj) {
			// Try alternative format in case of different date input
			$firstdate_obj = DateTime::createFromFormat('Y-m-d', $new_firstdate_input);
			if (!$firstdate_obj) {
				log_message('error', 'Failed to parse first date input: ' . $new_firstdate_input);
				return FALSE; // Invalid date format, apply normal validation
			}
		}
		$new_firstdate = $firstdate_obj->format('Y-m-d');
		
		$date_changed = ($original_firstdate !== $new_firstdate);
		log_message('info', 'First date comparison: original=' . $original_firstdate . ', new=' . $new_firstdate . ', changed=' . ($date_changed ? 'YES' : 'NO'));
		
		// Additional check: if the new first date is after the last date AND critical params changed,
		// this is clearly a scenario where validation should be skipped (new configuration starting in future)
		if ($date_changed) {
			$lastdate_input = $this->input->post('lastdate');
			$lastdate_obj = DateTime::createFromFormat('d-m-Y', $lastdate_input);
			if (!$lastdate_obj) {
				$lastdate_obj = DateTime::createFromFormat('Y-m-d', $lastdate_input);
			}
			if ($lastdate_obj) {
				$new_lastdate = $lastdate_obj->format('Y-m-d');
				if ($new_firstdate > $new_lastdate) {
					log_message('info', 'Skipping validation - new first date is after last date (new configuration setup)');
					return TRUE;
				}
			}
		}
		
		// Skip validation if critical params changed AND first date was updated
		return $date_changed;
	}
	
	/**
	 * Add Draw input boxes to the latest draw
	 * 
	 * @param       $id			Lottery id
	 * @return      none 
	 */
	public function draw_add($id)
	{
		$this->data['lottery'] = $this->lotteries_m->get($id);
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		// Check for existing lottery draws
		$this->data['message'] = '';  // Create a Message object
		
		if (!empty($this->input->post('add')))	// Save to Database
		{
			$draw = $this->lotteries_m->array_from_post ( array (
				'ball1',
				'ball2',
				'ball3'
			) );

			$edit_rules = array(
				'ball1' => array(
					'field' => 'ball1', 
					'label' => 'Ball 1', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				), 
				'ball2' => array(
					'field' => 'ball2', 
					'label' => 'Ball 2', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				), 
				'ball3' => array(  
					'field' => 'ball3', 
					'label' => 'Ball 3', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				)
			);
			if (intval($this->data['lottery']->balls_drawn)>=4) {
				$draw['ball4'] = $this->input->post('ball4');
				$edit_rules['ball4'] = array(
					'field' => 'ball4', 
					'label' => 'Ball 4', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				); 
			}
			if (intval($this->data['lottery']->balls_drawn)>=5) {
				$draw['ball5'] = $this->input->post('ball5');
				$edit_rules['ball5'] = array(
					'field' => 'ball5', 
					'label' => 'Ball 5', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if (intval($this->data['lottery']->balls_drawn)>=6) 
			{
				$draw['ball6'] = $this->input->post('ball6');
				$edit_rules['ball6'] = array(
					'field' => 'ball6', 
					'label' => 'Ball 6', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if (intval($this->data['lottery']->balls_drawn)>=7) 
			{
				$draw['ball7'] = $this->input->post('ball7');
				$edit_rules['ball7'] = array(
					'field' => 'ball7', 
					'label' => 'Ball 7', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if (intval($this->data['lottery']->balls_drawn)>=8) 
			{
				$draw['ball8'] = $this->input->post('ball8');
				$edit_rules['ball8'] = array(
					'field' => 'ball8', 
					'label' => 'Ball 8', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if (intval($this->data['lottery']->balls_drawn)>=9) 
			{
				$draw['ball9'] = $this->input->post('ball9');
				$edit_rules['ball9'] = array(
					'field' => 'ball9', 
					'label' => 'Ball 9', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if ($this->data['lottery']->extra_ball) 
			{
				$draw['extra'] = $this->input->post('extra_ball');
				$edit_rules['extra'] = array(
					'field' => 'extra_ball', 
					'label' => 'Extra Ball', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_extra_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_extra_ball).']'
				);
			}
			$draw['draw_date'] = date("Y-m-d", strtotime($this->input->post('next_date')));	// format the date without the day of week
			$draw['lottery_id'] = $id;	// Foreign Key to Lottery Profile

			$this->form_validation->set_rules($edit_rules);
			
			if ($this->form_validation->run() == TRUE) {

				$next_id = $this->lotteries_m->insert_draw($tbl_name, $draw); 
				if ($next_id)
				{
					$this->data['message'] = "Draw has been added to the database. Last Draw Date:".date("l M d, Y", strtotime($draw['draw_date']));  // Successfully added draw message
					
					// Update lastdate field in lottery_profiles with the new draw date
					$this->lotteries_m->update_lastdraw($id, $draw['draw_date']);

					// Snapshot current hwc_predictions → prev_h_w_c_predictions so history
					// page can show which balls were predicted before this new draw
					$this->statistics_m->hwc_snapshot_predictions($id);
					$this->statistics_m->hwc_followers_snapshot($id);
				} 
				else
				{
					$this->data['message'] = "The draw could not be added to the database. Check the numbers and Save the draw again.";  // Error message	
				}
			}
			else 
			{
				$this->data['add']	= 'add';
				$this->data['message'] = "Number Range Error. Check the numbers and Save the draw again.";  // Error message
			}
		}
		else
		{
			$this->data['add']	= 'add';
		}
		if(!empty($this->uri->segment(5))) 
		{
			$new_range = $this->uri->segment(5,0); // Return segment range
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
			$old_range = $this->session->userdata('range'); // Default will be 100 previous draws
			if(!$new_range) $new_range = $old_range;	// Database Range
			$sel_range = 1;								// All Defaults
			if($new_range>100) $sel_range = intval($new_range / 100);
			$this->data['draws'] = $this->lotteries_m->load_draws($tbl_name);
			$c = count($this->data['draws']);					// Determine total count of array of objects
			$ld = $this->data['draws'][0]->draw_date;		// Return last draw date
			$day = $this->lotteries_m->return_day($ld);						// Returns the day of draw, Saturdday, Sunday, etc.

			$this->data['lottery']->next_draw_date = $this->lotteries_m->next_date($this->data['lottery'], $day, $ld);
			$this->data['lottery']->num = strval(++$c);

		$this->data['interval'] = $interval;		// Record the interval here (for the dropdown)
		$this->data['sel_range'] = $sel_range;		// What was selected for the range in the previous page
		$this->data['range'] = $new_range;
		$this->data['all'] = $all;
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/view'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	 
		$this->data['subview']  = 'admin/lotteries/view';
		$this->load->view('admin/_layout_main', $this->data);
	}

	/**
	 * Edit Draw Input Boxes and Save them to new box
	 * 
	 * 
	 * @param       int $id		id of Lottery Profile
	 * @return      none 
	 */
	public function draw_edit($id)
	{
		$this->data['lottery'] = $this->lotteries_m->get($id);
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		// Check for existing lottery draws
		$this->data['message'] = '';  // Create a Message object

		if (!empty($this->input->post('edit')))	// Save to Database
		{
			$draw = $this->lotteries_m->array_from_post ( array (
				'draw[]',
				'ball_1[]',
				'ball_2[]',
				'ball_3[]'
			) );

			$edit_rules = array(
				'ball1' => array(
					'field' => 'ball_1[]', 
					'label' => 'Ball 1', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				), 
				'ball2' => array(
					'field' => 'ball_2[]', 
					'label' => 'Ball 2', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				), 
				'ball3' => array(  
					'field' => 'ball_3[]', 
					'label' => 'Ball 3', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				)
			);
			if (intval($this->data['lottery']->balls_drawn)>=4) {
				$draw['ball_4[]'] = $this->input->post('ball_4[]');
				$edit_rules['ball_4[]'] = array(
					'field' => 'ball_4[]', 
					'label' => 'Ball 4', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				); 
			}
			if (intval($this->data['lottery']->balls_drawn)>=5) {
				$draw['ball_5[]'] = $this->input->post('ball_5[]');
				$edit_rules['ball_5[]'] = array(
					'field' => 'ball_5[]', 
					'label' => 'Ball 5', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if (intval($this->data['lottery']->balls_drawn)>=6) 
			{
				$draw['ball_6[]'] = $this->input->post('ball_6[]');
				$edit_rules['ball_6[]'] = array(
					'field' => 'ball_6[]', 
					'label' => 'Ball 6', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if (intval($this->data['lottery']->balls_drawn)>=7) 
			{
				$draw['ball_7[]'] = $this->input->post('ball_7[]');
				$edit_rules['ball_7[]'] = array(
					'field' => 'ball_7[]', 
					'label' => 'Ball 7', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if (intval($this->data['lottery']->balls_drawn)>=8) 
			{
				$draw['ball_8[]'] = $this->input->post('ball_8[]');
				$edit_rules['ball_8[]'] = array(
					'field' => 'ball_8[]', 
					'label' => 'Ball 8', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if (intval($this->data['lottery']->balls_drawn)>=9) 
			{
				$draw['ball_9[]'] = $this->input->post('ball_9[]');
				$edit_rules['ball_9[]'] = array(
					'field' => 'ball_9[]', 
					'label' => 'Ball 9', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if ($this->data['lottery']->extra_ball) 
			{
				$draw['extra[]'] = $this->input->post('extra_ball');
				$edit_rules['extra'] = array(
					'field' => 'extra_ball[]', 
					'label' => 'Extra Ball', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_extra_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_extra_ball).']'
				);
			}
			$draw['lottery_id'] = $id;	// Foreign Key to Lottery Profile
			print_r($draw['extra[]']);
			$this->form_validation->set_rules($edit_rules);
			
			if ($this->form_validation->run() == TRUE) {
				// Draw Updates need to be passed like this ->
				// 0 => id, ball1, ball2, ball3, ball4, ball5, ball6, extra, lottery_id
				// 1 => id, ball1, ball2, ball3, ball4, ball5, ball6, extra, lottery_id

				$result = $this->lotteries_m->update_from_post($draw, $tbl_name, $this->data['lottery']->balls_drawn, $this->data['lottery']->extra_ball);

				if ($result)
				{
					$this->data['message'] = "The Draw(s) have been updated in the database.";  // Successfully added draw message
					
					// Update lastdate field in lottery_profiles if needed
					$current_latest_date = $this->lotteries_m->get_latest_draw_date($tbl_name);
					if ($current_latest_date) {
						// Get current lastdate from lottery_profiles
						$lottery_profile = $this->lotteries_m->get($id);
						if (!$lottery_profile->lastdate || $lottery_profile->lastdate !== $current_latest_date) {
							$this->lotteries_m->update_lastdraw($id, $current_latest_date);
						}
					}
				} 
				else
				{
					$this->data['message'] = "The draw(s) could not be updated in the database. Check the numbers and Save the draw(s) again.";  // Error message	
				}
			}
			else 
			{
				$this->data['edit']	= 'edit';
				$this->data['message'] = "Number Range Error. Check the numbers and Save the draw(s) again.";  // Error message
			}
		}
		else
		{
			if (empty($this->input->post('draw'))) $this->data['message'] = "Please Select the Draw Number(s) and click Manually Edit Draw(s) below.";  // Error message
			else $this->data['edit']	= 'edit';
		}
		if(!empty($this->uri->segment(5))) 
		{
			$new_range = $this->uri->segment(5,0); // Return segment range
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
			$old_range = $this->session->userdata('range'); // Default will be 100 previous draws
			if(!$new_range) $new_range = $old_range;	// Database Range
			$sel_range = 1;								// All Defaults
			if($new_range>100) $sel_range = intval($new_range / 100);
		$this->data['draws'] = $this->lotteries_m->load_draws($tbl_name);
		$this->data['selected'] = $this->input->post('draw');	// Return the posted array
		$c = count($this->data['draws']);						// Determine total count of array of objects
		$ld = $this->data['draws'][0]->draw_date;			// Return last draw date
		$day = $this->lotteries_m->return_day($ld);							// Returns the day of draw, Saturdday, Sunday, etc.
		$this->data['lottery']->next_draw_date = $this->lotteries_m->next_date($this->data['lottery'], $day, $ld);
		$this->data['lottery']->num = strval(++$c);

		$this->data['interval'] = $interval;		// Record the interval here (for the dropdown)
		$this->data['sel_range'] = $sel_range;		// What was selected for the range in the previous page
		$this->data['range'] = $new_range;
		$this->data['all'] = $all;
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/view'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->data['subview']  = 'admin/lotteries/view';
		$this->load->view('admin/_layout_main', $this->data);
	}
	/**
	 * Returns the extra ball is set (TRUE) or not set (FALSE)
	 *
	 * @param		str $str 		Lottery value to compare with $lottery profile
	 * @param       arr $lottery	Lottery Profile Information
	 * @return      TRUE/FALSE 		TRUE (if not set), FALSE (if set)
	 */
	public function _duplicate_check($str, $lottery) 
	{
	return (intval($str)>$lottery['maximum_ball'] ? FALSE : TRUE);
	}
	
	/**
	 * Get repeater numbers from current draw compared to previous draw
	 *
	 * @param       obj $lottery    Lottery object
	 * @param       obj $lastdraw   Last draw object
	 * @return      array           Array of repeater numbers
	 */
	private function get_repeater_numbers($lottery, $lastdraw)
	{
		// Get the current draw numbers
		$current_numbers = array();
		$current_numbers[] = $lastdraw->ball1;
		$current_numbers[] = $lastdraw->ball2;
		$current_numbers[] = $lastdraw->ball3;
		
		$balls_count = intval($lottery->balls_drawn);
		if ($balls_count >= 4 && isset($lastdraw->ball4)) $current_numbers[] = $lastdraw->ball4;
		if ($balls_count >= 5 && isset($lastdraw->ball5)) $current_numbers[] = $lastdraw->ball5;
		if ($balls_count >= 6 && isset($lastdraw->ball6)) $current_numbers[] = $lastdraw->ball6;
		if ($balls_count >= 7 && isset($lastdraw->ball7)) $current_numbers[] = $lastdraw->ball7;
		if ($balls_count >= 8 && isset($lastdraw->ball8)) $current_numbers[] = $lastdraw->ball8;
		if ($balls_count >= 9 && isset($lastdraw->ball9)) $current_numbers[] = $lastdraw->ball9;
		
		// Get the lottery table name
		$lottery_table = $this->lotteries_m->lotto_table_convert($lottery->lottery_name);
		
		// Get the previous draw
		$this->db->select('*');
		$this->db->from($lottery_table);
		$this->db->where('draw_date <', $lastdraw->draw_date);
		$this->db->order_by('draw_date', 'DESC');
		$this->db->limit(1);
		$previous_draw_query = $this->db->get();
		
		if ($previous_draw_query->num_rows() > 0) {
			$previous_draw = $previous_draw_query->row();
			
			// Get previous draw numbers
			$previous_numbers = array();
			$previous_numbers[] = $previous_draw->ball1;
			$previous_numbers[] = $previous_draw->ball2;
			$previous_numbers[] = $previous_draw->ball3;
			
			if ($balls_count >= 4 && isset($previous_draw->ball4)) $previous_numbers[] = $previous_draw->ball4;
			if ($balls_count >= 5 && isset($previous_draw->ball5)) $previous_numbers[] = $previous_draw->ball5;
			if ($balls_count >= 6 && isset($previous_draw->ball6)) $previous_numbers[] = $previous_draw->ball6;
			if ($balls_count >= 7 && isset($previous_draw->ball7)) $previous_numbers[] = $previous_draw->ball7;
			if ($balls_count >= 8 && isset($previous_draw->ball8)) $previous_numbers[] = $previous_draw->ball8;
			if ($balls_count >= 9 && isset($previous_draw->ball9)) $previous_numbers[] = $previous_draw->ball9;
			
			// Find repeaters (numbers that appear in both draws)
			return array_intersect($current_numbers, $previous_numbers);
		}
		
		return array();
	}
	
	/**
	 * Calculate Max Last (maximum repeating last digits) for a draw
	 *
	 * @param       obj $lottery    Lottery object
	 * @param       obj $lastdraw   Last draw object
	 * @return      int             Maximum count of numbers ending with same digit
	 */
	private function calculate_max_last($lottery, $lastdraw)
	{
		// Get the current draw numbers
		$draw_numbers = array();
		$draw_numbers[] = $lastdraw->ball1;
		$draw_numbers[] = $lastdraw->ball2;
		$draw_numbers[] = $lastdraw->ball3;
		
		$balls_count = intval($lottery->balls_drawn);
		if ($balls_count >= 4 && isset($lastdraw->ball4)) $draw_numbers[] = $lastdraw->ball4;
		if ($balls_count >= 5 && isset($lastdraw->ball5)) $draw_numbers[] = $lastdraw->ball5;
		if ($balls_count >= 6 && isset($lastdraw->ball6)) $draw_numbers[] = $lastdraw->ball6;
		if ($balls_count >= 7 && isset($lastdraw->ball7)) $draw_numbers[] = $lastdraw->ball7;
		if ($balls_count >= 8 && isset($lastdraw->ball8)) $draw_numbers[] = $lastdraw->ball8;
		if ($balls_count >= 9 && isset($lastdraw->ball9)) $draw_numbers[] = $lastdraw->ball9;
		
		// Count occurrences of each last digit (0-9)
		$last_digit_counts = array_fill(0, 10, 0);
		
		foreach ($draw_numbers as $number) {
			$last_digit = $number % 10; // Get the last digit
			$last_digit_counts[$last_digit]++;
		}
		
		// Return the maximum count
		return max($last_digit_counts);
	}

	/**
	 * AJAX endpoint for Calculate functionality
	 *
	 * @param int $id Lottery ID
	 * @return void
	 */
	public function ajax_calculate($id)
	{
		header('Content-Type: application/json');
		
		try {
			// Get lottery data
			$lottery = $this->lotteries_m->get($id);
			if (!$lottery) {
				echo json_encode(['success' => false, 'message' => 'Lottery not found']);
				return;
			}
			
			// Redirect to statistics calculate - capture output
			ob_start();
			redirect('admin/statistics/calculate/' . $id);
			$redirect_output = ob_get_clean();
			
			// Since redirect happens, we check session for success message
			$message = $this->session->flashdata('message');
			if ($message && strpos($message, 'Draw Statistics Complete and Up To-Date') !== false) {
				echo json_encode(['success' => true, 'message' => 'Draw Statistics Complete and Up To-Date']);
			} else {
				echo json_encode(['success' => false, 'message' => $message ?: 'Unable to complete calculation']);
			}
		} catch (Exception $e) {
			echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
		}
	}

	/**
	 * AJAX endpoint for ReCalc functionality
	 *
	 * @param int $id Lottery ID
	 * @return void
	 */
	public function ajax_recalc($id)
	{
		header('Content-Type: application/json');
		
		try {
			// Get lottery data
			$lottery = $this->lotteries_m->get($id);
			if (!$lottery) {
				echo json_encode(['success' => false, 'message' => 'Lottery not found']);
				return;
			}
			
			$last_date = '';
			if ($lottery->last_draw !== 'nodraws' && !empty($lottery->last_draw->draw_date)) {
				$last_date = date("M d, Y", strtotime(str_replace('/', '-', $lottery->last_draw->draw_date)));
			}
			
			// Redirect to statistics recalc - capture output
			ob_start();
			redirect('admin/statistics/recalc/' . $id);
			$redirect_output = ob_get_clean();
			
			// Check session for success message
			$message = $this->session->flashdata('message');
			if ($message && strpos($message, 'Hot - Warm - Cold, Followers and Friends Statistics have ALL been updated') !== false) {
				echo json_encode([
					'success' => true, 
					'message' => 'The Hot - Warm - Cold, Followers and Friends Statistics have ALL been updated to the latest draw',
					'lottery_name' => $lottery->lottery_name,
					'last_date' => $last_date
				]);
			} else {
				echo json_encode(['success' => false, 'message' => $message ?: 'Unable to complete recalculation']);
			}
		} catch (Exception $e) {
			echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
		}
	}
	
	/**
	 * Toggle enabled status of a lottery
	 * 
	 * @param int $id Lottery ID
	 * @return void
	 */
	public function toggle_enabled($id) {
		if (!$id) {
			$this->session->set_flashdata('message', 'Invalid lottery ID.');
			redirect('admin/lotteries');
		}
		
		$lottery = $this->lotteries_m->get($id);
		if (!$lottery) {
			$this->session->set_flashdata('message', 'Lottery not found.');
			redirect('admin/lotteries');
		}
		
		// Toggle the enabled status
		$new_status = ($lottery->enabled == 1) ? 0 : 1;
		$data = array('enabled' => $new_status);
		$this->lotteries_m->save($data, $id);
		
		$status_text = ($new_status == 1) ? 'made visible' : 'hidden';
		$this->session->set_flashdata('message', 'Lottery "' . $lottery->lottery_name . '" has been ' . $status_text . '.');
		redirect('admin/lotteries');
	}

	/**
	 * Check if critical lottery parameters have changed that would invalidate historical predictions
	 * 
	 * @param object $original Original lottery data
	 * @param array $new_data New lottery data from form
	 * @return boolean TRUE if critical parameters changed, FALSE otherwise
	 */
	private function has_critical_parameter_changed($original, $new_data) {
		// Define critical parameters that invalidate historical data
		$critical_params = array(
			'balls_drawn',       // Number of balls drawn
			'minimum_ball',      // Lowest ball number
			'maximum_ball',      // Highest ball number
			'extra_ball',        // Extra ball inclusion (0 or 1)
			'minimum_extra_ball',// Lowest extra ball
			'maximum_extra_ball' // Highest extra ball
		);
		
		// Check each critical parameter
		foreach ($critical_params as $param) {
			$original_value = isset($original->$param) ? $original->$param : NULL;
			$new_value = isset($new_data[$param]) ? $new_data[$param] : NULL;
			
			// Convert to same type for comparison
			$original_value = intval($original_value);
			$new_value = intval($new_value);
			
			if ($original_value !== $new_value) {
				log_message('info', "Critical parameter change detected: $param changed from $original_value to $new_value");
				return TRUE;
			}
		}
		
		return FALSE;
	}

	/**
	 * Clear all historical prediction data for a lottery
	 * This includes H-W-C, Followers, Friends, and Non-Followers data
	 * 
	 * @param int $lottery_id Lottery ID
	 * @return boolean TRUE on success, FALSE on failure
	 */
	private function clear_historical_prediction_data($lottery_id) {
		try {
			$deleted_count = 0;
			
			// Get the lottery details to access the table name
			$lottery = $this->lotteries_m->get($lottery_id);
			if ($lottery) {
				$table_name = $this->lotteries_m->lotto_table_convert($lottery->lottery_name);
				
				// IMPORTANT: Truncate the actual draw data table
				if ($this->db->table_exists($table_name)) {
					$this->db->truncate($table_name);
					log_message('info', "Truncated draw data table: $table_name for lottery_id=$lottery_id");
					
					// CRITICAL: Update table structure to match current lottery configuration
					// This ensures the table has the correct number of ball columns after truncation
					$this->lotteries_m->update_lotto_table_fields(
						$table_name, 
						$lottery->balls_drawn, 
						$lottery->extra_ball
					);
					log_message('info', "Updated table structure for $table_name: {$lottery->balls_drawn} balls, extra_ball={$lottery->extra_ball}");
					
					// Update lottery profile to reflect no draws
					$update_data = array(
						'lastdate' => NULL
					);
					$this->db->where('id', $lottery_id);
					$this->db->update('lottery_profiles', $update_data);
				}
			}
			
			// Clear from lottery_followers table
			$this->db->where('lottery_id', $lottery_id);
			$this->db->delete('lottery_followers');
			$deleted_count += $this->db->affected_rows();
			log_message('info', "Deleted " . $this->db->affected_rows() . " rows from lottery_followers");
			
			// Clear from lottery_nonfollowers table
			$this->db->where('lottery_id', $lottery_id);
			$this->db->delete('lottery_nonfollowers');
			$deleted_count += $this->db->affected_rows();
			log_message('info', "Deleted " . $this->db->affected_rows() . " rows from lottery_nonfollowers");
			
			// Clear from lottery_friends table
			$this->db->where('lottery_id', $lottery_id);
			$this->db->delete('lottery_friends');
			$deleted_count += $this->db->affected_rows();
			log_message('info', "Deleted " . $this->db->affected_rows() . " rows from lottery_friends");
			
			// Clear from lottery_h_w_c table (Hot-Warm-Cold data)
			if ($this->db->table_exists('lottery_h_w_c')) {
				$this->db->where('lottery_id', $lottery_id);
				$this->db->delete('lottery_h_w_c');
				$deleted_count += $this->db->affected_rows();
				log_message('info', "Deleted " . $this->db->affected_rows() . " rows from lottery_h_w_c");
			}
			
			// Clear from lottery_h_w_c_stats table (Hot-Warm-Cold history statistics)
			if ($this->db->table_exists('lottery_h_w_c_stats')) {
				$this->db->where('lottery_id', $lottery_id);
				$this->db->delete('lottery_h_w_c_stats');
				$deleted_count += $this->db->affected_rows();
				log_message('info', "Deleted " . $this->db->affected_rows() . " rows from lottery_h_w_c_stats");
			}

			// Clear the prev snapshot fields in lottery_h_w_c_followers — lottery parameters
			// have changed so previous predicted numbers are no longer meaningful.
			if ($this->db->table_exists('lottery_h_w_c_followers')) {
				$this->db->where('lottery_id', $lottery_id);
				$this->db->update('lottery_h_w_c_followers', array('prev_lottery_numbers' => ''));
				log_message('info', "Cleared prev_lottery_numbers in lottery_h_w_c_followers for lottery_id=$lottery_id");
			}
			
			// Clear statistics cache
			if (isset($this->statistics_m)) {
				$lottery = $this->lotteries_m->get($lottery_id);
				if ($lottery) {
					$table_name = $this->lotteries_m->lotto_table_convert($lottery->lottery_name);
					$this->statistics_m->clear_cache($table_name);
					log_message('info', "Cleared statistics cache for $table_name");
				}
			}
			
			// Set a flag to force complete recalculation on next statistics view
			$this->session->set_userdata('force_recalc_lottery_' . $lottery_id, TRUE);
			
			log_message('info', "Historical prediction data cleared for lottery_id=$lottery_id (Total: $deleted_count rows deleted)");
			return TRUE;
		} catch (Exception $e) {
			log_message('error', "Failed to clear historical data for lottery_id=$lottery_id: " . $e->getMessage());
			return FALSE;
		}
	}
}