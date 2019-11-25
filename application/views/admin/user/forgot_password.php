	<?php echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>'); 
	echo $this->session->flashdata('error');
	echo form_open(); ?>
	<table class = "table table-borderless">
		<tbody>	
			<div class="form-group">
				<tr>
					<td scope = "row" style="text-align:left; width:8em;"><i class="fa fa-envelope-o"></i>&nbsp;<?php echo form_label('Email Address:', 'email_l'); ?>
					<td><?php echo form_input('email', '', 'class="form-control-lg"'); ?></td>
				</tr>
				<tr>
					<td colspan = "2"><div style = "text-align: center; font-weight: bold">So that we know you are human?
					<br />Enter the series of characters you see below:</div></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><?php echo $captcha['image']; ?></td>
					<td><?php echo form_button('Reload', 'Refresh', 'class="btn btn-success" onClick="history.go(0)"', 'class="form-control-lg"'); ?></td>
				</tr>
				<tr>
					<td scope = "row" style="text-align:left; width:8em;"><i class="fa fa-keyboard-o"></i>Type Characters:</td>
					<td><?php echo form_input('captcha', '', 'class="form-control-lg"'); ?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><?php echo form_submit('submit', 'Continue', 'class="btn btn-dark btn-primary btn-lg" style="margin-top:1em;"'); ?></td>    		
				</tr>
				<tr>
					<td colspan = "2"><?php echo anchor('admin/user/login', 'Back to Login')?></td>
					<td>&nbsp;</td>
				</tr>
		</tbody>
	</table>
<?php echo form_close();?>