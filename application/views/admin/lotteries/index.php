<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<style>
	table{
    	width:100%;
	}
	tr{
		font-size: 0.95em; /* Minimum size before horizontal toolbar appears under list */
	}
	label {
    	display: inline-flex;
    	margin-bottom: .5rem;
    	margin-top: .5rem;
	}
	
	/* Mobile-friendly responsive design */
	@media (max-width: 768px) {
		/* Hide less critical columns on mobile */
		.mobile-hide {
			display: none !important;
		}
		
		/* Reduce font sizes further on mobile */
		table th, table td {
			font-size: 0.6em !important;
			padding: 2px 4px !important;
		}
		
		/* Compact button styles */
		.btn-sm {
			padding: 1px 4px !important;
			font-size: 0.5em !important;
		}
		
		/* Stack lottery name vertically if needed */
		.lottery-name {
			max-width: 80px;
			word-wrap: break-word;
			line-height: 1.1;
		}
		
		/* Make table more compact on mobile */
		.table-responsive {
			border: none;
		}
		
		/* Compact pagination on mobile */
		.pagination {
			font-size: 0.8em;
		}
	}
	
	@media (max-width: 1024px) and (min-width: 769px) {
		/* Tablet view - hide some columns but keep more than mobile */
		.tablet-hide {
			display: none !important;
		}
		
		table th, table td {
			font-size: 0.65em !important;
			padding: 3px 5px !important;
		}
	}
	
	/* Ensure buttons are compact */
	.action-btns {
		white-space: nowrap;
	}
	
	/* Status badges */
	.badge {
		font-size: 0.6em !important;
		padding: 2px 4px !important;
	}
	
	/* Toggle buttons mobile friendly */
	.fa-toggle-on, .fa-toggle-off {
		font-size: 1em !important;
	}
	
	@media (max-width: 576px) {
		/* Extra small devices */
		h2 {
			font-size: 1.2em;
		}
		
		table th, table td {
			font-size: 0.55em !important;
			padding: 1px 2px !important;
		}
		
		.lottery-name {
			max-width: 60px;
		}
	}
