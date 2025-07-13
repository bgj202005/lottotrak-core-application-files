# Refresh Method Fix Summary

## Problem
The `refresh` method in `Predictions.php` was not finding combination files because it was looking in the wrong directory.

## Root Cause
The combination files are stored in subdirectories like:
- `combinations/pick5/` for 5-ball games  
- `combinations/pick7/` for 7-ball games
- etc.

But the code was looking directly in `combinations/` directory.

## Solution
Modified the file path construction in the refresh method (around line 2040) to include the correct pick subdirectory:

### Before:
```php
$combinations_dir = FCPATH . 'combinations/';
$filepath = $combinations_dir . $filename . '.txt';
```

### After:
```php
$pick_number = $saved_settings['R']; // Get the pick number (balls drawn)
$combinations_dir = FCPATH . 'combinations/pick' . $pick_number . '/';
$filepath = $combinations_dir . $filename . '.txt';
```

## Example
For a Pick 6 lottery with saved settings containing `R = 6` and filename `"06077ADMIN01"`:
- **Before**: Would look for `d:\wamp64\www\lottotrak\combinations\06077.txt` ❌
- **After**: Will look for `d:\wamp64\www\lottotrak\combinations\pick6\06077ADMIN01.txt` ✅

## Key Fix Details
1. **Directory Structure**: Files are stored in `combinations/pick{R}/` subdirectories
2. **Filename Format**: Files have ADMIN suffix (e.g., `06077ADMIN01.txt`)
3. **Path Construction**: Uses full filename with ADMIN suffix for file lookup
4. **User Display**: Shows clean filename without ADMIN suffix in success messages

## Verification
The system now properly constructs file paths based on the R (pick number) value stored in the lottery_combination_filters table and looks in the appropriate pick subdirectory.

## Expected Behavior
When the refresh method is called with a valid record ID (Eye icon clicked):
1. Retrieves saved settings from database 
2. Extracts the R value (pick number) 
3. Constructs correct file path: `combinations/pick{R}/{filename}.txt`
4. Successfully finds and processes the combination file
5. **Automatically generates and displays combination tickets** with restored filter settings
6. **Auto-scrolls to combinations section** with visual highlighting
7. **Shows enhanced success message** with combination count

## Enhanced Features Added
- **Auto-Display**: Combinations are automatically displayed after Eye icon click
- **Unified Logic**: Uses same proven ticket generation logic as "Generate Tickets" button
- **Session Integration**: Properly sets all session data for seamless experience
- **Direct Method Call**: Refresh method calls combination method directly with restored settings
- **Visual Enhancement**: Auto-generated tickets have special styling and highlighting
- **Auto-Scroll**: Page automatically scrolls to combinations section  
- **Enhanced Messages**: Better success/error messages with formatting
- **Visual Feedback**: Temporary border highlight around combinations section

### New Approach: Direct Method Integration
Instead of duplicating ticket generation logic, the refresh method now:
1. Sets session data with restored settings
2. Prepares POST data to simulate form submission
3. Calls the `combination()` method directly
4. Uses the exact same proven logic as "Generate Tickets" button

This ensures 100% consistency and eliminates any potential discrepancies between manual and automatic ticket generation.

This resolves the "Could not generate numbers with the restored settings" error and provides immediate display of combinations without requiring "Generate Tickets" button click.

## Additional Issue Fixed: Immediate Expiration Problem

### Problem
After clicking the Eye icon, combinations would be restored but the status would change from "Active" to "Expired" on the next page load.

### Root Cause
The system has an automatic expiration check (`verify_active_date`) that runs when the futures page loads. This check compares each filter's `lastdate` with the lottery's last draw date and expires any filters where `lastdate <= last_draw_date`.

### Solution
Added code to update the `lastdate` field to the next draw date when restoring settings, preventing immediate expiration:

```php
// Update the lastdate to prevent immediate expiration
$mysql_next_date = date('Y-m-d H:i:s', strtotime($this->data['lottery']->next_draw_date));
$update_data = [
    'lastdate' => $mysql_next_date,
    'active' => 1
];
$this->db->where('id', $record_id);
$this->db->update('lottery_combination_filters', $update_data);
```

### Result
- Settings are restored correctly ✅
- Combinations are displayed immediately ✅  
- Filter remains "Active" instead of expiring ✅
- Previously saved tickets are loaded and displayed ✅
