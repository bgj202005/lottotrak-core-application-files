<?php
// OPTIMIZED APPROACH - Save already filtered combinations

/**
 * Save pre-filtered combinations directly to file (no re-filtering needed)
 * 
 * @param array $filtered_combinations Already filtered combinations from Generate Tickets
 * @param string $output_file_path Path to save the combinations
 * @return bool True on success
 */
function save_prefiltered_combinations_to_file($filtered_combinations, $output_file_path)
{
    log_message('info', "save_prefiltered_combinations_to_file: Saving " . count($filtered_combinations) . " pre-filtered combinations");
    
    $output_handle = fopen($output_file_path, 'w');
    if (!$output_handle) {
        return false;
    }

    $saved_count = 0;
    foreach ($filtered_combinations as $combo) {
        // Format combination for output
        if (is_array($combo)) {
            $output_line = implode(' ', $combo);
            fwrite($output_handle, $output_line . "\n");
            $saved_count++;
        }
    }

    fclose($output_handle);
    
    log_message('info', "save_prefiltered_combinations_to_file: Saved {$saved_count} combinations directly");
    return $saved_count > 0;
}

// CURRENT CONTROLLER MODIFICATION NEEDED:
// Instead of calling save_filtered_combinations_to_file() which re-filters,
// Store filtered combinations after Generate Tickets and pass them to save method:

// In save_tickets method:
// 1. Get the filtered combinations that were already displayed
// 2. Call save_prefiltered_combinations_to_file($filtered_combos, $output_path)
// 3. Set CCCC = count($filtered_combos) - no need to recalculate
