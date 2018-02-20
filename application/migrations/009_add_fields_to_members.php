<?php
class Migration_Add_fields_to_members extends CI_Migration {

	public function up()
	{
		$fields  = (array(
				'username' => array(
						'type' => 'VARCHAR',
						'constraint' => 255,
						'null'	=> FALSE
				),
				'reg_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
		));
		$this->dbforge->add_column('members', $fields);
	}

	public function down()
	{
		$this->dbforge->drop_column('members', 'username');
		$this->dbforge->drop_column('members', 'reg_time');
	}
}