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
						<?php
						$country_options = ['' => 'Select Country'] + $countries; // $countries should be passed from the controller
						$extra = ['class' => 'form-control', 'id' => 'country', 'onchange' => 'fetchProvinces(this.value)'];
						echo form_dropdown('country', $country_options, set_value('country', $lottery->lottery_country_id), $extra);
						echo form_error('country', '<div class="bg-warning mt-2 p-2 text-center text-white">', '</div>');
						?>
					</div>
				</div>
				<!-- Province/State Dropdown -->
				<div class="form-group row justify-content-center">
					<?php
					$extra = ['class' => 'col-4 col-form-label col-form-label-md text-right'];
					echo form_label('Province/State', 'province', $extra);
					?>
					<div class="col-6">
						<?php
						$province_options = ['' => 'Select Province/State', 'ALL' => 'ALL']; // Default options
						$extra = ['class' => 'form-control', 'id' => 'province', 'onchange' => 'fetchLotteryGames(this.value)', 'disabled' => 'disabled'];
						echo form_dropdown('province', $province_options, set_value('province', $lottery->lottery_state_prov), $extra);
						echo form_error('province', '<div class="bg-warning mt-2 p-2 text-center text-white">', '</div>');
						?>
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
    $(document).ready(function () {
        // Fetch provinces/states when a country is selected
        $('#country').change(function () {
            var country_id = $(this).val();
            $('#province').prop('disabled', true).empty().append('<option value="">Select Province/State</option><option value="ALL">ALL</option>');
            $('#lottery').prop('disabled', true).empty().append('<option value="">Select Lottery Game</option>');
            $('#wheeling').prop('disabled', true).empty().append('<option value="">Select Wheeling Table</option>');
            $('#submit-btn').prop('disabled', true);

            if (country_id) {
                $.ajax({
                    url: '<?php echo base_url("admin/predictions/get_provinces/"); ?>' + country_id,
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#province').prop('disabled', false);
                        $.each(data, function (key, value) {
                            $('#province').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            }
        });

        // Fetch lottery games when a province/state is selected
        $('#province').change(function () {
            var country_id = $('#country').val();
            var province_id = $(this).val();
            $('#lottery').prop('disabled', true).empty().append('<option value="">Select Lottery Game</option>');
            $('#wheeling').prop('disabled', true).empty().append('<option value="">Select Wheeling Table</option>');
            $('#submit-btn').prop('disabled', true);

            if (province_id) {
                $.ajax({
                    url: '<?php echo base_url("admin/predictions/get_lottery_games/"); ?>' + country_id + '/' + province_id,
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#lottery').prop('disabled', false);
                        $.each(data, function (key, value) {
                            $('#lottery').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            }
        });

        // Fetch wheeling tables when a lottery game is selected
        $('#lottery').change(function () {
            var lottery_id = $(this).val();
            $('#wheeling').prop('disabled', true).empty().append('<option value="">Select Wheeling Table</option>');
            $('#submit-btn').prop('disabled', true);

            if (lottery_id) {
                $.ajax({
                    url: '<?php echo base_url("admin/predictions/get_wheeling_tables/"); ?>' + lottery_id,
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#wheeling').prop('disabled', false);
                        $.each(data, function (key, value) {
                            $('#wheeling').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            }
        });

        // Enable the submit button when a wheeling table is selected
        $('#wheeling').change(function () {
            if ($(this).val()) {
                $('#submit-btn').prop('disabled', false);
            } else {
                $('#submit-btn').prop('disabled', true);
            }
        });
    });
</script>