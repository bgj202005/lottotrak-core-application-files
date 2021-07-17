<?php
class Maintenance_m extends MY_Model
{
    /**
	 * Checks the status of the frontend of the website for maintenance mode or "live"
	 * @param	none
	 * @return 	boolean	Return True or False value to indicate if the Frontend is active or not
	 * */
	public function maintenance_check()
	{
		// Quick Maintenance Check (Frontend off or Frontend On)
		$this->db->select('maintenance');
		$this->db->from('maintenance');
		$query = $this->db->get();
		$row = $query->row();
	return $row->maintenance;
	}

 	/**
	 * Toggles between the "Live" mode to the "Maintenance" mode of the frontend of the website
	 * @param	none
	 * @return 	boolean	$active		Return True indicating the website is in maintenance mode
	 * */
	public function maintenance_on()
	{
		$active = 1; // "Maintenance" Mode
		$this->db->update('maintenance', array('maintenance' => $active));	
	return $active;
	}

    /**
	* Toggles between from the "Maintenance" mode to the "live" mode of the frontend of the website
	 * @param	none
	 * @return 	boolean	$active	Return False indicating the website is now "live"
	 * */
	public function maintenance_off()
	{
		// Quick Maintenance Check (Frontend off or Frontend On)
		$active = 0; // "Live" Mode
		$this->db->update('maintenance', array('maintenance' => $active));	
	return $active;
	}
	/**
	 * Turns on/off the admin as a logged in user
	 * @param	integer	$admin_id	Admin user id
	 * @param	integer	$active		Currently logged in is 1 or logged out is 0
	 * @return 	none
	 * */
	public function logged($admin_id, $active = 0)
	{
		
		$ip = sprintf("%u", ip2long($this->user_m->adminIP())); // Convert the IP address to INT, convert to back using long2ip(sprintf("%d", $ip_address));
		$this->db->where('id', $admin_id);
		$this->db->update('users', array('logged_in' => $active, 'last_active' => time(), 'ip_address' => $ip));	
	}
	/**
	 * Looks at the current visitors on the website, excluding admins or members
	 * @param	none	
	 * @return 	integer $visitors['active_count']  Current Active Visitors on the website. Not Admins or Members
	 * */
	public function active_visitors()
	{
		$query = $this->db->query("SELECT * FROM `visitors`");
		$visitors = $query->row_array();	// A non-object value and an array
	return (int) $visitors['active_count']; // Return value must be an integer
	}
}