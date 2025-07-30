<!--  Main Content -->
<section id="content">
    <div class="content-inner  col-centered">
	   <div class="row">
            <div class="col-xs-12 col-md-8">
			<div class="row">	
				<h1 class="bg-success" style = "text-align:center; color:snow">Your Password is now reset.</h1>
				<h2 class="bg-info" style = "text-align:center; color:snow">You must use login to use the new password.</h2>	
			</div>
			<div class="row">
				<div class="col-md-4">
				</div>
				<div class="col-md-4">
					</div>
				<div class="col-md-4">
					</div>
			</div>
				<div class="row">
					<div class="col-md-4">
					</div>
					<div class="col-md-4">
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