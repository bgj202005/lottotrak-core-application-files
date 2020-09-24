<section>
	<h2>Lottery Profiles</h2>
	<?php echo anchor('admin/lotteries/edit', '<i class = "icon-plus"></i> Add a Lottery'); ?>
	
	<div class="table-responsive">
	<table class="table-sm table-striped">
		<thead>
			<tr> 
				<td style = "white-space: nowrap;">Lottery Logo</td>
				<td style = "white-space: nowrap;">Lottery Name</td>
				<td style = "white-space: nowrap;">State / Province</td>
				<td style = "white-space: nowrap;">Country</td>
				<td style = "white-space: nowrap;">Pick</td>
				<td style = "white-space: nowrap;">From</td>
				<td style = "white-space: nowrap;">To</td>
				<td style = "white-space: nowrap;">Extra / Bonus Ball?</td>
				<td style = "white-space: nowrap;">Duplicates Allowed?</td>
				<td style = "white-space: nowrap;">From</td>
				<td style = "white-space: nowrap;">To</td>
				<th>View</th>
				<th>Edit</th>
				<th>Prizes</th>
				<th>Import</th>
				<th>Delete</th>
			</tr>
		</thead>
		<tbody>
	<?php if (count($lotteries)): foreach($lotteries as $lottery): ?>
	<tr> 
		<td style = "text-align:center;"><?php if (!empty($lottery->lottery_image)) 
			{ 
				$image_info = getimagesize(base_url().'images/uploads/'.$lottery->lottery_image); 
				$extra = array('width' => $image_info[0]/2, 'height' => $image_info[1]/2);
				echo img(base_url().'images/uploads/'.$lottery->lottery_image, FALSE, $extra); 
			} ?></td>
		<td style = "text-align:center;"><?php echo anchor('admin/lotteries/edit/'.$lottery->id, $lottery->lottery_name);?></td>
		<td style = "text-align:center;"><?=$lottery->lottery_state_prov; ?></td>
		<td style = "text-align:center;"><?=$lottery->lottery_country_id; ?></td>
		<td style = "text-align:center;"><?=$lottery->balls_drawn; ?></td>
		<td style = "text-align:center;"><?=$lottery->minimum_ball; ?></td>
		<td style = "text-align:center;"><?=$lottery->maximum_ball; ?></td>
		<td style = "text-align:center;"><?=($lottery->extra_ball ? 'Yes' : 'No'); ?></td>
		<td style = "text-align:center;"><?=($lottery->duplicate_extra_ball ? 'Yes' : 'No'); ?></td>
		<td style = "text-align:center;"><?=($lottery->extra_ball ? $lottery->minimum_extra_ball : '--'); ?></td>
		<td style = "text-align:center;"><?=($lottery->extra_ball ? $lottery->maximum_extra_ball : '--'); ?></td>
		<td style = "text-align:center;"><?php echo btn_view('admin/lotteries/view_draws/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo btn_edit('admin/lotteries/edit/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo btn_prizes('admin/lotteries/prizes/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo btn_import('admin/lotteries/import/'.$lottery->id); ?></td>
	    <td style = "text-align:center;"><?php echo btn_delete('admin/lotteries/delete/'.$lottery->id); ?></td>
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