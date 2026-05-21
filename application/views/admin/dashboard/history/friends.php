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
	/* friendtype table */
	table.friendtype{
 		border:1px solid black;
  		display:inline-block;
		max-width: 221px;
		margin:20px;
	}
	/* nonfriendtype table */
	table.nonfriendtype{
 		border:1px solid black;
  		display:inline-block;
		max-width: 280px;
		margin:20px;
	}
	/* Previous Draw Friends styles */
	.prev-friends-ball {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 44px;
		height: 44px;
		border-radius: 50%;
		font-weight: bold;
		font-size: 14px;
		box-shadow: 0 2px 4px rgba(0,0,0,0.25);
		margin: 4px;
	}
	.prev-friends-ball.two-way {
		background-color: #FFD700;
		color: #333;
		border: 2px solid #b8860b;
	}
	.prev-friends-ball.one-way {
		background-color: #1565C0;
		color: #fff;
		border: 2px solid #0d47a1;
	}
	.prev-friends-ball.no-friend {
		background-color: #6c757d;
		color: #fff;
		border: 2px solid #495057;
	}
	.prev-friends-ball.extra-ball {
		border-style: dashed;
	}
	.friends-legend-dot {
		display: inline-block;
		width: 16px;
		height: 16px;
		border-radius: 50%;
		vertical-align: middle;
		margin-right: 4px;
	}
	/* directions table */
	table.directions{
 		border:1px solid black;
  		display:inline-block;
		max-width: 203px;
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
	.last-draws {
		margin-top:3em;
		text-align: center;
	}
	.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    color: #000000;
	}	
.shadow-sm {
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
	}
