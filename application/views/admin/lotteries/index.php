<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<style>
	/* Prevent horizontal overflow */
	html, body {
		overflow-x: hidden !important;
		max-width: 100vw !important;
	}
	
	.container-fluid {
		padding-left: 10px !important;
		padding-right: 10px !important;
		max-width: 100vw !important;
		overflow-x: hidden !important;
	}
	
	table{
    	width: 100% !important;
		table-layout: fixed !important;
		word-wrap: break-word !important;
	}
	
	tr{
		font-size: 0.95em; /* Minimum size before horizontal toolbar appears under list */
	}
	
	label {
    	display: inline-flex;
    	margin-bottom: .5rem;
    	margin-top: .5rem;
	}
	
	/* Table responsive wrapper */
	.table-responsive {
		border: none !important;
		overflow-x: auto !important;
		-webkit-overflow-scrolling: touch !important;
		max-width: 100% !important;
		margin: 0 !important;
		padding: 0 !important;
		/* Hide scrollbar but keep functionality */
		scrollbar-width: none !important; /* Firefox */
		-ms-overflow-style: none !important; /* IE and Edge */
	}
	
	/* Hide scrollbar for Chrome, Safari and Opera */
	.table-responsive::-webkit-scrollbar {
		display: none !important;
	}
	
	/* Ensure table cells don't expand beyond fixed widths - except for logo and numbers columns */
	table th, table td {
		overflow: hidden !important;
		text-overflow: ellipsis !important;
		white-space: nowrap !important;
	}
	
	/* Special handling for logo column - allow full display */
	table td:first-child {
		overflow: visible !important;
		white-space: normal !important;
		text-overflow: initial !important;
		padding: 5px !important;
	}
	
	/* Special handling for drawn numbers column - allow full display */
	table td:nth-child(6) {
		overflow: visible !important;
		white-space: normal !important;
		text-overflow: initial !important;
		word-wrap: break-word !important;
		line-height: 1.2 !important;
	}
	
	/* Special handling for action columns - prevent truncation */
	table th:nth-child(18), /* Prizes */
	table th:nth-child(19), /* Import */
	table th:nth-child(20), /* Delete */
	table td:nth-child(18),
	table td:nth-child(19),
	table td:nth-child(20) {
		overflow: visible !important;
		white-space: nowrap !important;
		text-overflow: initial !important;
		min-width: fit-content !important;
	}
	
	/* Ensure logos are properly sized and visible */
	table td img {
		max-width: 100% !important;
		height: auto !important;
		display: block !important;
		margin: 0 auto !important;
	}
	
	/* Mobile-friendly responsive design */
	@media (max-width: 768px) {
		/* Hide less critical columns on mobile but keep action columns visible */
		.mobile-hide:not(.action-btns) {
			display: none !important;
		}
		
		/* Keep action columns visible on mobile */
		table th:nth-child(16), /* View */
		table th:nth-child(17), /* Edit */
		table th:nth-child(18), /* Prizes */
		table th:nth-child(19), /* Import */
		table th:nth-child(20), /* Delete */
		table td:nth-child(16),
		table td:nth-child(17),
		table td:nth-child(18),
		table td:nth-child(19),
		table td:nth-child(20) {
			display: table-cell !important;
		}
		
		/* Increase font sizes for better readability on mobile */
		table th, table td {
			font-size: 0.85em !important;
			padding: 4px 5px !important;
			white-space: nowrap !important;
			overflow: hidden !important;
			text-overflow: ellipsis !important;
		}
		
		/* Special mobile handling for logo column when it's visible on larger mobile screens */
		table td:first-child {
			overflow: visible !important;
			white-space: normal !important;
			text-overflow: initial !important;
			padding: 5px !important;
		}
		
		/* Special mobile handling for drawn numbers column */
		table td:nth-child(6) {
			overflow: visible !important;
			white-space: normal !important;
			text-overflow: initial !important;
			word-wrap: break-word !important;
			line-height: 1.2 !important;
			max-width: none !important;
		}
		
		/* Ensure action columns are not truncated on mobile when visible */
		table th:nth-child(16), /* View */
		table th:nth-child(17), /* Edit */
		table th:nth-child(18), /* Prizes */
		table th:nth-child(19), /* Import */
		table th:nth-child(20), /* Delete */
		table td:nth-child(16),
		table td:nth-child(17),
		table td:nth-child(18),
		table td:nth-child(19),
		table td:nth-child(20) {
			overflow: visible !important;
			white-space: nowrap !important;
			text-overflow: initial !important;
			min-width: fit-content !important;
		}
		
		/* Ensure logos are properly sized on mobile */
		table td img {
			max-width: 50px !important;
			max-height: 30px !important;
			height: auto !important;
			display: block !important;
			margin: 0 auto !important;
		}
		
		/* Compact button styles for mobile */
		.btn-sm, .btn {
			padding: 2px 4px !important;
			font-size: 0.7em !important;
			margin: 1px !important;
		}
		
		/* Make action buttons more compact for mobile */
		.action-btns a {
			font-size: 0.65em !important;
			padding: 2px 3px !important;
			margin: 0 1px !important;
			display: inline-block !important;
		}
		
		/* Stack lottery name vertically if needed */
		.lottery-name {
			max-width: 90px !important;
			word-wrap: break-word !important;
			line-height: 1.2 !important;
			overflow: hidden !important;
			text-overflow: ellipsis !important;
		}
		
		/* Compact pagination on mobile */
		.pagination {
			font-size: 0.9em !important;
		}
		
		/* Reduce header and section spacing */
		h2 {
			font-size: 1.4em !important;
			margin-bottom: 12px !important;
		}
		
		section {
			padding: 10px !important;
			margin: 10px 0 !important;
		}
		
		/* Make icons readable */
		.fa {
			font-size: 1.1em !important;
		}
		
		/* Compact badge styling */
		.badge {
			font-size: 0.7em !important;
			padding: 3px 4px !important;
		}
		
		/* Ensure container doesn't overflow */
		.col-md-8 {
			padding-left: 10px !important;
			padding-right: 10px !important;
			max-width: 100% !important;
		}
	}
	
	@media (max-width: 1024px) and (min-width: 769px) {
		/* Tablet view - hide some columns but keep more than mobile */
		.tablet-hide {
			display: none !important;
		}
		
		table th, table td {
			font-size: 0.7em !important;
			padding: 3px 4px !important;
		}
		
		/* Logo handling on tablet */
		table td:first-child {
			overflow: visible !important;
			white-space: normal !important;
			text-overflow: initial !important;
			padding: 4px !important;
		}
		
		/* Numbers handling on tablet */
		table td:nth-child(6) {
			overflow: visible !important;
			white-space: normal !important;
			text-overflow: initial !important;
			word-wrap: break-word !important;
			line-height: 1.2 !important;
		}
		
		/* Ensure action columns are not truncated on tablet */
		table th:nth-child(16), /* View */
		table th:nth-child(17), /* Edit */
		table th:nth-child(18), /* Prizes */
		table th:nth-child(19), /* Import */
		table td:nth-child(16),
		table td:nth-child(17),
		table td:nth-child(18),
		table td:nth-child(19) {
			overflow: visible !important;
			white-space: nowrap !important;
			text-overflow: initial !important;
			min-width: fit-content !important;
		}
		
		/* Tablet logo sizing */
		table td img {
			max-width: 60px !important;
			max-height: 35px !important;
			height: auto !important;
			display: block !important;
			margin: 0 auto !important;
		}
	}
	
	/* Ensure buttons are compact */
	.action-btns {
		white-space: nowrap !important;
		overflow: hidden !important;
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
		/* Extra small devices - keep all action columns visible */
		html, body {
			overflow-x: hidden !important;
		}
		
		/* Ensure all action columns remain visible */
		table th:nth-child(16), /* View */
		table th:nth-child(17), /* Edit */
		table th:nth-child(18), /* Prizes */
		table th:nth-child(19), /* Import */
		table th:nth-child(20), /* Delete */
		table td:nth-child(16),
		table td:nth-child(17),
		table td:nth-child(18),
		table td:nth-child(19),
		table td:nth-child(20) {
			display: table-cell !important;
			overflow: visible !important;
			white-space: nowrap !important;
			text-overflow: initial !important;
			min-width: fit-content !important;
		}
		
		h2 {
			font-size: 1.3em !important;
			margin-bottom: 10px !important;
		}
		
		table th, table td {
			font-size: 0.75em !important;
			padding: 3px 4px !important;
			line-height: 1.3 !important;
		}
		
		.lottery-name {
			max-width: 80px !important;
			font-size: 0.7em !important;
		}
		
		/* Readable action buttons */
		.action-btns a {
			font-size: 0.65em !important;
			padding: 3px 4px !important;
		}
		
		/* Readable icons for very small screens */
		.fa {
			font-size: 1em !important;
		}
		
		/* Readable badges */
		.badge {
			font-size: 0.65em !important;
			padding: 3px 4px !important;
		}
		
		/* Adjust spacing */
		.container-fluid {
			padding: 8px !important;
		}
		
		.col-md-8 {
			padding: 8px !important;
		}
		
		/* Make table readable */
		.table-responsive {
			font-size: 1em !important;
		}
	}
	
	@media (max-width: 480px) {
		/* Very small phones - keep all action columns visible */
		
		/* Ensure all action columns remain visible */
		table th:nth-child(16), /* View */
		table th:nth-child(17), /* Edit */
		table th:nth-child(18), /* Prizes */
		table th:nth-child(19), /* Import */
		table th:nth-child(20), /* Delete */
		table td:nth-child(16),
		table td:nth-child(17),
		table td:nth-child(18),
		table td:nth-child(19),
		table td:nth-child(20) {
			display: table-cell !important;
			overflow: visible !important;
			white-space: nowrap !important;
			text-overflow: initial !important;
			min-width: fit-content !important;
		}
		
		table th, table td {
			font-size: 0.7em !important;
			padding: 2px 3px !important;
		}
		
		.lottery-name {
			max-width: 70px !important;
			font-size: 0.65em !important;
		}
		
		.action-btns a {
			font-size: 0.6em !important;
			padding: 2px 3px !important;
		}
		
		.fa {
			font-size: 0.9em !important;
		}
		
		.badge {
			font-size: 0.6em !important;
			padding: 2px 3px !important;
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
	<table class="table-sm table-striped" style="table-layout: fixed; word-wrap: break-word;">
		<thead>
			<tr> 
				<td style="white-space: nowrap; font-size: 0.85em; width: 8%;" class="mobile-hide">Logo</td>
				<td style="white-space: nowrap; font-size: 0.75em; width: 10%;">Name</td>
				<td style="white-space: nowrap; font-size: 0.85em; width: 5%;" class="tablet-hide">Status</td>
				<th style="font-size: 0.85em; width: 5%;">Visible</th>
				<td style="white-space: nowrap; font-size: 0.85em; width: 7%;">Last Draw</td>
				<td style="white-space: nowrap; font-size: 0.85em; width: 12%;">Numbers</td>
				<td style="white-space: nowrap; font-size: 0.75em; width: 5%;" class="mobile-hide">State/Prov</td>
				<td style="white-space: nowrap; font-size: 0.75em; width: 5%;" class="mobile-hide">Country</td>
				<td style="white-space: nowrap; font-size: 0.75em; width: 3%;">Pick</td>
				<td style="white-space: nowrap; font-size: 0.75em; width: 3%;" class="mobile-hide">From</td>
				<td style="white-space: nowrap; font-size: 0.75em; width: 3%;" class="mobile-hide">To</td>
				<td style="white-space: nowrap; font-size: 0.75em; width: 3%;" class="mobile-hide">Extra</td>
				<td style="white-space: nowrap; font-size: 0.75em; width: 3%;" class="mobile-hide">Dupes</td>
				<td style="white-space: nowrap; font-size: 0.75em; width: 3%;" class="mobile-hide">E.From</td>
				<td style="white-space: nowrap; font-size: 0.75em; width: 3%;" class="mobile-hide">E.To</td>
				<th style="font-size: 0.85em; width: 4%;" class="tablet-hide">View</th>
				<th style="font-size: 0.85em; width: 4%;">Edit</th>
				<th style="font-size: 0.85em; width: 5%;">Prizes</th>
				<th style="font-size: 0.85em; width: 5%;">Import</th>
				<th style="font-size: 0.85em; width: 5%;">Delete</th>
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
				$image_info = safe_getimagesize($path); 
				$extra = get_responsive_image_attrs($image_info, 100);
				$extra['alt'] = '';
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
		<td style = "text-align:center; font-size: 0.85em;" class="tablet-hide action-btns">
			<?php if (!empty($lottery->last_date) && !empty($lottery->last_draw)): ?>
				<?php echo btn_view('admin/lotteries/view_draws/'.$lottery->id); ?>
			<?php else: ?>
				<span style="color: #ccc; cursor: not-allowed;" title="No draws available">
					<i class="fa fa-eye fa-2x" aria-hidden="true"></i>
				</span>
			<?php endif; ?>
		</td>
		<td style = "text-align:center; font-size: 0.85em;" class="action-btns"><?php echo btn_edit('admin/lotteries/edit/'.$lottery->id); ?></td>
		<td style = "text-align:center; font-size: 0.85em;" class="action-btns"><?php echo btn_prizes('admin/lotteries/prizes/'.$lottery->id); ?></td>
		<td style = "text-align:center; font-size: 0.85em;" class="action-btns"><?php echo btn_import('admin/lotteries/import/'.$lottery->id); ?></td>
	    <td style = "text-align:center;" class="action-btns"><?php echo btn_lottery_delete('admin/lotteries/delete/'.$lottery->id, $lottery->lottery_name); ?></td>
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