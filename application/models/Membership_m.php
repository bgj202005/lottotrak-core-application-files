 <?php
 defined('BASEPATH') OR exit('No direct script access allowed');
 
class Membership_M extends MY_Model 
{

	protected $_table_name = 'members';
	protected $_order_by = 'username';

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
					'rules' => 'trim|xss_clean'
			),
			'email' => array (
					'field' => 'email',
					'label' => 'Email',
					'rules' => 'trim|xss_clean'
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
	
	/**
	 * Creates a Member Data Standard Object
	 * 
	 * @param       none
	 * @return      none
	 */
	public function get_new() 
	{
		$member = new stdClass();
		$member->id = NULL;
		$member->username = '';
		$member->password = '';
		$member->email = '';
		$member->first_name = '';
		$member->last_name = '';
		$member->city = '';
		$member->state_prov = '';
		$member->country_id = '';
		$member->lottery_id = '';
		$member->member_active = 0;
		$member->reg_time = date('Y-m-d H:i:s'); // Current Time Stamp
		return $member;
	}

	public function hash($password)
	{
		return password_hash($password, PASSWORD_DEFAULT);
	}
	
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
	  * @param	     $id (user) integer
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
	  * @param		 $id (user), $email, $firstname   integer, string, string
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
	 * @param	    $email, $code    string
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
	
	/**
	 * Retrieve the List of Lotteries base on Country
	 *
	 * @param	    string	$country_code	Two letter designation for country
	 * @return      array	List of Lotteries in the Country
	 */
	public function lotteries_list($country_code = 'CA')
	{
	
	$query = $this->db->query("SELECT lottery_name from lottery_profiles where lottery_country_id = '".$country_code."'");
	
	$c = 1;
	$result = array();
		foreach ($query->result_array() as $row)
		{
			$result[$c] = $row['lottery_name'];
			$c++;	
		}
	
	return $result;
	}
	/**
	 * Retrieve the Selected Lotteries
	 *
	 * @param	    string	$lotto_id	Lotteries currently selected
	 * @return      string				String of currently selected lotteries on there account.
	 */
	public function lotteries_selected($lotto_id)
	{
	
		$id = array();
		$result = "";
		$id = explode(',', $lotto_id);
			foreach($id as $c)
			{
				$query = $this->db->query("SELECT lottery_name from lottery_profiles where id = '".$c."'");
				$row = $query->row();
				if (isset($row))
				{
					$result .= $row->lottery_name;
					$result	.= '<br />';
				}
			}
	return $result;
	}		
}