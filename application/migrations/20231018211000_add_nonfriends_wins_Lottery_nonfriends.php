<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Nonfriends_wins_Lottery_nonfriends extends CI_Migration {
    
    public function up()
    {
        $fields = (array(
                'wins' => array(
        	    'type' => 'VARCHAR',
        	    'constraint' => '100',
                'null' => false, 
                'after' => 'lottery_nonfriends'
            )
        ));
        $this->dbforge->add_column('lottery_nonfriends', $fields);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('lottery_nonfriends', 'position');
    }
}