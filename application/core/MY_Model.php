<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class MY_Model extends CI_Model {
	
	protected $_table_name = '';
	protected $_primary_key = 'id';
	protected $_primary_filter = 'intval';
	protected $_order_by = '';
	public $rules = array();
	protected $_time_stamps = FALSE;
	
	function __construct() {
		parent::__construct();
	}
	
	public function array_from_post($fields) {
		$data = array();
		foreach($fields as $field) {
				$data[$field] = $this->input->post($field);
		}
		return $data;
	}
	
	public function get($id = NULL, $single = FALSE) {
		
		if ($id != NULL) {
			$filter = $this->_primary_filter;
			$id = $filter($id);
			$this->db->where($this->_primary_key, $id);
			$method = 'row';
		} elseif ($single == TRUE) {
			$method = 'row';
		} else {
			$method = 'result';
		}
	   
	   if (!is_array($this->db->order_by('id'))) {  // Depreciated in PHP 7.2 count($this->db->order_by('id')
	   		$this->db->order_by($this->_order_by);
	}
		return $this->db->get($this->_table_name)->$method();
	}
	
	public function get_by($where, $single = FALSE) {
		$this->db->where($where);
		return $this->get(NULL, $single);
	}
	
	public function save($data, $id = NULL) {
		// Debug logging
		log_message('debug', 'MY_Model->save() called with data: ' . print_r($data, true));
		log_message('debug', 'MY_Model->save() called with ID: ' . ($id ? $id : 'NULL'));
		log_message('debug', 'MY_Model->save() table name: ' . $this->_table_name);
		
		// Set Timestamps
		if ($this->_time_stamps==TRUE) {
			$now = date('Y-m-d H:i:s');
			$id || $data['created'] = $now;
			$data['modified'] = $now;
		}
		
		// Insert
		if ($id == NULL) {
			!isset($data[$this->_primary_key])||$data[$this->_primary_key]=NULL;
			log_message('debug', 'MY_Model->save() performing INSERT');
			$this->db->set($data);
			$insert_result = $this->db->insert($this->_table_name);
			$insert_id = $this->db->insert_id();
			
			// Debug the insert
			log_message('debug', 'MY_Model->save() insert result: ' . ($insert_result ? 'TRUE' : 'FALSE'));
			log_message('debug', 'MY_Model->save() insert ID: ' . $insert_id);
			$db_error = $this->db->error();
			log_message('debug', 'MY_Model->save() DB error after insert: ' . print_r($db_error, true));
			
			$id = $insert_id;
		// Update
		} else {
			$filter = $this->_primary_filter;
			$id = $filter($id);
			log_message('debug', 'MY_Model->save() performing UPDATE for ID: ' . $id);
			$this->db->set($data);
			$this->db->where($this->_primary_key, $id);
			$update_result = $this->db->update($this->_table_name);
			
			// Debug the update
			log_message('debug', 'MY_Model->save() update result: ' . ($update_result ? 'TRUE' : 'FALSE'));
			$db_error = $this->db->error();
			log_message('debug', 'MY_Model->save() DB error after update: ' . print_r($db_error, true));
		}
		
		log_message('debug', 'MY_Model->save() returning ID: ' . $id);
		return $id;
	}
	
	public function delete($id) {
		$filter = $this->_primary_filter;
		$id = $filter($id);
	
		if (!$id) {
				return FALSE;
		}
		$this->db->where($this->_primary_key, $id);
		$this->db->limit(1);
		$this->db->delete($this->_table_name);
	}

	/**
	 * Iterates a array to a standard object
	 *
	 * @params      $obj, $arr    	object reference, array to be interated
	 * @return      $obj		  	Returns object values
	 */
	public function array_to_object($obj, $arr)
	{
		foreach ($arr as $key => $value)
		{
    		$obj->$key = $value;
		}
	return $obj;	
	}
	
	/**
	* logged_online method
	* 
	* @param	integer	$user 	0 = members, 1 = admins		
	* @return   integer	$logged->num_rows(); logged in members/admins  		
	*/
	public function logged_online($user=0)
	{
		$table = ($user ? 'users' : 'members');
		$logged = $this->db->query('select * FROM '.$table.' WHERE logged_in=1');
	return $logged->num_rows();
	}

	/**
	* Reset Old Loggings, if they are automatically logged out
	* 
	* @param	string	$tble 	'members', 'users', default = 'members'		
	* @return   none	
	*/
	public function deactivate_old($tbl='members')
	{
		$this->db->reset_query();
		$expiration_time = time() - intval($this->config->item('sess_expiration'));	// Past time (current time - expiration period)
		$activities = $this->db->select('*')
                	->where('logged_in', '1')
					->where('last_active <=', $expiration_time)
                	->get($tbl);

		if($activities->num_rows() > 0)
		{
			foreach($activities->result_object() as $activity)
			{
				$this->db->where('id', $activity->id);
				$this->db->update($tbl, array('logged_in' => 0));	
			}
		} 
	}

}