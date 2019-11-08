<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Activate_m extends MY_Model
{
    protected $_table_name = 'members';
    protected $_order_by = 'username';
    protected $_primary_key = 'id';
    protected $_primary_filter = 'intval';

    public $activation_member_rules = array(
        'username' => array (
                 'field' => 'username',
                 'label' => 'Username',
                 'rules' => 'required|min_length[5]|max_length[15]',
         ),
         'email' => array (
                 'field' => 'email',
                 'label' => 'Email',
                 'rules' => 'trim|required|valid_email|xss_clean',
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
          'first_name' => array (
              'field' => 'first_name',
              'label' => 'First Name',
              'rules' => 'required|min_length[4]|max_length[25]',
        ),
        'state_prov' => array (
            'field' => 'state_prov',
            'label' => 'State or Province',
            'rules' => 'required|callback_state_prov_check',
        ),
          'country_id' => array (
              'field' => 'country_id',
              'label' => 'Country',
              'rules' => 'required|callback_country_check',
        ),
            'lottery_id' => array (
            'field' => 'lottery_id',
            'label' => 'Lottery',
            'rules' => 'required|callback_lottery_check',
        ),
    );

    public function update_status($subscriptionkey, $subscriptionstatus)
    {
        $sql = "UPDATE ".$this->_table_name." SET member_active = ".$this->db->escape($subscriptionstatus)." WHERE subscription_key = ".$this->db->escape($subscriptionkey);
        $result = $this->db->query($sql);
        
        if (empty($result)) 
		{
			return FALSE;
		}
	return TRUE;
    }

    public function validate_member($subscriptionkey)
    {
        $now = date('Y-m-d H:i:s');
        $subscriptionstatus = 0; // New Member and not active
       
        $sql = "SELECT * FROM ".$this->_table_name." where subscription_key = ".$this->db->escape($subscriptionkey)." and member_active = ".$this->db->escape($subscriptionstatus)." 
        and (DATEDIFF(CURDATE(), reg_time) <= 5) LIMIT 1";
        $result = $this->db->query($sql);
        $row = $result->row();
        
        if (!count($row)) 
		{
			return FALSE;
		}
	return $row->id;
    }
    
    /**
	 * Checks if the email address exists
	 * 
	 * @param       $id_value   integer
	 * @return      $row object (member) or False
	 */
	 public function get_member($id_value) 
	 {
	     $sql = "SELECT * FROM ".$this->_table_name." WHERE id = '{$id_value}' LIMIT 1";
	     $result = $this->db->query($sql);
	     $row = $result->row();
	     return ($result->num_rows() === 1) ? $row : FALSE;
     }
     
    /**
	 * Checks if the email address exists
	 * 
	 * @param       $password   string
	 * @none        no return
	 */
     public function hash($password)
	{
		return password_hash($password, PASSWORD_DEFAULT);
	}
}   