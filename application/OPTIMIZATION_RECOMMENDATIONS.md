# Lottery Import and Recalc Optimization Recommendations

## Current State Analysis

### Import Process (https://localhost/lottotrak/admin/lotteries/import/1)
**Current bottlenecks:**
1. Full CSV file parsing with row-by-row filtering
2. Individual database inserts per draw
3. Sequential recalculation of all statistics after each draw
4. No batch processing for database operations
5. Memory inefficient for large CSV files

### Recalc Process
**Current approach:**
- Full recalculation of all draws each time
- No incremental updates
- H-W-C, Followers, and Friends all recalculate from scratch

---

## Optimization Strategy

### 1. IMPORT OPTIMIZATION (Ready to Implement)

#### A. CSV Pre-Processing Phase
```php
// Already partially implemented in import_process()
- Pre-scan CSV to identify only NEW draws
- Skip already imported records at scan time
- Build array of records to import before database operations
```

#### B. Batch Database Operations
**Implement batched inserts:**
```php
// Replace: Individual inserts per draw
// With: Batch inserts of 100-500 records at a time

$batch_size = 500;
$insert_batch = array();

foreach ($records_to_import as $record) {
    $insert_batch[] = $this->prepare_draw_data($record);
    
    if (count($insert_batch) >= $batch_size) {
        $this->db->insert_batch($table_name, $insert_batch);
        $insert_batch = array();
    }
}

// Insert remaining records
if (!empty($insert_batch)) {
    $this->db->insert_batch($table_name, $insert_batch);
}
```

#### C. Deferred Statistics Calculation
```php
// Don't calculate statistics during import
// Set flag to recalculate at end
$this->session->set_userdata('needs_recalc_' . $lottery_id, true);

// After import completion, offer one-click recalc
```

---

### 2. SLIDING WINDOW ALGORITHM FOR H-W-C

#### Implementation Strategy

**Concept:** When only ONE new draw is added, we don't need to recalculate ALL draws. We can:
1. Remove the oldest draw from the calculation
2. Add the newest draw to the calculation
3. Update the frequency counts

**Current:** O(n * m) where n = range, m = ball count  
**Optimized:** O(m) constant time for single draw updates

#### Code Implementation

```php
/**
 * Sliding window update for H-W-C statistics
 * Only recalculates when ONE new draw is added
 * 
 * @param string $table_name Lottery table name
 * @param int $lottery_id Lottery ID
 * @param int $range Draw range (100, 200, etc.)
 * @param object $old_hwc Previous H-W-C data
 * @return array Updated H-W-C statistics
 */
public function hwc_sliding_window($table_name, $lottery_id, $balls_drawn, $extra_included, $extra_draws, $range, $w_start, $c_start, $duplicate_extra)
{
    // Get the oldest draw in current range
    $oldest_draw = $this->db->query("
        SELECT * FROM {$table_name}
        ORDER BY draw_date ASC
        LIMIT 1 OFFSET {$range}
    ")->row_array();
    
    // Get the newest draw
    $newest_draw = $this->db->query("
        SELECT * FROM {$table_name}
        ORDER BY draw_date DESC
        LIMIT 1
    ")->row_array();
    
    if (!$oldest_draw || !$newest_draw) {
        return array('success' => false);
    }
    
    // Load existing H-W-C data
    $existing = $this->h_w_c_exists($lottery_id);
    if (!$existing || empty($existing['hots'])) {
        return array('success' => false);
    }
    
    // Parse existing frequency counts
    $frequency = array();
    $this->parse_hwc_frequencies($existing['hots'], $frequency);
    $this->parse_hwc_frequencies($existing['warms'], $frequency);
    $this->parse_hwc_frequencies($existing['colds'], $frequency);
    
    // Subtract oldest draw balls
    for ($i = 1; $i <= $balls_drawn; $i++) {
        $ball = $oldest_draw['ball' . $i];
        if (isset($frequency[$ball])) {
            $frequency[$ball]--;
            if ($frequency[$ball] < 0) $frequency[$ball] = 0;
        }
    }
    
    // Add newest draw balls
    for ($i = 1; $i <= $balls_drawn; $i++) {
        $ball = $newest_draw['ball' . $i];
        if (!isset($frequency[$ball])) {
            $frequency[$ball] = 0;
        }
        $frequency[$ball]++;
    }
    
    // Re-classify into hots, warms, colds based on frequencies
    arsort($frequency); // Sort by frequency descending
    
    $hots = array();
    $warms = array();
    $colds = array();
    $count = 0;
    
    foreach ($frequency as $ball => $freq) {
        if ($count < ($w_start - 1)) {
            $hots[] = $ball . ',' . $freq;
        } elseif ($count < $c_start) {
            $warms[] = $ball . ',' . $freq;
        } else {
            $colds[] = $ball . ',' . $freq;
        }
        $count++;
    }
    
    return array(
        'success' => true,
        'hots' => implode('<', $hots),
        'warms' => implode('<', $warms),
        'colds' => implode('<', $colds),
        'method' => 'sliding_window'
    );
}

/**
 * Helper to parse H-W-C frequency strings
 */
private function parse_hwc_frequencies($hwc_string, &$frequency)
{
    if (empty($hwc_string)) return;
    
    $balls = explode('<', $hwc_string);
    foreach ($balls as $ball_data) {
        if (empty($ball_data)) continue;
        $parts = explode(',', $ball_data);
        if (count($parts) >= 2) {
            $ball = (int)$parts[0];
            $count = (int)$parts[1];
            $frequency[$ball] = $count;
        }
    }
}
```

