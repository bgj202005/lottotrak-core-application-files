<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page_m extends MY_Model
{
	protected $_table_name = 'pages';
	protected $_order_by = 'parent_id, order';
	public $rules = array(
		'parent_id' => array(
			'field' => 'parent_id', 
			'label' => 'Parent', 
			'rules' => 'trim|intval'
		),
        'menu_id' => array(
            'field' => 'menu_id',
            'label' => 'Menu Item Location',
            'rules' => 'trim|intval'
        ),
		'template' => array(
			'field' => 'template', 
			'label' => 'Template', 
			'rules' => 'required'
		), 
		'title' => array(
			'field' => 'title', 
			'label' => 'Title', 
			'rules' => 'trim|required|max_length[100]|xss_clean'
		), 
		'slug' => array(  
			'field' => 'slug', 
			'label' => 'Slug', 
			'rules' => 'trim|required|max_length[100]|url_title|callback__unique_slug|xss_clean'
		), 
		'body' => array(
			'field' => 'body', 
			'label' => 'Body', 
			'rules' => 'trim|required'
		)
	);

	public $new_member_rules = array(
	        'username' => array (
	                'field' => 'username',
	                'label' => 'Username',
	                'rules' => 'required|min_length[5]|max_length[15]',
	        ),
	        'email' => array (
	                'field' => 'email',
	                'label' => 'Email',
	                'rules' => 'trim|required|valid_email|xss_clean',
	        ),
	        'password' => array (
	                'field' => 'password',
	                'label' => 'Password',
	                'rules' => 'trim|required|min_length[6]|max_length[30]|matches[password_confirm]|xss_clean',
	        ),
	        'password_confirm' => array (
	                'field' => 'password_confirm',
	                'label' => 'Confirm password',
	                'rules' => 'trim|required|min_length[6]|max_length[30]|xss_clean',
	        ),
	);
	
	public function get_new ()
	{
		$page = new stdClass();
		$page->title = '';
		$page->slug = '';
		$page->body = '';
		$page->parent_id = 0;
		$page->menu_id = 0; // New Menu Location 0 = Header, 1 = Footer Menu (Inside), 2 = Footer Menu (Outside)
		$page->template = 'page';
		$page->position = 'full_page';
		return $page;
	}
/*	public function get_archive_link(){
		$page = parent::get_by(array('template' => 'newsarticle'), TRUE);
		return isset($page->slug) ? $page->slug : '';
	} */
	
	public function delete ($id)
	{
		// Delete a page
		parent::delete($id);
		
		// Reset parent ID for its children
		$this->db->set(array(
			'parent_id' => 0
		))->where('parent_id', $id)->update($this->_table_name);
	}
	
	public function save_order($pages)
	{
	     
	    if (count($pages)) {
			foreach ($pages as $order => $page) {
			    if ($page['item_id'] != '') {
					$data = array('parent_id' => (int) $page['parent_id'], 
					        'menu_id' => (int) $page['menu_id'], 'order' => $order);

					$this->db->set($data)->where($this->_primary_key, $page['item_id'])->update($this->_table_name);
			 }
		  }
	  }
	}
	
	public function get_nested ($id)
	{
		$this->db->order_by($this->_order_by);
		$pages = $this->db->get('pages')->result_array();
		$array = array();
		foreach ($pages as $page) 
		{
			if ((int) $id == (int) $page['menu_id']&&$page['menu_item']) 
			{ // The Menu Location Must Match
				  if (! $page['parent_id']) 
				  {
        				// This page has no parent
        				$array[$page['id']] = $page;
        		  }
				  else
				  {
        		  // This is a child page
        		  /* $array[$page['parent_id']]['children'][] = $page; */
        		   $this->process_children($page, $array);
				  }
    		    }
		    }

		return $array;
	}
	
	function process_children($item, &$arr)
	{
		if (is_array($arr))
		{
			foreach ($arr as $key => $parent_item)
			{
				// Match?
				if (isset($parent_item['id']) && $parent_item['id'] == $item['parent_id'])
				{
					$arr[$key]['children'][$item['id']] = $item;
				}
				else
				{
			// Keep looking, recursively
					$this->process_children($item, $arr[$key]);
				}
 			}	
		}
	}
	
	public function get_with_parent ($id = NULL, $single = FALSE)
	{
		$this->db->select('pages.*, p.slug as parent_slug, p.title as parent_title');
		$this->db->join('pages as p', 'pages.parent_id=p.id', 'left');
		return parent::get($id, $single);
	}
	
	public function get_no_parents ()
	{
		// Fetch pages without parents
		$this->db->select('id, title, menu_id');
		$this->db->where('parent_id', 0);  
		$pages = parent::get();
		// Return key => value pair array
		$array = array(
			0 => 'Top Level'
		);
		if (count($pages)) {
			foreach ($pages as $page) {
			    $array[$page->id] = $page->title;
			}
		}
		return $array;
	}
	
	public function get_article_link() {
		$page = parent::get_by(array('template' => 'newsarticle'), TRUE);
		return isset($page->slug) ? $page->slug : '';  
	}

	/**
	 * Takes the $fields array and transfers the field objects to the array
	 * @param	arr	$fields 	array field values from post
	 * @param	obj $data		called by reference object values from database (is returned from the database as object)
	 * @return 	none
	 * */
	public function object_from_page_post($fields, &$data)
	{
		$data->title  		= $fields['title'];
		$data->slug  		= $fields['slug'];
		$data->order  		= $fields['order'];
		$data->body  		= $fields['body'];
		$data->parent_id 	= $fields['parent_id'];
		$data->menu_id 		= $fields['menu_id'];	
		$data->template 	= $fields['template'];
	}
	/**
	 * Takes the $placement field (top_section, bottom_left and bottom_right)
	 * @param	arr	$placement 	array field values from post
	 * @return 	obj 			Row of homepage template from the current position on the homepage
	 * */
	public function home_pages($placement)
	{
		// Fetch pages without parents
		$this->db->select('id, title, slug, body, menu_id');
		$this->db->where('position', $placement);  
		return parent::get(NULL, TRUE);
	}

	/**
	 * Takes the $placement field (top, middle and bottom)
	 * @param	arr	$placement 	array field values from post
	 * @return 	obj 			Row of homepage template from the current position on the homepage
	 * */
	public function side_bar($placement)
	{
		// Fetch pages without parents
		$this->db->select('id, title, slug, body, menu_id');
		$this->db->where('position', $placement);  
		return parent::get(NULL, TRUE);
	}

}