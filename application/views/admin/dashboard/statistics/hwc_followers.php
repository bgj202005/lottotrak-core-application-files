<style>
	.pred-ball {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		background: #e74c3c;
		color: #fff;
		border-radius: 50%;
		width: 40px;
		height: 40px;
		font-weight: bold;
		font-size: 0.95em;
		margin: 3px;
	}
	.pred-ball-prev {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		background: #888;
		color: #fff;
		border-radius: 50%;
		width: 38px;
		height: 38px;
		font-weight: bold;
		font-size: 0.92em;
		margin: 3px;
	}
	.option-panel {
		background: #f8f9fa;
		border: 1px solid #dee2e6;
		border-radius: 6px;
		padding: 16px 20px;
		margin-bottom: 18px;
	}
	.pred-panel {
		background: #fef9e7;
		border-left: 4px solid #e74c3c;
		border-radius: 4px;
		padding: 14px 18px;
		margin-bottom: 16px;
		text-align: center;
	}
	.prev-pred-panel {
		background: #f4f4f4;
		border-left: 4px solid #aaa;
		border-radius: 4px;
		padding: 12px 18px;
		margin-bottom: 16px;
		text-align: center;
	}
</style>

<section>
	<h2><i class="fa fa-fire" style="color:#e74c3c;"></i> H-W-C + Followers &mdash; <?=htmlspecialchars($lottery->lottery_name);?></h2>

	<?php if(isset($message) && $message !== ''): ?>
	<div class="alert alert-warning"><?=$message;?></div>
	<?php endif; ?>

	<?php if($this->session->flashdata('message')): ?>
	<div class="alert alert-warning"><?=$this->session->flashdata('message');?></div>
	<?php endif; ?>

	<?php if(!empty($hwc_follower_message)): ?>
	<div class="alert alert-success"><?=htmlspecialchars($hwc_follower_message);?></div>
	<?php endif; ?>

	<h5 style="text-align:left"><?php echo anchor('admin/statistics', 'Back to Statistics Dashboard', 'title="Back to Statistics"'); ?></h5>

	<!-- Read-only Settings Panel -->
	<div style="display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 18px;">

		<!-- H-W-C Settings -->
		<div style="flex: 1; min-width: 240px; background: #eaf4fb; border: 1px solid #aed6f1; border-radius: 6px; padding: 12px 16px;">
			<div style="font-weight: bold; color: #1a5276; margin-bottom: 8px;">
				<i class="fa fa-thermometer-full" aria-hidden="true"></i> H-W-C Settings <small class="text-muted">(read-only)</small>
			</div>
			<table class="table table-condensed" style="margin:0; background:transparent; border:none; display:table; max-width:none; overflow:visible;">
				<tbody>
					<tr>
						<td style="border:none; padding: 2px 8px 2px 0; color:#555; white-space:nowrap;">Range:</td>
						<td style="border:none; padding: 2px 0; font-weight:bold;"><?=htmlspecialchars($hwc_settings['range']);?> draws</td>
					</tr>
					<tr>
						<td style="border:none; padding: 2px 8px 2px 0; color:#555; white-space:nowrap;">Extra Ball Included:</td>
						<td style="border:none; padding: 2px 0;">
							<?php if($hwc_settings['extra_included']): ?>
								<span class="label label-success">Yes</span>
							<?php else: ?>
								<span class="label label-default">No</span>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td style="border:none; padding: 2px 8px 2px 0; color:#555; white-space:nowrap;">Extra Draws Included:</td>
						<td style="border:none; padding: 2px 0;">
							<?php if($hwc_settings['extra_draws']): ?>
								<span class="label label-success">Yes</span>
							<?php else: ?>
								<span class="label label-default">No</span>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<!-- Last Draw -->
		<div style="flex: 1; min-width: 240px; background: #f8f9fa; border: 1px solid #adb5bd; border-radius: 6px; padding: 12px 16px;">
			<div style="font-weight: bold; color: #343a40; margin-bottom: 8px;">
				<i class="fa fa-calendar" aria-hidden="true"></i> Last Draw <small class="text-muted">(read-only)</small>
			</div>
			<div style="color:#555; font-size:0.9em; margin-bottom:8px;">
				<?php if(!empty($lottery->last_drawn['draw_date'])): ?>
					<?=date('D, M j, Y', strtotime(str_replace('/', '-', $lottery->last_drawn['draw_date'])));?>
				<?php else: ?>
					<span class="text-muted">N/A</span>
				<?php endif; ?>
			</div>
			<div style="display:flex; flex-wrap:wrap; align-items:center; gap:4px;">
				<?php for($__i = 1; $__i <= $lottery->balls_drawn; $__i++): ?>
					<?php if(!empty($lottery->last_drawn['ball'.$__i])): ?>
					<span class="badge badge-dark" style="font-size:0.9em; padding:5px 6px;"><?=(int)$lottery->last_drawn['ball'.$__i];?></span>
					<?php endif; ?>
				<?php endfor; ?>
				<?php if($lottery->extra_ball && !empty($lottery->last_drawn['extra'])): ?>
					<span style="margin:0 2px; color:#888;">+</span>
					<span class="badge badge-secondary" style="font-size:0.9em; padding:5px 6px;"><?=(int)$lottery->last_drawn['extra'];?></span>
				<?php endif; ?>
			</div>
		</div>

		<!-- Followers Settings -->
		<div style="flex: 1; min-width: 240px; background: #eafaf1; border: 1px solid #a9dfbf; border-radius: 6px; padding: 12px 16px;">
			<div style="font-weight: bold; color: #1e8449; margin-bottom: 8px;">
				<i class="fa fa-retweet" aria-hidden="true"></i> Followers Settings <small class="text-muted">(read-only)</small>
			</div>
			<table class="table table-condensed" style="margin:0; background:transparent; border:none; display:table; max-width:none; overflow:visible;">
				<tbody>
					<tr>
						<td style="border:none; padding: 2px 8px 2px 0; color:#555; white-space:nowrap;">Range:</td>
						<td style="border:none; padding: 2px 0; font-weight:bold;"><?=htmlspecialchars($followers_settings['range']);?> draws</td>
					</tr>
					<tr>
						<td style="border:none; padding: 2px 8px 2px 0; color:#555; white-space:nowrap;">Extra Ball Included:</td>
						<td style="border:none; padding: 2px 0;">
							<?php if($followers_settings['extra_included']): ?>
								<span class="label label-success">Yes</span>
							<?php else: ?>
								<span class="label label-default">No</span>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td style="border:none; padding: 2px 8px 2px 0; color:#555; white-space:nowrap;">Extra Draws Included:</td>
						<td style="border:none; padding: 2px 0;">
							<?php if($followers_settings['extra_draws']): ?>
								<span class="label label-success">Yes</span>
							<?php else: ?>
								<span class="label label-default">No</span>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

	</div>

	<!-- Option Settings Panel -->
	<div class="option-panel">
		<strong>Prediction Option:</strong>
		<?php
		$frm_attr = array('id' => 'frm_hwc_followers', 'style' => 'display:inline;');
		echo form_open(base_url('admin/statistics/hwc_followers/' . $lottery->id), $frm_attr);
		?>
		<div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-top: 10px;">

			<!-- H-W-C Group -->
			<label for="h_w_c_group_sel" style="margin:0; white-space:nowrap;"><strong>H-W-C Group:</strong></label>
			<select name="h_w_c_group" id="h_w_c_group_sel" class="form-control" style="width:auto; min-width:160px;">
				<?php if(!empty($h_w_c_group)): foreach($h_w_c_group as $pattern => $display): ?>
				<option value="<?=htmlspecialchars($pattern);?>" <?=($saved_h_w_c_group === $pattern ? 'selected' : '');?>><?=htmlspecialchars($display);?></option>
				<?php endforeach; endif; ?>
			</select>

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
			echo form_submit('change_hwc_follower_options', 'Change H-W-C + Follower Options', $btn_attr);
			echo form_close(); ?>
		</div>
	</div>

	<!-- Predicted Numbers for the Next Draw -->
	<?php if(!empty($lottery_numbers)):
		// Build label: H-W-C (pattern) + After Ball X / Position X
		$hw_label = htmlspecialchars($saved_h_w_c_group);
		if(!empty($saved_h_w_c_group) && !empty($h_w_c_group) && isset($h_w_c_group[$saved_h_w_c_group])) {
			$hw_label = htmlspecialchars($h_w_c_group[$saved_h_w_c_group]);
		}
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
			(<?=$hw_label;?> + <?=$fl_label;?>)
		</div>
		<div style="display:flex; flex-wrap:wrap; justify-content:center;">
			<?php foreach(explode(',', $lottery_numbers) as $num): ?>
			<span class="pred-ball"><?=trim(htmlspecialchars($num));?></span>
			<?php endforeach; ?>
		</div>
	</div>
	<?php elseif(empty($lottery_numbers)): ?>
	<div class="alert alert-info">
		No prediction has been generated yet. Select an H-W-C group and follower option above and click <strong>Change H-W-C + Follower Options</strong>.
	</div>
	<?php endif; ?>



</section>
