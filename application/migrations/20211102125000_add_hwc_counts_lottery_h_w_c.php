<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Hwc_Counts_Lottery_H_w_c extends CI_Migration {
    
    public function up()
    {
        $fields = (array(
            'h_count' => array(
                'type' => 'INT',
                'constraint' => 2,
                'unsigned' => FALSE,
                'comment' => 'Hot Count'
            ),
            'w_count' => array(
                'type' => 'INT',
                'constraint' => 2,
                'unsigned' => FALSE,
                'comment' => 'Warm Count'
            ),
            'c_count' => array(
                'type' => 'INT',
                'constraint' => 2,
                'unsigned' => FALSE,
                'comment' => 'Cold Count'
            )
        ));
        $this->dbforge->add_column('lottery_h_w_c', $fields);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('lottery_h_w_c', 'h_count');
        $this->dbforge->drop_column('lottery_h_w_c', 'w_count');
        $this->dbforge->drop_column('lottery_h_w_c', 'c_count');
    }
}