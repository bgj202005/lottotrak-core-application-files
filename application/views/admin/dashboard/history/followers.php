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
	.followers {
		margin-top:3em;
		text-align: center;
	}
	.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    color: #000000;
	}
	ul li { 
		font-family: Arial, Sans-Serif;
		font-size: 0.95em;
		color: #000000;
	} 
.shadow-sm {
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
	}
	/* Previous Draw Followers styles */
	.winner-legend {
		background-color: #f8f9fa;
		border-left: 4px solid #28a745;
		padding: 10px 15px;
		margin-bottom: 15px;
		border-radius: 4px;
	}
	.prev-draw-match {
		background-color: #fff3cd;
		border: 2px solid #ffc107;
		padding: 2px 6px;
		border-radius: 3px;
		font-weight: bold;
	}
	.prev-ball-badge {
		cursor: pointer;
		margin: 2px;
		padding: 6px 10px;
		transition: all 0.2s;
	}
	.prev-ball-badge:hover {
		transform: scale(1.1);
		box-shadow: 0 2px 4px rgba(0,0,0,0.2);
	}
	.prev-ball-section {
		display: none;
		margin-bottom: 15px;
		padding: 10px;
		border: 1px solid #ddd;
		border-radius: 5px;
		background-color: #fff;
	}
	.prev-ball-section.active {
		display: block;
	}
