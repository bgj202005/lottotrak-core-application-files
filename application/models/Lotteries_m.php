<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lotteries_m extends MY_Model
{
	protected $_table_name = 'lottery_profiles';
	protected $_order_by = 'id';
	public $rules = array(
        'lottery_name' => array(
            'field' => 'lottery_name',
            'label' => 'Lottery Name',
            'rules' => 'trim|required|max_length[100]|callback__unique_lotteryname|xss_clean'
		),
		'lottery_image' => array(
            'field' => 'lottery_image',
            'label' => 'Lottery Image Upload',
            'rules' => 'callback__file_check'
		),
		'balls_drawn' => array(
			'field' => 'balls_drawn', 
			'label' => 'Balls Drawn', 
			'rules' => 'required|max_length[1]|integer|greater_than_equal_to[3]|less_than_equal_to[9]'
		), 
		'minimum_ball' => array(
			'field' => 'minimum_ball', 
			'label' => 'Minimum Ball', 
			'rules' => 'required|max_length[2]|integer|greater_than[0]|less_than_equal_to[10]'
		), 
		'maximum_ball' => array(  
			'field' => 'maximum_ball', 
			'label' => 'Maximum Ball', 
			'rules' => 'required|max_length[2]|integer|greater_than[11]|less_than_equal_to[54]'
		),
		'minimum_extra_ball' => array(
			'field' => 'minimum_extra_ball', 
			'label' => 'Lowest Extra Ball', 
			'rules' => 'max_length[2]|greater_than[0]|callback__extra_ball_set|integer'
		),
		'maximum_extra_ball' => array(
			'field' => 'maximum_extra_ball', 
			'label' => 'Hightest Extra Ball', 
			'rules' => 'max_length[2]|less_than_equal_to[54]|callback__extra_ball_set|integer'
		),
		'monday' => array(
			'field' => 'monday', 
			'label' => 'Monday', 
			'rules' => 'callback__require_day_of_week_set'
		),
		'tuesday' => array(
			'field' => 'tuesday', 
			'label' => 'Tuesday', 
			'rules' => 'callback__require_day_of_week_set'
		),
		'wednesday' => array(
			'field' => 'wednesday', 
			'label' => 'Wednesday', 
			'rules' => 'callback__require_day_of_week_set'
		),
		'thursday' => array(
			'field' => 'thursday', 
			'label' => 'Thursday', 
			'rules' => 'callback__require_day_of_week_set'
		),
		'friday' => array(
			'field' => 'friday', 
			'label' => 'Friday', 
			'rules' => 'callback__require_day_of_week_set'
		),
		'saturday' => array(
			'field' => 'saturday', 
			'label' => 'Saturday', 
			'rules' => 'callback__require_day_of_week_set'
		),
		'sunday' => array(
			'field' => 'sunday', 
			'label' => 'sunday', 
			'rules' => 'callback__require_day_of_week_set'
		)
	);

	public function get_new ()
	{
		$lottery = new stdClass();
		$lottery->id = NULL;
		$lottery->lottery_name = '';
		$lottery->lottery_description = '';
		$lottery->lottery_image = '';
		$lottery->lottery_country_id = '';
		$lottery->lottery_state_prov = '';
		$lottery->balls_drawn = 0;
		$lottery->minimum_ball = 0;
		$lottery->maximum_ball = 0;
		$lottery->extra_ball = 0; // 0 = False 1 = True
		$lottery->duplicate_extra = 0; // 0 = False 1 = True
		$lottery->minimum_extra_ball = 0;
		$lottery->maximum_extra_ball = 0;
		$lottery->days = array(
			'sunday' => 0,
			'monday' => 0,
			'tuesday' => 0,
			'wednesday' => 0,
			'thursday' => 0,
			'friday' => 0,
			'saturday' => 0
		);
		$lottery->last_draw_date = '';
		return $lottery;
	}
	
	
	public function delete ($id)
	{
		// Delete a page
		parent::delete($id);
		
		// Reset parent ID for its children
		$this->db->set(array(
			'parent_id' => 0
		))->where('parent_id', $id)->update($this->_table_name);
	}

	/**
	 * Creates or Updates existing Lottery Database
	 * 
	 * @param       $_POST values
	 * @return     none
	 */
	public function create_lottery_db($post_fields) 
	{
		// 1. Convert Lottery Name to a Table Name, Replaces any spaces with underscores.
		$name_counter = 0;  // if it does exist, a unique identifier will be added even though the name is the same 
		$lotto_name = $this->lotto_table_convert($post_fields['lottery_name']);
		// 2. Check for existing table name, if exists, add a number at end of name with underscore e.g. lotto_1
		$exists = $this->lotto_table_exists($lotto_name);
		do {
		if ($exists) {
			$name_counter++;
			$lotto_name .= $lotto_name.strval($name_counter);
		}
		// 3. If does not exist, create table
		// 4. Create Starting Date of first Draw
		// 5. Create fields for each ball based on the number drawn
		// 6. Create field for extra ball (bonus)
		$exists = $this->create_lotto_table_fields($lotto_name, $post_fields['balls_drawn'], $post_fields['extra_ball']);
		} while (!$exists);
	}
	
	/**
	 * Convert Lottery Name to Table name conversion
	 * 
	 * @param       $str	Corresponding Lottery Name
	 * @return     	$str replaced with underscores for spaces
	 */
	public function lotto_table_convert($str)
	{
		return str_replace(' ', '_', $str);
	}
	
	/**
	 * If existing Lottery table exists
	 * 
	 * @param       $lotto_tbl	Lottery Table Name
	 * @return     	TRUE/FALSE	TRUE (if exists), FALSE (if does not exist, go ahead and create)
	 */
	public function lotto_table_exists($lotto_tbl)
	{
	    return $this->db->table_exists($lotto_tbl);
	}

	/**
	 * Create Lotto Table and Fields
	 * 
	 * @param       $lotto_tbl, $balls_drawn, $extra	Lottery Table Name, , Balls Drawn, Extra Ball (TRUE / FALSE)
	 * @return     	TRUE/FALSE	TRUE (if table created), FALSE (error, table not created)
	 */
	public function create_lotto_table_fields($lotto_tbl, $balls_drawn, $extra) 
	{
		// Query Builder, Create Table Query with Tablename,  Add Field 1 ... Field N from Balls Drawn
		// If Extra, Add Field Name Extra
		// Execute Create Table
		// If False, add a number to table name, create tabke again
		// Exit on True, Table Created
		$sql = "CREATE TABLE `".$lotto_tbl."` (`id` INT(11) unsigned NOT NULL AUTO_INCREMENT, ";
		$count_r=1;
		do {
			// Add the number of fields cooresponding to the number of balls drawn
			$sql .= "`ball".$count_r."` INT(2) unsigned, ";
			$count_r++;
			//$sql .= (($count_r!=$balls_drawn) ? ", " : " ");
		} while($count_r<=$balls_drawn);

		if ($extra) 
		{
			// If a bonus / extra ball is included
			$sql .= "`extra` TINYINT(1) unsigned,";
		}
		// Final Query Builder Requirement for Foreign key as lottery_id
		$sql .= "`draw_date` DATE, `lottery_id` INT(11) unsigned NOT NULL COMMENT 'foreign key', PRIMARY KEY (`id`), FOREIGN KEY (`lottery_id`) 
		REFERENCES lottery_profiles(`id`));";
		
		$result = $this->db->query($sql);
	return $result;
	}

}