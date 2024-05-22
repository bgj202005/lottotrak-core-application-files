<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Position_Last_H_w_c_stats extends CI_Migration {
    
    public function up()
    {
        $fields = (array(
            'position_last' => array(
        	    'type' => 'VARCHAR',
        	    'constraint' => '3000',
                'null' => TRUE, 
                'after' => 'position'
            )
        ));
        $this->dbforge->add_column('lottery_h_w_c_stats', $fields);
    }
    public function down()
    {
        $this->dbforge->drop_column('lottery_h_w_c_stats', 'position_last');
    }
}