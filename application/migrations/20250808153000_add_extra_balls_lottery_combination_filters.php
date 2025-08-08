<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Extra_Balls_Lottery_Combination_Filters extends CI_Migration {

        public function up()
        {
        	$field = (array(
                        'extra_balls' => array(
                        'type' => 'VARCHAR',
                        'constraint' => '50',   
                        'null' => TRUE,
                         'after' => 'h_w_c_group'
                        )
            ));
             $this->dbforge->add_column('lottery_combination_filters', $field);
        }
        public function down()
        {
            $this->dbforge->drop_column('lottery_combination_filters', 'extra_balls');
        }
}