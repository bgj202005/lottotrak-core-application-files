<?php
class History_m extends MY_Model
{
    /**
	 * This load_history loads a range of draws in ascending order and returned as an array. 
     * key / value pairs:
	 * history( 'id' => '859', 
     *          'ball1' => '25',
     *          'ball2' => '26',
     *          'ball3' => '31',
     *          'ball4' => '35',
     *          'ball5' => '45',
     *          'ball6' => '48',
     *          'extra' => '12',
     *          'draw_date  => '1983-01-01',
     *  
     *              )
	 * @param		string		$tbl	    Name of current Lottery Table
     * @param		integer     $lotto_id	Lottery id
     * @param		integer     $coverage	Range of draws, default to 100 if no parameter
     * @param		boolean     $e      	Extra Draw Coverage, 0 = no (False) do not add extra draws with a zero extra ball, 
     * 1 = yes (true) include the extra draws where the extra = 0 (or is all the balls drawn where the extra ball equals zero)
	 * @return      array		$history    Array of lottery draws for a given range	
	 */
    public function load_history($tbl, $lotto_id, $coverage = 100, $e = 0)
    {
        // todo: load the range of lottery draws, ascending order
        $this->db->reset_query();	// Clear any previous queries that are cached
        $ex_d = (!$e ?  ' WHERE extra <> 0' : '');
  
        $query = $this->db->query('SELECT d.*
                                    FROM (
                                    SELECT *
                                    FROM '.$tbl.$ex_d.    
                                    ' ORDER BY draw_date DESC LIMIT '.$coverage.' 
                                    ) as d ORDER BY d.draw_date ASC;'); // Utilized draw_date instead of id in case of deletion
        $history = $query->result_array();
    return (!is_null($history) ? $history : FALSE); // Returns a false if the query did not return results    
    }
    /* glance_exist with query for a single row result from the lottery_id
     * @param		integer     $lotto_id	Lottery id
	 * @return      boolean		TRUE/FALSE  At A Glance Statistics Exist (TRUE) / Do not exist (FALSE)	
	 */
    /**
     * Load a batch of draws starting at a given offset from the most recent draw.
     * Used by digit_sum_prediction to look back beyond the initial draw window.
     *
     * @param  string  $tbl       Draw table name
     * @param  int     $lotto_id  Lottery ID
     * @param  int     $coverage  Number of draws to fetch
     * @param  int     $offset    How many draws to skip (newest-first)
     * @param  int     $e         Extra draws flag (matches load_history convention)
     * @return array|false
     */
    public function load_history_offset($tbl, $lotto_id, $coverage = 100, $offset = 0, $e = 0)
    {
        $this->db->reset_query();
        $ex_d = (!$e ? ' WHERE extra <> 0' : '');
        $query = $this->db->query('SELECT d.*
                                    FROM (
                                    SELECT *
                                    FROM '.$tbl.$ex_d.
                                    ' ORDER BY draw_date DESC LIMIT '.$coverage.' OFFSET '.$offset.'
                                    ) as d ORDER BY d.draw_date ASC;');
        $history = $query->result_array();
        return (!is_null($history) && !empty($history)) ? $history : FALSE;
    }

    public function glance_exists($lotto_id)
    {
        // todo: load the range of lottery draws, ascending order
        $this->db->reset_query();	// Clear any previous queries in the cache
        $query = $this->db->select('*')
                          ->where('lottery_id', $lotto_id)
                          ->get('lottery_highlights');
		$row = $query->row();
    return (!is_null($row) ? $row : FALSE); // Returns a TRUE (if returning a result) or FALSE if the query did not a single row result    
    }
    /**
	 * This method looks at the previous draw with the next draw and returns an up change (1) or a down change (-1) or defaulted to no change (0)
	 * 
	 * @param		integer		$prev		current ball number	
	 * @param 		integer 	$next		next draw number
	 * @return      integer		0, 1, -1    0 = no change, 1 = up, -1 = down		
	 */
	public function trend($prev, $next)
	{
		$change = 0;       // default is no change
        if ($prev<=$next)  // Repeaters are included
		{
			$change = 1;   // There was an increase in change
		}
		if($prev>=$next)   // Repeaters included
		{
			$change = -1;  // There was an decrease in change
		}
	return $change;
	}
    /**
	 * trend_history looks at the previous draw with the next draw and returns an up change (1) or a down change (-1) or defaulted to no change (0)
	 * 
	 * @param       array       $draws      Array of draws for a given range
     * @param       integer     $pick       Pick Game. Pick 7, Pick 6, Pick 5 
     * @param 		boolean 	$b_ex       Bonus ball used		    
	 * @return      string		$trend      Concatenated String. Format: up=14,down=5,2021-05-10,15,down	
	 */
    public function trend_history($draws, $pick, $b_ex = FALSE)
    {
        $up = 0;
        $down = 0;
        $total = count($draws);
         foreach($draws as $count => $draw)
        {
            if(($total-1)!=$count)  // Most Recent Draw in db?
            {
                $change = 0;
                for($c=1; $c<=$pick; $c++) // Interate the draw for changes from the previous draw and the next draw
                {
                    $change = $change+intval($this->trend($draw['ball'.$c], $draws[$count+1]['ball'.$c]));
                }
                if($change==intval($pick)&&(!$b_ex)) // All Up
                {
                    $up++;  
                    $dd = $draws[$count+1]['draw_date']; // Record the most recent date for an up occurrence
                    $lt = 'up'; // last trend was up
                } 
                elseif(($change==-$pick)&&(!$b_ex))
                {
                    $down++;
                    $dd = $draws[$count+1]['draw_date']; // Record the most recent date for an up occurrence
                    $lt = 'down'; // last trend was down
                }
                if($b_ex&&$draw['extra']!=0)  // Now look at the extra or bonus ball
                {
                    $change = $change+intval($this->trend($draw['extra'], $draws[$count+1]['extra']));
                    if($change==intval($pick+1)) // All Up
                    {
                        $up++;  // Yes
                        $dd = $draws[$count+1]['draw_date']; // Record the most recent date for an up occurrence
                        $lt = 'up'; // last trend was up
                    } 
                    else if($change==(intval(-($pick+1))))
                    {
                        $down++;
                        $dd = $draws[$count+1]['draw_date']; // Record the most recent date for an up occurrence
                        $lt = 'down'; // last trend was down
                    }
                }
             }
        }
        $trend = 'up='.$up.',down='.$down;              // Format is 'up=xx,down=xx,drawdate,top,up/down'
        $trend .= ($up>=$down ? ','.$dd.','.$lt.','.$up.',up' : ','.$dd.','.$lt.','.$down.',down');
    return $trend;    
    }
    /**
	 * repeat_history tabulates the number of repeats over a given range and then looks at the most probable numbers that will be drawn for the next draw
	 * 
	 * @param       array       $draws      Array of draws for a given range
     * @param       integer     $pick       Pick Game. Pick 7, Pick 6, Pick 5 
     * @param 		boolean 	$b_ex       Bonus ball used		    
	 * @return      string		$r_values  Concatenated String. Format: 0=75,1=10,2=5,3=5,4=3,5=0,6=0|3=7,22=2. e.g. Pick 6 then looks at 6 number repeat maximum and pipe
     *                                     will separate the highest probable number(s) to be drawn for the next draw.	
	 */
    public function repeat_history($draws, $pick, $b_ex = FALSE)
    {
        $total = count($draws);
        $next = array();                                                    // empty set for the top picks
        $repeaters = $this->zeroed(new SplFixedArray($pick+1), $pick+1);    // include the zero repeaters
        
        foreach($draws as $count => $draw)
        {
            if(($total-1)!=$count)
            {
            $repeats = 0; 
                for($c=1; $c<=$pick; $c++)                      // Interate the draw for changes from the previous draw and the next draw
                {
                    $n = 1;                                     // Compare with the next drawn numbers
                    do
                    {
                        if($draw['ball'.$c]==$draws[$count+1]['ball'.$n])
                        {
                            $repeats++;
                            $next[$draw['ball'.$c]] = ((!array_key_exists($draw['ball'.$c], $next)) ? 1 : $next[$draw['ball'.$c]]+1); // Add Key or Existing One?
                        }
                    $n++;
                    }
                    while($n<=$pick); 
                }
                if($b_ex&&$draw['extra']!=0) // Extra included in the Repeaters
                {
                    for($c=1; $c<=$pick; $c++)                      // Interate the draw for changes from the previous draw and the next draw
                    {
                        if($draw['extra']==$draws[$count+1]['ball'.$c])
                        {
                            $repeats++;
                            $next[$draw['extra']] = (!array_key_exists($draw['extra'], $next) ? 1 : $next[$draw['extra']]+1); // Add Key or Existing One?
                        }
                        if($draw['ball'.$c]==$draws[$count+1]['extra'])
                        {
                            $repeats++;
                            $next[$draw['ball'.$c]] = ((!array_key_exists($draw['ball'.$c], $next)) ? 1 : $next[$draw['ball'.$c]]+1); // Add Key or Existing One?
                        }
                    }
                    if($draw['extra']==$draws[$count+1]['extra'])
                    {
                        $next[$draw['extra']] = (!array_key_exists($draw['extra'], $next) ? 1 : $next[$draw['extra']]+1); // Add Key or Existing One?   
                    }
                }
            }
            if($repeats!=0) $repeaters[$repeats] +=1;    // Count the number of repeaters for the next draw
            else $repeaters[0] += 1;                     // If no repeats, do count the draws that did not have a repeat
        }
        $r_text = '';
        unset($draws);
        foreach($repeaters as $c => $r)
        {
            $r_text .= $c.'='.$r.',';
        }
        $r_text = substr_replace($r_text, '|', -1);	    // Replace the ',' with the '|' (pipe)
        arsort($next);                                  // Sort by value descending
        if($this->top_pick($next, 3))
        {
            $i = 4; // Only 4 Top Numbers;
            foreach ($next as $k => $v)
            {
                $r_text .= $k.'='.$v.',';
                $i--;
                if($i<0) 
                {
                    break;
                }
            }
        }
        else $r_text .= '0=0,';   // Nothing Here  
    return substr($r_text, 0, -1);	// Return the repeats without an extra ',' Comma
    }

    /**
	 * Returns if the Top Pick has been picked a minimum number of times
	 * 
	 * @param       array       $picks           Array of draws for a given range
     * @param       integer     $limit           The minimum value that is used in the comparision to return true
	 * @return      boolean		true/false       Returns true if the minimum value (5) is equal or exceeded
	 */
    public function top_pick($picks, $limit=5)
    {
        foreach($picks as $pick => $value)
        {
            if($value>=$limit) return true;
        }
    return false;
    }
    /**
	 * Passes a fixed NULL array and sets all array elements  to 0 and returns the array
	 * 
	 * @param       array       $picks           Fixed NULL Array
     *  @param      integer     $s               Size of fixed array
	 * @return      array		$fixed           Returns the zeroed array
	 */
    public function zeroed($fixed, $s)
    {
        for($l=0; $l<$s; $l++)
        {
            $fixed[$l] = 0;                         // Initialize to 0 int
        }
    return $fixed; 
    }

    /**
	 * consecutive_history tabulates the number of consecutives for a range of draws including no consecutives, 1 consecutive, 2 consecutives, etc.
	 * 
	 * @param       array       $draws          Array of draws for a given range
     * @param       integer     $pick           Pick Game. Pick 7, Pick 6, Pick 5 
	 * @param 		boolean 	$ex 		    extra draws used
     * @param 		boolean 	$b_ex           Bonus ball used		    
	 * @return      string		$c_text         Concatenated String. Format: 0=40,1=25,2=13,3=8,4=1,5=0,6=0|1=2020-11-03. 
     *                                          e.g. Pick 6 No consecutives = 40 drawas, 1 consecutive is 25 draws, etc.
     *                                          1=1 Consecutive and the last date it occurred
	 */
    public function consecutive_history($draws, $pick, $ex = FALSE, $b_ex = FALSE)
    {
        $total = count($draws);
        $consecutives = $this->zeroed(new SplFixedArray($pick+1), $pick+1);         // include the zero consecutives
        
        foreach($draws as $count => $draw)
        {
            if(($total)!=$count)
            {
            $consecutives_draw = 0;    
                for($c=1; $c<$pick; $c++)                           // Interate the draw for changes from the previous draw and the next draw
                {
                    if(intval($draw['ball'.($c+1)])-intval($draw['ball'.$c])==1)    // The next drawn number is consecutive
                    {
                        $consecutives_draw++;
                        $lcd = $consecutives_draw.'='.$draw['draw_date'];           // Include the last draw date of the occurrence
                    }
                }
                if($b_ex&&$draw['extra']!=0)            // Extra included in the Consecutives
                {
                    for($c=1; $c<=$pick; $c++)          // Must interate in this case with the extra drawn number
                    {
                        if(abs(intval($draw['extra'])-intval($draw['ball'.$c]))==1) 
                        {
                            $consecutives_draw++;       // Include the last draw date of the occurrence
                            $lcd = $consecutives_draw.'='.$draw['draw_date'];
                        }
                    }
                }
            }
            if($consecutives_draw!=0) $consecutives[$consecutives_draw] +=1;    // Count the number of consecutives for this draw
            else $consecutives[0] += 1;                                       // If no consecutives, do count the draws that did not have a consecutive
        }
        $c_text = '';
        unset($draws);
        foreach($consecutives as $n => $c)
        {
            $c_text .= $n.'='.$c.',';
        }
        $c_text = substr_replace($c_text, '|', -1);	    // Replace the ',' with the '|' (pipe)
        $c_text .= $lcd;                             // include the last draw date of consecutive occurrence
    return $c_text;                         
    }
    /**
     * adjacent_history averages the number of adjacents of each ball position for balls 1 and 2 = 1, 2 and 3 = 2, 3 and 4 = 3, 4 and 5 = 4, 5 and 6 = 5 for a pick 6 game. 
     * 6 and 7 = 6 for Pick 7. 7 and 8 = 7 for Pick 8, etc. The maximum separation is included between any balls is included. 2 = 14. e.g for balls 2 and 3, the maximum
     * difference is 14 for balls 2 and 3 over all other balls selected in the given range.
     * 
     * @param       array       $draws          Array of draws for a given range
     * @param       integer     $pick           Pick Game. Pick 7, Pick 6, Pick 5 
     * @return      string      $adj_text       Concatenated String. Format: . e.g. Pick 6 average difference, 1=5,2=5,3=2,4=7,5=5|2=14
     */
    public function adjacents_history($draws, $pick)
    {
        $total = count($draws);
        $lg_diff = 0;           // Largest difference across all positions and draws
        $adj = 1;               // Position where largest difference occurred
        $adjacents = $this->zeroed(new SplFixedArray($pick), $pick);    // Zero-based array for positions 0 to pick-1
        $processed_draws = 0;   // Count of actually processed draws
        
        foreach($draws as $count => $draw)
        {
            $processed_draws++;
            for($c = 1; $c < $pick; $c++)  // Iterate through adjacent ball positions
            {
                $diff = intval($draw['ball'.($c+1)]) - intval($draw['ball'.$c]);
                $adjacents[$c-1] += $diff;  // Use zero-based indexing for array
                
                // Track the largest difference and its position across all draws
                if($lg_diff < $diff) 
                {
                    $lg_diff = $diff;     // Current difference is now the largest     
                    $adj = $c;            // Position between balls where this occurred
                }
            }
        }
        
        $adj_text = '';
        unset($draws);
        
        // Calculate averages and build output string
        for($c = 1; $c < $pick; $c++)
        {
            $average = round(($adjacents[$c-1] / $processed_draws)); // Average difference for this position
            $adj_text .= $c.'='.$average.',';
        }
        
        $adj_text = substr_replace($adj_text, '|', -1);    // Replace last ',' with '|'
        $adj_text .= $adj.'='.$lg_diff;                    // Include position and value of largest difference
        
        return $adj_text;                        
    }
    /**
	 * sums_history summarizes the winning sums over a given range of draws. Only the top 10 Winning sums if they have occurred more than once, will be retained. 
	 * 
	 * @param       array       $draws          Array of draws for a given range
	 * @return      string		$sum_text       Concatenated String. Format: 147=5,209=5,187=4,162=3,109=2|5=17,INCREASE
     * Percentage differences will be calculated for 0-5%, 6-10%, 11-15%, 16-20%, 21-25%, 26-30%, 31-35%, 36-40%, 41-45% and 46-50% 
     * for example, 5=17 is intrepreted as 0 - 5 % of all draws (over 100 draws, as an example) has occurred 17 times in 100.
     * The sum for the next draw has been found to increase from 0 - 5% in 17 draws of 100. 
     * the number of occurences and if the percentage is an INCREASE or DECREASE from the previous total sum. Only the highest occurrence will be retained. 
	 */
    public function sums_history($draws)
    {
        $total = count($draws);
        $sums = array(); // empty set for the top sums
        $percents = array(-50,-45,-40,-35,-30,-25,-20,-15,-10,-5,5,10,15,20,25,30,35,40,45,50); // ranges of percentages for both positive and negative
                        //-50-46%,-45-41%,-40-36%,-35-31%,-30-26%,-25-21%,-20-16%,-15-11%,-10-6%,-5-0%,0-5%,6-10%,11-15%,16-20%,21-25%,26-30%,31-35%,36-40%,41-45%,46-50%
        
        $ranges = $this->zeroed(new SplFixedArray(20), 20);   // 20 Percentage Ranges
        
        foreach($draws as $count => $draw)
        {
            if(($total)!=$count)
            {
                $sums[$draw['sum_draw']] = (!array_key_exists($draw['sum_draw'], $sums) ? 1 : $sums[$draw['sum_draw']]+1); // Add Key or Existing One?
                if(!empty($count)) // if not 0
                {
                    //$diff = $draws[$count]['sum_draw']-$draws[$count-1]['sum_draw'];                 // Formula for percentage difference
                    $percent_diff = (1-$draws[$count-1]['sum_draw']/$draws[$count]['sum_draw'])*100;   // Perecentage Difference = |ΔV|[ΣV2]×100
                    $percent_diff = round($percent_diff);
                    foreach($percents as $r => $v)
                    {
                        if(($percent_diff<0)&&($percent_diff<=($v+4))&&($percent_diff>=$v)) // 0 < Negatives
                        {
                            $ranges[$r] += 1;
                            break;
                        }
                        elseif(($percent_diff>0)&&($percent_diff>=($v-4))&&($percent_diff<=$v)) // 0 > Positives
                        {
                            $ranges[$r] += 1;
                            break;
                        }
                    }
                }   
            }
        }
        $s_text = "";
        arsort($sums);    // Sort by value NOT Key DESCENTDING
        unset($draws);
        if($this->top_pick($sums,2))
        {
            $i = 10; // Only 10 Top Numbers;
            foreach ($sums as $k => $v)
            {
                $s_text .= $k.'='.$v.',';
                $i--;
                if(!$i) 
                {
                    break;
                }
            }
        }
        else $s_text .= '0=0,';   // Nothing Here, rare event
        $s_text = substr_replace($s_text, '|', -1);	    // Replace the ',' with the '|' (pipe)
        $top = $ranges[0];                              // Start at the beginning
        $offset = 0;
        foreach($ranges as $r => $v)                    // Find the greatest Percentage difference 
        {
            if($top<$v) 
            {
                $top = $v;
                $offset = $r;
            }
        }
        if($percents[$offset]<0) $s_text .= abs($percents[$offset]).'='.$top.',DECREASE';
        else  $s_text .= abs($percents[$offset]).'='.$top.',INCREASE';
    return $s_text;
    }
    /**
	 * digits_history tabulates the digit sums over a given range of draws. Only the top 5 digit sums will be retained.
	 * 
	 * @param       array       $draws          Array of draws for a given range	 
     * @return      string		$d_text         Concatenated String. Format: 42=7,33=5,41=4,33=4,54=4|10=19,INCREASE
     * Percentage differences will be calculated for 0-5%, 6-10%, 11-15%, 16-20%, 21-25%, 26-30%, 31-35%, 36-40%, 41-45% and 46-50%
     * for example, 10=19 is interpreted as 6 - 10 % of all draws (over 100 draws, as an example) has occurred 19 times in 100.
     * The sum for the next draw has been found to increase from 6 - 10% in 17 draws of 100.  
     * the number of occurences and if the percentage is an INCREASE or DECREASE from the previous digits sum. Only the highest occurrence will be retained. 
	 */
    public function digits_history($draws)
    {
        $total = count($draws);
        $digits = array();        // empty set for the top sums
        $percents = array(-50,-45,-40,-35,-30,-25,-20,-15,-10,-5,5,10,15,20,25,30,35,40,45,50); // ranges of percentages for both positive and negagtive
                        //-50-46%,-45-41%,-40-36%,-35-31%,-30-26%,-25-21%,-20-16%,-15-11%,-10-6%,-5-0%,0-5%,6-10%,11-15%,16-20%,21-25%,26-30%,31-35%,36-40%,41-45%,46-50%
        
        $ranges = $this->zeroed(new SplFixedArray(20), 20);   // 20 Percentage Ranges
        
        foreach($draws as $count => $draw)
        {
            // Count all digits, regardless of position
            $digits[$draw['sum_digits']] = (!array_key_exists($draw['sum_digits'], $digits) ? 1 : $digits[$draw['sum_digits']]+1); // Add Key or Existing One?
            
            // Only calculate percentage differences if not the last draw (for trend analysis)
            if(($count + 1) < $total && !empty($count)) // if not the last draw and not the first
            {
                //$diff = $draws[$count]['sum_draw']-$draws[$count-1]['sum_draw'];                                // Formula for percentage difference
                $percent_diff = (1-$draws[$count-1]['sum_digits']/$draws[$count]['sum_digits'])*100;   // Perecentage Difference = |ΔV|[ΣV2]×100
                $percent_diff = round($percent_diff); // No decimals
                foreach($percents as $r => $v)
                {
                    if(($percent_diff<0)&&($percent_diff<=($v+4))&&($percent_diff>=$v)) // 0 < Negatives
                    {
                        $ranges[$r] += 1;
                        break;
                    }
                    elseif(($percent_diff>0)&&($percent_diff>=($v-4))&&($percent_diff<=$v)) // 0 > Positives
                    {
                        $ranges[$r] += 1;
                        break;
                    }
                }
            }   
        }
        $d_text = "";
        arsort($digits);    // Sort by value NOT Key only value DESCENDING
        unset($draws);
        if($this->top_pick($digits,2))
        {
            $i = 10; // Only 10 Top Numbers;
            foreach ($digits as $k => $v)
            {
                $d_text .= $k.'='.$v.',';
                $i--;
                if(!$i) 
                {
                    break;
                }
            }
        }
        else $d_text .= '0=0,';   // Nothing Here, rare event
        $d_text = substr_replace($d_text, '|', -1);	    // Replace the ',' with the '|' (pipe)
        $top = $ranges[0];                              // Start at the beginning
        $offset = 0;
        foreach($ranges as $r => $v)                    // Find the greatest Percentage difference 
        {
            if($top<$v) 
            {
                $top = $v;
                $offset = $r;
            }
        }
        if($percents[$offset]<0) $d_text .= abs($percents[$offset]).'='.$top.',DECREASE';
        else  $d_text .= abs($percents[$offset]).'='.$top.',INCREASE';
    return $d_text;
    }
    /**
	 * range_history subtracts the difference between the highest drawn mnumber and the lowest drawn number 
	 * The top 5 ranges will be summarized over the given range of draws
	 * @param       array       $draws          Array of draws for a given range
     * @param       integer     $pick           Pick Game. Pick 7, Pick 6, Pick 5 
 	 * @return      string		$r_text         Concatenated String. Format: 42=10,23=8,11=8,23=7
     * the number of occurences must exceed the average for that odd / even combination based from the range to be included
     * Low occurrences over a given range will also be highlighted
	 */
    public function range_history($draws, $pick)
    {
        $total = count($draws);
        $ranges = array();              // empty set for the top picks
        
        foreach($draws as $count => $draw)
        {
            if(($total)!=$count)
            {
                $diff = intval($draw['ball'.$pick])-intval($draw['ball1']);   // Subtract the top drawn number from the first number drraw
                $ranges[$diff] = (array_key_exists($diff,$ranges) ? $ranges[$diff]+1 : 1); // Add Key or Existing One?
            }
        }
        arsort($ranges);                // Sort by value NOT Key DESCENDING
        unset($draws);
        $r_text = "";
        if($this->top_pick($ranges,5))  // Must have a minimum count of 5
        {
            $i = 4; // Only 4 Top Numbers;
            foreach ($ranges as $k => $v)
            {
                if($v>=2) $r_text .= $k.'='.$v.','; // Must have a minium of 2 occurences in the given range
                $i--;
                if($i<0) 
                {
                    break;
                }
            }
        }
        else $r_text .= '0=0,';     // Nothing Here, rare event
    return substr($r_text, 0, -1);	// Return the repeats without an extra ',' Comma                         
    }
    /**
	 * parity_history calculates the odd/even combination that has exceeded the average odd/even for that given range 
	 * 
	 * @param       array       $draws          Array of draws for a given range
     * @param       string      $lotto          Lottery Table name
     * @param       boolean     $ex             Extra Draws, 0 = none, 1 = included
     * @param       integer     $pick           Pick Game. Pick 7, Pick 6, Pick 5
 	 * @return      string		$oe_text        Concatenated String. Format: 4-3=55,3-4=34,5-2=20,2-5=15,1-6=12,6-1=8,7-0=6,0-7=4|7-0=2020-11-0,0-7=2021-10-13
     * the number of occurences must exceed the average for that odd / even combination based from the range to be included
     * Low occurrences over a given range will also be included. For example, in a pick 7, if the odd/even was 7-0 and 5 occurrences in the last 100 draws. This will 
     * be included with the last draw date of the occurence.
	 */
    public function parity_history($draws, $pick, $ex = FALSE, $lotto)
    {
        // Step 1: Return all the odd-even combinations from the moss occurrences to the least
        $total  = count($draws);
        $parity = $this->parity_list($total, $ex, $lotto);      // Return the Odds and Evens over the given range
        if(!$parity) return FALSE;                              // could not return the query and return FALSE
        $top = array();
        $low = 0;
        $low_evens = 0;
        $low_odds = 0;
        $low_date = '';
        foreach($parity as $count => $oddevens)
        {
            $top[$oddevens['odd'].'-'.$oddevens['even']] = $oddevens['count(*)'];   // Arrange the format as odd-even=count
            if(($oddevens['odd']==$pick)&&($oddevens['even']==0))
            {
                if($low<$oddevens['count(*)']) 
                {
                    $low = $oddevens['count(*)'];
                    $low_odds = $oddevens['odd'];
                    $low_evens = $oddevens['even'];
                    $low_date = $oddevens['draw_date'];
                }
            }
            elseif(($oddevens['even']==$pick)&&($oddevens['odd']==0))
            {
                if($low<$oddevens['count(*)']) 
                {
                    $low = $oddevens['count(*)'];
                    $low_evens = $oddevens['even'];
                    $low_odds = $oddevens['odd'];
                    $low_date = $oddevens['draw_date'];
                } 
            }
        }
        arsort($top);   // Sort the odd - even combination in reverse order by the count value only
        $oe_text = '';
        foreach($top as $c => $oe)
        {
            $oe_text .= $c.'='.$oe.',';
        }
        $oe_text = substr_replace($oe_text, '|', -1);	    // Replace the ',' with the '|' (pipe)
        unset($top);                                        // Destroy the $top array
        // Step 2, is to find the low number odd - even combinations starting at the pick (e.g. for Pick 6 it would be 6-0 and 0 - 6)
        // next, look for the largest count of a given odd - even (6-0 or 0-6),  
        // In a 649, a 6 odd - 0 even would eliminate 24 balls, 0-6 would eliminate 25 balls
        // For example, pick 6 would be 6 odd - 0 even, 0 odd - 6 even
        // Pick 7 is 7 odd - 0 even, 0 odd and 7 even
        // Retrieve all the dates and the draw separation between draws.  Add these results to the string and return
        // For example, 6-0,2021/01/18,5,2021/02/28,10,2021/05/15,5,2021/07/01,25
        // The complete format will be displayed as: 4-2=34,3-3=32,2-4=25,4-2=20,5-1=15,1-5=14,6-0=4,00-6=2|6-0,2021/01/18,5,2021/02/28,10,2021/05/15,5,2021/07/01.25
        if(!empty($low_date)&&($low>1))     // Is there additional dates?
        {
            $oe_text .= $low_odds.'-'.$low_evens.',';
            $oe_text .= $this->parity_dates($draws, $low_odds, $low_evens, $low);    
        }
        elseif(!empty($low_date)&&($low==1)) $oe_text .= $low_odds.'-'.$low_evens.','.$low_date;
        else 
        {
            $oe_text .= "0-0";  // The rare odd / even combination did not happen
            $result = "";
            $oe_text .= $result;    
        }
        unset($draws);    
    return $oe_text;   
    }
    /**
     * digit_sum_prediction computes the most likely next Digit Sum and the best associated
     * Winning Number Sum using two complementary signals:
     *
     *   Signal 1 — Frequency (from stored winning_digits string):
     *     How often each Digit Sum has appeared in the last N draws (top-10 stored).
     *
     *   Signal 2 — Overdue (from raw $draws array, when provided):
     *     overdue_ratio = draws_since_last_seen / avg_gap_between_appearances.
     *     A ratio > 1.0 means the value is overdue; higher = more overdue.
     *     Capped at 3× avg to prevent extreme outliers dominating.
     *
     *   Combined score = (0.5 × normalised_frequency) + (0.5 × capped_overdue_ratio / 3)
     *   The Digit Sum with the highest combined score is selected as the prediction.
     *
     *   Best Number Sum: the most frequently co-occurring sum_draw in the raw draws
     *   where sum_digits equals the predicted Digit Sum.
     *   Falls back to the frequency + trend approach when raw draws are unavailable.
     *
     * @param   string  $winning_digits  Stored string e.g. "42=7,33=5,41=4|10=19,INCREASE"
     * @param   string  $winning_sums    Stored string e.g. "163=4,147=3,178=3|5=17,INCREASE"
     * @param   array   $draws           Optional raw draws array (each row has sum_digits, sum_draw)
     * @return  array   ['predicted_digit_sum' => int, 'predicted_winning_sum' => int, 'predicted_runners_up' => string]
     *                  predicted_runners_up stores all 3 candidates with scores.
     *                  Format per entry: ds=ws=combined=freq=overdue  e.g. "41=140=0.72=0.85=1.75"
     *                  Three entries separated by commas e.g. "41=140=0.72=0.85=1.75,38=128=0.68=0.78=1.22,35=98=0.61=0.65=0.88"
     *                    ds       = Digit Sum
     *                    ws       = best associated Number Sum
     *                    combined = final score 0-1 (higher = stronger overall prediction)
     *                    freq     = normalised frequency 0-1 (1.0 = appeared as often as the most frequent)
     *                    overdue  = overdue ratio 0-3 (1.0 = right on schedule, 2.0 = twice overdue)
     */
    public function digit_sum_prediction($winning_digits, $winning_sums, $draws = array(), $tbl_name = '', $lottery_id = 0, $extra_draws = 0)
    {
        $predicted_digit_sum   = 0;
        $predicted_winning_sum = 0;
        $predicted_runners_up  = '';
        $digit_trend_dir       = 'INCREASE';
        $sum_trend_dir         = 'INCREASE';

        // --- Parse Digit Sum frequency data from stored string ---
        $digit_freq = array();
        if (!empty($winning_digits))
        {
            $parts           = explode('|', $winning_digits);
            $entries         = explode(',', $parts[0]);
            $digit_trend_dir = (isset($parts[1]) && strpos($parts[1], 'INCREASE') !== FALSE) ? 'INCREASE' : 'DECREASE';
            foreach ($entries as $entry)
            {
                $kv = explode('=', $entry);
                if (count($kv) === 2 && intval($kv[0]) > 0)
                {
                    $digit_freq[intval($kv[0])] = intval($kv[1]);
                }
            }
        }

        if (empty($digit_freq))
        {
            return array('predicted_digit_sum' => 0, 'predicted_winning_sum' => 0, 'predicted_runners_up' => '');
        }

        // --- Signal analysis using raw draws ---
        if (!empty($draws))
        {
            $total_draws = count($draws);

            // -------------------------------------------------------
            // Signal 1: Recency-weighted frequency
            // Draws in the last 20 get weight 3, last 21-50 get 2, older get 1.
            // This captures momentum/streaks rather than flat historical count.
            // -------------------------------------------------------
            $weighted_freq = array();
            foreach (array_keys($digit_freq) as $ds) { $weighted_freq[$ds] = 0.0; }

            foreach ($draws as $age_asc => $draw)
            {
                // $age_asc is 0 = oldest, $total_draws-1 = most recent
                $ds      = intval($draw['sum_digits']);
                $recency = $total_draws - 1 - $age_asc; // 0 = most recent
                if ($ds <= 0) continue;
                if      ($recency < 20) $w = 3;
                elseif  ($recency < 50) $w = 2;
                else                    $w = 1;
                $weighted_freq[$ds] = isset($weighted_freq[$ds]) ? $weighted_freq[$ds] + $w : $w;
            }

            // -------------------------------------------------------
            // Signal 2: Markov transition — P(next_ds | last_drawn_ds)
            // Build transition counts from consecutive draw pairs.
            // -------------------------------------------------------
            $from_ds     = 0;   // digit sum of the most recent draw
            $transitions = array(); // [from_ds][to_ds] => count
            $prev_ds     = 0;
            foreach ($draws as $draw)
            {
                $ds = intval($draw['sum_digits']);
                if ($ds <= 0) continue;
                if ($prev_ds > 0)
                {
                    if (!isset($transitions[$prev_ds][$ds])) $transitions[$prev_ds][$ds] = 0;
                    $transitions[$prev_ds][$ds]++;
                }
                $prev_ds = $ds;
            }
            $from_ds = $prev_ds; // last draw's digit sum is the "from" state

            // Compute Markov probability for each candidate ds from current state
            $markov_scores = array();
            if ($from_ds > 0 && isset($transitions[$from_ds]))
            {
                $row_total = array_sum($transitions[$from_ds]);
                foreach ($digit_freq as $ds => $freq)
                {
                    $cnt = isset($transitions[$from_ds][$ds]) ? $transitions[$from_ds][$ds] : 0;
                    $markov_scores[$ds] = ($row_total > 0) ? ($cnt / $row_total) : 0.0;
                }
            }
            else
            {
                // No transition data for current state — fall back to uniform (no signal)
                foreach ($digit_freq as $ds => $freq) { $markov_scores[$ds] = 0.0; }
            }

            // -------------------------------------------------------
            // Signal 3: Overdue (kept as a small tiebreaker only — 15%)
            // -------------------------------------------------------
            $last_seen      = array();
            $all_ds_ordered = array();
            $reversed = array_reverse($draws);
            foreach ($reversed as $gap => $draw)
            {
                $ds = intval($draw['sum_digits']);
                if ($ds <= 0) continue;
                $all_ds_ordered[] = $ds;
                if (!isset($last_seen[$ds])) $last_seen[$ds] = $gap;
            }
            unset($reversed);

            $raw_counts = array_count_values($all_ds_ordered);
            $overdue    = array();
            foreach ($raw_counts as $ds => $cnt)
            {
                $avg_gap      = ($cnt > 0) ? ($total_draws / $cnt) : $total_draws;
                $current_gap  = isset($last_seen[$ds]) ? $last_seen[$ds] : $total_draws;
                $overdue[$ds] = ($avg_gap > 0) ? ($current_gap / $avg_gap) : 0.0;
            }
            unset($all_ds_ordered);

            // -------------------------------------------------------
            // Combined score: 40% recency-freq + 45% Markov + 15% overdue
            // -------------------------------------------------------
            $max_wfreq  = (!empty($weighted_freq)) ? max($weighted_freq) : 1;
            $max_markov = (!empty($markov_scores)) ? max($markov_scores) : 1;
            if ($max_markov == 0) $max_markov = 1; // avoid divide-by-zero when all Markov = 0

            $scored       = array();
            $freq_norms   = array();
            $overdue_caps = array();
            foreach ($digit_freq as $ds => $freq)
            {
                $norm_wfreq        = isset($weighted_freq[$ds]) ? ($weighted_freq[$ds] / $max_wfreq) : 0.0;
                $norm_markov       = isset($markov_scores[$ds]) ? ($markov_scores[$ds] / $max_markov) : 0.0;
                $overdue_capped    = isset($overdue[$ds]) ? min($overdue[$ds], 3.0) : 0.0;
                $freq_norms[$ds]   = $norm_wfreq;
                $overdue_caps[$ds] = $overdue_capped;
                $scored[$ds]       = (0.40 * $norm_wfreq)
                                   + (0.45 * $norm_markov)
                                   + (0.15 * ($overdue_capped / 3.0));
            }
            arsort($scored);

            // Extract top 3 digit sums from the scored ranking
            $top3_ds = array_slice(array_keys($scored), 0, 3, TRUE);

            // For each top-3 digit sum, find its best associated number sum from raw draws.
            // If all co-occurring sums tie (each appeared only once), look back an additional
            // 100 draws at a time (up to 5 extra batches) until a winner clearly emerges.
            $best_sums = array(); // ds => best_sum
            foreach ($top3_ds as $ds)
            {
                $sum_tally = array();
                foreach ($draws as $draw)
                {
                    if (intval($draw['sum_digits']) === $ds && intval($draw['sum_draw']) > 0)
                    {
                        $sd = intval($draw['sum_draw']);
                        $sum_tally[$sd] = isset($sum_tally[$sd]) ? $sum_tally[$sd] + 1 : 1;
                    }
                }

                // Iterative lookback: if still tied, fetch older batches one at a time
                if (!empty($sum_tally) && !empty($tbl_name) && $lottery_id > 0)
                {
                    $offset    = count($draws); // skip the draws we already have
                    $max_batch = 5;             // look back up to 5 × 100 = 500 more draws
                    for ($batch = 0; $batch < $max_batch; $batch++)
                    {
                        // Check whether there is a clear winner (one sum leads over all others)
                        $max_count = max($sum_tally);
                        $tie_count = 0;
                        foreach ($sum_tally as $c) { if ($c === $max_count) $tie_count++; }
                        if ($tie_count === 1) break; // clear winner found — stop looking back

                        $more_draws = $this->load_history_offset($tbl_name, $lottery_id, 100, $offset, $extra_draws);
                        if (empty($more_draws)) break; // no more history available

                        foreach ($more_draws as $draw)
                        {
                            if (intval($draw['sum_digits']) === $ds && intval($draw['sum_draw']) > 0)
                            {
                                $sd = intval($draw['sum_draw']);
                                $sum_tally[$sd] = isset($sum_tally[$sd]) ? $sum_tally[$sd] + 1 : 1;
                            }
                        }
                        $offset += 100;
                    }
                }

                if (!empty($sum_tally))
                {
                    arsort($sum_tally);
                    $best_sums[$ds] = intval(key($sum_tally));
                }
                else
                {
                    $best_sums[$ds] = 0;
                }
            }

            // Assign 1st place
            $predicted_digit_sum   = isset($top3_ds[0]) ? intval($top3_ds[0]) : 0;
            $predicted_winning_sum = isset($best_sums[$predicted_digit_sum]) ? $best_sums[$predicted_digit_sum] : 0;

            // Build full scored string for all 3 candidates stored in predicted_runners_up
            // Format: ds=ws=combined=freq=overdue  e.g. "41=140=0.72=0.85=1.75"
            $all_runners = array();
            foreach ($top3_ds as $ds)
            {
                if ($ds > 0)
                {
                    $ws      = isset($best_sums[$ds]) ? $best_sums[$ds] : 0;
                    $comb    = number_format(isset($scored[$ds]) ? $scored[$ds] : 0, 2);
                    $freq_s  = number_format(isset($freq_norms[$ds]) ? $freq_norms[$ds] : 0, 2);
                    $over_s  = number_format(isset($overdue_caps[$ds]) ? $overdue_caps[$ds] : 0, 2);
                    $all_runners[] = $ds . '=' . $ws . '=' . $comb . '=' . $freq_s . '=' . $over_s;
                }
            }
            $predicted_runners_up = implode(',', $all_runners);
        }
        else
        {
            // Fallback (cache read path): frequency + trend tiebreaker only
            arsort($digit_freq);
            $top3_keys           = array_slice(array_keys($digit_freq), 0, 3);
            $predicted_digit_sum = ($digit_trend_dir === 'INCREASE') ? intval(max((array)$top3_keys[0])) : intval(min((array)$top3_keys[0]));
            // No raw draws available — scores cannot be calculated
        }

        // --- Fallback for predicted_winning_sum if not resolved from raw draws ---
        if ($predicted_winning_sum === 0 && !empty($winning_sums))
        {
            $parts         = explode('|', $winning_sums);
            $entries       = explode(',', $parts[0]);
            $sum_trend_dir = (isset($parts[1]) && strpos($parts[1], 'INCREASE') !== FALSE) ? 'INCREASE' : 'DECREASE';
            $sum_freq      = array();
            foreach ($entries as $entry)
            {
                $kv = explode('=', $entry);
                if (count($kv) === 2 && intval($kv[0]) > 0)
                {
                    $sum_freq[intval($kv[0])] = intval($kv[1]);
                }
            }
            if (!empty($sum_freq))
            {
                $max_freq  = max($sum_freq);
                $top_keys  = array_keys($sum_freq, $max_freq);
                $predicted_winning_sum = ($sum_trend_dir === 'INCREASE') ? intval(max($top_keys)) : intval(min($top_keys));
            }
        }

        return array(
            'predicted_digit_sum'   => $predicted_digit_sum,
            'predicted_winning_sum' => $predicted_winning_sum,
            'predicted_runners_up'  => $predicted_runners_up
        );
    }
    /**
     * glance_prediction_save. Updates only the three prediction fields in lottery_highlights.
     * Used when scores are being backfilled without a full recalculation of all columns.
     *
     * @param   integer $lottery_id     Lottery id
     * @param   array   $prediction     Array with keys: predicted_digit_sum, predicted_winning_sum, predicted_runners_up
     * @return  boolean                 TRUE on success, FALSE on failure
     */

    /**
     * short_repeat_indicator — for each unique DS and WS value that appeared in the
     * last $window draws, checks whether it historically tends to repeat within
     * $window draws of any occurrence. Only values with >= $min_repeats confirmed
     * short-repeats are returned.
     *
     * @param   array   $draws          Raw draw rows (from load_history), oldest first
     * @param   int     $window         Look-ahead / look-back window in draws (default 10)
     * @param   int     $min_repeats    Minimum confirmed repeats to qualify (default 3)
     * @param   array   $extra_ds       Additional DS values to evaluate (e.g. top-3 predicted)
     *                                  even if they did not appear in the last $window draws.
     *                                  Only the single best qualifier is returned, tagged 'from_prediction'.
     * @return  array   Keys 'ds' and 'ws', each an array of qualifying candidates sorted by rate desc
     */
    public function short_repeat_indicator($draws, $window = 10, $min_repeats = 3, $extra_ds = array())
    {
        if (empty($draws)) return array();

        $draws = array_values($draws); // ensure 0-indexed
        $n     = count($draws);

        // Collect unique DS and WS values seen in the last $window draws
        $recent_start = max(0, $n - $window);
        $recent_ds    = array();
        $recent_ws    = array();
        for ($i = $recent_start; $i < $n; $i++)
        {
            $ds = intval($draws[$i]['sum_digits']);
            $ws = intval($draws[$i]['sum_draw']);
            if ($ds > 0) $recent_ds[$ds] = true;
            if ($ws > 0) $recent_ws[$ws] = true;
        }

        $fields = array(
            'ds' => array('field' => 'sum_digits', 'candidates' => array_keys($recent_ds)),
            'ws' => array('field' => 'sum_draw',   'candidates' => array_keys($recent_ws)),
        );

        $result = array('ds' => array(), 'ws' => array());

        foreach ($fields as $key => $cfg)
        {
            $field = $cfg['field'];
            foreach ($cfg['candidates'] as $val)
            {
                if ($val <= 0) continue;

                $occurrences   = 0;
                $short_repeats = 0;

                for ($i = 0; $i < $n; $i++)
                {
                    if (intval($draws[$i][$field]) !== $val) continue;
                    $occurrences++;
                    $end = min($i + $window, $n - 1);
                    for ($j = $i + 1; $j <= $end; $j++)
                    {
                        if (intval($draws[$j][$field]) === $val)
                        {
                            $short_repeats++;
                            break; // one repeat counted per trigger occurrence
                        }
                    }
                }

                if ($short_repeats < $min_repeats) continue; // not enough evidence

                // How many draws since this value last appeared?
                $draws_since = 0;
                for ($i = $n - 1; $i >= 0; $i--)
                {
                    if (intval($draws[$i][$field]) === $val) break;
                    $draws_since++;
                }

                $rate = ($occurrences > 0) ? round($short_repeats / $occurrences, 2) : 0.0;

                $result[$key][] = array(
                    'value'           => $val,
                    'occurrences'     => $occurrences,
                    'repeats'         => $short_repeats,
                    'rate'            => $rate,
                    'draws_since'     => $draws_since,
                    'from_prediction' => false,
                );
            }

            // Sort by repeat rate descending
            if (!empty($result[$key]))
            {
                usort($result[$key], function($a, $b) {
                    return $b['rate'] <=> $a['rate'];
                });
            }
        }

        // --- Extra DS from prediction: check top-3 predicted values not already in recent window ---
        if (!empty($extra_ds))
        {
            // Keys already shown from recent-window scan
            $already_shown = array();
            foreach ($result['ds'] as $item) { $already_shown[$item['value']] = true; }

            $best_prediction = null;
            foreach ($extra_ds as $val)
            {
                $val = intval($val);
                if ($val <= 0 || isset($already_shown[$val])) continue;

                $occurrences   = 0;
                $short_repeats = 0;

                for ($i = 0; $i < $n; $i++)
                {
                    if (intval($draws[$i]['sum_digits']) !== $val) continue;
                    $occurrences++;
                    $end = min($i + $window, $n - 1);
                    for ($j = $i + 1; $j <= $end; $j++)
                    {
                        if (intval($draws[$j]['sum_digits']) === $val)
                        {
                            $short_repeats++;
                            break;
                        }
                    }
                }

                if ($short_repeats < $min_repeats) continue;

                $draws_since = 0;
                for ($i = $n - 1; $i >= 0; $i--)
                {
                    if (intval($draws[$i]['sum_digits']) === $val) break;
                    $draws_since++;
                }

                $rate = ($occurrences > 0) ? round($short_repeats / $occurrences, 2) : 0.0;
                $candidate = array(
                    'value'           => $val,
                    'occurrences'     => $occurrences,
                    'repeats'         => $short_repeats,
                    'rate'            => $rate,
                    'draws_since'     => $draws_since,
                    'from_prediction' => true,
                );
                // Keep only the best-rate prediction candidate
                if ($best_prediction === null || $rate > $best_prediction['rate'])
                {
                    $best_prediction = $candidate;
                }
            }

            if ($best_prediction !== null)
            {
                $result['ds'][] = $best_prediction;
                // Re-sort so prediction entry appears in natural rate order
                usort($result['ds'], function($a, $b) {
                    return $b['rate'] <=> $a['rate'];
                });
            }
        }

        return $result;
    }

    public function glance_prediction_save($lottery_id, $prediction)
    {
        $this->db->set('predicted_digit_sum',   $prediction['predicted_digit_sum']);
        $this->db->set('predicted_winning_sum', $prediction['predicted_winning_sum']);
        $this->db->set('predicted_runners_up',  $prediction['predicted_runners_up']);
        $this->db->where('lottery_id', $lottery_id);
        return $this->db->update('lottery_highlights');
    }
    /** 
	* glance_data_save. Insert / Update the At a Glance Statistics to the database
	* 
	* @param 	array	$data		key / value pairs of Friend Profile to be inserted / updated
	* @param	boolean $exist		add a new entry (FALSE), if no previous friends has been added otherwise update the existing friends row (TRUE), default is FALSE
	* @return   boolean $success    TRUE on success, FALSE on failure (insert or update)	
	*/
	public function glance_data_save($data, $exist = FALSE)
	{
		if (!$exist) 
		{
			$this->db->set($data);		// Set the query with the key / value pairs
			$success = $this->db->insert('lottery_highlights');
		}
		else
		{
			$this->db->set($data);		// Set the query with the key / value pairs
			$this->db->where('lottery_id', $data['lottery_id']);
			$success = $this->db->update('lottery_highlights');
		}
    return $success;
	}
    /** 
	* parity_list. Insert / Update the At a Glance Statistics to the database
	* 
	* @param 	integer	$rows		$rows returned from the lottery table
	* @param	string  $tbl		Actual table name of the lottery
    * @param    boolean $e          Extra Draws, 0 = none, 1 = included
	* @return   array   $result     Array of the odd / even and total counts for the range, FALSE on failure	
	*/
	private function parity_list($rows, $e = 0, $tbl)
	{
		$this->db->reset_query();	// Clear any previous queries in the cache
        $ex_d = (!empty($e) ? " " : " WHERE extra <> 0 ");
        $query = $this->db->query("select odd, even, draw_date, count(*) from (SELECT * FROM 
        `".$tbl."`".$ex_d."ORDER BY draw_date DESC LIMIT ".$rows.") sub 
        group by odd, even ORDER BY draw_date ASC;");
        $result = $query->result_array();    
    return $result;
	}
    /** 
	* parity_list. Insert / Update the At a Glance Statistics to the database
	* @param    array   $draws      Draws within the given range
	* @param	integer $o	    	Odd Number
    * @param    integer $e          Even Number
    * @param    integer $l          Low number occurence
	* @return   string  $result     Draw occurences, draw date and number of skips between draws before the next one	
	*/
	private function parity_dates($draws, $o, $e, $l) 
	{
		$str = '';
        $blnstart = FALSE;
        $occur = 0;
        foreach($draws as $c => $d)
        {
          if(($d['odd']==$o)&&($d['even']==$e)&&($l))
          {
            $ld = $d['draw_date'];
            if($blnstart) $str .= $occur.",";
            $str .= $ld.",";
            $occur = 0; // Reset the count
            $blnstart = TRUE;
            $l--;
          }
          elseif(($blnstart)&&($l)) // Keep counting and there is another draw
          {
              $occur++;
          }
          elseif(!$l) break; // Break out of the interation as complete.
        }
    return substr($str, 0, -1);	// Return the string without an extra ','
	}
    /** 
	* Onlydrawn draw numbers from the last draw. Return only the numbers in an index array (1,2,3...)
	* 
	* @param 	array	$dr		    key / value pairs of the last drawn numbers in this lottery
	* @param	boolean $xt		    The lottery has an extra / bonus flag. No Extra Ball = 0 (FALSE), Extra/Bonus ball included = 1 (TRUE) 
   	* @param	boolean $dxb        The lottery has an duplicate extra / bonus flag. No Independent Extra Ball = 0 (FALSE), 
    *                               Extra/Bonus ball included = 1 (TRUE) independent ball
	* @return   array   $drawn      Return index array of only drawn numbers	
	*/
	public function onlydrawn($dr, $xt = 1, $dxb = 0)
	{
		$drawn = array();
        $ball = 1;
        unset($dr['id']);
        unset($dr['draw_date']);         
        do
        {
            if(isset($dr['ball'.$ball])) $drawn[$ball] = $dr['ball'.$ball];
            ++$ball;
        } while($ball<10);
        // Include extra ball if requested, regardless of duplicate_extra_ball setting
        // The $xt parameter indicates whether extra ball should be included in the display
        if($xt)
        {
            $next = array_key_last($drawn); // next available index key value
            if($next!=NULL) 
            {
                $next++;
                $drawn[$next] = $dr['extra'];  // include the extra / bonus (which is the last ball!)
            }
        }
        unset($dr);
    return $drawn;
	}
    /** 
	* Adds the prize array to each number drawm in the last draw and the position of each drawn number
	* 
	* @param 	array	$last_draw	 key / value pairs of the last drawn numbers in this lottery
	* @param	integer $drn		The number of drawn numbers for this lottery, e.g. Canada 649 has 6 numbers plus the extra / bonus number
	* @param	boolean $ex		    The lottery has an extra / bonus flag. No Extra Ball = 0 (FALSE), Extra/Bonus ball included = 1 (TRUE) 
	* @param	array   $pg 	    Array structure of the associated prize pool
	* @return   array   $last_draw  Return index array of the last drawn numbers including the associated array of the prize pool for each number drawn	
	*/
    public function last_draw_prizegroup($last_draw, $drn, $ex, $pg)
    {
        for($b = 1; $b <= $drn; $b++)
        {
            $found = FALSE;
            foreach ($last_draw as $key => $value) 
            {
                if ($key === 'ball'.$b && !$found) 
                {
                    // Insert the sub-array after 'ball
                    $last_draw[$key.'_win'] =  $pg;
                    $last_draw['position'.$b.'_win'] = $pg;
                    $found = true;
                }
            }
        }
        if($ex) // Extra / Bonus ball
        {
            $last_draw['extra_win'] = $pg;
            $last_draw['position_extra_win'] = $pg;
        }
    return $last_draw; // (array) of prize group arrays and positional prize group array
    }
    /** 
	* Adds the prize array to each number drawm in the last draw and the position of each drawn number
	* 
	* @param 	array	$last_draw	 key / value pairs of the last drawn numbers in this lottery
	* @param	integer $drn		The number of drawn numbers for this lottery, e.g. Canada 649 has 6 numbers plus the extra / bonus number
	* @param	boolean $ei		    The extra / bonus flag is used. No Extra Ball included = 0 (FALSE), Extra/Bonus ball included = 1 (TRUE)
    * @param	array   $pg		    Array of the prize group profile
	* @param	array   $fp 	    Array of all follower lottery balls prizes ($fp) not extracted. e.g. ball 1 = 2,2,3,0,4,1,5, etc.
   	* @param	array   $ps 	    Array of all follower lottery draw positions ($ps) not extracted. e.g. ball 1 = 2,2,3,0,4,1,5, etc.
  	* @return   array   $last_draw  Return index array of the last drawn numbers including the associated array of the prize pool for each number drawn	
	*/
    public function last_draw_addwins($last_draw,$drn,$ei,$pg,$fp,$ps)
    {
        // Canonical 19-category order used by the old wins-string format (get_empty_win_categories).
        // When a stored string has exactly 19 values per ball we use this map to look up the
        // correct index by category name so that lotteries with sparse prize profiles (e.g.
        // LottoMAX which has no extra/1_win/2_win tiers) are read correctly even from legacy data.
        $canonical_categories = ['extra','1_win','1_win_extra','2_win','2_win_extra',
                                  '3_win','3_win_extra','4_win','4_win_extra',
                                  '5_win','5_win_extra','6_win','6_win_extra',
                                  '7_win','7_win_extra','8_win','8_win_extra',
                                  '9_win','9_win_extra'];

        for($b = 1; $b<=$drn; $b++)
        {
            $ball = $last_draw['ball'.$b];
            
            // Add safety checks for array bounds
            $ball_index = $ball - 1;
            $position_index = $b - 1;
            
            if (isset($fp[$ball_index])) {
                $ball_prizes = explode(',',$fp[$ball_index]); 
            } else {
                $ball_prizes = ['0','0','0','0','0','0','0','0','0','0']; // Default zeros
            }
            
            if (isset($ps[$position_index])) {
                $position_prizes = explode(',',$ps[$position_index]); 
            } else {
                $position_prizes = ['0','0','0','0','0','0','0','0','0','0']; // Default zeros
            }

            // Detect legacy 19-value format vs. lottery-specific format
            $ball_uses_full_fmt     = (count($ball_prizes)     == 19);
            $position_uses_full_fmt = (count($position_prizes) == 19);

            $index = 0;     
            foreach($pg as $prize => $value)
            {
                // Ball wins
                if ($ball_uses_full_fmt) {
                    $cat_pos = array_search($prize, $canonical_categories);
                    $last_draw['ball'.$b.'_win'][$prize] = ($cat_pos !== false && isset($ball_prizes[$cat_pos])) ? $ball_prizes[$cat_pos] : '0';
                } else {
                    $last_draw['ball'.$b.'_win'][$prize] = isset($ball_prizes[$index]) ? $ball_prizes[$index] : '0';
                }

                // Position wins
                if ($position_uses_full_fmt) {
                    $cat_pos = array_search($prize, $canonical_categories);
                    $last_draw['position'.$b.'_win'][$prize] = ($cat_pos !== false && isset($position_prizes[$cat_pos])) ? $position_prizes[$cat_pos] : '0';
                } else {
                    $last_draw['position'.$b.'_win'][$prize] = isset($position_prizes[$index]) ? $position_prizes[$index] : '0';
                }

                $index++;
            }
        }
        if(($ei)) // Doesn't matter if duplicate extra / bonus
        {
            $extra = $last_draw['extra'];
            
            // Add safety checks for extra ball array bounds
            $extra_index = $extra - 1;
            $extra_position_index = $drn;
            
            if (isset($fp[$extra_index])) {
                $extra_prize = explode(',',$fp[$extra_index]);
            } else {
                $extra_prize = ['0','0','0','0','0','0','0','0','0','0']; // Default zeros
            }
            
            if (isset($ps[$extra_position_index])) {
                $position_prizes = explode(',',$ps[$extra_position_index]);
            } else {
                $position_prizes = ['0','0','0','0','0','0','0','0','0','0']; // Default zeros
            }

            $extra_uses_full_fmt    = (count($extra_prize)     == 19);
            $ex_pos_uses_full_fmt   = (count($position_prizes) == 19);

            $index = 0;
            foreach($pg as $prize => $value)
            {
                // Extra ball wins
                if ($extra_uses_full_fmt) {
                    $cat_pos = array_search($prize, $canonical_categories);
                    $last_draw['extra_win'][$prize] = ($cat_pos !== false && isset($extra_prize[$cat_pos])) ? $extra_prize[$cat_pos] : '0';
                } else {
                    $last_draw['extra_win'][$prize] = isset($extra_prize[$index]) ? $extra_prize[$index] : '0';
                }

                // Extra position wins
                if ($ex_pos_uses_full_fmt) {
                    $cat_pos = array_search($prize, $canonical_categories);
                    $last_draw['position_extra_win'][$prize] = ($cat_pos !== false && isset($position_prizes[$cat_pos])) ? $position_prizes[$cat_pos] : '0';
                } else {
                    $last_draw['position_extra_win'][$prize] = isset($position_prizes[$index]) ? $position_prizes[$index] : '0';
                }

                $index++;
            }
        }
    return $last_draw;   	
    }
    /** 
	* Adds the prize points element to each number drawm in the last draw and the position points element of each drawn number
	* 
	* @param 	array	$last_draw	 key / value pairs of the last drawn numbers in this lottery
	* @param	integer $drn		The number of drawn numbers for this lottery, e.g. Canada 649 has 6 numbers plus the extra / bonus number
	* @param	boolean $ex		    The lottery has an extra / bonus flag. No Extra Ball = 0 (FALSE), Extra/Bonus ball included = 1 (TRUE) 
	* @param	boolean $duplicate_extra_ball	Whether this lottery has an independent extra ball (duplicate_extra_ball = 1)
	* @return   array   $last_draw  Return index array of the last drawn numbers including the associated array of the prize pool for each number drawn	
	*/
    public function last_draw_addpoints($last_draw, $drn, $ex, $duplicate_extra_ball = 0)
    {
        if ($duplicate_extra_ball) {
            // Category mapping for point calculation (independent extra ball lotteries only)
            $category_mapping = array(
                'extra' => 1,
                '1_win' => 2,
                '1_win_extra' => 3,
                '2_win' => 4,
                '2_win_extra' => 5,
                '3_win' => 6,
                '3_win_extra' => 7,
                '4_win' => 8,
                '4_win_extra' => 9,
                '5_win' => 10,
                '5_win_extra' => 11,
                '6_win' => 12,
                '6_win_extra' => 13,
                '7_win' => 14,
                '7_win_extra' => 15,
                '8_win' => 16,
                '8_win_extra' => 17,
                '9_win' => 18,
                '9_win_extra' => 19
            );
            
            // For each ball (using category mapping)
            for ($b = 1; $b <= $drn; $b++) {
                // Ball win
                $win_key = 'ball' . $b . '_win';
                $total_key = 'ball' . $b . '_total';
                $last_draw[$total_key] = 0;
                if (isset($last_draw[$win_key]) && is_array($last_draw[$win_key])) {
                    foreach ($last_draw[$win_key] as $cat => $val) {
                        $point_value = isset($category_mapping[$cat]) ? $category_mapping[$cat] : 1;
                        $points = $point_value * intval($val);
                        $last_draw[$total_key] += $points;
                        // Store points inside the win array
                        $last_draw[$win_key][$cat . '_points'] = $points;
                    }
                }
                // Position win
                $pos_key = 'position' . $b . '_win';
                $pos_total_key = 'position' . $b . '_total';
                $last_draw[$pos_total_key] = 0;
                if (isset($last_draw[$pos_key]) && is_array($last_draw[$pos_key])) {
                    foreach ($last_draw[$pos_key] as $cat => $val) {
                        $point_value = isset($category_mapping[$cat]) ? $category_mapping[$cat] : 1;
                        $points = $point_value * intval($val);
                        $last_draw[$pos_total_key] += $points;
                        // Store points inside the position win array
                        $last_draw[$pos_key][$cat . '_points'] = $points;
                    }
                }
            }
            // For extra ball if $ex is true (using category mapping)
            if ($ex) {
                if (isset($last_draw['extra_win']) && is_array($last_draw['extra_win'])) {
                    $last_draw['extra_total'] = 0;
                    foreach ($last_draw['extra_win'] as $cat => $val) {
                        $point_value = isset($category_mapping[$cat]) ? $category_mapping[$cat] : 1;
                        $points = $point_value * intval($val);
                        $last_draw['extra_total'] += $points;
                        $last_draw['extra_win'][$cat . '_points'] = $points;
                    }
                }
                if (isset($last_draw['position_extra_win']) && is_array($last_draw['position_extra_win'])) {
                    $last_draw['position_extra_total'] = 0;
                    foreach ($last_draw['position_extra_win'] as $cat => $val) {
                        $point_value = isset($category_mapping[$cat]) ? $category_mapping[$cat] : 1;
                        $points = $point_value * intval($val);
                        $last_draw['position_extra_total'] += $points;
                        $last_draw['position_extra_win'][$cat . '_points'] = $points;
                    }
                }
            }
        } else {
            // Original logic for regular lotteries (using incrementing counter)
            // For each ball
            for ($b = 1; $b <= $drn; $b++) {
                // Ball win
                $win_key = 'ball' . $b . '_win';
                $total_key = 'ball' . $b . '_total';
                $last_draw[$total_key] = 0;
                if (isset($last_draw[$win_key]) && is_array($last_draw[$win_key])) {
                    $win_counter = 1;
                    foreach ($last_draw[$win_key] as $cat => $val) {
                        $points = $win_counter * intval($val);
                        $last_draw[$total_key] += $points;
                        // Store points inside the win array
                        $last_draw[$win_key][$cat . '_points'] = $points;
                        $win_counter++;
                    }
                }
                // Position win
                $pos_key = 'position' . $b . '_win';
                $pos_total_key = 'position' . $b . '_total';
                $last_draw[$pos_total_key] = 0;
                if (isset($last_draw[$pos_key]) && is_array($last_draw[$pos_key])) {
                    $win_counter = 1;
                    foreach ($last_draw[$pos_key] as $cat => $val) {
                        $points = $win_counter * intval($val);
                        $last_draw[$pos_total_key] += $points;
                        // Store points inside the position win array
                        $last_draw[$pos_key][$cat . '_points'] = $points;
                        $win_counter++;
                    }
                }
            }
            // For extra ball if $ex is true (using incrementing counter)
            if ($ex) {
                if (isset($last_draw['extra_win']) && is_array($last_draw['extra_win'])) {
                    $last_draw['extra_total'] = 0;
                    $win_counter = 1;
                    foreach ($last_draw['extra_win'] as $cat => $val) {
                        $points = $win_counter * intval($val);
                        $last_draw['extra_total'] += $points;
                        $last_draw['extra_win'][$cat . '_points'] = $points;
                        $win_counter++;
                    }
                }
                if (isset($last_draw['position_extra_win']) && is_array($last_draw['position_extra_win'])) {
                    $last_draw['position_extra_total'] = 0;
                    $win_counter = 1;
                    foreach ($last_draw['position_extra_win'] as $cat => $val) {
                        $points = $win_counter * intval($val);
                        $last_draw['position_extra_total'] += $points;
                        $last_draw['position_extra_win'][$cat . '_points'] = $points;
                        $win_counter++;
                    }
                }
            }
        }
        return $last_draw;
    }

	/**
	 * Analyse H-W-C patterns combined with dynamic follower hits for all balls.
	 * For each ball 1..max_ball, scans historical draw pairs to find which H-W-C
	 * draw pattern produced the most follower hits in the next draw.
	 *
	 * @param  string  $tbl_name       Lottery draw table name
	 * @param  int     $picks          Balls drawn per draw
	 * @param  int     $max_ball       Highest ball number in the lottery
	 * @param  bool    $extra_included Whether extra/bonus ball is included in analysis
	 * @param  bool    $extra_draws    Whether to include draws with extra = 0
	 * @param  int     $h_count        Number of Hot positions
	 * @param  int     $w_count        Number of Warm positions
	 * @param  string  $hots_str       Hots string from lottery_h_w_c table ("n=count,...")
	 * @param  string  $warms_str      Warms string
	 * @param  string  $colds_str      Colds string
	 * @param  int     $range          Draw range (capped at 500)
	 * @return array   Results array keyed by ball number, sorted by best avg hits desc
	 */
	public function get_hwc_follower_stats($tbl_name, $picks, $max_ball, $extra_included, $extra_draws, $h_count, $w_count, $hots_str, $warms_str, $colds_str, $range, $is_dup_extra = false, $max_extra_ball = 0)
	{
		$range = min(500, intval($range));

		// Build H-W-C lookup: ball_number => 'H' | 'W' | 'C'
		$hwc_lookup = array();
		foreach (explode(',', $hots_str) as $entry) {
			$n = strstr($entry, '=', true);
			if ($n !== false && $n !== '') $hwc_lookup[intval($n)] = 'H';
		}
		foreach (explode(',', $warms_str) as $entry) {
			$n = strstr($entry, '=', true);
			if ($n !== false && $n !== '') $hwc_lookup[intval($n)] = 'W';
		}
		foreach (explode(',', $colds_str) as $entry) {
			$n = strstr($entry, '=', true);
			if ($n !== false && $n !== '') $hwc_lookup[intval($n)] = 'C';
		}

		// Load range+1 draws (oldest first) so we have range draw pairs (draw i, draw i+1)
		$draws = $this->load_history($tbl_name, 0, $range + 1, $extra_draws);
		if (!$draws || count($draws) < 2) return array();

		$total = count($draws);

		// Build flat integer-ball arrays for speed.
		// $draw_main_balls: main picks only — used for H-W-C pattern classification.
		// $draw_balls:      main + extra (when extra_included) — used for follower computation.
		$draw_balls      = array();
		$draw_main_balls = array();
		$extra_draw_ball = array(); // single extra ball per draw (dup_extra only)
		foreach ($draws as $idx => $draw) {
			$main = array();
			for ($i = 1; $i <= $picks; $i++) {
				$key = 'ball' . $i;
				if (isset($draw[$key]) && intval($draw[$key]) > 0)
					$main[] = intval($draw[$key]);
			}
			$draw_main_balls[$idx] = $main;   // main balls only
			$balls = $main;
			if ($extra_included && isset($draw['extra']) && intval($draw['extra']) > 0) {
				$eb = intval($draw['extra']);
				if ($is_dup_extra) {
					$extra_draw_ball[$idx] = $eb; // separate pool — do not mix with main
				} else {
					$balls[] = $eb;               // merge into main pool
				}
			}
			if ($is_dup_extra && !isset($extra_draw_ball[$idx])) {
				$extra_draw_ball[$idx] = null;
			}
			$draw_balls[$idx] = $balls;       // main only (dup_extra) or main+extra (normal)
		}

		// Step 1: Compute dynamic followers for every ball that appears in draws
		// followers[$ball][$follower] = occurrence count across all draw pairs
		$followers = array();
		for ($i = 0; $i < $total - 1; $i++) {
			foreach ($draw_balls[$i] as $ball) {
				if (!isset($followers[$ball])) $followers[$ball] = array();
				foreach ($draw_balls[$i + 1] as $next) {
					if (!isset($followers[$ball][$next])) $followers[$ball][$next] = 0;
					$followers[$ball][$next]++;
				}
			}
		}
		// Apply minimum threshold (>= 3), matching the existing system
		foreach ($followers as $ball => $flist) {
			foreach ($flist as $fb => $cnt) {
				if ($cnt < 3) unset($followers[$ball][$fb]);
			}
		}

		// Step 1b: Compute extra-pool followers for duplicate_extra_ball lotteries
		$extra_followers = array();
		if ($is_dup_extra) {
			for ($i = 0; $i < $total - 1; $i++) {
				$eb = isset($extra_draw_ball[$i])     ? $extra_draw_ball[$i]     : null;
				$nb = isset($extra_draw_ball[$i + 1]) ? $extra_draw_ball[$i + 1] : null;
				if ($eb !== null && $nb !== null) {
					if (!isset($extra_followers[$eb])) $extra_followers[$eb] = array();
					if (!isset($extra_followers[$eb][$nb])) $extra_followers[$eb][$nb] = 0;
					$extra_followers[$eb][$nb]++;
				}
			}
			foreach ($extra_followers as $ball => $flist) {
				foreach ($flist as $fb => $cnt) {
					if ($cnt < 3) unset($extra_followers[$ball][$fb]);
				}
			}
		}

		// Step 2: Walk every draw pair once; for each ball in the draw,
		//         classify the full draw as an H-W-C pattern and count follower hits
		$pattern_stats       = array(); // [ball][pattern] = array(times, total_hits, max_hits)
		$extra_pattern_stats = array(); // same but for extra-pool (dup_extra only)
		$times_drawn         = array_fill(1, $max_ball, 0);
		$extra_times_drawn   = ($is_dup_extra && $max_extra_ball > 0) ? array_fill(1, $max_extra_ball, 0) : array();

		for ($i = 0; $i < $total - 1; $i++) {
			$curr      = $draw_balls[$i];      // all balls (main + extra) for follower tracking
			$curr_main = $draw_main_balls[$i]; // main balls only for H-W-C classification
			$next      = $draw_balls[$i + 1];

			// Classify the current draw into its H-W-C pattern (main balls only — no extra ball)
			$h = 0; $w = 0; $c = 0;
			foreach ($curr_main as $b) {
				switch (isset($hwc_lookup[$b]) ? $hwc_lookup[$b] : 'C') {
					case 'H': $h++; break;
					case 'W': $w++; break;
					default:  $c++; break;
				}
			}
			$pattern = "{$h}-{$w}-{$c}";

			// Build fast lookup for next-draw balls
			$next_lookup = array_flip($next);

			foreach ($curr as $ball) {
				if ($ball < 1 || $ball > $max_ball) continue;
				$times_drawn[$ball]++;

				// Count how many of this ball's followers appeared in the next draw
				$hits = 0;
				if (!empty($followers[$ball])) {
					foreach (array_keys($followers[$ball]) as $fb) {
						if (isset($next_lookup[$fb])) $hits++;
					}
				}

				if (!isset($pattern_stats[$ball])) $pattern_stats[$ball] = array();
				if (!isset($pattern_stats[$ball][$pattern]))
					$pattern_stats[$ball][$pattern] = array('times' => 0, 'total_hits' => 0, 'non_follower_hits' => 0, 'max_hits' => 0);

				$pattern_stats[$ball][$pattern]['times']++;
				$pattern_stats[$ball][$pattern]['total_hits'] += $hits;
				// Non-follower hits = next-draw balls NOT in this ball's follower set
				$non_hits = 0;
				foreach ($next as $nb) {
					if (empty($followers[$ball]) || !isset($followers[$ball][$nb])) $non_hits++;
				}
				$pattern_stats[$ball][$pattern]['non_follower_hits'] += $non_hits;
				if ($hits > $pattern_stats[$ball][$pattern]['max_hits'])
					$pattern_stats[$ball][$pattern]['max_hits'] = $hits;
			}

			// Track extra ball stats against the same main-ball H-W-C pattern (dup_extra only)
			if ($is_dup_extra && $max_extra_ball > 0) {
				$xb = isset($extra_draw_ball[$i]) ? $extra_draw_ball[$i] : null;
				if ($xb !== null && $xb >= 1 && $xb <= $max_extra_ball) {
					$extra_times_drawn[$xb]++;
					$next_xb = isset($extra_draw_ball[$i + 1]) ? $extra_draw_ball[$i + 1] : null;
					$xhits = ($next_xb !== null && !empty($extra_followers[$xb]) && isset($extra_followers[$xb][$next_xb])) ? 1 : 0;
					$xnon  = ($next_xb !== null && (empty($extra_followers[$xb]) || !isset($extra_followers[$xb][$next_xb]))) ? 1 : 0;
					if (!isset($extra_pattern_stats[$xb])) $extra_pattern_stats[$xb] = array();
					if (!isset($extra_pattern_stats[$xb][$pattern]))
						$extra_pattern_stats[$xb][$pattern] = array('times' => 0, 'total_hits' => 0, 'non_follower_hits' => 0, 'max_hits' => 0);
					$extra_pattern_stats[$xb][$pattern]['times']++;
					$extra_pattern_stats[$xb][$pattern]['total_hits']       += $xhits;
					$extra_pattern_stats[$xb][$pattern]['non_follower_hits'] += $xnon;
					if ($xhits > $extra_pattern_stats[$xb][$pattern]['max_hits'])
						$extra_pattern_stats[$xb][$pattern]['max_hits'] = $xhits;
				}
			}
		}

		// Step 3: Build final results per ball
		$results = array();
		for ($ball = 1; $ball <= $max_ball; $ball++) {
			$bstats = isset($pattern_stats[$ball]) ? $pattern_stats[$ball] : array();

			// Calculate average hits per pattern occurrence (kept for row colour coding)
			foreach ($bstats as $pattern => &$ps) {
				$ps['avg'] = $ps['times'] > 0 ? round($ps['total_hits'] / $ps['times'], 2) : 0;
			}
			unset($ps);

			// Sort patterns: most times occurred first, then total_hits desc
			uasort($bstats, function($a, $b) {
				if ($b['times'] != $a['times']) return $b['times'] - $a['times'];
				return $b['total_hits'] - $a['total_hits'];
			});

			// Top pattern is first after sorting
			reset($bstats);
			$best_key = key($bstats);
			$best = $best_key !== null
				? $bstats[$best_key]
				: array('times' => 0, 'total_hits' => 0, 'avg' => 0, 'max_hits' => 0);

			$results[$ball] = array(
				'ball'              => $ball,
				'times_drawn'       => $times_drawn[$ball],
				'follower_count'    => isset($followers[$ball]) ? count($followers[$ball]) : 0,
				'best_pattern'      => $best_key !== null ? $best_key : '-',
				'best_times'        => $best['times'],
				'best_hits'         => $best['total_hits'],
				'best_non_hits'     => $best['non_follower_hits'],
				'best_avg'          => $best['avg'],
				'best_max'          => $best['max_hits'],
				'all_patterns'      => $bstats,
			);
		}

		// Sort all balls: most times in best pattern first, then total_hits desc
		uasort($results, function($a, $b) {
			if ($b['best_times'] != $a['best_times']) return $b['best_times'] - $a['best_times'];
			return $b['best_hits'] - $a['best_hits'];
		});

		// Build extra-pool results (duplicate_extra_ball lotteries only)
		$extra_results = array();
		if ($is_dup_extra && $max_extra_ball > 0) {
			for ($ball = 1; $ball <= $max_extra_ball; $ball++) {
				$bstats = isset($extra_pattern_stats[$ball]) ? $extra_pattern_stats[$ball] : array();
				foreach ($bstats as $pattern => &$ps) {
					$ps['avg'] = $ps['times'] > 0 ? round($ps['total_hits'] / $ps['times'], 2) : 0;
				}
				unset($ps);
				uasort($bstats, function($a, $b) {
					if ($b['times'] != $a['times']) return $b['times'] - $a['times'];
					return $b['total_hits'] - $a['total_hits'];
				});
				reset($bstats);
				$best_key = key($bstats);
				$best = $best_key !== null
					? $bstats[$best_key]
					: array('times' => 0, 'total_hits' => 0, 'non_follower_hits' => 0, 'avg' => 0, 'max_hits' => 0);
				$extra_results[$ball] = array(
					'ball'           => $ball,
					'times_drawn'    => $extra_times_drawn[$ball],
					'follower_count' => isset($extra_followers[$ball]) ? count($extra_followers[$ball]) : 0,
					'best_pattern'   => $best_key !== null ? $best_key : '-',
					'best_times'     => $best['times'],
					'best_hits'      => $best['total_hits'],
					'best_non_hits'  => $best['non_follower_hits'],
					'best_avg'       => $best['avg'],
					'best_max'       => $best['max_hits'],
					'all_patterns'   => $bstats,
				);
			}
			uasort($extra_results, function($a, $b) {
				if ($b['best_times'] != $a['best_times']) return $b['best_times'] - $a['best_times'];
				return $b['best_hits'] - $a['best_hits'];
			});
		}

		return array('main' => $results, 'extra' => $extra_results);
	}
}