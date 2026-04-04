<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Modify_Predicted_Runners_Up_Lottery_Highlights extends CI_Migration {

    public function up()
    {
        $this->dbforge->modify_column('lottery_highlights', array(
            'predicted_runners_up' => array(
                'name'       => 'predicted_runners_up',
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => TRUE,
                'default'    => NULL
            )
        ));
    }

    public function down()
    {
        $this->dbforge->modify_column('lottery_highlights', array(
            'predicted_runners_up' => array(
                'name'       => 'predicted_runners_up',
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => TRUE,
                'default'    => NULL
            )
        ));
    }
}
