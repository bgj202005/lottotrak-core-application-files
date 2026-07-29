<link href="https://unpkg.com/bootstrap-table@1.18.0/dist/bootstrap-table.min.css" rel="stylesheet">
<link href="https://unpkg.com/bootstrap-table@1.18.0/dist/extensions/reorder-rows/bootstrap-table-reorder-rows.css" rel="stylesheet">
<style>
	.card {
    background-color: #ffffff;
    border: 1px solid rgba(0, 34, 51, 0.1);
    box-shadow: 2px 4px 10px 0 rgba(0, 34, 51, 0.05), 2px 4px 10px 0 rgba(0, 34, 51, 0.05);
    border-radius: 0.15rem;
	}
	/* Tabs Card */
	.tab-card {
	border:1px solid #eee;
	}
	.tab-card-header {
	background:none;
	}
	/* Default mode */
	.tab-card-header > .nav-tabs {
	border: none;
	margin: 0px;
	}
	.tab-card-header > .nav-tabs > li {
	margin-right: 2px;
	}
	.tab-card-header > .nav-tabs > li > a {
	border: 0;
	border-bottom:2px solid transparent;
	margin-right: 0;
	color: #737373;
	padding: 2px 15px;
	}
	.tab-card-header > .nav-tabs > li > a.show {
		border-bottom:2px solid #007bff;
		color: #007bff;
	}
	.tab-card-header > .nav-tabs > li > a:hover {
		color: #007bff;
	}
	/* Custom class for nav-tabs */
        .nav-tabs.custom-nav-tabs {
            margin-left: 20px;
            margin-right: 20px;
    }
	.tab-card-header > .tab-content {
	padding-bottom: 0;
	}
	.card-title {
		color:#000000;
	}
	.card-text {
		color:steelblue; 
	}
	table{
  		border: 2px solid #000;
  		border-top: 4px solid #000;
  		width: 100%;
  		margin: 0 auto;
  		margin-bottom: 15px;
  		box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  		table-layout: fixed;
	}
	/* pos */
	table.pos{
 		border: 2px solid #000;
 		border-top: 4px solid #000;
  		width: 100%;
		margin: 0 auto;
		margin-bottom: 15px;
		box-shadow: 0 2px 4px rgba(0,0,0,0.1);
		table-layout: fixed;
	}
	
	/* Center table content */
	table th, table td {
		text-align: center !important;
		vertical-align: middle;
		padding: 8px;
	}
	
	/* Prevent header text wrapping */
	table th {
		white-space: nowrap;
		font-size: 0.85em;
		font-weight: bold;
		min-width: 80px;
		padding: 6px 4px;
	}
	
	/* Smaller font for second row headers to fit within borders */
	table thead tr:nth-child(2) th {
		font-size: 0.75em;
		padding: 4px 2px;
		line-height: 1.2;
	}
	
	/* Ensure data cells have consistent sizing */
	table td {
		font-size: 0.95em;
		min-width: 80px;
	}

	/* H-W-C Winners Table - Increase font size by 20% */
	/* Override Bootstrap table-sm class which makes fonts smaller */
	#hwc-winners-table {
		font-size: 1.2em !important;
	}
	
	#hwc-winners-table.table-sm th {
		font-size: 1.02em !important; /* Override Bootstrap table-sm header size */
		padding: 0.75rem !important; /* Override Bootstrap table-sm padding */
	}
	
	#hwc-winners-table.table-sm td {
		font-size: 1.14em !important; /* Override Bootstrap table-sm cell size */
		padding: 0.75rem !important; /* Override Bootstrap table-sm padding */
	}
	
	/* Maintain responsive text sizes for H-W-C Winners on different screens */
	@media (max-width: 576px) {
		#hwc-winners-table {
			font-size: 1.1em !important; /* Slightly smaller increase on mobile */
		}
		#hwc-winners-table th {
			font-size: 0.95em !important;
		}
		#hwc-winners-table td {
			font-size: 1.05em !important;
		}
	}
	
	/* Table container for mobile-friendly layout */
	.table-container {
		display: flex;
		flex-wrap: wrap;
		gap: 20px;
		justify-content: flex-start;
		align-items: flex-start;
	}
	
	/* Table pair wrapper - groups related tables together */
	.table-pair {
		display: flex;
		gap: 15px;
		flex: 1;
		min-width: 300px;
		align-items: flex-start;
	}
	
	/* Individual table wrapper */
	.table-wrapper {
		flex: 1;
		min-width: 140px;
		display: flex;
		justify-content: center;
		align-items: flex-start;
	}
	
	.table-wrapper table {
		width: 100%;
		max-width: 100%;
	}
	
	/* Extra ball table - full width when present */
	.extra-table-wrapper {
		flex: 1 1 100%;
		min-width: 280px;
		max-width: 400px;
		margin: 0 auto;
		display: flex;
		justify-content: center;
		align-items: flex-start;
	}
	
	.extra-table-wrapper table {
		width: 100%;
		max-width: 100%;
	}
	
	/* Mobile responsiveness */
	@media (max-width: 768px) {
		.table-pair {
			flex-direction: column;
			min-width: 100%;
		}
		
		.table-wrapper {
			flex: 1 1 100%;
			min-width: 100%;
		}
		
		.extra-table-wrapper {
			min-width: 100%;
			max-width: 100%;
		}
		
		.table-container {
			flex-direction: column;
		}
	}
	
	@media (min-width: 769px) and (max-width: 1200px) {
		.table-pair {
			flex: 1 1 100%;
			margin-bottom: 20px;
		}
	}
	th.datafont{
		text-align:center;
		font-size: 0.95em;
	}
	td.datafont { 
		font-size: 	0.95em;
		text-align: center; 
		white-space: nowrap;
	}
	.last-draws {
		margin-top:3em;
		text-align: center;
	}
	.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    color: #000000;
	}	
.shadow-sm {
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
	}
