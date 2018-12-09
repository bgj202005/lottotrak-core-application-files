<div class = "model-body">
	<?php echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>'); 
	echo $this->session->flashdata('error');
	
	echo (isset($action) ? form_open($action) : form_open()); ?>
	
	<table class = "table">
		<tr>
			<td><span class="glyphicon glyphicon-user"></span>&nbsp;User Name:</td>
			<td><?php echo form_input('username'); ?></td>
		</tr>
		<tr>
			<td><span class="glyphicon glyphicon-envelope"></span>&nbsp;Email:</td>
			<td><?php echo form_input('email'); ?></td>
		</tr>
		<tr>
		     <td>&nbsp;</td>
		     <td><?php echo anchor('admin/user/forgotpassword', 'Forgot Your Password?')?></td>
		</tr>
		<tr>
			<td><span class="glyphicon glyphicon-lock"></span>&nbsp;Password:</td>
			<td><?php echo form_password('password'); ?></td>
		</tr>
		<tr>
		<td></td>
		<td><?php echo form_submit('submit', 'Log in', 'class="btn btn-primary"'); ?></td>
		</tr>
	</table>
<?php echo form_close();?>
</div>