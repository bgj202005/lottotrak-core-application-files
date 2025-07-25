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
	#futures-filter-table th,
	#futures-filter-table td {
		font-size: 0.75em;
		padding: 0.18em 0.25em;
		white-space: nowrap;
	}

	#futures-filter-table select.form-control,
	#futures-filter-table select {
		font-size: 0.90em;
		padding: 0.1em 0.3em;
		min-width: 65px;  /* Increased to fit most dropdown text */
		max-width: 90px;  /* Allow to expand as needed */
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
	.d-flex {
    display: flex;
    align-items: center;
    gap: 0.3em;
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
						
						<?php if (!empty($message)) ?> <h3 class="bg-warning" style = "text-align:center;"><?=$message; ?></h3>
						<?php echo validation_errors('<H2><div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">','</div></H2>'); ?>
						<?php echo form_open(base_url().'admin/predictions/combination/'.$lottery->id); ?>
						<hr>
						<!-- Country -->
						<div class="form-group row justify-content-center">
							<?php
							$extra = ['class' => 'col-4 col-form-label col-form-label-md text-right'];
							echo form_label('Country:', 'country', $extra);
							?>
							<div class="col-6" style = "margin-top: 0.5em;">
								<span id="country-name"></span>
							</div>
						</div>
						<!-- Province/State -->
						<div class="form-group row justify-content-center">
							<?php
							$extra = ['class' => 'col-4 col-form-label col-form-label-md text-right'];
							echo form_label('Province/State:', 'province', $extra);
							?>
							<div class="col-6" style = "margin-top: 0.5em;">
								<span id="state-name"></span>
							</div>
						</div>
						<div class="form-group row justify-content-center">
							<?php
							// Label for the dropdown
							$extra = ['class' => 'col-4 col-form-label col-form-label-md text-right'];
							echo form_label('Combination Table:', 'wheeling', $extra);
							?>
							<div class="col-6">
								<?php
								// Prepare the dropdown options
								$wheeling_options = ['' => 'Select Combination Table']; // Default option
								if (!empty($combination_files)) {
									foreach ($combination_files as $file) {
										// Use the id|filename format for the value, display filename with details
										$value = $file['id'] . '|' . $file['file_name']; // e.g., "246|060828"
										$display = '(' . $file['file_name'] . ')     ' . $file['N'] . ' Numbers - ' . number_format($file['CCCC']) . ' Tickets';
										$wheeling_options[$value] = $display;
									}
								}
								
								// Dropdown attributes
								$extra = ['class' => 'form-control', 'id' => 'wheeling','style' => 'width: 70%;'];
								if (!empty($disable_combination_dropdown)) {
									$extra['disabled'] = 'disabled';
								}
								
								// For the selected value, we need to check if it matches the filename part
								$selected_value = '';
								if (isset($selected_wheeling)) {
									// If selected_wheeling is just a filename, find the matching id|filename value
									foreach ($wheeling_options as $option_value => $option_display) {
										if (strpos($option_value, '|') !== false) {
											list($option_id, $option_filename) = explode('|', $option_value, 2);
											if ($option_filename === $selected_wheeling) {
												$selected_value = $option_value;
												break;
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
								
								echo form_dropdown('wheeling', $wheeling_options, $selected_value, $extra);
								// Display form error if any
								echo form_error('wheeling', '<div class="bg-warning mt-2 p-2 text-center text-white">', '</div>');
								?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<!-- First Table: Presets Control Panel -->
								<div class="table-section" style="border:2px solid #007bff; border-radius:8px; margin-bottom:2em; padding:1em;">
									<div class="table-title" style="font-weight:bold; font-size:1.2em; background:#f8f9fa; border-bottom:1px solid #007bff; padding:0.5em 1em; border-radius:6px 6px 0 0; margin:-1em -1em 1em -1em;">
										LOTTERY PROFILE STATISTICS PRESETS CONTROL PANEL
										<?php if (!is_NULL($combo_id)): ?>
											| 
											<?=$active 
														? '<span class="badge badge-success">Active</span>' 
														: '<span class="badge badge-danger">Expired</span>'; ?>
											<i class="fa fa-eye fa-2x" title="Restore previous Combination Filter Settings" style="color:#007bff; cursor:pointer; margin:0 5px;" onclick="refreshFilter(<?= $combo_id ?>)"></i>
											<i class="fa fa-money fa-2x" title="View Combination Ticket Winners" style="color:#28a745; cursor:pointer; margin:0 5px;" onclick="viewCombinationWinners(<?= $combo_id ?>)"></i>
											<i class="fa fa-trash-o fa-2x" title="Delete this file and Combination Table Filtered Tickets" style="color:#dc3545; cursor:pointer; margin:0 5px;" onclick="deleteFilter(<?= $combo_id ?>, '<?= $file_name ?>')"></i>
											<span style="color:#28a745; font-weight:bold;">Filtered Tickets: <?=$CCCC ?></span>
										<?php endif; ?>
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
															$js = 'id="hwc-checkbox" class="preset-checkbox" disabled';
															echo form_checkbox('hwc', '1', !empty($selected_hwc), $js);
															?>
														</td>
														<td data-label="Range"><?php echo $h_w_c['range']; ?></td>
														<td data-label="Extra Draws?"> <span class="preset-option disabled"><?php echo $h_w_c['extra_draws'] ? '✓' : ''; ?></span></td>
														<td data-label="Includes Extra?"><span class="preset-option disabled"><?php echo $h_w_c['extra_included'] ? '✓' : ''; ?></span></td>
														<!-- Followers -->
														<td data-label="Followers">
															<?php
															$js = 'id="followers-checkbox" class="preset-checkbox" disabled';
															echo form_checkbox('followers', '1', !empty($selected_followers), $js);
															?>
														</td>
														<td data-label="Range"><?php echo $followers['range']; ?></td>
														<td data-label="Extra Draws?"><span class="preset-option disabled"><?php echo $followers['extra_draws'] ? '✓' : ''; ?></span></td>
														<td data-label="Includes Extra?"><span class="preset-option disabled"><?php echo $followers['extra_included'] ? '✓' : ''; ?></span></td>
														<!-- Friends -->
														<td data-label="Friends">
															<?php
															$js = 'id="friends-checkbox" class="preset-checkbox" disabled';
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
							<div class="col-md-12">
								<!-- Second Table: Actual Win History Filtering -->
								<div class="table-section" style="border:2px solid #28a745; border-radius:8px; margin-bottom:2em; padding:1em;">
									<div class="table-title" style="font-weight:bold; font-size:1.2em; background:#f8f9fa; border-bottom:1px solid #28a745; padding:0.5em 1em; border-radius:6px 6px 0 0; margin:-1em -1em 1em -1em;">
										Actual Win History Filtering for <?= htmlspecialchars($lottery->next_draw_date); ?>
									</div>		
									<!-- Move .table-responsive OUTSIDE the table for proper scrolling -->
									<div class="table-responsive">
										<table class="table table-bordered" id="futures-filter-table">
											<thead>
												<tr>
													<th>H-W-C</th>
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
													<td data-label="H-W-C">
														<?= form_dropdown('h_w_c_group', $h_w_c_group, isset($selected_h_w_c_group) ? $selected_h_w_c_group : '', 'class="form-control" id="h_w_c_group"') ?>
													</td>
													<td data-label="After Ball">
														<div class="d-flex align-items-center" style="gap:0.3em;">
															<?= form_radio([
																	'name' => 'followers_type',
																	'id' => 'after_ball_radio',
																	'value' => 'after_ball',
																	'checked' => (isset($selected_followers_type) && $selected_followers_type == 'after_ball')
																]); ?>
															<?= form_dropdown('ball_points', $ball_points_options, isset($selected_ball_points) ? $selected_ball_points : '', 'class="form-control" id="ball_points"') ?>
														</div>
													</td>
													<td data-label="Position">
														<div class="d-flex align-items-center" style="gap:0.3em;">
															<?= form_radio([
																'name' => 'followers_type',
																'id' => 'position_radio',
																'value' => 'position',
																'checked' => (isset($selected_followers_type) && $selected_followers_type == 'position')
															]); ?>
															<?= form_dropdown('position_points', $position_points_options, isset($selected_position_points) ? $selected_position_points : '', 'class="form-control" id="position_points"') ?>
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
														<?= form_dropdown('trends', $lottery->trends, isset($selected_trends) ? $selected_trends : '', 'class="form-control"') ?>
													</td>
													<td data-label="Sums">
														<?= form_dropdown('winning_sums', $lottery->winning_sums, isset($selected_winning_sums) ? $selected_winning_sums : '', 'class="form-control"') ?>
													</td>
													<td data-label="Digit Sums">
														<?= form_dropdown('winning_digits', $lottery->winning_digits, isset($selected_winning_digits) ? $selected_winning_digits : '', 'class="form-control"') ?>
													</td>
													<td data-label="Repeaters">
														<?= form_dropdown('repeaters', $lottery->repeaters,  isset($selected_repeaters) ? $selected_repeaters : '', 'class="form-control"') ?>
													</td>
													<td data-label="Consecutives">
														<?= form_dropdown('consecutives', $lottery->consecutives, isset($selected_consecutives) ? $selected_consecutives : '', 'class="form-control"') ?>
													</td>
													<td data-label="Odd/Even">
														<?= form_dropdown('parity', $lottery->parity, isset($selected_parity) ? $selected_parity : '', 'class="form-control"') ?>
													</td>
													<td data-label="Decades">
														<?= form_dropdown('decades', $lottery->decades, isset($selected_decades) ? $selected_decades : '', 'class="form-control"') ?>
													</td>
													<td data-label="Last">
														<?= form_dropdown('last_digits', $lottery->last_digits, isset($selected_last_digits) ? $selected_last_digits : '', 'class="form-control"') ?>
													</td>
													<td data-label="Range">
														<?= form_dropdown('number_range', $lottery->number_range, isset($selected_number_range) ? $selected_number_range : '', 'class="form-control"') ?>
													</td>
													<td data-label="Adjacent">
														<?= form_dropdown('adjacents', $lottery->adjacents, isset($selected_adjacents) ? $selected_adjacents : '', 'class="form-control"') ?>
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
							<?php
							$extra = ['class' => 'btn btn-primary btn-lg', 'id' => 'submit-btn'];
							if ($disable_generate_button) {
								$extra['disabled'] = 'disabled';
							}
							echo form_submit('submit', 'Generate Tickets', $extra);
						// Both buttons are disabled by default
							echo form_button([
								'type' => 'button',
								'class' => 'btn btn-success btn-lg mx-2',
								'id' => 'save-filtered-btn',
								'disabled' => 'disabled'
							], 'Save Filtered Tickets');
							echo form_button([
								'type' => 'button',
								'class' => 'btn btn-warning btn-lg mx-2',
								'id' => 'reset-settings-btn',
								'disabled' => 'disabled'
							], 'Reset Settings');
							echo form_button([
								'type' => 'button',
								'class' => 'btn btn-danger btn-lg mx-2',
								'id' => 'delete-filtered-btn',
								'disabled' => 'disabled'
							], 'Delete Filtered Tickets');
							?>	
							<?= form_close(); ?>
						</div>
						<?php if (!empty($number_array)): ?>
							<div class="alert alert-info text-center mb-2" style="font-weight:bold;">
								GENERATED NUMBERS ARE: <?= implode(', ', $number_array); ?>
							</div>
						<?php endif; ?>
						<?php if (!empty($combos_paginated)): 
							?>
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
										data-filter-control="true"
										data-show-filter-control-switch="true"
										data-filter-show-clear="true"
										data-sort-name="ticket"
										data-sort-order="asc"
										data-pagination="false"
										data-search="true"
										data-show-refresh="true"
										data-show-toggle="true"
										data-show-columns="true">
										<thead class="table-dark">
											<tr>
												<th data-field="ticket" data-sortable="true">#</th>
												<th data-field="combination" data-sortable="false">Combination</th>
												<th data-field="sum" data-sortable="true" data-filter-control="select" data-align="center">Sum</th>
												<th data-field="digit_sum" data-sortable="true" data-filter-control="select" data-align="center">Digit Sum</th>
												<th data-field="repeaters" data-sortable="true" data-filter-control="select" data-align="center">Repeaters</th>
												<th data-field="consecutive" data-sortable="true" data-filter-control="select" data-align="center">Consecutive</th>
												<th data-field="odd" data-sortable="true" data-filter-control="select" data-align="center">Odd</th>
												<th data-field="even" data-sortable="true" data-filter-control="select" data-align="center">Even</th>
												<th data-field="decade" data-sortable="true" data-filter-control="select" data-align="center">Decade</th>
												<th data-field="last" data-sortable="true" data-filter-control="select" data-align="center">Last</th>
												<th data-field="range" data-sortable="true" data-filter-control="select" data-align="center">Range</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($combos_paginated as $idx => $item): ?>
												<tr>
													<td class="nowrap"><?= (($pagination['current']-1)*$pagination['per_page'])+$idx+1 ?></td>
													<td class="nowrap">
														<?php
															$ticket_numbers = array_values($item['combo']);
															sort($ticket_numbers, SORT_NUMERIC);
															echo implode(' ', $ticket_numbers);
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
								
								<!-- Pagination Info Display -->
								<div class="row mt-3 mb-2">
									<div class="col-sm-6">
										<div class="pagination-info">
											<?php 
											$total_filtered = $pagination['total_filtered'] ?? 0;
											$start = $total_filtered > 0 ? (($pagination['current']-1) * $pagination['per_page']) + 1 : 0;
											$end = $total_filtered > 0 ? min($pagination['current'] * $pagination['per_page'], $total_filtered) : 0;
											?>
											Showing <?= $start ?> to <?= $end ?> of <?= $total_filtered ?> entries
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
										
										// Base URL for pagination - use combination method for proper session handling
										$base_url = base_url('admin/predictions/combination/' . $lottery->id);

										// Previous arrow
										$prev_disabled = ($current <= 1) ? 'disabled' : '';
										$prev_page = max(1, $current - 1);
										?>
										<li class="page-item <?= $prev_disabled ?>">
											<a class="page-link" href="<?= $base_url ?>?page=<?= $prev_page ?>&per_page=<?= $per_page ?>" aria-label="Previous">
												<span aria-hidden="true">&laquo;</span>
											</a>
										</li>
										<?php
										// Dot notation logic
										if ($total <= 10) {
											// Show all pages
											for ($i = 1; $i <= $total; $i++) {
												$active = ($i == $current) ? 'active' : '';
												echo '<li class="page-item '.$active.'"><a class="page-link" href="'.$base_url.'?page='.$i.'&per_page='.$per_page.'">'.$i.'</a></li>';
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
													echo '<li class="page-item '.$active.'"><a class="page-link" href="'.$base_url.'?page='.$i.'&per_page='.$per_page.'">'.$i.'</a></li>';
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
											<a class="page-link" href="<?= $base_url ?>?page=<?= $next_page ?>&per_page=<?= $per_page ?>" aria-label="Next">
												<span aria-hidden="true">&raquo;</span>
											</a>
										</li>
									</ul>
								</nav>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
	<script>
    // Pass PHP variables to JavaScript
    const countryCode = '<?php echo $country_code; ?>';
    const stateProvCode = '<?php echo $state_prov_code; ?>';
    // Run your script after the page is loaded
    document.addEventListener('DOMContentLoaded', function () {
        console.log('Country Code:', countryCode);
        console.log('State/Province Code:', stateProvCode);

        // Example: Use the codes to display full names
        const countryName = BFHCountriesList[countryCode] || 'Unknown Country';
        const stateName = stateProvCode
            ? (BFHStatesList[countryCode] && BFHStatesList[countryCode][stateProvCode]) || 'Unknown State/Province'
            : (countryCode === 'CA' ? 'All Provinces' : countryCode === 'US' ? 'All States' : 'All Regions');

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
		
		// Initial state
		saveBtn.disabled = true;
		resetBtn.disabled = true;
		deleteBtn.disabled = true;
		
		// Check if tickets have been generated (number_array exists)
		<?php if (!empty($number_array)): ?>
		// Tickets have been generated, enable Save and Reset buttons
		saveBtn.disabled = false;
		resetBtn.disabled = false;
		<?php endif; ?>
		//generateBtn.disabled = true;
		
		// Enable Generate Tickets when a combination table is selected
		combinationDropdown.addEventListener('change', function () {
			if (combinationDropdown.value) {
				generateBtn.disabled = false;
				// Only disable Save/Reset buttons if tickets haven't been generated yet
				<?php if (empty($number_array)): ?>
				saveBtn.disabled = true;
				resetBtn.disabled = true;
				<?php endif; ?>
				deleteBtn.disabled = true;
			} else {
				generateBtn.disabled = true;
				saveBtn.disabled = true;
				resetBtn.disabled = true;
				deleteBtn.disabled = true;
			}
		});
		generateBtn.addEventListener('click', function (e) {
        	// You may want to check if tickets are actually generated before enabling
			setTimeout(function() {
				saveBtn.disabled = false;
				resetBtn.disabled = false;
			}, 500); // Adjust delay as needed for your ticket generation process
		});

		// After Save Filtered Tickets is clicked, enable Delete Filtered Tickets
		saveBtn.addEventListener('click', function (e) {
			e.preventDefault();
			
			// Show progress indicator
			const originalText = saveBtn.textContent;
			saveBtn.textContent = 'Saving...';
			saveBtn.disabled = true;
			
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
					Saving filtered tickets...
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
				<?php if (!is_null($combo_id) && !empty($file_name)): ?>
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
				if (confirm('You are about to delete the Combination Ticket file: ' + fileName + '. Do You want to Continue? (Y/N)')) {
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
            } else {
                // Disable the checkboxes and grey out the checkmarks
                presetCheckboxes.forEach(checkbox => {
                    checkbox.disabled = true;
                    checkbox.checked = true; // Uncheck the checkbox
                });
                presetOptions.forEach(option => {
                    option.classList.remove('enabled');
                    option.classList.add('disabled');
                });
            }
        });
		
		// Initialize Bootstrap Table for Generated Combination Tickets
		<?php if (!empty($combos_paginated)): ?>
		$(document).ready(function() {
			$('#generated-tickets-table').bootstrapTable({
				filterControl: true,
				filterShowClear: true,
				pagination: false,
				search: true,
				showRefresh: true,
				showToggle: true,
				showColumns: true,
				sortName: 'ticket',
				sortOrder: 'asc',
				classes: 'table table-bordered table-striped',
				filterControlVisible: false, // Start with filters hidden
				onRefresh: function() {
					// Custom refresh logic if needed
					console.log('Table refreshed');
				},
				onToggle: function() {
					// Handle table view toggle
					console.log('Table view toggled');
				},
				onPostBody: function() {
					// Ensure all filters are blank after table is rendered
					$('.filter-control select').val('');
				}
			});
			
			// Custom styling for filter controls
			setTimeout(function() {
				// Customize filter dropdown options
				$('.filter-control select').each(function() {
					// Remove any existing empty options and add a completely blank one
					$(this).find('option[value=""]').remove();
					$(this).prepend('<option value=""></option>');
					// Set the dropdown to blank value
					$(this).val('');
				});
				
				// Style filter selects
				$('.filter-control select').each(function() {
					$(this).addClass('form-select');
				});
				
				// Clear any existing filters to show all data
				$('#generated-tickets-table').bootstrapTable('clearFilterControl');
			}, 100);
		});
		
		<?php endif; ?>
    });
    
    // Function to refresh filter settings
    function refreshFilter(comboId) {
        if (confirm('Loading the Previous Saved Settings. Do you want to continue? (Y/N)')) {
            window.location.href = '<?= base_url() ?>admin/predictions/refresh/<?= $lottery->id ?>?combo_id=' + comboId;
        }
    }
    
    // Function to view combination ticket winners
    function viewCombinationWinners(comboId) {
        // Use the filter record ID if available, otherwise show error
        <?php if (!empty($filter_record_id)): ?>
            var filterRecordId = <?= $filter_record_id ?>;
            var isActive = <?= $active ? 'true' : 'false' ?>;
            
            console.log('Navigating to combination winners with filter record ID:', filterRecordId, 'Active:', isActive);
            
            // Show warning for expired filters but still allow navigation
            if (!isActive) {
                if (confirm('This filter is EXPIRED. You can still view the combination ticket winners, but results will be based on historical data. Continue?')) {
                    // Add referrer parameter to indicate we came from prediction futures
                    window.location.href = '<?= base_url() ?>admin/prize/view_combination_tickets/' + filterRecordId + '?referrer=futures&lottery_id=<?= $lottery->id ?>&combo_id=<?= $combo_id ?>';
                }
            } else {
                // Active filter - navigate directly
                window.location.href = '<?= base_url() ?>admin/prize/view_combination_tickets/' + filterRecordId + '?referrer=futures&lottery_id=<?= $lottery->id ?>&combo_id=<?= $combo_id ?>';
            }
        <?php else: ?>
            alert('No saved filter found. Please save filtered tickets first before viewing combination winners.');
        <?php endif; ?>
    }
    
    function deleteFilter(comboId, fileName) {
        if (confirm('You are about to delete the Combination Ticket file: ' + fileName + '. Do You want to Continue? (Y/N)')) {
            // Future implementation for delete functionality
             window.location.href = '<?= base_url() ?>admin/predictions/delete_combo/' + comboId;
        }
    }
</script>