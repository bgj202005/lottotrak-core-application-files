<?php
class Migration_Add_City_Stateprov_Country_To_Members extends CI_Migration {

	public function up()
	{
		$fields  = (array(
				'city' => array(
						'type'       => 'VARCHAR',
						'constraint' => 100,
						'null'	     => FALSE
				),
		        'state_prov' => array(
		                 'type'       => 'VARCHAR',
		                 'constraint' => 155,
		                  'null'	  => FALSE
		        ),
		        'country' => array(
		                  'type'       => 'INTEGER',
		                  'constraint' => 10,
		                  'null'	   => FALSE
		    )
		));
		$this->dbforge->add_column('members', $fields);
	}

	public function down()
	{
		$this->dbforge->drop_column('members', 'city');
		$this->dbforge->drop_column('members', 'state_prov');
		$this->dbforge->drop_column('members', 'country');
	}
}