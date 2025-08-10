<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Combination Filters Model
 * Handles filtering combinations based on various criteria
 */
class Combination_filters_m extends MY_Model
{
    protected $_table_name = 'lottery_combination_filters';
    protected $_order_by = 'id';

    /**
     * Save combination filter data to lottery_combination_filters table
     *
     * @param array $data Data to save
     * @return bool True on success, false on failure
     */
    public function save_combination_filter($data)
    {
        return $this->db->insert('lottery_combination_filters', $data);
    }

    /**
     * Get combination filter data by combo_id
     *
     * @param int $combo_id Combination ID
     * @return array|null Filter data or null if not found
     */
    public function get_combination_filter($combo_id)
    {
        $this->db->where('combo_id', $combo_id);
        $query = $this->db->get('lottery_combination_filters');
        
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        
        return null;
    }

    /**
     * Insert number combination with filtering and pagination
     *
     * @param string $filepath Path to the combination file
     * @param array  $number_array  Array of numbers to substitute
     * @param int    $page Current page (1-based)
     * @param int    $per_page Number of combinations per page
     * @param array  $filter_select Array of filters to apply
     * @return array Paginated and filtered combinations
     */
    public function insert_number_combination($filepath, $number_array, $page = 1, $per_page = 10, $filter_select = [])
    {
        if (!file_exists($filepath)) {
            return [];
        }
        
        $combinations = [];
        $count = 0;
        $start_line = ($page - 1) * $per_page;
        $end_line = $start_line + $per_page;
        $current_line = 0;
        
        // Check if we need to apply filters
        $selected_trends = $filter_select['selected_trends'] ?? 'ALL';
        $has_other_filters = $this->has_active_filters($filter_select);
        
        if ($selected_trends === 'ALL' && !$has_other_filters) {
            // No filters, simple pagination
            if (($handle = fopen($filepath, 'r')) !== false) {
                while (($line = fgets($handle)) !== false) {
                    if ($current_line >= $start_line && $current_line < $end_line) {
                        $line = trim($line);
                        if (!empty($line)) {
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
                            
                            if (!empty($combo_numbers)) {
                                sort($combo_numbers, SORT_NUMERIC);
                                
                                // For independent extra ball lotteries, return structured data
                                if ($extra_ball_number !== null) {
                                    $combo_data = array(
                                        'main_numbers' => $combo_numbers,
                                        'extra_ball' => $extra_ball_number
                                    );
                                } else {
                                    // Regular lottery - return just the numbers
                                    $combo_data = $combo_numbers;
                                }
                                
                                $combinations[] = $combo_data;
                            }
                        }
                    }
                    $current_line++;
                    if ($current_line >= $end_line) break;
                }
                fclose($handle);
            }
        } else {
            // Apply filters
            $combinations = $this->get_filtered_combinations($filepath, $number_array, $filter_select, $page, $per_page);
        }
        
        return $combinations;
    }

