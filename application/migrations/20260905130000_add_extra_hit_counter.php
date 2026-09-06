<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Extra_Hit_Counter extends CI_Migration {

    public function up()
    {
        // Extra ball prediction hit record: hits = drawn extra was in predicted pool, checked = draws evaluated
        if (!$this->db->field_exists('extra_hits', 'lottery_h_w_c')) {
            $this->dbforge->add_column('lottery_h_w_c', array(
                'extra_hits' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'default' => 0,
                    'null' => FALSE,
                    'after' => 'prev_hwc_extra_predictions'
                )
            ));
        }
        if (!$this->db->field_exists('extra_checked', 'lottery_h_w_c')) {
            $this->dbforge->add_column('lottery_h_w_c', array(
                'extra_checked' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'unsigned' => TRUE,
                    'default' => 0,
                    'null' => FALSE,
                    'after' => 'extra_hits'
                )
            ));
        }
    }

    public function down()
    {
        if ($this->db->field_exists('extra_hits', 'lottery_h_w_c')) {
            $this->dbforge->drop_column('lottery_h_w_c', 'extra_hits');
        }
        if ($this->db->field_exists('extra_checked', 'lottery_h_w_c')) {
            $this->dbforge->drop_column('lottery_h_w_c', 'extra_checked');
        }
    }
}
