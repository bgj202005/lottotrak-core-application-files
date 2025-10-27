<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_users_inactivity extends CI_Migration {

    public function up()
    {
        // Add inactivity_timeout field to users table
        $fields = array(
            'inactivity_timeout' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1800, // Default 30 minutes (1800 seconds)
                'after' => 'logged_in',
                'comment' => 'Session inactivity timeout in seconds (300-14400 range: 5 min to 4 hours)'
            )
        );
        
        $this->dbforge->add_column('users', $fields);
        
        log_message('info', 'Migration: Added inactivity_timeout field to users table');
    }

    public function down()
    {
        // Remove inactivity_timeout field from users table
        $this->dbforge->drop_column('users', 'inactivity_timeout');
        
        log_message('info', 'Migration: Removed inactivity_timeout field from users table');
    }
}