<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_prev_hwc_predictions_lottery_h_w_c extends CI_Migration {

	public function up()
	{
		// Add prev_h_w_c_predictions VARCHAR(1000) after hwc_predictions
		$this->db->query("ALTER TABLE `lottery_h_w_c` ADD COLUMN `prev_h_w_c_predictions` VARCHAR(1000) NOT NULL DEFAULT '' AFTER `hwc_predictions`");
	}

	public function down()
	{
		$this->db->query("ALTER TABLE `lottery_h_w_c` DROP COLUMN `prev_h_w_c_predictions`");
	}
}
