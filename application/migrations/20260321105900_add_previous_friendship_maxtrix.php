<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Previous_Friendship_Maxtrix extends CI_Migration {

    public function up()
    {
        $fields = array(
            'friendship_matrix' => array(
                'type' => 'LONGTEXT',
                'null' => TRUE,
                'after' => 'lottery_friends'
            ),
        );
        $this->dbforge->add_column('lottery_friends', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('lottery_friends', 'friendship_matrix');
    }
}
