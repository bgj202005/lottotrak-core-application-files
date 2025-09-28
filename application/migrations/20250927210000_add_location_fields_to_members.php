<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Location_Fields_To_Members extends CI_Migration {
    
    public function up()
    {
        // Add location fields to members table
        $fields = array(
            'ip_address_readable' => array(
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => TRUE,
                'comment' => 'Human readable IP address'
            ),
            'location_city' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
                'comment' => 'Detected city based on IP'
            ),
            'location_region' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
                'comment' => 'Detected region/state based on IP'
            ),
            'location_country' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
                'comment' => 'Detected country based on IP'
            ),
            'location_country_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '2',
                'null' => TRUE,
                'comment' => 'Detected country code based on IP'
            ),
            'location_detected_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
                'comment' => 'When the location was last detected'
            )
        );
        
        $this->dbforge->add_column('members', $fields);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('members', 'ip_address_readable');
        $this->dbforge->drop_column('members', 'location_city');
        $this->dbforge->drop_column('members', 'location_region');
        $this->dbforge->drop_column('members', 'location_country');
        $this->dbforge->drop_column('members', 'location_country_code');
        $this->dbforge->drop_column('members', 'location_detected_at');
    }
}