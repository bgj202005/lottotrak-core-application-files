<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Previous_Lottery_Nonfollowers extends CI_Migration {
    
    public function up()
    {
        $fields = array(
            'prev_lottery_nonfollowers' => array(
                'type' => 'LONGTEXT',
                'null' => TRUE,
                'after' => 'lottery_nonfollowers'
            ),
            'prev_draw_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
                'after' => 'prev_lottery_nonfollowers'
            ),
        );
        $this->dbforge->add_column('lottery_nonfollowers', $fields);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('lottery_nonfollowers', 'prev_lottery_nonfollowers');
        $this->dbforge->drop_column('lottery_nonfollowers', 'prev_draw_id');
    }
}