---

### 3. SLIDING WINDOW ALGORITHM FOR FOLLOWERS

#### Implementation Strategy

**Concept:** Track which numbers followed each ball in the previous draw
- Maintain a rolling window of follower relationships
- When adding new draw: increment followers for that draw
- When removing old draw: decrement followers for that draw

**Current:** O(n²) where n = range  
**Optimized:** O(n) linear time

#### Code Implementation

```php
/**
 * Sliding window update for Followers statistics
 * 
 * @param string $table_name Lottery table name
 * @param int $lottery_id Lottery ID  
 * @param int $range Draw range
 * @param array $existing_data Previous followers data
 * @return array Updated followers statistics
 */
public function followers_sliding_window($table_name, $lottery_id, $balls_drawn, $range, $existing_data)
{
    // Parse existing followers data into matrix
    $follower_matrix = $this->parse_followers_string($existing_data['lottery_followers']);
    
    // Get the oldest two consecutive draws to remove
    $oldest_draws = $this->db->query("
        SELECT * FROM {$table_name}
        ORDER BY draw_date ASC
        LIMIT 2 OFFSET " . ($range - 1)
    )->result_array();
    
    // Get the newest two consecutive draws to add
    $newest_draws = $this->db->query("
        SELECT * FROM {$table_name}
        ORDER BY draw_date DESC
        LIMIT 2
    ")->result_array();
    
    if (count($oldest_draws) < 2 || count($newest_draws) < 2) {
        return array('success' => false);
    }
    
    // Remove oldest follower relationships
    for ($i = 1; $i <= $balls_drawn; $i++) {
        $current_ball = $oldest_draws[0]['ball' . $i];
        for ($j = 1; $j <= $balls_drawn; $j++) {
            $next_ball = $oldest_draws[1]['ball' . $j];
            if (isset($follower_matrix[$current_ball][$next_ball])) {
                $follower_matrix[$current_ball][$next_ball]--;
                if ($follower_matrix[$current_ball][$next_ball] < 0) {
                    $follower_matrix[$current_ball][$next_ball] = 0;
                }
            }
        }
    }
    
    // Add newest follower relationships  
    for ($i = 1; $i <= $balls_drawn; $i++) {
        $current_ball = $newest_draws[1]['ball' . $i]; // Second newest (current)
        for ($j = 1; $j <= $balls_drawn; $j++) {
            $next_ball = $newest_draws[0]['ball' . $j]; // Newest (next)
            if (!isset($follower_matrix[$current_ball])) {
                $follower_matrix[$current_ball] = array();
            }
            if (!isset($follower_matrix[$current_ball][$next_ball])) {
                $follower_matrix[$current_ball][$next_ball] = 0;
            }
            $follower_matrix[$current_ball][$next_ball]++;
        }
    }
    
    // Convert matrix back to string format
    $followers_string = $this->build_followers_string($follower_matrix);
    
    return array(
        'success' => true,
        'lottery_followers' => $followers_string,
        'method' => 'sliding_window'
    );
}

/**
 * Parse followers string into matrix for manipulation
 */
private function parse_followers_string($followers_string)
{
    $matrix = array();
    if (empty($followers_string)) return $matrix;
    
    $balls = explode('<', $followers_string);
    foreach ($balls as $ball_data) {
        if (empty($ball_data)) continue;
        
        $parts = explode('=', $ball_data);
        if (count($parts) < 2) continue;
        
        $ball = (int)$parts[0];
        $followers_data = $parts[1];
        
        $matrix[$ball] = array();
        $followers = explode(',', $followers_data);
        
        foreach ($followers as $follower_pair) {
            if (empty($follower_pair)) continue;
            $pair = explode(':', $follower_pair);
            if (count($pair) == 2) {
                $follower_ball = (int)$pair[0];
                $count = (int)$pair[1];
                $matrix[$ball][$follower_ball] = $count;
            }
        }
    }
    
    return $matrix;
}

/**
 * Build followers string from matrix
 */
private function build_followers_string($matrix)
{
    $ball_strings = array();
    
    foreach ($matrix as $ball => $followers) {
        $follower_pairs = array();
        foreach ($followers as $follower_ball => $count) {
            if ($count > 0) {
                $follower_pairs[] = $follower_ball . ':' . $count;
            }
        }
        if (!empty($follower_pairs)) {
            $ball_strings[] = $ball . '=' . implode(',', $follower_pairs);
        }
    }
    
    return implode('<', $ball_strings);
}
```

