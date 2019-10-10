<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Subscription_Key extends CI_Migration {
    
    public function up()
    {
        $field = (array(
            'subscription_key' => array(
                'type' => 'VARCHAR',
                'constraint' => 255
            ),
        ));
        $this->dbforge->add_column('members', $field);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('members', 'subscription_key');
    }
}