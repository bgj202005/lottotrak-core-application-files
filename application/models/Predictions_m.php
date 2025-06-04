<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Predictions_m extends MY_Model
{
	protected $_table_name = 'lottery_predictions';
	protected $_order_by = 'id';
	public $rules = array(
        'ball_predict' => array(
            'field' => 'ball_predict',
            'label' => 'Number of Balls to Predict',
            'rules' => 'trim|required|callback__range_ball_values|callback__validate_picks'
		)
	);

	const DIR = 'combinations';

/** This function returns the total count of the number of possible unique
 * 	combinations there are of N distinct items selected R at a time. The
 * 	sequential order of the items in each group is NOT important.
 * 	Only the collective content matters, regardless of order. 
 *	 Author   : Jay Tanner - 2014
 *   Language : PHP v5.x
 * 	 @param		integer	$N	distinct items (3 - 9 Numbers Drawn)
 *   @param 	integer $R  Number of Predicted Numbers (3 - 50)
 *	 @return	integer	$C	Number of Distinct Combinations
*/
 	public function bcComb_N_R ($N, $R)
	{
	$C = 1;

	for ($i=0;   $i < $N-$R;   $i++)
		{
		$C = bcdiv(bcmul($C, $N-$i), $i+1);
		}
	return $C;
	}
	/**
	 * Returns the Lottery Combination File(s), if does not exist return FALSE
	 * 
	 * @param       integer	$pick_id	Related to the number of picks in a lottery. eg. 3, 4, 5, 6, 7, 8, 9
	 * @return     	object 	$result		Return row, if lottery combination file(s) previously exists for the given lottery, else no record found and return false			
	 */
	public function lottery_combination_files($R)
	{

		// Fetch combination files based on pick_id
    	return $this->db->where('R', $R)
                    ->get('lottery_combination_files')
                    ->result();
	}

	/**
	 * Returns the Lottery Combination File(s), if does not exist return FALSE
	 * 
	 * @param       none
	 * @return     	object 	$result		Return row, if lottery combination file(s) previously exists for the given lottery, else no record found and return false			
	 */
	public function all_combination_files()
	{
		$sql = "SELECT * FROM `lottery_combination_files`";
		$result = $this->db->query($sql);
			
		if (empty($result->row())) return FALSE;
	return $result->result_object;
	}

	/**
	 * Returns a Lottery Combination Record (only one), if does not exist return FALSE
	 * 
	 * @param       string	$name		The name of the file_name of the combination file without the .txt extention
	 * @return     	object 	$result		Return row, if lottery combination file(s) previously exists for the given lottery, else no record found and return false			
	 */
	public function lottery_combination_record($name)
	{
			$sql = "SELECT * FROM `lottery_combination_files` WHERE `file_name`=".$name." LIMIT 1";
			$result = $this->db->query($sql);
			
			if (empty($result->row())) return FALSE;
	return $result->result_object;
	}
	/** 
	* Insert Lottery Combination File data of current lottery
	* 
	* @param 	array	$data		key / value pairs of new Lottery Combination File to be inserted / updated
	* @return   none	
	*/
	public function lottery_combo_save($data)
	{
		// Ensure the data includes pick_id instead of lottery_id
    	$combo_data = [
			'file_name' => $data['file_name'],
			'N' => $data['N'], // Number of predictions
			'R' => $data['R'], // Pick game (e.g., 3, 4, 5, 6, etc.)
			'CCCC' => $data['CCCC'], // Calculated combinations
    	];
		return $this->db->insert('lottery_combination_files', $combo_data);
	}

	/**
	 * Returns the complete full path of the combination file including .txt file extension
	 * 
	 * @param       string	$name			Filename of the combination file without the .txt extention
	 * @return     	string	$full_path		The complete path of the filename. Different depending on Windows or Linux machines
	 */
	public function full_path($name)
	{
		if(DIRECTORY_SEPARATOR=='\\')
		{
		// Windows	
			$full_path = 'd:\\wamp64\\www\\lottotrak\\'.self::DIR.'\\'.$name.'.txt';
		}
		else 
		// Linux 
		{
			// This is a Linux server, so the path must be changed to reflect the server
			$full_path = '/home/metad231/lottotrak.com/'.self::DIR.'/'.$name.'.txt';
		}
		if(!file_exists($full_path))
		{
			// If the file does not exist, create it
			$fp = fopen($full_path, 'w');
			fclose($fp);
		}
		else //This is a Linux server, so the path must be changed to reflect the server
		{
			$full_path = self::DIR.DIRECTORY_SEPARATOR.$name.'.txt';
		}
	return $full_path; // Full Path of Filename
	}

	/**
	 * Removes the record in the database from the filename (excluding the .txt extension)
	 * 
	 * @param       string	$name			The name of the file_name of the combination file without the .txt extention
	 * @return     	boolean	TRUE/FALSE		Returns TRUE on successful removal of the record, FALSE if the record could not be deleted
	 */
	public function delete_combination_record($name)
	{
		$this -> db -> where('file_name', $name);
    return $this -> db -> delete('lottery_combination_files');
	}
	/**
	 * Returns a Lottery Combination Record (only one), if does not exist return FALSE
	 * 
	 * @param       string	$name			The name of the file_name of the combination file without the .txt extention
	 * @return     	boolean	TRUE/FALSE		Returns TRUE on successful removal of the file in the /combinations/ directory, FALSE if the file could not be deleted
	 */
	public function delete_combination_file($name)
	{
		$full_path = $this->full_path($name);

	return unlink($full_path); // Remove File, TRUE successful, FALSE on error
	}
	/** Iterates the number of predictions and returns them in an array to
	 * be used in the Math Combinatorics combination methods
 	* @param	integer	$Pr		Predicted Numbers (e.g. 1 to 15)
 	* @return	array	$combs	Array of the number of predicted (1,2,3,4,5,6,7,8,9...15)
	*/
 	public function wheeled($Pr)
	{
		$c = 1;
		$combs = array();
		for ($i=0;   $i < $Pr;   $i++)
			{
				$combs[] = $c; // Add next predicted element onto the array
				$c++;
			}
	return $combs;
	}
	/** 
	* Sort the array into a text line and save to the provided text file after complete
	* 
	* @param 	string 	$name				Filename without the .txt extension
	* @param 	array	$combs_array		Array of Combinations in the form of [0] => [1] = 1, [2] = 2, [3] = 3, [4] = 4, [5] = 5, [6] = 6
	* @return   boolean $success	TRUE / FALSE, TRUE = Saved to text file successfully, FALSE = Something when wrong	
	*/
	public function text_combs_save($name, $combs_array)
	{
		$success = TRUE;
		$returned_path = $this->full_path($name); 
		$fp = fopen($returned_path, 'a');
		if($fp)	
		{
			foreach($combs_array as $combo => $key)
			{
				$str = implode(' ', $key);
				fwrite($fp, $str);
				fwrite($fp, "\n"); // NB double quotes must be used here
			}
		}
		else	// Can't Open the File?
		{
			$success = FALSE;
		}
		fclose($fp);
	return $success;
	}

	/** 
	* Sort the array into a text line and save to the provided text file after complete
	* 
	* @param 	string	$name			Filename without the .txt extension
	* @param	integer	$combs_count	Number of actual combinations in the array
	* @return   boolean $saved			TRUE / FALSE, True $combos >= $combos_count, Everything else is FALSE
	*/
	public function combs_already($name, $combs_count)
	{
		$saved = FALSE;
		$returned_path = $this->full_path($name); 
		$combs = 0;
		$fp = fopen($returned_path, 'r');
		while (!feof($fp)) 
		{
			$combs++;
			if($combs>=$combs_count)
			{
				$saved = TRUE;
				break; // Abruptly leave the loop
			}
			if(!fgets($fp)) break; // No Return of anything, indicated no combinations have been previously saved.
		}
		fclose($fp);	
		
	return $saved; // Returns TRUE or False based on the actual count of the combinations in the text file.
	}

	/**
	 * Returns the complete list of combinations based on the number of combinations
	 * 
	 * @param       string	$name			The name of the file_name of the combination file without the .txt extention
	 * @param 		integer	$pick			Number of balls picked
	 * @param 		integer $i				interval in multiples of 200
	 * @return     	object	$draws			Drawn numbers placed from the text file into the array and typecast to an object
	 */
	public function load_draws($name, $pick, $i)
	{
		$fp = fopen($this->predictions_m->full_path($name), "r");
		$combotext = "";
		$draws = [];
		if($fp)
		{
			while(!feof($fp)&&($i))
			{
				$combotext = fgets($fp);
				if(!empty($combotext))	// Check for blank lines near the end of the file //
				{
					$drawing = explode(' ', $combotext);
					switch($pick)
					{
						case 3:
							array_push($draws, (object) [
							'ball1'	=> $drawing[0],
							'ball2'	=> $drawing[1],
							'ball3'	=> $drawing[2],
							]);
							break;
						case 4:
							array_push($draws, (object) [
							'ball1'	=> $drawing[0],
							'ball2'	=> $drawing[1],
							'ball3'	=> $drawing[2],
							'ball4'	=> $drawing[3],
							]);
							break;
						case 5:
							array_push($draws, (object) [
							'ball1'	=> $drawing[0],
							'ball2'	=> $drawing[1],
							'ball3'	=> $drawing[2],
							'ball4'	=> $drawing[3],
							'ball5'	=> $drawing[4],
							]);
							break;
						case 6:
							array_push($draws, (object) [
							'ball1'	=> $drawing[0],
							'ball2'	=> $drawing[1],
							'ball3'	=> $drawing[2],
							'ball4'	=> $drawing[3],
							'ball5'	=> $drawing[4],
							'ball6'	=> $drawing[5],
							]);
							break;
						case 7:
							array_push($draws, (object) [
							'ball1'	=> $drawing[0],
							'ball2'	=> $drawing[1],
							'ball3'	=> $drawing[2],
							'ball4'	=> $drawing[3],
							'ball5'	=> $drawing[4],
							'ball6'	=> $drawing[5],
							'ball7'	=> $drawing[6],
							]);
							break;
						case 8:
							array_push($draws, (object) [
							'ball1'	=> $drawing[0],
							'ball2'	=> $drawing[1],
							'ball3'	=> $drawing[2],
							'ball4'	=> $drawing[3],
							'ball5'	=> $drawing[4],
							'ball6'	=> $drawing[5],
							'ball7'	=> $drawing[6],
							'ball8'	=> $drawing[7],
							]);
							break;
						default:
							array_push($draws, (object) [
							'ball1'	=> $drawing[0],
							'ball2'	=> $drawing[1],
							'ball3'	=> $drawing[2],
							'ball4'	=> $drawing[3],
							'ball5'	=> $drawing[4],
							'ball6'	=> $drawing[5],
							'ball7'	=> $drawing[6],
							'ball8'	=> $drawing[7],
							'ball9'	=> $drawing[8],
							]);
					}
				}
			$i--;	// Load only what is required to be viewed
			}
		}
		else return FALSE;
	return $draws;
	}
	/** Checks if a combination file has been generated for the given filename.
	 * This method verifies the existence of a combination file in the specified directory.
	 * It returns `TRUE` if the file exists, otherwise `FALSE`.
	 * @param string $filename The name of the combination file without the `.txt` extension.
	 * @return bool  Returns `TRUE` if the combination file exists, otherwise `FALSE`.
	 */
	public function is_combination_generated($filename)
	{
		// Define the path to the combination files directory
		$file_path = $this->full_path($filename);
		// Check if the file exists
		// Check if the file exists and is not empty
		if (file_exists($file_path) && filesize($file_path) > 0) {
			return TRUE; // File exists and contains data
		}
    return FALSE; // File does not exist or is empty
	}
	/**
	 * Calculate the number of tickets that match the required number of balls for a prize tier.
	 *
	 * This method compares each combination in the file with the winning numbers
	 * for a specific prize tier and counts how many tickets meet the criteria.
	 *
	 * @param array $combinations Array of ticket combinations (each combination is a string of numbers).
	 * @param array $winning_numbers Array of winning numbers for the prize tier.
	 * @return int Number of matching tickets.
	 */
	public function calculate_matching_tickets($combinations, $required_matches) {
		$matching_tickets = 0;
		 // Define the range of winning numbers (e.g., 1 to the maximum number of balls)
		$winning_numbers = range(1, $required_matches);
		foreach ($combinations as $combination) {
			// Convert the combination string into an array of numbers
			$numbers = explode(' ', trim($combination));

			// Count how many numbers match the winning numbers
			$matches = count(array_intersect($numbers, $winning_numbers));

			// If the number of matches is equal to or greater than the required matches, count it
			if ($matches >= $required_matches) {
				$matching_tickets++;
			}
		}
		return $matching_tickets;
	}
	/**
	 * Returns the Lottery Prize Profile as an associative array.
	 * This method retrieves the prize data for a given lottery ID and returns it
	 * as an associative array. If no data exists, it returns FALSE.
	 *
	 * @param int $lotto_id The ID of the lottery.
	 * @return array|false The prize data as an associative array, or FALSE if no data exists.
	 */
	public function prizes_data_array($lotto_id)
	{
		$sql = "SELECT * FROM `lottery_prize_profiles` WHERE `lottery_id` = " . $lotto_id . " LIMIT 1";
		$result = $this->db->query($sql);

		if (empty($result->row())) {
			return FALSE; // Return FALSE if no data exists
		}
		return $result->row_array(); // Return the result as an associative array
	}
	/**
     * Retrieves the list of all countries.
     * @param $lottery_id	
     * @return array An array of country objects with `id` and `name` fields.
     */
    public function get_countries($lottery_id)
    {
        return $this->db->select('lottery_country_id')->from('lottery_profiles')->where('lottery_id', $lottery_id)->get()->result();
    }
	/**
     * Retrieves the list of provinces/states for a specific country.
     *
     * @param int $country_id The ID of the country.
     * @return array An array of province/state objects with `id` and `name` fields.
     */
    public function get_prov_states($country_id)
    {
        return $this->db->select('lottery_state_prov')->from('lottery_profiles')->where('country_id', $country_id)->get()->result();
    }
	/**
     * Retrieves the list of lottery games for a specific country and province/state.
     *
     * @param int $country_id The ID of the country.
     * @param string $province_id The ID of the province/state or "ALL" for country-wide lotteries.
     * @return array An array of lottery game objects with `id` and `name` fields.
     */
    public function get_lottery_games($country_id, $province_id)
    {
        $this->db->select('id, name')->from('lottery_games')->where('country_id', $country_id);
        if ($province_id !== 'ALL') {
            $this->db->where('province_id', $province_id);
        }
        return $this->db->get()->result();
    }
	/**
     * Retrieves the list of wheeling tables for a specific lottery game.
     *
     * @param int $lottery_id The ID of the lottery game.
     * @return array Field file_name wheeling (coverage) table to be returned from the lottery_id, to retrieve the text file.
     */
    public function get_wheeling_tables($lottery_id)
    {
        return $this->db->select('file_name')->from('lottery_combination_files')->where('lottery_id', $lottery_id)->get()->result();
    }
	/**
	 * Checks if there is at least one generated file for the given number of balls drawn (R).
	 *
	 * @param int $balls_drawn The number of balls drawn (R) for the lottery.
	 * @return bool TRUE if at least one file exists, FALSE otherwise.
	 */
	public function has_generated_file($balls_drawn)
	{
		$result = $this->db->where('R', $balls_drawn)
						->count_all_results('lottery_combination_files');
		return $result > 0;
	}
	/**
     * Get the country code for a lottery
     * @param int $id Lottery ID
     * @return string|null Country code
     */
    public function get_lottery_country($lottery_id)
    {
        $this->db->select('lottery_country_id');
        $this->db->from('lottery_profiles');
        $this->db->where('id', $lottery_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->lottery_country_id;
        }
        return null; // Return null if no record is found
    }
	/**
     * Get the state/province code for a lottery
     * @param int $lottery_id Lottery ID
     * @return string|null State/Province code or NULL if blank
     */
    public function get_lottery_state_prov($lottery_id)
    {
        $this->db->select('lottery_state_prov');
        $this->db->from('lottery_profiles');
        $this->db->where('id', $lottery_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $state_prov = $query->row()->lottery_state_prov;
            return !empty($state_prov) ? $state_prov : null; // Return NULL if blank
        }
        return null; // Return null if no record is found
    }
	/**
     * Get combination files for a lottery
     * @param int $lottery_id
     * @return array
     */
    public function get_combination_files($lottery_id)
    {
        // Get the balls_drawn value for the lottery
        $this->db->select('balls_drawn');
        $this->db->from('lottery_profiles');
        $this->db->where('id', $lottery_id);
        $lottery = $this->db->get()->row();
        if (!$lottery) {
            return []; // Return an empty array if the lottery is not found
        }
        $balls_drawn = $lottery->balls_drawn;
        // Fetch combination files that match the balls_drawn value
        $this->db->select('file_name, N, CCCC');
        $this->db->from('lottery_combination_files');
        $this->db->where('R', $balls_drawn); // Match the balls_drawn value
        $query = $this->db->get();
        return $query->result_array(); // Return the result as an array
    }
	/**
     * Retrieves the H-W-C (High, Winning, Cold) range, extra draws, and extra included settings for a lottery.
     *
     * @param int $lottery_id The ID of the lottery.
     * @return array An associative array containing the range, extra_included, and extra_draws values.
     */
    public function get_h_w_c($lottery_id)
    {
        $this->db->select('range, extra_included, extra_draws');
        $this->db->from('lottery_h_w_c');
        $this->db->where('lottery_id', $lottery_id);
        return $this->db->get()->row_array();
    }
    /**
     * Retrieves the Followers range, extra draws, and extra included settings for a lottery.
     *
     * @param int $lottery_id The ID of the lottery.
     * @return array An associative array containing range, lottery_followers, wins, positions, draw_id, extra_included, extra_draws.
     */
    public function get_followers($lottery_id)
    {
        $this->db->select('range, lottery_followers, wins, positions, draw_id, extra_included, extra_draws');
        $this->db->from('lottery_followers');
        $this->db->where('lottery_id', $lottery_id);
        return $this->db->get()->row_array();
    }
    /**
     * Retrieves the Friends range, extra draws, and extra included settings for a lottery.
     *
     * @param int $lottery_id The ID of the lottery.
     * @return array An associative array containing the range, extra_included, and extra_draws values.
     */
    public function get_friends($lottery_id)
    {
        $this->db->select('range, extra_included, extra_draws');
        $this->db->from('lottery_friends');
        $this->db->where('lottery_id', $lottery_id);
        return $this->db->get()->row_array();
    }
	/**
	 * Retrieves and parses the h_w_c_range field for a lottery.
	 * Returns an associative array: [ 'h-w-c' => total, ... ]
	 *
	 * @param int 		$lottery_id The ID of the lottery.
	 * @return array 	 $hwc Associative array of h-w-c => total
	 */
	public function get_h_w_c_range($lottery_id)
	{
		$this->db->select('h_w_c_range');
		$this->db->from('lottery_h_w_c_stats');
		$this->db->where('lottery_id', $lottery_id);
		$row = $this->db->get()->row();
		$result = [];	// clear array
		if ($row && !empty($row->h_w_c_range)) {
			$hwc = [];
			$items = explode(',', $row->h_w_c_range);
			foreach ($items as $item) {
				$parts = explode('=', $item);
				if (count($parts) == 2) {
					$label = trim($parts[0]);
					$total = (int)trim($parts[1]);
					if ($total > 0) { // Only include if total > 0
						$hwc[$label] = $total;
					}
				}
			}
			// Sort by total descending
			arsort($hwc);
			// Build dropdown array: 1 => '2-2-2 (16)', 2 => '1-3-2 (10)', ...
			$i = 1;
			foreach ($hwc as $label => $total) {
				$result[$i++] = $label . ' (' . $total . ')';
			}
		}
    return $result;
	}
	/**
	 * Returns an associative array of actual ball numbers (including extra as +N) 
	 * mapped to their total points, sorted descending by points.
	 * Any ball with 0 points is excluded.
	 *
	 * @param array $last_drawn The last_drawn array from the lottery object.
	 * @param int $balls_drawn  The number of main balls drawn.
	 * @return array Sorted associative array: [ 'ball_number' => points, ... ]
	 */
	public function get_sorted_ball_points($last_drawn, $balls_drawn)
	{
		$ball_points = [];
		// Main balls
		for ($i = 1; $i <= $balls_drawn; $i++) {
			$points = 0;
			if (isset($last_drawn['ball'.$i.'_win'])) {
				foreach ($last_drawn['ball'.$i.'_win'] as $k => $v) {
					if (strpos($k, '_points') !== false) $points += intval($v);
				}
				$ball_number = $last_drawn['ball'.$i];
				if ($points > 0) {
					$ball_points[$ball_number] = $points;
				}
			}
		}
		// Extra ball (if exists)
		if (isset($last_drawn['extra_win']) && isset($last_drawn['extra'])) {
			$points = 0;
			foreach ($last_drawn['extra_win'] as $k => $v) {
				if (strpos($k, '_points') !== false) $points += intval($v);
			}
			if ($points > 0) {
				$ball_points['+'.$last_drawn['extra']] = $points;
			}
		}
		// Sort by points descending
		arsort($ball_points);
		// Build dropdown array: 0 => '7 (115)', 1 => '34 (83)', ...
		$result = [];
		foreach ($ball_points as $number => $points) {
			$result[] = $number . ' (' . $points . ')';
		}
    return $result;
	}
	/**
	 * Returns an associative array of actual ball numbers (including extra as +N)
	 * mapped to their total position points, sorted descending by points.
	 * Any position with 0 points is excluded.
	 *
	 * @param array $last_drawn The last_drawn array from the lottery object.
	 * @param int $balls_drawn  The number of main balls drawn.
	 * @return array Sorted associative array: [ 'ball_number' => points, ... ]
	 */
	public function get_sorted_position_points($last_drawn, $balls_drawn)
	{
			$position_points = [];
		// Loop through each position
		for ($i = 1; $i <= $balls_drawn; $i++) {
			$points = 0;
			if (isset($last_drawn['position'.$i.'_win'])) {
				foreach ($last_drawn['position'.$i.'_win'] as $k => $v) {
					if (strpos($k, '_points') !== false) $points += intval($v);
				}
				$position_number = $i;
				if ($points > 0) {
					$position_points[$position_number] = $points;
				}
			}
		}
		// Sort by points descending
		arsort($position_points);
		// Build dropdown array: 0 => '1 (115)', 1 => '2 (83)', ...
		$result = [];
		foreach ($position_points as $position => $points) {
			$result[] = $position . ' (' . $points . ')';
		}
		return $result;
	}
	/**
	 * Retrieves lottery highlights for a given lottery_id.
	 * Returns an associative array with keys: trends, repeats, consecutives, adjacents, winning_sums, winning_digits, number_range, parity.
	 *
	 * @param int $lottery_id The ID of the lottery.
	 * @return array Associative array of highlights, or empty array if not found.
	 */
	public function get_lottery_highlights($lottery_id)
	{
		$this->db->select('range, trends, repeats, consecutives, adjacents, winning_sums, winning_digits, number_range, parity');
		$this->db->from('lottery_highlights');
		$this->db->where('lottery_id', $lottery_id);
		$row = $this->db->get()->row_array();

		if (!$row) {
			return [];
		}
	return $row;
	}
	/**
	 * Parses a trends string and returns an associative array for dropdown:
	 * 0 => 'ALL', 1 => 'UP (N)', 2 => 'DOWN (N)'
	 *
	 * @param string $trend_string The trends string, e.g. "up=2,down=6,2024-11-29,down,6,down"
	 * @return array Dropdown array for trends.
	 */
	public function get_trends($trend_string)
	{
		$up = 0;
		$down = 0;
		$parts = explode(',', $trend_string);
		foreach ($parts as $part) {
			if (strpos($part, 'up=') === 0) {
				$up = (int)substr($part, 3);
			}
			if (strpos($part, 'down=') === 0) {
				$down = (int)substr($part, 5);
			}
		}
		$trends = [
			0 => 'ALL',
			1 => 'UP (' . $up . ')',
			2 => 'DOWN (' . $down . ')'
		];
		return $trends;
	}
	/**
	 * Parses the winning_digits string and returns an array for the top 10 digit sums.
	 * Each entry is [digit_sum => total], in the order provided.
	 *
	 * @param string $winning_digits The string, e.g. "50=10,46=10,39=9,43=6,47=6,52=5,56=5,41=5,37=4,36=3|15=10,INCREASE"
	 * @return array Array for dropdown: [digit_sum => total, ...]
	 */
	public function get_digit_sums($digits)
	{
		$result = [0 => 'ALL'];
		// Split by '|', take the first part
		$parts = explode('|', $digits);
		$main_part = isset($parts[0]) ? $parts[0] : '';
		$digit_sums_arr = [];
		if ($main_part) {
			$pairs = explode(',', $main_part);
			foreach ($pairs as $pair) {
				$kv = explode('=', $pair);
				if (count($kv) == 2) {
					$digit_sum = trim($kv[0]);
					$total = (int)trim($kv[1]);
					if ($total > 0) {
						$digit_sums_arr[$digit_sum] = $total;
					}
				}
			}
			// Sort by total descending, then by digit sum descending
			arsort($digit_sums_arr);
			// Build dropdown array: 1 => "50 (10)", 2 => "46 (10)", ...
			foreach ($digit_sums_arr as $digit_sum => $total) {
				$result[] = $digit_sum . ' (' . $total . ')';
			}
		}
		return $result;
	}
	/**
	 * Parses the winning_sum string and returns an array for the dropdown.
	 * Each entry is [sum => total], in the order provided.
	 * Adds "ALL" (value: 0) as the top option.
	 *
	 * @param string $winning_sums The string, e.g. "163=4,147=3,178=3,173=3,190=3,149=3,151=3,221=2,200=2,153=2|15=13,INCREASE"
	 * @return array Array for dropdown: [0 => 'ALL', sum => total, ...]
	 */
	public function get_sums($winning_sums)
	{
		// Split by '|', take the first part
		$parts = explode('|', $winning_sums);
		$main_part = isset($parts[0]) ? $parts[0] : '';
		$result = [0 => 'ALL'];
		if ($winning_sums) {
			// Split by '|' and use the first part
			$parts = explode('|', $winning_sums);
			$main_part = isset($parts[0]) ? $parts[0] : '';
			$sums_arr = [];
			if ($main_part) {
				$pairs = explode(',', $main_part);
				foreach ($pairs as $pair) {
					$kv = explode('=', $pair);
					if (count($kv) == 2) {
						$sum = trim($kv[0]);
						$count = (int)trim($kv[1]);
						if ($count > 0) {
							$sums_arr[$sum] = $count;
						}
					}
				}
				// Sort by count descending, then by sum descending
				arsort($sums_arr);
				// Build dropdown array: 1 => "145 (3)", 2 => "156 (3)", ...
				foreach ($sums_arr as $sum => $count) {
					$result[] = $sum . ' (' . $count . ')';
				}
			}
		}
		return $result;
	}
	/**
	 * Parses the repeaters string and returns an associative array for the dropdown.
	 * Each entry is [repeater_count => total], in the order provided, with "ALL" (value: 0) as the top option.
	 * Any repeater with a total of 0 is removed.
	 *
	 * @param string $repeaters The string, e.g. "0=17,1=44,2=30,3=9,4=0,5=0,6=0,7=0|27=7,46=5,30=5,28=5,33=5"
	 * @return array Array for dropdown: [0 => 'ALL', repeater_count => total, ...]
	 */
	public function get_repeaters($repeaters)
	{
		// Split by '|', take the first part
		$result = [0 => 'ALL'];
		// Split by '|', take the first part
		$parts = explode('|', $repeaters);
		$main_part = isset($parts[0]) ? $parts[0] : '';
		$repeaters_arr = [];
		if ($main_part) {
			$pairs = explode(',', $main_part);
			foreach ($pairs as $pair) {
				$kv = explode('=', $pair);
				if (count($kv) == 2) {
					$repeater = trim($kv[0]);
					$total = (int)trim($kv[1]);
					if ($total > 0) {
						$repeaters_arr[$repeater] = $total;
					}
				}
			}
			// Sort by total descending, then by repeater descending
			arsort($repeaters_arr);
			// Build dropdown array: 1 => "1 (44)", 2 => "2 (30)", ...
			foreach ($repeaters_arr as $repeater => $total) {
				$result[] = $repeater . ' (' . $total . ')';
			}
		}
		return $result;
	}
	/**
	 * Parses the consecutives string and returns an associative array for the dropdown.
	 * Each entry is [consecutive_count => total], in the order provided, with "ALL" (value: 0) as the top option.
	 * Any consecutive with a total of 0 is removed.
	 *
	 * @param string $c The string, e.g. "0=23,1=43,2=25,3=7,4=2,5=0,6=0,7=0|2=2025-01-31"
	 * @return array $consecutives for dropdown: [0 => 'ALL', consecutive_count => total, ...]
	 */
	public function get_consecutives($c)
	{
		// Split by '|', take the first part
		$parts = explode('|', $c);
		$main_part = isset($parts[0]) ? $parts[0] : '';
		$consecutives_arr = [];
		if ($main_part) {
			$pairs = explode(',', $main_part);
			foreach ($pairs as $pair) {
				$kv = explode('=', $pair);
				if (count($kv) == 2) {
					$count = trim($kv[0]);
					$total = (int)trim($kv[1]);
					if ($total > 0) {
						$consecutives_arr[$count] = $total;
					}
				}
			}
		}
		// Sort by total descending, then by count descending
		arsort($consecutives_arr);
		// Build dropdown array: 0 => "ALL", 1 => "1 (43)", 2 => "2 (25)", ...
		$result = [0 => 'ALL'];
		foreach ($consecutives_arr as $count => $total) {
			$result[] = $count . ' (' . $total . ')';
		}
    return $result;
	}
	/**
	 * Parses the parity string and returns an associative array for the dropdown.
	 * Each entry is [odd-even => total], in the order provided, with "ALL" (value: 0) as the top option.
	 * Any parity with a total of 0 is removed.
	 *
	 * @param string $parity The string, e.g. "4-3=29,5-2=28,3-4=25,2-5=11,1-6=4,6-1=3|0-0"
	 * @return array Array for dropdown: [0 => 'ALL', '4-3' => 29, ...]
	 */
	public function get_parity($p)
	{
		// Split by '|', take the first part
		$parts = explode('|', $p);
		$main_part = isset($parts[0]) ? $parts[0] : '';
		$parity_arr = [];
		if ($main_part) {
			$pairs = explode(',', $main_part);
			foreach ($pairs as $pair) {
				$kv = explode('=', $pair);
				if (count($kv) == 2) {
					$odd_even = trim($kv[0]);
					$total = (int)trim($kv[1]);
					if ($total > 0) {
						$parity_arr[$odd_even] = $total;
					}
				}
			}
		}
		// Sort by total descending, then by odd-even descending
		arsort($parity_arr);
		// Build dropdown array: 0 => "ALL", 1 => "3 - 3 (28)", ...
		$result = [0 => 'ALL'];
		foreach ($parity_arr as $odd_even => $total) {
			$result[] = str_replace('-', ' / ', $odd_even) . ' (' . $total . ')';
		}
		return $result;
	}
	/**
	 * Retrieves the count of repeat_decade values for a given table and range.
	 * Returns an associative array: [decade => count, ...], sorted by count descending.
	 * If no results, returns ['error' => 'No data found.']
	 *
	 * @param string $tbl_name The name of the lottery table.
	 * @param int $range The number of draws to consider (e.g., 100).
	 * @return array Associative array for dropdown: [decade => count, ...] or ['error' => 'No data found.']
	 */
	public function get_decade($tbl_name, $range)
	{
		// Query the latest $range draws for repeat_decade
		$this->db->select('repeat_decade');
		$this->db->from($tbl_name);
		$this->db->order_by('draw_date', 'DESC');
		$this->db->limit($range);
		$query = $this->db->get();
		if (!$query || $query->num_rows() == 0) {
			return NULL; // No data found
		}
		// Count occurrences of each decade
		$decade_counts = [];
		foreach ($query->result() as $row) {
			$decade = (int)$row->repeat_decade;
			if ($decade >= 0) {
				if (!isset($decade_counts[$decade])) {
					$decade_counts[$decade] = 1;
				} else {
					$decade_counts[$decade]++;
				}
			}
		}
		if (empty($decade_counts)) {
			return NULL; // No data found
		}
		// Sort by count descending
		arsort($decade_counts);
		// Build dropdown array: 0 => 'ALL', 1 => '3 (12)', 2 => '4 (10)', ...
		$result = [0 => 'ALL'];
		foreach ($decade_counts as $decade => $count) {
			$result[] = $decade . ' (' . $count . ')';
		}
    return $result;
	}
	/**
	 * Retrieves the count of last digits for a given table and range.
	 * Returns an associative array: [last_digit => count, ...], sorted by count descending.
	 * If no results, returns NULL.
	 *
	 * @param string $tbl_name The name of the lottery table.
	 * @param int $range The number of draws to consider (e.g., 100).
	 * @return array|null Associative array for dropdown: [last_digit => count, ...] or NULL if no data found.
	 */
	public function get_last($tbl_name, $range)
	{
		// Query the latest $range draws for repeat_last
		$this->db->select('repeat_last');
		$this->db->from($tbl_name);
		$this->db->order_by('draw_date', 'DESC');
		$this->db->limit($range);
		$query = $this->db->get();
		if (!$query || $query->num_rows() == 0) {
			return NULL; // No data found
		}
		// Count occurrences of each last digit
		$last_counts = [];
		foreach ($query->result() as $row) {
			$last_digit = (int)$row->repeat_last;
			if ($last_digit >= 0) {
				if (!isset($last_counts[$last_digit])) {
					$last_counts[$last_digit] = 1;
				} else {
					$last_counts[$last_digit]++;
				}
			}
		}
		if (empty($last_counts)) {
			return NULL; // No data found
		}
		// Sort by count descending
		arsort($last_counts);
		// Build dropdown array: 0 => 'ALL', 1 => '3 (12)', 2 => '7 (10)', ...
		$result = [0 => 'ALL'];
		foreach ($last_counts as $digit => $count) {
			$result[] = $digit . ' (' . $count . ')';
		}
		return $result;
	}
	/**
	 * Parses the number_range string from the highlights table and returns an array of the top 5 ranges.
	 * Each entry is [range => total], in descending order by total.
	 * Adds "ALL" as the first option in the array.
	 *
	 * @param string $number_range The string, e.g. "46=8,38=7,32=7,33=7,42=7"
	 * @return array Array for dropdown: [0 => 'ALL', range => total, ...] (top 5 only, descending)
	 */
	public function get_range($number_range)
	{
	$ranges = [];
    if ($number_range) {
        $pairs = explode(',', $number_range);
        foreach ($pairs as $pair) {
            $kv = explode('=', $pair);
            if (count($kv) == 2) {
                $range = trim($kv[0]);
                $total = (int)trim($kv[1]);
                if ($total > 0) {
                    $ranges[$range] = $total;
                }
            }
        }
    }
    // Sort by total descending
    arsort($ranges);
    // Limit to top 5
    $ranges = array_slice($ranges, 0, 5, true);
    // Build dropdown array: 0 => 'ALL', 1 => '46 (8)', ...
    $result = [0 => 'ALL'];
    foreach ($ranges as $range => $total) {
        $result[] = $range . ' (' . $total . ')';
    }
    return $result;
	}
	/**
	 * Parses the adjacents string and returns an associative array for the dropdown.
	 * Each entry is [adjacent_number => total], with "ALL" as the top option.
	 * The description for each is "Between Ball X and Ball Y", e.g. 1 => "Between Ball 1 and Ball 2 (7)".
	 *
	 * @param string $adjacents The string, e.g. "1=6,2=7,3=7,4=6,5=6,6=7|4=27"
	 * @return array Array for dropdown: [0 => 'ALL', 1 => 'Between Ball 1 and Ball 2 (6)', ...]
	 */
	public function get_adjacents($adjacents)
	{
		// Split by '|', take the first part
		$parts = explode('|', $adjacents);
		$main_part = isset($parts[0]) ? $parts[0] : '';
		$adjacents_arr = [0 => 'ALL'];
		if ($main_part) {
			$pairs = explode(',', $main_part);
			foreach ($pairs as $pair) {
				$kv = explode('=', $pair);
				if (count($kv) == 2) {
					$adj_num = (int)trim($kv[0]);
					$total = (int)trim($kv[1]);
					if ($total > 0) {
						$desc = "Ball {$adj_num} & Ball " . ($adj_num + 1) . " ({$total})";
						$adjacents_arr[$adj_num] = $desc;
					}
				}
			}
		}
	return $adjacents_arr;
	}
	/**
	 * Generate a set of numbers using the H-W-C (Hot-Warm-Cold) method for a given lottery.
	 *
	 * @param int    $lottery_id         The lottery ID.
	 * @param int    $combination_size   The total number of numbers to select.
	 * @param string $h_w_c			     The HWC group string (e.g., "3-3-3 (17)").
	 * @return string                    Comma-separated string of selected numbers.
	 */
	public function hwc_only($lottery_id, $combination_size, $h_w_c)
	{
		// 1. Parse H-W-C group (e.g., "3-3-3 (17)")
		preg_match('/(\d+)-(\d+)-(\d+)/', $h_w_c, $matches);
		$h = (int)$matches[1];
		$w = (int)$matches[2];
		$c = (int)$matches[3];
		// 2. Calculate scaled totals for combination size
		$total = $h + $w + $c;
		$h_total = round(($h / $total) * $combination_size);
		$w_total = round(($w / $total) * $combination_size);
		$c_total = $combination_size - $h_total - $w_total; // Ensure total matches
		// 3. Get HWC data from DB
		$hwc = $this->statistics_m->h_w_c_exists($lottery_id);
		$position_row = $this->statistics_m->hwc_history_exists($lottery_id);
		if (!$hwc) {
			return show_error('Hots Warms and Colds data not found for this lottery.');
		} elseif(!$position_row || empty($position_row['position'])) {
			return show_error('Position data not found for this lottery.');
		}
		// 4. Parse numbers for each group (discard counts, keep order)
		$hots = array_map('intval', array_map(function($v){ return explode('=', $v)[0]; }, explode(',', $hwc['hots'])));
		$warms = array_map('intval', array_map(function($v){ return explode('=', $v)[0]; }, explode(',', $hwc['warms'])));
		$colds = array_map('intval', array_map(function($v){ return explode('=', $v)[0]; }, explode(',', $hwc['colds'])));
		// 5. Parse positions for each group
		$parts = explode('|', $position_row['position']);
		$h_positions = $this->parse_position_part($parts[0]); // [position => count]
		$w_positions = $this->parse_position_part($parts[1]);
		$c_positions = $this->parse_position_part($parts[2]);
		// 6. Select numbers for each group by top position counts
		$selected = [];
		$selected = array_merge($selected, $this->select_by_position_index($h_positions, $hots, $h_total));
		$selected = array_merge($selected, $this->select_by_position_index($w_positions, $warms, $w_total));
		$selected = array_merge($selected, $this->select_by_position_index($c_positions, $colds, $c_total));

	return implode(',', $selected);
	}
	/**
	 * Parses a position part string (e.g., "H>0=21,1=16,...") into an associative array.
	 * The returned array maps position indices to their counts.
	 * Example: [0 => 21, 1 => 16, ...]
	 *
	 * @param string $str The position part string to parse.
	 * @return array Associative array of position => count.
	 */
	private function parse_position_part($str) {
		$str = preg_replace('/^[HWC]>/', '', $str);
		$pairs = explode(',', $str);
		$arr = [];
		foreach ($pairs as $pair) {
			$kv = explode('=', $pair);
			if (count($kv) == 2) {
				$arr[(int)$kv[0]] = (int)$kv[1];
			}
		}
		return $arr;
	}
	/**
	 * Selects numbers by top position counts.
	 * For each top position (by count), selects the number at that position in the $numbers array.
	 * Skips duplicates. Stops when $limit is reached.
	 *
	 * @param array $positions [position => count]
	 * @param array $numbers   [0 => num, 1 => num, ...] (order matters)
	 * @param int   $limit     How many numbers to select
	 * @return array           Selected numbers
	 */
	private function select_by_position_index($positions, $numbers, $limit) {
		arsort($positions);
		$selected = [];
		if ($limit <= 0) return $selected;
		foreach ($positions as $pos => $count) {
			if (isset($numbers[$pos]) && !in_array($numbers[$pos], $selected)) {
				$selected[] = $numbers[$pos];
				if (count($selected) >= $limit) break;
			}
		}
		return $selected;
	}
	/**
	 * Generates a set of numbers using the Followers Only method for a given lottery.
	 *
	 * @param 	int    $lottery_id        The lottery ID.
	 * @param 	int    $combination_size  The total number of numbers to select.
	 * @param 	string $type              'ball_after' for actual ball, 'position' for draw order.
	 * @param 	string $select            The selected ball (may have '+' for extra) or position.
	 * @return 	string $selected |false   String of selected numbers, or FALSE if data not found or invalid.
	 */
	public function followers_only($lottery_id, $combination_size, $type, $select)
	{
		// Get followers and non-followers data from statistics_m
		$followers_row = $this->statistics_m->followers_exists($lottery_id);
		$nonfollowers_row = $this->statistics_m->nonfollowers_exists($lottery_id);
		if (!$followers_row) {
			return FALSE;
		}
		$followers_field = $followers_row['lottery_followers'];
		$nonfollowers_field = $nonfollowers_row ? $nonfollowers_row['lottery_nonfollowers'] : '';
		// For ball_after, strip '+' if present (extra ball)
		$select = trim($select);
		if ($type === 'after_ball' && strpos($select, '+') === 0) {
			$select = substr($select, 1);
		}
		$followers_groups = explode(',', $followers_field);
		$nonfollowers_groups = $nonfollowers_field ? explode(',', $nonfollowers_field) : [];
		$selected_followers = '';
		$selected_nonfollowers = '';
		if ($type === 'position') {
			// $select is the position (1-based)
			$position = (int)$select;
			// Use the Nth group (1-based) for position N
			if (isset($followers_groups[$position - 1])) {
				$group = $followers_groups[$position - 1];
				$selected_followers = substr($group, strpos($group, '>') + 1);
			}
			if (isset($nonfollowers_groups[$position - 1])) {
				$group = $nonfollowers_groups[$position - 1];
				$selected_nonfollowers = substr($group, strpos($group, '>') + 1);
			}
		} else {
			// Find the group for the selected ball (e.g., "34>")
			foreach ($followers_groups as $group) {
				if (strpos($group, $select . '>') === 0) {
					$selected_followers = substr($group, strlen($select) + 1); // Remove "34>"
					break;
				}
			}
			foreach ($nonfollowers_groups as $group) {
				if (strpos($group, $select . '>') === 0) {
					$selected_nonfollowers = substr($group, strlen($select) + 1); // Remove "34>"
					break;
				}
			}
		}
		if ($selected_followers === '') {
			return FALSE;
		}
		// Parse followers into dynamic groups by weight
		$follower_numbers = explode('|', $selected_followers);
		$groups = [];
		foreach ($follower_numbers as $item) {
			if (strpos($item, '=') !== false) {
				list($num, $weight) = explode('=', $item);
				$num = trim($num);
				$weight = (int)trim($weight);
				if (!isset($groups[$weight])) {
					$groups[$weight] = [];
				}
				$groups[$weight][] = $num;
			}
		}
		// Parse non-followers group (0 group)
		if (!empty($selected_nonfollowers)) {
			$groups[0] = explode('|', $selected_nonfollowers);
		}
		// Sort groups by weight descending (so highest group first)
		krsort($groups);
		// Count total numbers in all groups
		$total_numbers = 0;
		foreach ($groups as $nums) {
			$total_numbers += count($nums);
		}
		if ($total_numbers == 0) {
			return FALSE;
		}
		// --- Improved: Ensure at least one pick from each group if possible ---
		$picks = [];
		$remaining = $combination_size;
		foreach ($groups as $weight => $nums) {
			if ($remaining > 0 && count($nums) > 0) {
				$picks[$weight] = 1;
				$remaining--;
			} else {
				$picks[$weight] = 0;
			}
		}
		// Distribute remaining picks proportionally
		if ($remaining > 0) {
			foreach ($groups as $weight => $nums) {
				if ($remaining <= 0) break;
				$extra = round((count($nums) / $total_numbers) * $remaining);
				$to_add = min($extra, count($nums) - $picks[$weight]);
				$picks[$weight] += $to_add;
				$remaining -= $to_add;
			}
			// If still remaining, fill in order
			while ($remaining > 0) {
				foreach ($groups as $weight => $nums) {
					if ($remaining > 0 && $picks[$weight] < count($nums)) {
						$picks[$weight]++;
						$remaining--;
					}
				}
			}
		}
		// Select numbers from each group (first N)
		$selected = [];
		foreach ($groups as $weight => $nums) {
			$selected = array_merge($selected, array_slice($nums, 0, $picks[$weight]));
		}
		// If not enough numbers, fill from remaining numbers in any group
		if (count($selected) < $combination_size) {
			foreach ($groups as $nums) {
				foreach ($nums as $num) {
					if (!in_array($num, $selected)) {
						$selected[] = $num;
						if (count($selected) >= $combination_size) break 2;
					}
				}
			}
		}
    return implode(',', $selected);
	}
}