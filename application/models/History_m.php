<?php
class History_m extends MY_Model
{
    /**
	 * This load_history loads a range od draws in ascending order and returned as an array. 
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
     * @param		integer     $coverage	Range of draws
     * @param		boolean     $e      	Extra Draw Coverage, 0 = no (False) do not add extra draws with a zero extra ball, 
     * 1 = yes (true) include the extra draws where the extra = 0 (or is all the balls drawn where the extra ball equals zero)
	 * @return      array		$history    Array of lottery draws for a given range	
	 */
    public function load_history($tbl, $lotto_id, $coverage, $e = 0)
    {
        // todo: load the range of lottery draws, ascending order
        $this->db->reset_query();	// Clear any previous queries that are cachedextra !=' => '0');
        $ex_d = (empty($e) ? ' AND extra <> "0"' : '');
  
        $query = $this->db->query('SELECT d.*
                                    FROM (
                                    SELECT *
                                    FROM '.$tbl.'
                                    WHERE lottery_id="'.$lotto_id.
                                    '"'.$ex_d.    
                                    ' ORDER BY id 
                                    ) d ORDER BY d.id DESC
                                    LIMIT '.$coverage.';'); // Utilized id instead of draw_date for ORDER BY
        $history = $query->result_array();
    return (!is_null($history) ? $history : FALSE); // Returns a false if the query did not return results    
    }
    /* glance_exist with query for a single row result from the lottery_id
     * @param		integer     $lotto_id	Lottery id
	 * @return      boolean		TRUE/FALSE  At A Glance Statistics Exist (TRUE) / Do not exist (FALSE)	
	 */
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
     * @param 		boolean 	$ex 		extra draws used
     * @param 		boolean 	$b_ex       Bonus ball used		    
	 * @return      string		$trend      Concatenated String. Format: up=14,down=5,2021-05-10,15,down	
	 */
    public function trend_history($draws, $pick, $ex = FALSE, $b_ex = FALSE)
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
                if($change==intval($pick)) // All Up
                {
                    $up++;  
                    $ud = $draws[$count+1]['draw_date']; // Record the most recent date for an up occurrence
                    $lt = 'up'; // last trend was up
                } 
                else if($change==(intval(-$pick)))
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
                        $ud = $draws[$count+1]['draw_date']; // Record the most recent date for an up occurrence
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
        $trend .= ($up>=$down ? ','.$ud.','.$lt.','.$up.',up' : ','.$dd.','.$lt.','.$down.',down');
    return $trend;    
    }
    /**
	 * repeat_history tabulates the number of repeats over a given range and then looks at the most probable numbers that will be drawn for the next draw
	 * 
	 * @param       array       $draws      Array of draws for a given range
     * @param       integer     $pick       Pick Game. Pick 7, Pick 6, Pick 5 
	 * @param 		boolean 	$ex 		extra draws used
     * @param 		boolean 	$b_ex       Bonus ball used		    
	 * @return      string		$r_values  Concatenated String. Format: 0=75,1=10,2=5,3=5,4=3,5=0,6=0|3=7,22=2. e.g. Pick 6 then looks at 6 number repeat maximum and pipe
     *                                     will separate the highest probable number(s) to be drawn for the next draw.	
	 */
    public function repeat_history($draws, $pick, $ex = FALSE, $b_ex = FALSE)
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
                    if($draw['ball'.$c]==$draws[$count+1]['ball'.$c])
                    {
                        $repeats++;
                        $next[$draw['ball'.$c]] = ((!array_key_exists($draw['ball'.$c], $next)) ? 1 : $next[$draw['ball'.$c]]+1); // Add Key or Existing One?
                    }
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
     * @param       integer     $limit             The minimum value that is used in the comparision to return true
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
            if(($total-1)!=$count)
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
	 * @return      string		$adj_text       Concatenated String. Format: . e.g. Pick 6 average difference, 1=5,2=5,3=2,4=7,5=5|2=14
	 */
    public function adjacents_history($draws, $pick)
    {
        $total = count($draws);
        $lg_diff = 0;           // Largest difference for any draw
        $adjacents = $this->zeroed(new SplFixedArray($pick), $pick);    // include the zero adacents, this is now zero based with zero occurrences
        
        $adj = 1;   // Set to the ball 1 to ball 2
        foreach($draws as $count => $draw)
        {
            if(($total-1)!=$count)
            {
                for($c=1; $c<$pick; $c++)                     // Interate the draw for changes from the previous draw and the next draw
                {
                    $diff = intval($draw['ball'.($c+1)])-intval($draw['ball'.$c]);
                    $adjacents[$c]  += $diff;
                    if($lg_diff<$diff) 
                    {
                        $lg_diff = $diff;     // The current (diff)erence for any draw is now the largest (lgdiff)erence     
                        $adj = $c;            // Where did this occur?  
                    }
                }
            }
        }
        $adj_text = '';
        unset($draws);
        for($c=1; $c<$pick; $c++)
        {
            $adjacents[$c] = round(($adjacents[$c] / $total)); // Nearest int <.5 or >.5
            $adj_text .= $c.'='.$adjacents[$c].',';
        }
        $adj_text = substr_replace($adj_text, '|', -1);	    // Replace the ',' with the '|' (pipe)
        $adj_text .= $adj.'='.$lg_diff;                     // include the last draw date of consecutive occurrence
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
            if(($total-1)!=$count)
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
            if(($total-1)!=$count)
            {
                $digits[$draw['sum_digits']] = (!array_key_exists($draw['sum_digits'], $digits) ? 1 : $digits[$draw['sum_digits']]+1); // Add Key or Existing One?
                if(!empty($count)) // if not 0
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
            if(($total-1)!=$count)
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
        $ex_d = (empty($e) ? "WHERE extra <> '0' " : " ");
        $query = $this->db->query("select odd, even, draw_date, count(*) from (SELECT * FROM 
        `".$tbl."`".$ex_d."ORDER BY id DESC LIMIT ".$rows.") sub 
        group by odd, even ORDER BY id ASC;");
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
}