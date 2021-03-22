<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Lottery_Stats extends CI_Migration {

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
                        'sum_10' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        'sum_100' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        'sum_200' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        'sum_400' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        'sum_500' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        'digits_10' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        'digits_100' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        'even_10' => array(
                            'type' => 'INT',
                            'constraint' => '2',
                            'unsigned' => TRUE
                        ),
                        'even_100' => array(
                            'type' => 'INT',
                            'constraint' => '2',
                            'unsigned' => TRUE
                        ),
                        'odd_10' => array(
                            'type' => 'INT',
                            'constraint' => '2',
                            'unsigned' => TRUE
                        ),
                        'odd_100' => array(
                            'type' => 'INT',
                            'constraint' => '2',
                            'unsigned' => TRUE
                        ),
                        'range_10' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        'range_100' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        'repeat_decade_10' => array(
                            'type' => 'INT',
                            'constraint' => '2',
                            'unsigned' => TRUE
                        ),
                        'repeat_decade_100' => array(
                            'type' => 'INT',
                            'constraint' => '2',
                            'unsigned' => TRUE
                        ),
                        'repeat_last_10' => array(
                            'type' => 'INT',
                            'constraint' => '2',
                            'unsigned' => TRUE
                        ),
                        'repeat_last_100' => array(
                            'type' => 'INT',
                            'constraint' => '2',
                            'unsigned' => TRUE
                        ),
                        'lottery_id' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
            ));
            $this->dbforge->add_key('lottery_id');
            $this->dbforge->create_table('lottery_stats');
        }
        public function down()
        {
                $this->dbforge->drop_table('lottery_prizes');
        }
}