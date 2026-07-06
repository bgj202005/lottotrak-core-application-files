<link rel="stylesheet" href="<?php echo base_url('css/prize_history.css'); ?>">

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-list"></i> Combination Ticket Winners
        </h1>
        <h5 style="text-align:left">
            <?php 
            // Use dynamic back navigation based on referrer
            $back_link = isset($back_link) ? $back_link : 'admin/prize/index/'.$filter->lottery_id;
            $back_text = isset($back_text) ? $back_text : 'Back to Prize History';
            echo anchor($back_link, $back_text, 'title="' . $back_text . '"'); 
            ?>
        </h5>
    </section>

    <section class="content">
        <div class="col-xs-12">
            <!-- White Card -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Draw Information -->
                    <?php if($draw_info): ?>
                    <div class="draw-info-header">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="draw-header-box">
                                    <?php if(isset($display_mode) && $display_mode == 'tbd'): ?>
                                        <h4><strong>Next Draw Date:</strong> <?php echo date('l j F, Y', strtotime($next_draw_date)); ?></h4>
                                        <div class="drawn-numbers-section">
                                            <strong>Drawn Numbers:</strong>
                                            <div class="drawn-numbers-display">
                                                <?php if ($filter->active == 0): ?>
                                                    <span class="out-of-date-display" style="background-color: #ff0000; color: white; padding: 4px 8px; border-radius: 4px;">Draw is Out of Date</span>
                                                <?php else: ?>
                                                    <span class="tbd-display">TBD (To Be Determined)</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <h4><strong>Draw Date:</strong> <?php echo date('l j F, Y', strtotime($draw_info->draw_date)); ?></h4>
                                        <div class="drawn-numbers-section">
                                            <strong>Drawn Numbers:</strong>
                                            <div class="drawn-numbers-display">
                                                <?php 
                                                // Display drawn numbers
                                                for ($i = 1; $i <= $filter->N; $i++) {
                                                    $ball_field = 'ball' . $i;
                                                    if (property_exists($draw_info, $ball_field)) {
                                                        echo '<span class="drawn-number-highlight">' . sprintf('%02d', $draw_info->$ball_field) . '</span>';
                                                    }
                                                }
                                                
                                                // Display extra/bonus number if available
                                                if ($draw_info->extra_ball_included) {
                                                    $bonus_fields = array('extra', 'bonus', 'extra_ball', 'bonus_ball', 'bonus_number');
                                                    foreach ($bonus_fields as $field) {
                                                        if (property_exists($draw_info, $field) && !is_null($draw_info->$field)) {
                                                            echo ' <strong class="plus-sign">+</strong> <span class="bonus-number-highlight">' . sprintf('%02d', $draw_info->$field) . '</span>';
                                                            break;
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-2 text-right">
                                <div class="filter-status-badge">
                                    <?php if ($filter->active == 1): ?>
                                        <span class="status-active">ACTIVE</span>
                                    <?php else: ?>
                                        <span class="status-expired">EXPIRED</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php elseif(isset($display_mode) && $display_mode == 'tbd' && isset($next_draw_date)): ?>
                    <!-- TBD case when no draw_info but we have next_draw_date -->
                    <div class="draw-info-header">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="draw-header-box">
                                    <h4><strong>Next Draw Date:</strong> <?php echo date('l j F, Y', strtotime($next_draw_date)); ?></h4>
                                    <div class="drawn-numbers-section">
                                        <strong>Drawn Numbers:</strong>
                                        <div class="drawn-numbers-display">
                                            <?php if ($filter->active == 0): ?>
                                                <span class="out-of-date-display" style="background-color: #ff0000; color: white; padding: 4px 8px; border-radius: 4px;">Draw is Out of Date</span>
                                            <?php else: ?>
                                                <span class="tbd-display">TBD (To Be Determined)</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 text-right">
                                <div class="filter-status-badge">
                                    <?php if ($filter->active == 1): ?>
                                        <span class="status-active">ACTIVE</span>
                                    <?php else: ?>
                                        <span class="status-expired">EXPIRED</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- No draw information available -->
                    <div class="draw-info-header" style="background: #f8f9fa; border: 1px solid #dee2e6;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="draw-header-box">
                                    <h4 style="color: #6c757d;"><strong>No Draw Information Available</strong></h4>
                                    <p>Please check back later for draw results.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Table Information -->
                    <div class="table-info-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h4><strong>Saved Combination Table:</strong> <?php echo htmlspecialchars(preg_replace('/ADMIN.*/', '', $filter->file_name)); ?></h4>
                                <div class="table-stats">
                                    <span class="stat-item"><strong>Total Filtered:</strong> <?php echo number_format($total_tickets); ?></span>
                                    <span class="stat-separator">|</span>
                                    <span class="stat-item"><strong>Total Winners:</strong> <span id="total-winners"><?php 
                                    // Use the total winners calculated by the controller for the entire file
                                    echo number_format(isset($total_winners) ? $total_winners : 0);
                                    ?></span></span>
                                    <span class="stat-separator">|</span>
                                    <span class="stat-item"><strong>Showing Page:</strong> <span id="current-page-display"><?php echo $current_page; ?></span> of <span id="total-pages-display"><?php echo $total_pages; ?></span></span>
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <!-- Bootstrap Table page size selector -->
                                <div class="form-group">
                                    <label for="per_page_select">Combinations per page:</label>
                                    <select id="per_page_select" class="form-control" style="width: auto; display: inline-block;">
                                        <?php foreach($pagination_options as $option): ?>
                                            <option value="<?php echo $option; ?>" <?php echo ($per_page == $option) ? 'selected' : ''; ?>>
                                                <?php echo $option; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Predicted Numbers Section -->
                    <div class="predicted-numbers-container" style="margin-bottom: 15px; padding: 15px; background: #e8f4fd; border-radius: 8px; border: 1px solid #b8daff;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="predicted-numbers-display" id="predicted-numbers-display">
                                    <?php if (!empty($filter->numbers)): ?>
                                        <?php 
                                        // Check if this is an independent extra ball lottery
                                        $is_independent_extra_ball = (!empty($filter->duplicate_extra_ball) && !empty($filter->extra_balls));
                                        
                                        // Parse the comma-separated numbers
                                        $predicted_numbers = explode(',', $filter->numbers);
                                        $drawn_numbers = array();
                                        $bonus_numbers = array();
                                        
                                        // Get drawn numbers if available
                                        if ($draw_info) {
                                            // Collect main drawn numbers
                                            for ($i = 1; $i <= $filter->N; $i++) {
                                                $ball_field = 'ball' . $i;
                                                if (property_exists($draw_info, $ball_field)) {
                                                    $drawn_numbers[] = $draw_info->$ball_field;
                                                }
                                            }
                                            
                                            // Collect bonus numbers if they exist (check multiple possible field names)
                                            if (!empty($filter->extra_balls) && $draw_info->extra_ball_included) {
                                                $bonus_fields = array('extra', 'bonus', 'extra_ball', 'bonus_ball', 'bonus_number');
                                                foreach ($bonus_fields as $field) {
                                                    if (property_exists($draw_info, $field) && !is_null($draw_info->$field)) {
                                                        $bonus_numbers[] = $draw_info->$field;
                                                        break; // Only get the first bonus number found
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                        
                                        <?php if ($is_independent_extra_ball): ?>
                                            <!-- Independent Extra Ball Lottery - Separate Main and Extra Numbers -->
                                            
                                            <!-- Main Predicted Numbers -->
                                            <h5 style="margin-bottom: 10px; color: #0c5aa6;"><strong>Main Predicted Numbers:</strong></h5>
                                            <div class="predicted-numbers-list" style="margin-bottom: 15px;">
                                                <?php
                                                // Display main predicted numbers with highlighting for drawn numbers
                                                foreach ($predicted_numbers as $number) {
                                                    $number = trim($number);
                                                    $class = 'combination-number';
                                                    
                                                    if (in_array($number, $drawn_numbers)) {
                                                        $class .= ' winning-number';
                                                    }
                                                    
                                                    echo '<span class="' . $class . '" style="margin-right: 8px;">' . sprintf('%02d', $number) . '</span>';
                                                }
                                                ?>
                                            </div>
                                            
                                            <!-- Extra Predicted Numbers -->
                                            <h5 style="margin-bottom: 10px; color: #0c5aa6;"><strong>Extra Predicted Numbers:</strong></h5>
                                            <div class="extra-predicted-numbers-list">
                                                <?php
                                                // Display extra ball predictions
                                                if (!empty($filter->extra_balls)) {
                                                    // Check if it's "ALL" or specific numbers
                                                    if ($filter->extra_balls === 'ALL') {
                                                        // Display all possible extra numbers from occurrences data
                                                        if (!empty($extra_ball_occurrences)) {
                                                            // Sort extra ball numbers numerically
                                                            $sorted_extra_balls = $extra_ball_occurrences;
                                                            usort($sorted_extra_balls, function($a, $b) {
                                                                return intval($a['value']) - intval($b['value']);
                                                            });
                                                            
                                                            foreach ($sorted_extra_balls as $occurrence) {
                                                                $extra_number = $occurrence['value'];
                                                                $extra_class = 'combination-number';
                                                                
                                                                if (in_array($extra_number, $bonus_numbers)) {
                                                                    $extra_class .= ' bonus-number-match';
                                                                }
                                                                
                                                                echo '<span class="' . $extra_class . '" style="margin-right: 8px;">' . sprintf('%02d', $extra_number) . '</span>';
                                                            }
                                                        } else {
                                                            echo '<span style="color: #6c757d; font-style: italic;">All extra numbers included</span>';
                                                        }
                                                    } else {
                                                        $extra_numbers = explode(',', $filter->extra_balls);
                                                        foreach ($extra_numbers as $extra_number) {
                                                            $extra_number = trim($extra_number);
                                                            $extra_class = 'combination-number';
                                                            
                                                            if (in_array($extra_number, $bonus_numbers)) {
                                                                $extra_class .= ' bonus-number-match';
                                                            }
                                                            
                                                            echo '<span class="' . $extra_class . '" style="margin-right: 8px;">' . sprintf('%02d', $extra_number) . '</span>';
                                                        }
                                                    }
                                                } else {
                                                    echo '<span style="color: #6c757d; font-style: italic;">No extra numbers predicted</span>';
                                                }
                                                ?>
                                            </div>
                                            
                                        <?php else: ?>
                                            <!-- Regular Lottery - Combined Predicted Numbers -->
                                            <h5 style="margin-bottom: 10px; color: #0c5aa6;"><strong>Predicted Numbers:</strong></h5>
                                            <div class="predicted-numbers-list">
                                                <?php
                                                // Display each predicted number with appropriate highlighting
                                                foreach ($predicted_numbers as $number) {
                                                    $number = trim($number);
                                                    $class = 'combination-number';
                                                    
                                                    if (in_array($number, $drawn_numbers)) {
                                                        $class .= ' winning-number';
                                                    } elseif (in_array($number, $bonus_numbers)) {
                                                        $class .= ' bonus-number-match';
                                                    }
                                                    
                                                    echo '<span class="' . $class . '" style="margin-right: 8px;">' . sprintf('%02d', $number) . '</span>';
                                                }
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php
                                        // Add (TBD) indicator if draw is not available
                                        if (isset($display_mode) && $display_mode == 'tbd') {
                                            echo '<span style="margin-left: 15px; color: #6c757d; font-style: italic;">(TBD)</span>';
                                        }
                                        ?>
                                        
                                    <?php else: ?>
                                        <h5 style="margin-bottom: 0; color: #6c757d;"><strong>No Predicted Numbers are available.</strong></h5>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lottery Profile Statistics Presets Container -->
                    <div class="profile-presets-container" style="margin-bottom: 15px; padding: 12px; background: #fff3cd; border-radius: 8px; border: 1px solid #ffc107;">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 style="margin-bottom: 10px; color: #856404;"><strong><i class="fa fa-cog"></i> Lottery Profile Statistics Presets:</strong></h5>
                                <div class="presets-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 8px;">
                                    
                                    <!-- H-W-C Settings -->
                                    <div class="preset-item">
                                        <strong style="color: #495057;">H-W-C:</strong>
                                        <span style="color: #212529;">
                                            <?php echo !empty($filter->hwc) && $filter->hwc == 1 ? 'Enabled' : 'Disabled'; ?>
                                            <?php if (!empty($filter->h_w_c_group) && $filter->h_w_c_group !== 'ALL'): ?>
                                                <small style="color: #6c757d;"> (<?php echo htmlspecialchars($filter->h_w_c_group); ?>)</small>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Followers Settings -->
                                    <div class="preset-item">
                                        <strong style="color: #495057;">Followers:</strong>
                                        <span style="color: #212529;">
                                            <?php echo !empty($filter->followers) && $filter->followers == 1 ? 'Enabled' : 'Disabled'; ?>
                                            <?php if (!empty($filter->follower_type)): ?>
                                                <small style="color: #6c757d;">(<?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $filter->follower_type))); ?>
                                                <?php if ($filter->follower_type == 'after_ball' && !empty($filter->ball_points) && $filter->ball_points !== 'ALL'): ?>,
                                                    Ball: <?php echo htmlspecialchars($filter->ball_points); ?>
                                                <?php endif; ?>
                                                <?php if ($filter->follower_type == 'position' && !empty($filter->position_points) && $filter->position_points !== 'ALL'): ?>,
                                                    Pos: <?php echo htmlspecialchars($filter->position_points); ?>
                                                <?php endif; ?>)</small>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Extra Ball Settings -->
                                    <div class="preset-item">
                                        <strong style="color: #495057;">Extra Ball:</strong>
                                        <span style="color: #212529;">
                                            <?php echo !empty($filter->extra_balls) ? htmlspecialchars($filter->extra_balls) : 'Not Used'; ?>
                                            <?php if (!empty($filter->duplicate_extra_ball)): ?>
                                                <small style="color: #6c757d;"> (Independent: <?php echo $filter->duplicate_extra_ball == 1 ? 'Yes' : 'No'; ?>)</small>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Number Range -->
                                    <div class="preset-item">
                                        <strong style="color: #495057;">Number Range:</strong>
                                        <span style="color: #212529;">
                                            <?php echo !empty($filter->number_range) ? htmlspecialchars($filter->number_range) : 'ALL'; ?>
                                        </span>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filter Settings Container -->
                    <div class="filter-settings-container" style="margin-bottom: 15px; padding: 12px; background: #d1ecf1; border-radius: 8px; border: 1px solid #17a2b8;">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 style="margin-bottom: 10px; color: #0c5460;"><strong><i class="fa fa-filter"></i> Applied Filter Settings:</strong></h5>
                                <div class="filters-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 8px;">
                                    
                                    <!-- Friends -->
                                    <div class="filter-item">
                                        <strong style="color: #495057;">Friends:</strong>
                                        <span style="color: #212529;">
                                            <?php 
                                            if (!empty($filter->friends) && $filter->friends > 0) {
                                                if ($filter->friends == 1) {
                                                    echo '1-way Friend';
                                                } else if ($filter->friends == 2) {
                                                    echo '2-way Friends';
                                                } else {
                                                    echo $filter->friends . '-way Friends';
                                                }
                                            } else {
                                                echo 'No Friends';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Trends -->
                                    <div class="filter-item">
                                        <strong style="color: #495057;">Trends:</strong>
                                        <span style="color: #212529;">
                                            <?php echo !empty($filter->trends) ? htmlspecialchars($filter->trends) : 'Not Applied'; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Winning Sums -->
                                    <div class="filter-item">
                                        <strong style="color: #495057;">Winning Sums:</strong>
                                        <span style="color: #212529;">
                                            <?php echo !empty($filter->winning_sums) ? htmlspecialchars($filter->winning_sums) : 'Not Applied'; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Digit Sums -->
                                    <div class="filter-item">
                                        <strong style="color: #495057;">Digit Sums:</strong>
                                        <span style="color: #212529;">
                                            <?php echo !empty($filter->winning_digits) ? htmlspecialchars($filter->winning_digits) : 'Not Applied'; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Repeaters -->
                                    <div class="filter-item">
                                        <strong style="color: #495057;">Repeaters:</strong>
                                        <span style="color: #212529;">
                                            <?php echo !empty($filter->repeaters) ? htmlspecialchars($filter->repeaters) : 'Not Applied'; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Consecutives -->
                                    <div class="filter-item">
                                        <strong style="color: #495057;">Consecutives:</strong>
                                        <span style="color: #212529;">
                                            <?php echo !empty($filter->consecutives) ? htmlspecialchars($filter->consecutives) : 'Not Applied'; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Odd / Even -->
                                    <div class="filter-item">
                                        <strong style="color: #495057;">Odd/Even:</strong>
                                        <span style="color: #212529;">
                                            <?php echo !empty($filter->parity) ? htmlspecialchars($filter->parity) : 'Not Applied'; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Decades -->
                                    <div class="filter-item">
                                        <strong style="color: #495057;">Decades:</strong>
                                        <span style="color: #212529;">
                                            <?php echo !empty($filter->decades) ? htmlspecialchars($filter->decades) : 'Not Applied'; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Last Digits -->
                                    <div class="filter-item">
                                        <strong style="color: #495057;">Last Digits:</strong>
                                        <span style="color: #212529;">
                                            <?php echo !empty($filter->last_digits) ? htmlspecialchars($filter->last_digits) : 'Not Applied'; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Adjacent -->
                                    <div class="filter-item">
                                        <strong style="color: #495057;">Adjacent:</strong>
                                        <span style="color: #212529;">
                                            <?php echo !empty($filter->adjacents) ? htmlspecialchars($filter->adjacents) : 'Not Applied'; ?>
                                        </span>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Number Highlighting Legend -->
                    <div class="legend-container" style="margin-bottom: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6;">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 style="margin-bottom: 10px; color: #495057;"><strong>Number Highlighting Guide:</strong></h5>
                                <div class="legend-items" style="display: flex; flex-wrap: wrap; align-items: center; gap: 15px;">
                                    <div class="legend-item" style="display: flex; align-items: center; gap: 8px;">
                                        <span class="combination-number" style="margin: 0;">00</span>
                                        <span style="font-size: 14px; color: #6c757d;">Regular Number</span>
                                    </div>
                                    <div class="legend-item" style="display: flex; align-items: center; gap: 8px;">
                                        <span class="combination-number winning-number" style="margin: 0; animation: none;">00</span>
                                        <span style="font-size: 14px; color: #28a745; font-weight: bold;">Winning Number</span>
                                    </div>
                                    <div class="legend-item" style="display: flex; align-items: center; gap: 8px;">
                                        <span class="combination-number bonus-number-match" style="margin: 0; animation: none;">00</span>
                                        <span style="font-size: 14px; color: #fd7e14; font-weight: bold;">Bonus Number</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Combination Tickets Table Container -->
                    <div id="tickets-container">
                        <!-- Loading indicator -->
                        <div id="loading-indicator" style="display: none; text-align: center; padding: 20px;">
                            <i class="fa fa-spinner fa-spin fa-2x"></i>
                            <p>Loading combination tickets...</p>
                        </div>
                        
                        <!-- Combination Tickets Table -->
                        <div class="table-responsive" id="tickets-table">
                            <table id="combinationTicketsTable" 
                                   class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 4%;">##</th>
                                        <th style="width: 32%;">Combination</th>
                                        <th style="width: 4%;">Sum</th>
                                        <th style="width: 6%;">Digit Sum</th>
                                        <th style="width: 6%;">Repeats</th>
                                        <th style="width: 7%;">Consecutives</th>
                                        <th style="width: 4%;">Odd</th>
                                        <th style="width: 4%;">Even</th>
                                        <th style="width: 5%;">Decade</th>
                                        <th style="width: 4%;">Last</th>
                                        <th style="width: 5%;">Range</th>
                                        <th style="width: 19%;" class="text-center sortable-check-results" data-sort="asc">
                                            Check Results 
                                            <span class="sort-icon">⇅</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="tickets-tbody">
                                    <?php if(empty($tickets)): ?>
                                        <tr>
                                            <td colspan="12" class="text-center">
                                                <em>No combination tickets found.</em>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $base_offset = isset($offset) ? $offset : 0;
                                        foreach($tickets as $index => $ticket): 
                                            $current_row_number = $base_offset + $index + 1;
                                            
                                            // Helper function to get sort value for results
                                            // Extract number of matches from category string
                                            $matches = 0;
                                            if (preg_match('/(\d+)\s+Match/', $ticket['win_result']['category'], $match_result)) {
                                                $matches = (int)$match_result[1];
                                            }
                                            
                                            // Base sort values for different categories
                                            $base_sort_values = array(
                                                'Jackpot Winner' => 1000,
                                                'Major Winner' => 900,
                                                'Minor Winner' => 800,
                                                'Bonus Winner' => 700,
                                                'Not a Winner' => 100,
                                                'No Win' => 100,
                                                'TBD (To Be Determined)' => 50,
                                                'Expired' => 10
                                            );
                                            
                                            // Get base value for the category
                                            $base_value = 0;
                                            foreach ($base_sort_values as $key => $value) {
                                                if (strpos($ticket['win_result']['category'], $key) !== false || $ticket['win_result']['category'] === $key) {
                                                    $base_value = $value;
                                                    break;
                                                }
                                            }
                                            
                                            // For "Not a Winner" categories with matches, add the number of matches
                                            // This ensures 2 Matches (Not a Winner) > 1 Matches (Not a Winner)
                                            if (strpos($ticket['win_result']['category'], 'Not a Winner') !== false && $matches > 0) {
                                                $result_sort_value = $base_value + $matches;
                                            } else {
                                                // For other categories, return base value plus matches for fine-tuning
                                                $result_sort_value = $base_value + $matches;
                                            }
                                        ?>
                                            <?php
                                            // Determine row class based on win category
                                            $row_class = ($filter->active == 0) ? 'expired-row' : '';
                                            $color_class = $ticket['win_result']['color_class'];
                                            
                                            // Add winner row highlighting classes
                                            if ($color_class === 'jackpot-win') {
                                                $row_class .= ' winner-row-jackpot';
                                            } elseif ($color_class === 'major-win') {
                                                $row_class .= ' winner-row-major';
                                            } elseif ($color_class === 'minor-win') {
                                                $row_class .= ' winner-row-minor';
                                            } elseif ($color_class === 'bonus-win') {
                                                $row_class .= ' winner-row-bonus';
                                            }
                                            ?>
                                            <tr class="<?php echo trim($row_class); ?>"
                                                data-row-number="<?php echo sprintf('%02d', $current_row_number); ?>"
                                                data-combination="<?php echo htmlspecialchars(implode(' ', array_map(function($n) { return sprintf('%02d', $n); }, $ticket['numbers']))); ?>"
                                                data-check-results="<?php echo $result_sort_value; ?>">
                                                <td class="text-center" data-label="##">
                                                    <?php echo sprintf('%02d', $current_row_number); ?>
                                                </td>
                                                <td class="combination-numbers" data-label="Combination">
                                                    <?php 
                                                    $winning_count = 0;
                                                    $has_bonus = false;
                                                    
                                                    // Check if this is an independent extra ball lottery
                                                    $is_independent_extra_ball = isset($ticket['is_independent_extra_ball']) && $ticket['is_independent_extra_ball'];
                                                    
                                                    if ($is_independent_extra_ball && isset($ticket['main_numbers']) && isset($ticket['extra_ball'])) {
                                                        // Display main numbers
                                                        foreach($ticket['main_numbers'] as $number) {
                                                            $is_winning = false;
                                                            $is_bonus = false;
                                                            
                                                            // Check if this number matches any drawn numbers
                                                            if ($draw_info && (!isset($display_mode) || $display_mode != 'tbd')) {
                                                                // Check main numbers first
                                                                for ($i = 1; $i <= $filter->N; $i++) {
                                                                    $ball_field = 'ball' . $i;
                                                                    if (property_exists($draw_info, $ball_field)) {
                                                                        $drawn_number = $draw_info->$ball_field;
                                                                        
                                                                        // Try both strict and loose comparison
                                                                        if ($drawn_number == $number || (int)$drawn_number == (int)$number) {
                                                                            $is_winning = true;
                                                                            $winning_count++;
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            
                                                            $number_class = '';
                                                            if ((!isset($display_mode) || $display_mode != 'tbd')) {
                                                                if ($is_winning) {
                                                                    $number_class = 'winning-number';
                                                                }
                                                            }
                                                            
                                                            echo '<span class="combination-number ' . $number_class . '">' . sprintf('%02d', $number) . '</span> ';
                                                        }
                                                        
                                                        // Add separator and extra ball
                                                        echo '<span style="color: #666; font-weight: bold; margin: 0 5px;">+</span>';
                                                        
                                                        // Display extra ball
                                                        $extra_number = $ticket['extra_ball'];
                                                        $is_extra_winning = false;
                                                        
                                                        // Check if extra ball matches
                                                        if ($draw_info && (!isset($display_mode) || $display_mode != 'tbd')) {
                                                            if ($draw_info->extra_ball_included) {
                                                                $bonus_fields = array('extra', 'bonus', 'extra_ball', 'bonus_ball', 'bonus_number');
                                                                foreach ($bonus_fields as $field) {
                                                                    if (property_exists($draw_info, $field)) {
                                                                        $bonus_number = $draw_info->$field;
                                                                        
                                                                        if ($bonus_number == $extra_number || (int)$bonus_number == (int)$extra_number) {
                                                                            $is_extra_winning = true;
                                                                            $has_bonus = true;
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        
                                                        $extra_class = '';
                                                        if ((!isset($display_mode) || $display_mode != 'tbd')) {
                                                            if ($is_extra_winning) {
                                                                $extra_class = 'bonus-number-match';
                                                            }
                                                        }
                                                        
                                                        echo '<span class="combination-number ' . $extra_class . '">' . sprintf('%02d', $extra_number) . '</span>';
                                                        
                                                    } else {
                                                        // Regular lottery display (existing logic)
                                                        foreach($ticket['numbers'] as $number) {
                                                            $is_winning = false;
                                                            $is_bonus = false;
                                                            
                                                            // Check if this number matches any drawn numbers
                                                            if ($draw_info && (!isset($display_mode) || $display_mode != 'tbd')) {
                                                                // Check main numbers first
                                                                for ($i = 1; $i <= $filter->N; $i++) {
                                                                    $ball_field = 'ball' . $i;
                                                                    if (property_exists($draw_info, $ball_field)) {
                                                                        $drawn_number = $draw_info->$ball_field;
                                                                        
                                                                        // Try both strict and loose comparison
                                                                        if ($drawn_number == $number || (int)$drawn_number == (int)$number) {
                                                                            $is_winning = true;
                                                                            $winning_count++;
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                                
                                                                // Check bonus number only if not already a main number match
                                                                if (!$is_winning && $draw_info->extra_ball_included) {
                                                                    $bonus_fields = array('extra', 'bonus', 'extra_ball', 'bonus_ball', 'bonus_number');
                                                                    foreach ($bonus_fields as $field) {
                                                                        if (property_exists($draw_info, $field)) {
                                                                            $bonus_number = $draw_info->$field;
                                                                            
                                                                            if ($bonus_number == $number || (int)$bonus_number == (int)$number) {
                                                                                $is_bonus = true;
                                                                                $has_bonus = true;
                                                                                break;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            
                                                            $number_class = '';
                                                            if ((!isset($display_mode) || $display_mode != 'tbd')) {
                                                                if ($is_winning) {
                                                                    $number_class = 'winning-number';
                                                                } elseif ($is_bonus) {
                                                                    $number_class = 'bonus-number-match';
                                                                }
                                                            }
                                                            
                                                            echo '<span class="combination-number ' . $number_class . '">' . sprintf('%02d', $number) . '</span> ';
                                                        }
                                                    }
                                                    
                                                    // Store the winning analysis for proper prize determination
                                                    // This should match the lottery_profile_prize table logic:
                                                    // - Bonus wins only count when you have main matches + bonus
                                                    // - Just bonus number alone = No Win
                                                    $ticket['calculated_winning_count'] = $winning_count;
                                                    $ticket['calculated_has_bonus'] = $has_bonus;
                                                    ?>
                                                </td>
                                                <td class="text-center" data-label="Sum"><?php echo isset($ticket['sum']) ? $ticket['sum'] : '-'; ?></td>
                                                <td class="text-center" data-label="Digit Sum"><?php echo isset($ticket['digit_sum']) ? $ticket['digit_sum'] : '-'; ?></td>
                                                <td class="text-center" data-label="Repeats"><?php echo isset($ticket['repeaters']) ? $ticket['repeaters'] : '-'; ?></td>
                                                <td class="text-center" data-label="Consecutives"><?php echo isset($ticket['consecutives']) ? $ticket['consecutives'] : '-'; ?></td>
                                                <td class="text-center" data-label="Odd"><?php echo isset($ticket['odd']) ? $ticket['odd'] : '-'; ?></td>
                                                <td class="text-center" data-label="Even"><?php echo isset($ticket['even']) ? $ticket['even'] : '-'; ?></td>
                                                <td class="text-center" data-label="Decade"><?php echo isset($ticket['decade']) ? $ticket['decade'] : '-'; ?></td>
                                                <td class="text-center" data-label="Last"><?php echo isset($ticket['last']) ? $ticket['last'] : '-'; ?></td>
                                                <td class="text-center" data-label="Range"><?php echo isset($ticket['range']) ? $ticket['range'] : '-'; ?></td>
                                                <td class="text-center check-results" data-label="Check Results">
                                                    <?php if(isset($display_mode) && $display_mode == 'tbd'): ?>
                                                        <?php if ($filter->active == 0): ?>
                                                            <span class="check-result-out-of-date" style="background-color: #ff0000; color: white; padding: 4px 8px; border-radius: 4px; font-weight: bold;">Draw is Out of Date</span>
                                                        <?php else: ?>
                                                            <span class="check-result-tbd">TBD (To Be Determined)</span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <?php
                                                        // Use the win_result from controller which now properly handles
                                                        // lottery_profile_prize table logic and extra_ball_included status
                                                        $display_category = $ticket['win_result']['category'];
                                                        $color_class = $ticket['win_result']['color_class'];
                                                        ?>
                                                        <span class="result-<?php echo $color_class; ?>">
                                                            <?php echo $display_category; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div id="pagination-container">
                            <?php if($total_pages > 1): ?>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="pagination-info" id="pagination-info">
                                            Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $per_page, $total_tickets); ?> 
                                            of <?php echo $total_tickets; ?> entries
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-center">
                                            <div class="pagination-wrapper" id="pagination-wrapper">
                                                <span class="pagination-nav" data-action="first" data-page="1">&laquo;&laquo;</span>
                                                <span class="pagination-nav" data-action="prev" data-page="<?php echo max(1, $current_page - 1); ?>">&laquo;</span>
                                                <?php 
                                                $start_page = max(1, $current_page - 2);
                                                $end_page = min($total_pages, $current_page + 2);
                                                
                                                for($i = $start_page; $i <= $end_page; $i++): 
                                                ?>
                                                    <span class="pagination-page <?php echo ($i == $current_page) ? 'active' : ''; ?>" data-page="<?php echo $i; ?>">
                                                        <?php echo $i; ?>
                                                    </span>
                                                <?php endfor; ?>
                                                <span class="pagination-nav" data-action="next" data-page="<?php echo min($total_pages, $current_page + 1); ?>">&raquo;</span>
                                                <span class="pagination-nav" data-action="last" data-page="<?php echo $total_pages; ?>">&raquo;&raquo;</span>
                                            </div>
                                            <div class="page-info" id="page-info">
                                                Page <?php echo $current_page; ?> of <?php echo $total_pages; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* Draw Information Styling */
.draw-info-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: 1px solid #5a6cb8;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    color: white;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.draw-header-box h4 {
    margin: 0 0 15px 0;
    color: white;
    font-size: 18px;
}

.drawn-numbers-section {
    font-size: 16px;
    font-weight: bold;
}

.drawn-numbers-display {
    margin-top: 10px;
    text-align: center;
}

.drawn-number-highlight {
    display: inline-block;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: white;
    padding: 8px 12px;
    margin: 3px;
    border-radius: 50%;
    min-width: 40px;
    text-align: center;
    font-weight: bold;
    font-size: 16px;
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    border: 2px solid white;
}

.bonus-number-highlight {
    display: inline-block;
    background: linear-gradient(135deg, #feca57 0%, #ff9ff3 100%);
    color: #333;
    padding: 8px 12px;
    margin: 3px;
    border-radius: 50%;
    min-width: 40px;
    text-align: center;
    font-weight: bold;
    font-size: 16px;
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    border: 2px solid white;
}

.plus-sign {
    color: white;
    font-size: 18px;
    margin: 0 10px;
}

/* Table Information Styling */
.table-info-header {
    margin-bottom: 20px;
    padding: 20px;
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    border-radius: 8px;
    border: 1px solid #d1ecf1;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Filter Status Badge Styling */
.filter-status-badge {
    margin-top: 10px;
}

.status-active {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 14px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    display: inline-block;
}

.status-expired {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 14px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    display: inline-block;
}

.table-info-header h4 {
    margin: 0 0 15px 0;
    color: #155724;
    font-size: 18px;
}

.table-stats {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    font-size: 14px;
}

.stat-item {
    color: #155724;
    font-weight: bold;
    margin-right: 15px;
}

.stat-separator {
    color: #6c757d;
    margin: 0 10px;
    font-weight: bold;
}

#total-winners {
    color: #dc3545;
    font-size: 16px;
}

/* Combination Numbers Styling */
.combination-number {
    display: inline-block;
    background-color: #f8f9fa;
    color: #333;
    padding: 3px 7px;
    margin: 1px;
    border-radius: 4px;
    min-width: 29px;
    text-align: center;
    font-weight: bold;
    border: 2px solid #dee2e6;
    font-size: 13px;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.combination-numbers {
    white-space: nowrap;
    font-size: 13px;
    line-height: 1.5;
}

.winning-number {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    color: white !important;
    border-color: #20c997 !important;
    box-shadow: 0 3px 8px rgba(40, 167, 69, 0.4) !important;
    transform: scale(1.05) !important;
    animation: winningPulse 2s infinite !important;
    font-weight: 900 !important;
}

.bonus-number-match {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
    color: #000 !important;
    border-color: #fd7e14 !important;
    box-shadow: 0 3px 8px rgba(255, 193, 7, 0.4) !important;
    transform: scale(1.05) !important;
    animation: bonusPulse 2s infinite !important;
    font-weight: 900 !important;
}

/* Enhanced animations for winning numbers */
@keyframes winningPulse {
    0%, 100% {
        box-shadow: 0 3px 8px rgba(40, 167, 69, 0.4);
    }
    50% {
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.7);
    }
}

@keyframes bonusPulse {
    0%, 100% {
        box-shadow: 0 3px 8px rgba(255, 193, 7, 0.4);
    }
    50% {
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.7);
    }
}

/* Result Styling */
.check-results {
    font-weight: bold;
}

.result-jackpot-win {
    color: #28a745;
    font-weight: bold;
    font-size: 14px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

.result-major-win {
    color: #17a2b8;
    font-weight: bold;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

.result-minor-win {
    color: #ffc107;
    font-weight: bold;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

.result-bonus-win {
    color: #6f42c1;
    font-weight: bold;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

.result-no-win {
    color: #6c757d;
}

.result-not-a-winner {
    color: #6c757d;
}

.result-expired {
    color: #dc3545;
    font-weight: bold;
}

/* Enhanced row highlighting for winners */
tr:has(.result-jackpot-win) {
    background: linear-gradient(90deg, rgba(40, 167, 69, 0.05) 0%, rgba(255, 255, 255, 0) 100%);
    border-left: 4px solid #28a745;
}

tr:has(.result-major-win) {
    background: linear-gradient(90deg, rgba(23, 162, 184, 0.05) 0%, rgba(255, 255, 255, 0) 100%);
    border-left: 4px solid #17a2b8;
}

tr:has(.result-minor-win) {
    background: linear-gradient(90deg, rgba(255, 193, 7, 0.05) 0%, rgba(255, 255, 255, 0) 100%);
    border-left: 4px solid #ffc107;
}

tr:has(.result-bonus-win) {
    background: linear-gradient(90deg, rgba(111, 66, 193, 0.05) 0%, rgba(255, 255, 255, 0) 100%);
    border-left: 4px solid #6f42c1;
}

/* Fallback row highlighting classes for better browser support */
.winner-row-jackpot {
    background: linear-gradient(90deg, rgba(40, 167, 69, 0.05) 0%, rgba(255, 255, 255, 0) 100%) !important;
    border-left: 4px solid #28a745 !important;
}

.winner-row-major {
    background: linear-gradient(90deg, rgba(23, 162, 184, 0.05) 0%, rgba(255, 255, 255, 0) 100%) !important;
    border-left: 4px solid #17a2b8 !important;
}

.winner-row-minor {
    background: linear-gradient(90deg, rgba(255, 193, 7, 0.05) 0%, rgba(255, 255, 255, 0) 100%) !important;
    border-left: 4px solid #ffc107 !important;
}

.winner-row-bonus {
    background: linear-gradient(90deg, rgba(111, 66, 193, 0.05) 0%, rgba(255, 255, 255, 0) 100%) !important;
    border-left: 4px solid #6f42c1 !important;
}

.expired-row {
    background-color: #f8f8f8;
    opacity: 0.7;
}

/* Pagination Styling */
.pagination-wrapper {
    display: inline-block;
    margin: 10px 0;
}

.pagination-page, .pagination-nav {
    display: inline-block;
    padding: 5px 10px;
    margin: 0 2px;
    border: 1px solid #dee2e6;
    background-color: white;
    color: #007bff;
    cursor: pointer;
    border-radius: 3px;
}

.pagination-page:hover, .pagination-nav:hover {
    background-color: #e9ecef;
}

.pagination-page.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.page-info {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
}

.pagination-info {
    margin-top: 15px;
    font-size: 13px;
    color: #777;
}

/* Responsive Design */
@media (max-width: 768px) {
    .drawn-number, .bonus-number {
        min-width: 30px;
        padding: 3px 6px;
        font-size: 12px;
    }
    
    .combination-number {
        min-width: 26px;
        padding: 3px 6px;
        font-size: 12px;
        margin: 1px;
    }
    
    .combination-numbers {
        white-space: normal !important;
        font-size: 12px;
    }
    
    /* Maintain enhanced styling on mobile */
    .winning-number {
        transform: scale(1.02) !important;
    }
    
    .bonus-number-match {
        transform: scale(1.02) !important;
    }
    
    /* Stack table for better mobile viewing */
    #combinationTicketsTable thead {
        display: none;
    }
    
    #combinationTicketsTable tbody tr {
        display: block;
        margin-bottom: 15px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 10px;
    }
    
    #combinationTicketsTable tbody td {
        display: block;
        text-align: left !important;
        padding: 5px;
        border: none;
    }
    
    #combinationTicketsTable tbody td:before {
        content: attr(data-label);
        font-weight: bold;
        display: inline-block;
        width: 120px;
    }
}

/* Statistics columns styling */
#combinationTicketsTable th:nth-child(n+3):nth-child(-n+11) {
    text-align: center;
    font-size: 10px;
    white-space: nowrap;
    padding: 8px 2px;
    line-height: 1.2;
}

#combinationTicketsTable td:nth-child(n+3):nth-child(-n+11) {
    text-align: center;
    font-family: 'Courier New', monospace;
    font-weight: bold;
    color: #495057;
    padding: 8px 2px;
}

/* Ensure combination column doesn't wrap */
#combinationTicketsTable td:nth-child(2) {
    white-space: nowrap;
    overflow: visible;
}

#combinationTicketsTable th:nth-child(2) {
    white-space: nowrap;
}

/* Table responsive wrapper */
.table-responsive {
    border: none;
    overflow-x: auto !important;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
    width: 100%;
    max-width: 100%;
    display: block;
}

/* Ensure table triggers scrollbar */
.table-responsive::-webkit-scrollbar {
    height: 12px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #555;
}

#combinationTicketsTable {
    min-width: 1250px;
    table-layout: fixed;
}

/* Table header styling */
#combinationTicketsTable thead th {
    vertical-align: middle;
    overflow: hidden;
    text-overflow: ellipsis;
}

#combinationTicketsTable tbody td {
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Custom Check Results Sorting */
.sortable-check-results {
    cursor: pointer;
    user-select: none;
    position: relative;
}

.sortable-check-results:hover {
    background-color: #f8f9fa;
}

.sort-icon {
    font-size: 12px;
    margin-left: 5px;
    color: #6c757d;
}

.sortable-check-results.asc .sort-icon::before {
    content: "↑";
    color: #007bff;
}

.sortable-check-results.desc .sort-icon::before {
    content: "↓";
    color: #007bff;
}

/* Profile Presets Container Styling */
.profile-presets-container {
    animation: fadeIn 0.5s ease-in;
}

.presets-grid {
    font-size: 14px;
}

.preset-item {
    padding: 6px 10px;
    background: white;
    border-radius: 6px;
    border: 1px solid #ffc107;
    font-size: 13px;
    line-height: 1.4;
}

.preset-item strong {
    display: inline;
    margin-right: 6px;
}

/* Filter Settings Container Styling */
.filter-settings-container {
    animation: fadeIn 0.5s ease-in 0.2s;
    animation-fill-mode: both;
}

.filters-grid {
    font-size: 14px;
}

.filter-item {
    padding: 6px 10px;
    background: white;
    border-radius: 6px;
    border: 1px solid #17a2b8;
    font-size: 13px;
    line-height: 1.4;
}

.filter-item strong {
    display: inline;
    margin-right: 6px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .presets-grid, .filters-grid {
        grid-template-columns: 1fr;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
$(document).ready(function() {
    var filterId = <?php echo $filter->id; ?>;
    var currentPage = <?php echo $current_page; ?>;
    var totalPages = <?php echo $total_pages; ?>;
    var currentSortColumn = null; // Track current sort column
    var currentSortOrder = 'asc'; // Track current sort order
    var currentPerPage = <?php echo $per_page; ?>; // Track current per page setting
    
    // Handle per page change - ORIGINAL FUNCTIONALITY RESTORED
    $('#per_page_select').change(function() {
        var per_page = $(this).val();
        currentPerPage = per_page;
        loadPage(1, per_page);
    });
    
    // Handle pagination clicks - ORIGINAL FUNCTIONALITY RESTORED
    $(document).on('click', '.pagination-page, .pagination-nav', function() {
        var page = $(this).data('page');
        if (page && page != currentPage && page >= 1 && page <= totalPages) {
            loadPage(page, $('#per_page_select').val());
        }
    });
    
    // Custom Check Results Sorting
    $(document).on('click', '.sortable-check-results', function(e) {
        e.preventDefault();
        
        var $this = $(this);
        var currentSort = $this.data('sort') || 'asc';
        var newSort = currentSort === 'asc' ? 'desc' : 'asc';
        
        // Show loading indicator immediately for sorting
        $('#loading-indicator').show();
        $('#tickets-table').hide();
        $('#pagination-container').hide();
        
        // Update sort indicator
        $this.removeClass('asc desc').addClass(newSort).data('sort', newSort);
        
        // Update sort icon
        var icon = newSort === 'asc' ? '↑' : '↓';
        $this.find('.sort-icon').text(icon);
        
        // Store current sort settings globally
        currentSortOrder = newSort;
        currentSortColumn = 'check_results';
        
        // Reload the first page with new sorting applied to all results
        loadPage(1, currentPerPage);
    });

    
    function loadPage(page, per_page) {
        // Show loading indicator
        $('#loading-indicator').show();
        $('#tickets-table').hide();
        $('#pagination-container').hide();
        
        $.ajax({
            url: '<?php echo site_url("admin/prize/load_combination_tickets"); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                filter_id: filterId,
                page: page,
                per_page: per_page,
                sort_column: currentSortColumn,
                sort_order: currentSortOrder
            },
            timeout: 30000, // 30 second timeout
            success: function(response) {
                if (response.success) {
                    // Update table content
                    updateTable(response.tickets, response.filter, response.draw_info, response);
                    
                    // Update pagination
                    updatePagination(response.pagination);
                    
                    // Update header stats
                    updateHeaderStats(response.pagination, response.total_winners);
                    
                    // Update draw header if display mode changes
                    updateDrawHeader(response);
                    
                    // Update current page tracking
                    currentPage = response.pagination.current_page;
                    totalPages = response.pagination.total_pages;
                    
                } else {
                    var errorMessage = response.message || 'Unknown error occurred';
                    alert('Error: ' + errorMessage);
                }
                
                // Hide loading indicator
                $('#loading-indicator').hide();
                $('#tickets-table').show();
                $('#pagination-container').show();
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                
                var errorMessage = 'Error loading tickets. Please try again.';
                
                // Check for timeout
                if (status === 'timeout') {
                    errorMessage = 'Request timed out. The operation may be taking too long.';
                }
                // Check for specific HTTP errors
                else if (xhr.status === 500) {
                    errorMessage = 'Server error (500). Please check the server logs.';
                }
                else if (xhr.status === 404) {
                    errorMessage = 'Endpoint not found (404). Please check the URL.';
                }
                else if (xhr.status === 403) {
                    errorMessage = 'Access denied (403). Please check your session.';
                }
                
                // Try to parse JSON response for specific error message
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response && response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    // Use default message if JSON parsing fails
                }
                
                alert(errorMessage);
                
                // Hide loading indicator
                $('#loading-indicator').hide();
                $('#tickets-table').show();
                $('#pagination-container').show();
            }
        });
    }
    
    function updateTable(tickets, filter, draw_info, response) {
        var tbody = $('#tickets-tbody');
        tbody.empty();
        
        if (tickets.length === 0) {
            tbody.append('<tr><td colspan="12" class="text-center"><em>No combination tickets found.</em></td></tr>');
            return;
        }
        
        // Calculate proper row numbers based on pagination
        var baseOffset = response.pagination ? (response.pagination.offset || 0) : 0;
        
        $.each(tickets, function(index, ticket) {
            var currentRowNumber = baseOffset + index + 1;
            
            // Determine row class based on win category
            var rowClass = filter.active == 0 ? 'expired-row' : '';
            var colorClass = ticket.win_result.color_class;
            
            // Add winner row highlighting classes
            if (colorClass === 'jackpot-win') {
                rowClass += ' winner-row-jackpot';
            } else if (colorClass === 'major-win') {
                rowClass += ' winner-row-major';
            } else if (colorClass === 'minor-win') {
                rowClass += ' winner-row-minor';
            } else if (colorClass === 'bonus-win') {
                rowClass += ' winner-row-bonus';
            }
            
            var row = '<tr class="' + rowClass + '"';
            row += ' data-check-results="' + getResultSortValue(ticket.win_result.category) + '">';
            row += '<td class="text-center">' + String(currentRowNumber).padStart(2, '0') + '</td>';
            row += '<td class="combination-numbers">';
            
            // Add combination numbers with highlighting and count matches
            var winningCount = 0;
            var hasBonus = false;
            
            // Check if this is an independent extra ball lottery
            var isIndependentExtraBall = ticket.is_independent_extra_ball && ticket.main_numbers && ticket.extra_ball !== undefined;
            
            if (isIndependentExtraBall) {
                // Display main numbers
                $.each(ticket.main_numbers, function(i, number) {
                    var numberClass = '';
                    var isWinning = false;
                    
                    if (draw_info && response.display_mode !== 'tbd') {
                        // Check main numbers first
                        for (var j = 1; j <= filter.N; j++) {
                            var ballField = 'ball' + j;
                            if (draw_info[ballField]) {
                                var drawnNumber = draw_info[ballField];
                                
                                // Try both strict and loose comparison
                                if (drawnNumber == number || parseInt(drawnNumber) == parseInt(number)) {
                                    isWinning = true;
                                    winningCount++;
                                    break;
                                }
                            }
                        }
                    }
                    
                    if (response.display_mode !== 'tbd') {
                        if (isWinning) {
                            numberClass = 'winning-number';
                        }
                    }
                    
                    row += '<span class="combination-number ' + numberClass + '">' + String(number).padStart(2, '0') + '</span> ';
                });
                
                // Add separator and extra ball
                row += '<span style="color: #666; font-weight: bold; margin: 0 5px;">+</span>';
                
                // Display extra ball
                var extraNumber = ticket.extra_ball;
                var extraClass = '';
                var isExtraWinning = false;
                
                if (draw_info && response.display_mode !== 'tbd') {
                    if (draw_info.extra_ball_included) {
                        var bonusFields = ['extra', 'bonus', 'extra_ball', 'bonus_ball', 'bonus_number'];
                        for (var k = 0; k < bonusFields.length; k++) {
                            var field = bonusFields[k];
                            if (draw_info[field]) {
                                var bonusNumber = draw_info[field];
                                
                                if (bonusNumber == extraNumber || parseInt(bonusNumber) == parseInt(extraNumber)) {
                                    isExtraWinning = true;
                                    hasBonus = true;
                                    break;
                                }
                            }
                        }
                    }
                }
                
                if (response.display_mode !== 'tbd') {
                    if (isExtraWinning) {
                        extraClass = 'bonus-number-match';
                    }
                }
                
                row += '<span class="combination-number ' + extraClass + '">' + String(extraNumber).padStart(2, '0') + '</span>';
                
            } else {
                // Regular lottery display (existing logic)
                $.each(ticket.numbers, function(i, number) {
                    var numberClass = '';
                    var isWinning = false;
                    var isBonus = false;
                    
                    if (draw_info && response.display_mode !== 'tbd') {
                        // Check main numbers first
                        for (var j = 1; j <= filter.N; j++) {
                            var ballField = 'ball' + j;
                            if (draw_info[ballField]) {
                                var drawnNumber = draw_info[ballField];
                                
                                // Try both strict and loose comparison
                                if (drawnNumber == number || parseInt(drawnNumber) == parseInt(number)) {
                                    isWinning = true;
                                    winningCount++;
                                    break;
                                }
                            }
                        }
                        
                        // Check bonus number only if not already a main number match
                        if (!isWinning && draw_info.extra_ball_included) {
                            var bonusFields = ['extra', 'bonus', 'extra_ball', 'bonus_ball', 'bonus_number'];
                            for (var k = 0; k < bonusFields.length; k++) {
                                var field = bonusFields[k];
                                if (draw_info[field]) {
                                    var bonusNumber = draw_info[field];
                                    
                                    if (bonusNumber == number || parseInt(bonusNumber) == parseInt(number)) {
                                        isBonus = true;
                                        hasBonus = true;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    
                    if (response.display_mode !== 'tbd') {
                        if (isWinning) {
                            numberClass = 'winning-number';
                        } else if (isBonus) {
                            numberClass = 'bonus-number-match';
                        }
                    }
                    
                    row += '<span class="combination-number ' + numberClass + '">' + String(number).padStart(2, '0') + '</span> ';
                });
            }
            
            row += '</td>';
            
            // Add statistics columns
            row += '<td class="text-center" data-label="Sum">' + (ticket.sum !== undefined ? ticket.sum : '-') + '</td>';
            row += '<td class="text-center" data-label="Digit Sum">' + (ticket.digit_sum !== undefined ? ticket.digit_sum : '-') + '</td>';
            row += '<td class="text-center" data-label="Repeats">' + (ticket.repeaters !== undefined ? ticket.repeaters : '-') + '</td>';
            row += '<td class="text-center" data-label="Consecutives">' + (ticket.consecutives !== undefined ? ticket.consecutives : '-') + '</td>';
            row += '<td class="text-center" data-label="Odd">' + (ticket.odd !== undefined ? ticket.odd : '-') + '</td>';
            row += '<td class="text-center" data-label="Even">' + (ticket.even !== undefined ? ticket.even : '-') + '</td>';
            row += '<td class="text-center" data-label="Decade">' + (ticket.decade !== undefined ? ticket.decade : '-') + '</td>';
            row += '<td class="text-center" data-label="Last">' + (ticket.last !== undefined ? ticket.last : '-') + '</td>';
            row += '<td class="text-center" data-label="Range">' + (ticket.range !== undefined ? ticket.range : '-') + '</td>';
            
            row += '<td class="text-center check-results" data-label="Check Results">';
            
            // Check for TBD display mode
            if (response.display_mode === 'tbd') {
                row += '<span class="check-result-tbd">TBD (To Be Determined)</span>';
            } else {
                // Apply lottery_profile_prize table logic
                var displayCategory = ticket.win_result.category;
                var colorClass = ticket.win_result.color_class;
                
                row += '<span class="result-' + colorClass + '">' + displayCategory + '</span>';
            }
            
            row += '</td>';
            row += '</tr>';
            
            // Apply lottery_profile_prize table logic for sorting
            var finalCategory = ticket.win_result.category;
            
            // Create row element and set data attribute with corrected sort value
            var $row = $(row);
            $row.attr('data-check-results', getResultSortValue(finalCategory));
            tbody.append($row);
        });
    }
    
    function updatePagination(pagination) {
        // Update pagination info
        $('#pagination-info').text('Showing ' + pagination.showing_from + ' to ' + pagination.showing_to + ' of ' + pagination.total_tickets + ' entries');
        $('#page-info').text('Page ' + pagination.current_page + ' of ' + pagination.total_pages);
        
        // Update pagination wrapper
        var wrapper = $('#pagination-wrapper');
        wrapper.empty();
        
        if (pagination.total_pages > 1) {
            // First and Previous buttons
            wrapper.append('<span class="pagination-nav" data-page="1">&laquo;&laquo;</span>');
            wrapper.append('<span class="pagination-nav" data-page="' + Math.max(1, pagination.current_page - 1) + '">&laquo;</span>');
            
            // Page numbers
            var startPage = Math.max(1, pagination.current_page - 2);
            var endPage = Math.min(pagination.total_pages, pagination.current_page + 2);
            
            for (var i = startPage; i <= endPage; i++) {
                var activeClass = (i == pagination.current_page) ? 'active' : '';
                wrapper.append('<span class="pagination-page ' + activeClass + '" data-page="' + i + '">' + i + '</span>');
            }
            
            // Next and Last buttons
            wrapper.append('<span class="pagination-nav" data-page="' + Math.min(pagination.total_pages, pagination.current_page + 1) + '">&raquo;</span>');
            wrapper.append('<span class="pagination-nav" data-page="' + pagination.total_pages + '">&raquo;&raquo;</span>');
        }
        
        // Show/hide pagination container
        if (pagination.total_pages <= 1) {
            $('#pagination-container').hide();
        } else {
            $('#pagination-container').show();
        }
    }
    
    function updateHeaderStats(pagination, totalWinnersOnPage) {
        $('#total-winners').text(totalWinnersOnPage.toLocaleString());
        $('#current-page-display').text(pagination.current_page);
        $('#total-pages-display').text(pagination.total_pages);
    }
    
    function updateDrawHeader(response) {
        // Update draw information header based on display mode
        if (response.display_mode === 'tbd') {
            // For TBD mode, use pre-formatted date if available, otherwise format from MySQL date
            var displayDate;
            if (response.next_draw_date) {
                displayDate = response.next_draw_date; // Use pre-formatted date from server
            } else if (response.next_draw_date_for_js) {
                // Parse MySQL format date and format it
                var nextDrawDate = new Date(response.next_draw_date_for_js);
                var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                displayDate = nextDrawDate.toLocaleDateString('en-US', options);
            } else {
                displayDate = 'Unknown';
            }
            
            $('.draw-header-box h4').html('<strong>Next Draw Date:</strong> ' + displayDate);
            $('.drawn-numbers-display').html('<span class="tbd-display">TBD (To Be Determined)</span>');
        } else if (response.draw_info) {
            // For draw results, use pre-formatted date if available, otherwise format from MySQL date
            var displayDate;
            if (response.next_draw_date) {
                displayDate = response.next_draw_date; // Use pre-formatted date from server
            } else if (response.next_draw_date_for_js) {
                // Parse MySQL format date and format it
                var drawDate = new Date(response.next_draw_date_for_js);
                var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                displayDate = drawDate.toLocaleDateString('en-US', options);
            } else {
                // Fallback to parsing draw_info date
                var drawDate = new Date(response.draw_info.draw_date);
                var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                displayDate = drawDate.toLocaleDateString('en-US', options);
            }
            
            $('.draw-header-box h4').html('<strong>Draw Date:</strong> ' + displayDate);
            
            // Rebuild drawn numbers display
            var numbersHtml = '';
            var filter = response.filter;
            
            for (var i = 1; i <= filter.N; i++) {
                var ballField = 'ball' + i;
                if (response.draw_info[ballField]) {
                    numbersHtml += '<span class="drawn-number-highlight">' + String(response.draw_info[ballField]).padStart(2, '0') + '</span>';
                }
            }
            
            // Add bonus number if available
            if (response.draw_info.extra_ball_included) {
                var bonusFields = ['extra', 'bonus', 'extra_ball', 'bonus_ball', 'bonus_number'];
                for (var j = 0; j < bonusFields.length; j++) {
                    var field = bonusFields[j];
                    if (response.draw_info[field] && response.draw_info[field] != null) {
                        numbersHtml += ' <strong class="plus-sign">+</strong> <span class="bonus-number-highlight">' + String(response.draw_info[field]).padStart(2, '0') + '</span>';
                        break;
                    }
                }
            }
            
            $('.drawn-numbers-display').html(numbersHtml);
        }
        
        // Update predicted numbers display
        updatePredictedNumbers(response);
    }
    
    function updatePredictedNumbers(response) {
        var predictedContainer = $('#predicted-numbers-display');
        
        if (!response.filter.numbers || response.filter.numbers.trim() === '') {
            // No predicted numbers available
            predictedContainer.html('<h5 style="margin-bottom: 0; color: #6c757d;"><strong>No Predicted Numbers are available.</strong></h5>');
            return;
        }
        
        // Check if this is an independent extra ball lottery
        var isIndependentExtraBall = (response.filter.duplicate_extra_ball && response.filter.extra_balls);
        
        // Parse predicted numbers
        var predictedNumbers = response.filter.numbers.split(',');
        var drawnNumbers = [];
        var bonusNumbers = [];
        
        // Get drawn numbers if available
        if (response.draw_info) {
            // Collect main drawn numbers
            for (var i = 1; i <= response.filter.N; i++) {
                var ballField = 'ball' + i;
                if (response.draw_info[ballField]) {
                    drawnNumbers.push(response.draw_info[ballField].toString());
                }
            }
            
            // Collect bonus numbers if they exist (check multiple possible field names)
            if (response.filter.extra_balls && response.draw_info.extra_ball_included) {
                var bonusFields = ['extra', 'bonus', 'extra_ball', 'bonus_ball', 'bonus_number'];
                for (var j = 0; j < bonusFields.length; j++) {
                    var field = bonusFields[j];
                    if (response.draw_info[field] && response.draw_info[field] != null) {
                        bonusNumbers.push(response.draw_info[field].toString());
                        break; // Only get the first bonus number found
                    }
                }
            }
        }
        
        var numbersHtml = '';
        
        if (isIndependentExtraBall) {
            // Independent Extra Ball Lottery - Separate Main and Extra Numbers
            
            // Main Predicted Numbers
            numbersHtml += '<h5 style="margin-bottom: 10px; color: #0c5aa6;"><strong>Main Predicted Numbers:</strong></h5>';
            numbersHtml += '<div class="predicted-numbers-list" style="margin-bottom: 15px;">';
            
            for (var i = 0; i < predictedNumbers.length; i++) {
                var number = predictedNumbers[i].trim();
                var className = 'combination-number';
                
                if (drawnNumbers.indexOf(number) !== -1) {
                    className += ' winning-number';
                }
                
                numbersHtml += '<span class="' + className + '" style="margin-right: 8px;">' + String(number).padStart(2, '0') + '</span>';
            }
            
            numbersHtml += '</div>';
            
            // Extra Predicted Numbers
            numbersHtml += '<h5 style="margin-bottom: 10px; color: #0c5aa6;"><strong>Extra Predicted Numbers:</strong></h5>';
            numbersHtml += '<div class="extra-predicted-numbers-list">';
            
            if (response.filter.extra_balls) {
                if (response.filter.extra_balls === 'ALL') {
                    // Display all possible extra numbers from occurrences data
                    if (response.extra_ball_occurrences && response.extra_ball_occurrences.length > 0) {
                        // Sort extra ball numbers numerically
                        var sortedExtraBalls = response.extra_ball_occurrences.slice().sort(function(a, b) {
                            return parseInt(a.value) - parseInt(b.value);
                        });
                        
                        for (var k = 0; k < sortedExtraBalls.length; k++) {
                            var occurrence = sortedExtraBalls[k];
                            var extraNumber = occurrence.value;
                            var extraClassName = 'combination-number';
                            
                            if (bonusNumbers.indexOf(extraNumber.toString()) !== -1) {
                                extraClassName += ' bonus-number-match';
                            }
                            
                            numbersHtml += '<span class="' + extraClassName + '" style="margin-right: 8px;">' + String(extraNumber).padStart(2, '0') + '</span>';
                        }
                    } else {
                        numbersHtml += '<span style="color: #6c757d; font-style: italic;">All extra numbers included</span>';
                    }
                } else {
                    var extraNumbers = response.filter.extra_balls.split(',');
                    for (var j = 0; j < extraNumbers.length; j++) {
                        var extraNumber = extraNumbers[j].trim();
                        var extraClassName = 'combination-number';
                        
                        if (bonusNumbers.indexOf(extraNumber) !== -1) {
                            extraClassName += ' bonus-number-match';
                        }
                        
                        numbersHtml += '<span class="' + extraClassName + '" style="margin-right: 8px;">' + String(extraNumber).padStart(2, '0') + '</span>';
                    }
                }
            } else {
                numbersHtml += '<span style="color: #6c757d; font-style: italic;">No extra numbers predicted</span>';
            }
            
            numbersHtml += '</div>';
            
        } else {
            // Regular Lottery - Combined Predicted Numbers
            numbersHtml += '<h5 style="margin-bottom: 10px; color: #0c5aa6;"><strong>Predicted Numbers:</strong></h5>';
            numbersHtml += '<div class="predicted-numbers-list">';
            
            for (var i = 0; i < predictedNumbers.length; i++) {
                var number = predictedNumbers[i].trim();
                var className = 'combination-number';
                
                if (drawnNumbers.indexOf(number) !== -1) {
                    className += ' winning-number';
                } else if (bonusNumbers.indexOf(number) !== -1) {
                    className += ' bonus-number-match';
                }
                
                numbersHtml += '<span class="' + className + '" style="margin-right: 8px;">' + String(number).padStart(2, '0') + '</span>';
            }
            
            numbersHtml += '</div>';
        }
        
        // Add (TBD) indicator if draw is not available
        if (response.display_mode === 'tbd') {
            numbersHtml += '<span style="margin-left: 15px; color: #6c757d; font-style: italic;">(TBD)</span>';
        }
        
        predictedContainer.html(numbersHtml);
    }
    
    function getResultSortValue(category) {
        // Extract number of matches from category string
        var matches = 0;
        var matchResult = category.match(/(\d+)\s+Match/);
        if (matchResult) {
            matches = parseInt(matchResult[1]);
        }
        
        // Base sort values for different categories
        var baseSortValues = {
            'Jackpot Winner': 1000,
            'Major Winner': 900,
            'Minor Winner': 800,
            'Bonus Winner': 700,
            'Not a Winner': 100,
            'No Win': 100,
            'TBD (To Be Determined)': 50,
            'Expired': 10
        };
        
        // Get base value for the category
        var baseValue = 0;
        for (var key in baseSortValues) {
            if (category.includes(key) || category === key) {
                baseValue = baseSortValues[key];
                break;
            }
        }
        
        // For "Not a Winner" categories with matches, add the number of matches
        // This ensures 2 Matches (Not a Winner) > 1 Matches (Not a Winner)
        if (category.includes('Not a Winner') && matches > 0) {
            return baseValue + matches;
        }
        
        // For other categories, return base value plus matches for fine-tuning
        return baseValue + matches;
    }
});
</script>