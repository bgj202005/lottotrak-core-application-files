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
        
        // Add meta title for page head
        $this->data['meta_title'] = 'lottotrak';
        
        $this->data['current'] = $this->uri->segment(2);
        $this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/index/'.$lottery_id);
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
        // Get all combination filters for this admin and lottery (active only)
        // IMPORTANT: Only process filters that are for Prize History, not Prediction Futures
        // Prize History filters typically have older lastdate values and are meant to be updated
        // Prediction Futures filters have lastdate = next draw date and should remain active
        $this->db->select('*');
        $this->db->from('lottery_combination_filters');
        $this->db->where('user', 1);
        $this->db->where('user_id', $admin_id);
        $this->db->where('lottery_id', $lottery_id);
        $this->db->where('active', 1); // Only process active filters
        
        // Add condition to exclude Prediction Futures filters
        // Prediction Futures typically have lastdate >= recent draws (future/current draw dates)
        // Prize History typically has lastdate < recent draws (past draw dates)
        // We'll only process filters where lastdate is clearly in the past (more than 7 days ago)
        $this->db->where('lastdate <', date('Y-m-d', strtotime('-7 days')));
        
        $query = $this->db->get();
        $filters = $query->result();
        
        if (empty($filters)) {
            return; // No active Prize History filters to update
        }
        
        log_message('info', "Auto-update: Found " . count($filters) . " Prize History filters to process for admin {$admin_id}, lottery {$lottery_id}");
        
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
        
        // Get lottery profile for extra ball information
        $this->db->select('extra_ball');
        $this->db->from('lottery_profiles');
        $this->db->where('id', $lottery_id);
        $lottery_profile = $this->db->get()->row();
        $extra_ball_included = ($lottery_profile && $lottery_profile->extra_ball == 1);
        
        foreach ($filters as $filter) {
            $filter_last_date = $filter->lastdate;
            
            // If filter's lastdate equals lottery's most recent draw date, skip (already up to date)
            if ($filter_last_date == $lottery_last_draw_date) {
                log_message('info', "Auto-update: Filter {$filter->id} is already up to date, skipping");
                continue;
            }
            
            // Get all draws from filter's lastdate (exclusive) to lottery's most recent draw date (inclusive)
            $this->db->select('*');
            $this->db->from($lottery_table);
            $this->db->where('draw_date >', $filter_last_date);
            $this->db->where('draw_date <=', $lottery_last_draw_date);
            $this->db->order_by('draw_date', 'ASC');
            $draws_query = $this->db->get();
            $draws = $draws_query->result();
            
            if (empty($draws)) {
                log_message('info', "Auto-update: No new draws found for filter {$filter->id}");
                continue;
            }
            
            // Process each draw in chronological order
            $processed_any_draw = false;
            foreach ($draws as $draw) {
                // Check if we should skip this draw - if extra is included and extra ball is 0, skip
                $should_skip = $this->should_skip_draw($draw, $extra_ball_included);
                if ($should_skip) {
                    log_message('info', "Auto-update: Skipping draw {$draw->draw_date} for filter {$filter->id} - extra ball is 0 and extra is included");
                    continue;
                }
                
                // Process this draw against combination tickets
                $this->process_single_draw_for_filter($filter, $draw, $extra_ball_included);
                $processed_any_draw = true;
                
                log_message('info', "Auto-update: Processed draw {$draw->draw_date} for filter {$filter->id}");
            }
            
            // After processing all draws, mark filter as expired (active = 0) and update lastdate
            if ($processed_any_draw) {
                $this->db->where('id', $filter->id);
                $this->db->update('lottery_combination_filters', array(
                    'active' => 0,
                    'lastdate' => $lottery_last_draw_date
                ));
                
                log_message('info', "Auto-update: Filter {$filter->id} marked as expired, lastdate updated to {$lottery_last_draw_date}");
            }
        }
    }
    
    /**
     * Check if a draw should be skipped based on extra number rules
     * @param object $draw Draw result
     * @param bool $extra_ball_included Whether lottery has extra ball included
     * @return bool True if draw should be skipped
     */
    private function should_skip_draw($draw, $extra_ball_included)
    {
        // If extra is included and extra number in draw is 0, skip draw
        if ($extra_ball_included) {
            $extra_number = $this->extract_bonus_number_from_draw($draw);
            if ($extra_number === 0 || $extra_number === null) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Process a single draw for a specific filter
     * @param object $filter Combination filter record
     * @param object $draw Draw result for the specific draw date
     * @param bool $extra_ball_included Whether lottery has extra ball included
     */
    private function process_single_draw_for_filter($filter, $draw, $extra_ball_included)
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
        
        // Get the required number of matches for top prize (from combination file R value)
        $this->db->select('R');
        $this->db->from('lottery_combination_files');
        $this->db->where('id', $filter->combo_id);
        $file_query = $this->db->get();
        $file_record = $file_query->row();
        $required_matches_for_top_prize = $file_record ? (int)$file_record->R : 0;
        
        // Process each combination ticket against this single draw
        $win_updates = array();
        foreach ($combination_tickets as $ticket) {
            $matches = $this->count_ticket_matches($ticket, $draw);
            $bonus_match = $this->check_bonus_match_for_ticket($ticket, $draw);
            
            // Special rule for extra number validation when extra is included
            if ($extra_ball_included) {
                // For top prize, must have exact matches (e.g., 6 out of 6 for pick 6, 7 out of 7 for pick 7)
                // AND must match the exact bonus number
                if ($matches == $required_matches_for_top_prize && $bonus_match) {
                    // This is a top prize win with extra number
                    $win_category = $required_matches_for_top_prize . '_win_extra';
                    if (property_exists($prize_profile, $win_category) && $prize_profile->$win_category == 1) {
                        if (!isset($win_updates[$win_category])) {
                            $win_updates[$win_category] = 0;
                        }
                        $win_updates[$win_category]++;
                        continue; // Skip regular win category determination for this ticket
                    }
                }
            }
            
            // Determine win category using standard logic for all other cases
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
        
        // Build file path - the filtered combination file is saved in pick{R} directory
        $pick_dir = 'pick' . $expected_picks;
        $file_path = FCPATH . 'combinations/' . $pick_dir . '/' . $filter->file_name . '.txt';
        
        if (!file_exists($file_path)) {
            log_message('error', "Combination file not found: {$file_path}");
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
                
                return $category . '_win_extra';
            }
            
            // Check for regular win
            if (property_exists($prize_profile, $regular_field) && 
                !is_null($prize_profile->$regular_field) && 
                $prize_profile->$regular_field == 1) {
                
                return $category . '_win';
            }
        }
        
        // Check for extra-only category
        if ($bonus_match && 
            property_exists($prize_profile, 'extra') && 
            !is_null($prize_profile->extra) && 
            $prize_profile->extra == 1) {
            
            return 'extra';
        }
        
        return null; // No win category matched
    }
    
    /**
     * Reset win records for a specific filter
     */
    public function reset_win_record()
    {
        // Set JSON content type
        header('Content-Type: application/json');
        
        try {
            $filter_id = $this->input->post('filter_id');
            $admin_id = $this->session->userdata('id');
            
            if (!$admin_id) {
                echo json_encode(['success' => false, 'message' => 'Not authorized']);
                return;
            }
            
            if (!$filter_id || !is_numeric($filter_id)) {
                echo json_encode(['success' => false, 'message' => 'Invalid filter ID']);
                return;
            }
            
            // Verify the filter belongs to this admin
            $this->db->select('file_name');
            $this->db->from('lottery_combination_filters');
            $this->db->where('id', $filter_id);
            $this->db->where('user', 1);
            $this->db->where('user_id', $admin_id);
            $filter = $this->db->get()->row();
            
            if (!$filter) {
                echo json_encode(['success' => false, 'message' => 'Filter not found or access denied']);
                return;
            }
            
            // Reset all prize columns to 0
            $reset_data = array(
                '2_win' => 0, '2_win_extra' => 0, '3_win' => 0, '3_win_extra' => 0,
                '4_win' => 0, '4_win_extra' => 0, '5_win' => 0, '5_win_extra' => 0,
                '6_win' => 0, '6_win_extra' => 0, '7_win' => 0, '7_win_extra' => 0,
                '8_win' => 0, '8_win_extra' => 0, '9_win' => 0, '9_win_extra' => 0,
                'extra' => 0
            );
            
            // Update the filter record
            $this->db->where('id', $filter_id);
            $result = $this->db->update('lottery_combination_filters', $reset_data);
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Win Record for ' . $filter->file_name . ' is now cleared'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to reset win record']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Display combination ticket winner table
     */
    public function view_combination_tickets($filter_id = null)
    {
        try {
            log_message('debug', "view_combination_tickets called with filter_id: $filter_id");
            
            if (!$filter_id || !is_numeric($filter_id)) {
                log_message('error', 'Invalid filter ID provided: ' . $filter_id);
                show_error('Invalid filter ID provided', 400);
            }
            
            $admin_id = $this->session->userdata('id');
            log_message('debug', "Admin ID from session: $admin_id");
            
            if (!$admin_id) {
                log_message('error', 'Administrator not logged in');
                show_error('Administrator must be logged in', 403);
            }
        
        // Get filter details
        log_message('debug', 'Getting filter details from database');
        $this->db->select('lcf.*, lp.lottery_name, lcfiles.file_name as original_filename, lcfiles.N, lcfiles.R');
        $this->db->from('lottery_combination_filters lcf');
        $this->db->join('lottery_profiles lp', 'lp.id = lcf.lottery_id', 'left');
        $this->db->join('lottery_combination_files lcfiles', 'lcfiles.id = lcf.combo_id', 'left');
        $this->db->where('lcf.id', $filter_id);
        $this->db->where('lcf.user', 1);
        $this->db->where('lcf.user_id', $admin_id);
        
        $filter = $this->db->get()->row();
        log_message('debug', 'Filter query result: ' . ($filter ? 'found' : 'not found'));
        
        if (!$filter) {
            log_message('error', 'Filter not found or access denied for filter_id: ' . $filter_id);
            show_error('Filter not found or access denied', 404);
        }
        
        // Get pagination settings
        $per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 10;
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        $offset = ($page - 1) * $per_page;
        log_message('debug', "Pagination settings: per_page=$per_page, page=$page, offset=$offset");
        
        // Get combination tickets
        log_message('debug', 'Getting paginated combination tickets');
        $tickets = $this->get_paginated_combination_tickets($filter, $per_page, $offset);
        log_message('debug', 'Got ' . count($tickets) . ' tickets');
        
        log_message('debug', 'Counting total tickets');
        $total_tickets = $this->count_combination_tickets($filter);
        log_message('debug', "Total tickets: $total_tickets");
        
        // Get latest draw information
        log_message('debug', 'Getting latest draw information');
        $draw_info = $this->get_latest_draw_info($filter->lottery_id);
        log_message('debug', 'Draw info: ' . ($draw_info ? 'found' : 'not found'));
        
        // Check if filter should be expired based on next draw date logic
        if ($draw_info && $filter->active == 1) {
            log_message('debug', 'Checking if filter should be expired based on draw dates');
            
            // Load required models
            $this->load->model('Lotteries_m', 'lotteries_m');
            
            // Get the lottery object for the next_date calculation
            $this->db->select('*');
            $this->db->from('lottery_profiles');
            $this->db->where('id', $filter->lottery_id);
            $lottery = $this->db->get()->row();
            
            if (!$lottery) {
                log_message('error', 'Could not load lottery profile for lottery_id: ' . $filter->lottery_id);
                return;
            }
            
            // Get the lastdate from the filter object (already loaded)
            $ld = $filter->lastdate;
            log_message('debug', "Retrieved lastdate from filter: " . $ld);
            
            if ($ld) {
                // Calculate next draw date based on the lastdate
                $day = $this->lotteries_m->return_day($ld);
                $next_draw_date = $this->lotteries_m->next_date($lottery, $day, $ld);
                log_message('debug', "Calculated next_draw_date: {$next_draw_date}, actual last draw date: {$draw_info->draw_date}");
                
                // If next_draw_date matches the last draw date of the lottery, expire the filter
                if ($next_draw_date == $draw_info->draw_date) {
                    log_message('debug', 'Next draw date matches last draw date - expiring filter');
                    $update_data = array(
                        'active' => 0,
                        'lastdate' => $draw_info->draw_date
                    );
                    $this->db->where('id', $filter_id);
                    $this->db->where('user_id', $admin_id); // Security check
                    $this->db->update('lottery_combination_filters', $update_data);
                    
                    // Update the filter object for current view
                    $filter->active = 0;
                    $filter->lastdate = $draw_info->draw_date;
                    log_message('debug', 'Filter updated to expired, lastdate set to: ' . $draw_info->draw_date);
                } else {
                    log_message('debug', 'Next draw date does not match - filter remains active');
                }
            } else {
                log_message('error', 'Filter lastdate is empty or null');
            }
        }
        
        // Calculate win results for each ticket
        log_message('debug', 'Calculating win results for tickets');
        foreach ($tickets as &$ticket) {
            $ticket['win_result'] = $this->calculate_ticket_win_result($ticket['numbers'], $draw_info, $filter);
        }
        log_message('debug', 'Win results calculated');
        
        $this->data['filter'] = $filter;
        $this->data['tickets'] = $tickets;
        $this->data['draw_info'] = $draw_info;
        $this->data['total_tickets'] = $total_tickets;
        $this->data['per_page'] = $per_page;
        $this->data['current_page'] = $page;
        $this->data['total_pages'] = ceil($total_tickets / $per_page);
        $this->data['offset'] = $offset;
        
        // Pagination options
        $this->data['pagination_options'] = array(10, 20, 50, 100, 200, 300, 500, 1000);
        
        // Add maintenance check for layout
        $this->data['maintenance'] = $this->maintenance_m->maintenance_check();
        $this->data['users'] = $this->maintenance_m->logged_online(0);
        $this->data['admins'] = $this->maintenance_m->logged_online(1);
        $this->data['visitors'] = $this->maintenance_m->active_visitors();
        $this->data['meta_title'] = 'Combination Ticket Winner Table - lottotrak';
        
        $this->data['current'] = $this->uri->segment(2);
        $this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/view_combination_tickets/'.$filter_id);
        $this->data['subview'] = 'admin/prize/combination_tickets';
        
        log_message('debug', 'Loading main layout view');
        $this->load->view('admin/_layout_main', $this->data);
        
        } catch (Exception $e) {
            log_message('error', 'Exception in view_combination_tickets: ' . $e->getMessage());
            show_error('Server error: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * AJAX endpoint for checking results progress and returning combination data
     */
    public function check_results_progress()
    {
        // Set JSON content type
        header('Content-Type: application/json');
        
        try {
            $filter_id = $this->input->post('filter_id');
            $admin_id = $this->session->userdata('id');
            
            if (!$admin_id || !$filter_id) {
                echo json_encode(['success' => false, 'message' => 'Invalid request - missing parameters']);
                return;
            }
            
            // Validate filter exists and belongs to user
            $this->db->select('id');
            $this->db->from('lottery_combination_filters');
            $this->db->where('id', $filter_id);
            $this->db->where('user', 1);
            $this->db->where('user_id', $admin_id);
            $filter = $this->db->get()->row();
            
            if (!$filter) {
                echo json_encode(['success' => false, 'message' => 'Filter not found or access denied']);
                return;
            }
            
            // Return success with redirect URL
            echo json_encode([
                'success' => true,
                'redirect' => site_url('admin/prize/view_combination_tickets/' . $filter_id),
                'message' => 'Redirecting to combination tickets...'
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
        }
    }
    
    /**
     * AJAX endpoint for loading combination tickets with pagination
     */
    public function load_combination_tickets()
    {
        // Set JSON content type
        header('Content-Type: application/json');
        
        try {
            log_message('debug', 'AJAX load_combination_tickets called');
            
            $filter_id = $this->input->post('filter_id');
            $page = $this->input->post('page') ? (int)$this->input->post('page') : 1;
            $per_page = $this->input->post('per_page') ? (int)$this->input->post('per_page') : 10;
            $admin_id = $this->session->userdata('id');
            
            log_message('debug', "AJAX params: filter_id=$filter_id, page=$page, per_page=$per_page, admin_id=$admin_id");
            
            if (!$admin_id || !$filter_id) {
                log_message('error', 'AJAX validation failed: missing admin_id or filter_id');
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
                return;
            }
            
            // Get filter details
            log_message('debug', 'AJAX getting filter details');
            $this->db->select('lcf.*, lp.lottery_name, lcfiles.file_name as original_filename, lcfiles.N, lcfiles.R');
            $this->db->from('lottery_combination_filters lcf');
            $this->db->join('lottery_profiles lp', 'lp.id = lcf.lottery_id', 'left');
            $this->db->join('lottery_combination_files lcfiles', 'lcfiles.id = lcf.combo_id', 'left');
            $this->db->where('lcf.id', $filter_id);
            $this->db->where('lcf.user', 1);
            $this->db->where('lcf.user_id', $admin_id);
            
            $filter = $this->db->get()->row();
            log_message('debug', 'AJAX filter query result: ' . ($filter ? 'found' : 'not found'));
            
            if (!$filter) {
                log_message('error', 'AJAX filter not found or access denied');
                echo json_encode(['success' => false, 'message' => 'Filter not found or access denied']);
                return;
            }
            
            // Calculate offset
            $offset = ($page - 1) * $per_page;
            log_message('debug', "AJAX calculated offset: $offset");
            
            // Get combination tickets
            log_message('debug', 'AJAX getting paginated tickets');
            $tickets = $this->get_paginated_combination_tickets($filter, $per_page, $offset);
            log_message('debug', 'AJAX got ' . count($tickets) . ' tickets');
            
            log_message('debug', 'AJAX counting total tickets');
            $total_tickets = $this->count_combination_tickets($filter);
            log_message('debug', "AJAX total tickets: $total_tickets");
            
            // Get latest draw information
            log_message('debug', 'AJAX getting draw info');
            $draw_info = $this->get_latest_draw_info($filter->lottery_id);
            log_message('debug', 'AJAX draw info: ' . ($draw_info ? 'found' : 'not found'));
            
            // Calculate win results for each ticket
            log_message('debug', 'AJAX calculating win results');
            foreach ($tickets as &$ticket) {
                $ticket['win_result'] = $this->calculate_ticket_win_result($ticket['numbers'], $draw_info, $filter);
            }
            log_message('debug', 'AJAX win results calculated');
            
            // Calculate pagination data
            $total_pages = ceil($total_tickets / $per_page);
            
            // Calculate total winners (only if filter is active)
            $total_winners = 0;
            if ($filter->active == 1) {
                foreach ($tickets as $ticket) {
                    if ($ticket['win_result']['matches'] > 0 || $ticket['win_result']['bonus_match']) {
                        $total_winners++;
                    }
                }
            }
            
            log_message('debug', 'AJAX sending response');
            echo json_encode([
                'success' => true,
                'tickets' => $tickets,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $total_pages,
                    'per_page' => $per_page,
                    'total_tickets' => $total_tickets,
                    'offset' => $offset,
                    'showing_from' => $offset + 1,
                    'showing_to' => min($offset + $per_page, $total_tickets)
                ],
                'total_winners_on_page' => $total_winners,
                'filter' => $filter,
                'draw_info' => $draw_info
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'AJAX exception: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Simple test endpoint to verify AJAX is working
     */
    public function test_ajax()
    {
        header('Content-Type: application/json');
        
        try {
            log_message('debug', 'test_ajax endpoint called');
            echo json_encode([
                'success' => true,
                'message' => 'AJAX endpoint is working',
                'timestamp' => date('Y-m-d H:i:s'),
                'session_id' => $this->session->userdata('id')
            ]);
        } catch (Exception $e) {
            log_message('error', 'test_ajax exception: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Ultra-simple test endpoint
     */
    public function simple_test()
    {
        echo json_encode([
            'success' => true,
            'message' => 'Simple test works',
            'time' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Temporary debug method to check active status logic
     */
    public function debug_active($lottery_id, $lastdate = null)
    {
        // For security, only allow in development
        if (ENVIRONMENT !== 'development') {
            show_404();
            return;
        }
        
        if (!$lastdate) {
            // Use a sample lastdate if not provided
            $lastdate = '2024-01-01'; // Adjust as needed
        }
        
        $debug_info = $this->prize_m->debug_active_status($lottery_id, $lastdate);
        
        echo "<h2>Active Status Debug for Lottery ID: $lottery_id</h2>";
        echo "<pre>";
        print_r($debug_info);
        echo "</pre>";
        
        // Also check actual combination filters
        $this->db->select('*');
        $this->db->from('lottery_combination_filters');
        $this->db->where('lottery_id', $lottery_id);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(5);
        $filters = $this->db->get()->result();
        
        echo "<h3>Recent Combination Filters:</h3>";
        echo "<pre>";
        foreach ($filters as $filter) {
            echo "ID: {$filter->id}, Active: {$filter->active}, Last Date: {$filter->lastdate}, File: {$filter->file_name}\n";
        }
        echo "</pre>";
    }
    
    /**
     * Debug method to test pick directory selection for different lotteries
     */
    public function debug_pick_directories()
    {
        // For security, only allow in development
        if (ENVIRONMENT !== 'development') {
            show_404();
            return;
        }
        
        $admin_id = $this->session->userdata('id');
        if (!$admin_id) {
            echo "<h2>Please log in as admin to test</h2>";
            return;
        }
        
        // Get some sample filters with their lottery and file information
        $this->db->select('lcf.*, lp.lottery_name, lcfiles.file_name as original_filename, lcfiles.N, lcfiles.R');
        $this->db->from('lottery_combination_filters lcf');
        $this->db->join('lottery_profiles lp', 'lp.id = lcf.lottery_id', 'left');
        $this->db->join('lottery_combination_files lcfiles', 'lcfiles.id = lcf.combo_id', 'left');
        $this->db->where('lcf.user', 1);
        $this->db->where('lcf.user_id', $admin_id);
        $this->db->limit(10);
        $filters = $this->db->get()->result();
        
        echo "<h2>Pick Directory Selection Debug</h2>";
        echo "<p>Testing how the system determines the correct pick directory for combination files:</p>";
        
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Filter ID</th><th>Lottery Name</th><th>File Name</th><th>N (from combo file)</th><th>R (from combo file)</th><th>Selected Pick Directory</th><th>Full File Path</th><th>File Exists?</th></tr>";
        
        foreach ($filters as $filter) {
            $file_path = $this->get_combination_file_path($filter);
            $pick_count = isset($filter->R) ? (int)$filter->R : (isset($filter->N) ? $filter->N : 6);
            $pick_dir = 'pick' . $pick_count;
            $file_exists = file_exists($file_path) ? 'YES' : 'NO';
            
            echo "<tr>";
            echo "<td>{$filter->id}</td>";
            echo "<td>{$filter->lottery_name}</td>";
            echo "<td>{$filter->file_name}</td>";
            echo "<td>{$filter->N}</td>";
            echo "<td>{$filter->R}</td>";
            echo "<td>{$pick_dir}</td>";
            echo "<td style='font-size: 10px;'>{$file_path}</td>";
            echo "<td>{$file_exists}</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Also show available directories
        echo "<h3>Available Pick Directories:</h3>";
        $combinations_dir = FCPATH . 'combinations/';
        if (is_dir($combinations_dir)) {
            $dirs = scandir($combinations_dir);
            $pick_dirs = array_filter($dirs, function($d) { return strpos($d, 'pick') === 0 && is_dir(FCPATH . 'combinations/' . $d); });
            echo "<ul>";
            foreach ($pick_dirs as $dir) {
                echo "<li>{$dir}</li>";
            }
            echo "</ul>";
        }
    }
    
    /**
     * Get paginated combination tickets for a filter
     */
    private function get_paginated_combination_tickets($filter, $per_page, $offset)
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
        
        // Build file path - the filtered combination file is saved in pick{R} directory
        $pick_dir = 'pick' . $expected_picks;
        $file_path = FCPATH . 'combinations/' . $pick_dir . '/' . $filter->file_name . '.txt';
        
        if (!file_exists($file_path)) {
            log_message('error', "Combination file not found: {$file_path}");
            return array();
        }
        
        // Read and parse the file with pagination
        $tickets = array();
        $file_content = file_get_contents($file_path);
        
        if ($file_content) {
            $lines = explode("\n", $file_content);
            $line_count = 0;
            $current_offset = 0;
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    // Skip lines until we reach our offset
                    if ($current_offset < $offset) {
                        $current_offset++;
                        continue;
                    }
                    
                    // Stop if we've collected enough tickets for this page
                    if ($line_count >= $per_page) {
                        break;
                    }
                    
                    $numbers = preg_split('/[\s,]+/', $line);
                    $numbers = array_map('intval', array_filter($numbers, 'is_numeric'));
                    
                    if (count($numbers) == $expected_picks) {
                        $tickets[] = array(
                            'ticket_number' => $current_offset + 1,
                            'numbers' => $numbers
                        );
                        $line_count++;
                    }
                    $current_offset++;
                }
            }
        }
        
        return $tickets;
    }
    
    /**
     * Count total combination tickets for a filter
     */
    private function count_combination_tickets($filter)
    {
        // Get file info including R (picks) from combination files
        $this->db->select('file_name, R');
        $this->db->from('lottery_combination_files');
        $this->db->where('id', $filter->combo_id);
        $file_query = $this->db->get();
        $file_record = $file_query->row();
        
        if (!$file_record) {
            return 0;
        }
        
        $expected_picks = (int)$file_record->R;
        
        // Build file path - the filtered combination file is saved in pick{R} directory
        $pick_dir = 'pick' . $expected_picks;
        $file_path = FCPATH . 'combinations/' . $pick_dir . '/' . $filter->file_name . '.txt';
        
        if (!file_exists($file_path)) {
            return 0;
        }
        
        // Count lines in file
        $file_content = file_get_contents($file_path);
        $count = 0;
        
        if ($file_content) {
            $lines = explode("\n", $file_content);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $numbers = preg_split('/[\s,]+/', $line);
                    $numbers = array_filter($numbers, 'is_numeric');
                    
                    if (count($numbers) == $expected_picks) {
                        $count++;
                    }
                }
            }
        }
        
        return $count;
    }
    
    /**
     * Get latest draw information for a lottery
     */
    private function get_latest_draw_info($lottery_id)
    {
        try {
            log_message('debug', "get_latest_draw_info called for lottery_id: $lottery_id");
            
            $this->db->select('*');
            $this->db->from('lottery_profiles');
            $this->db->where('id', $lottery_id);
            $this->db->limit(1);
            $lottery_profile = $this->db->get()->row();
            
            if (!$lottery_profile) {
                log_message('debug', 'get_latest_draw_info: lottery profile not found');
                return null;
            }
            
            // Load the Lotteries model to convert lottery name to table name
            $this->load->model('Lotteries_m', 'lotteries_m');
            $table_name = $this->lotteries_m->lotto_table_convert($lottery_profile->lottery_name);
            
            log_message('debug', "get_latest_draw_info: found profile, lottery_name: {$lottery_profile->lottery_name}, table_name: {$table_name}");
            
            if (!$table_name || !is_string($table_name) || strlen($table_name) == 0) {
                log_message('error', "get_latest_draw_info: invalid table name generated for lottery_name: {$lottery_profile->lottery_name}");
                return null;
            }
            
            // Check if the table exists before querying
            $table_exists = $this->db->table_exists($table_name);
            if (!$table_exists) {
                log_message('error', "get_latest_draw_info: table {$table_name} does not exist");
                return null;
            }
            
            // Get the latest draw for this lottery (only where extra ball exists)
            $this->db->select('*');
            $this->db->from($table_name);
            // Only check for 'extra' field since 'bonus' doesn't exist
            $this->db->where('extra > 0');
            $this->db->where('extra IS NOT NULL');
            $this->db->order_by('draw_date', 'DESC');
            $this->db->limit(1);
            $latest_draw = $this->db->get()->row();
            
            if ($latest_draw) {
                // Add extra ball information
                $latest_draw->extra_ball_included = ($lottery_profile->extra_ball == 1);
                log_message('debug', 'get_latest_draw_info: found latest draw');
            } else {
                log_message('debug', 'get_latest_draw_info: no draws found');
            }
            
            return $latest_draw;
            
        } catch (Exception $e) {
            log_message('error', 'get_latest_draw_info exception: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Calculate win result for a ticket
     */
    private function calculate_ticket_win_result($ticket_numbers, $draw_info, $filter)
    {
        if (!$draw_info) {
            return array(
                'category' => 'No Draw Data',
                'color_class' => 'no-win',
                'matches' => 0,
                'bonus_match' => false
            );
        }
        
        // Count matches regardless of filter status
        $matches = $this->count_ticket_matches($ticket_numbers, $draw_info);
        $bonus_match = $this->check_bonus_match_for_ticket($ticket_numbers, $draw_info);
        
        // Determine win category based on matches
        $category = 'Not a Winner';
        $color_class = 'not-a-winner';
        
        if ($matches >= 6) {
            $category = 'JACKPOT WIN';
            $color_class = 'jackpot-win';
        } elseif ($matches >= 4) {
            $category = 'MAJOR WIN';
            $color_class = 'major-win';
        } elseif ($matches >= 2) {
            $category = $matches . ' Winning Numbers';
            $color_class = 'minor-win';
        } elseif ($bonus_match) {
            $category = 'BONUS WIN';
            $color_class = 'bonus-win';
        }
        
        return array(
            'category' => $category,
            'color_class' => $color_class,
            'matches' => $matches,
            'bonus_match' => $bonus_match
        );
    }
    
    /**
     * Quick redirect version for troubleshooting
     */
    public function check_results_progress_redirect()
    {
        $filter_id = $this->input->post('filter_id');
        
        if ($filter_id) {
            // Redirect to the working view_combination_tickets page
            redirect('admin/prize/view_combination_tickets/' . $filter_id);
        } else {
            show_error('Invalid filter ID', 400);
        }
    }
}
