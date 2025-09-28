<!-- Membership Table Styles -->
<style> 
	.members-table {
		width: 100%;
		font-size: 0.75em;
		table-layout: fixed;
	}
	
	.members-table th,
	.members-table td {
		padding: 4px 6px;
		vertical-align: middle;
		word-wrap: break-word;
		overflow: hidden;
	}
	
	/* Column widths - optimized for content */
	.members-table .col-username { width: 8%; }
	.members-table .col-email { width: 12%; }
	.members-table .col-name { width: 6%; }
	.members-table .col-reg-date { width: 10%; }
	.members-table .col-city { width: 6%; }
	.members-table .col-state { width: 6%; }
	.members-table .col-country { width: 6%; }
	.members-table .col-ip { width: 8%; }
	.members-table .col-location { width: 12%; }
	.members-table .col-lotteries { width: 10%; }
	.members-table .col-action { width: 4%; }
	.members-table .col-active { width: 4%; }
	
	/* Compact styles for specific content */
	.compact-date {
		font-size: 0.85em;
		line-height: 1.2;
	}
	
	.compact-location {
		font-size: 0.8em;
		line-height: 1.1;
	}
	
	.compact-ip {
		font-family: monospace;
		font-size: 0.75em;
	}
	
	.compact-lotteries {
		font-size: 0.8em;
		line-height: 1.1;
	}
	
	.lottery-badge {
		background: #007bff;
		color: white;
		display: inline-block;
		padding: 1px 4px;
		border-radius: 8px;
		font-size: 0.7em;
		margin-top: 2px;
	}
	
	/* Mobile responsive */
	@media (max-width: 1200px) {
		.members-table {
			font-size: 0.7em;
		}
		.members-table th,
		.members-table td {
			padding: 2px 4px;
		}
	}
	
	@media (max-width: 992px) {
		/* Hide less critical columns on medium screens */
		.col-city, .col-state { display: none; }
		.col-username { width: 10%; }
		.col-email { width: 15%; }
		.col-location { width: 15%; }
	}
	
	@media (max-width: 768px) {
		.members-table {
			display: block;
			overflow-x: auto;
			white-space: nowrap;
			font-size: 0.65em;
		}
		
		/* Hide additional columns on small screens */
		.col-name, .col-reg-date, .col-ip { display: none; }
		.col-username { width: 15%; }
		.col-email { width: 20%; }
		.col-lotteries { width: 20%; }
		.col-location { width: 20%; }
	}
	
	@media (max-width: 480px) {
		/* Ultra compact view for very small screens */
		.members-table {
			font-size: 0.6em;
		}
		.col-country, .col-location { display: none; }
		.col-username { width: 25%; }
		.col-email { width: 30%; }
		.col-lotteries { width: 25%; }
	}
	
	label {
    	display: inline-flex;
    	margin-bottom: .5rem;
    	margin-top: .5rem;
	}
</style>
<!-- Bootstrap Form Helpers -->
<link href="<?php echo site_url('css/bootstrap-formhelpers.min.css');?>" rel="stylesheet" media="screen">
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	  <script src="../js/html5shiv.js"></script>
	  <script src="../js/respond.min.js"></script>
	<![endif]-->
<!-- Bootstrap Form Helpers -->	
<script src="<?php echo site_url('js/bootstrap-formhelpers.min.js');?>"></script>	
<section>
	<h2>Members List</h2>
	<?php echo anchor('admin/membership/edit', '<i class = "icon-plus"></i>Add a new Member'); ?>
	<table class="table table-striped members-table">
		<thead>
			<tr> 
				<th scope="col" class="col-username">Username</th>
				<th scope="col" class="col-email">Email</th>
				<th scope="col" class="col-name">First</th>
				<th scope="col" class="col-name">Last</th>
				<th scope="col" class="col-reg-date">Reg. Date</th>
				<th scope="col" class="col-city">City</th>
				<th scope="col" class="col-state">State</th>
				<th scope="col" class="col-country">Country</th>
				<th scope="col" class="col-ip">IP</th>
				<th scope="col" class="col-location">Location</th>
				<th scope="col" class="col-lotteries">Lotteries</th>
				<th scope="col" class="col-action">Edit</th>
				<th scope="col" class="col-action">Del</th>
				<th scope="col" class="col-active">Active</th>
			</tr>
		</thead>
		<tbody>
