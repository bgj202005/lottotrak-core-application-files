<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Member_m extends MY_Model
{
    protected $_table_name = 'members';
    protected $_order_by = 'username';
    protected $_primary_key = 'id';
	protected $_primary_filter = 'intval';
    protected $_time_stamps = TRUE;
    
    public $new_member_rules = array(
	       'username' => array (
	                'field' => 'username',
	                'label' => 'Username',
	                'rules' => 'required|min_length[5]|callback__unique_username|max_length[15]',
	        ),
	        'email' => array (
	                'field' => 'email',
	                'label' => 'Email',
	                'rules' => 'trim|required|callback__unique_email|valid_email|xss_clean',
	        ),
    );
    
    public $forgot_password_rules = array(
        'email_forgot' => array (
                'field' => 'email_forgot',
                'label' => 'Email Address',
                'rules' => 'trim|required|valid_email|xss_clean',
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
    
    /**
     * create a new member with default blank values
     * @param none
     * @return void
     */

    public function get_new_member() 
	{
		$member = new stdClass();
        $member->first_name = '';
        $member->last_name = '';
        $member->username = '';
	    $member->email = '';
        $member->reg_time = '';
        $member->city = '';
        $member->state_prov = '';
        $member->country_id = 0;
        $member->lottery_id = 0;
        $member->member_active = 0; // 0 = Member not active, 1 = Member Active        
		
		return $member;
    }
    /**
     * Save (Overridden Method from MY_Model
     *
     * @param [array] $data
     * @param [integer] $id
     * @return $id 
     */

    public function save($data, $id = NULL) {
		// Set Timestamps
        if ($this->_time_stamps==TRUE) {
			$now = date('Y-m-d H:i:s');
			$data['reg_time'] = $now;
		}
		
		// Insert
		if ($id == NULL) {
			!isset($data[$this->_primary_key])||$data[$this->_primary_key]=NULL;
			$this->db->set($data);
			$this->db->insert($this->_table_name);
			$id = $this->db->insert_id();
		// Update
		} else {
			$filter = $this->_primary_filter;
			$id = $filter($id);
			$this->db->set($data);
			$this->db->where($this->_primary_key, $id);
			$this->db->update($this->_table_name);
		}
		return $id;
	}
    
    public function login_database() {
        
        // Does the Member Exist?
        
        $nonmember = $this->get_by(array(
                    'username' => $this->input->post('username_login')),TRUE);

        if (isset($nonmember)) {
            
            $hashed_password = $nonmember->password;            
            
        if ($this->check_password($this->input->post('password_login'), $hashed_password)) {
                
            $member = array (
                   'username' => $nonmember->username,
                   'email' => $nonmember->email,
                   'first_name' => $nonmember->first_name,
                   'last_name' => $nonmember->last_name,
                   'city' => $nonmember->city,
                   'state_prov' => $nonmember->state_prov,
                   'country_id' => $nonmember->country_id,
                   'lottery_id' => $nonmember->lottery_id,
                   'id' => $nonmember->id,
                   'logged_in' => TRUE
                   );
                
            $this->session->set_userdata ($member);
            return TRUE;
        } else {
            return FALSE;
        }
      }
    }

    public function logout_database() 
	{
		$this->session->sess_destroy();
	}
    
    public function hash_password($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    public function check_password($password, $hash) {
        
        // returns true or false
        return password_verify($password, $hash);
    }
    
    public function send_confirmation_message($urlsecuretoken, $mailto) 
	{
	    $this->load->library('email');
	    $this->email->set_mailtype('html');
	    $this->email->from('info@lottotrak.com', 'Lottotrak Administration');
	    $this->email->to($mailto);
	    $this->email->subject('Activate Your Account');
	    $message = '<DOCTYPE html PUCLIC "-//W3C//DTD XHTML 1.0 Strict/EN"
	            "http://www.w3.org/TR/xhtml1-strict-dtd"><HTML>
	            <meta http-equiv="Content-Type" content="text/html; charseet=urf-8" />
	            </head><body>';
	    $message .= '<p>Hi <strong></strong>New Lottotrak Member,</strong></p>';
        $message .= '<p>You have Registered for an account with Lottotrak. To Confirm, this email has been sent to: <strong><u>'.$mailto.'</u></strong>.';
        $message .='<br /><br />To Activate Your Account by clicking on this link here to <strong><a href ="'.base_url().
                   'activate/validation_code/'. $urlsecuretoken . '"/>Validate your account.</a></strong></p>';
        $message .= '<br /><br /><p>If your account is not validated within 5 days, the account will be deleted</p>';           
	    $message .= '<p>Thank you!</p>';
	    $message .= '<p>Lottotrak Administrator';
	    $message .= '</body></html>';
	    $this->email->message($message);
	    $this->email->send();
    }

    /**
     * If you are using PHP, this is the best possible secure hash
     * do not try to implement somthing on your own
     *
     * @param string $text
     * @return string
     */
    public function getSecureHash($text)
    {
        $hashedText = password_hash($text, PASSWORD_DEFAULT);
        return $hashedText;
    }

    /**
     * generates a random token of the length passed
     *
     * @param int $length
     * @return string
     */
    public function getToken($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet) - 1;
        for ($i = 0; $i < $length; $i ++) {
            $token .= $codeAlphabet[$this->cryptoRandSecure(0, $max)];
        }
        return $token;
    }

    public function cryptoRandSecure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) {
            return $min; // not so random...
        }
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }

    /**
     * makes the passed string url safe and return encoded url
     *
     * @param string $str
     * @return string
     */
    public function cleanUrl($str, $isEncode = 'true')
    {
        $delimiter = "-";
        $str = str_replace(' ', $delimiter, $str); // Replaces all spaces with hyphens.
        $str = preg_replace('/[^A-Za-z0-9\-]/', '', $str); // allows only alphanumeric and -
        $str = trim($str, $delimiter); // remove delimiter from both ends
        $regexConseqChars = '/' . $delimiter . $delimiter . '+/';
        $str = preg_replace($regexConseqChars, $delimiter, $str); // remove consequtive delimiter
        $str = mb_strtolower($str, 'UTF-8'); // convert to all lower
        if ($isEncode) {
            $str = urldecode($str); // encode to url
        }
        return $str;
    }

    /**
     * to mitigate XSS attack
     */
    public function xssafe($data, $encoding = 'UTF-8')
    {
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML401, $encoding);
    }

    /**
     * convenient method to print XSS mitigated text
     *
     * @param string $data
     */
    public function xecho($data)
    {
        echo $this->xssafe($data);
    }

    /**
	 * Checks if the email address exists
	 * 
	 * @param       $email_value   string
	 * @return      $row object (user) or False
	 */
	 public function Email_exists($email_value) 
	 {
	     $sql = "SELECT id, email, first_name, username FROM members WHERE email = '{$email_value}' LIMIT 1";
	     $result = $this->db->query($sql);
	     $row = $result->row();
	     return ($result->num_rows() === 1 && $row->email) ? $row : FALSE;
     }
    
     /**
	  * Return the email address for the member from the member id
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
	  * The relative url will be sent as member/reset_password/id/email_code
	  * @params		 $id (user), $email, $firstname   integer, string, string
	  * @return      none
	  */

    public function Send_email($id, $email, $first_name) 
	{
        $this->load->library('email');
        $email_code = md5($this->config->item('salt').$first_name);
	    $this->email->set_mailtype('html');
	    $this->email->from('info@lottotrak.com', 'Lottotrak Administration');
	    $this->email->to($email);
	    $this->email->subject('You requested a password reset for the Lottotrak Administration');
	    //$URI_Encoded_email = rawurlencode($email);
	    $message = '<DOCTYPE html PUCLIC "-//W3C//DTD XHTML 1.0 Strict/EN"
	            "http://www.w3.org/TR/xhtml1-strict-dtd"><HTML>
	            <meta http-equiv="Content-Type" content="text/html; charseet=urf-8" />
	            </head><body>';
	    $message .= '<p>Hi '. $first_name . ',</p>';
	    // the link we send will look like: /login/reset_password/john@doe.com/d23c45da23cc367742vn0209vn
	    $message .= '<p>You have put in a request to reset your password! Please <strong><a href ="'.base_url().
	       	    'member/reset_password/'.$id.'/'.$email_code . '" />click here</a></strong> to reset your password.</p>';
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
	    $sql = "SELECT first_name, email FROM members WHERE email = '{$email}' LIMIT 1";
	    $result = $this->db->query($sql);
	    $row = $result->row();
	    
	    if ($result->num_rows() === 1) 
		{
	       return ($code == md5($this->config->item('salt').$row->first_name)) ? TRUE : FALSE;
	    } 
		else 
		{
	        return FALSE;
	    }
	}
}