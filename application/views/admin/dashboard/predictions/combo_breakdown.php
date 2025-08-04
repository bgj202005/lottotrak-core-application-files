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
            font-size: 11px; 		/* Adjust font size for smaller screens */
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
    .breakdown-table th {
        background-color: #343a40;
        color: white;
        text-align: center;
        vertical-align: middle;
    }
    .breakdown-table td {
        text-align: center;
        vertical-align: middle;
    }
    .scenario-header {
        background-color: #17a2b8;
        color: white;
        font-weight: bold;
    }
    .subprize-cell {
        background-color: #f8f9fa;
    }
    .percentage-cell {
        font-size: 0.9em;
        color: #6c757d;
    }
</style>

<h2 class="text-center">Winning Statistics - Detailed Breakdown</h2>
<h2><?php echo 'Lottery: '.$lottery->lottery_name; ?></h2>

<?php if ($is_independent_extra_ball): ?>
<div class="alert alert-info">
	<h5><i class="fa fa-star"></i> Independent Extra Ball Lottery (Pick <?= $pick_per_ticket; ?>)</h5>
	<p>This lottery has an <strong>independent extra ball</strong> drawn from a range of <?= $extra_ball_range; ?> numbers, which means the extra ball can duplicate main numbers. Additional prize tiers with extra ball matches are shown in <span style="color: #28a745; font-weight: bold;">green columns</span> below.</p>
	<p><strong>Applies to:</strong> Pick 6, Pick 7, Pick 8, and Pick 9 lotteries with independent extra ball enabled.</p>
</div>
<?php endif; ?>

<h5 style="text-align:left"><?php echo anchor('admin/predictions', 'Back to Predictions Dashboard', 'title="Back to Predictions"'); ?></h5>

