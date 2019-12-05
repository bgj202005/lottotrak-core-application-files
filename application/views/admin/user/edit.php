<?php echo validation_errors(); ?>
<?php echo form_open(); ?>
	<h2><?php echo empty($user->id) ? 'Add a new admin user' : 'Edit Administrator: '.$user->name; ?></h2>
	<div class = "modal-body" style = "width:85%">
	
<table class = "table" style = "width:80%;">
	<tr>
		<td>Administrator Name:</td>
		<td><?php echo form_input('name', set_value('name',$user->name)); ?></td>
	</tr>
	<tr>
		<td>User Name (Unique):</td>
		<td><?php echo form_input('username', set_value('username',$user->username)); ?></td>
	</tr>
	
	<tr>
		<td>Email:</td>
		<td><?php echo form_input('email', set_value('email', $user->email)); ?></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><?php echo form_password('password'); ?></td>
	</tr>
	<tr>
		<td>Confirm Password:</td>
		<td><?php echo form_password('password_confirm'); ?></td>
	</tr>
	<tr>
	<td><?php echo form_submit('submit', 'Submit', '
			class="btn btn-primary"'); ?></td>
	</tr>
</table>