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

	public function __construct()
	{
		parent::__construct();
		$this->load->model('statistics_m');
	}

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
	 * @param array 	$last_drawn The last_drawn array from the lottery object.
	 * @param int 		$balls_drawn  The number of main balls drawn.
	 * @param boolean	$duplicate    Whether to include duplicate extra balls.
	 * @return array Sorted associative array: [ 'ball_number' => points, ... ]
	 */
	public function get_sorted_ball_points($last_drawn, $balls_drawn, $duplicate)
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
		// Extra ball (if exists) and not duplicate
		if (isset($last_drawn['extra_win']) && isset($last_drawn['extra']) && !$duplicate) {
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
	 * 'ALL' => 'ALL', 1 => 'UP (N)', 2 => 'DOWN (N)'
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
		// Build the trends array with 'ALL' first, then the largest trend, then the other
		if ($up >= $down) {
			$trends = [
				'ALL' => 'ALL',
				'UP' => 'UP (' . $up . ')',
				'DOWN' => 'DOWN (' . $down . ')'
			];
		} else {
			$trends = [
				'ALL' => 'ALL',
				'DOWN' => 'DOWN (' . $down . ')',
				'UP' => 'UP (' . $up . ')'
			];
		}
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
		$result = ['ALL' => 'ALL'];
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
				$result[$digit_sum] = $digit_sum . ' (' . $total . ')';
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
	 * @return array Array for dropdown: ['ALL' => 'ALL', sum => total, ...]
	 */
	public function get_sums($winning_sums)
	{
		// Split by '|', take the first part
		$parts = explode('|', $winning_sums);
		$main_part = isset($parts[0]) ? $parts[0] : '';
		$result = ['ALL' => 'ALL'];
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
					$result[$sum] = $sum . ' (' . $count . ')';
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
	 * @return array Array for dropdown: ['ALL' => 'ALL', repeater_count => total, ...]
	 */
	public function get_repeaters($repeaters)
	{
		// Split by '|', take the first part
		$result = ['ALL' => 'ALL'];
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
				$result[$repeater] = $repeater . ' (' . $total . ')';
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
	 * @return array $consecutives for dropdown: ['ALL' => 'ALL', consecutive_count => total, ...]
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
		$result = ['ALL' => 'ALL'];
		foreach ($consecutives_arr as $count => $total) {
			$result[$count] = $count . ' (' . $total . ')';
		}
    return $result;
	}
	/**
	 * Parses the parity string and returns an associative array for the dropdown.
	 * Each entry is [odd-even => total], in the order provided, with "ALL" (value: 0) as the top option.
	 * Any parity with a total of 0 is removed.
	 *
	 * @param string $parity The string, e.g. "4-3=29,5-2=28,3-4=25,2-5=11,1-6=4,6-1=3|0-0"
	 * @return array Array for dropdown: ['ALL' => 'ALL', '4-3' => 29, ...]
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
		$result = ['ALL' => 'ALL'];
		foreach ($parity_arr as $odd_even => $total) {
			$result[$odd_even] = str_replace('-', ' / ', $odd_even) . ' (' . $total . ')';
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
		// Build dropdown array: 'ALL' => 'ALL', 1 => '3 (12)', 2 => '4 (10)', ...
		$result = ['ALL' => 'ALL'];
		foreach ($decade_counts as $decade => $count) {
			$result[$decade] = $decade . ' (' . $count . ')';
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
		// Build dropdown array: 'ALL' => 'ALL', 1 => '3 (12)', 2 => '7 (10)', ...
		$result = ['ALL' => 'ALL'];
		foreach ($last_counts as $digit => $count) {
			$result[$digit] = $digit . ' (' . $count . ')';
		}
		return $result;
	}
	/**
	 * Parses the number_range string from the highlights table and returns an array of the top 5 ranges.
	 * Each entry is [range => total], in descending order by total.
	 * Adds "ALL" as the first option in the array.
	 *
	 * @param string $number_range The string, e.g. "46=8,38=7,32=7,33=7,42=7"
	 * @return array Array for dropdown: ['ALL' => 'ALL', range => total, ...] (top 5 only, descending)
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
    // Build dropdown array: 'ALL' => 'ALL', 1 => '46 (8)', ...
    $result = ['ALL' => 'ALL'];
    foreach ($ranges as $range => $total) {
        $result[$range] = $range . ' (' . $total . ')';
    }
    return $result;
	}
	/**
	 * Parses the adjacents string and returns an associative array for the dropdown.
	 * Each entry is [adjacent_number => total], with "ALL" as the top option.
	 * The description for each is "Between Ball X and Ball Y", e.g. 1 => "Between Ball 1 and Ball 2 (7)".
	 *
	 * @param string $adjacents The string, e.g. "1=6,2=7,3=7,4=6,5=6,6=7|4=27"
	 * @return array Array for dropdown: ['ALL' => 'ALL', 1 => 'Between Ball 1 and Ball 2 (6)', ...]
	 */
	public function get_adjacents($adjacents)
	{
		// Split by '|', take the first part
		$parts = explode('|', $adjacents);
		$main_part = isset($parts[0]) ? $parts[0] : '';
		$adjacents_arr = ['ALL' => 'ALL'];
		if ($main_part) {
		$temp = [];
		$pairs = explode(',', $main_part);
		foreach ($pairs as $pair) {
			$kv = explode('=', $pair);
			if (count($kv) == 2) {
				$adj_num = (int)trim($kv[0]);
				$total = (int)trim($kv[1]);
				if ($total > 0) {
					$desc = "Ball {$adj_num} & Ball " . ($adj_num + 1) . " ({$total})";
					$temp[] = [
						'adj_num' => $adj_num,
						'desc'    => $desc,
						'total'   => $total
					];
				}
			}
		}
		// Sort by total descending
		usort($temp, function($a, $b) {
			return $b['total'] <=> $a['total'];
		});
		// Add to $adjacents_arr after 'ALL'
		foreach ($temp as $item) {
			$adjacents_arr[$item['adj_num']] = $item['desc'];
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
	/**
	 * Generates a set of numbers using the combined H-W-C and Followers method for a given lottery.
	 * 
	 * This method first calculates the H-W-C split (e.g. 4 hots, 3 warms, 3 colds for 10 numbers).
	 * For each group, it selects numbers by highest position count, but only includes numbers that are
	 * present in the followers/non-followers list for the selected ball (e.g. after 34).
	 * If a number from the H-W-C group is not found in the followers/non-followers, it is skipped.
	 * The process continues for hots, warms, and colds until the required total is reached.
	 * If not enough numbers are found, the remaining are filled from the followers/non-followers list.
	 *
	 * @param int    $lottery_id        The lottery ID.
	 * @param int    $combination_size  The total number of numbers to select.
	 * @param string $h_w_c             The HWC group string (e.g., "4-3-3").
	 * @param string $follower_type     'after_ball' for actual ball, 'position' for draw order.
	 * @param string $follower_select   The selected ball (may have '+' for extra) or position.
	 * @return string|false             Comma-separated string of selected numbers, or FALSE if data not found or invalid.
	 */
	public function hwc_followers($lottery_id, $combination_size, $h_w_c, $follower_type, $follower_select)
	{
	// 1. Parse H-W-C group (e.g., "4-3-3")
	preg_match('/(\d+)-(\d+)-(\d+)/', $h_w_c, $matches);
	$h = (int)$matches[1];
	$w = (int)$matches[2];
	$c = (int)$matches[3];
	// 2. Calculate scaled totals for combination size
	$total = $h + $w + $c;
	$h_total = round(($h / $total) * $combination_size);
	$w_total = round(($w / $total) * $combination_size);
	$c_total = $combination_size - $h_total - $w_total;
	// 3. Get HWC data
	$hwc = $this->statistics_m->h_w_c_exists($lottery_id);
	$position_row = $this->statistics_m->hwc_history_exists($lottery_id);
	if (!$hwc || !$position_row || empty($position_row['position'])) {
		return FALSE;
	}
	// 4. Parse numbers for each group (discard counts, keep order)
	$hots = array_map('intval', array_map(function($v){ return explode('=', $v)[0]; }, explode(',', $hwc['hots'])));
	$warms = array_map('intval', array_map(function($v){ return explode('=', $v)[0]; }, explode(',', $hwc['warms'])));
	$colds = array_map('intval', array_map(function($v){ return explode('=', $v)[0]; }, explode(',', $hwc['colds'])));
	// 5. Parse positions for each group
	$parts = explode('|', $position_row['position']);
	$h_positions = $this->parse_position_part($parts[0]);
	$w_positions = $this->parse_position_part($parts[1]);
	$c_positions = $this->parse_position_part($parts[2]);
	// 6. Get followers and non-followers for the selected ball
	$followers_row = $this->statistics_m->followers_exists($lottery_id);
	$nonfollowers_row = $this->statistics_m->nonfollowers_exists($lottery_id);
	if (!$followers_row) return FALSE;
	$followers_field = $followers_row['lottery_followers'];
	$nonfollowers_field = $nonfollowers_row ? $nonfollowers_row['lottery_nonfollowers'] : '';
	$follower_select = trim($follower_select);
	if ($follower_type === 'after_ball' && isset($follower_select[0]) && $follower_select[0] === '+') {
		$follower_select = substr($follower_select, 1);
	}
	$followers_groups = explode(',', $followers_field);
	$nonfollowers_groups = $nonfollowers_field ? explode(',', $nonfollowers_field) : [];
	$selected_followers = '';
	$selected_nonfollowers = '';
	if ($follower_type === 'position') {
		// $select is the position (1-based)
		$position = (int)$follower_select;
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
			if (strpos($group, $follower_select . '>') === 0) {
				$selected_followers = substr($group, strlen($follower_select) + 1); // Remove "34>"
				break;
			}
		}
		foreach ($nonfollowers_groups as $group) {
			if (strpos($group, $follower_select . '>') === 0) {
				$selected_nonfollowers = substr($group, strlen($follower_select) + 1); // Remove "34>"
				break;
			}
		}
	}
	if ($selected_followers === '') return FALSE;
	// Parse followers/nonfollowers into arrays
	$followers_list = [];
	foreach (explode('|', $selected_followers) as $item) {
		if (strpos($item, '=') !== false) {
			list($num, $weight) = explode('=', $item);
			$followers_list[] = trim($num);
		}
	}
	$nonfollowers_list = $selected_nonfollowers ? explode('|', $selected_nonfollowers) : [];
	// 7. Select HWC numbers by position, but only if in followers/nonfollowers
	$select_from_group = function($positions, $numbers, $limit, $valid_list) {
		arsort($positions);
		$selected = [];
		if ($limit <= 0) return $selected; // <-- Place this at the top!
		foreach ($positions as $pos => $count) {
			if (isset($numbers[$pos]) && in_array($numbers[$pos], $valid_list) && !in_array($numbers[$pos], $selected)) {
				$selected[] = $numbers[$pos];
				if (count($selected) >= $limit) break;
			}
		}
		return $selected;
	};
	$selected = [];
	if ($h_total > 0) {
		$selected = array_merge($selected, $select_from_group($h_positions, $hots, $h_total, $followers_list));
	}
	if ($w_total > 0) {
		$selected = array_merge($selected, $select_from_group($w_positions, $warms, $w_total, $followers_list));
	}
	if ($c_total > 0) {
		$selected = array_merge($selected, $select_from_group($c_positions, $colds, $c_total, array_merge($followers_list, $nonfollowers_list)));
	}
	// If not enough numbers, fill from remaining followers/nonfollowers
	$all_valid = array_merge($followers_list, $nonfollowers_list);
	if (count($selected) < $combination_size) {
		foreach ($all_valid as $num) {
			if (!in_array($num, $selected)) {
				$selected[] = $num;
				if (count($selected) >= $combination_size) break;
			}
		}
	}
	return implode(',', $selected);
	}
	/**
	 * Remove or allow friends in $selections based on $friendship, selection rules, and H-W-C heat map.
	 * @param int $lottery_id		Lottery/game id
	 * @param array $selections  	Array of selected numbers as strings, e.g. ['1', '2', '3']
	 * @param string $friendship 	'ALL', 'none', '1', or '2'
	 * @param array $heat_map 		['H' => [num => count,...], 'W' => [...], 'C' => [...]]
	 * @return array
	 */
	public function friend_search_hwc($lottery_id, $selections, $friendship, $heat_map)
	{
		// Fetch wins field from DB
		$row = $this->db->get_where('lottery_friends', ['lottery_id' => $lottery_id])->row_array();
		if (!$row || empty($row['wins'])) {
			return $selections;
		}
		// Parse friendship string (after first '|')
		$parts = explode('|', $row['wins']);
		$friend_str = isset($parts[1]) ? $parts[1] : '';
		if (!$friend_str) {
			return $selections;
		}
		// Parse friendships into 1-way and 2-way arrays
		$oneway = [];
		$twoway = [];
		$friendships = array_filter(array_map('trim', explode(',', $friend_str)));
		foreach ($friendships as $idx => $f) {
			$ball = $idx + 1; // Ball number (1-based)
			if (strpos($f, '<>') !== false) {
				$friend = (int)trim(str_replace('<>', '', $f));
				$twoway[] = [$ball, $friend];
			} elseif (strpos($f, '>') !== false) {
				$friend = (int)trim(str_replace('>', '', $f));
				$oneway[] = [$ball, $friend];
			}
		}
		$twoway = $this->predictions_m->twoway_unique($twoway);
		// Helper: Find replacement in the same heat group, starting from the same count, then lower, skipping already selected
		$find_heat_replacement = function($exclude, $target_num, $heat_map) {
			foreach (['H', 'W', 'C'] as $cat) {
				if (isset($heat_map[$cat][$target_num])) {
					$target_heat = $cat;
					$target_count = $heat_map[$cat][$target_num];
					// Build a list of [num, count] for this heat group
					$candidates = [];
					foreach ($heat_map[$cat] as $num => $count) {
						if (!in_array($num, $exclude)) {
							$candidates[$num] = $count;
						}
					}
					// Sort by count descending, then by number ascending
					arsort($candidates);
					// Try to find a replacement with the same count, then lower
					$counts_tried = [];
					$current_count = $target_count;
					while (true) {
						$found = false;
						foreach ($candidates as $num => $count) {
							if ($count == $current_count) {
								$found = true;
								return $num;
							}
						}
						$counts_tried[] = $current_count;
						// Find next lower count
						$lower_counts = array_filter($candidates, function($c) use ($counts_tried) {
							return !in_array($c, $counts_tried);
						});
						if (empty($lower_counts)) break;
						$current_count = max($lower_counts);
					}
				}
			}
			return null;
		};
		$result = $selections;
		// --- NONE: Remove all friendships, one full pass only ---
		if ($friendship === 'none' ) {
			$replaced_in_twoway = [];
			// Check and replace all 2-way friendships in one pass
			foreach ($twoway as $pair) {
				list($a, $b) = $pair;
				if (in_array($a, $result) && in_array($b, $result)) {
					$replace_idx = array_search($b, $result);
					$replacement = $find_heat_replacement($result, $b, $heat_map);
					if ($replacement !== null) {
						$replaced_in_twoway[] = $b; // Track replaced number
						$result[$replace_idx] = (string) $replacement; // Ensure replacement is a string
					}
				}
			}
			$replaced_in_oneway = [];
			foreach ($oneway as $pair) {
				list($a, $b) = $pair;
				// Exclude numbers that were replaced in twoway or already in oneway
				if (
					in_array($a, $result) && in_array($b, $result) &&
					!in_array($a, $replaced_in_twoway) && !in_array($b, $replaced_in_twoway) &&
					!in_array($a, $replaced_in_oneway) && !in_array($b, $replaced_in_oneway)
				) {
					$replace_idx = array_search($b, $result);
					// Exclude both current result, all replaced_in_twoway, and all replaced_in_oneway numbers
					$exclude = array_unique(array_merge($result, $replaced_in_twoway, $replaced_in_oneway));
					$replacement = $find_heat_replacement($exclude, $b, $heat_map);
					if ($replacement !== null) {
						$replaced_in_oneway[] = $b; // Track replaced number in oneway
						$result[$replace_idx] = (string) $replacement;
					}
				}
			}
			// After one full pass, return the result (no endless loop)
			return $result;
		}
		// --- 1-WAY: Only allow 1-way friendships, remove 2-way, stop if at least one 1-way friendship exists ---
		if ($friendship === '1') {
			$replaced_in_twoway = [];
			// Remove all 2-way friendships in one pass and track replaced numbers
			foreach ($twoway as $pair) {
				list($a, $b) = $pair;
				if (in_array($a, $result) && in_array($b, $result)) {
					$replace_idx = array_search($b, $result);
					$replacement = $find_heat_replacement($result, $b, $heat_map);
					if ($replacement !== null) {
						$replaced_in_twoway[] = $b; // Track replaced number
						$result[$replace_idx] = (string) $replacement;
					}
				}
			}
			// If any 1-way friendship exists, stop and return immediately
			foreach ($oneway as $pair) {
				list($a, $b) = $pair;
				if (in_array($a, $result) && in_array($b, $result)) {
					return $result;
				}
			}
			// If no 1-way friendship found, continue with replacements as before
			$replaced_in_oneway = [];
			foreach ($oneway as $pair) {
				list($a, $b) = $pair;
				if (
					in_array($a, $result) && in_array($b, $result) &&
					!in_array($a, $replaced_in_twoway) && !in_array($b, $replaced_in_twoway) &&
					!in_array($a, $replaced_in_oneway) && !in_array($b, $replaced_in_oneway)
				) {
					$replace_idx = array_search($b, $result);
					$exclude = array_unique(array_merge($result, $replaced_in_twoway, $replaced_in_oneway));
					$replacement = $find_heat_replacement($exclude, $b, $heat_map);
					if ($replacement !== null) {
						$replaced_in_oneway[] = $b;
						$result[$replace_idx] = (string) $replacement;
					}
				}
			}
			return $result;
		}
		// --- 2-WAY: Only allow 2-way friendships, remove 1-way, stop if at least one 2-way friendship exists ---
		if ($friendship === '2') {
			$replaced_in_oneway = [];
			// Remove all 2-way friendships in one pass and track replaced numbers
			foreach ($oneway as $pair) {
				list($a, $b) = $pair;
				if (in_array($a, $result) && in_array($b, $result)) {
					$replace_idx = array_search($b, $result);
					$replacement = $find_heat_replacement($result, $b, $heat_map);
					if ($replacement !== null) {
						$replaced_in_oneway[] = $b; // Track replaced number
						$result[$replace_idx] = (string) $replacement;
					}
				}
			}
			// If any 2-way friendship exists, stop and return immediately
			foreach ($twoway as $pair) {
				list($a, $b) = $pair;
				if (in_array($a, $result) && in_array($b, $result)) {
					return $result;
				}
			}
			// If no two-way friendship found, continue with replacements as before
			$replaced_in_twoway = [];
			foreach ($twoway as $pair) {
				list($a, $b) = $pair;
				// Case: $a is in $result, $b is NOT in $result
				if (in_array($a, $result) && !in_array($b, $result)) {
					// Find the heat group for $b
					$heat_group = null;
					foreach (['H', 'W', 'C'] as $cat) {
						if (isset($heat_map[$cat][$b])) {
							$heat_group = $cat;
							break;
						}
					}
					// Find a candidate in $result (not $a) to replace with $b, and in the same heat group
					foreach ($result as $idx => $num) {
						if ($num != $a && $heat_group !== null && isset($heat_map[$heat_group][$num])) {
							$result[$idx] = (string)$b;
							break; // Only replace one number
						}
					}
					// After inserting $b, check if both $a and $b are now in $result
					if (in_array($a, $result) && in_array($b, $result)) {
						return $result;
					}
				}
			}
		}
	}
	/**
	 * Helper function to ensure unique pairs in a two-way friendship array.
	 * This is used to avoid duplicates in the $twoway array.
	 *
	 * @param array 	$tw 		Array of two-way friendships
	 * @return array 	$unique		Unique two-way friendships
	 */
	private function twoway_unique($tw) {
		$unique = [];
		foreach ($tw as $pair) {
			// Sort the pair so [10,11] and [11,10] become [10,11]
			sort($pair, SORT_NUMERIC);
			$key = implode('<>', $pair);
			if (!isset($unique[$key])) {
				$unique[$key] = $pair;
			}
		}
		return array_values($unique);
	}
	/**
	 * Filters the $numbers array based on friend relationships, selection rules and followers.
	 *
	 * @param int    $lottery_id            Lottery/game id
	 * @param string $selections			Array of selected numbers as strings, e.g. ['1', '2', '3']
	 * @param string $friendship			'ALL', 'none', '1', or '2'
	 * @param array  $follow_list   	 	Array of followers for fallback
	 * @return array Filtered $selections
	 */
	public function friend_search_followers($lottery_id, $selections, $friendship, $follow_list)
	{
		// Fetch wins field from DB
		$row = $this->db->get_where('lottery_friends', ['lottery_id' => $lottery_id])->row_array();
		if (!$row || empty($row['wins'])) {
			return $selections;
		}
		// Parse friendship string (after first '|')
		$parts = explode('|', $row['wins']);
		$friend_str = isset($parts[1]) ? $parts[1] : '';
		if (!$friend_str) {
			return $selections;
		}
		// Parse friendships into 1-way and 2-way arrays
		$oneway = [];
		$twoway = [];
		$friendships = array_filter(array_map('trim', explode(',', $friend_str)));
		foreach ($friendships as $idx => $f) {
			$ball = $idx + 1; // Ball number (1-based)
			if (strpos($f, '<>') !== false) {
				$friend = (int)trim(str_replace('<>', '', $f));
				$twoway[] = [$ball, $friend];
			} elseif (strpos($f, '>') !== false) {
				$friend = (int)trim(str_replace('>', '', $f));
				$oneway[] = [$ball, $friend];
			}
		}
		$twoway = $this->predictions_m->twoway_unique($twoway);
		    // Helper: Find a replacement from $follow_list not already in $exclude
			$find_follower_replacement = function($exclude) use ($follow_list) {
				foreach ($follow_list as $num) {
					if (!in_array($num, $exclude)) {
						return $num;
					}
				}
				return null;
			};
			$result = $selections;
			// --- NONE: Remove all friendships, one full pass only ---
			if ($friendship === 'none') {
				$replaced_in_twoway = [];
				// 1. Check and replace all 2-way friendships in one pass
				foreach ($twoway as $pair) {
					list($a, $b) = $pair;
					if (in_array($a, $result) && in_array($b, $result)) {
						$replace_idx = array_search($b, $result);
						$replacement = $find_follower_replacement($result);
						if ($replacement !== null) {
							$replaced_in_twoway[] = $b;
							$result[$replace_idx] = (string) $replacement;
						}
					}
				}
				// 2. Exclude numbers that were replaced in twoway or already in oneway
				$replaced_in_oneway = [];
				foreach ($oneway as $pair) {
					list($a, $b) = $pair;
					if (
						in_array($a, $result) && in_array($b, $result) &&
						!in_array($a, $replaced_in_twoway) && !in_array($b, $replaced_in_twoway) &&
						!in_array($a, $replaced_in_oneway) && !in_array($b, $replaced_in_oneway)
					) {
						$replace_idx = array_search($b, $result);
						$exclude = array_unique(array_merge($result, $replaced_in_twoway, $replaced_in_oneway));
						$replacement = $find_follower_replacement($exclude);
						if ($replacement !== null) {
							$replaced_in_oneway[] = $b;
							$result[$replace_idx] = (string) $replacement;
						}
					}
				}
				return $result;
			}
			// --- 1-WAY: Only allow 1-way friendships, remove 2-way, stop if at least one 1-way friendship exists ---
			if ($friendship === '1') {
				// Remove all 2-way friendships in one pass
				foreach ($twoway as $pair) {
					list($a, $b) = $pair;
					if (in_array($a, $result) && in_array($b, $result)) {
						$replace_idx = array_search($b, $result);
						$replacement = $find_follower_replacement($result);
						if ($replacement !== null) {
							$result[$replace_idx] = (string) $replacement;
						}
					}
				}
				// If any 1-way friendship exists, stop and return immediately
				foreach ($oneway as $pair) {
					list($a, $b) = $pair;
					if (in_array($a, $result) && in_array($b, $result)) {
						return $result;
					}
				}
				// If no 1-way friendship found, continue with replacements as before
				foreach ($oneway as $pair) {
					list($a, $b) = $pair;
					if (in_array($a, $result) && in_array($b, $result)) {
						$replace_idx = array_search($b, $result);
						$replacement = $find_follower_replacement($result);
						if ($replacement !== null) {
							$result[$replace_idx] = (string) $replacement;
						}
					}
				}
				return $result;
			}
			// --- 2-WAY: Only allow 2-way friendships, remove 1-way, stop if at least one 2-way friendship exists ---
			if ($friendship === '2') {
				// 1. Remove all 1-way friendships in one pass
				foreach ($oneway as $pair) {
					list($a, $b) = $pair;
					if (in_array($a, $result) && in_array($b, $result)) {
						$replace_idx = array_search($b, $result);
						$replacement = $find_follower_replacement($result);
						if ($replacement !== null) {
							$result[$replace_idx] = (string) $replacement;
						}
					}
				}
				// 2. If any 2-way friendship exists, stop and return immediately
				foreach ($twoway as $pair) {
					list($a, $b) = $pair;
					if (in_array($a, $result) && in_array($b, $result)) {
						return $result;
					}
				}
				// 3. If no 2-way, find the first 2-way that is available (a in result, b not in result)
				foreach ($twoway as $pair) {
					list($a, $b) = $pair;
					if (in_array($a, $result) && !in_array($b, $result)) {
						// Replace a number in $result (not $a) with $b
						foreach ($result as $idx => $num) {
							if ($num != $a && !in_array($b, $result) && in_array($b, $follow_list)) {
								$result[$idx] = (string) $b;
								break;
							}
						}
						// After inserting $b, check if both $a and $b are now in $result
						if (in_array($a, $result) && in_array($b, $result)) {
							return $result;
						}
					}
				}
			}
		}
	/**
	 * Insert and filter number combinations with integrated filtering and pagination
	 *
	 * @param string $filepath Path to the combination file
	 * @param array  $number_array  Array of numbers to substitute (0-based index).
	 * @param int    $page          Current page number (1-based).
	 * @param int    $per_page      Number of combinations per page.
	 * @param array  $filter_select Array of filters to apply (e.g., trends, winning sums, etc.).
	 * @return array $result        Array of updated combinations (each as an array of numbers).
	 */
	public function insert_number_combination($filepath, $number_array, $page = 1, $per_page = 10, $filter_select = [])
	{
		// - lottery_data (for stats calculations)
		// $filter_select array contains:
    	// 1 - selected_trends
    	// 2 - selected_winning_sums
    	// 3 - selected_winning_digits
    	// 4 - selected_repeaters
    	// 5 - selected_consecutives
		// 6 - selected_parity (odd/even distribution)
		// 7 - selected_decades
		// 8  selected_last_digits
		// 9 - selected_number_range
		// 10 - selected_adjacents
		$result = [];
		$combinations_found = 0;
		$line_count = 0;
		$skip_count = 0;
		
		// Extract filter values
		$selected_trends = (!empty($filter_select['selected_trends']) && $filter_select['selected_trends'] !== '') 
			? $filter_select['selected_trends'] 
			: 'ALL';
		$drawn = $filter_select['drawn'] ?? 0;
		$last_drawn = $filter_select['lottery_last_drawn'] ?? [];
		$extra_ball = $filter_select['extra_ball'] ?? 0;
		
		// Prepare last drawn numbers for trend filtering
		$last_drawn_numbers = [];
		if ($selected_trends !== 'ALL' && !empty($last_drawn)) {
			for ($i = 1; $i <= $drawn; $i++) {
				if (isset($last_drawn['ball' . $i])) {
					$last_drawn_numbers[] = (int)$last_drawn['ball' . $i];
				}
			}
			if ($extra_ball && isset($last_drawn['extra'])) {
				$last_drawn_numbers[] = (int)$last_drawn['extra'];
			}
		}
		
		// Read file line by line and apply filters
		if (($handle = fopen($filepath, 'r')) !== false) {
			while (($line = fgets($handle)) !== false && $combinations_found < $per_page) {
				$line = trim($line);
				if (empty($line)) continue;
				
				$line_count++;
				
				// Skip lines for pagination (only if no filtering is applied)
				if ($selected_trends === 'ALL' && $line_count <= ($page - 1) * $per_page) {
					continue;
				}
				
				// Parse combination
				$positions = array_map('intval', explode(' ', $line));
				$combo_numbers = [];
				foreach ($positions as $pos) {
					// Validate position index
					if ($pos > 0 && isset($number_array[$pos - 1])) {
						$combo_numbers[] = $number_array[$pos - 1];
					}
				}
				
				// Skip if we don't have valid numbers
				if (empty($combo_numbers)) continue;
				
				sort($combo_numbers, SORT_NUMERIC); // Sort numbers from lowest to highest

				// Re-index as ball1, ball2, ...
				$combo = [];
				foreach ($combo_numbers as $idx => $num) {
					$combo['ball'.($idx+1)] = $num;
				}
				
				// Apply trend filter if specified
				if ($selected_trends !== 'ALL') {
					if (!$this->check_trend_match($combo, $last_drawn_numbers, $selected_trends)) {
						continue; // Skip this combination if it doesn't match trend
					}
					
					// For filtered results, we need to skip already collected combinations for pagination
					if ($skip_count < ($page - 1) * $per_page) {
						$skip_count++;
						continue;
					}
				}
				
				// Apply other filters
				if (!$this->apply_other_filters($combo, $filter_select)) {
					continue; // Skip this combination if it doesn't pass other filters
				}
				
				// Create a separate combo array for the combination display
				$combo_data = ['combo' => $combo];
				
				// Get stats if filter_select is provided and has the required keys
				if (!empty($filter_select) && isset($filter_select['drawn']) && isset($filter_select['lottery_last_drawn'])) {
					$stats = $this->get_combo_stats($combo, $filter_select['drawn'], $filter_select['lottery_last_drawn']);
					// Merge combo data with stats - flattens into one array
					$combo_data = array_merge($combo_data, $stats);
				} else {
					// If no filter data, just add the combo
					$combo_data = ['combo' => $combo];
				}
				
				$result[] = $combo_data;
				$combinations_found++;
			}
			fclose($handle);
		}
		
		return $result;
	}
	
	/**
	 * Apply additional filters to a combination
	 *
	 * @param array $combo The combination to check
	 * @param array $filter_select Array of filter criteria
	 * @return bool True if combination passes all filters, false otherwise
	 */
	private function apply_other_filters($combo, $filter_select)
	{
		// Example filter implementations - expand as needed
		
		// Filter by winning sums
		if (!empty($filter_select['selected_winning_sums']) && $filter_select['selected_winning_sums'] !== 'ALL') {
			$combo_sum = array_sum(array_values($combo));
			$winning_sums = is_array($filter_select['selected_winning_sums']) 
				? $filter_select['selected_winning_sums'] 
				: [$filter_select['selected_winning_sums']];
			
			if (!in_array($combo_sum, $winning_sums)) {
				return false;
			}
		}
		
		// Filter by repeaters
		if (!empty($filter_select['selected_repeaters']) && $filter_select['selected_repeaters'] !== 'ALL') {
			$drawn = $filter_select['drawn'] ?? 0;
			$last_drawn = $filter_select['lottery_last_drawn'] ?? [];
			$repeater_count = $this->is_repeater($combo, $drawn, $last_drawn);
			
			$expected_repeaters = (int)$filter_select['selected_repeaters'];
			if ($repeater_count !== $expected_repeaters) {
				return false;
			}
		}
		
		// Filter by consecutive numbers
		if (!empty($filter_select['selected_consecutives']) && $filter_select['selected_consecutives'] !== 'ALL') {
			$drawn = $filter_select['drawn'] ?? 0;
			$consecutive_count = $this->has_consecutive($combo, $drawn);
			
			$expected_consecutives = (int)$filter_select['selected_consecutives'];
			if ($consecutive_count !== $expected_consecutives) {
				return false;
			}
		}
		
		// Filter by digit sums (selected_winning_digits)
		if (!empty($filter_select['selected_winning_digits']) && $filter_select['selected_winning_digits'] !== 'ALL') {
			$drawn = $filter_select['drawn'] ?? 0;
			$combo_digit_sum = $this->statistics_m->lottery_draw_sumdigits($combo, $drawn);
			
			$selected_digit_sum = (int)$filter_select['selected_winning_digits'];
			if ($combo_digit_sum !== $selected_digit_sum) {
				return false;
			}
		}
		
		// Filter by odd/even distribution (selected_parity)
		if (!empty($filter_select['selected_parity']) && $filter_select['selected_parity'] !== 'ALL') {
			$drawn = $filter_select['drawn'] ?? 0;
			$odd_count = $this->statistics_m->lottery_draw_odd($combo, $drawn);
			$even_count = $this->statistics_m->lottery_draw_even($combo, $drawn);
			
			// Parse the selected parity format (e.g., "4-3" for 4 odd, 3 even)
			$parity_parts = explode('-', $filter_select['selected_parity']);
			if (count($parity_parts) === 2) {
				$expected_odd = (int)$parity_parts[0];
				$expected_even = (int)$parity_parts[1];
				
				if ($odd_count !== $expected_odd || $even_count !== $expected_even) {
					return false;
				}
			}
		}
		
		// Filter by decades (selected_decades)
		if (!empty($filter_select['selected_decades']) && $filter_select['selected_decades'] !== 'ALL') {
			$drawn = $filter_select['drawn'] ?? 0;
			$decade_count = $this->count_decade_numbers($combo, $drawn);
			
			$expected_decades = (int)$filter_select['selected_decades'];
			if ($decade_count !== $expected_decades) {
				return false;
			}
		}
		
		// Add more filter implementations here:
		// - selected_last_digits
		// - selected_number_range
		// - selected_adjacents
		
		return true;
	}

	// ...existing code...
	
	/**
	 * Calculates statistics for a given combination array.
	 *
	 * Returns an associative array with keys: sum, digit_sum, repeater, consecutive,
	 * odd_even, decade, last, and range. Each value represents a statistic for the combination.
	 *
	 * @param array $combo  		Array of numbers representing a single combination.
	 * @param array $last_draw  	Array of last drawn numbers
	 * @return array        		Associative array of statistics for the combination.
	 */
	public function get_combo_stats($combo,$max,$last_draw)
	{
		// Validate that combo has the expected keys
		if (empty($combo) || !is_array($combo)) {
			return [
				'sum' => 0,
				'digit_sum' => 0,
				'repeater' => 0,
				'consecutive' => 0,
				'even' => 0,
				'odd' => 0,
				'decade' => 0,
				'last' => 0,
				'range' => 0,
			];
		}
		
		// Get the numeric values from the combo array
		$combo_values = array_values($combo);
		
		return [
			'sum' => $this->statistics_m->lottery_draw_sum($combo,$max),
			'digit_sum' => $this->statistics_m->lottery_draw_sumdigits($combo,$max),
			'repeater' => $this->is_repeater($combo,$max,$last_draw), // Implement as needed
			'consecutive' => $this->has_consecutive($combo,$max), // Implement as needed
			'even' => $this->statistics_m->lottery_draw_even($combo,$max),
			'odd' => $this->statistics_m->lottery_draw_odd($combo,$max), // Implement as needed
			'decade' => $this->statistics_m->lottery_draw_decade($combo,$max), // Implement as needed
			'last' => $this->statistics_m->lottery_draw_last($combo,$max), // Implement as needed
			'range' => !empty($combo_values) ? max($combo_values) - min($combo_values) : 0,
		];
	}
	
	/**
	 * Counts how many numbers in $combo are also in $last_draw (repeaters).
	 * Returns the number of repeaters (0, 1, ...).
	 *
	 * @param array $combo     Associative array of balls (e.g., ['ball1'=>2, ...])
	 * @param int   $max       Number of balls in the combination
	 * @param array $last_draw Array of last drawn numbers (e.g., ['ball1'=>2, ...])
	 * @return int             Number of repeaters
	 */
	public function is_repeater($combo, $max, $last_draw)
	{
		// Extract just the numbers from both arrays
		$combo_numbers = array_values($combo);
		$last_numbers = [];
		for ($i = 1; $i <= $max; $i++) {
			if (isset($last_draw['ball'.$i])) {
				$last_numbers[] = $last_draw['ball'.$i];
			}
		}
		// Count how many numbers are repeated
	return count(array_intersect($combo_numbers, $last_numbers));
	}

	/**
	 * Counts the number of consecutive pairs in the combination.
	 * Returns 0 if no consecutive numbers, 1 for one pair, etc.
	 *
	 * @param array $combo Associative array of balls (e.g., ['ball1'=>2, ...])
	 * @param int   $max   Number of balls in the combination
	 * @return int         Number of consecutive pairs
	 */
	public function has_consecutive($combo, $max)
	{
		$numbers = array_values($combo);
		sort($numbers, SORT_NUMERIC);
		$consecutive_count = 0;
		for ($i = 1; $i < $max; $i++) {
			if ($numbers[$i] - $numbers[$i-1] == 1) {
				$consecutive_count++;
			}
		}
	return $consecutive_count;
	}
	
	/**
	 * Counts the total number of numbers that fall within the same decade as at least one other number.
	 * Returns the count of numbers that share a decade with another number in the combination.
	 *
	 * @param array $combo     Associative array of balls (e.g., ['ball1'=>22, 'ball2'=>23, ...])
	 * @param int   $max       Number of balls in the combination
	 * @return int             Count of numbers that share a decade with at least one other number
	 */
	public function count_decade_numbers($combo, $max)
	{
		$numbers = array_values($combo);
		$decade_counts = [];
		
		// Count numbers in each decade
		foreach ($numbers as $number) {
			$decade = intval($number / 10); // 22 -> 2, 23 -> 2, 35 -> 3, etc.
			if (!isset($decade_counts[$decade])) {
				$decade_counts[$decade] = 0;
			}
			$decade_counts[$decade]++;
		}
		
		// Count total numbers that are in decades with more than 1 number
		$total_decade_numbers = 0;
		foreach ($decade_counts as $count) {
			if ($count > 1) {
				$total_decade_numbers += $count;
			}
		}
		
		return $total_decade_numbers;
	}
	
	/**
	 * Filter combinations based on up/down trends compared to last drawn numbers
	 *
	 * @param array $combinations Array of combinations to filter
	 * @param array $last_drawn Last drawn numbers including extra ball
	 * @param int $drawn Number of balls drawn for this lottery
	 * @param int $extra_ball Whether extra ball is included (1 or 0)
	 * @param string $selected_trend 'UP', 'DOWN', or 'ALL'
	 * @param int $page Current page number
	 * @param int $per_page Number of combinations per page
	 * @param string $filepath Path to the combination text file
	 * @return array|false Filtered combinations or false if no matches found
	 */
	public function filtered_trends($combinations, $last_drawn, $drawn, $extra_ball, $selected_trend, $page, $per_page, $filepath)
	{
		if ($selected_trend === 'ALL') {
			return $combinations;
		}
		
		// Extract last drawn numbers for comparison
		$last_drawn_numbers = [];
		for ($i = 1; $i <= $drawn; $i++) {
			if (isset($last_drawn['ball' . $i])) {
				$last_drawn_numbers[] = (int)$last_drawn['ball' . $i];
			}
		}
		// Include extra ball if enabled
		if ($extra_ball && isset($last_drawn['extra'])) {
			$last_drawn_numbers[] = (int)$last_drawn['extra'];
		}
		
		$filtered_combinations = [];
		$needed_combinations = $per_page;
		$combinations_found = 0;
		
		// Start filtering from the provided combinations
		foreach ($combinations as $combo) {
			if ($this->check_trend_match($combo, $last_drawn_numbers, $selected_trend)) {
				$filtered_combinations[] = $combo;
				$combinations_found++;
				
				if ($combinations_found >= $needed_combinations) {
					break;
				}
			}
		}
		
		// If we don't have enough combinations, fetch more from the file
		if ($combinations_found < $needed_combinations && file_exists($filepath)) {
			// Get the number array from session for processing additional combinations
			$CI =& get_instance();
			$number_array = $CI->session->userdata('futures_number_array');
			
			if ($number_array) {
				$additional_combinations = $this->fetch_additional_trend_combinations(
					$filepath,
					$number_array,
					$last_drawn_numbers,
					$selected_trend,
					$needed_combinations - $combinations_found,
					($page - 1) * $per_page + count($combinations) // Skip already processed lines
				);
				
				if ($additional_combinations) {
					$filtered_combinations = array_merge($filtered_combinations, $additional_combinations);
				}
			}
		}
		
		// Return false if no combinations match the trend filter
		if (empty($filtered_combinations)) {
			return false;
		}
		
		return $filtered_combinations;
	}
	
	/**
	 * Check if a combination matches the selected trend
	 *
	 * @param array $combo Combination to check (with ball1, ball2, etc. keys)
	 * @param array $last_drawn_numbers Last drawn numbers to compare against
	 * @param string $trend 'UP' or 'DOWN'
	 * @return bool True if combination matches trend, false otherwise
	 */
	private function check_trend_match($combo, $last_drawn_numbers, $trend)
	{
		// Extract combination numbers in order
		$combo_numbers = [];
		foreach ($combo as $key => $value) {
			if (strpos($key, 'ball') === 0) {
				$combo_numbers[] = (int)$value;
			}
		}
		
		// Sort both arrays for consistent comparison
		sort($combo_numbers, SORT_NUMERIC);
		sort($last_drawn_numbers, SORT_NUMERIC);
		
		if ($trend === 'UP') {
			// All combination numbers must be greater than corresponding last drawn numbers
			foreach ($combo_numbers as $index => $combo_number) {
				if (isset($last_drawn_numbers[$index])) {
					if ($combo_number <= $last_drawn_numbers[$index]) {
						return false;
					}
				}
			}
			return true;
		} elseif ($trend === 'DOWN') {
			// All combination numbers must be less than corresponding last drawn numbers
			foreach ($combo_numbers as $index => $combo_number) {
				if (isset($last_drawn_numbers[$index])) {
					if ($combo_number >= $last_drawn_numbers[$index]) {
						return false;
					}
				}
			}
			return true;
		}
		
		return false;
	}
	
	/**
	 * Fetch additional combinations from file to meet pagination requirements
	 *
	 * @param string $filepath Path to combinations file
	 * @param array $number_array Number array for position mapping
	 * @param array $last_drawn_numbers Last drawn numbers for comparison
	 * @param string $trend Trend type ('UP' or 'DOWN')
	 * @param int $needed_count Number of additional combinations needed
	 * @param int $skip_lines Number of lines to skip (already processed)
	 * @return array Additional filtered combinations
	 */
	private function fetch_additional_trend_combinations($filepath, $number_array, $last_drawn_numbers, $trend, $needed_count, $skip_lines)
	{
		if (!file_exists($filepath)) {
			return [];
		}
		
		$additional_combinations = [];
		$line_count = 0;
		$found_count = 0;
		
		if (($handle = fopen($filepath, 'r')) !== false) {
			// Skip already processed lines
			while ($line_count < $skip_lines && ($line = fgets($handle)) !== false) {
				$line_count++;
			}
			
			// Continue reading and filtering until we have enough combinations
			while (($line = fgets($handle)) !== false && $found_count < $needed_count) {
				$line = trim($line);
				if (empty($line)) continue;
				
				// Parse the combination line and convert to proper format
				$positions = array_map('intval', explode(' ', $line));
				$combo_numbers = [];
				foreach ($positions as $pos) {
					if (isset($number_array[$pos - 1])) {
						$combo_numbers[] = $number_array[$pos - 1];
					}
				}
				
				// Sort and format as ball1, ball2, etc.
				sort($combo_numbers, SORT_NUMERIC);
				$combo = [];
				foreach ($combo_numbers as $idx => $num) {
					$combo['ball'.($idx+1)] = $num;
				}
				
				if ($this->check_trend_match($combo, $last_drawn_numbers, $trend)) {
					$additional_combinations[] = $combo;
					$found_count++;
				}
				
				$line_count++;
			}
			fclose($handle);
		}
		
		return $additional_combinations;
	}
	
	/**
	 * Get total count of combinations that pass all filters
	 *
	 * @param string $filepath Path to the combination file
	 * @param array  $number_array  Array of numbers to substitute
	 * @param array  $filter_select Array of filters to apply
	 * @return int Total count of filtered combinations
	 */
	public function get_filtered_combinations_count($filepath, $number_array, $filter_select = [])
	{
		if (!file_exists($filepath)) {
			return 0;
		}
		
		// If no filters are applied, return total file lines
		$selected_trends = $filter_select['selected_trends'] ?? 'ALL';
		$has_other_filters = $this->has_active_filters($filter_select);
		
		if ($selected_trends === 'ALL' && !$has_other_filters) {
			return count(file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
		}
		
		// Count filtered combinations
		$count = 0;
		$drawn = $filter_select['drawn'] ?? 0;
		$last_drawn = $filter_select['lottery_last_drawn'] ?? [];
		$extra_ball = $filter_select['extra_ball'] ?? 0;
		
		// Prepare last drawn numbers for trend filtering
		$last_drawn_numbers = [];
		if ($selected_trends !== 'ALL' && !empty($last_drawn)) {
			for ($i = 1; $i <= $drawn; $i++) {
				if (isset($last_drawn['ball' . $i])) {
					$last_drawn_numbers[] = (int)$last_drawn['ball' . $i];
				}
			}
			if ($extra_ball && isset($last_drawn['extra'])) {
				$last_drawn_numbers[] = (int)$last_drawn['extra'];
			}
		}
		
		// Count combinations that pass filters
		if (($handle = fopen($filepath, 'r')) !== false) {
			while (($line = fgets($handle)) !== false) {
				$line = trim($line);
				if (empty($line)) continue;
				
				// Parse combination
				$positions = array_map('intval', explode(' ', $line));
				$combo_numbers = [];
				foreach ($positions as $pos) {
					if ($pos > 0 && isset($number_array[$pos - 1])) {
						$combo_numbers[] = $number_array[$pos - 1];
					}
				}
				
				if (empty($combo_numbers)) continue;
				
				sort($combo_numbers, SORT_NUMERIC);
				$combo = [];
				foreach ($combo_numbers as $idx => $num) {
					$combo['ball'.($idx+1)] = $num;
				}
				
				// Check if combination passes all filters
				if ($selected_trends !== 'ALL') {
					if (!$this->check_trend_match($combo, $last_drawn_numbers, $selected_trends)) {
						continue;
					}
				}
				
				if (!$this->apply_other_filters($combo, $filter_select)) {
					continue;
				}
				
				$count++;
			}
			fclose($handle);
		}
		
		return $count;
	}
	
	/**
	 * Check if any filters other than trends are active
	 *
	 * @param array $filter_select Array of filter criteria
	 * @return bool True if other filters are active
	 */
	private function has_active_filters($filter_select)
	{
		$filter_keys = [
			'selected_winning_sums', 'selected_winning_digits', 'selected_repeaters',
			'selected_consecutives', 'selected_parity', 'selected_decades',
			'selected_last_digits', 'selected_number_range', 'selected_adjacents'
		];
		
		foreach ($filter_keys as $key) {
			if (!empty($filter_select[$key]) && $filter_select[$key] !== 'ALL') {
				return true;
			}
		}
		
		return false;
	}
}