<section>
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="card mt-3 tab-card">
					<div class="card-header tab-card-header" style="background-color: #ffffff;">
						<h4 class="text-center">Combination File: <strong><?= $file_name.'.txt'; ?></strong></h4>
						<div class="row text-center mt-3">
							<div class="col-md-3">
								<strong>Pick per Ticket:</strong> <?= (int) $pick_per_ticket; ?> Numbers
							</div>
							<div class="col-md-3">
								<strong>Numbers to Pick:</strong> <?= (int) $numbers_to_pick; ?>
							</div>
							<div class="col-md-3">
								<strong>Total Tickets:</strong> <?= number_format($breakdown_data['total_tickets']); ?>
							</div>
							<div class="col-md-3">
								<strong>Min Prize Match:</strong> <?= $minimum_prize_match; ?>
							</div>
						</div>
					</div>
					
					<div class="card-body">
						<div class="alert alert-info">
							<h5><i class="fa fa-info-circle"></i> Understanding the Breakdown</h5>
							<p><strong>Example:</strong> For a Pick <?= $pick_per_ticket; ?> lottery where you select <?= $numbers_to_pick; ?> numbers (Full Coverage System):</p>
							<ul>
								<li><strong>Picked Correctly:</strong> How many of the <?= $pick_per_ticket; ?> drawn numbers are in your <?= $numbers_to_pick; ?> selected numbers</li>
								<li><strong>Sub-prizes:</strong> Number of tickets that win various prize tiers for that scenario</li>
								<?php if ($is_independent_extra_ball): ?>
								<li><strong>Extra Ball Prizes:</strong> <span style="color: #28a745; font-weight: bold;">Green columns</span> show combinations that also match the independent extra ball (1 in <?= $extra_ball_range; ?> chance)</li>
								<?php endif; ?>
								<li><strong>Percentage:</strong> Probability of each outcome as a percentage of total tickets</li>
								<li><strong>Total Tickets:</strong> <?= number_format($breakdown_data['total_tickets']); ?> (using formula C(<?= $numbers_to_pick; ?>,<?= $pick_per_ticket; ?>))</li>
							</ul>
							<p><strong>Interpretation:</strong> If <?= $scenario['picked_correctly'] ?? $pick_per_ticket; ?> of the drawn numbers match your selection, the table shows how many tickets win at different prize levels.</p>
							<?php if ($is_independent_extra_ball): ?>
							<p><strong>Independent Extra Ball:</strong> The extra ball is drawn from a range of <?= $extra_ball_range; ?> numbers and can duplicate main numbers. This creates additional prize tiers (e.g., <?= $pick_per_ticket; ?>+Extra, <?= $pick_per_ticket - 1; ?>+Extra, etc.) with better payouts than regular matches.</p>
							<?php endif; ?>
						</div>
						
						<div class="table-responsive">
							<table class="table table-striped table-hover table-bordered breakdown-table">
								<thead>
									<tr>
										<th rowspan="2" style="vertical-align:middle;">Picked Correctly<br><small>(Out of <?= $numbers_to_pick; ?>)</small></th>
										<?php 
										// Create header for regular matches
										for ($i = $pick_per_ticket; $i >= $minimum_prize_match; $i--): ?>
											<th><?= $i; ?> Match<?= $i > 1 ? 'es' : ''; ?></th>
										<?php endfor; 
										
										// Create header for extra ball matches if independent extra ball lottery
										if ($is_independent_extra_ball):
											for ($i = $pick_per_ticket; $i >= 1; $i--): ?>
												<th style="background-color: #28a745; color: white;"><?= $i; ?>+Extra</th>
											<?php endfor;
										endif; ?>
										<th>Non-Winners</th>
									</tr>
									<tr>
										<?php 
										// Regular match sub-headers
										for ($i = $pick_per_ticket; $i >= $minimum_prize_match; $i--): ?>
											<th style="font-size:0.8em; font-weight:normal;">Tickets (% of Total)</th>
										<?php endfor; 
										
										// Extra ball match sub-headers
										if ($is_independent_extra_ball):
											for ($i = $pick_per_ticket; $i >= 1; $i--): ?>
												<th style="font-size:0.8em; font-weight:normal; background-color: #28a745; color: white;">Tickets (% of Total)</th>
											<?php endfor;
										endif; ?>
										<th style="font-size:0.8em; font-weight:normal;">Tickets</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($breakdown_data['breakdown'] as $scenario): ?>
										<tr>
											<td class="scenario-header">
												<?= $scenario['picked_correctly']; ?>
											</td>
											<?php 
											$row_total_percentage = 0;
											
											// Display regular match columns
											for ($match_level = $pick_per_ticket; $match_level >= $minimum_prize_match; $match_level--): 
												if (isset($scenario['subprizes'][$match_level])): 
													$tickets = $scenario['subprizes'][$match_level]['tickets'];
													$percentage = $scenario['subprizes'][$match_level]['percentage'];
													$row_total_percentage += $percentage;
												?>
													<td class="subprize-cell">
														<?= number_format($tickets); ?><br>
														<small class="percentage-cell">(<?= $percentage; ?>%)</small>
													</td>
												<?php else: ?>
													<td class="subprize-cell">
														0<br>
														<small class="percentage-cell">(0%)</small>
													</td>
												<?php endif; 
											endfor; 
											
											// Display extra ball match columns for independent extra ball lotteries
											if ($is_independent_extra_ball):
												for ($match_level = $pick_per_ticket; $match_level >= 1; $match_level--): 
													if (isset($scenario['extra_ball_subprizes'][$match_level])): 
														$tickets = $scenario['extra_ball_subprizes'][$match_level]['tickets'];
														$percentage = $scenario['extra_ball_subprizes'][$match_level]['percentage'];
														$row_total_percentage += $percentage;
													?>
														<td class="subprize-cell" style="background-color: #d4edda;">
															<?= number_format($tickets); ?><br>
															<small class="percentage-cell">(<?= $percentage; ?>%)</small>
														</td>
													<?php else: ?>
														<td class="subprize-cell" style="background-color: #d4edda;">
															0<br>
															<small class="percentage-cell">(0%)</small>
														</td>
													<?php endif; 
												endfor;
											endif;
											
											// Calculate non-winning tickets for this scenario
											$total_scenario_tickets = 0;
											foreach ($scenario['subprizes'] as $subprize) {
												$total_scenario_tickets += $subprize['tickets'];
											}
											
											// Add extra ball tickets to total if applicable
											if ($is_independent_extra_ball && isset($scenario['extra_ball_subprizes'])) {
												foreach ($scenario['extra_ball_subprizes'] as $subprize) {
													$total_scenario_tickets += $subprize['tickets'];
												}
											}
											
											// Use the calculated non-winning tickets from the model
											$non_winning_tickets = $scenario['non_winning_tickets'];
											$non_winning_percentage = $breakdown_data['total_tickets'] > 0 ? round(($non_winning_tickets / $breakdown_data['total_tickets']) * 100, 2) : 0;
											?>
											<td>
												<?= number_format($non_winning_tickets); ?><br>
												<small class="percentage-cell">(<?= $non_winning_percentage; ?>%)</small>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
						
						<div class="mt-4">
							<h5>Mathematical Formula Used:</h5>
							<div class="alert alert-secondary">
								<p><strong>Combinatorial Formula:</strong> nCr = n! / (r! × (n-r)!)</p>
								<p><strong>For each scenario:</strong> C(correct_numbers, matches) × C(non_winning_numbers, remaining_positions)</p>
								<?php if ($is_independent_extra_ball): ?>
								<p><strong>Extra Ball Formula:</strong> (Base tickets) × (1/<?= $extra_ball_range; ?>) - probability of matching the independent extra ball</p>
								<?php endif; ?>
								<p><strong>Total Tickets:</strong> C(<?= $numbers_to_pick; ?>, <?= $pick_per_ticket; ?>) = <?= number_format($breakdown_data['total_tickets']); ?></p>
								
								<?php if (!empty($breakdown_data['breakdown'])): 
									$example = $breakdown_data['breakdown'][0]; // Use the first scenario as example
								?>
								<hr>
								<p><strong>Example Calculation (<?= $example['picked_correctly']; ?> numbers correct):</strong></p>
								<ul>
									<?php foreach ($example['subprizes'] as $matches => $data): ?>
									<li><?= $matches; ?> matches: C(<?= $example['picked_correctly']; ?>, <?= $matches; ?>) × C(<?= $numbers_to_pick - $example['picked_correctly']; ?>, <?= $pick_per_ticket - $matches; ?>) = <?= number_format($data['tickets']); ?> tickets (<?= $data['percentage']; ?>%)</li>
									<?php endforeach; ?>
									
									<?php if ($is_independent_extra_ball && !empty($example['extra_ball_subprizes'])): ?>
									<li style="color: #28a745; font-weight: bold;">Extra Ball Combinations:</li>
									<?php foreach ($example['extra_ball_subprizes'] as $matches => $data): ?>
									<li style="color: #28a745;"><?= $matches; ?>+Extra: <?= number_format($data['tickets']); ?> tickets (<?= $data['percentage']; ?>%) - Base tickets ÷ <?= $extra_ball_range; ?></li>
									<?php endforeach; ?>
									<?php endif; ?>
								</ul>
								<?php endif; ?>
							</div>
						</div>
						
						<div class="form-group form-group-lg row mt-4">
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
