<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Admin_Controller 
{
	
	public function __construct() 
	{
		 parent::__construct();
		 $this->data['status'] = ''; //Empty Status
	}
	
	public function index() 
	{
		// Fetch all users from the database
		$this->data['users'] = $this->user_m->get();
		
		// Load the view
		$this->data['subview'] = 'admin/user/index';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function edit($id = NULL) 
	{
		
		// Fetch a user or set a new one
		$id == NULL OR $this->data['user'] = $this->user_m->get($id);
		$this->data['status'] = ''; //Empty Status
		
		if ($id) 
		{
			$this->data['user'] = $this->user_m->get($id); 
			count($this->data['user']) || $this->data['errors'][] = 'User could not be found';
		} 
		else 
		{
			$this->data['user'] = $this->user_m->get_new();
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
			redirect('admin/user');
		}
		
		// Load the View
		$this->data['subview'] = 'admin/user/edit';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function delete($id) 
	{
		
		$this->user_m->delete($id);
		redirect('admin/user');
	}
	
	public function login() 
	{
		
		//if(!$this->input->is_ajax_request()) { echo "No Valid Request."; }
		// Redirect a user if he's already logged in
		/* $this->load->helper('url'); */
		$dashboard = 'admin/dashboard';
		$this->user_m->loggedin() == FALSE  || redirect('admin/dashboard');
		// Set the form
 		$rules = $this->user_m->rules;
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run()  == TRUE) 
		{

			$validate_login = $this->user_m->login();
			// We can login and redirect
			if (is_bool($validate_login)&&$validate_login == TRUE) 
			{
				redirect($dashboard);
			} 
			elseif (is_bool($validate_login)&&$validate_login == FALSE) 
			{
	
				$this->session->set_flashdata('error', '<div class="alert alert-danger" role="alert"><p>That <strong>email/password combination</strong> is incorrect.</font></div>');
					//echo validation_errors(); exit(1);
					echo '<div id="text-login-msg"><div class="alert alert-warning" role="alert">'.validation_errors().'</div></div>';
					$this->session->set_flashdata('text-login-email', '<p><div class="alert alert-danger" role="alert">That <strong>email/password combination</strong> is incorrect.</div>');
					redirect('admin/user/login', 'refresh');
			} 
			elseif (is_string($validate_login)&&$validate_login == "username") 
			{
				// Username is correct?
				$this->session->set_flashdata('error', '<div class="alert alert-danger" role="alert"><p>That <strong>Username </strong> is incorrect.</font></div>');
				echo '<div id="text-login-msg"><div class="alert alert-warning" role="alert">'.validation_errors().'</div></div>';
				$this->session->set_flashdata('text-login-email', '<p><div class="alert alert-danger" role="alert">That <strong>Username</strong> is incorrect.</div>');
				redirect('admin/user/login', 'refresh');
			}
		}
		// Load the View
		$this->data['subview'] = 'admin/user/login';
		$this->data['title'] = 'Login';
		$this->data['message'] = 'Please Log in using your credentials';
		$this->load->view('admin/_layout_modal', $this->data);
	}
	
	public function logout() 
	{
		$this->user_m->logout();
		redirect('admin/user/login');
	}
	
	public function forgotpassword() 
	{
	   
	    $this->load->helper('captcha','form');
 	    
	     // email address be entered and not blank  
	        $rules = $this->user_m->forgot_password_rules;
	        $this->form_validation->set_rules($rules);

	        $form_validate = $this->form_validation->run();
	         
	        // Setup Captcha
	        $original_string = array_merge(range(0,9), range('a','z'), range('A', 'Z'));
	        $original_string = implode("", $original_string);
	        $captcha = substr(str_shuffle($original_string), 0, 6);
	        
	        $vals = array(
	                'word'          => $captcha,
	                'img_path'      => './images/captcha/',
	                'img_url'       => site_url('/images/captcha/'),
	                'font_path'     => '../../../system/fonts/texb.ttf',
	                'img_width'     => '150',
	                'img_height'    => 50,
	                'expiration'    => 7200,
	                'font_size'     => 20,
	                 
	                // White background and border, black text and red grid
	                'colors' => array(
	                        'background' => array(255, 255, 255),
	                        'border' => array(255, 255, 255),
	                        'text' => array(0, 0, 0),
	                        'grid' => array(255, 40, 40)
	                )
	        );
	        $cap = create_captcha($vals);
	        $this->data['captcha'] = $cap;
	        
	        if ($form_validate == FALSE) 
			{
	           
			if (isset($this->session->userdata['image'])) 
			{
	            	if(file_exists(FCPATH."images/captcha/".$this->session->userdata['image'])) 
				{
	               unlink(FCPATH.'images/captcha/'.$this->session->userdata['image']); 
				} 
	        }
	           $this->session->set_userdata(array('captcha'=>$captcha, 'image' => $cap['time'].'.jpg'));
	        } 
			else 
			{
    	        // Check if email exists, return first name
    	        $email = $this->input->post('email');
    	        $email_exists = $this->user_m->Email_exists($email);
    	         
    	        if ($email_exists->id) 
				{
    	            // Captcha and Email exist, send out email
    	            $this->user_m->Send_email($email_exists->id, $email, $email_exists->name);
    	            $this->session->set_flashdata('error', '<div class="alert alert-success" role="alert"><strong>An Email was sent to the email address!</strong>
                         Please check your junk folder, if you don\'t see it in your inbox in 15 minutes.</div>');
    	        } 
				else 
				{
    	            $this->session->set_flashdata('error', '<div class="alert alert-danger" role="alert">The Email Address does not exist, Please contact info@lottotrak.com for assistance</div>');
    	        }
	         
	    	}
	    // Load the View
	    $this->data['subview'] = 'admin/user/forgot_password';
	    $this->data['title'] = 'Password Assistance';
	    $this->data['message'] = 'Please Enter Your Email Address';
	    
	    $this->load->view('admin/_layout_modal', $this->data);
	    
	     if (($form_validate == TRUE)&&$email_exists) 
		 {
	        if(file_exists(FCPATH."images/captcha/".$this->session->userdata['image'])) 
			{
	            unlink(FCPATH."images/captcha/".$this->session->userdata['image']); 
			}
	            $this->session->unset_userdata('captcha');
	            $this->session->unset_userdata('image');
	    } 
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
	
	public function _validate_captcha()
	{
		if(trim($this->input->post('captcha')) != $this->session->userdata['captcha']) 
		{
	        $this->form_validation->set_message('_validate_captcha', 'Wrong captcha code, hmm are you the Terminator?');
	        return FALSE;
		} 
		else 
		{
	        return TRUE;
		} 
	}
	
	public function reset_password($id, $email_code) 
	{ /* change $email to $id */
	    
	    $email = $this->user_m->Retrieve_email($id);
	    if (isset($email, $email_code)) 
		{
	        $email = trim($email);
	        $email_hash = sha1($email.$email_code);
	        $verified = $this->user_m->verify_reset_password($email, $email_code);
	        
	        if ($verified) 
			{
				// Load the View
	            $this->data['id'] = $id;
	            $this->data['email_hash'] = $email_hash;
	            $this->data['email_code'] = $email_code;
	            $this->data['email'] = $email;
	            $this->data['subview'] = 'admin/user/reset_password';
        	    $this->data['title'] = 'Change Your Password';
        	    $this->data['message'] = 'Enter a new password and type the password in again to confirm it is correct.';
        	    $this->data['action'] = '/admin/user/update_password';
        	     
	        } 
			else 
			{
	            // Load the View
	            $this->session->set_flashdata('error', '<div class="alert alert-danger" role="alert">There was a problem with your link. Please click it again or request to reset you password again.</div>');
	            $this->data['subview'] = 'admin/user/forgot_password';
	            $this->data['title'] = 'Password Assistance';
	            $this->data['message'] = 'Please Enter Your Email Address';
	        }
	        	$this->load->view('admin/_layout_modal', $this->data);
	    	}
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