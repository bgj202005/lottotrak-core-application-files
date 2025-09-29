<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Statistics Optimization Configuration
|--------------------------------------------------------------------------
|
| This file contains configuration options for optimizing the statistics
| views and draw loading performance.
|
*/

/*
|--------------------------------------------------------------------------
| Pagination Settings
|--------------------------------------------------------------------------
*/

// Threshold for switching to AJAX pagination (number of draws)
$config['ajax_pagination_threshold'] = 200;

// Default page size for AJAX pagination
$config['default_page_size'] = 50;

// Available page size options
$config['page_size_options'] = array(25, 50, 100, 200);

// Maximum page size allowed
$config['max_page_size'] = 500;

/*
|--------------------------------------------------------------------------
| Cache Settings
|--------------------------------------------------------------------------
*/

// Cache time-to-live for different data types (in seconds)
$config['cache_ttl'] = array(
    'lottery_stats'     => 3600,    // 1 hour
    'evensodds'         => 1800,    // 30 minutes
    'draws_data'        => 900,     // 15 minutes
    'trend_data'        => 1800     // 30 minutes
);

// Enable/disable caching
$config['cache_enabled'] = TRUE;

/*
|--------------------------------------------------------------------------
| Performance Settings
|--------------------------------------------------------------------------
*/

// Maximum number of draws to process for trends in single request
$config['max_trend_processing'] = 100;

// Enable lazy loading for large datasets
$config['lazy_loading_enabled'] = TRUE;

// Database query timeout (seconds)
$config['query_timeout'] = 30;

/*
|--------------------------------------------------------------------------
| Frontend Optimization
|--------------------------------------------------------------------------
*/

// Enable progressive loading
$config['progressive_loading'] = TRUE;

// Number of rows to render initially before pagination
$config['initial_render_rows'] = 50;

// Enable virtual scrolling for very large datasets
$config['virtual_scrolling'] = FALSE;

// Search debounce time (milliseconds)
$config['search_debounce'] = 300;

/*
|--------------------------------------------------------------------------
| Debug and Monitoring
|--------------------------------------------------------------------------
*/

// Log slow queries (queries taking longer than this many seconds)
$config['slow_query_threshold'] = 2;

// Enable performance profiling
$config['enable_profiling'] = FALSE;

// Log performance metrics
$config['log_performance'] = TRUE;