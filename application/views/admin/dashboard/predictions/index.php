<section>
	<h2>Lottery Predictions for Listed Lotteries</h2>
	
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
				<th>Combinations</th>
				<th>Generate Wheel</th>
				<th>Files</th>
				<th>Prize History</th>
				<th>Predictions</th>
			</tr>
		</thead>
		<tbody>
	<?php if (count($lotteries)): foreach($lotteries as $lottery): ?>
	<tr> 
		<td style = "text-align:center;"><?php if (!empty($lottery->lottery_image)) 
			{ 
				if (DIRECTORY_SEPARATOR === '/') 
				{
    			// unix, linux, mac
					$path = $this->input->server('DOCUMENT_ROOT').'/images/uploads/'.$lottery->lottery_image;
				}
				else
				{
				// windows	
					$path =	base_url().'images/uploads/'.$lottery->lottery_image;
				}
				$image_info = getimagesize($path); 
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
	    <td style = "text-align:center;"><?php echo $predictions->btn_calculate('admin/predictions/combinations/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $predictions->btn_generate('admin/predictions/generate/'.$lottery->id, $predictions->active($lottery->id)); ?></td>
		<td style = "text-align:center;"><?php echo $predictions->btn_files('admin/predictions/files/'.$lottery->id, $predictions->active($lottery->id)); ?></td>
		<td style = "text-align:center;"><?php echo $predictions->btn_wins('admin/predictions/history/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $predictions->btn_predicts('admin/predictions/predicts/'.$lottery->id); ?></td>
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