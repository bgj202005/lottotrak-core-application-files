<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Frontend_Controller extends MY_Controller
{
	function __construct() {
		parent::__construct();
		
		$this->load->helper('form');
		$this->load->helper('cookie');
		$this->load->helper('string');
		$this->load->library('form_validation');
		$this->load->library('session');

    	// Load stuff
    	$this->load->model('page_m');
    	$this->load->model('article_m');
		$this->load->model('maintenance_m'); 
	
    	// Fetch Navigation
    	$this->data['menu'] = $this->page_m->get_nested(0);
    	$this->data['footer_menu_inside'] = $this->page_m->get_nested(1); // Footer Inside Menu
    	$this->data['footer_menu_outside'] = $this->page_m->get_nested(2); // Footer Outside Menu
    	$this->data['news_article_link'] = $this->page_m->get_article_link();
    	$this->data['meta_title'] = config_item('site_name');
	}
}
