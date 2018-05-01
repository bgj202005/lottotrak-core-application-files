<!--  Main Content -->
<section id="content">
	<div class="content-inner col-centered">
    	<div class="row">
    		<div class="col-xs-12 col-md-8">
    			<div class="row">
    				<article>
    					<h2><?php echo e($article->title);?></h2>
    					<p class="pubdate"><?php echo e($article->pubdate); ?></p>
    					<?php echo $article->body; ?>
    				</article>
    				<table class = "table" style = "width:80%;">
						<tr>
							<td>User Name:</td>
							<td><?php echo form_input('name', set_value('name',$user->name)); ?></td>
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
				</div>
    		</div>
	<!--  Sidebar -->
	<div class="col-xs-12 col-md-4 sidebar">
    	<section>
        	<h2>Recent News</h2>
            	<?php $this->load->view('sidebar'); ?>
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