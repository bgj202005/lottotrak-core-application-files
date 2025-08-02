<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_H_w_c_Wins_Lottery_H_w_c_Stats extends CI_Migration {
    
    public function up()
    {
        $fields = (array(
                'wins' => array(
        	    'type' => 'VARCHAR',
        	    'constraint' => '3000',
                'null' => false, 
                'after' => 'position_last'
            )
        ));
        $this->dbforge->add_column('lottery_h_w_c_stats', $fields);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('lottery_h_w_c_stats', 'wins');
    }
}