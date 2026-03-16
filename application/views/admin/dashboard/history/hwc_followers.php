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
				<?php for ($__i = 1; $__i <= $lottery->balls_drawn; $__i++): ?><span class="badge" style="background:#e67e00; color:#fff; font-size:clamp(0.6em,1.8vw,0.95em); padding:4px 5px;"><?= $lottery->last_drawn['ball'.$__i] ?></span><?php endfor; ?><?php if (!empty($lottery->last_drawn['extra'])): ?><span class="badge badge-secondary" style="font-size:clamp(0.6em,1.8vw,0.95em); padding:4px 5px;">+&nbsp;<?= $lottery->last_drawn['extra'] ?></span><?php endif; ?>
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
		foreach ($hwc_follower_results as $result):
			// Colour-code main row by best pattern occurrence count
			$row_class = '';
			if     ($result['best_times'] >= 10) $row_class = 'table-success';
			elseif ($result['best_times'] >= 5)  $row_class = 'table-warning';
			elseif ($result['best_times'] >= 2)  $row_class = 'table-info';

			$has_detail = count($result['all_patterns']) > 1;
			$detail_id  = 'detail-' . $result['ball'];
		?>
		<!-- Main row -->
		<tr class="hwcf-row <?= $row_class ?>"
			<?php if ($has_detail): ?>
				data-toggle="collapse"
				data-target="#<?= $detail_id ?>"
				aria-expanded="false"
				aria-controls="<?= $detail_id ?>"
			<?php endif; ?>>
			<td class="text-center align-middle"><?= $rank++ ?></td>
			<td class="text-center align-middle">
				<span class="ball-num"><?= $result['ball'] ?></span>
			</td>
			<td class="text-center align-middle"><?= $result['times_drawn'] ?></td>
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
			<td colspan="10">
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
			foreach ($hwc_extra_results as $result):
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
					<span class="ball-num xball-num"><?= $result['ball'] ?></span>
				</td>
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
				<td colspan="10">
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

</div><!-- /container-fluid -->
</section>

<script>
<?php
// Build last-drawn ball list (main only for dup_extra; main+extra for normal lotteries)
$__drawn       = array();
$__drawn_extra = 0;
for ($__i = 1; $__i <= $lottery->balls_drawn; $__i++) {
	if (!empty($lottery->last_drawn['ball'.$__i])) $__drawn[] = intval($lottery->last_drawn['ball'.$__i]);
}
if (!empty($lottery->last_drawn['extra'])) {
	$__drawn_extra = intval($lottery->last_drawn['extra']); // always separate — grey highlight
}
?>
var _lastDrawnBalls     = <?= json_encode($__drawn); ?>;
var _lastDrawnExtraBall = <?= intval($__drawn_extra) ?>;
var _isDupExtra         = <?= !empty($is_dup_extra) ? 'true' : 'false' ?>;
var _bestHwcBall        = <?= intval($best_points_ball) ?>;
var _bestHwcBallPts     = <?= intval($best_points_val) ?>;
var _bestHwcBallIsExtra = <?= !empty($best_points_is_extra) ? 'true' : 'false' ?>;
$(document).ready(function() {
	// Rotate chevron icon when row expands/collapses
	$('.hwcf-row').on('click', function() {
		var icon = $(this).find('.fa-chevron-down, .fa-chevron-up');
		if (icon.length) {
			icon.toggleClass('fa-chevron-down fa-chevron-up');
		}
	});
	// Highlight main-ball badges that were in the last draw (orange)
	$('#hwcf-table .ball-num').each(function() {
		if (_lastDrawnBalls.indexOf(parseInt($(this).text().trim(), 10)) !== -1) {
			$(this).css({'background': '#e67e00', 'box-shadow': '0 0 0 3px #e67e00', 'color': '#fff'});
		}
	});
	// Highlight the last-drawn extra/bonus ball in grey (main table, non-dup-extra lotteries only)
	// For dup-extra lotteries the extra ball is a separate pool — do not touch main table balls
	if (_lastDrawnExtraBall && !_isDupExtra) {
		$('#hwcf-table .ball-num').each(function() {
			if (parseInt($(this).text().trim(), 10) === _lastDrawnExtraBall) {
				$(this).css({'background': '#6c757d', 'box-shadow': '0 0 0 3px #6c757d', 'color': '#fff'});
			}
		});
	}
	// Highlight the highest follower-points ball in green (overrides orange/grey — runs last)
	if (_bestHwcBall) {
		// For dup-extra lotteries: best ball may be in main table OR extra table
		var $bestTarget = (_isDupExtra && _bestHwcBallIsExtra)
			? $('#hwcf-extra-table .xball-num')
			: $('#hwcf-table .ball-num');
		$bestTarget.each(function() {
			if (parseInt($(this).text().trim(), 10) === _bestHwcBall) {
				$(this).css({'background': '#28a745', 'box-shadow': '0 0 0 3px #28a745', 'color': '#fff'});
				$(this).closest('td').append(
					'<div class="text-success small mt-1" style="white-space:nowrap;font-size:0.75em;">&#9733; Best Ball (' + _bestHwcBallPts + ' pts)</div>'
				);
			}
		});
	}
	// Highlight the last-drawn extra ball in grey in the extra-pool table (dup-extra lotteries)
	if (_lastDrawnExtraBall) {
		$('#hwcf-extra-table .xball-num').each(function() {
			if (parseInt($(this).text().trim(), 10) === _lastDrawnExtraBall) {
				$(this).css({'background': '#6c757d', 'box-shadow': '0 0 0 3px #6c757d', 'color': '#fff'});
			}
		});
	}
});
</script>
