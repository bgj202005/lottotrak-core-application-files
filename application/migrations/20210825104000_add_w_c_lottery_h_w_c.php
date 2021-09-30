<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_W_C_Lottery_H_w_c extends CI_Migration {
    
    public function up()
    {
        $fields = (array(
            'w' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => FALSE,
                'comment' => 'The boundary start of warm numbers'
            ),
            'c' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => FALSE,
                'comment' => 'The boundary start of cold numbers'
            )
        ));
        $this->dbforge->add_column('lottery_h_w_c', $fields);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('lottery_h_w_c', 'w');
        $this->dbforge->drop_column('lottery_h_w_c', 'c');
    }
}