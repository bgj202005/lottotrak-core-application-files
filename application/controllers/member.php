<?php
class Member extends Frontend_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('member_m');
    }
    
    function register() {
        
        /*if ($this->input->server('REQUEST_METHOD') == 'POST') { */
        if ($this->input->is_ajax_request()) {
            exit('this is an ajax request.');
            
            // Setup the form
            $new_member_rules = $this->page_m->new_member_rules;
            $this->form_validation->set_rules($new_member_rules);
            
            if ($this->form_validation->run()  == TRUE) {
                
                redirect('page/dashboard');
            } else {
                // Make sure the user does not exist and the email does not exist.
                $error = validation_errors();
                echo json_encode(['js-reg-error'=>$errors]);
                /* $this->form_validation->set_message('signup_error', '<div class="alert alert-danger signup_error">This Username Already Exists.</div>'); */
            }
            
            $action = [];
            
            /* $action['redirect'] = 'page/dashboard';
             $this->output
             ->set_content_type('application/json') //set Json header
             ->set_output(json_encode($action, JSON_PRETTY_PRINT))
             ->_display();
             exit;// make output json encoded
             */ return;
        } else {
            exit('test');
        }
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
    
    function login() {
        // Retrieve Login Data
        $login_data = array(
            'username_login' 
                            => $this->input->post('username_login'),
            'password_login' 
                            => $this->input->post('password_login'));
        
        // Compare with Login Data in Database
        $validate = $this->member_m->login_database($login_data);
        
        // If Match, Save the User Session to remain logged in, Switch to Welcome User!
        // Go to User Details Page, Listing:
        // Username (Can't be Changed)
        // Password
        // Email Address
        // City and Country
        // Lottery Game they are currently Playing (Array of Lotteries)
        
        // If Incorrect Match, Send Error Message back to Login Box to js-login-error div.
        
        
        if ($validate) {
            return True;
        }
        
     return False;   
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
    
    public function _unique_username()
    {
        // Do Not validate if email already exists
        // Unless it's the email for the current user
        
        //dump($id); exit(1);
        $this->db->where('username', $this->input->post('username'));
        !$id || $this->db->where('id !=', $id);
        $user = $this->user_m->get();
        
        if (count($user)) {
            $this->form_validation->set_message('_unique_username', '%s already exists. Please type another username');
            return FALSE;
        }
        return TRUE;
    }
    
    public function _unique_email()
    {
        // Do Not validate if email already exists
        // Unless it's the email for the current user
        
        $this->db->where('email', $this->input->post('email'));
        !$id || $this->db->where('id !=', $id);
        $user = $this->user_m->get();
        
        if (count($user)) {
            $this->form_validation->set_message('_unique_email', '%s already exists. Please type another email address');
            return FALSE;
        }
        return TRUE;
    }
    
    function DashBoard() {
        exit ('Stop');
    }
}
