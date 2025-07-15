<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url('application/views/admin/prize/prize_history.css'); ?>">

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-list"></i> Combination Ticket Winner Table
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
                            <div class="col-md-12">
                                <div class="draw-header-box">
                                    <h4><strong>Draw Date:</strong> <?php echo date('l j F, Y', strtotime($draw_info->draw_date)); ?></h4>
                                    <div class="drawn-numbers">
                                        <strong>Drawn Numbers:</strong>
                                        <?php 
                                        // Display drawn numbers
                                        for ($i = 1; $i <= $filter->N; $i++) {
                                            $ball_field = 'ball' . $i;
                                            if (property_exists($draw_info, $ball_field)) {
                                                echo '<span class="drawn-number">' . sprintf('%02d', $draw_info->$ball_field) . '</span>';
                                            }
                                        }
                                        
                                        // Display extra/bonus number if available
                                        if ($draw_info->extra_ball_included) {
                                            $bonus_fields = array('extra', 'bonus', 'extra_ball', 'bonus_ball', 'bonus_number');
                                            foreach ($bonus_fields as $field) {
                                                if (property_exists($draw_info, $field) && !is_null($draw_info->$field)) {
                                                    echo ' <strong>+</strong> <span class="bonus-number">' . sprintf('%02d', $draw_info->$field) . '</span>';
                                                    break;
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
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
                                <p><strong>Filtered:</strong> <?php echo number_format($filter->CCCC); ?> &nbsp;&nbsp;&nbsp; 
                                   <strong>Winning Tickets:</strong> <?php 
                                   $winning_count = 0;
                                   foreach ($tickets as $ticket) {
                                       if ($ticket['win_result']['matches'] > 0 || $ticket['win_result']['bonus_match']) {
                                           $winning_count++;
                                       }
                                   }
                                   echo $winning_count;
                                   ?> Tickets</p>
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

                    <!-- Combination Tickets Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="combinationTicketsTable">
                            <thead>
                                <tr>
                                    <th style="width: 8%;">##</th>
                                    <th style="width: 60%;">Combination</th>
                                    <th style="width: 32%;" class="text-center">Check Results</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($tickets)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            <em>No combination tickets found.</em>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($tickets as $ticket): ?>
                                        <tr class="<?php echo ($filter->active == 0) ? 'expired-row' : ''; ?>">
                                            <td class="text-center">
                                                <?php echo sprintf('%02d', $ticket['ticket_number']); ?>
                                            </td>
                                            <td class="combination-numbers">
                                                <?php 
                                                foreach($ticket['numbers'] as $number) {
                                                    $is_winning = false;
                                                    $is_bonus = false;
                                                    
                                                    // Check if this number matches any drawn numbers
                                                    if ($draw_info && $filter->active == 1) {
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
                                                    if ($filter->active == 1) {
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
                                                <?php if ($filter->active == 0): ?>
                                                    <span class="result-expired">EXPIRED</span>
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
                    <?php if($total_pages > 1): ?>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="pagination-info">
                                    Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $per_page, $total_tickets); ?> 
                                    of <?php echo $total_tickets; ?> entries
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-center">
                                    <div class="pagination-wrapper">
                                        <span class="pagination-nav">&laquo;&laquo;</span>
                                        <?php 
                                        $start_page = max(1, $current_page - 2);
                                        $end_page = min($total_pages, $current_page + 2);
                                        
                                        for($i = $start_page; $i <= $end_page; $i++): 
                                        ?>
                                            <span class="pagination-page <?php echo ($i == $current_page) ? 'active' : ''; ?>" data-page="<?php echo $i; ?>">
                                                <?php echo $i; ?>
                                            </span>
                                        <?php endfor; ?>
                                        <span class="pagination-nav">&raquo;&raquo;</span>
                                    </div>
                                    <div class="page-info">
                                        Page <?php echo $current_page; ?> of <?php echo $total_pages; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* Draw Information Styling */
.draw-info-header {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 20px;
}

.draw-header-box h4 {
    margin: 0 0 10px 0;
    color: #333;
}

.drawn-numbers {
    font-size: 16px;
    font-weight: bold;
}

.drawn-number {
    display: inline-block;
    background-color: #007bff;
    color: white;
    padding: 5px 10px;
    margin: 2px;
    border-radius: 50%;
    min-width: 35px;
    text-align: center;
    font-weight: bold;
}

.bonus-number {
    display: inline-block;
    background-color: #dc3545;
    color: white;
    padding: 5px 10px;
    margin: 2px;
    border-radius: 50%;
    min-width: 35px;
    text-align: center;
    font-weight: bold;
}

/* Table Information Styling */
.table-info-header {
    margin-bottom: 20px;
    padding: 15px;
    background-color: #e8f5e8;
    border-radius: 5px;
}

.table-info-header h4 {
    margin: 0 0 5px 0;
    color: #2d5016;
}

.table-info-header p {
    margin: 0;
    color: #2d5016;
    font-weight: bold;
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
    // Handle per page change
    $('#per_page_select').change(function() {
        var per_page = $(this).val();
        loadPage(1, per_page);
    });
    
    // Handle pagination clicks
    $('.pagination-page').click(function() {
        var page = $(this).data('page');
        if (page && !$(this).hasClass('active')) {
            loadPage(page, $('#per_page_select').val());
        }
    });
    
    function loadPage(page, per_page) {
        var url = new URL(window.location);
        url.searchParams.set('per_page', per_page);
        url.searchParams.set('page', page);
        window.location.href = url.toString();
    }
});
</script>
