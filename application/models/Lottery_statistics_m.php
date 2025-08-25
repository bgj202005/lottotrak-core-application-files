<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lottery Statistics Model
 * Handles statistical analysis and calculations for lottery data
 */
class Lottery_statistics_m extends MY_Model
{
    protected $_table_name = 'lottery_statistics';
    protected $_order_by = 'id';

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
     *
     * @param array $last_drawn Array of last drawn numbers with points
     * @param int $balls_drawn Number of balls drawn
     * @param bool $duplicate Whether duplicate extra ball is allowed
     * @return array Sorted ball points
     */
    public function get_sorted_ball_points($last_drawn, $balls_drawn, $duplicate)
    {
        $ball_points = [];
        
        // Process regular balls
        for ($i = 1; $i <= $balls_drawn; $i++) {
            $ball_key = 'ball' . $i;
            if (isset($last_drawn[$ball_key])) {
                $number = $last_drawn[$ball_key];
                $total_key = 'ball' . $i . '_total';
                $points = isset($last_drawn[$total_key]) ? $last_drawn[$total_key] : 0;
                $ball_points[$number] = $points;
            }
        }
        
        // Process extra ball if present
        if (isset($last_drawn['extra']) && !empty($last_drawn['extra'])) {
            $extra_number = $last_drawn['extra'];
            $extra_points = isset($last_drawn['extra_total']) ? $last_drawn['extra_total'] : 0;
            $ball_points['+' . $extra_number] = $extra_points;
        }
        
        // Sort by points descending
        arsort($ball_points);
        
        // Format for display
        $formatted_points = [];
        foreach ($ball_points as $number => $points) {
            $formatted_points[] = $number . ' (' . $points . ')';
        }
        
        return $formatted_points;
    }

