<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_Firstdate_And_Lastdate_To_Lottery_Profiles extends CI_Migration {
    
    public function up()
    {
        $field = (array(
            'firstdate' => array(
						'type' => 'DATE',
                        'null' => TRUE, 
                        'after' => 'sunday'
            ),          
             'lastdate' => array(
						'type' => 'DATE',
                        'null' => TRUE           
            )
        ));
        $this->dbforge->add_column('lottery_profiles', $field);
    }
    public function down()
    {
        $this->dbforge->drop_column('lottery_profiles', 'firstdate');
        $this->dbforge->drop_column('lottery_profiles', 'lastdate');
    }
}