<?php
class Member_m extends MY_Model
{
    
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
	        )
	);
	
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
}