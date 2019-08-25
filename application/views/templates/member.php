<!--  Main Content -->
<section id="content">
	<div class="content-inner col-centered">
    	<div class="row">
    		<div class="col-xs-12 col-md-8">
    			<div class="row">
    					<h2>Your Profile</h2>
    				<article>
						<table class = "table" style = "width:80%;">
							<tr>
								<td>User Name:</td>
								<td><?php echo form_input('username', set_value('user_name',$user->user_name)); ?></td>
							</tr>
							<tr>
								<td>First Name:</td>
								<td><?php echo form_input('firstname', set_value('first_name',$user->first_name)); ?></td>
							</tr>
							<tr>
								<td>Last Name:</td>
								<td><?php echo form_input('lastname', set_value('last_name',$user->last_name)); ?></td>
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
							<td><?php echo form_submit('submit', 'Submit', '
									class="btn btn-primary"'); ?></td>
							</tr>
						</table>
					</article>
				</div>
    		</div>
	<!--  Sidebar -->
	<div class="col-xs-12 col-md-4 sidebar">
    	<section>
        	<h2>Current Lottery Selection</h2>
            	<?php $this->load->view('lotto_selector'); ?>
    	</section>
	</div>
    <div class="wrapper">
        <div class="content-menu">
        	<?php echo get_footer_menu($footer_menu_inside); ?>
       	
            <div class="clear"></div>
            </div>
	       </div>
        </div>
    </div>
</section>