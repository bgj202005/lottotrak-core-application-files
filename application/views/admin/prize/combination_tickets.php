<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url('application/views/admin/prize/prize_history.css'); ?>">

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-list"></i> Combination Ticket Winners
        </h1>
        <h5 style="text-align:left">
            <?php echo anchor('admin/prize/index/'.$filter->lottery_id, 'Back to Prize History', 'title="Back to Prize History"'); ?>
        </h5>
    </section>

    <section class="content">
        <div class="container mt-4">
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
                                                <span class="tbd-display">TBD (To Be Determined)</span>
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
                    <?php endif; ?>
                    
                    <!-- Table Information -->
                    <div class="table-info-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h4><strong>Saved Combination Table:</strong> <?php echo htmlspecialchars($filter->file_name); ?></h4>
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
                                <!-- Combinations per page selector -->
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

                    <!-- Combination Tickets Table Container -->
                    <div id="tickets-container">
                        <!-- Loading indicator -->
                        <div id="loading-indicator" style="display: none; text-align: center; padding: 20px;">
                            <i class="fa fa-spinner fa-spin fa-2x"></i>
                            <p>Loading combination tickets...</p>
                        </div>
                        
                        <!-- Combination Tickets Table -->
                        <div class="table-responsive" id="tickets-table">
                            <table class="table table-bordered table-striped" id="combinationTicketsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 8%;">##</th>
                                        <th style="width: 60%;">Combination</th>
                                        <th style="width: 32%;" class="text-center">Check Results</th>
                                    </tr>
                                </thead>
                                <tbody id="tickets-tbody">
                                    <?php if(empty($tickets)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center">
                                                <em>No combination tickets found.</em>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $base_offset = isset($offset) ? $offset : 0;
                                        foreach($tickets as $index => $ticket): 
                                            $current_row_number = $base_offset + $index + 1;
                                        ?>
                                            <tr class="<?php echo ($filter->active == 0) ? 'expired-row' : ''; ?>">
                                                <td class="text-center">
                                                    <?php echo sprintf('%02d', $current_row_number); ?>
                                                </td>
                                                <td class="combination-numbers">
                                                    <?php 
                                                    foreach($ticket['numbers'] as $number) {
                                                        $is_winning = false;
                                                        $is_bonus = false;
                                                        
                                                        // Check if this number matches any drawn numbers
                                                        if ($draw_info && $filter->active == 1 && (!isset($display_mode) || $display_mode != 'tbd')) {
                                                            for ($i = 1; $i <= $filter->N; $i++) {
                                                                $ball_field = 'ball' . $i;
                                                                if (property_exists($draw_info, $ball_field) && $draw_info->$ball_field == $number) {
                                                                    $is_winning = true;
                                                                    break;
                                                                }
                                                            }
                                                            
                                                            // Check bonus number
                                                            if (!$is_winning && $draw_info->extra_ball_included) {
                                                                $bonus_fields = array('extra', 'bonus', 'extra_ball', 'bonus_ball', 'bonus_number');
                                                                foreach ($bonus_fields as $field) {
                                                                    if (property_exists($draw_info, $field) && $draw_info->$field == $number) {
                                                                        $is_bonus = true;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        
                                                        $number_class = '';
                                                        if ($filter->active == 1 && (!isset($display_mode) || $display_mode != 'tbd')) {
                                                            if ($is_winning) {
                                                                $number_class = 'winning-number';
                                                            } elseif ($is_bonus) {
                                                                $number_class = 'bonus-number-match';
                                                            }
                                                        }
                                                        
                                                        echo '<span class="combination-number ' . $number_class . '">' . sprintf('%02d', $number) . '</span> ';
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-center check-results">
                                                    <?php if(isset($display_mode) && $display_mode == 'tbd'): ?>
                                                        <span class="check-result-tbd">TBD (To Be Determined)</span>
                                                    <?php else: ?>
                                                        <span class="result-<?php echo $ticket['win_result']['color_class']; ?>">
                                                            <?php echo $ticket['win_result']['category']; ?>
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
    padding: 3px 8px;
    margin: 1px;
    border-radius: 3px;
    min-width: 25px;
    text-align: center;
    font-weight: bold;
    border: 1px solid #dee2e6;
}

.winning-number {
    background-color: #28a745 !important;
    color: white !important;
    border-color: #28a745 !important;
}

.bonus-number-match {
    background-color: #ffc107 !important;
    color: #333 !important;
    border-color: #ffc107 !important;
}

/* Result Styling */
.check-results {
    font-weight: bold;
}

.result-jackpot-win {
    color: #28a745;
    font-weight: bold;
    font-size: 14px;
}

.result-major-win {
    color: #17a2b8;
    font-weight: bold;
}

.result-minor-win {
    color: #ffc107;
    font-weight: bold;
}

.result-bonus-win {
    color: #6f42c1;
    font-weight: bold;
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
        min-width: 20px;
        padding: 2px 5px;
        font-size: 11px;
    }
    
    .table-responsive {
        border: none;
    }
}
</style>

<script>
$(document).ready(function() {
    var filterId = <?php echo $filter->id; ?>;
    var currentPage = <?php echo $current_page; ?>;
    var totalPages = <?php echo $total_pages; ?>;
    
    // Handle per page change
    $('#per_page_select').change(function() {
        var per_page = $(this).val();
        loadPage(1, per_page);
    });
    
    // Handle pagination clicks
    $(document).on('click', '.pagination-page, .pagination-nav', function() {
        var page = $(this).data('page');
        if (page && page != currentPage && page >= 1 && page <= totalPages) {
            loadPage(page, $('#per_page_select').val());
        }
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
                per_page: per_page
            },
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
                    alert('Error loading tickets: ' + response.message);
                }
                
                // Hide loading indicator
                $('#loading-indicator').hide();
                $('#tickets-table').show();
                $('#pagination-container').show();
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Error loading tickets. Please try again.');
                
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
            tbody.append('<tr><td colspan="3" class="text-center"><em>No combination tickets found.</em></td></tr>');
            return;
        }
        
        // Calculate proper row numbers based on pagination
        var baseOffset = response.pagination ? (response.pagination.offset || 0) : 0;
        
        $.each(tickets, function(index, ticket) {
            var currentRowNumber = baseOffset + index + 1;
            var row = '<tr class="' + (filter.active == 0 ? 'expired-row' : '') + '">';
            row += '<td class="text-center">' + String(currentRowNumber).padStart(2, '0') + '</td>';
            row += '<td class="combination-numbers">';
            
            // Add combination numbers with highlighting
            $.each(ticket.numbers, function(i, number) {
                var numberClass = '';
                var isWinning = false;
                var isBonus = false;
                
                if (draw_info && filter.active == 1 && response.display_mode !== 'tbd') {
                    // Check if number matches drawn numbers
                    for (var j = 1; j <= filter.N; j++) {
                        var ballField = 'ball' + j;
                        if (draw_info[ballField] && draw_info[ballField] == number) {
                            isWinning = true;
                            break;
                        }
                    }
                    
                    // Check bonus number
                    if (!isWinning && draw_info.extra_ball_included) {
                        var bonusFields = ['extra', 'bonus', 'extra_ball', 'bonus_ball', 'bonus_number'];
                        for (var k = 0; k < bonusFields.length; k++) {
                            if (draw_info[bonusFields[k]] && draw_info[bonusFields[k]] == number) {
                                isBonus = true;
                                break;
                            }
                        }
                    }
                }
                
                if (filter.active == 1 && response.display_mode !== 'tbd') {
                    if (isWinning) {
                        numberClass = 'winning-number';
                    } else if (isBonus) {
                        numberClass = 'bonus-number-match';
                    }
                }
                
                row += '<span class="combination-number ' + numberClass + '">' + String(number).padStart(2, '0') + '</span> ';
            });
            
            row += '</td>';
            row += '<td class="text-center check-results">';
            
            // Check for TBD display mode
            if (response.display_mode === 'tbd') {
                row += '<span class="check-result-tbd">TBD (To Be Determined)</span>';
            } else {
                row += '<span class="result-' + ticket.win_result.color_class + '">' + ticket.win_result.category + '</span>';
            }
            
            row += '</td>';
            row += '</tr>';
            
            tbody.append(row);
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
        if (response.display_mode === 'tbd' && response.next_draw_date) {
            // Update to show next draw date and TBD
            var nextDrawDate = new Date(response.next_draw_date);
            var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            var formattedDate = nextDrawDate.toLocaleDateString('en-US', options);
            
            $('.draw-header-box h4').html('<strong>Next Draw Date:</strong> ' + formattedDate);
            $('.drawn-numbers-display').html('<span class="tbd-display">TBD (To Be Determined)</span>');
        } else if (response.draw_info) {
            // Update to show actual draw results
            var drawDate = new Date(response.draw_info.draw_date);
            var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            var formattedDate = drawDate.toLocaleDateString('en-US', options);
            
            $('.draw-header-box h4').html('<strong>Draw Date:</strong> ' + formattedDate);
            
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
    }
});
</script>
