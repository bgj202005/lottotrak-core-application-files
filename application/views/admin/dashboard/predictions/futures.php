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
  		border:1px solid black;
  		display:inline-block;
  		max-width: 178px;
  		margin:20px;
	}
	/* hwc */
	table hwc{
    	width:100%;
	}
	tr hwc{
		font-size: 0.90em;
	}
	th hwc{
		text-align:center;
		font-size: 0.90em;
	}
	table.hwc tr{
		font-size: 0.65em;
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
	#hwc_paginate{
    float:right;
	}	  
</style>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<h2><?php echo 'Prediction Futures for: '.$lottery->lottery_name; ?></h2>
	<h5 style = "text-align:left"><?php echo anchor('admin/predictions', 'Back to Predictions Dashboard', 'title="Back to Predictions"'); ?></h5>
		<section>
			<div class="container">
				<h3 class="text-center mt-4">Prediction Futures</h3> <!-- Centered h3 -->
				<!-- Country Dropdown -->
				<div class="form-group row justify-content-center">
					<?php
					$extra = ['class' => 'col-4 col-form-label col-form-label-md text-right'];
					echo form_label('Country', 'country', $extra);
					?>
					<div class="col-6">
						<span id="country-name"></span>	
					</div>
				</div>
				<!-- Province/State Dropdown -->
				<div class="form-group row justify-content-center">
					<?php
					$extra = ['class' => 'col-4 col-form-label col-form-label-md text-right'];
					echo form_label('Province/State', 'province', $extra);
					?>
					<div class="col-6">
						<span id="state-name"></span>
					</div>
				</div>
				<!-- Lottery Game Dropdown -->
				<div class="form-group row justify-content-center">
					<?php
					$extra = ['class' => 'col-4 col-form-label col-form-label-md text-right'];
					echo form_label('Lottery Game', 'lottery', $extra);
					?>
					<div class="col-6">
						<?php
						$lottery_options = ['' => 'Select Lottery Game']; // Default options
						$extra = ['class' => 'form-control', 'id' => 'lottery', 'onchange' => 'fetchWheelingTables(this.value)', 'disabled' => 'disabled'];
						echo form_dropdown('lottery', $lottery_options, set_value('lottery', $lottery->lottery_name), $extra);
						echo form_error('lottery', '<div class="bg-warning mt-2 p-2 text-center text-white">', '</div>');
						?>
					</div>
				</div>
				<!-- Wheeling Table Dropdown -->
				<div class="form-group row justify-content-center">
					<?php
					$extra = ['class' => 'col-4 col-form-label col-form-label-md text-right'];
					echo form_label('Wheeling Table', 'wheeling', $extra);
					?>
					<div class="col-6">
						<?php
						$wheeling_options = ['' => 'Select Wheeling Table']; // Default options
						$extra = ['class' => 'form-control', 'id' => 'wheeling', 'disabled' => 'disabled'];
						echo form_dropdown('wheeling', $wheeling_options, set_value('wheeling', ''), $extra);
						echo form_error('wheeling', '<div class="bg-warning mt-2 p-2 text-center text-white">', '</div>');
						?>
					</div>
				</div>
				<!-- Submit Button -->
				<div class="form-group text-center">
					<?php
					$extra = ['class' => 'btn btn-primary btn-lg', 'id' => 'submit-btn', 'disabled' => 'disabled'];
					echo form_submit('submit', 'Save Prediction', $extra);
					?>
				</div>
				<?php echo form_close(); ?>
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
</script>