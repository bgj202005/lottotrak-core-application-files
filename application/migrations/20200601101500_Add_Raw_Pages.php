<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Raw_Pages extends CI_Migration {

	public function up()
	{
		$field = (array(
				'raw' => array(
				'type' => 'TEXT'
				)
		));
		
        $this->dbforge->add_column('pages', $field);
    }

	public function down()
	{
		$this->dbforge->drop_column('pages', 'raw');
	}
}