<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Modify_Lottery_Nonfriends_Lottery_Nonfriends extends CI_Migration {

    public function up()
    {
        $this->dbforge->modify_column('lottery_nonfriends',array(
                    'lottery_nonfriends' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '4500'
                    )
            ));
    }

    public function down()
    {
        $this->dbforge->modify_column('lottery_nonfriends',array(
            'lottery_nonfriends' => array(
            'type' => 'VARCHAR',
            'constraint' => '4500'
            )
    ));
    }
}