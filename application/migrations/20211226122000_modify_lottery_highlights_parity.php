<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Modify_Lottery_Highlights_Parity extends CI_Migration {

    public function up()
    {
        $this->dbforge->modify_column('lottery_highlights',array(
                    'parity' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '510'
                    )
            ));
    }

    public function down()
    {
        $this->dbforge->modify_column('lottery_highlights',array(
            'parity' => array(
            'type' => 'VARCHAR',
            'constraint' => '255'
            )
    ));
    }
}