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
                            foreach ($positions as $pos) {
                                if ($pos > 0 && isset($number_array[$pos - 1])) {
                                    $combo_numbers[] = $number_array[$pos - 1];
                                }
                            }
                            if (!empty($combo_numbers)) {
                                sort($combo_numbers, SORT_NUMERIC);
                                $combinations[] = $combo_numbers;
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
                if ($this->passes_all_filters($combo, $filter_select)) {
                    if ($count >= $skip_count) {
                        $combinations[] = $combo_numbers;
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
            if ($this->passes_all_filters($combo, $filters)) {
                fwrite($output_handle, $line . "\n");
                $saved_count++;
            }
        }

        fclose($handle);
        fclose($output_handle);

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
            'selected_last_digits', 'selected_number_range', 'selected_adjacents'
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
     * Apply other filters (non-trend filters)
     *
     * @param array $combo Combination to check
     * @param array $filter_select Filter criteria
     * @return bool True if combination passes filters
     */
    private function apply_other_filters($combo, $filter_select)
    {
        // Check winning sums filter
        if (!empty($filter_select['selected_winning_sums']) && $filter_select['selected_winning_sums'] !== 'ALL') {
            $sum = array_sum($combo);
            $allowed_sums = explode(',', $filter_select['selected_winning_sums']);
            if (!in_array($sum, $allowed_sums)) {
                return false;
            }
        }
        
        // Check winning digits filter
        if (!empty($filter_select['selected_winning_digits']) && $filter_select['selected_winning_digits'] !== 'ALL') {
            $digit_sum = array_sum(array_map(function($num) {
                return array_sum(str_split($num));
            }, $combo));
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
            
            if (!$this->is_repeater($combo, $max, $last_draw)) {
                return false;
            }
        }
        
        // Check consecutives filter
        if (!empty($filter_select['selected_consecutives']) && $filter_select['selected_consecutives'] !== 'ALL') {
            $lottery_highlights = $filter_select['lottery_highlights'] ?? [];
            $max = $lottery_highlights['range'] ?? 49;
            
            if (!$this->has_consecutive($combo, $max)) {
                return false;
            }
        }
        
        // Check parity filter
        if (!empty($filter_select['selected_parity']) && $filter_select['selected_parity'] !== 'ALL') {
            $even_count = 0;
            $odd_count = 0;
            foreach ($combo as $number) {
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
            $decade_counts = $this->count_decade_numbers($combo, $max);
            
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
            $last_digit_counts = $this->count_last_digit_numbers($combo, $max);
            
            $allowed_last_digits = explode(',', $filter_select['selected_last_digits']);
            $combo_last_digit_pattern = implode('-', $last_digit_counts);
            if (!in_array($combo_last_digit_pattern, $allowed_last_digits)) {
                return false;
            }
        }
        
        // Check number range filter
        if (!empty($filter_select['selected_number_range']) && $filter_select['selected_number_range'] !== 'ALL') {
            $min_number = min($combo);
            $max_number = max($combo);
            $range = $max_number - $min_number;
            
            $allowed_ranges = explode(',', $filter_select['selected_number_range']);
            if (!in_array($range, $allowed_ranges)) {
                return false;
            }
        }
        
        // Check adjacents filter
        if (!empty($filter_select['selected_adjacents']) && $filter_select['selected_adjacents'] !== 'ALL') {
            $adjacent_count = 0;
            sort($combo);
            for ($i = 0; $i < count($combo) - 1; $i++) {
                if ($combo[$i + 1] - $combo[$i] == 1) {
                    $adjacent_count++;
                }
            }
            
            $allowed_adjacents = explode(',', $filter_select['selected_adjacents']);
            if (!in_array($adjacent_count, $allowed_adjacents)) {
                return false;
            }
        }
        
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
}
