<link href="https://unpkg.com/bootstrap-table@1.18.0/dist/bootstrap-table.min.css" rel="stylesheet">
<link href="https://unpkg.com/bootstrap-table@1.18.0/dist/extensions/reorder-rows/bootstrap-table-reorder-rows.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.3/jquery.tablednd.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.18.0/dist/bootstrap-table.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.18.0/dist/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.18.0/dist/extensions/reorder-rows/bootstrap-table-reorder-rows.min.js"></script>
<style>
	@media (max-width: 768px) {
        .table th, .table td {
            display: table-cell; 	/* Ensure table cells remain visible */
            text-align: center; 	/* Center-align text for better readability */
            font-size: 12px; 		/* Adjust font size for smaller screens */
        }
        .table {
            overflow-x: auto; /* Allow horizontal scrolling */
        }
    }
    table {
        width: 100%;
    }
    #draws_filter, #draws_paginate {
        float: right;
    }
    label {
        display: inline-flex;
        margin-bottom: 0.5rem;
        margin-top: 0.5rem;
    }
</style>
	<h2 class="text-center">Winning Statistics</h2>
	<h2><?php echo 'Lottery: '.$lottery->lottery_name; ?></h2>	
	<h5 style = "text-align:left"><?php echo anchor('admin/predictions', 'Back to Predictions Dashboard', 'title="Back to Predictions"'); ?></h5>
	<section>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="card mt-3 tab-card">
						<div class="card-header tab-card-header" style="background-color: #ffffff;">
							<h4 class="text-center">Combination File: <strong><?= $file_name.'.txt'; ?></strong></h4>
							<div class="row text-center mt-3">
								<div class="col-md-4">
									<strong>Picks per Ticket:</strong> <?= $pick_per_ticket; ?> Numbers
								</div>
								<div class="col-md-4">
									<strong>Numbers to Pick:</strong> <?= $numbers_to_pick; ?>
								</div>
								<div class="col-md-4">
									<strong>Tickets:</strong> <?= $tickets; ?>
								</div>
							</div>
						<div class="container mt-4">
							<div class="table-responsive">
								<table class="table table-striped table-hover table-bordered">
									<thead class="thead-dark">
										<tr>
											<th scope="col" style="text-align:center;">Prize Tier</th>
											<th scope="col" style="text-align:center;">Tickets</th>
											<th scope="col" style="text-align:center;">Percentage of Wins (%)</th>
											<th scope="col" style="text-align:center;">Probability Chance of Winning (%)</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach (array_reverse($stats) as $stat): ?>
											<tr>
												<td><?= $stat['tier']; ?></td>
												<td style="text-align:center;"><?= $stat['tickets']; ?></td>
												<td style="text-align:center;"><?= $stat['percentage']; ?>%</td>
												<td style="text-align:center;"><?= $stat['probability']; ?>%</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
								<div class="form-group form-group-lg row">
									<?php 
										$js = "location.href='".base_url()."admin/predictions/files/".$lottery->id."'";
										$attributes = array(
										'class' 	=> "btn btn-primary btn-lg btn-info",
										'onClick' 	=> "$js", 
										'style' 	=> "padding:5px; display: block; margin:20px 20px;"
									);
									echo form_button('combination_list', 'Back to Combination List', $attributes); 
									$js = "location.href='".base_url()."admin/predictions/'";
									$attributes = array(
									'class' 	=> "btn btn-primary btn-lg btn-info",
									'onClick' 	=> "$js", 
									'style' 	=> "padding:5px; display: block; margin:20px 20px;"
								);
									echo form_button('prediction_list', 'Back to Prediction List', $attributes); 
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>