</style>	
<section>
	<h2>Lottery Profiles</h2>
	<?php echo anchor('admin/lotteries/edit', '<i class = "icon-plus"></i> Add a Lottery'); ?>
	<?php if (!empty($message)) ?> <h4 class="bg-warning" style = "margin-top: 20px; text-align:center;"><?=$message; ?></h4>
	<?php if($pagination): ?>
		<section><?php echo $pagination; ?></section>
	<?php endif; ?>
	<div class="table-responsive">
	<table class="table-sm table-striped">
		<thead>
			<tr> 
				<td style = "white-space: nowrap; font-size: 0.85em;" class="mobile-hide">Lottery Logo</td>
				<td style = "white-space: nowrap; font-size: 0.75em;">Lottery Name</td>
				<td style = "white-space: nowrap; font-size: 0.85em;" class="tablet-hide">Status</td>
				<th style = "font-size: 0.85em;">Visible</th>
				<td style = "white-space: nowrap; font-size: 0.85em;">Last Draw Date</td>
				<td style = "white-space: nowrap; font-size: 0.85em;">Drawn Numbers</td>
				<td style = "white-space: nowrap; font-size: 0.75em;" class="mobile-hide">State / Province</td>
				<td style = "white-space: nowrap; font-size: 0.75em;" class="mobile-hide">Country</td>
				<td style = "white-space: nowrap; font-size: 0.75em;">Pick</td>
				<td style = "white-space: nowrap; font-size: 0.75em;" class="mobile-hide">From</td>
				<td style = "white-space: nowrap; font-size: 0.75em;" class="mobile-hide">To</td>
				<td style = "white-space: nowrap; font-size: 0.75em;" class="mobile-hide">Extra / Bonus Ball?</td>
				<td style = "white-space: nowrap; font-size: 0.75em;" class="mobile-hide">Duplicates Allowed?</td>
				<td style = "white-space: nowrap; font-size: 0.75em;" class="mobile-hide">From</td>
				<td style = "white-space: nowrap; font-size: 0.75em;" class="mobile-hide">To</td>
				<th style = "font-size: 0.85em;" class="tablet-hide">View</th>
				<th style = "font-size: 0.85em;">Edit</th>
				<th style = "font-size: 0.85em;" class="mobile-hide">Prizes</th>
				<th style = "font-size: 0.85em;" class="mobile-hide">Import</th>
				<th style = "font-size: 0.85em;" class="tablet-hide">Delete</th>
			</tr>
		</thead>
		<tbody>
	<?php if (count($lotteries)): foreach($lotteries as $lottery): ?>
	<tr> 
		<td style = "text-align:center;" class="mobile-hide"><?php if (!empty($lottery->lottery_image)) 
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
				$extra = array('width' => $image_info[0]/2, 'height' => $image_info[1]/2, 'alt'	=> '');
				echo img(base_url().'images/uploads/'.$lottery->lottery_image, FALSE, $extra); 
			} ?></td>
		<td style = "text-align:center; font-size: 0.7em;" class="lottery-name"><?php echo anchor('admin/lotteries/edit/'.$lottery->id, $lottery->lottery_name);?></td>
		<td style = "text-align:center;" class="tablet-hide">
			<?php if (isset($lottery->enabled) && $lottery->enabled == 1): ?>
				<span class="badge badge-success">Visible</span>
			<?php else: ?>
				<span class="badge badge-warning">Hidden</span>
			<?php endif; ?>
		</td>
		<td style = "text-align:center; font-size: 0.85em;">
			<?php if (isset($lottery->enabled) && $lottery->enabled == 1): ?>
				<?php echo anchor('admin/lotteries/toggle_enabled/'.$lottery->id, 
					'<i class="fa fa-toggle-on" style="color: green; font-size: 1.2em;" title="Click to make invisible"></i>', 
					'onclick="return confirm(\'Are you sure you want to make this lottery invisible?\')"'); ?>
			<?php else: ?>
				<?php echo anchor('admin/lotteries/toggle_enabled/'.$lottery->id, 
					'<i class="fa fa-toggle-off" style="color: red; font-size: 1.2em;" title="Click to make visible"></i>', 
					'onclick="return confirm(\'Are you sure you want to make this lottery visible?\')"'); ?>
			<?php endif; ?>
		</td>
		<td style = "text-align:center; font-size: 0.75em;">
			<?php echo (!empty($lottery->last_date) ? date('M j, Y', strtotime($lottery->last_date)) : 'N/A'); ?>
		</td>
		<td style = "text-align:center; font-size: 0.75em;">
			<?php if (!empty($lottery->last_draw)): ?>
				<?php 
				$numbers = explode(',', str_replace(['[', ']'], '', $lottery->last_draw));
				echo '<span style="color: #007bff; font-weight: bold;">' . implode(', ', $numbers) . '</span>';
				?>
			<?php else: ?>
				<span style="color: #6c757d;">N/A</span>
			<?php endif; ?>
		</td>
		<td style = "text-align:center; font-size: 0.7em;" class="mobile-hide"><?=$lottery->lottery_state_prov; ?></td>
		<td style = "text-align:center; font-size: 0.7em;" class="mobile-hide"><?=$lottery->lottery_country_id; ?></td>
		<td style = "text-align:center; font-size: 0.7em;"><?=$lottery->balls_drawn; ?></td>
		<td style = "text-align:center; font-size: 0.7em;" class="mobile-hide"><?=$lottery->minimum_ball; ?></td>
		<td style = "text-align:center; font-size: 0.7em;" class="mobile-hide"><?=$lottery->maximum_ball; ?></td>
		<td style = "text-align:center; font-size: 0.7em;" class="mobile-hide"><?=($lottery->extra_ball ? 'Yes' : 'No'); ?></td>
		<td style = "text-align:center; font-size: 0.7em;" class="mobile-hide"><?=($lottery->duplicate_extra_ball ? 'Yes' : 'No'); ?></td>
		<td style = "text-align:center; font-size: 0.7em;" class="mobile-hide"><?=($lottery->extra_ball ? $lottery->minimum_extra_ball : '--'); ?></td>
		<td style = "text-align:center; font-size: 0.7em;" class="mobile-hide"><?=($lottery->extra_ball ? $lottery->maximum_extra_ball : '--'); ?></td>
		<td style = "text-align:center; font-size: 0.85em;" class="tablet-hide action-btns"><?php echo btn_view('admin/lotteries/view_draws/'.$lottery->id); ?></td>
		<td style = "text-align:center; font-size: 0.85em;" class="action-btns"><?php echo btn_edit('admin/lotteries/edit/'.$lottery->id); ?></td>
		<td style = "text-align:center; font-size: 0.85em;" class="mobile-hide action-btns"><?php echo btn_prizes('admin/lotteries/prizes/'.$lottery->id); ?></td>
		<td style = "text-align:center; font-size: 0.85em;" class="mobile-hide action-btns"><?php echo btn_import('admin/lotteries/import/'.$lottery->id); ?></td>
	    <td style = "text-align:center;" class="tablet-hide action-btns"><?php echo btn_lottery_delete('admin/lotteries/delete/'.$lottery->id, $lottery->lottery_name); ?></td>
	</tr>
	<?php endforeach; ?> 
	
	<?php else: ?>
		<tr>
			<td colspan="20" style = "text-align:center" class="d-block d-md-table-cell">No Lotteries are available.</td>
		</tr>
<?php endif; ?>
		</tbody>
	</table>
	</div>
	<?php if($pagination): ?>
		<section style = "margin-top:20px;"><?php echo $pagination; ?></section>
	<?php endif; ?>
</section>