<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Combination_Table_Filters_Combo_Id extends CI_Migration {
    
    public function up()
    {
        $fields = (array(
            'combo_id' => array(
        	    'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => TRUE, 
                'after' => 'active'
             )
        ));
        $this->dbforge->add_column('lottery_combination_filters', $fields);
    }
    public function down()
    {
        $this->dbforge->drop_column('lottery_combination_filters', 'combo_id');
    }
}