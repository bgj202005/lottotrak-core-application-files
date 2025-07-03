<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Lottery_Combination_Filters extends CI_Migration {

        public function up()
        {
        	$this->dbforge->add_key('id', TRUE);
        	$this->dbforge->add_field(array(
                        'id' => array(
                            'type' => 'INT',
                            'constraint' => 11,
                            'unsigned' => TRUE,
                            'auto_increment' => TRUE
                        ),
                        'file_name' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '255',
                            'null' => FALSE,
                            'comment' => 'text file without .txt extention'
                        ),
                        'N' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => 'Balls to Predict (N)'
                        ),
                        'R' => array(
                            'type' => 'INT',
                            'constraint' => '2',
                            'unsigned' => TRUE,
                            'comment' => 'Pick Game (R)'
                        ),
                        'CCCC' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => 'Combinations Filtered Result C(N,R)'
                        ),
                        'hwc' => array(
                            'type' => 'TINYINT',
                            'constraint' => '1',
                            'unsigned' => TRUE,
                            'default' => 0, 
                            'comment' => 'hwc settings (1=hwc, 0=not hwc)'
                        ),
                        'followers' => array(
                            'type' => 'TINYINT',
                            'constraint' => '1',
                            'unsigned' => TRUE,
                            'default' => 0, 
                            'comment' => 'followers settings (1=followers, 0=not followers)'
                        ),
                        'friends' => array(
                            'type' => 'TINYINT',
                            'constraint' => '1',
                            'unsigned' => TRUE,
                            'default' => 0, 
                            'comment' => 'friends settings (1=friends, 0=not friends)'
                        ),
                        'h_w_c_group' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '50',
                            'null' => TRUE
                        ),
                        'follower_type' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '50',   
                            'null' => TRUE
                        ),
                        'ball_points' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '11',
                            'null' => TRUE
                        ),
                        'position_points' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',  
                            'null' => TRUE
                        ),
                        'selected_friends' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100', 
                            'null' => TRUE
                        ),
                        'trends' => array(
                           'type' => 'VARCHAR',
                            'constraint' => '50',   
                            'null' => TRUE
                        ),
                        'winning_sums' => array(
                           'type' => 'VARCHAR',
                            'constraint' => '50',   
                            'null' => TRUE
                        ),
                        'winning_digits' => array(
                           'type' => 'VARCHAR',
                            'constraint' => '50',   
                            'null' => TRUE
                        ),
                        'repeaters' => array(
                           'type' => 'VARCHAR',
                            'constraint' => '50',   
                            'null' => TRUE
                        ),
                        'consecutives' => array(
                           'type' => 'VARCHAR',
                            'constraint' => '50',   
                            'null' => TRUE
                        ),
                        'parity' => array(
                           'type' => 'VARCHAR',
                            'constraint' => '50',   
                            'null' => TRUE
                        ),
                        'decades' => array(
                           'type' => 'VARCHAR',
                            'constraint' => '50',   
                            'null' => TRUE
                        ),
                        'last_digits' => array(
                           'type' => 'VARCHAR',
                            'constraint' => '50',   
                            'null' => TRUE
                        ),
                        'number_range' => array(
                           'type' => 'VARCHAR',
                            'constraint' => '50',   
                            'null' => TRUE
                        ),
                        'adjacents' => array(
                           'type' => 'VARCHAR',
                           'constraint' => '50',   
                           'null' => TRUE
                        ),
                        'user' => array(
                            'type' => 'TINYINT',
                            'constraint' => '1',
                            'unsigned' => TRUE,
                            'default' => 0, 
                            'comment' => 'Administrator User (1=admin, 0=member)'
                        ),
                        'user_id' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => 'Administrator ID'
                        ),
                        'member_id' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => 'Member ID'
                        ),
                        'extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => 'Extra Only'
                        ),
                        '1_win' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '1 Number Only'
                        ),
                        '1_win_extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '1 Number plus extra'
                        ),
                        '2_win' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '2 Number Winners'
                        ),
                        '2_win_extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '2 Numbers plus extra'
                        ),
                        '3_win' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '3 Number Winners'
                        ),
                        '3_win_extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '3 Numbers plus extra'
                        ),
                        '4_win' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '4 Numbers Winners'
                        ),
                        '4_win_extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '4 Numbers plus extra'
                        ),
                        '5_win' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '5 Numbers Winners'
                        ),
                        '5_win_extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '5 Numbers plus extra'
                        ),
                        '6_win' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '6 Numbers Winners'
                        ),
                        '6_win_extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '6 Numbers plus extra'
                        ),
                        '7_win' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '7 Numbers Winners'
                        ),
                        '7_win_extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '7 Numbers plus extra'
                        ),
                        '8_win' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '8 Numbers Winners'
                        ),
                        '8_win_extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '8 Numbers plus extra'
                        ),
                        '9_win' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '9 Numbers Winners'
                        ),
                        '9_win_extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => '9 Numbers plus extra'
                        ),
                        'active' => array(
                           'type' => 'TINYINT',
                           'constraint' => '1',
                           'unsigned' => TRUE,
                           'default' => 0, 
                           'comment' => '(1) = active, (0) = expired'
                        ),
                       'lottery_id' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        )
            ));
            $this->dbforge->add_key('lottery_id');
            $this->dbforge->create_table('lottery_combination_filters');
        }
        public function down()
        {
                $this->dbforge->drop_table('lottery_combination_filters');
        }
}