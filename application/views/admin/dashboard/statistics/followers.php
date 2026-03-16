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
	/* Winner highlighting styles */
	.winner-legend {
		background-color: #f8f9fa;
		border-left: 4px solid #28a745;
		padding: 10px 15px;
		margin-bottom: 15px;
		border-radius: 4px;
	}
	.winner-legend .badge {
		font-size: 0.9em;
	}
	/* Highlight followers/non-followers matching previous draw */
	.prev-draw-match {
		background-color: #fff3cd;
		border: 2px solid #ffc107;
		padding: 2px 6px;
		border-radius: 3px;
		font-weight: bold;
	}
	/* Previous draw number badges - clickable */
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
	/* Hide ball sections by default */
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
	<h5 style = "text-align:left"><?php echo anchor('admin/statistics', 'Back to Statistics Dashboard', 'title="Back to Statistics"'); ?></h5>
	
	<?php if($lottery->out_of_range): ?>
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="alert alert-danger" role="alert" style = "text-align:center;">
					<h4>There are not enough draws to calculate the prizes.</h4>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<section>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="card mt-3 tab-card">
						<div class="card-header tab-card-header">
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
								<li><div class="dropdown" style = "margin-left: 50px;">
										<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											Draw Range
										</button>
										<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
											<?php $interval = (integer) $lottery->last_drawn['interval'];
											if(!$interval) : 
												$sel_range = $lottery->last_drawn['range']; ?>
												<a class="dropdown-item active" href="<?=base_url('admin/statistics/followers/'.$lottery->id)?>">All Draws (<?=$lottery->last_drawn['range'];?>) </a>
											<?php else:
												$sel_range = (integer) $lottery->last_drawn['sel_range']; // Selected a different range from the complete range of draws?
												for($i = 1; $i <= $interval; $i++):
													$step = $i * 100;	// in multiples of 100
													if($i!=$interval): ?>
														<a class="dropdown-item <?php if($i==$sel_range) echo 'active'; ?> " href="<?=base_url('admin/statistics/followers/'.$lottery->id.'/'.$step);?>">Last <?=$step;?></a>
													<?php else : ?>
														<a class="dropdown-item <?php if($i==$sel_range) echo 'active'; ?> " href="<?=base_url('admin/statistics/followers/'.$lottery->id.'/'.$lottery->last_drawn['all']);?>">All Draws (<?=$lottery->last_drawn['all'];?>)</a>
													<?php endif;
												endfor; ?> 
												<?php endif;?>
										</div>
										<div class="form-check" style="margin-top: 10px;">
										<?php 
											// Build URL with current extra_draws state preserved
											$current_extra_draws = (!empty($lottery->extra_draws)) ? '/draws' : '';
											$base_url = base_url()."admin/statistics/followers/".$lottery->id."/".$lottery->last_drawn['range'];
											// Fixed toggle logic: if currently checked (extra_included=1), remove /extra to uncheck it
											// If currently unchecked (extra_included=0), add /extra to check it
											if($lottery->extra_included) {
												// Currently checked - clicking should uncheck (go to base URL without /extra)
												$extra_url = $base_url.$current_extra_draws;
											} else {
												// Currently unchecked - clicking should check (add /extra)
												$extra_url = $base_url.'/extra'.$current_extra_draws;
											}
											$js = "event.preventDefault(); location.href='".$extra_url."'";
											$attr = array(
												'onClick' 	=> "$js", 
												'class'		=> "form-check-input"
											);
											// Determine checked state directly from URL parameters, not form helper  
											$checked = (!empty($lottery->extra_included)) ? 'checked' : '';

											echo '<input type="checkbox" name="extra_included" id="extra_included" value="1" class="form-check-input" '.$checked.' onClick="'.$js.'" />';
											echo '<label for="extra_included">Extra (Bonus) Ball Included?</label>';
										?>
										</div>
										<div class="form-check" style="margin-top: 10px;">
										<?php
											// Build URL with current extra_included state preserved  
											$current_extra_included = (!empty($lottery->extra_included)) ? '/extra' : '';
											$base_url = base_url()."admin/statistics/followers/".$lottery->id."/".$lottery->last_drawn['range'];
											// Fixed toggle logic: if currently checked (extra_draws=1), remove /draws to uncheck it
											// If currently unchecked (extra_draws=0), add /draws to check it
											if($lottery->extra_draws) {
												// Currently checked - clicking should uncheck (go to base URL without /draws)
												$draws_url = $base_url.$current_extra_included;
											} else {
												// Currently unchecked - clicking should check (add /draws)
												$draws_url = $base_url.$current_extra_included.'/draws';
											}
											$js = "event.preventDefault(); location.href='".$draws_url."'";
											$attr = array(
												'onClick' 	=> "$js", 
												'class'		=> "form-check-input"
											);
											// Determine checked state directly from URL parameters, not form helper
											$checked = (!empty($lottery->extra_draws)) ? 'checked' : '';

											echo '<input type="checkbox" name="extra_draws" id="extra_draws" value="1" class="form-check-input" '.$checked.' onClick="'.$js.'" />';
											echo '<label for="extra_draws">Extra Draw(s) Included?</label>'; 
										?>
										</div>
									</div>
								</li>			
							</ul>
						</div>
						<div class="tab-content" id="myTabContent">
							<?php $b = 1;			   
							$cd = intval($max);						  // This is the maximum ball drawn without an extra ball
							if($lottery->extra_included):  $max++; endif; // Include the extra ball
							do
							{ ?> 
							<div class="tab-pane fade p-3 <?php if($b==1) echo 'show active'; ?>" id="ball<?=$b?>" role="tabpanel" aria-labelledby="tab-<?=$b;?>">
								<?php $dup_or_not = ($lottery->duplicate_extra_ball ? $lottery->last_drawn['extra'].'x' : $lottery->last_drawn['extra']);
									if(array_key_exists(($b>$cd ? $dup_or_not : $lottery->last_drawn['ball'.$b]), $lottery->last_drawn)):
										/* difference is when any of the regular balls match the duplicate extra ball **/
										if(isset($lottery->last_drawn[$dup_or_not])):
											$xtr = (($lottery->duplicate_extra_ball&&$lottery->extra_included) ? $lottery->last_drawn[$lottery->last_drawn['extra'].'x'] : $lottery->last_drawn[$lottery->last_drawn['extra']]);
										else:
											$xtr = "0|0";
										endif;
										$trailer = explode('|', ($b>$cd ? $xtr : $lottery->last_drawn[$lottery->last_drawn['ball'.$b]])); ?>
 										<h5 class="card-title">After Ball <?=($b>$cd ? $lottery->last_drawn['extra'] : $lottery->last_drawn['ball'.$b]);?> has been drawn in <?=$lottery->last_drawn['range']; ?> draws.</h5>
										<?php 
										// Check if this is an independent extra ball lottery and if we have extra followers data
										$has_extra_followers = false;
										$extra_trailer = array();
										if($lottery->duplicate_extra_ball && $b<=$cd) {
											$extra_key = $lottery->last_drawn['ball'.$b].'_extra';
											if(isset($lottery->last_drawn[$extra_key])) {
												$has_extra_followers = true;
												$extra_trailer = explode('|', $lottery->last_drawn[$extra_key]);
											}
										} elseif($lottery->duplicate_extra_ball && $b>$cd) {
											$extra_key = $lottery->last_drawn['extra'].'x_extra';
											if(isset($lottery->last_drawn[$extra_key])) {
												$has_extra_followers = true;
												$extra_trailer = explode('|', $lottery->last_drawn[$extra_key]);
											}
										}
										
										$t_picks = array(); 
											foreach($trailer as $t):  
												$picks = explode('=', $t);
												$t_picks += array(
														$picks[0] => $picks[1]
												);
												unset($picks);
											endforeach;
										arsort($t_picks); // Sort from the most picks to the least picks
										$s_picks = "";
										$sum = 0;
										$counts = current($t_picks);
										
										// Always show Main Balls heading for independent extra ball lotteries
										if($lottery->duplicate_extra_ball): ?>
											<h5 class="text-primary bg-light p-2 border rounded"><strong>Main Balls</strong> (Range: <?=$lottery->minimum_ball;?> - <?=$lottery->maximum_ball;?>)</h5>
										<?php endif;
										
										if($counts) // followers with a count of greater than 0
											{
												do
											{
												if($counts==current($t_picks)):
													$s_picks .= 'Number <strong>'.key($t_picks).'</strong>';
													$current = next($t_picks);
													$sum++;
													if($counts!=$current):
														$s_picks .= ' has been drawn <strong>'.$counts.'</strong> Times.</p>';
														echo "<p class='card-text'> ".$s_picks."</p>";
														$counts = $current;
														$s_picks = "";
													else:
														$s_picks .= ' AND ';
													endif;
												else:
													$counts = next($t_picks); 
												endif; 
											} while(!is_null(key($t_picks)));
											unset($trailer);?>
											<p class="card-text">The total number of <?=($lottery->duplicate_extra_ball ? 'main ball ' : '');?>followers for this <?=($b>$cd ? 'extra ball' : 'main ball');?> is <strong><?=$sum;?></strong>.</p>
										<?php }
										else{ ?>
											<p class="card-text">There are no <?=($lottery->duplicate_extra_ball ? 'main ball ' : '');?>followers with more than 2 occurrences in the range of <?=$lottery->last_drawn['range'];?> draws.</p>
										<?php }
										
										// Display non-followers for all lotteries (regular and independent extra ball lotteries)
										// Get non-followers data
										if(isset($lottery->last_drawn[$lottery->last_drawn['extra'].'nf'])):
											$xtr = (($lottery->duplicate_extra_ball&&$lottery->extra_included) ? $lottery->last_drawn[$lottery->last_drawn['extra'].'nfx'] : $lottery->last_drawn[$lottery->last_drawn['extra'].'nf']);
										else:
											$xtr = '0|0';
										endif;	
										$nonfollowers = explode('|', ($b>$cd ? $xtr : $lottery->last_drawn[$lottery->last_drawn['ball'.$b].'nf'])); 
										$non_picks = "";
										if($nonfollowers[0]): ?>
											<?php $sum_nf = 0; // Reset the sum counter;
											$non_picks .= "These ".($lottery->duplicate_extra_ball ? "Main Ball " : "")."Numbers have <strong>NEVER</strong> followed this Ball <strong>".($b>$cd ? $lottery->last_drawn['extra'] : $lottery->last_drawn['ball'.$b])."</strong> for ".$lottery->last_drawn['range']." Draws:<br />";
											foreach($nonfollowers as $nf):  
											$non_picks .= 'Number: <strong>'.$nf.'</strong><br />';
												$sum_nf++;	
											endforeach; ?>
											<p class='card-text'><?php echo $non_picks; ?></p>
											<?php $plural_nf = (string) ($sum_nf>1 ?  " balls " : " ball "); ?>
											<p class='card-text'><strong><?php echo $sum_nf.$plural_nf; ?></strong> in this <?=($lottery->duplicate_extra_ball ? 'main ball ' : '');?>non-follower group.</p>
										<?php endif;
										unset($nonfollowers);
										
										// Display main ball non-followers under Main Balls heading for independent extra ball lotteries
										if($lottery->duplicate_extra_ball): 
											// For independent extra ball lotteries, we already displayed the main ball non-followers above
											// So we don't need to duplicate the display here
										endif;
										
										// Display extra ball section for independent extra ball lotteries
										if($lottery->duplicate_extra_ball): ?>
											<h5 class="text-success bg-light p-2 border rounded"><strong>Extra Balls</strong> (Range: <?=$lottery->minimum_extra_ball;?> - <?=$lottery->maximum_extra_ball;?>)</h5>
											<?php if($has_extra_followers && !empty($extra_trailer)): ?>
											<?php $extra_t_picks = array(); 
											foreach($extra_trailer as $t):  
												$picks = explode('=', $t);
												if(count($picks) == 2):
													$extra_t_picks += array(
															$picks[0] => $picks[1]
													);
												endif;
												unset($picks);
											endforeach;
											arsort($extra_t_picks); // Sort from the most picks to the least picks
											$extra_s_picks = "";
											$extra_sum = 0;
											$extra_counts = current($extra_t_picks);
											if($extra_counts): 
												$extra_first_run = true;
												while(!is_null(key($extra_t_picks)) || $extra_first_run):
													$extra_first_run = false;
													if($extra_counts==current($extra_t_picks)):
												$extra_s_picks .= 'Number <strong>'.key($extra_t_picks).'</strong>';
														$extra_current = next($extra_t_picks);
														$extra_sum++;
														if($extra_counts!=$extra_current):
															$extra_s_picks .= ' has been drawn <strong>'.$extra_counts.'</strong> Times.</p>';
															echo "<p class='card-text'> ".$extra_s_picks."</p>";
															$extra_counts = $extra_current;
															$extra_s_picks = "";
														else:
															$extra_s_picks .= ' AND ';
														endif;
													else:
														$extra_counts = next($extra_t_picks); 
													endif; 
												endwhile; ?>
												<p class="card-text">The total number of extra ball followers for this <?=($b>$cd ? 'extra ball' : 'main ball');?> is <strong><?php echo $extra_sum; ?></strong>.</p>
											<?php else: ?>
												<p class="card-text">There are no extra ball followers with more than 2 occurrences in the range of <?php echo $lottery->last_drawn['range']; ?> draws.</p>
											<?php endif; ?>
											<?php else: ?>
												<p class="card-text">There are no extra ball followers with more than 2 occurrences in the range of <?php echo $lottery->last_drawn['range']; ?> draws.</p>
										<?php endif; ?>
										
										<!-- Display extra ball non-followers under Extra Balls heading -->
										<?php 
										$extra_nf_key = '';
										if($b<=$cd):
											$extra_nf_key = $lottery->last_drawn['ball'.$b].'nf_extra';
										else:
											$extra_nf_key = $lottery->last_drawn['extra'].'nfx_extra';
										endif;
										
										if(isset($lottery->last_drawn[$extra_nf_key])):
											$extra_nonfollowers = explode('|', $lottery->last_drawn[$extra_nf_key]);
											if($extra_nonfollowers[0] && $extra_nonfollowers[0] != '0'): ?>
												<?php $extra_non_picks = "";
												$extra_sum = 0;
												$extra_non_picks .= "These Extra Ball Numbers have <strong>NEVER</strong> followed this ".($b>$cd ? "Extra " : "")."Ball <strong>".($b>$cd ? $lottery->last_drawn['extra'] : $lottery->last_drawn['ball'.$b])."</strong> for ".$lottery->last_drawn['range']." Draws:<br />";
												foreach($extra_nonfollowers as $nf):  
													if($nf && $nf != '0'):
														$extra_non_picks .= 'Number: <strong>'.$nf.'</strong><br />';
														$extra_sum++;
													endif;
												endforeach;
												if($extra_sum > 0): ?>
													<p class='card-text'><?php echo $extra_non_picks; ?></p>
													<?php $extra_plural = (string) ($extra_sum>1 ?  " balls " : " ball "); ?>
													<p class='card-text'><strong><?php echo $extra_sum.$extra_plural; ?></strong> in the extra non-follower group.</p>
												<?php endif; ?>
											<?php endif; ?>
										<?php endif; ?>
										<?php endif;
									
									// Original "No Criteria" message
									else: 
										echo "<p class='card-text'> No Criteria High enough to Use for this Ball. </p>";
									endif; ?>
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
								$winners_count = 0; // Track how many followers were actually drawn
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
										elseif($is_extra && isset($current_draw_numbers['extra']) && $num == $current_draw_numbers['extra']):
											$was_drawn = true;
										elseif(!$is_extra && in_array($num, $current_draw_numbers)):
											// EXCEPTION for duplicate_extra_ball lotteries:
											// Do NOT highlight if this number is the extra ball (appears in both main and extra)
											if($lottery->duplicate_extra_ball && isset($current_draw_numbers['extra']) && $num == $current_draw_numbers['extra']):
												$was_drawn = false;
											else:
												$was_drawn = true;
											endif;
										endif;
										
										if($was_drawn) $winners_count++; // Increment winner counter
										
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
												$nonfollowers_winners_count = 0; // Track winners in non-followers
												
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
													elseif($is_extra && isset($current_draw_numbers['extra']) && $nf_num == $current_draw_numbers['extra']):
														$was_drawn = true;
													elseif(!$is_extra && in_array($nf_num, $current_draw_numbers)):
														// EXCEPTION for duplicate_extra_ball lotteries:
														// Do NOT highlight if this number is the extra ball (appears in both main and extra)
														if($lottery->duplicate_extra_ball && isset($current_draw_numbers['extra']) && $nf_num == $current_draw_numbers['extra']):
															$was_drawn = false;
														else:
															$was_drawn = true;
														endif;
													endif;
													
													if($was_drawn) $nonfollowers_winners_count++; // Increment winner counter
													
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
											$nonfollowers_winners_count_extra = 0;
										endif;
									else:
										$nonfollowers_winners_count = 0;
										$nonfollowers_winners_count_extra = 0;
									endif;
									
									// Display total winners from all sections (followers + non-followers + extra ball followers + extra ball non-followers)
									$total_winners = $winners_count + $nonfollowers_winners_count + $winners_count_extra + $nonfollowers_winners_count_extra;
									if($total_winners > 0):
										$plural_winners = ($total_winners > 1) ? "winners" : "winner";
										// Build breakdown string
										$breakdown_parts = array();
										if($winners_count > 0) $breakdown_parts[] = $winners_count.' from main followers';
										if($winners_count_extra > 0) $breakdown_parts[] = $winners_count_extra.' from extra ball followers';
										if($nonfollowers_winners_count > 0) $breakdown_parts[] = $nonfollowers_winners_count.' from main non-followers';
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
								<li><strong>Current Followers:</strong> Predictions for the next draw (displayed in the tabs above)</li>
								<li><strong>Previous Draw Followers:</strong> What the predictions were before the previous draw (shown in the blue box above)</li>
							<li><strong>Yellow Border Highlighting:</strong> Numbers with <span style="background-color: #fff3cd; border: 2px solid #ffc107; padding: 2px 6px; border-radius: 3px;">yellow border</span> were actually drawn on <strong><?=date("l, F j, Y", strtotime(str_replace('/','-',$lottery->last_drawn['draw_date'])));?></strong> (most recent draw)
								<?php if($lottery->duplicate_extra_ball): ?>
								<br><em style="margin-left: 20px; font-size: 0.9em;">Exception: For main ball predictions, numbers are NOT highlighted if they match the extra ball (appear in both main and extra).</em>
								<?php endif; ?>
							</li>
							</ul>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>