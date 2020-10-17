<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statistics extends Admin_Controller {
	
	public function __construct() {
		 parent::__construct();
		 $this->load->model('lotteries_m');
		 $this->load->model('statistics_m');
		 $this->load->helper('file');
		 $this->load->library('image_lib');
	}

	/**
	 * Retrieves List of All Lotteries
	 * 
	 * @param       none
	 * @return      none
	 */
	public function index() { 
		// Fetch all lotteries from the database
		$this->data['lotteries'] = $this->lotteries_m->get();
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the lotteries menu
		$this->data['subview'] = 'admin/lotteries/index';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	

}