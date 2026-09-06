<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Extra_Prediction_Columns extends CI_Migration {

    public function up()
    {
        // lottery_h_w_c: extra ball predictions for independent / duplicate extra ball lotteries
        if (!$this->db->field_exists('hwc_extra_predictions', 'lottery_h_w_c')) {
            $this->dbforge->add_column('lottery_h_w_c', array(
                'hwc_extra_predictions' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '300',
                    'null' => TRUE,
                    'default' => NULL,
                    'after' => 'prev_h_w_c_predictions'
                )
            ));
        }
        if (!$this->db->field_exists('prev_hwc_extra_predictions', 'lottery_h_w_c')) {
            $this->dbforge->add_column('lottery_h_w_c', array(
                'prev_hwc_extra_predictions' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '300',
                    'null' => TRUE,
                    'default' => NULL,
                    'after' => 'hwc_extra_predictions'
                )
            ));
        }

        // lottery_followers: extra ball predictions
        if (!$this->db->field_exists('extra_numbers', 'lottery_followers')) {
            $this->dbforge->add_column('lottery_followers', array(
                'extra_numbers' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '300',
                    'null' => TRUE,
                    'default' => NULL,
                    'after' => 'prev_lottery_numbers'
                )
            ));
        }
        if (!$this->db->field_exists('prev_extra_numbers', 'lottery_followers')) {
            $this->dbforge->add_column('lottery_followers', array(
                'prev_extra_numbers' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '300',
                    'null' => TRUE,
                    'default' => NULL,
                    'after' => 'extra_numbers'
                )
            ));
        }

        // lottery_h_w_c_followers: extra ball predictions
        if (!$this->db->field_exists('extra_numbers', 'lottery_h_w_c_followers')) {
            $this->dbforge->add_column('lottery_h_w_c_followers', array(
                'extra_numbers' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '300',
                    'null' => TRUE,
                    'default' => NULL,
                    'after' => 'prev_lottery_numbers'
                )
            ));
        }
        if (!$this->db->field_exists('prev_extra_numbers', 'lottery_h_w_c_followers')) {
            $this->dbforge->add_column('lottery_h_w_c_followers', array(
                'prev_extra_numbers' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '300',
                    'null' => TRUE,
                    'default' => NULL,
                    'after' => 'extra_numbers'
                )
            ));
        }
    }

    public function down()
    {
        if ($this->db->field_exists('hwc_extra_predictions', 'lottery_h_w_c')) {
            $this->dbforge->drop_column('lottery_h_w_c', 'hwc_extra_predictions');
        }
        if ($this->db->field_exists('prev_hwc_extra_predictions', 'lottery_h_w_c')) {
            $this->dbforge->drop_column('lottery_h_w_c', 'prev_hwc_extra_predictions');
        }
        if ($this->db->field_exists('extra_numbers', 'lottery_followers')) {
            $this->dbforge->drop_column('lottery_followers', 'extra_numbers');
        }
        if ($this->db->field_exists('prev_extra_numbers', 'lottery_followers')) {
            $this->dbforge->drop_column('lottery_followers', 'prev_extra_numbers');
        }
        if ($this->db->field_exists('extra_numbers', 'lottery_h_w_c_followers')) {
            $this->dbforge->drop_column('lottery_h_w_c_followers', 'extra_numbers');
        }
        if ($this->db->field_exists('prev_extra_numbers', 'lottery_h_w_c_followers')) {
            $this->dbforge->drop_column('lottery_h_w_c_followers', 'prev_extra_numbers');
        }
    }
}
