<link href="https://unpkg.com/bootstrap-table@1.18.0/dist/bootstrap-table.min.css" rel="stylesheet">
<link href="https://unpkg.com/bootstrap-table@1.18.0/dist/extensions/reorder-rows/bootstrap-table-reorder-rows.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.3/jquery.tablednd.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.18.0/dist/bootstrap-table.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.18.0/dist/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.18.0/dist/extensions/reorder-rows/bootstrap-table-reorder-rows.min.js"></script>
<style>
	<?php if ($is_independent_extra_ball): ?>
	/* Smaller fonts for independent extra ball lotteries due to more columns */
	.breakdown-table th {
        background-color: #343a40;
        color: white;
        text-align: center;
        vertical-align: middle;
        font-size: 0.75rem;
        padding: 0.3rem;
    }
    .breakdown-table td {
        text-align: center;
        vertical-align: middle;
        font-size: 0.7rem;
        padding: 0.25rem;
    }
    .scenario-header {
        background-color: #17a2b8;
        color: white;
        font-weight: bold;
        font-size: 0.8rem;
    }
    .percentage-cell {
        font-size: 0.65rem;
        color: #212529;
        font-weight: 500;
    }
    @media (max-width: 768px) {
        .breakdown-table th, .breakdown-table td {
            font-size: 0.6rem;
            padding: 0.2rem;
        }
        .scenario-header {
            font-size: 0.7rem;
        }
        .percentage-cell {
            font-size: 0.55rem;
            color: #212529;
            font-weight: 500;
        }
    }
    @media (max-width: 576px) {
        .breakdown-table th, .breakdown-table td {
            font-size: 0.55rem;
            padding: 0.15rem;
        }
        .scenario-header {
            font-size: 0.65rem;
        }
        .percentage-cell {
            font-size: 0.5rem;
            color: #212529;
            font-weight: 500;
        }
    }
    <?php else: ?>
    /* Regular fonts for standard lotteries */
    .breakdown-table th {
        background-color: #343a40;
        color: white;
        text-align: center;
        vertical-align: middle;
        font-size: 0.9rem;
        padding: 0.5rem;
    }
    .breakdown-table td {
        text-align: center;
        vertical-align: middle;
        font-size: 0.85rem;
        padding: 0.4rem;
    }
    .scenario-header {
        background-color: #17a2b8;
        color: white;
        font-weight: bold;
        font-size: 0.9rem;
    }
    .percentage-cell {
        font-size: 0.8rem;
        color: #212529;
        font-weight: 500;
    }
    @media (max-width: 768px) {
        .breakdown-table th, .breakdown-table td {
            font-size: 0.8rem;
            padding: 0.3rem;
        }
        .scenario-header {
            font-size: 0.85rem;
        }
        .percentage-cell {
            font-size: 0.75rem;
            color: #212529;
            font-weight: 500;
        }
    }
    <?php endif; ?>
    
    .subprize-cell {
        background-color: #f8f9fa;
    }
    .extra-ball-header {
        background-color: #28a745;
        color: white;
    }
    .extra-only-header {
        background-color: #ffc107;
        color: black;
    }
    .table-responsive {
        overflow-x: hidden;
    }
</style>

<h2 class="text-center">Winning Statistics - Detailed Breakdown</h2>
<h2><?php echo 'Lottery: '.$lottery->lottery_name; ?></h2>

<h5 style="text-align:left"><?php echo anchor('admin/predictions', 'Back to Predictions Dashboard', 'title="Back to Predictions"'); ?></h5>

<?php if ($is_independent_extra_ball): ?>
<div class="alert alert-info">
	<h5><i class="fa fa-star"></i> Independent Extra Ball Lottery (Pick <?= $pick_per_ticket; ?>)</h5>
	<p>This lottery has an <strong>independent extra ball</strong> drawn from a range of <?= $extra_ball_range; ?> numbers, which means the extra ball can duplicate main numbers. Additional prize tiers with extra ball matches are shown in <span style="color: #28a745; font-weight: bold;">green columns</span> below.</p>
	<p><strong>Applies to:</strong> Pick 5, Pick 6, Pick 7, Pick 8, and Pick 9 lotteries with independent extra ball enabled.</p>
