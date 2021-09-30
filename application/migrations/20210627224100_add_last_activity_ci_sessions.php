<?php
class Migration_Add_Last_Activity_Ci_Sessions extends CI_Migration {

	public function up()
	{
		$field  = (array(
				'last_activity' => array(
				'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
                'default' => 0
			)));
		$this->dbforge->add_column('ci_sessions', $field);
	}

	public function down()
	{
		$this->dbforge->drop_column('ci_sessions', 'last_activity');
	}
}