<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Activate extends Frontend_Controller {
    
    function __construct() 
    {
        parent::__construct();
        $this->load->model('activate_m');
        $this->load->library('session');
        $this->data['recent_news'] = $this->article_m->get_recent();
    }
    
    public function index()
    {
        
        $this->data['member'] = (object) $this->activate_m->array_from_post(array('id', 'username', 'email', 'first_name', 'last_name', 'password',
            'city', 'state_prov', 'country_id', 'lottery_id'));
        $id = $this->input->post('id');    
        if ($id)
        {
            $this->data['status'] = TRUE;
            $this->data['alert_message'] = "Your Account has been activated.";
            $rules = $this->activate_m->activation_member_rules;
            $this->form_validation->set_rules($rules);
            
            if ($this->form_validation->run()  == TRUE) 
		    {
                // We can save and redirect
                $this->data['member']->password = $this->activate_m->hash($this->data['member']->password);
                $id = $this->activate_m->save($this->data['member'], $id);
                
                // Session Data //
                $member = array('username' => $this->data['member']->username,
                                'email' => $this->data['member']->email,
                                'first_name' => $this->data['member']->first_name,
                                'id' => $this->data['member']->id,
                                'loggedin' => TRUE
            );
                $this->session->set_userdata ( $member );
                // Redirect to Member's Dashboard
                redirect('member/dashboard');
            }
            else // Errors found
            {
                    
            }
        } 
        else
            {
                $this->data['alert_message'] = "You can not tamper with this form.";    
            }
         // Default View
    $this->data['subview'] = 'member/activate';
    $this->load->view('_main_layout', $this->data); 
    }

    /* As a first step, GET the token and to verify the user against the database. Check,
    1. if such a token exists,
    2. it is not expired,
    3. The user is not already subscribed
    4. Add more validation as you deem fit. */
    public function validation_code() 
    {
        // Validate the /activate/XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX portion to see if exists 
        if ($this->uri->segment(3)!==NULL) {
            $urlsecuretoken = $this->uri->segment(3);
                
                $id = $this->activate_m->validate_member($urlsecuretoken);
                if ($id) {
                    if ($this->activate_m->update_status($urlsecuretoken, 0)==TRUE) // Change this to 1 after debugging //
                    {
                        $this->data['member'] = $this->activate_m->get_member($id);
                        $this->data['alert_message'] = "Your Account has been activated.";
                        $this->data['status'] = TRUE; 
                    }
                }
                    else
                    {
                        $this->data['status'] = FALSE; 
                        $this->data['alert_message'] = "There is a problem with the url security token.";
                        $this->data['second_message'] = "All accounts not validated in 5 days are removed. Please Re-register.";
                    } 
        }     
        else 
        {
            $this->data['alert_message'] = "Can not verify the security token.";
        }
    // Default View
    $this->data['subview'] = 'member/activate';
    $this->load->view('_main_layout', $this->data); 
    }
    
    public function state_prov_check()
    {
            if ($this->input->post('state_prov') === '')  {
            $this->form_validation->set_message('state_prov_check', 'Please choose your State or Province.');
            return FALSE;
        }
        else {
            return TRUE;
        }
    }

    public function country_check()
    {
            if ($this->input->post('country_id') === '')  {
            $this->form_validation->set_message('country_check', 'Please choose your Country.');
            return FALSE;
        }
        else {
            return TRUE;
        }
    }
    
    public function lottery_check()
    {
            if ($this->input->post('lottery_id') === '0')  {
            $this->form_validation->set_message('lottery_check', 'Please choose your Lottery.');
            return FALSE;
        }
        else {
            return TRUE;
        }
    }
}