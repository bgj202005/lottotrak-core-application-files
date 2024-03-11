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
				'7'		=>		'7-0-0,6-1-0,6-0-1,5-2-0,5-1-1,5-0-2,4-3-0,4-2-1,4-1-2,4-0-3,3-4-0,3-3-1,3-2-2,3-1-3,3-0-4,2-5-0,2-4-1,2-3-2,2-2-3,2-1-4,2-0-5,1-6-0,1-5-1,1-4-2,1-3-3,1-2-4,1-1-5,1-0-6,0-6-1,0-5-2,0-4-3,0-3-4,0-2-5,0-1-6,0-7-0,0-0-7',
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
		$conditions = array('sum_draw ' => NULL, 'sum_digits ' => NULL, 'even ' => NULL, 'odd ' => NULL, 'range_draw ' => NULL, 'repeat_decade ' => NULL, 'repeat_last ' => NULL);
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
							'repeat_decade ' => NULL, 'repeat_last ' => NULL); //, 'extra !=' => '0' --> Does not matter if they are extra draws
		// include extra draw statistics					
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
	 * @return	array 			last row, previous row or next row depending on the row value of lottery records		
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
		
	return  ceil($dec_ave->average_decade); // Returns the average maximum decade rounded off
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

		$result = $query->row();
	
	return ceil($result->average_last); // Returns the average maximum decade rounded off
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
	 * @param	array	$ldn		last drawn numbers (index, date, ball1 ... ball N, Extra (Bonus ball), lottery id)
	 * @param 	integer $max		maximum number of balls drawn
	 * @param	boolean	$bonus		If an extra / bonus ball is included (1 = TRUE, 0 = False)
	 * @param	boolean $draws		If extra (bonus) draws are included in the calculation (1 = TRUE, 0 = FALSE)
	 * @param  	integer	$range		Range of number of draws (default is 100). If less than 100, the number must be set in $range
	 * @param	string	$last		last date to calculate for the draws, in yyyy-mm-dd format, it blank skip. useful to back in time through the draws
	 * @param 	boolean	$duple		Duplicate extra ball. FALSE by default.  The extra can have the same number drawn based on the minimum and maximum number drawn
	 * @return  string	$followers	Followers string in this format that follow with the number of occurrences (minumum 3 Occurrences)
	 * 								e.g. 10=>3=4|22=3,17=>10=5|37=4|48=4
	 */
	public function followers_calculate($name, $ldn, $max, $bonus, $draws, $range = 100, $last = '', $duple = FALSE)
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

		$s .= ', extra, draw_date'; // Include the draw date is this query
		$b_max = $max;	// The maximum of the ONLY the balls drawn
		if($bonus) $max++;

		$w = (!$draws ? " AND extra <> '0'" : ""); 
		$w .= (!empty($last) ? " AND draw_date <= '".$last."'" : "");  
		
		// Calculate
		$b = 1; // ball 1
		// Initialize and create blank associate array
		$followers = '';	// set as a blank string
		do
		{
			$blnExDup = ($bonus&&$duple&&($b>$b_max) ? TRUE : FALSE); // Has reached the extra number that is an independent and duplicate Extra ball (TRUE) or everything else is FALSE
			$c_b = ($bonus&&($b>$b_max) ? $ldn['extra'] : $ldn['ball'.$b]); // If there is an Extra / Bonus Ball and this bonus ball has exceeded the regularly drawn numbers, retrieve the extra ball
			$sql = ($blnExDup ? "SELECT t.* FROM (SELECT extra, draw_date FROM ".$name." WHERE id <> '".$ldn['id']."'".$w." ORDER BY draw_date DESC LIMIT ".$range.") as t ORDER BY t.draw_date ASC;" 
			: "SELECT t.* FROM (SELECT ".$s." FROM ".$name." WHERE id <> '".$ldn['id']."'".$w." ORDER BY draw_date DESC LIMIT ".$range.") as t ORDER BY t.draw_date ASC;");
			// Execute Query
			$query = $this->db->query($sql);
 			$row = $query->first_row('array');
			$followlist = array(); // default empty set array
			if(!$blnExDup) // Condition has not been met, not Duplicate Extra
			{
				do 
				{
					if($this->is_drawn($c_b, $row, $b_max, $bonus))
					{
						$row = $query->next_row('array');
						if(!is_null($row))
						{
							unset($row['draw_date']);
							if((!$bonus)||($duple)) unset($row['extra']); // do not use in the add / update compare
							// Special condition for a duplicate extra is not to include the extra ball (in case the duplicate extra is set)
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
			}
			else		// Condition has been met
			{
				 do 
				 {
					if($ldn['extra']==$row['extra'])
					{
						$row = $query->next_row('array');
						if(!is_null($row))
						{
							unset($row['draw_date']);
							if(!empty($followlist))
							{
								$followlist = $this->update_dupalextra($followlist, $row['extra']);
							}
							else
							{
								$followlist = $this->add_dupalextra($row['extra']);
							}
						}
					}
					else
					{
						$row = $query->next_row('array');
					}
				} while(!is_null($row));
			}
		
		// Build Follower string
		if(empty($followlist)) $followlist = NULL;
		/* echo"<pre>";
		print_r($followlist);
		echo "</pre>"; */
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
		if(isset($curr['extra'])) // only if the extra does exit, it will be removed if the bonus flag is false
		{
			If($ex&&($num==$curr['extra'])) $found = TRUE;
		}
		if(!$found)
		{
			do
			{
				if ($num==$curr['ball'.$i])   
				{
					$found = TRUE;
					break;		// exit loop
				}
				$i++;
			} while($i<=$pick);
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
			if((($balls_drawn!=0)&&(array_key_exists($balls_drawn, $list))))
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
	 * Return the added only list of Duplicate Extras (Specific Lottery) after the current draw
	 * 
	 * @param	integer	$extra		Current Draw to compare and add to the followers list		
	 * @return	array	$list		List of updated Duplicate Extras
	 */
	private function add_dupalextra($extra)
	{
	
		$list = array();	// Empty set array
			if($extra!=0) 
			{
				$list += [
					$extra => 1]; 
			}
	return $list;		// Return the followers of the current draw
	}
	/**
	 * Return the updated list of Duplicate Extras (Specific Lottery) that were was drawn from the current draw
	 * 
	 * @param	array	$list		List of Duplicate Extras and the totals
	 * @param	integer	$extra		Current Duplicate Extra Number Draw to compare and update		
	 * @return	array	$list		Return List of updated Duplicate Extras
	 */
	private function update_dupalextra($list, $extra)
	{
			if(($extra!=0)&&(array_key_exists($extra, $list)))
			{
				$list[$extra]++;	// Auto increment the array from the $key
			}
			elseif($extra!=0)
			{
				$list += [			// If it does not exist, add the key and set the value to one.
					$extra => 1];
			}
	return $list;	// Return the range of balls drawn from the first ball to ball N
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
			$str = (!empty($str) ? $ball.'>'.$str :  $ball.'>0=0|'); // Format '10>'  Drawn Ball Number 10 or (only if all the counts are 2 or less. Default is ball 0 = 0 times
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
	/**  
	 * Calculate the number of never trailing (follower) numbers based on the last draw and the draw rang
	 * 
	 * @param 	string 	$name			specific lottery table name
	 * @param	array	$ldn			last drawn numbers (index, date, ball1 ... ball N, Extra (Bonus ball), lottery id)
	 * @param 	integer $max			maximum number of balls drawn
	 * @param	boolean	$bonus			If an extra / bonus ball is included (1 = TRUE, 0 = False)
	 * @param	boolean $draws			If extra (bonus) draws are included in the calculation (1 = TRUE, 0 = FALSE)
	 * @param  	integer	$range			Range of number of draws (default is 100). If less than 100, the number must be set in $range
	 * @param	integer	$top			Last Ball in Lottery that is drawn, e.g. 649 - 49 balls maximum
	 * @param	string	$last			last date to calculate for the draws, in yyyy-mm-dd format, it blank skip. useful to back in time through the draws
	 * @param 	boolean	$duple			Duplicate extra ball. FALSE by default.  The extra can have the same number drawn as a duplicate from any of the main set of numbers
	 * @param 	integer	$mx_ex			Maximum Extra Ball drawn for the independent and duplicate extra lotteries
	 * @return  string	$nonfollowers	non-Followers string in this format that follow with the number of occurrences (minumum 3 Occurrences)
	 * 									e.g. 10=>3=4|22=3,17=>10=5|37=4|48=4
	 */
	public function nonfollowers_calculate($name, $ldn, $max, $bonus, $draws = 0, $range = 100, $top, $last = '', $duple = FALSE, $mx_ex)
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
		$b_max = $max;	// The maximum of the ONLY the balls drawn
		if($bonus) $max++;

		$w = (!$draws ? ' AND extra <> "0"' : '');
		$w .= (!empty($last) ? " AND draw_date <= '".$last."'" : "");  		
		// Calculate
		$b = 1; // ball 1
		// Initialize and create blank associate array
		$nonfollowers = '';	// set as a blank string
		do
		{
			$blnExDup = ($bonus&&$duple&&($b>$b_max) ? TRUE : FALSE); // Has reached the extra number that is an independent and duplicate Extra ball (TRUE) or everything else is FALSE
			if($blnExDup) $top = $mx_ex;	// Swap over the Top Extra ball as the top number instead of the regular balls
			$c_b = ($bonus&&($b>$b_max) ? $ldn['extra'] : $ldn['ball'.$b]); // If there is an Extra / Bonus Ball and this bonus ball has exceeded the regularly drawn numbers, retrieve the extra ball
			$sql = ($blnExDup ? "SELECT t.* FROM (SELECT extra, draw_date FROM ".$name." WHERE id <> '".$ldn['id']."'".$w." ORDER BY draw_date DESC LIMIT ".$range.") as t ORDER BY t.draw_date ASC;" 
			: "SELECT t.* FROM (SELECT ".$s." FROM ".$name." WHERE id <> '".$ldn['id']."'".$w." ORDER BY draw_date DESC LIMIT ".$range.") as t ORDER BY t.draw_date ASC;");
			// Execute Query
			$query = $this->db->query($sql);
			$row = $query->first_row('array');
			
			$followlist = array();
			$nonfollowlist = array();
			if(!$blnExDup) // Condition has not been met, not Duplicate Extra
			{
				do 
				{
					if($this->is_drawn($c_b, $row, $b_max, $bonus))
					{
						$row = $query->next_row('array');
						if(!is_null($row))
						{
							unset($row['draw_date']);
							if((!$bonus)||($duple)) unset($row['extra']);
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
			}
			else		// Condition has been met
			{
				 do 
				 {
					if($ldn['extra']==$row['extra'])
					{
						$row = $query->next_row('array');
						if(!is_null($row))
						{
							unset($row['draw_date']);
							if(!empty($followlist))
							{
								$followlist = $this->update_dupalextra($followlist, $row['extra']);
							}
							else
							{
								$followlist = $this->add_dupalextra($row['extra']);
							}
						}
					}
					else
					{
						$row = $query->next_row('array');
					}
				} while(!is_null($row));
			}
		
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
		$non_list = array();
		if(is_null($list)||count($list)==$last) return $non_list; // empty set and all drawn numbers 
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
	 * Returns the prize pool group to summarize wins based on the prize pool
	 * 
	 * @param 	integer			$id		Lottery id foreign key
	 * @return	array 			Return Complete Prize Profile from the lottery id
	 */
	public function prize_group_profile($id)
	{	
		$query = $this->db->query('SELECT * FROM `lottery_prize_profiles` WHERE `lottery_id` ='.$id.';');
		
	return $this->prize_group_nonnulls($query->result_array()); // Return the Lottery Prize Pool for analysis
	}

	/**
	 * Returns only the actual prize categories that exist
	 * 
	 * @param 	array	$p	Prize Group Profile
	 * @return	array	$pool 	Prize Group without Prizes that are NULL or non-existent prize categories
	 */
	private function prize_group_nonnulls(array $p)
	{	

		$pool = $p[0];
		unset($pool['lottery_id']); // both lottery id and id not required for this return
		unset ($pool['id']);
		
		foreach($pool as $key => $value)
		{
			if($value==NULL) unset($pool[$key]);
		}
		
	return $pool;
	}

	/**
	 * Returns only the prizes for the lottery
	 * 
	 * @param 	array			$p	DB record of prize group profile
	 * @param 	boolean			$e	Extra Included
	 * @return	array 			$p	Returns only the winning prizes and removes the NULL prizes (don't exist)
	 */
	public function prizes_only(array $p, $e)	
	{

		// ** important ** Eliminate all non-winning fields 
		foreach($p as $onlywins => $prizes)
		{
			(int) $p[$onlywins] = 0;	// Active Win Categories. Will be used as a counter and all values are set to 0.
			 if (!$e&&(strpos($onlywins, 'extra')) !== FALSE) 
			 {
        		unset($p[$onlywins]);
			 }
		}
	return $p;		//pass array back without lottery id and id
	}

	/**
	 * Returns the associative array of prizes for each ball played
	 * 
	 * @param 	array			$p			updated prize group, constant for each drawn number
	 * @param 	integer			$min		Minimum Regular ball drawn e.g. 1
	 * @param 	integer			$max		Maximum Regular ball drawn e.g. 49
	 * @return	array 			$p_result	Returns the associated array of an array for number drawn (1, 2, 3, etc.) 
	 * 										and the win zeroed totals (3_win, 3_win_extra, 4_win, 4_win_extra, etc.)
	 */
	public function create_prize_array(array $p, $min, $max)
	{
		$p_result = array();
		do
		{
			$p_result[$min] = $p;
			$min++;
		} while($min<=$max); 
	return $p_result;		//return array with 1 => '3_win' = 0, '3_win_extra' = 0
	}

	/**
	 * Returns the associative array of prizes for each ball played
	 * 
	 * @param 	array			$p			updated prize group, constant for each drawn number
	 * @param 	integer			$max_drawn	Maximum Number of balls drawn. Excluding Extra
	 * @param	booleaan		$ex			Extra Ball / Duplicate Extra Ball
	 * @return	array 			$p_result	Returns the associated array for draw positions (position 1, position 2, position 3, etc.) 
	 * 										and the win zeroed totals (3_win, 3_win_extra, 4_win, 4_win_extra, etc.)
	 */
	public function create_positions_prize_array(array $p, $max_drawn, $ex)
	{
		$p_result = array();
		$pos = 1;  // Array begins with position 1
		
		do
		{
			$p_result[$pos] = $p; // Interste prize categories for each position
			$pos++;
		} while($pos<=$max_drawn);
		if($ex) $p_result['E'] = $p; // Only if the $extrs flag is set

	return $p_result;		//return array with 1 => '3_win' = 0, '3_win_extra' = 0, e
	}

	/**  
	 * Calculate the prize results for the follower and non follower group. E.g. Range is 100 will be 100 draws previous plus 100 draws with the prize pool
	 * This does the follower group for each drawn number. For example, Canada 649 has 49 drawn numbers. The prizes will be calcalated for all balls
	 * following each drawn number. With or without the extra ball.
	 * 
	 * @param 	string 	$name			specific lottery table name
	 * @param	array	$ldn			last drawn numbers (index, date, ball1 ... ball N, Extra (Bonus ball), lottery id)
	 * @param 	integer $max			maximum number of balls drawn
	 * @param	boolean	$bonus			If an extra / bonus ball is included (1 = TRUE, 0 = False)
	 * @param	boolean $draws			If extra (bonus) draws are included in the calculation (1 = TRUE, 0 = FALSE)
	 * @param  	integer	$range			Range of number of draws (default is 100). If less than 100, the number must be set in $range
	 * @param	integer	$top			Last Ball in Lottery that is drawn, e.g. 649 - 49 balls maximum
	 * @param	string	$last			last date to calculate for the draws, in yyyy-mm-dd format, it blank skip. useful to back in time through the draws
	 * @param 	boolean	$duple			Duplicate extra ball. FALSE by default.  The extra can have the same number drawn as a duplicate from any of the main set of numbers
	 * @param 	integer	$mx_ex			Maximum Extra Ball drawn for the independent and duplicate extra lotteries
	 * @return  boolean	$error 			Default is False, True is exceeding the lottery draws range and in error.
	 * 								 
	 */
	public function followers_prizes($name, $ldn, $max, $bonus, $draws = 0, $range = 100, $top, $last = '', $duple = FALSE)
	{
		$error = $this->inrange($name,$range,$draws);
		 
		if(!$error) // The Range is good, let's do this!
		{
			global $prizes;						// Retrieve Global $prizes array
			$prize_counts = $prizes;
			global $positions;					// Wins only by positions e.g. position 1 ... position 6 (pick 6 game)
			$dbl_range = (int) ($range * 2)-1;  // Must be double the available draws available less the most recent draw
			$range_ptr = 1; 					// range_ptr starts at the first draw (single Range)
			$last_ball = $top;					// $top drawn ball is different when there is a duplicate extra ball

			// Step 1. Must have the first range of draws for each drawn number of this lottery
			// Query Builder
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
			$b_max = $max;	// The maximum of the ONLY the balls drawn
			if($bonus) $max++;

			$w = (!$draws ? ' AND extra <> "0"' : '');
			$w .= (!empty($last) ? " AND draw_date <= '".$last."'" : "");  		
			// Calculate
			$b = 1; // ball 1 to ball N for this lottery
			do
			{
				$sql = "SELECT t.* FROM (SELECT ".$s." FROM ".$name." WHERE id <> '".$ldn['id']."'".$w." ORDER BY draw_date DESC LIMIT ".$dbl_range.") as t ORDER BY t.draw_date ASC;";
				// Execute Query
				$query = $this->db->query($sql);
				$row = $query->first_row('array');
				// Initialize and create blank associate array
				$followlist = array();
				$nonfollowlist = array();
				$lowest_row = array(); // First Drawn numbers after followers occurred
				$first = array();		// Lowest draw from the lowest row array
				if($duple) $duplelist = array(); // Only if this lottery has a duplicate extra ball
				
				// Step 1, get the totals for the first range of draws
					do 
					{
						if($this->is_drawn($b, $row, $b_max, $bonus))
						{
							if (($range_ptr>=$range)&&(!isset($loc))) $loc = $this->followers_positions($b,$row,$bonus);
							if($duple) $extra = $row['extra'];
							$row = $query->next_row('array');
							$row['row'] = $range_ptr+1; 	// This is completed only within range
							array_push($lowest_row, $row); 		// Add this row to the end of the array 
							if(!is_null($row))
							{
								unset($row['draw_date']);
								unset($row['row']);
								if(!empty($followlist))
								{
									$followlist = $this->update_followers($followlist, $row);
								}
								else
								{
									$followlist = $this->add_followers($row);
								}
								if($duple&&(isset($extra))&&($b==$extra)) // Only with the duplicate extra and must exist
								{
									unset($row['draw_date']);
									unset($row['row']);
									if(!empty($duplelist))
									{
										$duplelist = $this->update_dupalextra($duplelist, $row['extra']);
									}
									else
									{
										$duplelist = $this->add_dupalextra($row['extra']);  
									}
								}
							}
							if($range_ptr>=$range) // Reached or exceeded the half way point? If yes .. do the prize counts
							{
								// Step 2. Next Range of Draws will include the prize pool
								$nonfollowlist = $this->non_followers($followlist, $last_ball);
								$prize_counts[$b] = $this->followers_prizecounts($bonus, $row, $followlist, $nonfollowlist, $duple, ($duple ? $duplelist : FALSE), $prize_counts[$b]);
								if(isset($loc)) $positions[$loc] = $this->followers_positions_prizecounts($positions[$loc]);
								$first = $lowest_row[0];
								if(intval($range_ptr-$first['row'])>$range) // Only if the current row pointer
																			// is out of range of the target range, remove draw. e.g. Range = 100 draws
								{
									$followlist = $this->remove_oldfollowers($followlist, $first, $bonus);
									if($duple) $duplelist = $this->remove_duplicates($duplelist, $first, $bonus);
									if(!empty($nonfollowlist)) $nonfollowlist = $this->remove_oldnonfollowers($nonfollowlist, $first, $bonus);
									array_shift($lowest_row); // Remove the lowest draw date freom the array, shift it off the beginning of the array
								}
							}
						 }
						else
						{
							$row = $query->next_row('array');
						}
					$range_ptr++; // update draw counter
					} while($range_ptr<$dbl_range&&(!is_null($row))); // !is_null($row)
			
 				$range_ptr = 1; // Reset the range for the next ball
				$b++;
				unset($followlist);				// clear the old followerlist
				unset($nonfollowlist);			// clear the old non follower list
				unset($loc);					// clear the previous position location index
				if($duple) unset($duplelist); 	// duplicate extra list
				$query->free_result();		// Removes the Memory associated with the result resource ID
			} while ($b<=$last_ball); 		// Not maximum balls drawn but the last ball drawn for this lottery
			$prizes = $prize_counts;		// Update the prize informaton for each ball
		}
	return $error;
	}

	/**
	 * Determine the position in the draw for the current matching drawn number 
	 * 
	 * @param 	integer	$bl		Current ball examined
	 * @param 	array	$dw		Associative Current Drawn Numbers
	 * @param 	boolean	$ex		Boolean flag for the extra / bonus ball selection
	 */
	private function followers_positions($bl, $dw, $ex)
	{
		unset($dw['draw_date']); // don't require draw date
		
		$key = array_search($bl,$dw);  // Returns the key
		if($key!='extra')
		{
			$pos_number = filter_var($key, FILTER_SANITIZE_NUMBER_INT); // Strip the string portion
		}
		elseif($key=='extra'&&($ex))
		{
			$pos_number = 'E'; // (E)xtra / Bonus position
		}
	return $pos_number;
	}

	/**
	 * Determine the position in the draw for the current matching drawn number 
	 * 
	 * @param 	array	$p_wins	Curent Associative prizes array
	 * @param 	return	$p_wins	Currents wins updated based on position 
	 */
	private function followers_positions_prizecounts($p_wins)
	{
		global $prizes_cnt; // globals from the individual draw prize count
		global $extra_cnt;	// global if the extra number is included in the prize pool: TRUE / FALSE

		if(!$extra_cnt) 
		{
			switch($prizes_cnt) //* Only the prize pool balls and no extra (bonus) or duplicate extra
			{
				case 1:
					if(array_key_exists('1_win', $p_wins)) ++$p_wins['1_win']; 
					break;
				case 2:
					if(array_key_exists('2_win', $p_wins)) ++$p_wins['2_win'];			
					break;
				case 3:
					if(array_key_exists('3_win', $p_wins)) ++$p_wins['3_win'];
					break;
				case 4:
					if(array_key_exists('4_win', $p_wins)) ++$p_wins['4_win'];
					break;
				case 5:
					if(array_key_exists('5_win', $p_wins)) ++$p_wins['5_win'];
					break;
				case 6:
					if(array_key_exists('6_win', $p_wins)) ++$p_wins['6_win'];
					break;
				case 7:
					if(array_key_exists('7_win', $p_wins)) ++$p_wins['7_win'];
					break;
				case 8:
					if(array_key_exists('8_win', $p_wins)) ++$p_wins['8_win'];
					break;
				case 9:
					if(array_key_exists('9_win', $p_wins)) ++$p_wins['9_win'];
			}
		} 
		else //* Only the prize pool balls and with an extra (bonus) ball AND/OR duplicate ball
		{
			switch($prizes_cnt) 
			{
				case 1:
					if(array_key_exists('extra', $p_wins)) ++$p_wins['extra']; 
					break;
				case 2:
					if(array_key_exists('1_win_extra', $p_wins)) ++$p_wins['1_win_extra'];			
					break;
				case 3:
					if(array_key_exists('2_win_extra', $p_wins)) ++$p_wins['2_win_extra'];
					break;
				case 4:
					if(array_key_exists('3_win_extra', $p_wins)) ++$p_wins['3_win_extra'];
					break;
				case 5:
					if(array_key_exists('4_win_extra', $p_wins)) ++$p_wins['4_win_extra'];
					break;
				case 6:
					if(array_key_exists('5_win_extra', $p_wins)) ++$p_wins['5_win_extra'];
					break;
				case 7:
					if(array_key_exists('6_win_extra', $p_wins)) ++$p_wins['6_win_extra'];
					break;
				case 8:
					if(array_key_exists('7_win_extra', $p_wins)) ++$p_wins['7_win_extra'];
					break;
				case 9:
					if(array_key_exists('8_win_extra', $p_wins)) ++$p_wins['8_win_extra'];
			}
		}

	return $p_wins;
	}

	/**
	 * Totals and updates current prize counts 
	 * 
	 * @param 	boolean	$ex			Extra Ball Flag
	 * @param 	array	$r			Associative Row of the current Draw Array minus the draw date
	 * @param 	array	$fl			Associative Followers Array
	 * @param 	array	$nonfl		Associative non Follower Array
	 * @param 	boolean	$df			Duplicate Extra Ball flag (TRUE = Duplicate Extra Ball Lottery, FALSE = Not Duplicate Extra Ball Lottery)
	 * @param 	array	$da			Associative Duplicate Extra Array (Associative Array, else FALSE)
	 * @param 	array	$p			Associative Prizes Array with current counts
	 * @return	array	$hits		Return the updated associative prizes with counts 
	 */
	private function followers_prizecounts($ex, $r, $fl, $nonfl, $df, $da, $p)
	{
		// Initialize Counters
		global $prizes_cnt;			// prizes_cnt global availability
		global $extra_cnt;			// Extra has also been included
		$prizes_cnt = 0; 			// init prize counter amd ball counter
		$ball_counter = 0; 
		$extra_cnt = FALSE;
		unset($r['draw_date']); 	// Draw date not required

		// for each followers
		if(!empty($fl))
		{
			foreach($r as $drawn => $dr_value) // Based on the next draw that has occurred
			{
				foreach($fl as $follower => $fl_value)
				{
 					if(($dr_value==$follower)) $ball_counter++; // Kepp count of followers
					if(($dr_value==$follower)&&($fl_value>=3)&&($drawn!='extra')) $prizes_cnt++;
					if(($dr_value==$follower)&&($fl_value>=3)&&($drawn=='extra'&&$ex&&!$df)) 
					{
						$prizes_cnt++;
						$extra_cnt=TRUE; // The Extra flag is set
						break;
					}
				}
			}
		}
		if(count($r)!=$ball_counter) // Continue with the non followers, if not all balls have been found in the followers table
		{
			// for each of the non followers
			if(!empty($nonfl))
			{
				foreach($r as $drawn => $dr_value)
				{
				foreach($nonfl as $nonfollower => $nonfl_value)
					{
						if(($dr_value==$nonfl_value)&&($drawn!='extra')) $prizes_cnt++;
						if(($dr_value==$nonfl_value)&&($drawn=='extra'&&$ex&&!$df)) 
						{
							$prizes_cnt++;
							$extra_cnt=TRUE;	// The extra flag is set
							break; 
						}
					}
				}
			}
		}
		if($df&&(is_array($da))) // Duplicate Extra Number is in the prize pool and the duplicates is an array
		{
			foreach($da as $dup => $dup_value)
			{
				if(($r['extra']==$dup&&($dup_value>=3))&&($ex)) // Must have the extra option, the duplicate count must be greater or equal to 3 
				{
					$prizes_cnt++;
					$extra_cnt=TRUE;	// The extra flag is set
					break;				// Found, exit loop
				}
			}
		}
		// for each prize based on ball
		$hits = $p;
		if(!$extra_cnt) 
		{
			switch($prizes_cnt) //* Only the prize pool balls and no extra (bonus) or duplicate extra
			{
				case 1:
					if(array_key_exists('1_win', $hits)) ++$hits['1_win']; 
					break;
				case 2:
					if(array_key_exists('2_win', $hits)) ++$hits['2_win'];			
					break;
				case 3:
					if(array_key_exists('3_win', $hits)) ++$hits['3_win'];
					break;
				case 4:
					if(array_key_exists('4_win', $hits)) ++$hits['4_win'];
					break;
				case 5:
					if(array_key_exists('5_win', $hits)) ++$hits['5_win'];
					break;
				case 6:
					if(array_key_exists('6_win', $hits)) ++$hits['6_win'];
					break;
				case 7:
					if(array_key_exists('7_win', $hits)) ++$hits['7_win'];
					break;
				case 8:
					if(array_key_exists('8_win', $hits)) ++$hits['8_win'];
					break;
				case 9:
					if(array_key_exists('9_win', $hits)) ++$hits['9_win'];
			}
		} 
		else //* Only the prize pool balls and with an extra (bonus) ball AND/OR duplicate ball
		{
			switch($prizes_cnt) 
			{
				case 1:
					if(array_key_exists('extra', $hits)) ++$hits['extra']; 
					break;
				case 2:
					if(array_key_exists('1_win_extra', $hits)) ++$hits['1_win_extra'];			
					break;
				case 3:
					if(array_key_exists('2_win_extra', $hits)) ++$hits['2_win_extra'];
					break;
				case 4:
					if(array_key_exists('3_win_extra', $hits)) ++$hits['3_win_extra'];
					break;
				case 5:
					if(array_key_exists('4_win_extra', $hits)) ++$hits['4_win_extra'];
					break;
				case 6:
					if(array_key_exists('5_win_extra', $hits)) ++$hits['5_win_extra'];
					break;
				case 7:
					if(array_key_exists('6_win_extra', $hits)) ++$hits['6_win_extra'];
					break;
				case 8:
					if(array_key_exists('7_win_extra', $hits)) ++$hits['7_win_extra'];
					break;
				case 9:
					if(array_key_exists('8_win_extra', $hits)) ++$hits['8_win_extra'];
			}
		}
	return $hits;
	}
	
	/**
	 * Update the followers list. FIFO - First in, Last out. Meaning the first set of drawn numbers are removed from the followers list
	 * 
	 * @param 	array	$fl			Current Follower list
	 * @param 	array 	$prev		Draw that is removed from the previous follower list
	 * @param 	boolean $ex			Extra Ball, True (include) False (do not include)
	 * @return	array	$fl			Returns updated followers list
	 */
	private function remove_oldfollowers($fl, $prev, $ex)
	{
		unset($prev['draw_date']); 	// Remove the date from the first draw in the range
		unset($prev['row']);		// Remove the draw number from which it occurred
		foreach($prev as $before => $drawn)
		{
			if(isset($fl[$drawn])&&($before!='extra')) $fl[$drawn]--;
			if(($before=='extra')&&($ex)&&(isset($fl[$drawn]))) $fl[$drawn]--;
			if(isset($fl[$drawn])&&$fl[$drawn]==0) unset($fl[$drawn]); // Remove the old array element
		}
	return $fl;
	}

	/**
	 * Update the nonfollowers list. FIFO - First in, Last out. Meaning the first set of drawn numbers are removed from the non followers list
	 * 
	 * @param 	array	$nonfl		Current Follower list
	 * @param 	array 	$first		Draw to be added as the most recent draw
	 * @param 	boolean $ex			Extra Ball, True (include) False (do not include)
	 * @return	array	$fl			Returns updated followers list
	 */
	private function remove_oldnonfollowers($nonfl,$prev,$ex)
	{
		unset($prev['draw_date']); // Remove the date from the first draw in the range
		unset($prev['row']);		// Remove the draw number from which it occurred

		foreach($prev as $before => $drawn)
		{
			foreach($nonfl as $count => $nf)
			{
				// Non Followers  are only listed in a sequential array
				// and if the draw has 1 or more of non followers, remove them from the list.			
				if(($drawn==$nf)&&($before!='extra')) unset($nonfl[$drawn]);
				if(($before=='extra')&&($ex)&&($drawn==$nf)) unset($nonfl[$drawn]);
			}
		}
	return $nonfl;
	}

	/**
	 * Update the DUplicate Extra list. FIFO - First in, Last out. Meaning the first set of drawn numbers are removed from the followers list
	 * 
	 * @param 	array	$da			Current Duplicate List
	 * @param 	array 	$prev		Draw that is removed from the previous follower list, First from a 100 Range
	 * @param 	boolean $ex			Extra Ball, True (include) False (do not include)
	 * @return	array	$da			Returns updated followers list
	 */
	private function remove_duplicates($da, $prev, $ex)
	{
		If(!$ex) return FALSE;
		unset($prev['draw_date']); // Remove the date from the first draw in the range

		if(isset($da[$prev['extra']])) $da[$prev['extra']]--;
		if(isset($da[$prev['extra']])&&$da[$prev['extra']]==0) unset($da[$prev['extra']]); // Remove the old array element
	return $da;
	}

	/**
	 * Checks the previous range of draws, that there is an minimum of 100 draws available
	 * 
	 * @param 	string	$tbl		Name of Lottery table
	 * @param 	integer $r			Current set range of draws
* 	 * @param 	boolean $dr			Extra Ball, True (include) False (do not include)
	 * @return	boolean				Error flag, TRUE (range exceeeded), FALSE (in range - OK)
	 */
	private function inrange($tbl, $r, $dr)
	{
 		$r = $r * 2; 		// The range must be twice the range of draws 
		//$r = $r - 100;	// The range will be a minimum of 100 draws 
		// for the follower totals and then the wins of those followers
		$where = (!$dr ? ' WHERE `extra` <> "0" ' : '');
		$query = $this->db->query('SELECT `draw_date` FROM '.$tbl.$where.' ORDER BY `draw_date` DESC LIMIT '.$r.';');
		if (!$query) return TRUE;	// Draw Database Does not Exist, error = TRUE
		$total = $query->num_rows();
	return ($total > $r ? TRUE : FALSE); // Range is more existing draws, error else the range exists.
	}

	/**
	 * Return the formatted string of prizes for each ball drawn. e/g 1>10,27,24,22,10,5,2,0<2>10,27,24,22,10,5,2,0 ... etc.
	 * @param	array	$p			Associative Array of prizes and the counts, directly from the prize session
	 * @return	string	$str		Return formatted string of the follower numbers with the counts in this format, 10>3=4|22=3
	 */
	public function followers_prize_string($p)
	{
		$str = "";	// Start with an empty string
		if(!is_null($p))
		{
			foreach($p as $ball => $prizes)
			{
				$str .= $ball.">";
				foreach($prizes as $prize => $total)
				{
					$str .= $total.",";
				}
				$str= substr($str, 0, -1);	
			$str .= "<";
			}
		}
	return substr($str, 0, -1);		// Return the prizes for each number drawn
	}

	/**
	 * Return the formatted string of prizes for each position, 1>10,27,24,22,10,5,2,0<2>10,27,24,22,10,5,2,0<3>10,27,24,22,10,5,2,0
	 * <4>10,27,24,22,10,5,2,0<5>10,27,24,22,10,5,2,0<6>10,27,24,22,10,5,2,0<E>10,27,24,22,10,5,2,0 
	 * @param	array	$p			Associative Array of prizes and the counts, directly from the (global) position prize array
	 * @return	string	$str		Return formatted string of the follower numbers with the counts in this format, 10>3=4|22=3
	 */
	public function followers_positions_prize_string($p)
	{
		$str = "";	// Start with an empty string and the left bracket
		if(!is_null($p))
		{
			foreach($p as $ball => $prizes)
			{
				$str .= $ball.">";
				foreach($prizes as $prize => $total)
				{
					$str .= $total.",";
				}
				$str= substr($str, 0, -1);
				$str .= '<';	
			}
		}
	return substr($str, 0, -1);		// Return the prizes for each number drawn
	}

	/**
	 * Initializes the friend relationships for no friends, a one-way friendship and a two way friendship
	 * @param 	none			
	 * @return	array	$f_init		return array with no friends, 1-way friendshps and 2-way friendships
	 */
	public function create_friend_array()
	{
		$f_init = array('nofriends' => 0,
						'1-way'	=> 0,
						'2-way'	=> 0);
	return $f_init;		//return array with nofriends, 1-way friendshps and 2-way friendships
	}

	/**
	 * Initializes the number of 0 draws with nonfriendships, 1 draws with nonfriendships, 2 to 4 draws with nonfriendships
	 * @param 	none			
	 * @return	array	$f_init		return array with draws of non-friends (0-nfdraws), draws of 1 non-friends 1-nfdraws, 
	 * 								2 draws of non-friendships (2-nfdraws), 3 draws of non-friendships (3-nfdraws),
	 * 								4 draws of non-friendships (4-nfdraws)
	 */
	public function create_nonfriend_array()
	{
		$f_init = array('0-nfdraws' => 0,
						'1-nfdraws' => 0,
						'2-nfdraws' => 0,
						'3-nfdraws' => 0,
						'4-nfdraws' => 0);
	return $f_init;		//return array with draws of non friend wins, 1 draw win 1 non friends wins
	}

	/**
	 * Calculate the Friends of the Lottery from Ball 1 to Ball N range, include the extra ball if TRUE. Based on the range of draws covered
	 * 
	 * @param 	string 	$name		specific lottery table name
	 * @param 	integer $max		maximum number of balls drawn
	 * @param	integer	$top		Maximum Ball drawn for this lottery. e.g. 49 in Lotto 649
	 * @param	boolean	$bonus		If an extra / bonus ball is included (1 = TRUE, 0 = False)
	 * @param	boolean $draws		If extra (bonus) draws are included in the calculation (1 = TRUE, 0 = FALSE)
	 * @param  	integer	$range		Range of number of draws (default is 100). If less than 100, the number must be set in $range
	 * @param 	string 	$last		last date to calculate for the draws, in yyyy-mm-dd format, it blank skip. useful to back in time through the draws
	 * @param 	boolean	$duple		Duplicate extra ball. FALSE by default.  The extra can have the same number drawn based on the minimum and maximum number drawn
	 * @return  string	$friends	Friends string in this format: 1>9=4:01/24/2020,2>11=8:09/18/2020,3>44=10:06/22/2019  ,etc. 
	 */
	public function friends_calculate($name, $max, $top, $bonus = 0, $draws = 0, $range = 100, $last = '', $duple = FALSE)
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
		$w .= (!empty($last)&&(!$draws) ? " AND draw_date <= '".$last."'" : "");
		$w .= (!empty($last)&&($draws) ? " WHERE draw_date <= '".$last."'" : "");  
		//$l = (!is_null($last) ? " WHERE draw_date <='".$last['draw_date']."'" : "");
		
		// Initialize and create blank associate array
		$friends = '';	// set as a blank string
		$nonfriends = '';	// set as a blank string
		$b = 1; // Number 1 to Number N from the size of the Lottery
		do
		{
			// Calculate
 			
			$sql = "SELECT t.* FROM (SELECT ".$s." FROM ".$name.$w." ORDER BY draw_date DESC LIMIT ".$range.") as t ORDER BY t.draw_date ASC;";
			// Execute Query
			$query = $this->db->query($sql);
			$row = $query->first_row('array'); // Doing the reverse to the first row because of the descending order.
			$friendlist = array();
			
			do {
				$blnExDup = ($bonus&&$duple&&($b==$row['extra']) ? TRUE : FALSE); // Has reached the extra number that is an independent and 
																				  // duplicate Extra ball (TRUE) or everything else is FALSE
				if($this->is_drawn($b, $row, $max, $bonus)&&(!$blnExDup))		  // Must always be FALSE to place on the friends list
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
		return $friends.'+'.$nonfriends;  // return friends+nonfriends (without the '|' at the end of non friends)
	}
	
	/**
	 * Determine the direction of a friendship, 
	 * 1> = 1 way frienship, current ball is a friend of the other ball but the other ball
	 * is not a friend of the current ball
	 * 2 = 2 way friendship, the current ball is friends with the other ball and the other
	 * ball is a friend of the current ball 
	 * @param 	string	$friendship		string format, ball1>count|last draw date,ball2>count|last draw date, etc.
	 * @param 	integer	$max			top ball drawn in the lottery, e.g. 49 in a 649
	 * @return	string	$direction		partial string format, ball>count|last draw date|1>, etc.
	 */
	private function friendship_direction($friendship,$max)
	{
		$other = array();
		$other = $this->extract_friends($friendship); // extract friendship string
		$direction = ''; // start with empty string
		// Find friendship direction
		$ball = 1;
		do
		{
			foreach($other as $items => $value)
			{
				if(($ball==$value)&&($other[$value]==$ball))
				{
					$direction .= '<>'.$other[$value];
				}
				elseif(($ball==$value)&&($other[$value]!=$ball))
				{
					$direction .= '>'.$other[$value];
				}
				elseif(($ball!=$value)&&($other[$value]==$ball))
				{
					$direction .= '>'.$other[$value];	
				}
				$direction .= ',';
				$ball++;
			}
		} while($ball<=$max);
	return $direction;
	}

	/**
	 * Return the added only list of friends of the ball drawn for this ball number
	 * 
	 * @param 	string	$fr			String of Friends to be extracted
	 * @return	array	$balls		Array of Balls that are friends of balls e.g. 1 to 49 balls
	 */
	private function extract_friends($fr)
	{
		$balls = array();
		$ball = 1;	//start at ball 1
		// $friend_array is ball>count|last draw date
		$friend_array = explode("|", $fr);
		
		foreach($friend_array as $items =>  $value)
		{
			$pos = strpos($value, '>'); // check an element with the '>' great than character
			if($pos===TRUE) $balls[$ball] = strstr($value, '>', TRUE); // Strip off each number
			$ball++;
		}
	return $balls;
	}

	/**
	 * Return the added only list of nonfriends of the balls drawn
	 * 
	 * @param 	string	$nfr		String of non Friends to be extracted
	 * @return	array	$nonfriends Array of Balls that are non friends for the set range
	 */
	private function extract_nonfriends($nfr)
	{
		// $friend_array is ball>count|last draw date
		$nonfriends_array = explode("|", $nfr);
	return $nonfriends_array;
	}

	/**
	 * Return the added only list of nonfriends of the balls drawn
	 * 
	 * @param 	array	$non		Associative array of the non friend global array
	 * @return	string	$nonfriends String of the non friend totals
	 */
	public function combine_nonfriends($non)
	{
		$nonfriends = '';
		foreach($non as $cat => $total)
		{
			$nonfriends .= $total.'|';
		}
	return substr($nonfriends, 0, -1);
	}

	/* Return the combined string of the relationship totals XX,XX,XX|>37,<>7,<>19,>42,>14,<>11,<>2,
	>3,>48,<>32,<>6,>17,<>47,>18,>48,>27,<>30,<5,<>3,<>44,>22,>25,>41,>17,<>48,>5,>19,>26,>5,<>17,>44,
	<>10,>35,>41,>48,>25,>9,<>41,>10,>47,<>38,>32,>6,<>20,>38,>31,<>13,<>25,>21
	* 
	* @param	array	$r				associative relationship array
	* @param 	string	$fr				String of friend directions 1 through the maximum ball drawn
	* @param 	integer	$max			Maximum ball drawn from the lottery
	* @return	string	$combined	 	Return the string as the example above
	*/
   public function combine_friends_string($r, $fr, $max)
   {
	$directions = '';	// empty string   
	// 
	$directions = $this->friendship_direction($fr, $max);
	$combined = '';
	foreach($r as $total => $value)
	{
		$combined .= $value.',';
	}
	$combined = substr($combined, 0, -1);

   return $combined.'|'.$directions;
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
		if(!$ex&&($ball==$row['extra'])) return $list; // Returns the array if the bonus is not included and the ball compared is the extra ball drawn
		if(!$ex) unset($row['extra']);				   // This totally eliminates the extra from the friend tabulation, as in, the independent and duplicate extra
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
		if(!$ex&&($ball==$row['extra'])) return $list;  // Returns the array if the bonus is not included and the ball compared is the extra ball drawn
		if(!$ex) unset($row['extra']);					// This totally eliminates the extra from the friend tabulation, as in, the independent and duplicate extra
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
	 * Calculate the Friendships that have NEVER hit, 1 way friendship counts and 2 way friendship counts
	 * Also calculate the number of non friendships that have occurred during each draw
	 * 
	 * @param 	array 	$str_fr			completed array string of the friends
	 * @param 	array 	$str_nfr		completed array string of the nonfriends
	 * @param 	string 	$name			specific lottery table name
	 * @param 	integer $max			maximum number of balls drawn
	 * @param	integer	$top			Maximum Ball drawn for this lottery. e.g. 49 in Lotto 649
	 * @param	boolean	$bonus			If an extra / bonus ball is included (1 = TRUE, 0 = False)
	 * @param	boolean $draws			If extra (bonus) draws are included in the calculation (1 = TRUE, 0 = FALSE)
	 * @param  	integer	$range			Range of number of draws (default is 100). If less than 100, the number must be set in $range
	 * @param 	string 	$last			last date to calculate for the draws, in yyyy-mm-dd format, it blank skip. useful to back in time through the draws
	 * @param 	boolean	$duple			Duplicate extra ball. FALSE by default.  The extra can have the same number drawn based on the minimum and maximum number drawn
	 * @return  none					none. Globals will be available from the controller
	 */
	public function friends_hits($str_fr, $str_nfr, $name, $max, $top, $bonus = 0, $draws = 0, $range = 100, $last = '', $duple = FALSE)
	{
			global $relatives;		// friendship array win totals for non friendship wins, 1 - way friendships and 2 way friendships
			global $nonrelatives;	// non friendship array win totals for non friendships, 1 non-friendship win occurence, 2 non-friendship
									// win occurences, 3 non-friendship win occurences, and 4 non-friendship win occurrences  
			$friends = array();		// Index array of friends
			$nonfriends = array();	// Associative  array of non friends
			$friends = $this->extract_friends($str_fr);
			$nonfriends = $this->extract_nonfriends($str_nfr);
			// Build Query
			$s = 'ball'; 
			/* Part 1 */
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
			$w .= (!empty($last)&&(!$draws) ? " AND draw_date <= '".$last."'" : "");
			$w .= (!empty($last)&&($draws) ? " WHERE draw_date <= '".$last."'" : "");  
			$b = 1; // Number 1 to Number N from the size of the Lottery
			do
			{
				// Calculate
				$sql = "SELECT t.* FROM (SELECT ".$s." FROM ".$name.$w." ORDER BY draw_date DESC LIMIT ".$range.") as t ORDER BY t.draw_date ASC;";
				// Execute Query
				$query = $this->db->query($sql);
				$row = $query->first_row('array'); // Doing the reverse to the first row because of the descending order.
				do {
					$blnExDup = ($bonus&&$duple&&($b==$row['extra']) ? TRUE : FALSE); 	// Has reached the extra number that is an independent and 
																						// duplicate Extra ball (TRUE) or everything else is FALSE
					if($this->is_drawn($b, $row, $max, $bonus)&&(!$blnExDup))			// Must always be FALSE to place on the friends list
					{
						// a hit has been found
						$row = $query->next_row('array'); // Go to the next draw for examination
						if(!is_null($row))
						{
							$relatives = $this->friends_hitcounts($relatives,$friends,$b,$row,$bonus,$duple);
							$nonassociates = $this->nonfriends($nonfriends, $b);
							$nonrelatives = $this->nonfriends_hitcounts($nonrelatives,$nonassociates,$row,$bonus,$duple);
						}
					}
					else
					{
						$row = $query->next_row('array');
					}
				} while(!is_null($row)); // Do until all draws complete
			$b++;
			unset($friendlist);		// Destroy the old friendlist
			$query->free_result();	// Removes the Memory associated with the result resource ID
		} while ($b<=$top);
		unset($friends);		// Destroy the old friendlist
		unset($nonfriends);
		$query->free_result();	// Removes the Memory associated with the result resource ID
	}

	/**
	* Return the non existent friends after the current draw
	* @param	array	$list		Associative Array of followers and the counts
	* @param	integer	$bl			Current Ball examined with the nonfriends. 
	* @return	array	$non		Return the array of all non friends in the current range
	*/
	private function nonfriends($list, $bl)
	{
		$non = array();
		$non = explode(",", $list[$bl]);
	return $non;	// Return the non-friends
	}

	/**
	* Return the relationships of a friend, e.g. # of non-friendship draws, # of 1-way friendships draws, #2 of 2-way friendships draws
	* @param	array	$rel		Associative Array of relatives for different friendships
	* @param	array	$fr			Index Array of current friends
	* @param	array	$rw			Current associative array of the next drawn numbers.
	* @param	boolean	$b			Bonus Flag, 0 = No Bonus / Extra, 1 = No Bonus / Extra Included
	* @param	boolean	$d			Duplicate Flag, 0 = No Duplicate, 1 = Duplicate Extra Ball
	* @return	array	$rel		Return Array of updated relatives
	*/
	private function friends_hitcounts($rel, $fr, $rw, $b, $d)
	{
		// $fl is the search array
		$blnfound = FALSE;
		foreach($rw as $position => $ball)
		{
			if(array_key_exists($ball,$fr)&&($b)&&((!$d||($d&&$position!='extra'))))
			{
				$blnfound = TRUE;
				// Two way
				if(isset($fl[$ball])) ++$rel['two-way'];
				elseif(!isset($fl[$ball])) ++$rel['one-way'];
				// no friends
			}
		}
		if(!$blnfound) ++$rel['nofriends']; 
	return $rel;	// Return the friendship relationship counts.
	}

	/**
	* Return the non friendships that were drawn (totals). Number of 0 draws with no friendships,
	* Draws with only 1 non-friend in the draw, 2 non-friends in the draw, 3 non-friends in the draw
	* or 4 non-friends in the draw
	* @param	array	$nonrel		Associative Array of non relatives and the counts
	* @param	array	$nonfl		index Array of current non followers
	* @param	array	$rw			Current next row of drawn numbers. Compared with the current followers
	* @param	boolean	$b			Bonus Flag, 0 = No Bonus / Extra, 1 = No Bonus / Extra Included
	* @param	boolean	$d			Duplicate Flag, 0 = No Duplicate lottery, 1 = Duplicate Extra Ball lottery
	* @return	array	$nonrel		Return Associated Array of non relatives updated 
	*/
	private function nonfriends_hitcounts($nonrel, $nonfl, $rw, $b, $d)
	{
		$hit_counter=0;	// Non - Friend Counter
		
		foreach($rw as $drawn => $ball)
		{
			if(array_key_exists($ball,$nonfl)&&($b)&&(!$d||($d&&$drawn!='extra')))
			{
				++$hit_counter;
			}
		}
		switch($hit_counter) 
			{
				case 0:
					if(array_key_exists('0-nfdraws', $nonrel)) ++$nonrel['0-nfdraws']; 
					break;
				case 1:
					if(array_key_exists('1-nfdraws', $nonrel)) ++$nonrel['1-nfdraws'];			
					break;
				case 2:
					if(array_key_exists('2-nfdraws', $nonrel)) ++$nonrel['2-nfdraws'];
					break;
				case 3:
					if(array_key_exists('3-nfdraws', $nonrel)) ++$nonrel['3-nfdraws'];
					break;
				case 4:
					if(array_key_exists('4-nfdraws', $nonrel)) ++$nonrel['4-nfdraws'];
			}
	return $nonrel;	// Return the non-friends
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
	 * @param 	boolean			$duple		Duplicate extra ball. FALSE by default.  The extra can have the same number drawn based on the minimum and maximum number drawn
	 * @param	string			$last		last date to calculate for the draws, in yyyy-mm-dd format, it blank skip. useful to back in time through the draws
	 * @return	string 			$hwc_string	returns as key value pairs with the number and the heat number.  Numbers are returned based on their heat value
	 * 										e.g. 4 as a key and 58 as the value for heat in the given range and date	
	 */
	public function h_w_c_calculate($lotto_tbl, $picks, $bonus = 0, $draws = 0, $range = 0, $w, $c, $last = '', $duple = FALSE)
	{
		// Build query
		$sql_range = ($range ? ' ORDER BY draw_date DESC LIMIT '.$range : ' ORDER BY draw_date DESC');
		$sql_date = '';
		$sql_draws = '';
		if (!empty($last)&&($draws)&&(!$bonus))
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
		if($bonus&&!$duple) $sql .= (!empty($last) ? ' UNION ALL (SELECT extra as ball_drawn FROM '.$lotto_tbl.' WHERE extra <> "0"'.$sql_date.$sql_range.')'
		: ' UNION ALL (SELECT extra as ball_drawn FROM '.$lotto_tbl.' WHERE extra <> "0"'.$sql_range.')');
		$sql .= ') as hwc
				GROUP BY ball_drawn
				ORDER BY heat DESC;';
		/* EXAMPLE: SELECT ball_drawn, count(*) as heat FROM ((SELECT ball1 as ball_drawn FROM lotto_max 
		WHERE draw_date <= '2022-01-25' ORDER BY draw_date DESC LIMIT 10) UNION ALL (SELECT ball2 
		as ball_drawn FROM lotto_max WHERE draw_date <= '2022-01-25' ORDER BY draw_date DESC LIMIT 10) 
		UNION ALL (SELECT ball3 as ball_drawn FROM lotto_max WHERE draw_date <= '2022-01-25' ORDER BY 
		draw_date DESC LIMIT 10) UNION ALL (SELECT ball4 as ball_drawn FROM lotto_max WHERE draw_date <= 
		'2022-01-25' ORDER BY draw_date DESC LIMIT 10) UNION ALL (SELECT ball5 as ball_drawn FROM lotto_max WHER E draw_date 
		<= '2022-01-25' ORDER BY draw_date DESC LIMIT 10) UNION ALL (SELECT ball6 as ball_drawn FROM lotto_max WHERE draw_date 
		<= '2022-01-25' ORDER BY draw_date DESC LIMIT 10) UNION ALL (SELECT ball7 as ball_drawn FROM lotto_max WHERE draw_date 
		<= '2022-01-25' ORDER BY draw_date DESC LIMIT 10)) as hwc GROUP BY ball_drawn ORDER BY heat DESC;
		*/
		//var_dump($sql); exit(1); 
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
	 * Returns the list of extras only (as a separate set of numbers) for a given range and date
	 * 
	 * @param	string			$lotto_tbl	Table of Lottery
	 * @param	boolean			$bonus		Bonus / Extra ball included in query. 0 = no, 1 = yes
	 * @param	boolean			$draws		Extra Draws (without the the extra ball included) in the query. 
	 * @param	integer			$range		Range of draws to calculate, 10 draws, 100 draws, 200 draws, etc.
	 * @param	string			$last		last date to calculate for the draws, in yyyy-mm-dd format, it blank skip. useful to back in time through the draws
	 * @return	string 			$xtra_string returns as key value pairs with the number and the heat number.  Numbers are returned based on their heat value
	 */
	public function hwc_duple_extra($lotto_tbl, $bonus = 0, $draws = 0, $range = 0, $last = '')
	{
		// Build query
		$sql_range = ($range ? ' ORDER BY draw_date DESC LIMIT '.$range : ' ORDER BY draw_date DESC');
		$sql_date = '';
		$sql_draws = '';
		if (!empty($last)&&($draws)&&(!$bonus))
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
				FROM ((SELECT extra as ball_drawn FROM '.$lotto_tbl.$sql_draws.$sql_date.$sql_range.')';
				$sql .= ') as hwc
				GROUP BY ball_drawn
				ORDER BY heat DESC;';
			$query = $this->db->query($sql);
			
			$xtra_string = "";
			foreach ($query->result() as $xtra)
			{
				$xtra_string .= $xtra->ball_drawn.'='.$xtra->heat.",";
			}
	return substr($xtra_string, 0, -1);	// Return the extra string without the last comma in the string	
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

		$select = "SELECT `draw_date` FROM `".$ld."`";

		foreach($arr_hots as $ahot)
		{
 			$heat = explode('=', $ahot);
			$due = intval(round(($range / $heat[1]))); // Round to nearest whole number
			if($max>=3)
			{
				$where = " WHERE (ball1='".$heat[0]."' OR ball2='".$heat[0]."' OR ball3='".$heat[0]."'";
			}
			if($max>=4)
			{
				$where .= " OR ball4='".$heat[0]."'";
			}
			if($max>=5)
			{
				$where .= " OR ball5='".$heat[0]."'";
			}
			if($max>=6)
			{
				$where .= " OR ball6='".$heat[0]."'";
			}
			if($max>=7)
			{
				$where .= " OR ball7='".$heat[0]."'";
			}
			if($max>=8)
			{
				$where .= " OR ball8='".$heat[0]."'";
			}
			if($max==9)
			{
				$where .= " OR ball9='".$heat[0]."'";
			}
			if($bonus) // Only include bonus if set
			{
				$where .= " OR extra='".$heat[0]."'";
			}
			$where .=")";	// Close off the bracket
			$limit = " ORDER BY `draw_date` DESC LIMIT 1";
			// Query Build
			$sql_draws = (!$draws ? " AND extra <> '0'": "" ); 	// If no extra draws are included, the extra ball is usually zero.
			$sql = $select.$where.$sql_draws.$limit;
			$query = $this->db->query($sql);
			$found_date = $query->row()->draw_date;
			$query->free_result(); 								// The $query result object will no longer be available
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
				$where = " WHERE (ball1='".$heat[0]."' OR ball2='".$heat[0]."' OR ball3='".$heat[0]."'";
			}
			if($max>=4)
			{
				$where .= " OR ball4='".$heat[0]."'";
			}
			if($max>=5)
			{
				$where .= " OR ball5='".$heat[0]."'";
			}
			if($max>=6)
			{
				$where .= " OR ball6='".$heat[0]."'";
			}
			if($max>=7)
			{
				$where .= " OR ball7='".$heat[0]."'";
			}
			if($max>=8)
			{
				$where .= " OR ball8='".$heat[0]."'";
			}
			if($max==9)
			{
				$where .= " OR ball9='".$heat[0]."'";
			}
			if($bonus) // Only include bonus if set
			{
				$where .= " OR extra='".$heat[0]."'";
			}
			$where .=")";	// Close off the bracket
			$limit = " ORDER BY `draw_date` DESC LIMIT 1";
			// Query Build
			$sql_draws = (!$draws ? " AND extra <> '0'": "" ); 	// If no extra draws are included, the extra ball is usually zero.
			$sql = $select.$where.$sql_draws.$limit;
			$query = $this->db->query($sql);
			$found_date = $query->row()->draw_date;
			$query->free_result(); // The $query result object will no longer be available
			
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
				$where = " WHERE (ball1='".$heat[0]."' OR ball2='".$heat[0]."' OR ball3='".$heat[0]."'";
			}
			if($max>=4)
			{
				$where .= " OR ball4='".$heat[0]."'";
			}
			if($max>=5)
			{
				$where .= " OR ball5='".$heat[0]."'";
			}
			if($max>=6)
			{
				$where .= " OR ball6='".$heat[0]."'";
			}
			if($max>=7)
			{
				$where .= " OR ball7='".$heat[0]."'";
			}
			if($max>=8)
			{
				$where .= " OR ball8='".$heat[0]."'";
			}
			if($max==9)
			{
				$where .= " OR ball9='".$heat[0]."'";
			}
			if($bonus) // Only include bonus if set
			{
				$where .= " OR extra='".$heat[0]."'";
			}
			$where .=")";	// Close off the bracket
			$limit = " ORDER BY `draw_date` DESC LIMIT 1";
			// Query Build
			$sql_draws = (!$draws ? " AND extra <> '0'": "" ); 	// If no extra draws are included, the extra ball is usually zero.
			$sql = $select.$where.$sql_draws.$limit;
			$query = $this->db->query($sql);
			$found_date = $query->row()->draw_date;
			$query->free_result(); // The $query result object will no longer be available
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

	/* Returns only the last draw id from the selected lottery database
	 * 
	 * @param	string			$tbl				Current Lottery Data Table Name
	 * @return	integer			$id					Returns only the last draw id or FALSE that it does not exist in the lottery draws database
	 */
	public function last_id($tbl)
	{	
		if (!$this->lotteries_m->lotto_table_exists($tbl)) return FALSE;	// Draw Database Does not Exist
		$draw = $this->db_row($tbl, 0);		
		
		if (isset($draw))
		{
			$id = $draw->id;
		}
		else return FALSE;
	return $id;	// Return only the draw id of the most recent draw
	}
	/**
	 * Validates that the H-W-C, Followers and Friends have not been updated to the latest draw (TRUE) or have been updated (FALSE)
	 * Will check all statistics methods, H-W-C, Followers and Friends. All the draw ids must match but the latest draw id of the lottery
	 * draws don't have to match. This will trigger the recalc for H-W-C, followers and friends.
	 * 
	 * @param	integer		$lt_id			Lottery id to reference
	 * @param 	integer		$ref			$id of last draw that had the statistics of the draw calculated
	 * @return	boolean		TRUE / FALSE	All 3 tables have the same reference, return TRUE for nothing to do, FALSE requires a recalc of all 3 tables		
	 */
	public function recalc_update($lt_id, $ref)
	{
		if(!$ref) return FALSE;
		$this->db->reset_query();
		$query = $this->db->query("SELECT t1.draw_id FROM lottery_h_w_c t1 
		JOIN lottery_followers t2 JOIN lottery_friends t3 
		ON (t1.draw_id=t2.draw_id)&&(t1.draw_id=t3.draw_id) WHERE t1.draw_id='".$ref."' && t1.lottery_id='".$lt_id."';");
	return (isset($query->row) ? FALSE : TRUE);
	}
	/** 
	* Returns the number of hots, warms, colds in the group as the h_count, w_count and c_count
	* 
	* @param 	integer	$id		Lottery id
	* @return   array	$size	Returns the hot dimenion, warm dimension and cold dimension as an array
	*/
	public function hwc_size($id)
	{
		$size = array();
		$this->db->reset_query();
		$query = $this->db->query('SELECT `h_count`, `w_count`, `c_count` FROM `lottery_h_w_c` WHERE `lottery_id` = "'.$id.'";');
		$size = $query->row_array();

	return $size;
	}

	/** 
	* Returns the positions that were drawn based on the heat level
	* 
	* @param 	array	$drawn_array	Current Drawn Numbers
	* @param  	array	$heat_array		Grouped Numbers based on heat level (hot, warm, colds)
	* @param	array	$position_array Old Position totals prior to the next drawn numbers
	* @param	boolean	$b	 			Bonus / Extra Flag, 0 = False. 1 = True
	* @param	boolean	$e 				Extra Draws Flags, 0 = False, 1 = Treue
	* @param	boolean	$dp				Duplicate Extra Ball Flag, 0 = False, 1 = True
	* @return	array	$position_array	Returns the updated positional hits after the next drawn numbers have been checked
	*/
	public function positions($drawn_array, $heat_array, $position_array, $b, $e, $dp)
	{

	if (!$b||$dp||$e) 								// If a bonus number, extra ball or duplicate extra number 
	{
		$key = array_key_last($drawn_array); 		//  Return the key from the drawn_array	
		unset($drawn_array[$key]); 					// Remove the last element from the drawn numbers
	}

	foreach ($drawn_array as $draw_pos => $ball)
	{
		foreach($heat_array as $pos => $heat)
		{
			if($heat==$ball) 
			{
				$position_array[$pos]++;			// Update the counter for that position
				break;
			}
		}		
	}
	
	return $position_array; // updated positions after the last draw has been validated.
	}
	/** 
	* Returns the formatted stroing of hots, warms, colds. Positions with a count of 0 are eliminated and will be decoded as such.
	* 
	* @param 	array	$h				Hot Numbers array
	* @param  	array	$w				Warm Numbers array
	* @param	array	$c				Cold Numbers array
	* @return	string	$str_positions	Returns the formated string
	*/
	public function position_string($h, $w, $c)
	{
		$str_positions = 'H>';

		foreach($h as $position => $count)
		{
			if($count!=0) $str_positions .= $position.'='.$count.',';
		}
		$str_positions = substr($str_positions,0,-1); // Remove last ','
		$str_positions .= '|W>';
		foreach($w as $position => $count)
		{
			if($count!=0) $str_positions .= $position.'='.$count.',';
		}
		$str_positions = substr($str_positions,0,-1); // Remove last ','
		$str_positions .= '|C>';
		foreach($c as $position => $count)
		{
			if($count!=0) $str_positions .= $position.'='.$count.','; 
		}
		$str_positions = substr($str_positions,0,-1); // Remove last ','
	return $str_positions; // formatted string returned
	}
}