<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Number Generation Model
 * Handles number generation, H-W-C, followers, and friends logic
 */
class Number_generation_m extends MY_Model
{
    protected $_table_name = 'number_generation';
    protected $_order_by = 'id';

    /**
     * Generate numbers using H-W-C (Hot, Warm, Cold) logic only with position count ranking
     * @param int $lottery_id Lottery ID
     * @param int $combination_size Number of numbers to generate
     * @param string $h_w_c H-W-C selection criteria (e.g., "2-2-2")
     * @return array Generated numbers
     */
    public function hwc_only($lottery_id, $combination_size, $h_w_c)
    {
        $this->load->model('statistics_m');
        
        // Parse H-W-C criteria (e.g., "2-2-2")
        preg_match('/^(\d+)-(\d+)-(\d+)/', $h_w_c, $matches);
        if (count($matches) < 4) {
            return [];
        }
        
        $h_ratio = intval($matches[1]);
        $w_ratio = intval($matches[2]);
        $c_ratio = intval($matches[3]);
        $total_ratio = $h_ratio + $w_ratio + $c_ratio;
        
        // Get H-W-C data from database
        $hwc_data = $this->statistics_m->h_w_c_exists($lottery_id);
        $position_data = $this->statistics_m->hwc_history_exists($lottery_id);
        
        if (!$hwc_data || !$position_data || empty($position_data['position'])) {
            return [];
        }
        
        // Calculate base allocation using ratio
        $base_h = floor(($h_ratio / $total_ratio) * $combination_size);
        $base_w = floor(($w_ratio / $total_ratio) * $combination_size);
        $base_c = floor(($c_ratio / $total_ratio) * $combination_size);
        
        // Calculate remaining numbers to allocate
        $remaining = $combination_size - ($base_h + $base_w + $base_c);
        
        // Get actual counts from lottery_h_w_c table for tie-breaking
        $h_count = isset($hwc_data['h_count']) ? intval($hwc_data['h_count']) : 0;
        $w_count = isset($hwc_data['w_count']) ? intval($hwc_data['w_count']) : 0;
        $c_count = isset($hwc_data['c_count']) ? intval($hwc_data['c_count']) : 0;
        
        // Create array for sorting by actual counts (tie-breaker)
        $categories = [
            ['type' => 'w', 'count' => $w_count, 'allocated' => $base_w],
            ['type' => 'h', 'count' => $h_count, 'allocated' => $base_h],
            ['type' => 'c', 'count' => $c_count, 'allocated' => $base_c]
        ];
        
        // Sort by actual count descending (largest count gets priority for extra numbers)
        usort($categories, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        // Distribute remaining numbers to categories with highest counts
        for ($i = 0; $i < $remaining; $i++) {
            $categories[0]['allocated']++;
        }
        
        // Extract final allocations
        $hot_count = 0;
        $warm_count = 0;
        $cold_count = 0;
        
        foreach ($categories as $category) {
            switch ($category['type']) {
                case 'h':
                    $hot_count = $category['allocated'];
                    break;
                case 'w':
                    $warm_count = $category['allocated'];
                    break;
                case 'c':
                    $cold_count = $category['allocated'];
                    break;
            }
        }
        
        // Parse H-W-C numbers and position counts
        $hot_numbers = $this->parse_hwc_numbers($hwc_data['hots']);
        $warm_numbers = $this->parse_hwc_numbers($hwc_data['warms']);
        $cold_numbers = $this->parse_hwc_numbers($hwc_data['colds']);
        
        // Parse position counts for each category
        $position_parts = explode('|', $position_data['position']);
        $hot_positions = $this->parse_position_counts($position_parts[0]);
        $warm_positions = $this->parse_position_counts($position_parts[1]);
        $cold_positions = $this->parse_position_counts($position_parts[2]);
        
        // Select numbers by highest position counts within each category
        $selected_numbers = [];
        
        // Select hot numbers by position count ranking
        $selected_hot = $this->select_by_position_ranking($hot_positions, $hot_numbers, $hot_count);
        $selected_numbers = array_merge($selected_numbers, $selected_hot);
        
        // Select warm numbers by position count ranking
        $selected_warm = $this->select_by_position_ranking($warm_positions, $warm_numbers, $warm_count);
        $selected_numbers = array_merge($selected_numbers, $selected_warm);
        
        // Select cold numbers by position count ranking
        $selected_cold = $this->select_by_position_ranking($cold_positions, $cold_numbers, $cold_count);
        $selected_numbers = array_merge($selected_numbers, $selected_cold);
        
        // If not enough numbers, fill from remaining numbers in order of preference (hot -> warm -> cold)
        if (count($selected_numbers) < $combination_size) {
            $all_numbers = array_merge($hot_numbers, $warm_numbers, $cold_numbers);
            foreach ($all_numbers as $number) {
                if (!in_array($number, $selected_numbers)) {
                    $selected_numbers[] = $number;
                    if (count($selected_numbers) >= $combination_size) break;
                }
            }
        }
        
        sort($selected_numbers);
        return array_slice($selected_numbers, 0, $combination_size);
    }

    /**
     * Generate numbers using followers logic only
     * @param int $lottery_id Lottery ID
     * @param int $combination_size Number of numbers to generate
     * @param string $type Follower type (after_ball or position)
     * @param string $select Selection criteria
     * @return array Generated numbers
     */
    public function followers_only($lottery_id, $combination_size, $type, $select)
    {
        $follow_list = $this->get_followers_list($lottery_id, $type, $select);
        
        if (empty($follow_list)) {
            return [];
        }
        
        // Remove zeros and invalid entries
        $valid_followers = [];
        foreach ($follow_list as $number) {
            if ($number !== '0' && $number !== 0 && $number !== '' && is_numeric($number) && intval($number) > 0) {
                $valid_followers[] = intval($number);
            }
        }
        
        if (empty($valid_followers)) {
            return [];
        }
        
        // Remove duplicates and sort
        $valid_followers = array_unique($valid_followers);
        sort($valid_followers);
        
        // Return requested number of followers
        return array_slice($valid_followers, 0, $combination_size);
    }

    /**
     * Generate numbers using both H-W-C and followers logic
     * @param int $lottery_id Lottery ID
     * @param int $combination_size Number of numbers to generate
     * @param string $h_w_c H-W-C selection criteria
     * @param string $follower_type Follower type
     * @param string $follower_select Follower selection criteria
     * @return array Generated numbers
     */
    public function hwc_followers($lottery_id, $combination_size, $h_w_c, $follower_type, $follower_select)
    {
        // Get H-W-C numbers
        $hwc_numbers = $this->hwc_only($lottery_id, ceil($combination_size / 2), $h_w_c);
        
        // Get follower numbers
        $follower_numbers = $this->followers_only($lottery_id, ceil($combination_size / 2), $follower_type, $follower_select);
        
        // Combine and remove duplicates
        $combined_numbers = array_unique(array_merge($hwc_numbers, $follower_numbers));
        sort($combined_numbers);
        
        // Fill remaining slots if needed
        if (count($combined_numbers) < $combination_size) {
            $heat_map = $this->get_heat_map($lottery_id);
            $all_numbers = array_keys($heat_map);
            $remaining = array_diff($all_numbers, $combined_numbers);
            
            while (count($combined_numbers) < $combination_size && !empty($remaining)) {
                $combined_numbers[] = array_shift($remaining);
            }
        }
        
        sort($combined_numbers);
        return array_slice($combined_numbers, 0, $combination_size);
    }

    /**
     * Apply friendship search with H-W-C heat map
     * @param int $lottery_id Lottery ID
     * @param array $selections Current number selections
     * @param string $friendship Friendship type (none, 1, 2)
     * @param array $heat_map Heat map data
     * @return array Modified selections
     */
    public function friend_search_hwc($lottery_id, $selections, $friendship, $heat_map)
    {
        // Get friendship data
        $this->load->model('lottery_data_m');
        $friends_data = $this->lottery_data_m->get_friends($lottery_id);
        
        if (empty($friends_data) || empty($friends_data['range'])) {
            return $selections;
        }
        
        // Parse friendship ranges
        $oneway = $this->parse_friendship_range($friends_data['range'], 'oneway');
        $twoway = $this->parse_friendship_range($friends_data['range'], 'twoway');
        $twoway = $this->twoway_unique($twoway);
        
        // Helper: Find a replacement from heat map not already in $exclude
        $find_hwc_replacement = function($exclude) use ($heat_map) {
            foreach ($heat_map as $num => $frequency) {
                if ($num !== '0' && $num !== 0 && $num !== '' && is_numeric($num) && intval($num) > 0 && !in_array($num, $exclude)) {
                    return intval($num);
                }
            }
            return null;
        };
        
        return $this->apply_friendship_logic($selections, $friendship, $oneway, $twoway, $find_hwc_replacement);
    }

    /**
     * Apply friendship search with followers list
     * @param int $lottery_id Lottery ID
     * @param array $selections Current number selections
     * @param string $friendship Friendship type (none, 1, 2)
     * @param array $follow_list Followers list
     * @return array Modified selections
     */
    public function friend_search_followers($lottery_id, $selections, $friendship, $follow_list)
    {
        // Get friendship data
        $this->load->model('lottery_data_m');
        $friends_data = $this->lottery_data_m->get_friends($lottery_id);
        
        if (empty($friends_data) || empty($friends_data['range'])) {
            return $selections;
        }
        
        // Parse friendship ranges
        $oneway = $this->parse_friendship_range($friends_data['range'], 'oneway');
        $twoway = $this->parse_friendship_range($friends_data['range'], 'twoway');
        $twoway = $this->twoway_unique($twoway);
        
        // Helper: Find a replacement from follow_list not already in $exclude
        $find_follower_replacement = function($exclude) use ($follow_list) {
            foreach ($follow_list as $num) {
                if ($num !== '0' && $num !== 0 && $num !== '' && is_numeric($num) && intval($num) > 0 && !in_array($num, $exclude)) {
                    return intval($num);
                }
            }
            return null;
        };
        
        return $this->apply_friendship_logic($selections, $friendship, $oneway, $twoway, $find_follower_replacement);
    }

    /**
     * Get heat map data for a lottery
     * @param int $lottery_id Lottery ID
     * @return array Heat map with number frequencies
     */
    public function get_heat_map($lottery_id)
    {
        // This would query the lottery's historical data to build frequency map
        // Placeholder implementation
        $heat_map = [];
        
        // Get lottery profile to determine number range
        $this->db->select('ball_min, ball_max, balls_drawn');
        $this->db->from('lottery_profiles');
        $this->db->where('id', $lottery_id);
        $lottery = $this->db->get()->row();
        
        if (!$lottery) {
            return [];
        }
        
        // Initialize heat map with all possible numbers
        for ($i = $lottery->ball_min; $i <= $lottery->ball_max; $i++) {
            $heat_map[$i] = 0;
        }
        
        // Get lottery table name and query historical data
        $this->load->model('lotteries_m');
        $lottery_name = $this->db->select('lottery_name')->from('lottery_profiles')->where('id', $lottery_id)->get()->row()->lottery_name;
        $table_name = $this->lotteries_m->lotto_table_convert($lottery_name);
        
        // Query last 100 draws for frequency analysis
        $sql = "SELECT * FROM `{$table_name}` ORDER BY draw_date DESC LIMIT 100";
        $query = $this->db->query($sql);
        
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            foreach ($results as $draw) {
                for ($i = 1; $i <= $lottery->balls_drawn; $i++) {
                    $ball_key = 'ball' . $i;
                    if (isset($draw[$ball_key]) && is_numeric($draw[$ball_key])) {
                        $number = intval($draw[$ball_key]);
                        if (isset($heat_map[$number])) {
                            $heat_map[$number]++;
                        }
                    }
                }
            }
        }
        
        return $heat_map;
    }
    
