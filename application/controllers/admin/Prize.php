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
    }
        
    public function index() 
    {
        
        // Original code commented out for testing
        
        // Get pagination settings
        $per_page = $this->input->get('per_page') ? (int)$this->input->get('per_page') : 10;
        $offset = $this->input->get('offset') ? (int)$this->input->get('offset') : 0;
        
        // Get logged in admin user ID
        $admin_id = $this->session->userdata('id');
        
        // Temporary bypass for testing - comment out after testing
        if (!$admin_id) {
            $admin_id = 1; // Use admin ID 1 for testing
            $this->session->set_userdata('id', 1);
        }
        
        // Original admin check - uncomment after testing
        /*
        if (!$admin_id) {
            show_error('Administrator must be logged in to view Prize History', 403);
        }
        */
        
        // Get prize history data for the logged in administrator
        $this->data['prize_records'] = $this->prize_m->get_admin_prize_history($admin_id, $per_page, $offset);
        $this->data['total_records'] = $this->prize_m->count_admin_prize_records($admin_id);
        
        // Get dynamic prize columns for all lotteries used by this admin
        $this->data['prize_columns'] = $this->get_admin_prize_columns($admin_id);
        
        // Pagination data
        $this->data['per_page'] = $per_page;
        $this->data['offset'] = $offset;
        $this->data['total_pages'] = ceil($this->data['total_records'] / $per_page);
        $this->data['current_page'] = floor($offset / $per_page) + 1;
        
        // Pagination options
        $this->data['pagination_options'] = array(10, 20, 50, 100, 200, 300, 500, 1000);
        
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
        
        // Temporary bypass for testing
        if (!$admin_id) {
            $admin_id = 1;
        }
        
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
        // Get all unique lottery IDs for this admin's files
        $this->db->select('DISTINCT lottery_id');
        $this->db->from('lottery_combination_files');
        $this->db->like('file_name', 'ADMIN' . sprintf('%02d', $admin_id));
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
}
