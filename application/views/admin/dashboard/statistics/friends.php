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
	
	/* Mobile responsive adjustments */
	@media (max-width: 768px) {
		.col-8, .col-4 {
			flex: 0 0 100%;
			max-width: 100%;
		}
		.col-4 > div {
			margin-left: 0 !important;
		}
	}
	
</style>
	<h2><?php echo 'View Friends for: '.$lottery->lottery_name; ?></h2>	
	<?php $max = $lottery->maximum_ball; 
	   $b = 1; ?>
	<h5 style = "text-align:left"><?php echo anchor('admin/statistics', 'Back to Statistics Dashboard', 'title="Back to Statistics"'); ?></h5>
	<section>
		<div class="container">
			<div class="row">
				<div class="col-8">
					<div class="card mt-3 tab-card">
						<div class="card-header tab-card-header">
							<ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
								<?php do
								{ ?>
								<li class="nav-item">
									<a id="tab-<?=$b;?>" data-toggle="tab" href="#ball<?=$b; ?>" role="tab" aria-controls="<?=$b;?>" aria-selected="true"> <?=$b?> </a>
								</li>
								<?php $b++;
								}
								while ($b<=$max); ?>
							</ul>
						</div>
						<div class="tab-content" id="myTabContent">
							<?php $b = 1;
							do
							{ ?>
							<div class="tab-pane fade p-3 <?php if($b==1) echo 'show active'; ?>" id="ball<?=$b?>" role="tabpanel" aria-labelledby="tab-<?=$b;?>">

								<?php if($lottery->friend['ball'.$b]): ?> 
									<div style = "color: #000000;" id="ball-<?=$b;?>">
											This is Ball <strong><?=$b;?></strong>. It's closest friend is Ball <strong><?php echo $lottery->friend['ball'.$b].
											'</strong>, being drawn <strong>'.$lottery->friend['count'.$b].
											'</strong> times. <br />The last time both <strong>'.$b.'</strong> and <strong>'.$lottery->friend['ball'.$b].
											'</strong> were drawn together was on <strong>'.date("l, F j, Y",strtotime(str_replace('/',' - ',$lottery->friend['date'.$b]))).'</strong>.';?>
										<div style = "margin-top:10px;" class="block p-2 bg-dark text-white">
										Ball <strong><?=$b;?> <u>NEVER</u></strong> had these balls show up over <strong><?php echo $lottery->last_drawn['range'];?></strong> 
										Draws:<br /><strong>
										<?php if(is_numeric($lottery->nonfriends['ball'.$b])):
												echo $lottery->nonfriends['ball'.$b];
										else:
												echo (!empty($lottery->nonfriends['ball'.$b]) ? substr_replace($lottery->nonfriends['ball'.$b], ' and ', 
												strrpos($lottery->nonfriends['ball'.$b], ','), 1).'.' : 'NONE');
										endif;?></strong>
										</div>
									</div>
								<?php else : ?>
									<div style = "color: #000000;" id="ball-<?=$b;?>">
											This is Ball <strong><?=$b;?></strong>. There is no close friend with this Ball for the given range of <strong><?php echo $lottery->last_drawn['range'];?></strong> Draws.
									</div>
								<?php endif; ?>
							</div>
							<?php $b++;
							}
							while ($b<=$max); ?>
							<p style = "margin:10px;" class="card-text">These are the numbers that have the highest probability of being drawn for the next draw.</p>  	
						</div>
					</div>
				</div>
				<div class="col-4">
					<div style="margin-left: 15px; margin-bottom: 2em;">
						<div class="dropdown" style="margin-bottom: 1em;">
							<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Draw Range
							</button>
							<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
								<?php $interval = (integer) $lottery->last_drawn['interval'];
								if(!$interval) : 
									$sel_range = $lottery->last_drawn['range']; ?>
									<a class="dropdown-item active" href="<?=base_url('admin/statistics/friends/'.$lottery->id)?>">All Draws (<?=$sel_range;?>) </a>
								<?php else:
									$sel_range = (integer) $lottery->last_drawn['sel_range']; // Selected a different range from the complete range of draws?
									for($i = 1; $i <= $interval; $i++):
										$step = $i * 100;	// in multiples of 100
										if($i!=$interval): ?>
											<a class="dropdown-item <?php if($i==$sel_range) echo 'active'; ?> " href="<?=base_url('admin/statistics/friends/'.$lottery->id.'/'.$step);?>">Last <?=$step;?></a>
										<?php else : ?>
											<a class="dropdown-item <?php if($i==$sel_range) echo 'active'; ?> " href="<?=base_url('admin/statistics/friends/'.$lottery->id.'/'.$lottery->last_drawn['all']);?>">All Draws (<?=$lottery->last_drawn['all'];?>)</a>
										<?php endif;
									endfor; ?> 
									<?php endif;?>
							</div>
						</div>
						<div class="form-check" style="margin-top: 10px;">
						<?php 
							$js = "location.href='".base_url()."admin/statistics/friends/".$lottery->id."/".(!$interval ? $sel_range : ($sel_range*100))."/extra'";
							$attr = array(
								'onClick' 	=> "$js", 
								'class'		=> "form-check-input"
							);
							$extra = array('for' => 'extra_lb');
							echo form_checkbox('extra_included', set_value('extra_included', '1'), set_checkbox('extra_included', '1', (!empty($lottery->extra_included))), $attr);
							echo form_label('Extra (Bonus) Ball Included?', 'extra_lb', $extra);
						?>
						</div>
						<div class="form-check" style="margin-top: 10px; margin-bottom: 20px;">
						<?php
							$js = "location.href='".base_url()."admin/statistics/friends/".$lottery->id."/".(!$interval ? $sel_range : ($sel_range*100))."/draws'";
							$attr = array(
								'onClick' 	=> "$js", 
								'class'		=> "form-check-input"
							);
						$extra = array('for' => 'extra_draw_lb');
							echo form_checkbox('extra_draws', '1', set_checkbox('extra_draws', '1', (!empty($lottery->extra_draws))), $attr);
							echo form_label('Extra Draw(s) Included?', 'extra_draw_lb', $extra); 
						?>
						</div>
						
						<!-- Last Draw Tile -->
						<div style="background: #f8f9fa; border: 1px solid #adb5bd; border-radius: 6px; padding: 8px 12px; width: 80%; max-width: 100%;">
							<div style="font-weight: bold; color: #343a40; margin-bottom: 4px; font-size: 0.95em;">
								<i class="fa fa-calendar" aria-hidden="true"></i> Last Draw
							</div>
							<div style="color:#555; font-size:0.85em; margin-bottom:4px;">
								<?php if(!empty($lottery->last_drawn['draw_date'])): ?>
									<?=date('D, M j, Y', strtotime(str_replace('/', '-', $lottery->last_drawn['draw_date'])));?>
								<?php else: ?>
									<span class="text-muted">N/A</span>
								<?php endif; ?>
							</div>
							<div style="display:flex; flex-wrap:wrap; align-items:center; gap:3px;">
								<?php for($__i = 1; $__i <= $lottery->balls_drawn; $__i++): ?>
									<?php if(!empty($lottery->last_drawn['ball'.$__i])): ?>
									<span class="badge badge-dark" style="font-size:0.85em; padding:4px 5px;"><?=(int)$lottery->last_drawn['ball'.$__i];?></span>
									<?php endif; ?>
								<?php endfor; ?>
								<?php if($lottery->extra_ball && !empty($lottery->last_drawn['extra'])): ?>
									<span style="margin:0 2px; color:#888; font-size:0.85em;">+</span>
									<span class="badge badge-secondary" style="font-size:0.85em; padding:4px 5px;"><?=(int)$lottery->last_drawn['extra'];?></span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>