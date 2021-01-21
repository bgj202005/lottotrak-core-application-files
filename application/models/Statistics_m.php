<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statistics_m extends MY_Model
{
	protected $_table_name = 'lottery_stats';
	protected $_order_by = 'id';
	
	/**
	 * Validates that statistics are available from the Retrieves all the Statistics per draw
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
	 * Returns the first id number in the table
	 * 
	 * @param	string	$table			Actual Name of the Table
	 * @return	string	id				Returns the starting index id from the lottery table.		
	 */
	public function lottery_start_id($table)
	{
		$this->db->reset_query();	// Clear any previous queries that are cached
		$this->db->select_min('id');	// Target is the index id.
		return $this->db->get($table);	
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
	 * @param string  			$table		Converted Lottery Table Name provided from lottery_table_convert
	 * @param integer 			$id			Current index id to be updated in
	 * @param array 			$balls		Associative array of the draw with, draw date, balls 1 ... N with/without extra/bonus ball
	 * @return integer/boolean  	index #id or FALSE 	Success or Failure on updating the statistics portion of the draw			
	 */
	public function lottery_draw_update($table, $id, $balls)
	{
		// Validate the $id with the draw date
		// If $id does not exist, (if this incremental index id was deleted, for example), use the id based on the draw date of the draw
		$query = $this->db->select('id')
				->where('draw_date', $balls['draw_date']) // This draw date must exist!!
				->order_by('id ASC')
				->limit(1,0)
				->get($table);	// Retrieve the id and only return 1 row
				
		$row = $query->row();

		if ($row->id!=$id) $id = $row->id;  // Draw index id does not match current one since we validated the actual draw date to retrieve the id

		unset($balls['id']);	// Can not update the index id, as a duplicate error will result
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
	 * @return	integer /'NA'  $sum / string value	Returns the sum from the last draw or NA if the draw database does not exist		
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
	 * @param	string			$tbl				Current Lottery Data Table Name
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
		$duplicates = array_intersect_assoc($current, $previous); // Compare for the simularites

		if(!empty($duplicates)) 
		{
			$s = "";
			foreach($duplicates as $duplicate)
			{
				$s .= $duplicate." ";
			}
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
	 * @return	object 			last row, previous row or next row depending on the row value of lottery records		
	 */
	public function db_row($tbl, $row = 0)
	{	
		$query = $this->db->query('SELECT * FROM '.$tbl. ' ORDER BY `id` DESC LIMIT 100');
		
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
	 * Returns the Average Sum of draws based on a given Range of Draws
	 * 
	 * @param	array	$tbl 		Current Draws for Calculation
	 * @param 	integer $max		maximum number of balls drawn
	 * @param 	integer	$range		Number of the number of draws to calculate from the latest draw
	 * @return	integer $ave_sum		Returns the Average sum of the drawn numbers		
	 */
	public function lottery_average_sum($tbl, $max = 3, $range = 10)
	{
		$this->db->reset_query();	// Clear any previous queries that are cached
		
		$sql = "";
		$ave_sum = 0;
		switch ($max)
		{
			case 3:
				$sql = "SELECT (ball1+ball2+ball3)";
			case 4:
				$sql .= "SELECT (ball1+ball2+ball3+ball4)";
				break;
			case 5:
				$sql .= "SELECT (ball1+ball2+ball3+ball4+ball5)";
				break;
			case 6:	
				$sql .= "SELECT (ball1+ball2+ball3+ball4+ball5+ball6)";
				break;
			case 7:	
				$sql .= "SELECT (ball1+ball2+ball3+ball4+ball5+ball6+ball7)";
				break;
			case 8:
				$sql .= "SELECT (ball1+ball2+ball3+ball4+ball5+ball6+ball7+ball8)";
				break;
			case 9:
				$sql .= "SELECT (ball1+ball2+ball3+ball4+ball5+ball6+ball7+ball8+ball9)";
		} 

		$sql .= " as sum FROM ".$tbl;
		$sql .= " ORDER BY id DESC ";
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
	 * @param	array				Current Draw Array
	 * @param 	integer $max		maximum number of balls drawn
	 * @return	integer $sum		Returns the sum of the digits of the drawn numbers		
	 */
	public function lottery_average_sumdigits($tbl, $max = 3, $range = 10)
	{
		$sum = 0; // Initialize to total sum to 0
		
		$this->db->reset_query();	// Clear any previous queries that are cached
		$query = $this->db->select('*')
				->order_by('id DESC')
				->limit($range)
				->get($tbl);	// Retrieve the id and only return 1 row
		
		if(!$query) return FALSE;
		foreach ($query->result_array() as $row)
		{
			$total = 0;
			$n = 1;
			$t = $max;
			do
			{
				$total = (intval($row['ball'.$n]) < 10 ? $sum+intval($row['ball'.$n]): $sum+(intval(substr($row['ball'.$n],0,1)))+(intval(substr($row['ball'.$n],1,1))));
				$n++;
				$t--;
			} while($t>0);
			$sum = $total;
		}
		$sum = $sum / $range; // Get the Average of the sums of the individual digits
		
	return (integer) round($sum,0);
	}
	/**
	 * Return only the average even number of balls drawn for a range of draws
	 * 
	 * @param	string	$tbl		Name of Lottery Table
	 * @param 	integer $max		maximum number of balls drawn
	 * @param 	integer $range		Range of Draws to Calculate, 10, 100, 500, etc.
	 * @return	integer $evens		Returns the even number of drawn numbers on average		
	 */
	public function lottery_average_evens($tbl, $max = 3, $range = 10)
	{
		$this->db->reset_query();	// Clear any previous queries that are cached
		$sql = "ball1, ball2, ball3";
		switch ($max)
		{
			case 4:
				$sql .= ", ball4,";
				break;
			case 5:
				$sql .= ",ball4, ball5";
				break;
			case 6:	
				$sql .= ", ball4, ball5, ball6";
				break;
			case 7:	
				$sql .= ", ball4, ball5, ball6, ball7";
				break;
			case 8:
				$sql .= ", ball4, ball5, ball6, ball7, ball8";
				break;
			case 9:
				$sql .= ", ball4, ball5, ball6, ball7, ball8, ball9";
		} 

		$query = $this->db->select($sql)
				->order_by('id DESC')
				->limit($range)
				->get($tbl);	// Retrieve the id and only return 1 row

		$evens = 0;
		$temp = $max;	// Tempoarily store the number of drawn balls
		foreach ($query->result_array() as $row)
		{
			$n = 1;
			do
			{
				if(!intval($row['ball'.$n]%2)) $evens++;
				$n++;
				$max--;
			} 
			while($max>0);
			$max = $temp;  // Recover the number of Drawn Balls
		}

		$evens = $evens / $range; // Get the Average of Even Numbers over a given range
		
	return (integer) round($evens,0);
	}
	/**
	* Return only the average odd number of balls drawn for a range of draws
	 * 
	 * @param	string	$tbl		Name of Lottery Table
	 * @param 	integer $max		maximum number of balls drawn
	 * @param 	integer $range		Range of Draws to Calculate, 10, 100, 500, etc.
	 * @return	integer $odds		Returns the odd number of drawn numbers on average			
	 */
	public function lottery_average_odds($tbl, $max = 3, $range = 10)
	{
		$this->db->reset_query();	// Clear any previous queries that are cached
		$sql = "ball1, ball2, ball3";
		switch ($max)
		{
			case 4:
				$sql .= ", ball4,";
				break;
			case 5:
				$sql .= ",ball4, ball5";
				break;
			case 6:	
				$sql .= ", ball4, ball5, ball6";
				break;
			case 7:	
				$sql .= ", ball4, ball5, ball6, ball7";
				break;
			case 8:
				$sql .= ", ball4, ball5, ball6, ball7, ball8";
				break;
			case 9:
				$sql .= ", ball4, ball5, ball6, ball7, ball8, ball9";
		} 

		$query = $this->db->select($sql)
				->order_by('id DESC')
				->limit($range)
				->get($tbl);	// Retrieve the id and only return 1 row

		$odds = 0;
		$temp = $max;	// Tempoarily store the number of drawn balls
		foreach ($query->result_array() as $row)
		{
			$n = 1;
			do
			{
				if(intval($row['ball'.$n]%2)) $odds++;
				$n++;
				$max--;
			} 
			while($max>0);
			$max = $temp;  // Recover the number of Drawn Balls
		}

		$odds = $odds / $range; // Get the Average of Even Numbers over a given range
		
	return (integer) round($odds,0);
	}
	/**
	 * Return the range of numbers drawnn for the current drawn numbers array
	 * 
	 * @param	string	$tbl		Name of Lottery Table
	 * @param 	integer $max		maximum number of balls drawn
	 * @param 	integer $range		Range of Draws to Calculate, 10, 100, 500, etc.
	 * @return	integer $range		Returns the range of drawn numbers		
	 */
	public function lottery_average_range($tbl, $max = 3, $range = 10)
	{
		$this->db->reset_query();	// Clear any previous queries that are cached
		$select = "ball1";				// Only the first ball drawn
		switch ($max)				
		{  							// Only the last ball is required for the range
			case 3:
				$select .= ", ball3,";
				break;
			case 4:
				$select .= ", ball4,"; 
				break;
			case 5:
				$select .= ", ball5"; 
				break;
			case 6:	
				$select .= ", ball6"; 
				break;
			case 7:	
				$select .= ", ball7";
				break;
			case 8:
				$select .= ", ball8";
				break;
			case 9:
				$select .= ", ball9";
		} 
		$query = $this->db->select($select)
				->order_by('id DESC')
				->limit($range)
				->get($tbl);
		if (!$query) return FALSE;	
		
		$diff = 0;		// Set the difference between the first ball and last ball
		$n = $max;
		foreach ($query->result_array() as $row)
		{
			$diff = (intval($row['ball'.$n])-intval($row['ball1'])) + $diff;
		}
		$diff = $diff / $range; // Get the Average of Even Numbers over a given range
		
	return (integer) round($diff,0); // Return the range of balls drawn from the first ball to ball N	
	}
	/**
	 * Returns the number the Aveage maximum drawn numbers that are repeating in the same decade (more than 2 must be in the same decade) and only return the largest number
	 * 
	 * @param	string	$tbl		Name of Lottery Table
	 * @param 	integer $max		maximum number of balls drawn
	 * @param 	integer $range		Range of Draws to Calculate, 10, 100, 500, etc.
	 * @return	integer $dec_ave	Returns the average maximum number of decades during a range
	 **/	
	public function lottery_average_decade($tbl, $max = 3, $range = 10)
	{
		$this->db->reset_query();	// Clear any previous queries that are cached
		$sql = "ball1, ball2, ball3";
		switch ($max)
		{
			case 4:
				$sql .= ", ball4,";
				break;
			case 5:
				$sql .= ",ball4, ball5";
				break;
			case 6:	
				$sql .= ", ball4, ball5, ball6";
				break;
			case 7:	
				$sql .= ", ball4, ball5, ball6, ball7";
				break;
			case 8:
				$sql .= ", ball4, ball5, ball6, ball7, ball8";
				break;
			case 9:
				$sql .= ", ball4, ball5, ball6, ball7, ball8, ball9";
		} 

		$query = $this->db->select($sql)
				->order_by('id DESC')
				->limit($range)
				->get($tbl);	// Retrieve the id and only return 1 row
		$dec_ave = 0;
		$temp = $max;
		
		foreach ($query->result_array() as $row)
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
				if(intval($row['ball'.$n])<10) $decade1++;
				elseif(intval($row['ball'.$n])<20) $decade2++;
				elseif(intval($row['ball'.$n])<30) $decade3++;
				elseif(intval($row['ball'.$n])<40) $decade4++;
				elseif(intval($row['ball'.$n])<50) $decade5++;
				elseif(intval($row['ball'.$n])<60) $decade6++;
				elseif(intval($row['ball'.$n])<70) $decade7++;
				elseif(intval($row['ball'.$n])<80) $decade8++;
				elseif(intval($row['ball'.$n])<90) $decade9++;
				$n++;   
				$max--;
			} while($max>0);
			$max = $temp;

			$decades = $decade1;	// Start with the first decade 0 - 9
			if($decades<$decade2) $decades = $decade2;	// 10 - 19
			if($decades<$decade3) $decades = $decade3;	// 20 - 29
			if($decades<$decade4) $decades = $decade4;	// 30 - 39
			if($decades<$decade5) $decades = $decade5;	// 40 - 49
			if($decades<$decade6) $decades = $decade6;	// 50 - 59
			if($decades<$decade7) $decades = $decade7;	// 60 - 69
			if($decades<$decade8) $decades = $decade8;	// 70 - 79
			if($decades<$decade9) $decades = $decade9;	// 80 - 89
			$dec_ave = $dec_ave+$decades;	// Add the commulative total for $decades
		}
		
		$dec_ave = $dec_ave / $range;		// Caclulate the average maximum decade over a range
		
	return (integer) round($dec_ave,0); // Returns the average maximum decade rounded off
	}
	/**
	 * Returns the number of maximum number of repeating last digits for the current drawn numbers
	 * 
	 * @param	string	$tbl		Name of Lottery Table
	 * @param 	integer $max		maximum number of balls drawn
	 * @param 	integer $range		Range of Draws to Calculate, 10, 100, 500, etc.
	 * @return	integer $last_ave	Returns the average maximum same last drawn digits for a given range
	 **/
	public function lottery_average_last($tbl, $max = 3, $range = 10)
	{
		$this->db->reset_query();	// Clear any previous queries that are cached
		$sql = "ball1, ball2, ball3";
		switch ($max)
		{
			case 4:
				$sql .= ", ball4,";
				break;
			case 5:
				$sql .= ",ball4, ball5";
				break;
			case 6:	
				$sql .= ", ball4, ball5, ball6";
				break;
			case 7:	
				$sql .= ", ball4, ball5, ball6, ball7";
				break;
			case 8:
				$sql .= ", ball4, ball5, ball6, ball7, ball8";
				break;
			case 9:
				$sql .= ", ball4, ball5, ball6, ball7, ball8, ball9";
		} 

		$query = $this->db->select($sql)
				->order_by('id DESC')
				->limit($range)
				->get($tbl);	// Retrieve the id and only return 1 row
		$last_ave = 0;

		$temp = $max;

		foreach ($query->result_array() as $row)
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
			{	if(intval($row['ball'.$n])<9) $row['ball'.$n] = '0'.$row['ball'.$n];
				if(substr($row['ball'.$n],1,1)=='0') $zeros++;
				if(substr($row['ball'.$n],1,1)=='1') $ones++;
				if(substr($row['ball'.$n],1,1)=='2') $twos++;
				if(substr($row['ball'.$n],1,1)=='3') $threes++;
				if(substr($row['ball'.$n],1,1)=='4') $fours++;
				if(substr($row['ball'.$n],1,1)=='5') $fives++;
				if(substr($row['ball'.$n],1,1)=='6') $sixs++;
				if(substr($row['ball'.$n],1,1)=='7') $sevens++;
				if(substr($row['ball'.$n],1,1)=='8') $eights++;
				if(substr($row['ball'.$n],1,1)=='9') $nines++;
				$n++;
				$max--;
			} while($max>0);
			$max = $temp;
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
			$last_ave = $last_ave+$last;		// Add the commulative total for $decades
		}

		$last_ave = $last_ave / $range;		// Caclulate the average maximum last digit over a given range
		
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
	 * If existing Record for the Followers table exist
	 * 
	 * @param 	string 	$name		specific lottery table name
	 * @param	array	$last		$last drawn numbers (index, date, ball1 ... ball N, Extra (Bonus ball), lottery id)
	 * @param 	integer $max		maximum number of balls drawn
	 * @param	boolean	$bonus		If an extra / bonus ball is included (1 = TRUE, 0 = False)
	 * @param  	integer	$range		Range of number of draws (default is 100). If less than 100, the number must be set in $range
	 * @return  string	$followers	Followers string in this format that follow with the number of occurrences (minumum 3 Occurrences)
	 * 								e.g. 10=>3=4|22=3,17=>10=5|37=4|48=4
	 */
	public function followers_calculate($name, $last, $max, $bonus, $range = 100)
	{
		// Build Query
		$s = 'ball'; 
		$i = 1; 	// Default Ball 1
		if($range>100) $range++; 	// Add one to range to eliminate the draw we are comparing with the records only if the number of draws are 100 or more.
		do
		{	
			$s .= $i;
			$i++;
			if($i<=$max) $s .= ', ball';
		} 
		while($i<=$max);

		if($bonus) $s .= ', extra';
		
		// Calculate
		$b = 1; // ball 1
		// Initialize and create blank associate array
		$followers = '';	// set as a blank string
		do
		{
			$sql = "SELECT ".$s." FROM ".$name." WHERE id <>".$last['id']." ORDER BY id DESC LIMIT ".$range; 
			// Execute Query
			$query = $this->db->query($sql);
			$row = $query->first_row('array');
			$c_b = $last['ball'.$b]; // Current ball to investigate
			$followlist = array();
			do {

				if($this->is_drawn($c_b, $row, $max))
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
		$followers .= (!empty($followlist) ? $this->follower_string($c_b, $followlist) : ''); // Empty Set? Then Skip
		// while not out of range
		// Return $follower number associative numbers that have 3 and above in this format, save in this format e.g. ball drawn 10 => 22=3,37=4,42=4
		// update ball counter
		// while ball count < $max
			$b++;
			if(($b<$max)&&(!empty($followlist))) $followers .= ',';
			unset($followlist);		// Destroy the old followerlist
			$query->free_result();	// Removes the Memory associated with the result resource ID
		} while ($b<$max);
		return substr($followers, 0, -1);
	}
	/**
	 * Return if the drawn number was drawn from the current row
	 * 
	 * @param	integer	$num		Drawn number from the most recent draw
	 * @param 	array 	$curr		Current set of drawn numbers
	 * @param	integer	$pick		How many numbers are drawn without the extra / bonus ball		
	 * @return	boolean $found		Found the ball drawn during this draw
	 */
	private function is_drawn($num, $curr, $pick)
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
		} while($i<$pick);
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
		foreach($row as $balls_drawn => $key)
		{
			$list += array(
				$key => 1); 
		}
	return $list;		// Return the followers of the current draw
	}
	/**
	 * Return if the drawn number was drawn from the current row
	 * 
	 * @param	array	$list		List of followers and the totals
	 * @param	array	$row		Current Draw to compare and update		
	 * @return	array	$list		List of updated followers
	 */
	private function update_followers($list, $row)
	{
		foreach($row as $balls_drawn => $key)
		{
			if(array_key_exists($key, $list))
			{
				$list[$key]++;	// Auto increment the array from the $key

			}
			else
			{
				$list += array(		// If it does not exist, add the key and set the value to one.
					$key => 1);
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
		foreach($list as $follows => $key)
		{
			if($key>2) $str .= $follows.'='.$key.'|'; // Format 3=4 Occurences with pipe and continue until the last follower has been added.
		}
		$str = (!empty($str) ? $ball.'>'.$str :  ''); // Format '10>'  Drawn Ball Number 10

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
}