<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url('application/views/admin/prize/prize_history.css'); ?>">

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-dollar"></i> Prize History
            <small>Administrator Combination Tables Win Records</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Prize History</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-table"></i> Prize History Records
                        </h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo site_url('admin/dashboard'); ?>" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> Back to Prediction Dashboard
                            </a>
                        </div>
                    </div>
                    
                    <div class="box-body">
                        <!-- Pagination Controls -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="per_page_select">File Win Records per page:</label>
                                    <select id="per_page_select" class="form-control" style="width: auto; display: inline-block;">
                                        <?php foreach($pagination_options as $option): ?>
                                            <option value="<?php echo $option; ?>" <?php echo ($per_page == $option) ? 'selected' : ''; ?>>
                                                <?php echo $option; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 text-right">
                                <div class="pagination-info">
                                    Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $per_page, $total_records); ?> 
                                    of <?php echo $total_records; ?> entries
                                </div>
                            </div>
                        </div>

                        <!-- Prize History Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="prizeHistoryTable">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>Original</th>
                                        <th>Lotto</th>
                                        <th style="width: 50px;">N</th>
                                        <th style="width: 60px;">R</th>
                                        <th>Saved</th>
                                        <th style="width: 80px;">R (Actual)</th>
                                        <th style="width: 80px;">Active</th>
                                        <th>Next Date</th>
                                        <?php $prize_columns = isset($prize_columns) ? $prize_columns : array(); ?>
                                        <th colspan="<?php echo max(1, count($prize_columns)); ?>" class="text-center" style="background-color: #f4f4f4;">
                                            <strong>Win Record</strong>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="9"></th>
                                        <!-- Dynamic Win Record Sub-headers -->
                                        <?php if(!empty($prize_columns)): ?>
                                            <?php foreach($prize_columns as $column): ?>
                                                <th class="text-center" style="width: 35px; background-color: #e8f5e8;" 
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
                                            <td colspan="<?php echo 9 + max(1, count($prize_columns)); ?>" class="text-center">
                                                <em>No prize history records found for this administrator.</em>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($prize_records as $record): ?>
                                            <tr>
                                                <td><?php echo sprintf('%02d', $record->row_number); ?></td>
                                                <td><?php echo htmlspecialchars($record->file_name); ?></td>
                                                <td><?php echo htmlspecialchars($record->lotto_name); ?></td>
                                                <td class="text-center"><?php echo sprintf('%02d', $record->N); ?></td>
                                                <td class="text-center"><?php echo number_format($record->R); ?></td>
                                                <td><?php echo htmlspecialchars($record->file_name . 'ADMIN' . sprintf('%02d', $this->session->userdata('id'))); ?></td>
                                                <td class="text-center"><?php echo number_format($record->CCCC); ?></td>
                                                <td class="text-center">
                                                    <?php if($record->is_active == 'YES'): ?>
                                                        <span class="label label-success">YES</span>
                                                    <?php else: ?>
                                                        <span class="label label-danger">EXPIRED</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('M j, Y', strtotime($record->lastdate)); ?></td>
                                                
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
$(document).ready(function() {
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
        
        // Show loading indicator
        var totalCols = 9 + <?php echo max(1, count($prize_columns)); ?>;
        $('#prizeHistoryTable tbody').html('<tr><td colspan="' + totalCols + '" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');
        
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
                alert('Error loading data. Please try again.');
                location.reload();
            }
        });
    }
});
</script>