</div>
<?php endif; ?>

<section>
	<div class="container-fluid px-2">
		<div class="row">
			<div class="col-12">
				<div class="card mt-3 tab-card">
					<div class="card-header tab-card-header" style="background-color: #ffffff;">
						<h4 class="text-center">Combination File: <strong><?= $file_name.'.txt'; ?></strong></h4>
						<div class="row text-center mt-3">
							<div class="col-6 col-md-3">
								<strong>Pick per Ticket:</strong> <?= (int) $pick_per_ticket; ?>
							</div>
							<div class="col-6 col-md-3">
								<strong>Numbers Picked:</strong> <?= (int) $numbers_to_pick; ?>
							</div>
							<div class="col-6 col-md-3">
								<strong>Total Tickets:</strong> <?= number_format($breakdown_data['total_tickets']); ?>
							</div>
							<div class="col-6 col-md-3">
								<strong>Min Prize Match:</strong> 
								<?php if ($is_independent_extra_ball): ?>
									Extra Only
								<?php else: ?>
									<?= $minimum_prize_match; ?>
								<?php endif; ?>
							</div>
						</div>
					</div>
					
					<div class="card-body">
						<div class="alert alert-info">
							<h5><i class="fa fa-info-circle"></i> Understanding the Breakdown</h5>
							<p><strong>Example:</strong> Pick <?= $pick_per_ticket; ?> lottery, select <?= $numbers_to_pick; ?> numbers (Full Coverage):</p>
							<ul class="mb-2">
								<?php if ($is_independent_extra_ball): ?>
								<li><strong>Left Column:</strong> Shows scenarios by number of correct main numbers picked</li>
								<li><strong>Regular Columns:</strong> Prizes for main numbers only (without extra ball)</li>
								<li><strong>Green Columns:</strong> Prizes for main numbers + extra ball (e.g., "1+E" = 1 main + extra)</li>
								<li><strong>Yellow Column:</strong> "Extra Only" prize (no main numbers, just extra ball)</li>
								<?php else: ?>
								<li><strong>Left Column:</strong> How many drawn numbers match your selection</li>
								<li><strong>Prize Columns:</strong> Tickets winning each prize tier</li>
								<?php endif; ?>
								<li><strong>Non-Win:</strong> Tickets that don't win any prize</li>
								<li><strong>Total:</strong> <?= number_format($breakdown_data['total_tickets']); ?> tickets (C(<?= $numbers_to_pick; ?>,<?= $pick_per_ticket; ?>)<?= $is_independent_extra_ball ? ' × 2' : ''; ?>)</li>
							</ul>
							<?php if ($is_independent_extra_ball): ?>
							<p><strong>Extra Ball:</strong> Range 1-<?= $extra_ball_range; ?>, creates prizes like <?= $pick_per_ticket; ?>+E, <?= $pick_per_ticket - 1; ?>+E, etc.</p>
							<?php endif; ?>
						</div>
						
						<div class="table-responsive">
							<table class="table table-striped table-hover table-bordered breakdown-table table-sm">
								<thead>
									<tr>
										<th rowspan="2" style="vertical-align:middle; min-width: 60px;">Picked</th>
										<?php 
										// Create header for regular matches
										for ($i = $pick_per_ticket; $i >= $minimum_prize_match; $i--): ?>
											<th style="min-width: 50px;"><?= $i; ?><?= $i > 1 ? '' : ''; ?></th>
										<?php endfor; 
										
										// Create header for extra ball matches if independent extra ball lottery
										if ($is_independent_extra_ball):
											for ($i = $pick_per_ticket; $i >= 1; $i--): ?>
												<th class="extra-ball-header" style="min-width: 50px;"><?= $i; ?>+E</th>
											<?php endfor; ?>
											<th class="extra-only-header" style="min-width: 45px;">Extra</th>
										<?php endif; ?>
										<th style="min-width: 60px;">Non-Win</th>
									</tr>
									<tr>
										<?php 
										// Regular match sub-headers
										for ($i = $pick_per_ticket; $i >= $minimum_prize_match; $i--): ?>
											<th style="font-size:0.6rem; font-weight:normal;">Tickets</th>
										<?php endfor; 
										
										// Extra ball match sub-headers
										if ($is_independent_extra_ball):
											for ($i = $pick_per_ticket; $i >= 1; $i--): ?>
												<th class="extra-ball-header" style="font-size:0.6rem; font-weight:normal;">Tickets</th>
											<?php endfor; ?>
											<th class="extra-only-header" style="font-size:0.6rem; font-weight:normal;">Tickets</th>
										<?php endif; ?>
										<th style="font-size:0.6rem; font-weight:normal;">Tickets</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($breakdown_data['breakdown'] as $scenario): ?>
										<?php 
										// Skip scenarios below minimum prize match for regular lotteries
										if (!$is_independent_extra_ball && $scenario['picked_correctly'] < $breakdown_data['minimum_prize_match']) {
											continue;
										}
										?>
										
										<?php if ($is_independent_extra_ball): ?>
											<!-- Row for main numbers only (without extra ball) -->
											<tr>
												<td class="scenario-header">
													<?= $scenario['picked_correctly']; ?>
												</td>
												<?php 
												// Display regular match columns
												for ($match_level = $pick_per_ticket; $match_level >= $minimum_prize_match; $match_level--): 
													if (isset($scenario['subprizes'][$match_level])): 
														$tickets = $scenario['subprizes'][$match_level]['tickets'];
														$percentage = $scenario['subprizes'][$match_level]['percentage'];
													?>
														<td class="subprize-cell">
															<?= number_format($tickets); ?><br>
															<small class="percentage-cell"><?= $percentage; ?>%</small>
														</td>
													<?php else: ?>
														<td class="subprize-cell">
															0<br>
															<small class="percentage-cell">0%</small>
														</td>
													<?php endif; 
												endfor; 
												
												// Empty cells for extra ball columns (shown in next row)
												for ($match_level = $pick_per_ticket; $match_level >= 1; $match_level--): ?>
													<td class="subprize-cell" style="background-color: #f8f9fa;">
														-<br>
														<small class="percentage-cell">-</small>
													</td>
												<?php endfor; ?>
												
												<!-- Empty Extra only column -->
												<td class="subprize-cell" style="background-color: #f8f9fa;">
													-<br>
													<small class="percentage-cell">-</small>
												</td>
												
												<!-- Show proportional non-winning for this scenario -->
												<td class="subprize-cell">
													<?= number_format($scenario['non_winning_tickets']); ?><br>
													<small class="percentage-cell"><?= round(($scenario['non_winning_tickets'] / $breakdown_data['total_tickets']) * 100, 3); ?>%</small>
												</td>
											</tr>
											
											<!-- Row for main numbers + extra ball -->
											<tr>
												<td class="scenario-header" style="background-color: #e7f3ff; color: #212529; font-weight: bold;">
													<?= $scenario['picked_correctly']; ?> + Extra
												</td>
												<?php 
												// Empty cells for regular match columns (shown in previous row)
												for ($match_level = $pick_per_ticket; $match_level >= $minimum_prize_match; $match_level--): ?>
													<td class="subprize-cell" style="background-color: #f8f9fa;">
														-<br>
														<small class="percentage-cell">-</small>
													</td>
												<?php endfor; 
												
												// Display extra ball match columns
												for ($match_level = $pick_per_ticket; $match_level >= 1; $match_level--): 
													if (isset($scenario['extra_ball_subprizes'][$match_level])): 
														$tickets = $scenario['extra_ball_subprizes'][$match_level]['tickets'];
														$percentage = $scenario['extra_ball_subprizes'][$match_level]['percentage'];
													?>
														<td class="subprize-cell" style="background-color: #d4edda; color: #212529; font-weight: 500;">
															<?= number_format($tickets); ?><br>
															<small class="percentage-cell" style="color: #212529; font-weight: 500;"><?= $percentage; ?>%</small>
														</td>
													<?php else: ?>
														<td class="subprize-cell" style="background-color: #d4edda; color: #212529; font-weight: 500;">
															0<br>
															<small class="percentage-cell" style="color: #212529; font-weight: 500;">0%</small>
														</td>
													<?php endif; 
												endfor; 
												
												// Display "Extra only" column (only for 0 correct + extra)
												if ($scenario['picked_correctly'] == 0 && isset($scenario['extra_ball_subprizes'][0])): 
													$tickets = $scenario['extra_ball_subprizes'][0]['tickets'];
													$percentage = $scenario['extra_ball_subprizes'][0]['percentage'];
												?>
													<td class="subprize-cell" style="background-color: #fff3cd; color: #212529; font-weight: 500;">
														<?= number_format($tickets); ?><br>
														<small class="percentage-cell" style="color: #212529; font-weight: 500;"><?= $percentage; ?>%</small>
													</td>
												<?php else: ?>
													<td class="subprize-cell" style="background-color: #fff3cd; color: #212529; font-weight: 500;">
														<?= $scenario['picked_correctly'] == 0 ? '0' : '-'; ?><br>
														<small class="percentage-cell" style="color: #212529; font-weight: 500;"><?= $scenario['picked_correctly'] == 0 ? '0' : '-'; ?>%</small>
													</td>
												<?php endif; ?>
												
												<!-- Empty non-winning for extra row - show extra scenario non-winning -->
												<td class="subprize-cell">
													<?php if (isset($scenario['extra_non_winning'])): ?>
														<?= number_format($scenario['extra_non_winning']); ?><br>
														<small class="percentage-cell"><?= round(($scenario['extra_non_winning'] / $breakdown_data['total_tickets']) * 100, 3); ?>%</small>
													<?php else: ?>
														-<br>
														<small class="percentage-cell">-</small>
													<?php endif; ?>
												</td>
											</tr>
											
										<?php else: ?>
											<!-- Regular lottery - single row per scenario -->
											<tr>
												<td class="scenario-header">
													<?= $scenario['picked_correctly']; ?>
												</td>
												<?php 
												// Display regular match columns
												for ($match_level = $pick_per_ticket; $match_level >= $minimum_prize_match; $match_level--): 
													if (isset($scenario['subprizes'][$match_level])): 
														$tickets = $scenario['subprizes'][$match_level]['tickets'];
														$percentage = $scenario['subprizes'][$match_level]['percentage'];
													?>
														<td class="subprize-cell">
															<?= number_format($tickets); ?><br>
															<small class="percentage-cell"><?= $percentage; ?>%</small>
														</td>
													<?php else: ?>
														<td class="subprize-cell">
															0<br>
															<small class="percentage-cell">0%</small>
														</td>
													<?php endif; 
												endfor; ?>
												
												<!-- Non-winning column -->
												<td class="subprize-cell">
													<?= number_format($scenario['non_winning_tickets']); ?><br>
													<small class="percentage-cell"><?= round(($scenario['non_winning_tickets'] / $breakdown_data['total_tickets']) * 100, 3); ?>%</small>
												</td>
											</tr>
										<?php endif; ?>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
						
						<div class="mt-4">
							<h5>Mathematical Formula:</h5>
							<div class="alert alert-secondary">
								<p><strong>Formula:</strong> nCr = n! / (r! × (n-r)!)</p>
								<?php if ($is_independent_extra_ball): ?>
								<p><strong>Extra Ball:</strong> Base tickets ÷ <?= $extra_ball_range; ?></p>
								<?php endif; ?>
								<p><strong>Total:</strong> C(<?= $numbers_to_pick; ?>, <?= $pick_per_ticket; ?>) = <?= number_format($breakdown_data['total_tickets']); ?></p>
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
