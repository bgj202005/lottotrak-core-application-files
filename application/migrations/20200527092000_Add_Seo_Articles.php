<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Seo_Articles extends CI_Migration {

	public function up()
	{
		$fields = (array(
				'description' => array(
						'type' => 'VARCHAR',
						'constraint' => 80
				),
				'canonical' => array(
					'type' => 'TINYINT',
					'constraint' => 1,
                    'unsigned' => TRUE,
                    'default'    => 0,
                    'null'	   => FALSE
				)
		));
		
        $this->dbforge->add_column('articles', $fields);
    }

	public function down()
	{
		$this->dbforge->drop_column('articles', 'description');
		$this->dbforge->drop_column('articles', 'canonical');
	}
}