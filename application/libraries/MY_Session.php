<?php
class MY_session extends CI_Session {
	
	function sess_update() 
	{
		// Listin to the HTTP_X_REQESTED_WITH	
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
			parent::sess_update();
		}
	}
}