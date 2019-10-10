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
	        'password' => array (
	                'field' => 'password',
	                'label' => 'Password',
	                'rules' => 'trim|required|min_length[6]|max_length[30]|xss_clean',
            ),
            'confirm_password' => array (
                'field' => 'confirm_password',
                'label' => 'Confirm Password',
                'rules' => 'trim|matches[password]'
        ),
	);
    
    /**
     * If id does not exist, create a new member with default blank values
     * @param none
     * @return void
     */

    public function get_new_member() 
	{
		$member = new stdClass();
        $member->first_name = '';
        $member->last_name = '';
        $member->username = '';
		$member->password = '';
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
    
    public function login_database($data) {
        
        // Does the User Exist?
        
        	$active_user = $this->get_by(array(
            'username' => $data['user_login'],TRUE));
        
        if (isset($active_user)) {
            
            $hashed_password = $active_user->password;            
            
            if ($this->check_password($data['password_login'], $hashed_password)) {
                
                $user_data = array (
                    'username' => $active_user->username,
                    'email' => $active_user->email,
                    'first_name' => $active_user->first_name,
                    'last_name' => $active_user->last_name,
                    'city' => $active_user->city,
                    'state_prov' => $active_user->state_prov,
                    'country' => $active_user->country,
                    'lotteries' => $active_user->lotteries,
                    'id' => $active_user->id,
                    'loggedin' => TRUE
                );
                
                $this->session->set_userdata ( $data );
            } else {
                return false;
            }
        }
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
                   'activate/?q='. $urlsecuretoken . ' />Validate your account.</a></strong></p>';
        $message .= '"<p>If your account is not validated within 5 days, the account will be deleted</p>';           
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
}