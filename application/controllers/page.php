<?php
class Page extends Frontend_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->model('page_m');
	}

	public function index() {
		$slug = (is_null($this->uri->segment(1)) ? array('slug' => (string) 'home') :  array('slug' => (string) $this->uri->segment(1)));
		$this->data['page'] = $this->page_m->get_by($slug, TRUE);
		count($this->data['page']) || show_404(current_url());
		 
		//echo '<pre>'. $this->db->last_query().'</pre>';
		$this->data['recent_news'] = $this->article_m->get_recent();
		
		$method = '_'.$this->data['page']->template;
		
		if (method_exists($this, $method)) {
			$this->$method();
		} else {
			log_message('error', 'Could not load template '.$method.' in file ' . __FILE__. ' at line '. __LINE__);
		} 
		$this->data['subview'] = $this->data['page']->template;
		$this->load->view('_main_layout', $this->data);
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
				
				// Make sure the user does not exist
				// Make sure the Can be added AND is added
				// Return the proper information back to JavaScript to redirect to.
			
				$action = [];
				$action[] = ["test", "test2", "test3"];
				$action['redirect'] = '/index.php?this-is-a-redirect';
				$this->output
				   		->set_content_type('application/json') //set Json header
				   		->set_output(json_encode($action, JSON_PRETTY_PRINT))
						->_display();
			 exit;// make output json encoded
			return;
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
	
}