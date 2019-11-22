<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Membership extends Admin_Controller 
{
	
	public function __construct() 
	{
		 parent::__construct();
		 $this->load->model('membership_m');
	}
	
	public function index() 
	{
		// Fetch all users from the database
		$this->data['members'] = $this->membership_m->get();
		
		// Load the view

		$this->data['subview'] = 'admin/membership/index';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function edit($id = NULL) 
	{
		
		// Fetch a user or set a new one
		$id == NULL OR $this->data['member'] = $this->membership_m->get($id);
		
		if ($id) 
		{
			$this->data['member'] = $this->membership_m->get($id); 
			count($this->data['member']) || $this->data['errors'][] = 'Member could not be found';
		} 
		else 
		{
			$this->data['member'] = $this->membership_m->get_new();
		}
		
		// Setup the form
		$rules = $this->user_m->rules_admin;
		$id OR $rules['password']['rules'].= '|required';
		$this->form_validation->set_rules($rules);
		
		if ($this->form_validation->run()  == TRUE) 
		{
				
			// We can save and redirect
			$data = $this->user_m->array_from_post(array('username', 'name', 'email', 'password'));

			//$data['password'] = $this->user_m->hash($data['password'], $this->user_m->unique_salt());
			$data['password'] = $this->user_m->hash($data['password']);

			$this->user_m->save($data, $id);
			redirect('admin/membership');
		}
		
		// Load the View
		$this->data['subview'] = 'admin/membership/edit';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function delete($id) 
	{
		
		$this->user_m->delete($id);
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