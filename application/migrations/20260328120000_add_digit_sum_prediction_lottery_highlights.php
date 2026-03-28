<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Digit_Sum_Prediction_Lottery_Highlights extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_column('lottery_highlights', array(
            'predicted_digit_sum' => array(
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'unsigned'   => TRUE,
                'null'       => TRUE,
                'default'    => NULL,
                'after'      => 'parity'
            ),
            'predicted_winning_sum' => array(
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'unsigned'   => TRUE,
                'null'       => TRUE,
                'default'    => NULL,
                'after'      => 'predicted_digit_sum'
            )
        ));
    }

    public function down()
    {
        $this->dbforge->drop_column('lottery_highlights', 'predicted_digit_sum');
        $this->dbforge->drop_column('lottery_highlights', 'predicted_winning_sum');
    }
}
