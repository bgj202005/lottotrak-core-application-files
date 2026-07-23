<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Lottery_Numbers_Prev_Lottery_Numbers extends CI_Migration {
    
    public function up()
    {
        $fields = array(
            'follower_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'positions'
            ),
            'ball_points' => array(
                'type' => 'VARCHAR',
                'constraint' => '11',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'follower_type'
            ),
            'position_points' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'ball_points'
            ),
            'lottery_numbers' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'position_points'
            ),
            'prev_lottery_numbers' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'lottery_numbers'
            ),
        );
        $this->dbforge->add_column('lottery_followers', $fields);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('lottery_followers', 'follower_type');
        $this->dbforge->drop_column('lottery_followers', 'ball_points');
        $this->dbforge->drop_column('lottery_followers', 'position_points');
        $this->dbforge->drop_column('lottery_followers', 'lottery_numbers');
        $this->dbforge->drop_column('lottery_followers', 'prev_lottery_numbers');
    }
}
