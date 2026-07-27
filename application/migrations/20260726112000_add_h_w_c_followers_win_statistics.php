<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_H_W_C_Followers_Win_Statistics extends CI_Migration {
    
    public function up()
    {
        $fields = array(
            'startdate' => array(
                'type' => 'DATE',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'prev_lottery_numbers'
            ),
            'lastdate' => array(
                'type' => 'DATE',
                'null' => TRUE,
                'default' => NULL,
                'after' => 'startdate'
            ),
            'extra' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => 'Extra Only',
                'after' => 'lastdate'
            ),
            '1_win' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '1 Number',
                'after' => 'extra'
            ),
            '1_win_extra' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '1 Number plus extra',
                'after' => '1_win'
            ),
            '2_win' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '2 Numbers',
                'after' => '1_win_extra'
            ),
            '2_win_extra' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '2 Numbers plus extra',
                'after' => '2_win'
            ),
            '3_win' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '3 Numbers',
                'after' => '2_win_extra'
            ),
            '3_win_extra' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '3 Numbers plus extra',
                'after' => '3_win'
            ),
            '4_win' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '4 Numbers',
                'after' => '3_win_extra'
            ),
            '4_win_extra' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '4 Numbers plus extra',
                'after' => '4_win'
            ),
            '5_win' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '5 Numbers',
                'after' => '4_win_extra'
            ),
            '5_win_extra' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '5 Numbers plus extra',
                'after' => '5_win'
            ),
            '6_win' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '6 Numbers',
                'after' => '5_win_extra'
            ),
            '6_win_extra' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '6 Numbers plus extra',
                'after' => '6_win'
            ),
            '7_win' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '7 Numbers',
                'after' => '6_win_extra'
            ),
            '7_win_extra' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '7 Numbers plus extra',
                'after' => '7_win'
            ),
            '8_win' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '8 Numbers',
                'after' => '7_win_extra'
            ),
            '8_win_extra' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '8 Numbers plus extra',
                'after' => '8_win'
            ),
            '9_win' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '9 Numbers',
                'after' => '8_win_extra'
            ),
            '9_win_extra' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'comment' => '9 Numbers plus extra',
                'after' => '9_win'
            ),
            'total_winners' => array(
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => 0,
                'after' => '9_win_extra'
            )
        );
        $this->dbforge->add_column('lottery_h_w_c_followers', $fields);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('lottery_h_w_c_followers', 'startdate');
        $this->dbforge->drop_column('lottery_h_w_c_followers', 'lastdate');
        $this->dbforge->drop_column('lottery_h_w_c_followers', 'extra');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '1_win');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '1_win_extra');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '2_win');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '2_win_extra');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '3_win');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '3_win_extra');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '4_win');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '4_win_extra');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '5_win');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '5_win_extra');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '6_win');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '6_win_extra');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '7_win');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '7_win_extra');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '8_win');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '8_win_extra');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '9_win');
        $this->dbforge->drop_column('lottery_h_w_c_followers', '9_win_extra');
        $this->dbforge->drop_column('lottery_h_w_c_followers', 'total_winners');
    }
}
