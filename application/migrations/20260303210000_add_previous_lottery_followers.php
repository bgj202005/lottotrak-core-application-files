<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Previous_Lottery_Followers extends CI_Migration {
    
    public function up()
    {
        $fields = array(
            'prev_lottery_followers' => array(
                'type' => 'LONGTEXT',
                'null' => TRUE,
                'after' => 'lottery_followers'
            ),
            'prev_draw_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
                'after' => 'prev_lottery_followers'
            ),
        );
        $this->dbforge->add_column('lottery_followers', $fields);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('lottery_followers', 'prev_lottery_followers');
        $this->dbforge->drop_column('lottery_followers', 'prev_draw_id');
    }
}
