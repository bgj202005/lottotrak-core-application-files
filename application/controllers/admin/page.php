<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends Admin_Controller {
	
	public function __construct() {
		 parent::__construct();
		 $this->load->model('page_m');
	}
	
	public function index() {
		// Fetch all pages
		$this->data['pages'] = $this->page_m->get_with_parent();
		
		// Load the view
		$this->data['subview'] = 'admin/page/index';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function edit($id = NULL) {
		
		// Fetch a page or set a new one
		
	    if ($id) {
			$this->data['page'] = $this->page_m->get($id);
			//$this->data['page']->body = $this->strip_false_tags($this->data['page']->body); // Strip HTML out of tinymce editor
			is_object($this->data['page']) || $this->data['errors'][] = 'page could not be found'; // deprecated php 7.2+ count($this->data['page']) 		} else {
			$this->data['page'] = $this->page_m->get_new();
		}
		
		// pages for dropdown
		$this->data['pages_no_parents'] = $this->page_m->get_no_parents();
		// Setup the form
		$rules = $this->page_m->rules;
		$this->form_validation->set_rules($rules);
		
		if ($this->form_validation->run()  == TRUE) {
				
			// We can save and redirect
			$data = $this->page_m->array_from_post ( array (
					'title',
					'slug',
					'order',
					'body',
					'template',
					'parent_id',
			        'menu_id' 
			) );
			$data['body'] = addslashes($data['body']);
			$this->page_m->save($data, $id);
			redirect('admin/page');
		}
		
		// Load the View
		$this->data['subview']  = 'admin/page/edit';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function delete($id) {
		
		$this->page_m->delete($id);
		redirect('admin/page');
		
	}

	public function _unique_slug($str)
	{
		// Do Not validate if slug already exists
		// Unless it's the slug for a newt page	
			$id = $this->uri->segment(4);
			$this->db->where('slug', $this->input->post('slug'));
			!$id || $this->db->where('id !=', $id);
			$page = $this->page_m->get();
			
			if (count($page)) {
				$this->form_validation->set_message('_unique_slug', '%s already exists. Please type another slug for the page');
				return FALSE;
			}
	return TRUE;
	}
	
	public function order()
	{
		$this->data['sortable'] = TRUE;
		$this->data['menu_id'] = $this->uri->segment(4); // Request Header, Footer (inside) or Footer (Outside)
		$this->data['subview'] = 'admin/page/order';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function order_ajax()
	{
	    $this->data['menu_id'] = $this->uri->segment(4); // Request Header, Footer (inside) or Footer (Outside)
		if (isset($_POST['sortable'])) {
			foreach ($_POST['sortable'] as $order => $menu_item) {
	            $_POST['sortable'][$order]['menu_id'] = $this->data['menu_id'];
	        } 
	        $this->page_m->save_order($_POST['sortable']);
 		}
		// fetch all pages
		$this->data['pages'] = $this->page_m->get_nested($this->data['menu_id']);
		// Load view
		$this->load->view('admin/page/order_ajax', $this->data);
	}
}