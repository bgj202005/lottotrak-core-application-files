<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Registered_Members extends CI_Migration {

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
        	           'first_name' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '100'
        	           ),
        	           'last_name' => array(
        	                'type' => 'VARCHAR',
        	                'constraint' => '100'
        	           ),
        	           'email' => array(
                                'type' => 'VARCHAR',
                                'constraint' => '100'
                        ),
                        'password' => array(
                                'type' => 'VARCHAR',
                                'constraint' => '128'
                        ),
                ));
                $this->dbforge->create_table('members');
        }

        public function down()
        {
                $this->dbforge->drop_table('members');
        }
}