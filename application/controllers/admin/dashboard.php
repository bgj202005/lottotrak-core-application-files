<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller 
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');

	}
		
	public function index() 
	{
		// Fetch Recently Modified Articles
		$this->load->model('article_m');
		$this->db->order_by('modified desc');
		$this->db->limit(5);
		$this->data['recent_articles'] = $this->article_m->get();
		// Fetch Most Recent Pages
		$this->load->model('page_m');
		$this->db->order_by('id desc'); // new updated column //
		$this->db->limit(5);
		$this->data['recent_pages'] = $this->page_m->get(); 
		
		$this->data['current'] = $this->uri->segment(2); // Sets the default
		$this->data['subview'] = 'admin/dashboard/index';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function modal() 
	{
		$this->load->view('admin/_layout_modal', $this->data);
	}
}