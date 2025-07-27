# Detailed Combination Breakdown Implementation

## Overview
This implementation replaces the default lottery combination statistics with a detailed breakdown system that shows the exact number of winning tickets for different match scenarios using combinatorial mathematics (nCr formulas).

## Changes Made

### 1. Enhanced Model Methods (Predictions_m.php)
- `calculate_detailed_breakdown()` - Main method that calculates the full breakdown
- `calculate_scenario_tickets()` - Calculates tickets for specific match scenarios
- `calculate_total_scenario_tickets()` - Calculates total tickets per scenario
- `combination()` - Implements nCr combinatorial formula (made public)

### 2. Modified Controller Method (Predictions.php)
- `combo_statistics()` - **REPLACED** to use detailed breakdown instead of simple statistics
- `combo_breakdown()` - Legacy method that redirects to combo_statistics
- Now accessible via: `/admin/predictions/combo_statistics/{lottery_id}/{file_name}`

### 3. Enhanced Views
- `combo_breakdown.php` - Main view for detailed breakdown display
- `combo_results.php` - Legacy view (kept for compatibility)
- The detailed breakdown is now the **DEFAULT** view for combination statistics

### 4. Key Changes from Original Implementation
- **REPLACED** the simple percentage/probability statistics with detailed mathematical breakdown
- **REMOVED** the "View Detailed Breakdown" button (no longer needed)
- **UNIFIED** the statistics display - detailed breakdown is now the primary view

## Mathematical Approach

### Total Tickets Formula
For a system where you pick N numbers and play all combinations of R:
```
Total Tickets = C(N, R) = N! / (R! × (N-R)!)
```

### Scenario Calculation
For each scenario where exactly X of your picked numbers match the drawn numbers:
```
Tickets with M matches = C(X, M) × C(N-X, R-M)
```

Where:
- X = Numbers picked correctly
- M = Required matches for prize
- N = Total numbers picked
- R = Numbers drawn in lottery

## Example: Pick 6 from 8 Numbers (060828.txt)
- **Pick per ticket:** 6 numbers
- **Numbers to pick:** 8 numbers  
- **Total tickets:** C(8,6) = 28

### Breakdown Table:
| Picked Correctly | 6 Matches | 5 Matches | 4 Matches | 3 Matches | 2 Matches | Non-Winners | Scenario % |
|------------------|-----------|-----------|-----------|-----------|-----------|-------------|------------|
| 6                | 1         | 12        | 15        | 0         | 0         | 0           | 3.57%      |
| 5                | 0         | 3         | 15        | 10        | 0         | 0           | 17.86%     |
| 4                | 0         | 0         | 6         | 16        | 6         | 0           | 32.14%     |
| 3                | 0         | 0         | 0         | 10        | 15        | 3           | 35.71%     |
| 2                | 0         | 0         | 0         | 0         | 15        | 13          | 10.71%     |

## Usage
1. Navigate to Predictions > Combo Statistics for any lottery
2. Select a combination file  
3. View the detailed mathematical breakdown (now the default view)
4. Analyze winning scenarios and probabilities

## Files Modified
- `/models/Predictions_m.php` - Added mathematical calculation methods, made combination() public
- `/controllers/admin/Predictions.php` - **REPLACED** combo_statistics method with detailed breakdown
- `/views/admin/dashboard/predictions/combo_breakdown.php` - Main statistics view
- `/views/admin/dashboard/predictions/combo_results.php` - Removed breakdown button (legacy view)

## Benefits
- **Default detailed analysis** - No need for separate breakdown view
- **Mathematical precision** - Exact calculations using combinatorial formulas
- **Complete probability distribution** - Shows all possible winning scenarios
- **Educational value** - Helps understand lottery mathematics
- **Simplified navigation** - One unified statistics view
