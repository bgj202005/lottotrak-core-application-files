<link href="https://unpkg.com/bootstrap-table@1.18.0/dist/bootstrap-table.min.css" rel="stylesheet">
<link href="https://unpkg.com/bootstrap-table@1.18.0/dist/extensions/reorder-rows/bootstrap-table-reorder-rows.css" rel="stylesheet">
<!-- Bootstrap Form Helper CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-formhelpers/2.3.0/css/bootstrap-formhelpers.min.css">
<!-- Bootstrap Form Helper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-formhelpers/2.3.0/js/bootstrap-formhelpers.min.js"></script>
<style>
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
	.card-title {
		color:#000000;
	}
	.card-text {
		color:steelblue; 
	}
	.card-body {
    width: 100%;
    box-sizing: border-box;
    padding-left: 0.5em;
    padding-right: 0.5em;
	}
	table{
  		border:1px solid black;
  		display:inline-block;
  		max-width: 178px; 
		margin-left: auto;
    	margin-right: auto;
	}
	.table-bordered.text-center {
    margin-left: auto;
    margin-right: auto;
    width: auto;
    display: table;
	}
	.mt-4 .table,
	.generated-tickets-table {
		width: 100% !important;
		max-width: 100% !important;
		margin-left: auto;
		margin-right: auto;
		display: table;
	}
	.form-group label {
        font-weight: bold;
    }
    .form-group span {
        font-size: 1rem;
        color: #555555;
    }
	.preset-option {
        color: #ccc; /* Greyed out */
        pointer-events: none; /* Disable interaction */
    }
    .preset-option.enabled {
        color: #fff; /* Black when enabled */
        pointer-events: auto; /* Enable interaction */
    }
	 .table-responsive {
        overflow-x: visible !important;
        -webkit-overflow-scrolling: touch; /* Smooth scrolling for mobile */
    }
    
    /* Only enable horizontal scroll on very small screens */
    @media (max-width: 576px) {
        .table-responsive {
            overflow-x: auto;
        }
    }
	 .nowrap {
        white-space: nowrap;
    }
	.form-group .col-4 {
        text-align: right;
        white-space: nowrap; /* Prevent text wrapping */
    }
	/* Default style for preset-option */
    .preset-option {
        color: #fff; /* White font color */
        pointer-events: none; /* Disable interaction */
    }
    /* Style for enabled preset-option */
    .preset-option.enabled {
        color: #fff; /* Keep font color white when enabled */
        pointer-events: auto; /* Enable interaction */
    }
	/* Make the table stackable for mobile */
    @media (max-width: 768px) {
        .table thead {
            display: none; /* Hide table headers on small screens */
        }
		.form-group .col-4 {
            text-align: left; /* Align labels to the left for better spacing */
            padding-bottom: 0.5rem; /* Add spacing below labels */
        }
		.form-group .col-6 {
            padding-left: 0;
            padding-right: 0;
        }
        .table tbody tr {
            display: block; /* Make rows block-level elements */
            margin-bottom: 1rem; /* Add spacing between rows */
        }
        .table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            border: 1px solid #ddd;
        }
        .table tbody td::before {
            content: attr(data-label); /* Use the data-label attribute for labels */
            flex: 1;
            font-weight: bold;
            text-align: left;
        }
		.table-section {
			max-width: 5 0vw !important;   /* 50% of the viewport width */
			min-width: 220px;             /* Optional: prevent it from getting too small */
			margin-left: auto;
			margin-right: auto;
			padding: 0.5em !important;
		}
    }
    /* Style the row with checkboxes */
    .checkbox-row {
        background-color: #000; /* Black background */
        color: #fff; /* White text */
    }
    .checkbox-row .preset-checkbox {
        accent-color: #fff; /* White checkbox color */
    }
	#futures-filter-table {
    max-width: 100%;         /* Ensure it doesn't exceed container */
	margin-left: auto;
    margin-right: auto;
	table-layout: auto;
	}
	
	/* Custom combination dropdown styling */
	#wheelingDropdown {
		color: #495057 !important;
		text-color: #495057 !important;
	}
	#wheelingDropdown:hover,
	#wheelingDropdown:focus,
	#wheelingDropdown:active,
	#wheelingDropdown.show {
		color: #495057 !important;
		background-color: white !important;
		border-color: #ced4da !important;
	}
	#wheelingSelectedText {
		color: #495057 !important;
	}
	
	/* Dropdown menu item styling */
	.dropdown-menu .dropdown-item {
		color: #212529 !important;
	}
	.dropdown-menu .dropdown-item:hover,
	.dropdown-menu .dropdown-item:focus {
		color: #212529 !important;
		background-color: #dee2e6 !important;
	}
	.dropdown-menu .dropdown-item.active {
		color: #212529 !important;
		background-color: #ced4da !important;
	}
	
	/* Ensure badges remain visible in dropdown items - more specific selector */
	.dropdown-menu .dropdown-item .badge,
	.dropdown-menu .dropdown-item .badge-success,
	.dropdown-menu .dropdown-item .badge-danger {
		display: inline-block !important;
		opacity: 1 !important;
		visibility: visible !important;
		font-size: 0.75em !important;
		padding: 0.25em 0.4em !important;
		margin-right: 0.25em !important;
	}
	
	/* Force badge colors to remain */
	.dropdown-menu .dropdown-item .badge-success {
		background-color: #28a745 !important;
		color: white !important;
	}
	.dropdown-menu .dropdown-item .badge-danger {
		background-color: #dc3545 !important;
		color: white !important;
	}
	
	/* Status Display Panel Styling */
	.status-display-panel {
		transition: all 0.3s ease-in-out;
		box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15);
	}
	
	.status-display-panel:hover {
		box-shadow: 0 4px 12px rgba(0, 123, 255, 0.25);
	}
	
	.status-info {
		min-height: 40px;
		align-items: center;
	}
	
	.action-icons {
		min-height: 40px;
		align-items: center;
	}
	
	.action-icons i {
		transition: all 0.2s ease;
	}
	
	.action-icons i:hover {
		transform: scale(1.1);
		opacity: 0.8;
	}
	
	/* Responsive adjustments for status display */
	@media (max-width: 768px) {
		.status-display-panel .d-flex {
			flex-direction: column !important;
			align-items: flex-start !important;
		}
		
		.status-info {
			width: 100%;
			margin-bottom: 1rem;
			justify-content: space-between;
		}
		
		.action-icons {
			width: 100%;
			justify-content: space-around;
			padding-top: 0.5rem;
			border-top: 1px solid #dee2e6;
		}
		
		.action-icons i {
			margin: 0 !important;
		}
	}
	
	/* Animate status display appearance */
	#combination-status-display {
		animation: slideIn 0.3s ease-out;
	}
	
	@keyframes slideIn {
		from {
			opacity: 0;
			transform: translateY(-10px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}
	
	/* Mobile-specific CSS */
	@media (max-width: 768px) {
		/* Make form labels full width and left-aligned on mobile */
		.form-group .col-form-label {
			text-align: left !important;
			margin-bottom: 0.5rem;
		}
		
		/* Ensure form controls are touch-friendly */
		.form-control, .btn {
			min-height: 44px;
			font-size: 16px; /* Prevents zoom on iOS */
		}
		
		/* Improve table responsiveness on mobile */
		.table-responsive {
			border: none;
			margin-bottom: 0;
		}
		
		/* Stack table sections with proper spacing */
		.table-section {
			margin-bottom: 1rem !important;
			padding: 0.75rem !important;
		}
		
		/* Optimize table title for mobile */
		.table-title {
			font-size: 1rem !important;
			padding: 0.5rem !important;
			line-height: 1.3;
		}
		
		/* Better button spacing on mobile */
		.btn {
			margin-bottom: 0.5rem;
			touch-action: manipulation;
		}
		
		/* Improve dropdown menu on mobile */
		.dropdown-menu {
			max-height: 250px;
			overflow-y: auto;
			-webkit-overflow-scrolling: touch;
		}
		
		/* Better spacing for form groups */
		.form-group {
			margin-bottom: 1rem;
		}
		
		/* Optimize alerts for mobile */
		.alert {
			font-size: 0.9rem;
			padding: 0.75rem;
		}
	}
	
	/* Tablet-specific optimizations */
	@media (min-width: 768px) and (max-width: 992px) {
		/* Adjust button sizing for tablets */
		.btn-lg {
			padding: 0.75rem 1.5rem;
			font-size: 1.1rem;
		}
		
		/* Optimize table for tablet view */
		.table-section {
			padding: 1rem;
		}
	}
	
	#futures-filter-table th,
	#futures-filter-table td {
		font-size: 0.75em;
		padding: 0.18em 0.25em;
		white-space: nowrap;
	}

	#futures-filter-table select.form-control,
	#futures-filter-table select {
		font-size: 1.02em; /* Reduced by additional 5% from 1.07em (1.07 * 0.95 = 1.0165) */
		padding: 0.12em 0.36em; /* Reduced by 10% from 0.13em and 0.40em */
		min-width: 77px;  /* Reduced by 10% from 86px */
		max-width: 107px;  /* Reduced by 10% from 119px */
		text-overflow: ellipsis;
		overflow: hidden;
		white-space: nowrap;
		background-color: #000 !important;
    	color: #fff !important;
    	border: 1px solid #444;
	}
	#futures-filter-table th,
	#futures-filter-table td,
	#futures-filter-table select,
	#futures-filter-table .form-control {
    text-align: center !important;
	}
	/* Remove horizontal scroll for desktop, keep for mobile only */
	.table-responsive {
		overflow-x: visible;
		width: 100%;
    	margin: 0;
    	padding: 0;
	}
	
	/* Ensure the table section with green border contains all its content */
	.table-section {
		overflow: visible !important;
		position: relative;
		box-sizing: border-box;
	}
	
	/* Ensure the filtering table stays within its container */
	#futures-filter-table {
		width: 100%;
		table-layout: auto;
		margin: 0 auto;
		overflow: visible;
	}
	
	/* Specific column width optimization for filtering table with extra ball */
	#futures-filter-table.with-extra-ball th:nth-child(1) { width: 6%; }   /* H-W-C Selection */
	#futures-filter-table.with-extra-ball th:nth-child(2) { width: 6%; }   /* Extra Ball Filter */
	#futures-filter-table.with-extra-ball th:nth-child(3) { width: 6%; }   /* After Ball */
	#futures-filter-table.with-extra-ball th:nth-child(4) { width: 6%; }   /* Position */
	#futures-filter-table.with-extra-ball th:nth-child(5) { width: 5%; }   /* Friends */
	#futures-filter-table.with-extra-ball th:nth-child(6) { width: 5%; }   /* Trends */
	#futures-filter-table.with-extra-ball th:nth-child(7) { width: 5%; }   /* Sums */
	#futures-filter-table.with-extra-ball th:nth-child(8) { width: 8%; }   /* Digit Sums */
	#futures-filter-table.with-extra-ball th:nth-child(9) { width: 8%; }   /* Repeaters */
	#futures-filter-table.with-extra-ball th:nth-child(10) { width: 9%; }  /* Consecutives */
	#futures-filter-table.with-extra-ball th:nth-child(11) { width: 6%; }  /* Odd/Even */
	#futures-filter-table.with-extra-ball th:nth-child(12) { width: 6%; }  /* Decades */
	#futures-filter-table.with-extra-ball th:nth-child(13) { width: 5%; }  /* Last */
	#futures-filter-table.with-extra-ball th:nth-child(14) { width: 6%; }  /* Range */
	#futures-filter-table.with-extra-ball th:nth-child(15) { width: 6%; }  /* Adjacent */
	
	/* Special compact styling for extra ball tables */
	#futures-filter-table.with-extra-ball select {
		font-size: 0.92em !important; /* Increased by additional 10% from 0.84em (32% total increase) */
		padding: 0.20rem 0.40rem !important; /* Increased by additional 10% */
		max-width: 100%;
		min-width: 0;
		min-height: 31px; /* Added minimum height for better usability, increased by 10% */
	}
	
	#futures-filter-table.with-extra-ball td {
		padding: 0.2rem !important;
		font-size: 0.75em;
	}
	
	#futures-filter-table.with-extra-ball th {
		padding: 0.3rem 0.2rem !important;
		font-size: 0.8em;
		line-height: 1.1;
	}
	
	/* Regular table without extra ball column */
	#futures-filter-table:not(.with-extra-ball) th:nth-child(1) { width: 8%; }   /* H-W-C Selection */
	#futures-filter-table:not(.with-extra-ball) th:nth-child(2) { width: 8%; }   /* After Ball */
	#futures-filter-table:not(.with-extra-ball) th:nth-child(3) { width: 8%; }   /* Position */
	#futures-filter-table:not(.with-extra-ball) th:nth-child(4) { width: 7%; }   /* Friends */
	#futures-filter-table:not(.with-extra-ball) th:nth-child(5) { width: 7%; }   /* Trends */
	#futures-filter-table:not(.with-extra-ball) th:nth-child(6) { width: 7%; }   /* Sums */
	#futures-filter-table:not(.with-extra-ball) th:nth-child(7) { width: 9%; }   /* Digit Sums */
	#futures-filter-table:not(.with-extra-ball) th:nth-child(8) { width: 9%; }   /* Repeaters */
	#futures-filter-table:not(.with-extra-ball) th:nth-child(9) { width: 10%; }  /* Consecutives */
	#futures-filter-table:not(.with-extra-ball) th:nth-child(10) { width: 7%; }  /* Odd/Even */
	#futures-filter-table:not(.with-extra-ball) th:nth-child(11) { width: 7%; }  /* Decades */
	#futures-filter-table:not(.with-extra-ball) th:nth-child(12) { width: 6%; }  /* Last */
	#futures-filter-table:not(.with-extra-ball) th:nth-child(13) { width: 7%; }  /* Range */
	#futures-filter-table:not(.with-extra-ball) th:nth-child(14) { width: 7%; }  /* Adjacent */
	
	/* Make sure dropdown menus stay within the green border */
	.table-section .table-responsive {
		overflow-x: auto;
		overflow-y: visible;
		position: relative;
		z-index: 1;
		max-width: 100%;
	}
	
	/* Adjust dropdown positioning to stay within container */
	.table-section select.form-control {
		position: relative;
		z-index: 10;
		max-width: 100%;
		width: 100%;
		box-sizing: border-box;
		font-size: 0.85em;
		padding: 0.25rem 0.5rem;
	}
	
	/* Ensure the filtering table dropdowns don't overflow */
	#futures-filter-table td {
		position: relative;
		overflow: visible;
		padding: 0.3rem !important;
		max-width: 0; /* Force content to respect column widths */
	}
	
	#futures-filter-table select {
		max-width: 100%;
		width: 100%;
		box-sizing: border-box;
		white-space: nowrap;
		text-overflow: ellipsis;
		font-size: 1.06em; /* Increased by additional 10% from 0.96em (32% total increase) */
		padding: 0.26rem 0.53rem; /* Increased by additional 10% */
		border: 1px solid #ced4da;
		border-radius: 0.25rem;
		min-width: 0; /* Allow shrinking below content width */
		min-height: 35px; /* Increased dropdown height by additional 10% */
	}
	
	/* Better table container for filtering table */
	.table-section:has(#futures-filter-table) {
		overflow: hidden !important;
		position: relative;
		max-width: 100%;
	}
	
	.table-section:has(#futures-filter-table) .table-responsive {
		overflow-x: auto;
		overflow-y: visible;
		margin: 0;
		padding: 0;
		max-width: 100%;
	}
	
	/* Ensure table itself doesn't exceed container */
	#futures-filter-table {
		max-width: 100% !important;
		width: 100% !important;
		table-layout: fixed !important;
		margin: 0 !important;
	}
	
	/* Results table column width optimization */
	#generated-tickets-table th:nth-child(1) { width: 6%; }    /* # */
	#generated-tickets-table th:nth-child(2) { width: 22%; }   /* Combination */
	#generated-tickets-table th:nth-child(3) { width: 7%; }    /* Sum */
	#generated-tickets-table th:nth-child(4) { width: 9%; }    /* Digit Sum */
	#generated-tickets-table th:nth-child(5) { width: 9%; }    /* Repeaters */
	#generated-tickets-table th:nth-child(6) { width: 11%; }   /* Consecutives */
	#generated-tickets-table th:nth-child(7) { width: 7%; }    /* Odd */
	#generated-tickets-table th:nth-child(8) { width: 7%; }    /* Even */
	#generated-tickets-table th:nth-child(9) { width: 8%; }    /* Decade */
	#generated-tickets-table th:nth-child(10) { width: 7%; }   /* Last */
	#generated-tickets-table th:nth-child(11) { width: 7%; }   /* Range */
	
	#generated-tickets-table {
		max-width: 100% !important;
		width: 100% !important;
		table-layout: fixed !important;
		margin: 0 !important;
	}
	
	#generated-tickets-table th {
		text-overflow: ellipsis;
		white-space: nowrap;
		overflow: hidden;
		padding: 0.5rem 0.25rem !important;
		font-size: 0.9rem;
	}
	
	#generated-tickets-table td {
		text-overflow: ellipsis;
		white-space: nowrap;
		overflow: hidden;
		padding: 0.4rem 0.25rem !important;
	}
	
	@media (max-width: 991px) {
		.table-responsive {
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
		}
		#futures-filter-table {
			margin-left: auto !important;
			margin-right: auto !important;
			display: table;
		}
		#futures-filter-table th, #futures-filter-table td {
			font-size: 0.7em;
			padding: 0.12em;
		}
		#futures-filter-table select.form-control,
		#futures-filter-table select {
			font-size: 0.7em;
			padding: 0.08em 0.2em;
		}
		
		/* Even more compact for extra ball tables on mobile */
		#futures-filter-table.with-extra-ball th,
		#futures-filter-table.with-extra-ball td {
			font-size: 0.6em !important;
			padding: 0.1em !important;
		}
		#futures-filter-table.with-extra-ball select {
			font-size: 0.79em !important; /* Increased by additional 10% from 0.72em (32% total increase) */
			padding: 0.07em 0.13em !important; /* Increased by additional 10% */
			min-height: 26px; /* Added minimum height for mobile, increased by 10% */
		}
	}
	@media (max-width: 767px) {
		.form-group.text-center.mt-3 button,
		.form-group.text-center.mt-3 input[type="submit"] {
			margin-bottom: 0.7em;
			padding-left: 1.2em;
			padding-right: 1.2em;
			width: 100%;
			max-width: 100%;
			box-sizing: border-box;
		}
		.form-group.text-center.mt-3 {
			padding-left: 0.5em;
			padding-right: 0.5em;
		}
	}
	/* Add this to your style section */
	
	/* Lottery outdated greyed-out styles */
	.lottery-outdated-disabled {
		opacity: 0.5;
		pointer-events: none;
		background-color: #f8f9fa;
		border: 1px solid #dee2e6;
		position: relative;
	}
	
	.lottery-outdated-disabled::before {
		content: "LOTTERY OUT OF DATE - UPDATE REQUIRED";
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		background-color: rgba(220, 53, 69, 0.9);
		color: white;
		padding: 0.5rem 1rem;
		border-radius: 0.25rem;
		font-weight: bold;
		font-size: 0.9rem;
		z-index: 1000;
		white-space: nowrap;
		text-align: center;
		box-shadow: 0 2px 4px rgba(0,0,0,0.2);
	}
	
	.lottery-outdated-disabled input,
	.lottery-outdated-disabled select,
	.lottery-outdated-disabled button,
	.lottery-outdated-disabled textarea {
		opacity: 0.6;
		cursor: not-allowed;
	}
	.d-flex {
    display: flex;
    align-items: center;
    gap: 0.2em;
	}
	
	/* Compress spacing for radio button + dropdown combinations */
	.d-flex input[type="radio"] {
		margin-right: 0.1em;
		flex-shrink: 0;
		transform: scale(0.8); /* Make radio buttons smaller for extra ball tables */
	}
	
	.d-flex select {
		flex: 1;
		min-width: 0;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	
	/* Special handling for extra ball table radio/dropdown combos */
	#futures-filter-table.with-extra-ball .d-flex {
		gap: 0.1em;
	}
	
	#futures-filter-table.with-extra-ball .d-flex input[type="radio"] {
		transform: scale(0.7);
		margin-right: 0.05em;
	}
	
	/* Styling for independent extra ball display */
	.main-numbers {
		font-weight: bold;
		color: #333;
	}
	
	.extra-separator {
		font-weight: bold;
		color: #28a745;
		margin: 0 0.3em;
		font-size: 1.1em;
	}
	
	.extra-ball {
		font-weight: bold;
		color: #dc3545;
		background-color: #fff2f2;
		padding: 0.1em 0.4em;
		border-radius: 4px;
		border: 1px solid #dc3545;
		font-size: 0.95em;
	}
	
	/* Mobile responsive adjustments for extra ball display */
	@media (max-width: 768px) {
		.main-numbers, .extra-ball {
			font-size: 0.85em;
		}
		
		.extra-separator {
			margin: 0 0.2em;
			font-size: 1em;
		}
		
		.extra-ball {
			padding: 0.05em 0.3em;
		}
	}
	#h_w_c_group.greyed-out {
    background-color: #444 !important;
    color: #ccc !important;
    cursor: not-allowed;
    opacity: 0.7;
	}
	#ball_points.greyed-out,
	#position_points.greyed-out {
		background-color: #444 !important;
		color: #ccc !important;
		cursor: not-allowed;
		opacity: 0.7;
	}
	#friends.greyed-out {
		background-color: #444 !important;
		color: #ccc !important;
		cursor: not-allowed;
		opacity: 0.7;
	}
	/* Center text for specific columns in Generated Combination Tickets Table */
	.generated-tickets-table tbody td[data-label="Total Sum"],
	.generated-tickets-table tbody td[data-label="Digit Sum"],
	.generated-tickets-table tbody td[data-label="Repeaters"],
	.generated-tickets-table tbody td[data-label="Consecutives"],
	.generated-tickets-table tbody td[data-label="Even"],
	.generated-tickets-table tbody td[data-label="Odd"],
	.generated-tickets-table tbody td[data-label="Decade"],
	.generated-tickets-table tbody td[data-label="Last"],
	.generated-tickets-table tbody td[data-label="Range"] {
		text-align: center !important;
	}
	/* Also center the corresponding header columns */
	.generated-tickets-table thead th:nth-child(3),  /* Sum */
	.generated-tickets-table thead th:nth-child(4),  /* Digit Sum */
	.generated-tickets-table thead th:nth-child(5),  /* Repeaters */
	.generated-tickets-table thead th:nth-child(6),  /* Consecutive */
	.generated-tickets-table thead th:nth-child(7),  /* Even */
	.generated-tickets-table thead th:nth-child(8),  /* Odd */
	.generated-tickets-table thead th:nth-child(9),  /* Decade */
	.generated-tickets-table thead th:nth-child(10), /* Last */
	.generated-tickets-table thead th:nth-child(11)  /* Range */ {
		text-align: center !important;
	}
	/* Bootstrap Table filter control styling */
	.filter-control input,
	.filter-control select {
		font-size: 0.85em;
		padding: 0.2em 0.5em;
		border: 1px solid #ccc;
		border-radius: 4px;
		width: 100%;
		box-sizing: border-box;
	}
	.filter-control {
		padding: 0.3em !important;
	}
	/* Style for filter dropdowns */
	.filter-control select {
		background-color: #f8f9fa;
		color: #333;
		border: 1px solid #ced4da;
	}
	.filter-control select:focus,
	.filter-control input:focus {
		border-color: #007bff;
		box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
		outline: 0;
	}
	
	<?php if (!empty($lottery) && $lottery->duplicate_extra_ball == 0): ?>
	/* Reduce Win History Filtering dropdown sizes for regular lotteries (duplicate_extra_ball = 0) */
	#futures-filter-table .form-control {
		font-size: 0.9em !important; /* Additional 10% reduction for regular lotteries */
		padding: 0.1em 0.3em !important; /* Reduced padding */
		min-width: 65px !important; /* Smaller minimum width */
		max-width: 85px !important; /* Smaller maximum width */
	}
	
	/* Specific styling for After Ball dropdown to show Position radio button */
	#futures-filter-table td[data-label="After Ball"] .form-control,
	#futures-filter-table td:nth-child(2) .form-control {
		max-width: 70px !important; /* Even smaller for After Ball to prevent hiding Position radio */
		width: 70% !important; /* Reduce width to create space */
	}
	
	/* Ensure Position radio button is visible */
	#futures-filter-table input[type="radio"] {
		display: inline-block !important;
		visibility: visible !important;
		margin-left: 0.3em !important;
		margin-right: 0.3em !important;
	}
	
	/* Improve spacing for radio button containers */
	#futures-filter-table .d-flex {
		gap: 0.2em !important;
		justify-content: flex-start !important;
	}
	<?php endif; ?>
	
	/* Fix for filtering table layout */
	#futures-filter-table {
		max-width: 100% !important;
		width: 100% !important;
		display: table !important;
		table-layout: fixed !important;
		margin: 0 !important;
	}
	
	/* Ensure the table section contains its content properly */
	.table-section {
		overflow: hidden;
		position: relative;
		max-width: 100%;
		box-sizing: border-box;
	}
	
	/* Button state styling */
	.btn:disabled {
		opacity: 0.5 !important;
		cursor: not-allowed !important;
	}
	
	.btn:not(:disabled) {
		opacity: 1 !important;
		cursor: pointer !important;
	}
	
	/* Progress bar styling */
	.progress {
		height: 25px;
		background-color: #e9ecef;
		border-radius: 0.375rem;
		overflow: hidden;
	}
	
	.progress-bar {
		display: flex;
		flex-direction: column;
		justify-content: center;
		color: #fff;
		text-align: center;
		white-space: nowrap;
		background-color: #007bff;
		transition: width 0.3s ease;
	}
	
	/* Generated Combination Tickets table optimization */
	.generated-tickets-table {
		table-layout: fixed !important;
		word-wrap: break-word;
	}
	
	/* Column width optimization for Generated Combination Tickets */
	.generated-tickets-table th:nth-child(1) { width: 5%; }   /* # */
	.generated-tickets-table th:nth-child(2) { width: 25%; }  /* Combination */
	.generated-tickets-table th:nth-child(3) { width: 7%; }   /* Sum */
	.generated-tickets-table th:nth-child(4) { width: 8%; }   /* Digit Sum */
	.generated-tickets-table th:nth-child(5) { width: 8%; }   /* Repeaters */
	.generated-tickets-table th:nth-child(6) { width: 9%; }   /* Consecutive */
	.generated-tickets-table th:nth-child(7) { width: 6%; }   /* Odd */
	.generated-tickets-table th:nth-child(8) { width: 6%; }   /* Even */
	.generated-tickets-table th:nth-child(9) { width: 8%; }   /* Decade */
	.generated-tickets-table th:nth-child(10) { width: 6%; }  /* Last */
	.generated-tickets-table th:nth-child(11) { width: 8%; }  /* Range */
	
	/* Responsive adjustments for Generated Combination Tickets */
	@media (max-width: 1200px) {
		.generated-tickets-table th:nth-child(2) { width: 20%; }  /* Combination */
		.generated-tickets-table th:nth-child(3) { width: 8%; }   /* Sum */
		.generated-tickets-table th:nth-child(4) { width: 9%; }   /* Digit Sum */
	}
	
	@media (max-width: 768px) {
		.generated-tickets-table {
			font-size: 0.85em;
		}
		.generated-tickets-table th:nth-child(1) { width: 8%; }   /* # */
		.generated-tickets-table th:nth-child(2) { width: 30%; }  /* Combination */
	}
