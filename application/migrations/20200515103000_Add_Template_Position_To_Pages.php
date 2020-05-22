<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class  Migration_Add_template_position_to_pages extends CI_Migration {

	public function up()
	{
		$fields  = (array(
		        'position' => array(
		                  'type'       => 'VARCHAR',
						  'constraint' => '25',
		                  'null'	   => FALSE
		    )
		));
		$this->dbforge->add_column('pages', $fields);
	}

	public function down()
	{
		$this->dbforge->drop_column('pages', 'position');
	}
}