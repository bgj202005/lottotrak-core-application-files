<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Remove_Pick_Id_From_Lottery_Combination_Files extends CI_Migration {
    public function up()
    {
        $this->dbforge->drop_column('lottery_combination_files', 'pick_id');
    }
    public function down()
    {
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
}