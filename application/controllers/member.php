<?php
class Member extends Frontend_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('member_m');
    }
    
    function register() {
        if(!empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") {

            $errors = array();     //array that will contain the errors if any
            $success = false;      //whether the ajax post and user creation are successful. Initial assumption is false
            /*
             * Unserialize the form data via parse_str() function
             */
            $formData = array();
            parse_str($_POST["formData"], $formData);
            if (isset($this->session->userdata("token")) && ($this->session->userdata("token") === $formData["_token"])  //if tokens match
            {
                $new_data_member = array (
                    'username' => $this->input->post('username'),
                    'email' => $this->input->post('email'),
                    'password' => $this->input->post('password')
                );
                
                /*
                 * Checking if posted fields are empty string (just in case) - e.g. user typing only whitespaces instead of actual name, email, username, password
                 */
                if(trim($formData["password"]) != trim($formData["confirm_password"]))
                {
                    $errors[] = "Password and the Confirmed Password don't Match.";
                }
                // Secondary Validation Rules
			    $new_member_rules = $this->member_m->new_member_rules;
			    $this->form_validation->set_rules($new_member_rules);
			    if ($this->form_validation->run()  == TRUE) {
                     echo "Success";
                     // ToDo   
                } else {
                    $errors[] = $this->form_validation->error_array();
                      
                }
                /* If no errors, then add the user to the database */

                if(empty($errors))
                {
                    $form_data['password'] = $this->member_m->hash($form_data['password'], $this->member_m->unique_salt());
                    /* $hashed_password = password_hash($formData["password"], PASSWORD_DEFAULT);
                    $create_user = $db->prepare("INSERT INTO users(name, email, username, password, created_at) VALUES(:name, :email, :username, :password, NOW())");
                    $create_user->execute(array(
                        ":name" => $formData["name"],
                        ":email" => $formData["email"],
                        ":username" => $formData["username"],
                        ":password" => $hashed_password
                    ));
                    $user_id = $db->lastInsertId();
                    $_SESSION["user"] = array(
                      "id" => $user_id,
                      "name" => $formData["name"],
                      "email" => $formData["email"],
                      "username" => $formData["username"],
                      "password" => $hashed_password
                    );
                    $success = true; */
                }
            }
            echo json_encode(array("errors" => $errors, "success" => $success));
        }
        /*if ($this->input->server('REQUEST_METHOD') == 'POST') { */
        //if ($this->input->is_ajax_request()) {
            /* $new_data_member = array (
				'username' => $this->input->post('username'),
				'email' => $this->input->post('email'),
				'password' => md5($this->input->post('password'))); */
            // Secondary Validation Rules
			/* $new_member_rules = $this->member_m->new_member_rules;

			$this->form_validation->set_rules($new_member_rules);
			if ($this->form_validation->run()  == TRUE) {

                redirect('member/dashboard');
            } else { */
                // Make sure the user does not exist and the email does not exist.
                            
				/* $errors = validation_errors();
                echo json_encode(array($errors=>'js-reg-error')); */
                /* $this->form_validation->set_message('signup_error', '<div class="alert alert-danger signup_error">This Username Already Exists.</div>'); */
            // } 
           /* $action = [];
            
            $action['redirect'] = 'member/dashboard';
             $this->output
             ->set_content_type('application/json') //set Json header
             ->set_output(json_encode($action, JSON_PRETTY_PRINT))
             ->_display();
             exit;// make output json encoded
             return;
        } */ 
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
    //}
    
    function login() {
        // Retrieve Login Data
        if ($this->input->is_ajax_request()) {
                $login_data = array(
					'username_login' 
									=> $this->input->post('username_login'),
					'password_login' 
									=> $this->input->post('password_login'));
            echo '<div class = "js-login-error">Username not found</div>';
        }
        
        // Compare with Login Data in Database
        /* $validate = $this->member_m->login_database($login_data); */
        
        // If Match, Save the User Session to remain logged in, Switch to Welcome User!
        // Go to User Details Page, Listing:
        // Username (Can't be Changed)
        // Password
        // Email Address
        // City and Country
        // Lottery Game they are currently Playing (Array of Lotteries)
        
        // If Incorrect Match, Send Error Message back to Login Box to js-login-error div.
        
        /* if ($validate) {
            return True;
        }
     return False;    */
    }
    
     /* Checks if the Username address exists
	 * 
	 * @param       $username_value   string
	 * @return      if count($result) object (user) then True else False
	 */
    public function _unique_username($str)
    {
        // Do Not validate if email already exists
        // Unless it's the email for the current user
        
        $sql = "SELECT * FROM users WHERE username = '{$str}' LIMIT 1";
	     $result = $this->db->query($sql);
	     $row = $result->row();
        
        if (count($result)) 
        {
            return TRUE;
        }
    return FALSE;
    }
    
    /* Checks if the email address exists
	 * 
	 * @param       $email_value   string
	 * @return      $row object (user) or False
	 */
	 public function _unique_email($str) 
	 {
	     $sql = "SELECT id, email, name, username FROM users WHERE email = '{$str}' LIMIT 1";
	     $result = $this->db->query($sql);
	     $row = $result->row();
	     return ($result->num_rows() === 1 && $row->email) ? $row : FALSE;
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
    
    public function DashBoard() {
        echo("We have reached this point");
        exit ('Stop');
    }
}