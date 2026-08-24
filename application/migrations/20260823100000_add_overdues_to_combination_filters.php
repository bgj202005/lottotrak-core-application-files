<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_overdues_to_combination_filters extends CI_Migration {

	public function up()
	{
		// Add overdues column to lottery_combination_filters table
		$this->dbforge->add_column('lottery_combination_filters', array(
			'overdues' => array(
				'type'			=> 'VARCHAR',
				'constraint'	=> '10',
				'null'			=> TRUE,
				'default'		=> NULL,
				'after'			=> 'winning_digits',
				'comment'		=> 'Overdues filter: ALL, 0, 1, 2, or 3'
			)
		));
	}

	public function down()
	{
		// Remove overdues column
		$this->dbforge->drop_column('lottery_combination_filters', 'overdues');
	}
}
