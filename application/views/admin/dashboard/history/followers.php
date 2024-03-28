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
										<div class="bg-white">
											<div class="p-4">
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
						<div class="tab-content" id="myTabContent">
							<?php $b = 1;			   
							$cd = intval($max);						  // This is the maximum ball drawn without an extra ball
							if($lottery->extra_included):  $max++; endif; // Include the extra ball
							do
							{ ?> 
							<div class="tab-pane fade p-3 <?php if($b==1) echo 'show active'; ?>" id="ball<?=$b?>" role="tabpanel" aria-labelledby="tab-<?=$b;?>">
								<?php $dup_or_not = ($lottery->duplicate_extra_ball ? $lottery->last_drawn['extra'].'x' : $lottery->last_drawn['extra']);
									if(!array_key_exists(($lottery->last_drawn['ball'.$b]), $lottery->last_drawn)):
										/* difference is when any of the regular balls match the duplicate extra ball **/
										if(isset($lottery->last_drawn[$dup_or_not])):
											$xtr = (($lottery->duplicate_extra_ball&&$lottery->extra_included) ? $lottery->last_drawn[$lottery->last_drawn['extra'].'x'] : $lottery->last_drawn[$lottery->last_drawn['extra']]);
										else:
											$xtr = "0|0";
										endif;
										//$trailer = explode('|', ($b>$cd ? $xtr : $lottery->last_drawn[$lottery->last_drawn['ball'.$b]])); ?>
 										<h5 class="card-title">After Ball <?=($b>$cd ? $lottery->last_drawn['extra'] : $lottery->last_drawn['ball'.$b]);?> has been drawn in <?=$lottery->last_drawn['range']; ?> draws.</h5>
										<?php $d_wins = array(); 
											$wins = $lottery->last_drawn['wins'.$b];
											$placement = explode(',', $wins);
											$count = 1;
											foreach($placement as $p):  
												$p_wins += array(
														'wins '.$count => $p
												);
												$count++;
											endforeach;
										foreach($p_wins as $winner => $total)
										{
											$s_picks = '<strong>'.$winner.'</strong> had '.total.' wins';
											echo "<p class='card-text'> ".$s_picks."</p>";
										}
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