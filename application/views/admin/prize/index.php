<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url('application/views/admin/prize/prize_history.css'); ?>">

<style>
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
                                    <th colspan="<?php echo max(1, count($prize_columns) + 1); ?>" class="text-center" style="background-color: #f4f4f4;">
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
                                        <th class="text-center" style="background-color: #f8d7da;">
                                            Reset
                                        </th>
                                    <?php else: ?>
                                        <th class="text-center" style="background-color: #e8f5e8;">
                                            No Prize Categories
                                        </th>
                                        <th class="text-center" style="background-color: #f8d7da;">
                                            Reset
                                        </th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                                <tbody>
                                    <?php if(empty($prize_records)): ?>
                                        <tr>
                                            <td colspan="<?php echo 8 + max(1, count($prize_columns)) + 1; ?>" class="text-center">
                                                <em>No prize history records found for this administrator.</em>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($prize_records as $record): ?>
                                            <tr>
                                                <td>
                                                    <button type="button" class="btn btn-link combination-link p-0" 
                                                            data-filter-id="<?php echo $record->id; ?>" 
                                                            data-filename="<?php echo htmlspecialchars($record->saved_filename); ?>"
                                                            onclick="handleCombinationClick(<?php echo $record->id; ?>, '<?php echo htmlspecialchars($record->saved_filename); ?>')"
                                                            style="color: #007bff; font-weight: bold; text-decoration: none;">
                                                        <?php echo sprintf('%02d', $record->row_number); ?>
                                                    </button>
                                                </td>
                                                <td><?php echo htmlspecialchars($record->original_filename); ?></td>
                                                <td class="text-center"><?php echo sprintf('%02d', $record->N); ?></td>
                                                <td class="text-center"><?php echo number_format($record->original_cccc); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-link combination-link p-0" 
                                                            data-filter-id="<?php echo $record->id; ?>" 
                                                            data-filename="<?php echo htmlspecialchars($record->saved_filename); ?>"
                                                            onclick="handleCombinationClick(<?php echo $record->id; ?>, '<?php echo htmlspecialchars($record->saved_filename); ?>')"
                                                            style="color: #007bff; font-weight: bold; text-decoration: none;">
                                                        <?php echo htmlspecialchars($record->saved_filename); ?>
                                                    </button>
                                                </td>
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
                                                    <!-- Reset Button Column -->
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-warning reset-win-record" 
                                                                data-filter-id="<?php echo $record->id; ?>" 
                                                                data-filename="<?php echo htmlspecialchars($record->saved_filename); ?>"
                                                                title="Reset win record for this filename">
                                                            <i class="fa fa-undo fa-lg" aria-hidden="true"></i> reset
                                                        </button>
                                                    </td>
                                                <?php else: ?>
                                                    <td class="text-center win-record">-</td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-warning reset-win-record" 
                                                                data-filter-id="<?php echo $record->id; ?>" 
                                                                data-filename="<?php echo htmlspecialchars($record->saved_filename); ?>"
                                                                title="Reset win record for this filename">
                                                            <i class="fa fa-undo fa-lg" aria-hidden="true"></i> reset
                                                        </button>
                                                    </td>
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

/* Clickable combination links */
.combination-link {
    color: #007bff !important;
    text-decoration: none !important;
    font-weight: bold !important;
    cursor: pointer !important;
    border: none !important;
    background: none !important;
    padding: 0 !important;
}

.combination-link:hover {
    color: #0056b3 !important;
    text-decoration: underline !important;
}

.combination-link:visited {
    color: #007bff !important;
}

.combination-link:focus, .combination-link:active {
    color: #0056b3 !important;
    text-decoration: none !important;
    box-shadow: none !important;
    outline: none !important;
}

/* Reset button styling */
.reset-win-record {
    padding: 4px 8px;
    font-size: 11px;
    background-color: #ffc107;
    border-color: #ffc107;
    color: #212529;
}

.reset-win-record:hover {
    background-color: #e0a800;
    border-color: #d39e00;
    color: #212529;
}

/* Responsive table improvements */
@media (max-width: 768px) {
    .table-responsive {
        border: none;
    }
    
    .win-record {
        font-size: 11px;
    }
    
    .reset-win-record {
        font-size: 9px;
        padding: 2px 4px;
    }
}

