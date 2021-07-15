<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Users_Ip_Address extends CI_Migration {
    
    public function up()
    {
        $field = (array(
            'ip_address' => array(
                'type'  => 'INT',
                'constraint' => '4',
                'unsigned'  => TRUE,
                'null' => FALSE
            )
        ));
        $this->dbforge->add_column('users', $field);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('users', 'ip_address');
    }
}