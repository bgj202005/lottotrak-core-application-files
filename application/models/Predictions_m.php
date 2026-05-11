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
	// Combination file functions moved to Combination_files_m model
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
	 * Calculate detailed breakdown of winning tickets for different match scenarios
	 * Based on nCr combinatorial mathematics for full coverage systems
	 * 
	 * @param int $numbers_picked Total numbers picked by user (e.g., 8)
	 * @param int $balls_drawn Numbers drawn in lottery (e.g., 6)  
	 * @param int $minimum_prize_match Minimum matches needed for a prize (usually 2)
	 * @return array Detailed breakdown of winning tickets for each scenario
	 */
	public function calculate_detailed_breakdown($numbers_picked, $balls_drawn, $minimum_prize_match = 2, $is_independent_extra_ball = false, $extra_ball_range = 7, $actual_tickets = null) {
		$breakdown = [];
		
		// Use actual ticket count from file
		$total_tickets = $actual_tickets ?? 1000; // Default if not provided
		
		// For independent extra ball lotteries, work backwards from actual ticket count
		if ($is_independent_extra_ball && $actual_tickets !== null) {
			// Calculate base combinations (half of total for independent extra ball)
			$base_combinations = $actual_tickets / 2;
			
			// Find numbers_picked that generates close to this many base combinations
			for ($n = $balls_drawn; $n <= 50; $n++) {
				if ($this->combination($n, $balls_drawn) >= $base_combinations) {
					$numbers_picked = $n;
					break;
				}
			}
		}
		
		// For each possible scenario (from all correct down to 0)
		for ($correct_numbers = $balls_drawn; $correct_numbers >= 0; $correct_numbers--) {
			$scenario = [
				'picked_correctly' => $correct_numbers,
				'subprizes' => [],
				'extra_ball_subprizes' => [],
				'total_winning_tickets' => 0,
				'non_winning_tickets' => 0,
				'scenario_probability' => 0
			];
			
			// Calculate base scenario tickets using mathematical model
			$base_scenario_tickets = $this->calculate_total_scenario_tickets($numbers_picked, $balls_drawn, $correct_numbers);
			
			// For independent extra ball, double the base (with/without extra ball)
			if ($is_independent_extra_ball) {
				$base_scenario_tickets *= 2;
			}
			
			// Calculate what proportion this scenario represents of the total
			$total_base_tickets = $is_independent_extra_ball ? 
				($this->combination($numbers_picked, $balls_drawn) * 2) : 
				$this->combination($numbers_picked, $balls_drawn);
			
			$scenario_proportion = ($total_base_tickets > 0) ? ($base_scenario_tickets / $total_base_tickets) : 0;
			
			// Apply this proportion to the actual file size - exact calculation
			$scenario_total_tickets = round($total_tickets * $scenario_proportion);
			
			if ($is_independent_extra_ball) {
				// Calculate regular prizes (without extra ball) - exact mathematical calculation
				$regular_winning_total = 0;
				for ($matches = $correct_numbers; $matches >= $minimum_prize_match; $matches--) {
					$base_tickets = $this->calculate_scenario_tickets($numbers_picked, $balls_drawn, $correct_numbers, $matches);
					$proportion = ($total_base_tickets > 0) ? ($base_tickets / $total_base_tickets) : 0;
					$tickets = round($total_tickets * $proportion);
					
					if ($tickets > 0) {
						$percentage = round(($tickets / $total_tickets) * 100, 3);
						$scenario['subprizes'][$matches] = [
							'tickets' => $tickets,
							'percentage' => $percentage
						];
						$regular_winning_total += $tickets;
					}
				}
				
				// Calculate extra ball prizes (with extra ball) - exact mathematical calculation
				$extra_winning_total = 0;
				for ($matches = $correct_numbers; $matches >= 1; $matches--) {
					$base_tickets = $this->calculate_scenario_tickets($numbers_picked, $balls_drawn, $correct_numbers, $matches);
					$proportion = ($total_base_tickets > 0) ? ($base_tickets / $total_base_tickets) : 0;
					$tickets = round($total_tickets * $proportion);
					
					if ($tickets > 0) {
						$percentage = round(($tickets / $total_tickets) * 100, 3);
						$scenario['extra_ball_subprizes'][$matches] = [
							'tickets' => $tickets,
							'percentage' => $percentage
						];
						$extra_winning_total += $tickets;
					}
				}
				
				// Add "Extra only" (0 main matches + extra ball) if this is the 0-correct scenario
				if ($correct_numbers == 0) {
					$base_tickets = $this->calculate_scenario_tickets($numbers_picked, $balls_drawn, 0, 0);
					$proportion = ($total_base_tickets > 0) ? ($base_tickets / $total_base_tickets) : 0;
					$tickets = round($total_tickets * $proportion);
					
					if ($tickets > 0) {
						$percentage = round(($tickets / $total_tickets) * 100, 3);
						$scenario['extra_ball_subprizes'][0] = [
							'tickets' => $tickets,
							'percentage' => $percentage
						];
						$extra_winning_total += $tickets;
					}
				}
				
				// Set total winning tickets (this will be corrected later in non-winning calculation)
				$scenario['total_winning_tickets'] = $regular_winning_total + $extra_winning_total;
			} else {
				// Regular lottery logic - only for valid prize levels
				for ($matches = $correct_numbers; $matches >= $minimum_prize_match; $matches--) {
					$base_tickets = $this->calculate_scenario_tickets($numbers_picked, $balls_drawn, $correct_numbers, $matches);
					$proportion = ($total_base_tickets > 0) ? ($base_tickets / $total_base_tickets) : 0;
					$tickets = round($total_tickets * $proportion);
					
					if ($tickets > 0) {
						$percentage = round(($tickets / $total_tickets) * 100, 3);
						$scenario['subprizes'][$matches] = [
							'tickets' => $tickets,
							'percentage' => $percentage
						];
						$scenario['total_winning_tickets'] += $tickets;
					}
				}
			}
			
			// Calculate scenario probability and non-winning tickets
			$scenario['scenario_probability'] = round(($scenario_total_tickets / $total_tickets) * 100, 3);
			
			if ($is_independent_extra_ball) {
				// For independent extra ball, each row shows the full scenario
				// Calculate non-winning for regular scenario (full scenario - regular wins only)
				$scenario['regular_non_winning'] = max(0, $scenario_total_tickets - $regular_winning_total);
				$scenario['non_winning_tickets'] = $scenario['regular_non_winning'];
				
				// Calculate non-winning for extra scenario (full scenario - extra wins only)
				$scenario['extra_non_winning'] = max(0, $scenario_total_tickets - $extra_winning_total);
				
			} else {
				// Ensure the row totals exactly match the scenario total
				$scenario['non_winning_tickets'] = max(0, $scenario_total_tickets - $scenario['total_winning_tickets']);
			}
			
			// Only include scenarios that have tickets
			if ($scenario_total_tickets > 0) {
				$breakdown[] = $scenario;
			}
		}
		
		return [
			'breakdown' => $breakdown,
			'total_tickets' => $total_tickets,
			'numbers_picked' => $numbers_picked,
			'balls_drawn' => $balls_drawn,
			'minimum_prize_match' => $minimum_prize_match,
			'is_independent_extra_ball' => $is_independent_extra_ball,
			'extra_ball_range' => $extra_ball_range
		];
	}
	
	/**
	 * Calculate total tickets for a specific scenario (exactly X numbers correct)
	 * 
	 * @param int $numbers_picked Total numbers picked
	 * @param int $balls_drawn Numbers drawn in lottery
	 * @param int $correct_in_picked How many of the drawn numbers are in user's picked numbers
	 * @return int Total tickets with exactly this many correct numbers
	 */
	private function calculate_total_scenario_tickets($numbers_picked, $balls_drawn, $correct_in_picked) {
		// This calculates all possible tickets where exactly $correct_in_picked numbers are correct
		// It sums up all the individual ticket types for this scenario
		
		$total_scenario_tickets = 0;
		
		// Sum all possible match levels for this scenario
		for ($matches = $correct_in_picked; $matches >= 0; $matches--) {
			$tickets = $this->calculate_scenario_tickets($numbers_picked, $balls_drawn, $correct_in_picked, $matches);
			$total_scenario_tickets += $tickets;
		}
		
		return $total_scenario_tickets;
	}
	
	/**
	 * Calculate tickets for a specific match scenario using combinatorial mathematics
	 * 
	 * @param int $numbers_picked Total numbers picked
	 * @param int $balls_drawn Numbers drawn in lottery
	 * @param int $correct_in_picked How many of the drawn numbers are in user's picked numbers
	 * @param int $matches Required matches for this prize tier
	 * @return int Number of tickets with exactly this many matches
	 */
	private function calculate_scenario_tickets($numbers_picked, $balls_drawn, $correct_in_picked, $matches) {
		// If we need more matches than correct numbers available, return 0
		if ($matches > $correct_in_picked) {
			return 0;
		}
		
		// Calculate non-winning numbers in picked set
		$non_winning_in_picked = $numbers_picked - $correct_in_picked;
		
		// Numbers needed from non-winning set to complete the ticket
		$non_winning_needed = $balls_drawn - $matches;
		
		// If we need more non-winning numbers than available, return 0
		if ($non_winning_needed > $non_winning_in_picked) {
			return 0;
		}
		
		// Calculate using combinatorial formula
		// C(correct_in_picked, matches) * C(non_winning_in_picked, non_winning_needed)
		$winning_combinations = $this->combination($correct_in_picked, $matches);
		$non_winning_combinations = $this->combination($non_winning_in_picked, $non_winning_needed);
		
		return $winning_combinations * $non_winning_combinations;
	}
	
	/**
	 * Calculate combinatorial nCr (n choose r)
	 * 
	 * @param int $n Total items
	 * @param int $r Items to choose
	 * @return int Result of nCr calculation
	 */
	public function combination($n, $r) {
		if ($r > $n || $r < 0) {
			return 0;
		}
		if ($r == 0 || $r == $n) {
			return 1;
		}
		
		// Use the more efficient calculation: C(n,r) = C(n, n-r)
		if ($r > $n - $r) {
			$r = $n - $r;
		}
		
		$result = 1;
		for ($i = 0; $i < $r; $i++) {
			$result = $result * ($n - $i) / ($i + 1);
		}
		
		return round($result);
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
     * Get combination files for a lottery with active status based on saved filters
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
        
        // Get current user ID for active status check
        $CI =& get_instance();
        $current_user_id = $CI->session->userdata('id');
        
        // Fetch combination files with left join to check for active saved filters
        $this->db->select('lcf.id, lcf.file_name, lcf.N, lcf.R, lcf.CCCC, 
                          COALESCE(MAX(lfc.active), 0) as active,
                          lfc.file_name as saved_filter_file_name');
        $this->db->from('lottery_combination_files lcf');
		$this->db->join('lottery_combination_filters lfc', 
			       'lcf.id = lfc.combo_id AND lfc.user = 1 AND lfc.user_id = ' . (int)$current_user_id . ' AND lfc.lottery_id = ' . (int)$lottery_id, 'left');
        $this->db->where('lcf.R', $balls_drawn); // Match the balls_drawn value
        $this->db->group_by('lcf.id, lcf.file_name, lcf.N, lcf.R, lcf.CCCC, lfc.file_name');
        $this->db->order_by('lcf.file_name', 'ASC');
        $query = $this->db->get();
        
        // Check if saved filtered files actually exist on disk
        $results = $query->result_array();
        foreach ($results as &$row) {
            $row['saved_file_exists'] = false;
            if (!empty($row['saved_filter_file_name'])) {
                $pick_dir = FCPATH . 'combinations/pick' . $balls_drawn . '/';
                $saved_file_path = $pick_dir . $row['saved_filter_file_name'] . '.txt';
                $row['saved_file_exists'] = file_exists($saved_file_path);
            }
        }
        
        return $results; // Return the result as an array
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
        $this->db->select('range, lottery_followers, wins, positions, draw_id, extra_included, extra_draws, dupextra_wins');
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
     * Retrieves the friendship counts from the wins field and returns them sorted by largest occurrence first.
     *
     * @param int $lottery_id The ID of the lottery.
     * @return array An array of friendship options with counts, sorted by largest occurrence first.
     */
    public function get_friends_dropdown_options($lottery_id)
    {
        $this->db->select('wins');
        $this->db->from('lottery_friends');
        $this->db->where('lottery_id', $lottery_id);
        $result = $this->db->get()->row_array();
        
        $options = ['all' => 'ALL'];
        
        if ($result && !empty($result['wins'])) {
            // Parse the wins field: "0,2121,259|..."
            $wins_parts = explode('|', $result['wins']);
            if (!empty($wins_parts[0])) {
                $counts = explode(',', $wins_parts[0]);
                
                if (count($counts) >= 3) {
                    $friendship_data = [
                        'none' => ['count' => (int)$counts[0], 'label' => 'No Friends'],
                        '1' => ['count' => (int)$counts[1], 'label' => '1-Way Friend'],
                        '2' => ['count' => (int)$counts[2], 'label' => '2-Way Friends']
                    ];
                    
                    // Sort by count (largest first)
                    uasort($friendship_data, function($a, $b) {
                        return $b['count'] - $a['count'];
                    });
                    
                    // Build options array sorted by largest occurrence first
                    foreach ($friendship_data as $key => $data) {
                        if ($data['count'] > 0) { // Only include non-zero counts
                            $options[$key] = $data['label'] . ' (' . $data['count'] . ')';
                        }
                    }
                    
                    // Also add any zero counts at the end
                    foreach ($friendship_data as $key => $data) {
                        if ($data['count'] == 0) {
                            $options[$key] = $data['label'] . ' (' . $data['count'] . ')';
                        }
                    }
                    
                    return $options;
                }
            }
        }
        
        // Fallback to default options if no wins data found
        return [
            'all' => 'ALL',
            'none' => '0 Friends',
            '1' => '1-Way',
            '2' => '2-Way'
        ];
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
	 * Retrieves and parses H-W-C data with count and rank for prediction futures dropdown.
	 * Returns dropdown array sorted by count (descending) with format: 'h-w-c' => 'h-w-c (count|#N ranked)'
	 *
	 * @param int $lottery_id The ID of the lottery.
	 * @return array Dropdown options array for H-W-C with count and rank
	 */
	public function get_h_w_c_range_with_rank($lottery_id)
	{
		// Get the H-W-C range data (count data)
		$this->db->select('h_w_c_range');
		$this->db->from('lottery_h_w_c_stats');
		$this->db->where('lottery_id', $lottery_id);
		$row = $this->db->get()->row();
		
		$hwc_counts = [];
		if ($row && !empty($row->h_w_c_range)) {
			$items = explode(',', $row->h_w_c_range);
			foreach ($items as $item) {
				$parts = explode('=', $item);
				if (count($parts) == 2) {
					$label = trim($parts[0]);
					$total = (int)trim($parts[1]);
					if ($total > 0) { // Only include if total > 0
						$hwc_counts[$label] = $total;
					}
				}
			}
		}
		
		// Get the wins data to calculate points for ranking
		$this->db->select('wins');
		$this->db->from('lottery_h_w_c_stats');
		$this->db->where('lottery_id', $lottery_id);
		$wins_row = $this->db->get()->row();
		
		$hwc_points = [];
		if ($wins_row && !empty($wins_row->wins)) {
			$hwc_points = $this->parse_hwc_points($wins_row->wins, $lottery_id);
		}
		
		// Calculate ranks based on points (highest points = rank #1)
		// Each pattern gets a unique rank, even when points are tied
		$hwc_ranks = [];
		if (!empty($hwc_points)) {
			// Points are already sorted by parse_hwc_points with tie-breaking
			$rank = 1;
			
			foreach ($hwc_points as $pattern => $points) {
				$hwc_ranks[$pattern] = $rank;
				$rank++; // Each pattern gets a unique sequential rank
			}
		}
		
		// Sort hwc_counts by count descending (most frequent first)
		arsort($hwc_counts);
		
		// Start with patterns that have occurrence counts (these definitely occurred)
		$result = [];
		
		// First pass: Add ranked patterns (those with wins)
		$ranked_patterns = [];
		foreach ($hwc_counts as $pattern => $count) {
			if ($count > 0 && isset($hwc_ranks[$pattern])) {
				$ranked_patterns[$pattern] = [
					'count' => $count,
					'original_rank' => $hwc_ranks[$pattern],
					'points' => isset($hwc_points[$pattern]) ? $hwc_points[$pattern] : 0
				];
			}
		}
		
		// Re-assign sequential ranks starting from 1 for ranked patterns
		$new_rank = 1;
		foreach ($hwc_points as $pattern => $points) {
			if (isset($ranked_patterns[$pattern])) {
				$count = $ranked_patterns[$pattern]['count'];
				$result[$pattern] = $pattern . ' (' . $count . ') - Rank #' . $new_rank;
				$new_rank++;
			}
		}
		
		// Second pass: Add unranked patterns (those with occurrences but no wins)
		// These are shown at the end without a rank
		foreach ($hwc_counts as $pattern => $count) {
			if ($count > 0 && !isset($hwc_ranks[$pattern])) {
				$result[$pattern] = $pattern . ' (' . $count . ') - No wins';
			}
		}
		
		return $result;
	}

	/**
	 * Parse wins string to calculate points for each H-W-C pattern
	 *
	 * @param string $wins_string The wins string from lottery_h_w_c_stats
	 * @param int $lottery_id The lottery ID to get prize profile
	 * @return array Array of H-W-C pattern => points
	 */
	private function parse_hwc_points($wins_string, $lottery_id)
	{
		// Get prize profile for this lottery to determine point values
		$this->load->model('statistics_m');
		$prize_profile = $this->statistics_m->get_lottery_prize_profile($lottery_id);
		if(empty($prize_profile)) {
			return [];
		}
		
		// Define point system based on follower wins
		$category_points = array(
			'extra' => 1,		'2_win' => 4,		'2_win_extra' => 5,
			'3_win' => 6,		'3_win_extra' => 7,	'4_win' => 8,
			'4_win_extra' => 9,	'5_win' => 10,		'5_win_extra' => 11,
			'6_win' => 12,		'6_win_extra' => 13,'7_win' => 14,
			'7_win_extra' => 15,'8_win' => 16,		'8_win_extra' => 17,
			'9_win' => 18,		'9_win_extra' => 19
		);
		
		$hwc_points = [];
		$hwc_entries = explode('|', $wins_string);
		
		foreach($hwc_entries as $entry) {
			if(empty($entry)) continue;
			
			$parts = explode('=', $entry);
			if(count($parts) != 2) continue;
			
			$hwc_pattern = $parts[0];  
			$win_counts = $parts[1];   
			$counts = explode(',', $win_counts);
			
			// Get enabled prize categories (matching History controller logic)
			$enabled_categories = array();
			$category_index = 0;
			
			foreach($prize_profile as $category => $enabled) {
				if($enabled == 1 && $category != 'lottery_id' && $category != 'id') {
					$enabled_categories[$category_index] = $category;
					$category_index++;
				}
			}
			
			// Calculate total points and track highest category for tie-breaking
			$total_points = 0;
			$highest_category_points = 0;
			$win_breakdown = [];
			
			foreach($counts as $index => $count) {
				$count = intval($count);
				if($count > 0 && isset($enabled_categories[$index])) {
					$category = $enabled_categories[$index];
					if(isset($category_points[$category])) {
						$points_per_win = $category_points[$category];
						$total_points += $count * $points_per_win;
						$win_breakdown[$category] = $count;
						
						// Track highest category for tie-breaking
						if($points_per_win > $highest_category_points) {
							$highest_category_points = $points_per_win;
						}
					}
				}
			}
			
			// Only include patterns with points > 0 (matching History controller logic)
			if($total_points > 0) {
				$hwc_points[$hwc_pattern] = [
					'total_points' => $total_points,
					'highest_category' => $highest_category_points,
					'win_breakdown' => $win_breakdown
				];
			}
		}
		
		// Sort by total points first, then by highest category for tie-breaking
		uasort($hwc_points, function($a, $b) {
			// Primary sort: by total points (descending)
			if($a['total_points'] != $b['total_points']) {
				return $b['total_points'] - $a['total_points'];
			}
			
			// Secondary sort: by highest category points (descending) for tie-breaking
			return $b['highest_category'] - $a['highest_category'];
		});
		
		// Convert back to simple points array for compatibility with existing code
		$points_only = [];
		foreach($hwc_points as $pattern => $data) {
			$points_only[$pattern] = $data['total_points'];
		}
		
		return $points_only;
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
			$ball_key = 'ball' . $i;
			if (isset($last_drawn[$ball_key])) {
				$ball_number = $last_drawn[$ball_key];
				$total_key = 'ball' . $i . '_total';
				$points = isset($last_drawn[$total_key]) ? $last_drawn[$total_key] : 0;
				$ball_points[$ball_number] = $points;
			}
		}
		// Extra ball (if exists) - now include for both duplicate and non-duplicate lotteries
		if (isset($last_drawn['extra']) && !empty($last_drawn['extra'])) {
			$extra_number = $last_drawn['extra'];
			$extra_points = isset($last_drawn['extra_total']) ? $last_drawn['extra_total'] : 0;
			
			$ball_points['+' . $extra_number] = $extra_points;
		}
		// Sort by points descending, then by number ascending for same points
		uksort($ball_points, function($a, $b) use ($ball_points) {
			// First compare by points (descending)
			$points_diff = $ball_points[$b] - $ball_points[$a];
			if ($points_diff != 0) {
				return $points_diff;
			}
			// If points are equal, sort by number (ascending)
			// Handle extra ball format (+number)
			$num_a = (strpos($a, '+') === 0) ? intval(substr($a, 1)) : intval($a);
			$num_b = (strpos($b, '+') === 0) ? intval(substr($b, 1)) : intval($b);
			return $num_a - $num_b;
		});
		// Build dropdown array: 0 => '7 (115)', 1 => '34 (83)', ...
		$result = [];
		foreach ($ball_points as $number => $points) {
			$result[] = $number . ' (' . $points . ')';
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
	 * Generate H-W-C predictions directly from raw hot/warm/cold strings.
	 * Used during recalc to produce "previous" predictions — i.e., what H-W-C would have
	 * suggested for the last draw — using hots_last/warms_last/colds_last (H-W-C data
	 * calculated excluding the last draw). Does not require a DB lookup for ball data.
	 *
	 * @param string $hots_str        Comma-separated "ball=heat" pairs for hot numbers
	 * @param string $warms_str       Comma-separated "ball=heat" pairs for warm numbers
	 * @param string $colds_str       Comma-separated "ball=heat" pairs for cold numbers
	 * @param int    $h_count         Size of the hot pool (used for boundary awareness only)
	 * @param int    $w_count         Size of the warm pool
	 * @param int    $c_count         Size of the cold pool
	 * @param int    $combination_size Total numbers to select
	 * @param string $h_w_c           H-W-C pattern string, e.g. "3-3-3 (17)"
	 * @return string|FALSE           Comma-separated selected numbers, or FALSE on failure
	 */
	public function hwc_only_from_strings($hots_str, $warms_str, $colds_str, $h_count, $w_count, $c_count, $combination_size, $h_w_c)
	{
		if (!preg_match('/(\d+)-(\d+)-(\d+)/', $h_w_c, $matches)) {
			return FALSE;
		}
		$h = (int)$matches[1];
		$w = (int)$matches[2];
		$c = (int)$matches[3];
		$total = $h + $w + $c;
		if ($total == 0) return FALSE;

		$h_total = round(($h / $total) * $combination_size);
		$w_total = round(($w / $total) * $combination_size);
		$c_total = $combination_size - $h_total - $w_total;

		// Parse each string into ordered arrays of ball numbers (highest heat first)
		$hots  = $this->parse_hwc_numbers($hots_str);
		$warms = $this->parse_hwc_numbers($warms_str);
		$colds = $this->parse_hwc_numbers($colds_str);

		if (empty($hots) && empty($warms) && empty($colds)) return FALSE;

		// Select the required count from each group (front of list = highest heat)
		$selected = array_merge(
			array_slice($hots,  0, max(0, $h_total)),
			array_slice($warms, 0, max(0, $w_total)),
			array_slice($colds, 0, max(0, $c_total))
		);

		if (empty($selected)) return FALSE;
		return implode(',', $selected);
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
		// Start performance timer for optimization tracking
		$start_time = microtime(true);
		
		// 1. Parse H-W-C group (e.g., "3-3-3 (17)") - optimized regex
		if (!preg_match('/(\d+)-(\d+)-(\d+)/', $h_w_c, $matches)) {
			log_message('error', "Invalid H-W-C format: $h_w_c");
			return FALSE;
		}
		
		$h = (int)$matches[1];
		$w = (int)$matches[2];
		$c = (int)$matches[3];
		
		// 2. Calculate scaled totals for combination size - optimized calculation
		$total = $h + $w + $c;
		if ($total == 0) {
			log_message('error', "Invalid H-W-C totals: $h-$w-$c");
			return FALSE;
		}
		
		$h_total = round(($h / $total) * $combination_size);
		$w_total = round(($w / $total) * $combination_size);
		$c_total = $combination_size - $h_total - $w_total; // Ensure total matches
		
		// 3. Get HWC data from DB with optimized caching
		$hwc_data = $this->get_cached_hwc_data($lottery_id);
		if (!$hwc_data) {
			log_message('error', "H-W-C data not found for lottery $lottery_id");
			return FALSE;
		}
		
		// 4. Extract pre-parsed data from cache
		$hots = $hwc_data['hots'];
		$warms = $hwc_data['warms'];
		$colds = $hwc_data['colds'];
		$h_positions = $hwc_data['h_positions'];
		$w_positions = $hwc_data['w_positions'];
		$c_positions = $hwc_data['c_positions'];
		$position_stats = isset($hwc_data['position_stats']) ? $hwc_data['position_stats'] : null;
		
		// 5. PHASE 2 ENHANCED: Select numbers using intelligent win rate sorting
		$selected = [];
		$selected = array_merge($selected, $this->select_by_position_index_optimized($h_positions, $hots, $h_total, 'hot', $position_stats));
		$selected = array_merge($selected, $this->select_by_position_index_optimized($w_positions, $warms, $w_total, 'warm', $position_stats));
		$selected = array_merge($selected, $this->select_by_position_index_optimized($c_positions, $colds, $c_total, 'cold', $position_stats));

		$elapsed = microtime(true) - $start_time;
		log_message('info', "H-W-C generation completed in " . round($elapsed * 1000, 2) . "ms for lottery $lottery_id");
		
		return implode(',', $selected);
	}
	/**
	 * PHASE 2 ENHANCED: Optimized selection with intelligent win rate sorting
	 * Uses position statistics to select best-performing positions instead of just highest occurrence
	 */
	private function select_by_position_index_optimized($positions, $numbers, $limit, $temperature = '', $position_stats = null) {
		if ($limit <= 0) return [];
		
		// Build array of positions with their performance data
		$position_data = [];
		foreach ($positions as $pos => $count) {
			if (isset($numbers[$pos])) {
				$position_data[] = [
					'position' => $pos,
					'number' => $numbers[$pos],
					'count' => $count,
					'win_rate' => $this->get_position_win_rate_predictions($pos, $temperature, $position_stats)
				];
			}
		}
		
		// PHASE 2: Sort by win rate (descending), then by count (descending) as tiebreaker
		usort($position_data, function($a, $b) {
			// Primary sort: win_rate descending (higher is better)
			if ($b['win_rate'] != $a['win_rate']) {
				return $b['win_rate'] <=> $a['win_rate'];
			}
			// Tiebreaker: count descending (more occurrences)
			return $b['count'] <=> $a['count'];
		});
		
		// Select top N positions by performance
		$selected = [];
		$selected_lookup = []; // Use array for faster duplicate checking
		
		foreach ($position_data as $data) {
			if (!isset($selected_lookup[$data['number']])) {
				$selected[] = $data['number'];
				$selected_lookup[$data['number']] = true;
				if (count($selected) >= $limit) break;
			}
		}
		
		return $selected;
	}
	
	/**
	 * Get cached H-W-C data with optimized parsing and caching
	 */
	private function get_cached_hwc_data($lottery_id) {
		// Check static cache first
		static $hwc_cache = [];
		$cache_key = "hwc_data_$lottery_id";
		
		if (isset($hwc_cache[$cache_key])) {
			return $hwc_cache[$cache_key];
		}
		
		// Get raw data from database
		$hwc = $this->statistics_m->h_w_c_exists($lottery_id);
		$position_row = $this->statistics_m->hwc_history_exists($lottery_id);
		
		if (!$hwc || !$position_row || empty($position_row['position'])) {
			return FALSE;
		}
		
		// Parse and cache the data
		$parsed_data = [
			'hots' => $this->parse_hwc_numbers($hwc['hots']),
			'warms' => $this->parse_hwc_numbers($hwc['warms']),
			'colds' => $this->parse_hwc_numbers($hwc['colds']),
			'h_count' => $hwc['h_count'],
			'w_count' => $hwc['w_count'],
			'c_count' => $hwc['c_count'],
		];
		
		// Parse positions efficiently
		$parts = explode('|', $position_row['position']);
		$parsed_data['h_positions'] = $this->parse_position_part_optimized($parts[0]);
		$parsed_data['w_positions'] = $this->parse_position_part_optimized($parts[1]);
		$parsed_data['c_positions'] = $this->parse_position_part_optimized($parts[2]);
		
		// PHASE 2 ENHANCEMENT: Load position statistics for intelligent selection
		$parsed_data['position_stats'] = $this->load_position_statistics($lottery_id);
		
		// Cache the parsed data
		$hwc_cache[$cache_key] = $parsed_data;
		
		// Prevent memory bloat - keep only last 5 lotteries in cache
		if (count($hwc_cache) > 5) {
			$hwc_cache = array_slice($hwc_cache, -5, 5, true);
		}
		
		return $parsed_data;
	}
	
	/**
	 * Load position statistics from database for intelligent selection
	 * PHASE 2: Retrieves historical position performance data
	 */
	private function load_position_statistics($lottery_id) {
		$this->db->where('lottery_id', $lottery_id);
		$query = $this->db->get('lottery_h_w_c_stats');
		
		if ($query->num_rows() == 0) {
			return array(); // Cold start
		}
		
		$result = $query->row();
		
		if (empty($result->position_stats)) {
			return array(); // No statistics yet
		}
		
		return $this->parse_position_statistics($result->position_stats);
	}
	
	/**
	 * Parse position statistics string from database
	 * PHASE 2: Decodes position win tracking data
	 */
	private function parse_position_statistics($position_stats_string) {
		if (empty($position_stats_string)) {
			return array();
		}
		
		$position_stats = array();
		$pattern_parts = explode('||', $position_stats_string);
		
		foreach ($pattern_parts as $pattern_part) {
			if (empty($pattern_part)) continue;
			
			$parts = explode('>', $pattern_part);
			if (count($parts) != 2) continue;
			
			$pattern = $parts[0];
			$temp_data = $parts[1];
			
			$position_stats[$pattern] = array('hot' => array(), 'warm' => array(), 'cold' => array());
			
			$temp_parts = explode('|', $temp_data);
			foreach ($temp_parts as $temp_part) {
				if (empty($temp_part)) continue;
				
				$temp_split = explode(':', $temp_part);
				if (count($temp_split) != 2) continue;
				
				$temp_code = $temp_split[0];
				$pos_data = $temp_split[1];
				
				$temp_map = array('H' => 'hot', 'W' => 'warm', 'C' => 'cold');
				if (!isset($temp_map[$temp_code])) continue;
				$temp_name = $temp_map[$temp_code];
				
				$position_values = explode(',', $pos_data);
				foreach ($position_values as $pos_value) {
					$pos_parts = explode('=', $pos_value);
					if (count($pos_parts) != 2) continue;
					
					$pos = (int)$pos_parts[0];
					$counts = explode('/', $pos_parts[1]);
					if (count($counts) != 2) continue;
					
					$position_stats[$pattern][$temp_name][$pos] = array(
						'selected' => (int)$counts[0],
						'won' => (int)$counts[1]
					);
				}
			}
		}
		
		return $position_stats;
	}
	
	/**
	 * Get win rate for a specific position
	 * PHASE 2: Calculates performance metric for intelligent selection
	 */
	private function get_position_win_rate_predictions($position, $temperature, $position_stats) {
		// Cold start: No statistics available yet
		if (empty($position_stats)) {
			return -1; // Negative indicates no data - will sort by count
		}
		
		// Aggregate win rates across all patterns for this position/temperature
		$total_selected = 0;
		$total_won = 0;
		
		foreach ($position_stats as $pattern => $temps) {
			if (isset($temps[$temperature][$position])) {
				$stats = $temps[$temperature][$position];
				$total_selected += $stats['selected'];
				$total_won += $stats['won'];
			}
		}
		
		// Minimum sample size: 10 selections before using win rate
		if ($total_selected < 10) {
			return -1; // Insufficient data - will sort by count
		}
		
		// Calculate win rate
		return $total_won / $total_selected;
	}
	
	/**
	 * Optimized parsing of H-W-C numbers (removes counts, keeps order)
	 */
	private function parse_hwc_numbers($hwc_string) {
		if (empty($hwc_string)) return [];
		
		// Use more efficient parsing - split once and extract numbers
		$pairs = explode(',', $hwc_string);
		$numbers = [];
		foreach ($pairs as $pair) {
			$eq_pos = strpos($pair, '=');
			if ($eq_pos !== false) {
				$numbers[] = (int)substr($pair, 0, $eq_pos);
			}
		}
		return $numbers;
	}
	
	/**
	 * Optimized version of parse_position_part
	 */
	private function parse_position_part_optimized($str) {
		// Remove prefix more efficiently
		$str = preg_replace('/^[HWC]>/', '', $str);
		if (empty($str)) return [];
		
		$pairs = explode(',', $str);
		$arr = [];
		foreach ($pairs as $pair) {
			$eq_pos = strpos($pair, '=');
			if ($eq_pos !== false) {
				$pos = (int)substr($pair, 0, $eq_pos);
				$count = (int)substr($pair, $eq_pos + 1);
				$arr[$pos] = $count;
			}
		}
		return $arr;
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
		// Start performance timer for optimization tracking
		$start_time = microtime(true);
		
		// Get followers and non-followers data from statistics_m (now cached)
		$followers_row = $this->statistics_m->followers_exists($lottery_id);
		$nonfollowers_row = $this->statistics_m->nonfollowers_exists($lottery_id);
		if (!$followers_row) {
			return FALSE;
		}
		$followers_field = $followers_row['lottery_followers'];
		$nonfollowers_field = $nonfollowers_row ? $nonfollowers_row['lottery_nonfollowers'] : '';
		
		// Cache key for parsed followers data
		$cache_key = "followers_parsed_{$lottery_id}_{$type}_{$select}";
		static $followers_cache = [];
		
		if (!isset($followers_cache[$cache_key])) {
			// For ball_after, strip '+' if present (extra ball)
			$select = trim($select);
			if ($type === 'after_ball' && strpos($select, '+') === 0) {
				$select = substr($select, 1);
			}
			
			// Pre-split the data to avoid repeated string operations
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
				// Find the group for the selected ball (e.g., "34>") - optimized search
				$search_prefix = $select . '>';
				foreach ($followers_groups as $group) {
					if (strpos($group, $search_prefix) === 0) {
						$selected_followers = substr($group, strlen($search_prefix));
						break;
					}
				}
				foreach ($nonfollowers_groups as $group) {
					if (strpos($group, $search_prefix) === 0) {
						$selected_nonfollowers = substr($group, strlen($search_prefix));
						break;
					}
				}
			}
			
			if ($selected_followers === '') {
				return FALSE;
			}
			
			// Parse and cache the groups data
			$groups = $this->parse_followers_groups_optimized($selected_followers, $selected_nonfollowers);
			$followers_cache[$cache_key] = $groups;
			
			// Prevent memory bloat - keep only last 10 entries
			if (count($followers_cache) > 10) {
				$followers_cache = array_slice($followers_cache, -10, 10, true);
			}
		} else {
			$groups = $followers_cache[$cache_key];
		}
		
		// Early validation: Check if we have enough numbers
		$total_numbers = 0;
		foreach ($groups as $nums) {
			$total_numbers += count($nums);
		}
		
		if ($total_numbers == 0 || $total_numbers < $combination_size) {
			if ($total_numbers < $combination_size) {
				log_message('error', "followers_only: Insufficient follower numbers for lottery $lottery_id - need $combination_size, have $total_numbers");
			}
			return FALSE;
		}
		
		// Optimized selection algorithm
		$selected = $this->select_followers_numbers_optimized($groups, $combination_size);
		
		$elapsed = microtime(true) - $start_time;
		log_message('info', "Followers generation completed in " . round($elapsed * 1000, 2) . "ms for lottery $lottery_id");
		
		return implode(',', $selected);
	}
	
	/**
	 * Optimized parsing of followers groups
	 */
	private function parse_followers_groups_optimized($selected_followers, $selected_nonfollowers) {
		// Parse followers into dynamic groups by weight
		$follower_numbers = explode('|', $selected_followers);
		$groups = [];
		
		foreach ($follower_numbers as $item) {
			$eq_pos = strpos($item, '=');
			if ($eq_pos !== false) {
				$num = trim(substr($item, 0, $eq_pos));
				$weight = (int)substr($item, $eq_pos + 1);
				
				// Only add valid numbers (not empty, not 0, and numeric)
				if ($num !== '' && $num !== '0' && is_numeric($num) && intval($num) > 0) {
					if (!isset($groups[$weight])) {
						$groups[$weight] = [];
					}
					$groups[$weight][] = $num;
				}
			}
		}
		
		// Parse non-followers group (0 group)
		if (!empty($selected_nonfollowers)) {
			$nonfollower_numbers = array_filter(array_map('trim', explode('|', $selected_nonfollowers)));
			$groups[0] = array_filter($nonfollower_numbers, function($num) {
				return $num !== '' && $num !== '0' && is_numeric($num) && intval($num) > 0;
			});
		}
		
		// Sort groups by weight descending (so highest group first)
		krsort($groups);
		return $groups;
	}
	
	/**
	 * Optimized selection of numbers from followers groups
	 */
	private function select_followers_numbers_optimized($groups, $combination_size) {
		$total_numbers = 0;
		foreach ($groups as $nums) {
			$total_numbers += count($nums);
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
		
		// Distribute remaining picks proportionally with optimized calculation
		// Distribute remaining picks proportionally with optimized calculation
		if ($remaining > 0) {
			foreach ($groups as $weight => $nums) {
				if ($remaining <= 0) break;
				$extra = max(1, round((count($nums) / $total_numbers) * $remaining));
				$to_add = min($extra, count($nums) - $picks[$weight], $remaining);
				$picks[$weight] += $to_add;
				$remaining -= $to_add;
			}
			
			// If still remaining, fill in order with safety check - optimized loop
			while ($remaining > 0) {
				$progress_made = false;
				foreach ($groups as $weight => $nums) {
					if ($remaining > 0 && $picks[$weight] < count($nums)) {
						$picks[$weight]++;
						$remaining--;
						$progress_made = true;
					}
				}
				
				// If no progress was made, we've exhausted all available numbers
				if (!$progress_made) {
					log_message('error', "followers_only: Insufficient follower numbers - needed $combination_size, available " . ($combination_size - $remaining));
					break;
				}
			}
		}
		
		// Select numbers from each group (first N) - optimized selection
		$selected = [];
		foreach ($groups as $weight => $nums) {
			if ($picks[$weight] > 0) {
				$selected_from_group = array_slice($nums, 0, $picks[$weight]);
				$selected = array_merge($selected, $selected_from_group);
			}
		}
		
		// Final safety check - if not enough numbers, fill from any remaining
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
		
		return $selected;
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
		// Start performance timer for optimization tracking
		$start_time = microtime(true);
		
		// 1. Parse H-W-C group (e.g., "4-3-3") - optimized regex
		if (empty($h_w_c) || !preg_match('/(\d+)-(\d+)-(\d+)/', $h_w_c, $matches)) {
			return FALSE;
		}
		
		$h = (int)$matches[1];
		$w = (int)$matches[2];
		$c = (int)$matches[3];
		
		// 2. Calculate scaled totals for combination size
		$total = $h + $w + $c;
		if ($total == 0) {
			return FALSE;
		}
		
		$h_total = round(($h / $total) * $combination_size);
		$w_total = round(($w / $total) * $combination_size);
		$c_total = $combination_size - $h_total - $w_total;
		
		// 3. Get HWC data using optimized cached method
		$hwc_data = $this->get_cached_hwc_data($lottery_id);
		if (!$hwc_data) {
			log_message('error', "H-W-C data not found for lottery $lottery_id");
			return FALSE;
		}
		
		// 4. Get followers data using optimized cached method
		$cache_key = "hwc_followers_parsed_{$lottery_id}_{$follower_type}_{$follower_select}";
		static $hwc_followers_cache = [];
		
		if (!isset($hwc_followers_cache[$cache_key])) {
			$followers_row = $this->statistics_m->followers_exists($lottery_id);
			$nonfollowers_row = $this->statistics_m->nonfollowers_exists($lottery_id);
			if (!$followers_row) {
				return FALSE;
			}
			
			// Parse followers data once and cache it
			$followers_data = $this->parse_hwc_followers_data($followers_row, $nonfollowers_row, $follower_type, $follower_select);
			$hwc_followers_cache[$cache_key] = $followers_data;
			
			// Prevent memory bloat
			if (count($hwc_followers_cache) > 10) {
				$hwc_followers_cache = array_slice($hwc_followers_cache, -10, 10, true);
			}
		} else {
			$followers_data = $hwc_followers_cache[$cache_key];
		}
		
		if (!$followers_data) {
			return FALSE;
		}
		
		// 5. Select numbers using optimized algorithm
		$selected = $this->select_hwc_followers_numbers_optimized(
			$hwc_data, 
			$followers_data, 
			$h_total, 
			$w_total, 
			$c_total, 
			$combination_size
		);
		
		$elapsed = microtime(true) - $start_time;
		log_message('info', "H-W-C + Followers generation completed in " . round($elapsed * 1000, 2) . "ms for lottery $lottery_id");
		
		return implode(',', $selected);
	}
	
	/**
	 * Optimized parsing of H-W-C followers data
	 */
	private function parse_hwc_followers_data($followers_row, $nonfollowers_row, $follower_type, $follower_select) {
		$followers_field = $followers_row['lottery_followers'];
		$nonfollowers_field = $nonfollowers_row ? $nonfollowers_row['lottery_nonfollowers'] : '';
		
		// For ball_after, strip '+' if present (extra ball)
		$follower_select = trim($follower_select);
		if ($follower_type === 'after_ball' && strpos($follower_select, '+') === 0) {
			$follower_select = substr($follower_select, 1);
		}
		
		$followers_groups = explode(',', $followers_field);
		$nonfollowers_groups = $nonfollowers_field ? explode(',', $nonfollowers_field) : [];
		
		$selected_followers = '';
		$selected_nonfollowers = '';
		
		if ($follower_type === 'position') {
			$position = (int)$follower_select;
			if (isset($followers_groups[$position - 1])) {
				$group = $followers_groups[$position - 1];
				$selected_followers = substr($group, strpos($group, '>') + 1);
			}
			if (isset($nonfollowers_groups[$position - 1])) {
				$group = $nonfollowers_groups[$position - 1];
				$selected_nonfollowers = substr($group, strpos($group, '>') + 1);
			}
		} else {
			$search_prefix = $follower_select . '>';
			foreach ($followers_groups as $group) {
				if (strpos($group, $search_prefix) === 0) {
					$selected_followers = substr($group, strlen($search_prefix));
					break;
				}
			}
			foreach ($nonfollowers_groups as $group) {
				if (strpos($group, $search_prefix) === 0) {
					$selected_nonfollowers = substr($group, strlen($search_prefix));
					break;
				}
			}
		}
		
		if ($selected_followers === '') {
			return FALSE;
		}
		
		// Parse into usable format
		return $this->parse_followers_groups_optimized($selected_followers, $selected_nonfollowers);
	}
	
	/**
	 * PHASE 2 ENHANCED: Optimized selection algorithm for H-W-C + Followers with intelligent win rate sorting
	 */
	private function select_hwc_followers_numbers_optimized($hwc_data, $followers_data, $h_total, $w_total, $c_total, $combination_size) {
		// Create list of all follower numbers for quick lookup
		$all_follower_numbers = [];
		foreach ($followers_data as $nums) {
			$all_follower_numbers = array_merge($all_follower_numbers, $nums);
		}
		$follower_lookup = array_flip($all_follower_numbers);
		
		// Extract pre-parsed H-W-C data
		$hots = $hwc_data['hots'];
		$warms = $hwc_data['warms'];
		$colds = $hwc_data['colds'];
		$h_positions = $hwc_data['h_positions'];
		$w_positions = $hwc_data['w_positions'];
		$c_positions = $hwc_data['c_positions'];
		$position_stats = isset($hwc_data['position_stats']) ? $hwc_data['position_stats'] : null;
		
		// PHASE 2: Select from each H-W-C group using intelligent selection, then filter by followers
		$selected = [];
		
		// Select hots that are in followers
		$selected_hots = $this->select_hwc_filtered_by_followers($h_positions, $hots, $h_total, $follower_lookup, 'hot', $position_stats);
		$selected = array_merge($selected, $selected_hots);
		
		// Select warms that are in followers
		$selected_warms = $this->select_hwc_filtered_by_followers($w_positions, $warms, $w_total, $follower_lookup, 'warm', $position_stats);
		$selected = array_merge($selected, $selected_warms);
		
		// Select colds that are in followers
		$selected_colds = $this->select_hwc_filtered_by_followers($c_positions, $colds, $c_total, $follower_lookup, 'cold', $position_stats);
		$selected = array_merge($selected, $selected_colds);
		
		// If not enough numbers, fill from followers data
		if (count($selected) < $combination_size) {
			$remaining = $combination_size - count($selected);
			foreach ($followers_data as $nums) {
				foreach ($nums as $num) {
					if (!in_array($num, $selected) && $remaining > 0) {
						$selected[] = $num;
						$remaining--;
					}
					if ($remaining == 0) break 2;
				}
			}
		}
		
		return $selected;
	}
	
	/**
	 * PHASE 2 ENHANCED: Helper method to select H-W-C numbers filtered by followers with intelligent win rate sorting
	 */
	private function select_hwc_filtered_by_followers($positions, $numbers, $limit, $follower_lookup, $temperature = '', $position_stats = null) {
		if ($limit <= 0) return [];
		
		// Build array of positions with their performance data
		$position_data = [];
		foreach ($positions as $pos => $count) {
			if (isset($numbers[$pos])) {
				$position_data[] = [
					'position' => $pos,
					'number' => $numbers[$pos],
					'count' => $count,
					'win_rate' => $this->get_position_win_rate_predictions($pos, $temperature, $position_stats),
					'is_follower' => isset($follower_lookup[$numbers[$pos]])
				];
			}
		}
		
		// PHASE 2: Sort by follower status first, then by win rate, then by count
		usort($position_data, function($a, $b) {
			// Primary: Followers first
			if ($a['is_follower'] != $b['is_follower']) {
				return $b['is_follower'] - $a['is_follower'];
			}
			// Secondary: win_rate descending (higher is better)
			if ($b['win_rate'] != $a['win_rate']) {
				return $b['win_rate'] <=> $a['win_rate'];
			}
			// Tiebreaker: count descending
			return $b['count'] <=> $a['count'];
		});
		
		// Select followers first, then non-followers if quota is not yet reached
		$selected = [];
		$selected_lookup = [];
		
		// First pass: followers only
		foreach ($position_data as $data) {
			if ($data['is_follower'] && !isset($selected_lookup[$data['number']])) {
				$selected[] = $data['number'];
				$selected_lookup[$data['number']] = true;
				if (count($selected) >= $limit) break;
			}
		}
		
		// Second pass: fill remaining quota from non-followers in same heat group
		if (count($selected) < $limit) {
			foreach ($position_data as $data) {
				if (!$data['is_follower'] && !isset($selected_lookup[$data['number']])) {
					$selected[] = $data['number'];
					$selected_lookup[$data['number']] = true;
					if (count($selected) >= $limit) break;
				}
			}
		}
		
		return $selected;
	}
	
	/**
	 * Remove or allow friends in $selections with friendship status information.
	 * @param int $lottery_id		Lottery/game id
	 * @param array $selections  	Array of selected numbers as strings, e.g. ['1', '2', '3']
	 * @param string $friendship 	'ALL', 'none', '1', or '2'
	 * @param array $heat_map 		['H' => [num => count,...], 'W' => [...], 'C' => [...]]
	 * @return array 				['numbers' => filtered_numbers, 'friendship_status' => status_info]
	 */
	public function friend_search_hwc_with_status($lottery_id, $selections, $friendship, $heat_map)
	{
		// Get the filtered numbers using the existing method
		$filtered_numbers = $this->friend_search_hwc($lottery_id, $selections, $friendship, $heat_map);
		
		// Initialize status information
		$status = [
			'requested_type' => $friendship,
			'found_friendships' => [],
			'warning_message' => null
		];
		
		// Check what friendships actually exist in the final result
		$friendship_analysis = $this->analyze_friendships($lottery_id, $filtered_numbers);
		$status['found_friendships'] = $friendship_analysis;
		
		// Generate appropriate warning message based on what was requested vs found
		if ($friendship === 'none') {
			if ($friendship_analysis['has_1way'] || $friendship_analysis['has_2way']) {
				$status['warning_message'] = 'Warning: Some friendships may still exist in the combination despite selecting "No Friends".';
			}
		} elseif ($friendship === '1') {
			if (!$friendship_analysis['has_1way']) {
				$status['warning_message'] = 'Warning: No 1-way friendships were found in the current combination.';
			}
			if ($friendship_analysis['has_2way']) {
				$status['warning_message'] = 'Warning: Some 2-way friendships may still exist despite selecting "1-way Friends Only".';
			}
		} elseif ($friendship === '2') {
			if (!$friendship_analysis['has_2way']) {
				$status['warning_message'] = 'Warning: No 2-way friendships were found in the current combination.';
			}
			if ($friendship_analysis['has_1way']) {
				$status['warning_message'] = 'Warning: Some 1-way friendships may still exist despite selecting "2-way Friends Only".';
			}
		}
		
		return [
			'numbers' => $filtered_numbers,
			'friendship_status' => $status
		];
	}
	
	/**
	 * Analyze friendships in a given set of numbers.
	 * @param int $lottery_id		Lottery/game id
	 * @param array $numbers  		Array of numbers to analyze
	 * @return array 				Friendship analysis results
	 */
	public function analyze_friendships($lottery_id, $numbers)
	{
		$analysis = [
			'has_1way' => false,
			'has_2way' => false,
			'oneway_pairs' => [],
			'twoway_pairs' => []
		];
		
		// Fetch friendship data
		$row = $this->db->get_where('lottery_friends', ['lottery_id' => $lottery_id])->row_array();
		if (!$row || empty($row['wins'])) {
			return $analysis;
		}
		
		// Parse friendship string (after first '|')
		$parts = explode('|', $row['wins']);
		$friend_str = isset($parts[1]) ? $parts[1] : '';
		if (!$friend_str) {
			return $analysis;
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
		
		$twoway = $this->twoway_unique($twoway);
		
		// Check for 1-way friendships in the current numbers
		foreach ($oneway as $pair) {
			list($a, $b) = $pair;
			if (in_array($a, $numbers) && in_array($b, $numbers)) {
				$analysis['has_1way'] = true;
				$analysis['oneway_pairs'][] = [$a, $b];
			}
		}
		
		// Check for 2-way friendships in the current numbers
		foreach ($twoway as $pair) {
			list($a, $b) = $pair;
			if (in_array($a, $numbers) && in_array($b, $numbers)) {
				$analysis['has_2way'] = true;
				$analysis['twoway_pairs'][] = [$a, $b];
			}
		}
		
		return $analysis;
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
		
		// Always return the result, even if no friendships were found or created
		return $result;
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
					// Ensure the number is valid (not 0, not empty, and numeric)
					if ($num !== '0' && $num !== 0 && $num !== '' && is_numeric($num) && intval($num) > 0 && !in_array($num, $exclude)) {
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
		 * Returns an associative array for hots, warms, and colds:
		 * [
		 *   'H' => [18 => 21, 42 => 15, 13 => 18, ...], // number => position count
		 *   'W' => [...],
		 *   'C' => [...]
		 * ]
		 * Uses numbers from lottery_h_w_c.hots/warms/colds and position counts from lottery_h_w_c_stats.position.
		 *
		 * @param int 		$lottery_id
		 * @return array 	$result
		 */
		public function get_heat_map($lottery_id)
		{
			// Get numbers for hots, warms, colds
			$row_hwc = $this->db->get_where('lottery_h_w_c', ['lottery_id' => $lottery_id])->row_array();
			// Get position counts for hots, warms, colds
			$row_stats = $this->db->get_where('lottery_h_w_c_stats', ['lottery_id' => $lottery_id])->row_array();

			if (!$row_hwc || !$row_stats || empty($row_stats['position'])) {
				return [];
			}
			// Parse numbers for each group (discard counts)
			$groups = ['H' => [], 'W' => [], 'C' => []];
			foreach (['H' => 'hots', 'W' => 'warms', 'C' => 'colds'] as $cat => $field) {
				if (!empty($row_hwc[$field])) {
					$pairs = explode(',', $row_hwc[$field]);
					foreach ($pairs as $pair) {
						$kv = explode('=', $pair);
						if (count($kv) == 2) {
							$num = (int)trim($kv[0]);
							$groups[$cat][] = $num;
						}
					}
				}
			}
			// Parse position counts for each group
			$positions = ['H' => [], 'W' => [], 'C' => []];
			$parts = explode('|', $row_stats['position']);
			foreach ($parts as $part) {
				$part = trim($part);
				if (preg_match('/^(H|W|C)>(.+)$/', $part, $matches)) {
					$cat = $matches[1];
					$pairs = explode(',', $matches[2]);
					foreach ($pairs as $pair) {
						$kv = explode('=', $pair);
						if (count($kv) == 2) {
							$idx = (int)trim($kv[0]);
							$count = (int)trim($kv[1]);
							$positions[$cat][$idx] = $count;
						}
					}
				}
			}
			// Combine: assign each number in group to its position count by index
			$result = ['H' => [], 'W' => [], 'C' => []];
			foreach (['H', 'W', 'C'] as $cat) {
				foreach ($groups[$cat] as $i => $num) {
					// Use the position count at the same index, if it exists
					$count = isset($positions[$cat][$i]) ? $positions[$cat][$i] : null;
					if ($count !== null) {
						$result[$cat][$num] = $count;
					}
				}
			}
		return $result; // returns 
		}
		/**
		 * Returns an associative array of followers (number => count, sorted descending by count)
		 * followed by non-followers (number => 0, in original order).
		 *
		 * @param int $lottery_id
		 * @param string $type 'after_ball' or 'position'
		 * @param string|int $select
		 * @return array
		 */
		public function get_followers_list($lottery_id, $type, $select)
		{
			$followers_row = $this->statistics_m->followers_exists($lottery_id);
			$nonfollowers_row = $this->statistics_m->nonfollowers_exists($lottery_id);
			if (!$followers_row) {
				return [];
			}
			$followers_field = $followers_row['lottery_followers'];
			$nonfollowers_field = $nonfollowers_row ? $nonfollowers_row['lottery_nonfollowers'] : '';
			$select = trim($select);
			if ($type === 'after_ball' && strpos($select, '+') === 0) {
				$select = substr($select, 1);
			}
			$followers_list = [];
			$non_followers_list = [];
			// Followers
			if ($type === 'position') {
				$position = (int)$select;
				$groups = explode(',', $followers_field);
				if (isset($groups[$position - 1])) {
					$group = $groups[$position - 1];
					$data = substr($group, strpos($group, '>') + 1);
					$pairs = array_filter(array_map('trim', explode('|', $data)));
					foreach ($pairs as $pair) {
						$kv = explode('=', $pair);
						if (count($kv) == 2) {
							$num = (int)trim($kv[0]);
							$count = (int)trim($kv[1]);
							if ($num > 0 && $count >= 3) { // Ensure num is valid
								$followers_list[$num] = $count;
							}
						}
					}
				}
				// Non-followers
				$groups = $nonfollowers_field ? explode(',', $nonfollowers_field) : [];
				if (isset($groups[$position - 1])) {
					$group = $groups[$position - 1];
					$data = substr($group, strpos($group, '>') + 1);
					$pairs = array_filter(array_map('trim', explode('|', $data)));
					foreach ($pairs as $pair) {
						$num = (int)trim($pair);
						if ($num > 0) { // Only add valid numbers > 0
							$non_followers_list[$num] = 0;
						}
					}
				}
			} else {
				// after_ball
				$groups = explode(',', $followers_field);
				foreach ($groups as $group) {
					if (strpos($group, $select . '>') === 0) {
						$data = substr($group, strlen($select) + 1);
						$pairs = array_filter(array_map('trim', explode('|', $data)));
						foreach ($pairs as $pair) {
							$kv = explode('=', $pair);
							if (count($kv) == 2) {
								$num = (int)trim($kv[0]);
								$count = (int)trim($kv[1]);
								if ($num > 0 && $count >= 3) { // Ensure num is valid
									$followers_list[$num] = $count;
								}
							}
						}
						break;
					}
				}
				$groups = $nonfollowers_field ? explode(',', $nonfollowers_field) : [];
				foreach ($groups as $group) {
					if (strpos($group, $select . '>') === 0) {
						$data = substr($group, strlen($select) + 1);
						$pairs = array_filter(array_map('trim', explode('|', $data)));
						foreach ($pairs as $pair) {
							$num = (int)trim($pair);
							if ($num > 0) { // Only add valid numbers > 0
								$non_followers_list[$num] = 0;
							}
						}
					}
				}
			}
			// Sort followers by count descending, keep non-followers in original order
			arsort($followers_list);
			// Merge and return
		return $followers_list + $non_followers_list;
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
	public function insert_number_combination($filepath, $number_array, $page = 1, $per_page = 10, $filter_select = [], $start_time = null, $timeout_seconds = 3, $lottery_id = null)
	{
		// Set start time if not provided
		if ($start_time === null) {
			$start_time = microtime(true);
		}
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
		
		// For efficiency, if no filters are applied, use simple file line pagination
		if ($selected_trends === 'ALL' && !$this->has_active_filters($filter_select)) {
			// Simple pagination for unfiltered results
			if (($handle = fopen($filepath, 'r')) !== false) {
				while (($line = fgets($handle)) !== false) {
					// Check for timeout every 1000 lines to avoid excessive overhead
					if ($line_count % 1000 === 0 && $start_time !== null) {
						$elapsed = microtime(true) - $start_time;
						if ($elapsed > $timeout_seconds) {
							fclose($handle);
							// Get CI instance to access controller
							$CI =& get_instance();
							if (method_exists($CI, 'check_timeout_and_redirect')) {
								$CI->check_timeout_and_redirect($start_time, $timeout_seconds, $lottery_id);
							}
							return $result; // Return partial results if timeout
						}
					}
					
					$line = trim($line);
					if (empty($line)) continue;
					
					$line_count++;
					
					// Skip lines for pagination
					if ($line_count <= ($page - 1) * $per_page) {
						continue;
					}
					
					// Stop when we have enough items for this page
					if ($combinations_found >= $per_page) {
						break;
					}
					
					// Parse and process combination
					$positions = array_map('intval', explode(' ', $line));
					$combo_numbers = [];
					$extra_ball_number = null;
					
					// For independent extra ball lotteries, treat last number differently
					if (!empty($filter_select['duplicate_extra_ball']) && !empty($filter_select['extra_ball'])) {
						// Last number is the actual extra ball, keep it as-is
						$extra_ball_number = array_pop($positions);
						
						// Process remaining positions as usual (insert generated numbers)
						foreach ($positions as $pos) {
							if ($pos > 0 && isset($number_array[$pos - 1])) {
								$combo_numbers[] = $number_array[$pos - 1];
							}
						}
					} else {
						// Regular lottery - all positions are for main numbers
						foreach ($positions as $pos) {
							if ($pos > 0 && isset($number_array[$pos - 1])) {
								$combo_numbers[] = $number_array[$pos - 1];
							}
						}
					}
					
					if (empty($combo_numbers)) continue;
					
					sort($combo_numbers, SORT_NUMERIC);
					$combo = [];
					foreach ($combo_numbers as $idx => $num) {
						$combo['ball'.($idx+1)] = $num;
					}
					
					// Add extra ball for independent extra ball lotteries
					if (!empty($filter_select['duplicate_extra_ball']) && !empty($filter_select['extra_ball'])) {
						if ($extra_ball_number !== null) {
							// Use the actual extra ball number from the combination file
							$combo['extra'] = $extra_ball_number;
						} else {
							// Fallback to generated extra ball (for backward compatibility)
							$max_ball = $filter_select['max_ball'] ?? 50;
							$combo['extra'] = $this->assign_extra_ball($combo_numbers, $max_ball);
						}
					}
					
					// Create combo data with stats if available
					$combo_data = ['combo' => $combo];
					if (!empty($filter_select) && isset($filter_select['drawn']) && isset($filter_select['lottery_last_drawn'])) {
						$stats = $this->get_combo_stats($combo, $filter_select['drawn'], $filter_select['lottery_last_drawn']);
						$combo_data = array_merge($combo_data, $stats);
					}
					
					$result[] = $combo_data;
					$combinations_found++;
				}
				fclose($handle);
			}
		} else {
			// For filtered results, collect all valid combinations first, then apply pagination
			$filtered_combinations = [];
			
			// Read file line by line and apply filters
			if (($handle = fopen($filepath, 'r')) !== false) {
				while (($line = fgets($handle)) !== false) {
					$line = trim($line);
					if (empty($line)) continue;
					
					// Parse combination
					$positions = array_map('intval', explode(' ', $line));
					$combo_numbers = [];
					$extra_ball_number = null;
					
					// For independent extra ball lotteries, treat last number differently
					if (!empty($filter_select['duplicate_extra_ball']) && !empty($filter_select['extra_ball'])) {
						// Last number is the actual extra ball, keep it as-is
						$extra_ball_number = array_pop($positions);
						
						// Process remaining positions as usual (insert generated numbers)
						foreach ($positions as $pos) {
							// Validate position index
							if ($pos > 0 && isset($number_array[$pos - 1])) {
								$combo_numbers[] = $number_array[$pos - 1];
							}
						}
					} else {
						// Regular lottery - all positions are for main numbers
						foreach ($positions as $pos) {
							// Validate position index
							if ($pos > 0 && isset($number_array[$pos - 1])) {
								$combo_numbers[] = $number_array[$pos - 1];
							}
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
					
					// Add extra ball for independent extra ball lotteries
					if (!empty($filter_select['duplicate_extra_ball']) && !empty($filter_select['extra_ball'])) {
						if ($extra_ball_number !== null) {
							// Use the actual extra ball number from the combination file
							$combo['extra'] = $extra_ball_number;
						} else {
							// Fallback to generated extra ball (for backward compatibility)
							$max_ball = $filter_select['max_ball'] ?? 50;
							$combo['extra'] = $this->assign_extra_ball($combo_numbers, $max_ball);
						}
					}
					
					// Apply trend filter if specified
					if ($selected_trends !== 'ALL') {
						if (!$this->check_trend_match($combo, $last_drawn_numbers, $selected_trends)) {
							continue; // Skip this combination if it doesn't match trend
						}
					}
					
					// Apply other filters
					if (!$this->apply_other_filters($combo, $filter_select)) {
						continue; // Skip this combination if it doesn't pass other filters
					}
					
					// Create combo data with stats if available
					$combo_data = ['combo' => $combo];
					if (!empty($filter_select) && isset($filter_select['drawn']) && isset($filter_select['lottery_last_drawn'])) {
						$stats = $this->get_combo_stats($combo, $filter_select['drawn'], $filter_select['lottery_last_drawn']);
						$combo_data = array_merge($combo_data, $stats);
					}
					
					$filtered_combinations[] = $combo_data;
				}
				fclose($handle);
			}
			
			// Apply pagination to filtered results
			$start_index = ($page - 1) * $per_page;
			$result = array_slice($filtered_combinations, $start_index, $per_page);
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
		if ($filter_select['selected_winning_sums'] !== 'ALL') {
			$combo_sum = array_sum(array_values($combo));
			$winning_sums = is_array($filter_select['selected_winning_sums']) 
				? $filter_select['selected_winning_sums'] 
				: [$filter_select['selected_winning_sums']];
			if (!in_array($combo_sum, $winning_sums)) {
				return false;
			}
		}
		// Filter by repeaters
		if ($filter_select['selected_repeaters'] !== 'ALL') {
			$drawn = $filter_select['drawn'] ?? 0;
			$last_drawn = $filter_select['lottery_last_drawn'] ?? [];
			$repeater_count = $this->is_repeater($combo, $drawn, $last_drawn);
			$expected_repeaters = (int)$filter_select['selected_repeaters'];
			if ($repeater_count !== $expected_repeaters) {
				return false;
			}
		}
		// Filter by consecutive numbers
		if ($filter_select['selected_consecutives'] !== 'ALL') {
			$drawn = $filter_select['drawn'] ?? 0;
			$consecutive_count = $this->has_consecutive($combo, $drawn);
			$expected_consecutives = (int)$filter_select['selected_consecutives'];
			if ($consecutive_count !== $expected_consecutives) {
				return false;
			}
		}
		// Filter by digit sums (selected_winning_digits)
 		if ($filter_select['selected_winning_digits'] !== 'ALL') {
			$drawn = $filter_select['drawn'] ?? 0;
			$combo_digit_sum = $this->statistics_m->lottery_draw_sumdigits($combo, $drawn);
			$selected_digit_sum = (int)$filter_select['selected_winning_digits'];
			if ($combo_digit_sum !== $selected_digit_sum) {
				return false;
			}
		}
		// Filter by odd/even distribution (selected_parity)
		if ($filter_select['selected_parity'] !== 'ALL') {
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
		if ($filter_select['selected_decades'] !== 'ALL') {
			$drawn = $filter_select['drawn'] ?? 0;
			$decade_count = $this->count_decade_numbers($combo, $drawn);
			$expected_decades = (int)$filter_select['selected_decades'];
			
			if ($decade_count !== $expected_decades) {
				return false;
			}
		}
		// Filter by last digits (selected_last_digits)
		if ($filter_select['selected_last_digits'] !== 'ALL') {
			$drawn = $filter_select['drawn'] ?? 0;
			$last_digit_count = $this->count_last_digit_numbers($combo, $drawn);
			$expected_last_digits = (int)$filter_select['selected_last_digits'];
			
			if ($last_digit_count !== $expected_last_digits) {
				return false;
			}
		}
		// Filter by number range (selected_number_range)
		if ($filter_select['selected_number_range'] !== 'ALL') {
			$combo_numbers = array_values($combo);
			$combo_range = !empty($combo_numbers) ? max($combo_numbers) - min($combo_numbers) : 0;
			$expected_range = (int)$filter_select['selected_number_range'];
			if ($combo_range !== $expected_range) {
				return false;
			}
		}
		// Filter by adjacents (selected_adjacents)
		if ($filter_select['selected_adjacents'] !== 'ALL') {
			$ball_position = (int)$filter_select['selected_adjacents'];
			// Calculate the actual difference between the specified adjacent balls
			$actual_difference = $this->calculate_adjacent_difference($combo, $ball_position);
			// For now, we'll get the expected difference from the adjacents data if available
			// This will need to be enhanced to get the expected difference from the lottery highlights
			if ($actual_difference !== null) {
				// The expected difference should come from the adjacents dropdown selection
				// For now, we'll implement a basic version and enhance it as needed
				$expected_difference = $this->get_expected_adjacent_difference($filter_select, $ball_position);
				if ($expected_difference !== null && $actual_difference !== $expected_difference) {
					return false;
				}
			}
		}
		
		// Filter by selected extra ball (for independent extra ball lotteries)
		if (isset($filter_select['selected_extra_ball']) && 
			$filter_select['selected_extra_ball'] !== 'ALL' && 
			!empty($filter_select['duplicate_extra_ball']) && 
			!empty($filter_select['extra_ball'])) {
			
			// Check if this combination has the selected extra ball
			$selected_extra_ball = (int)$filter_select['selected_extra_ball'];
			if (isset($combo['extra']) && (int)$combo['extra'] !== $selected_extra_ball) {
				return false;
			}
		}
		
		// Filter by H-W-C group (Hot-Warm-Cold) - only apply if H-W-C checkbox is checked
		// When checked, H-W-C dropdown has no 'ALL' option - a specific distribution must be selected
		if (isset($filter_select['selected_hwc']) && $filter_select['selected_hwc'] && 
			isset($filter_select['selected_h_w_c_group']) && 
			!empty($filter_select['selected_h_w_c_group'])) {
			
			$h_w_c_group = $filter_select['selected_h_w_c_group'];
			
			// Parse H-W-C group (e.g., "2-2-1" for 2 hot, 2 warm, 1 cold)
			if (preg_match('/(\d+)-(\d+)-(\d+)/', $h_w_c_group, $matches)) {
				$expected_hot = (int)$matches[1];
				$expected_warm = (int)$matches[2];
				$expected_cold = (int)$matches[3];
				
				// Get lottery ID for H-W-C stats
				$lottery_id = $filter_select['lottery_id'] ?? null;
				if ($lottery_id) {
					// Load statistics model if not already loaded
					if (!isset($this->statistics_m)) {
						$this->load->model('Statistics_m', 'statistics_m');
					}
					
					// For independent extra ball lotteries, separate main numbers from extra ball
					$main_numbers = $combo;
					if (!empty($filter_select['duplicate_extra_ball']) && !empty($filter_select['extra_ball']) && isset($combo['extra'])) {
						// Remove extra ball from main numbers for H-W-C calculation
						unset($main_numbers['extra']);
					}
					
					// Get H-W-C classification for each number in the combination (main numbers only)
					$combo_numbers = array_values($main_numbers);
					$hot_count = 0;
					$warm_count = 0;
					$cold_count = 0;
					
					foreach ($combo_numbers as $number) {
						$classification = $this->statistics_m->get_number_hwc_classification($lottery_id, $number);
						
						switch ($classification) {
							case 'hot':
								$hot_count++;
								break;
							case 'warm':
								$warm_count++;
								break;
							case 'cold':
								$cold_count++;
								break;
						}
					}
					
					// Check if the combination matches the expected H-W-C distribution
					if ($hot_count !== $expected_hot || $warm_count !== $expected_warm || $cold_count !== $expected_cold) {
						return false;
					}
				}
			}
		}
		
        // Filter by friendship relationships - only apply if friendship checkbox is checked
        if (isset($filter_select['selected_friends_checkbox']) && $filter_select['selected_friends_checkbox']) {
            $friends_value = $filter_select['selected_friends'] ?? '';
            log_message('info', "FRIENDSHIP FILTER DEBUG (Predictions_m): Checkbox checked, friends value: '$friends_value'");
            
            if (!empty($friends_value) && strtolower($friends_value) !== 'all') {
                log_message('info', "FRIENDSHIP FILTER DEBUG (Predictions_m): Applying friendship filter for type: $friends_value");
            } else {
                log_message('info', "FRIENDSHIP FILTER DEBUG (Predictions_m): Skipping friendship filter - value is 'All' or empty");
            }
        }
        
        if (isset($filter_select['selected_friends_checkbox']) && $filter_select['selected_friends_checkbox'] && 
            isset($filter_select['selected_friends']) && 
            !empty($filter_select['selected_friends']) && 
            strtolower($filter_select['selected_friends']) !== 'all') {
            
            $lottery_id = $filter_select['lottery_id'] ?? null;
            $friendship_type = $filter_select['selected_friends'];
            
            if ($lottery_id) {
                // Get the combination numbers as an array
                $combo_numbers = array_values($combo);
                
                // Validate the friendship requirements for this combination
                if (!$this->validate_combination_friendships($lottery_id, $combo_numbers, $friendship_type)) {
                    return false;
                }
            }
        }
		
		// If we reach here, combination passed all filters
		return true;
	}
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
			'range' => (!empty($combo_values) && count($combo_values) > 1) ? max($combo_values) - min($combo_values) : 0,
		];
	}
	
	/**
	 * Counts how many numbers in $combo are also in $last_draw (repeaters).
	 * Returns the number of repeaters (0, 1, ...).
	 * For independent extra ball lotteries, only compares main numbers (excludes extra ball).
	 * For regular lotteries, compares all numbers including extra balls.
	 *
	 * @param array $combo     Associative array of balls (e.g., ['ball1'=>2, ...])
	 * @param int   $max       Number of balls in the combination
	 * @param array $last_draw Array of last drawn numbers (e.g., ['ball1'=>2, ...])
	 * @return int             Number of repeaters
	 */
	public function is_repeater($combo, $max, $last_draw)
	{
		// For Canada 649 and other regular lotteries, we should NOT treat them as independent extra ball
		// Independent extra ball lotteries have duplicate_extra_ball = 1 in the lottery table
		// Use consistent detection: if combo has 'extra' key AND it's truly independent extra ball lottery
		
		// For now, assume regular lottery behavior (include extra ball) unless specifically BC 649 style
		// This can be enhanced later with lottery table lookup if needed
		$is_independent_extra_ball = false;
		
		$combo_numbers = [];
		$last_numbers = [];
		
		if ($is_independent_extra_ball) {
			// For independent extra ball lotteries, exclude the extra ball from repeater calculation
			foreach ($combo as $key => $value) {
				if ($key !== 'extra') {
					$combo_numbers[] = $value;
				}
			}
			
			// Extract main numbers from last draw (exclude 'extra' key)
			for ($i = 1; $i <= $max; $i++) {
				if (isset($last_draw['ball'.$i])) { 
					$last_numbers[] = $last_draw['ball'.$i];
				}
			}
		} else {
			// For regular lotteries, include all numbers (main + extra if present)
			$combo_numbers = array_values($combo);
			
			// Extract all numbers from last draw (main + extra if present)
			for ($i = 1; $i <= $max; $i++) {
				if (isset($last_draw['ball'.$i])) {
					$last_numbers[] = $last_draw['ball'.$i];
				}
			}
			
			// Include extra ball if present in last draw
			if (isset($last_draw['extra'])) {
				$last_numbers[] = $last_draw['extra'];
			}
		}
		
		// Count how many numbers are repeated
		$repeater_count = count(array_intersect($combo_numbers, $last_numbers));
		
		return $repeater_count;
	}

	/**
	 * Counts the number of consecutive pairs in the combination.
	 * Returns 0 if no consecutive numbers, 1 for one pair, etc.
	 * For independent extra ball lotteries, only considers main numbers (excludes extra ball).
	 * For regular lotteries, considers all numbers including extra balls.
	 *
	 * @param array $combo Associative array of balls (e.g., ['ball1'=>2, ...])
	 * @param int   $max   Number of balls in the combination
	 * @return int         Number of consecutive pairs
	 */
	public function has_consecutive($combo, $max)
	{
		// Check if this is an independent extra ball lottery by looking for 'extra' key in combo
		$is_independent_extra_ball = isset($combo['extra']);
		
		$numbers = [];
		
		if ($is_independent_extra_ball) {
			// For independent extra ball lotteries, exclude the extra ball from consecutive calculation
			foreach ($combo as $key => $value) {
				if ($key !== 'extra') {
					$numbers[] = $value;
				}
			}
		} else {
			// For regular lotteries, include all numbers
			$numbers = array_values($combo);
		}
		
		sort($numbers, SORT_NUMERIC);
		$consecutive_count = 0;
		$actual_count = count($numbers);
		
		// Use the smaller of max or actual count to avoid accessing non-existent indices
		$loop_max = min($max, $actual_count);
		
		for ($i = 1; $i < $loop_max; $i++) {
			if (isset($numbers[$i]) && isset($numbers[$i-1]) && $numbers[$i] - $numbers[$i-1] == 1) {
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
		
		// Return the maximum count of numbers in any single decade
		return max($decade_counts);
	}
	
	/**
	 * Counts the total number of numbers that have the same last digit as at least one other number.
	 * Returns the count of numbers that share a last digit with another number in the combination.
	 *
	 * @param array $combo     Associative array of balls (e.g., ['ball1'=>12, 'ball2'=>22, ...])
	 * @param int   $max       Number of balls in the combination
	 * @return int             Count of numbers that share a last digit with at least one other number
	 */
	public function count_last_digit_numbers($combo, $max)
	{
		$numbers = array_values($combo);
		$last_digit_counts = [];
		
		// Count numbers by their last digit
		foreach ($numbers as $number) {
			$last_digit = $number % 10; // 12 -> 2, 22 -> 2, 35 -> 5, etc.
			if (!isset($last_digit_counts[$last_digit])) {
				$last_digit_counts[$last_digit] = 0;
			}
			$last_digit_counts[$last_digit]++;
		}
		
		// Return the maximum count of numbers with the same last digit
		return max($last_digit_counts);
	}
	
	/**
	 * Gets the expected difference for a specific adjacent ball position.
	 * 
	 * @param array $filter_select The filter selection array
	 * @param int $ball_position The ball position (1 for Ball 1 & Ball 2, 2 for Ball 2 & Ball 3, etc.)
	 * @return int|null The expected difference or null if filtering should be skipped
	 */
	private function get_expected_adjacent_difference($filter_select, $ball_position)
	{
		// Check if lottery highlights are available
		if (!isset($filter_select['lottery_highlights']) || !is_array($filter_select['lottery_highlights'])) {
			return null; // Skip filtering if highlights not available
		}
		
		// Get the adjacents string from lottery highlights
		if (!isset($filter_select['lottery_highlights']['adjacents'])) {
			return null;
		}
		
		$adjacents_string = $filter_select['lottery_highlights']['adjacents'];
		
		// Parse the adjacents string (e.g., "1=6,2=7,3=7,4=6,5=6,6=7|4=27")
		$parts = explode('|', $adjacents_string);
		$main_part = isset($parts[0]) ? $parts[0] : '';
		
		if ($main_part) {
			$pairs = explode(',', $main_part);
			foreach ($pairs as $pair) {
				$kv = explode('=', $pair);
				if (count($kv) == 2) {
					$adj_num = (int)trim($kv[0]);
					$difference = (int)trim($kv[1]);
					
					if ($adj_num === $ball_position) {
						return $difference;
					}
				}
			}
		}
		
		return null;
	}
	
	/**
	 * Calculates the actual difference between adjacent balls in a combination.
	 * 
	 * @param array $combo The combination array (e.g., ['ball1'=>4, 'ball2'=>12, ...])
	 * @param int $ball_position The ball position (1 for Ball 1 & Ball 2, 2 for Ball 2 & Ball 3, etc.)
	 * @return int|null The actual difference or null if balls don't exist
	 */
	private function calculate_adjacent_difference($combo, $ball_position)
	{
		$ball1_key = 'ball' . $ball_position;
		$ball2_key = 'ball' . ($ball_position + 1);
		
		if (isset($combo[$ball1_key]) && isset($combo[$ball2_key])) {
			return $combo[$ball2_key] - $combo[$ball1_key];
		}
		
		return null;
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
	public function get_filtered_combinations_count($filepath, $number_array, $filter_select = [], $start_time = null, $timeout_seconds = 3, $lottery_id = null)
	{
		if (!file_exists($filepath)) {
			log_message('error', "get_filtered_combinations_count: File does not exist: {$filepath}");
			return 0;
		}
		
		// Set start time if not provided
		if ($start_time === null) {
			$start_time = microtime(true);
		}
		
		// If no filters are applied, return total file lines
		$selected_trends = $filter_select['selected_trends'] ?? 'ALL';
		$has_other_filters = $this->has_active_filters($filter_select);
		
		$is_independent_extra_ball = !empty($filter_select['duplicate_extra_ball']) && !empty($filter_select['extra_ball']);
		$selected_extra_ball = $filter_select['selected_extra_ball'] ?? 'ALL';
		
		// For independent extra ball lotteries, we must always process combinations due to different structure
		// even when filters are 'ALL', because the combinations need proper parsing
		if ($selected_trends === 'ALL' && !$has_other_filters && !$is_independent_extra_ball) {
			$total_lines = count(file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
			return $total_lines;
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
			$line_number = 0;
			while (($line = fgets($handle)) !== false) {
				// Check for timeout every 1000 lines to avoid excessive overhead
				if ($line_number % 1000 === 0 && $start_time !== null) {
					$elapsed = microtime(true) - $start_time;
					if ($elapsed > $timeout_seconds) {
						fclose($handle);
						// Get CI instance to access controller
						$CI =& get_instance();
						if (method_exists($CI, 'check_timeout_and_redirect')) {
							$CI->check_timeout_and_redirect($start_time, $timeout_seconds, $lottery_id);
						}
						return $count; // Return partial count if timeout
					}
				}
				
				$line = trim($line);
				if (empty($line)) continue;
				
				$line_number++;
				
				// Parse combination
				$positions = array_map('intval', explode(' ', $line));
				$combo_numbers = [];
				$extra_ball_number = null;
				
				// For independent extra ball lotteries, treat last number differently
				if (!empty($filter_select['duplicate_extra_ball']) && !empty($filter_select['extra_ball'])) {
					// Last number is the actual extra ball, keep it as-is
					$extra_ball_number = array_pop($positions);
					
					// Process remaining positions as usual (insert generated numbers)
					foreach ($positions as $pos) {
						if ($pos > 0 && isset($number_array[$pos - 1])) {
							$combo_numbers[] = $number_array[$pos - 1];
						}
					}
				} else {
					// Regular lottery - all positions are for main numbers
					foreach ($positions as $pos) {
						if ($pos > 0 && isset($number_array[$pos - 1])) {
							$combo_numbers[] = $number_array[$pos - 1];
						}
					}
				}
				
				if (empty($combo_numbers)) continue;
				
				sort($combo_numbers, SORT_NUMERIC);
				$combo = [];
				foreach ($combo_numbers as $idx => $num) {
					$combo['ball'.($idx+1)] = $num;
				}
				
				// Add extra ball for independent extra ball lotteries
				if (!empty($filter_select['duplicate_extra_ball']) && !empty($filter_select['extra_ball'])) {
					if ($extra_ball_number !== null) {
						// Use the actual extra ball number from the combination file
						$combo['extra'] = $extra_ball_number;
					} else {
						// Fallback to generated extra ball (for backward compatibility)
						$max_ball = $filter_select['max_ball'] ?? 50;
						$combo['extra'] = $this->assign_extra_ball($combo_numbers, $max_ball);
					}
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
		
		log_message('info', "get_filtered_combinations_count: Final count: {$count} out of {$line_number} total lines");
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
			'selected_last_digits', 'selected_number_range', 'selected_adjacents',
			'selected_extra_ball'
		];
		
		foreach ($filter_keys as $key) {
			if (isset($filter_select[$key]) && $filter_select[$key] !== 'ALL') {
				return true;
			}
		}
		
		// Special check for H-W-C group: only active if checkbox is checked AND dropdown has value
		if (isset($filter_select['selected_hwc']) && $filter_select['selected_hwc'] && 
			isset($filter_select['selected_h_w_c_group']) && 
			!empty($filter_select['selected_h_w_c_group'])) {
			return true;
		}
		
		return false;
	}
	/**
	 * Save combination filter data to lottery_combination_filters table
	 * Updates existing record if file_name, user_id and lottery_id exists, otherwise inserts new record
	 * Preserves existing win records when updating configuration settings
	 *
	 * @param array $data Data to save
	 * @return bool True on success, false on failure
	 */
	public function save_combination_filter($data)
	{
		// Check if a record with the same file_name, user_id and lottery_id already exists
		if (isset($data['file_name']) && isset($data['user_id']) && isset($data['lottery_id'])) {
			$this->db->where('file_name', $data['file_name']);
			$this->db->where('user_id', $data['user_id']);
			$this->db->where('lottery_id', $data['lottery_id']);
			$this->db->where('user', 1); // Admin user
			$existing = $this->db->get('lottery_combination_filters')->row();
			
			if ($existing) {
				// Preserve existing win records - only update configuration settings
				$update_data = $data;
				
				// Remove win record fields from update data to preserve existing values
				$win_fields = [
					'extra', '1_win', '1_win_extra', '2_win', '2_win_extra', 
					'3_win', '3_win_extra', '4_win', '4_win_extra', '5_win', '5_win_extra',
					'6_win', '6_win_extra', '7_win', '7_win_extra', '8_win', '8_win_extra',
					'9_win', '9_win_extra'
				];
				
				foreach ($win_fields as $field) {
					if (isset($update_data[$field])) {
						unset($update_data[$field]);
					}
				}
				
				// Update existing record with configuration settings only
				$this->db->where('file_name', $data['file_name']);
				$this->db->where('user_id', $data['user_id']);
				$this->db->where('lottery_id', $data['lottery_id']);
				$this->db->where('user', 1);
				return $this->db->update('lottery_combination_filters', $update_data);
			}
		}
		
		// Insert new record if no existing record found
		return $this->db->insert('lottery_combination_filters', $data);
	}

	/**
	 * Save filtered combinations to a file
	 *
	 * @param string $filepath Path to the source combination file
	 * @param array $number_array Array of numbers to filter with
	 * @param array $filters Array of filter criteria
	 * @param string $output_file_path Path to save the filtered combinations
	 * @return bool True on success, false on failure
	 */
	public function save_filtered_combinations_to_file($filepath, $number_array, $filters, $output_file_path)
	{
		if (!file_exists($filepath)) {
			return false;
		}

		// Add debugging for filters
		$duplicate_extra_ball = isset($filters['duplicate_extra_ball']) ? $filters['duplicate_extra_ball'] : 0;
		$extra_ball = isset($filters['extra_ball']) ? $filters['extra_ball'] : 0;
		$selected_extra_ball = isset($filters['selected_extra_ball']) ? $filters['selected_extra_ball'] : null;

		$handle = fopen($filepath, 'r');
		$output_handle = fopen($output_file_path, 'w');
		
		if (!$handle || !$output_handle) {
			if ($handle) fclose($handle);
			if ($output_handle) fclose($output_handle);
			return false;
		}

		$line_count = 0;
		$saved_count = 0;

		while (($line = fgets($handle)) !== false) {
			$line_count++;
			$line = trim($line);
			
			if (empty($line)) {
				continue;
			}

			// Parse the combination line
			$positions = array_map('intval', explode(' ', $line));
			
			// Check if this is an independent extra ball lottery
			$duplicate_extra_ball = isset($filters['duplicate_extra_ball']) ? $filters['duplicate_extra_ball'] : 0;
			$extra_ball = isset($filters['extra_ball']) ? $filters['extra_ball'] : 0;
			$is_independent_extra_ball = ($duplicate_extra_ball == 1 && $extra_ball == 1);
			
			$combo_numbers = [];
			$extra_ball_value = null;
			
			if ($is_independent_extra_ball) {
				// For independent extra ball lotteries, last position is the extra ball
				$total_positions = count($positions);
				$main_positions = array_slice($positions, 0, $total_positions - 1);
				$extra_ball_position = $positions[$total_positions - 1];
				
				// Get main ball numbers
				foreach ($main_positions as $pos) {
					if ($pos > 0 && isset($number_array[$pos - 1])) {
						$combo_numbers[] = $number_array[$pos - 1];
					}
				}
				
				// Get extra ball value (stored directly as the number in independent extra ball lotteries)
				$extra_ball_value = $extra_ball_position;
			} else {
				// For regular lotteries, all positions are main balls
				foreach ($positions as $pos) {
					if ($pos > 0 && isset($number_array[$pos - 1])) {
						$combo_numbers[] = $number_array[$pos - 1];
					}
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
			
			// Add extra ball if it exists
			if ($extra_ball_value !== null) {
				$combo['extra'] = $extra_ball_value;
			}
			
			// Apply filters to this combination
			if ($this->passes_all_filters($combo, $filters)) {
				// Write the actual sorted numbers to the file, not the template positions
				$output_line = implode(' ', $combo_numbers);
				if ($extra_ball_value !== null) {
					$output_line .= ' ' . $extra_ball_value;
				}
				fwrite($output_handle, $output_line . "\n");
				$saved_count++;
			}
		}

		fclose($handle);
		fclose($output_handle);

		return $saved_count > 0;
	}

		/**
		 * Check if a combination passes all filters
		 *
		 * @param array $combo The combination to check (as ball1, ball2, etc.)
		 * @param array $filters Array of filter criteria
		 * @return bool True if combination passes all filters
		 */
		private function passes_all_filters($combo, $filters)
	{
		// Apply trend filter if specified
		if (!empty($filters['selected_trends']) && $filters['selected_trends'] !== 'ALL') {
			$drawn = $filters['drawn'] ?? 0;
			$last_drawn = $filters['lottery_last_drawn'] ?? [];
			$extra_ball = $filters['extra_ball'] ?? 0;
			
			// Prepare last drawn numbers for trend filtering
			$last_drawn_numbers = [];
			for ($i = 1; $i <= $drawn; $i++) {
				if (isset($last_drawn['ball' . $i])) {
					$last_drawn_numbers[] = (int)$last_drawn['ball' . $i];
				}
			}
			if ($extra_ball && isset($last_drawn['extra'])) {
				$last_drawn_numbers[] = (int)$last_drawn['extra'];
			}
			
			if (!$this->check_trend_match($combo, $last_drawn_numbers, $filters['selected_trends'])) {
				return false;
			}
		}
		// Apply other filters using existing method
		return $this->apply_other_filters($combo, $filters);
	}
/**
	 * Check if a combination passes all filters
	 *
	 * @param string 	$name 	The combination to check (as ball1, ball2, etc.)
	 * @return integer 	id	 	Unique ID for the combination 
	 */
	public function get_combination_id($name)
	{
		// Trim the input to remove any leading/trailing whitespace
    	$name = trim($name);
		// Check if the combination already exists in the database
		$this->db->select('id');
		$this->db->from('lottery_combination_files');
		$this->db->where('file_name', $name);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->row()->id; // Return existing ID
		} else {
			return NULL; // return NULL if not found, or error
		}
	}

	/**
	 * Friends-only processing with status information
	 * @param integer $lottery_id The lottery ID
	 * @param array $selections The current number selections
	 * @param string $friendship The friendship type ('none', '1', '2', 'all')
	 * @return array ['numbers' => filtered_numbers, 'friendship_status' => status_info]
	 */
	public function friends_only_with_status($lottery_id, $selections, $friendship)
	{
		// Get the filtered numbers using the existing method
		$filtered_numbers = $this->friends_only($lottery_id, $selections, $friendship);
		
		// Initialize status information
		$status = [
			'requested_type' => $friendship,
			'found_friendships' => [],
			'warning_message' => null
		];
		
		// Check what friendships actually exist in the final result
		$friendship_analysis = $this->analyze_friendships($lottery_id, $filtered_numbers);
		$status['found_friendships'] = $friendship_analysis;
		
		// Generate appropriate warning message based on what was requested vs found
		if ($friendship === 'none') {
			if ($friendship_analysis['has_1way'] || $friendship_analysis['has_2way']) {
				$status['warning_message'] = 'Warning: Some friendships may still exist in the combination despite selecting "No Friends".';
			}
		} elseif ($friendship === '1') {
			if (!$friendship_analysis['has_1way']) {
				$status['warning_message'] = 'Warning: No 1-way friendships were found in the current combination.';
			}
			if ($friendship_analysis['has_2way']) {
				$status['warning_message'] = 'Warning: Some 2-way friendships may still exist despite selecting "1-way Friends Only".';
			}
		} elseif ($friendship === '2') {
			if (!$friendship_analysis['has_2way']) {
				$status['warning_message'] = 'Warning: No 2-way friendships were found in the current combination.';
			}
			if ($friendship_analysis['has_1way']) {
				$status['warning_message'] = 'Warning: Some 1-way friendships may still exist despite selecting "2-way Friends Only".';
			}
		}
		
		return [
			'numbers' => $filtered_numbers,
			'friendship_status' => $status
		];
	}

	/**
	 * Friends-only processing: filter numbers based on friendship relationships only
	 * @param integer $lottery_id The lottery ID
	 * @param array $selections The current number selections
	 * @param string $friendship The friendship type ('none', '1', '2', 'all')
	 * @return array The filtered selections
	 */
	public function friends_only($lottery_id, $selections, $friendship)
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
		
		$twoway = $this->twoway_unique($twoway);
		$result = $selections;
		
		// Get lottery information for replacement range
		$lottery = $this->lotteries_m->get($lottery_id);
		$max_number = $lottery ? $lottery->maximum_ball : 49; // Default to 49 if not found
		
		// Helper: Find random replacement from available numbers
		$find_random_replacement = function($exclude, $max_number) {
			$available = [];
			for ($i = 1; $i <= $max_number; $i++) {
				if (!in_array($i, $exclude)) {
					$available[] = $i;
				}
			}
			return !empty($available) ? $available[array_rand($available)] : null;
		};
		
		// Process friendship types
		if ($friendship === 'none') {
			// Remove all friendships
			$replaced_in_twoway = [];
			foreach ($twoway as $pair) {
				list($a, $b) = $pair;
				if (in_array($a, $result) && in_array($b, $result)) {
					$replace_idx = array_search($b, $result);
					$replacement = $find_random_replacement($result, $max_number);
					if ($replacement !== null) {
						$replaced_in_twoway[] = $b;
						$result[$replace_idx] = (string) $replacement;
					}
				}
			}
			
			foreach ($oneway as $pair) {
				list($a, $b) = $pair;
				if (
					in_array($a, $result) && in_array($b, $result) &&
					!in_array($a, $replaced_in_twoway) && !in_array($b, $replaced_in_twoway)
				) {
					$replace_idx = array_search($b, $result);
					$replacement = $find_random_replacement($result, $max_number);
					if ($replacement !== null) {
						$result[$replace_idx] = (string) $replacement;
					}
				}
			}
		} elseif ($friendship === '1') {
			// Only allow 1-way friendships, remove 2-way
			foreach ($twoway as $pair) {
				list($a, $b) = $pair;
				if (in_array($a, $result) && in_array($b, $result)) {
					$replace_idx = array_search($b, $result);
					$replacement = $find_random_replacement($result, $max_number);
					if ($replacement !== null) {
						$result[$replace_idx] = (string) $replacement;
					}
				}
			}
		} elseif ($friendship === '2') {
			// Only allow 2-way friendships, remove 1-way
			foreach ($oneway as $pair) {
				list($a, $b) = $pair;
				if (in_array($a, $result) && in_array($b, $result)) {
					$replace_idx = array_search($b, $result);
					$replacement = $find_random_replacement($result, $max_number);
					if ($replacement !== null) {
						$result[$replace_idx] = (string) $replacement;
					}
				}
			}
		}
		// For 'all', return as-is (all friendships allowed)
		
		return $result;
	}
	
	/**
	 * Assign an extra ball to a combination for independent extra ball lotteries
	 * 
	 * @param array $combo_numbers Main numbers in the combination
	 * @param int $max_ball Maximum ball number available
	 * @return int The assigned extra ball number
	 */
	private function assign_extra_ball($combo_numbers, $max_ball)
	{
		// For independent extra ball lotteries, we can assign any number from 1 to max_ball
		// Use a weighted random approach based on combination characteristics
		
		// Simple deterministic method based on combination numbers
		// This ensures the same combination always gets the same extra ball
		$sum = array_sum($combo_numbers);
		$extra_ball = ($sum % $max_ball) + 1;
		
		return $extra_ball;
	}
	
	/**
	 * Validate that a combination respects friendship filtering rules
	 * 
	 * @param int $lottery_id The lottery ID
	 * @param array $combo_numbers Array of numbers in the combination
	 * @param string $friendship_type The friendship filter type ('none', '1', '2')
	 * @return bool True if combination respects friendship rules, false otherwise
	 */
	private function validate_combination_friendships($lottery_id, $combo_numbers, $friendship_type)
	{
		// Get friendship data from database
		$row = $this->db->get_where('lottery_friends', ['lottery_id' => $lottery_id])->row_array();
		if (!$row || empty($row['wins'])) {
			return true; // No friendship data, allow all combinations
		}
		
        // Parse friendship data - split on pipe character first
        $friend_str = trim($row['wins']);
        $parts = explode('|', $friend_str);
        
        // Friendships are in the part after the pipe
        if (count($parts) > 1) {
            $friend_str = trim($parts[1]);
        } else {
            $friend_str = trim($parts[0]);
        }
        
        $friendships = array_filter(array_map('trim', explode(',', $friend_str)));
        
        $oneway = [];  // 1-way friendships
        $twoway = [];  // 2-way friendships
        
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
        
        // Make sure 2-way friendships are unique (remove duplicates like [1,2] and [2,1])
        $twoway = $this->twoway_unique($twoway);
        
        // Look specifically for friendships involving 17 and 46
        foreach ($oneway as $pair) {
            list($a, $b) = $pair;
            if ($a == 17 || $b == 17 || $a == 46 || $b == 46) {
                log_message('info', "FRIENDSHIP DEBUG (Predictions_m): Found 1-way friendship involving 17 or 46: {$a} > {$b}");
            }
        }
        foreach ($twoway as $pair) {
            list($a, $b) = $pair;
            if ($a == 17 || $b == 17 || $a == 46 || $b == 46) {
                log_message('info', "FRIENDSHIP DEBUG (Predictions_m): Found 2-way friendship involving 17 or 46: {$a} <> {$b}");
            }
        }		// Check friendship rules based on selected filter type
		switch ($friendship_type) {
			case 'none':
				// No friendships should exist
				return $this->validate_no_friendships($combo_numbers, $oneway, $twoway);
				
			case '1':
				// Only 1-way friendships allowed (no 2-way friendships)
				return $this->validate_oneway_friendships_only($combo_numbers, $oneway, $twoway);
				
			case '2':
				// Only 2-way friendships allowed (no 1-way friendships)
				return $this->validate_twoway_friendships_only($combo_numbers, $oneway, $twoway);
				
			default:
				return true; // 'all' or unknown type - allow everything
		}
	}
	
	/**
	 * Validate that combination has no friendships
	 */
	private function validate_no_friendships($combo_numbers, $oneway, $twoway)
	{
		// Check for any 2-way friendships
		foreach ($twoway as $pair) {
			list($a, $b) = $pair;
			if (in_array($a, $combo_numbers) && in_array($b, $combo_numbers)) {
				return false; // Found 2-way friendship
			}
		}
		
		// Check for any 1-way friendships
		foreach ($oneway as $pair) {
			list($a, $b) = $pair;
			if (in_array($a, $combo_numbers) && in_array($b, $combo_numbers)) {
				return false; // Found 1-way friendship
			}
		}
		
		return true; // No friendships found
	}
	
	/**
	 * Validate that combination only has 1-way friendships (no 2-way friendships)
	 * For 1-way friendships to be valid: if A>B and A is in combo, then B MUST also be in combo
	 * Must have at least one complete 1-way friendship
	 */
	private function validate_oneway_friendships_only($combo_numbers, $oneway, $twoway)
	{
		// First, check that no 2-way friendships exist
		foreach ($twoway as $pair) {
			list($a, $b) = $pair;
			if (in_array($a, $combo_numbers) && in_array($b, $combo_numbers)) {
				return false; // Found 2-way friendship - not allowed
			}
		}
		
		$found_complete_oneway = false;
		
		// Check that 1-way friendships are complete (if A>B and A is present, B must be present)
		foreach ($oneway as $pair) {
			list($a, $b) = $pair;
			if (in_array($a, $combo_numbers) && !in_array($b, $combo_numbers)) {
				return false; // Found incomplete 1-way friendship
			}
			if (in_array($a, $combo_numbers) && in_array($b, $combo_numbers)) {
				$found_complete_oneway = true;
			}
		}
		
		if (!$found_complete_oneway) {
			return false; // Must have at least one complete 1-way friendship
		}
		
		return true; // Only valid 1-way friendships found
	}
	
	/**
	 * Validate that combination only has 2-way friendships (no 1-way friendships)
	 * For 2-way friendships to be valid: if A-B and either A or B is in combo, then both must be in combo
	 * Must have at least one complete 2-way friendship
	 */
	private function validate_twoway_friendships_only($combo_numbers, $oneway, $twoway)
	{
		// First, check that no 1-way friendships exist
		foreach ($oneway as $pair) {
			list($a, $b) = $pair;
			if (in_array($a, $combo_numbers) && in_array($b, $combo_numbers)) {
				return false; // Found 1-way friendship - not allowed
			}
		}
		
		$found_complete_twoway = false;
		
		// Check that 2-way friendships are complete (if A-B and A is present, B must be present)
		foreach ($twoway as $pair) {
			list($a, $b) = $pair;
			if ((in_array($a, $combo_numbers) && !in_array($b, $combo_numbers)) ||
				(in_array($b, $combo_numbers) && !in_array($a, $combo_numbers))) {
				return false; // Found incomplete 2-way friendship
			}
			if (in_array($a, $combo_numbers) && in_array($b, $combo_numbers)) {
				$found_complete_twoway = true;
			}
		}
		
		if (!$found_complete_twoway) {
			return false; // Must have at least one complete 2-way friendship
		}
		
		return true; // Only valid 2-way friendships found
	}
}