    /**
     * Parse H-W-C numbers from database format (e.g., "7=15,23=12,34=11")
     * @param string $hwc_string Database string with number=count format
     * @return array Array of numbers in order
     */
    private function parse_hwc_numbers($hwc_string)
    {
        $numbers = [];
        if (empty($hwc_string)) {
            return $numbers;
        }
        
        $pairs = explode(',', $hwc_string);
        foreach ($pairs as $pair) {
            $parts = explode('=', $pair);
            if (count($parts) == 2) {
                $numbers[] = intval(trim($parts[0]));
            }
        }
        
        return $numbers;
    }
    
    /**
     * Parse position counts from database format (e.g., "H>0=17,1=18,2=16")
     * @param string $position_string Database string with position=count format
     * @return array Array of position => count
     */
    private function parse_position_counts($position_string)
    {
        $positions = [];
        if (empty($position_string)) {
            return $positions;
        }
        
        // Remove category prefix (H>, W>, C>)
        $cleaned = preg_replace('/^[HWC]>/', '', $position_string);
        
        $pairs = explode(',', $cleaned);
        foreach ($pairs as $pair) {
            $parts = explode('=', $pair);
            if (count($parts) == 2) {
                $position = intval(trim($parts[0]));
                $count = intval(trim($parts[1]));
                $positions[$position] = $count;
            }
        }
        
        return $positions;
    }
    
