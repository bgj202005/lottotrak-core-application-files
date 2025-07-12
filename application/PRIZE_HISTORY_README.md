# Prize History Feature

## Overview
The Prize History feature displays all combination tables created by the logged-in administrator. This feature provides a comprehensive view of generated lottery combinations along with their win records and status.

## Files Created

### 1. Controller: `/application/controllers/admin/Prize.php`
- Handles the Prize History display logic
- Manages pagination and filtering by administrator ID
- Provides AJAX endpoint for dynamic table updates

### 2. Model: `/application/models/Prize_m.php`
- Manages database queries for prize history data
- Calculates win records from combination results
- Handles administrator-specific filtering (ADMINXX pattern)

### 3. View: `/application/views/admin/prize/index.php`
- Complete table display with responsive design
- Pagination controls with configurable page sizes
- Win record columns with dynamic headers
- Active/Expired status indicators

### 4. Styles: `/application/views/admin/prize/prize_history.css`
- Custom styling for the prize history table
- Responsive design for mobile devices
- Color-coded win records and status indicators

## Features

### Table Columns
1. **##** - Row number (01, 02, 03, etc.)
2. **Original** - Original combination table filename
3. **Lotto** - Lottery name (e.g., "Lotto Max")
4. **N** - Number of original picks
5. **R** - Original number of combination tickets
6. **Saved** - Generated filename with ADMINXX format
7. **R (Actual)** - Actual filtered combination tickets
8. **Active** - YES (green) or EXPIRED (red) status
9. **Next Date** - Next draw date for the combination table
10. **Win Record Columns** - Dynamic columns based on lottery type:
    - 7+, 7, 6+, 6, 5+, 5, 4+, 4, 3+, 3 (for 7-pick with bonus ball)

### Key Features
- **Administrator Filtering**: Only shows records for the logged-in administrator
- **Pagination**: Configurable page sizes (10, 20, 50, 100, 200, 300, 500, 1000)
- **Status Indicators**: Color-coded active/expired status
- **Win Records**: Displays actual win counts based on prize profiles
- **Responsive Design**: Works on desktop and mobile devices
- **AJAX Updates**: Dynamic table updates without page refresh

## Database Requirements

The feature expects the following database tables:

### lottery_combination_files
Stores combination file information including filenames, counts, and dates.

### lotteries
Contains lottery configuration including picks and bonus ball settings.

### lottery_prize_profiles
Stores prize information for each lottery type.

### lottery_winning_combinations
Records actual wins for combination files.

See `PRIZE_HISTORY_DATABASE.md` for complete table structures and sample data.

## Navigation Integration

The Prize History is accessible through:
- **Admin Menu**: Predictions → Prize History
- **URL**: `/admin/prize`
- **Breadcrumb**: Dashboard → Prize History

## Usage

1. **Access**: Administrator must be logged into the backend
2. **Filtering**: Automatically filters by logged-in administrator's ID
3. **Pagination**: Use dropdown to change records per page
4. **Sorting**: Default sort by creation date (newest first)
5. **Status**: Active combinations show "YES" (green), expired show "EXPIRED" (red)

## Technical Details

### Administrator Identification
- Files are filtered by pattern: `ADMINXX` where XX is the user ID
- Only records matching the logged-in administrator's ID are displayed

### Status Calculation
- **Active**: Next draw date is today or in the future
- **Expired**: Next draw date has passed

### Win Record Calculation
- Dynamically calculated from `lottery_winning_combinations` table
- Grouped by match count and bonus ball matches
- Supports different lottery configurations (picks + bonus ball)

## Error Handling
- Displays appropriate message if administrator is not logged in
- Shows empty state if no records found
- Handles database connection errors gracefully
- AJAX error handling for dynamic updates

## Performance Considerations
- Pagination limits database load
- Indexed database queries for efficient filtering
- AJAX loading for better user experience
- Responsive design for mobile optimization

## Future Enhancements
- Export functionality (CSV, PDF)
- Advanced filtering options
- Detailed win record drill-down
- Combination file download links
- Real-time status updates
