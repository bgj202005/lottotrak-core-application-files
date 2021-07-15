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
			$this->data['lotteries']['selected'] = (!empty($this->input->post('lottery_id')) ? $this->input->post('lottery_id') : explode(',', $this->data['member']->lottery_id));	// Retrieve Number of Lotteries (Max of 3) the member wants to play
			$this->data['lotteries']['list'] = $this->membership_m->lotteries_list($this->data['member']->country_id);
		} 
		else 
		{
			$this->data['member'] = $this->membership_m->get_new();
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