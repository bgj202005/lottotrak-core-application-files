<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin_Controller extends MY_Controller
{
	function __construct() {
		parent::__construct();
		$this->data['meta_title'] = 'Lottotrak';
		$this->load->helper('form');
		$this->load->helper('string');
		$this->load->library('form_validation');
		$this->load->library('session');
		$this->load->model('user_m');
		// Login Check
		$exception_uris = array (
		      'admin/user/login',
		      'admin/user/logout',
		      'admin/user/forgotpassword',
		      'admin/user/reset_password',
		      'admin/user/update_password'
		);
		
		$uri_string = (string) $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3);
		//var_dump(preg_grep(uri_string(), $exception_uris)); exit(1);
		if (in_array($uri_string, $exception_uris) == FALSE) { // in_array(uri_string(), $exception_uris) similar to uri_string()
				if ($this->user_m->loggedin() == FALSE) {
					redirect('admin/user/login');
				}
		}	
		
	}
	function strip_false_tags($s)
    {
        //stops non tags being converted into tags
       $search = array("/\&60;/","/\&62;/");
	   $replace= array(htmlspecialchars("<"),htmlspecialchars(">"));
        
        return preg_replace($search, $replace, $s);
    }
}
