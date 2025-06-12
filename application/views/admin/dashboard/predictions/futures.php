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
		width: 95% !important;
		max-width: 95% !important;
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
        overflow-x: auto;
        -webkit-overflow-scrolling: touch; /* Smooth scrolling for mobile */
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
</style>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<h2><?php echo 'Prediction Futures for: '.$lottery->lottery_name; ?></h2>
	<h5 style = "text-align:left"><?php echo anchor('admin/predictions', 'Back to Predictions Dashboard', 'title="Back to Predictions"'); ?></h5>
		<section>
			<div class="container mt-4">
				<!-- White Tile (Card) -->
				<div class="card shadow-sm">
					<div class="card-body">
						<h3 class="card-title text-center">Prediction Futures</h3>
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
							echo form_label('Combination Table', 'wheeling', $extra);
							?>
							<div class="col-6">
								<?php
								// Prepare the dropdown options
								$wheeling_options = ['' => 'Select Combination Table']; // Default option
								if (!empty($combination_files)) {
									foreach ($combination_files as $file) {
										$wheeling_options[$file['file_name']] = '(' . $file['file_name'] . ')     ' . $file['N'] . ' Numbers - ' . $file['CCCC'] . ' Tickets';
									}
								}
								// Dropdown attributes
								$extra = ['class' => 'form-control', 'id' => 'wheeling','style' => 'width: 70%;'];
								echo form_dropdown('wheeling', $wheeling_options, isset($selected_wheeling) ? $selected_wheeling : set_value('wheeling', ''), $extra);
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
														<?= form_dropdown('friends', [
															'all' => 'ALL',
															'0' => '0 Friends',
															'1' => '1-Way',
															'2' => '2-Way'
														], isset($selected_friends) ? $selected_friends : '', 'class="form-control" id="friends"') ?>
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
							$extra = ['class' => 'btn btn-primary btn-lg', 'id' => 'submit-btn', 'disabled' => 'disabled'];
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
						<?php if (!empty($combos_paginated)): ?>
							<form method="get" class="mb-3" id="pagination-size-form">
								<label for="per_page" class="me-2">Combinations per page:</label>
								<select name="per_page" id="per_page" class="form-select d-inline-block w-auto" onchange="document.getElementById('pagination-size-form').submit();">
									<?php
									$sizes = [10, 20, 50, 100, 200];
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
							<div class="mb-4">
								<h4>Generated Combination Tickets</h4>
								<div class="table-responsive mb-4">
									<table class="table table-bordered table-striped generated-tickets-table" style="width:95%; margin:0 auto;">
										<thead class="table-dark">
											<tr>
												<th>#</th>
												<th>Combination</th>
												<th>Sum</th>
												<th>Digit Sum</th>
												<th>Repeaters</th>
												<th>Consecutive</th>
												<th>Even</th>
												<th>Odd</th>
												<th>Decade</th>
												<th>Last</th>
												<th>Range</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($combos_paginated as $idx => $item): ?>
												<tr>
													<td><?= (($pagination['current']-1)*$pagination['per_page'])+$idx+1 ?></td>
													<td>
														<?php
															$ticket_numbers = array_values($item['combo']);
															sort($ticket_numbers, SORT_NUMERIC);
															echo implode(' ', $ticket_numbers);
														?>
													</td>
													<td><?= $item['stats']['sum'] ?></td>
													<td><?= $item['stats']['digit_sum'] ?></td>
													<td><?= $item['stats']['repeater'] ?></td>
													<td><?= $item['stats']['consecutive'] ?></td>
													<td><?= $item['stats']['even'] ?></td>
													<td><?= $item['stats']['odd'] ?></td>
													<td><?= $item['stats']['decade'] ?></td>
													<td><?= $item['stats']['last'] ?></td>
													<td><?= $item['stats']['range'] ?></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<!-- Bootstrap Pagination here -->
								<nav>
									<ul class="pagination justify-content-center">
										<?php
										$current = $pagination['current'];
										$total = $pagination['total'];
										$per_page = $pagination['per_page'];

										// Previous arrow
										$prev_disabled = ($current <= 1) ? 'disabled' : '';
										$prev_page = max(1, $current - 1);
										?>
										<li class="page-item <?= $prev_disabled ?>">
											<a class="page-link" href="?page=<?= $prev_page ?>&per_page=<?= $per_page ?>" aria-label="Previous">
												<span aria-hidden="true">&laquo;</span>
											</a>
										</li>

										<?php
										// Dot notation logic
										if ($total <= 10) {
											// Show all pages
											for ($i = 1; $i <= $total; $i++) {
												$active = ($i == $current) ? 'active' : '';
												echo '<li class="page-item '.$active.'"><a class="page-link" href="?page='.$i.'&per_page='.$per_page.'">'.$i.'</a></li>';
											}
										} else {
											// Always show first page
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
													echo '<li class="page-item '.$active.'"><a class="page-link" href="?page='.$i.'&per_page='.$per_page.'">'.$i.'</a></li>';
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
											<a class="page-link" href="?page=<?= $next_page ?>&per_page=<?= $per_page ?>" aria-label="Next">
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
    	var deleteBtn = document.getElementById('delete-filtered-btn');
		
		// Initial state
		saveBtn.disabled = true;
		deleteBtn.disabled = true;
		generateBtn.disabled = true;
		
		// Enable Generate Tickets when a combination table is selected
		combinationDropdown.addEventListener('change', function () {
			if (combinationDropdown.value) {
				generateBtn.disabled = false;
				saveBtn.disabled = true;
				deleteBtn.disabled = true;
			} else {
				generateBtn.disabled = true;
				saveBtn.disabled = true;
				deleteBtn.disabled = true;
			}
		});
		generateBtn.addEventListener('click', function (e) {
        // You may want to check if tickets are actually generated before enabling
			setTimeout(function() {
				saveBtn.disabled = false;
			}, 500); // Adjust delay as needed for your ticket generation process
		});

		// After Save Filtered Tickets is clicked, enable Delete Filtered Tickets
		saveBtn.addEventListener('click', function (e) {
			deleteBtn.disabled = false;
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
    });
</script>