---

### 4. SLIDING WINDOW ALGORITHM FOR FRIENDS

#### Implementation Strategy

**Concept:** Friends are balls that appear together in the same draw
- Maintain co-occurrence matrix
- When adding draw: increment all pairs in that draw
- When removing draw: decrement all pairs in that draw

**Current:** O(n * m²) where n = range, m = balls per draw  
**Optimized:** O(m²) constant time per draw update

#### Code Implementation

```php
/**
 * Sliding window update for Friends statistics
 * 
 * @param string $table_name Lottery table name
 * @param int $lottery_id Lottery ID
 * @param int $balls_drawn Number of balls drawn
 * @param int $range Draw range
 * @param array $existing_data Previous friends data
 * @return array Updated friends statistics
 */
public function friends_sliding_window($table_name, $lottery_id, $balls_drawn, $range, $existing_data)
{
    // Parse existing friends data into co-occurrence matrix
    $cooccurrence_matrix = $this->parse_friends_string($existing_data['lottery_friends']);
    
    // Get the oldest draw to remove
    $oldest_draw = $this->db->query("
        SELECT * FROM {$table_name}
        ORDER BY draw_date ASC
        LIMIT 1 OFFSET {$range}
    ")->row_array();
    
    // Get the newest draw to add
    $newest_draw = $this->db->query("
        SELECT * FROM {$table_name}
        ORDER BY draw_date DESC
        LIMIT 1
    ")->row_array();
    
    if (!$oldest_draw || !$newest_draw) {
        return array('success' => false);
    }
    
    // Remove oldest draw's co-occurrences
    $old_balls = array();
    for ($i = 1; $i <= $balls_drawn; $i++) {
        $old_balls[] = $oldest_draw['ball' . $i];
    }
    
    // Decrement all pairs from oldest draw
    for ($i = 0; $i < count($old_balls); $i++) {
        for ($j = $i + 1; $j < count($old_balls); $j++) {
            $ball1 = min($old_balls[$i], $old_balls[$j]);
            $ball2 = max($old_balls[$i], $old_balls[$j]);
            
            if (isset($cooccurrence_matrix[$ball1][$ball2])) {
                $cooccurrence_matrix[$ball1][$ball2]--;
                if ($cooccurrence_matrix[$ball1][$ball2] < 0) {
                    $cooccurrence_matrix[$ball1][$ball2] = 0;
                }
            }
        }
    }
    
    // Add newest draw's co-occurrences
    $new_balls = array();
    for ($i = 1; $i <= $balls_drawn; $i++) {
        $new_balls[] = $newest_draw['ball' . $i];
    }
    
    // Increment all pairs from newest draw
    for ($i = 0; $i < count($new_balls); $i++) {
        for ($j = $i + 1; $j < count($new_balls); $j++) {
            $ball1 = min($new_balls[$i], $new_balls[$j]);
            $ball2 = max($new_balls[$i], $new_balls[$j]);
            
            if (!isset($cooccurrence_matrix[$ball1])) {
                $cooccurrence_matrix[$ball1] = array();
            }
            if (!isset($cooccurrence_matrix[$ball1][$ball2])) {
                $cooccurrence_matrix[$ball1][$ball2] = 0;
            }
            $cooccurrence_matrix[$ball1][$ball2]++;
        }
    }
    
    // Convert matrix back to string format
    $friends_string = $this->build_friends_string($cooccurrence_matrix);
    
    return array(
        'success' => true,
        'lottery_friends' => $friends_string,
        'method' => 'sliding_window'
    );
}

/**
 * Parse friends string into co-occurrence matrix
 */
private function parse_friends_string($friends_string)
{
    $matrix = array();
    if (empty($friends_string)) return $matrix;
    
    $ball_groups = explode('<', $friends_string);
    foreach ($ball_groups as $group) {
        if (empty($group)) continue;
        
        $parts = explode('=', $group);
        if (count($parts) < 2) continue;
        
        $ball1 = (int)$parts[0];
        $friends_data = $parts[1];
        
        $matrix[$ball1] = array();
        $friends = explode(',', $friends_data);
        
        foreach ($friends as $friend_pair) {
            if (empty($friend_pair)) continue;
            $pair = explode(':', $friend_pair);
            if (count($pair) == 2) {
                $ball2 = (int)$pair[0];
                $count = (int)$pair[1];
                $matrix[$ball1][$ball2] = $count;
            }
        }
    }
    
    return $matrix;
}

/**
 * Build friends string from co-occurrence matrix
 */
private function build_friends_string($matrix)
{
    $ball_strings = array();
    
    foreach ($matrix as $ball1 => $friends) {
        $friend_pairs = array();
        foreach ($friends as $ball2 => $count) {
            if ($count > 0) {
                $friend_pairs[] = $ball2 . ':' . $count;
            }
        }
        if (!empty($friend_pairs)) {
            $ball_strings[] = $ball1 . '=' . implode(',', $friend_pairs);
        }
    }
    
    return implode('<', $ball_strings);
}
```