    /**
     * Select numbers by highest position counts within a category
     * @param array $position_counts Array of position => count
     * @param array $numbers Array of available numbers in category
     * @param int $limit Maximum numbers to select
     * @return array Selected numbers
     */
    private function select_by_position_ranking($position_counts, $numbers, $limit)
    {
        $selected = [];
        if ($limit <= 0 || empty($numbers)) {
            return $selected;
        }
        
        // Sort positions by count (highest first)
        arsort($position_counts);
        
        // Select numbers at positions with highest counts
        foreach ($position_counts as $position => $count) {
            if (isset($numbers[$position]) && !in_array($numbers[$position], $selected)) {
                $selected[] = $numbers[$position];
                if (count($selected) >= $limit) {
                    break;
                }
            }
        }
        
        // If still need more numbers, fill from remaining numbers in order
        if (count($selected) < $limit) {
            foreach ($numbers as $number) {
                if (!in_array($number, $selected)) {
                    $selected[] = $number;
                    if (count($selected) >= $limit) {
                        break;
                    }
                }
            }
        }
        
        return $selected;
    }

    /**
     * Get followers list based on type and selection
     * @param int $lottery_id Lottery ID
     * @param string $type Type of followers (after_ball or position)
     * @param string $select Selection criteria
     * @return array Followers list
     */
    public function get_followers_list($lottery_id, $type, $select)
    {
        $this->load->model('lottery_data_m');
        $followers_data = $this->lottery_data_m->get_followers($lottery_id);
        
        if (empty($followers_data)) {
            return [];
        }
        
        $follow_list = [];
        
        if ($type === 'after_ball') {
            // Parse ball-based followers
            if (!empty($followers_data['wins'])) {
                $wins = explode('>', $followers_data['wins']);
                if (is_numeric($select) && isset($wins[intval($select)])) {
                    $numbers = explode(',', $wins[intval($select)]);
                    foreach ($numbers as $num) {
                        $num = trim($num);
                        if (is_numeric($num)) {
                            $follow_list[] = intval($num);
                        }
                    }
                }
            }
        } elseif ($type === 'position') {
            // Parse position-based followers
            if (!empty($followers_data['positions'])) {
                $positions = explode('>', $followers_data['positions']);
                $pos_parts = $this->parse_position_part($select);
                if (!empty($pos_parts)) {
                    $position_index = $pos_parts['position'] - 1;
                    if (isset($positions[$position_index])) {
                        $numbers = explode(',', $positions[$position_index]);
                        $selected_numbers = $this->select_by_position_index($pos_parts, $numbers, 12);
                        $follow_list = array_merge($follow_list, $selected_numbers);
                    }
                }
            }
        }
        
        return $follow_list;
    }