/* Combination Tickets Styling */
#combinationTicketsContainer {
    animation: slideDown 0.5s ease-in-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#combinationTicketsTable {
    font-size: 0.9em;
}

#combinationTicketsTable .badge {
    font-size: 0.75em;
    padding: 0.25em 0.6em;
}

.table-success {
    background-color: rgba(40, 167, 69, 0.1) !important;
}

.combination-numbers {
    font-family: 'Courier New', monospace;
    font-weight: bold;
    letter-spacing: 1px;
}
</style>

<script>
// Check if jQuery is loaded
if (typeof jQuery === 'undefined') {
    console.error('jQuery is not loaded!');
} else {
    console.log('jQuery version:', jQuery.fn.jquery);
    console.log('jQuery AJAX support:', typeof jQuery.ajax !== 'undefined');
}

// Check if Bootstrap is loaded
if (typeof jQuery === 'undefined' || typeof jQuery.fn.modal === 'undefined') {
    console.error('Bootstrap JavaScript is not loaded properly!');
} else {
    console.log('Bootstrap modal support: Available');
}

$(document).ready(function() {
    console.log('Prize History page loaded, setting up event handlers...'); // Debug log
    
    // Test if elements exist
    console.log('Found combination links:', $('.combination-link').length);
    console.log('Found reset buttons:', $('.reset-win-record').length);
    
    // Log all combination links and their data
    $('.combination-link').each(function(index) {
        console.log('Link ' + index + ':', {
            element: this,
            filterId: $(this).data('filter-id'),
            filename: $(this).data('filename'),
            text: $(this).text()
        });
    });
    
    // Handle reset win record clicks
    $(document).on('click', '.reset-win-record', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var filterId = $(this).data('filter-id');
        var filename = $(this).data('filename');
        
        console.log('Reset button clicked:', filterId, filename); // Debug log
        
        if (confirm('Are you sure you want to reset the win record for ' + filename + '?')) {
            resetWinRecord(filterId, filename);
        }
        
        return false;
    });
    
    // Handle combination links (row numbers and saved filenames)
    $(document).on('click', '.combination-link', function(e) {
        console.log('Combination link click event fired!'); // Debug log
        e.preventDefault();
        e.stopPropagation();
        
        var $this = $(this);
        var filterId = $this.data('filter-id');
        var filename = $this.data('filename');
        
        console.log('Combination link clicked:', {
            element: this,
            filterId: filterId,
            filename: filename,
            dataAttributes: $this.data()
        });
        
        if (!filterId || !filename) {
            console.error('Missing data attributes:', filterId, filename);
            alert('Error: Missing filter data. Please refresh the page and try again.');
            return false;
        }
        
        showProgressBar(filename);
        checkResults(filterId, filename);
        
        return false; // Ensure no default behavior
    });
    
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
        var totalCols = 9 + <?php echo max(1, count($prize_columns)) + 1; ?>;
        $('#prizeHistoryTable tbody').html('<tr><td colspan="' + totalCols + '" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading Prize History Data...</td></tr>');
        
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

// Global functions for inline handlers and jQuery events
function resetWinRecord(filterId, filename) {
    console.log('resetWinRecord called with:', filterId, filename);
    
    $.ajax({
        url: '<?php echo site_url("admin/prize/reset_win_record"); ?>',
        type: 'POST',
        data: {
            filter_id: filterId
        },
        dataType: 'json',
        beforeSend: function() {
            console.log('Sending reset request...');
        },
        success: function(response) {
            console.log('Reset response:', response);
            if (response.success) {
                // Show success message
                showMessage(response.message, 'success');
                
                // Reload the page to show updated data
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Reset AJAX Error:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            alert('Error resetting win record. Server responded with: ' + xhr.status + ' ' + xhr.statusText);
        }
    });
}

function showProgressBar(filename) {
    // Remove any existing progress modal
    $('#progressModal').remove();
    
    // Create progress modal using Bootstrap 4 syntax
    var progressModal = $('<div class="modal fade" id="progressModal" tabindex="-1" role="dialog" aria-labelledby="progressModalLabel" aria-hidden="true">' +
        '<div class="modal-dialog modal-dialog-centered" role="document">' +
        '<div class="modal-content">' +
        '<div class="modal-header">' +
        '<h5 class="modal-title" id="progressModalLabel">Checking Results</h5>' +
        '</div>' +
        '<div class="modal-body text-center">' +
        '<h6>Checking Results for <strong>' + filename + '</strong></h6>' +
        '<div class="progress" style="height: 25px; margin-top: 20px;">' +
        '<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">' +
        '<span class="sr-only">0% Complete</span>' +
        '</div>' +
        '</div>' +
        '<p class="mt-2"><small class="text-muted">Please wait while we process your request...</small></p>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>');
    
    $('body').append(progressModal);
    
    // Show modal using Bootstrap 4 syntax
    $('#progressModal').modal({
        backdrop: 'static', 
        keyboard: false,
        show: true
    });
    
    // Animate progress bar
    var progress = 0;
    var progressInterval = setInterval(function() {
        progress += 15; // Faster progress
        $('#progressModal .progress-bar')
            .css('width', progress + '%')
            .attr('aria-valuenow', progress)
            .find('.sr-only').text(progress + '% Complete');
        
        if (progress >= 100) {
            clearInterval(progressInterval);
            // Keep modal open briefly to show completion
            setTimeout(function() {
                $('#progressModal').modal('hide');
                setTimeout(function() {
                    $('#progressModal').remove();
                }, 500);
            }, 800);
        }
    }, 120); // Slightly faster animation
}

function checkResults(filterId, filename) {
    console.log('checkResults called with:', filterId, filename);
    
    $.ajax({
        url: '<?php echo site_url("admin/prize/check_results_progress"); ?>',
        type: 'POST',
        data: {
            filter_id: filterId
        },
        dataType: 'json',
        beforeSend: function() {
            console.log('Sending check results request...');
        },
        success: function(response) {
            console.log('Check results response:', response);
            
            // Always hide and remove the progress modal first
            $('#progressModal').modal('hide');
            setTimeout(function() {
                $('#progressModal').remove();
                $('.modal-backdrop').remove(); // Force remove any stuck backdrop
                $('body').removeClass('modal-open'); // Remove modal-open class from body
            }, 500);
            
            if (response.success) {
                if (response.redirect) {
                    // Redirect to the combination tickets page
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 800); // Delay to ensure modal is fully removed
                } else if (response.data) {
                    // Display the combination ticket table below the prize history
                    setTimeout(function() {
                        displayCombinationTickets(response.data);
                    }, 800); // Delay to ensure modal is fully removed
                }
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Check Results AJAX Error:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            
            // Always clean up modal on error too
            $('#progressModal').modal('hide');
            setTimeout(function() {
                $('#progressModal').remove();
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            }, 500);
            
            alert('Error checking results. Server responded with: ' + xhr.status + ' ' + xhr.statusText);
        }
    });
}

function showMessage(message, type) {
    var alertClass = type === 'success' ? 'alert-warning' : 'alert-danger';
    var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible" style="position: fixed; top: 70px; right: 20px; z-index: 9999; min-width: 300px;">' +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        '</button>' +
        message +
        '</div>';
    
    $('body').append(alertHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}

// Inline function for combination clicks (fallback)
function handleCombinationClick(filterId, filename) {
    console.log('Inline combination click handler called:', filterId, filename);
    
    if (!filterId || !filename) {
        console.error('Missing parameters:', filterId, filename);
        alert('Error: Missing filter data. Please refresh the page and try again.');
        return false;
    }
    
    showProgressBar(filename);
    checkResults(filterId, filename);
}

function displayCombinationTickets(data) {
    console.log('Displaying combination tickets:', data);
    
    // Ensure any modal backdrop is removed immediately
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('#progressModal').remove();
    
    // Remove any existing combination table
    $('#combinationTicketsContainer').remove();
    
    // Create the combination tickets container
    var containerHtml = '<div id="combinationTicketsContainer" class="mt-4">' +
        '<div class="card shadow-sm">' +
        '<div class="card-body">' +
        '<h3 class="card-title text-center">' +
        '<i class="fa fa-list"></i> Combination Ticket Winners' +
        '</h3>' +
        
        // Draw Information Header
        '<div class="alert alert-info">' +
        '<div class="row">' +
        '<div class="col-md-6">' +
        '<strong>Filter:</strong> ' + data.filter.file_name + '<br>' +
        '<strong>Lottery:</strong> ' + data.filter.lottery_name + '<br>' +
        '<strong>Picks:</strong> ' + (data.filter.lottery_picks || data.filter.pick_count || data.filter.N) + ' numbers' +
        '</div>' +
        '<div class="col-md-6">' +
        '<strong>Total Tickets:</strong> ' + data.total_tickets.toLocaleString() + '<br>' +
        '<strong>Showing:</strong> First ' + data.showing_count + ' tickets<br>';
    
    // Add drawn numbers if available
    if (data.draw_info && data.drawn_numbers.length > 0) {
        containerHtml += '<strong>Drawn Numbers:</strong> ' + data.drawn_numbers.join(' ') + '';
    } else {
        containerHtml += '<strong>Status:</strong> <span class="text-warning">No recent draw data</span>';
    }
    
    containerHtml += '</div>' +
        '</div>';
    
    // Add debug information if no tickets found
    if (!data.tickets || data.tickets.length === 0) {
        var expectedPicks = data.filter.lottery_picks || data.filter.pick_count || data.filter.N;
        containerHtml += '<div class="alert alert-warning">' +
            '<strong>Debug Info:</strong><br>' +
            'Expected file path: <code>combinations/pick' + expectedPicks + '/' + data.filter.file_name + '.txt</code><br>' +
            'Lottery picks: ' + (data.filter.lottery_picks || 'N/A') + '<br>' +
            'File N value: ' + data.filter.N + '<br>' +
            'File name: ' + data.filter.file_name + '<br>' +
            '<small class="text-muted">Check server logs for more detailed file path information.</small>' +
            '</div>';
    }
    
    containerHtml += '</div>' +
        
        // Tickets Table
        '<div class="table-responsive">' +
        '<table class="table table-bordered table-striped table-sm" id="combinationTicketsTable">' +
        '<thead class="thead-light">' +
        '<tr>' +
        '<th style="width: 80px;">#</th>' +
        '<th>Combination Numbers</th>' +
        '<th style="width: 120px;">Win Result</th>' +
        '</tr>' +
        '</thead>' +
        '<tbody>';
    
    // Add ticket rows
    if (data.tickets && data.tickets.length > 0) {
        data.tickets.forEach(function(ticket, index) {
            var ticketNumbers = ticket.numbers ? ticket.numbers.map(function(num) {
                return String(num).padStart(2, '0');
            }).join(' ') : 'N/A';
            
            var winResult = ticket.win_result || 'No Match';
            
            // Add row styling based on win result
            var rowClass = '';
            var badgeClass = 'badge-secondary';
            
            if (winResult.includes('Matches') && !winResult.includes('No Match')) {
                rowClass = 'table-success';
                badgeClass = 'badge-success';
            } else if (winResult.includes('Extra Match')) {
                rowClass = 'table-warning';
                badgeClass = 'badge-warning';
            }
            
            containerHtml += '<tr class="' + rowClass + '">' +
                '<td class="text-center">' + (index + 1) + '</td>' +
                '<td class="text-center combination-numbers">' + ticketNumbers + '</td>' +
                '<td class="text-center">' +
                '<span class="badge ' + badgeClass + '">' + winResult + '</span>' +
                '</td>' +
                '</tr>';
        });
    } else {
        containerHtml += '<tr>' +
            '<td colspan="3" class="text-center">' +
            '<em>No combination tickets found</em>' +
            '</td>' +
            '</tr>';
    }
    
    containerHtml += '</tbody>' +
        '</table>' +
        '</div>' +
        
        // Close Button
        '<div class="text-center mt-3">' +
        '<button type="button" class="btn btn-secondary" onclick="hideCombinationTickets()">' +
        '<i class="fa fa-times"></i> Close Combination Table' +
        '</button>' +
        '</div>' +
        
        '</div>' +
        '</div>' +
        '</div>';
    
    // Add the container after the prize history card
    $('.content .container .card:first').after(containerHtml);
    
    // Scroll to the combination table
    $('html, body').animate({
        scrollTop: $('#combinationTicketsContainer').offset().top - 20
    }, 800);
}

function hideCombinationTickets() {
    $('#combinationTicketsContainer').fadeOut(500, function() {
        $(this).remove();
    });
}
</script>
