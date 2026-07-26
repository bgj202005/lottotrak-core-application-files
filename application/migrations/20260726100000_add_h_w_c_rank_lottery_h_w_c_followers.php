<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_h_w_c_rank_lottery_h_w_c_followers extends CI_Migration {

	public function up()
	{
		// Add h_w_c_rank column to store the rank number (1-based) selected by the administrator
		$fields = array(
			'h_w_c_rank' => array(
				'type'       => 'TINYINT',
				'constraint' => 3,
				'unsigned'   => TRUE,
				'null'       => FALSE,
				'default'    => 1,
				'after'      => 'h_w_c_group'
			)
		);
		
		if (!$this->db->field_exists('h_w_c_rank', 'lottery_h_w_c_followers')) {
			$this->dbforge->add_column('lottery_h_w_c_followers', $fields);
			log_message('info', 'Migration: Added h_w_c_rank column to lottery_h_w_c_followers');
		}
	}

	public function down()
	{
		if ($this->db->field_exists('h_w_c_rank', 'lottery_h_w_c_followers')) {
			$this->dbforge->drop_column('lottery_h_w_c_followers', 'h_w_c_rank');
			log_message('info', 'Migration: Dropped h_w_c_rank column from lottery_h_w_c_followers');
		}
	}
}
