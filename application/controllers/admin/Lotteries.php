<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lotteries extends Admin_Controller {
	
	public function __construct() {
		 parent::__construct();
		 $this->load->model('lotteries_m');
		 $this->load->helper('file');
	}

	/**
	 * Retrieves List of All Lotteries
	 * 
	 * @param       none
	 * @return      none
	 */
	public function index() {
		// Fetch all users from the database
		$this->data['lotteries'] = $this->lotteries_m->get();
		// Load the view
		$this->data['subview'] = 'admin/lotteries/index';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function edit($id = NULL) {
		
		// Setup for File Uploads
		$config['upload_path']          = base_url().'images/uploads/';
		$config['allowed_types']        = 'gif|jpg|png';
		$config['max_size']             = 100;
		$config['max_width']            = 1024;
		$config['max_height']           = 768;
		$this->load->library('upload', $config);

		// Fetch a lottery profile or create a new one
		if ($id) {
			$this->data['lottery'] = $this->lotteries_m->get($id);
			count($this->data['lottery']) || $this->data['errors'][] = 'Lottery Profile could not be found';
		} else {
			//load file helper
			$this->data['lottery'] = $this->lotteries_m->get_new();
		}
		
		$this->data['message'] = '';  // Create a Message object
		// Setup the form
		$rules = $this->lotteries_m->rules;
		$this->form_validation->set_rules($rules);
		
		if ($this->form_validation->run() == TRUE) {
			
			//if (empty($this->input->post('lottery_state_prov'))) $_POST['lottery_state_prov'] = $this->data['lottery']->lottery_state_prov;
			
			$_POST['extra_ball'] = (is_null($this->input->post('extra_ball')) ? 0 : 1);
			$_POST['duplicate_extra_ball'] = (is_null($this->input->post('duplicate_extra_ball')) ? 0 : 1);
			// We can save and redirect
			$data = $this->lotteries_m->array_from_post ( array (
					'lottery_name',
					'lottery_description',
					'balls_drawn',
					'lottery_image',
					'lottery_state_prov',
					'lottery_country_id',
					'minimum_ball',
					'maximum_ball',
					'extra_ball',
					'duplicate_extra_ball',
					'minimum_extra_ball',
					'maximum_extra_ball',
					'monday',
					'tuesday',
					'wednesday',
					'thursday',
					'friday',
					'saturday',
					'sunday',
			) );
			

			foreach ($data as $key => $value)
			{
				if(intval($value)) 
				{
					$data[$key] = intval($value);
				}
				if(is_null($value)||empty($value)&&$key!='lottery_state_prov') $data[$key] = 0;  // Revert from NULL to 0 only or FALSE (int 0)
			}
			
			var_dump($data);

			$this->data['lottery'] = $this->lotteries_m->array_to_object($this->data['lottery'], $data);
			$this->data['lottery']->id = $this->lotteries_m->save($data, $id);
			if (!$id) $this->lotteries_m->create_lottery_db($data);

			$this->data['message'] = (is_null($id) ? "The Lottery Profile has been added to the Database." : "The ".$this->data['lottery']->lottery_name." Profile has been updated.");
		}
		// Load the View
		if(!$this->data['lottery']->minimum_extra_ball) $this->data['lottery']->minimum_extra_ball = '';
		if(!$this->data['lottery']->maximum_extra_ball) $this->data['lottery']->maximum_ball = '';
		$this->data['subview']  = 'admin/lotteries/edit';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function delete($id) {
		
		$this->lotteries_m->delete($id);
		redirect('admin/lotteries');
		
	}

	/**
	 * Returns TRUE if Lottery Name is Unique and does not match the id, 
	 * otherwise FALSE for the lottery name already exists.
	 * @param       $str
	 * @return      TRUE/FALSE TRUE (if does not exist), FALSE (if the name selected currently exists)
	 */
	public function _unique_lotteryname($str)
	{
		// Do Not validate if Lottery Name already exists
		// Unless it's the Lottery Name for the current Lottery
		$id = $this->uri->segment(4);
		$lottery_name = $this->input->post('lottery_name');
		$this->db->where('lottery_name', $lottery_name);
		! $id || $this->db->where('id !=', $id);
		
		$lotteries = $this->lotteries_m->get();
			
			if (count($lotteries)) 
			{
				$this->form_validation->set_message('_unique_lotteryname', '%s already exists. Please type another Lottery Name');
				return FALSE;
			}
	return TRUE;
	}

	/**
	 * Returns the extra ball is set (TRUE) or not set (FALSE)
	 * 
	 * @param       none
	 * @return      TRUE/FALSE TRUE (if not set), FALSE (if set)
	 */
	public function _extra_ball_set($str) 
	{
		if (is_null($this->input->post('extra_ball'))&&empty($this->input->post('minimum_extra_ball'))&&empty($this->input->post('maximum_extra_ball')))
		{
			return TRUE;
		} 
		elseif (!is_null($this->input->post('extra_ball'))&&!empty($this->input->post('minimum_extra_ball'))&&!empty($this->input->post('maximum_extra_ball'))) 
		{
			return TRUE;
		}
	$this->form_validation->set_message('_extra_ball_set', 'The Extra Ball must be set to adjust the minimum and maximum extra ball values.');
	return FALSE;
	}

	/**
	 * Returns TRUE if at least one day in the week is set, or 
	 * FALSE is all the days are not set
	 * 
	 * @param       $str (monday, tuesday, wednesday, thursday, friday, saturday, sunday) depending on which checkboxes are set.
	 * @return      TRUE/FALSE TRUE (if at least one day is set), FALSE (if no days are set, we need at least one draw day)
	 */
	public function _require_day_of_week_set($str) 
	{
		$get_user_posts = $this->input->post(NULL, FALSE); 
		if (is_array($get_user_posts)) 
		{
			foreach($get_user_posts as $key => $value) 
			{
				if ($key=='monday'&&$value=='1') return TRUE;
				if ($key=='tuesday'&&$value=='1') return TRUE;
				if ($key=='wednesday'&&$value=='1') return TRUE;
				if ($key=='thursday'&&$value=='1') return TRUE;
				if ($key=='friday'&&$value=='1') return TRUE;
				if ($key=='saturday'&&$value=='1') return TRUE;
				if ($key=='sunday'&&$value=='1') return TRUE;
			}	
		}
		
	$this->form_validation->set_message('_require_day_of_week_set', 'There is no days set for the draw.<br />Select at least one draw day.');
	return FALSE;	
	}
	
	/**
	 * file value and type check during validation
	 * 
	 * 
	 * @param       $str
	 * @return      TRUE/FALSE 
	 */
	public function _file_check($str){
        $allowed_mime_type_arr = array('image/gif','image/jpeg','image/pjpeg','image/png','image/x-png');
        $mime = get_mime_by_extension($_FILES['lottery_image']['name']);
        if(isset($_FILES['lottery_image']['name']) && $_FILES['lottery_image']['name']!=""){
            if(in_array($mime, $allowed_mime_type_arr)){
                return TRUE;
            }else{
                $this->form_validation->set_message('_file_check', 'Please select only pdf/gif/jpg/png file.');
                return FALSE;
            }
        }else{
            $this->form_validation->set_message('_file_check', 'Please choose a file to upload.');
            return TRUE;
        }
	}
	
}