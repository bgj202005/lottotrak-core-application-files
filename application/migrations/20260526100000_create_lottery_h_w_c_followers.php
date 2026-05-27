<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_lottery_h_w_c_followers extends CI_Migration {

	public function up()
	{
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `lottery_h_w_c_followers` (
				`id`                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`lottery_id`            INT UNSIGNED NOT NULL,
				`h_w_c_group`           VARCHAR(50)  CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
				`follower_type`         VARCHAR(50)  CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
				`ball_points`           VARCHAR(11)  CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
				`position_points`       VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
				`lottery_numbers`       VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
				`prev_lottery_numbers`  VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `uq_lottery_h_w_c_followers_lottery` (`lottery_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
		");
	}

	public function down()
	{
		$this->db->query("DROP TABLE IF EXISTS `lottery_h_w_c_followers`");
	}
}
