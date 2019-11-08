<div class = "model-body">
	<?php echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>'); 
	echo $this->session->flashdata('error');
	echo form_open($action); ?>
	
	<table class = "table">
    	
    	<tr>
    		<td><?php echo form_label('Email:', 'email'); ?></td>
    		<td><?php if (isset($email_hash, $email_code)) { 
			  echo form_hidden('email_hash', $email_hash); 
			  echo form_hidden('email_code', $email_code);
    		} 
    		echo form_hidden('id', $id);
    		if (!isset($email)) $email = '';
    		echo form_input('email', $email); ?></td>
    	</tr>
    	<tr>
    		<td>Password:</td>
    		<td><?php echo form_password('password'); ?></td>
    	</tr>
    	<tr>
    		<td>Confirm Password:</td>
    		<td><?php echo form_password('password_confirm'); ?></td>
    	</tr>
			<td><?php echo form_submit('submit', 'Update My Password', 'class="btn btn-primary"'); ?></td>
		</tr>
	</table>
<?php echo form_close();?>
</div>