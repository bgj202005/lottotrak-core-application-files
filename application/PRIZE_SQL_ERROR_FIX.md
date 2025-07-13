# Prize History SQL Error Fix

## Problem
Multiple SQL errors were occurring in Prize History page:

### Error 1: Invalid table name
```
Error Number: 1064
You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '14 WHERE `draw_date` >= '2025-07-11'' at line 2

SELECT COUNT(*) as count FROM 14 WHERE `draw_date` >= '2025-07-11'
```

### Error 2: Unknown column 'picks'
```
Error Number: 1054
Unknown column 'picks' in 'field list'
SELECT `picks`, `bonus_ball` FROM `lottery_profiles` WHERE `id` = '14'
```

## Root Causes

### Issue 1: Wrong table name generation
The code was incorrectly passing a lottery ID (number) to the `lotto_table_convert()` method instead of a lottery name (string).

### Issue 2: Wrong table for picks data
The code was trying to get `picks` from `lottery_profiles` table, but this field doesn't exist there. The picks data is actually stored in `lottery_combination_files.N` field.

## Solutions Applied

### Fix 1: Table Name Generation
**Before (Broken):**
```php
$table_name = $this->lotteries_m->lotto_table_convert($record->lottery_id); // Returns "14"
```

**After (Fixed):**
```php
// Get lottery name first
$this->db->select('lottery_name');
$this->db->from('lottery_profiles');
$this->db->where('id', $record->lottery_id);
$lottery_profile = $this->db->get()->row();

// Then convert name to table name
$table_name = $this->lotteries_m->lotto_table_convert($lottery_profile->lottery_name); // Returns "powerball"
```

### Fix 2: Picks Data Source
**Before (Broken):**
```php
// Wrong: lottery_profiles doesn't have 'picks' column
$this->db->select('picks, bonus_ball');
$this->db->from('lottery_profiles');
$lottery_info = $this->db->get()->row();
$expected_picks = intval($lottery_info->picks);
```

**After (Fixed):**
```php
// Correct: Use N field from lottery_combination_files via the record
$expected_picks = intval($record->N);  // N comes from main query join
```

## Technical Details

### Data Source Mapping:
- **picks/N**: `lottery_combination_files.N` (number of picks in lottery)
- **lottery_name**: `lottery_profiles.lottery_name` 
- **extra_ball**: `lottery_profiles.extra_ball`
- **bonus_ball**: `lottery_profiles.extra_ball` (same field, different name)

### Main Query Structure:
The Prize model already joins the correct tables:
```sql
SELECT lcf.*, lp.lottery_name, lcfiles.N, lcfiles.R, lcfiles.CCCC
FROM lottery_combination_filters lcf
LEFT JOIN lottery_profiles lp ON lp.id = lcf.lottery_id  
LEFT JOIN lottery_combination_files lcfiles ON lcfiles.id = lcf.combo_id
```

This provides all needed data in the record object without additional queries.

## Files Modified
- `/application/models/Prize_m.php`
  - Fixed `check_and_update_active_status()` method (table name issue)
  - Fixed `calculate_win_records()` method (table name issue)
  - Fixed `get_lottery_info()` method (picks column issue)
  - Fixed `get_combination_tickets()` method (picks source issue)
  - Fixed `get_combination_file_path()` method (picks source issue)
  - Updated `debug_lottery_table()` method (both issues)

## Prevention Measures
### For Table Names:
- Always get lottery_name from lottery_profiles first
- Then convert lottery_name to table_name using lotto_table_convert()
- Never pass numeric IDs to lotto_table_convert()

### For Picks Data:
- Use `lottery_combination_files.N` field for picks count
- Available via main query join as `$record->N`
- Don't query lottery_profiles for picks (field doesn't exist)

## Database Schema Notes
### lottery_profiles table contains:
- `lottery_name` (string)
- `extra_ball` (integer 0/1)
- `lottery_country_id` (integer)

### lottery_combination_files table contains:
- `N` (integer) - number of picks
- `R` (integer) - number of draws
- `CCCC` (integer) - combination count
- `file_name` (string)

## Testing Results
After fixes:
- No more "SELECT FROM 14" errors
- No more "Unknown column 'picks'" errors  
- Prize history loads correctly
- Proper table names generated for lottery queries
- File path construction works with correct pick counts

Both SQL errors are now resolved and the Prize History page functions properly.
