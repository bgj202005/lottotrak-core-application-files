<div class = "model-body">
	<?php echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>'); 
	echo $this->session->flashdata('error');
	echo form_open(); ?>
	<table class = "table">
		<tr>
			<td><span class="glyphicon glyphicon-envelope"></span>&nbsp;Email Address:</td>
			<td><?php echo form_input('email'); ?></td>
			<td><?php echo anchor('admin/user/login', 'Back to Login')?></td>
		</tr>
		<tr>
			<td colspan = "3"><div style = "text-align: center; font-weight: bold">So that we know you are human?
			<br />Enter the series of characters you see below:</div></td>
		    <td>&nbsp;</td>
		    <td>&nbsp;</td>  
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><?php echo $captcha['image']; ?></td>
		    <td><?php echo form_button('Reload', 'Refresh', 'class="btn btn-success" onClick="history.go(0)"'); ?></td>
		</tr>
		<tr>
			<td>Type Characters:</td>
			<td><?php echo form_input('captcha'); ?></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
    		<td>&nbsp;</td>
    		<td><?php echo form_submit('submit', 'Continue', 'class="btn btn-primary"'); ?></td>    		
    		<td>&nbsp;</td>
	</table>
<?php echo form_close();?>
</div>