.row-striped:nth-of-type(odd){
  background-color: #efefef;
}
.row-striped:nth-of-type(even){
  background-color: #ffffff;
}
</style>
	<h2><?php echo 'View Friends for: '.$lottery->lottery_name; ?></h2>	
	<?php $max = $lottery->maximum_ball; 
	   $b = 1; ?>
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
						<div class="container" id="content" style = "margin:20px;">
								<div class = "row justify-content-center">
									<table class="table friendtype">
										<thead>
											<tr>
												<th class="text-center" colspan="2">Friendship Occurrences</th>
											</tr>
											<tr>
												<th class="text-center">Draws</th>
												<th class="text-center">Friendship Type</th>
											</tr>
										</thead>
										<tbody>
											<?php echo "<tr class='table-light'>"; 
											echo "<tr class='table-light row-stripped'>";
											echo "<td class='text-center'>".$lottery->friend['nofriends']."</td>";
											echo "<td class='text-center'>No Friends</td></tr>";
											echo "<td class='text-center'>".$lottery->friend['1-way']."</td>";
											echo "<td class='text-center'>1-Way Friend</td></tr>";
											echo "<td class='text-center'>".$lottery->friend['2-way']."</td>";
											echo "<td class='text-center'>2-Way Friends</td></tr>"; ?>
										</tbody>
									</table>
								
									<!-- <table class="table nonfriendtype">
										<thead>
											<tr>
												<th class="text-center" colspan="2">Non-Friendship Occurrences</th>
											</tr>
											<tr>
												<th class="text-center">Draws</th>
												<th class="text-center">Non-Friendships Drawn</th>
											</tr>
										</thead>
										<tbody>
											<?php //echo "<tr class='table-light'>"; 
											/* echo "<tr class='table-light'>";
											echo "<td class='text-center'>".$lottery->friend['0-friends']."</td>";
											echo "<td class='text-center'>No Non-friends Drawn</td></tr>";
											echo "<td class='text-center'>".$lottery->friend['1-friends']."</td>";
											echo "<td class='text-center'>1 Non-friend Drawn</td></tr>";
											echo "<td class='text-center'>".$lottery->friend['2-friends']."</td>";
											echo "<td class='text-center'>2 Non-friends Drawn</td></tr>";
											echo "<td class='text-center'>".$lottery->friend['3-friends']."</td>";
											echo "<td class='text-center'>3 Non-Friend Drawn</td></tr>";
											echo "<td class='text-center'>".$lottery->friend['4-friends']."</td>";
											echo "<td class='text-center'>4 Non-Friends Drawn</td></tr>";
											*/ ?>  
										</tbody>
									</table> -->
									</div>
									<div class = "row justify-content-center">
									<?php $row = 1; // Pagenation row 
									$counter = 1;	// Countinuous counter
									do 
									{ ?>
									<table class="table directions">
										<thead>
											<tr>
												<th class="text-center" colspan="2">Friendship Directions</th>
											</tr>
											<tr>
												<th class="text-text-center">Ball Drawn</th>
												<th class="text-text-center">Direction</th>
											</tr>
										</thead>
										<tbody>
											<?php  
												for($ball = $row; $ball <= $max; $ball++):	
													if(isset($lottery->friend['ball_friend'.$counter]))
													{  echo "<tr class='row-striped'>";
													   echo "<td class='text-center'>".$ball."</td>";
													   $direction = ((substr($lottery->friend['ball_friend'.$counter],0,2)=='<>') ? '<i class="fa fa-arrows-h fa-2x" aria-hidden="true"></i> '
													   .ltrim($lottery->friend['ball_friend'.$counter],'<>') : 
													   '<i class="fa fa-long-arrow-right fa-2x" aria-hidden="true"></i> '.ltrim($lottery->friend['ball_friend'.$counter],">"));
													   echo "<td class='text-center'>".$direction."</td>";
													   echo "</tr>";
													}
													$counter++;
													if($counter==26):
														$row = $counter;
														break;
													elseif($counter==51) :
														$row = $counter;
														break;
													endif;
												endfor; ?>
										</tbody>
									</table>
									<?php } 
										while(($row<=$max)&&($counter<$max)); ?>
								</div>
							</div>
						</div>

						<?php
						// ============================================================
						// PREVIOUS DRAW FRIENDS — show all drawn balls coloured by friendship type
						// ============================================================
						$drawn_balls_list = array();
						$balls_drawn_count = (int) $lottery->balls_drawn;
						for ($pi = 1; $pi <= $balls_drawn_count; $pi++) {
							if (isset($lottery->last_drawn['ball'.$pi])) {
								$drawn_balls_list[] = array('num' => (int)$lottery->last_drawn['ball'.$pi], 'is_extra' => false);
							}
						}
						// Only include the extra ball when it shares the same pool as main balls.
						// For duplicate_extra_ball lotteries the extra ball is independent and
						// has no meaningful friendship relationship with the main balls.
						if ($lottery->extra_ball && !$lottery->duplicate_extra_ball && isset($lottery->last_drawn['extra']) && $lottery->last_drawn['extra'] > 0) {
							$drawn_balls_list[] = array('num' => (int)$lottery->last_drawn['extra'], 'is_extra' => true);
						}
						// Build a flat array of just the drawn ball numbers for easy lookup
						$drawn_nums_only = array_column($drawn_balls_list, 'num');
						?>
						<div style="margin: 30px 20px 20px 20px; padding: 20px; background-color: #f8f9fa; border: 2px solid #28a745; border-radius: 5px;">
							<h4 class="mb-3" style="color: #155724;">
								<strong>&#128101; Previous Draw Friends</strong>
								&mdash; <span style="font-size:0.85em; font-weight:normal;">
									<?=date("F j, Y", strtotime(str_replace('/', '-', $lottery->last_drawn['draw_date'])));?>
								</span>
							</h4>
							<p class="text-muted mb-3" style="font-size:0.9em;">
								Each drawn ball is highlighted based on its friendship type with its closest friend number.
							</p>
							<div style="display:flex; flex-wrap:wrap; justify-content:center; gap:12px; margin-bottom:20px;">
								<?php foreach($drawn_balls_list as $dball):
									$num      = $dball['num'];
									$is_extra = $dball['is_extra'];
									// Get the direction string stored for this ball number (e.g. "<>15" or ">23")
									$direction = isset($lottery->friend['ball_friend'.$num]) ? $lottery->friend['ball_friend'.$num] : '';
									// Determine the relationship type and extract the friend ball number
									if (substr($direction, 0, 2) === '<>') {
										$friend_ball  = ltrim($direction, '<>');
										$friend_type  = 'two-way';
									} elseif (substr($direction, 0, 1) === '>') {
										$friend_ball  = ltrim($direction, '>');
										$friend_type  = 'one-way';
									} else {
										$friend_ball  = '';
										$friend_type  = 'none';
									}
									// The friend ball MUST also be in the drawn numbers to count as 1-Way or 2-Way
									$friend_was_drawn = ($friend_ball !== '' && in_array((int)$friend_ball, $drawn_nums_only));
									if ($friend_type !== 'none' && $friend_was_drawn) {
										if ($friend_type === 'two-way') {
											$css_class  = 'two-way';
											$type_label = '2-Way Friend';
											$title      = '2-Way Friend with Ball ' . $friend_ball;
										} else {
											$css_class  = 'one-way';
											$type_label = '1-Way Friend';
											$title      = '1-Way Friend with Ball ' . $friend_ball;
										}
									} else {
										$css_class   = 'no-friend';
										$type_label  = 'No Friends';
										$friend_ball = '';
										$title       = 'No Friends';
									}
									if ($is_extra) $css_class .= ' extra-ball';
								?>
								<div style="text-align:center;">
									<div class="prev-friends-ball <?=$css_class;?>" title="<?=htmlspecialchars($title);?>">
										<?=$num;?>
									</div>
									<div style="font-size:0.72em; color:#555; margin-top:2px; max-width:52px;">
										<?=$type_label;?>
										<?php if($friend_ball): ?>
										<br><span style="color:#888;">&#8594; <?=$friend_ball;?></span>
										<?php endif; ?>
									</div>
								</div>
								<?php endforeach; ?>
							</div>

							<!-- Legend -->
							<div style="font-size:0.85em; color:#555; text-align:center; border-top:1px solid #dee2e6; padding-top:10px;">
								<span class="friends-legend-dot" style="background:#FFD700; border:2px solid #b8860b;"></span>Gold = 2-Way Friend &nbsp;&nbsp;
								<span class="friends-legend-dot" style="background:#1565C0; border:2px solid #0d47a1;"></span>Blue = 1-Way Friend &nbsp;&nbsp;
								<span class="friends-legend-dot" style="background:#6c757d; border:2px solid #495057;"></span>Grey = No Friends
								<?php if($lottery->extra_ball && !$lottery->duplicate_extra_ball): ?>
								&nbsp;&nbsp; <em>(Dashed border = Bonus/Extra Ball)</em>
								<?php endif; ?>
							</div>
						</div>

						<!-- How to Read This Page -->
						<div style="margin: 20px; padding: 15px 20px; background-color: #fff8e1; border-left: 4px solid #ffc107; border-radius: 4px;">
							<strong>How to Read This Page:</strong>
							<ul class="mb-0 mt-2">
								<li><strong>Friendship Directions Tab:</strong> Shows the closest friend ball for each ball number (1 to <?=$max;?>) and the direction of friendship.</li>
								<li><strong>Friendship Occurrences Table:</strong> Counts how many draws had No Friends, 1-Way Friends, or 2-Way Friends drawn together.</li>
								<li><strong>2-Way Friend <span class="friends-legend-dot" style="background:#FFD700; border:2px solid #b8860b; width:12px; height:12px;"></span>:</strong> Both balls consider each other close friends &mdash; a mutual pairing over the range.</li>
								<li><strong>1-Way Friend <span class="friends-legend-dot" style="background:#1565C0; border:2px solid #0d47a1; width:12px; height:12px;"></span>:</strong> One ball considers the other a close friend, but not vice versa.</li>
								<li><strong>No Friends <span class="friends-legend-dot" style="background:#6c757d; border:2px solid #495057; width:12px; height:12px;"></span>:</strong> No close friendship pairing found for this ball over the draw range.</li>
								<li><strong>Previous Draw Friends Section:</strong> Shows the friendship type for each ball drawn on <?=date("l, F j, Y", strtotime(str_replace('/', '-', $lottery->last_drawn['draw_date'])));?> — no clicking required.</li>
								<?php if($lottery->extra_ball && !$lottery->duplicate_extra_ball): ?>
								<li><strong>Dashed border:</strong> Indicates the Bonus/Extra Ball.</li>
								<?php endif; ?>
							</ul>
						</div>

					</div>
				</div>
			</div>
		</div>
	</section>