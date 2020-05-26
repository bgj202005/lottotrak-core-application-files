<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lotteries extends Admin_Controller {
	
	public function __construct() {
		 parent::__construct();
		 $this->load->model('lotteries_m');
		 $this->load->helper('file');
		 $this->load->library('image_lib');
		 $this->load->library('CSV_Import');
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
		$this->data['current'] = $this->uri->segment(2); // Sets the lotteries menu
		$this->data['subview'] = 'admin/lotteries/index';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	public function edit($id = NULL) {
		

		// Fetch a lottery profile or create a new one
		if ($id) {
			$this->data['lottery'] = $this->lotteries_m->get($id);
			is_array($this->data['lottery']) || $this->data['errors'][] = 'Lottery Profile could not be found';
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
		$this->data['current'] = $this->uri->segment(2); // Sets the Page Menu
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
		// Retrieve the lottery table name for the database
		$this->data['lottery']->table_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		// Check for existing lottery draws
		$this->data['lottery']->last_draw =	$this->lotteries_m->last_draw_db($this->data['lottery']->lottery_name);
		
		$this->data['message'] = '';  // Create a Message object
		$error = NULL;				  // Related to Image upload only
		
		if(!empty($this->input->post('hidden_field')))
		{
			if(empty($this->input->post('import_lottery_url'))) {
				$error = '';
				$total_data = '';
				/* session_start(); */
				$allowed_extension = array('csv');
				$file_array = explode(".", $_FILES["lottery_upload_csv"]["name"]);
				$extension = end($file_array);

				if($_FILES['lottery_upload_csv']['name'] != '') {
					if(in_array($extension, $allowed_extension))
					{
						$new_file_name = rand(). '.' . $extension;
						$this->session->set_userdata(array('new_file_name' => $new_file_name));
						move_uploaded_file($_FILES['lottery_upload_csv']['tmp_name'], 'lotto_zip_csv_uploads/'.$new_file_name);
						$file_content = file('lotto_zip_csv_uploads/'.$new_file_name, FILE_SKIP_EMPTY_LINES);
						$total_data = count($file_content);
					}
					else
					{
						$error = 'Only CSV File Format is allowed';
					}
				}
				else
				{
					$error = 'Please Select File';
				}
				if($error !='')
				{
					$output = array(
						'error'		=> $error
					);
				}
				else
				{
					$output = array(
						'success' => TRUE,
						'total_data'	=>	($total_data - 1)
					);
				}
			} 
			else 
			{
				// Do something with URL
				$output = array(
					'error' => 'The URL has been entered'
				);
			}
			//$this->import_process($id);
			echo json_encode($output);
		}

		else {
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
		  /* $config['upload_path'] = 'lotto_zip_cvs_uploads/';
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
		  } */
 	  	$this->data['current'] = $this->uri->segment(2); // Sets the Page Menu
		$this->data['subview']  = 'admin/lotteries/import';
		$this->load->view('admin/_layout_main', $this->data);
		}
	}

	public function import_process($id)
	{
		$this->data['lottery'] = $this->lotteries_m->get($id);
		// Retrieve the lottery table name for the database
		$table = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		header('Content-type: text/html; charset=utf-8');
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");

		set_time_limit(0);

		ob_implicit_flush(1);
		
		if (!empty($this->session->userdata('new_file_name')))
		{
			$file_data = fopen('lotto_zip_csv_uploads/'.$this->session->userdata('new_file_name'), 'r');
			$header = TRUE;		// Begin with header
			fgetcsv($file_data); // Set the File Pointer to start of file and move passed the header
			while($row = fgetcsv($file_data)) 
			{
				$n = count($_POST['cvs_field']);
					if ($n>1)		//There are currently fields that we can't import into the database
					{
						for($i=0; $i<$n; $i++)
						{
							if(trim($_POST['cvs_field'][$i])!='')
							{
								$elim[$row] = ((strtolower($row))==(strtolower(trim($_POST['cvs_field'][$i]))) ? TRUE : FALSE);
							}
						}
					} 

				$draw_data = array(
					'PRODUCT'		=> 	$row[0],
					'DRAW NUMBER'	=>	$row[1],
					'SEQUENCE'		=>	$row[2],
					'draw_date'		=>	$row[3],
				);

				$draw_data['draw_date'] = date("Y/m/d", strtotime($draw_data['draw_date']));
				$balls_drawn = intval($this->data['lottery']->balls_drawn);		// balls drawn

				$c = 4; // array counter
				$b = 1;	// pick 3, pick 4, pick 5, pick 6, pick 7, pick 8, pick 9 // balls drawn starting at pick 4
				for ($balls_drawn; $balls_drawn>0; $balls_drawn--) 
				{
					$draw_data['ball'.$b] =  $row[$c];
					$b++;	// Increment ball count
					$c++;	// Increment row count
				}
				
				if (!empty($this->data['lottery']->extra_ball)) $draw_data['extra'] = $row[$c];

				$draw_data += ['lottery_id'	=> $id];
				
				
				unset($draw_data['PRODUCT']);
				unset($draw_data['DRAW NUMBER']);
				unset($draw_data['SEQUENCE']);

				// Check the draw date to make sure it is in the correct format for Month / Day / Year
				$date = explode('/', $draw_data['draw_date']);
				
				if (intval($date[1])<1||(intval($date[1]>12))) // Month between 1 and 12
				{
					$draw_data = array(
						'month_error'	=>	TRUE,
						'draw_date'		=>	$draw_data['draw_date']
					);
					break;
				} 
				elseif (intval($date[2])<1||(intval($date[2])>cal_days_in_month(CAL_GREGORIAN, $date[1], $date[0])))
				{
					$draw_data = array(
						'day_error'	=>	TRUE,
						'draw_date'	=>	$draw_data['draw_date']
					);
					break;
				} 
				elseif (intval($date[0]<1)||intval($date[0])>intval(date("Y")))
				{
					$draw_data = array(
						'year_error'	=>	TRUE,
						'draw_date'		=>	$draw_data['draw_date']
					);
					break;
				}

				if (!$this->lotteries_m->range_check($this->data['lottery'], $draw_data)) 
				{
					$draw_data = array(
						'range_error'	=>	TRUE,
						'draw_date'		=>	$draw_data['draw_date']
					);
					break;
				}

				if (!$this->lotteries_m->csv_array_to_query($table, $draw_data)) 
				{
					$draw_data = array(
						'error'	=>	TRUE);
					break;
				}
				else
				{
					$draw_data += ['success' => TRUE];
				}

				sleep(1);

				if(ob_get_level() > 0)
				{
					ob_end_flush();
				}
				
			echo json_encode($draw_data);
				
			}
			fclose($file_data);		// Close out File data stream
		echo json_encode($draw_data);
		}
		$this->session->unset_userdata('new_file_name');
	}

	/**
	 * Determines the current row count of the csv to data
	 *  being imported in the database
	 * @param       $table	$current lottery table name		
	 * @return      none
	 */
	public function process($table)
	{
		$row_count = $this->lotteries_m->db_row_count($table);
	echo $row_count;
	}

	/**
	 * Views all draws with pagination, filtering and Draw Search Options
	 *  being imported in the database
	 * @param       $id		current id of draws		
	 * @return      none
	 */
	public function view_draws($id)
	{
		$this->data['lottery'] = $this->lotteries_m->get($id);
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		// Check for existing lottery draws
		$this->data['message'] = '';  // Create a Message object
		$this->data['request'] = '';  // only view draws		
		
		$this->data['draws'] = $this->lotteries_m->load_draws($tbl_name, $id);
		
		if (!$this->data['draws'])
		{
			$this->data['message'] = 'There are no draws associated with this lottery. Please import draws.'; 
		}
		else	// Yes, Draws are available. next we need to find the next draw date
		{
			$c = count($this->data['draws']);						// Determine total count of array of objects
			$ld = $this->data['draws'][$c-1]->draw_date;			// Return last draw date
			$day = $this->return_day($ld);							// Returns the day of draw, Saturday, Sunday, etc.
			$this->data['lottery']->next_draw_date = $this->lotteries_m->next_date($this->data['lottery'], $day, $ld);
			$this->data['lottery']->num = strval(++$c);
		}
		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->data['subview']  = 'admin/lotteries/view';
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
	
	/**
	 * Add Draw input boxes to the latest draw
	 * 
	 * @param       $id			Lottery id
	 * @return      none 
	 */
	public function draw_add($id)
	{
		$this->data['lottery'] = $this->lotteries_m->get($id);
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		// Check for existing lottery draws
		$this->data['message'] = '';  // Create a Message object
		
		if (!empty($this->input->post('add')))	// Save to Database
		{
			$draw = $this->lotteries_m->array_from_post ( array (
				'ball1',
				'ball2',
				'ball3'
			) );

			$edit_rules = array(
				'ball1' => array(
					'field' => 'ball1', 
					'label' => 'Ball 1', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				), 
				'ball2' => array(
					'field' => 'ball2', 
					'label' => 'Ball 2', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				), 
				'ball3' => array(  
					'field' => 'ball3', 
					'label' => 'Ball 3', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				)
			);
			if (intval($this->data['lottery']->balls_drawn)>=4) {
				$draw['ball4'] = $this->input->post('ball4');
				$edit_rules['ball4'] = array(
					'field' => 'ball4', 
					'label' => 'Ball 4', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				); 
			}
			if (intval($this->data['lottery']->balls_drawn)>=5) {
				$draw['ball5'] = $this->input->post('ball5');
				$edit_rules['ball5'] = array(
					'field' => 'ball5', 
					'label' => 'Ball 5', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if (intval($this->data['lottery']->balls_drawn)>=6) 
			{
				$draw['ball6'] = $this->input->post('ball6');
				$edit_rules['ball6'] = array(
					'field' => 'ball6', 
					'label' => 'Ball 6', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if (intval($this->data['lottery']->balls_drawn)>=7) 
			{
				$draw['ball7'] = $this->input->post('ball7');
				$edit_rules['ball7'] = array(
					'field' => 'ball7', 
					'label' => 'Ball 7', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if (intval($this->data['lottery']->balls_drawn)>=8) 
			{
				$draw['ball8'] = $this->input->post('ball8');
				$edit_rules['ball8'] = array(
					'field' => 'ball8', 
					'label' => 'Ball 8', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if (intval($this->data['lottery']->balls_drawn)>=9) 
			{
				$draw['ball9'] = $this->input->post('ball9');
				$edit_rules['ball9'] = array(
					'field' => 'ball9', 
					'label' => 'Ball 9', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if ($this->data['lottery']->extra_ball) 
			{
				$draw['extra'] = $this->input->post('extra_ball');
				$edit_rules['extra'] = array(
					'field' => 'extra_ball', 
					'label' => 'Extra Ball', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_extra_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_extra_ball).']'
				);
			}
			$draw['draw_date'] = date("Y-m-d", strtotime($this->input->post('next_date')));	// format the date without the day of week
			$draw['lottery_id'] = $id;	// Foreign Key to Lottery Profile

			$this->form_validation->set_rules($edit_rules);
			
			if ($this->form_validation->run() == TRUE) {

				$next_id = $this->lotteries_m->insert_draw($tbl_name, $draw); 
				if ($next_id)
				{
					$this->data['message'] = "Draw has been added to the database. Last Draw Date:".date("l M d, Y", strtotime($draw['draw_date']));  // Successfully added draw message	
				} 
				else
				{
					$this->data['message'] = "The draw could not be added to the database. Check the numbers and Save the draw again.";  // Error message	
				}
			}
			else 
			{
				$this->data['add']	= 'add';
				$this->data['message'] = "Number Range Error. Check the numbers and Save the draw again.";  // Error message
			}
		}
		else
		{
			$this->data['add']	= 'add';
		}
			$this->data['draws'] = $this->lotteries_m->load_draws($tbl_name, $id);
			$c = count($this->data['draws']);					// Determine total count of array of objects
			$ld = $this->data['draws'][$c-1]->draw_date;		// Return last draw date
			$day = $this->return_day($ld);						// Returns the day of draw, Saturdday, Sunday, etc.

			$this->data['lottery']->next_draw_date = $this->lotteries_m->next_date($this->data['lottery'], $day, $ld);
			$this->data['lottery']->num = strval(++$c);

		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->data['subview']  = 'admin/lotteries/view';
		$this->load->view('admin/_layout_main', $this->data);
	}

	/**
	 * Edit Draw Input Boxes and Save them to new box
	 * 
	 * 
	 * @param       int $id		id of Lottery Profile
	 * @return      none 
	 */
	public function draw_edit($id)
	{
		$this->data['lottery'] = $this->lotteries_m->get($id);
		// Retrieve the lottery table name for the database
		$tbl_name = $this->lotteries_m->lotto_table_convert($this->data['lottery']->lottery_name);
		// Check for existing lottery draws
		$this->data['message'] = '';  // Create a Message object

		if (!empty($this->input->post('edit')))	// Save to Database
		{
			$draw = $this->lotteries_m->array_from_post ( array (
				'draw[]',
				'ball_1[]',
				'ball_2[]',
				'ball_3[]'
			) );

			$edit_rules = array(
				'ball1' => array(
					'field' => 'ball_1[]', 
					'label' => 'Ball 1', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				), 
				'ball2' => array(
					'field' => 'ball_2[]', 
					'label' => 'Ball 2', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				), 
				'ball3' => array(  
					'field' => 'ball_3[]', 
					'label' => 'Ball 3', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				)
			);
			if (intval($this->data['lottery']->balls_drawn)>=4) {
				$draw['ball_4[]'] = $this->input->post('ball_4[]');
				$edit_rules['ball_4[]'] = array(
					'field' => 'ball_4[]', 
					'label' => 'Ball 4', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				); 
			}
			if (intval($this->data['lottery']->balls_drawn)>=5) {
				$draw['ball_5[]'] = $this->input->post('ball_5[]');
				$edit_rules['ball_5[]'] = array(
					'field' => 'ball_5[]', 
					'label' => 'Ball 5', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if (intval($this->data['lottery']->balls_drawn)>=6) 
			{
				$draw['ball_6[]'] = $this->input->post('ball_6[]');
				$edit_rules['ball_6[]'] = array(
					'field' => 'ball_6[]', 
					'label' => 'Ball 6', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if (intval($this->data['lottery']->balls_drawn)>=7) 
			{
				$draw['ball_7[]'] = $this->input->post('ball7[]');
				$edit_rules['ball_7[]'] = array(
					'field' => 'ball_7[]', 
					'label' => 'Ball 7', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if (intval($this->data['lottery']->balls_drawn)>=8) 
			{
				$draw['ball_8[]'] = $this->input->post('ball_8[]');
				$edit_rules['ball_8[]'] = array(
					'field' => 'ball_8[]', 
					'label' => 'Ball 8', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if (intval($this->data['lottery']->balls_drawn)>=9) 
			{
				$draw['ball_9[]'] = $this->input->post('ball_9[]');
				$edit_rules['ball_9[]'] = array(
					'field' => 'ball_9[]', 
					'label' => 'Ball 9', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_ball).']'
				);
			}
			if ($this->data['lottery']->extra_ball) 
			{
				$draw['extra[]'] = $this->input->post('extra_ball');
				$edit_rules['extra'] = array(
					'field' => 'extra_ball[]', 
					'label' => 'Extra Ball', 
					'rules' => 'required|greater_than_equal_to['.intval($this->data["lottery"]->minimum_extra_ball).']|numeric|integer|less_than_equal_to['.intval($this->data["lottery"]->maximum_extra_ball).']'
				);
			}
			$draw['lottery_id'] = $id;	// Foreign Key to Lottery Profile
			print_r($draw['extra[]']);
			$this->form_validation->set_rules($edit_rules);
			
			if ($this->form_validation->run() == TRUE) {
				// Draw Updates need to be passed like this ->
				// 0 => id, ball1, ball2, ball3, ball4, ball5, ball6, extra, lottery_id
				// 1 => id, ball1, ball2, ball3, ball4, ball5, ball6, extra, lottery_id

				$result = $this->lotteries_m->update_from_post($draw, $tbl_name, $this->data['lottery']->balls_drawn, $this->data['lottery']->extra_ball);

				if ($result)
				{
					$this->data['message'] = "The Draw(s) have been updated in the database.";  // Successfully added draw message	
				} 
				else
				{
					$this->data['message'] = "The draw(s) could not be updated in the database. Check the numbers and Save the draw(s) again.";  // Error message	
				}
			}
			else 
			{
				$this->data['edit']	= 'edit';
				$this->data['message'] = "Number Range Error. Check the numbers and Save the draw(s) again.";  // Error message
			}
		}
		else
		{
			if (empty($this->input->post('draw'))) $this->data['message'] = "Please Select the Draw Number(s) and click Manually Edit Draw(s) below.";  // Error message
			else $this->data['edit']	= 'edit';
		}
		
		$this->data['draws'] = $this->lotteries_m->load_draws($tbl_name, $id);
		$this->data['selected'] = $this->input->post('draw');	// Return the posted array
		$c = count($this->data['draws']);						// Determine total count of array of objects
		$ld = $this->data['draws'][$c-1]->draw_date;			// Return last draw date
		$day = $this->return_day($ld);							// Returns the day of draw, Saturdday, Sunday, etc.
		$this->data['lottery']->next_draw_date = $this->lotteries_m->next_date($this->data['lottery'], $day, $ld);
		$this->data['lottery']->num = strval(++$c);

		$this->data['current'] = $this->uri->segment(2); // Sets the Admins Menu Highlighted
		$this->data['subview']  = 'admin/lotteries/view';
		$this->load->view('admin/_layout_main', $this->data);
	}
	
	/**
	 * Returns the extra ball is set (TRUE) or not set (FALSE)
	 *
	 * @param		str $str 		Lottery value to compare with $lottery profile
	 * @param       arr $lottery	Lottery Profile Information
	 * @return      TRUE/FALSE 		TRUE (if not set), FALSE (if set)
	 */
	public function _duplicate_check($str, $lottery) 
	{
	return (intval($str)>$lottery['maximum_ball'] ? FALSE : TRUE);
	}
	
	/**
	 * Add Draw input boxes to the latest draw
	 * 
	 * @param	str	$last		Data Object			
	 * @return	str				Day of Draw 
	 */
	private function return_day($last)
	{
		$unixTimestamp = strtotime($last);  	// Convert the date string into a unix timestamp.
		return strtolower(date("l", $unixTimestamp));	
	}

}