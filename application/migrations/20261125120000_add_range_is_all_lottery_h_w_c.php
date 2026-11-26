<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Range_Is_All_Lottery_H_w_c extends CI_Migration {

    public function up()
    {
        $fields = array(
            'range_is_all' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'unsigned' => TRUE,
                'default' => 0,
                'null' => FALSE,
                'comment' => 'Draw Range dropdown was set to All Draws; keep tracking the growing total instead of the dynamic 50/100 default',
                'after' => 'range'
            )
        );
        $this->dbforge->add_column('lottery_h_w_c', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('lottery_h_w_c', 'range_is_all');
    }
}
