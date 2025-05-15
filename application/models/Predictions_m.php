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
}