    /**
     * Parse position part string (e.g., "P1", "+14")
     * @param string $str Position string
     * @return array Parsed position data
     */
    private function parse_position_part($str)
    {
        if (strpos($str, 'P') === 0) {
            // Position format: P1, P2, etc.
            $position = intval(substr($str, 1));
            return ['type' => 'position', 'position' => $position];
        } elseif (strpos($str, '+') === 0) {
            // Offset format: +14, +7, etc.
            $offset = intval(substr($str, 1));
            return ['type' => 'offset', 'offset' => $offset];
        } elseif (is_numeric($str)) {
            // Direct number
            $number = intval($str);
            return ['type' => 'number', 'number' => $number];
        }
        
        return [];
    }

    /**
     * Select numbers by position index
     * @param array $positions Position data
     * @param array $numbers Available numbers
     * @param int $limit Maximum numbers to select
     * @return array Selected numbers
     */
    private function select_by_position_index($positions, $numbers, $limit)
    {
        $selected = [];
        
        if ($positions['type'] === 'position') {
            // Select from specific position
            $start_index = ($positions['position'] - 1) * $limit;
            $selected = array_slice($numbers, $start_index, $limit);
        } elseif ($positions['type'] === 'offset') {
            // Select with offset
            foreach ($numbers as $index => $number) {
                if (($index + 1) % $positions['offset'] === 0) {
                    $selected[] = $number;
                    if (count($selected) >= $limit) break;
                }
            }
        } elseif ($positions['type'] === 'number') {
            // Direct number selection
            $selected[] = $positions['number'];
        }
        
        return $selected;
    }

