<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Days_To_Lottery_Profiles extends CI_Migration {
    
    public function up()
    {
        $field = (array(
            'monday' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'unsigned' => TRUE
            ),
            'tuesday' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'unsigned' => TRUE
            ),
            'wednesday' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'unsigned' => TRUE
            ),
            'thursday' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'unsigned' => TRUE
            ),
            'friday' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'unsigned' => TRUE
            ),
            'saturday' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'unsigned' => TRUE
            ),
            'sunday' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'unsigned' => TRUE
            )
        ));
        $this->dbforge->add_column('lottery_profiles', $field);
    }
    
    public function down()
    {
        $this->dbforge->drop_column('lottery_profiles', 'monday');
        $this->dbforge->drop_column('lottery_profiles', 'tuesday');
        $this->dbforge->drop_column('lottery_profiles', 'wednesday');
        $this->dbforge->drop_column('lottery_profiles', 'thurday');
        $this->dbforge->drop_column('lottery_profiles', 'friday');
        $this->dbforge->drop_column('lottery_profiles', 'saturday');
        $this->dbforge->drop_column('lottery_profiles', 'sunday');
    }
}