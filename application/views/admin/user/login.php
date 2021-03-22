	<?php echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>'); 
	echo $this->session->flashdata('error');
	echo (isset($action) ? form_open($action) : form_open()); ?>
	<table class="table table-borderless">
		<tbody>
			<div class="form-group">
				<tr>
					<td scope = "row" style="text-align:left; width:8em;"><i class="fa fa-user"></i>&nbsp;<?php echo form_label('User Name:', 'username_l');?></td>
					<td><?php echo form_input('username', '', 'class="form-control-lg"'); ?></td>
				</tr>
				<tr>
					<td scope = "row"><i class="fa fa-envelope-o"></i>&nbsp;<?php echo form_label('Email:', 'email_l'); ?></td>
					<td><?php echo form_input('email', '', 'class="form-control-lg"'); ?></td>
				</tr>
				<tr>
					<td scope = "row" style="text-align:left; width:8em;"><i class="fa fa-lock"></i>&nbsp;<?php echo form_label('Password:', 'password_l'); ?></td>
					<td><?php echo form_password('password', '', 'class="form-control-lg'); ?></td>
				</tr>
				<tr>
					<td scope = "row" colspan = 2><?php echo form_submit('submit','Log in','class="btn btn-dark btn-primary btn-lg" style="margin-top:1em;"');?></td>
					<td>&nbsp;</td>
				</tr>
			</div>
			<tr>
				<td scope = "row" colspan = 2><?php echo anchor('admin/user/forgotpassword', 'Forgot Your Password?')?></td>
				<td>&nbsp;</td>
			</tr>
		</tbody>
	</table>
<?php echo form_close();?>