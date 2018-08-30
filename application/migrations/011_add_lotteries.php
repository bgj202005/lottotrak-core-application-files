<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Lotteries extends CI_Migration {
    
    public function up()
    {
        $this->dbforge->add_key('id', TRUE);
        $fields = (array(
            'lottery_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'default' => 0
            ),
        ));
        $this->dbforge->add_column('members', $fields);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('members', 'parent_id');
    }
}