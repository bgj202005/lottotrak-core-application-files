<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Enabled_To_Lottery_Profiles extends CI_Migration {
    
    public function up()
    {
        $field = (array(
            'enabled' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'   => 1,
                'unsigned' => TRUE
            )
        ));
        $this->dbforge->add_column('lottery_profiles', $field);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('lottery_profiles', 'enabled');
    }
}