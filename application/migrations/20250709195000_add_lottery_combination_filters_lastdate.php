<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Lottery_Combination_Filters_Lastdate extends CI_Migration {
    
    public function up()
    {
        $field = (array(
            'lastdate' => array(
						'type' => 'DATE',
                        'null' => TRUE, 
                        'after' => 'combo_id'
            ),          
        ));
        $this->dbforge->add_column('lottery_combination_filters', $field);
    }
    public function down()
    {
        $this->dbforge->drop_column('lottery_combination_filters', 'firstdate');
        $this->dbforge->drop_column('lottery_combination_filters', 'lastdate');
    }
}