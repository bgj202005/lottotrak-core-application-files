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
		$this->data['subview'] = 'admin/page/index';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function edit($id = NULL) {
		
		// Fetch a page or set a new one
		
		if ($id) 
		{
			$this->data['page'] = $this->page_m->get($id);
			$this->data['page']->body = $this->strip_false_tags($this->data['page']->body); // Strip HTML out of tinymce editor
			if(!is_null($this->data['page']->raw)) $this->data['page']->raw = $this->strip_false_tags($this->data['page']->raw);
			if(is_object($this->data['page'])||empty($this->data['page']))
			{
				$this->data['errors'][] = 'page could not be found';
			}
			//count($this->data['page'])  || $this->data['errors'][] = 'page could not be found'; // deprecated php 7.2+ count($this->data['page'])	} else {
			//$this->data['page'] = $this->page_m->get_new();
		}
		else
		{
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
					'raw',
					'template',
					'position',
					'menu_item',
					'parent_id',
					'menu_id',
					'description',
					'canonical'
			) );
			
			if(is_null($data['menu_item'])) $data['menu_item'] = 0;
			if(is_null($data['canonical'])) $data['canonical'] = 0;
			$data['body'] = addslashes($data['body']);				// Sanitize Data going to the database
			if(!is_null($data['raw'])) $data['raw'] = addslashes($data['raw']);
			$this->page_m->object_from_page_post($data, $this->data['page']);

			$this->page_m->save($data, $id);
			if (!$this->uri->segment(5))  redirect('admin/page');	// Save and Redirect		
		}
		
		// Load the default position
		$this->data['position_options'] = $this->default_position($this->data['page']->template);

		// Load the View
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/edit'.($id ? '/'.$id : ''));
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->page_m->logged_online(0);	// Members
		$this->data['admins'] = $this->page_m->logged_online(1);	// Admins	 
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
		$this->data['current'] = $this->data['menu_id']; // Hightlight Menu 0, 1 or 2
		$this->session->set_userdata('uri', 'admin/page/order/'.$this->data['current']);
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		$this->data['users'] = $this->page_m->logged_online(0);		// Members
		$this->data['admins'] = $this->page_m->logged_online(1);	// Admins	 
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