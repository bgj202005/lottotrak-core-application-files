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

								<div style = "color: #000000;" id="ball-<?=$b;?>">
										This is Ball <strong><?=$b;?></strong>. It's closest friend is Ball <strong><?php echo $lottery->friend['ball'.$b].'</strong>, being drawn <strong>'.$lottery->friend['count'.$b].
										'</strong> times. <br />The last time it was drawn was on <strong>'.date("D, M-d-Y",strtotime(str_replace('/',' - ',$lottery->friend['date'.$b]))).'</strong>'; ?>
								</div>
							</div>
							<?php $b++;
							}
							while ($b<=$max); ?>
							<p class="card-text">These are the numbers that have the highest probability of being drawn for the next draw.</p>  	
						</div>
					</div>
				</div>
				<div class="col-4">
					<div class="dropdown" style = "margin-left: 50px;">
						<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Draw Range
						</button>
						<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
							<?php $interval = (integer) $lottery->last_drawn['interval'];
							if(!$interval) : ?>
								<a class="dropdown-item active" href="<?=base_url('admin/statistics/friends/'.$lottery->id)?>">All Draws (<?=$lottery->last_drawn['range'];?>) </a>
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
				</div>
			</div>
		</div>
	</section>