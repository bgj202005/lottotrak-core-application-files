<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_total_tickets_lottery_combination_filters extends CI_Migration {

	public function up()
	{
		$this->db->query("
			ALTER TABLE `lottery_combination_filters`
			ADD COLUMN `total_tickets` INT UNSIGNED NOT NULL DEFAULT 0
		");
	}

	public function down()
	{
		$this->db->query("
			ALTER TABLE `lottery_combination_filters`
			DROP COLUMN `total_tickets`
		");
	}
}
