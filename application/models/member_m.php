<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Member_m extends MY_Model
{
    protected $_table_name = 'members';
	protected $_order_by = 'username';
    
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
        $member->city = '';
        $member->province_state = '';
        $member->country = '';
        $member->lottery_id = 0;
        $member->active = False;        
		
		return $member;
    }
    /**
     * Save (Overridden Method from MY_Model)
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
            
            $hashed_password = $user->password;            
            
            if ($this->check_password($hashed_password, $data['password_login'])) {
                
                $user_data = array (
                    'username' => $ctive_user->username,
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
    public function validate_email($id, $email, $name) 
	{
	    $this->load->library('email');
	    $email_code = md5($this->config->item('salt').$name);
	    $this->email->set_mailtype('html');
	    $this->email->from('info@lottotrak.com', 'Lottotrak Administration');
	    $this->email->to($email);
	    $this->email->subject('Activate Your Account');
	    //$URI_Encoded_email = rawurlencode($email);
	    $message = '<DOCTYPE html PUCLIC "-//W3C//DTD XHTML 1.0 Strict/EN"
	            "http://www.w3.org/TR/xhtml1-strict-dtd"><HTML>
	            <meta http-equiv="Content-Type" content="text/html; charseet=urf-8" />
	            </head><body>';
	    $message .= '<p>Hi '. $name . ',</p>';
	    // the link we send will look like: /login/reset_password/john@doe.com/d23c45da23cc367742vn0209vn
	    $message .= '<p>You have Registered for an account with Lottotrak. Please Activate Your Account by clicking on this link here to <strong><a href ="'.base_url().
	       	    'member/'.$id.'/'.$email_code . '" />Validate your account</a></strong></p>';
	    $message .= '<p>Thank you!</p>';
	    $message .= '<p>Lottotrak Administrator';
	    $message .= '</body></html>';
	    $this->email->message($message);
	    $this->email->send();
	}
}