<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends Admin_Controller {
	
	public function __construct() {
		 parent::__construct();
		 $this->load->model('page_m');
		 $this->load->model('maintenance_m');
	}
	
	public function index() {
		// Fetch all pages
		$this->data['pages'] = $this->page_m->get_with_parent();
		
		// Load the view
		$this->data['current'] = $this->uri->segment(2); // Sets the Page Menu
		$this->session->set_userdata('uri', 'admin/'.$this->data['current']);
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->page_m->logged_online(0);	// Members
		$this->data['admins'] = $this->page_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins		 
		$this->data['subview'] = 'admin/page/index';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function edit($id = NULL) {
		
		// Initialize errors array
		$this->data['errors'] = array();
		
		// Fetch a page or set a new one
		
		if ($id) 
		{
			$this->data['page'] = $this->page_m->get($id);
			
			// Check if page exists before trying to access properties
			if(!is_object($this->data['page']) || empty($this->data['page']))
			{
				$this->data['errors'][] = 'page could not be found';
				$this->data['page'] = $this->page_m->get_new(); // Set default new page
			}
			else
			{
				// Only process if page object exists
				$this->data['page']->body = $this->strip_false_tags($this->data['page']->body); // Strip HTML out of tinymce editor
				if(!is_null($this->data['page']->raw) && $this->data['page']->raw !== '') {
					$this->data['page']->raw = $this->strip_false_tags($this->data['page']->raw);
				}
			}
		}
		else
		{
			$this->data['page'] = $this->page_m->get_new();	
		}
		
		// Ensure raw field is properly initialized for the view
		if(is_null($this->data['page']->raw)) {
			$this->data['page']->raw = '';
		}
		

		// pages for dropdown
		$this->data['pages_no_parents'] = $this->page_m->get_no_parents();
		
		// Debug: Check database table structure
		$query = $this->db->query("DESCRIBE pages");
		if($query) {
			$fields = $query->result();
			log_message('debug', 'Pages table structure: ' . print_r($fields, true));
		} else {
			log_message('error', 'Could not describe pages table: ' . $this->db->error()['message']);
		}
		
		// Setup the form - TEMPORARILY use simplified rules for debugging
		$rules = array(
			array(
				'field' => 'title',
				'label' => 'Title',
				'rules' => 'trim|required|max_length[100]'
			),
			array(
				'field' => 'slug',
				'label' => 'Slug',
				'rules' => 'trim|max_length[100]|callback__unique_slug'
			)
		);
		
		// Use simplified rules temporarily for testing
		$this->form_validation->set_rules($rules);
		
		// Debug: Show what we received
		if ($_POST) {
			log_message('debug', 'POST data received: ' . print_r($_POST, true));
			log_message('debug', 'Form validation rules: ' . print_r($rules, true));
			
			// Auto-generate slug if it's empty but we have a title
			if (empty($_POST['slug']) && !empty($_POST['title'])) {
				$_POST['slug'] = $this->generate_slug($_POST['title']);
				log_message('debug', 'Auto-generated slug: ' . $_POST['slug']);
			}
		}
		
		if ($this->form_validation->run()  == TRUE) {
			
			// Debug: Show what validation passed
			log_message('debug', 'Form validation passed');
				
			// We can save and redirect
			$data = $this->page_m->array_from_post ( array (
					'title',
					'slug',
					'order',
					'body',
					'raw',
					'template',
					'position',
					'menu_item',
					'parent_id',
					'menu_id',
					'description',
					'canonical'
			) );
			
			// Debug: Log the data being saved
			log_message('debug', 'Page save data: ' . print_r($data, true));
			
			// Set default values for null fields
			if(!isset($data['menu_item']) || is_null($data['menu_item']) || $data['menu_item'] === '') $data['menu_item'] = 0;
			if(!isset($data['canonical']) || is_null($data['canonical']) || $data['canonical'] === '') $data['canonical'] = 0;
			if(!isset($data['order']) || is_null($data['order']) || $data['order'] === '') $data['order'] = 0;
			if(!isset($data['parent_id']) || is_null($data['parent_id']) || $data['parent_id'] === '') $data['parent_id'] = 0;
			if(!isset($data['menu_id']) || is_null($data['menu_id']) || $data['menu_id'] === '') $data['menu_id'] = 0;
			
			// Ensure required fields have values
			if(empty($data['title'])) $data['title'] = 'Untitled Page';
			if(empty($data['slug'])) $data['slug'] = 'untitled-page-' . time();
			if(empty($data['template'])) $data['template'] = 'page';
			if(empty($data['position'])) $data['position'] = 'full_page';
			
			// Sanitize data going to the database
			$data['body'] = addslashes($data['body']);
			if(!is_null($data['raw']) && $data['raw'] !== '') $data['raw'] = addslashes($data['raw']);

		// Debug: Log before save attempt
		log_message('debug', 'About to call page_m->save() with data: ' . print_r($data, true));
		log_message('debug', 'Save ID parameter: ' . ($id ? $id : 'NULL (new page)'));

		// Fix: Don't pass 'save' as an ID - only pass numeric IDs
		$save_id = (is_numeric($id)) ? $id : NULL;
		log_message('debug', 'Corrected save ID: ' . ($save_id ? $save_id : 'NULL (new page)'));

		$save_result = $this->page_m->save($data, $save_id);
		
		// Debug: Log save result
		log_message('debug', 'Save result: ' . print_r($save_result, true));
		log_message('debug', 'Save result type: ' . gettype($save_result));
		
		// Check for database errors
		$db_error = $this->db->error();
		log_message('debug', 'Database error after save: ' . print_r($db_error, true));
		
		if (!empty($db_error['message'])) {
			$this->data['errors'][] = 'Database error: ' . $db_error['message'];
			log_message('error', 'Page save error: ' . $db_error['message']);
		}
		
		if ($save_result) {
			// Save was successful
			log_message('debug', 'Save successful. URI segment 5: ' . $this->uri->segment(5));
			log_message('debug', 'Current ID: ' . ($id ? $id : 'NULL'));
			log_message('debug', 'Save ID: ' . ($save_id ? $save_id : 'NULL'));
			
			if ($this->uri->segment(5) === 'save') {
				// "Save and No Exit" was clicked - reload the page with saved data
				log_message('debug', 'Save and No Exit detected');
				if (!$save_id) {
					// For new pages, redirect to edit the newly created page
					log_message('debug', 'New page created. Redirecting to edit page with ID: ' . $save_result);
					redirect('admin/page/edit/' . $save_result . '/save');
				} else {
					// For existing pages, reload the current page with saved data
					log_message('debug', 'Existing page updated. Reloading page data for ID: ' . $save_id);
					$this->data['page'] = $this->page_m->get($save_id);
						
						// Strip false tags for display (same as when loading existing pages)
						if (is_object($this->data['page'])) {
							$this->data['page']->body = $this->strip_false_tags($this->data['page']->body);
							if(!is_null($this->data['page']->raw) && $this->data['page']->raw !== '') {
								$this->data['page']->raw = $this->strip_false_tags($this->data['page']->raw);
							}
						}
						
						// Ensure raw field is properly initialized for the view
						if(is_null($this->data['page']->raw)) {
							$this->data['page']->raw = '';
						}
						$this->data['success_message'] = 'Page saved successfully.';
					}
				} else {
					// "Save and Exit" was clicked - redirect to page list
					redirect('admin/page');
				}
			} else {
				// Save failed
				$this->data['errors'][] = 'Failed to save page. Please try again.';
			}		
		}
		else {
			// Form validation failed - errors will be displayed in the view
			if ($_POST) {
				// Form was submitted but validation failed
				log_message('debug', 'Form validation failed');
				log_message('debug', 'Validation errors: ' . print_r($this->form_validation->error_array(), true));
				log_message('debug', 'POST data: ' . print_r($this->input->post(), true));
				$this->data['errors'][] = 'Please check the form for errors and try again.';
			}
		}
		
		// Load the default position
		$template = (isset($this->data['page']->template) && !empty($this->data['page']->template)) ? $this->data['page']->template : 'page';
		$this->data['position_options'] = $this->default_position($template);

		// Load the View
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/edit'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->page_m->logged_online(0);	// Members
		$this->data['admins'] = $this->page_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins		 
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
		// Unless it's the slug for a new page	
			$id = $this->uri->segment(4);
			$this->db->where('slug', $this->input->post('slug'));
			!$id || $this->db->where('id !=', $id);
			$page = $this->page_m->get();
			
			if ($page && (is_object($page) || is_array($page))) {
				$this->form_validation->set_message('_unique_slug', '%s already exists. Please type another slug for the page');
				return FALSE;
			}
	return TRUE;
	}
	
	/**
	 * Generate a URL-friendly slug from a title
	 */
	public function generate_slug($title) {
		// Convert to lowercase
		$slug = strtolower($title);
		
		// Replace spaces and special characters with hyphens
		$slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
		
		// Remove leading/trailing hyphens
		$slug = trim($slug, '-');
		
		// Limit length to 100 characters
		if (strlen($slug) > 100) {
			$slug = substr($slug, 0, 100);
			$slug = trim($slug, '-');
		}
		
		// Ensure uniqueness by adding a number if needed
		$original_slug = $slug;
		$counter = 1;
		while ($this->slug_exists($slug)) {
			$slug = $original_slug . '-' . $counter;
			$counter++;
		}
		
		return $slug;
	}
	
	/**
	 * Check if a slug already exists in the database
	 */
	private function slug_exists($slug) {
		$id = $this->uri->segment(4);
		$this->db->where('slug', $slug);
		if ($id) {
			$this->db->where('id !=', $id);
		}
		$page = $this->page_m->get();
		return ($page && (is_object($page) || is_array($page)));
	}
	
	public function order()
	{
		$this->data['sortable'] = TRUE;
		$this->data['menu_id'] = $this->uri->segment(4); // Request Header, Footer (inside) or Footer (Outside)
		$this->data['current'] = $this->data['menu_id']; // Hightlight Menu 0, 1 or 2
		$this->session->set_userdata('uri', 'admin/page/order/'.$this->data['current']);
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->page_m->logged_online(0);		// Members
		$this->data['admins'] = $this->page_m->logged_online(1);	// Admins
		$this->data['visitors'] = $this->maintenance_m->active_visitors();	// Active Visitors excluding users and admins	 
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

	/**
	 * Returns the positional array from the template give. homepage, right_sidebar, newsarticle, page 
	 *
	 * @param		str $tpl 		Name of Template
	 * @return      arr $position	Array of positions from the template
	 */
	public function default_position($tpl)
	{
		switch($tpl)
		{
			case 'homepage':
				return array('top_home' => 'Top Section', 'bottom_left' => 'Bottom Left', 'bottom_right' => 'Bottom Right');
			case 'sidebar':
				return array('top_section' => 'Top', 'middle_section' => 'Middle', 'bottom_section' => 'Bottom');
			case 'newsarticle':
				return array('featured_article' => 'featured', 'archived_article' => 'archived');
			case 'page':
				return array('full_page' => 'Full Page');
		}
	}

	/**
	 * Ajax call to return the positional array from the template give. homepage, right_sidebar, newsarticle, page 
	 *
	 * @param		none
	 * @return      none			
	 */

	public function get_position()
	{
		//$template = $this->input->post['template'];   // template id
		header("Content-type: text/javascript");
		$template = $_POST['template'];

		$position_arr = array();

		switch($template)
		{
			case 'homepage' :
				$position_arr[] = array(
									array(
										'id' => 'top_section', 
										'name' => 'Top Section'
									),
									array(
										'id' => 'bottom_left', 
										'name' => 'Bottom Left'
									), 
									array(
										'id' => 'bottom_right', 
										'name' => 'Bottom Right'
									));
			break;
			case 'sidebar':
				$position_arr[] = array(
									array(
										'id' => 'top_section', 
										'name' => 'Top Section'
									),
									array(
										'id' => 'middle_section', 
										'name' => 'Middle Section' 
									), 
									array(
										'id' => 'bottom_secton', 
										'name' => 'Bottom Section'
									));
			case 'newsarticle':
				$position_arr[] = array(
									array(
										'id' => 'featured_article', 
										'name' => 'Featured Article'
									), 
									array(
										'id' => 'archived_article', 
										'name' => 'Archived Article'
									));
			break;
			case 'page':
				$position_arr[] = array(
									array(
										'id' =>'page', 
										'name' => 'Full Page'
									));	

		} 
		// encoding array to json format
		echo json_encode($position_arr);
	}
}