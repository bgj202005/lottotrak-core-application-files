<?php echo validation_errors(); ?>
<?php echo form_open(); ?>
	<h5 style = "text-align:left"><?php echo anchor('admin/user', 'Back to the Lottery Administrator Dashboard', 'title="Back to Lottery Administrator Dashboard"'); ?></h5>
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
		<td>Session Inactivity Timeout:</td>
		<td>
			<?php 
			// Define timeout options from 5 minutes to 4 hours
			$timeout_options = array(
				'300' => '5 minutes',
				'600' => '10 minutes', 
				'900' => '15 minutes',
				'1200' => '20 minutes',
				'1800' => '30 minutes',
				'2700' => '45 minutes',
				'3600' => '1 hour',
				'5400' => '1.5 hours',
				'7200' => '2 hours',
				'10800' => '3 hours',
				'14400' => '4 hours'
			);
			$selected_timeout = isset($user->inactivity_timeout) ? $user->inactivity_timeout : 1800;
			echo form_dropdown('inactivity_timeout', $timeout_options, set_value('inactivity_timeout', $selected_timeout), 'class="form-control"');
			?>
			<small class="form-text text-muted">Set how long before automatic logout due to inactivity (default: 30 minutes)</small>
		</td>
	</tr>
	<tr>
	<td><?php echo form_submit('submit', 'Submit', '
			class="btn btn-primary"'); ?></td>
	</tr>
</table>