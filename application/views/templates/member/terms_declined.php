<!--  Main Content -->
<section id="content">
    <div class="content-inner  col-centered">
	   <div class="row">
            <div class="col-xs-12 col-md-8">
			<div class="row">	
				<h1>Registration Declined</h1>
				<h2>Your account registration has been cancelled</h2>	
			</div>
			
			<div class="row">
				<div class="col-md-12 text-center">
					<div class="alert alert-info" style="margin: 30px 0;">
						<h4><i class="glyphicon glyphicon-info-sign"></i> Registration Not Completed</h4>
						<p>You have declined to accept the terms and conditions.</p>
						<p>Your account registration has been cancelled and your information has been removed from our system.</p>
					</div>
					
					<p>If you change your mind and would like to register in the future, you are welcome to start the registration process again.</p>
					
					<div style="margin: 30px 0;">
						<a href="<?php echo site_url('home'); ?>" class="btn btn-primary">
							<i class="glyphicon glyphicon-home"></i> Return to Home Page
						</a>
						<a href="<?php echo site_url('member'); ?>" class="btn btn-success" style="margin-left: 15px;">
							<i class="glyphicon glyphicon-user"></i> Try Registration Again
						</a>
					</div>
				</div>
			</div>
			</div>
			<!--  Sidebar -->
			<div class="col-xs-12 col-md-4 sidebar">
				<?php $this->load->view('sidebar'); ?>
			</div>
          </div>
	</div>
</section>