    /**
     * Get filtered combinations with pagination
     *
     * @param string $filepath Path to the combination file
     * @param array  $number_array Array of numbers to substitute
     * @param array  $filter_select Array of filters to apply
     * @param int    $page Current page
     * @param int    $per_page Number of combinations per page
     * @return array Filtered combinations
     */
    public function get_filtered_combinations($filepath, $number_array, $filter_select, $page, $per_page)
    {
        $combinations = [];
        $count = 0;
        $skip_count = ($page - 1) * $per_page;
        $drawn = $filter_select['drawn'] ?? 0;
        $last_drawn = $filter_select['lottery_last_drawn'] ?? [];
        $extra_ball = $filter_select['extra_ball'] ?? 0;
        $selected_trends = $filter_select['selected_trends'] ?? 'ALL';
        
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
                if ($extra_ball_number !== null) {
                    $combo['extra'] = $extra_ball_number;
                }
                
                // Check if combination passes all filters
                if ($this->passes_all_filters($combo, $filter_select)) {
                    if ($count >= $skip_count) {
                        // For independent extra ball lotteries, return structured data
                        if ($extra_ball_number !== null) {
                            $combo_result = array(
                                'main_numbers' => $combo_numbers,
                                'extra_ball' => $extra_ball_number
                            );
                        } else {
                            // Regular lottery - return just the numbers
                            $combo_result = $combo_numbers;
                        }
                        
                        $combinations[] = $combo_result;
                        if (count($combinations) >= $per_page) {
                            break;
                        }
                    }
                    $count++;
                }
            }
            fclose($handle);
        }
        
        return $combinations;
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
                if ($extra_ball_number !== null) {
                    $combo['extra'] = $extra_ball_number;
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
            log_message('error', "save_filtered_combinations_to_file: Source file not found: {$filepath}");
            return false;
        }

        $handle = fopen($filepath, 'r');
        $output_handle = fopen($output_file_path, 'w');
        
        if (!$handle || !$output_handle) {
            if ($handle) fclose($handle);
            if ($output_handle) fclose($output_handle);
            log_message('error', "save_filtered_combinations_to_file: Could not open files - source: {$filepath}, output: {$output_file_path}");
            return false;
        }

        $line_count = 0;
        $saved_count = 0;
        $processed_count = 0;

        while (($line = fgets($handle)) !== false) {
            $line_count++;
            $line = trim($line);
            if (empty($line)) continue;
            
            $processed_count++;
            
            // Parse combination
            $positions = array_map('intval', explode(' ', $line));
            $combo_numbers = [];
            $extra_ball_number = null;
            
            // Debug logging for first few combinations
            static $conversion_debug_count = 0;
            if ($conversion_debug_count < 3) {
                log_message('debug', "CONVERSION DEBUG #{$conversion_debug_count}: Raw line: '{$line}'");
                log_message('debug', "CONVERSION DEBUG #{$conversion_debug_count}: Positions: " . print_r($positions, true));
                log_message('debug', "CONVERSION DEBUG #{$conversion_debug_count}: Number array (first 10): " . print_r(array_slice($number_array, 0, 10, true), true));
                $conversion_debug_count++;
            }
            
            // For independent extra ball lotteries, handle last position as extra ball
            if (!empty($filters['duplicate_extra_ball']) && !empty($filters['extra_ball'])) {
                // Last position is the actual extra ball number
                $extra_ball_number = array_pop($positions);
                
                // Process remaining positions normally
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
            
            // Add extra ball to combo for filtering purposes
            if ($extra_ball_number !== null) {
                $combo['extra'] = $extra_ball_number;
            }
            
            // Debug logging for first few converted combinations
            static $combo_debug_count = 0;
            if ($combo_debug_count < 3) {
                log_message('debug', "COMBO DEBUG #{$combo_debug_count}: Final combo array: " . print_r($combo, true));
                log_message('debug', "COMBO DEBUG #{$combo_debug_count}: Sum: " . array_sum(array_values(array_filter($combo, function($key) { return $key !== 'extra'; }, ARRAY_FILTER_USE_KEY))));
                $combo_debug_count++;
            }
            
            // Check if combination passes all filters
            if ($this->passes_all_filters($combo, $filters)) {
                // For lotteries with independent extra ball (duplicate_extra_ball = 1),
                // format the output to include the extra ball as part of the number sequence
                if (!empty($filters['duplicate_extra_ball']) && !empty($filters['extra_ball'])) {
                    // Append extra ball to main numbers: "8 18 20 32 49 1"
                    $all_numbers = $combo_numbers;
                    $all_numbers[] = $extra_ball_number;
                    $output_line = implode(' ', $all_numbers);
                } else {
                    // For regular lotteries, output the substituted numbers
                    $output_line = implode(' ', $combo_numbers);
                }
                
                fwrite($output_handle, $output_line . "\n");
                $saved_count++;
            }
        }

        fclose($handle);
        fclose($output_handle);
        
        // Log the filtering results for debugging
        log_message('info', "save_filtered_combinations_to_file: Processed {$processed_count} combinations, saved {$saved_count} to {$output_file_path}");
        
        // If no combinations were saved, log the filter criteria for debugging
        if ($saved_count == 0) {
            log_message('warning', "save_filtered_combinations_to_file: No combinations passed filters. Filters: " . print_r($filters, true));
        }

        return true;
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
            if (!empty($filter_select[$key]) && $filter_select[$key] !== 'ALL') {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if a combination passes all filters
     *
     * @param array $combo Combination to check
     * @param array $filters Array of filter criteria
     * @return bool True if combination passes all filters
     */
    private function passes_all_filters($combo, $filters)
    {
        // For independent extra ball lotteries, separate main numbers from extra ball for filtering
        $main_numbers = $combo;
        if (!empty($filters['duplicate_extra_ball']) && !empty($filters['extra_ball']) && isset($combo['extra'])) {
            // Remove extra ball from main numbers for trend filtering calculations
            unset($main_numbers['extra']);
        }
        
        // Convert associative array (ball1, ball2, etc.) to indexed array of values for trend calculations
        $main_numbers_values = array_values($main_numbers);
        
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
            // For independent extra ball lotteries, include extra ball in trend matching
            // but use it separately from main number matching
            if ($extra_ball && isset($last_drawn['extra'])) {
                $last_drawn_numbers[] = (int)$last_drawn['extra'];
            }
            
            if (!$this->check_trend_match($main_numbers_values, $last_drawn_numbers, $filters['selected_trends'])) {
                return false;
            }
        }
        
        // Apply other filters using existing method
        return $this->apply_other_filters($combo, $filters);
    }

    /**
     * Apply other filters (non-trend filters)
     *
     * @param array $combo Combination to check
     * @param array $filter_select Filter criteria
     * @return bool True if combination passes filters
     */
    private function apply_other_filters($combo, $filter_select)
    {
        // Add debugging to track filter rejections
        $combo_str = is_array($combo) ? implode(',', array_slice(array_values($combo), 0, 5)) : 'invalid';
        
        // Debug: Log active filters for first combo
        static $filter_debug_done = false;
        if (!$filter_debug_done) {
            log_message('info', "apply_other_filters (Combination_filters_m): Active filters check:");
            foreach ($filter_select as $key => $value) {
                if ($key !== 'lottery_last_drawn' && $key !== 'lottery_highlights') {
                    log_message('info', "  $key = " . (is_array($value) ? print_r($value, true) : $value));
                }
            }
            $filter_debug_done = true;
        }
        
        // For independent extra ball lotteries, separate main numbers from extra ball
        $main_numbers = $combo;
        $has_extra_ball = false;
        if (!empty($filter_select['duplicate_extra_ball']) && !empty($filter_select['extra_ball']) && isset($combo['extra'])) {
            $has_extra_ball = true;
            // Remove extra ball from main numbers for filtering calculations
            unset($main_numbers['extra']);
        }
        
        // Convert associative array (ball1, ball2, etc.) to indexed array of values for calculations
        $main_numbers_values = array_values($main_numbers);
        
        // Check winning sums filter
        if (!empty($filter_select['selected_winning_sums']) && $filter_select['selected_winning_sums'] !== 'ALL') {
            $sum = array_sum($main_numbers_values); // Use only main numbers for sum calculation
            $allowed_sums = explode(',', $filter_select['selected_winning_sums']);
            
            // Enhanced debugging - log all filter values on first iteration
            static $first_filter_debug = true;
            if ($first_filter_debug) {
                log_message('debug', "FILTER DEBUG - All filter values: " . print_r($filter_select, true));
                log_message('debug', "FILTER DEBUG - Sum filter raw value: '{$filter_select['selected_winning_sums']}'");
                log_message('debug', "FILTER DEBUG - Allowed sums array: " . print_r($allowed_sums, true));
                $first_filter_debug = false;
            }
            
            if (!in_array($sum, $allowed_sums)) {
                // Log first few failed sum checks for debugging
                static $sum_debug_count = 0;
                if ($sum_debug_count < 5) {
                    log_message('debug', "Sum filter failed: calculated sum {$sum}, allowed sums: " . implode(',', $allowed_sums) . ", main numbers: " . implode(',', $main_numbers_values));
                    $sum_debug_count++;
                }
                return false;
            }
        }
        
        // Check winning digits filter
        if (!empty($filter_select['selected_winning_digits']) && $filter_select['selected_winning_digits'] !== 'ALL') {
            $digit_sum = array_sum(array_map(function($num) {
                return array_sum(str_split($num));
            }, $main_numbers_values)); // Use only main numbers for digit sum
            $allowed_digits = explode(',', $filter_select['selected_winning_digits']);
            if (!in_array($digit_sum, $allowed_digits)) {
                return false;
            }
        }
        
        // Check repeaters filter
        if (!empty($filter_select['selected_repeaters']) && $filter_select['selected_repeaters'] !== 'ALL') {
            $lottery_highlights = $filter_select['lottery_highlights'] ?? [];
            $max = $lottery_highlights['range'] ?? 49;
            $last_draw = $filter_select['lottery_last_drawn'] ?? [];
            
            if (!$this->is_repeater($main_numbers_values, $max, $last_draw)) { // Use main numbers only
                return false;
            }
        }
        
        // Check consecutives filter
        if (!empty($filter_select['selected_consecutives']) && $filter_select['selected_consecutives'] !== 'ALL') {
            $lottery_highlights = $filter_select['lottery_highlights'] ?? [];
            $max = $lottery_highlights['range'] ?? 49;
            
            if (!$this->has_consecutive($main_numbers_values, $max)) { // Use main numbers only
                return false;
            }
        }
        
        // Check parity filter
        if (!empty($filter_select['selected_parity']) && $filter_select['selected_parity'] !== 'ALL') {
            $even_count = 0;
            $odd_count = 0;
            foreach ($main_numbers_values as $number) { // Use main numbers only for parity calculation
                if ($number % 2 == 0) {
                    $even_count++;
                } else {
                    $odd_count++;
                }
            }
            
            $parity_ratio = $even_count . '-' . $odd_count;
            $allowed_parity = explode(',', $filter_select['selected_parity']);
            if (!in_array($parity_ratio, $allowed_parity)) {
                return false;
            }
        }
        
        // Check decades filter
        if (!empty($filter_select['selected_decades']) && $filter_select['selected_decades'] !== 'ALL') {
            $lottery_highlights = $filter_select['lottery_highlights'] ?? [];
            $max = $lottery_highlights['range'] ?? 49;
            $decade_counts = $this->count_decade_numbers($main_numbers_values, $max); // Use main numbers only
            
            $allowed_decades = explode(',', $filter_select['selected_decades']);
            $combo_decade_pattern = implode('-', $decade_counts);
            if (!in_array($combo_decade_pattern, $allowed_decades)) {
                return false;
            }
        }
        
        // Check last digits filter
        if (!empty($filter_select['selected_last_digits']) && $filter_select['selected_last_digits'] !== 'ALL') {
            $lottery_highlights = $filter_select['lottery_highlights'] ?? [];
            $max = $lottery_highlights['range'] ?? 49;
            $last_digit_counts = $this->count_last_digit_numbers($main_numbers_values, $max); // Use main numbers only
            
            $allowed_last_digits = explode(',', $filter_select['selected_last_digits']);
            $combo_last_digit_pattern = implode('-', $last_digit_counts);
            if (!in_array($combo_last_digit_pattern, $allowed_last_digits)) {
                return false;
            }
        }
        
        // Check number range filter
        if (!empty($filter_select['selected_number_range']) && $filter_select['selected_number_range'] !== 'ALL') {
            $min_number = min($main_numbers_values); // Use main numbers only
            $max_number = max($main_numbers_values); // Use main numbers only
            $range = $max_number - $min_number;
            
            $allowed_ranges = explode(',', $filter_select['selected_number_range']);
            if (!in_array($range, $allowed_ranges)) {
                return false;
            }
        }
        
        // Check adjacents filter
        if (!empty($filter_select['selected_adjacents']) && $filter_select['selected_adjacents'] !== 'ALL') {
            $adjacent_count = 0;
            $sorted_main = $main_numbers_values; // Use main numbers only
            sort($sorted_main);
            for ($i = 0; $i < count($sorted_main) - 1; $i++) {
                if ($sorted_main[$i + 1] - $sorted_main[$i] == 1) {
                    $adjacent_count++;
                }
            }
            
            $allowed_adjacents = explode(',', $filter_select['selected_adjacents']);
            if (!in_array($adjacent_count, $allowed_adjacents)) {
                return false;
            }
        }
        
        // Filter by selected extra ball (for independent extra ball lotteries) - MUST COME FIRST TO MATCH PREDICTIONS_M
        if (isset($filter_select['selected_extra_ball']) && 
            $filter_select['selected_extra_ball'] !== 'ALL' && 
            !empty($filter_select['duplicate_extra_ball']) && 
            !empty($filter_select['extra_ball'])) {
            
            // Check if this combination has the selected extra ball
            $selected_extra_ball = (int)$filter_select['selected_extra_ball'];
            if (isset($combo['extra']) && (int)$combo['extra'] !== $selected_extra_ball) {
                log_message('info', "apply_other_filters (Combination_filters_m): Extra ball filter rejecting combo - expected: {$selected_extra_ball}, actual: " . $combo['extra']);
                return false;
            }
            log_message('info', "apply_other_filters (Combination_filters_m): Extra ball filter passed - expected: {$selected_extra_ball}, actual: " . $combo['extra']);
        }
        
        // Filter by H-W-C group (Hot-Warm-Cold) - APPLIED AFTER EXTRA BALL FILTER
        if (isset($filter_select['selected_h_w_c_group']) && 
            $filter_select['selected_h_w_c_group'] !== '' && 
            $filter_select['selected_h_w_c_group'] !== 'ALL') {
            
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
                    
                    // Get H-W-C classification for each number in the combination
                    $combo_numbers = array_values($main_numbers); // Use main numbers only (excludes extra ball)
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
                        log_message('info', "apply_other_filters (Combination_filters_m): H-W-C filter rejecting combo - expected: {$expected_hot}-{$expected_warm}-{$expected_cold}, actual: {$hot_count}-{$warm_count}-{$cold_count}");
                        return false;
                    }
                    log_message('info', "apply_other_filters (Combination_filters_m): H-W-C filter passed - expected: {$expected_hot}-{$expected_warm}-{$expected_cold}, actual: {$hot_count}-{$warm_count}-{$cold_count}");
                }
            }
        }
        
        // If we reach here, combination passed all filters
        $combo_str = is_array($combo) ? implode(',', array_slice(array_values($combo), 0, 5)) : 'invalid';
        log_message('info', "apply_other_filters (Combination_filters_m): PASSED all filters - combo: $combo_str");
        return true;
    }

    /**
     * Check trend match for combination
     *
     * @param array $combo Combination to check
     * @param array $last_drawn_numbers Last drawn numbers
     * @param string $trend Trend to match
     * @return bool True if trend matches
     */
    private function check_trend_match($combo, $last_drawn_numbers, $trend)
    {
        if (empty($last_drawn_numbers) || $trend === 'ALL') {
            return true;
        }
        
        $matches = count(array_intersect($combo, $last_drawn_numbers));
        
        switch ($trend) {
            case 'hot':
                return $matches >= 3;
            case 'warm':
                return $matches >= 2 && $matches <= 3;
            case 'cold':
                return $matches <= 1;
            case 'none':
                return $matches == 0;
            default:
                if (is_numeric($trend)) {
                    return $matches == intval($trend);
                }
                return true;
        }
    }

    /**
     * Check if combination contains repeaters
     *
     * @param array $combo Combination to check
     * @param int $max Maximum number in range
     * @param array $last_draw Last draw data
     * @return bool True if has repeaters
     */
    public function is_repeater($combo, $max, $last_draw)
    {
        if (empty($last_draw)) {
            return false;
        }
        
        $last_drawn_numbers = [];
        foreach ($last_draw as $key => $value) {
            if (strpos($key, 'ball') === 0 && is_numeric($value)) {
                $last_drawn_numbers[] = intval($value);
            }
        }
        
        $repeaters = array_intersect($combo, $last_drawn_numbers);
        return count($repeaters) > 0;
    }

    /**
     * Check if combination has consecutive numbers
     *
     * @param array $combo Combination to check
     * @param int $max Maximum number in range
     * @return bool True if has consecutives
     */
    public function has_consecutive($combo, $max)
    {
        sort($combo);
        for ($i = 0; $i < count($combo) - 1; $i++) {
            if ($combo[$i + 1] - $combo[$i] == 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Count numbers by decade
     *
     * @param array $combo Combination to analyze
     * @param int $max Maximum number in range
     * @return array Decade counts
     */
    public function count_decade_numbers($combo, $max)
    {
        $decades = [];
        $max_decade = floor($max / 10);
        
        // Initialize decade counters
        for ($i = 0; $i <= $max_decade; $i++) {
            $decades[$i] = 0;
        }
        
        // Count numbers in each decade
        foreach ($combo as $number) {
            $decade = floor($number / 10);
            if (isset($decades[$decade])) {
                $decades[$decade]++;
            }
        }
        
        return array_values($decades);
    }

    /**
     * Count numbers by last digit
     *
     * @param array $combo Combination to analyze
     * @param int $max Maximum number in range
     * @return array Last digit counts
     */
    public function count_last_digit_numbers($combo, $max)
    {
        $last_digits = array_fill(0, 10, 0);
        
        // Count numbers by last digit
        foreach ($combo as $number) {
            $last_digit = $number % 10;
            $last_digits[$last_digit]++;
        }
        
        return $last_digits;
    }
    /**
     * Get saved settings from lottery_combination_filters table by record ID or combo_id
     * 
     * @param int $id The record ID or combo_id to retrieve settings for
     * @return array|false The saved settings array or false if not found
     */
    public function get_saved_settings($id)
    {
        // First try to find by record id
        $this->db->where('id', $id);
        $this->db->limit(1);
        
        $query = $this->db->get('lottery_combination_filters');
        
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        
        // If not found by id, try by combo_id
        $this->db->where('combo_id', $id);
        $this->db->order_by('id', 'DESC'); // Get the most recent record if multiple exist
        $this->db->limit(1);
        
        $query = $this->db->get('lottery_combination_filters');
        
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        
        return false;
    }

    /**
     * Extract original filename by removing ADMIN## suffix
     * 
     * @param string $filename The filename with ADMIN suffix (e.g., "0612924ADMIN01")
     * @return string The original filename (e.g., "0612924")
     */
    public function extract_original_filename($filename)
    {
        // Remove ADMIN## pattern from the end of filename
        $pattern = '/ADMIN\d+$/';
        return preg_replace($pattern, '', $filename);
    }

    /**
     * Check if saved settings exist for a combo_id
     * 
     * @param int $combo_id The combination ID to check
     * @return bool True if settings exist, false otherwise
     */
    public function has_saved_settings($combo_id)
    {
        $this->db->where('combo_id', $combo_id);
        $this->db->where('active', 1);
        
        $query = $this->db->get('lottery_combination_filters');
        
        return $query->num_rows() > 0;
    }

    /**
     * Get all saved filter records for a specific combo_id
     * 
     * @param int $combo_id The combination ID
     * @return array Array of saved filter records
     */
    public function get_all_saved_settings($combo_id)
    {
        $this->db->where('combo_id', $combo_id);
        $this->db->where('active', 1);
        $this->db->order_by('id', 'DESC');
        
        $query = $this->db->get('lottery_combination_filters');
        
        return $query->result_array();
    }
    /**
     * Get active flag from record ID or combo_id
     *
     * @param   int         $id Record ID or combo_id
     * @return  boolean     TRUE on active flag, FALSE on expired
     */
    public function get_active_flag($id)
    {
        // First try to find by record id
        $this->db->select('active');
        $this->db->where('id', $id);
        $query = $this->db->get('lottery_combination_filters');
        
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->active == 1;
        }
        
        // If not found by id, try by combo_id
        $this->db->select('active');
        $this->db->where('combo_id', $id);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('lottery_combination_filters');
        
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->active == 1;
        }
        
        return FALSE;
    }
}
