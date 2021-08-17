<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Extra_Included_Extra_Draws_Lottery_Nonfollowers extends CI_Migration {
    
    public function up()
    {
        $fields = (array(
            'extra_included' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => FALSE,
                'default' => 0
            ),
            'extra_draws' => array(
                'type' => 'INT',
                'constraint' => 1,
                'unsigned' => FALSE,
                'default' => 0
            ),
        ));
        $this->dbforge->add_column('lottery_nonfollowers', $fields);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('lottery_nonfollowers', 'extra_included');
        $this->dbforge->drop_column('lottery_nonfollowers', 'extra_draws');
    }
}