<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Article extends Frontend_Controller {
	
	public function __construct() {
		parent::__construct();
		//$this->load->model('article_m');
		$this->data['recent_news'] = $this->article_m->get_recent();
	}
	
	public function index($id, $slug) 
	{
		
		// Fetch the 404, if not found
		
		//$this->db->where('pubdate <=', date('Y-m-d'));
		
		$this->article_m->set_published();
		$this->data['article'] = $this->article_m->get($id);
		if(is_null($this->data['article']))
		{
			$this->error_404();
		}
		else
		{
			$this->data['article']->body = strip_slashes($this->data['article']->body); // Remove the slashes from the database.
			if (!is_null($this->data['article']->description)) $this->data['article']->description = strip_slashes($this->data['article']->description);
			is_object($this->data['article']) || show_404(uri_string()); // Depreciated in PHP 7.2 count($$this->data['article'])
		
			$this->db->limit(6);
			$this->data['articles'] = $this->article_m->get();
			// Return 404 if not found
			// Redirect if slug was incorrect
			
			$requested_slug = $this->uri->segment(3);
			$set_slug = $this->data['article']->slug;
			
			if ($requested_slug != $set_slug) {
				redirect('article/'. $this->data['article']->id.'/'.$this->data['article']->slug, 'location', '301');
			}
			// Load Sidebar
			$this->data['sidebar_top'] = $this->page_m->side_bar('top_section');
			$this->data['sidebar_top']->body = strip_slashes($this->data['sidebar_top']->body); // Remove the slashes from the database.
			$this->data['sidebar_middle'] = $this->page_m->home_pages('middle_section');
			$this->data['sidebar_middle']->body = strip_slashes($this->data['sidebar_middle']->body); // Remove the slashes from the database.
			$this->data['sidebar_bottom'] = $this->page_m->side_bar('bottom_section');
			$this->data['sidebar_bottom']->body = strip_slashes($this->data['sidebar_bottom']->body); // Remove the slashes from the database.
			// Load view
			add_meta_title($this->data['article']->title);
			add_meta_description($this->data['article']->description);
			add_meta_canonical($this->data['article']->canonical);
			$this->data['subview'] = 'article';
			$this->load->view('_main_layout', $this->data);
		}
	}
	public function error_404()
	{
	   $this->output->set_status_header('404');
	   $this->data['recent_news'] = $this->article_m->get_recent();
       $this->data['subview'] = 'errors/error_404';
	   $this->load->view('_main_layout', $this->data);
    }
}