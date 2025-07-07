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
}
