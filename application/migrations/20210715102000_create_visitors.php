<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Visitors extends CI_Migration {

        public function up()
        {
        	$this->dbforge->add_field(array(
                        'active_count' => array(
                                'type' => 'INT',
                                'constraint' => '11',
                                'unsigned' => FALSE,
                                'null'  => FALSE,
                                'default' => 0
                        )
                ));
                $this->dbforge->create_table('visitors');
                $this->db->insert('visitors', array('active_count' => '0')); // Set Value to Online
        }

        public function down()
        {
                $this->dbforge->drop_table('visitors');
        }
}