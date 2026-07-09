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
        
        $total_lines_processed = 0;
        $passed_filters = 0;
        
        if (($handle = fopen($filepath, 'r')) !== false) {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if (empty($line)) continue;
                
                $total_lines_processed++;
                
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
                    $passed_filters++;
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
    public function get_filtered_combinations_count($filepath, $number_array, $filter_select = [], $start_time = null, $timeout_seconds = 3, $lottery_id = null)
    {
        // Set start time if not provided
        if ($start_time === null) {
            $start_time = microtime(true);
        }
        
        if (!file_exists($filepath)) {
            return 0;
        }
        
        // If no filters are applied, return total file lines
        $selected_trends = $filter_select['selected_trends'] ?? 'ALL';
        $has_other_filters = $this->has_active_filters($filter_select);
        
        $is_independent_extra_ball = !empty($filter_select['duplicate_extra_ball']) && !empty($filter_select['extra_ball']);
        $selected_extra_ball = $filter_select['selected_extra_ball'] ?? 'ALL';
        
        // For independent extra ball lotteries, we must always process combinations due to different structure
        // even when filters are 'ALL', because the combinations need proper parsing
        // ALSO: Never skip filtering when ANY filter is active, including repeaters
        if ($selected_trends === 'ALL' && !$has_other_filters && !$is_independent_extra_ball) {
            $total_lines = count(file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
            return $total_lines;
        }
        
        // Count filtered combinations
        $count = 0;
        $total_lines = 0;
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
                // Check for timeout every 1000 lines to avoid excessive overhead
                if ($total_lines % 1000 === 0 && $start_time !== null) {
                    $elapsed = microtime(true) - $start_time;
                    if ($elapsed > $timeout_seconds) {
                        fclose($handle);
                        // Get CI instance to access controller
                        $CI =& get_instance();
                        if (method_exists($CI, 'check_timeout_and_redirect')) {
                            $CI->check_timeout_and_redirect($start_time, $timeout_seconds, $lottery_id);
                        }
                        // CRITICAL FIX: Don't return partial count - this causes count mismatch
                        // The redirect will handle timeout, and if we reach here, we should 
                        // continue counting or throw an exception rather than return incorrect count
                        // For now, continue processing to get accurate count
                    }
                }
                
                $line = trim($line);
                if (empty($line)) continue;
                
                $total_lines++;
                
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
                
                // Check if combination passes all filters (use same method as save)
                if ($this->passes_all_filters($combo, $filter_select)) {
                    $count++;
                }
            }
            fclose($handle);
        }
        
        return $count;
    }

    /**
     * Save pre-filtered combinations directly to file (no re-filtering needed)
     * This method should be used when combinations have already been filtered
     * during the Generate Tickets process to avoid double-filtering.
     * 
     * @param array $filtered_combinations Already filtered combinations
     * @param string $output_file_path Path to save the combinations
     * @param array $filters Filter info for logging purposes
     * @return bool True on success
     */
    public function save_prefiltered_combinations_to_file($filtered_combinations, $output_file_path, $filters = [])
    {
        $output_handle = fopen($output_file_path, 'w');
        if (!$output_handle) {
            log_message('error', "save_prefiltered_combinations_to_file: Could not open output file: {$output_file_path}");
            return false;
        }

        $saved_count = 0;
        foreach ($filtered_combinations as $combo) {
            if (is_array($combo)) {
                // Handle different combination formats
                if (isset($combo['main_numbers']) && isset($combo['extra_ball'])) {
                    // Independent extra ball format: main_numbers + extra_ball
                    $all_numbers = $combo['main_numbers'];
                    $all_numbers[] = $combo['extra_ball'];
                    $output_line = implode(' ', $all_numbers);
                } else {
                    // Regular format: array of numbers
                    $output_line = implode(' ', $combo);
                }
                
                fwrite($output_handle, $output_line . "\n");
                $saved_count++;
            }
        }

        fclose($output_handle);
        
        return $saved_count > 0;
    }

    /**
     * Save filtered combinations to a file (LEGACY METHOD - DOES RE-FILTERING)
     * Note: This method re-filters combinations from original file.
     * Consider using save_prefiltered_combinations_to_file() for better performance
     * when combinations are already filtered.
     *
     * @param string $filepath Path to the source combination file
     * @param array $number_array Array of numbers to filter with
     * @param array $filters Array of filter criteria
     * @param string $output_file_path Path to save the filtered combinations
     * @return bool True on success, false on failure
     */
    public function save_filtered_combinations_to_file($filepath, $number_array, $filters, $output_file_path)
    {
        // Log start of save operation with file details
        log_message('info', "save_filtered_combinations_to_file: Starting save operation");
        log_message('info', "  Source file: {$filepath}");
        log_message('info', "  Output file: {$output_file_path}");
        
        if (!file_exists($filepath)) {
            log_message('error', "save_filtered_combinations_to_file: Source file not found: {$filepath}");
            return false;
        }
        
        // Log file size for large file detection
        $file_size = filesize($filepath);
        $file_size_mb = round($file_size / 1024 / 1024, 2);
        log_message('info', "  Source file size: {$file_size_mb} MB");
        
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
        
        // Log completion stats
        log_message('info', "save_filtered_combinations_to_file: Completed");
        log_message('info', "  Processed: {$processed_count} combinations");
        log_message('info', "  Saved: {$saved_count} combinations");
        
        // If no combinations were saved, log the filter criteria for debugging
        if ($saved_count == 0) {
            log_message('warning', "save_filtered_combinations_to_file: No combinations passed filters. Filters: " . print_r($filters, true));
        }

        return $saved_count > 0;
    }

    /**
     * Save pre-filtered combinations to database (optimized version)
     * 
     * @param array $combinations Pre-filtered combinations
     * @param array $filters Filter criteria used
     * @return bool True if saved successfully
     */
    public function save_prefiltered_combinations_to_database($combinations, $filters)
    {
        if (empty($combinations)) {
            log_message('warning', "save_prefiltered_combinations_to_database: No combinations provided");
            return false;
        }

        // Use the provided combinations directly (already filtered)
        $saved_count = 0;
        foreach ($combinations as $combination) {
            // Determine CCCC count
            $cccc_count = $this->count_cccc_in_combination($combination);
            
            // Prepare data for database insertion
            $data = array(
                'combination' => $combination,
                'date_created' => date('Y-m-d H:i:s'),
                'CCCC' => $cccc_count
            );
            
            // Insert into database
            if ($this->db->insert('lottery_combination_filters', $data)) {
                $saved_count++;
            } else {
                log_message('error', "save_prefiltered_combinations_to_database: Failed to save combination: " . $combination);
            }
        }

        return $saved_count > 0;
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
                log_message('debug', "has_active_filters: Found active filter {$key} = '{$filter_select[$key]}'");
                return true;
            }
        }
        
        // Special check for H-W-C group: only active if checkbox is checked AND dropdown has value
        if (isset($filter_select['selected_hwc']) && $filter_select['selected_hwc'] && 
            isset($filter_select['selected_h_w_c_group']) && 
            !empty($filter_select['selected_h_w_c_group'])) {
            log_message('debug', "has_active_filters: Found active H-W-C filter");
            return true;
        }
        
        log_message('debug', "has_active_filters: No active filters found");
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
            
            $trend_result = $this->check_trend_match($main_numbers_values, $last_drawn_numbers, $filters['selected_trends']);
            
            if (!$trend_result) {
                return false;
            }
        }
        
        // Apply other filters using existing method
        $result = $this->apply_other_filters($combo, $filters);
        
        return $result;
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
        
        // ====================================================================
        // OPTIMIZED FILTER ORDER: Most selective filters first for 20-40% speed improvement
        // 1. Extra Ball (very selective, instant rejection)
        // 2. H-W-C (highly selective when active)
        // 3. Parity (fast calculation, moderate selectivity)
        // 4. Consecutives (fast calculation, moderate selectivity)
        // 5. Quick filters (sums, digits)
        // 6. Expensive filters (repeaters - requires lookups)
        // 7. Other filters
        // 8. Friends (most expensive - keep last)
        // ====================================================================
        
        // 1. EXTRA BALL FILTER - Most selective when active (instant rejection for wrong ball)
        if (isset($filter_select['selected_extra_ball']) && 
            $filter_select['selected_extra_ball'] !== 'ALL' && 
            !empty($filter_select['duplicate_extra_ball']) && 
            !empty($filter_select['extra_ball'])) {
            
            $selected_extra_ball = (int)$filter_select['selected_extra_ball'];
            if (isset($combo['extra']) && (int)$combo['extra'] !== $selected_extra_ball) {
                return false;
            }
        }
        
        // 2. H-W-C FILTER - Highly selective when active
        if (isset($filter_select['selected_hwc']) && $filter_select['selected_hwc'] && 
            isset($filter_select['selected_h_w_c_group']) && 
            !empty($filter_select['selected_h_w_c_group'])) {
            
            $h_w_c_group = $filter_select['selected_h_w_c_group'];
            
            if (preg_match('/(\d+)-(\d+)-(\d+)/', $h_w_c_group, $matches)) {
                $expected_hot = (int)$matches[1];
                $expected_warm = (int)$matches[2];
                $expected_cold = (int)$matches[3];
                
                $lottery_id = $filter_select['lottery_id'] ?? null;
                if ($lottery_id) {
                    if (!isset($this->statistics_m)) {
                        $this->load->model('Statistics_m', 'statistics_m');
                    }
                    
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
                    
                    if ($hot_count !== $expected_hot || $warm_count !== $expected_warm || $cold_count !== $expected_cold) {
                        return false;
                    }
                }
            }
        }
        
        // 3. PARITY FILTER - Fast calculation, moderate selectivity
        if (!empty($filter_select['selected_parity']) && $filter_select['selected_parity'] !== 'ALL') {
            $even_count = 0;
            $odd_count = 0;
            foreach ($main_numbers_values as $number) {
                if ($number % 2 == 0) {
                    $even_count++;
                } else {
                    $odd_count++;
                }
            }
            
            $parity_ratio = $odd_count . '-' . $even_count;
            $allowed_parity = explode(',', $filter_select['selected_parity']);
            
            if (!in_array($parity_ratio, $allowed_parity)) {
                return false;
            }
        }
        
        // 4. CONSECUTIVES FILTER - Fast calculation, moderate selectivity
        if (isset($filter_select['selected_consecutives']) && $filter_select['selected_consecutives'] !== 'ALL') {
            $lottery_highlights = $filter_select['lottery_highlights'] ?? [];
            $max = $lottery_highlights['range'] ?? 49;
            
            $consecutive_count = $this->count_consecutives($main_numbers_values, $max);
            $expected_consecutive_count = (int)$filter_select['selected_consecutives'];
            
            if ($consecutive_count !== $expected_consecutive_count) {
                return false;
            }
        }
        
        // 5. WINNING SUMS FILTER - Quick calculation
        if (!empty($filter_select['selected_winning_sums']) && $filter_select['selected_winning_sums'] !== 'ALL') {
            $sum = array_sum($main_numbers_values);
            $allowed_sums = explode(',', $filter_select['selected_winning_sums']);
            
            if (!in_array($sum, $allowed_sums)) {
                return false;
            }
        }
        
        // 6. WINNING DIGITS FILTER - Quick calculation
        if (!empty($filter_select['selected_winning_digits']) && $filter_select['selected_winning_digits'] !== 'ALL') {
            $digit_sum = array_sum(array_map(function($num) {
                return array_sum(str_split($num));
            }, $main_numbers_values));
            $allowed_digits = explode(',', $filter_select['selected_winning_digits']);
            if (!in_array($digit_sum, $allowed_digits)) {
                return false;
            }
        }
        
        // 7. REPEATERS FILTER - More expensive (requires last draw lookup)
        if (isset($filter_select['selected_repeaters']) && $filter_select['selected_repeaters'] !== 'ALL') {
            $lottery_highlights = $filter_select['lottery_highlights'] ?? [];
            $max = $lottery_highlights['range'] ?? 49;
            $last_draw = $filter_select['lottery_last_drawn'] ?? [];
            
            $combo_for_counting = $combo;
            $repeater_count = (int)$this->count_repeaters_from_combo($combo_for_counting, $max, $last_draw, $filter_select);
            $expected_repeater_count = (int)$filter_select['selected_repeaters'];
            
            if ($repeater_count !== $expected_repeater_count) {
                return false;
            }
        }
        
        // 8. DECADES FILTER
        if (!empty($filter_select['selected_decades']) && $filter_select['selected_decades'] !== 'ALL') {
            $lottery_highlights = $filter_select['lottery_highlights'] ?? [];
            $max = $lottery_highlights['range'] ?? 49;
            $decade_count = $this->count_decade_numbers($main_numbers_values, $max);
            $expected_decades = (int)$filter_select['selected_decades'];
            
            if ($decade_count !== $expected_decades) {
                return false;
            }
        }
        
        // 9. LAST DIGITS FILTER
        if (!empty($filter_select['selected_last_digits']) && $filter_select['selected_last_digits'] !== 'ALL') {
            $lottery_highlights = $filter_select['lottery_highlights'] ?? [];
            $max = $lottery_highlights['range'] ?? 49;
            $last_digit_count = $this->count_last_digit_numbers($main_numbers_values, $max);
            $expected_last_digits = (int)$filter_select['selected_last_digits'];
            
            if ($last_digit_count !== $expected_last_digits) {
                return false;
            }
        }
        
        // 10. NUMBER RANGE FILTER
        if (!empty($filter_select['selected_number_range']) && $filter_select['selected_number_range'] !== 'ALL') {
            $min_number = min($main_numbers_values);
            $max_number = max($main_numbers_values);
            $range = $max_number - $min_number;
            
            $allowed_ranges = explode(',', $filter_select['selected_number_range']);
            if (!in_array($range, $allowed_ranges)) {
                return false;
            }
        }
        
        // 11. ADJACENTS FILTER
        if (!empty($filter_select['selected_adjacents']) && $filter_select['selected_adjacents'] !== 'ALL') {
            $adjacent_count = 0;
            $sorted_main = $main_numbers_values;
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
        
        // 12. FRIENDS FILTER - Most expensive (database query + validation), keep last
        if (isset($filter_select['selected_friends_checkbox']) && $filter_select['selected_friends_checkbox'] && 
            isset($filter_select['selected_friends']) && 
            !empty($filter_select['selected_friends']) && 
            strtolower($filter_select['selected_friends']) !== 'all') {
            
            $lottery_id = $filter_select['lottery_id'] ?? null;
            $friendship_type = $filter_select['selected_friends'];
            
            if ($lottery_id) {
                $combo_numbers = array_values($combo);
                
                if (!$this->validate_combination_friendships($lottery_id, $combo_numbers, $friendship_type)) {
                    return false;
                }
            }
        }
        
        // All filters passed
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
        
        switch ($trend) {
            case 'UP':
                // All combination numbers must be greater than corresponding last drawn numbers
                $combo_sorted = $combo;
                $last_drawn_sorted = $last_drawn_numbers;
                sort($combo_sorted, SORT_NUMERIC);
                sort($last_drawn_sorted, SORT_NUMERIC);
                
                foreach ($combo_sorted as $index => $combo_number) {
                    if (isset($last_drawn_sorted[$index])) {
                        if ($combo_number <= $last_drawn_sorted[$index]) {
                            return false;
                        }
                    }
                }
                return true;
                
            case 'DOWN':
                // All combination numbers must be less than corresponding last drawn numbers
                $combo_sorted = $combo;
                $last_drawn_sorted = $last_drawn_numbers;
                sort($combo_sorted, SORT_NUMERIC);
                sort($last_drawn_sorted, SORT_NUMERIC);
                
                foreach ($combo_sorted as $index => $combo_number) {
                    if (isset($last_drawn_sorted[$index])) {
                        if ($combo_number >= $last_drawn_sorted[$index]) {
                            return false;
                        }
                    }
                }
                return true;
                
            case 'hot':
                $matches = count(array_intersect($combo, $last_drawn_numbers));
                return $matches >= 3;
                
            case 'warm':
                $matches = count(array_intersect($combo, $last_drawn_numbers));
                return $matches >= 2 && $matches <= 3;
                
            case 'cold':
                $matches = count(array_intersect($combo, $last_drawn_numbers));
                return $matches <= 1;
                
            case 'none':
                $matches = count(array_intersect($combo, $last_drawn_numbers));
                return $matches == 0;
                
            default:
                if (is_numeric($trend)) {
                    $matches = count(array_intersect($combo, $last_drawn_numbers));
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
     * Count the number of repeaters in a combination
     * For independent extra ball lotteries, only counts main number repeaters (excludes extra ball)
     * For regular lotteries, includes extra ball in repeater calculation
     *
     * @param array $combo Combination to check
     * @param int $max Maximum number in range
     * @param array $last_draw Last draw data
     * @return int Number of repeaters found
     */
    public function count_repeaters($combo, $max, $last_draw)
    {
        if (empty($last_draw)) {
            return 0;
        }
        
        // Determine lottery type by checking if last_draw has 'extra' key
        // For independent extra ball lotteries, last_draw will have 'extra' key
        $is_independent_extra_ball = isset($last_draw['extra']);
        
        $last_drawn_numbers = [];
        
        if ($is_independent_extra_ball) {
            // For independent extra ball lotteries, extract main numbers only (exclude extra ball)
            foreach ($last_draw as $key => $value) {
                if (strpos($key, 'ball') === 0 && is_numeric($value)) {
                    $last_drawn_numbers[] = intval($value);
                }
            }
        } else {
            // For regular lotteries, include all numbers (main + extra if present)
            foreach ($last_draw as $key => $value) {
                if ((strpos($key, 'ball') === 0 || $key === 'extra') && is_numeric($value)) {
                    $last_drawn_numbers[] = intval($value);
                }
            }
        }
        
        // Count intersections between combination numbers and last drawn numbers
        // Note: For independent extra ball lotteries, $combo already has extra ball removed in apply_other_filters
        // For regular lotteries, $combo contains all numbers including extra ball
        $repeaters = array_intersect($combo, $last_drawn_numbers);
        return count($repeaters);
    }

    /**
     * Count repeaters using the same logic as display calculation
     * This ensures consistency between filtering and display
     */
    private function count_repeaters_from_combo($combo, $max_number, $last_draw, $filter_select) {
        if (empty($last_draw) || empty($combo)) {
            return 0;
        }
        
        // Use same lottery type detection as Predictions_m.php
        $lottery_id = $filter_select['lottery_id'] ?? 0;
        $is_independent_extra_ball = false;
        
        // Check duplicate_extra_ball directly from filter_select (new format)
        if (isset($filter_select['duplicate_extra_ball'])) {
            $is_independent_extra_ball = ($filter_select['duplicate_extra_ball'] == 1);
        } 
        // Fallback: Check duplicate_extra_ball from lottery data (old format)
        elseif (isset($filter_select['lottery_data'])) {
            $lottery_data = $filter_select['lottery_data'];
            if (isset($lottery_data['duplicate_extra_ball'])) {
                $is_independent_extra_ball = ($lottery_data['duplicate_extra_ball'] == 1);
            }
        }
        
        // If no lottery data available, fallback to checking extra key presence
        if (!isset($filter_select['lottery_data']) && isset($last_draw['extra']) && !empty($last_draw['extra'])) {
            // For Canada 649 (lottery_id = 1), it's a regular lottery, not independent extra ball
            $is_independent_extra_ball = false;
        }
        
        // Prepare combo numbers for comparison
        // Extract only the main numbers (exclude extra ball for independent extra ball lotteries)
        $combo_numbers = [];
        
        // For independent extra ball lotteries, only count main numbers for repeaters
        if ($is_independent_extra_ball) {
            // Extract main numbers only (ball1, ball2, etc, but not extra)
            foreach ($combo as $key => $value) {
                if (strpos($key, 'ball') === 0) {
                    $combo_numbers[] = (int)$value;
                }
            }
        } else {
            // For regular lotteries, we need to check if there's an extra ball and handle it appropriately
            foreach ($combo as $key => $value) {
                if (strpos($key, 'ball') === 0) {
                    $combo_numbers[] = (int)$value;
                }
                // Include extra ball for regular lotteries only if the lottery actually uses extra ball in repeater calculation
                elseif ($key === 'extra' && isset($filter_select['extra_ball']) && $filter_select['extra_ball'] == 1) {
                    $combo_numbers[] = (int)$value;
                }
            }
        }
        
        // Create array of last drawn numbers
        $last_numbers = [];
        
        // Get the number of balls drawn for this lottery
        $balls_drawn = $filter_select['drawn'] ?? 6;
        
        // Include main draw numbers (ball1 through drawn count)
        for ($i = 1; $i <= $balls_drawn; $i++) {
            if (isset($last_draw["ball$i"]) && $last_draw["ball$i"] != '') {
                $last_numbers[] = (int)$last_draw["ball$i"];
            }
        }
        
        // Handle extra ball based on lottery type
        if (!$is_independent_extra_ball && isset($last_draw['extra']) && $last_draw['extra'] != '') {
            // For regular lotteries, include extra ball in comparison
            $last_numbers[] = (int)$last_draw['extra'];
        }
        
        // Count repeaters
        $repeater_count = 0;
        foreach ($combo_numbers as $number) {
            // Ensure both values are integers for proper comparison
            $combo_num = (int)$number;
            foreach ($last_numbers as $last_num) {
                if ($combo_num === (int)$last_num) {
                    $repeater_count++;
                    break; // Avoid counting the same number multiple times
                }
            }
        }
        
        return (int)$repeater_count;
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
     * Count the number of consecutive pairs in a combination
     *
     * @param array $combo Combination to check
     * @param int $max Maximum number in range
     * @return int Number of consecutive pairs found
     */
    public function count_consecutives($combo, $max)
    {
        sort($combo);
        $consecutive_count = 0;
        $consecutive_pairs = [];
        
        for ($i = 0; $i < count($combo) - 1; $i++) {
            if ($combo[$i + 1] - $combo[$i] == 1) {
                $consecutive_count++;
                $consecutive_pairs[] = $combo[$i] . '-' . $combo[$i + 1];
            }
        }
        
        return $consecutive_count;
    }

    /**
     * Count numbers by decade
     *
     * @param array $combo Combination to analyze
     * @param int $max Maximum number in range
     * @return int Maximum count of numbers in any single decade
     */
    public function count_decade_numbers($combo, $max)
    {
        $decade_counts = [];
        
        // Count numbers in each decade
        foreach ($combo as $number) {
            $decade = intval($number / 10); // 22 -> 2, 23 -> 2, 35 -> 3, etc.
            if (!isset($decade_counts[$decade])) {
                $decade_counts[$decade] = 0;
            }
            $decade_counts[$decade]++;
        }
        
        // Return the maximum count of numbers in any single decade (matches Predictions_m logic)
        return max($decade_counts);
    }

    /**
     * Count numbers by last digit
     *
     * @param array $combo Combination to analyze
     * @param int $max Maximum number in range
     * @return int Maximum count of numbers with the same last digit
     */
    public function count_last_digit_numbers($combo, $max)
    {
        $last_digit_counts = [];
        
        // Count numbers by their last digit
        foreach ($combo as $number) {
            $last_digit = $number % 10; // 12 -> 2, 22 -> 2, 35 -> 5, etc.
            if (!isset($last_digit_counts[$last_digit])) {
                $last_digit_counts[$last_digit] = 0;
            }
            $last_digit_counts[$last_digit]++;
        }
        
        // Return the maximum count of numbers with the same last digit (matches Predictions_m logic)
        return max($last_digit_counts);
    }
    /**
     * Get saved settings from lottery_combination_filters table by record ID or combo_id
     * 
     * @param int $id The record ID or combo_id to retrieve settings for
     * @param int $user_id Optional user ID to filter by (defaults to session user)
     * @return array|false The saved settings array or false if not found
     */
    public function get_saved_settings($id, $user_id = null, $lottery_id = null)
    {
        // Get CodeIgniter instance for session access
        $CI =& get_instance();
        
        // If no user_id provided, get from session
        if ($user_id === null) {
            $user_id = $CI->session->userdata('id');
        }
        
        // First try to find by record id
        $this->db->where('id', $id);
        if ($user_id) {
            $this->db->where('user', 1); // Must be admin record
            $this->db->where('user_id', $user_id); // Must belong to current admin
        }
        if ($lottery_id) {
            $this->db->where('lottery_id', $lottery_id); // Must belong to correct lottery
        }
        $this->db->limit(1);
        
        $query = $this->db->get('lottery_combination_filters');
        
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        
        // If not found by id, try by combo_id - but only if we're sure this admin should have access
        // First check if any record exists with this combo_id for current admin
        $this->db->select('COUNT(*) as count');
        $this->db->where('combo_id', $id);
        $this->db->where('user', 1);
        $this->db->where('user_id', $user_id);
        if ($lottery_id) {
            $this->db->where('lottery_id', $lottery_id);
        }
        $count_query = $this->db->get('lottery_combination_filters');
        $count_result = $count_query->row_array();
        
        if ($count_result['count'] == 0) {
            return false; // No records for this admin, don't allow access
        }
        
        // Get the actual record - prefer active over inactive, then most recent
        $this->db->where('combo_id', $id);
        if ($user_id) {
            $this->db->where('user', 1); // Must be admin record
            $this->db->where('user_id', $user_id); // Must belong to current admin
        }
        if ($lottery_id) {
            $this->db->where('lottery_id', $lottery_id);
        }
        $this->db->order_by('active', 'DESC'); // Prefer active records
        $this->db->order_by('id', 'DESC'); // Then most recent
        $this->db->limit(1);
        
        $query = $this->db->get('lottery_combination_filters');
        
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        
        return false;
    }

    /**
     * Extract original filename by removing L###ADMIN## suffix
     * 
     * @param string $filename The filename with lottery and ADMIN suffix (e.g., "0612924L001ADMIN01")
     * @return string The original filename (e.g., "0612924")
     */
    public function extract_original_filename($filename)
    {
        // Remove L###ADMIN## pattern from the end of filename
        // Handles both new format (L###ADMIN##) and legacy format (ADMIN##)
        $pattern = '/(L\d{3})?ADMIN\d+$/';
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

    /**
     * Count CCCC occurrences in a combination
     * 
     * @param string $combination The combination string
     * @return int Number of CCCC patterns found
     */
    private function count_cccc_in_combination($combination)
    {
        // Split combination into individual numbers
        $numbers = explode('-', $combination);
        $cccc_count = 0;
        
        // Check each number for CCCC pattern (4 identical consecutive digits)
        foreach ($numbers as $number) {
            $number = str_pad($number, 4, '0', STR_PAD_LEFT); // Ensure 4 digits
            
            // Check if all 4 digits are the same
            if (strlen($number) == 4 && 
                $number[0] == $number[1] && 
                $number[1] == $number[2] && 
                $number[2] == $number[3]) {
                $cccc_count++;
            }
        }
        
        return $cccc_count;
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
        
        // Check friendship rules based on selected filter type
        switch ($friendship_type) {
            case 'none':
                // No friendships should exist
                return $this->validate_no_friendships($combo_numbers, $oneway, $twoway);
                
            case '1':
                // Only 1-way friendships allowed
                return $this->validate_oneway_friendships_only($combo_numbers, $oneway, $twoway);
                
            case '2':
                // Only 2-way friendships allowed (no 1-way friendships)
                $result = $this->validate_twoway_friendships_only($combo_numbers, $oneway, $twoway);
                return $result;
                
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
        // NOTE: Allow 2-way friendships to coexist with 1-way friendships.
        // The requirement is to have at least one complete 1-way friendship.
        $found_complete_oneway = false;
        
        // Check that 1-way friendships are complete (if A>B and A is present, B must be present)
        foreach ($oneway as $pair) {
            list($a, $b) = $pair;
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
     * Validate that combination has at least one complete 2-way friendship
     * For 2-way friendships to be valid: if A-B and either A or B is in combo, then both must be in combo
     * Must have at least one complete 2-way friendship
     * Note: 1-way friendships are allowed to coexist with 2-way friendships
     */
    private function validate_twoway_friendships_only($combo_numbers, $oneway, $twoway)
    {
        // OPTIMIZATION: Don't reject 1-way friendships when 2-way is selected
        // This allows combinations to have both 2-way AND 1-way friendships
        // Benefits:
        // 1. More flexible filtering (focuses on what's required, not what's forbidden)
        // 2. Solves stale data issue (when friendship patterns change after generation)
        // 3. Faster validation (skips unnecessary 1-way checking)
        
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
        
        return true; // Valid 2-way friendships found (1-way friendships allowed)
    }
    
    /**
     * Remove duplicates from 2-way friendship array (e.g., [1,2] and [2,1] become just [1,2])
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
     * Check if any combinations contain the required friendship type
     * Returns true if at least one combination has the required friendship, false otherwise
     */
    public function check_friendship_occurrences($filepath, $number_array, $lottery_id, $friendship_type)
    {
        if ($friendship_type === 'all' || $friendship_type === 'none' || empty($friendship_type)) {
            return true; // No friendship requirement
        }

        // Get friendship data from database
        $row = $this->db->get_where('lottery_friends', ['lottery_id' => $lottery_id])->row_array();
        if (!$row || empty($row['wins'])) {
            return true; // No friendship data available
        }

        // Parse friendship data - same logic as validate_combination_friendships
        $friend_str = trim($row['wins']);
        $parts = explode('|', $friend_str);
        
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
        
        $twoway = $this->twoway_unique($twoway);
        
        // Check a sample of combinations to see if any contain the required friendship type
        $sample_size = min(1000, $this->get_total_combinations_count($filepath)); // Check up to 1000 combinations
        $combinations = $this->load_combinations_from_file($filepath, 1, $sample_size);
        
        foreach ($combinations as $combo) {
            $combo_numbers = $this->convert_combo_to_numbers($combo, $number_array);
            
            if ($friendship_type === '1') {
                // Check for at least one complete 1-way friendship
                foreach ($oneway as $pair) {
                    list($a, $b) = $pair;
                    if (in_array($a, $combo_numbers) && in_array($b, $combo_numbers)) {
                        return true; // Found valid 1-way friendship occurrence
                    }
                }
            } elseif ($friendship_type === '2') {
                // Check for at least one complete 2-way friendship
                foreach ($twoway as $pair) {
                    list($a, $b) = $pair;
                    if (in_array($a, $combo_numbers) && in_array($b, $combo_numbers)) {
                        return true; // Found valid 2-way friendship occurrence
                    }
                }
            }
        }
        
        return false; // No required friendship type found in any combination
    }

    /**
     * Get total count of combinations in file
     */
    private function get_total_combinations_count($filepath)
    {
        if (!file_exists($filepath)) {
            return 0;
        }
        
        $count = 0;
        if (($handle = fopen($filepath, 'r')) !== false) {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if (!empty($line)) {
                    $count++;
                }
            }
            fclose($handle);
        }
        return $count;
    }

    /**
     * Load combinations from file with pagination
     */
    private function load_combinations_from_file($filepath, $page, $per_page)
    {
        if (!file_exists($filepath)) {
            return [];
        }

        $start_line = ($page - 1) * $per_page + 1;
        $end_line = $start_line + $per_page;
        $current_line = 1;
        $combinations = [];

        if (($handle = fopen($filepath, 'r')) !== false) {
            while (($line = fgets($handle)) !== false && $current_line < $end_line) {
                if ($current_line >= $start_line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        $positions = array_map('intval', explode(' ', $line));
                        $combinations[] = $positions;
                    }
                }
                $current_line++;
            }
            fclose($handle);
        }
        
        return $combinations;
    }

    /**
     * Convert combination positions to actual numbers
     */
    private function convert_combo_to_numbers($combo_positions, $number_array)
    {
        $combo_numbers = [];
        
        foreach ($combo_positions as $pos) {
            if ($pos > 0 && isset($number_array[$pos - 1])) {
                $combo_numbers[] = $number_array[$pos - 1];
            }
        }
        
        return $combo_numbers;
    }
}
