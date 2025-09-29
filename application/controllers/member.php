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
            // Load location helper
            $this->load->helper('location');
            
            // Get the user's IP address and location information
            $ip = get_real_ip_address();
            $location_info = detect_location_by_ip($ip);
            
            // Store both old format (for compatibility) and new readable format
            $data['ip_address'] = sprintf("%u", ip2long($ip)); // Convert to INT format like in login
            $data['ip_address_readable'] = $location_info['ip'];
            $data['location_city'] = $location_info['city'];
            $data['location_region'] = $location_info['region'];
            $data['location_country'] = $location_info['country'];
            $data['location_country_code'] = $location_info['country_code'];
            $data['location_detected_at'] = $location_info['detected_at'];
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
            // 5. Set expiry date (5 days from now) - will add this after creating DB column
            // $data['validation_expiry'] = date('Y-m-d H:i:s', strtotime('+5 days'));
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
            // Send Confirmation email - user must validate email first
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

    /**
     * Get user's IP address and location info using location helper
     */
    public function get_user_location_info()
    {
        // Load location helper
        $this->load->helper('location');
        
        // Use the location helper to get comprehensive location information
        return detect_location_by_ip();
    }
    
    /**
     * Get user's real IP address
     */
    private function get_user_ip()
    {
        // Check for IP behind proxy/load balancer
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Handle comma-separated list of IPs
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    /**
     * Show profile completion form
     */
    public function complete_profile()
    {
        // Check if user has valid profile completion session
        $profile_member = $this->session->userdata('profile_completion_member');
        
        if (!$profile_member) {
            redirect('home');
            return;
        }
        
        // Check if email has been validated
        if (!isset($profile_member['email_validated']) || !$profile_member['email_validated']) {
            // Email not validated, show message and redirect
            $this->session->set_flashdata('error_message', 'Please validate your email address first by clicking the link in your email.');
            redirect('home');
            return;
        }

        // Load models for dropdowns
        $this->load->model('lotteries_m');
        $this->data['maintenance'] = $this->maintenance_m->maintenance_check();
        
        // Get user's IP and location info for security display
        $this->data['user_location'] = $this->get_user_location_info();
        
        // BFH will handle country/state loading automatically
        // No need to load countries from database
        $this->data['member_email'] = $profile_member['email'];
        $this->data['member_username'] = $profile_member['username'];
        
        $this->data['subview'] = 'member/complete_profile';
        $this->load->view('_main_layout', $this->data);
    }

    /**
     * Process profile completion and activate account
     */
    public function process_profile()
    {
        // Check if user has valid profile completion session
        $profile_member = $this->session->userdata('profile_completion_member');
        
        if (!$profile_member) {
            $array = array(
                'error' => TRUE,
                'validation_error' => '<div class="alert alert-danger">Session expired. Please register again.</div>'
            );
            echo json_encode($array);
            return;
        }

        // Validation rules
        $this->form_validation->set_rules('first_name', 'First Name', 'required|min_length[2]|max_length[50]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|min_length[2]|max_length[50]');
        $this->form_validation->set_rules('city', 'City', 'required|min_length[2]|max_length[100]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[50]|callback__validate_secure_password');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
        $this->form_validation->set_rules('country_id', 'Country', 'required');
        $this->form_validation->set_rules('state_province', 'State/Province', 'required');
        $this->form_validation->set_rules('lottery_ids[]', 'Lottery Selection', 'required');

        if ($this->form_validation->run() == FALSE) {
            $errors = array();
            if (form_error('first_name')) $errors[] = form_error('first_name');
            if (form_error('last_name')) $errors[] = form_error('last_name');
            if (form_error('city')) $errors[] = form_error('city');
            if (form_error('password')) $errors[] = form_error('password');
            if (form_error('confirm_password')) $errors[] = form_error('confirm_password');
            if (form_error('country_id')) $errors[] = form_error('country_id');
            if (form_error('state_province')) $errors[] = form_error('state_province');
            if (form_error('lottery_ids[]')) $errors[] = 'Please select at least one lottery';
            
            $array = array(
                'error' => TRUE,
                'validation_error' => '<div class="alert alert-danger">' . implode('<br>', $errors) . '</div>'
            );
        } else {
            // Additional lottery selection validation
            $lottery_ids = $this->input->post('lottery_ids');
            if (empty($lottery_ids) || !is_array($lottery_ids)) {
                $array = array(
                    'error' => TRUE,
                    'validation_error' => '<div class="alert alert-danger">Please select at least one lottery.</div>'
                );
                echo json_encode($array);
                return;
            }
            
            if (count($lottery_ids) > 5) {
                $array = array(
                    'error' => TRUE,
                    'validation_error' => '<div class="alert alert-danger">You can select maximum 5 lotteries. Please uncheck some selections.</div>'
                );
                echo json_encode($array);
                return;
            }
            
            // Check if we have a valid member_id from email validation
            if (isset($profile_member['member_id'])) {
                // Debug: Log session data
                log_message('debug', 'Profile completion - Session data: ' . print_r($profile_member, true));
                
                // Update member profile
                $state_prov = $this->input->post('state_province');
                // Start with a minimal update to test
                $update_data = array(
                    'member_active' => 1 // Just activate first to test
                );
                
                // Add other fields one by one if basic update works
                $first_name = trim($this->input->post('first_name'));
                $last_name = trim($this->input->post('last_name'));
                $city = trim($this->input->post('city'));
                $password = $this->input->post('password');
                $country_id = $this->input->post('country_id');
                $lottery_ids = $this->input->post('lottery_ids');
                
                if (!empty($first_name)) {
                    $update_data['first_name'] = $first_name;
                }
                if (!empty($last_name)) {
                    $update_data['last_name'] = $last_name;
                }
                if (!empty($city)) {
                    $update_data['city'] = $city;
                }
                if (!empty($password)) {
                    $update_data['password'] = password_hash($password, PASSWORD_DEFAULT);
                }
                if (!empty($country_id)) {
                    $update_data['country_id'] = $country_id;
                }
                if (!empty($state_prov)) {
                    $update_data['state_prov'] = $state_prov;
                }
                if (!empty($lottery_ids) && is_array($lottery_ids)) {
                    $update_data['lottery_id'] = implode(',', $lottery_ids);
                }
                
                // Load location helper and update IP address and location for security tracking
                $this->load->helper('location');
                
                $ip = get_real_ip_address();
                $location_info = detect_location_by_ip($ip);
                
                // Store both old format (for compatibility) and new readable format + location
                $update_data['ip_address'] = sprintf("%u", ip2long($ip)); // Convert to INT format
                $update_data['ip_address_readable'] = $location_info['ip'];
                $update_data['location_city'] = $location_info['city'];
                $update_data['location_region'] = $location_info['region'];
                $update_data['location_country'] = $location_info['country'];
                $update_data['location_country_code'] = $location_info['country_code'];
                $update_data['location_detected_at'] = $location_info['detected_at'];

                // Debug: Log update data and member ID
                log_message('debug', 'Profile completion - Member ID: ' . $profile_member['member_id']);
                log_message('debug', 'Profile completion - Update data: ' . print_r($update_data, true));
                log_message('debug', 'Profile completion - POST data: ' . print_r($this->input->post(), true));

                // Check if member exists first
                $this->db->where('id', $profile_member['member_id']);
                $existing_member = $this->db->get('members')->row();
                log_message('debug', 'Profile completion - Existing member: ' . print_r($existing_member, true));
                
                if (!$existing_member) {
                    log_message('debug', 'Profile completion - ERROR: Member not found with ID: ' . $profile_member['member_id']);
                    $array = array(
                        'error' => TRUE,
                        'validation_error' => '<div class="alert alert-danger">Member record not found. Please register again.</div>'
                    );
                    echo json_encode($array);
                    return;
                }

                $this->db->where('id', $profile_member['member_id']);
                $success = $this->db->update('members', $update_data);
                
                // Debug: Log database result
                log_message('debug', 'Profile completion - DB update result: ' . ($success ? 'SUCCESS' : 'FAILED'));
                log_message('debug', 'Profile completion - Affected rows: ' . $this->db->affected_rows());
                if (!$success) {
                    $db_error = $this->db->error();
                    log_message('debug', 'Profile completion - DB error: ' . $db_error['message']);
                }

                if ($success) {
                    // Get the updated member data for auto-login
                    $member_data = $this->db->where('id', $profile_member['member_id'])->get('members')->row();
                    
                    if ($member_data) {
                        // Automatically log in the user after successful profile completion
                        $session_data = array(
                            'member_name' => $member_data->username,
                            'member_email' => $member_data->email,
                            'member_first_name' => $member_data->first_name,
                            'member_last_name' => $member_data->last_name,
                            'member_city' => $member_data->city,
                            'member_state_prov' => $member_data->state_prov,
                            'member_country_id' => $member_data->country_id,
                            'member_lottery_id' => $member_data->lottery_id,
                            'member_id' => $member_data->id,
                            'member_logged_in' => TRUE
                        );
                        
                        $this->session->set_userdata($session_data);
                        log_message('debug', 'Profile completion - User automatically logged in: ' . $member_data->username);
                    }
                    
                    // Send account activation welcome email
                    $this->member_m->send_welcome_email($profile_member['email']);
                    
                    // Clear profile completion session
                    $this->session->unset_userdata('profile_completion_member');
                    
                    $array = array(
                        'success' => '<div class="alert alert-success"><strong>Account Activated!</strong> Your profile has been completed and your account is now active. You are now logged in!</div>',
                        'redirect_url' => site_url('member/profile_complete_success')
                    );
                } else {
                    $array = array(
                        'error' => TRUE,
                        'validation_error' => '<div class="alert alert-danger">Failed to update profile. Please try again.</div>'
                    );
                }
            } else {
                $array = array(
                    'error' => TRUE,
                    'validation_error' => '<div class="alert alert-danger">Invalid session. Please validate your email first.</div>'
                );
            }
        }

        echo json_encode($array);
    }

    /**
     * AJAX: Get provinces/states by country
     */
    public function get_provinces_by_country()
    {
        $country_id = $this->input->post('country_id');
        
        if (empty($country_id)) {
            echo json_encode(array('error' => 'Country ID required'));
            return;
        }

        // Debug: Log the country_id being searched
        log_message('debug', 'Getting provinces for country_id: ' . $country_id);

        // Get distinct provinces/states for the country from lottery_profiles
        $this->db->distinct()
                ->select('lottery_state_prov as province_code')
                ->from('lottery_profiles')
                ->where('lottery_country_id', $country_id)
                ->where('lottery_state_prov IS NOT NULL')
                ->where('lottery_state_prov !=', '');
        
        // Debug: Log the SQL query
        $query_string = $this->db->get_compiled_select('', FALSE);
        log_message('debug', 'Province query: ' . $query_string);
        
        $provinces = $this->db->order_by('lottery_state_prov')
                            ->get()
                            ->result();

        // Debug: Log the results
        log_message('debug', 'Provinces found: ' . print_r($provinces, true));

        echo json_encode($provinces);
    }

    /**
     * AJAX: Get lotteries by country and optional province
     */
    public function get_lotteries_by_location()
    {
        $country_id = $this->input->post('country_id');
        $province_code = $this->input->post('province_code');
        
        if (empty($country_id)) {
            echo json_encode(array('error' => 'Country ID required'));
            return;
        }

        // Debug logging for BFH integration
        log_message('debug', 'BFH Lottery request - Country: ' . $country_id . ', Province: ' . $province_code);

        // Always get country-wide lotteries first
        $this->db->select('id, lottery_name, "country" as lottery_type')
                 ->from('lottery_profiles')
                 ->where('lottery_country_id', $country_id)
                 ->where('(lottery_state_prov IS NULL OR lottery_state_prov = "")');

        $country_lotteries = $this->db->order_by('lottery_name')
                                    ->get()
                                    ->result();

        $all_lotteries = $country_lotteries;

        // If province is specified, add province-specific lotteries
        if (!empty($province_code)) {
            $this->db->select('id, lottery_name, "province" as lottery_type')
                     ->from('lottery_profiles')
                     ->where('lottery_country_id', $country_id)
                     ->where('lottery_state_prov', $province_code);
            
            $province_lotteries = $this->db->order_by('lottery_name')
                                          ->get()
                                          ->result();
            
            // Combine country and province lotteries
            $all_lotteries = array_merge($country_lotteries, $province_lotteries);
        }

        // Debug logging
        log_message('debug', 'BFH Lottery response - Total lotteries: ' . count($all_lotteries));

        // Always return lotteries (at minimum country lotteries should exist)
        echo json_encode($all_lotteries);
    }

    /**
     * Debug method to check lottery_profiles table data
     */
    public function debug_lottery_data()
    {
        // Only allow in development
        if (ENVIRONMENT !== 'development') {
            show_404();
            return;
        }
        
        echo "<h3>Lottery Profiles Table Analysis:</h3>";
        
        // Show table structure first
        echo "<h4>Table Structure:</h4>";
        $fields = $this->db->field_data('lottery_profiles');
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Max Length</th></tr>";
        foreach ($fields as $field) {
            echo "<tr><td>{$field->name}</td><td>{$field->type}</td><td>{$field->max_length}</td></tr>";
        }
        echo "</table><br>";
        
        // Show all data in the table
        echo "<h4>All Lottery Profiles Data:</h4>";
        $all_data = $this->db->select('*')->from('lottery_profiles')->get()->result();
        if ($all_data) {
            echo "<table border='1' style='border-collapse: collapse; font-size: 12px;'>";
            echo "<tr><th>ID</th><th>Country</th><th>State/Prov</th><th>Lottery Name</th><th>Other Fields</th></tr>";
            foreach ($all_data as $row) {
                echo "<tr>";
                echo "<td>{$row->id}</td>";
                echo "<td>{$row->lottery_country_id}</td>";
                echo "<td>{$row->lottery_state_prov}</td>";
                echo "<td>{$row->lottery_name}</td>";
                echo "<td>" . json_encode($row) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No data found in lottery_profiles table.<br>";
        }
        
        echo "<br><h4>Available Countries:</h4>";
        $countries = $this->db->distinct()
                            ->select('lottery_country_id')
                            ->from('lottery_profiles')
                            ->order_by('lottery_country_id')
                            ->get()
                            ->result();
        
        foreach ($countries as $country) {
            echo "<strong>Country ID: {$country->lottery_country_id}</strong><br>";
            
            // Show provinces for this country
            $provinces = $this->db->select('lottery_state_prov, lottery_name')
                                ->from('lottery_profiles')
                                ->where('lottery_country_id', $country->lottery_country_id)
                                ->where('lottery_state_prov IS NOT NULL')
                                ->where('lottery_state_prov !=', '')
                                ->get()
                                ->result();
            
            if ($provinces) {
                echo "Provinces/States:<br>";
                foreach ($provinces as $province) {
                    echo "- {$province->lottery_state_prov} ({$province->lottery_name})<br>";
                }
            } else {
                echo "<em>No provinces found for this country.</em><br>";
            }
            echo "<hr>";
        }
    }
    
    /**
     * Add sample Canadian lottery data for testing
     */
    public function add_canadian_lottery_data()
    {
        // Only allow in development
        if (ENVIRONMENT !== 'development') {
            show_404();
            return;
        }
        
        echo "<h3>Adding Canadian Lottery Data:</h3>";
        
        // Sample Canadian lottery data
        $canadian_lotteries = array(
            // National lotteries
            array(
                'lottery_country_id' => 'CA',
                'lottery_state_prov' => '',
                'lottery_name' => 'Lotto 6/49',
                'lottery_description' => 'National Canadian lottery'
            ),
            array(
                'lottery_country_id' => 'CA', 
                'lottery_state_prov' => '',
                'lottery_name' => 'Lotto Max',
                'lottery_description' => 'National Canadian lottery'
            ),
            // Ontario lotteries
            array(
                'lottery_country_id' => 'CA',
                'lottery_state_prov' => 'ON',
                'lottery_name' => 'Ontario 49',
                'lottery_description' => 'Ontario provincial lottery'
            ),
            array(
                'lottery_country_id' => 'CA',
                'lottery_state_prov' => 'ON',
                'lottery_name' => 'Lottario',
                'lottery_description' => 'Ontario provincial lottery'
            ),
            // Quebec lotteries
            array(
                'lottery_country_id' => 'CA',
                'lottery_state_prov' => 'QC',
                'lottery_name' => 'Quebec 49',
                'lottery_description' => 'Quebec provincial lottery'
            ),
            // British Columbia lotteries
            array(
                'lottery_country_id' => 'CA',
                'lottery_state_prov' => 'BC',
                'lottery_name' => 'BC/49',
                'lottery_description' => 'British Columbia provincial lottery'
            ),
            // Alberta lotteries
            array(
                'lottery_country_id' => 'CA',
                'lottery_state_prov' => 'AB',
                'lottery_name' => 'Western 649',
                'lottery_description' => 'Alberta provincial lottery'
            )
        );
        
        $inserted = 0;
        foreach ($canadian_lotteries as $lottery) {
            // Check if it already exists
            $exists = $this->db->where('lottery_country_id', $lottery['lottery_country_id'])
                             ->where('lottery_state_prov', $lottery['lottery_state_prov'])
                             ->where('lottery_name', $lottery['lottery_name'])
                             ->get('lottery_profiles')
                             ->num_rows();
            
            if ($exists == 0) {
                $this->db->insert('lottery_profiles', $lottery);
                echo "✓ Added: {$lottery['lottery_name']} ({$lottery['lottery_state_prov']})<br>";
                $inserted++;
            } else {
                echo "- Already exists: {$lottery['lottery_name']}<br>";
            }
        }
        
        echo "<br><strong>Total new lotteries added: {$inserted}</strong><br>";
        echo "<br><a href='" . site_url('member/debug_lottery_data') . "'>View Updated Lottery Data</a>";
    }
    
    /**
     * Run database migrations
     */
    public function run_migration()
    {
        // Only allow in development
        if (ENVIRONMENT !== 'development') {
            show_404();
            return;
        }
        
        $this->load->library('migration');
        
        echo "<h3>Running Database Migrations:</h3>";
        
        try {
            if ($this->migration->current() === FALSE) {
                echo "<p style='color: red;'>✗ Migration failed: " . $this->migration->error_string() . "</p>";
            } else {
                echo "<p style='color: green;'>✓ Migrations completed successfully!</p>";
                
                // Check if validation_expiry column now exists
                $fields = $this->db->field_data('members');
                $column_exists = false;
                foreach ($fields as $field) {
                    if ($field->name === 'validation_expiry') {
                        $column_exists = true;
                        break;
                    }
                }
                
                if ($column_exists) {
                    echo "<p style='color: blue;'>✓ validation_expiry column is now available.</p>";
                    echo "<p>You can now enable validation expiry features in the code.</p>";
                } else {
                    echo "<p style='color: orange;'>! validation_expiry column not found. Migration may not have included this change.</p>";
                }
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Migration error: " . $e->getMessage() . "</p>";
        }
        
        echo "<br><h4>Current Migration Version:</h4>";
        echo "<p>Version: " . $this->migration->version() . "</p>";
        
        echo "<br><h4>Current Members Table Structure:</h4>";
        $fields = $this->db->field_data('members');
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($fields as $field) {
            echo "<tr>";
            echo "<td>{$field->name}</td>";
            echo "<td>{$field->type}</td>";
            echo "<td>" . ($field->null ? 'YES' : 'NO') . "</td>";
            echo "<td>{$field->primary_key}</td>";
            echo "<td>{$field->default}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<br><a href='" . site_url('member/enable_validation_expiry_code') . "'>Enable Validation Expiry Code</a>";
    }
    
    /**
     * Enable validation expiry code after migration
     */
    public function enable_validation_expiry_code()
    {
        // Only allow in development
        if (ENVIRONMENT !== 'development') {
            show_404();
            return;
        }
        
        echo "<h3>Enable Validation Expiry Code:</h3>";
        
        // Check if column exists
        $fields = $this->db->field_data('members');
        $column_exists = false;
        foreach ($fields as $field) {
            if ($field->name === 'validation_expiry') {
                $column_exists = true;
                break;
            }
        }
        
        if (!$column_exists) {
            echo "<p style='color: red;'>✗ validation_expiry column does not exist. <a href='" . site_url('member/run_migration') . "'>Run migration first</a></p>";
            return;
        }
        
        echo "<p style='color: green;'>✓ validation_expiry column exists. You can now enable the code:</p>";
        
        echo "<h4>Code Changes to Make:</h4>";
        echo "<div style='background: #f5f5f5; padding: 15px; border: 1px solid #ddd;'>";
        
        echo "<h5>1. In Member.php controller:</h5>";
        echo "<pre>";
        echo "// Change this line (around line 82):\n";
        echo "// \$data['validation_expiry'] = date('Y-m-d H:i:s', strtotime('+5 days'));\n";
        echo "// To this:\n";
        echo "\$data['validation_expiry'] = date('Y-m-d H:i:s', strtotime('+5 days'));\n\n";
        
        echo "// And change this line (around line 56):\n";
        echo "// 'reg_time', 'city', 'state_prov', 'country_id','lottery_id', 'member_active', 'subscription_key', 'ip_address', 'terms_agreement'));\n";
        echo "// To this:\n";
        echo "'reg_time', 'city', 'state_prov', 'country_id','lottery_id', 'member_active', 'subscription_key', 'ip_address', 'terms_agreement', 'validation_expiry'));";
        echo "</pre>";
        
        echo "<h5>2. In Member_m.php model:</h5>";
        echo "<pre>";
        echo "// Change this line (around line 65):\n";
        echo "// \$member->validation_expiry = NULL; // Will add after creating DB column\n";
        echo "// To this:\n";
        echo "\$member->validation_expiry = NULL; // Expiry date for email validation link";
        echo "</pre>";
        
        echo "</div>";
        
        echo "<br><p><strong>After making these changes:</strong></p>";
        echo "<ul>";
        echo "<li>New registrations will have 5-day expiry dates</li>";
        echo "<li>Activation links will respect expiry dates</li>";
        echo "<li>Used links will be properly disabled</li>";
        echo "</ul>";
    }

    /**
     * Add all Canadian provinces with placeholder lotteries if needed
     */
    public function initialize_canadian_provinces()
    {
        // Only allow in development
        if (ENVIRONMENT !== 'development') {
            show_404();
            return;
        }
        
        echo "<h3>Initializing All Canadian Provinces:</h3>";
        
        // All Canadian provinces and territories
        $canadian_provinces = array(
            'AB' => 'Alberta',
            'BC' => 'British Columbia', 
            'MB' => 'Manitoba',
            'NB' => 'New Brunswick',
            'NL' => 'Newfoundland and Labrador',
            'NS' => 'Nova Scotia',
            'ON' => 'Ontario',
            'PE' => 'Prince Edward Island',
            'QC' => 'Quebec',
            'SK' => 'Saskatchewan',
            'NT' => 'Northwest Territories',
            'NU' => 'Nunavut',
            'YT' => 'Yukon'
        );
        
        $inserted = 0;
        foreach ($canadian_provinces as $code => $name) {
            // Check if province already has lottery data
            $exists = $this->db->where('lottery_country_id', 'CA')
                             ->where('lottery_state_prov', $code)
                             ->get('lottery_profiles')
                             ->num_rows();
            
            if ($exists == 0) {
                // Add a placeholder lottery for this province
                $lottery_data = array(
                    'lottery_country_id' => 'CA',
                    'lottery_state_prov' => $code,
                    'lottery_name' => $name . ' Regional Lottery',
                    'lottery_description' => 'Regional lottery for ' . $name
                );
                
                $this->db->insert('lottery_profiles', $lottery_data);
                echo "✓ Added placeholder lottery for: {$name} ({$code})<br>";
                $inserted++;
            } else {
                echo "- {$name} ({$code}) already has lottery data<br>";
            }
        }
        
        echo "<br><strong>Total provinces initialized: {$inserted}</strong><br>";
        echo "<br><a href='" . site_url('member/debug_lottery_data') . "'>View Updated Lottery Data</a><br>";
        echo "<a href='" . site_url('member/complete_profile') . "'>Test Profile Form</a>";
    }

    /**
     * Show profile completion success page
     */
    public function profile_complete_success()
    {
        $this->data['maintenance'] = $this->maintenance_m->maintenance_check();
        $this->data['subview'] = 'member/profile_complete_success';
        $this->load->view('_main_layout', $this->data);
    }

    /**
     * Debug: Test session data
     */
    public function debug_session()
    {
        if (ENVIRONMENT !== 'development') {
            show_404();
            return;
        }
        
        echo "<h3>Profile Completion Session Data:</h3>";
        echo "<pre>";
        print_r($this->session->userdata('profile_completion_member'));
        echo "</pre>";
        
        echo "<h3>All Session Data:</h3>";
        echo "<pre>";
        print_r($this->session->userdata());
        echo "</pre>";
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
    
    /**
     * Validate secure password requirements
     * @param string $password
     * @return boolean
     */
    public function _validate_secure_password($password)
    {
        // Check minimum length (8 characters)
        if (strlen($password) < 8) {
            $this->form_validation->set_message('_validate_secure_password', 'Password must be at least 8 characters long.');
            return FALSE;
        }
        
        // Check for at least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            $this->form_validation->set_message('_validate_secure_password', 'Password must contain at least one uppercase letter.');
            return FALSE;
        }
        
        // Check for at least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            $this->form_validation->set_message('_validate_secure_password', 'Password must contain at least one lowercase letter.');
            return FALSE;
        }
        
        // Check for at least one digit
        if (!preg_match('/[0-9]/', $password)) {
            $this->form_validation->set_message('_validate_secure_password', 'Password must contain at least one number.');
            return FALSE;
        }
        
        // Check for at least one special character
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\?\/]/', $password)) {
            $this->form_validation->set_message('_validate_secure_password', 'Password must contain at least one special character (!@#$%^&*()_+-=[]{};\':"|,.<>?/).');
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
    
    /**
     * Update location information for existing members
     * Only available in development environment
     */
    public function update_member_locations()
    {
        // Only allow in development
        if (ENVIRONMENT !== 'development') {
            show_404();
            return;
        }
        
        echo "<h3>Updating Member Location Information:</h3>";
        
        // Get all members with IP addresses but missing location data
        $this->db->select('id, ip_address, ip_address_readable');
        $this->db->from('members');
        $this->db->where('(location_city IS NULL OR location_city = "")');
        $this->db->where('ip_address_readable IS NOT NULL');
        $members = $this->db->get()->result();
        
        if (empty($members)) {
            echo "<p>No members found that need location updates.</p>";
            return;
        }
        
        echo "<p>Found " . count($members) . " members to update...</p>";
        
        $updated_count = 0;
        foreach ($members as $member) {
            if (!empty($member->ip_address_readable)) {
                $location_info = detect_location_by_ip($member->ip_address_readable);
                
                if ($location_info['success']) {
                    $update_data = array(
                        'location_city' => $location_info['city'],
                        'location_region' => $location_info['region'],
                        'location_country' => $location_info['country'],
                        'location_country_code' => $location_info['country_code'],
                        'location_detected_at' => $location_info['detected_at']
                    );
                    
                    $this->db->where('id', $member->id);
                    if ($this->db->update('members', $update_data)) {
                        echo "<p style='color: green;'>✓ Updated member ID {$member->id} location: {$location_info['city']}, {$location_info['country']}</p>";
                        $updated_count++;
                    } else {
                        echo "<p style='color: red;'>✗ Failed to update member ID {$member->id}</p>";
                    }
                } else {
                    echo "<p style='color: orange;'>⚠ Could not detect location for member ID {$member->id} (IP: {$member->ip_address_readable})</p>";
                }
                
                // Small delay to be respectful to the API
                usleep(100000); // 0.1 second delay
            }
        }
        
        echo "<p><strong>Location update complete! Updated {$updated_count} out of " . count($members) . " members.</strong></p>";
        echo "<p><a href='" . site_url('admin/membership') . "'>View Updated Members in Admin Panel</a></p>";
    }
}