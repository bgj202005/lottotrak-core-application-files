<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Hwc_Stats_Last_Draw_id extends CI_Migration {
    
    public function up()
    {
        $fields = (array(
            'last_draw_id' => array(
        	    'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => TRUE, 
                'after' => 'draw_id'
             )
        ));
        $this->dbforge->add_column('lottery_h_w_c_stats', $fields);
    }
    public function down()
    {
        $this->dbforge->drop_column('lottery_h_w_c_stats', 'last_draw_id');
    }
}