<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Member_Active extends CI_Migration {
    
    public function up()
    {
        $field = (array(
            'member_active' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => FALSE,
                'default' => 0
            ),
        ));
        $this->dbforge->add_column('members', $field);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('members', 'member_active');
    }
}