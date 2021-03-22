<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Raw_Articles extends CI_Migration {

	public function up()
	{
		$field = $field = (array(
			'raw' => array(
			'type' => 'TEXT'
			)
	));
			
        $this->dbforge->add_column('articles', $field);
    }

	public function down()
	{
		$this->dbforge->drop_column('articles', 'raw');
	}
}