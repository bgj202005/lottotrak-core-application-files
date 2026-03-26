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
					</div>
				</div>
			</div>
		</div>
	</section>
<script>
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