# Prize History - Outdated Combination Files Auto-Expiration

## Overview
Implemented automatic detection and expiration of outdated combination files when newer lottery draws are imported that exceed the predicted next draw dates.

## Problem Statement
Users reported that combination files remained active even when new lottery draws were imported that were newer than the dates the combinations were predicted for. This could lead to incorrect win record calculations and confusion about which combinations are still valid for future draws.

## Solution Implemented

### 1. Enhanced Auto-Update Prize Records Method
**Location:** `controllers/admin/Prize.php` - `auto_update_prize_records()` method

**Changes:**
- Previously disabled method now calls `expire_outdated_combination_files()`
- Maintains existing behavior of not updating win records until explicitly requested
- Adds automated outdated combination detection
- Stores alert messages in session for user notification

### 2. New Outdated Combination Detection Method
**Location:** `controllers/admin/Prize.php` - `expire_outdated_combination_files()` method

**Functionality:**
- Retrieves all active combination filters for specified lottery (or all lotteries)
- For each active filter:
  1. Calculates the expected next draw date based on filter's `lastdate`
  2. Gets the most recent draw date from the lottery table
  3. Compares dates to detect if imported draws are newer than expected
  4. Expires (sets `active = 0`) combination files that are outdated
- Returns count of expired combination files
- Comprehensive error handling and logging

**Logic:**
```
If (most_recent_draw_date > expected_next_draw_date):
    Set combination_filter.active = 0
    Log expiration action
```

### 3. User Alert Display
**Location:** `views/admin/prize/index.php` - Prize History page

**Changes:**
- Added alert message display section after page title
- Uses Bootstrap alert styling with warning color and dismiss button
- Displays count of expired combination files
- Alert automatically appears when combination files are expired

**Display Example:**
```
⚠️ Alert: Expired 3 outdated combination file(s) due to newer draws being imported.
```

## Technical Details

### Database Impact
- **Table:** `lottery_combination_filters`
- **Field Modified:** `active` (set to 0 for outdated combinations)
- **Query Type:** UPDATE operations only (no structure changes)

### Performance Considerations
- Method runs only when Prize History page is accessed
- Processes only active combination filters
- Single database query per lottery table
- Efficient date comparison using MySQL date format

### Error Handling
- Comprehensive try-catch blocks for each filter processing
- Detailed logging for troubleshooting
- Graceful handling of missing lottery profiles or tables
- Continues processing other filters if one fails

### Date Calculation Logic
- Uses existing `lotteries_m->next_date()` method for consistency
- Leverages existing `convert_to_mysql_date()` method for date formatting
- Ensures accurate date comparisons using MySQL format

## Benefits

1. **Data Integrity:** Prevents use of outdated combination files for win calculations
2. **User Awareness:** Clear notification when combinations are expired
3. **Automatic Maintenance:** No manual intervention required
4. **Performance Optimized:** Runs only when needed, not on every page load
5. **Backward Compatible:** Doesn't affect existing win record calculation logic

## Usage

### Automatic Operation
- Runs automatically when accessing Prize History page (`admin/prize`)
- Checks for outdated combinations based on latest imported draws
- Expires outdated combinations and displays alert message

### Manual Testing
1. Access Prize History page for any lottery
2. System automatically checks for outdated combinations
3. If any are found and expired, alert message displays
4. Check application logs for detailed expiration information

## Logging
All operations are logged with details including:
- Filter IDs being processed
- Date comparisons performed
- Combination files expired
- Error conditions encountered

**Log Location:** CodeIgniter application logs
**Log Level:** INFO for normal operations, ERROR for issues

## Future Enhancements
- Could be extended to run on a scheduled basis
- Could add email notifications for expired combinations
- Could provide restoration option for accidentally expired combinations
- Could add batch processing for large numbers of combinations

## Testing Notes
- Implementation preserves all existing functionality
- No changes to win record calculation logic
- Safe to deploy as it only adds new functionality
- Error handling ensures system stability even with data issues