</style>
	<h2><?php echo 'View Followers for: '.$lottery->lottery_name; ?></h2>
	<?php $max = $lottery->balls_drawn; 
	   $b = 1; 
	   ?>	
	<h5 style = "text-align:left"><?php echo anchor('admin/history', 'Back to History Win Dashboard', 'title="Back to Win History"'); ?></h5>
	<section>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="card mt-3 tab-card">
						<div id = "message"></div>
						<div class="card-header tab-card-header">
							<div class="d-flex flex-row-reverse">
								<div class="col-md-6">
									<div class="bg-white card followers mb-4 shadow-sm">
										<div class="p-4">
											<h4 class="mb-1">Draw Range: <?=$lottery->last_drawn['range'];?> Draws</h4>
										</div>
									</div>
										<div class="bg-white card followers mb-4 shadow-sm">
											<div class="p-4">
											<h4 class="mb-1">
											<?php $extra = array('for' => 'extra_lb', 'style' =>'margin-right:10px;');
												echo form_label('Next Draw Date:', 'extra_lb', $extra); 
												echo (($lottery->next_draw_date)||$lottery->next_draw_date!='nodraws') ? form_label($lottery->next_draw_date, 'extra_lb', $extra) : form_label(' Not Available', 'extra_lb', $extra);
												?></h4>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="bg-white card followers mb-4 shadow-sm">
											<div class="p-4">
												<h4 class="mb-1">
												<?php $extra = array('for' => 'extra_lb', 'style' =>'margin-right:10px;');
												echo form_label('Extra (Bonus) Ball Included?', 'extra_lb', $extra);
												echo (!empty($lottery->extra_included)) ? form_label(' YES', 'extra_lb', $extra) : form_label(' NO', 'extra_lb', $extra);
												?></h4>
											</div>
										</div>
									<div class="bg-white card followers mb-4 shadow-sm">
											<div class="p-4">
												<h4 class="mb-1">
												<?php $extra = array('for' => 'extra_lb', 'style' =>'margin-right:10px;');
												echo form_label('Extra Draws Included?', 'extra_lb', $extra);
												echo (!empty($lottery->extra_draws)) ? form_label(' YES', 'extra_lb', $extra) : form_label(' NO', 'extra_lb', $extra);
												?></h4>
											</div>
										</div>
									</div>
								</div>
							<ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
							<div style = "white-space: nowrap; margin-right:20px;"><?php echo date("l, F j, Y", strtotime(str_replace('/','-',$lottery->last_drawn['draw_date']))); ?></div>
								<?php do
								{ ?>
								<li class="nav-item">
									<a class="nav-link" id="tab-<?=$b;?>" data-toggle="tab" href="#ball<?=$b; ?>" role="tab" aria-controls="<?=$b;?>" aria-selected="true"><?=$lottery->last_drawn['ball'.$b]?></a>
								</li>
								<?php $b++;
								}
								while ($b<=$max); ?>
								<li>
									<?php if($lottery->extra_ball): ?>
										<li class="nav-item">
										<a class="nav-link" id="tab-<?=$b;?>" data-toggle="tab" href="#ball<?=$b; ?>" role="tab" aria-controls="<?=$b;?>" aria-selected="true">  +  <?=$lottery->last_drawn['extra']?></a>
									</li>
									<?php endif;?>
								</li>
							</ul>
						</div>
						<?php
						// Calculate best ball and best position by points
						$best_ball = 1;
						$best_ball_points = 0;
						$best_pos = 1;
						$best_pos_points = 0;
						$max_balls = $lottery->balls_drawn;
						if ($lottery->extra_included) $max_balls++;
						
						// Check if this is an enhanced display for independent extra ball lottery
						$is_enhanced = isset($lottery->enhanced_wins) && $lottery->enhanced_wins;
						
						if ($is_enhanced && isset($lottery->enhanced_point_rankings)) {
							// Use enhanced point rankings for independent extra ball lotteries
							$ball_rankings = $lottery->enhanced_point_rankings['balls'];
							$position_rankings = $lottery->enhanced_point_rankings['positions'];
							
			if (!empty($ball_rankings)) {
				// Collect all drawn ball numbers first
				$drawn_balls = array();
				for ($i = 1; $i <= $lottery->balls_drawn; $i++) {
					if (isset($lottery->last_drawn['ball'.$i])) {
						$drawn_balls[] = $lottery->last_drawn['ball'.$i];
					}
				}
				if ($lottery->extra_included && isset($lottery->last_drawn['extra'])) {
					$drawn_balls[] = $lottery->last_drawn['extra'];
				}
				
				// Find the highest points among ONLY the drawn balls
				$max_points_drawn = 0;
				$tied_balls = array();
				foreach ($ball_rankings as $ball_num => $points) {
					if (in_array($ball_num, $drawn_balls)) {
						if ($points > $max_points_drawn) {
							$max_points_drawn = $points;
							$tied_balls = array($ball_num);
						} elseif ($points == $max_points_drawn) {
							$tied_balls[] = $ball_num;
						}
					}
				}
				$best_ball_points = $max_points_drawn;
				
				// Store tie information for display
				$ball_tie_info = array(
					'balls' => $tied_balls,
					'points' => $best_ball_points,
					'has_tie' => count($tied_balls) > 1
				);
			}
							
							if (!empty($position_rankings)) {
								$best_pos = key($position_rankings);
								$best_pos_points = current($position_rankings);
								
								// Check for ties at the highest point value for positions
								$tied_positions = array();
								foreach ($position_rankings as $pos_num => $points) {
									if ($points == $best_pos_points) {
										$tied_positions[] = $pos_num;
									}
								}
								
								// Store position tie information for display
								$position_tie_info = array(
									'positions' => $tied_positions,
									'points' => $best_pos_points,
									'has_tie' => count($tied_positions) > 1
								);
							}
						} else {
							// Use regular point calculation for standard lotteries
							for ($i = 1; $i <= $max_balls; $i++) {
								// Ball points
								// Regular handling for all balls including duplicate extra balls
								$wins = ($i > $lottery->balls_drawn ? $lottery->last_drawn['extra_win'] : $lottery->last_drawn['ball'.$i.'_win']);
								
								$points_total = 0;
								foreach ($wins as $key => $value) {
									if (strpos($key, "_points") !== false) $points_total += intval($value);
								}
								if ($points_total > $best_ball_points) {
									$best_ball_points = $points_total;
									$best_ball = $i;
								}
								// Position points
								$positions = ($i > $lottery->balls_drawn ? $lottery->last_drawn['position_extra_win'] : $lottery->last_drawn['position'.$i.'_win']);
								$points_total_pos = 0;
								foreach ($positions as $key => $value) {
									if (strpos($key, "_points") !== false) $points_total_pos += intval($value);
								}
								if ($points_total_pos > $best_pos_points) {
									$best_pos_points = $points_total_pos;
									$best_pos = $i;
								}
							}
						}
						?>
						<div class="tab-content" id="myTabContent">
							<?php $b = 1;			   
							$cd = intval($max);						  // This is the maximum ball drawn without an extra ball
							if($lottery->extra_included):  $max++; endif; // Include the extra ball
							do
							{ ?> 
							<div class="tab-pane fade p-3 <?php if($b==1) echo 'show active'; ?>" id="ball<?=$b?>" role="tabpanel" aria-labelledby="tab-<?=$b;?>">
								<div class="row mb-2">
									<div class="col-md-6">
										<div class="alert alert-success text-center mb-2">
											<strong>Ball with Highest Points:</strong>
											<?php
											if ($is_enhanced && isset($ball_tie_info)) {
												// For enhanced displays with tie detection
												if ($ball_tie_info['has_tie']) {
													// Handle ties - show all tied balls
													$display_parts = array();
													foreach ($ball_tie_info['balls'] as $ball_num) {
														if ($lottery->extra_included && $ball_num == $lottery->last_drawn['extra']) {
															$display_parts[] = '<strong>Extra Ball +' . $ball_num . '</strong>';
														} else {
															$display_parts[] = '<strong>Ball ' . $ball_num . '</strong>';
														}
													}
													echo implode(' and ', $display_parts);
												} else {
													// Single highest ball from drawn balls
													$best_drawn_ball = $ball_tie_info['balls'][0];
													if ($lottery->extra_included && $best_drawn_ball == $lottery->last_drawn['extra']) {
														echo '<strong>Extra Ball +' . $best_drawn_ball . '</strong>';
													} else {
														echo '<strong>Ball ' . $best_drawn_ball . '</strong>';
													}
												}
											} else {
												// For regular displays, $best_ball is the position index
												if ($best_ball > $lottery->balls_drawn) {
													echo '<strong>Extra Ball +' . $lottery->last_drawn['extra'] . '</strong>';
												} else {
													// Ensure we show the actual ball number, not the position
													$actual_ball_number = isset($lottery->last_drawn['ball'.$best_ball]) ? $lottery->last_drawn['ball'.$best_ball] : $best_ball;
													echo '<strong>Ball ' . $actual_ball_number . '</strong>';
												}
											}
											?>
											(<?= isset($ball_tie_info) ? $ball_tie_info['points'] : $best_ball_points; ?> points)
										</div>
									</div>
									<div class="col-md-6">
										<div class="alert alert-info text-center mb-2">
											<strong>Position with Highest Points:</strong>
											<?php
											if ($is_enhanced && isset($position_tie_info)) {
												// For enhanced displays with tie detection
												if ($position_tie_info['has_tie']) {
													// Handle ties - show all tied positions
													$display_parts = array();
													foreach ($position_tie_info['positions'] as $pos_num) {
														if ($pos_num > $lottery->balls_drawn) {
															$display_parts[] = '<strong>Extra Ball Position</strong>';
														} else {
															$display_parts[] = '<strong>Position ' . $pos_num . '</strong>';
														}
													}
													echo implode(' and ', $display_parts);
												} else {
													// Single highest position
													echo ($best_pos > $lottery->balls_drawn ? '<strong>Extra Ball Position</strong>' : '<strong>Position '.$best_pos.'</strong>');
												}
											} else {
												// For regular displays
												echo ($best_pos > $lottery->balls_drawn ? '<strong>Extra Ball Position</strong>' : '<strong>Position '.$best_pos.'</strong>');
											}
											?>
											(<?= $best_pos_points; ?> points)
										</div>
									</div>
								</div>
								<div class="card-deck mb-3 text-center">
									<div class="card mb-4 shadow-sm">
									<div class="card-header">
										<h6 class="my-0 font-weight-normal card-title"><strong>Win Record After Ball <?=($b>$cd ? $lottery->last_drawn['extra'] : $lottery->last_drawn['ball'.$b]);?> has been drawn in <?=$lottery->last_drawn['range']; ?> draws</strong></h6>
									</div>
									<div class="card-body">
										<?php 
										// Prepare data based on enhanced vs regular display
										if ($is_enhanced && isset($lottery->enhanced_parsed_wins)) {
											// Enhanced display for independent extra ball lotteries
											$ball_number = ($b > $cd ? $lottery->last_drawn['extra'] : $lottery->last_drawn['ball'.$b]);
											
											// Regular enhanced display for all balls including duplicate extra balls
											$wins = isset($lottery->enhanced_parsed_wins[$ball_number]) ? $lottery->enhanced_parsed_wins[$ball_number] : array();
											$total_winners = array_sum($wins);
											
											// Calculate points for enhanced display
											$points_total = 0;
											$category_mapping = array(
												'extra' => 1,
												'1_win' => 2,
												'1_win_extra' => 3,
												'2_win' => 4,
												'2_win_extra' => 5,
												'3_win' => 6,
												'3_win_extra' => 7,
												'4_win' => 8,
												'4_win_extra' => 9,
												'5_win' => 10,
												'5_win_extra' => 11,
												'6_win' => 12,
												'6_win_extra' => 13,
												'7_win' => 14,
												'7_win_extra' => 15,
												'8_win' => 16,
												'8_win_extra' => 17,
												'9_win' => 18,
												'9_win_extra' => 19
											);
											foreach ($wins as $category => $count) {
												// For duplicate_extra_ball lotteries, only calculate points for valid categories
												if ($lottery->duplicate_extra_ball && isset($lottery->valid_prize_categories)) {
													if (!in_array($category, $lottery->valid_prize_categories)) {
														continue; // Skip categories not in the prize profile
													}
												}
												
												if (isset($category_mapping[$category])) {
													$points_total += intval($count) * $category_mapping[$category];
												}
											}
											
											// Calculate total winners for percentage calculation (enhanced display)
											$total_winners = 0;
											foreach ($wins as $category => $count) {
												if ($lottery->duplicate_extra_ball && isset($lottery->valid_prize_categories)) {
													if (!in_array($category, $lottery->valid_prize_categories)) {
														continue; // Skip categories not in the prize profile
													}
												}
												$total_winners += intval($count);
											}
										} else {
											// Regular display
											$wins = ($b > $cd ? $lottery->last_drawn['extra_win'] : $lottery->last_drawn['ball'.$b.'_win']);
											$total_winners = 0;
											$points_total = 0;
											foreach ($wins as $key => $value) {
												if (strpos($key, "_points") === false) $total_winners += intval($value);
											}
										}
										$percentage_total = 0; // Initialize percentage total for both enhanced and regular display
										?>
										<table class="table table-bordered table-sm mb-3 w-100 mx-auto">
											<thead class="thead-light">
												<tr>
													<th>Category</th>
													<th>Winners</th>
													<th>Points</th>
													<th>(%) Percentage</th>
												</tr>
											</thead>
											<tbody>
												<?php
												if ($is_enhanced && isset($lottery->enhanced_parsed_wins)) {
													// Enhanced display for independent extra ball lotteries using associative array
													$category_mapping = array(
														'extra' => array('label' => 'Extra Only', 'points' => 1),
														'1_win' => array('label' => '1 Wins', 'points' => 2),
														'1_win_extra' => array('label' => '1 Win + Extra', 'points' => 3),
														'2_win' => array('label' => '2 Wins', 'points' => 4),
														'2_win_extra' => array('label' => '2 Wins + Extra', 'points' => 5),
														'3_win' => array('label' => '3 Wins', 'points' => 6),
														'3_win_extra' => array('label' => '3 Wins + Extra', 'points' => 7),
														'4_win' => array('label' => '4 Wins', 'points' => 8),
														'4_win_extra' => array('label' => '4 Wins + Extra', 'points' => 9),
														'5_win' => array('label' => '5 Wins', 'points' => 10),
														'5_win_extra' => array('label' => '5 Wins + Extra', 'points' => 11),
														'6_win' => array('label' => '6 Wins', 'points' => 12),
														'6_win_extra' => array('label' => '6 Wins + Extra', 'points' => 13),
														'7_win' => array('label' => '7 Wins', 'points' => 14),
														'7_win_extra' => array('label' => '7 Wins + Extra', 'points' => 15),
														'8_win' => array('label' => '8 Wins', 'points' => 16),
														'8_win_extra' => array('label' => '8 Wins + Extra', 'points' => 17),
														'9_win' => array('label' => '9 Wins', 'points' => 18),
														'9_win_extra' => array('label' => '9 Wins + Extra', 'points' => 19)
													);
													
													foreach ($category_mapping as $category_key => $category_info) {
														// Skip extra categories when extra_included = 0
														if (!$lottery->extra_included && (strpos($category_key, '_extra') !== false || $category_key === 'extra')) {
															continue;
														}
														
														// For duplicate_extra_ball lotteries, only show categories that exist in lottery_prize_profiles
														if ($lottery->duplicate_extra_ball && isset($lottery->valid_prize_categories)) {
															if (!in_array($category_key, $lottery->valid_prize_categories)) {
																continue; // Skip categories not in the prize profile
															}
														}
														
														$winners = isset($wins[$category_key]) ? intval($wins[$category_key]) : 0;
														$points = $winners * $category_info['points'];
														$range = $lottery->last_drawn['range'] ?? 100; // Use range for percentage calculation
														$percentage = $range > 0 ? round(($winners / $range) * 100, 2) : 0;
														$percentage_total += $percentage; // Add to percentage total
														?>
														<tr>
															<td><?= $category_info['label'] ?></td>
															<td><?= $winners ?></td>
															<td><?= $points ?></td>
															<td><?= $percentage ?>%</td>
														</tr>
													<?php }
												} else {
													// Regular display
													foreach ($wins as $prize => $winners) {
														if (strpos($prize, "_points") !== false) continue; // Only process main categories
														
														// Skip extra categories when extra_included = 0
														if (!$lottery->extra_included && (strpos($prize, '_extra') !== false || $prize === 'extra')) {
															continue;
														}
														
														$points = isset($wins[$prize . '_points']) ? $wins[$prize . '_points'] : 0;
														$points_total += $points;
														$range = $lottery->last_drawn['range'] ?? 100; // Use range for percentage calculation
														$percentage = $range > 0 ? round(($winners / $range) * 100, 2) : 0;
														$percentage_total += $percentage; // Add to percentage total
														switch ($prize) {
															case "9_win": $label = "9 out of $cd Winners"; break;
															case "8_win_extra": $label = "8 out of $cd Winners + Extra"; break;
															case "8_win": $label = "8 out of $cd Winners"; break;
															case "7_win_extra": $label = "7 out of $cd Winners + Extra"; break;
															case "7_win": $label = "7 out of $cd Winners"; break;
															case "6_win_extra": $label = "6 out of $cd Winners + Extra"; break;
															case "6_win": $label = "6 out of $cd Winners"; break;
															case "5_win_extra": $label = "5 out of $cd Winners + Extra"; break;
															case "5_win": $label = "5 out of $cd Winners"; break;
															case "4_win_extra": $label = "4 out of $cd Winners + Extra"; break;
															case "4_win": $label = "4 out of $cd Winners"; break;
															case "3_win_extra": $label = "3 out of $cd Winners + Extra"; break;
															case "3_win": $label = "3 out of $cd Winners"; break;
															case "2_win_extra": $label = "2 out of $cd Winners + Extra"; break;
															case "2_win": $label = "2 out of $cd Winners"; break;
															case "1_win_extra": $label = "1 out of $cd Winners + Extra"; break;
															case "1_win": $label = "1 out of $cd Winners"; break;
														case "extra": $label = "Extra / Bonus Ball"; break;
														default: $label = $prize;
													}
													?>
													<tr>
														<td><?= $label ?></td>
														<td><?= $winners ?></td>
														<td><?= $points ?></td>
														<td><?= $percentage ?>%</td>
													</tr>
												<?php }
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<th colspan="2" class="text-right">Total Points:</th>
													<th><?= $points_total ?></th>
													<th><?= round($percentage_total, 2) ?>%</th>
												</tr>
											</tfoot>
										</table>
									</div>
									</div>
									<div class="card mb-4 shadow-sm">
									<div class="card-header">
									<h6 class="my-0 font-weight-normal"><strong>Win Record for Position <?=$b;?> (Ball <?=($b>$cd ? $lottery->last_drawn['extra'] : $lottery->last_drawn['ball'.$b]);?>) in <?=$lottery->last_drawn['range']; ?> draws</strong></h5>
									</div>
									<div class="card-body">
										<?php
										// Prepare position data based on enhanced vs regular display
										if ($is_enhanced && isset($lottery->enhanced_parsed_positions)) {
											// Enhanced display for independent extra ball lotteries - positions
											$position_key = 'position_' . $b;
											$positions = isset($lottery->enhanced_parsed_positions[$position_key]) ? $lottery->enhanced_parsed_positions[$position_key] : array();
											
											// Handle special total_points field for aggregated data
											if (isset($positions['total_points'])) {
												$points_total_pos = $positions['total_points'];
												// Calculate total winners excluding the total_points field
												$total_winners_pos = 0;
												foreach ($positions as $key => $value) {
													if ($key !== 'total_points') {
														$total_winners_pos += intval($value);
													}
												}
											} else {
												// Normal calculation for positions without total_points
												$total_winners_pos = array_sum($positions);
												
												// Calculate points for enhanced position display
												$points_total_pos = 0;
												$category_mapping = array(
													'extra' => 1,
													'1_win' => 2,
													'1_win_extra' => 3,
													'2_win' => 4,
													'2_win_extra' => 5,
													'3_win' => 6,
													'3_win_extra' => 7,
													'4_win' => 8,
													'4_win_extra' => 9,
													'5_win' => 10,
													'5_win_extra' => 11,
													'6_win' => 12,
													'6_win_extra' => 13,
													'7_win' => 14,
													'7_win_extra' => 15,
													'8_win' => 16,
													'8_win_extra' => 17,
													'9_win' => 18,
													'9_win_extra' => 19
												);
												foreach ($positions as $category => $count) {
													// Skip total_points field in calculation
													if ($category === 'total_points') continue;
													
													// For duplicate_extra_ball lotteries, only calculate points for valid categories
													if ($lottery->duplicate_extra_ball && isset($lottery->valid_prize_categories)) {
														if (!in_array($category, $lottery->valid_prize_categories)) {
															continue; // Skip categories not in the prize profile
														}
													}
													
													if (isset($category_mapping[$category])) {
														$points_total_pos += intval($count) * $category_mapping[$category];
													}
												}
											}
										} else {
											// Regular display
											$positions = ($b > $cd ? $lottery->last_drawn['position_extra_win'] : $lottery->last_drawn['position'.$b.'_win']);
											$total_winners_pos = 0;
											$points_total_pos = 0;
											foreach ($positions as $key => $value) {
												if (strpos($key, "_points") === false) $total_winners_pos += intval($value);
											}
										}
										$percentage_total_pos = 0; // Initialize percentage total for both enhanced and regular display
										?>
										<table class="table table-bordered table-sm mb-3 w-100 mx-auto">
											<thead class="thead-light">
												<tr>
													<th>Category</th>
													<th>Winners</th>
													<th>Points</th>
													<th>(%) Percentage</th>
												</tr>
											</thead>
											<tbody>
												<?php
												if ($is_enhanced && isset($lottery->enhanced_parsed_positions)) {
													// Enhanced display for independent extra ball lotteries - positions using associative array
													$category_mapping = array(
														'extra' => array('label' => 'Extra Only', 'points' => 1),
														'1_win' => array('label' => '1 Wins', 'points' => 2),
														'1_win_extra' => array('label' => '1 Win + Extra', 'points' => 3),
														'2_win' => array('label' => '2 Wins', 'points' => 4),
														'2_win_extra' => array('label' => '2 Wins + Extra', 'points' => 5),
														'3_win' => array('label' => '3 Wins', 'points' => 6),
														'3_win_extra' => array('label' => '3 Wins + Extra', 'points' => 7),
														'4_win' => array('label' => '4 Wins', 'points' => 8),
														'4_win_extra' => array('label' => '4 Wins + Extra', 'points' => 9),
														'5_win' => array('label' => '5 Wins', 'points' => 10),
														'5_win_extra' => array('label' => '5 Wins + Extra', 'points' => 11),
														'6_win' => array('label' => '6 Wins', 'points' => 12),
														'6_win_extra' => array('label' => '6 Wins + Extra', 'points' => 13),
														'7_win' => array('label' => '7 Wins', 'points' => 14),
														'7_win_extra' => array('label' => '7 Wins + Extra', 'points' => 15),
														'8_win' => array('label' => '8 Wins', 'points' => 16),
														'8_win_extra' => array('label' => '8 Wins + Extra', 'points' => 17),
														'9_win' => array('label' => '9 Wins', 'points' => 18),
														'9_win_extra' => array('label' => '9 Wins + Extra', 'points' => 19)
													);
													
													foreach ($category_mapping as $category_key => $category_info) {
														// Skip extra categories when extra_included = 0
														if (!$lottery->extra_included && (strpos($category_key, '_extra') !== false || $category_key === 'extra')) {
															continue;
														}
														
														// For duplicate_extra_ball lotteries, only show categories that exist in lottery_prize_profiles
														if ($lottery->duplicate_extra_ball && isset($lottery->valid_prize_categories)) {
															if (!in_array($category_key, $lottery->valid_prize_categories)) {
																continue; // Skip categories not in the prize profile
															}
														}
														
														$winners = isset($positions[$category_key]) ? intval($positions[$category_key]) : 0;
														$points = $winners * $category_info['points'];
														$range = $lottery->last_drawn['range'] ?? 100; // Use range for percentage calculation
														$percentage = $range > 0 ? round(($winners / $range) * 100, 2) : 0;
														$percentage_total_pos += $percentage; // Add to percentage total
														?>
														<tr>
															<td><?= $category_info['label'] ?></td>
															<td><?= $winners ?></td>
															<td><?= $points ?></td>
															<td><?= $percentage ?>%</td>
														</tr>
													<?php }
												} else {
													// Regular display
													foreach ($positions as $prize => $winners) {
														if (strpos($prize, "_points") !== false) continue;
														
														// Skip extra categories when extra_included = 0
														if (!$lottery->extra_included && (strpos($prize, '_extra') !== false || $prize === 'extra')) {
															continue;
														}
														
														$points = isset($positions[$prize . '_points']) ? $positions[$prize . '_points'] : 0;
														$points_total_pos += $points;
														$range = $lottery->last_drawn['range'] ?? 100; // Use range for percentage calculation
														$percentage = $range > 0 ? round(($winners / $range) * 100, 2) : 0;
														$percentage_total_pos += $percentage; // Add to percentage total
														switch ($prize) {
															case "9_win": $label = "9 out of $cd Winners"; break;
															case "8_win_extra": $label = "8 out of $cd Winners + Extra"; break;
															case "8_win": $label = "8 out of $cd Winners"; break;
															case "7_win_extra": $label = "7 out of $cd Winners + Extra"; break;
															case "7_win": $label = "7 out of $cd Winners"; break;
															case "6_win_extra": $label = "6 out of $cd Winners + Extra"; break;
															case "6_win": $label = "6 out of $cd Winners"; break;
															case "5_win_extra": $label = "5 out of $cd Winners + Extra"; break;
															case "5_win": $label = "5 out of $cd Winners"; break;
															case "4_win_extra": $label = "4 out of $cd Winners + Extra"; break;
															case "4_win": $label = "4 out of $cd Winners"; break;
															case "3_win_extra": $label = "3 out of $cd Winners + Extra"; break;
															case "3_win": $label = "3 out of $cd Winners"; break;
														case "2_win_extra": $label = "2 out of $cd Winners + Extra"; break;
														case "2_win": $label = "2 out of $cd Winners"; break;
														case "1_win_extra": $label = "1 out of $cd Winners + Extra"; break;
														case "1_win": $label = "1 out of $cd Winners"; break;
														case "extra": $label = "Extra / Bonus Ball"; break;
														default: $label = $prize;
													}
												?>
												<tr>
													<td><?= $label ?></td>
													<td><?= $winners ?></td>
													<td><?= $points ?></td>
													<td><?= $percentage ?>%</td>
												</tr>
												<?php }
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<th colspan="2" class="text-right">Total Points:</th>
													<th><?= $points_total_pos ?></th>
													<th><?= round($percentage_total_pos, 2) ?>%</th>
												</tr>
											</tfoot>
										</table>
									</div>
									</div>
								</div>
							</div>
							<?php $b++;
							}
							while ($b<=$max);?>
						</div>
						
						<?php 
						// ============================================================
						// PREVIOUS DRAW FOLLOWERS SUMMARY - Show ALL balls with their followers
						// ============================================================
						if(isset($prev_followers_data) && $prev_followers_data && isset($prev_draw) && $prev_draw['exists']): ?>
						<div style="margin: 30px 20px 20px 20px; padding: 20px; background-color: #f8f9fa; border: 2px solid #007bff; border-radius: 5px;">
							<h4 class="text-primary mb-3">
								<strong>📊 Previous Draw Followers</strong> - Predictions before <?=date("F j, Y", strtotime($prev_draw['date']));?>
							</h4>
							<div class="card-text mt-2 mb-3 p-2" style="background-color: #e7f3ff; border-left: 3px solid #007bff;">
								<strong>Click a number to view its followers:</strong><br>
								<?php 
								foreach($prev_draw['numbers'] as $key => $num):
									if($key === 'extra') continue;
									echo '<span class="prev-ball-badge badge badge-primary" data-ball="'.$num.'" data-is-extra="0">'.$num.'</span> ';
								endforeach;
								if(isset($prev_draw['numbers']['extra']) && $prev_draw['numbers']['extra'] > 0):
									echo '+ <span class="prev-ball-badge badge badge-success" data-ball="'.$prev_draw['numbers']['extra'].'" data-is-extra="1">'.$prev_draw['numbers']['extra'].'</span>';
								endif;
								?>
								<br><small class="text-muted mt-1">Click "Show All" to see all balls at once</small>
								<button class="btn btn-sm btn-info ml-2" id="show-all-prev-balls">Show All</button>
								<button class="btn btn-sm btn-secondary ml-1" id="hide-all-prev-balls">Hide All</button>
							</div>
							
							<?php 
							// Parse previous followers data - format is: ball_num>follower=count|follower=count,...
							$prev_balls_array = explode(",", $prev_followers_data);
							
							// Parse previous non-followers data if available
							$prev_nonfollowers_array = array();
							if($prev_nonfollowers_data):
								$prev_nonfollowers_array = explode(",", $prev_nonfollowers_data);
							endif;
							
							// Display each ball's followers in collapsible sections
							foreach($prev_balls_array as $ball_data):
								if(strpos($ball_data, '>') === FALSE) continue;
								list($ball_num, $followers_str) = explode(">", $ball_data, 2);
								$ball_num = trim($ball_num);
								$followers_str = trim($followers_str);
								
								// Determine if this is an extra ball
								$is_extra = (isset($prev_draw['balls']['extra']) && $prev_draw['balls']['extra'] == $ball_num);
								?>
								
								<div class="prev-ball-section" data-ball="<?=$ball_num;?>" data-is-extra="<?=($is_extra ? '1' : '0');?>">
									<h5><?=($is_extra ? 'Extra Ball' : 'Ball');?> <?=$ball_num;?> - Previous Followers</h5>
									<?php 
									// Parse followers
								$prev_picks = array();        // Main ball followers
								$prev_picks_extra = array();  // Extra ball followers (duplicate_extra_ball only)
								$has_dual_followers = false;
								
								// Check for independent extra ball format (main#extra)
								if($lottery->duplicate_extra_ball && strpos($followers_str, '#') !== FALSE):
									$has_dual_followers = true;
									$fol_parts = explode('#', $followers_str, 2);
									$fol_main_data = $fol_parts[0];
									$fol_extra_data = isset($fol_parts[1]) ? $fol_parts[1] : '';
									// Parse main ball followers
									foreach(explode('|', $fol_main_data) as $t):
										$picks = explode('=', $t);
										if(count($picks) == 2) $prev_picks[$picks[0]] = $picks[1];
									endforeach;
									// Parse extra ball followers
									foreach(explode('|', $fol_extra_data) as $t):
										$picks = explode('=', $t);
										if(count($picks) == 2) $prev_picks_extra[$picks[0]] = $picks[1];
									endforeach;
								else:
									// Standard format
									foreach(explode('|', $followers_str) as $t):
										$picks = explode('=', $t);
										if(count($picks) == 2) $prev_picks[$picks[0]] = $picks[1];
									endforeach;
								endif;
								
								arsort($prev_picks);
								arsort($prev_picks_extra);
								
								// Show section heading for duplicate_extra_ball lotteries
								if($has_dual_followers):
								echo '<h5 class="text-primary bg-light p-2 border rounded"><strong>Main Balls</strong> (Range: '.$lottery->minimum_ball.' - '.$lottery->maximum_ball.')</h5>';
								endif;
								
								// Display ALL followers grouped by count (matching current followers format)
								if(count($prev_picks) > 0):
									$s_picks = "";
									$sum = 0;
$winners_count = 0;       // followers drawn as main balls
							$winners_count_as_extra = 0; // followers drawn as the extra ball (standard $is_extra sections)
								$counts = current($prev_picks);
								
								do {
									if($counts == current($prev_picks)):
										$num = key($prev_picks);
										
										// Check if this follower was actually drawn in the CURRENT/MOST RECENT draw
										$was_drawn = false;
										if($has_dual_followers):
											// In dual format, $prev_picks always contains main ball followers
											if(in_array($num, $current_draw_numbers)):
												// Duplicate_extra_ball exception: skip if it's the extra ball number
												if(isset($current_draw_numbers['extra']) && $num == $current_draw_numbers['extra']):
													$was_drawn = false;
												else:
													$was_drawn = true;
												endif;
											endif;
elseif($is_extra):
												// Extra ball's followers are main ball numbers - highlight if drawn as main ball or extra ball
												if(in_array($num, $current_draw_numbers)):
													$was_drawn = true;
												elseif(isset($current_draw_numbers['extra']) && $num == $current_draw_numbers['extra']):
													$was_drawn = true;
												endif;
										elseif(!$is_extra && in_array($num, $current_draw_numbers)):
											// EXCEPTION for duplicate_extra_ball lotteries:
											// Do NOT highlight if this number is the extra ball (appears in both main and extra)
											if($lottery->duplicate_extra_ball && isset($current_draw_numbers['extra']) && $num == $current_draw_numbers['extra']):
												$was_drawn = false;
											else:
												$was_drawn = true;
											endif;
										endif;
										
										// Increment counter - track extra-ball hits separately from main-ball hits
										if($was_drawn):
											if(!$has_dual_followers && isset($current_draw_numbers['extra']) && $num == $current_draw_numbers['extra']):
												$winners_count_as_extra++;
											else:
												$winners_count++;
											endif;
										endif;
										
										$match_class = $was_drawn ? ' class="prev-draw-match"' : '';
										$s_picks .= 'Number <strong'.$match_class.'>'.$num.'</strong>';
										$current = next($prev_picks);
										$sum++;
										
										if($counts != $current):
											$s_picks .= ' has been drawn <strong>'.$counts.'</strong> Times.</p>';
											echo "<p class='card-text'> ".$s_picks."</p>";
											$counts = $current;
											$s_picks = "";
										else:
											$s_picks .= ' AND ';
										endif;
									else:
										$counts = next($prev_picks);
									endif;
								} while(!is_null(key($prev_picks)));
								
								echo '<small class="text-muted">Total: '.$sum.' followers</small>';
							else:
								echo '<em>No followers found</em>';
								$winners_count = 0;
							endif;
							
							// Display extra ball followers section for duplicate_extra_ball lotteries
							$winners_count_extra = 0;
							if($has_dual_followers):
								echo '<hr style="margin: 10px 0;">';
								echo '<h5 class="text-success bg-light p-2 border rounded"><strong>Extra Balls</strong> (Range: '.$lottery->minimum_extra_ball.' - '.$lottery->maximum_extra_ball.')</h5>';
								if(count($prev_picks_extra) > 0):
									$s_picks_e = "";
									$sum_e = 0;
									$counts_e = current($prev_picks_extra);
									do {
										if($counts_e == current($prev_picks_extra)):
											$num_e = key($prev_picks_extra);
											$xwas_drawn = (isset($current_draw_numbers['extra']) && $num_e == $current_draw_numbers['extra']);
											if($xwas_drawn) $winners_count_extra++;
											$xmatch_class = $xwas_drawn ? ' class="prev-draw-match"' : '';
											$s_picks_e .= 'Number <strong'.$xmatch_class.'>'.$num_e.'</strong>';
											$xcurrent = next($prev_picks_extra);
											$sum_e++;
											if($counts_e != $xcurrent):
												$s_picks_e .= ' has been drawn <strong>'.$counts_e.'</strong> Times.</p>';
												echo "<p class='card-text'> ".$s_picks_e."</p>";
												$counts_e = $xcurrent;
												$s_picks_e = "";
											else:
												$s_picks_e .= ' AND ';
											endif;
										else:
											$counts_e = next($prev_picks_extra);
										endif;
									} while(!is_null(key($prev_picks_extra)));
									echo '<p class="card-text">The total number of extra ball followers for this '.($is_extra ? 'extra ball' : 'ball').' is <strong>'.$sum_e.'</strong>.</p>';
								else:
									echo '<p class="card-text">There are no extra ball followers for this '.($is_extra ? 'extra ball' : 'ball').' in the range of '.$lottery->last_drawn['range'].' draws.</p>';
								endif;
							endif; // end if($has_dual_followers)
							?>
							
							<?php 
							// Now display non-followers for this ball
								if($prev_nonfollowers_data):
										// Find non-followers for this specific ball
										$nonfollowers_list = array();
										$nonfollowers_list_extra = array();
										foreach($prev_nonfollowers_array as $nf_data):
											if(strpos($nf_data, '>') === FALSE) continue;
											list($nf_ball_num, $nonfollowers_str) = explode(">", $nf_data, 2);
											$nf_ball_num = trim($nf_ball_num);
											
											// Check if this is the matching ball
											if($nf_ball_num == $ball_num):
												$nonfollowers_str = trim($nonfollowers_str);
												
												// Check for independent extra ball format (main#extra)
												if($lottery->duplicate_extra_ball && strpos($nonfollowers_str, '#') !== FALSE):
													// Parse both main and extra non-followers
													$nf_parts = explode('#', $nonfollowers_str, 2);
													$nonfollowers_list = explode('|', $nf_parts[0]);
													$nonfollowers_list_extra = isset($nf_parts[1]) ? explode('|', $nf_parts[1]) : array();
													$nonfollowers_list_extra = array_filter(array_map('trim', $nonfollowers_list_extra));
												else:
													// Standard format
													$nonfollowers_list = explode('|', $nonfollowers_str);
													$nonfollowers_list_extra = array();
												endif;
												
												// Clean up the main list
												$nonfollowers_list = array_filter(array_map('trim', $nonfollowers_list));
												break;
											endif;
										endforeach;
										
										if(count($nonfollowers_list) > 0):
											?>
											<hr style="margin: 15px 0;">
											<p class='card-text'>
												<?php 
													$non_picks = "These ".($lottery->duplicate_extra_ball ? "Main Ball " : "")."Numbers have <strong>NEVER</strong> followed this <?=($is_extra ? 'Extra Ball' : 'Ball');?> <strong><?=$ball_num;?></strong> for ".$lottery->last_drawn['range']." Draws:<br />";
$nonfollowers_winners_count = 0;       // non-followers drawn as main balls
													$nonfollowers_winners_count_as_extra = 0; // non-followers drawn as the extra ball
												
												foreach($nonfollowers_list as $nf_num):
													if(empty($nf_num)) continue;
													
													// Check if this non-follower was actually drawn in the CURRENT/MOST RECENT draw
													$was_drawn = false;
													if($has_dual_followers):
														// In dual format, $nonfollowers_list always contains main ball numbers
														if(in_array($nf_num, $current_draw_numbers)):
															if(isset($current_draw_numbers['extra']) && $nf_num == $current_draw_numbers['extra']):
																$was_drawn = false;
															else:
																$was_drawn = true;
															endif;
														endif;
elseif($is_extra):
																// Extra ball's non-followers are main ball numbers - highlight if drawn as main ball or extra ball
																if(in_array($nf_num, $current_draw_numbers)):
																	$was_drawn = true;
																elseif(isset($current_draw_numbers['extra']) && $nf_num == $current_draw_numbers['extra']):
																	$was_drawn = true;
																endif;
													elseif(!$is_extra && in_array($nf_num, $current_draw_numbers)):
														// EXCEPTION for duplicate_extra_ball lotteries:
														// Do NOT highlight if this number is the extra ball (appears in both main and extra)
														if($lottery->duplicate_extra_ball && isset($current_draw_numbers['extra']) && $nf_num == $current_draw_numbers['extra']):
															$was_drawn = false;
														else:
															$was_drawn = true;
														endif;
													endif;
													
// Increment counter - track extra-ball hits separately from main-ball hits
															if($was_drawn):
																if(!$has_dual_followers && isset($current_draw_numbers['extra']) && $nf_num == $current_draw_numbers['extra']):
																$nonfollowers_winners_count_as_extra++;
															else:
																$nonfollowers_winners_count++;
															endif;
														endif;
													
													$match_class = $was_drawn ? ' class="prev-draw-match"' : '';
													$non_picks .= 'Number: <strong'.$match_class.'>'.$nf_num.'</strong><br />';
												endforeach;
												
												echo $non_picks;
												?>
											</p>
											<?php 
											$plural_nf = (string) (count($nonfollowers_list) > 1 ?  " balls " : " ball "); 
											?>
											<p class='card-text'><strong><?php echo count($nonfollowers_list).$plural_nf; ?></strong> in this <?=($lottery->duplicate_extra_ball ? 'main ball ' : '');?>non-follower group.</p>
											<?php
											// Display extra ball non-followers for duplicate_extra_ball lotteries
											$nonfollowers_winners_count_extra = 0;
											if($has_dual_followers && count($nonfollowers_list_extra) > 0):
												echo '<hr style="margin: 10px 0;">';
												echo '<h5 class="text-success bg-light p-2 border rounded"><strong>Extra Balls</strong> (Range: '.$lottery->minimum_extra_ball.' - '.$lottery->maximum_extra_ball.')</h5>';
												$extra_non_picks = "These Extra Ball Numbers have <strong>NEVER</strong> followed this ".($is_extra ? 'Extra Ball' : 'Ball')." <strong>".$ball_num."</strong> for ".$lottery->last_drawn['range']." Draws:<br />";
												foreach($nonfollowers_list_extra as $xnf_num):
													if(empty($xnf_num)) continue;
													$xnf_drawn = (isset($current_draw_numbers['extra']) && $xnf_num == $current_draw_numbers['extra']);
													if($xnf_drawn) $nonfollowers_winners_count_extra++;
													$xnf_class = $xnf_drawn ? ' class="prev-draw-match"' : '';
													$extra_non_picks .= 'Number: <strong'.$xnf_class.'>'.$xnf_num.'</strong><br />';
												endforeach;
												echo '<p class="card-text">'.$extra_non_picks.'</p>';
												$xnf_plural = (count($nonfollowers_list_extra) > 1) ? ' balls' : ' ball';
												echo '<p class="card-text"><strong>'.count($nonfollowers_list_extra).$xnf_plural.'</strong> in the extra ball non-follower group.</p>';
											endif;
										else:
											$nonfollowers_winners_count = 0;
									$nonfollowers_winners_count_as_extra = 0;
									$nonfollowers_winners_count_extra = 0;
								endif;
							else:
								$nonfollowers_winners_count = 0;
								$nonfollowers_winners_count_as_extra = 0;
								$nonfollowers_winners_count_extra = 0;
							endif;
									
									// Display total winners from all sections
										$total_winners = $winners_count + $winners_count_as_extra + $nonfollowers_winners_count + $nonfollowers_winners_count_as_extra + $winners_count_extra + $nonfollowers_winners_count_extra;
										if($total_winners > 0):
											$plural_winners = ($total_winners > 1) ? "winners" : "winner";
											// Build breakdown string
											// Only use "main" qualifier when there are also extra-ball hits to distinguish from
											$has_extra_hits = ($winners_count_as_extra > 0 || $nonfollowers_winners_count_as_extra > 0 || $winners_count_extra > 0 || $nonfollowers_winners_count_extra > 0);
											$breakdown_parts = array();
											if($winners_count > 0) $breakdown_parts[] = $winners_count.($has_extra_hits ? ' from main followers' : ' from followers');
											if($winners_count_as_extra > 0) $breakdown_parts[] = $winners_count_as_extra.' extra '.($winners_count_as_extra == 1 ? 'follower' : 'followers');
											if($winners_count_extra > 0) $breakdown_parts[] = $winners_count_extra.' from extra ball followers';
											if($nonfollowers_winners_count > 0) $breakdown_parts[] = $nonfollowers_winners_count.($has_extra_hits ? ' from main non-followers' : ' from non-followers');
											if($nonfollowers_winners_count_as_extra > 0) $breakdown_parts[] = $nonfollowers_winners_count_as_extra.' extra non-'.($nonfollowers_winners_count_as_extra == 1 ? 'follower' : 'followers');
											if($nonfollowers_winners_count_extra > 0) $breakdown_parts[] = $nonfollowers_winners_count_extra.' from extra ball non-followers';
										?>
										<div style="margin-top: 15px; padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px;">
											<p class='card-text mb-0'><strong>Total Winners:</strong> <?=$total_winners;?> <?=$plural_winners;?> found
											<?php if(count($breakdown_parts) > 0): ?>
												(<?=implode(', ', $breakdown_parts);?>)
											<?php endif; ?>
											</p>
										</div>
										<?php
									endif;
									?>
								</div>
							<?php endforeach; ?>
						</div>
						
						<script>
						$(document).ready(function() {
							// Handle clicking on previous draw ball badges
							$('.prev-ball-badge').on('click', function() {
								var ballNum = $(this).data('ball');
								var isExtra = $(this).data('is-extra');
								
								// Hide all sections first
								$('.prev-ball-section').removeClass('active');
								
								// Show only the clicked ball's section
								$('.prev-ball-section[data-ball="'+ballNum+'"][data-is-extra="'+isExtra+'"]').addClass('active');
								
								// Scroll to the section
								var $section = $('.prev-ball-section[data-ball="'+ballNum+'"]');
								if($section.length) {
									$('html, body').animate({
										scrollTop: $section.offset().top - 100
									}, 300);
								}
							});
							
							// Show all button
							$('#show-all-prev-balls').on('click', function() {
								$('.prev-ball-section').addClass('active');
							});
							
							// Hide all button
							$('#hide-all-prev-balls').on('click', function() {
								$('.prev-ball-section').removeClass('active');
							});
						});
						</script>
						<?php endif; ?>
						
						<?php if(isset($prev_draw) && $prev_draw['exists']): ?>
						<div class="winner-legend" style="margin: 30px 20px 20px 20px;">
							<strong>How to Read This Page:</strong>
							<ul class="mb-0 mt-2">
								<li><strong>Win History Tables:</strong> Prize category wins and position wins for each drawn ball (displayed in the tabs above)</li>
								<li><strong>Previous Draw Followers:</strong> What the follower predictions were before the previous draw (shown in the blue box above)</li>
								<li><strong>Yellow Border Highlighting:</strong> Numbers with <span style="background-color: #fff3cd; border: 2px solid #ffc107; padding: 2px 6px; border-radius: 3px;">yellow border</span> were actually drawn on <strong><?=date("l, F j, Y", strtotime(str_replace('/','-',$lottery->last_drawn['draw_date'])));?></strong> (most recent draw)
								<?php if($lottery->duplicate_extra_ball): ?>
								<br><em style="margin-left: 20px; font-size: 0.9em;">Exception: For main ball predictions, numbers are NOT highlighted if they match the extra ball (appear in both main and extra).</em>
								<?php endif; ?>
							</li>
							</ul>
						</div>
						<?php endif; ?>
						
						<!-- Followers Previous Predicted Winners -->
						<?php if(!empty($prev_lottery_numbers)): 
							// Parse encoded prev_lottery_numbers: follower_type|ball_points|position_points|numbers
							$prev_parts = explode('|', $prev_lottery_numbers);
							if (count($prev_parts) === 4) {
								// Encoded format with metadata
								$prev_follower_type = $prev_parts[0];
								$prev_ball_points = $prev_parts[1];
								$prev_position_points = $prev_parts[2];
								$prev_numbers_str = $prev_parts[3];
							} else {
								// Legacy format - just numbers
								$prev_follower_type = 'after_ball';
								$prev_ball_points = '';
								$prev_position_points = '';
								$prev_numbers_str = $prev_lottery_numbers;
							}
							$prev_numbers = explode(',', $prev_numbers_str);
							
							// Build subtitle based on follower type
							$prev_subtitle = '';
							if ($prev_follower_type === 'after_ball' && !empty($prev_ball_points)) {
								$prev_subtitle = 'After Ball ' . htmlspecialchars($prev_ball_points);
							} elseif ($prev_follower_type === 'position' && !empty($prev_position_points)) {
								$prev_subtitle = 'Position ' . htmlspecialchars($prev_position_points);
							}
							
							// Get current draw numbers for comparison
							$current_main_balls = array();
							$current_extra_ball = null;
							if (isset($lottery->last_drawn)) {
								for ($i = 1; $i <= $lottery->balls_drawn; $i++) {
									$ball_key = 'ball' . $i;
									if (isset($lottery->last_drawn[$ball_key])) {
										$current_main_balls[] = $lottery->last_drawn[$ball_key];
									}
								}
								if ($lottery->extra_ball && isset($lottery->last_drawn['extra'])) {
									$current_extra_ball = $lottery->last_drawn['extra'];
								}
							}
						?>
						<div style="background-color: #e8f5e9; border-left: 4px solid #4caf50; padding: 15px; margin: 30px 20px 20px 20px; border-radius: 4px;">
							<h5 style="color: #2e7d32; margin-bottom: 10px;">
								<i class="fa fa-chart-line"></i> Followers Previous Predicted Winners
								<?php if(!empty($prev_subtitle)): ?>
									<span style="font-size: 0.85em; font-weight: normal; color: #555;">(<?=$prev_subtitle;?>)</span>
								<?php endif; ?>
							</h5>
							<div style="margin-top: 10px;">
								<?php foreach ($prev_numbers as $num): 
									$num = trim($num);
									if (empty($num)) continue;
									
									// Determine ball color based on match type
									$ball_color = '#28a745'; // Default green (not drawn)
									$ball_title = 'Not drawn in current draw';
									
									if (in_array($num, $current_main_balls)) {
										$ball_color = '#FFD700'; // Gold - main ball match
										$ball_title = 'Main ball match';
									} elseif ($current_extra_ball && $num == $current_extra_ball) {
										$ball_color = '#1565C0'; // Blue - bonus/extra ball match
										$ball_title = 'Bonus/Extra ball match';
									}
								?>
								<span class="badge" style="
									background-color: <?=$ball_color;?>;
									color: #ffffff;
									font-size: 16px;
									font-weight: bold;
									padding: 8px 12px;
									margin: 3px;
									border-radius: 50%;
									display: inline-block;
									min-width: 40px;
									text-align: center;
									box-shadow: 0 2px 4px rgba(0,0,0,0.2);
								" title="<?=$ball_title;?>"><?=htmlspecialchars($num);?></span>
								<?php endforeach; ?>
							</div>
							<div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #c8e6c9; font-size: 0.9em; color: #555;">
								<strong>Legend:</strong>
								<span style="color: #FFD700; font-weight: bold;">●</span> Main ball match &nbsp;
								<span style="color: #1565C0; font-weight: bold;">●</span> Bonus/Extra ball match &nbsp;
								<span style="color: #28a745; font-weight: bold;">●</span> Not drawn
							</div>
						</div>
						<?php endif; ?>
						
						<!-- Followers Win Statistics -->
						<?php if(isset($followers_win_stats)): ?>
	<?php
	// Build ordered prize columns (highest to lowest) filtered by valid categories
	$followers_prize_cols = array();
	$valid_cats = isset($lottery->valid_prize_categories) ? $lottery->valid_prize_categories : array();
	$balls_drawn = isset($lottery->balls_drawn) ? intval($lottery->balls_drawn) : 9;
	$has_extra = !empty($lottery->extra_ball);
	
	// Start from balls_drawn down to 1, interleaving extra ball prizes by prize hierarchy
	for($i = $balls_drawn; $i >= 1; $i--) {
		$col = $i.'_win';
		// Add current number without extra
		if(in_array($col, $valid_cats)) {
			$followers_prize_cols[] = array('key' => $col, 'label' => $i, 'title' => $i.' Number'.($i > 1 ? 's' : ''));
		}
		// Add next lower number WITH extra (higher prize than next lower without extra)
		if($i > 1 && $has_extra) {
			$col_lower_extra = ($i-1).'_win_extra';
			if(in_array($col_lower_extra, $valid_cats)) {
				$followers_prize_cols[] = array('key' => $col_lower_extra, 'label' => ($i-1).'+', 'title' => ($i-1).' Number'.($i > 2 ? 's' : '').' + Extra');
			}
		}
	}
	// Add 1 number alone if not already added (when i=1 in loop, we don't add 0+)
	// Note: It's already added in the loop when i=1
	// Add extra-only at the end (lowest prize)
	if($has_extra && in_array('extra', $valid_cats)) {
		$followers_prize_cols[] = array('key' => 'extra', 'label' => '+', 'title' => 'Extra Ball Only');
	}
	
	// Calculate draw count
	$draw_count = 0;
	if($followers_win_stats['startdate'] && $followers_win_stats['lastdate']) {
		$start = new DateTime($followers_win_stats['startdate']);
		$end = new DateTime($followers_win_stats['lastdate']);
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
		.followers-win-stats-table { font-size: 0.80em; table-layout: fixed; width: 100%; margin-bottom: 0; background-color: white; }
		.followers-win-stats-table th { font-size: 0.80em; font-weight: bold; white-space: nowrap; padding: 0.3rem 0.2rem; text-align: center; }
		.followers-win-stats-table td { padding: 0.25rem 0.2rem; text-align: center; white-space: nowrap; }
		.followers-win-record-col { width: 22px !important; font-size: 0.80em; white-space: nowrap; }
		.followers-win-total-col { width: 46px !important; font-size: 0.80em; white-space: nowrap; }
		@media (max-width: 992px) {
			.followers-win-stats-table { font-size: 0.74em; table-layout: auto; width: auto; min-width: 600px; }
			.followers-win-stats-table th { font-size: 0.74em; padding: 0.25rem 0.15rem; }
			.followers-win-stats-table td { padding: 0.2rem 0.15rem; }
			.followers-win-record-col { width: 20px !important; font-size: 0.74em; }
			.followers-win-total-col { width: 42px !important; font-size: 0.74em; }
		}
		@media (max-width: 576px) {
			.followers-win-stats-table { font-size: 0.68em; min-width: 500px; }
			.followers-win-stats-table th { font-size: 0.68em; padding: 0.2rem 0.1rem; }
			.followers-win-stats-table td { padding: 0.18rem 0.1rem; }
			.followers-win-record-col { width: 18px !important; font-size: 0.68em; }
			.followers-win-total-col { width: 38px !important; font-size: 0.68em; }
		}
	</style>
	<div style="margin: 20px; padding: 18px; background-color: #e3f2fd; border-left: 4px solid #1976d2; border-radius: 4px;">
		<div style="text-align: center; margin-bottom: 15px;">
			<strong style="color: #000000; font-size: 1.1em;">
				<i class="fa fa-trophy"></i> Followers Prediction Win Records
							</strong>
						</div>
						<div style="margin-bottom: 10px; text-align: center; font-size: 0.9em; color: #666;">
							<?php if($followers_win_stats['startdate'] || $followers_win_stats['lastdate']): ?>
								<strong>Start:</strong> <?php echo $followers_win_stats['startdate'] ? date('M j, Y', strtotime($followers_win_stats['startdate'])) : date('M j, Y', strtotime($lottery->next_draw_date)); ?>
								&nbsp;&nbsp;|
								<?php if($followers_win_stats['lastdate']): ?>
									<strong>Last Draw:</strong> <?php echo date('M j, Y', strtotime($followers_win_stats['lastdate'])); ?> (<?php echo $draw_count; ?> draw<?php echo $draw_count != 1 ? 's' : ''; ?>)
								<?php else: ?>
									<strong>Last Draw:</strong> None yet
								<?php endif; ?>
							<?php else: ?>
								<strong>Start:</strong> <?php echo date('M j, Y', strtotime($lottery->next_draw_date)); ?> &nbsp;|&nbsp; <strong>Last Draw:</strong> None yet
							<?php endif; ?>
							<button type="button" class="btn btn-sm btn-danger" onclick="resetFollowersWinStats(<?php echo $lottery->id; ?>)" 
								style="margin-left: 15px;">
								<i class="fa fa-undo"></i> Reset
							</button>
						</div>
						<div class="table-responsive" style="overflow-x: auto;">
							<table class="table table-bordered table-striped followers-win-stats-table">
								<thead>
									<tr>
										<th colspan="<?php echo count($followers_prize_cols) + 1; ?>" class="text-center" style="background-color: #f4f4f4;">
											<strong>Win Record</strong>
										</th>
									</tr>
									<tr>
										<?php foreach($followers_prize_cols as $col): ?>
											<th class="text-center followers-win-record-col" style="background-color: #e8f5e8;" title="<?php echo htmlspecialchars($col['title']); ?>"><?php echo htmlspecialchars($col['label']); ?></th>
										<?php endforeach; ?>
										<th class="text-center followers-win-total-col" style="background-color: #d4edda; font-weight: bold;" title="Total Winners">Total</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<?php foreach($followers_prize_cols as $col): ?>
											<td class="text-center"><?php echo number_format($followers_win_stats[$col['key']]); ?></td>
										<?php endforeach; ?>
										<td class="text-center" style="background-color: #bbdefb; font-weight: bold;"><?php echo number_format($followers_win_stats['total_winners']); ?></td>
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
// Reset Followers Win Statistics
function resetFollowersWinStats(lotteryId) {
	if(confirm('WARNING: This will clear all Followers prediction win records and reset the statistics.\n\nThe new start date will be set to the next draw date.\n\nAre you sure you want to continue?')) {
		$.ajax({
			url: '<?php echo site_url("admin/history/reset_followers_win_stats"); ?>',
			type: 'POST',
			data: { lottery_id: lotteryId },
			success: function(response) {
				location.reload();
			},
			error: function() {
				alert('Error resetting win statistics. Please try again.');
			}
		});
	}
}

(function() {
	if (!window._bestPointsBalls || !window._bestPointsBalls.length) return;
	// Highlight the nav tab(s) whose text matches a best-ball number
	document.querySelectorAll('#myTab .nav-link').forEach(function(el) {
		var txt = el.textContent.replace(/[^0-9]/g, '').trim();
		if (!txt) return;
		var num = parseInt(txt, 10);
		if (window._bestPointsBalls.indexOf(num) !== -1) {
			el.style.backgroundColor = '#d4edda';
			el.style.color = '#155724';
			el.style.fontWeight = 'bold';
			el.style.borderRadius = '4px';
			el.insertAdjacentHTML('afterend',
				'<div class="text-success small mt-1" style="white-space:nowrap;">&#9733; Highest Points (' + window._bestPointsVal + ' pts)</div>'
			);
		}
	});
})();
</script>
