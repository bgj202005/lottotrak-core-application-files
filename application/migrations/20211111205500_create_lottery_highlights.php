<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Lottery_Highlights extends CI_Migration {

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
                        'range' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        'trends' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '2000'
                        ),
                        'repeats' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '2000'
                        ),
                        'consecutives' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '2000'
                        ),
                        'adjacents' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '500'
                        ),
                        'winning_sums' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '2000'
                        ),
                        'winning_digits' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '2000'
                        ),
                        'number_range' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '2000'
                        ),
                        'parity' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '2000'
                        ),
                        'draw_id' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        'lottery_id' => array(
                             'type' => 'INT',
                             'constraint' => '11',
                             'unsigned' => TRUE
                        ),
                        'extra_included' => array(
                            'type' => 'INT',
                            'constraint' => 1,
                            'unsigned' => FALSE,
                            'default' => 0
                        ),
                        'extra_draws' => array(
                            'type' => 'INT',
                            'constraint' => 1,
                            'unsigned' => FALSE,
                            'default' => 0
                        )
                ));
                $this->dbforge->add_key('draw_id'); // Non-primary and foreign key
                $this->dbforge->add_key('lottery_id'); // Non-primary and foreign key
                $this->dbforge->create_table('lottery_highlights');
        }

        public function down()
        {
                $this->dbforge->drop_table('lottery_highlights');
        }
}