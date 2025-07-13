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
For a Pick 7 lottery with saved settings containing `R = 7` and filename `"07088"`:
- **Before**: Would look for `d:\wamp64\www\lottotrak\combinations\07088.txt` ❌
- **After**: Will look for `d:\wamp64\www\lottotrak\combinations\pick7\07088.txt` ✅

## Verification
The system now properly constructs file paths based on the R (pick number) value stored in the lottery_combination_filters table and looks in the appropriate pick subdirectory.

## Expected Behavior
When the refresh method is called with a valid record ID:
1. Retrieves saved settings from database 
2. Extracts the R value (pick number) 
3. Constructs correct file path: `combinations/pick{R}/{filename}.txt`
4. Successfully finds and processes the combination file
5. Generates tickets with restored filter settings

This should resolve the "Could not generate numbers with the restored settings" error.
