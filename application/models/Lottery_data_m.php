<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lottery Data Model
 * Handles lottery profile data and related information
 */
class Lottery_data_m extends MY_Model
{
    protected $_table_name = 'lottery_profiles';
    protected $_order_by = 'lottery_name';

    /**
     * Get the country code for a lottery
     * @param int $lottery_id Lottery ID
     * @return string|null Country code or NULL if not found
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
     * Get countries for lottery selection
     * @param int $lottery_id The lottery ID
     * @return array List of countries
     */
    public function get_countries($lottery_id)
    {
        $this->db->select('*');
        $this->db->from('countries');
        $this->db->order_by('country_name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get provinces/states for a given country
     * @param int $country_id The country ID
     * @return array List of provinces/states
     */
    public function get_prov_states($country_id)
    {
        $this->db->select('*');
        $this->db->from('provinces_states');
        $this->db->where('country_id', $country_id);
        $this->db->order_by('prov_state_name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get lottery games for a given country and province
     * @param int $country_id The country ID
     * @param int $province_id The province/state ID
     * @return array List of lottery games
     */
    public function get_lottery_games($country_id, $province_id)
    {
        $this->db->select('*');
        $this->db->from('lottery_profiles');
        $this->db->where('lottery_country', $country_id);
        if ($province_id) {
            $this->db->where('lottery_state_prov', $province_id);
        }
        $this->db->order_by('lottery_name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get wheeling tables for a lottery
     * @param int $lottery_id The lottery ID
     * @return array List of wheeling tables
     */
    public function get_wheeling_tables($lottery_id)
    {
        // Get the balls_drawn value for the lottery
        $this->db->select('balls_drawn');
        $this->db->from('lottery_profiles');
        $this->db->where('id', $lottery_id);
        $lottery = $this->db->get()->row();
        
        if (!$lottery) {
            return [];
        }
        
        // Get wheeling tables that match
        $this->db->select('*');
        $this->db->from('wheeling_tables');
        $this->db->where('pick_game', $lottery->balls_drawn);
        $this->db->order_by('numbers_predicted', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Calculate matching tickets for prizes
     * @param array $combinations Array of combinations
     * @param int $required_matches Required number of matches
     * @return int Number of matching tickets
     */
    public function calculate_matching_tickets($combinations, $required_matches) 
    {
        $matching_count = 0;
        
        foreach ($combinations as $combination) {
            // Logic to check matches would go here
            // This is a placeholder implementation
            $matches = 0;
            
            // Compare combination with winning numbers
            // $matches = count(array_intersect($combination, $winning_numbers));
            
            if ($matches >= $required_matches) {
                $matching_count++;
            }
        }
        
        return $matching_count;
    }

    /**
     * Get prize data array for a lottery
     * @param int $lotto_id The lottery ID
     * @return array Prize data
     */
    public function prizes_data_array($lotto_id)
    {
        $this->db->select('*');
        $this->db->from('lottery_prizes');
        $this->db->where('lottery_id', $lotto_id);
        $this->db->order_by('matches_required', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * Validate the combo_id and the administrator is the owner of the combination table file
     * @param int $combo_id   Combination ID
     * @return TRUE|NULL      Returns Exists or NULL if does not exist
     */
    public function validate_combo_id($combo_id)
    {
        $this->db->select('combo_id, file_name');
        $this->db->from('lottery_combination_filters');
        $this->db->where('combo_id', $combo_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            $file_name = $result['file_name'];
            // If user_id is provided, validate ownership
            $user_id = (int) $this->session->userdata('id'); // check with the current administrator logged in
            if ($user_id !== null) {
                // Check if filename contains 'ADMIN' keyword
                if (strpos($file_name, 'ADMIN') === false) {
                    return null; // File doesn't contain ADMIN keyword
                }
                // Extract user ID from end of filename (e.g., 0612924ADMIN01 -> 01)
                $admin_id = (int)substr($file_name, -2, 2);
                // Check if the user IDs match
                if ($user_id !== $admin_id) {
                    return null; // User doesn't own this filter
                }
            }
            return TRUE; // TRUE that the combo_id exists and is held by the current admin
        }
        return null; // Return null if no record is found
    }

    /**
     * Get combination filename and CCCC by combo_id
     * @param int $combo_id The combination ID
     * @return array|null Array containing file_name and CCCC, or null if not found
     */
    public function get_combination_filename_cccc($combo_id)
    {
        $this->db->select('file_name, CCCC');
        $this->db->from('lottery_combination_filters');
        $this->db->where('combo_id', $combo_id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        
        return null; // Return null if no record is found
    }
    /**
     * Format date string to MySQL date format
     * 
     * Converts various date formats to MySQL-compatible YYYY-MM-DD format.
     * Handles formats like "Wed Jul 9, 2025", "July 9, 2025", "07/09/2025", etc.
     * 
     * @param string $date_string The date string to format (e.g., "Wed Jul 9, 2025")
     * @return string|false MySQL-formatted date string (YYYY-MM-DD) or FALSE on failure
     * 
     * @example
     * $mysql_date = $this->format_date_to_mysql("Wed Jul 9, 2025");
     * // Returns: "2025-07-09"
     */
    public function format_date_to_mysql($date_string)
    {
        // Function implementation would go here
        // Convert the date string to MySQL format YYYY-MM-DD
        $timestamp = strtotime($date_string);
        if ($timestamp === false) {
            return false; // Invalid date string
        }
        return date('Y-m-d', $timestamp);
    }
    /**
     * Verify active date for lottery combination filters
     * 
     * Validates all records in lottery_combination_filters table for a given lottery ID
     * and compares the lottery's last draw date with the lastdate field. Updates expired records
     * to active = 0 when the lottery's last draw date is greater than the lastdate.
     * Records with lastdate equal to lottery's last draw date remain active until the next draw.
     * 
     * @param int       $lottery_id The lottery ID to reference combo_id
     * @param string    $tble       Formatted table name
     * @return bool     Returns TRUE on success, FALSE if there's an error updating the table
     */
    public function verify_active_date($lottery_id, $tbl)
    {
        try {
            // Get the last draw date from the lottery's table
            $last_draw = $this->lotteries_m->last_draw_db($tbl);
            
            if (!$last_draw || empty($last_draw->draw_date)) {
                return false; // No last draw date found
            }
            // Convert the lottery's last draw date to MySQL format
            $lottery_last_date = $this->format_date_to_mysql($last_draw->draw_date);
            
            if ($lottery_last_date === false) {
                return false; // Invalid date format
            }
            
            // **EXCLUDE PREDICTION FUTURES FROM AUTO-EXPIRATION**
            // Only process combinations that are for Prize History (older combinations)
            // Prediction Futures have lastdate >= recent draws and should remain active
            $recent_cutoff = date('Y-m-d H:i:s', strtotime('-7 days'));
            
            log_message('debug', "verify_active_date: lottery_last_date={$lottery_last_date}, recent_cutoff={$recent_cutoff}");
            
            // Get all active records for the given lottery that are expired
            // Only expire records where:
            // 1. lastdate <= lottery's last draw date (the prediction has been completed)
            // 2. lastdate < recent cutoff (exclude Prediction Futures which have recent/future dates)
            $this->db->select('combo_id, lastdate');
            $this->db->from('lottery_combination_filters');
            $this->db->where('lottery_id', $lottery_id);
            $this->db->where('active', 1);
            $this->db->where('lastdate <=', $lottery_last_date);
            $this->db->where('lastdate <', $recent_cutoff); // Exclude Prediction Futures
            $query = $this->db->get();
            
            log_message('debug', "verify_active_date: Found " . $query->num_rows() . " Prize History combinations to expire");
            
            // If there are expired records, update them to inactive
            if ($query->num_rows() > 0) {
                $expired_ids = [];
                foreach ($query->result() as $row) {
                    $expired_ids[] = $row->combo_id;
                }
                
                $this->db->where('lottery_id', $lottery_id);
                $this->db->where('active', 1);
                $this->db->where('lastdate <=', $lottery_last_date);
                $this->db->where('lastdate <', $recent_cutoff); // Exclude Prediction Futures
                $update_result = $this->db->update('lottery_combination_filters', ['active' => 0]);
                
                if (!$update_result) {
                    log_message('error', "verify_active_date: Failed to update expired combinations");
                    return false; // Error updating the table
                }
                
                log_message('info', "verify_active_date: Successfully expired " . count($expired_ids) . " Prize History combinations: " . implode(',', $expired_ids));
            }
            
            return true; // Success - either no expired records or successfully updated
        } catch (Exception $e) {
            // Log the error if needed
            log_message('error', 'Error in verify_active_date: ' . $e->getMessage());
            return false;
        }
    }
}
