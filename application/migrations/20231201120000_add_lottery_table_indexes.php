<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Lottery_Table_Indexes extends CI_Migration {

    public function up()
    {
        // Add indexes to common lottery table patterns for better performance
        // This migration should be run for existing lottery tables
        
        // Get all lottery profiles to determine table names
        $this->load->model('lotteries_m');
        $lotteries = $this->lotteries_m->get();
        
        foreach ($lotteries as $lottery) {
            $table_name = $this->lotteries_m->lotto_table_convert($lottery->lottery_name);
            
            // Check if table exists before adding indexes
            if ($this->db->table_exists($table_name)) {
                // Add index on draw_date for ORDER BY performance
                $this->add_index_if_not_exists($table_name, 'idx_draw_date', 'draw_date');
                
                // Add index on extra field for trend filtering
                $this->add_index_if_not_exists($table_name, 'idx_extra', 'extra');
                
                // Add composite index for trend queries
                $this->add_composite_index_if_not_exists($table_name, 'idx_extra_draw_date', ['extra', 'draw_date']);
                
                // Add indexes on statistical fields for filtering
                $this->add_index_if_not_exists($table_name, 'idx_sum_draw', 'sum_draw');
                $this->add_index_if_not_exists($table_name, 'idx_sum_digits', 'sum_digits');
                $this->add_index_if_not_exists($table_name, 'idx_odd', 'odd');
                $this->add_index_if_not_exists($table_name, 'idx_even', 'even');
                $this->add_index_if_not_exists($table_name, 'idx_range_draw', 'range_draw');
            }
        }
    }

    public function down()
    {
        // Get all lottery profiles to determine table names
        $this->load->model('lotteries_m');
        $lotteries = $this->lotteries_m->get();
        
        foreach ($lotteries as $lottery) {
            $table_name = $this->lotteries_m->lotto_table_convert($lottery->lottery_name);
            
            if ($this->db->table_exists($table_name)) {
                // Drop indexes
                $this->drop_index_if_exists($table_name, 'idx_draw_date');
                $this->drop_index_if_exists($table_name, 'idx_extra');
                $this->drop_index_if_exists($table_name, 'idx_extra_draw_date');
                $this->drop_index_if_exists($table_name, 'idx_sum_draw');
                $this->drop_index_if_exists($table_name, 'idx_sum_digits');
                $this->drop_index_if_exists($table_name, 'idx_odd');
                $this->drop_index_if_exists($table_name, 'idx_even');
                $this->drop_index_if_exists($table_name, 'idx_range_draw');
            }
        }
    }
    
    /**
     * Add index if it doesn't already exist
     */
    private function add_index_if_not_exists($table, $index_name, $field)
    {
        if (!$this->index_exists($table, $index_name)) {
            $this->db->query("CREATE INDEX {$index_name} ON {$table} ({$field})");
        }
    }
    
    /**
     * Add composite index if it doesn't already exist
     */
    private function add_composite_index_if_not_exists($table, $index_name, $fields)
    {
        if (!$this->index_exists($table, $index_name)) {
            $field_list = implode(',', $fields);
            $this->db->query("CREATE INDEX {$index_name} ON {$table} ({$field_list})");
        }
    }
    
    /**
     * Drop index if it exists
     */
    private function drop_index_if_exists($table, $index_name)
    {
        if ($this->index_exists($table, $index_name)) {
            $this->db->query("DROP INDEX {$index_name} ON {$table}");
        }
    }
    
    /**
     * Check if index exists on table
     */
    private function index_exists($table, $index_name)
    {
        $query = $this->db->query("SHOW INDEX FROM {$table} WHERE Key_name = '{$index_name}'");
        return $query->num_rows() > 0;
    }
}