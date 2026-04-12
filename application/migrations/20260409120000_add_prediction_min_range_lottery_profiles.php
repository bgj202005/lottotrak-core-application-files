<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add prediction_min_range to lottery_profiles
 * 
 * Adds a new field to allow configurable prediction start ranges.
 * Options: 25 (50 draws), 50 (100 draws), or 100 (200 draws required)
 * 
 * @author  LottoTrak Development Team
 * @version 1.0.0
 * @date    2026-04-09
 */
class Migration_Add_Prediction_Min_Range_Lottery_Profiles extends CI_Migration {
    
    /**
     * Migration UP - Add prediction_min_range column
     */
    public function up()
    {
        $fields = array(
            'prediction_min_range' => array(
                'type' => 'INT',
                'constraint' => 3,
                'unsigned' => TRUE,
                'default' => 100,
                'null' => FALSE,
                'comment' => 'Minimum range for predictions (25, 50, or 100). Determines min draws required: range × 2',
                'after' => 'maximum_extra_ball'
            ),
        );
        
        $this->dbforge->add_column('lottery_profiles', $fields);
        
        // Log the migration
        log_message('info', 'Migration: Added prediction_min_range column to lottery_profiles table');
        
        // Update existing records to have default value of 100
        $this->db->set('prediction_min_range', 100);
        $this->db->where('prediction_min_range IS NULL', NULL, FALSE);
        $this->db->update('lottery_profiles');
        
        log_message('info', 'Migration: Updated existing lottery profiles with default prediction_min_range=100');
    }
    
    /**
     * Migration DOWN - Remove prediction_min_range column
     */
    public function down()
    {
        $this->dbforge->drop_column('lottery_profiles', 'prediction_min_range');
        
        log_message('info', 'Migration: Removed prediction_min_range column from lottery_profiles table');
    }
}
