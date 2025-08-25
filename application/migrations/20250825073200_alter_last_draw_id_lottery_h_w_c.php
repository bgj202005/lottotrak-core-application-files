<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_Last_Draw_Id_Lottery_H_w_c extends CI_Migration {
    
    public function up()
    {
        $field = array(
        'last_draw_id' => array(
         'name' => 'id_last',
         'type' => 'INT',
        ),
    );
    $this->dbforge->modify_column('lottery_h_w_c', $field);
    }
    public function down()
    {
        $this->dbforge->drop_column('lottery_h_w_c', 'last_draw_id');
    }
}