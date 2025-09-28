<!-- Membership Table Styles -->
<style> 
	table{
    	width:100%;
	}
	tr{
		font-size: 0.77em;
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
	<table class="table table-striped">
		<thead>
			<tr> 
				<th scope="col">Username</th>
				<th scope="col">Email</th>
				<th scope="col">First Name</th>
				<th scope="col">Last Name</th>
				<th scope="col">Registration Date and Time</th>
				<th scope="col">City</th>
				<th scope="col">State/Province</th>
				<th scope="col">Country</th>
				<th scope="col">IP Address</th>
				<th scope="col">Detected Location</th>
				<th scope="col">Lotteries (Count)</th>
				<th scope="col">Edit</th>
				<th scope="col"> Delete</th>
				<th scope="col">Account Active?</th>
			</tr>
		</thead>
		<tbody>
<?php if (count($members)): foreach($members as $member): ?>
	<tr> 
		<td><?php echo anchor('admin/membership/edit/'.$member->id, $member->username);?></td>
		<td><?=$member->email?></td>
		<td><?=$member->first_name;?></td>
		<td><?=$member->last_name;?></td>
		<td><?=date('l, F, d Y h:i:s A', strtotime($member->reg_time));?></td>
		<td><?=$member->city;?></td>
		<td><span class="bfh-states" data-country="<?=$member->country_id; ?>" data-state="<?=$member->state_prov; ?>"></span></td>
		<td><span class="bfh-countries" data-country="<?=$member->country_id;?>" data-flags="true"></span></td>
		<td style="font-family: monospace; font-size: 0.9em;">
			<?php if (!empty($member->ip_address_readable)): ?>
				<span style="color: #c7254e; background: #f9f2f4; padding: 2px 4px; border-radius: 3px;">
					<?=$member->ip_address_readable;?>
				</span>
			<?php else: ?>
				<span class="text-muted">Not recorded</span>
			<?php endif; ?>
		</td>
		<td style="font-size: 0.85em;">
			<?php if (!empty($member->location_city) || !empty($member->location_country)): ?>
				<div>
					<i class="fa fa-map-marker" style="color: #28a745;"></i>
					<?php
					$location_parts = array();
					if (!empty($member->location_city)) $location_parts[] = $member->location_city;
					if (!empty($member->location_region)) $location_parts[] = $member->location_region;
					if (!empty($member->location_country)) $location_parts[] = $member->location_country;
					echo !empty($location_parts) ? implode(', ', $location_parts) : 'Unknown';
					?>
				</div>
				<?php if (!empty($member->location_detected_at)): ?>
					<small class="text-muted">Detected: <?=date('M j, Y', strtotime($member->location_detected_at));?></small>
				<?php endif; ?>
			<?php else: ?>
				<span class="text-muted">Location not detected</span>
			<?php endif; ?>
		</td>
		<td class = "btn-sm">
			<?=$member->lottery_names;?>
			<div style="background: #007bff; color: white; display: inline-block; padding: 2px 6px; border-radius: 10px; font-size: 0.8em; margin-top: 5px;">
				<?=$member->lottery_count;?> selected
			</div>
		</td>
	    <td><?php echo btn_edit('admin/membership/edit/'.$member->id); ?></td>
		<td><?php echo btn_delete('admin/membership/delete/'.$member->id); ?></td>
		<td><?=($member->member_active ? 'Yes' : 'No'); ?></td>
	</tr>
	<?php endforeach; ?>
	
	<?php else: ?>
		<tr>
			<td colspan="3">We could not find any members.</td>
		</tr>
<?php endif; ?>
		</tbody>
	</table>
</section>