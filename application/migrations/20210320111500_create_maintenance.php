<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Maintenance extends CI_Migration {

        public function up()
        {
        	$this->dbforge->add_field(array(
                        'maintenance' => array(
                                'type' => 'INT',
                                'constraint' => '1',
                                'unsigned' => FALSE,
                                'null'  => FALSE,
                                'default' => 0
                        )
                ));
                $this->dbforge->create_table('maintenance');
                $this->db->insert('maintenance', array('maintenance' => '0')); // Set Value to Online
        }

        public function down()
        {
                $this->dbforge->drop_table('maintenance');
        }
}