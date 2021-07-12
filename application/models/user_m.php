 <?php
 defined('BASEPATH') OR exit('No direct script access allowed');
 
class User_M extends MY_Model 
{

	protected $_table_name = 'users';
	protected $_order_by = 'name';

	public $rules = array (
			'username' => array (
					'field' => 'username',
					'label' => 'Username',
					'rules' => 'trim|required|xss_clean'
			),
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

	function __construct() 
	{
		parent::__construct();
	}
	
	public function login() 
	{
		$user = $this->get_by(array(
				'email' => $this->input->post('email')),TRUE);
		$user_validate = $this->input->post('username');
		
		if (isset($user)&&$user->username==$user_validate) 
		{  // checking for a user and same username
			$hashed_password = $user->password;
			if ($this->check_password($this->input->post('password'), $hashed_password)) 
			{ 
				$data = array (
						'username' => $user->username,
						'name' => $user->name,
						'email' => $user->email,
						'id' => $user->id,
						'loggedin' => TRUE,
						'uri'	=> 'admin/dashboard/' 
				);
				$this->session->set_userdata ( $data );
				$this->logged($user->id,1); // Admin is actively logged
				return TRUE;
			}
		} 
		elseif (isset($user)&&$user->username!=$user_validate) 
		{
			return "username";	
		} 
			return FALSE;
    	}
	
	public function logout() 
	{
		$this->logged($this->session->userdata('id'),0); // Admin is actively not logged
		$this->session->unset_userdata('username');
		$this->session->unset_userdata('name');
		$this->session->unset_userdata('email');
		$this->session->unset_userdata('id');
		$this->session->unset_userdata('loggedin');
		$this->session->unset_userdata('uri');
		$this->session->sess_destroy();
	}
	
	public function loggedin() 
	{
		return (bool) $this->session->userdata('loggedin');
	}
	
	public function get_new() 
	{
		$user = new stdClass();
		$user->name = '';
		$user->username = '';
		$user->password = '';
		$user->email = '';
		
		return $user;
	}

	public function hash($password)
	{
		return password_hash($password, PASSWORD_DEFAULT);
	}
	
	/* public function hash($password, $unique_salt) 
	{
		return crypt($password, '$2a$10$'.$unique_salt);
	} */
	
	public function check_password($password, $hash)
	{
	// returns true or false
		return password_verify($password, $hash);
	}

	/**
	 * Checks if the email address exists
	 * 
	 * @param       $email_value   string
	 * @return      $row object (user) or False
	 */
	 public function Email_exists($email_value) 
	 {
	     $sql = "SELECT id, email, name, username FROM users WHERE email = '{$email_value}' LIMIT 1";
	     $result = $this->db->query($sql);
	     $row = $result->row();
	     return ($result->num_rows() === 1 && $row->email) ? $row : FALSE;
	 }

	 /**
	  * Return the email address for the administration user from the user id
	  *
	  * @params      $id (user)  integer
	  * @return      $email or boolean  string or TRUE/FALSE
	  */
	 
	 public function Retrieve_email($id_key) 
	 {
	     $sql = "SELECT email FROM users WHERE id = '{$id_key}' LIMIT 1";
	     $result = $this->db->query($sql);
	     $row = $result->row();
	     
	 return ($result->num_rows(1) === 1 && $row->email) ? $row->email : FALSE;
	 }
	 /**
	  * Sends out email for password reset
	  * The relative url will be sent as admin/user/reset_password/id/email_code
	  * @params		 $id (user), $email, $firstname   integer, string, string
	  * @return      none
	  */
	 
	public function Send_email($id, $email, $name) 
	{
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
	
	public function verify_reset_password($email, $code) 
	{
	    $sql = "SELECT name, email FROM users WHERE email = '{$email}' LIMIT 1";
	    $result = $this->db->query($sql);
	    $row = $result->row();
	    
	    if ($result->num_rows() === 1) 
		{
	       return ($code == md5($this->config->item('salt').$row->name)) ? TRUE : FALSE;
	    } 
		else 
		{
	        return FALSE;
	    }
	}

}