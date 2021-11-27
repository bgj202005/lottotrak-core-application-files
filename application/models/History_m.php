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
        if($b_ex) $pick++;
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
                    if($b_ex) // Extra included in the trends
                    {
                        $change = $change+intval($this->trend($draw['extra'], $draws[$count+1]['extra']));
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
        if($b_ex) $pick++;
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
        if($this->top_pick($next))
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
        else $r_text .= '0=0';   // Nothing Here  
    return substr($r_text, 0, -1);	// Return the repeats without an extra ',' Comma
    }

    /**
	 * Returns if the Top Pick has been picked a minimum number of times
	 * 
	 * @param       array       $picks           Array of draws for a given range
	 * @return      boolean		true/false       Returns true if the minimum value (5) is equal or exceeded
	 */
    public function top_pick($picks)
    {
        foreach($picks as $pick => $value)
        {
            if($value>=5) return true;
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
	 * adjacent_history averrages the number of adjacents of each ball position for balls 1 and 2, 2 and 3, 3 and 4, 4 and 5, 5 and 6 for a pick 6 game. The maximum separation is included
	 * 
	 * @param       array       $draws          Array of draws for a given range
     * @param       integer     $pick           Pick Game. Pick 7, Pick 6, Pick 5 
	 * @param 		boolean 	$ex 		    extra draws used
     * @param 		boolean 	$b_ex           Bonus ball used		    
	 * @return      string		$adj_text       Concatenated String. Format: . e.g. Pick 6 average difference, 1=5,2=5,3=2,4=7,5=5|14
	 */
    public function adjacents_history($draws, $pick, $ex = FALSE, $b_ex = FALSE)
    {
        $total = count($draws);
        if($b_ex) $pick++;
        $adajcents = $this->zeroed(new SplFixedArray($pick+1), $pick+1);         // include the zero consecutives
        
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
        $adj_text = '';
        foreach($consecutives as $n => $c)
        {
            $adj_text .= $n.'='.$c.',';
        }
        $adj_text = substr_replace($c_text, '|', -1);	    // Replace the ',' with the '|' (pipe)
        $adj_text .= $lcd;                             // include the last draw date of consecutive occurrence
    return $adj_text;                        
    }
    /**
	 * sums_history tabulates the winning sums over a given range of draws. Only the top 10 Winning sums will be retained. 
	 * 
	 * @param       array       $draws          Array of draws for a given range
     * @param       integer     $pick           Pick Game. Pick 7, Pick 6, Pick 5 
	 * @param 		boolean 	$ex 		    extra draws used
     * @param 		boolean 	$b_ex           Bonus ball used		    
	 * @return      string		$sum_text       Concatenated String. Format: 147=5,209=5,187=4,162=3,109=2|5=17,INCREASE
     * Percentage differences will be calculated for 0-5%, 6-10%, 11-15%, 16-20%, 21-25%, 26-30%, 31-35%, 36-40%, 41-45% and 46-50% 
     * the number of occurences and if the percentage is an INCREASE or DECREASE from the previous total sum. Only the highest occurrence will be retained. 
	 */
    public function sums_history($draws, $pick, $ex = FALSE, $b_ex = FALSE)
    {

    }
    /**
	 * digits_history tabulates the digit sums over a given range of draws. Only the top 5 digit sums will be retained.
	 * 
	 * @param       array       $draws          Array of draws for a given range
     * @param       integer     $pick           Pick Game. Pick 7, Pick 6, Pick 5 
	 * @param 		boolean 	$ex 		    extra draws used
     * @param 		boolean 	$b_ex           Bonus ball used		    
	 * @return      string		$digits_text    Concatenated String. Format: 42=7,33=5,41=4,33=4,54=4|10=19,INCREASE
     * Percentage differences will be calculated for 0-5%, 6-10%, 11-15%, 16-20%, 21-25%, 26-30%, 31-35%, 36-40%, 41-45% and 46-50% 
     * the number of occurences and if the percentage is an INCREASE or DECREASE from the previous digits sum. Only the highest occurrence will be retained. 
	 */
    public function digits_history($draws, $pick, $ex = FALSE, $b_ex = FALSE)
    {

    }
    /**
	 * oddevens_history calculates the odd/even combination that has exceeded the average odd/even for that given range 
	 * 
	 * @param       array       $draws          Array of draws for a given range
     * @param       integer     $pick           Pick Game. Pick 7, Pick 6, Pick 5 
	 * @param 		boolean 	$ex 		    extra draws used
     * @param 		boolean 	$b_ex           Bonus ball used		    
	 * @return      string		$oddevens_text   Concatenated String. Format: 4-3=55,6-0=5,0-6=4
     * the number of occurences must exceed the average for that odd / even combination based from the range to be included
	 */
    public function oddevens_history($draws, $pick, $ex = FALSE, $b_ex = FALSE)
    {

    }
}