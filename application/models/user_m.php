 <?php
class User_M extends MY_Model {

	protected $_table_name = 'users';
	protected $_order_by = 'name';

	public $rules = array (
			'email' => array (
					'field' => 'email',
					'label' => 'Email',
					'rules' => 'trim|required|valid_email|xss_clean' 
			),
			'password' => array (
					'field' => 'password',
					'label' => 'Password',
					'rules' => 'trim|required' 
			) 
	);
	
	public $rules_admin = array (
			'username' => array (
					'field' => 'username',
					'label' => 'Username',
					'rules' => 'trim|required|callback__unique_username|xss_clean'
			),
			'name' => array (
					'field' => 'name',
					'label' => 'Name',
					'rules' => 'trim|required|xss_clean'
			),
			'email' => array (
					'field' => 'email',
					'label' => 'Email',
					'rules' => 'trim|required|valid_email|callback__unique_email|xss_clean'
			),
			'password' => array (
					'field' => 'password',
					'label' => 'Password',
					'rules' => 'trim'
			),
			'password_confirm' => array (
					'field' => 'password_confirm',
					'label' => 'Confirm Password',
					'rules' => 'trim|matches[password]'
			),
				
	);
	
	public $forgot_password_rules = array(
           'email' => array (
                    'field' => 'email',
                    'label' => 'Email',
                    'rules' => 'trim|required|valid_email|xss_clean',
           ),
           'captcha' => array (
                   'field' => 'captcha',
                   'label' => 'Captcha',
                   'rules' => 'required|callback__validate_captcha|xss_clean',
                   ),
	  );
	
	public $update_password_rules = array(
	        'email_hash' => array (
	                'field' => 'email_hash',
	                'label' => 'Email hash',
	                'rules' => 'trim|required',
	        ),
	        'email' => array (
	                'field' => 'email',
	                'label' => 'Email',
	                'rules' => 'trim|required|valid_email|xss_clean',
	        ),
	        'password' => array (
	                'field' => 'password',
	                'label' => 'Password',
	                'rules' => 'trim|required|min_length[6]|max_length[50]|matches[password_confirm]|xss_clean',
	        ),
	        'password_confirm' => array (
	                'field' => 'password_confirm',
	                'label' => 'Confirm password',
	                'rules' => 'trim|required|min_length[6]|max_length[50]|xss_clean',
	        ),
	);
	
	protected $_time_stamps = FALSE;

	function __construct() {
		parent::__construct();
	}
	
	public function login() {
		
		$user = $this->get_by(array(
				'email' => $this->input->post('email')),TRUE);
		
		//dump($user);
		
		if (isset($user)) {
			$hashed_password = $user->password;
			
			if ($this->check_password($hashed_password, $this->input->post('password'))) { 
	
				$data = array (
						'username' => $user->username,
						'name' => $user->name,
						'email' => $user->email,
						'id' => $user->id,
						'loggedin' => TRUE 
				);
				
				$this->session->set_userdata ( $data );
			} else {
				return false;
			}
		  }
		
	return true;
	} 
	
	public function logout() {
		
		$this->session->sess_destroy();
	}
	
	public function loggedin() {
		
		return (bool) $this->session->userdata('loggedin');
	}
	
	public function get_new() {
		$user = new stdClass();
		$user->name = '';
		$user->username = '';
		$user->password = '';
		$user->email = '';
		
		return $user;
	}
	
	public function hash($password, $unique_salt) {
		return crypt($password, '$2a$10$'.$unique_salt);
	}
	
	public function unique_salt() {
		return substr(sha1(mt_rand()),0,22);
	}
	
	public function check_password($hash, $password) {
	
		// first 29 characters include algorithm, cost and salt
		// let's call it $full_salt
		$full_salt = substr($hash, 0, 29);
	
		// run the hash function on $password
		$new_hash = crypt($password, $full_salt);
	
		// returns true or false
		return ($hash == $new_hash);
	}

	/**
	 * Checks if the email address exists
	 * 
	 * @param       $email_value   string
	 * @return      $row object (user) or False
	 */
	 public function Email_exists($email_value) {
	     
	     $sql = "SELECT id, email, name FROM users WHERE email = '{$email_value}' LIMIT 1";
	     $result = $this->db->query($sql);
	     $row = $result->row();
	     
	     return ($result->num_rows() === 1 && $row->email) ? $row : FALSE;
	 }

	 /**
	  * Update to the new password
	  *
	  * @params      $id (user)  integer
	  * @return      $email or boolean  string or TRUE/FALSE
	  */
	 
	 public function Retrieve_email($id_key) {
	     $sql = "SELECT email FROM users WHERE id = '{$id_key}' LIMIT 1";
	     $result = $this->db->query($sql);
	     $row = $result->row();
	     
	 return ($result->num_rows(1) === 1 && $row->email) ? $row->email : FALSE;
	 }
	 /**
	  * Sends out email for password reset
	  *
	  * @params      $email, $firstname    string
	  * @return      none
	  */
	 
	public function Send_email($id, $email, $name) {
	    $this->load->library('email');
	    $email_code = md5($this->config->item('salt').$name);
	    $this->email->set_mailtype('html');
	    $this->email->from('info@lottotrak.com', 'Lottotrak Administration');
	    $this->email->to($email);
	    $this->email->subject('You requested a password reset for the Lottotrak Administration');
	    //$URI_Encoded_email = rawurlencode($email);
	    $message = '<DOCTYPE html PUCLIC "-//W3C//DTD XHTML 1.0 Strict/EN"
	            "http://www.w3.org/TR/xhtml1-strict-dtd"><HTML>
	            <meta http-equiv="Content-Type" content="text/html; charseet=urf-8" />
	            </head><body>';
	    $message .= '<p>Hi '. $name . ',</p>';
	    // the link we send will look like: /login/reset_password/john@doe.com/d23c45da23cc367742vn0209vn
	    $message .= '<p>You have put in a request to reset your password! Please <strong><a href ="'.base_url().
	       	    'admin/user/reset_password/'.$id.'/'.$email_code . '" />click here</a></strong> to reset your password.</p>';
	    $message .= '<p>Thank you!</p>';
	    $message .= '<p>Lottotrak Administrator';
	    $message .= '</body></html>';
	    $this->email->message($message);
	    $this->email->send();
	}
	
	/**
	 * Verify the reset password code 
	 *
	 * @params      $email, $code    string
	 * @return      boolean
	 */
	
	public function verify_reset_password($email, $code) {
	    $sql = "SELECT name, email FROM users WHERE email = '{$email}' LIMIT 1";
	    $result = $this->db->query($sql);
	    $row = $result->row();
	    
	    if ($result->num_rows() === 1) {
	       return ($code == md5($this->config->item('salt').$row->name)) ? TRUE : FALSE;
	    } else {
	        return FALSE;
	    }
	}
}