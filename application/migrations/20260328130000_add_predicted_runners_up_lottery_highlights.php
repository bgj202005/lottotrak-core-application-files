<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Predicted_Runners_Up_Lottery_Highlights extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_column('lottery_highlights', array(
            'predicted_runners_up' => array(
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => TRUE,
                'default'    => NULL,
                'after'      => 'predicted_winning_sum'
            )
        ));
    }

    public function down()
    {
        $this->dbforge->drop_column('lottery_highlights', 'predicted_runners_up');
    }
}
