<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Article extends Frontend_Controller {
	
	public function __construct() {
		parent::__construct();
		//$this->load->model('article_m');
		$this->data['recent_news'] = $this->article_m->get_recent();
	}
	
	public function index($id, $slug) {
		
		// Fetch the 404, if not found
		
		//$this->db->where('pubdate <=', date('Y-m-d'));
		
		$this->article_m->set_published();
		$this->data['article'] = $this->article_m->get($id);
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
		
		// Load view
		add_meta_title($this->data['article']->title);
		$this->data['subview'] = 'article';
		$this->load->view('_main_layout', $this->data);
	}
}