---

## Integration into Existing Code

### Modify Statistics_m.php Model

Add these methods to the Statistics_m model file.

### Modify Statistics.php Controller

Update the recalc methods to check if sliding window can be used:

```php
public function recalc_hwc($id, $lotto)
{
    // ... existing code ...
    
    // Try sliding window optimization if existing data is present
    $use_sliding_window = false;
    if(!is_null($h_w_c) && !empty($h_w_c['hots']) && $h_w_c['draw_id'] > 0) {
        // Check if we can use sliding window (same settings, only one new draw)
        $can_slide = (
            $h_w_c['extra_included'] == $lotto->extra_included &&
            $h_w_c['extra_draws'] == $lotto->extra_draws &&
            $h_w_c['draw_id'] == ($lotto->last_drawn['id'] - 1)  // Exactly one draw behind
        );
        
        if ($can_slide) {
            $slide_result = $this->statistics_m->hwc_sliding_window(
                $tbl, $id, $drawn, $h_w_c['extra_included'], 
                $h_w_c['extra_draws'], $new_range, $w_start, 
                $c_start, $blnduplicate
            );
            
            if ($slide_result['success']) {
                $strhots = $slide_result['hots'];
                $strwarms = $slide_result['warms'];
                $strcolds = $slide_result['colds'];
                $use_sliding_window = true;
            }
        }
    }
    
    // Full recalculation if sliding window wasn't used
    if (!$use_sliding_window) {
        // ... existing full calculation code ...
    }
}
```

---

## Expected Performance Improvements

### Import Process
- **Before:** 5-10 minutes for 1000 draws
- **After:** 30-60 seconds for 1000 draws
- **Improvement:** 5-10x faster

### H-W-C Recalc
- **Before:** O(n * m) = 100 draws × 49 balls = 4,900 operations
- **After:** O(m) = 49 operations
- **Improvement:** 100x faster for single draw updates

### Followers Recalc
- **Before:** O(n²) = 100² = 10,000 operations
- **After:** O(n) = 100 operations  
- **Improvement:** 100x faster

### Friends Recalc
- **Before:** O(n * m²) = 100 × 6² = 3,600 operations
- **After:** O(m²) = 36 operations
- **Improvement:** 100x faster

---

## Implementation Priority

1. **HIGH PRIORITY: Import Batch Processing**
   - Immediate 5-10x speedup
   - Low risk, high reward

2. **HIGH PRIORITY: H-W-C Sliding Window**
   - Most frequently used calculation
   - 100x speedup for daily updates

3. **MEDIUM PRIORITY: Followers Sliding Window**
   - Complex but significant gains
   - Requires careful testing

4. **MEDIUM PRIORITY: Friends Sliding Window**
   - Similar to Followers
   - Can reuse parsing logic

---

## Testing Strategy

1. **Unit Tests:** Test sliding window algorithms with known inputs
2. **Comparison Tests:** Run both old and new methods, verify identical results
3. **Performance Tests:** Measure execution time improvements
4. **Edge Cases:** Test with various draw ranges, settings changes

---

## Rollback Plan

All sliding window optimizations include success/failure returns. If optimization fails, system automatically falls back to full recalculation, ensuring data integrity.
