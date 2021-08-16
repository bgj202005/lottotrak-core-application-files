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
		$page->raw = NULL;
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
		$data->raw			= $fields['raw'];
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
		$this->db->select('id, title, slug, body, raw, menu_id');
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
		$this->db->select('id, title, slug, body, raw, menu_id');
		$this->db->where('position', $placement);  
		return parent::get(NULL, TRUE);
	}
	
	/**
	 * Takes the $placement field (top_section, bottom_left and bottom_right)
	 * @param	none
	 * @return 	none
	 **/
	public function users_record()
	{
	// ###############  SET UP THE VARIABLES  ########################################

		// FOLDER USED TO STORE TEMPORAL FILES
		//    IMPORTANT: the folder must have proper permissions to allow writing files
		//    The name of the temporal files contains the IP address of the user
		//    ($_SERVER["DOCUMENT_ROOT"] is the root folder for the website)
		$folder = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? FCPATH.'visitor_ips'.DIRECTORY_SEPARATOR: $_SERVER["DOCUMENT_ROOT"]."/visitor_ips/"); 


		// ###############  THE WORKING PART OF THE SCRIPT ##############################

		// GET IP ADDRESS OF USER (a function in the bottom is used)
		$ip=$this->getIP();

		// REGISTER THE USER
		//      A file will be created. The name of the file will contain the IP of the user.
		//      In case the file already exists, it will be overwritted.
		//      The creation time of the file will indicate how long ago the user
		//      with this IP visited a page containing this active users counter
				// OPTION 1, for PHP4 or superior
				$cf = fopen($folder.$ip, "w");
				fwrite($cf, "");
				fclose($cf);
				// OPTION 2, for PHP5 or superior
				// file_put_contents ($folder."$ip.txt", "0");
	}
	/**
	 * funtion getIP will be used to get IP address of visitor for the frontend
	 * @param	none
	 * @return 	none
	 **/
	public function getIP() 
	{
	// Option 1 to get the IP address of visitor
	//      if a value for $_SERVER['HTTP_X_FORWARDED_FOR'] is available
	//      $ip is obtained and returned
	$ip = $this->input->server('HTTP_X_FORWARDED_FOR');
	if(isset($ip))
	{
		if($ip=='::1') $ip = "127.0.1.1"; // Localhost
		return $ip;
	}
	// Option 2 to get the IP address of visitor
	//      if a value for $_SERVER['REMOTE_ADDR'] is available
	//      $ip is obtained and returned
	$ip = $this->input->server('REMOTE_ADDR');
	if(isset($ip))
	{
		if($ip=='::1') $ip = "127.0.1.1"; // Localhost
		return $ip;
	}
	// IP has not been obtained, so a default IP is returned
	//      The default value will be used very few times, so
	return "0.0.0.0";
	}
	/**
	 * COUNT NUMBER OF ACTIVE USERS
	 * @param	none
	 * @return 	none
	 **/
	public function active_users()
	{
		// ###############  SET UP THE VARIABLES  ########################################
		// FOLDER USED TO STORE TEMPORAL FILES
		//    IMPORTANT: the folder must have proper permissions to allow writing files
		//    The name of the temporal files contains the IP address of the user
		//    ($_SERVER["DOCUMENT_ROOT"] is the root folder for the website)
		$folder = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? FCPATH.'visitor_ips'.DIRECTORY_SEPARATOR: $_SERVER["DOCUMENT_ROOT"]."/visitor_ips/");     
		
		// TIME SINCE LAST ACTIVITY OF AN USERS TO BE CONSIDERED NON-ACTIVE
		$timeold=300;   // seconds or 5 minutes0

		// ###############  THE WORKING PART OF THE SCRIPT ##############################

		// GET ACTUAL TIME
		$actualtime=date("U");   // seconds since January 1st, 1970.

		// GET IP ADDRESS OF USER (a function in the bottom is used)
		$ip=$this->getIP();
		$int_ip = sprintf("%u", ip2long($ip)); // Convert to the integer equivalent
		$simple_sql = $this->db->simple_query("SELECT * FROM `members` WHERE logged_in = '1' AND ip_address = '.$int_ip.'");
		if(!$simple_sql->num_rows)
		{
			// REGISTER THE USER
			//      A file will be created. The name of the file will contain the IP of the user.
			//      In case the file already exists, it will be overwritted.
			//      The creation time of the file will indicate how long ago the user
			//              with this IP visited a page containing this active users counter
					// OPTION 1, for PHP4 or superior
				$cf = fopen($folder.$ip, "w");
				fwrite($cf, "");
				fclose($cf);
					// OPTION 2, for PHP5 or superior
					// file_put_contents ($folder."$ip.txt", "0");

				// COUNT NUMBER OF ACTIVE USERS
				//      All files within folder $folder will be checked
				//      Files $timeold seconds old (defined above) will be deleted
				//      Files created up to $timeold seconds ago will be accounted as active users

				// a counter; no users at this moment
				$counter=0;

				// get the list of files within $folder
				$dir = dir($folder);
				// check all files one by one (variable $temp will be the name of each file)
				while($temp = $dir->read())
				{
					// the ones bellow are not files, so continue to next $temp
					if ($temp=="." or $temp==".."){continue;}
					// For real files, get the last modification time
					//   (number of seconds since January 1st, 1970)
					//    and save the data to variable $filecreatedtime
					$filecreatedtime=date("U", filemtime($folder.$temp));
					// check whether the file is $timeold seconds old
					if ($actualtime>($filecreatedtime+$timeold))
					{
						// the file IS old, so delete it
						unlink ($folder.$temp);
					}
					else
					{
						// the file IS NOT old, so an active user will be accounted
						$counter++;
					}
				}
				$this->db->set('active_count', $counter, FALSE); // Update Database Counter
				$this->db->update('visitors');
		}
	}
}