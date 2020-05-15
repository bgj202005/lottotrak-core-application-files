<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class  Migration_Menu_Item_To_Pages extends CI_Migration {

	public function up()
	{
		$field  = (array(
		        'menu_item' => array(
		                  'type'       => 'TINYINT',
		                  'constraint' => 1,
                          'unsigned'   => TRUE,
                          'default'     => 0
		    )
		));
		$this->dbforge->add_column('pages', $field);
	}

	public function down()
	{
		$this->dbforge->drop_column('pages', 'menu_item');
	}
}