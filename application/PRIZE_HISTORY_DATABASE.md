# Prize History Database Structure

This document outlines the existing database structure used by the Prize History feature.

## Existing Database Tables

The Prize History feature utilizes the following existing tables in the database:

### 1. lottery_profiles
This existing table stores lottery information.

**Key Fields Used:**
- `id` - Primary key, used as foreign key in lottery_combination_files
- `lottery_name` - Name of the lottery (e.g., "Lotto Max")

### 2. lottery_combination_files
This existing table stores information about generated combination files.

**Key Fields Used:**
- `id` - Primary key (combo_id)
- `lottery_id` - Foreign key to lottery_profiles table
- `file_name` - Original combination table filename
- `N` - Number of original picks
- `R` - Original number of combination tickets
- `CCCC` - Actual filtered combinations (after filtering)
- `active` - Status indicator (1 = active, 0 = expired)
- `lastdate` - Next draw date for the combination table

### 3. lottery_prize_profiles
This existing table defines prize categories for each lottery.

**Key Fields Used:**
- `id` - Primary key
- `lottery_id` - Foreign key to lottery_profiles table
- `extra` - Extra/bonus ball prize category (1 = has prize, 0 = no prize)
- `1_win` - 1 number match prize category (1 = has prize, 0 = no prize)
- `1_win_extra` - 1 number + extra ball prize category
- `2_win` - 2 numbers match prize category
- `2_win_extra` - 2 numbers + extra ball prize category
- `3_win` - 3 numbers match prize category
- `3_win_extra` - 3 numbers + extra ball prize category
- `4_win` - 4 numbers match prize category
- `4_win_extra` - 4 numbers + extra ball prize category
- `5_win` - 5 numbers match prize category
- `5_win_extra` - 5 numbers + extra ball prize category
- `6_win` - 6 numbers match prize category
- `6_win_extra` - 6 numbers + extra ball prize category
- `7_win` - 7 numbers match prize category
- `7_win_extra` - 7 numbers + extra ball prize category
- `8_win` - 8 numbers match prize category
- `8_win_extra` - 8 numbers + extra ball prize category
- `9_win` - 9 numbers match prize category

### 4. lottery_combination_filters
This existing table contains the actual filtered combinations and win records.

**Key Fields Used:**
- `id` - Primary key
- `lottery_id` - Foreign key to lottery_profiles table
- Combination data and win matching results are stored here

## Database Field Mapping

The Prize History table columns map to the existing database fields as follows:

| Table Column | Database Field | Source Table | Description |
|--------------|----------------|--------------|-------------|
| ## | - | Calculated | Row number (auto-generated) |
| Original | `file_name` | lottery_combination_files | Original combination table filename |
| Lotto | `lottery_name` | lottery_profiles | Retrieved via lottery_id foreign key |
| N | `N` | lottery_combination_files | Number of original picks |
| R | `R` | lottery_combination_files | Original combination tickets |
| Saved | `file_name` + ADMINXX | lottery_combination_files | Generated filename with admin pattern |
| R (Actual) | `CCCC` | lottery_combination_files | Actual filtered combinations |
| Active | `active` | lottery_combination_files | 1 = YES (active), 0 = EXPIRED |
| Next Date | `lastdate` | lottery_combination_files | Next draw date |
| Win Records | Calculated | lottery_combination_files | Win counts by match level |

## Data Retrieval Logic

### Administrator Filtering
The system filters records by looking for 'ADMIN' + user_id pattern in the filename field.

### Active Status Display
- `active = 1` displays as "YES" with green styling
- `active = 0` displays as "EXPIRED" with red styling

### Lottery Name Lookup
Lottery names are retrieved by joining:
```sql
lottery_combination_files.lottery_id = lottery_profiles.id
```

## Win Record Calculation Logic

### Active Status and Draw Date Logic
1. **Active Status Determination:**
   - If `active = 1`: Record is active, proceed with win calculation
   - If `active = 0`: Record is expired, display as "EXPIRED"

2. **Draw Date Processing:**
   - Use `lastdate` field as the lottery draw date
   - Retrieve actual drawn numbers for that specific date from lottery system
   - Compare filtered combinations against drawn numbers

### Win Calculation Process

The system calculates win records by following this iterative process:

1. **For Each Active Combination File:**
   - Retrieve all filtered combinations from `lottery_combination_filters` table
   - Get the prize profile from `lottery_prize_profiles` using `lottery_id`
   - Retrieve drawn numbers for the `lastdate`

2. **For Each Filtered Combination:**
   - Compare combination numbers against drawn numbers
   - Count exact number matches
   - Check if extra/bonus ball matches (if applicable)
   - Determine prize category based on matches

3. **Prize Category Matching (Highest to Lowest):**
   - Start with highest prize category (e.g., 9_win_extra, 9_win)
   - Check if combination qualifies for that category
   - If match found, increment that category counter (+1)
   - If no match, check next lower category
   - Continue until all categories checked

4. **Prize Category Hierarchy Example:**
   ```
   9_win_extra → 9_win → 8_win_extra → 8_win → 7_win_extra → 7_win
   → 6_win_extra → 6_win → 5_win_extra → 5_win → 4_win_extra → 4_win
   → 3_win_extra → 3_win → 2_win_extra → 2_win → 1_win_extra → 1_win
   ```

5. **Multiple Draw Dates:**
   - Check if there are additional draw dates after `lastdate`
   - If more draws exist, repeat the process for each date
   - Accumulate win counts across all draw dates
   - If no further draws exist, set `active = 0` (expired)

### Display Mapping

The calculated win counts map to display columns as follows:

| Display Column | Prize Categories Checked | Description |
|---------------|-------------------------|-------------|
| 7+ | 7_win_extra | 7 numbers + extra ball |
| 7 | 7_win | 7 numbers exact |
| 6+ | 6_win_extra | 6 numbers + extra ball |
| 6 | 6_win | 6 numbers exact |
| 5+ | 5_win_extra | 5 numbers + extra ball |
| 5 | 5_win | 5 numbers exact |
| 4+ | 4_win_extra | 4 numbers + extra ball |
| 4 | 4_win | 4 numbers exact |
| 3+ | 3_win_extra | 3 numbers + extra ball |
| 3 | 3_win | 3 numbers exact |

**Note:** Only prize categories where the corresponding field = 1 in `lottery_prize_profiles` are checked and displayed.

## Key Points

1. **Admin Filtering**: The system filters records by looking for 'ADMIN' + user_id pattern in the `file_name` field.

2. **Active Status**: Records are marked as 'YES' or 'EXPIRED' based on the `active` field (1 = active, 0 = expired).

3. **Win Records**: The win record columns are calculated by comparing filtered combinations against actual drawn numbers for the lottery draw date(s).

4. **Prize Categories**: Only prize categories marked as 1 in `lottery_prize_profiles` are checked and counted.

5. **Multiple Draws**: The system processes all draw dates from `lastdate` onwards until no more draws exist.

6. **Auto-Expiration**: When no future draw dates exist, the system automatically sets `active = 0`.

7. **Pagination**: The system supports configurable pagination (10, 20, 50, 100, 200, 300, 500, 1000 records per page).

## Technical Implementation Notes

- **Foreign Key Relationships:**
  - `lottery_combination_files.lottery_id` → `lottery_profiles.id`
  - `lottery_prize_profiles.lottery_id` → `lottery_profiles.id`
  - `lottery_combination_filters.lottery_id` → `lottery_profiles.id`

- **Win Calculation Performance:**
  - Win calculations are performed real-time when viewing the Prize History
  - Results can be cached for better performance on large datasets
  - Active status is updated during the calculation process