.nav-tabs {
  margin: 20px 0;
}
.tab-card-header > .tab-content {
  padding-bottom: 0;
  margin: 25px;
}
/* New styles for tab margins and padding */
    .tab-content {
        margin-left: 20px;
        margin-right: 20px;
        padding-bottom: 20px;
        border: 1px solid #eee; /* Add border around tab content */
        padding: 20px; /* Add padding inside the tab content */
        margin-bottom: 10px; /* Add bottom margin */
        border-radius: 0.15rem; /* Rounded corners for all sides */
        margin-top: -20px; /* Ensure border starts below tabs */
    }
    /* Add border around tabs */
    .nav-tabs {
        border: none; /* Remove border from tabs */
    }
    .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
        border-color: #eee #eee #fff; /* Ensure active tab blends with content border */
    }
    
    /* H-W-C Winners Table Styling - Mobile-First Bootstrap Responsive */
    #hwc-winners-table {
        table-layout: auto;
        width: 100%;
        max-width: 100%;
        word-wrap: break-word;
        overflow-wrap: break-word;
        font-size: 0.95em; /* Match datafont size from Last Draw and Future Draw tabs */
        min-width: 600px; /* Ensure table has minimum width to maintain readability */
    }
    
    .table-responsive {
        overflow-x: auto !important; /* Allow horizontal scrolling when needed */
        overflow-y: auto;
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
        max-width: 100%;
    }
    
    /* Sticky header for better mobile experience */
    #hwc-winners-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #343a40 !important;
    }
    
    /* Badge enhancements for mobile */
    .badge-lg {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
    
    /* Mobile-optimized badges */
    @media (max-width: 576px) {
        .badge {
            font-size: 0.8em; /* Proportional to new base font size */
            padding: 0.25rem 0.5rem;
        }
        
        .badge-lg {
            font-size: 0.85em; /* Proportional to new base font size */
            padding: 0.3rem 0.6rem;
        }
    }
    
    #hwc-winners-table .hwc-row {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    
    #hwc-winners-table .hwc-row:hover {
        background-color: #e3f2fd !important;
        border-left: 4px solid #2196f3;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: translateY(-1px);
    }
    
    #hwc-winners-table .hwc-row:hover td {
        font-weight: 600;
        color: #1976d2;
    }
    
    /* Force font size overrides for Bootstrap table classes */
    #hwc-winners-table.table th,
    #hwc-winners-table.table td,
    #hwc-winners-table.table-sm th,
    #hwc-winners-table.table-sm td {
        font-size: 0.97em !important; /* Increased by 10% from 0.88em (0.88 * 1.1 = 0.968 ≈ 0.97) */
        padding: 0.3rem 0.15rem !important; /* Override Bootstrap padding */
        white-space: nowrap !important;
        text-align: center !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        line-height: 1.2 !important; /* Tighter line height */
    }
    
    /* Ensure table container allows proper scrolling */
    #hwc-winners-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
    }
    
    /* Override Bootstrap table header styles specifically */
    .thead-dark th {
        font-size: inherit !important;
        padding: inherit !important;
        font-weight: 600 !important; /* Slightly reduce font weight for smaller text */
    }
    
    /* Force override any external Bootstrap Table CSS */
    .table-responsive .table th,
    .table-responsive .table td {
        font-size: inherit !important;
        padding: inherit !important;
    }
    
    /* Ultimate override for desktop display - very specific selector */
    div.table-responsive#hwc-winners-container table#hwc-winners-table.table.table-hover.table-striped.table-sm th,
    div.table-responsive#hwc-winners-container table#hwc-winners-table.table.table-hover.table-striped.table-sm td {
        font-size: 0.88em !important; /* Increased by 10% from 0.80em (0.80 * 1.1 = 0.88) */
        padding: 0.25rem 0.1rem !important;
        line-height: 1.1 !important;
        border: 1px solid #dee2e6 !important; /* Maintain borders */
    }
    
    #hwc-winners-table th.col-rank,
    #hwc-winners-table td.col-rank { 
        min-width: 50px;
        max-width: 60px;
    }
    
    #hwc-winners-table th.col-hot,
    #hwc-winners-table td.col-hot,
    #hwc-winners-table th.col-warm,
    #hwc-winners-table td.col-warm,
    #hwc-winners-table th.col-cold,
    #hwc-winners-table td.col-cold { 
        min-width: 40px;
        max-width: 50px;
    }
    
    #hwc-winners-table th.col-separator,
    #hwc-winners-table td.col-separator { 
        min-width: 15px;
        max-width: 20px;
        padding: 0.5rem 0.1rem;
    }
    
    #hwc-winners-table th.col-prize,
    #hwc-winners-table td.col-prize { 
        min-width: 35px !important; /* Reduced further for compact display */
        max-width: 70px !important; /* Allow more flexibility for "Extra" text */
        width: auto !important; /* Let content determine width */
    }
    
    #hwc-winners-table th.col-points,
    #hwc-winners-table td.col-points { 
        min-width: 60px;
        max-width: 70px;
        font-weight: bold;
    }
    
    #hwc-winners-table td.col-rank,
    #hwc-winners-table td.col-hot,
    #hwc-winners-table td.col-separator,
    #hwc-winners-table td.col-warm,
    #hwc-winners-table td.col-cold,
    #hwc-winners-table td.col-prize,
    #hwc-winners-table td.col-points {
        vertical-align: middle;
        padding: 0.5rem 0.25rem;
    }
    
    /* Sortable rank header styling */
    .sortable-header {
        cursor: pointer;
        user-select: none;
        position: relative;
        padding-right: 25px !important;
        transition: background-color 0.2s ease;
    }
    
    .sortable-header:hover {
        background-color: rgba(255,255,255,0.1);
    }
    
    .sort-arrow {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 12px;
        opacity: 0.7;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }
    
    .sortable-header:hover .sort-arrow {
        opacity: 1;
    }
    
    .sort-arrow.asc {
        transform: translateY(-50%) rotate(0deg);
    }
    
    .sort-arrow.desc {
        transform: translateY(-50%) rotate(180deg);
    }
    
    /* Enhanced row highlighting */
    #hwc-winners-table tbody tr {
        transition: all 0.2s ease-in-out;
        border-left: 4px solid transparent;
    }
    
    #hwc-winners-table tbody tr:nth-child(odd) {
        background-color: #f9f9f9;
    }
    
    #hwc-winners-table tbody tr:hover {
        background-color: #e3f2fd !important;
        border-left: 4px solid #2196f3 !important;
        box-shadow: 0 2px 8px rgba(33, 150, 243, 0.3);
        transform: scale(1.01);
    }
    
    /* Bootstrap Responsive Enhancement with !important overrides */
    @media (max-width: 1199.98px) {
        #hwc-winners-table.table th,
        #hwc-winners-table.table td {
            font-size: 0.88em !important; /* Increased by 10% from 0.80em (0.80 * 1.1 = 0.88) */
            padding: 0.25rem 0.1rem !important;
        }
    }
    
    @media (max-width: 991.98px) {
        #hwc-winners-table.table th,
        #hwc-winners-table.table td {
            font-size: 0.83em !important; /* Increased by 10% from 0.75em (0.75 * 1.1 ≈ 0.83) */
            padding: 0.2rem 0.08rem !important;
        }
    }
    
    @media (max-width: 767.98px) {
        #hwc-winners-table.table th,
        #hwc-winners-table.table td {
            font-size: 0.77em !important; /* Increased by 10% from 0.70em (0.70 * 1.1 = 0.77) */
            padding: 0.15rem 0.05rem !important;
        }
        
        /* Enhanced mobile badge styling - proportionally scaled */
        .badge {
            font-size: 0.75em;
            min-width: 20px;
        }
        
        .badge-lg {
            font-size: 0.8em;
            min-width: 25px;
        }
    }
    
    @media (max-width: 575.98px) {
        #hwc-winners-table.table th,
        #hwc-winners-table.table td {
            font-size: 0.70em !important; /* Increased by 10% from 0.64em (0.64 * 1.1 ≈ 0.70) */
            padding: 0.1rem 0.03rem !important; /* Minimal padding */
            min-width: unset !important; /* Allow columns to be as narrow as needed */
        }
        
        #hwc-winners-table {
            min-width: 400px !important; /* Reduce minimum table width */
        }
        
        /* Ultra-compact mobile view */
        .badge {
            font-size: 0.7em;
            padding: 0.2rem 0.4rem;
            min-width: 18px;
        }
        
        .badge-lg {
            font-size: 0.75em;
            padding: 0.25rem 0.5rem;
            min-width: 22px;
        }
        
        /* Compact column spacing for mobile */
        #hwc-winners-table th.col-rank,
        #hwc-winners-table td.col-rank {
            width: 35px;
        }
        
        #hwc-winners-table th.col-hot,
        #hwc-winners-table td.col-hot,
        #hwc-winners-table th.col-warm,
        #hwc-winners-table td.col-warm,
        #hwc-winners-table th.col-cold,
        #hwc-winners-table td.col-cold {
            width: 30px;
        }
        
        #hwc-winners-table th.col-points,
        #hwc-winners-table td.col-points {
            width: 40px;
        }
        
        /* Make prize columns more compact on mobile */
        #hwc-winners-table th.col-prize,
        #hwc-winners-table td.col-prize {
            width: 28px;
            padding: 0.2rem 0.1rem;
        }
    }
    
    /* Extra small screens - horizontal scroll with very compact layout */
    @media (max-width: 480px) {
        #hwc-winners-table {
            min-width: 400px; /* Further reduced for very small screens */
            font-size: 0.7em; /* Proportional scaling from 0.95em base */
        }
        
        #hwc-winners-table th,
        #hwc-winners-table td {
            padding: 0.2rem 0.1rem;
        }
        
        .badge {
            font-size: 0.65em;
            padding: 0.15rem 0.3rem;
            min-width: 16px;
        }
        
        .badge-lg {
            font-size: 0.7em;
            padding: 0.2rem 0.4rem;
            min-width: 20px;
        }
        
        /* Ultra-compact columns */
        #hwc-winners-table th.col-rank,
        #hwc-winners-table td.col-rank,
        #hwc-winners-table th.col-hot,
        #hwc-winners-table td.col-hot,
        #hwc-winners-table th.col-warm,
        #hwc-winners-table td.col-warm,
        #hwc-winners-table th.col-cold,
        #hwc-winners-table td.col-cold {
            width: 25px;
        }
        
        #hwc-winners-table th.col-prize,
        #hwc-winners-table td.col-prize {
            width: 24px;
        }
        
        #hwc-winners-table th.col-points,
        #hwc-winners-table td.col-points {
            width: 35px;
        }
    }
    
    /* Mobile Touch-Friendly Enhancements */
    .nav-tabs .nav-link {
        min-height: 48px; /* Touch-friendly minimum height */
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    
    @media (max-width: 767.98px) {
        .nav-tabs .nav-link {
            min-height: 44px;
            padding: 0.5rem 0.25rem;
            font-size: 0.875rem;
        }
        
        .nav-tabs {
            margin-left: 10px !important;
            margin-right: 10px !important;
        }
        
        .tab-content {
            margin-left: 10px !important;
            margin-right: 10px !important;
            padding: 10px !important;
        }
    }
    
    @media (max-width: 575.98px) {
        .nav-tabs .nav-link {
            min-height: 40px;
            padding: 0.375rem 0.125rem;
            font-size: 0.8rem;
        }
        
        .nav-tabs {
            margin-left: 5px !important;
            margin-right: 5px !important;
        }
        
        .tab-content {
            margin-left: 5px !important;
            margin-right: 5px !important;
            padding: 5px !important;
        }
    }
    
    /* Responsive container handling */
    .table-responsive {
        max-width: 100% !important;
        position: relative;
    }
    
    .container-fluid {
        max-width: 100% !important;
        overflow-x: hidden; /* Prevent page-level horizontal scroll */
    }
    
    /* Horizontal scroll bar styling */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }
    
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .table-responsive::-webkit-scrollbar-thumb {
        background: #007bff;
        border-radius: 4px;
    }
    
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #0056b3;
    }
    
    /* Scroll indicator for better UX */
    .scroll-indicator {
        position: absolute;
        bottom: 10px;
        right: 20px;
        background: rgba(0, 123, 255, 0.9);
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.75rem;
        z-index: 100;
        animation: fadeInOut 3s ease-in-out;
        pointer-events: none;
    }
    
    @keyframes fadeInOut {
        0%, 100% { opacity: 0; }
        10%, 90% { opacity: 1; }
    }
    
    /* Enhanced mobile summary cards */
    @media (max-width: 767.98px) {
        .bg-light.rounded {
            border: 1px solid #e9ecef;
            transition: all 0.2s ease;
        }
        
        .bg-light.rounded:hover {
            background-color: #f8f9fa !important;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    }
</style>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<h2><?php echo 'H (Hots), W (Warms) and C (Colds) winners for: '.$lottery->lottery_name; ?></h2>
	<?php $max = $lottery->balls_drawn; 
	   $b = 1; 
	   ?>	
	<h5 style = "text-align:left"><?php echo anchor('admin/history', 'Back to the Win History Dashboard', 'title="Back to History"'); ?></h5>
	<section>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="card mt-3 tab-card">
						<div id = "error"></div>
						<div class="card-header tab-card-header">
							<div class="d-flex flex-row-reverse">
								<div class="col-md-6">
									<div class="bg-white card last-draws mb-4 shadow-sm">
										<div class="p-4">
											<h4 class="mb-1">Draw Range: <?=$lottery->last_drawn['range'];?> Draws</h4>
										</div>
									</div>
									<div class="bg-white card last-draws mb-4 shadow-sm">
										<div class="p-4">
											<h4 class="mb-1">
											<?php $extra = array('for' => 'extra_lb', 'style' =>'margin-right:10px;');
											echo form_label('Extra (Bonus) Ball Included?', 'extra_lb', $extra);
											echo (!empty($lottery->extra_included)) ? form_label(' YES', 'extra_lb', $extra) : form_label(' NO', 'extra_lb', $extra);
											?></h4>
										</div>
									</div>
									<div class="bg-white card last-draws mb-4 shadow-sm">
										<div class="p-4">
											<h4 class="mb-1">
											<?php $format = array('for' => 'extra_lb', 'style' =>'margin-right:10px;');
											echo form_label('Last Draw Date:', 'extra_lb', $format);
											echo "<br />";
											echo date("l, M-d-Y",strtotime(str_replace('/','-',$lottery->last_drawn['draw_date']))); 
											echo "<br /><br />";
											$mx = 1;
											$extra_ball = 0;	// set extra ball to something, even if not used.
											$xtr = FALSE;
											foreach($lottery->draw as $count => $drawn):
												echo $drawn." ";
												if($xtr) $extra_ball = $drawn; // capture the extra / bonus ball
												$mx++;
												if(($mx>$lottery->balls_drawn)&&($lottery->extra_ball)&&(!$xtr)):
													echo " + ";
													$xtr = TRUE;
												endif;
											endforeach;
											?></h4>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="bg-white card last-draws mb-4 shadow-sm">
										<div class="p-4">
											<h4 class="mb-1">
											<div class = "text-center" style = "display:inline-block;">
											<?php 	$hot = array('style' =>'margin-right:10px; color:red;');
													$warm = array('style' =>'margin-right:10px; color:orange;');
													$cold = array('style' =>'color:blue;');
													echo form_label("Hots:", "id => 'lb_hots'", $extra);
													echo form_label($lottery->H, "id => 'hots'", $hot);
													echo form_label("Warms:", "id => 'lb_warms'", $extra);
													echo form_label($lottery->W, "id => 'warms'", $warm);
													echo form_label("Colds:", "id => 'lb_colds'", $extra);
													echo form_label($lottery->C, "id => 'colds'", $cold);
												?>
											</div></h4>
										</div>
									</div>
									<div class="bg-white card last-draws mb-4 shadow-sm">
										<div class="p-4">
											<h4 class="mb-1">
											<?php $extra = array('for' => 'extra_lb', 'style' =>'margin-right:10px;');
											echo form_label('Extra Draws Included?', 'extra_lb', $extra);
											echo (!empty($lottery->extra_draws)) ? form_label(' YES', 'extra_lb', $extra) : form_label(' NO', 'extra_lb', $extra);
											?></h4>
										</div>
									</div>
									<div class="bg-white card last-draws mb-4 shadow-sm">
										<div class="p-4">
											<h4 class="mb-1">
											<?php $extra = array('for' => 'extra_lb', 'style' =>'margin-right:10px;');
											echo form_label('Last Drawn HWC:', 'extra_lb', $extra);
											$hot = array('style' =>'margin-right:10px; color:red;');
													$warm = array('style' =>'margin-right:10px; color:orange;');
													$cold = array('style' =>'color:blue;');
													echo form_label("H:", "id => 'lb_hots'", $extra);
													echo form_label($lottery->hwc[0], "id => 'hots'", $hot);
													echo form_label("W:", "id => 'lb_warms'", $extra);
													echo form_label($lottery->hwc[1], "id => 'warms'", $warm);
													echo form_label("C:", "id => 'lb_colds'", $extra);
													echo form_label($lottery->hwc[2], "id => 'colds'", $cold);
											?></h4>
										</div>
									</div>
									<div class="bg-white card last-draws mb-4 shadow-sm">
										<div class="p-4">
											<h4 class="mb-1">
											<?php 
											$pool_style = array('for' => 'pool_lb', 'style' =>'margin-right:10px;');
											$pool_number_style = array('style' =>'margin-right:10px; color:#007bff; font-weight:bold;');
											echo form_label('Prediction Number Pool:', 'pool_lb', $pool_style);
											
											// Get prediction pool from lottery_h_w_c table prediction_pool field
											$prediction_pool = isset($lottery->prediction_pool) ? $lottery->prediction_pool : 18; // Default to 18 if not available
											
											echo form_label($prediction_pool . ' Numbers', 'pool_numbers', $pool_number_style);
											?></h4>
										</div>
									</div>
								</div>		
							</div>
						</div>
						<ul class="nav nav-tabs nav-fill" id="myTab" role="tablist" style="margin-left: 20px; margin-right: 20px;">
							<li class="nav-item">
								<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">
									<div class="card-heading">
										<span class="d-none d-md-inline">Last Draw</span>
										<span class="d-inline d-md-none">Last</span>
									</div>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">
									<div class="card-heading">
										<span class="d-none d-md-inline">Future Draw</span>
										<span class="d-inline d-md-none">Future</span>
									</div>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="winners-tab" data-toggle="tab" href="#winners" role="tab" aria-controls="winners" aria-selected="false">
									<div class="card-heading">
										<span class="d-none d-sm-inline">H-W-C Winners</span>
										<span class="d-inline d-sm-none">Winners</span>
									</div>
								</a>
							</li>
						</ul>
						<div class="tab-content" id="myTabContent">
						<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
							<div class="container-fluid" style="margin: 25px;">
								<div class="table-container">
									<!-- Hot Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Last Hots</th>
													</tr>
													<tr>
														<th class="text-center">Ball</th>
														<th class="text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														$cntr = 0;
														$noEX = FALSE; 
														foreach($lottery->hots_last as $ball => $count):	
														if($ball) :
															echo "<tr class='table-danger'>";
															$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
															$ball = rtrim($ball,'*');  // Remove the special '*' symbol
															if($sym=='*'&&$ball!=$extra_ball) :
																echo "<td class='text-center bg-danger text-white'>".$ball."</td>";
																echo "<td class='text-center bg-danger text-white'>".$count."</td>";
															elseif($sym=='*'&&$lottery->extra_included&&$ball==$extra_ball&&!$lottery->duplicate_extra_ball):
																echo "<td class='text-center bg-info text-white'>".$ball."</td>";
																echo "<td class='text-center bg-info text-white'>".$count."</td>";
															else:
																echo "<td class='text-center'>".$ball."</td>";
																echo "<td class='text-center'>".$count."</td>";
															endif;
															echo "</tr>";
														else:
															echo "<tr class='table-danger'><td colspan = '2'>No Hots</td></tr>";
														endif;
														if($sym=='*'&&!$lottery->extra_included&&$ball==$extra_ball):
															$noEX = TRUE;
														endif;
														if(!$noEX):
															$cntr++;
														endif;
													endforeach; ?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Hot Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														foreach($lottery->hots_pos_last as $position => $count):	
															$exists = FALSE;
															echo "<tr class='table-light'>";
															$exists = array_key_exists($position, $lottery->positions_last);
															$position = rtrim($position,'h');  // Remove the special 'h' symbol
															if($exists) :
																if($noEX&&($cntr==$position)):
																	echo "<td class='text-center'>".$position."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																else:	
																	echo "<td class='text-center bg-danger text-white'>".$position."</td>";
																	echo "<td class='text-center bg-danger text-white'>".$count."</td>";
																endif;
															else:
															echo "<td class='text-center'>".$position."</td>";
															echo "<td class='text-center'>".$count."</td>";
															endif;
															echo "</tr>";
														endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Warm Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Last Warms</th>
													</tr>
													<tr>
														<th class="text-text-center">Ball</th>
														<th class="text-text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php  
														$cntr = 0;
														$noEX = FALSE;
														foreach($lottery->warms_last as $ball => $count):	
														if($ball) :
															echo "<tr class='table-warning'>";
															$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
															$ball = rtrim($ball,'*');  // Remove the special '*' symbol
															if($sym=='*'&&$ball!=$extra_ball) :
																echo "<td class='text-center bg-danger text-white'>".$ball."</td>";
																echo "<td class='text-center bg-danger text-white'>".$count."</td>";
															elseif($sym=='*'&&$lottery->extra_included&&$ball==$extra_ball&&!$lottery->duplicate_extra_ball):
																echo "<td class='text-center bg-info text-white'>".$ball."</td>";
																echo "<td class='text-center bg-info text-white'>".$count."</td>";
															else:
																echo "<td class='text-center'>".$ball."</td>";
																echo "<td class='text-center'>".$count."</td>";
															endif;
															echo "</tr>";
														else:
															echo "<tr class='table-warning'><td colspan = '2'>No Warms</td></tr>";
														endif;
														if($sym=='*'&&!$lottery->extra_included&&$ball==$extra_ball):
															$noEX = TRUE;
														endif;
														if(!$noEX):
															$cntr++;
														endif;
													endforeach; ?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Warm Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($lottery->warms_pos_last as $position => $count):	
															$exists = FALSE;
															echo "<tr class='table-light'>";
															$exists = array_key_exists($position, $lottery->positions_last);
															$position = rtrim($position,'w');  // Remove the special '*' symbol
															if($exists) :
																if($noEX&&($cntr==$position)):
																	echo "<td class='text-center'>".$position."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																else:	
																	echo "<td class='text-center bg-danger text-white'>".$position."</td>";
																	echo "<td class='text-center bg-danger text-white'>".$count."</td>";
																endif;
															else:
																echo "<td class='text-center'>".$position."</td>";
																echo "<td class='text-center'>".$count."</td>";
															endif;
															echo "</tr>";
													endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Cold Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Last Colds</th>
													</tr>
													<tr>
														<th class="text-center">Ball</th>
														<th class="text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														$cntr = 0;
														$noEX = FALSE;
														foreach($lottery->colds_last as $ball => $count):	
														if($ball) :
															echo "<tr class='table-primary'>";
															$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
															$ball = rtrim($ball,'*');  // Remove the special '*' symbol
															if($sym=='*'&&$ball!=$extra_ball) :
																$ball = rtrim($ball,'*'); // Remove the special '*' symbol
																echo "<td class='text-center bg-danger text-white'>".$ball."</td>";
																echo "<td class='text-center bg-danger text-white'>".$count."</td>";
															elseif($sym=='*'&&$lottery->extra_included&&$ball==$extra_ball&&!$lottery->duplicate_extra_ball):
																echo "<td class='text-center bg-info text-white'>".$ball."</td>";
																echo "<td class='text-center bg-info text-white'>".$count."</td>";
															else:
																echo "<td class='text-center'>".$ball."</td>";
																echo "<td class='text-center'>".$count."</td>";
															endif;
															echo "</tr>";
														else:
															echo "<tr class='table-primary'><td colspan = '2'>No Colds</td></tr>";
														endif;
														if($sym=='*'&&!$lottery->extra_included&&$ball==$extra_ball):
															$noEX = TRUE;
														endif;
														if(!$noEX):
															$cntr++;
														endif;
													endforeach; ?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Cold Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($lottery->colds_pos_last as $position => $count):	
															$exists = FALSE;
															echo "<tr class='table-light'>";
															$exists = array_key_exists($position, $lottery->positions_last);
															$position = rtrim($position,'c');  // Remove the special '*' symbol
															if($exists) :
																if($noEX&&($cntr==$position)):
																	echo "<td class='text-center'>".$position."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																else:	
																	echo "<td class='text-center bg-danger text-white'>".$position."</td>";
																	echo "<td class='text-center bg-danger text-white'>".$count."</td>";
																endif;
															else:
																echo "<td class='text-center'>".$position."</td>";
																echo "<td class='text-center'>".$count."</td>";
															endif;
															echo "</tr>";
													endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Extra Ball Table (standalone if present) -->
									<?php if(isset($lottery->dupextra_last)) : ?>
									<div class="extra-table-wrapper">
										<table class="table">
											<thead>
												<tr>
													<th class="text-center" colspan="2">Last Extra Ball</th>
												</tr>
												<tr>
													<th class="text-center">Ball</th>
													<th class="text-center">Occurrences</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($lottery->dupextra_last as $ball => $count):	
														echo "<tr class='table-success'>";
														if($ball==$lottery->last_drawn['extra']):
															echo "<td class='text-center bg-info'>".$ball."</td>";
															echo "<td class='text-center bg-info'>".$count."</td>";
														else:
															echo "<td class='text-center'>".$ball."</td>";
															echo "<td class='text-center'>".$count."</td>";
														endif;
														echo "</tr>";
												endforeach; ?>
											</tbody>
										</table>
									</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
							<div class="container-fluid" style="margin: 25px;">
								<div class="table-container">
									<!-- Hot Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Future Hots</th>
													</tr>
													<tr>
														<th class="text-center">Ball</th>
														<th class="text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														$cntr = 0;
														$noEX = FALSE; 
														foreach($lottery->hots as $ball => $count):	
														if($ball) :
															echo "<tr class='table-danger'>";
															$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
															$ball = rtrim($ball,'*');  // Remove the special '*' symbol
															echo "<td class='text-center'>".$ball."</td>";
															echo "<td class='text-center'>".$count."</td>";
															echo "</tr>";
														else:
															echo "<tr class='table-danger'><td colspan = '2'>No Hots</td></tr>";
														endif;
														if($sym=='*'&&!$lottery->extra_included&&$ball==$extra_ball):
															$noEX = TRUE;
														endif;
														if(!$noEX):
															$cntr++;
														endif;
													endforeach; ?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Hot Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														foreach($lottery->hots_pos as $position => $count):	
															$exists = FALSE;
															echo "<tr class='table-light'>";
															$position = rtrim($position,'h');  // Remove the special 'h' symbol
															echo "<td class='text-center'>".$position."</td>";
															echo "<td class='text-center'>".$count."</td>";
															echo "</tr>";
														endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Warm Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Future Warms</th>
													</tr>
													<tr>
														<th class="text-text-center">Ball</th>
														<th class="text-text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php  
														$cntr = 0;
														$noEX = FALSE;
														foreach($lottery->warms as $ball => $count):	
														if($ball) :
															echo "<tr class='table-warning'>";
															$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
															$ball = rtrim($ball,'*');  // Remove the special '*' symbol
																echo "<td class='text-center'>".$ball."</td>";
																echo "<td class='text-center'>".$count."</td>";
																echo "</tr>";
														else:
															echo "<tr class='table-warning'><td colspan = '2'>No Warms</td></tr>";
														endif;
														if($sym=='*'&&!$lottery->extra_included&&$ball==$extra_ball):
															$noEX = TRUE;
														endif;
														if(!$noEX):
															$cntr++;
														endif;
													endforeach; ?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Warm Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($lottery->warms_pos as $position => $count):	
															$exists = FALSE;
															echo "<tr class='table-light'>";
															$exists = array_key_exists($position, $lottery->positions);
															$position = rtrim($position,'w');  // Remove the special '*' symbol
															echo "<td class='text-center'>".$position."</td>";
															echo "<td class='text-center'>".$count."</td>";
															echo "</tr>";
													endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Cold Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Future Colds</th>
													</tr>
													<tr>
														<th class="text-center">Ball</th>
														<th class="text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														$cntr = 0;
														$noEX = FALSE;
														foreach($lottery->colds as $ball => $count):	
														if($ball) :
															echo "<tr class='table-primary'>";
															$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
															$ball = rtrim($ball,'*');  // Remove the special '*' symbol
															echo "<td class='text-center'>".$ball."</td>";
															echo "<td class='text-center'>".$count."</td>";
															echo "</tr>";
														else:
															echo "<tr class='table-primary'><td colspan = '2'>No Colds</td></tr>";
														endif;
														if($sym=='*'&&!$lottery->extra_included&&$ball==$extra_ball):
															$noEX = TRUE;
														endif;
														if(!$noEX):
															$cntr++;
														endif;
													endforeach; ?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Cold Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($lottery->colds_pos as $position => $count):	
															$exists = FALSE;
															echo "<tr class='table-light'>";
															$exists = array_key_exists($position, $lottery->positions);
															$position = rtrim($position,'c');  // Remove the special '*' symbol
															echo "<td class='text-center'>".$position."</td>";
															echo "<td class='text-center'>".$count."</td>";
															echo "</tr>";
													endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Extra Ball Table (standalone if present) -->
									<?php if(isset($lottery->dupextra)) : ?>
									<div class="extra-table-wrapper">
										<table class="table">
											<thead>
												<tr>
													<th class="text-center" colspan="2">Future Extra Ball</th>
												</tr>
												<tr>
													<th class="text-center">Ball</th>
													<th class="text-center">Occurrences</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($lottery->dupextra as $ball => $count):	
														echo "<tr class='table-success'>";
															echo "<td class='text-center'>".$ball."</td>";
															echo "<td class='text-center'>".$count."</td>";
														echo "</tr>";
												endforeach; ?>
											</tbody>
										</table>
									</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
						
						<!-- Future Draw Tab Content -->
						<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
							<div class="container-fluid" style="margin: 25px;">
								<div class="table-container">
									<!-- Hot Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Future Hots</th>
													</tr>
													<tr>
														<th class="text-center">Ball</th>
														<th class="text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														$cntr = 0;
														$noEX = FALSE; 
														if(isset($lottery->hots)) {
															foreach($lottery->hots as $ball => $count):	
															if($ball) :
																echo "<tr class='table-danger'>";
																$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
																$ball = rtrim($ball,'*');  // Remove the special '*' symbol
																if($sym=='*'&&$ball!=$extra_ball) :
																	echo "<td class='text-center bg-danger text-white'>".$ball."</td>";
																	echo "<td class='text-center bg-danger text-white'>".$count."</td>";
																elseif($sym=='*'&&isset($lottery->extra_included)&&$lottery->extra_included&&$ball==$extra_ball&&!$lottery->duplicate_extra_ball):
																	echo "<td class='text-center bg-info text-white'>".$ball."</td>";
																	echo "<td class='text-center bg-info text-white'>".$count."</td>";
																else:
																	echo "<td class='text-center'>".$ball."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																endif;
																echo "</tr>";
															else:
																echo "<tr class='table-danger'><td colspan = '2'>No Hots</td></tr>";
															endif;
															if($sym=='*'&&!isset($lottery->extra_included)&&$ball==$extra_ball):
																$noEX = TRUE;
															endif;
															if(!$noEX):
																$cntr++;
															endif;
														endforeach;
														} else {
															echo "<tr class='table-danger'><td colspan='2'>No Future Draw Hots Available</td></tr>";
														}
														?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Hot Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														if(isset($lottery->hots_pos)) {
															foreach($lottery->hots_pos as $position => $count):	
																$exists = FALSE;
																echo "<tr class='table-light'>";
																if(isset($lottery->positions)) {
																	$exists = array_key_exists($position, $lottery->positions);
																}
																$position = rtrim($position,'h');  // Remove the special 'h' symbol
																if($exists) :
																	echo "<td class='text-center bg-danger text-white'>".$position."</td>";
																	echo "<td class='text-center bg-danger text-white'>".$count."</td>";
																else:
																	echo "<td class='text-center'>".$position."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																endif;
																echo "</tr>";
															endforeach;
														} else {
															echo "<tr class='table-light'><td colspan='2'>No Hot Positions Available</td></tr>";
														}
														?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Warm Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Future Warms</th>
													</tr>
													<tr>
														<th class="text-center">Ball</th>
														<th class="text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														if(isset($lottery->warms)) {
															foreach($lottery->warms as $ball => $count):	
																echo "<tr class='table-warning'>";
																$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
																$ball = rtrim($ball,'*');  // Remove the special '*' symbol
																if($sym=='*'&&$ball!=$extra_ball) :
																	echo "<td class='text-center bg-warning text-dark'>".$ball."</td>";
																	echo "<td class='text-center bg-warning text-dark'>".$count."</td>";
																elseif($sym=='*'&&isset($lottery->extra_included)&&$lottery->extra_included&&$ball==$extra_ball&&!$lottery->duplicate_extra_ball):
																	echo "<td class='text-center bg-info text-white'>".$ball."</td>";
																	echo "<td class='text-center bg-info text-white'>".$count."</td>";
																else:
																	echo "<td class='text-center'>".$ball."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																endif;
																echo "</tr>";
															endforeach;
														} else {
															echo "<tr class='table-warning'><td colspan='2'>No Future Draw Warms Available</td></tr>";
														}
														?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Warm Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														if(isset($lottery->warms_pos)) {
															foreach($lottery->warms_pos as $position => $count):	
																$exists = FALSE;
																echo "<tr class='table-light'>";
																if(isset($lottery->positions)) {
																	$exists = array_key_exists($position, $lottery->positions);
																}
																$position = rtrim($position,'w');  // Remove the special 'w' symbol
																if($exists) :
																	echo "<td class='text-center bg-warning text-dark'>".$position."</td>";
																	echo "<td class='text-center bg-warning text-dark'>".$count."</td>";
																else:
																	echo "<td class='text-center'>".$position."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																endif;
																echo "</tr>";
															endforeach;
														} else {
															echo "<tr class='table-light'><td colspan='2'>No Warm Positions Available</td></tr>";
														}
														?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Cold Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Future Colds</th>
													</tr>
													<tr>
														<th class="text-center">Ball</th>
														<th class="text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														if(isset($lottery->colds)) {
															foreach($lottery->colds as $ball => $count):	
																echo "<tr class='table-info'>";
																$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
																$ball = rtrim($ball,'*');  // Remove the special '*' symbol
																if($sym=='*'&&$ball!=$extra_ball) :
																	echo "<td class='text-center bg-info text-white'>".$ball."</td>";
																	echo "<td class='text-center bg-info text-white'>".$count."</td>";
																elseif($sym=='*'&&isset($lottery->extra_included)&&$lottery->extra_included&&$ball==$extra_ball&&!$lottery->duplicate_extra_ball):
																	echo "<td class='text-center bg-info text-white'>".$ball."</td>";
																	echo "<td class='text-center bg-info text-white'>".$count."</td>";
																else:
																	echo "<td class='text-center'>".$ball."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																endif;
																echo "</tr>";
															endforeach;
														} else {
															echo "<tr class='table-info'><td colspan='2'>No Future Draw Colds Available</td></tr>";
														}
														?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Cold Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														if(isset($lottery->colds_pos)) {
															foreach($lottery->colds_pos as $position => $count):	
																$exists = FALSE;
																echo "<tr class='table-light'>";
																if(isset($lottery->positions)) {
																	$exists = array_key_exists($position, $lottery->positions);
																}
																$position = rtrim($position,'c');  // Remove the special 'c' symbol
																if($exists) :
																	echo "<td class='text-center bg-info text-white'>".$position."</td>";
																	echo "<td class='text-center bg-info text-white'>".$count."</td>";
																else:
																	echo "<td class='text-center'>".$position."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																endif;
																echo "</tr>";
															endforeach;
														} else {
															echo "<tr class='table-light'><td colspan='2'>No Cold Positions Available</td></tr>";
														}
														?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Extra Ball Table (standalone if present) -->
									<?php if(isset($lottery->dupextra)) : ?>
									<div class="extra-table-wrapper">
										<table class="table">
											<thead>
												<tr>
													<th class="text-center" colspan="2">Future Extra Ball</th>
												</tr>
												<tr>
													<th class="text-center">Ball</th>
													<th class="text-center">Occurrences</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($lottery->dupextra as $ball => $count):	
														echo "<tr class='table-success'>";
															echo "<td class='text-center'>".$ball."</td>";
															echo "<td class='text-center'>".$count."</td>";
														echo "</tr>";
												endforeach; ?>
											</tbody>
										</table>
									</div>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<!-- H-W-C Winners Tab Content -->
						<div class="tab-pane fade" id="winners" role="tabpanel" aria-labelledby="winners-tab">
							<div class="container-fluid px-2 px-md-3 px-lg-4">
								<div class="row">
									<div class="col-12">
										<div class="card border-0 shadow-sm">
											<div class="card-header bg-primary text-white">
												<h5 class="card-title mb-1 text-white">H-W-C Winners Analysis</h5>
												<p class="card-text mb-0 text-light small">H-W-C patterns sorted by total points based on follower win system</p>
											</div>
											<div class="card-body p-2 p-md-3">
												<?php if(!isset($hwc_winners) || empty($hwc_winners)): ?>
													<div class="alert alert-warning" role="alert">
														<strong>No H-W-C winner data available.</strong> 
														Please recalculate H-W-C statistics from the Statistics page to generate winner analysis.
													</div>
												<?php else: ?>
													<!-- Mobile Summary Stats (visible only on mobile) -->
													<div class="d-block d-md-none mb-3">
														<div class="row">
															<div class="col-4">
																<div class="text-center p-2 bg-light rounded">
																	<small class="text-muted d-block">Patterns</small>
																	<strong><?= count($hwc_winners) ?></strong>
																</div>
															</div>
															<div class="col-4">
																<div class="text-center p-2 bg-light rounded">
																	<small class="text-muted d-block">Top Points</small>
																	<strong class="text-success"><?= isset($hwc_winners[0]['total_points']) ? $hwc_winners[0]['total_points'] : 0 ?></strong>
																</div>
															</div>
															<div class="col-4">
																<div class="text-center p-2 bg-light rounded">
																	<small class="text-muted d-block">Categories</small>
																	<strong><?= isset($hwc_winners[0]['enabled_categories']) ? count($hwc_winners[0]['enabled_categories']) : 0 ?></strong>
																</div>
															</div>
														</div>
													</div>
													
													<!-- Scroll instruction for narrow screens -->
													<div class="alert alert-info d-block d-lg-none py-2" role="alert">
														<small>
															<i class="fas fa-info-circle"></i>
															<strong>Tip:</strong> Swipe left/right or use the horizontal scroll bar below to see all columns.
														</small>
													</div>
													
													<div class="table-responsive" id="hwc-winners-container">
														<table class="table table-hover table-striped table-sm" id="hwc-winners-table">
															<thead class="thead-dark sticky-top">
																<tr>
																	<th class="text-center col-rank sortable-header" onclick="toggleSort()">
																		<span class="d-none d-sm-inline">Rank</span>
																		<span class="d-inline d-sm-none">#</span>
																		<span class="sort-arrow desc" id="sort-arrow">▲</span>
																	</th>
																	<th class="text-center col-hot">
																		<span class="d-none d-sm-inline text-danger">Hot</span>
																		<span class="d-inline d-sm-none text-danger">H</span>
																	</th>
																	<th class="text-center col-separator d-none d-sm-table-cell">-</th>
																	<th class="text-center col-warm">
																		<span class="d-none d-sm-inline text-warning">Warm</span>
																		<span class="d-inline d-sm-none text-warning">W</span>
																	</th>
																	<th class="text-center col-separator d-none d-sm-table-cell">-</th>
																	<th class="text-center col-cold">
																		<span class="d-none d-sm-inline text-info">Cold</span>
																		<span class="d-inline d-sm-none text-info">C</span>
																	</th>
																	<?php if(isset($hwc_winners[0]['enabled_categories'])): ?>
																		<?php foreach($hwc_winners[0]['enabled_categories'] as $category): ?>
																			<?php if($category == 'extra'): ?>
																				<th class="text-center col-prize d-none d-md-table-cell">
																					<span class="d-none d-lg-inline">Extra</span>
																					<span class="d-inline d-lg-none">E</span>
																				</th>
																			<?php elseif($category == '1_win'): ?>
																				<th class="text-center col-prize d-none d-md-table-cell">1</th>
																			<?php elseif($category == '1_win_extra'): ?>
																				<th class="text-center col-prize d-none d-lg-table-cell">
																					<span class="d-none d-lg-inline">1 + Extra</span>
																					<span class="d-inline d-lg-none">1+</span>
																				</th>
																			<?php elseif($category == '2_win'): ?>
																				<th class="text-center col-prize">2</th>
																			<?php elseif($category == '2_win_extra'): ?>
																				<th class="text-center col-prize d-none d-lg-table-cell">
																					<span class="d-none d-lg-inline">2 + Extra</span>
																					<span class="d-inline d-lg-none">2+</span>
																				</th>
																			<?php elseif($category == '3_win'): ?>
																				<th class="text-center col-prize">3</th>
																			<?php elseif($category == '3_win_extra'): ?>
																				<th class="text-center col-prize d-none d-lg-table-cell">
																					<span class="d-none d-lg-inline">3 + Extra</span>
																					<span class="d-inline d-lg-none">3+</span>
																				</th>
																			<?php elseif($category == '4_win'): ?>
																				<th class="text-center col-prize">4</th>
																			<?php elseif($category == '4_win_extra'): ?>
																				<th class="text-center col-prize d-none d-lg-table-cell">
																					<span class="d-none d-lg-inline">4 + Extra</span>
																					<span class="d-inline d-lg-none">4+</span>
																				</th>
																			<?php elseif($category == '5_win'): ?>
																				<th class="text-center col-prize">5</th>
																			<?php elseif($category == '5_win_extra'): ?>
																				<th class="text-center col-prize d-none d-lg-table-cell">
																					<span class="d-none d-lg-inline">5 + Extra</span>
																					<span class="d-inline d-lg-none">5+</span>
																				</th>
																			<?php elseif($category == '6_win'): ?>
																				<th class="text-center col-prize">6</th>
																			<?php elseif($category == '6_win_extra'): ?>
																				<th class="text-center col-prize d-none d-lg-table-cell">
																					<span class="d-none d-lg-inline">6 + Extra</span>
																					<span class="d-inline d-lg-none">6+</span>
																				</th>
																			<?php elseif($category == '7_win'): ?>
																				<th class="text-center col-prize d-none d-sm-table-cell">7</th>
																			<?php elseif($category == '7_win_extra'): ?>
																				<th class="text-center col-prize d-none d-lg-table-cell">
																					<span class="d-none d-lg-inline">7 + Extra</span>
																					<span class="d-inline d-lg-none">7+</span>
																				</th>
																			<?php elseif($category == '8_win'): ?>
																				<th class="text-center col-prize d-none d-sm-table-cell">8</th>
																			<?php elseif($category == '8_win_extra'): ?>
																				<th class="text-center col-prize d-none d-lg-table-cell">
																					<span class="d-none d-lg-inline">8 + Extra</span>
																					<span class="d-inline d-lg-none">8+</span>
																				</th>
																			<?php elseif($category == '9_win'): ?>
																				<th class="text-center col-prize d-none d-sm-table-cell">9</th>
																			<?php elseif($category == '9_win_extra'): ?>
																				<th class="text-center col-prize d-none d-lg-table-cell">
																					<span class="d-none d-lg-inline">9 + Extra</span>
																					<span class="d-inline d-lg-none">9+</span>
																				</th>
																			<?php endif; ?>
																		<?php endforeach; ?>
																	<?php endif; ?>
																	<th class="text-center col-points">
																		<span class="d-none d-sm-inline">Points</span>
																		<span class="d-inline d-sm-none">Pts</span>
																	</th>
																</tr>
															</thead>
															<tbody>
																<?php 
																$rank = 1;
																foreach($hwc_winners as $winner): 
																	$hwc_parts = explode('-', $winner['hwc_pattern']);
																	$hot_count = isset($hwc_parts[0]) ? $hwc_parts[0] : 0;
																	$warm_count = isset($hwc_parts[1]) ? $hwc_parts[1] : 0;
																	$cold_count = isset($hwc_parts[2]) ? $hwc_parts[2] : 0;
																?>
																	<tr class="hwc-row" data-hwc="<?= $winner['hwc_pattern'] ?>" data-points="<?= $winner['total_points'] ?>" data-rank="<?= $rank ?>">
																		<td class="text-center font-weight-bold col-rank">
																			<span class="badge badge-secondary"><?= $rank ?></span>
																		</td>
																		<td class="text-center text-danger font-weight-bold col-hot">
																			<span class="badge badge-danger"><?= $hot_count ?></span>
																		</td>
																		<td class="text-center col-separator d-none d-sm-table-cell">-</td>
																		<td class="text-center text-warning font-weight-bold col-warm">
																			<span class="badge badge-warning"><?= $warm_count ?></span>
																		</td>
																		<td class="text-center col-separator d-none d-sm-table-cell">-</td>
																		<td class="text-center text-info font-weight-bold col-cold">
																			<span class="badge badge-info"><?= $cold_count ?></span>
																		</td>
																		<?php foreach($winner['enabled_categories'] as $category): ?>
																			<?php 
																			$cell_classes = "text-center col-prize";
																			if($category == 'extra' || strpos($category, '1_win') === 0) {
																				$cell_classes .= " d-none d-md-table-cell";
																			} else if(strpos($category, '_extra') !== false && $category != 'extra') {
																				$cell_classes .= " d-none d-lg-table-cell";
																			} else if(in_array($category, ['7_win', '8_win', '9_win'])) {
																				$cell_classes .= " d-none d-sm-table-cell";
																			}
																			?>
																			<td class="<?= $cell_classes ?>">
																				<?php $win_count = isset($winner['win_breakdown'][$category]) ? $winner['win_breakdown'][$category] : 0; ?>
																				<?php if($win_count > 0): ?>
																					<span class="badge badge-success"><?= $win_count ?></span>
																				<?php else: ?>
																					<span class="text-muted">0</span>
																				<?php endif; ?>
																			</td>
																		<?php endforeach; ?>
																		<td class="text-center font-weight-bold text-success col-points">
																			<span class="badge badge-success badge-lg"><?= $winner['total_points'] ?></span>
																		</td>
																	</tr>
																<?php 
																	$rank++; 
																endforeach; 
																?>
															</tbody>
														</table>
													</div>
												<?php endif; ?>
											</div>
										</div>
									</div>
								</div>
								
								<?php if(!empty($h_w_c_last_10) || !empty($h_w_c_range)): ?>
							<!-- H-W-C Last 10 and Last Range Tables - matching statistics page style -->
							<?php
								// Parse Last 10 - preserve DB order, include ALL patterns (even zeros)
								$tbl_last10 = array();
								if(!empty($h_w_c_last_10)) {
									foreach(explode(',', $h_w_c_last_10) as $item) {
										$parts = explode('=', trim($item));
										if(count($parts) == 2 && $parts[0] !== '') {
											$tbl_last10[$parts[0]] = (int)$parts[1];
										}
									}
								}
								// Parse Last Range - preserve DB order, include ALL patterns (even zeros)
								$tbl_range = array();
								if(!empty($h_w_c_range)) {
									foreach(explode(',', $h_w_c_range) as $item) {
										$parts = explode('=', trim($item));
										if(count($parts) == 2 && $parts[0] !== '') {
											$tbl_range[$parts[0]] = (int)$parts[1];
										}
									}
								}
							?>
							<div class="row mt-4 justify-content-center">
								<div class="col-12 col-sm-6 col-md-5 mb-3">
									<div class="card border-0 shadow-sm">
										<div class="card-header bg-info text-white py-2">
											<h5 class="card-title mb-0 text-white">Last 10 Draws</h5>
										</div>
										<div class="card-body p-0">
											<?php if(!empty($tbl_last10)): ?>
											<table class="table table-striped table-sm mb-0">
												<thead class="thead-dark">
													<tr>
														<th class="text-center datafont">H - W - C</th>
														<th class="text-center datafont">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($tbl_last10 as $pattern => $count): ?>
														<tr<?= $count > 0 ? ' class="table-success"' : '' ?>>
															<td class="text-center datafont"><?= str_replace('-', ' - ', $pattern) ?></td>
															<td class="text-center datafont">
																<?php if($count > 0): ?>
																	<strong><?= $count ?></strong>
																<?php else: ?>
																	<span class="text-muted">0</span>
																<?php endif; ?>
															</td>
														</tr>
													<?php endforeach; ?>
												</tbody>
											</table>
											<?php else: ?>
												<p class="text-muted p-3 mb-0"><small>No Last 10 data. Please recalculate H-W-C statistics.</small></p>
											<?php endif; ?>
										</div>
									</div>
								</div>
								<div class="col-12 col-sm-6 col-md-5 mb-3">
									<div class="card border-0 shadow-sm">
										<div class="card-header bg-success text-white py-2">
											<h5 class="card-title mb-0 text-white">Last <?= isset($hwc_range) ? $hwc_range : 100 ?> Draws</h5>
										</div>
										<div class="card-body p-0">
											<?php if(!empty($tbl_range)): ?>
											<table class="table table-striped table-sm mb-0">
												<thead class="thead-dark">
													<tr>
														<th class="text-center datafont">H - W - C</th>
														<th class="text-center datafont">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($tbl_range as $pattern => $count): ?>
														<tr<?= $count > 0 ? ' class="table-success"' : '' ?>>
															<td class="text-center datafont"><?= str_replace('-', ' - ', $pattern) ?></td>
															<td class="text-center datafont">
																<?php if($count > 0): ?>
																	<strong><?= $count ?></strong>
																<?php else: ?>
																	<span class="text-muted">0</span>
																<?php endif; ?>
															</td>
														</tr>
													<?php endforeach; ?>
												</tbody>
											</table>
											<?php else: ?>
												<p class="text-muted p-3 mb-0"><small>No range data. Please recalculate H-W-C statistics.</small></p>
											<?php endif; ?>
										</div>
									</div>
								</div>
								<?php endif; ?>
								
							</div>
						</div>
						</div> <!-- END winners tab-pane -->
						</div> <!-- END tab-content -->
						
						<!-- H-W-C Previous Predicted Winners - Always visible at bottom -->
						<?php
						if(!empty($prev_hwc_predictions)):
							// Parse "label|numbers" format (new) or plain numbers (legacy).
							if (strpos($prev_hwc_predictions, '|') !== false) {
								$_prev_parts    = explode('|', $prev_hwc_predictions, 2);
								$_prev_lbl      = $_prev_parts[0];
								$_prev_num_str  = $_prev_parts[1];
							} else {
								// Legacy format: plain numbers stored without a label.
								// We do not know which option was active at snapshot time,
								// so show no label. The correct frozen label will appear
								// automatically after the next draw is imported/entered.
								$_prev_lbl     = '';
								$_prev_num_str = $prev_hwc_predictions;
							}
							$prev_ball_nums = array_filter(array_map('trim', explode(',', $_prev_num_str)), 'strlen');
							// Separate extra/bonus ball from main drawn balls
							$extra_ball_val = ($lottery->extra_ball && isset($lottery->last_drawn['extra']))
								? (string) trim($lottery->last_drawn['extra']) : '';
							$main_winning_balls = array();
							if(!empty($lottery->draw)):
								$balls_drawn_count = (int) $lottery->balls_drawn;
								$draw_idx = 0;
								foreach($lottery->draw as $wb):
									$draw_idx++;
									// Only count it as a main winner if it's within balls_drawn count
									if($draw_idx <= $balls_drawn_count):
										$main_winning_balls[] = (string) trim($wb);
									endif;
								endforeach;
							endif;
						?>
						<div style="margin: 20px; padding: 14px 18px; background-color: #e8f5e9; border-left: 4px solid #28a745; border-radius: 4px; text-align: center;">
							<strong style="color: #000000;">H-W-C Previous Predicted Winners</strong>
							<?php if(!empty($_prev_lbl)): ?>
							<span class="text-muted" style="font-size:0.85em; margin-left:8px;">(<?=htmlspecialchars($_prev_lbl);?>)</span>
							<?php endif; ?>
							<div style="margin-top: 10px; display: flex; flex-wrap: wrap; justify-content: center; gap: 8px;">
								<?php foreach($prev_ball_nums as $pball):
									$pball       = (string) trim($pball);
									$is_bonus    = ($extra_ball_val !== '' && $pball === $extra_ball_val);
									$is_main_win = in_array($pball, $main_winning_balls);
									if ($is_bonus):
										$bg     = '#1565C0'; // blue for bonus ball match
										$color  = '#fff';
										$border = 'border: 2px solid #0d47a1;';
										$title  = 'Bonus/Extra Ball!';
									elseif ($is_main_win):
										$bg     = '#FFD700'; // gold for main ball match
										$color  = '#333';
										$border = 'border: 2px solid #b8860b;';
										$title  = 'Winner!';
									else:
										$bg     = '#28a745'; // green — not drawn
										$color  = '#fff';
										$border = '';
										$title  = '';
									endif;
								?>
								<div title="<?=$title;?>" style="
									display: inline-flex; align-items: center; justify-content: center;
									width: 40px; height: 40px; border-radius: 50%;
									background-color: <?=$bg;?>; color: <?=$color;?>;
									font-weight: bold; font-size: 14px;
									box-shadow: 0 2px 4px rgba(0,0,0,0.25);
									<?=$border;?>
								"><?=$pball;?></div>
								<?php endforeach; ?>
							</div>
							<p style="margin-top: 8px; margin-bottom: 0; font-size: 0.85em; color: #555;">
								<span style="display:inline-block; width:14px; height:14px; background:#FFD700; border-radius:50%; border:1px solid #b8860b; vertical-align:middle;"></span>
								Gold = main ball match &nbsp;
								<?php if($lottery->extra_ball): ?>
								<span style="display:inline-block; width:14px; height:14px; background:#1565C0; border-radius:50%; border:1px solid #0d47a1; vertical-align:middle;"></span>
								Blue = bonus/extra ball match &nbsp;
								<?php endif; ?>
								<span style="display:inline-block; width:14px; height:14px; background:#28a745; border-radius:50%; vertical-align:middle;"></span>
								Green = not drawn
							</p>
						</div>
						<?php endif; ?>
						
						<!-- H-W-C Win Statistics -->
						<?php if(isset($hwc_win_stats)): ?>
	<?php
	// Build ordered prize columns (highest to lowest) filtered by valid categories
	$hwc_prize_cols = array();
	$valid_cats = isset($lottery->valid_prize_categories) ? $lottery->valid_prize_categories : array();
	$balls_drawn = isset($lottery->balls_drawn) ? intval($lottery->balls_drawn) : 9;
	$has_extra = !empty($lottery->extra_ball);
	
	// Start from balls_drawn down to 1, interleaving extra ball prizes by prize hierarchy
	for($i = $balls_drawn; $i >= 1; $i--) {
		$col = $i.'_win';
		// Add current number without extra
		if(in_array($col, $valid_cats)) {
			$hwc_prize_cols[] = array('key' => $col, 'label' => $i, 'title' => $i.' Number'.($i > 1 ? 's' : ''));
		}
		// Add next lower number WITH extra (higher prize than next lower without extra)
		if($i > 1 && $has_extra) {
			$col_lower_extra = ($i-1).'_win_extra';
			if(in_array($col_lower_extra, $valid_cats)) {
				$hwc_prize_cols[] = array('key' => $col_lower_extra, 'label' => ($i-1).'+', 'title' => ($i-1).' Number'.($i > 2 ? 's' : '').' + Extra');
			}
		}
	}
	// Add 1 number alone if not already added (when i=1 in loop, we don't add 0+)
	// Note: It's already added in the loop when i=1
	// Add extra-only at the end (lowest prize)
	if($has_extra && in_array('extra', $valid_cats)) {
		$hwc_prize_cols[] = array('key' => 'extra', 'label' => '+', 'title' => 'Extra Ball Only');
	}
	
	// Calculate draw count
	$draw_count = 0;
	if($hwc_win_stats['startdate'] && $hwc_win_stats['lastdate']) {
		$start = new DateTime($hwc_win_stats['startdate']);
		$end = new DateTime($hwc_win_stats['lastdate']);
		$diff_days = $start->diff($end)->days;
		// Rough estimate based on draw days per week
		$draws_per_week = 0;
		if($lottery->monday) $draws_per_week++;
		if($lottery->tuesday) $draws_per_week++;
		if($lottery->wednesday) $draws_per_week++;
		if($lottery->thursday) $draws_per_week++;
		if($lottery->friday) $draws_per_week++;
		if($lottery->saturday) $draws_per_week++;
		if($lottery->sunday) $draws_per_week++;
		$draw_count = ($draws_per_week > 0) ? max(1, round(($diff_days / 7) * $draws_per_week)) : 1;
	}
	?>
	<style>
		.hwc-win-stats-table { font-size: 0.80em; table-layout: fixed; width: 100%; margin-bottom: 0; background-color: white; }
		.hwc-win-stats-table th { font-size: 0.80em; font-weight: bold; white-space: nowrap; padding: 0.3rem 0.2rem; text-align: center; }
		.hwc-win-stats-table td { padding: 0.25rem 0.2rem; text-align: center; white-space: nowrap; }
		.hwc-win-record-col { width: 22px !important; font-size: 0.80em; white-space: nowrap; }
		.hwc-win-total-col { width: 46px !important; font-size: 0.80em; white-space: nowrap; }
		@media (max-width: 992px) {
			.hwc-win-stats-table { font-size: 0.74em; table-layout: auto; width: auto; min-width: 600px; }
			.hwc-win-stats-table th { font-size: 0.74em; padding: 0.25rem 0.15rem; }
			.hwc-win-stats-table td { padding: 0.2rem 0.15rem; }
			.hwc-win-record-col { width: 20px !important; font-size: 0.74em; }
			.hwc-win-total-col { width: 42px !important; font-size: 0.74em; }
		}
		@media (max-width: 576px) {
			.hwc-win-stats-table { font-size: 0.68em; min-width: 500px; }
			.hwc-win-stats-table th { font-size: 0.68em; padding: 0.2rem 0.1rem; }
			.hwc-win-stats-table td { padding: 0.18rem 0.1rem; }
			.hwc-win-record-col { width: 18px !important; font-size: 0.68em; }
			.hwc-win-total-col { width: 38px !important; font-size: 0.68em; }
		}
	</style>
	<div style="margin: 20px; padding: 18px; background-color: #fff8e1; border-left: 4px solid #ffa000; border-radius: 4px;">
		<div style="text-align: center; margin-bottom: 15px;">
			<strong style="color: #000000; font-size: 1.1em;">
				<i class="fa fa-trophy"></i> H-W-C Prediction Win Records
							</strong>
						</div>
						<div style="margin-bottom: 10px; text-align: center; font-size: 0.9em; color: #666;">
							<?php if($hwc_win_stats['startdate'] || $hwc_win_stats['lastdate']): ?>
								<strong><?php echo ($hwc_win_stats['lastdate'] || $hwc_win_stats['total_winners'] > 0) ? 'Start:' : 'Starting Date:'; ?></strong> <?php echo $hwc_win_stats['startdate'] ? date('l F j, Y', strtotime($hwc_win_stats['startdate'])) : date('l F j, Y', strtotime($lottery->next_draw_date)); ?>
								&nbsp;&nbsp;|
								<?php if($hwc_win_stats['lastdate']): ?>
									<strong>Last Draw Date:</strong> <?php echo date('l F j, Y', strtotime($hwc_win_stats['lastdate'])); ?> (<?php echo $draw_count; ?> draw<?php echo $draw_count != 1 ? 's' : ''; ?>)
								<?php else: ?>
									<strong>Last Draw Date:</strong> None yet
								<?php endif; ?>
							<?php else: ?>
								<strong>Starting Date:</strong> <?php echo date('l F j, Y', strtotime($lottery->next_draw_date)); ?> &nbsp;|&nbsp; <strong>Last Draw Date:</strong> None yet
							<?php endif; ?>
							<button type="button" class="btn btn-sm btn-danger" onclick="resetHWCWinStats(<?php echo $lottery->id; ?>)" 
								style="margin-left: 15px;">
								<i class="fa fa-undo"></i> Reset
							</button>
						</div>
						<div class="table-responsive" style="overflow-x: auto;">
							<table class="table table-bordered table-striped hwc-win-stats-table">
								<thead>
									<tr>
										<th colspan="<?php echo count($hwc_prize_cols) + 1; ?>" class="text-center" style="background-color: #f4f4f4;">
											<strong>Win Record</strong>
										</th>
									</tr>
									<tr>
										<?php foreach($hwc_prize_cols as $col): ?>
											<th class="text-center hwc-win-record-col" style="background-color: #e8f5e8;" title="<?php echo htmlspecialchars($col['title']); ?>"><?php echo htmlspecialchars($col['label']); ?></th>
										<?php endforeach; ?>
										<th class="text-center hwc-win-total-col" style="background-color: #d4edda; font-weight: bold;" title="Total Winners">Total</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<?php foreach($hwc_prize_cols as $col): ?>
											<td class="text-center"><?php echo number_format($hwc_win_stats[$col['key']]); ?></td>
										<?php endforeach; ?>
										<td class="text-center" style="background-color: #fff3cd; font-weight: bold;"><?php echo number_format($hwc_win_stats['total_winners']); ?></td>
								</table>
							</div>
						</div>
						<?php endif; ?>
						
					</div>
				</div>
			</div>
		</div>
	</section>

	<script>
		// Reset H-W-C Win Statistics
		function resetHWCWinStats(lotteryId) {
			if(confirm('WARNING: This will clear all H-W-C prediction win records and reset the statistics.\n\nThe new start date will be set to the next draw date.\n\nAre you sure you want to continue?')) {
				$.ajax({
					url: '<?php echo site_url("admin/history/reset_hwc_win_stats"); ?>',
					type: 'POST',
					data: { lottery_id: lotteryId },
					dataType: 'json',
					success: function(response) {
						if(response.success) {
							alert(response.message || 'H-W-C win statistics reset successfully');
							location.reload();
						} else {
							alert(response.message || 'Error resetting win statistics. Please try again.');
						}
					},
					error: function(xhr, status, error) {
						console.log('AJAX Error:', status, error);
						console.log('Response:', xhr.responseText);
						alert('Error resetting win statistics. Check console for details.\n\nStatus: ' + status + '\nError: ' + error);
					}
				});
			}
		}
	
		let isDescending = true; // Start with descending order (highest points first)
		
		function toggleSort() {
			const table = document.getElementById('hwc-winners-table');
			const tbody = table.getElementsByTagName('tbody')[0];
			const rows = Array.from(tbody.getElementsByTagName('tr'));
			const arrow = document.getElementById('sort-arrow');
			
			// Toggle sort direction
			isDescending = !isDescending;
			
			// Update arrow appearance and direction
			if (isDescending) {
				arrow.textContent = '▲';
				arrow.className = 'sort-arrow desc';
			} else {
				arrow.textContent = '▼';
				arrow.className = 'sort-arrow asc';
			}
			
			// Sort rows based on points (since ranks correspond to points)
			rows.sort((a, b) => {
				const pointsA = parseInt(a.getAttribute('data-points'));
				const pointsB = parseInt(b.getAttribute('data-points'));
				
				if (isDescending) {
					return pointsB - pointsA; // Descending order (highest points first)
				} else {
					return pointsA - pointsB; // Ascending order (lowest points first)
				}
			});
			
			// Clear tbody first
			tbody.innerHTML = '';
			
			// Re-append rows in new order and update rank numbers based on sort direction
			rows.forEach((row, index) => {
				let newRank;
				
				if (isDescending) {
					// Descending: Rank 1 = highest points (best)
					newRank = index + 1;
				} else {
					// Ascending: Rank 1 = lowest points (worst), so reverse the ranking
					newRank = rows.length - index;
				}
				
				const rankCell = row.querySelector('.col-rank');
				rankCell.textContent = newRank;
				row.setAttribute('data-rank', newRank);
				tbody.appendChild(row);
			});
		}
		
		// Enhanced row highlighting with touch and mouse support
		document.addEventListener('DOMContentLoaded', function() {
			const tableRows = document.querySelectorAll('#hwc-winners-table .hwc-row');
			const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
			
			tableRows.forEach(row => {
				// Mouse events for desktop
				row.addEventListener('mouseenter', function() {
					if (!isTouchDevice) {
						highlightRow(this);
					}
				});
				
				row.addEventListener('mouseleave', function() {
					if (!isTouchDevice) {
						unhighlightRow(this);
					}
				});
				
				// Touch events for mobile
				if (isTouchDevice) {
					row.addEventListener('touchstart', function(e) {
						// Clear any existing highlights first
						tableRows.forEach(r => unhighlightRow(r));
						highlightRow(this);
					});
					
					// Add tap gesture for mobile info display
					row.addEventListener('click', function(e) {
						e.preventDefault();
						const hwcPattern = this.getAttribute('data-hwc');
						const points = this.getAttribute('data-points');
						const rank = this.getAttribute('data-rank');
						
						// Show mobile-friendly info modal or toast
						if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
							showToast(`Rank ${rank}: ${hwcPattern} (${points} points)`);
						} else {
							alert(`Rank ${rank}: H-W-C ${hwcPattern} - ${points} points`);
						}
					});
				}
			});
			
			// Clear highlights when touching outside table on mobile
			if (isTouchDevice) {
				document.addEventListener('touchstart', function(e) {
					if (!e.target.closest('#hwc-winners-table')) {
						tableRows.forEach(row => unhighlightRow(row));
					}
				});
			}
			
			function highlightRow(row) {
				row.style.backgroundColor = '#e3f2fd';
				row.style.borderLeft = '4px solid #2196f3';
				row.style.boxShadow = '0 2px 8px rgba(33, 150, 243, 0.3)';
				row.style.transform = 'scale(1.01)';
				
				const hwcPattern = row.getAttribute('data-hwc');
				const points = row.getAttribute('data-points');
				row.title = `H-W-C Pattern: ${hwcPattern} | Total Points: ${points}`;
			}
			
			function unhighlightRow(row) {
				row.style.backgroundColor = '';
				row.style.borderLeft = '';
				row.style.boxShadow = '';
				row.style.transform = '';
				row.title = '';
			}
			
			function showToast(message) {
				// Create and show a bootstrap toast if available
				const toastHtml = `
					<div class="toast" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
						<div class="toast-header">
							<strong class="mr-auto">H-W-C Info</strong>
							<button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
								<span>&times;</span>
							</button>
						</div>
						<div class="toast-body">${message}</div>
					</div>
				`;
				
				const toastElement = document.createElement('div');
				toastElement.innerHTML = toastHtml;
				document.body.appendChild(toastElement.firstElementChild);
				
				// Auto remove after 3 seconds
				setTimeout(() => {
					const toastEl = document.querySelector('.toast');
					if (toastEl) toastEl.remove();
				}, 3000);
			}
			
			// Add scroll management for better UX
			initializeScrollHandling();
			
			function initializeScrollHandling() {
				const tableContainer = document.querySelector('.table-responsive');
				if (!tableContainer) return;
				
				// Check if horizontal scrolling is needed
				function checkScrollNeeded() {
					const needsScroll = tableContainer.scrollWidth > tableContainer.clientWidth;
					
					if (needsScroll) {
						addScrollIndicator(tableContainer);
						addScrollHints(tableContainer);
					}
					
					return needsScroll;
				}
				
				// Initial check
				setTimeout(checkScrollNeeded, 100);
				
				// Check on window resize
				window.addEventListener('resize', function() {
					// Remove existing indicators
					const existingIndicators = document.querySelectorAll('.scroll-indicator');
					existingIndicators.forEach(ind => ind.remove());
					
					setTimeout(checkScrollNeeded, 100);
				});
			}
			
			function addScrollIndicator(container) {
				const indicator = document.createElement('div');
				indicator.className = 'scroll-indicator';
				indicator.innerHTML = '← Scroll to see all columns →';
				container.style.position = 'relative';
				container.appendChild(indicator);
				
				// Hide indicator after scrolling starts
				let scrollTimer;
				container.addEventListener('scroll', function() {
					if (indicator) {
						indicator.style.display = 'none';
					}
					
					// Clear existing timer
					clearTimeout(scrollTimer);
					
					// Show scroll position indicator briefly
					showScrollPosition(container);
				});
			}
			
			function addScrollHints(container) {
				// Add touch/mouse scroll event listeners for better UX
				let isScrolling = false;
				
				container.addEventListener('scroll', function() {
					isScrolling = true;
					
					// Add visual feedback during scrolling
					this.style.boxShadow = '0 0.25rem 0.75rem rgba(0, 123, 255, 0.15)';
					
					// Remove feedback after scrolling stops
					clearTimeout(this.scrollTimer);
					this.scrollTimer = setTimeout(() => {
						this.style.boxShadow = '0 0.125rem 0.25rem rgba(0, 0, 0, 0.075)';
						isScrolling = false;
					}, 150);
				});
				
				// Add keyboard navigation support
				container.addEventListener('keydown', function(e) {
					if (e.key === 'ArrowLeft') {
						e.preventDefault();
						this.scrollLeft -= 50;
					} else if (e.key === 'ArrowRight') {
						e.preventDefault();
						this.scrollLeft += 50;
					}
				});
				
				// Make container focusable for keyboard navigation
				container.setAttribute('tabindex', '0');
			}
			
			function showScrollPosition(container) {
				// Remove existing position indicator
				const existingPos = container.querySelector('.scroll-position');
				if (existingPos) existingPos.remove();
				
				// Create position indicator
				const posIndicator = document.createElement('div');
				posIndicator.className = 'scroll-position';
				posIndicator.style.cssText = `
					position: absolute;
					top: 10px;
					right: 20px;
					background: rgba(0, 0, 0, 0.7);
					color: white;
					padding: 2px 8px;
					border-radius: 10px;
					font-size: 0.7rem;
					z-index: 101;
					pointer-events: none;
				`;
				
				const scrollPercent = Math.round((container.scrollLeft / (container.scrollWidth - container.clientWidth)) * 100);
				posIndicator.textContent = `${scrollPercent}%`;
				
				container.appendChild(posIndicator);
				
				// Remove after 1 second
				setTimeout(() => {
					if (posIndicator && posIndicator.parentNode) {
						posIndicator.remove();
					}
				}, 1000);
			}
		});
	</script>	
