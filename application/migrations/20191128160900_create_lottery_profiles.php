<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Lottery_Profiles extends CI_Migration {

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
        	            'lottery_name' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '100'
                        ),
                        'lottery_description' => array(
                            'type' => 'TEXT',
                            'null' => TRUE
                        ),
                        'lottery_image' => array(
        	                'type'       => 'VARCHAR',
                            'constraint' => '255',
                            'null'       => TRUE
                        ),
                        'lottery_country_id' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '2'
                        ),
                        'lottery_state_prov' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '2'
                        ),
                        'balls_drawn' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
        	            'minimum_ball' => array(
        	                'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
        	            ),
        	            'maximum_ball' => array(
                            'type' => 'INT',
                             'constraint' => '11',
                             'unsigned' => TRUE
                        ),
                        'extra_ball' => array(
                                'type' => 'TINYINT',
                                'constraint' => '1',
                                'unsigned' => TRUE
                        ),
                        'extra_balls_drawn' => array(
        	                'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
        	           ),
                        'minumum_extra_ball' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        'maximum_extra_ball' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        ),
                        'duplicate_extra_ball' => array(
                            'type' => 'TINYINT',
                            'constraint' => '1',
                            'unsigned' => TRUE
                    ),
                ));
                $this->dbforge->create_table('lottery_profiles');
        }

        public function down()
        {
                $this->dbforge->drop_table('lottery_profiles');
        }
}