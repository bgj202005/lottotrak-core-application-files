<style>
	.card {
		background-color: #ffffff;
		border: 1px solid rgba(0,34,51,.1);
		box-shadow: 2px 4px 10px 0 rgba(0,34,51,.05);
		border-radius: .15rem;
	}
	.card-title { color: #000000; }
	.card-text  { color: steelblue; }
	.h1,.h2,.h3,.h4,.h5,.h6,h1,h2,h3,h4,h5,h6 { color: #000000; }
	/* Ball badge */
	.ball-num {
		display: inline-block;
		width: 32px; height: 32px; line-height: 32px;
		border-radius: 50%;
		background: #343a40; color: #fff;
		font-weight: bold; font-size: .9em;
		text-align: center;
	}
	/* Expand cursor on main rows */
	tr.hwcf-row { cursor: pointer; }
	tr.hwcf-row:hover td { background-color: rgba(0,123,255,.07) !important; }
	/* Detail sub-table */
	.detail-wrap td { padding: 0 !important; }
	.detail-inner { background: #f8f9fa; font-size: .875em; }
	.detail-inner thead th { font-size: .8em; background: #e9ecef; }
	/* Prevent header wrapping */
	#hwcf-table thead th,
	#hwcf-extra-table thead th { white-space: nowrap; font-size: .78em; }
	/* Legend swatches */
	.swatch { display:inline-block; width:14px; height:14px; border-radius:2px; vertical-align:middle; margin-right:3px; }
	.swatch-green  { background:#28a745; }
	.swatch-yellow { background:#ffc107; }
	.swatch-blue   { background:#17a2b8; }
	.swatch-none   { background:#dee2e6; border:1px solid #adb5bd; }
</style>

<h2><?php echo 'H-W-C + Follower Analysis: ' . $lottery->lottery_name; ?></h2>
<h5 style="text-align:left"><?php echo anchor('admin/history', 'Back to History Win Dashboard', 'title="Back to Win History"'); ?></h5>

<?php if (isset($message) && $message): ?>
	<div class="alert alert-warning"><?= $message ?></div>
<?php endif; ?>

<section>
<div class="container">
<div class="row">
<div class="col-12">
<div class="card mt-3 p-4">

<div class="container-fluid" style="margin-top:15px;">

	<!-- Info row -->
	<div class="row mb-3">
		<div class="col-md-2 col-sm-6 mb-2">
			<div class="card text-center h-100">
				<div class="card-body py-3">
					<h6 class="card-title text-muted mb-1">Analysis Range</h6>
					<h3 class="text-primary mb-0"><?= $lottery->last_drawn['range'] ?> Draws</h3>
				</div>
			</div>
		</div>
		<div class="col-md-3 col-sm-6 mb-2">
			<div class="card text-center h-100">
				<div class="card-body py-3">
					<h5 class="card-title text-muted mb-1"><strong>Last Draw</strong></h5>
					<div class="mb-2 text-muted" style="font-size:1em;"><?php echo date('l, M-d-Y', strtotime(str_replace('/', '-', $lottery->last_drawn['draw_date']))); ?></div>
					<div style="display:flex; flex-wrap:nowrap; justify-content:center; align-items:center; gap:3px; margin-top:6px;">
				<?php for ($__i = 1; $__i <= $lottery->balls_drawn; $__i++): ?><span class="badge badge-dark" style="font-size:clamp(0.6em,1.8vw,0.95em); padding:4px 5px;"><?= $lottery->last_drawn['ball'.$__i] ?></span><?php endfor; ?><?php if (!empty($lottery->last_drawn['extra'])): ?><span class="badge badge-secondary" style="font-size:clamp(0.6em,1.8vw,0.95em); padding:4px 5px;">+&nbsp;<?= $lottery->last_drawn['extra'] ?></span><?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-2 col-sm-6 mb-2">
			<div class="card text-center h-100">
				<div class="card-body py-3">
					<h6 class="card-title text-muted mb-1">H-W-C Distribution</h6>
					<div style="display:flex; flex-wrap:nowrap; justify-content:center; align-items:center; gap:3px; margin-top:6px;">
					<span class="badge badge-danger" style="font-size:clamp(0.6em,1.8vw,0.95em); padding:4px 5px;">H: <?= $lottery->H ?></span><span class="badge badge-warning text-dark" style="font-size:clamp(0.6em,1.8vw,0.95em); padding:4px 5px;">W: <?= $lottery->W ?></span><span class="badge badge-primary" style="font-size:clamp(0.6em,1.8vw,0.95em); padding:4px 5px;">C: <?= $lottery->C ?></span>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-2 col-sm-6 mb-2">
			<div class="card h-100">
				<div class="card-body py-3">
					<h6 class="card-title text-muted mb-2">Settings</h6>
					<div class="mb-2">
						<small class="text-muted">Extra Ball Included?</small><br>
						<span class="badge badge-<?= $lottery->extra_included ? 'success' : 'secondary' ?> p-2">
							<?= $lottery->extra_included ? 'YES' : 'NO' ?>
						</span>
					</div>
					<div>
						<small class="text-muted">Extra Draws Included?</small><br>
						<span class="badge badge-<?= $lottery->extra_draws ? 'success' : 'secondary' ?> p-2">
							<?= $lottery->extra_draws ? 'YES' : 'NO' ?>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3 col-sm-6 mb-2">
			<div class="card h-100">
				<div class="card-body py-3">
					<h6 class="card-title text-muted mb-1">How to Read</h6>
					<p class="card-text small mb-0">
						Each ball shows the H-W-C pattern that produced the <strong>highest average follower hits</strong>
						in the <em>next</em> actual draw. Followers computed dynamically from draw history (min. 3&times;).
						Click any row to see all patterns for that ball.
					</p>
				</div>
			</div>
		</div>
	</div><!-- /row -->

	<?php if (empty($hwc_follower_results)): ?>
		<div class="alert alert-info">
			No results found. Ensure H-W-C statistics have been calculated and there are at least 2 draws in the configured range.
		</div>
	<?php else: ?>
	<?php if (!empty($is_dup_extra)): ?>
		<div class="alert alert-info mb-2"><strong>Separate pool lottery detected.</strong> Main balls (1&ndash;<?= intval($lottery->maximum_ball) ?>) and extra balls (1&ndash;<?= intval($max_extra_ball) ?>) are analysed in independent tables below.</div>
	<?php endif; ?>

	<!-- Legend -->
	<div class="mb-2 small">
		<span class="swatch swatch-green"></span>&nbsp;Times &ge; 10 &nbsp;&nbsp;
		<span class="swatch swatch-yellow"></span>&nbsp;Times &ge; 5 &nbsp;&nbsp;
		<span class="swatch swatch-blue"></span>&nbsp;Times &ge; 2 &nbsp;&nbsp;
		<span class="swatch swatch-none"></span>&nbsp;Times = 1 &nbsp;&nbsp;
		<span class="text-muted ml-3"><i class="fa fa-hand-o-up"></i> Click any row to expand all H-W-C patterns for that ball</span>
	</div>

	<div class="table-responsive">
	<table class="table table-hover table-sm table-bordered" id="hwcf-table">
		<thead class="thead-dark">
			<tr>
				<th class="text-center" style="width:50px;">Rank</th>
				<th class="text-center" style="width:60px;">Ball</th>
				<th class="text-center">Occurrences</th>
				<th class="text-center">Total Points</th>
				<th class="text-center">Best H-W-C Pattern</th>
				<th class="text-center">Times Occurred</th>
				<th class="text-center">Total Followers</th>
				<th class="text-center">Follower Hits</th>
				<th class="text-center">Total Non-Followers</th>
				<th class="text-center">Non-Follower Hits</th>
				<th class="text-center">Best in 1 Draw</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$rank = 1;
		foreach ($last_drawn_balls as $_drawn_ball):
			if (!isset($hwc_follower_results[$_drawn_ball])) continue;
			$result = $hwc_follower_results[$_drawn_ball];
			$_ball_pts    = isset($ball_points[$_drawn_ball]) ? $ball_points[$_drawn_ball] : 0;
			$_is_xball    = (!empty($is_dup_extra) === false) && ($last_drawn_extra_ball > 0) && ($_drawn_ball === $last_drawn_extra_ball);
			$_ball_style  = $_is_xball ? 'background:#6c757d;' : '';

			$has_detail = count($result['all_patterns']) > 1;
			$detail_id  = 'detail-' . $result['ball'];
		?>
		<!-- Main row -->
		<tr class="hwcf-row"
			<?php if ($has_detail): ?>
				data-toggle="collapse"
				data-target="#<?= $detail_id ?>"
				aria-expanded="false"
				aria-controls="<?= $detail_id ?>"
			<?php endif; ?>>
			<td class="text-center align-middle"><?= $rank++ ?></td>
			<td class="text-center align-middle">
				<span class="ball-num" <?= $_ball_style ? 'style="'.$_ball_style.'"' : '' ?>><?= $result['ball'] ?></span>
			</td>
			<td class="text-center align-middle"><?= isset($hwc_scores[$_drawn_ball]) ? $hwc_scores[$_drawn_ball] : 0 ?></td>
			<td class="text-center align-middle"><?= $_ball_pts ?></td>
			<td class="text-center align-middle">
				<?php if ($result['best_pattern'] !== '-' && strpos($result['best_pattern'], '-') !== false):
					$pp = explode('-', $result['best_pattern']); ?>
					<span class="badge badge-danger"><?= $pp[0] ?>H</span>
					<span class="badge badge-warning text-dark"><?= $pp[1] ?>W</span>
					<span class="badge badge-primary"><?= $pp[2] ?>C</span>
				<?php else: ?>
					<span class="text-muted">—</span>
				<?php endif; ?>
				<?php if ($has_detail): ?>
					<small class="text-muted ml-1"><i class="fa fa-chevron-down"></i></small>
				<?php endif; ?>
			</td>
			<td class="text-center align-middle"><?= $result['best_times'] ?></td>
			<td class="text-center align-middle"><?= $result['follower_count'] ?></td>
			<td class="text-center align-middle"><?= $result['best_hits'] ?></td>
			<td class="text-center align-middle"><?= intval($lottery->maximum_ball) - $result['follower_count'] ?></td>
			<td class="text-center align-middle"><?= $result['best_non_hits'] ?></td>
			<td class="text-center align-middle"><?= $result['best_max'] ?></td>
		</tr>

		<!-- Expandable detail row (all patterns for this ball) -->
		<?php if ($has_detail): ?>
		<tr class="detail-wrap">
			<td colspan="11">
				<div class="collapse" id="<?= $detail_id ?>">
					<table class="table table-sm table-bordered detail-inner mb-0">
						<thead>
							<tr>
								<th class="text-center">H-W-C Pattern</th>
								<th class="text-center">Times Occurred</th>
								<th class="text-center">Follower Hits</th>
								<th class="text-center">Non-Follower Hits</th>
								<th class="text-center">Best in 1 Draw</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($result['all_patterns'] as $pattern => $ps):
							$is_best = ($pattern === $result['best_pattern']);
							$pp2 = explode('-', $pattern);
						?>
						<tr <?= $is_best ? 'class="font-weight-bold"' : '' ?>>
							<td class="text-center">
								<span class="badge badge-danger"><?= $pp2[0] ?>H</span>
								<span class="badge badge-warning text-dark"><?= $pp2[1] ?>W</span>
								<span class="badge badge-primary"><?= $pp2[2] ?>C</span>
								<?php if ($is_best): ?><span class="badge badge-success ml-1">Best</span><?php endif; ?>
							</td>
							<td class="text-center"><?= $ps['times'] ?></td>
							<td class="text-center"><?= $ps['total_hits'] ?></td>
						<td class="text-center"><?= $ps['non_follower_hits'] ?></td>
							<td class="text-center"><?= $ps['max_hits'] ?></td>
						</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
		<?php endif; ?>

		<?php endforeach; ?>
		</tbody>
	</table>
	</div><!-- /table-responsive -->

	<?php endif; ?>

	<?php if (!empty($hwc_extra_results)): ?>
	<div class="mt-4">
		<h4 class="mb-1">Extra Ball Analysis &mdash; pool 1&ndash;<?= intval($max_extra_ball) ?></h4>
		<p class="text-muted small mb-2">The extra ball is drawn from its own independent pool. Follower analysis is computed within that pool only. H-W-C pattern column shows which main-ball pattern was active when the extra ball was drawn.</p>
		<!-- Legend -->
		<div class="mb-2 small">
			<span class="swatch swatch-green"></span>&nbsp;Times &ge; 10 &nbsp;&nbsp;
			<span class="swatch swatch-yellow"></span>&nbsp;Times &ge; 5 &nbsp;&nbsp;
			<span class="swatch swatch-blue"></span>&nbsp;Times &ge; 2 &nbsp;&nbsp;
			<span class="swatch swatch-none"></span>&nbsp;Times = 1 &nbsp;&nbsp;
			<span class="text-muted ml-3"><i class="fa fa-hand-o-up"></i> Click any row to expand all H-W-C patterns for that ball</span>
		</div>
		<div class="table-responsive">
		<table class="table table-hover table-sm table-bordered" id="hwcf-extra-table">
			<thead class="thead-dark">
				<tr>
					<th class="text-center" style="width:50px;">Rank</th>
					<th class="text-center" style="width:60px;">Ball</th>
					<th class="text-center">Occurrences</th>
					<th class="text-center">Times Drawn</th>
					<th class="text-center">Best H-W-C Pattern</th>
					<th class="text-center">Times Occurred</th>
					<th class="text-center">Total Followers</th>
					<th class="text-center">Follower Hits</th>
					<th class="text-center">Total Non-Followers</th>
					<th class="text-center">Non-Follower Hits</th>
					<th class="text-center">Best in 1 Draw</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$rank = 1;
			$_xball = isset($last_drawn_extra_ball) ? intval($last_drawn_extra_ball) : 0;
			foreach ($hwc_extra_results as $result):
				if ($_xball && $result['ball'] !== $_xball) continue;
				$row_class = '';
				if     ($result['best_times'] >= 10) $row_class = 'table-success';
				elseif ($result['best_times'] >= 5)  $row_class = 'table-warning';
				elseif ($result['best_times'] >= 2)  $row_class = 'table-info';
				$has_detail = count($result['all_patterns']) > 1;
				$detail_id  = 'xdetail-' . $result['ball'];
			?>
			<tr class="hwcf-row <?= $row_class ?>"
				<?php if ($has_detail): ?>
					data-toggle="collapse"
					data-target="#<?= $detail_id ?>"
					aria-expanded="false"
					aria-controls="<?= $detail_id ?>"
				<?php endif; ?>>
				<td class="text-center align-middle"><?= $rank++ ?></td>
				<td class="text-center align-middle">
					<span class="ball-num xball-num" style="background:#6c757d;"><?= $result['ball'] ?></span>
				</td>
				<td class="text-center align-middle"><?= isset($hwc_scores[$result['ball']]) ? $hwc_scores[$result['ball']] : 0 ?></td>
				<td class="text-center align-middle"><?= $result['times_drawn'] ?></td>
				<td class="text-center align-middle">
					<?php if ($result['best_pattern'] !== '-' && strpos($result['best_pattern'], '-') !== false):
						$pp = explode('-', $result['best_pattern']); ?>
						<span class="badge badge-danger"><?= $pp[0] ?>H</span>
						<span class="badge badge-warning text-dark"><?= $pp[1] ?>W</span>
						<span class="badge badge-primary"><?= $pp[2] ?>C</span>
					<?php else: ?>
						<span class="text-muted">&mdash;</span>
					<?php endif; ?>
					<?php if ($has_detail): ?>
						<small class="text-muted ml-1"><i class="fa fa-chevron-down"></i></small>
					<?php endif; ?>
				</td>
				<td class="text-center align-middle"><?= $result['best_times'] ?></td>
				<td class="text-center align-middle"><?= $result['follower_count'] ?></td>
				<td class="text-center align-middle"><?= $result['best_hits'] ?></td>
				<td class="text-center align-middle"><?= intval($max_extra_ball) - $result['follower_count'] ?></td>
				<td class="text-center align-middle"><?= $result['best_non_hits'] ?></td>
				<td class="text-center align-middle"><?= $result['best_max'] ?></td>
			</tr>
			<?php if ($has_detail): ?>
			<tr class="detail-wrap">
				<td colspan="11">
					<div class="collapse" id="<?= $detail_id ?>">
						<table class="table table-sm table-bordered detail-inner mb-0">
							<thead>
								<tr>
									<th class="text-center">H-W-C Pattern</th>
									<th class="text-center">Times Occurred</th>
									<th class="text-center">Follower Hits</th>
									<th class="text-center">Non-Follower Hits</th>
									<th class="text-center">Best in 1 Draw</th>
								</tr>
							</thead>
							<tbody>
							<?php foreach ($result['all_patterns'] as $pattern => $ps):
								$is_best = ($pattern === $result['best_pattern']);
								$pp2 = explode('-', $pattern);
							?>
							<tr <?= $is_best ? 'class="font-weight-bold"' : '' ?>>
								<td class="text-center">
									<span class="badge badge-danger"><?= $pp2[0] ?>H</span>
									<span class="badge badge-warning text-dark"><?= $pp2[1] ?>W</span>
									<span class="badge badge-primary"><?= $pp2[2] ?>C</span>
									<?php if ($is_best): ?><span class="badge badge-success ml-1">Best</span><?php endif; ?>
								</td>
								<td class="text-center"><?= $ps['times'] ?></td>
								<td class="text-center"><?= $ps['total_hits'] ?></td>
								<td class="text-center"><?= $ps['non_follower_hits'] ?></td>
								<td class="text-center"><?= $ps['max_hits'] ?></td>
							</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</td>
			</tr>
			<?php endif; ?>
			<?php endforeach; ?>
			</tbody>
		</table>
		</div><!-- /table-responsive -->
	</div>
	<?php endif; ?>

	<!-- H-W-C + Followers Previous Predicted Winners -->
	<?php if(!empty($prev_hwc_followers)):
		$_prev_ball_nums = array_filter(array_map('trim', explode(',', $prev_hwc_followers)), 'strlen');
		$_extra_ball_val = ($lottery->extra_ball && isset($lottery->last_drawn['extra'])) ? (string)intval($lottery->last_drawn['extra']) : '';
		$_main_winning   = array();
		for ($_wi = 1; $_wi <= $lottery->balls_drawn; $_wi++) {
			if (!empty($lottery->last_drawn['ball'.$_wi])) {
				$_main_winning[] = (string)intval($lottery->last_drawn['ball'.$_wi]);
			}
		}
		if($hwcf_saved_follower_type === 'position') {
			$_fl_sub = 'Position ' . htmlspecialchars($hwcf_saved_position_points);
		} else {
			$_fl_sub = !empty($hwcf_saved_ball_points) ? 'After Ball ' . htmlspecialchars($hwcf_saved_ball_points) : '';
		}
	?>
	<div class="mt-4" style="padding:14px 18px; background-color:#e8f5e9; border-left:4px solid #28a745; border-radius:4px; text-align:center;">
		<strong>H-W-C + Followers Previous Predicted Winners</strong>
		<span class="text-muted" style="font-size:0.85em; margin-left:8px;">(<?=htmlspecialchars($hwcf_saved_h_w_c_group);?><?=($_fl_sub ? ' &mdash; ' . $_fl_sub : '');?>)</span>
		<div style="margin-top:10px; display:flex; flex-wrap:wrap; justify-content:center; gap:8px;">
			<?php foreach($_prev_ball_nums as $_pball):
				$_pball    = (string)trim($_pball);
				$_is_bonus = ($_extra_ball_val !== '' && $_pball === $_extra_ball_val);
				$_is_main  = in_array($_pball, $_main_winning);
				if($_is_bonus):
					$_bg = '#1565C0'; $_color = '#fff'; $_border = 'border:2px solid #0d47a1;'; $_title = 'Bonus/Extra Ball!';
				elseif($_is_main):
					$_bg = '#FFD700'; $_color = '#333'; $_border = 'border:2px solid #b8860b;'; $_title = 'Winner!';
				else:
					$_bg = '#28a745'; $_color = '#fff'; $_border = ''; $_title = '';
				endif;
			?>
			<div title="<?=$_title;?>" style="display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:50%; background-color:<?=$_bg;?>; color:<?=$_color;?>; font-weight:bold; font-size:14px; box-shadow:0 2px 4px rgba(0,0,0,0.25); <?=$_border;?>"><?=$_pball;?></div>
			<?php endforeach; ?>
		</div>
		<p style="margin-top:8px; font-size:0.85em; color:#555;">
			<span style="display:inline-block; width:14px; height:14px; background:#FFD700; border-radius:50%; border:1px solid #b8860b; vertical-align:middle;"></span> Gold = main ball match &nbsp;
			<?php if($lottery->extra_ball): ?>
			<span style="display:inline-block; width:14px; height:14px; background:#1565C0; border-radius:50%; border:1px solid #0d47a1; vertical-align:middle;"></span> Blue = bonus/extra ball match &nbsp;
			<?php endif; ?>
			<span style="display:inline-block; width:14px; height:14px; background:#28a745; border-radius:50%; vertical-align:middle;"></span> Green = not drawn
		</p>
	</div>
	<?php endif; ?>

	<!-- H-W-C + Followers Win Statistics -->
	<?php if(isset($hwcf_win_stats)): ?>
	<?php
	// Build ordered prize columns (highest to lowest) filtered by valid categories
	$hwcf_prize_cols = array();
	$valid_cats = isset($lottery->valid_prize_categories) ? $lottery->valid_prize_categories : array();
	$balls_drawn = isset($lottery->balls_drawn) ? intval($lottery->balls_drawn) : 9;
	$has_extra = !empty($lottery->extra_ball);
	
	// Start from balls_drawn down to 1, interleaving extra ball prizes by prize hierarchy
	for($i = $balls_drawn; $i >= 1; $i--) {
		$col = $i.'_win';
		// Add current number without extra
		if(in_array($col, $valid_cats)) {
			$hwcf_prize_cols[] = array('key' => $col, 'label' => $i, 'title' => $i.' Number'.($i > 1 ? 's' : ''));
		}
		// Add next lower number WITH extra (higher prize than next lower without extra)
		if($i > 1 && $has_extra) {
			$col_lower_extra = ($i-1).'_win_extra';
			if(in_array($col_lower_extra, $valid_cats)) {
				$hwcf_prize_cols[] = array('key' => $col_lower_extra, 'label' => ($i-1).'+', 'title' => ($i-1).' Number'.($i > 2 ? 's' : '').' + Extra');
			}
		}
	}
	// Add 1 number alone if not already added (when i=1 in loop, we don't add 0+)
	// Note: It's already added in the loop when i=1
	// Add extra-only at the end (lowest prize)
	if($has_extra && in_array('extra', $valid_cats)) {
		$hwcf_prize_cols[] = array('key' => 'extra', 'label' => '+', 'title' => 'Extra Ball Only');
	}
	
	// Calculate draw count
	$draw_count = 0;
	if($hwcf_win_stats['startdate'] && $hwcf_win_stats['lastdate']) {
		$start = new DateTime($hwcf_win_stats['startdate']);
		$end = new DateTime($hwcf_win_stats['lastdate']);
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
		.hwcf-win-stats-table { font-size: 0.80em; table-layout: fixed; width: 100%; margin-bottom: 0; background-color: white; }
		.hwcf-win-stats-table th { font-size: 0.80em; font-weight: bold; white-space: nowrap; padding: 0.3rem 0.2rem; text-align: center; }
		.hwcf-win-stats-table td { padding: 0.25rem 0.2rem; text-align: center; white-space: nowrap; }
		.hwcf-win-record-col { width: 22px !important; font-size: 0.80em; white-space: nowrap; }
		.hwcf-win-total-col { width: 46px !important; font-size: 0.80em; white-space: nowrap; }
		@media (max-width: 992px) {
			.hwcf-win-stats-table { font-size: 0.74em; table-layout: auto; width: auto; min-width: 600px; }
			.hwcf-win-stats-table th { font-size: 0.74em; padding: 0.25rem 0.15rem; }
			.hwcf-win-stats-table td { padding: 0.2rem 0.15rem; }
			.hwcf-win-record-col { width: 20px !important; font-size: 0.74em; }
			.hwcf-win-total-col { width: 42px !important; font-size: 0.74em; }
		}
		@media (max-width: 576px) {
			.hwcf-win-stats-table { font-size: 0.68em; min-width: 500px; }
			.hwcf-win-stats-table th { font-size: 0.68em; padding: 0.2rem 0.1rem; }
			.hwcf-win-stats-table td { padding: 0.18rem 0.1rem; }
			.hwcf-win-record-col { width: 18px !important; font-size: 0.68em; }
			.hwcf-win-total-col { width: 38px !important; font-size: 0.68em; }
		}
	</style>
	<div style="margin: 20px 0; padding: 18px; background-color: #f3e5f5; border-left: 4px solid #7b1fa2; border-radius: 4px;">
		<div style="text-align: center; margin-bottom: 15px;">
			<strong style="color: #000000; font-size: 1.1em;">
				<i class="fa fa-trophy"></i> H-W-C + Followers Prediction Win Records
			</strong>
		</div>
		<div style="margin-bottom: 10px; text-align: center; font-size: 0.9em; color: #666;">
			<?php if($hwcf_win_stats['startdate'] || $hwcf_win_stats['lastdate']): ?>
				<strong><?php echo ($hwcf_win_stats['lastdate'] || $hwcf_win_stats['total_winners'] > 0) ? 'Start:' : 'Starting Date:'; ?></strong> <?php echo $hwcf_win_stats['startdate'] ? date('l F j, Y', strtotime($hwcf_win_stats['startdate'])) : date('l F j, Y', strtotime($lottery->next_draw_date)); ?>
				&nbsp;&nbsp;|
				<?php if($hwcf_win_stats['lastdate']): ?>
					<strong>Last Draw Date:</strong> <?php echo date('l F j, Y', strtotime($hwcf_win_stats['lastdate'])); ?> (<?php echo $draw_count; ?> draw<?php echo $draw_count != 1 ? 's' : ''; ?>)
				<?php else: ?>
					<strong>Last Draw Date:</strong> None yet
				<?php endif; ?>
			<?php else: ?>
				<strong>Starting Date:</strong> <?php echo date('l F j, Y', strtotime($lottery->next_draw_date)); ?> &nbsp;|&nbsp; <strong>Last Draw Date:</strong> None yet
			<?php endif; ?>
			<button type="button" class="btn btn-sm btn-danger" onclick="resetHWCFWinStats(<?php echo $lottery->id; ?>)" 
				style="margin-left: 15px;">
				<i class="fa fa-undo"></i> Reset
			</button>
		</div>
		<div class="table-responsive" style="overflow-x: auto;">
			<table class="table table-bordered table-striped hwcf-win-stats-table">
				<thead>
					<tr>
						<th colspan="<?php echo count($hwcf_prize_cols) + 1; ?>" class="text-center" style="background-color: #f4f4f4;">
							<strong>Win Record</strong>
						</th>
					</tr>
					<tr>
						<?php foreach($hwcf_prize_cols as $col): ?>
							<th class="text-center hwcf-win-record-col" style="background-color: #e8f5e8;" title="<?php echo htmlspecialchars($col['title']); ?>"><?php echo htmlspecialchars($col['label']); ?></th>
						<?php endforeach; ?>
						<th class="text-center hwcf-win-total-col" style="background-color: #d4edda; font-weight: bold;" title="Total Winners">Total</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<?php foreach($hwcf_prize_cols as $col): ?>
							<td class="text-center"><?php echo number_format($hwcf_win_stats[$col['key']]); ?></td>
						<?php endforeach; ?>
						<td class="text-center" style="background-color: #e1bee7; font-weight: bold;"><?php echo number_format($hwcf_win_stats['total_winners']); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php endif; ?>

</div><!-- /container-fluid -->

</div><!-- /card -->
</div><!-- /col-12 -->
</div><!-- /row -->
</div><!-- /container -->
</section>

<script>
// Reset H-W-C + Followers Win Statistics
function resetHWCFWinStats(lotteryId) {
	if(confirm('WARNING: This will clear all H-W-C + Followers prediction win records and reset the statistics.\n\nThe new start date will be set to the next draw date.\n\nAre you sure you want to continue?')) {
		$.ajax({
			url: '<?php echo site_url("admin/history/reset_hwcf_win_stats"); ?>',
			type: 'POST',
			data: { lottery_id: lotteryId },
			dataType: 'json',
			success: function(response) {
				if(response.success) {
					alert(response.message || 'H-W-C + Followers win statistics reset successfully');
					location.reload();
				} else {
					alert(response.message || 'Error resetting win statistics. Please try again.');
				}
			},
			error: function(xhr, status, error) {
				console.log('AJAX Error:', status, error);
				console.log('Response:', xhr.responseText);
				alert('Error resetting win statistics. Check console for details.\n\nStatus: ' + status + '\nError: ' + error);
			}
		});
	}
}

$(document).ready(function() {
	// Rotate chevron icon when row expands/collapses
	$('.hwcf-row').on('click', function() {
		var icon = $(this).find('.fa-chevron-down, .fa-chevron-up');
		if (icon.length) {
			icon.toggleClass('fa-chevron-down fa-chevron-up');
		}
	});
});
</script>