    /**
     * Returns an associative array of position numbers mapped to their total points
     *
     * @param array $last_drawn Array of last drawn numbers with points
     * @param int $balls_drawn Number of balls drawn
     * @return array Sorted position points
     */
    public function get_sorted_position_points($last_drawn, $balls_drawn, $duplicate_extra_ball = false)
    {
        $position_points = [];
        // Loop through each regular position
        for ($i = 1; $i <= $balls_drawn; $i++) {
            $position_total_key = 'position' . $i . '_total';
            if (isset($last_drawn[$position_total_key]) && $last_drawn[$position_total_key] > 0) {
                $position_points[$i] = $last_drawn[$position_total_key];
            }
        }
        
        // For independent extra ball lotteries, add position 6 (extra ball position)
        if ($duplicate_extra_ball && isset($last_drawn['position_extra_total']) && $last_drawn['position_extra_total'] > 0) {
            $position_points[6] = $last_drawn['position_extra_total']; // Always use position 6 for extra ball
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
     * Get lottery highlights data
     * @param int $lottery_id The lottery ID
     * @return array Lottery highlights
     */
    public function get_lottery_highlights($lottery_id)
    {
        $this->db->select('*');
        $this->db->from('lottery_highlights');
        $this->db->where('lottery_id', $lottery_id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        
        return [];
    }

    /**
     * Parse trends string and return array
     * @param string $trend_string Comma-separated trend values
     * @return array Parsed trends
     */
    public function get_trends($trend_string)
    {
        if (empty($trend_string)) {
            return [];
        }
        
        $trends = explode(',', $trend_string);
        $result = [];
        
        foreach ($trends as $trend) {
            $trend = trim($trend);
            if (!empty($trend)) {
                $result[] = $trend;
            }
        }
        
        return $result;
    }

    /**
     * Parse digit sums and return array
     * @param string $digits Comma-separated digit values
     * @return array Parsed digit sums
     */
    public function get_digit_sums($digits)
    {
        if (empty($digits)) {
            return [];
        }
        
        $digit_array = explode(',', $digits);
        $result = [];
        
        foreach ($digit_array as $digit) {
            $digit = trim($digit);
            if (!empty($digit) && is_numeric($digit)) {
                $result[] = intval($digit);
            }
        }
        
        return $result;
    }

    /**
     * Parse winning sums and return array
     * @param string $winning_sums Comma-separated sum values
     * @return array Parsed sums
     */
    public function get_sums($winning_sums)
    {
        if (empty($winning_sums)) {
            return [];
        }
        
        $sums_array = explode(',', $winning_sums);
        $result = [];
        
        foreach ($sums_array as $sum) {
            $sum = trim($sum);
            if (!empty($sum) && is_numeric($sum)) {
                $result[] = intval($sum);
            }
        }
        
        return $result;
    }

    /**
     * Parse repeaters and return array
     * @param string $repeaters Comma-separated repeater values
     * @return array Parsed repeaters
     */
    public function get_repeaters($repeaters)
    {
        if (empty($repeaters)) {
            return [];
        }
        
        $repeaters_array = explode(',', $repeaters);
        $result = [];
        
        foreach ($repeaters_array as $repeater) {
            $repeater = trim($repeater);
            if (!empty($repeater) && is_numeric($repeater)) {
                $result[] = intval($repeater);
            }
        }
        
        return $result;
    }

    /**
     * Parse consecutives and return array
     * @param string $c Comma-separated consecutive values
     * @return array Parsed consecutives
     */
    public function get_consecutives($c)
    {
        if (empty($c)) {
            return [];
        }
        
        $consecutives_array = explode(',', $c);
        $result = [];
        
        foreach ($consecutives_array as $consecutive) {
            $consecutive = trim($consecutive);
            if (!empty($consecutive) && is_numeric($consecutive)) {
                $result[] = intval($consecutive);
            }
        }
        
        return $result;
    }

    /**
     * Parse parity and return array
     * @param string $p Comma-separated parity values
     * @return array Parsed parity
     */
    public function get_parity($p)
    {
        if (empty($p)) {
            return [];
        }
        
        $parity_array = explode(',', $p);
        $result = [];
        
        foreach ($parity_array as $parity) {
            $parity = trim($parity);
            if (!empty($parity)) {
                $result[] = $parity;
            }
        }
        
        return $result;
    }

    /**
     * Get decade statistics from lottery table
     * @param string $tbl_name Table name for the lottery
     * @param string $range Date range for analysis
     * @return array Decade statistics
     */
    public function get_decade($tbl_name, $range)
    {
        // This would contain complex SQL queries to analyze decades
        // Placeholder implementation
        $decades = [];
        
        // Query the lottery table for decade analysis
        $sql = "SELECT * FROM `{$tbl_name}` WHERE draw_date >= DATE_SUB(NOW(), INTERVAL {$range} DAY) ORDER BY draw_date DESC";
        $query = $this->db->query($sql);
        
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            // Process decade analysis here
            // This is a simplified placeholder
            $decades = ['0-9' => 0, '10-19' => 0, '20-29' => 0, '30-39' => 0, '40-49' => 0];
        }
        
        return $decades;
    }

    /**
     * Get last digit statistics
     * @param string $tbl_name Table name for the lottery
     * @param string $range Date range for analysis
     * @return array Last digit statistics
     */
    public function get_last($tbl_name, $range)
    {
        // This would contain complex SQL queries to analyze last digits
        // Placeholder implementation
        $last_digits = [];
        
        // Query the lottery table for last digit analysis
        $sql = "SELECT * FROM `{$tbl_name}` WHERE draw_date >= DATE_SUB(NOW(), INTERVAL {$range} DAY) ORDER BY draw_date DESC";
        $query = $this->db->query($sql);
        
        if ($query->num_rows() > 0) {
            $results = $query->result_array();
            // Process last digit analysis here
            // This is a simplified placeholder
            $last_digits = ['0' => 0, '1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0];
        }
        
        return $last_digits;
    }

    /**
     * Get number range statistics
     * @param string $number_range Range specification
     * @return array Number range data
     */
    public function get_range($number_range)
    {
        if (empty($number_range)) {
            return [];
        }
        
        $range_parts = explode('-', $number_range);
        if (count($range_parts) == 2) {
            return [
                'min' => intval($range_parts[0]),
                'max' => intval($range_parts[1])
            ];
        }
        
        return [];
    }

    /**
     * Get adjacents statistics
     * @param string $adjacents Adjacent values
     * @return array Adjacents data
     */
    public function get_adjacents($adjacents)
    {
        if (empty($adjacents)) {
            return [];
        }
        
        $adjacents_array = explode(',', $adjacents);
        $result = [];
        
        foreach ($adjacents_array as $adjacent) {
            $adjacent = trim($adjacent);
            if (!empty($adjacent) && is_numeric($adjacent)) {
                $result[] = intval($adjacent);
            }
        }
        
        return $result;
    }

    /**
     * Get combination statistics
     * @param array $combo Combination to analyze
     * @param int $max Maximum value
     * @param array $last_draw Last draw data
     * @return array Combination statistics
     */
    public function get_combo_stats($combo, $max, $last_draw)
    {
        $stats = [
            'sum' => 0,
            'even_count' => 0,
            'odd_count' => 0,
            'repeaters' => 0,
            'consecutives' => 0,
            'decades' => [],
            'last_digits' => []
        ];
        
        // Calculate sum
        $stats['sum'] = array_sum($combo);
        
        // Count even/odd
        foreach ($combo as $number) {
            if ($number % 2 == 0) {
                $stats['even_count']++;
            } else {
                $stats['odd_count']++;
            }
        }
        
        // Analyze decades
        foreach ($combo as $number) {
            $decade = floor($number / 10) * 10;
            $decade_range = $decade . '-' . ($decade + 9);
            if (!isset($stats['decades'][$decade_range])) {
                $stats['decades'][$decade_range] = 0;
            }
            $stats['decades'][$decade_range]++;
        }
        
        // Analyze last digits
        foreach ($combo as $number) {
            $last_digit = $number % 10;
            if (!isset($stats['last_digits'][$last_digit])) {
                $stats['last_digits'][$last_digit] = 0;
            }
            $stats['last_digits'][$last_digit]++;
        }
        
        return $stats;
    }
}
