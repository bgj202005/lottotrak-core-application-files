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
		// Delete a lottery profile and database
		parent::delete($id);
		
		// Delete Entire Database (if Exists)
		
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
		return strtolower(str_replace(' ', '_', $str));
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
	/**
	 * Returns Last Drawn Numbers from the Lottery Name
	 * 
	 * @param       $lottery_name		Corresponding Lottery Name
	 * @return     	$last_drawn (object), 'nodraws' (string) or FALSE (If Table does not Exist) 
	 */
	public function last_draw_db($lottery_name)
	{
		$lottery_name = $this->lotto_table_convert($lottery_name); // Converts with Underscores

		if ($this->lotto_table_exists($lottery_name))
		{
			$sql = "SELECT * FROM `".$lottery_name."` WHERE `draw_date` IN (SELECT MAX(`draw_date`) FROM `".$lottery_name."`) LIMIT 1";
			$result = $this->db->query($sql);
			
			$row = $result->row();
			return ($result->num_rows() === 1) ? $row : 'nodraws';
		}
	return FALSE;
	}

	/**
	 * Takes the CSV Row and Queries the database to input the field values
	 * 
	 * @param       $tbl_name (string), $db_values (array)  -- Corresponding Lottery Name and Draw Data Details
	 * @return     	TRUE / FALSE			-- SUCCESS / FAIL for INSERT Lottery Draw */
	public function csv_array_to_query($tbl_name, $db_values)
	{
		foreach ($db_values as $key => $value) {
			if(empty($value)) return FALSE;
		}

		$str = $this->db->insert_string($tbl_name, $db_values);

		return $this->db->simple_query($str);	// Return TRUE on insert OK or FALSE on failure of insert
	}

	/**
	 * Returns the current count of rows that have been imported from the CSV File
	 * 
	 * @param	int	$table			id of Lottery Table
	 * @return  bool 				SUCCESS / FAIL for adding key/value pairs
	 */
	public function db_row_count($table)
	{
		return $this->db->count_all($table);
	}

	/**
	 * Returns True (for range OK), or False (Out of Bounds Error) in ranges of Lottery Numbers (e.i. 1 to 49)
	 * 
	 * @param       obj	$range_values	array of objects of range values for lottery parameters 	
	 * @param		arr $data_values	array of values from current draw being imported
	 * @return     	TRUE/FALSE			SUCCESS (TRUE) on draw ranges successful or FAIL (FALSE) on a number out of range
	 */
	public function range_check($range_values, $data_values)
	{
		$drawn = $range_values->balls_drawn;
		$max_ball = $range_values->maximum_ball;
		$min_ball = $range_values->minimum_ball;
		
		$ball = 1;
		do 
		{
			if (($data_values['ball'.$ball]<$min_ball)||($data_values['ball'.$ball]>$max_ball)) return FALSE;
			$ball++;
			$drawn--;
		} 
		while($drawn>0);

		$extra = $range_values->extra_ball;

		if ($extra)		//Extra as part of the game
		{
			$drawn = $range_values->balls_drawn;
			$min_extra = $range_values->minimum_extra_ball;
			$max_extra = $range_values->maximum_extra_ball;
			$duplicate = $range_values->duplicate_extra_ball;
			if (($data_values['extra']<$min_extra)||($data_values['extra']>$max_extra)) return FALSE;
			if (!$duplicate)
			{
				$drawn = $range_values->balls_drawn;
				$ball = 1;
				do 
				{
					if ($data_values['ball'.$ball]==$data_values['extra']) return FALSE;
					$ball++;
					$drawn--;
				} 
				while($drawn>0);
			} 
		}
	return TRUE; // All Good!
	}

	/* Returns the next draw date based on the days of the draw
	* 
	* @param	arr	Data object of set days of week
	* @param 	str	$current
	* @param 	str $last_draw
	* @return   str	Next Draw Date 
	*/
	public function next_date($days, $current, $last_draw)
	{
		$offset = 0;	// Counter to find out how many days until next draw day
		$found = FALSE;
		$day_select = array(
						'monday' => $days->monday,
						'tuesday' => $days->tuesday,
						'wednesday' => $days->wednesday,
						'thursday' => $days->thursday,
						'friday' => $days->friday,
						'saturday' => $days->saturday,
						'sunday'	=> $days->sunday
		);
		// Find the current draw day in the array, move the pointer
		// Find the current day of the last draw, do not update the offset
		while($current!=key($day_select))
		{
			next($day_select);
		}

		next($day_select);
		
		while (!$found)
		{
			if(!current($day_select))
			{
				$offset++;
				(key($day_select)=='sunday' ? reset($day_select) : next($day_select));
			}
			else
			{	
				$offset++;
				$found = TRUE;
			}
		}
		return date('l M d, Y', strtotime($last_draw.' + '.$offset.' days')); // Returns, for example, Thursday 23rd April 2020
	}
	
	/* Load Draws with Draw Number
	* 
	* @param	int	$lottery_id
	* @param 	str	$name
	* @return   bool TRUE/FALSE 
	*/
	
	public function load_draws($table, $lottery_id)
	{
		$this->db->query('SET @draw_number = 0; '); // Add a Draw Number to the Draw List
		$query = $this->db->query('SELECT *, 
				(@draw_number:=@draw_number + 1) AS draw 
				FROM '.$table.' WHERE lottery_id='.$lottery_id.' 
				ORDER BY draw_date ASC;');					
		
	return $query->result(); 	// Return the Draw History
	}

	/* Insert Draw with Draw Number
	* 
	* @param	str	$table		Current table of the Lottery Draws
	* @param 	arr	$data		key / value pairs of new draw to be inserted
	* @return   int				returns the next ID number for the record	 
	*/
	public function insert_draw($table, $data)
	{
		$this->db->set($data);		// Set the query with the key / value pairs
		$this->db->insert($table);	// Query insert a new record
	return $this->db->insert_id();	// Return the latest id
	}

	/* Update Draw()s with Draw Number(s)
	* 
	* @param	str	$table		Current table of the Lottery Draws
	* @param 	arr	$data		key / value pairs of new draw to be inserted
	* @return   bool			TRUE / FALSE, All Records successfully updated / A Record failed to update
	*/
	public function update_draws($table, $data)
	{
		$this->db->where('id', $data['id']);
	return $this->db->update($table, $data);
	}

	/* Move Post Values to Update Array for Editting Draw(s)
	* 
	* @param	obj	$data		Current table of the Lottery Draws
	* @param 	int	$drawn		key / value pairs of new draw to be inserted
	* @param	bool $extra		If lottery has an extra ball
	* @return   bool			TRUE / FALSE, All Records successfully updated / A Record failed to update
	*/
	public function update_from_post($data, $tbl, $drawn, $extra)
	{
		// Draw Updates need to be passed like this ->
		// 0 => id, ball1, ball2, ball3, ball4, ball5, ball6, extra, lottery_id
		// 1 => id, ball1, ball2, ball3, ball4, ball5, ball6, extra, lottery_id

			$c = count($data['draw[]']);
			$ld = array();

			for ($i = 0; $i < $c; $i++) 
			{
				$ld['id'] = $data['draw[]'][$i];
				$ld['ball1'] = $data['ball_1[]'][$i];
				$ld['ball2'] = $data['ball_2[]'][$i];
				$ld['ball3'] = $data['ball_3[]'][$i];
				if(intval($drawn)>=4) $ld['ball4'] = $data['ball_4[]'][$i];
				if(intval($drawn)>=5) $ld['ball5'] = $data['ball_5[]'][$i];
				if(intval($drawn)>=6) $ld['ball6'] = $data['ball_6[]'][$i];
				if(intval($drawn)>=7) $ld['ball7'] = $data['ball_7[]'][$i];
				if(intval($drawn)>=8) $ld['ball8'] = $data['ball_8[]'][$i];
				if(intval($drawn)>=9) $ld['ball9'] = $data['ball_9[]'][$i];
				if ($extra) $ld['extra'] = $data['extra[]'][$i];
				$ld['lottery_id'] = $data['lottery_id'];
				$success = $this->update_draws($tbl, $ld);
				unset($ld); 
			}
		return $success;	
	}
}