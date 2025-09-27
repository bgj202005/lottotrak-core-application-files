<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Terms_Agreement_To_Members extends CI_Migration {

	public function up()
	{
		$fields = array(
			'terms_agreement' => array(
				'type' => 'BOOLEAN',
				'null' => TRUE,
				'default' => NULL,
				'comment' => 'User agreement to terms of service: TRUE=agreed, FALSE=declined, NULL=not yet asked'
			)
		);
		$this->dbforge->add_column('members', $fields);
	}

	public function down()
	{
		$this->dbforge->drop_column('members', 'terms_agreement');
	}
}