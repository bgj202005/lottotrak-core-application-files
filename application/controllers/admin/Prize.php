<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prize extends Admin_Controller 
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
        
        // Auto-update prize records before displaying - check for new draws and update win records
        $this->auto_update_prize_records($admin_id, $lottery_id);
        
        // Note: Filter expiration now only happens when viewing Combination Ticket Winner table
        // This ensures filters remain active until user actually views the results
        
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
     * Auto-update prize records when there are new draws available
     * @param int $admin_id Administrator ID
     * @param int $lottery_id Lottery ID
     * Note: This method is now disabled. Win records are only updated when user views Combination Ticket Winner table.
     */
    private function auto_update_prize_records($admin_id, $lottery_id)
    {
        // This method is intentionally disabled to prevent premature win record updates
        // Win records will only be processed when user explicitly views the Combination Ticket Winner table
        log_message('info', "Auto-update disabled: Win records will only be processed when viewing Combination Ticket Winner table");
        return;
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
            $win_category = $this->determine_win_category($matches, $bonus_match, $prize_profile, $extra_ball_included);
            if ($win_category) {
                if (!isset($win_updates[$win_category])) {
                    $win_updates[$win_category] = 0;
                }
                $win_updates[$win_category]++;
            }
        }
        
        // Update the filter's win record fields in database
        if (!empty($win_updates)) {
            // Get all current win record values for this filter in one query
            $this->db->select('*');
            $this->db->from('lottery_combination_filters');
            $this->db->where('id', $filter->id);
            $current_query = $this->db->get();
            $current_filter = $current_query->row();
            
            if ($current_filter) {
                $update_data = array();
                
                // Add win record updates to existing values
                foreach ($win_updates as $category => $count) {
                    $current_value = isset($current_filter->$category) ? (int)$current_filter->$category : 0;
                    $new_value = $current_value + $count;
                    $update_data[$category] = $new_value;
                }
                
                // Update the filter record with new win counts
                if (!empty($update_data)) {
                    $this->db->where('id', $filter->id);
                    $this->db->update('lottery_combination_filters', $update_data);
                }
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
    private function determine_win_category($matches, $bonus_match, $prize_profile, $extra_ball_included = true)
    {
        // Check from highest to lowest prize category
        $prize_categories = array(9, 8, 7, 6, 5, 4, 3, 2, 1);
        
        foreach ($prize_categories as $category) {
            if ($matches < $category) {
                continue; // Not enough matches for this category
            }
            
            $regular_field = $category . '_win';
            $extra_field = $category . '_win_extra';
            
            // Check for extra win first (higher priority) - only if extra ball is included
            if ($extra_ball_included && $bonus_match && 
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
        
        // Check for extra-only category - only if extra ball is included
        if ($extra_ball_included && $bonus_match && 
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
            if (!$filter_id || !is_numeric($filter_id)) {
                log_message('error', 'Invalid filter ID provided: ' . $filter_id);
                show_error('Invalid filter ID provided', 400);
            }
            
            $admin_id = $this->session->userdata('id');
            
        // Get filter details including lottery configuration for independent extra ball support
        $this->db->select('lcf.*, lp.lottery_name, lp.duplicate_extra_ball, lp.extra_ball, lp.balls_drawn, lcfiles.file_name as original_filename, lcfiles.N, lcfiles.R');
        $this->db->from('lottery_combination_filters lcf');
        $this->db->join('lottery_profiles lp', 'lp.id = lcf.lottery_id', 'left');
        $this->db->join('lottery_combination_files lcfiles', 'lcfiles.id = lcf.combo_id', 'left');
        $this->db->where('lcf.id', $filter_id);
        $this->db->where('lcf.user', 1);
        $this->db->where('lcf.user_id', $admin_id);
        
        $filter = $this->db->get()->row();
        
        if (!$filter) {
            log_message('error', 'Filter not found or access denied for filter_id: ' . $filter_id . ', admin_id: ' . $admin_id);
            show_error('Filter not found or access denied', 404);
        }
        
        // Get pagination settings
        $per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 10;
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
        $offset = ($page - 1) * $per_page;
        
        // Get combination tickets
        $tickets = $this->get_paginated_combination_tickets($filter, $per_page, $offset);
        
        $total_tickets = $this->count_combination_tickets($filter);
        
        // Get latest draw information
        $draw_info = $this->get_latest_draw_info($filter->lottery_id, $filter->lastdate);
        
        // Check if filter should be processed for win records (but don't expire yet)
        // Only expire when returning to Prize History, not when viewing tickets
        $should_process_wins = false;
        $next_draw_date = null;
        $next_draw_date_for_js = null; // Initialize JS-parseable date format
        $display_mode = 'normal'; // 'normal', 'tbd', or 'results'
        
        // Always calculate expected next draw date for active filters, regardless of whether draw_info exists
        if ($filter->active == 1 && $filter->lastdate) {
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
            
            // CORRECTED LOGIC: Always calculate the NEXT draw date after filter_lastdate
            // The filter_lastdate is when predictions were made, we check against the NEXT draw
            
            $day = $this->lotteries_m->return_day($ld);
            
            // Always calculate the next draw date after the filter lastdate
            $expected_next_draw_date = $this->lotteries_m->next_date($lottery, $day, $ld);
            
            // Convert expected date to MySQL format for comparison and display
            $next_draw_date_mysql = $this->convert_to_mysql_date($expected_next_draw_date);
            
            // Handle failed date conversion
            if (!$next_draw_date_mysql) {
                log_message('error', "Date conversion failed for: {$expected_next_draw_date}. Using raw date instead.");
                $next_draw_date = $expected_next_draw_date; // Use the raw date as fallback
                $next_draw_date_for_js = null; // No reliable JS format available
            } else {
                // Set the expected next draw date for display (user-friendly format)
                $next_draw_date = $expected_next_draw_date;
                $next_draw_date_for_js = $next_draw_date_mysql; // MySQL format for JavaScript parsing
            }
            // Now check if we have draw info and if it matches the expected date
            if ($draw_info) {
                // Check if the draw_info is for the expected NEXT date
                if ($next_draw_date_mysql && $next_draw_date_mysql == $draw_info->draw_date) {
                    // There's a draw on the expected next date - show the results
                    $should_process_wins = true;
                    $display_mode = 'results';
                    // Keep user-friendly format for display, JS format already set above
                } else {
                    // Draw info exists but not for expected date - this shouldn't happen with new logic
                    $display_mode = 'results';
                    // Convert MySQL date to user-friendly format for consistent display
                    $next_draw_date = date('l, F j, Y', strtotime($draw_info->draw_date));
                    $next_draw_date_for_js = $draw_info->draw_date; // MySQL format for JavaScript parsing
                }
            } else {
                // No draw info at all - show TBD for expected next draw
                $display_mode = 'tbd';
                // Use the user-friendly format for display, not MySQL format
                $next_draw_date = $expected_next_draw_date;
            }
        } else {
            // Filter is expired (inactive) - show results for the date the prediction was made for
            if ($filter->lastdate) {
                // For expired filters, check if there's a draw on the exact lastdate (the date predictions were made for)
                $exact_date_draw = $this->get_draw_on_date($filter->lottery_id, $filter->lastdate);
                
                if ($exact_date_draw) {
                    // Show results for the exact date the prediction was made for
                    $display_mode = 'results';
                    $draw_info = $exact_date_draw; // Use the draw from the exact prediction date
                    // Convert MySQL date to user-friendly format for consistent display
                    $next_draw_date = date('l, F j, Y', strtotime($exact_date_draw->draw_date));
                } else {
                    // No draw found on the exact date - show TBD
                    $display_mode = 'tbd';
                    // Convert filter lastdate to user-friendly format
                    $next_draw_date = date('l, F j, Y', strtotime($filter->lastdate));
                }
            } else {
                // No lastdate available - fallback to any available draw info
                if ($draw_info) {
                    $display_mode = 'results';
                    // Convert MySQL date to user-friendly format for consistent display
                    $next_draw_date = date('l, F j, Y', strtotime($draw_info->draw_date));
                } else {
                    $display_mode = 'tbd';
                    $next_draw_date = 'Unknown';
                }
            }
        }
        
        // Process win records if lottery has been updated to expected date
        // Only process if filter is still active (not already expired)
        if ($should_process_wins && $filter->active == 1) {
            $this->process_filter_win_records($filter, $draw_info, false); // Don't update lastdate during processing
            
            // Update the filter's lastdate to the draw date that was just processed
            // This ensures next access will look for the draw after this one
            $this->db->where('id', $filter->id);
            $this->db->where('user_id', $admin_id); // Security check
            $this->db->update('lottery_combination_filters', array('lastdate' => $draw_info->draw_date));
            
            // Update the filter object for current view
            $filter->lastdate = $draw_info->draw_date;
            log_message('info', "Filter {$filter->id} lastdate updated to {$draw_info->draw_date} after processing results");
            
            // After processing wins, expire the filter since results are now final
            // This happens after the user views the results
            $this->db->where('id', $filter->id);
            $this->db->where('user_id', $admin_id); // Security check
            $this->db->update('lottery_combination_filters', array('active' => 0));
            
            // Update the filter object for current view (but display will still show results)
            $filter->active = 0;
        }
        
        // Calculate win results for each ticket
        foreach ($tickets as &$ticket) {
            $ticket['win_result'] = $this->calculate_ticket_win_result($ticket['numbers'], $draw_info, $filter, $display_mode, $next_draw_date);
        }
        
        // Calculate total winners across the entire file
        $total_winners = $this->count_total_winners($filter, $draw_info, $display_mode, $next_draw_date);
        
        $this->data['filter'] = $filter;
        $this->data['tickets'] = $tickets;
        $this->data['draw_info'] = $draw_info;
        $this->data['total_tickets'] = $total_tickets;
        $this->data['total_winners'] = $total_winners;
        $this->data['per_page'] = $per_page;
        $this->data['current_page'] = $page;
        $this->data['total_pages'] = ceil($total_tickets / $per_page);
        $this->data['offset'] = $offset;
        $this->data['display_mode'] = $display_mode;
        $this->data['next_draw_date'] = $next_draw_date;
        
        // Pagination options
        $this->data['pagination_options'] = array(10, 20, 50, 100, 200, 300, 500, 1000);
        
        // Add maintenance check for layout
        $this->data['maintenance'] = $this->maintenance_m->maintenance_check();
        $this->data['users'] = $this->maintenance_m->logged_online(0);
        $this->data['admins'] = $this->maintenance_m->logged_online(1);
        $this->data['visitors'] = $this->maintenance_m->active_visitors();
        $this->data['meta_title'] = 'Combination Ticket Winner Table - lottotrak';
        
        // Handle referrer-based navigation
        $referrer = $this->input->get('referrer');
        $lottery_id = $this->input->get('lottery_id');
        $combo_id = $this->input->get('combo_id');
        
        if ($referrer === 'futures' && $lottery_id && $combo_id) {
            // User came from prediction futures - set up back navigation to futures
            $this->data['back_link'] = base_url('admin/predictions/refresh/' . $lottery_id . '?combo_id=' . $combo_id);
            $this->data['back_text'] = 'Back to Prediction Futures';
            log_message('info', "Prize::view_combination_tickets - Setting futures back navigation: lottery_id={$lottery_id}, combo_id={$combo_id}");
        } else {
            // Default back navigation to prize history
            $this->data['back_link'] = base_url('admin/prize/' . $filter->lottery_id);
            $this->data['back_text'] = 'Back to Prize History';
        }
        
        $this->data['current'] = $this->uri->segment(2);
        $this->session->set_userdata('uri', 'admin/'.$this->data['current'].'/view_combination_tickets/'.$filter_id);
        $this->data['subview'] = 'admin/prize/combination_tickets';
        
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
            $filter_id = $this->input->post('filter_id');
            $page = $this->input->post('page') ? (int)$this->input->post('page') : 1;
            $per_page = $this->input->post('per_page') ? (int)$this->input->post('per_page') : 10;
            $admin_id = $this->session->userdata('id');
            
            if (!$admin_id || !$filter_id) {
                log_message('error', 'AJAX validation failed: missing admin_id or filter_id');
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
                return;
            }
            
            // Get filter details
            $this->db->select('lcf.*, lp.lottery_name, lcfiles.file_name as original_filename, lcfiles.N, lcfiles.R');
            $this->db->from('lottery_combination_filters lcf');
            $this->db->join('lottery_profiles lp', 'lp.id = lcf.lottery_id', 'left');
            $this->db->join('lottery_combination_files lcfiles', 'lcfiles.id = lcf.combo_id', 'left');
            $this->db->where('lcf.id', $filter_id);
            $this->db->where('lcf.user', 1);
            $this->db->where('lcf.user_id', $admin_id);
            
            $filter = $this->db->get()->row();
            
            if (!$filter) {
                log_message('error', 'AJAX filter not found or access denied');
                echo json_encode(['success' => false, 'message' => 'Filter not found or access denied']);
                return;
            }
            
            // Calculate offset
            $offset = ($page - 1) * $per_page;
            
            // Get combination tickets
            $tickets = $this->get_paginated_combination_tickets($filter, $per_page, $offset);
            
            $total_tickets = $this->count_combination_tickets($filter);
            
            // Get latest draw information
            $draw_info = $this->get_latest_draw_info($filter->lottery_id, $filter->lastdate);
            
            // Determine display mode for AJAX response and process wins if needed
            $display_mode = 'normal';
            $next_draw_date = null;
            $next_draw_date_for_js = null; // Initialize JS-parseable date format
            $should_process_wins = false;
            
            // Always calculate expected next draw date for active filters, regardless of whether draw_info exists
            if ($filter->active == 1 && $filter->lastdate) {
                // Load required models
                $this->load->model('Lotteries_m', 'lotteries_m');
                
                // Get the lottery object for the next_date calculation
                $this->db->select('*');
                $this->db->from('lottery_profiles');
                $this->db->where('id', $filter->lottery_id);
                $lottery = $this->db->get()->row();
                
                if ($lottery) {
                    // CORRECTED LOGIC: Always calculate the NEXT draw date after filter_lastdate
                    // The filter_lastdate is when predictions were made, we check against the NEXT draw
                    
                    $day = $this->lotteries_m->return_day($filter->lastdate);
                    
                    // Always calculate the next draw date after the filter lastdate
                    $expected_next_draw_date = $this->lotteries_m->next_date($lottery, $day, $filter->lastdate);
                    
                    // Convert expected date to MySQL format for comparison
                    $next_draw_date_mysql = $this->convert_to_mysql_date($expected_next_draw_date);
                    
                    // Set the expected next draw date for display (keep user-friendly format)  
                    $next_draw_date = $expected_next_draw_date;
                    $next_draw_date_for_js = $next_draw_date_mysql; // MySQL format for JavaScript parsing
                    
                    // Now check if we have draw info and if it matches the expected date
                    if ($draw_info) {
                        // Check if the draw_info is for the expected NEXT date
                        if ($next_draw_date_mysql == $draw_info->draw_date) {
                            // There's a draw on the expected next date - show the results
                            $display_mode = 'results';
                            $should_process_wins = true;
                            // Keep the user-friendly format for display, don't change to MySQL format
                        } else {
                            // Draw info exists but not for expected date - this shouldn't happen with new logic
                            $display_mode = 'results';
                            // Convert MySQL date back to user-friendly format for consistent display
                            $next_draw_date = date('l, F j, Y', strtotime($draw_info->draw_date));
                            $next_draw_date_for_js = $draw_info->draw_date; // MySQL format for JavaScript parsing
                        }
                    } else {
                        // No draw info at all - show TBD for expected next draw
                        $display_mode = 'tbd';
                        // Keep the user-friendly format and set JS format
                        $next_draw_date_for_js = $next_draw_date_mysql; // MySQL format for JavaScript parsing
                    }
                }
            } else {
                // Filter is expired (inactive) - show results for the date the prediction was made for
                if ($filter->lastdate) {
                    // For expired filters, check if there's a draw on the exact lastdate (the date predictions were made for)
                    $exact_date_draw = $this->get_draw_on_date($filter->lottery_id, $filter->lastdate);
                    
                    if ($exact_date_draw) {
                        // Show results for the exact date the prediction was made for
                        $display_mode = 'results';
                        $draw_info = $exact_date_draw; // Use the draw from the exact prediction date
                        // Convert MySQL date to user-friendly format for consistent display
                        $next_draw_date = date('l, F j, Y', strtotime($exact_date_draw->draw_date));
                        $next_draw_date_for_js = $exact_date_draw->draw_date; // MySQL format for JavaScript parsing
                    } else {
                        // No draw found on the exact date - show TBD
                        $display_mode = 'tbd';
                        // Convert filter lastdate to user-friendly format
                        $next_draw_date = date('l, F j, Y', strtotime($filter->lastdate));
                        $next_draw_date_for_js = $filter->lastdate; // MySQL format for JavaScript parsing
                    }
                } else {
                    // No lastdate available - fallback to any available draw info
                    if ($draw_info) {
                        $display_mode = 'results';
                        // Convert MySQL date to user-friendly format for consistent display
                        $next_draw_date = date('l, F j, Y', strtotime($draw_info->draw_date));
                        $next_draw_date_for_js = $draw_info->draw_date; // MySQL format for JavaScript parsing
                    } else {
                        $display_mode = 'tbd';
                        $next_draw_date = 'Unknown';
                        $next_draw_date_for_js = null; // No valid date available
                    }
                }
            }
            
            // Process win records if lottery has been updated to expected date (AJAX)
            // Only process if filter is still active (not already expired)
            if ($should_process_wins && $filter->active == 1) {
                $this->process_filter_win_records($filter, $draw_info, false); // Don't update lastdate during processing
                
                // Update the filter's lastdate to the draw date that was just processed
                // This ensures next access will look for the draw after this one
                $this->db->where('id', $filter->id);
                $this->db->where('user_id', $admin_id); // Security check
                $this->db->update('lottery_combination_filters', array('lastdate' => $draw_info->draw_date));
                
                // Update the filter object for current response
                $filter->lastdate = $draw_info->draw_date;
                log_message('info', "AJAX: Filter {$filter->id} lastdate updated to {$draw_info->draw_date} after processing results");
                
                // After processing wins, expire the filter since results are now final
                $this->db->where('id', $filter->id);
                $this->db->where('user_id', $admin_id); // Security check
                $this->db->update('lottery_combination_filters', array('active' => 0));
                
                // Update the filter object for current response
                $filter->active = 0;
            }
            
            // Calculate win results for each ticket
            foreach ($tickets as &$ticket) {
                $ticket['win_result'] = $this->calculate_ticket_win_result($ticket['numbers'], $draw_info, $filter, $display_mode, $next_draw_date);
            }
            
            // Calculate pagination data
            $total_pages = ceil($total_tickets / $per_page);
            
            // Calculate total winners across the entire file (not just current page)
            $total_winners = $this->count_total_winners($filter, $draw_info, $display_mode, $next_draw_date);
            
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
                'total_winners' => $total_winners,
                'filter' => $filter,
                'draw_info' => $draw_info,
                'display_mode' => $display_mode,
                'next_draw_date' => $next_draw_date,
                'next_draw_date_for_js' => $next_draw_date_for_js // MySQL format for reliable JavaScript parsing
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
        
        // For independent extra ball lotteries (duplicate_extra_ball = 1),
        // the file contains main numbers + extra ball, so actual count is picks + 1
        $is_independent_extra_ball = (!empty($filter->duplicate_extra_ball) && !empty($filter->extra_ball));
        $expected_numbers_per_line = $expected_picks;
        if ($is_independent_extra_ball) {
            $expected_numbers_per_line = $expected_picks + 1; // Main numbers + independent extra ball
        }
        
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
                    
                    // Validate the expected number count for this lottery type
                    if (count($numbers) == $expected_numbers_per_line) {
                        $ticket_data = array(
                            'ticket_number' => $current_offset + 1,
                            'numbers' => $numbers,
                            'is_independent_extra_ball' => $is_independent_extra_ball
                        );
                        
                        // For independent extra ball lotteries, separate main numbers and extra ball
                        if ($is_independent_extra_ball) {
                            $ticket_data['main_numbers'] = array_slice($numbers, 0, $expected_picks);
                            $ticket_data['extra_ball'] = $numbers[$expected_picks]; // Last number is the extra ball
                        }
                        
                        $tickets[] = $ticket_data;
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
        
        // For independent extra ball lotteries (duplicate_extra_ball = 1),
        // the file contains main numbers + extra ball, so actual count is picks + 1
        $is_independent_extra_ball = (!empty($filter->duplicate_extra_ball) && !empty($filter->extra_ball));
        $expected_numbers_per_line = $expected_picks;
        if ($is_independent_extra_ball) {
            $expected_numbers_per_line = $expected_picks + 1; // Main numbers + independent extra ball
        }
        
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
                    
                    // Validate the expected number count for this lottery type
                    if (count($numbers) == $expected_numbers_per_line) {
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
    private function get_latest_draw_info($lottery_id, $filter_lastdate = null)
    {
        try {
            $this->db->select('*');
            $this->db->from('lottery_profiles');
            $this->db->where('id', $lottery_id);
            $this->db->limit(1);
            $lottery_profile = $this->db->get()->row();
            
            if (!$lottery_profile) {
                return null;
            }
            
            // Load the Lotteries model to convert lottery name to table name
            $this->load->model('Lotteries_m', 'lotteries_m');
            $table_name = $this->lotteries_m->lotto_table_convert($lottery_profile->lottery_name);
            
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
            
            // If we have a filter lastdate, try to get the appropriate expected draw first
            if ($filter_lastdate) {
                try {
                    // FIRST: Calculate the NEXT draw date after filter_lastdate (the draw we predicted for)
                    // The filter_lastdate is when predictions were made, we check for the NEXT draw
                    $day = $this->lotteries_m->return_day($filter_lastdate);
                    
                    // Always calculate the next draw date after the filter lastdate
                    $expected_next_draw_date = $this->lotteries_m->next_date($lottery_profile, $day, $filter_lastdate);
                    
                    // Check if there's a draw on the expected NEXT draw date (the one we predicted for)
                    $expected_draw = $this->get_draw_on_date($lottery_id, $expected_next_draw_date);
                    
                    if ($expected_draw) {
                        // There's a draw on the expected date we predicted for - return this draw
                        return $expected_draw;
                    }
                    
                    // If no draw on expected date, this means the draw hasn't happened yet
                    // Return null so the system shows TBD for the future draw
                    return null;
                    
                    
                    if ($expected_draw) {
                        
                        // Verify the drawn numbers exist and are valid (not zero or null)
                        $has_valid_numbers = false;
                        
                        // Check for individual ball fields (ball1, ball2, etc.)
                        for ($i = 1; $i <= 9; $i++) {
                            $ball_field = 'ball' . $i;
                            if (property_exists($expected_draw, $ball_field) && 
                                !is_null($expected_draw->$ball_field) && 
                                $expected_draw->$ball_field > 0) {
                                $has_valid_numbers = true;
                                break; // Found at least one valid ball number
                            }
                        }
                        
                        // Also check for other common number field names
                        if (!$has_valid_numbers) {
                            $number_fields = array('numbers', 'drawn_numbers', 'winning_numbers');
                            foreach ($number_fields as $field) {
                                if (property_exists($expected_draw, $field) && 
                                    !empty($expected_draw->$field) && 
                                    trim($expected_draw->$field) != '') {
                                    $has_valid_numbers = true;
                                    break;
                                }
                            }
                        }
                        
                        if ($has_valid_numbers) {
                            return $expected_draw;
                        } else {
                            return null; // No valid numbers set, treat as TBD
                        }
                    } else {
                        // No draw found on expected date, look for next available draw after filter_lastdate
                        
                        // Query to find next available draw
                        $this->db->select('*');
                        $this->db->from($table_name);
                        $this->db->where('draw_date >', $filter_lastdate);
                        $this->db->order_by('draw_date', 'ASC');
                        $this->db->limit(5);
                        $next_draws_query = $this->db->get();
                        $next_draws = $next_draws_query->result();
                        
                        if ($next_draws) {
                            // Use the first (closest) draw
                            $next_available_draw = $next_draws[0];
                            $next_available_draw->extra_ball_included = ($lottery_profile->extra_ball == 1);
                            return $next_available_draw;
                        } else {
                            return null; // Return null so the display mode will be set to TBD
                        }
                    }
                } catch (Exception $e) {
                    log_message('error', "get_latest_draw_info: error calculating expected draw date: " . $e->getMessage());
                    // When we have a filter_lastdate but error occurred, return null instead of falling back
                    return null;
                }
            }
            
            // Only get latest draw if no filter_lastdate was provided
            // When filter_lastdate is provided, we only want the specific expected draw or null
            if (!$filter_lastdate) {
                // Get the latest draw for this lottery with non-zero extra ball
                // Draws with extra = 0 are bonus/extra draws not used for combination comparison
                $this->db->select('*');
                $this->db->from($table_name);
                $this->db->where('extra > 0'); // Only get draws with valid extra ball for combination comparison
                $this->db->order_by('draw_date', 'DESC');
                $this->db->limit(1);
                $latest_draw = $this->db->get()->row();
                
                if ($latest_draw) {
                    // Add extra ball information from lottery profile
                    $latest_draw->extra_ball_included = ($lottery_profile->extra_ball == 1);
                } 
                
                return $latest_draw;
            } else {
                // filter_lastdate was provided but no draw found on expected date
                return null;
            }
            
        } catch (Exception $e) {
            log_message('error', 'get_latest_draw_info exception: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Check if there's a draw on a specific date (regardless of extra ball value)
     * @param int $lottery_id Lottery ID
     * @param string $expected_date Expected draw date (YYYY-MM-DD format)
     * @return object|null Draw info if found, null if not found
     */
    private function get_draw_on_date($lottery_id, $expected_date)
    {
        try {
            $this->db->select('*');
            $this->db->from('lottery_profiles');
            $this->db->where('id', $lottery_id);
            $this->db->limit(1);
            $lottery_profile = $this->db->get()->row();
            
            if (!$lottery_profile) {
                return null;
            }
            
            // Load the Lotteries model to convert lottery name to table name
            $this->load->model('Lotteries_m', 'lotteries_m');
            $table_name = $this->lotteries_m->lotto_table_convert($lottery_profile->lottery_name);
            
            if (!$table_name || !is_string($table_name) || strlen($table_name) == 0) {
                log_message('error', "get_draw_on_date: invalid table name generated for lottery_name: {$lottery_profile->lottery_name}");
                return null;
            }
            
            // Check if the table exists before querying
            $table_exists = $this->db->table_exists($table_name);
            if (!$table_exists) {
                log_message('error', "get_draw_on_date: table {$table_name} does not exist");
                return null;
            }
            
            // Convert date format to MySQL format (YYYY-MM-DD) if needed
            $mysql_date = $this->convert_to_mysql_date($expected_date);
            if (!$mysql_date) {
                log_message('error', "get_draw_on_date: invalid date format: {$expected_date}");
                return null;
            }
            
            // Get the draw for this specific date (any draw, regardless of extra ball)
            $this->db->select('*');
            $this->db->from($table_name);
            $this->db->where('draw_date', $mysql_date);
            $this->db->limit(1);
            $draw_on_date = $this->db->get()->row();
            
            if ($draw_on_date) {
                // Add extra ball information from lottery profile
                $draw_on_date->extra_ball_included = ($lottery_profile->extra_ball == 1);
            } 
            
            return $draw_on_date;
            
        } catch (Exception $e) {
            log_message('error', 'get_draw_on_date exception: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Convert various date formats to MySQL date format (YYYY-MM-DD)
     * @param string $date_string Date in various formats
     * @return string|false MySQL formatted date or false if invalid
     */
    private function convert_to_mysql_date($date_string)
    {
        try {
            if (empty($date_string)) {
                return false;
            }
            
            // If already in MySQL format (YYYY-MM-DD), return as is
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_string)) {
                return $date_string;
            }
            
            // Try to parse the date using strtotime
            $timestamp = strtotime($date_string);
            if ($timestamp === false) {
                return false;
            }
            
            // Convert to MySQL format
            $mysql_date = date('Y-m-d', $timestamp);
            return $mysql_date;
            
        } catch (Exception $e) {
            log_message('error', 'convert_to_mysql_date exception: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Calculate win result for a ticket
     */
    private function calculate_ticket_win_result($ticket_numbers, $draw_info, $filter, $display_mode = 'normal', $next_draw_date = null)
    {
        // Handle TBD display mode for future draws (even when draw_info is null)
        if ($display_mode == 'tbd') {
            return array(
                'category' => 'TBD (To Be Determined)',
                'color_class' => 'tbd-result',
                'matches' => 'TBD',
                'bonus_match' => 'TBD',
                'next_draw_date' => $next_draw_date
            );
        }
        
        if (!$draw_info) {
            return array(
                'category' => 'No Draw Data',
                'color_class' => 'no-win',
                'matches' => 0,
                'bonus_match' => false
            );
        }
        
        
        // Count matches regardless of filter status for normal and results modes
        $matches = $this->count_ticket_matches($ticket_numbers, $draw_info);
        $bonus_match = $this->check_bonus_match_for_ticket($ticket_numbers, $draw_info);
        
        // Get prize profile to determine valid win categories
        $prize_profile = $this->get_lottery_prize_profile($filter->lottery_id);
        
        // Get the required number of matches for top prize (from combination file R value)
        $this->db->select('R');
        $this->db->from('lottery_combination_files');
        $this->db->where('id', $filter->combo_id);
        $file_query = $this->db->get();
        $file_record = $file_query->row();
        $required_matches_for_top_prize = $file_record ? (int)$file_record->R : 0;
        
        // Get extra ball status for this lottery including independent extra ball support
        $this->db->select('extra_ball, duplicate_extra_ball');
        $this->db->from('lottery_profiles');
        $this->db->where('id', $filter->lottery_id);
        $lottery_profile = $this->db->get()->row();
        $extra_ball_included = ($lottery_profile && $lottery_profile->extra_ball == 1);
        $is_independent_extra_ball = ($lottery_profile && $lottery_profile->duplicate_extra_ball == 1 && $lottery_profile->extra_ball == 1);
        
        // Determine win category based on prize profile
        $category = 'Not a Winner';
        $color_class = 'not-a-winner';
        
        if ($prize_profile) {
            // Handle independent extra ball lotteries (like Daily Grand)
            if ($is_independent_extra_ball) {
                // For independent extra ball: total match count includes the extra ball
                // No separate bonus matching needed since extra ball is part of main numbers
                $win_category = $this->determine_win_category($matches, false, $prize_profile, false);
            } else if ($extra_ball_included) {
                // For regular extra ball lotteries
                // Special rule for extra number validation when extra is included (same as auto-update logic)
                if ($matches == $required_matches_for_top_prize && $bonus_match) {
                    $win_category = $required_matches_for_top_prize . '_win_extra';
                    if (property_exists($prize_profile, $win_category) && $prize_profile->$win_category == 1) {
                        // This is a top prize win with extra number - skip normal determination
                        $win_category = $required_matches_for_top_prize . '_win_extra';
                    } else {
                        // Use normal determination
                        $win_category = $this->determine_win_category($matches, $bonus_match, $prize_profile, $extra_ball_included);
                    }
                } else {
                    // Use normal determination for all other cases
                    $win_category = $this->determine_win_category($matches, $bonus_match, $prize_profile, $extra_ball_included);
                }
            } else {
                // No extra ball - use normal determination
                $win_category = $this->determine_win_category($matches, $bonus_match, $prize_profile, $extra_ball_included);
            }
            
            if ($win_category) {
                // Determine display category and color based on win category
                if ($win_category == 'extra') {
                    // Pure bonus win (only bonus number, no main matches)
                    $category = 'BONUS WIN';
                    $color_class = 'bonus-win';
                } elseif (strpos($win_category, '_win_extra') !== false) {
                    // Main matches + bonus (e.g., "6_win_extra" = "6 Winners + Bonus")
                    $match_number = (int)str_replace('_win_extra', '', $win_category);
                    if ($match_number == ($required_matches_for_top_prize - 1)) {
                        // Second-highest prize with bonus (MAJOR PRIZE)
                        $category = $match_number . ' Winners + Bonus (MAJOR PRIZE)';
                        $color_class = 'jackpot-win';
                    } else {
                        $category = $match_number . ' Winners + Bonus';
                        $color_class = 'bonus-win';
                    }
                } else {
                    // Regular wins without bonus
                    $match_number = (int)str_replace('_win', '', $win_category);
                    if ($match_number == $required_matches_for_top_prize) {
                        // Top prize (MAJOR PRIZE)
                        $category = $match_number . ' Winners (MAJOR PRIZE)';
                        $color_class = 'jackpot-win';
                    } else {
                        $category = $match_number . ' Winning Numbers';
                        $color_class = 'minor-win';
                    }
                }
            } else {
                // No valid win category found
                if ($matches > 0) {
                    $category = $matches . ' Matches (Not a Winner)';
                    $color_class = 'no-win';
                }
            }
        } else {
            // Fallback if no prize profile found
            $category = 'No Prize Profile';
            $color_class = 'no-win';
        }
        
        return array(
            'category' => $category,
            'color_class' => $color_class,
            'matches' => $matches,
            'bonus_match' => $bonus_match
        );
    }
    
    /**
     * Process win records for a filter when lottery has been updated to expected draw date
     */
    private function process_filter_win_records($filter, $draw_info, $update_lastdate = true)
    {
        try {
            // Get combination tickets from the file
            $combination_tickets = $this->get_combination_tickets_for_filter($filter);
            if (empty($combination_tickets)) {
                log_message('error', "No combination tickets found for filter {$filter->id}");
                return;
            }
            
            // Get prize profile for win category determination
            $prize_profile = $this->get_lottery_prize_profile($filter->lottery_id);
            if (!$prize_profile) {
                log_message('error', "No prize profile found for lottery {$filter->lottery_id}");
                return;
            }
            
            // Get the required number of matches for top prize (from combination file R value)
            $this->db->select('R');
            $this->db->from('lottery_combination_files');
            $this->db->where('id', $filter->combo_id);
            $file_query = $this->db->get();
            $file_record = $file_query->row();
            $required_matches_for_top_prize = $file_record ? (int)$file_record->R : 0;
            
            // Get lottery profile for extra ball information
            $this->db->select('extra_ball');
            $this->db->from('lottery_profiles');
            $this->db->where('id', $filter->lottery_id);
            $lottery_profile = $this->db->get()->row();
            $extra_ball_included = ($lottery_profile && $lottery_profile->extra_ball == 1);
            
            // Skip if extra is included and extra number is 0
            if ($this->should_skip_draw($draw_info, $extra_ball_included)) {
                log_message('info', "Skipping win record processing - extra ball is 0 and extra is included");
                return;
            }
            
            // Process each combination ticket against this draw
            $win_updates = array();
            foreach ($combination_tickets as $ticket) {
                $matches = $this->count_ticket_matches($ticket, $draw_info);
                $bonus_match = $this->check_bonus_match_for_ticket($ticket, $draw_info);
                
                // Special rule for extra number validation when extra is included
                if ($extra_ball_included) {
                    // For top prize, must have exact matches AND must match the exact bonus number
                    if ($matches == $required_matches_for_top_prize && $bonus_match) {
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
                $win_category = $this->determine_win_category($matches, $bonus_match, $prize_profile, $extra_ball_included);
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
                
                // Only update lastdate if requested (for batch processing, not user viewing)
                if ($update_lastdate) {
                    $update_data['lastdate'] = $draw_info->draw_date;
                }
                
                // Update the filter record with new win counts and optionally lastdate
                if (!empty($update_data)) {
                    $this->db->where('id', $filter->id);
                    $this->db->update('lottery_combination_filters', $update_data);
                    
                    // Update the filter object for current view only if lastdate was updated
                    if ($update_lastdate) {
                        $filter->lastdate = $draw_info->draw_date;
                    }
                    
                    log_message('info', "Filter {$filter->id} win records updated with draw from {$draw_info->draw_date}" . ($update_lastdate ? ", lastdate updated" : ", lastdate preserved"));
                }
            } else {
                // Even if no wins, update the lastdate only if requested
                if ($update_lastdate) {
                    $this->db->where('id', $filter->id);
                    $this->db->update('lottery_combination_filters', array('lastdate' => $draw_info->draw_date));
                    
                    // Update the filter object for current view
                    $filter->lastdate = $draw_info->draw_date;
                    
                    log_message('info', "Filter {$filter->id} lastdate updated to {$draw_info->draw_date} (no wins)");
                } else {
                    log_message('info', "Filter {$filter->id} win processing completed, lastdate preserved (no wins)");
                }
            }
            
        } catch (Exception $e) {
            log_message('error', 'Exception in process_filter_win_records: ' . $e->getMessage());
        }
    }
    
    /**
     * Explicitly expire filters when returning to Prize History
     * This method is called when user navigates back from Combination Ticket Winner view
     */
    public function expire_filters_on_return($lottery_id)
    {
        $admin_id = $this->session->userdata('id');
        
        if (!$admin_id) {
            redirect('admin/login');
            return;
        }
        
        // Expire completed filters
        $this->expire_completed_filters($admin_id, $lottery_id);
        
        // Redirect back to Prize History
        redirect('admin/prize/index/' . $lottery_id);
    }
    
    /**
     * Expire filters that have been processed and should now be marked as expired
     * Note: This method is now disabled to prevent automatic filter expiration
     * This is called when returning to Prize History page from Combination Ticket view
     */
    private function expire_completed_filters($admin_id, $lottery_id)
    {
        // Automatic filter expiration is disabled
        // Filters will remain active until manually expired by user
        return;
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
    
    /**
     * Count total winners across all tickets in a combination file (OPTIMIZED)
     * @param object $filter Filter object
     * @param object $draw_info Draw information
     * @param string $display_mode Display mode (normal, tbd, results)
     * @param string $next_draw_date Next draw date for TBD mode
     * @return int Total number of winning tickets
     */
    private function count_total_winners($filter, $draw_info, $display_mode = 'normal', $next_draw_date = null)
    {
        if ($display_mode == 'tbd' || !$draw_info) {
            return 0;
        }
        
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
        
        // Build file path
        $pick_dir = 'pick' . $expected_picks;
        $file_path = FCPATH . 'combinations/' . $pick_dir . '/' . $filter->file_name . '.txt';
        
        if (!file_exists($file_path)) {
            return 0;
        }
        
        // Get prize profile for efficient win validation
        $this->db->select('*');
        $this->db->from('lottery_prize_profiles');
        $this->db->where('lottery_id', $filter->lottery_id);
        $prize_profile = $this->db->get()->row();
        
        if (!$prize_profile) {
            return 0;
        }
        
        // Get extra ball status for this lottery
        $this->db->select('extra_ball');
        $this->db->from('lottery_profiles');
        $this->db->where('id', $filter->lottery_id);
        $lottery_profile = $this->db->get()->row();
        $extra_ball_included = ($lottery_profile && $lottery_profile->extra_ball == 1);
        
        // Extract drawn numbers and bonus number once
        $drawn_numbers = $this->extract_drawn_numbers_from_draw($draw_info);
        $bonus_number = $this->extract_bonus_number_from_draw($draw_info);
        
        if (empty($drawn_numbers)) {
            return 0;
        }
        
        // Count winners in the entire file with optimized logic
        $total_winners = 0;
        $file_content = file_get_contents($file_path);
        
        if ($file_content) {
            $lines = explode("\n", $file_content);
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $numbers = preg_split('/[\s,]+/', $line);
                    $numbers = array_map('intval', array_filter($numbers, 'is_numeric'));
                    
                    if (count($numbers) == $expected_picks) {
                        // Quick match calculation using array_intersect
                        $matches = count(array_intersect($numbers, $drawn_numbers));
                        $bonus_match = !is_null($bonus_number) && in_array($bonus_number, $numbers);
                        
                        // Quick win category determination using prize profile
                        if ($this->fast_determine_win_category($matches, $bonus_match, $prize_profile, $extra_ball_included)) {
                            $total_winners++;
                        }
                    }
                }
            }
        }
        
        return $total_winners;
    }
    
    /**
     * Fast win category determination for counting (optimized version)
     * @param int $matches Number of matches
     * @param bool $bonus_match Bonus match status
     * @param object $prize_profile Prize profile
     * @return bool True if it's a winning combination
     */
    private function fast_determine_win_category($matches, $bonus_match, $prize_profile, $extra_ball_included = true)
    {
        // Check from highest to lowest prize category
        $prize_categories = array(9, 8, 7, 6, 5, 4, 3, 2, 1);
        
        foreach ($prize_categories as $category) {
            if ($matches < $category) {
                continue; // Not enough matches for this category
            }
            
            $regular_field = $category . '_win';
            $extra_field = $category . '_win_extra';
            
            // Check for extra win first (higher priority) - only if extra ball is included
            if ($extra_ball_included && $bonus_match && 
                property_exists($prize_profile, $extra_field) && 
                !is_null($prize_profile->$extra_field) && 
                $prize_profile->$extra_field == 1) {
                
                return true;
            }
            
            // Check for regular win
            if (property_exists($prize_profile, $regular_field) && 
                !is_null($prize_profile->$regular_field) && 
                $prize_profile->$regular_field == 1) {
                
                return true;
            }
        }
        
        // Check for extra-only category - only if extra ball is included
        if ($extra_ball_included && $bonus_match && 
            property_exists($prize_profile, 'extra') && 
            !is_null($prize_profile->extra) && 
            $prize_profile->extra == 1) {
            
            return true;
        }
        
        return false; // No win category matched
    }
}
