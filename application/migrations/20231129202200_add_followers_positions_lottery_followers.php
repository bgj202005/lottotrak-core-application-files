<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Followers_positions_lottery_Followers extends CI_Migration {
    
    public function up()
    {
        $fields = (array(
                'positions' => array(
        	    'type' => 'VARCHAR',
        	    'constraint' => '300',
                'null' => false, 
                'after' => 'wins'
            )
        ));
        $this->dbforge->add_column('lottery_followers', $fields);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('lottery_followers', 'positions');
    }
}