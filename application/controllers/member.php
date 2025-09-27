<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Member extends Frontend_Controller 
{
    
    function __construct() {
        parent::__construct();
        $this->load->model('member_m');
        $this->data['recent_news'] = $this->article_m->get_recent();
        /* Sidebar Articles */
        $this->data['sidebar_top'] = $this->page_m->side_bar('top_section');
		$this->data['sidebar_top']->body = strip_slashes($this->data['sidebar_top']->body); // Remove the slashes from the database.
		$this->data['sidebar_middle'] = $this->page_m->home_pages('middle_section');
		$this->data['sidebar_middle']->body = strip_slashes($this->data['sidebar_middle']->body); // Remove the slashes from the database.
		$this->data['sidebar_bottom'] = $this->page_m->side_bar('bottom_section');
		$this->data['sidebar_bottom']->body = strip_slashes($this->data['sidebar_bottom']->body); // Remove the slashes from the database.
    }

    public function index() 
    {   // If a new user, their account must be validated
        if ($this->session->flashdata('token')=="validate") {
        // We must retrieve the next record id and save the data but the account will be not validated yet.
            $this->data['subview'] = $this->data['validate_email']->template;
            $this->load->view('_main_layout', $this->data);
        }
    }

    /**  If member exists, then retrieve data from member, log them in, save the session
     *   and log them into there account. 
     *   If member does not exist, then retrieve a new id, save to database
     *   and send double opt-in email to verify to activate the account
     *   display email validation message
     * save_member
     *
     * @param [integer] $id
     * @return void
     */    
    public function member_update($id = NULL, $member) 
	{
		// Fetch a user or set a new one
        if ($id != NULL) $this->data['member'] = $this->member_m->get($id); 
        
        if ($id) 
		{
			$this->data['member'] = $this->member_m->get($id); 
			count($this->data['member']) || $this->data['errors'][] = 'User could not be found';
            redirect('member/dashboard');
        } 
		else 
		{
            // Initialize Object Array
            $this->data['member'] = $this->member_m->get_new_member();

            // We can save and redirect
            $data = $this->member_m->array_from_post(array('first_name', 'last_name', 'email', 'username', 
            'reg_time', 'city', 'state_prov', 'country_id','lottery_id', 'member_active', 'subscription_key', 'ip_address', 'terms_agreement'));
            $data['username'] = $member['username'];
            $data['email'] = $member['email'];
            // Set terms agreement if provided
            if (isset($member['terms_agreement'])) {
                $data['terms_agreement'] = $member['terms_agreement'];
            }
            // Get the user's IP address using the same method as login
            $ip = $this->page_m->getIP();
            $data['ip_address'] = sprintf("%u", ip2long($ip)); // Convert to INT format like in login
            // $data['password'] = $this->member_m->hash_password($member['password']);
            // Initialize Values
            $data['first_name'] = $this->data['member']->first_name;
            $data['last_name'] = $this->data['member']->last_name;
            $data['city'] = $this->data['member']->city;
            $data['state_prov'] = $this->data['member']->state_prov;
            $data['country_id'] = $this->data['member']->country_id;
            $data['lottery_id'] = $this->data['member']->lottery_id;
            $data['member_active'] = $this->data['member']->member_active;
            // 1. get a 12 char length random string token
            $token = $this->member_m->getToken(12);
            // 2. make that random token to a secure hash
            $securetoken = $this->member_m->getSecureHash($token);
             // 3. convert that secure hash to a url string
            $urlsecuretoken = $this->member_m->cleanUrl($securetoken);
            // 4. Include it to the Database set for a week long validation
            $data['subscription_key'] = $urlsecuretoken;
            $id = $this->member_m->save($data, $id); 
        }
        $email_confirmation = array('urlsecuretoken' => $urlsecuretoken,
                                    'email' => $member['email']);
    return $email_confirmation;
    } 
           
    public function register() {
        
        $new_data_member = array (
            'username' => $this->input->post('username'),
            'email' => $this->input->post('email'),
        );
        /*
         * Checking if posted fields are empty string (just in case) - e.g. user typing only whitespaces instead of actual name, email, username, password
         */
        // Secondary Validation Rules
        $new_member_rules = $this->member_m->new_member_rules;
        $this->form_validation->set_rules($new_member_rules);
        
        if ($this->form_validation->run() == FALSE)
        {             
           if (form_error('username')) { $error = '<div class="alert alert-danger">'.form_error('username').'</div>'; }
           if (form_error('email')) { $error = '<div class="alert alert-danger">'.form_error('email').'</div>'; }
           
           $array = array(
            'error'   => TRUE,
            'validation_error' => $error
           );
        } 
        else
        {
            // Store member data in session and redirect to terms agreement
            $this->session->set_userdata('terms_pending', 'active');
            $this->session->set_userdata('pending_member_data', $new_data_member);
            $array = array(
                'success' => '<div class="alert alert-success"><p>Registration data validated. <br />Redirecting to Terms of Service agreement...<br />
                Please wait...</p></div>',
                'redirect_url' => site_url('member/terms_agreement')
           );
        }
        echo json_encode($array);
    }

    /**
     * Display terms and conditions agreement page
     */
    public function terms_agreement()
    {
        // Check if user has valid registration session
        $has_valid_session = ($this->session->userdata('terms_pending') == 'active' && 
                             $this->session->userdata('pending_member_data'));
        
        if ($has_valid_session) 
        {
            // Add maintenance check if maintenance model is available
            if (method_exists($this, 'maintenance_m') || isset($this->maintenance_m)) {
                $this->data['maintenance'] = $this->maintenance_m->maintenance_check();
            }
            
            // Show terms agreement page
            $this->data['subview'] = 'member/terms_agreement';
            $this->load->view('_main_layout', $this->data);
        } 
        else 
        {
            // No valid registration session, redirect to home
            redirect('home');         
        } 
    }

    /**
     * Process the terms agreement response (agree/decline)
     */
    public function process_terms()
    {
        // Debug: Log what we received
        error_log("DEBUG process_terms: POST data = " . print_r($_POST, true));
        error_log("DEBUG process_terms: terms_pending = " . $this->session->userdata('terms_pending'));
        error_log("DEBUG process_terms: pending_member exists = " . ($this->session->userdata('pending_member_data') ? 'YES' : 'NO'));
        
        // Get pending member data from session
        $pending_member = $this->session->userdata('pending_member_data');
        
        // Verify valid registration session
        if (!$pending_member || $this->session->userdata('terms_pending') != 'active') {
            // No pending member data or invalid session, redirect to home
            error_log("DEBUG process_terms: FAILED session validation - redirecting to home");
            redirect('home');
            return;
        }

        $terms_response = $this->input->post('terms_response');
        error_log("DEBUG process_terms: terms_response = " . ($terms_response ?: 'NULL'));
        
        if ($terms_response === 'agree') {
            // User agreed to terms - create the account
            error_log("DEBUG process_terms: User agreed to terms");
            $pending_member['terms_agreement'] = TRUE;
            
            // Clear registration session data
            $this->session->unset_userdata('terms_pending');
            $this->session->unset_userdata('pending_member_data');
            
            // Use Token to send a message to validate Email Address before Activating Account.
            $this->session->set_userdata('validate_token', 'validate');
            $this->session->set_userdata('validate_member', $pending_member);
            
            error_log("DEBUG process_terms: Set validate_token and validate_member, redirecting to validate_email");
            redirect('member/validate_email');
        } 
        else if ($terms_response === 'decline') {
            // User declined terms - clear session and show decline page
            $this->session->unset_userdata('terms_pending');
            $this->session->unset_userdata('pending_member_data');
            
            // Add maintenance check if maintenance model is available
            if (method_exists($this, 'maintenance_m') || isset($this->maintenance_m)) {
                $this->data['maintenance'] = $this->maintenance_m->maintenance_check();
            }
            
            $this->data['subview'] = 'member/terms_declined';
            $this->load->view('_main_layout', $this->data);
        } 
        else {
            // Invalid response, clear session and redirect to home
            $this->session->unset_userdata('registration_token');
            $this->session->unset_userdata('pending_member_data');
            redirect('home');
        }
    }

    public function validate_email()
    {
        // Check for valid validation token (must come from terms agreement)
        $has_validate_token = ($this->session->userdata('validate_token') == 'validate');
        
        if ($has_validate_token) 
        {
            $new_member = $this->session->userdata('validate_member');
            
            // CRITICAL: Verify that terms were actually agreed to
            if (!isset($new_member['terms_agreement']) || $new_member['terms_agreement'] !== TRUE) {
                exit('Account creation blocked: Terms of service must be accepted first.');
            }
            
            // Create member account ONLY after terms verification
            $member = $this->member_update(NULL, $new_member);
            $this->data['maintenance'] = $this->maintenance_m->maintenance_check();
            // Send Confirmation email
            $this->member_m->send_confirmation_message($member['urlsecuretoken'], $member['email']);  
            
            // Clean up validation session data after use
            $this->session->unset_userdata('validate_token');
            $this->session->unset_userdata('validate_member');
            
            $this->data['subview'] = 'member/validate_email'; 
            $this->load->view('_main_layout', $this->data);
        } 
        else 
        {
            exit('Unauthorized. Intrusion Detected. You must complete the terms of service agreement first.');         
        } 
    }

    public function validate_forgotpassword()
    {
        if ($this->session->flashdata('token')=='validate') 
        {
            $this->data['maintenance'] = $this->maintenance_m->maintenance_check();

            // Setup View
            $this->data['subview'] = 'member/validate_forgotpassword'; 
            $this->load->view('_main_layout', $this->data);
        } 
        else 
        {
            exit('Unauthorized. Intrusion Detected.');         
        } 
    }

    public function login() {

    // Compare with Login Data to the Database
    if ($this->member_m->login_database()) 
    {  
       $array = array(
            'login_error' => '',
            'success' => '<div class="alert alert-success">You are now Logged into Lottotrak.</div>'
        );
    } 
    else 
    {
        $array = array(
            'error' => TRUE,
            'login_error' => 'invalid username or password',
            'validation_error' => '<div class="alert alert-danger">The Username / Password combination is incorrect.</div>'
        ); 
    } 
    echo json_encode($array);             
    }

    public function logout() 
	{
		$this->member_m->logout_database();
		redirect('home');
	}

    public function dashboard() {
        // Automatic Redirection to Homepage as Logged In
        redirect('home');
    }

    public function _unique_username($str)
	{
		$sql = "SELECT username FROM members WHERE username = '{$str}' LIMIT 1";
	    $result = $this->db->query($sql);
	    $row = $result->row();
			
	    if (!empty($row)) 
		{
			$this->form_validation->set_message('_unique_username', '%s already exists. Please type another username');
			return FALSE;
		}
	return TRUE;
	}

	public function _unique_email($str)
	{
		$sql = "SELECT email FROM members WHERE email = '{$str}' LIMIT 1";
	    $result = $this->db->query($sql);
	    $row = $result->row();
			
		if (!empty($row)) 
		{
			$this->form_validation->set_message('_unique_email', '%s already exists. Please type another email address');
			return FALSE;
		}
		return TRUE;
    }
    public function forgotpassword() 
	{
	   
	     // email address be entered and not blank  
	        $rules = $this->member_m->forgot_password_rules;
	        $this->form_validation->set_rules($rules);

	        $form_validate = $this->form_validation->run();
	               
	        if ($form_validate == FALSE) 
			{
			
                if (form_error('email_forgot')) { $error = '<div class="alert alert-danger">'.form_error('email_forgot').'</div>'; }
                $array = array(
                    'error'   => TRUE,
                    'validation_error' => $error
                );
	         
            } 
            else
            {
               // Check if email exists, return first name
    	        $email = $this->input->post('email_forgot');
    	        $email_exists = $this->member_m->Email_exists($email);
    	         
                if (is_object($email_exists)) 
				{
    	            // Email exist, send out email
    	            $this->member_m->Send_email($email_exists->id, $email, $email_exists->first_name);
                    $this->session->set_flashdata('token', 'validate');
                    $array = array(
                        'error' => FALSE,
                        'forgot_error_message' => '',
                        'success' => '<div class="alert alert-success">An email has sent you instructions on resetting your password.</div>'
                    );
                } 
				else 
				{
    	            $array = array(
                        'error' => TRUE,
                        'forgot_error_message' => 'Email Address does not exist',
                        'validation_error' => '<div class="alert alert-danger">Email Address is not in our System.</div>'
                    ); 
    	        }     
            }
        echo json_encode($array);     
    }
    
    public function reset_password($id, $email_code) 
	{ /* change $email to $id */
        
	    $email = $this->member_m->Retrieve_email($id);
	    if (isset($email, $email_code)) 
		{
            $email = trim($email);
            $email_hash = sha1($email.$email_code);
	        $verified = $this->member_m->verify_reset_password($email, $email_code);
	        $this->data['verified'] = $verified;
	        if ($verified) 
			{
                // Load the View
	            $this->data['id'] = $id;
                $this->data['email_code'] = $email_code;
                $this->data['email_hash'] = $email_hash;
	            $this->data['email'] = $email;
	            $this->data['subview'] = 'member/update_password';
        	    $this->data['message'] = 'Enter a new password and type the password in again to confirm it is correct.';
        	     
	        } 
			else 
			{
	            // Load the View
	            $this->data['subview'] = 'member/update_password';
	            $this->data['message'] = '<div class="alert alert-danger" role="alert">There was a problem with your link. Please click it again or request to reset you password again.</div>';
	        }
	        	$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
                $this->load->view('_main_layout', $this->data);
	    	}
		}
	
	public function update_password() 
	{
	   
        if (! isset($_POST['email'],
		$_POST['email_hash']) || $_POST['email_hash'] !== sha1($_POST['email'].$_POST['email_code'])) {
	       die('Error updating your password');
	    }
	   
        $this->data['member'] = $this->member_m->array_from_post(array('id', 'email', 'password')); // email_hash, email_code not used
        
        $id = $this->data['member']['id'];

        // verify that the passwords match, valid email and email hash
        $rules = $this->member_m->update_password_rules;
        $this->form_validation->set_rules($rules);
        
        if ($this->form_validation->run() == FALSE) 
        {
            // Reload this view
            $this->data['id'] = $id;
            $this->data['email_code'] = $this->input->post('email_code');
            $this->data['email_hash'] = $this->input->post('email_hash');
            $this->data['verified'] = $this->input->post('verified');
            $this->data['email'] = $this->input->post('email');
            $this->data['subview'] = 'member/update_password';
            $this->data['meta_title'] = 'Change Your Password';
        } 
        else 
        {
            // We can save and redirect
            $this->data['member']['password'] = $this->member_m->hash_password($this->data['member']['password']);
            $id = $this->member_m->save($this->data['member'], $id);
            
            if (isset($id)) 
            {
                $this->data['meta_title'] = 'Password has been changed';
                $this->data['subview'] = 'member/password_reset';
            } 
            else 
            {
                $this->session->set_flashdata('error', '<div class="alert alert-danger" role="alert">Your Password has not been updated. Please try again.</div>');
            }
        }
            $this->data['maintenance'] = $this->maintenance_m->maintenance_check();
            $this->load->view('_main_layout', $this->data);
            $this->session->unset_userdata('member_name'); // Logout only the member on the front end but any other members/admins are untouched
            $this->session->unset_userdata('member_email');
            $this->session->unset_userdata('member_first_name');
            $this->session->unset_userdata('member_last_name');
            $this->session->unset_userdata('member_city');
            $this->session->unset_userdata('member_state_prov');
            $this->session->unset_userdata('member_country_id');
            $this->session->unset_userdata('member_lottery_id');
            $this->session->unset_userdata('member_id');
            $this->session->unset_userdata('member_logged_in');
            $this->session->sess_destroy();
        }
}