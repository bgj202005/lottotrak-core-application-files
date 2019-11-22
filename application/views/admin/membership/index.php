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
				<th scope="col">Lotteries</th>
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
		<td><?=$member->lottery_id;?></td>
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