<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Lottery_Followers extends CI_Migration {

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
                        'lottery_followers' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '255'
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
                    )
                ));
                $this->dbforge->add_key('draw_id'); // Non-primary and foreign key
                $this->dbforge->add_key('lottery_id'); // Non-primary and foreign key
                $this->dbforge->create_table('lottery_followers');
        }

        public function down()
        {
                $this->dbforge->drop_table('lottery_followers');
        }
}