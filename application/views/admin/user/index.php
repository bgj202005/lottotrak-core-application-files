<section>
	<h2>Admin Users</h2>
	<?php echo anchor('admin/user/edit', '<i class = "icon-plus"></i> Add a new admin user'); ?>
	
	<table class="table table-striped">
		<thead>
			<tr> 
				<td>Email</td>
				<th>Edit</th>
				<td>Delete</td>
			</tr>
		</thead>
		<tbody>
<?php if (count($administrators)): foreach($administrators as $administrator): ?>
	<tr> 
		<td><?php echo anchor('admin/user/edit/'.$administrator->id, $administrator->email);?></td>
	    <td><?php echo btn_edit('admin/user/edit/'.$administrator->id); ?></td>
	    <td><?php echo btn_delete('admin/user/delete/'.$administrator->id); ?></td>
	</tr>
	<?php endforeach; ?>
	
	<?php else: ?>
		<tr>
			<td colspan="3">We could not find any Administrator Users.</td>
		</tr>
<?php endif; ?>
		</tbody>
	</table>
</section>