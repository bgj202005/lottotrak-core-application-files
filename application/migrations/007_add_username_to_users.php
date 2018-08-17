<?php
class Migration_add_username_to_users extends CI_Migration {

	public function up()
	{
		$fields  = (array(
				'username' => array(
						'type' => 'VARCHAR',
						'constraint' => 255,
						'null'	=> FALSE
				),
		));
		$this->dbforge->add_column('users', $fields);
	}

	public function down()
	{
		$this->dbforge->drop_column('users', 'username');
	}
}