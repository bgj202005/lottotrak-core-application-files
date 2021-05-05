<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_Lottery_Combination_Files extends CI_Migration {

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
                        'file_name' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '10',
                            'unsigned' => TRUE,
                            'comment' => 'text file without .txt extention'
                        ),
                        'N' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => 'Balls to Predict (N)'
                        ),
                        'R' => array(
                            'type' => 'INT',
                            'constraint' => '2',
                            'unsigned' => TRUE,
                            'comment' => 'Pick Game (R)'
                        ),
                        'CCCC' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE,
                            'comment' => 'Combinations Result C(N,R)'
                        ),
                       'lottery_id' => array(
                            'type' => 'INT',
                            'constraint' => '11',
                            'unsigned' => TRUE
                        )
            ));
            $this->dbforge->add_key('lottery_id');
            $this->dbforge->create_table('lottery_combination_files');
        }
        public function down()
        {
                $this->dbforge->drop_table('lottery_combination_files');
        }
}