</style>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<!-- Bootstrap Table JS -->
	<script src="https://unpkg.com/bootstrap-table@1.18.0/dist/bootstrap-table.min.js"></script>
	<script src="https://unpkg.com/bootstrap-table@1.18.0/dist/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>
	<h2><?php echo 'Prediction Futures for: '.$lottery->lottery_name; ?></h2>
	<h5 style = "text-align:left"><?php echo anchor('admin/predictions', 'Back to Predictions Dashboard', 'title="Back to Predictions"'); ?></h5>
		<section>
			<div class="container mt-4">
				<!-- White Tile (Card) -->
				<div class="card shadow-sm">
					<div class="card-body">
						<h3 class="card-title text-center">Prediction Futures</h3>
						
						<!-- Flash Messages -->
						<?php if ($this->session->flashdata('success_message')): ?>
							<div class="alert alert-success alert-dismissible fade show" role="alert">
								<?= $this->session->flashdata('success_message') ?>
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
						<?php endif; ?>
						
						<?php if ($this->session->flashdata('error_message')): ?>
							<div class="alert alert-danger alert-dismissible fade show" role="alert">
								<?= $this->session->flashdata('error_message') ?>
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
						<?php endif; ?>
						
						<?php if ($this->session->flashdata('predictions_alert')): ?>
							<div class="alert alert-warning alert-dismissible fade show" role="alert">
								<i class="fas fa-exclamation-triangle"></i> <?= $this->session->flashdata('predictions_alert') ?>
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
						<?php endif; ?>
						
						<!-- Lottery Out of Date Warning -->
						<?php if (!empty($lottery_outdated) && $lottery_outdated): ?>
							<div class="alert alert-danger alert-dismissible fade show" role="alert">
								<i class="fas fa-exclamation-circle"></i> 
								<strong><?= htmlspecialchars($lottery->lottery_name) ?> is out of date by <?= $draws_behind ?> draw<?= $draws_behind > 1 ? 's' : '' ?>.</strong>
								Import or manually add the new draws on the Lotteries Edit or Lotteries Import page.
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
						<?php endif; ?>
						
						<?php if (!empty($message)) ?> <h3 class="bg-warning" style = "text-align:center;"><?=$message; ?></h3>
						
						<!-- Friendship Warning Messages -->
						<?php if (!empty($friendship_warning)): ?>
							<div class="alert alert-info alert-dismissible fade show" role="alert">
								<i class="fas fa-info-circle"></i> <?= $friendship_warning ?>
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
						<?php endif; ?>
						<?php echo validation_errors('<H2><div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">','</div></H2>'); ?>
						<div class="prediction-form-container <?= (!empty($lottery_outdated) && $lottery_outdated) ? 'lottery-outdated-disabled' : '' ?>">
						<?php echo form_open(base_url().'admin/predictions/combination/'.$lottery->id); ?>
						<hr>
						<!-- Country -->
						<div class="form-group row justify-content-center">
							<?php
							$extra = ['class' => 'col-sm-4 col-12 col-form-label col-form-label-md text-sm-right text-left'];
							echo form_label('Country:', 'country', $extra);
							?>
							<div class="col-sm-6 col-12" style = "margin-top: 0.5em;">
								<span id="country-name"></span>
							</div>
						</div>
						<!-- Province/State -->
						<div class="form-group row justify-content-center">
							<?php
							$extra = ['class' => 'col-sm-4 col-12 col-form-label col-form-label-md text-sm-right text-left'];
							echo form_label('Province/State:', 'province', $extra);
							?>
							<div class="col-sm-6 col-12" style = "margin-top: 0.5em;">
								<span id="state-name"></span>
							</div>
						</div>
						<div class="form-group row justify-content-center">
							<?php
							// Label for the dropdown
							$extra = ['class' => 'col-sm-4 col-12 col-form-label col-form-label-md text-sm-right text-left'];
							echo form_label('Combination Table:', 'wheeling', $extra);
							?>
							<div class="col-sm-6 col-12">
								<?php
								// For the selected value, we need to check if it matches the filename part
								$selected_value = '';
								if (isset($selected_wheeling)) {
									// If selected_wheeling is just a filename, find the matching id|filename value
									if (!empty($combination_files)) {
										foreach ($combination_files as $file) {
											$value = $file['id'] . '|' . $file['file_name'];
											if (strpos($value, '|') !== false) {
												list($option_id, $option_filename) = explode('|', $value, 2);
												if ($option_filename === $selected_wheeling) {
													$selected_value = $value;
													break;
												}
											}
										}
									}
									// If no match found, use the original selected_wheeling value
									if (empty($selected_value)) {
										$selected_value = $selected_wheeling;
									}
								} else {
									$selected_value = set_value('wheeling', '');
								}
								
								// Build custom dropdown with HTML badge support
								$disabled_attr = !empty($disable_combination_dropdown) ? 'disabled' : '';
								?>
								<div class="dropdown" style="width: 80%;">
									<button class="btn btn-outline-secondary dropdown-toggle form-control text-left" type="button" id="wheelingDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: white; border: 1px solid #ced4da; color: #495057 !important; width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-height: 38px; touch-action: manipulation;" <?= $disabled_attr ?>>
										<span id="wheelingSelectedText">
											<?php 
											if (!empty($selected_value)) {
												// Find the selected file and display its text without status badge
												foreach ($combination_files as $file) {
													$value = $file['id'] . '|' . $file['file_name'];
													if ($value === $selected_value) {
														// Don't show status for the currently selected item
														echo '(' . htmlspecialchars($file['file_name']) . ') ' . $file['N'] . ' Numbers - ' . number_format($file['CCCC']) . ' Tickets';
														break;
													}
												}
											} else {
												echo 'Select Combination Table';
											}
											?>
										</span>
									</button>
									<div class="dropdown-menu" aria-labelledby="wheelingDropdown" style="width: 100%; max-height: 300px; overflow-y: auto;">
										<?php if (!empty($combination_files)): ?>
											<?php foreach ($combination_files as $file): ?>
												<?php 
												$value = $file['id'] . '|' . $file['file_name']; // e.g., "246|060828"
												$status_badge = '';
												$selected_class = ($selected_value === $value) ? 'active' : '';
												$is_selected = ($selected_value === $value);
												
												// More robust check for active status - handle string/int/bool values
												$is_active = false;
												if (isset($file['active'])) {
													$active_val = $file['active'];
													$is_active = ($active_val == 1 || $active_val === '1' || $active_val === 1 || $active_val === true);
												}
												
												// Check if a filter was actually saved (has saved_filter_file_name)
												$has_saved_filter = !empty($file['saved_filter_file_name']);
												
												// Only show status badge if a filter was actually saved AND this is NOT the currently selected item
												if ($has_saved_filter && !$is_selected) {
													if ($is_active) {
														$status_badge = '<span class="badge badge-success" style="background-color: #28a745; color: white;">Active</span> ';
													} else {
														// Show Expired for filters that were saved and checked in prize history
														$status_badge = '<span class="badge badge-danger" style="background-color: #dc3545; color: white;">Expired</span> ';
													}
												}
												$display = $status_badge . '(' . htmlspecialchars($file['file_name']) . ') ' . $file['N'] . ' Numbers - ' . number_format($file['CCCC']) . ' Tickets';
												?>
												<a class="dropdown-item <?= $selected_class ?>" href="#" data-value="<?= htmlspecialchars($value) ?>" onclick="selectCombination('<?= htmlspecialchars($value, ENT_QUOTES) ?>', this.innerHTML); return false;"><?= $display ?></a>
											<?php endforeach; ?>
										<?php endif; ?>
									</div>
								</div>
								<!-- Hidden input to store the selected value -->
								<input type="hidden" id="wheeling" name="wheeling" value="<?= htmlspecialchars($selected_value) ?>">
								<?php
								// Display form error if any
								echo form_error('wheeling', '<div class="bg-warning mt-2 p-2 text-center text-white">', '</div>');
								?>
							</div>
						</div>
						
						<!-- Status Display Area (shown when dropdown selection is made) -->
						<div id="combination-status-display" class="row mt-3" style="display: none;">
							<div class="col-12">
								<div class="status-display-panel" style="border: 2px solid #007bff; border-radius: 8px; background: #f8f9fa; padding: 1em; margin-bottom: 1em;">
									<div class="d-flex align-items-center justify-content-between flex-wrap">
										<div class="status-info d-flex align-items-center flex-wrap">
											<span id="status-badge" class="badge badge-success mr-3" style="font-size: 0.9rem;">Active</span>
											<span id="tickets-count" class="mr-3" style="color: #28a745; font-weight: bold;">Filtered Tickets: 0</span>
										</div>
										<div class="action-icons d-flex align-items-center">
											<i id="restore-icon" class="fa fa-eye fa-2x mr-2" title="Restore previous Combination Filter Settings" style="color: #007bff; cursor: pointer;" onclick="restoreFromStatus()"></i>
											<i id="winners-icon" class="fa fa-money fa-2x mr-2" title="View Combination Ticket Winners" style="color: #28a745; cursor: pointer;" onclick="viewWinnersFromStatus()"></i>
											<i id="delete-icon" class="fa fa-trash-o fa-2x" title="Delete this file and Combination Table Filtered Tickets" style="color: #dc3545; cursor: pointer;" onclick="deleteFromStatus()"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-12">
								<!-- First Table: Presets Control Panel -->
								<div class="table-section" style="border:2px solid #007bff; border-radius:8px; margin-bottom:2em; padding:1em;">
									<div class="table-title" style="font-weight:bold; font-size:1.2em; background:#f8f9fa; border-bottom:1px solid #007bff; padding:0.5em 1em; border-radius:6px 6px 0 0; margin:-1em -1em 1em -1em;">
										LOTTERY PROFILE STATISTICS PRESETS CONTROL PANEL
									</div>		
										<div class="table-responsive">
											<table class="table table-bordered text-center">
												<thead>
													<tr>
														<th class="nowrap">H-W-C</th>
														<th>Range</th>
														<th>Extra Draws?</th>
														<th>Includes Extra?</th>
														<th class="nowrap">Followers</th>
														<th>Range</th>
														<th>Extra Draws?</th>
														<th>Includes Extra?</th>
														<th class="nowrap">Friends</th>
														<th>Range</th>
														<th>Extra Draws?</th>
														<th>Includes Extra?</th>
													</tr>
												</thead>
												<tbody>
													<tr class="checkbox-row">
														<!-- H-W-C -->
														<td data-label="H-W-C">
															<?php
															// Always allow checkbox to be interactable
															$js = 'id="hwc-checkbox" class="preset-checkbox"';
															echo form_checkbox('hwc', '1', !empty($selected_hwc), $js);
															?>
														</td>
														<td data-label="Range"><?php echo $h_w_c['range']; ?></td>
														<td data-label="Extra Draws?"> <span class="preset-option disabled"><?php echo $h_w_c['extra_draws'] ? '✓' : ''; ?></span></td>
														<td data-label="Includes Extra?"><span class="preset-option disabled"><?php echo $h_w_c['extra_included'] ? '✓' : ''; ?></span></td>
														<!-- Followers -->
														<td data-label="Followers">
															<?php
															// Always allow checkbox to be interactable
															$js = 'id="followers-checkbox" class="preset-checkbox"';
															echo form_checkbox('followers', '1', !empty($selected_followers), $js);
															?>
														</td>
														<td data-label="Range"><?php echo $followers['range']; ?></td>
														<td data-label="Extra Draws?"><span class="preset-option disabled"><?php echo $followers['extra_draws'] ? '✓' : ''; ?></span></td>
														<td data-label="Includes Extra?"><span class="preset-option disabled"><?php echo $followers['extra_included'] ? '✓' : ''; ?></span></td>
														<!-- Friends -->
														<td data-label="Friends">
															<?php
															// Always allow checkbox to be interactable
															$js = 'id="friends-checkbox" class="preset-checkbox"';
															echo form_checkbox('friends', '1', !empty($selected_friends_checkbox), $js);
															?>
														</td>
														<td data-label="Range"><?php echo $friends['range']; ?></td>
														<td data-label="Extra Draws?"><span class="preset-option disabled"><?php echo $friends['extra_draws'] ? '✓' : ''; ?></span></td>
														<td data-label="Includes Extra?"><span class="preset-option disabled"><?php echo $friends['extra_included'] ? '✓' : ''; ?></span></td>
													</tr>
												</tbody>
											</table>
										</div>
								</div>		
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<!-- Second Table: Actual Win History Filtering -->
								<div class="table-section" style="border:2px solid #28a745; border-radius:8px; margin-bottom:2em; padding:1em; overflow: hidden; position: relative;">
									<div class="table-title" style="font-weight:bold; font-size:1.2em; background:#f8f9fa; border-bottom:1px solid #28a745; padding:0.5em 1em; border-radius:6px 6px 0 0; margin:-1em -1em 1em -1em;">
										Actual Win History Filtering for <?= htmlspecialchars(isset($lottery->next_draw_date) ? $lottery->next_draw_date : 'Next Draw'); ?>
									</div>
									
									<!-- Table responsive container -->
									<div class="table-responsive" style="overflow-x: auto; overflow-y: visible; max-width: 100%;">
										<table class="table table-bordered<?php echo (isset($is_independent_extra_ball) && $is_independent_extra_ball) ? ' with-extra-ball' : ''; ?>" id="futures-filter-table" style="white-space: nowrap; margin: 0; width: 100%; max-width: 100%;">
											<thead>
												<tr>
													<th>H-W-C</th>
													<?php if (isset($is_independent_extra_ball) && $is_independent_extra_ball): ?>
													<th>Extra Ball</th>
													<?php endif; ?>
													<th>After Ball</th>
													<th>Position</th>
													<th>Friends</th>
													<th>Trends</th>
													<th>Sums</th>
													<th>Digit Sums</th>
													<th>Repeaters</th>
													<th>Consecutives</th>
													<th>Odd/Even</th>
													<th>Decades</th>
													<th>Last</th>
													<th>Range</th>
													<th>Adjacent</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td data-label="H-W-C Selection">
														<?php 
														$hwc_disabled = isset($disable_hwc_dropdown) && $disable_hwc_dropdown ? ' disabled' : '';
														?>
														<?= form_dropdown('h_w_c_group', $h_w_c_group, isset($selected_h_w_c_group) ? $selected_h_w_c_group : '', 'class="form-control" id="h_w_c_group"' . $hwc_disabled) ?>
													</td>
													<?php if (isset($is_independent_extra_ball) && $is_independent_extra_ball): ?>
													<td data-label="Extra Ball Filter">
														<?php 
														$extra_ball_options = ['ALL' => 'ALL'];
														foreach ($extra_ball_occurrences as $occurrence) {
															$extra_ball_options[$occurrence['value']] = $occurrence['display'];
														}
														?>
														<?= form_dropdown('extra_ball_filter', $extra_ball_options, isset($selected_extra_ball) ? $selected_extra_ball : 'ALL', 'class="form-control" id="extra_ball_filter"') ?>
													</td>
													<?php endif; ?>
													<td data-label="After Ball">
														<div class="d-flex align-items-center" style="gap:0.4em;">
															<?php 
															$followers_disabled = isset($disable_followers_controls) && $disable_followers_controls;
															$radio_disabled = $followers_disabled ? ['disabled' => true] : [];
															$dropdown_disabled = $followers_disabled ? ' disabled' : '';
															?>
															<?= form_radio(array_merge([
																	'name' => 'followers_type',
																	'id' => 'after_ball_radio',
																	'value' => 'after_ball',
																	'checked' => (isset($selected_followers_type) && $selected_followers_type == 'after_ball')
																], $radio_disabled)); ?>
															<?= form_dropdown('ball_points', isset($ball_points_options) ? $ball_points_options : [], isset($selected_ball_points) ? $selected_ball_points : '', 'class="form-control" id="ball_points"' . $dropdown_disabled) ?>
														</div>
													</td>
													<td data-label="Position">
														<div class="d-flex align-items-center" style="gap:0.4em;">
															<?= form_radio(array_merge([
																'name' => 'followers_type',
																'id' => 'position_radio',
																'value' => 'position',
																'checked' => (isset($selected_followers_type) && $selected_followers_type == 'position')
															], $radio_disabled)); ?>
															<?= form_dropdown('position_points', isset($position_points_options) ? $position_points_options : [], isset($selected_position_points) ? $selected_position_points : '', 'class="form-control" id="position_points"' . $dropdown_disabled) ?>
														</div>
													</td>
													<td data-label="Friends">
														<?= form_dropdown('friends_select', 
															isset($friends_dropdown_options) ? $friends_dropdown_options : [
																'all' => 'ALL',
																'none' => '0 Friends',
																'1' => '1-Way',
																'2' => '2-Way'
															], 
															isset($selected_friends) ? $selected_friends : '', 
															'class="form-control" id="friends"') ?>
													</td>
													<td data-label="Trends">
														<?= form_dropdown('trends', isset($lottery->trends) ? $lottery->trends : [], isset($selected_trends) ? $selected_trends : '', 'class="form-control"') ?>
													</td>
													<td data-label="Sums">
														<?= form_dropdown('winning_sums', isset($lottery->winning_sums) ? $lottery->winning_sums : [], isset($selected_winning_sums) ? $selected_winning_sums : '', 'class="form-control"') ?>
													</td>
													<td data-label="Digit Sums">
														<?= form_dropdown('winning_digits', isset($lottery->winning_digits) ? $lottery->winning_digits : [], isset($selected_winning_digits) ? $selected_winning_digits : '', 'class="form-control"') ?>
													</td>
													<td data-label="Repeaters">
														<?= form_dropdown('repeaters', isset($lottery->repeaters) ? $lottery->repeaters : [],  isset($selected_repeaters) ? $selected_repeaters : '', 'class="form-control"') ?>
													</td>
													<td data-label="Consecutives">
														<?= form_dropdown('consecutives', isset($lottery->consecutives) ? $lottery->consecutives : [], isset($selected_consecutives) ? $selected_consecutives : '', 'class="form-control"') ?>
													</td>
													<td data-label="Odd/Even">
														<?= form_dropdown('parity', isset($lottery->parity) ? $lottery->parity : [], isset($selected_parity) ? $selected_parity : '', 'class="form-control"') ?>
													</td>
													<td data-label="Decades">
														<?= form_dropdown('decades', isset($lottery->decades) ? $lottery->decades : [], isset($selected_decades) ? $selected_decades : '', 'class="form-control"') ?>
													</td>
													<td data-label="Last">
														<?= form_dropdown('last_digits', isset($lottery->last_digits) ? $lottery->last_digits : [], isset($selected_last_digits) ? $selected_last_digits : '', 'class="form-control"') ?>
													</td>
													<td data-label="Range">
														<?= form_dropdown('number_range', isset($lottery->number_range) ? $lottery->number_range : [], isset($selected_number_range) ? $selected_number_range : '', 'class="form-control"') ?>
													</td>
													<td data-label="Adjacent">
														<?= form_dropdown('adjacents', isset($lottery->adjacents) ? $lottery->adjacents : [], isset($selected_adjacents) ? $selected_adjacents : '', 'class="form-control"') ?>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						<!-- Submit Button -->
						<div class="form-group text-center mt-3">
							<div class="row justify-content-center">
								<div class="col-lg-3 col-md-6 col-12 mb-2">
									<?php
									$extra = ['class' => 'btn btn-primary btn-lg w-100', 'id' => 'submit-btn', 'style' => 'touch-action: manipulation; min-height: 48px;'];
									if ($disable_generate_button) {
										$extra['disabled'] = 'disabled';
									}
									echo form_submit('submit', 'Generate Tickets', $extra);
									?>
								</div>
								<div class="col-lg-3 col-md-6 col-12 mb-2">
									<?php
									$save_button_attributes = [
										'type' => 'button',
										'class' => 'btn btn-success btn-lg w-100',
										'id' => 'save-filtered-btn',
										'style' => 'touch-action: manipulation; min-height: 48px;'
									];
									// Only disable if no tickets have been generated
									if (empty($combos_paginated)) {
										$save_button_attributes['disabled'] = 'disabled';
									}
									echo form_button($save_button_attributes, 'Save Filtered Tickets');
									?>
								</div>
								<div class="col-lg-3 col-md-6 col-12 mb-2">
									<?php
									echo form_button([
										'type' => 'button',
										'class' => 'btn btn-warning btn-lg w-100',
										'id' => 'reset-settings-btn',
										'style' => 'touch-action: manipulation; min-height: 48px;'
									], 'Reset Settings');
									?>
								</div>
								<div class="col-lg-3 col-md-6 col-12 mb-2">
									<?php
									echo form_button([
										'type' => 'button',
										'class' => 'btn btn-danger btn-lg w-100',
										'id' => 'delete-filtered-btn',
										'disabled' => 'disabled',
										'style' => 'touch-action: manipulation; min-height: 48px;'
									], 'Delete Filtered Tickets');
									?>
								</div>
							</div>
							<?= form_close(); ?>
						</div> <!-- Close prediction-form-container -->
						<?php if (!empty($number_array)): ?>
							<div class="alert alert-info text-center mb-2" style="font-weight:bold;">
								GENERATED NUMBERS ARE: <?= implode(', ', $number_array); ?>
							</div>
						<?php endif; ?>
						<?php if (!empty($combos_paginated)): 
							?>
							<div class="combination-results-container <?= (!empty($lottery_outdated) && $lottery_outdated) ? 'lottery-outdated-disabled' : '' ?>">
							<form method="get" class="mb-3" id="pagination-size-form" action="<?= base_url('admin/predictions/combination/' . $lottery->id) ?>">
								<label for="per_page" class="me-2">Combinations per page:</label>
								<select name="per_page" id="per_page" class="form-select d-inline-block w-auto" onchange="document.getElementById('pagination-size-form').submit();">
									<?php
									$sizes = [10, 20, 50, 100, 200, 300, 500, 1000];
									foreach ($sizes as $size): ?>
										<option value="<?= $size ?>" <?= (isset($pagination['per_page']) && $pagination['per_page'] == $size) ? 'selected' : '' ?>>
											<?= $size ?>
										</option>
									<?php endforeach; ?>
								</select>
								<noscript><button type="submit" class="btn btn-primary btn-sm">Go</button></noscript>
								<!-- Keep other GET params (like page) -->
								<?php if (isset($pagination['current'])): ?>
									<input type="hidden" name="page" value="<?= $pagination['current'] ?>">
								<?php endif; ?>
							</form>
							<div class="mb-4" id="generated-combinations-section">
								<h4>Generated Combination Tickets</h4>
								<div class="table-responsive mb-4">
									<table 
										id="generated-tickets-table"
										class="table table-bordered table-striped generated-tickets-table" 
										style="width:100%; margin:0 auto;"
										data-toggle="table"
										data-sort-name="ticket"
										data-sort-order="asc">
										<thead class="table-dark">
											<tr>
												<th data-field="ticket" data-sortable="true">#</th>
												<th data-field="combination" data-sortable="false">Combination</th>
												<th data-field="sum" data-sortable="false">Sum</th>
												<th data-field="digit_sum" data-sortable="false">Digit Sum</th>
												<th data-field="repeaters" data-sortable="false">Repeaters</th>
												<th data-field="consecutive" data-sortable="false">Consecutives</th>
												<th data-field="odd" data-sortable="false">Odd</th>
												<th data-field="even" data-sortable="false">Even</th>
												<th data-field="decade" data-sortable="false">Decade</th>
												<th data-field="last" data-sortable="false">Last</th>
												<th data-field="range" data-sortable="false">Range</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($combos_paginated as $idx => $item): ?>
												<tr>
													<td class="nowrap"><?= (($pagination['current']-1)*$pagination['per_page'])+$idx+1 ?></td>
													<td class="nowrap">
														<?php
														if (isset($is_independent_extra_ball) && $is_independent_extra_ball && isset($item['combo']['extra'])) {
															// For independent extra ball lotteries, show main numbers + separated extra ball
															$main_numbers = [];
															foreach ($item['combo'] as $key => $value) {
																if ($key !== 'extra') {
																	$main_numbers[] = $value;
																}
															}
															sort($main_numbers, SORT_NUMERIC);
															$main_numbers_str = implode(' ', $main_numbers);
															$extra_number = $item['combo']['extra'];
															echo '<span class="main-numbers">' . $main_numbers_str . '</span> <span class="extra-separator">Extra</span> <span class="extra-ball">' . $extra_number . '</span>';
														} else {
															// For regular lotteries, show numbers normally
															$ticket_numbers = array_values($item['combo']);
															sort($ticket_numbers, SORT_NUMERIC);
															echo implode(' ', $ticket_numbers);
														}
														?>
													</td>
													<td><?= $item['sum'] ?></td>
													<td><?= $item['digit_sum'] ?></td>
													<td><?= $item['repeater'] ?></td>
													<td><?= $item['consecutive'] ?></td>
													<td><?= $item['odd'] ?></td>
													<td><?= $item['even'] ?></td>
													<td><?= $item['decade'] ?></td>
													<td><?= $item['last'] ?></td>
													<td><?= $item['range'] ?></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								
								<?php 
								// Get pagination data
								$actual_results_count = count($combos_paginated); // Results on current page
								$total_filtered_results = $pagination['total_filtered'] ?? 0; // Total results across all pages
								$per_page = $pagination['per_page'] ?? 10;
								$total_pages = $pagination['total'] ?? 1;
								
								// Show pagination only if total filtered results exceed per_page limit
								// This ensures pagination appears when you have multiple pages, even if last page has fewer results
								$should_show_pagination = ($total_filtered_results > $per_page) && ($total_pages > 1);
								
								if ($should_show_pagination): 
								?>
								<!-- Pagination Info Display -->
								<div class="row mt-3 mb-2">
									<div class="col-sm-6">
										<div class="pagination-info">
											<?php 
											$start = $total_filtered_results > 0 ? (($pagination['current']-1) * $pagination['per_page']) + 1 : 0;
											$end = $total_filtered_results > 0 ? min($pagination['current'] * $pagination['per_page'], $total_filtered_results) : 0;
											?>
											Showing <?= $start ?> to <?= $end ?> of <?= $total_filtered_results ?> entries
										</div>
									</div>
									<div class="col-sm-6 text-right">
										<div class="pagination-info">
											Page <?= $pagination['current'] ?> of <?= $pagination['total'] ?>
										</div>
									</div>
								</div>
								
								<!-- Bootstrap Pagination here -->
								<nav>
									<ul class="pagination justify-content-center">
										<?php
										$current = $pagination['current'];
										$total = $pagination['total'];
										$per_page = $pagination['per_page'];
										
										// Base URL for pagination - preserve all current GET parameters
										$base_url = base_url('admin/predictions/combination/' . $lottery->id);
										
										// Build query string with current GET parameters (excluding page)
										$current_params = $_GET;
										unset($current_params['page']); // We'll add page separately
										$current_params['per_page'] = $per_page; // Ensure per_page is included
										$query_string = http_build_query($current_params);
										$query_prefix = $query_string ? '&' : '';

										// Previous arrow
										$prev_disabled = ($current <= 1) ? 'disabled' : '';
										$prev_page = max(1, $current - 1);
										?>
										<li class="page-item <?= $prev_disabled ?>">
											<a class="page-link" href="<?= $base_url ?>?page=<?= $prev_page ?><?= $query_prefix . $query_string ?>" aria-label="Previous">
												<span aria-hidden="true">&laquo;</span>
											</a>
										</li>
										<?php
										// Dot notation logic
										if ($total <= 10) {
											// Show all pages
											for ($i = 1; $i <= $total; $i++) {
												$active = ($i == $current) ? 'active' : '';
												echo '<li class="page-item '.$active.'"><a class="page-link" href="'.$base_url.'?page='.$i.$query_prefix.$query_string.'">'.$i.'</a></li>';
											}
										} else {
											$showed_dots = false;
											for ($i = 1; $i <= $total; $i++) {
												if (
													$i == 1 || // first page
													$i == $total || // last page
													($i >= $current - 2 && $i <= $current + 2) || // near current
													($i <= 3 && $current <= 5) || // first 3 if near start
													($i >= $total - 2 && $current >= $total - 4) // last 3 if near end
												) {
													$active = ($i == $current) ? 'active' : '';
													echo '<li class="page-item '.$active.'"><a class="page-link" href="'.$base_url.'?page='.$i.$query_prefix.$query_string.'">'.$i.'</a></li>';
													$showed_dots = false;
												} else {
													if (!$showed_dots) {
														echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
														$showed_dots = true;
													}
												}
											}
										}
										// Next arrow
										$next_disabled = ($current >= $total) ? 'disabled' : '';
										$next_page = min($total, $current + 1);
										?>
										<li class="page-item <?= $next_disabled ?>">
											<a class="page-link" href="<?= $base_url ?>?page=<?= $next_page ?><?= $query_prefix . $query_string ?>" aria-label="Next">
												<span aria-hidden="true">&raquo;</span>
											</a>
										</li>
									</ul>
								</nav>
								<?php endif; // End pagination condition ?>
							</div>
						<?php endif; ?>
						</div> <!-- Close combination-results-container -->
					</div>
				</div>
			</div>
		</section>
	<script>
    // Pass PHP variables to JavaScript
    const countryCode = '<?php echo $country_code; ?>';
    const stateProvCode = '<?php echo $state_prov_code; ?>';
    
    // Global variable to track active status (updated by AJAX)
    var isActive = <?php if (isset($active) && $active == 1) { echo 'true'; } else { echo 'false'; } ?>;
    
    // Function to handle combination selection from custom dropdown
    window.selectCombination = function(value, displayHtml) {
        // Set the hidden input value
        const hiddenInput = document.getElementById('wheeling');
        if (hiddenInput) {
            hiddenInput.value = value;
        }
        
        // Update the dropdown button text (strip HTML for button display)
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = displayHtml;
        const textContent = tempDiv.textContent || tempDiv.innerText || '';
        const selectedTextElement = document.getElementById('wheelingSelectedText');
        if (selectedTextElement) {
            selectedTextElement.textContent = textContent;
        }
        
        // Close the dropdown
        const dropdownButton = document.getElementById('wheelingDropdown');
        if (dropdownButton) {
            try {
                // Try jQuery/Bootstrap dropdown method first
                if (typeof $ !== 'undefined' && $.fn.dropdown) {
                    $(dropdownButton).dropdown('hide');
                } else {
                    // Fallback: manually close dropdown by removing show class
                    dropdownButton.classList.remove('show');
                    dropdownButton.setAttribute('aria-expanded', 'false');
                    const dropdownMenu = dropdownButton.nextElementSibling;
                    if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                        dropdownMenu.classList.remove('show');
                    }
                }
            } catch (error) {
                // Continue execution even if dropdown close fails
            }
        }
        
        // Manually enable elements that should be enabled when a combination is selected
        if (value) {
            // Enable Generate Tickets button
            const generateBtn = document.getElementById('submit-btn');
            if (generateBtn) {
                generateBtn.disabled = false;
            }
            
            // Enable all preset checkboxes (including H-W-C and Followers)
            const presetCheckboxes = document.querySelectorAll('.preset-checkbox');
            const presetOptions = document.querySelectorAll('.preset-option');
            
            presetCheckboxes.forEach(checkbox => {
                checkbox.disabled = false;
            });
            
            presetOptions.forEach(option => {
                option.classList.remove('disabled');
                option.classList.add('enabled');
            });
            
            // Load and display combination status immediately
            loadCombinationStatus(value);
        }
        
        // Trigger change event for other listeners
        if (hiddenInput) {
            const changeEvent = new Event('change', { bubbles: true });
            hiddenInput.dispatchEvent(changeEvent);
        }
        
        // Update active class
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.classList.remove('active');
        });
        if (event && event.target) {
            event.target.classList.add('active');
        }
    }
    
    // Function to load and display combination status via AJAX
    window.loadCombinationStatus = function(comboValue) {
        if (!comboValue) {
            // Hide status display if no value
            const statusDisplay = document.getElementById('combination-status-display');
            if (statusDisplay) {
                statusDisplay.style.display = 'none';
            }
            return;
        }
        
        // Get lottery ID from the current URL or a hidden field
        const lotteryId = <?= $lottery->id ?>;
        
        // Make AJAX request
        fetch('<?= base_url("admin/predictions/get_combination_status") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'lottery_id=' + encodeURIComponent(lotteryId) + '&combo_value=' + encodeURIComponent(comboValue)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatusDisplay(data);
            } else {
                console.error('Error loading combination status:', data.message);
                // Hide status display on error
                const statusDisplay = document.getElementById('combination-status-display');
                if (statusDisplay) {
                    statusDisplay.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('AJAX error:', error);
            // Hide status display on error
            const statusDisplay = document.getElementById('combination-status-display');
            if (statusDisplay) {
                statusDisplay.style.display = 'none';
            }
        });
    }
    
    // Function to update the status display with received data
    window.updateStatusDisplay = function(data) {
        const statusDisplay = document.getElementById('combination-status-display');
        
        // Check if status should be shown
        if (!data.show_status) {
            // Hide status display if no saved filter exists for this lottery
            if (statusDisplay) {
                statusDisplay.style.display = 'none';
            }
            return;
        }
        
        const statusBadge = document.getElementById('status-badge');
        const ticketsCount = document.getElementById('tickets-count');
        const actionIcons = document.querySelector('.action-icons');

        if (statusDisplay && statusBadge && ticketsCount) {
            // Update badge
            statusBadge.textContent = data.status_text;
            statusBadge.className = 'badge ' + data.status_badge_class + ' mr-3';
            statusBadge.style.backgroundColor = data.status_badge_color;
            statusBadge.style.color = 'white';
            statusBadge.style.fontSize = '0.9rem';
            
            // Update tickets count
            ticketsCount.textContent = 'Filtered Tickets: ' + data.filtered_tickets_count;
            
            // Show/hide action icons based on whether icons should be shown
            if (actionIcons) {
                actionIcons.style.display = data.show_icons ? 'flex' : 'none';
            }
            
            // Store combo_id and file_name globally for icon click handlers
            window.currentComboId = data.combo_id;
            window.currentFileName = data.file_name;
            
            // Show the status display
            statusDisplay.style.display = 'block';
        }
    }
    
    // Icon click handlers that use the stored combo info
    window.restoreFromStatus = function() {
        if (window.currentComboId) {
            refreshFilter(window.currentComboId);
        }
    }
    
    window.viewWinnersFromStatus = function() {
        if (window.currentComboId) {
            viewCombinationWinners(window.currentComboId);
        }
    }
    
    window.deleteFromStatus = function() {
        if (window.currentComboId && window.currentFileName) {
            // Get the required parameters for enhanced deletion
            const lotteryId = <?= $lottery->id ?? 0 ?>; // Get lottery ID from lottery data
            const fullFileName = window.currentFileName; // Keep full filename with ADMIN suffix
            const adminId = <?= $this->session->userdata('id') ?? 0 ?>; // Get current admin user ID
            
            if (lotteryId && fullFileName && adminId) {
                deleteCombinationFileFilters(lotteryId, fullFileName, adminId);
            } else {
                alert('Unable to determine all required parameters for deletion.\nLottery ID: ' + lotteryId + '\nFile Name: ' + fullFileName + '\nAdmin ID: ' + adminId);
            }
        }
    }
    
    // Run your script after the page is loaded
    document.addEventListener('DOMContentLoaded', function () {
        // Use the codes to display full names
        const countryName = (typeof BFHCountriesList !== 'undefined' && BFHCountriesList[countryCode]) || 'Unknown Country';
        
        let stateName = 'Unknown State/Province';
        if (stateProvCode && typeof BFHStatesList !== 'undefined' && BFHStatesList[countryCode]) {
            // BFHStatesList structure is numbered objects with code/name properties
            // Need to search through the numbered objects to find matching code
            const countryStates = BFHStatesList[countryCode];
            let foundState = null;
            
            // Loop through numbered objects (1, 2, 3, etc.)
            for (let key in countryStates) {
                if (countryStates[key] && countryStates[key].code === stateProvCode) {
                    foundState = countryStates[key];
                    break;
                }
            }
            
            if (foundState) {
                stateName = foundState.name;
            }
        } else if (!stateProvCode) {
            // No specific state/province selected - show "All" for the country
            stateName = countryCode === 'CA' ? 'All Provinces' : countryCode === 'US' ? 'All States' : 'All Regions';
        }

        // Display the names in the view
        document.getElementById('country-name').textContent = countryName;
        document.getElementById('state-name').textContent = stateName;
    });
	document.addEventListener('DOMContentLoaded', function () {
        const combinationDropdown = document.getElementById('wheeling');
        const presetCheckboxes = document.querySelectorAll('.preset-checkbox');
        const presetOptions = document.querySelectorAll('.preset-option');
		const hwcCheckbox = document.getElementById('hwc-checkbox');
    	const hwcDropdown = document.getElementById('h_w_c_group');
		const followersCheckbox = document.getElementById('followers-checkbox');
		const afterBallDropdown = document.getElementById('ball_points');
		const positionDropdown = document.getElementById('position_points');
		const friendsCheckbox = document.getElementById('friends-checkbox');
	    const friendsDropdown = document.getElementById('friends');
		const generateBtn = document.getElementById('submit-btn');
    	var saveBtn = document.getElementById('save-filtered-btn');
    	var resetBtn = document.getElementById('reset-settings-btn');
    	var deleteBtn = document.getElementById('delete-filtered-btn');
		
		// Simplified logic - PHP now handles initial button states correctly
		// JavaScript only handles Generate button and combination dropdown interaction
		
		// Enable Generate Tickets when a combination table is selected
		combinationDropdown.addEventListener('change', function () {
			if (combinationDropdown.value) {
				generateBtn.disabled = false;
			} else {
				generateBtn.disabled = true;
			}
			// Don't modify Save/Reset button states - PHP handles this correctly based on $combos_paginated
		});
		
		// No need to modify button states on generate click - PHP handles this after page reload

		// After Save Filtered Tickets is clicked, enable Delete Filtered Tickets
		saveBtn.addEventListener('click', function (e) {
			e.preventDefault();
			
			// Show progress indicator
			const originalText = saveBtn.textContent;
			saveBtn.textContent = 'Saving...';
			saveBtn.disabled = true;
			
		// Get the filtered count for display
		const filteredCount = <?= isset($pagination['total_filtered']) ? $pagination['total_filtered'] : 0 ?>;
		const countText = filteredCount > 0 ? filteredCount + ' ' : '';
		
		// Create and show progress bar
		const progressContainer = document.createElement('div');
		progressContainer.className = 'progress mb-3';
		progressContainer.innerHTML = `
			<div class="progress-bar progress-bar-striped progress-bar-animated" 
				 role="progressbar" 
				 style="width: 0%" 
				 aria-valuenow="0" 
				 aria-valuemin="0" 
				 aria-valuemax="100">
				Saving ${countText}filtered tickets...
			</div>
		`;
		
		// Insert progress bar before the form
		const form = document.querySelector('form');
		form.insertBefore(progressContainer, form.firstChild);
			
			// Animate progress bar
			const progressBar = progressContainer.querySelector('.progress-bar');
			let progress = 0;
			const progressInterval = setInterval(() => {
				progress += 10;
				progressBar.style.width = progress + '%';
				progressBar.setAttribute('aria-valuenow', progress);
				
				if (progress >= 90) {
					clearInterval(progressInterval);
				}
			}, 200);
			
			// Make AJAX request to save filtered tickets
			fetch('<?= base_url(); ?>admin/predictions/combination_save/<?= $lottery->id; ?>', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-Requested-With': 'XMLHttpRequest'
				},
				body: JSON.stringify({
					lottery_id: <?= $lottery->id; ?>
				})
			})
			.then(response => response.json())
			.then(data => {
				// Complete progress bar
				clearInterval(progressInterval);
				progressBar.style.width = '100%';
				progressBar.setAttribute('aria-valuenow', '100');
				progressBar.textContent = 'Complete!';
				
				// Remove progress bar after a short delay
				setTimeout(() => {
					progressContainer.remove();
				}, 1000);
				
				// Reset button state but keep it disabled after successful save
				saveBtn.textContent = originalText;
				
				if (data.success) {
					// Show success message
					const messageDiv = document.createElement('div');
					messageDiv.className = 'alert alert-success alert-dismissible fade show';
					messageDiv.innerHTML = data.message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
					
					// Insert message at the top of the form
					form.insertBefore(messageDiv, form.firstChild);
					
					// Update the Filtered Tickets count if provided in response
					if (data.filtered_tickets_count) {
						const filteredTicketsElement = document.getElementById('tickets-count');
						if (filteredTicketsElement) {
							filteredTicketsElement.textContent = 'Filtered Tickets: ' + data.filtered_tickets_count;
						}
					}
					
					// Update the active flag since we just saved an active filter
					isActive = true;
					
					// Update status badge if status changed from Expired to Active
					if (data.status_changed && data.new_status) {
						// Find the status badge and update it
						const statusBadge = document.getElementById('status-badge');
						if (statusBadge) {
							statusBadge.className = 'badge badge-success mr-3';
							statusBadge.style.backgroundColor = '#28a745'; // Green background
							statusBadge.style.color = 'white';
							statusBadge.style.fontSize = '0.9rem';
							statusBadge.textContent = data.new_status;
						}
					}
					
					// Grey out and disable Save Filtered Tickets button (requirement 6)
					saveBtn.disabled = true;
					saveBtn.style.opacity = '0.5';
					saveBtn.style.cursor = 'not-allowed';
					
					// Enable and not greyed out Delete Filtered Tickets button (requirement 7)
					deleteBtn.disabled = false;
					deleteBtn.style.opacity = '1';
					deleteBtn.style.cursor = 'pointer';
					
					// Store combo data for delete functionality
					// Extract combo_id from the selected wheeling dropdown
					const wheelingDropdown = document.getElementById('wheeling');
					if (wheelingDropdown && wheelingDropdown.value) {
						const selectedValue = wheelingDropdown.value;
						if (selectedValue.includes('|')) {
							const parts = selectedValue.split('|');
							// Store in global variables for delete function
							window.savedComboId = parseInt(parts[0]);
							window.savedFileName = parts[1];
						}
					}
				} else {
					// Show error message and re-enable save button
					const messageDiv = document.createElement('div');
					messageDiv.className = 'alert alert-danger alert-dismissible fade show';
					messageDiv.innerHTML = data.message + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
					
					// Insert message at the top of the form
					form.insertBefore(messageDiv, form.firstChild);
					
					// Re-enable save button on error
					saveBtn.disabled = false;
					saveBtn.style.opacity = '1';
					saveBtn.style.cursor = 'pointer';
				}
			})
			.catch(error => {
				// Clear progress interval and remove progress bar
				clearInterval(progressInterval);
				progressContainer.remove();
				
				// Reset button state
				saveBtn.textContent = originalText;
				saveBtn.disabled = false;
				saveBtn.style.opacity = '1';
				saveBtn.style.cursor = 'pointer';
				
				// Show error message
				const messageDiv = document.createElement('div');
				messageDiv.className = 'alert alert-danger alert-dismissible fade show';
				messageDiv.innerHTML = 'An error occurred while saving filtered tickets. Please try again. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
				
				// Insert message at the top of the form
				const form = document.querySelector('form');
				form.insertBefore(messageDiv, form.firstChild);
				
				console.error('Error:', error);
			});
		});

		// Delete Filtered Tickets button functionality
		deleteBtn.addEventListener('click', function (e) {
			e.preventDefault();
			
			// Try to get combo data from multiple sources
			let comboId = null;
			let fileName = null;
			
			// First, check if we have stored data from the save operation
			if (window.savedComboId && window.savedFileName) {
				comboId = window.savedComboId;
				fileName = window.savedFileName;
			}
			// Second, try to get from PHP variables if they exist
			else {
				<?php if (isset($combo_id) && !is_null($combo_id) && isset($file_name) && !empty($file_name)): ?>
					comboId = <?= $combo_id ?>;
					fileName = '<?= $file_name ?>';
				<?php endif; ?>
			}
			
			// If no stored data, try to extract from the combination dropdown selection
			if (!comboId || !fileName) {
				const wheelingDropdown = document.getElementById('wheeling');
				if (wheelingDropdown && wheelingDropdown.value) {
					const selectedValue = wheelingDropdown.value;
					if (selectedValue.includes('|')) {
						const parts = selectedValue.split('|');
						comboId = parseInt(parts[0]);
						fileName = parts[1];
					}
				}
			}
			
			// If we still don't have the data, check if there's a restore icon (which means there's saved data)
			if (!comboId || !fileName) {
				// Look for the eye icon in the control panel which indicates saved filter data
				const eyeIcon = document.querySelector('i[onclick*="refreshFilter"]');
				if (eyeIcon) {
					// Extract combo_id from the onclick attribute
					const onclickAttr = eyeIcon.getAttribute('onclick');
					const match = onclickAttr.match(/refreshFilter\((\d+)\)/);
					if (match) {
						comboId = parseInt(match[1]);
						// For fileName, we can use the selected combination table name
						const wheelingDropdown = document.getElementById('wheeling');
						if (wheelingDropdown && wheelingDropdown.value) {
							const selectedValue = wheelingDropdown.value;
							if (selectedValue.includes('|')) {
								fileName = selectedValue.split('|')[1];
							}
						}
					}
				}
			}
			
			// Check if we have the required data
			if (comboId && fileName) {
				// Use the same confirmation message as the trash can icon
				// Remove L###ADMIN## suffix for display (handles both new and legacy formats)
				if (confirm('You are about to delete the Combination Ticket file: ' + fileName.replace(/(L\d{3})?ADMIN.*/, '') + '. Do You want to Continue? (Y/N)')) {
					window.location.href = '<?= base_url() ?>admin/predictions/delete_combo/' + comboId;
				}
			} else {
				alert('No combination filter data available to delete. Please save filtered tickets first.');
			}
		});

		// Reset Settings button functionality
		resetBtn.addEventListener('click', function (e) {
			if (confirm('Are you sure you want to reset all settings? This will clear all form data and redirect to the main predictions page.')) {
				// Clear session data by redirecting to a controller method that clears the session
				window.location.href = '<?= base_url(); ?>admin/predictions/reset_settings/<?= $lottery->id; ?>';
			}
		});
		function updateHwcDropdown() {
			if (hwcCheckbox && hwcDropdown) {
				if (hwcCheckbox.checked) {
					hwcDropdown.disabled = false;
					hwcDropdown.classList.remove('greyed-out');
				} else {
					hwcDropdown.disabled = true;
					hwcDropdown.classList.add('greyed-out');
				}
			}
		}
		function updateFollowersDropdowns() {
			if (followersCheckbox && afterBallDropdown && positionDropdown) {
				if (followersCheckbox.checked) {
					afterBallDropdown.disabled = false;
					afterBallDropdown.classList.remove('greyed-out');
					positionDropdown.disabled = false;
					positionDropdown.classList.remove('greyed-out');
				} else {
					afterBallDropdown.disabled = true;
					afterBallDropdown.classList.add('greyed-out');
					positionDropdown.disabled = true;
					positionDropdown.classList.add('greyed-out');
				}
			}
    	}
		function updateFriendsDropdown() {
			if (friendsCheckbox && friendsDropdown) {
				if (friendsCheckbox.checked) {
					friendsDropdown.disabled = false;
					friendsDropdown.classList.remove('greyed-out');
				} else {
					friendsDropdown.disabled = true;
					friendsDropdown.classList.add('greyed-out');
				}
			}
		}
		// Initial state
		updateHwcDropdown();
		updateFollowersDropdowns();
		updateFriendsDropdown();
		// Listen for changes
		if (hwcCheckbox) {
			hwcCheckbox.addEventListener('change', updateHwcDropdown);
		}
		if (followersCheckbox) {
			followersCheckbox.addEventListener('change', updateFollowersDropdowns);
		}
		 if (friendsCheckbox) {
        	friendsCheckbox.addEventListener('change', updateFriendsDropdown);
    	}
		// Listen for changes in the combination table dropdown
        combinationDropdown.addEventListener('change', function () {
            if (combinationDropdown.value) {
                // Enable the checkboxes and checkmarks
                presetCheckboxes.forEach(checkbox => {
                    checkbox.disabled = false;
                });
                presetOptions.forEach(option => {
                    option.classList.remove('disabled');
                    option.classList.add('enabled');
                });
                // Ensure checkboxes are interactive after enabling
                enableCheckboxInteraction();
            } else {
                // Disable the checkboxes and grey out the checkmarks
                presetCheckboxes.forEach(checkbox => {
                    checkbox.disabled = true;
                    // DON'T uncheck the checkbox when disabled - preserve user selection
                    // checkbox.checked = false; // Uncheck the checkbox when disabled
                });
                presetOptions.forEach(option => {
                    option.classList.remove('enabled');
                    option.classList.add('disabled');
                });
            }
        });
		
		// Initialize checkboxes state on page load - especially important for error messages
		function initializeCheckboxState() {
			if (combinationDropdown && combinationDropdown.value) {
				// Enable the checkboxes and checkmarks since a combination is selected
				presetCheckboxes.forEach(checkbox => {
					checkbox.disabled = false;
				});
				presetOptions.forEach(option => {
					option.classList.remove('disabled');
					option.classList.add('enabled');
				});
			}
		}
		
		// Ensure checkboxes respond to clicks after being enabled
		function enableCheckboxInteraction() {
			presetCheckboxes.forEach(checkbox => {
				if (!checkbox.disabled) {
					// Force re-enable interactivity
					checkbox.style.pointerEvents = 'auto';
					checkbox.removeAttribute('readonly');
				}
			});
		}
		
		// Special handling for error cases - ensure checkboxes are functional
		<?php if (!empty($message) && strpos($message, 'Either Hot - Warm - Cold checkbox or Follower checkbox') !== false): ?>
		// We have the specific error, ensure checkboxes are definitely enabled and functional
		setTimeout(function() {
			if (combinationDropdown && combinationDropdown.value) {
				presetCheckboxes.forEach(checkbox => {
					checkbox.disabled = false;
					checkbox.style.pointerEvents = 'auto';
					checkbox.removeAttribute('readonly');
				});
				presetOptions.forEach(option => {
					option.classList.remove('disabled');
					option.classList.add('enabled');
				});
			}
		}, 100);
		<?php endif; ?>
		
		// Call initialization when page loads
		initializeCheckboxState();
		enableCheckboxInteraction();
		
		// Initialize Bootstrap Table for Generated Combination Tickets
		<?php if (!empty($combos_paginated)): ?>
		$(document).ready(function() {
		$('#generated-tickets-table').bootstrapTable({
			filterControl: false,
			pagination: false,
			search: false,
			showRefresh: false,
			showToggle: false,
			showColumns: false,
			sortName: 'ticket',
			sortOrder: 'asc',
			classes: 'table table-bordered table-striped'
		});
		}); // Close $(document).ready
		<?php endif; ?>
    }); // Close DOMContentLoaded function
    
    // Function to refresh filter settings
    function refreshFilter(comboId) {
        if (confirm('Loading the Previous Saved Settings. Do you want to continue? (Y/N)')) {
            window.location.href = '<?= base_url() ?>admin/predictions/restore_settings/<?= $lottery->id ?>?combo_id=' + comboId;
        }
    }
    
    // Function to view combination ticket winners
    function viewCombinationWinners(comboId) {
        // If comboId is passed (from status area), use dynamic lookup
        if (comboId && comboId !== 'undefined') {
            // Make AJAX call to get filter record ID for this combo
            fetch('<?= base_url("admin/predictions/get_filter_record_id") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'combo_id=' + encodeURIComponent(comboId) + '&lottery_id=' + <?= $lottery->id ?>
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.filter_record_id) {
                    // Check if filter is active or expired
                    const isActive = data.is_active;
                    
                    if (!isActive) {
                        // Show warning for expired filters but still allow navigation
                        if (confirm('This filter is EXPIRED. You can still view the combination ticket winners, but results will be based on historical data. Continue?')) {
                            window.location.href = '<?= base_url() ?>admin/prize/view_combination_tickets/' + data.filter_record_id + '?referrer=futures&lottery_id=<?= $lottery->id ?>&combo_id=' + comboId;
                        }
                    } else {
                        // Active filter - navigate directly without warning
                        window.location.href = '<?= base_url() ?>admin/prize/view_combination_tickets/' + data.filter_record_id + '?referrer=futures&lottery_id=<?= $lottery->id ?>&combo_id=' + comboId;
                    }
                } else {
                    alert('No saved filter found. Please save filtered tickets first before viewing combination winners.');
                }
            })
            .catch(error => {
                console.error('Error getting filter record ID:', error);
                alert('Error loading combination data. Please try again.');
            });
            return;
        }
        
        // Fallback to original logic for page-loaded combinations
        <?php if (!empty($filter_record_id)): ?>
            var filterRecordId = <?= $filter_record_id ?: 'null' ?>;
            
            // Check if filter record exists
            if (!filterRecordId || filterRecordId === null) {
                alert('No saved filter found. Please save filtered tickets first using the "Save Filtered Tickets" button, then try again.');
                return;
            }
            
            // Check the global isActive variable (which gets updated by AJAX)
            if (!isActive) {
                // Show warning for expired filters but still allow navigation
                if (confirm('This filter is EXPIRED. You can still view the combination ticket winners, but results will be based on historical data. Continue?')) {
                    // Add referrer parameter to indicate we came from prediction futures
                    window.location.href = '<?= base_url() ?>admin/prize/view_combination_tickets/' + filterRecordId + '?referrer=futures&lottery_id=<?= $lottery->id ?><?= (isset($combo_id) && !is_null($combo_id)) ? '&combo_id=' . $combo_id : '' ?>';
                }
                // If user cancels, do nothing (return from function)
            } else {
                // Active filter - navigate directly without warning
                window.location.href = '<?= base_url() ?>admin/prize/view_combination_tickets/' + filterRecordId + '?referrer=futures&lottery_id=<?= $lottery->id ?><?= (isset($combo_id) && !is_null($combo_id)) ? '&combo_id=' . $combo_id : '' ?>';
            }
        <?php else: ?>
            alert('No saved filter found. Please save filtered tickets first before viewing combination winners.');
        <?php endif; ?>
    }
    
    function deleteFilter(comboId, fileName) {
        // Remove L###ADMIN## suffix for display (handles both new and legacy formats)
        if (confirm('You are about to delete the Combination Ticket file: ' + fileName.replace(/(L\d{3})?ADMIN.*/, '') + '. This will REMOVE ALL winning data. Do You want to Continue? (Y/N)')) {
            // Future implementation for delete functionality
             window.location.href = '<?= base_url() ?>admin/predictions/delete_combo/' + comboId;
        }
    }
    
    // Enhanced deletion function for combination file filters
    function deleteCombinationFileFilters(lotteryId, fullFileName, adminId) {
        // Remove L###ADMIN## suffix and .txt extension (handles both new and legacy formats)
        const baseFileName = fullFileName.replace(/(L\d{3})?ADMIN.*/, '').replace(/\.txt$/, '');
        
        if (confirm('You are about to delete ALL combination filter records for file: "' + baseFileName + '".\n\n' +
                   'This will remove:\n' +
                   '• All saved filter settings for this file\n' +
                   '• All associated combination files\n' +
                   '• All winning data for this combination\n\n' +
                   'This action cannot be undone. Do you want to continue?')) {
            
            // Show loading indicator
            const statusDisplay = document.getElementById('combination-status-display');
            if (statusDisplay) {
                statusDisplay.innerHTML = '<div class="col-12 text-center"><i class="fa fa-spinner fa-spin"></i> Deleting previously saved settings...</div>';
            }
            
            // Call the new simplified deletion method
            window.location.href = '<?= base_url() ?>admin/predictions/delete_combination_file_filters/' + 
                                  lotteryId + '/' + encodeURIComponent(fullFileName) + '/' + adminId;
        }
    }
    
    // Function to refresh the dropdown options and remove badges after deletion
    function refreshDropdownAfterDeletion() {
        const wheelingDropdown = document.getElementById('wheeling');
        if (wheelingDropdown) {
            // Find and update options that might have had badges
            const options = wheelingDropdown.options;
            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                // Remove any existing badges from option text
                if (option.text && (option.text.includes('Active') || option.text.includes('Expired'))) {
                    // Clean the option text by removing badge HTML/text
                    option.text = option.text.replace(/\s*(Active|Expired)\s*$/, '').trim();
                }
            }
            
            // Reset the dropdown selection
            wheelingDropdown.selectedIndex = 0;
            
            // Hide the status display
            const statusDisplay = document.getElementById('combination-status-display');
            if (statusDisplay) {
                statusDisplay.style.display = 'none';
            }
            
            // Clear global variables
            window.currentComboId = null;
            window.currentFileName = null;
            
            // Trigger change event to ensure any dependent elements are updated
            const event = new Event('change', { bubbles: true });
            wheelingDropdown.dispatchEvent(event);
        }
    }
    
    // Check if there's already a selected combination when page loads
    document.addEventListener('DOMContentLoaded', function() {
        const wheelingInput = document.getElementById('wheeling');
        if (wheelingInput && wheelingInput.value) {
            // Load status for the pre-selected combination
            loadCombinationStatus(wheelingInput.value);
        }
    });
</script>