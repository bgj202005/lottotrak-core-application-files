<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration to remove the 'wins' field from lottery_nonfollowers table
 * The wins field is no longer required as nonfollowers don't track win statistics
 */
class Migration_Remove_Wins_From_Lottery_Nonfollowers extends CI_Migration {
    
    public function up()
    {
        // Check if the 'wins' column exists before attempting to drop it
        if ($this->db->field_exists('wins', 'lottery_nonfollowers')) {
            $this->dbforge->drop_column('lottery_nonfollowers', 'wins');
            log_message('info', 'Migration: Removed wins column from lottery_nonfollowers table');
        } else {
            log_message('info', 'Migration: wins column does not exist in lottery_nonfollowers table, skipping');
        }
    }
    
    public function down()
    {
        // Rollback: Re-add the 'wins' column if migration needs to be reversed
        if (!$this->db->field_exists('wins', 'lottery_nonfollowers')) {
            $fields = array(
                'wins' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '200',
                    'null' => TRUE,
                    'default' => NULL
                )
            );
            $this->dbforge->add_column('lottery_nonfollowers', $fields);
            log_message('info', 'Migration: Restored wins column to lottery_nonfollowers table');
        }
    }
}
