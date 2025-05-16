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
        padding: 20px;
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
	table{
  		border:1px solid black;
  		display:inline-block;
  		max-width: 178px;
  		margin:20px;
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
    }
    /* Style the row with checkboxes */
    .checkbox-row {
        background-color: #000; /* Black background */
        color: #fff; /* White text */
    }
    .checkbox-row .preset-checkbox {
        accent-color: #fff; /* White checkbox color */
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
						<?php echo form_open(base_url().'admin/predictions/futures/'.$lottery->id); ?>
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
								echo form_dropdown('wheeling', $wheeling_options, set_value('wheeling', ''), $extra);

								// Display form error if any
								echo form_error('wheeling', '<div class="bg-warning mt-2 p-2 text-center text-white">', '</div>');
								?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
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
													echo form_checkbox('hwc', '1', TRUE, $js);
													?>
												</td>
												<td data-label="Range"><?php echo $h_w_c['range']; ?></td>
												<td data-label="Extra Draws?"> <span class="preset-option disabled"><?php echo $h_w_c['extra_draws'] ? '✓' : ''; ?></span></td>
												<td data-label="Includes Extra?"><span class="preset-option disabled"><?php echo $h_w_c['extra_included'] ? '✓' : ''; ?></span></td>

												<!-- Followers -->
												<td data-label="Followers">
													<?php
													$js = 'id="followers-checkbox" class="preset-checkbox" disabled';
													echo form_checkbox('followers', '1', TRUE, $js);
													?>
												</td>
												<td data-label="Range"><?php echo $followers['range']; ?></td>
												<td data-label="Extra Draws?"><span class="preset-option disabled"><?php echo $followers['extra_draws'] ? '✓' : ''; ?></span></td>
												<td data-label="Includes Extra?"><span class="preset-option disabled"><?php echo $followers['extra_included'] ? '✓' : ''; ?></span></td>
												<!-- Friends -->
												<td data-label="Friends">
													<?php
													$js = 'id="friends-checkbox" class="preset-checkbox" disabled';
													echo form_checkbox('friends', '1', TRUE, $js);
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
						<!-- Submit Button -->
						<div class="form-group text-center">
							<?php
							$extra = ['class' => 'btn btn-primary btn-lg', 'id' => 'submit-btn', 'disabled' => 'disabled'];
							echo form_submit('submit', 'Ganerate Tickets', $extra);
							?>
						</div>
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
                    checkbox.checked = false; // Uncheck the checkbox
                });
                presetOptions.forEach(option => {
                    option.classList.remove('enabled');
                    option.classList.add('disabled');
                });
            }
        });
    });
</script>