<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Member extends Frontend_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('member_m');
        $this->data['recent_news'] = $this->article_m->get_recent();
    }

    public function index() 
    {   // If a new user, their account must be validated
        if ($this->session->flashdata('token')=="validate") {
        // We must retrieve the next record id and save the data but the account will be not validated yet.
            $this->data['subview'] = $this->data['validate_email']->template;
            $this->load->view('_main_layout', $this->data);
        }
    }

    /**  If member exists, then retrieve data from member, log them in, save the session
     *   and log them into there account. 
     *   If member does not exist, then retrieve a new id, save to database
     *   and send double opt-in email to verify to activate the account
     *   display email validation message
     * save_member
     *
     * @param [integer] $id
     * @return void
     */    
    private function save_member($id = NULL) 
	{
		// Fetch a user or set a new one
		$id == NULL OR $this->data['member'] = $this->member_m->get($id);
		if ($id) 
		{
			$this->data['member'] = $this->member_m->get($id); 
			count($this->data['member']) || $this->data['errors'][] = 'User could not be found';
        } 
		else 
		{
            $this->data['member'] = $this->member_m->get_new_member();
            // We can save and redirect
			$data = $this->user_m->array_from_post(array('username', 'email', 'password'));

			$data['password'] = $this->member_m->hash($data['password'], $this->member_m->unique_salt());
			$this->member_m->save($data, $id);
		}
    }
           
    function register() {
        
        $new_data_member = array (
            'username' => $this->input->post('username'),
            'email' => $this->input->post('email'),
            'password' => $this->input->post('password')
        );
        /*
         * Checking if posted fields are empty string (just in case) - e.g. user typing only whitespaces instead of actual name, email, username, password
         */
        // Secondary Validation Rules
        $new_member_rules = $this->member_m->new_member_rules;
        $this->form_validation->set_rules($new_member_rules);
        
        if ($this->form_validation->run() == FALSE)
        {             
           if (form_error('username')) { $error = '<div class="alert alert-danger">'.form_error('username').'</div>'; }
           if (form_error('email')) { $error = '<div class="alert alert-danger">'.form_error('email').'</div>'; }
           if (form_error('password')) { $error = '<div class="alert alert-danger">'.form_error('password').'</div>'; }
           if (form_error('confirm_password')) { $error = '<div class="alert alert-danger">'.form_error('confirm_password').'</div>'; }     
           
           $array = array(
            'error'   => true,
            'validation_error' => $error
           );
        } 
        else
        {
            // Use Token to send a message to validate Email Address before Activating Account.
            $this->session->$this->session->set_flashdata('token', 'validate');
            $this->save_member(); // Save to the Database
            $array = array(
                'success' => '<div class="alert alert-success">You are now registered with Lottotrak. You\'re account has been created.<br />
                Redirecting to dashboard in 3 seconds. Please wait...</div>'
           );
        }
        echo json_encode($array);
    }

function login() {
// Retrieve Login Data
if ($this->input->is_ajax_request()) {
        $login_data = array(
            'username_login' 
                            => $this->input->post('username_login'),
            'password_login' 
                            => $this->input->post('password_login'));
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
$array = array(
    'success' => '<div class="alert alert-success">You are now Logged into Lottotrak.</div>'
);

echo json_encode($array);             
    }

    public function dashboard() {
        echo("We have reached this point");
        exit ('Stop');
    }

    public function _unique_username($str)
	{
		$sql = "SELECT username FROM members WHERE username = '{$str}' LIMIT 1";
	    $result = $this->db->query($sql);
	    $row = $result->row();
			
	    if (count($row)) 
		{
			$this->form_validation->set_message('_unique_username', '%s already exists. Please type another username');
			return FALSE;
		}
	return TRUE;
	}

	public function _unique_email($str)
	{
		$sql = "SELECT email FROM members WHERE email = '{$str}' LIMIT 1";
	    $result = $this->db->query($sql);
	    $row = $result->row();
			
		if (count($row)) 
		{
			$this->form_validation->set_message('_unique_email', '%s already exists. Please type another email address');
			return FALSE;
		}
		return TRUE;
	}
}