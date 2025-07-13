<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url('application/views/admin/prize/prize_history.css'); ?>">

<!-- Loading Overlay -->
<div id="prizeHistoryLoader" class="prize-loading-overlay">
    <div class="prize-loading-content">
        <div class="prize-loading-text">
            <h4>Calculating and Loading Prize History</h4>
        </div>
        <div class="prize-progress-bar">
            <div class="prize-progress-fill"></div>
        </div>
    </div>
</div>

<style>
    /* Loading Overlay Styles */
    .prize-loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.95);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(3px);
    }
    
    .prize-loading-content {
        text-align: center;
        background: white;
        padding: 2rem 3rem;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        border: 1px solid #e0e0e0;
        min-width: 300px;
    }
    
    .prize-loading-text h4 {
        color: #333;
        margin-bottom: 1.5rem;
        font-weight: 500;
        font-size: 1.1rem;
    }
    
    .prize-progress-bar {
        width: 100%;
        height: 6px;
        background-color: #f0f0f0;
        border-radius: 3px;
        overflow: hidden;
        position: relative;
    }
    
    .prize-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #007bff, #0056b3);
        width: 0%;
        border-radius: 3px;
        animation: simpleProgress 2s ease-in-out infinite;
        position: relative;
    }
    
    .prize-progress-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        animation: shimmer 1.5s ease-in-out infinite;
    }
    
    @keyframes simpleProgress {
        0% { width: 0%; }
        50% { width: 70%; }
        100% { width: 100%; }
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    /* Page Loading Overlay for AJAX */
    .page-loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.3);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .page-loading-content {
        background: white;
        padding: 1.5rem 2rem;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        color: #333;
        font-size: 0.9rem;
    }
    
    .page-loading-content i {
        color: #007bff;
    }

    .card {
        background-color: #ffffff;
        border: 1px solid rgba(0, 34, 51, 0.1);
        box-shadow: 2px 4px 10px 0 rgba(0, 34, 51, 0.05), 2px 4px 10px 0 rgba(0, 34, 51, 0.05);
        border-radius: 0.25rem;
        padding: 0px;
        max-width: 100%;
        width: 100%;
        box-sizing: border-box;
    }
    .card-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #333333;
    }
    .card-body {
        width: 100%;
        box-sizing: border-box;
        padding: 1.5rem;
    }
    /* Reduce table width and font size to fit in card */
    #prizeHistoryTable {
        font-size: 0.85em;
        margin: 0 auto;
        max-width: 100%;
    }
    #prizeHistoryTable th,
    #prizeHistoryTable td {
        padding: 0.4rem 0.3rem;
        text-align: center;
        white-space: nowrap;
    }
    #prizeHistoryTable th {
        font-size: 0.8em;
        font-weight: bold;
    }
    /* Specific column widths for better fit */
    #prizeHistoryTable th:nth-child(1) { width: 30px; }     /* # */
    #prizeHistoryTable th:nth-child(2) { width: 100px; }    /* Original */
    #prizeHistoryTable th:nth-child(3) { width: 50px; }     /* Picks */
    #prizeHistoryTable th:nth-child(4) { width: 120px; }    /* Original Combinations */
    #prizeHistoryTable th:nth-child(5) { width: 100px; }    /* Saved */
    #prizeHistoryTable th:nth-child(6) { width: 120px; }    /* Actual Filtered Combinations */
    #prizeHistoryTable th:nth-child(7) { width: 80px; }     /* Active */
    #prizeHistoryTable th:nth-child(8) { width: 90px; }     /* Last Date */
    /* Win record columns */
    .win-record-col {
        width: 30px !important;
        min-width: 30px;
        font-size: 0.75em;
    }
    .table-responsive {
        overflow-x: auto;
        margin: 0;
    }
    /* Pagination controls styling */
    .pagination-controls {
        margin-bottom: 1rem;
    }
    .pagination-info {
        font-size: 0.9rem;
        color: #666;
    }
    /* Enhanced Status Tags */
    .status-tag {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75em;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid;
        min-width: 60px;
        text-align: center;
    }
    .status-active {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }
    .status-expired {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
    .status-generated {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-dollar"></i> Prize History Win Records for: <?php echo isset($lottery) ? $lottery->lottery_name : 'Unknown Lottery'; ?>
        </h1>
        <h5 style="text-align:left"><?php echo anchor('admin/predictions', 'Back to Predictions Dashboard', 'title="Back to Predictions"'); ?></h5>
    </section>

    <section class="content">
        <div class="container mt-4">
            <!-- White Card -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-center">
                        <i class="fa fa-table"></i> Prize History
                    </h3>
                    
                    <!-- Pagination Controls -->
                    <div class="pagination-controls">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="per_page_select">Combination Table per page:</label>
                                    <select id="per_page_select" class="form-control" style="width: auto; display: inline-block;">
                                        <?php foreach($pagination_options as $option): ?>
                                            <option value="<?php echo $option; ?>" <?php echo ($per_page == $option) ? 'selected' : ''; ?>>
                                                <?php echo $option; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="pagination-info">
                                    <strong>Drawn:</strong> <?php echo !empty($prize_records) ? number_format($prize_records[0]->R) : '0'; ?><br>
                                    <strong>Extra Included:</strong> <?php echo $extra_ball_included; ?>
                                </div>
                            </div>
                            <div class="col-sm-4 text-right">
                                <div class="pagination-info">
                                    Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $per_page, $total_records); ?> 
                                    of <?php echo $total_records; ?> entries
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prize History Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="prizeHistoryTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Original</th>
                                    <th>Picks</th>
                                    <th>Original Combinations</th>
                                    <th>Saved</th>
                                    <th>Actual Filtered Combinations</th>
                                    <th>Active</th>
                                    <th>Last Date</th>
                                    <?php $prize_columns = isset($prize_columns) ? $prize_columns : array(); ?>
                                    <th colspan="<?php echo max(1, count($prize_columns)); ?>" class="text-center" style="background-color: #f4f4f4;">
                                        <strong>Win Record</strong>
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="8"></th>
                                    <!-- Dynamic Win Record Sub-headers -->
                                    <?php if(!empty($prize_columns)): ?>
                                        <?php foreach($prize_columns as $column): ?>
                                            <th class="text-center win-record-col" 
                                                style="background-color: #e8f5e8;" 
                                                title="<?php echo htmlspecialchars($column['tooltip']); ?>">
                                                <?php echo htmlspecialchars($column['label']); ?>
                                            </th>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <th class="text-center" style="background-color: #e8f5e8;">
                                            No Prize Categories
                                        </th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                                <tbody>
                                    <?php if(empty($prize_records)): ?>
                                        <tr>
                                            <td colspan="<?php echo 8 + max(1, count($prize_columns)); ?>" class="text-center">
                                                <em>No prize history records found for this administrator.</em>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($prize_records as $record): ?>
                                            <tr>
                                                <td><?php echo sprintf('%02d', $record->row_number); ?></td>
                                                <td><?php echo htmlspecialchars($record->original_filename); ?></td>
                                                <td class="text-center"><?php echo sprintf('%02d', $record->N); ?></td>
                                                <td class="text-center"><?php echo number_format($record->original_cccc); ?></td>
                                                <td><?php echo htmlspecialchars($record->saved_filename); ?></td>
                                                <td class="text-center"><?php echo number_format($record->actual_cccc); ?></td>
                                                <td class="text-center">
                                                    <?php if($record->is_active == 'YES'): ?>
                                                        <span class="status-tag status-active">ACTIVE</span>
                                                    <?php else: ?>
                                                        <span class="status-tag status-expired">EXPIRED</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('D M j, Y', strtotime($record->lastdate)); ?></td>
                                                
                                                <!-- Dynamic Win Record Columns -->
                                                <?php if(!empty($prize_columns)): ?>
                                                    <?php foreach($prize_columns as $column): ?>
                                                        <td class="text-center win-record">
                                                            <?php 
                                                            $key = $column['key'];
                                                            echo property_exists($record->win_records, $key) ? 
                                                                 $record->win_records->$key : '0'; 
                                                            ?>
                                                        </td>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <td class="text-center win-record">-</td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if($total_pages > 1): ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="text-center">
                                        <ul class="pagination">
                                            <!-- First Page -->
                                            <?php if($current_page > 1): ?>
                                                <li><a href="#" data-page="1">&laquo;&laquo;</a></li>
                                                <li><a href="#" data-page="<?php echo $current_page - 1; ?>">&laquo;</a></li>
                                            <?php endif; ?>
                                            
                                            <!-- Page Numbers -->
                                            <?php 
                                            $start_page = max(1, $current_page - 2);
                                            $end_page = min($total_pages, $current_page + 2);
                                            
                                            for($i = $start_page; $i <= $end_page; $i++): 
                                            ?>
                                                <li class="<?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                                    <a href="#" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endfor; ?>
                                            
                                            <!-- Show ellipsis if needed -->
                                            <?php if($end_page < $total_pages): ?>
                                                <?php if($end_page < $total_pages - 1): ?>
                                                    <li class="disabled"><span>...</span></li>
                                                <?php endif; ?>
                                                <li><a href="#" data-page="<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a></li>
                                            <?php endif; ?>
                                            
                                            <!-- Next Page -->
                                            <?php if($current_page < $total_pages): ?>
                                                <li><a href="#" data-page="<?php echo $current_page + 1; ?>">&raquo;</a></li>
                                                <li><a href="#" data-page="<?php echo $total_pages; ?>">&raquo;&raquo;</a></li>
                                            <?php endif; ?>
                                        </ul>
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
.win-record {
    font-family: monospace;
    font-weight: bold;
    background-color: #f9f9f9;
}

.table th {
    background-color: #f4f4f4;
    font-weight: bold;
}

.pagination {
    margin: 20px 0;
}

.pagination-info {
    margin-top: 8px;
    font-size: 13px;
    color: #777;
}

.label-success {
    background-color: #5cb85c;
}

.label-danger {
    background-color: #d9534f;
}

/* Responsive table improvements */
@media (max-width: 768px) {
    .table-responsive {
        border: none;
    }
    
    .win-record {
        font-size: 11px;
    }
}
</style>

<script>
// Prize History Loading Animation
$(document).ready(function() {
    var loader = $('#prizeHistoryLoader');
    
    // Hide loader when page is fully loaded
    $(window).on('load', function() {
        setTimeout(function() {
            loader.fadeOut(500);
        }, 800); // Small delay to ensure content is ready
    });
    
    // Fallback: hide loader after reasonable time
    setTimeout(function() {
        if (loader.is(':visible')) {
            loader.fadeOut(500);
        }
    }, 4000); // 4 second maximum

    // Original Prize History JavaScript
    // Handle per page change
    $('#per_page_select').change(function() {
        var per_page = $(this).val();
        loadPage(1, per_page);
    });
    
    // Handle pagination clicks
    $('.pagination a').click(function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        if (page) {
            loadPage(page, $('#per_page_select').val());
        }
    });
    
    function loadPage(page, per_page) {
        var offset = (page - 1) * per_page;
        
        // Show simple loading overlay
        var pageLoader = $('<div class="page-loading-overlay"><div class="page-loading-content"><i class="fa fa-spinner fa-spin fa-lg"></i><p style="margin-top: 0.5rem; margin-bottom: 0;">Updating...</p></div></div>');
        $('body').append(pageLoader);
        
        // Show loading indicator in table
        var totalCols = 9 + <?php echo max(1, count($prize_columns)); ?>;
        $('#prizeHistoryTable tbody').html('<tr><td colspan="' + totalCols + '" class="text-center"><i class="fa fa-spinner fa-spin"></i> Calculating and Loading prize Data...</td></tr>');
        
        $.ajax({
            url: '<?php echo site_url("admin/prize/get_table_data"); ?>',
            type: 'POST',
            data: {
                per_page: per_page,
                offset: offset
            },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    alert(response.error);
                    pageLoader.remove();
                    return;
                }
                
                // Update URL parameters
                var url = new URL(window.location);
                url.searchParams.set('per_page', per_page);
                url.searchParams.set('offset', offset);
                window.history.pushState({}, '', url);
                
                // Reload the page to show updated data
                location.reload();
            },
            error: function() {
                pageLoader.remove();
                alert('Error loading data. Please try again.');
                location.reload();
            }
        });
    }
});
</script>
