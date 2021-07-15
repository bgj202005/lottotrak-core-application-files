<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Users_Logged_In_Last_Active extends CI_Migration {
    
    public function up()
    {
        $fields = (array(
            'logged_in' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => FALSE,
                'default' => 0
            ),
            'last_active' => array(
                'type'  => 'INT',
                'constraint' => '10',
                'unsigned'  => TRUE,
                'default' => '0',
                'null' => FALSE
            )
        ));
        $this->dbforge->add_column('users', $fields);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('users', 'logged_in');
        $this->dbforge->drop_column('users', 'last_active');
    }
}