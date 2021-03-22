<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Lottery_Prizes extends CI_Migration {

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
                        'extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        '2_win' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        '2_win_extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        '3_win' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        '3_win_extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        '4_win' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        '4_win_extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        '5_win' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        '5_win_extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        '6_win' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        '6_win_extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        '7_win' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        '7_win_extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        '8_win' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        '8_win_extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        '9_win' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        '9_win_extra' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        'lottery_id' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
            ));
            $this->dbforge->add_key('lottery_id');
            $this->dbforge->create_table('lottery_prizes');
        }
        public function down()
        {
                $this->dbforge->drop_table('lottery_prizes');
        }
}