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
											$js = "location.href='".base_url()."admin/statistics/followers/".$lottery->id."/".(!$interval ? $sel_range : ($sel_range*100))."/extra'";
											$attr = array(
												'onClick' 	=> "$js", 
												'class'		=> "form-check-input"
											);
											$extra = array('for' => 'extra_lb');
											echo form_checkbox('extra_included', set_value('extra_included', '1'), set_checkbox('extra_included', '1', (!empty($lottery->extra_included))), $attr);
											echo form_label('Extra (Bonus) Ball Included?', 'extra_lb', $extra);
										?>
										</div>
										<div class="form-check" style="margin-top: 10px;">
										<?php
											$js = "location.href='".base_url()."admin/statistics/followers/".$lottery->id."/".(!$interval ? $sel_range : ($sel_range*100))."/draws'";
											$attr = array(
												'onClick' 	=> "$js", 
												'class'		=> "form-check-input"
											);
										$extra = array('for' => 'extra_draw_lb');
											echo form_checkbox('extra_draws', '1', set_checkbox('extra_draws', '1', (!empty($lottery->extra_draws))), $attr);
											echo form_label('Extra Draw(s) Included?', 'extra_draw_lb', $extra); 
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
								<?php if(array_key_exists(($b>$cd ? $lottery->last_drawn['extra'] : $lottery->last_drawn['ball'.$b]), $lottery->last_drawn)):
										/* difference is when any of the regular balls match the duplicate extra ball **/
										$xtr = (($lottery->duplicate_extra_ball&&$lottery->extra_included) ? $lottery->last_drawn[$lottery->last_drawn['extra'].'x'] : $lottery->last_drawn[$lottery->last_drawn['extra']]);
										$trailer = explode('|', ($b>$cd ? $xtr : $lottery->last_drawn[$lottery->last_drawn['ball'.$b]])); ?>
 										<h5 class="card-title">After Ball <?=($b>$cd ? $lottery->last_drawn['extra'] : $lottery->last_drawn['ball'.$b]);?> has been drawn in <?=$lottery->last_drawn['range']; ?> draws.</h5>
										<?php $t_picks = array(); 
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
											<p class="card-text">The total number of predictor balls for this ball is <strong><?=$sum;?></strong>.</p>
											<p class="card-text">These are the numbers listed above that have the highest probability of being drawn.</p>
										<?php }
										else{ ?>
											<p class="card-text">There a followers with not more than 2 occurrences in the range of <?=$lottery->last_drawn['range'];?> draws.</p>
										<?php }
										/* Display the non - following numbers for the given range */
										/* Determine the number from the ball position and then access the ball+nf for not followed */
											/* difference is when any of the regular balls match the duplicate extra ball load the duplicate extra non followers **/
											$xtr = (($lottery->duplicate_extra_ball&&$lottery->extra_included) ? $lottery->last_drawn[$lottery->last_drawn['extra'].'nfx'] : $lottery->last_drawn[$lottery->last_drawn['extra']]);
											$nonfollowers = explode('|', ($b>$cd ? $xtr : $lottery->last_drawn[$lottery->last_drawn['ball'.$b].'nf'])); 
											$non_picks = "";
											if($nonfollowers[0]): /* Check for all non followers have followed */
												$sum = 0; // Reset the sum counter;
												$non_picks .= "These Numbers have <strong>NEVER</strong> followed this Ball <strong>".($b>$cd ? $lottery->last_drawn['extra'] : $lottery->last_drawn['ball'.$b])."</strong> for ".$lottery->last_drawn['range']." Draws:<br />";
												foreach($nonfollowers as $nf):  
													$non_picks .= 'Number: <strong>'.$nf.'</strong><br />';
													$sum++;	
												endforeach;
												echo "<p class='card-text'> ".$non_picks."</p>";
												$plural = (string) ($sum>1 ?  " balls " : " ball ");
												echo "<p class='card-text'><strong>".$sum.$plural."</strong> in this non-follower group.</p>";
											unset($nonfollowers);
										endif;  
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