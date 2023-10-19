<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Friends_wins_Lottery_Friends extends CI_Migration {
    
    public function up()
    {
        $fields = (array(
                'wins' => array(
        	    'type' => 'VARCHAR',
        	    'constraint' => '1000',
                'null' => false, 
                'after' => 'lottery_friends'
            )
        ));
        $this->dbforge->add_column('lottery_friends', $fields);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('lottery_friends', 'wins');
    }
}