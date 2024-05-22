<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Hwc_last_H_w_c extends CI_Migration {
    
    public function up()
    {
        $fields = (array(
            'hots_last' => array(
        	     'type' => 'VARCHAR',
        	    'constraint' => '1000',
                'null' => TRUE, 
                'after' => 'hots'
             ),
            'warms_last' => array(
        	   'type' => 'VARCHAR',
        	    'constraint' => '1000',
                'null' => TRUE, 
                'after' => 'warms'
            ),
            'colds_last' => array(
                'type' => 'VARCHAR',
                'constraint' => '1000',
                'null' => TRUE, 
                'after' => 'colds'
            ),
        ));
        $this->dbforge->add_column('lottery_h_w_c', $fields);
    }
    public function down()
    {
        $this->dbforge->drop_column('lottery_h_w_c', 'hots_last');
        $this->dbforge->drop_column('lottery_h_w_c', 'warms_last');
        $this->dbforge->drop_column('lottery_h_w_c', 'colds_last');
    }
}