<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lotteries extends Admin_Controller {
	
	public function __construct() {
		 parent::__construct();
		 $this->load->model('lotteries_m');
		 $this->load->helper('file');
		 
		 $this->load->library('image_lib');
	}

	/**
	 * Retrieves List of All Lotteries
	 * 
	 * @param       none
	 * @return      none
	 */
	public function index() {
		// Fetch all lotteries from the database
		$this->data['lotteries'] = $this->lotteries_m->get();
		// Load the view
		$this->data['subview'] = 'admin/lotteries/index';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function edit($id = NULL) {
		

		// Fetch a lottery profile or create a new one
		if ($id) {
			$this->data['lottery'] = $this->lotteries_m->get($id);
			count($this->data['lottery']) || $this->data['errors'][] = 'Lottery Profile could not be found';
		} else {
			//load file helper
			$this->data['lottery'] = $this->lotteries_m->get_new();
		}
		

		$this->data['message'] = '';  // Create a Message object
		$error = NULL;				  // Related to Image upload only
		// Setup the form

		if (isset($_FILES['lottery_image'])) //File being uploaded
		{
			// Setup for File Uploads
			$config['upload_path']          = 'images/uploads/'; 
			$config['allowed_types']        = 'gif|jpg|png|jpeg';
			$config['max_size']             = '0'; /* 10000; */
			$config['max_width']            = '0'; /* 2048; */
			$config['max_height']           = '0'; /* 1536; */
			$config['overwrite'] 	        = TRUE;
			$this->load->library('upload', $config); 
			/* $this->load->initialize($config); */

			$image_field_name = 'lottery_image';
			if ($_FILES['lottery_image']['error']!=4)  // Did not select a file to upload.  Indicates the file browse was not selected, 
			// so no image required to upload
			{ 
				if (!$this->upload->do_upload($image_field_name))
				{
					$error = array('error' => $this->upload->display_errors());
				}
				else
				{
					$image_data = $this->upload->data();
					$config['image_library'] = 'gd2';
					$config['source_image'] = 'images/uploads/'.$image_data["raw_name"].$image_data['file_ext'];
					$config['new_image'] = 'images/uploads/'.$image_data["raw_name"].$image_data['file_ext'];
					$config['create_thumb'] = FALSE;
					$config['maintain_ratio'] = TRUE;
					$config['width']         = 175;
					$config['height']       = 175;

					$this->image_lib->initialize($config);  // Change the dimentions keeping the proportions
					$this->image_lib->resize();
				}
			} 
		}

		$rules = $this->lotteries_m->rules;
		$this->form_validation->set_rules($rules);
		
		if ($this->form_validation->run() == TRUE&&is_null($error)) {
			
			//if (empty($this->input->post('lottery_state_prov'))) $_POST['lottery_state_prov'] = $this->data['lottery']->lottery_state_prov;
			
			$_POST['extra_ball'] = (is_null($this->input->post('extra_ball')) ? 0 : 1);
			$_POST['duplicate_extra_ball'] = (is_null($this->input->post('duplicate_extra_ball')) ? 0 : 1);
			$_POST['lottery_image'] = (is_null($_FILES['lottery_image']) ? '': $_FILES['lottery_image']['name']); 
			// We can save and redirect
			$data = $this->lotteries_m->array_from_post ( array (
					'lottery_name',
					'lottery_description',
					'balls_drawn',
					'lottery_state_prov',
					'lottery_country_id',
					'lottery_image',
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

			$data['lottery_image'] = (empty($data['lottery_image']) ? $_POST['image']: $data['lottery_image']);  // Only if not updating the image
			foreach ($data as $key => $value)
			{
				if(intval($value)) 
				{
					$data[$key] = intval($value);
				}
				if(is_null($value)||empty($value)&&($key!='lottery_state_prov'&&$key!='lottery_image')) $data[$key] = 0;  // Revert from NULL to 0 only or FALSE (int 0)
			}


			$this->data['lottery'] = $this->lotteries_m->array_to_object($this->data['lottery'], $data);
			$this->data['lottery']->id = $this->lotteries_m->save($data, $id);
			if (!$id) $this->lotteries_m->create_lottery_db($data);

			$this->data['message'] = (is_null($id) ? "The Lottery Profile has been added to the Database." : "The ".$this->data['lottery']->lottery_name." Profile has been updated.");
		}
		else 
		{
			$this->data['message'] = $error['error'];  // Only Errors associated with Uploading an image.
		}
		// Load the View
		if(!$this->data['lottery']->minimum_extra_ball) $this->data['lottery']->minimum_extra_ball = '';
		if(!$this->data['lottery']->maximum_extra_ball) $this->data['lottery']->maximum_ball = '';
		if ($id) $this->data['lastdraw'] = $this->lotteries_m->last_draw_db($this->data['lottery']->lottery_name);
		$this->data['subview']  = 'admin/lotteries/edit';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	/**
	 * Retrieves Lottery to import
	 * 
	 * @param       $id, Lottery id
	 * @return      none
	 */

	public function import($id)
	{
		$this->data['lottery'] = $this->lotteries_m->get($id);

		/* 	1. Check if the File has been selected (Uploading is first examined)
				
			If selected, start the Upload process

			2. if not selected, Check if the url textbox of the location of the file has been entered
			if the textbox is not empty, 
				check for a valid url (http: or https:)
				if valid, copy file to server at uploaded location cvs_zip_upload
					If Filename is valid zip file
						unzip in directory, uncompress cvs file
						delete zip file
						open cvs file
						Retrieve header titles
						Find Date title,
						Find Ball 1 ... Ball N title
						Find Bonus / Extra Ball
				Do 
				 Array Lottery draw, Date
					 Ball 1 .. Ball N
					 Extra / Bonus Ball
					Save draw in database

				until last draw and not errors
				Success Message, "The 'last draw date' has been imported successfully to the Lottery_name Database."

				else 
					not valid url, error "your url is not valid, check the url and type in again. Don't forget, http or https"

			
			else (filename not selected and url text field blank)
				Error Message, 'Enter a valid file or url.


		*/
		/* echo '<pre>';
          print_r($_FILES);
          exit; */
		  $config['upload_path'] = 'lotto_zip_cvs_uploads/';
		  $config['allowed_types'] = 'text/plain|text/anytext|csv|text/x-comma-separated-values|text/comma-separated-values|application/octet-stream|application/vnd.ms-excel|application/x-csv|text/x-csv|text/csv|application/csv|application/excel|application/vnd.msexcel';
		    
		  $this->load->library('CSV_Import', $config);
		  // If upload failed, display error
		  if (!$this->upload->do_upload()) {
  
			  echo $this->upload->display_errors();
		  
			} else {
			  $this->load->library('CSV_Import');
			  $file_data = $this->upload->data();
			  $file_path = base_url().'lotto_zip_cvs_uploads/' . $file_data['file_name'];
			  
  
			  if ($this->csvimport->get_array($file_path)) {
				  $csv_array = $this->csvimport->get_array($file_path);
				  
				  foreach ($csv_array as $row) {
					  $insert_data = array(
						  'ROLL_NO' => $row['ROLL_NO'],
						  'MARKS' => $row['MARKS'],
					  );
					  // insert data into database
					  $this->utilities->insertData($insert_data, 'admission_test_result');
				  }
			  }
		  }
		
		$this->data['message'] = '';  // Create a Message object
		$error = NULL;				  // Related to Image upload only
		$this->data['subview']  = 'admin/lotteries/import';
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