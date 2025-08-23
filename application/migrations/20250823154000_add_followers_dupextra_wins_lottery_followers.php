<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Followers_Dupextra_Wins_Lottery_Followers extends CI_Migration {
    
    public function up()
    {
        $fields = (array(
                'dupextra_wins' => array(
        	    'type' => 'VARCHAR',
        	    'constraint' => '3000',
                'null' => true, 
                'after' => 'wins'
            )
        ));
        $this->dbforge->add_column('lottery_followers', $fields);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('lottery_followers', 'dupextra_wins');
    }
}