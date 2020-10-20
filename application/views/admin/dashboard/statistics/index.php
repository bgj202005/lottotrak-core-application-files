<section>
	<h2>Lottery Profile Statistics</h2>
	
	<div class="table-responsive">
	<table class="table-sm table-striped">
		<thead>
			<tr> 
				<td style = "white-space: nowrap;">Lottery Logo</td>
				<td style = "white-space: nowrap;">Lottery Name</td>
				<td style = "white-space: nowrap;">State / Province</td>
				<td style = "white-space: nowrap;">Country</td>
				<td style = "white-space: nowrap;">Average Sum (100 Draws)</td>
				<td style = "white-space: nowrap;">Sum (Last Draw)</td>
				<td style = "white-space: nowrap;">Repeaters</td>
				<th>Commulative Stats</th>
				<th>Followers</th>
				<th>Calculate</th>
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
		<td style = "text-align:center;">123</td>
		<td style = "text-align:center;">108</td>
		<td style = "text-align:center;">12 21</td>
		<td style = "text-align:center;"><?php echo $statistics->btn_stat('admin/statistics/view_draws/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $statistics->btn_followers('admin/statistics/followers/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $statistics->btn_calculate('admin/statistics/calculate/'.$lottery->id); ?></td>
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