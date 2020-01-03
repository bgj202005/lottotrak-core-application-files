<section>
	<h2>Lottery Profiles</h2>
	<?php echo anchor('admin/lotteries/edit', '<i class = "icon-plus"></i> Add a Lottery'); ?>
	
	<div class="table-responsive">
	<table class="table-sm table-striped">
		<thead>
			<tr> 
				<td style = "white-space: nowrap;">Lottery Name</td>
				<td style = "white-space: nowrap;">Country</td>
				<td style = "white-space: nowrap;">State / Province</td>
				<td style = "white-space: nowrap;">Pick</td>
				<td style = "white-space: nowrap;">Lowest Ball Drawn</td>
				<td style = "white-space: nowrap;">Highest Ball Drawn</td>
				<td style = "white-space: nowrap;">Extra / Bonus Ball?</td>
				<td style = "white-space: nowrap;">Allow Duplicate Extra / Bonus?</td>
				<td style = "white-space: nowrap;">Lowest Extra / Bonus Ball</td>
				<td style = "white-space: nowrap;">Highest Extra Ball</td>
				<th>Edit</th>
				<th>Delete</th>
			</tr>
		</thead>
		<tbody>
	<?php if (count($lotteries)): foreach($lotteries as $lottery): ?>
	<tr> 
		<td><?php echo anchor('admin/lotteries/edit/'.$lottery->id, $lottery->lottery_name);?></td>
		<td><?=$lottery->lottery_country_id; ?></td>
		<td><?=$lottery->lottery_state_prov; ?></td>
		<td><?=$lottery->balls_drawn; ?></td>
		<td><?=$lottery->minimum_ball; ?></td>
		<td><?=$lottery->maximum_ball; ?></td>
		<td><?=($lottery->extra_ball ? 'Yes' : 'No'); ?></td>
		<td><?=($lottery->duplicate_extra_ball ? 'Yes' : 'No'); ?></td>
		<td><?=($lottery->duplicate_extra_ball ? $lottery->minimum_extra_ball : '--'); ?></td>
		<td><?=($lottery->duplicate_extra_ball ? $lottery->maximum_extra_ball : '--'); ?></td>
	    <td><?php echo btn_edit('admin/lotteries/edit/'.$lottery->id); ?></td>
	    <td><?php echo btn_delete('admin/lotteries/delete/'.$lottery->id); ?></td>
	</tr>
	<?php endforeach; ?> 
	
	<?php else: ?>
		<tr>
			<td colspan="12" style = "text-align:center">No Lotteries are available.</td>
		</tr>
<?php endif; ?>
		</tbody>
	</table>
	</div>
</section>