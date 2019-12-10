<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lotteries_m extends MY_Model
{
	protected $_table_name = 'lottery_profiles';
	protected $_order_by = 'id, order';
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
			'rules' => 'trim|required|xss_clean'
		), 
		'lotteryname' => array(
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

	public function get_new ()
	{
		$lottery = new stdClass();
		$lottery->id = NULL;
		$lottery->name = '';
		$lottery->description = '';
		$lottery->image = '';
		$lottery->country_id = '';
		$lottery->state_prov = '';
		$lottery->balls_drawn = 0;
		$lottery->minimum_ball = 0;
		$lottery->maximum_ball = 0;
		$lottery->extra_ball = 0; // 0 = False 1 = True
		$lottery->duplicate_extra = 0; // 0 = False 1 = True
		$lottery->extra_minimum_ball = 0;
		$lottery->extra_maximum_ball = 0;
		$lottery->days = array(
			'sunday' => 0,
			'monday' => 0,
			'tuesday' => 0,
			'wednesday' => 0,
			'thursday' => 0,
			'friday' => 0,
			'saturday' => 0
		);
		$lottery->last_draw_date = '';
		return $lottery;
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
		foreach ($pages as $page) {
		    if ((int) $id == (int) $page['menu_id']) 
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
			0 => 'No parent'
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
}