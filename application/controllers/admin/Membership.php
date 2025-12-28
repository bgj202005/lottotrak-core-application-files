<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Membership extends Admin_Controller 
{
	
	public function __construct() 
	{
		 parent::__construct();
		 $this->load->model('membership_m');
		 $this->load->model('maintenance_m');
	}
	
	/**
	 * Retrieves List of All Members
	 * 
	 * @param       none
	 * @return      none
	 */
	public function index() 
	{
		// Fetch all users from the database
		$this->data['members'] = $this->membership_m->get();
		if (count($this->data['members']))
		{ 
			foreach($this->data['members'] as $member)
			{
				$member->lottery_names = $this->membership_m->lotteries_selected($member->lottery_id); 
				$member->lottery_count = $this->membership_m->lotteries_count($member->lottery_id);
			}
		}
		$this->data['current'] = $this->uri->segment(2); // Sets the membership menu
		$this->session->set_userdata('uri', 'admin/'.$this->data['current']);
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	 
		$this->data['subview'] = 'admin/membership/index';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function edit($id = NULL) 
	{
		
		// Fetch a user or set a new one
		//$id == NULL OR $this->data['member'] = $this->membership_m->get($id);
		
		if ($id) 
		{
			$this->data['member'] = $this->membership_m->get($id);
			is_object($this->data['member']) || $this->data['errors'][] = 'Member could not be found'; //deprecated php 7.2+ count($this->data['member']) 
			
			// Debug lottery selection
			$lottery_ids_raw = $this->data['member']->lottery_id;
			$lottery_ids_exploded = explode(',', $lottery_ids_raw);
			
			// Log for debugging (only in development)
			if (ENVIRONMENT === 'development') {
				error_log("DEBUG Admin Edit - Member ID: {$id}");
				error_log("DEBUG Admin Edit - Raw lottery_id: " . $lottery_ids_raw);
				error_log("DEBUG Admin Edit - Exploded lottery_id: " . print_r($lottery_ids_exploded, true));
			}
			
			$this->data['lotteries']['selected'] = (!empty($this->input->post('lottery_id')) ? $this->input->post('lottery_id') : $lottery_ids_exploded);	// Retrieve Number of Lotteries (Max of 3) the member wants to play
			$this->data['lotteries']['list'] = $this->membership_m->lotteries_list($this->data['member']->country_id);
			
			// More debug info
			if (ENVIRONMENT === 'development') {
				error_log("DEBUG Admin Edit - Available lotteries: " . print_r($this->data['lotteries']['list'], true));
				error_log("DEBUG Admin Edit - Selected lotteries: " . print_r($this->data['lotteries']['selected'], true));
			}
		} 
		else 
		{
			$this->data['member'] = $this->membership_m->get_new();
			$this->data['lotteries']['list'] = $this->membership_m->lotteries_list();
		}
		$this->data['message'] = '';  // Create a Message object
		// Setup the form
		$rules = $this->membership_m->rules_admin;
		$id OR $rules['password']['rules'].= '|required';
		$id OR $rules['username']['rules'].= '|required|callback__unique_username';
		$id OR $rules['email']['rules'].= '|required|valid_email|callback__unique_email';
		
		if ($id) 
		{ 
			if (empty($this->input->post('password'))) 
			{
				$_POST['password'] = $this->data['member']->password;
				$_POST['password_confirm'] = $this->data['member']->password;
			}
		}

		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == TRUE) 
		{
				
			if (empty($this->input->post('state_prov'))) $_POST['state_prov'] = $this->data['member']->state_prov;
			
			$_POST['member_active'] = (is_null($this->input->post('member_active')) ? 0 : 1); 
		
			// We can save and redirect
			$data = $this->membership_m->array_from_post(array('username', 'email', 'password', 
										'first_name', 'last_name', 'city', 'state_prov', 'country_id', 
										'lottery_id[]', 'member_active'));

			$data['lottery_id'] = (string) implode(",", $data['lottery_id[]']); // Combine everything in a single string
			unset($data['lottery_id[]']); // Remove the data array
			$data['password'] = $this->membership_m->hash($data['password']);
			
			$this->data['member'] = $this->membership_m->array_to_object($this->data['member'], $data);
			$this->data['member']->id = $this->membership_m->save($data, $id);

			$this->data['message'] = (is_null($id) ? "The Member has been added and an email has been sent." : "The Member profile has been updated.");
		} 
		// Load the View
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/edit'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	
		$this->data['subview'] = 'admin/membership/edit';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function delete($id) 
	{
		$this->membership_m->delete($id);
		redirect('admin/membership');
	}
	
	/**
	 * Debug method to check lottery selection data for a specific member
	 */
	public function debug_lottery($id = NULL)
	{
		// Only allow in development
		if (ENVIRONMENT !== 'development') {
			show_404();
			return;
		}
		
		if (!$id) {
			echo "<p>Please provide a member ID. Usage: /admin/membership/debug_lottery/41</p>";
			return;
		}
		
		echo "<h3>Lottery Selection Debug for Member ID: {$id}</h3>";
		
		// Get member data
		$member = $this->membership_m->get($id);
		if (!$member) {
			echo "<p style='color: red;'>Member not found!</p>";
			return;
		}
		
		echo "<h4>Member Information:</h4>";
		echo "<p><strong>Username:</strong> {$member->username}</p>";
		echo "<p><strong>Email:</strong> {$member->email}</p>";
		echo "<p><strong>Country ID:</strong> {$member->country_id}</p>";
		echo "<p><strong>Raw lottery_id:</strong> '{$member->lottery_id}'</p>";
		
		// Test the explode
		$lottery_ids_array = explode(',', $member->lottery_id);
		echo "<h4>Exploded Lottery IDs:</h4>";
		echo "<pre>" . print_r($lottery_ids_array, true) . "</pre>";
		
		// Get available lotteries for this country
		$available_lotteries = $this->membership_m->lotteries_list($member->country_id);
		echo "<h4>Available Lotteries for Country '{$member->country_id}':</h4>";
		echo "<pre>" . print_r($available_lotteries, true) . "</pre>";
		
		// Check which ones should be selected
		echo "<h4>Selection Analysis:</h4>";
		foreach ($lottery_ids_array as $lottery_id) {
			$lottery_id = trim($lottery_id);
			if (isset($available_lotteries[$lottery_id])) {
				echo "<p style='color: green;'>✓ Lottery ID {$lottery_id}: '{$available_lotteries[$lottery_id]}' - SHOULD be selected</p>";
			} else {
				echo "<p style='color: red;'>✗ Lottery ID {$lottery_id}: Not found in available lotteries</p>";
			}
		}
		
		// Test the multiselect data structure
		$lotteries_data = array(
			'list' => $available_lotteries,
			'selected' => $lottery_ids_array
		);
		
		echo "<h4>Form Multiselect Data Structure:</h4>";
		echo "<pre>" . print_r($lotteries_data, true) . "</pre>";
	}
	
		
	public function _unique_username($str)
	{
		// Do Not validate if email already exists
		// Unless it's the email for the current user	
		$id = $this->uri->segment(4);
		//dump($id); exit(1);	
		$this->db->where('username', $this->input->post('username'));
			! $id || $this->db->where('id !=', $id);
			$user = $this->user_m->get();
			
			if (count($user)) 
			{
				$this->form_validation->set_message('_unique_username', '%s already exists. Please type another username');
				return FALSE;
			}
	return TRUE;
	}
	public function _unique_email($str)
	{
		// Do Not validate if email already exists
		// Unless it's the email for the current user
		$id = $this->uri->segment(4);
		$this->db->where('email', $this->input->post('email'));
		! $id || $this->db->where('id !=', $id);
		$user = $this->user_m->get();
			
		if (count($user)) 
		{
			$this->form_validation->set_message('_unique_email', '%s already exists. Please type another email address');
			return FALSE;
		}
		return TRUE;
	}
			
	public function update_password() 
	{
	   
		if (! isset($_POST['email'],
		$_POST['email_hash']) || $_POST['email_hash'] !== sha1($_POST['email'].$_POST['email_code'])) {
	       die('Error updating your password');
	}
	   
	   $this->data = $this->user_m->array_from_post(array('id', 'email', 'password')); // email_hash, email_code not used
	   $id = $this->data['id'];
	   // verify that the passwords match, valid email and email hash
	   $rules = $this->user_m->update_password_rules;
	   $this->form_validation->set_rules($rules);
	   
	   if ($this->form_validation->run() == FALSE) 
	   {
	       $this->data['subview'] = 'admin/user/reset_password';
	       $this->data['title'] = 'Change Your Password';
	       $this->data['message'] = 'Enter a new password and type the password in again to confirm it is correct.';
	       $this->data['action'] = '/admin/user/update_password';
	   } 
	   else 
	   {
	       // We can save and redirect
	       $this->data['password'] = $this->user_m->hash($this->data['password']);
	       $id = $this->user_m->save($this->data, $id);
	       // Load the View
	       $this->data['subview'] = 'admin/user/login';
	       $this->data['title'] = 'Login';
	       $this->data['message'] = 'Please Log in using your credentials';
	       $this->data['action'] = '/admin/user/login';
	       
	       if (isset($id)) 
		   {
	           $this->session->set_flashdata('error', '<div class="alert alert-success" role="alert"><strong>Your Password has been succesfully been updated.</strong>
	                   Please enter your email address and password to login.</div>');
	       } 
		   else 
		   {
	           $this->session->set_flashdata('error', '<div class="alert alert-danger" role="alert">Your Password has not been updated. Please try again.</div>');
	       }
	   }

	   $this->load->view('admin/_layout_modal', $this->data);
	   $this->session->sess_destroy();
	}
}