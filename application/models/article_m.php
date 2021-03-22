<?php
class Article_m extends MY_Model
{
	protected $_table_name = 'articles';
	protected $_order_by = 'pubdate desc, id desc';
	protected $_timestamps = TRUE;
	public $rules = array(
		'pubdate' => array(
			'field' => 'pubdate', 
			'label' => 'Publication date', 
			'rules' => 'trim|required|exact_length[10]|xss_clean'
		), 
		'title' => array(
			'field' => 'title', 
			'label' => 'Title', 
			'rules' => 'trim|required|max_length[100]|xss_clean'
		), 
		'slug' => array(
			'field' => 'slug', 
			'label' => 'Slug', 
			'rules' => 'trim|required|max_length[100]|url_title|xss_clean'
		), 
		'body' => array(
			'field' => 'body', 
			'label' => 'Body', 
			'rules' => 'trim|required'
		)
	);

	public function get_new ()
	{
		$article = new stdClass();
		$article->title = '';
		$article->slug = '';
		$article->body = '';
		$article->pubdate = date('Y-m-d');
		return $article;
	}
	
	public function delete ($id)
	{
		// Delete a page
		parent::delete($id);
	}
	
	public function get_recent($limit = 3) {
		$limit = (int) $limit;
		$this->set_published();
		$this->db->limit($limit);
		return parent::get();
	}
	
	public function set_published() {
		
		$this->db->where('pubdate <=', date('Y-m-d'));
	}

	/**
	 * Takes the $fields array and transfers the field objects to the array
	 * @param	arr	$fields 	array field values from post
	 * @param	obj $data		called by reference object values from database (is returned from the database as object)
	 * @return 	none
	 * */
	public function object_from_article_post($fields, &$data)
	{
		$data->title  		= $fields['title'];
		$data->slug  		= $fields['slug'];
		$data->body  		= $fields['body'];
		$data->pubdate	 	= $fields['pubdate'];
	}
}