    /**
     * Remove duplicate two-way friendships
     * @param array $tw Two-way friendships array
     * @return array Unique two-way friendships
     */
    private function twoway_unique($tw)
    {
        $unique = [];
        
        foreach ($tw as $pair) {
            if (count($pair) == 2) {
                sort($pair);
                $key = implode('-', $pair);
                if (!isset($unique[$key])) {
                    $unique[$key] = $pair;
                }
            }
        }
        
        return array_values($unique);
    }

    /**
     * Parse friendship range data
     * @param string $range Friendship range string
     * @param string $type Type to parse (oneway or twoway)
     * @return array Parsed friendships
     */
    private function parse_friendship_range($range, $type)
    {
        // This would parse the friendship range data
        // Placeholder implementation
        $friendships = [];
        
        if ($type === 'oneway') {
            // Parse one-way friendships
            // Format: "1>2,3>4,5>6"
            $pairs = explode(',', $range);
            foreach ($pairs as $pair) {
                $parts = explode('>', $pair);
                if (count($parts) == 2) {
                    $friendships[] = [trim($parts[0]), trim($parts[1])];
                }
            }
        } elseif ($type === 'twoway') {
            // Parse two-way friendships
            // Format: "1<>2,3<>4,5<>6"
            $pairs = explode(',', $range);
            foreach ($pairs as $pair) {
                $parts = explode('<>', $pair);
                if (count($parts) == 2) {
                    $friendships[] = [trim($parts[0]), trim($parts[1])];
                }
            }
        }
        
        return $friendships;
    }

    /**
     * Apply friendship logic to selections
     * @param array $selections Current selections
     * @param string $friendship Friendship type
     * @param array $oneway One-way friendships
     * @param array $twoway Two-way friendships  
     * @param callable $find_replacement Replacement function
     * @return array Modified selections
     */
    private function apply_friendship_logic($selections, $friendship, $oneway, $twoway, $find_replacement)
    {
        $result = $selections;
        
        // --- NONE: Remove all friendships, one full pass only ---
        if ($friendship === 'none') {
            $replaced_in_twoway = [];
            // 1. Check and replace all 2-way friendships in one pass
            foreach ($twoway as $pair) {
                list($a, $b) = $pair;
                if (in_array($a, $result) && in_array($b, $result)) {
                    $replace_idx = array_search($b, $result);
                    $exclude = array_unique(array_merge($result, $replaced_in_twoway));
                    $replacement = $find_replacement($exclude);
                    if ($replacement !== null) {
                        $result[$replace_idx] = (string) $replacement;
                        $replaced_in_twoway[] = $b;
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
                    $replacement = $find_replacement($exclude);
                    if ($replacement !== null) {
                        $result[$replace_idx] = (string) $replacement;
                        $replaced_in_oneway[] = $b;
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
                    $replacement = $find_replacement($result);
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
                    $replacement = $find_replacement($result);
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
                    $replacement = $find_replacement($result);
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
                    // Replace one number with the friend
                    $exclude = array_diff($result, [$a]);
                    $replace_idx = array_search(end($exclude), $result);
                    $result[$replace_idx] = (string) $b;
                    return $result;
                }
            }
        }
        
        return $result;
    }

    /** 
     * Iterates the number of predictions and returns them in an array to
     * be used in the Math Combinatorics combination methods
     * @param	integer	$Pr		Predicted Numbers (e.g. 1 to 15)
     * @return	array	$combs	Array of the number of predicted (1,2,3,4,5,6,7,8,9...15)
     */
    public function wheeled($Pr)
    {
        $c = 1;
        $combs = array();
        for ($i=0; $i < $Pr; $i++)
        {
            $combs[] = $c; // Add next predicted element onto the array
            $c++;
        }
        return $combs;
    }
}
