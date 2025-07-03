<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_Lottery_Combination_Files extends CI_Migration {
    
    public function up()
    {
        $this->dbforge->drop_column('lottery_combination_files', 'lottery_id');
        $fields = (array(
            'pick_id' => array(
        	    'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE, 
                'after' => 'CCCC'
             )
        ));
        $this->dbforge->add_column('lottery_combination_files', $fields);
    }
    public function down()
    {
        $this->dbforge->drop_column('lottery_combination_files', 'pick_id');
        $fields = (array(
            'lottery_id' => array(
        	    'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE, 
                'after' => 'CCCC'
             )
        ));
        $this->dbforge->add_column('lottery_combination_files', $fields);
    }
}