-- Investigate admin user settings retrieval issue
-- Issue: ADMIN01 (user_id=1) getting ADMIN03 (user_id=3) records due to missing user filtering

-- Check current state of lottery_combination_filters table
SELECT 'Current lottery_combination_filters records:' as info;
SELECT id, combo_id, user, user_id, h_w_c_group, selected_friends, parity, active, created_at 
FROM lottery_combination_filters 
WHERE combo_id LIKE '%072077520%' 
ORDER BY combo_id, user_id;

-- Check user_lottery_filters table for admin users
SELECT 'Current user_lottery_filters records:' as info;
SELECT id, user_id, lottery_id, hwc_range, selected_friends, parity, created_at 
FROM user_lottery_filters 
WHERE user_id IN (1, 3) AND lottery_id = 4
ORDER BY user_id, created_at DESC;

-- Test query to simulate what ADMIN01 should get
SELECT 'What ADMIN01 (user_id=1) should get:' as info;
SELECT id, combo_id, user, user_id, h_w_c_group, selected_friends, parity 
FROM lottery_combination_filters 
WHERE combo_id LIKE '%072077520%' 
  AND user = 1    -- Admin role
  AND user_id = 1 -- ADMIN01's user ID
ORDER BY id DESC 
LIMIT 1;

-- Test query to simulate what ADMIN03 should get  
SELECT 'What ADMIN03 (user_id=3) should get:' as info;
SELECT id, combo_id, user, user_id, h_w_c_group, selected_friends, parity 
FROM lottery_combination_filters 
WHERE combo_id LIKE '%072077520%' 
  AND user = 1    -- Admin role  
  AND user_id = 3 -- ADMIN03's user ID
ORDER BY id DESC 
LIMIT 1;