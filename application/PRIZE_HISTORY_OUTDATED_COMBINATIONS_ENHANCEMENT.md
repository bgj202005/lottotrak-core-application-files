# Prize History - Outdated Combinations Enhancement

## Overview
Enhanced the Prize History system to automatically detect and expire outdated combination files when newer lottery draws have been imported, preventing incorrect predictions and win calculations.

## Key Features Implemented

### 1. Automatic Expiration Detection
- **Only checks ACTIVE combination files** - Already EXPIRED files are skipped for efficiency
- **Smart date comparison** - Compares most recent imported draw dates with expected next draw dates from combination filters
- **Preserves data integrity** - Only sets `active = 0`, keeps all historical data intact

### 2. User-Friendly Alert System
- **Specific filename alerts** - Shows exactly which files were expired
- **Example format**: "Combination Ticket Filenames 06155005 and 06143003 Status changed from ACTIVE to EXPIRED because the draw is out of date."
- **Bootstrap alert styling** - Warning alert with dismiss button

### 3. Enhanced Combination Ticket Winners Display
- **Expected draw date display** - Shows the next draw date that combinations were predicted for
- **Out of date indication** - Red background alert when draw is expired
- **Smart status detection** - Automatically detects when combinations are no longer valid

### 4. Check Results Enhancement
- **TBD replacement** - Changes "TBD (To Be Determined)" to "Draw is Out of Date" for expired combinations
- **Visual emphasis** - Red background with white text for out-of-date status
- **Consistent messaging** - Uniform "Draw is Out of Date" across all display areas

## Technical Implementation

### Modified Files

#### 1. `controllers/admin/Prize.php`
- **Enhanced `auto_update_prize_records()`** - Now calls expiration checking with detailed alerts
- **New `expire_outdated_combination_files()`** - Core logic for detecting and expiring outdated combinations
- **Returns detailed information** - Count and specific filenames of expired combinations

#### 2. `views/admin/prize/index.php`
- **Added alert display section** - Bootstrap warning alert for expired combinations
- **Session flash message integration** - Shows alerts from controller processing

#### 3. `views/admin/prize/combination_tickets.php`
- **Enhanced draw date display** - Shows expected next draw date for all combinations
- **Updated TBD logic** - Conditionally shows "Draw is Out of Date" vs "TBD"
- **Check Results enhancement** - Red background for expired combination results

### Key Logic Flow

1. **Page Access Trigger** - When Prize History page is accessed
2. **ACTIVE Filter Check** - Only processes combination files with `active = 1`
3. **Date Calculation** - Calculates expected next draw date from filter `lastdate`
4. **Latest Draw Query** - Gets most recent draw from lottery table
5. **Comparison Logic** - If latest draw date > expected next draw date, expire the combination
6. **Bulk Update** - Sets `active = 0` for all outdated combinations
7. **User Notification** - Creates alert with specific filenames
8. **Display Enhancement** - Shows "Draw is Out of Date" instead of "TBD"

## Benefits

- **Data Accuracy** - Prevents incorrect win calculations from outdated predictions
- **User Awareness** - Clear notification when combinations become invalid
- **Automatic Maintenance** - No manual intervention required
- **Performance Optimized** - Only runs when Prize History is accessed
- **Backwards Compatible** - Existing functionality preserved

## Example Scenarios

### Scenario 1: Normal Active Combination
- **Filter Created**: Saturday, July 12, 2025
- **Expected Next Draw**: Wednesday, July 16, 2025
- **Latest Import**: Saturday, July 12, 2025
- **Result**: Remains ACTIVE, shows "TBD (To Be Determined)"

### Scenario 2: Outdated Combination
- **Filter Created**: Saturday, July 12, 2025
- **Expected Next Draw**: Wednesday, July 16, 2025
- **Latest Import**: Tuesday, August 8, 2025 (7 draws later)
- **Result**: Changed to EXPIRED, shows "Draw is Out of Date"

### Alert Message Example
```
Combination Ticket Filenames 06155005 and 06143003 Status changed from ACTIVE to EXPIRED because the draw is out of date.
```

## Validation Rules

- **Only ACTIVE combinations** are checked for expiration
- **Date parsing validation** ensures reliable date comparisons
- **Table existence checks** prevent database errors
- **Exception handling** logs errors without breaking functionality
- **Valid draws only** - Only considers draws with `extra > 0`

## Future Enhancements

- **Email notifications** for expired combinations
- **Batch expiration reports** for administrative review
- **Configurable grace periods** before expiration
- **Re-activation workflows** for combinations that become valid again

---

*Implementation completed: August 12, 2025*
*Files modified: 3*
*New features: 4*
*Backwards compatibility: Maintained*
