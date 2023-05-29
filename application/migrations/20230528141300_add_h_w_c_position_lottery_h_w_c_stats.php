<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_H_w_c_position_Lottery_H_w_c_stats extends CI_Migration {
    
    public function up()
    {
        $fields = (array(
            'position' => array(
        	    'type' => 'VARCHAR',
        	    'constraint' => '3000',
                'null' => false, 
                'after' => 'h_w_c_last_10'
            )
        ));
        $this->dbforge->add_column('lottery_h_w_c_stats', $fields);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('lottery_h_w_c_stats', 'position');
    }
}