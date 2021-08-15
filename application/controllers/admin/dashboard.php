<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller 
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->model('article_m');
		$this->load->model('page_m');
		$this->load->model('maintenance_m');
	}
		
	public function index() 
	{
		// Fetch Recently Modified Articles
		$this->db->order_by('modified desc');
		$this->db->limit(5);
		$this->data['recent_articles'] = $this->article_m->get();
		// Fetch Most Recent Pages
		$this->db->order_by('id desc'); // new updated column //
		$this->db->limit(5);
		$this->data['recent_pages'] = $this->page_m->get(); 
		
		$this->data['current'] = $this->uri->segment(2); // Sets the default
		$this->session->set_userdata('uri', 'admin/'.$this->data['current']);
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		//$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		//$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		//$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins
		$this->data['subview'] = 'admin/dashboard/index';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function modal() 
	{
		$this->load->view('admin/_layout_modal', $this->data);
	}
}