<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin_Controller extends MY_Controller
{
	function __construct() {
		parent::__construct();
		$this->data['meta_title'] = 'Lottotrak';
		$this->load->helper('form');
		$this->load->helper('string');
		$this->load->library('form_validation');
		$this->load->library('session');
		$this->load->model('user_m');
		// Login Check
		$exception_uris = array (
		      'admin/user/login',
		      'admin/user/logout',
		      'admin/user/forgotpassword',
		      'admin/user/reset_password',
		      'admin/user/update_password'
		);
		
		$uri_string = (string) $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3);
		//var_dump(preg_grep(uri_string(), $exception_uris)); exit(1);
		if (in_array($uri_string, $exception_uris) == FALSE) { // in_array(uri_string(), $exception_uris) similar to uri_string()
				if ($this->user_m->loggedin() == FALSE) {
					redirect('admin/user/login');
				} else {
					// Check for session timeout with user-specific inactivity setting
					$this->check_session_timeout();
				}
		}	
		
	}
	
	/**
	 * Check if the current session has exceeded the user's inactivity timeout
	 */
	private function check_session_timeout() {
		$user_id = $this->session->userdata('id');
		$last_activity = $this->session->userdata('last_activity');
		
		if ($user_id) {
			// Get user's inactivity timeout setting (with fallback for pre-migration state)
			$this->load->database();
			$timeout_seconds = 1800; // Default 30 minutes
			
			try {
				// Check if inactivity_timeout column exists
				if ($this->db->field_exists('inactivity_timeout', 'users')) {
					$query = $this->db->select('inactivity_timeout')
									  ->from('users')
									  ->where('id', $user_id)
									  ->get();
					
					$user = $query->row();
					$timeout_seconds = ($user && isset($user->inactivity_timeout)) ? $user->inactivity_timeout : 1800;
				}
			} catch (Exception $e) {
				// Column doesn't exist yet (pre-migration) - use default timeout
				log_message('debug', 'Admin_Controller: inactivity_timeout column not found, using default timeout');
			}
			
			$current_time = time();
			
			// If last_activity is not set, set it now
			if (!$last_activity) {
				$this->session->set_userdata('last_activity', $current_time);
				return;
			}
			
			// Check if session has timed out
			if (($current_time - $last_activity) > $timeout_seconds) {
				// Session timed out - logout and redirect to login
				$this->user_m->logout();
				$this->session->set_flashdata('timeout_message', 'Your session has expired due to inactivity. Please log in again.');
				redirect('admin/user/login');
			} else {
				// Update last activity timestamp for active sessions
				$this->session->set_userdata('last_activity', $current_time);
			}
		}
	}
	function strip_false_tags($s)
    {
        //stops non tags being converted into tags
       $search = array("/\&60;/","/\&62;/");
	   $replace= array(htmlspecialchars("<"),htmlspecialchars(">"));
        
        return preg_replace($search, $replace, $s);
    }
}
