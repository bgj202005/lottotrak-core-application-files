<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Prize_m Model - Prize History Management
 * 
 * Handles lottery combination filters and calculates win records by comparing
 * combination tickets against drawn numbers from lottery tables.
 * 
 * File Structure for Combination Files:
 * combinations/
 * ├── pick3/{filename}    (for 3-pick lotteries)
 * ├── pick4/{filename}    (for 4-pick lotteries)
 * ├── pick5/{filename}    (for 5-pick lotteries)
 * ├── pick6/{filename}    (for 6-pick lotteries)
 * ├── pick7/{filename}    (for 7-pick lotteries)
 * ├── pick8/{filename}    (for 8-pick lotteries)
 * └── pick9/{filename}    (for 9-pick lotteries)
 */
class Prize_m extends MY_Model
{
    protected $_table_name = 'lottery_combination_files'; // Assuming this table stores combination files
    protected $_order_by = 'created_date DESC';
    
    /**
     * Get prize history for a specific administrator and lottery
     * @param int $admin_id Administrator user ID
     * @param int $limit Number of records per page
     * @param int $offset Starting offset for pagination
     * @param int $lottery_id Lottery ID to filter by
     * @return array Prize history records
     */
    public function get_admin_prize_history($admin_id, $limit = 10, $offset = 0, $lottery_id = null)
    {
        // Load lotteries model for table name conversion
        $this->load->model('lotteries_m');
        
        // Query lottery_combination_filters for this administrator and specific lottery
        $this->db->select('
            lcf.*,
            lp.lottery_name as lotto_name,
            lp.duplicate_extra_ball,
            lp.extra_ball,
            lcfiles.file_name as original_filename,
            lcfiles.N,
            lcfiles.R,
            lcfiles.CCCC as original_cccc,
            lcf.CCCC as actual_cccc
        ');
        $this->db->from('lottery_combination_filters lcf');
        $this->db->join('lottery_profiles lp', 'lp.id = lcf.lottery_id', 'left');
        $this->db->join('lottery_combination_files lcfiles', 'lcfiles.id = lcf.combo_id', 'left');
        
        // Filter by administrator (user = 1 and user_id = admin_id)
        $this->db->where('lcf.user', 1);
        $this->db->where('lcf.user_id', $admin_id);
        
        // Filter by specific lottery if provided
        if ($lottery_id) {
            $this->db->where('lcf.lottery_id', $lottery_id);
        }
        
        $this->db->limit($limit, $offset);
        $this->db->order_by('lcf.id', 'DESC');
        
        $query = $this->db->get();
        $results = $query->result();
        
        // Process results to add calculated fields and win records
        foreach ($results as $key => $record) {
            // Check if record should be expired and update if necessary
            $this->check_and_update_active_status($record);
            
            // Determine if record is active or expired
            $record->is_active = ($record->active == 1) ? 'YES' : 'EXPIRED';
            
            // Use stored win records from database instead of recalculating
            $record->win_records = $this->get_stored_win_records($record);
            
            // Calculate the actual filtered count from the combination file
            $record->actual_filtered_count = $this->calculate_actual_filtered_count($record);
            
            // Add row number
            $record->row_number = $offset + $key + 1;
            
            // Format saved filename using original filename to avoid duplication
            $record->saved_filename = $record->original_filename . 'ADMIN' . sprintf('%02d', $admin_id);
        }
        
        return $results;
    }
    
    /**
     * Get all available prize columns for reset functionality
     * @return array Array of prize column field names
     */
    public function get_all_prize_columns()
    {
        return array(
            '2_win', '2_win_extra', '3_win', '3_win_extra', '4_win', '4_win_extra',
            '5_win', '5_win_extra', '6_win', '6_win_extra', '7_win', '7_win_extra',
            '8_win', '8_win_extra', '9_win', '9_win_extra', 'extra'
        );
    }

    /**
     * Get stored win records from database for a filter record
     * @param object $record Filter record from lottery_combination_filters
     * @return object Win records with actual database values
     */
    public function get_stored_win_records($record)
    {
        $win_records = (object) array();
        
        // Define all possible win categories (1 through 9)
        $categories = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
        
        foreach ($categories as $category) {
            // Regular win field: e.g., '3_win' -> 'win_3'
            $regular_field = $category . '_win';
            if (property_exists($record, $regular_field)) {
                $win_records->{'win_' . $category} = (int) $record->$regular_field;
            } else {
                $win_records->{'win_' . $category} = 0;
            }
            
            // Extra win field: e.g., '3_win_extra' -> 'win_3_extra'  
            $extra_field = $category . '_win_extra';
            if (property_exists($record, $extra_field)) {
                $win_records->{'win_' . $category . '_extra'} = (int) $record->$extra_field;
            } else {
                $win_records->{'win_' . $category . '_extra'} = 0;
            }
        }
        
        // Handle special 'extra' field if it exists
        if (property_exists($record, 'extra')) {
            $win_records->win_extra = (int) $record->extra;
        } else {
            $win_records->win_extra = 0;
        }
        
        return $win_records;
    }

    /**
     * Count total prize records for an administrator and specific lottery
     * @param int $admin_id Administrator user ID
     * @param int $lottery_id Lottery ID to filter by (optional)
     * @return int Total count
     */
    public function count_admin_prize_records($admin_id, $lottery_id = null)
    {
        $this->db->from('lottery_combination_filters');
        $this->db->where('user', 1);
        $this->db->where('user_id', $admin_id);
        
        // Filter by specific lottery if provided
        if ($lottery_id) {
            $this->db->where('lottery_id', $lottery_id);
        }
        
        return $this->db->count_all_results();
    }
    
    /**
     * Calculate win records for a specific combination file
     * @param int $combo_file_id Combination file ID
     * @param int $lottery_id Lottery ID
     * @param int $active Active status (1 = active, 0 = expired)
     * @param string $lastdate Last draw date
     * @return object Win record counts
     */
    public function calculate_win_records($combo_file_id, $lottery_id, $active, $lastdate)
    {
        // Get prize profile for this lottery to determine available categories
        $prize_profile = $this->get_lottery_prize_profile($lottery_id);
        if (!$prize_profile) {
            return (object) array(); // Return empty object if no prize profile
        }
        
        // Initialize dynamic win counters based on prize profile
        $win_records = $this->initialize_win_records($prize_profile);
        
        // If not active, return zeros
        if ($active != 1) {
            return $win_records;
        }
        
        // Get filtered combinations for this file
        $combinations = $this->get_filtered_combinations($combo_file_id);
        if (empty($combinations)) {
            return $win_records;
        }
        
        // Get draw results for lastdate and subsequent dates
        $draw_dates = $this->get_draw_dates_from($lottery_id, $lastdate);
        
        foreach ($draw_dates as $draw_date) {
            $drawn_numbers = $this->get_drawn_numbers($lottery_id, $draw_date);
            if (!$drawn_numbers) continue;
            
            // Process each combination against this draw
            foreach ($combinations as $combination) {
                $matches = $this->count_number_matches($combination, $drawn_numbers);
                $bonus_match = $this->check_bonus_match($combination, $drawn_numbers);
                
                // Check prize categories from highest to lowest
                $this->check_prize_category($matches, $bonus_match, $prize_profile, $win_records);
            }
        }
        
        // Update active status if no more future draws
        if (empty($draw_dates) || $this->no_future_draws($lottery_id, $lastdate)) {
            $this->update_active_status($combo_file_id, 0); // Set to expired
        }
        
        return $win_records;
    }
    
    /**
     * Initialize win records structure based on prize profile
     * @param object $prize_profile Prize profile from database
     * @return object Win records with dynamic structure
     */
    private function initialize_win_records($prize_profile)
    {
        $win_records = (object) array();
        
        // Define all possible prize categories (9 down to 1)
        $prize_categories = array(9, 8, 7, 6, 5, 4, 3, 2, 1);
        
        foreach ($prize_categories as $category) {
            // Check if this category exists in prize profile (not NULL)
            $regular_field = $category . '_win';
            $extra_field = $category . '_win_extra';
            
            // Add regular category if it exists in prize profile
            if (property_exists($prize_profile, $regular_field) && !is_null($prize_profile->$regular_field)) {
                $win_records->{'win_' . $category} = 0;
            }
            
            // Add extra category if it exists in prize profile
            if (property_exists($prize_profile, $extra_field) && !is_null($prize_profile->$extra_field)) {
                $win_records->{'win_' . $category . '_extra'} = 0;
            }
        }
        
        // Check for final 'extra' category
        if (property_exists($prize_profile, 'extra') && !is_null($prize_profile->extra)) {
            $win_records->win_extra = 0;
        }
        
        return $win_records;
    }
    
    /**
     * Get lottery prize profile
     * @param int $lottery_id Lottery ID
     * @return object Prize profile
     */
    private function get_lottery_prize_profile($lottery_id)
    {
        $this->db->select('*');
        $this->db->from('lottery_prize_profiles');
        $this->db->where('lottery_id', $lottery_id);
        return $this->db->get()->row();
    }
    
    /**
     * Get filtered combinations for a combination file
     * @param int $combo_file_id Combination file ID
     * @return array Filtered combinations
     */
    private function get_filtered_combinations($combo_file_id)
    {
        // This would retrieve the actual filtered combinations
        // Implementation depends on how combinations are stored
        $this->db->select('combination_data');
        $this->db->from('lottery_combination_filters');
        $this->db->where('combo_file_id', $combo_file_id);
        return $this->db->get()->result();
    }
    
    /**
     * Get draw dates from a specific date onwards
     * @param int $lottery_id Lottery ID
     * @param string $from_date Starting date
     * @return array Draw dates
     */
    private function get_draw_dates_from($lottery_id, $from_date)
    {
        // This would retrieve draw dates for the lottery
        // Implementation depends on how draw dates are stored
        $this->db->select('draw_date');
        $this->db->from('lottery_draws');
        $this->db->where('lottery_id', $lottery_id);
        $this->db->where('draw_date >=', $from_date);
        $this->db->order_by('draw_date', 'ASC');
        return $this->db->get()->result_array();
    }
    
    /**
     * Get drawn numbers for a specific draw
     * @param int $lottery_id Lottery ID
     * @param string $draw_date Draw date
     * @return array Drawn numbers
     */
    private function get_drawn_numbers($lottery_id, $draw_date)
    {
        // This would retrieve the actual drawn numbers
        // Implementation depends on how drawn numbers are stored
        $this->db->select('drawn_numbers, bonus_number');
        $this->db->from('lottery_draws');
        $this->db->where('lottery_id', $lottery_id);
        $this->db->where('draw_date', $draw_date);
        return $this->db->get()->row();
    }
    
    /**
     * Count number matches between combination and drawn numbers
     * @param object $combination User combination
     * @param object $drawn_numbers Drawn numbers
     * @return int Number of matches
     */
    private function count_number_matches($combination, $drawn_numbers)
    {
        // Implementation depends on how numbers are stored and compared
        // This is a placeholder for the actual matching logic
        return 0; // Return actual match count
    }
    
    /**
     * Check if bonus number matches
     * @param object $combination User combination
     * @param object $drawn_numbers Drawn numbers
     * @return bool True if bonus matches
     */
    private function check_bonus_match($combination, $drawn_numbers)
    {
        // Implementation depends on how bonus numbers are handled
        return false; // Return actual bonus match result
    }
    
    /**
     * Check prize category and increment appropriate counter
     * @param int $matches Number of matches
     * @param bool $bonus_match Bonus match status
     * @param object $prize_profile Prize profile
     * @param object $win_records Win records object to update
     */
    private function check_prize_category($matches, $bonus_match, $prize_profile, &$win_records)
    {
        // Check from highest to lowest prize category (9 down to 1)
        $prize_categories = array(9, 8, 7, 6, 5, 4, 3, 2, 1);
        
        foreach ($prize_categories as $category) {
            // Skip if not enough matches for this category
            if ($matches < $category) {
                continue;
            }
            
            $regular_field = $category . '_win';
            $extra_field = $category . '_win_extra';
            $regular_counter = 'win_' . $category;
            $extra_counter = 'win_' . $category . '_extra';
            
            // Check for extra win first (higher priority)
            if ($bonus_match && 
                property_exists($prize_profile, $extra_field) && 
                !is_null($prize_profile->$extra_field) && 
                $prize_profile->$extra_field == 1 &&
                property_exists($win_records, $extra_counter)) {
                
                $win_records->$extra_counter++;
                return; // Stop after first match (highest priority)
            }
            
            // Check for regular win
            if (property_exists($prize_profile, $regular_field) && 
                !is_null($prize_profile->$regular_field) && 
                $prize_profile->$regular_field == 1 &&
                property_exists($win_records, $regular_counter)) {
                
                $win_records->$regular_counter++;
                return; // Stop after first match (highest priority)
            }
        }
        
        // Check for final 'extra' category (lowest priority)
        if ($bonus_match && 
            property_exists($prize_profile, 'extra') && 
            !is_null($prize_profile->extra) && 
            $prize_profile->extra == 1 &&
            property_exists($win_records, 'win_extra')) {
            
            $win_records->win_extra++;
        }
    }
    
    /**
     * Check if there are no future draws
     * @param int $lottery_id Lottery ID
     * @param string $last_date Last date
     * @return bool True if no future draws
     */
    private function no_future_draws($lottery_id, $last_date)
    {
        $this->db->select('COUNT(*) as count');
        $this->db->from('lottery_draws');
        $this->db->where('lottery_id', $lottery_id);
        $this->db->where('draw_date >', $last_date);
        $result = $this->db->get()->row();
        return ($result->count == 0);
    }
    
    /**
     * Update active status of combination file
     * @param int $combo_file_id Combination file ID
     * @param int $active Active status (1 or 0)
     */
    private function update_active_status($combo_file_id, $active)
    {
        $this->db->where('id', $combo_file_id);
        $this->db->update('lottery_combination_files', array('active' => $active));
    }
    
    /**
     * Get lottery prize profile columns for dynamic display
     * @param int $lottery_id Lottery ID
     * @return array Prize column configuration
     */
    public function get_prize_columns($lottery_id)
    {
        // Get prize profile for this lottery
        $prize_profile = $this->get_lottery_prize_profile($lottery_id);
        if (!$prize_profile) {
            return array();
        }
        
        $columns = array();
        $prize_categories = array(9, 8, 7, 6, 5, 4, 3, 2, 1);
        
        // Generate dynamic prize columns based on what's available in prize profile
        foreach ($prize_categories as $category) {
            $regular_field = $category . '_win';
            $extra_field = $category . '_win_extra';
            
            // Add regular category if it exists and is not NULL
            if (property_exists($prize_profile, $regular_field) && !is_null($prize_profile->$regular_field)) {
                $columns[] = array(
                    'key' => 'win_' . $category,
                    'label' => (string)$category,
                    'tooltip' => $category . ' numbers matched'
                );
            }
            
            // Add extra category if it exists and is not NULL
            if (property_exists($prize_profile, $extra_field) && !is_null($prize_profile->$extra_field)) {
                $columns[] = array(
                    'key' => 'win_' . $category . '_extra',
                    'label' => $category . '+',
                    'tooltip' => $category . ' numbers matched with extra ball'
                );
            }
        }
        
        // Add final 'extra' category if it exists
        if (property_exists($prize_profile, 'extra') && !is_null($prize_profile->extra)) {
            $columns[] = array(
                'key' => 'win_extra',
                'label' => '+',
                'tooltip' => 'Extra ball only'
            );
        }
        
        return $columns;
    }
    
    /**
     * Get all unique lottery types for filter dropdown
     * @return array Lottery options
     */
    public function get_lottery_options()
    {
        $this->db->select('DISTINCT l.id, l.lottery_name');
        $this->db->from('lottery_combination_files lcf');
        $this->db->join('lotteries l', 'l.id = lcf.lottery_id');
        $this->db->order_by('l.lottery_name');
        
        return $this->db->get()->result();
    }
    
    /**
     * Get available prize categories for a lottery based on its prize profile
     * @param int $lottery_id Lottery ID
     * @return array Available prize categories with their keys and labels
     */
    public function get_available_prize_categories($lottery_id)
    {
        $prize_profile = $this->get_lottery_prize_profile($lottery_id);
        if (!$prize_profile) {
            return array();
        }
        
        $categories = array();
        $prize_numbers = array(9, 8, 7, 6, 5, 4, 3, 2, 1);
        
        foreach ($prize_numbers as $number) {
            $regular_field = $number . '_win';
            $extra_field = $number . '_win_extra';
            
            // Check if regular category exists and is not NULL
            if (property_exists($prize_profile, $regular_field) && !is_null($prize_profile->$regular_field)) {
                $categories[] = array(
                    'key' => 'win_' . $number,
                    'label' => (string)$number,
                    'type' => 'regular',
                    'matches' => $number,
                    'tooltip' => $number . ' numbers matched'
                );
            }
            
            // Check if extra category exists and is not NULL
            if (property_exists($prize_profile, $extra_field) && !is_null($prize_profile->$extra_field)) {
                $categories[] = array(
                    'key' => 'win_' . $number . '_extra',
                    'label' => $number . '+',
                    'type' => 'extra',
                    'matches' => $number,
                    'tooltip' => $number . ' numbers matched with extra ball'
                );
            }
        }
        
        // Check for final 'extra' category
        if (property_exists($prize_profile, 'extra') && !is_null($prize_profile->extra)) {
            $categories[] = array(
                'key' => 'win_extra',
                'label' => '+',
                'type' => 'extra_only',
                'matches' => 0,
                'tooltip' => 'Extra ball only'
            );
        }
        
        return $categories;
    }
    
    /**
     * Check if a record should be expired and update its status
     * Note: Automatic expiration is now disabled to prevent premature filter expiration
     * @param object $record Lottery combination filter record
     */
    private function check_and_update_active_status($record)
    {
        // Automatic expiration disabled - filters will remain active until manually expired
        // This prevents filters from being expired when Prize History page loads
        return;
    }

    /**
     * Calculate actual win records by comparing combination tickets to drawn numbers
     * @param object $record Lottery combination filter record
     * @return object Win record counts
     */
    private function calculate_actual_win_records($record)
    {
        // Get prize profile for dynamic win structure
        $prize_profile = $this->get_lottery_prize_profile($record->lottery_id);
        if (!$prize_profile) {
            return (object) array();
        }
        
        // Initialize win records
        $win_records = $this->initialize_win_records($prize_profile);
        
        // For expired filters, return the stored win records from database instead of calculating
        if ($record->active != 1) {
            // Get stored win records from the filter record itself
            $stored_records = $this->get_stored_win_records_with_profile($record, $prize_profile);
            return $stored_records;
        }
        
        // Get lottery name from lottery_profiles table first
        $this->db->select('lottery_name');
        $this->db->from('lottery_profiles');
        $this->db->where('id', $record->lottery_id);
        $lottery_profile = $this->db->get()->row();
        
        if (!$lottery_profile || !$lottery_profile->lottery_name) {
            log_message('error', "Prize History: Lottery profile not found for lottery_id {$record->lottery_id}");
            return $win_records;
        }
        
        // Convert lottery name to table name
        $table_name = $this->lotteries_m->lotto_table_convert($lottery_profile->lottery_name);
        
        // Validate table name - should be a string and not empty
        if (!$table_name || !is_string($table_name) || strlen($table_name) == 0) {
            log_message('error', "Prize History: Invalid table name generated for lottery_id {$record->lottery_id}, lottery_name: {$lottery_profile->lottery_name}, table_name: " . var_export($table_name, true));
            return $win_records;
        }
        
        // Get all draws from last date onwards
        $draws = $this->get_draws_from_date($table_name, $record->lastdate);
        if (empty($draws)) {
            return $win_records;
        }
        
        // Get combination tickets from file
        $combination_tickets = $this->get_combination_tickets($record);
        if (empty($combination_tickets)) {
            return $win_records;
        }
        
        // Process each draw
        foreach ($draws as $draw) {
            // Process each combination ticket
            foreach ($combination_tickets as $ticket) {
                $matches = $this->count_matching_numbers($ticket, $draw);
                $bonus_match = $this->check_extra_number_match($ticket, $draw);
                
                // Check prize categories and increment counters
                $this->check_prize_category($matches, $bonus_match, $prize_profile, $win_records);
            }
        }
        
        return $win_records;
    }
    
    /**
     * Get draws from a specific date onwards
     * @param string $table_name Lottery table name
     * @param string $from_date Starting date
     * @return array Draw results
     */
    private function get_draws_from_date($table_name, $from_date)
    {
        // Validate table name
        if (!$table_name || !is_string($table_name) || $table_name === '1') {
            log_message('error', "Prize History: Invalid table name for get_draws_from_date: " . var_export($table_name, true));
            return array();
        }
        
        $this->db->select('*');
        $this->db->from($table_name);
        $this->db->where('draw_date >=', $from_date); // Use >= instead of >
        $this->db->where('extra > 0'); // Only get draws with valid extra ball for combination comparison
        $this->db->order_by('draw_date', 'ASC');
        return $this->db->get()->result();
    }
    
    /**
     * Get combination tickets from file
     * @param object $record Lottery combination filter record (has N field for picks)
     * @return array Combination tickets
     */
    private function get_combination_tickets($record)
    {
        // Get validated file path
        $file_path = $this->get_combination_file_path($record);
        if (!$file_path) {
            return array();
        }
        
        // Use picks from the record (N field from lottery_combination_files)
        $expected_picks = intval($record->N);
        
        // Read and parse the combination file
        $tickets = array();
        $file_content = file_get_contents($file_path);
        
        if ($file_content) {
            $lines = explode("\n", $file_content);
            foreach ($lines as $line_num => $line) {
                $line = trim($line);
                if (!empty($line)) {
                    // Parse combination numbers (assuming space or comma separated)
                    $numbers = preg_split('/[\s,]+/', $line);
                    $numbers = array_map('intval', array_filter($numbers, 'is_numeric'));
                    
                    // Validate that we have the expected number of picks
                    if (count($numbers) == $expected_picks) {
                        $tickets[] = $numbers;
                    }
                }
            }
        }
        
        return $tickets;
    }
    
    /**
     * Get lottery information
     * @param int $lottery_id Lottery ID
     * @return object Lottery info
     */
    private function get_lottery_info($lottery_id)
    {
        // Get lottery profile for extra_ball information only
        $this->db->select('extra_ball');
        $this->db->from('lottery_profiles');
        $this->db->where('id', $lottery_id);
        $lottery_profile = $this->db->get()->row();
        
        // Return basic lottery info structure
        // Note: picks should be obtained from lottery_combination_files.N field
        return (object) array(
            'extra_ball' => $lottery_profile ? $lottery_profile->extra_ball : 0,
            'picks' => null  // This should be passed from record->N instead
        );
    }
    
    /**
     * Count matching numbers between ticket and draw
     * @param array $ticket Combination ticket numbers
     * @param object $draw Draw result
     * @return int Number of matches
     */
    private function count_matching_numbers($ticket, $draw)
    {
        // Get drawn numbers from draw object
        $drawn_numbers = $this->extract_drawn_numbers($draw);
        
        if (empty($drawn_numbers) || empty($ticket)) {
            return 0;
        }
        
        // Count matches
        $matches = 0;
        foreach ($ticket as $number) {
            if (in_array($number, $drawn_numbers)) {
                $matches++;
            }
        }
        
        return $matches;
    }
    
    /**
     * Check if extra/bonus number matches
     * @param array $ticket Combination ticket numbers
     * @param object $draw Draw result
     * @return bool True if extra number matches
     */
    private function check_extra_number_match($ticket, $draw)
    {
        // Get extra/bonus number from draw
        $extra_number = $this->extract_extra_number($draw);
        
        if (is_null($extra_number)) {
            return false;
        }
        
        // Check if any ticket number matches extra number
        return in_array($extra_number, $ticket);
    }
    
    /**
     * Extract drawn numbers from draw object
     * @param object $draw Draw result
     * @return array Drawn numbers
     */
    private function extract_drawn_numbers($draw)
    {
        // Try common field names for drawn numbers
        $number_fields = array('numbers', 'drawn_numbers', 'winning_numbers', 'balls');
        
        foreach ($number_fields as $field) {
            if (property_exists($draw, $field) && !empty($draw->$field)) {
                // Handle different formats (comma-separated, space-separated, etc.)
                $numbers_str = $draw->$field;
                $numbers = preg_split('/[\s,\-]+/', $numbers_str);
                return array_map('intval', array_filter($numbers, 'is_numeric'));
            }
        }
        
        // Try individual number fields (ball1, ball2, etc.) - support up to 9 balls
        $numbers = array();
        for ($i = 1; $i <= 9; $i++) {
            $ball_field = 'ball' . $i;
            if (property_exists($draw, $ball_field) && !is_null($draw->$ball_field)) {
                $numbers[] = intval($draw->$ball_field);
            }
        }
        
        return $numbers;
    }
    
    /**
     * Extract extra/bonus number from draw object
     * @param object $draw Draw result
     * @return int|null Extra number or null if not found
     */
    private function extract_extra_number($draw)
    {
        // Try common field names for extra/bonus numbers
        $extra_fields = array('extra', 'bonus', 'extra_ball', 'bonus_ball', 'bonus_number');
        
        foreach ($extra_fields as $field) {
            if (property_exists($draw, $field) && !is_null($draw->$field)) {
                return intval($draw->$field);
            }
        }
        
        return null;
    }
    
    /**
     * Validate and get combination file path
     * @param object $record Lottery combination filter record (has N field for picks)
     * @return string|false File path if valid, false if not found
     */
    private function get_combination_file_path($record)
    {
        // Use picks from the record (N field from lottery_combination_files)
        $picks = intval($record->N);
        
        // Validate pick range (3 to 9)
        if ($picks < 3 || $picks > 9) {
            log_message('error', "Prize History: Invalid pick count {$picks} for lottery_id {$record->lottery_id}");
            return false;
        }
        
        // Build file path
        $pick_dir = 'pick' . $picks;
        $file_path = APPPATH . '../combinations/' . $pick_dir . '/' . $record->file_name;
        
        // Check if file exists
        if (!file_exists($file_path)) {
            log_message('error', "Prize History: Combination file not found: {$file_path}");
            return false;
        }
        
        return $file_path;
    }
    
    /**
     * Get supported pick range for validation
     * @return array Supported pick numbers
     */
    public function get_supported_pick_range()
    {
        return array(3, 4, 5, 6, 7, 8, 9);
    }
    
    /**
     * Get stored win records from the filter record itself (for expired filters)
     * @param object $record Lottery combination filter record
     * @param object $prize_profile Prize profile for available categories
     * @return object Win record counts from database
     */
    private function get_stored_win_records_with_profile($record, $prize_profile)
    {
        $win_records = (object) array();
        
        // Define all possible prize categories (9 down to 1)
        $prize_categories = array(9, 8, 7, 6, 5, 4, 3, 2, 1);
        
        foreach ($prize_categories as $category) {
            // Check if this category exists in prize profile (not NULL)
            $regular_field = $category . '_win';
            $extra_field = $category . '_win_extra';
            
            // Add regular category if it exists in prize profile and record
            if (property_exists($prize_profile, $regular_field) && !is_null($prize_profile->$regular_field)) {
                $record_field = $regular_field; // Field name in record matches prize profile
                $win_records->{'win_' . $category} = property_exists($record, $record_field) ? (int)$record->$record_field : 0;
            }
            
            // Add extra category if it exists in prize profile and record
            if (property_exists($prize_profile, $extra_field) && !is_null($prize_profile->$extra_field)) {
                $record_field = $extra_field; // Field name in record matches prize profile
                $win_records->{'win_' . $category . '_extra'} = property_exists($record, $record_field) ? (int)$record->$record_field : 0;
            }
        }
        
        // Check for final 'extra' category
        if (property_exists($prize_profile, 'extra') && !is_null($prize_profile->extra)) {
            $win_records->win_extra = property_exists($record, 'extra') ? (int)$record->extra : 0;
        }
        
        return $win_records;
    }
    
    /**
     * Calculate the actual filtered combination count from the saved combination file
     * @param object $record Filter record from lottery_combination_filters
     * @return int Actual count of combinations in the filtered file
     */
    private function calculate_actual_filtered_count($record)
    {
        // Build file path using the saved filename from the filter
        $expected_picks = (int)$record->R; // Picks from combination files table
        $pick_dir = 'pick' . $expected_picks;
        $file_path = FCPATH . 'combinations/' . $pick_dir . '/' . $record->file_name . '.txt';
        
        if (!file_exists($file_path)) {
            log_message('error', "calculate_actual_filtered_count: File not found: {$file_path}");
            return 0; // Return 0 if file doesn't exist
        }
        
        // For independent extra ball lotteries (duplicate_extra_ball = 1),
        // the file contains main numbers + extra ball, so actual count is picks + 1
        $is_independent_extra_ball = (!empty($record->duplicate_extra_ball) && !empty($record->extra_ball));
        $expected_numbers_per_line = $expected_picks;
        if ($is_independent_extra_ball) {
            $expected_numbers_per_line = $expected_picks + 1; // Main numbers + independent extra ball
        }
        
        // Count valid combinations in the file
        $file_content = file_get_contents($file_path);
        $count = 0;
        
        if ($file_content) {
            $lines = explode("\n", $file_content);
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue; // Skip empty lines
                }
                
                $numbers = preg_split('/[\s,]+/', $line);
                $numbers = array_filter($numbers, 'is_numeric');
                
                // Validate the expected number count for this lottery type
                if (count($numbers) == $expected_numbers_per_line) {
                    $count++;
                }
            }
        }
        
        return $count;
    }
}
