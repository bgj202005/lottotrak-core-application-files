<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Validation_Expiry_To_Members extends CI_Migration {
    
    public function up()
    {
        // Add validation_expiry column to members table
        $field = array(
            'validation_expiry' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
                'default' => NULL
            ),
        );
        
        $this->dbforge->add_column('members', $field, 'terms_agreement');
        
        // Update existing inactive members to have expiry dates (5 days from registration)
        $sql = "UPDATE `members` SET `validation_expiry` = DATE_ADD(`reg_time`, INTERVAL 5 DAY) 
                WHERE `validation_expiry` IS NULL AND `member_active` = 0 AND `reg_time` IS NOT NULL";
        $this->db->query($sql);
        
        echo "Migration: Added validation_expiry column to members table and updated existing records.\n";
    }
    
    public function down()
    {
        // Remove the validation_expiry column
        $this->dbforge->drop_column('members', 'validation_expiry');
        
        echo "Migration: Removed validation_expiry column from members table.\n";
    }
}