<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prize_m extends MY_Model
{
    protected $_table_name = 'lottery_combination_files'; // Assuming this table stores combination files
    protected $_order_by = 'created_date DESC';
    
    /**
     * Get prize history for a specific administrator
     * @param int $admin_id Administrator user ID
     * @param int $limit Number of records per page
     * @param int $offset Starting offset for pagination
     * @return array Prize history records
     */
    public function get_admin_prize_history($admin_id, $limit = 10, $offset = 0)
    {
        $this->db->select('
            lcf.*,
            lp.lottery_name as lotto_name
        ');
        $this->db->from('lottery_combination_files lcf');
        $this->db->join('lottery_profiles lp', 'lp.id = lcf.lottery_id', 'left');
        
        // Filter by admin ID in filename (files containing ADMIN + user_id)
        $this->db->like('lcf.file_name', 'ADMIN' . sprintf('%02d', $admin_id));
        
        $this->db->limit($limit, $offset);
        $this->db->order_by('lcf.id', 'DESC');
        
        $query = $this->db->get();
        $results = $query->result();
        
        // Process results to add calculated fields and win records
        foreach ($results as $key => $record) {
            // Determine if record is active or expired based on active field
            $record->is_active = ($record->active == 1) ? 'YES' : 'EXPIRED';
            
            // Get win records for this combination file
            $record->win_records = $this->calculate_win_records($record->id, $record->lottery_id, $record->active, $record->lastdate);
            
            // Add row number
            $record->row_number = $offset + $key + 1;
        }
        
        return $results;
    }
    
    /**
     * Count total prize records for an administrator
     * @param int $admin_id Administrator user ID
     * @return int Total count
     */
    public function count_admin_prize_records($admin_id)
    {
        $this->db->from('lottery_combination_files');
        $this->db->like('file_name', 'ADMIN' . sprintf('%02d', $admin_id));
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
                    'label' => $category . 'E',
                    'tooltip' => $category . ' numbers matched with extra ball'
                );
            }
        }
        
        // Add final 'extra' category if it exists
        if (property_exists($prize_profile, 'extra') && !is_null($prize_profile->extra)) {
            $columns[] = array(
                'key' => 'win_extra',
                'label' => 'E',
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
                    'label' => $number . 'E',
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
                'label' => 'E',
                'type' => 'extra_only',
                'matches' => 0,
                'tooltip' => 'Extra ball only'
            );
        }
        
        return $categories;
    }
}
