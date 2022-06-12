<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statistics_m extends MY_Model
{
	protected $_table_name = 'lottery_stats';
	protected $_order_by = 'id';
	
	// Based on the lottery maximum range for the balls being drawn, miniumum ball = 11, maximum ball = 54 for any lottery created
	public $hwc_defaults = array(
				'11'	=>		'3-5-3',
				'12'	=>		'4-4-4',
				'13'	=>		'4-5-4',
				'14'	=>		'4-6-4',
				'15'	=>		'5-5-5',
				'16'	=>		'5-6-5',
				'17'	=>		'5-7-5',
				'18'	=>		'6-6-6',
				'19'	=>		'6-7-6',
				'20'	=>		'6-8-6',
				'21'	=>		'7-7-7',
				'22'	=>		'7-8-7',
				'23'	=>		'7-9-7',
				'24'	=>		'8-8-8',
				'25'	=>		'8-9-8',
				'26'	=>		'8-10-8',
				'27'	=>		'9-9-9',
				'28'	=>		'9-10-9',
				'29'	=>		'9-11-9',
				'30'	=>		'10-10-10',
				'31'	=>		'10-11-10',
				'32'	=>		'10-12-10',
				'33'	=>		'11-11-11',
				'34'	=>		'11-12-11',
				'35'	=>		'11-13-11',
				'36'	=>		'12-12-12',
				'37'	=>		'12-13-12',
				'38'	=>		'12-14-12',
				'39'	=>		'13-13-13',
				'40'	=>		'13-14-13',
				'41'	=>		'13-15-13',
				'42'	=>		'14-14-14',
				'43'	=>		'14-15-14',
				'44'	=>		'14-16-14',
				'45'	=>		'15-15-15',
				'46'	=>		'15-16-15',
				'47'	=>		'15-17-15',
				'48'	=>		'16-16-16',
				'49'	=>		'16-17-16',
				'50'	=>		'16-18-16',
				'51'	=>		'17-17-17',
				'52'	=>		'17-18-17',
				'53'	=>		'18-18-18',
				'54'	=>		'18-19-18'
	);
	public $hwc_heats = array(
				'3'		=>		'3-0-0,2-1-0,2-0-1,1-2-0,1-1-1,1-0-2,0-3-0,0-0-3',
				'4'		=>		'4-0-0,3-1-0,3-0-1,2-2-0,2-1-1,2-0-2,1-3-0,1-2-1,1-1-2,1-0-3,0-3-1,0-2-2,0-1-3,0-4-0,0-0-4',
				'5'		=>		'5-0-0,4-1-0,4-0-1,3-2-0,3-1-1,3-0-2,2-3-0,2-2-1,2-1-2,2-0-3,1-4-0,1-3-1,1-2-2,1-1-3,1-0-4,0-5-0,0-4-1,0-3-2,0-2-3,0-1-4,0-5-0,0-0-5',
				'6'		=>		'6-0-0,5-1-0,5-0-1,4-2-0,4-1-1,4-0-2,3-3-0,3-2-1,3-1-2,3-0-3,2-4-0,2-3-1,2-2-2,2-1-3,2-0-4,1-5-0,1-4-1,1-3-2,1-2-3,1-1-4,1-0-5,0-6-0,0-5-1,0-4-2,0-3-3,0-2-4,0-1-5,0-6-0,0-0-6',
				'7'		=>		'7-0-0,6-1-0,6-0-1,5-2-0,5-1-1,5-0-2,4-3-0,4-2-2,4-2-1,4-1-2,4-0-3,3-4-0,3-3-1,3-2-2,3-1-3,3-0-4,2-5-0,2-4-1,2-3-2,2-2-3,2-1-4,2-0-5,1-6-0,1-5-1,1-4-2,1-3-3,1-2-4,1-1-5,1-0-6,0-6-1,0-5-2,0-4-3,0-3-4,0-2-5,0-1-6,0-7-0,0-0-7',
				'8'		=>		'8-0-0,7-1-0,7-0-1,6-2-0,6-1-1,6-0-2,5-3-0,5-2-1,5-1-2,5-0-3,4-4-0,4-3-1,4-2-2,4-1-3,4-0-4,3-5-0,3-4-1,3-3-2,3-2-3,3-1-4,3-0-5,2-6-0,2-5-1,2-4-2,2-3-3,2-2-4,2-1-5,2-0-6,1-7-0,1-6-1,1-5-2,1-4-3,1-3-4,1-2-5,1-1-6,1-0-7,0-8-0,0-0-8',
				'9'		=>		'9-0-0,8-1-0,8-0-1,7-2-0,7-1-1,7-0-2,6-3-0,6-2-1,6-1-2,6-0-3,5-4-0,5-3-1,5-2-2,5-1-3,5-0-4,4-5-0,4-4-1,4-3-2,4-2-3,4-1-4,4-0-5,3-6-0,3-5-1,3-4-2,3-3-3,3-2-4,3-1-5,3-0-6,2-7-0,2-6-1,2-5-2,2-4-3,2-3-4,2-2-5,2-1-6,2-0-7,1-8-0,1-7-1,1-6-2,1-5-3,1-4-4,1-3-5,1-2-6,1-1-7,1-0-8,0-8-1,0-7-2,0-6-3,0-5-4,0-4-5,0-3-6,0-2-7,0-1-8,0-9-0,0-0-9'
	);

	/**
	 * Validates that statistics fields are available for each draw, otherwise return no statistic fields available
	 * 
	 * @param	string		$lottery		Current Lottery table name
	 * @return	boolean		TRUE / FALSE	Stats have been calculated (true), Stats do not exist (False)		
	 */
	public function lottery_stats_exist($lottery)
	{
		// Fields in the database to be compared, must be exactly 8
		$stats = array('sum_draw','sum_digits','even','odd', 'range_draw', 'repeat_decade', 'repeat_last');
		
		$i = 0;
		// Make a comparision, if 8 fields are found return TRUE, otherwise return FALSE
		foreach($stats as $stat)
		{
			if($this->db->field_exists($stat, $lottery))
			{
				++$i;
			} 
		}
			
	return ($i==7 ? TRUE : FALSE);	// Must be 7 fields or 0 Fields (returns FALSE!)
	}	
	
	/**
	 * Expand the columns on the lottery draws table in the database
	 * 
	 * @param	string	$table			Actual Name of the Table
	 * @return	boolean	TRUE / FALSE	TRUE on added columns successfully to lottery database, FALSE on did not successfully add columns to database		
	 */
	public function lottery_expand_columns($table)
	{
		$fields = array (
			'sum_draw'	=> array(
						'type' => 'INT',
                		'constraint' => '11',
                		'unsigned' => TRUE
			),
			'sum_digits' =>	array(
						'type' => 'INT',
						'constraint' => '11',
						'unsigned' => TRUE
			),
			'even'		 =>	array(
						'type' => 'INT',
						'constraint' => '2',
						'unsigned' => TRUE
			),
			'odd'		 => array(
						'type' => 'INT',
						'constraint' => '2',
						'unsigned' => TRUE
			),
			'range_draw' => array(
						'type' => 'INT',
						'constraint' => '11',
						'unsigned' => TRUE
			),
			'repeat_decade' => array(
						'type' => 'INT',
						'constraint' => '2',
						'unsigned' => TRUE
			),
			'repeat_last'	=> array(
						'type' => 'INT',
						'constraint' => '2',
						'unsigned' => TRUE
			)
		);
	return $this->dbforge->add_column($table, $fields);		// Add Statistic Columns to Lottery Database, return SUCCESS / FAILURE
	}
	/**
	 * Returns the number of rows in the table
	 * 
	 * @param	string	$table			Actual Name of the Table
	 * @return	integer					Returns the number of rows in the table		
	 */
	public function lottery_rows($table)
	{
		return $this->db->count_all($table);	
	}

	/**
	 * Returns the draw date of the range of draws back. e.i. $draw_back = 100 and returns the draw date from 100 draws ago
	 * If the draw back does not return a draw date, then FALSE is returned
	 * 
	 * @param	string			$tbl									Current Lottery Data Table Name
	 * @param	integer			$draw_back								The number of draws back to return the draw date
	 * @param	boolean			$ex										Extra Draws included in query, 0 = No, 1 = Yes
	 * @return	string			$draw_date (YYYY-MM-DD format)			Returns the last date of the most recent draw, FALSE if no draw date is returned		
	 */
	public function lottery_return_date($tbl, $draw_back, $ex)
	{	
		$where = (!$ex ? ' WHERE `extra` <> "0" ' : '');
		//$sql = 'SELECT `draw_date` FROM '.$tbl.$where.' ORDER BY `draw_date` DESC LIMIT '.$draw_back.';';
		$query = $this->db->query('SELECT `draw_date` FROM '.$tbl.$where.' ORDER BY `draw_date` DESC LIMIT '.$draw_back.';');
		if (!$query) return FALSE;	// Draw Database Does not Exist
		$row = $query->last_row();
		return $row->draw_date;	// Return the draw date (YYYY-MM-DD format)			
	}

	/**
	 * Returns the number of rows without extra draws. If a bonus is included, extra draws usually have 0 for the bonus / extra draw
	 * 
	 * @param	string	$table			Actual Name of the Table
	 * @param	boolean	$bonus			True / False, if bonus is used for this lottery. Default is Lottery DOES NOT have a bonus ball drawn
	 * @return	integer					Returns the number of rows in the table without the extra draws played		
	 */
	public function lottery_rows_noextra($table, $bonus = FALSE)
	{
		if(!$bonus) return FALSE;
		$query = $this->db->query('SELECT * FROM '.$table.' WHERE extra<>"0";');
		return $query->num_rows();	
	}

	/**
	 * Returns the first id number in the table
	 * 
	 * @param	string	$table			Actual Name of the Table
	 * @return	string	id				Returns the starting index id from the lottery table.		
	 */
	public function lottery_start_id($table)
	{
		$this->db->reset_query();				// Clear any previous queries that are cached
		$query = $this->db->select_min('id')	// Target is the index id.
				->get($table);
		$row = $query->row();
		return $row->id;	
	}

	/**
	 * Returns the (auto incremented) index id, from the Lottery id
	 * 
	 * @param	integer		$lotto_id 		Lottery_id
	 * @return	boolean		TRUE/FALSE		Existing Statistics Record? 		
	 */
	public function stats_id($lotto_id)
	{
		$this->db->where('lottery_id',$lotto_id);
    	$exist = $this->db->get($this->_table_name);
    	if ($exist->num_rows() > 0)
		{
			return TRUE;	// Existing Record is found in the lottery_stats table
		}
	return FALSE;	// returns FALSE, indicating there is no existing Record
	}

	/**
	 * Returns the (auto incremented) index id, from the Lottery id
	 * 
	 * @param	integer			$lotto		Lottery_id
	 * @return	integer	 		$row->id	Index id from lottery_stats		
	 */
	public function update_stats_id($lotto)
	{
		$query = $this->db->select('id')
                ->where('lottery_id', $lotto)
				->get($this->_table_name);
		$row = $query->row();
	return $row->id;	// returns the index from the lottery_id
	}
	/**
	 * Returns the number of rows that have the statistics fields but have no statistic data, used to update the current lottery database draws with no statistics
	 * 
	 * @param	string	$table			Actual Name of the Table
	 * @return	integer					Returns the number of rows in the table		
	 */
	public function lottery_next_rows($table)
	{
		$conditions = array('sum_draw ' => NULL, 'sum_digits ' => NULL, 'even ' => NULL, 'odd ' => NULL, 'range_draw ' => NULL, 'repeat_decade ' => NULL, 'repeat_decade ' => NULL);
		$this->db->where($conditions);
		$this->db->from($table);
		return $this->db->count_all_results();
	}
	/**
	 * Returns the next available index id with active records that have no statistics, used to update the current lottery database draws with no statistics
	 * 
	 * @param	string	$table				Actual Name of the Table
	 * @return	string	$row->id or FALSE	Returns the starting index id from the lottery table or NULL, if no draws are available		
	 */
	public function lottery_next_id($table)
	{
		$this->db->reset_query();	// Clear any previous queries that are cached
		$conditions = array('sum_draw ' => NULL, 'sum_digits ' => NULL, 'even ' => NULL, 'odd ' => NULL, 'range_draw ' => NULL, 
							'repeat_decade ' => NULL, 'repeat_decade ' => NULL, 'extra !=' => '0');
		$query = $this->db->select('id')
						->where($conditions)
						->order_by('id', 'ASC')
						->limit(1)
						->get($table);
		$row = $query->row_array();
	return (!is_null($row) ? $row['id'] : FALSE); // This will return the next available id that has no statistics for that draw and was not a bonus draw
	}

	/**
	 * Update Lottery Draw from current Lottery Draw DB
	 * 
	 * 
	 * @param string  			$table					Converted Lottery Table Name provided from lottery_table_convert
	 * @param integer 			$id						Current index id to be updated in
	 * @param array 			$balls					Associative array of the draw with, draw date, balls 1 ... N with/without extra/bonus ball
	 * @return integer/boolean  index #id or FALSE 		Success or Failure on updating the statistics portion of the draw			
	 */
	public function lottery_draw_update($table, $id, $balls)
	{
		// Update Statistics to Draw
		$this->db->where('id', $id);
		$this->db->where('draw_date', $balls['draw_date']);

	if (!$this->db->update($table, $balls)) return FALSE;	

	return $id;	// Return the $id of the draw record
	}

	/**
	 * Returns the first id number in the table
	 * 
	 * @param	string $table		Name of Lottery Draw Table, index id of lottery draw record, n
	 * @param 	integer $id			index id of lottery draw record
	 * @param 	integer $max		maximum number of balls draw
	 * @return	array  $draw		Returns the starting index id from the lottery table.		
	 */
	public function lottery_draw_stats($table, $id, $max)
	{
		$this->db->reset_query();	// Clear any previous queries that are cached
		$query = $this->db->where('id', $id)
						  ->get($table);
		$draw = $query->row_array(); // Return the Draw Details as an array, since it will not be passed in as an array
		if($draw)
		{
			$draw['sum_draw'] = $this->lottery_draw_sum($draw,$max);
			$draw['sum_digits'] = $this->lottery_draw_sumdigits($draw,$max);
			$draw['even'] = $this->lottery_draw_even($draw,$max);
			$draw['odd'] = $this->lottery_draw_odd($draw,$max);
			$draw['range_draw'] = $this->lottery_draw_range($draw,$max);
			$draw['repeat_decade'] = $this->lottery_draw_decade($draw,$max);
			$draw['repeat_last'] = $this->lottery_draw_last($draw,$max);
		} 
		else
		{
			return FALSE;	// Something went wrong
		}
	return $draw;	// Return the draw with the individual stats
	}
	/**
	 * Returns the sum of the current drawn numbers array
	 * 
	 * @param	array				Current Draw Array
	 * @param 	integer $max		maximum number of balls drawn
	 * @return	integer $sum		Returns the sum of the drawn numbers		
	 */
	public function lottery_draw_sum($draw, $max)
	{
		$sum = 0;  // Initialize to total sum to 0
		$n = 1;
		do
		{
			$sum = $sum + intval($draw['ball'.$n]);
			$n++;
			$max--;
		} while($max>0);
	
	return $sum;	
	}
	/**
	 * Returns the sum of the individual digits and totals them as tghe final sum in the draw 
	 * @param	array				Current Draw Array
	 * @param 	integer $max		maximum number of balls drawn
	 * @return	integer $sum		Returns the sum of the digits of the drawn numbers		
	 */
	public function lottery_draw_sumdigits($draw, $max)
	{
		$sum = 0; // Initialize to total sum to 0
		$n = 1;
		do
		{
			$sum = (intval($draw['ball'.$n]) < 10 ? $sum+intval($draw['ball'.$n]): $sum+(intval(substr($draw['ball'.$n],0,1)))+(intval(substr($draw['ball'.$n],1,1))));
			$n++;
			$max--;
		} while($max>0);
	return $sum;
	}
	/**
	 * Return only the even number of balls drawn for the current drawn numbers array
	 * 
	 * @param	array				Current Draw Array
	 * @param 	integer $max		maximum number of balls drawn
	 * @return	integer $sum		Returns the even number of drawn numbers		
	 */
	public function lottery_draw_even($draw, $max)
	{
		$even = 0; 	// Set Even Number to 0
		$n = 1;
		do
		{
			if(!intval($draw['ball'.$n]%2)) $even++;
			$n++;
			$max--;
		} while($max>0);
	return $even;	// Return only the even numbers
	}
	/**
	 * Returns only the odd numnbers drawn from the current drawn numbers array
	 * 
	 * @param	array				Current Draw Array
	 * @param 	integer $max		maximum number of balls drawn
	 * @return	integer $sum		Returns the odd number of drawn numbers		
	 */
	public function lottery_draw_odd($draw, $max)
	{
		$odd = 0; 	// Set Odd Number to 0
		$n = 1;
		do
		{
			if(intval($draw['ball'.$n]%2)) $odd++;
			$n++;
			$max--;
		} while($max>0);
	return $odd;	// return only the odd numbers
	}
	/**
	 * Return the range of numbers drawnn for the current drawn numbers array
	 * 
	 * @param	array				Current Draw Array
	 * @param 	integer $max		maximum number of balls drawn
	 * @return	integer $range		Returns the range of drawn numbers		
	 */
	public function lottery_draw_range($draw, $max)
	{
		$range = 0;		// Set the range to 0
		$range = intval($draw['ball'.$max])-intval($draw['ball1']);
	return $range;	// Return the range of balls drawn from the first ball to ball N
	}
	/**
	 * Returns the number the Maximum drawn numbers that are repeating in the same decade (more than 2 must be in the same decade) and only return the largest number
	 * 
	 * @param	array				Current Draw Array
	 * @param 	integer $max		maximum number of balls drawn
	 * @return	integer $decades	Returns the number of drawn numbers in the given decades		
	 */
	public function lottery_draw_decade($draw, $max)
	{
		$n = 1;
		$decades = 0;
		$decade1 = 0;
		$decade2 = 0;
		$decade3 = 0;
		$decade4 = 0;
		$decade5 = 0;
		$decade6 = 0;
		$decade7 = 0;
		$decade8 = 0;
		$decade9 = 0;

		do
		{
			if(intval($draw['ball'.$n])<10) $decade1++;
			elseif(intval($draw['ball'.$n])<20) $decade2++;
			elseif(intval($draw['ball'.$n])<30) $decade3++;
			elseif(intval($draw['ball'.$n])<40) $decade4++;
			elseif(intval($draw['ball'.$n])<50) $decade5++;
			elseif(intval($draw['ball'.$n])<60) $decade6++;
			elseif(intval($draw['ball'.$n])<70) $decade7++;
			elseif(intval($draw['ball'.$n])<80) $decade8++;
			elseif(intval($draw['ball'.$n])<90) $decade9++;
			$n++;   
			$max--;
		} while($max>0);

		$decades = $decade1;	// Start with the first decade 0 - 9
		if($decades<$decade2) $decades = $decade2;	// 10 - 19
		if($decades<$decade3) $decades = $decade3;	// 20 - 29
		if($decades<$decade4) $decades = $decade4;	// 30 - 39
		if($decades<$decade5) $decades = $decade5;	// 40 - 49
		if($decades<$decade6) $decades = $decade6;	// 50 - 59
		if($decades<$decade7) $decades = $decade7;	// 60 - 69
		if($decades<$decade8) $decades = $decade8;	// 70 - 79
		if($decades<$decade9) $decades = $decade9;	// 80 - 89
		
	return $decades;
	}
	/**
	 * Returns the number of maximum number of repeating last digits for the current drawn numbers
	 * 
	 * @param	array				Current Draw Array
	 * @param 	integer $max		Name of Lottery Draw Table, index id of lottery draw record, maximum number of balls drawn
	 * @return	integer $last		Returns the number of repeating last digit of drawn numbers		
	 */
	public function lottery_draw_last($draw, $max)
	{
		$n = 1;
		$last = 0;		// Set the last digit to 0
		$zeros = 0;		// How many zeros?
		$ones = 0;		// How Many Ones?
		$twos = 0;		// How Many Twos?
		$threes = 0;	// How Many Threes?
		$fours = 0;		// How Many Fours? 
		$fives = 0;		// How Many Fives? 
		$sixs = 0;		// How Many Sixs? 
		$sevens = 0;	// How Many Sevens? 
		$eights = 0;	// How Many eights? 
		$nines = 0;		// How Many nines?
		do
		{	if(intval($draw['ball'.$n])<9) $draw['ball'.$n] = '0'.$draw['ball'.$n];
			if(substr($draw['ball'.$n],1,1)=='0') $zeros++;
			if(substr($draw['ball'.$n],1,1)=='1') $ones++;
			if(substr($draw['ball'.$n],1,1)=='2') $twos++;
			if(substr($draw['ball'.$n],1,1)=='3') $threes++;
			if(substr($draw['ball'.$n],1,1)=='4') $fours++;
			if(substr($draw['ball'.$n],1,1)=='5') $fives++;
			if(substr($draw['ball'.$n],1,1)=='6') $sixs++;
			if(substr($draw['ball'.$n],1,1)=='7') $sevens++;
			if(substr($draw['ball'.$n],1,1)=='8') $eights++;
			if(substr($draw['ball'.$n],1,1)=='9') $nines++;
			$n++;
			$max--;
		} while($max>0);
		// Only update the the number of last digits for the maximum only
		
		$last = $zeros;	// Start with the last zeros
		if($last<$ones) $last = $ones;	// Last 1's
		if($last<$twos) $last = $twos;	// Last 2's
		if($last<$threes) $last = $threes;	// Last 3's
		if($last<$fours) $last = $fours;	// Last 4's
		if($last<$fives) $last = $fives;	// Last 5's
		if($last<$sixs) $last = $sixs;	// Last 6's
		if($last<$sevens) $last = $sevens;	// Last 7's
		if($last<$eights) $last = $eights;	// Last 8's
		if($last<$nines) $last = $nines;	// Last 9's

	return $last;		// Return the Maximum number of last digits from this draw	
	}
	
	/**
	 * Returns the sum of last draw or the most recent drawn numbers
	 * 
	 * @param	string	$tbl						Current Lottery Data Table Name
	 * @return	integer $sum or 'NA' (string)		Returns the sum from the last draw or NA if the draw database does not exist		
	 */
	public function sum_last($tbl, $drawn)
	{	
		if (!$this->lotteries_m->lotto_table_exists($tbl)) return 'NA';
		$row_last = (array) $this->db_row($tbl, 0);

		$b = 1;
		$sum = 0;
		do
		{
			$sum = $sum + intval($row_last['ball'.$b]);	
			$b++;	
		} 
		while($b<=$drawn);
	
	return $sum;
	}

	/**
	 * Returns the number of repeating last digits for the current drawn numbers array
	 * 
	 * @param	string			 $tbl				Current Lottery Data Table Name
	 * @param 	integer			 $drawn				Maximum balls drawn excluding the extra ball (if applies)
	 * @return	integer/boolean  $sum or FALSE		Returns the number of repeating numbers from the last draw with the draw before the latest draw.		
	 */
	public function repeaters($tbl, $drawn)
	{	
		if (!$this->lotteries_m->lotto_table_exists($tbl)) return 'NA';	// Draw Database Does not Exist
		$row_last = (array) $this->db_row($tbl, 0);
		$row_previous = (array) $this->db_row($tbl, 1);

		$b = 1;
		$current = array();
		$previous = array();
		do {
			$current['ball'.$b] = $row_last['ball'.$b];
			$previous['ball'.$b] = $row_previous['ball'.$b];
			$b++;
		} while($b<=$drawn);
		$duplicates = array_intersect($current, $previous); // Compare for the simularites

		if(!empty($duplicates)) 
		{
			$s = "";
			foreach($duplicates as $duplicate)
			{
				$s .= $duplicate.", ";
			}
			$s = substr($s, 0, -2);	// Remove the comma & extra space
		}
		else
		{
			$s = "None";	// No Repeating Numbers
		}
	return $s;
	}

	/**
	 * Returns the last date of the most recent draw or N/A, if the draw database does not exist
	 * 
	 * @param	string			$tbl									Current Lottery Data Table Name
	 * @return	string			draw date (YYYY-MM-DD format)			Returns the last date of the most recent draw		
	 */
	public function last_date($tbl)
	{	
		if (!$this->lotteries_m->lotto_table_exists($tbl)) return 'NA';	// Draw Database Does not Exist
		$draw = $this->db_row($tbl, 0);
		return $draw->draw_date;	// Return the draw date (YYYY-MM-DD format)			
	}

	/**
	 * Returns the most recent last drawn numbers or N/A if the draw database does not exist
	 * 
	 * @param	string			$tbl				Current Lottery Data Table Name
	 * @param	integer			$drawn				Number of Drawn Numbers for this lottery
	 * @param	boolean			$extra				TRUE, has extra ball,  FALSE, has no extra ball
	 * @return	string								Returns the last drawn numbers in a string format, N! N2 N3 ... + EXTRA		
	 */
	public function last_draw($tbl, $drawn, $extra)
	{	
		if (!$this->lotteries_m->lotto_table_exists($tbl)) return 'NA';	// Draw Database Does not Exist
		$draw = $this->db_row($tbl, 0);		
		
		if (isset($draw))
		{
			$s = $draw->ball1.' '.$draw->ball2.' '.$draw->ball3;	// Build the first 3 numbers
			if($drawn>3) $s .= ' '.$draw->ball4;
			if($drawn>4) $s .= ' '.$draw->ball5;
			if($drawn>5) $s .= ' '.$draw->ball6;
			if($drawn>6) $s .= ' '.$draw->ball7;
			if($drawn>7) $s .= ' '.$draw->ball8;
			if($drawn>9) $s .= ' '.$draw->ball9;
			if($extra) $s .= ' + '.$draw->extra;
		}
		else return 'NA';
	return $s;	// Return Drawn Numbers in 'N1 N2 N3 ... + Extra' Format	
	}

	/**
	 * Returns the last row, previous row or next row from the lottery database query
	 * 
	 * @param	string			$tbl	Current Lottery Data Table Name
	 * @param 	integer			$row	Return the last, previous or next database object. Default to the last row of the database draws
	 * @param	integer			$e		Include Extra Draws such as bonus draws. 		
	 * @return	object 			last row, previous row or next row depending on the row value of lottery records		
	 */
	public function db_row($tbl, $row = 0)
	{	
		$query = $this->db->query('SELECT * FROM '.$tbl. ' WHERE `extra` <> "0" ORDER BY `draw_date` DESC LIMIT 100');
		
		switch($row)
		{
			case 0:
				return $query->row(0); // Last Row
			case 1:
				return $query->row(1); // Previous to the Last Row
			case 2:
				return $query->result_array(); // Return the Lottery Draw Results for the last 100 draws.
		}
	}
	
	/**
	 * Returns the last row, previous row or next row from the lottery database query
	 * 
	 * @param	string		$tbl			Current Lottery Data Table Name, in the proper format
	 * @return	boolean		TRUE/FALSE 		If the statistics fields have an actual value (calculated) (TRUE) or (not calculated) NULL (False)	
	 */
	public function last_stats_exist($tbl)
	{	
		$this->db->reset_query();
		$query = $this->db->query('SELECT * FROM '.$tbl. ' ORDER BY `draw_date` DESC LIMIT 100');
		
		$stats = $query->row(0);
		if((is_null($stats->sum_draw))&&(is_null($stats->sum_digits))&&(is_null($stats->even))&&(is_null($stats->odd))&&
		(is_null($stats->range_draw))&&(is_null($stats->repeat_decade))&&(is_null($stats->repeat_last))) return FALSE;

	return TRUE;
	}

	/**
	 * Returns the Average Sum of draws based on a given Range of Draws
	 * 
	 * @param	array	$tbl 		Current Draws for Calculation
	 * @param 	integer	$range		Number of the number of draws to calculate from the latest draw
	 * @return	integer $ave_sum	Returns the Average sum of the drawn numbers		
	 */
	public function lottery_average_sum($tbl, $range = 10)
	{
		$this->db->reset_query();	// Clear any previous queries that are cached
		
		$ave_sum = 0;

		$sql = "SELECT `sum_draw` as sum FROM `".$tbl;
		$sql .= "` ORDER BY draw_date DESC ";
		$sql .= "LIMIT ".$range;
		$query = $this->db->query($sql);
		if(!$query) return FALSE;
		$added = $query->result_object(); // Return the range of object sums
		foreach($added as $row)
		{
			$ave_sum = $ave_sum + $row->sum;
		}
		$ave_sum = $ave_sum / $range;	// Find the Average Sum for the Range

	return (integer) round($ave_sum,0);	
	}
	/**
	 * Returns the average sum of the individual digits for the drawn numbers over a range of drawns
	 * 
	 * @param	string	$tbl				Current Lottery
	 * @param 	integer $range				Range from 10 to the limit of the draws
	 * @return	integer $ave_sumdigits		Returns the average sum of the digits for the draw range		
	 */
	public function lottery_average_sumdigits($tbl, $range = 10)
	{
		$sum = 0; // Initialize to total sum to 0
		
		$this->db->reset_query();	// Clear any previous queries that are cached
		$query = $this->db->select('*')
				->order_by('draw_date DESC')
				->limit($range)
				->get($tbl);	// Retrieve the id and only return 1 row
		
		if(!$query) return FALSE;
		$total = 0;
		foreach ($query->result_array() as $row)
		{
				$total = $total + $row['sum_digits'];
		}
		$ave_sumdigits = $total / $range; // Get the Average of the sums of the individual digits
		
	return (integer) round($ave_sumdigits,0);
	}

	/**
	 * Return only the average even number of balls drawn for a range of draws
	 * 
	 * @param	string	$tbl		Name of Lottery Table
	 * @param 	integer $range		Range of Draws to Calculate, 10, 100, 500, etc.
	 * @return	integer $evens		Returns the even number of drawn numbers on average		
	 */
	public function lottery_average_evens($tbl, $range = 10)
	{
		$this->db->reset_query();	// Clear any previous queries that are cached
		$sql = "SELECT AVG(`even`) as `average_evens`";
		$sql .=	" FROM (";
  		$sql .=	"select `even`";
  		$sql .= " FROM `".$tbl;
  		$sql .=  "` ORDER BY draw_date DESC LIMIT ".$range;
		$sql .= ") evens";

		$query = $this->db->query($sql);
		if (!$query) return FALSE;

		$evens = $query->row();
		
	return (integer) round($evens->average_evens,0);
	}
	/**
	* Return only the average odd number of balls drawn for a range of draws
	 * 
	 * @param	string	$tbl		Name of Lottery Table
	 * @param 	integer $range		Range of Draws to Calculate, 10, 100, 500, etc.
	 * @return	integer $odds		Returns the odd number of drawn numbers on average			
	 */
	public function lottery_average_odds($tbl, $range = 10)
	{
		$sql = "SELECT AVG(`odd`) as `average_odds`";
		$sql .=	" FROM (";
  		$sql .=	"select `odd`";
  		$sql .= " FROM `".$tbl;
  		$sql .=  "` ORDER BY draw_date DESC LIMIT ".$range;
		$sql .= ") odds";

		$query = $this->db->query($sql);
		if (!$query) return FALSE;

		$odds = $query->row();

	return (integer) round($odds->average_odds,0);
	}
	/**
	 * Return the range of numbers drawnn for the current drawn numbers array
	 * 
	 * @param	string	$tbl		Name of Lottery Table
	 * @param 	integer $range		Range of Draws to Calculate, 10, 100, 500, etc.
	 * @return	integer $range		Returns the range of drawn numbers		
	 */
	public function lottery_average_range($tbl, $range = 10)
	{
		$this->db->reset_query();	// Clear any previous queries that are cached
		
		$sql = "SELECT AVG(`range_draw`) as `average_range`";
		$sql .=	" FROM (";
  		$sql .=	"select `range_draw`";
  		$sql .= " FROM `".$tbl;
  		$sql .=  "` ORDER BY draw_date DESC LIMIT ".$range;
		$sql .= ") average_range";

		$query = $this->db->query($sql);
		if (!$query) return FALSE;	

		$average = $query->row();
		
	return (integer) round($average->average_range,0); // Return the range of balls drawn from the first ball to ball N	
	}
	/**
	 * Returns the number the Aveage maximum drawn numbers that are repeating in the same decade (more than 2 must be in the same decade) and only return the largest number
	 * 
	 * @param	string	$tbl		Name of Lottery Table
	 * @param 	integer $max		maximum number of balls drawn
	 * @param 	integer $range		Range of Draws to Calculate, 10, 100, 500, etc.
	 * @return	integer $dec_ave	Returns the average maximum number of decades during a range
	 **/	
	public function lottery_average_decade($tbl, $range = 10)
	{
		$this->db->reset_query();	// Clear any previous queries that are cached
		$sql = "SELECT AVG(`repeat_decade`) as `average_decade`";
		$sql .=	" FROM (";
  		$sql .=	"select `repeat_decade`";
  		$sql .= " FROM `".$tbl;
  		$sql .=  "` ORDER BY draw_date DESC LIMIT ".$range;
		$sql .= ") average_decade";

		$query = $this->db->query($sql);
		if (!$query) return FALSE;	

		$dec_ave = $query->row();
		
	return (integer) round($dec_ave->average_decade,0); // Returns the average maximum decade rounded off
	}
	/**
	 * Returns the number of maximum number of repeating last digits for the current drawn numbers
	 * 
	 * @param	string	$tbl		Name of Lottery Table
	 * @param 	integer $range		Range of Draws to Calculate, 10, 100, 500, etc.
	 * @return	integer $last_ave	Returns the average maximum same last drawn digits for a given range
	 **/
	public function lottery_average_last($tbl, $range = 10)
	{
		$this->db->reset_query();	// Clear any previous queries that are cached
		$sql = "SELECT AVG(`repeat_last`) as `average_last`";
		$sql .=	" FROM (";
  		$sql .=	"select `repeat_last`";
  		$sql .= " FROM `".$tbl;
  		$sql .=  "` ORDER BY draw_date DESC LIMIT ".$range;
		$sql .= ") average_last";

		$query = $this->db->query($sql);
		if (!$query) return FALSE;	

		$last_ave = $query->row();
		
	return (integer) round($last_ave,0); // Returns the average maximum decade rounded off
	}
	/**
	 * If existing Record for the Followers table exist
	 * 
	 * @param	integer	$id		Lottery ID of current Lottery
	 * @return  array	$query 	result set query or FALSE	
	 */
	public function followers_exists($id)
	{
		$query = $this->db->where('lottery_id', $id)
                ->limit(1, 0)
                ->get('lottery_followers');
		return $query->row_array();
	}
	/**
	 * If existing Record for the nonFollowers table exist
	 * 
	 * @param	integer	$id		Lottery ID of current Lottery
	 * @return  array	$query 	result set query or FALSE	
	 */
	public function nonfollowers_exists($id)
	{
		$query = $this->db->where('lottery_id', $id)
                ->limit(1, 0)
                ->get('lottery_nonfollowers');
		return $query->row_array();
	}
	/**
	 * If existing Record for the Friends table exist
	 * 
	 * @param	integer	$id		Lottery ID of current Lottery
	 * @return  array	$query 	result set query or FALSE	
	 */
	public function friends_exists($id)
	{
		$query = $this->db->where('lottery_id', $id)
                ->limit(1, 0)
                ->get('lottery_friends');
		return $query->row_array();
	}
	/**
	 * If existing Record for the NonFriends table exist
	 * 
	 * @param	integer	$id		Lottery ID of current Lottery
	 * @return  array	$query 	result set query or FALSE	
	 */
	public function nonfriends_exists($id)
	{
		$query = $this->db->where('lottery_id', $id)
                ->limit(1, 0)
                ->get('lottery_nonfriends');
		return $query->row_array();
	}

	/**
	 * If existing Record for the h_w_c table exist
	 * 
	 * @param	integer	$id		Lottery ID of current Lottery
	 * @return  array	$query 	result set query or FALSE	
	 */
	public function h_w_c_exists($id)
	{
		$query = $this->db->where('lottery_id', $id)
                ->limit(1, 0)
                ->get('lottery_h_w_c');
		return $query->row_array();
	}

	/**
	 * If existing Record for the h_w_c table exist
	 * 
	 * @param	integer	$id		Lottery ID of current Lottery
	 * @return  array	$query 	result set query or FALSE	
	 */
	public function hwc_history_exists($id)
	{
		$this->db->reset_query();
		//$query = $this->db->query("SELECT * FROM lottery_h_w_c_stats WHERE id = '".$id."' AND h_w_c_range = '".$r."' LIMIT 1,0");
		$query = $this->db->where('lottery_id', $id)
                ->limit(1, 0)
                ->get('lottery_h_w_c_stats'); 
	return $query->row_array();
	}

	/**
	 * Toggle Extra (Bonus) Ball included in the query
	 * 
	 * @param	integer	$id				Lottery_id for the follower and friend methods
	 * @param 	string 	$table	 		Either of two tables, lottery_followers or lottery_friends
	 * @return  boolean	TRUE / FALSE  	If previously set, then save as unset (FALSE), If previously unset, then save as set (TRUE), Return TRUE or FALSE	
	 */
	public function extra_included($id, $update = FALSE, $table)
	{
		$this->db->reset_query();	// Clear any previous queries that are cached
		$query = $this->db->select('extra_included')
					->where('lottery_id', $id)
                	->limit(1,0)
                	->get($table);
		$row = $query->row();			
		$included = $row->extra_included;

		if($update)
		{
			$included = (!$included ? '1' : '0'); // Toggle the Extra (Bonus) Ball to included

			$this->db->set('extra_included', $included);
			$this->db->where('lottery_id', $id);
			$this->db->update($table);
		}
	return $included; 
	}

	/**
	 * Toggle Extra (Bonus) Ball included in the query for friends
	 * 
	 * @param	integer	$id				Lottery_id for the follower and friend methods
	 * @param 	string 	$table	 		Either of two tables, lottery_followers or lottery_friends	
	 * @return  boolean	TRUE / FALSE  	If previously set, then save as unset (FALSE), If previously unset, then save as set (TRUE), Return TRUE or FALSE	
	 */
	public function extra_draws($id, $update = FALSE, $table)
	{
		$this->db->reset_query();	// Clear any previous queries that are cached
		$query = $this->db->select('extra_draws')
				->where('lottery_id', $id)
                ->limit(1, 0)
                ->get($table);
		$included = $query->row()->extra_draws;

		if($update)
		{
			$included = (!$included ? '1' : '0'); // Toggle the Extra (Bonus) Draws to included

			$data = array(
				'extra_draws' => $included
			);
			$this->db->where('lottery_id', $id);
			$this->db->update($table, $data);
		}
	return $included; // included has been updated, FALSE to don't use and TRUE to include the extra (Bonus) draws
	}

	/**
	 * Calculate the number of trailing (follower) numbers based on the last draw
	 * 
	 * @param 	string 	$name		specific lottery table name
	 * @param	array	$last		$last drawn numbers (index, date, ball1 ... ball N, Extra (Bonus ball), lottery id)
	 * @param 	integer $max		maximum number of balls drawn
	 * @param	boolean	$bonus		If an extra / bonus ball is included (1 = TRUE, 0 = False)
	 * @param	boolean $draws		If extra (bonus) draws are included in the calculation (1 = TRUE, 0 = FALSE)
	 * @param  	integer	$range		Range of number of draws (default is 100). If less than 100, the number must be set in $range
	 * @return  string	$followers	Followers string in this format that follow with the number of occurrences (minumum 3 Occurrences)
	 * 								e.g. 10=>3=4|22=3,17=>10=5|37=4|48=4
	 */
	public function followers_calculate($name, $last, $max, $bonus, $draws, $range = 100)
	{
		// Build Query
		$range--;	// Not including the last draw within the range of draws
		$s = 'ball'; 
		$i = 1; 	// Default Ball 1
		do
		{	
			$s .= $i;
			$i++;
			if($i<=$max) $s .= ', ball';
		} 
		while($i<=$max);

		$s .= ', extra, draw_date';
		$b_max = $max;	// The maximum of the ONLY the balls drawn
		if($bonus) $max++;

		$w = (!$draws ? " AND extra <> '0'" : ""); 
		
		// Calculate
		$b = 1; // ball 1
		// Initialize and create blank associate array
		$followers = '';	// set as a blank string
		do
		{
			//$sql = "SELECT ".$s." FROM (SELECT * FROM ".$name." WHERE id <>".$last['id']." ORDER BY draw_date DESC LIMIT ".$range.") as t".$w."ORDER BY t.draw_date ASC"; 
			$sql = "SELECT t.* FROM (SELECT ".$s." FROM ".$name." WHERE id <> '".$last['id']."'".$w." ORDER BY draw_date DESC LIMIT ".$range.") as t ORDER BY t.draw_date ASC;";
			// Execute Query
			$query = $this->db->query($sql);
			$row = $query->first_row('array');
			$c_b = ($bonus&&($b>$b_max) ? $last['extra'] : $last['ball'.$b]); // If there is an Extra / Bonus Ball and this bonus ball has exceeded the regularly drawn numbers, retrieve the extra ball
			$followlist = array();
			do {
				if($this->is_drawn($c_b, $row, $b_max, $bonus))
				{
					$row = $query->next_row('array');
					if(!is_null($row))
					{
						unset($row['draw_date']);
						if(!empty($followlist))
						{
							$followlist = $this->update_followers($followlist, $row);
						}
						else
						{
							$followlist = $this->add_followers($row);
						}
					}
				}
				else
				{
					$row = $query->next_row('array');
				}
			} while(!is_null($row));
		
		// Build Follower string
		if(empty($followlist)) $followlist = NULL; 
		$followers .= $this->follower_string($c_b, $followlist); 
		// Return $follower number associative numbers that have 3 and above in this format, save in this format e.g. ball drawn 10 => 22=3,37=4,42=4
		// update ball counter
		// while ball count < $max
			$b++;
			if(($b<=$max)&&(!empty($followlist))) $followers .= ',';
			unset($followlist);		// Destroy the old followerlist
			$query->free_result();	// Removes the Memory associated with the result resource ID
		} while ($b<=$max);
		return $followers;
	}
	/**
	 * Return if the drawn number was drawn from the current row
	 * 
	 * @param	integer	$num		Drawn number from the most recent draw
	 * @param 	array 	$curr		Current set of drawn numbers
	 * @param	integer	$pick		How many numbers are drawn without the extra / bonus ball	
	 * @param 	boolean $ex			Extra / Bonus ball include flag. TRUE / FALSE	
	 * @return	boolean $found		Found the ball drawn during this draw
	 */
	private function is_drawn($num, $curr, $pick, $ex)
	{
		$found = FALSE;
		$i=1;
		// 		if query ball equals current ball then
		//   	for each query ball that does not exist, +1 for each ball add to associative array
		//		if query ball exists in associative array then +1 for existing associative query ball
		do
		{
			if ($num==$curr['ball'.$i]) 
			{
				$found = TRUE;
				break;		// exit loop
			}
			$i++;
		} while($i<=$pick);
		if($ex&&($num==$curr['extra'])) // If the bonus / extra ball is included in the drawn number analysis
		{
			$found = TRUE;
		}	
	return $found;		// Return the range of balls drawn from the first ball to ball N
	}
	/**
	 * Return the added only list of followers after the current draw
	 * 
	 * @param	array	$row		Current Draw to compare and add to the followers list		
	 * @return	array	$list		List of updated followers
	 */
	private function add_followers($row)
	{
	
		$list = array();	// Empty set array
		foreach($row as $key => $balls_drawn)
		{
			if($balls_drawn!=0) 
			{
				$list += [
					$balls_drawn => 1]; 
			}
		}
	return $list;		// Return the followers of the current draw
	}
	/**
	 * Return the updated list of followers that were was drawn from the current draw
	 * 
	 * @param	array	$list		List of followers and the totals
	 * @param	array	$row		Current Draw to compare and update		
	 * @return	array	$list		List of updated followers
	 */
	private function update_followers($list, $row)
	{
		foreach($row as $key => $balls_drawn)
		{
			if(($balls_drawn!=0)&&(array_key_exists($balls_drawn, $list)))
			{
				$list[$balls_drawn]++;	// Auto increment the array from the $key
			}
			elseif($balls_drawn!=0)
			{
				$list += [		// If it does not exist, add the key and set the value to one.
					$balls_drawn => 1];
			}
		}
	return $list;		// Return the range of balls drawn from the first ball to ball N
	}
	/**
	 * Return the added only list of followers after the current draw
	 * @param	integer	$ball		Ball that the list is associated with, for example, Drawn ball 10 had Ball 3 (with 4 occurences) and Ball 22 (with 3 occurences)
	 * @param	array	$list		Associative Array of followers and the counts		
	 * @return	string	$str		Return formatted string of the follower numbers with the counts in this format, 10>3=4|22=3
	 */
	private function follower_string($ball,$list)
	{
		$str = "";
		if(is_null($list)) $str = '0>0=0|'; // Empty Set
		else
		{
			foreach($list as $key => $follows)
			{
				if($follows>=3) $str .= $key.'='.$follows.'|'; // Format 3=4 Occurences with pipe and continue until the last follower has been added.
			}
			$str = (!empty($str) ? $ball.'>'.$str :  ''); // Format '10>'  Drawn Ball Number 10
		}
	return substr($str, 0, -1);		// Return the followers of the current draw without the extra Pipe character on the end of string
	}
	/** 
	* Insert / Update Follower Profile of current lottery
	* 
	* @param 	array	$data		key / value pairs of Follower Profile to be inserted / updated
	* @param	boolean $exist		add a new entry (FALSE), if no previous follower has been added otherwise update the existing follower row (TRUE), default is FALSE
	* @return   none	
	*/
	public function follower_data_save($data, $exist = FALSE)
	{
		if (!$exist) 
		{
			$this->db->set($data);		// Set the query with the key / value pairs
			$this->db->insert('lottery_followers');
		}
		else
		{
			$this->db->set($data);		// Set the query with the key / value pairs
			$this->db->where('lottery_id', $data['lottery_id']);
			$this->db->update('lottery_followers');
		}
	}
	/** NEW CALCULATION 
	 * Calculate the number of never trailing (follower) numbers based on the last draw and the draw rang
	 * 
	 * @param 	string 	$name			specific lottery table name
	 * @param	array	$last			last drawn numbers (index, date, ball1 ... ball N, Extra (Bonus ball), lottery id)
	 * @param 	integer $max			maximum number of balls drawn
	 * @param	boolean	$bonus			If an extra / bonus ball is included (1 = TRUE, 0 = False)
	 * @param	boolean $draws			If extra (bonus) draws are included in the calculation (1 = TRUE, 0 = FALSE)
	 * @param  	integer	$range			Range of number of draws (default is 100). If less than 100, the number must be set in $range
	 * @param	integer	$top			Last Ball in Lottery that is drawn, e.g. 649 - 49 balls maximum
	 * @return  string	$nonfollowers	non-Followers string in this format that follow with the number of occurrences (minumum 3 Occurrences)
	 * 									e.g. 10=>3=4|22=3,17=>10=5|37=4|48=4
	 */
	public function nonfollowers_calculate($name, $last, $max, $bonus, $draws = 0, $range = 100, $top)
	{
		// Build Query
		$s = 'ball'; 
		$i = 1; 	// Default Ball 1
		do
		{	
			$s .= $i;
			$i++;
			if($i<=$max) $s .= ', ball';
		} 
		while($i<=$max);

		$s .= ', extra, draw_date';
		$b_max = $max;	// The maximum of the ONLY the balls drawn
		if($bonus) $max++;

		$w = (!$draws ? ' AND extra <> "0"' : '');
				
		// Calculate
		$b = 1; // ball 1
		// Initialize and create blank associate array
		$nonfollowers = '';	// set as a blank string
		do
		{
			//$sql = "SELECT ".$s." FROM (SELECT * FROM ".$name." WHERE id <>".$last['id']." ORDER BY id DESC LIMIT ".$range.") as t".$w."ORDER BY t.id ASC"; 
			$sql = "SELECT t.* FROM (SELECT ".$s." FROM ".$name." WHERE id <> '".$last['id']."'".$w." ORDER BY draw_date DESC LIMIT ".$range.") as t ORDER BY t.draw_date ASC;";
			// Execute Query
			$query = $this->db->query($sql);
			$row = $query->first_row('array');
			$c_b = ($bonus&&($b>$b_max) ? $last['extra'] : $last['ball'.$b]); // If there is an Extra / Bonus Ball and this bonus ball has exceeded the regularly drawn numbers, retrieve the extra ball
			$followlist = array();
			$nonfollowlist = array();
			do {
				if($this->is_drawn($c_b, $row, $b_max, $bonus))
				{
					$row = $query->next_row('array');
					if(!is_null($row))
					{
						if(!empty($followlist))
						{
							$followlist = $this->update_followers($followlist, $row);
						}
						else
						{
							$followlist = $this->add_followers($row);
						}
					}
				}
				else
				{
					$row = $query->next_row('array');
				}
			} while(!is_null($row));
		
		// Build Follower string
		if(empty($followlist)) $followlist = NULL; 
		$nonfollowlist = $this->non_followers($followlist, $top);
		$nonfollowers .= $this->nonfollower_string($c_b, $nonfollowlist); 
		// Return $follower number associative numbers that have 3 and above in this format, save in this format e.g. ball drawn 10 => 22,37,42
		// update ball counter
		// while ball count < $max
			$b++;
			if($b<=$max) $nonfollowers .= ',';
			unset($followlist);		// Destroy the old followerlist
			unset($nonfollowlist);
			$query->free_result();	// Removes the Memory associated with the result resource ID
		} while ($b<=$max);
		return $nonfollowers;
	}
	/**
	 * Return the updated list of followers that were was drawn from the current draw
	 * 
	 * @param	array	$list		List of followers and the totals
	 * @param	integer	$last		Last Ball that is drawn for this lottery		
	 * @return	array	$non_list	List of all non-followers (That have never been drawn for the given range)
	 */
	private function non_followers($list, $last)
	{
		if(is_null($list)) return NULL;
		$non_list = array();
		$start = 1;		// Start will ball 1
		$i = 0;
		do
		{
			if(!isset($list[$start]))
			{
				$non_list[$i] = $start;
				$i++;
			}
			$start++;	//Update the ball count
		}
		while($start<$last);
	return $non_list;		// Return the range of balls drawn from the first ball to ball N
	}
	/**
	 * Return the added only list of followers after the current draw
	 * @param	integer	$ball		Ball that the list is associated with, for example, Drawn ball 10 had Ball 3 fall after 10 (with 4 occurences)
	 * @param	array	$list		Associative Array of followers and the counts		
	 * @return	string	$str		Return formatted string of the non-follower numbers with the counts in this format, 10>3|22
	 */
	private function nonfollower_string($ball,$list)
	{
		$str = "";
		if(empty($list)) $str = $ball.'>0|'; // Empty Set
		else
		{
			$str = $ball.'>';
			foreach($list as $key)
			{
				$str .= $key.'|'; // Format 3=4 Occurences with pipe and continue until the last follower has been added.
			}
		}
	return substr($str, 0, -1);		// Return the followers of the current draw without the extra Pipe character on the end of string
	}

	/** 
	* Insert / Update Follower Profile of current lottery
	* 
	* @param 	array	$data		key / value pairs of Follower Profile to be inserted / updated
	* @param	boolean $exist		add a new entry (FALSE), if no previous follower has been added otherwise update the existing follower row (TRUE), default is FALSE
	* @return   none	
	*/
	public function nonfollower_data_save($data, $exist = FALSE)
	{
		if (!$exist) 
		{
			$this->db->set($data);		// Set the query with the key / value pairs
			$this->db->insert('lottery_nonfollowers');
		}
		else
		{
			$this->db->set($data);		// Set the query with the key / value pairs
			$this->db->where('lottery_id', $data['lottery_id']);
			$this->db->update('lottery_nonfollowers');
		}
	}
	/**
	 * Calculate the Friends of the Lottery from Ball 1 to Ball N range, include the extra ball if TRUE. Based on the range of draws covered
	 * 
	 * @param 	string 	$name		specific lottery table name
	 * @param 	string 	$last		last drawn numbers (index, date, ball1 ... ball N, Extra (Bonus ball), lottery id)
	 * @param 	integer $max		maximum number of balls drawn
	 * @param	integer	$top		Maximum Ball drawn for this lottery. e.g. 49 in Lotto 649
	 * @param	boolean	$bonus		If an extra / bonus ball is included (1 = TRUE, 0 = False)
	 * @param	boolean $draws		If extra (bonus) draws are included in the calculation (1 = TRUE, 0 = FALSE)
	 * @param  	integer	$range		Range of number of draws (default is 100). If less than 100, the number must be set in $range
	 * @return  string	$friends	Friends string in this format: 1>9=4:01/24/2020,2>11=8:09/18/2020,3>44=10:06/22/2019  ,etc. 
	 */
	public function friends_calculate($name, $max, $top, $bonus = 0, $draws = 0, $range = 100)
	{
		// Build Query
		$s = 'ball'; 
		$i = 1; 	// Default Ball 1
		do
		{	
			$s .= $i;
			$i++;
			if($i<=$max) $s .= ', ball';
		} 
		while($i<=$max);

		$s .= ', extra, draw_date'; // Include the draw date is this query

		$w = (!$draws ? ' WHERE extra <> "0" ' : ' ');
		//$l = (!is_null($last) ? " WHERE draw_date <='".$last['draw_date']."'" : "");
		
		// Initialize and create blank associate array
		$friends = '';	// set as a blank string
		$nonfriends = '';	// set as a blank string
		$b = 1; // Number 1 to Number N from the size of the Lottery
		do
		{
			// Calculate
			//$sql = "SELECT ".$s." FROM (SELECT * FROM ".$name." ORDER BY id DESC LIMIT ".$range.") as t".$w."ORDER BY t.id ASC"; 
			$sql = "SELECT t.* FROM (SELECT ".$s." FROM ".$name.$w." ORDER BY draw_date DESC LIMIT ".$range.") as t ORDER BY t.draw_date ASC;";
			// Execute Query
			$query = $this->db->query($sql);
			$row = $query->first_row('array'); // Doing the reverse to the first row because of the descending order.
			$friendlist = array();
			do {
				if($this->is_drawn($b, $row, $max, $bonus))
				{
					if(!is_null($row))
					{
						if(!empty($friendlist))
						{
							$friendlist = $this->update_friends($b, $friendlist, $row, $bonus);
						}
						else
						{
							$friendlist = $this->add_friends($b, $row, $bonus);
						}
					}
					$row = $query->next_row('array'); // Go to the next draw for examination
				}
				else
				{
					$row = $query->next_row('array');
				}
			} while(!is_null($row)); // Do until all draws complete
		
		// Separate the non-friends out of the friends.  All the numbers in that range that have NEVER followed a given ball will be added.
		// eg. 20,11,27,30,40,49,22|2,10,15,20,30,38,40| ... have never occurred with this number in that range.
		$nonfriends .= $this->nonfriends_string($friendlist, $b, $top);
		// Check duplicate occurrences in the array. If duplicate, go with most recent draw date following the latest trend for that number. return only 1 friend array
		$friendlist = (!empty($friendlist) ? $this->duplicate_friends($friendlist) : NULL);
		// Build Friend string
		$friends .= $this->friends_string($friendlist); // Empty Set? Then Skip
		// while not out of range
		// Returns $friendr number associative numbers, save in this format e.g. friend ball drawn 10>6:2020/12/06
		// update ball counter
		// while ball count < $max
			$b++;
			if($b<=$top) $friends .= ','; //If there are numbers to do in a pick 3 to pick 9 system
			unset($friendlist);	// Destroy the old friendlist
			$query->free_result();	// Removes the Memory associated with the result resource ID
		} while ($b<=$top);
		return $friends.'+'.$nonfriends; // return friends+nonfriends (without the '|' at the end of non friends)
	}
	/**
	 * Return the added only list of friends of the ball drawn for this ball number
	 * 
	 * @param 	integer	$ball		Current Ball being not included in the friends list
	 * @param	array	$row		Current Draw to compare and add to the followers list
	 * @param	boolean	$ex			Extra / Bonus Ball TRUE / FALSE - FALSE and ball is equal to the current draw, DO NOT INCLUDE as friends
	 * @return	array	$list		List of updated followers
	 */
	private function add_friends($ball, $row, $ex)
	{
		$list = array();	// Empty set array
		if(!$ex&&($ball==$row['extra'])) return $list;
		foreach($row as $key => $balls_drawn)
		{
			// Every Ball is counted as a friend except the ball that is currently examined
			if(($ball!=$balls_drawn)&&($balls_drawn!=0)&&($key!='draw_date')) $list += [
				$balls_drawn => 1,
				(intval($balls_drawn)<10 ? '0'.$balls_drawn : $balls_drawn).'_draw_date' => $row['draw_date']
			]; 
		}
	return $list;		// Return the followers of the current draw
	}
	/**
	 * Return the updated list of friends of the ball drawn for this ball number
	 * 
	 * @param	integer	$ball		Current Ball being not included in the friends list
	 * @param	array	$list		List of followers and the totals
	 * @param	array	$row		Current Draw to compare and update
	 * @param	boolean	$ex			Extra / Bonus Ball TRUE / FALSE - FALSE and ball is equal to the current draw, DO NOT INCLUDE as friends
	 * @return	array	$list		List of updated followers
	 */
	private function update_friends($ball, $list, $row, $ex)
	{
		
		if(!$ex&&($ball==$row['extra'])) return $list;
		foreach($row as $key => $balls_drawn)
		{
			if(($ball!=$balls_drawn)&&($balls_drawn!=0)&&($key!='draw_date')&&(array_key_exists($balls_drawn, $list)))
			{
				$list[$balls_drawn]++;
				$list[(intval($balls_drawn)<10 ? '0'.$balls_drawn : $balls_drawn).'_draw_date'] = $row['draw_date'];
			}
			elseif(($ball!=$balls_drawn)&&($balls_drawn!=0)&&($key!='draw_date'))
			{
				$list += [
					$balls_drawn => 1, 	// If it does not exist, add the key and set the value to one.
					(intval($balls_drawn)<10 ? '0'.$balls_drawn : $balls_drawn).'_draw_date' => $row['draw_date']
				]; 
			}
		}
	return $list;		// Return the range of balls drawn from the first ball to ball N
	}
	/**
	 * Check for Duplicates in the friend list. Sort the duplicates list from Descending down. 
	 * If duplicates found, keep all the duplicates with the most recent date. If No duplicates, return single record
	 * Remove all other duplicates in the list. All keys are unique but the values are not. Values can be duplicate for different keys
	 * @param 	array	$duplicates		Complete friendlist
	 * @return	array	$friend			Return the updated friend with draw date
	 */
	private function duplicate_friends($duplicates)
	{
	
	// Complete a key sort, no boolean check required
	ksort($duplicates);

	$counts = array();	// declare a blank associative array for counts
	$dates = array();   // and ditto for the dates

	// Strip out counts and the dates into their own arrays
	
	foreach($duplicates as $key => $value) // $date could be the count or it could be the date
	{
		if(strpos($key, '_draw_date')) 
		{
			$dates[$key] = $value; // Actually the date
		}
		else
		{
			$counts[$key] = $value; // Actually the count
		}
	}
	return $this->one_friend($counts, $dates);		// Return the followers of the current draw
	}

	/**
	 * Check for Multiples of the same count.  Get the most recent date and elimate all other associative array elements
	 * If only a single top count, eliminate all other associative array elements 
	 * Return only a single associate array element as the only friend to the companion number
	 * @param 	array	$totals			All the counts for the given ball
	 * @param	array	$d_dates		All the draw dates for the given counts
	 * @return	array	friend			Return the updated single friend only array element as Number => xx, count => xx, draw_date => yyyy/mm/dd
	 */
	private function one_friend($totals, $d_dates)
	{
		$max = max($totals); // Determine the highest count
		foreach($totals as $key => $value)
		{
			if($value==$max) $k = $key;  // Retrieve the key from the highest count
		}

		$max_date = strtotime($d_dates[(intval($k)<10 ? '0'.$k : $k).'_draw_date']);	// convert to a unix date

		// Second iteration for looking for the most recent draw date
		foreach($totals as $key => $value)
		{
			$d = strtotime($d_dates[(intval($key)<10 ? '0'.$key : $key).'_draw_date']);
			if(($value==$max)&&($d>$max_date)) // Date must be greater than the max date to make it more recent
			{
				$max_date = $d; // This iteration has found a more recent date
				$k = $key;		// Make this key, the new key as the friend
			}
		}

		$friend = [
			'number'	=> $k,
			'count'		=> $totals[$k],
			'draw_date'	=> $d_dates[(intval($k)<10 ? '0'.$k : $k).'_draw_date']
		];
		
	return $friend;	// Return the single most important friend after the current draw
	}
	/**
	 * Return the added only list of friends after the current draw
	 * @param	array	$list		Associative Array of followers and the counts		
	 * @return	string	$str		Return formatted string of the follower numbers with the counts in this format, 24>7|2020-12-25, e.g. YYYY-MM-DD
	 */
	private function friends_string($list)
	{
		$str = (!is_null($list) ? $list['number'].'>'.$list['count'].'|'.$list['draw_date'] : '0>0|yyyy-mm-dd'); // Format 3=4 Occurences with pipe and continue until the last follower has been added.

	return $str;	// Return the followers of the current draw without the extra Pipe character on the end of string
	}

	/**
	 * Return the non existent friends after the current draw
	 * @param	array	$list		Associative Array of followers and the counts
	 * @param	integer	$exclude	Current Ball is excluded from the nonfriends. It can't be a friend to itself
	 * @param	integer	$limit		Maximum Ball drawn for this lottery. e.g. 49 in Lotto 649	
	 * @return	string	$str		Return formatted string of all the non friends in that range that have NEVER followed a given ball.
	 */
	private function nonfriends_string($list, $exclude, $limit)
	{
		$str = '';
		for ($count = 1; $count <= $limit; $count++)
		{
			if (!array_key_exists($count, $list)&&($count!=$exclude)) // Include ONLY if that number has NEVER occurred
			{
				$str .= $count.', '; 								 // Used as display only with a comma and space
			}
		}

	return substr($str,0,-2).'|';	// Return the friends with a '|' separator
	}
	/** 
	* Insert / Update Friends Profile of current lottery
	* 
	* @param 	array	$data		key / value pairs of Friend Profile to be inserted / updated
	* @param	boolean $exist		add a new entry (FALSE), if no previous friends has been added otherwise update the existing friends row (TRUE), default is FALSE
	* @return   none	
	*/
	public function friends_data_save($data, $exist = FALSE)
	{
		if (!$exist) 
		{
			$this->db->set($data);		// Set the query with the key / value pairs
			$this->db->insert('lottery_friends');
		}
		else
		{
			$this->db->set($data);		// Set the query with the key / value pairs
			$this->db->where('lottery_id', $data['lottery_id']);
			$this->db->update('lottery_friends');
		}
	}
	/** 
	* Insert / Update NonFriends Profile of current lottery
	* 
	* @param 	array	$data		key / value pairs of Friend Profile to be inserted / updated
	* @param	boolean $exist		add a new entry (FALSE), if no previous friends has been added otherwise update the existing friends row (TRUE), default is FALSE
	* @return   none	
	*/
	public function nonfriends_data_save($data, $exist = FALSE)
	{
		if (!$exist) 
		{
			$this->db->set($data);		// Set the query with the key / value pairs
			$this->db->insert('lottery_nonfriends');
		}
		else
		{
			$this->db->set($data);		// Set the query with the key / value pairs
			$this->db->where('lottery_id', $data['lottery_id']);
			$this->db->update('lottery_nonfriends');
		}
	}
	/**
	 * Returns the list of evens, odds, number of occureeces and the percentage of the occurences for the given range
	 * 
	 * @param	string			$tbl		Lottery table with underscores e.g lottery_649
	 * @param 	boolean 		$tod		Trends of Draws, will remove extra draws '0' from the query or keep them '1'
	 * @return	object 			$all		evens, odds, total for each even/odd combination and the percentage (%) of occurences for each even/odd combination		
	 */
	public function evensodds_sum($tbl, $tod)
	{	
		$query = $this->db->query('SELECT odd, even, count(*) as count from '.$tbl.' group by odd, even');
		$all = $query->result(); // Retrieve the total result from the query
		$total = 0;
		foreach($all as $parity)
		{
			$total += $parity->count; 
		}
		
		$interval = intval($total/100);
		if($interval>=2) $interval = 2;	// Maximum 200 draws to compare
		foreach($all as $key => $compare)
		{
			if(!isset($all[$key]->total)) $all[$key]->total=$total;
			if(!isset($all[$key]->last_10)) $all[$key]->count_10=0;
			if(!isset($all[$key]->last_100)&&$interval>=1) $all[$key]->count_100=0;
			if(!isset($all[$key]->last_200)&&$interval>=2) $all[$key]->count_200=0;
		}

		$range = 10;
		do
		{
			$this->db->reset_query();
			$ex_d = (!empty($tod) ? "WHERE extra <> '0' " : " ");
			$query = $this->db->query("select odd, even, count(*) from (SELECT * FROM 
			`".$tbl."`".$ex_d."ORDER BY draw_date DESC LIMIT ".$range.") sub 
			group by odd, even ORDER BY draw_date ASC;");

			$subject = $query->result_array();
			$arr_count = count($all);
			foreach($subject as $key => $compare)
			{
				for($itr = 0; $itr < $arr_count; $itr++)
				{
					if(($compare['even']==$all[$itr]->even)&&($compare['odd']==$all[$itr]->odd)) 
					{
						switch($range)
						{
							case 10:
								$all[$itr]->count_10 = $compare['count(*)'];
								break;
							case 100:
								$all[$itr]->count_100 = $compare['count(*)'];
								break;
							case 200:
								$all[$itr]->count_200 = $compare['count(*)'] ;
						}
					}
				}
			}
			($range==10 ? $range=100 : $range += 100);
			$interval--;
		} 
		while($interval>=0);
	return $all; // Return the updated query with odd / even data
	}
	/**
	 * Returns the list of hots, warms and colds for a given range and date
	 * 
	 * @param	string			$lotto_tbl	Table of Lottery
	 * @param	integer			$picks		Number of balls drawn (pick 6, pick 7, etc.) excluding the extra / bonus ball
	 * @param	boolean			$bonus		Bonus / Extra ball included in query. 0 = no, 1 = yes
	 * @param	boolean			$draws		Extra Draws (without the the extra ball included) in the query. 
	 * @param	integer			$range		Range of draws to calculate, 10 draws, 100 draws, 200 draws, etc.
	 * @param 	integer 		$w			Start of the warm numbers begin. e.g 1-16 Hots, 17-33 Warms, 34-49 Colds in 49 system
	 * @param 	integer 		$c			Cold count of the numbers .e.g 16 hot, 17 warm adn 16 cold for a pick 6 - 49 system
	 * @param	string			$last		last date to calculate for the draws, in yyyy-mm-dd format, it blank skip. useful to back in time through the draws
	 * @return	string 			$hwc_string	returns as key value pairs with the number and the heat number.  Numbers are returned based on their heat value
	 * 										e.g. 4 as a key and 58 as the value for heat in the given range and date	
	 */
	public function h_w_c_calculate($lotto_tbl, $picks, $bonus = 0, $draws = 0, $range = 0, $w, $c, $last = '')
	{
		// Build query
		$sql_range = ($range ? ' ORDER BY draw_date DESC LIMIT '.$range : ' ORDER BY draw_date DESC');
		$sql_date = '';
		$sql_draws = '';
		if (!empty($last)&&($draws))
		{
			$sql_date = ' WHERE draw_date <= "'.$last.'"';
		}
		if(!empty($last)&&(!$draws))
		{
			$sql_draws = ' WHERE extra <> "0"';
			$sql_date = ' AND draw_date <= "'.$last.'"';
		}
		//$sql_date = (!empty($last) ? ' WHERE draw_date <= "'.$last.'"' : '');
		elseif(empty($last)&&(!$draws))
		{
			$sql_draws = ' WHERE extra <> "0"';
		} 

		$sql = 'SELECT ball_drawn, count(*) as heat 
				FROM ((SELECT ball1 as ball_drawn FROM '.$lotto_tbl.$sql_draws.$sql_date.$sql_range.') UNION ALL
      			(SELECT ball2 as ball_drawn FROM '.$lotto_tbl.$sql_draws.$sql_date.$sql_range.') UNION ALL
     			(SELECT ball3 as ball_drawn FROM '.$lotto_tbl.$sql_draws.$sql_date.$sql_range.')';
		if($picks>=4) $sql .= ' UNION ALL (SELECT ball4 as ball_drawn FROM '.$lotto_tbl.$sql_draws.$sql_date.$sql_range.')';
		if($picks>=5) $sql .= ' UNION ALL (SELECT ball5 as ball_drawn FROM '.$lotto_tbl.$sql_draws.$sql_date.$sql_range.')';
		if($picks>=6) $sql .= ' UNION ALL (SELECT ball6 as ball_drawn FROM '.$lotto_tbl.$sql_draws.$sql_date.$sql_range.')';
		if($picks>=7) $sql .= ' UNION ALL (SELECT ball7 as ball_drawn FROM '.$lotto_tbl.$sql_draws.$sql_date.$sql_range.')';
		if($picks>=8) $sql .= ' UNION ALL (SELECT ball8 as ball_drawn FROM '.$lotto_tbl.$sql_draws.$sql_date.$sql_range.')';
		if($picks==9) $sql .= ' UNION ALL (SELECT ball9 as ball_drawn FROM '.$lotto_tbl.$sql_draws.$sql_date.$sql_range.')';
		if($bonus) $sql .= (!empty($last) ? ' UNION ALL (SELECT extra as ball_drawn FROM '.$lotto_tbl.' WHERE extra <> "0"'.$sql_date.$sql_range.')'
		: ' UNION ALL (SELECT extra as ball_drawn FROM '.$lotto_tbl.' WHERE extra <> "0"'.$sql_range.')');
		$sql .= ') as hwc
				GROUP BY ball_drawn
				ORDER BY heat DESC;';
		/* EXAMPLE: SELECT ball_drawn, count(*) as heat FROM ((SELECT ball1 as ball_drawn FROM lotto_max 
		WHERE draw_date <= '2022-01-25' ORDER BY draw_date DESC LIMIT 10) UNION ALL (SELECT ball2 
		as ball_drawn FROM lotto_max WHERE draw_date <= '2022-01-25' ORDER BY draw_date DESC LIMIT 10) 
		UNION ALL (SELECT ball3 as ball_drawn FROM lotto_max WHERE draw_date <= '2022-01-25' ORDER BY 
		draw_date DESC LIMIT 10) UNION ALL (SELECT ball4 as ball_drawn FROM lotto_max WHERE draw_date <= 
		'2022-01-25' ORDER BY draw_date DESC LIMIT 10) UNION ALL (SELECT ball5 as ball_drawn FROM lotto_max WHERE draw_date 
		<= '2022-01-25' ORDER BY draw_date DESC LIMIT 10) UNION ALL (SELECT ball6 as ball_drawn FROM lotto_max WHERE draw_date 
		<= '2022-01-25' ORDER BY draw_date DESC LIMIT 10) UNION ALL (SELECT ball7 as ball_drawn FROM lotto_max WHERE draw_date 
		<= '2022-01-25' ORDER BY draw_date DESC LIMIT 10)) as hwc GROUP BY ball_drawn ORDER BY heat DESC;
		*/
		$query = $this->db->query($sql);
		$hwc_string = ""; // List string in the format of number=hits,
		$i = 1; // non-zero integer
		foreach ($query->result() as $hwc)
		{
        	$hwc_string .= $this->hwc_string($hwc->ball_drawn, $hwc->heat,$i,$w,$c);
			if(($i==($w-1))||($i==(($c)-1))) $hwc_string = substr($hwc_string, 0, -1);
			$i++;
		}
	return substr($hwc_string, 0, -1);		// Return the hwc string without the last comma in the string
	}
	/**
	 * Return the added only list of followers after the current draw
	 * @param	integer	$ball_drawn		Current ball being added to the hwc_string
  	 * @param	integer	$heat			Current occurence being added to the hwc_string
	 * @param 	integer $count			Current ball number of the lottery. Default is always 1 but must be set
	 * @param 	integer $warms			Start of the warm ball in the lottery
	 * @param 	integer $colds			Start of the cold ball in the lottery
	 * @return	string	$str			Return formatted string of the follower numbers with the counts in this format, 24>7|2020-12-25, e.g. YYYY-MM-DD
	 */
	private function hwc_string($ball_drawn, $heat, $count=1, $warms, $colds)
	{
		$str = "";
		if($count==$warms) // Must land on the starting warm number
		{
			$str = ">".$ball_drawn."=".$heat.','; // Format is number=occurences and warm boundary
		}
		elseif($count==$colds) // Must land on the starting cold number
		{
			$str = "<".$ball_drawn."=".$heat.','; // Format is number=occurences and cold boundary
		}
		else
		{
			$str = $ball_drawn."=".$heat.","; // Format is number=occurences for all - hot - warm - cold
		}
	return $str;	// Return the hwc in the string during the iteration
	}
	/**
	 * Return the added only list of hot numbers after the current draw
	 * @param	string	$str_heat	Current ball being added to the hwc_string
	 * @return	string	$str		Return only the hot numbers in the group and truncate the rest of the string
	 */
	public function hots($str_heat)
	{
		$str = substr($str_heat, 0, strpos($str_heat, ">"));
	return $str;	// Return the hots only without the '>'
	}
	/**
	 * Return the added only list of cold numbers after the current draw
	 * @param	string	$str_heat	Current ball being added to the hwc_string
	 * @return	string	$str		Return only the cold numbers in the group and truncate the rest of the string
	 */
	public function colds($str_heat)
	{
		$str_len = strlen($str_heat);
		$str_pos = strpos($str_heat, "<");
		$str_diff = $str_pos-$str_len; // should be negative
		$str = substr($str_heat, $str_diff);
	return substr($str, 1);	// Return the hots only without the '>'
	}

	/**
	 * Return the added only list of cold numbers after the current draw
	 * @param	string	$str_heat	Current ball being added to the hwc_string
	 * @return	string	$str		Return only the cold numbers in the group and truncate the rest of the string
	 */
	public function warms($str_heat)
	{
		$str = $this->return_warms($str_heat, '>', '<');

	return $str; // ($str = warm counts in the string)
	}
	/**
	 * Return only the warm portion of the full h_w_c string\
	 * @param	string	$str_full	The Full H_W_C string
	 * @return	string				Return only the warm portion numbers in the group and truncate the the hots and colds (before the > and the <)
	 */
	private function return_warms($str_full, $start, $end)
	{
		$str_full = ' ' . $str_full;
		$ini = strpos($str_full, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($str_full, $end, $ini) - $ini;
	return substr($str_full, $ini, $len);
	}
	/**
	 * Return the added only list of hot numbers after the current draw
	 * @param	string	$hot		String of hot numbers
	 * @param	string	$warm		String of warm numbers
	 * @param	string	$cold		String of cold numbers
	 * @param	string	$ld			Name of Lottery Database
	 * @param	integer	$max		Maximum number of balls drawn, e.g. Pick 6 and 6 balls picked
	 * @param	boolean	$bonus		Bonus / Extra ball included in query. 0 = no, 1 = yes
	 * @param	boolean	$draws		Extra Draws (without the the extra ball included) in the query. 
	 * @param	integer	$range		Range of draws to analyse
	 * @param	string	$last		last date to calculate for the draws, in yyyy-mm-dd format, it blank skip. useful to back in time through the draws
	 * @return	string	$str		Return only the hot numbers in the group and truncate the rest of the string
	 */
	public function overdue($hots, $warms, $colds, $ld, $max, $bonus = 0, $draws = 0, $range, $last = '')
	{
		$str = ""; // Initialize the Overdue string, format will be the same as h_w_c string, e.i. Number 4 = 10 Last number of draws since last drawn
		$last_date = $this->last_date($ld); // Return the last draw date
		$arr_hots = explode(',', $hots);	// Convert to a hot array
		$arr_warms = explode(',', $warms);  // Convert to a warm array 
		$arr_colds = explode(',', $colds);	// Convert to a cold array

		$select = "SELECT `draw_date` FROM ".$ld;

		foreach($arr_hots as $ahot)
		{
			$heat = explode('=', $ahot);
			$due = intval(round(($range / $heat[1]))); // Round to nearest whole number
			if($max>=3)
			{
				$where = " WHERE ball1=".$heat[0]." OR ball2=".$heat[0]." OR ball3=".$heat[0];
			}
			if($max>=4)
			{
				$where .= " OR ball4=".$heat[0];
			}
			if($max>=5)
			{
				$where .= " OR ball5=".$heat[0];
			}
			if($max>=6)
			{
				$where .= " OR ball6=".$heat[0];
			}
			if($max>=7)
			{
				$where .= " OR ball7=".$heat[0];
			}
			if($max>=8)
			{
				$where .= " OR ball8=".$heat[0];
			}
			if($max==9)
			{
				$where .= " OR ball9=".$heat[0];
			}
			if($bonus) // Only include bonus if set
			{
				$where .= " OR extra=".$heat[0];
			}
			$limit = " ORDER BY `draw_date` DESC LIMIT 1";
			// Query Build
			$sql = $select.$where.$limit;
			$query = $this->db->query($sql);
			$found_date = $query->row()->draw_date;
			$query->free_result(); 								// The $query result object will no longer be available
			$sql_draws = (!$draws ? ' AND extra <> 0': '' ); 	// If no extra draws are included, the extra ball is usually zero.
			$sql = "SELECT * FROM ".$ld." WHERE `draw_date` > '".$found_date."' AND `draw_date` <= '".$last_date."'".$sql_draws;

			$query = $this->db->query($sql);
			$count = $query->num_rows();
			$query->free_result(); // The $query result object will no longer be available again
			if($count>$due)
			{
				$str .= $heat[0]."=".$count."|".$due.",";
			}
		}
		foreach($arr_warms as $awarm)
		{
			$heat = explode('=', $awarm);
			$due = intval(round(($range / $heat[1]))); // Round to nearest whole number
			if($max>=3)
			{
				$where = " WHERE ball1=".$heat[0]." OR ball2=".$heat[0]." OR ball3=".$heat[0];
			}
			if($max>=4)
			{
				$where .= " OR ball4=".$heat[0];
			}
			if($max>=5)
			{
				$where .= " OR ball5=".$heat[0];
			}
			if($max>=6)
			{
				$where .= " OR ball6=".$heat[0];
			}
			if($max>=7)
			{
				$where .= " OR ball7=".$heat[0];
			}
			if($max>=8)
			{
				$where .= " OR ball8=".$heat[0];
			}
			if($max==9)
			{
				$where .= " OR ball9=".$heat[0];
			}
			if($bonus) // Only include bonus if set
			{
				$where .= " OR extra=".$heat[0];
			}
			$limit = " ORDER BY `draw_date` DESC LIMIT 1";
			// Query Build
			$sql = $select.$where.$limit;
			$query = $this->db->query($sql);
			$found_date = $query->row()->draw_date;
			$query->free_result(); // The $query result object will no longer be available
			$sql_draws = (!$draws ? ' AND extra <> 0': '' ); 	// If no extra draws are included, the extra ball is usually zero.
			$sql = "SELECT * FROM ".$ld." WHERE `draw_date` > '".$found_date."' AND `draw_date` <= '".$last_date."'".$sql_draws;
			$query = $this->db->query($sql);
			$count = $query->num_rows();
			$query->free_result(); // The $query result object will no longer be available again
			if($count>$due)
			{
				$str .= $heat[0]."=".$count."|".$due.",";
			}
		}	
		foreach($arr_colds as $acold)
		{
			$heat = explode('=', $acold);
			$due = intval(round(($range / $heat[1]))); // Round to nearest whole number
			if($max>=3)
			{
				$where = " WHERE ball1=".$heat[0]." OR ball2=".$heat[0]." OR ball3=".$heat[0];
			}
			if($max>=4)
			{
				$where .= " OR ball4=".$heat[0];
			}
			if($max>=5)
			{
				$where .= " OR ball5=".$heat[0];
			}
			if($max>=6)
			{
				$where .= " OR ball6=".$heat[0];
			}
			if($max>=7)
			{
				$where .= " OR ball7=".$heat[0];
			}
			if($max>=8)
			{
				$where .= " OR ball8=".$heat[0];
			}
			if($max==9)
			{
				$where .= " OR ball9=".$heat[0];
			}
			if($bonus) // Only include bonus if set
			{
				$where .= " OR extra=".$heat[0];
			}
			$limit = " ORDER BY `draw_date` DESC LIMIT 1";
			// Query Build
			$sql = $select.$where.$limit;
			$query = $this->db->query($sql);
			$found_date = $query->row()->draw_date;
			$query->free_result(); // The $query result object will no longer be available
			$sql_draws = (!$draws ? ' AND extra <> 0': '' ); 	// If no extra draws are included, the extra ball is usually zero.
			$sql = "SELECT * FROM ".$ld." WHERE `draw_date` > '".$found_date."' AND `draw_date` <= '".$last_date."'".$sql_draws;
			$query = $this->db->query($sql);
			$count = $query->num_rows();
			$query->free_result(); // The $query result object will no longer be available again
			if($count>$due)
			{
				$str .= $heat[0]."=".$count."|".$due.",";
			}
		}		

	return substr($str, 0, -1);	// Return the overdues only without an extra ','
	}
	/** 
	* Insert / Update the hot warms and colds, including overdues list of the currently saved lottery
	* 
	* @param 	array	$data		key / value pairs of Friend Profile to be inserted / updated
	* @param	boolean $exist		add a new entry (FALSE), if no previous friends has been added otherwise update the existing friends row (TRUE), default is FALSE
	* @return   none	
	*/
	public function hwc_data_save($data, $exist = FALSE)
	{
		$this->db->reset_query();
		if (!$exist) 
		{
			$this->db->set($data);		// Set the query with the key / value pairs
			$this->db->insert('lottery_h_w_c');
		}
		else
		{
			//$this->db->set($data);		// Set the query with the key / value pairs
			$this->db->where('lottery_id', $data['lottery_id']);
			$this->db->update('lottery_h_w_c', $data);
		}
	}

	/** 
	* Insert / Update the historic hots, warms and colds over the given range
	* 
	* @param 	array	$data		key / value pairs of Friend Profile to be inserted / updated
	* @param	boolean $exist		add a new entry (FALSE), if no previous hwc history has been added otherwise update the existing hwc history (TRUE), default is FALSE
	* @return   none	
	*/
	public function hwc_history_save($data, $exist = FALSE)
	{
		$this->db->reset_query();
		if (!$exist) 
		{
			$this->db->set($data);		// Set the query with the key / value pairs
			$this->db->insert('lottery_h_w_c_stats');
		}
		else
		{
			//$this->db->set($data);		// Set the query with the key / value pairs
			$this->db->where('lottery_id', $data['lottery_id']);
			$this->db->update('lottery_h_w_c_stats', $data);
		}
	}
	/** 
	* Returns the next resulting draw from the given date
	* 
	* @param 	string	$tble	Exact name of Lottery table in the DB
	* @param	string 	$dd		Draw Date in format of yyyy-mm-dd	
	* @return   array	$next	Return the next draw with all the numbers drawn as an array.
	*/
	public function hwc_next_draw($tble, $dd)
	{
		$this->db->reset_query();
		$query = $this->db->query('SELECT * FROM '.$tble.' WHERE extra <> "0" AND draw_date >= "'.$dd.'" LIMIT 2');
		
		$next =  $query->next_row('array');
	return (!$next ? FALSE : $next);
	}

	/** 
	* Returns the next resulting draw from the given date
	* 
	* @param 	integer	$pk		Pick 3, Pick 4, Pick 5, Pick 6, Pick 7, Pick 8 or Pick 9
	* @param	Array 	$drawn	Complete Draw structure id, draw date, ball 1 ... ball N, extra		
	* @return   array	$only	Return only ball 1 ... ball n
	*/
	public function only_picks($pk, $drawn)
	{
		$only = array(); // integer array

		for($l = 0; $l < $pk; $l++)
		{
			$only[$l] = $drawn['ball'.($l+1)];
		}	
	return $only;
	}
}