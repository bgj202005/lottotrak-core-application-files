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
	/* Prediction styles */
	.pred-ball {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		background: #28a745;
		color: #fff;
		border-radius: 50%;
		width: 40px;
		height: 40px;
		font-weight: bold;
		font-size: 0.95em;
		margin: 3px;
	}
	.option-panel {
		background: #f8f9fa;
		border: 1px solid #dee2e6;
		border-radius: 6px;
		padding: 12px 16px;
		margin-bottom: 12px;
	}
	.pred-panel {
		background: #d4edda;
		border-left: 4px solid #28a745;
		border-radius: 4px;
		padding: 14px 18px;
		margin-bottom: 16px;
		text-align: center;
	}
</style>

<h2><?php echo 'View Followers for: '.$lottery->lottery_name; ?></h2>
	<?php $max = $lottery->balls_drawn; 
	   $b = 1; 
	   ?>	
	<h5 style = "text-align:left"><?php echo anchor('admin/statistics', 'Back to Statistics Dashboard', 'title="Back to Statistics"'); ?></h5>
	
	<?php if(!empty($follower_message)): ?>
	<div class="alert alert-success"><?=htmlspecialchars($follower_message);?></div>
	<?php endif; ?>
	
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
						
						<!-- PREDICTION PANELS INSIDE CARD -->
						<div style="padding: 20px; border-bottom: 1px solid #dee2e6;">
							<!-- Prediction Number Pool Panel -->
							<div class="option-panel">
								<strong>Prediction Number Pool: <?=htmlspecialchars($prediction_pool);?></strong>
								<div style="color: #6c757d; font-size: 0.9em; margin-top: 5px;">
									This number pool is set in the <a href="<?=base_url('admin/statistics/h_w_c/'.$lottery->id);?>">H-W-C view</a> and is read-only here.
								</div>
							</div>
							
							<!-- Option Settings Panel -->
							<div class="option-panel">
								<strong>Prediction Option:</strong>
								<?php
								$frm_attr = array('id' => 'frm_followers', 'style' => 'display:inline;');
							// Build form action URL with current range and checkbox states preserved
							$form_url = 'admin/statistics/followers/' . $lottery->id . '/' . $lottery->last_drawn['range'];
							if (!empty($lottery->extra_included)) $form_url .= '/extra';
							if (!empty($lottery->extra_draws)) $form_url .= '/draws';
							echo form_open(base_url($form_url), $frm_attr);
								?>
								<div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-top: 10px;">

									<!-- After Ball radio + dropdown -->
									<div class="form-check form-check-inline" style="margin:0;">
										<input class="form-check-input" type="radio" name="follower_type" id="ft_after_ball" value="after_ball"
											<?=($saved_follower_type !== 'position' ? 'checked' : '');?>
											onchange="document.getElementById('ball_pts_sel').style.display='inline-block'; document.getElementById('pos_pts_sel').style.display='none';">
										<label class="form-check-label" for="ft_after_ball" style="white-space:nowrap;">After Ball</label>
									</div>
									<select name="ball_points" id="ball_pts_sel" class="form-control" style="width:auto; min-width:110px; <?=($saved_follower_type === 'position' ? 'display:none;' : 'display:inline-block;');?>">
										<?php if(!empty($ball_points_options)): foreach($ball_points_options as $val => $lbl): ?>
										<option value="<?=htmlspecialchars($val);?>" <?=($saved_ball_points === (string)$val ? 'selected' : '');?>><?=htmlspecialchars($lbl);?></option>
										<?php endforeach; endif; ?>
									</select>

									<!-- Position radio + dropdown -->
									<div class="form-check form-check-inline" style="margin:0;">
										<input class="form-check-input" type="radio" name="follower_type" id="ft_position" value="position"
											<?=($saved_follower_type === 'position' ? 'checked' : '');?>
											onchange="document.getElementById('ball_pts_sel').style.display='none'; document.getElementById('pos_pts_sel').style.display='inline-block';">
										<label class="form-check-label" for="ft_position" style="white-space:nowrap;">Position</label>
									</div>
									<select name="position_points" id="pos_pts_sel" class="form-control" style="width:auto; min-width:110px; <?=($saved_follower_type === 'position' ? 'display:inline-block;' : 'display:none;');?>">
										<?php if(!empty($position_points_options)): foreach($position_points_options as $val => $lbl): ?>
										<option value="<?=htmlspecialchars($val);?>" <?=($saved_position_points === (string)$val ? 'selected' : '');?>><?=htmlspecialchars($lbl);?></option>
										<?php endforeach; endif; ?>
									</select>

									<!-- Submit -->
									<?php $btn_attr = array('class' => 'btn btn-danger');
									echo form_submit('change_follower_options', 'Change Follower Options', $btn_attr);
									echo form_close(); ?>
								</div>
							</div>

							<!-- Predicted Numbers for the Next Draw -->
							<?php if(!empty($lottery_numbers)):
								$fl_label = ($saved_follower_type === 'position')
									? 'Position ' . htmlspecialchars($saved_position_points)
									: 'After Ball ' . htmlspecialchars($saved_ball_points);
								// Next draw date label
								$draw_label = '';
								if(!empty($next_draw_date)) {
									$draw_label = date('D, M j, Y', strtotime(str_replace('/', '-', $next_draw_date)));
								}
							?>
							<div class="pred-panel">
								<strong>Predicted Numbers for the Next Draw<?=($draw_label ? ' &mdash; ' . $draw_label : '');?></strong>
								<div class="text-muted" style="font-size:0.85em; margin: 4px 0 10px;">
									(<?=$fl_label;?>)
								</div>
								<div style="display:flex; flex-wrap:wrap; justify-content:center;">
									<?php foreach(explode(',', $lottery_numbers) as $num): ?>
									<span class="pred-ball"><?=trim(htmlspecialchars($num));?></span>
									<?php endforeach; ?>
								</div>
							</div>
							<!-- Extra Ball Predictions Tile (independent / duplicate extra ball lotteries only) -->
							<?php if(!empty($lottery->duplicate_extra_ball) && !empty($lottery->extra_ball) && !empty($extra_numbers)): ?>
							<div class="pred-panel" style="background-color: #e3f2fd; border-left: 4px solid #1565C0;">
								<strong>Extra Ball Predictions for the Next Draw<?=($draw_label ? ' &mdash; ' . $draw_label : '');?></strong>
								<div style="display:flex; flex-wrap:wrap; justify-content:center; margin-top: 8px;">
									<?php foreach(explode(',', $extra_numbers) as $num): ?>
									<span class="pred-ball" style="background:#1565C0; color:#fff;"><?=trim(htmlspecialchars($num));?></span>
									<?php endforeach; ?>
								</div>
							</div>
							<?php endif; ?>
							<?php elseif(empty($lottery_numbers)): ?>
							<div class="alert alert-info" style="margin-bottom: 0;">
								No prediction has been generated yet. Select a follower option above and click <strong>Change Follower Options</strong>.
							</div>
							<?php endif; ?>
						</div>
						<!-- END PREDICTION PANELS -->
						
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
					</div>
				</div>
			</div>
		</div>
	</section>
