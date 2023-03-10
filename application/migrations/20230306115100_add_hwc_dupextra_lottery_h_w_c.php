<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Hwc_Dupextra_Lottery_H_w_c extends CI_Migration {
    
    public function up()
    {
        $fields = (array(
            'dupextra' => array(
        	    'type' => 'VARCHAR',
        	    'constraint' => '500',
                'null' => false, 
                'after' => 'colds'
            )
        ));
        $this->dbforge->add_column('lottery_h_w_c', $fields);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('lottery_h_w_c', 'dupextra');
    }
}