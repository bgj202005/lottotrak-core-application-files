<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Lottery_Combination_Filters_Numbers extends CI_Migration {
    
    public function up()
    {
        $field = (array(
            'numbers' => [
            'type' => 'VARCHAR',
            'constraint' => 300,
            'null' => TRUE,
            'after' => 'adjacents'
            ]       
        ));
        $this->dbforge->add_column('lottery_combination_filters', $field);
    }
    public function down()
    {
        $this->dbforge->drop_column('lottery_combination_filters', 'numbers');
    }
}