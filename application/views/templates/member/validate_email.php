<!--  Main Content -->
<section id="content">
    <div class="content-inner  col-centered">
	   <div class="row">
            <div class="col-xs-12 col-md-8">
			<div class="row">	
				<h1 style="color: #333333;">Check Your Email</h1>
				<h2 style="color: #555555;">An activation email has been sent to your inbox</h2>	
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-info">
						<h4><i class="fa fa-envelope"></i> Email Activation Required</h4>
						<p>Your account has been created but <strong>requires email activation</strong> before you can log in.</p>
						<p>Please check your email inbox and click the activation link to complete your registration.</p>
					</div>
					
					<div class="alert alert-warning">
						<h4><i class="fa fa-clock-o"></i> Important Reminder</h4>
						<p>You must activate your account within <strong>5 days</strong> or it will be automatically deleted.</p>
						<p>If you don't see the email, please check your spam/junk folder.</p>
					</div>
				</div>
			</div>
			</div>
			<!--  Sidebar -->
			<div class="col-xs-12 col-md-4 sidebar">
				<?php $this->load->view('sidebar'); ?>
			</div>
          </div>
			<div class="row">
				<div class="col-xs-12 col-md-8">
					<div class="wrapper">
						<div class="content-menu">
							<?php echo get_footer_menu($footer_menu_inside, $maintenance); ?>
							<div class="clear"></div>
					  </div>
				   </div>
				</div>
			</div>	
		</div>
	</div>
</section>