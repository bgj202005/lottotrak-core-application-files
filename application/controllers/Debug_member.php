<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Debug_member extends Frontend_Controller 
{
    public function __construct() {
        parent::__construct();
        $this->load->model('member_m');
    }
    
    /**
     * Debug the current session state and access permissions
     */
    public function session_debug()
    {
        echo "<h1>Session Debug Information</h1>";
        echo "<style>body{font-family:monospace;} .error{color:red;} .success{color:green;} .info{color:blue;}</style>";
        
        // 1. Show all session data
        echo "<h2>Complete Session Data:</h2>";
        $session_data = $this->session->all_userdata();
        if (empty($session_data)) {
            echo "<span class='error'>No session data found</span><br>";
        } else {
            foreach ($session_data as $key => $value) {
                echo "<strong>$key:</strong> ";
                if (is_array($value)) {
                    echo "ARRAY(" . count($value) . " items)<br>";
                    foreach ($value as $subkey => $subvalue) {
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;$subkey: " . (is_string($subvalue) ? htmlspecialchars($subvalue) : gettype($subvalue)) . "<br>";
                    }
                } else {
                    echo htmlspecialchars($value) . "<br>";
                }
            }
        }
        
        // 2. Check terms agreement access
        echo "<h2>Terms Agreement Access Check:</h2>";
        $terms_pending = $this->session->userdata('terms_pending');
        $pending_member = $this->session->userdata('pending_member_data');
        
        echo "terms_pending: <strong>" . ($terms_pending ?: 'NULL') . "</strong><br>";
        echo "pending_member_data: <strong>" . ($pending_member ? 'SET' : 'NULL') . "</strong><br>";
        
        if ($terms_pending === 'active' && $pending_member) {
            echo "<span class='success'>✓ Terms agreement access: ALLOWED</span><br>";
        } else {
            echo "<span class='error'>✗ Terms agreement access: DENIED</span><br>";
            echo "<span class='info'>Expected: terms_pending='active' AND pending_member_data=SET</span><br>";
        }
        
        // 3. Check email validation access
        echo "<h2>Email Validation Access Check:</h2>";
        $validate_token = $this->session->userdata('validate_token');
        $validate_member = $this->session->userdata('validate_member');
        
        echo "validate_token: <strong>" . ($validate_token ?: 'NULL') . "</strong><br>";
        echo "validate_member: <strong>" . ($validate_member ? 'SET' : 'NULL') . "</strong><br>";
        
        if ($validate_token === 'validate' && $validate_member) {
            echo "<span class='success'>✓ Email validation access: ALLOWED</span><br>";
        } else {
            echo "<span class='error'>✗ Email validation access: DENIED (would show 'Unauthorized. Intrusion Detected.')</span><br>";
            echo "<span class='info'>Expected: validate_token='validate' AND validate_member=SET</span><br>";
        }
        
        // 4. Show direct access links for testing
        echo "<h2>Test Links:</h2>";
        echo "<a href='" . base_url() . "debug_member/test_registration'>Test Registration Flow</a><br>";
        
        if ($terms_pending === 'active' && $pending_member) {
            echo "<a href='" . base_url() . "member/terms_agreement'>✓ Access Terms Agreement (SHOULD WORK)</a><br>";
        } else {
            echo "<span class='error'>✗ Terms Agreement (Need registration session first)</span><br>";
        }
        
        if ($validate_token === 'validate' && $validate_member) {
            echo "<a href='" . base_url() . "member/validate_email'>✓ Access Email Validation (SHOULD WORK)</a><br>";
        } else {
            echo "<span class='error'>✗ Email Validation (Need to accept terms first) - THIS IS WHY YOU GET THE ERROR</span><br>";
        }
        
        echo "<a href='" . base_url() . "debug_member/clear_terms_data'>Clear Terms Session Data</a><br>";
        echo "<a href='" . base_url() . "debug_member/clear_session'>Clear All Session Data</a><br>";
        
        // 5. Show maintenance status
        echo "<h2>System Status:</h2>";
        $maintenance_status = $this->maintenance_m->maintenance_check();
        if ($maintenance_status == 1) {
            echo "<span class='error'>System in MAINTENANCE MODE</span><br>";
        } else {
            echo "<span class='success'>System ONLINE</span><br>";
        }
    }
    
    /**
     * Test the complete registration flow
     */
    public function test_registration()
    {
        echo "<h1>Testing Registration Flow</h1>";
        echo "<style>body{font-family:monospace;} .error{color:red;} .success{color:green;}</style>";
        
        // Step 1: Simulate registration
        echo "<h2>Step 1: Simulating Registration</h2>";
        $this->session->set_userdata('terms_pending', 'active');
        $this->session->set_userdata('pending_member_data', array(
            'username' => 'debuguser',
            'email' => 'debug@example.com'
        ));
        echo "<span class='success'>✓ Registration session data set</span><br>";
        
        // Step 2: Check terms access
        echo "<h2>Step 2: Testing Terms Agreement Access</h2>";
        if ($this->session->userdata('terms_pending') === 'active' && $this->session->userdata('pending_member_data')) {
            echo "<span class='success'>✓ Terms agreement should work</span><br>";
            echo "<a href='" . base_url() . "member/terms_agreement'>Click here to test terms agreement</a><br>";
        } else {
            echo "<span class='error'>✗ Terms agreement would fail</span><br>";
        }
        
        // Step 3: Simulate terms acceptance
        echo "<h2>Step 3: Simulating Terms Acceptance</h2>";
        $pending_member = $this->session->userdata('pending_member_data');
        $pending_member['terms_agreement'] = TRUE;
        
        // Clear registration tokens
        $this->session->unset_userdata('terms_pending');
        $this->session->unset_userdata('pending_member_data');
        
        // Set validation tokens
        $this->session->set_userdata('validate_token', 'validate');
        $this->session->set_userdata('validate_member', $pending_member);
        
        echo "<span class='success'>✓ Terms acceptance simulated, validation tokens set</span><br>";
        
        // Step 4: Check email validation access
        echo "<h2>Step 4: Testing Email Validation Access</h2>";
        if ($this->session->userdata('validate_token') === 'validate' && $this->session->userdata('validate_member')) {
            echo "<span class='success'>✓ Email validation should work</span><br>";
            echo "<a href='" . base_url() . "member/validate_email'>Click here to test email validation</a><br>";
        } else {
            echo "<span class='error'>✗ Email validation would fail</span><br>";
        }
        
        echo "<br><a href='" . base_url() . "debug_member/session_debug'>View Current Session Data</a>";
    }
    
    /**
     * Clear all session data
     */
    public function clear_session()
    {
        $this->session->sess_destroy();
        echo "All session data cleared. <a href='" . base_url() . "debug_member/session_debug'>Check session status</a>";
    }
    
    /**
     * Clear only terms-related session data
     */
    public function clear_terms_data()
    {
        $this->session->unset_userdata('terms_pending');
        $this->session->unset_userdata('pending_member_data');
        $this->session->unset_userdata('validate_token');
        $this->session->unset_userdata('validate_member');
        
        echo "Terms-related session data cleared. <a href='" . base_url() . "debug_member/session_debug'>Check session status</a>";
    }
    
    /**
     * Simulate terms acceptance - sets up validate_token for testing email validation
     */
    public function simulate_terms_accepted()
    {
        echo "<h1>Simulating Terms Acceptance</h1>";
        echo "<style>body{font-family:monospace;} .error{color:red;} .success{color:green;}</style>";
        
        // Check if we have pending terms data
        $pending_member = $this->session->userdata('pending_member_data');
        
        if (!$pending_member) {
            echo "<span class='error'>No pending member data found. Run test registration first.</span><br>";
            echo "<a href='" . base_url() . "debug_member/test_registration'>Run Test Registration</a>";
            return;
        }
        
        // Simulate terms acceptance process
        $pending_member['terms_agreement'] = TRUE;
        
        // Clear registration session data
        $this->session->unset_userdata('terms_pending');
        $this->session->unset_userdata('pending_member_data');
        
        // Set validation session data (this is what process_terms does)
        $this->session->set_userdata('validate_token', 'validate');
        $this->session->set_userdata('validate_member', $pending_member);
        
        echo "<span class='success'>✓ Terms acceptance simulated successfully!</span><br>";
        echo "<span class='success'>✓ Validation tokens set</span><br>";
        echo "<br>Now you can test email validation:<br>";
        echo "<a href='" . base_url() . "member/validate_email'>Test Email Validation (should work now)</a><br>";
        echo "<a href='" . base_url() . "debug_member/session_debug'>Check Session Status</a><br>";
    }
}
?>