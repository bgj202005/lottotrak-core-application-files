<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_hwc_predictions_lottery_h_w_c extends CI_Migration {

	public function up()
	{
		// Add hwc_option: 1 = Top Ranked / Count, 2 = Manual Selected
		$this->dbforge->add_column('lottery_h_w_c', array(
			'hwc_option' => array(
				'type'			=> 'INT',
				'constraint'	=> 1,
				'default'		=> 1,
				'null'			=> FALSE,
				'after'			=> 'prediction_pool'
			)
		));

		// Add hwc_select: rank index (1-based) of the manually selected H-W-C group
		$this->dbforge->add_column('lottery_h_w_c', array(
			'hwc_select' => array(
				'type'			=> 'INT',
				'constraint'	=> 11,
				'default'		=> 1,
				'null'			=> FALSE,
				'after'			=> 'hwc_option'
			)
		));

		// Add hwc_predictions: comma-separated generated predicted numbers
		$this->dbforge->add_column('lottery_h_w_c', array(
			'hwc_predictions' => array(
				'type'			=> 'VARCHAR',
				'constraint'	=> 1000,
				'default'		=> '',
				'null'			=> FALSE,
				'after'			=> 'hwc_select'
			)
		));
	}

	public function down()
	{
		$this->dbforge->drop_column('lottery_h_w_c', 'hwc_predictions');
		$this->dbforge->drop_column('lottery_h_w_c', 'hwc_select');
		$this->dbforge->drop_column('lottery_h_w_c', 'hwc_option');
	}
}
