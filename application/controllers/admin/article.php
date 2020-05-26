<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Article extends Admin_Controller {
	
	public function __construct() {
		 parent::__construct();
		 $this->load->model('article_m');
		 		
	}
	
	public function index() {
		// Fetch all articles
		$this->data['articles'] = $this->article_m->get();

		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the Article menu
		$this->data['subview'] = 'admin/article/index';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function edit($id = NULL) {
		
		// Fetch a article or set a new one
		
	    if ($id) {
			$this->data['article'] = $this->article_m->get($id);
			$this->data['article']->body = $this->strip_false_tags($this->data['article']->body); // Strip HTML out of tinymce editor
			is_object($this->data['article']) || $this->data['errors'][] = 'article could not be found'; //deprecated php 7.2+ count($this->data['article']) 
		} else {
			$this->data['article'] = $this->article_m->get_new();
		}
		
		// Setup the form
		$rules = $this->article_m->rules;
		$this->form_validation->set_rules($rules);
		
		if ($this->form_validation->run()  == TRUE) {
				
			// We can save and redirect
			$data = $this->article_m->array_from_post ( array (
					'title',
					'slug',
					'pubdate',
					'modified',
					'body' 
			) );
			$data['pubdate'] = date( 'Y-m-d', strtotime(str_replace('/', '-', $data['pubdate'])));
			$data['modified'] = date( 'Y-m-d', strtotime(str_replace('/', '-', $data['modified'])));
			$data['body'] = addslashes($data['body']);				// Sanitize Data going to the database
			$this->article_m->object_from_article_post($data, $this->data['article']);

			$this->article_m->save($data, $id);
			if (!$this->uri->segment(5))  redirect('admin/article');	// Save and Exit
		}
		// Load the View
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->data['subview']  = 'admin/article/edit';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function delete($id) {
		
		$this->article_m->delete($id);
		redirect('admin/article');
		
	}
}