<?php if (count($members)): foreach($members as $member): ?>
	<tr> 
		<td class="col-username"><?php echo anchor('admin/membership/edit/'.$member->id, $member->username);?></td>
		<td class="col-email" title="<?=$member->email?>"><?=strlen($member->email) > 20 ? substr($member->email, 0, 18) . '..' : $member->email?></td>
		<td class="col-name"><?=$member->first_name;?></td>
		<td class="col-name"><?=$member->last_name;?></td>
		<td class="col-reg-date compact-date" title="<?=date('l, F d, Y h:i:s A', strtotime($member->reg_time));?>">
			<?=date('M j, Y', strtotime($member->reg_time));?><br>
			<small class="text-muted"><?=date('g:i A', strtotime($member->reg_time));?></small>
		</td>
		<td class="col-city"><?=$member->city;?></td>
		<td class="col-state"><span class="bfh-states" data-country="<?=$member->country_id; ?>" data-state="<?=$member->state_prov; ?>"></span></td>
		<td class="col-country"><span class="bfh-countries" data-country="<?=$member->country_id;?>" data-flags="true"></span></td>
		<td class="col-ip compact-ip">
			<?php if (!empty($member->ip_address_readable)): ?>
				<span style="color: #c7254e; background: #f9f2f4; padding: 1px 3px; border-radius: 2px;" title="<?=$member->ip_address_readable?>">
					<?=strlen($member->ip_address_readable) > 12 ? substr($member->ip_address_readable, 0, 10) . '..' : $member->ip_address_readable?>
				</span>
			<?php else: ?>
				<span class="text-muted" style="font-size: 0.7em;">None</span>
			<?php endif; ?>
		</td>
		<td class="col-location compact-location">
			<?php if (!empty($member->location_city) || !empty($member->location_country)): ?>
				<div title="<?php
				$location_parts = array();
				if (!empty($member->location_city)) $location_parts[] = $member->location_city;
				if (!empty($member->location_region)) $location_parts[] = $member->location_region;
				if (!empty($member->location_country)) $location_parts[] = $member->location_country;
				echo !empty($location_parts) ? implode(', ', $location_parts) : 'Unknown';
				?>">
					<i class="fa fa-map-marker" style="color: #28a745; font-size: 0.8em;"></i>
					<?php
					// Show compact location - city, country only
					$compact_location = array();
					if (!empty($member->location_city)) $compact_location[] = $member->location_city;
					if (!empty($member->location_country)) $compact_location[] = $member->location_country;
					echo !empty($compact_location) ? implode(', ', $compact_location) : 'Unknown';
					?>
				</div>
			<?php else: ?>
				<span class="text-muted" style="font-size: 0.7em;">Not detected</span>
			<?php endif; ?>
		</td>
		<td class="col-lotteries compact-lotteries">
			<div title="<?=strip_tags($member->lottery_names)?>">
				<?php
				// Show compact lottery list - just names without <br> tags
				$lottery_names_clean = strip_tags($member->lottery_names);
				$lottery_names_array = explode("\n", trim($lottery_names_clean));
				$lottery_names_filtered = array_filter($lottery_names_array);
				
				if (!empty($lottery_names_filtered)) {
					// Show first lottery name if too long, truncate
					$first_lottery = trim($lottery_names_filtered[0]);
					if (strlen($first_lottery) > 12) {
						echo substr($first_lottery, 0, 10) . '..';
					} else {
						echo $first_lottery;
					}
					
					// Show additional lotteries count if more than 1
					if (count($lottery_names_filtered) > 1) {
						echo '<br><small class="text-muted">+' . (count($lottery_names_filtered) - 1) . ' more</small>';
					}
				}
				?>
			</div>
			<div class="lottery-badge">
				<?=$member->lottery_count;?> selected
			</div>
		</td>
	    <td class="col-action"><?php echo btn_edit('admin/membership/edit/'.$member->id); ?></td>
		<td class="col-action"><?php echo btn_delete('admin/membership/delete/'.$member->id); ?></td>
		<td class="col-active"><?=($member->member_active ? 'Yes' : 'No'); ?></td>
	</tr>
	<?php endforeach; ?>
	
	<?php else: ?>
		<tr>
			<td colspan="14" class="text-center">We could not find any members.</td>
		</tr>
<?php endif; ?>
		</tbody>
	</table>
</section>