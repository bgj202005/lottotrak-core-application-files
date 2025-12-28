# Draw Numbering Fix - Statistics Pagination

## Issue Description
The AJAX pagination system had two critical problems with draw numbering:

1. **Wrong Range Used**: When requesting 300 draws, the system was using the total database count (4349) instead of the requested range (300) for numbering
2. **Off-by-One Error**: Draw numbers were starting at 199 instead of 200, and ending at 0 instead of 1

## Root Cause Analysis

### Problem 1: Incorrect Range Parameter
- **Issue**: The AJAX controller was passing `$total_count` (total draws in database) to `load_draws_paginated()` instead of the requested range
- **Result**: For 300 draws request, numbering started at 4349 instead of 300

### Problem 2: Missing Range Parameter in AJAX Call
- **Issue**: The JavaScript wasn't sending the requested range to the server
- **Result**: Server had no way to know the intended range (200, 300, etc.)

### Problem 3: Off-by-One in Draw Numbering
- **Issue**: MySQL variable assignment with `(@draw_number:=@draw_number - 1)` decrements BEFORE assignment
- **Result**: First draw got number 199 instead of 200, last draw got number 0 instead of 1

## Files Modified

### 1. `application/views/admin/dashboard/statistics/view_optimized.php`

**Added Range to JavaScript Class:**
```javascript
// BEFORE
this.lotteryId = <?= $lottery->id; ?>;
this.searchTerm = '';

// AFTER  
this.lotteryId = <?= $lottery->id; ?>;
this.requestedRange = <?= $range; ?>; // The requested range (200, 300, etc.)
this.searchTerm = '';
```

**Added Range to AJAX Request:**
```javascript
// BEFORE
new URLSearchParams({
    page: this.currentPage,
    limit: this.pageSize,
    trend: this.trend,
    search: this.searchTerm
});

// AFTER
new URLSearchParams({
    page: this.currentPage,
    limit: this.pageSize, 
    trend: this.trend,
    range: this.requestedRange,
    search: this.searchTerm
});
```

### 2. `application/controllers/admin/Statistics.php`

**Added Range Parameter Handling:**
```php
// BEFORE
$trend = (int)$this->input->get('trend', TRUE) ?: 0;
$search = $this->input->get('search', TRUE) ?: '';

// AFTER
$trend = (int)$this->input->get('trend', TRUE) ?: 0;
$requested_range = (int)$this->input->get('range', TRUE) ?: 100; // The requested range
$search = $this->input->get('search', TRUE) ?: '';
```

**Fixed Method Call:**
```php
// BEFORE - using total database count
$draws = $this->lotteries_m->load_draws_paginated($tbl_name, $limit, $offset, $trend, $total_count);

// AFTER - using requested range for numbering
$draws = $this->lotteries_m->load_draws_paginated($tbl_name, $limit, $offset, $trend, $requested_range);
```

### 3. `application/models/Lotteries_m.php`

**Fixed Parameter Name and Logic:**
```php
// BEFORE
public function load_draws_paginated($table, $limit = 100, $offset = 0, $trnd = 0, $total_range = null)
{
    $start_draw_number = $total_range - $offset;
    $this->db->query('SET @draw_number = '.$start_draw_number.'; ');

// AFTER
public function load_draws_paginated($table, $limit = 100, $offset = 0, $trnd = 0, $requested_range = null)
{
    // Add 1 because MySQL decrements BEFORE assigning the value
    $start_draw_number = $requested_range - $offset + 1;
    $this->db->query('SET @draw_number = '.$start_draw_number.'; ');
```

## Draw Numbering Logic Explanation

### For 300 Draws Request:

**Page 1 (offset=0, limit=50):**
- `start_draw_number = 300 - 0 + 1 = 301`
- MySQL assigns: 300, 299, 298, ..., 251 (50 draws)

**Page 2 (offset=50, limit=50):**
- `start_draw_number = 300 - 50 + 1 = 251` 
- MySQL assigns: 250, 249, 248, ..., 201 (50 draws)

**Page 6 (offset=250, limit=50):**
- `start_draw_number = 300 - 250 + 1 = 51`
- MySQL assigns: 50, 49, 48, ..., 1 (50 draws)

### Why +1 is Needed:
MySQL's `(@draw_number:=@draw_number - 1)` decrements the variable BEFORE assigning it to the column:
- Set `@draw_number = 301`
- First row gets: `301 - 1 = 300` ✓
- Second row gets: `300 - 1 = 299` ✓
- etc.

## Testing Results

### Before Fix:
- **200 draws**: Numbered 4349 to 4150 (wrong!)
- **300 draws**: Numbered 4349 to 4050 (wrong!)
- **Last draw**: Number 0 (should be 1)

### After Fix:
- **200 draws**: Numbered 200 to 1 ✓
- **300 draws**: Numbered 300 to 1 ✓  
- **First draw**: Number matches requested range ✓
- **Last draw**: Number 1 ✓

## Compatibility Notes

- **Backwards Compatible**: Existing non-AJAX views (under 200 draws) are unaffected
- **Performance**: No impact on query performance
- **Caching**: No cache invalidation needed

The fix ensures that draw numbering is consistent with user expectations and matches the behavior of the original non-paginated system.