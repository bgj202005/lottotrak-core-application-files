<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends Frontend_Controller {
	
	function __construct() {
		parent::__construct();
	}

	public function index() 
	{
		$this->data['maintenance'] = $this->maintenance_m->maintenance_check();
		if(!$this->data['maintenance']) 
		{
			$slug = (is_null($this->uri->segment(1)) ? array('slug' => (string) 'home') :  array('slug' => (string) $this->uri->segment(1)));
			$this->data['page'] = $this->page_m->get_by($slug, TRUE);
			if(is_null($this->data['page']))
			{
				$this->error_404($slug); // Related Page not found
			}
			else
			{
				$this->data['page']->body = stripslashes($this->data['page']->body); // Remove the slashes from the database.
				if (!is_null($this->data['page']->raw)) $this->data['page']->raw = stripslashes($this->data['page']->raw); // Remove the slashes from the database.
				if (!is_null($this->data['page']->description)) $this->data['page']->description = stripslashes($this->data['page']->description);
				/* (!empty($this->data['page'])) || show_404(current_url()); */ // Depreciated in PHP 7.2 count($this->data['page'])
					if($this->data['page']->template=='homepage')  // Include Bottom Left & Bottom Right Positions
					{
						$this->data['page_bottom_left'] = $this->page_m->home_pages('bottom_left');
						$this->data['page_bottom_left']->body = stripslashes($this->data['page_bottom_left']->body); // Remove the slashes from the database.
						$this->data['page_bottom_right'] = $this->page_m->home_pages('bottom_right');
						$this->data['page_bottom_right']->body = stripslashes($this->data['page_bottom_right']->body); // Remove the slashes from the database.
					} 
					$this->data['sidebar_top'] = $this->page_m->side_bar('top_section');
					$this->data['sidebar_top']->body = stripslashes($this->data['sidebar_top']->body); // Remove the slashes from the database.
					$this->data['sidebar_middle'] = $this->page_m->home_pages('middle_section');
					$this->data['sidebar_middle']->body = stripslashes($this->data['sidebar_middle']->body); // Remove the slashes from the database.
					$this->data['sidebar_bottom'] = $this->page_m->side_bar('bottom_section');
					$this->data['sidebar_bottom']->body = stripslashes($this->data['sidebar_bottom']->body); // Remove the slashes from the database.

					$this->data['recent_news'] = $this->article_m->get_recent();
				
				$method = '_'.$this->data['page']->template;
				
				if (method_exists($this, $method)) {
					$this->$method();
				} else 
				{
					log_message('error', 'Could not load template '.$method.' in file ' . __FILE__. ' at line '. __LINE__);
				} 
				add_meta_title($this->data['page']->title);
				add_meta_description($this->data['page']->description);
				add_meta_canonical($this->data['page']->canonical);
				$this->data['page_m'] = $this->page_m; 	
				$this->data['subview'] = $this->data['page']->template;
				$this->load->view('_main_layout', $this->data);
			}
		}
		else
		{
			$this->offline(); // Present the entire frontend offline with the page offline template. Give a maintenance message
		}
	}
	
	private function _page() {
		
		$this->data['recent_news'] = $this->article_m->get_recent();
	}

	private function _homepage() {

		$this->article_m->set_published();
		
		$this->db->limit(6);
		$this->data['articles'] = $this->article_m->get();
	}
	
	private function _newsarticle() {
		
		//$this->load->model('article_m');
		$this->data['recent_news'] = $this->article_m->get_recent();
		// Count all articles
		// Set up pagination
		// Fetch Articles
		//$this->db->where('pubdate <=', date('Y-m-d'));
		$this->article_m->set_published();
		$count = $this->db->count_all_results('articles');

		$perpage = 4;
		if ($count > $perpage) {
			$this->load->library('pagination');
			$config['base_url'] = site_url($this->uri->segment(1).'/');
			$config['total_rows'] = $count;
			$config['per_page'] = $perpage;
			$config['url_segment'] = 2;
				
			$this->pagination->initialize($config);
			
			//echo $this->pagination->create_links();
			$this->data['pagination'] = $this->pagination->create_links();

			$offset = $this->uri->segment(2);
		}
		else {
			$this->data['pagination'] = '';
			$offset = 0;
		}
		
		//dump($this->pagination);
		// Fetch the articles
		//$this->db->where('pubdate <=', date('Y-m-d'));
		
		$this->article_m->set_published();
		
		//$this->db->order_by("'pubdate' desc");
		$this->db->limit($perpage, $offset);
		
		$this->data ['articles'] = $this->article_m->get();
		
		// dump($this->data['articles']);
		
		// dump(count($this->data['articles']));
		
		//echo '<pre>'. $this->db->last_query().'</pre>';
		
	}
	
	function register() {
		
		/*if ($this->input->server('REQUEST_METHOD') == 'POST') { */
			if ($this->input->is_ajax_request()) {
				
		
				// Setup the form
				$new_member_rules = $this->page_m->new_member_rules;
				$this->form_validation->set_rules($new_member_rules);
		
				if ($this->form_validation->run()  == TRUE) {

					redirect('page/dashboard');
				} else { 
				// Make sure the user does not exist and the email does not exist.
					$error = validation_errors();
					echo json_encode(['js-reg-error'=>$error]);
					/* $this->form_validation->set_message('signup_error', '<div class="alert alert-danger signup_error">This Username Already Exists.</div>'); */
				}
				
				$action = [];
				
				/* $action['redirect'] = 'page/dashboard';
				$this->output
				   		->set_content_type('application/json') //set Json header
				   		->set_output(json_encode($action, JSON_PRETTY_PRINT))
						->_display();
			 exit;// make output json encoded 
			*/ return;
		} else {
			exit('test');
		}
		/*$this->load->library('form_validation');
		$rules = $this->page_m->new_member_rules; 
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>'); // Displaying Errors in Div
		if ($this->form_validation->run() == FALSE) {
			$this->data['subview'] = $this->data['page']->template;
			$this->load->view('_main_layout', $this->data);
		} else {
		// Initializing database table columns.
		$data = array(
		'username' => $this->input->post('username'),
		'email' => $this->input->post('email'),
		'password' => $this->input->post('password'),
		);
	}	*/
//		echo "This is the register function.";
	}

	function login() {
		
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		
		$is_form = $this->input->post('userform');
		$data['html'] = "Hello";
		if ($is_form) {
			echo $data;
		   }
			
		}
	
	function LoginValidate() {
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('username','Username', 'required');
		$this->form_validation->set_rules('password','Password', 'required');
		$this->form_validation->set_rules('confirm-password','Confirm Password', 'trim|matches[password]');
		//$this->form_validation->set_rules('email','Email Address','required|valid_email|is_unique[email]');
	if ($this->form_validation->run() == FALSE) {
			echo validation_errors();
	} 
	else {
	  // To who are you wanting with input value such to insert as 
		  $data['usename']=$this->input->post('username');
		  $data['password']=$this->input->post('password');
		  $data['email']=$this->input->post('email');
	  // Then pass $data  to Modal to insert bla bla!!
		}
	}
	
	public function _unique_username($id)
	{
		// Do Not validate if email already exists
		// Unless it's the email for the current user	
		
		$this->db->where('username', $this->input->post('username'));
			!$id || $this->db->where('id !=', $id);
			$user = $this->user_m->get();
		
			if (count($user)) {
				$this->form_validation->set_message('_unique_username', '%s already exists. Please type another username');
				return FALSE;
			}
	return TRUE;
	}
	
	public function _unique_email($id)
	{
		// Do Not validate if email already exists
		// Unless it's the email for the current user

		$this->db->where('email', $this->input->post('email'));
		!$id || $this->db->where('id !=', $id);
		$user = $this->user_m->get();
			
		if (count($user)) {
			$this->form_validation->set_message('_unique_email', '%s already exists. Please type another email address');
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Error 404 Pages
	 * @param       $keyword	Same as Slug on the page but it is not a slug that currently exists
	 * @return      none
 	*/
	public function error_404($keyword)
	{
	   $this->output->set_status_header('404');
	   $this->data['sidebar_top'] = $this->page_m->side_bar('top_section');
	   $this->data['sidebar_top']->body = stripslashes($this->data['sidebar_top']->body); // Remove the slashes from the database.
 	   $this->data['sidebar_middle'] = $this->page_m->home_pages('middle_section');
	   $this->data['sidebar_middle']->body = stripslashes($this->data['sidebar_middle']->body); // Remove the slashes from the database.
	   $this->data['sidebar_bottom'] = $this->page_m->side_bar('bottom_section');
	   $this->data['sidebar_bottom']->body = stripslashes($this->data['sidebar_bottom']->body); // Remove the slashes from the database.

	   $this->data['recent_news'] = $this->article_m->get_recent();
	   $this->data['slug'] = $keyword['slug']; //keyword / slug
       $this->data['subview'] = 'errors/error_404';
	   add_meta_title($this->data['slug'].' not found');
	   add_meta_description('Check the URL of the page and try again.  This page does not exist.');
	   add_meta_canonical(0);
	   add_meta_noindex();
	   $this->load->view('_main_layout', $this->data);
    }
	
	/**
	 * Sets the Website Offline Message to the Frontend
	 * 
	 * @param       none
	 * @return      none
	 */
	public function offline()
	{
	   $this->data['sidebar_top'] = $this->page_m->side_bar('top_section');
	   $this->data['sidebar_top']->body = stripslashes($this->data['sidebar_top']->body); // Remove the slashes from the database.
 	   $this->data['sidebar_middle'] = $this->page_m->home_pages('middle_section');
	   $this->data['sidebar_middle']->body = stripslashes($this->data['sidebar_middle']->body); // Remove the slashes from the database.
	   $this->data['sidebar_bottom'] = $this->page_m->side_bar('bottom_section');
	   $this->data['sidebar_bottom']->body = stripslashes($this->data['sidebar_bottom']->body); // Remove the slashes from the database.

	   $this->data['recent_news'] = $this->article_m->get_recent();
       $this->data['subview'] = 'offline';
	   $this->load->view('_main_layout', $this->data);
    }
}