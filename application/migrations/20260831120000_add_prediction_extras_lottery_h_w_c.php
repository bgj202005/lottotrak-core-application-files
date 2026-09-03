<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Prediction_Extras_Lottery_H_w_c extends CI_Migration {

    public function up()
    {
        $fields = array(
            'prediction_extras' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'default' => 1,
                'null' => FALSE,
                'after' => 'prediction_pool'
            )
        );
        $this->dbforge->add_column('lottery_h_w_c', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('lottery_h_w_c', 'prediction_extras');
    }
}
