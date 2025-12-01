<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration to remove the 'wins' field from lottery_nonfollowers table
 * The wins field is no longer required as nonfriends don't track win statistics
 */
class Migration_Remove_Wins_From_Lottery_Nonfriends extends CI_Migration {
    
    public function up()
    {
        // Check if the 'wins' column exists before attempting to drop it
        if ($this->db->field_exists('wins', 'lottery_nonfriends')) {
            $this->dbforge->drop_column('lottery_nonfriends', 'wins');
            log_message('info', 'Migration: Removed wins column from lottery_nonfriends table');
        } else {
            log_message('info', 'Migration: wins column does not exist in lottery_nonfriends table, skipping');
        }
    }
    
    public function down()
    {
        // Rollback: Re-add the 'wins' column if migration needs to be reversed
        if (!$this->db->field_exists('wins', 'lottery_nonfriends')) {
            $fields = array(
                'wins' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '200',
                    'null' => TRUE,
                    'default' => NULL
                )
            );
            $this->dbforge->add_column('lottery_nonfriends', $fields);
            log_message('info', 'Migration: Restored wins column to lottery_nonfriends table');
        }
    }
}
