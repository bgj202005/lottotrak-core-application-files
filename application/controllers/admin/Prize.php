<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prize extends CI_Controller 
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->model('prize_m');
        $this->load->model('user_m');
        $this->load->model('lotteries_m'); 
        $this->load->model('maintenance_m');
    }
        
    public function index($lottery_id = null) 
    {
        
        // Original code commented out for testing
        
        // Validate lottery_id parameter
        if (!$lottery_id || !is_numeric($lottery_id)) {
            show_error('Invalid lottery ID provided', 400);
        }
        
        // Get lottery information
        $lottery = $this->lotteries_m->get($lottery_id);
        if (!$lottery) {
            show_error('Lottery not found', 404);
        }
        
        // Get lottery profile for extra ball information
        $this->db->select('extra_ball');
        $this->db->from('lottery_profiles');
        $this->db->where('id', $lottery_id);
        $lottery_profile = $this->db->get()->row();
        
        $this->data['extra_ball_included'] = ($lottery_profile && $lottery_profile->extra_ball == 1) ? 'YES' : 'NO';
        
        // Get pagination settings
        $per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 10;
        $offset = $this->input->get('offset') ? (int)$this->input->get('offset') : 0;
        
        // Get logged in admin user ID
        $admin_id = $this->session->userdata('id');
        
        if (!$admin_id) {
            show_error('Administrator must be logged in to view Prize History', 403);
        }
        
        // Auto-update prize records before displaying - check for new draws and update win records
        $this->auto_update_prize_records($admin_id, $lottery_id);
        
        // Get prize history data for the specific lottery and admin
        $this->data['prize_records'] = $this->prize_m->get_admin_prize_history($admin_id, $per_page, $offset, $lottery_id);
        $this->data['total_records'] = $this->prize_m->count_admin_prize_records($admin_id, $lottery_id);
        $this->data['lottery'] = $lottery;
        
        // Get dynamic prize columns for this specific lottery
        $this->data['prize_columns'] = $this->prize_m->get_prize_columns($lottery_id);
        
        // Pagination data
        $this->data['per_page'] = $per_page;
        $this->data['offset'] = $offset;
        $this->data['total_pages'] = ceil($this->data['total_records'] / $per_page);
        $this->data['current_page'] = floor($offset / $per_page) + 1;
        
        // Pagination options
        $this->data['pagination_options'] = array(10, 20, 50);
        
        // Add maintenance check for layout
        $this->data['maintenance'] = $this->maintenance_m->maintenance_check();
        
        // Add online user counts for layout
        $this->data['users'] = $this->maintenance_m->logged_online(0);      // Members
        $this->data['admins'] = $this->maintenance_m->logged_online(1);     // Admins
        $this->data['visitors'] = $this->maintenance_m->active_visitors();  // Active Visitors
        
        $this->data['current'] = $this->uri->segment(2);
        $this->session->set_userdata('uri', 'admin/'.$this->data['current']);
        $this->data['subview'] = 'admin/prize/index';
        $this->load->view('admin/_layout_main', $this->data);
    }
    
    /**
     * AJAX endpoint for getting updated table data
     */
    public function get_table_data()
    {
        $per_page = $this->input->post('per_page') ? (int)$this->input->post('per_page') : 10;
        $offset = $this->input->post('offset') ? (int)$this->input->post('offset') : 0;
        $admin_id = $this->session->userdata('id');
        
        if (!$admin_id) {
            echo json_encode(['error' => 'Not authorized']);
            return;
        }
        
        $prize_records = $this->prize_m->get_admin_prize_history($admin_id, $per_page, $offset);
        $total_records = $this->prize_m->count_admin_prize_records($admin_id);
        $prize_columns = $this->get_admin_prize_columns($admin_id);
        
        echo json_encode([
            'records' => $prize_records,
            'total' => $total_records,
            'current_page' => floor($offset / $per_page) + 1,
            'total_pages' => ceil($total_records / $per_page),
            'prize_columns' => $prize_columns
        ]);
    }
    
    /**
     * Get all unique prize columns for lotteries used by an admin
     * @param int $admin_id Administrator ID
     * @return array Unique prize columns across all admin's lotteries
     */
    private function get_admin_prize_columns($admin_id)
    {
        // Get all unique lottery IDs for this admin's filters
        $this->db->distinct();
        $this->db->select('lottery_id');
        $this->db->from('lottery_combination_filters');
        $this->db->where('user', 1);
        $this->db->where('user_id', $admin_id);
        $query = $this->db->get();
        $lottery_ids = $query->result();
        
        $all_columns = array();
        $column_keys = array();
        
        // Get prize columns for each lottery and merge unique ones
        foreach ($lottery_ids as $lottery) {
            $lottery_columns = $this->prize_m->get_available_prize_categories($lottery->lottery_id);
            
            foreach ($lottery_columns as $column) {
                // Only add if we haven't seen this column key before
                if (!in_array($column['key'], $column_keys)) {
                    $all_columns[] = $column;
                    $column_keys[] = $column['key'];
                }
            }
        }
        
        // Sort columns by matches descending, then by type (regular first, then extra)
        usort($all_columns, function($a, $b) {
            if ($a['matches'] == $b['matches']) {
                // If same number of matches, prioritize regular columns over extra
                if ($a['type'] == 'regular' && $b['type'] == 'extra') return -1;
                if ($a['type'] == 'extra' && $b['type'] == 'regular') return 1;
                if ($a['type'] == 'extra_only') return 1; // Extra-only goes last
                return 0;
            }
            return $b['matches'] - $a['matches']; // Higher matches first
        });
        
        return $all_columns;
    }
    
    /**
     * Debug method to check lottery table conversion
     */
    public function debug_table($lottery_id = 1)
    {
        $debug_info = $this->prize_m->debug_lottery_table($lottery_id);
        
        echo "<h3>Lottery Table Debug Info</h3>";
        echo "<pre>";
        print_r($debug_info);
        echo "</pre>";
        
        // Also test with different lottery IDs
        echo "<h3>Testing Multiple Lottery IDs</h3>";
        for ($i = 1; $i <= 5; $i++) {
            echo "<h4>Lottery ID: $i</h4>";
            echo "<pre>";
            print_r($this->prize_m->debug_lottery_table($i));
            echo "</pre>";
        }
    }
    
    /**
     * Auto-update prize records when there are new draws available
     * @param int $admin_id Administrator ID
     * @param int $lottery_id Lottery ID
     */
    private function auto_update_prize_records($admin_id, $lottery_id)
    {
        // Get all combination filters for this admin and lottery (active and inactive)
        $this->db->select('*');
        $this->db->from('lottery_combination_filters');
        $this->db->where('user', 1);
        $this->db->where('user_id', $admin_id);
        $this->db->where('lottery_id', $lottery_id);
        $query = $this->db->get();
        $filters = $query->result();
        
        if (empty($filters)) {
            return; // No filters to update
        }
        
        // Get lottery name first, then convert to table name
        $lottery = $this->lotteries_m->get($lottery_id);
        if (!$lottery || empty($lottery->lottery_name)) {
            log_message('error', "Auto-update: Invalid lottery or lottery name for lottery_id {$lottery_id}");
            return;
        }
        
        $lottery_table = $this->lotteries_m->lotto_table_convert($lottery->lottery_name);
        
        if (!$lottery_table || !is_string($lottery_table)) {
            log_message('error', "Auto-update: Invalid lottery table for lottery {$lottery->lottery_name}");
            return;
        }
        
        // Get the latest draw date from the lottery table (last available draw)
        $this->db->select_max('draw_date');
        $this->db->from($lottery_table);
        $latest_draw_query = $this->db->get();
        $latest_draw_result = $latest_draw_query->row();
        $lottery_last_draw_date = $latest_draw_result->draw_date ?? null;
        
        if (!$lottery_last_draw_date) {
            log_message('error', "Auto-update: No draws available in lottery table {$lottery_table}");
            return; // No draws available
        }
        
        foreach ($filters as $filter) {
            // Find the last draw date from this filter's lastdate
            $filter_last_date = $filter->lastdate;
            
            // Get the drawn numbers for the filter's last date
            $this->db->select('*');
            $this->db->from($lottery_table);
            $this->db->where('draw_date', $filter_last_date);
            $draw_query = $this->db->get();
            $draw_result = $draw_query->row();
            
            if ($draw_result) {
                // Process this specific draw against combination tickets
                $this->process_single_draw_for_filter($filter, $draw_result);
            } else {
                log_message('error', "Auto-update: No draw found for date {$filter_last_date} in filter {$filter->id}");
            }
            
            // Mark filter as expired and update lastdate to lottery's last draw date
            $this->db->where('id', $filter->id);
            $this->db->update('lottery_combination_filters', array(
                'active' => 0,
                'lastdate' => $lottery_last_draw_date
            ));
            
            log_message('info', "Auto-update: Filter {$filter->id} marked as expired, lastdate updated to {$lottery_last_draw_date}");
        }
    }
    
    /**
     * Process a single draw for a specific filter
     * @param object $filter Combination filter record
     * @param object $draw Draw result for the filter's lastdate
     */
    private function process_single_draw_for_filter($filter, $draw)
    {
        // Get combination tickets from the file
        $combination_tickets = $this->get_combination_tickets_for_filter($filter);
        if (empty($combination_tickets)) {
            log_message('error', "Auto-update: No combination tickets found for filter {$filter->id}");
            return;
        }
        
        // Get prize profile for win category determination
        $prize_profile = $this->get_lottery_prize_profile($filter->lottery_id);
        if (!$prize_profile) {
            log_message('error', "Auto-update: No prize profile found for lottery {$filter->lottery_id}");
            return;
        }
        
        // Process each combination ticket against this single draw
        $win_updates = array();
        foreach ($combination_tickets as $ticket) {
            $matches = $this->count_ticket_matches($ticket, $draw);
            $bonus_match = $this->check_bonus_match_for_ticket($ticket, $draw);
            
            // Determine win category and increment counter
            $win_category = $this->determine_win_category($matches, $bonus_match, $prize_profile);
            if ($win_category) {
                if (!isset($win_updates[$win_category])) {
                    $win_updates[$win_category] = 0;
                }
                $win_updates[$win_category]++;
            }
        }
        
        // Update the filter's win record fields in database
        if (!empty($win_updates)) {
            $update_data = array();
            
            // Add win record updates to existing values
            foreach ($win_updates as $category => $count) {
                // Get current value and add new wins
                $this->db->select($category);
                $this->db->from('lottery_combination_filters');
                $this->db->where('id', $filter->id);
                $current_query = $this->db->get();
                $current_result = $current_query->row();
                
                if ($current_result) {
                    $current_value = isset($current_result->$category) ? (int)$current_result->$category : 0;
                    $update_data[$category] = $current_value + $count;
                }
            }
            
            // Update the filter record with new win counts
            if (!empty($update_data)) {
                $this->db->where('id', $filter->id);
                $this->db->update('lottery_combination_filters', $update_data);
                
                log_message('info', "Auto-update: Filter {$filter->id} updated with draw from {$draw->draw_date}, win records updated");
            }
        }
    }
    
    /**
     * Get combination tickets for a filter
     * @param object $filter Filter record
     * @return array Combination tickets
     */
    private function get_combination_tickets_for_filter($filter)
    {
        // Get file info including R (picks) from combination files
        $this->db->select('file_name, R');
        $this->db->from('lottery_combination_files');
        $this->db->where('id', $filter->combo_id);
        $file_query = $this->db->get();
        $file_record = $file_query->row();
        
        if (!$file_record) {
            return array();
        }
        
        $expected_picks = (int)$file_record->R;
        
        // Build file path
        $pick_dir = 'pick' . $expected_picks;
        $file_path = APPPATH . '../combinations/' . $pick_dir . '/' . $file_record->file_name;
        
        if (!file_exists($file_path)) {
            log_message('error', "Auto-update: Combination file not found: {$file_path}");
            return array();
        }
        
        // Read and parse the file
        $tickets = array();
        $file_content = file_get_contents($file_path);
        
        if ($file_content) {
            $lines = explode("\n", $file_content);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $numbers = preg_split('/[\s,]+/', $line);
                    $numbers = array_map('intval', array_filter($numbers, 'is_numeric'));
                    
                    if (count($numbers) == $expected_picks) {
                        $tickets[] = $numbers;
                    }
                }
            }
        }
        
        return $tickets;
    }
    
    /**
     * Get lottery prize profile
     * @param int $lottery_id Lottery ID
     * @return object|null Prize profile
     */
    private function get_lottery_prize_profile($lottery_id)
    {
        $this->db->select('*');
        $this->db->from('lottery_prize_profiles');
        $this->db->where('lottery_id', $lottery_id);
        return $this->db->get()->row();
    }
    
    /**
     * Count matches between ticket and draw
     * @param array $ticket Ticket numbers
     * @param object $draw Draw result
     * @return int Number of matches
     */
    private function count_ticket_matches($ticket, $draw)
    {
        $drawn_numbers = $this->extract_drawn_numbers_from_draw($draw);
        
        if (empty($drawn_numbers) || empty($ticket)) {
            return 0;
        }
        
        $matches = 0;
        foreach ($ticket as $number) {
            if (in_array($number, $drawn_numbers)) {
                $matches++;
            }
        }
        
        return $matches;
    }
    
    /**
     * Check if bonus number matches any ticket number
     * @param array $ticket Ticket numbers
     * @param object $draw Draw result
     * @return bool True if bonus matches
     */
    private function check_bonus_match_for_ticket($ticket, $draw)
    {
        $bonus_number = $this->extract_bonus_number_from_draw($draw);
        
        if (is_null($bonus_number)) {
            return false;
        }
        
        return in_array($bonus_number, $ticket);
    }
    
    /**
     * Extract drawn numbers from draw object
     * @param object $draw Draw result
     * @return array Drawn numbers
     */
    private function extract_drawn_numbers_from_draw($draw)
    {
        // Try common field names
        $number_fields = array('numbers', 'drawn_numbers', 'winning_numbers', 'balls');
        
        foreach ($number_fields as $field) {
            if (property_exists($draw, $field) && !empty($draw->$field)) {
                $numbers_str = $draw->$field;
                $numbers = preg_split('/[\s,\-]+/', $numbers_str);
                return array_map('intval', array_filter($numbers, 'is_numeric'));
            }
        }
        
        // Try individual ball fields
        $numbers = array();
        for ($i = 1; $i <= 9; $i++) {
            $ball_field = 'ball' . $i;
            if (property_exists($draw, $ball_field) && !is_null($draw->$ball_field)) {
                $numbers[] = (int)$draw->$ball_field;
            }
        }
        
        return $numbers;
    }
    
    /**
     * Extract bonus number from draw object
     * @param object $draw Draw result
     * @return int|null Bonus number or null
     */
    private function extract_bonus_number_from_draw($draw)
    {
        $bonus_fields = array('extra', 'bonus', 'extra_ball', 'bonus_ball', 'bonus_number');
        
        foreach ($bonus_fields as $field) {
            if (property_exists($draw, $field) && !is_null($draw->$field)) {
                return (int)$draw->$field;
            }
        }
        
        return null;
    }
    
    /**
     * Determine win category based on matches and bonus
     * @param int $matches Number of matches
     * @param bool $bonus_match Bonus match status
     * @param object $prize_profile Prize profile
     * @return string|null Win category field name or null
     */
    private function determine_win_category($matches, $bonus_match, $prize_profile)
    {
        // Check from highest to lowest prize category
        $prize_categories = array(9, 8, 7, 6, 5, 4, 3, 2, 1);
        
        foreach ($prize_categories as $category) {
            if ($matches < $category) {
                continue; // Not enough matches for this category
            }
            
            $regular_field = $category . '_win';
            $extra_field = $category . '_win_extra';
            
            // Check for extra win first (higher priority)
            if ($bonus_match && 
                property_exists($prize_profile, $extra_field) && 
                !is_null($prize_profile->$extra_field) && 
                $prize_profile->$extra_field == 1) {
                
                return 'win_' . $category . '_extra';
            }
            
            // Check for regular win
            if (property_exists($prize_profile, $regular_field) && 
                !is_null($prize_profile->$regular_field) && 
                $prize_profile->$regular_field == 1) {
                
                return 'win_' . $category;
            }
        }
        
        // Check for extra-only category
        if ($bonus_match && 
            property_exists($prize_profile, 'extra') && 
            !is_null($prize_profile->extra) && 
            $prize_profile->extra == 1) {
            
            return 'win_extra';
        }
        
        return null; // No win category matched
    }
}
