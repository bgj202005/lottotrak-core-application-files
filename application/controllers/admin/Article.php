<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Article extends Admin_Controller {
	
	public function __construct() {
		 parent::__construct();
		 $this->load->model('Article_m', 'article_m');
		 $this->load->model('maintenance_m');		
	}
	
	public function index() {
		// Fetch all articles
		$this->data['articles'] = $this->article_m->get();

		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the Article menu
		$this->session->set_userdata('uri', 'admin/'.$this->data['current']);
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	 
		$this->data['subview'] = 'admin/article/index';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function edit($id = NULL) {
		
		// Initialize errors array
		$this->data['errors'] = array();
		
		// Fetch a article or set a new one
		
		if ($id) {
			$this->data['article'] = $this->article_m->get($id);
			
			// Check if article exists before trying to access properties
			if(!is_object($this->data['article']) || empty($this->data['article']))
			{
				$this->data['errors'][] = 'article could not be found';
				$this->data['article'] = $this->article_m->get_new(); // Set default new article
			}
			else
			{
				// Only process if article object exists
				$this->data['article']->body = $this->strip_false_tags($this->data['article']->body); // Strip HTML out of tinymce editor
				if(!is_null($this->data['article']->raw) && $this->data['article']->raw !== '') {
					$this->data['article']->raw = stripslashes($this->data['article']->raw); // Remove the slashes from the database.
				}
			}
		} else {
			$this->data['article'] = $this->article_m->get_new();
		}
		
		// Ensure raw field is properly initialized for the view
		if(is_null($this->data['article']->raw)) {
			$this->data['article']->raw = '';
		}
		
		// Setup the form - Use simplified rules with auto-slug generation
		$rules = array(
			array(
				'field' => 'title',
				'label' => 'Title',
				'rules' => 'trim|required|max_length[100]'
			),
			array(
				'field' => 'slug',
				'label' => 'Slug',
				'rules' => 'trim|max_length[100]'
			),
			array(
				'field' => 'pubdate',
				'label' => 'Publication date',
				'rules' => 'trim|required|exact_length[10]'
			),
			array(
				'field' => 'body',
				'label' => 'Body',
				'rules' => 'trim|required'
			)
		);
		
		$this->form_validation->set_rules($rules);
		
		// Auto-generate slug if it's empty but we have a title
		if ($_POST) {
			if (empty($_POST['slug']) && !empty($_POST['title'])) {
				$_POST['slug'] = $this->generate_slug($_POST['title'], $id);
			}
		}
		
		if ($this->form_validation->run()  == TRUE) {
				
			// We can save and redirect
			$data = $this->article_m->array_from_post ( array (
					'title',
					'slug',
					'pubdate',
					'modified',
					'body',
					'raw',
					'description',
					'canonical' 
			) );
			
			// Set default values for null fields
			if(!isset($data['canonical']) || is_null($data['canonical']) || $data['canonical'] === '') $data['canonical'] = 0;
			if(!isset($data['description']) || is_null($data['description'])) $data['description'] = '';
			if(!isset($data['raw']) || is_null($data['raw'])) $data['raw'] = '';
			
			// Handle date formatting
			if (!empty($data['pubdate'])) {
				$data['pubdate'] = date( 'Y-m-d H:i:s', strtotime(str_replace('/', '-', $data['pubdate'])));
			}
			if (!empty($data['modified'])) {
				$data['modified'] = date( 'Y-m-d H:i:s', strtotime(str_replace('/', '-', $data['modified'])));
			}
			
			// Add created field for new articles
			if (!$id || !is_numeric($id)) {
				$data['created'] = date('Y-m-d H:i:s');
			}
			
			// Sanitize data going to the database
			$data['body'] = addslashes($data['body']);
			if(!empty($data['description'])) $data['description'] = addslashes($data['description']);
			if(!empty($data['raw'])) $data['raw'] = addslashes($data['raw']);
			
			// Transfer data to object
			$this->article_m->object_from_article_post($data, $this->data['article']);
			
			// Fix: Don't pass 'save' as an ID - only pass numeric IDs
			$save_id = (is_numeric($id)) ? $id : NULL;
			
			$save_result = $this->article_m->save($data, $save_id);
			
			if ($save_result) {
				// Check if this is a "Save and No Exit" request
				$is_save_no_exit = ($this->uri->segment(5) === 'save') || (strpos($this->uri->uri_string(), '/save') !== false);
				
				// Save was successful
				if ($is_save_no_exit) {
					// "Save and No Exit" was clicked - reload the page with saved data
					if (!$save_id) {
						// For new articles, redirect to edit the newly created article
						redirect('admin/article/edit/' . $save_result . '/save');
					} else {
						// For existing articles, reload the current page with saved data
						$this->data['article'] = $this->article_m->get($save_id);
						
						// Strip false tags for display (same as when loading existing articles)
						if (is_object($this->data['article'])) {
							$this->data['article']->body = $this->strip_false_tags($this->data['article']->body);
							if(!is_null($this->data['article']->raw) && $this->data['article']->raw !== '') {
								$this->data['article']->raw = stripslashes($this->data['article']->raw);
							}
						}
						
						// Ensure raw field is properly initialized for the view
						if(is_null($this->data['article']->raw)) {
							$this->data['article']->raw = '';
						}
						$this->data['success_message'] = 'Article saved successfully.';
						
						// Important: Don't redirect, just continue to load the view with success message
					}
				} else {
					// "Save and Exit" was clicked - redirect to article list
					redirect('admin/article');
				}
			} else {
				// Save failed
				$this->data['errors'][] = 'Failed to save article. Please try again.';
			}
		}
		// Load the View
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/edit'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->maintenance_m->logged_online(0);	// Members
		$this->data['admins'] = $this->maintenance_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	  
		$this->data['subview']  = 'admin/article/edit';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function delete($id) {
		
		$this->article_m->delete($id);
		redirect('admin/article');
		
	}
	
	/**
	 * Generate a URL-friendly slug from a title
	 */
	private function generate_slug($title, $current_id = null) {
		// Convert to lowercase and replace spaces/special chars with hyphens
		$slug = strtolower($title);
		$slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
		$slug = preg_replace('/[\s-]+/', '-', $slug);
		$slug = trim($slug, '-');
		
		// Ensure uniqueness
		$original_slug = $slug;
		$counter = 1;
		
		while ($this->slug_exists($slug, $current_id)) {
			$slug = $original_slug . '-' . $counter;
			$counter++;
		}
		
		return $slug;
	}
	
	/**
	 * Check if a slug already exists in the database (excluding current article)
	 */
	private function slug_exists($slug, $current_id = null) {
		$this->db->where('slug', $slug);
		if ($current_id) {
			$this->db->where('id !=', $current_id);
		}
		$query = $this->db->get('articles');
		return $query->num_rows() > 0;
	}
}