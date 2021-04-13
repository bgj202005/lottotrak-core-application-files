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
}