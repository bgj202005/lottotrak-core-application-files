<?php
// Test script to verify the combination files query works correctly
require_once('../system/core/CodeIgniter.php');

// Simulate a user session
$_SESSION['id'] = 1; // Assuming user ID 1

// Load the database
$CI =& get_instance();
$CI->load->database();

// Test the new query
$lottery_id = 1;
$current_user_id = 1;

echo "Testing new combination files query for lottery_id: $lottery_id, user_id: $current_user_id\n\n";

// Get balls_drawn for lottery
$CI->db->select('balls_drawn');
$CI->db->from('lottery_profiles');
$CI->db->where('id', $lottery_id);
$lottery = $CI->db->get()->row();

if (!$lottery) {
    echo "Lottery not found!\n";
    exit;
}

$balls_drawn = $lottery->balls_drawn;
echo "Balls drawn for lottery $lottery_id: $balls_drawn\n\n";

// Run the new query
$CI->db->select('lcf.id, lcf.file_name, lcf.N, lcf.R, lcf.CCCC, 
                COALESCE(MAX(lfc.active), 0) as active');
$CI->db->from('lottery_combination_files lcf');
$CI->db->join('lottery_combination_filters lfc', 
             'lcf.id = lfc.combo_id AND lfc.user = 1 AND lfc.user_id = ' . (int)$current_user_id, 'left');
$CI->db->where('lcf.R', $balls_drawn);
$CI->db->group_by('lcf.id, lcf.file_name, lcf.N, lcf.R, lcf.CCCC');
$CI->db->order_by('lcf.file_name', 'ASC');

echo "Query: " . $CI->db->get_compiled_select() . "\n\n";

$CI->db->select('lcf.id, lcf.file_name, lcf.N, lcf.R, lcf.CCCC, 
                COALESCE(MAX(lfc.active), 0) as active');
$CI->db->from('lottery_combination_files lcf');
$CI->db->join('lottery_combination_filters lfc', 
             'lcf.id = lfc.combo_id AND lfc.user = 1 AND lfc.user_id = ' . (int)$current_user_id, 'left');
$CI->db->where('lcf.R', $balls_drawn);
$CI->db->group_by('lcf.id, lcf.file_name, lcf.N, lcf.R, lcf.CCCC');
$CI->db->order_by('lcf.file_name', 'ASC');

$query = $CI->db->get();
$results = $query->result_array();

echo "Results:\n";
foreach ($results as $row) {
    $status = $row['active'] ? 'ACTIVE' : 'EXPIRED';
    echo "ID: {$row['id']}, File: {$row['file_name']}, Status: $status\n";
}
?>