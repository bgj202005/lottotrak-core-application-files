<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Lottery_H_w_c extends CI_Migration {

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
                        'hots' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '1000'
                        ),
                        'warms' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '1000'
                        ),
                        'colds' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '1000'
                        ),
                        'overdue' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '500'
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
                $this->dbforge->create_table('lottery_h_w_c');
        }

        public function down()
        {
                $this->dbforge->drop_table('lottery_h_w_c');
        }
}