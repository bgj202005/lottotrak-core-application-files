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
	 * @return      array		$history    Array of lottery draws for a given range	
	 */
    public function load_history($tbl, $lotto_id, $coverage)
    {
        // todo: load the range of lottery draws, ascending order
        $this->db->reset_query();	// Clear any previous queries that are cachedextra !=' => '0');
        $query = $this->db->query('SELECT d.*
                                    FROM (
                                    SELECT *
                                    FROM '.$tbl.'
                                    WHERE lottery_id='.$lotto_id.'
                                    ORDER BY id DESC
                                    LIMIT '.$coverage.'
                                    ) d
                                    ORDER BY d.id;'); // Utilized id instead of draw_date for ORDER BY
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
        if($b_ex) // Bonus (Extra) can be set but not the extra draw
        {
            foreach($draws as $c => $draw)
            {
                if($draw['extra']==0) unset($draw[$c]); 
            }
        }
        foreach($draws as $count => $draw)
        {
            if(($total-1)!=$count)  // Most Recent Draw in db?
            {
                $change = 0;
                if((!$ex&&$draw['extra']!=0)||($ex&&$draw['extra']==0)) // For this to be included, extra ball is not set and the extra must noy be zero, or
                {                                                       // if the extra ball is set, the extra ball must be zero
                    for($c=1; $c<=$pick; $c++) // Interate the draw for changes from the previous draw and the next draw
                    {
                        $change = $change+intval($this->trend($draw['ball'.$c], $draws[$count+1]['ball'.$c]));
                    }
                    if($change==intval($pick)) // All Up
                    {
                        $up++;  // Yes
                        $ud = $draws[$count+1]['draw_date']; // Record the most recent date for an up occurrence
                    } 
                    else if($change==(intval(-$pick)))
                    {
                        $down++;
                        $dd = $draws[$count+1]['draw_date']; // Record the most recent date for an up occurrence
                    }
                    if($b_ex&&$draw['extra']!=0)  // Now look at the extra or bonus ball
                    {
                        $change = $change+intval($this->trend($draw['extra'], $draws[$count+1]['extra']));
                        if($change==intval($pick+1)) // All Up
                        {
                            $up++;  // Yes
                            $ud = $draws[$count+1]['draw_date']; // Record the most recent date for an up occurrence
                        } 
                        else if($change==(intval(-($pick+1))))
                        {
                            $down++;
                            $dd = $draws[$count+1]['draw_date']; // Record the most recent date for an up occurrence
                        }
                    }
                }
            }
        }
        $trend = 'up='.$up.',down='.$down;              // Format is 'up=xx,down=xx,drawdate,top,up/down'
        $trend .= ($up>=$down ? ','.$ud.','.$up.',up' : ','.$dd.','.$down.',down');
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
     *                                      will separate the highest probable number(s) to be drawn for the next draw.	
	 */
    public function repeat_history($draws, $pick, $ex = FALSE, $b_ex = FALSE)
    {
        $total = count($draws);
        $next = array();                                                    // empty set for the top picks
        $repeaters = $this->zeroed(new SplFixedArray($pick+1), $pick+1);        // include the zero repeaters
        
        foreach($draws as $count => $draw)
        {
            if(($total-1)!=$count)
            {
            $repeats = 0;    
            if((!$ex&&$draw['extra']!=0)||($ex&&$draw['extra']==0)) // For this to be included, extra ball is not set and the extra must noy be zero, or
                {                                                   // if the extra ball is set, the extra ball must be zero
                    for($c=1; $c<=$pick; $c++)                      // Interate the draw for changes from the previous draw and the next draw
                    {
                        if($draw['ball'.$c]==$draws[$count+1]['ball'.$c])
                        {
                            $repeats++;
                            if(!empty($next))   // In case no keys to look at and to prevent the undefined offset message
                            {
                                $next[$draw['ball'.$c]] = (array_key_exists($draw['ball'.$c], $next) ? 1 : $next[$draw['ball'.$c]]+1); // Add Key or Existing One?
                            }
                        }
                    }
                    if($b_ex&&$draw['extra']!=0) // Extra included in the Repeaters
                    {
                        $repeats++;
                    }
                }
            }
            if($repeats!=0) $repeaters[$repeats] +=1;    // Count the number of repeaters for the next draw
            else $repeaters[0] += 1;                     // If no repeats, do count the draws that did not have a repeat
        }
        $r_text = '';
        foreach($repeaters as $c => $r)
        {
            $r_text .= $c.'='.$r.',';
        }
        $r_text = substr_replace($r_text, '|', -1);	    // Replace the ',' with the '|' (pipe)
        rsort($next);                                   // Sort by value descending
        if($this->top_pick($next, 5))
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
            if($value>$limit) return true;
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
        if($b_ex) $pick++;
        $consecutives = $this->zeroed(new SplFixedArray($pick+1), $pick+1);         // include the zero consecutives
        
        foreach($draws as $count => $draw)
        {
            if(($total-1)!=$count)
            {
            $consecutives_draw = 0;    
            if((!$ex&&$draw['extra']!=0)||($ex&&$draw['extra']==0))     // For this to be included, extra ball is not set and the extra must noy be zero, or
                {                                                       // if the extra ball is set, the extra ball must be zero
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
            }
            if($consecutives_draw!=0) $consecutives[$consecutives_draw] +=1;    // Count the number of consecutives for this draw
            else $consecutives[0] += 1;                                       // If no consecutives, do count the draws that did not have a consecutive
        }
        $c_text = '';
        foreach($consecutives as $n => $c)
        {
            $c_text .= $n.'='.$c.',';
        }
        $c_text = substr_replace($c_text, '|', -1);	    // Replace the ',' with the '|' (pipe)
        $c_text .= $lcd;                             // include the last draw date of consecutive occurrence
    return $c_text;                         
    }
    /**
	 * adjacent_history averrages the number of adjacents of each ball position for balls 1 and 2, 2 and 3, 3 and 4, 4 and 5, 5 and 6 for a pick 6 game. 
     * 6 and 7 for Pick 7. 7 and 8 for Pick 8, etc. The maximum separation is included between any balls is added.
	 * 
	 * @param       array       $draws          Array of draws for a given range
     * @param       integer     $pick           Pick Game. Pick 7, Pick 6, Pick 5 
	 * @return      string		$adj_text       Concatenated String. Format: . e.g. Pick 6 average difference, 1=5,2=5,3=2,4=7,5=5|14
	 */
    public function adjacents_history($draws, $pick)
    {
        $total = count($draws);
        $lg_diff = 0;           // Largest difference for any draw
        $adjacents = $this->zeroed(new SplFixedArray($pick), $pick);    // include the zero adacents, this is now zero based with zero occurrences
        
        foreach($draws as $count => $draw)
        {
            if(($total-1)!=$count)
            {
                for($c=1; $c<$pick; $c++)                     // Interate the draw for changes from the previous draw and the next draw
                {
                    $diff = 0;
                    $diff = intval($draw['ball'.($c+1)])-intval($draw['ball'.$c]);
                    $adjacents[$c]  += $diff;
                    if($lg_diff<$diff) $lg_diff = $diff;     // The current (diff)erence for any draw is now the largest (lgdiff)erence     
                }
            }
        }

        $adj_text = '';
        for($c=1; $c<$pick; $c++)
        {
            $adjacents[$c] = ceil(($adjacents[$c] / $total));
            $adj_text .= $c.'='.$adjacents[$c].',';
        }

        $adj_text = substr_replace($adj_text, '|', -1);	    // Replace the ',' with the '|' (pipe)
        $adj_text .= $lg_diff;                              // include the last draw date of consecutive occurrence
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
        $sums = array();                                      // empty set for the top sums
        $percents = array(-50,-45,-40,-35,-30,-25,-20,-15,-10,-5,5,10,15,20,25,30,35,40,45,50); // ranges of percentages for both positive and negagtive
        // 0-5%, 6-10%, 11-15%, 16-20%, 21-25%, 26-30%, 31-35%, 36-40%, 41-45% and 46-50% 
        
        $ranges = $this->zeroed(new SplFixedArray(20), 20);   // 20 Percentage Ranges
        
        foreach($draws as $count => $draw)
        {
            if(($total-1)!=$count)
            {
                $sums[$draw['sum_draw']] = (!array_key_exists($draw['sum_draw'], $sums) ? 1 : $sums[$draw['sum_draw']]+1); // Add Key or Existing One?
                if(!empty($count)) // if not 0
                {
                    //$diff = $draws[$count]['sum_draw']-$draws[$count-1]['sum_draw'];                                // Formula for percentage difference
                    $percent_diff = (1-$draws[$count]['sum_draw']/$draws[$count-1]['sum_draw'])*100;   // Perecentage Difference = |ΔV|[ΣV2]×100
                    $percent_diff = round($percent_diff,0);
                    $flag = FALSE;
                    foreach($percents as $r => $v)
                    {
                        if(($percent_diff<=$v)&&(!$flag)) 
                        {
                            $ranges[$r] += 1;
                            $flag = TRUE;
                        }
                    }
                }   
            }
        }
        $s_text = "";
        arsort($sums);    // Sort by value NOT Key DESCENTDING
        if($this->top_pick($sums,2))
        {
            $i = 10; // Only 4 Top Numbers;
            foreach ($sums as $k => $v)
            {
                $s_text .= $k.'='.$v.',';
                $i--;
                if($i<0) 
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
        // 0-5%, 6-10%, 11-15%, 16-20%, 21-25%, 26-30%, 31-35%, 36-40%, 41-45% and 46-50% 
        
        $ranges = $this->zeroed(new SplFixedArray(20), 20);   // 20 Percentage Ranges
        
        foreach($draws as $count => $draw)
        {
            if(($total-1)!=$count)
            {
                $digits[$draw['sum_digits']] = (!array_key_exists($draw['sum_digits'], $digits) ? 1 : $digits[$draw['sum_digits']]+1); // Add Key or Existing One?
                if(!empty($count)) // if not 0
                {
                    //$diff = $draws[$count]['sum_draw']-$draws[$count-1]['sum_draw'];                                // Formula for percentage difference
                    $percent_diff = (1-$draws[$count]['sum_digits']/$draws[$count-1]['sum_digits'])*100;   // Perecentage Difference = |ΔV|[ΣV2]×100
                    $percent_diff = round($percent_diff,0); // No decimals
                    $flag = FALSE;
                    foreach($percents as $r => $v)
                    {
                        if(($percent_diff<=$v)&&(!$flag)) 
                        {
                            $ranges[$r] += 1;
                            $flag = TRUE;
                        }
                    }
                }   
            }
        }
        $d_text = "";
        arsort($digits);    // Sort by value NOT Key only value DESCENDING
        if($this->top_pick($digits,2))
        {
            $i = 10; // Only 4 Top Numbers;
            foreach ($digits as $k => $v)
            {
                $d_text .= $k.'='.$v.',';
                $i--;
                if($i<0) 
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
	 * range_history calculates the difference between the lowest drawn mnumber and the highest drawn number 
	 * The top 5 ranges will be summarized over the given range of draws
	 * @param       array       $draws          Array of draws for a given range
     * @param       integer     $pick           Pick Game. Pick 7, Pick 6, Pick 5 
 	 * @return      string		$r_text         Concatenated String. Format: 42=10,23=8,11=8,23=7,
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
        $r_text = "";
        if($this->top_pick($ranges,5))  // Must have a minimum count of 5
        {
            $i = 10; // Only 4 Top Numbers;
            foreach ($ranges as $k => $v)
            {
                $r_text .= $k.'='.$v.',';
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
     * @param       integer     $pick           Pick Game. Pick 7, Pick 6, Pick 5
 	 * @return      string		$oe_text        Concatenated String. Format: 4-3=55,3-4=34,5-2=20,2-5=15,1-6=12,6-1=8,7-0=6,0-7=4|7-0=2020-11-0,0-7=2021-10-13
     * the number of occurences must exceed the average for that odd / even combination based from the range to be included
     * Low occurrences over a given range will also be included. For example, in a pick 7, if the odd/even was 7-0 and 5 occurrences in the last 100 draws. This will 
     * be included with the last draw date of the occurence.
	 */
    public function parity_history($draws, $pick, $lotto)
    {
        // Step 1: Return all the odd-even combinations from the moss occurrences to the least
        $total  = count($draws);
        $parity = $this->parity_list($total, $lotto);           // Return the Odds and Evens over the given range
        if(!$parity) return FALSE;                              // could not return the query and return FALSE
        $top = array();
        foreach($parity as $count => $oddevens)
        {
            $top[$oddevens['odd'].'-'.$oddevens['even']] = $oddevens['count(*)']; // Arrange the format as odd-even=count
        }
        arsort($top);   // Sort the odd - even combination in reverse order by the count value only
        $oe_text = '';
        foreach($top as $c => $oe)
        {
            $oe_text .= $c.'='.$oe.',';
        }
        $oe_text = substr_replace($oe_text, '|', -1);	    // Replace the ',' with the '|' (pipe)
        unset($top);                                        // Destroy the $top array
        // Step 2, is to find the low number odd - even combinations starting at the pick and pick - 1 (e.g. for Pick6 it would be pick 6)
        // next, look for the largest count of a given odd - even. For example, pick 6 would be 6 odd - 0 even, 0 odd - 6 even, 5 odds - 1 even and 1 odd and 5 even
        // Retrieve all the dates and the draw separation between draws.  Add these results to the string and return
        // For example, 6-0,2021/01/18,5,2021/02/28,10,2021/05/15,5,2021/07/01.25
        // The complete format will be displayed as: 4-2=34,3-3=32,2-4=25,4-2=20,5-1=15,1-5=14,6-0=4,00-6=2|6-0,2021/01/18,5,2021/02/28,10,2021/05/15,5,2021/07/01.25
            $low = 0;
            $draw_dates = array();  // Series of draw dates
            foreach($parity as $count => $oddevens)
            {
                if(($oddevens['odd']==$pick)&&($oddevens['even']==0)||($oddevens['odd']==0)&&($oddevens['even']==$pick))
                {
                    if($low<$oddevens['count(*)']) 
                    {
                        $odd = $oddevens['odd'];
                        $even = $oddevens['even'];
                        array_push($draw_dates, $oddevens['draw_date']);
                        $low=$oddevens['count(*)'];
                    }
                }
            }
        if(!empty($draw_dates))     // Is there dates?
        {
            $oe_text .= $odd.'-'.$even.',';
            $result = $this->parity_dates($draw_dates, $lotto);    
            if(!$result) return FALSE;
        }
        else 
        {
            $oe_text .= "0-0";  // The rare odd / even combination did not happen
            $result = "";
        }
        unset($draw_dates);    
        $oe_text .= $result;    
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
	* @return   array   $result     Array of the odd / even and total counts for the range, FALSE on failure	
	*/
	private function parity_list($rows, $tbl)
	{
		$query = $this->db->query("select odd, even, draw_date, count(*) from (SELECT * FROM 
        `".$tbl."` ORDER BY id DESC LIMIT ".$rows.") sub 
        group by odd, even ORDER BY id ASC;");
        $result = $query->result_array();    
    return $result;
	}
    /** 
	* parity_list. Insert / Update the At a Glance Statistics to the database
	* @param    array   $parity_dates Series of draw dates for this odd / even combination
	* @param	string  $tbl		Actual table name of the lottery (e.g. canada_649)
	* @return   array   $result     Array of the odd / even and total counts for the range, FALSE on failure	
	*/
	private function parity_dates($parity_dates, $tbl) 
	{
		$result = '';
        foreach($parity_dates as $c => $r)
        {
            if(!empty($c))
            {
                $sql = "SELECT * FROM ".$tbl." WHERE draw_date <= ".$parity_dates[$c-1]." AND draw_date => ".$parity_dates[$c];
                $query = $this->db->query("SELECT * FROM ".$tbl." WHERE draw_date <= '".$parity_dates[$c-1]."' AND draw_date >= '".$parity_dates[$c]."'");
                if(!$query) return FALSE;
                $result = $parity_dates[$c-1].','.$query->num_rows().',';
            }
        }
        $last_date = end($parity_dates);  // Return the last date that this odd / even combination occurred to determine how many draws have elapsed since this occurred.
        $query = $this->db->query("SELECT * FROM ".$tbl." WHERE draw_date >= '".$last_date."'");
        if(!$query) return FALSE;
        $result .= $last_date.",".$query->num_rows;
    return $result;
	}

}