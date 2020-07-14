<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Imports extends CI_Migration {

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
                        'column' => array(
                                'type' => 'TEXT'
                        ),
                        'header' => array(
                                'type' => 'TEXT'
                        ),
	        	'zero_extra' => array(
	        		'type' => 'TINYINT',
				'constraint' => 1,
                                'unsigned' => TRUE,
                                'default'    => 0,
                                'null'	   => FALSE
	        	),        			
        		'cvs_file' => array(
                                'type' => 'VARCHAR',
                                'constraint' => 100
                        ),
                        'cvs_url' => array(
                                'type' => 'VARCHAR',
                                'constraint' => 100
                        ),
                        'lottery_id' => array(
                                'type' => 'INT',
                                'constraint' => '11',
                                'unsigned' => TRUE
                        )
                ));
                $this->dbforge->add_key('lottery_id');
                $this->dbforge->create_table('import_data');
        }

        public function down()
        {
                $this->dbforge->drop_table('import_data');
        }
}